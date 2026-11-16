# TASK INSTRUCTIONS

# SmartFood Home --- Pembagian Tugas Tim 4 Anggota

> **Versi:** 1.0\
> **Tanggal:** 16 November 2026\
> **Dokumen acuan utama:** `prd.md`\
> **Stack:** Laravel, MySQL, Blade, Tailwind CSS\
> **Platform:** Web responsive\
> **Target:** UMKM makanan rumahan

------------------------------------------------------------------------

## 1. Tujuan Dokumen

Dokumen ini menetapkan pembagian tanggung jawab, batas kepemilikan
modul, aturan kolaborasi, kontrak integrasi, dan instruksi kerja untuk
empat anggota tim SmartFood Home.

Setiap anggota bertanggung jawab mengembangkan modul yang ditetapkan di
bawah. Seluruh anggota tetap wajib membaca `prd.md` dan mengikuti
arsitektur, business rules, role, permission, serta acceptance criteria
yang ada di dalamnya.

**Urutan prioritas jika terjadi perbedaan instruksi:** 1. Keputusan
terbaru yang disepakati seluruh tim dan dicatat dalam dokumentasi. 2.
`prd.md` sebagai spesifikasi kebutuhan produk. 3. `TASK_INSTRUCTIONS.md`
ini sebagai pembagian kepemilikan pekerjaan. 4. `AGENTS.md` sebagai
aturan teknis umum agent. 5. Instruksi individual anggota.

Jangan mengubah kebutuhan produk atau hak akses secara sepihak.

------------------------------------------------------------------------

## 2. Pembagian Tugas Resmi

  ------------------------------------------------------------------------
  Anggota               Modul                                       Jumlah
  --------------------- --------------------- ----------------------------
  Anggota 1             Autentikasi; Kelola                              3
                        Pengguna; Rekomendasi 
                        Produksi              

  Anggota 2             Pengelolaan Menu;                                2
                        Pengelolaan Resep     

  Anggota 3             Pengelolaan Stok;                                2
                        Pembelian Bahan Baku  

  Anggota 4             Pencatatan Penjualan;                            3
                        Biaya Operasional;    
                        Laporan Keuntungan    
  ------------------------------------------------------------------------

Pembagian ini adalah kepemilikan implementasi utama. Modul yang saling
terhubung tetap harus diintegrasikan melalui kontrak yang disepakati,
bukan dikembangkan sebagai sistem terpisah.

------------------------------------------------------------------------

## 3. Tanggung Jawab Per Anggota

### 3.1 Anggota 1 --- Autentikasi, Kelola Pengguna, Rekomendasi Produksi

**Modul yang dimiliki:** 1. Autentikasi. 2. Kelola Pengguna. 3.
Rekomendasi Produksi.

**Tanggung jawab Autentikasi:** - Implementasi login dan logout untuk
Admin, Pengelola, dan Staf Operasional. - Implementasi ganti password. -
Validasi kredensial dan status akun aktif. - Manajemen session dan
perlindungan endpoint terautentikasi. - Pengalihan pengguna ke dashboard
sesuai role. - Menyediakan middleware, policy, gate, atau mekanisme
otorisasi yang disepakati tim.

**Tanggung jawab Kelola Pengguna:** - Admin dapat menambahkan akun
Pengelola dan Staf Operasional. - Admin dapat mengedit data pengguna. -
Admin dapat menonaktifkan akun. - Menjaga agar pengguna tidak dapat
mengubah role atau permission dirinya sendiri. - Menjaga agar akun
nonaktif tidak dapat login. - Menyediakan data pengguna untuk kebutuhan
audit aktivitas.

