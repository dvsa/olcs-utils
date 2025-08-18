<?php

namespace Dvsa\Olcs\Utils\Translation;

use Dvsa\Olcs\Utils\View\Factory\Helper\GetPlaceholder;
use Laminas\EventManager\Event;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\I18n\Translator\Translator;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Renderer\RendererInterface as Renderer;
use Laminas\View\Resolver\ResolverInterface as Resolver;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

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
     * {@inheritdoc}
     */
    #[\Override]
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $events->attach(Translator::EVENT_MISSING_TRANSLATION, [$this, 'processEvent'], $priority);
    }

    /**
     * @return string|void
     */
    public function processEvent(Event $e)
    {
        $translator = $e->getTarget();

        if (!$translator instanceof TranslatorInterface) {
            return;
        }

        $params = $e->getParams();

        $message = $params['message'];

        if (empty($message)) {
            return;
        }

        if (!is_string($message)) {
            return;
        }

        if (preg_match_all('/\{([^}]+)}/', $message, $matches)) {
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

    protected function populatePlaceholder(string $message): string
    {
        if ($this->placeholder === null) {
            return $message;
        }

        if (preg_match_all('/\{\{PLACEHOLDER:([a-zA-Z_0-9]+)}}/', $message, $matches)) {
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[\Override]
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MissingTranslationProcessor
    {
        $this->renderer = $container->get('ViewRenderer');

        if ($container->get('ViewHelperManager')->has('getPlaceholder')) {
            $this->placeholder = $container->get('ViewHelperManager')->get('getPlaceholder');
        }
        $this->resolver = $container->get(\Laminas\View\Resolver\TemplatePathStack::class);
        return $this;
    }
}
