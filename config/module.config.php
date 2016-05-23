<?php

return [
    'service_manager' => [
        'factories' => [
            'Utils\NiTextTranslation' => \Dvsa\Olcs\Utils\Translation\NiTextTranslation::class,
            'Utils\MissingTranslationProcessor' => \Dvsa\Olcs\Utils\Translation\MissingTranslationProcessor::class,
            'Utils\MissingTranslationLogger' => \Dvsa\Olcs\Utils\Translation\MissingTranslationLogger::class,
        ]
    ],
    'view_helpers' => [
        'factories' => [
            'getPlaceholder' => \Dvsa\Olcs\Utils\View\Helper\GetPlaceholderFactory::class,
        ]
    ]
];
