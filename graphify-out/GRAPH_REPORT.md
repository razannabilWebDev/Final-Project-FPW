# Graph Report - SmartfoodHome  (2026-11-23)

## Corpus Check
- 144 files · ~82,054 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 21 file(s) not represented in the graph (top: (none) 17, .example 1, .xml 1)

## Summary
- 1252 nodes · 1388 edges · 104 communities (85 shown, 19 thin omitted)
- Extraction: 96% EXTRACTED · 4% INFERRED · 0% AMBIGUOUS · INFERRED: 50 edges (avg confidence: 0.94)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `2ca2bb37`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- composer.json
- [2026-11-23 11:00] — Anggota 4 — Modul Pencatatan Penjualan (fase 1)
- Design System --- SmartFood
- SmartFood Home --- Pembagian Tugas Tim 4 Anggota
- AGENTS.md
- Cloud CLI
- Cloud CLI
- Tasklist — SmartFood Home
- Design System v2 — SmartFood Home
- Laravel Boost Guidelines
- package.json
- PaymentMethod
- SaleController
- Database performance best practices
- bootstrap/app.php
- Riwayat Prompt — SmartFood Home
- Sale
- User
- SaleStatus
- Illuminate\Database\Schema\Blueprint
- Database Contract — SmartFood Home
- Design System — SmartFood Home
- Detection checklist
- Process
- Implementation Plan — SmartFood Home
- create.blade.php
- Security best practices
- Architecture best practices
- Security best practices
- Tailwind CSS development
- index.blade.php
- Tailwind CSS development
- 5. Kebutuhan Fungsional
- show.blade.php
- Advanced query best practices
- Events and notifications best practices
- Migration best practices
- Queue and job best practices
- Advanced query best practices
- Events and notifications best practices
- Queue and job best practices
- Architecture Contract — SmartFood Home
- 4. Alur Pengguna Utama
- 6. Aturan Bisnis
- Caching best practices
- Caching best practices
- Migration best practices
- SmartFood Home
- .agents/skills/laravel-best-practices/SKILL.md
- Blade and view best practices
- Eloquent best practices
- Error handling best practices
- Task scheduling best practices
- .agents/skills/testing-best-practices/SKILL.md
- [2026-11-23 11:00 WIB] — Anggota 4
- .claude/skills/laravel-best-practices/SKILL.md
- Blade and view best practices
- Eloquent best practices
- Error handling best practices
- Task scheduling best practices
- .claude/skills/testing-best-practices/SKILL.md
- ExampleTest
- Collection best practices
- HTTP client best practices
- Mail best practices
- Routing and controller best practices
- Validation and forms best practices
- SalesTest.php
- Process
- Collection best practices
- HTTP client best practices
- Mail best practices
- Routing and controller best practices
- Validation and forms best practices
- Permission Contract — SmartFood Home
- laravel-boost
- 11. Roadmap Pengembangan
- 3.1 Definisi Role
- Configuration best practices
- Configuration best practices
- 12. Acceptance Criteria
- SmartFood Home
- 14. Instruksi Implementasi untuk OpenCode
- 1. Ringkasan Produk
- Decision Log — SmartFood Home
- 13. Strategi Pengujian
- 7. Kebutuhan Data dan Rancangan Database Awal
- 8. Kebutuhan UI/UX
- 9. Teknologi dan Arsitektur
- laravel-boost

## God Nodes (most connected - your core abstractions)
1. `Sale` - 20 edges
2. `SaleStatus` - 18 edges
3. `User` - 18 edges
4. `SmartFood Home` - 17 edges
5. `Design System --- SmartFood` - 16 edges
6. `[2026-11-23 11:00] — Anggota 4 — Modul Pencatatan Penjualan (fase 1)` - 16 edges
7. `Riwayat Prompt — SmartFood Home` - 14 edges
8. `SaleController` - 13 edges
9. `SalesTest` - 13 edges
10. `TestCase` - 13 edges

## Surprising Connections (you probably didn't know these)
- `Anggota 3` --references--> `Sale`  [INFERRED]
  docs/database.md → app/Models/Sale.php
- `Names and structure` --references--> `TestCase`  [INFERRED]
  .agents/skills/testing-best-practices/rules/review.md → tests/TestCase.php
