# Permission Contract — SmartFood Home

> **STATUS: FINAL (2026-11-18) — ditetapkan Anggota 1** (pemilik modul
> autentikasi/role, TASK_INSTRUCTIONS §3.1). Anggota lain wajib menggunakan
> mekanisme ini dan tidak membuat sistem role alternatif (§4.3).

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

Middleware tambahan (Anggota 1): `active` — memeriksa `users.is_active`;
akun yang dinonaktifkan di-logout otomatis dan diarahkan ke halaman login.

Gate final — didefinisikan terpusat di `app/Providers/AuthServiceProvider.php`:

| Gate | Role diizinkan | Keterangan |
|---|---|---|
| `manage-users` | Admin | tambah/edit/nonaktifkan akun |
| `manage-menus` | Admin | CRUD menu, harga jual, foto produk |
| `manage-recipes` | Admin | takaran & komposisi resep |
| `manage-recipe-costs` | Admin, Pengelola | harga modal resep (**gate baru**, koordinasi A2) |
| `view-recipes` | Admin, Pengelola | Staf tidak melihat resep — PRD §3.2 |
| `manage-ingredients` | Admin | master bahan baku |
| `manage-stock` | Admin, Pengelola | memantau/mengelola persediaan (**gate baru**, koordinasi A3) |
| `update-stock-physical` | semua role | update stok fisik harian |
| `record-waste` | Admin, Staf | Pengelola **tidak** — PRD §3.2 |
| `create-purchases` | Admin, Staf | input nota belanja & foto struk |
| `verify-purchases` | Admin, Pengelola | verifikasi pembelian |
| `view-sales-summary` | Admin, Pengelola | rekap/analisis & pesanan masuk (**gate baru**, koordinasi A4) |
| `create-sales` | semua role | input pesanan/penjualan |
| `update-sale-status` | semua role | update status transaksi |
| `manage-expenses` | Admin | catat/kelola biaya operasional |
| `view-reports` | Admin | dashboard keuangan & laporan laba |
| `export-reports` | Admin | unduh/cetak laporan PDF/Excel |
| `manage-forecast` | Admin | analisis & penyesuaian prediksi |
| `view-forecast` | semua role | lihat estimasi porsi harian (read-only, **gate baru**) |

Catatan: gate yang berbasis role juga menolak akun nonaktif (`is_active = false`).
`can:manage-users` dipakai pada route group `users.*`; gate lain dipasang oleh
pemilik modul masing-masing melalui `can:` atau `@can`.

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

## 6. Tugas Anggota 1 (status)

- [x] Konfirmasi/ubah daftar middleware alias & gate di §3 (2026-11-18).
- [x] Implementasi middleware `role`, `active`, dan ke-19 gate di `AuthServiceProvider`.
- [ ] Seed akun Admin awal — **koordinasi seed akun awal per §5.2 masih terbuka**.
- [x] Umumkan ke tim: alias `role`/`active` dan gate di §3 siap dipakai Anggota 2/3/4.
- [ ] Anggota 2/3/4: pasang gate pada route modul masing-masing; laporkan bila butuh gate baru.
- [x] FR-AUTH-06/D14: route lupa password (`password.request`, `password.email`, `password.reset`, `password.reset.store`) sengaja `guest`; tidak menambah gate baru.
- [x] Audit log A1 (D13): `LogActivityAction` dipakai `UserService`/`ChangePasswordAction`; A3/A4 menunggu koordinasi pemilik final.
- [x] Rekomendasi produksi: `forecast.index` memakai `view-forecast` (semua role), `forecast.generate`/`forecast.adjust` memakai `manage-forecast` (Admin), diuji 403untuk Pengelola/Staf.
