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

## Tasklist

- Every session that does work must append a checklist block to `tasklist.md` (newest first): timestamp WIB, member name, session title, then checkboxes `[x]` done / `[ ]` pending / `[~]` partial, and the commit hash if any. Keep it a per-session work log; phase status lives in `docs/implementation-plan.md` — do not duplicate it here.

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.5. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

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
