<?php
use App\Http\Controllers\Mahasiswa\PengajuanDraftController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Mahasiswa\LaporanController as MahasiswaLaporanController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\PenilaianController as AdminPenilaianController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\PengajuanController as AdminPengajuanController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\Mahasiswa\DokumenController;
use App\Http\Controllers\Mahasiswa\LogbookController as MahasiswaLogbookController;
use App\Http\Controllers\Mahasiswa\PenilaianController as MahasiswaPenilaianController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DivisiController;
use App\Http\Controllers\Admin\PembimbingController;
use App\Http\Controllers\Admin\PenempatanController;
use App\Http\Controllers\Admin\LogbookController as AdminLogbookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\NotifikasiController as AdminNotifikasiController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('home'));

/*
|--------------------------------------------------------------------------
| AUTH (hanya untuk yang belum login)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', fn () => view('auth.index'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::get('/register', fn () => view('auth.index'));
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');

    // Login dengan Google
    Route::get('/auth/google/redirect', [GoogleController::class, 'redirect']);
    Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
    Route::get('/auth/google/confirm', [GoogleController::class, 'confirm'])->name('google.confirm');
    Route::post('/auth/google/confirm', [GoogleController::class, 'link'])->middleware('throttle:5,1');

});

/*
|--------------------------------------------------------------------------
| VERIFIKASI OTP
|--------------------------------------------------------------------------
*/

