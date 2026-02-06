<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการเข้าPO ของนอก</title>
    <style>
        /* ─── RESET & VARIABLES ─── */
        :root {
            --navy:       #1b2d4f;
            --navy-mid:   #243a5e;
            --navy-light: #3a5a8a;
            --accent:     #4a90d9;
            --accent-soft:#d6e8f7;
            --white:      #ffffff;
            --off-white:  #f4f6f9;
            --border:     #e2e6ec;
            --text-main:  #1e293b;
            --text-mid:   #5a6a7e;
            --text-soft:  #94a3b5;
            --row-even:   #f8fafc;
            --row-hover:  #eef4fb;
            --shadow-sm:  0 1px 3px rgba(27,45,79,.08);
            --shadow-md:  0 3px 12px rgba(27,45,79,.10);
            --shadow-lg:  0 6px 24px rgba(27,45,79,.13);
            --radius:     8px;
            --radius-sm:  5px;
            --transition:.22s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'IBM Plex Sans Thai', 'IBM Plex Sans', sans-serif;
            background-color: var(--off-white);
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
            padding: 24px 0 40px;
        }

        a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: color var(--transition);
        }
        a:hover { color: var(--navy); }

        /* ─── LAYOUT WRAPPER ─── */
        .page-wrap {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ─── HEADER ─── */
        .header {
            background: linear-gradient(110deg, var(--navy) 0%, var(--navy-mid) 60%, var(--navy-light) 100%);
            color: var(--white);
            border-radius: var(--radius);
            padding: 18px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: var(--shadow-md);
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        /* subtle top-edge highlight */
        .header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.25), transparent);
        }

        .header h2 {
            font-size: 20px;
            font-weight: 600;
            letter-spacing: .2px;
        }

        /* ─── BUTTONS ─── */
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 7px 18px;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .2s ease, background-color var(--transition);
            white-space: nowrap;
            text-decoration: none;
            color: var(--white);
        }
        .btn:active { transform: scale(.96); }

        .btn-primary        { background-color: var(--navy); box-shadow: var(--shadow-sm); }
        .btn-primary:hover  { background-color: var(--navy-mid); box-shadow: var(--shadow-md); }

        .btn-warning        { background-color: #e8a838; box-shadow: var(--shadow-sm); }
        .btn-warning:hover  { background-color: #d49525; box-shadow: var(--shadow-md); }

        .btn-danger         { background-color: #c94c4c; box-shadow: var(--shadow-sm); }
        .btn-danger:hover   { background-color: #b33d3d; box-shadow: var(--shadow-md); }

        /* ─── CARD SHELL (filter + table) ─── */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        /* ─── FILTER ─── */
        .filter-container {
            padding: 20px 28px;
            border-bottom: 1px solid var(--border);
        }

        .filter-row {
            display: flex;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 18px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-mid);
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .filter-group input {
            font-family: inherit;
            font-size: 13px;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--off-white);
            color: var(--text-main);
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
        }
        .filter-group input:focus {
            border-color: var(--accent);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(74,144,217,.15);
        }
        .filter-group input::placeholder { color: var(--text-soft); }

        .filter-group input[type="text"]  { width: 170px; }
        .filter-group input[type="date"]  { width: 155px; }

        .filter-buttons {
            display: flex;
            gap: 8px;
            padding-bottom: 2px;
        }

        /* ─── INFO BAR ─── */
        .info-bar {
            padding: 10px 28px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: var(--text-soft);
            background: var(--off-white);
        }

        /* ─── TABLE ─── */
        .table-scroll {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            min-width: 960px;
        }

        /* thead */
        thead tr {
            background: var(--navy);
        }

        th {
            color: var(--white);
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .55px;
            padding: 13px 14px;
            border-bottom: 2px solid var(--navy-light);
            white-space: nowrap;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        th.left-align { text-align: left; }

        /* tbody */
        td {
            padding: 11px 14px;
            border-bottom: 1px solid var(--border);
            text-align: center;
            vertical-align: middle;
            color: var(--text-main);
            font-size: 13px;
            line-height: 1.5;
        }
        td.left-align { text-align: left; }

        tr:nth-child(even) td { background-color: var(--row-even); }

        tbody tr {
            transition: background-color var(--transition);
        }
        tbody tr:hover td {
            background-color: var(--row-hover) !important;
        }

        /* wrap long text */
        .wrap-text {
            white-space: normal;
            word-wrap: break-word;
            max-width: 220px;
        }

        /* PO link style */
        td a {
            font-weight: 600;
            color: var(--accent);
            font-size: 13px;
            transition: color var(--transition);
        }
        td a:hover { color: var(--navy); }

        /* ─── NO DATA ─── */
        .no-data {
            text-align: center;
            padding: 52px 20px;
            color: var(--text-soft);
            font-size: 15px;
            font-weight: 400;
        }
        .no-data p { margin: 0; }

        #noResult {
            display: none;
            padding: 52px 20px;
            text-align: center;
            color: var(--text-soft);
            font-size: 15px;
            background: var(--white);
            border-radius: 0 0 var(--radius) var(--radius);
        }

        /* ─── PAGINATION ─── */
        .pagination-container {
            padding: 16px 28px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--white);
        }

        .pagination {
            display: flex;
            list-style: none;
            gap: 5px;
        }

        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--navy);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: background-color var(--transition), color var(--transition), border-color var(--transition), box-shadow var(--transition);
            background: var(--white);
        }

        .pagination a:hover {
            background-color: var(--navy);
            color: var(--white);
            border-color: var(--navy);
            box-shadow: var(--shadow-sm);
        }

        .pagination .active span {
            background-color: var(--navy);
            color: var(--white);
            border-color: var(--navy);
            font-weight: 700;
            box-shadow: var(--shadow-sm);
        }

        .pagination .disabled span {
            color: var(--text-soft);
            cursor: not-allowed;
            background: var(--off-white);
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 768px) {
            .page-wrap      { padding: 0 12px; }
            .header         { padding: 14px 18px; }
            .header h2      { font-size: 17px; }
            .filter-container { padding: 16px 18px; }
            .filter-row     { flex-direction: column; align-items: stretch; }
            .filter-group input { width: 100% !important; }
            .filter-buttons { width: 100%; }
            .info-bar       { flex-direction: column; align-items: flex-start; gap: 4px; padding: 10px 18px; }
            th, td          { padding: 9px 8px; font-size: 11px; }
            .wrap-text      { max-width: 140px; }
        }
    </style>
