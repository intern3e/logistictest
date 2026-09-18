<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เอกสารชั่วคราว</title>

    <style>
        :root {
            --ink-900: #111111;
            --ink-700: #333333;
            --ink-500: #666666;
            --ink-300: #999999;
            --ink-150: #d4d4d4;
            --ink-100: #e6e6e6;
            --ink-050: #f4f4f4;
            --paper:   #ffffff;
            --line:    #dcdcdc;
            --radius:  6px;
        }

        * { box-sizing: border-box; }

        html, body {
            font-size: clamp(12px, 0.45vw + 7px, 16px);
        }

        body {
            font-family: 'Segoe UI', 'Noto Sans Thai', 'Roboto', sans-serif;
            background-color: var(--ink-050);
            margin: 0;
            padding: 0;
            color: var(--ink-900);
            line-height: 1.6;
        }

        a {
            color: var(--ink-900);
            text-decoration: none;
            font-weight: 600;
            border-bottom: 1px solid var(--ink-300);
        }
        a:hover { border-bottom-color: var(--ink-900); }

        /* HEADER */
        .header {
            background-color: var(--ink-900);
            color: #fff;
            border-radius: var(--radius);
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
            margin: 20px;
        }

        .header h2 {
            font-size: clamp(16px, 1vw + 6px, 22px);
            margin: 0;
            letter-spacing: 0.02em;
        }

        .buttons { display: flex; gap: 12px; align-items: center; }
        .buttons span { font-size: clamp(11px, 0.55vw + 5px, 14px); color: var(--ink-150); }

        .btn {
            padding: 7px 16px;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: clamp(11px, 0.55vw + 5px, 14px);
            transition: 0.2s;
            border: 1px solid #fff;
        }

        .btn-outline-light {
            background-color: transparent;
            color: #fff;
            border: 1px solid #ffffff55;
        }
        .btn-outline-light:hover { background-color: #ffffff22; border-color: #fff; }

        .btn-solid {
            background-color: #fff;
            color: var(--ink-900);
            border: 1px solid #fff;
        }
        .btn-solid:hover { background-color: var(--ink-150); }

        /* ปุ่มแก้ไข */
        .btn-edit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 10px;
            border-radius: var(--radius);
            background-color: var(--paper);
            color: var(--ink-900);
            font-size: clamp(10px, 0.5vw + 4px, 12px);
            font-weight: 600;
            text-decoration: none;
            transition: 0.2s;
            border: 1px solid var(--ink-150);
        }
        .btn-edit:hover {
            background-color: var(--ink-900);
            color: #fff;
            border-color: var(--ink-900);
        }

        /* FILTER */
        .filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            background-color: var(--paper);
            padding: 12px 24px;
            margin: 0 20px;
            border: 1px solid var(--line);
            border-radius: var(--radius);
        }

        .filter-form { display: flex; align-items: center; gap: 10px; font-size: clamp(11px, 0.55vw + 5px, 14px); }
        .filter-form label { font-weight: 600; color: var(--ink-700); }

        .filter-form input[type="date"],
        .headcom select {
            padding: 6px 12px;
            border-radius: var(--radius);
            border: 1px solid var(--ink-150);
            background-color: var(--paper);
            font-size: clamp(11px, 0.55vw + 5px, 14px);
            color: var(--ink-900);
        }

        .headcom { display: flex; align-items: center; gap: 8px; }
        .headcom label { font-weight: 600; color: var(--ink-700); font-size: clamp(11px, 0.55vw + 5px, 14px); }

        .search-box { margin-left: auto; }

        #search-input {
            padding: 6px 12px;
            border: 1px solid var(--ink-150);
            border-radius: var(--radius);
            font-size: clamp(11px, 0.55vw + 5px, 14px);
        }

        /* TABLE */
        .table-container { padding: 20px; overflow-x: auto; }

        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            background-color: var(--paper);
            font-size: clamp(10px, 0.62vw + 4px, 14px);
            border-radius: 10px;
            overflow: hidden;
            min-width: 1180px;
            border: 1px solid var(--line);
        }

        th, td {
            padding: 8px 10px;
            border: 1px solid var(--line);
            text-align: center;
            vertical-align: middle;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        th.customer-name, td.customer-name { text-align: left !important; }

        table thead {
            background-color: var(--ink-900);
            color: #fff;
            font-size: clamp(10px, 0.62vw + 4px, 14px);
        }

        table tbody tr:nth-child(even) { background-color: var(--ink-050); }
        table tbody tr:hover { background-color: var(--ink-100); }

        .wrap-text { text-align: left; white-space: normal; word-wrap: break-word; padding: 10px; }

        /* แถวที่ statusdeli == 1 */
        td.row-flagged { background-color: var(--ink-150) !important; font-weight: 600; }

        .col-no      { width: 4%; }
        .col-docid   { width: 7%; }
        .col-so      { width: 7%; }
        .col-headcom { width: 13%; }
        .col-comname { width: 12%; }
        .col-contact { width: 9%; }
        .col-tel     { width: 6%; }
        .col-doctype { width: 7%; }
        .col-emp     { width: 6%; }
        .col-date    { width: 7%; }
        .col-pdf     { width: 5%; }
        .col-detail  { width: 11%; }
        .col-edit    { width: 6%; }

        td.col-tel, th.col-tel {
            white-space: nowrap;
            text-align: center;
        }

        td.col-detail, th.col-detail {
            min-width: 140px;
        }

        /* ปุ่มไอคอนเอกสาร PDF */
        .pdf-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1.5px solid var(--ink-900);
            background-color: var(--paper);
            cursor: pointer;
            transition: 0.2s;
        }
        .pdf-btn:hover { background-color: var(--ink-900); }
        .pdf-btn:hover svg { stroke: #fff; }
        .pdf-btn svg { width: 17px; height: 17px; stroke: var(--ink-900); transition: 0.2s; }

        .pdf-btn-disabled {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1.5px dashed var(--ink-300);
            background-color: var(--ink-050);
            cursor: not-allowed;
        }
        .pdf-btn-disabled svg { width: 17px; height: 17px; stroke: var(--ink-300); }

        /* POPUP */
        .popup-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(17, 17, 17, 0.6);
            display: flex; align-items: center; justify-content: center;
            z-index: 1000; padding: 20px;
        }

        .popup-content {
            background-color: var(--paper);
            padding: 25px;
            border-radius: 12px;
            width: 100%;
            max-width: 1200px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow-y: auto;
            max-height: 90vh;
            border: 1px solid var(--line);
        }

        .close-btn { float: right; font-size: 24px; cursor: pointer; color: var(--ink-700); }
        .close-btn:hover { color: var(--ink-900); }

        textarea {
            font-family: 'Segoe UI', sans-serif;
            font-size: clamp(11px, 0.55vw + 5px, 14px);
            padding: 10px;
            border: 1px solid var(--ink-150);
            border-radius: var(--radius);
            resize: vertical;
            width: 100%;
        }

        /* 🚚 Popup สถานะจ่ายงาน - ปรับปรุงใหม่ */
        #deliveryPopup .popup-content {
            max-width: 900px;
            max-height: 85vh;
            padding: 30px;
        }

        #deliveryPopup h3 {
            font-size: 22px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--ink-150);
            color: var(--ink-900);
        }

        #dlvBody { line-height: 1.8; font-size: 15px; }

        #dlvBody .delivery-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        #dlvBody .delivery-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        #dlvBody .section-title {
            font-weight: 700;
            color: #1971c2;
            font-size: 16px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px dashed var(--ink-150);
        }

        #dlvBody .info-row {
            display: flex;
            margin-bottom: 8px;
            align-items: flex-start;
        }

        #dlvBody .info-label {
            font-weight: 600;
            color: var(--ink-700);
            min-width: 120px;
            flex-shrink: 0;
        }

        #dlvBody .info-value {
            color: var(--ink-900);
            flex: 1;
        }

        #dlvBody .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        #dlvBody .status-received {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        #dlvBody .status-pending {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        #dlvBody .no-delivery {
            text-align: center;
            padding: 40px 20px;
            background-color: #fff3cd;
            border: 2px dashed #ffc107;
            border-radius: 12px;
            color: #856404;
            font-size: 16px;
            font-weight: 600;
        }

