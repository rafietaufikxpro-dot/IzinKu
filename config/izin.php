<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Batas Durasi Izin (Hari)
    |--------------------------------------------------------------------------
    | Batas maksimal hari per pengajuan izin sesuai aturan sekolah (FR-06).
    | Pengajuan yang melebihi batas ini akan ditolak oleh validasi sistem.
    */
    'max_duration_days' => (int) env('IZIN_MAX_DURATION_DAYS', 3),

    /*
    |--------------------------------------------------------------------------
    | Aturan Berkas Surat Sakit (FR-05)
    |--------------------------------------------------------------------------
    | Pengajuan izin jenis sakit wajib melampirkan berkas bukti.
    | Berkas disimpan di disk privat agar terlindungi dan hanya bisa diakses
    | oleh pihak berwenang (wali murid bersangkutan & guru/admin).
    */
    'surat_sakit' => [
        'disk' => env('IZIN_SURAT_DISK', 'local'), // Disimpan di storage/app/private
        'folder' => 'surat_sakit',
        'max_size_kb' => (int) env('IZIN_MAX_FILE_SIZE_KB', 2048), // 2 MB
        'allowed_mimes' => ['pdf', 'jpg', 'jpeg', 'png'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Aturan Tanggal Izin Susulan
    |--------------------------------------------------------------------------
    | Apakah pengajuan izin susulan (tanggal lampau) diperbolehkan.
    | max_past_days menentukan batas toleransi hari ke belakang.
    */
    'allow_retroactive' => (bool) env('IZIN_ALLOW_RETROACTIVE', false),
    'max_past_days' => (int) env('IZIN_MAX_PAST_DAYS', 0),
];
