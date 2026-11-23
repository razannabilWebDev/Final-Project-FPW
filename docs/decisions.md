# Decision Log — SmartFood Home

> **STATUS: USULAN (DRAFT) — perlu persetujuan tim sebelum dianggap final.**
> Mencatat 10 keputusan wajib TASK_INSTRUCTIONS §11. Default diusulkan dari
> PRD (yang sudah memberi rekomendasi eksplisit) supaya diskusi tim fokus
> pada yang belum tertulis. Setiap keputusan final → update dokumen teknis
> terkait + tandai tanggal & persetujuan di sini.

| # | Keputusan | Usulan default | Sumber PRD | Status |
|---|---|---|---|---|
| D1 | Status pemicu pengurangan stok | **Saat transaksi dikonfirmasi/diterima** (masuk `processing`), sekali saja, idempotent, dalam DB transaction | §6.1 "default implementasi" | ⚠️ A3 (2026-11-23): dipakai di kontrak service; menunggu konfirmasi A4 |
| D2 | Status pendapatan final | **`completed`** — pending/processing tidak masuk pendapatan final | §6.1 rekomendasi default | ⏳ Usulan |
| D3 | Kebijakan stok tidak cukup | **Gagal terkontrol**: tampilkan bahan yang kurang + jumlah kekurangan; tidak pernah membuat stok negatif; tanpa deduct partial | §6.5 | ⚠️ A3 (2026-11-23): diterapkan via `InsufficientStockException`; menunggu konfirmasi tim |
| D4 | Metode valuasi harga modal bahan | **`average_cost`** (rata-rata) pada master bahan, di-update saat pembelian disetujui; HPP dihitung dari `average_cost` × takaran saat transaksi | implisit §7.1 (`average_cost`) | ⚠️ A3 (2026-11-23): rata-rata tertimbang diterapkan di `StockService::recordIn` |
| D5 | Konversi satuan bahan | Simpan resep dalam **satuan dasar bahan** (`base_unit`) — konversi kg→gram, liter→ml dilakukan saat input resep via faktor konversi; tabel `unit_conversions` bila perlu | §6.4 | ⚠️ A3 (2026-11-23): `base_unit` tanpa tabel konversi; menunggu konfirmasi A2 |
| D6 | Pembatalan & refund | Pembatalan setelah stok terpotong → **reversal stok sekali** (idempotent); refund dicatat sebagai penurunan pendapatan pada laporan (bukan penghapusan transaksi); transaksi tetap ada dengan status `cancelled` + history | §6.1 | ⚠️ A3 (2026-11-23): reversal dari ledger `sale_out`; menunggu konfirmasi A4 |
| D7 | Format snapshot HPP & resep | `sale_items.hpp_snapshot` = **HPP per porsi** (decimal 12,2) dari versi resep aktif saat transaksi dikonfirmasi; resep ber-versi (`recipes.version`), snapshot mereferensikan versi yang dipakai | §6.4, §7.1 | ⏳ Usulan — final Anggota 2 |
| D8 | Periode historis & metode prediksi | **Rata-rata bergerak harian per menu, window 28 hari**; hanya `sales.type=menu` & `status=completed`; penjualan manual dikecualikan; < 7 hari data → status "data terbatas"; parameter di `config/forecast.php` (bukan tabel `settings`, yang pemiliknya belum ditetapkan) | §6.9 | ✅ Disetujui Anggota 1 (2026-11-18) |
| D9 | Struktur kategori & data awal | Seed 8 kategori: pembelian bahan baku, listrik/air, gas, gaji, sewa, transportasi, perawatan, lainnya; Admin bisa tambah/ubah nama/nonaktifkan; terpakai → tidak boleh hard-delete | §6.8 | ⏳ Usulan — modul Anggota 4 |
| D10 | Struktur route/service lintas modul | Lihat `docs/architecture.md` §3 (konvensi nama) + kontrak service stok Anggota 3 di `docs/database.md` §5 | §11 | ⏳ Usulan |
| D11 | Kolom idempotensi penjualan | `stock_deducted_at` + `reversal_done_at` di tabel `sales` | `docs/database.md` usulan | ⚠️ Diterapkan sbg asumsi Anggota 1 (2026-11-18) — perlu konfirmasi A3 & A4 |
| D12 | Format `transaction_code` | `TRX-YYYYMMDD-XXXX`, unique | `docs/database.md` usulan | ⚠️ Diterapkan sbg asumsi Anggota 1 (2026-11-18) — perlu konfirmasi A4 |
| D13 | `settings` & `activity_logs` | Dibuat sesuai baseline; pemilik sementara Anggota 1 | `docs/database.md` §1 | ⚠️ Diterapkan, pemilik final belum ditetapkan tim |
| D14 | Alur lupa password (FR-AUTH-06) | Email via broker Laravel (dev `log`, test `array`, produksi SMTP); akun nonaktif ditolak dengan pesan generik; token 60 menit & sekali pakai | Persetujuan tim 2026-11-23; PRD §5.1 | ✅ Disetujui tim (2026-11-23) |
| D14 | Dependency ekspor laporan | `maatwebsite/excel` (Excel) + `barryvdh/laravel-dompdf` (PDF); gate `export-reports` (Admin) | PRD §2.1.14 | ✅ Disetujui tim (2026-11-23) |

