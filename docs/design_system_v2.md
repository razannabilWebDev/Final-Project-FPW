# Design System v2 — SmartFood Home

> **Status:** Mutakhir, menggantikan `design_system.md` sebagai acuan visual utama.
> `design_system.md` (ekstraksi Figma Make 2026-11-16) tetap dipakai sebagai arsip token,
> tetapi dokumen ini adalah **sumber kebenaran visual** karena disusun dari screenshot
> panel-per-panel Figma "SmartFood Home Management Dashboard" yang aktif dipakai tim.
>
> **Source of truth konflik (PRD §15 + TASK_INSTRUCTIONS §1):**
> 1. PRD & keputusan kebutuhan final (field, aturan bisnis, hak akses).
> 2. Use case diagram & matriks hak akses.
> 3. Screenshot Figma (tampilan visual).
> 4. Dokumen ini (panduan implementasi).
>
> **Stack:** Laravel + Blade + Tailwind CSS 4 + MySQL. UI Bahasa Indonesia, Rupiah
> `Rp15.000` (tanpa spasi), timezone `Asia/Jakarta`.

---

## 1. Prinsip desain

- **Dashboard-first:** ringkasan operasional terlihat segera setelah login.
- **Data-centric:** tabel, filter, status badge, dan KPI adalah pola utama.
- **Konsisten:** semua modul memakai shell, toolbar, tabel, dan modal yang sama.
- **Visual makanan:** foto menu dipakai sebagai pengenal visual pada kartu menu & detail.
- **Jelas & efisien:** label eksplisit, aksi mudah dibedakan, konfirmasi untuk aksi destruktif.
- **Aksesibel:** kontras memadai, teks ≥11px, status tidak hanya mengandalkan warna.

---

## 2. Design tokens

### 2.1 Warna inti

| Token CSS | Hex | Pemakaian |
|---|---|---|
| `--color-brand` | `#2563eb` | Tombol primer, link, aksen aktif, ikon |
| `--color-brand-hover` | `#1d4ed8` | Hover tombol primer, teks badge biru |
| `--color-navy` | `#142d70` | Sidebar, panel login kiri (solid), hero |
| `--color-navy-mid` | `#153577` | Panel login (basis gradient), hero rekomendasi |
| `--color-navy-warm` | `#1e3a8a` | Teks judul di permukaan gelap, ujung gradient |
| `--color-accent-light` | `#93c5fd` | Headline two-tone login, progress bar, indikator sidebar aktif |
| `--color-surface` | `#ffffff` | Card, tabel, modal, topbar, panel form |
| `--color-cream` | `#fff9ed` | Background aplikasi (app-shell, body) |
| `--color-line` | `#ecece8` | Border card & garis pemisah |
| `--color-line-input` | `#dfe3e8` | Border input/select |
| `--color-head-bg` | `#f8fafc` | Background header tabel & footer modal |
| `--color-text` | `#1f2937` | Teks utama |
| `--color-text-form` | `#4b5563` | Teks form & isi tabel |
| `--color-muted` | `#6b7280` | Teks sekunder/keterangan |
| `--color-muted-light` | `#9ca3af` | Placeholder, ikon pasif |

### 2.2 Warna semantik (tonal: teks di atas latar terangnya)

| Semantik | Teks/ikon | Latar lembut |
|---|---|---|
| Sukses / aktif / aman / tersedia / selesai / disetujui | `#16a34a` (label `#15803d`) | `#dcfce7` |
| Peringatan / menunggu / menipis / diproses | `#d97706` (label `#b45309`) | `#fef3c7` |
| Bahaya / habis / kritikal / ditolak / dibatalkan | `#dc2626` (label `#b91c1c`) | `#fee2e2` |
| Info / netral | `#2563eb` (label `#1d4ed8`) | `#dbeafe` |
| Ungu (statistik) | `#7c3aed` | `#ede9fe` |
| Cyan (statistik) | `#0891b2` | `#cffafe` |
| Netral abu | `#64748b` | `#f1f5f9` |
| Kuning insight (rekomendasi) | `#b45309` | `#fffbeb`, border `#fde68a` |

### 2.3 Warna grafik (tambahan dari Figma)

| Seri | Warna | Pemakaian |
|---|---|---|
| Pendapatan / seri utama | `#2563eb` (biru) | Grafik batang pendapatan, tren penjualan |
| Pengeluaran / seri pembanding | `#f59e0b` (oranye) | Grafik batang pengeluaran |
| Garis laba bersih | `#16a34a` (hijau) | Grafik garis "Tren Laba Bersih" |

