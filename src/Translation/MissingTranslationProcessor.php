<?php

/**
 * Missing Translation Processor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\Olcs\Utils\Translation;

use Dvsa\Olcs\Utils\View\Factory\Helper\GetPlaceholder;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\I18n\Translator\Translator;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Renderer\RendererInterface as Renderer;
use Laminas\View\Resolver\ResolverInterface as Resolver;
use Dvsa\Olcs\Utils\View\Factory\Helper\GetPlaceholderFactory;
use Interop\Container\ContainerInterface;

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
     * @var GetPlaceholder
     */
    protected $placeholder;

    /**
     * Factory
     *
     * @param ServiceLocatorInterface $serviceLocator ServiceLocator
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null): MissingTranslationProcessor
    {
        return $this($serviceLocator, MissingTranslationProcessor::class);
    }

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $events->attach(Translator::EVENT_MISSING_TRANSLATION, [$this, 'processEvent'], $priority);
    }

    /**
     * Process an event
     *
     * @param \Laminas\EventManager\Event $e Event
     *
     * @return string|void
     */
    public function processEvent(\Laminas\EventManager\Event $e)
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
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return $this
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MissingTranslationProcessor
    {
        $this->renderer = $container->get('ViewRenderer');
        if ($container->get('ViewHelperManager')->has('getPlaceholder')) {
            $this->placeholder = $container->get('ViewHelperManager')->get('getPlaceholder');
        }
        $this->resolver = $container->get('Laminas\View\Resolver\TemplatePathStack');
        return $this;
    }
}
