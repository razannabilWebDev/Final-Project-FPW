# Design System --- SmartFood

> Dokumen acuan desain antarmuka SmartFood berdasarkan screenshot UI
> yang diberikan. Dokumen ini menjelaskan pola visual, layout, komponen,
> dan perilaku antarmuka agar implementasi konsisten. Detail teks kecil
> pada screenshot tidak seluruhnya terbaca, sehingga nama field atau
> label yang tidak jelas ditulis secara generik dan perlu disesuaikan
> dengan PRD serta use case final.

## 1. Ringkasan Produk

**SmartFood** adalah aplikasi web manajemen operasional untuk usaha
makanan rumahan/UMKM. Antarmuka pada screenshot memperlihatkan dua area
utama:

1.  **Halaman autentikasi** dengan layout dua kolom: panel branding
    berwarna navy dan panel formulir.
2.  **Aplikasi dashboard internal** dengan sidebar navigasi tetap, area
    konten utama, tabel data, kartu ringkasan, grafik, kartu menu
    bergambar, serta dialog/form untuk tambah, ubah, lihat, dan hapus
    data.

Desain ditujukan untuk penggunaan desktop dan tetap responsif pada
tablet maupun ponsel.

### Prinsip desain

-   **Dashboard-first:** informasi operasional penting terlihat cepat
    setelah pengguna masuk.
-   **Data-centric:** tabel, filter, status, dan ringkasan angka menjadi
    pola utama.
-   **Visual makanan:** foto menu digunakan sebagai pengenal visual pada
    pengelolaan menu dan rekomendasi.
-   **Konsisten:** halaman modul menggunakan struktur header, aksi
    utama, filter, konten, dan pagination yang seragam.
-   **Jelas dan efisien:** formulir menggunakan label yang eksplisit,
    tombol aksi yang mudah dibedakan, serta konfirmasi untuk tindakan
    berisiko.

## 2. Arah Visual

### Karakter visual

-   Modern, bersih, profesional, dan berorientasi pada aplikasi bisnis.
-   Warna dominan navy/biru tua untuk identitas dan navigasi.
-   Putih sebagai permukaan utama konten.
-   Abu-abu sangat muda atau krem pucat sebagai latar area tertentu.
-   Biru terang untuk aksi utama dan elemen interaktif.
-   Hijau, oranye, dan merah untuk status atau umpan balik yang memiliki
    makna.

### Palet warna

Nilai berikut merupakan token awal yang diturunkan secara visual dari
screenshot. Sesuaikan nilai akhir melalui color sampling dari aset
desain/Figma apabila tersedia.

  -----------------------------------------------------------------------
  Token                   Nilai awal              Penggunaan
  ----------------------- ----------------------- -----------------------
  `primary-900`           `#102F6B`               Sidebar, panel
                                                  branding, elemen
                                                  identitas

  `primary-800`           `#173D82`               Hover/active sidebar
                                                  dan variasi navy

  `primary-600`           `#2563EB`               Tombol utama, link,
                                                  fokus

  `primary-100`           `#E8F0FF`               Latar item aktif dan
                                                  highlight biru

  `surface`               `#FFFFFF`               Card, tabel, modal,
                                                  form

  `background`            `#F7F8FA`               Latar area aplikasi

  `warm-background`       `#FFF8ED`               Area bernuansa krem
                                                  pada halaman bergambar

  `text-primary`          `#172033`               Judul dan teks utama

  `text-secondary`        `#667085`               Deskripsi, hint,
                                                  metadata

  `border`                `#E5E7EB`               Garis tabel, input,
                                                  card

  `success`               `#16A34A`               Status
                                                  berhasil/tersedia

  `warning`               `#F59E0B`               Status perlu perhatian

  `danger`                `#DC2626`               Hapus, gagal, stok
                                                  kritis

  `info`                  `#0EA5E9`               Informasi dan status
                                                  netral
  -----------------------------------------------------------------------

Gunakan warna status secara konsisten. Jangan menggunakan warna hanya
sebagai dekorasi jika warna tersebut dapat ditafsirkan sebagai status.

## 3. Tipografi

