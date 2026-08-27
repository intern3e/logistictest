<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <title>สร้างใบชั่วคราว</title>

<style>
    * { box-sizing: border-box; }
    
    :root {
        --primary: #3E6AE1;
        --primary-hover: #2f56c4;
        --primary-light: #eef2fd;
        --primary-border: #c7d5f5;
        
        --amber: #f59e0b;
        --amber-light: #fef3c7;
        
        --red: #dc2626;
        --red-light: #fee2e2;
        
        --bg: #f5f7fa;
        --surface: #ffffff;
        --border: #e5e7eb;
        --border-light: #f0f2f5;
        
        --text: #1b2d4f;
        --text-secondary: #374151;
        --text-muted: #6b7280;
        
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 12px rgba(62,106,225,0.10);
        
        --radius: 12px;
        --radius-sm: 6px;
    }
    
    body {
        font-family: 'Sarabun', 'Segoe UI', system-ui, sans-serif;
        background: var(--bg);
        color: var(--text);
        margin: 0;
        padding: 0;
        line-height: 1.5;
        font-size: 14px;
    }
    
    .container {
        max-width: 1400px;
        margin: 4px auto 14px;
        padding: 0 20px;
    }
    
    .header-bar {
        padding: 6px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    
    .text-dark {
        font-size: 17px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .text-dark::before {
        content: '🧾';
        font-size: 19px;
    }
    
    .btn-back {
        background: var(--surface);
        color: var(--text-secondary);
        border: 1px solid var(--border);
        padding: 10px 20px;
        border-radius: var(--radius-sm);
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .btn-back:hover {
        background: var(--bg);
        border-color: var(--text-muted);
    }
    
    /* ===== GRID LAYOUT ===== */
    .page {
        display: grid;
        grid-template-columns: 2fr 3fr;  /* ซ้าย 40% / ขวา 60% */
        grid-template-rows: auto auto;
        gap: 20px;
        margin-bottom: 24px;
    }
    
    /* แถวบน: ข้อมูลเอกสาร + ข้อมูลบริษัท/ผู้ติดต่อ รวมเป็นช่องเดียว เต็มความกว้าง */
    .area-doc-company {
        grid-column: 1 / 3;
        grid-row: 1 / 2;
    }
    
    /* แถวล่าง: ที่อยู่จัดส่ง (ซ้าย) | รายการสินค้า (ขวา) */
    .area-address {
        grid-column: 1 / 2;
        grid-row: 2 / 3;
        align-self: start;
        min-height: 340px;
    }
    
    .area-items {
        grid-column: 2 / 3;
        grid-row: 2 / 3;
        min-height: 340px;
    }
    
    .card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        height: 100%;
    }
    
    .card-head {
        padding: 14px 24px;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--surface);
    }
    
    .card-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .ci-green {
        background: #dbeafe;
        border: 1px solid #bfdbfe;
    }
    
    .ci-blue {
        background: #dbeafe;
        border: 1px solid #bfdbfe;
    }
    
    .ci-amber {
        background: var(--amber-light);
        border: 1px solid #fde68a;
    }
    
    .card-head-text h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }
    
    .card-head-text p {
        font-size: 12px;
        color: var(--text-muted);
        margin: 2px 0 0;
    }
    
    .card-badge {
        margin-left: auto;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        background: var(--bg);
        color: var(--text-muted);
        border: 1px solid var(--border);
    }
    
    .card-body {
        padding: 24px;
    }
    
    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .span-full {
        grid-column: 1 / -1;
    }
    
    .header-field-date {
        min-width: 0;
        flex: 0 0 auto;
    }
    
    .header-field-doctype {
        min-width: 240px;
        flex: 1 1 240px;
        max-width: 320px;
    }
    
    .headcom-com-row {
        display: grid;
        grid-template-columns: 1fr 2.5fr;
        gap: 16px;
        margin-bottom: 0;
    }
    
    .so-contact-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px 16px;
    }
    
    .field {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 0;
    }
    
    .field label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .field label .req {
        color: var(--red);
        font-size: 13px;
    }
    
    .field input[type="text"],
    .field input[type="date"],
    .field select {
        width: 100%;
        height: 42px;
        padding: 10px 12px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--surface);
        color: var(--text);
        font-size: 14px;
        font-family: inherit;
        outline: none;
        transition: all 0.2s;
    }
    
    .field textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--surface);
        color: var(--text);
        font-size: 14px;
        font-family: inherit;
        outline: none;
        transition: all 0.2s;
    }
    
    .field input:focus,
    .field select:focus,
    .field textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(62,106,225,0.10);
    }
    
    .field.is-invalid input,
    .field.is-invalid select,
    .field.is-invalid textarea {
        border-color: var(--red) !important;
        background: var(--red-light) !important;
    }
    
    #com_address {
        min-height: 64px;
    }
    
    #notes {
        min-height: 64px;
    }
    
    .field textarea {
        resize: vertical;
        min-height: 80px;
        font-family: inherit;
    }
    
    .field select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M3 4.5L6 7.5L9 4.5' stroke='%236b7280' stroke-width='1.5' stroke-linecap='round' fill='none'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }
    
    #headcom:invalid {
        text-align: center;
        text-align-last: center;
    }
    
    #headcom:valid {
        text-align: left;
        text-align-last: left;
    }
    
    #doctype:invalid {
        text-align: center;
        text-align-last: center;
    }
    
    #doctype:valid {
        text-align: left;
        text-align-last: left;
    }
    
    .autocomplete-list {
        list-style: none;
        margin: 4px 0 0;
        padding: 4px;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-md);
        max-height: 240px;
        overflow-y: auto;
        z-index: 100;
    }
    
    .autocomplete-list li {
        padding: 10px 12px;
        font-size: 13px;
        color: var(--text);
        border-radius: 4px;
        cursor: pointer;
    }
    
    .autocomplete-list li:hover {
        background: var(--primary-light);
    }
    
    .coords-row {
        display: flex;
        gap: 8px;
        align-items: stretch;
    }
    
    .coords-row input {
        flex: 1;
        font-family: 'JetBrains Mono', monospace;
        font-size: 13px;
    }
    
    .btn-custom {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: 42px;
        padding: 0 16px;
        border: 1.5px solid var(--primary);
        background: var(--primary);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        border-radius: var(--radius-sm);
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s;
    }
    
    .btn-custom:hover {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
    }
    
    .preview-frame {
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        overflow: hidden;
        background: var(--bg);
        height: 200px;
        position: relative;
        margin-top: 8px;
    }
    
    .preview-frame iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
    
    .preview-empty {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 16px;
        text-align: center;
        background: var(--bg);
        color: var(--text-muted);
        font-size: 12px;
    }
    
    .tbl-wrap {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 296px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        margin-bottom: 16px;
    }
    
    table.table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    
    table.table thead {
        background: var(--primary);
    }
    
    table.table th {
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 700;
        color: #fff;
        text-align: left;
        border-right: 1px solid rgba(255,255,255,0.15);
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 2;
        background: var(--primary);
    }
    
    table.table th:last-child {
        border-right: none;
    }
    
    table.table td {
        padding: 10px 16px;
        border-bottom: 1px solid var(--border-light);
        border-right: 1px solid var(--border-light);
        vertical-align: middle;
    }
    
    table.table td:last-child {
        border-right: none;
    }
    
    table.table tbody tr:last-child td {
        border-bottom: none;
    }
    
    table.table tbody tr:hover td {
        background: var(--primary-light);
    }
    
    .form-control1 {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid var(--border);
        border-radius: 4px;
        font-size: 13px;
        background: var(--surface);
        color: var(--text);
        font-family: 'JetBrains Mono', monospace;
    }
    
    .checkbox-container {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--primary);
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        font-family: inherit;
        transition: all 0.2s;
    }
    
    .btn-success {
        background: var(--primary);
        color: #fff;
    }
    
    .btn-success:hover {
        background: var(--primary-hover);
    }
    
    .btn-danger {
        background: #fff;
        color: var(--red);
        border-color: var(--red);
        padding: 6px 12px;
        font-size: 12px;
    }
    
    .btn-danger:hover {
        background: var(--red-light);
    }
    
    .validation-summary {
        background: var(--red-light);
        border: 1px solid #fecaca;
        border-radius: var(--radius);
        padding: 16px 20px;
        margin-bottom: 20px;
        display: none;
    }
    
    .validation-summary.show {
        display: block;
    }
    
    .validation-summary-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--red);
        margin: 0 0 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .validation-summary ul {
        margin: 0;
        padding-left: 20px;
        font-size: 13px;
        color: var(--text-secondary);
    }
    
    .validation-summary li {
        margin-bottom: 4px;
    }
    
    .submit-row {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        margin-top: 24px;
        padding: 24px 0;
    }
    
    .btn-submit-main {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--primary) 0%, #5a85e8 100%);
        color: #fff;
        border: none;
        padding: 16px 48px;
        border-radius: var(--radius);
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        min-width: 260px;
        min-height: 48px;
        font-family: inherit;
        transition: all 0.2s;
        box-shadow: var(--shadow-md);
    }
    
    .btn-submit-main:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(62,106,225,0.25);
    }
    
    .btn-submit-main:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .btn-hint {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .btn-hint.warn {
        color: var(--red);
        font-weight: 600;
    }
    
    @media (max-width: 1150px) {
        .header-field-date,
        .header-field-doctype {
            flex: 1 1 100%;
            min-width: 0;
            max-width: none;
            width: 100%;
        }

        .header-field-date input#datestamp {
            width: 100%;
        }
    }
    
    @media (max-width: 992px) {
        .page {
            grid-template-columns: 1fr;
            grid-template-rows: auto;
        }
        
        .area-doc-company,
        .area-address,
        .area-items {
            grid-column: 1 / -1;
            grid-row: auto;
        }
    }
    
    @media (max-width: 640px) {
        .container {
            padding: 0 12px;
            margin: 4px auto 12px;
        }
        
        .header-bar {
            padding: 6px 0;
        }
        
        .text-dark {
            font-size: 20px;
        }
        
        .card-body {
            padding: 16px;
        }
        
        .form-grid-2 {
            grid-template-columns: 1fr;
        }
        
        .headcom-com-row {
            grid-template-columns: 1fr;
        }
        
        .so-contact-row {
            grid-template-columns: 1fr;
        }
        
        .btn-submit-main {
            width: 100%;
            min-width: 0;
        }
    }
