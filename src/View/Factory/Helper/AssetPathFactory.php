<?php

namespace Dvsa\Olcs\Utils\View\Factory\Helper;

use Dvsa\Olcs\Utils\View\Helper\AssetPath;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for @see Dvsa\Olcs\Snapshot\View\Helper\AssetPath
 */
class AssetPathFactory implements FactoryInterface
{
    /**
     * @param \Zend\View\HelperPluginManager $sl
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');

        return new AssetPath($config);
    }
}
