<?php

namespace Dvsa\OlcsTest\Utils\Translation;

use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Dvsa\OlcsTest\Utils\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\I18n\Translator\Translator;
use Dvsa\Olcs\Utils\Translation\TranslatorLogger;

/**
 * TranslatorLoggerTest
 */
class TranslatorLoggerTest extends MockeryTestCase
{
    /**
     * @var TranslatorLogger
     */
    protected $sut;

    /**
     * @var m\Mock
     */
    protected $logger;

    public function setUp()
    {
    }

    public function testLogTranslationNoCache()
    {
        $cache = m::mock(\Zend\Cache\Storage\StorageInterface::class);
        $cache->shouldReceive('hasItem')->with(TranslatorLogger::CACHE_KEY)->once()->andReturn(false);
        $cache->shouldReceive('setItem')->with(TranslatorLogger::CACHE_KEY, ['MESSAGE1'])->once()->andReturn(false);
        $logger = m::mock(\Zend\Log\LoggerInterface::class);
        $request = m::mock(\Zend\Http\PhpEnvironment\Request::class);
        $request->shouldReceive('getRequestUri')->with()->andReturn('URI');

        $sut = new TranslatorLogger($logger, $request, $cache);

        $logger->shouldReceive('info')->with(
            'Missing translation',
            [
                'message' => 'MESSAGE1',
                'en_GB' => 'TRANSLATED_MESSAGE1',
                'request' => 'URI',
            ]
        )->once();

        $translator = m::mock(\Zend\I18n\Translator\TranslatorInterface::class);
        $translator->shouldReceive('translate')->with('MESSAGE1')->once()->andReturn('TRANSLATED_MESSAGE1');

        $sut->logTranslation('MESSAGE1', $translator);
    }

    public function testLogTranslationWithCache()
    {
        $cache = m::mock(\Zend\Cache\Storage\StorageInterface::class);
        $cache->shouldReceive('hasItem')->with(TranslatorLogger::CACHE_KEY)->once()->andReturn(true);
        $cache->shouldReceive('getItem')->with(TranslatorLogger::CACHE_KEY)->once()->andReturn(['OLD1']);
        $cache->shouldReceive('setItem')->with(TranslatorLogger::CACHE_KEY, ['OLD1', 'MESSAGE1'])->once()
            ->andReturn(false);
        $logger = m::mock(\Zend\Log\LoggerInterface::class);
        $request = m::mock(\Zend\Http\PhpEnvironment\Request::class);
        $request->shouldReceive('getRequestUri')->with()->andReturn('URI');

        $sut = new TranslatorLogger($logger, $request, $cache);

        $logger->shouldReceive('info')->with(
            'Missing translation',
            [
                'message' => 'MESSAGE1',
                'en_GB' => 'TRANSLATED_MESSAGE1',
                'request' => 'URI',
            ]
        )->once();

        $translator = m::mock(\Zend\I18n\Translator\TranslatorInterface::class);
        $translator->shouldReceive('translate')->with('MESSAGE1')->once()->andReturn('TRANSLATED_MESSAGE1');

        $sut->logTranslation('MESSAGE1', $translator);
    }
}
