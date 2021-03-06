<?php

/**
 * Ni Text Translation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Utils\Translation;

use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Dvsa\OlcsTest\Utils\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\I18n\Translator\Translator;

/**
 * Ni Text Translation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class NiTextTranslationTest extends MockeryTestCase
{
    /**
     * @var NiTextTranslation
     */
    protected $sut;

    /**
     * @var Translator
     */
    protected $translator;

    public function setUp()
    {
        $this->translator = m::mock(Translator::class)->makePartial();
        $this->translator->setLocale('en_GB');

        $sm = Bootstrap::getServiceManager();
        $sm->setService('translator', $this->translator);

        $this->sut = new NiTextTranslation();
        $this->sut->createService($sm);
    }

    /**
     * @dataProvider niFlagProvider
     */
    public function testSetLocaleForNiFlag($niFlag, $expected, $expectedFallback)
    {
        $this->sut->setLocaleForNiFlag($niFlag);

        $this->assertEquals($expected, $this->translator->getLocale());
        $this->assertEquals($expectedFallback, $this->translator->getFallbackLocale());
    }

    public function niFlagProvider()
    {
        return [
            [
                'N',
                'en_GB',
                null
            ],
            [
                'Y',
                'en_NI',
                'en_GB'
            ]
        ];
    }
}