</style>

</head>
<body>

<div class="container">

<div class="header-bar">
    <h2 class="text-dark">สร้างใบชั่วคราว</h2>
    <button onclick="history.back()" class="btn-back">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        ย้อนกลับ
    </button>
</div>

<form id="billForm">

<div class="page">

    <div class="card area-doc-company">
        <div class="card-head" style="justify-content:space-between;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="card-icon ci-green">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <rect x="3" y="2" width="12" height="14" rx="2.5" stroke="#3E6AE1" stroke-width="1.5"/>
                        <path d="M6 6h6M6 9h6M6 12h4" stroke="#3E6AE1" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="card-head-text">
                    <h3>ข้อมูลเอกสาร &amp; บริษัท / ผู้ติดต่อ</h3>
                    <p>บริษัทหัวเอกสาร ประเภทบิล วันที่ และข้อมูลบริษัทคู่ค้า ผู้ติดต่อ</p>
                </div>
            </div>

            <div class="field header-field-date" style="display:flex;flex-direction:row;align-items:center;gap:10px;margin:0;">
                <label for="datestamp" style="white-space:nowrap;margin:0;">วันที่เอกสาร <span class="req">*</span></label>
                <input type="date" id="datestamp" name="datestamp" required style="width:160px;">
            </div>

            <div class="field header-field-doctype" style="margin:0;">
                <div style="display:flex;flex-direction:row;align-items:center;gap:10px;">
                    <label for="doctype" style="white-space:nowrap;margin:0;">ประเภทบิล <span class="req">*</span></label>
                    <select id="doctype" name="doctype" required onchange="toggleOtherInput()" style="flex:1;">
                        <option value="" disabled selected>-- กรุณาเลือกประเภทบิล --</option>
                        <option value="รับของ">รับของ</option>
                        <option value="ส่งของ">ส่งของ</option>
                        <option value="รับของและส่งของ">รับของและส่งของ</option>
                        <option value="ส่งของและรับกลับ">ส่งของและรับกลับ</option>
                        <option value="อื่นๆ" id="other_option">อื่น ๆ</option>
                    </select>
                </div>
                <input type="text" id="other_input" name="other_input" style="display:none;margin-top:8px;" placeholder="กรุณากรอกข้อมูล" oninput="updateOtherOption()">
            </div>
        </div>
        <div class="card-body">
            <div class="form-grid-2" style="row-gap:10px">

                <div class="span-full headcom-com-row">
                    <div class="field">
                        <label for="headcom">ชื่อบริษัทหัวเอกสาร <span class="req">*</span></label>
                        <select id="headcom" name="headcom" required onchange="toggleSoBlock()">
                            <option value="" disabled selected style="text-align:center;">-- กรุณาเลือกชื่อบริษัท --</option>
                            <option value="บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด" style="text-align:left;">บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด</option>
                            <option value="บริษัท ทริปเปิ้ล อี อินโนเวชั่น จำกัด" style="text-align:left;">บริษัท ทริปเปิ้ล อี อินโนเวชั่น จำกัด</option>
                            <option value="บริษัท ทริบเปิ้ล พี แฟคตอรี่ แอนด์ เอ็นจิเนียริ่ง จำกัด" style="text-align:left;">บริษัท ทริบเปิ้ล พี แฟคตอรี่ แอนด์ เอ็นจิเนียริ่ง จำกัด</option>
                            <option value="บริษัท เอตะ แอนด์ พอล อินโนเวชั่น จำกัด" style="text-align:left;">บริษัท เอตะ แอนด์ พอล อินโนเวชั่น จำกัด</option>
                            <option value="บริษัท ฮิคาริ เดงกิ จำกัด" style="text-align:left;">บริษัท ฮิคาริ เดงกิ จำกัด</option>
                            <option value="บริษัท เอ อี แอนด์ ที อินเตอร์เนชั่นแนล จำกัด" style="text-align:left;">บริษัท เอ อี แอนด์ ที อินเตอร์เนชั่นแนล จำกัด</option>
                            <option value="บริษัท ทริปเปิ้ล อี ไลท์ติ้ง จำกัด" style="text-align:left;">บริษัท ทริปเปิ้ล อี ไลท์ติ้ง จำกัด</option>
                            <option value="บริษัท ทริปเปิ้ล อี เอ็มไพร์ กรุ๊ป จำกัด" style="text-align:left;">บริษัท ทริปเปิ้ล อี เอ็มไพร์ กรุ๊ป จำกัด</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="com_name">บริษัท</label>
                        <div style="position:relative;">
                            <input type="text" id="com_name" name="com_name" autocomplete="off" placeholder="พิมพ์ชื่อบริษัทอย่างน้อย 3 ตัวอักษร">
                            <ul id="autocomplete_list" class="autocomplete-list" style="display:none;"></ul>
                            <div id="no_data_message" style="display:none;">ไม่มีข้อมูล</div>
                        </div>
                    </div>
                </div>

                <div class="span-full so-contact-row">
                    <div class="field" id="so_block" style="display:none;">
                        <label for="so_num">เลข SO</label>
                        <div class="coords-row">
                            <input type="text" id="so_num" placeholder="เช่น 69/012494">
                            <button type="button" class="btn-custom" onclick="fetchSODetail()">ค้นหา</button>
                        </div>
                    </div>
                    <input type="hidden" id="so_id" name="so_id">

                    <div class="field">
                        <label for="contact_name">ชื่อผู้ติดต่อ <span class="req">*</span></label>
                        <input type="text" id="contact_name" name="contact_name">
                    </div>
                    <div class="field">
                        <label for="contact_tel">เบอร์ติดต่อ <span class="req">*</span></label>
                        <input type="text" id="contact_tel" name="contact_tel">
                    </div>
                </div>
            </div>
            <input type="hidden" id="inpUser" value="{{ $creator }}">
            <input type="hidden" id="id_com" name="id_com">
        </div>
    </div>

    <div class="card area-address">
        <div class="card-head">
            <div class="card-icon ci-amber">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M9 1.8c-2.76 0-5 2.24-5 5 0 3.75 5 9.4 5 9.4s5-5.65 5-9.4c0-2.76-2.24-5-5-5z" stroke="#b45309" stroke-width="1.5" stroke-linejoin="round"/>
                    <circle cx="9" cy="6.8" r="1.8" stroke="#b45309" stroke-width="1.5"/>
                </svg>
            </div>
            <div class="card-head-text">
                <h3>ที่อยู่จัดส่ง &amp; แผนที่</h3>
                <p>ที่อยู่ พิกัด และรายละเอียดสำหรับคนขับ</p>
            </div>
        </div>
        <div class="card-body">
            <div class="form-grid-2" style="row-gap:10px">
                <div class="field span-full">
                    <label for="com_address">ที่อยู่จัดส่ง <span class="req">*</span></label>
                    <textarea id="com_address" name="com_address" rows="2"></textarea>
                </div>
                <div class="field span-full">
                    <label for="com_la_long">พิกัด (ละติจูด, ลองจิจูด)</label>
                    <div class="coords-row">
                        <input type="text" id="com_la_long" name="com_la_long" placeholder="13.7563, 100.5018">
                        <button type="button" class="btn-custom" onclick="openGoogleMaps()">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M7 1.5c-2.21 0-4 1.79-4 4 0 3 4 7 4 7s4-4 4-7c0-2.21-1.79-4-4-4z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                                <circle cx="7" cy="5.5" r="1.4" stroke="currentColor" stroke-width="1.4"/>
                            </svg>
                            Google Maps
                        </button>
                    </div>
                </div>
                <div class="field span-full">
                    <label for="notes">รายละเอียดเพิ่มเติมเกี่ยวกับการจัดส่ง <span class="req">*</span> จำเป็น</label>
                    <textarea id="notes" name="notes" rows="2"></textarea>
                </div>
            </div>

            <div style="margin-top:20px">
                <div class="preview-frame">
                    <iframe id="mapFrame" allowfullscreen="" loading="lazy"></iframe>
                    <div class="preview-empty" id="mapEmpty">
                        กรอกพิกัด ละติจูด, ลองจิจูด<br>เพื่อแสดงแผนที่
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="card area-items">
        <div class="card-head">
            <div class="card-icon ci-amber">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <rect x="2" y="5" width="10" height="9" rx="1.5" stroke="#b45309" stroke-width="1.5"/>
                    <path d="M12 7.5l4 2.5v4h-4" stroke="#b45309" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="4.5" cy="15" r="1.3" stroke="#b45309" stroke-width="1.3"/>
                    <circle cx="13.5" cy="15" r="1.3" stroke="#b45309" stroke-width="1.3"/>
                </svg>
            </div>
            <div class="card-head-text">
                <h3 style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <span>รายการสินค้า</span>
                    <span id="so_search_status" style="font-size:12px;font-weight:400;color:var(--text-muted);"></span>
                </h3>
                <p>เพิ่มรายการและจำนวนที่ต้องการ</p>
            </div>
            <button type="button" class="btn btn-success insert-btn" style="margin-left:auto;padding:8px 16px;font-size:12px;">+ เพิ่มสินค้า</button>
            <button type="button" class="btn btn-danger" id="clearAllItemsBtn" style="padding:8px 16px;font-size:12px;">ลบสินค้าทั้งหมด</button>
        </div>
        <div class="card-body">
            <div class="tbl-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:8%;text-align:center">ลำดับ</th>
                            <th style="width:52%">รายการ</th>
                            <th style="width:20%;text-align:center">จำนวน</th>
                            <th style="width:20%;text-align:center">ลบ</th>
                        </tr>
                    </thead>
                    <tbody id="detail"></tbody>
                </table>
            </div>

            <div class="validation-summary" id="validationSummary">
                <p class="validation-summary-title">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <circle cx="8" cy="8" r="7" stroke="#dc2626" stroke-width="1.5"/>
                        <path d="M8 4.5v4M8 11h.01" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span>กรุณากรอกข้อมูลให้ครบก่อนบันทึก</span>
                </p>
                <ul id="validationList"></ul>
            </div>

            <div class="submit-row">
                <button type="button" id="submitBill" class="btn-submit-main">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                        <path d="M3.5 9l4 4 6-7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    บันทึก
                </button>
                <div class="btn-hint" id="btnHint">
                    <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
                        <circle cx="7" cy="7" r="6" stroke="currentColor" stroke-width="1.4"/>
                        <path d="M7 4v3.5M7 9.5h.01" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    </svg>
                    <span id="btnHintText">กรุณากรอกข้อมูลที่จำเป็นให้ครบก่อน</span>
                </div>
            </div>
        </div>
    </div>

