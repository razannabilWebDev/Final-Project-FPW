---
paths:
  - prompt_history.md
---

# Prompt History Md

## Log setiap prompt pengguna ke prompt_history.md
Setiap prompt baru dari pengguna wajib segera dicatat sebagai satu entri teratas baru di prompt_history.md — termasuk balasan pendek seperti "gas eksekusi". Format: timestamp WIB, model AI, teks prompt utuh dalam blok `text` (buang `<system-reminder>` dan output tool), lalu ringkasan hasil. Jawaban via form pertanyaan ditaruh di bullet `- **Jawaban via form pertanyaan:**` terpisah, bukan di dalam blok prompt. Penulis entri = pengirim prompt (Anggota 1 untuk sesi di mesin ini). Berlaku untuk semua sesi/agent; jangan menunda sampai akhir sesi dan jangan melewati satu prompt pun.
