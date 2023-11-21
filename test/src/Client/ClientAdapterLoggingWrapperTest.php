<?php

namespace OlcsTest\Utils\Client;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Laminas\Http\Client;
use Laminas\Http\Client\Adapter\AdapterInterface;
use Laminas\Log\Writer\Mock;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Logging\Log\Logger;

/**
 * @covers  Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper
 */
class ClientAdapterLoggingWrapperTest extends MockeryTestCase
{
    /** @var ClientAdapterLoggingWrapper */
    private $sut;

    /** @var  m\MockInterface|Client\Adapter\Curl */
    private $mockAdapter;

    public function setUp(): void
    {
        $writer = new Mock();

        $this->mockAdapter = m::mock(Client\Adapter\Curl::class)->makePartial();

        $mockLogger = new \Laminas\Log\Logger();
        $mockLogger->addWriter($writer);

        $this->sut = new ClientAdapterLoggingWrapper();
        $this->sut->setAdapter($this->mockAdapter);

        Logger::setLogger($mockLogger);
    }

    public function testSetAdapter()
    {
        $this->assertSame($this->mockAdapter, $this->sut->getAdapter());
    }

    public function testWrapAdapter()
    {
        /** @var Client $client */
        $client = m::mock(Client::class)->makePartial();
        $client->setAdapter($this->mockAdapter);

        $this->sut->wrapAdapter($client);

        $this->assertSame($this->mockAdapter, $this->sut->getAdapter());
        $this->assertSame($this->sut, $client->getAdapter());
    }

    public function testSetOptions()
    {
        $this->mockAdapter->shouldReceive('setOptions')->once()->with(['foo' => 'bar']);

        $this->sut->setOptions(['foo' => 'bar']);
    }

    public function testConnect()
    {
        $this->mockAdapter->shouldReceive('connect')->once()->with('foo.com', 80, false);

        $this->sut->connect('foo.com', 80);
    }

    public function testWrite()
    {
        $this->mockAdapter->shouldReceive('write')->once()->with('GET', '/foo', '1.1', [], '');

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

        $this->mockAdapter->shouldReceive('read')->once()->andReturn($response);

        $this->sut->setShouldLogData(false);

        $this->assertEquals($response, $this->sut->read());
    }

    public function testClose()
    {
        $this->mockAdapter->shouldReceive('close')->once();

        $this->sut->close();
    }

    public function testCall()
    {
        $this->mockAdapter->shouldReceive('getConfig')->once()->andReturn('foo');

        $this->assertEquals('foo', $this->sut->getConfig());
    }

    public function testSetOutputStream()
    {
        $stream = m::mock(\stdClass::class);

        $this->mockAdapter->shouldReceive('setOutputStream')->once()->with($stream);

        $this->assertSame($this->sut, $this->sut->setOutputStream($stream));
    }
}
