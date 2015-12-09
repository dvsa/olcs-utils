<?php

/**
 * Module
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Utils;

use Dvsa\Olcs\Utils\Auth\AuthHelper;

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
                    // Local
                    'olcs-internal.olcs.gov.uk',
                    'olcs-selfserve.olcs.gov.uk',
                    'olcs-backend.olcs.gov.uk',
                    // Dev
                    'internal.dev.olcs.mgt.mtpdvsa',
                    'selfserve.dev.olcs.mgt.mtpdvsa',
                    'backend.dev.olcs.mgt.mtpdvsa',
                    // OpenAM Dev
                    'internal.openam-dev.olcs.mgt.mtpdvsa',
                    'selfserve.openam-dev.olcs.mgt.mtpdvsa',
                    'backend.openam-dev.olcs.mgt.mtpdvsa',
                ]
            ],
            'non-openam' => [
                'hosts' => [
                    // Local
                    'olcs-internal',
                    'olcs-selfserve',
                    'olcs-backend',
                    // Dev underscore
                    'dev_dvsa-internal.web01.olcs.mgt.mtpdvsa',
                    'dev_dvsa-selfserve.web01.olcs.mgt.mtpdvsa',
                    'dev_dvsa-backend.web01.olcs.mgt.mtpdvsa',
                    // Dev hyphen
                    'dev-dvsa-internal.web01.olcs.mgt.mtpdvsa',
                    'dev-dvsa-selfserve.web01.olcs.mgt.mtpdvsa',
                    'dev-dvsa-backend.web01.olcs.mgt.mtpdvsa',
                    // Dev external
                    'mtp-dev-olcs-internal.i-env.net',
                    'mtp-dev-olcs-selfserve.i-env.net',
                    // Test
                    'test-dvsa-internal.web02.olcs.mgt.mtpdvsa',
                    'test-dvsa-selfserve.web02.olcs.mgt.mtpdvsa',
                    'test-dvsa-backend.web02.olcs.mgt.mtpdvsa',
                    // Test underscore
                    'test_dvsa-internal.web02.olcs.mgt.mtpdvsa',
                    'test_dvsa-selfserve.web02.olcs.mgt.mtpdvsa',
                    'test_dvsa-backend.web02.olcs.mgt.mtpdvsa',
                    // Demo
                    'demo-dvsa-internal.web03.olcs.mgt.mtpdvsa',
                    'demo-dvsa-selfserve.web03.olcs.mgt.mtpdvsa',
                    'demo-dvsa-backend.web03.olcs.mgt.mtpdvsa',
                    // Demo underscore
                    'demo_dvsa-internal.web03.olcs.mgt.mtpdvsa',
                    'demo_dvsa-selfserve.web03.olcs.mgt.mtpdvsa',
                    'demo_dvsa-backend.web03.olcs.mgt.mtpdvsa',
                    // Demo dvsa
                    'demo-olcs-selfserve.i-env.net',
                    'demo-olcs-internal.i-env.net',
                    // Demo external
                    'open-demo-olcs-selfserve.i-env.net',
                    'open-demo-olcs-internal.i-env.net',
                    // Integration
                    'integration-dvsa-selfserve.web04.olcs.mgt.mtpdvsa',
                    'integration-dvsa-internal.web04.olcs.mgt.mtpdvsa',
                    'integration-dvsa-backend.web04.olcs.mgt.mtpdvsa',
                    // Integration underscore
                    'integration_dvsa-selfserve.web04.olcs.mgt.mtpdvsa',
                    'integration_dvsa-internal.web04.olcs.mgt.mtpdvsa',
                    'integration_dvsa-backend.web04.olcs.mgt.mtpdvsa',
                    // Integration dvsa
                    'integration-olcs-internal.i-env.net',
                    'integration-olcs-selfserve.i-env.net',
                    // CIT
                    'selfserve.cit.olcs.mgt.mtpdvsa',
                    'internal.cit.olcs.mgt.mtpdvsa',
                    'backend.cit.olcs.mgt.mtpdvsa',
                    // Data
                    'selfserve.data.olcs.mgt.mtpdvsa',
                    'internal.data.olcs.mgt.mtpdvsa',
                    'backend.data.olcs.mgt.mtpdvsa',
                    // Pre sit
                    'internal.pre-sit.olcs.mgt.mtpdvsa',
                    'selfserve.pre-sit.olcs.mgt.mtpdvsa',
                    'backend.pre-sit.olcs.mgt.mtpdvsa',
                ]
            ],
            'api-map' => [
                // Local
                'olcs-internal' => 'olcs-backend',
                'olcs-selfserve' => 'olcs-backend',
                // Local OpenAM
                'olcs-internal.olcs.gov.uk' => 'olcs-backend.olcs.gov.uk',
                'olcs-selfserve.olcs.gov.uk' => 'olcs-backend.olcs.gov.uk',
                // Dev underscore
                'dev_dvsa-internal.web01.olcs.mgt.mtpdvsa' => 'dev_dvsa-backend.web01.olcs.mgt.mtpdvsa',
                'dev_dvsa-selfserve.web01.olcs.mgt.mtpdvsa' => 'dev_dvsa-backend.web01.olcs.mgt.mtpdvsa',
                // Dev hyphen
                'dev-dvsa-internal.web01.olcs.mgt.mtpdvsa' => 'dev-dvsa-backend.web01.olcs.mgt.mtpdvsa',
                'dev-dvsa-selfserve.web01.olcs.mgt.mtpdvsa' => 'dev-dvsa-backend.web01.olcs.mgt.mtpdvsa',
                // Dev external
                'mtp-dev-olcs-internal.i-env.net' => 'dev-dvsa-backend.web01.olcs.mgt.mtpdvsa',
                'mtp-dev-olcs-selfserve.i-env.net' => 'dev-dvsa-backend.web01.olcs.mgt.mtpdvsa',
                // Dev OpenAM
                'internal.dev.olcs.mgt.mtpdvsa' => 'backend.dev.olcs.mgt.mtpdvsa',
                'selfserve.dev.olcs.mgt.mtpdvsa' => 'backend.dev.olcs.mgt.mtpdvsa',
                'internal.openam-dev.olcs.mgt.mtpdvsa' => 'backend.openam-dev.olcs.mgt.mtpdvsa',
                'selfserve.openam-dev.olcs.mgt.mtpdvsa' => 'backend.openam-dev.olcs.mgt.mtpdvsa',
                // Test
                'test-dvsa-internal.web02.olcs.mgt.mtpdvsa' => 'test-dvsa-backend.web02.olcs.mgt.mtpdvsa',
                'test-dvsa-selfserve.web02.olcs.mgt.mtpdvsa' => 'test-dvsa-backend.web02.olcs.mgt.mtpdvsa',
                // Test underscore
                'test_dvsa-internal.web02.olcs.mgt.mtpdvsa' => 'test_dvsa-backend.web02.olcs.mgt.mtpdvsa',
                'test_dvsa-selfserve.web02.olcs.mgt.mtpdvsa' => 'test_dvsa-backend.web02.olcs.mgt.mtpdvsa',
                // Demo
                'demo-dvsa-internal.web03.olcs.mgt.mtpdvsa' => 'demo-dvsa-backend.web03.olcs.mgt.mtpdvsa',
                'demo-dvsa-selfserve.web03.olcs.mgt.mtpdvsa' => 'demo-dvsa-backend.web03.olcs.mgt.mtpdvsa',
                // Demo underscore
                'demo_dvsa-internal.web03.olcs.mgt.mtpdvsa' => 'demo_dvsa-backend.web03.olcs.mgt.mtpdvsa',
                'demo_dvsa-selfserve.web03.olcs.mgt.mtpdvsa' => 'demo_dvsa-backend.web03.olcs.mgt.mtpdvsa',
                // Demo dvsa
                'demo-olcs-selfserve.i-env.net' => 'demo-dvsa-backend.web03.olcs.mgt.mtpdvsa',
                'demo-olcs-internal.i-env.net' => 'demo-dvsa-backend.web03.olcs.mgt.mtpdvsa',
                // Demo external
                'open-demo-olcs-selfserve.i-env.net' => 'demo-dvsa-backend.web03.olcs.mgt.mtpdvsa',
                'open-demo-olcs-internal.i-env.net' => 'demo-dvsa-backend.web03.olcs.mgt.mtpdvsa',
                // Integration
                'integration-dvsa-selfserve.web04.olcs.mgt.mtpdvsa'
                    => 'integration-dvsa-backend.web04.olcs.mgt.mtpdvsa',
                'integration-dvsa-internal.web04.olcs.mgt.mtpdvsa' => 'integration-dvsa-backend.web04.olcs.mgt.mtpdvsa',
                // Integration underscore
                'integration_dvsa-selfserve.web04.olcs.mgt.mtpdvsa'
                    => 'integration_dvsa-backend.web04.olcs.mgt.mtpdvsa',
                'integration_dvsa-internal.web04.olcs.mgt.mtpdvsa' => 'integration_dvsa-backend.web04.olcs.mgt.mtpdvsa',
                // Integration dvsa
                'integration-olcs-internal.i-env.net' => 'integration-dvsa-backend.web04.olcs.mgt.mtpdvsa',
                'integration-olcs-selfserve.i-env.net' => 'integration-dvsa-backend.web04.olcs.mgt.mtpdvsa',
                // CIT
                'selfserve.cit.olcs.mgt.mtpdvsa' => 'backend.cit.olcs.mgt.mtpdvsa',
                'internal.cit.olcs.mgt.mtpdvsa' => 'backend.cit.olcs.mgt.mtpdvsa',
                // Data
                'selfserve.data.olcs.mgt.mtpdvsa' => 'backend.data.olcs.mgt.mtpdvsa',
                'internal.data.olcs.mgt.mtpdvsa' => 'backend.data.olcs.mgt.mtpdvsa',
                // Pre sit
                'internal.pre-sit.olcs.mgt.mtpdvsa' => 'backend.pre-sit.olcs.mgt.mtpdvsa',
                'selfserve.pre-sit.olcs.mgt.mtpdvsa' => 'backend.pre-sit.olcs.mgt.mtpdvsa',
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