**Tanggung jawab Rekomendasi Produksi:** - Mengolah riwayat penjualan
berbasis menu untuk menghasilkan estimasi jumlah porsi harian. -
Menggunakan pendekatan statistik, misalnya rata-rata bergerak, sesuai
keputusan implementasi yang disepakati. - Menampilkan status kecukupan
data dan periode historis yang digunakan. - Memungkinkan Admin
menyesuaikan hasil prediksi secara manual. - Menyimpan nilai prediksi
awal dan nilai setelah penyesuaian. - Menghasilkan estimasi kebutuhan
bahan berdasarkan resep dan takaran dari modul Anggota 2. - Menampilkan
estimasi jumlah porsi kepada Admin, Pengelola, dan Staf sesuai
permission. - Tidak menggunakan penjualan manual sebagai data permintaan
menu tertentu.

**Output yang harus disediakan:** - Sistem autentikasi berfungsi. - Role
dan permission dapat digunakan oleh seluruh modul. - CRUD akun Pengelola
dan Staf oleh Admin. - Halaman/komponen rekomendasi produksi. - Service
prediksi dan hasil prediksi yang terdokumentasi. - Test autentikasi,
permission, dan prediksi.

**Bukan tanggung jawab utama Anggota 1:** - CRUD menu dan resep. -
Pengelolaan stok aktual. - Input transaksi penjualan dan biaya
operasional. - Perhitungan laporan keuangan utama.

### 3.2 Anggota 2 --- Pengelolaan Menu dan Pengelolaan Resep

**Modul yang dimiliki:** 1. Pengelolaan Menu. 2. Pengelolaan Resep.

**Tanggung jawab Pengelolaan Menu:** - Admin dapat menambah, mengedit,
dan menghapus secara aman atau menonaktifkan menu. - Admin dapat
menentukan harga jual. - Admin dapat mengunggah foto produk. - Admin,
Pengelola, dan Staf dapat melihat daftar menu sesuai permission. -
Menjaga histori transaksi ketika menu diubah atau dinonaktifkan. -
Menyediakan data menu aktif untuk modul penjualan Anggota 4.

**Tanggung jawab Pengelolaan Resep:** - Admin dapat membuat dan mengatur
takaran resep. - Admin dapat menambah/mengubah bahan dan kuantitas per
porsi. - Admin dan Pengelola dapat melihat resep terbatas sesuai PRD. -
Admin dan Pengelola dapat mengelola harga modal resep sesuai batas
permission pada PRD. - Menghitung estimasi HPP bahan per porsi
berdasarkan takaran resep dan harga modal bahan. - Menyimpan
versi/snapshot resep atau data biaya yang diperlukan agar histori
transaksi tidak berubah saat resep diperbarui. - Menyediakan data
kebutuhan bahan per menu untuk modul stok dan rekomendasi produksi.

**Output yang harus disediakan:** - CRUD menu. - CRUD resep dan detail
takaran. - Perhitungan HPP per porsi. - Service/query untuk mengambil
komposisi resep per menu. - Data menu dan resep yang dapat digunakan
oleh Anggota 3, Anggota 4, dan modul prediksi Anggota 1. - Test menu,
resep, permission, dan kalkulasi HPP.

**Bukan tanggung jawab utama Anggota 2:** - Menyimpan transaksi
penjualan. - Mengubah stok aktual secara langsung. - Memverifikasi
pembelian bahan baku. - Membuat laporan keuntungan akhir.

### 3.3 Anggota 3 --- Pengelolaan Stok dan Pembelian Bahan Baku

**Modul yang dimiliki:** 1. Pengelolaan Stok. 2. Pembelian Bahan Baku.

**Tanggung jawab Pengelolaan Stok:** - Admin mengelola master bahan
baku. - Admin dan Pengelola memantau serta mengelola persediaan. -
Admin, Pengelola, dan Staf dapat memperbarui stok fisik harian sesuai
PRD. - Admin dan Staf dapat mencatat stok susut/rusak. - Menyediakan
histori pergerakan stok (stock movement). - Mengelola stok minimum dan
peringatan stok menipis. - Menyediakan service terpusat untuk stok
masuk, stok keluar, penyesuaian, dan reversal. - Menangani pengurangan
stok otomatis yang dipicu transaksi penjualan Anggota 4 berdasarkan
resep Anggota 2.

