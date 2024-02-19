<?php

namespace Dvsa\Olcs\Utils\Translation;

use Psr\Container\ContainerInterface;
use Laminas\I18n\Translator\Loader\RemoteLoaderInterface;
use Laminas\I18n\Translator\Translator;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;

class TranslatorDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, callable $callback, array $options = null)
    {
        /** @var Translator $realTranslator */
        $realTranslator = $callback();

        $config = $container->get('Config');

        //use the same remote translation loader to load replacements
        /** @var RemoteLoaderInterface $translationLoader */
        $loaderClass = $config['translator']['remote_translation'][0]['type'];
        $translationLoader = $realTranslator->getPluginManager()->get($loaderClass);
        $replacements = $translationLoader->loadReplacements();

        return new TranslatorDelegator($realTranslator, $replacements);
    }
}
