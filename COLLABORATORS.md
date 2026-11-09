# Kolaborator & Aturan Kerja Tim IzinKu

IzinKu adalah Sistem Informasi Perizinan dan Monitoring Kehadiran Siswa berbasis Agile (Scrum). Dokumen ini menjelaskan siapa saja yang berkontribusi, tanggung jawab masing-masing, dan aturan kerja di repositori ini.

## Anggota Tim

| Anggota | GitHub | Akses | Peran | Tanggung jawab (Sprint 1) |
|---|---|---|---|---|
| Rapik | @username-rapik | Owner | Pemimpin tim, merge akhir ke `main` | Task 4: upload & validasi surat sakit<br>Task 7: verifikasi & ACC izin guru piket<br>Task 8: deteksi siswa belum fingerprint |
| Irul | @kairul-azzam | Write | Developer (autentikasi, role, notifikasi) | Task 1: login WhatsApp wali murid<br>Task 2: login guru/admin berdasarkan role<br>Task 9: notifikasi WhatsApp & peringatan absen > 2 hari |
| Tristan | @zentristan-vitadi | Write | Developer (pengajuan izin) | Task 3: form pengajuan izin<br>Task 5: aturan batas waktu/durasi izin |
| Alpat | @alfathfachrizy | Write | Developer (konfirmasi & pelaporan) | Task 6: konfirmasi izin oleh wali murid<br>Task 10: halaman riwayat & rekap perizinan |

> Ganti `@username-rapik` dengan username GitHub pemilik repo.

## Alur Branch

- `main` selalu stabil dan siap didemokan. **Dilarang push langsung ke `main`.**
- Satu task = satu branch, dibuat dari `main` terbaru.
- Format nama branch: `feature/<nama>-<task>` atau `fix/<nama>-<masalah>`
  - Contoh: `feature/irul-login-wa`, `feature/tristan-form-izin`, `fix/alpat-riwayat-filter`
- Sebelum membuka PR, jalankan `git pull origin main` dan selesaikan konflik di branch sendiri.
- Hapus branch setelah merge.

## Aturan Pull Request

1. Judul PR memuat nomor task dan ID kebutuhan, contoh: `Task 1 (FR-01): Login nomor WhatsApp`.
2. Deskripsi PR berisi apa yang dibuat, cara mengujinya, dan tangkapan layar bila ada perubahan tampilan.
3. Minimal **1 approval** dari anggota lain. Tidak boleh meng-approve PR sendiri.
4. Rapik melakukan merge akhir ke `main` dengan **Squash and merge**.
5. Review dilakukan maksimal 1 hari kerja.
6. Hubungkan PR dengan issue menggunakan `Closes #nomor`.

## Aturan Commit

Gunakan awalan jenis perubahan:

| Awalan | Dipakai untuk | Contoh |
|---|---|---|
| `feat` | Fitur baru | `feat: tambah login nomor WhatsApp` |
| `fix` | Perbaikan bug | `fix: validasi durasi izin melewati batas` |
| `docs` | Dokumentasi | `docs: perbarui README instalasi` |
| `refactor` | Perapian kode | `refactor: pisahkan service notifikasi` |
| `chore` | Konfigurasi/dependensi | `chore: pasang spatie laravel-permission` |

## Aturan Khusus Laravel

- **Jangan commit `.env`.** Pastikan ada di `.gitignore`; bagikan konfigurasi lewat `.env.example`. Kunci API WhatsApp dan kata sandi database tidak boleh masuk repo.
- **Migrasi:** satu migrasi dibuat oleh satu orang. Beri tahu grup sebelum mengubah tabel bersama (`users`, `siswa`, `izin`). Jangan edit migrasi yang sudah di-merge; buat migrasi baru.
- **Role & permission:** seeder role (wali murid, guru, guru piket, admin) dimiliki Irul. Anggota lain memakai role tersebut.
- **Data nyata:** jangan masukkan data siswa, nomor WhatsApp, atau surat sakit asli ke repo. Gunakan data dummy lewat seeder.
- **Dependensi baru:** tambahkan lewat PR dan beri tahu tim, karena `composer.lock` / `package-lock.json` ikut berubah.
- **Berkas unggahan:** folder `storage` tidak di-commit. Setiap anggota menjalankan `php artisan storage:link` sendiri.

## Pelacakan Pekerjaan

- Setiap task Sprint Backlog dibuat sebagai GitHub Issue dengan label `sprint-1` dan ID kebutuhan (misalnya `FR-05`).
- Papan GitHub Projects memakai kolom **Pending → In Progress → Review → Done**.
- Pemilik task memperbarui statusnya sendiri.

## Definition of Done

- Kriteria penerimaan pada PRD untuk FR terkait terpenuhi.
- Berjalan di lokal tanpa error, dan `php artisan migrate:fresh --seed` sukses.
- Tampilan diuji di ponsel dan desktop.
- Tidak ada kunci rahasia atau data nyata di kode.
- Sudah di-review dan di-approve satu anggota lain.

## Catatan Akses

Repositori berada di akun pribadi, sehingga semua kolaborator memiliki akses Write. Karena itu, proteksi branch `main` (wajib PR, minimal 1 approval, blokir force push) diatur oleh Rapik di **Settings → Rules/Branches**, dan aturan di atas dijalankan sebagai kesepakatan tim.
