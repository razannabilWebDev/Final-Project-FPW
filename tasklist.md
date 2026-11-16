# Tasklist — SmartFood Home

> Checklist pekerjaan berjalan. Agent (atau anggota) memasukkan blok
> checklist SETIAP KALI mengerjakan sesuatu — rencana → status, terbaru
> di atas. Format: `- [ ]` belum · `- [x]` selesai · `- [~]` sebagian/blocked.
>
> **Batasan:** file ini adalah riwayat pekerjaan per sesi. Status fase
> proyek (Tahap 1–5) tetap bersumber dari `docs/implementation-plan.md` —
> jangan menduplikat centang fase di sini.

---

## [2026-11-16 19:35] — Anggota 4 — Onboarding tim: README + panduan docx

- [x] Ganti `README.md` dari boilerplate Laravel menjadi onboarding tim (setup, checklist Tahap 0–1, template prompt agent, git workflow)
- [x] Buat `docs/panduan-tim.docx` (11 bagian: pembagian tugas, setup, dokumen wajib, checklist, D1–D10, aturan non-negotiable, panduan AI agent + contoh prompt baik/buruk, git, DoD, peta koordinasi) via python-docx
- [x] Commit `be3af6a`

## [2026-11-16 19:05] — Anggota 4 — Fondasi tooling + dokumen kontrak

- [x] Verifikasi skeleton sehat (`composer test` 2/2 lulus)
- [x] Install Laravel Boost v2.10 + sinkronisasi 5 skill + MCP (commit `11f4016`)
- [x] Draft `docs/database.md` (baseline 20 tabel per pemilik, aturan integritas)
- [x] Draft `docs/permissions.md` (usulan gate/middleware — final say Anggota 1)
- [x] Draft `docs/architecture.md` (konvensi penamaan, pola Service/Form Request)
- [x] Draft `docs/implementation-plan.md` (status Tahap 0–5 + checklist §10)
- [x] Draft `docs/decisions.md` (usulan default D1–D10, menunggu persetujuan tim)
- [x] Commit `edc7965`
- [~] Menunggu tim: review 5 dokumen kontrak, putuskan driver DB, finalisasi permission, sepakati dependency bersama

## [2026-11-16 18:33] — Anggota 4 — Design system dari Figma

- [x] Baca source Figma Make (`index.css` + `App.tsx`) via MCP Figma
- [x] Tulis `docs/design_system.md`: token warna/typografi, blok `@theme` Tailwind 4, pemetaan badge status, 12+ komponen, layout shell, konvensi konten
- [x] Tandai 3 temuan deviasi: font <10px (normalisasi ≥11px), format uang mock vs PRD, dropdown switch-role hanya mock
- [x] Commit oleh user (`f48ac13`)

## [2026-11-16 18:05] — Anggota 4 — Identitas Anggota 4

- [x] Buat catatan identitas di konfigurasi global OpenCode (`~/.config/opencode/AGENTS.md`, hanya mesin ini, tidak ikut commit)
- [x] Isi placeholder nama `(isi nama/peran)` → "Anggota 4" di `prompt_history.md` + konvensi penulis entri

## [2026-11-16 17:03] — Anggota 4 — Graphify (skill proyek)

- [x] Install graphify skill + plugin hook di `.opencode/` (level proyek)
- [x] Tambah instruksi graphify ke `AGENTS.md`
- [~] Knowledge graph `graphify-out/` sengaja ditunda — dibangun setelah modul inti (auth, master data, integrasi penjualan↔stok) ada di `master`

## [2026-11-16 16:59] — Anggota 4 — Prompt log

- [x] Buat `prompt_history.md` dengan template entri + konvensi (timestamp WIB, model, teks prompt utuh, hasil)
- [x] Backfill prompt sebelumnya dari database sesi OpenCode

## [2026-11-16 sebelumnya] — Anggota 4 — Dokumen tim pertama kali

- [x] Tulis `AGENTS.md` (aturan agent repo-specific + konvensi enforced)
- [x] Commit dokumen fondasi oleh user: `prd.md`, `TASK_INSTRUCTIONS.md`, `prompt_history.md` (`2ae25c6`), `docs/design_system.md` (`f48ac13`)
