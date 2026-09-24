<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ระบบติดตามสถานะคำสั่งซื้อและจัดส่ง (SO Tracking)</title>
    <!-- Google Fonts & FontAwesome Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        /* จัดให้ Header และกล่องสถิติอยู่บรรทัดเดียวกัน */
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

        /* กล่องสถิติ เอาพื้นหลังและกรอบออกแล้ว */
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

        /* หัวเว็บ: ชื่อ + เมนู + ตัวเลข อยู่แถวเดียวกัน, จอแคบลงให้ตัวเลขลงไปอีกแถว */
        .top-header-section { justify-content: flex-start; }
        .top-header-section .stats-container {
            flex-wrap: nowrap;
            gap: 24px;
            margin-left: auto;   /* ตัวเลขชิดขวา, เมนูอยู่ติดชื่อเสมอ */
        }
        @media (max-width: 1350px) {
            .top-header-section .stats-container {
                flex: 1 1 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
                gap: 16px 32px;
            }
        }

        /* ===== สรุปความคืบหน้าแต่ละขั้น ===== */
        .stage-summary {
            margin-bottom: 20px;
        }

        .stage-summary-caption {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .stage-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .stage-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            background: #fff;
        }

        .stage-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }

        .stage-card-head i {
            color: #3498db;
            margin-right: 6px;
        }

        .stage-percent {
            font-size: 13px;
            color: #166534;
            font-weight: 700;
        }

        .stage-bar {
            height: 6px;
            background: #f1f5f9;
            border-radius: 99px;
            margin: 10px 0;
            overflow: hidden;
        }

        .stage-bar span {
            display: block;
            height: 100%;
            background: #22c55e;
            border-radius: 99px;
        }

        .stage-counts {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
        }

        .stage-counts .done {
            color: #166534;
        }

        .stage-counts .pending {
            color: #b45309;
        }

        .stage-counts strong {
            font-size: 16px;
            margin-left: 4px;
        }

        @media (max-width: 1100px) {
            .stage-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 560px) {
            .stage-grid {
                grid-template-columns: 1fr;
            }
        }

        .actions-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ช่องค้นหาให้กว้างคงที่ ไม่ยืดกินพื้นที่ทั้งแถว */
        .search-box {
            position: relative;
            flex: 1 1 300px;
            min-width: 240px;
            max-width: 420px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s;
        }

        .search-box input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        /* ส่วนฟิลเตอร์ชิดขวา */
        .filter-date-form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex: 0 0 auto;
            margin-left: auto; /* ชิดขวา */
        }

        .filter-date-form input[type="date"] {
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        .stage-select {
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            background-color: #fff;
            color: #334155;
            cursor: pointer;
            /* dropdown กว้างพอดีข้อความ ไม่ยืด */
            flex: 0 0 190px;
            width: 190px;
        }

        .filter-date-form input[type="date"] {
            flex: 0 0 160px;
        }

        .btn-filter, .btn-reset {
            flex-shrink: 0;
            white-space: nowrap;
        }

        .stage-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        /* ไฮไลต์ dropdown ที่กำลังกรองอยู่ */
        .stage-select.active {
            border-color: #3498db;
            background-color: #eff6ff;
            color: #1e40af;
            font-weight: 600;
        }

        @media (max-width: 1300px) {
            .filter-date-form {
                flex-wrap: wrap;
            }
        }

        .btn-filter, .btn-reset {
            padding: 9px 15px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-filter {
            background-color: #3498db;
            color: white;
        }

        .btn-filter:hover {
            background-color: #2980b9;
        }

        .btn-reset {
            background-color: #94a3b8;
            color: white;
        }

        .btn-reset:hover {
            background-color: #64748b;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid #e2e8f0;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        tbody td {
            text-align: center;
            vertical-align: middle;
        }

        thead th {
            text-align: center;
        }

        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        /* ===== คอลัมน์ข้อมูล (ลูกค้า/SO/PO) บีบให้แคบ, คอลัมน์สถานะ 4 ขั้นให้เด่น ===== */
        col.col-info  { width: 7%; }
        col.col-stage { width: 16.5%; }
        col.col-dur   { width: 13%; }

        th.th-info, td.td-info {
            padding: 10px 2px;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        td.td-info { color: #475569; }

        th.th-stage {
            font-size: 13px;
            font-weight: 700;
        }

        th.th-stage i {
            color: #3498db;
            margin-right: 4px;
        }

        tbody td:nth-child(n+4) .badge {
            font-size: 13px;
            padding: 6px 14px;
            font-weight: 600;
        }

        tbody td:nth-child(n+4) .status-meta {
            font-size: 12px;
        }

        .money-val.loading { color: #cbd5e1; font-weight: 400; }
        .money-val .partial { font-size: 11px; font-weight: 400; color: #94a3b8; margin-left: 4px; }

        /* ราคาใต้ช่องเปิดบิลส่งของ */
        /* ให้ระยะ/ขนาดเท่ากับบรรทัด "ผู้จัด" ในช่องอื่น (ต่อจากบรรทัดเวลาเลย) */
        .bill-price {
            margin-top: 0;
            font-size: 12px;
            line-height: 1.5;
            font-weight: 700;
            color: #0c4a6e;
        }
        .bill-price i {
            font-weight: 900;
            color: #64748b;
            margin-right: 2px;
        }
        .bill-price.loading {
            color: #cbd5e1;
            font-weight: 400;
        }
        .bill-price.na {
            color: #cbd5e1;
            font-weight: 400;
        }

        /* ===================== เมนู รายการ / สรุป ===================== */
        .view-menu {
            display: inline-flex;
            gap: 4px;
            padding: 4px;
            background: #f1f5f9;
            border-radius: 10px;
            flex-shrink: 0;
        }
        .view-btn {
            border: none;
            background: transparent;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .view-btn:hover { color: #1e40af; }
        .view-btn.active {
            background: #fff;
            color: #1e40af;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12);
        }

        table.sum-table { table-layout: auto; }
        table.sum-table > tbody > tr > td,
        table.sum-table > thead > tr > th { text-align: center; }
        .mini-bar {
            height: 4px;
            background: #f1f5f9;
            border-radius: 99px;
            margin: 4px auto 0;
            max-width: 90px;
            overflow: hidden;
        }
        .mini-bar span { display: block; height: 100%; background: #22c55e; }
        .sum-cancel { color: #b91c1c; font-weight: 600; }
        .sum-warn   { color: #b45309; font-weight: 600; }
        .sum-zero   { color: #cbd5e1; }
        .sum-ok     { color: #166534; font-weight: 600; }
        .sum-ok small { font-weight: 400; color: #64748b; }
        .sum-money  { font-weight: 700; color: #0c4a6e; }
        .sum-money.ok { color: #166534; }

        /* ตารางคนขับ: คอลัมน์ตัวเลขแคบ, ชื่อ + ยอดเงินกว้างและเด่น */
        table.drv-table { table-layout: fixed; }
        table.drv-table col.c-num { width: 8%; }
        table.drv-table > thead > tr > th.c-name,
        table.drv-table > tbody > tr.drv-row > td:first-child { text-align: left; padding-left: 20px; }
        table.drv-table > tbody > tr.drv-row > td:first-child strong { font-size: 15px; }
        table.drv-table > tbody > tr.drv-row > td:nth-child(2) strong { font-size: 16px; }
        table.drv-table .sum-money { font-size: 16px; }
        table.drv-table > thead > tr > th:nth-child(n+7) { color: #0c4a6e; }

        .sum-unit { font-size: 11px; color: #94a3b8; font-weight: 400; }
        .rate {
            display: inline-block;
            min-width: 52px;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
        }
        .rate.good { background: #dcfce7; color: #166534; }
        .rate.mid  { background: #fef3c7; color: #b45309; }
        .rate.low  { background: #fee2e2; color: #b91c1c; }
        /* ตัวกรองหน้าสรุป */
        .sum-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            margin-bottom: 16px;
        }
        .seg {
            display: inline-flex;
            gap: 4px;
            padding: 4px;
            background: #f1f5f9;
            border-radius: 10px;
        }
        .seg-item {
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
        }
        .seg-item input { display: none; }
        .seg-item.active {
            background: #fff;
            color: #1e40af;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12);
        }
        .sum-input {
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            width: 170px;
        }
        .sum-input:focus { border-color: #3498db; }

        .sum-kpis {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }
        .sum-kpi {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
        }
        .k-label { font-size: 12px; color: #64748b; font-weight: 600; }
        .k-val   { font-size: 20px; font-weight: 700; color: #0c4a6e; margin-top: 2px; }
        .k-val.ok { color: #166534; }
        .k-val small { font-size: 13px; font-weight: 400; color: #64748b; }

        /* แถวคนขับ กดเพื่อดูรายการบิล */
        tr.drv-row { cursor: pointer; }
        .drv-caret { font-size: 11px; color: #94a3b8; margin-right: 4px; transition: transform 0.15s; }
        tr.drv-row.open .drv-caret { transform: rotate(90deg); }
        tr.drv-detail > td { background: #f8fafc; padding: 8px 12px 14px; }
        table.drv-bills { table-layout: auto; font-size: 13px; background: #fff; border-radius: 8px; }
        table.drv-bills th, table.drv-bills td { padding: 8px 10px; text-align: center; }
        table.drv-bills .badge { font-size: 12px; padding: 3px 10px; }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge.success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge.pending {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge.processing {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .badge.danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* ผลส่ง 4 แบบ (สีเดียวกับปุ่มในแอปคนขับ) */
        .badge.hold   { background-color: #ffedd5; color: #c2410c; }
        .badge.resend { background-color: #dbeafe; color: #1d4ed8; }
        .sum-resend   { color: #1d4ed8; font-weight: 600; }

        /* คอลัมน์ระยะเวลา */
        .dur-list {
            display: inline-flex;
            flex-direction: column;
            gap: 2px;
            min-width: 130px;
            font-size: 12px;
        }
        .dur-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            color: #64748b;
            line-height: 1.6;
        }
        .dur-row strong { color: #334155; font-weight: 600; }
        .dur-row.slow span, .dur-row.slow strong { color: #b91c1c; font-weight: 700; }
        .dur-row.wait span, .dur-row.wait strong { color: #b45309; font-weight: 600; }
        .dur-row.total { border-top: 1px dashed #e2e8f0; margin-top: 2px; padding-top: 2px; }
        .dur-row.total span, .dur-row.total strong { color: #0c4a6e; font-weight: 700; }
        .dur-na { color: #cbd5e1; }
        .stage-avg {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #e2e8f0;
            font-size: 12px;
            color: #64748b;
        }
        .stage-avg.slow { color: #b91c1c; font-weight: 600; }
        .slow-tag {
            display: inline-block;
            margin-left: 6px;
            padding: 1px 8px;
            border-radius: 10px;
            background: #fee2e2;
            color: #b91c1c;
            font-size: 11px;
            font-weight: 700;
        }
        .stage-card:has(.stage-avg.slow) { border-color: #fecaca; }

        /* เวลา/ชื่อผู้ทำรายการ ใต้ badge */
        .status-meta {
            font-size: 11px;
            color: #64748b;
            margin-top: 4px;
            line-height: 1.5;
        }

        .alert-message {
            padding: 12px;
            background-color: #fee2e2;
            color: #991b1b;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .action-btns {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #64748b;
            transition: color 0.2s;
        }

        .action-btn.edit:hover {
            color: #3498db;
        }

        .action-btn.delete:hover {
            color: #e74c3c;
        }

        /* ===================== Pagination — standard modern web style ===================== */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 14px;
        }

        .pagination-info {
            font-size: 13.5px;
            color: #64748b;
        }

        .pagination-info strong {
            color: #1e293b;
            font-weight: 600;
        }

        /* ซ่อนข้อความ "Showing X to Y of Z results" ที่ Laravel สร้างมาให้อัตโนมัติ */
        .pagination-container nav p,
        .pagination-container nav > div:first-child {
            display: none !important;
        }

        .pagination-container nav {
            display: flex;
            justify-content: flex-end;
        }

        .pagination-container nav ul.pagination,
        .pagination-container nav > div:last-child > div {
            display: flex;
            list-style: none;
            gap: 4px;
            align-items: center;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }

        .pagination-container svg {
            width: 15px;
            height: 15px;
        }

        .pagination-container nav a,
        .pagination-container nav span:not([aria-current="page"]) span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            color: #475569;
            background-color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            line-height: 1;
            transition: border-color 0.15s ease, color 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
        }

        .pagination-container nav span[aria-current="page"] span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 10px;
            background-color: #3498db !important;
            color: #fff !important;
            border: 1px solid #3498db !important;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(52, 152, 219, 0.4);
        }

        .pagination-container nav a:hover {
            border-color: #3498db;
            color: #3498db;
            background-color: #fff;
            box-shadow: 0 1px 4px rgba(52, 152, 219, 0.18);
        }

        .pagination-container nav a:active {
            background-color: #eff6ff;
        }

        .pagination-container nav span[aria-disabled="true"] span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            border-radius: 8px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #cbd5e1;
            cursor: not-allowed;
        }

        .pagination-container nav span:not([aria-current="page"]):not([aria-disabled="true"]) span {
            background: transparent;
            border-color: transparent;
            box-shadow: none;
            color: #94a3b8;
            font-weight: 600;
            cursor: default;
        }

        @media (max-width: 640px) {
            .pagination-container {
                flex-direction: column;
                align-items: flex-start;
            }
            .pagination-container nav {
                justify-content: flex-start;
                width: 100%;
                overflow-x: auto;
            }
        }

        /* ===================== ขั้นปัจจุบัน ===================== */
        tbody td.td-stage {
            vertical-align: middle;   /* ข้อมูลทุกช่องอยู่กึ่งกลางแนวตั้ง */
        }
        /* ขั้นปัจจุบัน: ป้ายสีเดิม แค่มีวงแหวนบาง ๆ บอกตำแหน่ง */
        td.st-current .badge {
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.18);
        }

        /* ===================== Responsive: ทุกขนาดหน้าจอ ===================== */

        /* จอกลาง (แท็บเล็ตแนวนอน / โน้ตบุ๊กเล็ก) */
        @media (max-width: 1300px) {
            .actions-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .search-box {
                max-width: none;
                flex: 0 0 auto;   /* แนวตั้ง: ไม่ให้ flex-basis กลายเป็นความสูง */
            }
            .filter-date-form {
                margin-left: 0;
                flex-wrap: wrap;
            }
            /* ให้คอลัมน์ข้อมูลกว้างพอไม่โดนตัด */
            col.col-info  { width: 9%; }
            col.col-stage { width: 15%; }
            col.col-dur   { width: 13%; }
        }

        @media (max-width: 1100px) {
            header h1 {
                white-space: normal;
                font-size: 20px;
            }
            .stats-container {
                justify-content: flex-start;
            }
            th.th-stage {
                font-size: 12px;
            }
            tbody td:nth-child(n+4) .badge {
                font-size: 12px;
                padding: 5px 10px;
            }
        }

        /* จอเล็ก (แท็บเล็ตแนวตั้ง / มือถือ): ตารางเปลี่ยนเป็นการ์ดทีละรายการ */
        @media (max-width: 900px) {
            body {
                padding: 6px;
            }
            .container {
                padding: 14px 12px;
                border-radius: 10px;
            }
            .top-header-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .stats-container {
                width: 100%;
                gap: 16px;
            }
            .stat-card {
                min-width: 0;
                flex: 1 1 140px;
            }

            .filter-date-form {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
            }
            .stage-select,
            .filter-date-form input[type="date"] {
                width: 100%;
                flex: none;
            }
            .btn-filter, .btn-reset {
                justify-content: center;
            }

            .table-responsive {
                overflow-x: visible;
            }
            table, tbody, tr, td {
                display: block;
                width: 100%;
            }
            table {
                table-layout: auto;
            }
            colgroup, thead {
                display: none;
            }
            tbody tr {
                display: grid;
                grid-template-columns: repeat(6, minmax(0, 1fr));
                gap: 0;
                margin-bottom: 12px;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                overflow: hidden;
                background: #fff;
            }
            tbody tr:hover {
                background: #fff;
            }
            tbody td {
                border-bottom: none;
                padding: 10px 8px;
                text-align: left;
            }
            tbody td.td-info {
                grid-column: span 2;
            }
            th.th-info, td.td-info {
                white-space: normal;
                overflow: visible;
                text-overflow: clip;
                padding: 10px 8px;
                background: #f8fafc;
            }
            tbody td.td-dur {
                grid-column: span 6 !important;
            }
            .dur-list { width: 100%; }
            tbody td.td-stage {
                grid-column: span 3;   /* 2 ขั้นต่อแถว */
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 6px 10px;
                border-top: 1px solid #edf2f7;
            }
            tbody td.td-stage .status-meta {
                margin-top: 0;
            }
            tbody td.td-stage .status-meta br {
                display: none;
            }
            tbody td.td-stage .status-meta i.fa-user {
                margin-left: 8px;
            }
            tbody td[data-label]::before {
                content: attr(data-label);
                display: block;
                font-size: 11px;
                font-weight: 600;
                color: #94a3b8;
                margin-bottom: 2px;
            }
            tbody td.td-stage[data-label]::before {
                flex: 0 0 100%;
                margin-bottom: 0;
            }
            tbody td.td-stage:nth-child(odd) {
                border-left: 1px solid #edf2f7;
            }
            table.sum-table > tbody > tr {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            table.drv-table { table-layout: auto; }
            table.drv-table > tbody > tr.drv-row > td:first-child { padding-left: 8px; }
            table.sum-table > tbody > tr > td {
                grid-column: span 1;
                border-top: 1px solid #edf2f7;
                text-align: left !important;
            }
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
            tbody td.td-empty {
                grid-column: span 6;
                text-align: center;
            }
        }

        /* มือถือเล็ก */
        @media (max-width: 480px) {
            header h1 {
                font-size: 17px;
            }
            .filter-date-form {
                grid-template-columns: 1fr;
            }
            tbody td.td-info:first-child {
                grid-column: span 6;
            }
            tbody td.td-info {
                grid-column: span 3;
            }
            tbody td.td-stage,
            tbody td.td-empty {
                grid-column: span 6;   /* มือถือเล็ก: 1 ขั้นต่อแถว */
            }
            tbody td.td-stage:nth-child(odd) {
                border-left: none;
            }
            .pagination-container nav a,
            .pagination-container nav span span {
                min-width: 34px !important;
                height: 34px !important;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Header และตัวเลขสถิติ -->
        <div class="top-header-section">
            <header style="border: none; margin: 0; padding: 0;">
                <h1><i class="fa-solid fa-truck-fast"></i> ติดตามสถานะคำสั่งซื้อและจัดส่ง (SO Tracking)</h1>
            </header>

            <!-- เมนูสลับมุมมอง: รายการ / สรุป -->
            <div class="view-menu" role="tablist">
                <button type="button" class="view-btn active" data-view="list" role="tab"><i class="fa-solid fa-list"></i> รายการ</button>
                <button type="button" class="view-btn" data-view="summary" role="tab"><i class="fa-solid fa-chart-column"></i> สรุป</button>
            </div>

            <div class="stats-container">
                <div class="stat-card total">
                    <div style="font-size: 12px; color: #64748b; font-weight: 600;">คำสั่งซื้อทั้งหมด</div>
                    <div style="font-size: 20px; font-weight: 700; color: #0c4a6e; margin-top: 2px;">
                        {{ number_format($totalCount ?? 0) }} <span style="font-size: 13px; font-weight: normal; color: #64748b;">รายการ</span>
                    </div>
                </div>

                <div class="stat-card today">
                    <div style="font-size: 12px; color: #64748b; font-weight: 600;">{{ request('date') ? 'คำสั่งซื้อวันที่' : 'คำสั่งซื้อในวันนี้' }} ({{ \Carbon\Carbon::parse($countDate ?? \Carbon\Carbon::today('Asia/Bangkok'))->format('d/m/Y') }})</div>
                    <div style="font-size: 20px; font-weight: 700; color: #14532d; margin-top: 2px;">
                        {{ number_format($todayCount ?? 0) }} <span style="font-size: 13px; font-weight: normal; color: #64748b;">รายการ</span>
                    </div>
                </div>

                <div class="stat-card money-total">
                    <div style="font-size: 12px; color: #64748b; font-weight: 600;">จำนวนเงินทั้งหมด</div>
                    <div style="font-size: 20px; font-weight: 700; color: #0c4a6e; margin-top: 2px;">
                        <span id="moneyAll" class="money-val">...</span>
                    </div>
                </div>

                <div class="stat-card money-day">
                    <div style="font-size: 12px; color: #64748b; font-weight: 600;">{{ request('date') ? 'จำนวนเงินวันที่' : 'จำนวนเงินวันนี้' }} ({{ \Carbon\Carbon::parse($countDate ?? \Carbon\Carbon::today('Asia/Bangkok'))->format('d/m/Y') }})</div>
                    <div style="font-size: 20px; font-weight: 700; color: #14532d; margin-top: 2px;">
                        <span id="moneyDay" class="money-val">...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- สรุปความคืบหน้า: เสร็จแล้ว / ยังไม่เสร็จ ของแต่ละขั้น -->
        <div class="stage-summary">
            <div class="stage-summary-caption">
                @if(request('date') || request('search'))
                    สรุปตามเงื่อนไขที่กรอง
                    @if(request('date')) (วันที่ {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}) @endif
                @else
                    สรุปทั้งระบบ
                @endif
                · {{ number_format($activeCount ?? 0) }} รายการ
                @if(!empty($avgTotal))
                    · เปิดบิลจนส่งสำเร็จ เฉลี่ย <strong>{{ $avgTotal }}</strong>
                @endif
                @if(($cancelledCount ?? 0) > 0)
                    (ไม่รวมยกเลิก {{ number_format($cancelledCount) }} รายการ)
                @endif
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
                            @if($loop->first)
                                <i class="fa-solid fa-play"></i> จุดเริ่มนับเวลา
                            @elseif(!empty($stage['avg']))
                                <i class="fa-solid fa-stopwatch"></i> เฉลี่ย {{ $stage['avg'] }}
                                @if(!empty($stage['slow']))<span class="slow-tag">ช้าสุด</span>@endif
                            @else
                                <i class="fa-solid fa-stopwatch"></i> ยังไม่มีข้อมูลเวลา
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ฟอร์มค้นหาและกรอง (ค้นหาทั้งฐานข้อมูล ไม่ใช่แค่หน้าที่แสดงอยู่) -->
        <div id="listView">
        <form action="{{ route('admin.dashboardadmin') }}" method="GET" class="actions-bar" id="filterForm">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="พิมพ์เพื่อค้นหา รหัสลูกค้า, รหัส SO, PO..." autocomplete="off">
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
            <div class="alert-message">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
            </div>
        @endif

        <div class="table-responsive">
            <table>
                <colgroup>
                    <col class="col-info">
                    <col class="col-info">
                    <col class="col-info">
                    <col class="col-stage">
                    <col class="col-stage">
                    <col class="col-stage">
                    <col class="col-stage">
                    <col class="col-dur">
                </colgroup>
                <thead>
                    <tr>
                        <th class="th-info">รหัสลูกค้า</th>
                        <th class="th-info">รหัส SO</th>
                        <th class="th-info">PO</th>
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
                        // เส้นความคืบหน้า: ขั้นไหนเสร็จแล้ว / ขั้นปัจจุบัน = ขั้นแรกที่ยังไม่เสร็จ
                        $stepDone = [
                            isset($item->statuspdf) && $item->statuspdf == 1,
                            (bool) $item->pick_done,
                            (bool) $item->route_done,
                            ($item->statusdeli ?? '') === 'จัดส่งสำเร็จ',
                        ];
                        $curStep = array_search(false, $stepDone, true); // false = ครบทุกขั้น
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
                        <td class="td-info" data-label="PO">{{ $item->billid ?? '-' }}</td>

                        {{-- เปิดบิลส่งของ --}}
                        <td class="td-stage{{ $stepCls(0) }}" data-label="เปิดบิลส่งของ">
                            @if($isCancelled)
                                <span class="badge danger"><i class="fa-solid fa-ban"></i> ยกเลิก</span>
                            @elseif(isset($item->statuspdf) && $item->statuspdf == 1)
                                <span class="badge success"><i class="fa-solid fa-check"></i> เปิดบิลแล้ว</span>
                            @else
                                <span class="badge pending"><i class="fa-solid fa-clock"></i> รอดำเนินการ</span>
                            @endif

                            @if(isset($item->time))
                                <div class="status-meta">
                                    <i class="fa-regular fa-clock"></i> {{ \Illuminate\Support\Str::substr((string) $item->time, 0, 16) }}
                                </div>
                            @endif

                            {{-- ราคา (NetAmnt) ดึงจาก API getSODetail ด้วยเลข SO (+ PO) หลังโหลดหน้า --}}
                            @if(!empty($item->so_id))
                                <div class="bill-price" data-so="{{ $item->so_id }}" data-po="{{ $item->billid }}"></div>
                            @endif
                        </td>

                        {{-- จัดสินค้า (จาก tblbill.emp_picker / picker_time) --}}
                        <td class="td-stage{{ $stepCls(1) }}" data-label="จัดสินค้า">
                            @if($isCancelled)
                                <span class="badge danger"><i class="fa-solid fa-ban"></i> ยกเลิก</span>
                            @elseif($item->pick_done)
                                <span class="badge success"><i class="fa-solid fa-box-open"></i> จัดสินค้าแล้ว</span>
                                @if(!empty($item->pick_time) || !empty($item->pick_name))
                                    <div class="status-meta">
                                        @if(!empty($item->pick_time))
                                            <i class="fa-regular fa-clock"></i> {{ $item->pick_time }}
                                        @endif
                                        @if(!empty($item->pick_name))
                                            <br><i class="fa-solid fa-user"></i> ผู้จัด: {{ $item->pick_name }}
                                        @endif
                                    </div>
                                @endif
                            @else
                                <span class="badge pending"><i class="fa-solid fa-clock"></i> รอดำเนินการ</span>
                            @endif
                        </td>

                        {{-- จัดเส้นทาง --}}
                        <td class="td-stage{{ $stepCls(2) }}" data-label="จัดเส้นทาง">
                            @if($isCancelled)
                                <span class="badge danger"><i class="fa-solid fa-ban"></i> ยกเลิก</span>
                            @elseif($item->route_done)
                                <span class="badge success"><i class="fa-solid fa-route"></i> จัดเส้นทางแล้ว</span>
                                @if(!empty($item->route_time) || !empty($item->route_name))
                                    <div class="status-meta">
                                        @if(!empty($item->route_time))
                                            <i class="fa-regular fa-clock"></i> {{ $item->route_time }}
                                        @endif
                                        @if(!empty($item->route_name))
                                            <br><i class="fa-solid fa-user"></i> ผู้จัด: {{ $item->route_name }}
                                        @endif
                                    </div>
                                @endif
                            @else
                                <span class="badge pending"><i class="fa-solid fa-clock"></i> รอดำเนินการ</span>
                            @endif
                        </td>

                        {{-- ส่งสินค้า --}}
                        <td class="td-stage{{ $stepCls(3) }}" data-label="ส่งสินค้า">
                            @if($isCancelled)
                                <span class="badge danger"><i class="fa-solid fa-ban"></i> ยกเลิก</span>
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

                                @if(!empty($item->deli_time) || !empty($item->deli_name))
                                    <div class="status-meta">
                                        @if(!empty($item->deli_time))
                                            <i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($item->deli_time)->format('Y-m-d H:i') }}
                                        @endif
                                        @if(!empty($item->deli_name))
                                            <br><i class="fa-solid fa-user"></i> คนขับ: {{ $item->deli_name }}
                                        @endif
                                    </div>
                                @endif
                            @else
                                <span class="badge pending"><i class="fa-solid fa-clock"></i> รอดำเนินการ</span>
                                @if(!empty($item->deli_name))
                                    <div class="status-meta">
                                        <i class="fa-solid fa-user"></i> คนขับ: {{ $item->deli_name }}
                                    </div>
                                @endif
                            @endif
                        </td>

                        {{-- ระยะเวลาแต่ละช่วง (นับจากเปิดบิล) --}}
                        <td class="td-stage td-dur" data-label="ระยะเวลา">
                            @if($isCancelled)
                                <span class="dur-na">-</span>
                            @else
                                @php
                                    $segs = [
                                        'pick'  => 'จัดสินค้า',
                                        'route' => 'จัดเส้นทาง',
                                        'deli'  => 'ส่งสินค้า',
                                    ];
                                    $stageName = ['เปิดบิล', 'จัดสินค้า', 'จัดเส้นทาง', 'ส่งสินค้า'];
                                @endphp
                                <div class="dur-list">
                                    @foreach($segs as $k => $lbl)
                                        @if($item->dur[$k])
                                            <div class="dur-row {{ $item->dur_slow === $k ? 'slow' : '' }}">
                                                <span>{{ $lbl }}</span><strong>+{{ $item->dur[$k] }}</strong>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if($curStep !== false && $item->wait)
                                        <div class="dur-row wait">
                                            <span><i class="fa-solid fa-hourglass-half"></i> รอ{{ $stageName[$curStep] }}</span><strong>{{ $item->wait }}</strong>
                                        </div>
                                    @endif
                                    @if($item->dur['total'])
                                        <div class="dur-row total">
                                            <span><i class="fa-solid fa-flag-checkered"></i> รวม</span><strong>{{ $item->dur['total'] }}</strong>
                                        </div>
                                    @endif
                                    @if(!$item->dur['pick'] && !$item->dur['route'] && !$item->dur['deli'] && !$item->wait && !$item->dur['total'])
                                        <span class="dur-na">-</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="td-empty" style="text-align: center; color: #94a3b8; padding: 20px;">ไม่พบข้อมูลคำสั่งซื้อในระบบ</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-container">
            <div class="pagination-info">
                แสดงผล <strong>{{ $bill->firstItem() ?? 0 }}</strong> ถึง <strong>{{ $bill->lastItem() ?? 0 }}</strong> จากทั้งหมด <strong>{{ $bill->total() }}</strong> รายการ
            </div>
            <div>
                {{ $bill->links() }}
            </div>
        </div>
        </div>{{-- /listView --}}

        <!-- ===================== มุมมองสรุป ===================== -->
        <div id="summaryView" hidden>
            <!-- ตัวกรองหน้าสรุป -->
            <form method="GET" action="{{ route('admin.dashboardadmin') }}#summary" class="sum-filter" id="sumForm">
                <input type="hidden" name="view" value="summary">
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

                <select name="sum_driver" class="stage-select {{ ($sumDriver ?? '') !== '' ? 'active' : '' }}">
                    <option value="">คนขับ: ทั้งหมด</option>
                    @foreach($driverOptions ?? [] as $name)
                        <option value="{{ $name }}" @selected(($sumDriver ?? '') === $name)>{{ $name }}</option>
                    @endforeach
                </select>
            </form>

            <!-- ตัวเลขรวมของช่วงที่เลือก -->
            <div class="sum-kpis">
                <div class="sum-kpi">
                    <div class="k-label">จำนวนงานส่ง ({{ $sumLabel ?? '' }})</div>
                    <div class="k-val">{{ number_format($sumTotals['jobs'] ?? 0) }} <small>งาน</small></div>
                </div>
                <div class="sum-kpi">
                    <div class="k-label">ส่งสำเร็จ</div>
                    <div class="k-val ok">{{ number_format($sumTotals['success'] ?? 0) }} <small>/ {{ number_format($sumTotals['jobs'] ?? 0) }}</small></div>
                </div>
                <div class="sum-kpi">
                    <div class="k-label">ยอดเงินที่ส่งทั้งหมด</div>
                    <div class="k-val"><span class="sum-money money-val" data-pairs='@json($sumTotals['pairs'] ?? [])'>...</span></div>
                </div>
                <div class="sum-kpi">
                    <div class="k-label">ยอดเงินที่ส่งสำเร็จ</div>
                    <div class="k-val ok"><span class="sum-money money-val" data-pairs='@json($sumTotals['pairs_ok'] ?? [])'>...</span></div>
                </div>
            </div>

            <!-- ตารางสรุปตามคนขับ -->
            <div class="table-responsive">
                <table class="sum-table drv-table">
                    <colgroup>
                        <col style="width: 12%">
                        <col class="c-num"><col class="c-num"><col class="c-num"><col class="c-num"><col class="c-num">
                        <col style="width: 16%">
                        <col style="width: 16%">
                        <col style="width: 8%">
                        <col style="width: 8%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="c-name">คนขับ</th>
                            <th>จำนวนงาน</th>
                            <th>ส่งสำเร็จ</th>
                            <th>ค้างบิล</th>
                            <th>ส่งใหม่</th>
                            <th>สินค้าผิด</th>
                            <th>ยอดเงินทั้งหมด</th>
                            <th>ยอดเงินส่งสำเร็จ</th>
                            <th>เฉลี่ย/วัน</th>
                            <th>% ส่งสำเร็จ</th>
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
                                <td data-label="ยอดเงินทั้งหมด"><span class="sum-money money-val" data-pairs='@json($dr['pairs'])'>...</span></td>
                                <td data-label="ยอดเงินส่งสำเร็จ"><span class="sum-money money-val ok" data-pairs='@json($dr['pairs_ok'])'>...</span></td>
                                <td data-label="เฉลี่ย/วัน" title="{{ $dr['jobs'] }} งาน ใน {{ $dr['work_days'] }} วันที่มีงาน">
                                    <strong>{{ rtrim(rtrim(number_format($dr['per_day'], 1), '0'), '.') }}</strong> <small class="sum-unit">งาน</small>
                                </td>
                                <td data-label="% ส่งสำเร็จ">
                                    <span class="rate {{ $dr['rate'] >= 90 ? 'good' : ($dr['rate'] >= 70 ? 'mid' : 'low') }}">{{ $dr['rate'] }}%</span>
                                </td>
                            </tr>
                            <tr class="drv-detail" id="drv-{{ $i }}" hidden>
                                <td colspan="10">
                                    <table class="drv-bills">
                                        <thead>
                                            <tr><th>วันที่ส่ง</th><th>รหัส SO</th><th>PO</th><th>รหัสลูกค้า</th><th>สถานะ</th><th>เวลายืนยัน</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dr['bills'] as $bl)
                                                @php
                                                    $cls = ['จัดส่งสำเร็จ' => 'success', 'ค้างบิล' => 'hold', 'ส่งใหม่' => 'resend', 'สินค้าผิด' => 'danger'][$bl['status']] ?? 'processing';
                                                @endphp
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
    </div>

    <!-- เมนูสลับ รายการ / สรุป (จำไว้ใน URL #summary) -->
    <script>
    (function () {
        const btns = document.querySelectorAll('.view-btn');
        const list = document.getElementById('listView');
        const sum  = document.getElementById('summaryView');
        function show(view) {
            const isSum = view === 'summary';
            list.hidden = isSum;
            sum.hidden  = !isSum;
            // การ์ดความคืบหน้า 4 ขั้น แสดงเฉพาะหน้า "รายการ"
            const stages = document.querySelector('.stage-summary');
            if (stages) stages.hidden = isSum;
            btns.forEach(b => b.classList.toggle('active', b.dataset.view === view));
            const qs = new URLSearchParams(location.search);
            if (!isSum) qs.delete('view');
            history.replaceState(null, '', location.pathname + (qs.toString() ? '?' + qs : '') + (isSum ? '#summary' : ''));
            if (isSum) document.dispatchEvent(new Event('summary:open'));
        }
        btns.forEach(b => b.addEventListener('click', () => show(b.dataset.view)));
        if (location.hash === '#summary' || new URLSearchParams(location.search).get('view') === 'summary') show('summary');

        // กดชื่อคนขับ = เปิด/ปิดรายการบิล
        document.querySelectorAll('tr.drv-row').forEach(r => r.addEventListener('click', () => {
            const d = document.getElementById(r.dataset.target);
            if (!d) return;
            d.hidden = !d.hidden;
            r.classList.toggle('open', !d.hidden);
        }));

        // ตัวกรองหน้าสรุป: เปลี่ยนแล้วค้นหาทันที
        const sumForm = document.getElementById('sumForm');
        if (sumForm) {
            const dateIn = document.getElementById('sumDate');
            sumForm.querySelectorAll('input[name="sum_period"]').forEach(rd => rd.addEventListener('change', () => {
                // เปลี่ยนชนิดช่องวันที่ให้ตรงกับ รายวัน/รายเดือน/รายปี โดยคงวันที่เดิมไว้
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
        }
    })();
    </script>

    <!-- ค้นหาอัตโนมัติ: พิมพ์แล้วหยุดพิมพ์ 0.6 วินาที ค้นหาทั้งฐานข้อมูลทันที (ไม่ต้องกดปุ่ม) -->
    <script>
    (function () {
        const form  = document.getElementById('filterForm');
        const input = document.getElementById('searchInput');
        if (!form || !input) return;

        let timer = null;
        let last  = input.value.trim();

        // ระหว่างพิมพ์: ซ่อนแถวที่ไม่ตรงในหน้านี้ทันที ให้เห็นผลเร็ว
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
                if (q === last) return;   // ไม่ได้เปลี่ยนคำค้นหา ไม่ต้องโหลดใหม่
                last = q;
                form.submit();            // ค้นหาทั้งฐานข้อมูล (ทุกหน้า)
            }, 600);
        });

        // กด Enter = ค้นหาทันที
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { clearTimeout(timer); }
        });

        // หลังโหลดหน้าจากการค้นหา: ให้เคอร์เซอร์กลับไปอยู่ท้ายช่องค้นหา พิมพ์ต่อได้เลย
        if (input.value) {
            input.focus();
            const len = input.value.length;
            input.setSelectionRange(len, len);
        }
    })();
    </script>


    <!-- ราคา (NetAmnt): ดึงจาก API getSODetail ด้วยเลข SO ใช้ทั้งในตาราง และรวมเป็นจำนวนเงินด้านบน -->
    <script>
    (function () {
        const API = 'http://server_update:8000/api/getSODetail?SONum=';
        const MONEY_ALL = @json($moneyAll ?? []);   // [{so, po}] ทั้งหมดตั้งแต่วันเริ่ม (ไม่นับยกเลิก)
        const MONEY_DAY = @json($moneyDay ?? []);   // [{so, po}] ของวันที่เลือก / วันนี้

        const cells = Array.from(document.querySelectorAll('.bill-price[data-so]'));
        const fmt  = n => '฿' + Number(n).toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const norm = s => String(s ?? '').replace(/\s+/g, '').replace(/^(SO|PO)/i, '').toUpperCase();

        // หา object ทุกตัวใน JSON ที่มีช่อง NetAmnt (ไล่จากชั้นบนลงล่าง)
        function findNetAmnt(data) {
            const found = [];
            const queue = [data];
            while (queue.length) {
                const cur = queue.shift();
                if (Array.isArray(cur)) { queue.push(...cur); continue; }
                if (cur && typeof cur === 'object') {
                    if ('NetAmnt' in cur) found.push(cur);
                    Object.values(cur).forEach(v => { if (v && typeof v === 'object') queue.push(v); });
                }
            }
            return found;
        }

        // เลือกรายการที่มีเลข PO ตรงก่อน ไม่งั้นใช้อันแรก (คืนทั้ง object เพื่อกันนับซ้ำ)
        function pickObj(list, po) {
            if (!list.length) return null;
            const p = norm(po);
            if (p) {
                const hit = list.find(o => Object.values(o).some(v => typeof v !== 'object' && norm(v) === p));
                if (hit) return hit;
            }
            return list[0];
        }
        const validAmt = o => o && o.NetAmnt !== null && o.NetAmnt !== '' && !isNaN(o.NetAmnt);

        // เรียก API ครั้งเดียวต่อ 1 SO แล้วเก็บผลไว้ใช้ร่วมกัน
        const cache = {};
        const getList = so => cache[so] ??= fetch(API + encodeURIComponent(so))
            .then(r => r.ok ? r.json() : null)
            .then(d => d ? findNetAmnt(d) : [])
            .catch(() => []);

        // ยิงพร้อมกันครั้งละ 6 SO: SO ที่อยู่ในตารางหน้านี้ก่อน แล้วค่อยที่เหลือ
        const soQueue = [...new Set([
            ...cells.map(c => c.dataset.so),
            ...MONEY_DAY.map(r => r.so),
            ...MONEY_ALL.map(r => r.so),
        ])];
        let qi = 0;
        const runQueue = () => Promise.all(Array.from({ length: Math.min(6, soQueue.length) }, async () => {
            while (qi < soQueue.length) {
                const so = soQueue[qi++];
                const list = await getList(so);
                fillCells(so, list);
            }
        }));

        // ราคาในตาราง
        cells.forEach(c => { c.classList.add('loading'); c.textContent = '...'; });
        function fillCells(so, list) {
            cells.filter(c => c.dataset.so === so).forEach(c => {
                const o = pickObj(list, c.dataset.po);
                c.classList.remove('loading');
                if (validAmt(o)) {
                    c.innerHTML = '<i class="fa-solid fa-coins"></i> ' + fmt(o.NetAmnt);
                } else {
                    c.classList.add('na');
                    c.textContent = 'ไม่พบราคา';
                }
            });
        }

        // รวมจำนวนเงิน (object เดียวกันนับครั้งเดียว กันนับซ้ำเมื่อ SO เดียวมีหลายบิล)
        async function sumPairs(pairs) {
            const used = new Set();
            let total = 0, missing = 0;
            for (const r of pairs) {
                const o = pickObj(await getList(r.so), r.po);
                if (!validAmt(o)) { missing++; continue; }
                if (used.has(o)) continue;
                used.add(o);
                total += Number(o.NetAmnt);
            }
            return { total, missing };
        }
        function showTotal(id, pairs) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.add('loading');
            sumPairs(pairs).then(({ total, missing }) => {
                el.classList.remove('loading');
                el.innerHTML = fmt(total) + (missing ? '<span class="partial">(ไม่พบราคา ' + missing + ' บิล)</span>' : '');
            });
        }

        runQueue();
        showTotal('moneyDay', MONEY_DAY);
        showTotal('moneyAll', MONEY_ALL);

        // ยอดเงินในหน้า "สรุป" (คำนวณตอนเปิดหน้าสรุปครั้งแรก)
        let dayDone = false;
        function fillDayMoney() {
            if (dayDone) return;
            dayDone = true;
            document.querySelectorAll('.sum-money[data-pairs]').forEach(el => {
                let pairs = [];
                try { pairs = JSON.parse(el.dataset.pairs); } catch (e) {}
                el.classList.add('loading');
                sumPairs(pairs).then(({ total, missing }) => {
                    el.classList.remove('loading');
                    el.innerHTML = fmt(total) + (missing ? '<span class="partial">(ไม่พบราคา ' + missing + ')</span>' : '');
                });
            });
        }
        document.addEventListener('summary:open', fillDayMoney);
        if (location.hash === '#summary' || new URLSearchParams(location.search).get('view') === 'summary') fillDayMoney();
    })();
    </script>

</body>
</html>