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
            display: inline-block;
            font-weight: 700;
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 10px;
            background: var(--primary-light);
            color: var(--primary-dark);
            border: 1px solid #bfdbfe;
            letter-spacing: 0.3px;
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

        /* งานค้าง 4 วันขึ้นไป → ทำช่อง "เวลาที่รับเข้า" เป็นสีแดง */
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

        .empty {
            text-align: center;
            color: var(--muted);
            padding: 60px 32px !important;
            font-size: 15px;
            font-style: italic;
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
        <div class="user-badge">ผู้ใช้งาน: {{ $creator }}</div>
    </div>

    <main>
        <div class="toolbar">
            <div class="filter-group">
                <input type="search" id="fShelf" list="shelfList" placeholder="🔍 ค้นหาโดยชั้น..." autocomplete="off">
                <datalist id="shelfList">
                    @foreach($shelfOptions as $sh)
                        <option value="{{ $sh }}"></option>
                    @endforeach
                </datalist>
                <input type="search" id="fSale" list="saleList" placeholder="🔍 ค้นหาโดย Sale (createdBy)..." autocomplete="off">
                <datalist id="saleList">
                    @foreach($saleOptions as $s)
                        <option value="{{ $s }}"></option>
                    @endforeach
                </datalist>
                <button type="button" class="btn-primary" id="btnSearch">ค้นหา</button>
                <button type="button" class="btn-ghost" id="btnClear">ล้าง</button>
            </div>
        </div>

        <div class="table-topbar">
            <div class="table-info">
                รอเช็คเอาท์ <span id="showCount">0</span> รายการ
                &nbsp;•&nbsp; งานค้าง 4 วันขึ้นไปช่องเวลาจะเป็นสีแดง
            </div>
        </div>

        <div class="table-scroll">
            <div class="table-inner">
                <table>
                    <thead>
                        <tr>
                            <th>SO</th>
                            <th>PO</th>
                            <th>ชั้น</th>
                            <th>รหัสลูกค้า</th>
                            <th style="text-align:left;">ลูกค้า</th>
                            <th>Sale</th>
                            <th>เวลาที่รับเข้า</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="7" class="empty">เลือกตัวกรอง (ชั้น หรือ Sale) แล้วกด "ค้นหา"</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
    const DATA_URL   = "{{ route('shelfsale.data') }}";
    const tbody      = document.getElementById('tableBody');
    const showCount  = document.getElementById('showCount');
    const btnSearch  = document.getElementById('btnSearch');
    const btnClear   = document.getElementById('btnClear');
    const fShelf     = document.getElementById('fShelf');
    const fSale      = document.getElementById('fSale');

    function esc(s){ return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

    function setMsg(text){
        tbody.innerHTML = '<tr><td colspan="7" class="empty">' + esc(text) + '</td></tr>';
    }

    function rowHtml(r){
        const overdueRecv = (r.days_in !== null && r.days_in >= 4);
        const recv = r.received_at
            ? esc(r.received_at) + ' <span class="days-badge">+' + r.days_in + ' วัน</span>'
            : '<span class="dash">-</span>';
        const shelf = r.shelf ? '<span class="shelf-badge">' + esc(r.shelf) + '</span>' : '<span class="dash">-</span>';

        return '<tr>'
            + '<td class="num"><span class="ref-link">' + esc(r.so) + '</span></td>'
            + '<td class="num">' + esc(r.po) + '</td>'
            + '<td>' + shelf + '</td>'
            + '<td class="num">' + esc(r.cust_id) + '</td>'
            + '<td class="cust-cell">' + esc(r.cust_name) + '</td>'
            + '<td>' + esc(r.sale) + '</td>'
            + '<td class="recv-time ' + (overdueRecv ? 'overdue' : '') + '">' + recv + '</td>'
            + '</tr>';
    }

    async function search(){
        const params = new URLSearchParams();
        params.set('shelf', fShelf.value.trim());
        params.set('sale',  fSale.value.trim());

        btnSearch.disabled = true;
        setMsg('⏳ กำลังค้นหา...');
        try {
            const res = await fetch(DATA_URL + '?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (!res.ok || !data.ok) { setMsg((data && data.message) || 'ค้นหาไม่สำเร็จ'); showCount.textContent = 0; return; }

            const rows = data.rows || [];
            showCount.textContent = rows.length;
            if (rows.length === 0) { setMsg(data.message || 'ไม่พบรายการตามตัวกรอง'); return; }
            tbody.innerHTML = rows.map(rowHtml).join('');
        } catch (e) {
            console.error(e);
            setMsg('เกิดข้อผิดพลาดในการเชื่อมต่อ');
            showCount.textContent = 0;
        } finally {
            btnSearch.disabled = false;
        }
    }

    btnSearch.addEventListener('click', search);
    btnClear.addEventListener('click', () => {
        fShelf.value = ''; fSale.value = '';
        showCount.textContent = 0;
        setMsg('เลือกตัวกรอง (ชั้น หรือ Sale) แล้วกด "ค้นหา"');
    });
    // กด Enter ในช่องกรอง = ค้นหา
    [fShelf, fSale].forEach(el => el.addEventListener('keydown', e => { if (e.key === 'Enter') search(); }));
</script>
</body>
</html>
