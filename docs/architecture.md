# Architecture Contract — SmartFood Home

> **STATUS: DRAFT — konvensi teknis bersama. Sepakati di Tahap 1
> (TASK_INSTRUCTIONS §8) sebelum pengembangan paralel.**

## 1. Stack (PRD §9.1 — tetap, tidak boleh ditukar)

- Laravel monolith, PHP ^8.3, Blade + Tailwind CSS 4 + Vite.
- Auth session bawaan Laravel (tanpa API/frontend terpisah).
- File storage: Laravel Storage (disk lokal).
- Timezone aplikasi `Asia/Jakarta`.
- Catatan lingkungan: dev & test lokal berjalan SQLite (`:memory:` untuk test), target produksi MySQL — keputusan driver di `docs/database.md` §5.

## 2. Struktur Kode — aturan wajib

- **Business logic di Service/Action class** (`app/Services/` atau `app/Actions/`), tidak pernah di controller atau Blade.
- **Validasi via Form Request** (`app/Http/Requests/`), bukan validasi inline di controller.
- **Authorization di backend** (middleware/policy/gate dari Anggota 1) — UI bukan lapisan keamanan; denial = 403.
- Controller tipis: terima Request → panggil Service → return response/redirect.

## 3. Konvensi Penamaan (usulan — final di Tahap 1)

| Item | Konvensi | Contoh |
|---|---|---|
| Route name | `modul.aksi` | `sales.index`, `sales.update-status`, `expenses.create` |
| URL | plural lowercase | `/sales`, `/expenses`, `/reports/profit` |
| Controller | `ModulController` | `SaleController`, `ExpenseController`, `ProfitReportController` |
| Service | `ModulService` / aksi spesifik | `SaleCreationService`, `StockDeductionService` (milik Anggota 3) |
| Form Request | `AksiModulRequest` | `StoreSaleRequest`, `UpdateSaleStatusRequest` |
| Model | singular, tabel default Laravel | `Sale`, `SaleItem`, `ExpenseCategory` |
| Blade view | `views/modul/halaman.blade.php` | `views/sales/index.blade.php` |
| Migration | deskriptif + urut domain | `create_sales_tables`, `create_expense_tables` |

## 4. Pola Lintas Modul

- **Satu sumber kebenaran per fungsi**: jangan duplikasi business logic atau membuat tabel/service alternatif milik anggota lain (§12).
- **Anggota 4 memanggil service Anggota 3** untuk semua perubahan stok — tidak pernah mengubah kolom stok langsung.
- **Transaksi DB** (`DB::transaction`) untuk operasi multi-tabel: pembuatan penjualan + item + status history (+ deduksi stok), persetujuan pembelian + stok masuk, penyesuaian stok fisik.
- **Idempotensi**: proses yang dipicu oleh perubahan status (deduksi stok, reversal) wajib aman dipanggil ulang — mekanisme penanda disepakati Anggota 3 & 4 (lihat `docs/decisions.md` D1, D6).
- **Snapshot** nama menu, harga jual, HPP ditulis saat transaksi; tidak ada query yang menimpa histori.
- **No hard delete** data berreferensi: gunakan `is_active`/soft delete (menus, users, expense_categories).

## 5. Penanganan Error

- Validasi gagal → response error Form Request standar Laravel (redirect back with errors untuk web).
- Stok tidak cukup saat deduksi → **gagal terkontrol**: tampilkan bahan yang kurang + jumlah kekurangan; tidak membuat stok negatif (PRD §6.5).
- Resep tidak ada/tidak lengkap → peringatan, **tidak** mengurangi stok perkiraan.
- Exception bisnis: gunakan exception domain (mis. `InsufficientStockException`) yang ditangkap service → response rapi, bukan 500 mentah.
- Rollback transaksi DB mengembalikan semua tulisan dalam 1 proses.

## 6. Response & UI Pola

- Web (Blade) redirect + flash message (toast sesuai `docs/design_system.md`).
- Status transaksi dirender sebagai badge dengan pemetaan warna dari design system (Menunggu/Diproses kuning, Selesai hijau, Ditolak merah).
- Semua teks UI Bahasa Indonesia; uang format `Rp15.000` (PRD §8.1).
- Setiap halaman wajib punya kondisi loading, kosong, sukses, error (Definition of Done tim §9).

## 7. Testing (PRD §13, PHPUnit)

- Feature test adalah default; unit test untuk kalkulasi murni (subtotal, HPP, laba).
- Skenario integrasi kritis: alur penjualan → deduksi stok; pembatalan → reversal; pembelian → verifikasi → stok masuk; permission per role (403).
- Jalankan: `composer test`; satu file: `php artisan test tests/Feature/NamaTest.php`.
- Style: `vendor/bin/pint --dirty` sebelum commit PHP.

## 8. Git & Kolaborasi (ringkas TASK_INSTRUCTIONS §6)