## Catatan per keputusan (yang butuh koordinasi nyata)

- **D1 (dengan Anggota 3 & 4):** pemicu deduksi harus identik di service stok
  dan di alur status penjualan. Butuh mekanisme idempotensi yang disepakati
  (usulan kolom `stock_deducted_at` di `sales` — `docs/database.md`).
- **D2 (Anggota 4):** memengaruhi semua query laporan keuntungan.
- **D4–D5 (Anggota 2 & 3):** HPP hanya benar kalau satuan resep = satuan
  harga modal; ini kontrak paling rawan salah hitung. A3 sudah memakai
  `base_unit` tanpa tabel konversi dan rata-rata tertimbang; A2 perlu
  mengonversi takaran resep ke `base_unit` sebelum kontrak ditandatangani.
- **D1, D3, D6 (Anggota 3 & 4):** kontrak service stok memakai pemicu
  `processing`, gagal tanpa deduksi sebagian, dan reversal yang jumlahnya
  dibaca dari ledger. Flag idempotensi di `sales` tetap menjadi tanggung
  jawaban A4. Detail: `docs/architecture.md` §10.
- **D6 (Anggota 3 & 4):** reversal = kebalikan deduksi, lewat service yang
  sama, idempotent, dalam transaksi yang sama dengan perubahan status.
- **D8 (Anggota 1 & 4) — FINAL:** Anggota 4 menyediakan query penjualan
  `type=menu`, `status=completed` dengan tanggal `completed_at` dalam window;
  manual dikecualikan. Parameter `window_days=28` dan `min_days=7` di
  `config/forecast.php`. Kontrak ini dipakai saat modul prediksi dibangun.
- **D14 (Anggota 1) — FINAL:** Lupa password memakai broker bawaan Laravel.
  email tidak dikenal/akun nonaktif dibalas dengan pesan generik yang sama
  seperti email terdaftar; kredensial SMTP produksi disimpan di environment,
  bukan repository. Laravel 13/Symfony Mailer hanya menerima `MAIL_SCHEME=smtp`
  (port 587, STARTTLS) atau `smtps` (port 465) — **`tls` tidak didukung**.
  `APP_NAME` wajib `"SmartFood Home"` (di `.env`/`.env.example`): header mail
  Laravel menampilkan logo hanya jika nama aplikasi persis `Laravel`.
  Route reset sengaja `guest`; `password.update` tetap dipakai untuk ganti
  password setelah login, sehingga aksi reset memakai `password.reset.store`.

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
| D8 | Anggota 1 (2026-11-18), dikonfirmasi pengguna | 2026-11-18 |
| D9 | — | — |
| D10 | — | — |
| D11 | Anggota 1 (asumsi), dipakai adapter A4→A3 | 2026-11-18 / 2026-11-23 |
| D12 | Anggota 1 (asumsi implementasi) | 2026-11-18 |
| D13 | Anggota 1 (pemilik sementara) | 2026-11-18 |
| D14 | Anggota 1 + persetujuan tim | 2026-11-23 |
| D1, D3, D4, D5, D6 | Anggota 3 (usulan implementasi, belum final tim) | 2026-11-23 |
