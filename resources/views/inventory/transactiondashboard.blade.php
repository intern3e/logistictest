<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>รายการสินค้า เข้า-ออก - 3E TRADING</title>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Sarabun',Arial,sans-serif;background:#f9fafb;min-height:100vh;padding-bottom:40px;color:#1f2937}
    
    /* Overlay & Block Loading */
    .ov{position:fixed;inset:0;background:rgba(0,0,0,.85);display:flex;justify-content:center;align-items:center;z-index:9999;opacity:0;visibility:hidden;transition:opacity .3s,visibility .3s;backdrop-filter:blur(4px)}
    .ov.on{opacity:1;visibility:visible}
    .progress-container{width: 320px; text-align: center;}
    .ov-text{color:#fff;font-size:18px;font-weight:600;margin-bottom:20px;letter-spacing: 0.5px;}
    .progress-track{
      width: 100%; 
      height: 32px; 
      background: rgba(255,255,255,0.1); 
      border: 2px solid rgba(255,255,255,0.3);
      border-radius: 4px; 
      overflow: hidden; 
      margin-bottom: 12px;
      box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
    }
    .progress-fill{
      width: 0%; 
      height: 100%; 
      background-color: #5B65F3;
      background-image: repeating-linear-gradient(
        90deg,
        #5B65F3 0px,
        #5B65F3 18px,
        rgba(255,255,255,0.15) 18px,
        rgba(255,255,255,0.15) 20px
      );
      background-size: 20px 100%;
      transition: width 0.15s ease-out;
      box-shadow: 0 0 15px rgba(91,101,243,0.6);
    }
    .ov-percent{color:#fff;font-size:16px;font-weight:700;font-variant-numeric: tabular-nums; text-shadow: 0 2px 4px rgba(0,0,0,0.3);}

    .topbar{height:64px;background:#fff;display:flex;align-items:center;gap:12px;padding:0 24px;position:fixed;top:0;left:0;right:0;z-index:2000;border-bottom:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    .topbar-logo{height:36px;border-radius:6px}
    .topbar-title{font-size:18px;font-weight:700;color:#111827;flex:1;letter-spacing:-0.025em}
    .topbar-right{display:flex;align-items:center;gap:12px}
    .topbar-name{font-size:14px;color:#6b7280;font-weight:500}
    .topbar-badge{font-size:12px;padding:4px 10px;font-weight:600;color:#5B65F3;background:#EEF2FF;border-radius:6px;border:1px solid #C7D2FE}
    .hamburger{background:none;border:none;cursor:pointer;padding:8px;border-radius:8px;display:flex;flex-direction:column;gap:5px;flex-shrink:0;transition:background .2s}
    .hamburger span{display:block;width:20px;height:2px;background:#374151;transition:background .2s}
    .hamburger:hover{background:#f3f4f6}
    .hamburger:hover span{background:#5B65F3}
    
    .sb-ov{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1500;opacity:0;pointer-events:none;transition:opacity .2s}
    .sb-ov.open{opacity:1;pointer-events:all}
    .sidebar{position:fixed;top:0;left:-280px;width:260px;height:100vh;z-index:1600;transition:left .3s ease;display:flex;flex-direction:column;background:#fff;border-right:1px solid #e5e7eb;box-shadow:4px 0 24px rgba(0,0,0,.08)}
    .sidebar.open{left:0}
    .sb-head{display:flex;align-items:center;gap:12px;padding:16px 20px;background:#fff;border-bottom:1px solid #e5e7eb;min-height:64px}
    .sb-head img{height:32px;border-radius:6px}
    .sb-head span{font-size:18px;font-weight:700;color:#111827;flex:1;letter-spacing:-0.025em}
    .sb-close{background:none;border:none;color:#6b7280;cursor:pointer;font-size:20px;font-weight:bold;padding:4px 8px;border-radius:6px;transition:all .2s}
    .sb-close:hover{background:#f3f4f6;color:#111827}
    .sb-nav{flex:1;overflow-y:auto;padding:12px 0}
    .sb-sec{padding:12px 20px 6px;font-size:11px;font-weight:700;color:#6b7280;letter-spacing:.05em;text-transform:uppercase}
    .sb-item{display:flex;align-items:center;gap:12px;padding:10px 20px;color:#374151;cursor:pointer;font-size:14px;font-weight:500;border-left:3px solid transparent;user-select:none;text-decoration:none;transition:all .2s;border-radius:0 8px 8px 0;margin-right:8px}
    .sb-item:hover{background:#f9fafb;border-left-color:#5B65F3;color:#111827}
    .sb-item.cur{background:#EEF2FF;border-left-color:#5B65F3;color:#5B65F3;font-weight:600}
    
    #content{padding:88px 0 24px;width:100%}
    .hdr{padding:0 24px 24px}
    .hdr h2{color:#111827;font-size:24px;font-weight:700;margin-bottom:20px;letter-spacing:-0.025em}
    .fbar{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;background:#fff;padding:20px;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    .fbox{display:flex;flex-direction:column;gap:6px}
    .fbox label{font-weight:600;color:#374151;font-size:13px;display:flex;align-items:center;gap:6px}
    .fbox label::before{content:'';width:3px;height:12px;background:#5B65F3;border-radius:2px}
    .finput{padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;background:#fff;font-family:'Sarabun',sans-serif;width:100%;transition:all .2s;color:#111827}
    .finput:focus{outline:none;border-color:#5B65F3;box-shadow:0 0 0 3px rgba(91,101,243,.1)}
    .btn-clr{padding:10px 20px;border:1px solid #d1d5db;cursor:pointer;font-weight:600;font-size:14px;font-family:'Sarabun',sans-serif;background:#fff;color:#374151;border-radius:8px;transition:all .2s}
    .btn-clr:hover{background:#f9fafb;border-color:#9ca3af;color:#111827}
    .pdf-btn{padding:10px 24px;border:none;cursor:pointer;font-weight:600;font-size:14px;font-family:'Sarabun',sans-serif;color:#fff;background:#5B65F3;border-radius:8px;box-shadow:0 1px 3px rgba(91,101,243,.3);display:flex;align-items:center;gap:8px;white-space:nowrap;align-self:flex-end;transition:all .2s}
    .pdf-btn::before{content:'';display:inline-block;width:14px;height:16px;background:#fff;clip-path:polygon(0 0,65% 0,100% 30%,100% 100%,0 100%);opacity:.9;flex-shrink:0}
    .pdf-btn:hover{background:#4F46E5;box-shadow:0 4px 6px rgba(91,101,243,.2);transform:translateY(-1px)}
    .pdf-btn:disabled{opacity:.5;cursor:not-allowed;transform:none}
    
    .tbl-wrap{background:#fff;overflow-x:auto;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    table{width:100%;border-collapse:collapse;font-size:14px}
    thead{background:#f9fafb}
    th{color:#374151;padding:14px 16px;text-align:left;font-weight:600;font-size:13px;white-space:nowrap;position:sticky;top:0;background:#f9fafb;z-index:5;border:1px solid #e5e7eb;border-top:none}
    th:first-child{border-left:none}
    th:last-child{border-right:none}
    td{padding:12px 16px;border:1px solid #e5e7eb;color:#1f2937;font-size:14px;vertical-align:middle}
    td:first-child{border-left:none}
    td:last-child{border-right:none}
    tbody tr:hover{background:#f9fafb}
    .edit-input{width:100%;padding:8px 10px;font-size:13px;border:1px solid #d1d5db;border-radius:6px;font-family:'Sarabun',sans-serif;transition:all .2s}
    .edit-input:focus{outline:none;border-color:#5B65F3;box-shadow:0 0 0 3px rgba(91,101,243,.1)}
    td.tc{font-weight:600;color:#fff;text-align:center;padding:12px 16px;font-size:13px;white-space:nowrap}
    td.t-in{background:#10b981}
    td.t-ret{background:#06b6d4}
    td.t-sell{background:#ef4444}
    td.t-bor{background:#f59e0b}
    td.t-wit{background:#f97316}
    .view-img-btn{color:#5B65F3;text-decoration:none;font-weight:600;padding:6px 12px;background:#EEF2FF;border:none;border-radius:6px;font-size:13px;cursor:pointer;display:inline-block;transition:all .2s}
    .view-img-btn:hover{background:#5B65F3;color:#fff}
    .acts{display:flex;gap:6px;flex-wrap:wrap}
    .acts button{padding:6px 12px;cursor:pointer;font-weight:600;font-size:13px;font-family:'Sarabun',sans-serif;white-space:nowrap;border:none;color:#fff;border-radius:6px;transition:all .2s}
    .a-edit{background:#5B65F3}
    .a-save{background:#10b981}
    .a-del{background:#ef4444}
    .a-can{background:#e5e7eb;color:#374151}
    .a-edit:hover{background:#4F46E5}
    .a-save:hover{background:#059669}
    .a-del:hover{background:#dc2626}
    .a-can:hover{background:#d1d5db}
    
    #paging{margin-top:16px;text-align:center;display:flex;justify-content:center;align-items:center;gap:12px;padding:16px}
    .pg-btn{background:#fff;color:#374151;border:1px solid #d1d5db;padding:10px 20px;cursor:pointer;font-weight:600;font-size:14px;border-radius:8px;transition:all .2s}
    .pg-btn:hover{background:#f9fafb;border-color:#9ca3af;color:#111827}
    .pg-info{font-weight:600;font-size:14px;color:#374151;padding:10px 16px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px}
    
    .toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#111827;color:#fff;padding:12px 28px;font-size:14px;font-weight:600;z-index:9999;display:none;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.15)}
    .toast.on{display:block;animation:slideUp .3s ease}
    @keyframes slideUp{from{transform:translate(-50%,20px);opacity:0}to{transform:translate(-50%,0);opacity:1}}
    
    /* Image Modal/Popup — ใช้ img แทน iframe */
    .modal-ov{position:fixed;inset:0;background:rgba(0,0,0,.95);display:flex;justify-content:center;align-items:center;z-index:10000;opacity:0;visibility:hidden;transition:opacity .3s,visibility .3s}
    .modal-ov.on{opacity:1;visibility:visible}
    .modal-content{position:relative;width:90%;max-width:900px;max-height:85vh;background:#1a1a1a;border-radius:12px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;padding:20px}
    .modal-img{max-width:100%;max-height:80vh;object-fit:contain;border-radius:4px;display:block}
    .modal-img-loading{color:#fff;font-size:16px;font-weight:500;display:none}
    .modal-img-error{color:#ef4444;font-size:15px;font-weight:500;display:none;text-align:center;line-height:1.6}
    .modal-close{position:absolute;top:16px;right:16px;width:44px;height:44px;background:rgba(255,255,255,.95);border:none;border-radius:50%;cursor:pointer;font-size:24px;color:#374151;display:flex;align-items:center;justify-content:center;transition:all .2s;box-shadow:0 4px 12px rgba(0,0,0,.3);z-index:1}
    .modal-close:hover{background:#fff;color:#ef4444;transform:rotate(90deg);box-shadow:0 6px 16px rgba(0,0,0,.4)}
    .modal-open-link{position:absolute;bottom:16px;right:16px;padding:8px 16px;background:rgba(255,255,255,.95);border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;color:#374151;text-decoration:none;transition:all .2s;box-shadow:0 4px 12px rgba(0,0,0,.3);z-index:1;font-family:'Sarabun',sans-serif}
    .modal-open-link:hover{background:#fff;color:#5B65F3}
    
    @media(max-width:768px){.fbar{flex-direction:column} .fbox{width:100%!important} #content{padding:80px 0 16px} .hdr{padding:0 16px 16px}}
    
    .btn-home{
      display:inline-flex;align-items:center;gap:8px;padding:10px 20px;font-family:'Sarabun',sans-serif;font-size:14px;font-weight:600;color:#fff;text-decoration:none;cursor:pointer;white-space:nowrap;background:#5B65F3;border-radius:8px;box-shadow:0 1px 3px rgba(91,101,243,.3);transition:all .2s;
    }
    .btn-home:hover{background:#4F46E5;box-shadow:0 4px 6px rgba(91,101,243,.2);transform:translateY(-1px);}
    .btn-home:active{transform:translateY(0);}
  </style>
</head>
<body>

<!-- Overlay with Block Loading -->
<div class="ov" id="ov">
  <div class="progress-container">
    <p class="ov-text" id="ovText">กำลังโหลดข้อมูล...</p>
    <div class="progress-track">
      <div class="progress-fill" id="progressBar"></div>
    </div>
    <p class="ov-percent" id="ovPercent">0%</p>
  </div>
</div>

<!-- Image Modal Popup — ใช้ img แทน iframe เพื่อแก้ CSP -->
<div class="modal-ov" id="modalOv">
  <div class="modal-content">
    <button class="modal-close" onclick="closeModal()">&#10005;</button>
    <span class="modal-img-loading" id="modalLoading">กำลังโหลดรูปภาพ...</span>
    <img class="modal-img" id="modalImg" src="" alt="รูปประกอบ" style="display:none;">
    <div class="modal-img-error" id="modalError">
      ไม่สามารถโหลดรูปภาพได้<br>
      <small>รูปภาพอาจไม่ได้ตั้งค่าการแชร์เป็นสาธารณะ</small><br>
      <a id="modalFallbackLink" href="#" target="_blank" 
         style="color:#5B65F3;margin-top:8px;display:inline-block;font-size:14px;font-weight:600">
        เปิดใน Google Drive →
      </a>
    </div>
    <a class="modal-open-link" id="modalOpenLink" href="#" target="_blank">เปิดใน Google Drive ↗</a>
  </div>
</div>

<div class="toast" id="toast"></div>

@php $q = ['create_by' => $authUser['name'] ?? '']; @endphp
<div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
<div class="sidebar" id="sidebar">
  <div class="sb-head"><img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo"><span>3E TRADING</span><button class="sb-close" onclick="closeSB()">&#10005;</button></div>
  <div class="sb-nav">
    <div class="sb-sec">เมนูหลัก</div>
    <a class="sb-item cur" href="{{ route('inventory.transaction', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รายการสินค้า เข้า-ออก</a>
    <a class="sb-item"target="_blank" href="{{ route('inventory.item', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>ค้นหาสินค้า</a>
    @if(str_contains($authUser['page'] ?? '', 'pr'))
      <a class="sb-item"target="_blank" href="{{ route('inventory.pr', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>สร้างใบขอซื้อ</a>
    @endif
    @if(in_array($authRole, ['admin','user']))
      <div style="height:1px;background:#e5e7eb;margin:8px 20px"></div>
      <div class="sb-sec">ดำเนินการ</div>
      <a class="sb-item"target="_blank" href="{{ route('inventory.stockout', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>ขายสินค้าออก</a>
      <a class="sb-item" target="_blank"href="{{ route('inventory.withdraw', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>เบิกของ</a>
    @endif
      @if(in_array($authRole,['admin']))
      <a class="sb-item"target="_blank" href="{{ route('inventory.users', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>จัดการผู้ใช้งาน</a>
      <a class="sb-item"target="_blank" href="{{ route('inventory.pr.dashboard', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>ขอซื้อ</a>
    @endif
  </div>
</div>

<div class="topbar">
  <button class="hamburger" onclick="openSB()"><span></span><span></span><span></span></button>
  <img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo" class="topbar-logo">
  <span class="topbar-title">3E TRADING</span>
  <div class="topbar-right">
    <span class="topbar-name">ผู้ใช้: {{ $authUser['name'] ?? '' }}</span>
    <span class="topbar-badge">{{ strtoupper($authRole) }}</span>
    <a href="http://server_update:8000/solist" button type="submit" class="btn-home"> หน้าหลัก</a>
  </div>
</div>

<div id="content">
  <div class="hdr">
    <h2>รายการสินค้า เข้า-ออก</h2>
    <div class="fbar">
      <div class="fbox" style="width:160px"><label>เลือกวันที่</label><input type="date" id="fDate" class="finput" onchange="applyFilter()"></div>
      <div class="fbox" style="width:200px"><label>ชื่อผู้ดำเนินงาน</label><input type="text" id="fOp" class="finput" placeholder="ค้นหา..." oninput="applyFilter()"></div>
      <div class="fbox" style="width:240px"><label>หมายเลขเอกสาร</label><input type="text" id="fBill" class="finput" placeholder="ค้นหา..." oninput="applyFilter()"></div>
      <div class="fbox" style="flex:1;min-width:200px"><label>รายการสินค้า</label><input type="text" id="fItem" class="finput" placeholder="ค้นหา..." oninput="applyFilter()"></div>
      <div class="fbox" style="width:180px"><label>ประเภทข้อมูล</label>
        <select id="fType" class="finput" onchange="applyFilter()">
          <option value="">ทั้งหมด</option><option value="รับเข้าสต็อก">รับเข้าสต็อก</option><option value="คืนเข้าสต็อก">คืนเข้าสต็อก</option>
          <option value="ขายสินค้าออก">ขายสินค้าออก</option><option value="ยืมสินค้า">ยืมสินค้า</option><option value="เบิกของ">เบิกของ</option>
        </select>
      </div>
      <div class="fbox" style="width:140px"><label>ชั้นวาง</label><input type="text" id="fShelf" class="finput" placeholder="ค้นหา..." oninput="applyFilter()"></div>
      @if(in_array($authRole, ['admin','user']))
        <button class="pdf-btn" id="pdfBtn" onclick="generatePDF()">PDF</button>
      @endif
      <button class="btn-clr" onclick="clearFilter()">ล้างตัวกรอง</button>
    </div>
  </div>
  <div class="tbl-wrap">
    <table>
      <thead><tr>
        <th>Timestamp</th><th>ผู้ดำเนินงาน</th><th>ประเภท</th><th>เอกสาร</th><th>รายการ</th><th>จำนวน</th><th>ราคา/หน่วย</th><th>ชั้นวาง</th><th>JOB Detail</th><th>รูป</th>
        @if($authRole==='admin')<th>จัดการ</th>@endif
      </tr></thead>
      <tbody id="tb"></tbody>
    </table>
  </div>
  <div id="paging"></div>
</div>

<script>
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
const ROLE=@json($authRole);
const COLS=ROLE==='admin'?11:10;
const PG=100;
const typeMap={'รับเข้าสต็อก':'t-in','คืนเข้าสต็อก':'t-ret','ขายสินค้าออก':'t-sell','ยืมสินค้า':'t-bor','เบิกของ':'t-wit'};
const NEST_URL=@json($nestUrl);
const NEST_KEY=@json($nestKey);
let isEditingTxRow = false;

const API={
  async get(u){return(await fetch(u)).json()},
  async put(u,d){return(await fetch(u,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(d)})).json()},
  async del(u){return(await fetch(u,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF}})).json()},
};

let pageData=[], pg=1, lastPage=1, total=0;
let _debounce=null;
let progressInterval = null;

function showOv(t){
  document.getElementById('ovText').textContent = t || 'กำลังโหลดข้อมูล...';
  document.getElementById('ov').classList.add('on');
  startProgressSimulation();
}

function hideOv(){
  clearInterval(progressInterval);
  document.getElementById('progressBar').style.width = '100%';
  document.getElementById('ovPercent').textContent = '100%';

  setTimeout(() => {
    document.getElementById('ov').classList.remove('on');
    setTimeout(() => {
      document.getElementById('progressBar').style.width = '0%';
      document.getElementById('ovPercent').textContent = '0%';
    }, 300);
  }, 200);
}

function startProgressSimulation(){
  let progress = 0;
  const bar = document.getElementById('progressBar');
  const text = document.getElementById('ovPercent');

  clearInterval(progressInterval);

  progressInterval = setInterval(() => {
    if (progress < 30) {
      progress += Math.random() * 15;
    } else if (progress < 70) {
      progress += Math.random() * 8;
    } else if (progress < 90) {
      progress += Math.random() * 3;
    } else {
      progress = 90;
    }

    if (progress > 90) progress = 90;

    bar.style.width = Math.floor(progress) + '%';
    text.textContent = Math.floor(progress) + '%';
  }, 150);
}

function openSB(){document.getElementById('sidebar').classList.add('open');document.getElementById('sbOv').classList.add('open')}
function closeSB(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sbOv').classList.remove('open')}
function toast(m,e){const t=document.getElementById('toast');t.textContent=m;t.style.background=e?'#ef4444':'#10b981';t.classList.add('on');setTimeout(()=>t.classList.remove('on'),3000)}

// ============================================================
// ส่วนแสดงรูปภาพ — แก้ CSP: ใช้ img + lh3 thumbnail แทน iframe
// ============================================================

function extractGoogleDriveFileId(url){
  if(!url) return null;
  const patterns = [
    /\/file\/d\/([a-zA-Z0-9_-]+)/,
    /\?id=([a-zA-Z0-9_-]+)/,
    /&id=([a-zA-Z0-9_-]+)/
  ];
  for(let pattern of patterns){
    const match = url.match(pattern);
    if(match && match[1]) return match[1];
  }
  return null;
}

function convertToDirectImageUrl(url){
  const fileId = extractGoogleDriveFileId(url);
  if(fileId){
    return `https://lh3.googleusercontent.com/d/${fileId}`;
  }
  return url;
}

function getGoogleDriveViewUrl(url){
  const fileId = extractGoogleDriveFileId(url);
  if(fileId){
    return `https://drive.google.com/file/d/${fileId}/view`;
  }
  return url;
}

let _currentOriginalUrl = '';

function openGoogleDriveImage(url){
  if(!url){
    toast('ไม่มีรูปภาพ', true);
    return;
  }

  _currentOriginalUrl = url;

  const imgEl = document.getElementById('modalImg');
  const loadingEl = document.getElementById('modalLoading');
  const errorEl = document.getElementById('modalError');
  const openLink = document.getElementById('modalOpenLink');
  const fallbackLink = document.getElementById('modalFallbackLink');

  const viewUrl = getGoogleDriveViewUrl(url);
  openLink.href = viewUrl;
  fallbackLink.href = viewUrl;

  imgEl.style.display = 'none';
  errorEl.style.display = 'none';
  loadingEl.style.display = 'block';

  const directUrl = convertToDirectImageUrl(url);

  imgEl.onload = function(){
    loadingEl.style.display = 'none';
    errorEl.style.display = 'none';
    imgEl.style.display = 'block';
  };

  imgEl.onerror = function(){
    loadingEl.style.display = 'none';
    imgEl.style.display = 'none';
    errorEl.style.display = 'block';
  };

  imgEl.src = directUrl;

  document.getElementById('modalOv').classList.add('on');
  document.body.style.overflow = 'hidden';
}

function closeModal(){
  document.getElementById('modalOv').classList.remove('on');
  document.body.style.overflow = '';
  _currentOriginalUrl = '';

  setTimeout(() => {
    const imgEl = document.getElementById('modalImg');
    imgEl.src = '';
    imgEl.style.display = 'none';
    document.getElementById('modalLoading').style.display = 'none';
    document.getElementById('modalError').style.display = 'none';
  }, 300);
  flushTxPendingRefresh();
}

document.addEventListener('keydown', function(e){
  if(e.key === 'Escape') closeModal();
});

document.getElementById('modalOv').addEventListener('click', function(e){
  if(e.target === this) closeModal();
});

// ============================================================

function buildQuery(page,limit){
  const fD=document.getElementById('fDate').value;
  const p=new URLSearchParams();
  p.set('page',page); p.set('limit',limit||PG);
  if(fD){const parts=fD.split('-');p.set('fDate',parts[2]+'/'+parts[1]+'/'+parts[0])}
  const fOp=document.getElementById('fOp').value.trim();    if(fOp) p.set('fOp',fOp);
  const fBill=document.getElementById('fBill').value.trim();if(fBill) p.set('fBill',fBill);
  const fItem=document.getElementById('fItem').value.trim();if(fItem) p.set('fItem',fItem);
  const fType=document.getElementById('fType').value;        if(fType) p.set('fType',fType);
  const fShelf=document.getElementById('fShelf').value.trim();if(fShelf) p.set('fShelf',fShelf);
  return '/api/transaction?'+p.toString();
}

async function loadPage(page, showLoader){
  if(showLoader) showOv();
  try{
    const res=await API.get(buildQuery(page));
    pageData=res.data||[];
    pg=res.page||1;
    lastPage=res.lastPage||1;
    total=res.total||0;
    render();
  }catch(e){alert('โหลดล้มเหลว: '+e.message)}
  if(showLoader) hideOv();
}

function render(){
  const tb=document.getElementById('tb');tb.innerHTML='';
  if(!pageData.length){tb.innerHTML=`<tr><td colspan="${COLS}" style="text-align:center;padding:40px;color:#9ca3af">ไม่มีรายการ (${total} ทั้งหมด)</td></tr>`;renderPg();return}

  pageData.forEach((row,i)=>{
    const type=row['ประเภทข้อมูล']||'',tc=typeMap[type]||'';

    const tr=document.createElement('tr');
    tr.innerHTML=`<td style="white-space:nowrap">${row['Timestamp']||'-'}</td><td>${row['ชื่อผู้ดำเนินงาน']||'-'}</td><td class="tc ${tc}">${type||'-'}</td><td>${row['หมายเลขเอกสาร']||'-'}</td><td>${row['รายการ']||'-'}</td><td>${row['จำนวน']!==''?row['จำนวน']:'-'}</td><td>${ROLE==='viewer'?'-':(row['ราคาต่อหน่วย']!==''?row['ราคาต่อหน่วย']:'-')}</td><td>${row['ชั้นวาง']||'-'}</td><td>${row['หมายเหตุ']||'-'}</td><td></td>`;

    if(row['รูปประกอบ']){
      const btn = document.createElement('button');
      btn.className = 'view-img-btn';
      btn.textContent = 'ดูรูป';
      btn.onclick = function(){
        openGoogleDriveImage(row['รูปประกอบ']);
      };
      tr.cells[9].appendChild(btn);
    } else {
      tr.cells[9].textContent = '-';
    }

    if(ROLE==='admin'){
      const tdActs = document.createElement('td');
      tdActs.className = 'acts';
      const editBtn = document.createElement('button');
      editBtn.className = 'a-edit';
      editBtn.textContent = 'แก้ไข';
      editBtn.onclick = function(){ editRow(i); };
      tdActs.appendChild(editBtn);
      tr.appendChild(tdActs);
    }

    tb.appendChild(tr);
  });
  renderPg();
}

function renderPg(){
  const el=document.getElementById('paging');el.innerHTML='';
  if(pg>1){const b=document.createElement('button');b.className='pg-btn';b.textContent='← ก่อนหน้า';b.onclick=()=>{loadPage(pg-1,false);scrollTo(0,0)};el.appendChild(b)}
  const s=document.createElement('span');s.className='pg-info';s.textContent=`หน้า ${pg} / ${lastPage} (${total.toLocaleString()} รายการ)`;el.appendChild(s);
  if(pg<lastPage){const b=document.createElement('button');b.className='pg-btn';b.textContent='ถัดไป →';b.onclick=()=>{loadPage(pg+1,false);scrollTo(0,0)};el.appendChild(b)}
}

function applyFilter(){
  clearTimeout(_debounce);
  _debounce=setTimeout(()=>loadPage(1,false),400);
}
function clearFilter(){
  ['fBill','fItem','fDate','fType','fShelf','fOp'].forEach(id=>document.getElementById(id).value='');
  loadPage(1,false);
}

function editRow(i){
  if(ROLE!=='admin')return;
  isEditingTxRow = true;
  const row=pageData[i],tr=document.getElementById('tb').children[i];if(!tr)return;tr.style.background='#EEF2FF';
  const types=['รับเข้าสต็อก','คืนเข้าสต็อก','ขายสินค้าออก','ยืมสินค้า','เบิกของ'];
  tr.innerHTML=`<td style="white-space:nowrap;font-size:12px">${row['Timestamp']||'-'}</td><td><input class="edit-input" id="eOp" value="${(row['ชื่อผู้ดำเนินงาน']||'').replace(/"/g, '&quot;')}"></td><td><select id="eType" class="edit-input">${types.map(t=>`<option ${row['ประเภทข้อมูล']===t?'selected':''}>${t}</option>`).join('')}</select></td><td><input class="edit-input" id="eBill" value="${(row['หมายเลขเอกสาร']||'').replace(/"/g, '&quot;')}"></td><td style="color:#374151;font-size:13px">${(row['รายการ']||'-').replace(/"/g, '&quot;')}</td><td><input class="edit-input" id="eQty" value="${row['จำนวน']||''}" type="number"></td><td><input class="edit-input" id="ePrice" value="${row['ราคาต่อหน่วย']!==''&&row['ราคาต่อหน่วย']!=null?row['ราคาต่อหน่วย']:''}" type="number" step="0.001"></td><td><input class="edit-input" id="eShelf" value="${(row['ชั้นวาง']||'').replace(/"/g, '&quot;')}"></td><td><input class="edit-input" id="eNote" value="${(row['หมายเหตุ']||'').replace(/"/g, '&quot;')}"></td><td><input type="hidden" id="eImg" value="${(row['รูปประกอบ']||'').replace(/"/g, '&quot;')}">-</td><td class="acts"><button class="a-save" onclick="saveRow(${i})">บันทึก</button><button class="a-del" onclick="delRow(${i})">ลบ</button><button class="a-can" onclick="isEditingTxRow=false;loadPage(pg,false);flushTxPendingRefresh()">ยกเลิก</button></td>`;
}

async function saveRow(i){
  const row=pageData[i];
  const op=document.getElementById('eOp').value.trim(),bill=document.getElementById('eBill').value.trim();
  if(!op||!bill){alert('กรุณากรอกข้อมูลให้ครบ');return}
  if(!confirm('ต้องการบันทึก?'))return;
  try{
    await API.put('/api/transaction/'+encodeURIComponent(row.transaction_id),{operator:op,type:document.getElementById('eType').value,bill,quantity:document.getElementById('eQty').value,price:document.getElementById('ePrice').value||'',shelf:document.getElementById('eShelf').value.trim(),note:document.getElementById('eNote').value.trim(),image:document.getElementById('eImg').value.trim(),oldQuantity:row['จำนวน'],oldType:row['ประเภทข้อมูล'],oldItemId:row['item_id']});
    toast('บันทึกสำเร็จ');
    isEditingTxRow=false;
    await loadPage(pg,false);
    flushTxPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

async function delRow(i){
  const row=pageData[i];
  if(!confirm(`ลบรายการ?\nเอกสาร: ${row['หมายเลขเอกสาร']}\nรายการ: ${row['รายการ']}`))return;
  try{
    await API.del('/api/transaction/'+encodeURIComponent(row.transaction_id));
    toast('ลบเรียบร้อย');
    isEditingTxRow=false;
    await loadPage(pg,false);
    flushTxPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

let _fontN=null,_fontB=null;

async function _fetchFontB64(url){
  const buf=await(await fetch(url)).arrayBuffer();
  const bytes=new Uint8Array(buf);let bin='';
  for(let i=0;i<bytes.length;i+=8192)bin+=String.fromCharCode.apply(null,bytes.subarray(i,i+8192));
  return btoa(bin);
}
async function loadFonts(){
  if(_fontN&&_fontB)return;
  [_fontN,_fontB]=await Promise.all([
    _fetchFontB64('https://cdn.jsdelivr.net/gh/google/fonts@main/ofl/sarabun/Sarabun-Regular.ttf'),
    _fetchFontB64('https://cdn.jsdelivr.net/gh/google/fonts@main/ofl/sarabun/Sarabun-Bold.ttf')
  ]);
}
function normalizeThai(t){return typeof t==='string'?t.normalize('NFC'):String(t??'-')}
function convertDateFormat(ymd){
  if(!ymd)return'-';
  const[y,m,d]=ymd.split('-');
  const months=['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
  return`${parseInt(d)} ${months[parseInt(m)]} ${parseInt(y)+543}`;
}

async function generatePDF(){
  const dateEl=document.getElementById('fDate');
  if(!dateEl.value){alert('กรุณาเลือกวันที่');return}
  if(typeof window.jspdf==='undefined'){alert('กำลังโหลด library PDF กรุณารอสักครู่แล้วลองอีกครั้ง');return}
  const btn=document.getElementById('pdfBtn');
  btn.disabled=true;
  const originalLabel=btn.textContent;
  btn.textContent='กำลังสร้าง...';
  try{
    const res=await API.get(buildQuery(1,1000000));
    let dataToUse=res.data||[];
    if(!dataToUse.length){alert('ไม่มีข้อมูล');return}

    await loadFonts();
    const{jsPDF}=window.jspdf;
    const doc=new jsPDF('p','mm','a4');
    doc.addFileToVFS('Sarabun-R.ttf',_fontN);doc.addFont('Sarabun-R.ttf','Sarabun','normal');
    doc.addFileToVFS('Sarabun-B.ttf',_fontB);doc.addFont('Sarabun-B.ttf','Sarabun','bold');

    const typeValue=document.getElementById('fType').value||'ทั้งหมด';
    const rawDate=dateEl.value;
    const fileName=`รายงาน(${typeValue})สต็อก-(${rawDate}).pdf`;

    if(typeValue==='ทั้งหมด'){
      const typeOrder={'รับเข้าสต็อก':1,'คืนเข้าสต็อก':2,'ขายสินค้าออก':3,'ยืมสินค้า':4,'เบิกของ':5};
      dataToUse=[...dataToUse].sort((a,b)=>(typeOrder[a['ประเภทข้อมูล']]||999)-(typeOrder[b['ประเภทข้อมูล']]||999));
    }

    const tableBody=dataToUse.map(r=>[
      normalizeThai(r.Timestamp||'-'),
      normalizeThai(r['ชื่อผู้ดำเนินงาน']||'-'),
      normalizeThai(r['ประเภทข้อมูล']||'-'),
      normalizeThai(r['หมายเลขเอกสาร']||'-'),
      normalizeThai(r['รายการ']||'-'),
      normalizeThai(String(r['จำนวน']??'-')),
      normalizeThai(String(r['ราคาต่อหน่วย']??'-')),
      normalizeThai(r['ชั้นวาง']||'-'),
    ]);

    doc.setFont('Sarabun','bold');doc.setFontSize(18);doc.setTextColor(0,0,0);
    doc.text(normalizeThai('3E TRADING'),105,15,{align:'center'});
    doc.setFontSize(14);
    doc.text(normalizeThai(`รายงานประวัติการดำเนินการคลังสินค้า (${typeValue})`),105,23,{align:'center'});
    doc.setFont('Sarabun','normal');doc.setFontSize(12);
    doc.text(normalizeThai(`วันที่รายงาน: ${convertDateFormat(rawDate)}`),15,32);

    doc.autoTable({
      startY:38,
      head:[['เวลา','ผู้ดำเนินงาน','ประเภท','หมายเลขเอกสาร','รายการสินค้า','จำนวน','ราคาต่อหน่วย','ชั้น'].map(normalizeThai)],
      body:tableBody,
      theme:'grid',
      rowPageBreak:'avoid',
      margin:{top:30,bottom:25,left:10,right:10},
      styles:{font:'Sarabun',fontSize:11,cellPadding:{top:4,right:2,bottom:2,left:2},valign:'middle',textColor:[0,0,0],lineColor:[0,0,0],lineWidth:.1,overflow:'linebreak'},
      headStyles:{fillColor:[255,255,255],textColor:[0,0,0],fontStyle:'bold',halign:'center',lineWidth:.2},
      alternateRowStyles:{fillColor:[255,255,255]},
      columnStyles:{0:{cellWidth:28},1:{cellWidth:20,halign:'center'},2:{cellWidth:24,halign:'center'},3:{cellWidth:26},4:{cellWidth:'auto'},5:{cellWidth:14,halign:'center'},6:{cellWidth:26,halign:'right'},7:{cellWidth:14,halign:'center'}},
    });

    const pageCount=doc.internal.getNumberOfPages();
    for(let i=1;i<=pageCount;i++){
      doc.setPage(i);doc.setFont('Sarabun','normal');doc.setFontSize(9);doc.setTextColor(0,0,0);
      doc.text(normalizeThai(`พิมพ์เมื่อ: ${new Date().toLocaleString('th-TH')}`),15,285);
      doc.text(normalizeThai(`หน้า ${i} จาก ${pageCount}`),195,285,{align:'right'});
    }
    doc.save(fileName);
    toast('สร้าง PDF เรียบร้อย: '+fileName);
  }catch(err){
    console.error(err);
    alert('Error: '+err.message);
  }finally{
    btn.disabled=false;
    btn.textContent=originalLabel;
  }
}

// โหลดข้อมูลครั้งแรกตอนเข้าหน้า — จุดเดียวที่แสดง overlay progress bar
loadPage(1,true);

// ══════════════ REALTIME (SSE) ══════════════
let txRefreshPending = false;
let txRefreshDebounce = null;

function isBusyEditingTx(){
  return document.getElementById('modalOv').classList.contains('on') || isEditingTxRow;
}

function requestTxSilentRefresh(){
  clearTimeout(txRefreshDebounce);
  txRefreshDebounce = setTimeout(() => {
    if (isBusyEditingTx()) { txRefreshPending = true; return; }
    loadPage(pg, false);
  }, 300);
}

function flushTxPendingRefresh(){
  if (!txRefreshPending || isBusyEditingTx()) return;
  txRefreshPending = false;
  loadPage(pg, false);
}

function connectTransactionSSE(){
  const url = `${NEST_URL}/transaction/events?key=${encodeURIComponent(NEST_KEY)}`;
  const es = new EventSource(url);
  es.onmessage = (e) => {
    if (e.data === 'heartbeat') return;
    if (e.data === 'transaction' || e.data === 'items') requestTxSilentRefresh();
  };
  es.onerror = () => console.warn('[SSE transaction] connection issue, browser will auto-retry');
}
connectTransactionSSE();
</script>
</body>
</html>