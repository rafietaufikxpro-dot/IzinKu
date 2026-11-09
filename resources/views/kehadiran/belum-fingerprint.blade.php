<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Belum Fingerprint') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-sm text-gray-500 mb-4">
                        {{ __('Tanggal') }}: {{ $tanggal->format('d M Y') }}
                    </p>

                    @if ($siswa->isEmpty())
                        <p class="text-gray-500">{{ __('Semua siswa sudah tercatat hadir.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr>
                                        <th class="text-left font-medium text-gray-500 py-2 pr-4">Nama</th>
                                        <th class="text-left font-medium text-gray-500 py-2 pr-4">Kelas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($siswa as $item)
                                        <tr>
                                            <td class="py-2 pr-4">{{ $item->nama }}</td>
                                            <td class="py-2 pr-4">{{ $item->kelas }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
