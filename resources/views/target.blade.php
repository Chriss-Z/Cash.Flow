@extends('layout.app')

@section('title', 'Cash.Flow | Target')

@section('content')

<div class="page-header target-header">
    <div>
        <p class="page-tag">Target</p>
        <h1>Tujuan Tabungan</h1>
    </div>
    <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
        <div class="page-meta">
            <span>{{ now()->format('M, Y') }}</span>
        </div>
        <div class="notification-trigger">
            <button type="button" class="icon-btn" aria-label="Notifications" id="topbar-notification-button">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M13.73 21a2 2 0 01-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                @if(count($notifications ?? []))
                    <span class="notification-badge">{{ count($notifications) }}</span>
                @endif
            </button>
            <div class="notification-menu" id="notification-menu" hidden>
                <div class="notification-menu-header">
                    <strong>Notifikasi</strong>
                </div>
                @forelse($notifications ?? [] as $notification)
                    <div class="notification-row">{{ $notification }}</div>
                @empty
                    <div class="notification-row empty">Tidak ada notifikasi baru.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@if(session('status') || session('error'))
<div class="target-flash {{ session('status') ? 'success' : 'error' }}" role="status" style="margin-bottom: 14px;">
    <span>{{ session('status') ?? session('error') }}</span>
</div>
@endif

@if($errors->any())
<div class="target-flash error" role="alert" style="margin-bottom: 14px;">
    <span>{{ $errors->first() }}</span>
</div>
@endif

@php
    $chartSets = $savingsChartData ?? [];
    $gaugePercent = min(max(round($percentage ?? 0), 0), 100);
    $gaugeOffset = 220 - (220 * $gaugePercent / 100);
    $targetAmount = optional($target)->target_amount ?? 0;
    $currentAmount = $progress ?? 0;
    $remainingAmount = max($targetAmount - $currentAmount, 0);
    $periodKey = is_string($defaultPeriod ?? null) ? $defaultPeriod : 'monthly';
    $selectedChart = $chartSets[$periodKey] ?? ['labels' => [], 'current' => [], 'previous' => [], 'current_total' => 0, 'previous_total' => 0];
    $currentSavingsTotal = $selectedChart['current_total'] ?? 0;
    $previousSavingsTotal = $selectedChart['previous_total'] ?? 0;
    $savingsDelta = $currentSavingsTotal - $previousSavingsTotal;
@endphp