- Branch `feature/member-N-deskripsi` dari `master`; tidak ada commit langsung ke `master`.
- Conventional commits: `feat(sales): ...`, `fix(report): ...`, `test(purchase): ...`.
- PR wajib direview sebelum merge; file bersama (layout, `composer.json`, migration bersama, docs kontrak) tidak diubah sepihak (§6.4).

## 9. Kontrak Terbuka yang Masih Ditunggu

| Kontrak | Pemilik keputusan | Dipakai oleh |
|---|---|---|
| ~~Signature service stok (deduksi/reversal)~~ ✅ A3 (2026-11-23), kontrak di §10 | Anggota 3 | Anggota 4 |
| Format HPP & konversi satuan | Anggota 2 (+3) | Anggota 4 (snapshot) |
| ~~Middleware/gate final~~ ✅ Selesai (2026-11-18): 19 gate di `AuthServiceProvider`, alias `role`/`active` di `bootstrap/app.php` | Anggota 1 | Semua |
| Format `transaction_code` | Anggota 4 (diusulkan di `docs/database.md`) | Anggota 1 (prediksi) |

### Konvensi tambahan yang sudah tersedia (Anggota 1, 2026-11-18)

- Route auth/users: `login`, `login.store`, `logout`, `password.edit`, `password.update`, `dashboard`, `users.{index,create,store,edit,update,status}`.
- Route lupa password (guest): `password.request`, `password.email`, `password.reset`, `password.reset.store`. `password.update` tetap dipakai untuk ganti password setelah login, sehingga aksi reset memakai nama berbeda (FR-AUTH-06/D14).
- Layout bersama: `layouts.auth` (halaman login) dan `layouts.app` (sidebar role-filtered). Navigasi memakai `Route::has()` sehingga tidak error sebelum modul lain terpasang; nama route modul lain mengikuti konvensi §3 (`menus.index`, `recipes.index`, `ingredients.index`, `purchases.index`, `sales.index`, `expenses.index`, `reports.profit`, `forecast.index`) — koordinasikan bila berbeda.
- Gate baru di luar usulan awal: `manage-recipe-costs`, `manage-stock`, `view-sales-summary`, `view-forecast` (lihat `docs/permissions.md` §3).
- Route rekomendasi produksi: `forecast.index` (`can:view-forecast`, semua role), `forecast.generate` & `forecast.adjust` (`can:manage-forecast`, Admin). Implementasi: `CalculateMovingAverageAction`, `GenerateForecastAction`, `AdjustForecastAction`, `ForecastIngredientService`, model `ProductionForecast`/`ProductionForecastItem`, `config/forecast.php`.
- Audit aktivitas: `LogActivityAction` dipanggil dari `UserService` dan `ChangePasswordAction`; format action `user.*`, metadata JSON. A3/A4 menyusul setelah koordinasi D13.

## 10. Kontrak service stok (Anggota 3, 2026-11-23 — menunggu konfirmasi A2/A4)

- `StockService::deductForSale(int $saleId, array $requirements, ?User $actor = null): void`
  - `$requirements` = `list<array{ingredient_id: int, quantity: string}>`; kuantitas adalah string desimal (3 angka di belakang koma) dalam `base_unit` bahan.
  - Anggota 4 menghitung kebutuhan dari resep Anggota 2, lalu memanggil service **di dalam `DB::transaction` yang sama** dengan perubahan status ke `processing` (D1).
  - Anggota 3 hanya menulis `ingredients.current_stock` dan `stock_movements` bertipe `sale_out`; **tidak** menulis `sales.stock_deducted_at` / `reversal_done_at` (milik Anggota 4).
  - Idempotensi: bila sudah ada movement `reference_type='Sale'`, `reference_id=$saleId`, `movement_type='sale_out'`, panggilan berikutnya tidak melakukan apa pun.
  - Kekurangan stok melempar `InsufficientStockException` (dengan daftar bahan, kebutuhan, dan saldo) sebelum ada tulisan; tidak ada deduksi sebagian.
- `StockService::reverseForSale(int $saleId, ?User $actor = null): void`
  - Jumlah pembalikan dibaca dari baris ledger `sale_out` untuk transaksi tersebut, bukan dari argumen, sehingga reversal selalu mencerminkan stok yang benar-benar dipotong.
  - Tidak ada reversal ganda; tidak ada reversal bila belum pernah ada deduksi.
- `recordIn`, `recordOpeningStock`, `recordWaste`, dan `adjustPhysical` adalah satu-satunya jalur perubahan `current_stock`. Semua menulis `balance_before`/`balance_after`.
- D5: tidak ada tabel `unit_conversions`; semua kuantitas disimpan dalam `base_unit` bahan. Anggota 2 wajib mengonversi takaran resep ke `base_unit` sebelum memanggil service.
- D4: `average_cost` diperbarui dengan rata-rata tertimbang ketika `recordIn` menerima `unit_cost` (dipakai saat verifikasi pembelian oleh Anggota 3).
- Penyesuaian fisik menyimpan `system_quantity`, `physical_quantity`, `difference`, `reason`, `created_by`, lalu satu movement `adjustment` dengan `reference_type='StockAdjustment'`.
