<?php

declare(strict_types=1);

namespace OCA\EditorChooser\Listener;

use OCA\EditorChooser\AppInfo\Application;
use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Util;

/**
 * @template-implements IEventListener<LoadAdditionalScriptsEvent>
 */
class LoadAdditionalScriptsListener implements IEventListener {

    public function handle(Event $event): void {
        if (!($event instanceof LoadAdditionalScriptsEvent)) {
            return;
        }
        Util::addInitScript(Application::APP_ID, 'editorchooser-fileaction');
    }
}
