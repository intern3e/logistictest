<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Delivery</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --blue:#2563eb;
            --border:#e5e7eb;
            --bg:#f9fafb;
            --white:#fff;
            --text:#111827;
            --muted:#6b7280;
            --mono:'IBM Plex Mono',monospace;
        }
        body{font-family:'Noto Sans Thai',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;font-size:14px;}

        /* TOPBAR */
        .topbar{
            height:54px;background:var(--white);border-bottom:1px solid var(--border);
            display:flex;align-items:center;justify-content:space-between;padding:0 24px;
            position:sticky;top:0;z-index:50;
        }
        .brand{font-size:15px;font-weight:700;display:flex;align-items:center;gap:8px;}
        .brand-line{width:3px;height:18px;background:var(--blue);border-radius:2px;}

        /* MAIN */
        main{padding:20px 24px;}

        /* FILTER */
        .filter-bar{
            background:var(--white);border:1px solid var(--border);border-radius:8px;
            padding:14px 18px;margin-bottom:14px;
            display:flex;align-items:flex-end;gap:10px;flex-wrap:wrap;
        }
        .field{display:flex;flex-direction:column;gap:4px;}
        .field label{font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;}
        .field input{
            height:36px;border:1px solid var(--border);border-radius:6px;
            padding:0 12px;font-family:'Noto Sans Thai',sans-serif;font-size:13px;
            color:var(--text);background:var(--white);outline:none;min-width:160px;
            transition:border-color .15s;
        }
        .field input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,.08);}
        .btn-search{
            height:36px;background:var(--blue);color:#fff;border:none;border-radius:6px;
            padding:0 18px;font-family:'Noto Sans Thai',sans-serif;font-size:13px;
            font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all .15s;
        }
        .btn-search:hover{filter:brightness(1.1);}
        .btn-reset{
            height:36px;background:var(--white);color:var(--muted);
            border:1px solid var(--border);border-radius:6px;padding:0 14px;
            font-family:'Noto Sans Thai',sans-serif;font-size:13px;cursor:pointer;transition:all .15s;
        }
        .btn-reset:hover{border-color:var(--muted);color:var(--text);}

        /* TABLE */
        .table-wrap{
            background:var(--white);border:1px solid var(--border);border-radius:8px;overflow:hidden;
        }
        .table-head{
            padding:12px 18px;border-bottom:1px solid var(--border);
            display:flex;align-items:center;justify-content:space-between;gap:10px;
        }
        .table-head-title{font-size:13px;font-weight:700;}
        .search-box{position:relative;}
        .search-box svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none;}
        .search-box input{
            height:32px;border:1px solid var(--border);border-radius:20px;
            padding:0 12px 0 32px;font-family:'Noto Sans Thai',sans-serif;font-size:12px;
            color:var(--text);background:var(--bg);outline:none;width:200px;transition:all .15s;
        }
        .search-box input:focus{border-color:var(--blue);background:var(--white);width:240px;}

        table{width:100%;border-collapse:collapse;}
        thead th{
            padding:10px 14px;text-align:left;font-size:11px;font-weight:600;
            color:var(--muted);text-transform:uppercase;letter-spacing:.07em;
            background:#f9fafb;border-bottom:1px solid var(--border);white-space:nowrap;
        }
        tbody tr{border-bottom:1px solid #f3f4f6;transition:background .1s;}
        tbody tr:last-child{border-bottom:none;}
        tbody tr:hover{background:#f8faff;}
        tbody td{padding:11px 14px;font-size:13px;vertical-align:middle;}

        .num{font-family:var(--mono);font-size:12px;color:var(--muted);}
        .date{font-family:var(--mono);font-size:12px;color:var(--muted);}
        .bill{font-family:var(--mono);font-size:12px;color:var(--blue);font-weight:500;}

        /* Status select */
        .st-sel{
            border:1px solid;border-radius:16px;padding:4px 10px;
            font-family:'Noto Sans Thai',sans-serif;font-size:11px;font-weight:600;
            cursor:pointer;outline:none;appearance:none;
            padding-right:22px;
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='5'%3E%3Cpath d='M0 0l4 5 4-5z' fill='%236b7280'/%3E%3C/svg%3E");
            background-repeat:no-repeat;background-position:right 8px center;
            transition:all .15s;
        }
        .st-empty {background:#f3f4f6;color:#4b5563;border-color:#d1d5db;}
        .st-wrong {background:#fef2f2;color:#dc2626;border-color:#fecaca;}
        .st-return{background:#fffbeb;color:#d97706;border-color:#fde68a;}
        .st-stock {background:#f0fdf4;color:#16a34a;border-color:#bbf7d0;}

        /* Empty / loading */
        .empty-cell{padding:56px 24px !important;text-align:center;}
        .empty-t{font-size:13px;font-weight:600;color:var(--muted);margin-bottom:4px;}
        .empty-s{font-size:12px;color:#9ca3af;}
        @keyframes spin{to{transform:rotate(360deg)}}
        .spin{display:inline-block;animation:spin .8s linear infinite;margin-right:5px;}

        .table-foot{
            padding:10px 18px;border-top:1px solid var(--border);background:#f9fafb;
            display:flex;justify-content:space-between;font-size:11px;color:var(--muted);
            font-family:var(--mono);
        }

        /* Toast */
        .toast{
            position:fixed;bottom:22px;left:50%;
            transform:translateX(-50%) translateY(8px);
            background:#111827;color:#fff;padding:9px 20px;border-radius:20px;
            font-size:12px;font-weight:600;opacity:0;transition:all .25s;
            z-index:999;pointer-events:none;white-space:nowrap;
        }
        .toast.show{opacity:1;transform:translateX(-50%) translateY(0);}

        @media(max-width:640px){
            main{padding:14px;}
            .filter-bar{flex-direction:column;align-items:stretch;}
            .field input{min-width:unset;width:100%;}
            .table-head{flex-direction:column;align-items:flex-start;}
            .search-box input{width:100%;}
            .search-box{width:100%;}
        }
    </style>
</head>
<body>

<header class="topbar">
    <div class="brand">
        <div class="brand-line"></div>
        Delivery
    </div>
    <span style="font-size:12px;color:var(--muted);" id="hdr-date"></span>
</header>

<main>
    <!-- FILTER -->
    <div class="filter-bar">
        <div class="field">
            <label>วันที่</label>
            <input type="date" id="f-date" value="{{ date('Y-m-d') }}">
        </div>
        <div class="field">
            <label>ชื่อคนขับ</label>
            <input type="text" id="f-shop" placeholder="ค้นหาคนขับ...">
        </div>
        <button class="btn-search" onclick="load()">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            ค้นหา
        </button>
        <button class="btn-reset" onclick="reset()">รีเซ็ต</button>
    </div>

    <!-- TABLE -->
    <div class="table-wrap">
        <div class="table-head">
            <span class="table-head-title" id="tbl-title">รายการทั้งหมด</span>
            <div class="search-box">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" placeholder="ค้นหาในตาราง..." oninput="search(this.value)">
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>วันที่</th>
                        <th>ชื่อ Sale</th>
                        <th>เลขที่บิล</th>
                        <th>หมายเหตุ</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    <tr><td colspan="6" class="empty-cell">
                        <div class="empty-t">เลือกวันที่และกดค้นหา</div>
                        <div class="empty-s">เพื่อดูรายการส่งของ</div>
                    </td></tr>
                </tbody>
            </table>
        </div>
        <div class="table-foot">
            <span id="ft-info">—</span>
            <span id="ft-time">—</span>
        </div>
    </div>
</main>

<div class="toast" id="toast"></div>

<script>
const CSRF = () => document.head.querySelector('meta[name="csrf-token"]').getAttribute('content');
const STATUS = [
    {v:'',      l:'ค่าว่าง',        c:'st-empty'},
    {v:'wrong', l:'ของผิด',         c:'st-wrong'},
    {v:'return',l:'ขอคืน',          c:'st-return'},
    {v:'stock', l:'เก็บเข้าสต็อก', c:'st-stock'},
];
let rows = [];

document.getElementById('hdr-date').textContent =
    new Date().toLocaleDateString('th-TH',{weekday:'long',year:'numeric',month:'long',day:'numeric'});

document.getElementById('f-date').onkeydown =
document.getElementById('f-shop').onkeydown = e => { if(e.key==='Enter') load(); };

async function load() {
    const date = document.getElementById('f-date').value;
    const shop = document.getElementById('f-shop').value.trim().toLowerCase();
    if (!date) { toast('⚠️ กรุณาเลือกวันที่'); return; }

    const tbody = document.getElementById('tbody');
    tbody.innerHTML = '<tr><td colspan="6" class="empty-cell"><span class="spin">⟳</span> กำลังโหลด...</td></tr>';

    try {
        const res = await fetch('/return/list', {headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF()}});
        if (!res.ok) throw new Error('HTTP ' + res.status);
        let data = await res.json();
        data = data.filter(r => (r.date||'').slice(0,10) === date);
        if (shop) data = data.filter(r => (r.customer||'').toLowerCase().includes(shop));
        rows = data;
        render(rows);
    } catch(e) {
        tbody.innerHTML = `<tr><td colspan="6" class="empty-cell"><div class="empty-t">❌ ${e.message}</div></td></tr>`;
    }
}

function render(data) {
    const tbody = document.getElementById('tbody');
    document.getElementById('tbl-title').textContent = `รายการทั้งหมด (${data.length})`;
    document.getElementById('ft-info').textContent = `แสดง ${data.length} รายการ`;
    document.getElementById('ft-time').textContent = new Date().toLocaleTimeString('th-TH');

    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="empty-cell"><div class="empty-t">ไม่พบรายการ</div><div class="empty-s">ลองเปลี่ยนเงื่อนไขการค้นหา</div></td></tr>';
        return;
    }

    tbody.innerHTML = data.map((r,i) => {
        const sv = r.delivery_status || '';
        const sc = STATUS.find(s=>s.v===sv)?.c || 'st-empty';
        const opts = STATUS.map(s=>`<option value="${s.v}"${sv===s.v?' selected':''}>${s.l}</option>`).join('');
        const note = (r.note && r.note!=='-') ? r.note : '—';
        return `<tr>
            <td class="num">${i+1}</td>
            <td class="date">${r.date||'-'}</td>
            <td style="font-weight:600;">${r.customer||'-'}</td>
            <td><span class="bill">${r.po||r.id||'-'}</span></td>
            <td style="color:#6b7280;font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${note}">${note}</td>
            <td>
                <select class="${sc} st-sel" data-id="${r.id}" onchange="saveStatus(this)">
                    ${opts}
                </select>
            </td>
        </tr>`;
    }).join('');
}

async function saveStatus(sel) {
    STATUS.forEach(s => sel.classList.remove(s.c));
    sel.classList.add(STATUS.find(s=>s.v===sel.value)?.c||'st-empty');
    const id = sel.dataset.id;
    try {
        const res = await fetch('/return/'+encodeURIComponent(id)+'/status',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF(),'Accept':'application/json'},
            body:JSON.stringify({status:sel.value||'processing',updated_by:'delivery'})
        });
        if(!res.ok) throw new Error('HTTP '+res.status);
        const r = rows.find(r=>r.id==id);
        if(r) r.delivery_status = sel.value;
        toast('✅ บันทึกเรียบร้อย');
    } catch(e) { toast('❌ บันทึกไม่สำเร็จ'); }
}

function search(q) {
    q = q.toLowerCase().trim();
    render(q ? rows.filter(r =>
        (r.po||r.id||'').toString().toLowerCase().includes(q) ||
        (r.customer||'').toLowerCase().includes(q) ||
        (r.note||'').toLowerCase().includes(q)
    ) : rows);
}

function reset() {
    document.getElementById('f-date').value = new Date().toISOString().slice(0,10);
    document.getElementById('f-shop').value = '';
    rows = [];
    document.getElementById('tbl-title').textContent = 'รายการทั้งหมด';
    document.getElementById('ft-info').textContent = '—';
    document.getElementById('tbody').innerHTML = '<tr><td colspan="6" class="empty-cell"><div class="empty-t">เลือกวันที่และกดค้นหา</div><div class="empty-s">เพื่อดูรายการส่งของ</div></td></tr>';
}

function toast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'), 2000);
}
</script>
</body>
</html>