# Permission Contract — SmartFood Home

> **STATUS: DRAFT — usulan Anggota 4 selaku konsumen. FINAL SAY: Anggota 1**
> (pemilik modul autentikasi/role, TASK_INSTRUCTIONS §3.1). Anggota lain
> menggunakan mekanisme Anggota 1 dan tidak membuat sistem role alternatif
> (§4.3). Implementasi tidak boleh dimulai sebelum dokumen ini disepakati.

## 1. Sumber Kebenaran

Matriks PRD §3.2. Prinsip: hak yang tidak tercantum = **tidak diberikan**
(default deny). Hak baru hanya lewat keputusan produk + pembaruan PRD.

## 2. Mekanisme (usulan implementasi)

- **Role** disimpan di kolom `users.role` (3 nilai: admin, pengelola, staf). Tanpa tabel role/perluasan — cukup enum sesuai PRD.
- **Pemeriksaan di backend**: middleware role untuk route group + Policy/Gate untuk aksi spesifik model.
- **Denial = HTTP 403** (atau redirect ke halaman "tanpa akses"), bukan sekadar menyembunyikan menu (PRD §3.2 catatan implementasi).
- UI hanya menyembunyikan menu sebagai kenyamanan; keamanan tetap di backend.

## 3. Usulan Middleware & Gate

Middleware (register oleh Anggota 1):

| Alias | Arti |
|---|---|
| `role:admin` | hanya Admin |
| `role:admin,pengelola` | Admin atau Pengelola |
| `auth` | semua role yang login (bawaan Laravel) |

Gate/Policy (usulan nama):

| Gate | Keterangan |
|---|---|
| `manage-users` | tambah/edit/nonaktifkan akun |
| `manage-menus` | CRUD menu, harga jual, foto produk |
| `manage-recipes` | takaran resep; harga modal resep = `admin,pengelola` |
| `view-recipes` | `admin,pengelola` (Staf tidak melihat resep — PRD §3.2) |
| `manage-ingredients` | master stok |
| `record-waste` | catat stok susut/rusak = `admin,staf` (Pengelola **tidak** — PRD §3.2) |
| `verify-purchases` | verifikasi pembelian = `admin,pengelola` |
| `create-purchases` | input nota belanja = `admin,staf` |
| `update-sale-status` | update status transaksi = semua role |
| `manage-expenses` | catat/kelola biaya operasional = **hanya Admin** |
| `view-reports` | dashboard keuangan & laporan laba = **hanya Admin** |
| `export-reports` | unduh/cetak laporan PDF/Excel = hanya Admin |
| `manage-forecast` | analisis & penyesuaian prediksi = hanya Admin |

## 4. Matriks → Gate (dari PRD §3.2)

| Modul / aksi | Admin | Pengelola | Staf | Gate/middleware usulan |
|---|:---:|:---:|:---:|---|
| Login, logout, ganti password | ✓ | ✓ | ✓ | `auth` |
| Tambah/edit/nonaktifkan akun | ✓ | — | — | `manage-users` |
| CRUD menu, harga jual, foto | ✓ | — | — | `manage-menus` |
| Lihat daftar menu | ✓ | ✓ | ✓ | `auth` |
| Atur takaran resep | ✓ | — | — | `manage-recipes` |
| Kelola harga modal resep | ✓ | ✓ | — | `manage-recipes` (admin,pengelola) |
| Melihat resep | ✓ | ✓ | — | `view-recipes` |
| Master stok bahan | ✓ | — | — | `manage-ingredients` |
| Memantau & mengelola persediaan | ✓ | ✓ | — | `manage-ingredients` (admin,pengelola) |
| Update stok fisik harian | ✓ | ✓ | ✓ | `auth` + policy stock adjustment |
| Catat stok susut/rusak | ✓ | — | ✓ | `record-waste` |
| Rekap pembelian | ✓ | ✓ | — | `verify-purchases` (view) |
| Verifikasi pembelian | ✓ | ✓ | — | `verify-purchases` |
| Input nota + foto struk | ✓ | — | ✓ | `create-purchases` |
| Rekap/analisis penjualan | ✓ | ✓ | — | policy `view-sales-summary` |
| Memantau pesanan masuk | ✓ | ✓ | — | policy `view-sales-summary` |
| Input pesanan masuk | ✓ | ✓ | ✓ | `auth` |
| Update status transaksi | ✓ | ✓ | ✓ | `update-sale-status` |
| Mencatat pengeluaran tetap | ✓ | — | — | `manage-expenses` |
| Mengategorikan jenis biaya | ✓ | — | — | `manage-expenses` |
| Melihat riwayat biaya | ✓ | — | — | `manage-expenses` |
| Dashboard keuangan | ✓ | — | — | `view-reports` |
| Menghitung laba bersih | ✓ | — | — | `view-reports` |
| Ekspor laporan PDF/Excel | ✓ | — | — | `export-reports` |
| Menganalisis estimasi produksi | ✓ | — | — | `manage-forecast` |
| Melihat estimasi porsi harian | ✓ | ✓ | ✓ | `auth` (read-only forecast) |

## 5. Aturan yang Mudah Terlewat (jebakan PRD §3.2)

1. **Pengelola tidak boleh** mengubah takaran resep, mengelola master bahan, atau mengelola akun — jangan beri izin implisit.
2. **Stok susut/rusak**: Admin + Staf, **bukan Pengelola**.
3. **Pengelola & Staf tidak boleh** menaikkan role sendiri atau mengubah permission dirinya sendiri.
4. Akun nonaktif tidak boleh login (dicek di proses autentikasi).
5. Anggota lain **tidak boleh** membuat sistem role alternatif; kebutuhan permission baru diajukan ke Anggota 1.
6. Semua aksi laporan (keuangan, laba, ekspor) adalah Admin-only — jaga modul Laporan Keuntungan (Anggota 4) tetap ketat.

## 6. Tugas Anggota 1 (tindak lanjut)

- [ ] Konfirmasi/ubah daftar middleware alias & gate di §3.
- [ ] Implementasi + seed role (koordinasi seed akun awal per §5.2).
- [ ] Umumkan ke tim begitu middleware tersedia supaya Anggota 2/3/4 bisa memasang di route masing-masing.
