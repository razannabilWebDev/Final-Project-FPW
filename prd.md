# Product Requirements Document (PRD)

# SmartFood Home

> **Status:** Spesifikasi produk untuk implementasi\
> **Versi:** 1.0\
> **Tanggal:** 16 November 2026\
> **Bahasa aplikasi:** Bahasa Indonesia\
> **Target pengguna:** UMKM makanan rumahan\
> **Platform:** Web responsive\
> **Dokumen acuan:** Mind map SmartFood Home dan Use Case Diagram
> SmartFood Home yang diberikan pengguna

------------------------------------------------------------------------

## 1. Ringkasan Produk

### 1.1 Deskripsi

**SmartFood Home** adalah aplikasi web responsive untuk membantu pemilik
usaha makanan rumahan mengelola kegiatan operasional dan keuangan dalam
satu sistem. Aplikasi mencakup pencatatan penjualan, pengelolaan menu
dan resep, pengelolaan stok bahan baku, pembelian bahan baku, biaya
operasional, laporan keuntungan, serta rekomendasi produksi berbasis
data historis.

Sistem digunakan oleh tiga role:

1.  **Admin (Owner):** pemilik usaha dan pemegang kendali utama
    aplikasi.
2.  **Pengelola:** pengguna yang membantu mengelola kegiatan usaha.
3.  **Staf Operasional:** pengguna yang menjalankan aktivitas
    operasional harian.

Satu akun/instalasi usaha ditujukan untuk **satu usaha**. Multi-tenant
dan pengelolaan banyak cabang tidak termasuk ruang lingkup versi awal.

### 1.2 Masalah yang Ingin Diselesaikan

UMKM makanan rumahan sering mencatat transaksi, pembelian bahan,
persediaan, dan pengeluaran secara terpisah atau manual. Hal ini dapat
menyebabkan: - Pemilik kesulitan mengetahui pendapatan dan keuntungan
aktual. - Stok bahan baku tidak selalu sesuai dengan kondisi fisik. -
Biaya modal per menu sulit dihitung secara konsisten. - Riwayat
transaksi dan pembelian sulit ditelusuri. - Perkiraan jumlah produksi
belum memanfaatkan riwayat penjualan.

SmartFood Home menyatukan proses tersebut agar data operasional dapat
digunakan untuk menghasilkan informasi usaha yang lebih terukur.

### 1.3 Tujuan Produk

-   Memudahkan pencatatan penjualan harian, baik berdasarkan menu maupun
    nominal pendapatan.
-   Mengelola menu, harga jual, resep, dan modal per porsi.
-   Memantau stok bahan baku dan mencatat perubahan persediaan.
-   Mencatat serta memverifikasi pembelian bahan baku.
-   Mencatat biaya operasional dengan kategori yang dapat dikelola
    Admin.
-   Menghitung keuntungan secara otomatis berdasarkan transaksi, HPP,
    dan biaya.
-   Menyediakan laporan yang dapat diunduh atau dicetak.
-   Memberikan estimasi produksi yang dapat ditinjau dan disesuaikan
    pengguna.

### 1.4 Indikator Keberhasilan

-   Pengguna dapat mencatat transaksi penjualan tanpa harus membuat
    catatan di luar sistem.
-   Penjualan berbasis menu dapat mengurangi stok sesuai resep yang
    berlaku.
-   Admin dapat meninjau pendapatan, HPP, pengeluaran, dan laba bersih
    dalam periode tertentu.
-   Riwayat stok, pembelian, dan transaksi dapat ditelusuri.
-   Rekomendasi produksi menampilkan dasar data dan dapat disesuaikan
    secara manual.

------------------------------------------------------------------------

## 2. Ruang Lingkup Produk

### 2.1 Dalam Ruang Lingkup

1.  Autentikasi dan pengelolaan akun.
2.  Pengelolaan pengguna berdasarkan role.
3.  Pengelolaan menu dan harga jual.
4.  Pengelolaan resep dan takaran bahan.
5.  Pengelolaan master bahan baku dan stok.
6.  Pencatatan pembelian bahan baku beserta bukti nota.
7.  Pencatatan penjualan dengan banyak item dalam satu transaksi.
8.  Pencatatan penjualan manual berdasarkan nominal pendapatan.
9.  Pencatatan metode pembayaran.
10. Pencatatan biaya operasional dan kategori biaya.
11. Perhitungan HPP dan keuntungan secara otomatis.
12. Dashboard dan laporan keuntungan.
13. Rekomendasi produksi berbasis statistik dengan penyesuaian manual.
14. Ekspor/cetak laporan PDF dan Excel.

### 2.2 Di Luar Ruang Lingkup Versi Awal

-   Pengelolaan beberapa usaha atau banyak cabang dalam satu akun.
-   Aplikasi native Android/iOS.
-   Integrasi langsung ke payment gateway atau verifikasi pembayaran
    otomatis.
-   Integrasi marketplace dan layanan pesan-antar pihak ketiga.
-   Prediksi berbasis machine learning atau AI eksternal.
-   Akuntansi penuh, perpajakan, payroll, dan pembukuan standar
    akuntansi.
-   Sinkronisasi offline-first.
-   Multi-currency.

------------------------------------------------------------------------

## 3. Pengguna, Role, dan Hak Akses

### 3.1 Definisi Role

#### Admin (Owner)

Pemilik usaha dan administrator utama. Admin memiliki akses penuh untuk
mengelola pengguna, menu, resep, stok, pembelian, penjualan, biaya,
laporan keuntungan, dan rekomendasi produksi.

#### Pengelola

Pengguna yang membantu Admin menjalankan dan memantau usaha. Pengelola
dapat melihat daftar menu, mengelola operasional resep sesuai batasan
diagram, memantau stok, melakukan verifikasi pembelian,
mencatat/memantau penjualan, melihat rekap pembelian, serta melihat
estimasi produksi.

#### Staf Operasional

Pengguna yang menjalankan aktivitas harian. Staf dapat melihat daftar
menu, melakukan input pesanan/penjualan, memperbarui status transaksi,
melakukan pembaruan stok fisik, mencatat stok susut/rusak, menginput
nota pembelian, dan mengunggah foto struk/nota.

### 3.2 Matriks Hak Akses

