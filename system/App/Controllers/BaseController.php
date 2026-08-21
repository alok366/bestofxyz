<?php

namespace App\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\ResponseService;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use App\Models\User;
use App\Services\JwtService;
use Site;

abstract class BaseController
{
    protected static $request;
    protected ResponseService $response;
    protected $auth;
    protected ValidatorFactory $validator;
    protected Site $site;


    public function __construct()
    {
        global $container;
        global $capsule;

        $this->response = new ResponseService();
        $this->auth = $container->make('auth');

        // validator setup
        $loader = new ArrayLoader();
        $translator = new Translator($loader, 'en');
        $this->validator = new ValidatorFactory($translator);
        if ($capsule) :
            $this->validator->setPresenceVerifier(new DatabasePresenceVerifier($capsule->getDatabaseManager()));
        endif;
    }

    protected function request(): Request
    {
        global $container;
        if ($container && $container->bound('request')) :
            return $container->make('request');
        endif;

        if (!self::$request) :
            self::$request = Request::capture();
        endif;

        return self::$request;
    }

    /**
     * Resolve the authenticated user from request attributes, session, or optional Bearer token.
     */
    protected function getAuthenticatedUser(): ?User
    {
        $user = $this->request()->attributes->get('auth_user') ?? ($_SESSION['login'] ?? null);
        if ($user instanceof User) :
            return $user;
        endif;

        $authHeader = $this->request()->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) :
            $token = substr($authHeader, 7);
            try {
                $jwtService = new JwtService();
                $payload = $jwtService->validateToken($token);
                if (!empty($payload['sub'])) :
                    return User::find((int) $payload['sub']);
                endif;
            } catch (\Throwable $e) {
                // Ignore token errors for public endpoints
            }
        endif;

        return null;
    }

    /**
     * Proxy for Illuminate's e() helper
     * Makes IDE happy while using Illuminate's implementation
     */
    protected function e($value): string
    {
        return e($value);
    }
}