</div>

</form>

</div>

<script>
    const SO_ENABLED_COMPANY = "บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด";

    function toggleSoBlock() {
        const headcom = document.getElementById("headcom").value;
        const soBlock = document.getElementById("so_block");
        if (headcom === SO_ENABLED_COMPANY) {
            soBlock.style.display = "flex";
        } else {
            soBlock.style.display = "none";
            document.getElementById("so_num").value = "";
            document.getElementById("so_id").value = "";
            document.getElementById("so_search_status").textContent = "";
        }
        refreshSubmitState();
    }

    const VALIDATION_RULES = [
        { id: "datestamp",    label: "วันที่เอกสาร" },
        { id: "doctype",      label: "ประเภทบิล" },
        { id: "headcom",      label: "ชื่อบริษัทหัวเอกสาร" },
        { id: "com_address",  label: "ที่อยู่จัดส่ง" },
        { id: "contact_name", label: "ชื่อผู้ติดต่อ" },
        { id: "contact_tel",  label: "เบอร์ติดต่อ" },
        { id: "notes",        label: "รายละเอียดเพิ่มเติมเกี่ยวกับการจัดส่ง" },
    ];

    function getFieldWrapper(el) { return el ? el.closest(".field") : null; }
    function clearFieldError(el) {
        const wrap = getFieldWrapper(el);
        if (wrap) wrap.classList.remove("is-invalid");
    }
    function markFieldError(el) {
        const wrap = getFieldWrapper(el);
        if (wrap) wrap.classList.add("is-invalid");
    }

    function validateForm() {
        const errors = [];
        VALIDATION_RULES.forEach(function (rule) {
            const el = document.getElementById(rule.id);
            const invalid = !el || !el.value || !el.value.trim();
            if (invalid) { errors.push(rule); markFieldError(el); }
            else { clearFieldError(el); }
        });

        // เลข SO ไม่บังคับกรอกแล้ว จึงไม่ต้องตรวจสอบหรือขึ้นสีแดง
        const soEl = document.getElementById("so_num");
        if (soEl) { clearFieldError(soEl); }

        return errors;
    }

    function refreshSubmitState() {
        const btn = document.getElementById("submitBill");
        const hint = document.getElementById("btnHint");
        const hintText = document.getElementById("btnHintText");
        if (!btn) return;
        const errors = validateForm();
        if (errors.length === 0) {
            btn.classList.remove("is-incomplete");
            btn.title = "";
            if (hint) hint.classList.remove("warn");
            if (hintText) hintText.textContent = "พร้อมบันทึก ✓";
            const summary = document.getElementById("validationSummary");
            if (summary) summary.classList.remove("show");
        } else {
            btn.classList.add("is-incomplete");
            btn.title = `ยังขาด ${errors.length} ช่อง: ` + errors.map(function (e) { return e.label; }).join(", ");
            if (hint) hint.classList.add("warn");
            if (hintText) hintText.textContent = `ยังขาดข้อมูลอีก ${errors.length} ช่อง — โปรดกรอกให้ครบ`;
        }
        return errors;
    }

    function showValidationSummary(errors) {
        const box = document.getElementById("validationSummary");
        const list = document.getElementById("validationList");
        if (!box || !list) return;
        list.innerHTML = "";
        errors.forEach(function (err) {
            const li = document.createElement("li");
            li.textContent = err.label;
            list.appendChild(li);
        });
        box.classList.add("show");
        box.scrollIntoView({ behavior: "smooth", block: "center" });
        const first = errors[0];
        if (first && first.id) {
            const el = document.getElementById(first.id);
            if (el) setTimeout(function () { el.focus(); }, 400);
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        VALIDATION_RULES.concat([{ id: "so_num" }]).forEach(function (rule) {
            const el = document.getElementById(rule.id);
            if (!el) return;
            el.addEventListener("input", refreshSubmitState);
            el.addEventListener("change", refreshSubmitState);
            el.addEventListener("blur", refreshSubmitState);
        });
        refreshSubmitState();
    });

    const detailObserver = new MutationObserver(refreshSubmitState);
    document.addEventListener("DOMContentLoaded", function () {
        const detailBody = document.getElementById("detail");
        if (detailBody) detailObserver.observe(detailBody, { childList: true, subtree: true });
    });

    function escapeHtmlSo(str) {
        return String(str)
            .replace(/&/g, "&amp;").replace(/</g, "&lt;")
            .replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    const VISIBLE_ROW_COUNT = 8;

    function adjustTableVisibleRows() {
        const wrap = document.querySelector('.tbl-wrap');
        const table = wrap ? wrap.querySelector('table') : null;
        const thead = table ? table.querySelector('thead') : null;
        const tbody = document.getElementById('detail');
        if (!wrap || !thead || !tbody) return;

        const firstRow = tbody.querySelector('tr');
        if (!firstRow) return; // no rows yet, keep whatever height is currently set

        const headerHeight = thead.getBoundingClientRect().height;
        const rowHeight = firstRow.getBoundingClientRect().height;
        if (!rowHeight) return;

        const targetHeight = Math.ceil(headerHeight + rowHeight * VISIBLE_ROW_COUNT);
        wrap.style.maxHeight = targetHeight + 'px';
    }

    let resizeDebounce = null;
    window.addEventListener('resize', function () {
        clearTimeout(resizeDebounce);
        resizeDebounce = setTimeout(adjustTableVisibleRows, 150);
    });

    function renumberRows() {
        const rows = document.querySelectorAll('#detail tr');
        rows.forEach(function (row, index) {
            const numCell = row.querySelector('.row-num');
            if (numCell) numCell.textContent = index + 1;
        });
        adjustTableVisibleRows();
    }

    function addItemRow(name, qty) {
        const tableBody = document.getElementById("detail");
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td class="row-num" style="text-align:center;"></td>
            <td><input type="text" class="form-control1" name="item_name[]" value="${escapeHtmlSo(name)}"></td>
            <td style="text-align:center;">
                <input type="text" class="form-control1 item_quantity" name="item_quantity[]"
                       value="${parseFloat(qty || 0).toFixed(2)}" style="text-align:center;">
            </td>
            <td style="text-align:center;"><button type="button" class="btn btn-danger delete-btn">ลบ</button></td>
        `;
        tableBody.appendChild(newRow);
        newRow.querySelector('input[name="item_quantity[]"]').addEventListener('blur', function () {
            if (this.value !== '') this.value = parseFloat(this.value).toFixed(2);
        });
        renumberRows();
    }

    async function fetchSODetail() {
        const soNum = document.getElementById("so_num").value.trim();
        const statusEl = document.getElementById("so_search_status");

        if (!soNum) { alert("กรุณากรอกเลข SO"); return; }

        statusEl.style.color = "var(--text-muted)";
        statusEl.textContent = "กำลังค้นหา...";

        try {
            const response = await fetch(`http://server_update:8000/api/getSODetail?SONum=${encodeURIComponent(soNum)}`);
            if (!response.ok) throw new Error("HTTP " + response.status);

            const data = await response.json();
            const soDetail = data?.SoDetail || {};

            const docuNo = soDetail.DocuNo || soNum;
            document.getElementById("so_id").value = docuNo;

            if (soDetail.ContactName && !document.getElementById("contact_name").value) {
                document.getElementById("contact_name").value = soDetail.ContactName;
            }

            const custCode = (soDetail.CustCode || soDetail.CustID || '').trim();
            if (custCode) {
                await autoSelectCustomerByCode(custCode, soDetail.CustName || '');
            }

            let items = [];
            if (Array.isArray(data.SOLists)) {
                data.SOLists.forEach(so => {
                    (so.ms_sodt || []).forEach(it => {
                        items.push({ name: it.GoodName || '', qty: it.GoodQty2 || '0' });
                    });
                });
            }

            if (items.length === 0) {
                statusEl.style.color = "var(--red)";
                statusEl.textContent = "ไม่พบข้อมูลสินค้าของเลข SO นี้";
                return;
            }

            document.getElementById("detail").innerHTML = "";
            items.forEach(item => addItemRow(item.name, item.qty));

            statusEl.style.color = "var(--primary)";
            statusEl.textContent = `พบสินค้า ${items.length} รายการ (SO: ${docuNo})`;

        } catch (err) {
            console.error(err);
            statusEl.style.color = "var(--red)";
            statusEl.textContent = "ดึงข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
        }
    }

    async function autoSelectCustomerByCode(custCode, custNameFallback) {
        try {
            const searchTerm = custNameFallback && custNameFallback.length >= 3 ? custNameFallback : custCode;
            const response = await fetch(`http://server_update:8000/api/getCustAndVendor?keySearch=${encodeURIComponent(searchTerm)}`);
            if (!response.ok) throw new Error("HTTP " + response.status);

            const data = await response.json();
            const results = [...(data.Customer || []), ...(data.Supplier || [])];

            const matched = results.find(c => (c.CustCode || c.VendorCode || '').trim() === custCode);

            if (matched) {
                const code = (matched.CustCode || matched.VendorCode || '').trim();
                const name = (matched.CustName || matched.VendorName || '').trim();
                const addr = [
                    matched.ContAddr1,
                    matched.ContAddr2,
                    matched.ContDistrict,
                    matched.ContAmphur,
                    matched.ContProvince,
                    matched.ContPostCode
                ].filter(part => part && part.trim() !== "").join(" ").trim();

                document.getElementById("id_com").value = code;
                document.getElementById("com_name").value = name;
                document.getElementById("com_address").value = addr;
                document.getElementById("autocomplete_list").style.display = "none";
                fetchlalong();
            } else {
                document.getElementById("com_name").value = custNameFallback || '';
            }
        } catch (err) {
            console.error('autoSelectCustomerByCode error:', err);
            document.getElementById("com_name").value = custNameFallback || '';
        }
    }

    function toggleOtherInput() {
        var doctype = document.getElementById("doctype").value;
        var otherInput = document.getElementById("other_input");

        if (doctype === "อื่นๆ") {
            otherInput.style.display = "block";
        } else {
            otherInput.style.display = "none";
        }
    }

    function updateOtherOption() {
        var otherInput = document.getElementById("other_input").value;
        var otherOption = document.getElementById("other_option");
        otherOption.value = otherInput;
        otherOption.text = otherInput || "อื่นๆ";
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('datestamp').value = new Date().toISOString().split('T')[0];
        refreshSubmitState();
    });
