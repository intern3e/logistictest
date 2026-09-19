<!DOCTYPE html>
{{-- resources/views/sale/dashboardshelf.blade.php  (ชั้น SALE — รอเช็คเอาท์) --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ชั้น SALE</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #1e293b;
            --canvas: #ffffff;
            --muted: #6b7280;
            --border: #dcdcdc;
            --primary: #2853d5;
            --primary-dark: #1d4ed8;
            --primary-light: #eff6ff;
            --on-primary: #ffffff;
            --success: #16a34a;
            --success-dark: #15803d;
            --danger: #dc2626;
            --danger-dark: #b91c1c;
            --danger-light: #fef2f2;
            --warning: #ea580c;
            --row-hover: #f0f7ff;
            --row-done: #f8fafc;
            --page-bg: #eef2f7;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.04);
            --shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
            --radius: 10px;
            --radius-sm: 6px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            background: var(--page-bg);
            font-family: 'Noto Sans Thai', 'Segoe UI', Tahoma, Arial, sans-serif;
            font-size: 14px;
            color: var(--ink);
            line-height: 1.5;
            width: 100%;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        .page-frame {
            width: 100%;
            max-width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-banner {
            background: var(--canvas);
            border-bottom: 1px solid var(--border);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: nowrap;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
            width: 100%;
        }

        .title-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .title-group .h1 {
            font-weight: 700;
            font-size: 18px;
            color: var(--ink);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .title-group .h1::before {
            content: '';
            display: inline-block;
            width: 3px;
            height: 18px;
            background: var(--primary);
            border-radius: 2px;
        }

        .sticker {
            background: var(--primary-light);
            color: var(--primary-dark);
            border: 1px solid #bfdbfe;
            font-weight: 600;
            font-size: 10px;
            padding: 3px 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 12px;
        }

        .user-badge {
            font-size: 13px;
            font-weight: 600;
            color: var(--on-primary);
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--primary);
            padding: 6px 14px;
            border-radius: 16px;
            white-space: nowrap;
        }

        .user-badge::before {
            content: '';
            width: 7px;
            height: 7px;
            background: var(--success);
            border-radius: 50%;
            box-shadow: 0 0 0 2px rgba(255,255,255,0.3);
        }

        main {
            padding: 16px 24px 40px;
            width: 100%;
            flex: 1;
        }

        .toolbar {
            background: var(--canvas);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 16px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
            box-shadow: var(--shadow-sm);
            width: 100%;
        }

        .toolbar .filter-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
            flex: 1;
        }

        .toolbar input[type="search"],
        .toolbar input[type="text"],
        .toolbar input[type="date"],
        .toolbar select {
            padding: 7px 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-family: inherit;
            font-size: 13px;
            background: var(--canvas);
            color: var(--ink);
            transition: all 0.2s;
            min-width: 150px;
            white-space: nowrap;
        }
        .toolbar input[type="date"] { min-width: 140px; }
        .btn-primary {
            padding: 7px 16px; border: none; border-radius: 6px; cursor: pointer;
            font-family: inherit; font-size: 13px; font-weight: 600;
            background: var(--primary); color: #fff;
        }
        .btn-primary:hover { filter: brightness(.95); }
        .filter-check { display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--ink); white-space:nowrap; }
        .due-badge { display:inline-block; margin-left:6px; padding:1px 7px; border-radius:999px; font-size:11px; font-weight:600; }
        .due-ok  { background:#e7f5ec; color:#1a7f3c; }
        .due-warn{ background:#fdecea; color:#c0392b; }
        .price-cell { text-align:right; font-variant-numeric:tabular-nums; white-space:nowrap; }
        .due-overdue { color:#c0392b; font-weight:800; }
        .due-today   { color:#b7791f; font-weight:600; }
        .due-upcoming{ color:#1a7f3c; }

        @keyframes softRedPulse {
            0%, 100% { background-color: #fff5f5; color: #c53030; font-weight: 600; }
            50% { background-color: #fed7d7; color: #9b2c2c; font-weight: 700; }
        }
        .blink-overdue-cell {
            animation: softRedPulse 1.5s ease-in-out infinite;
            border-left: 3px solid var(--danger);
            text-align: center;
        }
        tr.row-today > td { background: #fffbeb; }
        
        @media (prefers-reduced-motion: reduce) {
            .blink-overdue-cell { animation: none; background-color: #fed7d7; color: #9b2c2c; font-weight: 700; }
        }

        .banner-right { display:flex; align-items:center; gap:12px; flex-shrink:0; }
        @keyframes alertPulse { 0%,100%{ box-shadow:0 0 0 0 rgba(192,57,43,.45); } 50%{ box-shadow:0 0 0 6px rgba(192,57,43,0); } }
        .overdue-alert {
            display:inline-flex; align-items:center; gap:7px;
            background:var(--danger); color:#fff; font-weight:800; font-size:13.5px;
            padding:7px 14px; border-radius:999px; white-space:nowrap;
            animation: alertPulse 1.3s ease-in-out infinite;
        }
        .overdue-alert .oa-icon {
            display:inline-flex; align-items:center; justify-content:center;
            width:18px; height:18px; border-radius:50%; background:#fff; color:var(--danger);
            font-weight:900; font-size:13px;
        }
        .value-block {
            display:flex; flex-direction:column; align-items:flex-end; line-height:1.15;
            background:var(--danger-light); border:1.5px solid var(--danger); border-radius:10px;
            padding:6px 14px; white-space:nowrap;
        }
        .value-block .vb-label { font-size:11px; font-weight:700; color:var(--danger-dark); letter-spacing:.3px; }
        .value-block .vb-amount { font-size:18px; font-weight:800; color:var(--danger); font-variant-numeric:tabular-nums; }
        .value-block .vb-amount .vb-unit { font-size:12px; font-weight:600; margin-left:2px; }
        @media (max-width:768px){
            .banner-right { flex-wrap:wrap; gap:8px; }
            .value-block { padding:4px 10px; }
            .value-block .vb-amount { font-size:15px; }
        }
        .btn-view { padding:4px 12px; border:1px solid var(--border); border-radius:6px; background:var(--canvas); color:var(--ink); font-family:inherit; font-size:12.5px; cursor:pointer; }
        .btn-view:hover { background:#f3f4f6; }
        .sub-table { width:100%; background:#fff; border-collapse:collapse; table-layout: fixed; }
        .sub-table th, .sub-table td { border:1px solid #e5e7eb; padding:10px 14px; font-size:13.5px; text-align:center; color:#111827; }
        .sub-table td { word-break: break-word; }
        .sub-table th { background:#eef2f7; color:#111827; }
        .btn-move { border-color:#3E6AE1 !important; color:#3E6AE1 !important; }
        .btn-checkout { border-color:#c0392b !important; color:#c0392b !important; }
        
        /* Modal Styles */
        .modal-overlay { 
            display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:200; 
            align-items:center; justify-content:center; backdrop-filter: blur(2px);
        }
        .modal-box { 
            background:#fff; border-radius:12px; padding:20px; width:min(92vw, 600px); 
            box-shadow:0 10px 40px rgba(0,0,0,.2); display: flex; flex-direction: column;
            max-height: 85vh;
        }
        .modal-box-lg {
            width: min(94vw, 980px);
            max-height: 88vh;
        }
        .modal-title { 
            font-weight:600; margin-bottom:16px; color:var(--ink); 
            display: flex; justify-content: space-between; align-items: center;
        }
        .modal-body {
            overflow-y: auto;
            flex: 1;
            padding-right: 4px;
        }
        .modal-actions { display:flex; gap:10px; justify-content:flex-end; margin-top: 16px; }
        .modal-box input { width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-family:inherit; font-size:14px; margin-bottom:14px; box-sizing:border-box; }
        
        .autocomplete-wrap { position:relative; display:inline-block; }
        .suggest-panel {
            display:none; position:absolute; left:0; right:0; top:calc(100% + 4px);
            background:var(--canvas); border:1px solid var(--border); border-radius:6px;
            box-shadow:0 8px 24px rgba(0,0,0,.12); max-height:min(320px,45vh); overflow-y:auto; z-index:9999;
        }
        .suggest-panel.open { display:block; }
        .suggest-item { padding:9px 14px; font-size:13px; color:var(--ink); cursor:pointer; border-bottom:1px solid #f1f5f9; }
        .suggest-item:last-child { border-bottom:none; }
        .suggest-item:hover, .suggest-item.hl { background:#eef2ff; color:var(--primary); }
        .suggest-empty { padding:12px 14px; font-size:13px; color:#6b7280; text-align:center; }

        .toolbar input[type="search"]:focus,
        .toolbar input[type="text"]:focus,
        .toolbar input[type="date"]:focus,
        .toolbar select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px var(--primary-light);
        }

        .toolbar input[type="search"]::placeholder {
            color: #9ca3af;
        }

        button {
            padding: 7px 16px;
            border: 1px solid transparent;
            border-radius: 6px;
            font-family: inherit;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        .btn-ghost {
            background: var(--canvas);
            color: var(--muted);
            border-color: var(--border);
        }

        .btn-ghost:hover {
            background: #f3f4f6;
            color: var(--ink);
        }

        .table-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
            flex-wrap: wrap;
            width: 100%;
        }

        .table-info {
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-info::before {
            content: '';
            width: 7px;
            height: 7px;
            background: var(--warning);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .table-scroll {
            background: var(--canvas);
            border-radius: 8px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: var(--shadow);
            width: 100%;
        }

        .table-inner {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
            background: var(--canvas);
        }

        /* ตอนไม่มีข้อมูล (เฉพาะแถวข้อความ) ให้ตารางหดตามพื้นที่ที่มองเห็นจริง
           จะได้ไม่บังคับ scroll แนวนอนที่ 900px แล้วทำให้ข้อความเยื้องศูนย์ */
        table.is-empty {
            min-width: 0;
        }

        th, td {
            border-bottom: 1px solid var(--border);
            border-right: 1px solid var(--border);
            padding: 10px 12px;
            text-align: center;
            font-size: 13px;
            vertical-align: middle;
        }

        th:last-child, td:last-child {
            border-right: none;
        }

        thead th {
            background: var(--primary);
            color: var(--on-primary);
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.3px;
            border-bottom: 2px solid var(--primary-dark);
            border-right-color: rgba(255,255,255,0.2);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        tbody tr {
            transition: background 0.15s ease;
        }

        tbody tr:nth-child(even) {
            background: #fafbfd;
        }

        tbody tr:hover {
            background: var(--row-hover);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tr.hidden-row { display: none; }

        .num {
            font-variant-numeric: tabular-nums;
            font-weight: 500;
        }

        .cust-cell {
            text-align: left;
            font-weight: 500;
        }

        .ref-link {
            font-weight: 700;
            color: var(--primary-dark);
            font-size: 13px;
        }

        .shelf-badge {
            font-weight: 700;
            font-size: 12px;
            color: var(--primary-dark);
        }

        .days-badge {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
            background: #e8f5e9;
            color: var(--success-dark);
            margin-left: 6px;
        }

        td.overdue {
            background: var(--danger-light) !important;
            color: var(--danger-dark);
            font-weight: 700;
        }
        td.overdue .days-badge {
            background: var(--danger);
            color: #fff;
        }

        .recv-time { white-space: nowrap; }

        /* ===== ข้อความตรงกลางตาราง ===== */
        .empty-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 500px;
            width: 100%;
        }

        .empty-state {
            text-align: center;
            color: var(--muted);
            font-size: 15px;
            font-style: italic;
        }

        tbody tr td[colspan] {
            padding: 0;
        }

        .dash { color: var(--muted); }

        @media (max-width: 768px) {
            .top-banner { padding: 10px 16px; }
            main { padding: 14px 14px 32px; }
            .toolbar { padding: 10px 12px; }
            .toolbar input[type="search"],
            .toolbar select { min-width: 100%; flex: 1 1 100%; }
            .toolbar .filter-group { flex-wrap: wrap; }
            .title-group .h1 { font-size: 16px; }
            .empty-wrapper { height: 320px; }
        }
    </style>
</head>
<body>
<div class="page-frame">
    <div class="top-banner">
        <div class="title-group">
            <span class="h1">ชั้น SALE</span>
            <span class="sticker">SALE</span>
        </div>
        <div class="banner-right">
            <div class="overdue-alert" id="overdueAlert" style="display:none;">
                <span class="oa-icon">!</span> เลยกำหนด <b id="overdueCount">0</b> รายการ
            </div>
            @if(($canSeePrice ?? false))
                <div class="value-block">
                    <span class="vb-label">มูลค่าทั้งหมด</span>
                    <span class="vb-amount"><span id="totalValue">0.00</span><span class="vb-unit">บาท</span></span>
                </div>
            @endif
            <div class="user-badge">ผู้ใช้งาน: {{ $creator }}</div>
        </div>
    </div>

    <main>
        <div class="toolbar">
            <div class="filter-group">
                <div class="autocomplete-wrap">
                    <input type="search" id="fShelf" placeholder=" ค้นหาโดยชั้น..." autocomplete="off">
                    <div id="shelfSuggest" class="suggest-panel"></div>
                </div>
                <datalist id="shelfList">
                    @foreach($shelfOptions as $sh)
                        <option value="{{ $sh }}"></option>
                    @endforeach
                </datalist>
                @if(($isSaleView ?? false))
                    <input type="search" id="fSale" value="{{ $loginName ?? '' }}" readonly
                           title="เห็นเฉพาะงานของคุณ" style="background:#f3f4f6;color:#6b7280;cursor:not-allowed;">
                @else
                    <div class="autocomplete-wrap">
                        <input type="search" id="fSale" placeholder=" ค้นหาโดย Sale (createdBy)..." autocomplete="off">
                        <div id="saleSuggest" class="suggest-panel"></div>
                    </div>
                @endif
                <input type="search" id="fSo" placeholder=" ค้นหาโดย SO..." autocomplete="off">
                <input type="search" id="fPo" placeholder=" ค้นหาโดย PO..." autocomplete="off">
                <button type="button" class="btn-primary" id="btnSearch">ค้นหา</button>
                <button type="button" class="btn-ghost" id="btnClear">ล้าง</button>
            </div>
        </div>

        <div class="table-topbar">
            <div class="table-info">
                รอเช็คเอาท์ <span id="showCount">0</span> รายการ
                &nbsp;•&nbsp; <b>ช่องกำหนดส่งกระพริบแดง</b> = เลยกำหนดส่ง (เรียงงานเลยกำหนดมากสุดไว้บน) / เหลือง = ครบวันนี้
            </div>
        </div>

        <div class="table-scroll">
            <div class="table-inner">
                @php $colspan = 7 + (($canSeePrice ?? false) ? 1 : 0) + (($canManage ?? false) ? 1 : 0); @endphp
                <table id="mainTable" class="is-empty">
                    <thead>
                        <tr>
                            <th>ชั้นวาง</th>
                            <th>กำหนดส่ง</th>
                            <th>สถานะ</th>
                            <th>SO</th>
                            <th>PO</th>
                            @if(($isSaleView ?? false))
                                <th style="text-align:left;">ลูกค้า</th>
                            @else
                                <th>Sale</th>
                            @endif
                            @if(($canSeePrice ?? false))
                                <th style="text-align:right;">มูลค่า</th>
                            @endif
                            <th>สินค้า</th>
                            @if(($canManage ?? false))
                                <th>จัดการ</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="{{ $colspan }}"><div class="empty-wrapper"><div class="empty-state">เลือกตัวกรอง (ชั้น / Sale / SO / PO) แล้วกด "ค้นหา"</div></div></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

@if(($canManage ?? false))
<!-- Modal ย้ายชั้น -->
<div id="moveModal" class="modal-overlay">
    <div class="modal-box" style="width: min(92vw, 380px);">
        <div class="modal-title">
            <span>ย้ายชั้น — <span id="movePoLabel"></span></span>
            <button type="button" class="btn-ghost" onclick="closeMove()" style="padding: 4px 8px;">✕</button>
        </div>
        <input type="search" id="moveShelfInput" list="shelfList" placeholder="เลือกชั้น..." autocomplete="off">
        <div class="modal-actions">
            <button type="button" class="btn-ghost" onclick="closeMove()">ยกเลิก</button>
            <button type="button" class="btn-primary" id="moveConfirmBtn" onclick="confirmMove()">ยืนยัน</button>
        </div>
    </div>
</div>

<!-- Modal ดูสินค้า (Popup) -->
<div id="productModal" class="modal-overlay">
    <div class="modal-box modal-box-lg">
        <div class="modal-title">
            <span>รายการสินค้า — <span id="productPoLabel"></span></span>
            <button type="button" class="btn-ghost" onclick="closeProductModal()" style="padding: 4px 8px;">✕</button>
        </div>
        <div class="modal-body">
            <table class="sub-table">
                <thead>
                    <tr>
                        <th style="width: 150px;">ชั้นวาง</th>
                        <th style="text-align: left;">ชื่อสินค้า</th>
                        @if(($canManage ?? false))
                            <th style="width: 110px;">จัดการ</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="productModalBody">
                    <!-- Dynamic content -->
                </tbody>
            </table>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-ghost" onclick="closeProductModal()">ปิด</button>
        </div>
    </div>
</div>
@endif

<script>
    const DATA_URL   = "{{ route('shelfsale.data') }}";
    const tbody      = document.getElementById('tableBody');
    const mainTable  = document.getElementById('mainTable');
    const showCount  = document.getElementById('showCount');
    const btnSearch  = document.getElementById('btnSearch');
    const btnClear   = document.getElementById('btnClear');
    const fShelf     = document.getElementById('fShelf');
    const fSale      = document.getElementById('fSale');
    const fSo        = document.getElementById('fSo');
    const fPo        = document.getElementById('fPo');

    const IS_SALE       = {{ ($isSaleView ?? false) ? 'true' : 'false' }};
    const CAN_SEE_PRICE = {{ ($canSeePrice ?? false) ? 'true' : 'false' }};
    const CAN_MANAGE    = {{ ($canManage ?? false) ? 'true' : 'false' }};
    const COLSPAN       = {{ 7 + (($canSeePrice ?? false) ? 1 : 0) + (($canManage ?? false) ? 1 : 0) }};
    const CSRF          = document.querySelector('meta[name="csrf-token"]').content;
    const MOVE_URL      = "{{ route('shelfsale.move') }}";
    const CHECKOUT_URL  = "{{ route('shelfsale.checkout') }}";

    let currentRows = [];

    function esc(s){ return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
    function escJs(s){ return String(s ?? '').replace(/\\/g,'\\\\').replace(/'/g,"\\'"); }
    function fmtBaht(n){ return Number(n||0).toLocaleString('th-TH',{minimumFractionDigits:2,maximumFractionDigits:2}); }

    const SHELF_OPTIONS = @json($shelfOptions ?? []);
    const SALE_OPTIONS  = @json($saleOptions ?? []);
    function attachSuggest(input, panel, options){
        if (!input || !panel) return;
        let hl = -1;
        function render(){
            const q = (input.value || '').trim().toLowerCase();
            const matches = (q ? options.filter(s => String(s).toLowerCase().includes(q)) : options).slice(0, 40);
            hl = -1;
            if (!matches.length){ panel.innerHTML = '<div class="suggest-empty">ไม่พบตัวเลือก</div>'; panel.classList.add('open'); return; }
            panel.innerHTML = matches.map(s => '<div class="suggest-item" data-val="' + String(s).replace(/"/g,'&quot;') + '">' + esc(s) + '</div>').join('');
            panel.classList.add('open');
        }
        input.addEventListener('focus', render);
        input.addEventListener('input', render);
        panel.addEventListener('mousedown', e => {
            const it = e.target.closest('.suggest-item'); if (!it) return;
            e.preventDefault(); input.value = it.dataset.val; panel.classList.remove('open'); input.focus();
        });
        input.addEventListener('keydown', e => {
            const items = Array.from(panel.querySelectorAll('.suggest-item'));
            if (e.key === 'ArrowDown' && items.length){ e.preventDefault(); hl = Math.min(hl+1, items.length-1); items.forEach((it,i)=>it.classList.toggle('hl', i===hl)); items[hl].scrollIntoView({block:'nearest'}); }
            else if (e.key === 'ArrowUp' && items.length){ e.preventDefault(); hl = Math.max(hl-1, 0); items.forEach((it,i)=>it.classList.toggle('hl', i===hl)); items[hl].scrollIntoView({block:'nearest'}); }
            else if (e.key === 'Enter' && hl >= 0 && items[hl]){ e.preventDefault(); input.value = items[hl].dataset.val; panel.classList.remove('open'); }
            else if (e.key === 'Escape'){ panel.classList.remove('open'); }
        });
        input.addEventListener('blur', () => setTimeout(() => panel.classList.remove('open'), 120));
    }
    attachSuggest(fShelf, document.getElementById('shelfSuggest'), SHELF_OPTIONS);
    if (fSale && !fSale.readOnly) attachSuggest(fSale, document.getElementById('saleSuggest'), SALE_OPTIONS);

    function setMsg(text){
        if (mainTable) mainTable.classList.add('is-empty');
        tbody.innerHTML = '<tr><td colspan="' + COLSPAN + '"><div class="empty-wrapper"><div class="empty-state">' + esc(text) + '</div></div></td></tr>';
    }

    function updateOverdueAlert(n){
        const el = document.getElementById('overdueAlert');
        const c  = document.getElementById('overdueCount');
        if (c) c.textContent = n;
        if (el) el.style.display = n > 0 ? 'inline-flex' : 'none';
    }

    function dueCell(r){
        if (r.due_days === null || r.due_days === undefined) return { cls:'', txt:'-' };
        if (r.due_days > 0)  return { cls:'due-upcoming', txt:'อีก ' + r.due_days + ' วัน' };
        if (r.due_days === 0) return { cls:'due-today', txt:'ครบวันนี้' };
        return { cls:'due-overdue', txt:'เลย ' + Math.abs(r.due_days) + ' วัน' };
    }

    function rowHtml(r, i){
        const d = dueCell(r);
        const shelf = r.shelf ? '<span class="shelf-badge">' + esc(r.shelf) + '</span>' : '<span class="dash">-</span>';
        const custOrSale = IS_SALE
            ? '<td class="cust-cell">' + esc(r.cust_name) + '</td>'
            : '<td>' + esc(r.sale) + '</td>';
        const priceCell = CAN_SEE_PRICE ? '<td class="price-cell">' + fmtBaht(r.price) + '</td>' : '';

        const manageCell = CAN_MANAGE
            ? '<td style="text-align:center;white-space:nowrap;">'
              + '<button type="button" class="btn-view btn-move" onclick="openMove(\'' + escJs(r.po) + '\',\'' + escJs(r.so) + '\')">ย้ายชั้น</button> '
              + '<button type="button" class="btn-view btn-checkout" onclick="doCheckout(this,\'' + escJs(r.po) + '\',\'' + escJs(r.so) + '\')">เช็คเอาท์</button>'
              + '</td>'
            : '';

        const isOverdue = (r.due_days !== null && r.due_days !== undefined && r.due_days < 0);
        const isToday = (r.due_days === 0);
        const rowCls = isToday ? 'row-today' : '';
        const dueDateClass = isOverdue ? 'blink-overdue-cell' : '';

        const productBtn = '<td style="text-align:center;">' +
            '<button type="button" class="btn-view" onclick="openProductModal(' + i + ', \'' + escJs(r.po) + '\', \'' + escJs(r.so) + '\')">' +
            'ดูสินค้า (' + (r.item_count || 0) + ')' +
            '</button></td>';

        return '<tr class="' + rowCls + '">'
            + '<td>' + shelf + '</td>'
            + '<td class="' + dueDateClass + '">' + (r.ship_date ? esc(r.ship_date) : '<span class="dash">-</span>') + '</td>'
            + '<td class="' + d.cls + '">' + esc(d.txt) + '</td>'
            + '<td class="num"><span class="ref-link">' + esc(r.so) + '</span></td>'
            + '<td class="num">' + esc(r.po) + '</td>'
            + custOrSale
            + priceCell
            + productBtn
            + manageCell
            + '</tr>';
    }

    function openProductModal(index, po, so) {
        const row = currentRows[index];
        if (!row) return;
        
        document.getElementById('productPoLabel').textContent = 'PO ' + po + (so ? ' / SO ' + so : '');
        const tbodyModal = document.getElementById('productModalBody');
        const products = row.products || [];
        const prodCols = CAN_MANAGE ? 3 : 2;
        
        if (products.length === 0) {
            tbodyModal.innerHTML = '<tr><td colspan="' + prodCols + '" style="text-align:center; padding: 24px; color: var(--muted);">ไม่มีรายการสินค้า</td></tr>';
        } else {
            tbodyModal.innerHTML = products.map(p => {
                const moveBtn = CAN_MANAGE 
                    ? '<td style="text-align:center;"><button type="button" class="btn-view btn-move" onclick="closeProductModal(); openMove(\'' + escJs(po) + '\', \'' + escJs(so) + '\', ' + (p.line_id ? p.line_id : 'null') + ')">ย้ายชั้น</button></td>'
                    : '';
                return '<tr>' +
                    '<td>' + esc(p.shelf || '-') + '</td>' +
                    '<td style="text-align:left;">' + esc(p.name || '-') + '</td>' +
                    moveBtn +
                    '</tr>';
            }).join('');
        }
        document.getElementById('productModal').style.display = 'flex';
    }

    function closeProductModal() {
        document.getElementById('productModal').style.display = 'none';
    }

    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                if (this.id === 'productModal') closeProductModal();
                if (this.id === 'moveModal') closeMove();
            }
        });
    });

    let moveTarget = null;
    function openMove(po, so, lineId){
        moveTarget = { po: po, so: so, lineId: (lineId || null) };
        document.getElementById('movePoLabel').textContent = 'PO ' + po + (so ? ' / SO ' + so : '')
            + (moveTarget.lineId ? ' (เฉพาะรายการนี้)' : '');
        const inp = document.getElementById('moveShelfInput'); inp.value = '';
        document.getElementById('moveModal').style.display = 'flex';
        setTimeout(() => inp.focus(), 30);
    }
    function closeMove(){ 
        const m = document.getElementById('moveModal'); 
        if (m) m.style.display = 'none'; 
        moveTarget = null; 
    }
    async function confirmMove(){
        if (!moveTarget) return;
        const shelf = document.getElementById('moveShelfInput').value.trim();
        if (!shelf) { alert('กรุณาเลือกชั้น'); return; }
        const btn = document.getElementById('moveConfirmBtn'); btn.disabled = true; btn.textContent = 'กำลังบันทึก...';
        try {
            const res = await fetch(MOVE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ po: moveTarget.po, so: moveTarget.so, shelf: shelf, line_id: moveTarget.lineId })
            });
            const data = await res.json().catch(() => null);
            if (!res.ok || !data || !data.ok) { alert((data && data.message) || 'ย้ายชั้นไม่สำเร็จ'); return; }
            closeMove(); search();
        } catch (e) { console.error(e); alert('เกิดข้อผิดพลาดในการเชื่อมต่อ'); }
        finally { btn.disabled = false; btn.textContent = 'ยืนยัน'; }
    }
    async function doCheckout(btn, po, so){
        if (!confirm('ยืนยันเช็คเอาท์ PO ' + po + ' ?')) return;
        btn.disabled = true;
        try {
            const res = await fetch(CHECKOUT_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ po: po, so: so })
            });
            const data = await res.json().catch(() => null);
            if (!res.ok || !data || !data.ok) { alert((data && data.message) || 'เช็คเอาท์ไม่สำเร็จ'); btn.disabled = false; return; }
            search();
        } catch (e) { console.error(e); alert('เกิดข้อผิดพลาดในการเชื่อมต่อ'); btn.disabled = false; }
    }

    async function search(){
        const params = new URLSearchParams();
        params.set('shelf', fShelf.value.trim());
        params.set('sale',  fSale.value.trim());
        params.set('so',    fSo.value.trim());
        params.set('po',    fPo.value.trim());

        btnSearch.disabled = true;
        setMsg('⏳ กำลังค้นหา...');
        try {
            const res = await fetch(DATA_URL + '?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (!res.ok || !data.ok) { setMsg((data && data.message) || 'ค้นหาไม่สำเร็จ'); showCount.textContent = 0; return; }

            const rows = data.rows || [];
            currentRows = rows;

            rows.sort((a, b) => {
                const da = (a.due_days === null || a.due_days === undefined) ? Infinity : a.due_days;
                const db = (b.due_days === null || b.due_days === undefined) ? Infinity : b.due_days;
                return da - db;
            });

            showCount.textContent = rows.length;
            const tv = document.getElementById('totalValue');
            if (tv) tv.textContent = fmtBaht(data.total_value || 0);

            const overdue = rows.filter(r => r.due_days !== null && r.due_days !== undefined && r.due_days < 0).length;
            updateOverdueAlert(overdue);

            if (rows.length === 0) { setMsg(data.message || 'ไม่พบรายการตามตัวกรอง'); return; }

            if (mainTable) mainTable.classList.remove('is-empty');
            tbody.innerHTML = rows.map((r, i) => rowHtml(r, i)).join('');
        } catch (e) {
            console.error(e);
            setMsg('เกิดข้อผิดพลาดในการเชื่อมต่อ');
            showCount.textContent = 0;
            updateOverdueAlert(0);
        } finally {
            btnSearch.disabled = false;
        }
    }

    btnSearch.addEventListener('click', search);
    btnClear.addEventListener('click', () => {
        fShelf.value = ''; fSo.value = ''; fPo.value = '';
        if (!fSale.readOnly) fSale.value = '';
        showCount.textContent = 0;
        const tv = document.getElementById('totalValue'); if (tv) tv.textContent = '0.00';
        updateOverdueAlert(0);
        setMsg('เลือกตัวกรอง (ชั้น / Sale / SO / PO) แล้วกด "ค้นหา"');
    });
    [fShelf, fSale, fSo, fPo].forEach(el => el.addEventListener('keydown', e => { if (e.key === 'Enter') search(); }));
</script>
</body>
</html>