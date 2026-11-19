{{-- SIMASTA | Lonceng notifikasi Mahasiswa --}}
@php
    $penggunaNotif = auth()->user();
    $jumlahBelumDibaca = $penggunaNotif ? $penggunaNotif->unreadNotifications()->count() : 0;
    $daftarNotif = $penggunaNotif
        ? $penggunaNotif->notifications()->latest('created_at')->limit(8)->get()
        : collect();
@endphp

<style>
.sim-notif{position:relative;display:inline-flex;flex-shrink:0;font-family:inherit;color:#243b53}
.sim-notif,.sim-notif *{box-sizing:border-box}
.sim-notif-trigger{position:relative;display:flex;align-items:center;justify-content:center;width:44px;height:44px;border:1px solid #e5edf5;border-radius:50%;background:#f1f5f9;color:#1d5fa8;font-size:19px;cursor:pointer;list-style:none;transition:.2s ease}
.sim-notif-trigger::-webkit-details-marker{display:none}
.sim-notif-trigger:hover,.sim-notif[open] .sim-notif-trigger{background:#e8f2ff;border-color:#c8def5;color:#17518e}
.sim-notif-trigger:focus-visible{outline:3px solid #93c5fd;outline-offset:3px}
.sim-notif-count{position:absolute;top:-5px;right:-6px;min-width:19px;height:19px;display:flex;align-items:center;justify-content:center;padding:0 5px;border:2px solid white;border-radius:999px;background:#ef4444;color:#fff;font-size:10px;font-weight:800;line-height:1}
.sim-notif-panel{position:absolute;top:calc(100% + 15px);right:0;z-index:1500;width:min(390px,calc(100vw - 30px));background:#fff;border:1px solid #e2e8f0;border-radius:16px;box-shadow:0 18px 55px rgba(15,23,42,.17);overflow:hidden}
.sim-notif-panel::before{content:"";display:block;height:4px;background:linear-gradient(90deg,#1d5fa8,#2fa266,#f5a65b)}
.sim-notif-header{padding:18px 19px 15px;border-bottom:1px solid #edf2f7}
.sim-notif-header-top{display:flex;align-items:center;justify-content:space-between;gap:12px}
.sim-notif-title{font-size:16px;font-weight:800;color:#163f71}
.sim-notif-subtitle{margin-top:5px;color:#8494a8;font-size:11px;line-height:1.5}
.sim-notif-unread{display:inline-flex;align-items:center;padding:6px 9px;border-radius:20px;background:#e8f2ff;color:#1d5fa8;font-size:10px;font-weight:750;white-space:nowrap}
.sim-notif-list{max-height:370px;overflow-y:auto;overscroll-behavior:contain}
.sim-notif-form{margin:0}
.sim-notif-item{display:flex;align-items:flex-start;gap:11px;width:100%;padding:15px 17px;border:0;border-bottom:1px solid #f0f3f8;background:#fff;text-decoration:none;color:inherit;text-align:left;font-family:inherit;cursor:pointer;transition:background .2s ease}
.sim-notif-item:hover{background:#f8fbff}.sim-notif-item.unread{background:#f0f7ff}
.sim-notif-item:focus-visible,.sim-notif-readall:focus-visible{outline:2px solid #2563eb;outline-offset:-2px}
.sim-notif-icon{display:grid;place-items:center;width:37px;height:37px;flex-shrink:0;border-radius:11px;background:#e5f0ff;color:#2165ab;font-size:17px}
.sim-notif-icon.success{background:#e5f8ed;color:#16855a}.sim-notif-icon.danger{background:#fff0f1;color:#c03d48}.sim-notif-icon.warning{background:#fff3e1;color:#b76b18}
.sim-notif-body{display:block;min-width:0;flex:1}
.sim-notif-item-title{display:block;color:#243f60;font-size:12px;font-weight:750;line-height:1.5}
.sim-notif-message{display:block;margin:5px 0;color:#697e95;font-size:11px;line-height:1.65;overflow-wrap:anywhere}
.sim-notif-time{display:block;color:#92a0af;font-size:10px}
.sim-notif-dot{width:7px;height:7px;margin-top:6px;flex-shrink:0;border-radius:50%;background:#2563eb}
.sim-notif-empty{padding:34px 24px;text-align:center}
.sim-notif-empty-icon{display:grid;place-items:center;width:55px;height:55px;margin:0 auto 12px;border-radius:16px;background:#edf5ff;color:#377bb7;font-size:25px}
.sim-notif-empty strong{display:block;margin-bottom:6px;font-size:13px;color:#214367}
.sim-notif-empty p{color:#8a99a9;font-size:11px;line-height:1.7}
.sim-notif-footer{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:9px;padding:13px 15px;border-top:1px solid #edf2f7;background:#fbfdff}
.sim-notif-footer a,.sim-notif-readall{color:#1d5fa8;text-decoration:none;font-size:11px;font-weight:750}
.sim-notif-footer a:hover,.sim-notif-readall:hover{text-decoration:underline}
.sim-notif-readall{border:0;background:transparent;font-family:inherit;cursor:pointer}
@media(max-width:600px){.sim-notif-panel{position:fixed;top:70px;right:12px;left:12px;width:auto;max-height:calc(100vh - 90px)}.sim-notif-list{max-height:calc(100vh - 260px)}}
</style>

<details class="sim-notif" id="simastaNotification">
    <summary class="sim-notif-trigger" aria-label="Buka notifikasi mahasiswa" title="Notifikasi SIMASTA">
        <i class="bi bi-bell" aria-hidden="true"></i>
        @if($jumlahBelumDibaca > 0)
            <span class="sim-notif-count">{{ $jumlahBelumDibaca > 99 ? '99+' : $jumlahBelumDibaca }}</span>
        @endif
    </summary>

    <div class="sim-notif-panel">
        <div class="sim-notif-header">
            <div class="sim-notif-header-top">
                <span class="sim-notif-title">Notifikasi SIMASTA</span>
                <span class="sim-notif-unread">{{ $jumlahBelumDibaca }} belum dibaca</span>
            </div>
            <p class="sim-notif-subtitle">Informasi terbaru mengenai pengajuan dan kegiatan magangmu.</p>
        </div>

        <div class="sim-notif-list">
            @forelse($daftarNotif as $notif)
                @php
                    $isiNotif = is_array($notif->data) ? $notif->data : [];
                    $judulNotif = $isiNotif['title'] ?? $isiNotif['judul'] ?? 'Informasi SIMASTA';
                    $pesanNotif = $isiNotif['message'] ?? $isiNotif['pesan'] ?? 'Ada pembaruan informasi magang.';
                    $jenisNotif = strtolower((string)($isiNotif['type'] ?? $isiNotif['jenis'] ?? 'info'));
                    $kelasIkon = match ($jenisNotif) {
                        'success', 'diterima' => 'success',
                        'danger', 'ditolak' => 'danger',
                        'warning' => 'warning',
                        default => '',
                    };
                    $ikonNotif = match ($kelasIkon) {
                        'success' => 'bi-check-circle',
                        'danger' => 'bi-x-circle',
                        'warning' => 'bi-exclamation-circle',
                        default => 'bi-file-earmark-text',
                    };
                    $sudahDibaca = $notif->read_at !== null;
                @endphp

                <form method="POST" action="{{ route('mahasiswa.notifikasi.buka', $notif->id) }}" class="sim-notif-form">
                    @csrf
                    <button type="submit" class="sim-notif-item {{ $sudahDibaca ? '' : 'unread' }}" title="Buka pengumuman">
                        <span class="sim-notif-icon {{ $kelasIkon }}"><i class="bi {{ $ikonNotif }}" aria-hidden="true"></i></span>
                        <span class="sim-notif-body">
                            <strong class="sim-notif-item-title">{{ $judulNotif }}</strong>
                            <span class="sim-notif-message">{{ $pesanNotif }}</span>
                            <span class="sim-notif-time">{{ $notif->created_at?->diffForHumans() }}</span>
                        </span>
                        @unless($sudahDibaca)
                            <span class="sim-notif-dot" title="Belum dibaca"></span>
                        @endunless
                    </button>
                </form>
            @empty
                <div class="sim-notif-empty">
                    <span class="sim-notif-empty-icon"><i class="bi bi-bell-slash" aria-hidden="true"></i></span>
                    <strong>Belum ada notifikasi</strong>
                    <p>Pengumuman mengenai pengajuan magang akan muncul di sini setelah dikirim oleh sistem SIMASTA.</p>
                </div>
            @endforelse
        </div>

        <div class="sim-notif-footer">
            <a href="{{ route('mahasiswa.dokumen') }}">Lihat Status Pengajuan Magang</a>
            @if($jumlahBelumDibaca > 0)
                <form method="POST" action="{{ route('mahasiswa.notifikasi.baca-semua') }}" style="margin:0">
                    @csrf
                    <button type="submit" class="sim-notif-readall"><i class="bi bi-check2-all" aria-hidden="true"></i> Tandai semua dibaca</button>
                </form>
            @endif
        </div>
    </div>
</details>
