<?php

declare(strict_types=1);

namespace OCA\EditorChooser\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\IConfig;
use OCP\IRequest;

class SettingsController extends Controller {

    public function __construct(
        string $appName,
        IRequest $request,
        private readonly IConfig $config,
    ) {
        parent::__construct($appName, $request);
    }

    #[NoAdminRequired]
    #[NoCSRFRequired]
    public function getSettings(): DataResponse {
        return new DataResponse([
            'onlyofficeUrl' => $this->config->getAppValue('editorchooser', 'onlyoffice_url', ''),
            'collaboraUrl' => $this->config->getAppValue('editorchooser', 'collabora_url', ''),
            'connectionLimit' => (int) $this->config->getAppValue('editorchooser', 'connection_limit', '20'),
            'pollInterval' => (int) $this->config->getAppValue('editorchooser', 'poll_interval', '10'),
        ]);
    }

    public function saveSettings(): DataResponse {
        $onlyofficeUrl = $this->request->getParam('onlyofficeUrl', '');
        $collaboraUrl = $this->request->getParam('collaboraUrl', '');
        $jwtSecret = $this->request->getParam('jwtSecret', '');
        $connectionLimit = (int) $this->request->getParam('connectionLimit', 20);
        $pollInterval = (int) $this->request->getParam('pollInterval', 10);

        $this->config->setAppValue('editorchooser', 'onlyoffice_url', $onlyofficeUrl);
        $this->config->setAppValue('editorchooser', 'collabora_url', $collaboraUrl);
        if ($jwtSecret !== '') {
            $this->config->setAppValue('editorchooser', 'jwt_secret', $jwtSecret);
        }
        $this->config->setAppValue('editorchooser', 'connection_limit', (string) $connectionLimit);
        $this->config->setAppValue('editorchooser', 'poll_interval', (string) $pollInterval);

        return new DataResponse(['status' => 'ok']);
    }
}
