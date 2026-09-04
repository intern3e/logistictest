<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สรุปงานคนขับ</title>
    <script>
        // ถ้าเข้าหน้านี้มาโดยยังไม่ได้กรองวันที่ ให้เด้งไปกรองวันที่ปัจจุบันทันที
        (function () {
            const params = new URLSearchParams(window.location.search);
            if (!params.has('date')) {
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                params.set('date', `${yyyy}-${mm}-${dd}`);
                window.location.replace(window.location.pathname + '?' + params.toString());
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
<style>
    :root{
        --ink:#1a2634;
        --ink-soft:#5c6b7a;
        --ink-faint:#8592a0;
        --paper:#f5f6f8;
        --surface:#ffffff;
        --line:#e3e7ec;
        --line-strong:#d2d8e0;

        --navy:#001137;
        --navy-rgb:0,17,55;
        --navy-soft:#eaeef4;

        --accent:#0f766e;
        --accent-rgb:15,118,110;
        --accent-soft:#e6f5f3;

        --danger:#c0392b;
    }

    *{ box-sizing:border-box; }
    html, body{ background:var(--paper); }
    body{
        font-family:'Sarabun','Segoe UI',Tahoma,Arial,sans-serif;
        color:var(--ink);
        font-size:16px;
        line-height:1.5;
        margin:0;
        -webkit-font-smoothing:antialiased;
    }
    a{ color:inherit; }
    button{ font-family:inherit; }

    .page-shell{
        width:100%;
        max-width:1600px;
        margin:0 auto;
        padding:32px 24px 64px;
    }

    /* ---------- Header ---------- */
    .page-header{
        display:flex;
        justify-content:space-between;
        align-items:flex-end;
        flex-wrap:wrap;
        gap:16px;
        padding-bottom:22px;
        margin-bottom:28px;
        border-bottom:2px solid var(--ink);
    }
    .page-title{
        margin:0;
        font-size:1.6rem;
        font-weight:800;
        letter-spacing:-0.01em;
    }
    .page-subtitle{
        margin-top:5px;
        font-size:.95rem;
        color:var(--ink-soft);
    }
    .page-header-user{
        display:flex;
        flex-direction:column;
        align-items:flex-end;
        gap:10px;
    }
    .user-line{
        font-size:.92rem;
        color:var(--ink-soft);
    }
    .user-line strong{ color:var(--ink); font-weight:700; }

    .btn-ghost{
        display:inline-flex;
        align-items:center;
        gap:7px;
        padding:9px 16px;
        border-radius:8px;
        border:1px solid var(--line-strong);
        background:var(--surface);
        color:var(--ink);
        font-size:.92rem;
        font-weight:600;
        text-decoration:none;
        cursor:pointer;
        transition:background-color .15s ease, border-color .15s ease;
    }
    .btn-ghost:hover{ background:var(--paper); border-color:var(--ink-faint); color:var(--ink); text-decoration:none; }

    /* ---------- Filter bar ---------- */
    .filter-bar{
        display:flex;
        align-items:flex-end;
        flex-wrap:wrap;
        gap:14px;
        background:var(--surface);
        border:1px solid var(--line-strong);
        border-radius:10px;
        padding:16px 18px;
        margin-bottom:32px;
    }
    .filter-field label{
        display:block;
        font-size:.85rem;
        font-weight:600;
        color:var(--ink-soft);
        margin-bottom:6px;
    }
    .filter-field input[type="date"]{
        border:1px solid var(--line-strong);
        border-radius:8px;
        padding:9px 12px;
        font-size:.95rem;
        font-family:inherit;
        color:var(--ink);
        background:var(--surface);
    }
    .filter-field input[type="date"]:focus{
        outline:none;
        border-color:var(--navy);
        box-shadow:0 0 0 3px rgba(var(--navy-rgb),.15);
    }
    .filter-actions{ display:flex; gap:8px; }
    .btn-filter{
        border:1px solid var(--navy);
        background:var(--navy);
        color:#fff;
        border-radius:8px;
        padding:10px 18px;
        font-size:.92rem;
        font-weight:600;
        cursor:pointer;
    }
    .btn-filter:hover{ background:#000d29; }
    .btn-clear{
        border:1px solid var(--line-strong);
        background:var(--surface);
        color:var(--ink-soft);
        border-radius:8px;
        padding:10px 16px;
        font-size:.92rem;
        font-weight:600;
        text-decoration:none;
        display:inline-flex;
        align-items:center;
    }
    .btn-clear:hover{ background:var(--paper); color:var(--ink); text-decoration:none; }

    /* ---------- Date groups ---------- */
    .date-group{ margin-bottom:20px; }
    .date-group-header{
        display:flex;
        align-items:center;
        gap:10px;
        width:100%;
        padding:14px 16px;
        border:1px solid var(--line-strong);
        border-radius:10px;
        background:var(--surface);
        cursor:pointer;
        text-align:left;
    }
    .date-group-header:hover{ background:var(--navy-soft); }
    .date-group-header:focus-visible{ outline:2px solid var(--navy); outline-offset:-2px; }
    .date-group.expanded .date-group-header{
        border-radius:10px 10px 0 0;
        border-bottom-color:transparent;
    }

    .date-toggle-icon{
        flex-shrink:0;
        color:var(--navy);
        transition:transform .15s ease;
    }
    .date-group.expanded .date-toggle-icon{ transform:rotate(90deg); }

    .date-group-title{
        font-size:1.02rem;
        font-weight:800;
        color:var(--ink);
        margin:0;
        flex:1;
    }
    .date-group-count{
        flex-shrink:0;
        font-size:.8rem;
        font-weight:700;
        padding:4px 10px;
        border-radius:999px;
        background:var(--navy-soft);
        color:var(--navy);
        white-space:nowrap;
    }

    .date-collapse{
        display:none;
        border:1px solid var(--line-strong);
        border-top:none;
        border-radius:0 0 10px 10px;
    }
    .date-group.expanded .date-collapse{ display:block; }
    .date-collapse-inner{ overflow:hidden; }
    .date-collapse-body{ padding:16px; }

    .box-grid{
        display:grid;
        grid-template-columns:repeat(3, minmax(0, 1fr));
        gap:14px;
        align-items:start;
    }
    @media (max-width:1200px){
        .box-grid{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width:720px){
        .box-grid{ grid-template-columns:1fr; }
    }

    /* ---------- Box card ---------- */
    .box-card{
        background:var(--surface);
        border:1px solid var(--line-strong);
        border-left:4px solid var(--navy);
        border-radius:10px;
        overflow:hidden;
        min-width:0;
    }

    .box-card-header{
        width:100%;
        display:flex;
        align-items:flex-start;
        gap:10px;
        padding:13px 14px;
        border:none;
        border-bottom:1px solid var(--line);
        background:var(--paper);
        cursor:pointer;
        text-align:left;
    }
    .box-card-header:hover{ background:var(--navy-soft); }
    .box-card-header:focus-visible{ outline:2px solid var(--navy); outline-offset:-2px; }

    .box-toggle-icon{
        flex-shrink:0;
        margin-top:3px;
        color:var(--ink-faint);
        transition:transform .15s ease;
    }
    .box-card.expanded .box-toggle-icon{ transform:rotate(90deg); }

    .box-head-main{ flex:1; min-width:0; }
    .box-transport-name{
        font-weight:800;
        font-size:1rem;
        color:var(--ink);
        word-break:break-word;
    }
    .box-driver-name{
        font-weight:700;
        color:var(--accent);
    }
    .box-driver-empty{
        font-weight:400;
        color:var(--ink-faint);
    }

    .box-count-badge{
        flex-shrink:0;
        font-size:.78rem;
        font-weight:700;
        padding:4px 10px;
        border-radius:999px;
        background:var(--navy-soft);
        color:var(--navy);
        white-space:nowrap;
    }

    .box-card-collapse{
        display:grid;
        grid-template-rows:0fr;
        transition:grid-template-rows .22s ease;
    }
    .box-card.expanded .box-card-collapse{ grid-template-rows:1fr; }
    .box-card-collapse-inner{ overflow:hidden; min-height:0; }

    .box-card-body{ padding:6px 14px 14px; }
    .box-assigned-by{
        font-size:.85rem;
        color:var(--ink-soft);
        padding:10px 0 4px;
    }

    .job-block{ padding:10px 0; border-top:1px solid var(--line); }
    .job-block:first-child{ border-top:none; }
    .job-customer-code{
        font-family:'JetBrains Mono',monospace;
        font-weight:800;
        font-size:.92rem;
        color:var(--ink);
    }
    .job-customer-name{
        font-weight:500;
        color:var(--ink-soft);
    }

    .job-item-row{
        display:flex;
        align-items:baseline;
        justify-content:space-between;
        gap:10px;
        font-size:.85rem;
        color:var(--ink-soft);
        margin-top:4px;
    }
    .job-print-link{
        flex-shrink:0;
        font-weight:600;
        color:var(--accent);
        text-decoration:none;
        white-space:nowrap;
    }
    .job-print-link:hover{ text-decoration:underline; }

    .box-print-all{
        text-align:right;
        margin-top:12px;
        padding-top:12px;
        border-top:1px solid var(--line);
    }
    .btn-print-group{
        display:inline-flex;
        align-items:center;
        gap:7px;
        padding:9px 16px;
        border-radius:8px;
        border:1px solid var(--accent);
        background:var(--accent-soft);
        color:var(--accent);
        font-size:.88rem;
        font-weight:700;
        text-decoration:none;
    }
    .btn-print-group:hover{ background:rgba(var(--accent-rgb),.18); color:var(--accent); text-decoration:none; }

    /* ---------- Empty states ---------- */
    .empty-note{
        font-size:.95rem;
        color:var(--ink-soft);
        padding:26px;
        border:1px dashed var(--line-strong);
        border-radius:10px;
        background:var(--surface);
        text-align:center;
    }
    .empty-state-global{
        font-size:.95rem;
        color:var(--ink-soft);
        padding:32px;
        border:1px dashed var(--line-strong);
        border-radius:10px;
        background:var(--surface);
        text-align:center;
    }

    /* ---------- Accessibility & motion ---------- */
    *:focus-visible{ outline:2px solid var(--navy); outline-offset:2px; }
    @media (prefers-reduced-motion: reduce){
        *{ transition:none !important; }
    }

    @media (max-width:768px){
        .page-shell{ padding:20px 16px 48px; }
        .page-header{ flex-direction:column; align-items:flex-start; }
        .page-header-user{ align-items:flex-start; width:100%; }
    }
</style>
</head>
<body>
<div class="page-shell">

    <div class="page-header">
        <div>
            <h1 class="page-title">สรุปงานคนขับ</h1>
            <div class="page-subtitle">งานที่จัดส่งแล้ว แยกตามวันที่และคนขับ</div>
        </div>
        <div class="page-header-user">
            <div class="user-line">ผู้ใช้งาน: <strong>{{ $loggedInName }}</strong></div>
            <a href="{{ route('deliverytrack') }}" class="btn-ghost">← กลับไปหน้าจ่ายงาน</a>
        </div>
    </div>

    @php $todayKey = date('Y-m-d'); @endphp

    <form method="GET" action="{{ route('deliverytrack.summary') }}" class="filter-bar">
        <div class="filter-field">
            <label for="filterDate">กรองตามวันที่จัดส่ง</label>
            <input type="date" id="filterDate" name="date" value="{{ $date ?: $todayKey }}">
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn-filter">กรอง</button>
            @if ($date)
                <a href="{{ route('deliverytrack.summary') }}" class="btn-clear">ล้างตัวกรอง</a>
            @endif
        </div>
    </form>

    @forelse ($boxesByDate as $dateKey => $boxes)
        @php
            $dateKeyId = 'date_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $dateKey);
            $isDefaultOpen = count($boxesByDate) === 1 || $dateKey === $todayKey;
        @endphp
        <div class="date-group {{ $isDefaultOpen ? 'expanded' : '' }}" id="{{ $dateKeyId }}">
            <button type="button" class="date-group-header" onclick="toggleDateGroup('{{ $dateKeyId }}')">
                <svg class="date-toggle-icon" width="14" height="14" viewBox="0 0 16 16" fill="none">
                    <path d="M5 3l6 5-6 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h2 class="date-group-title">วันที่จัดส่ง: {{ $dateKey }}</h2>
                <span class="date-group-count">{{ collect($boxes)->sum('total_items') }} รายการ</span>
            </button>

            <div class="date-collapse">
                <div class="date-collapse-inner">
                    <div class="date-collapse-body">
                        @if (count($boxes) > 0)
                            <div class="box-grid">
                                @foreach ($boxes as $box)
                                    @php
                                        $boxKey = 'box_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $dateKey . '_' . $box['transport_name'] . '_' . ($box['driver_name'] ?? 'none'));
                                        $printUrl = route('deliverytrack.printGroup', [
                                            'date'      => $dateKey === 'ไม่ระบุวันที่' ? null : $dateKey,
                                            'transport' => $box['transport_name'],
                                            'driver'    => $box['driver_name'],
                                        ]);
                                    @endphp
                                    <div class="box-card" id="{{ $boxKey }}">
                                        <button type="button" class="box-card-header" onclick="toggleBoxCard('{{ $boxKey }}')">
                                            <svg class="box-toggle-icon" width="14" height="14" viewBox="0 0 16 16" fill="none">
                                                <path d="M5 3l6 5-6 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <span class="box-head-main">
                                                <span class="box-transport-name">
                                                    {{ $box['transport_name'] }}
                                                    @if (!empty($box['driver_name']))
                                                        <span class="box-driver-name"> x {{ $box['driver_name'] }}</span>
                                                    @else
                                                        <span class="box-driver-empty"> (ไม่ระบุผู้รับผิดชอบ)</span>
                                                    @endif
                                                </span>
                                            </span>
                                            <span class="box-count-badge">{{ $box['total_items'] }} รายการ</span>
                                        </button>

                                        <div class="box-card-collapse">
                                            <div class="box-card-collapse-inner">
                                                <div class="box-card-body">
                                                    <div class="box-assigned-by">ผู้จ่ายงาน: {{ $box['assigned_by'] ?: '-' }}</div>

                                                    @foreach ($box['customers'] as $cust)
                                                        <div class="job-block">
                                                            <div>
                                                                <span class="job-customer-code">{{ $cust['customer_code'] }}</span>
                                                                @if (!empty($cust['customer_name']))
                                                                    <span class="job-customer-name">- {{ $cust['customer_name'] }}</span>
                                                                @endif
                                                            </div>
                                                            @foreach ($cust['items'] as $item)
                                                                <div class="job-item-row">
                                                                    <span>งานที่ {{ $item['seq'] }} · บิล {{ $item['bill_no'] }}</span>
                                                                    <a href="{{ route('print.notes', $item['id']) }}" target="_blank" class="job-print-link">ปริ้นบิล</a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endforeach

                                                    <div class="box-print-all">
                                                        <a class="btn-print-group" target="_blank" href="{{ $printUrl }}">🖨️ ปริ้นใบงาน (A4)</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-note">ไม่มีรายการ</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state-global">ยังไม่มีงานที่จัดส่งแล้ว{{ $date ? ' ในวันที่เลือก' : '' }}</div>
    @endforelse

</div>

<script>
function toggleBoxCard(id) {
    const card = document.getElementById(id);
    if (card) card.classList.toggle('expanded');
}

function toggleDateGroup(id) {
    const group = document.getElementById(id);
    if (group) group.classList.toggle('expanded');
}
</script>
</body>
</html>