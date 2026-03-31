<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập quản trị</title>
    <style>
        :root {
            --bg-start: #f7f3ec;
            --bg-end: #e9dfd2;
            --panel: rgba(255, 255, 255, 0.9);
            --panel-border: rgba(92, 74, 56, 0.12);
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #2f5d50;
            --accent-soft: rgba(176, 137, 104, 0.12);
            --success: #2f7d57;
            --success-bg: rgba(47, 125, 87, 0.12);
            --danger: #b54747;
            --danger-bg: #fde8e8;
            --warning: #9a6a18;
            --warning-bg: rgba(194, 135, 44, 0.14);
            --input: #fcfaf7;
            --shadow: 0 30px 70px rgba(31, 41, 51, 0.12);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(176, 137, 104, 0.18), transparent 28%),
                radial-gradient(circle at bottom right, rgba(47, 93, 80, 0.12), transparent 24%),
                linear-gradient(145deg, var(--bg-start), var(--bg-end));
        }

        .login-shell {
            width: 100%;
            max-width: 1120px;
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            overflow: hidden;
            border-radius: 32px;
            border: 1px solid rgba(255, 255, 255, 0.72);
            background: rgba(255, 255, 255, 0.58);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
        }

        .showcase {
            position: relative;
            padding: 42px;
            display: flex;
            align-items: flex-end;
            background:
                linear-gradient(160deg, rgba(34, 48, 43, 0.95), rgba(47, 93, 80, 0.9)),
                linear-gradient(120deg, #20312c, #2f5d50);
            color: #f7f4ef;
        }

        .showcase::before,
        .showcase::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            pointer-events: none;
        }

        .showcase::before {
            width: 240px;
            height: 240px;
            top: -70px;
            right: -70px;
        }

        .showcase::after {
            width: 180px;
            height: 180px;
            bottom: -50px;
            left: -50px;
        }

        .showcase-box {
            position: relative;
            z-index: 1;
            width: 100%;
            padding: 24px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .showcase-mark {
            width: 56px;
            height: 56px;
            display: grid;
            place-items: center;
            border-radius: 18px;
            background: linear-gradient(145deg, #d4b08c, #8b6b4f);
            color: #fff;
            font-weight: 700;
            letter-spacing: 0.08em;
            margin-bottom: 18px;
        }

        .showcase-box strong { display: block; font-size: 24px; margin-bottom: 6px; }
        .showcase-box span { color: rgba(247, 244, 239, 0.76); }

        .login-panel {
            padding: 42px;
            background: var(--panel);
            border-left: 1px solid var(--panel-border);
            display: flex;
            align-items: center;
        }

        .login-card { width: 100%; }

        .brand-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 28px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-mark {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(145deg, var(--primary), #3f7c6a);
            box-shadow: 0 12px 28px rgba(47, 93, 80, 0.22);
        }

        .brand h2 { font-size: 17px; }

        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: #7a5c44;
            font-size: 13px;
            font-weight: 600;
        }

        .login-card h3 { font-size: 30px; margin-bottom: 22px; }

        .alert,
        .validation-list {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 16px;
            font-size: 14px;
            line-height: 1.6;
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success);
            border: 1px solid rgba(47, 125, 87, 0.12);
        }

        .alert-danger {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid rgba(181, 71, 71, 0.12);
        }

        .validation-list {
            background: var(--warning-bg);
            color: var(--warning);
            border: 1px solid rgba(194, 135, 44, 0.16);
        }

        .validation-list ul { padding-left: 18px; }
        form { display: grid; gap: 18px; }
        .field { display: grid; gap: 9px; }

        .field-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .field-top label { font-weight: 600; }
        .hint, .caps-lock, .field-error { font-size: 13px; }
        .hint, .caps-lock { color: var(--muted); }
        .caps-lock { display: none; color: var(--danger); }
        .field-error { color: var(--danger); }

        .input-wrap { position: relative; }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid rgba(92, 74, 56, 0.16);
            background: var(--input);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus {
            border-color: rgba(47, 93, 80, 0.45);
            box-shadow: 0 0 0 4px rgba(47, 93, 80, 0.12);
        }

        .field-action {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            min-width: 54px;
            border: none;
            border-radius: 12px;
            padding: 9px 12px;
            background: #ece6dd;
            color: #4b5563;
            cursor: pointer;
        }

        .utility-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .checkbox {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-size: 14px;
        }

        .checkbox input { width: auto; }

        .ghost-button,
        .submit-button {
            border: none;
            border-radius: 16px;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .ghost-button {
            padding: 12px 14px;
            background: #ece6dd;
            color: #4b5563;
        }

        .submit-button {
            padding: 15px 18px;
            background: var(--primary);
            color: #fff;
            font-weight: 600;
        }

        .submit-button:hover,
        .ghost-button:hover,
        .field-action:hover { transform: translateY(-1px); }

        .submit-button:disabled {
            opacity: 0.7;
            cursor: progress;
        }

        .helper-panel {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 14px;
            margin-top: 22px;
        }

        .helper-card {
            padding: 16px;
            border-radius: 18px;
            background: #f7f4ef;
            border: 1px solid rgba(92, 74, 56, 0.1);
        }

        .helper-card strong {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .helper-card span {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        @media (max-width: 980px) {
            .login-shell { grid-template-columns: 1fr; }
            .showcase, .login-panel { padding: 30px 24px; }
        }

        @media (max-width: 640px) {
            body { padding: 14px; }
            .showcase, .login-panel { padding: 22px 18px; }
            .brand-row, .utility-row { flex-direction: column; align-items: flex-start; }
            .login-card h3 { font-size: 26px; }
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <section class="showcase">
            <div class="showcase-box">
                <div class="showcase-mark">HS</div>
                <strong>HomeStore</strong>
                <span>Admin</span>
            </div>
        </section>

        <section class="login-panel">
            <div class="login-card">
                <div class="brand-row">
                    <div class="brand">
                        <div class="brand-mark">QN</div>
                        <div><h2>Quản lý nội thất</h2></div>
                    </div>
                    <div class="status-chip">Sẵn sàng truy cập</div>
                </div>

                <h3>Đăng nhập hệ thống</h3>

                @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if($errors->has('email') && !$errors->has('password') && $errors->count() === 1)
                    <div class="alert alert-danger">{{ $errors->first('email') }}</div>
                @endif

                @if($errors->any() && !($errors->has('email') && !$errors->has('password') && $errors->count() === 1))
                    <div class="validation-list">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}" id="loginForm" novalidate>
                    @csrf

                    <div class="field">
                        <div class="field-top">
                            <label for="email">Địa chỉ email</label>
                            <span class="hint">Sử dụng tài khoản được cấp quyền</span>
                        </div>
                        <div class="input-wrap">
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Nhập email quản trị"
                                autocomplete="username"
                                class="@error('email') is-invalid @enderror"
                                required
                            >
                        </div>
                        @error('email')
                            @if($message !== 'Email hoặc mật khẩu không chính xác.')
                                <div class="field-error">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>

                    <div class="field">
                        <div class="field-top">
                            <label for="password">Mật khẩu</label>
                            <span class="caps-lock" id="capsLockHint">Caps Lock đang bật</span>
                        </div>
                        <div class="input-wrap">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Nhập mật khẩu"
                                autocomplete="current-password"
                                class="@error('password') is-invalid @enderror"
                                required
                            >
                            <button type="button" class="field-action" id="togglePassword">Hiện</button>
                        </div>
                        @error('password')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="utility-row">
                        <label class="checkbox" for="remember">
                            <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                            <span>Ghi nhớ đăng nhập</span>
                        </label>
                    </div>

                    <button type="submit" class="submit-button" id="submitButton">
                        <span id="submitLabel">Đăng nhập hệ thống</span>
                    </button>
                </form>

                <div class="helper-panel">
                    <div class="helper-card">
                        <strong>Lưu ý bảo mật</strong>
                        <span>Không chia sẻ thông tin đăng nhập. Nếu quên mật khẩu, vui lòng liên hệ quản trị hệ thống để được cấp lại.</span>
                    </div>
                    <a href="{{ route('register') }}" class="ghost-button" style="display:grid; place-items:center; text-decoration:none;">
                        Chưa có tài khoản? Đăng ký ngay
                    </a>
                </div>
            </div>
        </section>
    </div>

    <script>
        (function () {
            const passwordInput = document.getElementById('password');
            const togglePasswordButton = document.getElementById('togglePassword');
            const submitButton = document.getElementById('submitButton');
            const submitLabel = document.getElementById('submitLabel');
            const capsLockHint = document.getElementById('capsLockHint');
            const loginForm = document.getElementById('loginForm');

            togglePasswordButton.addEventListener('click', function () {
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                togglePasswordButton.textContent = isHidden ? 'Ẩn' : 'Hiện';
                passwordInput.focus();
            });

            passwordInput.addEventListener('keyup', function (event) {
                capsLockHint.style.display = event.getModifierState('CapsLock') ? 'inline' : 'none';
            });

            passwordInput.addEventListener('keydown', function (event) {
                capsLockHint.style.display = event.getModifierState('CapsLock') ? 'inline' : 'none';
            });

            loginForm.addEventListener('submit', function () {
                submitButton.disabled = true;
                submitLabel.textContent = 'Đang đăng nhập...';
            });
        })();
    </script>
</body>
</html>
