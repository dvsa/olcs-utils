<?php

namespace Dvsa\Olcs\Utils\View\Factory\Helper;

use Dvsa\Olcs\Utils\View\Helper\AssetPath;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for @see Dvsa\Olcs\Snapshot\View\Helper\AssetPath
 */
class AssetPathFactory implements FactoryInterface
{
    /**
     * @param \Laminas\View\HelperPluginManager $sl
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        $config = $sl->getServiceLocator()->get('Config');

        return new AssetPath($config);
    }
}
