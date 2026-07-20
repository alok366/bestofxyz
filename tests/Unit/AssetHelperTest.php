<?php

use PHPUnit\Framework\TestCase;

class AssetHelperTest extends TestCase
{
    public function testMixReturnsViteManifestEntryForBuiltSpaBundle(): void
    {
        require_once __DIR__ . '/../../framework/helpers.php';

        $result = mix('/fe-js/bundle.js');

        $this->assertSame('/dist/fe-js/bundle.js', $result);
    }
}
