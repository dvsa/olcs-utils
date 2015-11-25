<?php

/**
 * Module
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Utils;

use Dvsa\Olcs\Utils\Auth\AuthHelper;
use Zend\ModuleManager\ModuleManager;

/**
 * Module
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Module
{
    public function init($moduleManager)
    {
        /**
         * NOTE Not ideal to hardcode the config in here, but this is only temporary
         * and due to certain complications it can't go anywhere else
         */
        $config = [
            'openam' => [
                'hosts' => [
                    'olcs-internal.olcs.gov.uk',
                    'olcs-selfserve.olcs.gov.uk',
                    'olcs-backend.olcs.gov.uk'
                ]
            ],
            'non-openam' => [
                'hosts' => [
                    'olcs-internal',
                    'olcs-selfserve',
                    'olcs-backend',
                    'dev_dvsa-internal.web01.olcs.mgt.mtpdvsa',
                    'dev_dvsa-selfserve.web01.olcs.mgt.mtpdvsa',
                    'dev_dvsa-backend.web01.olcs.mgt.mtpdvsa',
                    'dev-dvsa-internal.web01.olcs.mgt.mtpdvsa',
                    'dev-dvsa-selfserve.web01.olcs.mgt.mtpdvsa',
                    'dev-dvsa-backend.web01.olcs.mgt.mtpdvsa'
                ]
            ],
            'api-map' => [
                'olcs-internal' => 'olcs-backend',
                'olcs-selfserve' => 'olcs-backend',
                'olcs-internal.olcs.gov.uk' => 'olcs-backend.olcs.gov.uk',
                'olcs-selfserve.olcs.gov.uk' => 'olcs-backend.olcs.gov.uk',
                'dev_dvsa-internal.web01.olcs.mgt.mtpdvsa' => 'dev_dvsa-backend.web01.olcs.mgt.mtpdvsa',
                'dev_dvsa-selfserve.web01.olcs.mgt.mtpdvsa' => 'dev_dvsa-backend.web01.olcs.mgt.mtpdvsa',
                'dev-dvsa-internal.web01.olcs.mgt.mtpdvsa' => 'dev-dvsa-backend.web01.olcs.mgt.mtpdvsa',
                'dev-dvsa-selfserve.web01.olcs.mgt.mtpdvsa' => 'dev-dvsa-backend.web01.olcs.mgt.mtpdvsa',
            ]
        ];
        AuthHelper::setConfig($config);
    }

    /**
     * Get module config
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
