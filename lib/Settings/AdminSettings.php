<?php

declare(strict_types=1);

namespace OCA\EditorChooser\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings {

    public function __construct(
        private readonly IConfig $config,
    ) {
    }

    public function getForm(): TemplateResponse {
        $params = [
            'onlyofficeUrl' => $this->config->getAppValue('editorchooser', 'onlyoffice_url', ''),
            'collaboraUrl' => $this->config->getAppValue('editorchooser', 'collabora_url', ''),
            'connectionLimit' => $this->config->getAppValue('editorchooser', 'connection_limit', '20'),
            'pollInterval' => $this->config->getAppValue('editorchooser', 'poll_interval', '10'),
        ];
        return new TemplateResponse('editorchooser', 'admin', $params, '');
    }

    public function getSection(): string {
        return 'additional';
    }

    public function getPriority(): int {
        return 50;
    }
}
