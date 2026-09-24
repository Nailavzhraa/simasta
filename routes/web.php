<?php

use App\Http\Controllers\Admin\MahasiswaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('auth.login');
});

Route::post('/login', function (Request $request) {

    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        if ($user->role === 'pembimbing') {
            return redirect('/pembimbing/dashboard');
        }

        return redirect('/mahasiswa/dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');

});

Route::get('/register', function () {
    return view('auth.register');
});

Route::post('/register', function (\Illuminate\Http\Request $request) {

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
    ]);

    \App\Models\User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => $validated['password'],
        'role' => 'mahasiswa',
    ]);

    return redirect('/login')->with(
        'success',
        'Registrasi berhasil. Silakan login.'
    );
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

    return redirect('/login')->with(
        'success',
        'Anda berhasil logout.'
    );

});

/*
|--------------------------------------------------------------------------
| MAHASISWA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
});

Route::get('/mahasiswa/dashboard', function () {
    return view('mahasiswa.dashboard');
});

Route::get('/mahasiswa/profil', function () {
    return view('mahasiswa.profil');

});

Route::get('/mahasiswa/pengajuan', function () {
    return view('mahasiswa.pengajuan');

});

Route::get('/mahasiswa/dokumen', function () {
    return view('mahasiswa.dokumen');

});

Route::get('/mahasiswa/penempatan', function () {
    return view('mahasiswa.penempatan');

});

Route::get('/mahasiswa/logbook', function () {
    return view('mahasiswa.logbook');

});

Route::get('/mahasiswa/penilaian', function () {
    return view('mahasiswa.penilaian');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
});
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

Route::resource('/admin/mahasiswa', MahasiswaController::class)
    ->names('admin.mahasiswa');

Route::get('/admin/pengajuan', function () {
    return view('admin.pengajuan.index');
});

Route::get('/admin/pembimbing', function () {
    return view('admin.pembimbing.index');
});

Route::get('/admin/penempatan', function () {
    return view('admin.penempatan.index');
});

Route::get('/admin/logbook', function () {
    return view('admin.logbook.index');
});

Route::get('/admin/penilaian', function () {
    return view('admin.penilaian.index');
});

Route::get('/admin/laporan', function () {
    return view('admin.laporan.index');
});
/*
|--------------------------------------------------------------------------
| PEMBIMBING
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:pembimbing'])->group(function () {
});
Route::get('/pembimbing/dashboard', function () {
    return view('pembimbing.dashboard');
});