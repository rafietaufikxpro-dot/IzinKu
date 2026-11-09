<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-800">
                    {{ __('Form Pengajuan Izin Siswa') }}
                </h2>
                <p class="text-sm text-gray-500">
                    Ajukan permohonan izin tidak masuk sekolah (sakit atau keperluan) untuk putra/putri Anda.
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200 self-start sm:self-auto">
                Maksimal {{ $maxDays }} Hari / Pengajuan
            </span>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flash Message: Berhasil --}}
            @if (session('success'))
                <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-emerald-900">Pengajuan Berhasil Disimpan!</h3>
                            <p class="mt-1 text-sm text-emerald-800">{{ session('success') }}</p>
                            <p class="mt-2 text-xs text-emerald-700">
                                Langkah berikutnya: Silakan konfirmasi pengajuan ini melalui tautan WhatsApp yang dikirimkan agar segera diproses oleh guru piket.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Flash Message: Error Umum --}}
            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 text-rose-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-rose-900">Periksa Kembali Data Anda</h3>
                            <ul class="mt-1 text-xs text-rose-800 list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if ($siswas->isEmpty())
                {{-- Kondisi jika akun belum memiliki data siswa terkait --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                    <div class="mx-auto w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Belum Ada Data Siswa Terhubung</h3>
                    <p class="mt-2 text-sm text-gray-600 max-w-md mx-auto">
                        Akun Anda belum terhubung dengan data siswa manapun. Pastikan nomor WhatsApp atau akun Anda telah didaftarkan oleh administrator sekolah.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            @else
                {{-- Form Pengajuan Izin --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
                     x-data="{
                        jenisIzin: '{{ old('jenis_izin', 'sakit') }}',
                        startDate: '{{ old('tanggal_mulai', '') }}',
                        endDate: '{{ old('tanggal_selesai', '') }}',
                        maxDays: {{ (int) $maxDays }},
                        fileName: '',
                        get calculatedDays() {
                            if (!this.startDate || !this.endDate) return 0;
                            const start = new Date(this.startDate);
                            const end = new Date(this.endDate);
                            if (end < start) return 0;
                            const diffTime = Math.abs(end - start);
                            return Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
                        },
                        get isExceeded() {
                            return this.calculatedDays > this.maxDays;
                        },
                        handleFile(event) {
                            const file = event.target.files[0];
                            this.fileName = file ? file.name : '';
                        }
                     }">
                    
                    <div class="p-6 sm:p-8">
                        <form method="POST" action="{{ route('izin.store') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            {{-- 1. Pilih Siswa --}}
                            <div>
                                <label for="siswa_id" class="block text-sm font-semibold text-gray-900 mb-1">
                                    Pilih Siswa <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500 mb-2">Pilih putra/putri Anda yang ingin diajukan perizinan.</p>
                                <select id="siswa_id"
                                        name="siswa_id"
                                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition @error('siswa_id') border-rose-400 text-rose-900 focus:border-rose-500 focus:ring-rose-500 @enderror"
                                        required>
                                    @if ($siswas->count() > 1)
                                        <option value="">-- Pilih Putra/Putri Anda --</option>
                                    @endif
                                    @foreach ($siswas as $siswa)
                                        <option value="{{ $siswa->id }}" {{ old('siswa_id') == $siswa->id || $siswas->count() === 1 ? 'selected' : '' }}>
                                            {{ $siswa->nama }} &mdash; Kelas {{ $siswa->kelas }} (NIS: {{ $siswa->nis }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('siswa_id')
                                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 2. Jenis Izin --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-1">
                                    Jenis Perizinan <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500 mb-3">Tentukan alasan utama ketidakhadiran siswa.</p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    {{-- Opsi Sakit --}}
                                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition"
                                           :class="jenisIzin === 'sakit' ? 'border-indigo-600 bg-indigo-50/40 text-indigo-900 shadow-sm' : 'border-gray-200 bg-white hover:border-gray-300 text-gray-700'">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center gap-2">
                                                <input type="radio"
                                                       name="jenis_izin"
                                                       value="sakit"
                                                       x-model="jenisIzin"
                                                       class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                                <span class="font-semibold text-sm">Sakit</span>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-rose-100 text-rose-700">
                                                Wajib Surat Dokter
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-500 pl-6">
                                            Siswa sedang sakit dan melampirkan bukti surat keterangan medis/dokter.
                                        </span>
                                    </label>

                                    {{-- Opsi Izin Keperluan --}}
                                    <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition"
                                           :class="jenisIzin === 'izin' ? 'border-indigo-600 bg-indigo-50/40 text-indigo-900 shadow-sm' : 'border-gray-200 bg-white hover:border-gray-300 text-gray-700'">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center gap-2">
                                                <input type="radio"
                                                       name="jenis_izin"
                                                       value="izin"
                                                       x-model="jenisIzin"
                                                       class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                                <span class="font-semibold text-sm">Izin Keperluan</span>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-blue-100 text-blue-700">
                                                Keperluan Keluarga
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-500 pl-6">
                                            Izin karena urusan keluarga penting, musibah, atau kegiatan khusus lainnya.
                                        </span>
                                    </label>
                                </div>
                                @error('jenis_izin')
                                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 3. Rentang Tanggal Izin --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-1">
                                    Waktu Perizinan <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500 mb-3">Tentukan tanggal mulai dan selesai izin.</p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="tanggal_mulai" class="block text-xs font-medium text-gray-700 mb-1">
                                            Tanggal Mulai
                                        </label>
                                        <input type="date"
                                               id="tanggal_mulai"
                                               name="tanggal_mulai"
                                               x-model="startDate"
                                               value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                                               min="{{ config('izin.allow_retroactive') ? '' : date('Y-m-d') }}"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition @error('tanggal_mulai') border-rose-400 text-rose-900 focus:border-rose-500 focus:ring-rose-500 @enderror"
                                               required>
                                        @error('tanggal_mulai')
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="tanggal_selesai" class="block text-xs font-medium text-gray-700 mb-1">
                                            Tanggal Selesai
                                        </label>
                                        <input type="date"
                                               id="tanggal_selesai"
                                               name="tanggal_selesai"
                                               x-model="endDate"
                                               value="{{ old('tanggal_selesai', date('Y-m-d')) }}"
                                               :min="startDate || '{{ date('Y-m-d') }}'"
                                               class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition @error('tanggal_selesai') border-rose-400 text-rose-900 focus:border-rose-500 focus:ring-rose-500 @enderror"
                                               required>
                                        @error('tanggal_selesai')
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Indikator Durasi & Validasi Batas Waktu (FR-06) --}}
                                <div class="mt-3">
                                    <template x-if="calculatedDays > 0">
                                        <div>
                                            {{-- Jika Melebihi Batas Konfigurasi --}}
                                            <div x-show="isExceeded" class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800 flex items-start gap-2">
                                                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                <div>
                                                    <span class="font-semibold">Peringatan Batas Durasi:</span>
                                                    Anda mengajukan durasi <span class="font-bold underline" x-text="calculatedDays"></span> hari.
                                                    Aturan sekolah membatasi izin permohonan mandiri maksimal <span class="font-bold" x-text="maxDays"></span> hari.
                                                    Pengajuan melebihi batas ini akan ditolak oleh sistem. Untuk izin lebih panjang, hubungi wali kelas langsung.
                                                </div>
                                            </div>

                                            {{-- Jika Durasi Masih Dalam Batas --}}
                                            <div x-show="!isExceeded" class="rounded-xl bg-indigo-50/60 border border-indigo-100 p-2.5 text-xs text-indigo-900 flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span>Total Durasi: <strong class="font-bold" x-text="calculatedDays"></strong> hari</span>
                                                </div>
                                                <span class="text-[11px] text-indigo-600 font-medium">(Maksimal: <span x-text="maxDays"></span> hari)</span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- 4. Alasan / Keterangan Izin --}}
                            <div>
                                <label for="alasan" class="block text-sm font-semibold text-gray-900 mb-1">
                                    Alasan / Keterangan Izin <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-xs text-gray-500 mb-2">Tuliskan penjelasan rinci perihal ketidakhadiran siswa.</p>
                                <textarea id="alasan"
                                          name="alasan"
                                          rows="3"
                                          class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition @error('alasan') border-rose-400 text-rose-900 focus:border-rose-500 focus:ring-rose-500 @enderror"
                                          placeholder="Contoh: Mengalami demam dan flu sejak semalam serta disarankan istirahat oleh dokter..."
                                          required>{{ old('alasan') }}</textarea>
                                @error('alasan')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 5. Upload Dokumen / Surat Sakit (FR-05) --}}
                            <div x-show="jenisIzin === 'sakit'"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="rounded-xl border border-dashed border-gray-300 bg-gray-50/70 p-5">
                                <div class="flex items-center justify-between mb-1">
                                    <label for="surat_sakit" class="block text-sm font-semibold text-gray-900">
                                        Lampirkan Surat Sakit / Keterangan Dokter <span class="text-rose-500">*</span>
                                    </label>
                                    <span class="text-[11px] font-semibold text-rose-600 uppercase tracking-wide">
                                        Wajib Dilampirkan
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mb-3">
                                    Format yang didukung: <strong>PDF, JPG, JPEG, PNG</strong> (Maksimal 2 MB). Berkas akan disimpan di penyimpanan privat sekolah.
                                </p>

                                <div class="flex flex-col sm:flex-row items-center gap-3">
                                    <label class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 cursor-pointer transition">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        <span>Pilih Berkas Dokumen</span>
                                        <input type="file"
                                               id="surat_sakit"
                                               name="surat_sakit"
                                               accept=".pdf,.jpg,.jpeg,.png"
                                               @change="handleFile($event)"
                                               class="hidden">
                                    </label>

                                    <div class="flex-1 w-full text-center sm:text-left">
                                        <template x-if="fileName">
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-800 text-xs font-medium">
                                                <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                </svg>
                                                <span x-text="fileName" class="truncate max-w-xs"></span>
                                            </div>
                                        </template>
                                        <template x-if="!fileName">
                                            <span class="text-xs text-gray-400 italic">Belum ada berkas dipilih</span>
                                        </template>
                                    </div>
                                </div>

                                @error('surat_sakit')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- 6. Card Ketentuan & Informasi --}}
                            <div class="rounded-xl bg-slate-50 border border-slate-200/80 p-4 text-xs text-slate-600 space-y-1.5">
                                <div class="font-semibold text-slate-800 flex items-center gap-1.5 mb-1">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Aturan & Ketentuan Pengajuan Izin
                                </div>
                                <ul class="list-disc list-inside space-y-1 text-slate-600 pl-1">
                                    <li>Pengajuan izin yang berstatus sakit <strong>wajib</strong> melampirkan surat sakit sah.</li>
                                    <li>Batas durasi mandiri adalah <strong>{{ $maxDays }} hari</strong>; izin > 2 hari berturut-turut akan memicu peringatan absensi.</li>
                                    <li>Permohonan izin belum diproses guru piket sebelum Anda melakukan konfirmasi melalui WhatsApp.</li>
                                </ul>
                            </div>

                            {{-- 7. Tombol Aksi --}}
                            <div class="pt-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                                <a href="{{ route('dashboard') }}"
                                   class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition">
                                    Batal & Kembali
                                </a>

                                <button type="submit"
                                        :disabled="isExceeded"
                                        :class="isExceeded ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm hover:shadow'"
                                        class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    <span>Kirim Pengajuan Izin</span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