Matriks berikut mengikuti aktor dan use case pada Use Case Diagram. Hak
yang tidak ditampilkan sebagai hubungan aktor-use case dianggap tidak
diberikan sampai ditetapkan kemudian.

  -------------------------------------------------------------------------------------
  Modul         Use case/aksi             Admin          Pengelola     Staf Operasional
  ------------- -------------------- ---------------- ---------------- ----------------
  Autentikasi   Login                       ✓                ✓                ✓

  Autentikasi   Logout                      ✓                ✓                ✓

  Autentikasi   Ganti password              ✓                ✓                ✓

  Kelola        Tambah akun                 ✓               ---              ---
  Pengguna      Pengelola dan Staf                                     

  Kelola        Edit data pengguna          ✓               ---              ---
  Pengguna                                                             

  Kelola        Menonaktifkan akun          ✓               ---              ---
  Pengguna      pengguna                                               

  Pengelolaan   Tambah/Edit/Hapus           ✓               ---              ---
  Menu          menu                                                   

  Pengelolaan   Menentukan harga            ✓               ---              ---
  Menu          jual                                                   

  Pengelolaan   Unggah foto produk          ✓               ---              ---
  Menu                                                                 

  Pengelolaan   Melihat daftar menu         ✓                ✓                ✓
  Menu                                                                 

  Pengelolaan   Mengatur takaran            ✓               ---              ---
  Resep         resep                                                  

  Pengelolaan   Mengelola harga             ✓                ✓               ---
  Resep         modal resep                                            

  Pengelolaan   Melihat resep               ✓                ✓               ---
  Resep         terbatas                                               

  Pengelolaan   Mengelola master            ✓               ---              ---
  Stok Bahan    stok                                                   
  Baku                                                                 

  Pengelolaan   Memantau dan                ✓                ✓               ---
  Stok Bahan    mengelola persediaan                                   
  Baku                                                                 

  Pengelolaan   Update stok fisik           ✓                ✓                ✓
  Stok Bahan    harian                                                 
  Baku                                                                 

  Pengelolaan   Mencatat stok               ✓               ---               ✓
  Stok Bahan    susut/rusak                                            
  Baku                                                                 

  Pembelian     Melihat rekap               ✓                ✓               ---
  Bahan Baku    pembelian                                              

  Pembelian     Verifikasi pembelian        ✓                ✓               ---
  Bahan Baku                                                           

  Pembelian     Input nota belanja          ✓               ---               ✓
  Bahan Baku    harian                                                 

  Pembelian     Unggah foto                 ✓               ---               ✓
  Bahan Baku    struk/nota                                             

  Pencatatan    Rekap dan analisis          ✓                ✓               ---
  Penjualan     penjualan                                              

  Pencatatan    Memantau pesanan            ✓                ✓               ---
  Penjualan     masuk                                                  

  Pencatatan    Input pesanan masuk         ✓                ✓                ✓
  Penjualan                                                            

  Pencatatan    Update status               ✓                ✓                ✓
  Penjualan     transaksi                                              

  Biaya         Mencatat pengeluaran        ✓               ---              ---
  Operasional   tetap                                                  

  Biaya         Mengategorikan jenis        ✓               ---              ---
  Operasional   biaya                                                  

  Biaya         Melihat riwayat             ✓               ---              ---
  Operasional   biaya                                                  

  Laporan       Melihat dashboard           ✓               ---              ---
  Keuntungan    keuangan                                               

  Laporan       Menghitung laba             ✓               ---              ---
  Keuntungan    bersih                                                 

  Laporan       Mengunduh/mencetak          ✓               ---              ---
  Keuntungan    laporan PDF/Excel                                      

  Rekomendasi   Menganalisis                ✓               ---              ---
  Produksi      estimasi produksi                                      

  Rekomendasi   Melihat estimasi            ✓                ✓                ✓
  Produksi      jumlah porsi harian                                    
  -------------------------------------------------------------------------------------

**Catatan implementasi permission:** - Hak akses harus diperiksa di
backend, bukan hanya dengan menyembunyikan menu pada antarmuka. -
Pengguna yang tidak memiliki izin harus menerima respons 403 atau
diarahkan ke halaman tanpa akses. - Akun yang dinonaktifkan tidak boleh
melakukan login. - Pengelola dan Staf tidak boleh dapat menaikkan role
atau mengubah permission dirinya sendiri. - Diagram tidak secara
eksplisit memberi Pengelola akses untuk mengubah takaran resep,
mengelola master bahan, atau mengelola akun. Jangan memberikan izin
tersebut secara implisit. - Diagram menghubungkan Admin dan Staf dengan
pencatatan stok susut/rusak; Pengelola tidak diberi use case tersebut. -
Hak akses tambahan harus melalui keputusan produk dan pembaruan dokumen
ini.

### 3.3 Aturan Akun

-   Admin merupakan Owner dan akun Admin pertama dibuat melalui proses
    instalasi/seeding yang aman.
-   Admin dapat menambahkan akun Pengelola dan Staf Operasional.
-   Admin dapat mengedit data pengguna dan menonaktifkan akun.
-   Akun tidak dihapus permanen apabila sudah memiliki transaksi atau
    aktivitas terkait; gunakan status aktif/nonaktif.
-   Setiap pengguna memiliki identitas login unik.
-   Password harus disimpan dalam bentuk hash.
-   Sistem harus menyediakan proses ganti password bagi seluruh role
    yang telah login.

------------------------------------------------------------------------

## 4. Alur Pengguna Utama

### 4.1 Alur Login

1.  Pengguna membuka halaman login.
2.  Pengguna memasukkan kredensial.
3.  Sistem memvalidasi kredensial dan status akun.
4.  Jika valid dan aktif, sistem membuat sesi autentikasi.
5.  Pengguna diarahkan ke dashboard sesuai role.
6.  Pengguna dapat logout melalui menu akun.

### 4.2 Alur Penjualan Berbasis Menu

1.  Pengguna membuka menu pencatatan penjualan.
2.  Pengguna membuat transaksi baru.
3.  Pengguna memilih satu atau lebih menu.
4.  Pengguna memasukkan jumlah porsi tiap menu.
5.  Sistem mengambil harga jual menu yang berlaku dan menghitung
    subtotal serta total.
6.  Pengguna memilih metode pembayaran: tunai, transfer, atau QRIS.
7.  Pengguna menyimpan transaksi.
8.  Sistem menghitung kebutuhan bahan berdasarkan resep dan jumlah
    porsi.
9.  Sistem mengurangi stok bahan baku secara otomatis.
10. Sistem menyimpan detail transaksi, snapshot harga, dan pergerakan
    stok.
11. Transaksi muncul pada riwayat dan rekap penjualan.

### 4.3 Alur Penjualan Manual

1.  Pengguna memilih mode penjualan manual.
2.  Pengguna memasukkan nominal pendapatan.
3.  Pengguna memilih metode pembayaran.
4.  Pengguna mengisi keterangan jika diperlukan.
5.  Sistem menyimpan transaksi sebagai penjualan manual.
6.  Transaksi masuk ke total pendapatan.

**Aturan penting:** penjualan manual tidak memiliki rincian menu
sehingga tidak dapat digunakan untuk mengurangi stok bahan berdasarkan
resep. Sistem harus menandai transaksi sebagai penjualan manual dan
tidak boleh mengarang pemakaian bahan.

### 4.4 Alur Pembelian Bahan Baku

1.  Staf membuka modul pembelian bahan baku.
2.  Staf menginput tanggal, item bahan, kuantitas, harga beli, pemasok
    (jika tersedia), dan catatan.
3.  Staf mengunggah foto struk/nota.
4.  Pembelian disimpan dengan status **Menunggu Verifikasi**.
5.  Admin atau Pengelola memeriksa detail dan bukti pembelian.
6.  Admin/Pengelola menyetujui atau menolak pembelian dengan alasan.
7.  Jika disetujui, sistem menambah stok bahan dan mencatat pergerakan
    stok.
8.  Jika ditolak, stok tidak bertambah dan catatan penolakan disimpan.

### 4.5 Alur Pembaruan Stok Fisik

1.  Pengguna berizin membuka stok bahan.
2.  Pengguna memasukkan jumlah stok fisik hasil pemeriksaan.
3.  Sistem membandingkan stok sistem dengan stok fisik.
4.  Sistem menampilkan selisih.
5.  Pengguna menyimpan penyesuaian beserta alasan.
6.  Sistem membuat catatan pergerakan stok tipe penyesuaian.
7.  Riwayat penyesuaian dapat ditelusuri.

### 4.6 Alur Stok Susut/Rusak

1.  Admin atau Staf memilih bahan.
2.  Pengguna memasukkan kuantitas yang susut/rusak.
3.  Pengguna memilih alasan atau mengisi keterangan.
4.  Sistem memvalidasi agar kuantitas tidak melebihi stok tersedia.
5.  Sistem mengurangi stok dan mencatat pergerakan stok tipe
    susut/rusak.

### 4.7 Alur Pengelolaan Resep

1.  Admin membuat atau mengedit resep suatu menu.
2.  Admin memilih bahan baku dan memasukkan takaran per porsi.
3.  Sistem menghitung estimasi biaya bahan berdasarkan harga modal
    bahan.
