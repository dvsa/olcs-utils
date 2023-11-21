<?php

namespace OlcsTest\Utils\View\Helper;

use Dvsa\Olcs\Utils\View\Helper\AssetPath;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Utils\View\Helper\AssetPath
 */
class AssetPathTest extends MockeryTestCase
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
