<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - SIMASTA</title>

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
            align-items: center;
            justify-content: center;
            background: #f8fafc;
        }

        .login-container {
            width: 400px;
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
            margin-bottom: 10px;
        }

        .title {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            cursor: pointer;
        }

        .register {
            text-align: center;
            margin-top: 20px;
        }

        .register a {
            color: #2563eb;
            text-decoration: none;
        }

    </style>

</head>

<body>

    <div class="login-container">

        <div class="logo">
            SIMASTA
        </div>

        <p class="title">
            Login ke akun Anda
        </p>

<form method="POST" action="/login">

    @csrf

    <div class="form-group">

        <label>
            Email
        </label>

        <input
            type="email"
            name="email"
            placeholder="Masukkan email"
            value="{{ old('email') }}"
            required>

    </div>


    <div class="form-group">

        <label>
            Password
        </label>

        <input
            type="password"
            name="password"
            placeholder="Masukkan password"
            required>

    </div>


    @if ($errors->any())

        <div style="
            color: #dc2626;
            font-size: 14px;
            margin-bottom: 15px;
        ">

            {{ $errors->first() }}

        </div>

    @endif


    @if (session('success'))

        <div style="
            color: #16a34a;
            font-size: 14px;
            margin-bottom: 15px;
        ">

            {{ session('success') }}

        </div>

    @endif


    <button type="submit">

        Login

    </button>

</form>

        </form>


        <div class="register">

            Belum memiliki akun?

            <a href="/register">
                Daftar
            </a>

        </div>

    </div>

</body>

</html>