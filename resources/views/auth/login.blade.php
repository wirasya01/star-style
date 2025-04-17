<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Starboy Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to bottom, #000000, #1a1a1a);
      height: 100vh;
      overflow: hidden;
      position: relative;
      color: #fff;
    }

    /* Stars Background */
    .stars {
      background: transparent url('https://i.ibb.co/kHLKTW7/stars.gif') repeat;
      position: absolute;
      width: 100%;
      height: 100%;
      z-index: 0;
      opacity: 0.6;
      animation: scrollStars 60s linear infinite;
    }

    @keyframes scrollStars {
      from { background-position: 0 0; }
      to { background-position: -1000px 1000px; }
    }

    .glow, .glow2 {
      position: absolute;
      border-radius: 50%;
      z-index: 0;
      filter: blur(70px);
    }

    .glow {
      width: 500px;
      height: 500px;
      background: rgba(255, 255, 255, 0.07);
      top: -150px;
      left: -150px;
      animation: pulse 6s ease-in-out infinite;
    }

    .glow2 {
      width: 350px;
      height: 350px;
      background: rgba(255, 255, 255, 0.05);
      bottom: -100px;
      right: -100px;
      animation: pulse 8s ease-in-out infinite alternate;
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.1); }
      100% { transform: scale(1); }
    }

    form {
      height: auto;
      width: 400px;
      padding: 50px 35px;
      background-color: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      box-shadow: 0 0 40px rgba(255, 255, 255, 0.1);
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      border: 1px solid rgba(255, 255, 255, 0.1);
      z-index: 1;
      transition: all 0.4s ease;
    }

    form:hover {
      box-shadow: 0 0 60px rgba(255, 255, 255, 0.2);
    }

    form h3 {
      text-align: center;
      font-size: 26px;
      margin-bottom: 30px;
      background: linear-gradient(90deg, #ffffff, #999999);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    label {
      font-size: 14px;
      margin-top: 15px;
      display: block;
    }

    input {
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #fff;
      font-size: 14px;
      margin-top: 8px;
    }

    input::placeholder {
      color: #bbb;
    }

    .invalid-feedback {
      font-size: 13px;
      color: #ff6b6b;
      margin-top: 4px;
    }

    button {
      margin-top: 25px;
      width: 100%;
      background: linear-gradient(90deg, #ffffff, #999999);
      color: #000;
      padding: 14px;
      font-size: 16px;
      font-weight: bold;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      transform: translateY(-2px);
      background: linear-gradient(90deg, #ffffff, #cccccc);
    }

    .register-btn {
      display: block;
      margin-top: 16px;
      text-align: center;
      font-size: 14px;
      color: #ddd;
      text-decoration: none;
      transition: color 0.3s;
    }

    .register-btn:hover {
      color: #fff;
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="stars"></div>
  <div class="glow"></div>
  <div class="glow2"></div>

  <form method="POST" action="{{ route('login') }}">
    @csrf
    <h3>🌟 Login to StarStyle</h3>

    <label for="email">Email</label>
    <input id="email" type="email" placeholder="Email" name="email" value="{{ old('email') }}" required>
    @error('email')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    <label for="password">Password</label>
    <input id="password" type="password" placeholder="Password" name="password" required>
    @error('password')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    <button type="submit"><i class="fa-solid fa-meteor me-1"></i> Log In</button>
    <a href="{{ route('register') }}" class="register-btn">Belum punya akun? Register sekarang</a>
  </form>
</body>
</html>
