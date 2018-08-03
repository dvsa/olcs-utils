<?php

namespace Dvsa\OlcsTest\Utils\Service\Translator;

use Dvsa\Olcs\Utils\View\Factory\Helper\GetPlaceholderFactory;
use Dvsa\OlcsTest\Utils\Bootstrap;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Zend\EventManager\EventManagerInterface;
use Zend\I18n\Translator\Translator;
use Dvsa\Olcs\Utils\Translation\MissingTranslationProcessor as Sut;
use Zend\View\Helper\Placeholder;

/**
 * Class MissingTranslationProcessorTest
 * @package CommonTest\Service\Translator
 */
class MissingTranslationProcessorTest extends TestCase
{
    /**
     * @var \Zend\View\Renderer\RendererInterface|m\MockInterface
     */
    protected $mockRenderer;

    /**
     * @var \Zend\View\Resolver\ResolverInterface|m\MockInterface
     */
    protected $mockResolver;

    /**
     * @var Sut
     */
    protected $sut;

    /** @var m\MockInterface */
    protected $getPlaceholder;

    public function setUp()
    {
        parent::setUp();

        $this->mockRenderer = m::mock('Zend\View\Renderer\RendererInterface');
        $this->mockResolver = m::mock('Zend\View\Resolver\ResolverInterface');
        $this->getPlaceholder = m::mock(GetPlaceholderFactory::class);

        $sm = Bootstrap::getServiceManager();
        $sm->setService('ViewRenderer', $this->mockRenderer);
        $sm->setService('Zend\View\Resolver\TemplatePathStack', $this->mockResolver);
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
            ->with(Translator::EVENT_MISSING_TRANSLATION, [$this->sut, 'processEvent']);

        $this->sut->attach($events);
    }

    public function testProcessEventForPartial()
    {
        $event = m::mock(\Zend\EventManager\Event::class)
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
        $event = m::mock(\Zend\EventManager\Event::class)
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
         * @var \Zend\I18n\Translator\TranslatorInterface
         */
        $translator = m::mock('Zend\I18n\Translator\TranslatorInterface')
            ->shouldReceive('translate')
            ->with('nested.translation.key')
            ->andReturn('translated substring')
            ->getMock();

        $event = m::mock(\Zend\EventManager\Event::class)
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
        $event = m::mock(\Zend\EventManager\Event::class)
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
        $event = m::mock(\Zend\EventManager\Event::class)
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

        $placeholder = m::mock(Placeholder::class);
        $placeholder->shouldReceive('asString')->once()->andReturn('foo-placeholder');
        $this->getPlaceholder->shouldReceive('__invoke')->once()->andReturn($placeholder);

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

    public function testProcessEventAddMissingLog()
    {
        $translator = m::mock(\Zend\I18n\Translator\TranslatorInterface::class);
        $event = m::mock(\Zend\EventManager\Event::class)
            ->shouldReceive('getTarget')
            ->once()
            ->andReturn($translator)
            //
            ->shouldReceive('getParams')
            ->andReturn(
                [
                    'locale' => 'cy_GB',
                    'message' => 'MESSAGE1',
                ]
            )
            ->getMock();

        $translationLogger = m::mock(\Dvsa\Olcs\Utils\Translation\TranslatorLogger::class);
        $translationLogger->shouldReceive('logTranslation')->with('MESSAGE1', $translator)->once();

        $this->sut->setTranslationLogger($translationLogger);
        $this->sut->processEvent($event);
    }

    public function testDontLogMissingTranslationForNi()
    {
        $translator = m::mock(\Zend\I18n\Translator\TranslatorInterface::class);
        $event = m::mock(\Zend\EventManager\Event::class)
            ->shouldReceive('getTarget')
            ->once()
            ->andReturn($translator)
            //
            ->shouldReceive('getParams')
            ->andReturn(
                [
                    'locale' => 'en_NI',
                    'message' => 'MESSAGE1',
                ]
            )
            ->getMock();

        $translationLogger = m::mock(\Dvsa\Olcs\Utils\Translation\TranslatorLogger::class);
        $translationLogger->shouldNotReceive('logTranslation');

        $this->sut->setTranslationLogger($translationLogger);
        $this->sut->processEvent($event);
    }
}
