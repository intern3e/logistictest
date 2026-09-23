<!DOCTYPE html>
{{-- resources/views/store/store_location.blade.php  (ด่าน 2: ระบุตำแหน่ง) --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ระบุตำแหน่งจัดเก็บ</title>
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
            flex-wrap: nowrap;
            align-items: center;
            box-shadow: var(--shadow-sm);
            width: 100%;
        }

        .toolbar .filter-group {
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
            align-items: center;
            flex: 1;
        }

        .toolbar input[type="search"],
        .toolbar select {
            padding: 7px 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-family: inherit;
            font-size: 13px;
            background: var(--canvas);
            color: var(--ink);
            transition: all 0.2s;
            min-width: 110px;
            white-space: nowrap;
        }

        .toolbar input[type="search"]:focus,
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

        .btn-primary {
            background: var(--primary);
            color: var(--on-primary);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

        .btn-success {
            background: var(--success);
            color: var(--on-primary);
        }

        .btn-success:hover {
            background: var(--success-dark);
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
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

        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
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

        #btnMain {
            font-size: 13px;
            padding: 7px 16px;
            border-radius: 6px;
            font-weight: 600;
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
            min-width: 1000px;
            border-collapse: collapse;
            background: var(--canvas);
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

        tr.done td {
            color: #94a3b8;
            background: var(--row-done);
        }

        tr.done:hover {
            background: var(--row-done);
        }

        tr.hidden-row {
            display: none;
        }

        .num {
            font-variant-numeric: tabular-nums;
            font-weight: 500;
        }

        .cust-cell {
            text-align: left;
            font-weight: 500;
        }

        .center {
            text-align: center;
        }

        .empty {
            text-align: center;
            color: var(--muted);
            padding: 60px 32px !important;
            font-size: 15px;
            font-style: italic;
        }

        .muted {
            font-size: 11px;
            color: var(--muted);
            margin-top: 2px;
        }

        input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1.5px solid #9ca3af;
            cursor: pointer;
            accent-color: var(--primary);
            transition: all 0.15s;
        }

        input[type="checkbox"]:hover {
            border-color: var(--primary);
        }

        .ref-link {
            font-weight: 700;
            color: var(--primary-dark);
            text-decoration: none;
            font-size: 13px;
        }

        .ref-link:hover {
            text-decoration: underline;
        }

        .po-type-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            margin-top: 4px;
            letter-spacing: 0.3px;
        }

        .po-internal {
            background: var(--primary-light);
            color: var(--primary-dark);
            border: 1px solid #bfdbfe;
        }

        .po-external {
            background: #fff7ed;
            color: var(--warning);
            border: 1px solid #fed7aa;
        }

        .items-cell {
            max-width: 320px;
            text-align: left;
            min-height: 40px;
        }

        .items-cell .more {
            color: var(--muted);
            font-weight: 500;
        }

        details.items-expand summary {
            cursor: pointer;
            color: var(--primary);
            font-size: 12px;
            list-style: none;
            font-weight: 600;
            transition: color 0.15s;
        }

        details.items-expand summary::-webkit-details-marker {
            display: none;
        }

        details.items-expand summary::after {
            content: ' ▾';
            font-size: 10px;
        }

        details.items-expand[open] summary::after {
            content: ' ▴';
        }

        details.items-expand[open] summary {
            margin-bottom: 6px;
        }

        details.items-expand summary:hover {
            color: var(--primary-dark);
        }

        .subline {
            font-size: 12px;
            color: #475569;
            padding: 2px 0 2px 12px;
            border-left: 2px solid var(--border);
            margin: 2px 0;
        }

        .btn-claim {
            font-size: 11px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 16px;
            background: var(--primary-light);
            color: var(--primary-dark);
            border: 1px solid #bfdbfe;
        }

        .btn-claim:hover {
            background: var(--primary);
            color: var(--on-primary);
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        /* ดรอปดาว "กำลังจัดการ" — เลือกชื่อผู้จัดการ (โอ/ฟิว) */
        .claim-select {
            font-size: 11px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 16px;
            background: var(--primary-light);
            color: var(--primary-dark);
            border: 1px solid #bfdbfe;
            cursor: pointer;
            font-family: inherit;
        }
        .claim-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 2px var(--primary-light); }

        .btn-finish-claim {
            font-size: 11px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 16px;
            background: #fff7ed;
            color: var(--warning);
            border: 1px solid #fed7aa;
        }

        .btn-finish-claim:hover {
            background: var(--warning);
            color: var(--on-primary);
            border-color: var(--warning);
            transform: translateY(-1px);
        }

        .finished-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            background: #f0fdf4;
            color: var(--success-dark);
            border: 1px solid #bbf7d0;
            border-radius: 16px;
        }

        .finished-tag::before {
            content: '';
            font-size: 10px;
            font-weight: 800;
        }

        .btn-view-items {
            font-size: 12px;
            padding: 6px 14px;
            border-radius: 6px;
            background: var(--canvas);
            color: var(--primary);
            border: 1px solid var(--primary);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-view-items:hover {
            background: var(--primary);
            color: var(--on-primary);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .btn-view-items::before {
            content: '';
            font-size: 11px;
        }

        .no-items {
            color: var(--muted);
            font-size: 12px;
            font-style: italic;
        }

        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            width: 100%;
        }

        .page-btn {
            padding: 7px 14px;
            border: 1px solid var(--border);
            background: var(--canvas);
            color: var(--primary);
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.15s ease;
            border-radius: 6px;
        }

        .page-btn:hover:not(.disabled) {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .page-btn.disabled {
            color: #cbd5e1;
            cursor: not-allowed;
            pointer-events: none;
        }

        .page-info {
            font-size: 13px;
            color: var(--muted);
            font-weight: 600;
            padding: 0 12px;
        }

        dialog {
            border: none;
            padding: 0;
            width: 520px;
            max-width: 92vw;
            max-height: 90vh;
            border-radius: var(--radius);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            margin: 0;
            overflow: hidden;
            animation: dialog-enter 0.25s ease;
        }
        /* เปิดอยู่ = flex column เพื่อให้ body เลื่อนได้ ส่วนหัว/ปุ่มปิดตรึงไว้ (สินค้าเยอะก็ยังกดปิดได้) */
        dialog[open] { display: flex; flex-direction: column; }

        @keyframes dialog-enter {
            from { opacity: 0; transform: translate(-50%, -48%) scale(0.96); }
            to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        dialog::backdrop {
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(2px);
        }

        .dialog-header {
            padding: 20px 24px 0;
        }

        dialog h2 {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 16px;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dialog-body {
            padding: 0 24px 24px;
            overflow-y: auto;          /* สินค้าเยอะ = เลื่อนดูได้ */
            min-height: 0;
            flex: 1 1 auto;
        }
        .dialog-header, .dialog-actions { flex-shrink: 0; }   /* หัว + ปุ่มปิด ตรึงไว้เสมอ */

        dialog label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            color: var(--muted);
            letter-spacing: 0.5px;
        }

        dialog input[type="text"] {
            width: 100%;
            font-size: 15px;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-family: inherit;
            transition: all 0.2s;
            background: var(--canvas);
        }

        dialog input[type="text"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px var(--primary-light);
        }

        .dialog-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            padding: 14px 24px 20px;
            background: #fafbfd;
            border-top: 1px solid var(--border);
        }

        .dialog-actions button {
            font-size: 13px;
            padding: 8px 18px;
        }

        .hint {
            font-size: 13px;
            color: var(--muted);
            margin-top: 10px;
            min-height: 20px;
            padding: 8px 12px;
            background: var(--page-bg);
            border-radius: 6px;
            border-left: 3px solid var(--border);
            transition: all 0.2s;
        }

        .autocomplete-wrap {
            position: relative;
        }

        .suggest-panel {
            display: none;
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 4px);
            background: var(--canvas);
            border: 1px solid var(--border);
            border-radius: 6px;
            box-shadow: var(--shadow-md);
            max-height: min(320px, 45vh);
            overflow-y: auto;
            z-index: 9999;
        }

        .suggest-panel.open {
            display: block;
            animation: suggest-enter 0.15s ease;
        }

        @keyframes suggest-enter {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .suggest-item {
            padding: 9px 14px;
            font-size: 13px;
            color: var(--ink);
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.1s;
        }

        .suggest-item:last-child {
            border-bottom: none;
        }

        .suggest-item:hover,
        .suggest-item.hl {
            background: var(--row-hover);
            color: var(--primary-dark);
            padding-left: 18px;
        }

        .suggest-empty {
            padding: 12px 14px;
            font-size: 13px;
            color: var(--muted);
            text-align: center;
            font-style: italic;
        }

        .chips-section {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid var(--border);
        }

        .chips-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .chips {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .chip {
            padding: 5px 12px;
            border: 1px solid var(--border);
            background: var(--canvas);
            color: var(--muted);
            font-weight: 600;
            font-size: 11px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.15s ease;
            border-radius: 16px;
        }

        a.chip {
            text-decoration: none;
        }

        .chip:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }

        .chip.active {
            background: var(--primary);
            color: var(--on-primary);
            border-color: var(--primary);
        }

        #itemsModal {
            width: 560px;
        }

        #itemsModal table {
            width: 100%;
            min-width: 0;
            margin-top: 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
        }

        #itemsModal th {
            background: var(--page-bg);
            padding: 9px 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--muted);
            font-weight: 700;
        }

        #itemsModal td {
            padding: 9px 12px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
        }

        #itemsModal tr:last-child td {
            border-bottom: none;
        }

        .items-modal-empty,
        .items-modal-loading {
            text-align: center;
            color: var(--muted);
            padding: 28px;
            font-size: 14px;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @media (max-width: 1024px) {
            .toolbar {
                flex-wrap: wrap;
            }
            .toolbar .filter-group {
                flex-wrap: wrap;
                width: 100%;
            }
            .toolbar input[type="search"],
            .toolbar select {
                flex: 1;
                min-width: calc(50% - 8px);
            }
        }

        @media (max-width: 768px) {
            .top-banner { padding: 10px 16px; }
            main { padding: 12px 16px; }
            .toolbar { padding: 10px 12px; }
            .toolbar input[type="search"],
            .toolbar select { 
                min-width: 100%; 
                width: 100%;
            }
            .toolbar .filter-group { 
                flex-direction: column; 
                align-items: stretch; 
            }
            th, td { padding: 10px 12px; font-size: 12px; }
            .title-group .h1 { font-size: 16px; }
        }
    </style>
</head>
<body>
<div class="page-frame">
    <div class="top-banner">
        <div class="title-group">
            <span class="h1">จัดบิลขึ้นชั้น</span>
            <span class="sticker">STORE</span>
        </div>
        <div class="user-badge">ผู้ใช้งาน: {{ $creator }}</div>
    </div>
    <input type="hidden" id="inpUser" value="{{ $creator }}">
    @php $nav = $creator ? ['create_by' => $creator] : []; @endphp
    <main>
        <div class="toolbar">
            <div class="filter-group">
                <input type="search" id="searchSO" value="{{ request('SONum') }}" placeholder=" ค้นหาเลข SO..." autocomplete="off">
                <input type="search" id="searchPO" value="{{ request('PONum') }}" placeholder=" ค้นหาเลข PO..." autocomplete="off">
                <input type="search" id="searchCustomer" value="{{ request('customer') }}" placeholder=" ค้นหาลูกค้า..." autocomplete="off">
                <select id="filterPoType">
                    <option value="">PO ทั้งหมด</option>
                    <option value="internal" {{ request('po_type') === 'internal' ? 'selected' : '' }}>ภายใน</option>
                    <option value="external" {{ request('po_type') === 'external' ? 'selected' : '' }}>ภายนอก</option>
                </select>
                <button type="button" class="btn-ghost" id="btnClear">ล้าง</button>
            </div>
            
            <button type="button" class="btn-success" id="btnMain" hidden onclick="openModal()">
                 ระบุตำแหน่ง (<span id="selCount">0</span>)
            </button>
        </div>

        <div class="table-topbar">
            <div class="table-info">
                รอระบุตำแหน่ง <span id="todoCount">{{ $totalTodo }}</span> / แสดง <span id="showCount">{{ $heads->total() }}</span> ใบ (หน้า {{ $heads->currentPage() }}/{{ $heads->lastPage() ?: 1 }})
            </div>
        </div>

        <div class="table-scroll">
            <div class="table-inner">
                <table>
                    <thead>
                        <tr>
                            <th class="center" style="width:50px;"><input type="checkbox" id="chkAll"></th>
                            <th>PO</th>
                            <th>SO</th>
                            <th style="text-align:left;">รายการสินค้า</th>
                            <th style="text-align:left;">ลูกค้า</th>
                            <th style="text-align:left;">Sale</th>
                            <th>จัดการ</th>
                            <th>รับโดย</th>
                            <th>เวลารับ</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse ($heads as $h)
                            @php
                                $todo        = $h->todo;
                                $cls         = $todo ? '' : 'done';
                                $items       = $h->items;
                                $totalQty    = $h->total_qty;
                                $location    = $h->location;
                                $checkboxVal = $h->type . ':' . $h->id;
                                $isClaimed   = $h->type === 'external' && ($h->claimed ?? false);
                                $isFinished  = $h->type === 'external' && ($h->finished ?? false);
                                $poType      = str_contains((string) $h->po_display, 'A') ? 'internal' : 'external';
                                // internal_po: PENDING+claim แล้ว = กำลังจัดการ (ต้องกด "จัดการเสร็จสิ้น") / FINISH = พร้อมระบุตำแหน่ง
                                $rowStatus       = $h->status ?? null;
                                $internalPending = $h->type === 'internal' && $rowStatus === \App\Models\internal_po::ST_PENDING;
                                $internalReady   = $h->type === 'internal' && $rowStatus === \App\Models\internal_po::ST_FINISH;
                                // เลือก checkbox เพื่อระบุตำแหน่งได้เฉพาะ: internal ที่พร้อม, หรือ external/legacy ที่ยังไม่ถูก claim
                                $canSelect       = $h->type === 'internal' ? $internalReady : ($todo && !$isClaimed);
                            @endphp
                            <tr class="{{ $cls }}" data-done="{{ $todo ? 0 : 1 }}"
                                data-so="{{ $h->so_id }}"
                                data-po="{{ $h->po_display }}"
                                data-customer="{{ $h->customer_name }}"
                                data-po-type="{{ $poType }}">
                                <td class="center">
                                    @if ($canSelect)<input type="checkbox" class="chkLine" value="{{ $checkboxVal }}">@endif
                                </td>
                                <td>
                                    <span class="ref-link">{{ $h->po_display }}</span>
                                    <div>
                                        @if ($poType === 'internal')
                                            <span class="po-type-badge po-internal">ภายใน</span>
                                        @else
                                            <span class="po-type-badge po-external">ภายนอก</span>
                                        @endif
                                    </div>
                                </td>
                                <td><span style="font-weight:600;">{{ $h->so_id }}</span></td>
                                <td class="items-cell">
                                    @php
                                        $itemsJson = $items
                                            ? $items->map(fn ($it) => [
                                                'name' => $it->item_name,
                                                'qty'  => (float) $it->item_quantity,
                                                'so'   => $it->so ?? $it->so_id ?? null,
                                            ])->values()
                                            : null;
                                    @endphp
                                    {{-- ทุกแถวใช้ปุ่มเปิด popup เหมือนกัน: มี items แล้วฝังไว้ (ไม่ต้อง fetch) / ไม่มี = โหลดตอนกด
                                         ส่ง data-so ไปด้วย เพราะ PO เดียวกันมีได้หลาย SO → ต้องกรองให้เห็นเฉพาะ SO ของแถวนี้ --}}
                                    <button type="button" class="btn-view-items"
                                        data-po="{{ $h->po_display }}" data-so="{{ $h->so_id }}"
                                        @if ($itemsJson !== null) data-items='@json($itemsJson)' @endif>
                                        ดูสินค้า @if ($items) ({{ $items->count() }}) @endif
                                    </button>
                                </td>
                                <td class="cust-cell">{{ $h->customer_name }}</td>
                                <td>{{ $h->sale ?: '—' }}</td>
                                <td>
                                    @if ($h->type === 'external' || $h->type === 'legacy')
                                        @if ($isClaimed)
                                            {{-- do_it: มีคนเอาของออกไปทำ (ยังไม่กดรับคืน) --}}
                                            <button type="button" class="btn-finish-claim" data-po="{{ $h->id }}">จัดการเสร็จสิ้น</button>
                                            <div class="muted">เอาไปทำโดย {{ $h->claimed_by ?: '—' }}</div>
                                            <div class="muted">{{ $h->claimed_at ? \Carbon\Carbon::parse($h->claimed_at)->format('d/m/Y H:i') : '' }}</div>
                                        @elseif ($isFinished)
                                            {{-- ผู้ดูแลกดรับของกลับแล้ว (sus) → พร้อมระบุตำแหน่ง — แสดงทั้งคนเอาไป (do_it) และคนรับคืน (sus) --}}
                                            <span class="finished-tag">พร้อมระบุตำแหน่ง</span>
                                            <div class="muted">เอาไปทำโดย {{ $h->claimed_by ?: '—' }}{{ $h->claimed_at ? ' · ' . \Carbon\Carbon::parse($h->claimed_at)->format('d/m/Y H:i') : '' }}</div>
                                            <div class="muted">รับคืนโดย {{ $h->finished_by ?: '—' }}{{ $h->finished_at ? ' · ' . \Carbon\Carbon::parse($h->finished_at)->format('d/m/Y H:i') : '' }}</div>
                                        @else
                                            {{-- เลือกชื่อผู้จัดการ (โอ/ฟิว) -> บันทึกเข้า do_it --}}
                                            <select class="claim-select" data-po="{{ $h->id }}" data-type="{{ $h->type }}">
                                                <option value="">กำลังจัดการ...</option>
                                                <option value="โอ">โอ</option>
                                                <option value="ฟิว">ฟิว</option>
                                            </select>
                                        @endif
                                    @else
                                        {{-- internal_po --}}
                                        @if ($internalPending)
                                            <button type="button" class="btn-finish-claim" data-po="{{ $h->id }}" data-internal="1">จัดการเสร็จสิ้น</button>
                                            <div class="muted">โดย {{ $h->claimed_by ?: '—' }}</div>
                                            <div class="muted">{{ $h->claimed_at ? \Carbon\Carbon::parse($h->claimed_at)->format('d/m/Y H:i') : '' }}</div>
                                        @elseif ($internalReady)
                                            <span class="finished-tag">พร้อมระบุตำแหน่ง</span>
                                        @else
                                            <span style="font-weight:600; color: var(--ink);">{{ $location ?: '—' }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td><span style="font-size:12px;">{{ $h->packed_by ?: '—' }}</span></td>
                                <td class="muted">{{ $h->packed_at ? \Carbon\Carbon::parse($h->packed_at)->format('d/m/Y H:i') : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="empty">ไม่มีรายการที่รอระบุตำแหน่ง</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($heads->hasPages())
            <div class="pagination">
                @if ($heads->onFirstPage())
                    <span class="page-btn disabled">‹ ก่อนหน้า</span>
                @else
                    <a class="page-btn" href="{{ $heads->previousPageUrl() }}">‹ ก่อนหน้า</a>
                @endif

                <span class="page-info">หน้า {{ $heads->currentPage() }} / {{ $heads->lastPage() }} (ทั้งหมด {{ $heads->total() }} ใบ)</span>

                @if ($heads->hasMorePages())
                    <a class="page-btn" href="{{ $heads->nextPageUrl() }}">ถัดไป ›</a>
                @else
                    <span class="page-btn disabled">ถัดไป ›</span>
                @endif
            </div>
        @endif
    </main>
</div>

<dialog id="locModal">
    <div class="dialog-header">
        <h2>ระบุตำแหน่งจัดเก็บ</h2>
    </div>
    <div class="dialog-body">
        <label for="inpLocation">ชั้นวาง</label>
        <div class="autocomplete-wrap">
            <input type="text" id="inpLocation" placeholder="พิมพ์ค้นหาชั้นวาง เช่น A11" autocomplete="off" maxlength="100">
            <div id="locSuggest" class="suggest-panel"></div>
        </div>
        <p class="hint" id="dlgHint">เลือกรายการที่ต้องการแล้วระบุชั้นวาง</p>

        @if (($locations ?? collect())->count())
            <div class="chips-section">
                <div class="chips-label">ใช้ล่าสุด</div>
                <div class="chips">
                    @foreach ($locations->take(6) as $loc)
                        <span class="chip" onclick="pickLoc(this)">{{ $loc }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <div class="dialog-actions">
        <button type="button" class="btn-ghost" onclick="document.getElementById('locModal').close()">ยกเลิก</button>
        <button type="button" class="btn-primary" onclick="confirmLoc()">บันทึกตำแหน่ง</button>
    </div>
</dialog>

<dialog id="itemsModal">
    <div class="dialog-header">
        <h2 id="itemsModalTitle">รายการสินค้า</h2>
    </div>
    <div class="dialog-body">
        <div id="itemsModalBody"></div>
    </div>
    <div class="dialog-actions">
        <button type="button" class="btn-ghost" onclick="document.getElementById('itemsModal').close()">ปิด</button>
    </div>
</dialog>

<script>
const SUBMIT_URL = "{{ route('store.location.submit') }}";
const CLAIM_URL  = "{{ route('store.location.claim') }}";
const FINISH_URL = "{{ route('store.location.finish') }}";
const FINISH_INTERNAL_URL = "{{ route('store.location.finishInternal') }}";
const LEGACY_ITEMS_URL = "{{ route('store.location.legacyItems') }}";
const LEGACY_CLAIM_URL = "{{ route('store.location.legacyClaim') }}";
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
const modal      = document.getElementById('locModal');
const SHELF_OPTIONS = [
  "1A01","1Kโบว์","1กวาง","1กี้","1ตี๋/พลอย","1ต่าย","1ท๊อป","1นภา",
  "1นุ/เต้น","1นุช","1นุ่น","1น้อย/มล","1น้ำ/กิ๊ฟ","1ฟอง","1ฟิล์ม",
  "1ภิรุณ","1มุก","1หนิง","1หมิง","1หมู/ต๋อง","1เจี๊ยบ","1เชร์",
  "1เนย","1เอก","1แยม","1แอม","1โจ",
  "A11","A12","A13","A14","A21","A22","A23","A24",
  "A31","A32","A33","A34","A41","A42","A43","A44",
  "aom stock","B1",
  "C1","C10","C11","C12","C13","C14","C15","C16",
  "C2","C3","C4","C5","C6","C7","C8","C9","Cท่อ",
  "LASADA",
  "Qกรมศุลเล็","Qจัดแล้วรอ","Qติดปัญหา","Qบิลสด","QรอครบSO","Qสหกรณ์","Qแก้ไข","Qแดง",
  "top stock","Z55",
  "ขมจ่ายแล้ว","ของเกิน","คืนstock","ช.เดช","ช.โอ","ชัย-เดช","ชัย1","ชัย2",
  "ด.1","ด.10","ด.11","ด.12","ด.2","ด.3","ด.4","ด.5","ด.6","ด.7","ด.8","ด.9",
  "ทำคืน","ท๊อปบน","ปอ-ฮิคาริ","ปิดรับบิล","ปี69","พู่",
  "ว๊าล","ว๊าลแก้ไข","หน้าออฟฟิศ","หยรอบิล","หยกรอเคลีย","หลังออฟฟิศ","พู่/เอ็ม","ว้าล/เอ็ม"
];

const inpLocation = document.getElementById('inpLocation');
const suggestPanel = document.getElementById('locSuggest');
let hlIndex = -1;

function renderSuggestions(filter) {
    const q = filter.trim().toLowerCase();
    const matches = q
        ? SHELF_OPTIONS.filter(s => s.toLowerCase().includes(q)).slice(0, 30)
        : SHELF_OPTIONS.slice(0, 30);

    hlIndex = -1;
    if (!matches.length) {
        suggestPanel.innerHTML = '<div class="suggest-empty">ไม่พบชั้นวางที่ตรงกับคำค้นหา</div>';
        suggestPanel.classList.add('open');
        return;
    }
    suggestPanel.innerHTML = matches
        .map(s => `<div class="suggest-item" data-val="${s.replace(/"/g, '&quot;')}">${s}</div>`)
        .join('');
    suggestPanel.classList.add('open');
}

inpLocation.addEventListener('focus', () => renderSuggestions(inpLocation.value));
inpLocation.addEventListener('input', () => renderSuggestions(inpLocation.value));

suggestPanel.addEventListener('click', (e) => {
    const item = e.target.closest('.suggest-item');
    if (!item) return;
    inpLocation.value = item.dataset.val;
    suggestPanel.classList.remove('open');
    inpLocation.focus();
});

inpLocation.addEventListener('keydown', (e) => {
    const items = Array.from(suggestPanel.querySelectorAll('.suggest-item'));
    if (e.key === 'ArrowDown' && items.length) {
        e.preventDefault();
        hlIndex = Math.min(hlIndex + 1, items.length - 1);
        items.forEach((it, i) => it.classList.toggle('hl', i === hlIndex));
        items[hlIndex].scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'ArrowUp' && items.length) {
        e.preventDefault();
        hlIndex = Math.max(hlIndex - 1, 0);
        items.forEach((it, i) => it.classList.toggle('hl', i === hlIndex));
        items[hlIndex].scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (hlIndex >= 0 && items[hlIndex]) { inpLocation.value = items[hlIndex].dataset.val; suggestPanel.classList.remove('open'); }
        else { confirmLoc(); }
    } else if (e.key === 'Escape') {
        suggestPanel.classList.remove('open');
    }
});

document.addEventListener('click', (e) => {
    if (!e.target.closest('.autocomplete-wrap')) suggestPanel.classList.remove('open');
});

const selectedIds = () => Array.from(document.querySelectorAll('.chkLine:checked')).map(c => c.value);
const currentUser = () => document.getElementById('inpUser').value.trim();

function refreshBtn() {
    const n = selectedIds().length;
    document.getElementById('selCount').textContent = n;
    document.getElementById('btnMain').hidden = (n === 0);
}
document.getElementById('chkAll').addEventListener('change', function () {
    document.querySelectorAll('.chkLine:not([disabled])').forEach(c => c.checked = this.checked);
    refreshBtn();
});
document.querySelectorAll('.chkLine').forEach(c => c.addEventListener('change', refreshBtn));

function pickLoc(el) { const i = document.getElementById('inpLocation'); i.value = el.textContent.trim(); i.focus(); }

async function postClaimAction(url, poId, btn, confirmMsg, fieldName = 'po_id', extra = {}) {
    if (!confirm(confirmMsg)) return false;
    btn.disabled = true;
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF },
            body: JSON.stringify({ [fieldName]: poId, ...extra })
        });
        const data = await res.json();
        if (res.ok && data.ok) {
            window.location.reload();
            return true;
        } else {
            alert(data.message || 'ดำเนินการไม่สำเร็จ');
            btn.disabled = false;
            return false;
        }
    } catch (e) {
        console.error(e);
        alert('เกิดข้อผิดพลาด');
        btn.disabled = false;
        return false;
    }
}
// ดรอปดาว "กำลังจัดการ" — เลือกชื่อ (โอ/ฟิว) แล้วบันทึกเข้า do_it
document.querySelectorAll('.claim-select').forEach(sel => {
    sel.addEventListener('change', async () => {
        const doBy = sel.value;
        if (!doBy) return;
        const ok = await postClaimAction(
            sel.dataset.type === 'legacy' ? LEGACY_CLAIM_URL : CLAIM_URL,
            sel.dataset.po, sel,
            'ยืนยันให้ "' + doBy + '" เป็นผู้จัดการงาน PO นี้ใช่หรือไม่',
            sel.dataset.type === 'legacy' ? 'store_id' : 'po_id',
            { do_by: doBy }
        );
        if (!ok) sel.value = '';   // ยกเลิก/ล้มเหลว -> รีเซ็ตดรอปดาว
    });
});
document.querySelectorAll('.btn-finish-claim').forEach(btn => {
    btn.addEventListener('click', () => {
        if (btn.dataset.internal === '1') {
            postClaimAction(FINISH_INTERNAL_URL, btn.dataset.po, btn, 'คุณยืนยันที่จะจัดงานเสร็จสิ้นหรือไม่', 'internal_id');
        } else {
            postClaimAction(FINISH_URL, btn.dataset.po, btn, 'คุณยืนยันที่จะจัดงานเสร็จสิ้นหรือไม่');
        }
    });
});

const itemsModal     = document.getElementById('itemsModal');
const itemsModalTitle = document.getElementById('itemsModalTitle');
const itemsModalBody  = document.getElementById('itemsModalBody');

// ดึง SO จากชื่อสินค้าที่ฝัง "_SO69/008617" ไว้ (กรณีไม่มี field so มาให้)
function extractSoFromName(name){
    const m = String(name || '').match(/_SO\s*([0-9./-]+)/i);
    return m ? m[1] : '';
}
// ตัดส่วน "_SO..." ออกจากชื่อให้สะอาด (SO ไปแสดงในคอลัมน์ SO แทน)
function cleanItemName(name){
    return String(name || '').replace(/_SO\s*[0-9./-]+/ig, '').trim() || '-';
}
// normalize SO เพื่อเทียบ (ตัดช่องว่าง/คำนำหน้า SO)
function normSo(v){
    return String(v || '').replace(/\s+/g, '').replace(/^SO/i, '');
}
// SO ของ item: จาก field so/so_id ก่อน ถ้าไม่มีค่อยแกะจากชื่อ
function soOfItem(it){
    const rawName = it.name ?? it.item_name ?? '-';
    return (it.so ?? it.so_id ?? '') || extractSoFromName(rawName) || '';
}
// filterSo = so_id ของแถวที่กด → แสดงเฉพาะสินค้าของ PO+SO นั้น (PO เดียวกันมีได้หลาย SO)
function renderItemsTable(items, filterSo){
    if (!items || !items.length) {
        itemsModalBody.innerHTML = '<div class="items-modal-empty">ไม่พบรายการสินค้า</div>';
        return;
    }
    // กรองให้เหลือเฉพาะ SO ของแถวนี้ (ถ้าข้อมูลระบุ SO ได้)
    const want = normSo(filterSo);
    if (want) {
        const anyHasSo = items.some(it => normSo(soOfItem(it)) !== '');
        if (anyHasSo) {
            items = items.filter(it => normSo(soOfItem(it)) === want);
        }
    }
    if (!items.length) {
        itemsModalBody.innerHTML = '<div class="items-modal-empty">ไม่พบรายการสินค้าของ SO นี้</div>';
        return;
    }
    // จัดกลุ่มตาม SO เพื่อให้เห็นว่าแต่ละ SO มีสินค้าอะไรบ้าง
    const groups = {};
    const order  = [];
    items.forEach(it => {
        const rawName = it.name ?? it.item_name ?? '-';
        const so = soOfItem(it);
        const key = so || 'ไม่ระบุ SO';
        if (!groups[key]) { groups[key] = []; order.push(key); }
        groups[key].push({ name: cleanItemName(rawName), qty: Number(it.qty ?? it.item_quantity ?? 0) });
    });

    const blocks = order.map(so => {
        const rows = groups[so].map(r => `
            <tr>
                <td style="text-align:left; font-weight:500;">${r.name}</td>
                <td class="num" style="text-align:right;">${r.qty.toFixed(2)}</td>
            </tr>`).join('');
        return `
            <div style="margin-bottom:14px;">
                <div style="display:inline-block;background:#EAF0FE;color:#2563eb;font-weight:600;
                            font-size:13px;padding:3px 12px;border-radius:999px;margin-bottom:6px;">SO ${so}</div>
                <table>
                    <thead><tr><th style="text-align:left;">ชื่อสินค้า</th><th style="text-align:right;">จำนวน</th></tr></thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
    }).join('');
    itemsModalBody.innerHTML = blocks;
}

document.querySelectorAll('.btn-view-items').forEach(btn => {
    btn.addEventListener('click', async () => {
        const po = btn.dataset.po;
        const so = btn.dataset.so || '';
        itemsModalTitle.textContent = 'รายการสินค้า — PO ' + po + (so ? ' / SO ' + so : '');
        itemsModal.showModal();

        // มี items ฝังไว้แล้ว (external/legacy/new) → แสดงเลย ไม่ต้อง fetch (กรองตาม SO ของแถวนี้)
        if (btn.dataset.items) {
            try { renderItemsTable(JSON.parse(btn.dataset.items), so); }
            catch (e) { itemsModalBody.innerHTML = '<div class="items-modal-empty">ข้อมูลสินค้าผิดพลาด</div>'; }
            return;
        }

        // ไม่มี → โหลดจาก server (ส่ง so ไปด้วยเพื่อกรองฝั่ง server ในอนาคต + กรองซ้ำฝั่ง client)
        itemsModalBody.innerHTML = '<div class="items-modal-loading">⏳ กำลังโหลด...</div>';
        try {
            const res = await fetch(LEGACY_ITEMS_URL + '?po=' + encodeURIComponent(po) + (so ? '&so=' + encodeURIComponent(so) : ''), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (res.ok && data.ok) renderItemsTable(data.items, so);
            else itemsModalBody.innerHTML = '<div class="items-modal-empty">โหลดไม่สำเร็จ</div>';
        } catch (e) {
            console.error(e);
            itemsModalBody.innerHTML = '<div class="items-modal-empty">เกิดข้อผิดพลาด</div>';
        }
    });
});

function openModal() {
    if (!currentUser())        { alert('กรุณาระบุชื่อผู้ดำเนินการ'); return; }
    if (!selectedIds().length) { alert('ยังไม่ได้เลือกรายการ'); return; }
    document.getElementById('inpLocation').value = '';
    const h = document.getElementById('dlgHint');
    h.textContent = 'จะบันทึก ' + selectedIds().length + ' ใบ (ทุกใบที่เลือกจะใช้ตำแหน่งเดียวกัน)';
    h.style.color = '#1e293b';
    h.style.borderLeftColor = 'var(--primary)';
    modal.showModal();
    document.getElementById('inpLocation').focus();
}

async function confirmLoc() {
    const box = document.getElementById('inpLocation').value.trim();
    const h   = document.getElementById('dlgHint');
    if (!box) {
        h.textContent = 'กรุณาระบุชั้นวาง';
        h.style.color = 'var(--danger)';
        h.style.borderLeftColor = 'var(--danger)';
        document.getElementById('inpLocation').focus();
        return;
    }
    if (!SHELF_OPTIONS.includes(box)) {
        h.textContent = 'ไม่พบชั้นวางนี้ในระบบ กรุณาเลือกจากรายการ';
        h.style.color = 'var(--danger)';
        h.style.borderLeftColor = 'var(--danger)';
        document.getElementById('inpLocation').focus();
        return;
    }

    if (!confirm('ยืนยันระบุตำแหน่ง ' + box + ' ให้ ' + selectedIds().length + ' ใบ?')) return;

    const btn = document.querySelector('#locModal .btn-primary');
    btn.disabled = true;
    btn.textContent = 'กำลังบันทึก...';
    try {
        const res = await fetch(SUBMIT_URL, {
            method: 'POST',
            headers: { 'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF },
            body: JSON.stringify({ ids: selectedIds(), user: currentUser(), location: box })
        });
        const data = await res.json();
        if (res.ok && data.ok) { alert('' + data.message); window.location.reload(); }
        else { alert(data.message || 'บันทึกไม่สำเร็จ'); btn.disabled = false; btn.textContent = 'บันทึกตำแหน่ง'; }
    } catch (e) { console.error(e); alert('เกิดข้อผิดพลาด'); btn.disabled = false; btn.textContent = 'บันทึกตำแหน่ง'; }
}

// ===== Live Search =====
const searchSO = document.getElementById('searchSO');
const searchPO = document.getElementById('searchPO');
const searchCustomer = document.getElementById('searchCustomer');
const filterPoType = document.getElementById('filterPoType');
const btnClear = document.getElementById('btnClear');

function liveFilter() {
    const soQ = searchSO.value.trim().toLowerCase();
    const poQ = searchPO.value.trim().toLowerCase();
    const custQ = searchCustomer.value.trim().toLowerCase();
    const typeQ = filterPoType.value;

    const rows = document.querySelectorAll('#tableBody tr[data-done]');
    let visibleCount = 0;
    let todoCount = 0;

    rows.forEach(row => {
        const so = (row.dataset.so || '').toLowerCase();
        const po = (row.dataset.po || '').toLowerCase();
        const cust = (row.dataset.customer || '').toLowerCase();
        const type = row.dataset.poType || '';

        const matchSO = !soQ || so.includes(soQ);
        const matchPO = !poQ || po.includes(poQ);
        const matchCust = !custQ || cust.includes(custQ);
        const matchType = !typeQ || type === typeQ;

        if (matchSO && matchPO && matchCust && matchType) {
            row.classList.remove('hidden-row');
            const chk = row.querySelector('.chkLine');
            if (chk) chk.disabled = (row.dataset.done === '1');
            visibleCount++;
            if (row.dataset.done === '0') todoCount++;
        } else {
            row.classList.add('hidden-row');
            const chk = row.querySelector('.chkLine');
            if (chk) {
                chk.checked = false;
                chk.disabled = true;
            }
        }
    });

    document.getElementById('showCount').textContent = visibleCount;
    document.getElementById('todoCount').textContent = todoCount;
    document.getElementById('chkAll').checked = false;
    refreshBtn();
}

searchSO.addEventListener('input', liveFilter);
searchPO.addEventListener('input', liveFilter);
searchCustomer.addEventListener('input', liveFilter);
filterPoType.addEventListener('change', liveFilter);

btnClear.addEventListener('click', () => {
    searchSO.value = '';
    searchPO.value = '';
    searchCustomer.value = '';
    filterPoType.value = '';
    liveFilter();
});

liveFilter();
</script>
</body>
</html>