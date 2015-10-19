<?php

/**
 * Client Adapter Logging Wrapper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Utils\Client;

use Olcs\Logging\Log\Logger;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\AdapterInterface as HttpAdapter;
use Zend\Http\Response;

/**
 * Client Adapter Logging Wrapper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ClientAdapterLoggingWrapper implements HttpAdapter
{
    private $adapter;
    private $host;
    private $port;
    private $shouldLogData;

    /**
     * Any adapter methods that don't exist in the interface will be wrapped
     *
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->getAdapter(), $method], $args);
    }

    public function setAdapter(HttpAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    public function wrapAdapter(Client $client)
    {
        $this->setAdapter($client->getAdapter());
        $client->setAdapter($this);
    }

    public function setShouldLogData($shouldLogData = true)
    {
        $this->shouldLogData = $shouldLogData;
    }

    /**
     * Set the configuration array for the adapter
     *
     * @param array $options
     */
    public function setOptions($options = array())
    {
        return $this->getAdapter()->setOptions($options);
    }

    /**
     * Connect to the remote server
     *
     * @param string  $host
     * @param int     $port
     * @param bool $secure
     */
    public function connect($host, $port = 80, $secure = false)
    {
        $this->host = $host;
        $this->port = $port;
        Logger::debug('Client Connection: ' . $host . ':' . $port);
        Logger::debug('Client Connection Adapter: ' . get_class($this->getAdapter()));

        return $this->getAdapter()->connect($host, $port, $secure);
    }

    /**
     * Send request to the remote server
     *
     * @param string        $method
     * @param \Zend\Uri\Uri $url
     * @param string        $httpVer
     * @param array         $headers
     * @param string        $body
     * @return string Request as text
     */
    public function write($method, $url, $httpVer = '1.1', $headers = array(), $body = '')
    {
        $data = [
            'data' => [
                'headers' => (array)$headers,
                'body' => $this->shouldLogData ? $body : '*** OMITTED ***'
            ]
        ];

        Logger::debug('Client Request: ' . $method . ' -> ' . $url, $data);

        return $this->getAdapter()->write($method, $url, $httpVer, $headers, $body);
    }

    /**
     * Read response from server
     *
     * @return string
     */
    public function read()
    {
        $response = $this->getAdapter()->read();

        $responseObject = Response::fromString($response);

        $data = [
            'data' => [
                'headers' => $responseObject->getHeaders(),
                'body' => $this->shouldLogData ? $responseObject->getBody() : '*** OMITTED ***'
            ]
        ];

        Logger::debug('Client Response', $data);

        return $response;
    }

    /**
     * Close the connection to the server
     *
     */
    public function close()
    {
        Logger::debug('Close Connection:' . $this->host . ':' . $this->port);
        $this->host = null;
        $this->post = null;

        return $this->getAdapter()->close();
    }
}
