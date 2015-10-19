<?php

/**
 * Client Adapter Logging Wrapper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Utils\Client;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Logging\Log\Logger;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\AdapterInterface;
use Zend\Log\Writer\Mock;

/**
 * Client Adapter Logging Wrapper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ClientAdapterLoggingWrapperTest extends MockeryTestCase
{
    /**
     * @var ClientAdapterLoggingWrapper
     */
    private $sut;

    public function setUp()
    {
        $writer = new Mock();

        $mockLogger = new \Zend\Log\Logger();
        $mockLogger->addWriter($writer);

        $this->sut = new ClientAdapterLoggingWrapper();
        Logger::setLogger($mockLogger);
    }

    public function testSetAdapter()
    {
        $mockAdapter = m::mock(Client\Adapter\Curl::class)->makePartial();

        $this->sut->setAdapter($mockAdapter);

        $this->assertSame($mockAdapter, $this->sut->getAdapter());
    }

    public function testWrapAdapter()
    {
        $mockAdapter = m::mock(Client\Adapter\Curl::class)->makePartial();

        /** @var Client $client */
        $client = m::mock(Client::class)->makePartial();
        $client->setAdapter($mockAdapter);

        $this->sut->wrapAdapter($client);

        $this->assertSame($mockAdapter, $this->sut->getAdapter());
        $this->assertSame($this->sut, $client->getAdapter());
    }

    public function testSetOptions()
    {
        $mockAdapter = m::mock(Client\Adapter\Curl::class)->makePartial();
        $mockAdapter->shouldReceive('setOptions')->once()->with(['foo' => 'bar']);

        $this->sut->setAdapter($mockAdapter);

        $this->sut->setOptions(['foo' => 'bar']);
    }

    public function testConnect()
    {
        $mockAdapter = m::mock(Client\Adapter\Curl::class)->makePartial();
        $mockAdapter->shouldReceive('connect')->once()->with('foo.com', 80, false);

        $this->sut->setAdapter($mockAdapter);

        $this->sut->connect('foo.com', 80);
    }

    public function testWrite()
    {
        $mockAdapter = m::mock(Client\Adapter\Curl::class)->makePartial();
        $mockAdapter->shouldReceive('write')->once()->with('GET', '/foo', '1.1', [], '');

        $this->sut->setAdapter($mockAdapter);

        $this->sut->write('GET', '/foo');
    }

    public function testRead()
    {
        $response = 'HTTP/1.1 200 OK\r\n'
            . 'Date: Mon, 19 Oct 2015 09:23:48 GMT\r\n'
            . 'Server: Apache/2.2.15 (CentOS)\r\n'
            . 'X-Powered-By: PHP/5.5.29\r\n'
            . 'Set-Cookie: PHPSESSID=6aqng9rv62ejn3ijvu2piri865; path=/\r\n'
            . 'Expires: Thu, 19 Nov 1981 08:52:00 GMT\r\n'
            . 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0\r\n'
            . 'Pragma: no-cache\r\n'
            . 'Connection: close\r\n'
            . 'Content-Type: application/json; charset=utf-8\r\n'
            . '\r\n'
            . '{\"foo\":\"bar\"}';

        $mockAdapter = m::mock(Client\Adapter\Curl::class)->makePartial();
        $mockAdapter->shouldReceive('read')->once()->andReturn($response);

        $this->sut->setShouldLogData(false);
        $this->sut->setAdapter($mockAdapter);

        $this->assertEquals($response, $this->sut->read());
    }

    public function testClose()
    {
        $mockAdapter = m::mock(Client\Adapter\Curl::class)->makePartial();
        $mockAdapter->shouldReceive('close')->once();

        $this->sut->setAdapter($mockAdapter);

        $this->sut->close();
    }

    public function testCall()
    {
        $mockAdapter = m::mock(Client\Adapter\Curl::class)->makePartial();
        $mockAdapter->shouldReceive('getConfig')->once()->andReturn('foo');

        $this->sut->setAdapter($mockAdapter);

        $this->assertEquals('foo', $this->sut->getConfig());
    }
}
