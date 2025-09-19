<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Utils\Translation;

use Dvsa\Olcs\Utils\Translation\MissingTranslationProcessor;
use Dvsa\Olcs\Utils\View\Helper\GetPlaceholder;
use Laminas\EventManager\Event;
use Laminas\EventManager\EventManagerInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Renderer\RendererInterface;
use Laminas\View\Resolver\ResolverInterface;
use Laminas\View\Resolver\TemplatePathStack;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MissingTranslationProcessorTest extends TestCase
{
    /**
     * @var RendererInterface|MockObject
     */
    protected $mockRenderer;

    /**
     * @var ResolverInterface|MockObject
     */
    protected $mockResolver;

    /**
     * @var MissingTranslationProcessor
     */
    protected $sut;

    /**
     * @var GetPlaceholder|MockObject
     */
    protected $getPlaceholder;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockRenderer = $this->createMock(RendererInterface::class);
        $this->mockResolver = $this->createMock(ResolverInterface::class);
        $this->getPlaceholder = $this->createMock(GetPlaceholder::class);
    }

    public function testAttach()
    {
        $events = $this->createMock(EventManagerInterface::class);
        $events
            ->expects($this->once())
            ->method('attach');

        $this->getService()->attach($events);
    }

    public function testProcessEventForPartial()
    {
        $event = $this->createMock(Event::class);

        $event->method('getTarget')->willReturn($this->createMock(TranslatorInterface::class));

        $event->expects($this->once())
            ->method('getParams')
            ->willReturn(
                [
                    'locale' => 'en_GB',
                    'message' => 'markup-some-partial',
                ]
            );

        $this->mockResolver
            ->method('resolve')
            ->with('en_GB/markup-some-partial')
            ->willReturn('path_to_the_partial');

        $this->mockRenderer
            ->method('render')
            ->with('en_GB/markup-some-partial')
            ->willReturn('markup');

        $this->assertEquals('markup', $this->getService()->processEvent($event));
    }

    public function testProcessEventForPartialNi()
    {
        $event = $this->createMock(Event::class);

        $event->method('getTarget')->willReturn($this->createMock(TranslatorInterface::class));

        $event
            ->method('getParams')
            ->willReturn(
                [
                    'locale' => 'en_NI',
                    'message' => 'markup-some-partial',
                ]
            );

        $this->mockResolver
            ->method('resolve')
            ->with('en_NI/markup-some-partial')
            ->willReturn('path_to_the_partial');

        $this->mockRenderer
            ->method('render')
            ->with('en_NI/markup-some-partial')
            ->willReturn('markup');

        $this->assertEquals('markup', $this->getService()->processEvent($event));
    }

    public function testProcessEventForNestedTranslation()
    {
        $translator = $this->createMock(TranslatorInterface::class);

        $translator
            ->method('translate')
            ->with('nested.translation.key')
            ->willReturn('translated substring');

        $event = $this->createMock(Event::class);

        $event
            ->method('getTarget')
            ->willReturn($translator);

        $event
            ->method('getParams')
            ->willReturn(
                [
                    'message' => 'text with a {nested.translation.key} in it',
                ]
            );

        $this->mockResolver->expects($this->never())->method('resolve');
        $this->mockRenderer->expects($this->never())->method('render');

        $this->assertEquals(
            'text with a translated substring in it',
            $this->getService()->processEvent($event)
        );
    }

    public function testOtherMissingKeysDontTriggerTemplateResolver()
    {
        $event = $this->createMock(Event::class);

        $event->method('getTarget')->willReturn($this->createMock(TranslatorInterface::class));

        $event
            ->method('getParams')
            ->willReturn(['message' => 'missing.key']);

        $this->mockResolver->expects($this->never())->method('resolve');
        $this->mockRenderer->expects($this->never())->method('render');

        $this->assertEquals(null, $this->getService()->processEvent($event));
    }

    public function testProcessEventForPartialWithPlaceholder()
    {
        $event = $this->createMock(Event::class);

        $event->method('getTarget')->willReturn($this->createMock(TranslatorInterface::class));

        $event
            ->method('getParams')
            ->willReturn(
                [
                    'locale' => 'en_GB',
                    'message' => 'markup-some-partial',
                ]
            );

        $this->getPlaceholder->method('asString')->willReturn('foo-placeholder');

        $this->mockResolver
            ->method('resolve')
            ->with('en_GB/markup-some-partial')
            ->willReturn('path_to_the_partial');

        $this->mockRenderer
            ->method('render')
            ->with('en_GB/markup-some-partial')
            ->willReturn('markup {{PLACEHOLDER:FOO}} bar');

        $this->assertEquals('markup foo-placeholder bar', $this->getService()->processEvent($event));
    }

    protected function getService(): MissingTranslationProcessor
    {
        $serviceManager = $this->createMock(ServiceManager::class);
        $serviceManager->method('get')->willReturnMap([
            ['ViewRenderer', $this->mockRenderer],
            [TemplatePathStack::class, $this->mockResolver],
            ['ViewHelperManager', $serviceManager],
            ['getPlaceholder', fn() => $this->getPlaceholder],
        ]);

        $serviceManager->method('has')->with('getPlaceholder')->willReturn(true);

        $missingTranslationProcessor = new MissingTranslationProcessor();
        $missingTranslationProcessor->__invoke($serviceManager, MissingTranslationProcessor::class);

        return $missingTranslationProcessor;
    }
}
