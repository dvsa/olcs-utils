<?php

namespace Dvsa\OlcsTest\Utils\Client;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Utils\Client\HttpExternalClientFactory;

/**
 * HttpExternalClientFactoryTest
 */
class HttpExternalClientFactoryTest extends MockeryTestCase
{
    public function testFactoryNoConfig()
    {
        $sut = new HttpExternalClientFactory();

        $mockSl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('config')->once()->andReturn([]);

        $object = $sut->createService($mockSl);

        $this->assertInstanceOf(\Zend\Http\Client::class, $object);
        $this->assertInstanceOf(\Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper::class, $object->getAdapter());
        $this->assertInstanceOf(\Zend\Http\Client\Adapter\Socket::class, $object->getAdapter()->getAdapter());
    }

    public function testFactoryConfig()
    {
        $sut = new HttpExternalClientFactory();

        $mockSl = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with('config')->once()
            ->andReturn(['http_external' => ['adapter' => \Zend\Http\Client\Adapter\Curl::class]]);

        $object = $sut->createService($mockSl);

        $this->assertInstanceOf(\Zend\Http\Client::class, $object);
        $this->assertInstanceOf(\Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper::class, $object->getAdapter());
        $this->assertInstanceOf(\Zend\Http\Client\Adapter\Curl::class, $object->getAdapter()->getAdapter());
    }
}