4.  Pengelola dapat mengelola harga modal resep sesuai use case diagram,
    tetapi tidak mengubah takaran resep.
5.  Pengguna yang berhak melihat resep terbatas hanya memperoleh
    informasi yang diizinkan; Staf tidak memiliki akses resep.

### 4.8 Alur Biaya Operasional

1.  Admin membuat kategori biaya jika kategori belum tersedia.
2.  Admin mencatat biaya, tanggal, nominal, kategori, dan keterangan.
3.  Sistem menyimpan biaya.
4.  Biaya masuk ke perhitungan laba bersih sesuai tanggal/periode.

### 4.9 Alur Rekomendasi Produksi

1.  Sistem mengambil riwayat penjualan berbasis menu.
2.  Sistem mengolah pola penjualan per menu berdasarkan rentang data
    yang tersedia.
3.  Sistem menghasilkan estimasi jumlah porsi harian dan estimasi
    kebutuhan bahan.
4.  Admin dapat meninjau hasil analisis dan menyesuaikan estimasi.
5.  Pengelola dan Staf dapat melihat estimasi jumlah porsi harian.
6.  Sistem membedakan nilai prediksi statistik dan nilai akhir setelah
    penyesuaian manual.

------------------------------------------------------------------------

## 5. Kebutuhan Fungsional

Prioritas: - **P0:** wajib untuk rilis dasar. - **P1:** penting untuk
menyelesaikan alur operasional. - **P2:** pengembangan lanjutan.

### 5.1 Autentikasi

  -----------------------------------------------------------------------
  ID                      Kebutuhan               Prioritas
  ----------------------- ----------------------- -----------------------
  FR-AUTH-01              Sistem menyediakan      P0
                          halaman login untuk     
                          semua role.             

  FR-AUTH-02              Sistem memvalidasi      P0
                          kredensial dan status   
                          aktif akun.             

  FR-AUTH-03              Sistem menyediakan      P0
                          logout.                 

  FR-AUTH-04              Semua role dapat        P0
                          mengganti password      
                          setelah login.          

  FR-AUTH-05              Sesi pengguna           P0
                          dilindungi dan diakhiri 
                          dengan aman saat        
                          logout.                 
  -----------------------------------------------------------------------

### 5.2 Kelola Pengguna

  -----------------------------------------------------------------------
  ID                      Kebutuhan               Prioritas
  ----------------------- ----------------------- -----------------------
  FR-USER-01              Admin dapat menambahkan P0
                          akun Pengelola dan Staf 
                          Operasional.            

  FR-USER-02              Admin dapat mengedit    P0
                          data pengguna.          

  FR-USER-03              Admin dapat             P0
                          menonaktifkan akun      
                          pengguna.               

  FR-USER-04              Sistem menyimpan role   P0
                          dan status akun.        

  FR-USER-05              Sistem menolak login    P0
                          dari akun nonaktif.     
  -----------------------------------------------------------------------

### 5.3 Pengelolaan Menu

  ---------------------------------------------------------------------------
  ID                      Kebutuhan                   Prioritas
  ----------------------- --------------------------- -----------------------
  FR-MENU-01              Admin dapat menambah,       P0
                          mengedit, dan               
                          menghapus/menu              
                          menonaktifkan menu.         

  FR-MENU-02              Admin dapat menentukan      P0
                          harga jual.                 

  FR-MENU-03              Admin dapat mengunggah foto P1
                          produk.                     

  FR-MENU-04              Admin, Pengelola, dan Staf  P0
                          dapat melihat daftar menu.  

  FR-MENU-05              Menu yang telah digunakan   P0
                          pada transaksi sebaiknya    
                          dinonaktifkan/diarsipkan,   
                          bukan dihapus permanen.     
  ---------------------------------------------------------------------------

### 5.4 Pengelolaan Resep

  -----------------------------------------------------------------------
  ID                      Kebutuhan               Prioritas
  ----------------------- ----------------------- -----------------------
  FR-RECIPE-01            Admin dapat mengatur    P0
                          takaran bahan untuk     
                          setiap menu.            

  FR-RECIPE-02            Admin dapat mengelola   P0
                          komposisi resep.        

  FR-RECIPE-03            Admin dan Pengelola     P0
                          dapat mengelola harga   
                          modal resep sesuai      
                          batas permission.       

  FR-RECIPE-04            Admin dan Pengelola     P0
                          dapat melihat resep     
                          terbatas.               

  FR-RECIPE-05            Sistem menghitung       P0
                          estimasi modal bahan    
                          per porsi dari takaran  
                          dan harga bahan.        

  FR-RECIPE-06            Sistem menyimpan        P0
                          versi/snapshot          
                          komposisi dan biaya     
                          yang digunakan saat     
                          transaksi agar histori  
                          HPP tidak berubah       
                          ketika resep            
                          diperbarui.             
  -----------------------------------------------------------------------

### 5.5 Pengelolaan Stok Bahan Baku

  -----------------------------------------------------------------------
  ID                      Kebutuhan               Prioritas
  ----------------------- ----------------------- -----------------------
  FR-STOCK-01             Admin dapat mengelola   P0
                          master bahan baku.      

  FR-STOCK-02             Admin dan Pengelola     P0
                          dapat memantau serta    
                          mengelola persediaan.   

  FR-STOCK-03             Admin, Pengelola, dan   P0
                          Staf dapat memperbarui  
                          stok fisik harian.      

  FR-STOCK-04             Admin dan Staf dapat    P0
                          mencatat stok           
                          susut/rusak.            

  FR-STOCK-05             Sistem mencatat seluruh P0
                          pergerakan stok dengan  
                          tipe dan referensi      
                          sumber.                 

  FR-STOCK-06             Sistem memberikan       P1
                          pengingat stok          
                          minimum/menipis.        

  FR-STOCK-07             Penjualan berbasis menu P0
                          mengurangi stok secara  
                          otomatis berdasarkan    
                          resep aktif dan jumlah  
                          porsi.                  

  FR-STOCK-08             Sistem mencegah stok    P0
                          menjadi negatif kecuali 
                          kebijakan khusus        
                          diaktifkan Admin;       
                          default-nya transaksi   
                          yang membutuhkan stok   
                          tidak cukup harus       
                          ditolak atau memerlukan 
                          penanganan eksplisit.   
  -----------------------------------------------------------------------

### 5.6 Pembelian Bahan Baku

  -----------------------------------------------------------------------
  ID                      Kebutuhan               Prioritas
  ----------------------- ----------------------- -----------------------
  FR-PUR-01               Staf dapat menginput    P0
                          nota belanja harian.    

  FR-PUR-02               Staf dapat mengunggah   P1
                          foto struk/nota.        

  FR-PUR-03               Admin dan Pengelola     P0
                          dapat melihat rekap     
                          pembelian.              

  FR-PUR-04               Admin dan Pengelola     P0
                          dapat memverifikasi     
                          pembelian.              

  FR-PUR-05               Pembelian yang          P0
                          disetujui menambah stok 
                          dan membuat stock       
                          movement.               

  FR-PUR-06               Pembelian yang ditolak  P0
                          tidak mengubah stok.    

  FR-PUR-07               Sistem mencegah         P0
                          pembelian yang sama     
                          diproses lebih dari     
                          sekali.                 
  -----------------------------------------------------------------------