- `Test class and methods` --references--> `TestCase`  [INFERRED]
  .claude/skills/testing-best-practices/rules/naming.md → tests/TestCase.php
- `[2026-11-23 11:00 WIB] — Anggota 4` --references--> `PaymentMethod`  [INFERRED]
  prompt_history.md → app/Enums/PaymentMethod.php
- `[2026-11-23 11:00] — Anggota 4 — Modul Pencatatan Penjualan (fase 1)` --references--> `PaymentMethod`  [INFERRED]
  tasklist.md → app/Enums/PaymentMethod.php

## Import Cycles
- None detected.

## Communities (104 total, 19 thin omitted)

### Community 0 - "composer.json"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 1 - "[2026-11-23 11:00] — Anggota 4 — Modul Pencatatan Penjualan (fase 1)"
Cohesion: 0.24
Nodes (6): RecipeCostService, StockService, {closure#1}(), {closure#2}(), SaleService, [2026-11-23 11:00] — Anggota 4 — Modul Pencatatan Penjualan (fase 1)

### Community 2 - "Design System --- SmartFood"
Cohesion: 0.05
Nodes (41): 10. Spacing, Radius, dan Elevation, 11. State yang Wajib Diimplementasikan, 12. Aksesibilitas dan Usability, 13. Aturan Implementasi Frontend, 14. Kriteria Penerimaan Visual, 15. Catatan Batas Referensi, 1. Ringkasan Produk, 2. Arah Visual (+33 more)

### Community 3 - "SmartFood Home --- Pembagian Tugas Tim 4 Anggota"
Cohesion: 0.05
Nodes (39): 10. Checklist Progres Tim, 11. Keputusan yang Wajib Dicatat Sebelum Integrasi, 12. Prinsip Kolaborasi, 1. Tujuan Dokumen, 2. Pembagian Tugas Resmi, 3.1 Anggota 1 --- Autentikasi, Kelola Pengguna, Rekomendasi Produksi, 3.2 Anggota 2 --- Pengelolaan Menu dan Pengelolaan Resep, 3.3 Anggota 3 --- Pengelolaan Stok dan Pembelian Bahan Baku (+31 more)

### Community 4 - "AGENTS.md"
Cohesion: 0.06
Nodes (34): APIs & Eloquent Resources, Application Structure & Architecture, Artisan, Commands, Conventions, Current state, Deployment, Do Things the Laravel Way (+26 more)

### Community 5 - "Cloud CLI"
Cohesion: 0.06
Nodes (32): Adding a cache to an existing environment, Adding a database to an existing environment, Checklists for Multi-Step Operations, Custom domain setup, Full environment setup (app + database + cache + domain), New app from scratch, Sending email with Resend, Application Setup (+24 more)

### Community 6 - "Cloud CLI"
Cohesion: 0.06
Nodes (32): Adding a cache to an existing environment, Adding a database to an existing environment, Checklists for Multi-Step Operations, Custom domain setup, Full environment setup (app + database + cache + domain), New app from scratch, Sending email with Resend, Application Setup (+24 more)

### Community 7 - "Tasklist — SmartFood Home"
Cohesion: 0.18
Nodes (10): [2026-11-16 16:59] — Anggota 4 — Prompt log, [2026-11-16 17:03] — Anggota 4 — Graphify (skill proyek), [2026-11-16 18:05] — Anggota 4 — Identitas Anggota 4, [2026-11-16 18:33] — Anggota 4 — Design system dari Figma, [2026-11-16 19:05] — Anggota 4 — Fondasi tooling + dokumen kontrak, [2026-11-16 19:35] — Anggota 4 — Onboarding tim: README + panduan docx, [2026-11-16 19:42] — Anggota 4 — Tasklist konvensi, [2026-11-16 sebelumnya] — Anggota 4 — Dokumen tim pertama kali (+2 more)

### Community 8 - "Design System v2 — SmartFood Home"
Cohesion: 0.07
Nodes (29): 10. Kriteria penerimaan visual, 11. Riwayat, 1. Prinsip desain, 2.1 Warna inti, 2.2 Warna semantik (tonal: teks di atas latar terangnya), 2.3 Warna grafik (tambahan dari Figma), 2.4 Tipografi, 2.5 Radius, shadow, focus (+21 more)

### Community 9 - "Laravel Boost Guidelines"
Cohesion: 0.08
Nodes (25): APIs & Eloquent Resources, Application Structure & Architecture, Artisan, Conventions, Deployment, Do Things the Laravel Way, Documentation Files, Foundational Context (+17 more)

### Community 10 - "package.json"
Cohesion: 0.13
Nodes (20): devDependencies, concurrently@^10.0.3, laravel-vite-plugin@^3.1, tailwindcss@^4.0.0, @tailwindcss/vite@^4.0.0, vite@^8.0.0, optionalDependencies, @laravel/multiplex@^0.4.1 (+12 more)

### Community 11 - "PaymentMethod"
Cohesion: 0.17
Nodes (5): PaymentMethod, Cash, Qris, Transfer, SaleFactory

### Community 12 - "SaleController"
Cohesion: 0.15
Nodes (5): Controller, SaleController, StoreSaleRequest, UpdateSaleStatusRequest, 3. Konvensi Penamaan (usulan — final di Tahap 1)

### Community 13 - "Database performance best practices"
Cohesion: 0.11
Nodes (16): Add indexes for measured query patterns, Count relationships without loading them, Database performance best practices, Eager load relationships before iterating, Keep queries out of Blade templates, Prevent lazy loading in development, Process large data sets incrementally, Select only the columns you need (+8 more)

### Community 14 - "bootstrap/app.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 15 - "Riwayat Prompt — SmartFood Home"
Cohesion: 0.14
Nodes (13): [2026-11-16 16:40 WIB] — Anggota 4, [2026-11-16 16:46 WIB] — Anggota 4, [2026-11-16 16:48 WIB] — Anggota 4, [2026-11-16 16:59 WIB] — Anggota 4, [2026-11-16 17:03 WIB] — Anggota 4, [2026-11-16 18:05 WIB] — Anggota 4, [2026-11-16 18:33 WIB] — Anggota 4, [2026-11-16 19:05 WIB] — Anggota 4 (+5 more)

### Community 16 - "Sale"
Cohesion: 0.19
Nodes (3): Sale, SaleItem, SaleStatusHistory

### Community 17 - "User"
Cohesion: 0.05
Nodes (16): Convention and style best practices, Follow project naming conventions, Keep presentation code maintainable, Prefer clear, idiomatic syntax, Use utilities when they clarify intent, Write comments that explain why, User, Convention and style best practices (+8 more)

### Community 18 - "SaleStatus"
Cohesion: 0.20
Nodes (5): SaleStatus, Cancelled, Completed, Pending, Processing

### Community 19 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.12
Nodes (12): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}() (+4 more)

