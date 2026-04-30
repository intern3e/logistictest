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
        .nav-item:hover { background: var(--sidebar-hover); color: #fff; }
        .nav-item.active { background: var(--sidebar-active); color: var(--sidebar-text-active); font-weight: 700; }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: -10px; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 18px;
            background: #5b8af7;
            border-radius: 0 3px 3px 0;
        }
        .nav-item-icon { width: 18px; height: 18px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .nav-item-label { flex: 1; opacity: 1; transition: opacity 0.2s; overflow: hidden; text-overflow: ellipsis; }
        .sidebar.collapsed .nav-item-label { opacity: 0; }

        .nav-badge {
            background: rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.8);
            font-size: 10px; font-weight: 700;
            padding: 2px 7px; border-radius: 10px;
            transition: opacity 0.2s; flex-shrink: 0;
        }
        .sidebar.collapsed .nav-badge { opacity: 0; }

        .nav-item .nav-tooltip {
            display: none;
            position: absolute; left: 62px; top: 50%;
            transform: translateY(-50%);
            background: #1d1d1f; color: #fff;
            font-size: 12px; font-weight: 600;
            padding: 6px 12px; border-radius: 7px;
            white-space: nowrap; pointer-events: none;
            z-index: 999; box-shadow: 0 4px 16px rgba(0,0,0,0.25);
        }
        .sidebar.collapsed .nav-item:hover .nav-tooltip { display: block; }

        .sidebar-user-card {
            display: flex; align-items: center; gap: 10px;
            margin: 10px 10px 4px; padding: 10px 12px;
            background: rgba(255,255,255,0.07);
            border-radius: 10px; border: 1px solid rgba(255,255,255,0.08);
            overflow: hidden; transition: opacity 0.2s;
        }
        .sidebar.collapsed .sidebar-user-card { justify-content: center; padding: 8px; }
        .sidebar-user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg,#5b8af7,#1428A0);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .sidebar-user-info { overflow: hidden; transition: opacity 0.2s; }
        .sidebar.collapsed .sidebar-user-info { opacity: 0; width: 0; }
        .sidebar-user-name { font-size: 13px; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-role { font-size: 10px; color: rgba(255,255,255,0.45); margin-top: 1px; white-space: nowrap; }
        .sidebar-user-role.is-admin { color: #f5a623; }

        .sidebar-divider { height: 1px; background: var(--sidebar-border); margin: 8px 20px; }

        .sidebar-footer { padding: 14px; border-top: 1px solid var(--sidebar-border); }
        .sidebar-collapse-btn {
            display: flex; align-items: center; gap: 10px;
            width: 100%; padding: 9px 10px; border-radius: 8px;
            background: none; border: none;
            color: rgba(255,255,255,0.5); cursor: pointer;
            font-family: 'Noto Sans Thai', sans-serif; font-size: 12px;
            transition: all 0.15s;
        }
        .sidebar-collapse-btn:hover { background: var(--sidebar-hover); color: #fff; }
        .collapse-icon { transition: transform 0.25s; flex-shrink: 0; }
        .sidebar.collapsed .collapse-icon { transform: rotate(180deg); }
        .collapse-label { transition: opacity 0.2s; white-space: nowrap; overflow: hidden; }
        .sidebar.collapsed .collapse-label { opacity: 0; }

        .page-wrapper {
            flex: 1; min-width: 0;
            width: calc(100% - var(--sidebar-w));
            margin-left: var(--sidebar-w);
            display: flex; flex-direction: column; min-height: 100vh;
            transition: margin-left 0.25s cubic-bezier(0.22,1,0.36,1), width 0.25s cubic-bezier(0.22,1,0.36,1);
        }
        .page-wrapper.sidebar-collapsed { margin-left: 64px; width: calc(100% - 64px); }

        .topbar {
            height: var(--topbar-h);
            background: #fff; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
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
        .btn-create:hover { background: var(--samsung-blue-hover); box-shadow: 0 4px 16px rgba(20,40,160,0.25); transform: translateY(-1px); }

        main { padding: 28px 32px; flex: 1; }

        .page-title { font-size: 20px; font-weight: 800; color: var(--text-primary); margin-bottom: 22px; letter-spacing: -0.3px; }

        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 26px; }
        .stat-card {
            background: #fff; border: 1px solid var(--border);
            border-radius: var(--radius); padding: 20px 22px;
            transition: box-shadow 0.2s, transform 0.2s;
            position: relative; overflow: hidden;
        }
        .stat-card::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; }
        .stat-card.total::after    { background: var(--samsung-blue); }
        .stat-card.pending::after  { background: #f5a623; }
        .stat-card.closed::after   { background: #0a7a4b; }
        .stat-card.rejected::after { background: #e53935; }
        .stat-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.10); transform: translateY(-2px); }

        .stat-number { font-size: 38px; font-weight: 700; line-height: 1; margin-bottom: 4px; letter-spacing: -1px; }
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
        .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; display: flex; }
        .search-input {
            width: 100%; background: var(--bg);
            border: 1px solid var(--border); border-radius: 24px;
            padding: 8px 16px 8px 38px;
            font-family: 'Noto Sans Thai', sans-serif; font-size: 13px; color: var(--text-primary);
            outline: none; transition: all 0.2s;
        }
        .search-input::placeholder { color: var(--text-muted); }
        .search-input:focus { background: #fff; border-color: var(--samsung-blue); box-shadow: 0 0 0 3px rgba(20,40,160,0.08); }

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

        tbody tr { border-bottom: 1px solid #f0f0f0; transition: background 0.12s; cursor: pointer; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f5f7ff; }
        tbody td { padding: 13px 16px; font-size: 13px; color: var(--text-primary); vertical-align: middle; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
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
        .product-item .prod-qty { flex-shrink: 0; font-size: 11px; font-weight: 700; color: var(--samsung-blue); background: #e8ecf8; padding: 1px 6px; border-radius: 10px; }
        .reason-text { color: var(--text-secondary); font-size: 12px; }

        .empty-state { padding: 60px 24px; text-align: center; }
        .empty-icon { width: 52px; height: 52px; background: var(--bg); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
        .empty-title { font-size: 14px; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; }
        .empty-sub { font-size: 13px; color: var(--text-muted); }

        .progress-wrapper { display: flex; align-items: center; gap: 10px; }
        .progress-bar-bg { width: 72px; height: 5px; background: #e5e5e5; border-radius: 3px; overflow: hidden; }
        .progress-bar-fill { height: 100%; border-radius: 3px; transition: width 0.6s cubic-bezier(0.22,1,0.36,1); }
        .progress-bar-fill.orange { background: #f5a623; }
        .progress-bar-fill.blue   { background: var(--samsung-blue); }
        .progress-text { font-size: 12px; color: var(--text-muted); font-weight: 600; font-family: 'Courier New', monospace; }

        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid; }
        .badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
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

        .photo-thumb { width: 34px; height: 34px; border-radius: 6px; object-fit: cover; border: 1px solid var(--border); cursor: pointer; transition: transform 0.15s; }
        .photo-thumb:hover { transform: scale(1.1); }
        .photo-count-badge { font-size: 10px; font-weight: 700; color: var(--samsung-blue); background: #e8ecf8; padding: 2px 6px; border-radius: 10px; }

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
        @keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

        .modal-header { display: flex; align-items: center; gap: 12px; margin-bottom: 22px; }
        .modal-icon { width: 38px; height: 38px; background: var(--samsung-blue-light); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .modal-title { font-size: 16px; font-weight: 700; }

        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: 11px; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.06em; }
        .form-input, .form-select {
            width: 100%; background: #fff; border: 1px solid var(--border);
            border-radius: 8px; padding: 10px 14px;
            color: var(--text-primary); font-family: 'Noto Sans Thai', sans-serif;
            font-size: 13px; outline: none; transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-input:focus, .form-select:focus { border-color: var(--samsung-blue); box-shadow: 0 0 0 3px rgba(20,40,160,0.10); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 22px; padding-top: 18px; border-top: 1px solid var(--border); }
        .btn-cancel { background: #fff; border: 1px solid var(--border); color: var(--text-secondary); padding: 9px 22px; border-radius: 24px; font-family: 'Noto Sans Thai', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.15s; }
        .btn-cancel:hover { border-color: #999; color: #333; }
        .btn-submit { background: var(--samsung-blue); color: #fff; border: none; padding: 9px 24px; border-radius: 24px; font-family: 'Noto Sans Thai', sans-serif; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
        .btn-submit:hover { background: var(--samsung-blue-hover); box-shadow: 0 4px 16px rgba(20,40,160,0.3); transform: translateY(-1px); }
        .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }

        .po-search-row { display: flex; gap: 8px; }
        .po-search-row .form-input { flex: 1; }
        .btn-po-search { background: var(--samsung-blue); color: #fff; border: none; padding: 10px 16px; border-radius: 8px; font-family: 'Noto Sans Thai', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; transition: all 0.2s; }
        .btn-po-search:hover { background: var(--samsung-blue-hover); }

        .docu-found { margin-top: 8px; padding: 8px 12px; background: #e6f7f0; border: 1px solid #a3e0c7; border-radius: 8px; font-size: 13px; font-weight: 600; color: #0a7a4b; }
        .docu-error { margin-top: 8px; padding: 8px 12px; background: #fff0f0; border: 1px solid #ffb3b3; border-radius: 8px; font-size: 13px; color: #e53935; }

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
        .preview-item img, .preview-item video { width: 100%; height: 100%; object-fit: cover; display: block; }
        .preview-item .video-badge {
            position: absolute; top: 4px; left: 4px;
            background: rgba(0,0,0,0.7); color: #fff;
            font-size: 9px; font-weight: 700;
            padding: 2px 6px; border-radius: 4px;
            display: flex; align-items: center; gap: 3px;
            pointer-events: none;
        }
        .preview-item .video-play-icon {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.55); color: #fff;
            width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            pointer-events: none;
        }
        .preview-item .remove-btn { position: absolute; top: 4px; right: 4px; background: rgba(0,0,0,0.65); color: #fff; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.15s; z-index: 3; }
        .preview-item .remove-btn:hover { background: rgba(229,57,53,0.9); }
        .preview-item.done-upload::after { content:'✓'; position:absolute; bottom:4px; right:4px; background:#0a7a4b; color:#fff; border-radius:50%; width:18px; height:18px; font-size:11px; display:flex; align-items:center; justify-content:center; }
        .preview-item.error-upload::after { content:'✕'; position:absolute; bottom:4px; right:4px; background:#e53935; color:#fff; border-radius:50%; width:18px; height:18px; font-size:11px; display:flex; align-items:center; justify-content:center; }

        /* ⭐ Progress bar overlay ตอนอัปโหลด - แสดงทับรูป preview */
        .preview-item .upload-progress {
            position: absolute; inset: 0;
            background: rgba(0,0,0,0.55);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 4px; padding: 6px;
            z-index: 4;
        }
        .preview-item .prog-bar-bg {
            width: 80%; height: 4px;
            background: rgba(255,255,255,0.25);
            border-radius: 2px; overflow: hidden;
        }
        .preview-item .prog-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #5b8af7, #1428A0);
            border-radius: 2px;
            transition: width 0.2s ease;
        }
        .preview-item .prog-text {
            font-size: 11px; font-weight: 700;
            color: #fff;
            text-shadow: 0 1px 2px rgba(0,0,0,0.4);
        }

        .upload-status-bar { margin-top: 8px; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; display: none; }
        .upload-status-bar.uploading { display:flex; align-items:center; gap:8px; background:#f0eeff; color:#7b61ff; border:1px solid #c4b8ff; }
        .upload-status-bar.done      { display:flex; align-items:center; gap:8px; background:#e6f7f0; color:#0a7a4b; border:1px solid #a3e0c7; }
        .upload-status-bar.error     { display:flex; align-items:center; gap:8px; background:#fff0f0; color:#e53935; border:1px solid #ffb3b3; }

        .invoice-cards-wrapper { display:flex; flex-wrap:wrap; gap:3px; justify-content:center; text-align:left; }
        .invoice-card { display:inline-flex; flex-direction:column; gap:1px; background:#f0f4ff; border:1px solid #c8d4f8; border-radius:6px; padding:4px 8px; font-size:10px; white-space:nowrap; }
        .invoice-card-num { font-weight:700; color:var(--samsung-blue); }

        .detail-modal { width: 980px; max-width: 95vw; padding: 0; overflow: hidden; border-radius: 16px; }
        .detail-header { padding: 22px 26px 18px; border-bottom: 1px solid var(--border); background: #fafafa; }
        .detail-title-row { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
        .detail-claim-id { font-size: 21px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.5px; margin-bottom: 3px; }
        .detail-meta { font-size: 12px; color: var(--text-muted); }
        .detail-header-right { display:flex; align-items:center; gap:10px; flex-shrink:0; }
        .detail-close { background: #fff; border: 1px solid var(--border); border-radius: 8px; width: 32px; height: 32px; display:flex; align-items:center; justify-content:center; cursor: pointer; color: var(--text-muted); transition: all 0.15s; }
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
        .btn-add-photo { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:16px; border:1.5px dashed var(--samsung-blue); color:var(--samsung-blue); font-size:11px; font-weight:700; background:#f0f4ff; cursor:pointer; transition:all 0.15s; font-family:'Noto Sans Thai',sans-serif; }
        .btn-add-photo:hover { background:var(--samsung-blue); color:#fff; }
        @media (max-width: 767px) { .detail-photos-section > div { grid-template-columns:1fr !important; } .detail-photos-section > div > div { border-right:none !important; border-bottom:1px solid var(--border); } }
        .detail-photos-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(76px, 1fr)); gap:8px; margin-top:10px; }
        .detail-photo-thumb { aspect-ratio:1; border-radius:8px; overflow:hidden; border:1px solid var(--border); cursor:pointer; transition:transform 0.15s, box-shadow 0.15s; position:relative; }
        .detail-photo-thumb img, .detail-photo-thumb video { width:100%; height:100%; object-fit:cover; display:block; }
        .detail-photo-thumb:hover { transform:scale(1.05); box-shadow:0 4px 16px rgba(0,0,0,0.2); }
        .detail-photo-thumb .thumb-video-badge {
            position:absolute; bottom:3px; left:3px;
            background:rgba(0,0,0,0.75); color:#fff;
            font-size:9px; font-weight:700;
            padding:2px 5px; border-radius:4px;
            display:flex; align-items:center; gap:2px;
            pointer-events:none;
        }
        .detail-photo-thumb .thumb-play-icon {
            position:absolute; top:50%; left:50%;
            transform:translate(-50%,-50%);
            background:rgba(0,0,0,0.55); color:#fff;
            width:24px; height:24px; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            pointer-events:none;
        }
        .photo-delete-btn {
            position: absolute; top: 3px; right: 3px;
            background: rgba(229,57,53,0.9); color: #fff;
            border: none; border-radius: 50%;
            width: 20px; height: 20px;
            font-size: 11px; font-weight: 700;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.15s, transform 0.15s;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            z-index: 2;
            font-family: inherit;
            line-height: 1;
            padding: 0;
        }
        .photo-delete-btn:hover { background: #e53935; transform: scale(1.15); }
        .no-photos-placeholder { padding:14px; text-align:center; font-size:12px; color:var(--text-muted); background:var(--bg); border-radius:8px; }

        .timeline { display:flex; flex-direction:column; gap:12px; }
        .timeline-item { display:flex; gap:12px; align-items:flex-start; }
        .timeline-dot { width:8px; height:8px; border-radius:50%; background:var(--samsung-blue); margin-top:4px; flex-shrink:0; }
        .timeline-text { font-size:13px; font-weight:500; color:var(--text-primary); line-height:1.4; }
        .timeline-by   { font-size:11px; color:var(--text-muted); margin-top:2px; }

        .detail-actions { padding:14px 26px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px; background:#fafafa; flex-wrap:wrap; }

        .btn-action-cancel { background:#fff; border:1.5px solid #e53935; color:#e53935; padding:9px 20px; border-radius:24px; font-family:'Noto Sans Thai',sans-serif; font-size:13px; font-weight:700; cursor:pointer; transition:all 0.15s; display:flex; align-items:center; gap:6px; }
        .btn-action-cancel:hover { background:#fff0f0; }
        .btn-action-accept { background:#0a7a4b; color:#fff; border:none; padding:9px 20px; border-radius:24px; font-family:'Noto Sans Thai',sans-serif; font-size:13px; font-weight:700; cursor:pointer; transition:all 0.15s; display:flex; align-items:center; gap:6px; }
        .btn-action-accept:hover { background:#085f3a; box-shadow:0 4px 14px rgba(10,122,75,0.3); }
        .btn-action-finish { background:var(--samsung-blue); color:#fff; border:none; padding:9px 20px; border-radius:24px; font-family:'Noto Sans Thai',sans-serif; font-size:13px; font-weight:700; cursor:pointer; transition:all 0.15s; display:flex; align-items:center; gap:6px; }
        .btn-action-finish:hover { background:var(--samsung-blue-hover); box-shadow:0 4px 14px rgba(20,40,160,0.3); }
        .btn-action-print { background:#fff; border:1.5px solid #6c757d; color:#6c757d; padding:9px 20px; border-radius:24px; font-family:'Noto Sans Thai',sans-serif; font-size:13px; font-weight:700; cursor:pointer; transition:all 0.15s; display:flex; align-items:center; gap:6px; }
        .btn-action-print:hover { background:#f8f9fa; border-color:#444; color:#222; }

        .readonly-notice { font-size:12px; color:var(--text-muted); display:flex; align-items:center; gap:6px; padding:6px 10px; background:#f5f5f5; border-radius:20px; border:1px solid var(--border); }

        .lightbox-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.92); z-index:500; align-items:center; justify-content:center; flex-direction:column; gap:16px; }
        .lightbox-overlay.active { display:flex; }
        .lightbox-img { max-width:90vw; max-height:80vh; border-radius:10px; box-shadow:0 24px 80px rgba(0,0,0,0.6); object-fit:contain; }
        .lightbox-video { max-width:90vw; max-height:80vh; border-radius:10px; box-shadow:0 24px 80px rgba(0,0,0,0.6); background:#000; }
        .lightbox-nav { display:flex; align-items:center; gap:20px; }
        .lightbox-btn { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); color:#fff; width:40px; height:40px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.15s; font-size:18px; }
        .lightbox-btn:hover { background:rgba(255,255,255,0.3); }
        .lightbox-counter { font-size:13px; color:rgba(255,255,255,0.7); min-width:60px; text-align:center; }
        .lightbox-close-btn { position:fixed; top:20px; right:20px; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); color:#fff; width:40px; height:40px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:18px; }
        .lightbox-close-btn:hover { background:rgba(229,57,53,0.8); }
        .lightbox-caption { font-size:12px; color:rgba(255,255,255,0.6); }

        input[type=number].prod-qty-display:focus { border-color:var(--samsung-blue)!important; background:#fff!important; box-shadow:0 0 0 3px rgba(20,40,160,0.10); }
        input[type=number].prod-qty-display::-webkit-inner-spin-button,
        input[type=number].prod-qty-display::-webkit-outer-spin-button { opacity:1; }

        @keyframes fadeInUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .stat-card { animation:fadeInUp 0.4s ease both; }
        .stat-card:nth-child(1){animation-delay:0.05s}
        .stat-card:nth-child(2){animation-delay:0.10s}
        .stat-card:nth-child(3){animation-delay:0.15s}
        .stat-card:nth-child(4){animation-delay:0.20s}
        .table-section { animation:fadeInUp 0.4s ease 0.28s both; }

        @keyframes spin { to { transform:rotate(360deg); } }
        .spin { animation:spin 0.8s linear infinite; display:inline-block; }

        @media (min-width: 1400px) { main { padding: 32px 40px; } .stat-number { font-size: 42px; } }
        @media (max-width: 1399px) { main { padding: 24px 28px; } }
        @media (max-width: 1099px) { :root { --sidebar-w: 200px; } main { padding: 20px 20px; } .stats-grid { grid-template-columns: repeat(4,1fr); gap: 10px; } .stat-number { font-size: 32px; } }
        @media (max-width: 899px) {
            :root { --sidebar-w: 64px; }
            .sidebar { width: 64px; }
            .sidebar .sidebar-logo-text, .sidebar .nav-item-label, .sidebar .nav-badge, .sidebar .nav-group-label, .sidebar .collapse-label, .sidebar .sidebar-user-info { opacity: 0; pointer-events: none; }
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
            thead th:nth-child(5), tbody td:nth-child(5) { display: none; }
            .modal { width: 98vw; padding: 16px; }
            .detail-modal { width: 98vw; }
            .detail-body { grid-template-columns: 1fr; }
            .detail-info-card { border-right: none; border-bottom: 1px solid var(--border); }
            .steps-bar { padding: 12px 16px; overflow-x: auto; }
            .step-label { font-size: 9px; }
        }
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
            thead th:nth-child(4), tbody td:nth-child(4),
            thead th:nth-child(5), tbody td:nth-child(5),
            thead th:nth-child(7), tbody td:nth-child(7) { display: none; }
            .detail-claim-id { font-size: 15px; }
            .modal { padding: 14px; }
            .form-row { grid-template-columns: 1fr; }
            .detail-actions { flex-wrap: wrap; gap: 8px; padding: 12px 16px; }
            .btn-action-cancel, .btn-action-accept, .btn-action-finish, .btn-cancel, .btn-action-print { padding: 8px 14px; font-size: 12px; }
        }
    </style>
</head>
<body>

<script>
    const GAS_URL = 'https://script.google.com/macros/s/AKfycby0JavxoTk3YJiq87DuioTDGIpSv8G-WGRKuyCsyuYcFdBtM3f83Do-i6v8LznbFlHA/exec';
    const MAX_IMAGE_MB = 10;
    const MAX_VIDEO_MB = 100;
    const MAX_FILES    = 10;
    // mime types ที่อนุญาต
    const ALLOWED_VIDEO_TYPES = ['video/mp4','video/quicktime','video/x-msvideo','video/webm','video/x-matroska','video/3gpp','video/x-ms-wmv'];
</script>

<!-- SIDEBAR -->
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

<!-- PAGE WRAPPER -->
<div class="page-wrapper" id="page-wrapper">

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

    <main>
        <div class="page-title">รายการเคลม / คืนสินค้า</div>

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

<!-- CREATE MODAL -->
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
                รูปภาพ / วิดีโอประกอบ
                <span style="font-size:11px;color:var(--text-muted);font-weight:400;text-transform:none;letter-spacing:0;">(สูงสุด <span id="lbl-max-file">10</span> ไฟล์ · รูปไม่เกิน <span id="lbl-max-img-mb">10</span> MB · วิดีโอไม่เกิน <span id="lbl-max-vid-mb">100</span> MB)</span>
            </label>
            <div class="image-upload-zone" id="upload-zone" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                <input type="file" id="f-images" accept="image/*,video/*" multiple onchange="handleImageSelect(this.files)">
                <div class="upload-zone-icon" style="margin-bottom:6px;color:var(--text-muted);">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
                <div class="upload-zone-text">คลิกเพื่อเลือกรูปหรือวิดีโอ หรือลากวางไฟล์ที่นี่</div>
                <div class="upload-zone-sub">รูป: JPG, PNG, WEBP, GIF · วิดีโอ: MP4, MOV, WEBM, AVI · รองรับหลายไฟล์พร้อมกัน</div>
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
                <!-- ⭐ ข้อมูลจากการอนุมัติ (admin กรอก) - แสดงเฉพาะเมื่อมีข้อมูล -->
                <div class="detail-info-row" id="d-shipping-row" style="display:none;"><span class="detail-info-label">ที่อยู่จัดส่ง</span><span class="detail-info-val" id="d-shipping-address" style="white-space:pre-wrap;text-align:right;"></span></div>
                <div class="detail-info-row" id="d-claim-type-row" style="display:none;"><span class="detail-info-label">ประเภทเคลม</span><span class="detail-info-val" id="d-claim-type"></span></div>
            </div>
            <div class="detail-info-card">
                <div class="detail-section-title">ประวัติการดำเนินงาน</div>
                <div class="timeline" id="d-timeline"></div>
            </div>
        </div>
        <div class="detail-photos-section">
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0;border-top:1px solid var(--border);">
                <div style="padding:14px 18px;border-right:1px solid var(--border);">
                    <div class="detail-section-title" style="margin-bottom:8px;">รูปภาพ / วิดีโอประกอบ <span id="d-photo-count" style="font-weight:400;font-size:11px;color:var(--text-muted);text-transform:none;letter-spacing:0;"></span></div>
                    <div id="d-photos-grid" class="detail-photos-grid"></div>
                </div>
                <div style="padding:14px 18px;border-right:1px solid var(--border);">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                        <div class="detail-section-title" style="margin-bottom:0;">หลักฐาน <span id="d-evidence-count" style="font-weight:400;font-size:11px;color:var(--text-muted);text-transform:none;letter-spacing:0;"></span></div>
                        <div id="d-evidence-upload-btn"></div>
                    </div>
                    <div id="d-evidence-grid" class="detail-photos-grid"></div>
                </div>
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
    <div id="lb-media-wrapper"></div>
    <div class="lightbox-nav">
        <button class="lightbox-btn" onclick="lbPrev()">&#8592;</button>
        <span class="lightbox-counter" id="lb-counter"></span>
        <button class="lightbox-btn" onclick="lbNext()">&#8594;</button>
    </div>
    <div class="lightbox-caption" id="lb-caption"></div>
</div>

<!-- ⭐ APPROVAL MODAL - ให้ admin กรอกที่อยู่จัดส่ง + ประเภทเคลม ตอนกดอนุมัติ -->
<div class="modal-overlay" id="approval-modal" onclick="closeApprovalOnOverlay(event)">
    <div class="modal" style="width:560px;max-width:95vw;">
        <div class="modal-header">
            <div class="modal-icon" style="background:#e6f7f0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0a7a4b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="modal-title">อนุมัติเคส</div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">กรุณากรอกข้อมูลเพิ่มเติมก่อนอนุมัติ</div>
            </div>
        </div>

        <div id="approval-case-info" style="padding:10px 14px;background:var(--samsung-blue-light);border-radius:8px;margin-bottom:14px;font-size:12px;">
            <div style="color:var(--text-muted);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:2px;">เคส</div>
            <div id="approval-case-id" style="font-weight:700;color:var(--samsung-blue);font-family:'Courier New',monospace;"></div>
        </div>

        <div class="form-group">
            <label class="form-label">ที่อยู่จัดส่ง <span style="color:#e53935;">*</span></label>
            <textarea id="f-shipping-address" class="form-input"
                placeholder="กรอกที่อยู่ที่ใช้จัดส่งสินค้ากลับ&#10;เช่น 123 ถนนสุขุมวิท แขวงคลองตัน เขตวัฒนา กรุงเทพฯ 10110"
                style="height:90px;resize:vertical;line-height:1.6;"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">ประเภทเคลม <span style="color:#e53935;">*</span></label>
            <select id="f-claim-type" class="form-select">
                <option value="">-- เลือกประเภทเคลม --</option>
                <option value="no_return">เคลมของไม่ต้องคืน</option>
                <option value="return_required">เคลมของต้องคืน</option>
            </select>
            <div style="margin-top:6px;font-size:11px;color:var(--text-muted);line-height:1.5;">
                <div>• <strong style="color:var(--text-secondary);">เคลมของไม่ต้องคืน</strong> = ลูกค้าเก็บสินค้าไว้ ไม่ต้องส่งคืน</div>
                <div>• <strong style="color:var(--text-secondary);">เคลมของต้องคืน</strong> = ลูกค้าต้องส่งสินค้ากลับมา</div>
            </div>
        </div>

        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeApprovalModal()">ยกเลิก</button>
            <button type="button" class="btn-action-accept" id="btn-confirm-approve" onclick="confirmApproval()" style="border-radius:24px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                ยืนยันอนุมัติ
            </button>
        </div>
    </div>
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
        var createBtn = document.getElementById('btn-create-case');
        if (createBtn) createBtn.style.display = 'none';
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

document.getElementById('lbl-max-file').textContent = MAX_FILES;
document.getElementById('lbl-max-img-mb').textContent = MAX_IMAGE_MB;
document.getElementById('lbl-max-vid-mb').textContent = MAX_VIDEO_MB;

// ─── MEDIA TYPE HELPERS ────────────────────────────────────────────
// ตรวจสอบว่าเป็นไฟล์วิดีโอหรือไม่ (จาก mime type หรือ field 'type' ใน object รูป)
function isVideoFile(file) {
    return file && file.type && file.type.startsWith('video/');
}
function isVideoMedia(media) {
    if (!media) return false;
    // เช็คจาก field type (ที่เราเซ็ตเองตอนอัปโหลด)
    if (media.type === 'video') return true;
    // เช็คจาก mimeType (เผื่อ backend ส่งกลับมา)
    if (media.mimeType && media.mimeType.startsWith('video/')) return true;
    // เช็คจาก filename extension
    if (media.filename) {
        const ext = media.filename.split('.').pop().toLowerCase();
        if (['mp4','mov','webm','avi','mkv','3gp','wmv','m4v'].indexOf(ext) !== -1) return true;
    }
    return false;
}

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
            var stepDates = Array.isArray(c.stepDates) && c.stepDates.length === 5
                ? c.stepDates
                : buildStepDates(c.date, sm.step);
            cases[c.id] = {
                id:c.id, po:c.po, customer:c.customer,
                product:productStr, products:c.products||[],
                reason:c.reason, note:c.note||'-', fix:'-',
                shipping_address: c.shipping_address || '',
                claim_type: c.claim_type || '',
                date:c.date, status:c.status, step:sm.step,
                images:c.images||[],
                images_evidence:c.images_evidence||[],
                images_pack:c.images_pack||[],
                stepDates: stepDates,
                stepBy:['ฝ่ายบริการ','ช่างเทคนิค','ผู้จัดการ','ฝ่ายจัดส่ง','ฝ่ายบริการ'],
                stepDesc:['รับแจ้งเรื่องจากลูกค้า','ตรวจสอบสินค้าเรียบร้อย','อนุมัติการดำเนินการ','จัดเตรียมสินค้า / ซ่อม / เปลี่ยนสินค้า','ปิดเคสเรียบร้อย'],
            };
        });
        renderTable();
    } catch(err) { console.error('โหลดข้อมูลไม่สำเร็จ:',err); renderTable(); }
}

// ─── DATETIME HELPERS ─────────────────────────────────────────────
function nowDatetimeStr() {
    const d = new Date();
    const p = n => String(n).padStart(2,'0');
    return d.getFullYear()+'-'+p(d.getMonth()+1)+'-'+p(d.getDate())+' '+p(d.getHours())+':'+p(d.getMinutes());
}
function formatStepDatetime(val) {
    if (!val) return '';
    if (typeof val === 'string' && (val.includes('T') || /\d{2}:\d{2}/.test(val))) {
        const d = new Date(val);
        if (!isNaN(d)) {
            const p = n => String(n).padStart(2,'0');
            return d.getFullYear()+'-'+p(d.getMonth()+1)+'-'+p(d.getDate())+' '+p(d.getHours())+':'+p(d.getMinutes());
        }
    }
    const n = new Date();
    const p = n2 => String(n2).padStart(2,'0');
    return String(val).slice(0,10)+' '+p(n.getHours())+':'+p(n.getMinutes());
}

function buildStepDates(date, step) {
    const base = date ? formatStepDatetime(date) : nowDatetimeStr();
    const arr = [null,null,null,null,null];
    for (let i=0; i<step&&i<5; i++) arr[i]=base;
    return arr;
}

// ─── FILE / IMAGE / VIDEO HELPERS ──────────────────────────────────
function fileToBase64(file) {
    return new Promise((resolve,reject) => { const r=new FileReader(); r.onload=()=>resolve(r.result.split(',')[1]); r.onerror=reject; r.readAsDataURL(file); });
}

// บีบรูปภาพ (skip ถ้าเป็นวิดีโอ)
function compressImage(file, maxPx=1600, quality=0.85) {
    return new Promise(resolve => {
        // ถ้าเป็นวิดีโอ → ไม่บีบ ส่งไฟล์ต้นฉบับกลับ
        if (isVideoFile(file)) {
            resolve(file);
            return;
        }
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

// อัปโหลดไฟล์ 1 ไฟล์ (รองรับทั้งรูปและวิดีโอ)
async function uploadOneImage(file, customFilename, onProgress) {
    if(typeof customFilename==='function'){onProgress=customFilename;customFilename=null;}
    const isVideo = isVideoFile(file);
    // ถ้าเป็นวิดีโอ → ไม่บีบ ใช้ไฟล์ต้นฉบับ
    const processed = isVideo ? file : await compressImage(file,1200,0.85);
    const base64=await fileToBase64(processed);
    if(onProgress) onProgress(30);
    const filename=customFilename||(file.name.replace(/[^a-zA-Z0-9._-]/g,'_'));
    const mimeType = processed.type || (isVideo ? 'video/mp4' : 'image/jpeg');
    const res=await fetch('/return/upload-image',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF()},body:JSON.stringify({image:base64,filename,mimeType,gasUrl:GAS_URL})});
    if(onProgress) onProgress(90);
    const data=await res.json();
    if(!data.success) throw new Error(data.error||'Upload failed');
    if(onProgress) onProgress(100);
    return {
        fileId:data.fileId,
        viewUrl:data.viewUrl,
        thumbUrl:data.thumbUrl,
        filename,
        // ⭐ เพิ่ม field type เพื่อให้รู้ว่าเป็นรูปหรือวิดีโอ
        type: isVideo ? 'video' : 'image',
        mimeType: mimeType
    };
}

function renderPreviews() {
    document.getElementById('image-preview-grid').innerHTML=pendingFiles.map((item,idx)=>{
        const isVid = isVideoFile(item.file);
        const mediaTag = isVid
            ? `<video src="${item.previewUrl}" muted preload="metadata"></video><div class="video-play-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg></div><div class="video-badge">▶ VIDEO</div>`
            : `<img src="${item.previewUrl}" alt="${item.file.name}">`;
        return `
        <div class="preview-item ${item.status==='done'?'done-upload':item.status==='error'?'error-upload':''}" id="prev-${idx}">
            ${mediaTag}
            ${item.status==='uploading'?`<div class="upload-progress"><div class="prog-bar-bg"><div class="prog-bar-fill" id="prog-${idx}" style="width:${item.progress||0}%"></div></div><div class="prog-text">${item.progress||0}%</div></div>`:''}
            ${item.status==='pending'||item.status==='error'?`<button class="remove-btn" onclick="removeImage(${idx})">✕</button>`:''}
        </div>`;
    }).join('');
}
function removeImage(idx) { URL.revokeObjectURL(pendingFiles[idx].previewUrl); pendingFiles.splice(idx,1); renderPreviews(); updateUploadStatus(); }
function updateUploadStatus() {
    const bar=document.getElementById('upload-status');
    const total=pendingFiles.length, done=pendingFiles.filter(f=>f.status==='done').length, errors=pendingFiles.filter(f=>f.status==='error').length, uploading=pendingFiles.filter(f=>f.status==='uploading').length;
    if(!total){bar.style.display='none';return;}
    if(uploading>0){bar.className='upload-status-bar uploading';bar.innerHTML=`<span class="spin">⟳</span> กำลังอัปโหลด ${uploading} ไฟล์...`;}
    else if(errors>0&&done+errors===total){bar.className='upload-status-bar error';bar.innerHTML=`⚠️ อัปโหลดล้มเหลว ${errors} ไฟล์`;}
    else if(done===total&&total>0){bar.className='upload-status-bar done';bar.innerHTML=`✓ อัปโหลดสำเร็จทั้งหมด ${done} ไฟล์`;}
    else{bar.className='upload-status-bar uploading';bar.innerHTML=`<span class="spin">⟳</span> รอดำเนินการ...`;}
}
function handleImageSelect(files) { addFiles(Array.from(files)); document.getElementById('f-images').value=''; }

// ⭐ เพิ่มไฟล์ลงคิวอัปโหลด - รองรับทั้งรูปและวิดีโอ
function addFiles(files) {
    const remaining=MAX_FILES-pendingFiles.length;
    if(remaining<=0){showToast(`⚠️ เพิ่มได้สูงสุด ${MAX_FILES} ไฟล์`);return;}
    const toAdd=files.slice(0,remaining);
    toAdd.forEach(file=>{
        const isImg = file.type.startsWith('image/');
        const isVid = file.type.startsWith('video/');
        // ตรวจสอบประเภทไฟล์
        if(!isImg && !isVid){
            showToast(`⚠️ ${file.name} ไม่ใช่รูปภาพหรือวิดีโอ`);
            return;
        }
        // ตรวจสอบขนาด - แยกตามประเภท
        const maxMb = isVid ? MAX_VIDEO_MB : MAX_IMAGE_MB;
        if(file.size > maxMb*1024*1024){
            showToast(`⚠️ ${file.name} ใหญ่เกิน ${maxMb}MB (${isVid?'วิดีโอ':'รูป'})`);
            return;
        }
        pendingFiles.push({file,previewUrl:URL.createObjectURL(file),status:'pending',progress:0,result:null});
    });
    if(files.length>remaining) showToast(`⚠️ เพิ่มได้อีก ${remaining} ไฟล์`);
    renderPreviews(); updateUploadStatus();
}
function handleDragOver(e){e.preventDefault();document.getElementById('upload-zone').classList.add('dragover');}
function handleDragLeave(e){document.getElementById('upload-zone').classList.remove('dragover');}
function handleDrop(e){
    e.preventDefault();
    document.getElementById('upload-zone').classList.remove('dragover');
    // รับทั้งรูปและวิดีโอ
    addFiles(Array.from(e.dataTransfer.files).filter(f=>f.type.startsWith('image/')||f.type.startsWith('video/')));
}

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
// ⭐ Flow ใหม่: อัปโหลดรูป/วิดีโอทั้งหมดเสร็จก่อน → จากนั้นค่อยสร้างเคส + ส่ง Line
// ข้อดี: ถ้าอัปโหลดล้มเหลวจะไม่เกิดเคสเปล่า, Line notification จะแนบรูปครบ
async function submitNewCase(e) {
    if (e) e.preventDefault();

    // ── 1. Validate ข้อมูลฟอร์ม ──
    const customer = document.getElementById('f-customer').value.trim();
    const reason   = document.getElementById('f-reason').value;
    const note     = document.getElementById('f-note').value.trim();
    if (!currentDocuNo) { alert('กรุณาค้นหา PO ก่อน'); return; }
    if (!customer || !reason) { alert('กรุณากรอกข้อมูลให้ครบ'); return; }

    const selectedItems = [];
    document.querySelectorAll('.prod-chk').forEach(chk => {
        if (!chk.checked) return;
        const idx = parseInt(chk.dataset.idx, 10);
        const item = poItems[idx]; if (!item) return;
        const name = (item.GoodName ?? '').replace(/<<[^>]*>>/g, '').trim();
        const qtyInput = document.querySelector(`.prod-qty-display[data-idx="${idx}"]`);
        const qty = qtyInput ? parseFloat(qtyInput.value) || 0 : parseFloat(item.GoodQty2 ?? 0);
        selectedItems.push({ goodName: name, qty, invoice: '' });
    });
    if (!selectedItems.length) { alert('กรุณาเลือกสินค้าอย่างน้อย 1 รายการ'); return; }

    // ── 2. ตรวจสอบว่ามีไฟล์ที่ error อยู่หรือไม่ (ถ้ามี ไม่ให้ submit) ──
    const errorFiles = pendingFiles.filter(f => f.status === 'error');
    if (errorFiles.length > 0) {
        alert('มีไฟล์ที่อัปโหลดล้มเหลว ' + errorFiles.length + ' ไฟล์ กรุณาลบไฟล์เหล่านั้นออกก่อน');
        return;
    }

    const btn = document.getElementById('btn-submit-case');
    btn.disabled = true;

    // ── 3. อัปโหลดรูป/วิดีโอทั้งหมดให้เสร็จก่อน (ถ้ามี) ──
    const filesToUpload = pendingFiles.filter(f => f.status === 'pending');
    let uploadedMedia = [];

    // เก็บไฟล์ที่อัปสำเร็จไว้แล้ว (เคยอัปสำเร็จจากครั้งก่อน เช่น กดสร้างซ้ำ)
    pendingFiles.filter(f => f.status === 'done' && f.result).forEach(f => {
        uploadedMedia.push(f.result);
    });

    if (filesToUpload.length > 0) {
        try {
            // ใช้ PO ปัจจุบันสำหรับชื่อไฟล์
            const poRaw = (currentDocuNo || '').replace(/^PO/i, '');
            const now = new Date();
            const dd = String(now.getDate()).padStart(2,'0');
            const mm = String(now.getMonth()+1).padStart(2,'0');
            const yy = String(now.getFullYear()).slice(-2);
            const dateStr = dd + '-' + mm + '-' + yy;

            // อัปโหลดทีละไฟล์ (เพื่อให้รู้ progress รายไฟล์)
            for (let i = 0; i < filesToUpload.length; i++) {
                const item = filesToUpload[i];
                const seqNum = String(uploadedMedia.length + 1).padStart(2,'0');
                const origExt = item.file.name.split('.').pop().toLowerCase() || (isVideoFile(item.file)?'mp4':'jpg');
                const filename = poRaw + '_' + dateStr + '_' + seqNum + '.' + origExt;

                // อัปเดท UI: แสดงว่ากำลังอัปโหลดไฟล์ที่เท่าไรของเท่าไร
                btn.innerHTML = `<span class="spin">⟳</span> กำลังอัปโหลด ${i+1}/${filesToUpload.length}...`;

                // เปลี่ยนสถานะไฟล์เป็น uploading + render preview ใหม่
                item.status = 'uploading';
                item.progress = 0;
                // หา index จริงใน pendingFiles
                const realIdx = pendingFiles.indexOf(item);
                if (realIdx >= 0) pendingFiles[realIdx] = item;
                renderPreviews();
                updateUploadStatus();

                try {
                    const result = await uploadOneImage(item.file, filename, function(p) {
                        item.progress = p;
                        const realIdx2 = pendingFiles.indexOf(item);
                        if (realIdx2 >= 0) pendingFiles[realIdx2] = item;
                        renderPreviews();
                    });
                    item.status = 'done';
                    item.result = result;
                    uploadedMedia.push(result);
                    const realIdx3 = pendingFiles.indexOf(item);
                    if (realIdx3 >= 0) pendingFiles[realIdx3] = item;
                    renderPreviews();
                    updateUploadStatus();
                } catch (err) {
                    item.status = 'error';
                    const realIdx4 = pendingFiles.indexOf(item);
                    if (realIdx4 >= 0) pendingFiles[realIdx4] = item;
                    renderPreviews();
                    updateUploadStatus();
                    btn.disabled = false;
                    btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 11l3 3L22 4"/></svg> สร้างเคส';
                    alert('❌ อัปโหลดไฟล์ล้มเหลว: ' + (item.file.name) + '\n\n' + err.message + '\n\nกรุณาลองใหม่ หรือลบไฟล์นั้นออกแล้วลองสร้างเคสอีกครั้ง');
                    return; // หยุด ไม่สร้างเคส
                }
            }
        } catch (err) {
            btn.disabled = false;
            btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 11l3 3L22 4"/></svg> สร้างเคส';
            alert('เกิดข้อผิดพลาดระหว่างอัปโหลด: ' + err.message);
            return;
        }
    }

    // ── 4. ทุกไฟล์อัปโหลดสำเร็จแล้ว → สร้างเคส + ส่ง Line ในครั้งเดียว ──
    btn.innerHTML = `<span class="spin">⟳</span> กำลังบันทึกเคส...`;

    try {
        const res = await fetch('/return/submit', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF() },
            body: JSON.stringify({
                poNum: currentDocuNo,
                vendor: customer,
                reason,
                note,
                selectedItems,
                images: uploadedMedia, // ⭐ ส่งรูป/วิดีโอที่อัปโหลดเสร็จแล้วไปด้วยเลย
                notify_line: true
            })
        });
        const data = await res.json();
        if (!data.success) { alert(`❌ ${data.message}`); return; }

        // ── 5. อัปเดท local state + ปิด modal ──
        const today = new Date().toISOString().slice(0,10);
        const nowDt = nowDatetimeStr();
        const stepDatesFromBackend = Array.isArray(data.stepDates) && data.stepDates.length === 5
            ? data.stepDates
            : [nowDt, nowDt, null, null, null];

        const newCase = {
            id: data.return_id, customer, reason,
            note: note || '-', fix: '-', date: today, po: currentDocuNo,
            product: selectedItems.map(i => `${i.goodName} (จำนวน: ${i.qty})`).join('\n'),
            products: selectedItems.map(i => ({ product_name: i.goodName, quantity: i.qty, invoice: i.invoice||'' })),
            status: 'processing', step: 2,
            images: uploadedMedia, // ⭐ ใช้รูป/วิดีโอที่อัปโหลดแล้ว
            images_evidence: [],
            images_pack: [],
            stepDates: stepDatesFromBackend,
            stepBy: ['ฝ่ายบริการ','ช่างเทคนิค',null,null,null],
            stepDesc: ['รับแจ้งเรื่องจากลูกค้า','ตรวจสอบสินค้าเรียบร้อย',null,null,null]
        };
        cases = Object.assign({ [data.return_id]: newCase }, cases);
        closeModal();
        renderTable();
        const fileText = uploadedMedia.length > 0 ? ` (พร้อมไฟล์ ${uploadedMedia.length} รายการ)` : '';
        showToast(`✅ สร้างเคส ${data.return_id} เรียบร้อยแล้ว${fileText}`);
    } catch (err) {
        alert('เกิดข้อผิดพลาด: ' + err.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 11l3 3L22 4"/></svg> สร้างเคส';
    }
}

// ⭐ uploadImagesBackground ถูกลบไปแล้ว เพราะตอนนี้อัปโหลดเสร็จก่อนสร้างเคสใน submitNewCase

// ─── REFRESH DETAIL PHOTOS ────────────────────────────────────────
function refreshDetailPhotos(caseId) {
    var c = cases[caseId];
    if (!c) return;
    var imgs = c.images || [];
    var photoGrid = document.getElementById('d-photos-grid');
    var photoCount = document.getElementById('d-photo-count');
    if (!photoGrid) return;
    photoCount.textContent = imgs.length > 0 ? '(' + imgs.length + ' ไฟล์)' : '';
    if (!imgs.length) {
        photoGrid.innerHTML = '<div class="no-photos-placeholder">ไม่มีรูปภาพประกอบ</div>';
        return;
    }
    photoGrid.innerHTML = buildMediaGridHtml(imgs, caseId, 'images', false);
}

// ─── PRINT CASE LABEL (A4 - ชิดบนซ้าย เว้นขอบนิดเดียว) ────────────
function printCaseLabel(caseId) {
    var c = cases[caseId]; if (!c) return;

    var productRows = '';
    if (Array.isArray(c.products) && c.products.length) {
        productRows = c.products.map(function(p) {
            return '<tr><td style="padding:6px 10px;border:1px solid #999;font-size:11px;color:#000;">' +
                (p.product_name||'').trim() +
                '</td><td style="padding:6px 10px;border:1px solid #999;text-align:center;font-size:11px;font-weight:700;color:#000;">×' +
                parseFloat(p.quantity||0) + '</td></tr>';
        }).join('');
    } else {
        productRows = c.product.split('\n').map(function(p) {
            var m = p.trim().match(/^(.+?)\s*\(จำนวน:\s*([\d.]+)\)$/);
            if (m) {
                return '<tr><td style="padding:6px 10px;border:1px solid #999;font-size:11px;color:#000;">' + m[1].trim() +
                    '</td><td style="padding:6px 10px;border:1px solid #999;text-align:center;font-size:11px;font-weight:700;color:#000;">×' +
                    parseFloat(m[2]) + '</td></tr>';
            }
            return p.trim() ? '<tr><td colspan="2" style="padding:6px 10px;border:1px solid #999;font-size:11px;color:#000;">' + p.trim() + '</td></tr>' : '';
        }).join('');
    }

    var statusLabel = (statusMap[c.status] || statusMap.processing).label;
    var printDate = new Date().toLocaleDateString('th-TH', { day:'numeric', month:'long', year:'numeric' });
    var printTime = new Date().toLocaleTimeString('th-TH', { hour:'2-digit', minute:'2-digit' });

    var win = window.open('', '_blank', 'width=620,height=720');
    win.document.write('<!DOCTYPE html><html><head><meta charset="UTF-8">');
    win.document.write('<title></title>');
    win.document.write('<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;600;700;800&display=swap" rel="stylesheet">');
    win.document.write('<style>');
    win.document.write('*{box-sizing:border-box;margin:0;padding:0;}');
    win.document.write('html,body{margin:0;padding:0;}');
    win.document.write('body{font-family:"Noto Sans Thai",Arial,sans-serif;background:#fff;color:#000;font-size:11px;}');
    win.document.write('.wrap{border:1px solid #999;width:100%;max-width:560px;margin:14px auto;background:#fff;}');
    win.document.write('.header{background:#fff;padding:10px 12px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #999;gap:8px;}');
    win.document.write('.header-left{display:flex;align-items:center;gap:8px;flex:1;min-width:0;}');
    win.document.write('.header-icon{width:28px;height:28px;background:#fff;border-radius:6px;display:flex;align-items:center;justify-content:center;border:1px solid #999;flex-shrink:0;}');
    win.document.write('.header-title{color:#000;font-size:13px;font-weight:800;letter-spacing:-0.2px;}');
    win.document.write('.header-sub{color:#777;font-size:9px;margin-top:1px;}');
    win.document.write('.header-date{color:#555;font-size:9px;text-align:right;line-height:1.4;flex-shrink:0;}');
    win.document.write('.body{padding:10px 12px;}');
    win.document.write('.po-box{background:#fff;border:1px solid #999;border-radius:6px;padding:8px 10px;margin-bottom:8px;display:flex;align-items:center;justify-content:space-between;gap:6px;}');
    win.document.write('.po-label{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#666;margin-bottom:2px;}');
    win.document.write('.po-val{font-size:18px;font-weight:800;color:#000;letter-spacing:-0.3px;font-family:"Courier New",monospace;}');
    win.document.write('.status-badge{padding:3px 10px;border-radius:14px;font-size:10px;font-weight:700;background:#fff;color:#000;border:1px solid #999;white-space:nowrap;}');
    win.document.write('.info-grid{display:grid;grid-template-columns:1fr;gap:5px;margin-bottom:8px;}');
    win.document.write('.info-box{background:#fff;border:1px solid #bbb;border-radius:5px;padding:6px 10px;}');
    win.document.write('.info-label{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#666;margin-bottom:2px;}');
    win.document.write('.info-val{font-size:12px;font-weight:700;color:#000;line-height:1.3;word-break:break-word;}');
    win.document.write('.section-title{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#666;margin-bottom:5px;}');
    win.document.write('table{width:100%;border-collapse:collapse;border:1px solid #999;}');
    win.document.write('thead th{background:#fff;color:#000;padding:6px 10px;text-align:left;font-size:10px;font-weight:700;letter-spacing:0.04em;border-bottom:1.5px solid #666;}');
    win.document.write('thead th:last-child{text-align:center;width:80px;}');
    win.document.write('.footer{margin-top:10px;padding-top:8px;border-top:1px dashed #999;display:flex;justify-content:space-between;align-items:center;gap:8px;}');
    win.document.write('.footer-note{font-size:9px;color:#777;}');
    win.document.write('.sig-box{border-top:1px solid #666;width:120px;text-align:center;padding-top:4px;font-size:9px;color:#666;margin-top:18px;}');

    // ⭐⭐⭐ KEY: เว้นขอบ 5mm รอบ wrap (กันเครื่องพิมพ์ตัดขอบ)
    win.document.write('@media print{');
    win.document.write('  @page{size:A4 portrait; margin:0;}');
    win.document.write('  html,body{margin:0!important;padding:0!important;width:210mm!important;}');
    win.document.write('  .wrap{');
    win.document.write('    border:1px solid #999!important;');
    win.document.write('    border-radius:0!important;');
    win.document.write('    max-width:148mm!important;');
    win.document.write('    width:148mm!important;');
    // ⭐ margin บน 5mm + ซ้าย 5mm (กันเครื่องพิมพ์ตัด, ไม่ลอยถึงกึ่งกลาง)
    win.document.write('    margin:5mm 0 0 5mm!important;');
    win.document.write('    padding:0!important;');
    win.document.write('  }');
    win.document.write('  .no-print{display:none!important;}');
    win.document.write('}');

    win.document.write('</style></head><body>');
    win.document.write('<div class="wrap">');
    win.document.write('<div class="header">');
    win.document.write('<div class="header-left">');
    win.document.write('<div class="header-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>');
    win.document.write('<div><div class="header-title">ใบแนบสินค้า / Return</div><div class="header-sub">Return System · Samsung</div></div>');
    win.document.write('</div>');
    win.document.write('<div class="header-date">พิมพ์<br>' + printDate + '<br>' + printTime + '</div>');
    win.document.write('</div>');
    win.document.write('<div class="body">');
    win.document.write('<div class="po-box">');
    win.document.write('<div><div class="po-label">เลข PO</div><div class="po-val">' + (c.po || c.id) + '</div></div>');
    win.document.write('<span class="status-badge">' + statusLabel + '</span>');
    win.document.write('</div>');
    win.document.write('<div class="info-grid">');
    win.document.write('<div class="info-box"><div class="info-label">ร้านค้า</div><div class="info-val">' + (c.customer || '-') + '</div></div>');
    win.document.write('<div class="info-box"><div class="info-label">วันที่เปิดเคส</div><div class="info-val">' + (c.date || '-') + '</div></div>');
    win.document.write('<div class="info-box"><div class="info-label">เหตุผล</div><div class="info-val">' + (c.reason || '-') + '</div></div>');
    if (c.note && c.note !== '-') {
        win.document.write('<div class="info-box"><div class="info-label">หมายเหตุ</div><div class="info-val" style="font-weight:400;color:#333;">' + c.note + '</div></div>');
    }
    if (c.shipping_address) {
        win.document.write('<div class="info-box"><div class="info-label">ที่อยู่จัดส่ง</div><div class="info-val" style="white-space:pre-wrap;">' + c.shipping_address + '</div></div>');
    }
    win.document.write('</div>');
    win.document.write('<div class="section-title">รายการสินค้า</div>');
    win.document.write('<table><thead><tr><th>ชื่อสินค้า</th><th>จำนวน</th></tr></thead><tbody>' + productRows + '</tbody></table>');
    win.document.write('<div class="footer">');
    win.document.write('<div class="footer-note">ออกโดยระบบ Return System</div>');
    win.document.write('<div style="text-align:right;"><div class="sig-box">ผู้รับสินค้า / Receiver</div></div>');
    win.document.write('</div>');
    win.document.write('</div></div>');
    win.document.write('<div class="no-print" style="text-align:center;margin-top:14px;padding-bottom:14px;">');
    win.document.write('<button onclick="window.print()" style="background:#000;color:#fff;border:none;padding:10px 32px;border-radius:24px;font-family:\'Noto Sans Thai\',sans-serif;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 10px rgba(0,0,0,0.3);">สั่งพิมพ์เอกสาร</button>');
    win.document.write('<div style="margin-top:10px;font-size:11px;color:#666;line-height:1.6;padding:0 20px;">');
    win.document.write('⚠️ <b>ในหน้าต่าง Print:</b> Paper size = <b>A4</b>, Margins = <b>None</b>, ปิด <b>Headers and footers</b>');
    win.document.write('</div>');
    win.document.write('</div>');
    win.document.write('</body></html>');
    win.document.close();
}

// ─── BUILD MEDIA GRID HTML (รูป + วิดีโอ ในกริดเดียว) ─────────────
function buildMediaGridHtml(items, caseId, type, canDelete) {
    if (!items || !items.length) return '<div class="no-photos-placeholder">ไม่มีรูปภาพ/วิดีโอ</div>';
    var fallbackSvg = 'data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2280%22%20height%3D%2280%22%3E%3Crect%20width%3D%2280%22%20height%3D%2280%22%20fill%3D%22%23f0f0f0%22%2F%3E%3Ctext%20x%3D%2240%22%20y%3D%2245%22%20text-anchor%3D%22middle%22%20font-size%3D%2212%22%20fill%3D%22%23999%22%3E-%3C%2Ftext%3E%3C%2Fsvg%3E';
    var html = '';
    for (var idx = 0; idx < items.length; idx++) {
        var media = items[idx];
        var fileId = media.fileId || '';
        if (!fileId && media.viewUrl) { var m = media.viewUrl.match(/\/d\/([^/]+)\//); if (m) fileId = m[1]; }
        var src = fileId ? '/return/drive-image?id=' + fileId : (media.thumbUrl || media.viewUrl || '');
        var title = media.filename || ('ไฟล์ ' + (idx + 1));
        var isVid = isVideoMedia(media);

        html += '<div class="detail-photo-thumb" style="position:relative;" title="' + title + '">';

        if (isVid) {
            // วิดีโอ: แสดง thumbnail (drive thumbnail สามารถสร้าง preview ของวิดีโอได้)
            // + ไอคอน play overlay + badge "VIDEO"
            html += '<img src="' + src + '" alt="' + title + '" loading="lazy" onerror="this.src=\'' + fallbackSvg + '\'" style="cursor:pointer;background:#000;" onclick="openLightboxType(' + idx + ',\'' + caseId + '\',\'' + type + '\')">';
            html += '<div class="thumb-play-icon"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg></div>';
            html += '<div class="thumb-video-badge">▶ VID</div>';
        } else {
            // รูปภาพ
            html += '<img src="' + src + '" alt="' + title + '" loading="lazy" onerror="this.src=\'' + fallbackSvg + '\'" onclick="openLightboxType(' + idx + ',\'' + caseId + '\',\'' + type + '\')" style="cursor:pointer;">';
        }

        if (canDelete) {
            html += '<button class="photo-delete-btn" onclick="event.stopPropagation();deleteExtraPhoto(\'' + caseId + '\',\'' + type + '\',' + idx + ')" title="ลบไฟล์นี้">✕</button>';
        }

        html += '</div>';
    }
    return html;
}

// ─── DETAIL MODAL ─────────────────────────────────────────────────
function openDetail(id){
    currentDetailId=id;var c=cases[id];if(!c)return;
    document.getElementById('d-id').textContent=c.id;
    document.getElementById('d-meta').textContent=c.customer+' · เปิดวันที่ '+c.date;
    document.getElementById('d-customer').textContent=c.customer;
    document.getElementById('d-reason').textContent=c.reason;
    document.getElementById('d-note').textContent=c.note;

    // ⭐ แสดงที่อยู่จัดส่ง + ประเภทเคลม (ถ้ามีข้อมูล - หมายความว่า admin อนุมัติแล้ว)
    var shippingRow = document.getElementById('d-shipping-row');
    var claimTypeRow = document.getElementById('d-claim-type-row');
    if (c.shipping_address && c.shipping_address.trim() !== '') {
        document.getElementById('d-shipping-address').textContent = c.shipping_address;
        shippingRow.style.display = 'flex';
    } else {
        shippingRow.style.display = 'none';
    }
    if (c.claim_type && c.claim_type.trim() !== '') {
        var claimTypeLabel = c.claim_type === 'no_return' ? '🎁 เคลมของไม่ต้องคืน'
            : c.claim_type === 'return_required' ? '↩️ เคลมของต้องคืน'
            : c.claim_type;
        document.getElementById('d-claim-type').textContent = claimTypeLabel;
        claimTypeRow.style.display = 'flex';
    } else {
        claimTypeRow.style.display = 'none';
    }
    var productEl=document.getElementById('d-product');productEl.style.textAlign='left';
    if(Array.isArray(c.products)&&c.products.length){
        productEl.innerHTML=c.products.map(function(p){return'<div style="display:flex;align-items:baseline;justify-content:space-between;gap:8px;padding:5px 8px;margin-bottom:4px;background:#f8f9ff;border-left:2px solid #1428A0;border-radius:0 4px 4px 0;"><span style="font-size:12px;color:#535353;">'+(p.product_name||'').trim()+'</span><span style="flex-shrink:0;font-size:11px;font-weight:700;color:#1428A0;background:#e8ecf8;padding:1px 8px;border-radius:10px;">×'+parseFloat(p.quantity||0)+'</span></div>';}).join('');
    } else {
        productEl.innerHTML=c.product.split('\n').map(function(p){var m=p.trim().match(/^(.+?)\s*\(จำนวน:\s*([\d.]+)\)$/);return m?'<div style="display:flex;align-items:baseline;justify-content:space-between;gap:8px;padding:5px 8px;margin-bottom:4px;background:#f8f9ff;border-left:2px solid #1428A0;border-radius:0 4px 4px 0;"><span style="font-size:12px;color:#535353;">'+m[1].trim()+'</span><span style="flex-shrink:0;font-size:11px;font-weight:700;color:#1428A0;background:#e8ecf8;padding:1px 8px;border-radius:10px;">×'+parseFloat(m[2])+'</span></div>':(p.trim()?'<div style="padding:5px 8px;margin-bottom:4px;background:#f8f9ff;border-left:2px solid #1428A0;border-radius:0 4px 4px 0;font-size:12px;color:#535353;">'+p.trim()+'</div>':'');}).join('');
    }
    var sm=statusMap[c.status]||statusMap.processing;
    var badge=document.getElementById('d-badge');badge.className='badge '+sm.cls;badge.textContent=sm.label;
    document.getElementById('d-steps').innerHTML=stepLabels.map(function(label,i){var cls=i<c.step?'done':i===c.step?'active':'';var icon=i<c.step?'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>':'<span style="font-size:11px;font-weight:700;color:'+(i===c.step?'var(--samsung-blue)':'#ccc')+'">'+(i+1)+'</span>';return'<div class="step-item '+cls+'"><div class="step-circle">'+icon+'</div><div class="step-label">'+label+'</div></div>';}).join('');
    document.getElementById('d-timeline').innerHTML=stepLabels.map(function(label,i){if(i>=c.step||!c.stepDates||!c.stepDates[i])return'';var dt=formatStepDatetime(c.stepDates[i]);return'<div class="timeline-item"><div class="timeline-dot"></div><div><div class="timeline-text">'+(c.stepDesc&&c.stepDesc[i]?c.stepDesc[i]:label)+'</div><div class="timeline-by">'+(c.stepBy&&c.stepBy[i]?c.stepBy[i]:'')+' · '+dt+'</div></div></div>';}).join('');

    // รูปภาพ/วิดีโอประกอบ (ส่วนแรก - ไม่มีปุ่มลบ)
    var imgs=c.images||[];
    var photoGrid=document.getElementById('d-photos-grid');
    var photoCount=document.getElementById('d-photo-count');
    photoCount.textContent=imgs.length>0?'('+imgs.length+' ไฟล์)':'';
    photoGrid.innerHTML = imgs.length ? buildMediaGridHtml(imgs, id, 'images', false) : '<div class="no-photos-placeholder">ไม่มีรูปภาพ/วิดีโอประกอบ</div>';

    renderExtraPhotos(id, 'evidence', 'd-evidence-grid', 'd-evidence-count', 'd-evidence-upload-btn');
    renderExtraPhotos(id, 'pack', 'd-pack-grid', 'd-pack-count', 'd-pack-upload-btn');

    // ACTIONS
    var actionsHtml = '<button class="btn-cancel" onclick="closeDetail()">ปิด</button>';
    if (c.status === 'accept') {
        actionsHtml += '<button class="btn-action-print" onclick="printCaseLabel(\'' + id + '\')">🖨️ ปริ้นเอกสารติดสินค้า</button>';
    }
    if (isAdmin) {
        var c_status = c.status;
        if (c_status === 'processing') {
            actionsHtml += '<button class="btn-action-cancel" onclick="changeStatus(\''+id+'\',\'cancel\')">✕ ยกเลิก (Cancel)</button>';
            actionsHtml += '<button class="btn-action-accept" onclick="openApprovalModal(\''+id+'\')">✓ อนุมัติ (Accept)</button>';
        }
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
    var isLocked = true;

    if (isAdmin) {
        if (type === 'pack') {
            if (c.status === 'accept') isLocked = false;
        } else {
            if (c.status === 'processing') isLocked = false;
        }
    }

    if (!isLocked) {
        // ⭐ accept ทั้งรูปและวิดีโอ
        btnEl.innerHTML = '<label class="btn-add-photo" title="เพิ่มรูป/วิดีโอ" style="cursor:pointer;display:inline-flex;align-items:center;gap:4px;background:var(--samsung-blue);color:#fff;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;">' +
            '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>' +
            ' เพิ่มไฟล์' +
            '<input type="file" accept="image/*,video/*" multiple style="display:none;" onchange="handleExtraPhotoUpload(this,\'' + caseId + '\',\'' + type + '\')">' +
            '</label>';
    } else {
        if (isAdmin) {
            btnEl.innerHTML = '<span style="font-size:10px;color:var(--text-muted);background:#f5f5f5;border:1px solid var(--border);border-radius:12px;padding:3px 8px;display:inline-flex;align-items:center;gap:4px;">🔒 ล็อคแล้ว</span>';
        } else {
            btnEl.innerHTML = '';
        }
    }

    renderExtraPhotosGrid(caseId, type);
}

async function handleExtraPhotoUpload(input, caseId, type) {
    if (!isAdmin) {
        showToast('⚠️ ไม่มีสิทธิ์เพิ่มไฟล์ (เฉพาะผู้ดูแลระบบ)');
        input.value = '';
        return;
    }

    var rawFiles = Array.from(input.files);
    if (!rawFiles.length) return;
    setTimeout(function(){ input.value = ''; }, 100);

    // ⭐ Validate ไฟล์: รับทั้งรูปและวิดีโอ + เช็คขนาด
    var files = [];
    for (var k = 0; k < rawFiles.length; k++) {
        var f = rawFiles[k];
        var isImg = f.type.startsWith('image/');
        var isVid = f.type.startsWith('video/');
        if (!isImg && !isVid) {
            showToast('⚠️ ' + f.name + ' ไม่ใช่รูปหรือวิดีโอ');
            continue;
        }
        var maxMb = isVid ? MAX_VIDEO_MB : MAX_IMAGE_MB;
        if (f.size > maxMb * 1024 * 1024) {
            showToast('⚠️ ' + f.name + ' ใหญ่เกิน ' + maxMb + 'MB');
            continue;
        }
        files.push(f);
    }
    if (!files.length) return;

    showToast('⏳ กำลังอัปโหลด ' + files.length + ' ไฟล์...');
    var c = cases[caseId]; if (!c) return;

    var isUploadAllowed = false;
    if (type === 'pack' && c.status === 'accept') {
        isUploadAllowed = true;
    } else if (type === 'evidence' && c.status === 'processing') {
        isUploadAllowed = true;
    }

    if (!isUploadAllowed) {
        showToast('⚠️ ไม่สามารถเพิ่มไฟล์ได้ เคสนี้ถูกล็อคแล้ว');
        return;
    }

    if (!c.images_evidence) c.images_evidence = [];
    if (!c.images_pack) c.images_pack = [];
    var arr = type === 'evidence' ? c.images_evidence : c.images_pack;
    var now = new Date();
    var dateStr = String(now.getDate()).padStart(2,'0') + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getFullYear()).slice(-2);
    var successCount = 0;
    for (var i = 0; i < files.length; i++) {
        try {
            var seqNum = String(arr.length + 1).padStart(2,'0');
            // ⭐ ใช้ extension ตามไฟล์จริง
            var ext = files[i].name.split('.').pop().toLowerCase() || (isVideoFile(files[i]) ? 'mp4' : 'jpg');
            var filename = type + '_' + (c.po||caseId) + '_' + dateStr + '_' + seqNum + '.' + ext;
            var result = await uploadOneImage(files[i], filename, null);
            arr.push(result);
            successCount++;
            renderExtraPhotosGrid(caseId, type);
            try {
                await fetch('/return/' + encodeURIComponent(caseId) + '/update-images', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF() },
                    body: JSON.stringify({ images: c.images, images_evidence: c.images_evidence, images_pack: c.images_pack, final: (i === files.length - 1) })
                });
            } catch(e) { console.warn('save failed', e); }
        } catch(e) { console.warn('upload failed', e); }
    }
    if (successCount > 0) showToast('✅ อัปโหลด ' + successCount + ' ไฟล์เรียบร้อย');
}

function renderExtraPhotosGrid(caseId, type) {
    var c = cases[caseId]; if (!c) return;
    var imgs = (type === 'evidence' ? c.images_evidence : c.images_pack) || [];
    var gridId  = type === 'evidence' ? 'd-evidence-grid'  : 'd-pack-grid';
    var countId = type === 'evidence' ? 'd-evidence-count' : 'd-pack-count';
    var grid    = document.getElementById(gridId);
    var countEl = document.getElementById(countId);
    if (!grid) return;
    countEl.textContent = imgs.length > 0 ? '(' + imgs.length + ' ไฟล์)' : '';

    var canDelete = false;
    if (isAdmin) {
        if (type === 'evidence' && c.status === 'processing') canDelete = true;
        if (type === 'pack' && c.status === 'accept') canDelete = true;
    }

    grid.innerHTML = buildMediaGridHtml(imgs, caseId, type, canDelete);
}

async function deleteExtraPhoto(caseId, type, idx) {
    if (!isAdmin) { showToast('⚠️ ไม่มีสิทธิ์ลบไฟล์'); return; }
    var c = cases[caseId]; if (!c) return;

    var allowed = (type === 'evidence' && c.status === 'processing')
               || (type === 'pack' && c.status === 'accept');
    if (!allowed) {
        showToast('⚠️ ไม่สามารถลบไฟล์ได้ เคสนี้ถูกล็อคแล้ว');
        return;
    }

    if (!confirm('ยืนยันลบไฟล์นี้?')) return;

    var arr = type === 'evidence' ? (c.images_evidence || []) : (c.images_pack || []);
    if (idx < 0 || idx >= arr.length) return;
    arr.splice(idx, 1);
    renderExtraPhotosGrid(caseId, type);

    try {
        await fetch('/return/' + encodeURIComponent(caseId) + '/update-images', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF() },
            body: JSON.stringify({
                images: c.images,
                images_evidence: c.images_evidence,
                images_pack: c.images_pack,
                final: true,
                notify_line: false
            })
        });
        showToast('✅ ลบไฟล์เรียบร้อย');
    } catch(e) {
        console.warn('ลบไฟล์ไม่สำเร็จ:', e);
        showToast('⚠️ บันทึกการลบไปยังเซิร์ฟเวอร์ไม่สำเร็จ');
    }
}

function closeDetail(){document.getElementById('detail-modal').classList.remove('active');currentDetailId=null;}
function closeDetailOnOverlay(e){if(e.target===document.getElementById('detail-modal'))closeDetail();}

// ─── LIGHTBOX (รองรับทั้งรูปและวิดีโอ) ────────────────────────────
function openLightboxType(startIdx, caseId, type) {
    var c = cases[caseId]; if (!c) return;
    var arr = type === 'evidence' ? (c.images_evidence||[]) : type === 'pack' ? (c.images_pack||[]) : (c.images||[]);
    if (!arr.length) return;
    lbImages = arr; lbIndex = startIdx; showLightboxImage();
    document.getElementById('lightbox').classList.add('active');
}
function openLightbox(startIdx,caseId){openLightboxType(startIdx,caseId,'images');}

// ⭐ แสดงไฟล์ใน lightbox
//   - รูป → แสดงรูปเต็ม
//   - วิดีโอ → แสดง card สวยๆ พร้อมปุ่มดาวน์โหลดเพื่อดู (ไม่เล่นในหน้าเว็บ - เสถียร 100%)
function showLightboxImage(){
    var media = lbImages[lbIndex];
    var fileId = media.fileId || '';
    if (!fileId && media.viewUrl) { var m = media.viewUrl.match(/\/d\/([^/]+)\//); if (m) fileId = m[1]; }
    var wrapper = document.getElementById('lb-media-wrapper');

    if (isVideoMedia(media)) {
        // ⭐ วิดีโอ: แสดง card เรียบง่าย พร้อมปุ่มดาวน์โหลด/เปิดใน Drive
        var driveViewUrl = fileId ? 'https://drive.google.com/file/d/' + fileId + '/view' : '';
        var downloadUrl  = fileId ? 'https://drive.google.com/uc?export=download&id=' + fileId : (media.viewUrl || '');
        var filename     = media.filename || 'video';

        wrapper.innerHTML =
            '<div style="background:#1a1a1a;border:1px solid rgba(255,255,255,0.12);border-radius:14px;width:min(90vw,460px);overflow:hidden;box-shadow:0 24px 80px rgba(0,0,0,0.6);">' +
                // ─── Body: ชื่อไฟล์ + ปุ่ม ──
                '<div style="padding:24px 24px;">' +
                    '<div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">' +
                        '<div style="width:36px;height:36px;background:rgba(20,40,160,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
                            '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5b8af7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>' +
                        '</div>' +
                        '<div style="flex:1;min-width:0;">' +
                            '<div style="font-size:13px;font-weight:700;color:#fff;word-break:break-all;line-height:1.4;">' + filename + '</div>' +
                            '<div style="font-size:11px;color:rgba(255,255,255,0.5);margin-top:2px;">ไฟล์วิดีโอ · กดดาวน์โหลดเพื่อดู</div>' +
                        '</div>' +
                    '</div>' +
                    '<div style="display:flex;flex-direction:column;gap:8px;">' +
                        '<a href="' + downloadUrl + '" download="' + filename + '" ' +
                        'style="background:#1428A0;color:#fff;padding:11px 20px;border-radius:24px;font-size:13px;font-weight:700;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px;transition:background 0.15s;font-family:inherit;" ' +
                        'onmouseover="this.style.background=\'#0f1f80\'" onmouseout="this.style.background=\'#1428A0\'">' +
                            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>' +
                            'ดาวน์โหลดวิดีโอเพื่อดู' +
                        '</a>' +
                        '<a href="' + driveViewUrl + '" target="_blank" rel="noopener" ' +
                        'style="background:rgba(255,255,255,0.08);color:#fff;border:1px solid rgba(255,255,255,0.2);padding:10px 20px;border-radius:24px;font-size:12px;font-weight:600;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:6px;transition:background 0.15s;font-family:inherit;" ' +
                        'onmouseover="this.style.background=\'rgba(255,255,255,0.15)\'" onmouseout="this.style.background=\'rgba(255,255,255,0.08)\'">' +
                            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>' +
                            'เปิดใน Google Drive' +
                        '</a>' +
                    '</div>' +
                '</div>' +
            '</div>';
    } else {
        // รูป
        var src = fileId ? '/return/drive-image?id=' + fileId + '&sz=w1600' : (media.viewUrl || '');
        wrapper.innerHTML = '<img class="lightbox-img" src="' + src + '" alt="">';
    }

    document.getElementById('lb-counter').textContent = (lbIndex+1) + ' / ' + lbImages.length;
    document.getElementById('lb-caption').textContent = (media.filename||'') + (isVideoMedia(media) ? '  •  🎬 วิดีโอ' : '');
}

// ฟังก์ชันสำรอง (ไม่ใช้แล้วแต่เก็บไว้เผื่ออนาคต)
function handleVideoError(videoEl, fileId) {
    console.warn('Video failed to load, fileId=', fileId);
}
function lbPrev(){lbIndex=(lbIndex-1+lbImages.length)%lbImages.length;showLightboxImage();}
function lbNext(){lbIndex=(lbIndex+1)%lbImages.length;showLightboxImage();}
function closeLightbox(){
    document.getElementById('lightbox').classList.remove('active');
    // ⭐ เคลียร์ DOM (iframe / video) เพื่อหยุดเล่น
    var wrap = document.getElementById('lb-media-wrapper');
    if (wrap) {
        var vid = wrap.querySelector('video');
        if (vid) { try { vid.pause(); vid.removeAttribute('src'); vid.load(); } catch(e){} }
        var ifr = wrap.querySelector('iframe');
        if (ifr) { try { ifr.src = 'about:blank'; } catch(e){} }
        wrap.innerHTML = '';
    }
}
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

// ─── APPROVAL MODAL (ADMIN) ───────────────────────────────────────
// เปิด modal ให้ admin กรอกที่อยู่จัดส่ง + ประเภทเคลม ก่อนอนุมัติ
var approvalCaseId = null;

function openApprovalModal(caseId) {
    if (!isAdmin) return;
    var c = cases[caseId]; if (!c) return;
    approvalCaseId = caseId;
    document.getElementById('approval-case-id').textContent = c.id + ' · ' + c.customer;
    // โหลดค่าเก่าถ้ามี (เผื่อเคยกรอกแล้ว)
    document.getElementById('f-shipping-address').value = c.shipping_address || '';
    document.getElementById('f-claim-type').value = c.claim_type || '';
    document.getElementById('approval-modal').classList.add('active');
}

function closeApprovalModal() {
    document.getElementById('approval-modal').classList.remove('active');
    approvalCaseId = null;
}

function closeApprovalOnOverlay(e) {
    if (e.target === document.getElementById('approval-modal')) closeApprovalModal();
}

async function confirmApproval() {
    if (!approvalCaseId) return;
    var shippingAddress = document.getElementById('f-shipping-address').value.trim();
    var claimType       = document.getElementById('f-claim-type').value;

    if (!shippingAddress) { alert('กรุณากรอกที่อยู่จัดส่ง'); return; }
    if (!claimType)       { alert('กรุณาเลือกประเภทเคลม'); return; }

    var btn = document.getElementById('btn-confirm-approve');
    btn.disabled = true;
    btn.innerHTML = '<span class="spin">⟳</span> กำลังอนุมัติ...';

    try {
        await changeStatus(approvalCaseId, 'accept', {
            shipping_address: shippingAddress,
            claim_type: claimType,
            skipConfirm: true
        });
        closeApprovalModal();
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> ยืนยันอนุมัติ';
    }
}

// ─── CHANGE STATUS (ADMIN ONLY) ───────────────────────────────────
async function changeStatus(caseId, newStatus, extraData) {
    if (!isAdmin) return;
    extraData = extraData || {};
    var labels = { cancel:'ยกเลิกเคส', accept:'อนุมัติเคส', finish:'ปิดเคส' };
    // ⭐ ถ้าเป็น approval modal เรียกมา → ไม่ต้องถาม confirm ซ้ำ
    if (!extraData.skipConfirm) {
        if (!confirm('ยืนยัน: ' + (labels[newStatus]||newStatus) + '?')) return;
    }
    try {
        var payload = {
            status: newStatus,
            updated_by: currentUser,
            notify_line: false
        };
        // ⭐ แนบที่อยู่จัดส่ง + ประเภทเคลม (เฉพาะตอน approve)
        if (newStatus === 'accept') {
            if (extraData.shipping_address !== undefined) payload.shipping_address = extraData.shipping_address;
            if (extraData.claim_type !== undefined)       payload.claim_type       = extraData.claim_type;
        }
        var res = await fetch('/return/' + encodeURIComponent(caseId) + '/status', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF() },
            body: JSON.stringify(payload)
        });
        var data = await res.json();
        if (!data.success) { alert('❌ ' + (data.message||'เกิดข้อผิดพลาด')); return; }
        var sm = statusMap[newStatus] || statusMap.processing;
        cases[caseId].status = newStatus;
        cases[caseId].step = sm.step;

        // ⭐ อัปเดทที่อยู่จัดส่ง + ประเภทเคลม จาก response
        if (data.shipping_address !== undefined) cases[caseId].shipping_address = data.shipping_address;
        if (data.claim_type !== undefined)       cases[caseId].claim_type       = data.claim_type;

        if (Array.isArray(data.stepDates) && data.stepDates.length === 5) {
            cases[caseId].stepDates = data.stepDates;
        } else {
            var nowDt = nowDatetimeStr();
            if (!cases[caseId].stepDates) cases[caseId].stepDates = [null,null,null,null,null];
            for (var si = 0; si < sm.step && si < 5; si++) {
                if (!cases[caseId].stepDates[si]) cases[caseId].stepDates[si] = nowDt;
            }
        }
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