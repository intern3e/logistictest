<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>รายการเข้าPO ของนอก - ค้นหา</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Sarabun', sans-serif; background-color: #f5f7fa; color: #333; font-size: clamp(12px, 0.45vw + 7px, 16px); }
        .mono { font-family: 'JetBrains Mono', ui-monospace, monospace; }
        .top-nav { background: #ffffff; border-bottom: 1px solid #e2e8f0; color: #1b2d4f; padding: 15px 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .bg-yellow { background-color: #ffc107 !important; color: #000 !important; }
        .bg-green  { background-color: #16a34a !important; color: #fff !important; }
        .bg-red    { background-color: #dc3545 !important; color: #fff !important; }
        .nav-title { display: flex; align-items: center; gap: 15px; font-size: clamp(15px, 1vw + 6px, 20px); font-weight: 600; color: #1b2d4f; }
        .user-info { font-size: clamp(11px, 0.5vw + 6px, 13px); font-weight: 400; opacity: 0.95; }
        .nav-btn, .back-button { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: #3E6AE1; color: #ffffff; text-decoration: none; border: 1px solid #3E6AE1; border-radius: 6px; font-size: clamp(11px, 0.55vw + 5px, 14px); font-weight: 600; white-space: nowrap; transition: background .2s ease, color .2s ease; }
        .nav-btn:hover, .back-button:hover { background: #2f56c4; color: #ffffff; border-color: #2f56c4; }
        .search-container { max-width: 1400px; margin: 24px auto 16px; padding: 0 24px; }
        .search-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 20px 24px; }
        .search-box { display: flex; gap: 12px; align-items: flex-end; }
        .search-input-group { flex: 1; display: flex; flex-direction: column; gap: 8px; }
        .search-label { font-size: clamp(11px, 0.5vw + 6px, 13px); font-weight: 700; color: #1b2d4f; text-transform: uppercase; letter-spacing: .8px; }
        .search-input { width: 100%; font-family: inherit; font-size: clamp(12px, 0.55vw + 6px, 15px); padding: 12px 16px; border: 2px solid #e2e6ec; border-radius: 6px; background: white; color: #1e293b; outline: none; transition: all .22s ease; box-shadow: 0 1px 3px rgba(27,45,79,.08); }
        .search-input:focus { border-color: #3E6AE1; box-shadow: 0 0 0 4px rgba(62,106,225,.12), 0 3px 12px rgba(27,45,79,.10); }
        .search-buttons { display: flex; gap: 10px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; border-radius: 6px; font-family: inherit; font-size: clamp(12px, 0.55vw + 6px, 14px); font-weight: 600; border: 1px solid transparent; cursor: pointer; transition: all .2s ease; white-space: nowrap; color: white; box-shadow: 0 1px 3px rgba(27,45,79,.08); }
        .btn:active { transform: translateY(1px); box-shadow: none; }
        .btn-primary { background: #3E6AE1; } .btn-primary:hover { background: #2f56c4; }
        .btn-ghost { background: #ffffff; color: #334155; border-color: #d4d9e2; } .btn-ghost:hover { background: #f3f5f8; color: #171a20; }
        .main-container { max-width: 1400px; margin: 0 auto; padding: 0 24px 24px 24px; }
        .content-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
        .section { padding: 24px; border-bottom: 1px solid #f0f0f0; } .section:last-child { border-bottom: none; }
        .section-title { font-size: clamp(15px, 0.9vw + 6px, 19px); font-weight: 600; color: #1b2d4f; margin-bottom: 20px; }
        .list-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding: 16px 24px; border-bottom: 1px solid #f0f0f0; background: #fbfcfd; }
        .list-toolbar .lt-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .list-toolbar .lt-title { font-size: clamp(14px, 0.7vw + 6px, 16px); font-weight: 600; color: #1b2d4f; }
        .count-pill { display: inline-flex; align-items: center; padding: 3px 10px; background: #eef2fd; color: #2f56c4; border: 1px solid #dbe4fb; border-radius: 20px; font-size: clamp(11px, 0.5vw + 6px, 12px); font-weight: 600; }
        .enrich-progress { display: none; align-items: center; gap: 6px; font-size: clamp(10px,0.5vw + 5px,12px); color: #2f56c4; font-weight: 600; }
        .enrich-progress.show { display: inline-flex; }
        .enrich-bar { width: 90px; height: 6px; background: #e2e8f0; border-radius: 99px; overflow: hidden; }
        .enrich-bar > i { display: block; height: 100%; background: #3E6AE1; width: 0%; transition: width .2s ease; }
        .btn-sm { padding: 6px 12px; font-size: clamp(11px, 0.5vw + 6px, 12.5px); border-radius: 6px; background: #fff; color: #334155; border: 1px solid #d4d9e2; cursor: pointer; font-weight: 600; transition: all .15s ease; }
        .btn-sm:hover { background: #f3f5f8; border-color: #3E6AE1; color: #2f56c4; }
        .po-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; padding: 24px; }
        @media (max-width: 2199px) { .po-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 1199px) { .po-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px)  { .po-grid { grid-template-columns: repeat(1, 1fr); } }
        .po-card { position: relative; background: #fff; border: 1px solid #e2e6ec; border-radius: 12px; padding: 16px 18px; cursor: pointer; display: flex; flex-direction: column; gap: 10px; transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease; }
        .po-card:hover { border-color: #3E6AE1; box-shadow: 0 4px 14px rgba(62,106,225,.12); transform: translateY(-2px); }
        .po-card.active { border-color: #3E6AE1; border-width: 2px; background: #f7f9ff; box-shadow: 0 4px 14px rgba(62,106,225,.18); }
        .po-card .pc-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }
        .po-card .po-no { font-family: 'JetBrains Mono', ui-monospace, monospace; font-size: clamp(15px, 0.8vw + 7px, 18px); font-weight: 600; color: #2f56c4; word-break: break-all; }
        .po-card .po-vendor { font-size: clamp(12px, 0.55vw + 6px, 14px); color: #1f2937; font-weight: 500; }
        .po-card .po-vendor-code { font-size: clamp(10px, 0.5vw + 5px, 12px); color: #6b7280; }
        .po-card .pc-bottom { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: auto; padding-top: 6px; border-top: 1px dashed #eef1f5; }
        .po-card .po-date { font-size: clamp(10px, 0.5vw + 5px, 12px); color: #6b7280; font-variant-numeric: tabular-nums; }
        .po-card .po-date.is-loading { color: #94a3b8; font-style: italic; }
        .po-card .pc-hint { font-size: 11px; color: #94a3b8; } .po-card.active .pc-hint { color: #2f56c4; font-weight: 600; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: clamp(10px, 0.5vw + 6px, 12px); font-weight: 600; text-align: center; white-space: nowrap; }
        .status-badge.is-loading { background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; }
        .status-complete { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .status-pending  { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .status-no-data  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .status-entry    { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .list-state { padding: 60px 20px; text-align: center; color: #6b7280; }
        .list-state .ls-icon { font-size: 32px; margin-bottom: 8px; }
        .list-state .ls-text { font-size: clamp(14px, 0.7vw + 6px, 16px); font-weight: 600; color: #1f2937; }
        .list-state .ls-sub { font-size: clamp(12px, 0.55vw + 6px, 13px); margin-top: 4px; }
        .po-pager { display: flex; justify-content: center; align-items: center; gap: 4px; padding: 16px 24px; flex-wrap: wrap; border-top: 1px solid #f0f0f0; background: #fbfcfd; }
        .po-pager button { min-width: 34px; height: 34px; padding: 0 10px; border: 1px solid #d4d9e2; background: #fff; color: #334155; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .15s; }
        .po-pager button:hover:not(:disabled):not(.active) { background: #eef2fd; border-color: #3E6AE1; color: #2f56c4; }
        .po-pager button.active { background: #3E6AE1; border-color: #2f56c4; color: #fff; cursor: default; }
        .po-pager button:disabled { opacity: .45; cursor: not-allowed; }
        .po-pager .gap { padding: 0 4px; color: #94a3b8; }
        .po-pager .pg-info { font-size: 12px; color: #64748b; margin: 0 10px; }
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, .55); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); z-index: 1000; padding: 24px; overflow-y: auto; }
        .modal-overlay.show { display: block; }
        .modal-content { background: #fff; border-radius: 12px; width: 100%; max-width: 1100px; margin: 24px auto; box-shadow: 0 20px 60px rgba(0, 0, 0, .28); max-height: calc(100vh - 48px); display: flex; flex-direction: column; overflow: hidden; animation: modalIn .22s ease; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 24px; background: #f7f9ff; border-bottom: 1px solid #e2e6ec; flex: 0 0 auto; }
        .modal-head .dh-title { font-size: clamp(15px, 0.9vw + 6px, 19px); font-weight: 700; color: #1b2d4f; } .modal-head .dh-title .mono { color: #2f56c4; }
        .modal-body { overflow-y: auto; flex: 1 1 auto; }
        .modal-close { width: 36px; height: 36px; border-radius: 6px; border: 1px solid #d4d9e2; background: #fff; color: #334155; font-size: 18px; line-height: 1; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .15s ease; flex: 0 0 auto; }
        .modal-close:hover { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
        @keyframes modalIn { from { opacity: 0; transform: translateY(-14px) scale(.98); } to { opacity: 1; transform: none; } }
        .progress-timeline { padding: 20px 0; }
        .timeline-track { display: flex; justify-content: space-between; align-items: flex-start; position: relative; margin-bottom: 10px; }
        .timeline-line { position: absolute; top: 32px; left: 10%; right: 10%; height: 4px; background: #e5e7eb; z-index: 1; border-radius: 4px; }
        .timeline-line-fill { position: absolute; top: 0; left: 0; height: 100%; background: linear-gradient(90deg, #16a34a 0%, #15803d 100%); transition: width 0.6s ease-in-out; border-radius: 4px; display: block; }
        .timeline-point { display: flex; flex-direction: column; align-items: center; position: relative; z-index: 2; flex: 1; }
        .timeline-circle { width: 64px; height: 64px; border-radius: 50%; background: white; border: 4px solid #e5e7eb; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; transition: all 0.3s ease; }
        .timeline-circle.completed { background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); border-color: #16a34a; }
        .timeline-circle.current { border-color: #3E6AE1; background: white; box-shadow: 0 0 0 3px rgba(62,106,225,0.2); }
        .timeline-icon { font-size: 28px; } .timeline-circle.completed .timeline-icon { color: white; } .timeline-circle.current .timeline-icon { color: #3E6AE1; }
        .timeline-label { text-align: center; font-size: clamp(12px, 0.55vw + 6px, 14px); color: #6b7280; max-width: 160px; line-height: 1.4; }
        .timeline-point.completed .timeline-label, .timeline-point.current .timeline-label { color: #1f2937; font-weight: 600; }
        .timeline-date { font-size: clamp(10px, 0.5vw + 5px, 12px); color: #9ca3af; margin-top: 4px; }
        .timeline-point.completed .timeline-date, .timeline-point.current .timeline-date { color: #15803d; font-weight: 500; }
        .notice-box { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 16px 20px; border-radius: 8px; display: flex; align-items: center; gap: 12px; } .notice-box.hidden { display: none; }
        .notice-text { font-size: clamp(12px, 0.55vw + 6px, 14px); color: #78350f; line-height: 1.5; } .notice-text strong { color: #92400e; font-weight: 600; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .info-item { display: flex; flex-direction: column; gap: 8px; }
        .info-label { font-size: clamp(11px, 0.5vw + 6px, 13px); color: #6b7280; font-weight: 500; }
        .info-value { font-size: clamp(13px, 0.6vw + 6px, 16px); color: #1f2937; font-weight: 500; }
        .items-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .items-table thead th { background: #f9fafb; padding: 14px 16px; text-align: left; font-size: clamp(11px, 0.55vw + 5px, 13px); font-weight: 600; color: #4b5563; border-bottom: 2px solid #e5e7eb; }
        .items-table tbody td { padding: 16px; border-bottom: 1px solid #f3f4f6; font-size: clamp(12px, 0.6vw + 5px, 14px); vertical-align: top; }
        .items-table tbody tr:hover { background: #f9fafb; }
        .item-name { font-weight: 500; color: #1f2937; word-wrap: break-word; word-break: break-word; }
        .item-invoices { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; margin-top: 6px; }
        .invoice-tag { display: inline-flex; flex-direction: column; gap: 2px; padding: 8px 12px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; font-size: clamp(10px, 0.5vw + 5px, 11px); color: #1e40af; }
        .invoice-tag.mismatch { background: #fff1f2; border: 1px solid #fca5a5; color: #991b1b; }
        .invoice-tag.mismatch .inv-num { color: #b91c1c; } .invoice-tag.mismatch .inv-date { color: #f87171; } .invoice-tag.mismatch .inv-qty { color: #dc2626; }
        .mismatch-label { display: flex; align-items: center; gap: 6px; padding: 6px 10px; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 6px; font-size: 12px; font-weight: 600; color: #991b1b; grid-column: 1 / -1; }
        .inv-num { font-weight: 600; font-size: 12px; } .inv-date { color: #60a5fa; } .inv-qty { color: #059669; font-weight: 600; }
        .qty-box { text-align: center; padding: 8px 12px; background: #f3f4f6; border-radius: 6px; font-weight: 600; font-size: 15px; color: #374151; }
        .qty-summary { margin-top: 12px; font-size: 13px; font-weight: 600; grid-column: 1 / -1; } .qty-summary.excess { color: #dc2626; } .qty-summary.shortage { color: #f59e0b; }
        .items-table .status-badge { padding: 8px 16px; }
        @media (max-width: 768px) {
            .main-container, .search-container { padding: 16px; }
            .search-box { flex-direction: column; align-items: stretch; } .search-buttons { width: 100%; } .search-buttons .btn { flex: 1; }
            .timeline-track { flex-direction: column; gap: 24px; } .timeline-line { display: none; }
            .timeline-point { flex-direction: row; width: 100%; gap: 16px; } .timeline-circle { margin-bottom: 0; width: 56px; height: 56px; } .timeline-label { text-align: left; max-width: none; }
            .section { padding: 20px; } .item-invoices { grid-template-columns: 1fr; }
            .top-nav { flex-direction: column; align-items: stretch; gap: 10px; } .top-nav > div:last-child { display: flex; flex-wrap: wrap; gap: 8px; } .nav-btn, .back-button { flex: 1; justify-content: center; }
            .po-grid { padding: 16px; gap: 12px; }
            .modal-overlay { padding: 10px; } .modal-content { margin: 10px auto; max-height: calc(100vh - 20px); }
        }
    </style>
</head>
<body>
<div class="top-nav">
    <div class="nav-title">
        <div>ตามของนอก</div>
        <span class="user-info">👤 ผู้ใช้: {{ request()->get('create_by', 'Guest') }}</span>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="/pooutside/invoice?create_by={{ urlencode(request()->get('create_by', $userName ?? 'Guest')) }}" class="nav-btn">ค้นหา Invoice</a>
        {{-- <a href="dashboardreturn" class="nav-btn">เคลมสินค้านอก</a> --}}
        <a href="http://server_update:8000/solist" class="back-button">← หน้าหลัก</a>
    </div>
</div>

<div class="search-container">
    <div class="search-card">
        <div class="search-box">
            <div class="search-input-group">
                <label class="search-label">ค้นหา PO / ร้านค้า</label>
                <input type="text" id="searchInput" class="search-input" placeholder="พิมพ์แล้วกด 🔍 หรือ Enter เพื่อค้นหา..." maxlength="40" />
            </div>
            <div class="search-buttons">
                <button class="btn btn-primary" onclick="doSearch()">🔍 ค้นหา</button>
                <button class="btn btn-ghost" onclick="resetAll()">↺ รีเซ็ต</button>
            </div>
        </div>
    </div>
</div>

<div class="main-container">
    <div class="content-card" style="margin-bottom: 20px;">
        <div class="list-toolbar">
            <div class="lt-left">
                <span class="lt-title">รายการ PO</span>
                <span class="count-pill" id="listCount">0 รายการ</span>
                <span class="enrich-progress" id="enrichProgress">
                    <span id="enrichText">⏳ โหลดสถานะ 0/0</span>
                    <span class="enrich-bar"><i id="enrichBar"></i></span>
                </span>
            </div>
            <button class="btn-sm" onclick="loadList(1, {refetch:true})">🔄 โหลดใหม่</button>
        </div>
        <div id="listArea">
            <div class="list-state"><div class="ls-icon">⏳</div><div class="ls-text">กำลังโหลดรายการ...</div></div>
        </div>
        <div id="pagerArea"></div>
    </div>
</div>

<div class="modal-overlay" id="detailModal">
    <div class="modal-content">
        <div class="modal-head">
            <div class="dh-title">รายละเอียด PO: <span class="mono" id="detail_ponum">-</span></div>
            <button class="modal-close" onclick="closeDetail()" title="ปิด (ESC)">✕</button>
        </div>
        <div class="modal-body">
            <div class="section">
                <div class="section-title">สถานะการสั่งซื้อ</div>
                <div class="progress-timeline">
                    <div class="timeline-track">
                        <div class="timeline-line"><div class="timeline-line-fill" id="progress_fill" style="width: 0%"></div></div>
                        @php
                            $timelineSteps = [
                                ['icon' => '📝', 'label' => 'สร้าง PO'],
                                ['icon' => '✅', 'label' => 'ยืนยันคำสั่งซื้อ'],
                                ['icon' => '📦', 'label' => 'ได้รับ Invoice'],
                                ['icon' => '🚚', 'label' => 'วันที่คาดว่าจะได้รับสินค้า'],
                                ['icon' => '⭐', 'label' => 'รับสินค้าครบ'],
                            ];
                        @endphp
                        @foreach ($timelineSteps as $i => $step)
                            <div class="timeline-point" data-step="{{ $i + 1 }}">
                                <div class="timeline-circle"><span class="timeline-icon">{{ $step['icon'] }}</span></div>
                                <div>
                                    <div class="timeline-label">{{ $step['label'] }}</div>
                                    <div class="timeline-date" id="date_step{{ $i + 1 }}">-</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="notice-box" id="noticeBox"><div class="notice-text">วันที่คาดว่าจะได้รับสินค้าครบทั้งหมด: <strong id="expected_date">-</strong></div></div>
                <div class="notice-box hidden" id="noteBox" style="background: #FF7D63; margin-top: 16px;"><div class="notice-text" style="color: #000;"><strong style="color: #000;">หมายเหตุ:</strong> <span id="note_text">-</span></div></div>
            </div>
            <div class="section">
                <div class="section-title">ข้อมูลผู้ขาย</div>
                <div class="info-grid">
                    <div class="info-item"><div class="info-label">รหัสร้านค้า</div><div class="info-value" id="vendor_code">-</div></div>
                    <div class="info-item"><div class="info-label">ชื่อร้านค้า</div><div class="info-value" id="vendor_name">-</div></div>
                    <div class="info-item"><div class="info-label">ที่อยู่</div><div class="info-value" id="vendor_address">-</div></div>
                </div>
            </div>
            <div class="section" style="padding: 0;">
                <div style="padding: 24px 24px 16px 24px;"><div class="section-title" style="margin-bottom: 0;">รายการสินค้า</div></div>
                <table class="items-table">
                    <thead><tr><th>สินค้า</th><th style="width: 120px; text-align: center;">จำนวนสั่ง</th><th style="width: 500px;">รายการ Invoice</th><th style="width: 160px; text-align: center;">สถานะ</th></tr></thead>
                    <tbody id="items_table_body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const PER_PAGE = 30;

    // ✅ ส่งเป็น array: โหลดเฉพาะ 30 ใบของหน้านี้ เปลี่ยนหน้าค่อยโหลดใหม่
    //    แบ่งยิงทีละ 10 ใบ → การ์ดทยอยขึ้นเรื่อย ๆ ไม่ต้องรอครบ 30 (รวม 3 request/หน้า)
    const BATCH_URL  = '/pooutside/search-batch';
    const BATCH_SIZE = 10;
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    let serverPaged = false;
    let allOriginal = [];
    let allFiltered = [];
    let pagePOs = [];
    let meta = null;
    let activePonum = null;
    let currentPage = 1;
    let clientPage = 1;
    let currentSearch = '';
    const detailCache = {};
    let enrichToken = 0;      // กันผลของหน้าเก่ามาทับหน้าใหม่
    let batchSupported = true; // ถ้า endpoint ยังไม่มี → fallback ยิงทีละใบ

    const PONUM_KEYS = ['^ponum$','po_no','^po$','ponumber','docuno','doc_no'];
    const VNAME_KEYS = ['vendor_name','vendorname','^vendor$','supplier_name','supplier','^name$'];
    const VCODE_KEYS = ['vendor_code','vendorcode','supplier_code','^code$'];
    const PLACE = new Set(['', '-', 'null', 'undefined', 'N/A', 'na']);
    function isPlace(v){ return v===null || v===undefined || PLACE.has(String(v).trim()); }
    function rawKeys(o){ return Object.keys(o).filter(k => !k.startsWith('_m_')); }
    function pickRaw(o, pats){ if(!o||typeof o!=='object') return undefined; const keys=rawKeys(o); for(const p of pats){ const re=new RegExp(p,'i'); const k=keys.find(k=>re.test(k)); if(k!==undefined && !isPlace(o[k])) return o[k]; } return undefined; }
    function getField(po, mKey, pats){ const v = po[mKey]; return !isPlace(v) ? v : pickRaw(po, pats); }
    function esc(s){ return String(s==null?'':s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
    function attrSafe(s){ return String(s==null?'':s).replace(/\\/g,'\\\\').replace(/"/g,'\\"'); }
    function chunk(arr, size){ const out = []; for (let i = 0; i < arr.length; i += size) out.push(arr.slice(i, i + size)); return out; }

    // ✅ จำนวน → ทศนิยม 2 ตำแหน่ง + คั่นหลักพัน
    function formatQty(v){
        const n = parseFloat(v);
        if (isNaN(n)) return (v == null ? '0' : String(v));
        return n.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    // ✅ ข้อความ "เกิน/ขาด X หน่วย" → เปลี่ยนเลขในข้อความเป็น 2 ตำแหน่ง
    function formatQtyMessage(msg){
        return String(msg).replace(/(\d[\d,]*\.\d+)/g, m => formatQty(m.replace(/,/g, '')));
    }

    function formatDateThai(v){
        if(isPlace(v)) return '—';
        const months=['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
        const str=String(v).trim(); let d=null;
        const m=str.match(/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{2,4})$/);
        if(m){ let day=+m[1],mon=+m[2],yy=+m[3]; if(yy<100)yy+=2000; if(yy>2400)yy-=543; d=new Date(yy,mon-1,day); }
        else if(/^\d{10,13}$/.test(str)){ d=new Date(str.length===10?(+str*1000):+str); }
        else { d=new Date(str); }
        if(!d||isNaN(d.getTime())) return str;
        return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    function hasMismatch(data){ return Array.isArray(data && data.items) && data.items.some(it => it.is_low_score); }

    // ✅ ป้ายสถานะบนการ์ด มีแค่ 3 แบบ
    const ST_CANCEL = { label: 'ยกเลิก',         cls: 'status-no-data'  };
    const ST_DOING  = { label: 'กำลังดำเนินการ', cls: 'status-pending'  };
    const ST_DONE   = { label: 'เสร็จสิ้น',       cls: 'status-complete' };

    function cardStatus(data){
        const tl   = (data && data.timeline) || {};
        const step = Number(tl.step) || 0;
        const st   = String(tl.status || '').toUpperCase();

        if (st === 'CANCELLED') return ST_CANCEL;   // ขั้น "ยืนยันคำสั่งซื้อ" ที่ถูกยกเลิก
        if (step >= 5)          return ST_DONE;     // ขั้น "รับสินค้าครบ"
        return ST_DOING;                            // ที่เหลือทั้งหมด
    }

    function cardInfo(po){
        const ponum = getField(po,'_m_ponum',PONUM_KEYS) ?? '';
        let vName = getField(po,'_m_vendor_name',VNAME_KEYS);
        let vCode = getField(po,'_m_vendor_code',VCODE_KEYS);
        const c = detailCache[ponum];
        if (c) {
            const tl = c.timeline || {};
            if (c.vendor) { if (isPlace(vName)) vName = c.vendor.name; if (isPlace(vCode)) vCode = c.vendor.code; }
            return { ponum, vName: isPlace(vName)?'—':vName, vCode: isPlace(vCode)?'':vCode, date: formatDateThai(tl.date_created), st: cardStatus(c), loaded: true };
        }
        return { ponum, vName: isPlace(vName)?'—':vName, vCode: isPlace(vCode)?'':vCode, date: null, st: null, loaded: false };
    }

    function parseTs(po){
        const v = po._m_date || po.date_created || po.created_at || po.DocuDate || po.docu_date || po.order_date || po.po_date || po.updated_at;
        if (v == null || v === '') return null;
        const t = Date.parse(String(v));
        return isNaN(t) ? null : t;
    }
    function sortDesc(arr){
        arr.sort((a,b) => {
            const ta = parseTs(a), tb = parseTs(b);
            if (ta && tb && ta !== tb) return tb - ta;
            const pa = String(getField(a,'_m_ponum',PONUM_KEYS) ?? '');
            const pb = String(getField(b,'_m_ponum',PONUM_KEYS) ?? '');
            return pb.localeCompare(pa, undefined, { numeric: true });
        });
    }

    function applyClientSearch(){
        const q = currentSearch.toLowerCase();
        allFiltered = q
            ? allOriginal.filter(po => Object.values(po).filter(v => v != null).join(' ').toLowerCase().includes(q))
            : allOriginal.slice();
    }
    function sliceClient(){
        const total = allFiltered.length;
        const last = Math.max(1, Math.ceil(total / PER_PAGE));
        clientPage = Math.min(Math.max(1, clientPage), last);
        pagePOs = allFiltered.slice((clientPage - 1) * PER_PAGE, clientPage * PER_PAGE);
        const from = total ? ((clientPage - 1) * PER_PAGE + 1) : 0;
        const to = Math.min(clientPage * PER_PAGE, total);
        meta = { current_page: clientPage, last_page: last, per_page: PER_PAGE, total: total, from: from, to: to };
    }
    function showClientPage(page){
        clientPage = page;
        sliceClient();
        renderList(pagePOs); renderPager(meta); enrichPage();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    async function loadList(page, opts){
        page = page || 1; opts = opts || {};
        const area = document.getElementById('listArea');
        const pager = document.getElementById('pagerArea');

        if (!serverPaged && allOriginal.length && !opts.refetch) {
            showClientPage(page);
            return;
        }

        area.innerHTML = `<div class="list-state"><div class="ls-icon">⏳</div><div class="ls-text">กำลังโหลดรายการ...</div></div>`;
        pager.innerHTML = '';
        hideProgress();
        try {
            const params = new URLSearchParams({ page: page, search: currentSearch });
            const res = await fetch(`{{ route('pooutside.list') }}?` + params.toString());
            const data = await res.json();
            if (!data.success) {
                area.innerHTML = `<div class="list-state"><div class="ls-icon">⚠️</div><div class="ls-text">${esc(data.message || 'โหลดไม่สำเร็จ')}</div></div>`;
                pagePOs = []; meta = null; updateCount(0); return;
            }

            // ✅ ปกติจะเข้าทางนี้เสมอ: server ส่ง meta มา = โหลดทีละหน้า หน้าละ 30
            if (data.meta && data.meta.last_page) {
                serverPaged = true;
                pagePOs = Array.isArray(data.data) ? data.data : [];
                meta = data.meta;
                currentPage = meta.current_page;
            } else {
                serverPaged = false;
                allOriginal = Array.isArray(data.data) ? data.data : [];
                sortDesc(allOriginal);
                applyClientSearch();
                clientPage = page;
                sliceClient();
            }

            renderList(pagePOs);
            renderPager(meta);
            enrichPage();
        } catch (err) {
            console.error(err);
            area.innerHTML = `<div class="list-state"><div class="ls-icon">⚠️</div><div class="ls-text">โหลดรายการไม่สำเร็จ</div><div class="ls-sub">ตรวจสอบ endpoint /pooutside/list</div></div>`;
            pagePOs = []; meta = null; updateCount(0);
        }
    }

    function doSearch(){
        currentSearch = (document.getElementById('searchInput').value || '').trim();
        if (serverPaged) {
            loadList(1, { refetch: true });
        } else {
            clientPage = 1;
            applyClientSearch();
            sliceClient();
            renderList(pagePOs); renderPager(meta); enrichPage();
        }
    }
    function resetAll(){
        document.getElementById('searchInput').value = '';
        currentSearch = ''; activePonum = null; closeDetail();
        if (serverPaged) { loadList(1, { refetch: true }); }
        else if (allOriginal.length) { clientPage = 1; applyClientSearch(); sliceClient(); renderList(pagePOs); renderPager(meta); enrichPage(); }
        else { loadList(1, { refetch: true }); }
    }

    function renderList(list){
        const area = document.getElementById('listArea');
        updateCount(meta ? meta.total : list.length);
        if (!list.length) {
            area.innerHTML = `<div class="list-state"><div class="ls-icon">📭</div><div class="ls-text">ไม่พบรายการ</div><div class="ls-sub">${currentSearch ? 'ไม่พบผลลัพธ์สำหรับ "' + esc(currentSearch) + '"' : 'ยังไม่มีข้อมูล PO'}</div></div>`;
            return;
        }
        const cards = list.map(po => {
            const info = cardInfo(po);
            const isActive = (String(info.ponum) === String(activePonum)) ? ' active' : '';
            const badge = info.loaded ? `<span class="status-badge ${esc(info.st.cls)}">${esc(info.st.label)}</span>` : `<span class="status-badge is-loading">⏳</span>`;
            const date  = info.loaded ? `<span class="po-date">📅 ${esc(info.date)}</span>` : `<span class="po-date is-loading">⏳ โหลด...</span>`;
            return `<div class="po-card${isActive}" data-ponum="${attrSafe(info.ponum)}" onclick="selectPO('${attrSafe(info.ponum)}')">
                <div class="pc-top"><div class="po-no">${esc(info.ponum)}</div>${badge}</div>
                <div class="po-vendor">${esc(info.vName)}</div>
                ${info.vCode ? `<div class="po-vendor-code">รหัส: ${esc(info.vCode)}</div>` : ''}
                <div class="pc-bottom">${date}<span class="pc-hint">${isActive ? '● กำลังดู' : 'กดดู →'}</span></div>
            </div>`;
        }).join('');
        area.innerHTML = `<div class="po-grid">${cards}</div>`;
    }

    function updateCount(n){ const el = document.getElementById('listCount'); if (el) el.textContent = (n || 0).toLocaleString() + ' รายการ'; }

    function pageItems(cur, last){
        if (last <= 7) return Array.from({ length: last }, (_, i) => ({ t: 'p', v: i + 1 }));
        const items = []; const add = (t, v) => items.push({ t, v });
        add('p', 1);
        let start = Math.max(2, cur - 1), end = Math.min(last - 1, cur + 1);
        if (cur <= 4) { start = 2; end = 5; }
        if (cur >= last - 3) { start = last - 4; end = last - 1; }
        if (start > 2) add('gap');
        for (let i = start; i <= end; i++) add('p', i);
        if (end < last - 1) add('gap');
        add('p', last);
        return items;
    }
    function renderPager(m){
        const pager = document.getElementById('pagerArea');
        if (!m || !m.last_page || m.last_page <= 1) { pager.innerHTML = ''; return; }
        const cur = m.current_page, last = m.last_page;
        let html = `<div class="po-pager">`;
        html += `<button ${cur <= 1 ? 'disabled' : ''} onclick="loadList(${cur - 1})">‹</button>`;
        pageItems(cur, last).forEach(it => {
            if (it.t === 'gap') html += `<span class="gap">…</span>`;
            else html += `<button class="${it.v === cur ? 'active' : ''}" onclick="loadList(${it.v})">${it.v}</button>`;
        });
        html += `<button ${cur >= last ? 'disabled' : ''} onclick="loadList(${cur + 1})">›</button>`;
        html += `<span class="pg-info">หน้า ${cur} / ${last} · แสดง ${m.from}–${m.to} จาก ${(m.total || 0).toLocaleString()}</span>`;
        html += `</div>`;
        pager.innerHTML = html;
    }

    /* ─── โหลดสถานะแบบ batch ──────────────────────────────────────────────── */

    // ส่ง ponum เป็น array ครั้งเดียว → { "PO001": {...}, ... }
    async function fetchBatch(ponums){
        try {
            const res = await fetch(BATCH_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ ponums: ponums })
            });
            if (res.status === 404 || res.status === 405) { batchSupported = false; return null; }
            if (!res.ok) return {};
            const json = await res.json();
            return (json && json.success && json.data) ? json.data : {};
        } catch (e) {
            console.warn('batch error', e);
            return {};
        }
    }

    // สำรอง: ถ้ายังไม่ได้เพิ่ม route batch จะยิงทีละใบเหมือนเดิม
    async function fetchOne(ponum){
        try {
            const res = await fetch(`/pooutside/search?ponum=${encodeURIComponent(ponum)}`);
            if (!res.ok) return null;
            const data = await res.json();
            return (data && data.success) ? data : null;
        } catch (e) { return null; }
    }

    async function enrichPage(){
        const myToken = ++enrichToken;

        const ponums = pagePOs
            .map(po => String(getField(po, '_m_ponum', PONUM_KEYS) ?? ''))
            .filter(p => p !== '');

        const total   = ponums.length;
        const todo    = ponums.filter(p => !detailCache[p]);
        let   done    = total - todo.length;

        if (!todo.length) { hideProgress(); return; }
        showProgress(done, total);

        for (const group of chunk(todo, BATCH_SIZE)) {
            if (myToken !== enrichToken) return; // เปลี่ยนหน้าไปแล้ว → ทิ้งผลนี้

            let map = batchSupported ? await fetchBatch(group) : null;

            // batch ใช้ไม่ได้ → fallback ยิงทีละใบ (ยังทำงานได้แม้ยังไม่เพิ่ม route)
            if (map === null) {
                map = {};
                for (const p of group) {
                    const d = await fetchOne(p);
                    if (d) map[p] = d;
                }
            }

            if (myToken !== enrichToken) return;

            group.forEach(p => {
                const d = map[p] || null;
                if (d) detailCache[p] = d;
                patchCard(p, d);
                done++;
            });
            showProgress(done, total);
        }

        hideProgress();
    }

    function showProgress(done, total){ const w = document.getElementById('enrichProgress'); w.classList.add('show'); document.getElementById('enrichText').textContent = `⏳ โหลดสถานะ ${done}/${total}`; document.getElementById('enrichBar').style.width = (total ? (done / total * 100) : 0) + '%'; }
    function hideProgress(){ document.getElementById('enrichProgress').classList.remove('show'); }

    function patchCard(ponum, data){
        const card = document.querySelector(`.po-card[data-ponum="${attrSafe(ponum)}"]`);
        if (!card) return;
        if (!data) {
            const badge = card.querySelector('.status-badge'); if (badge) { badge.className = 'status-badge status-no-data'; badge.textContent = '—'; }
            const dateEl = card.querySelector('.po-date'); if (dateEl) { dateEl.className = 'po-date'; dateEl.textContent = '📅 —'; }
            return;
        }
        const st = cardStatus(data);
        const date = formatDateThai((data.timeline || {}).date_created);
        const badge = card.querySelector('.status-badge'); if (badge) { badge.className = 'status-badge ' + st.cls; badge.textContent = st.label; }
        const dateEl = card.querySelector('.po-date'); if (dateEl) { dateEl.className = 'po-date'; dateEl.textContent = '📅 ' + date; }
        if (data.vendor) { const v = card.querySelector('.po-vendor'); if (v && (v.textContent === '—' || v.textContent === '-' || !v.textContent.trim())) v.textContent = data.vendor.name || '—'; }
    }

    function openModal(){ const m = document.getElementById('detailModal'); m.classList.add('show'); document.body.style.overflow = 'hidden'; const b = m.querySelector('.modal-body'); if (b) b.scrollTop = 0; }
    function closeDetail(){ const m = document.getElementById('detailModal'); m.classList.remove('show'); document.body.style.overflow = ''; activePonum = null; document.querySelectorAll('.po-card.active').forEach(c => { c.classList.remove('active'); const h = c.querySelector('.pc-hint'); if (h) h.textContent = 'กดดู →'; }); }
    document.getElementById('detailModal').addEventListener('click', function (e) { if (e.target === this) closeDetail(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && document.getElementById('detailModal').classList.contains('show')) closeDetail(); });

    async function selectPO(ponum){
        if (isPlace(ponum)) return;
        activePonum = ponum;
        document.querySelectorAll('.po-card').forEach(c => { const on = c.dataset.ponum === String(ponum); c.classList.toggle('active', on); const h = c.querySelector('.pc-hint'); if (h) h.textContent = on ? '● กำลังดู' : 'กดดู →'; });
        openModal();
        document.getElementById('detail_ponum').textContent = ponum;
        ['vendor_code', 'vendor_name', 'vendor_address'].forEach(id => document.getElementById(id).textContent = '-');
        document.getElementById('noteBox').classList.add('hidden');
        const render = (data) => {
            document.getElementById('vendor_code').textContent = data.vendor.code;
            document.getElementById('vendor_name').textContent = data.vendor.name;
            document.getElementById('vendor_address').textContent = data.vendor.address;
            renderTimeline(data.timeline);
            const nb = document.getElementById('noteBox');
            if (data.notes) { document.getElementById('note_text').textContent = data.notes; nb.classList.remove('hidden'); } else { nb.classList.add('hidden'); }
            renderItems(data.items);
        };
        // ✅ ส่วนใหญ่จะมีใน cache จาก batch แล้ว → เปิดทันทีไม่ต้องยิงซ้ำ
        if (detailCache[ponum]) { render(detailCache[ponum]); patchCard(ponum, detailCache[ponum]); return; }
        document.getElementById('items_table_body').innerHTML = `<tr><td colspan="4" style="text-align:center;color:#6b7280;padding:40px;">กำลังโหลดรายละเอียด...</td></tr>`;
        try {
            const res = await fetch(`/pooutside/search?ponum=${encodeURIComponent(ponum)}`);
            if (!res.ok) { document.getElementById('items_table_body').innerHTML = `<tr><td colspan="4" style="text-align:center;color:#991b1b;padding:40px;">ไม่พบรายละเอียด (HTTP ${res.status})</td></tr>`; return; }
            const data = await res.json();
            if (!data.success) { document.getElementById('items_table_body').innerHTML = `<tr><td colspan="4" style="text-align:center;color:#991b1b;padding:40px;">${esc(data.message || 'ไม่พบรายละเอียด')}</td></tr>`; return; }
            detailCache[ponum] = data; patchCard(ponum, data); render(data);
        } catch (err) { console.error(err); document.getElementById('items_table_body').innerHTML = `<tr><td colspan="4" style="text-align:center;color:#991b1b;padding:40px;">โหลดรายละเอียดไม่สำเร็จ</td></tr>`; }
    }

    function renderTimeline(timeline){
        const totalSteps = 5;
        document.getElementById('progress_fill').style.width = ((timeline.step - 1) / (totalSteps - 1)) * 100 + '%';
        for (let i = 1; i <= totalSteps; i++) { const el = document.getElementById(`date_step${i}`); if (el) el.textContent = '-'; }
        document.querySelectorAll('.timeline-point').forEach((point, i) => { const stepNum = i + 1, circle = point.querySelector('.timeline-circle'); point.classList.remove('completed', 'current'); circle.classList.remove('completed', 'current', 'bg-yellow', 'bg-green', 'bg-red'); if (stepNum < timeline.step) { point.classList.add('completed'); circle.classList.add('completed'); } else if (stepNum === timeline.step) { point.classList.add('current'); circle.classList.add('current'); } });
        [timeline.date_created, timeline.date_created, timeline.date_invoice, timeline.date_expected, null].forEach((d, i) => { const el = document.getElementById(`date_step${i + 1}`); if (el && d) el.textContent = formatDateThai(d); });
        const colorMap = { ENTRY: 'bg-green', PARTIAL: 'bg-yellow', COMPLETED: 'bg-green', CANCELLED: 'bg-red' }; const color = colorMap[timeline.status];
        if (color) { const el = document.querySelector(`.timeline-point[data-step="${timeline.step}"] .timeline-circle`); if (el) el.classList.add(color); }
        document.getElementById('noticeBox').classList.toggle('hidden', !timeline.show_expected_box);
        document.getElementById('expected_date').textContent = formatDateThai(timeline.date_expected) || '-';
    }
    function renderInvoiceTags(item){
        if (item.is_low_score && item.invoices.length > 0) { return `<div class="mismatch-label" style="grid-column:1/-1;">ชื่อสินค้าไม่ถูกต้อง ไม่สามารถจับคู่ได้ *ติดต่อผู้ดูแลระบบ</div>` + item.invoices.map(inv => `<div class="invoice-tag mismatch"><span class="inv-num">Invoice: ${esc(inv.invoice)}</span><span class="inv-num">Name: ${esc(inv.name)}</span><span class="inv-date">วันที่: ${formatDateThai(inv.date_invoice)}</span><span class="inv-qty">จำนวน: ${formatQty(inv.quantity)}</span></div>`).join(''); }
        if (item.invoices.length > 0) { const tags = item.invoices.map(inv => `<div class="invoice-tag"><span class="inv-num">Invoice: ${esc(inv.invoice)}</span><span class="inv-num">Name: ${esc(inv.name)}</span><span class="inv-date">วันที่: ${formatDateThai(inv.date_invoice)}</span><span class="inv-qty">จำนวน: ${formatQty(inv.quantity)}</span></div>`).join(''); const summary = item.qty_summary ? `<div class="qty-summary ${esc(item.qty_summary.type)}">${formatQtyMessage(item.qty_summary.message)}</div>` : ''; return tags + summary; }
        return '<span style="color:#9ca3af;">ยังไม่มีข้อมูล Invoice</span>';
    }
    function renderItems(items){
        const tbody = document.getElementById('items_table_body');
        if (!items.length) { tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;color:red;">ไม่พบข้อมูล</td></tr>`; return; }
        tbody.innerHTML = items.map(item => `<tr><td><div class="item-name">${esc(item.name)}</div></td><td style="text-align:center"><div class="qty-box">${formatQty(item.qty_ordered)}</div></td><td><div class="item-invoices">${renderInvoiceTags(item)}</div></td><td style="text-align:center"><span class="status-badge ${esc(item.status_class)}">${esc(item.status)}</span></td></tr>`).join('');
    }

    document.getElementById('searchInput').addEventListener('keypress', function (e) { if (e.key === 'Enter') doSearch(); });
    window.addEventListener('DOMContentLoaded', function () { loadList(1); });
</script>
</body>
</html>