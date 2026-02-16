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
            left: calc(10% + 0px); 
            right: calc(10% + 0px); 
            height: 4px; 
            background: #e5e7eb; 
            z-index: 1; 
            border-radius: 4px;
        }
        
        .timeline-line-fill { 
            position: absolute; 
            top: 0; 
            left: 0; 
            height: 100%; 
            background: linear-gradient(90deg, #10b981 0%, #059669 100%); 
            transition: width 0.6s ease-in-out; 
            border-radius: 4px;
            display: block;
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
            background: white; 
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        
        .timeline-icon { font-size: 28px; }
        .timeline-circle.completed .timeline-icon { color: white; }
        .timeline-circle.current .timeline-icon { color: #10b981; }
        .timeline-label { text-align: center; font-size: 14px; color: #6b7280; max-width: 160px; line-height: 1.4; }
        .timeline-point.completed .timeline-label, .timeline-point.current .timeline-label { color: #1f2937; font-weight: 600; }
        .timeline-date { font-size: 12px; color: #9ca3af; margin-top: 4px; }
        .timeline-point.completed .timeline-date, .timeline-point.current .timeline-date { color: #059669; font-weight: 500; }

        /* ─── NOTICE BOX ─── */
        .notice-box { 
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); 
            padding: 16px 20px; 
            border-radius: 8px; 
            display: flex; 
            align-items: center; 
            gap: 12px; 
        }

        .notice-box.hidden {
            display: none;
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
            vertical-align: top; 
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
            word-wrap: break-word;
            word-break: break-word;
            max-width: 100%;
        }
        
        .item-invoices { 
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px; 
            margin-top: 6px; 
        }
        
        .invoice-tag { 
            display: inline-flex; 
            flex-direction: column; 
            gap: 2px; 
            padding: 8px 12px; 
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

        .qty-summary {
            margin-top: 12px;
            font-size: 13px;
            font-weight: 600;
            grid-column: 1 / -1;
        }

        .qty-summary.excess {
            color: #dc2626;
        }

        .qty-summary.shortage {
            color: #f59e0b;
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

            .item-invoices {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- TOP NAV -->
    <div class="top-nav">
        <div class="nav-title">ตามของนอก</div>
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
                            maxlength="10"
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
                                <div><div class="timeline-label">ได้รับInvoice</div><div class="timeline-date" id="date_step3">-</div></div>
                            </div>
                            <div class="timeline-point" data-step="4">
                                <div class="timeline-circle"><span class="timeline-icon">🚚</span></div>
                                <div><div class="timeline-label" style="white-space: nowrap;">วันที่คาดว่าจะได้รับสินค้า</div><div class="timeline-date" id="date_step4">-</div></div>
                            </div>
                            <div class="timeline-point" data-step="5">
                                <div class="timeline-circle"><span class="timeline-icon">⭐</span></div>
                                <div><div class="timeline-label">รับสินค้าครบ</div><div class="timeline-date" id="date_step5">-</div></div>
                            </div>
                        </div>
                    </div>

                    <!-- Notice Box - Expected Delivery Date -->
                    <div class="notice-box" id="noticeBox">
                        <div class="notice-text">วันที่คาดว่าจะได้รับสินค้าครบทั้งหมด: <strong id="expected_date">-</strong></div>
                    </div>

                    <!-- Notice Box - Note from Database -->
                    <div class="notice-box hidden" id="noteBox" style="background: #FF7D63; margin-top: 16px;">
                        <div class="notice-text" style="color: #000000">
                            <strong style="color: #000000;">หมายเหตุ:</strong> <span id="note_text">-</span>
                        </div>
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
                        <div class="info-item">
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
                                <th style="width: 500px;">รายการ Invoice</th>
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
            
            // Update progress bar - หยุดที่ step ปัจจุบัน
            progressBar.style.width = ((step - 1) / (points.length - 1)) * 100 + '%';
            
            points.forEach((point, index) => {
                const stepNum = index + 1;
                const circle = point.querySelector('.timeline-circle');
                
                // Reset classes
                point.classList.remove('completed', 'current');
                circle.classList.remove('completed', 'current', 'bg-yellow', 'bg-green', 'bg-red');
                
                // Apply state
                if (stepNum < step) {
                    point.classList.add('completed');
                    circle.classList.add('completed');
                } else if (stepNum === step) {
                    point.classList.add('current');
                    circle.classList.add('current');
                }
            });

            // Update dates
            Object.keys(dates).forEach(key => {
                const el = document.getElementById('date_' + key);
                if (el && dates[key]) el.textContent = dates[key];
            });
        }

        function showLoading() {
            const initialState = document.getElementById('initialEmptyState');
            const contentArea = document.getElementById('contentArea');
            
            contentArea.classList.remove('show');
            contentArea.style.display = 'none';
            initialState.style.display = 'block';
            initialState.innerHTML = `
                <div class="content-card">
                    <div class="section">
                        <div class="loading-state">
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
            contentArea.style.display = 'none';
            initialState.style.display = 'block';
            initialState.innerHTML = `
                <div class="content-card">
                    <div class="section">
                        <div class="error-state">
                            <div class="error-text">${message}</div>
                        </div>
                    </div>
                </div>
            `;
        }

        function resetSearch() {
            document.getElementById('searchInput').value = '';
            const initialState = document.getElementById('initialEmptyState');
            const contentArea = document.getElementById('contentArea');
            
            // Hide content area
            contentArea.style.display = 'none';
            contentArea.classList.remove('show');
            
            // Show initial empty state
            initialState.style.display = 'block';
            initialState.innerHTML = `
                <div class="content-card">
                    <div class="section">
                        <div class="empty-state">
                            <div class="empty-text">กรุณากรอกเลข PO</div>
                            <div class="empty-subtext">ระบบจะค้นหาและแสดงข้อมูลรายละเอียดให้ท่านทราบ</div>
                        </div>
                    </div>
                </div>
            `;
            
            // Focus on search input
            document.getElementById('searchInput').focus();
        }

        // Helper function: รวม Invoice ที่ซ้ำกัน (Invoice, name, quantity เหมือนกัน)
        function mergeInvoices(dbItems) {
            const invoiceMap = new Map();
            
            dbItems.forEach(item => {
                // สร้าง key จาก invoice + name + quantity
                const key = `${item.invoice}_${item.name}_${item.quantity}`;
                
                if (!invoiceMap.has(key)) {
                    // ถ้ายังไม่มีใน Map ให้เพิ่มเข้าไป
                    invoiceMap.set(key, {
                        invoice: item.invoice,
                        name: item.name,
                        quantity: item.quantity,
                        date_invoice: item.date_invoice,
                        note: item.note // เพิ่ม note field
                    });
                }
                // ถ้ามีแล้ว จะไม่เพิ่มซ้ำ (นับเป็น 1 card)
            });
            
            // แปลง Map กลับเป็น Array
            return Array.from(invoiceMap.values());
        }

        // Helper function: จับคู่สินค้า DB กับ API
        // กฎ: DB แต่ละตัวจับคู่ได้แค่ 1 ครั้ง แต่ API จับคู่ได้หลายครั้ง
        // ใช้ชื่อตรงๆ ไม่เคลียร์คำ
        function matchProducts(dbItems, apiItems) {
            const matched = [];
            const usedDbIndices = new Set(); // ติดตาม DB ที่ถูกใช้ไปแล้ว
            
            // สร้าง map เพื่อรวม DB items ที่ match กับ API item เดียวกัน
            const apiToDbMap = new Map();

            // วนลูป DB items แต่ละตัว
            dbItems.forEach((dbItem, dbIdx) => {
                // ถ้า DB item นี้ถูกใช้ไปแล้ว ข้ามไป
                if (usedDbIndices.has(dbIdx)) return;
                
                const dbName = dbItem.name.trim().toUpperCase();
                
                let bestMatch = 0; // เริ่มที่ 0 (API ตัวแรก)
                let bestMatchScore = -1; // ให้เป็นติดลบเพื่อให้มั่นใจว่าต้องเลือกได้เสมอ
                
                // หา API item ที่ match ดีที่สุด
                apiItems.forEach((apiItem, apiIdx) => {
                    const apiName = apiItem.GoodName.trim().toUpperCase();
                    
                    // คำนวณคะแนนความใกล้เคียง
                    let score = 0;
                    
                    // 1. เช็คว่า API มี DB name ทั้งหมดอยู่ในนั้นหรือไม่
                    if (apiName.includes(dbName)) {
                        score = 10000 + dbName.length;
                    } 
                    // 2. เช็คว่า DB มี API name ทั้งหมดอยู่ในนั้นหรือไม่
                    else if (dbName.includes(apiName)) {
                        score = 8000 + apiName.length;
                    }
                    // 3. นับคำที่ตรงกัน
                    else {
                        const dbWords = dbName.split(/\s+/);
                        const apiWords = apiName.split(/\s+/);
                        let matchCount = 0;
                        
                        dbWords.forEach(dbWord => {
                            if (dbWord.length > 2) {
                                apiWords.forEach(apiWord => {
                                    if (dbWord === apiWord) {
                                        matchCount += dbWord.length;
                                    }
                                });
                            }
                        });
                        
                        score = matchCount;
                    }
                    
                    // เก็บ match ที่ดีที่สุด
                    if (score > bestMatchScore) {
                        bestMatchScore = score;
                        bestMatch = apiIdx;
                    }
                });
                
                // เพิ่ม DB item เข้า API map
                if (!apiToDbMap.has(bestMatch)) {
                    apiToDbMap.set(bestMatch, {
                        apiItem: apiItems[bestMatch],
                        dbItems: []
                    });
                }
                apiToDbMap.get(bestMatch).dbItems.push(dbItem);
                
                // ทำเครื่องหมายว่า DB item นี้ถูกใช้แล้ว
                usedDbIndices.add(dbIdx);
            });

            // แปลง Map เป็น Array และรวม Invoice ที่ซ้ำกัน
            apiToDbMap.forEach((value, apiIdx) => {
                matched.push({
                    apiItem: value.apiItem,
                    dbItems: mergeInvoices(value.dbItems),
                    apiIndex: apiIdx
                });
            });

            // เพิ่ม API items ที่ไม่มี DB match
            const matchedApiIndices = new Set(Array.from(apiToDbMap.keys()));
            apiItems.forEach((apiItem, apiIdx) => {
                if (!matchedApiIndices.has(apiIdx)) {
                    matched.push({
                        apiItem: apiItem,
                        dbItems: [],
                        apiIndex: apiIdx
                    });
                }
            });

            // เรียงผลลัพธ์ตาม API index
            matched.sort((a, b) => a.apiIndex - b.apiIndex);

            return matched;
        }

        // Helper: หาวันที่ Invoice ที่ใกล้เคียงกับวันปัจจุบันที่สุด
        function getClosestInvoiceDate(invoices) {
            if (!invoices || invoices.length === 0) return null;
            
            const now = new Date();
            let closest = null;
            let minDiff = Infinity;

            invoices.forEach(inv => {
                if (inv.date_invoice) {
                    const invDate = new Date(inv.date_invoice);
                    const diff = Math.abs(now - invDate);
                    if (diff < minDiff) {
                        minDiff = diff;
                        closest = inv.date_invoice;
                    }
                }
            });

            return closest;
        }

        // Helper: รวม notes ทั้งหมดที่ไม่ซ้ำกัน
        function collectUniqueNotes(dbItems) {
            if (!dbItems || dbItems.length === 0) return null;
            
            const uniqueNotes = new Set();
            dbItems.forEach(item => {
                if (item.note && item.note.trim() !== '') {
                    uniqueNotes.add(item.note.trim());
                }
            });
            
            return uniqueNotes.size > 0 ? Array.from(uniqueNotes).join(' | ') : null;
        }

        // Helper: เพิ่ม 15 วันให้กับวันที่
        function addDays(dateString, days) {
            if (!dateString) return null;
            const date = new Date(dateString);
            date.setDate(date.getDate() + days);
            return formatDateThai(date);
        }

        // Helper: คำนวณสรุปจำนวนสินค้า
        function calculateQuantitySummary(orderedQty, dbItems) {
            if (!dbItems || dbItems.length === 0) {
                return null;
            }

            // รวมจำนวนที่ได้รับทั้งหมด
            const totalReceived = dbItems.reduce((sum, item) => sum + parseFloat(item.quantity || 0), 0);
            const ordered = parseFloat(orderedQty || 0);

            if (totalReceived > ordered) {
                // เกิน
                const excess = totalReceived - ordered;
                return {
                    type: 'excess',
                    message: `เกิน ${excess.toFixed(4)} หน่วย`
                };
            } else if (totalReceived < ordered) {
                // ขาด
                const shortage = ordered - totalReceived;
                return {
                    type: 'shortage',
                    message: `ขาด ${shortage.toFixed(4)} หน่วย`
                };
            } else {
                // เท่าพอดี
                return null;
            }
        }

        // Auto-format PO number with dash
        document.getElementById('searchInput').addEventListener('input', function(e) {
            let value = e.target.value;
            
            // Remove all non-digit characters except dash
            value = value.replace(/[^\d-]/g, '');
            
            // Remove all dashes first
            value = value.replace(/-/g, '');
            
            // Format: xxxx-xxxxx
            if (value.length > 4) {
                value = value.substring(0, 4) + '-' + value.substring(4, 9);
            }
            
            e.target.value = value;
        });

        // Handle paste event to clean up pasted text
        document.getElementById('searchInput').addEventListener('paste', function(e) {
            e.preventDefault();
            
            // Get pasted text
            let pastedText = (e.clipboardData || window.clipboardData).getData('text');
            
            // Remove all non-digit characters except dash
            pastedText = pastedText.replace(/[^\d-]/g, '');
            
            // Remove all dashes
            pastedText = pastedText.replace(/-/g, '');
            
            // Remove spaces
            pastedText = pastedText.replace(/\s/g, '');
            
            // Format: xxxx-xxxxx
            if (pastedText.length > 4) {
                pastedText = pastedText.substring(0, 4) + '-' + pastedText.substring(4, 9);
            }
            
            // Set the formatted value
            e.target.value = pastedText;
            
            // Trigger input event to ensure any other listeners are notified
            e.target.dispatchEvent(new Event('input', { bubbles: true }));
        });

        // กด Enter เพื่อค้นหา
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchPO();
            }
        });

        async function searchPO() {
            const searchValue = document.getElementById('searchInput').value.trim();
            if (!searchValue) {
                alert('กรุณากรอกเลข PO');
                return;
            }
            
            showLoading();
            
            try {
                // Fetch LOCAL DB
                const localRes = await fetch(`/pooutside/check?ponum=${searchValue}`);
                const localData = await localRes.json();
                console.log("LOCAL:", localData);

                // Fetch ERP API
                const apiRes = await fetch(`http://server_update:8000/api/getPODetail?PONum=${searchValue}`);
                const apiData = await apiRes.json();
                console.log("API:", apiData);

                // Update vendor info
                document.getElementById('vendor_code').textContent = apiData.VendorCode || '-';
                document.getElementById('vendor_name').textContent = apiData.VendorName || '-';
                document.getElementById('vendor_address').textContent = [
                    apiData.ContAddr1,
                    apiData.ContAddr2,
                    apiData.ContDistrict,
                    apiData.ContAmphur,
                    apiData.ContProvince,
                    apiData.ContPostCode
                ].filter(Boolean).join(', ') || '-';

                // Determine current step
                const hasLocalData = localData.exists && localData.data.length > 0;
                let step = 1;
                let statusColor = '';

                switch (apiData.store_status) {
                    case "ENTRY":
                        if (hasLocalData) {
                            step = 4; // จัดส่งสินค้า
                            statusColor = 'bg-green';
                        } else {
                            step = 2; // ยืนยันคำสั่งซื้อ
                            statusColor = 'bg-green';
                        }
                        break;
                    case "PARTIAL":
                        step = 5; // รับสินค้าครบ
                        statusColor = 'bg-yellow';
                        break;
                    case "COMPLETED":
                        step = 5; // รับสินค้าครบ
                        statusColor = 'bg-green';
                        break;
                    case "CANCELLED":
                        step = 2; // ยืนยันคำสั่งซื้อ
                        statusColor = 'bg-red';
                        break;
                    default:
                        step = 1; // สร้าง PO
                }

                // ซ่อน Notice Box ถ้า status เป็น COMPLETED
                const noticeBox = document.getElementById('noticeBox');
                if (apiData.store_status === 'COMPLETED') {
                    noticeBox.classList.add('hidden');
                } else {
                    noticeBox.classList.remove('hidden');
                }

                // จัดการ Note Box
                const noteBox = document.getElementById('noteBox');
                const noteText = document.getElementById('note_text');
                const allNotes = collectUniqueNotes(localData.data);
                
                if (allNotes) {
                    noteText.textContent = allNotes;
                    noteBox.classList.remove('hidden');
                } else {
                    noteBox.classList.add('hidden');
                }

                // หา Invoice date ที่ใกล้เคียงกับวันปัจจุบันที่สุด
                const closestInvoiceDate = getClosestInvoiceDate(localData.data);
                const expectedDeliveryDate = addDays(closestInvoiceDate, 15);

                // Update timeline dates
                const timelineDates = {
                    step1: formatDateThai(apiData.DocuDate), // สร้าง PO
                    step2: formatDateThai(apiData.DocuDate), // ยืนยันคำสั่งซื้อ (ใช้ DocuDate)
                    step3: closestInvoiceDate ? formatDateThai(closestInvoiceDate) : '-', // ออก Invoice
                    step4: closestInvoiceDate ? addDays(closestInvoiceDate, 15) : '-', // จัดส่งสินค้า (+15 วัน)
                };

                updateTimeline(step, timelineDates);

                // เพิ่มสีให้ current step
                if (statusColor) {
                    const currentPoint = document.querySelector(`.timeline-point[data-step="${step}"] .timeline-circle`);
                    if (currentPoint) {
                        currentPoint.classList.add(statusColor);
                    }
                }

                // Update expected date
                document.getElementById('expected_date').textContent = expectedDeliveryDate || '-';

                // Match products
                const matchedProducts = matchProducts(localData.data || [], apiData.ms_podt || []);

                // Build table
                const tbody = document.getElementById('items_table_body');
                tbody.innerHTML = '';

                if (matchedProducts.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" style="text-align:center;color:red;">
                                ไม่พบข้อมูล
                            </td>
                        </tr>
                    `;
                } else {
                    matchedProducts.forEach(match => {
                        const apiItem = match.apiItem;
                        const dbItems = match.dbItems;

                        // กำหนดสถานะตาม store_status
                        let itemStatus = 'ไม่มีข้อมูล';
                        let statusClass = 'status-no-data';

                        if (dbItems.length > 0) {
                            if (apiData.store_status === 'COMPLETED') {
                                itemStatus = 'จัดส่งสำเร็จ';
                                statusClass = 'status-complete';
                            } else {
                                itemStatus = 'กำลังจัดส่ง';
                                statusClass = 'status-pending';
                            }
                        }

                        // คำนวณสรุปจำนวน
                        const summary = calculateQuantitySummary(apiItem.GoodQty2, dbItems);

                        // สร้าง Invoice tags (แสดงเฉพาะ unique invoices, 2 cards ต่อแถว)
                        let invoiceTags = '';
                        if (dbItems.length > 0) {
                            dbItems.forEach(dbItem => {
                                invoiceTags += `
                                    <div class="invoice-tag">
                                        <span class="inv-num">Invoice: ${dbItem.invoice || '-'}</span>
                                        <span class="inv-num">Name: ${dbItem.name || '-'}</span>
                                        <span class="inv-date">วันที่: ${formatDateThai(dbItem.date_invoice)}</span>
                                        <span class="inv-qty">จำนวน: ${dbItem.quantity || 0}</span>
                                    </div>
                                `;
                            });

                            // เพิ่มสรุปผล (ถ้ามี)
                            if (summary) {
                                invoiceTags += `
                                    <div class="qty-summary ${summary.type}">
                                        ${summary.message}
                                    </div>
                                `;
                            }
                        } else {
                            invoiceTags = '<span style="color: #9ca3af;">ยังไม่มีข้อมูล Invoice</span>';
                        }

                        tbody.innerHTML += `
                            <tr>
                                <td>
                                    <div class="item-name">${apiItem.GoodName}</div>
                                </td>
                                <td style="text-align:center"> 
                                <div class="qty-box">${apiItem.GoodQty2 || 0}</div> </td>
                                <td>
                                    <div class="item-invoices">
                                        ${invoiceTags}
                                    </div>
                                </td>
                                <td style="text-align:center">
                                    <span class="status-badge ${statusClass}">${itemStatus}</span>
                                </td>
                            </tr>
                        `;
                    });
                }

                // Show content
                document.getElementById('initialEmptyState').style.display = 'none';
                const contentArea = document.getElementById('contentArea');
                contentArea.style.display = 'block';
                contentArea.classList.add('show');

            } catch (err) {
                console.error(err);
                showError("ไม่พบเลข PO");
            }
        }
    </script>
</body>
</html>