# Data Privacy & Security — Smart Assistant for Dolibarr

**Document version:** 1.0 · **Last updated:** 2026-08-05 · **Publisher:** Integmia · **Target platform:** DoliStore

---

## 1. Overview

Smart Assistant for Dolibarr embeds an AI operations dashboard into Dolibarr. The module
itself is a thin, open-source (GPLv3) connector; the intelligence (monitoring agents,
LLM analysis, scheduling, recommendations) runs on the **Smart Assistant hosted service**
at `https://dolibarr.smartassistant.site`. This document describes exactly what data is
collected, transferred, stored and processed, and how it is protected.

---

## 2. What the Module Sends

The module is deliberately **thin** and only ever talks to the hosted service:

| Data | When | Why |
|------|------|-----|
| Dolibarr URL + **read-only API key** | Once, at **Connect** (Setup page) | Register the instance; the service verifies the key and issues an instance token |
| Instance token | Every dashboard view (in the embed URL) | Authenticate the dashboard request |
| Module version, Dolibarr version, entity | Once, at Connect | Operational metadata |

The module **never** sends customer records, contacts, invoices or other business data
itself — data flows from Dolibarr directly to the hosted service only via the agents'
API reads (§3).

---

## 3. What the Hosted Service Reads

On a configurable schedule (weekday business hours by default), five agents query the
customer's Dolibarr via its REST API v2, using the stored read-only API key:

- **Revenue & Sales Ops:** customers/prospects, opportunities, quotes, orders
- **Customer Success:** customers, contracts, tickets, invoices
- **Procurement & Inventory:** suppliers, supplier orders, products, stock movements
- **Operations Orchestration:** orders, invoices, tickets, projects, contracts
- **Business Intelligence:** cross-module aggregates, trends

All access is **read-only**; the service never creates, updates or deletes data in the
customer's Dolibarr without explicit human approval of a suggested action (and actions
are executed through the same API under the customer's read/write key only if the
customer configures one — the default integration uses a **read-only key**).

---

## 4. What Is Stored by the Hosted Service

| Data | Stored? | Details |
|------|---------|---------|
| Instance registration (Dolibarr URL, API key, token, versions, timestamps) | ✅ | Required to serve the dashboard and run agents; held in the service's database |
| Agent results (recommendations, suggested actions, org memory) | ✅ | The product's core output — shown on the dashboard; derived from business data |
| Raw business records (contacts, invoices, products…) | ❌ | Read live per run; only compact derived context is used for analysis; raw records are not persisted |
| LLM prompts/responses | ❌ | Sent transiently to the LLM provider for analysis (§6); not stored by the service |
| Access logs | ✅ | Method, path, status, duration, IP, request ID — **no payloads, tokens or API keys** |

**Retention:** instance registrations and agent results are kept while the account is
active. Disconnecting in the module removes the token from Dolibarr and stops access;
to delete the instance record on the service, contact the publisher (self-service
deletion is on the roadmap). Logs rotate (≈5 MB per file) and old archives are pruned.

---

## 5. Authentication & Token Handling

- **Instance token:** random 48-hex-character token issued per registration; it scopes
  every API call and dashboard view to that instance. Re-connecting rotates the token
  (old tokens stop working immediately).
- **Dolibarr API key:** stored to allow the agents to read data. A **read-only key** is
  recommended and sufficient. It is never exposed to browsers or third parties and is
  not written to logs.
- **Register endpoint:** rate-limited (10 attempts/minute/IP) to prevent abuse.
- The module stores its token in Dolibarr's `llx_const` table (Dolibarr's own database).

---

## 6. Third-Party Services

### 6.1 Google Gemini (LLM provider)

- Agents send a **compact summary** of relevant records (not full records) to Google's
  Gemini API for analysis.
- Per Google's API terms, prompts and responses are **not used to train models** when
  accessed via the API.
- The LLM key is held in Google Secret Manager; the customer needs no LLM account.

### 6.2 Google Cloud (hosting)

- The service runs on Google Cloud Run (region `us-central1`); storage is Google-managed.
- No other third parties are involved: no analytics SDKs, no advertising, no tracking.

---

## 7. GDPR & User Rights

**Lawful basis:** processing is performed on behalf of the organization that operates
the Dolibarr instance (contract/legitimate interest): the service analyzes the
organization's own business data to produce recommendations.

| Right | How it is handled |
|-------|-------------------|
| **Access** | The dashboard shows everything derived from your data; instance data can be exported on request |
| **Rectification** | Data originates from your Dolibarr — correct it there; agents pick up changes on the next run |
| **Erasure** | Disconnect in the module, then request deletion of the instance record (contact publisher) |
| **Data portability** | Your data lives in Dolibarr — use Dolibarr's export features |
| **Restriction** | Disable agents/schedules (server-side) or disconnect the module |
| **Objection** | Stop using the module; no further processing occurs after disconnect |

**Controller/Processor note:** the customer organization remains the controller of its
Dolibarr data; Integmia acts as processor for the purpose of providing the service.

---

## 8. Security Measures

| Area | Measure |
|------|---------|
| **Transport** | TLS 1.2+ on all connections (browser↔service, service↔Dolibarr, service↔LLM) |
| **Authentication** | Per-instance bearer tokens; rate-limited registration; no passwords collected |
| **Secrets** | LLM key and defaults in Google Secret Manager; instance API keys not logged |
| **Input validation** | All API inputs validated; server rejects malformed requests |
| **Logging** | Request IDs for audit; payloads, tokens and keys never logged |
| **Hosting** | Managed serverless platform (Cloud Run) with container isolation |
| **Module** | Open source (GPLv3) — the full module code is public for review |
| **Errors** | Graceful error handling; no stack traces or secrets leaked to clients |

---

## 9. Data Residency

- The hosted service and its state store run in Google Cloud **us-central1 (US)**.
- Customer data stays in the customer's own Dolibarr (wherever they host it); only the
  derived analysis described above transits to the service.
- *(A European region for the hosted service is planned.)*

---

## 10. Changes to This Document

Material changes will be announced via the DoliStore listing and the module repository.

---

## 11. Contact

- **Publisher:** Integmia
- **Website:** https://dolibarr.smartassistant.site
- **Email:** *(to be confirmed)*

---

*© 2026 Integmia — Smart Assistant for Dolibarr*