### Community 20 - "Database Contract — SmartFood Home"
Cohesion: 0.17
Nodes (11): 1. Kepemilikan Schema (TASK_INSTRUCTIONS §5.1), 2. Baseline Tabel (PRD §7.1), 3. Relasi Utama (PRD §7.2), 4. Aturan Integritas (PRD §7.3) — non-negotiable, 5. Keputusan Terbuka (harus diputuskan tim sebelum migration pertama), Anggota 1, Anggota 2, Anggota 3 (+3 more)

### Community 21 - "Design System — SmartFood Home"
Cohesion: 0.15
Nodes (12): 1.1 Warna inti, 1.2 Warna semantik (pewarnaan "tonal": warna teks di atas latar terangnya), 1.3 Tipografi, 1.4 Radius, shadow, focus, 1.5 Pemetaan ke Tailwind 4 (`resources/css/app.css`), 1. Design Tokens, 2. Status → Warna Badge, 3. Komponen Inti (+4 more)

### Community 22 - "Detection checklist"
Cohesion: 0.08
Nodes (22): A. Validation & HTTP input, B. Controllers & routing, C. Authorization, D. Eloquent & models, Detection checklist, E. Architecture & organization, F. Frontend & views, G. Database & migrations (+14 more)

### Community 23 - "Process"
Cohesion: 0.17
Nodes (11): Edge cases, Glob mapping, Ground rules (read before you start), Infer conventions, Process, Step 0: Orient, Step 1: Predefined sweep, Step 2: Open-ended pass (+3 more)