### 5.7 Pencatatan Penjualan

  -----------------------------------------------------------------------
  ID                      Kebutuhan               Prioritas
  ----------------------- ----------------------- -----------------------
  FR-SALES-01             Admin, Pengelola, dan   P0
                          Staf dapat menginput    
                          pesanan/penjualan.      

  FR-SALES-02             Satu transaksi dapat    P0
                          berisi beberapa menu.   

  FR-SALES-03             Sistem menghitung       P0
                          subtotal dan total      
                          transaksi.              

  FR-SALES-04             Sistem menyediakan      P0
                          pencatatan manual       
                          berdasarkan nominal     
                          pendapatan.             

  FR-SALES-05             Sistem mencatat metode  P0
                          pembayaran tunai,       
                          transfer, atau QRIS.    

  FR-SALES-06             Admin dan Pengelola     P0
                          dapat melihat rekap dan 
                          analisis penjualan.     

  FR-SALES-07             Admin, Pengelola, dan   P0
                          Staf dapat memantau     
                          pesanan masuk dan       
                          memperbarui status      
                          transaksi sesuai alur.  

  FR-SALES-08             Sistem menyimpan        P0
                          snapshot nama menu,     
                          harga jual, dan HPP     
                          saat transaksi dibuat.  

  FR-SALES-09             Penjualan berbasis menu P0
                          mengurangi stok secara  
                          otomatis.               

  FR-SALES-10             Penjualan manual tidak  P0
                          mengurangi stok resep.  

  FR-SALES-11             Pembatalan transaksi    P0
                          yang telah mengurangi   
                          stok harus menghasilkan 
                          pembalikan stok yang    
                          tercatat, bukan         
                          menghapus histori.      
  -----------------------------------------------------------------------

### 5.8 Biaya Operasional

  -----------------------------------------------------------------------
  ID                      Kebutuhan               Prioritas
  ----------------------- ----------------------- -----------------------
  FR-EXP-01               Admin dapat mencatat    P0
                          pengeluaran tetap.      

  FR-EXP-02               Admin dapat             P0
                          mengategorikan jenis    
                          biaya.                  

  FR-EXP-03               Admin dapat menambah,   P0
                          mengubah, dan           
                          menonaktifkan kategori  
                          biaya.                  

  FR-EXP-04               Admin dapat melihat     P0
                          riwayat biaya.          

  FR-EXP-05               Sistem menyertakan      P0
                          biaya operasional dalam 
                          perhitungan laba        
                          bersih.                 
  -----------------------------------------------------------------------

### 5.9 Laporan Keuntungan

  -----------------------------------------------------------------------
  ID                      Kebutuhan               Prioritas
  ----------------------- ----------------------- -----------------------
  FR-REPORT-01            Admin dapat melihat     P0
                          dashboard keuangan.     

  FR-REPORT-02            Sistem menghitung       P0
                          pendapatan, HPP, biaya  
                          operasional, dan laba   
                          bersih.                 

  FR-REPORT-03            Admin dapat memfilter   P1
                          laporan berdasarkan     
                          rentang tanggal.        

  FR-REPORT-04            Admin dapat             P1
                          mengunduh/mencetak      
                          laporan PDF.            

  FR-REPORT-05            Admin dapat mengunduh   P1
                          laporan Excel.          

  FR-REPORT-06            Sistem menyediakan      P1
                          ringkasan penjualan dan 
                          menu terlaris.          

  FR-REPORT-07            Sistem membedakan       P0
                          penjualan berbasis menu 
                          dan penjualan manual.   
  -----------------------------------------------------------------------

### 5.10 Rekomendasi Produksi

  -----------------------------------------------------------------------
  ID                      Kebutuhan               Prioritas
  ----------------------- ----------------------- -----------------------
  FR-PRED-01              Sistem menganalisis     P2
                          riwayat penjualan untuk 
                          memperkirakan kebutuhan 
                          produksi.               

  FR-PRED-02              Sistem menampilkan      P2
                          estimasi jumlah porsi   
                          harian per menu.        

  FR-PRED-03              Sistem memperkirakan    P2
                          kebutuhan bahan baku    
                          dari estimasi porsi dan 
                          resep.                  

  FR-PRED-04              Admin dapat             P2
                          menyesuaikan estimasi   
                          produksi secara manual. 

  FR-PRED-05              Sistem menyimpan nilai  P2
                          prediksi statistik dan  
                          nilai setelah           
                          penyesuaian secara      
                          terpisah.               

  FR-PRED-06              Pengelola dan Staf      P2
                          dapat melihat estimasi  
                          jumlah porsi harian.    

  FR-PRED-07              Sistem menampilkan      P2
                          informasi periode dan   
                          kecukupan data yang     
                          digunakan untuk         
                          prediksi.               
  -----------------------------------------------------------------------

------------------------------------------------------------------------

## 6. Aturan Bisnis

### 6.1 Penjualan dan Status Transaksi

Status transaksi minimum: - `pending`: pesanan baru dan belum
diselesaikan. - `processing`: sedang diproses. - `completed`: transaksi
selesai. - `cancelled`: transaksi dibatalkan.

Ketentuan: - Perubahan status harus disimpan bersama pengguna dan waktu
perubahan. - Pengurangan stok dilakukan satu kali ketika transaksi
mencapai status yang ditetapkan sebagai titik pengakuan penjualan.
**Default implementasi:** saat transaksi dikonfirmasi/diterima, bukan
setiap kali halaman dibuka. - Sistem harus menggunakan mekanisme
idempotensi agar perubahan status berulang tidak mengurangi stok
berulang. - Pembatalan transaksi yang stoknya sudah dikurangi harus
membuat pergerakan stok reversal. - Definisi final kapan pendapatan
diakui perlu konsisten: rekomendasi default adalah saat transaksi
berstatus `completed`. Transaksi pending/processing tidak masuk
pendapatan final.

### 6.2 Metode Pembayaran

Metode pembayaran: - Tunai. - Transfer. - QRIS.

Versi awal hanya mencatat metode pembayaran. Sistem tidak melakukan
verifikasi otomatis ke rekening atau penyedia QRIS.

### 6.3 Penjualan Manual

-   Penjualan manual menyimpan nominal pendapatan, tanggal/waktu, metode
    pembayaran, pencatat, dan catatan opsional.
-   Penjualan manual tidak memiliki item menu dan tidak memengaruhi stok
    otomatis.
-   Laporan harus menampilkan penjualan manual secara terpisah agar
    analisis menu dan prediksi produksi tidak menganggapnya sebagai
    penjualan menu tertentu.

### 6.4 HPP dan Resep

-   HPP per porsi dihitung dari jumlah takaran setiap bahan dikalikan
    harga modal per satuan bahan.
-   Harga modal bahan harus menggunakan satuan yang konsisten dengan
    satuan resep.
-   Sistem harus mendukung konversi satuan yang diperlukan (contoh kg ke
    gram, liter ke ml) melalui aturan konversi yang jelas.
-   Perubahan resep atau harga bahan di masa depan tidak boleh mengubah
    HPP transaksi historis.
-   Sistem menyimpan snapshot HPP per item transaksi pada saat transaksi
    dikonfirmasi.
-   Biaya tenaga kerja, kemasan, dan overhead tidak otomatis masuk HPP
    kecuali dicatat sebagai komponen resep/biaya yang secara eksplisit
    dikonfigurasi. Versi awal berfokus pada modal bahan berdasarkan
    resep.

### 6.5 Stok Bahan Baku

Rumus dasar:
`Stok akhir = Stok awal + Stok masuk - Pemakaian penjualan - Stok susut/rusak +/- Penyesuaian`

-   Stok bertambah ketika pembelian diverifikasi/disetujui.
-   Stok berkurang dari penjualan berbasis menu sesuai resep dan jumlah
    porsi.
-   Stok berkurang ketika susut/rusak dicatat.
-   Penyesuaian stok fisik harus menyimpan nilai sebelum, nilai sesudah,
    selisih, alasan, pengguna, dan waktu.
-   Setiap perubahan stok harus memiliki catatan pergerakan (stock
    movement).
-   Pengurangan stok harus dilakukan dalam database transaction agar
    perubahan stok dan status penjualan konsisten.
