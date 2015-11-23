<?php

/**
 * Auth Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Utils\Auth;

/**
 * Auth Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AuthHelper
{
    private static $config = [];

    private static $isOpenAm;

    /**
     * Inject the auth config
     *
     * @param array $config
     */
    public static function setConfig(array $config)
    {
        self::$config = $config;
    }

    /**
     * Get the determined backend uri
     *
     * @return string
     */
    public static function getBackendUri()
    {
        if (empty(self::$config)) {
            return '';
        }

        return self::$config['api-map'][self::getHostname()];
    }

    /**
     * Determine whether we are using openAm or not
     *
     * @return bool
     */
    public static function isOpenAm()
    {
        // Fallback to isOpenAm = true
        if (empty(self::$config)) {
            return true;
        }

        if (self::$isOpenAm === null) {
            self::$isOpenAm = in_array(self::getHostname(), self::$config['openam']['hosts']);
        }

        return self::$isOpenAm;
    }

    public static function getHostname()
    {
        $hostname = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_HOST');
        if (empty($hostname)) {
            $hostname = filter_input(INPUT_SERVER, 'HTTP_HOST');
        }

        return $hostname;
    }
}
