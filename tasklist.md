# Tasklist — SmartFood Home

> Checklist pekerjaan berjalan. Agent (atau anggota) memasukkan blok
> checklist SETIAP KALI mengerjakan sesuatu — rencana → status, terbaru
> di atas. Format: `- [ ]` belum · `- [x]` selesai · `- [~]` sebagian/blocked.
>
> **Batasan:** file ini adalah riwayat pekerjaan per sesi. Status fase
> proyek (Tahap 1–5) tetap bersumber dari `docs/implementation-plan.md` —
> jangan menduplikat centang fase di sini.

---

## [2026-11-24 19:32] — Anggota 1 — Samakan sapaan dashboard

- [x] Catat 2 prompt sesi ini ke `prompt_history.md` (termasuk jawaban form pertanyaan)
- [x] Satukan sapaan jadi "Selamat datang, {nama}!" untuk Admin/Pengelola/Staf lewat `$greeting` di `DashboardController` (eyebrow tidak diubah)
- [x] `DashboardRoleTest` 8/8; `composer test` 120/120 lulus (642 assertion); pint & `npm run build` sukses
- [ ] Commit (hash menyusul)

## [2026-11-24 17:37] — Anggota 1 — Modal Tambah Pengguna

- [x] Catat 3 prompt sesi ini ke `prompt_history.md` (termasuk jawaban form pertanyaan)
- [x] Ubah tombol Tambah Pengguna jadi popup modal di halaman yang sama (`<x-modal>` reusable, form `user-create-form`)
- [x] `UserController::index()` meneruskan `$roles`; include `_form` dengan `user => null`; error validasi membuka modal otomatis via `@if ($errors->any())`
- [x] Halaman `users.create` standalone tetap dipertahankan (kompatibilitas test)
- [x] `UserManagementTest` 12/12; `composer test` 119/119 lulus (630 assertion); pint & `npm run build` sukses
- [ ] Commit (hash menyusul)

## [2026-11-23 21:46] — Anggota 1 — Audit log + Rekomendasi Produksi

- [x] Catat 3 prompt sesi ini ke `prompt_history.md` (termasuk jawaban form pertanyaan)
- [x] Audit log A1: `LogActivityAction` + integrasi `UserService`/`ChangePasswordAction`; `ActivityLogTest` 9 test/33 assertion
- [x] Rekomendasi Produksi: `config/forecast.php`, 2 model, 3 Action, `ForecastIngredientService` via kontrak `RecipeCostService`, controller + 3 route + view; `MovingAverageTest` (3) + `ForecastTest` (7)
- [x] Perbaiki kartu forecast di dashboard Admin (baca total dari `production_forecast_items`)
- [x] Update dokumen: D8 (rata-rata/idempotensi/bahan), D13 (helper A1), duplikat D14 ekspor → D15, architecture, permissions, implementation-plan
- [x] `composer install` untuk dependency ekspor A4 yang sudah dideklarasikan (perbaiki 1 test gagal di luar modul A1)
- [x] `composer test` 117/117 lulus (618 assertion); pint & `npm run build` sukses
- [x] Audit kelengkapan `prompt_history.md`: tambah entri 22:05 & 22:13, jawaban form entri 19:50, pulihkan entri 10:45 yang kosong
- [ ] Commit (hash menyusul)
## [2026-11-24 21:31] — Anggota 2 — Format takaran tanpa trailing .000

- [x] Catat prompt sesi ke `prompt_history.md`
- [x] Format takaran di form edit & detail resep (2.000 → 2)
- [x] Test regresi tampilan takaran
- [x] Verifikasi: pint bersih, 13/13 test resep, build sukses, full suite 130/131 (failure lama: excel)
- [x] Commit `2bc4cfb`

## [2026-11-24 20:52] — Anggota 2 — Resep: hapus, tambah bahan, dropdown satuan

