<?php

declare(strict_types=1);

namespace OCA\EditorChooser\AppInfo;

use OCA\EditorChooser\Listener\LoadAdditionalScriptsListener;
use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\Util;

class Application extends App implements IBootstrap {
    public const APP_ID = 'editorchooser';

    public function __construct(array $urlParams = []) {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void {
        $context->registerEventListener(
            LoadAdditionalScriptsEvent::class,
            LoadAdditionalScriptsListener::class,
        );
    }

    public function boot(IBootContext $context): void {
        Util::addInitScript(self::APP_ID, 'editorchooser-fileaction');
    }
}