Gunakan font sans-serif modern yang mudah dibaca. Rekomendasi
implementasi: **Inter** atau font sans-serif sistem jika font eksternal
tidak tersedia.

  Elemen                 Ukuran rekomendasi Ketebalan
  -------------------- -------------------- -----------
  Judul halaman                   24--28 px 700
  Judul section/card              16--18 px 600
  Isi utama                       14--16 px 400
  Label form                      13--14 px 500--600
  Teks tabel                      13--14 px 400
  Caption/hint                        12 px 400
  Angka KPI                       24--30 px 700

Gunakan line-height sekitar 1.4--1.6 untuk teks isi. Hindari terlalu
banyak variasi ukuran dan ketebalan dalam satu halaman.

## 4. Layout dan Grid

### Struktur aplikasi internal

``` text
┌──────────────────────────────────────────────────────────────┐
│ Sidebar tetap │ Header halaman / breadcrumb / profil         │
│               ├──────────────────────────────────────────────┤
│ Navigasi      │ Judul halaman                    Aksi utama  │
│ modul         │ Deskripsi singkat                            │
│               ├──────────────────────────────────────────────┤
│               │ KPI / filter / tab (sesuai modul)            │
│               ├──────────────────────────────────────────────┤
│               │ Tabel / grafik / kartu / formulir             │
│               │                                              │
│               └──────────────────────────────────────────────┘
└──────────────────────────────────────────────────────────────┘
```

-   Sidebar desktop memiliki lebar acuan sekitar **220--250 px** dan
    memenuhi tinggi viewport.
-   Konten utama mengisi sisa lebar layar.
-   Header konten berada di bagian atas area kerja; jangan membuat
    header terlalu tinggi.
-   Gunakan padding konten desktop sekitar **24--32 px**.
-   Gunakan jarak antar-section sekitar **20--24 px**.
-   Card menggunakan padding sekitar **16--20 px**.
-   Lebar konten harus fleksibel dan tidak memaksa tabel melewati
    viewport.

### Responsif

-   **Desktop (≥ 1024 px):** sidebar tampil penuh dan konten menggunakan
    grid multi-kolom.
-   **Tablet (768--1023 px):** sidebar dapat dipadatkan menjadi ikon
    atau drawer; KPI dapat menjadi dua kolom.
-   **Mobile (\< 768 px):** navigasi menjadi drawer/menu, KPI satu kolom
    atau dua kolom sesuai ruang, form satu kolom, tabel menggunakan
    horizontal scroll atau tampilan daftar ringkas.
-   Modal pada mobile menggunakan hampir seluruh lebar layar dengan
    margin yang cukup.
-   Tombol dan kontrol sentuh memiliki area interaksi yang nyaman,
    idealnya minimal 40--44 px.

## 5. Navigasi dan Hak Akses

Screenshot memperlihatkan sidebar vertikal dengan identitas aplikasi di
bagian atas dan daftar modul di bawahnya. Implementasikan navigasi
berdasarkan role dan use case final, bukan sekadar menyembunyikan tombol
di frontend.

### Pola sidebar

-   Logo/nama **SmartFood** di bagian atas.
-   Item navigasi berupa ikon dan label.
-   Item aktif memiliki latar biru yang lebih terang/kontras atau
    indikator sisi.
-   Kelompok navigasi dapat dipisahkan dengan label section kecil.
-   Bagian bawah dapat memuat profil pengguna dan aksi keluar.
-   Pada layar kecil, sidebar berubah menjadi drawer.

### Role aplikasi

Gunakan role yang telah ditetapkan dalam kebutuhan produk:

-   **Admin (Owner)**
-   **Pengelola**
-   **Staf Operasional**

Daftar menu dan izin setiap role wajib mengikuti dokumen PRD serta use
case diagram. Jangan mengasumsikan semua role memiliki akses CRUD yang
sama. Validasi otorisasi harus dilakukan di backend.

## 6. Pola Komponen

### 6.1 Tombol

  -----------------------------------------------------------------------
  Jenis                   Gaya                    Penggunaan
  ----------------------- ----------------------- -----------------------
  Primary                 Biru solid, teks putih  Simpan, tambah data,
                                                  proses utama

  Secondary               Putih/abu muda dengan   Batal, kembali, aksi
                          border                  alternatif

  Danger                  Merah                   Hapus atau aksi
                                                  destruktif

  Text/Ghost              Transparan, teks        Aksi ringan di tabel
                          berwarna                atau toolbar

  Icon button             Ikon dengan area klik   Detail, edit, menu
                          jelas                   tambahan
  -----------------------------------------------------------------------

