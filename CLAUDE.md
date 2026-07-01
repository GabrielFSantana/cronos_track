# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this project is

TimeTracker (provisional name — candidates: TimeFlow, BiuTrack, PulseTime): a personal, single-user time-tracking system. It automatically logs active-window usage on Windows, browser tab/URL activity, and also supports manual time entry. All data flows into one dashboard for reporting (time per app/domain/project/day).

This is explicitly **not** multi-tenant: no multi-user auth, roles, or permissions should be implemented. Mobile support and multi-machine sync are out of scope for v1. Full setup/run instructions and current status are in `readme.md` — read it first.

## Architecture

Three independent data sources feed a single PHP API, which writes to MySQL and serves the dashboard:

```
Windows Agent (Python)  ─┐
Browser Extension (MV3) ─┼─► API REST (PHP + CodeIgniter 4, in api/) ─► MySQL ─► Dashboard (CodeIgniter views + Chart.js)
Manual entry (web form) ─┘
```

The Windows agent and the browser extension do **not** talk to each other — each sends data directly to the API independently.

Folder structure:
```
timetracker/
├── agent/                       # Python agent (Windows-only)
│   ├── main.py                  # Tracker class: session lifecycle, idle detection, batch flush, tray icon
│   ├── idle_detector.py         # GetLastInputInfo via ctypes
│   ├── window_tracker.py        # active window (app + title) via pywin32/psutil
│   └── requirements.txt / .env.example
├── extension/                   # Browser extension (Manifest V3)
│   ├── background.js            # service worker: session per tab/domain, chrome.alarms-based flush
│   └── popup.html / popup.js    # config UI (API URL, API key, full-URL toggle) → chrome.storage.local
├── api/                         # CodeIgniter 4 app (backend + dashboard, same codebase)
│   ├── app/Controllers/Api/     # ActivitiesController, ManualController, ReportController, ProjetosController
│   ├── app/Controllers/         # DashboardController, ManualEntryController (web pages, not JSON)
│   ├── app/Models/               # ActivityModel, ProjetoModel (validation rules live here)
│   ├── app/Services/ReportService.php  # aggregation logic shared by Api\ReportController and DashboardController
│   ├── app/Views/                 # dashboard.php, manual_entry.php, partials/nav.php (Tailwind CDN, dark theme)
│   └── app/Config/Routes.php, Filters.php, Security.php
└── docs/schema.sql
```

## Commands

All commands below assume you're in the relevant subdirectory.

**API (`api/`)**
- `composer install` — install CodeIgniter 4 and dependencies (run once, or after `composer.json` changes)
- `php spark serve` — start the dev server (default `http://localhost:8080`)
- `cp env .env` — create local env config (see `readme.md` for which values to fill in)
- `vendor/bin/phpunit` — run tests (only CodeIgniter's default scaffold tests exist under `tests/`, e.g. `tests/unit/HealthTest.php`; no project-specific tests written yet)
- `vendor/bin/phpunit tests/unit/HealthTest.php` — run a single test file; add `--filter testMethodName` to run one test method

**Agent (`agent/`)** — Windows only
- `python -m venv .venv && .venv\Scripts\pip install -r requirements.txt` — setup
- `.venv\Scripts\python main.py` — run (creates a system tray icon)

**Extension (`extension/`)**
- No build step. Load unpacked via `chrome://extensions` → Developer mode → "Load unpacked" → select `extension/`.
- Config (API URL, API key, full-URL capture) is set through the extension's popup UI, not a file.

There's no root-level build/test command — each of the three components (`api/`, `agent/`, `extension/`) is independent and has its own toolchain.

## Key business rules

- **Session aggregation**: never write one row per poll. The agent/extension only sends a record when the active app/tab changes (marking the end of the previous session and start of the new one). This logic lives in `agent/main.py`'s `Tracker` class and `extension/background.js`'s `handleTab`/`closeCurrentSession` — both follow the same pattern independently.
- **Idle detection**: if there's no mouse/keyboard input for a configurable period (default 5 min), automatically pause tracking (agent only; the extension has no idle detection, it pauses based on browser window focus instead).
- **Domain vs full URL**: by default the extension records only the domain (e.g. `github.com`). Full URL capture is optional/configurable via the popup, to avoid it becoming a full browsing history.
- **Duration is always computed**, never recalculated in report queries — it's a generated column (`duracao_seg`) via `TIMESTAMPDIFF(SECOND, inicio, fim)`. Reports use `SUM(duracao_seg)`, never `TIMESTAMPDIFF` again (see `app/Services/ReportService.php`).
- **Two different auth mechanisms in the same CodeIgniter app**: JSON endpoints under `/api/*` require the `X-API-Key` header (checked by the `apikey` filter, `app/Filters/ApiKeyFilter.php`). Browser-facing form routes (`/`, `/lancamento`) use CodeIgniter's built-in CSRF filter instead — don't mix the two up when adding routes.

## Database schema

See `docs/schema.sql`. Core tables: `projetos` (id, nome, cor) and `activities` (id, origem enum('app','navegador','manual'), app_ou_dominio, titulo, projeto_id FK, inicio, fim, duracao_seg generated, criado_em). Indexes on `inicio` and `(projeto_id, inicio)` for report filtering — any new report query filter must be covered by an index.

## Code conventions

- PHP: PSR-12, following the conventions already used in other projects (PRORADIS/TELERADIS).
- Commits in Portuguese, format `tipo: descrição` (e.g. `feat: adiciona endpoint de relatório`).
- No business logic in Controllers — delegate to Models (validation rules) or Services (`app/Services/`) when logic is shared across controllers.
- Before assuming `pywin32` APIs are available in the Python agent, validate that the package is installed.
- When writing SQL, always consider indexes for report filters (`inicio`, `projeto_id`).

## Known gaps (see readme.md's "Status atual e próximos passos" for the full list)

No automated tests, no CI, no dedicated UI for managing `projetos` (API only), agent has no auto-start on Windows boot, frontend uses Tailwind via CDN with no build pipeline.