</script>

<script>
    let allCompanies = [];
    const searchCache = new Map();
    let currentAbortController = null;
    let debounceTimer = null;

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function showLoadingInList() {
        const listEl = document.getElementById("autocomplete_list");
        listEl.innerHTML = '<li class="ac-loading">กำลังค้นหา...</li>';
        listEl.style.display = "block";
    }

    async function performSearch(keyword) {
        if (!keyword) return;

        if (searchCache.has(keyword)) {
            renderResults(searchCache.get(keyword));
            return;
        }

        if (currentAbortController) currentAbortController.abort();
        currentAbortController = new AbortController();

        showLoadingInList();

        try {
            const response = await fetch(
                `http://server_update:8000/api/getCustAndVendor?keySearch=${encodeURIComponent(keyword)}`,
                { signal: currentAbortController.signal }
            );
            if (!response.ok) throw new Error("เกิดข้อผิดพลาดในการโหลดข้อมูล");

            const data = await response.json();
            const results = [...(data.Customer || []), ...(data.Supplier || [])];

            searchCache.set(keyword, results);
            renderResults(results);

        } catch (err) {
            if (err.name === "AbortError") return;
            console.error(err);
            document.getElementById("autocomplete_list").style.display = "none";
            alert("เกิดข้อผิดพลาดในการดึงข้อมูล");
        }
    }

    function renderResults(companies) {
        allCompanies = companies;
        const noDataMessage = document.getElementById("no_data_message");

        if (companies.length === 0) {
            noDataMessage.style.display = "block";
            document.getElementById("autocomplete_list").style.display = "none";
        } else {
            noDataMessage.style.display = "none";
            showAutocompleteResults(companies);
        }
    }

    function showAutocompleteResults(companies) {
        const listEl = document.getElementById("autocomplete_list");
        const fragment = document.createDocumentFragment();

        companies.forEach(company => {
            const isVendor = !!company.VendorCode;
            const code = (company.CustCode || company.VendorCode || "").trim();
            const name = (company.CustName || company.VendorName || "").trim();
            const addr = [
                company.ContAddr1,
                company.ContAddr2,
                company.ContDistrict,
                company.ContAmphur,
                company.ContProvince,
                company.ContPostCode
            ]
            .filter(part => part && part.trim() !== "")
            .join(" ")
            .trim();

            const item = document.createElement("li");
            item.innerHTML = `
                <div class="ac-row">
                    <span class="ac-name">
                        <span class="ac-type ${isVendor ? 'ac-type-vendor' : 'ac-type-cust'}">${escapeHtml(code)}</span>${escapeHtml(name)}
                    </span>
                </div>
                ${addr ? `<div class="ac-addr">${escapeHtml(addr)}</div>` : ""}
            `;
            item.addEventListener("click", () => {
                document.getElementById("id_com").value = code;
                document.getElementById("com_name").value = name;
                document.getElementById("com_address").value = addr;
                listEl.style.display = "none";
                fetchlalong();
            });
            fragment.appendChild(item);
        });

        listEl.innerHTML = "";
        listEl.appendChild(fragment);
        listEl.style.display = "block";
    }

    document.getElementById('com_name').addEventListener('input', function () {
        const inputText = this.value.trim();
        const noDataMessage = document.getElementById('no_data_message');

        clearTimeout(debounceTimer);

        if (inputText.length >= 3) {
            debounceTimer = setTimeout(() => performSearch(inputText), 350);
        } else {
            noDataMessage.style.display = 'none';
            document.getElementById("autocomplete_list").style.display = "none";
        }
    });

    document.getElementById('com_name').addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            clearTimeout(debounceTimer);
            const keyword = this.value.trim();
            if (!keyword) {
                alert("กรุณากรอกชื่อบริษัท");
                return;
            }
            performSearch(keyword);
        }
    });

    document.addEventListener("click", function (e) {
        const list = document.getElementById("autocomplete_list");
        if (!document.getElementById("com_name").contains(e.target) && !list.contains(e.target)) {
            list.style.display = "none";
        }
    });
