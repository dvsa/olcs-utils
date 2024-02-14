<?php

namespace Dvsa\OlcsTest\Utils\View\Factory\Helper;

use Dvsa\Olcs\Utils\View\Factory\Helper\AssetPathFactory;
use Dvsa\Olcs\Utils\View\Helper\AssetPath;
use Psr\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Utils\View\Factory\Helper\AssetPathFactory
 */
class AssetPathFactoryTest extends MockeryTestCase
{
    public function test()
    {
        $container = m::mock(ContainerInterface::class)
            ->shouldReceive('get')
            ->with('Config')->andReturn(['unit_Config'])
            ->getMock();

        static::assertInstanceOf(
            AssetPath::class,
            (new AssetPathFactory())->__invoke($container, AssetPath::class)
        );
    }
}
