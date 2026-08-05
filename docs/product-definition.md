# Smart Assistant for Dolibarr — Product Definition

**Version:** 1.0 · **Date:** 2026-08-05 · **Publisher:** Integmia

---

## 1. Product Summary

**Smart Assistant** is a Dolibarr module that gives small and mid-sized businesses an
**AI operations team** that monitors their ERP continuously and produces prioritized,
actionable recommendations. It embeds a hosted AI dashboard directly inside Dolibarr:
Revenue, Customer Success, Procurement, Operations and BI agents watch the business data,
detect issues (overdue invoices, at-risk customers, low stock, stalled workflows) and
propose concrete next actions — so the owner and their team spend minutes, not days,
on operational review.

> **Tagline:** Your AI operations team, embedded in Dolibarr.

---

## 2. The Problem

Dolibarr users — typically SMBs — run their whole business in the ERP, but:

- **No time to monitor:** invoices go overdue, stock runs low, follow-ups slip, because
  nobody reviews every record every day.
- **Data is scattered:** sales, purchases, stock and support live in different modules;
  spotting a pattern means manual cross-referencing.
- **Reactive, not proactive:** problems are discovered when a customer calls, not when
  the risk first appears.
- **No BI capability:** dashboards show *what happened*; nobody produces *what to do next*.

---

## 3. The Solution

Smart Assistant continuously analyzes the Dolibarr data through **five specialized agents**
and presents the result as a prioritized, human-readable dashboard inside Dolibarr:

| Agent | Domain | What It Watches |
|-------|--------|-----------------|
| **Revenue & Sales Ops** | Sales | Leads, opportunities, quotes, renewals, upsell signals |
| **Customer Success** | Relationships | Health scores, churn risk, retention plays, support trends |
| **Procurement & Inventory** | Supply chain | Reorder points, supplier performance, stock forecasts |
| **Operations Orchestration** | Cross-module | Process bottlenecks, stalled workflows, escalations |
| **Business Intelligence** | Analytics | Trend analysis, executive summaries, cross-agent coordination |

Each agent produces **recommendations** (what to do) and — where approved — **suggested
actions** (do it for you), all logged with priority and timestamps.

### Core Capabilities

| Feature | Description |
|---------|-------------|
| **Embedded dashboard** | The AI dashboard renders inside Dolibarr (Smart Assistant → Dashboard) |
| **Continuous monitoring** | Agents run on a configurable schedule (weekday business hours by default) |
| **Prioritized recommendations** | Every recommendation has a priority and rationale |
| **Human-in-the-loop actions** | Suggested actions require approval before execution |
| **Org memory** | Agents remember decisions and context across runs |
| **Zero configuration** | Connect once with a read-only API key; agents start working |

---

## 4. Architecture (30-second version)

```
Customer Dolibarr ──────────► Smart Assistant hosted service
    (module: thin GPL)         (agents · AI · scheduler · dashboard)
        │  read-only API  ───────────►  Google Gemini (LLM)
        └  dashboard embed ◄───────────  HTTPS (instance token)
```

- The **module is deliberately thin** (open source, GPLv3): it connects the instance and
  embeds the dashboard. All intelligence runs server-side on the hosted service.
- The hosted service accesses the customer's Dolibarr **read-only** via the REST API with
  a per-instance token and a read-only API key.
- See [`overview.md`](./overview.md) for the full architecture.

---

## 5. Target Users

- **Small and mid-sized businesses** running Dolibarr (18+) who want operations oversight
  without hiring analysts.
- **Dolibarr integrators/consultants** who want to offer monitoring as a value-add.
- **Busy owners/managers** who need a daily "what should I look at today" briefing.

**Not for:** large enterprises requiring on-premise AI processing (a Docker/local edition
is on the roadmap), or users unwilling to share business data with a hosted service
(see [`data-privacy.md`](./data-privacy.md)).

---

## 6. Pricing & Licensing

| Component | License | Price |
|-----------|---------|-------|
| **Dolibarr module** (this package) | GPLv3 or later | **Free** (DoliStore) |
| **Hosted service** (agents + dashboard) | Proprietary (SaaS) | Subscription; **free during beta** |

The module is fully open source; the hosted service is a separate product. The module
works without the service installed, but the dashboard requires an active connection.

---

## 7. Requirements

- **Dolibarr 18+** (developed and tested against 23.x)
- **PHP 7.0+** (PHP 8 recommended)
- **Dolibarr REST API enabled** with a **read-only API key** (created in Setup → API / user settings)
- **HTTPS** on both the Dolibarr site and the hosted service
- Module ID **194000** (reserved range 194000–194019, claimed on the Dolibarr wiki)

---

## 8. Getting Started

1. Install the module ZIP (Setup → Modules → Install external module)
2. Enable **Smart Assistant** in Setup → Modules/Applications
3. Open **Smart Assistant → Setup**, enter the Dolibarr URL + read-only API key, click **Connect**
4. Open **Smart Assistant → Dashboard** — agents begin their first analysis

Full walkthrough: [`tutorial.md`](./tutorial.md)

---

## 9. Roadmap

- Short-lived session tokens (no token in the iframe URL)
- Event-driven agent runs (`core/triggers/` — instant reaction to invoice validation, etc.)
- Self-service instance deletion & data export
- Docker/local edition (bring-your-own LLM key)
- More languages

---

## 10. Contact & Support

- **Publisher:** Integmia
- **Website:** https://dolibarr.smartassistant.site
- **Source code (GPLv3):** https://github.com/mkeshmirian/smartassistant-dolibarr
- **Email:** *(to be confirmed)*

---

*© 2026 Integmia — Smart Assistant for Dolibarr*
