<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/insertdata.blade.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <title>สร้างเส้นทางส่งสินค้า</title>

<style>
*{box-sizing:border-box}
:root{
    --primary:#3f865d; --primary-hover:#326f4d; --primary-light:#e8f2ec;
    --primary-mid:#4a9d6c; --primary-border:#c4dfd0;
    --blue:#1e6fd9; --blue-hover:#1857ad;
    --red:#9b1b1b; --red-light:#fee2e2;
    --bg:#fff; --surface:#fff;
    --border:#d0d7de; --border-light:#e8eef1;
    --text:#0f172a; --text-secondary:#475569;
    --text-muted:#94a3b8; --text-hint:#cbd5e1;
    --shadow-xs:0 1px 2px rgba(63,134,93,.06);
    --shadow-sm:0 2px 8px rgba(63,134,93,.08);
    --shadow-md:0 4px 16px rgba(63,134,93,.10);
    --t-fast:.12s ease; --t-base:.2s ease;
}
body{
    font-family:'Sarabun','Segoe UI',system-ui,sans-serif;
    background:rgb(233, 233, 233);
    color:var(--text);
    margin:0;padding:0;
    line-height:1.5;
    font-size:14px;
}

/* ===== Container ===== */
.container{
    max-width:1140px;
    margin:24px auto;
    background:#fff;
    border:1px solid var(--border-light);
    border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,.04);
    overflow:hidden;
}

/* ===== Header bar ===== */
.header-bar{
    padding:18px 28px;
    display:flex;justify-content:space-between;align-items:center;
    flex-wrap:wrap;gap:12px;
    border-bottom:2px solid var(--border-light);
    background:#fff;
}
.text-dark{font-size:24px;color:var(--text);margin:0;font-weight:700;letter-spacing:-.3px}
.btn-back{
    background:var(--primary);color:#fff;border:none;
    padding:8px 18px;border-radius:6px;cursor:pointer;
    font-size:14px;font-weight:500;
    font-family:inherit;transition:background .2s;
}
.btn-back:hover{background:var(--primary-hover)}

/* ===== Page ===== */
.page{
    padding:24px 28px 32px;
    display:flex;flex-direction:column;gap:18px;
}

