<?php

/**
 * Translator Delegator Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Utils\Translation;

use Zend\I18n\Translator\Loader\RemoteLoaderInterface;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Translator Delegator Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TranslatorDelegatorFactory implements DelegatorFactoryInterface
{
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        /** @var Translator $realTranslator */
        $realTranslator = $callback();

        $config = $serviceLocator->get('Config');

        //use the same remote translation loader to load replacements
        /** @var RemoteLoaderInterface $translationLoader */
        $loaderClass = $config['translator']['remote_translation'][0]['type'];
        $translationLoader = $realTranslator->getPluginManager()->get($loaderClass);
        $replacements = $translationLoader->loadReplacements();

        return new TranslatorDelegator($realTranslator, $replacements);
    }
}