**Tanggung jawab Pembelian Bahan Baku:** - Staf dapat menginput nota
belanja harian. - Staf dapat mengunggah foto struk/nota. - Admin dan
Pengelola dapat melihat rekap pembelian. - Admin dan Pengelola dapat
memverifikasi atau menolak pembelian. - Pembelian yang disetujui
menambah stok. - Pembelian yang ditolak tidak mengubah stok. - Mencegah
persetujuan pembelian diproses lebih dari satu kali. - Menyimpan
pengguna, waktu, dan alasan verifikasi/penolakan.

**Output yang harus disediakan:** - Master bahan baku. - Pengelolaan
stok dan stock movement ledger. - Form penyesuaian stok dan stok
susut/rusak. - Input dan verifikasi pembelian. - Service stok yang aman
untuk dipanggil modul penjualan. - Informasi stok dan kebutuhan bahan
yang dapat dipakai modul rekomendasi. - Test stok, pembelian,
verifikasi, dan proses stok otomatis.

**Bukan tanggung jawab utama Anggota 3:** - CRUD menu dan resep. -
Membuat transaksi penjualan. - Mengelola biaya operasional. - Menyusun
dashboard laporan keuntungan.

### 3.4 Anggota 4 --- Pencatatan Penjualan, Biaya Operasional, Laporan Keuntungan

**Modul yang dimiliki:** 1. Pencatatan Penjualan. 2. Biaya Operasional.
3. Laporan Keuntungan.

**Tanggung jawab Pencatatan Penjualan:** - Admin, Pengelola, dan Staf
dapat menginput pesanan/penjualan. - Satu transaksi dapat berisi
beberapa menu. - Menyediakan mode penjualan manual berdasarkan nominal
pendapatan. - Menghitung subtotal dan total transaksi. - Mencatat metode
pembayaran: tunai, transfer, atau QRIS. - Menyediakan pemantauan pesanan
dan pembaruan status transaksi. - Menyimpan snapshot nama menu, harga
jual, dan HPP saat transaksi dikonfirmasi. - Memanggil service resep
dari Anggota 2 untuk mengetahui kebutuhan bahan. - Memanggil service
stok Anggota 3 untuk memproses pengurangan stok secara atomik. -
Menangani pembatalan melalui proses reversal stok yang disediakan
Anggota 3. - Memastikan perubahan status tidak menyebabkan pengurangan
stok ganda.

**Tanggung jawab Biaya Operasional:** - Admin dapat mencatat pengeluaran
tetap/operasional. - Admin dapat membuat dan mengelola kategori biaya. -
Admin dapat melihat riwayat biaya. - Menyimpan tanggal, nominal,
kategori, keterangan, dan pengguna pencatat.

**Tanggung jawab Laporan Keuntungan:** - Menampilkan dashboard keuangan
khusus Admin. - Menghitung pendapatan bersih penjualan, HPP, laba kotor,
biaya operasional, dan laba bersih operasional. - Menyediakan filter
periode. - Menyediakan ringkasan harian/bulanan dan menu terlaris jika
data tersedia. - Menyediakan ekspor/cetak PDF dan Excel. - Memisahkan
penjualan berbasis menu dan penjualan manual. - Menandai HPP penjualan
manual sebagai tidak tersedia, bukan menganggapnya nol. - Menggunakan
snapshot HPP pada detail transaksi agar laporan historis konsisten.

**Output yang harus disediakan:** - Modul penjualan multi-item dan
manual. - Status transaksi dan riwayat perubahan. - Modul biaya
operasional dan kategori biaya. - Dashboard laporan keuntungan. - Ekspor
PDF dan Excel. - Test penjualan, pembatalan, biaya, kalkulasi laba, dan
laporan.

