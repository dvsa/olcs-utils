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
use Common\View\Helper\GetPlaceholderFactory;

/**
 * Missing Translation Processor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class MissingTranslationProcessor implements FactoryInterface, ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var \Zend\View\Renderer\RendererInterface
     */
    protected $renderer;

    /**
     * @var \Zend\View\Resolver\ResolverInterface
     */
    protected $resolver;

    /**
     * @var GetPlaceholderFactory
     */
    protected $placeholder;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->renderer = $serviceLocator->get('ViewRenderer');

        if ($serviceLocator->get('ViewHelperManager')->has('getPlaceholder')) {
            $this->placeholder = $serviceLocator->get('ViewHelperManager')->get('getPlaceholder');
        }

        $this->resolver = $serviceLocator->get('Zend\View\Resolver\TemplatePathStack');

        return $this;
    }

    public function attach(EventManagerInterface $events)
    {
        $events->attach(Translator::EVENT_MISSING_TRANSLATION, [$this, 'processEvent']);
    }

    /**
     * Process an event
     *
     * @param object $e
     * @return string
     */
    public function processEvent($e)
    {
        $translator = $e->getTarget();
        $params = $e->getParams();

        $message = $params['message'];

        if (preg_match_all('/\{([^\}]+)\}/', $message, $matches)) {
            // handles text with translation keys inside curly braces {}
            foreach ($matches[0] as $key => $match) {
                $message = str_replace($match, $translator->translate($matches[1][$key]), $message);
            }
            return $message;
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

        return $message;
    }

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
}
