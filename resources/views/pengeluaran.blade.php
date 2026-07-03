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
    <div class="topbar-right">
        <div class="notification-trigger">
            <button type="button" class="icon-btn" aria-label="Notifications" id="topbar-notification-button">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.73 21a2 2 0 01-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
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

            $chartItems = collect($activeChart)->map(function ($item) {
                return [
                    'label' => $item['label'] ?? '-',
                    'current' => $item['current'] ?? 0,
                    'previous' => $item['previous'] ?? 0,
                ];
            });

            $values = $chartItems->flatMap(function ($item) {
                return [$item['current'], $item['previous']];
            })->all();

            $maxChart = !empty($values) ? max($values) : 1;

            $currentLegend = $chartData['weekly']['current_label'] ?? 'Tahun Ini';
            $previousLegend = $chartData['weekly']['previous_label'] ?? 'Tahun Lalu';
@endphp
        <div class="stats-chart">
            <div class="chart-y-axis">
                @for($i = 5; $i >= 0; $i--)
                    <span>Rp {{ number_format(($maxChart / 5) * $i, 0, ',', '.') }}</span>
                @endfor
                <span>0</span>
            </div>

            <div class="chart-bars">
                @forelse($chartItems as $item)
                    @php
                        $currentPercent = $maxChart > 0 ? (($item['current'] / $maxChart) * 100) : 0;
                        $previousPercent = $maxChart > 0 ? (($item['previous'] / $maxChart) * 100) : 0;
                    @endphp
                    <div class="bar-group">
                        <div class="bar-stack">
                            <div class="bar last" style="height: {{ $previousPercent }}%;" title="{{ $previousLegend }}: Rp {{ number_format($item['previous'], 0, ',', '.') }}"></div>
                            <div class="bar current" style="height: {{ $currentPercent }}%;" title="{{ $currentLegend }}: Rp {{ number_format($item['current'], 0, ',', '.') }}"></div>
                        </div>
                        <div class="bar-label">{{ $item['label'] }}</div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; padding: 24px 0; color: #6B7280; text-align: center;">
                        Belum ada data pengeluaran untuk ditampilkan.
                    </div>
                @endforelse
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
                @php
                    $trend = $category['trend'] ?? 'down';
                    $change = $category['change'] ?? '0%';
                    $recent = $category['recent'] ?? collect();
                @endphp
                <div style="display: inline-flex; align-items: center; gap: 8px; color: {{ $trend === 'up' ? '#DC2626' : '#16A34A' }}; font-weight: 700;">
                    <span>{{ $trend === 'up' ? '↑' : '↓' }}</span>
                    <span>{{ $change }}</span>
                </div>
            </div>
            <p style="margin: 0 0 18px; color: #6B7280; font-size: 0.92rem;">Compare to last month</p>

            @if(count($recent) > 0)
                <div style="display: grid; gap: 12px;">
                    @foreach($recent as $transaction)
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
</script>
@endsection
