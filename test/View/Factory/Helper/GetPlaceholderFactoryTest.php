<?php

namespace Dvsa\OlcsTest\Utils\View\Factory\Helper;

use Dvsa\Olcs\Utils\View\Factory\Helper\GetPlaceholderFactory;
use Dvsa\Olcs\Utils\View\Helper\GetPlaceholder;
use Interop\Container\ContainerInterface;
use Laminas\View\Helper\Placeholder;
use Laminas\View\HelperPluginManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class GetPlaceholderFactoryTest extends MockeryTestCase
{
    protected $sut;

    /** @var  m\MockInterface */
    protected $mockPlaceholder;

    public function setUp(): void
    {
        $this->sut = new GetPlaceholderFactory();
        $this->mockPlaceholder = m::mock(Placeholder::class);
    }

    public function testInvoke(): void
    {
        $this->mockPlaceholder->shouldReceive('__invoke')
            ->with('foo')
            ->andReturn(m::mock());

        $viewHelperManager = m::mock(HelperPluginManager::class);
        $viewHelperManager->expects('get')->with('placeholder')->andReturn($this->mockPlaceholder);

        /** @var ContainerInterface|m\MockInterface $container */
        $container = m::mock(ContainerInterface::class);
        $container->expects('get')->with('ViewHelperManager')->andReturn($viewHelperManager);

        $factory = $this->sut;
        $result = $factory($container, 'getPlaceholder');

        static::assertInstanceOf(\Closure::class, $result);

        $getPlaceholder = $result('foo');
        static::assertInstanceOf(GetPlaceholder::class, $getPlaceholder);
    }
}
