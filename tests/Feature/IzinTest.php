<?php

namespace Tests\Feature;

use App\Models\Izin;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IzinTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_form_izin(): void
    {
        $response = $this->get(route('izin.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_wali_murid_can_view_form_izin_with_their_students(): void
    {
        $user = User::factory()->create();
        $siswa = Siswa::factory()->create([
            'user_id' => $user->id,
            'nama' => 'Budi Santoso',
        ]);

        $response = $this->actingAs($user)->get(route('izin.create'));

        $response->assertOk();
        $response->assertSee('Form Pengajuan Izin Siswa');
        $response->assertSee('Budi Santoso');
    }

    public function test_wali_murid_can_submit_regular_izin(): void
    {
        $user = User::factory()->create();
        $siswa = Siswa::factory()->create(['user_id' => $user->id]);

        $startDate = now()->addDay()->toDateString();
        $endDate = now()->addDays(2)->toDateString();

        $response = $this->actingAs($user)->post(route('izin.store'), [
            'siswa_id' => $siswa->id,
            'jenis_izin' => 'izin',
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'alasan' => 'Acara keluarga di luar kota selama dua hari.',
        ]);

        $response->assertRedirect(route('izin.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('izin', [
            'user_id' => $user->id,
            'siswa_id' => $siswa->id,
            'jenis_izin' => 'izin',
            'jumlah_hari' => 2,
            'status_konfirmasi' => 'menunggu',
            'status' => 'menunggu',
        ]);
    }

    public function test_sakit_without_attachment_is_rejected(): void
    {
        $user = User::factory()->create();
        $siswa = Siswa::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('izin.store'), [
            'siswa_id' => $siswa->id,
            'jenis_izin' => 'sakit',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
            'alasan' => 'Demam tinggi dan flu berat.',
            'surat_sakit' => null,
        ]);

        $response->assertSessionHasErrors(['surat_sakit']);
        $this->assertDatabaseCount('izin', 0);
    }

    public function test_sakit_with_attachment_is_accepted_and_stored_in_private_disk(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $siswa = Siswa::factory()->create(['user_id' => $user->id]);
        $file = UploadedFile::fake()->create('surat_dokter.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post(route('izin.store'), [
            'siswa_id' => $siswa->id,
            'jenis_izin' => 'sakit',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
            'alasan' => 'Demam tinggi dan disarankan dokter istirahat.',
            'surat_sakit' => $file,
        ]);

        $response->assertRedirect(route('izin.create'));
        $response->assertSessionHas('success');

        $izin = Izin::where('siswa_id', $siswa->id)->first();
        $this->assertNotNull($izin);
        $this->assertNotNull($izin->surat_sakit);

        Storage::disk('local')->assertExists($izin->surat_sakit);
    }

    public function test_duration_exceeding_configuration_limit_is_rejected(): void
    {
        Config::set('izin.max_duration_days', 3);

        $user = User::factory()->create();
        $siswa = Siswa::factory()->create(['user_id' => $user->id]);

        // Ajukan 5 hari (melebihi batas 3 hari)
        $startDate = now()->addDay()->toDateString();
        $endDate = now()->addDays(5)->toDateString();

        $response = $this->actingAs($user)->post(route('izin.store'), [
            'siswa_id' => $siswa->id,
            'jenis_izin' => 'izin',
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'alasan' => 'Keperluan keluarga bepergian ke luar pulau.',
        ]);

        $response->assertSessionHasErrors(['tanggal_selesai']);
        $this->assertDatabaseCount('izin', 0);
    }

    public function test_user_cannot_submit_izin_for_another_users_child(): void
    {
        $waliA = User::factory()->create();
        $waliB = User::factory()->create();
        $anakWaliB = Siswa::factory()->create(['user_id' => $waliB->id]);

        $response = $this->actingAs($waliA)->post(route('izin.store'), [
            'siswa_id' => $anakWaliB->id,
            'jenis_izin' => 'izin',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
            'alasan' => 'Percobaan izin untuk siswa lain.',
        ]);

        // FormRequest authorize() mengembalikan false -> 403 Forbidden
        $response->assertForbidden();
        $this->assertDatabaseCount('izin', 0);
    }

    public function test_end_date_before_start_date_is_rejected(): void
    {
        $user = User::factory()->create();
        $siswa = Siswa::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('izin.store'), [
            'siswa_id' => $siswa->id,
            'jenis_izin' => 'izin',
            'tanggal_mulai' => now()->addDays(3)->toDateString(),
            'tanggal_selesai' => now()->addDays(1)->toDateString(),
            'alasan' => 'Tanggal tidak valid.',
        ]);

        $response->assertSessionHasErrors(['tanggal_selesai']);
        $this->assertDatabaseCount('izin', 0);
    }
}