Ketentuan: - Gunakan label berupa kata kerja yang jelas, misalnya
**Tambah Menu**, **Simpan**, **Batal**, dan **Hapus**. - Sediakan state
hover, focus, active, disabled, dan loading. - Cegah submit berulang
ketika proses sedang berjalan.

### 6.2 Card

-   Latar putih, border tipis, radius sekitar 10--14 px.
-   Shadow sangat ringan atau tanpa shadow jika pemisahan border sudah
    cukup.
-   Gunakan struktur judul, deskripsi opsional, isi, dan aksi.
-   Hindari card bersarang terlalu banyak.

### 6.3 KPI / summary card

Digunakan pada dashboard dan halaman laporan untuk menampilkan ringkasan
seperti total penjualan, jumlah transaksi, biaya, keuntungan, stok, atau
kebutuhan produksi sesuai data modul.

Setiap kartu berisi: - Label metrik. - Nilai utama yang paling
menonjol. - Perbandingan/periode atau keterangan tambahan jika datanya
tersedia. - Ikon kecil opsional.

Format angka dan mata uang harus konsisten menggunakan Rupiah (`Rp`) dan
pemisah ribuan Indonesia.

### 6.4 Tabel data

Screenshot menampilkan tabel sebagai pola utama untuk halaman
pengelolaan.

Struktur umum: - Toolbar judul dan tombol tambah. - Pencarian. - Filter
yang relevan dengan modul. - Header kolom dengan kontras yang cukup. -
Baris data dengan tinggi nyaman dibaca. - Kolom status menggunakan
badge. - Kolom aksi untuk detail/edit/hapus sesuai izin. - Pagination
dan informasi jumlah data.

Ketentuan: - Header tabel tetap terbaca saat konten panjang. - Gunakan
empty state ketika data kosong. - Sediakan loading state dan error
state. - Pada mobile, gunakan horizontal scroll atau ubah setiap baris
menjadi item/list card. - Jangan menampilkan aksi yang tidak diizinkan
untuk role aktif.

### 6.5 Badge status

Gunakan badge kecil dengan latar lembut dan teks kontras: - Hijau:
aktif, tersedia, berhasil, lunas. - Oranye: menunggu, perlu perhatian,
stok menipis. - Merah: nonaktif karena masalah, stok habis/kritis,
gagal. - Abu-abu/biru: draft, netral, atau informasi.

Makna warna harus disertai teks status, bukan warna saja.

### 6.6 Form dan input

-   Label berada di atas input.
-   Placeholder hanya sebagai contoh, bukan pengganti label.
-   Field wajib diberi indikator.
-   Hint dan pesan validasi diletakkan dekat field terkait.
-   Gunakan select untuk pilihan terbatas, date picker untuk tanggal,
    textarea untuk deskripsi, dan input angka untuk kuantitas/harga.
-   Kelompokkan field yang saling berkaitan.
-   Validasi server tetap wajib meskipun validasi frontend tersedia.

### 6.7 Modal / dialog

Screenshot memperlihatkan banyak dialog untuk proses tambah, ubah,
detail, dan konfirmasi.

Pola modal: - Overlay gelap transparan di belakang. - Panel putih di
tengah dengan radius sekitar 10--14 px. - Header berisi judul dan tombol
tutup. - Konten form/detail berada di tengah. - Footer berisi tombol
Batal dan aksi utama. - Modal konfirmasi hapus menonjolkan konsekuensi
dan tombol Danger. - Modal detail menu dapat menampilkan foto, nama,
informasi, dan status.

Perilaku: - Tutup melalui tombol X dan tombol Batal. - Escape dapat
menutup modal jika tidak sedang memproses. - Fokus keyboard tetap berada
di dalam modal. - Setelah sukses, tampilkan notifikasi dan perbarui data
yang relevan. - Jangan menutup modal ketika penyimpanan gagal; tampilkan
kesalahan.

### 6.8 Notifikasi

Gunakan toast/alert ringkas untuk: - Berhasil
menyimpan/mengubah/menghapus. - Gagal memproses data. - Validasi atau
konflik data. - Informasi proses yang sedang berjalan.

