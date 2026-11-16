# Database Contract — SmartFood Home

> **STATUS: DRAFT — perlu disepakati tim sebelum migration pertama di-merge.**
> Sumber baseline: PRD §7 (rancangan database awal). PRD §7 membolehkan
> penyesuaian nama tabel/kolom, tetapi relasi dan aturan integritas wajib
> dipertahankan. Perubahan dokumen ini hanya boleh lewat kesepakatan tim
> (TASK_INSTRUCTIONS §4.2, §5).

## 1. Kepemilikan Schema (TASK_INSTRUCTIONS §5.1)

| Area data | Penanggung jawab |
|---|---|
| `users` (role, status akun) | Anggota 1 |
| `production_forecasts`, `production_forecast_items` | Anggota 1 |
| `menus`, `recipes`, `recipe_items` | Anggota 2 |
| `ingredients`, `stock_movements`, `stock_adjustments` | Anggota 3 |
| `purchases`, `purchase_items` | Anggota 3 |
| `sales`, `sale_items`, `sale_status_histories` | Anggota 4 |
| `expense_categories`, `expenses` | Anggota 4 |
| `settings`, `activity_logs` | Disepakati bersama — pemilik perubahan ditentukan sebelum dibuat |

Setiap anggota menulis migration miliknya sendiri; jangan mengedit migration
bersama yang sudah masuk branch utama tanpa koordinasi (§5.2).

## 2. Baseline Tabel (PRD §7.1)

Kolom `id`, `created_at`, `updated_at` tersirat. Kolom timestamp tambahan
disebut eksplisit.

### Anggota 1

**`users`**
- `name` string
- `email` string unique (identitas login unik — PRD §3.3)
- `password` string (hash bcrypt)
- `role` enum: `admin`, `pengelola`, `staf` *(usulan nilai enum — final Anggota 1)*
- `is_active` boolean default `true` — akun nonaktif tidak bisa login

### Anggota 2

**`menus`**
- `name` string
- `description` text nullable
- `selling_price` decimal(12,2)
- `image_path` string nullable (disimpan via Laravel Storage, bukan kolom BLOB)
- `is_active` boolean default `true` (tidak hard-delete jika sudah direferensikan)

**`recipes`**
- `menu_id` FK → `menus`
- `notes` text nullable
- `version` unsigned integer — resep ber-versi; perubahan takaran = versi baru

**`recipe_items`**
- `recipe_id` FK → `recipes`
- `ingredient_id` FK → `ingredients`
- `quantity` decimal(12,3)
- `unit` string — satuan konsisten dengan master bahan (konversi: keputusan §11.5)

### Anggota 3

**`ingredients`**
- `name` string
- `base_unit` string
- `current_stock` decimal(12,3) — **hanya di-update lewat service stok Anggota 3**, tidak pernah langsung
- `minimum_stock` decimal(12,3)
- `average_cost` decimal(12,2)
- `is_active` boolean default `true`

**`stock_movements`** (ledger, append-only)
- `ingredient_id` FK → `ingredients`
- `movement_type` string — usulan nilai: `purchase_in`, `sale_out`, `waste`, `adjustment`, `reversal` *(final Anggota 3)*
- `quantity_in` decimal(12,3), `quantity_out` decimal(12,3)
- `balance_before` decimal(12,3), `balance_after` decimal(12,3) — wajib konsisten
- `reference_type` string + `reference_id` unsigned int nullable — polimorfik (mis. `Sale`, `Purchase`)
- `note` string nullable, `created_by` FK → `users`

**`stock_adjustments`**
- `ingredient_id` FK → `ingredients`
- `system_quantity`, `physical_quantity`, `difference` decimal(12,3)
- `reason` string, `created_by` FK → `users`

**`purchases`**
- `purchase_code` string unique
- `purchase_date` date
- `supplier_name` string nullable
- `status` enum: `pending`, `approved`, `rejected` (persetujuan sekali saja — PRD §6.6)
- `total` decimal(12,2)
- `receipt_path` string nullable (foto nota di Storage)
- `created_by`, `verified_by` FK → `users` nullable
- `verified_at` datetime nullable
- `rejection_note` text nullable *(usulan — PRD §6.6 wajib simpan alasan penolakan; nama kolom final Anggota 3)*

**`purchase_items`**
- `purchase_id` FK → `purchases`
- `ingredient_id` FK → `ingredients`
- `quantity` decimal(12,3), `unit` string
- `unit_cost` decimal(12,2), `subtotal` decimal(12,2)

### Anggota 4

