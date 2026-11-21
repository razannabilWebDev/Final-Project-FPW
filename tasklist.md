# Tasklist — SmartFood Home

> Checklist pekerjaan berjalan. Agent (atau anggota) memasukkan blok
> checklist SETIAP KALI mengerjakan sesuatu — rencana → status, terbaru
> di atas. Format: `- [ ]` belum · `- [x]` selesai · `- [~]` sebagian/blocked.
>
> **Batasan:** file ini adalah riwayat pekerjaan per sesi. Status fase
> proyek (Tahap 1–5) tetap bersumber dari `docs/implementation-plan.md` —
> jangan menduplikat centang fase di sini.

---

## [2026-11-21 21:23] — Anggota 2 — Tahap 1: Implementasi Master Menu

- [x] Audit struktur & Git: branch `feature/member-2-menu-recipe`; kontrak tabel `menus` di `docs/database.md` sudah sesuai migration ada → **tidak ada migration baru**
- [x] Konfirmasi permission final A1: `manage-menus` (Admin) + `auth`/`active`; tidak membuat gate/role alternatif
- [x] Model `Menu` (cast `decimal:2`/boolean, scope `active`) + `MenuFactory`
- [x] `MenuService` (create/update, upload & replace foto via Storage `public`, hapus foto lama, setActive) + `MenuPhotoUploadException`
- [x] `StoreMenuRequest` & `UpdateMenuRequest` (validasi nama/deskripsi/harga/foto + `authorize()` manage-menus, pesan Bahasa Indonesia)
- [x] `MenuController` (index, create, store, show, edit, update, setActive) dengan notifikasi sukses/gagal
- [x] 7 route `menus.*` di `routes/web.php`: `index`/`show` = `auth`+`active`; `create`/`store`/`edit`/`update`/`status` = `can:manage-menus`
- [x] 5 view Blade `menus/{index,create,edit,show,_form}` (Tailwind 4, format `Rp15.000`, badge status, konfirmasi nonaktif, error per field)
- [x] `tests/Feature/MenuManagementTest.php`: 14 test / 70 assertion lulus (CRUD, harga, foto, validasi, status, 403 per role, tamper snapshot historis)
- [x] Suite penuh: 54 test / 381 assertion lulus; `route:list --path=menus` sesuai; `vendor/bin/pint --dirty --format agent` passed
- [x] Prompt lengkap dicatat di `prompt_history.md` (entri 21:17) dan hasil akhir dilengkapi; tasklist ini diperbarui
- [ ] Menunggu persetujuan pengguna sebelum melanjutkan Tahap 2 (Resep/HPP)
- [ ] Koordinasi D4/D5/D7 dengan A1/A3/A4 (di luar lingkup Master Menu)
- [ ] Verifikasi symlink `public/storage` di dev lokal jika foto tidak tampil (test memakai `Storage::fake`)
- [ ] Commit: tidak dilakukan (menunggu instruksi)

## [2026-11-21 21:14] — Anggota 2 — Pencatatan prompt & finalisasi tasklist audit

- [x] Periksa struktur/format `prompt_history.md` yang sudah ada; konvensi: terbaru di atas, blok `text`, model AI, timestamp WIB
- [x] Pastikan prompt audit 21:07 sudah tercatat lengkap → tidak dibuat entri duplikat
- [x] Tambah entri baru untuk prompt 21:14 di bagian paling bawah `prompt_history.md` (sesuai instruksi) dengan Modul, Tahap, Tujuan, Ringkasan instruksi, Ringkasan hasil audit, File yang diperiksa, Status
- [x] Verifikasi isi `prompt_history.md` setelah edit; riwayat lama tetap utuh, template manual tidak dihapus
- [x] Tandai pekerjaan audit yang benar-benar selesai; implementasi Menu/Resep sengaja **tidak** ditandai selesai
- [x] Pertahankan format & riwayat `tasklist.md` (blok 21:07 dan entri lama tetap ada)
- [x] Verifikasi `git status --short`: hanya `prompt_history.md` & `tasklist.md` yang berubah; tidak ada kode/migration/dokumen kontrak tersentuh
- [ ] Menunggu persetujuan pengguna sebelum mulai coding Master Menu Tahap 1
- [ ] Menunggu persetujuan tim atas D4 (valuasi harga modal), D5 (konversi satuan), D7 (format snapshot & versi resep)
- [ ] Koordinasi kontrak service dengan A3 (bahan & HPP), A4 (snapshot HPP), dan A1 (nama route/service prediksi)
- [ ] Commit: tidak dilakukan (menunggu instruksi)

## [2026-11-21 21:07] — Anggota 2 — Audit & persiapan Menu/Resep (belum coding)

