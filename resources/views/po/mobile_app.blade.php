<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>รับสินค้าเข้า (PO)</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500&display=swap" rel="stylesheet">
<style>
    /* ===== Tesla-inspired tokens ===== */
    :root{
        --blue:#3E6AE1;          /* Electric Blue */
        --blue-dark:#3457B1;
        --canvas:#FFFFFF;        /* Pure White */
        --ash:#F4F4F4;           /* Light Ash */
        --carbon:#171A20;        /* Carbon Dark */
        --graphite:#393C41;      /* Graphite */
        --pewter:#5C5E62;        /* Pewter */
        --silver:#8E8E8E;        /* Silver Fog */
        --cloud:#EEEEEE;         /* Cloud Gray */
        --pale:#D0D1D2;          /* Pale Silver */
        --mobile-w:480px;
        --r:4px;
        --t:0.33s;
    }
    *{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent}
    html{background:#E9E9E9}
    body{
        font-family:'Sarabun',-apple-system,Arial,sans-serif;
        background:var(--ash);
        color:var(--graphite);
        min-height:100vh;
        padding-bottom:110px;
        max-width:var(--mobile-w);
        margin:0 auto;
        position:relative;
        font-weight:400;
    }

    /* ===== Header ===== */
    .topbar{
        background:rgba(244,244,244,0.8);
        backdrop-filter:blur(12px);
        -webkit-backdrop-filter:blur(12px);
        padding:20px 16px 16px;
        position:sticky;top:0;z-index:20;
    }
    .topbar h1{
        color:var(--carbon);font-size:22px;font-weight:500;
        display:flex;align-items:center;gap:10px;margin-bottom:14px;
    }
    .topbar h1 .badge{
        color:var(--pewter);font-size:13px;font-weight:400;
    }
    .searchrow{display:flex;gap:8px}
    .searchrow input{
        flex:1;height:40px;
        border:1px solid var(--pale);border-radius:var(--r);
        padding:0 12px;font-size:15px;font-family:inherit;
        outline:none;background:var(--canvas);min-width:0;color:var(--carbon);
        transition:border-color var(--t);
    }
    .searchrow input:focus{border-color:var(--blue)}
    .searchrow input::placeholder{color:var(--silver)}
    .searchrow button{
        height:40px;min-width:100px;
        border:none;border-radius:var(--r);
        background:var(--blue);color:#FFFFFF;
        font-size:14px;font-weight:500;font-family:inherit;cursor:pointer;
        transition:background-color var(--t);
    }
    .searchrow button:active{background:var(--blue-dark)}
    .searchrow button:disabled{background:var(--pale);color:#fff}

    /* ===== Stepper Progress Bar ===== */
    .stepper{
        display:none;
        background:var(--canvas);
        margin:8px 16px 0;
        padding:12px 16px;
        border-radius:var(--r);
        justify-content:space-between;
        align-items:center;
        border-bottom:1px solid var(--cloud);
    }
    .stepper.show{display:flex}
    .step-item{
        display:flex;align-items:center;gap:6px;
        font-size:13px;color:var(--silver);font-weight:400;
    }
    .step-item .num{
        width:22px;height:22px;border-radius:50%;
        background:var(--cloud);color:var(--pewter);
        display:flex;align-items:center;justify-content:center;
        font-size:12px;font-weight:500;
    }
    .step-item.active{color:var(--blue);font-weight:500}
    .step-item.active .num{background:var(--blue);color:#fff}
    .step-item.done{color:var(--carbon)}
    .step-item.done .num{background:var(--carbon);color:#fff}
    .step-line{flex:1;height:2px;background:var(--cloud);margin:0 8px}

    /* ===== PO Head ===== */
    .po-head{
        margin:8px 16px 0;background:var(--canvas);
        border-radius:var(--r);padding:16px;
    }
    .po-head .docu{font-size:17px;font-weight:500;color:var(--carbon)}
    .po-head .vendor{font-size:14px;font-weight:400;color:var(--graphite);margin-top:4px;line-height:1.45}
    .po-head .meta{
        display:flex;gap:16px;margin-top:12px;padding-top:12px;
        border-top:1px solid var(--cloud);
        font-size:13px;color:var(--pewter);flex-wrap:wrap;
    }
    .po-head .meta b{color:var(--carbon);font-weight:500}
    .po-head .meta .v-amnt b{color:var(--blue)}

    /* ===== Step Containers ===== */
    .step-page{display:none}
    .step-page.active{display:block}

    /* ===== Step 1: Items List ===== */
    .list-title{
        margin:16px 16px 10px;
        display:flex;justify-content:space-between;align-items:center;
    }
    #itemCountLabel{font-size:14px;font-weight:500;color:var(--carbon)}
    .select-all{
        font-size:14px;color:var(--pewter);font-weight:400;
        background:none;border:none;font-family:inherit;cursor:pointer;
        transition:color var(--t);
    }
    .select-all:active{color:var(--blue)}
    .item{
        margin:0 16px 8px;background:var(--canvas);
        border:1px solid transparent;border-radius:var(--r);
        padding:14px;display:flex;gap:12px;align-items:flex-start;
        transition:border-color var(--t), background-color var(--t);
    }
    .item.checked{border-color:var(--blue);background:#F5F8FE}
    .item input[type=checkbox]{
        width:22px;height:22px;flex-shrink:0;
        align-self:center;
        accent-color:var(--blue);
    }
    .item .info{flex:1;min-width:0}
    .item .gname{font-size:14px;font-weight:500;line-height:1.4;word-break:break-word;color:var(--carbon)}
    .item .gcode{font-size:12px;font-weight:400;color:var(--pewter);margin-top:2px;word-break:break-all}
    .item .price{font-size:12.5px;font-weight:400;color:var(--pewter);margin-top:4px}
    .item .price .unit{color:var(--blue);font-weight:500}
    .item .price .total{color:var(--blue);font-weight:500}

    .qtybox{display:flex;flex-direction:column;align-items:center;gap:4px;flex-shrink:0}
    .qtybox label{font-size:12px;font-weight:400;color:var(--pewter)}
    .qty-ctrl{
        display:flex;align-items:center;gap:0;
        border:1px solid var(--pale);border-radius:var(--r);
        overflow:hidden;background:var(--canvas);
    }
    .qty-ctrl button{
        width:36px;height:38px;border:none;background:var(--ash);
        font-size:18px;color:var(--carbon);cursor:pointer;font-family:inherit;
        transition:background-color var(--t);
    }
    .qty-ctrl button:active{background:var(--cloud)}
    .qty-ctrl input{
        width:52px;height:38px;border:none;text-align:center;
        font-size:15px;font-weight:500;font-family:inherit;outline:none;color:var(--carbon);
        background:var(--canvas);
    }
    .qty-ctrl input:focus{background:var(--ash)}
    .qty-ctrl input::-webkit-outer-spin-button,
    .qty-ctrl input::-webkit-inner-spin-button{
        -webkit-appearance:none;margin:0;
    }
    .qty-ctrl input[type=number]{
        -moz-appearance:textfield;appearance:textfield;
    }

    /* ===== Step 2 & 3 Sections ===== */
    .card-section{
        margin:16px;background:var(--canvas);
        border-radius:var(--r);padding:20px 16px;
    }
    .card-section .lbl{font-size:15px;font-weight:500;color:var(--carbon);margin-bottom:12px}
    .card-section .lbl small{color:var(--pewter);font-weight:400;font-size:13px}

    .shelf-input{
        width:100%;height:44px;
        border:1px solid var(--pale);border-radius:var(--r);
        padding:0 14px;font-size:15px;font-family:inherit;
        outline:none;background:var(--canvas);color:var(--carbon);
        transition:border-color var(--t);
    }
    .shelf-input:focus{border-color:var(--blue)}
    .shelf-input::placeholder{color:var(--silver)}

    .photo-row{display:flex;align-items:center;gap:16px;margin-top:8px}
    .photo-preview{
        width:90px;height:90px;border-radius:var(--r);flex-shrink:0;
        background:var(--ash);border:1px dashed var(--pale);
        display:flex;align-items:center;justify-content:center;overflow:hidden;
    }
    .photo-preview img{width:100%;height:100%;object-fit:cover}
    .photo-preview .ph-icon{font-size:32px;color:var(--silver)}
    .photo-actions{display:flex;flex-direction:column;gap:10px;flex:1}
    .photo-actions button{
        height:40px;border:1px solid var(--pale);border-radius:var(--r);
        background:var(--canvas);color:var(--carbon);font-size:14px;font-weight:500;
        font-family:inherit;cursor:pointer;transition:background-color var(--t), border-color var(--t);
    }
    .photo-actions button.primary{background:var(--blue);color:#fff;border-color:var(--blue)}
    .photo-actions button.primary:active{background:var(--blue-dark)}
    .photo-actions button.remove{color:var(--pewter)}

    /* Summary Selected Items in Step 2 & 3 */
    .summary-box{
        margin:0 16px;padding:12px 14px;background:#EBF1FF;
        border-radius:var(--r);font-size:13.5px;color:var(--graphite);
    }
    .summary-box b{color:var(--blue)}

    /* ===== State Box ===== */
    .state{
        position:fixed;
        top:50%;left:50%;
        transform:translate(-50%,-50%);
        width:calc(100% - 48px);
        max-width:calc(var(--mobile-w) - 48px);
        text-align:center;color:var(--pewter);font-size:14px;line-height:1.7;
        z-index:10;
    }
    .state .icon{font-size:40px;margin-bottom:12px;opacity:.85}
    .spinner{
        width:32px;height:32px;border:3px solid var(--cloud);
        border-top-color:var(--blue);border-radius:50%;
        margin:0 auto 14px;animation:spin .8s linear infinite;
    }
    @keyframes spin{to{transform:rotate(360deg)}}
    .err{color:var(--graphite)}

    /* ===== Bottom Navigation Bar ===== */
    .nav-bar{
        position:fixed;bottom:0;z-index:30;
        left:50%;transform:translateX(-50%);
        width:100%;max-width:var(--mobile-w);
        background:rgba(255,255,255,0.9);
        backdrop-filter:blur(12px);
        -webkit-backdrop-filter:blur(12px);
        border-top:1px solid var(--cloud);
        padding:12px 16px calc(12px + env(safe-area-inset-bottom));
        display:none;gap:10px;align-items:center;
    }
    .nav-bar.show{display:flex}
    .nav-bar .btn-nav{
        height:44px;border:none;border-radius:var(--r);
        font-size:14px;font-weight:500;font-family:inherit;cursor:pointer;
        transition:background-color var(--t);
        display:flex;align-items:center;justify-content:center;gap:6px;
    }
    .btn-back{
        background:var(--ash);color:var(--carbon);
        border:1px solid var(--pale) !important;
        padding:0 18px;
    }
    .btn-back:active{background:var(--cloud)}
    .btn-next{
        flex:1;background:var(--blue);color:#FFFFFF;
    }
    .btn-next:active{background:var(--blue-dark)}
    .btn-next:disabled{background:var(--pale);cursor:not-allowed}

    /* ===== Toast ===== */
    #toast{
        position:fixed;left:50%;bottom:120px;transform:translateX(-50%) translateY(20px);
        background:var(--carbon);color:#FFFFFF;font-size:13px;font-weight:400;
        padding:11px 20px;border-radius:var(--r);opacity:0;pointer-events:none;
        transition:all .25s;z-index:50;
        max-width:calc(var(--mobile-w) - 40px);
        white-space:normal;word-break:break-word;text-align:center;line-height:1.5;
    }
    #toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
    #toast.ok{background:var(--blue)}
    #toast.error{background:var(--carbon)}
</style>
</head>
<body>

<!-- ส่วนหัว + ค้นหา -->
<div class="topbar">
    <h1>รับสินค้าเข้า <span class="badge">Purchase Order</span></h1>
    <div class="searchrow">
        <input type="text" id="poInput" placeholder="พิมพ์เลขที่ PO"
               inputmode="text" autocomplete="off" onkeydown="if(event.key==='Enter')searchPO()">
        <button id="btnSearch" onclick="searchPO()">ค้นหา</button>
    </div>
</div>

<!-- Stepper Progress Bar -->
<div class="stepper" id="stepper">
    <div class="step-item active" id="st-1">
        <span class="num">1</span> เลือกสินค้า
    </div>
    <div class="step-line"></div>
    <div class="step-item" id="st-2">
        <span class="num">2</span> ระบุชั้นวาง
    </div>
    <div class="step-line"></div>
    <div class="step-item" id="st-3">
        <span class="num">3</span> ถ่ายรูป
    </div>
</div>

<!-- ส่วนหัวเอกสาร -->
<div id="poHead"></div>

<!-- ================= STEP 1: รายการสินค้า ================= -->
<div class="step-page active" id="pageStep1">
    <div id="listTitle" class="list-title" style="display:none">
        <span id="itemCountLabel">รายการสินค้า</span>
        <button class="select-all" onclick="toggleAll()">เลือกทั้งหมด</button>
    </div>
    <div id="itemList"></div>
</div>

<!-- ================= STEP 2: ระบุชั้นวาง ================= -->
<div class="step-page" id="pageStep2">
    <div class="summary-box" id="sumStep2"></div>
    <div class="card-section">
        <div class="lbl">ชั้นวาง / ตำแหน่งจัดเก็บ <small>(ระบุเพื่อความสะดวกในการจัดเก็บ)</small></div>
        <input type="text" id="shelfInput" class="shelf-input" placeholder="พิมพ์ชั้นวาง เช่น A-01"
               autocomplete="off" maxlength="100">
    </div>
</div>

<!-- ================= STEP 3: แนบรูปถ่าย ================= -->
<div class="step-page" id="pageStep3">
    <div class="summary-box" id="sumStep3"></div>
    <div class="card-section">
        <div class="lbl">แนบรูปถ่ายหน้างาน <small>(1 รูป สำหรับการรับเข้าครั้งนี้)</small></div>
        <div class="photo-row">
            <div class="photo-preview" id="photoPreview">
                <span class="ph-icon" id="photoIcon">📷</span>
                <img id="photoImg" style="display:none" alt="รูปถ่ายที่แนบ">
            </div>
            <div class="photo-actions">
                <button type="button" class="primary" onclick="triggerPhoto()">ถ่ายรูป / เลือกรูป</button>
                <button type="button" class="remove" id="btnRemovePhoto" onclick="removePhoto()" style="display:none">ลบรูป</button>
            </div>
        </div>
        <input type="file" id="photoInput" accept="image/*" capture="environment" style="display:none" onchange="onPhotoSelected(event)">
    </div>
</div>

<!-- สถานะเริ่มต้น / โหลด / error -->
<div id="stateBox" class="state">
    <div class="icon">🔎</div>
    พิมพ์เลขที่ PO ด้านบน แล้วกดค้นหา<br>เพื่อดึงรายการสินค้ามารับเข้า
</div>

<!-- แถบปุ่มควบคุมล่างจอ (Nav Bar) -->
<div class="nav-bar" id="navBar">
    <button class="btn-nav btn-back" id="btnBack" onclick="prevStep()">ย้อนกลับ</button>
    <button class="btn-nav btn-next" id="btnNext" onclick="nextStep()">ถัดไป</button>
</div>

<div id="toast"></div>

<script>
const API_URL = '{{ url('/api/getPODetail') }}';
const RECEIVE_URL = '{{ url('/api/receivePO') }}';
const HISTORY_URL = '{{ url('/api/receivePO/history') }}';
const CSRF_TOKEN = '{{ csrf_token() }}';

let currentPO = null;   
let capturedPhoto = null; 
let currentStep = 1;

const $ = id => document.getElementById(id);

function normName(s){
    return String(s || '').trim().toLowerCase().replace(/\s+/g, ' ');
}

async function getReceivedQtyMap(ponum){
    try{
        const res = await fetch(`${HISTORY_URL}?PONum=${encodeURIComponent(ponum)}`);
        if(!res.ok) return new Map();
        const rows = await res.json();
        const map = new Map();
        (rows || []).forEach(r => {
            if(!r.good_name) return;
            const key = normName(r.good_name);
            const qty = parseFloat(r.recv_qty || 0);
            map.set(key, (map.get(key) || 0) + qty);
        });
        return map;
    }catch(e){
        return new Map();
    }
}

/* ---------- จัดการขั้นตอน Step Flow ---------- */
function goToStep(step){
    currentStep = step;

    // อัปเดต stepper UI
    [1, 2, 3].forEach(s => {
        const el = $('st-' + s);
        el.classList.remove('active', 'done');
        if(s === currentStep){
            el.classList.add('active');
        }else if(s < currentStep){
            el.classList.add('done');
        }
    });

    // ซ่อน/แสดง หน้า Step
    $('pageStep1').classList.toggle('active', currentStep === 1);
    $('pageStep2').classList.toggle('active', currentStep === 2);
    $('pageStep3').classList.toggle('active', currentStep === 3);

    // ปรับปุ่มควบคุมด้านล่าง
    $('btnBack').style.display = currentStep === 1 ? 'none' : 'block';

    if(currentStep === 1){
        updateCount();
    } else if(currentStep === 2){
        $('btnNext').textContent = 'ถัดไป';
        $('btnNext').disabled = false;
        const selCount = getSelectedItems().length;
        $('sumStep2').innerHTML = `กำลังทำรายการรับเข้า <b>${selCount}</b> รายการ`;
    } else if(currentStep === 3){
        $('btnNext').textContent = 'บันทึกรับเข้า';
        $('btnNext').disabled = false;
        const selCount = getSelectedItems().length;
        const shelf = $('shelfInput').value.trim() || 'ไม่ได้ระบุ';
        $('sumStep3').innerHTML = `กำลังทำรายการรับเข้า <b>${selCount}</b> รายการ<br>ชั้นวาง: <b>${esc(shelf)}</b>`;
    }
}

function nextStep(){
    if(currentStep === 1){
        const selected = getSelectedItems();
        if(selected.length === 0){
            toast('กรุณาเลือกรายการสินค้าอย่างน้อย 1 รายการ','error');
            return;
        }
        if(selected.some(s => s.RecvQty <= 0)){
            toast('จำนวนรับต้องมากกว่า 0','error');
            return;
        }
        goToStep(2);
    } else if(currentStep === 2){
        goToStep(3);
    } else if(currentStep === 3){
        receiveItems();
    }
}

function prevStep(){
    if(currentStep > 1){
        goToStep(currentStep - 1);
    }
}

function getSelectedItems(){
    if(!currentPO || !currentPO.ms_podt) return [];
    return currentPO.ms_podt
        .map((it,i)=> ({it,i}))
        .filter(x => $('chk-'+x.i) && $('chk-'+x.i).checked)
        .map(x => ({
            GoodID:    x.it.GoodID,
            GoodName:  x.it.GoodName,
            UnitPrice: parseFloat(x.it.GoodPrice2 || 0),
            RecvQty:   parseFloat($('qty-'+x.i).value || 0)
        }));
}

/* ---------- แนบรูปถ่าย ---------- */
function triggerPhoto(){
    $('photoInput').click();
}

function onPhotoSelected(event){
    const file = event.target.files && event.target.files[0];
    event.target.value = ''; 
    if(!file) return;

    compressImage(file, 1600, 0.75).then(dataUrl => {
        capturedPhoto = dataUrl;
        $('photoImg').src = dataUrl;
        $('photoImg').style.display = 'block';
        $('photoIcon').style.display = 'none';
        $('btnRemovePhoto').style.display = 'block';
    }).catch(() => {
        toast('อ่านไฟล์รูปไม่สำเร็จ','error');
    });
}

function removePhoto(){
    capturedPhoto = null;
    $('photoImg').src = '';
    $('photoImg').style.display = 'none';
    $('photoIcon').style.display = 'block';
    $('btnRemovePhoto').style.display = 'none';
}

function compressImage(file, maxDim, quality){
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onerror = reject;
        reader.onload = e => {
            const img = new Image();
            img.onerror = reject;
            img.onload = () => {
                let {width, height} = img;
                if(width > maxDim || height > maxDim){
                    const scale = Math.min(maxDim / width, maxDim / height);
                    width = Math.round(width * scale);
                    height = Math.round(height * scale);
                }
                const canvas = document.createElement('canvas');
                canvas.width = width; canvas.height = height;
                canvas.getContext('2d').drawImage(img, 0, 0, width, height);
                resolve(canvas.toDataURL('image/jpeg', quality));
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}

function showNotFound(poNumber){
    clearResult();
    $('stateBox').innerHTML =
        '<div class="icon">❌</div>' +
        '<span class="err" style="font-size:16px;font-weight:500;color:var(--carbon)">ไม่มีเลข PO นี้</span><br>' +
        esc(poNumber) + '<br>' +
        'กรุณาตรวจสอบเลขที่ PO แล้วค้นหาใหม่';
    $('stateBox').style.display = 'block';
}

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

        if(!res.ok){
            if(res.status === 404 || res.status === 500){
                showNotFound(poNumber);
                return;
            }
            const body = await res.json().catch(() => null);
            throw new Error((body && body.message) || ('HTTP ' + res.status));
        }

        let data = await res.json();

        if(Array.isArray(data)) data = data[0];
        if(data && data.data) data = Array.isArray(data.data) ? data.data[0] : data.data;

        if(!data || !data.ms_podt || data.ms_podt.length === 0){
            showNotFound(poNumber);
            return;
        }

        const receivedQtyMap = await getReceivedQtyMap(data.DocuNo || poNumber);
        data.ms_podt = data.ms_podt
            .map(it => {
                const ordered  = parseFloat(it.AppvQty2 || it.GoodQty2 || 0);
                const received = receivedQtyMap.get(normName(it.GoodName)) || 0;
                return { ...it, _remainingQty: ordered - received };
            })
            .filter(it => it._remainingQty > 0);

        if(data.ms_podt.length === 0){
            clearResult();
            $('stateBox').innerHTML = '<div class="icon">✅</div>สินค้าทั้งหมดของ PO นี้ถูกรับเข้าไปแล้ว';
            $('stateBox').style.display = 'block';
            return;
        }

        currentPO = data;
        renderPO(data);
    }catch(err){
        clearResult();
        $('stateBox').innerHTML = '<div class="icon">⚠️</div><span class="err">เชื่อมต่อ server ไม่ได้<br>' + esc(err.message) + '</span>';
        $('stateBox').style.display = 'block';
    }finally{
        $('btnSearch').disabled = false;
    }
}

/* ---------- แสดงผล PO ---------- */
function renderPO(po){
    $('stateBox').style.display = 'none';

    $('poHead').innerHTML = `
        <div class="po-head">
            <div class="docu">${esc(po.DocuNo || '-')}</div>
            <div class="vendor">${esc(po.VendorName || po.VendorNameEng || '-')}</div>
            <div class="meta">
                <span>วันที่: <b>${fmtDate(po.DocuDate)}</b></span>
                <span>กำหนดส่ง: <b>${fmtDate(po.ShipDate)}</b></span>
                <span class="v-amnt">ยอดสุทธิ: <b>${fmtNum(po.NetAmnt)} ฿</b></span>
            </div>
        </div>`;

    const items = po.ms_podt || [];
    $('itemCountLabel').textContent = `รายการสินค้า (${items.length})`;
    $('listTitle').style.display = 'flex';

    $('itemList').innerHTML = items.map((it, i) => {
        const {name, code} = splitGoodName(it.GoodName);
        const ordered = parseFloat(it.AppvQty2 || it.GoodQty2 || 0);
        const qty = (it._remainingQty !== undefined) ? it._remainingQty : ordered;
        return `
        <div class="item" id="item-${i}">
            <input type="checkbox" id="chk-${i}" onchange="onCheck(${i})">
            <div class="info" onclick="toggleItem(${i})">
                <div class="gname">${esc(name)}</div>
                ${code ? `<div class="gcode">${esc(code)}</div>` : ''}
                <div class="price">ราคา/หน่วย <b class="unit">${fmtNum(it.GoodPrice2)}</b> ฿ · รวม <b class="total">${fmtNum(it.GoodAmnt)}</b> ฿</div>
                ${qty < ordered ? `<div class="gcode">รับไปแล้ว ${fmtQty(ordered - qty)} จาก ${fmtQty(ordered)} · เหลือรับ ${fmtQty(qty)}</div>` : ''}
            </div>
            <div class="qtybox">
                <label>จำนวนรับ</label>
                <div class="qty-ctrl">
                    <button type="button" onclick="stepQty(${i},-1)">−</button>
                    <input type="number" id="qty-${i}" value="${qty}" min="0" max="${qty}" data-max="${qty}" inputmode="decimal" onclick="event.stopPropagation()" onchange="clampQty(${i})">
                    <button type="button" onclick="stepQty(${i},1)">+</button>
                </div>
            </div>
        </div>`;
    }).join('');

    $('stepper').classList.add('show');
    $('navBar').classList.add('show');
    goToStep(1);
}

/* ---------- กดบันทึกรับเข้า (ใน Step 3) ---------- */
async function receiveItems(){
    const selected = getSelectedItems();

    if(selected.length === 0){
        toast('ยังไม่ได้เลือกรายการ','error');
        goToStep(1);
        return;
    }

    if(!capturedPhoto){
        toast('กรุณาถ่ายรูปหรือเลือกรูปแนบก่อนกดบันทึก','error');
        return;
    }

    $('btnNext').disabled = true;
    $('btnNext').textContent = 'กำลังบันทึก...';

    try{
        const res = await fetch(RECEIVE_URL,{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':CSRF_TOKEN,
                'Accept':'application/json'
            },
            body:JSON.stringify({
                PONum: currentPO.DocuNo,
                Shelf: $('shelfInput').value.trim() || null,
                items: selected,
                Photo: capturedPhoto
            })
        });

        const result = await res.json();

        if(!res.ok){
            throw new Error(result.message || ('HTTP '+res.status));
        }

        toast(`รับเข้าสำเร็จ ${selected.length} รายการ`,'ok');
        removePhoto();
        $('shelfInput').value = '';

        selected.forEach(sel => {
            const key = normName(sel.GoodName);
            const idx = currentPO.ms_podt.findIndex(it => normName(it.GoodName) === key);
            if(idx === -1) return;

            const item = currentPO.ms_podt[idx];
            const ordered = parseFloat(item.AppvQty2 || item.GoodQty2 || 0);
            const before = (item._remainingQty !== undefined) ? item._remainingQty : ordered;
            const after = before - sel.RecvQty;

            if(after <= 0){
                currentPO.ms_podt.splice(idx, 1);
            }else{
                item._remainingQty = after;
            }
        });

        if(currentPO.ms_podt.length === 0){
            clearResult();
            $('stateBox').innerHTML = `
                <div class="icon">✅</div>
                สินค้าทั้งหมดของ PO นี้ถูกรับเข้าไปแล้ว
            `;
            $('stateBox').style.display = 'block';
        }else{
            renderPO(currentPO);
        }

    }catch(err){
        toast('บันทึกไม่สำเร็จ : '+err.message,'error');
    }finally{
        $('btnNext').disabled = false;
        $('btnNext').textContent = 'บันทึกรับเข้า';
    }
}

/* ---------- Checkbox Handlers ---------- */
function onCheck(i){
    const chk = $('chk-'+i);
    const item = $('item-'+i);
    if(!chk || !item) return;
    item.classList.toggle('checked', chk.checked);
    updateCount();
}

function toggleItem(i){
    const chk = $('chk-'+i);
    if(!chk) return;
    chk.checked = !chk.checked;
    onCheck(i);
}

function toggleAll(){
    if(!currentPO) return;
    const items = currentPO.ms_podt || [];
    const allChecked = items.every((_, i) => $('chk-'+i) && $('chk-'+i).checked);
    items.forEach((_, i) => {
        const chk = $('chk-'+i);
        if(!chk) return;
        chk.checked = !allChecked;
        onCheck(i);
    });
}

function updateCount(){
    if(currentStep !== 1) return;
    if(!currentPO || !currentPO.ms_podt){
        $('btnNext').textContent = 'ถัดไป (เลือก 0)';
        $('btnNext').disabled = true;
        return;
    }
    let count = 0;
    currentPO.ms_podt.forEach((_, i) => {
        const chk = $('chk-'+i);
        if(chk && chk.checked) count++;
    });
    $('btnNext').textContent = `ถัดไป (เลือก ${count})`;
    $('btnNext').disabled = count === 0;
}

/* ---------- Qty Handlers ---------- */
function stepQty(i, delta){
    const input = $('qty-'+i);
    if(!input) return;
    const max = parseFloat(input.dataset.max ?? input.max ?? Infinity);
    let val = (parseFloat(input.value) || 0) + delta;
    if(val < 0) val = 0;
    if(val > max) val = max;
    input.value = val;
}

function clampQty(i){
    const input = $('qty-'+i);
    if(!input) return;
    const max = parseFloat(input.dataset.max ?? input.max ?? Infinity);
    let val = parseFloat(input.value);
    if(isNaN(val) || val < 0) val = 0;
    if(val > max) val = max;
    input.value = val;
}

/* ---------- Helpers ---------- */
function splitGoodName(raw){
    if(!raw) return {name:'-', code:''};
    const idx = raw.indexOf('++');
    if(idx === -1) return {name: raw.trim(), code:''};
    return {
        name: raw.substring(0, idx).trim(),
        code: raw.substring(idx).replace(/\+\+|--/g,' ').replace(/\s+/g,' ').trim()
    };
}
function fmtQty(v){
    const n = parseFloat(v || 0);
    return (n % 1 === 0) ? String(n) : n.toFixed(2);
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
    currentStep = 1;
    $('poHead').innerHTML = '';
    $('itemList').innerHTML = '';
    $('listTitle').style.display = 'none';
    $('stepper').classList.remove('show');
    $('navBar').classList.remove('show');
    $('shelfInput').value = '';
    
    // ซ่อนหน้า Step ทั้งหมด
    document.querySelectorAll('.step-page').forEach(el => el.classList.remove('active'));
    
    removePhoto();
}
let toastTimer;
function toast(msg, type=''){
    const t = $('toast');
    t.textContent = msg;
    t.className = 'show ' + type;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(()=> t.className='', type === 'error' ? 8000 : 2600);
}
</script>
</body>
</html>