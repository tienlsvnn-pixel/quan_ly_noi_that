<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <style>
        :root {
            --bg-start: #f7f3ec;
            --bg-end: #e9dfd2;
            --panel: rgba(255, 255, 255, 0.92);
            --panel-border: rgba(92, 74, 56, 0.14);
            --text: #1f2937;
            --muted: #64707d;
            --primary: #2f5d50;
            --danger: #b54747;
            --danger-bg: #fde8e8;
            --success: #2f7d57;
            --success-bg: rgba(47, 125, 87, 0.12);
            --input: #fcfaf7;
            --shadow: 0 28px 60px rgba(31, 41, 51, 0.12);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            font-family: "Be Vietnam Pro", "Segoe UI", Tahoma, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(176, 137, 104, 0.18), transparent 28%),
                radial-gradient(circle at bottom right, rgba(47, 93, 80, 0.12), transparent 24%),
                linear-gradient(145deg, var(--bg-start), var(--bg-end));
        }

        .login-shell {
            width: 100%;
            max-width: 520px;
            padding: 28px;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.72);
            background: var(--panel);
            box-shadow: var(--shadow);
            backdrop-filter: blur(14px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.08em;
            background: linear-gradient(145deg, var(--primary), #3f7c6a);
            box-shadow: 0 12px 28px rgba(47, 93, 80, 0.22);
        }

        .brand h1 {
            margin: 0;
            font-size: 22px;
        }

        .brand p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .alert,
        .validation-list {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 14px;
            font-size: 14px;
            line-height: 1.5;
            border: 1px solid transparent;
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success);
            border-color: rgba(47, 125, 87, 0.14);
        }

        .alert-danger,
        .validation-list {
            background: var(--danger-bg);
            color: var(--danger);
            border-color: rgba(181, 71, 71, 0.18);
        }

        .validation-list ul { padding-left: 18px; }

        form { display: grid; gap: 16px; }

        .field { display: grid; gap: 8px; }

        .field-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .field-top label { font-weight: 600; }
        .caps-lock, .field-error { font-size: 13px; }
        .caps-lock { display: none; color: var(--danger); }
        .field-error { color: var(--danger); }

        .input-wrap { position: relative; }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 13px 14px;
            border-radius: 14px;
            border: 1px solid var(--panel-border);
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
            right: 8px;
            transform: translateY(-50%);
            min-width: 54px;
            border: none;
            border-radius: 11px;
            padding: 8px 10px;
            background: #ece6dd;
            color: #4b5563;
            cursor: pointer;
        }

        .utility-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .checkbox {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 14px;
        }

        .checkbox input { width: auto; }

        .forgot-link {
            color: var(--primary);
            font-size: 14px;
            font-weight: 600;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .submit-button,
        .ghost-button {
            border: none;
            border-radius: 14px;
            cursor: pointer;
            transition: 0.2s ease;
            min-height: 46px;
            font-weight: 600;
        }

        .submit-button {
            background: var(--primary);
            color: #fff;
        }

        .ghost-button {
            background: #ece6dd;
            color: #4b5563;
            text-decoration: none;
            display: grid;
            place-items: center;
        }

        .submit-button:hover,
        .ghost-button:hover,
        .field-action:hover { transform: translateY(-1px); }

        .submit-button:disabled {
            opacity: 0.72;
            cursor: progress;
        }

        @media (max-width: 560px) {
            body { padding: 12px; }
            .login-shell { padding: 20px 16px; }
            .utility-row { align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <div class="brand">
            <div class="brand-mark">HS</div>
            <div>
                <h1>Đăng nhập</h1>
                <p>HomeStore</p>
            </div>
        </div>

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
                    <label for="email">Email</label>
                </div>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Nhập email"
                    autocomplete="username"
                    class="@error('email') is-invalid @enderror"
                    required
                >
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
                <a href="{{ route('password.request') }}" class="forgot-link">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="submit-button" id="submitButton">
                <span id="submitLabel">Đăng nhập</span>
            </button>

            <a href="{{ route('register') }}" class="ghost-button">Chưa có tài khoản? Đăng ký</a>
        </form>
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