### 2.4 Tipografi

- **Font:** `Inter` (Google Fonts), weight 400/500/600/700/800.
- **Skala terpakai di Figma:** hero login `clamp(34px→54px)`, H1 topbar 17px, H2 halaman 22px,
  judul card 13–14px, label form 12px, isi tabel 9.5px.
- **Normalisasi wajib (PRD NFR-11):** ukuran <11px di mock **dinormalisasi minimal 11px** di Blade.
  Jangan meniru 8–9px.
- Judul memakai `letter-spacing: -0.02em … -0.045em`; label kategori *eyebrow* uppercase dengan
  `letter-spacing: 0.05–0.14em`.
- Line height isi 1.4–1.6.

### 2.5 Radius, shadow, focus

| Token | Nilai |
|---|---|
| Radius input/tombol | `8px` |
| Radius card | `13–16px` (card 14, modal 16, food-card 12) |
| Radius pill/badge/progress | `99px` |
| Radius sidebar aktif | `8px` + indikator kiri `3px × 21px` |
| Shadow card | `0 8px 24px rgba(30,58,138,0.06)` |
| Shadow modal | `0 25px 80px rgba(15,23,42,0.3)` |
| Shadow toast | `0 15px 45px rgba(15,23,42,0.15)` |
| Focus | `outline 3px #bfdbfe, offset 1px`; input fokus: border biru + ring `0 0 0 3px #dbeafe` |

### 2.6 Pemetaan Tailwind 4 (`resources/css/app.css`)

```css
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap");
@import "tailwindcss";

@theme {
  --font-sans: "Inter", sans-serif;
  --color-brand: #2563eb;
  --color-brand-hover: #1d4ed8;
  --color-navy: #142d70;
  --color-navy-mid: #153577;
  --color-navy-warm: #1e3a8a;
  --color-accent-light: #93c5fd;
  --color-cream: #fff9ed;
  --color-line: #ecece8;
  --color-line-input: #dfe3e8;
  --color-head-bg: #f8fafc;
  --color-success: #16a34a;
  --color-success-soft: #dcfce7;
  --color-warning: #d97706;
  --color-warning-soft: #fef3c7;
  --color-danger: #dc2626;
  --color-danger-soft: #fee2e2;
  --color-info-soft: #dbeafe;
  --color-insight: #fffbeb;
  --color-insight-line: #fde68a;
  --color-chart-orange: #f59e0b;
  --shadow-card: 0 8px 24px rgb(30 58 138 / 0.06);
  --shadow-modal: 0 25px 80px rgb(15 23 42 / 0.3);
  --shadow-toast: 0 15px 45px rgb(15 23 42 / 0.15);
}
```

Utility yang tersedia: `bg-cream`, `text-brand`, `border-line`, `shadow-card`, dll.

---

## 3. Layout shell (semua halaman internal)

```
sidebar (fixed, 252px, navy) │ workspace
                             ├─ topbar (76px, sticky, putih)
                             └─ main-content (padding 26px 30px, max-width 1600px)
```

- **Sidebar:** navy `#142d70`, item 39px radius 8px; aktif bg `#3154a5` + indikator `#93c5fd`
  di kiri; label grup uppercase "MENU UTAMA"; profil mini + "Keluar dari akun" merah muda.
  Di ≤900px menjadi drawer + scrim.
- **Topbar:** breadcrumb `SmartFood Home › <Modul>`, judul halaman 17px, dropdown
  **"Tampilan: Admin"** (⚠️ mock/demo — di produksi role berasal dari akun, bukan dropdown),
  notifikasi dengan dot merah, avatar + nama + peran.
- **Navigasi per role** (filter `admin: true` hanya untuk Admin):
  Dashboard, Kelola Pengguna (Admin), Pengelolaan Menu, Pengelolaan Resep, Stok Bahan Baku,
  Pembelian Bahan Baku, Pencatatan Penjualan, Biaya Operasional, Laporan Keuntungan (Admin),
  Rekomendasi Produksi. Selalu tampil: Ganti Password, profil, Keluar.
- **Halaman awal setelah login:** dashboard per role (PRD §8.3).
- **Breakpoint:** `1180px`, `900px` (sidebar drawer, grid 2 kolom), `640px` (single column,
  topbar ringkas 67px).