-   Bila stok tidak cukup, sistem menampilkan bahan yang kurang dan
    jumlah kekurangannya. Sistem tidak boleh diam-diam membuat stok
    negatif.
-   Jika resep tidak tersedia atau tidak lengkap, sistem harus memberi
    peringatan dan tidak mengurangi stok secara perkiraan.

### 6.6 Pembelian

-   Pembelian yang baru diinput memiliki status `pending`.
-   Hanya Admin atau Pengelola yang dapat menyetujui/menolak.
-   Persetujuan hanya boleh diproses satu kali.
-   Stok masuk hanya setelah pembelian disetujui.
-   Penolakan wajib menyimpan alasan.
-   Foto nota merupakan lampiran bukti, bukan pengganti data item
    pembelian terstruktur.

### 6.7 Keuntungan

Definisi metrik: - **Pendapatan bersih penjualan:** total nilai
penjualan yang telah selesai, setelah pengurangan/refund yang
tercatat. - **HPP:** total biaya bahan untuk item penjualan berbasis
menu yang selesai, menggunakan snapshot HPP. - **Laba kotor:**
pendapatan bersih penjualan dikurangi HPP. - **Biaya operasional:**
pengeluaran operasional yang dicatat Admin pada periode laporan. -
**Laba bersih operasional:** laba kotor dikurangi biaya operasional.

Rumus: `Laba Kotor = Pendapatan Bersih Penjualan - HPP`

`Laba Bersih Operasional = Laba Kotor - Biaya Operasional`

Catatan: - Penjualan manual berkontribusi ke pendapatan, tetapi HPP-nya
tidak diketahui. Laporan harus menandai bagian ini sebagai **HPP tidak
tersedia**, bukan menganggap HPP = 0 sebagai fakta. - Dashboard
sebaiknya memperlihatkan nilai penjualan manual dan penjualan berbasis
menu secara terpisah. - Laporan harus menjelaskan cakupan perhitungan
laba jika ada penjualan manual yang tidak memiliki HPP.

### 6.8 Kategori Pengeluaran

-   Sistem menyediakan kategori awal yang dapat digunakan sebagai seed
    data.
-   Admin dapat menambah, mengubah nama, dan menonaktifkan kategori.
-   Kategori yang sudah digunakan transaksi tidak boleh dihapus
    permanen; gunakan status aktif/nonaktif.
-   Contoh kategori awal: pembelian bahan baku, listrik/air, gas, gaji,
    sewa, transportasi, perawatan, dan lainnya. Kategori ini adalah
    rekomendasi awal dan dapat disesuaikan Admin.

### 6.9 Prediksi Produksi

-   Prediksi menggunakan data historis penjualan berbasis menu.
-   Penjualan manual tidak digunakan untuk menghitung pola permintaan
    per menu.
-   Admin dapat menyesuaikan hasil prediksi secara manual.
-   Sistem harus menyimpan nilai prediksi awal, nilai penyesuaian,
    alasan (opsional), pengguna, dan waktu.
-   Jika data historis tidak cukup, tampilkan status data terbatas dan
    jangan menampilkan angka seolah-olah akurat.
-   Estimasi bahan dihitung dari jumlah porsi rekomendasi dikalikan
    takaran resep.
-   Metode statistik awal dapat menggunakan rata-rata bergerak penjualan
    harian per menu. Parameter jendela historis harus dapat diatur di
    konfigurasi atau ditetapkan sebagai nilai default terdokumentasi.
-   Hasil prediksi adalah rekomendasi, bukan perintah produksi otomatis.

------------------------------------------------------------------------

## 7. Kebutuhan Data dan Rancangan Database Awal

Rancangan ini merupakan baseline konseptual. Nama tabel/kolom dapat
disesuaikan saat implementasi, tetapi relasi dan aturan integritas harus
dipertahankan.

### 7.1 Entitas Utama

  -----------------------------------------------------------------------------
  Tabel                         Tujuan                  Kolom penting (contoh)
  ----------------------------- ----------------------- -----------------------
  `users`                       Akun pengguna           id, name,
                                                        email/username,
                                                        password, role,
                                                        is_active, timestamps

  `menus`                       Data menu               id, name, description,
                                                        selling_price,
                                                        image_path, is_active

  `ingredients`                 Master bahan baku       id, name, base_unit,
                                                        current_stock,
                                                        minimum_stock,
                                                        average_cost, is_active

  `recipes`                     Resep per menu          id, menu_id, notes,
                                                        version

  `recipe_items`                Detail takaran resep    id, recipe_id,
                                                        ingredient_id,
                                                        quantity, unit

  `sales`                       Header transaksi        id, transaction_code,
                                penjualan               type, status,
                                                        payment_method, total,
                                                        created_by,
                                                        completed_at

  `sale_items`                  Item menu per transaksi id, sale_id, menu_id,
                                                        menu_name_snapshot,
                                                        quantity,
                                                        unit_price_snapshot,
                                                        hpp_snapshot, subtotal

  `sale_status_histories`       Riwayat status          id, sale_id,
                                                        from_status, to_status,
                                                        changed_by, note,
                                                        created_at

  `purchases`                   Header pembelian        id, purchase_code,
                                                        purchase_date,
                                                        supplier_name, status,
                                                        total, receipt_path,
                                                        created_by,
                                                        verified_by,
                                                        verified_at

  `purchase_items`              Detail item pembelian   id, purchase_id,
                                                        ingredient_id,
                                                        quantity, unit,
                                                        unit_cost, subtotal

  `stock_movements`             Ledger perubahan stok   id, ingredient_id,
                                                        movement_type,
                                                        quantity_in,
                                                        quantity_out,
                                                        balance_before,
                                                        balance_after,
                                                        reference_type,
                                                        reference_id, note,
                                                        created_by

  `stock_adjustments`           Penyesuaian stok fisik  id, ingredient_id,
                                                        system_quantity,
                                                        physical_quantity,
                                                        difference, reason,
                                                        created_by

  `expense_categories`          Kategori biaya          id, name, description,
                                                        is_active

  `expenses`                    Pengeluaran             id,
                                                        expense_category_id,
                                                        expense_date, amount,
                                                        description, created_by

  `production_forecasts`        Hasil prediksi          id, forecast_date,
                                                        method, data_window,
                                                        generated_by,
                                                        generated_at

  `production_forecast_items`   Prediksi per menu       id, forecast_id,
                                                        menu_id,
                                                        predicted_quantity,
                                                        adjusted_quantity,
                                                        adjustment_note

  `settings`                    Pengaturan              id, key, value
                                aplikasi/usaha          

  `activity_logs`               Jejak aktivitas penting id, user_id, action,
                                                        subject_type,
                                                        subject_id, metadata,
                                                        created_at
  -----------------------------------------------------------------------------

### 7.2 Relasi Utama

-   `users` membuat banyak `sales`, `purchases`, `expenses`, dan
    aktivitas.
-   `menus` memiliki satu atau lebih versi `recipes`.
-   `recipes` memiliki banyak `recipe_items`.
-   `recipe_items` mengacu pada `ingredients`.
-   `sales` memiliki banyak `sale_items`.
-   `sales` memiliki banyak `sale_status_histories`.
-   `purchases` memiliki banyak `purchase_items`.
-   `ingredients` memiliki banyak `stock_movements`.
-   `expense_categories` memiliki banyak `expenses`.
-   `production_forecasts` memiliki banyak `production_forecast_items`.

### 7.3 Persyaratan Integritas Data

-   Gunakan foreign key untuk relasi penting.
-   Gunakan decimal untuk nilai uang dan kuantitas pecahan; hindari
    floating point untuk uang.
-   Gunakan database transaction untuk proses yang mengubah beberapa
    tabel sekaligus.
