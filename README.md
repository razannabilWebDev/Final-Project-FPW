# SmartFood Home

Aplikasi web manajemen usaha kuliner rumahan (UMKM): menu/resep, stok,
pembelian, penjualan, biaya operasional, laporan keuntungan, dan rekomendasi
produksi. Dibangun dengan Laravel 13 + Blade + Tailwind CSS 4.

> **Dokumen tim (WAJIB dibaca sebelum coding):**
> - `prd.md` — spesifikasi produk, sumber kebenaran tunggal (matriks hak
>   akses §3.2, aturan bisnis §6, rancangan database §7)
> - `TASK_INSTRUCTIONS.md` — pembagian tugas 4 anggota, kontrak integrasi,
>   aturan git
> - `docs/database.md` — baseline tabel/kolom per pemilik (**draft**)
> - `docs/permissions.md` — usulan gate/middleware (**draft, final say Anggota 1**)
> - `docs/architecture.md` — konvensi penamaan & pola kode (**draft**)
> - `docs/decisions.md` — 10 keputusan wajib sebelum integrasi (**usulan, belum final**)
> - `docs/design_system.md` — token & komponen UI dari Figma
> - `docs/implementation-plan.md` — status & checklist progres tim

## Status saat ini (update 2026-11-25)

Seluruh modul sudah terintegrasi dan diuji end-to-end via Chrome. Test suite:
**169/169 lulus (902 assertion)**; `npm run build` sukses.

**Akun demo** ( password: `password` ):

| Role | Email |
|---|---|
| Admin | `admin@smartfood.test` |
| Pengelola | `pengelola@smartfood.test` |
| Staf | `staf@smartfood.test` |

Fitur utama: auth & permission per role, CRUD menu/resep/HPP/bahan baku,
stok terpusat + ledger `stock_movements`, pembelian & verifikasi, pencatatan
penjualan (pilih menu dari dropdown; harga snapshot dari master), biaya
operasional, laporan keuntungan + ekspor PDF/Excel, rekomendasi produksi,
serta notifikasi ringkasan kondisi aplikasi di navbar.

> Catatan lingkungan: mesin ini memakai **MySQL** (`smartfood`) untuk dev;
> test memakai SQLite `:memory:` via `phpunit.xml` (lihat `docs/database.md` §5).
> `php artisan migrate --seed` menghasilkan data dummy idempoten.

## Setup (sekali per mesin)

1. `git clone` → `composer setup` (install deps, buat `.env`, app key, migrate, npm build)
2. Pastikan PHP 8.3+ dan Node terpasang; local dev memakai SQLite bawaan
   sampai keputusan driver database tim disepakati (lihat `docs/database.md` §5)
3. AI agent: [OpenCode](https://opencode.ai) — skill & tooling sudah
   ter-commit di repo (`.opencode/`, `.agents/`), aktif otomatis
4. Jalankan aplikasi: `composer dev` · Jalankan test: `composer test`

## Yang harus tim lakukan SEKARANG (Tahap 0–1)

Sebelum tiap anggota mulai coding modulnya masing-masing:

- [ ] **Semua anggota:** review & setujui 5 dokumen kontrak di `docs/` —
      keberatan disampaikan SEBELUM migration pertama di-merge
- [ ] **Semua anggota:** putuskan 10 keputusan di `docs/decisions.md`
      (D1–D10) dan isi tabel persetujuan
- [ ] **Anggota 1:** finalisasi `docs/permissions.md` (nama gate/middleware),
      lalu umumkan ke grup
- [ ] **Semua anggota:** sepakati dependency bersama (export PDF/Excel, ikon
      Lucide untuk Blade) — jangan `composer require` sepihak
- [ ] **Satu orang:** bangun layout + komponen Blade bersama dari
      `docs/design_system.md` (sidebar, topbar, card, badge, DataTable,
      modal, toast); anggota lain JANGAN meniru komponen sendiri
- [ ] **Semua anggota:** kerjakan di branch
      `feature/member-N-deskripsi`, tidak pernah commit langsung ke `master`

## Prompt awal untuk AI agent (tiap anggota)

Salin template ini sebagai prompt PERTAMA di sesi agent Anda
(dari TASK_INSTRUCTIONS §7.1), ganti nomor anggota:

```text
Saya adalah Anggota [N] di proyek ini. Modul saya: [sesuai TASK_INSTRUCTIONS §2].
Baca prd.md, TASK_INSTRUCTIONS.md, dan docs/ sebelum bekerja.
Batasi pekerjaan ke modul saya; jangan menyentuh tabel, file, atau
service milik anggota lain tanpa koordinasi. Ikuti konvensi di
docs/architecture.md dan AGENTS.md.
```

Aturan agar agent keempat anggota tetap selaras:

1. **Rujuk dokumen, bukan ingatan** — sebutkan file kontrak spesifik di
   prompt (mis. "ikuti `docs/database.md` untuk nama kolom").
2. **Jangan ubah file bersama** (`composer.json`, layout bersama, migration
   anggota lain, dokumen kontrak) tanpa koordinasi — agent juga tahu ini
   dari `AGENTS.md`.
3. **Keputusan lintas modul** (mis. mengubah D1–D6 di `docs/decisions.md`)
   harus selalu disebut "perlu persetujuan tim, jangan anggap final".
4. **Catat prompt penting** di `prompt_history.md` (aturan lengkap di `AGENTS.md`).
5. **Sebelum coding**, agent wajib cek `docs/implementation-plan.md` agar
   tidak mengerjakan fase di luar urutan (PRD §11).

## Git workflow (ringkas TASK_INSTRUCTIONS §6)

- Branch: `feature/member-1-auth`, `feature/member-2-menu-recipes`,
  `feature/member-3-stock-purchases`, `feature/member-4-sales-expenses-reports`.
- Conventional commits: `feat(sales): ...`, `fix(report): ...`,
  `test(purchase): ...`.
- Pull request wajib direview sebelum merge ke `master`.
- File bersama tidak boleh diubah sepihak (TASK_INSTRUCTIONS §6.4).
