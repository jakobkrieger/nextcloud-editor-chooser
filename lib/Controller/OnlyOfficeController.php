<?php

declare(strict_types=1);

namespace OCA\EditorChooser\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\Http\Client\IClientService;
use OCP\IConfig;
use OCP\IRequest;

class OnlyOfficeController extends Controller {

    public function __construct(
        string $appName,
        IRequest $request,
        private readonly IClientService $clientService,
        private readonly IConfig $config,
    ) {
        parent::__construct($appName, $request);
    }

    #[NoAdminRequired]
    #[NoCSRFRequired]
    public function connections(): DataResponse {
        $onlyofficeUrl = $this->config->getAppValue('editorchooser', 'onlyoffice_url', '');
        $limit = (int) $this->config->getAppValue('editorchooser', 'connection_limit', '20');

        if (empty($onlyofficeUrl)) {
            return new DataResponse([
                'active' => null,
                'limit' => $limit,
                'limitReached' => false,
                'error' => 'not_configured',
            ]);
        }

        $infoUrl = rtrim($onlyofficeUrl, '/') . '/info/info.json';

        try {
            $client = $this->clientService->newClient();
            $response = $client->get($infoUrl, [
                'timeout' => 5,
                'verify' => false,
            ]);
            $body = json_decode($response->getBody(), true);
            $active = $body['connections']['edit'] ?? 0;
            return new DataResponse([
                'active' => $active,
                'limit' => $limit,
                'limitReached' => $active >= $limit,
            ]);
        } catch (\Exception $e) {
            return new DataResponse([
                'active' => null,
                'limit' => $limit,
                'limitReached' => false,
                'error' => 'unreachable',
            ]);
        }
    }
}
