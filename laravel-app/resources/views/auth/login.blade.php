<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light">
  <title>Masuk · Sistem Pengolahan Data Transaksi Keuangan Berbasis Web pada Telaga Rizqy 21</title>
  <style>
    :root { color-scheme: light; --ink:#17334a; --muted:#617789; --line:#dce6e9; --paper:#fff; --canvas:#eff5f5; --blue:#176b83; --blue-dark:#105365; --green:#85c6a3; --error:#a33131; }
    * { box-sizing:border-box; }
    body { margin:0; min-height:100vh; display:grid; place-items:center; padding:32px 20px; background:var(--canvas); color:var(--ink); font-family:Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
    .login-shell { width:min(100%, 1000px); min-height:600px; display:grid; grid-template-columns:0.92fr 1.08fr; overflow:hidden; background:var(--paper); border:1px solid #e3ebed; border-radius:24px; box-shadow:0 28px 70px rgba(24,57,73,.12); }
    .brand-panel { position:relative; isolation:isolate; overflow:hidden; display:flex; flex-direction:column; justify-content:space-between; padding:42px; color:#f5fbfc; background:#174e65; }
    .brand-panel::before,.brand-panel::after { content:""; position:absolute; z-index:-1; border:1px solid rgba(199,235,220,.16); border-radius:50%; }
    .brand-panel::before { width:430px; height:430px; right:-190px; bottom:-120px; }
    .brand-panel::after { width:310px; height:310px; right:-130px; bottom:-60px; }
    .brand-lockup { display:flex; align-items:center; gap:14px; }
    .brand-mark { width:54px; height:54px; flex:none; display:grid; place-items:center; border-radius:17px; color:#174e65; background:#f4fbf7; }
    .brand-name { font-size:15px; line-height:1.25; font-weight:800; letter-spacing:.035em; }
    .brand-caption { margin-top:4px; color:#c4d9df; font-size:11px; }
    .brand-copy { max-width:340px; padding-bottom:28px; }
    .brand-copy h1 { max-width:330px; margin:0; font-size:clamp(32px, 4vw, 45px); line-height:1.08; letter-spacing:-.045em; font-weight:720; }
    .brand-copy p { max-width:310px; margin:18px 0 0; color:#c8dce1; font-size:14px; line-height:1.75; }
    .milk-note { display:flex; align-items:center; gap:10px; color:#d4e7e4; font-size:12px; }
    .milk-note span { width:7px; height:7px; border-radius:50%; background:#9fd5b8; box-shadow:0 0 0 5px rgba(159,213,184,.14); }
    .form-panel { display:flex; align-items:center; justify-content:center; padding:50px 56px; }
    .form-wrap { width:min(100%, 370px); }
    .mobile-brand { display:none; }
    .form-wrap h2 { margin:0; font-size:28px; letter-spacing:-.035em; line-height:1.2; }
    .form-intro { margin:9px 0 30px; color:var(--muted); font-size:14px; line-height:1.55; }
    .notice { margin:0 0 18px; padding:12px 14px; border:1px solid #b7dfca; border-radius:10px; background:#effaf3; color:#286a48; font-size:13px; line-height:1.5; }
    .notice-error { border-color:#f0c8c8; background:#fff4f3; color:var(--error); }
    .field { margin-top:18px; }
    .field label { display:block; margin-bottom:7px; font-size:13px; font-weight:650; }
    .input-shell { position:relative; }
    .input-shell input { display:block; width:100%; height:48px; padding:0 14px; border:1px solid var(--line); border-radius:10px; outline:none; background:#fbfdfd; color:var(--ink); font:inherit; font-size:14px; transition:border-color .15s, box-shadow .15s, background .15s; }
    .input-shell input::placeholder { color:#93a4ae; }
    .input-shell input:focus { border-color:var(--blue); background:#fff; box-shadow:0 0 0 3px rgba(23,107,131,.13); }
    .password-input { padding-right:76px !important; }
    .reveal { position:absolute; top:50%; right:10px; transform:translateY(-50%); padding:7px 5px; border:0; border-radius:6px; background:transparent; color:#4d7280; font:inherit; font-size:11px; font-weight:700; cursor:pointer; }
    .reveal:hover { color:var(--blue-dark); background:#eef6f6; }
    .reveal:focus-visible,.forgot:focus-visible,.submit:focus-visible,.remember input:focus-visible { outline:3px solid rgba(23,107,131,.35); outline-offset:3px; }
    .form-options { display:flex; align-items:center; justify-content:space-between; gap:12px; margin:17px 0 24px; }
    .remember { display:inline-flex; align-items:center; gap:8px; color:#526b7b; font-size:13px; cursor:pointer; }
    .remember input { width:16px; height:16px; margin:0; accent-color:var(--blue); }
    .forgot { color:var(--blue); font-size:13px; font-weight:650; text-decoration:none; }
    .forgot:hover { color:var(--blue-dark); text-decoration:underline; }
    .submit { width:100%; min-height:49px; border:0; border-radius:10px; background:var(--blue); color:#fff; font:inherit; font-size:14px; font-weight:700; cursor:pointer; transition:background .15s, transform .15s; }
    .submit:hover { background:var(--blue-dark); }
    .submit:active { transform:translateY(1px); }
    .submit:disabled { cursor:wait; opacity:.75; }
    .form-foot { margin:23px 0 0; color:#7a8d98; font-size:11px; line-height:1.6; text-align:center; }
    .register-link { display:block; margin-top:20px; color:var(--blue); font-size:13px; font-weight:650; text-align:center; text-decoration:none; }
    .register-link:hover { color:var(--blue-dark); text-decoration:underline; }
    .error-text { margin-top:6px; color:var(--error); font-size:12px; }
    @media (max-width:760px) { body { padding:18px 14px; } .login-shell { min-height:0; max-width:480px; grid-template-columns:1fr; border-radius:20px; } .brand-panel { display:none; } .form-panel { padding:30px 26px 32px; } .mobile-brand { display:flex; align-items:center; gap:12px; margin-bottom:32px; } .mobile-brand .brand-mark { width:46px; height:46px; background:#eaf5f1; } .mobile-brand .brand-caption { color:#708594; } }
    @media (prefers-reduced-motion:reduce) { *,*::before,*::after { scroll-behavior:auto !important; transition:none !important; } }
  </style>
</head>
<body>
  <main class="login-shell">
    <aside class="brand-panel" aria-label="Telaga Rizqy 21">
      <div class="brand-lockup">
        <div class="brand-mark" aria-hidden="true">
          <svg width="34" height="38" viewBox="0 0 40 44" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 3h12v5l3 5v25a3 3 0 0 1-3 3H14a3 3 0 0 1-3-3V13l3-5V3Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/><path d="M14 8h12M11 15h18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/><path d="M20 19c-3 4-4 5.5-4 7.3a4 4 0 0 0 8 0c0-1.8-1-3.3-4-7.3Z" fill="#78B99A"/></svg>
        </div>
        <div><div class="brand-name">TELAGA RIZQY 21</div><div class="brand-caption">Sistem Pengolahan Data Transaksi Keuangan Berbasis Web</div></div>
      </div>
      <div class="brand-copy">
        <h1>Selamat datang kembali.</h1>
        <p>Masuk untuk melanjutkan ke layanan Telaga Rizqy 21 dengan aman.</p>
      </div>
      <div class="milk-note"><span aria-hidden="true"></span>Segar, dekat, dan terpercaya</div>
    </aside>

    <section class="form-panel" aria-labelledby="login-title">
      <div class="form-wrap">
        <div class="mobile-brand">
          <div class="brand-mark" aria-hidden="true">
            <svg width="30" height="34" viewBox="0 0 40 44" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 3h12v5l3 5v25a3 3 0 0 1-3 3H14a3 3 0 0 1-3-3V13l3-5V3Z" stroke="#174E65" stroke-width="2.2" stroke-linejoin="round"/><path d="M14 8h12M11 15h18" stroke="#174E65" stroke-width="2.2" stroke-linecap="round"/><path d="M20 19c-3 4-4 5.5-4 7.3a4 4 0 0 0 8 0c0-1.8-1-3.3-4-7.3Z" fill="#78B99A"/></svg>
          </div>
          <div><div class="brand-name">TELAGA RIZQY 21</div><div class="brand-caption">Sistem Pengolahan Data Transaksi Keuangan Berbasis Web</div></div>
        </div>

        <h2 id="login-title">Masuk ke akun</h2>
        <p class="form-intro">Gunakan email atau username dan kata sandi Anda.</p>

        @if (session('status'))
          <div class="notice" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
          <div class="notice notice-error" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" id="login-form">
          @csrf
          <div class="field">
            <label for="login">Email atau username</label>
            <div class="input-shell"><input id="login" name="login" type="text" value="{{ old('login') }}" autocomplete="username" maxlength="255" required autofocus placeholder="nama@email.com atau username" aria-describedby="login-error"></div>
            @error('login')<div id="login-error" class="error-text">{{ $message }}</div>@enderror
          </div>

          <div class="field">
            <label for="password">Kata sandi</label>
            <div class="input-shell">
              <input class="password-input" id="password" name="password" type="password" autocomplete="current-password" required placeholder="Masukkan kata sandi">
              <button class="reveal" type="button" id="password-toggle" aria-controls="password" aria-pressed="false">Tampilkan</button>
            </div>
            @error('password')<div class="error-text">{{ $message }}</div>@enderror
          </div>

          <div class="form-options">
            <label class="remember"><input type="checkbox" name="remember" value="1" @checked(old('remember'))><span>Ingat saya</span></label>
            <a class="forgot" href="{{ route('password.request') }}">Lupa kata sandi?</a>
          </div>

          <button class="submit" type="submit" id="submit-button">Masuk</button>

          <!-- Shortcut Masuk Cepat Demo (Admin & Kasir) -->
          <div style="margin-top: 18px; padding: 12px; border-radius: 12px; background: #f0f7fb; border: 1px dashed #b9d8e8; text-align: center;">
            <div style="font-size: 11px; font-weight: 700; color: #1e5a75; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">
              ⚡ Akses Cepat Demo (1-Klik Masuk)
            </div>
            <div style="display: flex; gap: 8px;">
              <a href="{{ route('demo.switch-role', 'admin') }}" style="flex: 1; padding: 8px 10px; background: #174e65; color: #fff; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 700; display: inline-block;">
                👑 Masuk Admin
              </a>
              <a href="{{ route('demo.switch-role', 'kasir') }}" style="flex: 1; padding: 8px 10px; background: #d97706; color: #fff; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 700; display: inline-block;">
                🛒 Masuk Kasir
              </a>
            </div>
          </div>
        </form>

        <a class="register-link" href="{{ route('register') }}">Register</a>
      </div>
    </section>
  </main>
  <script>
    (() => {
      const input = document.getElementById('password');
      const toggle = document.getElementById('password-toggle');
      const form = document.getElementById('login-form');
      const submit = document.getElementById('submit-button');

      toggle.addEventListener('click', () => {
        const showing = input.type === 'password';
        input.type = showing ? 'text' : 'password';
        toggle.textContent = showing ? 'Sembunyikan' : 'Tampilkan';
        toggle.setAttribute('aria-pressed', String(showing));
      });

      form.addEventListener('submit', () => {
        submit.disabled = true;
        submit.textContent = 'Memeriksa…';
      });
    })();
  </script>
</body>
</html>
