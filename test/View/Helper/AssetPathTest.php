<?php

namespace Dvsa\OlcsTest\Utils\View\Helper;

use Dvsa\Olcs\Utils\View\Helper\AssetPath;
use PHPUnit\Framework\TestCase;

class AssetPathTest extends TestCase
{
    public function testCacheBustingStrategyNone()
    {
        $helper = new AssetPath([
            'assets' => [
                'base_url' => '/assets/',
                'cache_busting_strategy' => AssetPath::CACHE_BUSTING_STRATEGY_NONE,
            ]
        ]);
        $result = $helper('style.css');
        $this->assertSame('/assets/style.css', $result);
    }

    public function testCacheBustingStrategyRelease()
    {
        $helper = new AssetPath([
            'assets' => [
                'base_url' => '/assets/',
                'cache_busting_strategy' => AssetPath::CACHE_BUSTING_STRATEGY_RELEASE,
            ],
            'version' => [
                'release' => '1.2.3',
            ],
        ]);

        $result = $helper('style.css');
        $this->assertSame('/assets/style.css?v=c47f5b18b8a4', $result);
    }

    public function testCacheBustingStrategyReleaseThrowsWhenNoReleaseConfigured()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AssetPath([
            'assets' => [
                'base_url' => '/assets/',
                'cache_busting_strategy' => AssetPath::CACHE_BUSTING_STRATEGY_RELEASE,
            ]
        ]);
        $this->expectExceptionMessage('Release version is required for cache busting strategy "release".');
    }

    public function testCacheBustingStrategyUnixTimestamp()
    {
        $helper = new AssetPath([
            'assets' => [
                'base_url' => '/assets/',
                'cache_busting_strategy' => AssetPath::CACHE_BUSTING_STRATEGY_UNIX_TIMESTAMP,
            ]
        ]);
        $result = $helper('style.css');
        $this->assertMatchesRegularExpression('/\/assets\/style\.css\?v=\d+/', $result);
    }

    public function testInvalidCacheBustingStrategyThrows()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AssetPath([
            'assets' => [
                'base_url' => '/assets/',
                'cache_busting_strategy' => 'invalid_strategy',
            ]
        ]);
    }

    public function testAssetPathWithNoPath()
    {
        $helper = new AssetPath([
            'assets' => [
                'base_url' => '/assets/',
                'cache_busting_strategy' => AssetPath::CACHE_BUSTING_STRATEGY_NONE,
            ]
        ]);
        $result = $helper();
        $this->assertSame('/assets', $result);
    }

    public function testAssetPathWithEmptyPath()
    {
        $helper = new AssetPath([
            'assets' => [
                'base_url' => '/assets/',
                'cache_busting_strategy' => AssetPath::CACHE_BUSTING_STRATEGY_NONE,
            ]
        ]);
        $result = $helper('');
        $this->assertSame('/assets', $result);
    }

    public function testAssetPathWithCustomCacheBustingStrategy()
    {
        $helper = new AssetPath([
            'assets' => [
                'base_url' => '/assets/',
                'cache_busting_strategy' => AssetPath::CACHE_BUSTING_STRATEGY_UNIX_TIMESTAMP,
            ]
        ]);
        $result = $helper('script.js', AssetPath::CACHE_BUSTING_STRATEGY_NONE);
        $this->assertSame('/assets/script.js', $result);
    }

    public function testAssetPathWithCustomCacheBustingStrategyWithEmptyPath()
    {
        $helper = new AssetPath([
            'assets' => [
                'base_url' => '/assets/',
                'cache_busting_strategy' => AssetPath::CACHE_BUSTING_STRATEGY_UNIX_TIMESTAMP,
            ]
        ]);
        $result = $helper('', AssetPath::CACHE_BUSTING_STRATEGY_NONE);
        $this->assertSame('/assets', $result);
    }
}
