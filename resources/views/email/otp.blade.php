<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Cash.Flow | OTP</title>
  <style>
    body { font-family: 'Poppins', sans-serif; background: #F8FBFB; padding: 20px; }
    .card { background: #fff; border-radius: 12px; padding: 24px; border: 1px solid #E8EEEE; }
    .brand { font-size: 24px; font-weight: 700; color: #2B9D8F; }
    .otp { font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #1B1B18; }
    .footer { margin-top: 20px; font-size: 14px; color: #6D7A7C; }
  </style>
</head>
<body>
  <div class="card">
    <div class="brand">Cash.Flow</div>
    <p>Halo, berikut adalah kode OTP untuk verifikasi akun kamu:</p>
    <p class="otp">{{ $otp }}</p>
    <p class="footer">Kode ini berlaku selama 5 menit. Jangan bagikan ke siapa pun.</p>
  </div>
</body>
</html>
