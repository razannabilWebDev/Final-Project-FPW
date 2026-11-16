# AGENTS.md

SmartFood Home — Laravel web app for UMKM home-food businesses (sales, menu/recipes, stock, purchases, expenses, profit reports, production forecasts).

## Spec first — the docs are the source of truth

- `prd.md` (in Indonesian) is the product spec and **single source of truth**: role/permission matrix (§3.2), business rules (§6), baseline schema (§7), agent rules (§14). Never invent permissions or business rules that contradict it; open questions live in PRD §15 — don't silently resolve them.
- `TASK_INSTRUCTIONS.md` assigns modules to 4 team members, defines integration contracts and git rules. Work only on the module the user says they own; coordinate before touching other members' modules.
- Conflict priority (per TASK_INSTRUCTIONS §1): latest documented team decision > `prd.md` > `TASK_INSTRUCTIONS.md` > this file.
- Don't unilaterally change product requirements, the permission matrix, or shared config (`composer.json`, `package.json`, `.env`, shared migrations/routes/layouts).

## Current state

- Fresh Laravel 13 skeleton (PHP ^8.3, Blade + Tailwind CSS 4 + Vite 8). Only default `users`/`cache`/`jobs` migrations and a welcome route exist — all PRD modules are unbuilt.
- Build order per PRD §11: auth + roles/permissions → menus + ingredients → recipes/HPP → sales + stock deduction → purchases/verification → expenses + reports → production forecast. Don't build phases out of order or all at once.
- Stack is fixed (PRD §9.1): Laravel monolith, Blade, Tailwind, session auth, Laravel Storage. No stack swaps, no paid/external integrations or AI services without approval.

## Commands

- `composer setup` — install deps, create `.env`, key, migrate, npm install + build.
- `composer dev` — `php artisan dev` (Laravel 13 combined dev server: app + queue + logs + Vite).
- `composer test` — config:clear + `php artisan test`. Single test: `php artisan test tests/Feature/ExampleTest.php` or `php artisan test --filter=Name`.
- Style: `./vendor/bin/pint` (auto-fixer, Laravel preset; no `pint.json`). Tests are PHPUnit, not Pest.

## Environment quirks

- Local dev and tests run on **SQLite** (`DB_CONNECTION=sqlite`; tests use `:memory:` via `phpunit.xml`), while PRD §9.1 targets **MySQL**. Confirm with the team before switching drivers or relying on MySQL-only SQL.
- `.npmrc` sets `ignore-scripts=true`; `composer setup` accounts for this.

## Enforced conventions (differ from naive Laravel defaults)

- Business logic in Services/Actions — never in controllers or Blade. Validation in Form Requests. Authorization (policies/gates/middleware) enforced **in the backend**, never UI-only; denied requests get 403.
- Every stock change goes through a central stock service and writes a `stock_movements` ledger row (`balance_before`/`balance_after`). Never update stock columns directly. Sale-driven stock deduction is idempotent, once, inside a DB transaction, at the agreed transaction status.
- Snapshot menu name, selling price, and HPP onto sale items at transaction time; later recipe/price edits must never rewrite history.
- No hard deletes of referenced data — use `is_active`/soft delete (menus, users, expense categories).
- Money and fractional quantities are `decimal`, never float. Timezone `Asia/Jakarta`.
- Manual (non-menu) sales have no items, never deduct stock, and reports must mark their HPP as *unavailable*, not zero.
- UI text in **Bahasa Indonesia**; money formatted as Rupiah (`Rp15.000`).
- A feature isn't done without tests for its business processes (PRD §13: subtotal/HPP/profit math, stock non-negativity, deduction, reversal, purchase verification).

## Git

- Conventional commits (`feat(menu): ...`, `fix(report): ...`, `test(purchase): ...`), feature branches off `main`, no direct commits to `main` (TASK_INSTRUCTIONS §6).

## Prompt log

- Append every new user prompt to `prompt_history.md` (timestamp WIB, AI model, full text, result). Recover past prompts from the local OpenCode session DB when asked; entries from other team members' machines are added manually via the template in that file.

<laravel-boost-guidelines>
Laravel 13 application. Verify `php -v` and `composer -V` work before doing anything else. If missing, install via https://php.new for the detected OS and ask the user to restart their terminal. Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Laravel Boost is not yet installed. If `laravel/boost` is absent from `composer.json`, run `composer require laravel/boost --dev` and `php artisan boost:install` before making application changes. After install, re-read this file — Boost replaces the text between these tags with tailored guidelines.
</laravel-boost-guidelines>

## graphify

This project has a knowledge graph at graphify-out/ with god nodes, community structure, and cross-file relationships.

When the user types `/graphify`, use the installed graphify skill or instructions before doing anything else.

Rules:
- When graphify-out/graph.json exists, run `graphify query "<question>"` before your first grep, search or source-file read for a codebase question, and put this rule in every subagent prompt that explores code. Read raw files only for lines the graph or an earlier read already located. Use `graphify path "<A>" "<B>"` for relationships and `graphify explain "<concept>"` for focused concepts. When the answer is under a Markdown heading, `graphify section "<heading>"` prints that section's text, so the .md need not be opened. These return a scoped subgraph, usually much smaller than GRAPH_REPORT.md or raw grep output.
- Dirty graphify-out/ files are expected after hooks or incremental updates; dirty graph files are not a reason to skip graphify. Only skip graphify if the task is about stale or incorrect graph output, or the user explicitly says not to use it.
- If graphify-out/wiki/index.md exists, use it for broad navigation instead of raw source browsing.
- Read graphify-out/GRAPH_REPORT.md only for broad architecture review or when query/path/explain do not surface enough context.
- After modifying code, run `graphify update .` to keep the graph current (AST-only, no API cost).
- A committed graphify-out/graph.json is rewritten whole on every build, so a large diff to it in a pull request is expected. Do not flag it as churn or suggest skipping the graph rebuild or its hooks.