-   Kode transaksi dan kode pembelian harus unik.
-   Simpan snapshot harga jual dan HPP pada detail penjualan.
-   Simpan ledger pergerakan stok; jangan hanya menimpa angka stok tanpa
    histori.
-   Penghapusan data master yang sudah direferensikan harus dibatasi
    atau menggunakan soft delete/status nonaktif.
-   Simpan waktu dengan konsisten dan tampilkan sesuai zona waktu usaha
    (default `Asia/Jakarta`).
-   File foto produk dan bukti nota disimpan di storage, sedangkan
    database menyimpan path dan metadata file.

------------------------------------------------------------------------

## 8. Kebutuhan UI/UX

### 8.1 Prinsip Desain

-   Antarmuka menggunakan Bahasa Indonesia.
-   Desain sederhana, bersih, dan mudah dipahami oleh pemilik UMKM.
-   Responsive untuk desktop, tablet, dan smartphone.
-   Navigasi menyesuaikan role.
-   Form input transaksi dan pembelian harus ringkas.
-   Angka uang menggunakan format Rupiah, misalnya `Rp15.000`.
-   Tampilkan status transaksi/pembelian dengan label yang mudah
    dipahami.
-   Tampilkan konfirmasi untuk tindakan penting seperti pembatalan
    transaksi, penonaktifan akun, dan penyesuaian stok.

### 8.2 Halaman Minimum

1.  Login.
2.  Dashboard sesuai role.
3.  Manajemen pengguna (Admin).
4.  Daftar dan form menu (Admin).
5.  Daftar dan form bahan baku (Admin).
6.  Daftar dan form resep (Admin/Pengelola sesuai izin).
7.  Pencatatan penjualan.
8.  Daftar/monitor pesanan.
9.  Detail transaksi dan riwayat status.
10. Input pembelian dan unggah nota.
11. Verifikasi pembelian.
12. Stok dan pergerakan stok.
13. Penyesuaian stok fisik.
14. Catatan stok susut/rusak.
15. Biaya operasional.
16. Dashboard/laporan keuntungan (Admin).
17. Rekomendasi produksi.
18. Profil usaha/pengaturan.
19. Ganti password.

### 8.3 Dashboard per Role

**Admin:** - Ringkasan pendapatan. - Ringkasan HPP dan laba. - Ringkasan
biaya operasional. - Grafik penjualan. - Menu terlaris. - Stok
menipis. - Pembelian menunggu verifikasi. - Rekomendasi produksi.

**Pengelola:** - Ringkasan aktivitas penjualan. - Pesanan masuk. -
Status pembelian untuk diverifikasi. - Kondisi stok. - Daftar menu. -
Estimasi produksi harian.

**Staf Operasional:** - Input pesanan/penjualan. - Pesanan yang perlu
diproses. - Daftar menu. - Pembaruan stok fisik. - Input
pembelian/nota. - Estimasi jumlah porsi harian.

Dashboard tidak boleh menampilkan informasi keuangan atau data yang
tidak diizinkan oleh role.

------------------------------------------------------------------------

## 9. Teknologi dan Arsitektur

### 9.1 Stack yang Ditetapkan

-   **Backend:** Laravel.
-   **Bahasa backend:** PHP.
-   **Database:** MySQL.
-   **Frontend:** Blade.
-   **CSS:** Tailwind CSS.
-   **Platform:** Web responsive.
-   **Autentikasi:** Laravel authentication dengan session.
-   **Otorisasi:** middleware, policies/gates, atau mekanisme
    role-permission yang konsisten.
-   **Penyimpanan file:** Laravel Storage.
-   **Ekspor laporan:** library PDF dan Excel yang kompatibel dengan
    versi Laravel/PHP yang dipilih.

Gunakan versi Laravel dan PHP yang stabil serta kompatibel pada saat
proyek mulai dibuat. Dokumentasikan versi aktual di README dan
`composer.json`; jangan mengasumsikan versi dependency tanpa memeriksa
kompatibilitas.

### 9.2 Arsitektur

Gunakan **Laravel monolith** dengan pemisahan tanggung jawab: -
Controllers menangani HTTP request dan response. - Form Requests
menangani validasi input dan otorisasi request bila sesuai. -
Policies/Gates atau middleware menangani permission. - Services/Actions
menangani proses bisnis seperti konfirmasi penjualan, pengurangan stok,
verifikasi pembelian, dan kalkulasi laporan. - Models menangani relasi
dan akses data. - Blade components digunakan untuk elemen UI yang
berulang.

Hindari menaruh seluruh logika bisnis di Controller atau Blade.

### 9.3 Prinsip Keamanan

-   Password menggunakan hashing bawaan Laravel.
-   Validasi dan otorisasi dilakukan di server.
-   Lindungi form dari CSRF.
-   Gunakan query builder/Eloquent dengan parameter binding.
-   Validasi MIME type, ukuran, dan ekstensi upload.
-   Simpan file upload dengan nama aman dan jangan mengeksekusi file
    upload.
-   Batasi akses file bukti pembelian sesuai role.
-   Gunakan rate limiting untuk login.
-   Jangan menyimpan secret/API key di repository.
-   Sediakan `.env.example` tanpa kredensial rahasia.
-   Catat aktivitas sensitif seperti perubahan role, verifikasi
    pembelian, perubahan stok, dan pembatalan transaksi.

------------------------------------------------------------------------

## 10. Kebutuhan Non-Fungsional

  -----------------------------------------------------------------------
  ID                      Kategori                Persyaratan
  ----------------------- ----------------------- -----------------------
  NFR-01                  Usability               Alur pencatatan
                                                  penjualan harus
                                                  sederhana dan dapat
                                                  digunakan pada layar
                                                  HP.

  NFR-02                  Responsive              Halaman utama harus
                                                  dapat digunakan pada
                                                  smartphone, tablet, dan
                                                  desktop.

  NFR-03                  Security                Semua endpoint
                                                  memeriksa autentikasi
                                                  dan permission sesuai
                                                  role.

  NFR-04                  Data integrity          Perubahan stok dan
                                                  status transaksi
                                                  dilakukan secara
                                                  atomik.

  NFR-05                  Auditability            Perubahan penting
                                                  memiliki jejak
                                                  pengguna, waktu, dan
                                                  alasan jika relevan.

  NFR-06                  Performance             Halaman daftar
                                                  menggunakan pagination
                                                  dan query yang efisien.

  NFR-07                  Reliability             Kesalahan proses tidak
                                                  boleh meninggalkan stok
                                                  dan transaksi dalam
                                                  kondisi setengah
                                                  tersimpan.

  NFR-08                  Maintainability         Logika bisnis
                                                  dipisahkan dari
                                                  controller dan view.

  NFR-09                  Backup                  Prosedur backup
                                                  database dan file
                                                  upload harus
                                                  didokumentasikan.

  NFR-10                  Localization            Format mata uang Rupiah
                                                  dan zona waktu
                                                  `Asia/Jakarta`.

  NFR-11                  Accessibility           Form memiliki label,
                                                  pesan validasi yang
                                                  jelas, dan kontras teks
                                                  yang memadai.

  NFR-12                  Compatibility           Mendukung browser
                                                  modern yang umum
                                                  digunakan.
  -----------------------------------------------------------------------

------------------------------------------------------------------------

## 11. Roadmap Pengembangan

Urutan fase mengikuti fase pada mind map yang diberikan.

### Fase 1 --- Penjualan dan Laporan Dasar

**Modul:** - Catat Jualan Harian. - Laporan Untung.

**Fitur:** - Input penjualan cepat. - Input penjualan berbasis menu dan
input nominal manual. - Ringkasan harian. - Riwayat penjualan. -
Contoh/format laporan. - Ringkasan untung harian dan bulanan. - Menu
paling laris, jika transaksi sudah memiliki detail menu.