### Community 24 - "Implementation Plan — SmartFood Home"
Cohesion: 0.22
Nodes (8): Implementation Plan — SmartFood Home, Keputusan yang Menahan Integrasi, Tahap 0 — Fondasi Repo & Tooling, Tahap 1 — Fondasi Bersama (TASK_INSTRUCTIONS §8), Tahap 2 — Pengembangan Paralel (boleh mulai setelah kontrak disepakati), Tahap 3 — Integrasi (TASK_INSTRUCTIONS §8), Tahap 4 — Pengujian Sistem, Tahap 5 — Stabilitas & Dokumentasi

### Community 26 - "Security best practices"
Cohesion: 0.18
Nodes (11): Apply cross-site request forgery protection, Audit dependencies, Authorize protected actions, Bind query parameters, Control mass assignment, Encrypt sensitive attributes when appropriate, Escape output in its context, Keep secrets out of application code (+3 more)

### Community 27 - "Architecture best practices"
Cohesion: 0.07
Nodes (23): Architecture best practices, Depend on contracts at boundaries, Extract focused business operations, Follow framework conventions, Inject required dependencies, Specify a deterministic sort order, Use atomic locks for race conditions, Use `Concurrency::run()` for parallel execution (+15 more)

### Community 28 - "Security best practices"
Cohesion: 0.17
Nodes (11): Apply cross-site request forgery protection, Audit dependencies, Authorize protected actions, Bind query parameters, Control mass assignment, Encrypt sensitive attributes when appropriate, Escape output in its context, Keep secrets out of application code (+3 more)

### Community 29 - "Tailwind CSS development"
Cohesion: 0.18
Nodes (10): Basic usage, Common pitfalls, CSS-first configuration, Dark mode, Documentation, Import syntax, Replaced utilities, Spacing (+2 more)

### Community 31 - "Tailwind CSS development"
Cohesion: 0.18
Nodes (10): Basic usage, Common pitfalls, CSS-first configuration, Dark mode, Documentation, Import syntax, Replaced utilities, Spacing (+2 more)

### Community 32 - "5. Kebutuhan Fungsional"
Cohesion: 0.18
Nodes (11): 5.10 Rekomendasi Produksi, 5.1 Autentikasi, 5.2 Kelola Pengguna, 5.3 Pengelolaan Menu, 5.4 Pengelolaan Resep, 5.5 Pengelolaan Stok Bahan Baku, 5.6 Pembelian Bahan Baku, 5.7 Pencatatan Penjualan (+3 more)

### Community 34 - "Advanced query best practices"
Cohesion: 0.20
Nodes (9): Advanced query best practices, Combine related counts with conditional aggregates, Compare `whereHas()` with an `IN` subquery, Consider a correlated subquery for has-many ordering, Create dynamic relationships with a subquery foreign key, Design composite indexes for the query, Measure two simple queries against one complex query, Reuse loaded parent models with `setRelation()` (+1 more)

### Community 35 - "Events and notifications best practices"
Cohesion: 0.22
Nodes (9): Cache event discovery during production deployment, Dispatch queued notifications after commit, Events and notifications best practices, Implement `HasLocalePreference` on notifiable models, Queue slow notifications, Rely on event discovery, Route notification channels to dedicated queues, Use on-demand notifications for non-user recipients (+1 more)

### Community 36 - "Migration best practices"
Cohesion: 0.20
Nodes (9): Define foreign-key constraints deliberately, Design indexes for real queries, Generate migrations with Artisan, Keep migrations focused, Make rollbacks honest, Migration best practices, Mirror defaults only when unsaved models need them, Stage changes that affect existing rows (+1 more)

### Community 37 - "Queue and job best practices"
Cohesion: 0.22
Nodes (9): Back off transient failures, Batch jobs for group coordination, Configure time-based retry limits deliberately, Handle terminal failure when needed, Keep reservation time longer than execution time, Queue and job best practices, Rate limit external calls, Use Horizon for Redis queue operations (+1 more)

### Community 38 - "Advanced query best practices"
Cohesion: 0.20
Nodes (9): Advanced query best practices, Combine related counts with conditional aggregates, Compare `whereHas()` with an `IN` subquery, Consider a correlated subquery for has-many ordering, Create dynamic relationships with a subquery foreign key, Design composite indexes for the query, Measure two simple queries against one complex query, Reuse loaded parent models with `setRelation()` (+1 more)

