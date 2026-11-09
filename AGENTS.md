# AGENTS.md — Panduan untuk AI Coding Agent (IzinKu)

File ini berisi konteks dan aturan yang harus diikuti agent (Claude Code, Copilot, Cursor, dll.) saat bekerja di repo ini. Baca juga `COLLABORATORS.md` untuk aturan tim dan PRD untuk detail kebutuhan.

## Ringkasan Proyek

**IzinKu** adalah sistem informasi berbasis web untuk perizinan dan monitoring kehadiran siswa.

- Wali murid mengajukan izin (termasuk unggah surat sakit) dan mengonfirmasinya lewat WhatsApp.
- Guru piket memeriksa dan menyetujui/menolak (ACC).
- Sistem mendeteksi siswa yang belum fingerprint setelah pukul 07.00 dan siswa yang tidak hadir lebih dari 2 hari.
- Guru, wali murid, dan admin melihat riwayat; admin dan guru melihat rekap dengan filter.

Metode pengembangan: Agile/Scrum. Bahasa antarmuka dan teks pengguna: **Bahasa Indonesia**.

## Tech Stack

- Laravel (PHP 8.2+), MySQL/MariaDB
- Laravel Breeze (Blade) + Tailwind CSS + Alpine.js
- `spatie/laravel-permission` untuk role dan permission
- Laravel Queue (notifikasi) dan Scheduler (tugas terjadwal)
- Gateway WhatsApp pihak ketiga (belum ditentukan; panggil lewat HTTP client Laravel)
- Laporan: `maatwebsite/excel` dan `barryvdh/laravel-dompdf` (jika dipakai)

Cek `composer.json` dan `package.json` untuk versi pasti sebelum menulis kode. Jangan menambah paket baru tanpa persetujuan tim.

