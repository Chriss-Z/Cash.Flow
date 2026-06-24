@extends('layout.app')

@section('title', 'Cash.Flow | Beranda')

@section('content')

<div class="topbar">
    <div class="topbar-left">
        <h1 class="topbar-title">Hi {{ auth()->user()->name ?? 'Chris' }}</h1>
        <div class="topbar-meta">
            <span>{{ now()->format('M d, Y') }}</span>
            <span class="meta-separator">•</span>
            <span>Welcome back to your finance dashboard</span>
        </div>
    </div>
</div>

@if(session('status') || session('error'))
<div class="notification-card {{ session('error') ? 'error' : 'success' }}">
    <strong>{{ session('status') ? 'Berhasil' : 'Pemberitahuan' }}</strong>
    <p>{{ session('status') ?? session('error') }}</p>
</div>
@endif

<div class="grid-3">
    <div class="card">
        <div class="card-small">
            <div>
                <h3>Total Saldo</h3>
                <strong>Rp {{ number_format($totalSaldo,0,',','.') }}</strong>
            </div>
            <span class="card-badge">All Accounts</span>
        </div>

        <div class="card-highlight account-panel">
            <div class="account-hero">
                <div>
                    <p class="account-hero-subtitle">Account Type</p>
                    <strong class="account-hero-title" id="account-type-text">{{ data_get($accounts->first(), 'type', 'Debit Card') }}</strong>
                    <span class="account-hero-label" id="account-title-text">{{ data_get($accounts->first(), 'title', 'Paspor blue debit') }}</span>
                </div>
                <div class="account-status">All Accounts</div>
            </div>

            <div class="account-card-panel">
                <div class="account-card-content">
                    <span class="account-number" id="account-number">{{ data_get($accounts->first(), 'number', '**** **** **** 2598') }}</span>
                    <div class="account-amount"><span class="account-currency-icon" id="account-currency-icon">{{ data_get($accounts->first(), 'currency_icon', '') }}</span><span id="account-amount-text">{{ data_get($accounts->first(), 'formatted', 'Rp 0') }}</span></div>
                </div>
                <span class="account-chip"></span>
            </div>

            <div class="account-footer">
                <button type="button" class="account-nav" id="prev-account-btn">Previous</button>
                <button type="button" class="account-nav" id="next-account-btn">Next</button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-small" style="display:flex; align-items:center; justify-content:space-between; gap:16px;">
            <div>
                <h3>Target</h3>
                <strong id="target-amount">Rp {{ number_format(optional($target)->target_amount ?? 0,0,',','.') }}</strong>
            </div>
            <a href="{{ route('target') }}" class="edit-target-btn">Kelola</a>
        </div>

        <div class="target-grid">
            <div class="target-item">
                <span class="icon-box">🏆</span>
                <div>
                    <span>Target Pencapaian</span>
                    <strong id="target-achievement">Rp {{ number_format($progress,0,',','.') }}</strong>
                </div>
            </div>
            <div class="target-item">
                <span class="icon-box">⏱</span>
                <div>
                    <span>Target Bulanan</span>
                    <strong id="target-monthly">Rp {{ number_format(optional($target)->target_amount ?? 0,0,',','.') }}</strong>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px; display: flex; align-items: center; justify-content: space-between; gap: 18px;">
            <div>
                <span style="font-size: 0.92rem; color: #6B7280;">Target vs Pencapaian</span>
            </div>
            <div class="progress-ring">
                <svg width="128" height="128" viewBox="0 0 128 128" aria-hidden="true">
                    <circle cx="64" cy="64" r="52" stroke="#E5E7EB" stroke-width="12" fill="none" />
                    <circle id="progress-ring-circle" cx="64" cy="64" r="52" stroke="#1F9C8A" stroke-width="12" fill="none"
                        stroke-dasharray="326.72"
                        stroke-dashoffset="{{ 326.72 - (326.72 * min(round($percentage), 100) / 100) }}"
                        transform="rotate(-90 64 64)" />
                </svg>
                <div class="progress-ring-text" id="progress-ring-text">{{ round($progress / 1000) }}K</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-small">
            <div>
                <h3>Tagihan</h3>
            </div>
            <a href="#" style="font-size: 0.9rem; color: #1F9C8A; text-decoration: none;">View All</a>
        </div>

        <div class="secondary-list">
            @forelse($bills ?? [] as $bill)
            <div class="secondary-item">
                <div class="meta">
                    <strong>{{ $bill['title'] }}</strong>
                    <span>{{ $bill['date'] }} · Last Charge - {{ $bill['last_charge'] }}</span>
                </div>
                <div class="amount">Rp {{ number_format($bill['amount'], 0, ',', '.') }}</div>
            </div>
            @empty
            <div class="empty-state">
                <p>Belum ada tagihan.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<div class="summary-grid">
    <section class="history-card">
        <div class="history-header">
            <h3>Riwayat Transaksi</h3>
            <a href="#" style="font-size: 0.9rem; color: #1F9C8A; text-decoration: none;">View All</a>
        </div>
        <div class="history-tabs">
            <button class="history-tab active">Semua</button>
            <button class="history-tab">Pendapatan</button>
            <button class="history-tab">Pengeluaran</button>
        </div>

        <div style="margin-top: 18px; display: grid; gap: 18px;">
            @foreach($transactions ?? [] as $trx)
            <div class="transaction-item">
                <div class="transaction-icon">{{ $trx->type == 'income' ? '💰' : '💸' }}</div>
                <div class="transaction-details">
                    <strong>{{ $trx->description }}</strong>
                    <span>{{ $trx->date }}</span>
                </div>
                <div class="transaction-price">
                    <strong>Rp {{ number_format($trx->amount,0,',','.') }}</strong>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <section class="stats-card">
        <div class="stats-header">
            <div class="stats-header-left">
                <h3>Statistik</h3>
                <div class="stats-subtitle" style="display:flex; align-items:center; justify-content:space-between; gap:16px;">
                    <div>
                        <span id="chart-period-title">{{ $chartData['weekly']['title'] }}</span>
                        <p id="chart-period-subtitle" style="margin:4px 0 0; color:#6B7280; font-size:.95rem;">{{ $chartData['weekly']['subtitle'] }}</p>
                    </div>
                    <select id="chart-period-select" class="stats-dropdown" aria-label="Pilih periode">
                        <option value="weekly">Mingguan</option>
                        <option value="monthly">Bulanan</option>
                        <option value="yearly">Tahunan</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="chart-summary" style="display:flex; gap:22px; margin-top:16px;">
            <div style="display:flex; flex-direction:column; gap:6px;">
                <span style="font-size:.85rem; color:#6B7280;">Total Saat Ini</span>
                <strong id="chart-total-current">Rp {{ number_format($chartData['weekly']['total'],0,',','.') }}</strong>
            </div>
            <div style="display:flex; flex-direction:column; gap:6px;">
                <span style="font-size:.85rem; color:#6B7280;">Total Sebelumnya</span>
                <strong id="chart-total-previous">Rp {{ number_format($chartData['weekly']['previous_total'],0,',','.') }}</strong>
            </div>
        </div>

        <div class="chart-legend">
            <div class="legend-item"><span class="legend-dot primary"></span>Periode Saat Ini</div>
            <div class="legend-item"><span class="legend-dot secondary"></span>Periode Sebelumnya</div>
        </div>

        <div class="stats-chart">
            <div class="chart-y-axis" id="chart-y-axis">
                <span>Rp 200.000</span>
                <span>Rp 150.000</span>
                <span>Rp 100.000</span>
                <span>Rp 50.000</span>
                <span>Rp 0</span>
            </div>
            <div class="chart-bars" id="chart-bars">
                @php
                $items = $chartData['weekly']['items'] ?? [];
                $defaultMax = !empty($items)
                ? max(array_column($items, 'amount'))
                : 1;
                @endphp
                @forelse($items as $item)
                <div class="bar-group">
                    <div class="bar-stack">
                        <div class="bar current" data-value="{{ $item['amount'] }}"
                            style="height: {{ ($item['amount'] / $defaultMax) * 100 }}%;">
                        </div>
                    </div>
                    <span class="bar-label">{{ $item['label'] }}</span>
                </div>
                @empty
                <div class="empty-chart">
                    Tidak ada data pengeluaran
                </div>
                @endforelse
            </div>
        </div>
    </section>
