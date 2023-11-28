<?php

namespace Dvsa\OlcsTest\Utils\Client;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Interop\Container\ContainerInterface;
use Laminas\Http\Client;
use Laminas\Http\Client\Adapter\Curl;
use Laminas\Http\Client\Adapter\Socket;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * HttpExternalClientFactoryTest
 */
class HttpExternalClientFactoryTest extends MockeryTestCase
{
    public function testFactoryNoConfig()
    {
        $sut = new HttpExternalClientFactory();

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('config')->once()->andReturn([]);

        $object = $sut->__invoke($mockSl, Client::class);

        $this->assertInstanceOf(Client::class, $object);
        $this->assertInstanceOf(ClientAdapterLoggingWrapper::class, $object->getAdapter());
        $this->assertInstanceOf(Socket::class, $object->getAdapter()->getAdapter());
    }

    public function testFactoryConfig()
    {
        $sut = new HttpExternalClientFactory();

        $mockSl = m::mock(ContainerInterface::class);
        $mockSl->shouldReceive('get')->with('config')->once()
            ->andReturn(['http_external' => ['adapter' => Curl::class]]);

        $object = $sut->__invoke($mockSl, Client::class);

        $this->assertInstanceOf(Client::class, $object);
        $this->assertInstanceOf(ClientAdapterLoggingWrapper::class, $object->getAdapter());
        $this->assertInstanceOf(Curl::class, $object->getAdapter()->getAdapter());
    }
}
