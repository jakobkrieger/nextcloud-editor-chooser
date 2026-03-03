<template>
    <div class="editorchooser-settings">
        <h2>{{ t('editorchooser', 'Editor Chooser Settings') }}</h2>
        <form @submit.prevent="saveSettings">
            <div class="editorchooser-settings__field">
                <label for="onlyoffice-url">{{ t('editorchooser', 'OnlyOffice Server URL') }}</label>
                <input
                    id="onlyoffice-url"
                    v-model="onlyofficeUrl"
                    type="url"
                    :placeholder="t('editorchooser', 'https://onlyoffice.example.com')"
                />
            </div>
            <div class="editorchooser-settings__field">
                <label for="collabora-url">{{ t('editorchooser', 'Collabora Server URL') }}</label>
                <input
                    id="collabora-url"
                    v-model="collaboraUrl"
                    type="url"
                    :placeholder="t('editorchooser', 'https://collabora.example.com')"
                />
            </div>
            <div class="editorchooser-settings__field">
                <label for="jwt-secret">{{ t('editorchooser', 'OnlyOffice JWT Secret') }}</label>
                <input
                    id="jwt-secret"
                    v-model="jwtSecret"
                    type="password"
                    :placeholder="t('editorchooser', 'Leave blank to keep current')"
                />
            </div>
            <div class="editorchooser-settings__field">
                <label for="connection-limit">{{ t('editorchooser', 'Connection Limit') }}</label>
                <input
                    id="connection-limit"
                    v-model.number="connectionLimit"
                    type="number"
                    min="1"
                    max="100"
                />
            </div>
            <div class="editorchooser-settings__field">
                <label for="poll-interval">{{ t('editorchooser', 'Poll Interval (seconds)') }}</label>
                <input
                    id="poll-interval"
                    v-model.number="pollInterval"
                    type="number"
                    min="5"
                    max="300"
                />
            </div>
            <div class="editorchooser-settings__actions">
                <NcButton type="primary" native-type="submit" :disabled="saving">
                    {{ saving ? t('editorchooser', 'Saving\u2026') : t('editorchooser', 'Save') }}
                </NcButton>
                <span v-if="saveSuccess" class="editorchooser-settings__success">
                    ✓ {{ t('editorchooser', 'Settings saved') }}
                </span>
            </div>
        </form>
    </div>
</template>

<script>
import { NcButton } from '@nextcloud/vue'
import { t } from '@nextcloud/l10n'
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

export default {
    name: 'AdminSettings',

    components: { NcButton },

    data() {
        return {
            onlyofficeUrl: '',
            collaboraUrl: '',
            jwtSecret: '',
            connectionLimit: 20,
            pollInterval: 10,
            saving: false,
            saveSuccess: false,
        }
    },

    async mounted() {
        await this.loadSettings()
    },

    methods: {
        t,

        async loadSettings() {
            try {
                const response = await axios.get(generateUrl('/apps/editorchooser/api/v1/settings'))
                const data = response.data
                this.onlyofficeUrl = data.onlyofficeUrl || ''
                this.collaboraUrl = data.collaboraUrl || ''
                this.connectionLimit = data.connectionLimit || 20
                this.pollInterval = data.pollInterval || 10
            } catch (e) {
                console.error('Failed to load editor chooser settings', e)
            }
        },

        async saveSettings() {
            this.saving = true
            this.saveSuccess = false
            try {
                await axios.post(generateUrl('/apps/editorchooser/api/v1/settings'), {
                    onlyofficeUrl: this.onlyofficeUrl,
                    collaboraUrl: this.collaboraUrl,
                    jwtSecret: this.jwtSecret,
                    connectionLimit: this.connectionLimit,
                    pollInterval: this.pollInterval,
                })
                this.saveSuccess = true
                this.jwtSecret = ''
                setTimeout(() => { this.saveSuccess = false }, 3000)
            } catch (e) {
                console.error('Failed to save editor chooser settings', e)
            } finally {
                this.saving = false
            }
        },
    },
}
</script>

<style scoped>
.editorchooser-settings {
    max-width: 600px;
}

.editorchooser-settings__field {
    margin-bottom: 16px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.editorchooser-settings__field label {
    font-weight: bold;
}

.editorchooser-settings__field input {
    width: 100%;
    max-width: 400px;
}

.editorchooser-settings__actions {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 8px;
}

.editorchooser-settings__success {
    color: var(--color-success);
}
</style>