</div>

<div class="breakdown-grid">
    @if(!empty($breakdowns) && count($breakdowns) > 0)
    @foreach($breakdowns as $item)
    <div class="breakdown-card">
        <div class="breakdown-value">
            <strong>{{ $item['label'] }}</strong>
            <strong>Rp {{ number_format($item['amount'], 0, ',', '.') }}</strong>

        </div>
        <span>
            {{ $item['change'] ?? 0 }}
            <span class="breakdown-change{{ ($item['change_direction'] ?? 'up') === 'down' ? ' down' : '' }}">
                {{ ($item['change_direction'] ?? 'up') === 'down' ? '↓' : '↑' }}
            </span>
        </span>
    </div>
    @endforeach
    @else
    <div class="empty-breakdown">
        <p>Belum ada ringkasan pengeluaran.</p>
    </div>
    @endif
</div>

<style>
    .balance-card {
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 24px;
        box-shadow: 0 18px 48px rgba(15, 23, 42, 0.06);
        padding: 24px;
        display: grid;
        gap: 20px;
    }

    .highlight-card {
        min-height: 360px;
    }

    .balance-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .balance-subtitle {
        margin: 0;
        font-size: 0.9rem;
        color: #6B7280;
    }

    .balance-title {
        margin: 6px 0 0;
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
    }

    .brand-badge {
        width: 48px;
        height: 28px;
        border-radius: 999px;
        background: linear-gradient(90deg, #F59E0B 0%, #EF4444 100%);
    }

    .cash-icon {
        background: linear-gradient(90deg, #34D399 0%, #10B981 100%);
    }

    .balance-number {
        font-size: 1.2rem;
        font-weight: 700;
        color: #111827;
        letter-spacing: 0.08em;
    }

    .balance-info,
    .balance-label {
        margin: 0;
        color: #6B7280;
        font-size: 0.9rem;
    }

    .balance-total {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
    }

    .balance-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .button-link {
        background: transparent;
        border: none;
        color: #10B981;
        font-weight: 700;
        cursor: pointer;
        padding: 0;
    }

    .button-link:hover {
        text-decoration: underline;
    }

    .empty-state,
    .empty-breakdown {
        padding: 24px;
        border: 1px dashed #D1D5DB;
        border-radius: 18px;
        text-align: center;
        color: #6B7280;
        background: #F9FAFB;
    }

    .empty-breakdown {
        grid-column: span 3;
    }

    .account-panel {
        background: linear-gradient(135deg, #1B9C86 0%, #2EA69E 100%);
        color: #FFFFFF;
        border-radius: 26px;
        padding: 20px 22px 18px;
        min-height: 240px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .account-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .account-hero-subtitle {
        margin: 0 0 8px;
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.82rem;
    }

    .account-hero-title {
        margin: 0;
        font-size: 1.75rem;
        line-height: 1.05;
        letter-spacing: -0.03em;
        display: block;
    }

    .account-hero-label {
        display: block;
        margin-top: 4px;
        color: rgba(255, 255, 255, 0.82);
        font-size: 0.88rem;
        font-weight: 500;
    }

    .account-status {
        background: rgba(255, 255, 255, 0.16);
        color: #FFFFFF;
        border-radius: 999px;
        padding: 10px 16px;
        font-size: 0.9rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .account-amount {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 1.4rem;
        font-weight: 700;
        color: #FFFFFF;
    }

    .account-currency-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.16);
        color: #FFFFFF;
        font-size: 0.95rem;
    }

    .notification-card {
        display: flex;
        align-items: center;
        gap: 18px;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 24px;
        padding: 18px 22px;
        margin-bottom: 18px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
    }

    .notification-card.success {
        border-color: #D1FAE5;
    }

    .notification-card.error {
        border-color: #FECACA;
    }

    .notification-trigger {
        position: relative;
        z-index: 9999;
    }

    .notification-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 18px;
        height: 18px;
        border-radius: 999px;
        background: #EF4444;
        color: #FFFFFF;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    .notification-menu {
        position: absolute;
        top: 62px;
        right: 0;
        width: 320px;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 18px;
        box-shadow: 0 18px 48px rgba(15, 23, 42, 0.12);
        padding: 16px;
        z-index: 9998;
    }

    .notification-menu-header {
        border-bottom: 1px solid #E5E7EB;
        padding-bottom: 10px;
        margin-bottom: 12px;
        font-size: 0.95rem;
        color: #111827;
    }

    .notification-row {
        padding: 12px 0;
        border-bottom: 1px solid #F3F4F6;
        color: #374151;
        font-size: 0.94rem;
    }

    .notification-row:last-child {
        border-bottom: none;
    }

    .notification-row.empty {
        color: #6B7280;
        text-align: center;
    }

    .notification-thumb {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: #F3F4F6;
        font-size: 1.3rem;
    }

    .search-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 18px;
        padding: 8px 10px;
        min-width: 220px;
        max-width: 260px;
    }

    .search-pill input {
        border: none;
        width: 100%;
        outline: none;
        font-size: 0.95rem;
        color: #111827;
        background: transparent;
    }

    .search-button {
        border: none;
        background: #1F9C8A;
        color: #FFFFFF;
        border-radius: 14px;
        padding: 8px 12px;
        cursor: pointer;
        font-weight: 700;
    }

    .search-feedback {
        margin-top: 10px;
        color: #92400E;
        font-size: 0.92rem;
        line-height: 1.4;
        min-height: 1.4rem;
    }

    .search-empty {
        grid-column: span 3;
        padding: 16px 18px;
        border-radius: 18px;
        background: #FEF3C7;
        color: #92400E;
        font-weight: 600;
        margin-bottom: 18px;
    }

    .account-card-panel {
        margin-top: 20px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 22px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        backdrop-filter: blur(10px);
    }

    .account-card-content {
        display: grid;
        gap: 10px;
    }

    .account-number {
        font-size: 1rem;
        letter-spacing: 0.18em;
        color: rgba(255, 255, 255, 0.85);
    }

    .account-amount {
        font-size: 1.2rem;
        font-weight: 700;
        color: #FFFFFF;
    }

    .account-chip {
        width: 36px;
        height: 24px;
        border-radius: 999px;
        background: linear-gradient(135deg, #F59E0B 0%, #EF4444 100%);
    }

    .account-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 18px;
    }

    .account-nav {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.38);
        color: #FFFFFF;
        border-radius: 16px;
        padding: 10px 18px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease, border-color 0.2s ease;
    }

    .account-nav:hover {
        background: rgba(255, 255, 255, 0.12);
        border-color: rgba(255, 255, 255, 0.65);
    }

    .account-nav:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }

    .edit-target-btn {
        border: 1px solid #1F9C8A;
        background: #FFFFFF;
        color: #1F9C8A;
        border-radius: 14px;
        padding: 10px 18px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .edit-target-btn:hover {
        background: #1F9C8A;
        color: #FFFFFF;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        z-index: 1000;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-card {
        width: min(100%, 500px);
        max-width: 500px;
        background: #ffffff;
        border-radius: 28px;
        padding: 34px 32px;
        position: relative;
        box-shadow: 0 36px 110px -60px rgba(0, 0, 0, 0.22);
    }

    .modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        border: none;
        background: transparent;
        font-size: 26px;
        line-height: 1;
        cursor: pointer;
        color: #374151;
    }

    .modal-card h3 {
        margin: 0 0 14px;
        font-size: 1.35rem;
    }

    .modal-text {
        margin: 0 0 24px;
        color: #4B5563;
        line-height: 1.6;
    }

    .modal-label {
        display: block;
        margin: 14px 0 10px;
        font-weight: 700;
        color: #111827;
        letter-spacing: -0.02em;
    }

    .modal-input {
        width: 100%;
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        padding: 12px 14px;
        font-size: 0.95rem;
        color: #111827;
        outline: none;
        background: #F9FAFB;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .modal-input:focus {
        border-color: #10B981;
        background: #FFFFFF;
        box-shadow: 0 0 0 5px rgba(16, 185, 129, 0.12);
    }

    .modal-card .button-primary {
        width: 100%;
        margin-top: 24px;
        padding: 14px 0;
        border-radius: 16px;
        background: linear-gradient(135deg, #1F9C8A 0%, #10B981 100%);
        box-shadow: 0 18px 40px -20px rgba(31, 156, 138, 0.6);
        color: #ffffff;
        font-weight: 700;
        letter-spacing: 0.01em;
        border: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .modal-card .button-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 22px 44px -24px rgba(31, 156, 138, 0.7);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const targetAmountEl = document.getElementById('target-amount');
        const targetAchievementEl = document.getElementById('target-achievement');
        const monthlyTargetEl = document.getElementById('target-monthly');
        const progressCircle = document.getElementById('progress-ring-circle');
        const progressText = document.getElementById('progress-ring-text');

        function updateProgressDisplay(progress, target) {
            const progressValue = Number(progress) || 0;
            const targetValue = Number(target) || 0;
            const ratio = targetValue > 0 ? Math.min(progressValue / targetValue, 1) : 0;
            const dashArray = 326.72;
            const offset = dashArray - (dashArray * ratio);
            if (progressCircle) {
                progressCircle.style.strokeDashoffset = offset;
            }
            if (progressText) {
                progressText.textContent = Math.floor(progressValue / 1000) + 'K';
            }
        }

        const prevBtn = document.getElementById('prev-account-btn');
        const nextBtn = document.getElementById('next-account-btn');
        const accountTypeText = document.getElementById('account-type-text');
        const accountTitleText = document.getElementById('account-title-text');
        const accountNumberText = document.getElementById('account-number');
        const accountAmountText = document.getElementById('account-amount-text');
        const accountCurrencyIcon = document.getElementById('account-currency-icon');
        const accountChip = document.querySelector('.account-chip');
        const chartSelect = document.getElementById('chart-period-select');
        const chartTitle = document.getElementById('chart-period-title');
        const chartSubtitle = document.getElementById('chart-period-subtitle');
        const chartTotalCurrent = document.getElementById('chart-total-current');
        const chartTotalPrevious = document.getElementById('chart-total-previous');
        const chartBars = document.getElementById('chart-bars');
        const chartYAxis = document.getElementById('chart-y-axis');

        const chartData = @json($chartData);
        const accounts = @json($accounts -> toArray());
        let activeAccount = 0;

        function formatRupiah(value, withDecimals = false) {
            const number = Number(value) || 0;
            const options = {
                minimumFractionDigits: withDecimals ? 2 : 0,
                maximumFractionDigits: withDecimals ? 2 : 0,
            };
            return 'Rp ' + number.toLocaleString('id-ID', options);
        }

        function renderYAxis(maxAmount) {
            if (!chartYAxis) return;
            const steps = 5;
            const baseStep = 50000;
            let step = Math.max(Math.ceil(maxAmount / (steps - 1) / baseStep), 1) * baseStep;
            let top = step * (steps - 1);
            if (top > 10000000) {
                top = 10000000;
                step = top / (steps - 1);
            }
            const labels = Array.from({
                length: steps
            }, (_, index) => {
                const amount = step * (steps - 1 - index);
                return formatRupiah(amount, false);
            });
            chartYAxis.innerHTML = labels.map(label => `<span>${label}</span>`).join('');
        }

        function renderChart(period) {
            const data = chartData[period] || chartData.weekly;
            chartTitle.textContent = data.title;
            chartSubtitle.textContent = data.subtitle;
            chartTotalCurrent.textContent = formatRupiah(data.total, false);
            chartTotalPrevious.textContent = formatRupiah(data.previous_total, false);

            const maxAmount = Math.max(...data.items.map(item => item.amount), 1);
            renderYAxis(maxAmount);
            chartBars.innerHTML = data.items.map(item => `
                <div class="bar-group">
                    <div class="bar-stack">
                        <div class="bar current" style="height: ${Math.round((item.amount / maxAmount) * 100)}%;" title="${formatRupiah(item.amount, false)}"></div>
                    </div>
                    <span class="bar-label">${item.label}</span>
                </div>
            `).join('');
        }

        function setAccount(index) {
            const account = accounts[index] || {
                type: 'Debit Card',
                title: 'Paspor blue debit',
                number: '**** **** **** 2598',
                formatted: 'Rp 0',
                currency_icon: '',
                chipStyle: ''
            };
            accountTypeText.textContent = account.type;
            accountTitleText.textContent = account.title;
            accountNumberText.textContent = account.number || 'No account number';
            accountAmountText.textContent = account.formatted;
            accountCurrencyIcon.textContent = account.currency_icon || '';
            accountCurrencyIcon.style.display = account.currency_icon ? 'inline-flex' : 'none';
            accountNumberText.style.display = account.number ? 'block' : 'none';
            accountChip.className = 'account-chip' + (account.chipStyle ? ' ' + account.chipStyle : '');
            prevBtn.disabled = index <= 0;
            nextBtn.disabled = index >= accounts.length - 1 || accounts.length === 0;
        }

        prevBtn.addEventListener('click', function() {
            if (activeAccount > 0) {
                activeAccount -= 1;
                setAccount(activeAccount);
            }
        });

        nextBtn.addEventListener('click', function() {
            if (activeAccount < accounts.length - 1) {
                activeAccount += 1;
                setAccount(activeAccount);
            }
        });

        chartSelect.addEventListener('change', function() {
            renderChart(chartSelect.value);
        });

        setAccount(activeAccount);
        renderChart('weekly');
        updateProgressDisplay({
            {
                $progress ?? 0
            }
        }, {
            {
                optional($target) - > target_amount ?? 0
            }
        });
    });
</script>

@endsection