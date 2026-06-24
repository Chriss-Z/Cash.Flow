@extends('layout.app')

@section('title', 'Cash.Flow | Target')

@section('content')

<div class="page-header target-header">
    <div>
        <p class="page-tag">Target</p>
        <h1>Tujuan Tabungan</h1>
    </div>
    <div class="page-meta">
        <span>{{ now()->format('M, Y') }}</span>
    </div>
</div>

@if(session('status') || session('error'))
<div class="notification-card {{ session('status') ? 'success' : 'error' }}" style="margin-bottom: 24px;">
    <div class="notification-thumb">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M13.73 21a2 2 0 01-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </div>
    <div>
        <strong>{{ session('status') ? 'Berhasil' : 'Pemberitahuan' }}</strong>
        <p>{{ session('status') ?? session('error') }}</p>
    </div>
</div>
@endif

@php
    $gaugePercent = min(max(round($percentage ?? 0), 0), 100);
    $gaugeOffset = 220 - (220 * $gaugePercent / 100);
    $targetAmount = optional($target)->target_amount ?? 0;
    $currentAmount = $progress ?? 0;
    $remainingAmount = max($targetAmount - $currentAmount, 0);
@endphp

<div class="target-grid">
    <section class="target-card card">
        <div class="target-card-head">
            <div>
                <p class="small-label">Target Pencapaian</p>
                <strong class="target-main-amount">Rp {{ number_format($currentAmount, 0, ',', '.') }}</strong>
                <p class="target-subtext">{{ $gaugePercent }}% dari target bulanan</p>
            </div>
            <div class="target-card-badge">Apr, 2026</div>
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
            <a href="{{ route('target') }}" class="button-secondary">Sesuaikan Target</a>
        </div>
    </section>

    <section class="summary-card card">
        <div class="summary-header">
            <div>
                <p class="small-label">Saving Summary</p>
                <h3>Grafik Penghematan</h3>
            </div>
            <select class="select-period" aria-label="Pilih periode">
                <option value="monthly">Bulan Ini</option>
                <option value="weekly">Minggu Ini</option>
                <option value="yearly">Tahun Ini</option>
            </select>
        </div>

        <div class="summary-chart">
            <div class="summary-chart-line"></div>
            <div class="summary-chart-shade"></div>
            <div class="summary-chart-overlay">
                @for($i = 0; $i < 6; $i++)
                    @php
                        $pointLeft = 12 + ($i * 14);
                        $pointBottom = rand(18, 66);
                        $pointStyle = "left: {$pointLeft}%; bottom: {$pointBottom}%;";
                    @endphp
                    <div class="summary-chart-point" style="{{ $pointStyle }}"></div>
                @endfor
            </div>
        </div>

        <div class="summary-legend">
            <span>Periode Saat Ini</span>
            <span>Periode Sebelumnya</span>
        </div>
    </section>
</div>

<section class="category-section">
    <div class="section-header">
        <h3>Target Pengeluaran Berdasarkan Kategori</h3>
    </div>

    <div class="category-grid">
        @php
            $categories = [
                ['label' => 'Perumahan', 'value' => 250000, 'icon' => '🏠'],
                ['label' => 'Makanan', 'value' => 250000, 'icon' => '🍔'],
                ['label' => 'Transportasi', 'value' => 250000, 'icon' => '🚗'],
                ['label' => 'Hiburan', 'value' => 250000, 'icon' => '🎬'],
                ['label' => 'Belanja', 'value' => 250000, 'icon' => '🛍️'],
                ['label' => 'Lainnya', 'value' => 250000, 'icon' => '🧾'],
            ];
        @endphp
        @foreach($categories as $item)
        <article class="category-card">
            <div class="category-info">
                <div class="category-icon">{{ $item['icon'] }}</div>
                <div>
                    <p class="category-label">{{ $item['label'] }}</p>
                    <strong>Rp {{ number_format($item['value'], 0, ',', '.') }}</strong>
                </div>
            </div>
            <button class="button-tertiary">Atur</button>
        </article>
        @endforeach
    </div>
</section>

<style>
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

    .summary-chart {
        position: relative;
        min-height: 260px;
        border-radius: 24px;
        background: linear-gradient(180deg, rgba(31,156,138,0.08) 0%, rgba(255,255,255,0.85) 100%);
        overflow: hidden;
    }

    .summary-chart-line {
        position: absolute;
        inset: 18px 24px auto 24px;
        height: 70%;
        border-bottom: 1px solid rgba(31, 156, 138, 0.15);
    }

    .summary-chart-shade {
        position: absolute;
        left: 24px;
        right: 24px;
        bottom: 18px;
        height: 45%;
        background: linear-gradient(180deg, rgba(31,156,138,0.22) 0%, rgba(255,255,255,0) 100%);
        border-radius: 24px;
    }

    .summary-chart-overlay {
        position: absolute;
        inset: 18px 24px 18px 24px;
    }

    .summary-chart-point {
        position: absolute;
        width: 12px;
        height: 12px;
        border-radius: 999px;
        background: #10B981;
        box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.12);
    }

    .summary-legend {
        display: flex;
        justify-content: flex-end;
        gap: 18px;
        margin-top: 18px;
        color: #6B7280;
        font-size: 0.95rem;
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
    }
</style>

@endsection
