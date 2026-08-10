<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/insertdoc.blade.css') }}">
    <!-- เพิ่มใหม่: library สำหรับสร้าง PDF ฝั่ง client -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <title>สร้างใบชั่วคราว</title>

<style>
    *{box-sizing:border-box}
    :root{
        /* ═══ ธีมหลัก — ขาว-ดำ (Monochrome) ═══ */
        --primary:#111111;
        --primary-hover:#000000;
        --primary-light:#f2f2f2;
        --primary-mid:#4d4d4d;
        --primary-border:#cfcfcf;

        --red:#c0392b;
        --red-light:#fbeceb;
        --red-dark:#8f2a20;

        --bg:#f4f4f4;
        --surface:#ffffff;
        --border:#e2e2e2;
        --border-light:#eeeeee;
        --border-strong:#c9c9c9;

        --text:#111111;
        --text-secondary:#333333;
        --text-muted:#6f6f6f;
        --text-hint:#9a9a9a;

        --shadow-xs:0 1px 2px rgba(0,0,0,.06);
        --shadow-sm:0 2px 8px rgba(0,0,0,.08);
        --shadow-md:0 4px 16px rgba(0,0,0,.10);

        --r:6px;
        --rl:12px;

        --font-thai:'Sarabun','Segoe UI',system-ui,sans-serif;
        --font-mono:'JetBrains Mono',ui-monospace,monospace;

        --t-fast:.12s ease;
        --t-base:.2s ease;
    }
    body{
        font-family:var(--font-thai);
        background:var(--bg);
        color:var(--text);
        margin:0;padding:0;
        line-height:1.5;
        font-size:14px;
        -webkit-font-smoothing:antialiased;
    }

    /* ===== Container ===== */
    .container{
        max-width:1140px;
        margin:24px auto;
        background:var(--surface);
        border:1px solid var(--border);
        border-radius:var(--rl);
        box-shadow:0 1px 3px rgba(0,0,0,.05);
        overflow:hidden;
    }

    /* ===== Header bar ===== */
    .header-bar{
        padding:18px 28px;
        display:flex;justify-content:space-between;align-items:center;
        flex-wrap:wrap;gap:12px;
        border-bottom:1px solid var(--border);
        background:var(--surface);
    }
    .text-dark{
        font-size:22px;color:var(--text);margin:0;font-weight:700;
        letter-spacing:-.3px;
        display:flex;align-items:center;gap:10px;
    }
    .text-dark::before{
        font-size:22px;
    }
    .btn-back{
        background:var(--surface);color:var(--text-secondary);
        border:1px solid var(--border);
        padding:8px 18px;border-radius:var(--r);cursor:pointer;
        font-size:14px;font-weight:600;
        font-family:inherit;transition:all var(--t-fast);
        display:inline-flex;align-items:center;gap:6px;
    }
    .btn-back:hover{
        background:var(--bg);border-color:var(--border-strong);color:var(--text);
    }

    /* ===== Page ===== */
    .page{
        padding:24px 28px 32px;
        display:flex;flex-direction:column;gap:18px;
    }

    /* ===== Card ===== */
    .card{
        background:var(--surface);
        border:1px solid var(--border);
        border-radius:var(--rl);
        box-shadow:var(--shadow-xs);
        overflow:hidden;
        transition:box-shadow var(--t-base);
    }
    .card:hover{box-shadow:var(--shadow-sm)}
    .card-head{
        padding:18px 24px 15px;
        border-bottom:1px solid var(--border-light);
        display:flex;align-items:center;gap:12px;
        background:var(--surface);
    }
    .card-icon{
        width:38px;height:38px;border-radius:var(--r);
        display:flex;align-items:center;justify-content:center;flex-shrink:0;
        background:var(--primary-light);border:1px solid var(--primary-border);
    }
    .card-head-text h3{font-size:15px;font-weight:700;color:var(--text);margin:0}
    .card-head-text p {font-size:12px;color:var(--text-muted);margin:2px 0 0}

    .card-body{padding:22px 24px}

    /* ===== Section divider ===== */
    .section-divider{
        display:flex;align-items:center;gap:10px;
        padding:0;margin-bottom:14px;
    }
    .section-divider-line{flex:1;height:1px;background:var(--border-light)}
    .section-divider-label{
        font-size:11px;font-weight:700;color:var(--text-muted);
        letter-spacing:.08em;text-transform:uppercase;white-space:nowrap;
    }

    /* ===== Form grid ===== */
    .form-grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
    .form-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
    .span-2{grid-column:span 2}
    .span-full{grid-column:1/-1}

    /* ===== Field ===== */
    .field{display:flex;flex-direction:column;gap:6px;min-width:0;position:relative}
    .field label{
        font-size:12px;font-weight:700;color:var(--text-secondary);
        letter-spacing:.04em;
        display:flex;align-items:center;gap:5px;margin:0;
    }
    .field label .req{color:var(--red);font-size:14px;line-height:1}
    .field input[type="text"],
    .field input[type="number"],
    .field input[type="date"],
    .field input[type="file"],
    .field select,
    .field textarea{
        width:100%;
        padding:10px 12px;
        border:1.5px solid var(--border);
        border-radius:var(--r);
        background:var(--surface);
        color:var(--text);
        font-size:14px;font-family:inherit;
        outline:none;
        transition:border var(--t-fast),box-shadow var(--t-fast),background var(--t-fast);
    }
    .field input:focus,.field select:focus,.field textarea:focus{
        border-color:var(--primary);
        background:var(--surface);
        box-shadow:0 0 0 3px rgba(17,17,17,.10);
    }
    .field textarea{resize:vertical;min-height:60px;line-height:1.5;font-family:inherit}
    .field textarea::placeholder,.field input::placeholder{color:var(--text-hint)}
    .field select{
        background:var(--surface);cursor:pointer;
        appearance:none;-webkit-appearance:none;
        background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M3 4.5L6 7.5L9 4.5' stroke='%236f6f6f' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round' fill='none'/%3E%3C/svg%3E");
        background-repeat:no-repeat;background-position:right 12px center;
        padding-right:32px;
    }

    /* ===== Autocomplete ===== */
    .autocomplete-list{
        list-style:none;margin:0;padding:4px;
        position:absolute;top:100%;left:0;right:0;
        background:var(--surface);
        border:1.5px solid var(--border-strong);
        border-radius:var(--r);
        box-shadow:var(--shadow-md);
        max-height:220px;overflow-y:auto;
        z-index:40;
    }
    .autocomplete-list li{
        padding:8px 10px;font-size:13px;color:var(--text);
        border-radius:4px;cursor:pointer;
    }
    .autocomplete-list li:hover{background:var(--primary-light)}
    #no_data_message{font-size:12px;color:var(--red);margin-top:4px}

    /* ===== Autocomplete row (ชื่อ + รหัส + ที่อยู่) ===== */
    .ac-row{display:flex;justify-content:space-between;align-items:baseline;gap:8px}
    .ac-name{font-weight:700;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .ac-code{font-family:var(--font-mono);font-size:11px;color:var(--text-muted);white-space:nowrap;flex-shrink:0}
    .ac-type{font-size:10px;font-weight:700;padding:1px 6px;border-radius:3px;margin-right:6px;white-space:nowrap}
    .ac-type-cust{background:var(--primary-light);color:var(--primary)}
    .ac-type-vendor{background:var(--red-light);color:var(--red)}
    .ac-addr{font-size:12px;color:var(--text-muted);margin-top:2px}
    .ac-loading{font-size:12px;color:var(--text-muted);text-align:center;padding:10px;cursor:default}
    .ac-loading:hover{background:transparent !important}

    /* ===== Coords + Maps button ===== */
    .coords-row{display:flex;gap:8px;align-items:stretch}
    .coords-row input{flex:1;font-family:var(--font-mono) !important;font-size:13px !important}
    .btn-custom{
        display:inline-flex;align-items:center;gap:6px;
        padding:0 18px;
        border:1.5px solid var(--primary);background:var(--primary);
        color:#fff;font-size:13px;font-weight:700;
        border-radius:var(--r);cursor:pointer;font-family:inherit;
        transition:all var(--t-fast);white-space:nowrap;
    }
    .btn-custom:hover{background:var(--primary-hover);border-color:var(--primary-hover)}

    /* ===== Preview frame (map) ===== */
    .preview-frame{
        border:1px solid var(--border);
        border-radius:var(--r);
        overflow:hidden;
        background:var(--bg);
        height:280px;position:relative;
    }
    .preview-frame iframe{
        width:100%;height:100%;border:none;display:block;background:#fff;
    }

    /* ===== Table ===== */
    .tbl-wrap{
        overflow-x:auto;
        border:1px solid var(--border);border-radius:var(--r);
    }
    table.table{
        width:100%;border-collapse:collapse;font-size:14px;margin:0;
        table-layout:fixed;
    }
    table.table thead{background:var(--primary)}
    table.table th{
        padding:11px 16px;font-size:11px;font-weight:700;color:#fff;
        letter-spacing:.07em;text-transform:uppercase;text-align:left;
        border-right:1px solid rgba(255,255,255,.15);white-space:nowrap;
    }
    table.table th:last-child{border-right:none}
    table.table td{
        padding:9px 16px;
        border-bottom:1px solid var(--border-light);
        border-right:1px solid var(--border-light);
        vertical-align:middle;
    }
    table.table td:last-child{border-right:none}
    table.table tbody tr:last-child td{border-bottom:none}
    table.table tbody tr:hover td{background:var(--primary-light)}
    .form-control1{
        width:100%;padding:7px 10px;
        border:1px solid var(--border);border-radius:4px;
        font-size:13px;background:var(--surface);color:var(--text);
        box-sizing:border-box;font-family:var(--font-mono);
    }

    /* ===== Buttons: add / delete row ===== */
    .checkbox-container{
        display:flex;justify-content:flex-end;
        margin-top:12px;
    }
    .btn{
        padding:9px 18px;border-radius:var(--r);border:1.5px solid var(--primary);
        cursor:pointer;font-weight:700;font-size:13px;font-family:inherit;
        transition:all var(--t-fast);
    }
    .btn-success{
        background:var(--primary);color:#fff;
    }
    .btn-success:hover{background:var(--primary-hover)}
    .btn-danger{
        background:#fff;color:var(--red);border-color:var(--red);
        padding:6px 12px;font-size:12px;
    }
    .btn-danger:hover{background:var(--red-light)}

    /* ===== Submit ===== */
    .submit-row{
        display:flex;flex-direction:column;align-items:center;gap:10px;
        margin-top:8px;
    }
    .btn-submit-main{
        display:inline-flex;align-items:center;justify-content:center;gap:8px;
        background:var(--primary);
        color:#fff;border:none;
        padding:14px 32px;border-radius:var(--rl);
        font-size:15px;font-weight:700;
        cursor:pointer;min-width:220px;
        font-family:inherit;letter-spacing:.01em;
        transition:all var(--t-base);
        box-shadow:0 4px 12px rgba(0,0,0,.22);
    }
    .btn-submit-main:hover{
        background:var(--primary-hover);
        transform:translateY(-1px);
        box-shadow:0 6px 18px rgba(0,0,0,.28);
    }
    .btn-submit-main:active{transform:scale(.98);box-shadow:var(--shadow-sm)}
    .btn-submit-main:disabled{opacity:.6;cursor:not-allowed;transform:none}

    /* ===== Responsive ===== */
    @media (max-width:1180px){
        .container{margin:16px;max-width:none}
    }
    @media (max-width:992px){
        .form-grid-4{grid-template-columns:repeat(2,1fr)}
    }
    @media (max-width:640px){
        .container{margin:0;border-radius:0;border:none;box-shadow:none}
        .header-bar{padding:14px 16px}
        .text-dark{font-size:18px}
        .page{padding:16px 14px 32px}
        .card-body{padding:16px 18px}
        .card-head{padding:14px 18px 12px;flex-wrap:wrap}
        .form-grid-2,.form-grid-4{grid-template-columns:1fr}
        .span-2{grid-column:1}
        .btn-submit-main{width:100%;min-width:0}
    }
</style>

</head>
<body>

<div class="container">

<!-- ===== Header ===== -->
<div class="header-bar">
    <h2 class="text-dark">สร้างใบชั่วคราว</h2>
    <button onclick="history.back()" class="btn-back">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        ย้อนกลับ
    </button>
</div>

<form id="billForm">

<div class="page">

<!-- ====================== Card 1: ข้อมูลเอกสาร ====================== -->
    <div class="card">
        <div class="card-head" style="justify-content:space-between;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="card-icon">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <rect x="3" y="2" width="12" height="14" rx="2.5" stroke="#111111" stroke-width="1.5"/>
                        <path d="M6 6h6M6 9h6M6 12h4" stroke="#111111" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="card-head-text">
                    <h3>ข้อมูลเอกสาร</h3>
                    <p>บริษัทหัวเอกสาร ผู้เปิดบิล วันที่ และประเภทบิล</p>
                </div>
            </div>

            <div class="field" style="min-width:180px;margin:0;">
                <label for="datestamp">วันที่เอกสาร <span class="req">*</span></label>
                <input type="date" id="datestamp" name="datestamp" required>
            </div>

            <div class="field" style="min-width:240px;margin:0;">
                <label for="doctype">ประเภทบิล <span class="req">*</span></label>
                <select id="doctype" name="doctype" required onchange="toggleOtherInput()">
                    <option value="" disabled selected>-- กรุณาเลือกประเภทบิล --</option>
                    <option value="รับของ">รับของ</option>
                    <option value="ส่งของ">ส่งของ</option>
                    <option value="รับของและส่งของ">รับของและส่งของ</option>
                    <option value="ส่งของและรับกลับ">ส่งของและรับกลับ</option>
                    <option value="อื่นๆ" id="other_option">อื่น ๆ</option>
                </select>
                <input type="text" id="other_input" name="other_input" style="display:none;margin-top:8px;" placeholder="กรุณากรอกข้อมูล" oninput="updateOtherOption()">
            </div>
        </div>
        <div class="card-body">
            <div class="form-grid-2" style="margin-bottom:14px">
                <div class="field span-full">
                    <label for="headcom">ชื่อบริษัทหัวเอกสาร <span class="req">*</span></label>
                    <select id="headcom" name="headcom" required onchange="toggleSoBlock()">
                        <option value="" disabled selected>-- กรุณาเลือกชื่อบริษัท --</option>
                        <option value="บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด">บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด</option>
                        <option value="บริษัท ทริปเปิ้ล อี อินโนเวชั่น จำกัด">บริษัท ทริปเปิ้ล อี อินโนเวชั่น จำกัด</option>
                        <option value="บริษัท ทริบเปิ้ล พี แฟคตอรี่ แอนด์ เอ็นจิเนียริ่ง จำกัด">บริษัท ทริบเปิ้ล พี แฟคตอรี่ แอนด์ เอ็นจิเนียริ่ง จำกัด</option>
                        <option value="บริษัท เอตะ แอนด์ พอล อินโนเวชั่น จำกัด">บริษัท เอตะ แอนด์ พอล อินโนเวชั่น จำกัด</option>
                        <option value="บริษัท ฮิคาริ เดงกิ จำกัด">บริษัท ฮิคาริ เดงกิ จำกัด</option>
                        <option value="บริษัท เอ อี แอนด์ ที อินเตอร์เนชั่นแนล จำกัด">บริษัท เอ อี แอนด์ ที อินเตอร์เนชั่นแนล จำกัด</option>
                        <option value="บริษัท ทริปเปิ้ล อี ไลท์ติ้ง จำกัด">บริษัท ทริปเปิ้ล อี ไลท์ติ้ง จำกัด</option>
                        <option value="บริษัท ทริปเปิ้ล อี เอ็มไพร์ กรุ๊ป จำกัด">บริษัท ทริปเปิ้ล อี เอ็มไพร์ กรุ๊ป จำกัด</option>
                    </select>
                </div>
            </div>
            <input type="hidden" id="emp_name" name="emp_name" value="{{ session('emp_name', 'Guest') }}">
            <input type="hidden" id="id_com" name="id_com">
        </div>
    </div>
    <!-- ====================== Card 2: ข้อมูลบริษัท / ผู้ติดต่อ ====================== -->
    <div class="card">
        <div class="card-head">
            <div class="card-icon">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <circle cx="9" cy="6" r="3.2" stroke="#111111" stroke-width="1.5"/>
                    <path d="M2.5 15.5c0-3.59 2.91-6.5 6.5-6.5s6.5 2.91 6.5 6.5" stroke="#111111" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="card-head-text">
                <h3>ข้อมูลบริษัท / ผู้ติดต่อ</h3>
                <p>ค้นหาบริษัท ผู้ติดต่อ และเบอร์โทร</p>
            </div>
        </div>
<div class="card-body">
    <div class="form-grid-2" style="row-gap:16px">

        <!-- เลข SO: field แยกต่างหาก ปิด div ให้ครบ -->
        <div class="field span-full" id="so_block" style="display:none;">
            <label for="so_num">เลข SO <span class="req">*</span></label>
            <div class="coords-row">
                <input type="text" id="so_num" placeholder="เช่น 69/012494">
                <button type="button" class="btn-custom" onclick="fetchSODetail()">ค้นหา</button>
            </div>
            <div id="so_search_status" style="font-size:12px;color:var(--text-muted);margin-top:4px;"></div>
        </div>
        <input type="hidden" id="so_id" name="so_id">

        <!-- บริษัท: field ของตัวเอง ครบ open/close -->
        <div class="field span-full">
            <label for="com_name">บริษัท</label>
            <div style="position:relative;">
                <input type="text" id="com_name" name="com_name" autocomplete="off" placeholder="พิมพ์ชื่อบริษัทอย่างน้อย 3 ตัวอักษร">
                <ul id="autocomplete_list" class="autocomplete-list" style="display:none;"></ul>
                <div id="no_data_message" style="display:none;">ไม่มีข้อมูล</div>
            </div>
        </div>

        <div class="field">
            <label for="contact_name">ชื่อผู้ติดต่อ</label>
            <input type="text" id="contact_name" name="contact_name">
        </div>
        <div class="field">
            <label for="contact_tel">เบอร์ติดต่อ</label>
            <input type="text" id="contact_tel" name="contact_tel">
        </div>
    </div>
</div>
    <!-- ====================== Card 3: ที่อยู่จัดส่ง & แผนที่ ====================== -->
    <div class="card">
        <div class="card-head">
            <div class="card-icon">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M9 1.8c-2.76 0-5 2.24-5 5 0 3.75 5 9.4 5 9.4s5-5.65 5-9.4c0-2.76-2.24-5-5-5z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
                    <circle cx="9" cy="6.8" r="1.8" stroke="#111111" stroke-width="1.5"/>
                </svg>
            </div>
            <div class="card-head-text">
                <h3>ที่อยู่จัดส่ง &amp; แผนที่</h3>
                <p>ที่อยู่ พิกัด และรายละเอียดสำหรับคนขับ</p>
            </div>
        </div>
        <div class="card-body">
            <div class="form-grid-2" style="row-gap:16px">
                <div class="field span-full">
                    <label for="com_address">ที่อยู่จัดส่ง</label>
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
                <div class="section-divider">
                    <div class="section-divider-line"></div>
                    <span class="section-divider-label">พรีวิวแผนที่</span>
                    <div class="section-divider-line"></div>
                </div>
                <div class="field">
                    <div class="preview-frame">
                        <iframe id="mapFrame" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================== Card 4: รายการสินค้า ====================== -->
    <div class="card">
        <div class="card-head">
            <div class="card-icon">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <rect x="2" y="5" width="10" height="9" rx="1.5" stroke="#111111" stroke-width="1.5"/>
                    <path d="M12 7.5l4 2.5v4h-4" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="4.5" cy="15" r="1.3" stroke="#111111" stroke-width="1.3"/>
                    <circle cx="13.5" cy="15" r="1.3" stroke="#111111" stroke-width="1.3"/>
                </svg>
            </div>
            <div class="card-head-text">
                <h3>รายการสินค้า</h3>
                <p>เพิ่มรายการและจำนวนที่ต้องการ</p>
            </div>
        </div>
        <div style="padding:18px 24px 24px">
            <div class="tbl-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:60%">รายการ</th>
                            <th style="width:20%;text-align:center">จำนวน</th>
                            <th style="width:20%;text-align:center">ลบ</th>
                        </tr>
                    </thead>
                    <tbody id="detail"></tbody>
                </table>
            </div>
            <div class="checkbox-container">
                <button type="button" class="btn btn-success insert-btn">+ เพิ่มสินค้า</button>
            </div>
        </div>
    </div>

    <!-- ====================== Submit ====================== -->
    <div class="submit-row">
        <button type="button" id="submitBill" class="btn-submit-main">
            <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                <path d="M3.5 9l4 4 6-7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            บันทึก
        </button>
    </div>

</div>

</form>

</div>

<!-- แก้: เอา script auto-fill วันที่พรุ่งนี้ออก ให้ datestamp เป็นค่าว่าง/null ไปเลย -->
<script>
    const SO_ENABLED_COMPANY = "บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด";

function toggleSoBlock() {
    const headcom = document.getElementById("headcom").value;
    const soBlock = document.getElementById("so_block");
    if (headcom === SO_ENABLED_COMPANY) {
        soBlock.style.display = "block";
    } else {
        soBlock.style.display = "none";
        document.getElementById("so_num").value = "";
        document.getElementById("so_id").value = "";
        document.getElementById("so_search_status").textContent = "";
    }
}

function escapeHtmlSo(str) {
    return String(str)
        .replace(/&/g, "&amp;").replace(/</g, "&lt;")
        .replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

function addItemRow(name, qty) {
    const tableBody = document.getElementById("detail");
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
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

        // เลข SO ที่จะบันทึกลง db (ใช้ DocuNo จาก SoDetail ถ้ามี ไม่งั้น fallback เป็นค่าที่กรอก)
        const docuNo = soDetail.DocuNo || soNum;
        document.getElementById("so_id").value = docuNo;

        if (soDetail.ContactName && !document.getElementById("contact_name").value) {
            document.getElementById("contact_name").value = soDetail.ContactName;
        }

        // ===== ใหม่: เอารหัสลูกค้าจาก SO ไปค้นหาในระบบลูกค้า/ผู้ขาย แล้ว auto-select ให้ com_name =====
        const custCode = (soDetail.CustCode || soDetail.CustID || '').trim();
        if (custCode) {
            await autoSelectCustomerByCode(custCode, soDetail.CustName || '');
        }

        // รวมสินค้าจากทุก SOLists -> ms_sodt
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
// ===== ใหม่: ค้นหาลูกค้าด้วยรหัส แล้วเลือกอัตโนมัติเหมือนคลิกจาก autocomplete =====
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
    });
</script>

<!-- ====================== Autocomplete: ค้นหาบริษัท (ลูกค้า/ผู้ขาย) ======================
     ปรับปรุงประสิทธิภาพ:
     1. Debounce (350ms) — ยิง API เมื่อผู้ใช้หยุดพิมพ์ ไม่ใช่ทุกครั้งที่กดคีย์
     2. Cache ผลลัพธ์ต่อคำค้น — คำเดิมไม่ต้องยิง API ซ้ำ
     3. AbortController — ยกเลิก request เก่าที่ยังค้าง กันผลลัพธ์เก่ามาทับผลลัพธ์ใหม่ (race condition)
     4. แสดงรหัส (CustCode/VendorCode) และประเภท (ลูกค้า/ผู้ขาย) ในผลการค้นหา
-->
<script>
    let allCompanies = [];
    const searchCache = new Map();       // เก็บผลลัพธ์ต่อคำค้นหา (in-memory)
    let currentAbortController = null;   // ใช้ยกเลิก fetch ที่ยังไม่เสร็จ
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

        // มีแคชแล้ว ใช้เลย ไม่ต้องยิง API ซ้ำ
        if (searchCache.has(keyword)) {
            renderResults(searchCache.get(keyword));
            return;
        }

        // ยกเลิก request ก่อนหน้าที่ยังค้างอยู่
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
            if (err.name === "AbortError") return; // ถูกยกเลิกเพราะมีคำค้นใหม่เข้ามา ไม่ใช่ error จริง
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
            // ดีเลย์ 350ms หลังหยุดพิมพ์ ก่อนยิง API — ลดจำนวน request ระหว่างพิมพ์เร็วๆ
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

    // ปิด dropdown ถ้าคลิกนอก
    document.addEventListener("click", function (e) {
        const list = document.getElementById("autocomplete_list");
        if (!document.getElementById("com_name").contains(e.target) && !list.contains(e.target)) {
            list.style.display = "none";
        }
    });
</script>

<script>
    function updateMap() {
        let coords = document.getElementById('com_la_long').value;
        if (coords) {
            document.getElementById('mapFrame').src = `https://www.google.com/maps?q=${coords}&output=embed`;
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
            }
        });
    }

    const insertBtn = document.querySelector('.insert-btn');
    if (insertBtn) {
        insertBtn.addEventListener('click', function() {
            var newRow = document.createElement('tr');
            newRow.innerHTML = `
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
        });
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const qtyInputs = document.querySelectorAll('.item_quantity');

    qtyInputs.forEach(input => {
        // เมื่อออกจากช่อง input ให้บังคับแสดงทศนิยม 2 ตำแหน่ง
        input.addEventListener('blur', function () {
            if (this.value !== '') {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });

        // เมื่อโหลดหน้า ถ้ามีค่าก็จัดรูปแบบ
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
<!-- ====================== แก้ใหม่ทั้งหมด: submitBill ======================
     ขั้นตอน: 1) บันทึกข้อมูลบิล (insertdocu) -> ได้ doc_id กลับมา
              2) สร้าง PDF จากค่าฟอร์มปัจจุบัน (โครงสร้างเดียวกับ admindoc downloadRowPDF)
              3) อัปโหลด PDF ไปเก็บที่ storage/app/public/temporary_bill/{doc_id}.pdf
-->
<script>
    document.getElementById('submitBill').addEventListener('click', async function (event) {
        event.preventDefault();

        const submitBtn = this;
        submitBtn.disabled = true;

        let formData = new FormData(document.getElementById('billForm'));
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
                throw new Error('บันทึกข้อมูลไม่สำเร็จ (HTTP ' + response.status + ')');
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
            qrDataUrl = await generateQrDataUrl(mapLink, 150);
        }

        const qrBlockHtml = hasCoords ? `
            <div style="text-align:center;">
                <img src="${qrDataUrl}" style="width:110px;height:110px;" />
            </div>
        ` : '';

        const CONTAINER_WIDTH_CSS = 1123;
        const PAGE_WIDTH_PT = 595.28;
        const PAGE_HEIGHT_PT = 841.89;

        // แก้: capture เนื้อหา พร้อม "วัดตำแหน่งแถวตาราง" ไปด้วย เพื่อเอาไว้กันตัดหน้าคร่อมกลางแถว
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

            // วัดตำแหน่ง top/bottom ของทุกแถว (thead + tbody) เทียบกับขอบบนของ wrap ก่อน capture
            const wrapRect = wrap.getBoundingClientRect();
            const rowEls = wrap.querySelectorAll('#billTable tr');
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

        // แก้: หาจุดตัดหน้าที่ "ปลอดภัย" — ถ้าจุดตัดเดิมจะคร่อมกลางแถวใด ให้เลื่อนขึ้นไปขอบบนของแถวนั้นแทน (ดันทั้งแถวไปหน้าถัดไป)
        function computeSafeBoundaries(totalHeight, budgetPx, rowRects) {
            const boundaries = [0];
            let cursor = 0;
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
                    // กรณีสุดโต่ง: แถวเดียวสูงเกิน 1 หน้า ตัดเลี่ยงไม่ได้จริงๆ ปล่อยตามเดิม
                    next = cursor + budgetPx;
                }
                boundaries.push(next);
                cursor = next;
            }
            return boundaries;
        }

        const contentHtml = `
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <div>
                    <h1 style="margin:0; font-size:46px; font-weight:800; color:#111;">ใบส่งของชั่วคราว</h1>
                    <p style="margin:6px 0 0; font-size:22px; color:#555;">ประเภทบิล: ${escapeHtmlSo(type)}</p>
                </div>
                <div style="display:flex; align-items:center; justify-content:flex-end; gap:14px;">
                    ${qrBlockHtml}
                    <div style="border:1.5px solid #111; padding:10px 20px; min-width:170px; text-align:center;">
                        <p style="margin:0; font-size:15px; font-weight:700; letter-spacing:.05em; color:#555;">เลขที่บิล</p>
                        <p style="margin:4px 0 0; font-size:22px; font-weight:700;">${escapeHtmlSo(doc_id)}</p>
                        <p style="margin:10px 0 0; font-size:15px; font-weight:700; letter-spacing:.05em; color:#555;">วันที่</p>
                        <p style="margin:2px 0 0; font-size:18px;">${escapeHtmlSo(revdate)}</p>
                    </div>
                </div>
            </div>

            <div style="border:1px solid #111; padding:10px 16px; text-align:center; margin:14px 0 24px;">
                <h2 style="margin:0; font-size:26px; font-weight:700;">${escapeHtmlSo(headcom)}</h2>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0 40px;">
                <div style="grid-column:1 / -1; margin-bottom:14px;">
                    <p style="margin:0 0 3px; font-size:16px; font-weight:700; color:#555;">บริษัท</p>
                    <p style="margin:0; font-size:22px; border-bottom:2px dotted #999; padding-bottom:6px;">${escapeHtmlSo(name) || '&nbsp;'}</p>
                </div>
                <div style="grid-column:1 / -1; margin-bottom:14px;">
                    <p style="margin:0 0 3px; font-size:16px; font-weight:700; color:#555;">ที่อยู่</p>
                    <p style="margin:0; font-size:22px; border-bottom:2px dotted #999; padding-bottom:6px;">${escapeHtmlSo(address) || '&nbsp;'}</p>
                </div>
                <div style="margin-bottom:14px;">
                    <p style="margin:0 0 3px; font-size:16px; font-weight:700; color:#555;">ชื่อผู้ติดต่อ</p>
                    <p style="margin:0; font-size:22px; border-bottom:2px dotted #999; padding-bottom:6px;">${escapeHtmlSo(contact_name) || '&nbsp;'}</p>
                </div>
                <div style="margin-bottom:14px;">
                    <p style="margin:0 0 3px; font-size:16px; font-weight:700; color:#555;">โทร</p>
                    <p style="margin:0; font-size:22px; border-bottom:2px dotted #999; padding-bottom:6px;">${escapeHtmlSo(contact_tel) || '&nbsp;'}</p>
                </div>
                <div style="grid-column:1 / -1; margin-bottom:20px;">
                    <p style="margin:0 0 3px; font-size:16px; font-weight:700; color:#555;">หมายเหตุ</p>
                    <p style="margin:0; font-size:22px; border-bottom:2px dotted #999; padding-bottom:6px;">${escapeHtmlSo(notes) || '&nbsp;'}</p>
                </div>
            </div>

            <table id="billTable" style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                <thead id="billTableHead">
                    <tr>
                        <th style="border:1px solid #111; padding:10px 8px; width:10%; background:#e0e0e0; color:#0a0a0a; font-size:20px;">ลำดับ</th>
                        <th style="border:1px solid #111; padding:10px 8px; width:60%; background:#e0e0e0; color:#0a0a0a; font-size:20px; text-align:left;">รายการ</th>
                        <th style="border:1px solid #111; padding:10px 8px; width:30%; background:#e0e0e0; color:#0a0a0a; font-size:20px;">จำนวน</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRowsHtml}
                </tbody>
            </table>
        `;

        const signatureHtml = `
            <div style="display:flex; justify-content:space-between;">
                <div style="width:45%;">
                    <p style="margin:0 0 30px; font-size:20px; font-weight:700;">ชื่อผู้รับ:</p>
                    <p style="margin:0; border-top:1px solid #111;"></p>
                </div>
                <div style="width:45%;">
                    <p style="margin:0 0 30px; font-size:20px; font-weight:700;">ชื่อผู้ส่ง:</p>
                    <p style="margin:0; border-top:1px solid #111;"></p>
                </div>
            </div>
        `;

        const { canvas: contentCanvas, rowRects, headRect } = await captureContentWithRows(contentHtml, '30px 40px 0 40px');
        const sigCanvas = await captureHtmlChunk(signatureHtml, '0 40px 30px 40px');

        const pxPerPt = contentCanvas.width / PAGE_WIDTH_PT;
        const pageHeightPx = Math.round(PAGE_HEIGHT_PT * pxPerPt);
        const headRowHeight = headRect ? Math.round(headRect.bottom - headRect.top) : 0;

        // แก้: กันงบประมาณความสูงไว้เผื่อหัวตารางซ้ำ จะได้ไม่ล้นหน้าตอนแปะหัวตารางซ้ำ
        const effectivePageHeightPx = pageHeightPx - headRowHeight;
        const boundaries = computeSafeBoundaries(contentCanvas.height, effectivePageHeightPx, rowRects);
        const contentPageCount = boundaries.length - 1;

        // สร้าง metadata ของแต่ละหน้า (sy, sh, ต้องแปะหัวตารางซ้ำไหม)
        const pages = [];
        for (let i = 0; i < contentPageCount; i++) {
            const sy = boundaries[i];
            const eY = boundaries[i + 1];
            const sh = eY - sy;
            const isCont = i > 0 && headRect && sy >= headRect.bottom - 1 &&
                rowRects.length > 0 && sy < rowRects[rowRects.length - 1].bottom;
            pages.push({ sy, sh, headH: isCont ? headRowHeight : 0 });
        }

        const lastPage = pages[pages.length - 1];
        const lastPageUsedHeight = lastPage.sh + lastPage.headH;
        const SAFETY_MARGIN_PX = 20 * 2;
        const sigFitsOnLastPage = (pageHeightPx - lastPageUsedHeight) >= (sigCanvas.height + SAFETY_MARGIN_PX);

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
            const appendSig = isLastPage && sigFitsOnLastPage;

            // แก้: ถ้าเป็นหน้าสุดท้ายและมีลายเซ็น ให้ใช้ความสูง "เต็มหน้ากระดาษ" เสมอ
            //      เพื่อดันลายเซ็นไปชิดขอบล่างสุดจริงๆ ไม่ใช่ต่อท้ายเนื้อหาเฉยๆ
            const pageCanvasHeight = appendSig
                ? Math.max(pageHeightPx, p.headH + p.sh + sigCanvas.height + 40) // เผื่อ margin เล็กน้อยด้านล่าง
                : (p.headH + p.sh);

            const pageCanvas = document.createElement('canvas');
            pageCanvas.width = contentCanvas.width;
            pageCanvas.height = pageCanvasHeight;
            const ctx = pageCanvas.getContext('2d');
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, pageCanvas.width, pageCanvas.height);

            let cursorY = 0;
            if (p.headH > 0) {
                ctx.drawImage(contentCanvas, 0, headRect.top, contentCanvas.width, p.headH, 0, cursorY, contentCanvas.width, p.headH);
                cursorY += p.headH;
            }
            ctx.drawImage(contentCanvas, 0, p.sy, contentCanvas.width, p.sh, 0, cursorY, contentCanvas.width, p.sh);

            if (appendSig) {
                // แก้: วางลายเซ็นชิดขอบล่างสุดของหน้าเสมอ (ไม่ใช่ต่อจาก cursorY)
                const BOTTOM_MARGIN_PX = 20; // เผื่อขอบล่างเล็กน้อย
                const sigY = pageCanvasHeight - sigCanvas.height - BOTTOM_MARGIN_PX;
                ctx.drawImage(sigCanvas, 0, sigY);
            }

            const pageDataUrl = compressPage(pageCanvas);
            const imgHeightPt = (pageCanvas.height / pageCanvas.width) * pageWidth;

            if (i > 0) pdf.addPage();
            pdf.addImage(pageDataUrl, 'JPEG', 0, 0, pageWidth, imgHeightPt);
        });

        if (!sigFitsOnLastPage) {
            // แก้: หน้าลายเซ็นเดี่ยว ก็ต้องชิดขอบล่างสุดของหน้าเช่นกัน ไม่ใช่ชิดบน
            const sigPageCanvas = document.createElement('canvas');
            sigPageCanvas.width = contentCanvas.width;
            sigPageCanvas.height = pageHeightPx;
            const ctx = sigPageCanvas.getContext('2d');
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, sigPageCanvas.width, sigPageCanvas.height);

            const BOTTOM_MARGIN_PX = 20;
            const sigY = pageHeightPx - sigCanvas.height - BOTTOM_MARGIN_PX;
            ctx.drawImage(sigCanvas, 0, sigY);

            const pageDataUrl = compressPage(sigPageCanvas);
            const imgHeightPt = (sigPageCanvas.height / sigPageCanvas.width) * pageWidth;

            pdf.addPage();
            pdf.addImage(pageDataUrl, 'JPEG', 0, 0, pageWidth, imgHeightPt);
        }

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