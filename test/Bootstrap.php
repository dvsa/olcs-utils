<?php

namespace Dvsa\OlcsTest\Utils;

use Mockery as m;
use Zend\ServiceManager\ServiceManager;

error_reporting(-1);
chdir(dirname(__DIR__));
date_default_timezone_set('Europe/London');

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap
{
    protected static $config = array();

    public static function init()
    {
        ini_set('memory_limit', '1G');
        // Setup the autoloader
        $loader = static::initAutoloader();
        $loader->addPsr4('Dvsa\\OlcsTest\\Utils\\', __DIR__ . '/src');

        // Grab the application config
        $config = array(
            'modules' => array(
                'Dvsa\Olcs\Utils'
            ),
            'module_listener_options' => array(
                'module_paths' => array(
                    __DIR__ . '/../'
                )
            )
        );

        self::$config = $config;

        self::getServiceManager();
    }

    /**
     * Changed this method to return a mock
     *
     * @return \Zend\ServiceManager\ServiceManager
     */
    public static function getServiceManager()
    {
        $sm = m::mock(ServiceManager::class)
            ->makePartial();

        return $sm;
    }

    protected static function initAutoloader()
    {
        return require('vendor/autoload.php');
    }
}

Bootstrap::init();
