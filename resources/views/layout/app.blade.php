<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', config('app.name', 'Cash.Flow'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
            background: #F6F8FA;
            color: #111827;
        }

        body {
            min-height: 100vh;
        }

        .app-shell {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .main-content {
            flex: 1;
            min-width: 0;
            padding: 24px;
            background: #F6F8FA;
            position: relative;
        }

        .main-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 24px;
            right: 24px;
            height: 4px;
            border-radius: 999px;
            background: linear-gradient(90deg, #9B72FF 0%, #C084FC 100%);
            opacity: 0.35;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
        }

        .topbar-left {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .topbar-title {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            color: #111827;
        }

        .topbar-meta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #6B7280;
            font-size: 0.95rem;
        }

        .topbar-meta .meta-separator {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 16px;
            border: 1px solid #E5E7EB;
            background: #FFFFFF;
            cursor: pointer;
            color: #1F9C8A;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.05);
        }

        .icon-btn svg {
            width: 20px;
            height: 20px;
        }

        .search-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 18px;
            padding: 8px 12px;
            min-width: 180px;
            max-width: 220px;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.05);
        }

        .search-pill svg {
            width: 16px;
            height: 16px;
        }

        .search-pill input {
            border: none;
            width: 100%;
            outline: none;
            font-size: 0.9rem;
            color: #111827;
            background: transparent;
        }

        .card {
            background: #FFFFFF;
            border-radius: 24px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.05);
            padding: 24px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
        }

        .card h3 {
            margin: 0 0 18px;
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
        }

        .card-small {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
            padding-bottom: 10px;
        }

        .card-small strong {
            display: block;
            font-size: 1.6rem;
            line-height: 1.1;
            margin-top: 4px;
        }

        .card-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 14px;
            border-radius: 999px;
            background: #F3F4F6;
            color: #111827;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .card-highlight {
            margin-top: 12px;
            border-radius: 20px;
            padding: 18px 20px;
            background: linear-gradient(135deg, #28A39E 0%, #1A877D 100%);
            color: #FFFFFF;
        }

        .card-highlight .subtitle {
            margin: 0;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
        }

        .card-highlight strong {
            display: block;
            margin: 14px 0 4px;
            font-size: 1.6rem;
            font-weight: 700;
        }

        .account-panel {
            display: grid;
            gap: 14px;
        }

        .account-card {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 16px;
            border: 1px solid rgba(255,255,255,0.22);
        }

        .account-card .account-info {
            display: grid;
            gap: 10px;
        }

        .account-card .account-type {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .account-card .account-number {
            letter-spacing: 0.12em;
            font-size: 0.95rem;
        }

        .account-card .account-balance {
            font-size: 0.95rem;
            font-weight: 700;
        }

        .account-card .card-logo {
            width: 38px;
            height: 24px;
            border-radius: 999px;
            background: linear-gradient(90deg, #F59E0B 0%, #EF4444 100%);
        }

        .account-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: rgba(255,255,255,0.8);
            font-size: 0.87rem;
        }

        .target-grid {
            display: grid;
            gap: 14px;
            margin-top: 14px;
        }

        .target-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .target-item .icon-box {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #F3F4F6;
            color: #1F9C8A;
        }

        .target-item div {
            display: grid;
            gap: 4px;
        }

        .target-item strong {
            font-size: 1rem;
            display: block;
            color: #111827;
        }

        .target-item span {
            color: #6B7280;
            font-size: 0.9rem;
        }

        .progress-ring {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 128px;
            height: 128px;
            position: relative;
        }

        .progress-ring circle {
            transition: stroke-dashoffset 0.3s ease;
        }

        .progress-ring-text {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
        }

        .secondary-list {
            display: grid;
            gap: 16px;
            margin-top: 12px;
        }

        .secondary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 16px 18px;
            border-radius: 18px;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
        }

        .secondary-item .meta {
            display: grid;
            gap: 4px;
        }

        .secondary-item .meta strong {
            font-size: 0.98rem;
            color: #111827;
        }

        .secondary-item .meta span {
            font-size: 0.86rem;
            color: #6B7280;
        }

        .secondary-item .amount {
            font-weight: 700;
            color: #111827;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 2.15fr 1fr;
            gap: 22px;
            margin-bottom: 22px;
            position: relative;
            z-index: 1;
        }

        .history-card,
        .stats-card {
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 28px;
            box-shadow: 0 18px 48px rgba(15, 23, 42, 0.06);
            padding: 24px;
        }

        .history-header,
        .stats-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            gap: 12px;
        }

        .history-header h3,
        .stats-header h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
        }

        .history-tabs {
            display: inline-flex;
            gap: 12px;
            border-radius: 999px;
            background: #F3F4F6;
            padding: 6px;
        }

        .history-tab {
            border: none;
            border-radius: 999px;
            padding: 10px 16px;
            font-size: 0.9rem;
            cursor: pointer;
            color: #6B7280;
            background: transparent;
        }

        .history-tab.active {
            background: #ECFDF5;
            color: #0F766E;
            font-weight: 700;
        }

        .transaction-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid #E5E7EB;
        }

        .transaction-item:last-child {
            border-bottom: none;
        }

        .transaction-icon {
            width: 50px;
            height: 50px;
            border-radius: 18px;
            background: #F3F4F6;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .transaction-details {
            display: grid;
            gap: 6px;
            min-width: 0;
            flex: 1;
        }

        .transaction-details strong {
            font-size: 0.98rem;
            color: #111827;
        }

        .transaction-details span {
            color: #6B7280;
            font-size: 0.9rem;
        }

        .transaction-price {
            text-align: right;
            min-width: 110px;
        }

        .transaction-price strong {
            display: block;
            font-size: 1rem;
            color: #111827;
        }

        .stats-header-left {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .stats-subtitle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #111827;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .stats-dropdown {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            background: transparent;
            padding: 4px 8px;
            color: #6B7280;
            cursor: pointer;
            border-radius: 999px;
            font-size: 0.95rem;
        }

        .stats-dropdown:hover {
            background: #F8FAFC;
        }

        .chart-legend {
            display: flex;
            justify-content: flex-end;
            gap: 18px;
            margin-bottom: 20px;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #6B7280;
            font-size: 0.9rem;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            display: inline-block;
        }

        .legend-dot.primary { background: #1F9C8A; }
        .legend-dot.secondary { background: #CBD5E1; }

        .stats-chart {
            display: grid;
            grid-template-columns: 40px 1fr;
            gap: 18px;
            align-items: end;
            min-height: 260px;
        }

        .chart-y-axis {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
            color: #9CA3AF;
            font-size: 0.78rem;
            line-height: 1.5;
            padding-top: 32px;
        }

        .chart-bars {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 12px;
            align-items: end;
        }

        .bar-group {
            display: grid;
            gap: 6px;
            align-items: end;
            justify-items: center;
        }

        .bar-stack {
            display: flex;
            gap: 6px;
            align-items: flex-end;
            height: 180px;
        }

        .bar-stack .bar {
            width: 100%;
            min-width: 12px;
            border-radius: 999px 999px 0 0;
        }

        .bar.current {
            background: #1F9C8A;
        }

        .bar.last {
            background: #E5E7EB;
        }

        .bar-label {
            font-size: 0.78rem;
            color: #6B7280;
            text-align: center;
        }

        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-top: 22px;
            position: relative;
            z-index: 1;
        }

        .breakdown-card {
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 24px;
            padding: 18px;
            display: grid;
            gap: 14px;
        }

        .breakdown-card strong {
            font-size: 1rem;
            color: #111827;
        }

        .breakdown-card span {
            color: #6B7280;
            font-size: 0.9rem;
        }

        .breakdown-value {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .breakdown-value strong {
            font-size: 1.1rem;
        }

        .breakdown-change {
            font-size: 0.9rem;
            font-weight: 700;
            color: #16A34A;
        }

        .breakdown-change.down {
            color: #DC2626;
        }

        @media (max-width: 1240px) {
            .grid-3,
            .summary-grid,
            .breakdown-grid {
                grid-template-columns: 1fr;
            }

            .main-content::before {
                left: 0;
                right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        @include('layout.navbar')
        <main class="main-content">
            @yield('content')
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function formatRupiah(value, withDecimals = true) {
                const number = Number(String(value).replace(/[^0-9,.-]+/g, '').replace(',', '.')) || 0;
                const options = {
                    minimumFractionDigits: withDecimals ? 2 : 0,
                    maximumFractionDigits: withDecimals ? 2 : 0,
                };
                return 'Rp ' + number.toLocaleString('id-ID', options);
            }

            function unformatRupiah(value) {
                if (!value) {
                    return '0';
                }
                const raw = String(value)
                    .replace(/Rp\s?/g, '')
                    .replace(/\./g, '')
                    .replace(/,/g, '.')
                    .trim();
                return raw === '' ? '0' : raw;
            }

            function bindCurrencyInput(input) {
                if (!input) return;
                input.addEventListener('focus', function() {
                    const raw = unformatRupiah(input.value);
                    input.value = raw;
                });
                input.addEventListener('blur', function() {
                    input.value = formatRupiah(input.value, true);
                });
                if (input.value) {
                    input.value = formatRupiah(input.value, true);
                }
            }

            function cleanCurrencyForm(form) {
                if (!form) return;
                form.addEventListener('submit', function() {
                    form.querySelectorAll('.currency-input').forEach(function(field) {
                        field.value = unformatRupiah(field.value);
                    });
                });
            }

            document.querySelectorAll('.currency-input').forEach(bindCurrencyInput);
            document.querySelectorAll('form').forEach(cleanCurrencyForm);
        });
    </script>
</body>
</html>
