<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cash.Flow | OTP Verification</title>
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

        .otp-card {
            width: min(100%, 520px);
            background: #ffffff;
            border: 1px solid #E8EEEE;
            border-radius: 32px;
            padding: 42px 38px;
            box-shadow: 0 32px 80px -56px rgba(0, 0, 0, 0.18);
        }

        .brand {
            text-align: center;
            font-size: 56px;
            line-height: 1.05;
            font-weight: 700;
            letter-spacing: -0.05em;
            margin: 0 0 6px;
        }

        .brand .accent {
            color: #2B9D8F;
        }

        .brand .muted {
            color: #5C6C6B;
        }

        .title {
            text-align: center;
            margin: 0 0 12px;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .subtitle {
            text-align: center;
            margin: 0 auto 32px;
            color: #57666A;
            font-size: 0.96rem;
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

        .otp-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 24px;
        }

        .otp-digit {
            width: 100%;
            height: 64px;
            border: 1px solid #D6DFE3;
            border-radius: 16px;
            text-align: center;
            font-size: 1.65rem;
            font-weight: 700;
            color: #1B1B18;
            background: #FBFEFF;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .otp-digit:focus {
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
    <div class="otp-card">
        <h1 class="brand"><span class="accent">Cash.</span><span class="muted">Flow</span></h1>
        <h2 class="title">OTP Verification</h2>
        <p class="subtitle">Check your email and enter the 6-digit code sent to your inbox.</p>

        @if(session('status'))
            <div class="alert">{{ session('status') }}</div>
        @endif
        @if(session('success'))
            <div class="alert" style="border-color:#CDE8E4;background:#EDF8F7;color:#1F4E4B;">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert" style="border-color:#F7D6D6;background:#FFF2F2;color:#7A2A2A;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('/otp') }}" onsubmit="prepareOtpValue()">
            @csrf
            <div class="input-group">
                <label for="email" class="input-label">Email Address</label>
                <input id="email" name="email" type="email" required readonly placeholder="christian@gmail.com" class="input-field" value="{{ old('email', $email ?? request('email')) }}" />
            </div>

            <div class="input-group">
                <label class="input-label">Verification Code</label>
                <div class="otp-grid">
                    <input id="otp1" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="otp-digit" autocomplete="one-time-code" />
                    <input id="otp2" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="otp-digit" autocomplete="one-time-code" />
                    <input id="otp3" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="otp-digit" autocomplete="one-time-code" />
                    <input id="otp4" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="otp-digit" autocomplete="one-time-code" />
                    <input id="otp5" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="otp-digit" autocomplete="one-time-code" />
                    <input id="otp6" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="otp-digit" autocomplete="one-time-code" />
                </div>
                @if($errors->has('otp'))
                    <p class="error-text">{{ $errors->first('otp') }}</p>
                @endif
            </div>

            <input type="hidden" name="otp" id="otp_input" value="{{ old('otp') }}" />
            <input type="hidden" name="flow" value="{{ old('flow', $flow ?? 'forgot') }}" />
            <button type="submit" class="button-primary">Continue</button>
        </form>

        <p class="back-link"><a href="{{ route('login') }}">Back to login</a></p>
    </div>

    <script>
        const otpInputs = document.querySelectorAll('.otp-digit');

        otpInputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                input.value = input.value.replace(/[^0-9]/g, '');
                if (input.value.length === 1 && otpInputs[index + 1]) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (event) => {
                if (event.key === 'Backspace' && input.value === '' && otpInputs[index - 1]) {
                    otpInputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', (event) => {
                event.preventDefault();
                const paste = (event.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                paste.split('').slice(0, 6).forEach((digit, idx) => {
                    otpInputs[idx].value = digit;
                });
                const next = Math.min(paste.length, otpInputs.length - 1);
                otpInputs[next].focus();
            });
        });

        function prepareOtpValue() {
            const code = Array.from(otpInputs).map(input => input.value).join('');
            document.getElementById('otp_input').value = code;
        }

        document.addEventListener('DOMContentLoaded', () => {
            otpInputs[0].focus();
        });
    </script>
</body>
</html>
