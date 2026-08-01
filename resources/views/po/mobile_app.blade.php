<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, interactive-widget=resizes-content">
<title>รับสินค้าเข้า (PO)</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    .btn-cancel-receive{
        height:42px;padding:0 20px;border:1px solid #F3C6C6;border-radius:var(--r);
        background:#FBE9E9;color:#B4232C;
        font-size:14px;font-weight:500;font-family:inherit;cursor:pointer;
        transition:background-color var(--t), border-color var(--t);
    }
    .btn-cancel-receive:active{background:#F6D5D5;border-color:#E8A9A9}
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
    @media(min-width:768px){
        :root{ --content-w:720px; }
    }
    @media(min-width:1024px){
        :root{ --content-w:900px; }
    }

    *{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent}
    html{background:#E9E9E9}
    body{
        font-family:'Sarabun',-apple-system,Arial,sans-serif;
        background:var(--ash);color:var(--graphite);
        min-height:100vh;padding-bottom:100px;
        max-width:var(--content-w);margin:0 auto;
        position:relative;font-weight:400;
    }
    .sheet-overlay{ touch-action:none; }
    .sheet-list{ touch-action:pan-y; overscroll-behavior:contain; }

    /* ===== Header ===== */
    .topbar{
        background:rgba(244,244,244,0.85);
        backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
        padding:20px 16px 16px;position:sticky;top:0;z-index:20;
    }
    .topbar h1{
        color:var(--carbon);font-size:22px;font-weight:600;
        display:flex;align-items:center;gap:10px;margin-bottom:14px;
    }
    .topbar h1 .badge{color:var(--pewter);font-size:13px;font-weight:400;}
    .refresh-btn{
        margin-left:auto;width:30px;height:30px;border-radius:50%;
        border:none;background:var(--ash);color:var(--pewter);
        font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;
        transition:background-color var(--t);flex-shrink:0;
    }
    .refresh-btn:active{background:var(--cloud)}
    .user-badge{font-size:13.5px;color:var(--pewter);margin:-6px 0 12px}
    .user-badge b{color:var(--carbon);font-weight:500}
    .searchrow{display:flex;gap:8px}
    .searchrow input{
        flex:1;height:42px;
        border:1px solid var(--pale);border-radius:var(--r);
        padding:0 14px;font-size:15px;font-family:inherit;
        outline:none;background:var(--canvas);min-width:0;color:var(--carbon);
        transition:border-color var(--t);
    }
    .searchrow input:focus{border-color:var(--blue)}
    .searchrow input::placeholder{color:var(--silver)}
    .searchrow button{
        height:42px;min-width:100px;
        border:none;border-radius:var(--r);
        background:var(--blue);color:#fff;
        font-size:14px;font-weight:500;font-family:inherit;cursor:pointer;
        transition:background-color var(--t);
    }
    .searchrow button:active{background:var(--blue-dark)}
    .searchrow button:disabled{background:var(--pale);color:#fff}

    /* ===== Desktop: two-column layout ===== */
    @media(min-width:768px){
        .topbar{padding:24px 24px 18px}
        .desk-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin:8px 24px 0}
        .desk-grid .po-head,.desk-grid #topFields{margin:0!important}
        #itemList .item{margin-left:24px;margin-right:24px}
        .list-title{margin-left:24px;margin-right:24px}
        .nav-bar{border-radius:12px 12px 0 0}
    }

    /* ===== PO Head ===== */
    .po-head{
        margin:8px 16px 0;background:var(--canvas);
        border-radius:var(--r);padding:16px;
    }
    .po-head .docu-row{display:flex;align-items:flex-start;justify-content:space-between;gap:8px}
    .po-head .docu{font-size:18px;font-weight:600;color:var(--carbon)}
    .po-head .vendor{font-size:15px;color:var(--graphite);margin-top:4px;line-height:1.45}
    .po-head .meta{
        display:flex;gap:16px;margin-top:12px;padding-top:12px;
        border-top:1px solid var(--cloud);font-size:14px;color:var(--pewter);flex-wrap:wrap;
    }
    .po-head .meta b{color:var(--carbon);font-weight:500}
    .po-head .meta .v-amnt b{color:var(--blue)}
    .po-head .so-wrap{display:flex;flex-direction:column;align-items:flex-end;gap:3px;flex-shrink:0}
    .po-head .so-sale{font-size:14px;color:var(--pewter);white-space:nowrap}
    .po-head .so-sale b{color:var(--carbon);font-weight:500}

    /* ===== SO Badge (single, clickable) ===== */
    .so-badge{
        font-size:13px;font-weight:500;color:var(--blue);background:#EAF0FE;
        padding:5px 10px 5px 12px;border-radius:14px;flex-shrink:0;white-space:nowrap;
        display:inline-flex;align-items:center;gap:6px;cursor:pointer;
        transition:background-color var(--t);
    }
    .so-badge:active{background:#D6E2FC}
    .so-badge.active{background:#D6E2FC}
    .so-badge .so-count{
        background:var(--blue);color:#fff;font-size:11px;font-weight:600;
        padding:1px 6px;border-radius:10px;line-height:1.5;
    }
    .so-badge .chev-so{display:inline-block;font-size:9px;transition:transform .25s ease}
    .so-badge.active .chev-so{transform:rotate(180deg)}

    /* ===== SO Info Card (panel) ===== */
    .so-card{
        margin:0 16px;background:var(--canvas);
        border-radius:0 0 var(--r) var(--r);padding:0 16px;
        border-left:3px solid var(--blue);
        max-height:0;overflow:hidden;opacity:0;
        transition:max-height .3s ease, padding .3s ease, opacity .25s ease;
    }
    .so-card.open{max-height:400px;padding:14px 16px;opacity:1;overflow-y:auto}
    .so-card .so-title{
        font-size:13px;font-weight:500;color:var(--blue);
        text-transform:uppercase;letter-spacing:.3px;margin-bottom:8px;
    }
    .so-card .so-row{display:flex;justify-content:space-between;gap:8px;padding:3px 0;font-size:14px}
    .so-card .so-row .so-lbl{color:var(--pewter);flex-shrink:0}
    .so-card .so-row .so-val{color:var(--carbon);font-weight:500;text-align:right;word-break:break-word}

    .so-card .so-others{margin-top:12px;padding-top:12px;border-top:1px dashed var(--cloud)}
    .so-card .so-others-lbl{font-size:12.5px;color:var(--pewter);margin-bottom:8px}
    .so-chip-list{display:flex;flex-wrap:wrap;gap:6px}
    .so-chip{
        font-size:12.5px;color:var(--graphite);background:var(--ash);
        padding:4px 10px;border-radius:12px;white-space:nowrap;
    }

    /* ===== Top Fields (Shelf / Photo / Printer) ===== */
    #topFields{
        display:none;margin:8px 16px 0;background:var(--canvas);
        border-radius:var(--r);padding:16px;
    }
    .field-row{display:flex;align-items:center;justify-content:space-between;gap:14px}
    .field-col{display:flex;flex-direction:column;gap:8px}
    .field-col .lbl{font-size:15px;font-weight:500;color:var(--carbon)}
    .divider{height:1px;background:var(--cloud);margin:14px 0}
    .combo-row{align-items:flex-start;gap:20px}
    .combo-item{display:flex;flex-direction:column;gap:8px;min-width:0}
    .combo-item .lbl{font-size:14px;font-weight:500;color:var(--carbon)}
    .shelf-item{flex:1}
    .photo-item{flex:0 0 auto}

    /* --- Shelf picker --- */
    .shelf-select{
        width:100%;height:42px;
        border:1px solid var(--pale);border-radius:var(--r);
        padding:0 12px;display:flex;align-items:center;justify-content:space-between;gap:8px;
        background:var(--canvas);font-size:15px;font-family:inherit;color:var(--carbon);
        cursor:pointer;transition:border-color var(--t);
    }
    .shelf-select:active{border-color:var(--blue)}
    .shelf-select #shelfSelectText{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .shelf-select #shelfSelectText.placeholder{color:var(--silver)}
    .shelf-select .chev{color:var(--silver);font-size:16px;flex-shrink:0}
    .sheet-overlay{
        position:fixed;left:0;right:0;top:0;
        height:100vh;height:100dvh;
        background:rgba(23,26,32,0.45);
        z-index:100;display:none;align-items:flex-end;justify-content:center;
    }
    .sheet-overlay.show{display:flex}
    .sheet{
        background:var(--canvas);width:100%;max-width:var(--content-w);
        border-radius:16px 16px 0 0;max-height:75vh;
        display:flex;flex-direction:column;padding:16px;
        padding-bottom:calc(16px + env(safe-area-inset-bottom));
        animation:slideUp .22s ease;
    }
    @keyframes slideUp{from{transform:translateY(30px);opacity:.4}to{transform:translateY(0);opacity:1}}
    .sheet-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
    .sheet-header span{font-size:16px;font-weight:500;color:var(--carbon)}
    .sheet-close{
        border:none;background:var(--ash);width:28px;height:28px;border-radius:50%;
        font-size:14px;color:var(--pewter);cursor:pointer;flex-shrink:0;
    }
    .sheet-search{
        width:100%;height:44px;border:1px solid var(--pale);border-radius:var(--r);
        padding:0 14px;font-size:15px;font-family:inherit;outline:none;
        margin-bottom:8px;color:var(--carbon);flex-shrink:0;
    }
    .sheet-search:focus{border-color:var(--blue)}
    .sheet-list{overflow-y:auto;flex:1;-webkit-overflow-scrolling:touch}
    .sheet-item{
        padding:14px 8px;font-size:15px;color:var(--carbon);
        border-bottom:1px solid var(--cloud);cursor:pointer;
    }
    .sheet-item:active{background:var(--ash)}
    .sheet-item.custom{color:var(--blue);font-weight:500}
    .sheet-empty{padding:24px 8px;text-align:center;color:var(--pewter);font-size:14px}

    /* --- Photo --- */
    .photo-tap{
        width:52px;height:52px;border-radius:var(--r);flex-shrink:0;
        background:var(--ash);border:1px dashed var(--pale);
        display:flex;align-items:center;justify-content:center;overflow:hidden;
        position:relative;cursor:pointer;transition:border-color var(--t);
    }
    .photo-tap.has-photo{border-style:solid;border-color:var(--blue)}
    .photo-tap img{width:100%;height:100%;object-fit:cover;display:none}
    .photo-tap .ph-icon{font-size:22px;color:var(--silver)}
    .photo-remove{
        position:absolute;top:2px;right:2px;width:18px;height:18px;border-radius:50%;
        border:none;background:rgba(23,26,32,0.72);color:#fff;font-size:10px;line-height:1;
        cursor:pointer;display:none;align-items:center;justify-content:center;
    }

    /* --- Printer --- */
    .printer-row{display:flex;gap:8px;align-items:center;width:100%}
    .printer-select{
        flex:1;min-width:0;height:42px;border:1px solid var(--pale);border-radius:var(--r);
        padding:0 10px;font-size:15px;font-family:inherit;background:var(--canvas);
        color:var(--carbon);outline:none;transition:border-color var(--t);
    }
    .printer-select:focus{border-color:var(--blue)}
    .qty-ctrl{
        display:flex;align-items:center;gap:0;
        border:1px solid var(--pale);border-radius:var(--r);
        overflow:hidden;background:var(--canvas);flex-shrink:0;
    }
    .qty-ctrl button{
        width:34px;height:42px;border:none;background:var(--ash);
        font-size:17px;color:var(--carbon);cursor:pointer;font-family:inherit;
        transition:background-color var(--t);
    }
    .qty-ctrl button:active{background:var(--cloud)}
    .qty-ctrl input{
        width:38px;height:42px;border:none;text-align:center;
        font-size:15px;font-weight:500;font-family:inherit;outline:none;color:var(--carbon);
        background:var(--canvas);
    }
    .qty-ctrl input:focus{background:var(--ash)}
    .qty-ctrl input::-webkit-outer-spin-button,
    .qty-ctrl input::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}
    .qty-ctrl input[type=number]{-moz-appearance:textfield;appearance:textfield}

    /* ===== Item List ===== */
    .list-title{
        margin:16px 16px 10px;
        display:flex;justify-content:space-between;align-items:center;
    }
    #itemCountLabel{font-size:15px;font-weight:500;color:var(--carbon)}
    .select-all{
        font-size:14px;color:var(--pewter);font-weight:400;
        background:none;border:none;font-family:inherit;cursor:pointer;
        transition:color var(--t);
    }
    .select-all:active{color:var(--blue)}

    /* --- Desktop item grid: fill full width, stretch remaining columns --- */
    @media(min-width:768px){
        #itemList{
            display:flex;
            flex-direction:column;
            gap:8px;
        }
    }
    .item{
        margin:0 16px 8px;background:var(--canvas);
        border:1px solid transparent;border-radius:var(--r);
        padding:14px;display:flex;gap:12px;align-items:flex-start;
        transition:border-color var(--t), background-color var(--t);
    }
    .item.checked{border-color:var(--blue);background:#F5F8FE}
    .item input[type=checkbox]{
        width:22px;height:22px;flex-shrink:0;align-self:center;accent-color:var(--blue);
    }
    .item .info{flex:1;min-width:0}
    .item .gname{font-size:15px;font-weight:500;line-height:1.4;word-break:break-word;color:var(--carbon)}
    .item .gcode{font-size:13px;color:var(--pewter);margin-top:2px;word-break:break-all}
    .item .price{font-size:14px;color:var(--pewter);margin-top:4px}
    .item .price .unit{color:var(--blue);font-weight:500}

    /* --- Recv summary text --- */
    .recv-summary{font-size:13px;color:var(--pewter);margin-top:5px}
    .recv-summary b{font-weight:500;color:var(--carbon)}

    /* --- History accordion --- */
    .hist-toggle{
        display:inline-flex;align-items:center;gap:4px;
        font-size:13px;color:var(--blue);cursor:pointer;
        background:none;border:none;font-family:inherit;padding:0;margin-top:5px;
    }
    .hist-toggle .hist-chev{
        display:inline-block;font-size:9px;transition:transform .2s ease;
    }
    .hist-toggle.open .hist-chev{transform:rotate(180deg)}
    .hist-detail{
        max-height:0;overflow:hidden;opacity:0;
        transition:max-height .25s ease, opacity .2s ease, margin .2s ease;
        margin-top:0;
    }
    .hist-detail.open{max-height:500px;opacity:1;margin-top:6px}
    .hist-entry{
        display:flex;align-items:baseline;gap:4px 8px;flex-wrap:wrap;
        font-size:13px;color:var(--pewter);padding:4px 0;
        border-bottom:1px dashed var(--cloud);
    }
    .hist-entry:last-child{border-bottom:none}
    .hist-entry .hist-who{color:var(--carbon);font-weight:500;flex-shrink:0}
    .hist-entry .hist-qty{color:var(--blue);font-weight:500;flex-shrink:0}
    .hist-entry .hist-shelf{color:var(--pewter);font-size:12.5px;word-break:break-word}
    .hist-entry .hist-when{margin-left:auto;flex-shrink:0;font-size:12.5px;white-space:nowrap;color:var(--pewter)}

    .qtybox{display:flex;flex-direction:column;align-items:center;gap:4px;flex-shrink:0}
    .qtybox label{font-size:13px;color:var(--pewter)}
    .qtybox .qty-ctrl input{width:52px;height:38px;font-size:16px}
    .qtybox .qty-ctrl button{width:36px;height:38px;font-size:18px}
    .qty-ordered{font-size:12.5px;color:var(--pewter)}

    /* ===== State Box ===== */
    .state{
        position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
        width:calc(100% - 48px);max-width:calc(var(--content-w) - 48px);
        text-align:center;color:var(--pewter);font-size:15px;line-height:1.7;z-index:10;
    }
    .state .icon{font-size:40px;margin-bottom:12px;opacity:.85}
    .spinner{
        width:32px;height:32px;border:3px solid var(--cloud);
        border-top-color:var(--blue);border-radius:50%;
        margin:0 auto 14px;animation:spin .8s linear infinite;
    }
    @keyframes spin{to{transform:rotate(360deg)}}
    .err{color:var(--graphite)}

    /* ===== Bottom Save Bar ===== */
    .nav-bar{
        position:fixed;bottom:0;z-index:30;
        left:50%;transform:translateX(-50%);
        width:100%;max-width:var(--content-w);
        background:rgba(255,255,255,0.92);
        backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);
        border-top:1px solid var(--cloud);
        padding:12px 16px calc(12px + env(safe-area-inset-bottom));
        display:none;gap:10px;align-items:center;
    }
    .nav-bar.show{display:flex}
    .btn-next{
        flex:1;height:46px;border:none;border-radius:var(--r);
        background:var(--blue);color:#fff;
        font-size:15px;font-weight:500;font-family:inherit;cursor:pointer;
        transition:background-color var(--t);
    }
    .btn-next:active{background:var(--blue-dark)}
    .btn-next:disabled{background:var(--pale);cursor:not-allowed}

    /* ===== Toast ===== */
    #toast{
        position:fixed;left:50%;bottom:100px;transform:translateX(-50%) translateY(20px);
        background:var(--carbon);color:#fff;font-size:14px;
        padding:11px 20px;border-radius:var(--r);opacity:0;pointer-events:none;
        transition:all .25s;z-index:50;
        max-width:calc(var(--content-w) - 40px);
        white-space:normal;word-break:break-word;text-align:center;line-height:1.5;
    }
    #toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
    #toast.ok{background:var(--blue)}
    #toast.error{background:var(--carbon)}

    /* ===== Confirm Modal ===== */
    .confirm-overlay{
        position:fixed;inset:0;background:rgba(23,26,32,0.5);
        z-index:200;display:none;align-items:center;justify-content:center;padding:24px;
    }
    .confirm-overlay.show{display:flex}
    .confirm-box{
        background:var(--canvas);width:100%;max-width:400px;
        border-radius:12px;padding:22px 20px;animation:popIn .18s ease;
    }
    @keyframes popIn{from{transform:scale(.94);opacity:0}to{transform:scale(1);opacity:1}}
    .confirm-title{font-size:18px;font-weight:600;color:var(--carbon);margin-bottom:10px}
    .confirm-body{font-size:15px;color:var(--graphite);line-height:1.7;margin-bottom:18px}
    .confirm-body b{color:var(--carbon);font-weight:500}
    .confirm-body .row{display:flex;justify-content:space-between;gap:12px;padding:4px 0}
    .confirm-body .row span:first-child{color:var(--pewter)}
    .confirm-status{display:inline-block;font-size:13px;font-weight:500;padding:2px 10px;border-radius:12px}
    .confirm-status.full{color:var(--green);background:var(--green-bg)}
    .confirm-status.partial{color:var(--amber);background:var(--amber-bg)}
    .confirm-actions{display:flex;gap:10px}
    .confirm-actions button{
        flex:1;height:44px;border:none;border-radius:var(--r);
        font-size:15px;font-weight:500;font-family:inherit;cursor:pointer;
        transition:background-color var(--t);
    }
    .confirm-cancel{background:var(--ash);color:var(--graphite)}
    .confirm-cancel:active{background:var(--cloud)}
    .confirm-ok{background:var(--blue);color:#fff}
    .confirm-ok:active{background:var(--blue-dark)}
</style>
</head>
<body>

<!-- Header + Search -->
<div class="topbar">
    <h1>รับสินค้าเข้า
        <button type="button" class="refresh-btn" onclick="location.reload()" title="รีเฟรชหน้าจอ">⟳</button>
    </h1>
    <div class="user-badge">เข้าใช้งานในชื่อ: <b id="userName">-</b></div>
    <div class="searchrow">
        <input type="text" id="poInput" placeholder="เลขที่ PO"
               inputmode="numeric" pattern="[0-9\-]*" autocomplete="off"
               oninput="formatPOInput(this)" onkeydown="if(event.key==='Enter')searchPO()">
        <button id="btnSearch" onclick="searchPO()">ค้นหา</button>
    </div>
</div>

<!-- PO Header -->
<div id="poHead"></div>
<!-- SO Info Card -->
<div id="soCard"></div>

<!-- Shelf + Photo + Printer -->
<div id="topFields">
    <div class="field-row combo-row">
        <div class="combo-item shelf-item">
            <div class="lbl">ชั้นวาง</div>
            <button type="button" class="shelf-select" id="shelfSelect" onclick="openShelfSheet()">
                <span id="shelfSelectText" class="placeholder">เลือกชั้นวาง</span>
                <span class="chev">›</span>
            </button>
        </div>
        <div class="combo-item photo-item">
            <div class="lbl">รูปหน้างาน</div>
            <div class="photo-tap" id="photoTap" onclick="triggerPhoto()">
                <span class="ph-icon" id="photoIcon">📷</span>
                <img id="photoImg" alt="รูปถ่ายที่แนบ">
                <button type="button" class="photo-remove" id="btnRemovePhoto" onclick="removePhoto(event)">✕</button>
            </div>
            <input type="file" id="photoInput" accept="image/*" capture="environment" style="display:none" onchange="onPhotoSelected(event)">
        </div>
    </div>
    <div class="divider"></div>
    <div class="field-col">
        <div class="lbl">พิมพ์สติกเกอร์</div>
        <div class="printer-row">
            <select id="printerSelect" class="printer-select" onchange="onPrinterChange()">
                <option value="" disabled selected>— เลือก —</option>
                <option value="none">ไม่พิมพ์</option>
                <option value="TSC TTP-247 internal">ภายใน</option>
                <option value="TSC TTP-247 store">สโตร์</option>
                <option value="\\ว้าล\TSC TTP-247">ภายนอก</option>
            </select>
            <div class="qty-ctrl" id="sheetCtrl" style="display:none">
                <button type="button" onclick="stepSheet(-1)">−</button>
                <input type="number" id="sheetQty" value="1" min="1" inputmode="numeric" onchange="clampSheet()">
                <button type="button" onclick="stepSheet(1)">+</button>
            </div>
        </div>
    </div>
</div>

<!-- Item List -->
<div id="listTitle" class="list-title" style="display:none">
    <span id="itemCountLabel">รายการสินค้า</span>
    <button class="select-all" onclick="toggleAll()">เลือกทั้งหมด</button>
</div>
<div id="itemList"></div>

<!-- State -->
<div id="stateBox" class="state">
    <div class="icon">🔎</div>
    พิมพ์เลขที่ PO แล้วกดค้นหา
</div>

<!-- Bottom bar -->
<div class="nav-bar" id="navBar">
    <button class="btn-next" id="btnNext" onclick="openConfirm()">บันทึกรับเข้า</button>
</div>
<div id="toast"></div>

<!-- Bottom sheet: Shelf -->
<div class="sheet-overlay" id="shelfOverlay" onclick="closeShelfSheetBackdrop(event)">
    <div class="sheet" onclick="event.stopPropagation()">
        <div class="sheet-header">
            <span>เลือกชั้นวาง</span>
            <button type="button" class="sheet-close" onclick="closeShelfSheet()">✕</button>
        </div>
        <input type="text" id="shelfSearch" class="sheet-search" placeholder="พิมพ์ค้นหา"
               autocomplete="off" oninput="renderShelfList()">
        <div class="sheet-list" id="shelfList"></div>
    </div>
</div>

<!-- Confirm Modal -->
<div class="confirm-overlay" id="confirmOverlay" onclick="closeConfirmBackdrop(event)">
    <div class="confirm-box" onclick="event.stopPropagation()">
        <div class="confirm-title">ยืนยันการบันทึกรับเข้า</div>
        <div class="confirm-body" id="confirmBody"></div>
        <div class="confirm-actions">
            <button type="button" class="confirm-cancel" onclick="closeConfirm()">ยกเลิก</button>
            <button type="button" class="confirm-ok" id="confirmOkBtn" onclick="confirmSave()">ยืนยันบันทึก</button>
        </div>
    </div>
</div>

<!-- Cancel Receive Modal -->
<div class="confirm-overlay" id="cancelOverlay" onclick="closeCancelBackdrop(event)">
    <div class="confirm-box" onclick="event.stopPropagation()">
        <div class="confirm-title">ยกเลิกการรับเข้า</div>
        <div class="confirm-body">
            <b id="cancelPONum"></b>
            <div style="margin-top:10px;font-size:13px;color:var(--pewter)">
                *สถานะจะถูกบันทึกเป็น <b style="color:var(--carbon)">รับเข้าผิด</b>
            </div>
        </div>
        <div class="confirm-actions">
            <button type="button" class="confirm-cancel" onclick="closeCancelModal()">ปิด</button>
            <button type="button" class="confirm-ok" id="cancelOkBtn" onclick="doCancelReceive()">ยืนยันยกเลิก</button>
        </div>
    </div>
</div>
<script>
const API_URL = '{{ url('/api/getPODetail') }}';
const RECEIVE_URL = '{{ url('/api/receivePO') }}';
const HISTORY_URL = '{{ url('/api/receivePO/history') }}';
const CANCEL_URL = '{{ url('/api/receivePO/cancel') }}';
const CSRF_TOKEN = '{{ csrf_token() }}';
let lastFullyReceivedPO = null;
const RECEIVED_BY = new URLSearchParams(window.location.search).get('create_by') || '';

if(!RECEIVED_BY){
    document.body.innerHTML = `
        <div style="min-height:100vh;display:flex;flex-direction:column;align-items:center;
                    justify-content:center;padding:32px;text-align:center;
                    font-family:'Sarabun',-apple-system,Arial,sans-serif;color:#171A20;">
            <div style="font-size:44px;margin-bottom:16px;">🔒</div>
            <div style="font-size:17px;font-weight:500;margin-bottom:8px;">ไม่พบสิทธิ์เข้าใช้งาน</div>
        </div>`;
    throw new Error('access_denied: missing create_by');
}
document.getElementById('userName').textContent = RECEIVED_BY;

const SHELF_OPTIONS = [
  "1A01","1Kโบว์","1กวาง","1กี้","1ตี๋/พลอย","1ต่าย","1ท๊อป","1นภา",
  "1นุ/เต้น","1นุช","1นุ่น","1น้อย/มล","1น้ำ/กิ๊ฟ","1ฟอง","1ฟิล์ม",
  "1ภิรุณ","1มุก","1หนิง","1หมิง","1หมู/ต๋อง","1เจี๊ยบ","1เชร์",
  "1เนย","1เอก","1แยม","1แอม","1โจ",
  "A11","A12","A13","A14","A21","A22","A23","A24",
  "A31","A32","A33","A34","A41","A42","A43","A44",
  "aom stock","B1",
  "C1","C10","C11","C12","C13","C14","C15","C16",
  "C2","C3","C4","C5","C6","C7","C8","C9","Cท่อ",
  "LASADA",
  "Qกรมศุลเล็","Qจัดแล้วรอ","Qติดปัญหา","Qบิลสด","QรอครบSO","Qสหกรณ์","Qแก้ไข","Qแดง",
  "top stock","Z55",
  "ขมจ่ายแล้ว","ของเกิน","คืนstock","ช.เดช","ช.โอ","ชัย-เดช","ชัย1","ชัย2",
  "ด.1","ด.10","ด.11","ด.12","ด.2","ด.3","ด.4","ด.5","ด.6","ด.7","ด.8","ด.9",
  "ทำคืน","ท๊อปบน","ปอ-ฮิคาริ","ปิดรับบิล","ปี69","พู่",
  "ว๊าล","ว๊าลแก้ไข","หน้าออฟฟิศ","หยกรอบิล","หยกรอเคลีย","หลังออฟฟิศ"
];

let currentPO = null;
let capturedPhoto = null;
let selectedShelf = '';
let historyDetailMap = new Map(); // key=normName → [{received_by, received_at, recv_qty, shelf}]

const $ = id => document.getElementById(id);

/* ========== PO Input ========== */
function formatPOInput(el){
    const digits = el.value.replace(/\D/g,'');
    el.value = digits.length > 4 ? digits.slice(0,4) + '-' + digits.slice(4) : digits;
}

function normName(s){
    return String(s || '').trim().toLowerCase().replace(/\s+/g, ' ');
}

/* ========== History: fetch + build maps ========== */
async function getReceivedHistory(ponum){
    const qtyMap = new Map();    // normName → total received qty
    const detailMap = new Map(); // normName → [{received_by, received_at, recv_qty, shelf}]
    try{
        const res = await fetch(`${HISTORY_URL}?PONum=${encodeURIComponent(ponum)}`);
        if(!res.ok) return { qtyMap, detailMap };
        const rows = await res.json();
        (rows || []).forEach(r => {
            if(!r.good_name) return;
            const key = normName(r.good_name);
            const qty = parseFloat(r.recv_qty || 0);
            qtyMap.set(key, (qtyMap.get(key) || 0) + qty);
            if(!detailMap.has(key)) detailMap.set(key, []);
            detailMap.get(key).push({
                received_by: r.received_by || '-',
                received_at: r.received_at || '',
                recv_qty: qty,
                shelf: r.shelf || ''
            });
        });
    }catch(e){ /* silent */ }
    return { qtyMap, detailMap };
}

/* ค้นหา qty จาก map ด้วย flexible matching */
function findQtyFromMap(qtyMap, key){
    if(qtyMap.has(key)) return qtyMap.get(key);
    const shortKey = key.split('++')[0].trim();
    if(shortKey && shortKey !== key){
        for(const [k, v] of qtyMap){
            if(k.split('++')[0].trim() === shortKey) return v;
        }
    }
    for(const [k, v] of qtyMap){
        if(k.includes(shortKey) || shortKey.includes(k)) return v;
    }
    return 0;
}

/* ค้นหา history ด้วย key matching ที่ยืดหยุ่นกว่า exact match */
function findHistory(key){
    // exact match ก่อน
    if(historyDetailMap.has(key)) return historyDetailMap.get(key);
    // fallback: ถ้า key ของ PO item มี ++ ให้ลอง match เฉพาะชื่อ (ก่อน ++)
    const shortKey = key.split('++')[0].trim();
    if(shortKey && shortKey !== key){
        for(const [k, v] of historyDetailMap){
            if(k.split('++')[0].trim() === shortKey) return v;
        }
    }
    // fallback: includes match
    for(const [k, v] of historyDetailMap){
        if(k.includes(shortKey) || shortKey.includes(k)) return v;
    }
    return [];
}

function getSelectedItems(){
    if(!currentPO || !currentPO.ms_podt) return [];
    return currentPO.ms_podt
        .map((it,i) => ({it,i}))
        .filter(x => $('chk-'+x.i) && $('chk-'+x.i).checked)
        .map(x => ({
            GoodName:  x.it.GoodName,
            UnitPrice: parseFloat(x.it.GoodPrice2 || 0),
            RecvQty:   parseFloat($('qty-'+x.i).value || 0)
        }));
}

function computeStatus(selected){
    const selMap = new Map(selected.map(s => [normName(s.GoodName), s.RecvQty]));
    const willBeComplete = currentPO.ms_podt.every(it => {
        const key = normName(it.GoodName);
        const remaining = it._remainingQty ?? 0;
        const recvNow = selMap.get(key) || 0;
        return (remaining - recvNow) <= 0;
    });
    return willBeComplete ? 'ครบ' : 'บางส่วน';
}

/* ========== Shelf Bottom Sheet ========== */
let shelfScrollY = 0;
function syncSheetViewport(){
    const overlay = $('shelfOverlay');
    if(!overlay || !overlay.classList.contains('show')) return;
    if(window.visualViewport){
        const vv = window.visualViewport;
        overlay.style.top = vv.offsetTop + 'px';
        overlay.style.height = vv.height + 'px';
    }
}
if(window.visualViewport){
    window.visualViewport.addEventListener('resize', syncSheetViewport);
    window.visualViewport.addEventListener('scroll', syncSheetViewport);
}
function openShelfSheet(){
    shelfScrollY = window.scrollY || window.pageYOffset || 0;
    document.documentElement.style.overflow = 'hidden';
    document.body.style.position = 'fixed';
    document.body.style.top = `-${shelfScrollY}px`;
    document.body.style.left = '0'; document.body.style.right = '0';
    document.body.style.width = '100%'; document.body.style.overflow = 'hidden';
    $('shelfOverlay').classList.add('show');
    syncSheetViewport();
    $('shelfSearch').value = '';
    renderShelfList();
    setTimeout(() => $('shelfSearch').focus({preventScroll:true}), 50);
}
function closeShelfSheet(){
    $('shelfOverlay').classList.remove('show');
    $('shelfOverlay').style.top = '';
    $('shelfOverlay').style.height = '';
    document.documentElement.style.overflow = '';
    document.body.style.position = ''; document.body.style.top = '';
    document.body.style.left = ''; document.body.style.right = '';
    document.body.style.width = ''; document.body.style.overflow = '';
    window.scrollTo(0, shelfScrollY);
}
function closeShelfSheetBackdrop(e){ if(e.target.id==='shelfOverlay') closeShelfSheet(); }
function renderShelfList(){
    const q = $('shelfSearch').value.trim().toLowerCase();
    const filtered = q ? SHELF_OPTIONS.filter(s => s.toLowerCase().includes(q)) : SHELF_OPTIONS;
    let html = filtered.map(s => `<div class="sheet-item" onclick="selectShelf('${escJs(s)}')">${esc(s)}</div>`).join('');
    if(!html) html = '<div class="sheet-empty">ไม่พบชั้นวางที่ค้นหา</div>';
    $('shelfList').innerHTML = html;
}
function selectShelf(val){
    selectedShelf = val;
    const el = $('shelfSelectText');
    el.textContent = val; el.classList.remove('placeholder');
    closeShelfSheet();
}
function resetShelf(){
    selectedShelf = '';
    const el = $('shelfSelectText');
    el.textContent = 'เลือกชั้นวาง'; el.classList.add('placeholder');
}

/* ========== Photo ========== */
function triggerPhoto(){ $('photoInput').click(); }
function onPhotoSelected(event){
    const file = event.target.files && event.target.files[0];
    event.target.value = '';
    if(!file) return;
    compressImage(file, 1600, 0.75).then(dataUrl => {
        capturedPhoto = dataUrl;
        $('photoImg').src = dataUrl; $('photoImg').style.display = 'block';
        $('photoIcon').style.display = 'none';
        $('btnRemovePhoto').style.display = 'flex';
        $('photoTap').classList.add('has-photo');
    }).catch(() => toast('อ่านไฟล์รูปไม่สำเร็จ','error'));
}
function removePhoto(event){
    if(event) event.stopPropagation();
    capturedPhoto = null;
    $('photoImg').src = ''; $('photoImg').style.display = 'none';
    $('photoIcon').style.display = 'block';
    $('btnRemovePhoto').style.display = 'none';
    $('photoTap').classList.remove('has-photo');
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
                    width = Math.round(width * scale); height = Math.round(height * scale);
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

/* ========== Printer ========== */
function onPrinterChange(){
    $('sheetCtrl').style.display = ($('printerSelect').value && $('printerSelect').value !== 'none') ? 'flex' : 'none';
}
function stepSheet(delta){
    const input = $('sheetQty');
    let val = (parseInt(input.value) || 0) + delta;
    if(val < 1) val = 1;
    input.value = val;
}
function clampSheet(){
    const input = $('sheetQty');
    let val = parseInt(input.value);
    if(isNaN(val) || val < 1) val = 1;
    input.value = val;
}
function resetPrinter(){
    $('printerSelect').value = '';
    $('sheetQty').value = 1;
    onPrinterChange();
}
function printerLabel(val){
    return {'TSC TTP-247 internal':'ภายใน','TSC TTP-247 store':'สโตร์','\\\\ว้าล\\TSC TTP-247':'ภายนอก'}[val] || val;
}

function showNotFound(poNumber){
    clearResult();
    $('stateBox').innerHTML =
        '<div class="icon">❌</div>' +
        '<span class="err" style="font-size:16px;font-weight:500;color:var(--carbon)">ไม่พบ PO นี้</span><br>' +
        esc(poNumber) + '<br>ตรวจสอบเลขที่แล้วค้นหาใหม่';
    $('stateBox').style.display = 'block';
}

/* ========== Search PO ========== */
async function searchPO(){
    const poNumber = $('poInput').value.trim();
    if(!poNumber){ toast('กรุณาพิมพ์เลขที่ PO ก่อน','error'); return; }

    $('btnSearch').disabled = true;
    clearResult();
    $('stateBox').innerHTML = '<div class="spinner"></div>กำลังค้นหา ' + esc(poNumber) + ' ...';
    $('stateBox').style.display = 'block';

    try{
        // 1) ดึง PO ก่อน เพื่อเอา DocuNo ที่ถูกต้อง (มี prefix PO ครบ)
        const res = await fetch(`${API_URL}?PONum=${encodeURIComponent(poNumber)}`);

        if(!res.ok){
            if(res.status === 404 || res.status === 500){ showNotFound(poNumber); return; }
            const body = await res.json().catch(() => null);
            throw new Error((body && body.message) || ('HTTP ' + res.status));
        }

        let raw = await res.json();
        let data, soList = [], soInfo = {};
        if(raw && raw.poData !== undefined){
            data = raw.poData; soList = raw.soList || []; soInfo = raw.soInfo || {};
        } else { data = raw; }
        data._soList = soList;

        if(Array.isArray(data)) data = data[0];
        if(data && data.data) data = Array.isArray(data.data) ? data.data[0] : data.data;

        if(!data || !data.ms_podt || data.ms_podt.length === 0){
            showNotFound(poNumber); return;
        }
        if(!data._soList || data._soList.length === 0){
            clearResult();
            $('stateBox').innerHTML =
                '<div class="icon">⚠️</div>' +
                '<span class="err" style="font-size:16px;font-weight:500;color:var(--carbon)">ไม่สามารถรับเข้าได้</span><br>' +
                'เลข PO <b>' + esc(poNumber) + '</b><br>ยังไม่ได้เชื่อมกับ SO';
            $('stateBox').style.display = 'block';
            return;
        }
        const history = await getReceivedHistory(data.DocuNo);

        data._soInfo = {
            SONum:      soInfo.SONum || '',
            CustPONo:   soInfo.CustPONo || '',
            CustName:   soInfo.CustName || '',
            ResponseBy: soInfo.ResponseBy || ''
        };
        historyDetailMap = history.detailMap;

        data.ms_podt = data.ms_podt.map(it => {
            const ordered  = parseFloat(it.AppvQty2 || it.GoodQty2 || 0);
            const received = findQtyFromMap(history.qtyMap, normName(it.GoodName));
            return { ...it, _orderedQty: ordered, _receivedQty: received, _remainingQty: ordered - received };
        });

        const hasRemaining = data.ms_podt.filter(it => it._remainingQty > 0);
        if(hasRemaining.length === 0){
            lastFullyReceivedPO = data.DocuNo;
            clearResult();
            $('stateBox').innerHTML = `
                สินค้าทั้งหมดของ PO นี้ถูกรับเข้าไปแล้ว
                <div style="margin-top:14px">
                    <button type="button" class="btn-cancel-receive" onclick="openCancelModal()">
                        ยกเลิกการรับเข้า
                    </button>
                </div>`;
            $('stateBox').style.display = 'block';
            return;
        }

        data.ms_podt = hasRemaining;
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
function toggleSoCard(){
    const card = $('soCardInner');
    const badge = $('soBadge');
    const chev = $('soChev');
    if(!card) return;
    card.classList.toggle('open');
    if(badge) badge.classList.toggle('active');
    if(chev) chev.style.transform = card.classList.contains('open') ? 'rotate(180deg)' : '';
}
/* ========== Render PO ========== */
    function renderPO(po){
        $('stateBox').style.display = 'none';
        const soList = po._soList || [];       // ทุก SO (badge)
        const soInfo = po._soInfo || {};       // รายละเอียด SO ล่าสุด
        const hasSO = soList.length > 0;
        const extraCount = soList.length - 1;  // SO อื่นนอกจากตัวล่าสุด

        $('poHead').innerHTML = `
            <div class="po-head">
                <div class="docu-row">
                    <div class="docu">${esc(po.DocuNo || '-')}</div>
                    ${hasSO ? `
                        <div class="so-wrap">
                            <span class="so-badge" id="soBadge" onclick="toggleSoCard()">
                                SO ${esc(soInfo.SONum || soList[0].SONum)}
                                ${extraCount > 0 ? `<span class="so-count">+${extraCount}</span>` : ''}
                                <span class="chev-so" id="soChev">▼</span>
                            </span>
                        </div>` : ''}
                </div>
                <div class="vendor">${esc(po.VendorName || po.VendorNameEng || '-')}</div>
                <div class="meta">
                    <span>กำหนดส่ง: <b>${fmtDate(po.ShipDate)}</b></span>
                    <span class="v-amnt">ยอดสุทธิ: <b>${fmtNum(po.NetAmnt)} ฿</b></span>
                </div>
            </div>`;

        if(hasSO){
            const others = soList.filter(s => s.SONum !== (soInfo.SONum || soList[0].SONum));
            $('soCard').innerHTML = `
                <div class="so-card" id="soCardInner">
                    <div class="so-title">SO ${esc(soInfo.SONum || '-')} (ล่าสุด)</div>
                    ${soInfo.CustName   ? `<div class="so-row"><span class="so-lbl">ลูกค้า</span><span class="so-val">${esc(soInfo.CustName)}</span></div>` : ''}
                    ${soInfo.CustPONo  ? `<div class="so-row"><span class="so-lbl">PO ลูกค้า</span><span class="so-val">${esc(soInfo.CustPONo)}</span></div>` : ''}
                    ${soInfo.ResponseBy ? `<div class="so-row"><span class="so-lbl">SALE</span><span class="so-val">${esc(soInfo.ResponseBy)}</span></div>` : ''}
                    ${others.length > 0 ? `
                        <div class="so-others">
                            <div class="so-others-lbl">SO อื่น (${others.length})</div>
                            <div class="so-chip-list">
                                ${others.map(s => `<span class="so-chip">${esc(s.SONum)}</span>`).join('')}
                            </div>
                        </div>` : ''}
                </div>`;
        } else {
            $('soCard').innerHTML = '';
        }
    const items = po.ms_podt || [];
    $('itemCountLabel').textContent = `รายการสินค้า (${items.length})`;
    $('listTitle').style.display = 'flex';

    $('itemList').innerHTML = items.map((it, i) => {
        const {name, code} = splitGoodName(it.GoodName);
        const ordered   = it._orderedQty || 0;
        const received  = it._receivedQty || 0;
        const remaining = it._remainingQty || 0;
        const key       = normName(it.GoodName);
        const details   = findHistory(key);

        let histHtml = '';
        if(details.length > 0){
            histHtml = `
                <button type="button" class="hist-toggle" id="histBtn-${i}" onclick="event.stopPropagation();toggleHist(${i})">
                    ประวัติรับ ${details.length} รอบ <span class="hist-chev">▼</span>
                </button>
                <div class="hist-detail" id="histDetail-${i}">
                    ${details.map(d => `
                        <div class="hist-entry">
                            <span class="hist-who">${esc(d.received_by)}</span>
                            <span class="hist-when">${fmtDateTime(d.received_at)}</span>
                            <span class="hist-qty">×${fmtQty(d.recv_qty)}</span>
                            ${d.shelf ? `<span class="hist-shelf">[${esc(d.shelf)}]</span>` : ''}
                        </div>
                    `).join('')}
                </div>`;
        }

        // ลบสรุปรับแล้ว/คงเหลือ - แสดงแค่ประวัติกับจำนวนที่สั่งด้านขวาแทน

        return `
        <div class="item" id="item-${i}">
            <input type="checkbox" id="chk-${i}" onchange="onCheck(${i})">
            <div class="info" onclick="toggleItem(${i})">
                <div class="gname">${esc(name)}</div>
                ${code ? `<div class="gcode">${esc(code)}</div>` : ''}
                <div class="price"><b class="unit">${fmtNum(it.GoodPrice2)}</b> ฿/หน่วย</div>
                ${histHtml}
            </div>
            <div class="qtybox">
                <label>จำนวน</label>
                <div class="qty-ctrl">
                    <button type="button" onclick="stepQty(${i},-1)">−</button>
                    <input type="number" id="qty-${i}" value="${remaining}" min="0" max="${remaining}" data-max="${remaining}" inputmode="decimal" onclick="event.stopPropagation()" onchange="clampQty(${i})">
                    <button type="button" onclick="stepQty(${i},1)">+</button>
                </div>
                <div class="qty-ordered">สั่ง ${fmtQty(ordered)}</div>
            </div>
        </div>`;
    }).join('');

    $('topFields').style.display = 'block';
    $('navBar').classList.add('show');
    updateCount();
}

/* ========== History toggle ========== */
function toggleHist(i){
    const btn = $('histBtn-'+i);
    const detail = $('histDetail-'+i);
    if(!btn || !detail) return;
    const open = detail.classList.toggle('open');
    btn.classList.toggle('open', open);
}

/* ========== Confirm Modal ========== */
let confirmScrollY = 0;
let pendingPayload = null;

function openConfirm(){
    const selected = getSelectedItems();
    if(selected.length === 0){ toast('กรุณาเลือกสินค้าอย่างน้อย 1 รายการ','error'); return; }
    if(selected.some(s => s.RecvQty <= 0)){ toast('จำนวนรับต้องมากกว่า 0','error'); return; }
    if(!selectedShelf){ toast('กรุณาเลือกชั้นวางก่อนบันทึก','error'); return; }
    if(!$('printerSelect').value){ toast('กรุณาเลือกเครื่องพิมพ์หรือเลือกไม่พิมพ์','error'); return; }

    const printerVal = $('printerSelect').value;
    const printer = (printerVal && printerVal !== 'none') ? printerVal : null;
    const printSheets = printer ? (parseInt($('sheetQty').value) || 1) : null;
    const status = computeStatus(selected);
    const totalQty = selected.reduce((sum, s) => sum + s.RecvQty, 0);
    const so = currentPO._soInfo || {};
    const soList = currentPO._soList || [];
    const soNums = soList.map(s => s.SONum).join(',');
    const custPONos = so.CustPONo || '';
    const custNames = so.CustName || '';

    pendingPayload = {
        PONum: currentPO.DocuNo,
        SONum: soNums || null,
        Status: status,
        Shelf: selectedShelf || null,
        Printer: printer,
        PrintSheets: printSheets,
        ReceivedBy: RECEIVED_BY || null,
        CustPONo: custPONos,
        CustName: custNames,
        items: selected,
        Photo: capturedPhoto
    };

    const statusBadge = status === 'ครบ'
        ? '<span class="confirm-status full">รับครบ</span>'
        : '<span class="confirm-status partial">รับบางส่วน</span>';

    $('confirmBody').innerHTML = `
        <div class="row"><span>เลขที่ PO</span><span><b>${esc(currentPO.DocuNo || '-')}</b></span></div>
        ${custNames ? `<div class="row"><span>ลูกค้า</span><span><b>${esc(custNames)}</b></span></div>` : ''}
        <div class="row"><span>จำนวนรายการ</span><span><b>${selected.length}</b> รายการ</span></div>
        <div class="row"><span>จำนวนรวม</span><span><b>${fmtQty(totalQty)}</b> ชิ้น</span></div>
        <div class="row"><span>ชั้นวาง</span><span><b>${esc(selectedShelf)}</b></span></div>
        <div class="row"><span>สถานะ PO</span><span>${statusBadge}</span></div>
        ${printer ? `<div class="row"><span>พิมพ์สติกเกอร์</span><span><b>${esc(printerLabel(printer))}</b> × ${printSheets}</span></div>` : ''}
    `;

    confirmScrollY = window.scrollY || window.pageYOffset || 0;
    document.body.style.position = 'fixed';
    document.body.style.top = `-${confirmScrollY}px`;
    document.body.style.left = '0'; document.body.style.right = '0';
    document.body.style.width = '100%';
    $('confirmOverlay').classList.add('show');
}

function closeConfirm(){
    $('confirmOverlay').classList.remove('show');
    document.body.style.position = ''; document.body.style.top = '';
    document.body.style.left = ''; document.body.style.right = '';
    document.body.style.width = '';
    window.scrollTo(0, confirmScrollY);
    pendingPayload = null;
}
function closeConfirmBackdrop(e){ if(e.target.id==='confirmOverlay') closeConfirm(); }

async function confirmSave(){
    if(!pendingPayload) return;
    const payload = pendingPayload;
    const selectedCount = payload.items.length;

    $('confirmOkBtn').disabled = true;
    $('confirmOkBtn').textContent = 'กำลังบันทึก...';
    $('btnNext').disabled = true;
    $('btnNext').textContent = 'กำลังบันทึก...';

    try{
        const res = await fetch(RECEIVE_URL,{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},
            body:JSON.stringify(payload)
        });
        const result = await res.json();
        if(!res.ok) throw new Error(result.message || ('HTTP '+res.status));

        closeConfirm();
        toast(`รับเข้าสำเร็จ ${selectedCount} รายการ`,'ok');
        clearResult();
        $('stateBox').innerHTML = '<div class="icon">🔎</div>พิมพ์เลขที่ PO แล้วกดค้นหา';
        $('stateBox').style.display = 'block';
    }catch(err){
        closeConfirm();
        toast('บันทึกไม่สำเร็จ : '+err.message,'error');
    }finally{
        $('confirmOkBtn').disabled = false;
        $('confirmOkBtn').textContent = 'ยืนยันบันทึก';
        $('btnNext').disabled = false;
        $('btnNext').textContent = 'บันทึกรับเข้า';
        updateCount();
    }
}

/* ========== Checkbox ========== */
function onCheck(i){
    const chk = $('chk-'+i), item = $('item-'+i);
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
    items.forEach((_, i) => { const chk = $('chk-'+i); if(!chk) return; chk.checked = !allChecked; onCheck(i); });
}
function updateCount(){
    if(!currentPO || !currentPO.ms_podt){
        $('btnNext').textContent = 'บันทึกรับเข้า'; $('btnNext').disabled = true; return;
    }
    let count = 0;
    currentPO.ms_podt.forEach((_, i) => { if($('chk-'+i) && $('chk-'+i).checked) count++; });
    $('btnNext').textContent = count > 0 ? `บันทึกรับเข้า (เลือก ${count})` : 'บันทึกรับเข้า';
    $('btnNext').disabled = count === 0;
}

/* ========== Qty ========== */
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

/* ========== Helpers ========== */
function splitGoodName(raw){
    if(!raw) return {name:'-', code:''};
    const idx = raw.indexOf('++');
    if(idx === -1) return {name: raw.trim(), code:''};
    return { name: raw.substring(0, idx).trim(), code: raw.substring(idx).replace(/\+\+|--/g,' ').replace(/\s+/g,' ').trim() };
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
function fmtDateTime(d){
    if(!d) return '-';
    const dt = new Date(d.replace(' ','T'));
    if(isNaN(dt)) return d;
    return dt.toLocaleDateString('th-TH',{day:'numeric',month:'short'})
        + ' ' + dt.toLocaleTimeString('th-TH',{hour:'2-digit',minute:'2-digit'});
}
function esc(s){ return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
function escJs(s){ return String(s).replace(/\\/g,'\\\\').replace(/'/g,"\\'"); }
function clearResult(){
    currentPO = null; historyDetailMap = new Map();
    $('poInput').value = '';
    $('poHead').innerHTML = ''; $('soCard').innerHTML = '';
    $('itemList').innerHTML = '';
    $('listTitle').style.display = 'none';
    $('topFields').style.display = 'none';
    $('navBar').classList.remove('show');
    resetShelf(); resetPrinter(); removePhoto();
}
let toastTimer;
function toast(msg, type=''){
    const t = $('toast');
    t.textContent = msg; t.className = 'show ' + type;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(()=> t.className='', type==='error' ? 8000 : 2600);
}
let cancelScrollY = 0;

function openCancelModal(){
    if(!lastFullyReceivedPO){ toast('ไม่พบเลขที่ PO','error'); return; }
    $('cancelPONum').textContent = lastFullyReceivedPO;

    cancelScrollY = window.scrollY || window.pageYOffset || 0;
    document.body.style.position = 'fixed';
    document.body.style.top = `-${cancelScrollY}px`;
    document.body.style.left = '0'; document.body.style.right = '0';
    document.body.style.width = '100%';
    $('cancelOverlay').classList.add('show');
}
function closeCancelModal(){
    $('cancelOverlay').classList.remove('show');
    document.body.style.position = ''; document.body.style.top = '';
    document.body.style.left = ''; document.body.style.right = '';
    document.body.style.width = '';
    window.scrollTo(0, cancelScrollY);
}
function closeCancelBackdrop(e){ if(e.target.id==='cancelOverlay') closeCancelModal(); }

async function doCancelReceive(){
    if(!lastFullyReceivedPO) return;

    $('cancelOkBtn').disabled = true;
    $('cancelOkBtn').textContent = 'กำลังยกเลิก...';

    try{
        const res = await fetch(CANCEL_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                PONum: lastFullyReceivedPO,
                Status: 'รับเข้าผิด',
                CancelBy: RECEIVED_BY || null
            })
        });
        const result = await res.json();
        if(!res.ok) throw new Error(result.message || ('HTTP ' + res.status));

        closeCancelModal();
        toast('ยกเลิกการรับเข้าเรียบร้อย','ok');
        lastFullyReceivedPO = null;
        clearResult();
        $('stateBox').innerHTML = '<div class="icon">🔎</div>พิมพ์เลขที่ PO แล้วกดค้นหา';
        $('stateBox').style.display = 'block';
    }catch(err){
        toast('ยกเลิกไม่สำเร็จ : ' + err.message, 'error');
    }finally{
        $('cancelOkBtn').disabled = false;
        $('cancelOkBtn').textContent = 'ยืนยันยกเลิก';
    }
}
</script>
</body>
</html>