### Community 39 - "Events and notifications best practices"
Cohesion: 0.20
Nodes (9): Cache event discovery during production deployment, Dispatch queued notifications after commit, Events and notifications best practices, Implement `HasLocalePreference` on notifiable models, Queue slow notifications, Rely on event discovery, Route notification channels to dedicated queues, Use on-demand notifications for non-user recipients (+1 more)

### Community 40 - "Queue and job best practices"
Cohesion: 0.22
Nodes (9): Back off transient failures, Batch jobs for group coordination, Configure time-based retry limits deliberately, Handle terminal failure when needed, Keep reservation time longer than execution time, Queue and job best practices, Rate limit external calls, Use Horizon for Redis queue operations (+1 more)

### Community 41 - "Architecture Contract — SmartFood Home"
Cohesion: 0.13
Nodes (10): InsufficientStockException, 1. Stack (PRD §9.1 — tetap, tidak boleh ditukar), 2. Struktur Kode — aturan wajib, 4. Pola Lintas Modul, 5. Penanganan Error, 6. Response & UI Pola, 7. Testing (PRD §13, PHPUnit), 8. Git & Kolaborasi (ringkas TASK_INSTRUCTIONS §6) (+2 more)

### Community 42 - "4. Alur Pengguna Utama"
Cohesion: 0.20
Nodes (10): 4.1 Alur Login, 4.2 Alur Penjualan Berbasis Menu, 4.3 Alur Penjualan Manual, 4.4 Alur Pembelian Bahan Baku, 4.5 Alur Pembaruan Stok Fisik, 4.6 Alur Stok Susut/Rusak, 4.7 Alur Pengelolaan Resep, 4.8 Alur Biaya Operasional (+2 more)

### Community 43 - "6. Aturan Bisnis"
Cohesion: 0.20
Nodes (10): 6.1 Penjualan dan Status Transaksi, 6.2 Metode Pembayaran, 6.3 Penjualan Manual, 6.4 HPP dan Resep, 6.5 Stok Bahan Baku, 6.6 Pembelian, 6.7 Keuntungan, 6.8 Kategori Pengeluaran (+2 more)

### Community 44 - "Caching best practices"
Cohesion: 0.22
Nodes (8): Caching best practices, Configure failover cache stores in production, Consider `Cache::flexible()` for stale-while-revalidate, Use `Cache::add()` for atomic conditional writes, Use `Cache::memo()` to avoid redundant hits within an execution, Use `Cache::remember()` for cache-aside reads, Use cache tags to invalidate related groups, Use `once()` for in-process memoization

### Community 46 - "Caching best practices"
Cohesion: 0.22
Nodes (8): Caching best practices, Configure failover cache stores in production, Consider `Cache::flexible()` for stale-while-revalidate, Use `Cache::add()` for atomic conditional writes, Use `Cache::memo()` to avoid redundant hits within an execution, Use `Cache::remember()` for cache-aside reads, Use cache tags to invalidate related groups, Use `once()` for in-process memoization

### Community 47 - "Migration best practices"
Cohesion: 0.20
Nodes (9): Define foreign-key constraints deliberately, Design indexes for real queries, Generate migrations with Artisan, Keep migrations focused, Make rollbacks honest, Migration best practices, Mirror defaults only when unsaved models need them, Stage changes that affect existing rows (+1 more)

### Community 49 - "SmartFood Home"
Cohesion: 0.22
Nodes (8): 10. Kebutuhan Non-Fungsional, 15. Asumsi dan Keputusan yang Masih Perlu Divalidasi, 16. Glosarium, 2.1 Dalam Ruang Lingkup, 2.2 Di Luar Ruang Lingkup Versi Awal, 2. Ruang Lingkup Produk, Product Requirements Document (PRD), SmartFood Home

### Community 50 - ".agents/skills/laravel-best-practices/SKILL.md"
Cohesion: 0.25
Nodes (5): Consistency first, Decision rules, How to apply, Laravel best practices, Rule index

