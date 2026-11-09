<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajukan Izin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('izin.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="siswa_id" :value="__('Siswa')" />
                            <select id="siswa_id" name="siswa_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">{{ __('Pilih siswa') }}</option>
                                @foreach ($siswa as $anak)
                                    <option value="{{ $anak->id }}" @selected(old('siswa_id') == $anak->id)>{{ $anak->nama }} ({{ $anak->kelas }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('siswa_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="alasan" :value="__('Alasan')" />
                            <select id="alasan" name="alasan" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="sakit" @selected(old('alasan') === 'sakit')>{{ __('Sakit') }}</option>
                                <option value="izin" @selected(old('alasan') === 'izin')>{{ __('Izin') }}</option>
                                <option value="lainnya" @selected(old('alasan') === 'lainnya')>{{ __('Lainnya') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('alasan')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="tanggal_mulai" :value="__('Tanggal Mulai')" />
                                <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="mt-1 block w-full" :value="old('tanggal_mulai')" required />
                                <x-input-error :messages="$errors->get('tanggal_mulai')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="tanggal_selesai" :value="__('Tanggal Selesai')" />
                                <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block w-full" :value="old('tanggal_selesai')" required />
                                <x-input-error :messages="$errors->get('tanggal_selesai')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="surat_sakit" :value="__('Surat Sakit (wajib jika alasan sakit)')" />
                            <x-text-input id="surat_sakit" name="surat_sakit" type="file" class="mt-1 block w-full" accept=".pdf,.jpg,.jpeg,.png" />
                            <x-input-error :messages="$errors->get('surat_sakit')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('Format: pdf, jpg, jpeg, png. Maksimal :ukuran KB.', ['ukuran' => config('izinku.surat_sakit.ukuran_maksimal_kb')]) }}
                            </p>
                        </div>

                        <div>
                            <x-input-label for="catatan" :value="__('Keterangan')" />
                            <textarea id="catatan" name="catatan" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('catatan') }}</textarea>
                            <x-input-error :messages="$errors->get('catatan')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Ajukan Izin') }}</x-primary-button>
                            <a href="{{ route('izin.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Batal') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
