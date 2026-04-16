<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản khách hàng</title>
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

        .register-shell {
            width: 100%;
            max-width: 560px;
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

        .validation-list {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 14px;
            font-size: 14px;
            line-height: 1.5;
            border: 1px solid rgba(181, 71, 71, 0.18);
            background: var(--danger-bg);
            color: var(--danger);
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
        .field-error { color: var(--danger); font-size: 13px; }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
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
        .ghost-button:hover { transform: translateY(-1px); }

        @media (max-width: 560px) {
            body { padding: 12px; }
            .register-shell { padding: 20px 16px; }
        }
    </style>
</head>
<body>
    <div class="register-shell">
        <div class="brand">
            <div class="brand-mark">HS</div>
            <div>
                <h1>Đăng ký tài khoản</h1>
                <p>HomeStore</p>
            </div>
        </div>

        @if($errors->any())
            <div class="validation-list">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}">
            @csrf

            <div class="field">
                <div class="field-top">
                    <label for="name">Họ và tên</label>
                </div>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Nhập họ và tên" required>
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <div class="field-top">
                    <label for="email">Email</label>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Nhập email" required>
                @error('email')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <div class="field-top">
                    <label for="password">Mật khẩu</label>
                </div>
                <input id="password" type="password" name="password" placeholder="Nhập mật khẩu" required>
                @error('password')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <div class="field-top">
                    <label for="password_confirmation">Xác nhận mật khẩu</label>
                </div>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
            </div>

            <button type="submit" class="submit-button">Đăng ký</button>
            <a href="{{ route('login') }}" class="ghost-button">Đã có tài khoản? Đăng nhập</a>
        </form>
    </div>
</body>
</html>