---

## 4. Pola halaman internal (seragam di semua modul)

Setiap halaman modul mengikuti urutan ini:

1. **Header halaman** — judul 22px + deskripsi singkat; filter periode/aksi kanan bila perlu.
2. **Baris KPI / stat-card** — 3–4 kartu: ikon 38px berlatar tonal, label, nilai utama,
   delta ±% berwarna (hijau positif, merah negatif). Grid 4/5/3 kolom → 2 → 1.
3. **Card konten utama** — judul card + keterangan + toolbar:
   - search box dengan placeholder "Cari data..." + tombol clear `×`,
   - tombol **Filter** (secondary, dengan ikon corong),
   - tombol aksi primer: "Tambah Menu", "Tambah Resep", "Tambah Bahan", "Input Pembelian",
     "Input Pesanan", "Tambah Biaya", "Tambah Pengguna".
4. **Tabel data** — header uppercase `#f8fafc`, baris dipisah `#f0f1f2`, sel teks `#4b5563`,
   kolom status memakai badge, kolom aksi ikon 29px kanan: **lihat (eye), edit (pencil),
   hapus (trash merah)**.
5. **Footer tabel** — "Menampilkan 1–4 dari 24 data" + pagination (tombol 27px, halaman aktif biru).

### Khusus Pengelolaan Menu
- Toolbar + **grid kartu menu bergambar** (2 kolom di desktop) sebagai tampilan utama,
  bukan tabel. Foto 145px `object-fit: cover`, badge status di pojok, kategori uppercase biru,
  nama, harga biru, dan tombol aksi: `Detail` (lebar) + ikon edit + ikon hapus.
- Fallback foto wajib bila gambar kosong/gagal dimuat.

### Khusus Laporan Keuntungan
- `period-tabs`: Harian / Mingguan / **Bulan** (aktif) / Tahunan.
- `report-actions`: Cetak, PDF, **Excel** (primary).
- 4 KPI: Total Pendapatan, Modal Bahan Baku, Biaya Operasional, Laba Bersih.
- 2 grafik: "Pendapatan vs Pengeluaran" (batang biru/oranye) + "Tren Laba Bersih"
  (batang + garis hijau).
- Tabel "Ringkasan Keuangan": Komponen / Nominal / Porsi / Perubahan.
- Info box: `Rumus: Pendapatan − Modal bahan baku terjual − Biaya operasional = Laba bersih`.
- ⚠️ Angka, porsi, dan rumus **hanya ditampilkan** dari hasil backend — UI tidak menghitung.

### Khusus Rekomendasi Produksi
- Hero gradient navy→biru (`120deg #153577 → #2864cc`), eyebrow "ESTIMASI CERDAS",
  headline "Siapkan produksi dengan lebih percaya diri.", kartu estimasi semi-transparan
  ("68 porsi, 3 menu direkomendasikan").
- Grafik "Tren Penjualan 7 Hari" + **insight card kuning** ("Insight hari ini" + tombol
  "Lihat bahan dibutuhkan").
- Tabel rekomendasi: Nama Menu / Rata-rata / Estimasi / Rekomendasi / Ketersediaan Stok /
  Status (Mencukupi hijau, Perlu ditambah kuning).
- 3 kartu kebutuhan bahan (Cukup hijau / Bali kuning) dengan stok tersedia vs dibutuhkan.
- Rekomendasi adalah **estimasi**, bukan kepastian; jejak penyesuaian mengikuti PRD.

---

## 5. Spesifikasi frame Login (lengkap dari Figma)

Layout dua kolom: kiri ~50% navy, kanan ~50% putih. Mobile: vertikal, form tetap fokus.

### 5.1 Panel kiri (branding)
- Latar **gradient navy** (`#142d70` → `#153577` → `#1e3a8a`) + dekorasi lingkaran
  besar transparan `rgba(255,255,255,.04)` di bagian tengah-bawah.
- Baris atas: logo SmartFood (kotak putih, ikon rumah biru) + nama "SmartFood / Home";
  di kanan ada **pill outline transparan** bertuliskan "Manajemen kuliner rumahan".
- Eyebrow dengan ikon sparkle: **"Lebih rapi, lebih untung"** (uppercase, tracking lebar).
- Headline **two-tone**, 2 baris:
  - baris 1 putih: `Kelola Usaha Kuliner Lebih`
  - baris 2 biru muda `#93c5fd`: `Mudah dan Terorganisir`