</script>

<script>
    function updateMap() {
        const coords = document.getElementById('com_la_long').value.trim();
        const frame = document.getElementById('mapFrame');
        const empty = document.getElementById('mapEmpty');
        const isValid = /^-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?$/.test(coords);
        if (isValid) {
            frame.src = `https://www.google.com/maps?q=${encodeURIComponent(coords)}&output=embed`;
            if (empty) empty.style.display = 'none';
        } else {
            frame.src = '';
            if (empty) empty.style.display = 'flex';
        }
    }

    document.getElementById('com_la_long').addEventListener('input', updateMap);
    updateMap();

    let mapWindow;
    function openGoogleMaps() {
        const screenWidth = window.screen.width;
        const screenHeight = window.screen.height;
        const windowWidth = 800;
        const windowHeight = 600;
        const leftPosition = screenWidth - windowWidth;
        const topPosition = (screenHeight - windowHeight) / 2;
        mapWindow = window.open(
            "https://www.google.com/maps/@13.7563,100.5018,14z",
            "Google Maps",
            `width=${windowWidth},height=${windowHeight},left=${leftPosition},top=${topPosition}`
        );
    }
</script>

<script>
    const tableBody = document.querySelector('table tbody');
    if (tableBody) {
        tableBody.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-btn')) {
                var row = e.target.closest('tr');
                row.remove();
                renumberRows();
            }
        });
    }

    const insertBtn = document.querySelector('.insert-btn');
    if (insertBtn) {
        insertBtn.addEventListener('click', function() {
            var newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td class="row-num" style="text-align:center;"></td>
                <td><input type="text" class="form-control1" name="item_name[]"></td>
                <td style="text-align: center;">
                    <input type="text" class="form-control1 item_quantity" name="item_quantity[]" step="0.01" style="text-align: center;">
                </td>
                <td style="text-align:center;"><button type="button" class="btn btn-danger delete-btn">ลบ</button></td>
            `;
            tableBody.appendChild(newRow);
            const quantityInput = newRow.querySelector('input[name="item_quantity[]"]');
            quantityInput.addEventListener('blur', function () {
                if (this.value !== '') {
                    this.value = parseFloat(this.value).toFixed(2);
                }
            });
            renumberRows();
        });
    }

    const clearAllItemsBtn = document.getElementById('clearAllItemsBtn');
    if (clearAllItemsBtn) {
        clearAllItemsBtn.addEventListener('click', function () {
            const rows = document.querySelectorAll('#detail tr');
            if (rows.length === 0) return;
            if (confirm('ต้องการลบรายการสินค้าทั้งหมดใช่หรือไม่?')) {
                document.getElementById('detail').innerHTML = '';
            }
        });
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const qtyInputs = document.querySelectorAll('.item_quantity');

    qtyInputs.forEach(input => {
        input.addEventListener('blur', function () {
            if (this.value !== '') {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });

        if (input.value !== '') {
            input.value = parseFloat(input.value).toFixed(2);
        }
    });
});
</script>

<script>
    function fetchlalong() {
        var id_com = document.getElementById("id_com").value;

        if (!id_com) {
            document.getElementById("com_la_long").value = '';
            return;
        }

        apiFetch('/fetch-doclalong', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_com: id_com })
        })
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(data => {
            if (data.com_la_long) {
                document.getElementById("com_la_long").value = data.com_la_long;
                updateMap();
            } else {
                document.getElementById("com_la_long").value = '';
                document.getElementById("com_la_long").placeholder = 'ไม่มีข้อมูลพิกัด';
            }
        })
        .catch(error => {
            console.error('fetchlalong error:', error);
            document.getElementById("com_la_long").value = '';
            document.getElementById("com_la_long").placeholder = 'ดึงพิกัดไม่สำเร็จ ลองใหม่อีกครั้ง';
        });
    }
</script>

<script>
    document.getElementById('submitBill').addEventListener('click', async function (event) {
        event.preventDefault();

        const btn = this;
        const errors = validateForm();
        if (errors.length > 0) {
            showValidationSummary(errors);
            btn.animate([
                { transform: 'translateX(0)' },
                { transform: 'translateX(-6px)' },
                { transform: 'translateX(6px)' },
                { transform: 'translateX(-4px)' },
                { transform: 'translateX(4px)' },
                { transform: 'translateX(0)' },
            ], { duration: 350, easing: 'ease-in-out' });
            return;
        }

        const submitBtn = this;
        submitBtn.disabled = true;

        let formData = new FormData(document.getElementById('billForm'));

        // FIX: FormData(form) already auto-collects every input named
        // "item_name[]" / "item_quantity[]" that lives inside <form id="billForm">.
        // The code below used to ALSO append indexed copies (item_name[0], item_name[1], ...)
        // on top of that, so the backend received the same items twice under two
        // different key shapes ("item_name[]" as an array AND "item_name[0]" as
        // separate scalar keys). Depending on how the Laravel controller parses/validates
        // the request this duplication is very likely what triggered the HTTP 500.
        // Fix: strip the auto-collected "[]" keys first, then append clean indexed keys.
        formData.delete('item_name[]');
        formData.delete('item_quantity[]');

        let itemRows = document.querySelectorAll('#detail tr');
        let itemsForPdf = [];

        itemRows.forEach((row, index) => {
            let itemName = row.querySelector('input[name="item_name[]"]').value;
            let itemQuantity = row.querySelector('input[name="item_quantity[]"]').value;

            formData.append(`item_name[${index}]`, itemName);
            formData.append(`item_quantity[${index}]`, itemQuantity);

            if (itemName) {
                itemsForPdf.push({ item_name: itemName, quantity: itemQuantity });
            }
        });

        try {
            let response = await apiFetch('{{ route("insertdocu") }}', {
                method: 'POST',
                body: formData,
            });

            if (!response.ok) {
                // FIX: try to read the server's JSON error body (Laravel validation errors,
                // exception message, etc.) instead of only surfacing a bare "HTTP 500".
                // This makes future failures self-diagnosing in the browser console/alert.
                let serverMessage = '';
                try {
                    const errBody = await response.clone().json();
                    serverMessage = errBody.message || errBody.error ||
                        (errBody.errors ? Object.values(errBody.errors).flat().join(', ') : '');
                } catch (parseErr) {
                    try { serverMessage = await response.text(); } catch (_) {}
                }
                throw new Error(
                    'บันทึกข้อมูลไม่สำเร็จ (HTTP ' + response.status + ')' +
                    (serverMessage ? ' — ' + serverMessage : '')
                );
            }

            let data = await response.json();

            if (data.error) {
                alert(data.error);
                submitBtn.disabled = false;
                return;
            }

            const doc_id = data.doc_id;

            await generateAndUploadBillPdf(doc_id, itemsForPdf);

            alert(data.success);
            window.location.href = 'dashboarddoc';

        } catch (error) {
            console.error('Error:', error);
            if (error.message !== 'SESSION_EXPIRED') {
                alert('มีข้อผิดพลาดในการส่งข้อมูล: ' + error.message);
            }
            submitBtn.disabled = false;
        }
    });

    function formatDateTH(dateStr) {
        if (!dateStr) return '';
        const [y, m, d] = dateStr.split('-');
        return `${d}/${m}/${y}`;
    }

    function generateQrDataUrl(text, size = 150) {
        return new Promise((resolve, reject) => {
            const tempDiv = document.createElement('div');
            tempDiv.style.position = 'absolute';
            tempDiv.style.left = '-9999px';
            tempDiv.style.top = '-9999px';
            document.body.appendChild(tempDiv);

            try {
                new QRCode(tempDiv, {
                    text: text,
                    width: size,
                    height: size,
                    correctLevel: QRCode.CorrectLevel.M
                });

                setTimeout(() => {
                    const canvas = tempDiv.querySelector('canvas');
                    const dataUrl = canvas
                        ? canvas.toDataURL('image/png')
                        : (tempDiv.querySelector('img') ? tempDiv.querySelector('img').src : '');
                    tempDiv.remove();
                    resolve(dataUrl);
                }, 50);
            } catch (err) {
                tempDiv.remove();
                reject(err);
            }
        });
    }

async function generateAndUploadBillPdf(doc_id, items) {
        const { jsPDF } = window.jspdf;
        if (!jsPDF || !window.html2canvas) {
            console.error("ไม่พบ library jsPDF หรือ html2canvas");
            return;
        }

        const name = document.getElementById('com_name').value;
        const address = document.getElementById('com_address').value;
        const contact_name = document.getElementById('contact_name').value;
        const contact_tel = document.getElementById('contact_tel').value;
        const revdate = formatDateTH(document.getElementById('datestamp').value);
        const headcom = document.getElementById('headcom').value;
        const notes = document.getElementById('notes').value;

        let doctypeSelect = document.getElementById('doctype');
        let type = doctypeSelect.value === 'อื่นๆ'
            ? document.getElementById('other_input').value
            : doctypeSelect.value;

        let tableRowsHtml = '';
        if (items.length > 0) {
            items.forEach((item, index) => {
                tableRowsHtml += `
                    <tr>
                        <td style="border:1px solid #ccc; padding:8px; text-align:center; font-size:18px;">${index + 1}</td>
                        <td style="border:1px solid #ccc; padding:8px; text-align:left; font-size:18px;">${escapeHtmlSo(item.item_name)}</td>
                        <td style="border:1px solid #ccc; padding:8px; text-align:center; font-size:18px;">${escapeHtmlSo(item.quantity)}</td>
                    </tr>
                `;
            });
        } else {
            tableRowsHtml = `
                <tr>
                    <td colspan="3" style="border:1px solid #ccc; padding:12px; text-align:center; color:#888; font-size:18px;">
                        ไม่มีข้อมูลสินค้า
                    </td>
                </tr>
            `;
        }

        const coords = document.getElementById('com_la_long').value.trim();
        const hasCoords = coords !== '' && coords !== 'ไม่มีข้อมูล';
        const mapLink = hasCoords ? `https://www.google.com/maps?q=${encodeURIComponent(coords)}` : '';

        let qrDataUrl = '';
        if (hasCoords) {
            qrDataUrl = await generateQrDataUrl(mapLink, 260);
        }

        const qrBlockHtml = hasCoords ? `
            <div id="qrBlock" style="text-align:center; align-self:flex-start; margin-top:6px;">
                <img src="${qrDataUrl}" style="width:80px;height:80px;display:block;margin:0 auto;" />
            </div>
        ` : '';

        const CONTAINER_WIDTH_CSS = 1123;
        const PAGE_WIDTH_PT = 595.28;
        const PAGE_HEIGHT_PT = 841.89;

        // FIX: only select tbody rows here — the old selector "#billTable tr"
        // also matched the <thead> row, which made the pagination logic treat
        // the header row as if it were a normal content row. That's what caused
        // the duplicated header (page 1 started its "body" crop at y=0, which is
        // exactly where the thead row lives).
        async function captureContentWithRows(innerHtml, paddingCss) {
            const wrap = document.createElement('div');
            wrap.style.position = 'fixed';
            wrap.style.left = '-99999px';
            wrap.style.top = '0';
            wrap.style.width = CONTAINER_WIDTH_CSS + 'px';
            wrap.style.background = '#fff';
            wrap.style.fontFamily = "'Sarabun','Arial',sans-serif";
            wrap.style.lineHeight = '1.6';
            wrap.style.padding = paddingCss;
            wrap.innerHTML = innerHtml;
            document.body.appendChild(wrap);

            const imgs = wrap.querySelectorAll('img');
            await Promise.all(Array.from(imgs).map(img =>
                img.complete ? Promise.resolve() : new Promise(r => { img.onload = r; img.onerror = r; })
            ));

            const wrapRect = wrap.getBoundingClientRect();
            const rowEls = wrap.querySelectorAll('#billTable tbody tr'); // <-- FIX: tbody only
            const rowRectsCss = Array.from(rowEls).map(row => {
                const r = row.getBoundingClientRect();
                return { top: r.top - wrapRect.top, bottom: r.bottom - wrapRect.top };
            });
            const headEl = wrap.querySelector('#billTableHead');
            const headRectCss = headEl ? (() => {
                const r = headEl.getBoundingClientRect();
                return { top: r.top - wrapRect.top, bottom: r.bottom - wrapRect.top };
            })() : null;

            const cvs = await html2canvas(wrap, { scale: 2, backgroundColor: '#FFFFFF', useCORS: true });
            wrap.remove();

            const scale = cvs.width / CONTAINER_WIDTH_CSS;
            const rowRects = rowRectsCss.map(r => ({ top: r.top * scale, bottom: r.bottom * scale }));
            const headRect = headRectCss ? { top: headRectCss.top * scale, bottom: headRectCss.bottom * scale } : null;

            return { canvas: cvs, rowRects, headRect };
        }

        // ใช้ตอนแคปส่วนหัว: นอกจากได้ canvas แล้ว ยังคืนตำแหน่งพิกเซลของ element
        // ที่มี id ตรงกับ markerId (เช่น กล่อง QR code) เพื่อเอาไปวางข้อความ
        // "แผ่นที่ x/y" ใต้ QR ให้ตรงตำแหน่งจริงบนแต่ละหน้า
        async function captureHtmlChunkWithMarker(innerHtml, paddingCss, markerId) {
            const wrap = document.createElement('div');
            wrap.style.position = 'fixed';
            wrap.style.left = '-99999px';
            wrap.style.top = '0';
            wrap.style.width = CONTAINER_WIDTH_CSS + 'px';
            wrap.style.background = '#fff';
            wrap.style.fontFamily = "'Sarabun','Arial',sans-serif";
            wrap.style.lineHeight = '1.6';
            wrap.style.padding = paddingCss;
            wrap.innerHTML = innerHtml;
            document.body.appendChild(wrap);

            const imgs = wrap.querySelectorAll('img');
            await Promise.all(Array.from(imgs).map(img =>
                img.complete ? Promise.resolve() : new Promise(r => { img.onload = r; img.onerror = r; })
            ));

            const wrapRect = wrap.getBoundingClientRect();
            let markerRectCss = null;
            if (markerId) {
                const markerEl = wrap.querySelector('#' + markerId);
                if (markerEl) {
                    const r = markerEl.getBoundingClientRect();
                    markerRectCss = {
                        top: r.top - wrapRect.top,
                        left: r.left - wrapRect.left,
                        width: r.width,
                        height: r.height
                    };
                }
            }

            const cvs = await html2canvas(wrap, { scale: 2, backgroundColor: '#FFFFFF', useCORS: true });
            wrap.remove();

            let markerRect = null;
            if (markerRectCss) {
                const scale = cvs.width / CONTAINER_WIDTH_CSS;
                markerRect = {
                    top: markerRectCss.top * scale,
                    left: markerRectCss.left * scale,
                    width: markerRectCss.width * scale,
                    height: markerRectCss.height * scale
                };
            }

            return { canvas: cvs, markerRect };
        }

        async function captureHtmlChunk(innerHtml, paddingCss) {
            const wrap = document.createElement('div');
            wrap.style.position = 'fixed';
            wrap.style.left = '-99999px';
            wrap.style.top = '0';
            wrap.style.width = CONTAINER_WIDTH_CSS + 'px';
            wrap.style.background = '#fff';
            wrap.style.fontFamily = "'Sarabun','Arial',sans-serif";
            wrap.style.lineHeight = '1.6';
            wrap.style.padding = paddingCss;
            wrap.innerHTML = innerHtml;
            document.body.appendChild(wrap);

            const imgs = wrap.querySelectorAll('img');
            await Promise.all(Array.from(imgs).map(img =>
                img.complete ? Promise.resolve() : new Promise(r => { img.onload = r; img.onerror = r; })
            ));

            const cvs = await html2canvas(wrap, { scale: 2, backgroundColor: '#FFFFFF', useCORS: true });
            wrap.remove();
            return cvs;
        }

        // FIX: added startY param. Boundaries now start right after the head
        // row instead of at 0, so the first "page" of body content no longer
        // includes the head row's own pixels.
        function computeSafeBoundaries(startY, totalHeight, budgetPx, rowRects) {
            const boundaries = [startY];
            let cursor = startY;
            while (cursor < totalHeight) {
                let next = cursor + budgetPx;
                if (next >= totalHeight) {
                    boundaries.push(totalHeight);
                    break;
                }
                for (const r of rowRects) {
                    if (next > r.top + 1 && next < r.bottom - 1) {
                        next = r.top;
                        break;
                    }
                }
                if (next <= cursor + 10) {
                    next = cursor + budgetPx;
                }
                boundaries.push(next);
                cursor = next;
            }
            return boundaries;
        }

        const headerHtml = `
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; padding-bottom:10px; border-bottom:3px solid #1e293b;">
                <div>
                    <h1 style="margin:0; font-size:36px; font-weight:800; color:#1e293b; letter-spacing:.01em;">ใบส่งของชั่วคราว</h1>
                    <p style="margin:4px 0 0; font-size:16px; color:#64748b;">ประเภทบิล: <span style="font-weight:600;color:#334155;">${escapeHtmlSo(type)}</span></p>
                </div>
                <div style="border:1.5px solid #1e293b; border-radius:6px; padding:6px 14px; min-width:120px; text-align:center; background:#f8fafc;">
                    <p style="margin:0; font-size:9px; font-weight:700; letter-spacing:.05em; color:#64748b; text-transform:uppercase;">เลขที่บิล</p>
                    <p style="margin:2px 0 6px; font-size:14px; font-weight:800; color:#1e293b;">${escapeHtmlSo(doc_id)}</p>
                    <p style="margin:0; font-size:9px; font-weight:700; letter-spacing:.05em; color:#64748b; text-transform:uppercase;">วันที่</p>
                    <p style="margin:2px 0 0; font-size:12px; font-weight:600; color:#1e293b;">${escapeHtmlSo(revdate)}</p>
                </div>
            </div>

            <div style="background:#6b7280; border-radius:6px; padding:8px 14px; text-align:center; margin:0 0 10px;">
                <h2 style="margin:0; font-size:14px; font-weight:700; color:#fff; letter-spacing:.01em;">${escapeHtmlSo(headcom)}</h2>
            </div>

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:14px 18px; margin-bottom:0; display:flex; align-items:center; justify-content:space-between; gap:16px;">
                <div style="font-size:15px; line-height:1.9; color:#1e293b; flex:1; min-width:0;">
                    <p style="margin:0;"><span style="font-weight:700;color:#475569; display:inline-block; min-width:82px;">บริษัท :</span> ${escapeHtmlSo(name) || '-'}</p>
                    <p style="margin:0;"><span style="font-weight:700;color:#475569; display:inline-block; min-width:82px;">ที่อยู่ :</span> ${escapeHtmlSo(address) || '-'}</p>
                    <p style="margin:0;"><span style="font-weight:700;color:#475569; display:inline-block; min-width:82px;">ผู้ติดต่อ :</span> ${escapeHtmlSo(contact_name) || '-'} &nbsp;&nbsp;<span style="font-weight:700;color:#475569;">โทร :</span> ${escapeHtmlSo(contact_tel) || '-'}</p>
                    <p style="margin:0;"><span style="font-weight:700;color:#475569; display:inline-block; min-width:82px;">หมายเหตุ :</span> ${escapeHtmlSo(notes) || '-'}</p>
                </div>
                ${qrBlockHtml}
            </div>
        `;

        const tableHtml = `
            <table id="billTable" style="width:100%; border-collapse:collapse; font-size:13px; margin-top:14px; margin-bottom:20px;">
                <thead id="billTableHead">
                    <tr>
                        <th style="border:1px solid #6b7280; padding:8px; width:10%; background:#6b7280; color:#fff; font-weight:700;">ลำดับ</th>
                        <th style="border:1px solid #6b7280; padding:8px; width:60%; background:#6b7280; color:#fff; font-weight:700; text-align:left;">รายการ</th>
                        <th style="border:1px solid #6b7280; padding:8px; width:30%; background:#6b7280; color:#fff; font-weight:700;">จำนวน</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRowsHtml}
                </tbody>
            </table>
        `;

        const signatureHtml = `
            <div style="display:flex; justify-content:space-between; margin-top:36px; padding-top:8px;">
                <div style="width:42%; text-align:center;">
                    <div style="border-bottom:1px solid #1e293b; height:44px;"></div>
                    <p style="margin:8px 0 0; font-size:13px; font-weight:700; color:#334155;">ผู้รับสินค้า</p>
                    <p style="margin:2px 0 0; font-size:11px; color:#94a3b8;">วันที่ ....../....../..........</p>
                </div>
                <div style="width:42%; text-align:center;">
                    <div style="border-bottom:1px solid #1e293b; height:44px;"></div>
                    <p style="margin:8px 0 0; font-size:13px; font-weight:700; color:#334155;">ผู้ส่งสินค้า</p>
                    <p style="margin:2px 0 0; font-size:11px; color:#94a3b8;">วันที่ ....../....../..........</p>
                </div>
            </div>
        `;

        // Capture each section separately.
        const { canvas: headerCanvas, markerRect: qrBlockRect } = await captureHtmlChunkWithMarker(
            headerHtml, '30px 40px 12px 40px', hasCoords ? 'qrBlock' : null
        );
        const { canvas: tableCanvas, rowRects, headRect: tableHeadRect } = await captureContentWithRows(tableHtml, '0 40px 0 40px');
        const sigCanvas = await captureHtmlChunk(signatureHtml, '0 40px 0 40px');

        const pageCanvasWidth = headerCanvas.width;
        const pxPerPt = pageCanvasWidth / PAGE_WIDTH_PT;
        const pageHeightPx = Math.round(PAGE_HEIGHT_PT * pxPerPt);

        // FIX: zones are no longer fixed 30% / 60% / 10% regardless of content.
        // Header zone = actual rendered header height + a small breathing gap
        // (capped so an unusually long address block can't eat the whole page).
        // Signature zone = actual rendered signature block height + a small gap.
        // Items zone gets whatever height remains — this is what makes the
        // table move up right after the header instead of floating 30% down.
        const HEADER_GAP_PX = Math.round(14 * pxPerPt);
        const SIG_GAP_PX = Math.round(18 * pxPerPt);

        const headerZoneHeightPx = Math.min(
            headerCanvas.height + HEADER_GAP_PX,
            Math.round(pageHeightPx * 0.42)
        );
        const sigZoneHeightPx = Math.min(
            sigCanvas.height + SIG_GAP_PX,
            Math.round(pageHeightPx * 0.18)
        );
        const itemsZoneHeightPx = pageHeightPx - headerZoneHeightPx - sigZoneHeightPx;

        const headRowHeight = tableHeadRect ? Math.round(tableHeadRect.bottom - tableHeadRect.top) : 0;
        const itemsRowBudgetPx = Math.max(1, itemsZoneHeightPx - headRowHeight);

        // FIX: start boundaries right after the head row (tableHeadRect.bottom)
        // instead of 0, so the body crop for page 1 never includes the head
        // row's pixels again.
        const bodyStartY = tableHeadRect ? tableHeadRect.bottom : 0;
        const boundaries = computeSafeBoundaries(bodyStartY, tableCanvas.height, itemsRowBudgetPx, rowRects);
        const pageCount = boundaries.length - 1;

        const pages = [];
        for (let i = 0; i < pageCount; i++) {
            const sy = boundaries[i];
            const eY = boundaries[i + 1];
            pages.push({ sy, sh: eY - sy });
        }

        const pageWidth = PAGE_WIDTH_PT;
        const MAX_PAGE_SIZE = 1024 * 1024;
        const pdf = new jspdf.jsPDF("p", "pt", "a4");

        function compressPage(pageCanvas) {
            let quality = 0.95;
            let pageDataUrl;
            let approxBytes;
            do {
                pageDataUrl = pageCanvas.toDataURL('image/jpeg', quality);
                approxBytes = Math.ceil((pageDataUrl.length - 'data:image/jpeg;base64,'.length) * 3 / 4);
                if (approxBytes > MAX_PAGE_SIZE) quality -= 0.05;
            } while (approxBytes > MAX_PAGE_SIZE && quality > 0.3);
            return pageDataUrl;
        }

        pages.forEach((p, i) => {
            const isLastPage = i === pages.length - 1;

            const pageCanvas = document.createElement('canvas');
            pageCanvas.width = pageCanvasWidth;
            pageCanvas.height = pageHeightPx;
            const ctx = pageCanvas.getContext('2d');
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, pageCanvas.width, pageCanvas.height);

            // --- ส่วนหัว : ซ้ำทุกหน้า, สูงเท่าที่เนื้อหาจริงต้องการ ---
            const headerScale = Math.min(1, headerZoneHeightPx / headerCanvas.height);
            const headerDrawW = headerCanvas.width * headerScale;
            const headerDrawH = headerCanvas.height * headerScale;
            const headerOffsetX = (pageCanvasWidth - headerDrawW) / 2;
            ctx.drawImage(headerCanvas, 0, 0, headerCanvas.width, headerCanvas.height, headerOffsetX, 0, headerDrawW, headerDrawH);

            // --- เลขแผ่น : เขียนไว้ใต้ QR code ทุกหน้า ---
            if (hasCoords && qrBlockRect) {
                const qrCenterX = headerOffsetX + (qrBlockRect.left + qrBlockRect.width / 2) * headerScale;
                const qrBottomY = (qrBlockRect.top + qrBlockRect.height) * headerScale;
                const pageLabelGapPx = Math.round(6 * pxPerPt);
                const pageLabelFontPx = Math.round(8 * pxPerPt);
                ctx.font = `700 ${pageLabelFontPx}px 'Sarabun','Arial',sans-serif`;
                ctx.fillStyle = '#1e293b';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'top';
                ctx.fillText(`แผ่นที่ ${i + 1}/${pages.length}`, qrCenterX, qrBottomY + pageLabelGapPx);
            }

            // --- ส่วนรายการสินค้า : หัวตารางซ้ำทุกหน้า แถวแบ่งหน้าแบบไม่ตัดกลางแถว ---
            if (headRowHeight > 0) {
                ctx.drawImage(tableCanvas, 0, tableHeadRect.top, tableCanvas.width, headRowHeight, 0, headerZoneHeightPx, tableCanvas.width, headRowHeight);
            }
            ctx.drawImage(tableCanvas, 0, p.sy, tableCanvas.width, p.sh, 0, headerZoneHeightPx + headRowHeight, tableCanvas.width, p.sh);

            // --- ส่วนลายเซ็น : เฉพาะหน้าสุดท้าย ---
            if (isLastPage) {
                const sigZoneStartY = headerZoneHeightPx + itemsZoneHeightPx;
                const sigScale = Math.min(1, sigZoneHeightPx / sigCanvas.height);
                const sigDrawW = sigCanvas.width * sigScale;
                const sigDrawH = sigCanvas.height * sigScale;
                const sigOffsetX = (pageCanvasWidth - sigDrawW) / 2;
                const sigOffsetY = sigZoneStartY + Math.max(0, (sigZoneHeightPx - sigDrawH) / 2);
                ctx.drawImage(sigCanvas, 0, 0, sigCanvas.width, sigCanvas.height, sigOffsetX, sigOffsetY, sigDrawW, sigDrawH);
            }

            const pageDataUrl = compressPage(pageCanvas);
            const imgHeightPt = (pageCanvas.height / pageCanvas.width) * pageWidth;

            if (i > 0) pdf.addPage();
            pdf.addImage(pageDataUrl, 'JPEG', 0, 0, pageWidth, imgHeightPt);
        });

        const pdfBlob = pdf.output("blob");

        const downloadUrl = URL.createObjectURL(pdfBlob);
        const downloadLink = document.createElement('a');
        downloadLink.href = downloadUrl;
        downloadLink.download = `${doc_id}.pdf`;
        document.body.appendChild(downloadLink);
        downloadLink.click();
        downloadLink.remove();
        URL.revokeObjectURL(downloadUrl);

        const uploadForm = new FormData();
        uploadForm.append('doc_id', doc_id);
        uploadForm.append('pdf', pdfBlob, `${doc_id}.pdf`);

        try {
            const uploadResponse = await apiFetch('{{ route("savebillpdf") }}', {
                method: 'POST',
                body: uploadForm,
            });
            const uploadData = await uploadResponse.json();
            console.log('PDF saved:', uploadData);
        } catch (err) {
            console.error('อัปโหลด PDF ไม่สำเร็จ:', err);
        }
    }
</script>

<script>
    async function getFreshCsrfToken() {
        try {
            const res = await fetch(window.location.pathname, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                cache: 'no-store'
            });
            const html = await res.text();

            const match = html.match(/<meta name="csrf-token" content="([^"]+)">/);
            if (match && match[1]) {
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', match[1]);
                return match[1];
            }
            throw new Error('ไม่พบ csrf-token ใน response');
        } catch (e) {
            console.error('รีเฟรช CSRF token ล้มเหลว:', e);
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }
    }

    setInterval(getFreshCsrfToken, 5 * 60 * 1000);

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) getFreshCsrfToken();
    });

    async function apiFetch(url, options = {}) {
        options.headers = options.headers || {};
        const method = (options.method || 'GET').toUpperCase();

        if (method !== 'GET') {
            options.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        let res = await fetch(url, options);

        if (res.status === 419) {
            const fresh = await getFreshCsrfToken();
            options.headers['X-CSRF-TOKEN'] = fresh;
            res = await fetch(url, options);

            if (res.status === 419) {
                alert('เซสชันหมดอายุ กรุณารีเฟรชหน้าเว็บแล้วลองใหม่อีกครั้ง');
                throw new Error('SESSION_EXPIRED');
            }
        }

        return res;
    }
</script>

</body>
</html>