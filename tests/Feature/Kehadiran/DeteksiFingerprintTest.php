<?php

namespace Tests\Feature\Kehadiran;

use App\Models\Fingerprint;
use App\Models\Izin;
use App\Models\Siswa;
use App\Models\User;
use App\Services\DeteksiKehadiranService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DeteksiFingerprintTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['wali_murid', 'guru', 'guru_piket', 'admin'] as $role) {
            Role::create(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_siswa_tanpa_fingerprint_dan_tanpa_izin_ditandai(): void
    {
        $siswa = Siswa::factory()->create();

        $hasil = app(DeteksiKehadiranService::class)->deteksiBelumFingerprint();

        $this->assertTrue($hasil->pluck('id')->contains($siswa->id));
    }

    public function test_siswa_yang_sudah_fingerprint_tidak_ditandai(): void
    {
        $siswa = Siswa::factory()->create();
        Fingerprint::factory()->create([
            'siswa_id' => $siswa->id,
            'tanggal' => today()->toDateString(),
        ]);

        $hasil = app(DeteksiKehadiranService::class)->deteksiBelumFingerprint();

        $this->assertFalse($hasil->pluck('id')->contains($siswa->id));
    }

    public function test_siswa_dengan_izin_disetujui_tidak_ditandai(): void
    {
        $siswa = Siswa::factory()->create();
        Izin::factory()->create([
            'siswa_id' => $siswa->id,
            'status' => 'disetujui',
            'tanggal_mulai' => today()->toDateString(),
            'tanggal_selesai' => today()->toDateString(),
        ]);

        $hasil = app(DeteksiKehadiranService::class)->deteksiBelumFingerprint();

        $this->assertFalse($hasil->pluck('id')->contains($siswa->id));
    }

    public function test_siswa_dengan_izin_menunggu_tetap_ditandai(): void
    {
        $siswa = Siswa::factory()->create();
        Izin::factory()->create([
            'siswa_id' => $siswa->id,
            'status' => 'menunggu',
            'tanggal_mulai' => today()->toDateString(),
            'tanggal_selesai' => today()->toDateString(),
        ]);

        $hasil = app(DeteksiKehadiranService::class)->deteksiBelumFingerprint();

        $this->assertTrue($hasil->pluck('id')->contains($siswa->id));
    }

    public function test_izin_disetujui_hanya_menutupi_tanggal_berlaku(): void
    {
        $siswa = Siswa::factory()->create();
        Izin::factory()->create([
            'siswa_id' => $siswa->id,
            'status' => 'disetujui',
            'tanggal_mulai' => now()->subDays(2)->toDateString(),
            'tanggal_selesai' => now()->subDay()->toDateString(),
        ]);

        $hasil = app(DeteksiKehadiranService::class)->deteksiBelumFingerprint();

        $this->assertTrue($hasil->pluck('id')->contains($siswa->id));
    }

    public function test_halaman_belum_fingerprint_tampil_untuk_guru(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $response = $this->actingAs($guru)->get(route('kehadiran.belum-fingerprint'));

        $response->assertOk();
    }

    public function test_wali_murid_tidak_bisa_mengakses_halaman_belum_fingerprint(): void
    {
        $wali = User::factory()->create();
        $wali->assignRole('wali_murid');

        $response = $this->actingAs($wali)->get(route('kehadiran.belum-fingerprint'));

        $response->assertForbidden();
    }

    public function test_halaman_menampilkan_siswa_yang_belum_fingerprint(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        $siswa = Siswa::factory()->create();

        $response = $this->actingAs($guru)->get(route('kehadiran.belum-fingerprint'));

        $response->assertOk();
        $response->assertSee($siswa->nama);
    }
}
