<?php

namespace Dvsa\Olcs\Utils\Translation;

/**
 * Log all the translation used by a Request
 *
 * Uses apcu to cache the messages so that we can eliminate duplicates across different requests
 */
class TranslatorLogger
{
    const CACHE_KEY = 'TranslatorLogger_messagesWritten';
    const CACHE_TTL = 60 * 60 * 24;

    /**
     * @var \Zend\Log\Logger
     */
    private $logger;

    /**
     * @var array
     */
    private $messagesWritten;

    /**
     * @var \Zend\Http\PhpEnvironment\Request
     */
    private $request;

    /**
     * @var \Zend\Cache\Storage\StorageInterface
     */
    private $cache;

    /**
     * TranslatorLogger constructor.
     *
     * @param \Zend\Log\Logger                     $logger  Logger to send message to
     * @param \Zend\Http\PhpEnvironment\Request    $request Request object
     * @param \Zend\Cache\Storage\StorageInterface $cache   Cache
     */
    public function __construct(
        \Zend\Log\LoggerInterface $logger,
        \Zend\Http\PhpEnvironment\Request $request,
        \Zend\Cache\Storage\StorageInterface $cache = null
    ) {
        $this->logger = $logger;
        $this->request = $request;
        $this->messagesWritten = [];

        if ($cache === null) {
            $cache = new \Zend\Cache\Storage\Adapter\Apc(['ttl' => self::CACHE_TTL]);
        }
        $this->cache = $cache;

        if ($this->cache->hasItem(self::CACHE_KEY)) {
            $this->messagesWritten = $this->cache->getItem(self::CACHE_KEY);
        }
    }

    /**
     *  Destructor to clear the file pointer
     */
    public function __destruct()
    {
        if ($this->cache) {
            $this->cache->setItem(self::CACHE_KEY, $this->messagesWritten);
        }
    }

    /**
     * Log translation
     *
     * @param string                                    $message    Message to be logged
     * @param \Zend\I18n\Translator\TranslatorInterface $translator Translator
     *
     * @return void
     */
    public function logTranslation($message, \Zend\I18n\Translator\TranslatorInterface $translator)
    {
        if (in_array($message, $this->messagesWritten)) {
            return;
        }

        $this->messagesWritten[] = $message;

        $this->logger->info(
            'Missing translation',
            [
                'message' => $message,
                'en_GB' => $translator->translate($message),
                'request' => $this->request->getRequestUri(),
            ]
        );
    }
}
