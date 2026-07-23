<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ค้นหา Invoice - รายการเข้า PO ของนอก</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Sarabun', sans-serif; background-color: #f5f7fa; color: #333; font-size: clamp(12px, 0.45vw + 7px, 16px); }
        .top-nav { background: #ffffff; border-bottom: 1px solid #e2e8f0; color: #1b2d4f; padding: 15px 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 200; }
        .nav-title { font-size: clamp(15px, 1vw + 6px, 20px); font-weight: 600; display: flex; align-items: center; gap: 15px; color: #1b2d4f; }
        .nav-title .user-info { font-size: clamp(11px, 0.5vw + 6px, 13px); font-weight: 400; color: #64748b; }
        .nav-btn, .back-button { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: #3E6AE1; color: #ffffff; text-decoration: none; border: 1px solid #3E6AE1; border-radius: 6px; font-size: clamp(11px, 0.55vw + 5px, 14px); font-weight: 600; white-space: nowrap; transition: background .2s ease, color .2s ease; }
        .nav-btn:hover, .back-button:hover { background: #2f56c4; color: #ffffff; border-color: #2f56c4; }
        .search-container { max-width: 1200px; margin: 28px auto; padding: 0 24px; }
        .search-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 28px; }
        .search-card h2 { font-size: clamp(17px, 1vw + 7px, 21px); font-weight: 700; color: #1b2d4f; margin-bottom: 20px; }
        .search-label { display: block; font-size: clamp(11px, 0.5vw + 6px, 13px); font-weight: 700; color: #1b2d4f; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 10px; }
        .search-row { display: flex; gap: 12px; align-items: stretch; }
        .search-input-group { flex: 1; position: relative; }
        .search-input { width: 100%; font-family: inherit; font-size: clamp(12px, 0.55vw + 6px, 15px); height: clamp(42px, 0.6vw + 38px, 48px); padding: 0 16px; border: 2px solid #e2e6ec; border-radius: 6px; background: white; color: #1e293b; outline: none; transition: all .22s ease; box-shadow: 0 1px 3px rgba(27,45,79,.08); }
        .search-input:focus { border-color: #3E6AE1; box-shadow: 0 0 0 4px rgba(62,106,225,.12), 0 3px 12px rgba(27,45,79,.10); }
        .ac-list { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1.5px solid #3E6AE1; border-top: none; border-radius: 0 0 8px 8px; max-height: 260px; overflow-y: auto; z-index: 300; box-shadow: 0 8px 24px rgba(27,45,79,.13); display: none; }
        .ac-list.open { display: block; }
        .ac-item { padding: 11px 16px; font-size: clamp(12px, 0.55vw + 6px, 14px); cursor: pointer; border-bottom: 1px solid #f0f4fa; color: #1e293b; transition: background .12s; display: flex; align-items: center; gap: 10px; }
        .ac-item:last-child { border-bottom: none; }
        .ac-item:hover, .ac-item.active { background: #eef2fd; color: #2f56c4; }
        .ac-badge { background: #eef2fd; color: #2f56c4; border-radius: 4px; padding: 2px 8px; font-size: clamp(10px, 0.5vw + 5px, 12px); font-weight: 700; white-space: nowrap; }
        .ac-meta { font-size: clamp(10px, 0.5vw + 5px, 12px); color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ac-empty { padding: 14px 16px; font-size: 14px; color: #9ca3af; text-align: center; }
        .search-buttons { display: flex; gap: 10px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: clamp(42px, 0.6vw + 38px, 48px); padding: 0 22px; border-radius: 6px; font-family: inherit; font-size: clamp(12px, 0.55vw + 6px, 14px); font-weight: 600; border: 1px solid transparent; cursor: pointer; transition: all .2s ease; white-space: nowrap; color: white; box-shadow: 0 1px 3px rgba(27,45,79,.08); }
        .btn:active { transform: translateY(1px); box-shadow: none; }
        .btn-primary { background: #3E6AE1; } .btn-primary:hover { background: #2f56c4; }
        .btn-ghost { background: #ffffff; color: #334155; border-color: #d4d9e2; } .btn-ghost:hover { background: #f3f5f8; color: #171a20; }
        .main-container { max-width: 1200px; margin: 0 auto; padding: 0 24px 40px; }
        .result-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
        .result-header { background: #3E6AE1; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .result-header .inv-title { color: white; font-size: clamp(15px, 0.9vw + 6px, 18px); font-weight: 700; }
        .result-header .inv-count { background: rgba(255,255,255,.18); color: white; border-radius: 20px; padding: 6px 16px; font-size: clamp(11px, 0.5vw + 6px, 13px); font-weight: 600; }
        .inv-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .inv-table thead th { background: #f9fafb; padding: 13px 16px; text-align: left; font-size: clamp(11px, 0.55vw + 5px, 13px); font-weight: 600; color: #4b5563; border-bottom: 2px solid #e5e7eb; }
        .inv-table tbody td { padding: 15px 16px; border-bottom: 1px solid #f3f4f6; font-size: clamp(12px, 0.6vw + 5px, 14px); vertical-align: middle; }
        .inv-table tbody tr:last-child td { border-bottom: none; }
        .inv-table tbody tr:hover { background: #f9fafb; }
        .cell-ponum { font-family: 'JetBrains Mono', 'Courier New', monospace; font-weight: 700; color: #2f56c4; font-size: clamp(11px, 0.5vw + 5px, 13px); background: #eef2fd; border-radius: 4px; padding: 4px 9px; display: inline-block; cursor: pointer; transition: background .15s; }
        .cell-ponum:hover { background: #dbe4fb; }
        .cell-name { color: #1f2937; font-weight: 500; word-break: break-word; }
        .cell-qty  { font-weight: 700; color: #059669; text-align: right; padding-right: 24px !important; }
        .cell-date { color: #6b7280; font-size: clamp(11px, 0.5vw + 5px, 13px); white-space: nowrap; }
        .cell-note { color: #6b7280; font-size: clamp(10px, 0.5vw + 5px, 12px); max-width: 160px; word-break: break-word; }
        .filter-bar { padding: 14px 24px; background: #fbfcfd; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 12px; }
        .filter-bar label { font-size: clamp(11px, 0.5vw + 6px, 13px); font-weight: 600; color: #4b5563; white-space: nowrap; }
        .filter-input { flex: 1; max-width: 360px; font-family: inherit; font-size: clamp(11px, 0.55vw + 5px, 13px); padding: 8px 14px; border: 1.5px solid #e2e6ec; border-radius: 6px; background: white; color: #1e293b; outline: none; transition: border-color .2s, box-shadow .2s; }
        .filter-input:focus { border-color: #3E6AE1; box-shadow: 0 0 0 3px rgba(62,106,225,.10); }
        .filter-count { font-size: clamp(10px, 0.5vw + 5px, 12px); color: #9ca3af; margin-left: auto; white-space: nowrap; }
        .state-box { padding: 70px 20px; text-align: center; color: #9ca3af; }
        .state-box .st-icon  { font-size: 48px; margin-bottom: 16px; }
        .state-box .st-title { font-size: clamp(15px, 0.8vw + 7px, 18px); font-weight: 600; margin-bottom: 6px; color: #6b7280; }
        .state-box .st-sub   { font-size: clamp(12px, 0.55vw + 6px, 14px); }
        @keyframes pulse { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.15); opacity: .7; } }
        @media (max-width: 640px) {
            .search-container, .main-container { padding: 0 14px; }
            .search-row { flex-direction: column; align-items: stretch; }
            .search-card { padding: 20px; }
            .search-buttons { width: 100%; }
            .search-buttons .btn { flex: 1; }
            .top-nav { flex-direction: column; align-items: stretch; gap: 10px; }
            .top-nav > div:last-child { display: flex; flex-wrap: wrap; gap: 8px; }
            .nav-btn, .back-button { flex: 1; justify-content: center; }
            .inv-table thead th:nth-child(6),
            .inv-table tbody td:nth-child(6) { display: none; }
        }
    </style>
</head>
<body>

<div class="top-nav">
    <div class="nav-title">
        <span>🔍 ค้นหา Invoice</span>
        <span class="user-info">👤 {{ request()->get('create_by', 'Guest') }}</span>
    </div>
    <div style="display:flex; gap:8px;">
        <a href="/pooutside?create_by={{ urlencode(request()->get('create_by','Guest')) }}" class="nav-btn">← ตามของนอก</a>
        <a href="http://server_update:8000/solist" class="back-button">← หน้าหลัก</a>
    </div>
</div>

<div class="search-container">
    <div class="search-card">
        <h2>📄 ค้นหาด้วยเลข Invoice</h2>
        <label class="search-label" for="invoiceInput">หมายเลข Invoice</label>
        <div class="search-row">
            <div class="search-input-group">
                <input type="text" id="invoiceInput" class="search-input"
                       placeholder="พิมพ์เลข Invoice..." autocomplete="off" />
                <div class="ac-list" id="acList"></div>
            </div>
            <div class="search-buttons">
                <button class="btn btn-primary" onclick="doSearch()">✓ ค้นหา</button>
                <button class="btn btn-ghost" onclick="doReset()">↺ Reset</button>
            </div>
        </div>
    </div>
</div>

<div class="main-container">
    <div id="resultArea">
        <div class="result-card">
            <div class="state-box">
                <div class="st-icon">🔍</div>
                <div class="st-title">พิมพ์เลข Invoice เพื่อค้นหา</div>
                <div class="st-sub">ระบบจะแนะนำ Invoice ที่ใกล้เคียงให้อัตโนมัติ</div>
            </div>
        </div>
    </div>
</div>

<script>
const input   = document.getElementById('invoiceInput');
const acList  = document.getElementById('acList');
const result  = document.getElementById('resultArea');
let timer     = null;
let acIdx     = -1;

// ✅ จำนวน → ทศนิยม 2 ตำแหน่ง + คั่นหลักพัน
function formatQty(v){
    const n = parseFloat(v);
    if (isNaN(n)) return (v == null ? '0' : String(v));
    return n.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function fmtDate(d) {
    if (!d) return '-';
    const m = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
    const dt = new Date(d);
    if (isNaN(dt)) return d;
    return `${dt.getDate()} ${m[dt.getMonth()]} ${dt.getFullYear() + 543}`;
}

input.addEventListener('input', function () {
    acIdx = -1;
    clearTimeout(timer);
    const q = this.value.trim();
    if (q.length < 2) { closeAC(); return; }
    timer = setTimeout(() => fetchAC(q), 200);
});

async function fetchAC(q) {
    try {
        const r = await fetch(`/pooutside/invoice-suggest?q=${encodeURIComponent(q)}`);
        const d = await r.json();
        renderAC(d.suggestions || [], q);
    } catch { closeAC(); }
}

function renderAC(items, q) {
    if (!items.length) {
        acList.innerHTML = `<div class="ac-empty">ไม่พบ Invoice ที่ตรงกับ "${q}"</div>`;
        acList.classList.add('open');
        return;
    }
    acList.innerHTML = items.map((inv, i) => `
        <div class="ac-item" data-val="${inv.invoice}" data-i="${i}"
             onmousedown="pickAC('${inv.invoice}')">
            <span class="ac-badge">${hlMatch(inv.invoice, q)}</span>
            <span class="ac-meta">${inv.name ?? ''} — ${fmtDate(inv.date_invoice)}</span>
        </div>`).join('');
    acList.classList.add('open');
}

function hlMatch(text, q) {
    const idx = text.toLowerCase().indexOf(q.toLowerCase());
    if (idx === -1) return text;
    return text.slice(0, idx)
        + '<b style="color:#2f56c4">' + text.slice(idx, idx + q.length) + '</b>'
        + text.slice(idx + q.length);
}

function closeAC() { acList.classList.remove('open'); acList.innerHTML = ''; acIdx = -1; }
function pickAC(val) { input.value = val; closeAC(); doSearch(); }

input.addEventListener('keydown', function (e) {
    const items = acList.querySelectorAll('.ac-item');
    if (e.key === 'ArrowDown') { e.preventDefault(); acIdx = Math.min(acIdx + 1, items.length - 1); markActive(items); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); acIdx = Math.max(acIdx - 1, 0); markActive(items); }
    else if (e.key === 'Enter') {
        e.preventDefault();
        if (acIdx >= 0 && items[acIdx]) { input.value = items[acIdx].dataset.val; closeAC(); }
        doSearch();
    }
    else if (e.key === 'Escape') { closeAC(); }
});

function markActive(items) { items.forEach((el, i) => el.classList.toggle('active', i === acIdx)); }
document.addEventListener('click', e => { if (!e.target.closest('.search-input-group')) closeAC(); });

async function doSearch() {
    const inv = input.value.trim();
    closeAC();
    if (!inv) { alert('กรุณากรอกหมายเลข Invoice'); return; }

    result.innerHTML = `<div class="result-card"><div class="state-box">
        <div class="st-icon" style="animation:pulse 1.2s infinite">⏳</div>
        <div class="st-title">กำลังค้นหา...</div></div></div>`;

    try {
        const r = await fetch(`/pooutside/invoice-search?invoice=${encodeURIComponent(inv)}`);
        const d = await r.json();
        if (!d.success || !d.rows.length) {
            result.innerHTML = `<div class="result-card"><div class="state-box">
                <div class="st-icon">📭</div>
                <div class="st-title">ไม่พบ Invoice "${inv}"</div>
                <div class="st-sub">ลองตรวจสอบหมายเลขอีกครั้ง</div></div></div>`;
            return;
        }
        renderResult(inv, d.rows);
    } catch {
        result.innerHTML = `<div class="result-card"><div class="state-box">
            <div class="st-icon">⚠️</div>
            <div class="st-title" style="color:#991b1b">เกิดข้อผิดพลาด</div></div></div>`;
    }
}

let allRows = [];

function renderResult(inv, rows) {
    allRows = rows;
    const total = rows.reduce((s, r) => s + parseFloat(r.quantity || 0), 0);

    result.innerHTML = `
    <div class="result-card">
        <div class="result-header">
            <div>
                <div class="inv-title">Invoice: ${inv}</div>
            </div>
            <div class="inv-count">📦 ${rows.length} รายการ · รวม ${formatQty(total)} หน่วย</div>
        </div>
        <div class="filter-bar">
            <label>🔎 ชื่อสินค้า:</label>
            <input type="text" class="filter-input" id="nameFilter"
                   placeholder="พิมพ์ชื่อสินค้าเพื่อ filter..." autocomplete="off" />
            <span class="filter-count" id="filterCount">แสดง ${rows.length} / ${rows.length} รายการ</span>
        </div>
        <table class="inv-table">
            <thead><tr>
                <th style="width:40px">#</th>
                <th>ชื่อสินค้า</th>
                <th style="text-align:right; padding-right:24px; width:120px;">จำนวน</th>
                <th style="width:130px;">เลข PO</th>
                <th style="width:130px;">วันที่ Invoice</th>
                <th style="width:150px;">หมายเหตุ</th>
            </tr></thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>`;

    renderTableRows(rows);

    document.getElementById('nameFilter').addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        if (!q) {
            renderTableRows(allRows);
            return;
        }
        const filtered = allRows.filter(r => (r.name || '').toLowerCase().includes(q));
        renderTableRows(filtered);
    });
}

function renderTableRows(rows) {
    const tbody = document.getElementById('tableBody');
    if (!tbody) return;

    if (!rows.length) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:30px; color:#9ca3af;">
            ไม่พบสินค้าที่ตรงกับ filter</td></tr>`;
    } else {
        tbody.innerHTML = rows.map((r, i) => `<tr>
            <td style="color:#9ca3af; font-size:13px;">${i + 1}</td>
            <td class="cell-name">${r.name ?? '-'}</td>
            <td class="cell-qty">${formatQty(r.quantity ?? 0)}</td>
            <td>${r.ponum
                ? `<span class="cell-ponum" onclick="gotoPO('${r.ponum}')">${r.ponum}</span>`
                : '<span style="color:#d1d5db">-</span>'}</td>
            <td class="cell-date">${fmtDate(r.date_invoice)}</td>
            <td class="cell-note">${r.note ?? '-'}</td>
        </tr>`).join('');
    }

    const countEl = document.getElementById('filterCount');
    if (countEl) countEl.textContent = `แสดง ${rows.length} / ${allRows.length} รายการ`;
}

function gotoPO(po) {
    const cb = new URLSearchParams(location.search).get('create_by') || 'Guest';
    location.href = '/pooutside?create_by=' + encodeURIComponent(cb) + '&prefill_po=' + encodeURIComponent(po);
}

function doReset() {
    input.value = ''; closeAC();
    result.innerHTML = `<div class="result-card"><div class="state-box">
        <div class="st-icon">🔍</div>
        <div class="st-title">พิมพ์เลข Invoice เพื่อค้นหา</div>
        <div class="st-sub">ระบบจะแนะนำ Invoice ที่ใกล้เคียงให้อัตโนมัติ</div></div></div>`;
    input.focus();
}
</script>
</body>
</html>