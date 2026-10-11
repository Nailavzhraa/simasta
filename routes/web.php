<?php

use App\Http\Controllers\Admin\MahasiswaController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

    Route::post('/login', function (Request $request) {

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return match (Auth::user()->role) {
                'admin' => redirect('/admin/dashboard'),
                'pembimbing' => redirect('/pembimbing/dashboard'),
                default => redirect('/mahasiswa/dashboard'),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    });

    Route::get('/register', fn () => view('auth.index'));

    Route::post('/register', function (Request $request) {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'], // otomatis di-hash lewat cast 'hashed' di model User
            'role' => 'mahasiswa',
        ]);

        return redirect('/login')->with('success', 'Registrasi berhasil. Silakan login.');
    });
});

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login')->with('success', 'Anda berhasil logout.');
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| MAHASISWA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {
        Route::get('/dashboard', fn () => view('mahasiswa.dashboard'))->name('dashboard');
        Route::get('/profil', fn () => view('mahasiswa.profil'))->name('profil');
        Route::get('/dokumen', fn () => view('mahasiswa.dokumen'))->name('dokumen');
        Route::get('/penempatan', fn () => view('mahasiswa.penempatan'))->name('penempatan');
        Route::get('/logbook', fn () => view('mahasiswa.logbook'))->name('logbook');
        Route::get('/penilaian', fn () => view('mahasiswa.penilaian'))->name('penilaian');
        Route::get('bantuan',    fn () => view('mahasiswa.bantuan'))->name('mahasiswa.bantuan');
        Route::get('pengumuman', fn () => view('mahasiswa.pengumuman'))->name('mahasiswa.pengumuman');
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

        // nama route jadi admin.mahasiswa.index, admin.mahasiswa.create, dst.
        Route::resource('mahasiswa', MahasiswaController::class);

        Route::get('/pengajuan', fn () => view('admin.pengajuan.index'))->name('pengajuan.index');
        Route::get('/pembimbing', fn () => view('admin.pembimbing.index'))->name('pembimbing.index');
        Route::get('/penempatan', fn () => view('admin.penempatan.index'))->name('penempatan.index');
        Route::get('/logbook', fn () => view('admin.logbook.index'))->name('logbook.index');
        Route::get('/penilaian', fn () => view('admin.penilaian.index'))->name('penilaian.index');
        Route::get('/laporan', fn () => view('admin.laporan.index'))->name('laporan.index');
    });

/*
|--------------------------------------------------------------------------
| PEMBIMBING
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:pembimbing'])
    ->prefix('pembimbing')
    ->name('pembimbing.')
    ->group(function () {
        Route::get('/dashboard', fn () => view('pembimbing.dashboard'))->name('dashboard');
    });