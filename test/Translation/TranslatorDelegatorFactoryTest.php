<?php

namespace Dvsa\OlcsTest\Utils\Translation;

use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegatorFactory;
use Psr\Container\ContainerInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\I18n\Translator\Loader\RemoteLoaderInterface;
use Laminas\I18n\Translator\LoaderPluginManager;

class TranslatorDelegatorFactoryTest extends MockeryTestCase
{
    public function testInvoke()
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

        $sm = m::mock(ContainerInterface::class);
        $sm->shouldReceive('get')->with('Config')->andReturn($config);

        $requestedName = 'foo';
        $replacements = ['replacements'];

        $translationLoader = m::mock(RemoteLoaderInterface::class);
        $translationLoader->expects('loadReplacements')->withNoArgs()->andReturn($replacements);

        $loaderPluginManager = m::mock(LoaderPluginManager::class);
        $loaderPluginManager->expects('get')->with($loaderClass)->andReturn($translationLoader);

        $realTranslator = m::mock(TranslatorInterface::class);
        $realTranslator->expects('getPluginManager')->withNoArgs()->andReturn($loaderPluginManager);

        $callback = function () use ($realTranslator) {
            return $realTranslator;
        };

        $sut = new TranslatorDelegatorFactory();
        $return = $sut($sm, $requestedName, $callback);

        $this->assertInstanceOf(TranslatorDelegator::class, $return);
    }
}
