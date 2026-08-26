<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, interactive-widget=resizes-content">
<title>VENDOR FILTER PRODUCT</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    :root{
        --blue:#3E6AE1;
        --blue-dark:#3457B1;
        --canvas:#FFFFFF;
        --ash:#F4F4F4;
        --carbon:#171A20;
        --graphite:#393C41;
        --pewter:#5C5E62;
        --silver:#8E8E8E;
        --cloud:#EEEEEE;
        --pale:#D0D1D2;
        --green:#1E7A3E;
        --green-bg:#E6F6EC;
        --amber:#B0790C;
        --amber-bg:#FDF2E0;
        --r:6px;
        --t:0.25s;
        --content-w:480px;
    }
    @media(min-width:768px){ :root{ --content-w:720px; } }
    @media(min-width:1024px){ :root{ --content-w:1080px; } }

    *{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent}
    html{background:#E9E9E9}
    body{
        font-family:'Sarabun',-apple-system,Arial,sans-serif;
        background:var(--ash);color:var(--graphite);
        min-height:100vh;
        max-width:var(--content-w);margin:0 auto;
        position:relative;font-weight:400;
    }

    /* ===== Header ===== */
    .topbar{
        background:rgba(244,244,244,0.9);
        backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
        padding:20px 16px 14px;position:sticky;top:0;z-index:20;
        border-bottom:1px solid var(--cloud);
    }
    .topbar h1{
        color:var(--carbon);font-size:21px;font-weight:600;
        display:flex;align-items:center;gap:10px;margin-bottom:12px;
    }
    .refresh-btn{
        margin-left:auto;width:30px;height:30px;border-radius:50%;
        border:none;background:var(--canvas);color:var(--pewter);
        font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;
        transition:background-color var(--t);flex-shrink:0;box-shadow:0 0 0 1px var(--cloud) inset;
    }
    .refresh-btn:active{background:var(--cloud)}
    .refresh-btn.spin{animation:spin .7s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}

    .vendor-badge{
        display:flex;align-items:center;gap:10px;flex-wrap:wrap;
        background:var(--canvas);border-radius:var(--r);padding:10px 12px;margin-bottom:12px;
    }
    .vendor-badge .v-code{
        font-size:13px;font-weight:600;color:var(--blue);background:#EAF0FE;
        padding:3px 9px;border-radius:12px;flex-shrink:0;
    }
    .vendor-badge .v-name{font-size:14px;color:var(--carbon);word-break:break-word}

    .searchrow{position:relative}
    .searchrow input{
        width:100%;height:44px;
        border:1px solid var(--pale);border-radius:var(--r);
        padding:0 38px 0 14px;font-size:15px;font-family:inherit;
        outline:none;background:var(--canvas);color:var(--carbon);
        transition:border-color var(--t);
    }
    .searchrow input:focus{border-color:var(--blue)}
    .searchrow input::placeholder{color:var(--silver)}
    .searchrow .clear-btn{
        position:absolute;right:6px;top:50%;transform:translateY(-50%);
        width:26px;height:26px;border-radius:50%;border:none;
        background:var(--ash);color:var(--pewter);font-size:13px;cursor:pointer;
        display:none;align-items:center;justify-content:center;
    }
    .searchrow .clear-btn.show{display:flex}

    .summary-row{
        display:flex;justify-content:flex-end;
        margin-bottom:10px;font-size:13px;color:var(--pewter);
    }
    .summary-row b{color:var(--carbon);font-weight:600}

    /* ===== Content ===== */
    .content{padding:14px 16px 40px}

    @media(min-width:768px){
        .topbar{padding:24px 24px 16px}
        .content{padding:18px 24px 48px}
        #poGrid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    }
    @media(min-width:1024px){
        #poGrid{grid-template-columns:1fr 1fr 1fr}
    }

    /* ===== PO Card ===== */
    .po-card{
        background:var(--canvas);border-radius:var(--r);
        padding:14px;margin-bottom:14px;
        border:1px solid var(--pale);
        border-top:3px solid var(--carbon);
    }
    @media(min-width:768px){ .po-card{margin-bottom:0} }

    .po-card-head{display:flex;justify-content:space-between;align-items:flex-start;gap:8px}

    /* เลข PO กดได้ -> ไปหน้ารับสินค้าเข้า (PO) พร้อมค้นหาเลขที่กดไปด้วย */
    .po-docu-link{
        font-size:16px;font-weight:600;color:var(--blue);word-break:break-all;
        background:none;border:none;padding:0;text-align:left;font-family:inherit;
        cursor:pointer;text-decoration:none;display:inline-block;
    }
    .po-docu-link:active{color:var(--blue-dark)}

    .po-badge{
        font-size:12px;font-weight:500;padding:3px 10px;border-radius:12px;
        white-space:nowrap;flex-shrink:0;
    }
    .po-badge.status-entry{color:var(--blue);background:#EAF0FE}
    .po-badge.status-partial{color:var(--amber);background:var(--amber-bg)}
    .po-badge.status-other{color:var(--pewter);background:var(--ash)}

    .po-meta{
        display:flex;flex-direction:column;gap:2px;
        font-size:12.5px;color:var(--pewter);margin-top:6px;
    }
    .po-meta b{color:var(--carbon);font-weight:500}
    .po-meta .cust-name{color:var(--carbon);font-weight:500}

    .po-items{margin-top:10px}
    .po-table{width:100%;border-collapse:collapse}
    .po-table thead th{
        text-align:left;font-size:11.5px;font-weight:600;color:var(--pewter);
        text-transform:uppercase;letter-spacing:.03em;
        padding:0 0 6px;border-bottom:2px solid var(--carbon);
    }
    .po-table thead th.qty-col{text-align:right}
    .po-table td{
        padding:8px 0;font-size:14px;border-bottom:1px solid var(--cloud);
        vertical-align:top;
    }
    .po-table tbody tr:last-child td{border-bottom:none}
    .po-table .item-name{color:var(--graphite);line-height:1.4;word-break:break-word}
    .po-table .item-qty{
        text-align:right;font-size:14px;font-weight:600;color:var(--carbon);
        white-space:nowrap;padding-left:10px;
    }
    mark{background:#FEF08A;color:var(--carbon);border-radius:2px;padding:0 1px}

    /* ===== State ===== */
    .state{
        text-align:center;color:var(--pewter);font-size:15px;line-height:1.7;
        padding:80px 24px;
    }
    .state .icon{font-size:40px;margin-bottom:12px;opacity:.85}
    .spinner{
        width:32px;height:32px;border:3px solid var(--cloud);
        border-top-color:var(--blue);border-radius:50%;
        margin:0 auto 14px;animation:spin .8s linear infinite;
    }
    .err{color:var(--graphite)}
</style>
</head>
<body>

<div class="topbar">
    <h1>
        PO Taobao
        <button type="button" class="refresh-btn" id="refreshBtn" onclick="loadData()" title="รีเฟรช">⟳</button>
    </h1>
    <div class="vendor-badge" id="vendorBadge">
        <span class="v-code">—</span>
        <span class="v-name">กำลังโหลดข้อมูล...</span>
    </div>
    <div class="searchrow">
        <input type="text" id="searchInput" placeholder="ค้นหาชื่อสินค้า หรือเลขที่ PO"
               autocomplete="off" oninput="onSearch()">
        <button type="button" class="clear-btn" id="clearBtn" onclick="clearSearch()">✕</button>
    </div>
</div>

<div class="content">
    <div class="summary-row" id="summaryRow" style="display:none"></div>
    <div id="stateBox" class="state">
        <div class="spinner"></div>
        กำลังโหลดรายการ PO...
    </div>
    <div id="poGrid" style="display:none"></div>
</div>

<script>
/* ปรับ VendorCode ได้ผ่าน query string ของหน้านี้เอง เช่น ?VendorCode=VEN-13383
   ถ้าไม่ระบุ จะ default เป็น VEN-13383 */
const params = new URLSearchParams(window.location.search);
const VENDOR_CODE = params.get('VendorCode') || 'VEN-13383';
const API_URL = `http://server_update:8000/api/getProductVender?VendorCode=${encodeURIComponent(VENDOR_CODE)}`;

/* หน้ารับสินค้าเข้า (PO) — คลิกเลขที่ PO แล้วพาไปหน้านี้พร้อมค้นหาเลขที่กดไปด้วย
   หน้าปลายทาง (po.mobile_app) รับ PONum แบบ "ไม่มี prefix PO" (เช่น 6905-01975)
   ส่วน po.DocuNo ที่ได้จาก API ตัวนี้จะมี prefix "PO" นำหน้าอยู่ (เช่น PO6905-01975) ต้องตัดออกก่อน */
const PO_RECEIVE_URL = (docuNo) => `/mobile-app?PONum=${encodeURIComponent(String(docuNo).replace(/^PO/i, ''))}`;

const $ = id => document.getElementById(id);

let rawData = null; // ผลลัพธ์ดิบจาก API

async function loadData(){
    $('refreshBtn').classList.add('spin');
    $('stateBox').style.display = 'block';
    $('stateBox').innerHTML = '<div class="spinner"></div>กำลังโหลดรายการ PO...';
    $('poGrid').style.display = 'none';
    $('summaryRow').style.display = 'none';

    try{
        const res = await fetch(API_URL);
        if(!res.ok){
            const body = await res.json().catch(() => null);
            throw new Error((body && body.message) || ('HTTP ' + res.status));
        }
        const data = await res.json();
        rawData = data;

        $('vendorBadge').innerHTML = `
            <span class="v-code">${esc(data.VendorCode || VENDOR_CODE)}</span>
            <span class="v-name">${esc(data.VendorName || '-')}</span>`;

        render();
    }catch(err){
        rawData = null;
        $('vendorBadge').innerHTML = `
            <span class="v-code">${esc(VENDOR_CODE)}</span>
            <span class="v-name">โหลดข้อมูลไม่สำเร็จ</span>`;
        $('poGrid').style.display = 'none';
        $('summaryRow').style.display = 'none';
        $('stateBox').style.display = 'block';
        $('stateBox').innerHTML =
            '<div class="icon">⚠️</div>' +
            '<span class="err" style="font-size:16px;font-weight:500;color:var(--carbon)">เชื่อมต่อ API ไม่สำเร็จ</span><br>' +
            esc(err.message) +
            '<br><br><button type="button" class="clear-btn show" style="position:static;width:auto;height:36px;padding:0 16px;border-radius:6px" onclick="loadData()">ลองใหม่</button>';
    }finally{
        $('refreshBtn').classList.remove('spin');
    }
}

function onSearch(){
    $('clearBtn').classList.toggle('show', $('searchInput').value.trim().length > 0);
    render();
}
function clearSearch(){
    $('searchInput').value = '';
    $('clearBtn').classList.remove('show');
    render();
}

function statusLabel(status){
    const s = (status || '').toUpperCase();
    if(s === 'ENTRY')   return {text:'ยังไม่รับเข้า', cls:'status-entry'};
    if(s === 'PARTIAL') return {text:'รับบางส่วน',   cls:'status-partial'};
    if(!s)               return {text:'-', cls:'status-other'};
    return {text:s, cls:'status-other'};
}

function render(){
    if(!rawData){ return; }
    const list = rawData.POList || [];
    const q = $('searchInput').value.trim().toLowerCase();

    // กรอง: ถ้าค้นด้วยเลข PO ให้โชว์ทุกรายการในใบนั้น
    // ถ้าค้นด้วยชื่อสินค้า ให้โชว์เฉพาะรายการสินค้าที่ตรง
    const filtered = list
        .map(po => {
            const docuMatch = q && (po.DocuNo || '').toLowerCase().includes(q);
            const items = (po.Items || []).filter(it =>
                !q || docuMatch || (it.GoodName || '').toLowerCase().includes(q)
            );
            return { po, items };
        })
        .filter(x => x.items.length > 0)
        .sort((a, b) => (b.po.DocuNo || '').localeCompare(a.po.DocuNo || ''));

    const totalItems = filtered.reduce((sum, x) => sum + x.items.length, 0);

    $('summaryRow').style.display = 'flex';
    $('summaryRow').innerHTML = q
        ? `พบ <b>${filtered.length}</b> ใบ / <b>${totalItems}</b> รายการสินค้า`
        : `PO ทั้งหมด <b>${rawData.POCount ?? list.length}</b> ใบ`;

    if(filtered.length === 0){
        $('poGrid').style.display = 'none';
        $('stateBox').style.display = 'block';
        $('stateBox').innerHTML = q
            ? '<div class="icon">🔍</div>ไม่พบสินค้าหรือเลขที่ PO ที่ตรงกับ<br>"' + esc($('searchInput').value.trim()) + '"'
            : '<div class="icon">✅</div>ไม่มี PO ที่ค้างรับเข้าสำหรับ vendor นี้';
        return;
    }

    $('stateBox').style.display = 'none';
    $('poGrid').style.display = 'block';

    $('poGrid').innerHTML = filtered.map(({po, items}) => {
        const st = statusLabel(po.store_status);
        const docuNo = po.DocuNo || '-';
        return `
        <div class="po-card ${st.cls}">
            <div class="po-card-head">
                <a class="po-docu-link" href="${esc(PO_RECEIVE_URL(docuNo))}" title="ไปหน้ารับสินค้าเข้า (PO) ${esc(docuNo)}">${highlight(docuNo, q)}</a>
                <span class="po-badge ${st.cls}">${esc(st.text)}</span>
            </div>
            <div class="po-meta">
                ${po.SONum ? `<div>SO <b>${esc(po.SONum)}</b></div>` : ''}
                ${po.CustomerName ? `<div class="cust-name">ชื่อลูกค้า ${esc(po.CustomerName)}</div>` : ''}
            </div>
            <div class="po-items">
                <table class="po-table">
                    <thead>
                        <tr>
                            <th>ชื่อ</th>
                            <th class="qty-col">จำนวน</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map(it => `
                            <tr>
                                <td class="item-name">${highlight(it.GoodName || '-', q)}</td>
                                <td class="item-qty">${fmtQty(it.GoodQty2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        </div>`;
    }).join('');
}

/* ========== Helpers ========== */
function fmtQty(v){
    const n = parseFloat(v || 0);
    return (n % 1 === 0 ? String(n) : n.toFixed(2));
}
function esc(s){
    return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}
function highlight(text, q){
    text = String(text ?? '-');
    if(!q) return esc(text);
    const idx = text.toLowerCase().indexOf(q);
    if(idx === -1) return esc(text);
    const before = text.slice(0, idx);
    const match  = text.slice(idx, idx + q.length);
    const after  = text.slice(idx + q.length);
    return esc(before) + '<mark>' + esc(match) + '</mark>' + esc(after);
}

loadData();
</script>
</body>
</html>