<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim & Return System | Samsung Thailand</title>
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

        /* NAVBAR */
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

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .nav-subtitle {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .nav-badge {
            font-size: 11px;
            color: var(--text-muted);
            background: var(--bg-gray);
            border: 1px solid var(--border);
            padding: 3px 10px;
            border-radius: 20px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-create {
            background: var(--samsung-blue);
            color: #fff;
            border: none;
            padding: 9px 20px;
            border-radius: 24px;
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: all 0.2s;
            letter-spacing: 0.01em;
        }

        .btn-create:hover {
            background: var(--samsung-blue-hover);
            box-shadow: 0 4px 16px rgba(20,40,160,0.25);
            transform: translateY(-1px);
        }

        /* MAIN */
        main {
            padding: 32px 40px;
            max-width: 1280px;
            margin: 0 auto;
        }

        /* STAT CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 22px 24px;
            cursor: default;
            transition: box-shadow 0.2s, transform 0.2s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
        }

        .stat-card.total::after { background: var(--samsung-blue); }
        .stat-card.pending::after { background: #f5a623; }
        .stat-card.processing::after { background: #7b61ff; }
        .stat-card.closed::after { background: #0a7a4b; }
        .stat-card.rejected::after { background: #e53935; }

        .stat-card:hover {
            box-shadow: 0 6px 24px rgba(0,0,0,0.10);
            transform: translateY(-2px);
        }

        .stat-number {
            font-size: 40px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 4px;
            letter-spacing: -1px;
        }

        .stat-card.total .stat-number { color: var(--samsung-blue); }
        .stat-card.pending .stat-number { color: #f5a623; }
        .stat-card.processing .stat-number { color: #7b61ff; }
        .stat-card.closed .stat-number { color: #0a7a4b; }
        .stat-card.rejected .stat-number { color: #e53935; }

        .stat-label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .stat-icon {
            position: absolute;
            top: 16px; right: 16px;
            opacity: 0.15;
            width: 36px;
            height: 36px;
        }

        .stat-icon svg {
            width: 36px;
            height: 36px;
        }

        /* TABLE SECTION */
        .table-section {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        .table-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fafafa;
        }

        .table-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .search-wrapper {
            position: relative;
            width: 280px;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            display: flex;
        }

        .search-input {
            width: 100%;
            background: var(--bg-gray);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 8px 16px 8px 38px;
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 13px;
            color: var(--text-primary);
            outline: none;
            transition: all 0.2s;
        }

        .search-input::placeholder { color: var(--text-muted); }

        .search-input:focus {
            background: #fff;
            border-color: var(--samsung-blue);
            box-shadow: 0 0 0 3px rgba(20,40,160,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead th {
            padding: 13px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: #fafafa;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        thead th:nth-child(1) { width: 14%; }
        thead th:nth-child(2) { width: 16%; }
        thead th:nth-child(3) { width: 22%; }
        thead th:nth-child(4) { width: 12%; }
        thead th:nth-child(5) { width: 14%; }
        thead th:nth-child(6) { width: 12%; }
        thead th:nth-child(7) { width: 10%; }

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.12s;
            cursor: pointer;
        }

        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f5f7ff; }

        tbody td {
            padding: 14px 16px;
            font-size: 13px;
            color: var(--text-primary);
            vertical-align: middle;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        tbody td:nth-child(3) {
            white-space: normal;
            word-break: break-word;
        }

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

        .claim-id {
            color: var(--samsung-blue);
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            font-family: 'Courier New', monospace;
        }

        .customer-name { font-weight: 600; }
        .product-name { color: var(--text-secondary); }
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

        /* EMPTY STATE */
        .empty-state {
            padding: 60px 24px;
            text-align: center;
        }

        .empty-icon {
            width: 56px;
            height: 56px;
            background: var(--bg-gray);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .empty-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .empty-sub {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* PROGRESS */
        .progress-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-bar-bg {
            width: 80px;
            height: 5px;
            background: #e5e5e5;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.6s cubic-bezier(0.22,1,0.36,1);
        }

        .progress-bar-fill.orange { background: #f5a623; }
        .progress-bar-fill.blue { background: var(--samsung-blue); }

        .progress-text {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }

        /* STATUS BADGE */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid;
        }

        .badge::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .badge-pending {
            background: var(--status-pending-bg);
            color: var(--status-pending-text);
            border-color: var(--status-pending-border);
        }

        .badge-pending::before { background: var(--status-pending-text); }

        .badge-closed {
            background: var(--status-closed-bg);
            color: var(--status-closed-text);
            border-color: var(--status-closed-border);
        }

        .badge-closed::before { background: var(--status-closed-text); }

        .date-text {
            font-size: 12px;
            color: var(--text-muted);
            font-family: 'Courier New', monospace;
        }

        /* FOOTER NOTE */
        .table-footer {
            padding: 14px 24px;
            border-top: 1px solid var(--border);
            background: #fafafa;
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* MODAL */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(3px);
            z-index: 200;
            align-items: flex-start;
            justify-content: center;
            overflow-y: auto;
            padding: 24px 0;
        }

        .modal-overlay.active { display: flex; }

        .modal {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            width: 920px;
            max-width: 95vw;
            animation: slideUp 0.25s cubic-bezier(0.22,1,0.36,1);
            box-shadow: 0 20px 60px rgba(0,0,0,0.18);
            margin: auto;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .modal-icon {
            width: 40px;
            height: 40px;
            background: var(--samsung-light-blue);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .form-group { margin-bottom: 16px; }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .form-input, .form-select {
            width: 100%;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            color: var(--text-primary);
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 13px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-input:focus, .form-select:focus {
            border-color: var(--samsung-blue);
            box-shadow: 0 0 0 3px rgba(20,40,160,0.10);
        }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .btn-cancel {
            background: #fff;
            border: 1px solid var(--border);
            color: var(--text-secondary);
            padding: 9px 22px;
            border-radius: 24px;
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-cancel:hover { border-color: #999; color: #333; }

        .btn-submit {
            background: var(--samsung-blue);
            color: #fff;
            border: none;
            padding: 9px 24px;
            border-radius: 24px;
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background: var(--samsung-blue-hover);
            box-shadow: 0 4px 16px rgba(20,40,160,0.3);
            transform: translateY(-1px);
        }

        .po-search-row {
            display: flex;
            gap: 8px;
        }

        .po-search-row .form-input {
            flex: 1;
        }

        .btn-po-search {
            background: var(--samsung-blue);
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .btn-po-search:hover { background: var(--samsung-blue-hover); }

        .docu-found {
            margin-top: 8px;
            padding: 8px 12px;
            background: #e6f7f0;
            border: 1px solid #a3e0c7;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #0a7a4b;
        }

        .docu-error {
            margin-top: 8px;
            padding: 8px 12px;
            background: #fff0f0;
            border: 1px solid #ffb3b3;
            border-radius: 8px;
            font-size: 13px;
            color: #e53935;
        }

        /* DETAIL MODAL */
        .detail-modal {
            width: 920px;
            max-width: 95vw;
            padding: 0;
            overflow: hidden;
            border-radius: 16px;
        }

        .detail-header {
            padding: 24px 28px 20px;
            border-bottom: 1px solid var(--border);
            background: #fafafa;
        }

        .detail-title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .detail-claim-id {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .detail-meta { font-size: 12px; color: var(--text-muted); }

        .detail-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .detail-close {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            transition: all 0.15s;
        }

        .detail-close:hover { border-color: #999; color: var(--text-primary); }

        /* STEPS */
        .steps-bar {
            display: flex;
            align-items: center;
            padding: 20px 28px;
            border-bottom: 1px solid var(--border);
            gap: 0;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 16px;
            left: 60%;
            width: 80%;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }

        .step-item.done:not(:last-child)::after { background: var(--samsung-blue); }

        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #e0e0e0;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
            position: relative;
            transition: all 0.2s;
        }

        .step-item.done .step-circle {
            background: var(--samsung-blue);
            border-color: var(--samsung-blue);
        }

        .step-item.active .step-circle {
            background: #fff;
            border-color: var(--samsung-blue);
            box-shadow: 0 0 0 3px rgba(20,40,160,0.12);
        }

        .step-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 6px;
            font-weight: 500;
            text-align: center;
        }

        .step-item.done .step-label, .step-item.active .step-label {
            color: var(--samsung-blue);
            font-weight: 700;
        }

        /* BODY */
        .detail-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        .detail-info-card {
            padding: 20px 24px;
            border-right: 1px solid var(--border);
        }

        .detail-info-card:last-child { border-right: none; }

        .detail-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 14px;
        }

        .detail-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f5f5f5;
            gap: 12px;
        }

        .detail-info-row:last-child { border-bottom: none; }

        .detail-info-label { font-size: 12px; color: var(--text-muted); flex-shrink: 0; }
        .detail-info-val { font-size: 13px; font-weight: 600; color: var(--text-primary); text-align: right; }

        /* TIMELINE */
        .timeline { display: flex; flex-direction: column; gap: 14px; }

        .timeline-item { display: flex; gap: 12px; align-items: flex-start; }

        .timeline-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--samsung-blue);
            margin-top: 4px;
            flex-shrink: 0;
        }

        .timeline-text { font-size: 13px; font-weight: 500; color: var(--text-primary); line-height: 1.4; }
        .timeline-by { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

        /* ACTIONS */
        .detail-actions {
            padding: 16px 28px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #fafafa;
        }

        .btn-reject {
            background: #fff;
            border: 1.5px solid #e53935;
            color: #e53935;
            padding: 9px 20px;
            border-radius: 24px;
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
        }

        .btn-reject:hover { background: #fff0f0; }

        .btn-approve {
            background: #0a7a4b;
            border: none;
            color: #fff;
            padding: 9px 20px;
            border-radius: 24px;
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
        }

        .btn-approve:hover { background: #085f3a; box-shadow: 0 4px 12px rgba(10,122,75,0.3); }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
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
    </div>
    <div class="nav-right">
        <button class="btn-create" onclick="openModal()">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            สร้างเคสใหม่
        </button>
    </div>
</nav>

<!-- MAIN -->
<main>
    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#1428A0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <div class="stat-number" id="stat-total">0</div>
            <div class="stat-label">ทั้งหมด</div>
        </div>
        <div class="stat-card pending">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#f5a623" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="stat-number" id="stat-pending">0</div>
            <div class="stat-label">รอดำเนินการ</div>
        </div>
        <div class="stat-card processing">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#7b61ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
            </div>
            <div class="stat-number" id="stat-processing">0</div>
            <div class="stat-label">กำลังดำเนินการ</div>
        </div>
        <div class="stat-card closed">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#0a7a4b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="stat-number" id="stat-closed">0</div>
            <div class="stat-label">ปิดแล้ว</div>
        </div>
        <div class="stat-card rejected">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#e53935" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
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
                    <th>รหัสเคส</th>
                    <th>ลูกค้า</th>
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
                            <div class="empty-sub">กดปุ่ม "สร้างเคสใหม่" เพื่อเพิ่มรายการแรก</div>
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

<!-- CREATE MODAL -->
<div class="modal-overlay" id="modal" onclick="closeOnOverlay(event)">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1428A0" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="modal-title">สร้างเคสใหม่</div>
            </div>
        </div>

        <!-- PO SEARCH -->
        <div class="form-group">
            <label class="form-label">รหัส PO <span style="color:#e53935;">*</span></label>
            <div class="po-search-row">
                <input id="f-ponum" class="form-input" type="text" placeholder="กรอก PO Number"
                    onkeydown="if(event.key==='Enter') fetchPODocuNo()">
                <button class="btn-po-search" type="button" onclick="fetchPODocuNo()">ค้นหา</button>
            </div>
            <div id="docu-result"></div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">ชื่อลูกค้า <span style="color:#e53935;">*</span></label>
                <input id="f-customer" class="form-input" type="text" placeholder="ชื่อลูกค้า">
            </div>
            <div class="form-group">
                <label class="form-label">เบอร์โทรศัพท์</label>
                <input id="f-phone" class="form-input" type="tel" placeholder="เบอร์โทรศัพท์">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">สินค้า <span style="color:#e53935;">*</span></label>
            <div id="f-product-placeholder" style="padding:14px 16px;text-align:center;border:1px dashed var(--border);border-radius:8px;font-size:13px;color:var(--text-muted);">
                ค้นหา PO เพื่อโหลดรายการสินค้า
            </div>
            <div id="f-product-table-wrapper" style="display:none;">
                <div style="border-radius:8px;overflow:hidden;max-height:260px;overflow-y:auto;">
                    <table style="width:100%;border-collapse:collapse;table-layout:auto;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;">
                        <thead style="position:sticky;top:0;z-index:2;">
                            <tr style="background:#f4f6ff;border-bottom:2px solid #d0d0d0;">
                                <th style="width:1%;padding:9px 10px;text-align:center;border-right:1px solid #e0e0e0;white-space:nowrap;">
                                    <input type="checkbox" id="chk-all" onchange="toggleAllProducts(this)"
                                        style="width:15px;height:15px;cursor:pointer;accent-color:var(--samsung-blue);">
                                </th>
                                <th style="padding:9px 14px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;border-right:1px solid #e0e0e0;">ชื่อสินค้า</th>
                                <th style="width:1%;padding:9px 10px;text-align:center;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap;">จำนวน</th>
                            </tr>
                        </thead>
                        <tbody id="f-product-tbody"></tbody>
                    </table>
                </div>
                <div id="f-product-selected-count" style="margin-top:6px;font-size:12px;color:var(--text-muted);"></div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">เหตุผลการเคลม <span style="color:#e53935;">*</span></label>
            <select id="f-reason" class="form-select">
                <option value="">-- เลือกเหตุผล --</option>
                <option>สินค้าชำรุด / เสียหาย</option>
                <option>สินค้าไม่ตรงกับที่สั่ง</option>
                <option>สินค้าขาดหาย</option>
                <option>ต้องการคืนสินค้า</option>
                <option>อื่นๆ</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">หมายเหตุ</label>
            <textarea id="f-note" class="form-input" placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)" style="height:80px;resize:vertical;line-height:1.6;"></textarea>
        </div>

        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">ยกเลิก</button>
            <button class="btn-submit" onclick="submitNewCase()">สร้างเคส</button>
        </div>
    </div>
</div>

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
                <div class="detail-info-row"><span class="detail-info-label">ลูกค้า</span><span class="detail-info-val" id="d-customer"></span></div>
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
    // ─── STATE ───────────────────────────────────────────────
    let cases = {};
    let currentDetailId = null;
    let currentDocuNo = null;

    const stepLabels = ['รับแจ้ง', 'ตรวจสอบ', 'อนุมัติ', 'ดำเนินการ', 'ปิดเคส'];
    const statusMap = {
        pending:    { label: 'รอดำเนินการ',    cls: 'badge-pending' },
        processing: { label: 'กำลังดำเนินการ', cls: 'badge-processing' },
        closed:     { label: 'ปิดเคสแล้ว',     cls: 'badge-closed' },
        rejected:   { label: 'ปฏิเสธ',         cls: 'badge-rejected' },
    };

    // ─── FETCH PO DETAIL FROM API ─────────────────────────────
    async function fetchPODocuNo() {
        let poNum = document.getElementById('f-ponum').value.trim();
        const docuResult = document.getElementById('docu-result');

        // ตัด "PO" นำหน้าออกอัตโนมัติ
        poNum = poNum.replace(/^PO/i, '');

        if (!poNum) {
            docuResult.innerHTML = `<div class="docu-error">กรุณากรอก PO Number</div>`;
            return;
        }

        docuResult.innerHTML = `<div style="margin-top:8px;font-size:13px;color:#999;">⏳ กำลังโหลด...</div>`;

        try {
            const response = await fetch(`/api/po-detail?PONum=${encodeURIComponent(poNum)}`);
            const data = await response.json();

            // โครงสร้าง response: ข้อมูล PO อยู่ที่ root level
            // เช่น data.DocuNo, data.VendorName, data.ContTel, data.ms_podt
            const docuNo = data?.DocuNo;

            if (docuNo) {
                currentDocuNo = docuNo;

                // ดึงชื่อ Vendor และเบอร์โทร (ใช้ VendorName เพราะไม่มี CustName ใน PO)
                const custName = data?.VendorName ?? '';
                const contTel  = data?.ContTel    ?? '';

                // ดึงรายการสินค้าจาก ms_podt (อยู่ที่ root level)
                const items = data?.ms_podt ?? [];
                const productLines = items.map(item => {
                    const qty = parseFloat(item.GoodQty2 ?? 0);
                    // ตัด <<...>> tag ออกจากชื่อสินค้า
                    const name = (item.GoodName ?? '').replace(/<<[^>]*>>/g, '').trim();
                    return `${name} (จำนวน: ${qty})`;
                });
                const productText = productLines.join('\n');

                // กรอกฟิลด์อัตโนมัติ
                document.getElementById('f-customer').value = custName;
                document.getElementById('f-phone').value    = contTel;

                // แสดงตารางสินค้า
                renderProductTable(items);

                // แสดงผลสำเร็จ
                docuResult.innerHTML = '';
            } else {
                currentDocuNo = null;
                docuResult.innerHTML = `<div class="docu-error">❌ ไม่พบข้อมูล PO นี้</div>`;
            }
        } catch (err) {
            currentDocuNo = null;
            docuResult.innerHTML = `<div class="docu-error">เกิดข้อผิดพลาด: ${err.message}</div>`;
        }
    }

    // ─── PRODUCT TABLE ───────────────────────────────────────
    let poItems = []; // เก็บรายการสินค้าทั้งหมดจาก PO

    function renderProductTable(items) {
        poItems = items;
        const tbody = document.getElementById('f-product-tbody');
        const wrapper = document.getElementById('f-product-table-wrapper');
        const placeholder = document.getElementById('f-product-placeholder');

        if (!items || items.length === 0) {
            wrapper.style.display = 'none';
            placeholder.style.display = 'block';
            placeholder.textContent = 'ไม่มีรายการสินค้าใน PO นี้';
            return;
        }

        tbody.innerHTML = items.map((item, idx) => {
            const qty = parseFloat(item.GoodQty2 ?? 0);
            const name = (item.GoodName ?? '').replace(/<<[^>]*>>/g, '').trim();
            return `
            <tr id="prod-row-${idx}" style="border-bottom:1px solid #e0e0e0;">
                <td style="padding:10px 10px;text-align:center;vertical-align:middle;border-right:1px solid #e0e0e0;width:1%;white-space:nowrap;">
                    <input type="checkbox" class="prod-chk" data-idx="${idx}"
                        onchange="updateProductSelection()"
                        style="width:15px;height:15px;cursor:pointer;accent-color:var(--samsung-blue);">
                </td>
                <td style="padding:10px 14px;font-size:13px;color:var(--text-primary);line-height:1.6;white-space:normal;word-break:break-word;border-right:1px solid #e0e0e0;">
                    ${name}
                </td>
                <td style="padding:10px 8px;text-align:center;vertical-align:middle;width:1%;white-space:nowrap;">
                    <span class="prod-qty-display" data-idx="${idx}" data-qty="${qty}"
                        style="display:inline-block;width:50px;padding:4px 6px;font-family:'Noto Sans Thai',sans-serif;font-size:13px;font-weight:600;color:var(--samsung-blue);text-align:center;">
                        ${qty}
                    </span>
                </td>
            </tr>`;
        }).join('');

        placeholder.style.display = 'none';
        wrapper.style.display = 'block';
        document.getElementById('chk-all').checked = false;
        updateProductSelection();
    }

    function toggleAllProducts(masterChk) {
        document.querySelectorAll('.prod-chk').forEach(chk => {
            chk.checked = masterChk.checked;
        });
        updateProductSelection();
    }

    function updateProductSelection() {
        const chks = document.querySelectorAll('.prod-chk');
        const total = chks.length;
        const checked = [...chks].filter(c => c.checked).length;
        const masterChk = document.getElementById('chk-all');
        masterChk.checked = checked === total;
        masterChk.indeterminate = checked > 0 && checked < total;
        document.getElementById('f-product-selected-count').textContent =
            checked > 0 ? `เลือกแล้ว ${checked} จาก ${total} รายการ` : 'ยังไม่ได้เลือกสินค้า';
    }

    function getSelectedProductText() {
        const lines = [];
        document.querySelectorAll('.prod-chk').forEach(chk => {
            if (!chk.checked) return;
            const idx = parseInt(chk.dataset.idx, 10);
            const item = poItems[idx];
            if (!item) return;
            const name = (item.GoodName ?? '').replace(/<<[^>]*>>/g, '').trim();
            const qtySpan = document.querySelector(`.prod-qty-display[data-idx="${idx}"]`);
            const qty = qtySpan ? parseFloat(qtySpan.dataset.qty) || 0 : parseFloat(item.GoodQty2 ?? 0);
            if (!name) return;
            lines.push(`${name} (จำนวน: ${qty})`);
        });
        return lines.join('\n');
    }

    function resetProductTable() {
        poItems = [];
        document.getElementById('f-product-table-wrapper').style.display = 'none';
        const ph = document.getElementById('f-product-placeholder');
        ph.style.display = 'block';
        ph.textContent = 'ค้นหา PO เพื่อโหลดรายการสินค้า';
        document.getElementById('f-product-tbody').innerHTML = '';
        document.getElementById('f-product-selected-count').textContent = '';
    }

        // ─── RENDER TABLE ─────────────────────────────────────────
    function renderTable() {
        const tbody = document.getElementById('cases-tbody');
        const all = Object.values(cases);

        if (all.length === 0) {
            tbody.innerHTML = `
            <tr id="empty-row">
                <td colspan="7">
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div class="empty-title">ยังไม่มีรายการเคส</div>
                        <div class="empty-sub">กดปุ่ม "สร้างเคสใหม่" เพื่อเพิ่มรายการแรก</div>
                    </div>
                </td>
            </tr>`;
        } else {
            tbody.innerHTML = all.map(c => {
                const pct = Math.round((c.step / 5) * 100);
                const barCls = c.status === 'closed' ? 'blue' : 'orange';
                const sm = statusMap[c.status] || statusMap.pending;
                return `
                <tr onclick="openDetail('${c.id}')" style="cursor:pointer;">
                    <td><span class="claim-id">${c.id}</span></td>
                    <td><span class="customer-name">${c.customer}</span></td>
                    <td>
                        <div class="product-list">
                            ${c.product.split('\n').map(p => p.trim()).filter(p=>p).map(p => {
                                const match = p.match(/^(.+?)\s*\(จำนวน:\s*([\d.]+)\)$/);
                                if (match) {
                                    return `<div class="product-item"><span>${match[1].trim()}</span><span class="prod-qty">×${parseFloat(match[2])}</span></div>`;
                                }
                                return `<div class="product-item"><span>${p}</span></div>`;
                            }).join('')}
                        </div>
                    </td>
                    <td><span class="reason-text">${c.reason}</span></td>
                    <td>
                        <div class="progress-wrapper">
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill ${barCls}" style="width:${pct}%"></div>
                            </div>
                            <span class="progress-text">${c.step}/5</span>
                        </div>
                    </td>
                    <td><span class="badge ${sm.cls}">${sm.label}</span></td>
                    <td><span class="date-text">${c.date}</span></td>
                </tr>`;
            }).join('');
        }
        updateStats();
    }

    // ─── STATS ────────────────────────────────────────────────
    function updateStats() {
        const all = Object.values(cases);
        document.getElementById('stat-total').textContent      = all.length;
        document.getElementById('stat-pending').textContent    = all.filter(c=>c.status==='pending').length;
        document.getElementById('stat-processing').textContent = all.filter(c=>c.status==='processing').length;
        document.getElementById('stat-closed').textContent     = all.filter(c=>c.status==='closed').length;
        document.getElementById('stat-rejected').textContent   = all.filter(c=>c.status==='rejected').length;
        document.getElementById('footer-count').textContent =
            `แสดง ${all.length} รายการ จากทั้งหมด ${all.length} รายการ`;
        if (all.length > 0) {
            const now = new Date();
            document.getElementById('footer-updated').textContent =
                `อัปเดตล่าสุด: ${now.toLocaleDateString('th-TH', {day:'numeric',month:'short',year:'numeric'})}`;
        } else {
            document.getElementById('footer-updated').textContent = '—';
        }
    }

    // ─── CREATE CASE ──────────────────────────────────────────
    function openModal() {
        currentDocuNo = null;
        document.getElementById('modal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('modal').classList.remove('active');
        ['f-ponum','f-customer','f-phone','f-note'].forEach(id => document.getElementById(id).value = '');
        resetProductTable();
        document.getElementById('f-reason').value = '';
        document.getElementById('docu-result').innerHTML = '';
        currentDocuNo = null;
    }

    function closeOnOverlay(e) { if (e.target === document.getElementById('modal')) closeModal(); }

    function submitNewCase() {
        const customer = document.getElementById('f-customer').value.trim();
        const product  = getSelectedProductText();
        const reason   = document.getElementById('f-reason').value;
        const note     = document.getElementById('f-note').value.trim();

        if (!currentDocuNo) {
            alert('กรุณาค้นหาและยืนยัน PO Number ก่อน');
            return;
        }
        if (!customer || !product || !reason) {
            alert('กรุณากรอกชื่อลูกค้า, สินค้า และเหตุผลให้ครบถ้วน');
            return;
        }

        const id = currentDocuNo; // ใช้ DocuNo จาก API เป็นรหัสเคส
        const today = new Date().toISOString().slice(0,10);

        cases[id] = {
            id, customer, product, reason,
            fix: '-', note: note || '-',
            date: today, status: 'processing', step: 2,
            stepDates: [today, today, null, null, null],
            stepBy:    ['ฝ่ายบริการ', 'ช่างเทคนิค', null, null, null],
            stepDesc:  ['รับแจ้งเรื่องจากลูกค้า', 'ตรวจสอบสินค้าเรียบร้อย', null, null, null],
        };

        closeModal();
        renderTable();
        showToast(`✅ สร้างเคส ${id} เรียบร้อยแล้ว`);
    }

    // ─── DETAIL MODAL ─────────────────────────────────────────
    function openDetail(id) {
        currentDetailId = id;
        const c = cases[id];
        if (!c) return;

        document.getElementById('d-id').textContent = c.id;
        document.getElementById('d-meta').textContent = `${c.customer} · เปิดวันที่ ${c.date}`;
        document.getElementById('d-customer').textContent = c.customer;
        const productLines = c.product.split('\n').map(p => p.trim()).filter(p => p);
        const productEl = document.getElementById('d-product');
        productEl.style.textAlign = 'left';
        productEl.innerHTML = productLines.map(p => {
            const match = p.match(/^(.+?)\s*\(จำนวน:\s*([\d.]+)\)$/);
            if (match) {
                return `<div style="display:flex;align-items:baseline;justify-content:space-between;gap:8px;padding:5px 8px;margin-bottom:4px;background:#f8f9ff;border-left:2px solid #1428A0;border-radius:0 4px 4px 0;">
                    <span style="font-size:12px;color:#535353;">${match[1].trim()}</span>
                    <span style="flex-shrink:0;font-size:11px;font-weight:700;color:#1428A0;background:#e8ecf8;padding:1px 8px;border-radius:10px;">×${parseFloat(match[2])}</span>
                </div>`;
            }
            return `<div style="padding:5px 8px;margin-bottom:4px;background:#f8f9ff;border-left:2px solid #1428A0;border-radius:0 4px 4px 0;font-size:12px;color:#535353;">${p}</div>`;
        }).join('');
        document.getElementById('d-reason').textContent   = c.reason;
        document.getElementById('d-note').textContent     = c.note;

        const sm = statusMap[c.status] || statusMap.pending;
        const badge = document.getElementById('d-badge');
        badge.className = 'badge ' + sm.cls;
        badge.textContent = sm.label;

        document.getElementById('d-steps').innerHTML = stepLabels.map((label, i) => {
            let cls = i < c.step ? 'done' : i === c.step ? 'active' : '';
            let icon = i < c.step
                ? `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>`
                : `<span style="font-size:11px;font-weight:700;color:${i===c.step?'var(--samsung-blue)':'#ccc'}">${i+1}</span>`;
            return `<div class="step-item ${cls}"><div class="step-circle">${icon}</div><div class="step-label">${label}</div></div>`;
        }).join('');

        document.getElementById('d-timeline').innerHTML = stepLabels.map((label, i) => {
            if (i >= c.step || !c.stepDates[i]) return '';
            const desc = (c.stepDesc && c.stepDesc[i]) ? c.stepDesc[i] : label;
            return `<div class="timeline-item">
                <div class="timeline-dot"></div>
                <div><div class="timeline-text">${desc}</div>
                <div class="timeline-by">${c.stepBy[i]} · ${c.stepDates[i]}</div></div>
            </div>`;
        }).join('');

        renderDetailActions(c);
        document.getElementById('detail-modal').classList.add('active');
    }

    function renderDetailActions(c) {
        const act = document.getElementById('d-actions');
        if (c.status === 'closed' || c.status === 'rejected') {
            act.innerHTML = `<button class="btn-cancel" onclick="closeDetail()">ปิด</button>`;
        } else if (c.step <= 2) {
            act.innerHTML = `
                <button class="btn-reject" onclick="rejectCase('${c.id}')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>ปฏิเสธ
                </button>
                <button class="btn-approve" onclick="approveCase('${c.id}')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>อนุมัติ
                </button>`;
        } else {
            act.innerHTML = `
                <button class="btn-approve" onclick="approveCase('${c.id}')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>อนุมัติ
                </button>`;
        }
    }

    function closeDetail() {
        document.getElementById('detail-modal').classList.remove('active');
        currentDetailId = null;
    }
    function closeDetailOnOverlay(e) { if (e.target===document.getElementById('detail-modal')) closeDetail(); }

    // ─── APPROVE / REJECT ─────────────────────────────────────
    const approveDesc = [
        'รับแจ้งเรื่องจากลูกค้า',
        'ตรวจสอบสินค้าเรียบร้อย',
        'อนุมัติการดำเนินการ',
        'ดำเนินการจัดส่ง / ซ่อม / เปลี่ยนสินค้า',
        'ปิดเคสเรียบร้อย',
    ];
    const approveBy = ['ฝ่ายบริการ','ช่างเทคนิค','ผู้จัดการ','ฝ่ายจัดส่ง','ฝ่ายบริการ'];

    function approveCase(id) {
        const c = cases[id];
        if (!c) return;
        const today = new Date().toISOString().slice(0,10);

        if (c.step < 5) {
            if (!c.stepDesc) c.stepDesc = [null,null,null,null,null];
            const stepsToAdvance = (c.step === 3) ? 2 : 1;
            for (let s = 0; s < stepsToAdvance && c.step < 5; s++) {
                c.stepDates[c.step] = today;
                c.stepBy[c.step]    = approveBy[c.step];
                c.stepDesc[c.step]  = approveDesc[c.step];
                c.step++;
            }
        }

        if (c.step >= 5) {
            c.status = 'closed';
            c.fix = 'อนุมัติและปิดเคส';
        } else if (c.step >= 3) {
            c.status = 'processing';
        } else {
            c.status = 'pending';
        }

        renderTable();
        openDetail(id);
        showToast(`✅ อนุมัติเคส ${id} เรียบร้อยแล้ว`);
    }

    function rejectCase(id) {
        if (!confirm(`ยืนยันปฏิเสธเคส ${id}?`)) return;
        cases[id].status = 'rejected';
        renderTable();
        closeDetail();
        showToast(`❌ ปฏิเสธเคส ${id} แล้ว`);
    }

    // ─── SEARCH ───────────────────────────────────────────────
    function searchTable(query) {
        const q = query.toLowerCase().trim();
        document.querySelectorAll('#cases-tbody tr:not(#empty-row)').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    // ─── TOAST ────────────────────────────────────────────────
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
        requestAnimationFrame(() => {
            t.style.opacity = '1';
            t.style.transform = 'translateX(-50%) translateY(0)';
        });
        setTimeout(() => {
            t.style.opacity = '0';
            t.style.transform = 'translateX(-50%) translateY(12px)';
            setTimeout(() => t.remove(), 300);
        }, 2500);
    }

    // ─── BADGE EXTRA STYLES ───────────────────────────────────
    const style = document.createElement('style');
    style.textContent = `
        .badge-processing { background:#f0eeff; color:#7b61ff; border-color:#c4b8ff; }
        .badge-processing::before { background:#7b61ff; }
        .badge-rejected   { background:#fff0f0; color:#e53935; border-color:#ffb3b3; }
        .badge-rejected::before   { background:#e53935; }
    `;
    document.head.appendChild(style);

    // ─── INIT ─────────────────────────────────────────────────
    renderTable();
</script>
</body>
</html>