</head>
<body>

    <div class="page-wrap">

        <!-- HEADER -->
        <div class="header">
            <h2>รายการเข้า PO ของนอก</h2>
            <div class="buttons">
                <a href="http://server_update:8000/solist" class="btn btn-danger">หน้าหลัก</a>
            </div>
        </div>

        <!-- CARD: filter + table -->
        <div class="card">

            <!-- FILTER -->
            <div class="filter-container">
                <div class="filter-row">

                    <div class="filter-group">
                        <label>เลข PO</label>
                        <input type="text" id="filterPO" placeholder="เลข PO" />
                    </div>

                    {{-- <div class="filter-group">
                        <label>วันที่ Invoice</label>
                        <input type="date" id="filterInvoiceDate" />
                    </div>

                    <div class="filter-group">
                        <label>วันที่คาดได้รับสินค้า</label>
                        <input type="date" id="filterExpectedDate" />
                    </div>

                    <div class="filter-group">
                        <label>ชื่อสินค้า</label>
                        <input type="text" id="filterProduct" placeholder="ชื่อสินค้า" />
                    </div> --}}

                    <div class="filter-group">
                        <label>ชื่อ Vendor</label>
                        <input type="text" id="filterVendor" placeholder="ชื่อ Vendor" />
                    </div>

                    <div class="filter-buttons">
                        <button class="btn btn-primary" onclick="applyFilters()">ค้นหา</button>
                        <button class="btn btn-warning" onclick="resetFilters()">Reset</button>
                    </div>

                </div>
            </div>

            <!-- TABLE -->
            <div class="table-scroll">
                @if($poData->count() > 0)
                <table id="poTable">
                    <thead>
                        <tr>
                            <th style="width:120px;">เลข PO</th>
                            <th style="width:105px;">วันที่ Invoice</th>
                            <th style="width:130px;">เลขที่ Invoice</th>
                            <th style="width:135px;">วันที่คาดได้รับสินค้า</th>
                            <th class="left-align" style="width:220px;">ชื่อสินค้า</th>
                            <th style="width:75px;">จำนวน</th>
                            <th class="left-align" style="width:210px;">ชื่อ Vendor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($poData as $index => $po)
                        <tr>
                            <td>
                                @if($po->ponum)
                                    <a href="{{ route('pooutside.detailpooutside', ['ponum' => $po->ponum]) }}">
                                        {{ $po->ponum }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>

                            <td data-date="{{ $po->date_invice ? \Carbon\Carbon::parse($po->date_invice)->format('Y-m-d') : '' }}">
                                @if ($po->date_invice)
                                    {{ \Carbon\Carbon::parse($po->date_invice)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>{{ $po->invice ?? '-' }}</td>

                            <td data-date="{{ $po->date_invice ? \Carbon\Carbon::parse($po->date_invice)->addDays(15)->format('Y-m-d') : '' }}">
                                @if ($po->date_invice)
                                    {{ \Carbon\Carbon::parse($po->date_invice)->addDays(15)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="left-align wrap-text">{{ $po->name ?? '-' }}</td>

                            <td>{{ $po->quantity ?? '-' }}</td>

                            <td class="left-align wrap-text">{{ $po->name_vendor ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="no-data">
                    <p>ไม่พบข้อมูล</p>
                </div>
                @endif
            </div>

            <!-- PAGINATION -->
            @if($poData->hasPages())
            <div class="pagination-container">
                {{ $poData->links() }}
            </div>
            @endif

        </div><!-- .card -->

    </div><!-- .page-wrap -->

    <!-- NO RESULT overlay (แสดงเมื่อ filter แล้วไม่พบ) -->
    <div id="noResult" style="margin-top:-8px; margin-left:24px; margin-right:24px; max-width:1320px; margin-left:auto; margin-right:auto; padding:0 24px;">
        <div style="background:#fff; border-radius:0 0 8px 8px; box-shadow:0 3px 12px rgba(27,45,79,.10); padding:52px 20px; text-align:center; color:#94a3b5; font-size:15px; display:none;" id="noResultInner">
            <p>ไม่พบข้อมูลที่ตรงกับเกณฑ์การค้นหา</p>
        </div>
    </div>

    <!-- JAVASCRIPT FILTER -->
    <script>
        function applyFilters() {
            const valPO           = document.getElementById('filterPO').value.trim().toLowerCase();
            const valInvoiceDate  = document.getElementById('filterInvoiceDate').value;
            const valExpectedDate = document.getElementById('filterExpectedDate').value;
            const valProduct      = document.getElementById('filterProduct').value.trim().toLowerCase();
            const valVendor       = document.getElementById('filterVendor').value.trim().toLowerCase();

            const rows   = document.querySelectorAll('#poTable tbody tr');
            let visCount = 0;

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');

                const cPO           = cells[0].textContent.trim().toLowerCase();
                const cInvoiceDate  = cells[1].getAttribute('data-date') || '';
                const cExpDate      = cells[3].getAttribute('data-date') || '';
                const cProduct      = cells[4].textContent.trim().toLowerCase();
                const cVendor       = cells[6].textContent.trim().toLowerCase();

                let show = true;

                if (valPO            && !cPO.includes(valPO))                        show = false;
                if (show && valInvoiceDate  && cInvoiceDate !== valInvoiceDate)      show = false;
                if (show && valExpectedDate && cExpDate     !== valExpectedDate)     show = false;
                if (show && valProduct      && !cProduct.includes(valProduct))       show = false;
                if (show && valVendor       && !cVendor.includes(valVendor))         show = false;

                row.style.display = show ? '' : 'none';
                if (show) visCount++;
            });

            document.getElementById('noResultInner').style.display = (visCount === 0) ? 'block' : 'none';
        }

        function resetFilters() {
            document.getElementById('filterPO').value            = '';
            document.getElementById('filterInvoiceDate').value   = '';
            document.getElementById('filterExpectedDate').value  = '';
            document.getElementById('filterProduct').value       = '';
            document.getElementById('filterVendor').value        = '';

            document.querySelectorAll('#poTable tbody tr').forEach(row => {
                row.style.display = '';
            });
            document.getElementById('noResultInner').style.display = 'none';
        }
    </script>

</body>
</html>