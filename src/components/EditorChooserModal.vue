<template>
    <NcModal
        :show="show"
        :name="t('editorchooser', 'Open with\u2026')"
        @close="close"
    >
        <div class="editorchooser-modal">
            <h2 class="editorchooser-modal__filename">{{ node.basename }}</h2>

            <!-- Connection status -->
            <div class="editorchooser-modal__status" :class="statusClass">
                <template v-if="connectionStatus === 'loading'">
                    <NcLoadingIcon :size="16" />
                    {{ t('editorchooser', 'Checking OnlyOffice status\u2026') }}
                </template>
                <template v-else-if="connectionStatus === 'error'">
                    ⚠️ {{ t('editorchooser', 'OnlyOffice status unknown') }}
                </template>
                <template v-else-if="connectionStatus === 'not_configured'">
                    {{ t('editorchooser', 'OnlyOffice not configured') }}
                </template>
                <template v-else>
                    {{ t('editorchooser', 'OnlyOffice: {active} / {limit} connections', { active: connections.active, limit: connections.limit }) }}
                </template>
            </div>

            <!-- Limit reached banner -->
            <div v-if="limitReached" class="editorchooser-modal__banner editorchooser-modal__banner--error">
                ⚠️ {{ t('editorchooser', 'OnlyOffice connection limit reached ({limit}/{limit}). You cannot open this document for editing in OnlyOffice right now.', { limit: connections.limit }) }}
            </div>

            <!-- Buttons -->
            <div class="editorchooser-modal__actions">
                <template v-if="limitReached">
                    <NcButton @click="openOnlyOffice('view')">
                        {{ t('editorchooser', 'Open in OnlyOffice (Read-Only)') }}
                    </NcButton>
                    <NcButton :disabled="true" :title="t('editorchooser', 'Connection limit reached')">
                        {{ t('editorchooser', 'Open in OnlyOffice (Edit)') }}
                    </NcButton>
                    <NcButton type="primary" @click="openCollabora">
                        {{ t('editorchooser', 'Open in Collabora (Edit)') }}
                    </NcButton>
                </template>
                <template v-else>
                    <NcButton @click="openOnlyOffice('edit')">
                        {{ t('editorchooser', 'Open in OnlyOffice') }}
                    </NcButton>
                    <NcButton type="primary" @click="openCollabora">
                        {{ t('editorchooser', 'Open in Collabora') }}
                    </NcButton>
                </template>
            </div>
        </div>
    </NcModal>
</template>

<script>
import { NcModal, NcButton, NcLoadingIcon } from '@nextcloud/vue'
import { t } from '@nextcloud/l10n'
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

export default {
    name: 'EditorChooserModal',

    components: {
        NcModal,
        NcButton,
        NcLoadingIcon,
    },

    props: {
        node: {
            type: Object,
            required: true,
        },
        onClose: {
            type: Function,
            default: () => {},
        },
    },

    data() {
        return {
            show: true,
            connections: {
                active: null,
                limit: 20,
                limitReached: false,
            },
            connectionStatus: 'loading',
            pollTimer: null,
            pollInterval: 10000,
        }
    },

    computed: {
        limitReached() {
            return this.connections.limitReached
        },
        statusClass() {
            if (this.connectionStatus === 'error' || this.connectionStatus === 'not_configured') return ''
            if (this.connectionStatus === 'loading') return ''
            const active = this.connections.active
            const limit = this.connections.limit
            if (active >= limit) return 'editorchooser-modal__status--red'
            if (active >= limit * 0.75) return 'editorchooser-modal__status--yellow'
            return 'editorchooser-modal__status--green'
        },
    },

    async mounted() {
        await this.loadSettings()
        await this.fetchConnections()
    },

    beforeUnmount() {
        if (this.pollTimer) clearInterval(this.pollTimer)
    },

    methods: {
        t,

        async loadSettings() {
            try {
                const response = await axios.get(generateUrl('/apps/editorchooser/api/v1/settings'))
                this.pollInterval = (response.data.pollInterval || 10) * 1000
                this.connections.limit = response.data.connectionLimit || 20
            } catch (e) {
                // use defaults
            }
        },

        async fetchConnections() {
            try {
                const response = await axios.get(generateUrl('/apps/editorchooser/api/v1/connections'))
                const data = response.data
                if (data.error === 'unreachable') {
                    this.connectionStatus = 'error'
                } else if (data.error === 'not_configured') {
                    this.connectionStatus = 'not_configured'
                } else {
                    this.connections = data
                    this.connectionStatus = 'ok'
                }
            } catch (e) {
                this.connectionStatus = 'error'
            }
            // Start polling
            if (!this.pollTimer) {
                this.pollTimer = setInterval(() => this.fetchConnections(), this.pollInterval)
            }
        },

        openOnlyOffice(mode) {
            const fileId = this.node.fileid
            let url = generateUrl('/apps/onlyoffice/{fileId}', { fileId })
            if (mode === 'view') {
                url += '?action=view'
            }
            window.open(url, '_blank')
            this.close()
        },

        openCollabora() {
            const fileId = this.node.fileid
            const url = generateUrl('/apps/richdocuments/direct/{fileId}', { fileId })
            window.open(url, '_blank')
            this.close()
        },

        close() {
            this.show = false
            if (this.pollTimer) {
                clearInterval(this.pollTimer)
                this.pollTimer = null
            }
            this.onClose()
        },
    },
}
</script>

<style scoped>
.editorchooser-modal {
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    min-width: 360px;
}

.editorchooser-modal__filename {
    font-size: 1.1em;
    font-weight: bold;
    word-break: break-word;
}

.editorchooser-modal__status {
    font-size: 0.9em;
    color: var(--color-text-lighter);
    display: flex;
    align-items: center;
    gap: 8px;
}

.editorchooser-modal__status--green {
    color: var(--color-success);
}

.editorchooser-modal__status--yellow {
    color: var(--color-warning);
}

.editorchooser-modal__status--red {
    color: var(--color-error);
}

.editorchooser-modal__banner {
    padding: 12px;
    border-radius: var(--border-radius);
    font-size: 0.9em;
}

.editorchooser-modal__banner--error {
    background-color: var(--color-error-light);
    color: var(--color-error);
    border: 1px solid var(--color-error);
}

.editorchooser-modal__actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
</style>
