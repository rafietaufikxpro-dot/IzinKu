<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengajuan Izin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-lg font-semibold text-gray-800">{{ __('Daftar Pengajuan Izin') }}</h1>
                <a href="{{ route('izin.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Ajukan Izin') }}
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($izin->isEmpty())
                        <p class="text-gray-500">{{ __('Belum ada pengajuan izin.') }}</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach ($izin as $item)
                                @php
                                    $badge = match ($item->status) {
                                        'disetujui' => 'bg-green-100 text-green-800',
                                        'ditolak' => 'bg-red-100 text-red-800',
                                        default => 'bg-yellow-100 text-yellow-800',
                                    };
                                @endphp
                                <li class="py-3 flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $item->siswa?->nama }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ __($item->jenis_izin) }} &middot;
                                            {{ $item->tanggal_mulai?->format('d M Y') }} - {{ $item->tanggal_selesai?->format('d M Y') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                            {{ __($item->status) }}
                                        </span>
                                        <a href="{{ route('izin.show', $item) }}" class="text-sm text-indigo-600 hover:text-indigo-900">{{ __('Lihat') }}</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4">
                            {{ $izin->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
