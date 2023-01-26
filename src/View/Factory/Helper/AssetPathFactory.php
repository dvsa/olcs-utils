<?php

namespace Dvsa\Olcs\Utils\View\Factory\Helper;

use Dvsa\Olcs\Utils\View\Helper\AssetPath;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Factory for @see Dvsa\Olcs\Snapshot\View\Helper\AssetPath
 */
class AssetPathFactory implements FactoryInterface
{
    /**
     * @param \Laminas\View\HelperPluginManager $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null): AssetPath
    {
        return $this($serviceLocator, AssetPath::class);
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return AssetPath
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AssetPath
    {
        $config = $container->getServiceLocator()->get('Config');
        return new AssetPath($config);
    }
}
