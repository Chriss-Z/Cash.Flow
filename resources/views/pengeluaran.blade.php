@extends('layout.app')

@section('title', 'Cash.Flow | Pengeluaran')

@section('content')
<div class="topbar">
    <div class="topbar-left">
        <h1 class="topbar-title">Pengeluaran</h1>
        <div class="topbar-meta">
            <span>{{ now()->format('M d, Y') }}</span>
            <span class="meta-separator">•</span>
            <span>Analisis pengeluaran Anda berdasarkan kategori dan kisaran terbaru.</span>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="history-header">
        <div>
            <h3>Expenses Comparison</h3>
            <p style="margin: 8px 0 0; color: #6B7280; font-size: 0.95rem;">Perbandingan pengeluaran bulan ini terhadap tahun sebelumnya.</p>
        </div>
        <div class="history-tabs" style="padding: 8px;">
            <button class="history-tab active">Monthly Comparison</button>
        </div>
    </div>

    <div class="chart-legend" style="margin-bottom: 18px;">
        <div class="legend-item"><span class="legend-dot primary"></span> Tahun Ini</div>
        <div class="legend-item"><span class="legend-dot secondary"></span> Tahun Lalu</div>
    </div>

    <div class="pengeluaran-chart">
        @php
    $activeChart = $chartData['weekly']['items'] ?? [];

    $values = [];

    foreach ($activeChart as $item) {
        $values[] = $item['current'] ?? 0;
        $values[] = $item['previous'] ?? 0;
    }

    $maxChart = !empty($values)
        ? max($values)
        : 1;
<div class="chart-bars">
    foreach ($activeChart as &$item) {
        $item['current_percent'] =
            $maxChart > 0
                ? (($item['current'] ?? 0) / $maxChart) * 100
                : 0;

        $item['previous_percent'] =
            $maxChart > 0
                ? (($item['previous'] ?? 0) / $maxChart) * 100
                : 0;
    }
@endphp
        <div class="stats-chart">
            <div class="chart-y-axis">
                @for($i = 5; $i >= 0; $i--)
                    <span>Rp {{ number_format(($maxChart / 5) * $i, 0, ',', '.') }}</span>
                @endfor
                <span>0</span>
            </div>

            <div class="chart-bars">
                @foreach($chartData as $item)
                    <div class="bar-group">
                        <div class="bar-stack">
                            <div class="bar last" style="height: {{ $item['previous_percent'] }}%;"></div>
                            <div class="bar current" style="height: {{ $item['current_percent'] }}%;"></div>
                        </div>
                        <div class="bar-label">{{ $item['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<h2 class="section-title">Expenses Breakdown</h2>
<div class="breakdown-grid">
    @forelse($categoryCards as $category)
        <article class="breakdown-card">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                <div>
                    <p style="margin: 0 0 6px; font-size: 0.85rem; color: #6B7280; text-transform: uppercase; letter-spacing: .04em;">{{ $category['label'] }}</p>
                    <strong style="font-size: 1.35rem;">Rp {{ number_format($category['amount'], 0, ',', '.') }}</strong>
                </div>
                <div style="display: inline-flex; align-items: center; gap: 8px; color: {{ $category['trend'] === 'up' ? '#DC2626' : '#16A34A' }}; font-weight: 700;">
                    <span>{{ $category['trend'] === 'up' ? '↑' : '↓' }}</span>
                    <span>{{ $category['change'] }}</span>
                </div>
            </div>
            <p style="margin: 0 0 18px; color: #6B7280; font-size: 0.92rem;">Compare to last month</p>

            @if(count($category['recent']) > 0)
                <div style="display: grid; gap: 12px;">
                    @foreach($category['recent'] as $transaction)
                        <div class="secondary-item" style="padding: 16px 18px;">
                            <div class="meta">
                                <strong>{{ $transaction->description }}</strong>
                                <span>{{ $transaction->date->format('d M Y') }}</span>
                            </div>
                            <div class="amount">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="margin: 0; color: #6B7280;">Belum ada transaksi pada kategori ini.</p>
            @endif
        </article>
    @empty
        <div class="card" style="padding: 30px; text-align: center;">
            <p style="margin: 0; color: #6B7280;">Belum ada pengeluaran. Tambahkan transaksi pengeluaran untuk melihat ringkasan.</p>
        </div>
    @endforelse
</div>

<style>
    .pengeluaran-chart .stats-chart {
        display: grid;
        grid-template-columns: 40px 1fr;
        gap: 18px;
        align-items: end;
        min-height: 260px;
    }

    .pengeluaran-chart .chart-bars {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 12px;
        align-items: end;
    }

    .pengeluaran-chart .bar-group {
        display: grid;
        gap: 6px;
        align-items: end;
        justify-items: center;
        width: 100%;
        max-width: 48px;
    }

    .pengeluaran-chart .bar-stack {
        display: flex;
        gap: 6px;
        align-items: flex-end;
        height: 180px;
        width: 100%;
    }

    .pengeluaran-chart .bar-stack .bar {
        width: 100%;
        min-width: 12px;
        border-radius: 999px 999px 0 0;
    }

    .pengeluaran-chart .bar-label {
        font-size: 0.78rem;
        color: #6B7280;
        text-align: center;
        white-space: nowrap;
    }

    @media (max-width: 1240px) {
        .pengeluaran-chart .chart-bars {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .pengeluaran-chart .stats-chart {
            grid-template-columns: 1fr;
        }

        .pengeluaran-chart .chart-y-axis {
            display: none;
        }
    }
</style>
@endsection
