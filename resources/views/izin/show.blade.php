<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pengajuan Izin') }}
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

                    @php
                        $badge = match ($izin->status) {
                            'disetujui' => 'bg-green-100 text-green-800',
                            'ditolak' => 'bg-red-100 text-red-800',
                            default => 'bg-yellow-100 text-yellow-800',
                        };
                    @endphp

                    <div class="flex items-center justify-between">
                        <h1 class="text-lg font-semibold text-gray-800">{{ __('Pengajuan Izin') }}</h1>
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
                            <dd class="text-gray-900">{{ __($izin->alasan) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Tanggal Mulai') }}</dt>
                            <dd class="text-gray-900">{{ $izin->tanggal_mulai?->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Tanggal Selesai') }}</dt>
                            <dd class="text-gray-900">{{ $izin->tanggal_selesai?->format('d M Y') }}</dd>
                        </div>
                        @if ($izin->catatan)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Keterangan') }}</dt>
                                <dd class="text-gray-900">{{ $izin->catatan }}</dd>
                            </div>
                        @endif
                        @if ($izin->alasan_penolakan)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Alasan Penolakan') }}</dt>
                                <dd class="text-gray-900">{{ $izin->alasan_penolakan }}</dd>
                            </div>
                        @endif
                    </dl>

                    @if ($izin->surat_sakit_path)
                        <div class="pt-4 border-t border-gray-200">
                            <a href="{{ route('izin.surat-sakit.download', $izin) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Unduh Surat Sakit') }}
                            </a>
                        </div>
                    @endif

                    <div class="pt-4">
                        <a href="{{ route('izin.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">&larr; {{ __('Kembali') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
