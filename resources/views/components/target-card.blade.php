<div class="card savings-card">
    <div class="savings-card-top">
        <div>
            <h4 class="savings-title">Target Tabungan</h4>
            <div class="savings-sub">Jumlah tercapai</div>
            <strong class="savings-achieved">Rp {{ number_format($progress ?? 0,0,',','.') }}</strong>
        </div>

        <div class="savings-gauge-wrap" aria-hidden="true">
            <svg class="savings-gauge" width="160" height="90" viewBox="0 0 160 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 80 A70 70 0 0 1 150 80" stroke="#E6EEF8" stroke-width="12" stroke-linecap="round" fill="none" />
                <path id="savings-gauge-arc" d="M10 80 A70 70 0 0 1 150 80" stroke="#1F9C8A" stroke-width="12" stroke-linecap="round" fill="none" stroke-dasharray="220" stroke-dashoffset="220" transform="rotate(0 80 80)" />
            </svg>
            <div class="savings-gauge-text">{{ round(($percentage ?? 0)) }}%</div>
        </div>
    </div>

    <div class="savings-body">
        <div class="savings-left">
            <div class="savings-row">
                <div class="label">Target Bulanan</div>
                <div class="value">Rp {{ number_format(optional($target)->target_amount ?? 0,0,',','.') }}</div>
            </div>
            <div class="savings-row">
                <div class="label">Sisa Target</div>
                <div class="value">Rp {{ number_format(max(optional($target)->target_amount - ($progress ?? 0), 0),0,',','.') }}</div>
            </div>
        </div>
        <div class="savings-actions">
            <button onclick="location.href='{{ route('target') }}'" class="btn-adjust">Sesuaikan Target</button>
        </div>
    </div>

    <style>
        .savings-card { padding: 18px; border-radius: 12px; display: flex; flex-direction: column; gap: 12px; }
        .savings-card-top { display:flex; align-items:center; justify-content:space-between; gap:12px; }
        .savings-title { margin:0; font-size:0.95rem; color:#111827; }
        .savings-sub { font-size:0.85rem; color:#6B7280; margin-top:6px }
        .savings-achieved { font-size:1.4rem; margin-top:6px; display:block }
        .savings-gauge-wrap { position:relative; width:160px; height:90px; }
        .savings-gauge { display:block }
        .savings-gauge-text { position:absolute; right:8px; bottom:8px; font-weight:700; color:#111827 }
        .savings-body { display:flex; align-items:center; justify-content:space-between; gap:12px; }
        .savings-left { display:flex; flex-direction:column; gap:10px }
        .savings-row { display:flex; flex-direction:column }
        .label { color:#6B7280; font-size:0.85rem }
        .value { font-weight:700; color:#111827 }
        .savings-actions { display:flex; align-items:center }
        .btn-adjust { background:#FFFFFF; border:1px solid #1F9C8A; color:#1F9C8A; padding:8px 12px; border-radius:8px; cursor:pointer; font-weight:700 }
    </style>

    <script>
        (function(){
            const arc = document.getElementById('savings-gauge-arc');
            if (!arc) return;
            const perc = Number({{ round($percentage ?? 0) }});
            const dash = 220; // rough length matching arc
            const offset = dash - (dash * Math.min(perc,100) / 100);
            arc.style.strokeDashoffset = offset;
        })();
    </script>
</div>
