# Riwayat Prompt — SmartFood Home

Log prompt yang dikirim ke AI coding agent (OpenCode) selama mengerjakan proyek ini.

**Konvensi:**
- Entri diurut dari yang terbaru.
- Timestamp dalam zona waktu `Asia/Jakarta` (WIB), diambil dari metadata sesi OpenCode.
- Blok `<system-reminder>` (instruksi bawaan harness, bukan tulisan pengguna) dihapus dari teks prompt.
- **Sumber data:** database sesi OpenCode lokal (`opencode.db`) hanya berisi sesi dari **mesin ini**. Prompt anggota tim lain tidak otomatis tercatat — tambahkan secara manual dengan template di bagian bawah file ini.
- Kolom nama/peran mencatat **penulis** entri, bukan pembaca file. Entri dari mesin ini ditulis oleh **Anggota 4**; anggota lain menulis entri mereka sendiri dengan nama masing-masing.

---

## [2026-11-16 19:05 WIB] — Anggota 4
- **Model AI:** GLM-5.3-Flash Free (`opencode/glm-5.3-flash-free`) via OpenCode — agent: build
- **Prompt:**
  ```text
  sudah di buat, selanjutnya apa yang sebaiknya dilakukan/ditambahkan sebelum mulai menulis kode program
  jalankan
  ```
- **Hasil:** Fondasi di-commit user sendiri (`2ae25c6`, `f48ac13` di `master`); branch `feature/member-4-setup` dibuat dari situ. `composer test` sehat (2/2). Laravel Boost v2.10 terpasang (`composer require laravel/boost --dev` + `php artisan boost:install`): 5 skill disinkron, MCP laravel-boost terdaftar untuk Claude Code & OpenCode, guideline digenerate di `AGENTS.md`. Perubahan Boost di-commit di branch.

## [2026-11-16 18:33 WIB] — Anggota 4
- **Model AI:** GLM-5.3-Flash Free (`opencode/glm-5.3-flash-free`) via OpenCode — agent: build
- **Prompt:**
  ```text
  pilihan c
  ini link figma make nya https://www.figma.com/make/xpI3m9AQS5hDLPVqD2RYcG/SmartFood-Home-Management-Dashboard?t=7ZvKXCZSX14vRvL8-1
  ```
- **Hasil:** `docs/design_system.md` dibuat dari desain Figma Make (diekstrak via MCP Figma: `index.css` + `App.tsx` dari source file Make): token warna/typografi/radius, pemetaan badge status, komponen inti, layout shell, navigasi per role, konvensi konten.

## [2026-11-16 18:05 WIB] — Anggota 4
- **Model AI:** GLM-5.3-Flash Free (`opencode/glm-5.3-flash-free`) via OpenCode — agent: build
- **Prompt:**
  ```text
  jalankan
  ```
- **Hasil:** Identitas Anggota 4 dicatat di konfigurasi global OpenCode (`~/.config/opencode/AGENTS.md`, hanya mesin ini, tidak ikut commit) dan semua placeholder nama di file ini diisi "Anggota 4".

## [2026-11-16 17:03 WIB] — Anggota 4
- **Model AI:** GLM-5.3-Flash Free (`opencode/glm-5.3-flash-free`) via OpenCode — agent: build
- **Prompt:**
  ```text
  jalankan agar tersedia untuk tim juga
  ```
- **Hasil:** `graphify install --platform opencode --project` dijalankan: skill + plugin hook di `.opencode/`, instruksi graphify ditambahkan ke `AGENTS.md`. Perlu `git add .opencode/ AGENTS.md` agar ter-commit.

## [2026-11-16 16:59 WIB] — Anggota 4
- **Model AI:** GLM-5.3-Flash Free (`opencode/glm-5.3-flash-free`) via OpenCode — agent: build
- **Prompt:**
  ```text
  install skill ini https://github.com/Graphify-Labs/graphify
  ```
- **Hasil:** Skill `graphify` terpasang: CLI `graphifyy 0.9.111` via pipx, skill terdaftar untuk OpenCode (`~/.config/opencode/skills/graphify/` + plugin hook) dan Claude Code (`~/.claude/skills/graphify/`).

## [2026-11-16 16:48 WIB] — Anggota 4
- **Model AI:** GLM-5.3-Flash Free (`opencode/glm-5.3-flash-free`) via OpenCode — agent: build
- **Prompt:**
  ```text
  kerjakan
  ```
- **Hasil:** File `prompt_history.md` dibuat (file ini) berisi pulihan riwayat sesi dari `opencode.db`, lalu ditambahkan catatan pemeliharaan di `AGENTS.md`.

