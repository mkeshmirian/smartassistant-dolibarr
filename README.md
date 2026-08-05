# Smart Assistant — Dolibarr module

Thin, open-source (GPLv3+) connector that embeds the **Smart Assistant**
dashboard in Dolibarr and connects your instance to the hosted AI service:
an AI operations team (Revenue, Customer Success, Procurement, Operations,
BI agents) that monitors your data and produces prioritized recommendations.

**This module intentionally contains no business logic.** All intelligence
(agents, LLM analysis, scheduling, recommendations, state) runs on the Smart
Assistant hosted service. The module:

1. Collects connection details in **Setup** (hosted service URL, this
   Dolibarr's URL + a read-only API key)
2. Registers the instance with the hosted service and stores the issued
   per-instance token in `llx_const`
3. Embeds the dashboard (`index.php`) authenticated with that token

## Install

1. Copy the `smartassistant` folder into `htdocs/custom/` (or install the ZIP
   via **Setup → Modules → Install external module**)
2. **Setup → Modules** → enable **Smart Assistant**
3. Open the **Smart Assistant → Setup** menu: fill the Dolibarr URL and a
   read-only API key, click **Connect**
4. Open **Smart Assistant → Dashboard**

## Hosted service

Default hosted service URL: **`https://dolibarr.smartassistant.site`**
(subdomain of smartassistant.site, which also hosts the separate Zoho widget
app — different origins on purpose, so credentials never share storage).
The URL is configurable in Setup, so a future Docker/local edition only needs
`SMARTASSISTANT_HOSTED_URL` pointed at the local instance.

## Compatibility

- Dolibarr **18+** (developed against 23.0.3)
- PHP 7.0+ (PHP 8 recommended)
- Module ID **194000** (reserved range 194000–194019, claimed on the
  [Dolibarr wiki](https://wiki.dolibarr.org/index.php/List_of_modules_id))

## Packaging for DoliStore

The ZIP must contain the module directory at its root:

```powershell
# from a copy of this repo named smartassistant/
cd ..
Copy-Item smartassistant-dolibarr smartassistant -Recurse
Compress-Archive -Path smartassistant -DestinationPath smartassistant-1.0.0.zip
```

Layout check: `smartassistant/core/modules/modSmartAssistant.class.php` must be
at the top level of the archive. Full GPLv3 text is included (`LICENSE` /
`docs/COPYING`).

## Files

```
smartassistant/
├── core/modules/modSmartAssistant.class.php   module descriptor (ID 194000)
├── admin/setup.php                            connection setup + token issuance
├── index.php                                  dashboard embed page
├── img/smartassistant.png                     module picto
├── langs/en_US/smartassistant.lang            English translations
└── docs/                                      documentation (COPYING = GPLv3)
```

## Roadmap (module side)

- [ ] Short-lived session token exchange (avoid token in iframe URL)
- [ ] `core/triggers/` — notify the hosted service on events (invoice
      validated, ticket created) for event-driven agent runs
- [ ] More languages
- [ ] Docker edition: point `SMARTASSISTANT_HOSTED_URL` at a local instance

## License

GPLv3 or later (see `LICENSE`). The hosted service (Smart Assistant engine) is
a separate, proprietary product; access is controlled by tokens issued by the
service provider.
