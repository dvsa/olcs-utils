<?php

namespace Dvsa\Olcs\Utils\Client;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
     * @return \Zend\Http\Client
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $client = new \Zend\Http\Client();

        $config = $container->get('config');
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
