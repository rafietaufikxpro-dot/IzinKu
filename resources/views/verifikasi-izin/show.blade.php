<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verifikasi Izin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    @if (session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-800 rounded-md p-3 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @error('izin')
                        <div class="bg-red-50 border border-red-200 text-red-800 rounded-md p-3 text-sm">
                            {{ $message }}
                        </div>
                    @enderror

                    @php
                        $badge = match ($izin->status) {
                            'disetujui' => 'bg-green-100 text-green-800',
                            'ditolak' => 'bg-red-100 text-red-800',
                            default => 'bg-yellow-100 text-yellow-800',
                        };
                    @endphp

                    <div class="flex items-center justify-between">
                        <h1 class="text-lg font-semibold text-gray-800">{{ __('Detail Pengajuan Izin') }}</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                            {{ __($izin->status) }}
                        </span>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Siswa') }}</dt>
                            <dd class="text-gray-900">{{ $izin->siswa?->nama }} ({{ $izin->siswa?->kelas }})</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Alasan') }}</dt>
                            <dd class="text-gray-900">{{ __($izin->jenis_izin) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Tanggal Mulai') }}</dt>
                            <dd class="text-gray-900">{{ $izin->tanggal_mulai?->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Tanggal Selesai') }}</dt>
                            <dd class="text-gray-900">{{ $izin->tanggal_selesai?->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Konfirmasi Wali Murid') }}</dt>
                            <dd class="text-gray-900">
                                @if ($izin->status_konfirmasi === 'dikonfirmasi')
                                    {{ $izin->dikonfirmasi_pada?->format('d M Y H:i') }}
                                    @if ($izin->pengonfirmasi)
                                        &middot; {{ $izin->pengonfirmasi->name }}
                                    @endif
                                @else
                                    <span class="text-yellow-700">{{ __('Belum dikonfirmasi') }}</span>
                                @endif
                            </dd>
                        </div>
                        @if ($izin->alasan)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Keterangan') }}</dt>
                                <dd class="text-gray-900">{{ $izin->alasan }}</dd>
                            </div>
                        @endif
                        @if ($izin->catatan_penolakan)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Alasan Penolakan') }}</dt>
                                <dd class="text-gray-900">{{ $izin->catatan_penolakan }}</dd>
                            </div>
                        @endif
                    </dl>

                    @if ($izin->surat_sakit)
                        <div>
                            <a href="{{ route('izin.surat-sakit.download', $izin) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Unduh Surat Sakit') }}
                            </a>
                        </div>
                    @endif

                    @if ($izin->status === 'menunggu')
                        @if ($izin->status_konfirmasi === 'dikonfirmasi')
                            <form method="POST" action="{{ route('izin-verifikasi.acc', $izin) }}" class="space-y-4 border-t border-gray-200 pt-4" x-data="{ aksi: 'disetujui' }">
                                @csrf

                                <div>
                                    <span class="block font-medium text-sm text-gray-700">{{ __('Aksi') }}</span>
                                    <label class="inline-flex items-center mt-2 mr-4">
                                        <input type="radio" name="aksi" value="disetujui" x-model="aksi" class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2">{{ __('Setujui') }}</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="aksi" value="ditolak" x-model="aksi" class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2">{{ __('Tolak') }}</span>
                                    </label>
                                    <x-input-error :messages="$errors->get('aksi')" class="mt-2" />
                                </div>

                                <div x-show="aksi === 'ditolak'" x-cloak>
                                    <x-input-label for="catatan_penolakan" :value="__('Alasan Penolakan')" />
                                    <textarea id="catatan_penolakan" name="catatan_penolakan" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('catatan_penolakan') }}</textarea>
                                    <x-input-error :messages="$errors->get('catatan_penolakan')" class="mt-2" />
                                </div>

                                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                            </form>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-md p-3 text-sm">
                                {{ __('Izin belum dikonfirmasi oleh wali murid sehingga belum dapat diproses.') }}
                            </div>
                        @endif
                    @else
                        <div class="bg-gray-50 border border-gray-200 text-gray-700 rounded-md p-3 text-sm">
                            {{ __('Izin sudah diproses.') }}
                            @if ($izin->diverifikasi_pada)
                                &middot; {{ $izin->diverifikasi_pada->format('d M Y H:i') }}
                                @if ($izin->verifikator)
                                    &middot; {{ $izin->verifikator->name }}
                                @endif
                            @endif
                        </div>
                    @endif

                    <div class="pt-4">
                        <a href="{{ route('izin-verifikasi.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">&larr; {{ __('Kembali') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
