<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Return System</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --samsung-blue: #1428A0;
            --samsung-blue-hover: #0f1f80;
            --samsung-blue-light: #e8ecf8;
            --sidebar-w: 240px;
            --sidebar-bg: #0d1b6e;
            --sidebar-border: rgba(255,255,255,0.08);
            --sidebar-text: rgba(255,255,255,0.65);
            --sidebar-text-active: #fff;
            --sidebar-hover: rgba(255,255,255,0.07);
            --sidebar-active: rgba(255,255,255,0.13);
            --topbar-h: 60px;
            --bg: #f4f5fb;
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
            --radius: 12px;
        }

        body {
            font-family: 'Noto Sans Thai', Arial, sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            width: 100%;
            overflow-x: hidden;
            font-size: 14px;
            display: flex;
        }

        /* ═══════════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0; top: 0; bottom: 0;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            transition: width 0.25s cubic-bezier(0.22,1,0.36,1);
            overflow: hidden;
        }

        .sidebar.collapsed { width: 64px; }

        /* Logo area */
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 18px;
            border-bottom: 1px solid var(--sidebar-border);
            min-height: var(--topbar-h);
        }
        .sidebar-logo-icon {
            width: 32px; height: 32px;
            background: linear-gradient(135deg,#3b5bdb,#1428A0);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 10px rgba(20,40,160,0.4);
        }
        .sidebar-logo-text {
            opacity: 1;
            transition: opacity 0.2s;
            white-space: nowrap;
            overflow: hidden;
        }
        .sidebar.collapsed .sidebar-logo-text { opacity: 0; pointer-events: none; }
        .sidebar-logo-main { font-size: 14px; font-weight: 700; color: #fff; letter-spacing: 0.02em; }
        .sidebar-logo-sub  { font-size: 10px; color: rgba(255,255,255,0.45); margin-top: 1px; }

        /* Nav groups */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 14px 0;
            scrollbar-width: none;
        }
        .sidebar-nav::-webkit-scrollbar { display: none; }

        .nav-group-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.28);
            padding: 10px 20px 5px;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s;
        }
        .sidebar.collapsed .nav-group-label { opacity: 0; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 18px;
            margin: 2px 10px;
            border-radius: 9px;
            cursor: pointer;
            color: var(--sidebar-text);
            font-size: 13px;
            font-weight: 500;
            transition: all 0.15s;
            white-space: nowrap;
            position: relative;
            text-decoration: none;
        }
        .nav-item:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }
        .nav-item.active {
            background: var(--sidebar-active);
            color: var(--sidebar-text-active);
            font-weight: 700;
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: -10px; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 18px;
            background: #5b8af7;
            border-radius: 0 3px 3px 0;
        }
        .nav-item-icon {
            width: 18px; height: 18px;
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .nav-item-label {
            flex: 1;
            opacity: 1;
            transition: opacity 0.2s;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar.collapsed .nav-item-label { opacity: 0; }

        .nav-badge {
            background: rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.8);
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
            transition: opacity 0.2s;
            flex-shrink: 0;
        }
        .sidebar.collapsed .nav-badge { opacity: 0; }

        /* Tooltip for collapsed state */
        .nav-item .nav-tooltip {
            display: none;
            position: absolute;
            left: 62px; top: 50%;
            transform: translateY(-50%);
            background: #1d1d1f;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 7px;
            white-space: nowrap;
            pointer-events: none;
            z-index: 999;
            box-shadow: 0 4px 16px rgba(0,0,0,0.25);
        }
        .sidebar.collapsed .nav-item:hover .nav-tooltip { display: block; }

        /* Sidebar user card */
        .sidebar-user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 10px 4px;
            padding: 10px 12px;
            background: rgba(255,255,255,0.07);
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.08);
            overflow: hidden;
            transition: opacity 0.2s;
        }
        .sidebar.collapsed .sidebar-user-card { justify-content: center; padding: 8px; }
        .sidebar-user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg,#5b8af7,#1428A0);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }
        .sidebar-user-info { overflow: hidden; transition: opacity 0.2s; }
        .sidebar.collapsed .sidebar-user-info { opacity: 0; width: 0; }
        .sidebar-user-name { font-size: 13px; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-role { font-size: 10px; color: rgba(255,255,255,0.45); margin-top: 1px; white-space: nowrap; }
        .sidebar-user-role.is-admin { color: #f5a623; }

        /* Sidebar divider */
        .sidebar-divider {
            height: 1px;
            background: var(--sidebar-border);
            margin: 8px 20px;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 14px;
            border-top: 1px solid var(--sidebar-border);
        }
        .sidebar-collapse-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 9px 10px;
            border-radius: 8px;
            background: none;
            border: none;
            color: rgba(255,255,255,0.5);
            cursor: pointer;
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 12px;
            transition: all 0.15s;
        }
        .sidebar-collapse-btn:hover { background: var(--sidebar-hover); color: #fff; }
        .collapse-icon { transition: transform 0.25s; flex-shrink: 0; }
        .sidebar.collapsed .collapse-icon { transform: rotate(180deg); }
        .collapse-label { transition: opacity 0.2s; white-space: nowrap; overflow: hidden; }
        .sidebar.collapsed .collapse-label { opacity: 0; }

        /* ═══════════════════════════════════════════
           MAIN LAYOUT
        ═══════════════════════════════════════════ */
        .page-wrapper {
            flex: 1;
            min-width: 0;
            width: calc(100% - var(--sidebar-w));
            margin-left: var(--sidebar-w);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left 0.25s cubic-bezier(0.22,1,0.36,1), width 0.25s cubic-bezier(0.22,1,0.36,1);
        }
        .page-wrapper.sidebar-collapsed { margin-left: 64px; width: calc(100% - 64px); }

        /* Top bar */
        .topbar {
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky; top: 0; z-index: 50;
            box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        }
        .topbar-left { display: flex; align-items: center; gap: 8px; }
        .topbar-breadcrumb { font-size: 12px; color: var(--text-muted); }
        .topbar-breadcrumb strong { color: var(--text-primary); font-weight: 700; }
        .topbar-sep { color: var(--border); margin: 0 4px; }
        .topbar-right { display: flex; align-items: center; gap: 10px; }

        .btn-create {
            background: var(--samsung-blue); color: #fff; border: none;
            padding: 9px 20px; border-radius: 24px;
            font-family: 'Noto Sans Thai', sans-serif;
            font-size: 13px; font-weight: 600; cursor: pointer;
            display: flex; align-items: center; gap: 7px;
            transition: all 0.2s;
        }
        .btn-create:hover {
            background: var(--samsung-blue-hover);
            box-shadow: 0 4px 16px rgba(20,40,160,0.25);
            transform: translateY(-1px);
        }

        /* ═══════════════════════════════════════════
           CONTENT
        ═══════════════════════════════════════════ */
        main { padding: 28px 32px; flex: 1; }

        .page-title { font-size: 20px; font-weight: 800; color: var(--text-primary); margin-bottom: 22px; letter-spacing: -0.3px; }

        .stats-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 14px; margin-bottom: 26px;
        }
        .stat-card {
            background: #fff; border: 1px solid var(--border);
            border-radius: var(--radius); padding: 20px 22px;
            transition: box-shadow 0.2s, transform 0.2s;
            position: relative; overflow: hidden;
        }
        .stat-card::after {
            content: ''; position: absolute;
            bottom: 0; left: 0; right: 0; height: 3px;
        }
        .stat-card.total::after    { background: var(--samsung-blue); }
        .stat-card.pending::after  { background: #f5a623; }

        .stat-card.closed::after   { background: #0a7a4b; }
        .stat-card.rejected::after { background: #e53935; }
        .stat-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.10); transform: translateY(-2px); }

        .stat-number {
            font-size: 38px; font-weight: 700;
            line-height: 1; margin-bottom: 4px; letter-spacing: -1px;
        }
        .stat-card.total .stat-number      { color: var(--samsung-blue); }
        .stat-card.pending .stat-number    { color: #f5a623; }

        .stat-card.closed .stat-number     { color: #0a7a4b; }
        .stat-card.rejected .stat-number   { color: #e53935; }
        .stat-label { font-size: 12px; color: var(--text-muted); font-weight: 500; }

        .table-section {
            background: #fff; border: 1px solid var(--border);
            border-radius: var(--radius); overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }
        .table-header {
            padding: 18px 22px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            background: #fafafa;
        }
        .table-title { font-size: 14px; font-weight: 700; color: var(--text-primary); }

        .search-wrapper { position: relative; width: 270px; }
        .search-icon {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%); color: var(--text-muted);
            pointer-events: none; display: flex;
        }
        .search-input {
            width: 100%; background: var(--bg);
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
            padding: 12px 16px; text-align: left;
            font-size: 11px; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.08em;
            background: #fafafa; border-bottom: 1px solid var(--border);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        thead th:nth-child(1) { width: 10%; }
        thead th:nth-child(2) { width: 14%; }
        thead th:nth-child(3) { width: 23%; }
        thead th:nth-child(4) { width: 12%; }
        thead th:nth-child(5) { width: 12%; }
        thead th:nth-child(6) { width: 14%; }
        thead th:nth-child(7) { width: 10%; }

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.12s; cursor: pointer;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f5f7ff; }
        tbody td {
            padding: 13px 16px; font-size: 13px;
            color: var(--text-primary); vertical-align: middle;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        tbody td:nth-child(3) { white-space: normal; word-break: break-word; }

        .claim-id { color: var(--samsung-blue); font-weight: 700; font-size: 13px; font-family: 'Courier New', monospace; }
        .customer-name { font-weight: 600; }
        .product-list { display: flex; flex-direction: column; gap: 4px; }
        .product-item {
            display: flex; align-items: baseline; gap: 6px;
            font-size: 12px; color: var(--text-secondary);
            white-space: normal; word-break: break-word; line-height: 1.5;
            padding: 3px 8px; background: #f8f9ff;
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
            width: 52px; height: 52px; background: var(--bg);
            border-radius: 14px; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 14px;
        }
        .empty-title { font-size: 14px; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; }
        .empty-sub { font-size: 13px; color: var(--text-muted); }

        .progress-wrapper { display: flex; align-items: center; gap: 10px; }
        .progress-bar-bg { width: 72px; height: 5px; background: #e5e5e5; border-radius: 3px; overflow: hidden; }
        .progress-bar-fill { height: 100%; border-radius: 3px; transition: width 0.6s cubic-bezier(0.22,1,0.36,1); }
        .progress-bar-fill.orange { background: #f5a623; }
        .progress-bar-fill.blue   { background: var(--samsung-blue); }
        .progress-text { font-size: 12px; color: var(--text-muted); font-weight: 600; font-family: 'Courier New', monospace; }

        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 11px; border-radius: 20px;
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
        .badge-processing { background:#f0eeff; color:#7b61ff; border-color:#c4b8ff; }
        .badge-processing::before { background:#7b61ff; }
        .badge-accept     { background:#fff4e0; color:#cc7a00; border-color:#ffd580; }
        .badge-accept::before     { background:#cc7a00; }
        .badge-rejected   { background:#fff0f0; color:#e53935; border-color:#ffb3b3; }
        .badge-rejected::before   { background:#e53935; }

        .date-text { font-size: 12px; color: var(--text-muted); font-family: 'Courier New', monospace; }

        .table-footer {
            padding: 13px 22px; border-top: 1px solid var(--border);
            background: #fafafa; font-size: 12px; color: var(--text-muted);
            display: flex; align-items: center; justify-content: space-between;
        }

        /* photo thumb */
        .photo-thumb { width: 34px; height: 34px; border-radius: 6px; object-fit: cover; border: 1px solid var(--border); cursor: pointer; transition: transform 0.15s; }
        .photo-thumb:hover { transform: scale(1.1); }
        .photo-count-badge { font-size: 10px; font-weight: 700; color: var(--samsung-blue); background: #e8ecf8; padding: 2px 6px; border-radius: 10px; }

        /* ═══════════════════════════════════════════
           MODALS
        ═══════════════════════════════════════════ */
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
            from { opacity:0; transform:translateY(20px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .modal-header { display: flex; align-items: center; gap: 12px; margin-bottom: 22px; }
        .modal-icon {
            width: 38px; height: 38px; background: var(--samsung-blue-light);
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
        }
        .modal-title { font-size: 16px; font-weight: 700; }

        .form-group { margin-bottom: 14px; }
        .form-label {
            display: block; font-size: 11px; font-weight: 700;
            color: var(--text-secondary); margin-bottom: 5px;
            text-transform: uppercase; letter-spacing: 0.06em;
        }
        .form-input, .form-select {
            width: 100%; background: #fff; border: 1px solid var(--border);
            border-radius: 8px; padding: 10px 14px;
            color: var(--text-primary); font-family: 'Noto Sans Thai', sans-serif;
            font-size: 13px; outline: none; transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-input:focus, .form-select:focus {
            border-color: var(--samsung-blue);
            box-shadow: 0 0 0 3px rgba(20,40,160,0.10);
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        .modal-actions {
            display: flex; gap: 10px; justify-content: flex-end;
            margin-top: 22px; padding-top: 18px; border-top: 1px solid var(--border);
        }
        .btn-cancel {
            background: #fff; border: 1px solid var(--border);
            color: var(--text-secondary); padding: 9px 22px; border-radius: 24px;
            font-family: 'Noto Sans Thai', sans-serif; font-size: 13px;
            font-weight: 600; cursor: pointer; transition: all 0.15s;
        }
        .btn-cancel:hover { border-color: #999; color: #333; }
        .btn-submit {
            background: var(--samsung-blue); color: #fff; border: none;
            padding: 9px 24px; border-radius: 24px;
            font-family: 'Noto Sans Thai', sans-serif; font-size: 13px;
            font-weight: 700; cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; gap: 8px;
        }
        .btn-submit:hover { background: var(--samsung-blue-hover); box-shadow: 0 4px 16px rgba(20,40,160,0.3); transform: translateY(-1px); }
        .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }

        .po-search-row { display: flex; gap: 8px; }
        .po-search-row .form-input { flex: 1; }
        .btn-po-search {
            background: var(--samsung-blue); color: #fff; border: none;
            padding: 10px 16px; border-radius: 8px;
            font-family: 'Noto Sans Thai', sans-serif; font-size: 13px;
            font-weight: 600; cursor: pointer; white-space: nowrap; transition: all 0.2s;
        }
        .btn-po-search:hover { background: var(--samsung-blue-hover); }

        .docu-found { margin-top: 8px; padding: 8px 12px; background: #e6f7f0; border: 1px solid #a3e0c7; border-radius: 8px; font-size: 13px; font-weight: 600; color: #0a7a4b; }
        .docu-error { margin-top: 8px; padding: 8px 12px; background: #fff0f0; border: 1px solid #ffb3b3; border-radius: 8px; font-size: 13px; color: #e53935; }

        /* Image upload */
        .image-upload-zone {
            border: 2px dashed var(--border); border-radius: 10px;
            padding: 18px; text-align: center; cursor: pointer;
            transition: all 0.2s; background: #fafafa; position: relative;
        }
        .image-upload-zone:hover, .image-upload-zone.dragover { border-color: var(--samsung-blue); background: var(--samsung-blue-light); }
        .image-upload-zone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
        .upload-zone-text { font-size: 13px; color: var(--text-secondary); font-weight: 500; }
        .upload-zone-sub  { font-size: 11px; color: var(--text-muted); margin-top: 3px; }

        .image-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(86px, 1fr)); gap: 8px; margin-top: 10px; }
        .preview-item { position: relative; border-radius: 8px; overflow: hidden; aspect-ratio: 1; background: #f0f0f0; border: 1px solid var(--border); }
        .preview-item img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .preview-item .remove-btn {
            position: absolute; top: 4px; right: 4px;
            background: rgba(0,0,0,0.65); color: #fff; border: none; border-radius: 50%;
            width: 20px; height: 20px; font-size: 12px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; transition: background 0.15s;
        }
        .preview-item .remove-btn:hover { background: rgba(229,57,53,0.9); }
        .preview-item.done-upload::after { content:'✓'; position:absolute; bottom:4px; right:4px; background:#0a7a4b; color:#fff; border-radius:50%; width:18px; height:18px; font-size:11px; display:flex; align-items:center; justify-content:center; }
        .preview-item.error-upload::after { content:'✕'; position:absolute; bottom:4px; right:4px; background:#e53935; color:#fff; border-radius:50%; width:18px; height:18px; font-size:11px; display:flex; align-items:center; justify-content:center; }

        .upload-status-bar { margin-top: 8px; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; display: none; }
        .upload-status-bar.uploading { display:flex; align-items:center; gap:8px; background:#f0eeff; color:#7b61ff; border:1px solid #c4b8ff; }
        .upload-status-bar.done      { display:flex; align-items:center; gap:8px; background:#e6f7f0; color:#0a7a4b; border:1px solid #a3e0c7; }
        .upload-status-bar.error     { display:flex; align-items:center; gap:8px; background:#fff0f0; color:#e53935; border:1px solid #ffb3b3; }

        /* Invoice cards */
        .invoice-cards-wrapper { display:flex; flex-wrap:wrap; gap:3px; justify-content:center; text-align:left; }
        .invoice-card { display:inline-flex; flex-direction:column; gap:1px; background:#f0f4ff; border:1px solid #c8d4f8; border-radius:6px; padding:4px 8px; font-size:10px; white-space:nowrap; }
        .invoice-card-num { font-weight:700; color:var(--samsung-blue); }

        /* Detail modal */
        .detail-modal { width: 980px; max-width: 95vw; padding: 0; overflow: hidden; border-radius: 16px; }
        .detail-header { padding: 22px 26px 18px; border-bottom: 1px solid var(--border); background: #fafafa; }
        .detail-title-row { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
        .detail-claim-id { font-size: 21px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.5px; margin-bottom: 3px; }
        .detail-meta { font-size: 12px; color: var(--text-muted); }
        .detail-header-right { display:flex; align-items:center; gap:10px; flex-shrink:0; }
        .detail-close {
            background: #fff; border: 1px solid var(--border); border-radius: 8px;
            width: 32px; height: 32px; display:flex; align-items:center; justify-content:center;
            cursor: pointer; color: var(--text-muted); transition: all 0.15s;
        }
        .detail-close:hover { border-color: #999; color: var(--text-primary); }

        .steps-bar { display:flex; align-items:center; padding:18px 26px; border-bottom:1px solid var(--border); }
        .step-item { display:flex; flex-direction:column; align-items:center; flex:1; position:relative; }
        .step-item:not(:last-child)::after { content:''; position:absolute; top:16px; left:60%; width:80%; height:2px; background:#e0e0e0; z-index:0; }
        .step-item.done:not(:last-child)::after { background: var(--samsung-blue); }
        .step-circle { width:32px; height:32px; border-radius:50%; border:2px solid #e0e0e0; background:#fff; display:flex; align-items:center; justify-content:center; z-index:1; position:relative; }
        .step-item.done .step-circle   { background:var(--samsung-blue); border-color:var(--samsung-blue); }
        .step-item.active .step-circle { background:#fff; border-color:var(--samsung-blue); box-shadow:0 0 0 3px rgba(20,40,160,0.12); }
        .step-label { font-size:11px; color:var(--text-muted); margin-top:6px; font-weight:500; text-align:center; }
        .step-item.done .step-label, .step-item.active .step-label { color:var(--samsung-blue); font-weight:700; }

        .detail-body { display:grid; grid-template-columns:1fr 1fr; }
        .detail-info-card { padding:18px 22px; border-right:1px solid var(--border); }
        .detail-info-card:last-child { border-right:none; }
        .detail-section-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); margin-bottom:12px; }
        .detail-info-row { display:flex; justify-content:space-between; align-items:flex-start; padding:7px 0; border-bottom:1px solid #f5f5f5; gap:12px; }
        .detail-info-row:last-child { border-bottom:none; }
        .detail-info-label { font-size:12px; color:var(--text-muted); flex-shrink:0; }
        .detail-info-val   { font-size:13px; font-weight:600; color:var(--text-primary); text-align:right; }

        .detail-photos-section { border-top:1px solid var(--border); background:#fafafa; }
        .btn-add-photo {
            display:inline-flex; align-items:center; gap:4px;
            padding:4px 10px; border-radius:16px; border:1.5px dashed var(--samsung-blue);
            color:var(--samsung-blue); font-size:11px; font-weight:700;
            background:#f0f4ff; cursor:pointer; transition:all 0.15s;
            font-family:'Noto Sans Thai',sans-serif;
        }
        .btn-add-photo:hover { background:var(--samsung-blue); color:#fff; }
        @media (max-width: 767px) {
            .detail-photos-section > div { grid-template-columns:1fr !important; }
            .detail-photos-section > div > div { border-right:none !important; border-bottom:1px solid var(--border); }
        }
        .detail-photos-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(76px, 1fr)); gap:8px; margin-top:10px; }
        .detail-photo-thumb { aspect-ratio:1; border-radius:8px; overflow:hidden; border:1px solid var(--border); cursor:pointer; transition:transform 0.15s, box-shadow 0.15s; }
        .detail-photo-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
        .detail-photo-thumb:hover { transform:scale(1.05); box-shadow:0 4px 16px rgba(0,0,0,0.2); }
        .no-photos-placeholder { padding:14px; text-align:center; font-size:12px; color:var(--text-muted); background:var(--bg); border-radius:8px; }

        .timeline { display:flex; flex-direction:column; gap:12px; }
        .timeline-item { display:flex; gap:12px; align-items:flex-start; }
        .timeline-dot { width:8px; height:8px; border-radius:50%; background:var(--samsung-blue); margin-top:4px; flex-shrink:0; }
        .timeline-text { font-size:13px; font-weight:500; color:var(--text-primary); line-height:1.4; }
        .timeline-by   { font-size:11px; color:var(--text-muted); margin-top:2px; }

        .detail-actions { padding:14px 26px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px; background:#fafafa; }

        /* Lightbox */
        .btn-action-cancel {
            background:#fff; border:1.5px solid #e53935; color:#e53935;
            padding:9px 20px; border-radius:24px;
            font-family:'Noto Sans Thai',sans-serif; font-size:13px; font-weight:700;
            cursor:pointer; transition:all 0.15s; display:flex; align-items:center; gap:6px;
        }
        .btn-action-cancel:hover { background:#fff0f0; }
        .btn-action-accept {
            background:#0a7a4b; color:#fff; border:none;
            padding:9px 20px; border-radius:24px;
            font-family:'Noto Sans Thai',sans-serif; font-size:13px; font-weight:700;
            cursor:pointer; transition:all 0.15s; display:flex; align-items:center; gap:6px;
        }
        .btn-action-accept:hover { background:#085f3a; box-shadow:0 4px 14px rgba(10,122,75,0.3); }
        .btn-action-finish {
            background:var(--samsung-blue); color:#fff; border:none;
            padding:9px 20px; border-radius:24px;
            font-family:'Noto Sans Thai',sans-serif; font-size:13px; font-weight:700;
            cursor:pointer; transition:all 0.15s; display:flex; align-items:center; gap:6px;
        }
        .btn-action-finish:hover { background:var(--samsung-blue-hover); box-shadow:0 4px 14px rgba(20,40,160,0.3); }

        .readonly-notice {
            font-size:12px; color:var(--text-muted);
            display:flex; align-items:center; gap:6px;
            padding:6px 10px; background:#f5f5f5; border-radius:20px; border:1px solid var(--border);
        }

        /* Lightbox */
        .lightbox-overlay {
            display:none; position:fixed; inset:0; background:rgba(0,0,0,0.92);
            z-index:500; align-items:center; justify-content:center; flex-direction:column; gap:16px;
        }
        .lightbox-overlay.active { display:flex; }
        .lightbox-img { max-width:90vw; max-height:80vh; border-radius:10px; box-shadow:0 24px 80px rgba(0,0,0,0.6); object-fit:contain; }
        .lightbox-nav { display:flex; align-items:center; gap:20px; }
        .lightbox-btn { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); color:#fff; width:40px; height:40px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.15s; font-size:18px; }
        .lightbox-btn:hover { background:rgba(255,255,255,0.3); }
        .lightbox-counter { font-size:13px; color:rgba(255,255,255,0.7); min-width:60px; text-align:center; }
        .lightbox-close-btn { position:fixed; top:20px; right:20px; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); color:#fff; width:40px; height:40px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:18px; }
        .lightbox-close-btn:hover { background:rgba(229,57,53,0.8); }
        .lightbox-caption { font-size:12px; color:rgba(255,255,255,0.6); }

        /* Product table stuff */
        input[type=number].prod-qty-display:focus { border-color:var(--samsung-blue)!important; background:#fff!important; box-shadow:0 0 0 3px rgba(20,40,160,0.10); }
        input[type=number].prod-qty-display::-webkit-inner-spin-button,
        input[type=number].prod-qty-display::-webkit-outer-spin-button { opacity:1; }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity:0; transform:translateY(12px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .stat-card { animation:fadeInUp 0.4s ease both; }
        .stat-card:nth-child(1){animation-delay:0.05s}
        .stat-card:nth-child(2){animation-delay:0.10s}
        .stat-card:nth-child(3){animation-delay:0.15s}
        .stat-card:nth-child(4){animation-delay:0.20s}
        .table-section { animation:fadeInUp 0.4s ease 0.28s both; }

        @keyframes spin { to { transform:rotate(360deg); } }
        .spin { animation:spin 0.8s linear infinite; display:inline-block; }

        /* ═══════════════════════════════════════════
           RESPONSIVE — ทุกขนาดหน้าจอ
        ═══════════════════════════════════════════ */

        /* Large desktop ≥1400px */
        @media (min-width: 1400px) {
            main { padding: 32px 40px; }
            .stat-number { font-size: 42px; }
        }

        /* Desktop 1100–1399px */
        @media (max-width: 1399px) {
            main { padding: 24px 28px; }
        }

        /* Laptop 900–1099px — ย่อ sidebar */
        @media (max-width: 1099px) {
            :root { --sidebar-w: 200px; }
            main { padding: 20px 20px; }
            .stats-grid { grid-template-columns: repeat(4,1fr); gap: 10px; }
            .stat-number { font-size: 32px; }
        }

        /* Tablet landscape 768–899px — sidebar icon only */
        @media (max-width: 899px) {
            :root { --sidebar-w: 64px; }
            .sidebar { width: 64px; }
            .sidebar .sidebar-logo-text,
            .sidebar .nav-item-label,
            .sidebar .nav-badge,
            .sidebar .nav-group-label,
            .sidebar .collapse-label,
            .sidebar .sidebar-user-info { opacity: 0; pointer-events: none; }
            .sidebar .sidebar-user-card { justify-content: center; padding: 8px; }
            .sidebar .collapse-icon { transform: rotate(180deg); }
            .page-wrapper { margin-left: 64px; width: calc(100% - 64px); }
            .stats-grid { grid-template-columns: repeat(2,1fr); gap: 10px; }
            .stat-number { font-size: 30px; }
            .modal { width: 95vw; padding: 20px; }
            .detail-modal { width: 95vw; }
            .detail-body { grid-template-columns: 1fr; }
            .detail-info-card { border-right: none; border-bottom: 1px solid var(--border); }
        }

        /* Tablet portrait 600–767px */
        @media (max-width: 767px) {
            .sidebar { display: none; }
            .page-wrapper { margin-left: 0 !important; width: 100% !important; }
            .topbar { padding: 0 16px; }
            .topbar-breadcrumb { display: none; }
            main { padding: 16px; }
            .page-title { font-size: 17px; margin-bottom: 14px; }
            .stats-grid { grid-template-columns: repeat(2,1fr); gap: 10px; }
            .stat-card { padding: 14px 16px; }
            .stat-number { font-size: 28px; }
            .table-header { flex-direction: column; align-items: flex-start; gap: 10px; }
            .search-wrapper { width: 100%; }
            thead th:nth-child(5),
            tbody td:nth-child(5) { display: none; }
            .modal { width: 98vw; padding: 16px; }
            .detail-modal { width: 98vw; }
            .detail-body { grid-template-columns: 1fr; }
            .detail-info-card { border-right: none; border-bottom: 1px solid var(--border); }
            .steps-bar { padding: 12px 16px; overflow-x: auto; }
            .step-label { font-size: 9px; }
        }

        /* Mobile 0–599px */
        @media (max-width: 599px) {
            .topbar { padding: 0 12px; height: 52px; }
            .btn-create { padding: 7px 14px; font-size: 12px; }
            main { padding: 12px; }
            .page-title { font-size: 15px; }
            .stats-grid { grid-template-columns: repeat(2,1fr); gap: 8px; }
            .stat-card { padding: 12px 14px; border-radius: 10px; }
            .stat-number { font-size: 26px; }
            .stat-label { font-size: 11px; }
            .table-title { font-size: 13px; }
            thead th { font-size: 10px; padding: 10px 10px; }
            tbody td { padding: 10px 10px; font-size: 12px; }
            thead th:nth-child(4),
            tbody td:nth-child(4),
            thead th:nth-child(5),
            tbody td:nth-child(5),
            thead th:nth-child(7),
            tbody td:nth-child(7) { display: none; }
            .detail-claim-id { font-size: 15px; }
            .modal { padding: 14px; }
            .form-row { grid-template-columns: 1fr; }
            .detail-actions { flex-wrap: wrap; gap: 8px; padding: 12px 16px; }
            .btn-action-cancel, .btn-action-accept, .btn-action-finish, .btn-cancel { padding: 8px 14px; font-size: 12px; }
        }
    </style>
</head>
<body>

<script>
    const GAS_URL = 'https://script.google.com/macros/s/AKfycby0JavxoTk3YJiq87DuioTDGIpSv8G-WGRKuyCsyuYcFdBtM3f83Do-i6v8LznbFlHA/exec';
    const MAX_FILE_MB = 10;
    const MAX_IMAGES  = 10;
</script>

<!-- ═══════════════════════════════════════════
     SIDEBAR
═══════════════════════════════════════════ -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
            </svg>
        </div>
        <div class="sidebar-logo-text">
            <div class="sidebar-logo-main">Return System</div>
            <div class="sidebar-logo-sub">ระบบจัดการเคลม</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <!-- USER CARD -->
        <div class="sidebar-user-card" id="sidebar-user-card">
            <div class="sidebar-user-avatar" id="sidebar-user-avatar">G</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name" id="sidebar-user-name">Guest</div>
                <div class="sidebar-user-role" id="sidebar-user-role">ผู้ใช้งาน</div>
            </div>
        </div>

        <div class="nav-group-label">เมนูหลัก</div>

        <a class="nav-item active" onclick="setActive(this);filterTable('all')" href="#">
            <span class="nav-item-icon">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
            </span>
            <span class="nav-item-label">ภาพรวม</span>
            <div class="nav-tooltip">ภาพรวม</div>
        </a>

        <a class="nav-item" onclick="setActive(this);filterTable('all')" href="#">
            <span class="nav-item-icon">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </span>
            <span class="nav-item-label">รายการเคส</span>
            <span class="nav-badge" id="sidebar-total-badge">0</span>
            <div class="nav-tooltip">รายการเคส</div>
        </a>

        <a class="nav-item" onclick="setActive(this);filterTable('processing')" href="#">
            <span class="nav-item-icon">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </span>
            <span class="nav-item-label">รอดำเนินการ</span>
            <span class="nav-badge" id="sidebar-pending-badge" style="background:rgba(245,166,35,0.25);color:#f5a623;">0</span>
            <div class="nav-tooltip">รอดำเนินการ</div>
        </a>

        <a class="nav-item admin-only-menu" onclick="setActive(this);filterTable('accept')" href="#" style="display:none;">
            <span class="nav-item-icon">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </span>
            <span class="nav-item-label">จัดของรอส่ง</span>
            <span class="nav-badge" id="sidebar-prepare-badge" style="background:rgba(255,255,255,0.15);color:#fff;">0</span>
            <div class="nav-tooltip">จัดของรอส่ง</div>
        </a>

        <a class="nav-item" onclick="setActive(this);filterTable('finish')" href="#">
            <span class="nav-item-icon">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </span>
            <span class="nav-item-label">ปิดแล้ว</span>
            <span class="nav-badge" id="sidebar-closed-badge" style="background:rgba(10,122,75,0.2);color:#0a7a4b;">0</span>
            <div class="nav-tooltip">ปิดแล้ว</div>
        </a>

        <a class="nav-item" onclick="setActive(this);filterTable('cancel')" href="#">
            <span class="nav-item-icon">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </span>
            <span class="nav-item-label">ปฏิเสธ</span>
            <span class="nav-badge" id="sidebar-rejected-badge" style="background:rgba(229,57,53,0.18);color:#e53935;">0</span>
            <div class="nav-tooltip">ปฏิเสธ</div>
        </a>

    </nav>

    <div class="sidebar-footer">
        <button class="sidebar-collapse-btn" onclick="toggleSidebar()">
            <svg class="collapse-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            <span class="collapse-label">ย่อเมนู</span>
        </button>
    </div>
</aside>

<!-- ═══════════════════════════════════════════
     PAGE WRAPPER
═══════════════════════════════════════════ -->
<div class="page-wrapper" id="page-wrapper">

    <!-- TOP BAR -->
    <header class="topbar">
        <div class="topbar-left">
            <span class="topbar-breadcrumb">
                Return System <span class="topbar-sep">›</span> <strong>ภาพรวม</strong>
            </span>
        </div>
        <div class="topbar-right">
            <button class="btn-create" id="btn-create-case" onclick="openModal()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                สร้างเคสใหม่
            </button>
        </div>
    </header>

    <!-- MAIN -->
    <main>
        <div class="page-title">รายการเคลม / คืนสินค้า</div>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-number" id="stat-total">0</div>
                <div class="stat-label">ทั้งหมด</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-number" id="stat-pending">0</div>
                <div class="stat-label">รอดำเนินการ</div>
            </div>

            <div class="stat-card closed">
                <div class="stat-number" id="stat-closed">0</div>
                <div class="stat-label">ปิดแล้ว</div>
            </div>
            <div class="stat-card rejected">
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
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
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
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
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
</div>

<!-- ═══════════════════════════════════════════
     CREATE MODAL
═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modal" onclick="closeOnOverlay(event)">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1428A0" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="modal-title">สร้างเคสใหม่</div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">รหัส PO <span style="color:#e53935;">*</span></label>
            <div class="po-search-row">
                <input id="f-ponum" class="form-input" type="text" placeholder="กรอก PO Number" onkeydown="if(event.key==='Enter') fetchPODocuNo()">
                <button class="btn-po-search" type="button" onclick="fetchPODocuNo()">ค้นหา</button>
            </div>
            <div id="docu-result"></div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">ร้านค้า / VEN <span style="color:#e53935;">*</span></label>
                <input id="f-customer" class="form-input" type="text" placeholder="ร้านค้า">
            </div>
            <div class="form-group">
                <label class="form-label">เบอร์โทรศัพท์</label>
                <input id="f-phone" class="form-input" type="tel" placeholder="ตามรหัส VEN">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">สินค้า <span style="color:#e53935;">*</span></label>
            <div id="f-product-placeholder" style="padding:14px 16px;text-align:center;border:1px dashed var(--border);border-radius:8px;font-size:13px;color:var(--text-muted);">ค้นหา PO เพื่อโหลดรายการสินค้า</div>
            <div id="f-product-table-wrapper" style="display:none;">
                <div style="border-radius:8px;overflow:hidden;max-height:320px;overflow-y:auto;">
                    <table style="width:100%;border-collapse:collapse;table-layout:auto;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;">
                        <thead style="position:sticky;top:0;z-index:2;">
                            <tr style="background:#f4f6ff;border-bottom:2px solid #d0d0d0;">
                                <th style="width:1%;padding:9px 10px;text-align:center;border-right:1px solid #e0e0e0;">
                                    <input type="checkbox" id="chk-all" onchange="toggleAllProducts(this)" style="width:15px;height:15px;cursor:pointer;accent-color:var(--samsung-blue);">
                                </th>
                                <th style="padding:9px 14px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;border-right:1px solid #e0e0e0;">ชื่อสินค้า</th>
                                <th style="width:1%;padding:9px 10px;text-align:center;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap;border-right:1px solid #e0e0e0;">จำนวน</th>
                                <th style="width:1%;padding:9px 14px;text-align:left;font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap;">Invoice</th>
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
            <textarea id="f-note" class="form-input" placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)" style="height:75px;resize:vertical;line-height:1.6;"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">
                รูปภาพประกอบ
                <span style="font-size:11px;color:var(--text-muted);font-weight:400;text-transform:none;letter-spacing:0;">(สูงสุด <span id="lbl-max-img">10</span> รูป · ไม่เกิน <span id="lbl-max-mb">10</span> MB/รูป)</span>
            </label>
            <div class="image-upload-zone" id="upload-zone" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                <input type="file" id="f-images" accept="image/*" multiple onchange="handleImageSelect(this.files)">
                <div class="upload-zone-icon" style="margin-bottom:6px;color:var(--text-muted);">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
                <div class="upload-zone-text">คลิกเพื่อเลือกรูป หรือลากวางไฟล์ที่นี่</div>
                <div class="upload-zone-sub">JPG, PNG, WEBP, GIF · รองรับหลายไฟล์พร้อมกัน</div>
            </div>
            <div id="upload-status" class="upload-status-bar"></div>
            <div class="image-preview-grid" id="image-preview-grid"></div>
        </div>

        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">ยกเลิก</button>
            <button type="button" class="btn-submit" id="btn-submit-case" onclick="submitNewCase(event)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 11l3 3L22 4"/></svg>
                สร้างเคส
            </button>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     DETAIL MODAL
═══════════════════════════════════════════ -->
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
        <div class="detail-photos-section">
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0;border-top:1px solid var(--border);">
                <!-- ช่อง 1: รูปภาพประกอบ -->
                <div style="padding:14px 18px;border-right:1px solid var(--border);">
                    <div class="detail-section-title" style="margin-bottom:8px;">รูปภาพประกอบ <span id="d-photo-count" style="font-weight:400;font-size:11px;color:var(--text-muted);text-transform:none;letter-spacing:0;"></span></div>
                    <div id="d-photos-grid" class="detail-photos-grid"></div>
                </div>
                <!-- ช่อง 2: หลักฐาน -->
                <div style="padding:14px 18px;border-right:1px solid var(--border);">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                        <div class="detail-section-title" style="margin-bottom:0;">หลักฐาน <span id="d-evidence-count" style="font-weight:400;font-size:11px;color:var(--text-muted);text-transform:none;letter-spacing:0;"></span></div>
                        <div id="d-evidence-upload-btn"></div>
                    </div>
                    <div id="d-evidence-grid" class="detail-photos-grid"></div>
                </div>
                <!-- ช่อง 3: แพ็คและบรรจุภัณฑ์ -->
                <div style="padding:14px 18px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                        <div class="detail-section-title" style="margin-bottom:0;">แพ็คและบรรจุภัณฑ์ <span id="d-pack-count" style="font-weight:400;font-size:11px;color:var(--text-muted);text-transform:none;letter-spacing:0;"></span></div>
                        <div id="d-pack-upload-btn"></div>
                    </div>
                    <div id="d-pack-grid" class="detail-photos-grid"></div>
                </div>
            </div>
        </div>
        <div class="detail-actions" id="d-actions"></div>
    </div>
</div>

<!-- LIGHTBOX -->
<div class="lightbox-overlay" id="lightbox" onclick="closeLightboxOnOverlay(event)">
    <button class="lightbox-close-btn" onclick="closeLightbox()">✕</button>
    <img class="lightbox-img" id="lb-img" src="" alt="">
    <div class="lightbox-nav">
        <button class="lightbox-btn" onclick="lbPrev()">&#8592;</button>
        <span class="lightbox-counter" id="lb-counter"></span>
        <button class="lightbox-btn" onclick="lbNext()">&#8594;</button>
    </div>
    <div class="lightbox-caption" id="lb-caption"></div>
</div>

<script>
// ─── USER INFO ────────────────────────────────────────────────────
var adminUsers = ['test101', 'หยก', 'admin01', 'superadmin'];
var currentUser = (new URLSearchParams(window.location.search)).get('create_by') || 'Guest';
var isAdmin = adminUsers.indexOf(currentUser) !== -1;

(function initUser() {
    document.getElementById('sidebar-user-name').textContent = currentUser;
    document.getElementById('sidebar-user-avatar').textContent = currentUser.charAt(0).toUpperCase();
    var roleEl = document.getElementById('sidebar-user-role');
    if (isAdmin) {
        roleEl.textContent = '⭐ ผู้ดูแลระบบ';
        roleEl.classList.add('is-admin');
        // ซ่อนปุ่มสร้างเคสใหม่
        var createBtn = document.getElementById('btn-create-case');
        if (createBtn) createBtn.style.display = 'none';
        // แสดงเมนู admin
        document.querySelectorAll('.admin-only-menu').forEach(function(el) {
            el.style.display = '';
        });
    } else {
        roleEl.textContent = '👤 ผู้ใช้งาน';
    }
})();

// ─── SIDEBAR TOGGLE ────────────────────────────────────────────────
function toggleSidebar() {
    const sb = document.getElementById('sidebar');
    const pw = document.getElementById('page-wrapper');
    sb.classList.toggle('collapsed');
    pw.classList.toggle('sidebar-collapsed');
    localStorage.setItem('sidebar-collapsed', sb.classList.contains('collapsed') ? '1' : '0');
}

function setActive(el) {
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    el.classList.add('active');
}

// restore state
if (localStorage.getItem('sidebar-collapsed') === '1') {
    document.getElementById('sidebar').classList.add('collapsed');
    document.getElementById('page-wrapper').classList.add('sidebar-collapsed');
}

// ─── STATE ────────────────────────────────────────────────────────
let cases           = {};
let currentDetailId = null;
let currentDocuNo   = null;
let poItems         = [];
let localInvoiceData = [];
let pendingFiles    = [];
let lbImages = [];
let lbIndex  = 0;

const stepLabels = ['รับแจ้ง','ตรวจสอบ','อนุมัติ','จัดเตรียมสินค้า','ปิดเคส'];
const statusMap = {
    processing: { label:'กำลังดำเนินการ', cls:'badge-processing', step:2 },
    accept:     { label:'อนุมัติแล้ว',    cls:'badge-accept',     step:3 },
    finish:     { label:'เสร็จสิ้น',       cls:'badge-closed',     step:5 },
    cancel:     { label:'ยกเลิก',          cls:'badge-rejected',   step:1 },
};
const CSRF = () => document.head.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.getElementById('lbl-max-img').textContent = MAX_IMAGES;
document.getElementById('lbl-max-mb').textContent  = MAX_FILE_MB;

// ─── LOAD FROM DB ──────────────────────────────────────────────────
async function loadCasesFromDB() {
    try {
        const res = await fetch('/return/list', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF() }
        });
        const contentType = res.headers.get('content-type') || '';
        if (!res.ok || !contentType.includes('application/json')) {
            const text = await res.text();
            console.error('API ไม่ได้ return JSON:', res.status, text.substring(0, 300));
            showToast('⚠️ โหลดข้อมูลไม่สำเร็จ (status: ' + res.status + ') ดู Console สำหรับรายละเอียด');
            renderTable();
            return;
        }
        const list = await res.json();
        cases = {};
        list.forEach(c => {
            const sm = statusMap[c.status] ?? statusMap.processing;
            let productStr = '';
            if (Array.isArray(c.products) && c.products.length) {
                productStr = c.products.map(p => `${(p.product_name||'').trim()} (จำนวน: ${p.quantity??0})`).join('\n');
            } else if (c.product) {
                productStr = c.product.includes('|') ? c.product.split('|').join('\n') : c.product;
            }
            cases[c.id] = {
                id:c.id, po:c.po, customer:c.customer,
                product:productStr, products:c.products||[],
                reason:c.reason, note:c.note||'-', fix:'-',
                date:c.date, status:c.status, step:sm.step,
                images:c.images||[],
                images_evidence:c.images_evidence||[],
                images_pack:c.images_pack||[],
                stepDates:buildStepDates(c.date,sm.step),
                stepBy:['ฝ่ายบริการ','ช่างเทคนิค','ผู้จัดการ','ฝ่ายจัดส่ง','ฝ่ายบริการ'],
                stepDesc:['รับแจ้งเรื่องจากลูกค้า','ตรวจสอบสินค้าเรียบร้อย','อนุมัติการดำเนินการ','จัดเตรียมสินค้า / ซ่อม / เปลี่ยนสินค้า','ปิดเคสเรียบร้อย'],
            };
        });
        renderTable();
    } catch(err) { console.error('โหลดข้อมูลไม่สำเร็จ:',err); renderTable(); }
}

function buildStepDates(date, step) {
    const d = date ?? new Date().toISOString().slice(0,10);
    const arr = [null,null,null,null,null];
    for (let i=0; i<step&&i<5; i++) arr[i]=d;
    return arr;
}

// ─── IMAGE HELPERS ────────────────────────────────────────────────
function fileToBase64(file) {
    return new Promise((resolve,reject) => { const r=new FileReader(); r.onload=()=>resolve(r.result.split(',')[1]); r.onerror=reject; r.readAsDataURL(file); });
}
function compressImage(file, maxPx=1600, quality=0.85) {
    return new Promise(resolve => {
        const img=new Image(); img.onload=()=>{
            let w=img.width,h=img.height;
            if(w<=maxPx&&h<=maxPx){resolve(file);return;}
            const scale=Math.min(maxPx/w,maxPx/h); w=Math.round(w*scale); h=Math.round(h*scale);
            const canvas=document.createElement('canvas'); canvas.width=w; canvas.height=h;
            canvas.getContext('2d').drawImage(img,0,0,w,h);
            canvas.toBlob(blob=>resolve(new File([blob],file.name,{type:'image/jpeg'})),'image/jpeg',quality);
        }; img.src=URL.createObjectURL(file);
    });
}
async function uploadOneImage(file, customFilename, onProgress) {
    if(typeof customFilename==='function'){onProgress=customFilename;customFilename=null;}
    const compressed=await compressImage(file,1200,0.85);
    const base64=await fileToBase64(compressed);
    if(onProgress) onProgress(30);
    const filename=customFilename||(file.name.replace(/[^a-zA-Z0-9._-]/g,'_'));
    const res=await fetch('/return/upload-image',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF()},body:JSON.stringify({image:base64,filename,mimeType:compressed.type||'image/jpeg',gasUrl:GAS_URL})});
    if(onProgress) onProgress(90);
    const data=await res.json();
    if(!data.success) throw new Error(data.error||'Upload failed');
    if(onProgress) onProgress(100);
    return {fileId:data.fileId,viewUrl:data.viewUrl,thumbUrl:data.thumbUrl,filename};
}

function renderPreviews() {
    document.getElementById('image-preview-grid').innerHTML=pendingFiles.map((item,idx)=>`
        <div class="preview-item ${item.status==='done'?'done-upload':item.status==='error'?'error-upload':''}" id="prev-${idx}">
            <img src="${item.previewUrl}" alt="${item.file.name}">
            ${item.status==='uploading'?`<div class="upload-progress"><div class="prog-bar-bg"><div class="prog-bar-fill" id="prog-${idx}" style="width:${item.progress||0}%"></div></div><div class="prog-text">${item.progress||0}%</div></div>`:''}
            ${item.status==='pending'||item.status==='error'?`<button class="remove-btn" onclick="removeImage(${idx})">✕</button>`:''}
        </div>`).join('');
}
function removeImage(idx) { URL.revokeObjectURL(pendingFiles[idx].previewUrl); pendingFiles.splice(idx,1); renderPreviews(); updateUploadStatus(); }
function updateUploadStatus() {
    const bar=document.getElementById('upload-status');
    const total=pendingFiles.length, done=pendingFiles.filter(f=>f.status==='done').length, errors=pendingFiles.filter(f=>f.status==='error').length, uploading=pendingFiles.filter(f=>f.status==='uploading').length;
    if(!total){bar.style.display='none';return;}
    if(uploading>0){bar.className='upload-status-bar uploading';bar.innerHTML=`<span class="spin">⟳</span> กำลังอัปโหลด ${uploading} รูป...`;}
    else if(errors>0&&done+errors===total){bar.className='upload-status-bar error';bar.innerHTML=`⚠️ อัปโหลดล้มเหลว ${errors} รูป`;}
    else if(done===total&&total>0){bar.className='upload-status-bar done';bar.innerHTML=`✓ อัปโหลดสำเร็จทั้งหมด ${done} รูป`;}
    else{bar.className='upload-status-bar uploading';bar.innerHTML=`<span class="spin">⟳</span> รอดำเนินการ...`;}
}
function handleImageSelect(files) { addFiles(Array.from(files)); document.getElementById('f-images').value=''; }
function addFiles(files) {
    const remaining=MAX_IMAGES-pendingFiles.length; if(remaining<=0){showToast(`⚠️ เพิ่มได้สูงสุด ${MAX_IMAGES} รูป`);return;}
    const toAdd=files.slice(0,remaining);
    toAdd.forEach(file=>{
        if(!file.type.startsWith('image/')){showToast(`⚠️ ${file.name} ไม่ใช่รูปภาพ`);return;}
        if(file.size>MAX_FILE_MB*1024*1024){showToast(`⚠️ ${file.name} ใหญ่เกิน ${MAX_FILE_MB}MB`);return;}
        pendingFiles.push({file,previewUrl:URL.createObjectURL(file),status:'pending',progress:0,result:null});
    });
    if(files.length>remaining) showToast(`⚠️ เพิ่มได้อีก ${remaining} รูป`);
    renderPreviews(); updateUploadStatus();
}
function handleDragOver(e){e.preventDefault();document.getElementById('upload-zone').classList.add('dragover');}
function handleDragLeave(e){document.getElementById('upload-zone').classList.remove('dragover');}
function handleDrop(e){e.preventDefault();document.getElementById('upload-zone').classList.remove('dragover');addFiles(Array.from(e.dataTransfer.files).filter(f=>f.type.startsWith('image/')));}

// ─── INVOICE HELPERS ──────────────────────────────────────────────
function mergeInvoices(dbItems){const map=new Map();dbItems.forEach(item=>{const key=`${item.name.trim().toUpperCase()}_${parseFloat(item.quantity)}`;if(!map.has(key)){map.set(key,item);return;}const ex=map.get(key);const ed=ex.date_invoice?new Date(ex.date_invoice):new Date(0);const nd=item.date_invoice?new Date(item.date_invoice):new Date(0);if(nd>ed)map.set(key,item);});return Array.from(map.values());}
function matchProductsWithInvoices(dbItems,apiItems){if(!dbItems||!dbItems.length)return apiItems.map((a,i)=>({apiItem:a,dbItems:[],apiIndex:i}));const usedDb=new Set(),apiToDb=new Map(),scores=[];dbItems.forEach((db,di)=>{const dn=db.name.trim().toUpperCase();apiItems.forEach((api,ai)=>{const an=api.GoodName.trim().toUpperCase();let score=an===dn?100000:0;if(!score){let maxLen=0;for(let i=0;i<dn.length;i++)for(let j=i+1;j<=dn.length;j++){const s=dn.substring(i,j);if(an.includes(s)&&s.length>maxLen)maxLen=s.length;}score=maxLen*1000-Math.abs(an.length-dn.length);}scores.push({di,ai,score});});});scores.sort((a,b)=>b.score-a.score);scores.forEach(({di,ai})=>{if(usedDb.has(di))return;if(!apiToDb.has(ai))apiToDb.set(ai,{apiItem:apiItems[ai],dbItems:[]});apiToDb.get(ai).dbItems.push(dbItems[di]);usedDb.add(di);});const result=[];apiToDb.forEach((v,ai)=>result.push({apiItem:v.apiItem,dbItems:mergeInvoices(v.dbItems),apiIndex:ai}));apiItems.forEach((a,ai)=>{if(!apiToDb.has(ai))result.push({apiItem:a,dbItems:[],apiIndex:ai});});return result.sort((a,b)=>a.apiIndex-b.apiIndex);}
function formatDateThai(d){if(!d)return'-';const m=['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];const dt=new Date(d);return isNaN(dt)?'-':`${dt.getDate()} ${m[dt.getMonth()]} ${dt.getFullYear()}`;}
function buildInvoiceCards(dbItems){if(!dbItems||!dbItems.length)return '<span style="font-size:11px;color:var(--text-muted);">-</span>';return`<div class="invoice-cards-wrapper">${dbItems.map(item=>`<div class="invoice-card"><span class="invoice-card-num">📄 ${item.invoice||'-'}</span><span style="font-size:10px;color:var(--text-secondary);">📅 ${formatDateThai(item.date_invoice)} · จำนวน: <strong style="color:var(--samsung-blue);">${parseFloat(item.quantity||0)}</strong></span></div>`).join('')}</div>`;}

// ─── FETCH PO ─────────────────────────────────────────────────────
async function fetchPODocuNo() {
    let poNum=document.getElementById('f-ponum').value.trim().replace(/^PO/i,'');
    const docuResult=document.getElementById('docu-result');
    if(!poNum){docuResult.innerHTML=`<div class="docu-error">กรุณากรอก PO Number</div>`;return;}
    docuResult.innerHTML=`<div style="margin-top:8px;font-size:13px;color:#999;">⏳ กำลังโหลด...</div>`;
    localInvoiceData=[];
    try {
        const [erpRes,localRes]=await Promise.allSettled([fetch(`/api/po-detail?PONum=${encodeURIComponent(poNum)}`),fetch(`/pooutside/check?ponum=${encodeURIComponent(poNum)}`)]);
        if(erpRes.status!=='fulfilled') throw new Error('ERP API failed');
        const data=await erpRes.value.json();
        const docuNo=data?.DocuNo;
        if(!docuNo){currentDocuNo=null;docuResult.innerHTML=`<div class="docu-error">❌ ไม่พบข้อมูล PO นี้</div>`;return;}
        currentDocuNo=docuNo;
        document.getElementById('f-customer').value=data?.VendorName??'';
        document.getElementById('f-phone').value=data?.ContTel??'';
        if(localRes.status==='fulfilled'){const ld=await localRes.value.json();if(ld?.exists&&Array.isArray(ld?.data)&&ld.data.length)localInvoiceData=ld.data;}
        renderProductTable(data?.ms_podt??[]);
        docuResult.innerHTML='';
    } catch(err){currentDocuNo=null;docuResult.innerHTML=`<div class="docu-error">เกิดข้อผิดพลาด: ${err.message}</div>`;}
}

// ─── PRODUCT TABLE ────────────────────────────────────────────────
function renderProductTable(items){
    poItems=items;
    const tbody=document.getElementById('f-product-tbody'),wrapper=document.getElementById('f-product-table-wrapper'),ph=document.getElementById('f-product-placeholder');
    if(!items||!items.length){wrapper.style.display='none';ph.style.display='block';ph.textContent='ไม่มีรายการสินค้าใน PO นี้';return;}
    const matched=matchProductsWithInvoices(localInvoiceData,items);
    tbody.innerHTML=matched.map(({apiItem:item,dbItems,apiIndex:ai})=>{
        const qty=parseFloat(item.GoodQty2??0);
        const name=(item.GoodName??'').replace(/<<[^>]*>>/g,'').trim();
        return`<tr id="prod-row-${ai}" style="border-bottom:1px solid #e0e0e0;">
            <td style="padding:10px;text-align:center;vertical-align:middle;border-right:1px solid #e0e0e0;width:1%;"><input type="checkbox" class="prod-chk" data-idx="${ai}" onchange="updateProductSelection()" style="width:15px;height:15px;cursor:pointer;accent-color:var(--samsung-blue);"></td>
            <td style="padding:10px 14px;font-size:13px;line-height:1.6;white-space:normal;word-break:break-word;border-right:1px solid #e0e0e0;vertical-align:middle;"><div style="font-weight:600;">${name}</div></td>
            <td style="padding:8px;text-align:center;vertical-align:middle;width:1%;border-right:1px solid #e0e0e0;"><div style="display:flex;flex-direction:column;align-items:center;gap:2px;"><input type="number" class="prod-qty-display" data-idx="${ai}" data-qty="${qty}" data-max="${qty}" value="${qty}" min="1" max="${qty}" oninput="validateQty(this)" style="width:64px;padding:5px 6px;font-family:'Noto Sans Thai',sans-serif;font-size:13px;font-weight:700;color:var(--samsung-blue);text-align:center;border:1.5px solid #c8d4f8;border-radius:6px;outline:none;background:#f0f4ff;"><span style="font-size:10px;color:var(--text-muted);">max: ${qty}</span></div></td>
            <td style="padding:6px 8px;vertical-align:middle;text-align:center;">${buildInvoiceCards(dbItems)}</td>
        </tr>`;
    }).join('');
    ph.style.display='none'; wrapper.style.display='block';
    document.getElementById('chk-all').checked=false; updateProductSelection();
}
function validateQty(input){let val=parseFloat(input.value);const max=parseFloat(input.dataset.max);if(isNaN(val)||val<1){input.value=1;val=1;}if(val>max){input.value=max;val=max;}input.style.borderColor=(val<max)?'#f5a623':'#c8d4f8';}
function toggleAllProducts(chk){document.querySelectorAll('.prod-chk').forEach(c=>c.checked=chk.checked);updateProductSelection();}
function updateProductSelection(){const chks=document.querySelectorAll('.prod-chk');const total=chks.length,checked=[...chks].filter(c=>c.checked).length;const master=document.getElementById('chk-all');master.checked=checked===total;master.indeterminate=checked>0&&checked<total;document.getElementById('f-product-selected-count').textContent=checked>0?`เลือกแล้ว ${checked} จาก ${total} รายการ`:'ยังไม่ได้เลือกสินค้า';}
function resetProductTable(){poItems=[];localInvoiceData=[];document.getElementById('f-product-table-wrapper').style.display='none';const ph=document.getElementById('f-product-placeholder');ph.style.display='block';ph.textContent='ค้นหา PO เพื่อโหลดรายการสินค้า';document.getElementById('f-product-tbody').innerHTML='';document.getElementById('f-product-selected-count').textContent='';}

// ─── RENDER TABLE ─────────────────────────────────────────────────
function renderTable() {
    const tbody=document.getElementById('cases-tbody'),all=Object.values(cases);
    if(!all.length){
        tbody.innerHTML=`<tr id="empty-row"><td colspan="7"><div class="empty-state"><div class="empty-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#bbb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><div class="empty-title">ยังไม่มีรายการเคส</div><div class="empty-sub">กดปุ่ม "สร้างเคสใหม่" เพื่อเพิ่มรายการแรก</div></div></td></tr>`;
    } else {
        tbody.innerHTML=all.map(c=>{
            const sm=statusMap[c.status]??statusMap.processing;
            const pct=Math.round((c.step/5)*100),barCls=c.status==='finish'?'blue':'orange';
            return`<tr data-status="${c.status}" onclick="openDetail('${c.id}')" style="cursor:pointer;">
                <td><span class="claim-id">${c.po||c.id}</span></td>
                <td><span class="customer-name">${c.customer}</span></td>
                <td><div class="product-list">${(()=>{if(Array.isArray(c.products)&&c.products.length)return c.products.map(p=>`<div class="product-item"><span>${(p.product_name||'').trim()}</span><span class="prod-qty">×${parseFloat(p.quantity??0)}</span></div>`).join('');return c.product.split('\n').map(p=>{const m=p.trim().match(/^(.+?)\s*\(จำนวน:\s*([\d.]+)\)$/);return m?`<div class="product-item"><span>${m[1].trim()}</span><span class="prod-qty">×${parseFloat(m[2])}</span></div>`:(p.trim()?`<div class="product-item"><span>${p.trim()}</span></div>`:'');}).join('');})()}</div></td>
                <td><span class="reason-text">${c.reason}</span></td>
                <td><div class="progress-wrapper"><div class="progress-bar-bg"><div class="progress-bar-fill ${barCls}" style="width:${pct}%"></div></div><span class="progress-text">${c.step}/5</span></div></td>
                <td style="white-space:nowrap;"><span class="badge ${sm.cls}">${sm.label}</span></td>
                <td><span class="date-text">${c.date}</span></td>
            </tr>`;
        }).join('');
    }
    updateStats();
    filterTable(currentFilter);
}

function updateStats() {
    const all=Object.values(cases);
    const pending=all.filter(c=>c.status==='processing').length;
    const accepted=all.filter(c=>c.status==='accept').length;
    const closed=all.filter(c=>c.status==='finish').length;
    const rejected=all.filter(c=>c.status==='cancel').length;
    document.getElementById('stat-total').textContent=all.length;
    document.getElementById('stat-pending').textContent=pending;
    document.getElementById('stat-closed').textContent=closed;
    document.getElementById('stat-rejected').textContent=rejected;
    document.getElementById('footer-count').textContent=`แสดง ${all.length} รายการ จากทั้งหมด ${all.length} รายการ`;
    document.getElementById('footer-updated').textContent=all.length>0?`อัปเดตล่าสุด: ${new Date().toLocaleDateString('th-TH',{day:'numeric',month:'short',year:'numeric'})}`:'—';
    document.getElementById('sidebar-total-badge').textContent=all.length;
    document.getElementById('sidebar-pending-badge').textContent=pending;
    document.getElementById('sidebar-closed-badge').textContent=closed;
    document.getElementById('sidebar-rejected-badge').textContent=rejected;
    var prepareBadge=document.getElementById('sidebar-prepare-badge');
    if(prepareBadge) prepareBadge.textContent=accepted;
}

// ─── MODAL OPEN/CLOSE ─────────────────────────────────────────────
function openModal(){currentDocuNo=null;pendingFiles=[];renderPreviews();document.getElementById('upload-status').style.display='none';document.getElementById('modal').classList.add('active');}
function closeModal(){document.getElementById('modal').classList.remove('active');['f-ponum','f-customer','f-phone','f-note'].forEach(id=>document.getElementById(id).value='');resetProductTable();document.getElementById('f-reason').value='';document.getElementById('docu-result').innerHTML='';pendingFiles.forEach(f=>URL.revokeObjectURL(f.previewUrl));pendingFiles=[];renderPreviews();document.getElementById('upload-status').style.display='none';currentDocuNo=null;}
function closeOnOverlay(e){if(e.target===document.getElementById('modal'))closeModal();}

// ─── SUBMIT ───────────────────────────────────────────────────────
async function submitNewCase(e) {
    if(e) e.preventDefault();
    const customer=document.getElementById('f-customer').value.trim(),reason=document.getElementById('f-reason').value,note=document.getElementById('f-note').value.trim();
    if(!currentDocuNo){alert('กรุณาค้นหา PO ก่อน');return;}
    if(!customer||!reason){alert('กรุณากรอกข้อมูลให้ครบ');return;}
    const selectedItems=[];
    document.querySelectorAll('.prod-chk').forEach(chk=>{if(!chk.checked)return;const idx=parseInt(chk.dataset.idx,10);const item=poItems[idx];if(!item)return;const name=(item.GoodName??'').replace(/<<[^>]*>>/g,'').trim();const qtyInput=document.querySelector(`.prod-qty-display[data-idx="${idx}"]`);const qty=qtyInput?parseFloat(qtyInput.value)||0:parseFloat(item.GoodQty2??0);selectedItems.push({goodName:name,qty,invoice:''});});
    if(!selectedItems.length){alert('กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');return;}
    const btn=document.getElementById('btn-submit-case');
    btn.disabled=true; btn.innerHTML=`<span class="spin">⟳</span> กำลังบันทึก...`;
    const pendingSnapshot=pendingFiles.filter(f=>f.status==='pending').map(f=>({...f}));
    try {
        const res=await fetch('/return/submit',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF()},body:JSON.stringify({poNum:currentDocuNo,vendor:customer,reason,note,selectedItems,images:[]})});
        const data=await res.json();
        if(!data.success){alert(`❌ ${data.message}`);return;}
        const today=new Date().toISOString().slice(0,10);
        const newCase={id:data.return_id,customer,reason,note:note||'-',fix:'-',date:today,po:currentDocuNo,product:selectedItems.map(i=>`${i.goodName} (จำนวน: ${i.qty})`).join('\n'),status:'processing',step:2,images:[],stepDates:[today,today,null,null,null],stepBy:['ฝ่ายบริการ','ช่างเทคนิค',null,null,null],stepDesc:['รับแจ้งเรื่องจากลูกค้า','ตรวจสอบสินค้าเรียบร้อย',null,null,null]};
        cases=Object.assign({[data.return_id]:newCase},cases);
        closeModal(); renderTable();
        showToast(`✅ สร้างเคส ${data.return_id} เรียบร้อยแล้ว`);
        if(pendingSnapshot.length>0) uploadImagesBackground(pendingSnapshot,data.return_id);
        else fetch('/return/'+encodeURIComponent(data.return_id)+'/update-images',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF()},body:JSON.stringify({images:[],final:true})}).catch(()=>{});
    } catch(err){alert('เกิดข้อผิดพลาด: '+err.message);}
    finally{btn.disabled=false;btn.innerHTML=`<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 11l3 3L22 4"/></svg> สร้างเคส`;}
}

async function uploadImagesBackground(snapshots,returnId) {
    const poRaw=(currentDocuNo||'').replace(/^PO/i,'')||returnId.split('-')[2]||'';
    const now=new Date(),dd=String(now.getDate()).padStart(2,'0'),mm=String(now.getMonth()+1).padStart(2,'0'),yy=String(now.getFullYear()).slice(-2);
    const dateStr=dd+'-'+mm+'-'+yy;
    if(!cases[returnId]) return;
    cases[returnId].images=[];
    let successCount=0;
    for(let i=0;i<snapshots.length;i++){
        const item=snapshots[i],seqNum=String(i+1).padStart(2,'0'),ext=item.file.name.split('.').pop().toLowerCase()||'jpg';
        try{const result=await uploadOneImage(item.file,poRaw+'_'+dateStr+'_'+seqNum+'.'+ext,null);cases[returnId].images.push(result);successCount++;const isLast=(i===snapshots.length-1);try{await fetch('/return/'+encodeURIComponent(returnId)+'/update-images',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF()},body:JSON.stringify({images:cases[returnId].images,final:isLast})});}catch(dbErr){console.warn('Save DB failed:',dbErr.message);}if(currentDetailId===returnId)refreshDetailPhotos(returnId);}catch(err){console.warn('Upload failed:',err.message);}
    }
    if(successCount>0) showToast('✅ อัปโหลดรูป '+successCount+' รูปเรียบร้อย');
}

// ─── REFRESH DETAIL PHOTOS (FIXED) ───────────────────────────────
function refreshDetailPhotos(caseId) {
    var c = cases[caseId];
    if (!c) return;
    var imgs = c.images || [];
    var photoGrid = document.getElementById('d-photos-grid');
    var photoCount = document.getElementById('d-photo-count');
    if (!photoGrid) return;
    photoCount.textContent = imgs.length > 0 ? '(' + imgs.length + ' รูป)' : '';
    if (!imgs.length) {
        photoGrid.innerHTML = '<div class="no-photos-placeholder">ไม่มีรูปภาพประกอบ</div>';
        return;
    }
    var html = '';
    for (var idx = 0; idx < imgs.length; idx++) {
        var img = imgs[idx];
        var fileId = img.fileId || '';
        if (!fileId && img.viewUrl) {
            var m = img.viewUrl.match(/\/d\/([^/]+)\//);
            if (m) fileId = m[1];
        }
        var src = fileId ? '/return/drive-image?id=' + fileId : (img.thumbUrl || img.viewUrl || '');
        var title = img.filename || ('รูป ' + (idx + 1));
        var fallbackSvg = 'data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2280%22%20height%3D%2280%22%3E%3Crect%20width%3D%2280%22%20height%3D%2280%22%20fill%3D%22%23f0f0f0%22%2F%3E%3Ctext%20x%3D%2240%22%20y%3D%2245%22%20text-anchor%3D%22middle%22%20font-size%3D%2212%22%20fill%3D%22%23999%22%3E-%3C%2Ftext%3E%3C%2Fsvg%3E';
        html += '<div class="detail-photo-thumb" onclick="openLightbox(' + idx + ',\'' + caseId + '\')" title="' + title + '">';
        html += '<img src="' + src + '" alt="' + title + '" loading="lazy" onerror="this.src=\'' + fallbackSvg + '\'">';
        html += '</div>';
    }
    photoGrid.innerHTML = html;
}

// ─── DETAIL MODAL ─────────────────────────────────────────────────
function openDetail(id){
    currentDetailId=id;var c=cases[id];if(!c)return;
    document.getElementById('d-id').textContent=c.id;
    document.getElementById('d-meta').textContent=c.customer+' · เปิดวันที่ '+c.date;
    document.getElementById('d-customer').textContent=c.customer;
    document.getElementById('d-reason').textContent=c.reason;
    document.getElementById('d-note').textContent=c.note;
    var productEl=document.getElementById('d-product');productEl.style.textAlign='left';
    if(Array.isArray(c.products)&&c.products.length){
        productEl.innerHTML=c.products.map(function(p){return'<div style="display:flex;align-items:baseline;justify-content:space-between;gap:8px;padding:5px 8px;margin-bottom:4px;background:#f8f9ff;border-left:2px solid #1428A0;border-radius:0 4px 4px 0;"><span style="font-size:12px;color:#535353;">'+(p.product_name||'').trim()+'</span><span style="flex-shrink:0;font-size:11px;font-weight:700;color:#1428A0;background:#e8ecf8;padding:1px 8px;border-radius:10px;">×'+parseFloat(p.quantity||0)+'</span></div>';}).join('');
    } else {
        productEl.innerHTML=c.product.split('\n').map(function(p){var m=p.trim().match(/^(.+?)\s*\(จำนวน:\s*([\d.]+)\)$/);return m?'<div style="display:flex;align-items:baseline;justify-content:space-between;gap:8px;padding:5px 8px;margin-bottom:4px;background:#f8f9ff;border-left:2px solid #1428A0;border-radius:0 4px 4px 0;"><span style="font-size:12px;color:#535353;">'+m[1].trim()+'</span><span style="flex-shrink:0;font-size:11px;font-weight:700;color:#1428A0;background:#e8ecf8;padding:1px 8px;border-radius:10px;">×'+parseFloat(m[2])+'</span></div>':(p.trim()?'<div style="padding:5px 8px;margin-bottom:4px;background:#f8f9ff;border-left:2px solid #1428A0;border-radius:0 4px 4px 0;font-size:12px;color:#535353;">'+p.trim()+'</div>':'');}).join('');
    }
    var sm=statusMap[c.status]||statusMap.processing;
    var badge=document.getElementById('d-badge');badge.className='badge '+sm.cls;badge.textContent=sm.label;
    document.getElementById('d-steps').innerHTML=stepLabels.map(function(label,i){var cls=i<c.step?'done':i===c.step?'active':'';var icon=i<c.step?'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>':'<span style="font-size:11px;font-weight:700;color:'+(i===c.step?'var(--samsung-blue)':'#ccc')+'">'+(i+1)+'</span>';return'<div class="step-item '+cls+'"><div class="step-circle">'+icon+'</div><div class="step-label">'+label+'</div></div>';}).join('');
    document.getElementById('d-timeline').innerHTML=stepLabels.map(function(label,i){if(i>=c.step||!c.stepDates||!c.stepDates[i])return'';return'<div class="timeline-item"><div class="timeline-dot"></div><div><div class="timeline-text">'+(c.stepDesc&&c.stepDesc[i]?c.stepDesc[i]:label)+'</div><div class="timeline-by">'+(c.stepBy&&c.stepBy[i]?c.stepBy[i]:'')+'·'+c.stepDates[i]+'</div></div></div>';}).join('');
    var imgs=c.images||[];
    var photoGrid=document.getElementById('d-photos-grid');
    var photoCount=document.getElementById('d-photo-count');
    photoCount.textContent=imgs.length>0?'('+imgs.length+' รูป)':'';
    if(!imgs.length){
        photoGrid.innerHTML='<div class="no-photos-placeholder">ไม่มีรูปภาพประกอบ</div>';
    } else {
        var html='';
        for(var idx=0;idx<imgs.length;idx++){
            var img=imgs[idx];
            var fileId=img.fileId||'';
            if(!fileId&&img.viewUrl){var m=img.viewUrl.match(/\/d\/([^/]+)\//);if(m)fileId=m[1];}
            var src=fileId?'/return/drive-image?id='+fileId:(img.thumbUrl||img.viewUrl||'');
            var title=img.filename||('รูป '+(idx+1));
            var fallbackSvg='data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2280%22%20height%3D%2280%22%3E%3Crect%20width%3D%2280%22%20height%3D%2280%22%20fill%3D%22%23f0f0f0%22%2F%3E%3Ctext%20x%3D%2240%22%20y%3D%2245%22%20text-anchor%3D%22middle%22%20font-size%3D%2212%22%20fill%3D%22%23999%22%3Eไม่พบรูป%3C%2Ftext%3E%3C%2Fsvg%3E';
            html+='<div class="detail-photo-thumb" onclick="openLightboxType('+idx+',\''+id+'\',\'images\')" title="'+title+'">';
            html+='<img src="'+src+'" alt="'+title+'" loading="lazy" onerror="this.src=\''+fallbackSvg+'\'">';
            html+='</div>';
        }
        photoGrid.innerHTML=html;
    }

    // ช่อง 2: หลักฐาน
    renderExtraPhotos(id, 'evidence', 'd-evidence-grid', 'd-evidence-count', 'd-evidence-upload-btn');
    // ช่อง 3: แพ็คและบรรจุภัณฑ์
    renderExtraPhotos(id, 'pack', 'd-pack-grid', 'd-pack-count', 'd-pack-upload-btn');

    // actions
    var actionsHtml = '<button class="btn-cancel" onclick="closeDetail()">ปิด</button>';
    if (isAdmin) {
        var c_status = c.status;
        if (c_status !== 'cancel') {
            actionsHtml += '<button class="btn-action-cancel" onclick="changeStatus(\''+id+'\',\'cancel\')">✕ ยกเลิก (Cancel)</button>';
        }
        // step processing (2) → แสดงปุ่ม อนุมัติ
        if (c_status === 'processing') {
            actionsHtml += '<button class="btn-action-accept" onclick="changeStatus(\''+id+'\',\'accept\')">✓ อนุมัติ (Accept)</button>';
        }
        // step accept (3) → แสดงแค่ จัดของพร้อมปิดเคส (ไม่มีปุ่ม Accept)
        if (c_status === 'accept') {
            actionsHtml += '<button class="btn-action-finish" onclick="changeStatus(\''+id+'\',\'finish\')">📦 จัดของพร้อมปิดเคส</button>';
        }
    }
    document.getElementById('d-actions').innerHTML = actionsHtml;
    document.getElementById('detail-modal').classList.add('active');
}

// ─── EXTRA PHOTOS (หลักฐาน / แพ็ค) ──────────────────────────────
function renderExtraPhotos(caseId, type, gridId, countId, btnId) {
    var c = cases[caseId]; if (!c) return;
    var btnEl = document.getElementById(btnId);
    // ปุ่มเพิ่มรูป เฉพาะ admin
    if (isAdmin) {
        btnEl.innerHTML = '<label class="btn-add-photo" title="เพิ่มรูป">' +
            '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>' +
            ' เพิ่มรูป' +
            '<input type="file" accept="image/*" multiple style="display:none;" onchange="handleExtraPhotoUpload(this,\'' + caseId + '\',\'' + type + '\')">' +
            '</label>';
    } else {
        btnEl.innerHTML = '';
    }
    renderExtraPhotosGrid(caseId, type);
}

async function handleExtraPhotoUpload(input, caseId, type) {
    var files = Array.from(input.files);
    if (!files.length) return;
    // clear AFTER reading files
    setTimeout(function(){ input.value = ''; }, 100);
    showToast('⏳ กำลังอัปโหลด ' + files.length + ' รูป...');
    var c = cases[caseId]; if (!c) return;
    if (!c.images_evidence) c.images_evidence = [];
    if (!c.images_pack) c.images_pack = [];
    var arr = type === 'evidence' ? c.images_evidence : c.images_pack;
    var now = new Date();
    var dateStr = String(now.getDate()).padStart(2,'0') + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getFullYear()).slice(-2);
    var successCount = 0;
    for (var i = 0; i < files.length; i++) {
        try {
            var seqNum = String(arr.length + 1).padStart(2,'0');
            var ext = files[i].name.split('.').pop().toLowerCase() || 'jpg';
            var filename = type + '_' + (c.po||caseId) + '_' + dateStr + '_' + seqNum + '.' + ext;
            var result = await uploadOneImage(files[i], filename, null);
            arr.push(result);
            successCount++;
            // render ทันทีหลังได้รูปแต่ละรูป
            renderExtraPhotosGrid(caseId, type);
            // save to DB
            try {
                await fetch('/return/' + encodeURIComponent(caseId) + '/update-images', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF() },
                    body: JSON.stringify({ images: c.images, images_evidence: c.images_evidence, images_pack: c.images_pack, final: (i === files.length - 1) })
                });
            } catch(e) { console.warn('save failed', e); }
        } catch(e) { console.warn('upload failed', e); }
    }
    if (successCount > 0) showToast('✅ อัปโหลด ' + successCount + ' รูปเรียบร้อย');
}

// render เฉพาะ grid + count ไม่แตะปุ่ม
function renderExtraPhotosGrid(caseId, type) {
    var c = cases[caseId]; if (!c) return;
    var imgs = (type === 'evidence' ? c.images_evidence : c.images_pack) || [];
    var gridId  = type === 'evidence' ? 'd-evidence-grid'  : 'd-pack-grid';
    var countId = type === 'evidence' ? 'd-evidence-count' : 'd-pack-count';
    var grid    = document.getElementById(gridId);
    var countEl = document.getElementById(countId);
    if (!grid) return;
    countEl.textContent = imgs.length > 0 ? '(' + imgs.length + ' รูป)' : '';
    var fallbackSvg = 'data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2280%22%20height%3D%2280%22%3E%3Crect%20width%3D%2280%22%20height%3D%2280%22%20fill%3D%22%23f0f0f0%22%2F%3E%3Ctext%20x%3D%2240%22%20y%3D%2245%22%20text-anchor%3D%22middle%22%20font-size%3D%2212%22%20fill%3D%22%23999%22%3E-%3C%2Ftext%3E%3C%2Fsvg%3E';
    if (!imgs.length) { grid.innerHTML = '<div class="no-photos-placeholder">ไม่มีรูปภาพ</div>'; return; }
    var html = '';
    for (var idx = 0; idx < imgs.length; idx++) {
        var img = imgs[idx];
        var fileId = img.fileId || '';
        if (!fileId && img.viewUrl) { var m = img.viewUrl.match(/\/d\/([^/]+)\//); if (m) fileId = m[1]; }
        var src = fileId ? '/return/drive-image?id=' + fileId : (img.thumbUrl || img.viewUrl || '');
        var title = img.filename || ('รูป ' + (idx + 1));
        html += '<div class="detail-photo-thumb" onclick="openLightboxType(' + idx + ',\'' + caseId + '\',\'' + type + '\')" title="' + title + '">';
        html += '<img src="' + src + '" alt="' + title + '" loading="lazy" onerror="this.src=\'' + fallbackSvg + '\'">';
        html += '</div>';
    }
    grid.innerHTML = html;
}
function closeDetail(){document.getElementById('detail-modal').classList.remove('active');currentDetailId=null;}
function closeDetailOnOverlay(e){if(e.target===document.getElementById('detail-modal'))closeDetail();}

// ─── LIGHTBOX ─────────────────────────────────────────────────────
function openLightboxType(startIdx, caseId, type) {
    var c = cases[caseId]; if (!c) return;
    var arr = type === 'evidence' ? (c.images_evidence||[]) : type === 'pack' ? (c.images_pack||[]) : (c.images||[]);
    if (!arr.length) return;
    lbImages = arr; lbIndex = startIdx; showLightboxImage();
    document.getElementById('lightbox').classList.add('active');
}
function openLightbox(startIdx,caseId){openLightboxType(startIdx,caseId,'images');}
function showLightboxImage(){
    var img=lbImages[lbIndex];
    var fileId=img.fileId||'';
    if(!fileId&&img.viewUrl){var m=img.viewUrl.match(/\/d\/([^/]+)\//);if(m)fileId=m[1];}
    document.getElementById('lb-img').src=fileId?'/return/drive-image?id='+fileId+'&sz=w1600':(img.viewUrl||'');
    document.getElementById('lb-counter').textContent=(lbIndex+1)+' / '+lbImages.length;
    document.getElementById('lb-caption').textContent=img.filename||'';
}
function lbPrev(){lbIndex=(lbIndex-1+lbImages.length)%lbImages.length;showLightboxImage();}
function lbNext(){lbIndex=(lbIndex+1)%lbImages.length;showLightboxImage();}
function closeLightbox(){document.getElementById('lightbox').classList.remove('active');}
function closeLightboxOnOverlay(e){if(e.target===document.getElementById('lightbox'))closeLightbox();}
document.addEventListener('keydown',function(e){if(!document.getElementById('lightbox').classList.contains('active'))return;if(e.key==='ArrowLeft')lbPrev();if(e.key==='ArrowRight')lbNext();if(e.key==='Escape')closeLightbox();});

// ─── FILTER ───────────────────────────────────────────────────────
var currentFilter = 'all';
function filterTable(status) {
    currentFilter = status;
    var q = document.querySelector('.search-input') ? document.querySelector('.search-input').value.toLowerCase().trim() : '';
    var shown = 0, total = 0;
    document.querySelectorAll('#cases-tbody tr:not(#empty-row)').forEach(function(r) {
        total++;
        var matchFilter = status === 'all' || r.dataset.status === status;
        var matchSearch = !q || r.textContent.toLowerCase().includes(q);
        var visible = matchFilter && matchSearch;
        r.style.display = visible ? '' : 'none';
        if (visible) shown++;
    });
    var allCount = Object.values(cases).length;
    document.getElementById('footer-count').textContent = 'แสดง ' + shown + ' รายการ จากทั้งหมด ' + allCount + ' รายการ';
}

// ─── SEARCH ───────────────────────────────────────────────────────
function searchTable(q){q=q.toLowerCase().trim();var shown=0;document.querySelectorAll('#cases-tbody tr:not(#empty-row)').forEach(function(r){var matchFilter=currentFilter==='all'||r.dataset.status===currentFilter;var matchSearch=!q||r.textContent.toLowerCase().includes(q);var visible=matchFilter&&matchSearch;r.style.display=visible?'':'none';if(visible)shown++;});var allCount=Object.values(cases).length;document.getElementById('footer-count').textContent='แสดง '+shown+' รายการ จากทั้งหมด '+allCount+' รายการ';}

// ─── TOAST ────────────────────────────────────────────────────────
function showToast(msg){var t=document.createElement('div');t.textContent=msg;Object.assign(t.style,{position:'fixed',bottom:'28px',left:'50%',transform:'translateX(-50%) translateY(12px)',background:'#1d1d1f',color:'#fff',padding:'10px 22px',borderRadius:'24px',fontSize:'13px',fontWeight:'600',zIndex:'9999',opacity:'0',transition:'all 0.3s ease',whiteSpace:'nowrap',boxShadow:'0 4px 20px rgba(0,0,0,0.2)'});document.body.appendChild(t);requestAnimationFrame(function(){t.style.opacity='1';t.style.transform='translateX(-50%) translateY(0)';});setTimeout(function(){t.style.opacity='0';t.style.transform='translateX(-50%) translateY(12px)';setTimeout(function(){t.remove();},300);},2800);}

// ─── CHANGE STATUS (ADMIN ONLY) ───────────────────────────────────
async function changeStatus(caseId, newStatus) {
    if (!isAdmin) return;
    var labels = { cancel:'ยกเลิกเคส', accept:'อนุมัติเคส', finish:'ปิดเคส' };
    if (!confirm('ยืนยัน: ' + (labels[newStatus]||newStatus) + '?')) return;
    try {
        var res = await fetch('/return/' + encodeURIComponent(caseId) + '/status', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF() },
            body: JSON.stringify({ status: newStatus, updated_by: currentUser })
        });
        var data = await res.json();
        if (!data.success) { alert('❌ ' + (data.message||'เกิดข้อผิดพลาด')); return; }
        // update local state
        var sm = statusMap[newStatus] || statusMap.processing;
        cases[caseId].status = newStatus;
        cases[caseId].step = sm.step;
        showToast('✅ เปลี่ยนสถานะเป็น "' + sm.label + '" เรียบร้อย');
        closeDetail();
        renderTable();
    } catch(err) {
        alert('เกิดข้อผิดพลาด: ' + err.message);
    }
}

// ─── INIT ─────────────────────────────────────────────────────────
loadCasesFromDB();
</script>
</body>
</html>