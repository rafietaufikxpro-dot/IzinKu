<?php

namespace Tests\Feature\Izin;

use App\Models\Izin;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuratSakitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['wali_murid', 'guru', 'guru_piket', 'admin'] as $role) {
            Role::create(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_wali_murid_dapat_mengunduh_surat_sakit_izin_sendiri(): void
    {
        Storage::fake('local');

        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');
        $siswa = Siswa::factory()->create(['user_id' => $wali->id]);

        $this->actingAs($wali)->post(route('izin.store'), [
            'siswa_id' => $siswa->id,
            'jenis_izin' => 'sakit',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
            'alasan' => 'Demam tinggi dan disarankan dokter istirahat.',
            'surat_sakit' => UploadedFile::fake()->create('surat.pdf', 500, 'application/pdf'),
        ]);

        $izin = Izin::where('siswa_id', $siswa->id)->first();

        $this->assertNotNull($izin->surat_sakit);

        // Berkas surat sakit tersimpan di disk privat, bukan disk publik.
        Storage::disk('local')->assertExists($izin->surat_sakit);
        Storage::disk('public')->assertMissing($izin->surat_sakit);

        $response = $this->actingAs($wali)->get(route('izin.surat-sakit.download', $izin));

        $response->assertOk();
    }

    public function test_wali_murid_tidak_bisa_mengunduh_surat_sakit_izin_orang_lain(): void
    {
        Storage::fake('local');

        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');

        $izin = Izin::factory()->sakit()->create();

        $response = $this->actingAs($wali)->get(route('izin.surat-sakit.download', $izin));

        $response->assertForbidden();
    }

    public function test_halaman_daftar_izin_tampil_untuk_wali_murid(): void
    {
        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');
        $siswa = Siswa::factory()->create(['user_id' => $wali->id]);
        Izin::factory()->create([
            'siswa_id' => $siswa->id,
            'user_id' => $wali->id,
        ]);

        $response = $this->actingAs($wali)->get(route('izin.index'));

        $response->assertOk();
        $response->assertSee($siswa->nama);
    }

    public function test_detail_izin_tampil_untuk_wali_murid_yang_bersangkutan(): void
    {
        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');
        $siswa = Siswa::factory()->create(['user_id' => $wali->id]);
        $izin = Izin::factory()->create([
            'siswa_id' => $siswa->id,
            'user_id' => $wali->id,
        ]);

        $response = $this->actingAs($wali)->get(route('izin.show', $izin));

        $response->assertOk();
        $response->assertSee($siswa->nama);
    }

    public function test_wali_murid_tidak_bisa_melihat_detail_izin_orang_lain(): void
    {
        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');
        $izin = Izin::factory()->create();

        $response = $this->actingAs($wali)->get(route('izin.show', $izin));

        $response->assertForbidden();
    }

    public function test_guru_dapat_melihat_detail_izin(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        $izin = Izin::factory()->create();

        $response = $this->actingAs($guru)->get(route('izin.show', $izin));

        $response->assertOk();
    }
}
