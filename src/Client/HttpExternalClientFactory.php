<?php

namespace Dvsa\Olcs\Utils\Client;

use http\Client;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

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
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this($serviceLocator, Client::class);
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return \Laminas\Http\Client
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $client = new \Laminas\Http\Client();
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