### Community 51 - "Blade and view best practices"
Cohesion: 0.25
Nodes (7): Blade and view best practices, Prefer components for explicit interfaces, Return Blade fragments for partial rendering, Share compatible view data with a view composer, Share parent component props with `@aware`, Use `$attributes->merge()` in component templates, Use `@pushOnce` for per-component scripts

### Community 52 - "Eloquent best practices"
Cohesion: 0.22
Nodes (8): Apply global scopes sparingly, Cast date and time attributes, Define attribute casts, Define precise relationship types, Eloquent best practices, Keep application queries model-aware, Use local scopes for reusable queries, Use `whereBelongsTo()` for relationship queries

### Community 54 - "Error handling best practices"
Cohesion: 0.25
Nodes (7): Add context to exception classes, Choose where to report and render exceptions, Define JSON rendering for API routes, Error handling best practices, Mark exceptions the handler should not report, Prevent duplicate reports of one exception instance, Throttle high-volume exception reports

### Community 55 - "Task scheduling best practices"
Cohesion: 0.25
Nodes (7): Bound work inside the task, Group shared configuration, Prevent unwanted overlap, Restrict tasks by environment, Run a task on one server, Run eligible commands in the background, Task scheduling best practices

### Community 56 - ".agents/skills/testing-best-practices/SKILL.md"
Cohesion: 0.04
Nodes (38): Arrange, act, assert, Assert a known value, Assert the complete result, Assertions, How to find the correct assertion, Named response assertions, Endpoint coverage, Endpoint tests (+30 more)

### Community 58 - "[2026-11-23 11:00 WIB] — Anggota 4"
Cohesion: 0.17
Nodes (4): AppServiceProvider, DefaultRecipeCostService, DefaultStockService, [2026-11-23 11:00 WIB] — Anggota 4

### Community 60 - ".claude/skills/laravel-best-practices/SKILL.md"
Cohesion: 0.25
Nodes (5): Consistency first, Decision rules, How to apply, Laravel best practices, Rule index

### Community 61 - "Blade and view best practices"
Cohesion: 0.25
Nodes (7): Blade and view best practices, Prefer components for explicit interfaces, Return Blade fragments for partial rendering, Share compatible view data with a view composer, Share parent component props with `@aware`, Use `$attributes->merge()` in component templates, Use `@pushOnce` for per-component scripts

### Community 62 - "Eloquent best practices"
Cohesion: 0.22
Nodes (8): Apply global scopes sparingly, Cast date and time attributes, Define attribute casts, Define precise relationship types, Eloquent best practices, Keep application queries model-aware, Use local scopes for reusable queries, Use `whereBelongsTo()` for relationship queries

### Community 63 - "Error handling best practices"
Cohesion: 0.25
Nodes (7): Add context to exception classes, Choose where to report and render exceptions, Define JSON rendering for API routes, Error handling best practices, Mark exceptions the handler should not report, Prevent duplicate reports of one exception instance, Throttle high-volume exception reports

### Community 64 - "Task scheduling best practices"
Cohesion: 0.25
Nodes (7): Bound work inside the task, Group shared configuration, Prevent unwanted overlap, Restrict tasks by environment, Run a task on one server, Run eligible commands in the background, Task scheduling best practices

### Community 65 - ".claude/skills/testing-best-practices/SKILL.md"
Cohesion: 0.04
Nodes (37): Arrange, act, assert, Assert a known value, Assert the complete result, Assertions, How to find the correct assertion, Named response assertions, Endpoint coverage, Endpoint tests (+29 more)

### Community 68 - "Collection best practices"
Cohesion: 0.29
Nodes (6): Choose between `cursor()` and `lazy()`, Collection best practices, Use `#[CollectedBy]` for custom collection classes, Use higher-order messages for simple operations, Use `lazyById()` when updating records while iterating, Use `toQuery()` for bulk operations on collections

### Community 69 - "HTTP client best practices"
Cohesion: 0.29
Nodes (6): Fake HTTP requests in tests, Handle errors explicitly, HTTP client best practices, Pool independent requests, Retry only safe operations, Set explicit timeouts

### Community 70 - "Mail best practices"
Cohesion: 0.29
Nodes (6): Assert the delivery mode, Dispatch queued mail after commit, Mail best practices, Queue slow mail delivery, Separate content and delivery tests, Use Markdown mailables when they fit

