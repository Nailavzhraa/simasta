<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeploymentReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_auth_pages_work_outside_local_environment(): void
    {
        foreach (['/', '/login', '/register', '/forgot-password', '/reset-password/test-token'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/preview/reset')->assertNotFound();
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_admin_pages_work_with_a_new_database(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        foreach (['dashboard', 'mahasiswa', 'pengajuan', 'penempatan', 'logbook', 'divisi', 'penilaian', 'laporan', 'pembimbing', 'pembimbing/create'] as $page) {
            $this->get('/admin/'.$page)->assertOk();
        }
    }

    public function test_supervisor_login_reaches_its_own_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'pembimbing']);
        $this->actingAs($user)->get($user->dashboardUrl())->assertOk();
        $this->get('/admin/dashboard')->assertForbidden();
    }

    public function test_accepted_student_pages_and_separate_attendance_destination(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $user->mahasiswa()->create([
            'nama' => $user->name,
            'email' => $user->email,
            'nim' => 'TEST-001',
            'no_telepon' => '081234567890',
            'perguruan_tinggi' => 'Universitas Uji',
            'program_studi' => 'Informatika',
            'jenis_kelamin' => 'Laki-laki',
            'alamat_kampus' => 'Alamat Uji',
            'semester' => '6',
            'dosen_pembimbing' => 'Dosen Uji',
            'foto' => 'foto-profil/test.jpg',
        ]);
        // Reapplying after a rejection must use the latest decision.
        $user->pengajuan()->create(['status' => 'ditolak']);
        $user->pengajuan()->create(['status' => 'diterima']);
        $this->actingAs($user);

        foreach (['dashboard', 'profil', 'dokumen', 'surat', 'logbook', 'penilaian', 'bantuan', 'pengumuman'] as $page) {
            $this->get('/mahasiswa/'.$page)->assertOk();
        }
        $this->get('/mahasiswa/presensi')->assertRedirect(config('services.presensi.url'));
        $this->get('/mahasiswa/pengajuan')->assertRedirect('/mahasiswa/dokumen');
        $this->get('/admin/dashboard')->assertForbidden();
    }

    public function test_production_seeding_does_not_create_demo_accounts(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        (new \Database\Seeders\UserSeeder())->run();
        $this->assertDatabaseCount('users', 0);
    }
}
