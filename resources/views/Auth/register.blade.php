<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cash.Flow | Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        :root {
            color-scheme: light;
            font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
            background: #FDFDFC;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            min-height: 100vh;
            width: 100%;
            height: 100%;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #FDFDFC;
            color: #1B1B18;
        }

        .register-card {
            width: min(100%, 460px);
            background: #ffffff;
            border: 1px solid #E3E3E0;
            border-radius: 32px;
            padding: 40px 38px;
            box-shadow: 0 40px 100px -60px rgba(0, 0, 0, 0.18);
        }

        .brand {
            text-align: center;
            font-size: 64px;
            line-height: 1.05;
            font-weight: 700;
            letter-spacing: -0.05em;
            margin: 0 0 8px;
        }

        .brand .accent {
            color: #299D91;
        }

        .brand .muted {
            color: #808F8E;
        }

        .subtitle {
            text-align: center;
            margin: 0 0 32px;
            color: #4E5555;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-label {
            display: block;
            margin-bottom: 10px;
            font-size: 0.88rem;
            font-weight: 600;
            color: #1B1B18;
        }

        .input-field {
            width: 100%;
            border: 1px solid #D2D6DA;
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 0.96rem;
            color: #1B1B18;
            background: #FFFFFF;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-field:focus {
            border-color: #299D91;
            box-shadow: 0 0 0 4px rgba(41, 157, 145, 0.12);
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            inset-y: 0;
            right: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            cursor: pointer;
            color: #7B7B7B;
        }

        .button-primary {
            width: 100%;
            border: none;
            border-radius: 14px;
            padding: 14px 18px;
            font-size: 0.96rem;
            font-weight: 700;
            color: #FFFFFF;
            background: #299D91;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .button-primary:hover {
            background: #238371;
        }

        .login-link {
            margin-top: 24px;
            text-align: center;
            font-size: 0.95rem;
            color: #4E5555;
        }

        .login-link a {
            color: #299D91;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 14px;
            border: 1px solid #F8B803;
            background: #FFF8E6;
            color: #5A4D1A;
            padding: 14px 16px;
            margin-bottom: 24px;
            font-size: 0.94rem;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <h1 class="brand"><span class="accent">Cash.</span><span class="muted">Flow</span></h1>
        <p class="subtitle">Create an account</p>

        @if(session('error'))
            <div class="alert">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/register">
            @csrf

            <div class="input-group">
                <label for="name" class="input-label">Nama Lengkap</label>
                <input id="name" name="name" type="text" required placeholder="Christian Prawira Gunawan" class="input-field" value="{{ old('name') }}" />
            </div>

            <div class="input-group">
                <label for="email" class="input-label">Email Address</label>
                <input id="email" name="email" type="email" required placeholder="christian@gmail.com" class="input-field" value="{{ old('email') }}" />
            </div>

            <div class="input-group">
                <label for="password" class="input-label">Password</label>
                <div class="password-wrapper">
                    <input id="password" name="password" type="password" required placeholder="................." class="input-field" />
                    <button type="button" class="toggle-password" onclick="togglePassword()">👁</button>
                </div>
            </div>

            <div class="input-group">
                <label for="password_confirmation" class="input-label">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="................." class="input-field" />
            </div>

            <button type="submit" class="button-primary">Register</button>
        </form>

        <p class="login-link">already have account? <a href="{{ route('login') }}">sign in here</a></p>
    </div>

    <script>
        function togglePassword() {
            var input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
