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

    public function test_wali_murid_tidak_dapat_mengajukan_izin_sakit_tanpa_surat(): void
    {
        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');
        $siswa = Siswa::factory()->create(['user_id' => $wali->id]);

        $response = $this->actingAs($wali)->post(route('izin.store'), [
            'siswa_id' => $siswa->id,
            'alasan' => 'sakit',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
            'catatan' => 'Demam',
        ]);

        $response->assertSessionHasErrors('surat_sakit');
        $this->assertDatabaseMissing('izin', ['siswa_id' => $siswa->id]);
    }

    public function test_wali_murid_dapat_mengajukan_izin_sakit_dengan_surat(): void
    {
        Storage::fake('surat_sakit');

        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');
        $siswa = Siswa::factory()->create(['user_id' => $wali->id]);

        $response = $this->actingAs($wali)->post(route('izin.store'), [
            'siswa_id' => $siswa->id,
            'alasan' => 'sakit',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
            'catatan' => 'Demam',
            'surat_sakit' => UploadedFile::fake()->image('surat.jpg'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $izin = Izin::where('siswa_id', $siswa->id)->first();

        $this->assertNotNull($izin);
        $this->assertSame('menunggu', $izin->status);
        $this->assertSame($wali->id, $izin->user_id);
        $this->assertNotNull($izin->surat_sakit_path);
        Storage::disk('surat_sakit')->assertExists($izin->surat_sakit_path);
    }

    public function test_surat_sakit_disimpan_di_disk_pribadi(): void
    {
        Storage::fake('surat_sakit');
        Storage::fake('public');

        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');
        $siswa = Siswa::factory()->create(['user_id' => $wali->id]);

        $this->actingAs($wali)->post(route('izin.store'), [
            'siswa_id' => $siswa->id,
            'alasan' => 'sakit',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
            'surat_sakit' => UploadedFile::fake()->image('surat.jpg'),
        ]);

        $izin = Izin::where('siswa_id', $siswa->id)->first();

        $this->assertNotNull($izin->surat_sakit_path);
        Storage::disk('surat_sakit')->assertExists($izin->surat_sakit_path);
        Storage::disk('public')->assertMissing($izin->surat_sakit_path);
    }

    public function test_tipe_berkas_surat_sakit_dibatasi(): void
    {
        Storage::fake('surat_sakit');

        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');
        $siswa = Siswa::factory()->create(['user_id' => $wali->id]);

        $response = $this->actingAs($wali)->post(route('izin.store'), [
            'siswa_id' => $siswa->id,
            'alasan' => 'sakit',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
            'surat_sakit' => UploadedFile::fake()->create('surat.txt', 100, 'text/plain'),
        ]);

        $response->assertSessionHasErrors('surat_sakit');
        $this->assertDatabaseMissing('izin', ['siswa_id' => $siswa->id]);
    }

    public function test_ukuran_berkas_surat_sakit_dibatasi(): void
    {
        Storage::fake('surat_sakit');

        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');
        $siswa = Siswa::factory()->create(['user_id' => $wali->id]);

        $response = $this->actingAs($wali)->post(route('izin.store'), [
            'siswa_id' => $siswa->id,
            'alasan' => 'sakit',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
            'surat_sakit' => UploadedFile::fake()->create('surat.pdf', 5000, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('surat_sakit');
        $this->assertDatabaseMissing('izin', ['siswa_id' => $siswa->id]);
    }

    public function test_wali_murid_tidak_bisa_mengajukan_izin_untuk_siswa_orang_lain(): void
    {
        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');
        $siswaLain = Siswa::factory()->create();

        $response = $this->actingAs($wali)->post(route('izin.store'), [
            'siswa_id' => $siswaLain->id,
            'alasan' => 'izin',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('izin', ['siswa_id' => $siswaLain->id]);
    }

    public function test_wali_murid_tidak_bisa_mengunduh_surat_sakit_izin_orang_lain(): void
    {
        Storage::fake('surat_sakit');

        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');

        $izin = Izin::factory()->create([
            'alasan' => 'sakit',
            'surat_sakit_path' => 'surat-sakit/contoh.jpg',
        ]);
        Storage::disk('surat_sakit')->put('surat-sakit/contoh.jpg', 'isi berkas');

        $response = $this->actingAs($wali)->get(route('izin.surat-sakit.download', $izin));

        $response->assertForbidden();
    }

    public function test_wali_murid_dapat_mengunduh_surat_sakit_izin_sendiri(): void
    {
        Storage::fake('surat_sakit');

        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');
        $siswa = Siswa::factory()->create(['user_id' => $wali->id]);

        $this->actingAs($wali)->post(route('izin.store'), [
            'siswa_id' => $siswa->id,
            'alasan' => 'sakit',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->toDateString(),
            'surat_sakit' => UploadedFile::fake()->image('surat.jpg'),
        ]);

        $izin = Izin::where('siswa_id', $siswa->id)->first();

        $response = $this->actingAs($wali)->get(route('izin.surat-sakit.download', $izin));

        $response->assertOk();
    }

    public function test_guru_dapat_melihat_detail_izin(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        $izin = Izin::factory()->create();

        $response = $this->actingAs($guru)->get(route('izin.show', $izin));

        $response->assertOk();
    }

    public function test_tamu_tidak_bisa_mengakses_halaman_izin(): void
    {
        $response = $this->get(route('izin.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_guru_tidak_bisa_mengakses_form_pengajuan_izin(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $response = $this->actingAs($guru)->get(route('izin.create'));

        $response->assertForbidden();
    }

    public function test_halaman_form_pengajuan_izin_tampil_untuk_wali_murid(): void
    {
        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');
        Siswa::factory()->create(['user_id' => $wali->id]);

        $response = $this->actingAs($wali)->get(route('izin.create'));

        $response->assertOk();
        $response->assertSee('Ajukan Izin');
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
}
