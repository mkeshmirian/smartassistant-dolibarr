# Smart Assistant for Dolibarr — Installation & Testing Guide (for DoliStore reviewers)

**Version:** 1.0 · **Date:** 2026-08-05 · **Publisher:** Integmia

This guide walks through installing the Smart Assistant module on a Dolibarr 18+
instance, connecting it to the hosted service, and verifying it works.

---

## 1. What You Need

- A Dolibarr instance **18+** (tested on 23.x), PHP 7.0+
- Dolibarr **REST API enabled** and a **read-only API key** (created for a user in
  Users & Groups → user → Edit → API key)
- The module ZIP: `smartassistant-1.0.0.zip`

---

## 2. Install the Module

**Option A — via the module installer:**

1. Log in as administrator → **Setup → Modules/Applications**
2. Click **Install external module** (top of the page)
3. Upload `smartassistant-1.0.0.zip`
4. The module appears in the list as **Smart Assistant**

**Option B — manual (FTP):**

1. Copy the `smartassistant/` folder from the ZIP into `htdocs/custom/`
   (result: `htdocs/custom/smartassistant/`)
2. Ensure files are readable by the web server (644) and directories traversable (755)
3. Go to **Setup → Modules/Applications** — Smart Assistant is listed

---

## 3. Enable the Module

1. In **Setup → Modules/Applications**, find **Smart Assistant**
2. Click **Enable** (activate)
3. A **Smart Assistant** top menu appears with **Dashboard** and **Setup** sub-menus
4. *(Optional check: Users & Groups → user rights now include two Smart Assistant
   permissions: "Read Smart Assistant dashboard" and "Manage Smart Assistant connection")*

---

## 4. Connect to the Hosted Service

1. Open **Smart Assistant → Setup**
2. Fill the form:
   - **Hosted service URL:** `https://dolibarr.smartassistant.site`
   - **Dolibarr URL (this instance):** e.g. `https://your-dolibarr.example.com`
   - **Dolibarr API key:** the read-only key from §1
3. Click **Connect**
4. The status flips to **Connected** (a per-instance token is issued and stored)

> The Connect button performs a live verification: the service calls your Dolibarr API
> once with the supplied key. If you see *"Dolibarr rejected the API key (HTTP 401)"*,
> the key is invalid — double-check it (no leading/trailing spaces, exactly as shown in
> the user's API key field).

---

## 5. Verify the Dashboard

1. Open **Smart Assistant → Dashboard**
2. The hosted dashboard embeds inside Dolibarr and shows:
   - The five agents (Revenue, Customer Success, Procurement, Operations, BI) with their
     last run status
   - **Open Recommendations** (prioritized findings)
   - **Pending Actions** (approval workflow)
3. Click **Run all agents** - agents execute immediately against your Dolibarr and new
   recommendations appear (first run may take a minute)
4. Items that target a specific record show a **View in Dolibarr** link - it opens the
   related record (invoice, order, customer, ticket, product...) in a new browser tab, so
   you can review and resolve the issue right in Dolibarr.

### Expected behavior

- Agents read data **read-only** from your Dolibarr.
- Recommendations are stored on the service and displayed on the dashboard.
- Disconnect in **Setup** stops the dashboard; re-connecting re-issues a token.

---

## 6. Troubleshooting

| Symptom | Likely cause / fix |
|---------|--------------------|
| Modules page blank after upload | Re-upload with the module installer, or copy via FTP (files may have been truncated). Ensure `core/modules/modSmartAssistant.class.php` is present (~5.4 KB) |
| Smart Assistant not listed | Check the folder is `htdocs/custom/smartassistant/` (not nested twice) and files are readable (644/755) |
| Connect fails with 401 | API key invalid — verify in Users & Groups → user → API key |
| Connect fails with network error | Hosted service unreachable from your server — check HTTPS egress |
| Dashboard shows "Missing or invalid token" | Re-run Connect (tokens rotate on re-registration) |

---

## 7. Source & License

- **Source (GPLv3):** https://github.com/mkeshmirian/smartassistant-dolibarr
- **License:** GPLv3 or later (full text in `LICENSE` / `docs/COPYING`)
- The hosted service is a separate proprietary product; the module contains no business
  logic.

---

*© 2026 Integmia — Smart Assistant for Dolibarr*