**`sales`**
- `transaction_code` string unique (format usulan: `TRX-YYYYMMDD-XXXX`; lihat §6 di bawah)
- `type` enum: `menu`, `manual` — penjualan manual tidak punya item & tidak memengaruhi stok (PRD §6.3)
- `status` enum: `pending`, `processing`, `completed`, `cancelled` (PRD §6.1)
- `payment_method` enum: `cash`, `transfer`, `qris` (PRD §6.2)
- `total` decimal(12,2) — untuk `manual`: nominal pendapatan yang diinput
- `note` text nullable (keterangan penjualan manual)
- `created_by` FK → `users`
- `completed_at` datetime nullable — waktu transaksi mencapai status `completed`
- `stock_deducted_at` datetime nullable *(usulan, kolom idempotensi: menandai stok sudah dikurangi sekali)*
- `reversal_done_at` datetime nullable *(usulan, idempotensi reversal saat pembatalan)*
- Kolom snapshot HPP agregat per transaksi **tidak** disimpan di header; HPP per item ada di `sale_items`.

**`sale_items`**
- `sale_id` FK → `sales`
- `menu_id` FK → `menus` nullable (jaga jika menu dinonaktifkan/dihapus soft)
- `menu_name_snapshot` string — snapshot nama menu
- `quantity` decimal(12,3)
- `unit_price_snapshot` decimal(12,2) — snapshot harga jual
- `hpp_snapshot` decimal(12,2) — snapshot HPP per porsi saat transaksi (format: keputusan §11.7)
- `subtotal` decimal(12,2) = quantity × unit_price_snapshot

**`sale_status_histories`** (append-only)
- `sale_id` FK → `sales`
- `from_status` string nullable, `to_status` string
- `changed_by` FK → `users`
- `note` string nullable, `created_at` (tanpa `updated_at` — baris tidak pernah diubah)

**`expense_categories`**
- `name` string
- `description` string nullable
- `is_active` boolean default `true` — kategori terpakai tidak boleh hard-delete (PRD §6.8)
- Seed kategori awal: pembelian bahan baku, listrik/air, gas, gaji, sewa, transportasi, perawatan, lainnya *(keputusan §11.9)*

**`expenses`**
- `expense_category_id` FK → `expense_categories`
- `expense_date` date
- `amount` decimal(12,2)
- `description` text nullable
- `created_by` FK → `users`

### Bersama (pemilik belum ditetapkan)

**`settings`** — `key` string unique, `value` text
**`activity_logs`** — `user_id` FK nullable, `action`, `subject_type`, `subject_id` nullable, `metadata` json, `created_at`

## 3. Relasi Utama (PRD §7.2)

- `users` 1—N `sales`, `purchases`, `expenses`, `activity_logs`
- `menus` 1—N `recipes` (ber-versi) → N `recipe_items` → `ingredients`
- `sales` 1—N `sale_items`, N `sale_status_histories`
- `purchases` 1—N `purchase_items`
- `ingredients` 1—N `stock_movements`
- `expense_categories` 1—N `expenses`
- `production_forecasts` 1—N `production_forecast_items`

## 4. Aturan Integritas (PRD §7.3) — non-negotiable

1. FK untuk semua relasi penting, dengan `restrict`/soft relation — bukan cascade delete pada data transaksional.
2. Uang & kuantitas pecahan = `decimal`, **bukan float**.
3. Proses multi-tabel (penjualan, pembelian, penyesuaian stok) dalam `DB::transaction`.
4. `transaction_code` & `purchase_code` unique.
5. Snapshot harga jual + HPP di `sale_items`; perubahan menu/resep/harga tidak pernah menulis ulang histori.
6. Semua perubahan stok via `stock_movements` (ledger dengan `balance_before/after`); kolom stok tidak di-update langsung.
7. Data master yang sudah direferensikan → soft delete atau `is_active`, bukan hard delete.
8. Waktu disimpan `Asia/Jakarta`; tampilan sesuai zona usaha.
9. Foto/nota disimpan di Laravel Storage; kolom hanya menyimpan path.

## 5. Keputusan Terbuka (harus diputuskan tim sebelum migration pertama)

- [ ] Driver database: MySQL (PRD §9.1) vs SQLite untuk dev lokal — **belum ada migration, sekarang waktunya memutuskan**. Usulan: dev MySQL, test SQLite `:memory:`.
- [ ] Nilai enum `role` di `users` (usulan: `admin|pengelola|staf`).
- [ ] Penamaan kolom idempotensi penjualan (`stock_deducted_at` dll.) — final Anggota 3 & 4 bersama.
- [ ] Format `transaction_code`.
- [ ] Desain detail `settings`/`activity_logs` + pemiliknya.
