<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Aturan Bisnis IzinKu
    |--------------------------------------------------------------------------
    |
    | Nilai-nilai di bawah dapat diubah tanpa menyentuh kode sumber.
    | Aturan yang belum ditetapkan mengambil nilai default yang aman.
    |
    */

    // Batas maksimal durasi izin per pengajuan (hari).
    'batas_durasi_izin_hari' => 3,

    // Batas ketidakhadiran berturut-turut sebelum peringatan (hari).
    'ketidakhadiran_maksimal_hari' => 2,

    // Definisi "lebih dari 2 hari": "kalender" atau "sekolah".
    'definisi_hari_ketidakhadiran' => 'kalender',

    // Role yang berwenang memberi ACC (persetujuan) izin.
    'role_pemberi_acc' => ['guru_piket'],

    // Validasi berkas surat sakit.
    'surat_sakit' => [
        'tipe_diizinkan' => ['pdf', 'jpg', 'jpeg', 'png'],
        'ukuran_maksimal_kb' => 2048,
    ],

];
