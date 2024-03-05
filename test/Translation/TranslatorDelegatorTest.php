<?php

namespace Dvsa\OlcsTest\Utils\Translation;

use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Laminas\I18n\Translator\TranslatorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class TranslatorDelegatorTest extends MockeryTestCase
{
    protected $sut;
    protected $mockTranslator;

    public function setUp(): void
    {
        $this->mockTranslator = m::mock(TranslatorInterface::class);
        $this->mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                fn($message, $textDomain, $locale) => 'translated-' . $message
            );

        $translations = [
            '{{foo}}' => 'bar',
            '{{bar}}' => 'foo'
        ];

        $this->sut = new TranslatorDelegator($this->mockTranslator, $translations);
    }

    public function testTranslate()
    {
        $this->assertEquals('translated-no-replacements', $this->sut->translate('no-replacements'));

        $this->assertEquals('translated-replace-bar', $this->sut->translate('replace-{{foo}}'));
        $this->assertEquals('translated-replace-foo-bar', $this->sut->translate('replace-{{bar}}-{{foo}}'));
    }

    public function testTranslateNull()
    {
        $this->assertEquals('', $this->sut->translate(null));
    }

    public function testCall()
    {
        $this->mockTranslator->shouldReceive('setLocale')->once();

        $this->sut->setLocale();
    }
}