### Community 71 - "Routing and controller best practices"
Cohesion: 0.29
Nodes (6): Keep controllers focused on HTTP concerns, Organize controllers around resources, Routing and controller best practices, Scope nested bindings, Use implicit route model binding, Use resource routes for resourceful actions

### Community 73 - "Validation and forms best practices"
Cohesion: 0.29
Nodes (6): Add cross-field validation after base rules, Express conditional rules clearly, Extract validation when it improves the boundary, Prefer readable rule syntax, Use only the validated data you intend, Validation and forms best practices

### Community 77 - "SalesTest.php"
Cohesion: 0.05
Nodes (26): File layout, Grouping, Naming and structure, Naming tests, Test class and methods, Common errors, Global fakes, How to find a slow test (+18 more)

### Community 81 - "Process"
Cohesion: 0.17
Nodes (11): Edge cases, Glob mapping, Ground rules (read before you start), Infer conventions, Process, Step 0: Orient, Step 1: Predefined sweep, Step 2: Open-ended pass (+3 more)

### Community 82 - "Collection best practices"
Cohesion: 0.29
Nodes (6): Choose between `cursor()` and `lazy()`, Collection best practices, Use `#[CollectedBy]` for custom collection classes, Use higher-order messages for simple operations, Use `lazyById()` when updating records while iterating, Use `toQuery()` for bulk operations on collections

### Community 83 - "HTTP client best practices"
Cohesion: 0.29
Nodes (6): Fake HTTP requests in tests, Handle errors explicitly, HTTP client best practices, Pool independent requests, Retry only safe operations, Set explicit timeouts

### Community 84 - "Mail best practices"
Cohesion: 0.29
Nodes (6): Assert the delivery mode, Dispatch queued mail after commit, Mail best practices, Queue slow mail delivery, Separate content and delivery tests, Use Markdown mailables when they fit

### Community 85 - "Routing and controller best practices"
Cohesion: 0.29
Nodes (6): Keep controllers focused on HTTP concerns, Organize controllers around resources, Routing and controller best practices, Scope nested bindings, Use implicit route model binding, Use resource routes for resourceful actions

### Community 87 - "Validation and forms best practices"
Cohesion: 0.29
Nodes (6): Add cross-field validation after base rules, Express conditional rules clearly, Extract validation when it improves the boundary, Prefer readable rule syntax, Use only the validated data you intend, Validation and forms best practices

### Community 93 - "Permission Contract — SmartFood Home"
Cohesion: 0.25
Nodes (7): 1. Sumber Kebenaran, 2. Mekanisme (usulan implementasi), 3. Usulan Middleware & Gate, 4. Matriks → Gate (dari PRD §3.2), 5. Aturan yang Mudah Terlewat (jebakan PRD §3.2), 6. Tugas Anggota 1 (tindak lanjut), Permission Contract — SmartFood Home

### Community 95 - "laravel-boost"
Cohesion: 0.29
Nodes (6): command, enabled, type, mcp, laravel-boost, $schema

### Community 96 - "11. Roadmap Pengembangan"
Cohesion: 0.29
Nodes (7): 11. Roadmap Pengembangan, Fase 1 --- Penjualan dan Laporan Dasar, Fase 2 --- Menu, Resep, dan Stok, Fase 3 --- Pengeluaran dan Pembelian, Fase 4 --- Akun dan Pengaturan, Fase 5 --- Rekomendasi Prediksi, Urutan Dependensi yang Disarankan

### Community 97 - "3.1 Definisi Role"
Cohesion: 0.29
Nodes (7): 3.1 Definisi Role, 3.2 Matriks Hak Akses, 3.3 Aturan Akun, 3. Pengguna, Role, dan Hak Akses, Admin (Owner), Pengelola, Staf Operasional

### Community 98 - "Configuration best practices"
Cohesion: 0.33
Nodes (5): Configuration best practices, Name repeated domain values, Protect production secrets, Read environment variables in configuration files, Use `App::environment()` for environment checks

### Community 101 - "Configuration best practices"
Cohesion: 0.33
Nodes (5): Configuration best practices, Name repeated domain values, Protect production secrets, Read environment variables in configuration files, Use `App::environment()` for environment checks

