<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\LoginRateLimitService;
use Framework\Http\Middleware\LoginThrottleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoginRateLimitServiceTest extends TestCase
{
    private string $testCacheDir;
    private LoginRateLimitService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testCacheDir = sys_get_temp_dir() . '/test_login_throttle_' . uniqid();
        $this->service = new LoginRateLimitService(['requests' => 3, 'window' => 60], $this->testCacheDir);
    }

    protected function tearDown(): void
    {
        // Cleanup test cache directory
        if (is_dir($this->testCacheDir)) {
            $files = glob($this->testCacheDir . '/*');
            if ($files) {
                foreach ($files as $file) {
                    @unlink($file);
                }
            }
            @rmdir($this->testCacheDir);
        }

        parent::tearDown();
    }

    private function getUniqueIp(): string
    {
        return '10.' . mt_rand(1, 254) . '.' . mt_rand(1, 254) . '.' . mt_rand(1, 254);
    }

    public function testInitialCheckIsAllowed(): void
    {
        $ip = $this->getUniqueIp();
        $result = $this->service->checkRateLimit($ip);

        $this->assertTrue($result['allowed']);
        $this->assertSame(3, $result['limit']);
        $this->assertSame(3, $result['remaining']);
        $this->assertSame(0, $result['retry_after']);
        $this->assertSame(0, $result['current']);
    }

    public function testRecordRequestDecrementsRemaining(): void
    {
        $ip = $this->getUniqueIp();

        $this->service->recordRequest($ip);
        $result1 = $this->service->checkRateLimit($ip);
        $this->assertTrue($result1['allowed']);
        $this->assertSame(2, $result1['remaining']);
        $this->assertSame(1, $result1['current']);

        $this->service->recordRequest($ip);
        $result2 = $this->service->checkRateLimit($ip);
        $this->assertTrue($result2['allowed']);
        $this->assertSame(1, $result2['remaining']);
        $this->assertSame(2, $result2['current']);
    }

    public function testExceedingLimitBlocksIdentifier(): void
    {
        $ip = $this->getUniqueIp();

        $this->service->recordRequest($ip);
        $this->service->recordRequest($ip);
        $this->service->recordRequest($ip);

        $result = $this->service->checkRateLimit($ip);
        $this->assertFalse($result['allowed']);
        $this->assertSame(0, $result['remaining']);
        $this->assertGreaterThan(0, $result['retry_after']);
        $this->assertSame(3, $result['current']);
    }

    public function testResetRateLimitClearsAttempts(): void
    {
        $ip = $this->getUniqueIp();

        $this->service->recordRequest($ip);
        $this->service->recordRequest($ip);
        $this->service->recordRequest($ip);

        $this->assertFalse($this->service->checkRateLimit($ip)['allowed']);

        $this->service->resetRateLimit($ip);

        $result = $this->service->checkRateLimit($ip);
        $this->assertTrue($result['allowed']);
        $this->assertSame(3, $result['remaining']);
        $this->assertSame(0, $result['current']);
    }

    public function testCustomLimitsOverrideDefaults(): void
    {
        $ip = $this->getUniqueIp();

        $custom = ['requests' => 1, 'window' => 30];
        $this->service->recordRequest($ip, 30);

        $result = $this->service->checkRateLimit($ip, $custom);
        $this->assertFalse($result['allowed']);
        $this->assertSame(1, $result['limit']);
        $this->assertSame(0, $result['remaining']);
    }

    public function testConvenienceHelpers(): void
    {
        $ip = $this->getUniqueIp();

        $this->assertFalse($this->service->tooManyAttempts($ip, 2, 60));
        $this->assertSame(2, $this->service->retriesLeft($ip, 2, 60));
        $this->assertSame(0, $this->service->attempts($ip, 60));

        $this->service->recordRequest($ip, 60);
        $this->assertSame(1, $this->service->attempts($ip, 60));
        $this->assertSame(1, $this->service->retriesLeft($ip, 2, 60));

        $this->service->recordRequest($ip, 60);
        $this->assertTrue($this->service->tooManyAttempts($ip, 2, 60));
        $this->assertSame(0, $this->service->retriesLeft($ip, 2, 60));
        $this->assertGreaterThan(0, $this->service->availableIn($ip, 60));
    }

    public function testMiddlewareIntegrationAllowsAndLimits(): void
    {
        $ip = $this->getUniqueIp();
        $middleware = new LoginThrottleMiddleware(2, 1);
        $request = Request::create('/api/auth/login', 'POST', [], [], [], [
            'REMOTE_ADDR' => $ip,
        ]);

        $nextCalled = 0;
        $next = function ($req) use (&$nextCalled) {
            $nextCalled++;
            return new Response(json_encode(['status' => 'ok']), 200);
        };

        // 1st attempt: allowed
        $response1 = $middleware->handle($request, $next, 2, 1);
        $this->assertSame(200, $response1->getStatusCode());
        $this->assertSame(1, $nextCalled);

        // 2nd attempt: allowed
        $response2 = $middleware->handle($request, $next, 2, 1);
        $this->assertSame(200, $response2->getStatusCode());
        $this->assertSame(2, $nextCalled);

        // 3rd attempt: throttled (429)
        $response3 = $middleware->handle($request, $next, 2, 1);
        $this->assertSame(429, $response3->getStatusCode());
        $this->assertSame(2, $nextCalled); // $next not called
        $this->assertTrue($response3->headers->has('Retry-After'));
        $this->assertSame('2', $response3->headers->get('X-RateLimit-Limit'));
        $this->assertSame('0', $response3->headers->get('X-RateLimit-Remaining'));

        $body = json_decode($response3->getContent(), true);
        $this->assertSame('Too Many Requests', $body['error']);
    }

    public function testFileFallbackMechanism(): void
    {
        $ip = $this->getUniqueIp();
        $limit = ['requests' => 2, 'window' => 60];

        $refRecord = new \ReflectionMethod($this->service, 'recordRequestFile');
        $refRecord->setAccessible(true);

        $refCheck = new \ReflectionMethod($this->service, 'checkRateLimitFile');
        $refCheck->setAccessible(true);

        // 1st request
        $refRecord->invoke($this->service, $ip, 60);
        $res1 = $refCheck->invoke($this->service, $ip, $limit);
        $this->assertTrue($res1['allowed']);
        $this->assertSame(1, $res1['remaining']);
        $this->assertSame(1, $res1['current']);

        // 2nd request
        $refRecord->invoke($this->service, $ip, 60);
        $res2 = $refCheck->invoke($this->service, $ip, $limit);
        $this->assertFalse($res2['allowed']);
        $this->assertSame(0, $res2['remaining']);
        $this->assertSame(2, $res2['current']);
        $this->assertGreaterThan(0, $res2['retry_after']);
    }
}