- Paragraf pendukung putih/abu muda: "Satu ruang kerja untuk mengelola menu, stok,
  pesanan, hingga melihat keuntungan usaha Anda."
- Foto makanan di bawah (radius 12px) dengan **dua kartu overlay**:
  - pill hijau di kanan atas foto: `✓ Stok aman hari ini`
  - kartu putih di kiri bawah: ikon tren hijau + `Penjualan minggu ini` / **`+18,4%`**
- Caption kecil di bawah foto: "Foto oleh Baya Syafii di Unsplash".

### 5.2 Panel kanan (form)
- Eyebrow biru dengan dot: **`● SMARTFOOD HOME`**.
- H1: `Selamat Datang Kembali!`; subteks: "Masuk untuk mengelola usaha kuliner Anda."
- Field:
  - **Email** — input dengan ikon user, terisi `admin@smartfood.id`.
  - **Password** — input dengan ikon lock + tombol mata (tampil/sembunyikan).
  - Checkbox **"Ingat saya"** (tercentang) + link **"Lupa password?"** di kanan.
  - **"Masuk sebagai"** — select dengan ikon user, nilai `Admin` (⚠️ mock/demo;
    di produksi peran berasal dari akun yang login).
- Tombol primer full-width: **`Masuk ke Dashboard ›`**.
- Info box biru pucat: ikon shield centang + **`Akses aman sesuai peran`** +
  "Fitur yang tampil disesuaikan dengan hak akses akun Anda."
- Footer: `© 2025 SmartFood Home · Bantuan & Privasi`.

---

## 6. Komponen inti

### 6.1 Tombol
- `primary`: bg biru, teks putih, shadow biru, hover naik 1px; tinggi ±40px; tombol form utama 49px full-width.
- `secondary`: putih, border `#dfe3e8`.
- `danger`: merah `#dc2626` (khusus "Ya, hapus data").
- `link`: biru bold, transparan (mis. "Lihat semua", "Kelola stok", "Lupa password?").
- Label kata kerja jelas: Tambah Menu, Simpan, Batal, Hapus, Input Pesanan. Sediakan state
  hover/focus/active/disabled/loading; cegah submit ganda.

### 6.2 Card & stat-card
- Card: putih, border `#ecece8`, radius 14px, padding ±19px, `shadow-card`;
  head = judul 13px + keterangan + aksi kanan.
- Stat-card: ikon 38px tonal, label, nilai, delta ±%.

### 6.3 Tabel & badge
- Badge: pill 99px, dot 5px `currentColor`, font bold, padding 5px 8px, **disertai teks status**.
- Pemetaan status (PRD §6.1, label Bahasa Indonesia):

| Status | Badge |
|---|---|
| `pending` / Menunggu | kuning |
| `processing` / Diproses | kuning |
| `completed` / Selesai / Disetujui | hijau |
| `cancelled` / Ditolak / Dibatalkan | merah |
| Stok: Aman / Tersedia / Cukup | hijau |
| Stok: Menipis / Perlu ditambah | kuning |
| Stok: Habis / Kritis | merah |
| Netral / informasi | biru / ungu / abu |

### 6.4 Form & modal
- Label bold 12px di atas input; input 48px radius 10px dengan ikon; fokus border biru + ring `#dbeafe`.
- Modal: backdrop `rgba(15,23,42,.56)` + blur 3px, panel radius 16px;
  header = ikon kotak biru pucat + judul + subteks "Lengkapi informasi dengan benar." + tombol X;
  body = `Nama ...`, grid 2 kolom `Kategori` + `Status` (Aktif), textarea `Keterangan`,
  untuk Menu/Pembelian ada upload box dashed "Unggah foto atau nota — PNG, JPG hingga 5 MB";
  footer `#f8fafc` rata kanan: `Batal` + `Simpan ...` (dengan ikon centang).
- Modal detail: foto besar + NAMA / STATUS (badge) / HARGA-TOTAL / TERAKHIR DIPERBARUI + `Batal`.
- Modal hapus: ikon trash merah dalam lingkaran, "Hapus data ini?", peringatan
  "Data yang telah dihapus tidak dapat dikembalikan. Pastikan Anda memilih data yang benar.",
  `Batal` + `Ya, hapus data`.
