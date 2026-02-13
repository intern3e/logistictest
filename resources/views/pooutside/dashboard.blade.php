<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>รายการเข้าPO ของนอก - ค้นหา</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Sarabun', sans-serif; background-color: #f5f7fa; color: #333; }
        
        /* ─── TOP NAV ─── */
        .top-nav {
            background: linear-gradient(135deg, #1b2d4f 0%, #243a5e 50%, #3a5a8a 100%);
            color: white;
            padding: 15px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .bg-yellow {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .bg-green {
            background-color: #28a745 !important;
            color: #fff !important;
        }

        .bg-red {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .nav-title {
            font-size: 18px;
            font-weight: 600;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            transition: all .2s ease;
        }
        
        .back-button:hover {
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(231,76,60,0.3);
        }

        /* ─── SEARCH BOX ─── */
        .search-container {
            max-width: 1400px;
            margin: 24px auto;
            padding: 0 24px;
        }

        .search-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            padding: 24px;
        }

        .search-box {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .search-input-group {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .search-label {
            font-size: 13px;
            font-weight: 700;
            color: #1b2d4f;
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

        .search-input {
            width: 100%;
            font-family: inherit;
            font-size: 14px;
            padding: 12px 16px 12px 42px;
            border: 2px solid #e2e6ec;
            border-radius: 6px;
            background: white;
            color: #1e293b;
            outline: none;
            transition: all .22s ease;
            box-shadow: 0 1px 3px rgba(27,45,79,.08);
        }

        .search-input:focus {
            border-color: #4a90d9;
            box-shadow: 0 0 0 4px rgba(74,144,217,.12), 0 3px 12px rgba(27,45,79,.10);
        }

        .search-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all .2s ease;
            white-space: nowrap;
            color: white;
            box-shadow: 0 1px 3px rgba(27,45,79,.08);
        }

        .btn:active { 
            transform: translateY(1px);
            box-shadow: none;
        }

        .btn-success {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        }
        .btn-success:hover {
            background: linear-gradient(135deg, #229954 0%, #27ae60 100%);
            box-shadow: 0 3px 12px rgba(27,45,79,.10);
        }

        .btn-warning { 
            background: linear-gradient(135deg, #f5a623 0%, #e89e1f 100%);
        }
        .btn-warning:hover { 
            background: linear-gradient(135deg, #e89e1f 0%, #f5a623 100%);
            box-shadow: 0 3px 12px rgba(27,45,79,.10);
        }

        /* ─── MAIN CONTAINER ─── */
        .main-container { 
            max-width: 1400px; 
            margin: 0 auto; 
            padding: 0 24px 24px 24px; 
        }

        /* ─── CONTENT CARD ─── */
        .content-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        /* ─── SECTION DIVIDER ─── */
        .section {
            padding: 24px;
            border-bottom: 1px solid #f0f0f0;
        }

        .section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1b2d4f;
            margin-bottom: 20px;
        }

        /* ─── TIMELINE ─── */
        .progress-timeline { 
            padding: 20px 0;
        }
        
        .timeline-track { 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start; 
            position: relative; 
            margin-bottom: 10px; 
        }
        
        .timeline-line { 
            position: absolute; 
            top: 32px; 
            left: 60px; 
            right: 60px; 
            height: 4px; 
            background: #e5e7eb; 
            z-index: 1; 
        }
        
        .timeline-line-fill { 
            position: absolute; 
            top: 0; 
            left: 0; 
            height: 100%; 
            background: linear-gradient(90deg, #10b981 0%, #059669 100%); 
            transition: width 0.6s ease-in-out; 
            border-radius: 4px; 
        }
        
        .timeline-point { 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            position: relative; 
            z-index: 2; 
            flex: 1; 
        }
        
        .timeline-circle { 
            width: 64px; 
            height: 64px; 
            border-radius: 50%; 
            background: white; 
            border: 4px solid #e5e7eb; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin-bottom: 12px; 
            transition: all 0.3s ease; 
        }
        
        .timeline-circle.completed { 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
            border-color: #10b981; 
        }
        
        .timeline-circle.current { 
            border-color: #3b82f6; 
            background: #eff6ff; 
            animation: pulse 2s infinite; 
        }
        
        @keyframes pulse { 
            0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); } 
            50% { box-shadow: 0 0 0 8px rgba(59, 130, 246, 0); } 
        }
        
        .timeline-icon { font-size: 28px; }
        .timeline-circle.completed .timeline-icon { color: white; }
        .timeline-circle.current .timeline-icon { color: #3b82f6; }
        .timeline-label { text-align: center; font-size: 14px; color: #6b7280; max-width: 140px; line-height: 1.4; }
        .timeline-point.completed .timeline-label, .timeline-point.current .timeline-label { color: #1f2937; font-weight: 600; }
        .timeline-date { font-size: 12px; color: #9ca3af; margin-top: 4px; }
        .timeline-point.completed .timeline-date, .timeline-point.current .timeline-date { color: #059669; font-weight: 500; }

        /* ─── NOTICE BOX ─── */
        .notice-box { 
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); 
            border-left: 4px solid #f59e0b; 
            padding: 16px 20px; 
            border-radius: 8px; 
            display: flex; 
            align-items: center; 
            gap: 12px; 
        }
        
        .notice-text { 
            font-size: 14px; 
            color: #78350f; 
            line-height: 1.5; 
        }
        
        .notice-text strong { 
            color: #92400e; 
            font-weight: 600; 
        }

        /* ─── INFO GRID ─── */
        .info-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            gap: 20px; 
        }
        
        .info-item { 
            display: flex; 
            flex-direction: column; 
            gap: 8px; 
        }
        
        .info-label { 
            font-size: 13px; 
            color: #6b7280; 
            font-weight: 500; 
        }
        
        .info-value { 
            font-size: 15px; 
            color: #1f2937; 
            font-weight: 500; 
        }

        /* ─── TABLE ─── */
        .items-table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0; 
        }
        
        .items-table thead th { 
            background: #f9fafb; 
            padding: 14px 16px; 
            text-align: left; 
            font-size: 13px; 
            font-weight: 600; 
            color: #4b5563; 
            border-bottom: 2px solid #e5e7eb; 
        }
        
        .items-table tbody td { 
            padding: 16px; 
            border-bottom: 1px solid #f3f4f6; 
            font-size: 14px; 
            vertical-align: middle; 
        }
        
        .items-table tbody tr:hover { 
            background: #f9fafb; 
        }
        
        .item-details { 
            display: flex; 
            flex-direction: column; 
            gap: 4px; 
        }
        
        .item-name { 
            font-weight: 500; 
            color: #1f2937; 
        }
        
        .item-invoices { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 6px; 
            margin-top: 6px; 
        }
        
        .invoice-tag { 
            display: inline-flex; 
            flex-direction: column; 
            gap: 2px; 
            padding: 6px 12px; 
            background: #eff6ff; 
            border: 1px solid #bfdbfe; 
            border-radius: 6px; 
            font-size: 11px; 
            color: #1e40af; 
        }
        
        .invoice-tag .inv-num { 
            font-weight: 600; 
            font-size: 12px; 
        }
        
        .invoice-tag .inv-date { 
            color: #60a5fa; 
        }
        
        .invoice-tag .inv-qty { 
            color: #059669; 
            font-weight: 600; 
        }

        .qty-box { 
            text-align: center; 
            padding: 8px 12px; 
            background: #f3f4f6; 
            border-radius: 6px; 
            font-weight: 600; 
            font-size: 15px; 
            color: #374151; 
        }
        
        .qty-received { 
            background: #d1fae5; 
            color: #065f46; 
        }

        .status-badge { 
            display: inline-block; 
            padding: 8px 16px; 
            border-radius: 20px; 
            font-size: 13px; 
            font-weight: 600; 
            text-align: center; 
            white-space: nowrap; 
        }
        
        .status-complete { 
            background: #d1fae5; 
            color: #065f46; 
            border: 1px solid #6ee7b7; 
        }
        
        .status-pending { 
            background: #fef3c7; 
            color: #92400e; 
            border: 1px solid #fcd34d; 
        }
        
        .status-no-data { 
            background: #fee2e2; 
            color: #991b1b; 
            border: 1px solid #fca5a5; 
        }

        /* ─── EMPTY STATE ─── */
        .empty-state { 
            text-align: center; 
            padding: 80px 20px; 
            color: #9ca3af; 
        }
        
        .empty-icon { 
            font-size: 64px; 
            margin-bottom: 12px; 
            opacity: 0.5; 
        }
        
        .empty-text { 
            font-size: 18px; 
            font-weight: 500; 
            margin-bottom: 8px; 
        }
        
        .empty-subtext { 
            font-size: 14px; 
            color: #9ca3af; 
        }

        .loading-state { 
            text-align: center; 
            padding: 80px 20px; 
            color: #6b7280; 
        }
        
        .loading-spinner { 
            font-size: 48px; 
            animation: spin 1s linear infinite; 
        }
        
        @keyframes spin { 
            from { transform: rotate(0deg); } 
            to { transform: rotate(360deg); } 
        }

        .error-state { 
            text-align: center; 
            padding: 60px 20px; 
            background: #fee2e2; 
            border-left: 4px solid #dc2626; 
            border-radius: 8px; 
            margin: 24px; 
        }
        
        .error-icon { 
            font-size: 48px; 
            margin-bottom: 12px; 
        }
        
        .error-text { 
            color: #991b1b; 
            font-size: 16px; 
            font-weight: 600; 
        }

        /* ─── CONTENT AREA ─── */
        #contentArea {
            display: none;
        }

        #contentArea.show {
            display: block;
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 768px) {
            .main-container, .search-container { 
                padding: 16px; 
            }
            
            .search-box { 
                flex-direction: column; 
                align-items: stretch; 
            }
            
            .search-buttons { 
                width: 100%; 
            }
            
            .search-buttons .btn { 
                flex: 1; 
            }
            
            .timeline-track { 
                flex-direction: column; 
                gap: 24px; 
            }
            
            .timeline-line { 
                display: none; 
            }
            
            .timeline-point { 
                flex-direction: row; 
                width: 100%; 
                gap: 16px; 
            }
            
            .timeline-circle { 
                margin-bottom: 0; 
                width: 56px; 
                height: 56px; 
            }
            
            .timeline-label { 
                text-align: left; 
                max-width: none; 
            }

            .section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- TOP NAV -->
    <div class="top-nav">
        <div class="nav-title">รายการเข้า PO ของนอก</div>
        <a href="http://server_update:8000/solist" class="back-button">
            ← หน้าหลัก
        </a>
    </div>

    <!-- SEARCH BOX -->
    <div class="search-container">
        <div class="search-card">
            <div class="search-box">
                <div class="search-input-group">
                    <label class="search-label">ค้นหา PO </label>
                    <div class="search-input-wrapper">
                        <input 
                            type="text" 
                            id="searchInput" 
                            class="search-input"
                            placeholder="กรอกเลข PO เพื่อค้นหา..."
                        />
                    </div>
                </div>

                <div class="search-buttons">
                    <button class="btn btn-success" onclick="searchPO()">
                        ✓ ค้นหา
                    </button>
                    <button class="btn btn-warning" onclick="resetSearch()">
                        ↺ Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="main-container">
        <!-- INITIAL EMPTY STATE -->
        <div id="initialEmptyState">
            <div class="content-card">
                <div class="section">
                    <div class="empty-state">
                        <div class="empty-text">กรุณากรอกเลข PO</div>
                        <div class="empty-subtext">ระบบจะค้นหาและแสดงข้อมูลรายละเอียดให้ท่านทราบ</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENT AREA (Hidden initially) -->
        <div id="contentArea">
            <div class="content-card">
                <!-- TIMELINE SECTION -->
                <div class="section">
                    <div class="section-title">สถานะการสั่งซื้อ</div>
                    <div class="progress-timeline">
                        <div class="timeline-track">
                            <div class="timeline-line">
                                <div class="timeline-line-fill" id="progress_fill" style="width: 0%"></div>
                            </div>
                            <div class="timeline-point" data-step="1">
                                <div class="timeline-circle"><span class="timeline-icon">📝</span></div>
                                <div><div class="timeline-label">สร้าง PO</div><div class="timeline-date" id="date_step1">-</div></div>
                            </div>
                            <div class="timeline-point" data-step="2">
                                <div class="timeline-circle"><span class="timeline-icon">✅</span></div>
                                <div><div class="timeline-label">ยืนยันคำสั่งซื้อ</div><div class="timeline-date" id="date_step2">-</div></div>
                            </div>
                            <div class="timeline-point" data-step="3">
                                <div class="timeline-circle"><span class="timeline-icon">📦</span></div>
                                <div><div class="timeline-label">ออก Invoice</div><div class="timeline-date" id="date_step3">-</div></div>
                            </div>
                            <div class="timeline-point" data-step="4">
                                <div class="timeline-circle"><span class="timeline-icon">🚚</span></div>
                                <div><div class="timeline-label">จัดส่งสินค้า</div><div class="timeline-date" id="date_step4">-</div></div>
                            </div>
                            <div class="timeline-point" data-step="5">
                                <div class="timeline-circle"><span class="timeline-icon">⭐</span></div>
                                <div><div class="timeline-label">รับสินค้าครบ</div><div class="timeline-date" id="date_step5">-</div></div>
                            </div>
                        </div>
                    </div>

                    <!-- Notice Box -->
                    <div class="notice-box">
                        <div class="notice-text">วันที่คาดว่าจะได้รับสินค้า: <strong id="expected_date">-</strong></div>
                    </div>
                </div>

                <!-- VENDOR INFO SECTION -->
                <div class="section">
                    <div class="section-title">ข้อมูลผู้ขาย</div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">รหัสร้านค้า</div>
                            <div class="info-value" id="vendor_code">-</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">ชื่อร้านค้า</div>
                            <div class="info-value" id="vendor_name">-</div>
                        </div>
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label">ที่อยู่</div>
                            <div class="info-value" id="vendor_address">-</div>
                        </div>
                    </div>
                </div>

                <!-- ITEMS TABLE SECTION -->
                <div class="section" style="padding: 0;">
                    <div style="padding: 24px 24px 16px 24px;">
                        <div class="section-title" style="margin-bottom: 0;">รายการสินค้า</div>
                    </div>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>สินค้า</th>
                                <th style="width: 120px; text-align: center;">จำนวนสั่ง</th>
                                <th style="width: 120px; text-align: center;">จำนวนรับ</th>
                                <th style="width: 160px; text-align: center;">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody id="items_table_body">
                            <tr><td colspan="4"><div class="empty-state"><div class="empty-icon">⏳</div><div>กำลังโหลดข้อมูล...</div></div></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function formatDateThai(dateInput) {
            if (!dateInput) return '-';
            const thaiMonths = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            const date = new Date(dateInput);
            return `${date.getDate()} ${thaiMonths[date.getMonth()]} ${date.getFullYear()}`;
        }

        function updateTimeline(step, dates = {}) {
            const points = document.querySelectorAll('.timeline-point');
            const progressBar = document.getElementById('progress_fill');
            progressBar.style.width = ((step - 1) / (points.length - 1)) * 100 + '%';
            
            points.forEach((point, index) => {
                const stepNum = index + 1;
                const circle = point.querySelector('.timeline-circle');
                point.classList.remove('completed', 'current');
                circle.classList.remove('completed', 'current');
                
                if (stepNum < step) {
                    point.classList.add('completed');
                    circle.classList.add('completed');
                } else if (stepNum === step) {
                    point.classList.add('current');
                    circle.classList.add('current');
                }
            });

            Object.keys(dates).forEach(key => {
                const el = document.getElementById('date_' + key);
                if (el && dates[key]) el.textContent = dates[key];
            });
        }

        function getItemStatus(matched, completeFlag, totalReceived) {
            if (completeFlag === 'Y') {
                return { text: 'กำลังส่งคลัง', class: 'status-complete' };
            }
            if (matched && totalReceived > 0) {
                return { text: 'กำลังจัดส่ง', class: 'status-pending' };
            }
            return { text: 'ยังไม่มีข้อมูล', class: 'status-no-data' };
        }

        function showLoading() {
            const initialState = document.getElementById('initialEmptyState');
            const contentArea = document.getElementById('contentArea');
            
            contentArea.classList.remove('show');
            initialState.style.display = 'block';
            initialState.innerHTML = `
                <div class="content-card">
                    <div class="section">
                        <div class="loading-state">
                            <div class="loading-spinner">⏳</div>
                            <div style="margin-top: 16px; font-size: 16px; font-weight: 500;">กำลังค้นหาข้อมูล...</div>
                        </div>
                    </div>
                </div>
            `;
        }

        function showError(message) {
            const initialState = document.getElementById('initialEmptyState');
            const contentArea = document.getElementById('contentArea');
            
            contentArea.classList.remove('show');
            initialState.style.display = 'block';
            initialState.innerHTML = `
                <div class="content-card">
                    <div class="section">
                        <div class="error-state">
                            <div class="error-icon">❌</div>
                            <div class="error-text">${message}</div>
                        </div>
                    </div>
                </div>
            `;
        }

        function showEmptyState() {
            const initialState = document.getElementById('initialEmptyState');
            const contentArea = document.getElementById('contentArea');
            
            contentArea.classList.remove('show');
            initialState.style.display = 'block';
            initialState.innerHTML = `
                <div class="content-card">
                    <div class="section">
                        <div class="empty-state">
                            <div class="empty-icon">🔍</div>
                            <div class="empty-text">กรุณากรอกเลข PO หรือชื่อ Vendor</div>
                            <div class="empty-subtext">ระบบจะค้นหาและแสดงข้อมูลรายละเอียดให้ท่านทราบ</div>
                        </div>
                    </div>
                </div>
            `;
        }

        function resetSearch() {
            document.getElementById('searchInput').value = '';
            const initialState = document.getElementById('initialEmptyState');
            const contentArea = document.getElementById('contentArea');
            
            contentArea.style.display = 'none';
            contentArea.classList.remove('show');
            initialState.style.display = 'block';
            
            showEmptyState();
        }

        // กด Enter เพื่อค้นหา
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchPO();
            }
        });
    </script>
    <script>
        async function searchPO() {
        const searchValue = document.getElementById('searchInput').value.trim();
        if (!searchValue) {
            alert('กรุณากรอกเลข PO');
            return;
        }
        showLoading();
        try {

            // =======================
            // 1️⃣ FETCH LOCAL DB
            // =======================
            const localRes = await fetch(`/pooutside/check?ponum=${searchValue}`);
            const localData = await localRes.json();
            console.log("LOCAL:", localData);

            // =======================
            // 2️⃣ FETCH ERP API
            // =======================
            const apiRes = await fetch(
                `http://server_update:8000/api/getPODetail?PONum=${searchValue}`
            );
            const apiData = await apiRes.json();
            console.log("API:", apiData);

            document.getElementById('vendor_code').textContent =
                apiData.VendorCode || '-';

            document.getElementById('vendor_name').textContent =
                apiData.VendorName || '-';

let step = 1; 
let statusColor = '';

const hasLocalData = localData.exists && localData.data.length > 0;

switch (apiData.store_status) {

    case "ENTRY":
        if (hasLocalData) {
            step = 4; // จัดส่งสินค้า
            statusColor = 'bg-yellow';
        } else {
            step = 1; // สร้าง PO
        }
        break;

    case "PARTIAL":
        step = 4; // จัดส่งสินค้า
        statusColor = 'bg-yellow';
        break;

    case "COMPLETED":
        step = 5; // รับสินค้าแล้ว
        statusColor = 'bg-green';
        break;

    case "CANCELLED":
        step = 2; // ยืนยันคำสั่งซื้อ
        statusColor = 'bg-red';
        break;

    default:
        step = 1;
}

// อัปเดต timeline
updateTimeline(step, {
    step1: formatDateThai(apiData.DocuDate),
    step2: formatDateThai(apiData.AppvDate),
    step3: formatDateThai(apiData.BillDate),
    step4: formatDateThai(apiData.ShipDate),
    step5: formatDateThai(apiData.ReqInDate)
});

// เปลี่ยนสี icon พื้นหลังของ step ปัจจุบัน
const currentPoint = document.querySelector(`.timeline-point[data-step="${step}"] .timeline-circle`);
if (currentPoint && statusColor) {
    currentPoint.classList.add(statusColor);
}
            document.getElementById('vendor_address').textContent = [
                apiData.ContAddr1,
                apiData.ContAddr2,
                apiData.ContDistrict,
                apiData.ContAmphur,
                apiData.ContProvince,
                apiData.ContPostCode
            ].filter(Boolean).join(', ') || '-';


        // =========================
        // TABLE
        // =========================
        const tbody = document.getElementById('items_table_body');
        tbody.innerHTML = '';

            // =======================
            // 🔵 แสดง ERP
            // =======================
            if (apiData.ms_podt && apiData.ms_podt.length > 0) {

                tbody.innerHTML += `
                    <tr style="background:#d9edf7;font-weight:bold;">
                        <td colspan="4">ERP ITEMS</td>
                    </tr>
                `;

                apiData.ms_podt.forEach(item => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${item.GoodName}</td>
                            <td style="text-align:center">${item.GoodQty2}</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    `;
                });
            }

            // =======================
            // 🟡 แสดง LOCAL DB
            // =======================
            if (localData.exists && localData.data.length > 0) {

                tbody.innerHTML += `
                    <tr style="background:#fcf8e3;font-weight:bold;">
                        <td colspan="4">LOCAL INVOICE</td>
                    </tr>
                `;

                localData.data.forEach(item => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${item.name}</td>
                            <td style="text-align:center">${item.quantity}</td>
                            <td style="text-align:center">${item.invice}</td>
                            <td style="text-align:center">${item.date_invice}</td>
                        </tr>
                    `;
                });
            }

            if (!apiData.ms_podt?.length && !localData.data?.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" style="text-align:center;color:red;">
                            ไม่พบข้อมูล
                        </td>
                    </tr>
                `;
            }

            // ✅ แสดง content จริง
            document.getElementById('initialEmptyState').style.display = 'none';
            document.getElementById('contentArea').classList.add('show');

        } catch (err) {
            console.error(err);
            alert("เกิดข้อผิดพลาด");
        }
    }
    </script>
</body>
</html>