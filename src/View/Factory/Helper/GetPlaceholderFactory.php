<?php

namespace Dvsa\Olcs\Utils\View\Factory\Helper;

use Dvsa\Olcs\Utils\View\Helper\GetPlaceholder;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Helper\AbstractHelper;

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

    public function __invoke($name)
    {
        $placeholder = $this->placeholder;

        if (!isset($this->containers[$name])) {
            $this->containers[$name] = new GetPlaceholder($placeholder($name));
        }

        return $this->containers[$name];
    }
}
