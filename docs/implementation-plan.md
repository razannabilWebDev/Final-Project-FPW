# Implementation Plan — SmartFood Home

> **STATUS: DRAFT — pantau status di sini. Checklist pekerjaan dari
> TASK_INSTRUCTIONS §10; urutan fase dari PRD §11 + §14.2.**
> Perbarui centang saat status nyata berubah (di repository / project board).

## Tahap 0 — Fondasi Repo & Tooling

- [x] Skeleton Laravel 13 sehat (`composer test` 2/2 pass)
- [x] Dokumen tim ter-commit: `prd.md`, `TASK_INSTRUCTIONS.md`, `AGENTS.md`, `prompt_history.md`
- [x] `docs/design_system.md` (dari Figma Make, via MCP)
- [x] Laravel Boost + graphify skill terpasang (`feature/member-4-setup`, commit `11f4016`)
- [x] `docs/database.md`, `docs/permissions.md`, `docs/architecture.md` — **draft siap direview tim**
- [ ] **Keputusan tim:** driver DB (MySQL vs SQLite untuk dev) → `docs/database.md` §5
- [ ] Sepakati & finalisasi 4 dokumen kontrak di atas (review Anggota 1 utk permission)
- [ ] Sepakati dependency bersama (PDF/Excel export, Lucide icons utk Blade) — jangan install sepihak (§6.4)
- [ ] Layout & komponen Blade bersama dari design system — satu orang membangun, yang lain konsumsi (§6.4)

## Tahap 1 — Fondasi Bersama (TASK_INSTRUCTIONS §8)

- [x] Auth: login, logout, ganti password (Anggota 1 — 2026-11-18, 33 test lulus)
- [x] Role + middleware/policy dari `docs/permissions.md` (Anggota 1 — 19 gate, alias `role`/`active`)
- [x] Kelola Pengguna: CRUD + nonaktifkan akun oleh Admin (Anggota 1 — guard self-role/self-deactivate)
- [ ] Master menu + harga jual (Anggota 2)
- [ ] Master bahan baku + satuan/konversi (Anggota 3)
- [ ] Service stok terpusat + `stock_movements` ledger (Anggota 3)
- [x] Migration semua tabel dari `docs/database.md`, urut dependensi FK (Anggota 1, 2026-11-18)
- [x] Seeder dummy lengkap & idempoten + test konsistensi ledger (`DatabaseSchemaTest`, 40 test lulus)
- [ ] Layout dasar + komponen umum (card, badge, DataTable, modal, toast)

## Tahap 2 — Pengembangan Paralel (boleh mulai setelah kontrak disepakati)

- **Anggota 1:** kelola pengguna, rekomendasi produksi
- **Anggota 2:** resep & HPP per porsi, snapshot/versi resep
- **Anggota 3:** pembelian + verifikasi → stok masuk, stok susut/rusak, penyesuaian stok fisik
- **Anggota 4 (modul kami):**
  - [x] Pencatatan penjualan: transaksi berbasis menu (multi-item) + penjualan manual — commit `e1e6847`
  - [x] Update status transaksi + `sale_status_histories` — commit `e1e6847`
  - [~] Deduksi stok saat status pemicu + reversal saat pembatalan: **logika & idempotensi sudah ada** (`SaleService` + `DefaultStockService`, commit `e1e6847`), tapi masih pakai **stub**; perlu diganti service asli Anggota 3
  - [x] Biaya operasional: kategori + pengeluaran (filter ganda: date range + harian/bulanan/tahunan) — commit `76054ed`
  - [x] Laporan keuntungan: pendapatan − HPP (snapshot) − biaya; manual ditandai "HPP tidak tersedia" (bukan 0) — commit `20253ea`
  - [ ] Ekspor PDF/Excel — **tombol sengaja disabled**, menunggu keputusan dependency tim (baris 16)
  - Boleh memakai stub/kontrak data bila dependensi (menu/HPP/service stok) belum siap (§8 Tahap 2) — **sedang dipakai** untuk `RecipeCostService` & `StockService`

### Catatan koordinasi Anggota 4 (2026-11-23)

- Branch kerja: `feature/member-4-setup` (sudah di-push, 65 test lulus). Sudah me-merge fondasi auth Anggota 1.
- **Perlu Anggota 2:** ganti `DefaultRecipeCostService` dengan service HPP/resep asli (signature: `costPerPortion(int $menuId): string`, `ingredientsPerPortion(int $menuId): array` di `app/Services/Contracts/RecipeCostService.php`).
- **Perlu Anggota 3:** ganti `DefaultStockService` dengan service stok terpusat (signature: `deductForSale(int $saleId, array $items, ?int $userId): void`, `reverseForSale(int $saleId, ?int $userId): void` di `app/Services/Contracts/StockService.php`). Idempotensi memakai `sales.stock_deducted_at` & `sales.reversal_done_at` (D11).
- **Perlu keputusan tim:** dependency ekspor PDF/Excel (baris 16) sebelum tombol di laporan diaktifkan.
- **Perlu koordinasi merge:** `feature/member-4-setup` → `develop`/`main` setelah kontrak di atas disepakati.

## Tahap 3 — Integrasi (TASK_INSTRUCTIONS §8)

1. [~] Auth & permission di semua modul — gate tersedia & dipakai Sales/Expense/Report; modul A2/A3 belum diverifikasi
2. [ ] Menu & resep ↔ master bahan (Anggota 2)
3. [~] Penjualan ↔ menu, HPP, service stok — stub aktif, menunggu service A2/A3
4. [ ] Pembelian ↔ stok masuk (Anggota 3)
5. [x] Biaya & penjualan → laporan keuntungan — commit `20253ea`
6. [ ] Penjualan + resep + stok → rekomendasi produksi (Anggota 1)

## Tahap 4 — Pengujian Sistem

- [x] Login 3 role; pembatasan akses (403) — AuthTest/PermissionTest Anggota 1 + ExpenseTest/ProfitReportTest
- [~] Transaksi multi-item → stok berkurang sesuai resep — sudah di-test dengan stub (SalesTest), perlu test ulang dengan service A3
- [~] Pembatalan → reversal stok (idempotent) — sudah di-test dengan stub (SalesTest), perlu test ulang dengan service A3
- [ ] Pembelian → verifikasi → stok masuk (Anggota 3)
- [x] Biaya operasional & laporan keuntungan benar — ExpenseTest + ProfitReportTest
- [ ] Rekomendasi produksi (abaikan penjualan manual) — kontrak D8 final, implementasi Anggota 1

## Tahap 5 — Stabilitas & Dokumentasi

- [ ] Perbaikan bug integrasi; seluruh test lulus
- [ ] Keamanan permission & upload; responsive
- [ ] README + dokumentasi teknis; backup/deployment

## Keputusan yang Menahan Integrasi

10 keputusan TASK_INSTRUCTIONS §11 → usulan di `docs/decisions.md`
(menunggu persetujuan tim; tidak dianggap final).
