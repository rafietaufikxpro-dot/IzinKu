<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verifikasi Izin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($izin->isEmpty())
                        <p class="text-gray-500">{{ __('Belum ada pengajuan izin.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr>
                                        <th class="text-left font-medium text-gray-500 py-2 pr-4">Siswa</th>
                                        <th class="text-left font-medium text-gray-500 py-2 pr-4">Alasan</th>
                                        <th class="text-left font-medium text-gray-500 py-2 pr-4">Tanggal</th>
                                        <th class="text-left font-medium text-gray-500 py-2 pr-4">Status</th>
                                        <th class="text-left font-medium text-gray-500 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($izin as $item)
                                        @php
                                            $badge = match ($item->status) {
                                                'disetujui' => 'bg-green-100 text-green-800',
                                                'ditolak' => 'bg-red-100 text-red-800',
                                                default => 'bg-yellow-100 text-yellow-800',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="py-2 pr-4">{{ $item->siswa?->nama }}</td>
                                            <td class="py-2 pr-4">{{ __($item->jenis_izin) }}</td>
                                            <td class="py-2 pr-4">{{ $item->tanggal_mulai?->format('d M Y') }} - {{ $item->tanggal_selesai?->format('d M Y') }}</td>
                                            <td class="py-2 pr-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                                    {{ __($item->status) }}
                                                </span>
                                            </td>
                                            <td class="py-2 text-right">
                                                <a href="{{ route('izin-verifikasi.show', $item) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Periksa') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $izin->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