Notifikasi harus menyebut hasil dengan jelas dan tidak hanya
mengandalkan warna.

## 7. Panduan Halaman

Bagian ini memetakan pola visual yang tampak pada screenshot ke kelompok
halaman aplikasi. Nama dan isi field spesifik tetap mengikuti PRD, skema
database, dan use case.

### 7.1 Login

Screenshot memperlihatkan halaman login dua kolom.

**Panel branding:** - Latar navy/biru tua. - Logo dan nama SmartFood. -
Headline singkat yang menjelaskan manfaat aplikasi. - Foto makanan
sebagai elemen visual di bagian bawah. - Teks pendukung berwarna putih
atau biru muda.

**Panel form:** - Latar putih. - Judul sambutan dan deskripsi singkat. -
Input identitas akun/email sesuai mekanisme autentikasi. - Input
password dengan kontrol tampil/sembunyikan. - Opsi ingat saya jika
disediakan dalam kebutuhan. - Tombol masuk berwarna biru dan selebar
area form. - Pesan error validasi ditampilkan dekat input.

Pada mobile, susun panel branding dan form secara vertikal; pastikan
form tetap menjadi fokus utama.

### 7.2 Dashboard

Gunakan dashboard sebagai ringkasan kondisi usaha: - Sapaan/judul
halaman dan konteks periode. - Kartu KPI untuk indikator operasional
yang relevan. - Grafik tren penjualan atau metrik yang ditentukan PRD. -
Ringkasan transaksi atau aktivitas terbaru. - Informasi stok atau bahan
yang memerlukan perhatian. - Ringkasan rekomendasi produksi bila role
memiliki akses.

Jangan mengisi dashboard dengan angka dummy ketika data produksi belum
tersedia. Gunakan empty state atau placeholder yang jelas pada tahap
pengembangan.

### 7.3 Pengelolaan Menu

Screenshot memperlihatkan tampilan menu berbentuk kartu dengan foto
makanan dan juga tampilan tabel.

Gunakan: - Header halaman dan tombol tambah menu. - Pencarian dan filter
kategori/status. - Tampilan grid kartu bergambar untuk eksplorasi
menu. - Tabel/list untuk pengelolaan data yang lebih padat jika
dibutuhkan. - Kartu menu berisi foto, nama, harga, kategori, status, dan
aksi sesuai izin. - Dialog tambah/edit dengan field menu dan unggah
gambar. - Dialog detail dengan foto berukuran lebih besar.

Foto harus memiliki rasio dan ukuran tampilan yang konsisten. Gunakan
fallback ketika gambar belum tersedia.

### 7.4 Pengelolaan Resep

Gunakan pola tabel dan form terstruktur: - Daftar resep/menu yang
memiliki komposisi bahan. - Detail resep menampilkan daftar bahan,
kuantitas, dan satuan. - Form tambah/edit memungkinkan pengguna menambah
atau menghapus baris bahan. - Tampilkan total komposisi secara
terstruktur. - Validasi agar kuantitas bernilai masuk akal dan bahan
yang dipilih valid.

### 7.5 Stok dan Bahan Baku

Gunakan tabel sebagai tampilan utama: - Nama bahan, satuan, jumlah stok,
dan status stok. - Pencarian dan filter status/kategori. - Badge untuk
stok aman, menipis, dan habis. - Form tambah/edit bahan. - Dialog
konfirmasi untuk penyesuaian stok yang berdampak pada persediaan.

### 7.6 Pembelian Bahan Baku

Gunakan pola form transaksi: - Informasi pemasok/tanggal jika tersedia
pada kebutuhan. - Daftar item bahan yang dapat ditambah lebih dari satu
baris. - Kuantitas, satuan, dan harga. - Ringkasan total pembelian. -
Riwayat pembelian dalam tabel. - Berikan konfirmasi sebelum transaksi
disimpan.

### 7.7 Penjualan

Screenshot memperlihatkan tabel transaksi dan sejumlah dialog/form.
Antarmuka penjualan perlu mendukung: - Pemilihan lebih dari satu menu
dalam satu transaksi. - Penambahan item dan perubahan kuantitas. -
Penjualan menu maupun input manual sesuai ketentuan PRD. - Ringkasan
subtotal dan total. - Metode pembayaran sebagai field wajib. -
Konfirmasi sebelum transaksi diselesaikan. - Riwayat transaksi dan
detail transaksi.

