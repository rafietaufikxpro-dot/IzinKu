<?php

namespace Tests\Feature\Izin;

use App\Models\Izin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VerifikasiAccTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['wali_murid', 'guru', 'guru_piket', 'admin'] as $role) {
            Role::create(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_guru_piket_dapat_mengakses_halaman_verifikasi(): void
    {
        $piket = User::factory()->create();
        $piket->assignRole('guru_piket');

        $response = $this->actingAs($piket)->get(route('izin-verifikasi.index'));

        $response->assertOk();
    }

    public function test_wali_murid_tidak_dapat_mengakses_halaman_verifikasi(): void
    {
        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');

        $response = $this->actingAs($wali)->get(route('izin-verifikasi.index'));

        $response->assertForbidden();
    }

    public function test_guru_piket_dapat_menyetujui_izin_yang_sudah_dikonfirmasi(): void
    {
        $piket = User::factory()->create();
        $piket->assignRole('guru_piket');
        $izin = Izin::factory()->dikonfirmasi()->create();

        $response = $this->actingAs($piket)->post(route('izin-verifikasi.acc', $izin), [
            'aksi' => 'disetujui',
        ]);

        $response->assertRedirect();

        $izin->refresh();

        $this->assertSame('disetujui', $izin->status);
        $this->assertSame($piket->id, $izin->status_updated_by);
        $this->assertNotNull($izin->status_updated_at);
    }

    public function test_guru_piket_dapat_menolak_izin_dengan_alasan(): void
    {
        $piket = User::factory()->create();
        $piket->assignRole('guru_piket');
        $izin = Izin::factory()->dikonfirmasi()->create();

        $response = $this->actingAs($piket)->post(route('izin-verifikasi.acc', $izin), [
            'aksi' => 'ditolak',
            'alasan_penolakan' => 'Dokumen tidak lengkap.',
        ]);

        $response->assertRedirect();

        $izin->refresh();

        $this->assertSame('ditolak', $izin->status);
        $this->assertSame('Dokumen tidak lengkap.', $izin->alasan_penolakan);
        $this->assertSame($piket->id, $izin->status_updated_by);
        $this->assertNotNull($izin->status_updated_at);
    }

    public function test_penolakan_wajib_menyertakan_alasan(): void
    {
        $piket = User::factory()->create();
        $piket->assignRole('guru_piket');
        $izin = Izin::factory()->dikonfirmasi()->create();

        $response = $this->actingAs($piket)->post(route('izin-verifikasi.acc', $izin), [
            'aksi' => 'ditolak',
        ]);

        $response->assertSessionHasErrors('alasan_penolakan');
        $this->assertSame('menunggu', $izin->fresh()->status);
    }

    public function test_guru_piket_tidak_dapat_memproses_izin_yang_belum_dikonfirmasi(): void
    {
        $piket = User::factory()->create();
        $piket->assignRole('guru_piket');
        $izin = Izin::factory()->create(['confirmed_at' => null]);

        $response = $this->actingAs($piket)->post(route('izin-verifikasi.acc', $izin), [
            'aksi' => 'disetujui',
        ]);

        $response->assertSessionHasErrors('izin');
        $this->assertSame('menunggu', $izin->fresh()->status);
    }

    public function test_guru_piket_tidak_dapat_memproses_izin_yang_sudah_diproses(): void
    {
        $piket = User::factory()->create();
        $piket->assignRole('guru_piket');
        $izin = Izin::factory()->dikonfirmasi()->create(['status' => 'disetujui']);

        $response = $this->actingAs($piket)->post(route('izin-verifikasi.acc', $izin), [
            'aksi' => 'ditolak',
            'alasan_penolakan' => 'Uji coba',
        ]);

        $response->assertSessionHasErrors('izin');
        $this->assertSame('disetujui', $izin->fresh()->status);
    }

    public function test_guru_biasa_tidak_dapat_memberi_acc(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        $izin = Izin::factory()->dikonfirmasi()->create();

        $response = $this->actingAs($guru)->post(route('izin-verifikasi.acc', $izin), [
            'aksi' => 'disetujui',
        ]);

        $response->assertForbidden();
    }

    public function test_halaman_verifikasi_menampilkan_izin(): void
    {
        $piket = User::factory()->create();
        $piket->assignRole('guru_piket');
        $izin = Izin::factory()->dikonfirmasi()->create();

        $response = $this->actingAs($piket)->get(route('izin-verifikasi.index'));

        $response->assertOk();
        $response->assertSee($izin->siswa->nama);
    }

    public function test_detail_verifikasi_tampil_untuk_guru_piket(): void
    {
        $piket = User::factory()->create();
        $piket->assignRole('guru_piket');
        $izin = Izin::factory()->dikonfirmasi()->create();

        $response = $this->actingAs($piket)->get(route('izin-verifikasi.show', $izin));

        $response->assertOk();
        $response->assertSee($izin->siswa->nama);
    }
}
