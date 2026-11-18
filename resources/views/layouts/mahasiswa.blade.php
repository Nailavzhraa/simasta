
{{-- resources/views/layouts/mahasiswa.blade.php --}}

@php
    use App\Models\Pengajuan;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $userSidebar = auth()->user();

    $mahasiswaSidebar = $userSidebar?->mahasiswa;

    /*
    |--------------------------------------------------------------------------
    | STATUS PENGAJUAN
    |--------------------------------------------------------------------------
    | Draft tidak dihitung sebagai pengajuan resmi.
    */

    $pengajuanSidebar = Pengajuan::query()
        ->where('user_id', auth()->id())
        ->latest('id')
        ->first();

    $statusMagang = $pengajuanSidebar?->status ?? 'belum_diajukan';

    $diterima = $statusMagang === 'diterima';

    /*
    |--------------------------------------------------------------------------
    | IDENTITAS PENGGUNA
    |--------------------------------------------------------------------------
    */

    $namaSidebar = $mahasiswaSidebar?->nama
        ?: ($userSidebar?->name ?? 'Mahasiswa');

    $emailSidebar = $userSidebar?->email ?? '';

    $inisialSidebar = Str::upper(
        Str::substr($namaSidebar, 0, 1)
    );

    /*
    |--------------------------------------------------------------------------
    | FOTO PROFIL
    |--------------------------------------------------------------------------
    | Gunakan kolom foto Mahasiswa jika tersedia.
    | Jika belum ada foto, tampilkan inisial.
    */

    $fotoSidebar = data_get($mahasiswaSidebar, 'foto');

    $fotoSidebarUrl = null;

    if (filled($fotoSidebar)) {
        $fotoPath = ltrim(
            str_replace('\\', '/', (string) $fotoSidebar),
            '/'
        );

        if (Str::startsWith($fotoPath, 'storage/')) {
            $fotoPath = Str::after($fotoPath, 'storage/');
        }

        $fotoSidebarUrl = Storage::disk('public')->url(
            $fotoPath
        );
    }

    /*
    |--------------------------------------------------------------------------
    | NAVIGASI MAHASISWA
    |--------------------------------------------------------------------------
    */

    $menus = [
        [
            'label' => 'Beranda',
            'path' => 'mahasiswa/dashboard',
            'icon' => 'bi-house-door',
            'locked' => false,
        ],
        [
            'label' => 'Pengajuan Magang',
            'path' => 'mahasiswa/dokumen',
            'icon' => 'bi-file-earmark-text',
            'locked' => false,
        ],
        [
            'label' => 'Surat Keterangan',
            'path' => 'mahasiswa/surat',
            'icon' => 'bi-file-earmark-check',
            'locked' => !$diterima,
        ],
        [
            'label' => 'Logbook Magang',
            'path' => 'mahasiswa/logbook',
            'icon' => 'bi-journal-text',
            'locked' => !$diterima,
        ],
        [
            'label' => 'Penilaian & Sertifikat',
            'path' => 'mahasiswa/penilaian',
            'icon' => 'bi-award',
            'locked' => !$diterima,
        ],
        [
            'label' => 'Bantuan',
            'path' => 'mahasiswa/bantuan',
            'icon' => 'bi-question-circle',
            'locked' => false,
        ],
    ];

    $statusLabels = [
        'belum_diajukan' => 'Calon Peserta',
        'diproses' => 'Pengajuan Diproses',
        'diverifikasi' => 'Dalam Verifikasi',
        'diterima' => 'Peserta Magang',
        'ditolak' => 'Pengajuan Ditolak',
    ];

    $labelStatus = $statusLabels[$statusMagang]
        ?? 'Calon Peserta';
@endphp

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
>

