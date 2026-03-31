<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Khách hàng') - HomeStore</title>
    <style>
        :root {
            --bg: #f5f2ec;
            --surface: #ffffff;
            --surface-soft: #fcfaf7;
            --border: #e6ddd1;
            --text: #1f2933;
            --muted: #6b7280;
            --brand: #2f5d50;
            --brand-soft: #e5f0ec;
            --warning: #9a6a18;
            --warning-soft: #f8ecd6;
            --success-soft: #e3f3ea;
            --danger-soft: #fae8e8;
            --radius-lg: 22px;
            --radius-sm: 12px;
            --shadow: 0 14px 30px rgba(31, 41, 51, 0.08);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(176, 137, 104, 0.13), transparent 24%),
                linear-gradient(180deg, #f9f7f3 0%, var(--bg) 100%);
        }

        a { color: inherit; text-decoration: none; }
        button, input, select, textarea { font: inherit; }

        .shell { min-height: 100vh; padding: 22px; }
        .frame { max-width: 1280px; margin: 0 auto; }

        .topbar {
            border-radius: 28px;
            background: rgba(34, 48, 43, 0.92);
            color: #f7f4ef;
            padding: 18px 22px;
            box-shadow: var(--shadow);
        }

        .topbar-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-weight: 700;
            letter-spacing: 0.08em;
            background: linear-gradient(135deg, #d4b08c, #8b6b4f);
            color: #fff;
        }

        .nav-panel {
            margin-top: 14px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.92);
            padding: 12px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .nav-link {
            padding: 9px 14px;
            border-radius: 999px;
            color: var(--muted);
            background: #f3eee7;
            transition: 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #fff;
            background: var(--brand);
        }

        .page-wrap { margin-top: 20px; display: grid; gap: 16px; }
        .card, .table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }

        .page-header h2 { margin: 0; }
        .page-header p { margin: 6px 0 0; color: var(--muted); }

        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-success { background: var(--success-soft); color: #1f5b3d; }
        .badge-warning { background: var(--warning-soft); color: var(--warning); }
        .badge-primary { background: var(--brand-soft); color: #1f4d63; }

        .button {
            border: none;
            border-radius: 999px;
            min-height: 40px;
            padding: 9px 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s ease;
        }

        .button:hover { transform: translateY(-1px); }
        .button-primary { background: var(--brand); color: #fff; }
        .button-soft { background: #ece6dd; color: var(--text); }

        .flash {
            border-radius: 14px;
            padding: 12px 14px;
            border: 1px solid transparent;
        }

        .flash-success { background: var(--success-soft); border-color: #cae7d9; color: #1f5b3d; }
        .flash-error { background: var(--danger-soft); border-color: #f2cfcf; color: #7c2e2e; }

        table { width: 100%; border-collapse: collapse; }
        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #efe8df;
            text-align: left;
        }
        th { color: var(--muted); font-size: 13px; }
        tbody tr:last-child td { border-bottom: none; }

        .empty-state {
            text-align: center;
            padding: 16px;
            border-radius: 14px;
            color: var(--muted);
            background: var(--surface-soft);
        }

        @media (max-width: 900px) {
            .topbar-row,
            .nav-panel,
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .shell { padding: 14px; }
        }
    </style>
</head>
<body>
    @php
        $navItems = [
            ['label' => 'Tổng quan', 'route' => 'customer.dashboard'],
            ['label' => 'Sản phẩm', 'route' => 'customer.products.index'],
            ['label' => 'Đơn hàng của tôi', 'route' => 'customer.orders.index'],
            ['label' => 'Đặt hàng mới', 'route' => 'customer.orders.create'],
        ];
    @endphp

    <div class="shell">
        <div class="frame">
            <header class="topbar">
                <div class="topbar-row">
                    <div class="brand">
                        <div class="brand-mark">HS</div>
                        <div>
                            <strong>HomeStore</strong>
                            <div style="font-size:13px; opacity:0.75;">Khu vực khách hàng</div>
                        </div>
                    </div>
                    <div>
                        <strong>{{ auth()->user()->name }}</strong>
                    </div>
                </div>
            </header>

            <div class="nav-panel">
                <nav class="nav-links">
                    @foreach($navItems as $item)
                        <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']) ? 'active' : '' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <a href="{{ route('logout') }}" class="button button-soft">Đăng xuất</a>
            </div>

            <main class="page-wrap">
                @if(session('status'))
                    <div class="flash flash-success">{{ session('status') }}</div>
                @endif
                @if(session('error'))
                    <div class="flash flash-error">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="flash flash-error">{{ $errors->first() }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
