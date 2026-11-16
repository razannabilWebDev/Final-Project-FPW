# Design System — SmartFood Home

> Sumber: desain Figma Make "SmartFood Home Management Dashboard"
> (fileKey `xpI3m9AQS5hDLPVqD2RYcG`, diekstrak 2026-11-16).
> Stack implementasi: **Blade + Tailwind CSS 4** — token di bawah dipetakan ke
> CSS variables `@theme` agar bisa dipakai sebagai utility class.
> Dokumen ini aset bersama tim (TASK_INSTRUCTIONS §6.4) — koordinasi sebelum perubahan.

## 1. Design Tokens

### 1.1 Warna inti

| Token | Hex | Pemakaian |
|---|---|---|
| `--color-brand` | `#2563eb` | Primer: tombol utama, link, aksen aktif, ikon |
| `--color-brand-hover` | `#1d4ed8` | Hover tombol primer, teks badge biru |
| `--color-navy` | `#142d70` | Sidebar |
| `--color-navy-mid` | `#153577` | Panel login (kiri), hero rekomendasi produksi |
| `--color-navy-warm` | `#1e3a8a` | Teks judul di permukaan gelap, gradient welcome card |
| `--color-surface` | `#ffffff` | Card, tabel, modal, topbar |
| `--color-cream` | `#fff9ed` | Background aplikasi (app-shell, body) |
| `--color-line` | `#ecece8` | Border card & garis pemisah halus (variasi: `#e8e8e3`, `#f0f1f2`, `#dfe3e8` untuk input) |
| `--color-text` | `#1f2937` | Teks utama |
| `--color-text-secondary` | `#374151` / `#4b5563` | Teks form, isi tabel |
| `--color-muted` | `#6b7280` | Teks sekunder/keterangan |
| `--color-muted-light` | `#9ca3af` | Placeholder, ikon pasif |

### 1.2 Warna semantik (pewarnaan "tonal": warna teks di atas latar terangnya)

| Semantik | Teks/ikon | Latar |
|---|---|---|
| Sukses / aktif | `#16a34a` (label `#15803d`) | `#dcfce7` |
| Peringatan | `#d97706` (label `#b45309`) | `#fef3c7` |
| Bahaya | `#dc2626` (label `#b91c1c`) | `#fee2e2` |
| Info | `#2563eb` (label `#1d4ed8`) | `#dbeafe` |
| Ungu (statistik) | `#7c3aed` | `#ede9fe` |
| Cyan (statistik) | `#0891b2` | `#cffafe` |
| Netral | `#64748b` | `#f1f5f9` |

### 1.3 Tipografi

- **Font:** `Inter` (Google Fonts), weight 400/500/600/700/800.
- **Skala terpakai:** `clamp(34px→54px)` hero login; H1 topbar 17px; H2 halaman 22px; judul card 13–14px; isi tabel 9.5px.
- **Gaya:** judul dengan letter-spacing negatif (`-0.02em`…`-0.045em`); label kategori *eyebrow* uppercase + `letter-spacing: 0.05–0.14em`.
- ⚠️ Mock menggunakan ukuran <10px untuk isi tabel/label — di implementasi Blade, **normalisasi minimal 11px** (PRD NFR-11 aksesibilitas). Jangan meniru ukuran 8px.

### 1.4 Radius, shadow, focus

| Token | Nilai |
|---|---|
| Radius input/tombol | `8px` |
| Radius card | `13–16px` (card 14, modal 16, food-card 12) |
| Radius pill/badge/progress | `99px` |
| Radius sidebar aktif | `8px` + indikator kiri `3px × 21px` |
| Shadow card | `0 8px 24px rgba(30,58,138,0.06)` |
| Shadow modal | `0 25px 80px rgba(15,23,42,0.3)` |
| Shadow toast | `0 15px 45px rgba(15,23,42,0.15)` |
| Focus state | `outline 3px #bfdbfe, offset 1px`; input fokus: border biru + ring `0 0 0 3px #dbeafe` |

