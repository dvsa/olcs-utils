<?php

namespace Dvsa\OlcsTest\Utils\View\Factory\Helper;

use Dvsa\Olcs\Utils\View\Factory\Helper\GetPlaceholderFactory;
use Dvsa\Olcs\Utils\View\Helper\GetPlaceholder;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Helper\Placeholder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Get Placeholder Factory Test
 */
class GetPlaceholderFactoryTest extends MockeryTestCase
{
    protected $sut;

    /** @var  m\MockInterface */
    protected $mockPlaceholder;

    public function setUp(): void
    {
        $this->sut = new GetPlaceholderFactory();

        $this->mockPlaceholder = m::mock(Placeholder::class);

        /** @var ServiceLocatorInterface|m\MockInterface $sm */
        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('placeholder')->andReturn($this->mockPlaceholder);

        $this->sut->createService($sm);
    }

    public function testInvoke()
    {
        $this->mockPlaceholder->shouldReceive('__invoke')
            ->with('foo')
            ->andReturn(m::mock());

        /** @var ContainerInterface|m\MockInterface $container */
        $container = m::mock(ContainerInterface::class);
        $container->shouldReceive('get')->with('placeholder')->andReturn($this->mockPlaceholder);

        $factory = $this->sut;
        $result = $factory($container, 'getPlaceholder');

        static::assertInstanceOf(\Closure::class, $result);

        $getPlaceholder = $result('foo');
        static::assertInstanceOf(GetPlaceholder::class, $getPlaceholder);
    }
}
