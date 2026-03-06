<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Return System</title>
    <link href="https://fonts.googleapis.com/css2?family=SamsungSharpSans:wght@400;700&family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --samsung-blue: #1428A0;
            --samsung-blue-hover: #0f1f80;
            --samsung-light-blue: #e8ecf8;
            --bg: #ffffff;
            --bg-gray: #f4f4f4;
            --bg-card: #ffffff;
            --border: #e0e0e0;
            --text-primary: #1d1d1f;
            --text-secondary: #535353;
            --text-muted: #999;
            --status-pending-bg: #fff4e0;
            --status-pending-text: #cc7a00;
            --status-pending-border: #ffd580;
            --status-closed-bg: #e6f7f0;
            --status-closed-text: #0a7a4b;
            --status-closed-border: #a3e0c7;
        }

        body {
            font-family: 'Noto Sans Thai', 'SamsungSharpSans', Arial, sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            font-size: 14px;
            padding-top: 16px;
        }

        nav {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 16px;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            margin: 0 24px;
        }

        .nav-brand { display: flex; align-items: center; gap: 16px; }
        .nav-subtitle { font-size: 16px; font-weight: 700; color: var(--text-primary); }
        .nav-badge {
            font-size: 11px; color: var(--text-muted);
            background: var(--bg-gray); border: 1px solid var(--border);
            padding: 3px 10px; border-radius: 20px;
        }
        .nav-badge-admin {
            font-size: 11px; color: #fff;
            background: var(--samsung-blue); border: 1px solid var(--samsung-blue);
            padding: 3px 10px; border-radius: 20px; font-weight: 700;
        }
        .nav-right { display: flex; align-items: center; gap: 12px; }

        main { padding: 32px 40px; max-width: 1280px; margin: 0 auto; }

        .stats-grid {
            display: grid; grid-template-columns: repeat(5, 1fr);
            gap: 16px; margin-bottom: 32px;
        }

        .stat-card {
            background: #fff; border: 1px solid var(--border);
            border-radius: 12px; padding: 22px 24px;
            cursor: default; transition: box-shadow 0.2s, transform 0.2s;
            position: relative; overflow: hidden;
        }
        .stat-card::after {
            content: ''; position: absolute;
            bottom: 0; left: 0; right: 0; height: 3px;
        }
        .stat-card.total::after    { background: var(--samsung-blue); }
        .stat-card.pending::after  { background: #f5a623; }
        .stat-card.processing::after { background: #7b61ff; }
        .stat-card.closed::after   { background: #0a7a4b; }
        .stat-card.rejected::after { background: #e53935; }
        .stat-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.10); transform: translateY(-2px); }

        .stat-number {
            font-size: 40px; font-weight: 700;
            line-height: 1; margin-bottom: 4px; letter-spacing: -1px;
        }
        .stat-card.total .stat-number      { color: var(--samsung-blue); }
        .stat-card.pending .stat-number    { color: #f5a623; }
        .stat-card.processing .stat-number { color: #7b61ff; }
        .stat-card.closed .stat-number     { color: #0a7a4b; }
        .stat-card.rejected .stat-number   { color: #e53935; }
        .stat-label { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        .stat-icon { position: absolute; top: 16px; right: 16px; opacity: 0.15; width: 36px; height: 36px; }
        .stat-icon svg { width: 36px; height: 36px; }

        .table-section {
            background: #fff; border: 1px solid var(--border);
            border-radius: 12px; overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }
        .table-header {
            padding: 20px 24px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            background: #fafafa;
        }
        .table-title { font-size: 15px; font-weight: 700; color: var(--text-primary); }

        .search-wrapper { position: relative; width: 280px; }
        .search-icon {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%); color: var(--text-muted);
            pointer-events: none; display: flex;
        }
        .search-input {
            width: 100%; background: var(--bg-gray);
            border: 1px solid var(--border); border-radius: 24px;
            padding: 8px 16px 8px 38px;
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 13px; color: var(--text-primary);
            outline: none; transition: all 0.2s;
        }
        .search-input::placeholder { color: var(--text-muted); }
        .search-input:focus {
            background: #fff; border-color: var(--samsung-blue);
            box-shadow: 0 0 0 3px rgba(20,40,160,0.08);
        }

        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        thead th {
            padding: 13px 16px; text-align: left;
            font-size: 11px; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.08em;
            background: #fafafa; border-bottom: 1px solid var(--border);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        thead th:nth-child(1) { width: 11%; }  /* เลข PO */
        thead th:nth-child(2) { width: 15%; }  /* ลูกค้า */
        thead th:nth-child(3) { width: 26%; }  /* สินค้า */
        thead th:nth-child(4) { width: 13%; }  /* เหตุผล */
        thead th:nth-child(5) { width: 13%; }  /* ขั้นตอน */
        thead th:nth-child(6) { width: 13%; }  /* สถานะ */
        thead th:nth-child(7) { width: 9%; }   /* วันที่ */

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.12s; cursor: pointer;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f5f7ff; }
        tbody td {
            padding: 14px 16px; font-size: 13px;
            color: var(--text-primary); vertical-align: middle;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        tbody td:nth-child(3) { white-space: normal; word-break: break-word; }

        @media (max-width: 1024px) {
            main { padding: 20px 16px; }
            .stats-grid { grid-template-columns: repeat(3, 1fr); }
            nav { margin: 0 12px; }
        }
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .table-section { overflow-x: auto; }
            table { min-width: 700px; }
            thead th:nth-child(3), thead th:nth-child(4) { display: none; }
            tbody td:nth-child(3), tbody td:nth-child(4) { display: none; }
        }

        .claim-id { color: var(--samsung-blue); font-weight: 700; font-size: 13px; font-family: 'Courier New', monospace; }
        .customer-name { font-weight: 600; }
        .product-list { display: flex; flex-direction: column; gap: 4px; }
        .product-item {
            display: flex; align-items: baseline; gap: 6px;
            font-size: 12px; color: var(--text-secondary);
            white-space: normal; word-break: break-word; line-height: 1.5;
            padding: 4px 8px; background: #f8f9ff;
            border-left: 2px solid var(--samsung-blue);
            border-radius: 0 4px 4px 0;
        }
        .product-item .prod-qty {
            flex-shrink: 0; font-size: 11px; font-weight: 700;
            color: var(--samsung-blue); background: #e8ecf8;
            padding: 1px 6px; border-radius: 10px;
        }
        .reason-text { color: var(--text-secondary); font-size: 12px; }

        .empty-state { padding: 60px 24px; text-align: center; }
        .empty-icon {
            width: 56px; height: 56px; background: var(--bg-gray);
            border-radius: 16px; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 16px;
        }
        .empty-title { font-size: 15px; font-weight: 700; color: var(--text-secondary); margin-bottom: 6px; }
        .empty-sub { font-size: 13px; color: var(--text-muted); }

        .progress-wrapper { display: flex; align-items: center; gap: 10px; }
        .progress-bar-bg { width: 80px; height: 5px; background: #e5e5e5; border-radius: 3px; overflow: hidden; }
        .progress-bar-fill { height: 100%; border-radius: 3px; transition: width 0.6s cubic-bezier(0.22,1,0.36,1); }
        .progress-bar-fill.orange { background: #f5a623; }
        .progress-bar-fill.blue   { background: var(--samsung-blue); }
        .progress-text { font-size: 12px; color: var(--text-muted); font-weight: 600; font-family: 'Courier New', monospace; }

        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700; border: 1px solid;
        }
        .badge::before {
            content: ''; width: 5px; height: 5px;
            border-radius: 50%; flex-shrink: 0;
        }
        .badge-pending  { background: var(--status-pending-bg); color: var(--status-pending-text); border-color: var(--status-pending-border); }
        .badge-pending::before { background: var(--status-pending-text); }
        .badge-closed   { background: var(--status-closed-bg); color: var(--status-closed-text); border-color: var(--status-closed-border); }
        .badge-closed::before { background: var(--status-closed-text); }

        .date-text { font-size: 12px; color: var(--text-muted); font-family: 'Courier New', monospace; }

        .table-footer {
            padding: 14px 24px; border-top: 1px solid var(--border);
            background: #fafafa; font-size: 12px; color: var(--text-muted);
            display: flex; align-items: center; justify-content: space-between;
        }

        /* INVOICE CARDS */
        .invoice-cards-wrapper { display: flex; flex-wrap: wrap; gap: 3px; justify-content: center; text-align: left; }
        .invoice-card {
            display: inline-flex; flex-direction: column; gap: 1px;
            background: #f0f4ff; border: 1px solid #c8d4f8;
            border-radius: 6px; padding: 4px 8px;
            font-size: 10px; min-width: 0; max-width: none;
            vertical-align: top; white-space: nowrap;
        }
        .invoice-card-num  { font-weight: 700; color: var(--samsung-blue); font-size: 10px; }

        /* DETAIL MODAL */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); backdrop-filter: blur(3px);
            z-index: 200; align-items: flex-start; justify-content: center;
            overflow-y: auto; padding: 24px 0;
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background: #fff; border-radius: 16px; padding: 32px;
            width: 920px; max-width: 95vw;
            animation: slideUp 0.25s cubic-bezier(0.22,1,0.36,1);
            box-shadow: 0 20px 60px rgba(0,0,0,0.18); margin: auto;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .detail-modal { width: 920px; max-width: 95vw; padding: 0; overflow: hidden; border-radius: 16px; }
        .detail-header { padding: 24px 28px 20px; border-bottom: 1px solid var(--border); background: #fafafa; }
        .detail-title-row {
            display: flex; align-items: flex-start;
            justify-content: space-between; gap: 12px;
        }
        .detail-claim-id { font-size: 22px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.5px; margin-bottom: 4px; }
        .detail-meta { font-size: 12px; color: var(--text-muted); }
        .detail-header-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .detail-close {
            background: #fff; border: 1px solid var(--border); border-radius: 8px;
            width: 32px; height: 32px; display: flex; align-items: center;
            justify-content: center; cursor: pointer; color: var(--text-muted); transition: all 0.15s;
        }
        .detail-close:hover { border-color: #999; color: var(--text-primary); }

        .steps-bar { display: flex; align-items: center; padding: 20px 28px; border-bottom: 1px solid var(--border); gap: 0; }
        .step-item { display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; }
        .step-item:not(:last-child)::after {
            content: ''; position: absolute; top: 16px; left: 60%;
            width: 80%; height: 2px; background: #e0e0e0; z-index: 0;
        }
        .step-item.done:not(:last-child)::after { background: var(--samsung-blue); }
        .step-circle {
            width: 32px; height: 32px; border-radius: 50%; border: 2px solid #e0e0e0;
            background: #fff; display: flex; align-items: center; justify-content: center;
            z-index: 1; position: relative; transition: all 0.2s;
        }
        .step-item.done .step-circle   { background: var(--samsung-blue); border-color: var(--samsung-blue); }
        .step-item.active .step-circle { background: #fff; border-color: var(--samsung-blue); box-shadow: 0 0 0 3px rgba(20,40,160,0.12); }
        .step-label { font-size: 11px; color: var(--text-muted); margin-top: 6px; font-weight: 500; text-align: center; }
        .step-item.done .step-label, .step-item.active .step-label { color: var(--samsung-blue); font-weight: 700; }

        .detail-body { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
        .detail-info-card { padding: 20px 24px; border-right: 1px solid var(--border); }
        .detail-info-card:last-child { border-right: none; }
        .detail-section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 14px; }
        .detail-info-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f5f5f5; gap: 12px; }
        .detail-info-row:last-child { border-bottom: none; }
        .detail-info-label { font-size: 12px; color: var(--text-muted); flex-shrink: 0; }
        .detail-info-val   { font-size: 13px; font-weight: 600; color: var(--text-primary); text-align: right; }

        .timeline { display: flex; flex-direction: column; gap: 14px; }
        .timeline-item { display: flex; gap: 12px; align-items: flex-start; }
        .timeline-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--samsung-blue); margin-top: 4px; flex-shrink: 0; }
        .timeline-text { font-size: 13px; font-weight: 500; color: var(--text-primary); line-height: 1.4; }
        .timeline-by   { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

        .detail-actions {
            padding: 16px 28px; border-top: 1px solid var(--border);
            display: flex; justify-content: flex-end; gap: 10px; background: #fafafa;
        }
        .btn-cancel {
            background: #fff; border: 1px solid var(--border);
            color: var(--text-secondary); padding: 9px 22px; border-radius: 24px;
            font-family: 'Noto Sans Thai', sans-serif; font-size: 13px;
            font-weight: 600; cursor: pointer; transition: all 0.15s;
        }
        .btn-cancel:hover { border-color: #999; color: #333; }
        .btn-reject {
            background: #fff; border: 1.5px solid #e53935; color: #e53935;
            padding: 9px 20px; border-radius: 24px;
            font-family: 'Noto Sans Thai', sans-serif; font-size: 13px; font-weight: 700;
            cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.15s;
        }
        .btn-reject:hover { background: #fff0f0; }
        .btn-approve {
            background: #0a7a4b; border: none; color: #fff;
            padding: 9px 20px; border-radius: 24px;
            font-family: 'Noto Sans Thai', sans-serif; font-size: 13px; font-weight: 700;
            cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.15s;
        }
        .btn-approve:hover { background: #085f3a; box-shadow: 0 4px 12px rgba(10,122,75,0.3); }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .stat-card { animation: fadeInUp 0.45s ease both; }
        .stat-card:nth-child(1) { animation-delay: 0.05s; }
        .stat-card:nth-child(2) { animation-delay: 0.10s; }
        .stat-card:nth-child(3) { animation-delay: 0.15s; }
        .stat-card:nth-child(4) { animation-delay: 0.20s; }
        .stat-card:nth-child(5) { animation-delay: 0.25s; }
        .table-section { animation: fadeInUp 0.45s ease 0.30s both; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <div class="nav-brand">
        <span class="nav-subtitle">Claim & Return System</span>
        <span class="nav-badge">ระบบจัดการเคลม/คืนสินค้า</span>
        <span class="nav-badge-admin">👤 Admin</span>
    </div>
    <div class="nav-right">
        {{-- ไม่มีปุ่มสร้างเคสใหม่ในหน้า Admin --}}
    </div>
</nav>

<main>
    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#1428A0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
            <div class="stat-number" id="stat-total">0</div>
            <div class="stat-label">ทั้งหมด</div>
        </div>
        <div class="stat-card pending">
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#f5a623" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
            <div class="stat-number" id="stat-pending">0</div>
            <div class="stat-label">รอดำเนินการ</div>
        </div>
        <div class="stat-card processing">
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#7b61ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg></div>
            <div class="stat-number" id="stat-processing">0</div>
            <div class="stat-label">กำลังดำเนินการ</div>
        </div>
        <div class="stat-card closed">
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#0a7a4b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
            <div class="stat-number" id="stat-closed">0</div>
            <div class="stat-label">ปิดแล้ว</div>
        </div>
        <div class="stat-card rejected">
            <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#e53935" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></div>
            <div class="stat-number" id="stat-rejected">0</div>
            <div class="stat-label">ปฏิเสธ</div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-section">
        <div class="table-header">
            <span class="table-title">รายการเคสทั้งหมด</span>
            <div class="search-wrapper">
                <span class="search-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input class="search-input" type="text" placeholder="ค้นหารหัสเคส, ชื่อลูกค้า, สินค้า..." oninput="searchTable(this.value)">
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>เลข PO</th>
                    <th>ร้านค้า</th>
                    <th>สินค้า</th>
                    <th>เหตุผล</th>
                    <th>ขั้นตอน</th>
                    <th>สถานะ</th>
                    <th>วันที่</th>
                </tr>
            </thead>
            <tbody id="cases-tbody">
                <tr id="empty-row">
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                            <div class="empty-title">ยังไม่มีรายการเคส</div>
                            <div class="empty-sub">ยังไม่มีเคสในระบบ</div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="table-footer">
            <span id="footer-count">แสดง 0 รายการ จากทั้งหมด 0 รายการ</span>
            <span id="footer-updated">—</span>
        </div>
    </div>
</main>

<!-- DETAIL MODAL -->
<div class="modal-overlay" id="detail-modal" onclick="closeDetailOnOverlay(event)">
    <div class="modal detail-modal">
        <div class="detail-header">
            <div class="detail-title-row">
                <div>
                    <div class="detail-claim-id" id="d-id"></div>
                    <div class="detail-meta" id="d-meta"></div>
                </div>
                <div class="detail-header-right">
                    <span id="d-badge" class="badge"></span>
                    <button class="detail-close" onclick="closeDetail()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="steps-bar" id="d-steps"></div>
        <div class="detail-body">
            <div class="detail-info-card">
                <div class="detail-section-title">ข้อมูลเคส</div>
                <div class="detail-info-row"><span class="detail-info-label">ร้านค้า</span><span class="detail-info-val" id="d-customer"></span></div>
                <div class="detail-info-row"><span class="detail-info-label">สินค้า</span><span class="detail-info-val" id="d-product"></span></div>
                <div class="detail-info-row"><span class="detail-info-label">เหตุผล</span><span class="detail-info-val" id="d-reason"></span></div>
                <div class="detail-info-row"><span class="detail-info-label">หมายเหตุ</span><span class="detail-info-val" id="d-note">-</span></div>
            </div>
            <div class="detail-info-card">
                <div class="detail-section-title">ประวัติการดำเนินงาน</div>
                <div class="timeline" id="d-timeline"></div>
            </div>
        </div>
        <div class="detail-actions" id="d-actions"></div>
    </div>
</div>

<script>
    let cases = {};
    let currentDetailId = null;

    const stepLabels = ['รับแจ้ง', 'ตรวจสอบ', 'อนุมัติ', 'ดำเนินการ', 'ปิดเคส'];

    const statusMap = {
        processing: { label: 'กำลังดำเนินการ', cls: 'badge-processing', step: 2 },
        accept:     { label: 'อนุมัติแล้ว',     cls: 'badge-accept',     step: 3 },
        finish:     { label: 'เสร็จสิ้น',        cls: 'badge-closed',     step: 5 },
        cancel:     { label: 'ยกเลิก',           cls: 'badge-rejected',   step: 1 },
    };

    const CSRF = () => document.head.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ─── LOAD FROM DB ────────────────────────────────────────────────────────
    async function loadCasesFromDB() {
        try {
            const res  = await fetch('/return/list');
            const list = await res.json();
            cases = {};
            list.forEach(c => {
                const sm   = statusMap[c.status] ?? statusMap.processing;
                const step = sm.step;
                cases[c.id] = {
                    id:        c.id,
                    po:        c.po,
                    customer:  c.customer,
                    product:   c.product,
                    reason:    c.reason,
                    note:      c.note || '-',
                    date:      c.date,
                    status:    c.status,
                    step,
                    stepDates: buildStepDates(c.date, step),
                    stepBy:    ['ฝ่ายบริการ','ช่างเทคนิค','ผู้จัดการ','ฝ่ายจัดส่ง','ฝ่ายบริการ'],
                    stepDesc:  ['รับแจ้งเรื่องจากลูกค้า','ตรวจสอบสินค้าเรียบร้อย','อนุมัติการดำเนินการ','ดำเนินการจัดส่ง / ซ่อม / เปลี่ยนสินค้า','ปิดเคสเรียบร้อย'],
                };
            });
            renderTable();
        } catch (err) {
            console.error('โหลดข้อมูลไม่สำเร็จ:', err);
            renderTable();
        }
    }

    function buildStepDates(date, step) {
        const d = date ?? new Date().toISOString().slice(0, 10);
        const arr = [null, null, null, null, null];
        for (let i = 0; i < step && i < 5; i++) arr[i] = d;
        return arr;
    }

    // ─── RENDER TABLE ─────────────────────────────────────────────────────────
    function renderTable() {
        const tbody = document.getElementById('cases-tbody');
        const all   = Object.values(cases);
        if (!all.length) {
            tbody.innerHTML = `<tr id="empty-row"><td colspan="7">
                <div class="empty-state">
                    <div class="empty-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                    <div class="empty-title">ยังไม่มีรายการเคส</div>
                    <div class="empty-sub">ยังไม่มีเคสในระบบ</div>
                </div></td></tr>`;
        } else {
            tbody.innerHTML = all.map(c => {
                const sm  = statusMap[c.status] ?? statusMap.processing;
                const pct = Math.round((c.step / 5) * 100);
                const barCls = c.status === 'finish' ? 'blue' : 'orange';
                return `
                <tr onclick="openDetail('${c.id}')" style="cursor:pointer;">
                    <td><span class="claim-id">${c.po || c.id}</span></td>
                    <td><span class="customer-name">${c.customer}</span></td>
                    <td><div class="product-list">${c.product.split('\n').map(p => {
                        const m = p.trim().match(/^(.+?)\s*\(จำนวน:\s*([\d.]+)\)$/);
                        return m
                            ? `<div class="product-item"><span>${m[1].trim()}</span><span class="prod-qty">×${parseFloat(m[2])}</span></div>`
                            : `<div class="product-item"><span>${p.trim()}</span></div>`;
                    }).join('')}</div></td>
                    <td><span class="reason-text">${c.reason}</span></td>
                    <td><div class="progress-wrapper">
                        <div class="progress-bar-bg"><div class="progress-bar-fill ${barCls}" style="width:${pct}%"></div></div>
                        <span class="progress-text">${c.step}/5</span>
                    </div></td>
                    <td style="white-space:nowrap;"><span class="badge ${sm.cls}">${sm.label}</span></td>
                    <td><span class="date-text">${c.date}</span></td>
                </tr>`;
            }).join('');
        }
        updateStats();
    }

    function updateStats() {
        const all = Object.values(cases);
        document.getElementById('stat-total').textContent      = all.length;
        document.getElementById('stat-pending').textContent    = all.filter(c => c.status === 'processing').length;
        document.getElementById('stat-processing').textContent = all.filter(c => c.status === 'accept').length;
        document.getElementById('stat-closed').textContent     = all.filter(c => c.status === 'finish').length;
        document.getElementById('stat-rejected').textContent   = all.filter(c => c.status === 'cancel').length;
        document.getElementById('footer-count').textContent    = `แสดง ${all.length} รายการ จากทั้งหมด ${all.length} รายการ`;
        document.getElementById('footer-updated').textContent  = all.length > 0
            ? `อัปเดตล่าสุด: ${new Date().toLocaleDateString('th-TH',{day:'numeric',month:'short',year:'numeric'})}` : '—';
    }

    // ─── DETAIL MODAL ─────────────────────────────────────────────────────────
    function openDetail(id) {
        currentDetailId = id;
        const c = cases[id];
        if (!c) return;

        document.getElementById('d-id').textContent       = c.id;
        document.getElementById('d-meta').textContent     = `${c.customer} · เปิดวันที่ ${c.date}`;
        document.getElementById('d-customer').textContent = c.customer;
        document.getElementById('d-reason').textContent   = c.reason;
        document.getElementById('d-note').textContent     = c.note;

        const productEl = document.getElementById('d-product');
        productEl.style.textAlign = 'left';
        productEl.innerHTML = c.product.split('\n').map(p => {
            const m = p.trim().match(/^(.+?)\s*\(จำนวน:\s*([\d.]+)\)$/);
            return m
                ? `<div style="display:flex;align-items:baseline;justify-content:space-between;gap:8px;padding:5px 8px;margin-bottom:4px;background:#f8f9ff;border-left:2px solid #1428A0;border-radius:0 4px 4px 0;">
                    <span style="font-size:12px;color:#535353;">${m[1].trim()}</span>
                    <span style="flex-shrink:0;font-size:11px;font-weight:700;color:#1428A0;background:#e8ecf8;padding:1px 8px;border-radius:10px;">×${parseFloat(m[2])}</span></div>`
                : `<div style="padding:5px 8px;margin-bottom:4px;background:#f8f9ff;border-left:2px solid #1428A0;border-radius:0 4px 4px 0;font-size:12px;color:#535353;">${p.trim()}</div>`;
        }).join('');

        const sm = statusMap[c.status] ?? statusMap.processing;
        const badge = document.getElementById('d-badge');
        badge.className   = 'badge ' + sm.cls;
        badge.textContent = sm.label;

        document.getElementById('d-steps').innerHTML = stepLabels.map((label, i) => {
            const cls  = i < c.step ? 'done' : i === c.step ? 'active' : '';
            const icon = i < c.step
                ? `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>`
                : `<span style="font-size:11px;font-weight:700;color:${i===c.step?'var(--samsung-blue)':'#ccc'}">${i+1}</span>`;
            return `<div class="step-item ${cls}"><div class="step-circle">${icon}</div><div class="step-label">${label}</div></div>`;
        }).join('');

        document.getElementById('d-timeline').innerHTML = stepLabels.map((label, i) => {
            if (i >= c.step || !c.stepDates?.[i]) return '';
            return `<div class="timeline-item"><div class="timeline-dot"></div>
                <div><div class="timeline-text">${c.stepDesc?.[i] ?? label}</div>
                <div class="timeline-by">${c.stepBy?.[i] ?? ''} · ${c.stepDates[i]}</div></div></div>`;
        }).join('');

        renderDetailActions(c);
        document.getElementById('detail-modal').classList.add('active');
    }

    function renderDetailActions(c) {
        const act = document.getElementById('d-actions');
        if (c.status === 'finish' || c.status === 'cancel') {
            act.innerHTML = `<button class="btn-cancel" onclick="closeDetail()">ปิด</button>`;
            return;
        }
        if (c.status === 'accept') {
            act.innerHTML = `
                <button class="btn-approve" onclick="doApprove('${c.id}')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    ยืนยัน (Finish)
                </button>`;
            return;
        }
        act.innerHTML = `
            <button class="btn-reject" onclick="doReject('${c.id}')">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                ยกเลิก (Cancel)
            </button>
            <button class="btn-approve" onclick="doApprove('${c.id}')">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                อนุมัติ (Accept)
            </button>`;
    }

    // ─── APPROVE ──────────────────────────────────────────────────────────────
    async function doApprove(id) {
        try {
            const res  = await fetch(`/return/${encodeURIComponent(id)}/approve`, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF() },
            });
            const data = await res.json();
            if (!data.success) { alert(data.message); return; }
            const c  = cases[id];
            const sm = statusMap[data.status] ?? statusMap.processing;
            c.status    = data.status;
            c.step      = sm.step;
            const today = new Date().toISOString().slice(0, 10);
            if (c.step <= 5) c.stepDates[c.step - 1] = today;
            renderTable();
            openDetail(id);
            showToast(`✅ ${data.status === 'accept' ? 'Accept' : 'Finish'} เคส ${id} แล้ว`);
        } catch (err) { alert('เกิดข้อผิดพลาด: ' + err.message); }
    }

    // ─── REJECT ───────────────────────────────────────────────────────────────
    async function doReject(id) {
        if (!confirm(`ยืนยันยกเลิกเคส ${id}?`)) return;
        try {
            const res  = await fetch(`/return/${encodeURIComponent(id)}/reject`, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF() },
            });
            const data = await res.json();
            if (!data.success) { alert(data.message); return; }
            cases[id].status = 'cancel';
            cases[id].step   = 1;
            renderTable();
            closeDetail();
            showToast(`❌ ยกเลิกเคส ${id} แล้ว`);
        } catch (err) { alert('เกิดข้อผิดพลาด: ' + err.message); }
    }

    function closeDetail() { document.getElementById('detail-modal').classList.remove('active'); currentDetailId = null; }
    function closeDetailOnOverlay(e) { if (e.target === document.getElementById('detail-modal')) closeDetail(); }

    // ─── SEARCH ───────────────────────────────────────────────────────────────
    function searchTable(q) {
        q = q.toLowerCase().trim();
        document.querySelectorAll('#cases-tbody tr:not(#empty-row)').forEach(r => {
            r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    // ─── TOAST ────────────────────────────────────────────────────────────────
    function showToast(msg) {
        const t = document.createElement('div');
        t.textContent = msg;
        Object.assign(t.style, {
            position:'fixed', bottom:'28px', left:'50%', transform:'translateX(-50%) translateY(12px)',
            background:'#1d1d1f', color:'#fff', padding:'10px 22px', borderRadius:'24px',
            fontSize:'13px', fontWeight:'600', zIndex:'9999', opacity:'0',
            transition:'all 0.3s ease', whiteSpace:'nowrap', boxShadow:'0 4px 20px rgba(0,0,0,0.2)'
        });
        document.body.appendChild(t);
        requestAnimationFrame(() => { t.style.opacity='1'; t.style.transform='translateX(-50%) translateY(0)'; });
        setTimeout(() => { t.style.opacity='0'; t.style.transform='translateX(-50%) translateY(12px)'; setTimeout(() => t.remove(), 300); }, 2500);
    }

    // ─── BADGE STYLES ─────────────────────────────────────────────────────────
    const style = document.createElement('style');
    style.textContent = `
        .badge-processing { background:#f0eeff; color:#7b61ff; border-color:#c4b8ff; }
        .badge-processing::before { background:#7b61ff; }
        .badge-accept     { background:#fff4e0; color:#cc7a00; border-color:#ffd580; }
        .badge-accept::before     { background:#cc7a00; }
        .badge-rejected   { background:#fff0f0; color:#e53935; border-color:#ffb3b3; }
        .badge-rejected::before   { background:#e53935; }
    `;
    document.head.appendChild(style);

    // ─── INIT ─────────────────────────────────────────────────────────────────
    loadCasesFromDB();
</script>
</body>
</html>