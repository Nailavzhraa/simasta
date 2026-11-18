<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SIMASTA - Sistem Informasi Magang Statistik Kota Batu</title>

<style>

*{margin:0;padding:0;box-sizing:border-box}

html{scroll-behavior:smooth}

:root{

  --neon:#34D399;--neon-dark:#10B981;--neon-soft:#A7F3D0;

  --orange:#FB923C;--orange-dark:#F97316;--orange-soft:#FED7AA;

  --blue:#60A5FA;--blue-dark:#3B82F6;--blue-soft:#BFDBFE;

  --navy:#1E293B;--navy-soft:#334155;--footer-bg:#1E3A5F;

}

body{

  font-family:Arial,sans-serif;color:#1f2937;

  background:

    radial-gradient(circle at 12% 15%,rgba(52,211,153,.18),transparent 35%),

    radial-gradient(circle at 88% 20%,rgba(96,165,250,.20),transparent 38%),

    radial-gradient(circle at 55% 75%,rgba(251,146,60,.14),transparent 35%),

    linear-gradient(135deg,#e7fff7 0%,#f5fbff 45%,#fffaf5 70%,#eaf3ff 100%);

  background-attachment:fixed;background-size:cover;

}

a:focus-visible,button:focus-visible{outline:3px solid var(--blue-dark);outline-offset:3px}

/* NAVBAR */

.navbar{position:fixed;top:0;left:0;width:100%;z-index:1000;padding:18px 7%;display:flex;align-items:center;justify-content:space-between;transition:background .3s,padding .3s,box-shadow .3s}

.navbar.scrolled{background:rgba(255,255,255,.96);backdrop-filter:blur(8px);box-shadow:0 4px 15px rgba(0,0,0,.08);padding-top:12px;padding-bottom:12px}

.logo{display:flex;align-items:center;gap:10px;text-decoration:none}

.logo img{width:55px;height:45px;object-fit:contain}

.logo-text{font-size:22px;font-weight:bold;color:var(--navy);line-height:1.1}

.logo-sub{font-size:10px;letter-spacing:1px;color:#64748b}

.nav-menu{display:flex;align-items:center;gap:30px;list-style:none}

.nav-menu a{color:var(--navy-soft);text-decoration:none;font-size:14px;font-weight:600;transition:.2s}

.nav-menu a:hover{color:var(--neon-dark)}

.nav-menu a.active{color:var(--blue-dark);font-weight:700}

.menu-toggle{display:none;width:40px;height:40px;border:none;border-radius:10px;background:white;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 4px 12px rgba(0,0,0,.08)}

.menu-toggle span,.menu-toggle span::before,.menu-toggle span::after{content:"";display:block;width:18px;height:2px;background:var(--navy);position:relative;transition:.2s}

.menu-toggle span::before{top:-6px}.menu-toggle span::after{top:4px}

/* HERO */

.hero{min-height:100vh;display:flex;align-items:center;justify-content:flex-start;text-align:left;padding:130px 7% 80px;position:relative;overflow:hidden;

  background:

    radial-gradient(circle at 12% 15%,rgba(52,211,153,.30),transparent 45%),

    radial-gradient(circle at 88% 20%,rgba(96,165,250,.32),transparent 50%),

    radial-gradient(circle at 55% 100%,rgba(251,146,60,.28),transparent 45%),#F8FAFC}

.hero::before{content:"";position:absolute;width:560px;height:560px;border-radius:50%;background:rgba(96,165,250,.10);right:-160px;top:-120px}

.hero-content{position:relative;z-index:2;max-width:680px}

.hero-badge{display:inline-flex;align-items:center;gap:8px;background:white;border:1px solid var(--neon-soft);color:var(--neon-dark);padding:8px 16px;border-radius:30px;font-size:11px;font-weight:700;letter-spacing:.8px;margin-bottom:22px;box-shadow:0 4px 12px rgba(16,185,129,.12)}

.hero-badge::before{content:"";flex:none;width:6px;height:6px;border-radius:50%;background:var(--neon)}

.hero h1{color:var(--navy);font-size:46px;line-height:1.15;margin-bottom:20px}

.hero h1 span{background:linear-gradient(120deg,var(--neon-dark),var(--blue-dark),var(--orange-dark));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}

.hero p{color:#475569;font-size:17px;line-height:1.7;margin:0 0 32px;max-width:600px}

.hero-buttons{display:flex;gap:15px;flex-wrap:wrap;justify-content:flex-start}

.btn-primary{display:inline-block;background:linear-gradient(120deg,var(--orange),var(--orange-dark));color:white;padding:14px 26px;border-radius:30px;text-decoration:none;font-weight:bold;box-shadow:0 10px 25px rgba(255,122,51,.25);transition:.2s}

.btn-primary:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(255,122,51,.32)}

/* SECTION */

.section{padding:80px 7%;scroll-margin-top:90px}

.section-title{text-align:center;margin-bottom:50px}

.section-title p.eyebrow{color:var(--blue);font-size:12px;font-weight:700;letter-spacing:1.5px;margin-bottom:10px}

.section-title h2{font-size:30px;color:var(--navy-soft);margin-bottom:12px}

.section-title p{color:#64748b;max-width:650px;margin:auto;line-height:1.6}

/* KEUNGGULAN */

.feature-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:25px}

.feature-card{padding:35px 25px;background:#f8fafc;border:1px solid #e5e7eb;border-top:3px solid var(--card-accent,var(--blue));border-radius:12px;text-align:center;transition:.3s}

.feature-card:hover{transform:translateY(-5px);box-shadow:0 10px 25px rgba(0,0,0,.08)}

.feature-card.neon{--card-accent:var(--neon)}.feature-card.blue{--card-accent:var(--blue)}.feature-card.orange{--card-accent:var(--orange)}

.feature-icon{width:55px;height:55px;margin:auto auto 20px;display:flex;align-items:center;justify-content:center;border-radius:12px;background:color-mix(in srgb,var(--card-accent,var(--blue)) 15%,white);color:var(--card-accent,var(--blue));font-size:22px;font-weight:bold}

.feature-card h3{margin-bottom:12px;color:var(--navy-soft)}

.feature-card p{color:#64748b;line-height:1.6;font-size:14px}

/* ALUR (STEPPER) */

.alur-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:10px;text-align:center;position:relative;max-width:1100px;margin:auto}

.alur-grid::before{content:"";position:absolute;top:30px;left:calc(100% / 14);right:calc(100% / 14);height:2px;background:linear-gradient(90deg,var(--neon-dark),var(--blue),var(--orange));opacity:.35}

.alur-item{position:relative;cursor:pointer;padding:0 4px;outline:none}

.alur-icon{width:60px;height:60px;margin:0 auto 16px;border-radius:50%;background:white;border:2px solid #e2e8f0;color:var(--step-color);display:flex;align-items:center;justify-content:center;position:relative;z-index:2;box-shadow:0 6px 16px rgba(30,41,59,.08);transition:.25s}

.alur-icon svg{width:24px;height:24px}

.alur-item:nth-child(1),.alur-item:nth-child(2){--step-color:var(--neon-dark)}

.alur-item:nth-child(3),.alur-item:nth-child(4),.alur-item:nth-child(5){--step-color:var(--blue-dark)}

.alur-item:nth-child(6),.alur-item:nth-child(7){--step-color:var(--orange-dark)}

.alur-item:hover .alur-icon,.alur-item:focus-visible .alur-icon{border-color:var(--step-color);transform:translateY(-3px)}

.alur-item.is-active .alur-icon{background:var(--step-color);border-color:var(--step-color);color:white;box-shadow:0 0 0 6px color-mix(in srgb,var(--step-color) 20%,transparent),0 10px 22px color-mix(in srgb,var(--step-color) 40%,transparent)}

.alur-item h4{color:var(--navy);margin-bottom:6px;font-size:15px}

.alur-item p{font-size:12px;color:#64748b;line-height:1.5}

/* KARYA MAGANG — satu proyek per tampilan */
.karya-section .section-title{margin-bottom:32px}
.karya-section .section-title h2{font-size:clamp(28px,3vw,38px);color:var(--navy)}
.karya-slider{max-width:1120px;margin:0 auto}
.karya-grid{display:grid;overflow:hidden;border-radius:24px;background:#102d43;box-shadow:0 24px 60px #1e293b20;touch-action:pan-y;isolation:isolate}
.karya-card{--accent:#93c5fd;position:relative;grid-area:1/1;min-width:0;min-height:550px;display:flex;flex-direction:column;justify-content:space-between;padding:36px 44px;color:white;overflow:hidden;visibility:hidden;opacity:0;transform:translateX(5%);transition:opacity .5s ease,transform .6s ease,visibility .6s}
.karya-card[hidden]{display:none!important}
.karya-card.is-active{visibility:visible;opacity:1;transform:translateX(0);z-index:1}
.karya-card.green{--accent:#6ee7b7}.karya-card.orange{--accent:#fdba74}
.karya-background{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:top;z-index:-2;transform:scale(1);transition:transform 1.2s ease}
.karya-card.is-active .karya-background{transform:scale(1.035)}
.karya-card::before{content:"";position:absolute;inset:0;z-index:-1;background:linear-gradient(180deg,rgba(8,22,40,.28),rgba(8,22,40,.05) 24%,rgba(8,22,40,.78) 67%,#081628 100%)}
.karya-topline{display:flex;align-items:center;justify-content:space-between;gap:16px}
.karya-label,.karya-go{background:radial-gradient(circle at 12% 15%,rgba(52,211,153,.18),transparent 55%),radial-gradient(circle at 88% 20%,rgba(96,165,250,.20),transparent 58%),radial-gradient(circle at 55% 90%,rgba(251,146,60,.14),transparent 55%),linear-gradient(135deg,#e7fff7 0%,#f5fbff 45%,#fffaf5 70%,#eaf3ff 100%);color:#000}
.karya-label{display:inline-flex;align-items:center;gap:8px;border:1px solid #ffffffb3;box-shadow:0 4px 14px rgba(30,41,59,.10);padding:9px 14px;border-radius:999px;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase}
.karya-label::before{content:"";width:6px;height:6px;border-radius:50%;background:#000}
.karya-number{font-size:12px;font-weight:700;letter-spacing:2px;padding:9px 12px;background:#0f253acc;border-radius:999px}
.karya-content{display:flex;align-items:flex-end;justify-content:space-between;gap:32px;margin-top:160px}
.karya-detail{max-width:690px;min-width:0}
.karya-caption{font-size:10px;letter-spacing:2.4px;text-transform:uppercase;color:var(--accent);margin-bottom:12px;font-weight:700}
.karya-detail h3{font-size:clamp(34px,4.7vw,60px);line-height:1.08;letter-spacing:-1.5px;margin-bottom:14px;text-wrap:balance}
.karya-detail p{font-size:15px;line-height:1.65;color:#e2e8f0;max-width:560px;margin-bottom:15px}
.karya-host{font-size:12px;color:#cbd5e1;overflow-wrap:anywhere}
.karya-go{flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;gap:12px;padding:15px 20px;border-radius:12px;text-decoration:none;font-size:13px;font-weight:700;box-shadow:0 8px 22px rgba(30,41,59,.14);transition:filter .2s,box-shadow .2s,transform .2s}
.karya-go:hover{filter:brightness(1.03);box-shadow:0 12px 28px rgba(30,41,59,.20);transform:translateY(-3px)}
.karya-go svg{width:17px;height:17px}
.karya-toolbar{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-top:24px}
.karya-count{font-size:12px;color:#475569}
.karya-count strong{display:block;font-size:14px;color:var(--navy);margin-bottom:5px}
.karya-controls{display:flex;align-items:center;gap:14px}
.karya-nav{width:44px;height:44px;display:grid;place-items:center;border:1px solid #d7e2ef;border-radius:50%;background:#fff;color:#334155;cursor:pointer;transition:.2s;flex-shrink:0}
.karya-nav:hover{background:var(--navy);border-color:var(--navy);color:white;transform:scale(1.06)}
.karya-nav svg{width:18px;height:18px}
.karya-dots{display:flex;align-items:center;gap:1px}
.karya-dot{position:relative;width:24px;height:32px;border:0;background:transparent;cursor:pointer;padding:0}
.karya-dot::after{content:"";display:block;width:6px;height:6px;margin:auto;border-radius:99px;background:#bdcbd8;transition:width .3s,background .3s}
.karya-dot[aria-current="true"]::after{width:20px;background:var(--neon-dark)}
.karya-note{margin:24px auto 0;max-width:680px;text-align:center;font-size:12px;color:#64748b;line-height:1.7}
@media(max-width:700px){
  .karya-grid{border-radius:18px}
  .karya-card{min-height:510px;padding:24px}
  .karya-content{flex-direction:column;align-items:flex-start;gap:22px;margin-top:150px}
  .karya-detail h3{font-size:38px;letter-spacing:-1px}
  .karya-detail p{font-size:14px}
  .karya-background{object-position:top center}
  .karya-card::before{background:linear-gradient(180deg,#08162830 0%,#08162820 18%,#081628dd 55%,#081628 100%)}
  .karya-toolbar{flex-direction:column;gap:12px}
  .karya-count{text-align:center}
  .karya-controls{width:100%;justify-content:space-between;gap:4px;max-width:390px}
  .karya-dot{width:20px}
  .karya-nav{width:38px;height:38px}
}

/* TENTANG */

.about{display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center}

.about h2{font-size:32px;color:var(--navy-soft);margin-bottom:20px}

.about p{color:#64748b;line-height:1.8;margin-bottom:15px}

.about-box{background:linear-gradient(160deg,#eafff5,#eff6ff);padding:40px;border-radius:15px;border:1px solid #dbeafe}

.about-box h3{color:var(--blue);margin-bottom:15px}

/* CTA */

.cta{margin:0 7% 80px;padding:60px 40px;border-radius:18px;text-align:center;position:relative;overflow:hidden;

  background:radial-gradient(circle at 20% 20%,rgba(52,211,153,.35),transparent 50%),radial-gradient(circle at 80% 80%,rgba(251,146,60,.30),transparent 50%),linear-gradient(120deg,var(--blue-soft),#EFF6FF)}

.cta h2{color:var(--navy);font-size:30px;margin-bottom:15px;position:relative}

.cta p{color:var(--navy-soft);margin-bottom:25px;position:relative}

.cta .btn-primary{position:relative}

/* FOOTER */

footer{background:var(--footer-bg);color:white;padding:60px 7% 25px;scroll-margin-top:0}

.footer-grid{display:grid;grid-template-columns:1.4fr 1.4fr 1fr;gap:50px;margin-bottom:40px}

.footer-brand{display:flex;align-items:center;gap:12px;margin-bottom:16px}

.footer-logo{display:flex;align-items:center}

.footer-logo img{width:46px;height:38px;object-fit:contain;display:block}

.footer-brand b{display:block;font-size:20px;line-height:1.1}

.footer-brand small{font-size:10px;letter-spacing:1px;color:#93c5fd}

footer h3{margin-bottom:18px;font-size:16px}

footer p{color:#cbd5e1;line-height:1.7;font-size:14px}

.contact-list{list-style:none}

.contact-list li{display:flex;gap:12px;margin-bottom:14px;color:#cbd5e1;font-size:14px;line-height:1.55}

.contact-list svg{width:18px;height:18px;flex:none;margin-top:2px;color:var(--neon)}

footer a{color:#cbd5e1;text-decoration:none;transition:.2s}

footer a:hover{color:var(--neon)}

.social-row{display:flex;flex-wrap:wrap;gap:10px}

.social-row a{width:42px;height:42px;border-radius:12px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:#fff;transition:.2s}

.social-row a:hover{background:var(--neon-dark);color:#fff;transform:translateY(-3px)}

.social-row svg{width:20px;height:20px}

.social-note{margin-top:14px;font-size:13px}

.footer-bottom{border-top:1px solid rgba(255,255,255,.12);padding-top:20px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;color:#94a3b8;font-size:13px}

.footer-bottom a{color:#94a3b8}

.footer-legal{display:flex;align-items:center;justify-content:center;gap:24px;flex-wrap:wrap}
.footer-legal button{border:0;background:none;color:#cbd5e1;font:inherit;cursor:pointer;padding:6px 0;transition:color .2s}
.footer-legal button:hover{color:var(--neon)}
.legal-dialog{width:min(720px,calc(100% - 32px));max-height:85dvh;margin:auto;padding:0;border:1px solid #e2e8f0;border-radius:22px;background:#fff;color:#111827;box-shadow:0 28px 90px #0f172a40;overflow:auto;overscroll-behavior:contain}
.legal-dialog::backdrop{background:#0f172a99;backdrop-filter:blur(5px)}
.legal-header{position:sticky;top:0;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;gap:20px;padding:28px 32px;border-bottom:1px solid #e2e8f0;background:linear-gradient(120deg,#e7fff7,#f5fbff 55%,#fff4e8)}
.legal-eyebrow{display:block;font-size:10px;letter-spacing:1.8px;font-weight:700;color:#334155;margin-bottom:10px}
.legal-header h2{font-size:clamp(23px,4vw,30px);line-height:1.2}
.legal-close{flex:none;width:36px;height:36px;border:1px solid #dbe3ec;border-radius:50%;background:#fff;color:#111827;font-size:25px;line-height:1;cursor:pointer}
.legal-content{padding:28px 32px;font-size:14px;line-height:1.8;color:#475569}
.legal-content h3{font-size:16px;color:#111827;margin:22px 0 7px}
.legal-content h3:first-child{margin-top:0}
.legal-content a{color:#0369a1;text-underline-offset:3px}
.legal-actions{padding:0 32px 28px;display:flex;justify-content:flex-end}
.legal-done{border:1px solid #dce6ec;border-radius:10px;padding:11px 24px;background:linear-gradient(120deg,#e7fff7,#eff6ff,#fff4e8);font:inherit;font-weight:700;cursor:pointer;color:#111827}
@media(max-width:600px){.legal-header{padding:22px}.legal-content{padding:22px}.legal-actions{padding:0 22px 22px}}

/* RESPONSIVE */

@media(max-width:900px){

  .menu-toggle{display:flex}

  .nav-menu{display:none;position:absolute;top:78px;right:5%;width:220px;padding:15px;background:white;border-radius:14px;box-shadow:0 12px 35px rgba(0,0,0,.12);flex-direction:column;align-items:stretch;gap:8px}

  .nav-menu.active{display:flex}

  .nav-menu li{width:100%}

  .nav-menu a{display:block;width:100%;padding:12px 14px;border-radius:8px}

  .nav-menu a:hover{background:#f1f5f9}

  .hero{min-height:auto;padding:120px 7% 70px}

  .hero h1{font-size:36px}

  .feature-grid{grid-template-columns:1fr}

  /* alur jadi vertikal */

  .alur-grid{grid-template-columns:1fr;gap:26px;text-align:left;max-width:420px}

  .alur-grid::before{top:30px;bottom:30px;left:30px;right:auto;width:2px;height:auto}

  .alur-item{display:grid;grid-template-columns:60px 1fr;column-gap:18px;align-items:center}

  .alur-icon{margin:0;grid-row:1 / span 2}

  .alur-item h4{margin:0 0 4px;align-self:end}

  .alur-item p{align-self:start}

  .about{grid-template-columns:1fr}

  .footer-grid{grid-template-columns:1fr}

}

@media(max-width:600px){

  .hero h1{font-size:30px}

  .hero-buttons .btn-primary{width:100%;text-align:center}

  .section{padding:60px 5%}

  .cta{margin-left:5%;margin-right:5%;padding:45px 24px}

  .footer-bottom{flex-direction:column;text-align:center}

}

@media(prefers-reduced-motion:reduce){

  *{transition:none!important;scroll-behavior:auto!important}

  .karya-background{transition:none!important;transform:none!important}

}

</style>

</head>

<body>

@php
    // Daftar 10 proyek karya magang; ubah data di sini bila diperlukan.
    $karya = [
        ['preview' => 'pengaduan.jpg', 'nama' => 'Pengaduan', 'url' => 'https://pengaduan.bpskotabatu.com', 'host' => 'pengaduan.bpskotabatu.com', 'warna' => 'orange', 'label' => 'Aplikasi Web', 'desc' => 'Portal pengaduan BPS Kota Batu.', 'ikon' => '<path d="M8 10h8M8 14h5"/><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5 9 9 0 0 1-4-.9L3 21l1.9-5.5a9 9 0 0 1-.9-4A8.5 8.5 0 0 1 12.5 3 8.5 8.5 0 0 1 21 11.5z"/>'],
        ['preview' => 'pdrb.jpg', 'nama' => 'PDRB', 'url' => 'https://pdrb.bpskotabatu.com', 'host' => 'pdrb.bpskotabatu.com', 'warna' => 'blue', 'label' => 'Dashboard', 'desc' => 'Visualisasi Produk Domestik Regional Bruto Kota Batu.', 'ikon' => '<path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>'],
        ['preview' => 'mitra.jpg', 'nama' => 'Mitra Statistik', 'url' => 'https://mitra.bpskotabatu.com', 'host' => 'mitra.bpskotabatu.com', 'warna' => 'blue', 'label' => 'Aplikasi Web', 'desc' => 'Pengelolaan data dan kegiatan mitra statistik BPS Kota Batu.', 'ikon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
        ['preview' => 'iph.jpg', 'nama' => 'IPH', 'url' => 'https://iph.bpskotabatu.com', 'host' => 'iph.bpskotabatu.com', 'warna' => 'orange', 'label' => 'Dashboard', 'desc' => 'Pemantauan Indeks Perkembangan Harga secara ringkas.', 'ikon' => '<path d="M22 7l-8.5 8.5-5-5L2 17"/><path d="M16 7h6v6"/>'],
        ['preview' => 'feedback.jpg', 'nama' => 'Feedback', 'url' => 'https://feedback.bpskotabatu.com', 'host' => 'feedback.bpskotabatu.com', 'warna' => 'green', 'label' => 'Website', 'desc' => 'Akses halaman feedback BPS Kota Batu.', 'ikon' => '<path d="M4 5h16v12H8l-4 4z"/><path d="M8 9h8M8 13h5"/>'],
        ['preview' => 'dtsen.jpg', 'nama' => 'DTSEN', 'url' => 'https://dtsen.bpskotabatu.com', 'host' => 'dtsen.bpskotabatu.com', 'warna' => 'green', 'label' => 'Aplikasi Web', 'desc' => 'Data Terpadu Sosial Ekonomi Nasional dalam satu tampilan.', 'ikon' => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/>'],
        ['preview' => 'desa-beji.jpg', 'nama' => 'Desa Beji', 'url' => 'https://desabeji.web.id', 'host' => 'desabeji.web.id', 'warna' => 'green', 'label' => 'Website', 'desc' => 'Website Desa Beji.', 'ikon' => '<path d="m3 10 9-7 9 7"/><path d="M5 9v12h14V9"/><path d="M9 21v-8h6v8"/>'],
        ['preview' => 'presensi.jpg', 'nama' => 'Presensi SIMASTA', 'url' => 'https://presensi-simasta.bpskotabatu.com', 'host' => 'presensi-simasta.bpskotabatu.com', 'warna' => 'green', 'label' => 'Aplikasi Web', 'desc' => 'Presensi harian peserta magang BPS Kota Batu.', 'ikon' => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>'],
        ['preview' => 'predikta.jpg', 'nama' => 'Predikta', 'url' => 'https://predikta.bpskotabatu.com', 'host' => 'predikta.bpskotabatu.com', 'warna' => 'orange', 'label' => 'Website', 'desc' => 'Akses proyek Predikta BPS Kota Batu.', 'ikon' => '<path d="M3 17l6-6 4 4 8-9"/><path d="M16 6h5v5"/>'],
        ['preview' => 'portal.jpg', 'nama' => 'Portal', 'url' => 'https://portal.bpskotabatu.com', 'host' => 'portal.bpskotabatu.com', 'warna' => 'blue', 'label' => 'Website', 'desc' => 'Portal layanan BPS Kota Batu.', 'ikon' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 9v12"/>'],
    ];
@endphp

<!-- NAVBAR -->

<nav class="navbar" id="navbar">

  <a href="/" class="logo">

    <img src="{{ asset('images/logo-bps.png') }}" alt="Logo BPS">

    <span>

      <span class="logo-text" style="display:block;">SIMASTA</span>

      <span class="logo-sub">BADAN PUSAT STATISTIK KOTA BATU</span>

    </span>

  </a>

  <ul class="nav-menu" id="navMenu">

    <li><a href="#beranda">Beranda</a></li>

    <li><a href="#tentang">Tentang</a></li>

    <li><a href="#alur">Alur Magang</a></li>

    <li><a href="#karya">Karya Magang</a></li>

    <li><a href="#kontak">Hubungi kami</a></li>

  </ul>

  <button class="menu-toggle" id="menuToggle" aria-label="Buka menu" aria-expanded="false"><span></span></button>

</nav>

<!-- 1. HERO / BERANDA -->

<section class="hero" id="beranda">

  <div class="hero-content">

    <span class="hero-badge">SISTEM INFORMASI MAGANG RESMI BADAN PUSAT STATISTIK KOTA BATU</span>

    <h1>Magang Statistik yang Terarah, <span>Diakui BPS Kota Batu</span></h1>

    <p>SIMASTA membantu mahasiswa dari berbagai kampus dan universitas mengikuti program magang di BPS Kota Batu secara digital — mulai dari pendaftaran, penempatan bidang, logbook harian, hingga penilaian dan sertifikat resmi, semuanya terpantau dalam satu sistem.</p>

    <div class="hero-buttons">

      <a href="/login" class="btn-primary">Masuk ke Aplikasi →</a>

    </div>

  </div>

</section>

<!-- 2. TENTANG -->

<section class="section" id="tentang">

  <div class="about">

    <div>

      <h2>Tentang SIMASTA</h2>

      <p>SIMASTA (Sistem Magang Statistik Kota Batu) adalah platform resmi Badan Pusat Statistik Kota Batu untuk mengelola seluruh proses magang secara digital, mulai dari pendaftaran hingga penerbitan sertifikat.</p>

      <p>Sistem ini menghubungkan mahasiswa dari berbagai kampus, admin, dan pembimbing lapangan dalam satu platform, sehingga proses yang dulunya manual lewat surat dan WhatsApp kini lebih cepat, terstruktur, dan terdokumentasi rapi.</p>

    </div>

    <div class="about-box">

      <h3>Tujuan SIMASTA</h3>

      <p>Meningkatkan efektivitas pengelolaan kegiatan magang melalui sistem yang terintegrasi, mudah digunakan, dan terorganisir.</p>

    </div>

  </div>

</section>

<!-- KEUNGGULAN -->

<section class="section" id="informasi">

  <div class="section-title">

    <p class="eyebrow">KEUNGGULAN</p>

    <h2>Kemudahan dalam Pengelolaan Magang</h2>

    <p>SIMASTA membantu mengintegrasikan berbagai proses administrasi dan kegiatan magang dalam satu sistem.</p>

  </div>

  <div class="feature-grid">

    <div class="feature-card neon">

      <div class="feature-icon">01</div>

      <h3>Pengajuan Terintegrasi</h3>

      <p>Mahasiswa dapat melakukan pengajuan kegiatan magang melalui sistem secara terstruktur dan mudah.</p>

    </div>

    <div class="feature-card blue">

      <div class="feature-icon">02</div>

      <h3>Monitoring Magang</h3>

      <p>Proses pelaksanaan magang dapat dipantau melalui penempatan, logbook, hingga penilaian.</p>

    </div>

    <div class="feature-card orange">

      <div class="feature-icon">03</div>

      <h3>Dokumen Terpusat</h3>

      <p>Pengelolaan dokumen magang menjadi lebih terorganisir dan mudah diakses oleh pengguna.</p>

    </div>

  </div>

</section>

<!-- 3. ALUR MAGANG -->

<section class="section alur" id="alur">

  <div class="section-title">

    <p class="eyebrow">ALUR</p>

    <h2>Alur Magang SIMASTA</h2>

    <p>Ikuti langkah mudah berikut untuk mengikuti magang di BPS Kota Batu.</p>

  </div>

  <div class="alur-grid" id="alurGrid">

    <div class="alur-item is-active" tabindex="0">

      <div class="alur-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg></div>

      <h4>Registrasi</h4><p>Membuat akun mahasiswa</p>

    </div>

    <div class="alur-item" tabindex="0">

      <div class="alur-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M12 18v-6M9 15l3-3 3 3"/></svg></div>

      <h4>Pengajuan</h4><p>Mengajukan kegiatan magang</p>

    </div>

    <div class="alur-item" tabindex="0">

      <div class="alur-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>

      <h4>Verifikasi</h4><p>Pemeriksaan pengajuan</p>

    </div>

    <div class="alur-item" tabindex="0">

      <div class="alur-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>

      <h4>Penempatan</h4><p>Penentuan tempat magang</p>

    </div>

    <div class="alur-item" tabindex="0">

      <div class="alur-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></div>

      <h4>Pelaksanaan</h4><p>Melaksanakan kegiatan magang</p>

    </div>

    <div class="alur-item" tabindex="0">

      <div class="alur-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.500A2.500 2.500 0 0 1 6.500 17H20"/><path d="M6.500 2H20v20H6.500A2.500 2.500 0 0 1 4 19.500v-15A2.500 2.500 0 0 1 6.500 2z"/></svg></div>

      <h4>Logbook</h4><p>Mencatat kegiatan magang</p>

    </div>

    <div class="alur-item" tabindex="0">

      <div class="alur-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.500 13.500 17 22l-5-3-5 3 1.500-8.500"/></svg></div>

      <h4>Penilaian</h4><p>Penilaian hasil magang</p>

    </div>

  </div>

</section>

<!-- 4. KARYA MAGANG -->
<section class="section karya-section" id="karya">
  <div class="section-title">
    <p class="eyebrow">KARYA MAGANG</p>
    <h2>Hasil Karya Anak Magang</h2>
    <p>Berbagai aplikasi dan visualisasi data yang dikembangkan selama program magang di BPS Kota Batu.</p>
  </div>
  <div class="karya-slider" id="karyaSlider" role="region" aria-roledescription="karusel" aria-label="Hasil karya magang">
    <div class="karya-grid" id="karyaGrid" tabindex="0" aria-label="Geser atau gunakan tombol panah untuk melihat proyek">
      @foreach($karya as $k)
      <article class="karya-card {{ $k['warna'] }} {{ $loop->first ? 'is-active' : '' }}" data-project-card role="group" aria-roledescription="slide" aria-label="{{ $loop->iteration }} dari {{ count($karya) }}: {{ $k['nama'] }}" @if(!$loop->first) hidden @endif>
        <img class="karya-background" src="{{ asset('images/projects/'.$k['preview']) }}" alt="" width="1440" height="960" {{ $loop->first ? 'fetchpriority=high' : 'loading=lazy' }} decoding="async" draggable="false">
        <div class="karya-topline">
          <span class="karya-label">{{ $k['label'] }}</span>
          <span class="karya-number" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ count($karya) }}</span>
        </div>
        <div class="karya-content">
          <div class="karya-detail">
            <div class="karya-caption">Dari ide, menjadi karya nyata</div>
            <h3>{{ $k['nama'] }}</h3>
            <p>{{ $k['desc'] }}</p>
            <div class="karya-host">{{ $k['host'] }}</div>
          </div>
          <a class="karya-go" href="{{ $k['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Jelajahi {{ $k['nama'] }} (tab baru)">
            Jelajahi Proyek
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M7 7h10v10"/></svg>
          </a>
        </div>
      </article>
      @endforeach
    </div>
    <div class="karya-toolbar">
      <p class="karya-count" id="karyaCount" role="status" aria-live="polite" aria-atomic="true"><strong>01 / {{ count($karya) }} proyek</strong>Geser untuk menemukan karya lainnya</p>
      <div class="karya-controls" aria-label="Navigasi proyek">
        <button type="button" class="karya-nav" id="karyaPrev" aria-label="Proyek sebelumnya" aria-controls="karyaGrid"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></button>
        <div class="karya-dots" id="karyaDots" aria-label="Pilih proyek"></div>
        <button type="button" class="karya-nav" id="karyaNext" aria-label="Proyek berikutnya" aria-controls="karyaGrid"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button>
      </div>
    </div>
  </div>
  <p class="karya-note">Beberapa aplikasi memerlukan login atau izin akses dari pengelola masing-masing.</p>
</section>

<!-- CTA -->

<section class="cta">

  <h2>Siap Jadi Bagian dari BPS Kota Batu?</h2>

  <p>Masuk ke SIMASTA untuk mendaftar dan mengelola kegiatan magang kamu.</p>

  <a href="/login" class="btn-primary">Masuk ke SIMASTA →</a>

</section>

<!-- FOOTER + HUBUNGI KAMI -->

<footer id="kontak">

  <div class="footer-grid">

    <div>

      <div class="footer-brand">

        <span class="footer-logo"><img src="{{ asset('images/logo-bps.png') }}" alt="Logo BPS"></span>

        <span><b>SIMASTA</b><small>BPS KOTA BATU</small></span>

      </div>

      <p>Sistem Magang Statistik Kota Batu — platform resmi Badan Pusat Statistik Kota Batu untuk mahasiswa dari berbagai kampus dan universitas.</p>

    </div>

    <div>

      <h3>Kontak Kami</h3>

      <ul class="contact-list">

        <li>

          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>

          <span>Jl. Melati, Songgokerto, Kec. Batu, Kota Batu, Jawa Timur, Indonesia</span>

        </li>

        <li>

          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.920v3a2 2 0 0 1-2.180 2 19.790 19.790 0 0 1-8.630-3.070 19.500 19.500 0 0 1-6-6A19.790 19.790 0 0 1 2.120 4.180 2 2 0 0 1 4.110 2h3a2 2 0 0 1 2 1.720c.130.960.360 1.900.700 2.810a2 2 0 0 1-.450 2.110L8.090 9.910a16 16 0 0 0 6 6l1.270-1.270a2 2 0 0 1 2.110-.450c.910.340 1.850.570 2.810.700A2 2 0 0 1 22 16.920z"/></svg>

          <a href="https://wa.me/6282190003579" target="\_blank" rel="noopener">+62 821-9000-3579</a>

        </li>

        <li>

          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>

          <a href="mailto:bps3579@bps.go.id">bps3579@bps.go.id</a>

        </li>

        <li>

          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.300 15.300 0 0 1 4 10 15.300 15.300 0 0 1-4 10 15.300 15.300 0 0 1-4-10 15.300 15.300 0 0 1 4-10z"/></svg>

          <a href="https://batukota.bps.go.id/id" target="\_blank" rel="noopener">batukota.bps.go.id</a>

        </li>

      </ul>

    </div>

    <div>

      <h3>Sosial Media</h3>

      <div class="social-row">

        <a href="https://www.instagram.com/bpskotabatu" target="\_blank" rel="noopener" aria-label="Instagram BPS Kota Batu" title="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.500" cy="6.500" r="1" fill="currentColor"/></svg></a>

        <a href="https://wa.me/6282190003579" target="\_blank" rel="noopener" aria-label="WhatsApp BPS Kota Batu" title="WhatsApp"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 1-13.400 7.800L3 21l1.300-4.500A9 9 0 1 1 21 12z"/><path d="M9 9c0 3 3 6 6 6l1.500-1.500-2-1-1 .8c-.8-.4-1.700-1.300-2.100-2.100l.8-1-1-2z"/></svg></a>

        <a href="https://www.linkedin.com/company/badan-pusat-statistik-kota-batu/" target="\_blank" rel="noopener" aria-label="LinkedIn BPS Kota Batu" title="LinkedIn"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M4.980 3.500a2.500 2.500 0 1 1 0 5 2.500 2.500 0 0 1 0-5zM3 9.500h4V21H3zM9.500 9.500h3.800v1.600h.1c.5-1 1.800-2 3.800-2 4 0 4.800 2.600 4.800 6V21h-4v-5c0-1.300 0-3-1.800-3s-2.100 1.400-2.100 2.900V21h-4z"/></svg></a>

        <a href="https://www.youtube.com/channel/UC_pcLwhiv5VU5y7WNVIA1pQ" target="\_blank" rel="noopener" aria-label="YouTube BPS Kota Batu" title="YouTube"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M22.500 6.800a2.800 2.800 0 0 0-2-2C18.800 4.300 12 4.300 12 4.300s-6.800 0-8.500.5a2.800 2.800 0 0 0-2 2C1 8.500 1 12 1 12s0 3.500.5 5.200a2.800 2.800 0 0 0 2 2c1.700.5 8.500.5 8.500.5s6.800 0 8.500-.5a2.800 2.800 0 0 0 2-2c.5-1.700.5-5.200.5-5.200s0-3.500-.5-5.200zM9.800 15.100V8.900l5.700 3.100-5.700 3.100z"/></svg></a>

        <a href="https://batukota.bps.go.id/id" target="\_blank" rel="noopener" aria-label="Website resmi BPS Kota Batu" title="Website"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.300 15.300 0 0 1 4 10 15.300 15.300 0 0 1-4 10 15.300 15.300 0 0 1-4-10 15.300 15.300 0 0 1 4-10z"/></svg></a>

      </div>

      <p class="social-note">@bpskotabatu</p>

    </div>

  </div>

  <div class="footer-bottom">

    <span>© 2026 SIMASTA — BPS Kota Batu. All Rights Reserved.</span>

    <nav class="footer-legal" aria-label="Informasi penggunaan layanan">
      <button type="button" data-legal-open="termsDialog" aria-haspopup="dialog" aria-controls="termsDialog">Syarat Ketentuan</button>
      <button type="button" data-legal-open="privacyDialog" aria-haspopup="dialog" aria-controls="privacyDialog">Kebijakan Privasi</button>
    </nav>

  </div>

</footer>

@include('partials.legal-dialogs')

<script>

document.querySelectorAll('[data-legal-open]').forEach(button => {
  button.addEventListener('click', () => {
    const dialog = document.getElementById(button.dataset.legalOpen);
    dialog.showModal();
    document.documentElement.style.overflow = 'hidden';
  });
});
document.querySelectorAll('.legal-dialog').forEach(dialog => {
  dialog.querySelectorAll('[data-legal-close]').forEach(button => {
    button.addEventListener('click', () => dialog.close());
  });
  dialog.addEventListener('click', event => {
    const bounds = dialog.getBoundingClientRect();
    if (event.target === dialog && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) dialog.close();
  });
  dialog.addEventListener('close', () => { document.documentElement.style.overflow = ''; });
});

const navbar = document.getElementById('navbar');

const menuToggle = document.getElementById('menuToggle');

const navMenu = document.getElementById('navMenu');

const links = [...navMenu.querySelectorAll('a[href^="#"]')];

const sections = links.map(a => document.querySelector(a.getAttribute('href')));

menuToggle.addEventListener('click', () => {

  const open = navMenu.classList.toggle('active');

  menuToggle.setAttribute('aria-expanded', open);

});

links.forEach(l => l.addEventListener('click', () => {

  navMenu.classList.remove('active');

  menuToggle.setAttribute('aria-expanded', 'false');

}));

function onScroll() {

  const y = window.scrollY;

  navbar.classList.toggle('scrolled', y > 50);

  const pos = y + 140;

  let current = 0;

  sections.forEach((s, i) => { if (s && s.offsetTop <= pos) current = i; });

  if (window.innerHeight + y >= document.body.scrollHeight - 4) current = sections.length - 1;

  links.forEach((a, i) => a.classList.toggle('active', i === current));

}

window.addEventListener('scroll', onScroll, { passive: true });

onScroll();

// Stepper alur: langkah yang disentuh/di-hover/difokus jadi aktif

const steps = [...document.querySelectorAll('#alurGrid .alur-item')];

function setStep(el) {

  steps.forEach(s => s.classList.toggle('is-active', s === el));

}

steps.forEach(s => {

  s.addEventListener('mouseenter', () => setStep(s));

  s.addEventListener('focus', () => setStep(s));

  s.addEventListener('click', () => setStep(s));

});

// Satu proyek per slide; seluruh navigasi berbagi state yang sama.
const karyaSlider = document.getElementById('karyaSlider');
const karyaGrid = document.getElementById('karyaGrid');
const karyaCards = [...karyaGrid.querySelectorAll('[data-project-card]')];
const karyaDots = document.getElementById('karyaDots');
const karyaCount = document.getElementById('karyaCount');
let karyaPage = 0;
const dots = karyaCards.map((card, index) => {
  const dot = document.createElement('button');
  dot.type = 'button';
  dot.className = 'karya-dot';
  dot.setAttribute('aria-label', `Lihat proyek ${index + 1}: ${card.querySelector('h3').textContent}`);
  dot.setAttribute('aria-controls', 'karyaGrid');
  dot.addEventListener('click', () => showKarya(index));
  karyaDots.append(dot);
  return dot;
});
function showKarya(index) {
  karyaPage = (index + karyaCards.length) % karyaCards.length;
  karyaCards.forEach((card, i) => {
    const active = i === karyaPage;
    card.hidden = false;
    card.classList.toggle('is-active', active);
    card.inert = !active;
    card.setAttribute('aria-hidden', String(!active));
    dots[i].setAttribute('aria-current', String(active));
  });
  const number = document.createElement('strong');
  number.textContent = `${String(karyaPage + 1).padStart(2, '0')} / ${karyaCards.length} proyek · ${karyaCards[karyaPage].querySelector('h3').textContent}`;
  karyaCount.replaceChildren(number, document.createTextNode('Geser untuk menemukan karya lainnya'));
}
document.getElementById('karyaPrev').addEventListener('click', () => showKarya(karyaPage - 1));
document.getElementById('karyaNext').addEventListener('click', () => showKarya(karyaPage + 1));
karyaSlider.addEventListener('keydown', event => {
  if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
    event.preventDefault();
    showKarya(karyaPage + (event.key === 'ArrowRight' ? 1 : -1));
  }
});
let touchStart = null;
karyaGrid.addEventListener('touchstart', event => {
  touchStart = event.touches.length === 1 ? {x:event.touches[0].clientX, y:event.touches[0].clientY} : null;
}, {passive:true});
karyaGrid.addEventListener('touchend', event => {
  if (!touchStart) return;
  const dx = event.changedTouches[0].clientX - touchStart.x;
  const dy = event.changedTouches[0].clientY - touchStart.y;
  if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.3) showKarya(karyaPage + (dx < 0 ? 1 : -1));
  touchStart = null;
}, {passive:true});
karyaGrid.addEventListener('touchcancel', () => { touchStart = null; }, {passive:true});
showKarya(0);

</script>

</body>

</html>
