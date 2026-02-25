# Iconify theme app extension

Theme app extension for **iconify-app**. Adds trust badges & icons to the storefront via an app embed block.

## Structure

- **production-extension/** – Production deploy (use for live app **iconify-app**)
- **staging-extension/** – Staging deploy
- **dev-extension/** – Local development

Each folder contains:

- `extensions/iconito-v2/` – Extension code
  - `shopify.extension.toml` – Extension config (name: iconify-app, type: theme)
  - `blocks/app-embed.liquid` – App embed block (used by “Activate” in the Laravel app)
  - `blocks/iconito_script.liquid` – Staging-only alternate block (kept for backward compatibility)
  - `assets/iconito-front.js` – Storefront script
  - `locales/en.default.json` – Copy/labels

## Deploy

1. **Link to your app** (first time or after cloning):

   From the extension folder (e.g. `production-extension`):

   ```bash
   cd theme-app-extension/production-extension
   npm install
   shopify app config link
   ```

   Choose the **iconify-app** app when prompted. This creates/updates `shopify.app.toml` (usually gitignored).

2. **Deploy the extension**:

   ```bash
   npm run deploy
   ```

   Or:

   ```bash
   shopify app deploy
   ```

3. **Get the extension UUID** after deploy:
   - Partners → **iconify-app** → **Extensions** → Theme app extension → copy **Extension ID**
   - Set in main app `.env`:
     - `SHOPIFY_ICONITO_APP_EXTENSION_ID=<uuid>`
     - `VITE_SHOPIFY_ICONITO_APP_EXTENSION_ID=<uuid>`

## Block handle

The Laravel app expects the app embed block handle **app-embed** (from `blocks/app-embed.liquid`).  
`.env` should have:

- `SHOPIFY_ICONITO_APP_EXTENSION_NAME=app-embed`
- `SHOPIFY_THEME_EXTENSION_APP_HANDLE=iconify-app`

## Merchants: enabling the extension

Merchants enable the app embed in **Theme Editor → App embeds → Iconify – Trust badges & icons**.
