# Decision Log — SmartFood Home

> **STATUS: USULAN (DRAFT) — perlu persetujuan tim sebelum dianggap final.**
> Mencatat 10 keputusan wajib TASK_INSTRUCTIONS §11. Default diusulkan dari
> PRD (yang sudah memberi rekomendasi eksplisit) supaya diskusi tim fokus
> pada yang belum tertulis. Setiap keputusan final → update dokumen teknis
> terkait + tandai tanggal & persetujuan di sini.

| # | Keputusan | Usulan default | Sumber PRD | Status |
|---|---|---|---|---|
| D1 | Status pemicu pengurangan stok | **Saat transaksi dikonfirmasi/diterima** (masuk `processing`), sekali saja, idempotent, dalam DB transaction | §6.1 "default implementasi" | ⏳ Usulan |
| D2 | Status pendapatan final | **`completed`** — pending/processing tidak masuk pendapatan final | §6.1 rekomendasi default | ⏳ Usulan |
| D3 | Kebijakan stok tidak cukup | **Gagal terkontrol**: tampilkan bahan yang kurang + jumlah kekurangan; tidak pernah membuat stok negatif; tanpa deduct partial | §6.5 | ⏳ Usulan |
| D4 | Metode valuasi harga modal bahan | **`average_cost`** (rata-rata) pada master bahan, di-update saat pembelian disetujui; HPP dihitung dari `average_cost` × takaran saat transaksi | implisit §7.1 (`average_cost`) | ⏳ Usulan |
| D5 | Konversi satuan bahan | Simpan resep dalam **satuan dasar bahan** (`base_unit`) — konversi kg→gram, liter→ml dilakukan saat input resep via faktor konversi; tabel `unit_conversions` bila perlu | §6.4 | ⏳ Usulan — final bersama Anggota 2 & 3 |
| D6 | Pembatalan & refund | Pembatalan setelah stok terpotong → **reversal stok sekali** (idempotent); refund dicatat sebagai penurunan pendapatan pada laporan (bukan penghapusan transaksi); transaksi tetap ada dengan status `cancelled` + history | §6.1 | ⏳ Usulan |
| D7 | Format snapshot HPP & resep | `sale_items.hpp_snapshot` = **HPP per porsi** (decimal 12,2) dari versi resep aktif saat transaksi dikonfirmasi; resep ber-versi (`recipes.version`), snapshot mereferensikan versi yang dipakai | §6.4, §7.1 | ⏳ Usulan — final Anggota 2 |
| D8 | Periode historis & metode prediksi | **Rata-rata bergerak harian per menu, window 28 hari** (diatur di `settings`/config); penjualan manual dikecualikan; data kurang → tampilkan status "data terbatas" | §6.9 | ⏳ Usulan — final Anggota 1 |
| D9 | Struktur kategori & data awal | Seed 8 kategori: pembelian bahan baku, listrik/air, gas, gaji, sewa, transportasi, perawatan, lainnya; Admin bisa tambah/ubah nama/nonaktifkan; terpakai → tidak boleh hard-delete | §6.8 | ⏳ Usulan — modul Anggota 4 |
| D10 | Struktur route/service lintas modul | Lihat `docs/architecture.md` §3 (konvensi nama) + kontrak service stok Anggota 3 di `docs/database.md` §5 | §11 | ⏳ Usulan |

## Catatan per keputusan (yang butuh koordinasi nyata)

- **D1 (dengan Anggota 3 & 4):** pemicu deduksi harus identik di service stok
  dan di alur status penjualan. Butuh mekanisme idempotensi yang disepakati
  (usulan kolom `stock_deducted_at` di `sales` — `docs/database.md`).
- **D2 (Anggota 4):** memengaruhi semua query laporan keuntungan.
- **D4–D5 (Anggota 2 & 3):** HPP hanya benar kalau satuan resep = satuan
  harga modal; ini kontrak paling rawan salah hitung.
- **D6 (Anggota 3 & 4):** reversal = kebalikan deduksi, lewat service yang
  sama, idempotent, dalam transaksi yang sama dengan perubahan status.
- **D8 (Anggota 1 & 4):** Anggota 4 menyediakan data penjualan; pastikan
  format & pengecualian penjualan manual jelas sejak awal.

## Tanda tangan keputusan

| Keputusan | Disetujui oleh | Tanggal |
|---|---|---|
| D1 | — | — |
| D2 | — | — |
| D3 | — | — |
| D4 | — | — |
| D5 | — | — |
| D6 | — | — |
| D7 | — | — |
| D8 | — | — |
| D9 | — | — |
| D10 | — | — |
