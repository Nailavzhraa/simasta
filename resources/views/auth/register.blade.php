<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Register - SIMASTA</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8fafc;
        }

        .register-container {
            width: 450px;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
        }

        .logo {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
        }

        .title {
            text-align: center;
            margin: 10px 0 30px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
        }

        .login {
            text-align: center;
            margin-top: 20px;
        }

        .login a {
            color: #2563eb;
        }

    </style>

</head>

<body>

    <div class="register-container">

        <div class="logo">
            SIMASTA
        </div>

        <p class="title">
            Buat akun mahasiswa
        </p>

<form action="/register" method="POST">

    @csrf

    <div class="form-group">

        <label>
            Nama Lengkap
        </label>

        <input
            type="text"
            name="name"
            placeholder="Nama lengkap"
            required>

    </div>


    <div class="form-group">

        <label>
            Email
        </label>

        <input
            type="email"
            name="email"
            placeholder="Email"
            required>

    </div>


    <div class="form-group">

        <label>
            Password
        </label>

        <input
            type="password"
            name="password"
            placeholder="Password"
            required>

    </div>


    <div class="form-group">

        <label>
            Konfirmasi Password
        </label>

        <input
            type="password"
            name="password_confirmation"
            placeholder="Ulangi password"
            required>

    </div>


    <button type="submit">
        Daftar
    </button>

</form>

        <div class="login">

            Sudah punya akun?

            <a href="/login">
                Login
            </a>

        </div>

    </div>

</body>

</html>