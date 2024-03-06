<?php

namespace Dvsa\OlcsTest\Utils\View\Helper;

use Dvsa\Olcs\Utils\View\Helper\AssetPath;
use PHPUnit\Framework\TestCase;

class AssetPathTest extends TestCase
{
    public function test()
    {
        $config = [
            'asset_path' => '/cfg/path/',
        ];
        $path = '////path/to/resource/';

        $invoke = new AssetPath($config);

        static::assertSame('/cfg/path/path/to/resource/', $invoke($path));
    }
}
