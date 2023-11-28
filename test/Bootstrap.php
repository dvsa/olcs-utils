<?php

namespace Dvsa\OlcsTest\Utils;

use Laminas\ServiceManager\ServiceManager;
use Mockery as m;

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap
{
    protected static $config = array();

    public static function init()
    {
        ini_set('memory_limit', '1G');

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
     * @return \Laminas\ServiceManager\ServiceManager
     */
    public static function getServiceManager()
    {
        $sm = m::mock(ServiceManager::class);

        $sm->shouldReceive('setService')
            ->andReturnUsing(
                function ($alias, $service) use ($sm) {
                    $sm->shouldReceive('get')->with($alias)->andReturn($service);
                    $sm->shouldReceive('has')->with($alias)->andReturn(true);
                    return $sm;
                }
            );

        return $sm;
    }
}
