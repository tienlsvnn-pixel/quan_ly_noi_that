<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản trị') - HomeStore</title>
    <style>
        :root {
            --bg: #f3f1ec;
            --surface: rgba(255, 255, 255, 0.92);
            --surface-strong: #ffffff;
            --border: #ddd6cb;
            --border-soft: #e8e1d8;
            --text: #1f2933;
            --muted: #6b7280;
            --brand: #2f5d50;
            --danger: #b54747;
            --shadow: 0 18px 40px rgba(31, 41, 51, 0.08);
            --radius-lg: 26px;
            --radius-sm: 12px;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(176, 137, 104, 0.12), transparent 22%),
                linear-gradient(180deg, #f8f6f1 0%, var(--bg) 100%);
        }

        a { color: inherit; text-decoration: none; }
        button, input, select, textarea { font: inherit; }

        .admin-shell { min-height: 100vh; padding: 24px; }
        .admin-frame { max-width: 1440px; margin: 0 auto; }

        .topbar {
            padding: 18px 22px;
            border: 1px solid rgba(255, 255, 255, 0.55);
            border-radius: 30px;
            background: rgba(34, 48, 43, 0.9);
            color: #f7f4ef;
            box-shadow: var(--shadow);
        }

        .topbar-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-mark {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            background: linear-gradient(135deg, #d4b08c, #8b6b4f);
            color: #fff;
            font-weight: 700;
            letter-spacing: 0.08em;
        }

        .brand h1, .page-header h2 { margin: 0; }

        .topbar-side { text-align: right; }
        .topbar-side p {
            margin: 4px 0 0;
            font-size: 13px;
            color: rgba(247, 244, 239, 0.7);
        }

        .nav-panel {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-top: 16px;
            padding: 14px 18px;
            border-radius: 24px;
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: var(--shadow);
            backdrop-filter: blur(14px);
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .nav-link {
            padding: 10px 14px;
            border-radius: 999px;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.62);
            transition: 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #fff;
            background: var(--brand);
        }

        .logout-form { margin: 0; }
        .page-wrap { margin-top: 22px; display: grid; gap: 18px; }

        .page-box,
        .form-card,
        .table-card,
        .info-card,
        .metric-card {
            padding: 22px;
            border-radius: var(--radius-lg);
            background: var(--surface-strong);
            border: 1px solid var(--border-soft);
            box-shadow: var(--shadow);
        }

        .card-grid { display: grid; gap: 18px; }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .page-header p {
            margin: 6px 0 0;
            color: var(--muted);
        }

        .toolbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            width: 100%;
        }

        .search-form input,
        .search-form select {
            min-width: 180px;
            flex: 1 1 180px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .field { display: grid; gap: 8px; }
        .field.full, .full { grid-column: 1 / -1; }
        label { font-weight: 600; font-size: 14px; }

        input, select, textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: #fcfbf8;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus, select:focus, textarea:focus {
            border-color: rgba(47, 93, 80, 0.55);
            box-shadow: 0 0 0 4px rgba(47, 93, 80, 0.12);
        }

        textarea { min-height: 120px; resize: vertical; }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 400;
            color: var(--muted);
        }

        .checkbox-row input { width: auto; margin: 0; }
        .field-error { color: var(--danger); font-size: 13px; }

        .primary-button,
        .secondary-button,
        .danger-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            padding: 10px 16px;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            transition: transform 0.2s ease, opacity 0.2s ease, background 0.2s ease;
        }

        .primary-button:hover,
        .secondary-button:hover,
        .danger-button:hover { transform: translateY(-1px); }

        .primary-button { color: #fff; background: var(--brand); }
        .secondary-button { color: var(--text); background: #ece6dd; }
        .danger-button { color: #fff; background: var(--danger); }

        .action-group { display: flex; flex-wrap: wrap; gap: 8px; }
        table { width: 100%; border-collapse: collapse; }

        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid #efe8df;
            text-align: left;
            vertical-align: top;
        }

        th {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted);
        }

        tbody tr:last-child td { border-bottom: none; }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-primary { color: #1f4d63; background: #ddeff7; }
        .badge-success { color: #1f5b3d; background: #ddf2e6; }
        .badge-warning { color: #7a5310; background: #f8e8c7; }
        .badge-danger { color: #7c2e2e; background: #f8dddd; }

        .empty-state {
            padding: 18px;
            border-radius: 16px;
            text-align: center;
            color: var(--muted);
            background: #f7f4ee;
        }

        .flash-stack { display: grid; gap: 10px; }
        .flash { padding: 14px 16px; border-radius: 16px; border: 1px solid transparent; }
        .flash-success { color: #1f5b3d; background: #e7f5ec; border-color: #cce9d7; }
        .flash-error { color: #7c2e2e; background: #fae8e8; border-color: #f2cfcf; }

        @media (max-width: 1100px) {
            .nav-panel,
            .topbar-row,
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .topbar-side { text-align: left; }
            .form-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .admin-shell { padding: 16px; }
            .nav-links { width: 100%; }
            .nav-link { flex: 1 1 calc(50% - 10px); text-align: center; }
        }
    </style>
</head>
<body>
    @php
        $navItems = [
            ['label' => 'Tổng quan', 'route' => 'admin.dashboard'],
            ['label' => 'Danh mục', 'route' => 'admin.categories.index'],
            ['label' => 'Sản phẩm', 'route' => 'admin.products.index'],
            ['label' => 'Khách hàng', 'route' => 'admin.customers.index'],
            ['label' => 'Đơn hàng', 'route' => 'admin.orders.index'],
            ['label' => 'Nhập hàng', 'route' => 'admin.purchase-receipts.index'],
            ['label' => 'Kho hàng', 'route' => 'admin.stock-movements.index'],
            ['label' => 'Nhà cung cấp', 'route' => 'admin.suppliers.index'],
            ['label' => 'Báo cáo', 'route' => 'admin.reports.index'],
        ];
    @endphp

    <div class="admin-shell">
        <div class="admin-frame">
            <header class="topbar">
                <div class="topbar-row">
                    <div class="brand">
                        <div class="brand-mark">HS</div>
                        <div><h1>HomeStore</h1></div>
                    </div>

                    <div class="topbar-side">
                        <strong>{{ auth()->user()->name }}</strong>
                        <p>{{ now()->format('d/m/Y') }}</p>
                    </div>
                </div>
            </header>

            <div class="nav-panel">
                <nav class="nav-links">
                    @foreach($navItems as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            class="nav-link {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']) ? 'active' : '' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <a href="{{ route('logout') }}" class="secondary-button">Đăng xuất</a>
            </div>

            <main class="page-wrap">
                @if(session('status') || session('success') || session('error') || $errors->any())
                    <div class="flash-stack">
                        @if(session('status'))
                            <div class="flash flash-success">{{ session('status') }}</div>
                        @endif
                        @if(session('success'))
                            <div class="flash flash-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="flash flash-error">{{ session('error') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="flash flash-error">{{ $errors->first() }}</div>
                        @endif
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