<style>
    :root {
        --sb-w: 250px;
    }

    html.sb-collapsed {
        --sb-w: 78px;
    }

    /* SIDEBAR UTAMA */

    .mhs-side {
        --mhs-blue: #1d5fa8;
        --mhs-blue-dark: #12457f;
        --mhs-green: #2fa266;
        --mhs-orange: #f5a65b;
        --mhs-orange-dark: #ee8a3a;

        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 100;

        display: flex;
        flex-direction: column;

        width: var(--sb-w);
        height: 100vh;

        color: #ffffff;
        overflow: hidden;

        background:
            radial-gradient(
                circle at 100% 0%,
                rgba(47, 162, 102, .32),
                transparent 40%
            ),
            radial-gradient(
                circle at 0% 100%,
                rgba(245, 166, 91, .22),
                transparent 35%
            ),
            linear-gradient(
                180deg,
                var(--mhs-blue),
                var(--mhs-blue-dark)
            );

        transition: width .25s ease;
    }

    .mhs-side * {
        box-sizing: border-box;
    }

    /* LOGO */

    .mhs-logo {
        display: flex;
        align-items: center;
        gap: 12px;

        min-height: 88px;
        padding: 18px;

        border-bottom: 1px solid rgba(255,255,255,.15);

        white-space: nowrap;
        flex-shrink: 0;
    }

    .mhs-logo img {
        width: 42px;
        height: 42px;

        object-fit: contain;
        flex-shrink: 0;
    }

    .mhs-logo strong {
        display: block;
        font-size: 17px;
        font-weight: 800;
        line-height: 1.25;
        letter-spacing: .2px;
    }

    .mhs-logo small {
        display: block;
        margin-top: 3px;

        font-size: 10px;
        letter-spacing: .8px;
        opacity: .85;
    }

    /* NAVIGASI */

    .mhs-menu {
        list-style: none;
        margin: 0;
        padding: 12px;

        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;

        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,.25) transparent;
    }

    .mhs-menu li {
        margin-bottom: 4px;
    }

    .mhs-menu a,
    .mhs-menu .locked,
    .mhs-toggle {
        display: flex;
        align-items: center;
        gap: 12px;

        width: 100%;
        min-height: 45px;
        padding: 11px 14px;

        border: none;
        border-radius: 12px;

        background: transparent;
        color: #ffffff;

        text-decoration: none;
        text-align: left;

        font-family: inherit;
        font-size: 13px;
        font-weight: 550;

        cursor: pointer;
        white-space: nowrap;

        transition:
            background .2s ease,
            transform .2s ease,
            color .2s ease;
    }

    .mhs-menu i {
        width: 20px;
        flex-shrink: 0;

        text-align: center;
        font-size: 18px;
    }

    .mhs-menu a:hover,
    .mhs-toggle:hover {
        background: rgba(255,255,255,.14);
    }

    /* MENU AKTIF */

    .mhs-menu a.active {
        background: linear-gradient(
            120deg,
            var(--mhs-orange),
            var(--mhs-orange-dark)
        );

        color: #ffffff;
        font-weight: 700;

        box-shadow: 0 6px 16px rgba(238,138,58,.28);
    }

    /* MENU TERKUNCI */

    .mhs-menu .locked {
        opacity: .52;
        cursor: not-allowed;
        position: relative;
    }

    .mhs-menu .locked:hover {
        background: rgba(255,255,255,.06);
    }

    .mhs-menu .locked .lock {
        margin-left: auto;
        width: auto;
        font-size: 12px;
    }

    /* TOGGLE SIDEBAR */

    .mhs-toggle {
        margin-bottom: 5px;
        border-bottom: 1px solid rgba(255,255,255,.12);
        border-radius: 12px 12px 0 0;
    }

    .mhs-toggle i {
        transition: transform .25s ease;
    }

    /* PROFIL PENGGUNA */

    .mhs-user {
        margin: 12px;
        padding: 9px;

        border-radius: 16px;
        background: rgba(255,255,255,.12);

        flex-shrink: 0;
        overflow: hidden;
    }

    .mhs-user-top {
        display: flex;
        align-items: center;
        gap: 10px;

        padding: 8px;
        border-radius: 12px;

        color: #ffffff;
        text-decoration: none;

        transition: background .2s ease;
    }

    .mhs-user-top:hover {
        background: rgba(255,255,255,.1);
        color: #ffffff;
    }

    .mhs-user-top.active {
        background: linear-gradient(
            120deg,
            var(--mhs-orange),
            var(--mhs-orange-dark)
        );
    }

    .mhs-avatar {
        display: grid;
        place-items: center;

        width: 43px;
        height: 43px;

        border-radius: 50%;
        background: var(--mhs-green);

        color: #ffffff;
        font-size: 16px;
        font-weight: 800;

        flex-shrink: 0;
        overflow: hidden;
    }

    .mhs-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .mhs-user-info {
        min-width: 0;
        flex: 1;
    }

    .mhs-user-info b {
        display: block;

        font-size: 13px;
        font-weight: 750;

        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .mhs-user-info small {
        display: block;
        margin-top: 2px;

        font-size: 10px;
        opacity: .85;

        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .mhs-badge {
        display: inline-block;

        margin-top: 5px;
        padding: 3px 8px;

        border-radius: 20px;

        background: var(--mhs-orange);
        color: #583000;

        font-size: 10px;
        font-weight: 700;
    }

    .mhs-badge.ok {
        background: var(--mhs-green);
        color: #ffffff;
    }

    .mhs-badge.rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .mhs-chev {
        font-size: 14px;
        opacity: .8;
        flex-shrink: 0;
    }

    /* LOGOUT */

    .mhs-logout {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;

        width: 100%;
        min-height: 40px;
        padding: 10px;

        border: none;
        border-radius: 11px;

        background: rgba(255,255,255,.16);
        color: #ffffff;

        font-family: inherit;
        font-size: 12px;
        font-weight: 700;

        cursor: pointer;
        transition: background .2s;
    }

    .mhs-logout:hover {
        background: var(--mhs-orange);
        color: #583000;
    }

    /* TOAST */

    .mhs-toast {
        position: fixed;
        left: 50%;
        bottom: 24px;
        z-index: 300;

        max-width: 90vw;
        padding: 12px 17px;

        border-radius: 12px;
        background: #14213d;
        color: #ffffff;

        font-size: 12px;
        line-height: 1.6;

        opacity: 0;
        pointer-events: none;

        transform: translate(-50%, 16px);
        transition: .25s ease;
    }

    .mhs-toast.show {
        opacity: 1;
        transform: translate(-50%, 0);
    }

    /* SIDEBAR COLLAPSED */

    html.sb-collapsed .mhs-logo {
        justify-content: center;
        padding: 18px 0;
    }

    html.sb-collapsed .mhs-logo > div,
    html.sb-collapsed .mhs-menu span,
    html.sb-collapsed .mhs-user-info,
    html.sb-collapsed .mhs-chev,
    html.sb-collapsed .mhs-logout span {
        display: none;
    }

    html.sb-collapsed .mhs-menu {
        padding: 12px 10px;
    }

    html.sb-collapsed .mhs-menu a,
    html.sb-collapsed .mhs-menu .locked,
    html.sb-collapsed .mhs-toggle {
        justify-content: center;
        padding: 11px 0;
    }

    html.sb-collapsed .mhs-toggle i {
        transform: rotate(180deg);
    }

    html.sb-collapsed .mhs-menu .locked .lock {
        position: absolute;
        top: 4px;
        right: 8px;
        margin: 0;
        font-size: 9px;
    }

    html.sb-collapsed .mhs-user {
        margin: 9px;
        padding: 5px;
    }

    html.sb-collapsed .mhs-user-top {
        justify-content: center;
        padding: 6px 0;
    }

    /* SIDEBAR MOBILE */

    .mhs-mobile-button {
        display: none;
    }

    .mhs-mobile-overlay {
        display: none;
    }

    @media (max-width: 600px) {
        :root,
        html.sb-collapsed {
            --sb-w: 0px;
        }

        .mhs-side {
            display: flex !important;
            width: 260px !important;
            transform: translateX(-100%);
            transition: transform .25s ease;
            z-index: 1100;
        }

        .mhs-side.mobile-open {
            transform: translateX(0);
        }

        .mhs-mobile-button {
            position: fixed;
            top: 17px;
            left: 12px;
            z-index: 1050;

            display: grid;
            place-items: center;

            width: 42px;
            height: 42px;

            border: 1px solid #dbe5f0;
            border-radius: 11px;

            background: #ffffff;
            color: #1d5fa8;

            font-size: 20px;
            cursor: pointer;
        }

        .mhs-mobile-overlay.mobile-open {
            position: fixed;
            inset: 0;
            z-index: 1090;

            display: block;
            background: rgba(15,23,42,.48);
        }

        .main,
        html.sb-collapsed .main {
            margin-left: 0 !important;
        }

        .topbar {
            padding-left: 64px !important;
            padding-right: 15px !important;
        }

        .mhs-logo > div,
        .mhs-menu span,
        .mhs-user-info,
        .mhs-chev,
        .mhs-logout span {
            display: block !important;
        }

        .mhs-menu a,
        .mhs-menu .locked {
            justify-content: flex-start !important;
            padding: 11px 14px !important;
        }

        .mhs-toggle {
            display: none;
        }

        .mhs-user-top {
            justify-content: flex-start !important;
        }

        .mhs-menu .locked .lock {
            position: static !important;
            margin-left: auto !important;
            font-size: 12px !important;
        }
    }

    @media (min-width: 601px) {
        .mhs-mobile-button,
        .mhs-mobile-overlay {
            display: none !important;
        }
    }

    /* AKSESIBILITAS */

    .mhs-menu a:focus-visible,
    .mhs-menu button:focus-visible,
    .mhs-user-top:focus-visible,
    .mhs-logout:focus-visible,
    .mhs-mobile-button:focus-visible {
        outline: 2px solid #ffffff;
        outline-offset: 2px;
    }
</style>

{{-- TOMBOL MENU MOBILE --}}
<button
    type="button"
    class="mhs-mobile-button"
    id="mhsMobileOpen"
    aria-label="Buka menu mahasiswa"
    aria-controls="mhsSidebar"
    aria-expanded="false"
>
    <i class="bi bi-list"></i>
</button>

{{-- OVERLAY MOBILE --}}
<div
    id="mhsMobileOverlay"
    class="mhs-mobile-overlay"
></div>

{{-- SIDEBAR --}}
<aside
    class="mhs-side"
    id="mhsSidebar"
    aria-label="Navigasi Mahasiswa"
>

    {{-- LOGO SIMASTA --}}
    <div class="mhs-logo">
        <img
            src="{{ asset('images/logo-bps.png') }}"
            alt="Logo BPS Kota Batu"
        >

        <div>
            <strong>SIMASTA</strong>
            <small>BPS KOTA BATU</small>
        </div>
    </div>

    {{-- NAVIGASI --}}
    <ul class="mhs-menu">

        <li>
            <button
                type="button"
                class="mhs-toggle"
                id="mhsToggle"
                title="Perkecil atau perbesar sidebar"
                aria-label="Perkecil atau perbesar sidebar"
            >
                <i class="bi bi-chevron-double-left"></i>
            </button>
        </li>

        @foreach($menus as $menu)
            @php
                $aktifMenu = request()->is(
                    $menu['path'] . '*'
                );
            @endphp

            <li>
                @if($menu['locked'])
                    <button
                        type="button"
                        class="locked"
                        data-locked="{{ $menu['label'] }}"
                        title="{{ $menu['label'] }} belum tersedia"
                        aria-disabled="true"
                    >
                        <i class="bi {{ $menu['icon'] }}"></i>

                        <span>{{ $menu['label'] }}</span>

                        <i class="bi bi-lock-fill lock"></i>
                    </button>
                @else
                    <a
                        href="{{ url($menu['path']) }}"
                        class="{{ $aktifMenu ? 'active' : '' }}"
                        title="{{ $menu['label'] }}"
                        @if($aktifMenu) aria-current="page" @endif
                    >
                        <i class="bi {{ $menu['icon'] }}"></i>

                        <span>{{ $menu['label'] }}</span>
                    </a>
                @endif
            </li>
        @endforeach

    </ul>

    {{-- PROFIL PENGGUNA --}}
    <div class="mhs-user">

        <a
            href="{{ url('mahasiswa/profil') }}"
            title="Profil dan posisi magang"
            class="mhs-user-top {{ request()->is('mahasiswa/profil*') ? 'active' : '' }}"
        >
            <div class="mhs-avatar">

                @if($fotoSidebarUrl)
                    <img
                        src="{{ $fotoSidebarUrl }}"
                        alt="Foto profil {{ $namaSidebar }}"
                        loading="lazy"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='block';"
                    >
                    <span style="display:none">
                        {{ $inisialSidebar }}
                    </span>
                @else
                    <span>{{ $inisialSidebar }}</span>
                @endif

            </div>

            <div class="mhs-user-info">

                <b>{{ $namaSidebar }}</b>

                <small>{{ $emailSidebar }}</small>

                <span class="mhs-badge
                    {{ $diterima ? 'ok' : '' }}
                    {{ $statusMagang === 'ditolak' ? 'rejected' : '' }}
                ">
                    {{ $labelStatus }}
                </span>

            </div>

            <i class="bi bi-chevron-right mhs-chev"></i>
        </a>

        {{-- LOGOUT --}}
        <form
            method="POST"
            action="{{ url('logout') }}"
        >
            @csrf

            <button
                type="submit"
                class="mhs-logout"
                title="Keluar dari SIMASTA"
            >
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar</span>
            </button>
        </form>

    </div>

</aside>

{{-- TOAST MENU TERKUNCI --}}
<div
    class="mhs-toast"
    id="mhsToast"
    role="status"
    aria-live="polite"
></div>

<script>
(function () {
    'use strict';

    const root = document.documentElement;
    const sidebar = document.getElementById('mhsSidebar');

    const toggle = document.getElementById('mhsToggle');

    const mobileOpen = document.getElementById(
        'mhsMobileOpen'
    );

    const mobileOverlay = document.getElementById(
        'mhsMobileOverlay'
    );

    const KEY = 'simasta_sidebar_collapsed';

    /*
     * SIDEBAR DESKTOP
     */

    try {
        if (localStorage.getItem(KEY) === '1') {
            root.classList.add('sb-collapsed');
        }
    } catch (error) {}

    if (toggle) {
        toggle.addEventListener('click', function () {
            const collapsed = root.classList.toggle(
                'sb-collapsed'
            );

            try {
                localStorage.setItem(
                    KEY,
                    collapsed ? '1' : '0'
                );
            } catch (error) {}
        });
    }

    /*
     * SIDEBAR MOBILE
     */

    function setMobileOpen(open) {
        if (!sidebar) return;

        sidebar.classList.toggle('mobile-open', open);

        mobileOverlay?.classList.toggle(
            'mobile-open',
            open
        );

        mobileOpen?.setAttribute(
            'aria-expanded',
            open ? 'true' : 'false'
        );

        document.body.style.overflow = open
            ? 'hidden'
            : '';
    }

    mobileOpen?.addEventListener('click', function () {
        const isOpen = sidebar?.classList.contains(
            'mobile-open'
        );

        setMobileOpen(!isOpen);
    });

    mobileOverlay?.addEventListener(
        'click',
        function () {
            setMobileOpen(false);
        }
    );

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            setMobileOpen(false);
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 600) {
            setMobileOpen(false);
        }
    });

    /*
     * PESAN MENU TERKUNCI
     */

    const toast = document.getElementById('mhsToast');

    let toastTimer = null;

    function showToast(message) {
        if (!toast) return;

        toast.textContent = message;

        toast.classList.add('show');

        clearTimeout(toastTimer);

        toastTimer = setTimeout(function () {
            toast.classList.remove('show');
        }, 3500);
    }

    document.querySelectorAll(
        '[data-locked]'
    ).forEach(function (button) {
        button.addEventListener('click', function () {
            showToast(
                '"' + button.dataset.locked +
                '" tersedia setelah pengajuan magang ' +
                'diterima oleh BPS Kota Batu.'
            );
        });
    });

    @if(session('terkunci'))
        showToast(@json(session('terkunci')));
    @endif

})();
</script>