**Bukan tanggung jawab utama Anggota 4:** - Mengelola master menu dan
resep. - Mengelola master stok dan proses pembelian. - Mengelola akun
serta autentikasi. - Mengembangkan algoritma prediksi produksi.

------------------------------------------------------------------------

## 4. Batas Kepemilikan dan Kontrak Integrasi

Modul SmartFood Home saling bergantung. Setiap anggota boleh
berkontribusi pada integrasi, tetapi perubahan inti pada modul milik
anggota lain harus dikoordinasikan dengan pemilik modul.

### 4.1 Peta Dependensi

  -----------------------------------------------------------------------
  Fitur                   Pemilik utama           Bergantung pada
  ----------------------- ----------------------- -----------------------
  Autentikasi dan role    Anggota 1               ---

  Kelola pengguna         Anggota 1               Autentikasi

  Menu                    Anggota 2               Permission Anggota 1

  Resep dan HPP bahan     Anggota 2               Menu dan master bahan
                                                  Anggota 3

  Master stok dan stock   Anggota 3               Permission Anggota 1
  movement                                        

  Pembelian bahan         Anggota 3               Master bahan

  Penjualan               Anggota 4               Menu Anggota 2,
                                                  permission Anggota 1

  Pengurangan stok saat   Anggota 3 sebagai       Resep Anggota 2 dan
  penjualan               pemilik service stok;   transaksi Anggota 4
                          dipicu Anggota 4        

  Biaya operasional       Anggota 4               Permission Anggota 1

  Laporan keuntungan      Anggota 4               Penjualan Anggota 4,
                                                  HPP Anggota 2, biaya
                                                  Anggota 4

  Rekomendasi produksi    Anggota 1               Penjualan Anggota 4,
                                                  resep Anggota 2, stok
                                                  Anggota 3
  -----------------------------------------------------------------------

### 4.2 Kontrak Integrasi yang Wajib Disepakati

Sebelum implementasi modul yang saling bergantung, anggota terkait harus
menyepakati:

1.  Nama tabel dan kolom database.
2.  Relasi model dan foreign key.
3.  Nama route dan format parameter.
4.  Bentuk data request/response atau service method.
5.  Aturan permission.
6.  Waktu proses bisnis dijalankan.
7.  Penanganan error dan rollback.
8.  Test integrasi minimal.

Kontrak harus dicatat dalam `docs/architecture.md`, `docs/database.md`,
atau dokumen teknis terkait.

### 4.3 Aturan Integrasi Antaranggota

**Anggota 1 ↔ Semua anggota** - Anggota 1 menyediakan role, middleware,
policy/gate, dan aturan autentikasi. - Anggota lain menggunakan
mekanisme tersebut dan tidak membuat sistem role alternatif. - Jika
diperlukan permission baru, ajukan dan sepakati sebelum implementasi.

**Anggota 2 ↔ Anggota 3** - Anggota 2 menentukan kebutuhan bahan dan
takaran resep. - Anggota 3 menyediakan master bahan baku dan satuan
dasar. - Keduanya harus menyepakati format satuan dan konversi (misalnya
kg ke gram, liter ke ml). - Anggota 2 tidak mengubah saldo stok secara
langsung.

**Anggota 2 ↔ Anggota 4** - Anggota 2 menyediakan data menu aktif, harga
jual, resep, dan HPP. - Anggota 4 menyimpan snapshot harga dan HPP pada
transaksi. - Perubahan harga/menu/resep tidak boleh mengubah histori
transaksi yang telah tersimpan.

**Anggota 3 ↔ Anggota 4** - Anggota 3 menyediakan service untuk
mengurangi dan mengembalikan stok. - Anggota 4 memanggil service
tersebut pada titik status transaksi yang telah disepakati. -
Pengurangan stok harus idempotent dan berada dalam database transaction
yang konsisten. - Anggota 4 tidak boleh mengurangi saldo stok dengan
mengubah kolom stok secara langsung.