- [x] Catat prompt sesi ke `prompt_history.md` (termasuk jawaban form pertanyaan)
- [x] Perbaiki tombol tambah bahan di modal edit (delegasi event + init ulang setelah fetch)
- [x] Satuan jadi dropdown dari base_unit bahan aktif + auto-select
- [x] Migration is_active recipes + service deaktivasi semua versi
- [x] Route destroy + aksi hapus + modal konfirmasi Figma
- [x] Test regresi (tambah bahan, dropdown, hapus, permission) — 12/12 lulus
- [x] Verifikasi: migrate dijalankan, pint bersih, build sukses, full suite 129/130 (failure lama: excel)
- [x] Commit `2bc4cfb`

## [2026-11-24 17:41] — Anggota 2 — Tahap 2: Pengelolaan Resep & HPP

- [x] Catat prompt sesi ke `prompt_history.md` (termasuk jawaban form pertanyaan)
- [x] Model Recipe/RecipeItem + factory + relasi
- [x] RecipeService: versioning, kalkulasi HPP bcmath, normalisasi satuan dasar
- [x] FormRequest, controller, route dengan gate view/manage-recipes/manage-recipe-costs
- [x] UI resep sesuai Figma (KPI, toolbar, tabel, modal Tambah/Ubah/Detail)
- [x] Implementasi kontrak RecipeCostService + ganti binding di AppServiceProvider
- [x] Test: HPP, versi, snapshot, permission, konversi satuan (10/10 lulus)
- [x] Dokumen: finalisasi D4/D5/D7 + interpretasi FR-RECIPE-03, update implementation-plan
- [x] Verifikasi: pint bersih, build sukses, full suite 127/128 (1 failure lama: excel)
- [x] Commit `2bc4cfb`

## [2026-11-24 17:27] — Anggota 2 — Koreksi penulis prompt history

- [x] Catat prompt "jangan lupa catat prompt ke @prompt_history.md sebagai anggota 2" sebagai entri teratas
- [x] Koreksi penulis 6 entri sesi ini (16:25–17:17 WIB) dari Anggota 1 → Anggota 2
- [x] Verifikasi 7 entri 2026-11-24 semuanya tercatat sebagai Anggota 2
- [x] Tidak ada perubahan kode atau dokumen kontrak

## [2026-11-24 17:17] — Anggota 2 — Fix akar masalah edit menu (variabel loop bocor ke modal)

- [x] Catat prompt sesi ke `prompt_history.md` (termasuk jawaban form pertanyaan)
- [x] Ganti variabel loop `$menu` → `$cardMenu` di grid kartu index
- [x] Mode form eksplisit (`$isEdit`) di partial form & `_form`, kirim dari semua pemanggil
- [x] Test regresi: index fresh tidak merender form edit di modal Tambah
- [x] Verifikasi: pint bersih, 20/20 test menu, render HTML benar, build sukses, full suite 117/118 (1 failure lama: excel)
- [x] Commit `2bc4cfb`

## [2026-11-24 17:00] — Anggota 2 — Fix edit menu via modal tidak menyimpan

- [x] Catat prompt sesi ke `prompt_history.md`
- [x] ID form & field unik untuk modal Tambah vs Ubah
- [x] Tombol submit footer diarahkan ke form masing-masing + JS busy handler
- [x] Test regresi alur edit modal
- [x] Verifikasi: pint bersih, `MenuManagementTest` 20/20, build sukses, full suite 117/118 (1 failure lama: `maatwebsite/excel` belum di-install)
- [x] Commit `2bc4cfb`

## [2026-11-24 16:39] — Anggota 2 — UI Pengelolaan Menu sesuai design system v2

