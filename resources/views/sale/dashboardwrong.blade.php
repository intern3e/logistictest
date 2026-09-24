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
        .btn-undo{background:var(--canvas);border-color:var(--border);color:var(--muted)}
        .btn-undo:hover{background:#f3f4f6;color:var(--ink)}
        .actions-cell{display:flex;gap:6px;justify-content:center;flex-wrap:wrap}
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

        <div class="tab-row">
            <label class="tab-lbl" for="fType">ประเภท:</label>
            <select id="fType">
                <option value="all">ทั้งหมด</option>
                <option value="wrong">ของผิด</option>
                <option value="hold">ค้างบิล</option>
            </select>
            <label class="tab-lbl" for="fStatus" style="margin-left:8px;">สถานะ:</label>
            <select id="fStatus">
                <option value="open">ยังไม่แก้</option>
                <option value="fixed">แก้แล้ว · รอผล</option>
                <option value="cleared">เคลียร์แล้ว</option>
                <option value="all">ทั้งหมด</option>
            </select>
        </div>

        <div class="table-scroll">
            <div class="table-inner">
                @php $colspan = $canSolve ? 7 : 6; @endphp
                <table id="mainTable" class="is-empty">
                    <thead>
                        <tr>
                            <th>เลขบิล / SO</th>
                            <th style="text-align:left;">ลูกค้า</th>
                            <th>Sale</th>
                            <th>ปัญหา</th>
                            <th>วิธีแก้ / ใครแก้ / เมื่อ</th>
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
@endif

<script>
    const DATA_URL  = "{{ route('wrongbill.data') }}";
    @if($canSolve)
    const SOLVE_URL = "{{ route('wrongbill.solve') }}";
    @endif
    const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
    const CAN_SOLVE = {{ ($canSolve ?? false) ? 'true' : 'false' }};
    const SEE_ALL   = {{ ($seeAll ?? false) ? 'true' : 'false' }};
    const COLSPAN   = {{ $canSolve ? 7 : 6 }};
    const SALE_OPTIONS = @json($saleOptions ?? []);

    const tbody     = document.getElementById('tableBody');
    const mainTable = document.getElementById('mainTable');
    const fSale = document.getElementById('fSale');   // อาจไม่มี (role sale)
    const fCust = document.getElementById('fCust');
    const fBill = document.getElementById('fBill');
    const btnClear = document.getElementById('btnClear');
    const openCountEl = document.getElementById('openCount');

    let currentType = 'all';
    let currentStatus = 'open';

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

    function setMsg(text){ if(mainTable) mainTable.classList.add('is-empty'); tbody.innerHTML = '<tr><td colspan="'+COLSPAN+'"><div class="empty-wrapper"><div class="empty-state">'+esc(text)+'</div></div></td></tr>'; }
    function setLoading(text){ if(mainTable) mainTable.classList.add('is-empty'); tbody.innerHTML = '<tr><td colspan="'+COLSPAN+'"><div class="empty-wrapper"><div class="loading-state"><div class="progress-track"><div class="progress-fill" id="progressFill"></div></div><div class="progress-label" id="progressLabel">'+esc(text)+' 0%</div></div></div></td></tr>'; }
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
    function actionsCell(r){
        if (!CAN_SOLVE) return '';
        let btns = '';
        if (r.state === 'open'){
            if (r.problem === 'ค้างบิล'){
                btns = '<button type="button" class="btn btn-tempdoc" onclick="openTarget(\'tempdoc\',\''+escJs(r.job_key)+'\',\''+escJs(r.bill_no)+'\')">เปิดเอกสารชั่วคราว</button>';
            } else {
                // ของผิด
                if (r.type === 'bill'){
                    btns += '<button type="button" class="btn btn-resend" onclick="doResend(\''+escJs(r.job_key)+'\',\''+escJs(r.bill_no)+'\')">ส่งใหม่เลขบิลเดิม</button>';
                }
                btns += '<button type="button" class="btn btn-changebill" onclick="openTarget(\'changebill\',\''+escJs(r.job_key)+'\',\''+escJs(r.bill_no)+'\')">เปลี่ยนเลขบิล</button>';
            }
        } else {
            // แก้แล้ว/เคลียร์แล้ว -> ยกเลิกการแก้ (undo)
            btns = '<button type="button" class="btn btn-undo" onclick="doUndo(\''+escJs(r.job_key)+'\')">ยกเลิกการแก้</button>';
        }
        return '<td><div class="actions-cell">'+btns+'</div></td>';
    }
    function rowHtml(r){
        const cust = '<div><b>'+esc(r.customer_name||'-')+'</b></div>'+(r.customer_code?'<div class="solve-info">'+esc(r.customer_code)+'</div>':'');
        const prob = problemBadge(r)
            + (r.reason?'<div class="reason-cell">'+esc(r.reason)+'</div>':'')
            + (r.wrong_by?'<div class="solve-info">โดย '+esc(r.wrong_by)+(r.wrong_time?' · '+esc(r.wrong_time):'')+'</div>':'')
            + (r.driver?'<div class="solve-info">คนขับ: '+esc(r.driver)+'</div>':'');
        return '<tr>'
            + '<td class="num bill-cell" style="border-left-color:'+esc(r.border||'#dcdcdc')+';"><div class="ref-link">'+esc(r.bill_no||'-')+'</div>'+(r.so_id?'<div>'+soLink(r.so_id)+'</div>':'')+'</td>'
            + '<td class="cell-left">'+cust+'</td>'
            + '<td>'+(r.sale?esc(r.sale):'<span class="dash">-</span>')+'</td>'
            + '<td>'+prob+'</td>'
            + '<td>'+solveCell(r)+'</td>'
            + '<td>'+stateBadge(r)+'</td>'
            + actionsCell(r)
            + '</tr>';
    }

    async function search(){
        const params = new URLSearchParams();
        if (fSale) params.set('sale', fSale.value.trim());
        params.set('customer', fCust.value.trim());
        params.set('bill', fBill.value.trim());
        params.set('type', currentType);
        params.set('status', currentStatus);

        setLoading('กำลังค้นหา...'); startProgress('กำลังค้นหา...');
        try{
            const res = await fetch(DATA_URL+'?'+params.toString(), { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} });
            const data = await res.json(); stopProgress();
            if (!res.ok || !data.ok){ setMsg((data&&data.message)||'ค้นหาไม่สำเร็จ'); return; }
            const rows = data.rows || [];
            openCountEl.textContent = rows.filter(r => r.state === 'open').length;
            if (rows.length === 0){ setMsg('ไม่พบงานตามเงื่อนไข'); return; }
            if (mainTable) mainTable.classList.remove('is-empty');
            tbody.innerHTML = rows.map(rowHtml).join('');
        }catch(e){ console.error(e); stopProgress(); setMsg('เกิดข้อผิดพลาดในการเชื่อมต่อ'); }
    }

    let searchDebounce=null;
    function scheduleSearch(delay=450){ if(searchDebounce)clearTimeout(searchDebounce); searchDebounce=setTimeout(()=>search(),delay); }

    const fType = document.getElementById('fType');
    const fStatus = document.getElementById('fStatus');
    fType.addEventListener('change', () => { currentType = fType.value; search(); });
    fStatus.addEventListener('change', () => { currentStatus = fStatus.value; search(); });
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
    async function doResend(jobKey, billNo){
        if (!confirm('ส่งใหม่ด้วยเลขบิลเดิม '+billNo+' ?\nงานจะถูกคืนไปหน้าจ่ายงานขนส่งเพื่อจ่ายให้คนขับใหม่ (บันทึกว่าเคยผิด 1 ครั้ง)')) return;
        await postSolve({ job_key:jobKey, mode:'resend' });
    }
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
    document.addEventListener('keydown', e => { if(e.key==='Escape') closeTarget(); });
    window.doResend=doResend; window.doUndo=doUndo; window.openTarget=openTarget; window.closeTarget=closeTarget; window.confirmTarget=confirmTarget;
    @endif

    search();   // โหลดทันที (sale = ของตัวเอง, อื่น ๆ = ทั้งหมด)
</script>
</body>
</html>