/* ปุ่มสถานะจ่ายงาน */
        .btn-delivery-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.3s;
            border: none; /* เอาเส้นกรอบออกทั้งหมด */
            cursor: pointer;
            vertical-align: middle;
        }

        /* สีแดง: เริ่มต้น หรือ ยังไม่มีข้อมูล */
        .btn-delivery-status.no-delivery {
            color: #dc3545;
            background-color: #fff5f5; /* พื้นหลังสีแดงอ่อนมากๆ แทนกรอบ */
            animation: pulse-glow 2s infinite;
        }

        .btn-delivery-status.no-delivery:hover {
            background-color: #dc3545;
            color: #fff;
            animation: none;
        }

        /* สีเขียว: เมื่อมีข้อมูลการจ่ายงานแล้ว */
        .btn-delivery-status.has-delivery {
            color: #155724;
            background-color: #d4edda;
            animation: none;
        }

        .btn-delivery-status.has-delivery:hover {
            background-color: #155724;
            color: #fff;
        }

        /* เอฟเฟกต์เรืองแสงจางๆ แทนการใช้กรอบ */
        @keyframes pulse-glow {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
            }
            70% {
                box-shadow: 0 0 0 6px rgba(220, 53, 69, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>เอกสารชั่วคราว</h2>
        <div class="buttons">
            <span>👤 ผู้ใช้: {{ $creator }}</span>
            <a href="{{ route('document.insertdoc') }}" class="btn btn-solid">สร้างเอกสารชั่วคราว</a>
            @csrf
            <a href="http://server_update:8000/solist" class="btn btn-outline-light">🚪 หน้าหลัก</a>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('document.dashboarddoc') }}" class="filter-form" id="autoSearchForm">
            <label for="date">📅 วันที่: เดือน / วัน / ปี</label>
            <input type="date" id="date" name="date" value="{{ request('date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
            <button type="submit" style="display: none;">ค้นหา</button>
        </form>

        <div class="headcom">
            <label for="headcom">บริษัทผู้ส่ง :</label>
            <select id="headcom" onchange="filterTable()">
                <option value="">ทั้งหมด</option>
                <option value="บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด">บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด</option>
                <option value="บริษัท ทริปเปิ้ล อี อินโนเวชั่น จำกัด">บริษัท ทริปเปิ้ล อี อินโนเวชั่น จำกัด</option>
                <option value="บริษัท ทริปเปิ้ลพี แฟคทอรี่ จำกัด">บริษัท ทริปเปิ้ลพี แฟคทอรี่ จำกัด</option>
                <option value="บริษัท เอตะ แอนด์ พอล อินโนเวชั่น จำกัด">บริษัท เอตะ แอนด์ พอล อินโนเวชั่น จำกัด</option>
                <option value="บริษัท ฮิคาริ เดงกิ จำกัด">บริษัท ฮิคาริ เดงกิ จำกัด</option>
                <option value="บริษัท เอ อี แอนด์ ที อินเตอร์เนชั่นแนล จำกัด">บริษัท เอ อี แอนด์ ที อินเตอร์เนชั่นแนล จำกัด</option>
            </select>
        </div>

        <div class="search-box">
            <input type="text" id="search-input" placeholder=" ค้นหา เลขที่บิล" onkeyup="searchTable()">
        </div>
    </div>

    <div class="table-container">
        <table>
            <colgroup>
                <col class="col-no">
                <col class="col-docid">
                <col class="col-so">
                <col class="col-headcom">
                <col class="col-comname">
                <col class="col-contact">
                <col class="col-tel">
                <col class="col-doctype">
                <col class="col-emp">
                <col class="col-date">
                <col class="col-pdf">
                <col class="col-detail">
                <col class="col-edit">
            </colgroup>
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>เลขที่บิล</th>
                    <th>เลข SO</th>
                    <th>บริษัทผู้ส่ง</th>
                    <th>บริษัท</th>
                    <th>ผู้ติดต่อ</th>
                    <th class="col-tel">เบอร์โทร</th>
                    <th>ประเภทงาน</th>
                    <th>ผู้เปิดบิล</th>
                    <th>วันที่</th>
                    <th>เอกสาร PDF</th>
                    <th class="col-detail">ข้อมูลรายละเอียด</th>
                    <th>แก้ไข</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @foreach($docbill as $item)
                @php
                    $pdfPath = "temporary_bill/{$item->doc_id}.pdf";
                    $hasPdf = \Storage::exists('public/' . $pdfPath) || $item->statuspdf == 1;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="{{ $item->statusdeli == 1 ? 'row-flagged' : '' }}">
                        @if($item->statusdeli == 1)
                            <a href="https://drive.google.com/drive/u/0/search?q={{ $item->doc_id }}+parent:1WyDB1b01cDQ53Ap7B03UIGFbL6a2Y6WB" target="_blank">
                                {{ $item->doc_id }}
                            </a>
                        @else
                            {{ $item->doc_id }}
                        @endif
                    </td>
                    <td>{{ $item->so_id ?? '-' }}</td>
                    <td>{{ $item->headcom }}</td>
                    <td>{{ $item->com_name }}</td>
                    <td>{{ $item->contact_name }}</td>
                    <td class="col-tel">{{ $item->contact_tel }}</td>
                    <td>{{ $item->doctype }}</td>
                    <td>{{ $item->emp_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->time)->format('d/m/Y') }}</td>

                    {{-- เอกสาร PDF --}}
                    <td>
                        @if($hasPdf)
                            <a href="{{ asset('storage/' . $pdfPath) }}" target="_blank" class="pdf-btn" style="border-bottom:none;" title="เปิดเอกสาร PDF">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <path d="M14 2v6h6"/>
                                    <path d="M9 15h6"/>
                                    <path d="M9 11h6"/>
                                </svg>
                            </a>
                        @else
                            <span class="pdf-btn-disabled" title="ยังไม่มีไฟล์ PDF">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <path d="M14 2v6h6"/>
                                </svg>
                            </span>
                        @endif
                    </td>

                    <td class="col-detail">
                        <a href="javascript:void(0);" onclick="openPopup(
                            '{{ $item->doc_id }}',
                            '{{ $item->com_name }}',
                            '{{ $item->com_address }}',
                            '{{ $item->contact_name }}',
                            '{{ $item->contact_tel }}',
                            '{{ $item->notes }}'
                        )">
                            เพิ่มเติม
                        </a>
                        <br>
                        
                        {{-- ปุ่มสถานะจ่ายงาน: เริ่มต้นเป็นสีแดง (no-delivery) --}}
                        <a href="javascript:void(0);" 
                           class="btn-delivery-status no-delivery" 
                           id="btn-dlv-{{ $item->doc_id }}"
                           data-bill-id="{{ $item->doc_id }}"
                           onclick="openDeliveryStatus('{{ $item->doc_id }}', this)"
                           style="border-bottom:none; margin-top: 5px;">
                            <span class="btn-text">️ สถานะยังไม่จ่ายงาน</span>
                        </a>
                    </td>

                    {{-- ปุ่มแก้ไขข้อมูล --}}
                    <td>
                        <a href="{{ route('document.editdoc', $item->doc_id) }}" class="btn-edit" style="border-bottom:none;" title="แก้ไขเอกสาร">
                           แก้ไข
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if(isset($message))
            <br>
            <p style="text-align: center">{{ $message }}</p>
        @endif
    </div>

    <!-- Popup รายละเอียด -->
    <div class="popup-overlay" id="popup" style="display: none;">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>เลขที่บิล</th>
                            <th>บริษัท</th>
                            <th>ที่อยู่</th>
                            <th>ผู้ติดต่อ</th>
                            <th>เบอร์โทร</th>
                        </tr>
                    </thead>
                    <tbody id="popup-body-1"></tbody>
                </table>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>รายการ</th>
                            <th>จำนวน</th>
                        </tr>
                    </thead>
                    <tbody id="popup-body"></tbody>
                </table>
                <br>
                <textarea id="popup-body-3" readonly></textarea>
            </div>
        </div>
    </div>

    <!-- Popup: สถานะจ่ายงานให้คนขับ -->
    <div class="popup-overlay" id="deliveryPopup" style="display:none;">
        <div class="popup-content">
            <span class="close-btn" onclick="closeDeliveryStatus()">&times;</span>
            <h3>📦 สถานะจ่ายงานให้คนขับ — <span id="dlvBillId" style="color:#1971c2;"></span></h3>
            <div id="dlvBody" style="line-height:1.7;"></div>
        </div>
    </div>

    <script>
        function filterTable() {
            let selectedType = document.getElementById("headcom").value;
            let table = document.getElementById("table-body");
            let rows = table.getElementsByTagName("tr");

            for (let i = 0; i < rows.length; i++) {
                let typeCell = rows[i].getElementsByTagName("td")[3];
                if (typeCell) {
                    let typeText = typeCell.textContent.trim();
                    rows[i].style.display = (selectedType === "" || typeText === selectedType) ? "" : "none";
                }
            }
        }

        const form = document.getElementById('autoSearchForm');
        const dateInput = document.getElementById('date');
        dateInput.addEventListener('change', () => { form.submit(); });

        window.addEventListener('load', () => {
            if (!sessionStorage.getItem('hasAutoSubmitted')) {
                sessionStorage.setItem('hasAutoSubmitted', 'true');
                form.submit();
            } else {
                // ✅ ตรวจสอบสถานะจ่ายงานทั้งหมดเมื่อโหลดหน้าเว็บ
                checkAllDeliveryStatus();
            }
        });

        // ✅ ฟังก์ชันตรวจสอบสถานะจ่ายงานทั้งหมด
        async function checkAllDeliveryStatus() {
            const buttons = document.querySelectorAll('.btn-delivery-status');
            
            for (const btn of buttons) {
                const billId = btn.getAttribute('data-bill-id');
                if (!billId) continue;
                
                try {
                    const res = await fetch(DELIVERY_STATUS_URL + '?bill_id=' + encodeURIComponent(billId), 
                        {headers:{'Accept':'application/json'}});
                    const j = await res.json();
                    
                    // ถ้ามีข้อมูลการจ่ายงาน ให้เปลี่ยนเป็นสีเขียว
                    if (j.found && j.rows && j.rows.length > 0) {
                        btn.className = 'btn-delivery-status has-delivery';
                        btn.innerHTML = '<span class="btn-text">✓ จ่ายงานแล้ว</span>';
                    }
                } catch (e) {
                    console.error('Error checking status for bill', billId, e);
                    // ถ้า error ยังคงเป็นสีแดง
                }
            }
        }

        function openPopup(doc_id, com_name, com_address, contact_name, contact_tel, notes) {
            document.getElementById("popup").style.display = "flex";
            document.getElementById("popup-body-1").innerHTML = `
                <tr>
                    <td>${doc_id}</td>
                    <td>${com_name}</td>
                    <td>${com_address}</td>
                    <td>${contact_name}</td>
                    <td>${contact_tel}</td>
                </tr>
            `;
            document.getElementById("popup-body-3").value = notes;

            let secondPopupBody = document.getElementById("popup-body");
            secondPopupBody.innerHTML = "<tr><td colspan='2'>กำลังโหลดข้อมูล...</td></tr>";

            fetch(`/get-docbill-detail/${doc_id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        secondPopupBody.innerHTML = "";
                        data.forEach(item => {
                            secondPopupBody.insertAdjacentHTML("beforeend", `
                                <tr>
                                    <td>${item.item_name}</td>
                                    <td>${item.quantity}</td>
                                </tr>
                            `);
                        });
                    } else {
                        secondPopupBody.innerHTML = "<tr><td colspan='2'>ไม่มีข้อมูล</td></tr>";
                    }
                })
                .catch(error => {
                    console.error("Error fetching data:", error);
                    secondPopupBody.innerHTML = "<tr><td colspan='2'>เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>";
                });
        }

        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }

        window.onclick = function(event) {
            let popup = document.getElementById("popup");
            if (event.target === popup) { closePopup(); }
        }

        const DELIVERY_STATUS_URL = "{{ route('document.deliveryStatus') }}";
        
        function escHtml(s){ return String(s == null ? '' : s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
        
        function fmtDT(s){
            if(!s) return '-';
            const d = new Date(String(s).replace(' ','T'));
            if(isNaN(d)) return s;
            const p = n => String(n).padStart(2,'0');
            return p(d.getDate())+'/'+p(d.getMonth()+1)+'/'+(d.getFullYear()+543)+' '+p(d.getHours())+':'+p(d.getMinutes());
        }

        async function openDeliveryStatus(billId, btnElement) {
            const pop = document.getElementById('deliveryPopup');
            document.getElementById('dlvBillId').textContent = billId;
            document.getElementById('dlvBody').innerHTML = '<div style="text-align:center;padding:40px;"><div style="font-size:18px;color:#666;">กำลังโหลดข้อมูล...</div></div>';
            pop.style.display = 'flex';
            
            try {
                const res = await fetch(DELIVERY_STATUS_URL + '?bill_id=' + encodeURIComponent(billId), {headers:{'Accept':'application/json'}});
                const j = await res.json();
                
                if (!j.found || !j.rows.length) {
                    // ไม่พบข้อมูล: ปุ่มยังคงเป็นสีแดง (no-delivery) ตามค่าเริ่มต้น
                    document.getElementById('dlvBody').innerHTML = `
                        <div class="no-delivery">
                            <div style="font-size:48px;margin-bottom:15px;">⚠️</div>
                            <div>บิลนี้ยังไม่มีการจ่ายงานให้คนขับ</div>
                            <div style="font-size:14px;margin-top:10px;color:#6c757d;">กรุณาติดต่อฝ่ายจ่ายงานเพื่อดำเนินการ</div>
                        </div>
                    `;
                } else {
                    // พบข้อมูล: เปลี่ยนปุ่มจากสีแดง เป็น สีเขียว/น้ำเงิน (has-delivery)
                    if (btnElement) {
                        btnElement.className = 'btn-delivery-status has-delivery';
                        btnElement.innerHTML = '<span class="btn-text">✓ จ่ายงานแล้ว</span>';
                    }

                    document.getElementById('dlvBody').innerHTML = j.rows.map(r => {
                        const recv = r.received
                            ? '<span class="status-badge status-received">✓ รับงานแล้ว</span>'
                            : '<span class="status-badge status-pending"> ยังไม่รับงาน</span>';
                        return `
                            <div class="delivery-card">
                                <div class="section-title">📋 ข้อมูลการจ่ายงาน</div>
                                <div class="info-row">
                                    <div class="info-label">ผู้จ่ายงาน:</div>
                                    <div class="info-value"><b>${escHtml(r.name_pick || '-')}</b></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">เวลาจ่ายงาน:</div>
                                    <div class="info-value">${fmtDT(r.time_pick)}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">คนขับ:</div>
                                    <div class="info-value"><b>${escHtml(r.driver_name || '-')}</b></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">รถ/ขนส่ง:</div>
                                    <div class="info-value">${escHtml(r.transport_name || '-')}</div>
                                </div>
                                
                                <div class="section-title" style="margin-top:20px;">🚚 การรับงาน / สถานะ</div>
                                <div class="info-row">
                                    <div class="info-label">สถานะ:</div>
                                    <div class="info-value">${recv}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">ผู้รับงาน:</div>
                                    <div class="info-value"><b>${escHtml(r.check_name || '-')}</b></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">เวลารับงาน:</div>
                                    <div class="info-value">${fmtDT(r.check_time)}</div>
                                </div>
                                ${r.delivery_date ? `
                                <div class="info-row">
                                    <div class="info-label">กำหนดส่ง:</div>
                                    <div class="info-value" style="color:#6c757d;">${escHtml(r.delivery_date)}</div>
                                </div>` : ''}
                            </div>`;
                    }).join('');
                }
            } catch (e) {
                document.getElementById('dlvBody').innerHTML = '<div class="no-delivery" style="background-color:#f8d7da;border-color:#f5c6cb;color:#721c24;">โหลดข้อมูลไม่สำเร็จ<br><small style="font-size:13px;">กรุณาลองใหม่อีกครั้ง</small></div>';
            }
        }

        function closeDeliveryStatus() { 
            document.getElementById('deliveryPopup').style.display = 'none'; 
        }

        function searchTable() {
            let searchInput = document.getElementById("search-input").value.toLowerCase();
            let table = document.querySelector("table tbody");
            let rows = table.getElementsByTagName("tr");

            for (let i = 0; i < rows.length; i++) {
                let row = rows[i];
                let cells = row.getElementsByTagName("td");
                let docId = cells[1] ? cells[1].textContent.toLowerCase() : "";
                row.style.display = (docId.indexOf(searchInput) > -1) ? "" : "none";
            }
        }
    </script>
</body>
</html>