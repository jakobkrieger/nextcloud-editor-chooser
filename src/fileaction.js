import { registerFileAction, FileAction, DefaultType } from '@nextcloud/files'
import { t } from '@nextcloud/l10n'
import { generateUrl } from '@nextcloud/router'
import { createApp } from 'vue'
import EditorChooserModal from './components/EditorChooserModal.vue'

const SUPPORTED_MIMES = [
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'application/vnd.oasis.opendocument.text',
    'application/vnd.oasis.opendocument.spreadsheet',
    'application/vnd.oasis.opendocument.presentation',
    'application/msword',
    'application/vnd.ms-excel',
    'application/vnd.ms-powerpoint',
]

registerFileAction(new FileAction({
    id: 'editorchooser-open-with',
    displayName: () => t('editorchooser', 'Open with\u2026'),
    default: DefaultType.DEFAULT,
    order: 1000,
    iconSvgInline: () => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>',
    enabled(nodes) {
        if (nodes.length !== 1) return false
        return SUPPORTED_MIMES.includes(nodes[0].mime)
    },
    async exec(node) {
        const container = document.createElement('div')
        document.body.appendChild(container)

        const app = createApp(EditorChooserModal, {
            node,
            onClose() {
                app.unmount()
                document.body.removeChild(container)
            },
        })
        app.mount(container)
        return null
    },
}))