**Catatan dependensi:** untuk perhitungan laba berbasis HPP yang akurat,
sistem memerlukan data resep dan modal bahan. Sebelum modul resep
tersedia, laporan harus menampilkan metrik yang memang sudah dapat
dihitung dan memberi label bahwa HPP belum lengkap. Jangan menampilkan
laba bersih seolah-olah akurat jika komponen HPP belum tersedia.

### Fase 2 --- Menu, Resep, dan Stok

**Modul:** - Kelola Menu & Harga. - Kelola Resep. - Stok Bahan Baku.

**Fitur:** - Daftar menu. - Tambah, edit, dan nonaktifkan menu. - Atur
harga jual. - Unggah foto menu. - Takaran resep. - Modal per porsi. -
Master bahan baku. - Stok masuk dan terpakai. - Pembaruan stok fisik. -
Pencatatan stok susut/rusak. - Peringatan stok menipis. - Pengurangan
stok otomatis dari penjualan berbasis menu.

### Fase 3 --- Pengeluaran dan Pembelian

**Modul:** - Catat Pengeluaran. - Pembelian Bahan Baku.

**Fitur:** - Belanja bahan baku. - Input nota belanja harian oleh
Staf. - Unggah foto struk/nota. - Verifikasi pembelian oleh
Admin/Pengelola. - Riwayat pembelian. - Biaya operasional. - Kategori
pengeluaran yang dapat dikelola Admin. - Riwayat pengeluaran.

### Fase 4 --- Akun dan Pengaturan

**Modul:** - Akun & Pengaturan.

**Fitur:** - Login dan logout. - Ganti password. - Tambah akun Pengelola
dan Staf. - Edit data pengguna. - Menonaktifkan akun. - Profil usaha. -
Pengaturan dasar aplikasi.

**Catatan:** autentikasi dan role/permission adalah fondasi teknis
sehingga harus dibuat sejak awal proyek, meskipun modul pengelolaan akun
tercantum pada Fase 4.

### Fase 5 --- Rekomendasi Prediksi

**Modul:** - Rekomendasi Produksi.

**Fitur:** - Perkiraan porsi masak. - Estimasi belanja bahan. -
Identifikasi menu kurang laku. - Analisis riwayat penjualan. -
Penyesuaian prediksi secara manual. - Tampilan estimasi untuk Admin,
Pengelola, dan Staf sesuai use case.

### Urutan Dependensi yang Disarankan

1.  Fondasi proyek, autentikasi, role, dan permission.
2.  Master menu dan bahan baku.
3.  Resep dan harga modal.
4.  Penjualan dan integrasi pengurangan stok.
5.  Pembelian dan verifikasi stok masuk.
6.  Biaya operasional dan laporan keuntungan.
7.  Rekomendasi produksi.

Urutan implementasi teknis dapat berbeda dari urutan presentasi fase,
selama dependensi data dan proses bisnis terpenuhi.

------------------------------------------------------------------------

## 12. Acceptance Criteria

### 12.1 Autentikasi dan Permission

-   [ ] Admin dapat login dan mengakses seluruh modul yang ditetapkan.
-   [ ] Admin dapat membuat akun Pengelola dan Staf.
-   [ ] Pengelola tidak dapat membuka halaman kelola pengguna.
-   [ ] Staf tidak dapat mengakses laporan keuntungan.
-   [ ] Pengguna tidak berizin ditolak di backend meskipun membuka URL
    secara langsung.
-   [ ] Akun nonaktif tidak dapat login.

### 12.2 Penjualan

-   [ ] Satu transaksi dapat memiliki beberapa item menu.
-   [ ] Total dihitung dari kuantitas dikalikan harga jual snapshot.
-   [ ] Metode pembayaran dapat dipilih.
-   [ ] Penjualan manual dapat disimpan tanpa item menu.
-   [ ] Penjualan manual tidak mengurangi stok.
-   [ ] Transaksi selesai masuk ke laporan periode yang sesuai.
-   [ ] Perubahan status berulang tidak menyebabkan stok terpotong
    berulang.

### 12.3 Resep dan Stok

-   [ ] Admin dapat mengatur takaran resep.
-   [ ] HPP per porsi dihitung dari bahan dan harga modal.
-   [ ] Penjualan berbasis menu mengurangi stok berdasarkan jumlah porsi
    dan resep.
-   [ ] Sistem menolak atau meminta penanganan eksplisit jika stok tidak
    cukup.
-   [ ] Pembelian terverifikasi menambah stok.
-   [ ] Stok susut/rusak mengurangi stok.
-   [ ] Setiap perubahan stok memiliki histori.

### 12.4 Pembelian dan Pengeluaran

-   [ ] Staf dapat menginput pembelian dan mengunggah bukti nota.
-   [ ] Admin/Pengelola dapat menyetujui atau menolak pembelian.
-   [ ] Pembelian yang ditolak tidak menambah stok.
-   [ ] Pembelian tidak dapat disetujui dua kali.
-   [ ] Admin dapat membuat kategori pengeluaran.
-   [ ] Biaya yang dicatat masuk ke laporan periode yang sesuai.

### 12.5 Laporan dan Prediksi

-   [ ] Admin dapat melihat pendapatan, HPP, biaya operasional, laba
    kotor, dan laba bersih operasional.
-   [ ] Penjualan manual ditampilkan terpisah dari penjualan berbasis
    menu.
-   [ ] Laporan tidak menganggap HPP penjualan manual sebagai nol yang
    terkonfirmasi.
-   [ ] Admin dapat mengunduh laporan PDF dan Excel.
-   [ ] Prediksi menampilkan estimasi jumlah porsi per menu.
-   [ ] Admin dapat menyesuaikan hasil prediksi.
-   [ ] Pengelola dan Staf dapat melihat estimasi porsi harian.
-   [ ] Sistem memberi peringatan jika data historis belum cukup.

------------------------------------------------------------------------

## 13. Strategi Pengujian

### 13.1 Unit Test

Prioritaskan pengujian untuk: - Perhitungan subtotal dan total
transaksi. - Perhitungan HPP per porsi. - Perhitungan kebutuhan bahan
dari resep. - Perhitungan laba kotor dan laba bersih. - Konversi satuan
bahan. - Perhitungan prediksi rata-rata bergerak. - Validasi stok tidak
negatif.

### 13.2 Feature Test

Uji: - Login setiap role. - Akses halaman sesuai permission. - Pembuatan
dan penonaktifan akun. - CRUD menu dan resep. - Input penjualan
multi-item. - Input penjualan manual. - Pengurangan stok otomatis. -
Pembatalan transaksi dan reversal stok. - Input dan verifikasi
pembelian. - Penyesuaian stok. - Pencatatan biaya. - Filter dan ekspor
laporan.

### 13.3 Skenario Integrasi Kritis

**Skenario A --- Penjualan mengurangi stok** 1. Bahan A tersedia 100
gram. 2. Resep Menu X membutuhkan 20 gram per porsi. 3. Pengguna
mencatat 3 porsi Menu X dan mengonfirmasi transaksi. 4. Stok bahan A
harus menjadi 40 gram. 5. Satu stock movement keluar sebesar 60 gram
harus tercatat.

**Skenario B --- Stok tidak cukup** 1. Bahan A tersedia 20 gram. 2.
Resep Menu X membutuhkan 20 gram per porsi. 3. Pengguna mencoba mencatat
2 porsi. 4. Sistem harus menolak konfirmasi atau meminta tindakan
eksplisit yang diizinkan. 5. Stok tidak boleh berubah sebagian.

