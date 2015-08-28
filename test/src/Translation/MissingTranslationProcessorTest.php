<?php

namespace Dvsa\OlcsTest\Utils\Service\Translator;

use Dvsa\OlcsTest\Utils\Bootstrap;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;
use Zend\EventManager\EventManagerInterface;
use Zend\I18n\Translator\Translator;
use Dvsa\Olcs\Utils\Translation\MissingTranslationProcessor as Sut;

/**
 * Class MissingTranslationProcessorTest
 * @package CommonTest\Service\Translator
 */
class MissingTranslationProcessorTest extends TestCase
{
    /**
     * @var \Zend\View\Renderer\RendererInterface
     */
    protected $mockRenderer;

    /**
     * @var \Zend\View\Resolver\ResolverInterface
     */
    protected $mockResolver;

    /**
     * @var Sut
     */
    protected $sut;

    public function setUp()
    {
        parent::setUp();

        $this->mockRenderer = m::mock('Zend\View\Renderer\RendererInterface');
        $this->mockResolver = m::mock('Zend\View\Resolver\ResolverInterface');

        $sm = Bootstrap::getServiceManager();
        $sm->setService('ViewRenderer', $this->mockRenderer);
        $sm->setService('Zend\View\Resolver\TemplatePathStack', $this->mockResolver);

        $this->sut = new Sut();
        $this->sut->createService($sm);
    }

    public function testAttach()
    {
        $events = m::mock(EventManagerInterface::class);
        $events->shouldReceive('attach')
            ->once()
            ->with(Translator::EVENT_MISSING_TRANSLATION, [$this->sut, 'processEvent']);

        $this->sut->attach($events);
    }

    public function testProcessEventForPartial()
    {
        $event = m::mock()
            ->shouldReceive('getTarget')
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

        $this->assertEquals('markup', $this->sut->processEvent($event));
    }

    public function testProcessEventForPartialNi()
    {
        $event = m::mock()
            ->shouldReceive('getTarget')
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

        $this->assertEquals('markup', $this->sut->processEvent($event));
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

        $event = m::mock()
            ->shouldReceive('getTarget')
            ->andReturn($translator)
            ->shouldReceive('getParams')
            ->andReturn(
                [
                    'message' => 'text with a {nested.translation.key} in it',
                ]
            )
            ->getMock();

        $this->mockResolver->shouldReceive('resolve')->never();
        $this->mockRenderer->shouldReceive('render')->never();

        $this->assertEquals(
            'text with a translated substring in it',
            $this->sut->processEvent($event)
        );
    }

    public function testOtherMissingKeysDontTriggerTemplateResolver()
    {
        $event = m::mock()
            ->shouldReceive('getTarget')
            ->shouldReceive('getParams')
            ->andReturn(['message' => 'missing.key'])
            ->getMock();

        $this->mockResolver->shouldReceive('resolve')->never();
        $this->mockRenderer->shouldReceive('render')->never();

        $this->assertEquals('missing.key', $this->sut->processEvent($event));
    }
}