<div class="target-grid">
    <section class="target-card card">
        <div class="target-card-head">
            <div>
                <p class="small-label">Target Pencapaian</p>
                <strong class="target-main-amount">Rp {{ number_format($currentAmount, 0, ',', '.') }}</strong>
                <p class="target-subtext">{{ $gaugePercent }}% dari target bulanan</p>
            </div>
            <div class="target-card-badge">{{ ($today ?? now())->translatedFormat('M, Y') }}</div>
        </div>

        <div class="target-card-body">
            <div class="target-details">
                <div class="detail-item">
                    <div class="detail-icon">🎯</div>
                    <div>
                        <span class="detail-label">Target Bulanan</span>
                        <strong>Rp {{ number_format($targetAmount, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon">🏆</div>
                    <div>
                        <span class="detail-label">Target Saat Ini</span>
                        <strong>Rp {{ number_format($currentAmount, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon">📉</div>
                    <div>
                        <span class="detail-label">Sisa Target</span>
                        <strong>Rp {{ number_format($remainingAmount, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>

            <div class="target-gauge-card">
                <div class="gauge-label">Target vs Pencapaian</div>
                <div class="gauge-shell">
                    <svg width="180" height="110" viewBox="0 0 180 110" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 90 A70 70 0 0 1 160 90" stroke="#E5E7EB" stroke-width="14" fill="none" stroke-linecap="round" />
                        <path d="M20 90 A70 70 0 0 1 160 90" stroke="#1F9C8A" stroke-width="14" fill="none" stroke-linecap="round" stroke-dasharray="220" stroke-dashoffset="{{ $gaugeOffset }}" />
                    </svg>
                    <div class="gauge-value">
                        <strong>{{ number_format($currentAmount, 0, ',', '.') }}</strong>
                        <span>Rp dicapai</span>
                    </div>
                </div>
                <div class="gauge-footer">
                    <span class="gauge-min">Rp 0</span>
                    <span class="gauge-mid">{{ $gaugePercent }}%</span>
                    <span class="gauge-max">Rp {{ number_format($targetAmount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="target-action-row">
            <form method="POST" action="{{ route('target.save') }}" class="target-edit-form">
                @csrf
                <label>
                    Target Bulanan
                    <input type="number" name="target_amount" min="0" step="0.01" value="{{ old('target_amount', $targetAmount) }}" required>
                </label>
                <label>
                    Pencapaian Saat Ini
                    <input type="number" name="current_amount" min="0" step="0.01" value="{{ old('current_amount', $currentAmount) }}" required>
                </label>
                <button type="submit" class="button-secondary">Simpan Target vs Pencapaian</button>
            </form>
        </div>
    </section>

    <section class="summary-card card">
        <div class="summary-header">
            <div>
                <p class="small-label">Saving Summary</p>
                <h3>Grafik Penghematan</h3>
            </div>
            <select class="select-period" id="savings-period" aria-label="Pilih periode">
                <option value="monthly" {{ $defaultPeriod === 'monthly' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                <option value="weekly" {{ $defaultPeriod === 'weekly' ? 'selected' : '' }}>7 Hari Terakhir</option>
                <option value="yearly" {{ $defaultPeriod === 'yearly' ? 'selected' : '' }}>5 Tahun Terakhir</option>
            </select>
        </div>

        <div class="summary-metric-row">
            <div class="summary-metric">
                <span>Total Penghematan</span>
                <strong id="metric-current">Rp {{ number_format($currentSavingsTotal, 0, ',', '.') }}</strong>
            </div>
            <div class="summary-metric">
                <span>Periode Sebelumnya</span>
                <strong id="metric-previous">Rp {{ number_format($previousSavingsTotal, 0, ',', '.') }}</strong>
            </div>
            <div class="summary-metric {{ $savingsDelta >= 0 ? 'positive' : 'negative' }}">
                <span>Selisih</span>
                <strong id="metric-delta">{{ $savingsDelta >= 0 ? '+' : '-' }}Rp {{ number_format(abs($savingsDelta), 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="summary-chart">
            <svg id="savings-chart-svg" viewBox="0 0 100 100" preserveAspectRatio="none" aria-label="Grafik penghematan user">
                <path id="previous-area" class="summary-area previous" d=""></path>
                <path id="current-area" class="summary-area current" d=""></path>
                <path id="previous-line" class="summary-line previous" d=""></path>
                <path id="current-line" class="summary-line current" d=""></path>
            </svg>
            <div class="summary-empty" id="summary-empty" hidden>Belum ada data transaksi untuk periode ini.</div>
            <div class="summary-axis" id="summary-axis"></div>
        </div>

        <div class="summary-legend">
            <span><i class="legend-dot current"></i> Periode Saat Ini</span>
            <span><i class="legend-dot previous"></i> Periode Sebelumnya</span>
        </div>
    </section>
</div>

<section class="category-section">
    <div class="section-header">
        <h3>Target Pengeluaran Berdasarkan Kategori</h3>
    </div>

    <form method="POST" action="{{ route('target.category.save') }}">
        @csrf
        <div class="category-grid">
            @forelse($expenseCategories ?? [] as $item)
                @php
                    $targetByCategory = (float) old('category_targets.' . $item['id'], $item['target_amount']);
                    $spentByCategory = (float) $item['spent_amount'];
                    $categoryPercent = $targetByCategory > 0 ? min(200, round(($spentByCategory / $targetByCategory) * 100)) : 0;
                @endphp
                <article class="category-card">
                    <div class="category-info">
                        <div class="category-icon">{{ $item['icon'] }}</div>
                        <div>
                            <p class="category-label">{{ $item['name'] }}</p>
                            <strong>Terpakai: Rp {{ number_format($spentByCategory, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <div class="category-actions">
                        <label class="category-input-label">
                            Target
                            <input
                                class="category-input"
                                type="number"
                                min="0"
                                step="0.01"
                                name="category_targets[{{ $item['id'] }}]"
                                value="{{ $targetByCategory > 0 ? $targetByCategory : '' }}"
                                placeholder="Masukkan target"
                            >
                        </label>
                        <div class="category-progress-wrap">
                            <div class="category-progress-bar">
                                <progress max="100" value="{{ min(100, $categoryPercent) }}"></progress>
                            </div>
                            <small>{{ $categoryPercent }}% dari target kategori</small>
                        </div>
                    </div>
                </article>
            @empty
                <article class="category-card category-empty-state">
                    <p>Belum ada kategori pengeluaran. Tambahkan kategori pengeluaran terlebih dahulu.</p>
                </article>
            @endforelse
        </div>

        @if(count($expenseCategories ?? []))
            <div class="category-submit-row">
                <button type="submit" class="button-tertiary">Simpan Target Per Kategori</button>
            </div>
        @endif
    </form>
</section>

<style>
    .target-flash {
        border-radius: 12px;
        padding: 9px 12px;
        font-size: 0.88rem;
        font-weight: 600;
        border: 1px solid transparent;
    }

    .target-flash.success {
        color: #065F46;
        background: #ECFDF5;
        border-color: #A7F3D0;
    }

    .target-flash.error {
        color: #991B1B;
        background: #FEF2F2;
        border-color: #FECACA;
    }

    .target-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
    }

    .page-tag {
        margin: 0 0 8px;
        color: #10B981;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        font-size: 0.83rem;
    }

    .target-header h1 {
        margin: 0;
        font-size: 2rem;
        letter-spacing: -0.03em;
    }

    .page-meta {
        color: #6B7280;
        background: #F3FAF8;
        padding: 12px 18px;
        border-radius: 16px;
        font-weight: 700;
        min-width: 140px;
        text-align: center;
    }

    .target-grid {
        display: grid;
        grid-template-columns: 1.15fr 1fr;
        gap: 22px;
        margin-bottom: 24px;
    }

    .target-card, .summary-card {
        border-radius: 28px;
        padding: 28px;
        box-shadow: 0 22px 52px rgba(15, 23, 42, 0.08);
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
    }

    .target-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 28px;
    }

    .small-label {
        margin: 0 0 8px;
        color: #6B7280;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.82rem;
        letter-spacing: 0.08em;
    }

    .target-main-amount {
        margin: 0;
        font-size: 2rem;
        line-height: 1;
        color: #111827;
    }

    .target-subtext {
        margin: 10px 0 0;
        color: #6B7280;
        font-size: 0.95rem;
    }

    .target-card-badge {
        padding: 10px 18px;
        border-radius: 999px;
        border: 1px solid #D1FAE5;
        background: #ECFDF5;
        color: #047857;
        font-weight: 700;
        white-space: nowrap;
    }

    .target-card-body {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: center;
    }

    .target-details {
        display: grid;
        gap: 16px;
    }

    .detail-item {
        display: flex;
        gap: 16px;
        align-items: center;
        padding: 18px 20px;
        border-radius: 20px;
        border: 1px solid #E5E7EB;
        background: #F9FAFB;
    }

    .detail-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #E6F6F0;
        color: #047857;
        font-size: 1.05rem;
    }

    .detail-label {
        display: block;
        margin-bottom: 6px;
        font-size: 0.9rem;
        color: #6B7280;
    }

    .target-gauge-card {
        padding: 18px;
        border-radius: 24px;
        border: 1px solid #E5E7EB;
        background: #F8FCFA;
    }

    .gauge-label {
        font-size: 0.95rem;
        color: #6B7280;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .gauge-shell {
        position: relative;
        width: 100%;
        min-height: 130px;
    }

    .gauge-shell svg {
        width: 100%;
        height: auto;
        display: block;
    }

    .gauge-value {
        position: absolute;
        inset: auto 20px 18px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
        right: 0;
        bottom: 0;
    }

    .gauge-value strong {
        font-size: 1.15rem;
        color: #111827;
    }

    .gauge-value span {
        color: #6B7280;
    }

    .gauge-footer {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 18px;
        font-size: 0.85rem;
        color: #6B7280;
    }

    .target-action-row {
        margin-top: 26px;
    }

    .target-edit-form {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        align-items: end;
    }

    .target-edit-form label {
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #6B7280;
    }

    .target-edit-form input {
        border: 1px solid #D1D5DB;
        border-radius: 12px;
        padding: 11px 12px;
        color: #111827;
        font-weight: 600;
    }

    .button-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 18px;
        border-radius: 16px;
        border: 1px solid #1F9C8A;
        background: #FFFFFF;
        color: #1F9C8A;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        min-height: 44px;
    }

    .summary-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
    }

    .summary-header h3 {
        margin: 0;
        font-size: 1.2rem;
    }

    .select-period {
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        padding: 10px 14px;
        background: #FFFFFF;
        color: #111827;
        font-weight: 600;
    }

    .summary-metric-row {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .summary-metric {
        border: 1px solid #E5E7EB;
        background: #F9FAFB;
        border-radius: 14px;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .summary-metric span {
        font-size: 0.78rem;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
    }

    .summary-metric strong {
        font-size: 1rem;
        color: #111827;
    }

    .summary-metric.positive strong {
        color: #047857;
    }

    .summary-metric.negative strong {
        color: #B91C1C;
    }

    .summary-chart {
        position: relative;
        min-height: 260px;
        padding: 18px 16px 44px;
        border-radius: 24px;
        background: linear-gradient(180deg, rgba(31,156,138,0.08) 0%, rgba(255,255,255,0.85) 100%);
        overflow: hidden;
    }

    #savings-chart-svg {
        width: 100%;
        height: 210px;
        display: block;
    }

    .summary-line {
        fill: none;
        stroke-width: 2.2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .summary-line.current {
        stroke: #0F766E;
    }

    .summary-line.previous {
        stroke: #94A3B8;
        stroke-dasharray: 4 4;
    }

    .summary-area {
        opacity: 0.2;
    }

    .summary-area.current {
        fill: #2DD4BF;
    }

    .summary-area.previous {
        fill: #CBD5E1;
    }

    .summary-empty {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6B7280;
        font-weight: 600;
        text-align: center;
        padding: 12px;
    }

    .summary-axis {
        position: absolute;
        left: 16px;
        right: 16px;
        bottom: 12px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
        gap: 6px;
        color: #64748B;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .summary-axis span {
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .summary-legend {
        display: flex;
        justify-content: flex-end;
        gap: 18px;
        margin-top: 18px;
        color: #6B7280;
        font-size: 0.95rem;
    }

    .legend-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 999px;
        margin-right: 6px;
    }

    .legend-dot.current {
        background: #0F766E;
    }

    .legend-dot.previous {
        background: #94A3B8;
    }

    .category-section {
        margin-top: 12px;
    }

    .section-header h3 {
        margin: 0 0 18px;
        font-size: 1.2rem;
        color: #111827;
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .category-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-radius: 22px;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        box-shadow: 0 16px 30px rgba(15, 23, 42, 0.05);
    }

    .category-info {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .category-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ECFDF5;
        color: #047857;
        font-size: 1.1rem;
    }

    .category-label {
        margin: 0 0 6px;
        font-size: 0.95rem;
        color: #6B7280;
    }

    .category-actions {
        width: min(270px, 100%);
        display: grid;
        gap: 10px;
    }

    .category-input-label {
        display: grid;
        gap: 6px;
        color: #6B7280;
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .category-input {
        border: 1px solid #D1D5DB;
        border-radius: 12px;
        padding: 10px 11px;
        color: #111827;
        font-weight: 600;
    }

    .category-progress-wrap {
        display: grid;
        gap: 6px;
    }

    .category-progress-wrap small {
        color: #64748B;
        font-size: 0.8rem;
    }

    .category-progress-bar {
        width: 100%;
        height: 9px;
        border-radius: 999px;
        background: #E5E7EB;
        overflow: hidden;
    }

    .category-progress-bar progress {
        width: 100%;
        height: 9px;
        border: 0;
        border-radius: 999px;
        overflow: hidden;
        background: #E5E7EB;
    }

    .category-progress-bar progress::-webkit-progress-bar {
        background: #E5E7EB;
        border-radius: 999px;
    }

    .category-progress-bar progress::-webkit-progress-value {
        background: linear-gradient(90deg, #14B8A6, #059669);
        border-radius: 999px;
    }

    .category-progress-bar progress::-moz-progress-bar {
        background: linear-gradient(90deg, #14B8A6, #059669);
        border-radius: 999px;
    }

    .category-empty-state {
        justify-content: center;
        text-align: center;
        color: #6B7280;
        font-weight: 600;
    }

    .category-submit-row {
        margin-top: 16px;
        display: flex;
        justify-content: flex-end;
    }

    .button-tertiary {
        border: 1px solid #D1FAE5;
        background: #FFFFFF;
        color: #047857;
        padding: 10px 14px;
        border-radius: 14px;
        font-weight: 700;
        cursor: pointer;
    }

    @media (max-width: 1100px) {
        .target-grid {
            grid-template-columns: 1fr;
        }
        .category-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .summary-metric-row {
            grid-template-columns: 1fr;
        }
        .target-edit-form {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 720px) {
        .target-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .page-meta {
            width: 100%;
            text-align: left;
        }
        .category-grid {
            grid-template-columns: 1fr;
        }
        .target-card, .summary-card {
            padding: 20px;
        }
        .target-card-body {
            grid-template-columns: 1fr;
        }
        .category-card {
            flex-direction: column;
            align-items: stretch;
        }
        .category-actions {
            width: 100%;
        }
        .summary-legend {
            justify-content: flex-start;
            flex-wrap: wrap;
        }
    }
</style>

<script id="savings-chart-data-json" type="application/json">{!! json_encode($chartSets, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK) !!}</script>

<script>
    const notificationButton = document.getElementById('topbar-notification-button');
    const notificationMenu = document.getElementById('notification-menu');

    if (notificationButton && notificationMenu) {
        notificationButton.addEventListener('click', function (event) {
            event.stopPropagation();
            notificationMenu.hidden = !notificationMenu.hidden;
        });

        document.addEventListener('click', function (event) {
            if (!notificationButton.contains(event.target) && !notificationMenu.contains(event.target)) {
                notificationMenu.hidden = true;
            }
        });
    }

    const chartDataElement = document.getElementById('savings-chart-data-json');
    const chartDataByPeriod = chartDataElement ? JSON.parse(chartDataElement.textContent) : {};
    const periodSelect = document.getElementById('savings-period');
    const currentLine = document.getElementById('current-line');
    const previousLine = document.getElementById('previous-line');
    const currentArea = document.getElementById('current-area');
    const previousArea = document.getElementById('previous-area');
    const summaryAxis = document.getElementById('summary-axis');
    const summaryEmpty = document.getElementById('summary-empty');
    const metricCurrent = document.getElementById('metric-current');
    const metricPrevious = document.getElementById('metric-previous');
    const metricDelta = document.getElementById('metric-delta');

    function formatRupiah(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(value || 0));
    }

    function buildLinePath(values) {
        if (!Array.isArray(values) || values.length === 0) {
            return '';
        }

        const minValue = Math.min(...values, 0);
        const maxValue = Math.max(...values, 0);
        const range = Math.max(maxValue - minValue, 1);
        const stepX = values.length > 1 ? 100 / (values.length - 1) : 100;
        const points = values.map((value, index) => {
            const x = values.length > 1 ? index * stepX : 50;
            const y = 90 - (((value - minValue) / range) * 70);
            return { x, y };
        });

        const line = points.map((point, index) => {
            const prefix = index === 0 ? 'M' : 'L';
            return prefix + point.x.toFixed(2) + ' ' + point.y.toFixed(2);
        }).join(' ');

        const area = line + ' L ' + points[points.length - 1].x.toFixed(2) + ' 90 L ' + points[0].x.toFixed(2) + ' 90 Z';

        return { line, area };
    }

    function updateChart(period) {
        const dataset = chartDataByPeriod[period] || { labels: [], current: [], previous: [], current_total: 0, previous_total: 0 };
        const labels = dataset.labels || [];
        const currentValues = (dataset.current || []).map(Number);
        const previousValues = (dataset.previous || []).map(Number);
        const mergedValues = currentValues.concat(previousValues);

        if (!labels.length || !mergedValues.some((value) => value !== 0)) {
            summaryEmpty.hidden = false;
            currentLine.setAttribute('d', '');
            previousLine.setAttribute('d', '');
            currentArea.setAttribute('d', '');
            previousArea.setAttribute('d', '');
            summaryAxis.innerHTML = labels.map((label) => '<span>' + label + '</span>').join('');
        } else {
            summaryEmpty.hidden = true;
            const currentPath = buildLinePath(currentValues);
            const previousPath = buildLinePath(previousValues);

            currentLine.setAttribute('d', currentPath.line);
            currentArea.setAttribute('d', currentPath.area);
            previousLine.setAttribute('d', previousPath.line);
            previousArea.setAttribute('d', previousPath.area);
            summaryAxis.innerHTML = labels.map((label) => '<span>' + label + '</span>').join('');
        }

        const currentTotal = Number(dataset.current_total || 0);
        const previousTotal = Number(dataset.previous_total || 0);
        const delta = currentTotal - previousTotal;

        metricCurrent.textContent = formatRupiah(currentTotal);
        metricPrevious.textContent = formatRupiah(previousTotal);
        metricDelta.textContent = (delta >= 0 ? '+Rp ' : '-Rp ') + new Intl.NumberFormat('id-ID').format(Math.round(Math.abs(delta)));
        metricDelta.parentElement.classList.toggle('positive', delta >= 0);
        metricDelta.parentElement.classList.toggle('negative', delta < 0);
    }

    if (periodSelect) {
        updateChart(periodSelect.value);
        periodSelect.addEventListener('change', function (event) {
            updateChart(event.target.value);
        });
    }
</script>

@endsection