- [x] Catat prompt sesi ke `prompt_history.md` (termasuk jawaban form pertanyaan)
- [x] Fondasi: ikon, badge, toast, modal, pagination + CSS pendukung
- [x] Shell minimal: sidebar aktif & topbar breadcrumb/peran statis/notifikasi/profil
- [x] `MenuController`: filter search/status, statistik KPI, dukungan `?partial=1`
- [x] Index menu: 3 KPI, toolbar, grid kartu foto, badge, aksi, empty state, pagination
- [x] Modal Tambah/Ubah/Detail/Konfirmasi + form 48px & upload sesuai batas backend
- [x] Test `MenuManagementTest` untuk filter, statistik, dan alur modal (19/19 lulus)
- [x] Verifikasi: pint bersih, `npm run build` sukses; full suite 115/116 (1 failure lama: `maatwebsite/excel` belum di-install di vendor); graphify CLI tidak tersedia
- [x] Commit `2bc4cfb`
## [2026-11-24 21:21] — Anggota 3 — Backfill log prompt sesi tanya-jawab

- [x] Audit `prompt_history.md`: 6 prompt Plan mode terakhir belum tercatat (mode build sudah di-backfill sebelumnya)
- [x] Tambah 6 entri teratas dengan teks prompt verbatim + hasil ringkas (21:04–21:21 WIB)
- [x] Catat keputusan: pencarian pembelian tetap berdasarkan kode & supplier saja; supplier bukan master data; verifikasi via modal detail pembelian
- [x] Catat status commit: 3 commit lokal, branch ahead 3, belum di-push
- [x] Tambah blok checklist sesi ini
- [x] Commit log: `docs(prompt): backfill plan-mode prompts`
- [ ] Tidak ada perubahan kode; tidak perlu test/pint/build

## [2026-11-24 18:06] — Anggota 3 — Modul Pembelian Bahan Baku

- [x] Catat 2 prompt sesi ini ke `prompt_history.md`
- [x] Commit pekerjaan stok sebelumnya: `0b3605f`
- [x] Enum `PurchaseStatus` + model/factory `Purchase`, `PurchaseItem`
- [x] `PurchaseService`: create multi-item (decimal, kode unik `PUR-YYYYMMDD-XXXX`) + verify idempoten; setuju → `StockService::recordIn` (ledger + `average_cost`), tolak → alasan wajib & stok tetap; semuanya dalam DB transaction + lock
- [x] 2 Form Request + `PurchaseController` + 5 route dengan gate `manage-stock` / `create-purchases` / `verify-purchases`
- [x] UI design v2: 3 KPI, tabel riwayat + search/filter + badge, modal input multi-item dengan unggah nota, modal detail + verifikasi, bukti nota privat
- [x] **Rekap/detail/bukti nota khusus Admin & Pengelola**; Staf hanya input dan diarahkan ke dashboard setelah simpan
- [x] `PurchaseTest`: 7 test (input+nota, rekap 403 Staf, setuju→ledger, tolak→stok tetap, verifikasi ganda, gate, kode urut)
- [x] Dokumentasi: `docs/architecture.md` §11 (kontrak), `docs/implementation-plan.md` Tahap 2–4, `docs/decisions.md` D15 (asumsi)
- [x] Verifikasi: 7/7 test pembelian, 106/108 suite penuh (2 gagal pre-existing di luar modul), pint bersih, `npm run build` sukses
- [x] Commit `e2a73d6` (feat(purchase): add purchase input and verification)
- [ ] Konfirmasi tim D15 (format kode pembelian) & D5 (satuan dasar)

## [2026-11-24 17:50] — Anggota 3 — Fix toggle status bahan baku

- [x] Catat 2 prompt sesi ini ke `prompt_history.md`
- [x] Diagnosis: JS modal mengirim state target `0` ke hidden input `is_active`, padahal `setActive()` membalik nilai (mengira state saat ini) → nonaktif jadi aktif
- [x] Perbaiki `resources/views/ingredients/index.blade.php`: kirim `button.dataset.active` (state saat ini)
- [x] Pastikan controller & test lama tidak diubah (konvensi `is_active` = state saat ini dipertahankan)
- [x] Verifikasi: 17/17 test modul stok lulus (75 assertion)
- [x] Commit `0b3605f` (feat(stock): align stock UI with design system v2)

