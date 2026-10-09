<!DOCTYPE html>
{{-- resources/views/sale/dashboardwrong.blade.php — แก้ของผิด (สินค้าผิด) + ค้างบิล สำหรับ Sale --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>แก้ของผิด / ค้างบิล</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --ink:#1e293b; --canvas:#fff; --muted:#6b7280; --border:#dcdcdc;
            --primary:#2853d5; --primary-dark:#1d4ed8; --primary-light:#eff6ff; --on-primary:#fff;
            --success:#16a34a; --success-dark:#15803d; --success-light:#e7f5ec;
            --danger:#dc2626; --danger-dark:#b91c1c; --danger-light:#fef2f2;
            --warning:#ea580c; --warning-light:#fff7ed;
            --page-bg:#eef2f7; --row-hover:#f0f7ff;
            --shadow-sm:0 1px 2px rgba(0,0,0,.04); --shadow:0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);
            --radius:10px;
            /* สีสถานะจัดส่ง (อิงหน้า billreceive) */
            --c-success:#2e7d32; --c-wrong:#c62828; --c-resend:#2853d5; --c-hold:#ed6c02;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        html,body{background:var(--page-bg);font-family:'Noto Sans Thai','Segoe UI',Tahoma,Arial,sans-serif;font-size:14px;color:var(--ink);line-height:1.5;min-height:100vh;-webkit-font-smoothing:antialiased;overflow-x:hidden}
        .page-frame{width:100%;min-height:100vh;display:flex;flex-direction:column}
        .top-banner{background:var(--canvas);border-bottom:1px solid var(--border);padding:12px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;position:sticky;top:0;z-index:100;box-shadow:var(--shadow-sm)}
        .title-group{display:flex;align-items:center;gap:8px}
        .title-group .h1{font-weight:700;font-size:18px;display:flex;align-items:center;gap:8px}
        .title-group .h1::before{content:'';display:inline-block;width:3px;height:18px;background:var(--warning);border-radius:2px}
        .sticker{background:var(--warning-light);color:var(--warning);border:1px solid #fed7aa;font-weight:600;font-size:10px;padding:3px 10px;text-transform:uppercase;letter-spacing:.5px;border-radius:12px}
        .banner-right{display:flex;align-items:center;gap:12px;flex-shrink:0}
        .user-badge{font-size:13px;font-weight:600;color:var(--on-primary);display:flex;align-items:center;gap:6px;background:var(--primary);padding:6px 14px;border-radius:16px;white-space:nowrap}
        .user-badge::before{content:'';width:7px;height:7px;background:var(--success);border-radius:50%;box-shadow:0 0 0 2px rgba(255,255,255,.3)}
        .count-block{display:flex;flex-direction:column;align-items:flex-end;line-height:1.15;background:var(--warning-light);border:1.5px solid var(--warning);border-radius:10px;padding:6px 14px;white-space:nowrap}
        .count-block .cb-label{font-size:11px;font-weight:700;color:var(--warning);letter-spacing:.3px}
        .count-block .cb-num{font-size:18px;font-weight:800;color:var(--warning);font-variant-numeric:tabular-nums}
        main{padding:16px 24px 40px;flex:1;width:100%}
        .toolbar{background:var(--canvas);border:1px solid var(--border);border-radius:8px;padding:10px 16px;margin-bottom:12px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;box-shadow:var(--shadow-sm)}
        .filter-group{display:flex;gap:8px;flex-wrap:wrap;align-items:center;flex:1}
        .toolbar input[type=search],.toolbar select{padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-family:inherit;font-size:13px;background:var(--canvas);color:var(--ink);min-width:160px}
        .toolbar input:focus,.toolbar select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 2px var(--primary-light)}
        .btn-ghost{background:var(--canvas);color:var(--muted);border:1px solid var(--border);padding:7px 16px;border-radius:6px;font-family:inherit;font-weight:600;font-size:13px;cursor:pointer}
        .btn-ghost:hover{background:#f3f4f6;color:var(--ink)}
        .tab-row{display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:12px}
        .tab-lbl{font-size:12px;font-weight:700;color:var(--muted)}
        .tab-row select{padding:7px 12px;border:1px solid var(--border);border-radius:8px;font-family:inherit;font-size:13px;font-weight:600;background:var(--canvas);color:var(--ink);cursor:pointer;min-width:150px}
        .tab-row select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 2px var(--primary-light)}
        .tabs{display:flex;gap:6px;flex-wrap:wrap}
        .tab{padding:6px 14px;border:1px solid var(--border);border-radius:999px;background:var(--canvas);color:var(--muted);font-weight:600;font-size:12.5px;cursor:pointer;transition:all .15s}
        .tab:hover{background:#f3f4f6}
        .tab.active{background:var(--primary);color:#fff;border-color:var(--primary)}
        .autocomplete-wrap{position:relative;display:inline-block}
        .suggest-panel{display:none;position:absolute;left:0;right:0;top:calc(100% + 4px);background:var(--canvas);border:1px solid var(--border);border-radius:6px;box-shadow:0 8px 24px rgba(0,0,0,.12);max-height:min(320px,45vh);overflow-y:auto;z-index:9999}
        .suggest-panel.open{display:block}
        .suggest-item{padding:9px 14px;font-size:13px;cursor:pointer;border-bottom:1px solid #f1f5f9}
        .suggest-item:last-child{border-bottom:none}
        .suggest-item:hover,.suggest-item.hl{background:#eef2ff;color:var(--primary)}
        .suggest-empty{padding:12px 14px;font-size:13px;color:#6b7280;text-align:center}
        .table-scroll{background:var(--canvas);border-radius:8px;border:1px solid var(--border);overflow:hidden;box-shadow:var(--shadow)}
        .table-inner{overflow-x:auto;-webkit-overflow-scrolling:touch;width:100%}
        table{width:100%;min-width:1040px;border-collapse:collapse;background:var(--canvas)}
        table.is-empty{min-width:0}
        th,td{border-bottom:1px solid var(--border);border-right:1px solid var(--border);padding:10px 12px;text-align:center;font-size:13px;vertical-align:middle}
        th:last-child,td:last-child{border-right:none}
        thead th{background:var(--primary);color:var(--on-primary);font-weight:700;font-size:12px;letter-spacing:.3px;border-bottom:2px solid var(--primary-dark);border-right-color:rgba(255,255,255,.2);position:sticky;top:0;z-index:10}
        tbody tr{transition:background .15s}
        tbody tr:nth-child(even){background:#fafbfd}
        tbody tr:hover{background:var(--row-hover)}
        tbody tr:last-child td{border-bottom:none}
        td.bill-cell{border-left:5px solid var(--border)}   /* สีกรอบซ้าย = ตามสถานะ (set inline) */
        .num{font-variant-numeric:tabular-nums;font-weight:500}
        .cell-left{text-align:left}
        .ref-link{font-weight:700;color:var(--primary-dark);font-size:13px}
        .reason-cell{text-align:left;max-width:240px;color:var(--danger-dark);font-size:12.5px}
        .badge{display:inline-block;padding:2px 10px;border-radius:999px;font-size:11.5px;font-weight:700;white-space:nowrap}
        .badge-wrong{background:var(--danger-light);color:var(--danger-dark)}
        .badge-hold{background:var(--warning-light);color:var(--warning)}
        .badge-open{background:var(--danger-light);color:var(--danger-dark)}
        .badge-fixed{background:#eaf0fc;color:#2853d5}
        .badge-cleared{background:var(--success-light);color:var(--success-dark)}
        .solve-info{font-size:12.5px;color:var(--muted)}
        .solve-info b{color:var(--ink)}
        .chip{display:inline-block;border-radius:6px;padding:1px 8px;font-weight:700;font-size:12px}
        .chip-blue{background:var(--primary-light);color:var(--primary-dark);border:1px solid #bfdbfe}
        .chip-green{background:var(--success-light);color:var(--success-dark);border:1px solid #bbf7d0}
        .dash{color:var(--muted)}
        .btn{padding:5px 12px;border:1px solid transparent;border-radius:6px;font-family:inherit;font-weight:600;font-size:12.5px;cursor:pointer;white-space:nowrap}
        .btn-resend{background:var(--canvas);border-color:var(--c-resend);color:var(--c-resend)}
        .btn-resend:hover{background:#eaf0fc}
        .btn-changebill{background:var(--canvas);border-color:var(--c-wrong);color:var(--c-wrong)}
        .btn-changebill:hover{background:var(--danger-light)}
        .btn-tempdoc{background:var(--canvas);border-color:var(--c-hold);color:var(--c-hold)}
        .btn-tempdoc:hover{background:var(--warning-light)}
        .btn-approve{background:#16a34a;border-color:#16a34a;color:#fff}
        .btn-approve:hover{background:#15803d}
        .btn-unapprove{background:var(--canvas);border-color:#16a34a;color:#16a34a}
        .btn-unapprove:hover{background:#dcfce7}
        .btn-block{background:#dc2626;border-color:#dc2626;color:#fff}
        .btn-block:hover{background:#b91c1c}
        .btn-undo{background:var(--canvas);border-color:var(--border);color:var(--muted)}
        .btn-undo:hover{background:#f3f4f6;color:var(--ink)}
        .actions-cell{display:flex;gap:6px;justify-content:center;flex-wrap:wrap}
        /* แท็บ 3 หมวด: บิลผิด / ค้างบิล / แก้ไขแล้ว */
        .cat-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px}
        .cat-tab{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border:1px solid var(--border);background:#fff;border-radius:999px;cursor:pointer;font-family:inherit;font-size:14px;font-weight:600;color:var(--muted);transition:all .15s}
        .cat-tab:hover{background:#f8fafc}
        .cat-tab .cat-dot{width:9px;height:9px;border-radius:50%}
        .cat-tab[data-tab=wrong] .cat-dot{background:#dc2626}
        .cat-tab[data-tab=hold] .cat-dot{background:#d97706}
        .cat-tab[data-tab=done] .cat-dot{background:#16a34a}
        .cat-tab.active{color:#fff;border-color:transparent}
        .cat-tab[data-tab=wrong].active{background:#dc2626}
        .cat-tab[data-tab=hold].active{background:#d97706}
        .cat-tab[data-tab=done].active{background:#16a34a}
        .cat-tab.active .cat-dot{background:#fff}
        .cat-tab .cat-cnt{font-size:12px;font-weight:700;background:rgba(0,0,0,.08);padding:1px 8px;border-radius:999px;min-width:20px;text-align:center}
        .cat-tab.active .cat-cnt{background:rgba(255,255,255,.25)}
        /* ปุ่มจัดการของผิด */
        .btn-dismiss{background:#fff;border-color:#9ca3af;color:#374151}
        .btn-dismiss:hover{background:#f3f4f6}
        .gate-box{display:flex;flex-direction:column;gap:5px;align-items:stretch;min-width:150px}
        .empty-wrapper{display:flex;align-items:center;justify-content:center;height:440px;width:100%}
        .empty-state{text-align:center;color:var(--muted);font-size:15px;font-style:italic}
        .loading-state{display:flex;flex-direction:column;align-items:center;gap:10px;width:min(300px,80%)}
        .progress-track{width:100%;height:9px;background:var(--primary-light);border-radius:999px;overflow:hidden}
        .progress-fill{height:100%;width:0;background:var(--primary);border-radius:999px;transition:width .18s}
        .progress-label{color:var(--muted);font-size:14px;font-variant-numeric:tabular-nums}
        tbody tr td[colspan]{padding:0}
        .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:200;align-items:center;justify-content:center;backdrop-filter:blur(2px)}
        .modal-box{background:#fff;border-radius:12px;padding:22px;width:min(92vw,440px);box-shadow:0 10px 40px rgba(0,0,0,.2);animation:modalPop .18s ease}
        @keyframes modalPop{from{opacity:0;transform:translateY(10px) scale(.96)}to{opacity:1;transform:translateY(0) scale(1)}}
        .modal-head{display:flex;align-items:flex-start;gap:12px;margin-bottom:16px}
        .modal-icon{width:40px;height:40px;border-radius:10px;flex-shrink:0;background:var(--primary-light);color:var(--primary-dark);display:flex;align-items:center;justify-content:center;font-size:19px}
        .modal-title{font-weight:700;font-size:16px;line-height:1.3}
        .modal-sub{margin-top:3px;font-size:12.5px;color:var(--muted);font-weight:600}
        .modal-label{display:block;font-size:12.5px;font-weight:700;color:var(--muted);margin-bottom:6px}
        .modal-box input{width:100%;padding:11px 12px;border:1px solid var(--border);border-radius:8px;font-family:inherit;font-size:14px;margin-bottom:6px}
        .modal-box input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
        .modal-hint{font-size:12px;color:var(--muted);margin-bottom:14px}
        .modal-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:8px}
        .btn-primary{padding:9px 18px;border:none;border-radius:8px;cursor:pointer;font-family:inherit;font-size:14px;font-weight:600;background:var(--primary);color:#fff}
        .btn-primary:hover{filter:brightness(.95)}
        /* ส่งใหม่ (ให้ UI เหมือนหน้า billreceive) */
        .redo-opt{display:flex;gap:10px;align-items:flex-start;border:1px solid var(--border);border-radius:10px;padding:12px 14px;margin-bottom:10px;cursor:pointer}
        .redo-opt:hover{border-color:var(--primary)}
        .redo-opt.active{border-color:var(--primary);background:var(--primary-light)}
        .redo-opt input{margin-top:3px;width:16px;height:16px;cursor:pointer;flex-shrink:0}
        .redo-opt b{display:block;font-size:14px;color:var(--ink)}
        .redo-opt span{font-size:12.5px;color:var(--muted)}
        .redo-fields{display:none;padding:4px 2px 6px}
        .redo-fields.open{display:block}
        .redo-fields label{display:block;font-size:12.5px;font-weight:700;color:var(--muted);margin:10px 0 6px}
        .redo-fields input,.redo-fields select{width:100%;height:40px;padding:0 12px;border:1px solid var(--border);border-radius:8px;font-family:inherit;font-size:14px;color:var(--ink);background:#fff;margin-bottom:0}
        .redo-fields input:focus,.redo-fields select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
        @media (max-width:768px){
            .top-banner{padding:10px 16px} main{padding:14px 14px 32px}
            .toolbar input[type=search],.toolbar select{min-width:100%;flex:1 1 100%}
            .title-group .h1{font-size:16px} .empty-wrapper{height:300px}
        }
    </style>
</head>
<body>
<div class="page-frame">
    <div class="top-banner">
        <div class="title-group">
            <span class="h1">แก้ของผิด / ค้างบิล</span>
            <span class="sticker">สินค้าผิด + ค้างบิล</span>
        </div>
        <div class="banner-right">
            <div class="count-block">
                <span class="cb-label">ค้างแก้</span>
                <span class="cb-num" id="openCount">0</span>
            </div>
            <div class="user-badge">ผู้ใช้งาน: {{ $creator }}</div>
        </div>
    </div>

    <main>
        <div class="toolbar">
            <div class="filter-group">
                @if($seeAll ?? false)
                <div class="autocomplete-wrap">
                    <input type="search" id="fSale" placeholder=" ค้นหาโดย Sale..." autocomplete="off">
                    <div id="saleSuggest" class="suggest-panel"></div>
                </div>
                @endif
                <input type="search" id="fCust" placeholder=" ค้นหาลูกค้า (รหัส/ชื่อ)..." autocomplete="off">
                <input type="search" id="fBill" placeholder=" ค้นหาเลขบิล..." autocomplete="off">
                <button type="button" class="btn-ghost" id="btnClear">ล้าง</button>
            </div>
        </div>

        <div class="cat-tabs" id="catTabs">
            <button type="button" class="cat-tab active" data-tab="wrong" id="tab-wrong" name="tab-wrong">
                <span class="cat-dot"></span>บิลผิด<span class="cat-cnt" id="cnt-wrong">0</span>
            </button>
            <button type="button" class="cat-tab" data-tab="hold" id="tab-hold" name="tab-hold">
                <span class="cat-dot"></span>ค้างบิล<span class="cat-cnt" id="cnt-hold">0</span>
            </button>
            <button type="button" class="cat-tab" data-tab="done" id="tab-done" name="tab-done">
                <span class="cat-dot"></span>แก้ไขแล้ว<span class="cat-cnt" id="cnt-done">0</span>
            </button>
        </div>

        <div class="table-scroll">
            <div class="table-inner">
                @php $colspan = $canSolve ? 6 : 5; @endphp
                <table id="mainTable" class="is-empty">
                    <thead>
                        <tr id="headRow">
                            <th>เลขบิล / SO</th>
                            <th style="text-align:left;">ลูกค้า</th>
                            <th>ผู้เปิดบิล</th>
                            <th>ปัญหา</th>
                            <th>สถานะ</th>
                            @if($canSolve)<th>จัดการ</th>@endif
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="{{ $colspan }}"><div class="empty-wrapper"><div class="empty-state">กำลังโหลด...</div></div></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

@if($canSolve)
<!-- Modal ใส่เลขปลายทาง (เปลี่ยนบิล / เอกสารชั่วคราว) -->
<div id="targetModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-icon" id="tmIcon">＋</div>
            <div>
                <div class="modal-title" id="tmTitle">เปลี่ยนเลขบิล</div>
                <div class="modal-sub" id="tmBillLabel"></div>
            </div>
        </div>
        <label class="modal-label" id="tmLabel" for="tmInput">เลขบิลใหม่</label>
        <input type="search" id="tmInput" placeholder="กรอกเลข..." autocomplete="off">
        <div class="modal-hint" id="tmHint"></div>
        <div class="modal-actions">
            <button type="button" class="btn-ghost" onclick="closeTarget()">ยกเลิก</button>
            <button type="button" class="btn-primary" id="tmConfirm" onclick="confirmTarget()">ยืนยัน</button>
        </div>
    </div>
</div>

<!-- Modal ส่งใหม่เลขบิลเดิม : จ่ายใหม่ที่นี่เลย (assign) หรือ คืนไปหน้าจ่ายงานขนส่ง (return) -->
<div id="redoModal" class="modal-overlay">
    <div class="modal-box" style="max-width:460px;position:relative;">
        <button type="button" aria-label="ปิด" onclick="closeRedo()"
            style="position:absolute;top:14px;right:14px;width:32px;height:32px;border:none;background:#f1f5f9;color:#475569;border-radius:8px;font-size:18px;line-height:1;cursor:pointer;">&times;</button>
        <div class="modal-title">ส่งใหม่เลขบิลเดิม</div>
        <div class="modal-sub" id="redoLabel" style="margin:6px 0 18px;"></div>

        <label class="redo-opt active" id="redoOptAssign">
            <input type="radio" name="redoMode" value="assign" checked>
            <div><b>เลือกเองเลย</b><span>กำหนดผู้รับผิดชอบ วิธีการจัดส่ง และวันที่ไปส่งที่นี่</span></div>
        </label>
        <div class="redo-fields open" id="redoFields">
            <label for="redoDriver">ผู้รับผิดชอบ</label>
            <input type="text" id="redoDriver" list="redoDriverList" placeholder="เลือกหรือพิมพ์ชื่อ (เว้นว่างได้)" autocomplete="off">
            <datalist id="redoDriverList"></datalist>
            <label for="redoTransport">วิธีการจัดส่ง</label>
            <select id="redoTransport"></select>
            <label for="redoDate">วันที่ไปส่ง</label>
            <input type="date" id="redoDate">
        </div>

        <label class="redo-opt" id="redoOptReturn">
            <input type="radio" name="redoMode" value="return">
            <div><b>กลับไปเลือกใหม่ที่หน้าจ่ายงานขนส่ง</b><span>คืนงานไปหน้าจ่ายงาน แล้วค่อยเลือกคนขับ/วันที่ที่นั่น</span></div>
        </label>

        <div class="modal-actions">
            <button type="button" class="btn-ghost" onclick="closeRedo()">ยกเลิก</button>
            <button type="button" class="btn-primary" id="redoConfirmBtn" onclick="confirmRedo()">ยืนยันส่งใหม่</button>
        </div>
    </div>
</div>
@endif

<script>
    const DATA_URL  = "{{ route('wrongbill.data') }}";
    @if($canSolve)
    const SOLVE_URL = "{{ route('wrongbill.solve') }}";
    const APPROVE_URL = "{{ route('wrongbill.approve') }}";
    const DISMISS_URL = "{{ route('wrongbill.dismiss') }}";
    @endif
    const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
    const CAN_SOLVE = {{ ($canSolve ?? false) ? 'true' : 'false' }};
    const SEE_ALL   = {{ ($seeAll ?? false) ? 'true' : 'false' }};
    // แท็บ "แก้ไขแล้ว" (done) ซ่อนคอลัมน์ สถานะ + จัดการ
    function isDoneView(){ return currentStatus === 'done'; }
    function currentColspan(){
        let n = 4;                               // เลขบิล/SO, ลูกค้า, ผู้เปิดบิล, ปัญหา
        if (!isDoneView()) n += 1;               // สถานะ
        if (CAN_SOLVE && !isDoneView()) n += 1;  // จัดการ
        return n;
    }
    function renderHead(){
        const done = isDoneView();
        let h = '<th>เลขบิล / SO</th><th style="text-align:left;">ลูกค้า</th><th>ผู้เปิดบิล</th><th>ปัญหา</th>';
        if (!done) h += '<th>สถานะ</th>';
        if (CAN_SOLVE && !done) h += '<th>จัดการ</th>';
        const hr = document.getElementById('headRow'); if (hr) hr.innerHTML = h;
    }
    const SALE_OPTIONS = @json($saleOptions ?? []);
    const DELIVERY_METHODS    = @json($deliveryMethods ?? []);
    const RESPONSIBLE_PERSONS = @json($responsiblePersons ?? []);

    const tbody     = document.getElementById('tableBody');
    const mainTable = document.getElementById('mainTable');
    const fSale = document.getElementById('fSale');   // อาจไม่มี (role sale)
    const fCust = document.getElementById('fCust');
    const fBill = document.getElementById('fBill');
    const btnClear = document.getElementById('btnClear');
    const openCountEl = document.getElementById('openCount');

    let currentType = 'wrong';     // แท็บเริ่มต้น: บิลผิด
    let currentStatus = 'open';
    let currentKind = 'all';       // คงไว้ (ส่งค่า all เสมอ)

    function esc(s){ return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
    function escJs(s){ return String(s ?? '').replace(/\\/g,'\\\\').replace(/'/g,"\\'"); }
    function soLink(so){
        if (!so || so === '-') return '<span class="dash">-</span>';
        return '<a class="ref-link" href="http://server_update:8000/sodetail?SONum=' + encodeURIComponent(so) + '" target="_blank" rel="noopener">SO ' + esc(so) + '</a>';
    }

    function attachSuggest(input, panel, options, onPick){
        if (!input || !panel) return;
        let hl = -1;
        function render(){
            const q = (input.value||'').trim().toLowerCase();
            const matches = (q ? options.filter(s => String(s).toLowerCase().includes(q)) : options).slice(0,40);
            hl = -1;
            if (!matches.length){ panel.innerHTML = '<div class="suggest-empty">ไม่พบตัวเลือก</div>'; panel.classList.add('open'); return; }
            panel.innerHTML = matches.map(s => '<div class="suggest-item" data-val="'+String(s).replace(/"/g,'&quot;')+'">'+esc(s)+'</div>').join('');
            panel.classList.add('open');
        }
        input.addEventListener('focus', render);
        input.addEventListener('input', render);
        panel.addEventListener('mousedown', e => {
            const it = e.target.closest('.suggest-item'); if(!it) return;
            e.preventDefault(); input.value = it.dataset.val; panel.classList.remove('open'); input.focus(); if(onPick) onPick();
        });
        input.addEventListener('blur', () => setTimeout(()=>panel.classList.remove('open'),120));
    }
    if (fSale) attachSuggest(fSale, document.getElementById('saleSuggest'), SALE_OPTIONS, () => search());

    function setMsg(text){ if(mainTable) mainTable.classList.add('is-empty'); tbody.innerHTML = '<tr><td colspan="'+currentColspan()+'"><div class="empty-wrapper"><div class="empty-state">'+esc(text)+'</div></div></td></tr>'; }
    function setLoading(text){ if(mainTable) mainTable.classList.add('is-empty'); tbody.innerHTML = '<tr><td colspan="'+currentColspan()+'"><div class="empty-wrapper"><div class="loading-state"><div class="progress-track"><div class="progress-fill" id="progressFill"></div></div><div class="progress-label" id="progressLabel">'+esc(text)+' 0%</div></div></div></td></tr>'; }
    let progressTimer=null;
    function startProgress(text){ if(progressTimer)clearInterval(progressTimer); let pct=0; progressTimer=setInterval(()=>{ pct+=(90-pct)*0.15+0.6; if(pct>90)pct=90; const f=document.getElementById('progressFill'),l=document.getElementById('progressLabel'); if(f)f.style.width=pct.toFixed(0)+'%'; if(l)l.textContent=text+' '+pct.toFixed(0)+'%'; },120); }
    function stopProgress(){ if(progressTimer){clearInterval(progressTimer);progressTimer=null;} }

    function problemBadge(r){
        if (r.problem === 'ค้างบิล') return '<span class="badge badge-hold">ค้างบิล</span>';
        return '<span class="badge badge-wrong">สินค้าผิด</span>';
    }
    function stateBadge(r){
        if (r.state === 'cleared') return '<span class="badge badge-cleared">เคลียร์แล้ว</span>';
        if (r.state === 'fixed')   return '<span class="badge badge-fixed">แก้แล้ว · รอผล</span>';
        return '<span class="badge badge-open">ยังไม่แก้</span>';
    }
    function methodLabel(r){
        if (r.solve_method === 'resend')     return 'ส่งใหม่ (เลขบิลเดิม)';
        if (r.solve_method === 'changebill') return 'เปลี่ยนเป็นบิล <span class="chip chip-blue">'+esc(r.solve_target)+'</span>';
        if (r.solve_method === 'tempdoc')    return 'เอกสารชั่วคราว <span class="chip chip-blue">'+esc(r.solve_target)+'</span>';
        if (r.solve_method === 'dismiss')    return 'เคลียร์ข้อมูล';
        return '';
    }
    function solveCell(r){
        if (!r.solve_method) return '<span class="dash">— ยังไม่แก้ —</span>';
        let h = '<div><b>'+methodLabel(r)+'</b>';
        if (r.state === 'cleared') h += ' <span class="chip chip-green">สำเร็จ</span>';
        h += '</div>';
        if (r.solve_by)  h += '<div class="solve-info">โดย: <b>'+esc(r.solve_by)+'</b></div>';
        if (r.solve_at)  h += '<div class="solve-info">เมื่อ: '+esc(r.solve_at)+'</div>';
        return h;
    }
    // กล่องบล็อก/อนุมัติ ราย SO (admin เท่านั้น + ต้องมี so) — บล็อกจับตาม so
    //   บล็อก (approved=0) -> ฝั่ง server_update so/show ปุ่มบันทึกข้อมูลจัดส่งกลายเป็น "กรุณาติดต่อผู้ดูแลระบบของผิด"
    //   อนุมัติ (approved=1) หรือยังไม่ตั้งค่า -> จัดส่งได้
    //   data-act ไว้ให้ bot กดอัตโนมัติ: block | unblock
    function approveHtml(r){
        if (!r.can_manage || !r.has_so) return '';          // ไม่มี so = ไม่มีบล็อก (เตะออกอย่างเดียว)
        const so = escJs(r.so_id);
        if (r.blocked){
            return '<div class="solve-info" style="color:#dc2626">บล็อกจัดส่ง'+(r.approved_by?' · โดย '+esc(r.approved_by):'')+(r.approved_at?' · '+esc(r.approved_at):'')+'</div>'
                 + '<button type="button" class="btn btn-approve" data-act="unblock" data-so="'+esc(r.so_id)+'" onclick="toggleApprove(this,\''+so+'\',true)">อนุมัติให้จัดส่ง</button>';
        }
        if (r.approved){
            return '<div class="solve-info" style="color:#16a34a">อนุมัติแล้ว'+(r.approved_by?' · โดย '+esc(r.approved_by):'')+(r.approved_at?' · '+esc(r.approved_at):'')+'</div>'
                 + '<button type="button" class="btn btn-block" data-act="block" data-so="'+esc(r.so_id)+'" onclick="toggleApprove(this,\''+so+'\',false)">บล็อกการจัดส่ง</button>';
        }
        return '<div class="solve-info">จัดส่งได้ (ยังไม่ตั้งค่า)</div>'
             + '<button type="button" class="btn btn-block" data-act="block" data-so="'+esc(r.so_id)+'" onclick="toggleApprove(this,\''+so+'\',false)">บล็อกการจัดส่ง</button>';
    }
    // คอลัมน์จัดการ (admin เท่านั้น) : บล็อก/ปลดบล็อก (ถ้ามี so) + ปุ่มแก้ + เตะออกจากของผิด
    function actionsCell(r){
        if (!CAN_SOLVE) return '';                           // ไม่ใช่ admin -> ไม่มีคอลัมน์จัดการ
        let btns = '';
        if (r.state === 'open'){
            // ส่งใหม่เลขบิลเดิม (บิล + เอกสารชั่วคราว SP รวมที่ไม่ได้เชื่อม SO) — เลือก assign/return เหมือนหน้า billreceive
            if (r.type === 'bill' || r.type === 'doc'){
                btns += '<button type="button" class="btn btn-resend" data-act="resend" data-job="'+esc(r.job_key)+'" onclick="openRedo(\''+escJs(r.job_key)+'\',\''+escJs(r.bill_no)+'\',\''+escJs(r.driver||'')+'\')">ส่งใหม่เลขบิลเดิม</button>';
            }
            if (r.problem === 'ค้างบิล'){
                // ค้างบิล: เปิดเอกสารชั่วคราว (เขียนเลขแล้วหายเอง) — ไม่มีบล็อก
                btns += '<button type="button" class="btn btn-tempdoc" data-act="tempdoc" data-job="'+esc(r.job_key)+'" onclick="openTarget(\'tempdoc\',\''+escJs(r.job_key)+'\',\''+escJs(r.bill_no)+'\')">เปิดเอกสารชั่วคราว</button>';
            }
            // เคลียร์ข้อมูล (เดิม "เตะออก") — ได้ทั้งของผิด/ค้างบิล
            btns += '<button type="button" class="btn btn-dismiss" data-act="dismiss" data-job="'+esc(r.job_key)+'" onclick="doDismiss(this,\''+escJs(r.job_key)+'\')">เคลียร์ข้อมูล</button>';
        } else {
            btns = '<span class="dash">—</span>';
        }
        // บล็อก/ปลดบล็อก: เฉพาะ "สินค้าผิด" (ค้างบิลไม่มีบล็อก)
        const gate = (r.state === 'open' && r.problem === 'สินค้าผิด') ? approveHtml(r) : '';
        return '<td><div class="gate-box">'+gate+'<div class="actions-cell">'+btns+'</div></div></td>';
    }
    async function toggleApprove(btn, soId, val){
        btn.disabled = true;
        try{
            const res = await fetch(APPROVE_URL, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body:JSON.stringify({ so_id:soId, approved:val }) });
            const d = await res.json().catch(()=>null);
            if (!res.ok || !d || !d.ok){ alert((d&&d.message)||'ไม่สำเร็จ'); btn.disabled=false; return; }
            search();   // รีโหลดให้ปุ่ม/สถานะอัปเดต
        }catch(e){ alert('ผิดพลาด: '+e.message); btn.disabled=false; }
    }
    async function doDismiss(btn, jobKey){
        if (!confirm('เคลียร์ข้อมูลงานนี้?\nจะย้ายไปหมวด "แก้ไขแล้ว" และปลดบล็อก SO ให้จัดส่งได้')) return;
        btn.disabled = true;
        try{
            const res = await fetch(DISMISS_URL, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body:JSON.stringify({ job_key:jobKey }) });
            const d = await res.json().catch(()=>null);
            if (!res.ok || !d || !d.ok){ alert((d&&d.message)||'ไม่สำเร็จ'); btn.disabled=false; return; }
            search();
        }catch(e){ alert('ผิดพลาด: '+e.message); btn.disabled=false; }
    }
    function rowHtml(r){
        const cust = '<div><b>'+esc(r.customer_name||'-')+'</b></div>'+(r.customer_code?'<div class="solve-info">'+esc(r.customer_code)+'</div>':'');
        let prob = problemBadge(r)
            + (r.reason?'<div class="reason-cell">'+esc(r.reason)+'</div>':'')
            + (r.wrong_by?'<div class="solve-info">โดย '+esc(r.wrong_by)+(r.wrong_time?' · '+esc(r.wrong_time):'')+'</div>':'')
            + (r.driver?'<div class="solve-info">คนขับ: '+esc(r.driver)+'</div>':'');
        // แท็บแก้ไขแล้ว: แสดงวิธีแก้ + ใครเคลียร์/แก้ + เมื่อไหร่ ใต้ปัญหา
        if (isDoneView() && r.solve_method){
            prob += '<div class="solve-info" style="margin-top:5px;color:var(--success-dark);font-weight:600;">'+methodLabel(r)
                + (r.solve_by?' · โดย <b>'+esc(r.solve_by)+'</b>':'')
                + (r.solve_at?' · '+esc(r.solve_at):'')+'</div>';
        }
        let html = '<tr>'
            + '<td class="num bill-cell" style="border-left-color:'+esc(r.border||'#dcdcdc')+';"><div class="ref-link">'+esc(r.bill_no||'-')+'</div>'+(r.so_id?'<div>'+soLink(r.so_id)+'</div>':'')+'</td>'
            + '<td class="cell-left">'+cust+'</td>'
            + '<td>'+(r.emp_name?esc(r.emp_name):'<span class="dash">-</span>')+'</td>'
            + '<td>'+prob+'</td>';
        // แท็บ "แก้ไขแล้ว" (done) ไม่แสดงคอลัมน์ สถานะ + จัดการ
        if (!isDoneView()){
            html += '<td>'+stateBadge(r)+'</td>' + actionsCell(r);
        }
        html += '</tr>';
        return html;
    }

    async function search(){
        renderHead();   // อัปเดตหัวตารางตามแท็บ (ซ่อน สถานะ/จัดการ ในแท็บแก้ไขแล้ว)
        const params = new URLSearchParams();
        if (fSale) params.set('sale', fSale.value.trim());
        params.set('customer', fCust.value.trim());
        params.set('bill', fBill.value.trim());
        params.set('type', currentType);
        params.set('status', currentStatus);
        params.set('kind', currentKind);

        setLoading('กำลังค้นหา...'); startProgress('กำลังค้นหา...');
        try{
            const res = await fetch(DATA_URL+'?'+params.toString(), { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} });
            const data = await res.json(); stopProgress();
            if (!res.ok || !data.ok){ setMsg((data&&data.message)||'ค้นหาไม่สำเร็จ'); return; }
            const rows = data.rows || [];
            if (data.counts){
                document.getElementById('cnt-wrong').textContent = data.counts.wrong;
                document.getElementById('cnt-hold').textContent  = data.counts.hold;
                const cntDoneEl = document.getElementById('cnt-done');
                if (cntDoneEl && data.counts.done !== undefined) cntDoneEl.textContent = data.counts.done;
                openCountEl.textContent = data.counts.wrong + data.counts.hold;
            } else {
                openCountEl.textContent = rows.filter(r => r.state === 'open').length;
            }
            if (rows.length === 0){ setMsg('ไม่พบงานตามเงื่อนไข'); return; }
            if (mainTable) mainTable.classList.remove('is-empty');
            tbody.innerHTML = rows.map(rowHtml).join('');
        }catch(e){ console.error(e); stopProgress(); setMsg('เกิดข้อผิดพลาดในการเชื่อมต่อ'); }
    }

    let searchDebounce=null;
    function scheduleSearch(delay=450){ if(searchDebounce)clearTimeout(searchDebounce); searchDebounce=setTimeout(()=>search(),delay); }

    // แท็บ 3 หมวด -> กำหนด type/status
    const TAB_MAP = {
        wrong: { type:'wrong', status:'open' },
        hold:  { type:'hold',  status:'open' },
        done:  { type:'all',   status:'done' },
    };
    document.querySelectorAll('#catTabs .cat-tab').forEach(t => t.addEventListener('click', () => {
        document.querySelectorAll('#catTabs .cat-tab').forEach(x => x.classList.remove('active'));
        t.classList.add('active');
        const m = TAB_MAP[t.dataset.tab] || TAB_MAP.wrong;
        currentType = m.type; currentStatus = m.status;
        search();
    }));
    btnClear.addEventListener('click', () => {
        if(searchDebounce)clearTimeout(searchDebounce);
        if (fSale) fSale.value=''; fCust.value=''; fBill.value='';
        search();
    });
    [fCust,fBill].forEach(el => el.addEventListener('input', () => scheduleSearch()));
    if (fSale) fSale.addEventListener('input', () => scheduleSearch());
    [fSale,fCust,fBill].forEach(el => { if(el) el.addEventListener('keydown', e => { if(e.key==='Enter'){ if(searchDebounce)clearTimeout(searchDebounce); search(); } }); });

    @if($canSolve)
    async function postSolve(payload, reload=true){
        try{
            const res = await fetch(SOLVE_URL, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body:JSON.stringify(payload) });
            const data = await res.json().catch(()=>null);
            if (!res.ok || !data || !data.ok){ alert((data&&data.message)||'บันทึกไม่สำเร็จ'); return false; }
            if (data.message) alert(data.message);
            if (reload) search();
            return data;
        }catch(e){ console.error(e); alert('เกิดข้อผิดพลาดในการเชื่อมต่อ'); return false; }
    }
    // ส่งใหม่เลขบิลเดิม -> เปิด modal เลือก assign (จ่ายที่นี่เลย) / return (คืนไปหน้าจ่ายงาน)
    let redoJob = null;
    function syncRedoMode(){
        const m = (document.querySelector('input[name="redoMode"]:checked')||{}).value || 'assign';
        document.getElementById('redoOptAssign').classList.toggle('active', m==='assign');
        document.getElementById('redoOptReturn').classList.toggle('active', m==='return');
        document.getElementById('redoFields').classList.toggle('open', m==='assign');
    }
    function openRedo(jobKey, billNo, driver){
        redoJob = jobKey;
        document.getElementById('redoLabel').textContent = 'บิล ' + billNo + (driver ? ' · คนขับเดิม ' + driver : '');
        document.getElementById('redoDriverList').innerHTML = RESPONSIBLE_PERSONS.map(o => '<option value="'+esc(o)+'">').join('');
        document.getElementById('redoDriver').value = driver || '';
        document.getElementById('redoTransport').innerHTML = '<option value="">— เลือกวิธีการจัดส่ง —</option>'
            + DELIVERY_METHODS.map(o => '<option value="'+esc(o)+'">'+esc(o)+'</option>').join('');
        const d = new Date(); d.setDate(d.getDate()+1);   // ค่าเริ่มต้น = พรุ่งนี้
        document.getElementById('redoDate').value = d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
        const assignRadio = document.querySelector('input[name="redoMode"][value="assign"]');
        if (assignRadio) assignRadio.checked = true;
        syncRedoMode();
        document.getElementById('redoModal').style.display = 'flex';
    }
    function closeRedo(){ const m=document.getElementById('redoModal'); if(m)m.style.display='none'; redoJob=null; }
    async function confirmRedo(){
        if (!redoJob) return;
        const mode = (document.querySelector('input[name="redoMode"]:checked')||{}).value || 'assign';
        const payload = { job_key:redoJob, mode:'resend', redo_mode:mode };
        if (mode === 'assign'){
            const driver    = document.getElementById('redoDriver').value.trim();
            const transport = document.getElementById('redoTransport').value.trim();
            const date      = document.getElementById('redoDate').value;
            if (!transport){ alert('กรุณาเลือกวิธีการจัดส่ง'); return; }
            if (!date){ alert('กรุณาเลือกวันที่ไปส่ง'); return; }
            if (transport === 'เซลล์ไปส่งเอง' && !driver){ alert('เลือก "เซลล์ไปส่งเอง" กรุณาระบุชื่อเซลล์ที่ไปส่งเอง'); return; }
            if (transport !== 'เซลล์ไปส่งเอง' && driver && RESPONSIBLE_PERSONS.indexOf(driver) === -1){ alert('กรุณาเลือกผู้รับผิดชอบจากรายการที่มีให้'); return; }
            Object.assign(payload, { redo_driver:driver, redo_transport:transport, redo_date:date });
        }
        const btn = document.getElementById('redoConfirmBtn'); btn.disabled = true;
        const data = await postSolve(payload, false);
        btn.disabled = false;
        if (data){ closeRedo(); search(); }
    }
    document.querySelectorAll('input[name="redoMode"]').forEach(r => r.addEventListener('change', syncRedoMode));
    document.getElementById('redoModal').addEventListener('click', function(e){ if(e.target===this) closeRedo(); });
    async function doUndo(jobKey){
        if (!confirm('ยกเลิกการแก้ไข และกลับไปสถานะ "ยังไม่แก้" ?')) return;
        await postSolve({ job_key:jobKey, mode:'clear' });
    }
    // modal เปลี่ยนบิล / เอกสารชั่วคราว
    let tmMode = null, tmJob = null;
    function openTarget(mode, jobKey, billNo){
        tmMode = mode; tmJob = jobKey;
        const isDoc = mode === 'tempdoc';
        document.getElementById('tmTitle').textContent   = isDoc ? 'เปิดเอกสารชั่วคราว (ค้างบิล)' : 'เปลี่ยนเลขบิล (ของผิด)';
        document.getElementById('tmBillLabel').textContent = 'งานเดิม: ' + billNo;
        document.getElementById('tmLabel').textContent   = isDoc ? 'เลขเอกสารชั่วคราว' : 'เลขบิลใหม่';
        document.getElementById('tmHint').textContent    = isDoc
            ? 'เปิดเอกสารชั่วคราวไปรับของกลับบริษัท — จะเคลียร์อัตโนมัติเมื่อเอกสารนี้ "จัดส่งสำเร็จ"'
            : 'ผูกเลขบิลใหม่ที่จะไปส่งแทน — จะเคลียร์อัตโนมัติเมื่อบิลใหม่ "จัดส่งสำเร็จ"';
        const inp = document.getElementById('tmInput'); inp.value=''; inp.placeholder = isDoc ? 'กรอกเลขเอกสารชั่วคราว...' : 'กรอกเลขบิลใหม่...';
        document.getElementById('targetModal').style.display = 'flex';
        setTimeout(()=>inp.focus(), 50);
    }
    function closeTarget(){ const m=document.getElementById('targetModal'); if(m)m.style.display='none'; tmMode=null; tmJob=null; }
    async function confirmTarget(){
        if (!tmJob || !tmMode) return;
        const target = document.getElementById('tmInput').value.trim();
        if (!target){ alert('กรุณากรอกเลข'); return; }
        const btn = document.getElementById('tmConfirm'); btn.disabled=true; btn.textContent='กำลังบันทึก...';
        const data = await postSolve({ job_key:tmJob, mode:tmMode, target:target }, false);
        btn.disabled=false; btn.textContent='ยืนยัน';
        if (data){ closeTarget(); search(); }
    }
    document.getElementById('targetModal').addEventListener('click', function(e){ if(e.target===this) closeTarget(); });
    document.addEventListener('keydown', e => { if(e.key==='Escape'){ closeTarget(); closeRedo(); } });
    window.openRedo=openRedo; window.closeRedo=closeRedo; window.confirmRedo=confirmRedo; window.doUndo=doUndo; window.openTarget=openTarget; window.closeTarget=closeTarget; window.confirmTarget=confirmTarget;
    @endif

    search();   // โหลดทันที (sale = ของตัวเอง, อื่น ๆ = ทั้งหมด)
</script>
</body>
</html>