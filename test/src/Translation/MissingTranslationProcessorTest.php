<?php

namespace Dvsa\OlcsTest\Utils\Service\Translator;

use Dvsa\Olcs\Utils\View\Factory\Helper\GetPlaceholderFactory;
use Dvsa\OlcsTest\Utils\Bootstrap;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Laminas\EventManager\EventManagerInterface;
use Laminas\I18n\Translator\Translator;
use Dvsa\Olcs\Utils\Translation\MissingTranslationProcessor as Sut;

/**
 * Class MissingTranslationProcessorTest
 * @package CommonTest\Service\Translator
 */
class MissingTranslationProcessorTest extends TestCase
{
    /**
     * @var \Laminas\View\Renderer\RendererInterface|m\MockInterface
     */
    protected $mockRenderer;

    /**
     * @var \Laminas\View\Resolver\ResolverInterface|m\MockInterface
     */
    protected $mockResolver;

    /**
     * @var Sut
     */
    protected $sut;

    /** @var m\MockInterface */
    protected $getPlaceholder;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockRenderer = m::mock('Laminas\View\Renderer\RendererInterface');
        $this->mockResolver = m::mock('Laminas\View\Resolver\ResolverInterface');
        $this->getPlaceholder = m::mock(GetPlaceholderFactory::class);

        $sm = Bootstrap::getServiceManager();
        $sm->setService('ViewRenderer', $this->mockRenderer);
        $sm->setService('Laminas\View\Resolver\TemplatePathStack', $this->mockResolver);
        $sm->setService('ViewHelperManager', $sm);
        $sm->setService('getPlaceholder', $this->getPlaceholder);

        $this->sut = new Sut();
        $this->sut->createService($sm);
    }

    public function testAttach()
    {
        /** @var EventManagerInterface|m\MockInterface $events */
        $events = m::mock(EventManagerInterface::class);
        $events->shouldReceive('attach')
            ->once()
            ->with(Translator::EVENT_MISSING_TRANSLATION, [$this->sut, 'processEvent'], 1);

        $this->sut->attach($events);
    }

    public function testProcessEventForPartial()
    {
        $event = m::mock(\Laminas\EventManager\Event::class)
            ->shouldReceive('getTarget')
            ->once()
            ->andReturn()
            //
            ->shouldReceive('getParams')
            ->andReturn(
                [
                    'locale' => 'en_GB',
                    'message' => 'markup-some-partial',
                ]
            )
            ->getMock();

        $this->mockResolver->shouldReceive('resolve')
            ->once()
            ->with('en_GB/markup-some-partial')
            ->andReturn('path_to_the_partial');

        $this->mockRenderer->shouldReceive('render')
            ->once()
            ->with('en_GB/markup-some-partial')
            ->andReturn('markup');

        static::assertEquals('markup', $this->sut->processEvent($event));
    }

    public function testProcessEventForPartialNi()
    {
        $event = m::mock(\Laminas\EventManager\Event::class)
            ->shouldReceive('getTarget')
            ->once()
            ->andReturn()
            //
            ->shouldReceive('getParams')
            ->andReturn(
                [
                    'locale' => 'en_NI',
                    'message' => 'markup-some-partial',
                ]
            )
            ->getMock();

        $this->mockResolver->shouldReceive('resolve')
            ->once()
            ->with('en_NI/markup-some-partial')
            ->andReturn(false)
            ->shouldReceive('resolve')
            ->once()
            ->with('en_GB/markup-some-partial')
            ->andReturn('path_to_the_partial');

        $this->mockRenderer->shouldReceive('render')
            ->once()
            ->with('en_GB/markup-some-partial')
            ->andReturn('markup');

        static::assertEquals('markup', $this->sut->processEvent($event));
    }

    public function testProcessEventForNestedTranslation()
    {
        /**
         * @var \Laminas\I18n\Translator\TranslatorInterface
         */
        $translator = m::mock('Laminas\I18n\Translator\TranslatorInterface')
            ->shouldReceive('translate')
            ->with('nested.translation.key')
            ->andReturn('translated substring')
            ->getMock();

        $event = m::mock(\Laminas\EventManager\Event::class)
            ->shouldReceive('getTarget')
            ->once()
            ->andReturn($translator)
            //
            ->shouldReceive('getParams')
            ->andReturn(
                [
                    'message' => 'text with a {nested.translation.key} in it',
                ]
            )
            ->getMock();

        $this->mockResolver->shouldReceive('resolve')->never();
        $this->mockRenderer->shouldReceive('render')->never();

        static::assertEquals(
            'text with a translated substring in it',
            $this->sut->processEvent($event)
        );
    }

    public function testOtherMissingKeysDontTriggerTemplateResolver()
    {
        $event = m::mock(\Laminas\EventManager\Event::class)
            ->shouldReceive('getTarget')
            ->once()
            ->andReturn()
            //
            ->shouldReceive('getParams')
            ->andReturn(['message' => 'missing.key'])
            ->getMock();

        $this->mockResolver->shouldReceive('resolve')->never();
        $this->mockRenderer->shouldReceive('render')->never();

        static::assertEquals(null, $this->sut->processEvent($event));
    }

    public function testProcessEventForPartialWithPlaceholder()
    {
        $event = m::mock(\Laminas\EventManager\Event::class)
            ->shouldReceive('getTarget')
            ->once()
            ->andReturn()
            //
            ->shouldReceive('getParams')
            ->andReturn(
                [
                    'locale' => 'en_GB',
                    'message' => 'markup-some-partial',
                ]
            )
            ->getMock();

        $placeholder = m::mock();
        $placeholder->shouldReceive('asString')->once()->andReturn('foo-placeholder');

        $this->getPlaceholder->shouldReceive('__invoke')->once()->with('FOO')->andReturn($placeholder);

        $this->mockResolver->shouldReceive('resolve')
            ->once()
            ->with('en_GB/markup-some-partial')
            ->andReturn('path_to_the_partial');

        $this->mockRenderer->shouldReceive('render')
            ->once()
            ->with('en_GB/markup-some-partial')
            ->andReturn('markup {{PLACEHOLDER:FOO}} bar');

        static::assertEquals('markup foo-placeholder bar', $this->sut->processEvent($event));
    }
}