## [2026-11-24 17:06] — Anggota 3 — Penyesuaian visual modul stok ke design_system_v2

- [x] Catat 3 prompt sesi ini ke `prompt_history.md` (termasuk jawaban form pertanyaan)
- [x] Audit view stok + screenshot Figma `-7.png` (stok), `-8.png` (pembelian), `-13.png`/`-19.png` (modal) dan konfirmasi field mock tidak disalin (PRD §7)
- [x] `IngredientController@index`: filter `search` + `status`, 4 angka KPI dari DB, `withQueryString()`, kirim `$units`
- [x] `StockMovementController@index`: filter `movement_type`, 3 angka KPI, `withQueryString()`
- [x] Rewrite `ingredients/index.blade.php`: header, KPI, toolbar search/filter, tabel badge status, aksi ikon, footer pagination, empty state
- [x] Modal Tambah/Ubah/Detail Bahan + konfirmasi perubahan status via `<x-modal>`; route lama tetap ada
- [x] `_form.blade.php` diberi prefix ID unik; create/edit standalone ikut dirapikan
- [x] Rapikan `stock/movements`, `stock/adjustments`, `stock/waste` (header, toolbar, input, tombol, empty state)
- [x] Tambah 3 test regresi: filter stok, KPI dari data nyata, filter jenis pergerakan
- [x] Verifikasi: 17/17 test modul, pint bersih, `npm run build` sukses; suite penuh 99/101 (2 gagal pre-existing di luar modul)
- [x] Commit `0b3605f` (feat(stock): align stock UI with design system v2)
- [x] Pembelian + verifikasi dikerjakan di sesi berikutnya

## [2026-11-23 19:50] — Anggota 1 — Audit modul + fitur lupa password

- [x] Catat 6 prompt sesi ini ke `prompt_history.md` (termasuk jawaban form pertanyaan)
- [x] Audit modul A1: auth/users/dashboard selesai; Rekomendasi Produksi belum ada; kontrak A2/A3/A4 perlu koordinasi
- [x] Analisis lupa password vs audit log; keputusan tim dicatat (email+SMTP produksi, akun nonaktif ditolak generik, FR-AUTH-06 + D14)
- [x] Panduan pembuatan Gmail App Password + variabel `.env` produksi diberikan ke user
- [x] Perbaiki `.env` lokal (kutip `MAIL_PASSWORD`, `MAIL_FROM_NAME`) setelah izin; `composer test` baseline 47/47 lulus
- [x] FR-AUTH-06 + D14 didokumentasikan di PRD & decision log
- [x] Implementasi: `User` `CanResetPassword` + notifikasi ID, 2 Action, 2 Form Request, controller, 4 route guest, 2 view, link login
- [x] Migration terpisah tidak diperlukan — tabel `password_reset_tokens` sudah ada di migration `users` bawaan Laravel 13
- [x] Test `ForgotPasswordTest` (10 test/27 assertion) + `composer test` 57/57 lulus (363 assertion); pint & npm build sukses
- [x] Uji SMTP sungguhan via `Mail::raw()` ke `razannabilannadif@gmail.com` berhasil; koreksi `MAIL_SCHEME=tls` → `smtp` (Laravel 13/Symfony tidak mendukung `tls`)
- [x] Hapus logo Laravel di email reset: `APP_NAME="SmartFood Home"` di `.env` + `.env.example`; assertion render HTML di test; D14 diperbarui; 10/10 lulus
- [ ] Commit (hash menyusul)
- [ ] Audit log menyusul (D13: pemilik final belum ditetapkan)
- [ ] Rekomendasi Produksi menyusul (butuh kontrak A2/A3/A4)

## [2026-11-23 10:45] — Anggota 1 — Fix tanggal purchase dashboard Pengelola
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