Route::get('/verifikasi-otp', [OtpController::class, 'show'])->name('otp.show');
Route::post('/verifikasi-otp', [OtpController::class, 'verify'])->middleware('throttle:10,1')->name('otp.verify');
Route::post('/verifikasi-otp/kirim-ulang', [OtpController::class, 'resend'])->middleware('throttle:3,1')->name('otp.resend');

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| MAHASISWA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {
        // Profil tetap terbuka bagi mahasiswa yang belum melengkapi data.
        Route::get('/profil', [ProfileController::class, 'show'])->name('profil');
        Route::post('/profil', [ProfileController::class, 'update'])->name('profil.simpan');

        Route::middleware('profil.lengkap')->group(function () {
            Route::get('/dashboard', fn () => view('mahasiswa.dashboard'))->name('dashboard');
            Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen');
            Route::post('/dokumen', [DokumenController::class, 'store'])->name('dokumen.store');
            Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan');
            Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.simpan');
            Route::get('/pengajuan/draft', [PengajuanDraftController::class, 'show'])->name('pengajuan.draft.show');
            Route::post('/pengajuan/draft', [PengajuanDraftController::class, 'save'])->middleware('throttle:60,1')->name('pengajuan.draft.save');
            Route::delete('/pengajuan/draft', [PengajuanDraftController::class, 'destroy'])->name('pengajuan.draft.destroy');
            Route::get('/surat', function () {return view('mahasiswa.surat');})->middleware('magang.diterima')->name('surat');            
            Route::get('/bantuan', fn () => view('mahasiswa.bantuan'))->name('bantuan');
            Route::get('/pengumuman', fn () => view('mahasiswa.pengumuman'))->name('pengumuman');
            Route::get('/penempatan', fn () => redirect('/mahasiswa/profil#posisi'))->name('penempatan');

            Route::middleware('magang.diterima')->group(function () {
                Route::get('/presensi', fn () => view('mahasiswa.presensi'))->name('presensi');
                Route::get('/logbook', [MahasiswaLogbookController::class, 'index'])->name('logbook');
                Route::post('/logbook', [MahasiswaLogbookController::class, 'store'])->name('logbook.simpan');
                Route::get('/penilaian', [MahasiswaPenilaianController::class, 'index'])->name('penilaian');
                Route::post('/penilaian/template', [MahasiswaPenilaianController::class, 'kirimTemplate'])->name('penilaian.template.kirim');
                Route::get('/penilaian/hasil/{berkas}/unduh', [MahasiswaPenilaianController::class, 'unduhHasil'])->name('penilaian.hasil.unduh');
            });
        });
    });


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');

        Route::post('/notifikasi/{notificationId}/buka', [AdminNotifikasiController::class, 'buka'])->name('notifikasi.buka');
        Route::post('/notifikasi/baca-semua', [AdminNotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');

        // nama route jadi admin.mahasiswa.index, admin.mahasiswa.create, dst.
        Route::resource('mahasiswa', MahasiswaController::class);

        // Pengajuan magang: daftar, detail, dan keputusan
        Route::get('/pengajuan', [AdminPengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/{pengajuan}/dokumen/{jenis}', [AdminPengajuanController::class, 'dokumen'])->name('pengajuan.dokumen');
        Route::get('/pengajuan/{pengajuan}', [AdminPengajuanController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/{pengajuan}/verifikasi', [AdminPengajuanController::class, 'verifikasi'])->name('pengajuan.verifikasi');
        Route::post('/pengajuan/{pengajuan}/terima', [AdminPengajuanController::class, 'terima'])->name('pengajuan.terima');
        Route::post('/pengajuan/{pengajuan}/tolak', [AdminPengajuanController::class, 'tolak'])->name('pengajuan.tolak');

        // PENEMPATAN MAGANG
        Route::get('/penempatan', [PenempatanController::class, 'index'])->name('penempatan.index');
        Route::post('/penempatan/{pengajuan}', [PenempatanController::class, 'simpan'])->name('penempatan.simpan');
        Route::patch('/penempatan/{pengajuan}/mulai', [PenempatanController::class, 'mulai'])->name('penempatan.mulai');
        Route::patch('/penempatan/{pengajuan}/selesai', [PenempatanController::class, 'selesai'])->name('penempatan.selesai');
        
        // logbook 
        Route::get('/logbook', [AdminLogbookController::class,'index'])->name('logbook.index');
        Route::get('/logbook/mahasiswa/{user}', [AdminLogbookController::class,'show'])->name('logbook.show');
        Route::post('/logbook/{logbook}/tinjau', [AdminLogbookController::class,'tinjau'])->name('logbook.tinjau');

        
        // ==========================================
        // KELOLA DIVISI & TIM KERJA
        // ==========================================

        // CRUD Divisi (tetap menggunakan route lama)
        Route::get('/divisi', [DivisiController::class, 'index'])
            ->name('divisi.index');

        Route::post('/divisi', [DivisiController::class, 'simpanDivisi'])
            ->name('divisi.simpan');

        Route::put('/divisi/{divisi}', [DivisiController::class, 'updateDivisi'])
            ->name('divisi.update');

        Route::delete('/divisi/{divisi}', [DivisiController::class, 'hapusDivisi'])
            ->name('divisi.hapus');


        // CRUD Tim Kerja (fitur baru)
        Route::post('/divisi/tim', [DivisiController::class, 'simpanTimKerja'])
            ->name('divisi.tim.simpan');

        Route::put('/divisi/tim/{timKerja}', [DivisiController::class, 'updateTimKerja'])
            ->name('divisi.tim.update');

        Route::delete('/divisi/tim/{timKerja}', [DivisiController::class, 'hapusTimKerja'])
            ->name('divisi.tim.hapus');


                /*
        |--------------------------------------------------------------------------
        | NAVBAR 5 - PENILAIAN & SERTIFIKAT
        |--------------------------------------------------------------------------
        */

        // Dashboard Penilaian & Sertifikat Admin
        Route::get('/penilaian', [
            AdminPenilaianController::class,
            'index'
        ])->name('penilaian.index');

        // Admin mengirim hasil penilaian ke mahasiswa
        Route::post('/penilaian/{pengajuan}/hasil', [
            AdminPenilaianController::class,
            'kirimHasil'
        ])->name('penilaian.hasil.kirim');

        // Admin mengunduh template dari mahasiswa
        Route::get('/penilaian/berkas/{berkas}/template', [
            AdminPenilaianController::class,
            'unduhTemplate'
        ])->name('penilaian.template.unduh');

        
/*
|--------------------------------------------------------------------------
| NAVBAR 6 — LAPORAN ADMIN
|--------------------------------------------------------------------------
*/

// Dashboard utama Laporan
Route::get('/laporan', [
    AdminLaporanController::class,
    'index'
])->name('laporan.index');

// Export rekap laporan ke CSV
// Harus berada sebelum route dinamis {laporan}
Route::get('/laporan/export/csv', [
    AdminLaporanController::class,
    'exportCsv'
])->name('laporan.export');

// Detail laporan mahasiswa
Route::get('/laporan/{laporan}', [
    AdminLaporanController::class,
    'show'
])->name('laporan.show');

// Unduh file laporan mahasiswa
Route::get('/laporan/{laporan}/unduh', [
    AdminLaporanController::class,
    'unduh'
])->name('laporan.unduh');

// Admin meninjau, meminta revisi,
// atau menyetujui laporan
Route::post('/laporan/{laporan}/status', [
    AdminLaporanController::class,
    'updateStatus'
])->name('laporan.status');


        /*
        |--------------------------------------------------------------------------
        | DATA PEMBIMBING - ADMIN
        |--------------------------------------------------------------------------
        */

        // Menampilkan daftar pembimbing
        Route::get('/pembimbing', [
            PembimbingController::class, 'index'
        ])->name('pembimbing.index');

        // Menampilkan form tambah pembimbing
        Route::get('/pembimbing/create', [
            PembimbingController::class, 'create'
        ])->name('pembimbing.create');

        // Menyimpan pembimbing baru
        Route::post('/pembimbing', [
            PembimbingController::class, 'store'
        ])->name('pembimbing.simpan');

        // Menampilkan form edit pembimbing
        Route::get('/pembimbing/{pembimbing}/edit', [
            PembimbingController::class, 'edit'
        ])->name('pembimbing.edit');

        // Menyimpan perubahan pembimbing
        Route::match(['put', 'patch'], '/pembimbing/{pembimbing}', [
            PembimbingController::class, 'update'
        ])->name('pembimbing.update');

        // Menghapus pembimbing
        Route::delete('/pembimbing/{pembimbing}', [
            PembimbingController::class, 'destroy'
        ])->name('pembimbing.hapus');

});



/*
|--------------------------------------------------------------------------
| PREVIEW STATUS (hanya di local, untuk tes tampilan)
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    Route::get('/preview/{status}', function ($status) {
        $status === 'reset'
            ? session()->forget('preview_status')
            : session(['preview_status' => $status]);

        return redirect('/mahasiswa/dashboard');
    })->middleware('auth');

    
/*
|--------------------------------------------------------------------------
| PREVIEW PENILAIAN & SERTIFIKAT MAHASISWA
|--------------------------------------------------------------------------
| Hanya untuk development lokal.
| Tidak mengubah status pengajuan atau data MySQL.
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {

    Route::middleware(['auth', 'role:mahasiswa'])
        ->get('/preview/mahasiswa/penilaian', function () {

            // Data pengajuan simulasi untuk tampilan.
            $pengajuan = new \App\Models\Pengajuan([
                'status' => 'diterima',
            ]);

            // Relasi simulasi tanpa query database.
            $divisi = new \App\Models\Divisi();
            $divisi->nama = 'Pranata Komputer';

            $pengajuan->setRelation('divisi', $divisi);

            // Pembimbing belum ditentukan.
            $pengajuan->setRelation('pembimbingRef', null);

            // Belum ada dokumen penilaian.
            $berkas = null;

            // Sertifikat belum diterbitkan.
            $sertifikat = null;

            return view('mahasiswa.penilaian', [
                'pengajuan' => $pengajuan,
                'berkas' => $berkas,
                'sertifikat' => $sertifikat,
            ]);

        })->name('preview.mahasiswa.penilaian');

}


/*
|--------------------------------------------------------------------------
| PREVIEW LOGBOOK MAHASISWA
|--------------------------------------------------------------------------
| Khusus development lokal.
| Tidak mengubah database atau status pengajuan.
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {

    Route::middleware(['auth', 'role:mahasiswa'])
        ->get('/preview/mahasiswa/logbook', function () {

            $pengajuan = new \App\Models\Pengajuan([
                'status' => 'diterima',
            ]);

            $divisi = new \App\Models\Divisi();
            $divisi->nama = 'Pranata Komputer';

            $pengajuan->setRelation('divisi', $divisi);
            $pengajuan->setRelation('pembimbingRef', null);

            $logbooks = new \Illuminate\Pagination\LengthAwarePaginator(
                [],
                0,
                10,
                1,
                [
                    'path' => request()->url(),
                ]
            );

            $statistik = [
                'total' => 0,
                'menunggu' => 0,
                'ditinjau' => 0,
            ];

            return view('mahasiswa.logbook', [
                'pengajuan' => $pengajuan,
                'logbooks' => $logbooks,
                'statistik' => $statistik,
                'modePreview' => true,
            ]);

        })->name('preview.mahasiswa.logbook');

}

// ========================================
// LUPA DAN RESET KATA SANDI SIMASTA
// ========================================

Route::middleware('guest')->group(function () {

    // Halaman lupa kata sandi
    Route::get('/forgot-password', [
        PasswordResetController::class,
        'showLinkRequestForm'
    ])->name('password.request');

    // Mengirim email reset password
    Route::post('/forgot-password', [
        PasswordResetController::class,
        'sendResetLinkEmail'
    ])->middleware('throttle:5,1')
      ->name('password.email');

    // Halaman membuat password baru
    Route::get('/reset-password/{token}', [
        PasswordResetController::class,
        'showResetForm'
    ])->name('password.reset');

    // Menyimpan password baru
    Route::post('/reset-password', [
        PasswordResetController::class,
        'reset'
    ])->middleware('throttle:5,1')
      ->name('password.update');

});

}