### 1.5 Pemetaan ke Tailwind 4 (`resources/css/app.css`)

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
  --color-cream: #fff9ed;
  --color-line: #ecece8;
  --color-success: #16a34a;
  --color-success-soft: #dcfce7;
  --color-warning: #d97706;
  --color-warning-soft: #fef3c7;
  --color-danger: #dc2626;
  --color-danger-soft: #fee2e2;
  --color-info-soft: #dbeafe;
  --shadow-card: 0 8px 24px rgb(30 58 138 / 0.06);
}
```

Setelah itu utility seperti `bg-cream`, `text-brand`, `border-line`, `shadow-card` tersedia otomatis.

## 2. Status → Warna Badge

Badge: pill `99px`, dot 5px `currentColor`, font bold, padding `5px 8px`.

| Status transaksi (PRD §6.1) | Badge | Tone CSS dari mock |
|---|---|---|
| `pending` (Menunggu) | kuning | `.badge.orange` |
| `processing` (Diproses) | kuning | `.badge.orange` |
| `completed` (Selesai / Disetujui) | hijau | `.badge` (default) |
| `cancelled` (Ditolak / Dibatalkan) | merah | `.badge.red` |
| Informasi/netral | biru / ungu / abu | `.badge.blue` / `.purple` / `.gray` |

Pemetaan sama untuk pembelian (Menunggu/Disetujui/Ditolak) — konsistenkan dengan istilah label Bahasa Indonesia dari PRD.

## 3. Komponen Inti

- **Tombol** — `primary` (bg biru, teks putih, shadow biru, hover naik 1px), `secondary` (putih, border `#dfe3e8`), `danger` (merah), `link` (biru bold, transparan). Tinggi standar ±40px; tombol form utama full-width 49px.
- **Card** — putih, border `#ecece8`, radius 14px, padding ±19px, `shadow-card`. Head section: judul 13px + keterangan 9px + aksi kanan.
- **Stat card** — ikon 38px berlatar tonal (blue/green/orange/purple/cyan/red), label 9px, nilai 18px, delta ±% (positif hijau). Grid 4/5/3 kolom → 2 → 1.
- **Tabel** — header `#f8fafc` uppercase 8px*, baris dipisah `#f0f1f2`, sel teks `#4b5563`, aksi ikon 29px kanan (lihat/edit/hapus, hapus merah). Pagination: tombol 27px, halaman aktif biru.
- **Sidebar** — navy, lebar 252px, item 39px radius 8px; aktif: bg `#3154a5`, indikator `#93c5fd` kiri; label grup uppercase 9px; profil mini + logout (`#fca5a5`). Di ≤900px jadi drawer + scrim.
- **Topbar** — putih 76px sticky: breadcrumb, judul, switch role (hanya untuk demo/mock — di implementasi nyata role berasal dari akun, bukan dropdown), notifikasi (dot merah), profil.
- **Form** — label bold 12px, input 48px radius 10px dengan ikon; focus border biru + ring `#dbeafe`. Modal form: input 10px*, `form-grid` 2 kolom, upload box dashed `#b9c5d3` 90px.
- **Modal** — backdrop `rgba(15,23,42,.56)` + blur 3px, panel 16px radius, footer `#f8fafc` rata kanan. Confirm dialog: ikon lingkaran merah 52px + tombol danger.
- **Toast** — kanan-bawah, ikon lingkaran hijau, judul + keterangan.
- **Hero panel** — gradient navy→biru (`115deg #214fa9→#1e3a8a` atau `120deg #153577→#2864cc`), teks putih, dekorasi lingkaran `rgba(255,255,255,.04)`, kartu target/forecast semi-transparan dengan progress bar `#93c5fd`.
- **Food card** — foto 145px, badge di pojok, kategori uppercase biru, harga biru, aksi `secondary` kecil.
- **Insight/peringatan card** — `#fffbeb` + border `#fde68a` (contoh: rekomendasi produksi).
- **Ikon:** set **Lucide** (line icon 16–18px). Di Blade, gunakan paket ikon Lucide yang tersedia untuk Laravel/Blade — jangan menulis SVG manual per halaman.

## 4. Layout Shell

```
sidebar (fixed, 252px, navy) │ workspace
                             ├─ topbar (76px, sticky)
                             └─ main-content (padding 26px 30px, max-width 1600px)
```

- **Navigasi per role** (filter `admin: true` hanya untuk Admin): Dashboard, Kelola Pengguna (Admin), Pengelolaan Menu, Pengelolaan Resep, Stok Bahan Baku, Pembelian Bahan Baku, Pencatatan Penjualan, Biaya Operasional, Laporan Keuntungan (Admin), Rekomendasi Produksi. Selalu tampilkan: Ganti Password, profil, Keluar.
- **Halaman awal setelah login:** dashboard per role (PRD §8.3). Mock hanya punya dashboard Admin — isi dashboard Pengelola/Staf mengikuti PRD §8.3 dengan komponen yang sama.
- Breakpoint: `1180px`, `900px` (sidebar drawer, grid 2 kolom), `640px` (single column, topbar ringkas 67px).

## 5. Konvensi Konten (wajib)

- **Bahasa UI:** Indonesia (nav mock sudah benar; gunakan istilah PRD: *Kelola Pengguna*, *Pencatatan Penjualan*, dst.).
- **Mata uang:** mock menulis `Rp 456.000` dan singkatan `Rp 48,6 jt` — di implementasi ikuti PRD §8.1: **`Rp15.000` (tanpa spasi)**; singkatan jt boleh untuk ringkasan.
- **Tanggal:** format Indonesia (`26 Mei 2025`, jam `09.42`), zona `Asia/Jakarta`.
- **Konfirmasi sebelum aksi destruktif** (hapus/nonaktif/batalkan) via confirm dialog — bukan langsung eksekusi (PRD §8.1).
- **Foto produk/nota:** mock memakai placeholder Unsplash — di implementasi simpan via Laravel Storage, tampil dengan `object-fit: cover`.

## 6. Dampak ke Anggota 4 (referensi cepat)

Modul penjualan/biaya/laporan memakai komponen yang sama: toolbar + search box, DataTable + badge status, modal input transaksi multi-item (`form-grid`, pilih menu, metode pembayaran), dashboard kartu keuangan (stat-card), period-tabs untuk filter laporan, dan tombol ekspor PDF/Excel pada `report-actions`. Snapshot HPP dan pemisahan penjualan manual tetap aturan data PRD — desain hanya menampilkannya.