## Perintah Umum

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate:fresh --seed     # reset database dengan data dummy
php artisan storage:link
npm install && npm run dev           # frontend (terminal terpisah)
php artisan serve
php artisan test                     # jalankan tes
php artisan schedule:work            # uji tugas terjadwal secara lokal
php artisan queue:work               # uji antrean notifikasi
./vendor/bin/pint                    # format kode (jika Pint terpasang)
```

## Peran Pengguna (Role)

| Role | Hak akses |
|---|---|
| `wali_murid` | Login dengan nomor WhatsApp; ajukan dan konfirmasi izin anaknya; lihat riwayat dan feedback anaknya saja |
| `guru` | Login; pantau perizinan dan kehadiran; lihat siswa belum hadir; beri feedback; lihat riwayat |
| `guru_piket` | Seperti guru, ditambah memeriksa, menyetujui (ACC), atau menolak izin |
| `admin` | Kelola akun wali murid, guru, dan data siswa; lihat laporan rekap |

Aturan: wali murid **tidak boleh** melihat atau mengubah data siswa yang bukan anaknya. Selalu terapkan pengecekan role dan kepemilikan data lewat middleware, policy, atau gate. Jangan hanya menyembunyikan menu di tampilan.

## Aturan Bisnis (wajib dipatuhi)

1. Nomor WhatsApp adalah identitas unik akun wali murid dan saluran notifikasi.
2. Izin karena **sakit wajib** melampirkan surat sakit; pengajuan tanpa dokumen ditolak.
3. Durasi/tanggal izin dibatasi sesuai aturan sekolah; sistem menolak atau memberi peringatan jika melewati batas. Batas maksimal disimpan sebagai konfigurasi, bukan angka yang ditulis langsung di banyak tempat.
4. Izin **belum boleh diproses** guru sebelum dikonfirmasi wali murid.
5. Status ACC hanya: `menunggu`, `disetujui`, `ditolak`. Penolakan disertai alasan. Setiap perubahan status mencatat siapa dan kapan.
6. Siswa yang belum fingerprint setelah 07.00 dianggap belum hadir, kecuali punya izin berstatus `disetujui`.
7. Ketidakhadiran lebih dari 2 hari berturut-turut memicu peringatan ke guru dan notifikasi ke wali murid.

## Pemetaan Kebutuhan (PRD)

| ID | Fitur | Task Sprint 1 |
|---|---|---|
| FR-01 | Login nomor WhatsApp (wali murid) | Task 1 |
| FR-02 | Login guru/admin berdasarkan role | Task 2 |
| FR-03 | Kelola akun dan data siswa (admin) | belum ada penanggung jawab |
| FR-04 | Form pengajuan izin | Task 3 |
| FR-05 | Upload dan validasi surat sakit | Task 4 |
| FR-06 | Aturan batas durasi izin | Task 5 |
| FR-07 | Konfirmasi izin oleh wali murid | Task 6 |
| FR-08, FR-09 | Verifikasi dan ACC izin guru piket | Task 7 |
| FR-10 | Feedback guru | Sprint berikutnya |
| FR-11 | Deteksi belum fingerprint setelah 07.00 | Task 8 |
| FR-12, FR-13 | Peringatan absen > 2 hari dan notifikasi WhatsApp | Task 9 |
| FR-14, FR-15 | Riwayat dan rekap perizinan | Task 10 |

Saat diminta mengerjakan task, cocokkan dengan ID di atas dan penuhi kriteria penerimaan di PRD.

## Konvensi Kode

- Ikuti gaya Laravel dan PSR-12; format dengan Pint jika tersedia.
- Nama class, method, dan variabel dalam **Bahasa Inggris**; teks yang tampil ke pengguna, pesan validasi, dan notifikasi dalam **Bahasa Indonesia**.
- Gunakan Eloquent, migrasi, dan seeder. Hindari query mentah kecuali perlu.
- Validasi lewat Form Request, bukan di dalam controller.
- Logika bisnis (misalnya aturan durasi izin, deteksi absen) ditaruh di class Service/Action, bukan di controller atau view.
- Tugas terjadwal (deteksi 07.00, absen > 2 hari) didaftarkan di Scheduler; pengiriman notifikasi lewat Queue dengan pencatatan status kirim dan retry.
- Unggahan surat sakit disimpan di disk privat, bukan di folder publik, dan hanya bisa diakses pihak berwenang. Batasi tipe dan ukuran berkas.
- Tulis tes (Feature test) untuk aturan bisnis penting: wajib surat sakit, konfirmasi sebelum diproses, pembatasan akses wali murid, deteksi absen > 2 hari.
- Tampilan harus responsif; wali murid terutama memakai ponsel.

## Keamanan dan Privasi

- Data siswa dan surat sakit adalah data pribadi sensitif.
- **Jangan commit `.env`**, kunci API, kata sandi, atau token.
- **Jangan masukkan data nyata** (nama siswa, nomor WhatsApp, surat sakit) ke repo, seeder, atau tes. Pakai data dummy.
- Jangan menulis data sensitif ke log.
- Lindungi semua form dengan CSRF dan semua route dengan autentikasi dan otorisasi.

## Alur Git (ringkas, lengkap di COLLABORATORS.md)

- Jangan push langsung ke `main`. Satu task = satu branch: `feature/<nama>-<task>` atau `fix/<nama>-<masalah>`.
- Commit memakai awalan: `feat`, `fix`, `docs`, `refactor`, `chore`.
- Judul PR: `Task N (FR-XX): <deskripsi>`; minimal 1 approval anggota lain; merge akhir oleh Rapik dengan Squash and merge.
- Migrasi: jangan edit migrasi yang sudah ter-merge, buat migrasi baru. Beri tahu tim sebelum mengubah tabel bersama (`users`, `siswa`, `izin`).
- Seeder role dimiliki Irul; jangan membuat definisi role sendiri di luar seeder tersebut.

## Yang Tidak Boleh Dilakukan Agent

- Mengubah branch `main` secara langsung atau force push.
- Menambah paket atau mengubah arsitektur tanpa persetujuan tim.
- Mengubah aturan bisnis di atas tanpa konfirmasi ke pemilik task atau Rapik.
- Mengarang data, endpoint, atau detail integrasi WhatsApp/fingerprint yang belum ditentukan. Jika belum jelas, tanyakan atau buat antarmuka (interface) dan implementasi tiruan (stub) yang jelas ditandai.
- Menghapus atau menimpa pekerjaan anggota lain di luar lingkup task.

## Hal yang Belum Ditentukan

- Gateway WhatsApp yang dipakai dan cara autentikasinya (OTP atau tidak).
- Format dan sumber data fingerprint (API, CSV, atau basis data).
- Batas maksimal durasi izin per pengajuan, dan apakah izin susulan diperbolehkan.
- Siapa yang berwenang memberi ACC selain guru piket.
- Definisi "lebih dari 2 hari": hari kalender atau hari sekolah.

Untuk item di atas, jangan berasumsi: tanyakan, atau gunakan konfigurasi dan stub yang mudah diganti.
