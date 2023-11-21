<?php

/**
 * Ni Text Translation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\Olcs\Utils\Translation;

use Dvsa\Olcs\Utils\Helper\ValueHelper;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\I18n\Translator\Translator;
use Interop\Container\ContainerInterface;

/**
 * Ni Text Translation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class NiTextTranslation implements FactoryInterface
{
    /**
     * @var Translator
     */
    private $translator;

    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null): NiTextTranslation
    {
        return $this($serviceLocator, NiTextTranslation::class);
    }

    public function setLocaleForNiFlag($niFlag)
    {
        if (!ValueHelper::isOn($niFlag)) {
            return;
        }

        $this->translator->setFallbackLocale($this->translator->getLocale());
        $this->translator->setLocale(str_replace('GB', 'NI', $this->translator->getLocale()));
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return $this
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NiTextTranslation
    {
        $this->translator = $container->get('translator');
        return $this;
    }
}
