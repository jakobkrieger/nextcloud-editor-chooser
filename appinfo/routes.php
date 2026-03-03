<?php

declare(strict_types=1);

return [
    'routes' => [
        [
            'name' => 'OnlyOffice#connections',
            'url' => '/api/v1/connections',
            'verb' => 'GET',
        ],
        [
            'name' => 'Settings#getSettings',
            'url' => '/api/v1/settings',
            'verb' => 'GET',
        ],
        [
            'name' => 'Settings#saveSettings',
            'url' => '/api/v1/settings',
            'verb' => 'POST',
        ],
    ],
];
