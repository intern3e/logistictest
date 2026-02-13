<!-- <!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ติดตามสถานะ PO - {{ $ponum ?? '' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Sarabun', sans-serif; background-color: #f5f7fa; color: #333; }
        
        .top-nav {
            background: white; padding: 15px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 100;
        }
        .back-button {
            display: flex; align-items: center; gap: 8px; color: #555;
            text-decoration: none; font-size: 16px; cursor: pointer;
            background: none; border: none; padding: 8px 12px; border-radius: 6px;
        }
        .back-button:hover { background: #f5f5f5; color: #007bff; }
        .po-header { font-size: 15px; color: #666; }
        .po-header strong { color: #333; font-weight: 600; }
        .refresh-btn { color: #007bff; text-decoration: none; font-size: 14px; font-weight: 500; padding: 8px 16px; border-radius: 6px; }
        .refresh-btn:hover { background: #e7f3ff; }

        .main-container { max-width: 1400px; margin: 0 auto; padding: 24px; }
        .card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; overflow: hidden; }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #f0f0f0; font-size: 18px; font-weight: 600; color: #333; }
        .card-body { padding: 24px; }

        .progress-timeline { padding: 40px 20px; }
        .timeline-track { display: flex; justify-content: space-between; align-items: flex-start; position: relative; margin-bottom: 10px; }
        .timeline-line { position: absolute; top: 32px; left: 60px; right: 60px; height: 4px; background: #e5e7eb; z-index: 1; }
        .timeline-line-fill { position: absolute; top: 0; left: 0; height: 100%; background: linear-gradient(90deg, #10b981 0%, #059669 100%); transition: width 0.6s ease-in-out; border-radius: 4px; }
        .timeline-point { display: flex; flex-direction: column; align-items: center; position: relative; z-index: 2; flex: 1; }
        .timeline-circle { width: 64px; height: 64px; border-radius: 50%; background: white; border: 4px solid #e5e7eb; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; transition: all 0.3s ease; }
        .timeline-circle.completed { background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-color: #10b981; }
        .timeline-circle.current { border-color: #3b82f6; background: #eff6ff; animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); } 50% { box-shadow: 0 0 0 8px rgba(59, 130, 246, 0); } }
        .timeline-icon { font-size: 28px; }
        .timeline-circle.completed .timeline-icon { color: white; }
        .timeline-circle.current .timeline-icon { color: #3b82f6; }
        .timeline-label { text-align: center; font-size: 14px; color: #6b7280; max-width: 140px; line-height: 1.4; }
        .timeline-point.completed .timeline-label, .timeline-point.current .timeline-label { color: #1f2937; font-weight: 600; }
        .timeline-date { font-size: 12px; color: #9ca3af; margin-top: 4px; }
        .timeline-point.completed .timeline-date, .timeline-point.current .timeline-date { color: #059669; font-weight: 500; }

        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .info-item { display: flex; flex-direction: column; gap: 8px; }
        .info-label { font-size: 13px; color: #6b7280; font-weight: 500; }
        .info-value { font-size: 15px; color: #1f2937; font-weight: 500; }

        .notice-box { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 4px solid #f59e0b; padding: 16px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
        .notice-icon { font-size: 24px; }
        .notice-text { font-size: 14px; color: #78350f; line-height: 1.5; }
        .notice-text strong { color: #92400e; font-weight: 600; }

        .items-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .items-table thead th { background: #f9fafb; padding: 14px 16px; text-align: left; font-size: 13px; font-weight: 600; color: #4b5563; border-bottom: 2px solid #e5e7eb; }
        .items-table tbody td { padding: 16px; border-bottom: 1px solid #f3f4f6; font-size: 14px; vertical-align: middle; }
        .items-table tbody tr:hover { background: #f9fafb; }
        
        .item-details { display: flex; flex-direction: column; gap: 4px; }
        .item-name { font-weight: 500; color: #1f2937; }
        .item-invoices { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
        .invoice-tag { display: inline-flex; flex-direction: column; gap: 2px; padding: 6px 12px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; font-size: 11px; color: #1e40af; }
        .invoice-tag .inv-num { font-weight: 600; font-size: 12px; }
        .invoice-tag .inv-date { color: #60a5fa; }
        .invoice-tag .inv-qty { color: #059669; font-weight: 600; }

        .qty-box { text-align: center; padding: 8px 12px; background: #f3f4f6; border-radius: 6px; font-weight: 600; font-size: 15px; color: #374151; }
        .qty-received { background: #d1fae5; color: #065f46; }

        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-align: center; white-space: nowrap; }
        .status-complete { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .status-pending { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .status-no-data { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
        .empty-icon { font-size: 48px; margin-bottom: 12px; }

        @media (max-width: 768px) {
            .main-container { padding: 16px; }
            .timeline-track { flex-direction: column; gap: 24px; }
            .timeline-line { display: none; }
            .timeline-point { flex-direction: row; width: 100%; gap: 16px; }
            .timeline-circle { margin-bottom: 0; width: 56px; height: 56px; }
            .timeline-label { text-align: left; max-width: none; }
        }
    </style>
</head>
<body>
    <div class="top-nav">
        <button class="back-button" onclick="history.back()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            ย้อนกลับ
        </button>
        <div class="po-header">เลขที่ PO: <strong id="po_display">{{ $ponum ?? '' }}</strong></div>
    </div>

    <div class="main-container">
        <div class="card">
            <div class="card-header">สถานะการสั่งซื้อ</div>
            <div class="card-body">
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
            </div>
        </div>

        <div class="notice-box" id="delivery_notice">
            <div class="notice-text">วันที่คาดว่าจะได้รับสินค้า: <strong id="expected_date">-</strong></div>
        </div>

        <div class="card">
            <div class="card-header">ข้อมูลผู้ขาย</div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item"><div class="info-label">รหัสร้านค้า</div><div class="info-value" id="vendor_code">-</div></div>
                    <div class="info-item"><div class="info-label">ชื่อร้านค้า</div><div class="info-value" id="vendor_name">-</div></div>
                    <div class="info-item" style="grid-column: 1 / -1;"><div class="info-label">ที่อยู่</div><div class="info-value" id="vendor_address">-</div></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">รายการสินค้า</div>
            <div class="card-body" style="padding: 0;">
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
                return { text: 'สินค้ามาครบแล้ว', class: 'status-complete' };
            }
            if (matched && totalReceived > 0) {
                return { text: 'กำลังจัดส่ง', class: 'status-pending' };
            }
            return { text: 'ยังไม่มีข้อมูล', class: 'status-no-data' };
        }

        async function loadPOData() {
            const poNumber = '{{ $ponum ?? '' }}';
            if (!poNumber) { alert('ไม่พบเลขที่ PO'); return; }

            try {
                // 1. ดึงข้อมูล PO จาก API
                const response = await fetch(`http://server_update:8000/api/getPODetail?PONum=${poNumber}`);
                if (!response.ok) throw new Error('เกิดข้อผิดพลาดในการโหลดข้อมูล');
                
                const data = await response.json();
                console.log('PO Data:', data);

                if (!data || !data.ms_podt || data.ms_podt.length === 0) {
                    alert('ไม่พบข้อมูลที่ตรงกับเลขที่ PO นี้');
                    return;
                }

                // Update vendor info
                document.getElementById('vendor_code').textContent = data.VendorCode || '-';
                document.getElementById('vendor_name').textContent = data.VendorName || '-';
                document.getElementById('vendor_address').textContent = [
                    data.ContAddr1, data.ContAddr2, data.ContDistrict,
                    data.ContAmphur, data.ContProvince, data.ContPostCode
                ].filter(Boolean).join(', ') || '-';

                // 2. ⭐ เตรียม API items สำหรับ batch matching
                const apiItems = data.ms_podt.map(item => ({
                    name: item.GoodName,
                    quantity: parseFloat(item.GoodQty2 || 0),
                    complete_flag: item.CompleteFlag || 'N'
                }));

                console.log('API Items for batch match:', apiItems);

                // 3. ⭐ เรียก batchMatch API
                const matchResponse = await fetch('{{ route("pooutside.batchMatch") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        po_number: poNumber,
                        api_items: apiItems
                    })
                });

                const matchResult = await matchResponse.json();
                console.log('Batch Match Result:', matchResult);

                // 4. แสดงผลในตาราง
                const tbody = document.getElementById('items_table_body');
                tbody.innerHTML = '';

                let hasAnyInvoice = false;
                let latestInvoiceDate = null;
                let totalItemsComplete = 0;

                matchResult.matches.forEach((item, index) => {
                    const orderedQty = item.api_quantity;
                    const totalReceived = item.total_received || 0;
                    const completeFlag = item.complete_flag;
                    const matched = item.matched;

                    if (matched) {
                        hasAnyInvoice = true;
                        if (item.date_invice && (!latestInvoiceDate || item.date_invice > latestInvoiceDate)) {
                            latestInvoiceDate = item.date_invice;
                        }
                    }
                    if (completeFlag === 'Y') totalItemsComplete++;

                    const status = getItemStatus(matched, completeFlag, totalReceived);

                    // Invoice tags
                    let invoiceTags = '';
                    if (item.records && item.records.length > 0) {
                        invoiceTags = item.records.map(r => `
                            <span class="invoice-tag">
                                <span class="inv-num">📋 ${r.invoice}</span>
                                <span class="inv-date">📅 ${r.date}</span>
                                <span class="inv-qty">📦 รับแล้ว: ${r.quantity}</span>
                            </span>
                        `).join('');
                    }

                    tbody.innerHTML += `
                        <tr>
                            <td>
                                <div class="item-details">
                                    <div class="item-name">${item.api_name || '-'}</div>
                                    ${invoiceTags ? `<div class="item-invoices">${invoiceTags}</div>` : ''}
                                </div>
                            </td>
                            <td style="text-align: center;"><div class="qty-box">${orderedQty.toFixed(2)}</div></td>
                            <td style="text-align: center;"><div class="qty-box qty-received">${totalReceived.toFixed(2)}</div></td>
                            <td style="text-align: center;"><span class="status-badge ${status.class}">${status.text}</span></td>
                        </tr>
                    `;
                });

                // 5. Update Timeline
                const timelineDates = {
                    step1: data.DocuDate ? formatDateThai(data.DocuDate) : '-',
                    step2: data.DocuDate ? formatDateThai(data.DocuDate) : '-',
                    step3: '-', step4: '-', step5: '-'
                };

                let currentStep = 2;
                let expectedDateToShow = null;

                if (hasAnyInvoice && latestInvoiceDate) {
                    const parts = latestInvoiceDate.split(/[-\/]/);
                    if (parts.length >= 3) {
                        const invDate = new Date(parts[0], parts[1] - 1, parts[2]);
                        invDate.setDate(invDate.getDate() + 15);
                        expectedDateToShow = formatDateThai(invDate);
                        timelineDates.step3 = formatDateThai(latestInvoiceDate);
                    }
                } else if (data.ShipDate) {
                    expectedDateToShow = formatDateThai(data.ShipDate);
                }

                document.getElementById('expected_date').textContent = expectedDateToShow || '-';

                if (totalItemsComplete === apiItems.length && apiItems.length > 0) {
                    currentStep = 5;
                    timelineDates.step5 = formatDateThai(new Date());
                    timelineDates.step4 = formatDateThai(new Date());
                } else if (hasAnyInvoice) {
                    currentStep = 4;
                }

                updateTimeline(currentStep, timelineDates);

            } catch (error) {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาด: ' + error.message);
            }
        }

        document.addEventListener('DOMContentLoaded', loadPOData);
    </script>
</body>
</html> -->