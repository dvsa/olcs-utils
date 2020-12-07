<?php

namespace Dvsa\Olcs\Utils\Client;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class HttpProxyClientFactory
 *
 * Creates a Http Client, for connecting to external URL's
 * Reasoning is that external URL's might go through a proxy server, this class allows that
 * config to be specified in one place
 *
 * @package Dvsa\Olcs\Utils\View\Factory\Helper
 */
class HttpExternalClientFactory implements FactoryInterface
{
    const CONFIG_KEY = 'http_external';

    /**
     * Factory
     *
     * @param ServiceLocatorInterface $sl Service manager
     *
     * @return \Laminas\Http\Client
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        $client = new \Laminas\Http\Client();

        $config = $sl->get('config');
        if (!empty($config[self::CONFIG_KEY])) {
            $client->setOptions($config[self::CONFIG_KEY]);
        }

        $wrapper = new \Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper();
        $wrapper->wrapAdapter($client);
        // Disable logging reponse data by default
        $wrapper->setShouldLogData(false);

        return $client;
    }
}
