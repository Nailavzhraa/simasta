<!DOCTYPE html>
<html lang="id">
 
<head>
 
    <meta charset="UTF-8">
 
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
 
    <title>SIMASTA - Sistem Informasi Magang Statistik Kota Batu</title>
 
    <style>
 
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
 
        html {
            scroll-behavior: smooth;
        }
 
        :root {
            --neon: #34D399;
            --neon-dark: #10B981;
            --neon-soft: #A7F3D0;
            --orange: #FB923C;
            --orange-dark: #F97316;
            --orange-soft: #FED7AA;
            --blue: #60A5FA;
            --blue-dark: #3B82F6;
            --blue-soft: #BFDBFE;
            --navy: #1E293B;      /* dipakai untuk teks gelap, bukan background */
            --navy-soft: #334155;
            --footer-bg: #1E3A5F; /* medium blue, bukan hitam pekat */
        }
 
body {
    font-family: Arial, sans-serif;
    color: #1f2937;
 
    background:
        radial-gradient(
            circle at 12% 15%,
            rgba(52, 211, 153, 0.18),
            transparent 35%
        ),
        radial-gradient(
            circle at 88% 20%,
            rgba(96, 165, 250, 0.20),
            transparent 38%
        ),
        radial-gradient(
            circle at 55% 75%,
            rgba(251, 146, 60, 0.14),
            transparent 35%
        ),
        linear-gradient(
            135deg,
            #e7fff7 0%,
            #f5fbff 45%,
            #fffaf5 70%,
            #eaf3ff 100%
        );
 
    background-attachment: fixed;
    background-size: cover;
}
 
