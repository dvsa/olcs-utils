<?php

namespace Dvsa\OlcsTest\Utils\View\Factory\Helper;

use Dvsa\Olcs\Utils\View\Factory\Helper\AssetPathFactory;
use Dvsa\Olcs\Utils\View\Helper\AssetPath;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

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

        static::assertInstanceOf(
            AssetPath::class,
            (new AssetPathFactory())->createService($mockSl)
        );
    }
}
