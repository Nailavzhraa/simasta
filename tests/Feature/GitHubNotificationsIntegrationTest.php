<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Pembimbing;
use App\Models\User;
use App\Notifications\PengajuanBaruNotification;
use App\Notifications\StatusPengajuanNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GitHubNotificationsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_acceptance_saves_one_student_notification_and_does_not_duplicate_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $application = $student->pengajuan()->create(['status' => 'diproses']);
        $division = Divisi::create(['nama' => 'Divisi Uji']);
        $supervisor = Pembimbing::create(['nama' => 'Pembimbing Uji', 'divisi_id' => $division->id]);
        $fields = ['divisi_id' => $division->id, 'pembimbing_id' => $supervisor->id, 'jobdesk' => 'Pengolahan data'];

        $this->actingAs($admin)->post(route('admin.pengajuan.terima', $application), $fields)->assertRedirect();
        $this->assertSame('diterima', $application->fresh()->status);
        $notification = $student->notifications()->sole();
        $this->assertSame('diterima', $notification->data['status']);
        $this->assertSame($application->id, $notification->data['pengajuan_id']);
        $this->post(route('admin.pengajuan.terima', $application), $fields)->assertSessionHas('error');
        $this->assertSame(1, $student->notifications()->count());

        $this->actingAs($student)->get('/mahasiswa/profil')->assertOk()
            ->assertSee(route('mahasiswa.notifikasi.buka', $notification->id), false);
        $this->post(route('mahasiswa.notifikasi.buka', $notification->id))->assertRedirect(route('mahasiswa.dokumen'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_notification_actions_are_scoped_to_the_authenticated_owner(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $otherStudent = User::factory()->create(['role' => 'mahasiswa']);
        $application = $student->pengajuan()->create(['status' => 'diproses']);
        $student->notify(new StatusPengajuanNotification($application, 'diverifikasi'));
        $otherStudent->notify(new StatusPengajuanNotification($application, 'diverifikasi'));
        $notification = $student->notifications()->sole();

        $this->actingAs($otherStudent)->post(route('mahasiswa.notifikasi.buka', $notification->id))->assertNotFound();
        $this->assertNull($notification->fresh()->read_at);
        $this->post(route('mahasiswa.notifikasi.baca-semua'))->assertRedirect();
        $this->assertSame(0, $otherStudent->unreadNotifications()->count());
        $this->assertSame(1, $student->unreadNotifications()->count());
        $this->post(route('admin.notifikasi.baca-semua'))->assertForbidden();
    }

    public function test_admin_opens_its_own_notification_without_trusting_a_stored_external_url(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $otherAdmin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $application = $student->pengajuan()->create(['status' => 'diproses']);
        $admin->notify(new PengajuanBaruNotification($application, $student->name));
        $notification = $admin->notifications()->sole();
        $notification->update(['data' => [...$notification->data, 'url' => 'https://example.com/untrusted']]);

        $this->actingAs($otherAdmin)->post(route('admin.notifikasi.buka', $notification->id))->assertNotFound();
        $this->assertNull($notification->fresh()->read_at);
        $this->actingAs($admin)->post(route('admin.notifikasi.buka', $notification->id))
            ->assertRedirect(route('admin.pengajuan.show', $application));
        $this->assertNotNull($notification->fresh()->read_at);
    }
}
