<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cash.Flow | Forget Password</title>
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

        .forget-card {
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

        .title {
            text-align: center;
            margin: 0 0 10px;
            font-size: 1.15rem;
            font-weight: 700;
        }

        .subtitle {
            text-align: center;
            margin: 0 0 32px;
            color: #4E5555;
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.6;
            max-width: 330px;
            margin-left: auto;
            margin-right: auto;
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

        .back-link {
            margin-top: 24px;
            text-align: center;
            font-size: 0.95rem;
            color: #4E5555;
        }

        .back-link a {
            color: #299D91;
            font-weight: 600;
            text-decoration: none;
        }

        .back-link a:hover {
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
    <div class="forget-card">
        <h1 class="brand"><span class="accent">Cash.</span><span class="muted">Flow</span></h1>
        <h2 class="title">Forget Password?</h2>
        <p class="subtitle">Enter your email address to get the password reset link.</p>

        @if(session('error'))
            <div class="alert">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ url('/forgot-password') }}">
            @csrf

            <div class="input-group">
                <label for="email" class="input-label">Email Address</label>
                <input id="email" name="email" type="email" required placeholder="christian@gmail.com" class="input-field" />
            </div>

            <div class="input-group">
                <label for="confirm_email" class="input-label">Confirm Email Address</label>
                <input id="confirm_email" name="confirm_email" type="email" required placeholder="christian@gmail.com" class="input-field" />
            </div>

            <button type="submit" class="button-primary">Password Reset</button>
        </form>

        <p class="back-link"><a href="{{ route('login') }}">Back to login</a></p>
    </div>
</body>
</html>
