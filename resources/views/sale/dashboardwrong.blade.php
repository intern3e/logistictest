<!DOCTYPE html>
{{-- resources/views/sale/dashboardwrong.blade.php — ระบบแก้ของผิด (สินค้าผิด) สำหรับ Sale --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>แก้ของผิด (สินค้าผิด)</title>
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
        /* tabs สถานะ */
        .tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px}
        .tab{padding:7px 16px;border:1px solid var(--border);border-radius:999px;background:var(--canvas);color:var(--muted);font-weight:600;font-size:13px;cursor:pointer;transition:all .15s}
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
        table{width:100%;min-width:1000px;border-collapse:collapse;background:var(--canvas)}
        table.is-empty{min-width:0}
        th,td{border-bottom:1px solid var(--border);border-right:1px solid var(--border);padding:10px 12px;text-align:center;font-size:13px;vertical-align:middle}
        th:last-child,td:last-child{border-right:none}
        thead th{background:var(--primary);color:var(--on-primary);font-weight:700;font-size:12px;letter-spacing:.3px;border-bottom:2px solid var(--primary-dark);border-right-color:rgba(255,255,255,.2);position:sticky;top:0;z-index:10}
        tbody tr{transition:background .15s}
        tbody tr:nth-child(even){background:#fafbfd}
        tbody tr:hover{background:var(--row-hover)}
        tbody tr:last-child td{border-bottom:none}
        .num{font-variant-numeric:tabular-nums;font-weight:500}
        .cell-left{text-align:left}
        .ref-link{font-weight:700;color:var(--primary-dark);font-size:13px}
        .reason-cell{text-align:left;max-width:260px;color:var(--danger-dark)}
        .badge{display:inline-block;padding:2px 10px;border-radius:999px;font-size:11.5px;font-weight:700;white-space:nowrap}
        .badge-pending{background:var(--danger-light);color:var(--danger-dark)}
        .badge-tracking{background:var(--warning-light);color:var(--warning)}
        .badge-cleared{background:var(--success-light);color:var(--success-dark)}
        .badge-wrong{background:var(--danger-light);color:var(--danger-dark)}
        .badge-hold{background:#e3f0ff;color:#1e5bb8}
        .solve-info{font-size:12.5px;color:var(--muted)}
        .solve-info b{color:var(--ink)}
        .newbill-chip{display:inline-block;background:var(--primary-light);color:var(--primary-dark);border:1px solid #bfdbfe;border-radius:6px;padding:1px 8px;font-weight:700;font-size:12px}
        .btn{padding:5px 12px;border:1px solid transparent;border-radius:6px;font-family:inherit;font-weight:600;font-size:12.5px;cursor:pointer;white-space:nowrap}
        .btn-stock{background:var(--canvas);border-color:var(--success);color:var(--success-dark)}
        .btn-stock:hover{background:var(--success-light)}
        .btn-newbill{background:var(--canvas);border-color:var(--primary);color:var(--primary-dark)}
        .btn-newbill:hover{background:var(--primary-light)}
        .btn-undo{background:var(--canvas);border-color:var(--border);color:var(--muted)}
        .btn-undo:hover{background:#f3f4f6;color:var(--ink)}
        .actions-cell{display:flex;gap:6px;justify-content:center;flex-wrap:wrap}
        .dash{color:var(--muted)}
        /* empty / loading */
        .empty-wrapper{display:flex;align-items:center;justify-content:center;height:440px;width:100%}
        .empty-state{text-align:center;color:var(--muted);font-size:15px;font-style:italic}
        .loading-state{display:flex;flex-direction:column;align-items:center;gap:10px;width:min(300px,80%)}
        .progress-track{width:100%;height:9px;background:var(--primary-light);border-radius:999px;overflow:hidden}
        .progress-fill{height:100%;width:0;background:var(--primary);border-radius:999px;transition:width .18s}
        .progress-label{color:var(--muted);font-size:14px;font-variant-numeric:tabular-nums}
        tbody tr td[colspan]{padding:0}
        /* modal */
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
                <div class="autocomplete-wrap">
                    <input type="search" id="fSale" placeholder=" ค้นหาโดย Sale..." autocomplete="off">
                    <div id="saleSuggest" class="suggest-panel"></div>
                </div>
                <input type="search" id="fCust" placeholder=" ค้นหาลูกค้า (รหัส/ชื่อ)..." autocomplete="off">
                <input type="search" id="fBill" placeholder=" ค้นหาเลขบิล..." autocomplete="off">
                <button type="button" class="btn-ghost" id="btnClear">ล้าง</button>
            </div>
        </div>

        <div class="tabs" id="statusTabs">
            <button type="button" class="tab active" data-status="open">ยังไม่เคลียร์</button>
            <button type="button" class="tab" data-status="pending">รอแก้</button>
            <button type="button" class="tab" data-status="tracking">เปิดบิลใหม่ (รอส่ง)</button>
            <button type="button" class="tab" data-status="cleared">เคลียร์แล้ว</button>
            <button type="button" class="tab" data-status="all">ทั้งหมด</button>
        </div>

        <div class="table-scroll">
            <div class="table-inner">
                @php $colspan = $canSolve ? 8 : 7; @endphp
                <table id="mainTable" class="is-empty">
                    <thead>
                        <tr>
                            <th>เลขบิล / SO</th>
                            <th style="text-align:left;">ลูกค้า</th>
                            <th>Sale</th>
                            <th>เหตุผลของผิด</th>
                            <th>รับเข้าโดย / เมื่อ</th>
                            <th>วิธีแก้ (solve)</th>
                            <th>สถานะ</th>
                            @if($canSolve)<th>จัดการ</th>@endif
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="{{ $colspan }}"><div class="empty-wrapper"><div class="empty-state">เลือกตัวกรอง (Sale / ลูกค้า / เลขบิล) เพื่อค้นหา</div></div></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

@if($canSolve)
<!-- Modal เปิดบิลใหม่ -->
<div id="newbillModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-icon">＋</div>
            <div>
                <div class="modal-title">เปิดบิลใหม่ไปส่ง</div>
                <div class="modal-sub" id="nbBillLabel"></div>
            </div>
        </div>
        <label class="modal-label" for="nbInput">เลขบิลใหม่</label>
        <input type="search" id="nbInput" placeholder="กรอกเลขบิลใหม่ที่จะไปส่ง..." autocomplete="off">
        <div class="modal-hint">ผูก 2 เลขบิลนี้เป็นงานเดียวกัน — บิลของผิดจะเคลียร์อัตโนมัติเมื่อบิลใหม่นี้ส่ง "จัดส่งสำเร็จ" หรือ "ค้างบิล"</div>
        <div class="modal-actions">
            <button type="button" class="btn-ghost" onclick="closeNewbill()">ยกเลิก</button>
            <button type="button" class="btn-primary" id="nbConfirm" onclick="confirmNewbill()">ยืนยัน</button>
        </div>
    </div>
</div>
@endif

<script>
    const DATA_URL  = "{{ route('wrongbill.data') }}";
    @if($canSolve)
    const SOLVE_URL = "{{ route('wrongbill.solve') }}";
    @endif
    const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
    const AUTO_LOAD = {{ ($autoLoad ?? false) ? 'true' : 'false' }};
    const CAN_SOLVE = {{ ($canSolve ?? false) ? 'true' : 'false' }};
    const COLSPAN   = {{ $canSolve ? 8 : 7 }};
    const SALE_OPTIONS = @json($saleOptions ?? []);

    const tbody     = document.getElementById('tableBody');
    const mainTable = document.getElementById('mainTable');
    const fSale = document.getElementById('fSale');
    const fCust = document.getElementById('fCust');
    const fBill = document.getElementById('fBill');
    const btnClear = document.getElementById('btnClear');
    const openCountEl = document.getElementById('openCount');

    let currentStatus = 'open';
    let currentRows = [];

    function esc(s){ return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
    function escJs(s){ return String(s ?? '').replace(/\\/g,'\\\\').replace(/'/g,"\\'"); }
    function soLink(so){
        if (!so || so === '-') return '<span class="dash">-</span>';
        return '<a class="ref-link" href="http://server_update:8000/sodetail?SONum=' + encodeURIComponent(so) + '" target="_blank" rel="noopener">SO ' + esc(so) + '</a>';
    }

    // ===== autocomplete Sale =====
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
        input.addEventListener('keydown', e => {
            const items = Array.from(panel.querySelectorAll('.suggest-item'));
            if(e.key==='ArrowDown'&&items.length){e.preventDefault();hl=Math.min(hl+1,items.length-1);items.forEach((it,i)=>it.classList.toggle('hl',i===hl));items[hl].scrollIntoView({block:'nearest'});}
            else if(e.key==='ArrowUp'&&items.length){e.preventDefault();hl=Math.max(hl-1,0);items.forEach((it,i)=>it.classList.toggle('hl',i===hl));items[hl].scrollIntoView({block:'nearest'});}
            else if(e.key==='Enter'&&hl>=0&&items[hl]){e.preventDefault();input.value=items[hl].dataset.val;panel.classList.remove('open');if(onPick)onPick();}
            else if(e.key==='Escape'){panel.classList.remove('open');}
        });
        input.addEventListener('blur', () => setTimeout(()=>panel.classList.remove('open'),120));
    }
    attachSuggest(fSale, document.getElementById('saleSuggest'), SALE_OPTIONS, () => search());

    function setMsg(text){ if(mainTable) mainTable.classList.add('is-empty'); tbody.innerHTML = '<tr><td colspan="'+COLSPAN+'"><div class="empty-wrapper"><div class="empty-state">'+esc(text)+'</div></div></td></tr>'; }
    function setLoading(text){ if(mainTable) mainTable.classList.add('is-empty'); tbody.innerHTML = '<tr><td colspan="'+COLSPAN+'"><div class="empty-wrapper"><div class="loading-state"><div class="progress-track"><div class="progress-fill" id="progressFill"></div></div><div class="progress-label" id="progressLabel">'+esc(text)+' 0%</div></div></div></td></tr>'; }
    let progressTimer=null;
    function startProgress(text){ if(progressTimer)clearInterval(progressTimer); let pct=0; progressTimer=setInterval(()=>{ pct+=(90-pct)*0.15+0.6; if(pct>90)pct=90; const f=document.getElementById('progressFill'),l=document.getElementById('progressLabel'); if(f)f.style.width=pct.toFixed(0)+'%'; if(l)l.textContent=text+' '+pct.toFixed(0)+'%'; },120); }
    function stopProgress(){ if(progressTimer){clearInterval(progressTimer);progressTimer=null;} }

    function stateBadge(r){
        if (r.state === 'cleared') return '<span class="badge badge-cleared">เคลียร์แล้ว</span>';
        if (r.state === 'tracking') return '<span class="badge badge-tracking">เปิดบิลใหม่ · รอส่ง</span>';
        return '<span class="badge badge-pending">รอแก้</span>';
    }
    function solveCell(r){
        if (r.solve_mode === 'stock') return '<span class="solve-info"><b>เก็บเข้าสต็อก</b></span>';
        if (r.solve_mode === 'newbill'){
            const st = r.new_status ? ' · ส่ง: '+esc(r.new_status) : ' · ยังไม่ส่ง';
            return '<span class="solve-info">บิลใหม่ <span class="newbill-chip">'+esc(r.new_bill)+'</span>'+st+'</span>';
        }
        return '<span class="dash">— ยังไม่ตัดสินใจ —</span>';
    }
    function actionsCell(r){
        if (!CAN_SOLVE) return '';
        if (r.cleared){
            // เคลียร์แล้ว: เปิดให้แก้ไข (undo) เฉพาะกรณี solve เป็นค่าที่ตั้งเอง
            return '<td><div class="actions-cell"><button type="button" class="btn btn-undo" onclick="doUndo(\''+escJs(r.job_key)+'\')">แก้ไข</button></div></td>';
        }
        if (r.solve_mode === 'newbill'){
            return '<td><div class="actions-cell">'
                + '<button type="button" class="btn btn-newbill" onclick="openNewbill(\''+escJs(r.job_key)+'\',\''+escJs(r.bill_no)+'\',\''+escJs(r.new_bill)+'\')">เปลี่ยนเลขบิล</button>'
                + '<button type="button" class="btn btn-stock" onclick="doStock(\''+escJs(r.job_key)+'\',\''+escJs(r.bill_no)+'\')">เก็บเข้าสต็อก</button>'
                + '<button type="button" class="btn btn-undo" onclick="doUndo(\''+escJs(r.job_key)+'\')">ยกเลิก</button>'
                + '</div></td>';
        }
        // pending
        return '<td><div class="actions-cell">'
            + '<button type="button" class="btn btn-stock" onclick="doStock(\''+escJs(r.job_key)+'\',\''+escJs(r.bill_no)+'\')">เก็บเข้าสต็อก</button>'
            + '<button type="button" class="btn btn-newbill" onclick="openNewbill(\''+escJs(r.job_key)+'\',\''+escJs(r.bill_no)+'\',\'\')">เปิดบิลใหม่</button>'
            + '</div></td>';
    }
    function rowHtml(r){
        const cust = '<div><b>'+esc(r.customer_name||'-')+'</b></div>'+(r.customer_code?'<div class="solve-info">'+esc(r.customer_code)+'</div>':'');
        const wrongBy = (r.wrong_by?esc(r.wrong_by):'<span class="dash">-</span>')
            + (r.wrong_time?'<div class="solve-info">'+esc(r.wrong_time)+'</div>':'')
            + (r.driver_name?'<div class="solve-info">คนขับ: '+esc(r.driver_name)+'</div>':'');
        return '<tr>'
            + '<td class="num"><div class="ref-link">'+esc(r.bill_no||'-')+'</div>'
              + (r.deli_status==='ค้างบิล' ? '<div style="margin-top:3px;"><span class="badge badge-hold">ค้างบิล</span></div>' : '<div style="margin-top:3px;"><span class="badge badge-wrong">สินค้าผิด</span></div>')
              + (r.so_id?'<div>'+soLink(r.so_id)+'</div>':'')+'</td>'
            + '<td class="cell-left">'+cust+'</td>'
            + '<td>'+(r.sale?esc(r.sale):'<span class="dash">-</span>')+'</td>'
            + '<td class="reason-cell">'+(r.reason?esc(r.reason):'<span class="dash">-</span>')+'</td>'
            + '<td>'+wrongBy+'</td>'
            + '<td>'+solveCell(r)+'</td>'
            + '<td>'+stateBadge(r)+'</td>'
            + actionsCell(r)
            + '</tr>';
    }

    function hasFilter(){ return !!(fSale.value.trim()||fCust.value.trim()||fBill.value.trim()); }

    async function search(){
        if (!AUTO_LOAD && !hasFilter()){
            openCountEl.textContent = 0;
            setMsg('เลือกตัวกรอง (Sale / ลูกค้า / เลขบิล) เพื่อค้นหา');
            return;
        }
        const params = new URLSearchParams();
        params.set('sale', fSale.value.trim());
        params.set('customer', fCust.value.trim());
        params.set('bill', fBill.value.trim());
        params.set('status', currentStatus);

        setLoading('กำลังค้นหา...'); startProgress('กำลังค้นหา...');
        try{
            const res = await fetch(DATA_URL+'?'+params.toString(), { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} });
            const data = await res.json(); stopProgress();
            if (!res.ok || !data.ok){ setMsg((data&&data.message)||'ค้นหาไม่สำเร็จ'); return; }
            const rows = data.rows || [];
            currentRows = rows;
            openCountEl.textContent = rows.filter(r => !r.cleared).length;
            if (rows.length === 0){ setMsg(data.message || 'ไม่พบบิลของผิดตามเงื่อนไข'); return; }
            if (mainTable) mainTable.classList.remove('is-empty');
            tbody.innerHTML = rows.map(rowHtml).join('');
        }catch(e){ console.error(e); stopProgress(); setMsg('เกิดข้อผิดพลาดในการเชื่อมต่อ'); }
    }

    let searchDebounce=null;
    function scheduleSearch(delay=450){ if(searchDebounce)clearTimeout(searchDebounce); searchDebounce=setTimeout(()=>search(),delay); }

    // tabs
    document.getElementById('statusTabs').addEventListener('click', e => {
        const t = e.target.closest('.tab'); if(!t) return;
        document.querySelectorAll('#statusTabs .tab').forEach(x=>x.classList.remove('active'));
        t.classList.add('active');
        currentStatus = t.dataset.status;
        search();
    });

    btnClear.addEventListener('click', () => {
        if(searchDebounce)clearTimeout(searchDebounce);
        fSale.value=''; fCust.value=''; fBill.value='';
        openCountEl.textContent = 0;
        setMsg('เลือกตัวกรอง (Sale / ลูกค้า / เลขบิล) เพื่อค้นหา');
    });
    [fCust,fBill].forEach(el => el.addEventListener('input', () => scheduleSearch()));
    fSale.addEventListener('input', () => scheduleSearch());
    [fSale,fCust,fBill].forEach(el => el.addEventListener('keydown', e => { if(e.key==='Enter'){ if(searchDebounce)clearTimeout(searchDebounce); search(); } }));

    @if($canSolve)
    // ===== actions =====
    async function postSolve(payload, okReload=true){
        try{
            const res = await fetch(SOLVE_URL, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body:JSON.stringify(payload) });
            const data = await res.json().catch(()=>null);
            if (!res.ok || !data || !data.ok){ alert((data&&data.message)||'บันทึกไม่สำเร็จ'); return false; }
            if (data.message) { /* toast แบบง่าย */ }
            if (okReload) search();
            return data;
        }catch(e){ console.error(e); alert('เกิดข้อผิดพลาดในการเชื่อมต่อ'); return false; }
    }
    async function doStock(jobKey, billNo){
        if (!confirm('ยืนยันเก็บของเข้าสต็อก สำหรับบิล '+billNo+' ?\n(บิลนี้จะถือว่าเคลียร์แล้ว)')) return;
        await postSolve({ job_key:jobKey, mode:'stock' });
    }
    async function doUndo(jobKey){
        if (!confirm('ยกเลิกการตัดสินใจ และกลับไปสถานะ "รอแก้" ?')) return;
        await postSolve({ job_key:jobKey, mode:'clear' });
    }
    // modal เปิดบิลใหม่
    let nbTarget = null;
    function openNewbill(jobKey, billNo, current){
        nbTarget = jobKey;
        document.getElementById('nbBillLabel').textContent = 'บิลของผิด: ' + billNo;
        const inp = document.getElementById('nbInput'); inp.value = current || '';
        document.getElementById('newbillModal').style.display = 'flex';
        setTimeout(()=>inp.focus(), 50);
    }
    function closeNewbill(){ const m=document.getElementById('newbillModal'); if(m)m.style.display='none'; nbTarget=null; }
    async function confirmNewbill(){
        if (!nbTarget) return;
        const newBill = document.getElementById('nbInput').value.trim();
        if (!newBill){ alert('กรุณากรอกเลขบิลใหม่'); return; }
        const btn = document.getElementById('nbConfirm'); btn.disabled=true; btn.textContent='กำลังบันทึก...';
        const data = await postSolve({ job_key:nbTarget, mode:'newbill', new_bill:newBill }, false);
        btn.disabled=false; btn.textContent='ยืนยัน';
        if (data){ if (data.warning && data.message) alert(data.message); closeNewbill(); search(); }
    }
    document.getElementById('newbillModal').addEventListener('click', function(e){ if(e.target===this) closeNewbill(); });
    document.addEventListener('keydown', e => { if(e.key==='Escape') closeNewbill(); });
    // เผยให้ inline onclick เรียกได้
    window.doStock=doStock; window.doUndo=doUndo; window.openNewbill=openNewbill; window.closeNewbill=closeNewbill; window.confirmNewbill=confirmNewbill;
    @endif

    @if($autoLoad ?? false)
    search();   // admin: โหลดทั้งหมดทันที
    @endif
</script>
</body>
</html>
