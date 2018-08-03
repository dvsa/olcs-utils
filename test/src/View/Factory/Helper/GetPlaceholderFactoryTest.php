<?php

namespace Dvsa\OlcsTest\Utils\View\Factory\Helper;

use Dvsa\Olcs\Utils\View\Factory\Helper\GetPlaceholderFactory;
use Dvsa\Olcs\Utils\View\Helper\GetPlaceholder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\Placeholder;

/**
 * Get Placeholder Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GetPlaceholderFactoryTest extends MockeryTestCase
{
    protected $sut;

    /** @var  m\MockInterface */
    protected $mockPlaceholder;

    public function setUp()
    {
        $this->sut = new GetPlaceholderFactory();

        $this->mockPlaceholder = m::mock(Placeholder::class);

        /** @var ServiceLocatorInterface|m\MockInterface $sm */
        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('placeholder')->andReturn($this->mockPlaceholder);

        $service = $this->sut->createService($sm);
    }

    public function testInvoke()
    {
        $sm = m::mock(ServiceLocatorInterface::class);
        $this->mockPlaceholder->shouldReceive('__invoke')
            ->with($sm,'foo')
            ->andReturn(m::mock());
        $this->mockPlaceholder->shouldReceive('__invoke')
            ->with('foo')
            ->andReturn(m::mock());

        $invoke = $this->sut;
        $getPlaceholder = $invoke($sm,'foo');
        static::assertInstanceOf(GetPlaceholder::class, $getPlaceholder);

        $getPlaceholder2 = $invoke($sm,'foo');
        static::assertSame($getPlaceholder, $getPlaceholder2);
    }
}
