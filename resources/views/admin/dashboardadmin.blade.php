<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ระบบติดตามสถานะคำสั่งซื้อและจัดส่ง (SO Tracking)</title>
    <!-- Google Fonts & FontAwesome Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Sarabun', sans-serif;
        }

        body {
            background-color: #f4f7fc;
            color: #333;
            padding: 10px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            background: #fff;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .top-header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #edf2f7;
            padding-bottom: 15px;
            gap: 20px;
            flex-wrap: wrap;
        }

        header h1 {
            font-size: 22px;
            color: #2c3e50;
            white-space: nowrap;
        }

        header h1 i {
            color: #3498db;
            margin-right: 8px;
        }

        .stats-container {
            display: flex;
            gap: 25px;
            flex: 1;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .stat-card {
            background: transparent;
            padding: 0;
            min-width: 0;
            white-space: nowrap;
            box-shadow: none;
        }

        .top-header-section { justify-content: flex-start; }
        .top-header-section .stats-container {
            flex-wrap: nowrap;
            gap: 24px;
            margin-left: auto;
        }
        @media (max-width: 1350px) {
            .top-header-section .stats-container {
                flex: 1 1 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
                gap: 16px 32px;
            }
        }

        .stage-summary { margin-bottom: 20px; }
        .stage-summary-caption { font-size: 12px; color: #64748b; margin-bottom: 8px; }
        .stage-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
        .stage-card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; background: #fff; }
        .stage-card-head { display: flex; justify-content: space-between; align-items: center; font-size: 14px; font-weight: 600; color: #334155; }
        .stage-card-head i { color: #3498db; margin-right: 6px; }
        .stage-percent { font-size: 13px; color: #166534; font-weight: 700; }
        .stage-bar { height: 6px; background: #f1f5f9; border-radius: 99px; margin: 10px 0; overflow: hidden; }
        .stage-bar span { display: block; height: 100%; background: #22c55e; border-radius: 99px; }
        .stage-counts { display: flex; justify-content: space-between; font-size: 13px; }
        .stage-counts .done { color: #166534; }
        .stage-counts .pending { color: #b45309; }
        .stage-counts strong { font-size: 16px; margin-left: 4px; }

        @media (max-width: 1100px) { .stage-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 560px) { .stage-grid { grid-template-columns: 1fr; } }

        .actions-bar { display: flex; justify-content: space-between; margin-bottom: 20px; gap: 10px; flex-wrap: wrap; }
        .search-box { position: relative; flex: 1 1 300px; min-width: 240px; max-width: 420px; }
        .search-box input { width: 100%; padding: 10px 15px 10px 40px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; transition: all 0.3s; }
        .search-box input:focus { border-color: #3498db; box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1); }
        .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; }

        .filter-date-form { display: flex; gap: 10px; align-items: center; flex: 0 0 auto; margin-left: auto; }
        .filter-date-form input[type="date"] { padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; flex: 0 0 160px; }
        .stage-select { padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background-color: #fff; color: #334155; cursor: pointer; flex: 0 0 190px; width: 190px; }
        .stage-select:focus { border-color: #3498db; box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1); }
        .stage-select.active { border-color: #3498db; background-color: #eff6ff; color: #1e40af; font-weight: 600; }

        @media (max-width: 1300px) { .filter-date-form { flex-wrap: wrap; } }

        .btn-filter, .btn-reset { padding: 9px 15px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; flex-shrink: 0; white-space: nowrap; }
        .btn-filter { background-color: #3498db; color: white; }
        .btn-filter:hover { background-color: #2980b9; }
        .btn-reset { background-color: #94a3b8; color: white; }
        .btn-reset:hover { background-color: #64748b; }

        .table-responsive { width: 100%; overflow-x: auto; }
        table { width: 100%; table-layout: fixed; border-collapse: collapse; text-align: left; font-size: 14px; }
        th, td { padding: 14px 12px; border-bottom: 1px solid #e2e8f0; word-wrap: break-word; overflow-wrap: break-word; }
        tbody td { text-align: center; vertical-align: middle; }
        thead th { text-align: center; background-color: #f8fafc; color: #475569; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
        tr:hover { background-color: #f8fafc; }

        col.col-info  { width: 7%; }
        col.col-stage { width: 16.5%; }
        col.col-dur   { width: 13%; }

        th.th-info, td.td-info { padding: 10px 2px; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        td.td-info { color: #475569; }
        th.th-stage { font-size: 13px; font-weight: 700; }
        th.th-stage i { color: #3498db; margin-right: 4px; }
        tbody td:nth-child(n+4) .badge { font-size: 13px; padding: 6px 14px; font-weight: 600; }
        tbody td:nth-child(n+4) .status-meta { font-size: 12px; }

        .money-val.loading { color: #cbd5e1; font-weight: 400; }
        .money-val .partial { font-size: 11px; font-weight: 400; color: #94a3b8; margin-left: 4px; }

        .bill-price { margin-top: 0; font-size: 12px; line-height: 1.5; font-weight: 700; color: #0c4a6e; }
        .bill-price i { font-weight: 900; color: #64748b; margin-right: 2px; }
        .bill-price.loading, .bill-price.na { color: #cbd5e1; font-weight: 400; }

        .view-menu { display: inline-flex; gap: 4px; padding: 4px; background: #f1f5f9; border-radius: 10px; flex-shrink: 0; }
        .view-btn { border: none; background: transparent; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; color: #64748b; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
        .view-btn:hover { color: #1e40af; }
        .view-btn.active { background: #fff; color: #1e40af; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12); }

        table.sum-table { table-layout: auto; }
        table.sum-table > tbody > tr > td, table.sum-table > thead > tr > th { text-align: center; }
        .mini-bar { height: 4px; background: #f1f5f9; border-radius: 99px; margin: 4px auto 0; max-width: 90px; overflow: hidden; }
        .mini-bar span { display: block; height: 100%; background: #22c55e; }
        .sum-cancel { color: #b91c1c; font-weight: 600; }
        .sum-warn   { color: #b45309; font-weight: 600; }
        .sum-zero   { color: #cbd5e1; }
        .sum-ok     { color: #166534; font-weight: 600; }
        .sum-ok small { font-weight: 400; color: #64748b; }
        .sum-money  { font-weight: 700; color: #0c4a6e; }
        .sum-money.ok { color: #166534; }

        table.drv-table { table-layout: fixed; }
        table.drv-table col.c-num { width: 8%; }
        table.drv-table > thead > tr > th.c-name, table.drv-table > tbody > tr.drv-row > td:first-child { text-align: left; padding-left: 20px; }
        table.drv-table > tbody > tr.drv-row > td:first-child strong { font-size: 15px; }
        table.drv-table > tbody > tr.drv-row > td:nth-child(2) strong { font-size: 16px; }
        table.drv-table .sum-money { font-size: 16px; }
        table.drv-table > thead > tr > th:nth-child(n+7) { color: #0c4a6e; }

        .sum-unit { font-size: 11px; color: #94a3b8; font-weight: 400; }
        .rate { display: inline-block; min-width: 52px; padding: 3px 10px; border-radius: 12px; font-size: 13px; font-weight: 700; }
        .rate.good { background: #dcfce7; color: #166534; }
        .rate.mid  { background: #fef3c7; color: #b45309; }
        .rate.low  { background: #fee2e2; color: #b91c1c; }
        .ok-num  { color: #15803d; }
        .bad-num { color: #b91c1c; }

        .sum-filter { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 16px; }
        .seg { display: inline-flex; gap: 4px; padding: 4px; background: #f1f5f9; border-radius: 10px; }
        .seg-item { padding: 6px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; color: #64748b; cursor: pointer; }
        .seg-item input { display: none; }
        .seg-item.active { background: #fff; color: #1e40af; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12); }
        .sum-input { padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; width: 170px; }
        .sum-input:focus { border-color: #3498db; }

        .sum-kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
        .sum-kpi { border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; }
        .k-label { font-size: 12px; color: #64748b; font-weight: 600; }
        .k-val   { font-size: 20px; font-weight: 700; color: #0c4a6e; margin-top: 2px; }
        .k-val.ok { color: #166534; }
        .k-val small { font-size: 13px; font-weight: 400; color: #64748b; }

        tr.drv-row { cursor: pointer; }
        .drv-caret { font-size: 11px; color: #94a3b8; margin-right: 4px; transition: transform 0.15s; }
        tr.drv-row.open .drv-caret { transform: rotate(90deg); }
        tr.drv-detail > td { background: #f8fafc; padding: 8px 12px 14px; }
        table.drv-bills { table-layout: auto; font-size: 13px; background: #fff; border-radius: 8px; }
        table.drv-bills th, table.drv-bills td { padding: 8px 10px; text-align: center; }
        table.drv-bills .badge { font-size: 12px; padding: 3px 10px; }

        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge.success { background-color: #dcfce7; color: #166534; }
        .badge.pending { background-color: #dbeafe; color: #1e40af; }
        .badge.processing { background-color: #e0f2fe; color: #0369a1; }
        .badge.danger { background-color: #fee2e2; color: #991b1b; }
        .badge.hold   { background-color: #ffedd5; color: #c2410c; }
        .badge.resend { background-color: #dbeafe; color: #1d4ed8; }
        .sum-resend   { color: #1d4ed8; font-weight: 600; }

        .dur-list { display: inline-flex; flex-direction: column; gap: 2px; min-width: 130px; font-size: 12px; }
        .dur-row { display: flex; justify-content: space-between; gap: 10px; color: #64748b; line-height: 1.6; }
        .dur-row strong { color: #334155; font-weight: 600; }
        .dur-row.slow span, .dur-row.slow strong { color: #b91c1c; font-weight: 700; }
        .dur-row.wait span, .dur-row.wait strong { color: #b45309; font-weight: 600; }
        .dur-row.total { border-top: 1px dashed #e2e8f0; margin-top: 2px; padding-top: 2px; }
        .dur-row.total span, .dur-row.total strong { color: #0c4a6e; font-weight: 700; }
        .dur-na { color: #cbd5e1; }

        .stage-avg { margin-top: 8px; padding-top: 8px; border-top: 1px dashed #e2e8f0; font-size: 12px; color: #64748b; }
        .stage-avg.slow { color: #b91c1c; font-weight: 600; }
        .slow-tag { display: inline-block; margin-left: 6px; padding: 1px 8px; border-radius: 10px; background: #fee2e2; color: #b91c1c; font-size: 11px; font-weight: 700; }
        .stage-card:has(.stage-avg.slow) { border-color: #fecaca; }

        .send-date { color: #1e40af; font-weight: 600; }
        .status-meta { font-size: 11px; color: #64748b; margin-top: 4px; line-height: 1.5; display: table; margin-left: auto; margin-right: auto; text-align: left; }
        .alert-message { padding: 12px; background-color: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 15px; font-size: 14px; }

        .pagination-container { display: flex; justify-content: space-between; align-items: center; margin-top: 22px; padding-top: 18px; border-top: 1px solid #e2e8f0; flex-wrap: wrap; gap: 14px; }
        .pagination-info { font-size: 13.5px; color: #64748b; }
        .pagination-info strong { color: #1e293b; font-weight: 600; }
        .pagination-container nav p, .pagination-container nav > div:first-child { display: none !important; }
        .pagination-container nav { display: flex; justify-content: flex-end; }
        .pagination-container nav ul.pagination, .pagination-container nav > div:last-child > div { display: flex; list-style: none; gap: 4px; align-items: center; margin: 0; padding: 0; flex-wrap: wrap; }
        .pagination-container svg { width: 15px; height: 15px; }
        .pagination-container nav a, .pagination-container nav span:not([aria-current="page"]) span { display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 10px; border-radius: 8px; border: 1px solid #e2e8f0; color: #475569; background-color: #fff; text-decoration: none; font-size: 14px; font-weight: 500; line-height: 1; transition: all 0.15s ease; }
        .pagination-container nav span[aria-current="page"] span { display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 10px; background-color: #3498db !important; color: #fff !important; border: 1px solid #3498db !important; border-radius: 8px; font-size: 14px; font-weight: 600; box-shadow: 0 1px 3px rgba(52, 152, 219, 0.4); }
        .pagination-container nav a:hover { border-color: #3498db; color: #3498db; background-color: #fff; box-shadow: 0 1px 4px rgba(52, 152, 219, 0.18); }
        .pagination-container nav span[aria-disabled="true"] span { display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; border-radius: 8px; background-color: #f8fafc; border: 1px solid #e2e8f0; color: #cbd5e1; cursor: not-allowed; }
        .pagination-container nav span:not([aria-current="page"]):not([aria-disabled="true"]) span { background: transparent; border-color: transparent; box-shadow: none; color: #94a3b8; font-weight: 600; cursor: default; }

        @media (max-width: 640px) {
            .pagination-container { flex-direction: column; align-items: flex-start; }
            .pagination-container nav { justify-content: flex-start; width: 100%; overflow-x: auto; }
        }

        tbody td.td-stage { vertical-align: middle; }
        td.st-current .badge { box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.18); }

        @media (max-width: 1300px) {
            .actions-bar { flex-direction: column; align-items: stretch; }
            .search-box { max-width: none; flex: 0 0 auto; }
            .filter-date-form { margin-left: 0; flex-wrap: wrap; }
            col.col-info  { width: 9%; }
            col.col-stage { width: 15%; }
            col.col-dur   { width: 13%; }
        }

        @media (max-width: 1100px) {
            header h1 { white-space: normal; font-size: 20px; }
            .stats-container { justify-content: flex-start; }
            th.th-stage { font-size: 12px; }
            tbody td:nth-child(n+4) .badge { font-size: 12px; padding: 5px 10px; }
        }

        @media (max-width: 900px) {
            body { padding: 6px; }
            .container { padding: 14px 12px; border-radius: 10px; }
            .top-header-section { flex-direction: column; align-items: flex-start; gap: 10px; }
            .stats-container { width: 100%; gap: 16px; }
            .stat-card { min-width: 0; flex: 1 1 140px; }
            .filter-date-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
            .stage-select, .filter-date-form input[type="date"] { width: 100%; flex: none; }
            .btn-filter, .btn-reset { justify-content: center; }
            .table-responsive { overflow-x: visible; }
            table, tbody, tr, td { display: block; width: 100%; }
            table { table-layout: auto; }
            colgroup, thead { display: none; }
            tbody tr { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 0; margin-bottom: 12px; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: #fff; }
            tbody tr:hover { background: #fff; }
            tbody td { border-bottom: none; padding: 10px 8px; text-align: left; }
            tbody td.td-info { grid-column: span 2; }
            th.th-info, td.td-info { white-space: normal; overflow: visible; text-overflow: clip; padding: 10px 8px; background: #f8fafc; }
            tbody td.td-dur { grid-column: span 6 !important; }
            .dur-list { width: 100%; }
            .stage-inner { display: contents; }
            tbody td.td-stage { grid-column: span 3; display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; border-top: 1px solid #edf2f7; }
            tbody td.td-stage .status-meta { margin-top: 0; margin-left: 0; margin-right: 0; }
            tbody td.td-stage .status-meta { flex: 0 0 100%; min-height: 0 !important; }
            tbody td.td-stage:not(.td-dur) { padding-top: 10px; }
            tbody td[data-label]::before { content: attr(data-label); display: block; font-size: 11px; font-weight: 600; color: #94a3b8; margin-bottom: 2px; }
            tbody td.td-stage[data-label]::before { flex: 0 0 100%; margin-bottom: 0; }
            tbody td.td-stage:nth-child(odd) { border-left: 1px solid #edf2f7; }
            table.sum-table > tbody > tr { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            table.drv-table { table-layout: auto; }
            table.drv-table > tbody > tr.drv-row > td:first-child { padding-left: 8px; }
            table.sum-table > tbody > tr > td { grid-column: span 1; border-top: 1px solid #edf2f7; text-align: left !important; }
            table.sum-table > tbody > tr > td:first-child { grid-column: span 2; background: #f8fafc; }
            table.sum-table > tbody > tr.drv-detail { display: block; margin-top: -10px; }
            table.sum-table > tbody > tr.drv-detail[hidden] { display: none; }
            table.sum-table > tbody > tr.drv-detail > td { grid-column: span 2; }
            table.sum-table .mini-bar { margin-left: 0; }
            table.drv-bills, table.drv-bills tbody, table.drv-bills tr, table.drv-bills td { display: revert; }
            table.drv-bills { display: table; width: 100%; }
            table.drv-bills thead { display: none; }
            table.drv-bills tr { display: grid !important; grid-template-columns: 1fr 1fr; border-bottom: 1px solid #edf2f7; margin: 0; border-radius: 0; border-left: 0; border-right: 0; border-top: 0; }
            table.drv-bills td { display: block; text-align: left; padding: 4px 6px; }
            .sum-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .sum-filter { display: grid; grid-template-columns: 1fr; }
            .seg { display: flex; }
            .seg-item { flex: 1; text-align: center; }
            .sum-input { width: 100%; }
            .view-menu { display: flex; }
            .view-btn { flex: 1; justify-content: center; }
            tbody td.td-empty { grid-column: span 6; text-align: center; }
        }

        @media (max-width: 480px) {
            header h1 { font-size: 17px; }
            .filter-date-form { grid-template-columns: 1fr; }
            tbody td.td-info:first-child { grid-column: span 6; }
            tbody td.td-info { grid-column: span 3; }
            tbody td.td-stage, tbody td.td-empty { grid-column: span 6; }
            tbody td.td-stage:nth-child(odd) { border-left: none; }
            .pagination-container nav a, .pagination-container nav span span { min-width: 34px !important; height: 34px !important; }
        }

        :root {
            --bg: #f8f9fb; --card: #ffffff; --line: #e5e7eb; --line-2: #eef0f3;
            --ink: #1f2937; --muted: #6b7280; --faint: #9ca3af;
            --primary: #1d4ed8; --danger: #dc2626;
            --green-bg: #d1f2dd; --green-fg: #15803d;
            --blue-bg: #dbe6fe; --blue-fg: #1e40af;
            --purple-bg: #ede4fe; --purple-fg: #6d28d9;
            --red-bg: #fde2e2; --red-fg: #b91c1c;
            --yellow-bg: #fdefc3; --yellow-fg: #92400e;
            --gray-bg: #f1f3f6; --gray-fg: #4b5563;
        }

        * { font-family: 'Nunito', 'Sarabun', sans-serif; }
        body { background: var(--bg); color: var(--ink); padding: 16px; }
        .money-val, .stat-value, .k-val, .sum-money, .dur-row strong, .stage-counts strong, td.td-info, .bill-price, .stage-percent { font-variant-numeric: tabular-nums; }
        .container { background: var(--card); border: 1px solid var(--line); border-radius: 12px; box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04); padding: 20px 20px 24px; }
        .top-header-section { background: transparent; margin: 0 0 18px; padding: 0 0 16px; border-bottom: 1px solid var(--line); color: var(--ink); gap: 20px; }
        header.app-title { border: 0; margin: 0; padding: 0; }
        header.app-title h1 { display: flex; align-items: baseline; gap: 8px; font-size: 20px; font-weight: 800; color: var(--ink); }
        header.app-title h1 small { font-size: 13px; font-weight: 600; color: var(--faint); text-transform: none; letter-spacing: 0; }
        .brand-mark { display: none; }
        .top-header-section .view-menu { background: transparent; border: 0; padding: 0; gap: 8px; }
        .top-header-section .view-btn { border-radius: 6px; padding: 7px 16px; font-weight: 700; background: var(--gray-bg); color: var(--gray-fg); }
        .top-header-section .view-btn i { display: inline-block; }
        .top-header-section .view-btn[data-view="list"].active { background: var(--blue-bg); color: var(--blue-fg); }
        .top-header-section .view-btn[data-view="po"].active { background: var(--yellow-bg); color: var(--yellow-fg); }
        .top-header-section .view-btn[data-tab="driver"].active { background: var(--purple-bg); color: var(--purple-fg); }
        .top-header-section .view-btn[data-tab="sale"].active { background: var(--green-bg); color: var(--green-fg); }
        .top-header-section .view-btn.active { box-shadow: none; }
        .top-header-section .view-btn:hover { filter: brightness(0.97); }
        .stat-card { padding: 0 0 0 16px; border-left: 1px solid var(--line); }
        .stat-label { font-size: 12px; color: var(--muted); font-weight: 600; }
        .stat-value { font-size: 20px; font-weight: 800; color: var(--ink); margin-top: 2px; line-height: 1.3; }
        .stat-value.accent { color: var(--green-fg); }
        .stat-unit { font-size: 12px; font-weight: 400; color: var(--faint); }
        .top-header-section .money-val .partial { color: var(--faint); }
        .stage-summary-caption { color: var(--muted); font-size: 12px; }
        .stage-card { background: var(--card); border: 1px solid var(--line); border-radius: 10px; padding: 14px 16px; box-shadow: none; }
        .stage-card-head { color: var(--ink); font-size: 14px; font-weight: 700; }
        .stage-card-head i { color: var(--primary); }
        .stage-percent { color: var(--ink); font-size: 18px; font-weight: 800; }
        .stage-bar { height: 6px; background: var(--line-2); border-radius: 99px; }
        .stage-bar span { background: #22c55e; border-radius: 99px; }
        .stage-counts .done { color: var(--green-fg); }
        .stage-counts .pending { color: var(--muted); }
        .stage-counts i { display: inline-block; }
        .stage-avg { border-top: 1px dashed var(--line); color: var(--muted); }
        .stage-avg.slow { color: var(--red-fg); }
        .slow-tag { background: var(--red-bg); color: var(--red-fg); border-radius: 6px; }
        .stage-card:has(.stage-avg.slow) { border-color: #f5c2c2; box-shadow: none; }
        .search-box input, .stage-select, .filter-date-form input[type="date"], .sum-input { height: 36px; border: 1px solid var(--line); border-radius: 6px; background: #fff; color: var(--ink); font-size: 14px; box-shadow: none; outline: none; }
        .search-box input::placeholder { color: var(--faint); }
        .search-box input:focus, .stage-select:focus, .sum-input:focus, .filter-date-form input[type="date"]:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12); outline: none; }
        .search-box i { color: var(--faint); }
        .stage-select.active { background: var(--blue-bg); border-color: #b9ccfb; color: var(--blue-fg); font-weight: 700; }
        .btn-reset { height: 36px; background: #fff; color: var(--danger); border: 1px solid var(--danger); border-radius: 6px; padding: 0 16px; font-weight: 700; }
        .btn-reset i { display: inline-block; }
        .btn-reset:hover { background: #fef2f2; color: var(--danger); }
        .table-responsive { background: transparent; }
        table { font-size: 14px; }
        th, td { border-bottom: 1px solid var(--line-2); }
        th, thead th { background: #f9fafb; color: var(--muted); font-size: 13px; font-weight: 700; text-transform: none; letter-spacing: 0; padding: 12px 10px; border-bottom: 1px solid var(--line); }
        th.th-stage { color: var(--ink); font-size: 13px; }
        th.th-stage i { display: inline-block; color: var(--primary); }
        tr:hover { background: transparent; }
        tbody tr:hover > td { background: #fafbfc; }
        td.td-info { color: var(--ink); }
        td.td-info strong { font-weight: 700; }
        .status-meta, tbody td:nth-child(n+4) .status-meta { color: var(--muted); font-size: 12px; letter-spacing: 0; line-height: 1.75; margin-top: 6px; }
        .status-meta .bill-price { line-height: 1.75; }
        tbody td.td-stage:not(.td-dur) { vertical-align: top; padding-top: 18px; }
        tbody td.td-stage:not(.td-dur) .status-meta { min-height: calc(3 * 1.75em); }
        /* ป้ายสถานะ + รายละเอียด ชิดซ้ายของช่อง */
        @media (min-width: 901px) {
            /* block + ชิดซ้าย -> ทุกแถวเริ่มที่ขอบซ้ายเดียวกัน ไม่ขยับตามความยาวข้อมูล */
            .stage-inner { display: block; text-align: left; padding-left: 8px; }
            /* ขยับให้ขอบซ้ายตรงกับตัวหนังสือหัวคอลัมน์ (ค่า --stage-pad-N คำนวณด้วย JS ด้านล่าง) */
            #billTable tbody td:nth-child(4) .stage-inner { padding-left: var(--stage-pad-0, 8px); }
            #billTable tbody td:nth-child(5) .stage-inner { padding-left: var(--stage-pad-1, 8px); }
            #billTable tbody td:nth-child(6) .stage-inner { padding-left: var(--stage-pad-2, 8px); }
            #billTable tbody td:nth-child(7) .stage-inner { padding-left: var(--stage-pad-3, 8px); }
            .stage-inner .status-meta { display: block; margin-left: 0; margin-right: 0; }
        }
        .status-meta i { color: var(--faint); width: 14px; text-align: center; }
        .bill-price { color: var(--ink); font-weight: 700; }
        .bill-price i { color: #d97706 !important; }
        .send-date { color: var(--blue-fg); font-weight: 700; }
        .badge, tbody td:nth-child(n+4) .badge { height: auto; border-radius: 6px; padding: 4px 10px; font-size: 12.5px; font-weight: 700; letter-spacing: 0; gap: 5px; line-height: 1.4; }
        .badge i { display: inline-block; }
        .badge::before { content: none; }
        .badge.success { background: var(--green-bg); color: var(--green-fg); }
        .badge.pending { background: var(--gray-bg); color: var(--gray-fg); }
        .badge.processing { background: var(--blue-bg); color: var(--blue-fg); }
        .badge.hold { background: var(--yellow-bg); color: var(--yellow-fg); }
        .badge.resend { background: var(--purple-bg); color: var(--purple-fg); }
        .badge.danger { background: var(--red-bg); color: var(--red-fg); }
        td.st-current .badge { box-shadow: none; }
        .dur-row { color: var(--muted); }
        .dur-row strong { color: var(--ink); font-weight: 700; }
        .dur-row.slow span, .dur-row.slow strong { color: var(--red-fg); font-weight: 700; }
        .dur-row.wait span, .dur-row.wait strong { color: var(--yellow-fg); font-weight: 700; }
        .dur-row.total { border-top: 1px dashed var(--line); }
        .dur-row.total span, .dur-row.total strong { color: var(--blue-fg); font-weight: 800; }
        .pagination-container { border-top: 1px solid var(--line); background: transparent; }
        .pagination-info { color: var(--muted); }
        .pagination-container nav a, .pagination-container nav span:not([aria-current="page"]) span { border: 1px solid var(--line); border-radius: 6px; color: var(--ink); background: #fff; }
        .pagination-container nav span[aria-current="page"] span { background-color: #fff !important; color: var(--primary) !important; border: 1px solid var(--primary) !important; border-radius: 6px; box-shadow: none; font-weight: 800; }
        .pagination-container nav a:hover { border-color: var(--primary); color: var(--primary); box-shadow: none; }
        .seg { background: transparent; border: 0; padding: 0; gap: 8px; }
        .seg-item { border-radius: 6px; background: var(--gray-bg); color: var(--gray-fg); font-weight: 700; padding: 7px 16px; }
        .seg-item.active { background: var(--blue-bg); color: var(--blue-fg); box-shadow: none; }
        .sum-kpi { background: #fff; border: 1px solid var(--line); border-radius: 10px; padding: 14px 16px; }
        .k-label { color: var(--muted); font-weight: 600; }
        .k-val { color: var(--ink); font-weight: 800; font-size: 22px; }
        .k-val.ok { color: var(--green-fg); }
        .k-val small { color: var(--faint); }
        .sum-money { color: var(--ink); font-weight: 800; }
        .sum-money.ok, .sum-ok { color: var(--green-fg); }
        .sum-warn { color: var(--yellow-fg); }
        .sum-cancel { color: var(--red-fg); }
        .sum-resend { color: var(--purple-fg); }
        .sum-zero { color: #d1d5db; }
        table.drv-table > thead > tr > th:nth-child(n+7) { color: var(--ink); }
        tr.drv-row.open > td { background: #fafbfc; }
        tr.drv-detail > td { background: var(--bg); }
        table.drv-bills { background: #fff; border-radius: 8px; }
        .rate { border-radius: 6px; font-weight: 800; }
        .rate.good { background: var(--green-bg); color: var(--green-fg); }
        .rate.mid { background: var(--yellow-bg); color: var(--yellow-fg); }
        .rate.low { background: var(--red-bg); color: var(--red-fg); }

        @media (max-width: 900px) {
            body { padding: 8px; }
            .container { padding: 14px 12px 16px; }
            .stat-card { border-left: 0; padding-left: 0; }
            tbody tr { border-color: var(--line); border-radius: 10px; background: #fff; }
            th.th-info, td.td-info { background: #f9fafb; }
        }

        /* ===== ปรับตาราง PO ให้เหมือนตารางหลัก ===== */
        table.po-table colgroup col.col-info { width: 8%; }
        table.po-table colgroup col.col-stage { width: 22%; }
        
        /* ลบพื้นหลังสีเทาออกจากช่องข้อมูล และบังคับให้เป็นสีขาว */
        table.po-table th.th-info, 
        table.po-table td.td-info {
            padding: 10px 2px;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            background: #ffffff !important; /* เปลี่ยนเป็นสีขาว */
        }
        
        table.po-table td.td-info { color: var(--ink); vertical-align: top; }
        table.po-table td.td-info strong { font-weight: 700; }
        
        table.po-table th.th-stage { font-size: 13px; font-weight: 700; color: var(--ink); }
        table.po-table th.th-stage i { color: var(--primary); margin-right: 4px; }
        
        table.po-table tbody td.td-stage { vertical-align: top; padding-top: 18px; background: #ffffff !important; }
        
        table.po-table tbody td.td-stage .status-meta,
        table.po-table tbody td.td-stage .po-meta {
            min-height: calc(3 * 1.75em);
            font-size: 12px;
            line-height: 1.75;
            color: var(--muted);
            margin-top: 6px;
        }
        table.po-table tbody td.td-stage .badge { font-size: 12.5px; padding: 4px 10px; font-weight: 700; }
        
        /* บังคับให้แถวหลักและแถวขยายเป็นสีขาวเสมอ */
        table.po-table tbody tr.drv-row,
        table.po-table tbody tr.drv-row > td,
        table.po-table tbody tr.drv-detail,
        table.po-table tbody tr.drv-detail > td {
            background: #ffffff !important;
        }

        /* ปิดหรือปรับ Hover effect ให้เป็นสีขาว เพื่อไม่ให้เปลี่ยนสีเมื่อเอาเมาส์ชี้ */
        table.po-table tbody tr.drv-row:hover > td {
            background: #ffffff !important;
        }

        table.po-table .po-days {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 700;
            line-height: 1.4;
            white-space: nowrap;
        }
        table.po-table .po-late   { background: var(--red-bg); color: var(--red-fg); }
        table.po-table .po-today  { background: var(--yellow-bg); color: var(--yellow-fg); }
        table.po-table .po-soon   { background: var(--blue-bg); color: var(--blue-fg); }
        table.po-table .po-future { background: var(--gray-bg); color: var(--gray-fg); }

        @media (max-width: 900px) {
            table.po-table tbody tr { grid-template-columns: repeat(7, minmax(0, 1fr)); background: #ffffff !important; }
            table.po-table tbody td.td-info { grid-column: span 2; background: #ffffff !important; }
            table.po-table tbody td.td-info:first-child { grid-column: span 7; background: #ffffff !important; }
            table.po-table tbody td.td-stage { grid-column: span 7; border-top: 1px solid #edf2f7; background: #ffffff !important; }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="top-header-section">
            <header class="app-title">
                <h1><span class="brand-mark"></span>ติดตามสถานะคำสั่งซื้อและจัดส่ง <small>SO Tracking</small></h1>
            </header>

            <div class="view-menu" role="tablist">
                <button type="button" class="view-btn active" data-view="list" role="tab"><i class="fa-solid fa-list"></i> รายการ</button>
                <button type="button" class="view-btn" data-view="po" role="tab"><i class="fa-solid fa-box-open"></i> PO รับของ</button>
                <button type="button" class="view-btn" data-view="summary" data-tab="driver" role="tab"><i class="fa-solid fa-truck"></i> สรุปคนขับ</button>
                <button type="button" class="view-btn" data-view="summary" data-tab="sale" role="tab"><i class="fa-solid fa-user-tie"></i> สรุป Sale</button>
            </div>

            <div class="stats-container">
                <div class="stat-card total">
                    <div class="stat-label">คำสั่งซื้อทั้งหมด</div>
                    <div class="stat-value">{{ number_format($totalCount ?? 0) }} <span class="stat-unit">รายการ</span></div>
                </div>
                <div class="stat-card today">
                    <div class="stat-label">{{ request('date') ? 'คำสั่งซื้อวันที่' : 'คำสั่งซื้อในวันนี้' }} ({{ \Carbon\Carbon::parse($countDate ?? \Carbon\Carbon::today('Asia/Bangkok'))->format('d/m/Y') }})</div>
                    <div class="stat-value accent">{{ number_format($todayCount ?? 0) }} <span class="stat-unit">รายการ</span></div>
                </div>
                <div class="stat-card money-total">
                    <div class="stat-label">จำนวนเงินทั้งหมด</div>
                    <div class="stat-value">
                        <span id="moneyAll" class="money-val">{{ '฿' . number_format($moneyAllSum['total'] ?? 0, 2) }}@if(($moneyAllSum['missing'] ?? 0) > 0)<span class="partial">(ไม่พบราคา {{ $moneyAllSum['missing'] }} บิล)</span>@endif</span>
                    </div>
                </div>
                <div class="stat-card money-day">
                    <div class="stat-label">{{ request('date') ? 'จำนวนเงินวันที่' : 'จำนวนเงินวันนี้' }} ({{ \Carbon\Carbon::parse($countDate ?? \Carbon\Carbon::today('Asia/Bangkok'))->format('d/m/Y') }})</div>
                    <div class="stat-value accent">
                        <span id="moneyDay" class="money-val">{{ '฿' . number_format($moneyDaySum['total'] ?? 0, 2) }}@if(($moneyDaySum['missing'] ?? 0) > 0)<span class="partial">(ไม่พบราคา {{ $moneyDaySum['missing'] }} บิล)</span>@endif</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="stage-summary">
            <div class="stage-summary-caption">
                @if(request('date') || request('search'))
                    สรุปตามเงื่อนไขที่กรอง @if(request('date')) (วันที่ {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}) @endif
                @else สรุปทั้งระบบ @endif
                · {{ number_format($activeCount ?? 0) }} รายการ
                @if(!empty($avgTotal)) · เปิดบิลจนส่งสำเร็จ เฉลี่ย <strong>{{ $avgTotal }}</strong> @endif
                @if(($cancelledCount ?? 0) > 0) (ไม่รวมยกเลิก {{ number_format($cancelledCount) }} รายการ) @endif
            </div>
            <div class="stage-grid">
                @foreach($stageStats ?? [] as $stage)
                    <div class="stage-card">
                        <div class="stage-card-head">
                            <span><i class="fa-solid {{ $stage['icon'] }}"></i>{{ $stage['label'] }}</span>
                            <span class="stage-percent">{{ $stage['percent'] }}%</span>
                        </div>
                        <div class="stage-bar"><span style="width: {{ $stage['percent'] }}%;"></span></div>
                        <div class="stage-counts">
                            <span class="done"><i class="fa-solid fa-check"></i> เสร็จแล้ว<strong>{{ number_format($stage['done']) }}</strong></span>
                            <span class="pending"><i class="fa-solid fa-clock"></i> ยังไม่เสร็จ<strong>{{ number_format($stage['pending']) }}</strong></span>
                        </div>
                        <div class="stage-avg {{ !empty($stage['slow']) ? 'slow' : '' }}">
                            @if($loop->first) <i class="fa-solid fa-play"></i> จุดเริ่มนับเวลา
                            @elseif(!empty($stage['avg'])) <i class="fa-solid fa-stopwatch"></i> เฉลี่ย {{ $stage['avg'] }} @if(!empty($stage['slow']))<span class="slow-tag">ช้าสุด</span>@endif
                            @else <i class="fa-solid fa-stopwatch"></i> ยังไม่มีข้อมูลเวลา @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div id="listView">
        <form action="{{ route('admin.dashboardadmin') }}" method="GET" class="actions-bar" id="filterForm">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="พิมพ์เพื่อค้นหา รหัสลูกค้า, รหัส SO, เลขบิล..." autocomplete="off">
            </div>
            <div class="filter-date-form">
                <select name="bill_status" class="stage-select {{ request('bill_status') ? 'active' : '' }}" onchange="this.form.submit()">
                    <option value="">เปิดบิลส่งของ: ทั้งหมด</option>
                    <option value="done" @selected(request('bill_status') === 'done')>เปิดบิลแล้ว</option>
                    <option value="pending" @selected(request('bill_status') === 'pending')>รอดำเนินการ</option>
                    <option value="cancel" @selected(request('bill_status') === 'cancel')>ยกเลิก</option>
                </select>
                <select name="pick_status" class="stage-select {{ request('pick_status') ? 'active' : '' }}" onchange="this.form.submit()">
                    <option value="">จัดสินค้า: ทั้งหมด</option>
                    <option value="done" @selected(request('pick_status') === 'done')>จัดสินค้าแล้ว</option>
                    <option value="pending" @selected(request('pick_status') === 'pending')>รอดำเนินการ</option>
                </select>
                <select name="route_status" class="stage-select {{ request('route_status') ? 'active' : '' }}" onchange="this.form.submit()">
                    <option value="">จัดเส้นทาง: ทั้งหมด</option>
                    <option value="done" @selected(request('route_status') === 'done')>จัดเส้นทางแล้ว</option>
                    <option value="pending" @selected(request('route_status') === 'pending')>รอดำเนินการ</option>
                </select>
                <select name="deli_status" class="stage-select {{ request('deli_status') ? 'active' : '' }}" onchange="this.form.submit()">
                    <option value="">ส่งสินค้า: ทั้งหมด</option>
                    <option value="success" @selected(request('deli_status') === 'success')>จัดส่งสำเร็จ</option>
                    <option value="hold" @selected(request('deli_status') === 'hold')>ค้างบิล</option>
                    <option value="resend" @selected(request('deli_status') === 'resend')>ส่งใหม่ (จ่ายงานใหม่)</option>
                    <option value="wrong" @selected(request('deli_status') === 'wrong')>สินค้าผิด</option>
                    <option value="pending" @selected(request('deli_status') === 'pending')>รอดำเนินการ</option>
                </select>
                <input type="date" name="date" value="{{ request('date') }}" min="{{ $startDate ?? '2026-09-19' }}" onchange="this.form.submit()">
                <a href="{{ route('admin.dashboardadmin') }}" class="btn-reset"><i class="fa-solid fa-rotate-right"></i> รีเซ็ต</a>
            </div>
        </form>

        @if(isset($message))
            <div class="alert-message"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
        @endif

        <div class="table-responsive">
            <table id="billTable">
                <colgroup>
                    <col class="col-info"><col class="col-info"><col class="col-info">
                    <col class="col-stage"><col class="col-stage"><col class="col-stage"><col class="col-stage"><col class="col-dur">
                </colgroup>
                <thead>
                    <tr>
                        <th class="th-info">รหัสลูกค้า</th>
                        <th class="th-info">รหัส SO</th>
                        <th class="th-info">เลขบิล</th>
                        <th class="th-stage"><i class="fa-solid fa-file-invoice"></i> เปิดบิลส่งของ</th>
                        <th class="th-stage"><i class="fa-solid fa-box-open"></i> จัดสินค้า</th>
                        <th class="th-stage"><i class="fa-solid fa-route"></i> จัดเส้นทาง</th>
                        <th class="th-stage"><i class="fa-solid fa-truck"></i> ส่งสินค้า</th>
                        <th class="th-stage"><i class="fa-solid fa-stopwatch"></i> ระยะเวลา</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($bill as $item)
                    @php
                        $isCancelled = isset($item->statuspdf) && $item->statuspdf == 6;
                        $stepDone = [
                            isset($item->statuspdf) && in_array((string) $item->statuspdf, ['1', '2'], true),
                            (bool) $item->pick_done,
                            (bool) $item->route_done,
                            ($item->statusdeli ?? '') === 'จัดส่งสำเร็จ',
                        ];
                        $curStep = array_search(false, $stepDone, true);
                        $stepCls = function ($i) use ($stepDone, $curStep, $isCancelled) {
                            if ($isCancelled) return ' st-cancel';
                            $c = $stepDone[$i] ? ' st-done' : ($i === $curStep ? ' st-current' : ' st-todo');
                            if ($i > 0 && ($stepDone[$i] || $i === $curStep)) $c .= $stepDone[$i] ? ' l-on' : ' l-cur';
                            if ($i < 3 && ($stepDone[$i + 1] || $i + 1 === $curStep)) $c .= $stepDone[$i + 1] ? ' r-on' : ' r-cur';
                            if ($i === 0) $c .= ' first';
                            if ($i === 3) $c .= ' last';
                            return $c;
                        };
                    @endphp
                    <tr>
                        <td class="td-info" data-label="รหัสลูกค้า"><strong>{{ $item->customer_id ?? '-' }}</strong></td>
                        <td class="td-info" data-label="รหัส SO"><strong>{{ $item->so_id ?? '-' }}</strong></td>
                        <td class="td-info" data-label="เลขบิล">{{ $item->billid ?? '-' }}</td>

                        <td class="td-stage{{ $stepCls(0) }}" data-label="เปิดบิลส่งของ">
                            <div class="stage-inner">
                            @if($isCancelled)
                                <span class="badge danger"><i class="fa-solid fa-ban"></i> ยกเลิก</span>
                            @elseif(isset($item->statuspdf) && in_array((string) $item->statuspdf, ['1', '2'], true))
                                <span class="badge success"><i class="fa-solid fa-check"></i> เปิดบิลแล้ว</span>
                            @else
                                <span class="badge pending"><i class="fa-solid fa-clock"></i> รอดำเนินการ</span>
                            @endif
                            @if(isset($item->time) || !empty($item->so_id) || !empty($item->sale_name))
                                <div class="status-meta">
                                    @if(isset($item->time)) <i class="fa-regular fa-clock"></i> {{ \Illuminate\Support\Str::substr((string) $item->time, 0, 16) }} @endif
                                    @if(!empty($item->sale_name)) <br><i class="fa-solid fa-user-tie"></i> ผู้ขาย: {{ $item->sale_name }} @endif
                                    @if(!empty($item->so_id))
                                        @if(!is_null($item->price)) <div class="bill-price" title="รวม VAT 7% แล้ว"><i class="fa-solid fa-coins"></i> ฿{{ number_format($item->price, 2) }}</div>
                                        @else <div class="bill-price na">ไม่พบราคา</div> @endif
                                    @endif
                                </div>
                            @endif
                            </div>
                        </td>

                        <td class="td-stage{{ $stepCls(1) }}" data-label="จัดสินค้า">
                            <div class="stage-inner">
                            @if($isCancelled) <span class="badge danger"><i class="fa-solid fa-ban"></i> ยกเลิก</span>
                            @elseif($item->pick_done)
                                <span class="badge success"><i class="fa-solid fa-box-open"></i> จัดสินค้าแล้ว</span>
                                @if(!empty($item->pick_time) || !empty($item->pick_name))
                                    <div class="status-meta">
                                        @if(!empty($item->pick_time)) <i class="fa-regular fa-clock"></i> {{ $item->pick_time }} @endif
                                        @if(!empty($item->pick_name)) <br><i class="fa-solid fa-user"></i> ผู้จัด: {{ $item->pick_name }} @endif
                                    </div>
                                @endif
                            @else <span class="badge pending"><i class="fa-solid fa-clock"></i> รอดำเนินการ</span> @endif
                            </div>
                        </td>

                        <td class="td-stage{{ $stepCls(2) }}" data-label="จัดเส้นทาง">
                            <div class="stage-inner">
                            @if($isCancelled) <span class="badge danger"><i class="fa-solid fa-ban"></i> ยกเลิก</span>
                            @else
                                @if($item->route_done) <span class="badge success"><i class="fa-solid fa-route"></i> จัดเส้นทางแล้ว</span>
                                @else <span class="badge pending"><i class="fa-solid fa-clock"></i> รอดำเนินการ</span> @endif
                                @php
                                    $routeLines = [];
                                    if ($item->route_done && !empty($item->route_time)) $routeLines[] = '<i class="fa-regular fa-clock"></i> ' . e($item->route_time);
                                    if ($item->route_done && !empty($item->route_name)) $routeLines[] = '<i class="fa-solid fa-user"></i> ผู้จัด: ' . e($item->route_name);
                                    if (!empty($item->route_send_date)) $routeLines[] = '<span class="send-date"><i class="fa-regular fa-calendar"></i> ส่งวันที่ ' . e($item->route_send_date) . '</span>';
                                @endphp
                                @if(count($routeLines)) <div class="status-meta">{!! implode('<br>', $routeLines) !!}</div> @endif
                            @endif
                            </div>
                        </td>

                        <td class="td-stage{{ $stepCls(3) }}" data-label="ส่งสินค้า">
                            <div class="stage-inner">
                            @if($isCancelled) <span class="badge danger"><i class="fa-solid fa-ban"></i> ยกเลิก</span>
                            @elseif(!empty($item->statusdeli))
                                @php
                                    $deliBadge = match(true) {
                                        $item->statusdeli === 'จัดส่งสำเร็จ' => 'success',
                                        $item->statusdeli === 'สินค้าผิด' => 'danger',
                                        $item->statusdeli === 'ค้างบิล' => 'hold',
                                        str_starts_with((string) $item->statusdeli, 'ส่งใหม่') => 'resend',
                                        default => 'processing',
                                    };
                                @endphp
                                <span class="badge {{ $deliBadge }}"><i class="fa-solid fa-truck"></i> {{ $item->statusdeli }}</span>
                                @if(!empty($item->deli_time) || !empty($item->deli_name) || !empty($item->deli_receiver))
                                    <div class="status-meta">
                                        @if(!empty($item->deli_time)) <i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($item->deli_time)->format('Y-m-d H:i') }} @endif
                                        @if(!empty($item->deli_name)) <br><i class="fa-solid fa-user"></i> คนขับ: {{ $item->deli_name }} @endif
                                        @if(!empty($item->deli_receiver)) <br><i class="fa-solid fa-user-check"></i> คนรับ: {{ $item->deli_receiver }} @endif
                                    </div>
                                @endif
                            @else
                                <span class="badge pending"><i class="fa-solid fa-clock"></i> รอดำเนินการ</span>
                                @if(!empty($item->deli_name)) <div class="status-meta"><i class="fa-solid fa-user"></i> คนขับ: {{ $item->deli_name }}</div> @endif
                            @endif
                            </div>
                        </td>

                        <td class="td-stage td-dur" data-label="ระยะเวลา">
                            @if($isCancelled) <span class="dur-na">-</span>
                            @else
                                @php
                                    $segs = ['pick' => 'จัดสินค้า', 'route' => 'จัดเส้นทาง', 'deli' => 'ส่งสินค้า'];
                                    $stageName = ['เปิดบิล', 'จัดสินค้า', 'จัดเส้นทาง', 'ส่งสินค้า'];
                                @endphp
                                <div class="dur-list">
                                    @foreach($segs as $k => $lbl)
                                        @if($item->dur[$k])
                                            <div class="dur-row {{ $item->dur_slow === $k ? 'slow' : '' }}">
                                                <span>{{ $k === 'deli' ? 'คนขับ → คนรับบิล' : $lbl }}</span><strong>+{{ $item->dur[$k] }}</strong>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if($curStep !== false && $item->wait)
                                        <div class="dur-row wait"><span><i class="fa-solid fa-hourglass-half"></i> รอ{{ $stageName[$curStep] }}</span><strong>{{ $item->wait }}</strong></div>
                                    @endif
                                    @if($item->dur['total'])
                                        <div class="dur-row total"><span><i class="fa-solid fa-flag-checkered"></i> รวม</span><strong>{{ $item->dur['total'] }}</strong></div>
                                    @endif
                                    @if(!$item->dur['pick'] && !$item->dur['route'] && !$item->dur['deli'] && !$item->wait && !$item->dur['total'])
                                        <span class="dur-na">-</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="td-empty" style="text-align: center; color: #94a3b8; padding: 20px;">ไม่พบข้อมูลคำสั่งซื้อในระบบ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            <div class="pagination-info">แสดงผล <strong>{{ $bill->firstItem() ?? 0 }}</strong> ถึง <strong>{{ $bill->lastItem() ?? 0 }}</strong> จากทั้งหมด <strong>{{ $bill->total() }}</strong> รายการ</div>
            <div>{{ $bill->links() }}</div>
        </div>
        </div>

        <!-- ===================== มุมมอง PO รับของ ===================== -->
        <div id="poView" hidden>
            <form method="GET" action="{{ route('admin.dashboardadmin') }}#po" class="sum-filter" id="poForm">
                <input type="hidden" name="view" value="po">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="po_search" id="poSearch" value="{{ $poSearch ?? '' }}" placeholder="ค้นหา เลข PO, รหัส SO..." autocomplete="off">
                </div>
                <input type="date" name="po_date" id="poDate" class="sum-input" value="{{ $poDate ?? '' }}" title="วันนัดรับ (ค่าเริ่มต้น = วันนี้)" onchange="this.form.submit()">
                <button type="button" class="btn-reset" style="color:#1d4ed8;border-color:#1d4ed8;"
                        title="แสดง PO ทุกวันนัดรับ"
                        onclick="document.getElementById('poDate').value='';this.form.submit()"><i class="fa-regular fa-calendar"></i> ทุกวัน</button>
                <select name="po_days" class="stage-select {{ ($poDays ?? '') !== '' ? 'active' : '' }}" onchange="this.form.submit()">
                    <option value="">สถานะวันนัดรับ: ทั้งหมด</option>
                    <option value="late" @selected(($poDays ?? '') === 'late')>เลยกำหนด</option>
                    <option value="today" @selected(($poDays ?? '') === 'today')>วันนี้</option>
                    <option value="soon" @selected(($poDays ?? '') === 'soon')>อีก 1-3 วัน</option>
                    <option value="future" @selected(($poDays ?? '') === 'future')>มากกว่า 3 วัน</option>
                </select>
                <select name="po_dispatch" class="stage-select {{ ($poDispatch ?? '') !== '' ? 'active' : '' }}" onchange="this.form.submit()">
                    <option value="">การส่ง / จ่ายงาน: ทั้งหมด</option>
                    <option value="none" @selected(($poDispatch ?? '') === 'none')>ยังไม่จ่ายงาน</option>
                    <option value="assigned" @selected(($poDispatch ?? '') === 'assigned')>จ่ายงานแล้ว (รอส่ง)</option>
                    <option value="sent" @selected(($poDispatch ?? '') === 'sent')>ส่งเสร็จแล้ว</option>
                </select>
                <select name="po_status" class="stage-select {{ ($poStatus ?? '') !== '' ? 'active' : '' }}" onchange="this.form.submit()">
                    <option value="">สถานะรับของ: ทั้งหมด</option>
                    <option value="wait" @selected(($poStatus ?? '') === 'wait')>รอรับของ</option>
                    <option value="partial" @selected(($poStatus ?? '') === 'partial')>รับบางส่วน</option>
                    <option value="done" @selected(($poStatus ?? '') === 'done')>รับครบแล้ว</option>
                    <option value="cancel" @selected(($poStatus ?? '') === 'cancel')>ยกเลิก</option>
                </select>
                <a href="{{ route('admin.dashboardadmin') }}?view=po#po" class="btn-reset"><i class="fa-solid fa-rotate-right"></i> รีเซ็ต</a>
            </form>

            @if(!empty($poError))
                <div class="po-alert"><i class="fa-solid fa-triangle-exclamation"></i> {{ $poError }}</div>
            @endif

            <div class="sum-box">
                <div class="sum-kpis">
                    <div class="sum-kpi"><div class="k-label">PO ไปรับเองทั้งหมด</div><div class="k-val">{{ number_format($poCounts['all'] ?? 0) }} <small>PO</small></div></div>
                    <div class="sum-kpi"><div class="k-label">รอรับของ / ยังไม่จ่ายงาน</div><div class="k-val">{{ number_format($poCounts['wait'] ?? 0) }} <small>/ {{ number_format($poCounts['unassigned'] ?? 0) }}</small></div></div>
                    <div class="sum-kpi"><div class="k-label">รับบางส่วน</div><div class="k-val sum-warn">{{ number_format($poCounts['partial'] ?? 0) }} <small>PO</small></div></div>
                    <div class="sum-kpi"><div class="k-label">รับครบแล้ว / ยกเลิก</div><div class="k-val ok">{{ number_format($poCounts['done'] ?? 0) }} <small>/ {{ number_format($poCounts['cancel'] ?? 0) }}</small></div></div>
                </div>

                <div class="table-responsive">
                    <table class="sum-table drv-table po-table">
                        <colgroup>
                            <col class="col-info"><col class="col-info"><col class="col-info"><col class="col-info">
                            <col class="col-stage"><col class="col-stage"><col class="col-stage">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="th-info">เลข PO</th>
                                <th class="th-info">รหัส SO</th>
                                <th class="th-info">เวลาสร้าง PO</th>
                                <th class="th-info">วันนัดรับ</th>
                                <th class="th-stage"><i class="fa-solid fa-calendar-day"></i> สถานะวันนัดรับ</th>
                                <th class="th-stage"><i class="fa-solid fa-truck"></i> สถานะการส่ง / จ่ายงาน</th>
                                <th class="th-stage"><i class="fa-solid fa-box-open"></i> สถานะรับของ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($poList ?? []) as $i => $po)
                                <tr class="drv-row" data-target="po-{{ $i }}" title="กดเพื่อดูรายการสินค้า">
                                    <td class="td-info" data-label="เลข PO"><strong><i class="fa-solid fa-chevron-right drv-caret"></i> PO{{ $po->PONum }}</strong></td>
                                    
                                    {{-- ✅ เอาชื่อผู้ขายออกจากตรงนี้แล้ว --}}
                                    <td class="td-info" data-label="รหัส SO">
                                        <strong>{{ $po->SONum ?? '-' }}</strong>
                                    </td>

                                    <td class="td-info" data-label="เวลาสร้าง PO">
                                        @if(!empty($po->docu_date)) {{ $po->docu_date }}
                                        @else <span class="sum-zero">-</span> @endif
                                    </td>
                                    <td class="td-info" data-label="วันนัดรับ">{{ $po->pickup_date ?? '-' }}</td>

                                    {{-- ✅ ย้ายชื่อผู้ขายมาแสดงตรงนี้แทน --}}
                                    <td class="td-stage" data-label="สถานะวันนัดรับ">
                                        @if(!empty($po->days_label))
                                            <span class="po-days {{ $po->days_class }}">
                                                @if($po->days_class === 'po-late') <i class="fa-solid fa-triangle-exclamation"></i>
                                                @elseif($po->days_class === 'po-today') <i class="fa-solid fa-calendar-day"></i>
                                                @elseif($po->days_class === 'po-soon') <i class="fa-solid fa-hourglass-half"></i>
                                                @else <i class="fa-regular fa-calendar"></i> @endif
                                                {{ $po->days_label }}
                                            </span>
                                        @else
                                            <span class="sum-zero">-</span>
                                        @endif

                                        @if(!empty($po->sale_name) || !empty($po->SaleName))
                                            <div class="po-meta">
                                                <i class="fa-solid fa-user-tie"></i> ผู้ขาย: {{ $po->sale_name ?? $po->SaleName }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="td-stage" data-label="สถานะการส่ง / จ่ายงาน">
                                        @if(!empty($po->transport_sent_complete))
                                            <span class="badge success"><i class="fa-solid fa-truck"></i> ส่งเสร็จแล้ว</span>
                                            <div class="po-meta">
                                                @if(!empty($po->transport_sent_time))<i class="fa-regular fa-clock"></i> {{ $po->transport_sent_time }}<br>@endif
                                                @if(!empty($po->transport_driver))<i class="fa-solid fa-user"></i> คนขับ: {{ $po->transport_driver }}@endif
                                            </div>
                                        @elseif(!empty($po->is_transport_sent))
                                            <span class="badge processing"><i class="fa-solid fa-truck"></i> จ่ายงานแล้ว (รอส่ง)</span>
                                            <div class="po-meta">
                                                @if(!empty($po->transport_assign_time))<i class="fa-regular fa-clock"></i> {{ $po->transport_assign_time }}<br>@endif
                                                @if(!empty($po->transport_driver))<i class="fa-solid fa-user"></i> คนขับ: {{ $po->transport_driver }}@endif
                                            </div>
                                        @elseif($po->assigned)
                                            <span class="badge processing"><i class="fa-solid fa-truck"></i> {{ $po->picker ?: 'จ่ายงานแล้ว' }}</span>
                                            <div class="po-meta">
                                                @if($po->go_date)<i class="fa-regular fa-calendar"></i> ไปรับ {{ $po->go_date }}<br>@endif
                                                @if($po->assign_time)<i class="fa-regular fa-clock"></i> {{ $po->assign_time }}@endif
                                                @if($po->assign_by) · {{ $po->assign_by }}@endif
                                            </div>
                                        @else
                                            <span class="badge pending"><i class="fa-solid fa-clock"></i> ยังไม่จ่ายงาน</span>
                                        @endif
                                    </td>

                                    <td class="td-stage" data-label="สถานะรับของ">
                                        <span class="badge {{ $po->st['badge'] }}">
                                            <i class="fa-solid {{ ['done' => 'fa-check', 'cancel' => 'fa-ban', 'partial' => 'fa-box-open'][$po->st['key']] ?? 'fa-clock' }}"></i> {{ $po->st['label'] }}
                                        </span>
                                        @if($po->receive_time && in_array($po->st['key'], ['done', 'partial'], true))
                                            <div class="po-meta"><i class="fa-regular fa-clock"></i> {{ $po->receive_time }}</div>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="drv-detail" id="po-{{ $i }}" hidden>
                                    <td colspan="7">
                                        <table class="drv-bills">
                                            <thead><tr><th style="width:60px">#</th><th style="text-align:left">สินค้า</th><th style="width:120px">จำนวน</th></tr></thead>
                                            <tbody>
                                                @forelse($po->items as $n => $it)
                                                    <tr><td>{{ $n + 1 }}</td><td style="text-align:left">{{ $it['name'] }}</td><td>{{ $it['qty'] }}</td></tr>
                                                @empty
                                                    <tr><td colspan="3" style="color:#94a3b8">ไม่พบรายการสินค้า</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="td-empty" style="text-align:center; color:#94a3b8; padding:24px;">ไม่พบ PO ไปรับของเอง</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===================== มุมมองสรุป ===================== -->
        <div id="summaryView" hidden>
            <form method="GET" action="{{ route('admin.dashboardadmin') }}#summary" class="sum-filter" id="sumForm">
                <input type="hidden" name="view" value="summary">
                <input type="hidden" name="sum_tab" id="sumTabInput" value="{{ $sumTab ?? 'driver' }}">
                <div class="seg" role="radiogroup">
                    @foreach(['day' => 'รายวัน', 'month' => 'รายเดือน', 'year' => 'รายปี'] as $k => $lbl)
                        <label class="seg-item {{ ($sumPeriod ?? 'day') === $k ? 'active' : '' }}">
                            <input type="radio" name="sum_period" value="{{ $k }}" @checked(($sumPeriod ?? 'day') === $k)> {{ $lbl }}
                        </label>
                    @endforeach
                </div>
                <input type="{{ ['day' => 'date', 'month' => 'month', 'year' => 'number'][$sumPeriod ?? 'day'] }}"
                       name="sum_date" id="sumDate" class="sum-input"
                       value="{{ $sumDate ?? '' }}" min="{{ ['day' => $startDate ?? '2026-09-19', 'month' => substr($startDate ?? '2026-09-19', 0, 7), 'year' => substr($startDate ?? '2026-09-19', 0, 4)][$sumPeriod ?? 'day'] }}" max="{{ ($sumPeriod ?? 'day') === 'year' ? 2100 : '' }}">
                <select name="sum_driver" data-tab-only="driver" class="stage-select {{ ($sumDriver ?? '') !== '' ? 'active' : '' }}">
                    <option value="">คนขับ: ทั้งหมด</option>
                    @foreach($driverOptions ?? [] as $name)
                        <option value="{{ $name }}" @selected(($sumDriver ?? '') === $name)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="sum_sale" data-tab-only="sale" class="stage-select {{ ($sumSale ?? '') !== '' ? 'active' : '' }}">
                    <option value="">Sale: ทั้งหมด</option>
                    @foreach($saleOptions ?? [] as $name)
                        <option value="{{ $name }}" @selected(($sumSale ?? '') === $name)>{{ $name }}</option>
                    @endforeach
                </select>
            </form>

            <div id="sumDriverBox" class="sum-box" data-tab-box="driver">
                <div class="sum-kpis">
                    <div class="sum-kpi"><div class="k-label">จำนวนงานส่ง ({{ $sumLabel ?? '' }})</div><div class="k-val">{{ number_format($sumTotals['jobs'] ?? 0) }} <small>งาน</small></div></div>
                    <div class="sum-kpi"><div class="k-label">ส่งสำเร็จ</div><div class="k-val ok">{{ number_format($sumTotals['success'] ?? 0) }} <small>/ {{ number_format($sumTotals['jobs'] ?? 0) }}</small></div></div>
                    <div class="sum-kpi"><div class="k-label">ยอดเงินที่ส่งทั้งหมด</div><div class="k-val"><span class="sum-money money-val">{{ '฿' . number_format($sumTotals['money']['total'] ?? 0, 2) }}@if(($sumTotals['money']['missing'] ?? 0) > 0)<span class="partial">(ไม่พบราคา {{ $sumTotals['money']['missing'] }})</span>@endif</span></div></div>
                    <div class="sum-kpi"><div class="k-label">ยอดเงินที่ส่งสำเร็จ</div><div class="k-val ok"><span class="sum-money money-val">{{ '฿' . number_format($sumTotals['money_ok']['total'] ?? 0, 2) }}@if(($sumTotals['money_ok']['missing'] ?? 0) > 0)<span class="partial">(ไม่พบราคา {{ $sumTotals['money_ok']['missing'] }})</span>@endif</span></div></div>
                </div>
                <div class="table-responsive">
                    <table class="sum-table drv-table">
                        <colgroup>
                            <col style="width: 12%"><col class="c-num"><col class="c-num"><col class="c-num"><col class="c-num"><col class="c-num">
                            <col style="width: 16%"><col style="width: 16%"><col style="width: 8%"><col style="width: 8%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="c-name">คนขับ</th><th>จำนวนงาน</th><th>ส่งสำเร็จ</th><th>ค้างบิล</th><th>ส่งใหม่</th><th>สินค้าผิด</th>
                                <th>ยอดเงินทั้งหมด</th><th>ยอดเงินส่งสำเร็จ</th><th>เฉลี่ย/วัน</th><th>% ส่งสำเร็จ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($driverSummary ?? [] as $i => $dr)
                                <tr class="drv-row" data-target="drv-{{ $i }}" title="กดเพื่อดูรายการบิล">
                                    <td data-label="คนขับ"><strong><i class="fa-solid fa-chevron-right drv-caret"></i> {{ $dr['name'] }}</strong></td>
                                    <td data-label="จำนวนงาน"><strong>{{ number_format($dr['jobs']) }}</strong></td>
                                    <td data-label="ส่งสำเร็จ" class="{{ $dr['success'] ? 'sum-ok' : 'sum-zero' }}">{{ number_format($dr['success']) }}</td>
                                    <td data-label="ค้างบิล" class="{{ $dr['hold'] ? 'sum-warn' : 'sum-zero' }}">{{ number_format($dr['hold']) }}</td>
                                    <td data-label="ส่งใหม่" class="{{ $dr['resend'] ? 'sum-resend' : 'sum-zero' }}">{{ number_format($dr['resend']) }}</td>
                                    <td data-label="สินค้าผิด" class="{{ $dr['wrong'] ? 'sum-cancel' : 'sum-zero' }}">{{ number_format($dr['wrong']) }}</td>
                                    <td data-label="ยอดเงินทั้งหมด"><span class="sum-money money-val">{{ '฿' . number_format($dr['money']['total'] ?? 0, 2) }}@if(($dr['money']['missing'] ?? 0) > 0)<span class="partial">(ไม่พบราคา {{ $dr['money']['missing'] }})</span>@endif</span></td>
                                    <td data-label="ยอดเงินส่งสำเร็จ"><span class="sum-money money-val ok">{{ '฿' . number_format($dr['money_ok']['total'] ?? 0, 2) }}@if(($dr['money_ok']['missing'] ?? 0) > 0)<span class="partial">(ไม่พบราคา {{ $dr['money_ok']['missing'] }})</span>@endif</span></td>
                                    <td data-label="เฉลี่ย/วัน" title="{{ $dr['jobs'] }} งาน ใน {{ $dr['work_days'] }} วันที่มีงาน"><strong>{{ rtrim(rtrim(number_format($dr['per_day'], 1), '0'), '.') }}</strong> <small class="sum-unit">งาน</small></td>
                                    <td data-label="% ส่งสำเร็จ"><span class="rate {{ $dr['rate'] >= 90 ? 'good' : ($dr['rate'] >= 70 ? 'mid' : 'low') }}">{{ $dr['rate'] }}%</span></td>
                                </tr>
                                <tr class="drv-detail" id="drv-{{ $i }}" hidden>
                                    <td colspan="10">
                                        <table class="drv-bills">
                                            <thead><tr><th>วันที่ส่ง</th><th>รหัส SO</th><th>เลขบิล</th><th>รหัสลูกค้า</th><th>สถานะ</th><th>เวลายืนยัน</th></tr></thead>
                                            <tbody>
                                                @foreach($dr['bills'] as $bl)
                                                    @php $cls = ['จัดส่งสำเร็จ' => 'success', 'ค้างบิล' => 'hold', 'ส่งใหม่' => 'resend', 'สินค้าผิด' => 'danger'][$bl['status']] ?? 'processing'; @endphp
                                                    <tr>
                                                        <td>{{ $bl['date'] ? \Carbon\Carbon::parse($bl['date'])->format('d/m/Y') : '-' }}</td>
                                                        <td><strong>{{ $bl['so'] ?: '-' }}</strong></td>
                                                        <td>{{ $bl['po'] ?: '-' }}</td>
                                                        <td>{{ $bl['customer'] ?: '-' }}</td>
                                                        <td><span class="badge {{ $cls }}">{{ $bl['status'] }}</span></td>
                                                        <td>{{ $bl['time'] ?: '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="td-empty" style="text-align:center; color:#94a3b8; padding:24px;">ไม่มีงานส่งใน{{ $sumLabel ?? 'ช่วงที่เลือก' }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="sumSaleBox" class="sum-box" data-tab-box="sale">
                <div class="sum-kpis">
                    <div class="sum-kpi"><div class="k-label">เปิดบิล ({{ $sumLabel ?? '' }})</div><div class="k-val">{{ number_format($saleTotals['jobs'] ?? 0) }} <small>งาน</small></div></div>
                    <div class="sum-kpi"><div class="k-label">สำเร็จ / ไม่สำเร็จ</div><div class="k-val"><span class="ok-num">{{ number_format($saleTotals['success'] ?? 0) }}</span> <small>/</small> <span class="bad-num">{{ number_format(($saleTotals['jobs'] ?? 0) - ($saleTotals['success'] ?? 0)) }}</span></div></div>
                    <div class="sum-kpi"><div class="k-label">ยอดเงินที่เปิดบิลทั้งหมด</div><div class="k-val"><span class="sum-money money-val">{{ '฿' . number_format($saleTotals['money']['total'] ?? 0, 2) }}@if(($saleTotals['money']['missing'] ?? 0) > 0)<span class="partial">(ไม่พบราคา {{ $saleTotals['money']['missing'] }})</span>@endif</span></div></div>
                    <div class="sum-kpi"><div class="k-label">ยอดเงินที่ส่งสำเร็จ</div><div class="k-val ok"><span class="sum-money money-val">{{ '฿' . number_format($saleTotals['money_ok']['total'] ?? 0, 2) }}@if(($saleTotals['money_ok']['missing'] ?? 0) > 0)<span class="partial">(ไม่พบราคา {{ $saleTotals['money_ok']['missing'] }})</span>@endif</span></div></div>
                </div>
                <div class="table-responsive">
                    <table class="sum-table drv-table">
                        <colgroup>
                            <col style="width: 14%"><col class="c-num"><col class="c-num"><col class="c-num"><col style="width: 27%"><col style="width: 27%"><col style="width: 8%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="c-name">Sale</th><th>เปิดบิล</th><th>สำเร็จ</th><th>ไม่สำเร็จ</th><th>ยอดเงินทั้งหมด</th><th>ยอดเงินส่งสำเร็จ</th><th>% ส่งสำเร็จ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($saleSummary ?? [] as $i => $sa)
                                <tr class="drv-row" data-target="sale-{{ $i }}" title="กดเพื่อดูรายการบิล">
                                    <td data-label="Sale"><strong><i class="fa-solid fa-chevron-right drv-caret"></i> {{ $sa['name'] }}</strong></td>
                                    <td data-label="เปิดบิล"><strong>{{ number_format($sa['jobs']) }}</strong></td>
                                    @php $saFail = $sa['jobs'] - $sa['success']; @endphp
                                    <td data-label="สำเร็จ" class="{{ $sa['success'] ? 'sum-ok' : 'sum-zero' }}">{{ number_format($sa['success']) }}</td>
                                    <td data-label="ไม่สำเร็จ" class="{{ $saFail ? 'sum-cancel' : 'sum-zero' }}">{{ number_format($saFail) }}</td>
                                    <td data-label="ยอดเงินทั้งหมด"><span class="sum-money money-val">{{ '฿' . number_format($sa['money']['total'] ?? 0, 2) }}@if(($sa['money']['missing'] ?? 0) > 0)<span class="partial">(ไม่พบราคา {{ $sa['money']['missing'] }})</span>@endif</span></td>
                                    <td data-label="ยอดเงินส่งสำเร็จ"><span class="sum-money money-val ok">{{ '฿' . number_format($sa['money_ok']['total'] ?? 0, 2) }}@if(($sa['money_ok']['missing'] ?? 0) > 0)<span class="partial">(ไม่พบราคา {{ $sa['money_ok']['missing'] }})</span>@endif</span></td>
                                    <td data-label="% ส่งสำเร็จ"><span class="rate {{ $sa['rate'] >= 90 ? 'good' : ($sa['rate'] >= 70 ? 'mid' : 'low') }}">{{ $sa['rate'] }}%</span></td>
                                </tr>
                                <tr class="drv-detail" id="sale-{{ $i }}" hidden>
                                    <td colspan="7">
                                        <table class="drv-bills">
                                            <thead><tr><th>วันที่เปิดบิล</th><th>รหัส SO</th><th>เลขบิล</th><th>รหัสลูกค้า</th><th>ผลส่ง</th></tr></thead>
                                            <tbody>
                                                @foreach($sa['bills'] as $bl)
                                                    @php $blOk = $bl['status'] === 'จัดส่งสำเร็จ'; @endphp
                                                    <tr>
                                                        <td>{{ $bl['date'] ?: '-' }}</td>
                                                        <td><strong>{{ $bl['so'] ?: '-' }}</strong></td>
                                                        <td>{{ $bl['po'] ?: '-' }}</td>
                                                        <td>{{ $bl['customer'] ?: '-' }}</td>
                                                        <td><span class="badge {{ $blOk ? 'success' : 'danger' }}">{{ $blOk ? 'สำเร็จ' : 'ไม่สำเร็จ' }}</span></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="td-empty" style="text-align:center; color:#94a3b8; padding:24px;">ไม่มีบิลที่เปิดใน{{ $sumLabel ?? 'ช่วงที่เลือก' }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function () {
        const btns = document.querySelectorAll('.view-btn');
        const list = document.getElementById('listView');
        const sum  = document.getElementById('summaryView');
        const po   = document.getElementById('poView');
        const tabInput = document.getElementById('sumTabInput');

        function show(view, tab) {
            const isSum = view === 'summary';
            tab = tab || (tabInput ? tabInput.value : 'driver') || 'driver';
            const isPo  = view === 'po';
            list.hidden = view !== 'list';
            sum.hidden  = !isSum;
            if (po) po.hidden = !isPo;
            const stages = document.querySelector('.stage-summary');
            if (stages) stages.hidden = view !== 'list';

            btns.forEach(b => b.classList.toggle('active', b.dataset.view === view && (!isSum || b.dataset.tab === tab)));
            document.querySelectorAll('[data-tab-box]').forEach(el => el.hidden = el.dataset.tabBox !== tab);
            document.querySelectorAll('[data-tab-only]').forEach(el => el.hidden = el.dataset.tabOnly !== tab);
            if (tabInput) tabInput.value = tab;

            const qs = new URLSearchParams(location.search);
            if (isSum) { qs.set('view', 'summary'); qs.set('sum_tab', tab); }
            else if (isPo) { qs.set('view', 'po'); qs.delete('sum_tab'); }
            else { qs.delete('view'); qs.delete('sum_tab'); }
            history.replaceState(null, '', location.pathname + (qs.toString() ? '?' + qs : '') + (isSum ? '#summary' : (isPo ? '#po' : '')));
        }
        btns.forEach(b => b.addEventListener('click', () => show(b.dataset.view, b.dataset.tab)));
        const q0 = new URLSearchParams(location.search);
        if (location.hash === '#po' || q0.get('view') === 'po') show('po');
        else if (location.hash === '#summary' || q0.get('view') === 'summary') show('summary', q0.get('sum_tab') || (tabInput && tabInput.value));

        document.querySelectorAll('tr.drv-row').forEach(r => r.addEventListener('click', () => {
            const d = document.getElementById(r.dataset.target);
            if (!d) return;
            d.hidden = !d.hidden;
            r.classList.toggle('open', !d.hidden);
        }));

        const sumForm = document.getElementById('sumForm');
        if (sumForm) {
            const dateIn = document.getElementById('sumDate');
            sumForm.querySelectorAll('input[name="sum_period"]').forEach(rd => rd.addEventListener('change', () => {
                const v = dateIn.value || '';
                const y = v.slice(0, 4) || String(new Date().getFullYear());
                const m = v.length >= 7 ? v.slice(5, 7) : String(new Date().getMonth() + 1).padStart(2, '0');
                const d = v.length >= 10 ? v.slice(8, 10) : '01';
                if (rd.value === 'year')  { dateIn.type = 'number'; dateIn.value = y; }
                if (rd.value === 'month') { dateIn.type = 'month';  dateIn.value = y + '-' + m; }
                if (rd.value === 'day')   { dateIn.type = 'date';   dateIn.value = y + '-' + m + '-' + d; }
                sumForm.submit();
            }));
            dateIn.addEventListener('change', () => sumForm.submit());
            sumForm.querySelector('select[name="sum_driver"]').addEventListener('change', () => sumForm.submit());
            sumForm.querySelector('select[name="sum_sale"]')?.addEventListener('change', () => sumForm.submit());
        }
    })();
    </script>

    <script>
    // ให้ข้อมูลในคอลัมน์ขั้นตอน เริ่มตรงกับตัวหนังสือหัวคอลัมน์ (หัวจัดกลาง -> วัดตำแหน่งจริงแล้วตั้ง padding)
    (function () {
        const table = document.getElementById('billTable');
        if (!table) return;
        function align() {
            const ths = table.querySelectorAll('thead th.th-stage');
            const row = table.querySelector('tbody tr');
            for (let i = 0; i < 4; i++) {
                const th = ths[i];
                const td = row && row.children[3 + i];
                if (!th || !td || window.innerWidth <= 900) { table.style.removeProperty('--stage-pad-' + i); continue; }
                const range = document.createRange();
                range.selectNodeContents(th);
                const textLeft = range.getBoundingClientRect().left;
                const tdBox = td.getBoundingClientRect();
                const tdPad = parseFloat(getComputedStyle(td).paddingLeft) || 0;
                const pad = Math.max(0, Math.round(textLeft - tdBox.left - tdPad));
                table.style.setProperty('--stage-pad-' + i, pad + 'px');
            }
        }
        align();
        window.addEventListener('resize', align);
        // เปิดหน้ามาที่แท็บอื่น ตารางถูกซ่อน วัดไม่ได้ -> วัดใหม่ตอนกลับมาแท็บรายการ
        document.querySelectorAll('.view-btn').forEach(b => b.addEventListener('click', () => setTimeout(align)));
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(align);
    })();
    </script>

    <script>
    (function () {
        const form = document.getElementById('poForm');
        const input = document.getElementById('poSearch');
        if (!form || !input) return;
        let timer = null, last = input.value.trim();
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                const q = input.value.trim();
                if (q === last) return;
                last = q;
                form.submit();
            }, 600);
        });
    })();
    </script>

    <script>
    (function () {
        const form  = document.getElementById('filterForm');
        const input = document.getElementById('searchInput');
        if (!form || !input) return;
        let timer = null, last = input.value.trim();
        const filterRows = q => {
            document.querySelectorAll('#tableBody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        };
        input.addEventListener('input', function () {
            filterRows(this.value.toLowerCase());
            clearTimeout(timer);
            timer = setTimeout(() => {
                const q = input.value.trim();
                if (q === last) return;
                last = q;
                form.submit();
            }, 600);
        });
        input.addEventListener('keydown', function (e) { if (e.key === 'Enter') { clearTimeout(timer); } });
        if (input.value) { input.focus(); const len = input.value.length; input.setSelectionRange(len, len); }
    })();
    </script>
</body>
</html>