/* ===== Card ===== */
.card{
    background:var(--surface);
    border:1px solid var(--border);
    border-radius:8px;
    box-shadow:var(--shadow-xs);
    overflow:hidden;
    transition:box-shadow var(--t-base);
}
.card:hover{box-shadow:var(--shadow-sm)}
.card-head{
    padding:18px 24px 15px;
    border-bottom:1px solid var(--border-light);
    display:flex;align-items:center;gap:12px;
    background:#fff;
}
.card-icon{
    width:38px;height:38px;border-radius:8px;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.ci-green{background:#fff;border:1px solid var(--border)}
.ci-blue {background:#fff;border:1px solid var(--border)}
.ci-amber{background:#fff;border:1px solid var(--border)}
.card-head-text h3{font-size:15px;font-weight:700;color:var(--text);margin:0}
.card-head-text p {font-size:12px;color:var(--text-muted);margin:2px 0 0}
.card-badge{
    margin-left:auto;padding:3px 10px;border-radius:4px;
    font-size:11px;font-weight:600;letter-spacing:.02em;
}
.badge-optional{background:#f1f5f9;color:var(--text-muted)}
.badge-required{background:var(--red-light);color:var(--red)}

/* Sale chip */
.sale-chip{
    margin-left:auto;
    display:inline-flex;align-items:center;gap:8px;
    padding:7px 14px;
    background:#fff;
    border:1px solid var(--border);
    border-radius:6px;
    font-size:12px;color:var(--text-secondary);
    max-width:300px;min-width:0;
}
.sale-chip-icon{
    flex-shrink:0;display:flex;align-items:center;justify-content:center;
    width:22px;height:22px;
    background:#fff;border:1px solid var(--border);
    border-radius:4px;
}
.sale-chip-label{
    font-weight:700;color:var(--primary);
    white-space:nowrap;font-size:11px;letter-spacing:.04em;text-transform:uppercase;
}
.sale-chip-value{
    font-weight:700;color:var(--text);font-size:13px;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0;
}

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
.form-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.form-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.form-grid-5{display:grid;grid-template-columns:repeat(5,1fr);gap:14px}
.span-2{grid-column:span 2}
.span-full{grid-column:1/-1}

/* ===== Field ===== */
.field{display:flex;flex-direction:column;gap:6px;min-width:0}
.field label{
    font-size:12px;font-weight:700;color:var(--text-secondary);
    letter-spacing:.04em;
    display:flex;align-items:center;gap:5px;margin:0;
}
.field label .req{color:var(--red);font-size:14px;line-height:1}
.field label .tip{
    display:inline-flex;align-items:center;justify-content:center;
    width:14px;height:14px;background:#f1f5f9;border-radius:3px;
    font-size:10px;color:var(--text-muted);cursor:help;
    font-style:normal;font-weight:700;position:relative;
}
.field label .tip:hover::after{
    content:attr(data-tip);
    position:absolute;bottom:calc(100% + 6px);left:50%;
    transform:translateX(-50%);
    background:var(--text);color:#fff;
    font-size:11px;font-weight:400;
    padding:5px 9px;border-radius:4px;
    white-space:nowrap;pointer-events:none;z-index:50;letter-spacing:0;
}
.field input[type="text"],
.field input[type="number"],
.field select,
.field textarea{
    width:100%;
    padding:10px 12px;
    border:1.5px solid var(--border);
    border-radius:6px;
    background:#f8fafc;
    color:var(--text);
    font-size:14px;font-family:inherit;
    outline:none;
    transition:border var(--t-fast),box-shadow var(--t-fast),background var(--t-fast);
}
.field input:focus,.field select:focus,.field textarea:focus{
    border-color:var(--primary);
    background:var(--surface);
    box-shadow:0 0 0 3px rgba(63,134,93,.12);
}
.field textarea{resize:vertical;min-height:70px;line-height:1.5;font-family:inherit}
.field textarea::placeholder,.field input::placeholder{color:var(--text-hint)}
.field input[readonly]{
    background:#f1f5f9;color:var(--text-secondary);cursor:default;
    border-style:dashed;
}
.field input[readonly]:focus{border-color:var(--border);box-shadow:none}
.field select{
    background:#f8fafc;cursor:pointer;
    appearance:none;-webkit-appearance:none;
    background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M3 4.5L6 7.5L9 4.5' stroke='%23475569' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round' fill='none'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 12px center;
    padding-right:32px;
}
.field input[type="file"]{
    padding:7px 10px;font-size:13px;cursor:pointer;
    background:var(--surface);
    border:1.5px solid var(--border);border-radius:6px;width:100%;
    color:var(--text-secondary);font-family:inherit;
}
.field input[type="file"]::file-selector-button{
    margin-right:10px;padding:6px 12px;
    border:1px solid var(--border);
    background:#f8fafc;color:var(--text-secondary);
    font-size:12px;font-weight:600;cursor:pointer;
    font-family:inherit;border-radius:4px;
    transition:all var(--t-fast);
}
.field input[type="file"]::file-selector-button:hover{
    background:var(--primary);color:#fff;border-color:var(--primary);
}

/* ===== Validation states ===== */
.field.is-invalid input,
.field.is-invalid select,
.field.is-invalid textarea{
    border-color:var(--red) !important;
    background:#fef2f2 !important;
    box-shadow:0 0 0 3px rgba(155,27,27,.10) !important;
}
.field.is-invalid label{ color:var(--red); }
.field.is-invalid input[readonly]{
    background:#fef2f2 !important;
}
.radio-group.is-invalid .radio-item label{
    border-color:var(--red);
    background:#fef2f2;
}

/* ===== Validation Summary box ===== */
.validation-summary{
    background:#fef2f2;
    border:1px solid #fecaca;
    border-radius:8px;
    padding:14px 18px;
    margin-bottom:4px;
    display:none;
    animation:slideDown .25s ease;
}
.validation-summary.show{ display:block; }
.validation-summary-title{
    font-size:14px;font-weight:700;color:var(--red);
    margin:0 0 8px;display:flex;align-items:center;gap:6px;
}
.validation-summary ul{
    margin:0;padding-left:20px;
    font-size:13px;color:var(--text-secondary);
    columns:2;column-gap:20px;
}
.validation-summary li{ margin-bottom:3px; break-inside:avoid; }
@keyframes slideDown{
    from{opacity:0;transform:translateY(-8px)}
    to{opacity:1;transform:translateY(0)}
}

/* ===== Radio group ===== */
.radio-group{display:flex;gap:10px;flex-wrap:wrap}
.radio-item{position:relative;flex:1;min-width:140px}
.radio-item input{position:absolute;opacity:0;width:0;height:0}
.radio-item label{
    display:flex;align-items:center;gap:8px;
    padding:10px 14px;
    border:1.5px solid var(--border);border-radius:6px;
    background:#f8fafc;
    cursor:pointer;
    font-size:13px;font-weight:600;color:var(--text-secondary);
    transition:all var(--t-fast);
    justify-content:center;text-align:center;letter-spacing:0;
}
.radio-item label::before{
    content:'';width:14px;height:14px;
    border:2px solid var(--text-hint);border-radius:50%;
    flex-shrink:0;transition:all var(--t-fast);background:#fff;
}
.radio-item input:checked + label{
    border-color:var(--primary);
    background:#fff;
    color:var(--primary);
}
.radio-item input:checked + label::before{
    border-color:var(--primary);
    background:radial-gradient(circle,var(--primary) 40%,#fff 45%);
}
.radio-item label:hover{border-color:var(--primary-mid)}

/* ===== Coords + Maps button ===== */
.coords-row{display:flex;gap:8px;align-items:stretch}
.coords-row input{flex:1}
.btn-custom{
    display:inline-flex;align-items:center;gap:6px;
    padding:0 18px;
    border:1.5px solid var(--blue);background:var(--blue);
    color:#fff;font-size:13px;font-weight:700;
    border-radius:6px;cursor:pointer;font-family:inherit;
    transition:all var(--t-fast);white-space:nowrap;
}
.btn-custom:hover{background:var(--blue-hover);border-color:var(--blue-hover)}

/* ===== Preview frames ===== */
.preview-grid{display:grid;grid-template-columns:2fr 1fr;gap:14px}
.preview-frame{
    border:1px solid var(--border);
    border-radius:6px;
    overflow:hidden;
    background:#f1f5f9;
    height:300px;position:relative;
}
.preview-frame iframe{
    width:100%;height:100%;border:none;display:block;background:#fff;
}
.preview-empty{
    position:absolute;inset:0;
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    gap:8px;padding:16px;text-align:center;
    background:#f8fafc;color:var(--text-muted);font-size:12px;
}

/* ===== Table ===== */
.tbl-wrap{
    overflow-x:auto;
    border:1px solid var(--border);border-radius:6px;
}
table.table{
    width:100%;border-collapse:collapse;font-size:14px;margin:0;
    table-layout:fixed;
}
table.table thead{background:#f8fafc}
table.table th{
    padding:11px 16px;font-size:11px;font-weight:700;color:var(--text-secondary);
    letter-spacing:.07em;text-transform:uppercase;text-align:left;
    border-bottom:1px solid var(--border);white-space:nowrap;
}
table.table td{
    padding:9px 16px;border-bottom:1px solid var(--border-light);
    vertical-align:middle;
}
table.table tbody tr:last-child td{border-bottom:none}
table.table tbody tr:hover td{background:#f8fafc}
.form-control1{
    width:100%;padding:7px 10px;
    border:1px solid var(--border-light);border-radius:4px;
    font-size:13px;background:#fafbfc;color:var(--text);
    box-sizing:border-box;font-family:inherit;
}
.form-control1[readonly]{background:#fafbfc;cursor:default}
.tbl-empty{padding:40px 20px;text-align:center;color:var(--text-muted);font-size:13px}

/* ===== Submit ===== */
.submit-row{
    display:flex;flex-direction:column;align-items:center;gap:12px;
    margin-top:8px;
}
.btn-success{
    display:inline-flex;align-items:center;justify-content:center;gap:8px;
    background:linear-gradient(135deg,var(--primary) 0%,var(--primary-mid) 100%);
    color:#fff;border:none;
    padding:14px 32px;border-radius:8px;
    font-size:15px;font-weight:700;
    cursor:pointer;min-width:240px;
    font-family:inherit;letter-spacing:.01em;
    transition:all var(--t-base);
    box-shadow:0 4px 12px rgba(63,134,93,.30);
}
.btn-success:hover{
    background:linear-gradient(135deg,var(--primary-hover) 0%,var(--primary) 100%);
    transform:translateY(-1px);
    box-shadow:0 6px 18px rgba(63,134,93,.36);
}
.btn-success:active{transform:scale(.98);box-shadow:var(--shadow-sm)}
.btn-success:disabled,.btn-danger{
    background:linear-gradient(135deg,#94a3b8,#cbd5e1);
    cursor:not-allowed;transform:none;box-shadow:none;
}
.btn-success.is-incomplete{
    background:linear-gradient(135deg,#94a3b8,#cbd5e1) !important;
    box-shadow:0 4px 12px rgba(148,163,184,.30) !important;
    cursor:not-allowed !important;
}
.btn-success.is-incomplete:hover{
    transform:none !important;
    box-shadow:0 4px 12px rgba(148,163,184,.30) !important;
}
.btn-hint{
    font-size:12px;color:var(--text-muted);
    display:flex;align-items:center;gap:5px;
}
.btn-hint.warn{ color:var(--red); font-weight:600; }

/* ===== Modal (login) ===== */
.modal-overlay{
    position:fixed;top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,.5);display:none;
    justify-content:center;align-items:center;z-index:9999;
}
.modal-box{
    background:#fff;padding:28px 30px;border-radius:12px;
    text-align:center;width:340px;
    box-shadow:0 10px 30px rgba(0,0,0,.2);
    animation:fadeIn .25s ease;
}
.modal-box h3{margin:0 0 10px;font-size:18px;color:var(--text)}
.modal-box p{margin:0 0 20px;color:#555;font-size:14px}
.modal-box button{
    background:#f3b201;color:#fff;border:none;
    padding:10px 26px;border-radius:20px;cursor:pointer;
    font-size:14px;font-weight:600;font-family:inherit;
}
.modal-box button:hover{background:#fbc42d}
@keyframes fadeIn{from{transform:scale(.9);opacity:0}to{transform:scale(1);opacity:1}}

/* ===== Responsive ===== */
@media (max-width:1180px){
    .container{margin:16px;max-width:none}
}
@media (max-width:992px){
    .form-grid-5{grid-template-columns:repeat(3,1fr)}
    .form-grid-4{grid-template-columns:repeat(2,1fr)}
    .validation-summary ul{columns:1}
}
@media (max-width:640px){
    .container{margin:0;border-radius:0;border:none;box-shadow:none}
    .header-bar{padding:14px 16px}
    .text-dark{font-size:18px}
    .page{padding:16px 14px 32px}
    .card-body{padding:16px 18px}
    .card-head{padding:14px 18px 12px;flex-wrap:wrap}
    .form-grid-2,.form-grid-3,.form-grid-4,.form-grid-5{grid-template-columns:1fr}
    .span-2{grid-column:1}
    .preview-grid{grid-template-columns:1fr}
    .radio-item{min-width:100%}
    .sale-chip{margin-left:0;width:100%;max-width:none;margin-top:4px}
    .btn-success{width:100%;min-width:0}
}
</style>
</head>

<body>

<div class="container">

<!-- ===== Header ===== -->
<div class="header-bar">
    <h2 class="text-dark">สร้างเส้นทางส่งสินค้า</h2>
    <button onclick="history.back()" class="btn-back">ย้อนกลับ</button>
</div>

<!-- ===== Login Modal ===== -->
<div id="loginModal" class="modal-overlay">
    <div class="modal-box">
        <h3>แจ้งเตือน</h3>
        <p>ไม่สามารถเปิดบิลได้ กรุณาเข้าสู่ระบบก่อน</p>
        <button onclick="goLogin()">ตกลง</button>
    </div>
</div>

<form id="billForm">
    @csrf
    <input type="hidden" name="so_id" id="so_id" value="">

    <div class="page">

        <!-- ====================== Card 1: ข้อมูลเอกสาร ====================== -->
        <div class="card">
            <div class="card-head">
                <div class="card-icon ci-green">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <rect x="3" y="2" width="12" height="14" rx="2.5" stroke="#3f865d" stroke-width="1.5"/>
                        <path d="M6 6h6M6 9h6M6 12h4" stroke="#3f865d" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="card-head-text">
                    <h3>ข้อมูลเอกสาร</h3>
                    <p>Sales Order, บิลส่งของ และผู้รับผิดชอบ</p>
                </div>
                <div class="sale-chip" title="ผู้ขายที่รับผิดชอบเอกสารนี้">
                    <div class="sale-chip-icon">
                        <svg width="12" height="12" viewBox="0 0 14 14" fill="none">
                            <circle cx="7" cy="5" r="2.6" stroke="#3f865d" stroke-width="1.4"/>
                            <path d="M2 12.5c0-2.76 2.24-5 5-5s5 2.24 5 5" stroke="#3f865d" stroke-width="1.4" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span class="sale-chip-label">ผู้ขาย:</span>
                    <span class="sale-chip-value" id="sale-chip-value">—</span>
                </div>
            </div>
            <div class="card-body">
                <!-- แถว 1: SO + บิล -->
                <div class="form-grid-2" style="margin-bottom:14px">
                    <div class="field">
                        <label for="so_number">เลขที่ SO <i class="tip" data-tip="รหัส Sales Order จากระบบ">?</i></label>
                        <input type="text" id="so_number" name="so_number" required readonly placeholder="—">
                    </div>
                    <div class="field">
                        <label for="billid">เลขที่บิลส่งของ <i class="tip" data-tip="เลขที่บิลส่งของจากระบบ">?</i></label>
                        <input type="text" id="billid" name="billid" readonly required placeholder="—">
                    </div>
                </div>

                <!-- แถว 2: ผู้เปิดบิล / PO / ประเภทบิล / รหัสลูกค้า -->
                <div class="form-grid-4">
                    <div class="field">
                        <label for="emp_name">ผู้เปิดบิล <span class="req">*</span></label>
                        <input type="text" id="emp_name" name="emp_name"
                            value="{{ request()->filled('create_by') ? request('create_by') : 'Guest' }}" readonly>
                    </div>
                    <div class="field">
                        <label for="ponum">เลขที่ PO ลูกค้า</label>
                        <input type="text" id="ponum" name="ponum" readonly placeholder="—">
                    </div>
                    <div class="field">
                        <label for="billtype">ประเภทบิล <i class="tip" data-tip="ขายสด / ขายเชื่อ ตามรหัสบิล">?</i></label>
                        <input type="text" id="billtype" name="billtype" placeholder="—" readonly>
                    </div>
                    <div class="field">
                        <label for="customer_id">รหัสลูกค้า</label>
                        <input type="text" id="customer_id" name="customer_id" readonly placeholder="—">
                    </div>
                </div>

                <input type="hidden" id="sale_name" name="sale_name">
            </div>
        </div>

        <!-- ====================== Card 2: ข้อมูลลูกค้า ====================== -->
        <div class="card">
            <div class="card-head">
                <div class="card-icon ci-blue">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <circle cx="9" cy="6" r="3.2" stroke="#1e6fd9" stroke-width="1.5"/>
                        <path d="M2.5 15.5c0-3.59 2.91-6.5 6.5-6.5s6.5 2.91 6.5 6.5" stroke="#1e6fd9" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="card-head-text">
                    <h3>ข้อมูลลูกค้า</h3>
                    <p>ชื่อบริษัท ผู้ติดต่อ เบอร์โทร และที่อยู่จัดส่ง</p>
                </div>
                <span class="card-badge badge-optional">แก้ไขได้</span>
            </div>
            <div class="card-body">
                <div class="form-grid-2" style="row-gap:16px">
                    <div class="field span-full">
                        <label for="customer_name">ชื่อบริษัท / ลูกค้า</label>
                        <input type="text" id="customer_name" name="customer_name" readonly required placeholder="ชื่อบริษัทหรือลูกค้า">
                    </div>

                    <div class="field">
                        <label for="contactso">ชื่อผู้ติดต่อ <span class="req">*</span></label>
                        <input type="text" id="contactso" name="contactso" required placeholder="กรอกชื่อผู้ติดต่อ">
                    </div>
                    <div class="field">
                        <label for="customer_tel">เบอร์ติดต่อ</label>
                        <input type="text" id="customer_tel" name="customer_tel" placeholder="0XX-XXX-XXXX">
                    </div>

                    <div class="field span-full">
                        <label for="date_of_dali">วันกำหนดส่ง</label>
                        <input type="text" id="date_of_dali" name="date_of_dali" readonly required placeholder="DD-MM-YYYY">
                    </div>

                    <div class="field span-full">
                        <label for="customer_address">ที่อยู่หัวบิล <i class="tip" data-tip="ที่อยู่ที่ใช้ในเอกสารบิล">?</i></label>
                        <input type="text" id="customer_address" name="customer_address" readonly required placeholder="ที่อยู่หัวบิล">
                    </div>

                    <div class="field span-full">
                        <label for="customer_la_long">ที่อยู่จัดส่ง (ละติจูด, ลองจิจูด) <span class="req">*</span> <i class="tip" data-tip="ใช้ Google Maps คัดลอกพิกัดมาวาง">?</i></label>
                        <div class="coords-row">
                            <input type="text" id="customer_la_long" name="customer_la_long"
                                placeholder="13.7563, 100.5018"
                                oninput="this.value = this.value.replace(/[^0-9,.\-\s]/g, '')">
                            <button type="button" class="btn-custom" onclick="return openGoogleMaps(event)">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <path d="M7 1.5c-2.21 0-4 1.79-4 4 0 3 4 7 4 7s4-4 4-7c0-2.21-1.79-4-4-4z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                                    <circle cx="7" cy="5.5" r="1.4" stroke="currentColor" stroke-width="1.4"/>
                                </svg>
                                Google Maps
                            </button>
                        </div>
                    </div>

                    <div class="field span-full">
                        <label for="notes">รายละเอียดเพิ่มเติม (สำหรับคนขับ) <i class="tip" data-tip="ข้อความนี้จะปรากฏในเอกสารสำหรับคนขับ">?</i></label>
                        <textarea id="notes" name="notes" rows="2"
                            placeholder="*กรณีเพิ่มข้อมูลที่ช่องนี้ระบบจะปริ้นเอกสารให้คนขับ*"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====================== Card 3: ประเภท + เอกสาร PO + พรีวิว ====================== -->
        <div class="card">
            <div class="card-head">
                <div class="card-icon ci-amber">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <path d="M5 2h6l3 3v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1z" stroke="#b45309" stroke-width="1.5" stroke-linejoin="round"/>
                        <path d="M11 2v3h3" stroke="#b45309" stroke-width="1.5" stroke-linejoin="round"/>
                        <path d="M7 9h4M7 12h3" stroke="#b45309" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="card-head-text">
                    <h3>ประเภทงาน &amp; เอกสาร PO</h3>
                    <p>เลือกประเภท แบบฟอร์ม และแนบไฟล์ PO เพื่อพรีวิว</p>
                </div>
            </div>
            <div class="card-body">
                <div class="form-grid-2" style="row-gap:16px">
                    <div class="field span-full" id="field-typeinbill">
                        <label>ประเภทสินค้า / บริการ <span class="req">*</span></label>
                        <div class="radio-group">
                            <div class="radio-item">
                                <input type="radio" id="type-sell" name="typeinbill" value="ขายสินค้า" required>
                                <label for="type-sell">งานขายสินค้า</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" id="type-service" name="typeinbill" value="งานบริการ">
                                <label for="type-service">งานบริการ / อบรม 3%</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" id="type-rent" name="typeinbill" value="งานเช่า">
                                <label for="type-rent">งานเช่า 5%</label>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label for="POdocument">อัปโหลดเอกสาร PO <i class="tip" data-tip="รองรับ PDF, JPG, PNG (ระบบจะแปลงเป็น PDF อัตโนมัติ)">?</i></label>
                        <input type="file" id="POdocument" name="POdocument" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div class="field">
                        <label for="formtype">แบบฟอร์มเอกสาร <span class="req">*</span></label>
                        <select id="formtype" name="formtype" required>
                            <option value="ไม่มีข้อมูล" disabled selected>ไม่มีข้อมูล</option>
                            <option value="บิล/PO3">บิล/PO3</option>
                            <option value="บิล/PO3/วางบิล">บิล/PO3/วางบิล</option>
                            <option value="บิล/PO3/วางบิล/สำเนาหน้าบิล2">บิล/PO3/วางบิล/สำเนาหน้าบิล2</option>
                            <option value="บิล/PO3/สำเนาหน้าบิล2">บิล/PO3/สำเนาหน้าบิล2</option>
                            <option value="บิล/PO5/สำเนาหน้าบิล3">บิล/PO5/สำเนาหน้าบิล3</option>
                            <option value="บิล/PO3/บัญชี">บิล/PO3/บัญชี</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top:20px">
                    <div class="section-divider">
                        <div class="section-divider-line"></div>
                        <span class="section-divider-label">พรีวิวแผนที่ &amp; เอกสาร</span>
                        <div class="section-divider-line"></div>
                    </div>
                    <div class="preview-grid">
                        <div class="field">
                            <label>แผนที่จุดส่งสินค้า</label>
                            <div class="preview-frame">
                                <iframe id="mapFrame" allowfullscreen="" loading="lazy"></iframe>
                                <div class="preview-empty" id="mapEmpty">
                                    กรอกพิกัด ละติจูด, ลองจิจูด<br>เพื่อแสดงแผนที่
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label>เอกสาร PO (PDF)</label>
                            <div class="preview-frame">
                                <iframe id="pdfPreview" allowfullscreen></iframe>
                                <div class="preview-empty" id="pdfEmpty">
                                    ยังไม่มีไฟล์ PO<br>อัปโหลดเพื่อแสดงตัวอย่าง
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====================== Card 4: รายการสินค้า ====================== -->
        <div class="card">
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
                    <h3>รายการสินค้า / บริการ</h3>
                    <p>รายการที่จะจัดส่งจากบิลนี้ (ดึงจากระบบอัตโนมัติ)</p>
                </div>
            </div>
            <div style="padding:18px 24px 24px">
                <div class="tbl-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width:18%">รหัสสินค้า</th>
                                <th style="width:48%">รายการ</th>
                                <th style="width:14%;text-align:center">จำนวน</th>
                                <th style="width:20%;text-align:center">ราคาต่อหน่วย</th>
                            </tr>
                        </thead>
                        <tbody id="detail">
                            <tr><td colspan="4"><div class="tbl-empty">กำลังโหลดรายการสินค้า...</div></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ====================== Validation Summary ====================== -->
        <div class="validation-summary" id="validationSummary">
            <p class="validation-summary-title">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <circle cx="8" cy="8" r="7" stroke="#9b1b1b" stroke-width="1.5"/>
                    <path d="M8 4.5v4M8 11h.01" stroke="#9b1b1b" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span>กรุณากรอกข้อมูลให้ครบก่อนบันทึก</span>
            </p>
            <ul id="validationList"></ul>
        </div>

        <!-- ====================== Submit ====================== -->
        <div class="submit-row">
            <button type="button" id="submitBill" class="btn-success">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                    <path d="M3.5 9l4 4 6-7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                บันทึกเส้นทางส่งสินค้า
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
</form>

</div>
<!-- ===== /container ===== -->

<!-- ===================== SCRIPTS ===================== -->
<script>
/* ====================== Login modal ====================== */
document.addEventListener('DOMContentLoaded', function () {
    const empInput = document.getElementById('emp_name');
    if (empInput && empInput.value.trim() === 'Guest') {
        document.getElementById('loginModal').style.display = 'flex';
    }
});
function goLogin(){ window.location.href = 'http://server_update:8000/login'; }

/* ====================== Bill type detect ====================== */
const billidInput  = document.getElementById('billid');
const billtypeInput = document.getElementById('billtype');
function checkBillid(){
    const v = billidInput.value.toLowerCase().trim();
    if(v.startsWith('cs'))      billtypeInput.value = 'ขายสด';
    else if(v.length > 0)        billtypeInput.value = 'ขายเชื่อ';
    else                          billtypeInput.value = '';
}
window.addEventListener('load', checkBillid);
billidInput.addEventListener('input', checkBillid);

/* ====================== Helper: safe JSON ====================== */
async function safeJson(response, label){
    const text = await response.text();
    if(!response.ok){
        console.error(`[${label}] HTTP ${response.status}:`, text.substring(0,800));
        const m = text.match(/<title>([^<]+)<\/title>/i);
        throw new Error(`${label} → ${response.status}` + (m?`: ${m[1]}`:''));
    }
    try{
        return JSON.parse(text);
    }catch(e){
        console.error(`[${label}] Server ตอบไม่ใช่ JSON:`, text.substring(0,500));
        throw new Error(`${label} → response ไม่ใช่ JSON`);
    }
}

/* ====================== Form-type fetch ====================== */
function fetchFormType(){
    const customer_id = (document.getElementById("customer_id").value || '').trim();
    const formtypeSelect = document.getElementById("formtype");
    if(!customer_id){
        console.log('[fetchFormType] skipped: customer_id is empty');
        formtypeSelect.value = 'ไม่มีข้อมูล';
        document.getElementById("customer_la_long").value = '';
        document.getElementById("notes").value = '';
        return;
    }
    fetch('/fetch-formtype', {
        method:'POST',
        credentials:'same-origin',
        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-Requested-With':'XMLHttpRequest',
            'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body:JSON.stringify({customer_id})
    })
    .then(r => safeJson(r, 'fetch-formtype'))
    .then(data=>{
        if(data.formtype){
            const exists = Array.from(formtypeSelect.options).some(o=>o.value===data.formtype);
            if(!exists) formtypeSelect.add(new Option(data.formtype, data.formtype));
            formtypeSelect.value = data.formtype;
        } else {
            formtypeSelect.value = 'ไม่มีข้อมูล';
        }
        const laLong = data.customer_la_long || '';
        const isValidCoords = /^-?\d+(\.\d+)?,\s*-?\d+(\.\d+)?$/.test(laLong.trim());
        document.getElementById("customer_la_long").value = isValidCoords ? laLong : '';
        document.getElementById("notes").value = data.note || '';
        updateMap();
        refreshSubmitState();
    })
    .catch(err=>{
        console.error('fetchFormType error:', err);
        formtypeSelect.value = 'ไม่มีข้อมูล';
        refreshSubmitState();
    });
}
function fetchContactSo(){
    const customer_id = (document.getElementById("customer_id").value || '').trim();
    const contactInput = document.getElementById("contactso");
    if(!customer_id){
        console.log('[fetchContactSo] skipped: customer_id is empty');
        contactInput.value = '';
        return;
    }
    fetch('/fetch-contactso',{
        method:'POST',
        credentials:'same-origin',
        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-Requested-With':'XMLHttpRequest',
            'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body:JSON.stringify({customer_id})
    })
    .then(r => safeJson(r, 'fetch-contactso'))
    .then(d=>{
        contactInput.value = d.contactso || '';
        refreshSubmitState();
    })
    .catch(err => { console.error('fetchContactSo error:', err); });
}

/* ====================== Map ====================== */
function updateMap(){
    const coords = (document.getElementById('customer_la_long').value||'').trim();
    const frame  = document.getElementById('mapFrame');
    const empty  = document.getElementById('mapEmpty');
    const isValid = /^-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?$/.test(coords);
    if(isValid){
        frame.src = `https://www.google.com/maps?q=${encodeURIComponent(coords)}&output=embed`;
        if(empty) empty.style.display = 'none';
    } else {
        frame.src = '';
        if(empty) empty.style.display = 'flex';
    }
}
document.getElementById('customer_la_long').addEventListener('input', updateMap);
updateMap();

/* ====================== Google Maps popup ====================== */
let mapWindow = null;
function openGoogleMaps(event){
    if(event){
        event.preventDefault();
        event.stopPropagation();
    }

    const w = 900, h = 650;
    const left = Math.max(0, Math.round((window.screen.availWidth  - w) / 2));
    const top  = Math.max(0, Math.round((window.screen.availHeight - h) / 2));

    const features = [
        'popup=yes',
        'width='+w,
        'height='+h,
        'left='+left,
        'top='+top,
        'scrollbars=yes',
        'resizable=yes',
        'toolbar=no',
        'menubar=no',
        'location=no',
        'status=no',
        'directories=no',
        'titlebar=no'
    ].join(',');

    if(mapWindow && !mapWindow.closed){
        try{
            mapWindow.focus();
            mapWindow.location.href = 'https://www.google.com/maps/@13.7563,100.5018,14z';
            return false;
        }catch(e){ /* fall through */ }
    }

    const winName = 'gmaps_popup_' + Date.now();
    mapWindow = window.open('https://www.google.com/maps/@13.7563,100.5018,14z', winName, features);

    if(!mapWindow || mapWindow.closed || typeof mapWindow.closed === 'undefined'){
        alert('Browser ปิดกั้น popup ไว้\nกรุณาอนุญาต popup สำหรับเว็บนี้ในการตั้งค่า Browser');
    } else {
        try{ mapWindow.focus(); }catch(e){}
    }
    return false;
}

/* ====================== SO loader ====================== */
const urlParams = new URLSearchParams(window.location.search);
const soNum = urlParams.get('so_num');
const billId = urlParams.get('billid');

if(soNum){
    document.getElementById('so_number').value = soNum;
    fetchSODetails(soNum);
}
window.addEventListener('DOMContentLoaded', function(){
    if(billId) document.getElementById('billid').value = billId;
});

async function fetchSODetails(soNum){
    try{
        let response = await fetch(`http://server_update:8000/api/getSODetail?SONum=${soNum}`);
        if(!response.ok) throw new Error('เกิดข้อผิดพลาดในการโหลดข้อมูล');
        let data = await response.json();
        if(!data.SoDetail || data.SoDetail.length === 0){
            alert("ไม่พบข้อมูลที่ตรงกับเลขที่ SO นี้: " + soNum);
            return;
        }
        const soDetails = data.SoDetail;
        const SoStatus  = data.SoStatus;
        document.getElementById('so_id').value         = SoStatus.SONum;
        document.getElementById('ponum').value         = soDetails.CustPONo;
        document.getElementById('customer_id').value   = SoStatus.CustID;
        fetchFormType();
        fetchContactSo();
        document.getElementById('customer_name').value = soDetails.CustName;
        document.getElementById('customer_address').value = [
            soDetails.CustAddr1, soDetails.ContDistrict, soDetails.ContAmphur,
            soDetails.ContProvince, soDetails.ContPostCode,
            (soDetails.ShipToAddr1 || soDetails.ShipToAddr2)
                ? "สถานที่ส่ง: " + [soDetails.ShipToAddr1, soDetails.ShipToAddr2].filter(Boolean).join(', ')
                : null
        ].filter(Boolean).join(', ');
        document.getElementById('customer_la_long').value =
            [soDetails.Latitude, soDetails.Longitude].filter(Boolean).join(', ');
        document.getElementById('customer_tel').value = soDetails.ContTel;
        document.getElementById('sale_name').value    = SoStatus.createdBy || '';
        document.getElementById('sale-chip-value').textContent = SoStatus.createdBy || '—';

        const billData = data.Bills[0][billId];
        const items = billData.items;
        let deliveryDate = billData.DocuDate;
        if(deliveryDate){
            let [datePart] = deliveryDate.split(' ');
            let [year,month,day] = datePart.split('-');
            document.getElementById('date_of_dali').value = `${day}-${month}-${year}`;
        }

        let itemCounter = 1;
        const tableBody = document.getElementById('detail');
        tableBody.innerHTML = '';
        items.forEach(item=>{
            let newRow = document.createElement('tr');
            const safeGoodName = item.GoodName.replace(/"/g,'&quot;');
            const itemId = `53-${String(itemCounter).padStart(4,'0')}`;
            newRow.innerHTML = `
                <td style="text-align:center;vertical-align:middle">
                    <input type="text" class="form-control1" name="item_id[]" value="${itemId}" readonly style="text-align:center">
                </td>
                <td><input type="text" class="form-control1" name="item_name[]" value="${safeGoodName}" readonly></td>
                <td style="text-align:center;vertical-align:middle">
                    <input type="text" class="form-control1 item_quantity" name="item_quantity[]" value="${parseFloat(item.GoodQty2).toFixed(2)}" readonly style="text-align:center">
                </td>
                <td style="text-align:center;vertical-align:middle">
                    <input type="text" class="form-control1" name="unit_price[]" value="${parseFloat(item.GoodPrice2).toFixed(2)}" readonly style="text-align:center">
                </td>
            `;
            tableBody.appendChild(newRow);
            itemCounter++;
        });
        if(items.length === 0){
            tableBody.innerHTML = '<tr><td colspan="4"><div class="tbl-empty">ไม่พบรายการสินค้า</div></td></tr>';
        }
        updateMap();
        refreshSubmitState();
    }catch(err){ console.error(err); }
}

/* ====================== PO upload + convert to PDF ====================== */
const fileInput  = document.getElementById('POdocument');
const pdfPreview = document.getElementById('pdfPreview');
const pdfEmpty   = document.getElementById('pdfEmpty');
let convertedPDFBlob = null;
let originalFilename = '';
const isLandscape = (w,h) => w > h;

fileInput.addEventListener('change', async function(){
    const file = fileInput.files[0];
    if(!file) return;
    const ext = file.name.split('.').pop().toLowerCase();
    originalFilename = file.name;

    if(ext === 'pdf'){
        const reader = new FileReader();
        reader.onload = async function(e){
            const base64 = e.target.result.split(',')[1];
            const pdf = await pdfjsLib.getDocument({data: atob(base64)}).promise;
            const firstPage = await pdf.getPage(1);
            const firstViewport = firstPage.getViewport({scale:1});
            const isPdfLandscape = isLandscape(firstViewport.width, firstViewport.height);
            const { jsPDF } = window.jspdf;
            const pdfDoc = new jsPDF(isPdfLandscape?'l':'p','mm','a4');
            for(let p=1; p<=pdf.numPages; p++){
                const page = await pdf.getPage(p);
                const viewport = page.getViewport({scale:2});
                const canvas = document.createElement('canvas');
                canvas.width  = viewport.width;
                canvas.height = viewport.height;
                const ctx = canvas.getContext('2d');
                await page.render({canvasContext:ctx, viewport}).promise;
                const imgData = canvas.toDataURL('image/jpeg',1.0);
                if(p>1) pdfDoc.addPage();
                const pw = pdfDoc.internal.pageSize.getWidth();
                const ph = pdfDoc.internal.pageSize.getHeight();
                pdfDoc.addImage(imgData,'JPEG',0,0,pw,ph);
            }
            convertedPDFBlob = pdfDoc.output('blob');
            pdfPreview.src = URL.createObjectURL(convertedPDFBlob) + '#toolbar=0&navpanes=0&scrollbar=0&view=FitH';
            if(pdfEmpty) pdfEmpty.style.display = 'none';
        };
        reader.readAsDataURL(file);
    } else if(['jpg','jpeg','png'].includes(ext)){
        const reader = new FileReader();
        reader.onload = function(e){
            const img = new Image();
            img.src = e.target.result;
            img.onload = function(){
                const isImgLandscape = isLandscape(img.width, img.height);
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF(isImgLandscape?'l':'p','mm','a4');
                const pw = pdf.internal.pageSize.getWidth();
                const ph = pdf.internal.pageSize.getHeight();
                const canvas = document.createElement('canvas');
                canvas.width  = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img,0,0);
                const imgData = canvas.toDataURL('image/jpeg',1.0);
                pdf.addImage(imgData,'JPEG',0,0,pw,ph);
                convertedPDFBlob = pdf.output('blob');
                pdfPreview.src = URL.createObjectURL(convertedPDFBlob) + '#toolbar=0&navpanes=0&scrollbar=0&view=FitH';
                if(pdfEmpty) pdfEmpty.style.display = 'none';
            };
        };
        reader.readAsDataURL(file);
    } else {
        alert('ไฟล์ไม่รองรับ กรุณาอัปโหลด PDF หรือรูปภาพ');
        fileInput.value = '';
        convertedPDFBlob = null;
        pdfPreview.src = '';
        if(pdfEmpty) pdfEmpty.style.display = 'flex';
    }
});

/* ====================== Validation System ====================== */
const VALIDATION_RULES = [
    { id:'so_number',         label:'เลขที่ SO',                    type:'input' },
    { id:'billid',            label:'เลขที่บิลส่งของ',               type:'input' },
    { id:'contactso',         label:'ชื่อผู้ติดต่อ',                  type:'input' },
    { id:'date_of_dali',      label:'วันกำหนดส่ง',                  type:'input' },
    { id:'customer_address',  label:'ที่อยู่หัวบิล',                  type:'input' },
    { id:'customer_la_long',  label:'พิกัดละติจูด, ลองจิจูด',         type:'coords' },
    { name:'typeinbill',      label:'ประเภทสินค้า / บริการ',          type:'radio' },
    { id:'formtype',          label:'แบบฟอร์มเอกสาร',                type:'select', invalidValue:'ไม่มีข้อมูล' },
];

function getFieldEl(rule){
    if(rule.type === 'radio') return null;
    return document.getElementById(rule.id);
}
function getFieldWrapper(el){
    return el ? el.closest('.field') : null;
}
function clearFieldError(rule){
    if(rule.type === 'radio'){
        const wrap = document.getElementById('field-typeinbill');
        if(wrap) wrap.querySelector('.radio-group')?.classList.remove('is-invalid');
        return;
    }
    const el = getFieldEl(rule);
    const wrap = getFieldWrapper(el);
    if(wrap) wrap.classList.remove('is-invalid');
}
function markFieldError(rule){
    if(rule.type === 'radio'){
        const wrap = document.getElementById('field-typeinbill');
        if(wrap) wrap.querySelector('.radio-group')?.classList.add('is-invalid');
        return;
    }
    const el = getFieldEl(rule);
    const wrap = getFieldWrapper(el);
    if(wrap) wrap.classList.add('is-invalid');
}

/** ตรวจสอบฟอร์มทั้งหมด คืน array ของ rule ที่ยังไม่ผ่าน */
function validateForm(){
    const errors = [];
    VALIDATION_RULES.forEach(rule=>{
        let invalid = false;
        if(rule.type === 'radio'){
            const checked = document.querySelector(`input[name="${rule.name}"]:checked`);
            invalid = !checked;
        } else if(rule.type === 'select'){
            const el = getFieldEl(rule);
            invalid = !el || !el.value || el.value === rule.invalidValue;
        } else if(rule.type === 'coords'){
            const el = getFieldEl(rule);
            const v  = (el?.value || '').trim();
            invalid = !v || !/^-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?$/.test(v);
        } else {
            const el = getFieldEl(rule);
            invalid = !el || !el.value.trim();
        }
        if(invalid){
            errors.push(rule);
            markFieldError(rule);
        } else {
            clearFieldError(rule);
        }
    });

    // ตรวจรายการสินค้า — ต้องมีอย่างน้อย 1 แถว
    const itemRows = document.querySelectorAll('#detail tr');
    let hasItem = false;
    itemRows.forEach(r=>{
        if(r.querySelector('input[name="item_id[]"]')) hasItem = true;
    });
    if(!hasItem){
        errors.push({ label:'รายการสินค้า (ยังไม่มีรายการในบิลนี้)', type:'items' });
    }

    return errors;
}

/** อัปเดตสถานะปุ่ม submit + hint ใต้ปุ่ม */
function refreshSubmitState(){
    const btn = document.getElementById('submitBill');
    const hint = document.getElementById('btnHint');
    const hintText = document.getElementById('btnHintText');
    const errors = validateForm();

    if(errors.length === 0){
        btn.classList.remove('is-incomplete');
        btn.title = '';
        hint.classList.remove('warn');
        hintText.textContent = 'พร้อมบันทึก ✓';
        document.getElementById('validationSummary').classList.remove('show');
    } else {
        btn.classList.add('is-incomplete');
        btn.title = `ยังขาด ${errors.length} ช่อง: ` + errors.map(e=>e.label).join(', ');
        hint.classList.add('warn');
        hintText.textContent = `ยังขาดข้อมูลอีก ${errors.length} ช่อง — โปรดกรอกให้ครบ`;
    }
}

/** แสดง box สรุปข้อผิดพลาด + เลื่อนไปช่องแรกที่ผิด */
function showValidationSummary(errors){
    const box  = document.getElementById('validationSummary');
    const list = document.getElementById('validationList');
    list.innerHTML = '';
    errors.forEach(err=>{
        const li = document.createElement('li');
        li.textContent = err.label;
        list.appendChild(li);
    });
    box.classList.add('show');
    box.scrollIntoView({ behavior:'smooth', block:'center' });

    // focus ช่องแรกที่ผิด (ที่ไม่ใช่ readonly)
    const first = errors.find(e => e.type && e.type !== 'radio' && e.type !== 'items');
    if(first){
        const el = getFieldEl(first);
        if(el && !el.readOnly){
            setTimeout(()=>el.focus(), 400);
        }
    }
}

/** ผูก real-time listener กับทุกฟิลด์ */
VALIDATION_RULES.forEach(rule=>{
    if(rule.type === 'radio'){
        document.querySelectorAll(`input[name="${rule.name}"]`).forEach(r=>{
            r.addEventListener('change', refreshSubmitState);
        });
    } else {
        const el = getFieldEl(rule);
        if(el){
            el.addEventListener('input',  refreshSubmitState);
            el.addEventListener('change', refreshSubmitState);
        }
    }
});

// MutationObserver: เฝ้าดูตาราง #detail ว่ามีแถวสินค้าเพิ่ม/ลบหรือไม่
const detailObserver = new MutationObserver(refreshSubmitState);
detailObserver.observe(document.getElementById('detail'), { childList:true, subtree:true });

// เรียกครั้งแรกตอนโหลดหน้า
window.addEventListener('DOMContentLoaded', refreshSubmitState);

/* ====================== Submit ====================== */
document.getElementById('submitBill').addEventListener('click', async function(event){
    event.preventDefault();
    const btn = this;
    const form = document.getElementById('billForm');

    // ✅ ตรวจสอบข้อมูลครบหรือไม่ก่อนส่ง
    const errors = validateForm();
    if(errors.length > 0){
        showValidationSummary(errors);
        // shake animation ที่ปุ่ม
        btn.animate([
            { transform:'translateX(0)' },
            { transform:'translateX(-6px)' },
            { transform:'translateX(6px)' },
            { transform:'translateX(-4px)' },
            { transform:'translateX(4px)' },
            { transform:'translateX(0)' },
        ], { duration:350, easing:'ease-in-out' });
        return; // หยุดทันที — ไม่ส่งฟอร์ม
    }

    const billid = form.querySelector('input[name="billid"]').value.trim();
    if(!billid){ alert('กรุณากรอก billid ก่อนส่ง'); return; }

    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.classList.remove('btn-success');
    btn.classList.add('btn-danger');
    btn.innerText = 'กำลังโหลด...';

    try{
        let checkResponse = await fetch('{{ route("check.billid") }}', {
            method:'POST',
            credentials:'same-origin',
            headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-Requested-With':'XMLHttpRequest',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body:JSON.stringify({billid}),
        });
        let checkData = await safeJson(checkResponse, 'check.billid');

        if(checkData.exists){
            let confirmAdd = confirm(`${checkData.billid} นี้ถูกสร้างโดย ${checkData.emp_name} แล้ว\nต้องการเพิ่มข้อมูลอีกครั้งหรือไม่?`);
            if(!confirmAdd){
                resetBtn(); return;
            }
        }
        if(typeof convertedPDFBlob === 'undefined' || !convertedPDFBlob){
            const confirmNoPO = confirm("คุณยังไม่ได้แนบเอกสาร PO\nต้องการเพิ่มข้อมูลโดยไม่มีเอกสาร PO ใช่หรือไม่?");
            if(!confirmNoPO){ resetBtn(); return; }
        }

        let formData = new FormData(form);
        if(typeof convertedPDFBlob !== 'undefined' && convertedPDFBlob){
            formData.append('POdocument', convertedPDFBlob, originalFilename || 'upload.pdf');
        }
        let itemRows = document.querySelectorAll('table tbody tr');
        itemRows.forEach((row, i)=>{
            const idIn  = row.querySelector('input[name="item_id[]"]');
            const nmIn  = row.querySelector('input[name="item_name[]"]');
            const qtIn  = row.querySelector('input[name="item_quantity[]"]');
            const prIn  = row.querySelector('input[name="unit_price[]"]');
            if(!idIn||!nmIn||!qtIn||!prIn) return;
            formData.append(`item_id[${i}]`,        idIn.value);
            formData.append(`item_name[${i}]`,      nmIn.value);
            formData.append(`item_quantity[${i}]`,  qtIn.value);
            formData.append(`unit_price[${i}]`,     prIn.value);
            formData.append(`status[${i}]`, 1);
        });

        let response = await fetch('{{ route("insert.post") }}', {
            method:'POST',
            credentials:'same-origin',
            body: formData,
            headers:{
                'Accept':'application/json',
                'X-Requested-With':'XMLHttpRequest',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
        });
        let data = await safeJson(response, 'insert.post');

        if(data.success){
            alert(data.success);
            window.location.href = 'http://server_update:8000/solist';
        } else if(data.error){
            alert(data.error);
            resetBtn();
        }
    }catch(err){
        console.error('❌ เกิดข้อผิดพลาด:', err);
        alert('เกิดข้อผิดพลาด: ' + (err.message || 'ไม่ทราบสาเหตุ') + '\nกรุณาดู Console (F12) เพื่อดูรายละเอียด');
        resetBtn();
    }

    function resetBtn(){
        btn.disabled = false;
        btn.classList.remove('btn-danger');
        btn.classList.add('btn-success');
        btn.innerHTML = originalHTML;
        refreshSubmitState();
    }
});
</script>
</body>
</html>