- Escape menutup modal jika tidak memproses; fokus keyboard terjaga; modal tidak tertutup saat
  penyimpanan gagal — tampilkan error.

### 6.5 Toast
- Kanan-bawah, ikon lingkaran hijau, judul + keterangan (sukses/gagal/validasi),
  `shadow-toast`. Notifikasi menyebut hasil dengan jelas, bukan hanya warna.

### 6.6 Ikon
- Set **Lucide** (line 16–18px). Di Blade gunakan paket ikon Lucide yang tersedia —
  jangan menulis SVG manual per halaman.

---

## 7. State wajib (setiap modul)

Default · Loading · Empty (dengan CTA bila diizinkan) · Error (ada retry) · Success ·
Disabled · Validation (pesan dekat field) · Confirmation (aksi destruktif/transaksi penting).
Dashboard/laporan **tidak boleh** memakai angka dummy saat data belum ada — gunakan empty state.

---

## 8. Aksesibilitas & konten

- Kontras memadai; semua input punya label; semua tombol punya nama untuk pembaca layar.
- Status tidak hanya warna — selalu ada teks. Navigasi keyboard + focus state terlihat.
- Heading hierarkis; pesan validasi spesifik; hindari teks <11px.
- Mata uang `Rp15.000` tanpa spasi (singkatan `Rp 48,6 jt` boleh untuk ringkasan).
- Tanggal Indonesia (`26 Mei 2025`, jam `09.42`), `Asia/Jakarta`.
- Foto menu/nota via Laravel Storage, `object-fit: cover`, dioptimalkan ukurannya.

---

## 9. ⚠️ Catatan penting: mock Figma vs aturan bisnis PRD

Screenshot Figma memakai **field modal generik** (`Nama ...`, `Kategori`, `Status Aktif`,
`Keterangan`, upload nota) untuk hampir semua modul. Ini **template mock**, bukan spesifikasi data.

- **Jangan** menyalin field modal mock apa adanya ke implementasi.
- Field, status, dan perhitungan **wajib mengikuti PRD**:
  - Penjualan: multi-item, metode pembayaran wajib, **penjualan manual tanpa item tidak
    mengurangi stok**, HPP manual ditandai *unavailable* (bukan nol), snapshot nama/harga/HPP
    ke sale_items, konfirmasi sebelum transaksi selesai.
  - Stok: setiap perubahan lewat central stock service + `stock_movements`
    (`balance_before`/`balance_after`), potong stok idempoten sekali dalam transaksi DB.
  - Biaya & Laporan: kategori biaya, periode, formula laba dari backend.
- Desain **hanya menampilkan** hasil perhitungan backend; UI tidak menghitung sendiri.
- Hak akses divalidasi di backend (policy/middleware), bukan hanya disembunyikan di UI.

---

## 10. Kriteria penerimaan visual

- [ ] Login dua panel: branding navy + form putih, sesuai §5 (termasuk headline two-tone & kartu overlay).
- [ ] Sidebar navy dengan navigasi aktif jelas; topbar breadcrumb + judul + notifikasi + profil.
- [ ] Latar krem, card putih, border halus, aksen biru.
- [ ] Hierarki judul halaman → deskripsi → aksi utama → KPI → konten konsisten.
- [ ] Tabel: search, filter, badge status, aksi ikon, pagination; grid kartu foto untuk Menu.
- [ ] Modal Tambah/Edit/Detail/Hapus mengikuti §6.4.
- [ ] Dashboard & laporan: KPI + grafik (biru/oranye, garis hijau) + empty state.
- [ ] Semua state di §7 terpenuhi.
- [ ] Hak akses UI mengikuti role tetapi tetap divalidasi backend (403).
- [ ] Responsif desktop/tablet/mobile; tanpa horizontal overflow tidak sengaja.
- [ ] Foto, angka, status, dan data berasal dari sumber data aplikasi yang benar.

---

## 11. Riwayat

| Versi | Tanggal | Perubahan |
|---|---|---|
| v1 (`design_system.md`) | 2026-11-16 | Ekstraksi awal Figma Make; token + komponen + konvensi. |
| **v2 (dokumen ini)** | 2026-11-23 | Disusun ulang dari screenshot panel-per-panel; tambah spesifikasi login lengkap, warna grafik, pola tiap modul, state, dan catatan pemisahan mock vs aturan bisnis PRD. |