**Anggota 1 ↔ Anggota 2, 3, dan 4 untuk Rekomendasi** - Anggota 1
mengambil data penjualan dari Anggota 4. - Anggota 1 mengambil komposisi
resep dari Anggota 2. - Anggota 1 mengambil informasi persediaan dari
Anggota 3. - Prediksi harus mengabaikan transaksi manual ketika
menghitung permintaan per menu. - Penyesuaian manual prediksi hanya
dapat dilakukan role yang diizinkan PRD.

------------------------------------------------------------------------

## 5. Aturan Database dan Kepemilikan Schema

Database adalah milik bersama. Tidak ada anggota yang boleh membuat
perubahan schema yang memengaruhi modul lain tanpa koordinasi.

### 5.1 Kepemilikan Area Data

  -----------------------------------------------------------------------
  Area data                           Penanggung jawab utama
  ----------------------------------- -----------------------------------
  Users, role, status akun            Anggota 1

  Production forecasts dan forecast   Anggota 1
  items                               

  Menus, recipes, recipe items        Anggota 2

  Ingredients dan stock movements     Anggota 3

  Purchases dan purchase items        Anggota 3

  Sales, sale items, status histories Anggota 4

  Expense categories dan expenses     Anggota 4

  Integrasi activity log/settings     Disepakati bersama; pemilik
                                      perubahan harus ditentukan sebelum
                                      dibuat
  -----------------------------------------------------------------------

### 5.2 Aturan Migration

-   Setiap anggota membuat migration untuk tabel yang menjadi tanggung
    jawabnya.
-   Nama migration harus jelas dan mengikuti konvensi Laravel.
-   Jangan mengedit migration yang sudah digunakan bersama atau sudah
    masuk branch utama tanpa koordinasi.
-   Perubahan struktur tabel bersama harus diumumkan sebelum merge.
-   Pastikan migration dapat dijalankan dari database kosong.
-   Jangan menggunakan migration untuk menghapus data produksi.
-   Seed data role dan akun awal harus dikoordinasikan dengan Anggota 1.
-   Semua anggota wajib menguji migration setelah menarik perubahan
    terbaru.

### 5.3 Aturan Data Historis

-   Jangan menghapus transaksi, pembelian, pengeluaran, atau stock
    movement yang sudah memiliki relasi.
-   Gunakan status nonaktif/soft delete untuk data master jika histori
    masih menggunakannya.
-   Gunakan snapshot pada detail transaksi untuk nilai yang dapat
    berubah seperti nama menu, harga jual, dan HPP.
-   Gunakan database transaction untuk operasi lintas tabel.

------------------------------------------------------------------------

## 6. Aturan Git dan Kolaborasi

### 6.1 Struktur Branch

Gunakan satu repository GitHub bersama.

Contoh branch:

``` text
main
├── feature/member-1-auth-users-forecast
├── feature/member-2-menu-recipes
├── feature/member-3-inventory-purchases
└── feature/member-4-sales-expenses-reports
```

Nama branch boleh dipecah lagi per fitur, misalnya: -
`feature/auth-login` - `feature/menu-crud` - `feature/stock-movements` -
`feature/sales-multi-item`

### 6.2 Aturan Commit

Gunakan pesan commit yang menjelaskan perubahan, misalnya:

``` text
feat(auth): implement role-based login
feat(menu): add menu management
feat(stock): add stock movement service
feat(sales): support multi-item transactions
fix(report): correct net profit calculation
test(purchase): cover purchase verification
docs: update database integration contract
```

### 6.3 Pull Request dan Merge

Sebelum merge ke `main`: - Fitur sudah sesuai PRD. - Migration dan test
telah dijalankan. - Tidak ada perubahan tak sengaja pada modul anggota
lain. - Permission diuji di backend. - Perubahan kontrak integrasi
didokumentasikan. - Minimal satu anggota lain melakukan review. -
Konflik migration dan file bersama sudah diselesaikan.

