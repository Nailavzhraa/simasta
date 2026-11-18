
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi - SIMASTA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 52% 48%;
            font-family: 'Poppins', sans-serif;
            background: #f7f9fc;
            color: #263348;
        }

        /* PANEL KIRI */
        .left-panel {
            min-height: 100vh;
            padding: 55px 7%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            position: relative;
            background:
                radial-gradient(circle at 90% 15%,
                    rgba(130, 183, 255, .42), transparent 35%),
                radial-gradient(circle at 45% 95%,
                    rgba(255, 190, 142, .38), transparent 35%),
                linear-gradient(125deg, #b8f0dc 0%, #d7f1f6 55%, #dcecff 100%);
        }

        .left-panel::after {
            content: "";
            position: absolute;
            width: 390px;
            height: 390px;
            border-radius: 50%;
            right: -125px;
            bottom: -155px;
            background: rgba(255, 255, 255, .55);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 13px;
            position: relative;
            z-index: 1;
        }

        .brand img {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }

        .brand-name {
            font-size: 19px;
            font-weight: 700;
            color: #172c45;
            line-height: 1.3;
        }

        .brand-name small {
            display: block;
            font-size: 11px;
            font-weight: 400;
            letter-spacing: .5px;
            color: #607b91;
        }

        .left-content {
            position: relative;
            z-index: 1;
            max-width: 510px;
            margin-top: -20px;
        }

        .tagline {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            margin-bottom: 27px;
            border-radius: 30px;
            background: white;
            color: #0eaf83;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.2px;
            box-shadow: 0 5px 15px rgba(38, 100, 90, .08);
        }

        .tagline span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #28c99a;
        }

        .left-content h1 {
            font-size: clamp(27px, 3vw, 35px);
            line-height: 1.35;
            font-weight: 700;
            color: #202e43;
            margin-bottom: 20px;
        }

        .left-content h1 span {
            background: linear-gradient(90deg, #24b9a4, #408fe8, #e18b55);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .left-content p {
            max-width: 440px;
            font-size: 15px;
            line-height: 1.9;
            color: #526d85;
        }

        .copyright {
            position: relative;
            z-index: 1;
            font-size: 12px;
            color: #7892aa;
        }

        /* PANEL KANAN */
        .right-panel {
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 47px 7%;
            background: #f7f9fc;
        }

        .form-card {
            width: 100%;
            max-width: 650px;
            padding: 42px;
            background: white;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 18px 55px rgba(34, 53, 76, .09);
        }

        .form-card::before {
            content: "";
            position: absolute;
            height: 6px;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(90deg, #28d39b, #64a8ff, #ff963f);
        }

        .form-card h2 {
            font-size: 27px;
            font-weight: 700;
            color: #263348;
            margin-bottom: 8px;
        }

        .description {
            font-size: 14px;
            line-height: 1.8;
            color: #75869f;
            margin-bottom: 28px;
        }

        .alert {
            padding: 12px 15px;
            margin-bottom: 18px;
            border-radius: 9px;
            font-size: 12px;
            line-height: 1.7;
        }

        .error {
            background: #fff0f0;
            color: #c62828;
        }

        label {
            display: block;
            margin-bottom: 9px;
            font-size: 13px;
            font-weight: 600;
            color: #263348;
        }

        input {
            width: 100%;
            padding: 15px;
            border: 1px solid #d2dce9;
            border-radius: 10px;
            outline: none;
            font-family: inherit;
            font-size: 13px;
            color: #263348;
            background: #fff;
            margin-bottom: 20px;
            transition: .2s;
        }

        input:focus {
            border-color: #68aaff;
            box-shadow: 0 0 0 3px rgba(87, 151, 255, .13);
        }

        input::placeholder {
            color: #a2adbd;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(100deg, #83baff, #65e2b7);
            color: #17334a;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(83, 183, 186, .18);
            transition: .2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(83, 183, 186, .25);
        }

        .back {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: #8290a5;
        }

        .back a {
            color: #2588ef;
            text-decoration: none;
            font-weight: 600;
        }

        .back a:hover {
            text-decoration: underline;
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            body {
                grid-template-columns: 45% 55%;
            }

            .left-panel {
                padding: 40px 7%;
            }

            .right-panel {
                padding: 35px 5%;
            }

            .form-card {
                padding: 32px 25px;
            }
        }

        @media (max-width: 650px) {
            body {
                grid-template-columns: 1fr;
            }

            .left-panel {
                min-height: 290px;
                padding: 30px 25px;
            }

            .left-content {
                margin-top: 40px;
            }

            .left-content h1 {
                font-size: 25px;
            }

            .left-content p {
                font-size: 13px;
            }

            .copyright {
                margin-top: 35px;
            }

            .right-panel {
                min-height: auto;
                padding: 25px 18px 40px;
            }

            .form-card {
                padding: 35px 24px;
            }

            .form-card h2 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>

    <!-- PANEL KIRI -->
    <section class="left-panel">

        <div class="brand">
            <img src="{{ asset('images/logo-bps.png') }}" alt="Logo BPS">

            <div class="brand-name">
                SIMASTA
                <small>BADAN PUSAT STATISTIK KOTA BATU</small>
            </div>
        </div>

        <div class="left-content">

            <div class="tagline">
                <span></span>
                SISTEM MAGANG RESMI BPS KOTA BATU
            </div>

            <h1>
                Buat kata sandi baru<br>
                <span>dan amankan akunmu.</span>
            </h1>

            <p>
                Satu langkah lagi! Buat kata sandi baru untuk
                akun SIMASTA Anda. Pastikan kata sandi mudah
                Anda ingat dan tetap aman.
            </p>

        </div>

        <div class="copyright">
            © {{ date('Y') }} SIMASTA — BPS Kota Batu
        </div>

    </section>

    <!-- PANEL KANAN -->
    <main class="right-panel">

        <div class="form-card">

            <h2>Atur Ulang Kata Sandi</h2>

            <p class="description">
                Silakan buat kata sandi baru untuk akun SIMASTA Anda.
            </p>

            @if ($errors->any())
                <div class="alert error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Masukkan alamat email"
                    value="{{ old('email', $email) }}"
                    required
                    autocomplete="email"
                >

                <label for="password">Kata Sandi Baru</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan kata sandi baru"
                    required
                    autocomplete="new-password"
                >

                <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Ulangi kata sandi baru"
                    required
                    autocomplete="new-password"
                >

                <button type="submit" class="btn">
                    Simpan Kata Sandi Baru
                </button>
            </form>

            <div class="back">
                <a href="{{ route('login') }}">Kembali ke halaman masuk</a>
            </div>

        </div>

    </main>

</body>
</html>
