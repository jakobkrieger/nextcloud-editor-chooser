# Editor Chooser — Nextcloud App

A native Nextcloud app that lets users choose between **OnlyOffice** and **Collabora Online** when opening documents. It monitors the OnlyOffice Community Edition connection limit and offers read-only mode or Collabora as a fallback when the limit is reached.

## Overview

When you right-click (or use the ⋯ menu on) a supported document file (`.docx`, `.xlsx`, `.pptx`, `.odt`, `.ods`, `.odp`), the **Editor Chooser** app adds an **"Open with…"** context menu entry. Clicking it opens a modal where you can choose your preferred editor.

### Key Features

- **Editor selection** — choose between OnlyOffice and Collabora Online per-document
- **Connection monitoring** — live counter showing active OnlyOffice connections (colour-coded: green / yellow / red)
- **Graceful fallback** — when the OnlyOffice 20-connection limit is reached:
  - OnlyOffice read-only mode is offered as an alternative
  - Collabora is highlighted as the recommended fallback
- **Admin settings** — configure server URLs, JWT secret, connection limit, and poll interval

## Requirements

- **Nextcloud** 28 or later
- **OnlyOffice** Nextcloud app (recommended: Community Document Server or self-hosted Document Server)
- **Nextcloud Office** (richdocuments) app for Collabora support

## Installation

1. Copy the `editorchooser` folder into your Nextcloud `apps/` directory:
   ```bash
   cp -r editorchooser /var/www/nextcloud/apps/
   ```
2. Install PHP dependencies:
   ```bash
   cd /var/www/nextcloud/apps/editorchooser
   composer install --no-dev
   ```
3. Install JS dependencies and build:
   ```bash
   npm ci
   npm run build
   ```
4. Enable the app in the Nextcloud admin panel under **Apps → Tools → Editor Chooser**.

## Configuration

1. Go to **Administration → Additional settings** in your Nextcloud admin panel.
2. Find the **Editor Chooser Settings** section and configure:
   - **OnlyOffice Server URL** — the URL of your OnlyOffice Document Server (e.g. `https://onlyoffice.example.com`)
   - **Collabora Server URL** — the URL of your Collabora Online server (e.g. `https://collabora.example.com`)
   - **OnlyOffice JWT Secret** — the JWT secret configured on your Document Server
   - **Connection Limit** — the maximum number of simultaneous OnlyOffice editing connections (default: 20)
   - **Poll Interval** — how often (in seconds) the modal checks the connection count (default: 10)

## OnlyOffice Info Endpoint

The connection monitoring feature relies on the OnlyOffice Document Server's `/info/info.json` endpoint.

Enable it by adding `"infoEnabled": true` to your Document Server's `local.json` configuration file:

```json
{
  "services": {
    "CoAuthoring": {
      "server": {
        "info": {
          "enabled": true
        }
      }
    }
  }
}
```

After restarting the Document Server, verify the endpoint is accessible:
```bash
curl https://onlyoffice.example.com/info/info.json
```

## Development Setup

1. Set up a local Nextcloud development environment (see [Nextcloud Development docs](https://docs.nextcloud.com/server/latest/developer_manual/getting_started/devenv.html)).
2. Symlink or copy this app into the `apps/` directory.
3. Start the JS watcher:
   ```bash
   npm run watch
   ```
4. Enable the app:
   ```bash
   php occ app:enable editorchooser
   ```

## License

AGPL-3.0-or-later