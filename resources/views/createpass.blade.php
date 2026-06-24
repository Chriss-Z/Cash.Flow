<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cash.Flow | Create New Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        :root {
            color-scheme: light;
            font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
            background: #F8FBFB;
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
            background: #F8FBFB;
            color: #1B1B18;
        }

        .card {
            width: min(100%, 520px);
            background: #ffffff;
            border: 1px solid #E8EEEE;
            border-radius: 32px;
            padding: 44px 38px;
            box-shadow: 0 32px 80px -56px rgba(0, 0, 0, 0.18);
        }

        .brand {
            text-align: center;
            font-size: 56px;
            line-height: 1.05;
            font-weight: 700;
            letter-spacing: -0.05em;
            margin: 0 0 8px;
        }

        .brand .accent {
            color: #2B9D8F;
        }

        .brand .muted {
            color: #5C6C6B;
        }

        .title {
            text-align: center;
            margin: 0 0 8px;
            font-size: 1.3rem;
            font-weight: 700;
        }

        .subtitle {
            text-align: center;
            margin: 0 auto 32px;
            color: #57666A;
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.6;
            max-width: 360px;
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
            border: 1px solid #D6DFE3;
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 0.96rem;
            color: #1B1B18;
            background: #FFFFFF;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-field:focus {
            border-color: #2B9D8F;
            box-shadow: 0 0 0 4px rgba(43, 157, 143, 0.14);
        }

        .button-primary {
            width: 100%;
            border: none;
            border-radius: 14px;
            padding: 16px 18px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #FFFFFF;
            background: #2B9D8F;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .button-primary:hover {
            background: #237766;
        }

        .back-link {
            margin-top: 18px;
            text-align: center;
            font-size: 0.94rem;
            color: #6D7A7C;
        }

        .back-link a {
            color: #2B9D8F;
            font-weight: 600;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 14px;
            border: 1px solid #E4E8E8;
            background: #F8FCFC;
            color: #1C3434;
            padding: 14px 16px;
            margin-bottom: 24px;
            font-size: 0.95rem;
        }

        .error-text {
            margin: 8px 0 0;
            color: #C44C4C;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1 class="brand"><span class="accent">Cash.</span><span class="muted">Flow</span></h1>
        <h2 class="title">Create New Password</h2>
        <p class="subtitle">Create a new password to keep your account secure. Make sure it is strong and easy for you to remember.</p>

        @if(session('status'))
            <div class="alert">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="alert" style="border-color:#F7D6D6;background:#FFF2F2;color:#7A2A2A;">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert" style="border-color:#F7D6D6;background:#FFF2F2;color:#7A2A2A;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('/createpass') }}">
            @csrf
            <input type="hidden" name="email" value="{{ old('email', $email) }}" />

            <div class="input-group">
                <label for="password" class="input-label">New Password</label>
                <input id="password" name="password" type="password" required placeholder="Enter new password" class="input-field" />
            </div>

            <div class="input-group">
                <label for="password_confirmation" class="input-label">Retype Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="Confirm new password" class="input-field" />
            </div>

            <button type="submit" class="button-primary">Password Reset</button>
        </form>

        <p class="back-link"><a href="{{ route('login') }}">Back to login</a></p>
    </div>
</body>
</html>
