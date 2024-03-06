<?php

namespace Dvsa\OlcsTest\Utils\Translation;

use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Laminas\I18n\Translator\Translator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TranslatorDelegatorTest extends TestCase
{
    /**
     * @var TranslatorDelegator
     */
    protected $sut;

    /**
     * @var Translator|MockObject
     */
    protected $mockTranslator;

    public function setUp(): void
    {
        $this->mockTranslator = $this->createMock(Translator::class);

        $this->mockTranslator
            ->method('translate')
            ->willReturnCallback(
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
        $this->mockTranslator->expects($this->once())->method('setLocale');

        // @phpstan-ignore-next-line `setLocale` is forwarded using `__call` to the wrapped translator.
        $this->sut->setLocale('en_GB');
    }
}
