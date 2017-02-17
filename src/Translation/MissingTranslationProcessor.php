<?php

/**
 * Missing Translation Processor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Utils\Translation;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\View\Resolver\ResolverInterface as Resolver;
use Dvsa\Olcs\Utils\View\Factory\Helper\GetPlaceholderFactory;

/**
 * Missing Translation Processor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class MissingTranslationProcessor implements FactoryInterface, ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var TranslatorLogger
     */
    private $translationLogger;

    /**
     * @var GetPlaceholderFactory
     */
    protected $placeholder;

    /**
     * Factory
     *
     * @param ServiceLocatorInterface $serviceLocator ServiceLocator
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->renderer = $serviceLocator->get('ViewRenderer');

        if ($serviceLocator->get('ViewHelperManager')->has('getPlaceholder')) {
            $this->placeholder = $serviceLocator->get('ViewHelperManager')->get('getPlaceholder');
        }

        $this->resolver = $serviceLocator->get('Zend\View\Resolver\TemplatePathStack');

        return $this;
    }

    /**
     * Attach to event manager
     *
     * @param EventManagerInterface $events Event manager
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $events->attach(Translator::EVENT_MISSING_TRANSLATION, [$this, 'processEvent']);
    }

    /**
     * Process an event
     *
     * @param \Zend\EventManager\Event $e Event
     *
     * @return string
     */
    public function processEvent(\Zend\EventManager\Event $e)
    {
        $translator = $e->getTarget();
        $params = $e->getParams();

        $message = $params['message'];

        if (preg_match_all('/\{([^\}]+)\}/', $message, $matches)) {
            // handles text with translation keys inside curly braces {}
            foreach ($matches[0] as $key => $match) {
                $message = str_replace($match, $translator->translate($matches[1][$key]), $message);
            }
        }

        // handles partials as translations. Note we only try to resolve keys
        // that match a pattern, to avoid having to run the template resolver
        // against ALL missing translations
        if (strpos($message, 'markup-') === 0) {
            $locale    = $params['locale'];
            $partial   = $locale . '/' . $message; // e.g. en_GB/my-translation-key
            $foundPath = $this->resolver->resolve($partial);

            // Check for the non-NI version of the file
            if ($foundPath === false && strstr($locale, 'NI')) {
                $fallbackLocale = str_replace('NI', 'GB', $locale);
                $partial   = $fallbackLocale . '/' . $message; // e.g. en_GB/my-translation-key
                $foundPath = $this->resolver->resolve($partial);
            }

            if ($foundPath !== false) {
                $message = $this->renderer->render($partial);
            }

            $message = $this->populatePlaceholder($message);
        }

        // if message has changed (ie its been translated) then return it
        if ($message !== $params['message']) {
            return $message;
        }

        // If logger is setup and not en_GB (ie don't log missing english messages as that is expected)
        if ($this->translationLogger !== null && $params['locale'] !== 'en_GB') {
            // if translationLogger is set then log missing message
            $this->translationLogger->logTranslation($message, $translator);
        }

        // needs to return void so that the event is propagated to other listeners
    }

    /**
     * Populate a placeholder
     *
     * @param string $message Message
     *
     * @return mixed
     */
    protected function populatePlaceholder($message)
    {
        if ($this->placeholder === null) {
            return $message;
        }

        if (preg_match_all('/\{\{PLACEHOLDER\:([a-zA-Z\_0-9]+)\}\}/', $message, $matches)) {

            $placeholderHelper = $this->placeholder;

            foreach ($matches[0] as $index => $match) {

                $placeholder = $placeholderHelper($matches[1][$index])->asString();

                $message = str_replace($match, $placeholder, $message);
            }
        }

        return $message;
    }

    /**
     * Set the TranslationLogger to log to
     *
     * @param TranslatorLogger $translationLogger Translation logger
     *
     * @return void
     */
    public function setTranslationLogger(TranslatorLogger $translationLogger)
    {
        $this->translationLogger = $translationLogger;
    }
}
