<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cash.Flow | Login</title>
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
            color: #1B1B18;
            background: #FDFDFC;
        }

        .login-card {
            width: min(100%, 420px);
            background: #ffffff;
            border: 1px solid #E3E3E0;
            border-radius: 24px;
            box-shadow: 0 30px 80px -50px rgba(0, 0, 0, 0.18);
            padding: 42px 36px;
        }

        .brand {
            font-size: 64px;
            line-height: 1.05;
            font-weight: 700;
            letter-spacing: -0.05em;
            margin: 0 0 32px;
            text-align: center;
        }

        .brand .accent {
            color: #299D91;
        }

        .brand .muted {
            color: #808F8E;
        }

        .field-label {
            display: block;
            font-size: 0.85rem;
            line-height: 1.5;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .input-group {
            margin-bottom: 24px;
        }

        .input-field {
            width: 100%;
            border: 1px solid #E3E3E0;
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 0.95rem;
            line-height: 1.4;
            color: #1B1B18;
            background: #ffffff;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-field:focus {
            border-color: #1E9A84;
            box-shadow: 0 0 0 4px rgba(30, 154, 132, 0.14);
        }

        .input-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .input-row a {
            color: #1E9A84;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .input-row a:hover {
            text-decoration: underline;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper button {
            position: absolute;
            inset-y: 0;
            right: 14px;
            border: none;
            background: transparent;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #7B7B7B;
        }

        .checkbox-field {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
            color: #706F6C;
            font-size: 0.95rem;
        }

        .checkbox-field input {
            width: 18px;
            height: 18px;
            accent-color: #1E9A84;
            border: 1px solid #E3E3E0;
            border-radius: 6px;
        }

        .button-primary {
            width: 100%;
            border: none;
            border-radius: 14px;
            padding: 14px 18px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
            background: #1E9A84;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .button-primary:hover {
            background: #17816e;
        }

        .footer-link {
            margin-top: 22px;
            text-align: center;
            font-size: 0.95rem;
        }

        .footer-link a {
            color: #1E9A84;
            font-weight: 600;
            text-decoration: none;
        }

        .footer-link a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 14px;
            border: 1px solid #F8B803;
            background: #fff8e6;
            color: #5A4D1A;
            padding: 14px 16px;
            margin-bottom: 24px;
            font-size: 0.94rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h1 class="brand"><span class="accent">Cash.</span><span class="muted">Flow</span></h1>

        @if(session('error'))
            <div class="alert">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-group">
                <label for="email" class="field-label">Email Address</label>
                <input id="email" name="email" type="email" required placeholder="christian@gmail.com" class="input-field" />
            </div>

            <div class="input-group">
                <div class="input-row">
                    <label for="password" class="field-label">Password</label>
                    <a href="/forgot-password">Forgot Password?</a>
                </div>
                <div class="password-wrapper">
                    <input id="password" name="password" type="password" required placeholder="................." class="input-field" />
                    <button type="button" aria-label="Toggle password visibility" onclick="togglePassword()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5C7 5 2.73 8.11 1 12C2.73 15.89 7 19 12 19C17 19 21.27 15.89 23 12C21.27 8.11 17 5 12 5Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>

            <label class="checkbox-field">
                <input type="checkbox" name="remember" />
                Keep me signed in
            </label>

            <button type="submit" class="button-primary">Login</button>
        </form>

        <p class="footer-link"> <a href="/register">Create an account</a> </p>
    </div>

    <script>
        function togglePassword() {
            var input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