Setelah transaksi berhasil, tampilkan ringkasan yang mudah dipahami.
Pengurangan stok bahan dan perhitungan keuntungan dilakukan oleh sistem
berdasarkan aturan bisnis, bukan dihitung manual oleh pengguna.

### 7.8 Biaya Operasional

Gunakan tabel riwayat dan form input biaya: - Tanggal. - Kategori biaya
yang dapat dikelola sesuai kebutuhan. - Deskripsi. - Nominal. - Filter
periode dan kategori. - Ringkasan total biaya pada periode terpilih.

### 7.9 Laporan Keuntungan

Gunakan layout laporan dengan: - Pemilih rentang tanggal/periode. - KPI
penjualan, biaya, dan keuntungan. - Grafik tren yang relevan. - Tabel
rincian transaksi/biaya bila diperlukan. - Format nominal Rupiah. -
Empty state saat belum ada data pada periode tersebut.

Perhitungan keuntungan harus mengikuti formula bisnis pada PRD. UI hanya
menyajikan hasil perhitungan dari backend.

### 7.10 Rekomendasi Produksi

Gunakan halaman yang menggabungkan hasil prediksi statistik dengan
penyesuaian manual: - Pilihan periode/tanggal rekomendasi. - Daftar menu
dan jumlah produksi yang disarankan. - Informasi pendukung yang
tersedia, misalnya tren/riwayat penjualan. - Input penyesuaian manual. -
Penjelasan singkat bahwa rekomendasi dapat disesuaikan oleh pengguna
berwenang. - Aksi simpan/terapkan rekomendasi sesuai use case.

Jangan menampilkan rekomendasi seolah-olah pasti benar. Tampilkan
sebagai estimasi/saran dan pertahankan jejak penyesuaian bila diwajibkan
oleh kebutuhan sistem.

## 8. Grafik dan Visualisasi Data

-   Gunakan grafik batang untuk perbandingan kategori atau periode.
-   Gunakan grafik garis untuk tren dari waktu ke waktu.
-   Berikan judul, label sumbu, legenda, dan tooltip yang informatif.
-   Gunakan warna seri secara konsisten.
-   Hindari terlalu banyak seri dalam satu grafik.
-   Pastikan informasi utama tetap tersedia dalam bentuk angka/tabel
    agar dapat diakses tanpa membaca grafik.
-   Pada mobile, grafik harus dapat menyusut atau digulir tanpa merusak
    layout.

## 9. Foto dan Aset Visual

-   Gunakan foto makanan yang relevan dan berkualitas baik.
-   Terapkan rasio gambar yang konsisten pada kartu menu.
-   Gunakan `object-fit: cover` untuk thumbnail.
-   Gunakan gambar resolusi lebih besar pada dialog detail.
-   Berikan placeholder/fallback untuk gambar kosong atau gagal dimuat.
-   Optimalkan ukuran file gambar agar halaman tetap cepat.
-   Jangan menggunakan gambar dekoratif yang mengganggu keterbacaan form
    dan tabel.

## 10. Spacing, Radius, dan Elevation

Token awal:

  Token               Nilai
  --------------- ---------
  `space-1`            4 px
  `space-2`            8 px
  `space-3`           12 px
  `space-4`           16 px
  `space-5`           20 px
  `space-6`           24 px
  `space-8`           32 px
  `radius-sm`          6 px
  `radius-md`         10 px
  `radius-lg`         14 px
  `radius-pill`     9999 px

Gunakan border tipis untuk memisahkan komponen. Shadow hanya untuk
elemen yang perlu terasa mengambang, seperti modal, dropdown, dan
popover.

## 11. State yang Wajib Diimplementasikan

Setiap halaman/modul harus memiliki state berikut sesuai konteks:

-   **Default:** data dan komponen tampil normal.
-   **Loading:** indikator ketika data sedang diambil/disimpan.
-   **Empty:** belum ada data; berikan penjelasan dan CTA jika pengguna
    berhak menambah data.
-   **Error:** pesan kesalahan dan cara mencoba kembali bila relevan.
-   **Success:** konfirmasi proses berhasil.
-   **Disabled:** kontrol tidak dapat digunakan karena kondisi atau
    izin.
