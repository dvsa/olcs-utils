<?php

namespace OlcsTest\Utils\View\Factory\Helper;

use Dvsa\Olcs\Utils\View\Factory\Helper\AssetPathFactory;
use Dvsa\Olcs\Utils\View\Helper\AssetPath;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Utils\View\Factory\Helper\AssetPathFactory
 */
class AssetPathFactoryTest extends MockeryTestCase
{
    public function test()
    {
        $mockSl = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->with('Config')->andReturn(['unit_Config'])
            ->getMock();

        /** @var ServiceManager $mockSm */
        $mockSm = m::mock(ServiceManager::class)
            ->shouldReceive('getServiceLocator')
            ->andReturn($mockSl)
            ->getMock();

        static::assertInstanceOf(
            AssetPath::class,
            (new AssetPathFactory())->createService($mockSm)
        );
    }
}