- [x] Git: branch aktif `feature/member-2-menu-recipe`, up-to-date `origin/...`, working tree clean; tidak ada checkout/merge/reset/commit
- [x] Baca `prd.md`, `TASK_INSTRUCTIONS.md`, `AGENTS.md`, `.ai/rules`, dan 7 dokumen `docs/` yang diminta
- [x] Verifikasi struktur aktual: 1 model (User), 6 controller auth/users/dashboard, 13 migration, 1 route file, 13 blade, 4 FormRequest, 3 service/action, 11 test, 8 seeder
- [x] Audit Menu: CRUD/harga/foto/nonaktif/historis/permission → seluruhnya belum diimplementasikan (baru schema + seeder)
- [x] Audit Resep: relasi, takaran, HPP, konversi, versi, snapshot, batas Pengelola → baru schema + seeder; belum ada service/UI/test
- [x] Audit integrasi A1/A3/A4, D4/D5/D7, serta urutan Tahap 1 vs Tahap 2 sesuai `implementation-plan.md`
- [x] Susun laporan audit 12 bagian; catat prompt + checklist hanya di dua file log
- [ ] Tunggu persetujuan pengguna sebelum coding atau membuat file modul apa pun
- [ ] Koordinasi D4 (valuasi), D5 (konversi), D7 (format snapshot/versi) dengan A3/A4/A1
- [ ] Commit: tidak dilakukan (sesuai instruksi audit)

## [2026-11-18 22:00] — Anggota 1 — Seluruh migration + seeder dummy

- [x] Catat prompt 21:58 & 22:00 ke `prompt_history.md` (sesuai aturan baru)
- [x] Buat 10 migration baru (18 tabel): menus, ingredients, expense_categories, recipes, stock, purchases, sales, expenses, forecasts, settings/activity_logs
- [x] Buat seeder lengkap & idempoten: users, master data, resep, stok/pembelian, penjualan/biaya, forecast, activity log
- [x] Tambah `DatabaseSchemaTest` (skema SQLite, seed 2x tanpa duplikat, konsistensi ledger, snapshot & reversal)
- [x] Update `docs/database.md`, `docs/decisions.md` (D11–D13), `docs/implementation-plan.md`, `AGENTS.md`
- [x] Jalankan pint + `composer test`: 40/40 test lulus, 311 assertion
- [x] Jalankan `php artisan migrate` + `php artisan db:seed` pada DB dev MySQL; seed diulang sekali, jumlah baris tetap; ledger 8 bahan konsisten
- [ ] Commit: belum (menunggu instruksi)

## [2026-11-18 21:32] — Anggota 1 — Aturan pencatatan prompt

- [x] Catat prompt pengguna sebagai entri teratas baru di `prompt_history.md` (teks verbatim + hasil)
- [x] Perkuat `AGENTS.md §Prompt log` jadi aturan wajib per-prompt: format, teks utuh, buang system-reminder/output tool, jawaban form di bullet terpisah, penulis = Anggota 1
- [x] Rekam rule durable via `laravel-boost_record-rule` → `.ai/rules/prompt-history-md.md`
- [x] Rapikan entri 21:15: anotasi `[jawaban form pertanyaan]` dipindah keluar dari blok prompt
- [x] Perbaiki konvensi penulis di `prompt_history.md`: sesi terkini = Anggota 1, riwayat lama tetap Anggota 4
- [x] Verifikasi entri + rule; tidak ada perubahan PHP sehingga pint/test tidak diperlukan
- [ ] Commit: belum (menunggu instruksi)

## [2026-11-18 21:15] — Anggota 1 — Autentikasi & Kelola Pengguna (fondasi role/permission)

- [x] Branch `feature/member-1-auth-users-forecast` dibuat dari `main`
- [x] Migration baru `add_role_and_status_to_users_table` (tidak mengedit migration lama); enum `UserRole`; update `User` + factory (state admin/pengelola/inactive)
- [x] `AuthServiceProvider`: 19 gate terpusat sesuai PRD §3.2; middleware `role` + `active` dan alias terdaftar
- [x] Login/logout/ganti password: Form Request, `AttemptLoginAction`, `ChangePasswordAction`, throttling `5,1`
- [x] Kelola Pengguna: CRUD akun oleh Admin (create Pengelola/Staf/Admin lain, edit, aktif/nonaktif) + guard self-role & self-deactivate
- [x] Layout auth + app shell minimal (role-filtered nav, responsive), view login/password/users/dashboard/403
- [x] Verifikasi: 33/33 test lulus, `vendor/bin/pint --dirty`, `npm run build`, `php artisan route:list`
- [x] Update dokumen kontrak: permission final, D8 final, enum role final, phase status
- [~] Rekomendasi Produksi ditunda: menunggu kontrak penjualan (A4), resep (A2), stok (A3)
- [~] Perlu koordinasi tim: gate baru `manage-recipe-costs`, `manage-stock`, `view-sales-summary`, `view-forecast`; nama route modul lain di layout; file bersama `bootstrap/app.php`
- [ ] Commit: belum (menunggu instruksi)

## [2026-11-16 19:42] — Anggota 4 — Tasklist konvensi

- [x] Buat `tasklist.md` (root repo): riwayat pekerjaan per sesi, 7 blok backfill, terbaru di atas
- [x] Tambah konvensi "## Tasklist" ke `AGENTS.md` (agent wajib masukkan checklist tiap sesi)
- [x] Commit `9e3d065`

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