Hindari commit langsung ke `main`, kecuali ada kesepakatan khusus tim.

### 6.4 File yang Tidak Boleh Diubah Sepihak

Perubahan berikut memerlukan koordinasi: - `composer.json` dan
`composer.lock`. - `package.json` dan lockfile frontend. - Konfigurasi
autentikasi dan middleware global. - Route utama yang digunakan lintas
modul. - Layout utama dan navigasi bersama. - Migration yang menjadi
dependensi modul lain. - Dokumen PRD dan matriks permission. - File
konfigurasi environment dan deployment.

------------------------------------------------------------------------

## 7. Aturan Kerja OpenCode untuk Setiap Anggota

Setiap anggota yang menggunakan OpenCode wajib memberikan konteks
tugasnya.

### 7.1 Prompt Awal Anggota

Gunakan template berikut dan ganti nomor anggota serta modulnya:

``` text
Baca dan pahami file berikut:
- prd.md
- AGENTS.md
- TASK_INSTRUCTIONS.md
- docs/architecture.md (jika tersedia)
- docs/database.md (jika tersedia)
- docs/permissions.md (jika tersedia)

Saya adalah Anggota [NOMOR].

Modul yang menjadi tanggung jawab saya:
[TULISKAN MODUL SESUAI PEMBAGIAN RESMI]

Ikuti aturan berikut:
1. Kerjakan hanya modul yang menjadi tanggung jawab saya.
2. Ikuti PRD sebagai sumber kebutuhan produk.
3. Periksa implementasi repository sebelum mengubah kode.
4. Jangan membuat ulang proyek atau mengganti stack.
5. Jangan mengubah modul anggota lain tanpa koordinasi.
6. Jangan mengubah schema bersama tanpa menyepakati dampaknya.
7. Gunakan service/kontrak integrasi yang telah disepakati.
8. Terapkan validasi dan authorization di backend.
9. Buat atau perbarui test untuk setiap fitur.
10. Jangan menyatakan fitur selesai jika test terkait belum dijalankan.

Sebelum coding:
- Audit struktur dan kondisi modul.
- Identifikasi dependensi dengan anggota lain.
- Laporkan file yang akan dibuat/diubah.
- Buat rencana implementasi bertahap.
- Tandai keputusan yang belum jelas dan minta klarifikasi.

Setelah coding:
- Jalankan test yang relevan.
- Laporkan file yang berubah.
- Laporkan migration baru.
- Laporkan kontrak integrasi yang perlu digunakan anggota lain.
- Laporkan test yang lulus/gagal dan pekerjaan tersisa.
```

### 7.2 Konteks Per Anggota

**Anggota 1:**

``` text
Nomor anggota: 1
Fokus: Autentikasi, Kelola Pengguna, Rekomendasi Produksi
Prioritas dependensi: Menyediakan fondasi role dan permission lebih awal.
Koordinasi utama: Semua anggota untuk authorization; Anggota 2, 3, dan 4 untuk prediksi.
```

**Anggota 2:**

``` text
Nomor anggota: 2
Fokus: Pengelolaan Menu, Pengelolaan Resep
Prioritas dependensi: Menyepakati master bahan dan satuan dengan Anggota 3.
Koordinasi utama: Anggota 3 untuk bahan baku; Anggota 4 untuk penjualan dan snapshot HPP.
```

**Anggota 3:**

``` text
Nomor anggota: 3
Fokus: Pengelolaan Stok, Pembelian Bahan Baku
Prioritas dependensi: Menyediakan master bahan dan service stock movement.
Koordinasi utama: Anggota 2 untuk resep/satuan; Anggota 4 untuk pengurangan dan reversal stok.
```

**Anggota 4:**