/* AGAR BACKGROUND MENYATU */
.section,
.about,
.feature-grid,
.alur-grid {
    background: transparent;
}
 
        /* =========================
           NAVBAR
        ========================= */
 
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
 
            width: 100%;
 
            z-index: 1000;
 
            padding: 18px 7%;
 
            display: flex;
            align-items: center;
            justify-content: space-between;
 
            transition: transform 0.3s ease, background 0.3s ease, padding 0.3s ease, box-shadow 0.3s ease;
        }
 
        /* Header hilang saat scroll ke bawah */
 
        .navbar.scrolled {
            background: white;
 
            box-shadow:
                0 4px 15px rgba(0,0,0,0.08);
 
            padding-top: 14px;
            padding-bottom: 14px;
        }
 
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
 
            text-decoration: none;
        }
 
        .logo img {
            width: 55px;
            height: 45px;
            
        }
 
        .logo-text {
            font-size: 22px;
            font-weight: bold;
            color: var(--navy);
            line-height: 1.1;
        }
 
        .logo-sub {
            font-size: 10px;
            letter-spacing: 1px;
            color: #64748b;
        }
 
        .nav-menu {
            display: flex;
            align-items: center;
            gap: 30px;
 
            list-style: none;
        }
 
        .nav-menu a {
            color: var(--navy-soft);
 
            text-decoration: none;
 
            font-size: 14px;
            font-weight: 600;
 
            transition: 0.2s;
        }
 
        .nav-menu a:hover {
            color: var(--neon-dark);
        }
 
        .btn-login {
            background: linear-gradient(120deg, var(--neon), var(--blue));
 
            color: white !important;
 
            padding: 10px 20px;
 
            border-radius: 30px;
 
            font-weight: 700;
        }
 
        .menu-toggle {
            display: none;
 
            width: 40px;
            height: 40px;
 
            border: none;
            border-radius: 10px;
 
            background: white;
 
            align-items: center;
            justify-content: center;
 
            cursor: pointer;
 
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
 
        .menu-toggle span,
        .menu-toggle span::before,
        .menu-toggle span::after {
            content: "";
            display: block;
            width: 18px;
            height: 2px;
            background: var(--navy);
            position: relative;
            transition: 0.2s;
        }
 
        .menu-toggle span::before { top: -6px; }
        .menu-toggle span::after { top: 4px; }
 
        /* =========================
           HERO
        ========================= */
 
        .hero {
 
            min-height: 500px;
 
            display: flex;
            align-items: center;
            padding: 215px 7% 80px;
            position: relative;
            overflow: hidden;
 
            background:
                radial-gradient(circle at 12% 15%, rgba(52, 211, 153, 0.30), transparent 45%),
                radial-gradient(circle at 88% 20%, rgba(96, 165, 250, 0.32), transparent 50%),
                radial-gradient(circle at 55% 100%, rgba(251, 146, 60, 0.28), transparent 45%),
                #F8FAFC;
        }
 
        .hero::before {
            content: "";
 
            position: absolute;
 
            width: 600px;
            height: 600px;
 
            border-radius: 50%;
 
            background: rgba(96, 165, 250, 0.08);
 
            right: -150px;
            top: -100px;
        }
 
        .hero-content {
            max-width: 650px;
 
            position: relative;
            z-index: 2;
        }
 
        .hero-badge {
 
            display: inline-flex;
            align-items: center;
            gap: 8px;
 
            background: white;
 
            border: 1px solid var(--neon-soft);
 
            color: var(--neon-dark);
 
            padding: 8px 16px;
 
            border-radius: 30px;
 
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
 
            margin-bottom: 22px;
 
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.12);
        }
 
        .hero-badge::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--neon);
        }
 
        .hero h1 {
 
            color: var(--navy);
 
            font-size: 48px;
 
            line-height: 1.15;
 
            margin-bottom: 20px;
        }
 
        .hero h1 span {
            background: linear-gradient(120deg, var(--neon-dark), var(--blue-dark), var(--orange-dark));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
 
        .hero p {
 
            color: #475569;
 
            font-size: 17px;
 
            line-height: 1.7;
 
            margin-bottom: 30px;
 
        }
 
        .hero-buttons {
 
            display: flex;
 
            gap: 15px;
        }
 
        .btn-primary {
 
            display: inline-block;
 
            background: linear-gradient(120deg, var(--orange), var(--orange-dark));
 
            color: white;
 
            padding: 14px 24px;
 
            border-radius: 30px;
 
            text-decoration: none;
 
            font-weight: bold;
 
            box-shadow: 0 10px 25px rgba(255, 122, 51, 0.25);
        }
 
        .btn-secondary {
 
            display: inline-block;
 
            border: 1px solid #cbd5e1;
 
            color: var(--navy-soft);
 
            padding: 14px 24px;
 
            border-radius: 30px;
 
            text-decoration: none;
 
            font-weight: bold;
 
            transition: 0.2s;
        }
 
        .btn-secondary:hover {
            border-color: var(--blue);
            color: var(--blue-dark);
        }
 
        /* =========================
           SECTION
        ========================= */
 
        .section {
 
            padding: 80px 7%;
            scroll-margin-top: 90px;
 
        }
 
        .section-title {
 
            text-align: center;
 
            margin-bottom: 50px;
        }
 
        .section-title p.eyebrow {
            color: var(--blue);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
        }
 
        .section-title h2 {
 
            font-size: 30px;
 
            color: var(--navy-soft);
 
            margin-bottom: 12px;
        }
 
        .section-title p {
 
            color: #64748b;
 
            max-width: 650px;
 
            margin: auto;
 
            line-height: 1.6;
        }
 
        /* =========================
           KEUNGGULAN
        ========================= */
 
        .feature-grid {
 
            display: grid;
 
            grid-template-columns:
                repeat(3, 1fr);
 
            gap: 25px;
        }
 
        .feature-card {
 
            padding: 35px 25px;
 
            background: #f8fafc;
 
            border: 1px solid #e5e7eb;
 
            border-top: 3px solid var(--card-accent, var(--blue));
 
            border-radius: 12px;
 
            text-align: center;
 
            transition: 0.3s;
        }
 
        .feature-card:hover {
 
            transform: translateY(-5px);
 
            box-shadow:
                0 10px 25px rgba(0,0,0,0.08);
        }
 
        .feature-card.neon { --card-accent: var(--neon); }
        .feature-card.blue { --card-accent: var(--blue); }
        .feature-card.orange { --card-accent: var(--orange); }
 
        .feature-icon {
 
            width: 55px;
            height: 55px;
 
            margin: auto auto 20px;
 
            display: flex;
 
            align-items: center;
            justify-content: center;
 
            border-radius: 12px;
 
            background: color-mix(in srgb, var(--card-accent, var(--blue)) 15%, white);
 
            color: var(--card-accent, var(--blue));
 
            font-size: 22px;
 
            font-weight: bold;
        }
 
        .feature-card h3 {
 
            margin-bottom: 12px;
 
            color: var(--navy-soft);
        }
 
        .feature-card p {
 
            color: #64748b;
 
            line-height: 1.6;
 
            font-size: 14px;
        }
 
        /* =========================
           ALUR
        ========================= */
 
        .alur {
            background: transparent;
        }
 
        .alur-grid {
 
            display: grid;
 
            grid-template-columns:
                repeat(7, 1fr);
 
            gap: 10px;
 
            text-align: center;
 
            position: relative;
        }
 
        .alur-grid::before {
            content: "";
 
            position: absolute;
 
            top: 25px;
            left: 7%;
            right: 7%;
            height: 2px;
 
            background: linear-gradient(90deg, var(--neon-dark), var(--blue), var(--orange));
 
            opacity: 0.35;
 
            z-index: 1;
        }
 
        .alur-item {
 
            position: relative;
        }
 
        .alur-number {
 
            width: 50px;
            height: 50px;
 
            margin: auto auto 15px;
 
            border-radius: 50%;
 
            background: var(--step-color, var(--blue));
 
            color: white;
 
            display: flex;
 
            align-items: center;
            justify-content: center;
 
            font-weight: bold;
 
            position: relative;
            z-index: 2;
        }
 
        .alur-item:nth-child(1) .alur-number,
        .alur-item:nth-child(2) .alur-number { --step-color: var(--neon-dark); }
 
        .alur-item:nth-child(3) .alur-number,
        .alur-item:nth-child(4) .alur-number,
        .alur-item:nth-child(5) .alur-number { --step-color: var(--blue); }
 
        .alur-item:nth-child(6) .alur-number,
        .alur-item:nth-child(7) .alur-number { --step-color: var(--orange); }
 
        .alur-item h4 {
 
            color: var(--navy-soft);
 
            margin-bottom: 8px;
        }
 
        .alur-item p {
 
            font-size: 12px;
 
            color: #64748b;
 
            line-height: 1.5;
        }
 
        /* =========================
           TENTANG
        ========================= */
 
        .about {
 
            display: grid;
 
            grid-template-columns:
                1fr 1fr;
 
            gap: 60px;
 
            align-items: center;
        }
 
        .about h2 {
 
            font-size: 32px;
 
            color: var(--navy-soft);
 
            margin-bottom: 20px;
        }
 
        .about p {
 
            color: #64748b;
 
            line-height: 1.8;
 
            margin-bottom: 15px;
        }
 
        .about-box {
 
            background: linear-gradient(160deg, #eafff5, #eff6ff);
 
            padding: 40px;
 
            border-radius: 15px;
 
            border: 1px solid #dbeafe;
        }
 
        .about-box h3 {
 
            color: var(--blue);
 
            margin-bottom: 15px;
        }
 
        /* =========================
           CTA
        ========================= */
 
        .cta {
 
            margin: 0 7% 80px;
 
            padding: 60px 40px;
 
            border-radius: 18px;
 
            text-align: center;
 
            position: relative;
            overflow: hidden;
 
            background:
                radial-gradient(circle at 20% 20%, rgba(52, 211, 153, 0.35), transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(251, 146, 60, 0.30), transparent 50%),
                linear-gradient(120deg, var(--blue-soft), #EFF6FF);
        }
 
        .cta h2 {
 
            color: var(--navy);
 
            font-size: 30px;
 
            margin-bottom: 15px;
 
            position: relative;
        }
 
        .cta p {
 
            color: var(--navy-soft);
 
            margin-bottom: 25px;
 
            position: relative;
        }
 
        .cta .btn-primary {
            position: relative;
        }
 
        /* =========================
           FOOTER
        ========================= */
 
        footer {
 
            background: var(--footer-bg);
 
            color: white;
 
            padding: 50px 7% 25px;
        }
 
        .footer-grid {
 
            display: grid;
 
            grid-template-columns:
                2fr 1fr 1fr;
 
            gap: 50px;
 
            margin-bottom: 40px;
        }
 
        footer h3 {
 
            margin-bottom: 15px;
        }
 
        footer p {
 
            color: #94a3b8;
 
            line-height: 1.7;
 
            font-size: 14px;
        }
 
        footer ul {
 
            list-style: none;
        }
 
        footer li {
 
            margin-bottom: 10px;
        }
 
        footer a {
 
            color: #94a3b8;
 
            text-decoration: none;
 
            font-size: 14px;
 
            transition: 0.2s;
        }
 
        footer a:hover {
 
            color: var(--neon);
        }
 
        .footer-bottom {
 
            border-top:
                1px solid rgba(255,255,255,0.1);
 
            padding-top: 20px;
 
            text-align: center;
 
            color: #64748b;
 
            font-size: 13px;
        }
 
        /* =========================
           RESPONSIVE
        ========================= */
 
@media (max-width: 900px) {
 
    .menu-toggle {
        display: flex;
    }
 
    .nav-menu {
        display: none;
 
        position: absolute;
 
        top: 78px;
        right: 5%;
 
        width: 220px;
 
        padding: 15px;
 
        background: white;
 
        border-radius: 14px;
 
        box-shadow:
            0 12px 35px rgba(0, 0, 0, 0.12);
 
        flex-direction: column;
        align-items: stretch;
 
        gap: 8px;
    }
 
    .nav-menu.active {
        display: flex;
    }
 
    .nav-menu li {
        width: 100%;
    }
 
    .nav-menu a {
        display: block;
        width: 100%;
        padding: 12px 14px;
 
        border-radius: 8px;
    }
 
    .nav-menu a:hover {
        background: #f1f5f9;
    }
 
    .nav-menu .btn-login {
        text-align: center;
    }
 
    .hero h1 {
        font-size: 38px;
    }
 
    .feature-grid {
        grid-template-columns: 1fr;
    }
 
    .alur-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
    }
 
    .about {
        grid-template-columns: 1fr;
    }
 
    .footer-grid {
        grid-template-columns: 1fr;
    }
}
 
        @media (max-width: 600px) {
 
            .hero {
                min-height: 600px;
            }
 
            .hero h1 {
                font-size: 32px;
            }
 
            .hero-buttons {
                flex-direction: column;
            }
 
            .btn-primary,
            .btn-secondary {
                text-align: center;
            }
 
            .alur-grid {
                grid-template-columns: 1fr;
            }
 
            .section {
                padding: 60px 5%;
            }
 
            .cta {
                margin-left: 5%;
                margin-right: 5%;
            }
 
        }
 
    </style>
 
</head>
 
<body>
 
 
<!-- =========================
     NAVBAR
========================= -->
 
<nav class="navbar" id="navbar">
 
    <a href="/" class="logo">
        <img src="{{ asset('images/logo-bps.png') }}" alt="Logo BPS">
        <span>
            <span class="logo-text" style="display:block;">SIMASTA</span>
            <span class="logo-sub">BADAN PUSAT STATISTIK KOTA BATU</span>
        </span>
    </a>
 
    <ul class="nav-menu" id="navMenu">
 
        <li>
            <a href="#beranda">Beranda</a>
        </li>
 
        <li>
            <a href="#tentang">Tentang</a>
        </li>
 
        <li>
            <a href="#alur">Alur Magang</a>
        </li>
 
        <li>
            <a href="#informasi">Informasi</a>
        </li>
 
        <li>
            <a href="/login"
               class="btn-login">
                Masuk
            </a>
        </li>
 
    </ul>
 
    <button class="menu-toggle" id="menuToggle" aria-label="Buka menu" aria-expanded="false">
        <span></span>
    </button>
 
</nav>
 
 
<!-- =========================
     HERO
========================= -->
 
<section class="hero"
         id="beranda">
 
    <div class="hero-content">
 
        <span class="hero-badge">
            SISTEM INFROMASI MAGANG RESMI BADAN PUSAT STATISTIK KOTA BATU
        </span>
 
        <h1>
            Magang Statistik yang Terarah,
            <span>Diakui BPS Kota Batu</span>
        </h1>
 
        <p>
            SIMASTA membantu mahasiswa dari berbagai kampus dan universitas mengikuti program magang
            di BPS Kota Batu secara digital — mulai dari pendaftaran, penempatan bidang, logbook harian,
            hingga penilaian dan sertifikat resmi, semuanya terpantau dalam satu sistem.
        </p>
 
        <div class="hero-buttons">
 
            <a href="/login"
               class="btn-primary">
                Masuk ke Aplikasi →
            </a>
 
            <a href="#alur"
               class="btn-secondary">
                Lihat Alur Magang
            </a>
 
        </div>
 
    </div>
 
</section>
 
 
<!-- =========================
     KEUNGGULAN
========================= -->
 
<section class="section"
         id="informasi">
 
    <div class="section-title">
 
        <p class="eyebrow">KEUNGGULAN</p>
 
        <h2>
            Kemudahan dalam Pengelolaan Magang
        </h2>
 
        <p>
            SIMASTA membantu mengintegrasikan
            berbagai proses administrasi dan
            kegiatan magang dalam satu sistem.
        </p>
 
    </div>
 
 
    <div class="feature-grid">
 
 
        <div class="feature-card neon">
 
            <div class="feature-icon">
                01
            </div>
 
            <h3>
                Pengajuan Terintegrasi
            </h3>
 
            <p>
                Mahasiswa dapat melakukan pengajuan
                kegiatan magang melalui sistem secara
                terstruktur dan mudah.
            </p>
 
        </div>
 
 
        <div class="feature-card blue">
 
            <div class="feature-icon">
                02
            </div>
 
            <h3>
                Monitoring Magang
            </h3>
 
            <p>
                Proses pelaksanaan magang dapat
                dipantau melalui penempatan,
                logbook, hingga penilaian.
            </p>
 
        </div>
 
 
        <div class="feature-card orange">
 
            <div class="feature-icon">
                03
            </div>
 
            <h3>
                Dokumen Terpusat
            </h3>
 
            <p>
                Pengelolaan dokumen magang menjadi
                lebih terorganisir dan mudah
                diakses oleh pengguna.
            </p>
 
        </div>
 
 
    </div>
 
</section>
 
 
<!-- =========================
     ALUR MAGANG
========================= -->
 
<section class="section alur"
         id="alur">
 
    <div class="section-title">
 
        <p class="eyebrow">ALUR</p>
 
        <h2>
            Alur Magang SIMASTA
        </h2>
 
        <p>
            Ikuti setiap tahapan kegiatan magang
            melalui sistem SIMASTA.
        </p>
 
    </div>
 
 
    <div class="alur-grid">
 
 
        <div class="alur-item">
 
            <div class="alur-number">
                1
            </div>
 
            <h4>
                Registrasi
            </h4>
 
            <p>
                Membuat akun mahasiswa
            </p>
 
        </div>
 
 
        <div class="alur-item">
 
            <div class="alur-number">
                2
            </div>
 
            <h4>
                Pengajuan
            </h4>
 
            <p>
                Mengajukan kegiatan magang
            </p>
 
        </div>
 
 
        <div class="alur-item">
 
            <div class="alur-number">
                3
            </div>
 
            <h4>
                Verifikasi
            </h4>
 
            <p>
                Pemeriksaan pengajuan
            </p>
 
        </div>
 
 
        <div class="alur-item">
 
            <div class="alur-number">
                4
            </div>
 
            <h4>
                Penempatan
            </h4>
 
            <p>
                Penentuan tempat magang
            </p>
 
        </div>
 
 
        <div class="alur-item">
 
            <div class="alur-number">
                5
            </div>
 
            <h4>
                Pelaksanaan
            </h4>
 
            <p>
                Melaksanakan kegiatan magang
            </p>
 
        </div>
 
 
        <div class="alur-item">
 
            <div class="alur-number">
                6
            </div>
 
            <h4>
                Logbook
            </h4>
 
            <p>
                Mencatat kegiatan magang
            </p>
 
        </div>
 
 
        <div class="alur-item">
 
            <div class="alur-number">
                7
            </div>
 
            <h4>
                Penilaian
            </h4>
 
            <p>
                Penilaian hasil magang
            </p>
 
        </div>
 
 
    </div>
 
</section>
 
 
<!-- =========================
     TENTANG
========================= -->
 
<section class="section"
         id="tentang">
 
    <div class="about">
 
 
        <div>
 
            <h2>
                Tentang SIMASTA
            </h2>
 
            <p>
                SIMASTA (Sistem Magang Statistik Kota Batu) adalah platform resmi Badan Pusat
                Statistik Kota Batu untuk mengelola seluruh proses magang secara digital, mulai
                dari pendaftaran hingga penerbitan sertifikat.
            </p>
 
            <p>
                Sistem ini menghubungkan mahasiswa dari berbagai kampus, admin, dan pembimbing
                lapangan dalam satu platform, sehingga proses yang dulunya manual lewat surat
                dan WhatsApp kini lebih cepat, terstruktur, dan terdokumentasi rapi.
            </p>
 
        </div>
 
 
        <div class="about-box">
 
            <h3>
                Tujuan SIMASTA
            </h3>
 
            <p>
                Meningkatkan efektivitas pengelolaan
                kegiatan magang melalui sistem yang
                terintegrasi, mudah digunakan, dan
                terorganisir.
            </p>
 
        </div>
 
 
    </div>
 
</section>
 
 
<!-- =========================
     CTA
========================= -->
 
<section class="cta">
 
    <h2>
        Siap Jadi Bagian dari BPS Kota Batu?
    </h2>
 
    <p>
        Masuk ke SIMASTA untuk mendaftar dan mengelola
        kegiatan magang kamu.
    </p>
 
    <a href="/login"
       class="btn-primary">
        Masuk ke SIMASTA →
    </a>
 
</section>
 
 
<!-- =========================
     FOOTER
========================= -->
 
<footer>
 
    <div class="footer-grid">
 
 
        <div>
 
            <h3>
                SIMASTA
            </h3>
 
            <p>
                Sistem Magang Statistik Kota Batu — platform resmi BPS Kota Batu untuk mahasiswa
                dari berbagai kampus dan universitas.
            </p>
 
        </div>
 
 
        <div>
 
            <h3>
                Navigasi
            </h3>
 
            <ul>
 
                <li>
                    <a href="#beranda">
                        Beranda
                    </a>
                </li>
 
                <li>
                    <a href="#tentang">
                        Tentang
                    </a>
                </li>
 
                <li>
                    <a href="#alur">
                        Alur Magang
                    </a>
                </li>
 
                <li>
                    <a href="/login">
                        Masuk
                    </a>
                </li>
 
            </ul>
 
        </div>
 
 
        <div>
 
            <h3>
                Informasi
            </h3>
 
            <p>
                Badan Pusat Statistik
            </p>
 
            <p>
                Kota Batu
            </p>
 
        </div>
 
 
    </div>
 
 
    <div class="footer-bottom">
 
        © 2026 SIMASTA — BPS Kota Batu. All Rights Reserved.
 
    </div>
 
</footer>
 
<!-- =========================
     JAVASCRIPT NAVBAR
========================= -->
 
<script>
 
    const navbar =
        document.getElementById('navbar');
 
    const menuToggle = document.getElementById('menuToggle');
    const navMenu = document.getElementById('navMenu');
 
    menuToggle.addEventListener('click', function () {
        const isOpen = navMenu.classList.toggle('active');
        menuToggle.setAttribute('aria-expanded', isOpen);
    });
 
    // Tutup menu otomatis kalau salah satu link diklik
    navMenu.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            navMenu.classList.remove('active');
            menuToggle.setAttribute('aria-expanded', 'false');
        });
    });
 
    let lastScrollY = window.scrollY;
 
    window.addEventListener('scroll', function () {
 
        const currentScrollY = window.scrollY;
 
        // Background putih muncul setelah scroll 50px (perilaku lama, dipertahankan)
        if (currentScrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
 
        // Header hilang saat scroll ke bawah, muncul lagi saat scroll ke atas
        if (currentScrollY < 40) {
            navbar.classList.remove('nav-hidden');
        } else if (currentScrollY > lastScrollY) {
            navbar.classList.add('nav-hidden');
        } else {
            navbar.classList.remove('nav-hidden');
        }
 
        lastScrollY = currentScrollY;
 
    });
 
</script>
 
 
</body>
 
</html>
   m    m n 