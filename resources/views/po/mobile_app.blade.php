<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>รับสินค้าเข้า (PO)</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root{
        --navy:#16324a;
        --navy-2:#1f4364;
        --accent:#f5a623;
        --green:#1e9e6a;
        --green-dark:#187f55;
        --bg:#eef1f4;
        --card:#ffffff;
        --line:#e3e8ee;
        --text:#22303c;
        --muted:#7b8794;
        --danger:#d64545;
    }
    *{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent}
    body{
        font-family:'Sarabun',sans-serif;
        background:var(--bg);
        color:var(--text);
        min-height:100vh;
        padding-bottom:110px; /* เผื่อที่ให้แถบปุ่มรับเข้าด้านล่าง */
    }

    /* ===== Header + ช่องค้นหา ===== */
    .topbar{
        background:linear-gradient(160deg,var(--navy) 0%,var(--navy-2) 100%);
        padding:18px 16px 22px;
        border-radius:0 0 22px 22px;
        position:sticky;top:0;z-index:20;
        box-shadow:0 4px 14px rgba(22,50,74,.25);
    }
    .topbar h1{
        color:#fff;font-size:17px;font-weight:600;
        display:flex;align-items:center;gap:8px;margin-bottom:14px;
    }
    .topbar h1 .badge{
        background:var(--accent);color:#4a3000;
        font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;
    }
    .searchrow{display:flex;gap:8px}
    .searchrow input{
        flex:1;height:48px;border:none;border-radius:12px;
        padding:0 14px;font-size:16px;font-family:inherit;
        outline:none;background:#fff;
    }
    .searchrow button{
        height:48px;min-width:86px;border:none;border-radius:12px;
        background:var(--accent);color:#4a3000;font-size:15px;font-weight:700;
        font-family:inherit;cursor:pointer;
        display:flex;align-items:center;justify-content:center;gap:6px;
    }
    .searchrow button:active{transform:scale(.97)}
    .searchrow button:disabled{opacity:.6}

    /* ===== ส่วนหัวเอกสาร PO ===== */
    .po-head{
        margin:14px 12px 0;background:var(--card);
        border:1px solid var(--line);border-radius:14px;padding:14px;
    }
    .po-head .docu{font-size:17px;font-weight:700;color:var(--navy)}
    .po-head .vendor{font-size:13px;color:var(--text);margin-top:4px;line-height:1.45}
    .po-head .meta{
        display:flex;gap:14px;margin-top:10px;padding-top:10px;
        border-top:1px dashed var(--line);
        font-size:12px;color:var(--muted);flex-wrap:wrap;
    }
    .po-head .meta b{color:var(--text);font-weight:600}

    /* ===== รายการสินค้า ===== */
    .list-title{
        margin:16px 14px 8px;font-size:13px;font-weight:600;color:var(--muted);
        display:flex;justify-content:space-between;align-items:center;
    }
    .select-all{
        font-size:13px;color:var(--navy);font-weight:600;
        background:none;border:none;font-family:inherit;cursor:pointer;
    }
    .item{
        margin:0 12px 10px;background:var(--card);
        border:1.5px solid var(--line);border-radius:14px;
        padding:12px;display:flex;gap:12px;align-items:flex-start;
        transition:border-color .15s, background .15s;
    }
    .item.checked{border-color:var(--green);background:#f2fbf7}
    .item input[type=checkbox]{
        width:24px;height:24px;flex-shrink:0;margin-top:4px;
        accent-color:var(--green);
    }
    .item .info{flex:1;min-width:0}
    .item .gname{font-size:14.5px;font-weight:600;line-height:1.4;word-break:break-word}
    .item .gcode{font-size:11.5px;color:var(--muted);margin-top:2px;word-break:break-all}
    .item .price{font-size:12.5px;color:var(--muted);margin-top:4px}
    .item .price b{color:var(--navy)}

    .qtybox{display:flex;flex-direction:column;align-items:center;gap:4px;flex-shrink:0}
    .qtybox label{font-size:11px;color:var(--muted)}
    .qty-ctrl{display:flex;align-items:center;gap:0;border:1.5px solid var(--line);border-radius:10px;overflow:hidden;background:#fff}
    .qty-ctrl button{
        width:34px;height:40px;border:none;background:#f4f6f8;
        font-size:20px;color:var(--navy);cursor:pointer;font-family:inherit;
    }
    .qty-ctrl button:active{background:#e3e8ee}
    .qty-ctrl input{
        width:56px;height:40px;border:none;text-align:center;
        font-size:16px;font-weight:700;font-family:inherit;outline:none;color:var(--text);
    }
    .qty-ctrl input:focus{background:#fffbe9}

    /* ===== สถานะต่าง ๆ ===== */
    .state{
        margin:60px 24px;text-align:center;color:var(--muted);font-size:14px;line-height:1.7;
    }
    .state .icon{font-size:44px;margin-bottom:10px}
    .spinner{
        width:34px;height:34px;border:4px solid var(--line);
        border-top-color:var(--navy);border-radius:50%;
        margin:0 auto 12px;animation:spin .8s linear infinite;
    }
    @keyframes spin{to{transform:rotate(360deg)}}
    .err{color:var(--danger)}

    /* ===== แถบปุ่มรับเข้า (ล่างจอ) ===== */
    .receive-bar{
        position:fixed;left:0;right:0;bottom:0;z-index:30;
        background:#fff;border-top:1px solid var(--line);
        padding:12px 14px calc(12px + env(safe-area-inset-bottom));
        display:none;gap:12px;align-items:center;
        box-shadow:0 -4px 16px rgba(0,0,0,.08);
    }
    .receive-bar.show{display:flex}
    .receive-bar .count{font-size:13px;color:var(--muted);line-height:1.4}
    .receive-bar .count b{font-size:17px;color:var(--navy)}
    .btn-receive{
        flex:1;height:52px;border:none;border-radius:14px;
        background:var(--green);color:#fff;font-size:17px;font-weight:700;
        font-family:inherit;cursor:pointer;
        display:flex;align-items:center;justify-content:center;gap:8px;
    }
    .btn-receive:active{background:var(--green-dark)}
    .btn-receive:disabled{background:#b8c4ce}

    /* ===== Toast ===== */
    #toast{
        position:fixed;left:50%;bottom:130px;transform:translateX(-50%) translateY(20px);
        background:var(--navy);color:#fff;font-size:14px;
        padding:12px 20px;border-radius:30px;opacity:0;pointer-events:none;
        transition:all .25s;z-index:50;white-space:nowrap;max-width:90vw;
        overflow:hidden;text-overflow:ellipsis;
    }
    #toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
    #toast.ok{background:var(--green)}
    #toast.error{background:var(--danger)}
</style>
</head>
<body>

<!-- ส่วนหัว + ค้นหา -->
<div class="topbar">
    <h1>📦 รับสินค้าเข้า <span class="badge">PO</span></h1>
    <div class="searchrow">
        <input type="text" id="poInput" placeholder="พิมพ์เลขที่ PO"
               inputmode="text" autocomplete="off" onkeydown="if(event.key==='Enter')searchPO()">
        <button id="btnSearch" onclick="searchPO()">🔍 ค้นหา</button>
    </div>
</div>

<!-- ส่วนหัวเอกสาร -->
<div id="poHead"></div>

<!-- รายการสินค้า -->
<div id="listTitle" class="list-title" style="display:none">
    <span id="itemCountLabel">รายการสินค้า</span>
    <button class="select-all" onclick="toggleAll()">เลือกทั้งหมด</button>
</div>
<div id="itemList"></div>

<!-- สถานะเริ่มต้น / โหลด / error -->
<div id="stateBox" class="state">
    <div class="icon">🔎</div>
    พิมพ์เลขที่ PO ด้านบน แล้วกดค้นหา<br>เพื่อดึงรายการสินค้ามารับเข้า
</div>

<!-- แถบปุ่มรับเข้า -->
<div class="receive-bar" id="receiveBar">
    <div class="count">เลือกแล้ว<br><b id="selCount">0</b> รายการ</div>
    <button class="btn-receive" id="btnReceive" onclick="receiveItems()">✅ รับเข้า</button>
</div>

<div id="toast"></div>

<script>
// เรียกผ่าน route ของ Laravel (Controller เป็นคน proxy ไปหา server_update ให้ กัน CORS)
const API_URL = '{{ url('/api/getPODetail') }}';
const RECEIVE_URL = '{{ url('/api/receivePO') }}';
const CSRF_TOKEN = '{{ csrf_token() }}';

let currentPO = null;   // เก็บข้อมูล PO ที่ค้นเจอล่าสุด

const $ = id => document.getElementById(id);

/* ---------- ค้นหา PO ---------- */
async function searchPO(){
    const poNumber = $('poInput').value.trim();
    if(!poNumber){ toast('กรุณาพิมพ์เลขที่ PO ก่อน','error'); return; }

    $('btnSearch').disabled = true;
    clearResult();
    $('stateBox').innerHTML = '<div class="spinner"></div>กำลังค้นหา ' + esc(poNumber) + ' ...';
    $('stateBox').style.display = 'block';

    try{
        const res = await fetch(`${API_URL}?PONum=${encodeURIComponent(poNumber)}`);
        if(!res.ok) throw new Error('HTTP ' + res.status);
        let data = await res.json();

        // รองรับทั้งกรณี API ส่ง object ตรง ๆ หรือห่อมาใน data / array
        if(Array.isArray(data)) data = data[0];
        if(data && data.data) data = Array.isArray(data.data) ? data.data[0] : data.data;

        if(!data || !data.ms_podt || data.ms_podt.length === 0){
            $('stateBox').innerHTML = '<div class="icon">❌</div><span class="err">ไม่พบข้อมูล PO เลขที่ ' + esc(poNumber) + '</span>';
            return;
        }
        currentPO = data;
        renderPO(data);
    }catch(err){
        $('stateBox').innerHTML = '<div class="icon">⚠️</div><span class="err">เชื่อมต่อ server ไม่ได้<br>' + esc(err.message) + '</span>';
    }finally{
        $('btnSearch').disabled = false;
    }
}

/* ---------- แสดงผล ---------- */
function renderPO(po){
    $('stateBox').style.display = 'none';

    $('poHead').innerHTML = `
        <div class="po-head">
            <div class="docu">${esc(po.DocuNo || '-')}</div>
            <div class="vendor">${esc(po.VendorName || po.VendorNameEng || '-')}</div>
            <div class="meta">
                <span>วันที่: <b>${fmtDate(po.DocuDate)}</b></span>
                <span>กำหนดส่ง: <b>${fmtDate(po.ShipDate)}</b></span>
                <span>ยอดสุทธิ: <b>${fmtNum(po.NetAmnt)} ฿</b></span>
            </div>
        </div>`;

    const items = po.ms_podt || [];
    $('itemCountLabel').textContent = `รายการสินค้า (${items.length})`;
    $('listTitle').style.display = 'flex';

    $('itemList').innerHTML = items.map((it, i) => {
        const {name, code} = splitGoodName(it.GoodName);
        // จำนวน fix มาจากใบ PO (แก้ไขได้)
        const qty = parseFloat(it.AppvQty2 || it.GoodQty2 || 0);
        return `
        <div class="item" id="item-${i}">
            <input type="checkbox" id="chk-${i}" onchange="onCheck(${i})">
            <div class="info" onclick="toggleItem(${i})">
                <div class="gname">${esc(name)}</div>
                ${code ? `<div class="gcode">${esc(code)}</div>` : ''}
                <div class="price">ราคา/หน่วย <b>${fmtNum(it.GoodPrice2)}</b> ฿ · รวม <b>${fmtNum(it.GoodAmnt)}</b> ฿</div>
            </div>
            <div class="qtybox">
                <label>จำนวนรับ</label>
                <div class="qty-ctrl">
                    <button type="button" onclick="stepQty(${i},-1)">−</button>
                    <input type="number" id="qty-${i}" value="${qty}" min="0" inputmode="decimal" onclick="event.stopPropagation()">
                    <button type="button" onclick="stepQty(${i},1)">+</button>
                </div>
            </div>
        </div>`;
    }).join('');

    $('receiveBar').classList.add('show');
    updateCount();
}

/* ---------- checkbox / จำนวน ---------- */
function onCheck(i){
    $('item-'+i).classList.toggle('checked', $('chk-'+i).checked);
    updateCount();
}
function toggleItem(i){
    const c = $('chk-'+i);
    c.checked = !c.checked;
    onCheck(i);
}
function toggleAll(){
    if(!currentPO) return;
    const items = currentPO.ms_podt;
    const allChecked = items.every((_,i)=> $('chk-'+i).checked);
    items.forEach((_,i)=>{ $('chk-'+i).checked = !allChecked; onCheck(i); });
}
function stepQty(i, d){
    const inp = $('qty-'+i);
    let v = parseFloat(inp.value || 0) + d;
    if(v < 0) v = 0;
    inp.value = v;
    // กด +/- แล้วติ๊กเลือกให้อัตโนมัติ
    if(!$('chk-'+i).checked){ $('chk-'+i).checked = true; onCheck(i); }
}
function updateCount(){
    if(!currentPO) return;
    const n = currentPO.ms_podt.filter((_,i)=> $('chk-'+i).checked).length;
    $('selCount').textContent = n;
    $('btnReceive').disabled = n === 0;
}

/* ---------- กดรับเข้า ---------- */
async function receiveItems(){
    const selected = currentPO.ms_podt
        .map((it,i)=> ({it,i}))
        .filter(x => $('chk-'+x.i).checked)
        .map(x => ({
            POID:    x.it.POID,
            ListNo:  x.it.ListNo,
            GoodID:  x.it.GoodID,
            GoodName:x.it.GoodName,
            RecvQty: parseFloat($('qty-'+x.i).value || 0)
        }));

    if(selected.length === 0){ toast('ยังไม่ได้เลือกรายการ','error'); return; }
    if(selected.some(s => s.RecvQty <= 0)){ toast('จำนวนรับต้องมากกว่า 0','error'); return; }

    if(!confirm(`ยืนยันรับเข้า ${selected.length} รายการ จาก ${currentPO.DocuNo} ?`)) return;

    $('btnReceive').disabled = true;
    $('btnReceive').textContent = 'กำลังบันทึก...';

    try{
        const res = await fetch(RECEIVE_URL, {
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept':'application/json'
            },
            body: JSON.stringify({
                PONum:  currentPO.DocuNo,
                POID:   currentPO.POID,
                items:  selected
            })
        });
        if(!res.ok) throw new Error('HTTP ' + res.status);
        toast(`✅ รับเข้าสำเร็จ ${selected.length} รายการ`,'ok');

        // เอา checkbox ออกหลังบันทึกสำเร็จ
        selected.forEach(s=>{
            const i = currentPO.ms_podt.findIndex(it=>it.ListNo===s.ListNo);
            if(i>-1){ $('chk-'+i).checked=false; onCheck(i); }
        });
    }catch(err){
        toast('บันทึกไม่สำเร็จ: ' + err.message,'error');
    }finally{
        $('btnReceive').disabled = false;
        $('btnReceive').innerHTML = '✅ รับเข้า';
        updateCount();
    }
}

/* ---------- helpers ---------- */
// แยกชื่อสินค้าออกจากรหัสท้ายชื่อ เช่น ++--++C.11362++S.014472++--++
function splitGoodName(raw){
    if(!raw) return {name:'-', code:''};
    const idx = raw.indexOf('++');
    if(idx === -1) return {name: raw.trim(), code:''};
    return {
        name: raw.substring(0, idx).trim(),
        code: raw.substring(idx).replace(/\+\+|--/g,' ').replace(/\s+/g,' ').trim()
    };
}
function fmtNum(v){
    const n = parseFloat(v || 0);
    return n.toLocaleString('th-TH',{minimumFractionDigits:2, maximumFractionDigits:2});
}
function fmtDate(d){
    if(!d) return '-';
    const dt = new Date(d.replace(' ','T'));
    if(isNaN(dt)) return d.split(' ')[0] || '-';
    return dt.toLocaleDateString('th-TH',{day:'numeric',month:'short',year:'2-digit'});
}
function esc(s){
    return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}
function clearResult(){
    currentPO = null;
    $('poHead').innerHTML = '';
    $('itemList').innerHTML = '';
    $('listTitle').style.display = 'none';
    $('receiveBar').classList.remove('show');
}
let toastTimer;
function toast(msg, type=''){
    const t = $('toast');
    t.textContent = msg;
    t.className = 'show ' + type;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(()=> t.className='', 2600);
}
</script>
</body>
</html>