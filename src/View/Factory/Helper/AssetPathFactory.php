<?php

namespace Dvsa\Olcs\Utils\View\Factory\Helper;

use Dvsa\Olcs\Utils\View\Helper\AssetPath;
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
    public function createService(ServiceLocatorInterface $sl)
    {
        $config = $sl->get('Config');

        return new AssetPath($config);
    }
}