**Skenario C --- Pembelian diverifikasi** 1. Staf memasukkan pembelian 2
kg bahan A. 2. Status pembelian menjadi pending. 3. Stok belum
bertambah. 4. Pengelola menyetujui pembelian. 5. Stok bertambah sesuai
konversi satuan dan satu stock movement masuk tercatat. 6. Permintaan
persetujuan kedua tidak boleh menggandakan stok.

**Skenario D --- Penjualan manual** 1. Pengguna memasukkan pendapatan
manual Rp100.000. 2. Transaksi disimpan dengan metode pembayaran. 3.
Pendapatan masuk ke laporan. 4. Tidak ada stok bahan yang berkurang. 5.
Laporan menandai HPP transaksi manual sebagai tidak tersedia.

------------------------------------------------------------------------

## 14. Instruksi Implementasi untuk OpenCode

Gunakan dokumen ini sebagai **single source of truth** untuk
pengembangan SmartFood Home.

### 14.1 Aturan Kerja Agent

1.  Baca seluruh `prd.md` sebelum mengubah atau membuat kode.
2.  Periksa struktur repository, dependency, versi PHP/Laravel,
    database, dan pola kode yang sudah ada sebelum melakukan perubahan.
3.  Jika repository sudah berisi aplikasi, pertahankan struktur dan
    fitur yang valid. Jangan membuat ulang proyek tanpa alasan.
4.  Implementasikan secara bertahap berdasarkan fase dan dependensi.
5.  Jangan mengimplementasikan seluruh fase sekaligus tanpa pengujian
    bertahap.
6.  Jangan mengarang permission. Ikuti matriks Role & Permission pada
    dokumen ini.
7.  Otorisasi harus diterapkan di backend.
8.  Semua perubahan stok harus melalui service/domain action terpusat
    dan dicatat dalam stock movement.
9.  Proses penjualan, pembelian, dan stok harus menggunakan database
    transaction serta perlindungan dari proses ganda.
10. Jangan menghapus histori transaksi, pembelian, biaya, atau
    pergerakan stok secara permanen.
11. Buat migration, model, relasi, request validation,
    policy/middleware, service, controller, route, dan view secara
    konsisten.
12. Buat test untuk setiap proses bisnis penting sebelum menyatakan
    fitur selesai.
13. Jangan mengganti teknologi yang telah ditetapkan tanpa meminta
    persetujuan.
14. Jangan menambahkan integrasi eksternal berbayar atau layanan AI
    eksternal tanpa persetujuan.
15. Jika ada keputusan bisnis yang belum ditentukan, catat sebagai
    pertanyaan/assumption dan minta klarifikasi sebelum membuat perilaku
    yang berisiko terhadap data.

### 14.2 Tahapan Implementasi yang Disarankan

Untuk setiap fase: 1. Analisis kebutuhan dan dependensi. 2. Tampilkan
rencana file dan perubahan database. 3. Implementasikan migration dan
model. 4. Implementasikan business logic/service. 5. Implementasikan
authorization dan validation. 6. Implementasikan UI responsive. 7.
Tambahkan unit test dan feature test. 8. Jalankan test dan perbaiki
kegagalan. 9. Ringkas file yang berubah, fitur selesai, hasil test, dan
pekerjaan tersisa. 10. Perbarui dokumentasi proyek.

### 14.3 Definition of Done

Sebuah fitur dinyatakan selesai apabila: - Fungsional sesuai kebutuhan
dan aturan bisnis PRD. - Permission telah diuji pada backend. - Validasi
input tersedia. - UI responsive dan memiliki state loading, empty,
success, dan error yang sesuai. - Perubahan data penting tercatat. -
Test terkait lulus. - Tidak merusak fitur yang telah selesai. -
Dokumentasi teknis diperbarui bila diperlukan.

### 14.4 Dokumen Pendamping yang Disarankan

Buat dan pelihara dokumen berikut di repository: - `README.md` ---
instalasi, konfigurasi, dan cara menjalankan aplikasi. - `AGENTS.md` ---
instruksi kerja AI coding agent. - `docs/architecture.md` --- arsitektur
dan pola kode. - `docs/database.md` --- ERD, tabel, relasi, dan aturan
data. - `docs/permissions.md` --- matriks role dan permission. -
`docs/implementation-plan.md` --- status fase dan daftar pekerjaan.

------------------------------------------------------------------------

## 15. Asumsi dan Keputusan yang Masih Perlu Divalidasi

Bagian ini sengaja dipisahkan agar agent tidak menganggap asumsi sebagai
keputusan final.

1.  **Titik pengurangan stok:** rekomendasi default adalah ketika
    transaksi dikonfirmasi/diterima. Perlu disepakati apakah status
    `processing` atau `completed` menjadi titik final.
2.  **Pengakuan pendapatan:** rekomendasi default adalah ketika
    transaksi `completed`.
3.  **Stok tidak cukup:** default sistem menolak konfirmasi agar stok
    tidak negatif. Kebijakan stok negatif belum disetujui.
4.  **Penjualan manual:** HPP tidak diketahui dan tidak mengurangi stok.
    Laporan harus mengungkap keterbatasan ini.
5.  **Resep tidak lengkap:** sistem tidak boleh mengurangi stok secara
    perkiraan.
6.  **Harga modal bahan:** metode valuasi awal perlu ditetapkan.
    Rekomendasi: harga rata-rata tertimbang setelah pembelian
    terverifikasi, dengan snapshot biaya pada transaksi.
7.  **Pembatalan/refund:** pembatalan setelah stok dikurangi harus
    menghasilkan reversal. Alur refund sebagian belum dirinci.
8.  **Pengelola dan resep:** diagram memberi Pengelola hak mengelola
    harga modal resep dan melihat resep terbatas, tetapi tidak mengatur
    takaran resep.
9.  **Pengingat stok menipis:** gunakan nilai minimum stok per bahan
    yang dapat diatur Admin.
10. **Data prediksi:** periode historis minimum dan rumus final perlu
    divalidasi setelah tersedia data penjualan nyata.
11. **Profil usaha:** detail field profil usaha belum ditetapkan.
12. **Pemasok:** nama pemasok dapat dicatat sebagai teks pada versi
    awal; manajemen master pemasok belum menjadi kebutuhan wajib.
13. **Banyak pembayaran dalam satu transaksi:** versi awal menggunakan
    satu metode pembayaran per transaksi. Split payment belum termasuk
    ruang lingkup.
14. **Multi-cabang:** tidak termasuk ruang lingkup versi awal.

------------------------------------------------------------------------

## 16. Glosarium

  -----------------------------------------------------------------------
  Istilah                             Definisi
  ----------------------------------- -----------------------------------
  Admin                               Owner/pemilik usaha dan
                                      administrator utama.

  Pengelola                           Pengguna yang membantu mengelola
                                      kegiatan usaha.

  Staf Operasional                    Pengguna yang menjalankan aktivitas
                                      operasional harian.

  HPP                                 Harga pokok produksi; dalam versi
                                      awal dihitung dari biaya bahan
                                      berdasarkan resep.

  Resep                               Daftar bahan dan takaran untuk
                                      menghasilkan satu porsi menu.

  Stock movement                      Catatan setiap perubahan stok masuk
                                      atau keluar.

  Penjualan manual                    Pencatatan nominal pendapatan tanpa
                                      rincian menu.

  Laba kotor                          Pendapatan bersih penjualan
                                      dikurangi HPP yang diketahui.

  Laba bersih operasional             Laba kotor dikurangi biaya
                                      operasional yang dicatat.

  Prediksi produksi                   Estimasi jumlah porsi berdasarkan
                                      pola penjualan historis, yang dapat
                                      disesuaikan manual.
  -----------------------------------------------------------------------

------------------------------------------------------------------------

**Akhir dokumen PRD SmartFood Home --- Versi 1.0**