## [2026-11-16 16:46 WIB] — Anggota 4
- **Model AI:** GLM-5.3-Flash Free (`opencode/glm-5.3-flash-free`) via OpenCode
- **Prompt:**
  ```text
  buatkan file prompt_history.md yang memuat setiap prompt yang dilakukan oleh setiap orang yang mengerjakan projek ini, sertakan model ai yang digunakan serta timestamp nya
  ```
- **Jawaban via form pertanyaan:** cakupan = "Mesin ini + template manual"; format = "Teks lengkap".
- **Hasil:** Rencana dibuat (ekstraksi dari `opencode.db`, format log, template manual untuk anggota lain).

## [2026-11-16 16:40 WIB] — Anggota 4
- **Model AI:** GLM-5.3-Flash Free (`opencode/glm-5.3-flash-free`) via OpenCode — agent: build
- **Prompt:**
  ```text
  Create or update `AGENTS.md` for this repository.

  The goal is a compact instruction file that helps future OpenCode sessions avoid mistakes and ramp up quickly. Every line should answer: "Would an agent likely miss this without help?" If not, leave it out.

  User-provided focus or constraints (honor these):


  ## How to investigate

  Read the highest-value sources first:
  - `README*`, root manifests, workspace config, lockfiles
  - build, test, lint, formatter, typecheck, and codegen config
  - CI workflows and pre-commit / task runner config
  - existing instruction files (`AGENTS.md`, `CLAUDE.md`, `.cursor/rules/`, `.cursorrules`, `.github/copilot-instructions.md`)
  - repo-local OpenCode config such as `opencode.json`

  If architecture is still unclear after reading config and docs, inspect a small number of representative code files to find the real entrypoints, package boundaries, and execution flow. Prefer reading the files that explain how the system is wired together over random leaf files.

  Prefer executable sources of truth over prose. If docs conflict with config or scripts, trust the executable source and only keep what you can verify.

  ## What to extract

  Look for the highest-signal facts for an agent working in this repo:
  - exact developer commands, especially non-obvious ones
  - how to run a single test, a single package, or a focused verification step
  - required command order when it matters, such as `lint -> typecheck -> test`
  - monorepo or multi-package boundaries, ownership of major directories, and the real app/library entrypoints
  - framework or toolchain quirks: generated code, migrations, codegen, build artifacts, special env loading, dev servers, infra deploy flow
  - testing quirks: fixtures, integration test prerequisites, snapshot workflows, required services, flaky or expensive suites
  - important constraints from existing instruction files worth preserving

  Good `AGENTS.md` content is usually hard-earned context that took reading multiple files to infer.

  ## Questions

  Only ask the user questions if the repo cannot answer something important. Use the `question` tool for one short batch at most.

  Good questions:
  - undocumented team conventions
  - branch / PR / release expectations
  - missing setup or test prerequisites that are known but not written down

  Do not ask about anything the repo already makes clear.

  ## Writing rules

  Include only high-signal, repo-specific guidance such as:
  - exact commands and shortcuts the agent would otherwise guess wrong
  - architecture notes that are not obvious from filenames
  - conventions that differ from language or framework defaults
  - setup requirements, environment quirks, and operational gotchas
  - references to existing instruction sources that matter

  Exclude:
  - generic software advice
  - long tutorials or exhaustive file trees
  - obvious language conventions
  - speculative claims or anything you could not verify
  - content better stored in another file referenced via `opencode.json` `instructions`

  When in doubt, omit.

  Prefer short sections and bullets. If the repo is simple, keep the file simple. If the repo is large, summarize the few structural facts that actually change how an agent should work.

  If `AGENTS.md` already exists at `C:\Laravel\SmartfoodHome`, improve it in place rather than rewriting blindly. Preserve verified useful guidance, delete fluff or stale claims, and reconcile it with the current codebase.
  ```
- **Hasil:** `AGENTS.md` ditulis ulang: panduan spesifik repo (spesifikasi `prd.md`/`TASK_INSTRUCTIONS.md`, perintah, kebiasaan SQLite-vs-MySQL, aturan stok/snapshot/presisi, git) + blok `<laravel-boost-guidelines>` diringkas.

---

<!-- Riwayat sebelum 2026-11-16 tidak tersedia di mesin ini. -->

## Template entri manual (untuk anggota tim lain)

Salin blok di bawah, isi, lalu tempel **di atas** entri terlama (urutan tetap terbaru → terlama).
Prompt dapat dipulihkan dari riwayat sesi OpenCode masing-masing mesin (buka aplikasi OpenCode → riwayat sesi).

```markdown
## [YYYY-MM-DD HH:MM WIB] — [Nama / Anggota N]
- **Model AI:** [nama model] (`provider/model-id`) via [OpenCode/agent lain]
- **Prompt:**
  ```text
  ...teks prompt lengkap...
  ```
- **Hasil:** [ringkasan singkat + file yang berubah]
```
