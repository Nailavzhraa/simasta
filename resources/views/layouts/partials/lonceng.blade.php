@php
    // SEMENTARA: data contoh (sama dengan halaman Pengumuman).
    // Nanti ganti dengan data dari database, mis.:
    // $daftarPengumuman = \App\Models\Pengumuman::latest()->take(5)->get()->map(fn ($p) => [...])->all();
    $daftarPengumuman = [
        ['id' => 3, 'kategori' => 'Seleksi',  'penting' => true,  'tanggal' => '29 Okt 2026',
         'judul' => 'Jadwal pemeriksaan berkas',
         'isi'   => 'Ini hanya contoh tampilan. Isi pengumuman asli dibuat admin.'],
        ['id' => 2, 'kategori' => 'Umum',     'penting' => false, 'tanggal' => '24 Okt 2026',
         'judul' => 'Pengumuman umum',
         'isi'   => 'Contohteks pengumuman umum untuk seluruh peserta dan calon peserta magang.'],
        ['id' => 1, 'kategori' => 'Kegiatan', 'penting' => false, 'tanggal' => '18 Okt 2026',
         'judul' => 'Informasi kegiatan',
         'isi'   => 'Contoh informasi kegiatan magang yang diumumkan oleh admin.'],
    ];
@endphp

<style>
    .bell{position:relative; margin-right:6px}
    .bell-btn{position:relative; display:grid; place-items:center; width:42px; height:42px; border:0;
        border-radius:50%; background:#eef2f7; color:#1d5fa8; font-size:19px; cursor:pointer; transition:.2s}
    .bell-btn:hover, .bell-btn[aria-expanded="true"]{background:#f5a65b; color:#fff}
    .bell-badge{position:absolute; top:-3px; right:-3px; min-width:19px; height:19px; padding:0 5px;
        border-radius:20px; background:#dc2626; color:#fff; font-size:10.5px; font-weight:700;
        line-height:19px; text-align:center; border:2px solid #fff}
    .bell [hidden]{display:none !important}

    .bell-menu{position:absolute; top:52px; right:-70px; width:min(380px, 92vw); z-index:300;
        background:#fff; border:1px solid #e5eaf1; border-radius:16px; overflow:hidden;
        box-shadow:0 18px 40px rgba(15,23,42,.16)}
    .bell-head{display:flex; align-items:center; justify-content:space-between; padding:14px 18px;
        border-bottom:1px solid #edf2f7; background:linear-gradient(120deg,#d9f7eb,#e8f1ff)}
    .bell-head strong{font-size:15px; color:#183153}
    .bell-head span{font-size:12px; color:#64748b}

    .bell-list{max-height:360px; overflow-y:auto}
    .bell-item{display:flex; gap:12px; padding:14px 18px; text-decoration:none; color:inherit;
        border-bottom:1px solid #f1f5f9; transition:.15s}
    .bell-item:hover{background:#f8fafc}
    .bell-item.baru{background:#fff8f0}
    .bell-dot{flex:0 0 9px; height:9px; margin-top:6px; border-radius:50%; background:#e2e8f0}
    .bell-item.baru .bell-dot{background:#f5a65b}
    .bell-meta{display:flex; align-items:center; gap:6px; margin-bottom:4px; font-size:11.5px; color:#94a3b8}
    .bell-tag{padding:2px 8px; border-radius:20px; background:#eef2f7; color:#475569; font-size:10.5px; font-weight:700}
    .bell-tag.penting{background:#ffedd5; color:#c2410c}
    .bell-item b{display:block; font-size:13.5px; color:#1e293b; margin-bottom:3px}
    .bell-item p{margin:0; font-size:12.5px; color:#64748b; line-height:1.5;
        display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden}

    .bell-all{display:block; padding:13px; text-align:center; font-size:13px; font-weight:700;
        color:#1d5fa8; text-decoration:none; background:#f8fafc}
    .bell-all:hover{color:#ee8a3a}

    @media (max-width:600px){ .bell-menu{right:-40px} }
</style>

<div class="bell" id="bell">
    <button type="button" class="bell-btn" id="bellBtn" aria-label="Pengumuman" aria-expanded="false" title="Pengumuman">
        <i class="bi bi-bell"></i>
        <span class="bell-badge" id="bellBadge" hidden>0</span>
    </button>

    <div class="bell-menu" id="bellMenu" hidden>
        <div class="bell-head">
            <strong>Pengumuman</strong>
            <span>{{ count($daftarPengumuman) }} terbaru</span>
        </div>

        <div class="bell-list">
            @forelse($daftarPengumuman as $p)
                <a href="/mahasiswa/pengumuman" class="bell-item" data-id="{{ $p['id'] }}">
                    <span class="bell-dot"></span>
                    <div>
                        <div class="bell-meta">
                            <span class="bell-tag">{{ $p['kategori'] }}</span>
                            @if($p['penting'])<span class="bell-tag penting">Penting</span>@endif
                            {{ $p['tanggal'] }}
                        </div>
                        <b>{{ $p['judul'] }}</b>
                        <p>{{ $p['isi'] }}</p>
                    </div>
                </a>
            @empty
                <div style="padding:30px; text-align:center; color:#94a3b8; font-size:13px">Belum ada pengumuman.</div>
            @endforelse
        </div>

        <a href="/mahasiswa/pengumuman" class="bell-all">Lihat semua pengumuman →</a>
    </div>
</div>

<script>
(function () {
    var KEY   = 'simasta_pengumuman_dibaca';
    var bell  = document.getElementById('bell'),
        btn   = document.getElementById('bellBtn'),
        menu  = document.getElementById('bellMenu'),
        badge = document.getElementById('bellBadge'),
        items = document.querySelectorAll('.bell-item');

    var terakhir = 0;
    try { terakhir = parseInt(localStorage.getItem(KEY) || '0', 10) || 0; } catch (e) {}

    var maxId = 0, belumDibaca = 0;
    items.forEach(function (it) {
        var id = parseInt(it.dataset.id, 10);
        if (id > maxId) maxId = id;
        if (id > terakhir) { belumDibaca++; it.classList.add('baru'); }
    });

    function gambarBadge() {
        badge.hidden = belumDibaca === 0;
        badge.textContent = belumDibaca > 9 ? '9+' : belumDibaca;
    }
    gambarBadge();

    function tutup() { menu.hidden = true; btn.setAttribute('aria-expanded', 'false'); }

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var buka = menu.hidden;
        menu.hidden = !buka;
        btn.setAttribute('aria-expanded', buka ? 'true' : 'false');
        if (buka) {                       // saat dibuka, tandai sudah dibaca
            try { localStorage.setItem(KEY, String(maxId)); } catch (e) {}
            belumDibaca = 0;
            gambarBadge();
        }
    });

    document.addEventListener('click', function (e) { if (!bell.contains(e.target)) tutup(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') tutup(); });
})();
</script>