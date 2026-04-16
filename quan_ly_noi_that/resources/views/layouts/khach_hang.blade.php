<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Khách hàng') - HomeStore</title>
    <style>
        :root {
            --bg: #f5f1e9;
            --surface: #ffffff;
            --surface-soft: #f9f6f1;
            --sidebar: #20312c;
            --sidebar-soft: #2a3f39;
            --border: #e7dfd3;
            --text: #1f2933;
            --muted: #64707d;
            --brand: #2f5d50;
            --danger: #b54747;
            --shadow: 0 16px 34px rgba(31, 41, 51, 0.08);
            --radius-lg: 22px;
            --radius-md: 16px;
            --radius-sm: 12px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--text);
            font-family: "Be Vietnam Pro", "Segoe UI", Tahoma, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(176, 137, 104, 0.14), transparent 24%),
                linear-gradient(180deg, #faf7f2 0%, var(--bg) 100%);
        }

        a { color: inherit; text-decoration: none; }
        button, input, select, textarea { font: inherit; }

        .shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 250px minmax(0, 1fr);
        }

        .sidebar {
            padding: 22px 16px;
            color: #f2f6f3;
            background: linear-gradient(160deg, var(--sidebar) 0%, var(--sidebar-soft) 100%);
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            display: grid;
            place-items: center;
            font-weight: 700;
            letter-spacing: 0.08em;
            background: linear-gradient(145deg, #d4b08c, #8b6b4f);
            color: #fff;
        }

        .brand strong { display: block; font-size: 17px; }
        .brand span { font-size: 12px; opacity: 0.78; }

        .nav-links {
            display: grid;
            gap: 8px;
        }

        .nav-link {
            padding: 10px 12px;
            border-radius: 12px;
            color: rgba(242, 246, 243, 0.84);
            transition: 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.14);
        }

        .sidebar-foot {
            margin-top: auto;
            display: grid;
            gap: 10px;
            padding: 8px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-foot .button-soft {
            width: 100%;
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
        }

        .content {
            padding: 22px;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr);
            gap: 16px;
        }

        .topbar {
            padding: 16px 18px;
            border-radius: var(--radius-lg);
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.74);
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .topbar h1 {
            margin: 0;
            font-size: 22px;
        }

        .topbar-meta {
            text-align: right;
            display: grid;
            gap: 3px;
        }

        .topbar-meta strong { font-size: 15px; }
        .topbar-meta span { font-size: 13px; color: var(--muted); }

        .page-wrap {
            display: grid;
            gap: 16px;
            align-content: start;
        }

        .card,
        .table-card {
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

        .badge-success { background: #e3f3ea; color: #1f5b3d; }
        .badge-warning { background: #f8ecd6; color: #9a6a18; }
        .badge-primary { background: #e5f0ec; color: #1f4d63; }

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

        .flash-success {
            background: #e3f3ea;
            border-color: #cae7d9;
            color: #1f5b3d;
        }

        .flash-error {
            background: #fae8e8;
            border-color: #f2cfcf;
            color: #7c2e2e;
        }

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

        @media (max-width: 980px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                gap: 12px;
            }

            .nav-links {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .sidebar-foot {
                grid-template-columns: 1fr auto;
                align-items: center;
            }

            .topbar,
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .topbar-meta { text-align: left; }
        }

        @media (max-width: 640px) {
            .content,
            .sidebar {
                padding: 14px;
            }

            .nav-links {
                grid-template-columns: 1fr;
            }
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
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">HS</div>
                <div>
                    <strong>HomeStore</strong>
                    <span>Khu vực khách hàng</span>
                </div>
            </div>

            <nav class="nav-links">
                @foreach($navItems as $item)
                    @php
                        $isActive = request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']);
                    @endphp
                    <a href="{{ route($item['route']) }}" class="nav-link {{ $isActive ? 'active' : '' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="sidebar-foot">
                <span style="font-size:13px; opacity:0.75;">{{ now()->format('d/m/Y') }}</span>
                <a href="{{ route('logout') }}" class="button button-soft">Đăng xuất</a>
            </div>
        </aside>

        <div class="content">
            <header class="topbar">
                <h1>@yield('title', 'Trang khách hàng')</h1>
                <div class="topbar-meta">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>Khách hàng</span>
                </div>
            </header>

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