-   **Validation:** pesan spesifik pada field yang bermasalah.
-   **Confirmation:** dialog untuk aksi destruktif atau transaksi
    penting.

## 12. Aksesibilitas dan Usability

-   Pastikan kontras teks dan latar memadai.
-   Semua input memiliki label.
-   Semua tombol memiliki nama/label yang dapat dikenali pembaca layar.
-   Jangan mengandalkan warna saja untuk menyampaikan status.
-   Dukung navigasi keyboard dan focus state yang terlihat.
-   Gunakan heading secara hierarkis.
-   Tampilkan pesan validasi yang spesifik dan mudah ditindaklanjuti.
-   Hindari teks terlalu kecil, terutama pada tabel dan metadata.

## 13. Aturan Implementasi Frontend

Stack antarmuka yang telah ditetapkan dalam kebutuhan proyek: -
**Laravel** sebagai aplikasi utama. - **Blade** untuk templating. -
**Tailwind CSS** untuk styling. - **MySQL** sebagai basis data.

Panduan implementasi: 1. Buat layout Blade bersama untuk sidebar,
header, footer bila ada, dan area konten. 2. Buat komponen Blade
reusable untuk tombol, input, select, badge, card, modal, alert, dan
pagination. 3. Terapkan token warna, spacing, radius, dan tipografi
secara konsisten melalui konfigurasi/theme Tailwind. 4. Pisahkan
komponen halaman berdasarkan modul; jangan menggandakan markup layout.
5. Pertahankan validasi backend Laravel sebagai sumber kebenaran. 6.
Terapkan authorization/policy sesuai role dan use case. 7. Pastikan
gambar diunggah, disimpan, dan ditampilkan melalui mekanisme storage
aplikasi. 8. Gunakan data dari backend; jangan menjadikan local storage
sebagai penyimpanan utama data bisnis. 9. Periksa tampilan pada desktop,
tablet, dan mobile. 10. Cocokkan hasil implementasi dengan screenshot
referensi pada ukuran viewport yang sama.

## 14. Kriteria Penerimaan Visual

Implementasi dianggap sesuai apabila:

-   [ ] Login menggunakan komposisi dua panel: branding navy dan form
    putih.
-   [ ] Area internal memiliki sidebar navy dengan navigasi aktif yang
    jelas.
-   [ ] Halaman menggunakan latar terang, card putih, border halus, dan
    aksen biru.
-   [ ] Judul halaman, deskripsi, aksi utama, filter, dan konten
    memiliki hierarki konsisten.
-   [ ] Tabel memiliki pencarian/filter, status, aksi, dan pagination
    sesuai kebutuhan modul.
-   [ ] Form dan modal mengikuti pola visual yang konsisten.
-   [ ] Halaman menu mendukung tampilan kartu dengan foto makanan.
-   [ ] Dashboard/laporan menampilkan ringkasan dan grafik secara rapi.
-   [ ] Semua halaman memiliki loading, empty, error, dan success state
    yang sesuai.
-   [ ] Hak akses UI mengikuti role dan tetap divalidasi di backend.
-   [ ] Layout tetap usable pada desktop, tablet, dan ponsel.
-   [ ] Tidak ada horizontal overflow yang tidak disengaja.
-   [ ] Foto, angka, status, dan data yang tampil berasal dari sumber
    data aplikasi yang benar.

## 15. Catatan Batas Referensi

Screenshot yang diberikan berupa kumpulan beberapa layar dalam satu
kanvas. Karena sebagian teks, label, dan angka berukuran sangat kecil,
dokumen ini menetapkan **sistem visual dan pola interaksi**, bukan
spesifikasi final setiap field atau aturan bisnis.

Jika terdapat perbedaan antara dokumen ini dengan PRD, use case diagram,
atau aturan bisnis yang telah disepakati, urutan acuan adalah: 1. PRD
dan keputusan kebutuhan final. 2. Use case diagram dan matriks hak
akses. 3. Desain/screenshot referensi untuk tampilan visual. 4. Dokumen
ini sebagai panduan implementasi design system.

Jangan mengarang field, status, perhitungan, atau hak akses yang belum
ditetapkan pada dokumen kebutuhan.