### Community 104 - "12. Acceptance Criteria"
Cohesion: 0.33
Nodes (6): 12.1 Autentikasi dan Permission, 12.2 Penjualan, 12.3 Resep dan Stok, 12.4 Pembelian dan Pengeluaran, 12.5 Laporan dan Prediksi, 12. Acceptance Criteria

### Community 105 - "SmartFood Home"
Cohesion: 0.33
Nodes (5): Git workflow (ringkas TASK_INSTRUCTIONS §6), Prompt awal untuk AI agent (tiap anggota), Setup (sekali per mesin), SmartFood Home, Yang harus tim lakukan SEKARANG (Tahap 0–1)

### Community 118 - "14. Instruksi Implementasi untuk OpenCode"
Cohesion: 0.40
Nodes (5): 14.1 Aturan Kerja Agent, 14.2 Tahapan Implementasi yang Disarankan, 14.3 Definition of Done, 14.4 Dokumen Pendamping yang Disarankan, 14. Instruksi Implementasi untuk OpenCode

### Community 119 - "1. Ringkasan Produk"
Cohesion: 0.40
Nodes (5): 1.1 Deskripsi, 1.2 Masalah yang Ingin Diselesaikan, 1.3 Tujuan Produk, 1.4 Indikator Keberhasilan, 1. Ringkasan Produk

### Community 121 - "Decision Log — SmartFood Home"
Cohesion: 0.50
Nodes (3): Catatan per keputusan (yang butuh koordinasi nyata), Decision Log — SmartFood Home, Tanda tangan keputusan

### Community 123 - "13. Strategi Pengujian"
Cohesion: 0.50
Nodes (4): 13.1 Unit Test, 13.2 Feature Test, 13.3 Skenario Integrasi Kritis, 13. Strategi Pengujian

### Community 124 - "7. Kebutuhan Data dan Rancangan Database Awal"
Cohesion: 0.50
Nodes (4): 7.1 Entitas Utama, 7.2 Relasi Utama, 7.3 Persyaratan Integritas Data, 7. Kebutuhan Data dan Rancangan Database Awal

### Community 125 - "8. Kebutuhan UI/UX"
Cohesion: 0.50
Nodes (4): 8.1 Prinsip Desain, 8.2 Halaman Minimum, 8.3 Dashboard per Role, 8. Kebutuhan UI/UX

### Community 126 - "9. Teknologi dan Arsitektur"
Cohesion: 0.50
Nodes (4): 9.1 Stack yang Ditetapkan, 9.2 Arsitektur, 9.3 Prinsip Keamanan, 9. Teknologi dan Arsitektur

## Knowledge Gaps
- **11 isolated node(s):** `php`, `Cash`, `Transfer`, `Qris`, `Pending` (+6 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 853 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **19 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `[2026-11-23 11:00] — Anggota 4 — Modul Pencatatan Penjualan (fase 1)`, `PaymentMethod`, `SalesTest.php`, `Sale`, `Architecture best practices`?**
  _High betweenness centrality (0.191) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `Sale` (e.g. with `3. Konvensi Penamaan (usulan — final di Tahap 1)` and `Anggota 3`) actually correct?**
  _`Sale` has 4 INFERRED edges - model-reasoned connections that need verification._
- **What connects `php`, `Cash`, `Transfer` to the rest of the system?**
  _11 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `composer.json` be split into smaller, more focused modules?**
  _Cohesion score 0.04081632653061224 - nodes in this community are weakly interconnected._
- **Why does `TestCase` connect `SalesTest.php` to `.agents/skills/testing-best-practices/SKILL.md`, `.claude/skills/testing-best-practices/SKILL.md`, `User`?**
  _High betweenness centrality (0.111) - this node is a cross-community bridge._
- **Are the 2 inferred relationships involving `SaleStatus` (e.g. with `[2026-11-23 11:00 WIB] — Anggota 4` and `[2026-11-23 11:00] — Anggota 4 — Modul Pencatatan Penjualan (fase 1)`) actually correct?**
  _`SaleStatus` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Should `Design System --- SmartFood` be split into smaller, more focused modules?**
  _Cohesion score 0.047619047619047616 - nodes in this community are weakly interconnected._