``` text
Nomor anggota: 4
Fokus: Pencatatan Penjualan, Biaya Operasional, Laporan Keuntungan
Prioritas dependensi: Menyepakati struktur menu/HPP dengan Anggota 2 dan service stok dengan Anggota 3.
Koordinasi utama: Anggota 1 untuk permission; Anggota 2 untuk harga/HPP; Anggota 3 untuk stok.
```

------------------------------------------------------------------------

## 8. Urutan Kerja Tim yang Disarankan

### Tahap 1 --- Fondasi Bersama

Dikerjakan dan disepakati sebelum semua anggota bekerja paralel: -
Struktur repository dan cara menjalankan proyek. - Versi Laravel/PHP dan
dependency. - Konfigurasi database. - Struktur role dan permission. -
Konvensi penamaan tabel, model, route, dan service. - Layout dasar dan
pola response. - Kontrak data awal untuk menu, bahan, resep, dan
transaksi.

**Pemimpin koordinasi:** Anggota 1 untuk autentikasi/permission, dengan
persetujuan seluruh anggota untuk keputusan lintas modul.

### Tahap 2 --- Pengembangan Paralel

Setelah kontrak awal tersedia: - Anggota 1 mengembangkan autentikasi dan
kelola pengguna. - Anggota 2 mengembangkan menu dan resep. - Anggota 3
mengembangkan master stok dan pembelian. - Anggota 4 mengembangkan
pencatatan penjualan, biaya, dan laporan dengan mock/stub atau kontrak
data yang telah disepakati bila modul dependensi belum siap.

### Tahap 3 --- Integrasi

Lakukan integrasi secara bertahap: 1. Integrasikan autentikasi dan
permission ke semua modul. 2. Integrasikan menu dan resep dengan master
bahan. 3. Integrasikan penjualan dengan menu, HPP, dan service stok. 4.
Integrasikan pembelian dengan stok masuk. 5. Integrasikan biaya dan
penjualan ke laporan keuntungan. 6. Integrasikan data penjualan, resep,
dan stok ke rekomendasi produksi.

### Tahap 4 --- Pengujian Sistem

Uji alur end-to-end: - Login untuk tiga role. - Pembatasan akses setiap
role. - Membuat menu dan resep. - Mencatat pembelian, verifikasi, dan
stok masuk. - Mencatat transaksi multi-item. - Memastikan stok berkurang
sesuai resep. - Membatalkan transaksi dan memastikan stok direversal. -
Mencatat biaya operasional. - Memeriksa laporan keuntungan. -
Menghasilkan dan menyesuaikan rekomendasi produksi.

### Tahap 5 --- Stabilitas dan Dokumentasi

-   Perbaiki bug hasil integrasi.
-   Jalankan seluruh test.
-   Periksa keamanan permission dan upload.
-   Pastikan tampilan responsive.
-   Perbarui README dan dokumentasi teknis.
-   Siapkan prosedur backup dan deployment.

------------------------------------------------------------------------

## 9. Definition of Done Tim

Pekerjaan anggota dianggap selesai apabila: - \[ \] Fitur sesuai ruang
lingkup modul dan PRD. - \[ \] Tidak melanggar permission role. - \[ \]
Validasi dilakukan di backend. - \[ \] UI responsive dan memiliki
kondisi loading, kosong, sukses, serta error yang sesuai. - \[ \] Proses
penting memiliki audit/history yang diperlukan. - \[ \] Migration dapat
dijalankan dari database kosong. - \[ \] Unit/feature test relevan
tersedia dan lulus. - \[ \] Tidak merusak modul anggota lain. - \[ \]
Kontrak integrasi dan perubahan schema didokumentasikan. - \[ \] Pull
request telah direview dan disetujui.

------------------------------------------------------------------------

## 10. Checklist Progres Tim

Gunakan checklist ini untuk memantau pekerjaan. Perbarui status secara
berkala di repository atau project board.

### Anggota 1

