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
- [x] Master bahan baku + satuan dasar (Anggota 3 — 2026-11-23; tanpa tabel konversi, D5 menunggu A2)
- [x] Service stok terpusat + `stock_movements` ledger (Anggota 3 — 2026-11-23; deduct/reversal untuk A4)
- [x] Migration semua tabel dari `docs/database.md`, urut dependensi FK (Anggota 1, 2026-11-18)
- [x] Seeder dummy lengkap & idempoten + test konsistensi ledger (`DatabaseSchemaTest`, 40 test lulus)
- [ ] Layout dasar + komponen umum (card, badge, DataTable, modal, toast)

## Tahap 2 — Pengembangan Paralel (boleh mulai setelah kontrak disepakati)

- **Anggota 1:** kelola pengguna, rekomendasi produksi
- **Anggota 2:** resep & HPP per porsi, snapshot/versi resep
- **Anggota 3:** pembelian + verifikasi → stok masuk, stok susut/rusak, penyesuaian stok fisik
  - [x] Fondasi selesai (CRUD bahan, ledger, penyesuaian, susut/rusak, service stok, 21 test baru)
  - [ ] Pembelian + verifikasi masih tertunda; koordinasi nav Staf dengan Anggota 1
- **Anggota 4 (modul kami):**
  - [ ] Pencatatan penjualan: transaksi berbasis menu (multi-item) + penjualan manual
  - [ ] Update status transaksi + `sale_status_histories`
  - [ ] Deduksi stok saat status pemicu (panggil service Anggota 3) + reversal saat pembatalan
  - [ ] Biaya operasional: kategori + pengeluaran
  - [ ] Laporan keuntungan: pendapatan bersih − HPP (snapshot) − biaya operasional; penjualan manual ditandai "HPP tidak tersedia" (bukan 0)
  - [ ] Ekspor PDF/Excel (setelah dependency disepakati)
  - Boleh memakai stub/kontrak data bila dependensi (menu/HPP/service stok) belum siap (§8 Tahap 2)

## Tahap 3 — Integrasi (TASK_INSTRUCTIONS §8)

1. [ ] Auth & permission di semua modul
2. [ ] Menu & resep ↔ master bahan
3. [ ] Penjualan ↔ menu, HPP, service stok
4. [ ] Pembelian ↔ stok masuk
5. [ ] Biaya & penjualan → laporan keuntungan
6. [ ] Penjualan + resep + stok → rekomendasi produksi

## Tahap 4 — Pengujian Sistem

- [ ] Login 3 role; pembatasan akses (403)
- [ ] Transaksi multi-item → stok berkurang sesuai resep
- [ ] Pembatalan → reversal stok (idempotent)
- [ ] Pembelian → verifikasi → stok masuk
- [ ] Biaya operasional & laporan keuntungan benar
- [ ] Rekomendasi produksi (abaikan penjualan manual)

## Tahap 5 — Stabilitas & Dokumentasi

- [ ] Perbaikan bug integrasi; seluruh test lulus
- [ ] Keamanan permission & upload; responsive
- [ ] README + dokumentasi teknis; backup/deployment

## Keputusan yang Menahan Integrasi

10 keputusan TASK_INSTRUCTIONS §11 → usulan di `docs/decisions.md`
(menunggu persetujuan tim; tidak dianggap final).
