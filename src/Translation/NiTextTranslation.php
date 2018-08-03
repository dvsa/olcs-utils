<?php

/**
 * Ni Text Translation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Utils\Translation;

use Dvsa\Olcs\Utils\Helper\ValueHelper;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\I18n\Translator\Translator;

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

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->translator = $container->get('translator');

        return $this;
    }

    public function setLocaleForNiFlag($niFlag)
    {
        if (!ValueHelper::isOn($niFlag)) {
            return;
        }

        $this->translator->setFallbackLocale($this->translator->getLocale());
        $this->translator->setLocale(str_replace('GB', 'NI', $this->translator->getLocale()));
    }
}
