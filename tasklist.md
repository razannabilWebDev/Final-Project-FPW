# Tasklist — SmartFood Home

> Checklist pekerjaan berjalan. Agent (atau anggota) memasukkan blok
> checklist SETIAP KALI mengerjakan sesuatu — rencana → status, terbaru
> di atas. Format: `- [ ]` belum · `- [x]` selesai · `- [~]` sebagian/blocked.
>
> **Batasan:** file ini adalah riwayat pekerjaan per sesi. Status fase
> proyek (Tahap 1–5) tetap bersumber dari `docs/implementation-plan.md` —
> jangan menduplikat centang fase di sini.

---

## [2026-11-23 10:45] — Anggota 4 — Fix tanggal purchase dashboard Pengelola

- [x] Perbaiki `Call to a member function translatedFormat() on string` di `pengelola.blade.php:78` (hasil `DB::table` bukan Carbon)
- [x] Tambah `purchase_date_label` di `DashboardController` via `Carbon::parse(...)->timezone('Asia/Jakarta')`
- [x] Tambah test regresi `test_pengelola_dashboard_renders_purchase_dates`
- [x] Pint + test 47/47 lulus (336 assertion)
- [ ] Commit (hash menyusul)

## [2026-11-23 10:30] — Anggota 4 — Login sesuai desain v2 + dashboard 3 role

- [x] Catat prompt sesi ke `prompt_history.md` (penulis Anggota 4)
- [x] Buat branch `feature/auth-dashboard` dari `origin/main`, merge branch Anggota 1 (`feature/member-1-auth-users-forecast`) tanpa konflik
- [x] Pasang token `design_system_v2.md` ke `resources/css/app.css` `@theme` (brand/navy/cream/semantik/chart + shadow) & font Inter
- [x] Buat komponen Blade reusable: `icon` (path Lucide inline), `stat-card`, `empty-state`
- [x] Login 2 panel sesuai §5 desain v2: branding navy + badge pill + headline two-tone + foto & kartu overlay; panel form dengan password toggle, "Lupa password?", info box akses; dropdown "Masuk sebagai" sengaja dihilangkan (role dari akun)
- [x] `DashboardController`: ringkas data per role dari tabel existing (aman via `Schema::hasTable`), pilih view admin/pengelola/staf
- [x] 3 view dashboard sesuai PRD §8.3: Admin (KPI keuangan, grafik, menu terlaris, stok menipis, pembelian, rekomendasi), Pengelola (aktivitas penjualan, pesanan, pembelian, stok, menu, estimasi — tanpa data keuangan), Staf (aksi cepat, pesanan, stok fisik, nota, estimasi)
- [x] Tambah `DashboardRoleTest` (5 test) + update assertion `LoginTest`; total 46/46 test lulus, 333 assertion
- [x] `npm install --ignore-scripts` + `npm run build`, `vendor/bin/pint --dirty`, `graphify update .`
- [x] Commit `0b24522` (branch `feature/auth-dashboard`)
## [2026-11-23 12:03] — Anggota 3 — Fondasi Pengelolaan Stok

- [x] Baca `prd.md`, `TASK_INSTRUCTIONS.md`, `docs/*`, dan audit kode yang ada sebelum bekerja
- [x] Catat prompt sesi ini ke `prompt_history.md` (termasuk jawaban form pertanyaan)
- [x] Model `Ingredient`, `StockMovement`, `StockAdjustment` + factory + `InsufficientStockException`
- [x] `StockService` (ledger, lock, non-negatif, deduct/reversal idempotent) + `IngredientService` + 2 actions
- [x] 4 Form Request, 4 controller, 11 route dengan gate; 7 view Blade Bahasa Indonesia
- [x] Test ledger, non-negatif, idempotensi, penyesuaian/susut, permission (21 test baru)
- [x] Verifikasi: pint bersih, 21/21 test modul, 61/61 seluruh suite, `npm run build` sukses
- [x] Kontrak service di `docs/architecture.md` §10; status D1/D3/D4/D5/D6 & rencana diperbarui
- [ ] Koordinasi A1 (nav Staf) & A2/A4 (satuan + kontrak stok); pembelian/verifikasi menyusul; commit bila diminta
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
## [2026-11-23 11:00] — Anggota 4 — Modul Pencatatan Penjualan (fase 1)

- [x] Kembali ke modul Anggota 4; prioritaskan Penjualan, pakai usulan default D1/D2/D3/D6/D11/D12, branch `feature/member-4-setup`
- [x] Enum `SaleStatus`, `PaymentMethod`; model `Sale`, `SaleItem`, `SaleStatusHistory`; migration `sales` (+ FK menus kondisional)
- [x] Stub service: `RecipeCostService`/`DefaultRecipeCostService` (HPP dari recipe_items × average_cost) & `StockService`/`DefaultStockService` (deduct/reversal idempoten + ledger `stock_movements`, cek non-negatif)
- [x] `SaleService`: transaksi DB, snapshot nama/harga/HPP, `transaction_code TRX-YYYYMMDD-XXXX`, history status, D1 potong sekali, D2 completed_at, D6 reversal sekali
- [x] FormRequest `StoreSaleRequest`/`UpdateSaleStatusRequest`, `SaleController`, route `sales.*` dengan gate
- [x] Blade sales (index/create/show) + komponen icon/stat-card/empty-state; layout sementara `layouts.sales`
- [x] `SaleFactory` + `SalesTest` (7 skenario: manual tanpa item, snapshot multi-item, deduksi/reversal idempoten, stok kurang gagal, completed_at, guest)
- [x] Pint + test 8/8 lulus (33 assertion)
- [x] Commit `e1e6847` (branch `feature/member-4-setup`)
- [~] Integrasi: ganti stub dengan service resmi Anggota 2 (HPP) & Anggota 3 (stok); samakan gate dengan `AuthServiceProvider` Anggota 1 saat merge

## [2026-11-23 10:15] — Anggota 4 — Design system v2 dari screenshot Figma panel-per-panel

- [x] Bandingkan `docs/design.md` vs `docs/design_system.md`; simpulkan Figma sebagai source of truth visual, `design_system.md` tetap dipakai untuk token/komponen
- [x] Baca `docs/UI SmartFood Home.png` (contact sheet) + seluruh screenshot per panel di `docs/UI SmartFood Home/` (login, 10 halaman modul, modal & delete dialog)
- [x] Catat temuan: spesifikasi login tidak ada di `design_system.md`; field modal mock bersifat generik dan tidak boleh menggantikan aturan bisnis PRD; `SmartFood Home Management Dashboard-3.png` kosong (export gagal)
- [x] Buat `docs/design_system_v2.md`: token + `@theme` Tailwind 4, warna grafik, shell & pola tiap modul, spesifikasi login lengkap, komponen/modal, state wajib, aksesibilitas, catatan mock vs PRD, riwayat versi
- [x] Catat sesi ini ke `prompt_history.md` + `tasklist.md`
- [~] Menunggu koordinasi tim: tinjau/koordinasi penggantian `design_system.md` oleh v2 (aset bersama, TASK_INSTRUCTIONS §6.4) — file lama sengaja tidak ditimpa
- [ ] Commit `docs/design_system_v2.md` + log (belum di-commit; user yang commit)

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
