<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบติดตามคำสั่งซื้อภายนอก</title>
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
            --radius:     10px;
            --radius-sm:  6px;
            --transition:.22s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'IBM Plex Sans Thai', 'IBM Plex Sans', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
            color: var(--text-main);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
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
            flex: 1;
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* ─── HEADER ─── */
        .header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 50%, var(--navy-light) 100%);
            color: var(--white);
            border-radius: var(--radius);
            padding: 22px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.4), transparent);
        }

        .header::after {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .header h2 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: .3px;
            position: relative;
            z-index: 1;
        }

        /* ─── BUTTONS ─── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all .2s ease;
            white-space: nowrap;
            text-decoration: none;
            color: var(--white);
            box-shadow: var(--shadow-sm);
        }
        
        .btn:active { 
            transform: translateY(1px);
            box-shadow: none;
        }

        .btn-primary { 
            background: linear-gradient(135deg, var(--accent) 0%, #3a7bc8 100%);
        }
        .btn-primary:hover { 
            background: linear-gradient(135deg, #3a7bc8 0%, var(--accent) 100%);
            box-shadow: var(--shadow-md);
        }

        .btn-warning { 
            background: linear-gradient(135deg, #f5a623 0%, #e89e1f 100%);
        }
        .btn-warning:hover { 
            background: linear-gradient(135deg, #e89e1f 0%, #f5a623 100%);
            box-shadow: var(--shadow-md);
        }

        .btn-danger { 
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }
        .btn-danger:hover { 
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
            box-shadow: var(--shadow-md);
        }

        .btn-success {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        }
        .btn-success:hover {
            background: linear-gradient(135deg, #229954 0%, #27ae60 100%);
            box-shadow: var(--shadow-md);
        }

        /* ─── STATUS BADGES ─── */
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
        }
        
        .status-complete {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        
        .status-shipping {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        /* ─── CARD SHELL ─── */
        .card {
            flex: 1;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* ─── FILTER ─── */
        .filter-container {
            padding: 24px 32px;
            border-bottom: 2px solid var(--border);
            background: linear-gradient(to bottom, var(--white) 0%, var(--off-white) 100%);
        }

        .filter-row {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            max-width: 900px;
        }

        .filter-group {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-size: 13px;
            font-weight: 700;
            color: var(--navy);
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .search-input-wrapper {
            position: relative;
        }

        .search-input-wrapper::before {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            opacity: 0.5;
        }

        .filter-group input {
            font-family: inherit;
            font-size: 14px;
            padding: 12px 16px 12px 42px;
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--white);
            color: var(--text-main);
            outline: none;
            transition: all var(--transition);
            box-shadow: var(--shadow-sm);
        }
        
        .filter-group input:focus {
            border-color: var(--accent);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(74,144,217,.12), var(--shadow-md);
        }
        
        .filter-group input::placeholder { 
            color: var(--text-soft);
            font-weight: 400;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
        }

        /* ─── INFO BAR ─── */
        .info-bar {
            padding: 12px 32px;
            border-bottom: 2px solid var(--border);
            background: linear-gradient(to bottom, var(--off-white) 0%, var(--white) 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-bar-left {
            color: var(--text-mid);
            font-size: 14px;
            font-weight: 600;
        }

        .info-bar-right {
            color: var(--text-soft);
            font-size: 13px;
        }

        .count-badge {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
            color: var(--white);
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 14px;
            margin-left: 8px;
        }

        /* ─── TABLE ─── */
        .table-scroll {
            flex: 1;
            overflow: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        thead tr {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
        }

        th {
            color: var(--white);
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .6px;
            padding: 16px 16px;
            border-bottom: 3px solid var(--navy-light);
            white-space: nowrap;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        th.left-align { text-align: left; }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            text-align: center;
            vertical-align: middle;
            color: var(--text-main);
            font-size: 14px;
            line-height: 1.6;
        }
        td.left-align { text-align: left; }

        tbody tr:nth-child(even) td { 
            background-color: var(--row-even); 
        }

        tbody tr {
            transition: all .15s ease;
        }
        
        tbody tr:hover td {
            background-color: var(--row-hover) !important;
            transform: scale(1.001);
        }

        .wrap-text {
            white-space: normal;
            word-wrap: break-word;
            max-width: 280px;
        }

        /* ─── NO DATA ─── */
        .no-data {
            text-align: center;
            padding: 80px 20px;
            color: var(--text-soft);
            font-size: 16px;
            font-weight: 500;
        }

        .no-data p { margin: 0; }

        #noResultInner {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            padding: 60px 20px;
            text-align: center;
            color: var(--text-soft);
            font-size: 16px;
            margin: 24px;
            display: none;
        }

        /* ─── PAGINATION ─── */
        .pagination-container {
            padding: 24px 32px;
            border-top: 2px solid var(--border);
            background: linear-gradient(to top, var(--off-white) 0%, var(--white) 100%);
        }

        .pagination-info {
            text-align: center;
            color: var(--text-mid);
            font-size: 14px;
            margin-bottom: 16px;
            font-weight: 500;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .pagination button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 46px;
            height: 46px;
            padding: 0 14px;
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--white);
            color: var(--navy);
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .pagination button:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--accent) 0%, #3a7bc8 100%);
            color: var(--white);
            border-color: var(--accent);
            box-shadow: 0 4px 12px rgba(74,144,217,.3);
            transform: translateY(-2px);
        }

        .pagination button.active {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
            color: var(--white);
            border-color: var(--navy);
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(27,45,79,.25);
            cursor: default;
        }

        .pagination button:disabled {
            background: var(--off-white);
            color: var(--text-soft);
            border-color: var(--border);
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination button.prev-btn,
        .pagination button.next-btn {
            min-width: 52px;
            font-weight: 700;
        }

        .pagination .dots {
            color: var(--text-soft);
            padding: 0 8px;
            font-weight: 700;
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 768px) {
            .page-wrap { 
                padding: 16px; 
            }
            
            .header { 
                padding: 18px 20px; 
            }
            
            .header h2 { 
                font-size: 18px; 
            }
            
            .filter-container { 
                padding: 20px; 
            }
            
            .filter-row { 
                flex-direction: column; 
                align-items: stretch; 
            }
            
            .filter-group input { 
                width: 100% !important; 
            }
            
            .filter-buttons { 
                width: 100%; 
            }
            
            .filter-buttons .btn {
                flex: 1;
            }

            .info-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                padding: 12px 20px;
            }
            
            th, td { 
                padding: 10px 8px; 
                font-size: 12px; 
            }
            
            .wrap-text { 
                max-width: 180px; 
            }

            .pagination-container {
                padding: 20px 16px;
            }

            .pagination button {
                min-width: 40px;
                height: 40px;
                font-size: 13px;
                padding: 0 10px;
            }

            .pagination button.prev-btn,
            .pagination button.next-btn {
                min-width: 44px;
            }
        }
    </style>
</head>
<body>

    <div class="page-wrap">
        <!-- HEADER -->
        <div class="header">
            <h2>ระบบติดตามคำสั่งซื้อภายนอก</h2>
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
                        <label>ค้นหา PO หรือ ชื่อร้านค้า</label>
                        <div class="search-input-wrapper">
                            <input 
                                type="text" 
                                id="searchInput" 
                                placeholder="ค้นหาเลข PO หรือ ชื่อร้านค้า" 
                                oninput="liveFilter()"
                            />
                        </div>
                    </div>

                    <div class="filter-buttons">
                        <button class="btn btn-warning" onclick="resetFilters()">
                            ↺ Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- INFO BAR -->
            <div class="info-bar">
                <div class="info-bar-left">
                    ทั้งหมด <span class="count-badge" id="totalCount">{{ $poData->count() }}</span> รายการ
                </div>
                <div class="info-bar-right" id="filterInfo">
                    {{-- แสดงทั้งหมด --}}
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-scroll">
                @if($poData->count() > 0)
                <table id="poTable">
                    <thead>
                        <tr>
                            <th style="width:120px;">เลข PO</th>
                            <th style="width:130px;">เลขที่ Invoice</th>
                            <th class="left-align" style="width:210px;">ชื่อ Vendor</th>
                            <th style="width:105px;">วันที่ Invoice</th>
                            <th style="width:135px;">วันที่คาดได้รับสินค้า</th>
                            <th class="left-align" style="width:220px;">ชื่อสินค้า</th>
                            <th style="width:75px;">จำนวน</th>
                            <th style="width:150px;">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($poData as $index => $po)
                        <tr data-po="{{ $po->ponum ?? '' }}" 
                            data-vendor="{{ $po->name_vendor ?? '' }}"
                            data-status="{{ $po->status ?? 'N' }}">
                            <td>{{ $po->ponum ?? '-' }}</td>
                            <td>{{ $po->invice ?? '-' }}</td>
                            <td class="left-align wrap-text">{{ $po->name_vendor ?? '-' }}</td>

                            <td data-date="{{ $po->date_invice ? \Carbon\Carbon::parse($po->date_invice)->format('Y-m-d') : '' }}">
                                @if ($po->date_invice)
                                    {{ \Carbon\Carbon::parse($po->date_invice)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td data-date="{{ $po->date_invice ? \Carbon\Carbon::parse($po->date_invice)->addDays(15)->format('Y-m-d') : '' }}">
                                @if ($po->date_invice)
                                    {{ \Carbon\Carbon::parse($po->date_invice)->addDays(15)->locale('th')->isoFormat('DD MMMM YYYY') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="left-align wrap-text">{{ $po->name ?? '-' }}</td>

                            <td>{{ $po->quantity ?? '-' }}</td>

                            <td>
                                @if(($po->status ?? 'N') === 'Y')
                                    <span class="status-badge status-complete">✓ รับของสำเร็จ</span>
                                @else
                                    <span class="status-badge status-shipping">📦 กำลังจัดส่ง</span>
                                @endif
                            </td>
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
            <div class="pagination-container">
                <div class="pagination-info" id="paginationInfo">
                    {{-- แสดง 1 ถึง 100 จากทั้งหมด {{ $poData->count() }} รายการ --}}
                </div>
                <div class="pagination" id="pagination">
                    <!-- Pagination buttons will be generated by JavaScript -->
                </div>
            </div>

        </div><!-- .card -->

    </div><!-- .page-wrap -->

    <!-- NO RESULT -->
    <div id="noResultInner">
        <p>ไม่พบข้อมูลที่ตรงกับการค้นหา</p>
    </div>
    <script>
        const ROWS_PER_PAGE = 100;
        let currentPage = 1;
        let filteredRows = [];
        let allRows = [];

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            allRows = Array.from(document.querySelectorAll('#poTable tbody tr'));
            filteredRows = [...allRows];
            renderPagination();
            showPage(1);
        });

        // แสดงหน้าที่ระบุ
        function showPage(page) {
            currentPage = page;
            const start = (page - 1) * ROWS_PER_PAGE;
            const end = start + ROWS_PER_PAGE;

            // ซ่อนทุก row ก่อน
            allRows.forEach(row => row.style.display = 'none');

            // แสดงเฉพาะ row ที่อยู่ในหน้าปัจจุบัน
            filteredRows.slice(start, end).forEach(row => {
                row.style.display = '';
            });

            // อัพเดท pagination info
            updatePaginationInfo();
            
            // อัพเดท pagination buttons
            renderPagination();

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // สร้าง pagination buttons
        function renderPagination() {
            const totalPages = Math.ceil(filteredRows.length / ROWS_PER_PAGE);
            const paginationEl = document.getElementById('pagination');
            paginationEl.innerHTML = '';

            if (totalPages <= 1) {
                return; // ไม่แสดง pagination ถ้ามีแค่ 1 หน้า
            }

            // Previous button
            const prevBtn = document.createElement('button');
            prevBtn.className = 'prev-btn';
            prevBtn.textContent = '‹';
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => showPage(currentPage - 1);
            paginationEl.appendChild(prevBtn);

            // Page buttons
            const maxButtons = 7; // จำนวนปุ่มสูงสุดที่แสดง
            let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);

            if (endPage - startPage < maxButtons - 1) {
                startPage = Math.max(1, endPage - maxButtons + 1);
            }

            // First page
            if (startPage > 1) {
                const btn = document.createElement('button');
                btn.textContent = '1';
                btn.onclick = () => showPage(1);
                paginationEl.appendChild(btn);

                if (startPage > 2) {
                    const dots = document.createElement('span');
                    dots.className = 'dots';
                    dots.textContent = '...';
                    paginationEl.appendChild(dots);
                }
            }

            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                const btn = document.createElement('button');
                btn.textContent = i;
                if (i === currentPage) {
                    btn.className = 'active';
                }
                btn.onclick = () => showPage(i);
                paginationEl.appendChild(btn);
            }

            // Last page
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const dots = document.createElement('span');
                    dots.className = 'dots';
                    dots.textContent = '...';
                    paginationEl.appendChild(dots);
                }

                const btn = document.createElement('button');
                btn.textContent = totalPages;
                btn.onclick = () => showPage(totalPages);
                paginationEl.appendChild(btn);
            }

            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.className = 'next-btn';
            nextBtn.textContent = '›';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => showPage(currentPage + 1);
            paginationEl.appendChild(nextBtn);
        }

        // อัพเดท pagination info
        function updatePaginationInfo() {
            const start = (currentPage - 1) * ROWS_PER_PAGE + 1;
            const end = Math.min(currentPage * ROWS_PER_PAGE, filteredRows.length);
            const total = filteredRows.length;

            const infoEl = document.getElementById('paginationInfo');
            infoEl.textContent = `แสดง ${start} ถึง ${end} จากทั้งหมด ${total} รายการ`;
        }

        // Live filter
        function liveFilter() {
            const searchValue = document.getElementById('searchInput').value.trim().toLowerCase();
            
            if (!searchValue) {
                // ถ้าไม่มีการค้นหา แสดงทั้งหมด
                filteredRows = [...allRows];
            } else {
                // กรองตามคำค้นหา
                filteredRows = allRows.filter(row => {
                    const po = row.getAttribute('data-po').toLowerCase();
                    const vendor = row.getAttribute('data-vendor').toLowerCase();
                    return po.includes(searchValue) || vendor.includes(searchValue);
                });
            }

            // แสดงข้อความถ้าไม่พบข้อมูล
            const noResultDiv = document.getElementById('noResultInner');
            if (searchValue && filteredRows.length === 0) {
                noResultDiv.style.display = 'block';
                document.querySelector('.pagination-container').style.display = 'none';
            } else {
                noResultDiv.style.display = 'none';
                document.querySelector('.pagination-container').style.display = 'block';
            }

            // อัพเดทข้อมูลการกรอง
            updateFilterInfoBar(searchValue);

            // Reset ไปหน้า 1 และแสดงผล
            currentPage = 1;
            showPage(1);
        }

        // อัพเดทข้อมูลการกรอง
        function updateFilterInfoBar(searchValue) {
            const filterInfo = document.getElementById('filterInfo');
            const totalRecords = allRows.length;
            
            if (searchValue) {
                // filterInfo.textContent = `กำลังแสดง ${filteredRows.length} จาก ${totalRecords} รายการ`;
            } else {
                // filterInfo.textContent = 'แสดงทั้งหมด';
            }
        }

        // Reset ฟิลเตอร์
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            filteredRows = [...allRows];
            document.getElementById('noResultInner').style.display = 'none';
            document.querySelector('.pagination-container').style.display = 'block';
            document.getElementById('filterInfo').textContent = 'แสดงทั้งหมด';
            currentPage = 1;
            showPage(1);
        }
    </script>

</body>
</html>