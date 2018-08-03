<?php

namespace Dvsa\Olcs\Utils\View\Factory\Helper;

use Dvsa\Olcs\Utils\View\Helper\GetPlaceholder;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Get Placeholder
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GetPlaceholderFactory extends AbstractHelper implements FactoryInterface
{
    private $placeholder;

    private $containers = [];

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->placeholder = $serviceLocator->get('placeholder');

        return $this;
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $placeholder = $this->placeholder;

        if (!isset($this->containers[$requestedName])) {
            $this->containers[$requestedName] = new GetPlaceholder($placeholder($requestedName));
        }

        return $this->containers[$requestedName];
    }
}
