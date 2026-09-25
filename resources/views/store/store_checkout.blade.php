<!DOCTYPE html>
{{-- resources/views/store/store_checkout.blade.php --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>จัดบิลส่งของ</title>
<style>
    :root{
        --ink: #1e293b; 
        --canvas: #f1f5f9; 
        --card-bg: #ffffff;
        --muted: #64748b; 
        --faint: #94a3b8; 
        --border: #e2e8f0;
        --primary: #2563eb; 
        --primary-dark: #1d4ed8; 
        --primary-light: #eff6ff;
        --on-primary: #ffffff; 
        --success: #22c55e; 
        --success-dark: #16a34a; 
        --success-light: #f0fdf4;
        --danger: #ef4444; 
        --danger-dark: #dc2626; 
        --danger-light: #fef2f2;
        --radius: 10px;
        --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1);
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { background: var(--canvas); overflow-x: hidden; max-width: 100%; }
    body {
        font-family: 'Segoe UI', Tahoma, Arial, sans-serif; 
        font-size: 16px;
        color: var(--ink); 
        padding-bottom: 50px;
        line-height: 1.5;
    }
    main { max-width: 1600px; margin: 0 auto; padding: 24px; }

    /* ===== Top Banner ===== */
    .top-banner {
        background: #ffffff; 
        padding: 16px 28px; 
        border-bottom: 1px solid var(--border);
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        flex-wrap: nowrap; 
        gap: 16px;
        box-shadow: var(--shadow-sm);
    }
    .top-banner .title-group { display: flex; flex-direction: column; flex-shrink: 0; }
    .top-banner .h1 { font-weight: 700; font-size: 20px; color: var(--ink); white-space: nowrap; }
    
    .top-banner .user-info { 
        font-size: 14px; 
        color: var(--on-primary); 
        font-weight: 600; 
        background: var(--primary); 
        padding: 6px 16px; 
        border-radius: 20px; 
        white-space: nowrap; 
        flex-shrink: 0; 
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: var(--shadow-sm);
    }
    .top-banner .user-info span { background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 10px; }

    /* ===== Toolbar / Filters ===== */
    .toolbar-container {
        display: flex;
        align-items: center;
        gap: 16px;
        background: transparent;
        padding: 0;
        border: none;
        box-shadow: none;
        flex-grow: 1;
        justify-content: flex-start;
        min-width: 0;
        overflow-x: auto;
    }
    .toolbar { 
        display: flex; 
        gap: 10px; 
        flex-wrap: nowrap; 
        align-items: center; 
        flex-shrink: 0; 
    }
    .toolbar .field-label { font-size: 15px; color: var(--muted); font-weight: 600; display: flex; align-items: center; gap: 8px; white-space: nowrap; }
    
    input[type="text"], input[type="search"], input[type="date"] {
        padding: 8px 12px; 
        border: 1px solid var(--border); 
        border-radius: 6px;
        font-family: inherit; 
        font-size: 15px; 
        background: var(--card-bg); 
        color: var(--ink);
        transition: all 0.2s;
    }
    .toolbar input[type="search"] { width: 150px; }
    
    /* บังคับช่องวันที่ให้แสดงรูปแบบ วัน/เดือน/ปี (วว/ดด/ปป) บนเบราว์เซอร์ที่รองรับ */
    input[type="date"] {
        appearance: none;
        -webkit-appearance: none;
        min-width: 155px;
    }
    input[type="date"]::-webkit-datetime-edit-fields-wrapper { display: flex; }
    input[type="date"]::-webkit-datetime-edit-text { padding: 0 2px; color: var(--muted); }
    input[type="date"]::-webkit-datetime-edit-month-field { content: "เดือน"; }
    input[type="date"]::-webkit-datetime-edit-day-field { content: "วัน"; }
    input[type="date"]::-webkit-datetime-edit-year-field { content: "ปี"; }

    input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
    
    button {
        padding: 8px 16px; 
        border: 1px solid transparent; 
        border-radius: 6px;
        font-family: inherit; 
        font-weight: 600; 
        font-size: 15px; 
        cursor: pointer; 
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-success { background: var(--success); color: var(--on-primary); box-shadow: var(--shadow-sm); }
    .btn-success:hover { background: var(--success-dark); }
    .btn-ghost { background: var(--card-bg); color: var(--muted); border-color: var(--border); }
    .btn-ghost:hover { background: #f1f5f9; color: var(--ink); }
    button:disabled { opacity: .4; cursor: not-allowed; }

    .empty-state {
        text-align: center; padding: 60px 20px; color: var(--muted);
        border: 2px dashed var(--border); background: var(--card-bg);
        border-radius: var(--radius); font-size: 16px; font-weight: 500;
    }

    /* ===== List Meta & Quick Filter Links ===== */
    .list-meta { font-size: 15px; color: var(--muted); white-space: nowrap; flex-shrink: 0; border-left: 1px solid var(--border); padding-left: 16px; display: flex; align-items: center; gap: 12px; }
    .filter-pills { display: inline-flex; gap: 4px; background: #f1f5f9; padding: 3px; border-radius: 6px; border: 1px solid var(--border); }
    .filter-pill {
        font-size: 13px; font-weight: 600; padding: 4px 10px; border-radius: 4px; text-decoration: none; color: var(--muted);
        transition: all 0.2s;
    }
    .filter-pill:hover { color: var(--ink); }
    .filter-pill.active { background: var(--card-bg); color: var(--primary-dark); box-shadow: var(--shadow-sm); }

    /* ===== SO Grid Cards ===== */
    .so-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        align-items: start;
        margin-top: 24px;
    }

    .so-card {
        background: var(--card-bg); 
        border: 1px solid var(--border); 
        border-left: 5px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden; 
        display: flex; 
        flex-direction: column; 
        min-width: 0;
        box-shadow: var(--shadow-sm);
        transition: box-shadow 0.2s;
    }
    .so-card:hover { box-shadow: var(--shadow); }
    .so-card[data-done="1"] { border-left-color: var(--success); }
    
    .so-card-header {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 16px; border-bottom: 1px solid var(--border); background: #fafbfc;
        cursor: pointer;
        transition: background 0.15s;
    }
    .so-card-header:hover { background: #f1f5f9; }
    
    .so-toggle {
        color: var(--faint); font-size: 14px; margin-top: 4px; flex-shrink: 0;
        display: inline-block; user-select: none; transition: transform 0.2s ease;
        pointer-events: none;
    }
    .so-card:not(.collapsed) .so-toggle { transform: rotate(90deg); color: var(--primary); }
    .so-card.collapsed .so-body { display: none; }
    
    .so-head-main { flex: 1; min-width: 0; }
    .so-id { font-weight: 700; font-size: 17px; word-break: break-all; color: var(--ink); }
    .so-id.is-done { color: var(--success-dark); }
    .so-billno-row {
        display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin-top: 6px;
    }
    .so-billno-count {
        font-size: 12px; font-weight: 700; color: var(--muted);
        background: #f1f5f9; border: 1px solid var(--border);
        padding: 2px 8px; border-radius: 20px; white-space: nowrap;
    }
    .so-billno-chip {
        font-size: 13px; font-weight: 600; font-variant-numeric: tabular-nums;
        color: var(--primary-dark); background: var(--primary-light); border: 1px solid #c7dbff;
        padding: 2px 9px; border-radius: 6px; word-break: break-all;
    }
    .so-billno-chip.is-picked {
        color: var(--success-dark); background: var(--success-light); border-color: #bbf7d0;
    }
    .so-billno-chip.is-cancelled {
        color: var(--danger); background: var(--danger-light); border-color: #fecaca;
        text-decoration: line-through;
    }
    .so-sub {
        font-size: 14px; color: var(--muted); margin-top: 4px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .so-status {
        flex-shrink: 0; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 20px; white-space: nowrap;
    }
    .so-status.pending { background: var(--danger-light); color: var(--danger-dark); }
    .so-status.done { background: var(--success-light); color: var(--success-dark); }

    .so-body { padding: 0; }

    /* ===== DN Section ===== */
    .dn-section { border-top: 1px solid var(--border); }
    .dn-section:first-child { border-top: none; }
    .dn-section-header {
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        padding: 12px 16px; background: #f8fafc;
    }
    .dn-no { font-weight: 600; font-size: 15px; }
    .dn-no.is-done { color: var(--success-dark); }
    .dn-no.is-cancelled { color: var(--danger); text-decoration: line-through; }
    .dn-time { font-size: 13px; color: var(--muted); }
    
    .dnSelectAll, .chkPickOnly, .chkBill {
        width: 20px; height: 20px; accent-color: var(--primary); flex-shrink: 0; cursor: pointer;
        border: 2px solid var(--border); border-radius: 4px;
    }
    .chkBill { accent-color: var(--success-dark); }
    .dn-check-label {
        display: inline-flex; align-items: center; gap: 6px; cursor: pointer;
        font-size: 13px; font-weight: 600; color: var(--muted); white-space: nowrap;
    }
    .dn-check-label:has(.chkBill) { color: var(--success-dark); }
    .dn-cancelled-badge {
        font-size: 13px; font-weight: 600; color: var(--danger); margin-left: auto; white-space: nowrap;
        background: var(--danger-light); padding: 2px 8px; border-radius: 4px;
    }
    .dn-picked-badge {
        font-size: 13px; font-weight: 600; color: var(--success-dark); margin-left: auto; white-space: nowrap;
        background: var(--success-light); padding: 2px 8px; border-radius: 4px;
    }
    .dn-section.dn-cancelled > .dn-section-header { background: var(--danger-light); }
    .dn-body { padding: 8px 16px 16px; }

    /* เช็คบ็อกซ์ "ได้รับบิลแล้ว" (ตัวจำของสโตร์) */
    .bill-received-label {
        display: inline-flex; align-items: center; gap: 5px; cursor: pointer;
        font-size: 12px; font-weight: 600; color: var(--muted); white-space: nowrap;
        border: 1px dashed var(--border, #d1d5db); border-radius: 6px; padding: 2px 8px; margin-left: 6px;
    }
    .bill-received-label input { width: 15px; height: 15px; cursor: pointer; accent-color: #059669; }
    .bill-received-label.is-received {
        color: #059669; border-style: solid; border-color: #6ee7b7; background: #ecfdf5;
    }

    /* Dropdown เลือกชั้นวาง (custom ใหญ่กว่า datalist) */
    .ms-shelf-list {
        display: none; position: absolute; left: 0; right: 0; top: calc(100% + 6px);
        background: #fff; border: 1px solid #d6dbe3; border-radius: 10px;
        box-shadow: 0 12px 30px rgba(0,0,0,.15); max-height: 340px; overflow-y: auto; z-index: 10;
        padding: 6px;
    }
    .ms-shelf-opt {
        padding: 12px 14px; font-size: 15.5px; font-weight: 500; color: #1f2937;
        border-radius: 8px; cursor: pointer; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .ms-shelf-opt:hover { background: #eff4ff; color: #2853d5; }

    /* ===== Item Table Head & PO Rows ===== */
    .item-col-head {
        display: flex; justify-content: space-between; gap: 10px;
        padding: 8px 12px; font-size: 12px; color: var(--muted);
        font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        background: #f1f5f9; border-radius: 6px; margin-bottom: 8px;
    }

    .po-row { padding: 12px; border: 1px solid var(--border); border-radius: 8px; margin-bottom: 10px; background: #ffffff; }
    .po-row:last-of-type { margin-bottom: 0; }
    .po-row.po-row-done { background: var(--success-light); border-color: #bbf7d0; }

    .po-row-head { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; flex-wrap: wrap; }
    .po-row-head input[type="checkbox"] {
        width: 20px; height: 20px; accent-color: var(--primary); flex-shrink: 0;
        border: 2px solid var(--border); border-radius: 4px; cursor: pointer;
    }
    
    .source-tag {
        font-size: 11px; font-weight: 700; color: var(--muted); border: 1px solid var(--border);
        padding: 2px 8px; border-radius: 4px; text-transform: uppercase; background: var(--card-bg);
    }
    .po-num { font-weight: 700; font-size: 15px; color: var(--ink); }

    .item-row {
        display: flex; justify-content: space-between; align-items: baseline; gap: 10px;
        padding: 6px 12px; border-bottom: 1px dashed var(--border);
    }
    .item-row:last-child { border-bottom: none; }
    .item-row .item-name { font-size: 14px; color: var(--ink); font-weight: 500; }
    .item-row .item-qty { font-size: 14px; font-weight: 700; color: var(--ink); white-space: nowrap; }

    .po-row-meta, .item-row-meta {
        display: block; font-size: 13px; color: var(--muted); font-weight: 500;
        margin-top: 8px; padding-left: 12px;
    }
    .po-row-meta.checkout-meta { color: var(--success-dark); font-weight: 600; }
    .no-dn-note { font-size: 14px; color: var(--muted); padding: 12px; text-align: center; }

    /* ===== Float Bar ===== */
    .checkout-floatbar {
        position: fixed; right: 24px; bottom: 24px; z-index: 50;
        display: flex; align-items: center; gap: 12px;
        padding: 12px 20px; background: #0f172a; 
        border: 1px solid rgba(255,255,255,0.1); border-radius: 30px;
        box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.3);
        color: #ffffff;
    }
    .checkout-floatbar .floatbar-count { font-size: 14px; font-weight: 500; color: #94a3b8; }
    .checkout-floatbar .floatbar-count strong { color: #38bdf8; font-size: 15px; }
    .checkout-floatbar .btn-ghost { background: rgba(255,255,255,0.1); color: #ffffff; border: none; }
    .checkout-floatbar .btn-ghost:hover { background: rgba(255,255,255,0.2); }
    body.has-floatbar { padding-bottom: 90px; }

    /* ===== Pagination ===== */
    .pager { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; justify-content: center; margin-top: 30px; }
    .pager a, .pager span {
        display: inline-block; min-width: 40px; text-align: center; padding: 8px 12px;
        border: 1px solid var(--border); border-radius: 6px; font-size: 14px; font-weight: 600;
        text-decoration: none; color: var(--ink); background: var(--card-bg);
        box-shadow: var(--shadow-sm); transition: all 0.2s;
    }
    .pager a:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .pager span.current { background: var(--primary); color: var(--on-primary); border-color: var(--primary); }
    .pager span.disabled { color: var(--faint); background: #f8fafc; cursor: not-allowed; }

    @media (max-width: 1400px){ .top-banner { flex-wrap: wrap; } }
    @media (max-width: 1200px){ .so-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 768px){ .so-grid { grid-template-columns: 1fr; } main { padding: 12px; } .top-banner { padding: 12px; } }
    .btn-move-shelf{ padding:5px 12px; border:1px solid var(--primary,#3E6AE1); color:var(--primary,#3E6AE1); background:#fff; border-radius:6px; font-size:13px; font-family:inherit; cursor:pointer; }
    .btn-move-shelf:hover{ background:#eef2ff; }
    /* ย้ายชั้นรายสินค้า */
    .item-move-row{ display:flex; align-items:center; justify-content:space-between; gap:8px; padding:2px 4px 8px; border-bottom:1px dashed var(--border); margin-bottom:6px; }
    .item-move-shelf{ font-size:12px; color:var(--muted); }
    .btn-move-line{ padding:3px 10px; border:1px solid var(--primary,#3E6AE1); color:var(--primary,#3E6AE1); background:#fff; border-radius:6px; font-size:12px; font-family:inherit; cursor:pointer; white-space:nowrap; }
    .btn-move-line:hover{ background:#eef2ff; }
</style>
</head>
<body lang="th">

<div class="top-banner">
    <div class="title-group">
        <span class="h1">จัดบิลส่งของ</span>
    </div>

    <div class="toolbar-container">
        <form class="toolbar" id="filterForm" method="GET" action="{{ url()->current() }}">
            <input type="hidden" name="filter_status" value="{{ request('filter_status', 'pending') }}">
            
            <input type="search" name="SONum" id="searchSO" value="{{ request('SONum') }}" placeholder=" ค้นหาเลข SO..." autocomplete="off">
            <input type="search" name="PONum" id="searchPO" value="{{ request('PONum') }}" placeholder=" ค้นหาเลข PO..." autocomplete="off">
            <input type="search" name="BillNo" id="searchBillNo" value="{{ request('BillNo') }}" placeholder=" ค้นหาเลขบิล..." autocomplete="off">
            
            <label class="field-label">
                วันที่เปิดบิล
                <input type="date" name="bill_date" id="searchDate" value="{{ $billDate }}">
            </label>

            <button type="button" class="btn-ghost" onclick="clearAllFilters()">ล้าง</button>
        </form>

        @php
            $currentFilter = request('filter_status', 'pending');
            $qsWithoutFilter = request()->except(['filter_status', 'page']);
            
            $urlPending = url()->current() . '?' . http_build_query(array_merge($qsWithoutFilter, ['filter_status' => 'pending']));
            $urlAll     = url()->current() . '?' . http_build_query(array_merge($qsWithoutFilter, ['filter_status' => 'all']));
        @endphp

        <div class="list-meta">
            <div class="filter-pills">
                <a href="{{ $urlPending }}" class="filter-pill {{ $currentFilter === 'pending' ? 'active' : '' }}">บิลยังค้างอยู่</a>
                <a href="{{ $urlAll }}" class="filter-pill {{ $currentFilter === 'all' ? 'active' : '' }}">ดูทั้งหมด</a>
            </div>
        </div>
    </div>

    <div class="user-info">ผู้ใช้งาน: <span>{{ $creator }}</span></div>
</div>

<input type="hidden" id="inpUser" value="{{ $creator }}">

<main>
    @if ($bills->count() > 0)
    <div class="so-grid">
        @foreach ($bills as $bill)
            @php
                $soIdSafe = 'so_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $bill->so_id);
                $dnList   = $bill->bills->isNotEmpty() ? $bill->bills : collect([(object) ['dn_no' => null, 'time' => null]]);
                $sourceLabel = fn ($type) => $type === 'internal' ? 'ภายใน' : ($type === 'external' ? 'ระบบใหม่' : '');
                $poClean = fn ($raw) => preg_replace('/^\s*PO\s*/i', '', (string) $raw);
            @endphp
            <div class="so-card collapsed" id="{{ $soIdSafe }}" data-done="{{ $bill->all_done ? 1 : 0 }}">
                <div class="so-card-header" role="button" tabindex="0" aria-expanded="false"
                     onclick="toggleSoCard('{{ $soIdSafe }}')">
                    <span class="so-toggle" aria-hidden="true">▶</span>
                    @php
                        $headerBills = $bill->bills->filter(fn ($d) => !empty($d->dn_no))
                            ->unique('dn_no')->values();
                    @endphp
                    <div class="so-head-main">
                        <div class="so-id {{ $bill->all_done ? 'is-done' : '' }}">{{ $bill->so_id }}</div>
                        @if($headerBills->isNotEmpty())
                            <div class="so-billno-row">
                                <span class="so-billno-count">{{ $headerBills->count() }} บิล</span>
                                @foreach($headerBills as $hb)
                                    <span class="so-billno-chip {{ ($hb->cancelled ?? false) ? 'is-cancelled' : (($hb->picked ?? false) ? 'is-picked' : '') }}"
                                          title="{{ ($hb->cancelled ?? false) ? 'ยกเลิกแล้ว' : (($hb->picked ?? false) ? 'จัดบิลแล้ว' : 'รอจัดบิล') }}">{{ $hb->dn_no }}</span>
                                @endforeach
                            </div>
                        @endif
                        <div class="so-sub">
                            @if($bill->customer_id) <span style="font-weight:600;">{{ $bill->customer_id }}</span> @endif
                            @if($bill->customer_id && $bill->customer_name) · @endif
                            @if($bill->customer_name) {{ $bill->customer_name }} @endif
                        </div>
                    </div>
                    @if ($bill->all_done)
                        <span class="so-status done">จัดของแล้ว</span>
                    @else
                        <span class="so-status pending">⏳ รอดำเนินการ</span>
                    @endif
                </div>

                <div class="so-body">
                    @if ($bill->bills->isEmpty())
                        <div class="no-dn-note">ยังไม่พบข้อมูลบิลขนส่ง (tblbill) ของ SO นี้</div>
                    @endif

                    @php
                        $itemHostDnIdx = collect($dnList)->search(fn ($d) => !($d->cancelled ?? false) && !($d->picked ?? false));
                        if ($itemHostDnIdx === false) {
                            $itemHostDnIdx = collect($dnList)->search(fn ($d) => !($d->cancelled ?? false));
                        }
                        if ($itemHostDnIdx === false) $itemHostDnIdx = 0;

                        $itemsElId   = $soIdSafe . '_items';
                        $selectAllId = $soIdSafe . '_selectall';
                        $hostDn      = $dnList[$itemHostDnIdx] ?? null;
                    @endphp

                    @foreach ($dnList as $dnIdx => $dn)
                        @php
                            $dnElId       = $soIdSafe . '_dn' . $dnIdx;
                            $isCancelled  = $dn->cancelled ?? false;
                            $isPicked     = $dn->picked ?? false;
                            $isItemHost   = ($dnIdx === $itemHostDnIdx);
                            // จัดบิลออก (dispatch bill) แยกอิสระจากเช็คเอ้าของ (ของติ๊กราย PO เอง)
                            $hasBill      = !$isCancelled && !$isPicked && ($dn->dn_no ?? null);
                        @endphp
                        <div class="dn-section {{ $isCancelled ? 'dn-cancelled' : '' }}" id="{{ $dnElId }}" data-dnno="{{ $dn->dn_no ?? '' }}">
                            <div class="dn-section-header">
                                @if ($hasBill)
                                    <label class="dn-check-label" title="จัดบิลออก (ไม่เกี่ยวกับเช็คเอ้าของ)">
                                        <input type="checkbox" class="chkBill" data-dnno="{{ $dn->dn_no }}" onchange="updateFloatBar()">
                                        <span>จัดบิลออก</span>
                                    </label>
                                @endif
                                <span class="dn-no {{ $isCancelled ? 'is-cancelled' : ($isPicked ? 'is-done' : '') }}">{{ ($dn->dn_no ?? null) ? $dn->dn_no : '— (ไม่มีเลขที่บิล)' }}</span>
                                @if (!empty($dn->dn_no))
                                    <label class="bill-received-label {{ ($dn->bill_received ?? false) ? 'is-received' : '' }}"
                                           title="ติ๊กว่าสโตร์ได้รับบิลนี้แล้ว (กันบิลปริ้นแล้วหาย)">
                                        <input type="checkbox" class="chkBillReceived" data-billid="{{ $dn->dn_no }}"
                                               {{ ($dn->bill_received ?? false) ? 'checked' : '' }}
                                               onchange="toggleBillReceived(this)">
                                        <span>ได้รับบิลแล้ว</span>
                                    </label>
                                @endif
                                
                                @if (!empty($dn->time))
                                    <span class="dn-time">{{ \Carbon\Carbon::parse($dn->time)->addYears(543)->format('d/m/Y H:i') }}</span>
                                @endif
                                
                                @if (!empty($dn->opened_by))
                                    <span class="dn-time">ผู้เปิดบิล: {{ $dn->opened_by }}</span>
                                @endif
                                @if ($isCancelled)
                                    <span class="dn-cancelled-badge">ยกเลิกแล้ว</span>
                                @elseif ($isPicked)
                                    <span class="dn-picked-badge">
                                        จัดของแล้ว{{ !empty($dn->picked_by) ? ' โดย ' . $dn->picked_by : '' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="dn-section" id="{{ $itemsElId }}" data-dnno="{{ $hostDn->dn_no ?? '' }}" data-selectall="{{ $selectAllId }}">
                        <div class="dn-body">
                            @forelse ($bill->groups->sortByDesc('todo') as $g)
                                <div class="po-row {{ $g->todo ? '' : 'po-row-done' }}">
                                    <div class="po-row-head">
                                        @if ($g->todo)
                                            <input type="checkbox" class="chkGroup" value="{{ $g->type }}:{{ $g->id }}"
                                                   onchange="updateDnButton(document.getElementById('{{ $itemsElId }}'))">
                                        @endif
                                        @if ($sourceLabel($g->type))
                                            <span class="source-tag">{{ $sourceLabel($g->type) }}</span>
                                        @endif
                                        <span class="po-num">PO: {{ $poClean($g->po_display) }}</span>
                                        @if ($g->type === 'external' && ($g->multi_round ?? false))
                                            <span class="source-tag" style="background:var(--primary-light);color:var(--primary-dark);border-color:#c7dbff;">รอบที่ {{ $g->round_no }}</span>
                                        @endif
                                    </div>

                                    <div class="item-col-head"><span>ชื่อสินค้า</span><span>จำนวน</span></div>

                                    @foreach ($g->items as $it)
                                        <div class="item-row">
                                            <div class="item-name">{{ $it->item_name }}</div>
                                            <div class="item-qty">{{ rtrim(rtrim(number_format($it->item_quantity, 2), '0'), '.') }}</div>
                                        </div>
                                        @if ($g->type === 'external' && $g->todo && ($it->id ?? null))
                                            {{-- ย้ายชั้น "รายสินค้า" (บางรายการในรอบเดียวไปคนละชั้นได้) --}}
                                            <div class="item-move-row">
                                                <span class="item-move-shelf">ชั้น: {{ $it->shelf ?: '—' }}</span>
                                                <button type="button" class="btn-move-line"
                                                        data-lineid="{{ $it->id }}"
                                                        data-name="{{ $it->item_name }}"
                                                        data-shelf="{{ $it->shelf }}"
                                                        onclick="openMoveShelfLineBtn(this)">ย้ายชั้นรายการนี้</button>
                                            </div>
                                        @endif
                                    @endforeach

                                    @if ($g->type === 'external')
                                        @php
                                            $locLines = collect($g->items)
                                                ->filter(fn ($it) => ($it->shelf ?? null) || ($it->done_by ?? null))
                                                ->unique(fn ($it) => ($it->shelf ?? '') . '|' . ($it->done_by ?? '') . '|' . ($it->done_at ?? ''))
                                                ->values();
                                        @endphp
                                        @foreach ($locLines as $it)
                                            <div class="item-row-meta">
                                                ที่เก็บ: {{ $it->shelf ?? '—' }} · จัดโดย: {{ $it->done_by ?? '—' }}
                                                @if (!empty($it->done_at)) ({{ \Carbon\Carbon::parse($it->done_at)->addYears(543)->format('d/m/Y H:i') }}) @endif
                                            </div>
                                        @endforeach
                                    @endif

                                    @if ($g->type !== 'external')
                                        <div class="po-row-meta">
                                            ที่เก็บ: {{ $g->location ?: '—' }} · จัดโดย: {{ $g->done_by ?: '—' }}
                                            @if ($g->done_at) ({{ \Carbon\Carbon::parse($g->done_at)->addYears(543)->format('d/m/Y H:i') }}) @endif
                                        </div>
                                    @endif
                                    @if (!$g->todo)
                                        <div class="po-row-meta checkout-meta">
                                            เช็คของออก{{ ($g->checkout_by ?? null) ? ' โดย ' . $g->checkout_by : '' }}
                                            @if ($g->checkout_at ?? null)
                                                ({{ \Carbon\Carbon::parse($g->checkout_at)->addYears(543)->format('d/m/Y H:i') }})
                                            @endif
                                        </div>
                                    @else
                                        <div class="po-row-meta" style="margin-top:6px;">
                                            <button type="button" class="btn-move-shelf"
                                                onclick="openMoveShelf('{{ $poClean($g->po_display) }}','{{ $bill->so_id }}','{{ $g->receive_id ?? '' }}')">ย้ายชั้นวาง{{ ($g->type === 'external' && ($g->multi_round ?? false)) ? ' (รอบที่ ' . $g->round_no . ')' : ' (PO/SO นี้)' }}</button>
                                        </div>
                                    @endif
                                </div>
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
        <div class="empty-state">
            ไม่พบบิลที่ตรงกับเงื่อนไขการค้นหา
        </div>
    @endif

    @if ($bills->lastPage() > 1)
        <div class="pager">
            @php
                $qs = collect(request()->except('page'))->toArray();
                $mkUrl = fn($p) => url()->current() . '?' . http_build_query(array_merge($qs, ['page' => $p]));
            @endphp
            @if ($bills->currentPage() > 1)
                <a href="{{ $mkUrl(1) }}">« แรก</a>
                <a href="{{ $mkUrl($bills->currentPage() - 1) }}">‹ ก่อนหน้า</a>
            @else
                <span class="disabled">« แรก</span>
                <span class="disabled">‹ ก่อนหน้า</span>
            @endif

            @php
                $start = max(1, $bills->currentPage() - 3);
                $end   = min($bills->lastPage(), $bills->currentPage() + 3);
            @endphp
            @for ($p = $start; $p <= $end; $p++)
                @if ($p == $bills->currentPage())
                    <span class="current">{{ $p }}</span>
                @else
                    <a href="{{ $mkUrl($p) }}">{{ $p }}</a>
                @endif
            @endfor

            @if ($bills->currentPage() < $bills->lastPage())
                <a href="{{ $mkUrl($bills->currentPage() + 1) }}">ถัดไป ›</a>
                <a href="{{ $mkUrl($bills->lastPage()) }}">สุดท้าย »</a>
            @else
                <span class="disabled">ถัดไป ›</span>
                <span class="disabled">สุดท้าย »</span>
            @endif
        </div>
    @endif
</main>

<div class="checkout-floatbar" id="floatBar" hidden>
    <span class="floatbar-count">เลือกไว้ <strong id="floatCount">0</strong> รายการ</span>
    <button type="button" class="btn-ghost" onclick="clearAllChecks()">ล้างค่า</button>
    <button type="button" class="btn-success" id="floatSubmitBtn" onclick="submitAllCheckout()">บันทึก</button>
</div>

{{-- Modal เลือกประเภทการขนส่ง (ตอนจัดบิล) --}}
<div id="transportModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:300; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; padding:22px; width:min(92vw,380px); box-shadow:0 12px 40px rgba(0,0,0,.25);">
        <div style="font-size:16px; font-weight:600; margin-bottom:4px;">เลือกประเภทการขนส่ง</div>
        <div style="font-size:13px; color:#6b7280; margin-bottom:16px;">สำหรับบิลที่กำลังจัด</div>
        <div style="display:flex; flex-direction:column; gap:10px;">
            <button type="button" onclick="pickTransport('company')" style="padding:14px; border:1.5px solid #3E6AE1; border-radius:10px; background:#eef2ff; color:#1e3a8a; font-size:15px; font-weight:600; cursor:pointer;">ขนส่งโดยรถบริษัท</button>
            <button type="button" onclick="pickTransport('private')" style="padding:14px; border:1.5px solid #16a34a; border-radius:10px; background:#f0fdf4; color:#166534; font-size:15px; font-weight:600; cursor:pointer;">บริษัทขนส่ง (เอกชน)</button>
            <button type="button" onclick="closeTransport()" style="padding:10px; border:1px solid #e5e7eb; border-radius:10px; background:#fff; color:#6b7280; font-size:14px; cursor:pointer;">ยกเลิก</button>
        </div>
    </div>
</div>

{{-- Modal ย้ายชั้นวาง (ต่อ PO+SO) --}}
<div id="moveShelfModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:300; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; padding:20px; width:min(92vw,380px); box-shadow:0 12px 40px rgba(0,0,0,.25);">
        <div style="font-weight:600; margin-bottom:10px;">ย้ายชั้นวาง — <span id="msLabel"></span></div>
        <div style="position:relative;">
            <input type="search" id="msShelf" placeholder="เลือกหรือพิมพ์ชั้นวาง..." autocomplete="off"
                   oninput="renderShelfOptions(this.value)" onfocus="renderShelfOptions(this.value)"
                   style="width:100%; padding:13px 14px; border:1px solid #d6dbe3; border-radius:10px; box-sizing:border-box; font-family:inherit; font-size:16px;">
            <div id="msShelfList" class="ms-shelf-list"></div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:14px;">
            <button type="button" class="btn-ghost" onclick="closeMoveShelf()">ยกเลิก</button>
            <button type="button" class="btn-success" id="msSaveBtn" onclick="confirmMoveShelf()">ย้าย</button>
        </div>
    </div>
</div>

<script>
const SUBMIT_URL = "{{ route('store.checkout.submit') }}";
const MOVE_SHELF_URL = "{{ route('shelfsale.move') }}";
const BILL_RECEIVED_URL = "{{ route('store.checkout.billReceived') }}";
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

/* ===== Dropdown เลือกชั้นวาง (custom) ===== */
const SHELF_OPTIONS = @json(\App\Http\Controllers\ShelfsaleController::SHELF_OPTIONS);
function _msEscHtml(s){ return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
function renderShelfOptions(q){
    const list = document.getElementById('msShelfList');
    if(!list) return;
    const query = (q||'').trim().toLowerCase();
    const items = SHELF_OPTIONS.filter(s => !query || String(s).toLowerCase().includes(query));
    list.innerHTML = items.map(s => `<div class="ms-shelf-opt" data-val="${_msEscHtml(s)}">${_msEscHtml(s)}</div>`).join('');
    list.style.display = items.length ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', function(){
    const list = document.getElementById('msShelfList');
    const inp  = document.getElementById('msShelf');
    if(list){
        list.addEventListener('mousedown', function(e){
            const opt = e.target.closest('.ms-shelf-opt');
            if(!opt) return;
            e.preventDefault();
            if(inp) inp.value = opt.dataset.val;
            list.style.display = 'none';
        });
    }
    if(inp){
        inp.addEventListener('blur', () => setTimeout(() => { if(list) list.style.display = 'none'; }, 150));
    }
});

/* ===== สโตร์ติ๊กว่าได้รับบิลแล้ว (บันทึก tblbill.status_bill) ===== */
async function toggleBillReceived(cb){
    const billid = cb.dataset.billid;
    const label  = cb.closest('.bill-received-label');
    // เอาติ๊กออก -> ยืนยันก่อน
    if(!cb.checked){
        if(!confirm('เอาการติ๊ก "ได้รับบิลแล้ว" ของบิล ' + billid + ' ออกใช่ไหม?')){
            cb.checked = true;   // ยกเลิก -> ติ๊กกลับ
            return;
        }
    }
    const received = cb.checked;
    cb.disabled = true;
    try{
        const res = await fetch(BILL_RECEIVED_URL, {
            method:'POST',
            headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json','X-Requested-With':'XMLHttpRequest' },
            body: JSON.stringify({ billid: billid, received: received }),
        });
        const data = await res.json();
        if(res.ok && data.ok){
            if(label) label.classList.toggle('is-received', received);
        } else {
            cb.checked = !received;   // rollback
            alert(data.message || 'บันทึกไม่สำเร็จ');
        }
    }catch(e){
        cb.checked = !received;       // rollback
        alert('เชื่อมต่อไม่สำเร็จ');
    }finally{ cb.disabled = false; }
}

/* ===== ย้ายชั้นวาง (ต่อ PO+SO) ===== */
let msTarget = null;
function openMoveShelf(po, so, receiveId){
    msTarget = { mode:'round', po: po, so: so, receive_id: (receiveId || '') };
    document.getElementById('msLabel').textContent = 'PO ' + po + ' / SO ' + so + (receiveId ? ' · รอบรับเข้า #' + receiveId : '');
    document.getElementById('msShelf').value = '';
    document.getElementById('moveShelfModal').style.display = 'flex';
    setTimeout(() => document.getElementById('msShelf').focus(), 30);
}
// ย้ายชั้น "รายสินค้า" (line เดียว) — บางรายการในรอบเดียวไปคนละชั้น
function openMoveShelfLineBtn(btn){
    const lineId = btn.dataset.lineid;
    const name   = btn.dataset.name || '';
    const shelf  = btn.dataset.shelf || '';
    msTarget = { mode:'line', line_id: lineId, name: name };
    document.getElementById('msLabel').textContent = 'สินค้า: ' + name + (shelf ? ' · ชั้นเดิม ' + shelf : '');
    document.getElementById('msShelf').value = '';
    document.getElementById('moveShelfModal').style.display = 'flex';
    setTimeout(() => document.getElementById('msShelf').focus(), 30);
}
function closeMoveShelf(){ document.getElementById('moveShelfModal').style.display = 'none'; msTarget = null; }
async function confirmMoveShelf(){
    if (!msTarget) return;
    const shelf = document.getElementById('msShelf').value.trim();
    if (!shelf){ alert('กรุณาเลือกชั้นวาง'); return; }
    const btn = document.getElementById('msSaveBtn'); btn.disabled = true; btn.textContent = 'กำลังย้าย...';
    try {
        const res = await fetch(MOVE_SHELF_URL, {
            method: 'POST',
            headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json' },
            body: JSON.stringify(
                msTarget.mode === 'line'
                    ? { line_id: msTarget.line_id, shelf: shelf }
                    : { po: msTarget.po, so: msTarget.so, shelf: shelf, po_receive_id: (msTarget.receive_id || null) }
            )
        });
        const data = await res.json().catch(() => null);
        if (!res.ok || !data || !data.ok){ alert((data && data.message) || 'ย้ายชั้นไม่สำเร็จ'); btn.disabled = false; btn.textContent = 'ย้าย'; return; }
        window.location.reload();
    } catch(e){ console.error(e); alert('เกิดข้อผิดพลาด'); btn.disabled = false; btn.textContent = 'ย้าย'; }
}

let searchTimer;
function triggerAutoSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        const soVal = document.getElementById('searchSO').value.trim();
        const poVal = document.getElementById('searchPO').value.trim();
        const billVal = document.getElementById('searchBillNo').value.trim();

        if (soVal === '' && poVal === '' && billVal === '') {
            const currentFilter = "{{ request('filter_status', 'pending') }}";
            const url = new URL(window.location.href);
            url.searchParams.delete('SONum');
            url.searchParams.delete('PONum');
            url.searchParams.delete('BillNo');
            url.searchParams.set('filter_status', currentFilter);
            window.location.href = url.toString();
            return;
        }
        document.getElementById('filterForm').submit();
    }, 500);
}

function triggerDateSearch() {
    document.getElementById('filterForm').submit();
}

function clearAllFilters() {
    const currentFilter = "{{ request('filter_status', 'pending') }}";
    const url = new URL(window.location.href);
    url.search = '';
    url.searchParams.set('filter_status', currentFilter);
    window.location.href = url.toString();
}

document.getElementById('searchSO').addEventListener('input', triggerAutoSearch);
document.getElementById('searchPO').addEventListener('input', triggerAutoSearch);
document.getElementById('searchBillNo').addEventListener('input', triggerAutoSearch);
document.getElementById('searchDate').addEventListener('change', triggerDateSearch);

function toggleSoCard(id) {
    const card = document.getElementById(id);
    if (!card) return;
    const willOpen = card.classList.contains('collapsed');   // ตอนนี้ปิดอยู่ = กำลังจะเปิด
    if (willOpen) {
        // accordion: เปิดอันใหม่ -> ปิดอันที่เปิดค้างอยู่ทั้งหมด
        document.querySelectorAll('.so-card:not(.collapsed)').forEach(other => {
            if (other === card) return;
            other.classList.add('collapsed');
            const h = other.querySelector('.so-card-header');
            if (h) h.setAttribute('aria-expanded', 'false');
        });
    }
    const collapsed = card.classList.toggle('collapsed');
    const header = card.querySelector('.so-card-header');
    if (header) header.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
}

function updateDnButton(itemsEl) {
    // sync เฉพาะ "เลือกของทั้งหมด" กับ checkbox ของ (chkGroup) เท่านั้น — ไม่ไปยุ่งกับ checkbox บิล
    const allBoxes = itemsEl.querySelectorAll('.chkGroup');
    const checked  = itemsEl.querySelectorAll('.chkGroup:checked').length;
    const selectAll = document.getElementById(itemsEl.dataset.selectall);
    if (selectAll) {
        selectAll.checked       = allBoxes.length > 0 && checked === allBoxes.length;
        selectAll.indeterminate = checked > 0 && checked < allBoxes.length;
    }
    updateFloatBar();
}

function toggleDnSelectAll(dnEl, checked) {
    dnEl.querySelectorAll('.chkGroup').forEach(cb => { cb.checked = checked; });
    updateDnButton(dnEl);
}

function updateFloatBar() {
    const checkedGroups = document.querySelectorAll('.chkGroup:checked').length;  // เช็คเอ้าของ
    const checkedBills  = document.querySelectorAll('.chkBill:checked').length;   // จัดบิลออก
    const total = checkedGroups + checkedBills;
    const bar = document.getElementById('floatBar');
    const cnt = document.getElementById('floatCount');
    if (cnt) cnt.textContent = total;
    if (bar) bar.hidden = total === 0;
    document.body.classList.toggle('has-floatbar', total > 0);
}

function clearAllChecks() {
    document.querySelectorAll('.chkGroup:checked').forEach(cb => { cb.checked = false; });
    document.querySelectorAll('.chkBill:checked').forEach(cb => { cb.checked = false; });
    document.querySelectorAll('.dnSelectAll').forEach(cb => { cb.checked = false; cb.indeterminate = false; });
    updateFloatBar();
}

let pendingSubmit = null;   // เก็บ {ids, dnNos, totalCount} ระหว่างรอเลือกประเภทขนส่ง

function submitAllCheckout() {
    // เช็คเอ้าของ (ids) มาจาก checkbox ของ (chkGroup) เท่านั้น
    // จัดบิลออก (dnNos) มาจาก checkbox บิล (chkBill) ที่ติ๊กเองเท่านั้น — ไม่ derive จาก PO
    const checkedBoxes = Array.from(document.querySelectorAll('.chkGroup:checked'));
    const billBoxes    = Array.from(document.querySelectorAll('.chkBill:checked'));
    if (!checkedBoxes.length && !billBoxes.length) return;

    const ids   = Array.from(new Set(checkedBoxes.map(c => c.value)));
    const dnNos = Array.from(new Set(billBoxes.map(c => c.dataset.dnno).filter(Boolean)));

    const totalCount = ids.length + dnNos.length;
    pendingSubmit = { ids, dnNos, totalCount };

    // มีจัดบิลออก -> ต้องเลือกประเภทขนส่งก่อน
    if (dnNos.length > 0) {
        document.getElementById('transportModal').style.display = 'flex';
        return;
    }
    // เช็คเอ้าของอย่างเดียว (ไม่จัดบิล) -> ยืนยันแล้วส่งเลย
    if (!confirm('ยืนยันบันทึกข้อมูล ' + totalCount + ' รายการใช่หรือไม่?')) { pendingSubmit = null; return; }
    doSubmit(null);
}

function closeTransport() {
    document.getElementById('transportModal').style.display = 'none';
    pendingSubmit = null;
}

function pickTransport(type) {
    document.getElementById('transportModal').style.display = 'none';
    if (!pendingSubmit) return;
    doSubmit(type);
}

async function doSubmit(transportType) {
    if (!pendingSubmit) return;
    const { ids, dnNos } = pendingSubmit;
    const btn = document.getElementById('floatSubmitBtn');
    btn.disabled = true;
    try {
        const res = await fetch(SUBMIT_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ ids, dn_nos: dnNos, transport_type: transportType }),
        });
        const data = await res.json();
        if (res.ok && data.ok) {
            window.location.reload();
        } else {
            alert(data.message || 'บันทึกไม่สำเร็จ');
            btn.disabled = false;
        }
    } catch (e) {
        console.error(e);
        alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
        btn.disabled = false;
    } finally {
        pendingSubmit = null;
    }
}
</script>
</body>
</html>