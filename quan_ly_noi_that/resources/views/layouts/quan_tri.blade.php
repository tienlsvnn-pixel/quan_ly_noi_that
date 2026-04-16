<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản trị') - HomeStore</title>
    <style>
        :root {
            --bg: #f3efe8;
            --surface: #ffffff;
            --surface-soft: #f9f6f0;
            --sidebar: #1c2b26;
            --sidebar-soft: #243631;
            --border: #e7dfd4;
            --text: #1f2933;
            --muted: #64707d;
            --brand: #2f5d50;
            --brand-strong: #24463c;
            --danger: #b54747;
            --shadow: 0 18px 40px rgba(31, 41, 51, 0.08);
            --radius-lg: 24px;
            --radius-md: 16px;
            --radius-sm: 12px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--text);
            font-family: "Be Vietnam Pro", "Segoe UI", Tahoma, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(176, 137, 104, 0.12), transparent 24%),
                linear-gradient(180deg, #faf7f2 0%, var(--bg) 100%);
        }

        a { color: inherit; text-decoration: none; }
        button, input, select, textarea { font: inherit; }

        .admin-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
        }

        .sidebar {
            padding: 24px 18px;
            background: linear-gradient(160deg, var(--sidebar) 0%, var(--sidebar-soft) 100%);
            color: #f2f6f3;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 8px;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-weight: 700;
            letter-spacing: 0.08em;
            background: linear-gradient(145deg, #d4b08c, #8b6b4f);
            color: #fff;
        }

        .brand strong { display: block; font-size: 17px; }
        .brand span { font-size: 12px; opacity: 0.75; }

        .sidebar-nav {
            display: grid;
            gap: 8px;
            align-content: start;
        }

        .sidebar-link {
            padding: 10px 12px;
            border-radius: 12px;
            color: rgba(242, 246, 243, 0.84);
            transition: 0.2s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.14);
        }

        .sidebar-foot {
            margin-top: auto;
            display: grid;
            gap: 10px;
            padding: 8px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.08);
        }

        .date-chip {
            font-size: 13px;
            color: rgba(242, 246, 243, 0.7);
        }

        .content-area {
            padding: 24px;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr);
            gap: 18px;
        }

        .topbar {
            padding: 18px 20px;
            border: 1px solid rgba(255, 255, 255, 0.72);
            border-radius: var(--radius-lg);
            background: rgba(255, 255, 255, 0.92);
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
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
        .topbar-meta span { color: var(--muted); font-size: 13px; }

        .page-wrap {
            display: grid;
            gap: 18px;
            align-content: start;
        }

        .page-box,
        .form-card,
        .table-card,
        .info-card,
        .metric-card {
            padding: 22px;
            border-radius: var(--radius-lg);
            background: var(--surface);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .card-grid { display: grid; gap: 18px; }

        .split-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
            gap: 18px;
        }

        .stack-grid { display: grid; gap: 18px; }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }

        .metric-card {
            display: grid;
            gap: 8px;
            align-content: start;
            background: linear-gradient(180deg, #fffdf9 0%, #f6efe4 100%);
        }

        .metric-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .metric-value {
            font-size: 30px;
            line-height: 1;
            font-weight: 800;
            color: var(--brand-strong);
        }

        .metric-note {
            font-size: 13px;
            color: var(--muted);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .page-header h2 { margin: 0; }

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
            background: #fcfaf7;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus, select:focus, textarea:focus {
            border-color: rgba(47, 93, 80, 0.52);
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

        .sidebar .secondary-button {
            width: 100%;
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
        }

        .action-group { display: flex; flex-wrap: wrap; gap: 8px; }
        .table-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            color: var(--muted);
            font-size: 14px;
        }

        .table-scroll { overflow-x: auto; }

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
            background: var(--surface-soft);
        }

        .table-subtle {
            margin-top: 4px;
            font-size: 13px;
            color: var(--muted);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .detail-item {
            padding: 14px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            background: var(--surface-soft);
        }

        .detail-item strong {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .badge-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .context-note {
            padding: 14px 16px;
            border-radius: var(--radius-md);
            background: var(--surface-soft);
            color: var(--muted);
            line-height: 1.6;
        }

        .flash-stack { display: grid; gap: 10px; }

        .flash {
            padding: 14px 16px;
            border-radius: var(--radius-md);
            border: 1px solid transparent;
        }

        .flash-success {
            color: #1f5b3d;
            background: #e7f5ec;
            border-color: #cce9d7;
        }

        .flash-error {
            color: #7c2e2e;
            background: #fae8e8;
            border-color: #f2cfcf;
        }

        @media (max-width: 1180px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                gap: 14px;
            }

            .sidebar-nav {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .sidebar-foot {
                grid-template-columns: 1fr auto;
                align-items: center;
            }
        }

        @media (max-width: 900px) {
            .sidebar-nav {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .split-grid,
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .page-header,
            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .topbar-meta {
                text-align: left;
            }
        }

        @media (max-width: 640px) {
            .content-area,
            .sidebar {
                padding: 14px;
            }

            .sidebar-nav {
                grid-template-columns: 1fr;
            }

            .toolbar {
                justify-content: stretch;
            }

            .action-group,
            .toolbar,
            .search-form {
                flex-direction: column;
                align-items: stretch;
            }
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
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">HS</div>
                <div>
                    <strong>HomeStore</strong>
                    <span>Quản trị nội thất</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                @foreach($navItems as $item)
                    @php
                        $isActive = request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']);
                    @endphp
                    <a href="{{ route($item['route']) }}" class="sidebar-link {{ $isActive ? 'active' : '' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="sidebar-foot">
                <span class="date-chip">{{ now()->format('d/m/Y') }}</span>
                <a href="{{ route('logout') }}" class="secondary-button">Đăng xuất</a>
            </div>
        </aside>

        <div class="content-area">
            <header class="topbar">
                <h1>@yield('title', 'Trang quản trị')</h1>
                <div class="topbar-meta">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>Quản trị viên</span>
                </div>
            </header>

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
