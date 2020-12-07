<?php

/**
 * Translator Delegator Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Utils\Translation;

use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegatorFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\I18n\Translator\Loader\RemoteLoaderInterface;
use Laminas\I18n\Translator\LoaderPluginManager;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Translator Delegator Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TranslatorDelegatorFactoryTest extends MockeryTestCase
{
    public function testCreatedDelegatorWithName()
    {
        $loaderClass = 'loader class';

        $config = [
            'translator' => [
                'remote_translation' => [
                    0 => [
                        'type' => $loaderClass,
                    ],
                ],
            ],
        ];

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Config')->andReturn($config);

        $name = 'foo';
        $requestedName = 'foo';
        $replacements = ['replacements'];

        $translationLoader = m::mock(RemoteLoaderInterface::class);
        $translationLoader->expects('loadReplacements')->withNoArgs()->andReturn($replacements);

        $loaderPluginManager = m::mock(LoaderPluginManager::class);
        $loaderPluginManager->expects('get')->with($loaderClass)->andReturn($translationLoader);

        $realTranslator = m::mock(Translator::class);
        $realTranslator->expects('getPluginManager')->withNoArgs()->andReturn($loaderPluginManager);

        $callback = function () use ($realTranslator) {
            return $realTranslator;
        };

        $sut = new TranslatorDelegatorFactory();
        $return = $sut->createDelegatorWithName($sm, $name, $requestedName, $callback);

        $this->assertInstanceOf(TranslatorDelegator::class, $return);
    }
}
