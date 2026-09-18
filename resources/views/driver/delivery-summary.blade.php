<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สรุปงานคนขับ</title>
    <script>
        // ถ้าเข้าหน้านี้มาโดยยังไม่ได้กรองวันที่ (และไม่ได้ค้นด้วยเลขบิล) ให้เด้งไปกรองวันที่ปัจจุบันทันที
        (function () {
            const params = new URLSearchParams(window.location.search);
            if (!params.has('date') && !params.get('bill_id')) {
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
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root{
        --paper:#eef1f5;
        --surface:#ffffff;
        --line:#e4e8ee;
        --line-strong:#d6dce4;

        --ink:#141f2c;
        --ink-soft:#54657a;
        --ink-faint:#8a97a8;

        --primary:#2853d5;
        --primary-rgb:40,83,213;
        --primary-deep:#1f41a6;
        --primary-soft:#eaeefb;

        --amber:#2853d5;
        --amber-rgb:40,83,213;
        --amber-soft:#eaeefb;

        --success:#276b2b;
        --success-soft:#e7f3e7;
        --danger:#b3261e;
        --danger-soft:#fbe9e7;

        --font-sans:'Sarabun','Segoe UI',Tahoma,Arial,sans-serif;
        --font-mono:'JetBrains Mono','Consolas',monospace;

        --radius-sm:7px;
        --radius-md:12px;
    }

    *{ box-sizing:border-box; }
    html, body{ background:var(--paper); }
    body{
        font-family:var(--font-sans);
        color:var(--ink);
        font-size:16px;
        line-height:1.5;
        margin:0;
        -webkit-font-smoothing:antialiased;
        background-color:var(--paper);
    }
    a{ color:inherit; }
    button{ font-family:inherit; }
    ::selection{ background:rgba(var(--primary-rgb),.16); color:var(--ink); }
    ::-moz-selection{ background:rgba(var(--primary-rgb),.16); color:var(--ink); }

    .page-shell{
        width:100%;
        max-width:1440px;
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
        margin-bottom:24px;
        border-bottom:2px solid var(--ink);
    }
    .page-title-row{ display:flex; align-items:center; gap:13px; }
    .page-mark{
        flex-shrink:0;
        width:42px; height:42px;
        border-radius:var(--radius-sm);
        background:var(--primary-soft);
        border:1px solid var(--line-strong);
        display:flex; align-items:center; justify-content:center;
        color:var(--primary);
    }
    .page-title{
        margin:0;
        font-size:1.5rem;
        font-weight:800;
        letter-spacing:-0.01em;
        color:var(--ink);
    }
    .page-subtitle{
        margin-top:4px;
        font-size:.92rem;
        color:var(--ink-soft);
    }
    .page-header-user{
        display:flex;
        flex-direction:column;
        align-items:flex-end;
        gap:10px;
    }
    .user-line{
        font-size:.88rem;
        color:var(--ink-soft);
    }
    .user-line strong{ color:var(--ink); font-weight:700; }

    .btn-ghost{
        display:inline-flex;
        align-items:center;
        gap:7px;
        padding:9px 16px;
        border-radius:var(--radius-sm);
        border:1px solid var(--line-strong);
        background:var(--surface);
        color:var(--ink);
        font-size:.88rem;
        font-weight:600;
        text-decoration:none;
        cursor:pointer;
        transition:background-color .15s ease, border-color .15s ease;
    }
    .btn-ghost:hover{ background:var(--paper); border-color:var(--ink-faint); color:var(--ink); text-decoration:none; }
    .btn-ghost svg{ flex-shrink:0; }

    /* ---------- Dashboard bar: filter + stats ---------- */
    .dashboard-bar{
        display:flex;
        align-items:stretch;
        flex-wrap:wrap;
        background:var(--surface);
        border:1px solid var(--line-strong);
        border-radius:var(--radius-md);
        margin-bottom:28px;
        overflow:hidden;
    }
    .dashboard-filter{
        flex:1 1 320px;
        display:flex;
        align-items:flex-end;
        flex-wrap:wrap;
        gap:14px;
        padding:18px 20px;
        border-bottom:1px solid var(--line);
    }
    .filter-field label{
        display:block;
        font-size:.82rem;
        font-weight:600;
        color:var(--ink-soft);
        margin-bottom:6px;
    }
    .filter-field input[type="date"]{
        border:1px solid var(--line-strong);
        border-radius:var(--radius-sm);
        padding:9px 12px;
        font-size:.95rem;
        font-family:inherit;
        color:var(--ink);
        background:var(--surface);
    }
    .filter-field input[type="date"]:focus{
        outline:none;
        border-color:var(--primary);
        box-shadow:0 0 0 3px rgba(var(--primary-rgb),.15);
    }
    .filter-actions{ display:flex; gap:8px; }
    .btn-filter{
        border:1px solid var(--primary);
        background:var(--primary);
        color:#fff;
        border-radius:var(--radius-sm);
        padding:10px 18px;
        font-size:.9rem;
        font-weight:600;
        cursor:pointer;
    }
    .btn-filter:hover{ background:var(--primary-deep); }
    .btn-clear{
        border:1px solid var(--line-strong);
        background:var(--surface);
        color:var(--ink-soft);
        border-radius:var(--radius-sm);
        padding:10px 16px;
        font-size:.9rem;
        font-weight:600;
        text-decoration:none;
        display:inline-flex;
        align-items:center;
    }
    .btn-clear:hover{ background:var(--paper); color:var(--ink); text-decoration:none; }

    .dashboard-stats{
        flex:1 1 360px;
        display:flex;
    }
    .stat-tile{
        flex:1;
        padding:16px 20px;
        border-left:1px solid var(--line);
        display:flex;
        flex-direction:column;
        justify-content:center;
        gap:4px;
    }
    .stat-tile:first-child{ border-left:none; }
    .stat-value{
        font-family:var(--font-mono);
        font-variant-numeric:tabular-nums;
        font-size:1.55rem;
        font-weight:800;
        line-height:1.1;
    }
    .stat-value.is-amber{ color:var(--amber); }
    .stat-value.is-primary{ color:var(--primary); }
    .stat-label{
        font-size:.79rem;
        color:var(--ink-soft);
    }

    @media (max-width:860px){
        .dashboard-filter{ border-right:none; }
        .dashboard-stats{ border-top:1px solid var(--line); }
    }

    /* ---------- Date groups ---------- */
    .date-group{ margin-bottom:18px; }
    .date-group-header{
        display:flex;
        align-items:center;
        gap:12px;
        width:100%;
        padding:14px 16px;
        border:1px solid var(--line-strong);
        border-radius:var(--radius-md);
        background:var(--surface);
        text-align:left;
    }
    .date-group-header:hover{ background:var(--primary-soft); }
    .date-group.expanded .date-group-header{
        border-radius:var(--radius-md) var(--radius-md) 0 0;
        border-bottom-color:transparent;
    }

    .date-group-toggle{
        display:flex;
        align-items:center;
        gap:12px;
        flex:1;
        min-width:0;
        background:none;
        border:none;
        padding:0;
        margin:0;
        cursor:pointer;
        text-align:left;
        font:inherit;
        color:inherit;
    }
    .date-group-toggle:focus-visible{ outline:2px solid var(--primary); outline-offset:-2px; }
    .date-group-header .btn-print-group{ flex-shrink:0; }

    .date-toggle-icon{
        flex-shrink:0;
        color:var(--primary);
        transition:transform .15s ease;
    }
    .date-group.expanded .date-toggle-icon{ transform:rotate(90deg); }

    .date-group-title{
        font-size:1rem;
        font-weight:800;
        color:var(--ink);
        margin:0;
    }
    .date-group-title-row{
        display:flex;
        align-items:center;
        gap:10px;
        flex:1;
        min-width:0;
    }
    .today-tag{
        flex-shrink:0;
        display:inline-flex;
        align-items:center;
        font-size:.74rem;
        font-weight:700;
        padding:4px 10px 4px 8px;
        border-radius:999px;
        background:var(--primary-soft);
        color:var(--primary);
    }
    .live-dot{
        display:inline-block;
        width:6px; height:6px;
        border-radius:50%;
        background:var(--primary);
        margin-right:6px;
    }
    .date-group-count{
        flex-shrink:0;
        font-family:var(--font-mono);
        font-size:.78rem;
        font-weight:700;
        padding:4px 10px;
        border-radius:999px;
        background:var(--amber-soft);
        color:var(--amber);
        white-space:nowrap;
    }

    .date-collapse{
        display:none;
        border:1px solid var(--line-strong);
        border-top:none;
        border-radius:0 0 var(--radius-md) var(--radius-md);
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

    /* ---------- Box card (vehicle ticket) ---------- */
    .box-card{
        background:var(--surface);
        border:1px solid var(--line-strong);
        border-radius:var(--radius-md);
        overflow:hidden;
        min-width:0;
    }

    .box-card-header{
        width:100%;
        display:flex;
        align-items:flex-start;
        gap:10px;
        padding:12px 14px;
        border:none;
        border-bottom:1px solid var(--line);
        background:var(--paper);
        cursor:pointer;
        text-align:left;
    }
    .box-card-header:hover{ background:var(--primary-soft); }
    .box-card-header:focus-visible{ outline:2px solid var(--primary); outline-offset:-2px; }

    .box-toggle-icon{
        flex-shrink:0;
        margin-top:9px;
        color:var(--ink-faint);
        transition:transform .15s ease;
    }
    .box-card.expanded .box-toggle-icon{ transform:rotate(90deg); }

    .box-avatar{
        flex-shrink:0;
        width:34px;
        height:34px;
        border-radius:50%;
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:800;
        font-size:.95rem;
        background:var(--primary-soft);
        color:var(--primary);
    }
    .box-avatar.is-unassigned{
        background:var(--line);
        color:var(--ink-faint);
    }

    .box-head-main{ flex:1; min-width:0; padding-top:1px; }
    .box-transport-name{
        font-weight:800;
        font-size:.98rem;
        color:var(--ink);
        word-break:break-word;
        display:block;
    }
    .box-driver-name{
        font-weight:600;
        color:var(--primary);
    }
    .box-driver-empty{
        font-weight:400;
        font-size:.85rem;
        color:var(--ink-faint);
        display:block;
        margin-top:1px;
    }

    .box-count-badge{
        flex-shrink:0;
        font-family:var(--font-mono);
        font-size:.76rem;
        font-weight:700;
        padding:4px 10px;
        margin-top:2px;
        border-radius:999px;
        background:var(--amber-soft);
        color:var(--amber);
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
        display:flex;
        align-items:center;
        gap:6px;
        font-size:.82rem;
        color:var(--ink-soft);
        padding:10px 0 4px;
    }
    .box-assigned-by svg{ flex-shrink:0; color:var(--ink-faint); }

    .job-block{ padding:10px 0; border-top:1px solid var(--line); }
    .job-block:first-child{ border-top:none; }
    .job-customer-code{
        font-family:var(--font-mono);
        font-weight:700;
        font-size:.9rem;
        color:var(--ink);
    }
    .job-customer-name{
        font-weight:500;
        color:var(--ink-soft);
    }

    /* Simple, scannable list of bills per customer */
    .stops-list{
        margin-top:6px;
        border-top:1px solid var(--line);
        border-radius:6px;
        overflow:hidden;
    }
    .stop-row{
        display:flex;
        align-items:baseline;
        gap:10px;
        padding:7px 8px;
        border-bottom:1px solid var(--line);
    }
    .job-seq-marker{
        flex-shrink:0;
        width:32px;
        text-align:right;
        font-family:var(--font-mono);
        font-weight:700;
        font-size:.78rem;
        color:var(--ink-faint);
    }
    .job-bill-no{ font-size:.88rem; color:var(--ink); font-weight:500; }

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
        border-radius:var(--radius-sm);
        border:1px solid var(--primary);
        background:var(--primary);
        color:#fff;
        font-size:.86rem;
        font-weight:700;
        text-decoration:none;
    }
    .btn-print-group:hover{ background:var(--primary-deep); border-color:var(--primary-deep); color:#fff; text-decoration:none; }
    .btn-print-group svg{ flex-shrink:0; }

    /* ---------- Empty states ---------- */
    .empty-note{
        font-size:.94rem;
        color:var(--ink-soft);
        padding:26px;
        border:1px dashed var(--line-strong);
        border-radius:var(--radius-md);
        background:var(--surface);
        text-align:center;
    }
    .empty-state-global{
        display:flex;
        flex-direction:column;
        align-items:center;
        gap:10px;
        font-size:.95rem;
        color:var(--ink-soft);
        padding:44px 32px;
        border:1px dashed var(--line-strong);
        border-radius:var(--radius-md);
        background:var(--surface);
        text-align:center;
    }
    .empty-state-global svg{ color:var(--ink-faint); }
    .empty-state-global strong{ color:var(--ink); font-size:1rem; }

    /* ---------- Toast Notification Styles ---------- */
    .toast-container {
        pointer-events: none;
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .toast-container > * {
        pointer-events: auto;
    }
    .custom-toast {
        min-width: 300px;
        max-width: 400px;
        border-radius: var(--radius-md);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        border: none;
        overflow: hidden;
        animation: slideInRight 0.3s ease-out;
        background: var(--surface);
    }
    .custom-toast.toast-success { border-left: 4px solid var(--success); }
    .custom-toast.toast-error { border-left: 4px solid var(--danger); }
    .custom-toast.toast-warning { border-left: 4px solid var(--amber); }
    .custom-toast.toast-info { border-left: 4px solid var(--primary); }

    .toast-header {
        padding: 12px 16px;
        border-bottom: 1px solid var(--line);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.95rem;
    }
    .toast-success .toast-header { background: var(--success-soft); color: var(--success); }
    .toast-error .toast-header { background: var(--danger-soft); color: var(--danger); }
    .toast-warning .toast-header { background: var(--amber-soft); color: var(--amber); }
    .toast-info .toast-header { background: var(--primary-soft); color: var(--primary); }

    .toast-body {
        padding: 12px 16px;
        font-size: 0.95rem;
        color: var(--ink);
    }
    .toast-icon { display:inline-flex; flex-shrink:0; }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    .toast.hiding { animation: slideOutRight 0.3s ease-out; }

    .toast-close {
        background: none;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0.6;
        transition: opacity 0.2s;
        padding: 0;
        line-height: 1;
        margin-left: auto;
        color: inherit;
    }
    .toast-close:hover { opacity: 1; }

    /* ---------- Accessibility & motion ---------- */
    *:focus-visible{ outline:2px solid var(--primary); outline-offset:2px; }
    @media (prefers-reduced-motion: reduce){
        *{ transition:none !important; animation:none !important; }
    }

    @media (max-width:768px){
        .page-shell{ padding:20px 16px 48px; }
        .page-header{ flex-direction:column; align-items:flex-start; }
        .page-header-user{ align-items:flex-start; width:100%; }
        .toast-container { left: 16px; right: 16px; top: 16px; }
        .custom-toast { min-width: auto; max-width: 100%; }
    }
/* ── แท็บ ส่งของ / รับของ ── */
.summary-tabs{ display:flex; gap:8px; margin:0 0 16px; }
.summary-tab{
    padding:9px 20px; border:1px solid #d0d5dd; border-radius:8px; background:#fff;
    font-weight:600; font-size:14px; color:#475467; cursor:pointer; transition:all .15s;
}
.summary-tab.active{ background:#2853d5; border-color:#2853d5; color:#fff; }
/* กรองรายการตามโหมด */
.summary-mode-delivery .stop-row[data-type="po"]{ display:none !important; }
.summary-mode-pickup .stop-row:not([data-type="po"]){ display:none !important; }
.summary-mode-pickup .box-print-all{ display:none !important; }  /* ปุ่มปริ้นใบงานส่งของ ซ่อนในโหมดรับของ */
.summary-mode-delivery .pickup-select{ display:none; }
/* งานรับของที่ครบแล้ว (รับเข้าแล้ว = เขียว, กดปริ้นไม่ได้) */
.pickup-done{ color:#0f7a3d; font-weight:600; }
.pickup-done-badge{
    display:inline-block; margin-left:6px; padding:1px 8px; border-radius:10px;
    background:#d1fadf; color:#0f7a3d; font-size:11px; font-weight:600;
}
.pickup-select{ margin-right:6px; cursor:pointer; }
.pickup-select:disabled{ cursor:not-allowed; }
/* แถบปริ้นที่เลือก (ลอยล่าง) */
.pickup-print-bar{
    position:fixed; left:50%; bottom:20px; transform:translateX(-50%);
    display:flex; gap:14px; align-items:center; z-index:900;
    background:#fff; border:1px solid #e0e0e0; box-shadow:0 6px 24px rgba(0,0,0,.14);
    padding:10px 18px; border-radius:12px; font-size:14px;
}
</style>
</head>
<body>

<!-- Toast Notification Container -->
<div id="toastContainer" class="toast-container"></div>

<div class="page-shell">

    <div class="page-header">
        <div class="page-title-row">
            <span class="page-mark">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M1.5 6.5h11v9h-11v-9Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M12.5 9.5h4l3 3v3h-7v-6Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><circle cx="5.5" cy="17.3" r="1.6" stroke="currentColor" stroke-width="1.4"/><circle cx="16.5" cy="17.3" r="1.6" stroke="currentColor" stroke-width="1.4"/></svg>
            </span>
            <div>
                <h1 class="page-title">สรุปงานคนขับ</h1>
                <div class="page-subtitle">งานที่จัดส่งแล้ว แยกตามวันที่และคนขับ</div>
            </div>
        </div>
        <div class="page-header-user">
            <div class="user-line">ผู้ใช้งาน: <strong>{{ $loggedInName }}</strong></div>
            <a href="{{ route('deliverytrack') }}" class="btn-ghost">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                กลับไปหน้าจ่ายงาน
            </a>
        </div>
    </div>

    @php
        $todayKey = date('Y-m-d');

        $totalItemsAll = 0;
        $totalBoxesAll = 0;
        $driverSet = [];
        foreach ($boxesByDate as $dk => $dboxes) {
            $totalBoxesAll += count($dboxes);
            foreach ($dboxes as $dbox) {
                $totalItemsAll += $dbox['total_items'];
                if (!empty($dbox['driver_name'])) {
                    $driverSet[$dbox['driver_name']] = true;
                }
            }
        }
        $totalDriversAll = count($driverSet);
    @endphp

    <div class="dashboard-bar">
        <form method="GET" action="{{ route('deliverytrack.summary') }}" class="dashboard-filter">
            <div class="filter-field">
                <label for="filterDate">กรองตามวันที่ (ส่งของ=วันจัดส่ง · รับเอง=วันจ่ายงาน)</label>
                <input type="date" id="filterDate" name="date" value="{{ ($billId ?? '') !== '' ? $date : ($date ?: $todayKey) }}">
            </div>
            <div class="filter-field">
                <label for="filterBillId">ค้นหาเลขบิล / SO / PO</label>
                <input type="text" id="filterBillId" name="bill_id" value="{{ $billId ?? '' }}" placeholder="เช่น 6901-01149" autocomplete="off"
                       style="border:1px solid var(--line-strong);border-radius:var(--radius-sm);padding:9px 12px;font-size:.95rem;font-family:inherit;color:var(--ink);background:var(--surface);min-width:190px;">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter">กรอง</button>
                @if ($date || ($billId ?? '') !== '')
                    <a href="{{ route('deliverytrack.summary') }}" class="btn-clear">ล้างตัวกรอง</a>
                @endif
            </div>
        </form>

        <div class="dashboard-stats">
            <div class="stat-tile">
                <div class="stat-value is-amber">{{ number_format($totalItemsAll) }}</div>
                <div class="stat-label">รายการจัดส่งทั้งหมด</div>
            </div>
            <div class="stat-tile">
                <div class="stat-value is-amber">{{ number_format($totalBoxesAll) }}</div>
                <div class="stat-label">กลุ่มงาน (รถ/คนขับ)</div>
            </div>
            <div class="stat-tile">
                <div class="stat-value is-primary">{{ number_format($totalDriversAll) }}</div>
                <div class="stat-label">คนขับที่ปฏิบัติงาน</div>
            </div>
        </div>
    </div>

    <div class="summary-tabs">
        <button type="button" class="summary-tab active" data-mode="company" onclick="switchSummaryMode('company')">🚚 ขนส่งโดยบริษัท</button>
        <button type="button" class="summary-tab" data-mode="private" onclick="switchSummaryMode('private')">🏢 ขนส่งเอกชน</button>
        <button type="button" class="summary-tab" data-mode="pickup" onclick="switchSummaryMode('pickup')">📦 รับของเอง</button>
    </div>

    <div id="summaryContent" class="summary-mode-delivery">
    @forelse ($boxesByDate as $dateKey => $boxes)
        @php
            $dateKeyId = 'date_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $dateKey);
            $isDefaultOpen = count($boxesByDate) === 1 || $dateKey === $todayKey || ($billId ?? '') !== '';

            $isValidDate = (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateKey);
            $displayDate = $dateKey;
            if ($isValidDate) {
                $thaiDays = ['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'];
                $thaiMonths = ['','มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
                $ts = strtotime($dateKey);
                $displayDate = 'วัน' . $thaiDays[date('w', $ts)] . 'ที่ ' . date('j', $ts) . ' ' . $thaiMonths[(int) date('n', $ts)] . ' ' . (date('Y', $ts) + 543);
            }
        @endphp
        <div class="date-group {{ $isDefaultOpen ? 'expanded' : '' }}" id="{{ $dateKeyId }}">
            <div class="date-group-header">
                <button type="button" class="date-group-toggle" onclick="toggleDateGroup('{{ $dateKeyId }}')" aria-expanded="{{ $isDefaultOpen ? 'true' : 'false' }}" aria-controls="{{ $dateKeyId }}_panel">
                    <svg class="date-toggle-icon" width="14" height="14" viewBox="0 0 16 16" fill="none">
                        <path d="M5 3l6 5-6 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="date-group-title-row">
                        <h2 class="date-group-title">{{ $displayDate }}</h2>
                        @if ($isValidDate && $dateKey === $todayKey)
                            <span class="today-tag"><span class="live-dot"></span>วันนี้</span>
                        @endif
                    </span>
                </button>

                <span class="date-group-count">{{ collect($boxes)->sum('total_items') }} รายการ</span>

                @if (count($boxes) > 0)
                    <a class="btn-print-group" target="_blank" href="{{ route('deliverytrack.printAllGroups', ['date' => $dateKey]) }}">
                        <svg width="15" height="15" viewBox="0 0 16 16" fill="none"><path d="M4 6V2h8v4M4 11H2.75A.75.75 0 0 1 2 10.25v-3.5A.75.75 0 0 1 2.75 6h10.5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-.75.75H12M4 9h8v5H4V9Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        ปริ้นทั้งหมด ({{ count($boxes) }} คัน)
                    </a>
                @endif
            </div>

            <div class="date-collapse" id="{{ $dateKeyId }}_panel">
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
                                        $hasDriver = !empty($box['driver_name']);
                                        $driverInitial = $hasDriver ? mb_substr($box['driver_name'], 0, 1) : '?';
                                    @endphp
                                    <div class="box-card {{ $hasDriver ? '' : 'box-unassigned' }}" id="{{ $boxKey }}">
                                        <button type="button" class="box-card-header" onclick="toggleBoxCard('{{ $boxKey }}')" aria-expanded="false" aria-controls="{{ $boxKey }}_panel">
                                            <svg class="box-toggle-icon" width="14" height="14" viewBox="0 0 16 16" fill="none">
                                                <path d="M5 3l6 5-6 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <span class="box-avatar {{ $hasDriver ? '' : 'is-unassigned' }}">{{ $driverInitial }}</span>
                                            <span class="box-head-main">
                                                <span class="box-transport-name">{{ $box['transport_name'] }}</span>
                                                @if ($hasDriver)
                                                    <span class="box-driver-name">{{ $box['driver_name'] }}</span>
                                                @else
                                                    <span class="box-driver-empty">ยังไม่ระบุคนขับ</span>
                                                @endif
                                            </span>
                                            <span class="box-count-badge">{{ $box['total_items'] }} รายการ</span>
                                        </button>

                                        <div class="box-card-collapse">
                                            <div class="box-card-collapse-inner" id="{{ $boxKey }}_panel">
                                                <div class="box-card-body">
                                                    <div class="box-assigned-by">
                                                        <svg width="13" height="13" viewBox="0 0 16 16" fill="none"><path d="M2 13.5c0-2.5 2.2-4 6-4s6 1.5 6 4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><circle cx="8" cy="5.5" r="2.5" stroke="currentColor" stroke-width="1.3"/></svg>
                                                        ผู้จ่ายงาน: {{ $box['assigned_by'] ?: '-' }}
                                                    </div>

                                                    @foreach ($box['customers'] as $cust)
                                                        <div class="job-block">
                                                            <div>
                                                                <span class="job-customer-code">{{ $cust['customer_code'] }}</span>
                                                            </div>
                                                            <div class="stops-list">
                                                                @foreach ($cust['items'] as $item)
                                                                    @php $isPo = ($item['type'] ?? '') === 'po'; $isDone = !empty($item['is_complete']); @endphp
                                                                    <div class="stop-row" data-type="{{ $item['type'] ?? '' }}" data-complete="{{ $isDone ? 1 : 0 }}" data-transport="{{ $item['transport_type'] ?? '' }}">
                                                                        @if ($isPo)
                                                                            <input type="checkbox" class="pickup-select" value="{{ $item['id'] }}"
                                                                                   {{ $isDone ? 'disabled' : '' }} onchange="updatePickupCount()">
                                                                        @endif
                                                                        <span class="job-seq-marker">{{ $item['seq'] }}</span>
                                                                        @if ($isPo)
                                                                            <span class="job-bill-no {{ $isDone ? 'pickup-done' : '' }}">ไปรับเอง PO {{ $item['bill_no'] }}</span>
                                                                            @if ($isDone)<span class="pickup-done-badge">รับเข้าแล้ว</span>@endif
                                                                        @else
                                                                            <span class="job-bill-no">บิล {{ $item['bill_no'] }}</span>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    <div class="box-print-all">
                                                        <a class="btn-print-group" target="_blank" href="{{ $printUrl }}">
                                                            <svg width="15" height="15" viewBox="0 0 16 16" fill="none"><path d="M4 6V2h8v4M4 11H2.75A.75.75 0 0 1 2 10.25v-3.5A.75.75 0 0 1 2.75 6h10.5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-.75.75H12M4 9h8v5H4V9Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                            ปริ้นใบงาน (A4)
                                                        </a>
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
        <div class="empty-state-global">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M3 16.5V6a2 2 0 0 1 2-2h9.5L21 8.5V18a2 2 0 0 1-2 2H8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 16.5 6 13l2.5 2.5L12 12" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <strong>ยังไม่มีงานที่จัดส่งแล้ว{{ $date ? 'ในวันที่เลือก' : '' }}</strong>
            <span>ลองเลือกวันที่อื่นจากตัวกรองด้านบน หรือกลับไปหน้าจ่ายงานเพื่อดูสถานะปัจจุบัน</span>
        </div>
    @endforelse
    </div>{{-- /#summaryContent --}}

    <div class="pickup-print-bar" id="pickupPrintBar" style="display:none;">
        <span>เลือกงานรับของ <strong id="pickupCount">0</strong> รายการ</span>
        <button type="button" class="btn-filter" onclick="printSelectedPickup()">🖨️ ปริ้นที่เลือก</button>
    </div>

</div>

<form id="pickupPrintForm" method="POST" action="{{ route('deliverytrack.printSelectedPickup') }}" target="_blank" style="display:none;">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <div id="pickupPrintInputs"></div>
</form>

<script>
function switchSummaryMode(mode){
    const el = document.getElementById('summaryContent');
    if(el){ el.className = 'summary-mode-' + mode; }
    document.querySelectorAll('.summary-tab').forEach(t => t.classList.toggle('active', t.dataset.mode === mode));
    document.getElementById('pickupPrintBar').style.display = (mode === 'pickup') ? 'flex' : 'none';

    // แสดง/ซ่อนราย row: pickup = งานไปรับเอง (po), company/private = บิลส่งของตามประเภทขนส่ง (null→company)
    function rowVisible(row){
        const type = row.dataset.type || '';
        if(mode === 'pickup') return type === 'po';
        if(type === 'po') return false;
        const t = (row.dataset.transport === 'private') ? 'private' : 'company';
        return t === mode;
    }
    document.querySelectorAll('.stop-row').forEach(row => { row.style.display = rowVisible(row) ? '' : 'none'; });

    // ซ่อน block/box/date-group ที่ไม่มี row มองเห็น
    document.querySelectorAll('.job-block').forEach(bl => {
        const rows = Array.from(bl.querySelectorAll('.stop-row')).filter(r => r.style.display !== 'none');
        bl.style.display = rows.length ? '' : 'none';
    });
    document.querySelectorAll('.box-card').forEach(bc => {
        const visBlocks = Array.from(bc.querySelectorAll('.job-block')).filter(b => b.style.display !== 'none');
        bc.style.display = visBlocks.length ? '' : 'none';
    });
    document.querySelectorAll('.date-group').forEach(dg => {
        const visBoxes = Array.from(dg.querySelectorAll('.box-card')).filter(b => b.style.display !== 'none');
        dg.style.display = visBoxes.length ? '' : 'none';
    });
    if(mode === 'pickup') updatePickupCount();
}
function updatePickupCount(){
    const n = document.querySelectorAll('.pickup-select:checked').length;
    const c = document.getElementById('pickupCount');
    if(c) c.textContent = n;
}
function printSelectedPickup(){
    const checked = Array.from(document.querySelectorAll('.pickup-select:checked'));
    if(!checked.length){ alert('กรุณาเลือกงานรับของอย่างน้อย 1 รายการ'); return; }
    const wrap = document.getElementById('pickupPrintInputs');
    wrap.innerHTML = '';
    checked.forEach(cb => {
        const i = document.createElement('input');
        i.type = 'hidden'; i.name = 'ids[]'; i.value = cb.value;
        wrap.appendChild(i);
    });
    document.getElementById('pickupPrintForm').submit();
}
// เช็คว่าโหมดนั้นมี row ให้เห็นไหม (ใช้ตอนค้นด้วยเลขบิลเพื่อเด้งไปแท็บที่มีผล)
function modeHasRows(mode){
    return Array.from(document.querySelectorAll('.stop-row')).some(row => {
        const type = row.dataset.type || '';
        if(mode === 'pickup') return type === 'po';
        if(type === 'po') return false;
        const t = (row.dataset.transport === 'private') ? 'private' : 'company';
        return t === mode;
    });
}
// ตั้งค่าเริ่มต้น: ปกติโหมดส่งของ — แต่ถ้าค้นด้วยเลขบิล ให้เด้งไปแท็บแรกที่มีผลลัพธ์
document.addEventListener('DOMContentLoaded', function(){
    const hasBillSearch = @json(($billId ?? '') !== '');
    let initial = 'company';
    if (hasBillSearch) {
        initial = ['company','private','pickup'].find(m => modeHasRows(m)) || 'company';
    }
    switchSummaryMode(initial);
});

function toggleBoxCard(id) {
    const card = document.getElementById(id);
    if (!card) return;
    const btn = card.querySelector('.box-card-header');
    const expanded = card.classList.toggle('expanded');
    if (btn) btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
}

function toggleDateGroup(id) {
    const group = document.getElementById(id);
    if (!group) return;
    const btn = group.querySelector('.date-group-toggle');
    const expanded = group.classList.toggle('expanded');
    if (btn) btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
}

// ==========================================
// Toast Notification System (Pure Vanilla JS)
// ==========================================
function showToast(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toastContainer');
    const toastId = 'toast-' + Date.now();

    const icons = {
        success: '<svg width="18" height="18" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.4"/><path d="M5.2 8.2l1.8 1.8 3.8-3.8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        error: '<svg width="18" height="18" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.4"/><path d="M6 6l4 4M10 6l-4 4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>',
        warning: '<svg width="18" height="18" viewBox="0 0 16 16" fill="none"><path d="M8 1.5 15 13.5H1L8 1.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M8 6.5v3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><circle cx="8" cy="11.3" r=".8" fill="currentColor"/></svg>',
        info: '<svg width="18" height="18" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.4"/><path d="M8 7.2v4M8 5v.01" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>'
    };

    const titles = {
        success: 'สำเร็จ',
        error: 'เกิดข้อผิดพลาด',
        warning: 'คำเตือน',
        info: 'ข้อมูล'
    };

    const toastHTML = `
        <div id="${toastId}" class="custom-toast toast-${type}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <span class="toast-icon">${icons[type]}</span>
                <strong class="me-auto">${titles[type]}</strong>
                <button type="button" class="toast-close" onclick="hideToast('${toastId}')" aria-label="Close">&times;</button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', toastHTML);

    if (duration > 0) {
        setTimeout(() => {
            hideToast(toastId);
        }, duration);
    }
}

function hideToast(toastId) {
    const toastElement = document.getElementById(toastId);
    if (toastElement) {
        toastElement.classList.add('hiding');
        setTimeout(() => {
            toastElement.remove();
        }, 300);
    }
}
</script>
</body>
</html>