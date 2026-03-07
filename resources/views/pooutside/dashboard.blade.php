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
        .top-nav { background: linear-gradient(135deg, #1b2d4f 0%, #243a5e 50%, #3a5a8a 100%); color: white; padding: 15px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .bg-yellow { background-color: #ffc107 !important; color: #000 !important; }
        .bg-green  { background-color: #28a745 !important; color: #fff !important; }
        .bg-red    { background-color: #dc3545 !important; color: #fff !important; }
        .nav-title { font-size: 18px; font-weight: 600; }
        .back-button { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; transition: all .2s ease; }
        .back-button:hover { background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(231,76,60,0.3); }
        .search-container { max-width: 1400px; margin: 24px auto; padding: 0 24px; }
        .search-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 24px; }
        .search-box { display: flex; gap: 12px; align-items: flex-end; }
        .search-input-group { flex: 1; display: flex; flex-direction: column; gap: 8px; }
        .search-label { font-size: 13px; font-weight: 700; color: #1b2d4f; text-transform: uppercase; letter-spacing: .8px; }
        .search-input { width: 100%; font-family: inherit; font-size: 14px; padding: 12px 16px; border: 2px solid #e2e6ec; border-radius: 6px; background: white; color: #1e293b; outline: none; transition: all .22s ease; box-shadow: 0 1px 3px rgba(27,45,79,.08); }
        .search-input:focus { border-color: #4a90d9; box-shadow: 0 0 0 4px rgba(74,144,217,.12), 0 3px 12px rgba(27,45,79,.10); }
        .search-buttons { display: flex; gap: 10px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; border-radius: 6px; font-family: inherit; font-size: 14px; font-weight: 600; border: none; cursor: pointer; transition: all .2s ease; white-space: nowrap; color: white; box-shadow: 0 1px 3px rgba(27,45,79,.08); }
        .btn:active { transform: translateY(1px); box-shadow: none; }
        .btn-success { background: linear-gradient(135deg, #27ae60 0%, #229954 100%); }
        .btn-success:hover { background: linear-gradient(135deg, #229954 0%, #27ae60 100%); }
        .btn-warning { background: linear-gradient(135deg, #f5a623 0%, #e89e1f 100%); }
        .btn-warning:hover { background: linear-gradient(135deg, #e89e1f 0%, #f5a623 100%); }
        .main-container { max-width: 1400px; margin: 0 auto; padding: 0 24px 24px 24px; }
        .content-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
        .section { padding: 24px; border-bottom: 1px solid #f0f0f0; }
        .section:last-child { border-bottom: none; }
        .section-title { font-size: 18px; font-weight: 600; color: #1b2d4f; margin-bottom: 20px; }
        .progress-timeline { padding: 20px 0; }
        .timeline-track { display: flex; justify-content: space-between; align-items: flex-start; position: relative; margin-bottom: 10px; }
        .timeline-line { position: absolute; top: 32px; left: calc(10% + 0px); right: calc(10% + 0px); height: 4px; background: #e5e7eb; z-index: 1; border-radius: 4px; }
        .timeline-line-fill { position: absolute; top: 0; left: 0; height: 100%; background: linear-gradient(90deg, #10b981 0%, #059669 100%); transition: width 0.6s ease-in-out; border-radius: 4px; display: block; }
        .timeline-point { display: flex; flex-direction: column; align-items: center; position: relative; z-index: 2; flex: 1; }
        .timeline-circle { width: 64px; height: 64px; border-radius: 50%; background: white; border: 4px solid #e5e7eb; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; transition: all 0.3s ease; }
        .timeline-circle.completed { background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-color: #10b981; }
        .timeline-circle.current { border-color: #3b82f6; background: white; box-shadow: 0 0 0 3px rgba(59,130,246,0.2); }
        .timeline-icon { font-size: 28px; }
        .timeline-circle.completed .timeline-icon { color: white; }
        .timeline-circle.current .timeline-icon { color: #10b981; }
        .timeline-label { text-align: center; font-size: 14px; color: #6b7280; max-width: 160px; line-height: 1.4; }
        .timeline-point.completed .timeline-label, .timeline-point.current .timeline-label { color: #1f2937; font-weight: 600; }
        .timeline-date { font-size: 12px; color: #9ca3af; margin-top: 4px; }
        .timeline-point.completed .timeline-date, .timeline-point.current .timeline-date { color: #059669; font-weight: 500; }
        .notice-box { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 16px 20px; border-radius: 8px; display: flex; align-items: center; gap: 12px; }
        .notice-box.hidden { display: none; }
        .notice-text { font-size: 14px; color: #78350f; line-height: 1.5; }
        .notice-text strong { color: #92400e; font-weight: 600; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .info-item { display: flex; flex-direction: column; gap: 8px; }
        .info-label { font-size: 13px; color: #6b7280; font-weight: 500; }
        .info-value { font-size: 15px; color: #1f2937; font-weight: 500; }
        .items-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .items-table thead th { background: #f9fafb; padding: 14px 16px; text-align: left; font-size: 13px; font-weight: 600; color: #4b5563; border-bottom: 2px solid #e5e7eb; }
        .items-table tbody td { padding: 16px; border-bottom: 1px solid #f3f4f6; font-size: 14px; vertical-align: top; }
        .items-table tbody tr:hover { background: #f9fafb; }
        .item-name { font-weight: 500; color: #1f2937; word-wrap: break-word; word-break: break-word; }
        .item-invoices { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; margin-top: 6px; }
        .invoice-tag { display: inline-flex; flex-direction: column; gap: 2px; padding: 8px 12px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; font-size: 11px; color: #1e40af; }
        .invoice-tag.mismatch { background: #fff1f2; border: 1px solid #fca5a5; color: #991b1b; }
        .invoice-tag.mismatch .inv-num  { color: #b91c1c; }
        .invoice-tag.mismatch .inv-date { color: #f87171; }
        .invoice-tag.mismatch .inv-qty  { color: #dc2626; }
        .mismatch-label { display: flex; align-items: center; gap: 6px; padding: 6px 10px; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 6px; font-size: 12px; font-weight: 600; color: #991b1b; grid-column: 1 / -1; }
        .inv-num  { font-weight: 600; font-size: 12px; }
        .inv-date { color: #60a5fa; }
        .inv-qty  { color: #059669; font-weight: 600; }
        .qty-box  { text-align: center; padding: 8px 12px; background: #f3f4f6; border-radius: 6px; font-weight: 600; font-size: 15px; color: #374151; }
        .qty-summary { margin-top: 12px; font-size: 13px; font-weight: 600; grid-column: 1 / -1; }
        .qty-summary.excess   { color: #dc2626; }
        .qty-summary.shortage { color: #f59e0b; }
        .status-badge  { display: inline-block; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-align: center; white-space: nowrap; }
        .status-complete { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .status-pending  { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .status-no-data  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .empty-state { text-align: center; padding: 80px 20px; color: #9ca3af; }
        .empty-text { font-size: 18px; font-weight: 500; margin-bottom: 8px; }
        .empty-subtext { font-size: 14px; }
        .loading-state { text-align: center; padding: 80px 20px; color: #6b7280; }
        .error-state { text-align: center; padding: 60px 20px; background: #fee2e2; border-left: 4px solid #dc2626; border-radius: 8px; margin: 24px; }
        .error-text { color: #991b1b; font-size: 16px; font-weight: 600; }
        #contentArea { display: none; }
        #contentArea.show { display: block; }
        @media (max-width: 768px) {
            .main-container, .search-container { padding: 16px; }
            .search-box { flex-direction: column; align-items: stretch; }
            .search-buttons { width: 100%; }
            .search-buttons .btn { flex: 1; }
            .timeline-track { flex-direction: column; gap: 24px; }
            .timeline-line { display: none; }
            .timeline-point { flex-direction: row; width: 100%; gap: 16px; }
            .timeline-circle { margin-bottom: 0; width: 56px; height: 56px; }
            .timeline-label { text-align: left; max-width: none; }
            .section { padding: 20px; }
            .item-invoices { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="top-nav">
    <div class="nav-title">ตามของนอก</div>
    <div style="display: flex; gap: 8px;">
        <a href="dashboardreturn"
            style="display: inline-block; background-color: #28a745; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: bold;"
            onmouseover="this.style.backgroundColor='#218838'"
            onmouseout="this.style.backgroundColor='#28a745'">
                เคลมสินค้านอก
        </a>
        <a href="http://server_update:8000/solist" class="back-button">← หน้าหลัก</a>
    </div>
</div>

{{-- SEARCH BOX --}}
<div class="search-container">
    <div class="search-card">
        <div class="search-box">
            <div class="search-input-group">
                <label class="search-label">ค้นหา PO</label>
                <input type="text" id="searchInput" class="search-input"
                    placeholder="กรอกเลข PO เพื่อค้นหา..." maxlength="10" />
            </div>
            <div class="search-buttons">
                <button class="btn btn-success" onclick="searchPO()">✓ ค้นหา</button>
                <button class="btn btn-warning" onclick="resetSearch()">↺ Reset</button>
            </div>
        </div>
    </div>
</div>

{{-- MAIN --}}
<div class="main-container">
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

    <div id="contentArea">
        <div class="content-card">
            {{-- TIMELINE --}}
            <div class="section">
                <div class="section-title">สถานะการสั่งซื้อ</div>
                <div class="progress-timeline">
                    <div class="timeline-track">
                        <div class="timeline-line">
                            <div class="timeline-line-fill" id="progress_fill" style="width: 0%"></div>
                        </div>
                        @php
                            $timelineSteps = [
                                ['icon' => '📝', 'label' => 'สร้าง PO'],
                                ['icon' => '✅', 'label' => 'ยืนยันคำสั่งซื้อ'],
                                ['icon' => '📦', 'label' => 'ได้รับ Invoice'],
                                ['icon' => '🚚', 'label' => 'วันที่คาดว่าจะได้รับสินค้า'],
                                ['icon' => '⭐', 'label' => 'รับสินค้าครบ'],
                            ];
                        @endphp
                        @foreach ($timelineSteps as $i => $step)
                            <div class="timeline-point" data-step="{{ $i + 1 }}">
                                <div class="timeline-circle">
                                    <span class="timeline-icon">{{ $step['icon'] }}</span>
                                </div>
                                <div>
                                    <div class="timeline-label">{{ $step['label'] }}</div>
                                    <div class="timeline-date" id="date_step{{ $i + 1 }}">-</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="notice-box" id="noticeBox">
                    <div class="notice-text">
                        วันที่คาดว่าจะได้รับสินค้าครบทั้งหมด: <strong id="expected_date">-</strong>
                    </div>
                </div>

                <div class="notice-box hidden" id="noteBox" style="background: #FF7D63; margin-top: 16px;">
                    <div class="notice-text" style="color: #000;">
                        <strong style="color: #000;">หมายเหตุ:</strong>
                        <span id="note_text">-</span>
                    </div>
                </div>
            </div>

            {{-- VENDOR --}}
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

            {{-- ITEMS TABLE --}}
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
                    <tbody id="items_table_body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // ─── Utilities ─────────────────────────────────────────────────────────────

    function formatDateThai(dateInput) {
        if (!dateInput) return '-';
        const months = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
        const d = new Date(dateInput);
        return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    // ─── UI State ──────────────────────────────────────────────────────────────

    function showLoading() {
        setContentVisible(false);
        document.getElementById('initialEmptyState').innerHTML = `
            <div class="content-card"><div class="section">
                <div class="loading-state">
                    <div style="margin-top:16px;font-size:16px;font-weight:500;">กำลังค้นหาข้อมูล...</div>
                </div>
            </div></div>`;
        document.getElementById('initialEmptyState').style.display = 'block';
    }

    function showError(msg) {
        setContentVisible(false);
        document.getElementById('initialEmptyState').innerHTML = `
            <div class="content-card"><div class="section">
                <div class="error-state"><div class="error-text">${msg}</div></div>
            </div></div>`;
        document.getElementById('initialEmptyState').style.display = 'block';
    }

    function setContentVisible(visible) {
        const el = document.getElementById('contentArea');
        el.style.display = visible ? 'block' : 'none';
        el.classList.toggle('show', visible);
    }

    function resetSearch() {
        document.getElementById('searchInput').value = '';
        setContentVisible(false);
        document.getElementById('initialEmptyState').style.display = 'block';
        document.getElementById('initialEmptyState').innerHTML = `
            <div class="content-card"><div class="section">
                <div class="empty-state">
                    <div class="empty-text">กรุณากรอกเลข PO</div>
                    <div class="empty-subtext">ระบบจะค้นหาและแสดงข้อมูลรายละเอียดให้ท่านทราบ</div>
                </div>
            </div></div>`;
        document.getElementById('searchInput').focus();
    }

    // ─── Render helpers ────────────────────────────────────────────────────────

    function renderTimeline(timeline) {
        const totalSteps = 5;
        const fill       = ((timeline.step - 1) / (totalSteps - 1)) * 100;
        document.getElementById('progress_fill').style.width = fill + '%';

        document.querySelectorAll('.timeline-point').forEach((point, i) => {
            const stepNum = i + 1;
            const circle  = point.querySelector('.timeline-circle');
            point.classList.remove('completed', 'current');
            circle.classList.remove('completed', 'current', 'bg-yellow', 'bg-green', 'bg-red');

            if (stepNum < timeline.step)      { point.classList.add('completed'); circle.classList.add('completed'); }
            else if (stepNum === timeline.step){ point.classList.add('current');   circle.classList.add('current'); }
        });

        const dates = [
            timeline.date_created,
            timeline.date_created,
            timeline.date_invoice,
            timeline.date_expected,
            null,
        ];
        dates.forEach((d, i) => {
            const el = document.getElementById(`date_step${i + 1}`);
            if (el && d) el.textContent = formatDateThai(d);
        });

        // สีปุ่ม timeline ตาม status
        const colorMap = { ENTRY: 'bg-green', PARTIAL: 'bg-yellow', COMPLETED: 'bg-green', CANCELLED: 'bg-red' };
        const color    = colorMap[timeline.status];
        if (color) {
            const el = document.querySelector(`.timeline-point[data-step="${timeline.step}"] .timeline-circle`);
            if (el) el.classList.add(color);
        }

        // Notice box วันที่คาดรับสินค้า
        document.getElementById('noticeBox').classList.toggle('hidden', !timeline.show_expected_box);
        document.getElementById('expected_date').textContent = formatDateThai(timeline.date_expected) || '-';
    }

    function renderInvoiceTags(item) {
        if (item.is_low_score && item.invoices.length > 0) {
            return `
                <div class="mismatch-label" style="grid-column:1/-1;">
                    ชื่อสินค้าไม่ถูกต้อง ไม่สามารถจับคู่ได้ *ติดต่อผู้ดูแลระบบ
                </div>
                ${item.invoices.map(inv => `
                    <div class="invoice-tag mismatch">
                        <span class="inv-num">Invoice: ${inv.invoice}</span>
                        <span class="inv-num">Name: ${inv.name}</span>
                        <span class="inv-date">วันที่: ${formatDateThai(inv.date_invoice)}</span>
                        <span class="inv-qty">จำนวน: ${inv.quantity}</span>
                    </div>`).join('')}`;
        }

        if (item.invoices.length > 0) {
            const tags = item.invoices.map(inv => `
                <div class="invoice-tag">
                    <span class="inv-num">Invoice: ${inv.invoice}</span>
                    <span class="inv-num">Name: ${inv.name}</span>
                    <span class="inv-date">วันที่: ${formatDateThai(inv.date_invoice)}</span>
                    <span class="inv-qty">จำนวน: ${inv.quantity}</span>
                </div>`).join('');

            const summary = item.qty_summary
                ? `<div class="qty-summary ${item.qty_summary.type}">${item.qty_summary.message}</div>`
                : '';

            return tags + summary;
        }

        return '<span style="color:#9ca3af;">ยังไม่มีข้อมูล Invoice</span>';
    }

    function renderItems(items) {
        const tbody = document.getElementById('items_table_body');
        if (!items.length) {
            tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;color:red;">ไม่พบข้อมูล</td></tr>`;
            return;
        }

        tbody.innerHTML = items.map(item => `
            <tr>
                <td><div class="item-name">${item.name}</div></td>
                <td style="text-align:center">
                    <div class="qty-box">${item.qty_ordered}</div>
                </td>
                <td>
                    <div class="item-invoices">${renderInvoiceTags(item)}</div>
                </td>
                <td style="text-align:center">
                    <span class="status-badge ${item.status_class}">${item.status}</span>
                </td>
            </tr>`).join('');
    }

    // ─── Search ────────────────────────────────────────────────────────────────

    async function searchPO() {
        const ponum = document.getElementById('searchInput').value.trim();
        if (!ponum) { alert('กรุณากรอกเลข PO'); return; }

        showLoading();

        try {
            const res  = await fetch(`/pooutside/search?ponum=${encodeURIComponent(ponum)}`);
            const data = await res.json();

            if (!data.success) { showError(data.message || 'ไม่พบเลข PO'); return; }

            // Vendor
            document.getElementById('vendor_code').textContent    = data.vendor.code;
            document.getElementById('vendor_name').textContent    = data.vendor.name;
            document.getElementById('vendor_address').textContent = data.vendor.address;

            // Timeline
            renderTimeline(data.timeline);

            // Notes
            const noteBox = document.getElementById('noteBox');
            if (data.notes) {
                document.getElementById('note_text').textContent = data.notes;
                noteBox.classList.remove('hidden');
            } else {
                noteBox.classList.add('hidden');
            }

            // Items
            renderItems(data.items);

            document.getElementById('initialEmptyState').style.display = 'none';
            setContentVisible(true);

        } catch (err) {
            console.error(err);
            showError('ไม่พบเลข PO');
        }
    }

    // ─── Input formatting ──────────────────────────────────────────────────────

    document.getElementById('searchInput').addEventListener('input', function (e) {
        let v = e.target.value.replace(/[^\d]/g, '');
        if (v.length > 4) v = v.slice(0, 4) + '-' + v.slice(4, 9);
        e.target.value = v;
    });

    document.getElementById('searchInput').addEventListener('paste', function (e) {
        e.preventDefault();
        let v = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
        if (v.length > 4) v = v.slice(0, 4) + '-' + v.slice(4, 9);
        e.target.value = v;
    });

    document.getElementById('searchInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') searchPO();
    });
</script>
</body>
</html>