-   [ ] Login
-   [ ] Logout
-   [ ] Ganti password
-   [ ] Role dan permission
-   [ ] Tambah akun Pengelola/Staf
-   [ ] Edit data pengguna
-   [ ] Nonaktifkan akun
-   [ ] Analisis riwayat penjualan untuk prediksi
-   [ ] Estimasi porsi harian
-   [ ] Penyesuaian prediksi manual
-   [ ] Estimasi kebutuhan bahan

### Anggota 2

-   [ ] Daftar menu
-   [ ] Tambah/edit/nonaktifkan menu
-   [ ] Atur harga jual
-   [ ] Unggah foto menu
-   [ ] Master resep per menu
-   [ ] Atur takaran bahan
-   [ ] Kelola harga modal resep sesuai permission
-   [ ] Hitung HPP per porsi
-   [ ] Snapshot/versi resep dan HPP

### Anggota 3

-   [ ] Master bahan baku
-   [ ] Satuan dan konversi bahan
-   [ ] Stok awal dan stok berjalan
-   [ ] Stock movement ledger
-   [ ] Update stok fisik
-   [ ] Catat stok susut/rusak
-   [ ] Peringatan stok minimum
-   [ ] Input pembelian
-   [ ] Unggah foto nota
-   [ ] Verifikasi/penolakan pembelian
-   [ ] Service stok masuk/keluar/reversal

### Anggota 4

-   [ ] Input penjualan berbasis menu
-   [ ] Transaksi multi-item
-   [ ] Input penjualan manual
-   [ ] Metode pembayaran
-   [ ] Status dan riwayat transaksi
-   [ ] Integrasi pengurangan stok
-   [ ] Pembatalan dan reversal stok
-   [ ] Kategori biaya
-   [ ] Input biaya operasional
-   [ ] Dashboard keuangan
-   [ ] Kalkulasi HPP dan laba
-   [ ] Filter laporan
-   [ ] Ekspor PDF
-   [ ] Ekspor Excel

------------------------------------------------------------------------

## 11. Keputusan yang Wajib Dicatat Sebelum Integrasi

Jangan biarkan agent mengambil keputusan yang dapat menyebabkan
perbedaan perilaku antar modul tanpa persetujuan tim.

Hal yang harus dipastikan dan dicatat: 1. Status transaksi yang menjadi
pemicu pengurangan stok. 2. Status transaksi yang dianggap sebagai
pendapatan final. 3. Kebijakan ketika stok tidak cukup. 4. Metode
valuasi harga modal bahan baku. 5. Konversi satuan bahan. 6. Perlakuan
pembatalan dan refund. 7. Format snapshot HPP dan resep. 8. Periode
historis dan metode statistik prediksi. 9. Struktur kategori dan data
awal. 10. Struktur route/service lintas modul.

Setiap keputusan baru harus diperbarui di dokumentasi teknis terkait
agar semua anggota dan AI agent menggunakan aturan yang sama.

------------------------------------------------------------------------

## 12. Prinsip Kolaborasi

-   Setiap anggota memiliki tanggung jawab utama, bukan bekerja dalam
    silo.
-   Komunikasikan dependensi sebelum mengubah kontrak data.
-   Jangan menduplikasi business logic yang seharusnya dimiliki satu
    modul.
-   Jangan membuat tabel atau service alternatif untuk fungsi yang sudah
    dimiliki anggota lain.
-   Gunakan pull request untuk perubahan lintas modul.
-   Pastikan satu sumber kebenaran untuk PRD, permission, schema, dan
    status pekerjaan.
-   Jika ada konflik antara implementasi dan PRD, hentikan asumsi,
    diskusikan, lalu dokumentasikan keputusan.

**Target akhir:** satu aplikasi SmartFood Home yang terintegrasi,
konsisten, dapat diuji, dan dapat dikembangkan bersama oleh empat
anggota tanpa saling menimpa pekerjaan.

------------------------------------------------------------------------

**Akhir dokumen TASK_INSTRUCTIONS.md**
