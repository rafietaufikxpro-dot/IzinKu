<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome Card --}}
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-2xl shadow-sm text-white p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-medium tracking-wide uppercase mb-2">
                            Portal IzinKu
                        </span>
                        <h3 class="text-2xl font-bold">Halo, {{ Auth::user()->name }}!</h3>
                        <p class="mt-1 text-indigo-100 text-sm max-w-xl">
                            Selamat datang di Sistem Perizinan dan Monitoring Kehadiran Siswa. Anda dapat mengajukan perizinan sakit atau keperluan mendesak untuk siswa secara daring.
                        </p>
                    </div>
                    <div class="self-start sm:self-auto flex-shrink-0">
                        <a href="{{ route('izin.create') }}"
                           class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white text-indigo-600 hover:bg-indigo-50 font-semibold text-sm shadow transition">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Ajukan Izin Sekarang</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Quick Menu & Information Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Menu 1: Form Pengajuan Izin --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-gray-900 mb-1">Form Pengajuan Izin</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Buat permohonan izin sakit (dengan surat keterangan dokter) atau izin keperluan keluarga.
                        </p>
                    </div>
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <a href="{{ route('izin.create') }}" class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                            Buka Form Izin &rarr;
                        </a>
                    </div>
                </div>

                {{-- Menu 2: Aturan Batas Waktu --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-gray-900 mb-1">Aturan Durasi Izin</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Maksimal izin mandiri adalah <strong>{{ config('izin.max_duration_days', 3) }} hari</strong>. Ketidakhadiran lebih dari 2 hari berturut-turut akan memicu peringatan absensi ke sekolah.
                        </p>
                    </div>
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <span class="text-xs text-gray-400">Aturan Sekolah IzinKu</span>
                    </div>
                </div>

                {{-- Menu 3: Data Siswa Anda --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-gray-900 mb-1">Siswa Terdaftar</h4>
                        <div class="text-xs text-gray-600 space-y-1">
                            @forelse (Auth::user()->siswas ?? [] as $siswa)
                                <div class="flex items-center justify-between py-1 border-b border-gray-50 last:border-0">
                                    <span class="font-medium text-gray-800">{{ $siswa->nama }}</span>
                                    <span class="text-gray-500">Kelas {{ $siswa->kelas }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-amber-600 italic">
                                    Belum ada siswa terhubung dengan akun ini.
                                </p>
                            @endforelse
                        </div>
                    </div>
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <span class="text-xs text-gray-400">Total: {{ Auth::user()->siswas->count() }} siswa</span>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
