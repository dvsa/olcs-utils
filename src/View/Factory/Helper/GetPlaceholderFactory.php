<?php

namespace Dvsa\Olcs\Utils\View\Factory\Helper;

use Dvsa\Olcs\Utils\View\Helper\GetPlaceholder;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Get Placeholder Factory
 */
class GetPlaceholderFactory implements FactoryInterface
{
    // For Laminas 3.x
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $placeholder = $container->get('placeholder');
        return function($name) use ($placeholder) {
            return new GetPlaceholder($placeholder->__invoke($name));
        };
    }

    // For Laminas 2.5
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'placeholder');
    }
}
