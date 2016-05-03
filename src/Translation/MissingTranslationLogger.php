<?php

namespace Dvsa\Olcs\Utils\Translation;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\View\Resolver\ResolverInterface as Resolver;
use Dvsa\Olcs\Utils\View\Helper\GetPlaceholderFactory;

/**
 * Purpose of this class is to create a file that shows all the translation used on a specific page
 *
 * To use it, in Common\Module.php setUpTranslator method
 *  1) Comment out the lines that add translation files eg addTranslationFilePattern
 *  2) Add this class as a listener
 *  3) Load the page you want translation for
 *  4) Look in vagrants /tmp folder
 */
class MissingTranslationLogger implements FactoryInterface, ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var string
     */
    private $logName;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setLogName('/tmp/missing-translations-'. date('Y-m-d_H-i-s'));
        return $this;
    }

    /**
     * Attach to the event manager
     *
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $events->attach(Translator::EVENT_MISSING_TRANSLATION, [$this, 'processEvent']);
    }

    /**
     * Set the log file name
     *
     * @param string $logName
     */
    public function setLogName($logName)
    {
        $this->logName = $logName;
    }

    /**
     * Get the log file name
     *
     * @return string
     */
    public function getLogName()
    {
        return $this->logName;
    }

    /**
     * Process an event
     *
     * @param \Zend\EventManager\Event $e
     */
    public function processEvent(\Zend\EventManager\Event $e)
    {
        $params = $e->getParams();
        $message = $params['message'];

        error_log($message . "\n", 3, $this->getLogName());
    }
}