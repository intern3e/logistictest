<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Dashboard ใบขอซื้อ - 3E TRADING</title>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Sarabun',Arial,sans-serif;background:#ece9d8;min-height:100vh;padding-bottom:40px}
    .topbar{height:52px;background:#444;display:flex;align-items:center;gap:10px;padding:0 12px;position:fixed;top:0;left:0;right:0;z-index:2000;border-bottom:2px solid #1a3f7a;box-shadow:0 2px 8px rgba(0,0,0,.5)}
    .topbar-logo{height:34px;border:1px solid rgba(255,255,255,.3)}
    .topbar-title{font-size:17px;font-weight:700;color:#fff;flex:1}
    .topbar-right{display:flex;align-items:center;gap:10px}
    .topbar-name{font-size:13px;color:rgba(255,255,255,.8)}
    .topbar-badge{font-size:11px;padding:2px 8px;font-weight:700;color:#000;background:#FCA50D}
    .hamburger{background:none;border:none;cursor:pointer;padding:5px;display:flex;flex-direction:column;gap:4px}
    .hamburger span{display:block;width:20px;height:2px;background:#fff}
    .sb-ov{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1500;opacity:0;pointer-events:none;transition:opacity .2s}
    .sb-ov.open{opacity:1;pointer-events:all}
    .sidebar{position:fixed;top:0;left:-250px;width:230px;height:100vh;z-index:1600;transition:left .25s;display:flex;flex-direction:column;background:linear-gradient(180deg,#dce9fa,#c5d9f5);border-right:2px solid #7a9fc8;box-shadow:3px 0 12px rgba(0,0,0,.3)}
    .sidebar.open{left:0}
    .sb-head{display:flex;align-items:center;gap:8px;padding:10px 12px;background:linear-gradient(180deg,#4a7db5,#2358a4);border-bottom:2px solid #1a3f7a;min-height:52px}
    .sb-head img{height:30px}
    .sb-head span{font-size:16px;font-weight:700;color:#fff;flex:1}
    .sb-close{background:none;border:none;color:rgba(255,255,255,.8);cursor:pointer;font-size:18px;font-weight:bold;padding:0 4px}
    .sb-nav{flex:1;overflow-y:auto;padding:6px 0}
    .sb-sec{padding:8px 12px 3px;font-size:11px;font-weight:700;color:#1e4d96;letter-spacing:.8px;text-transform:uppercase}
    .sb-item{display:flex;align-items:center;gap:10px;padding:9px 14px;color:#1a1a6e;cursor:pointer;font-size:14px;font-weight:500;border-left:3px solid transparent;user-select:none;text-decoration:none}
    .sb-item:hover{background:rgba(42,95,168,.15);border-left-color:#2a5fa8}
    .sb-item.cur{background:rgba(42,95,168,.2);border-left-color:#FCA50D;font-weight:700}
    #content{width:100%;margin:0 auto;padding:68px 16px 40px}
    .page-title{font-size:20px;font-weight:700;color:#1a1a6e;margin-bottom:16px;padding-bottom:6px;border-bottom:2px solid #a8c3e0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px}
    .stat-row{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px}
    .stat-card{background:#fff;border:2px inset #aaa;padding:12px 14px;text-align:center}
    .stat-card .num{font-size:28px;font-weight:700}
    .stat-card .lbl{font-size:14px;color:#666;margin-top:2px}
    .stat-card.all .num{color:#333}.stat-card.pend .num{color:#D97706}.stat-card.appr .num{color:#16a34a}.stat-card.rej .num{color:#dc2626}
    .filter-bar{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;align-items:flex-end}
    .filter-box{display:flex;flex-direction:column;gap:3px}
    .filter-box label{font-size:13px;font-weight:600;color:#333}
    .filter-input{padding:5px 7px;border:2px inset #aaa;font-size:14px;font-family:'Sarabun',Arial,sans-serif;background:#fff;outline:none}
    .filter-input:focus{border-color:#2358a4}
    .btn-clear-filter{padding:6px 14px;border:1px solid #999;background:linear-gradient(180deg,#f0f0f0,#d0d0d0);font-size:14px;font-weight:600;cursor:pointer;font-family:'Sarabun',Arial,sans-serif}
    .table-wrap{background:#fff;border:2px inset #aaa;width:100%;overflow-x:auto}
    table{width:100%;border-collapse:collapse;font-size:14px;table-layout:auto}
    thead{background:linear-gradient(180deg,#555,#333)}
    th{color:#fff;padding:8px 10px;text-align:left;font-size:13px;white-space:nowrap;border-right:1px solid #555}
    th:last-child{border-right:none;width:110px;white-space:nowrap}
    td:last-child{width:110px;white-space:nowrap}
    th:nth-child(1),td:nth-child(1){width:180px}
    th:nth-child(2),td:nth-child(2){width:90px}
    th:nth-child(3),td:nth-child(3){width:80px}
    th:nth-child(4),td:nth-child(4){width:110px}
    th:nth-child(5),td:nth-child(5){width:100px}
    th:nth-child(6),td:nth-child(6){width:90px}
    th:nth-child(7),td:nth-child(7){width:100px}
    th:nth-child(9),td:nth-child(9){width:100px}
    th:nth-child(10),td:nth-child(10){width:90px}
    th:nth-child(11),td:nth-child(11){width:100px}
    td{padding:7px 10px;border-bottom:1px solid #f0f0f0;font-size:14px;vertical-align:middle}
    tbody tr:hover{background:#f0f5ff}
    .badge{display:inline-block;padding:3px 10px;font-size:12px;font-weight:700}
    .badge-pending{background:#FEF3C7;color:#D97706;border:1px solid #D97706}
    .badge-approved{background:#DCFCE7;color:#16a34a;border:1px solid #16a34a}
    .badge-rejected{background:#FEE2E2;color:#dc2626;border:1px solid #dc2626}
    .btn-view{padding:4px 12px;background:linear-gradient(180deg,#6090f0,#2749F5);color:#fff;border:1px solid #1a35c7;font-size:13px;font-weight:600;cursor:pointer;font-family:'Sarabun',Arial,sans-serif;white-space:nowrap}
    .iditem-chip{display:inline-block;background:#e8f0fe;color:#1a3f7a;font-size:11px;font-weight:700;padding:2px 5px;border:1px solid #a8c3e0;white-space:nowrap;margin:1px 1px}
    .pagination-bar{display:flex;justify-content:center;align-items:center;gap:12px;padding:12px 0;margin-top:8px}
    .page-btn{padding:6px 18px;border:1px solid #999;background:linear-gradient(180deg,#f0f0f0,#d0d0d0);font-size:14px;font-weight:600;cursor:pointer;font-family:'Sarabun',Arial,sans-serif}
    .page-btn:disabled{opacity:.4;cursor:not-allowed}
    .page-info{font-size:14px;font-weight:600;padding:6px 14px;background:#f5f5f5;border:1px solid #ccc}
    .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:8000;display:none;justify-content:center;align-items:flex-start;padding:0;overflow-y:auto}
    .modal-overlay.active{display:flex}
    .modal{background:#fff;width:100%;max-width:100%;min-height:100vh;border:none;box-shadow:0 20px 60px rgba(0,0,0,.5);display:flex;flex-direction:column;animation:mIn .25s ease}
    @keyframes mIn{from{transform:scale(.9);opacity:0}to{transform:scale(1);opacity:1}}
    .modal-header{background:linear-gradient(180deg,#4a7db5,#2358a4);color:#fff;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
    .modal-header h3{font-size:17px;font-weight:700}
    .modal-close{background:none;border:none;color:#fff;font-size:22px;cursor:pointer;padding:0 4px}
    .modal-body{padding:20px;overflow-y:visible;flex:1}
    .modal-section{margin-bottom:16px}
    .modal-section-title{font-size:13px;font-weight:700;color:#1e4d96;letter-spacing:.8px;text-transform:uppercase;border-bottom:1px solid #ddd;padding-bottom:4px;margin-bottom:10px}
    .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px 16px}
    .info-item label{font-size:12px;color:#888;font-weight:600;display:block;margin-bottom:2px}
    .info-item span{font-size:15px;color:#222;font-weight:500}
    .modal-table{width:100%;border-collapse:collapse;font-size:13px}
    .modal-table th{background:#444;color:#fff;padding:6px 8px;text-align:left;white-space:nowrap;font-size:12px}
    .modal-table td{padding:6px 8px;border-bottom:1px solid #eee;vertical-align:middle}
    .modal-table tfoot td{background:#f5f5f5;font-weight:700}
    .table-scroll{overflow-x:auto}
    .reason-box{background:#f9f9f9;border:1px solid #e0e0e0;padding:10px 12px;font-size:14px;color:#333;line-height:1.7}
    .modal-footer{padding:14px 20px;border-top:2px solid #eee;display:flex;gap:10px;justify-content:flex-end;background:#f9f9f9;flex-shrink:0}
    .btn-approve{padding:8px 24px;background:#16a34a;color:#fff;border:1px solid #166534;font-size:15px;font-weight:700;cursor:pointer;font-family:'Sarabun',Arial,sans-serif}
    .btn-approve:disabled{opacity:.5;cursor:not-allowed}
    .btn-reject{padding:8px 24px;background:#dc2626;color:#fff;border:1px solid #991b1b;font-size:15px;font-weight:700;cursor:pointer;font-family:'Sarabun',Arial,sans-serif}
    .btn-reject:disabled{opacity:.5;cursor:not-allowed}
    .btn-modal-close{padding:8px 20px;background:linear-gradient(180deg,#f0f0f0,#d8d8d8);color:#555;border:1px solid #999;font-size:15px;font-weight:600;cursor:pointer;font-family:'Sarabun',Arial,sans-serif}
    .img-thumb{width:70px;height:70px;object-fit:cover;border:1px solid #ccc;cursor:pointer;margin:2px}
    .img-thumb:hover{border-color:#2358a4}
    .confirm-overlay{position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:9000;display:none;justify-content:center;align-items:center;padding:16px}
    .confirm-overlay.active{display:flex}
    .confirm-box{background:#fff;width:100%;max-width:420px;border:2px solid #444;box-shadow:0 10px 40px rgba(0,0,0,.4);animation:mIn .2s ease}
    .confirm-header{padding:12px 16px;font-size:16px;font-weight:700;border-bottom:1px solid #eee}
    .confirm-body{padding:16px 20px;font-size:14px;color:#333;line-height:1.7}
    .confirm-footer{padding:12px 16px;display:flex;gap:8px;justify-content:flex-end;border-top:1px solid #eee;background:#f9f9f9}
    .empty-row td{text-align:center;padding:40px;color:#999;font-size:15px}
    .spin-sm{display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top:2px solid #fff;border-radius:50%;animation:spin 1s linear infinite;vertical-align:middle;margin-right:6px}
    @keyframes spin{to{transform:rotate(360deg)}}
    @media(max-width:768px){.stat-row{grid-template-columns:repeat(2,1fr)}}
    .btn-home{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:7px 18px;
  font-family:'Sarabun',sans-serif;
  font-size:14px;
  font-weight:600;
  color:#fff;
  text-decoration:none;
  cursor:pointer;
  white-space:nowrap;
  border:1px solid #8b0000;
  background:linear-gradient(180deg,#e03030 0%,#b00000 50%,#8b0000 100%);
  box-shadow:inset 0 1px 0 rgba(255,255,255,.3),1px 1px 3px rgba(0,0,0,.3);
}
.btn-home:hover{
  background:linear-gradient(180deg,#ff4444 0%,#cc0000 50%,#a00000 100%);
}
.btn-home:active{
  box-shadow:inset 1px 1px 3px rgba(0,0,0,.4);
  transform:translateY(1px);
}
  </style>
</head>
<body>

@php $q = ['create_by' => $authUser['name'] ?? '']; @endphp
<div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
<div class="sidebar" id="sidebar">
  <div class="sb-head"><img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo"><span>3E TRADING</span><button class="sb-close" onclick="closeSB()">&#10005;</button></div>
  <div class="sb-nav">
    <div class="sb-sec">เมนูหลัก</div>
    <a class="sb-item" target="_blank" href="{{ route('inventory.transaction', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รายการสินค้า เข้า-ออก</a>
    <a class="sb-item" target="_blank" href="{{ route('inventory.item', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>ค้นหาสินค้า</a>
    <div style="height:1px;background:#a8c3e0;margin:5px 12px"></div>
    <div class="sb-sec">ใบขอซื้อ</div>
    @if(str_contains($authUser['page'] ?? '', 'pr'))
      <a class="sb-item" target="_blank" href="{{ route('inventory.pr', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>สร้างใบขอซื้อ</a>
    @endif
    <a class="sb-item cur" target="_blank" href="{{ route('inventory.pr.dashboard', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>รายการขอซื้อ</a>
  </div>
</div>

<div class="topbar">
  <button class="hamburger" onclick="openSB()"><span></span><span></span><span></span></button>
  <img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo" class="topbar-logo">
  <span class="topbar-title">3E TRADING</span>
  <div class="topbar-right"><span class="topbar-name">{{ $authUser['name'] ?? '' }}</span><span class="topbar-badge">{{ strtoupper($authRole) }}</span></div>
  <a href="http://server_update:8000/solist" button  type="submit" class="btn-home">🚪 หน้าหลัก</a>
</div>

<div id="content">
  <div class="page-title">
    <span>Dashboard ใบขอซื้อ</span>
    @if($authRole === 'admin')
      <span style="background:#FCA50D;color:#000;font-size:13px;font-weight:700;padding:3px 12px;border:1px solid #c07800">ADMIN MODE</span>
    @endif
  </div>

  <div class="stat-row">
    <div class="stat-card all"><div class="num" id="cntAll">0</div><div class="lbl">ทั้งหมด</div></div>
    <div class="stat-card pend"><div class="num" id="cntPend">0</div><div class="lbl">รอดำเนินการ</div></div>
    <div class="stat-card appr"><div class="num" id="cntAppr">0</div><div class="lbl">อนุมัติแล้ว</div></div>
    <div class="stat-card rej"><div class="num" id="cntRej">0</div><div class="lbl">ไม่อนุมัติ</div></div>
  </div>

  <div class="filter-bar">
    <div class="filter-box">
      <label>ค้นหา</label>
      <input type="text" id="searchInput" class="filter-input" placeholder="PR No. / ชื่อผู้ขอ / รหัสสินค้า / ชื่อสินค้า..." oninput="applyFilter()" style="width:320px">
    </div>
    <div class="filter-box">
      <label>สถานะ</label>
      <select id="statusFilter" class="filter-input" onchange="applyFilter()">
        <option value="">ทั้งหมด</option>
        <option value="รอดำเนินการ">รอดำเนินการ</option>
        <option value="อนุมัติแล้ว">อนุมัติแล้ว</option>
        <option value="ไม่อนุมัติ">ไม่อนุมัติ</option>
      </select>
    </div>
    <button class="btn-clear-filter" onclick="clearFilter()">ล้าง</button>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>PR No.</th><th>วันที่</th><th>ผู้ขอซื้อ</th><th>ผู้ซื้อ</th><th>เบอร์โทร</th><th>PO</th>
          <th>รหัสสินค้า</th><th>รายการสินค้า</th><th>ราคารวม (฿)</th><th>สถานะ</th><th>ดำเนินการโดย</th><th></th>
        </tr>
      </thead>
      <tbody id="prTableBody"></tbody>
    </table>
  </div>

  <div class="pagination-bar" id="paginationBar" style="display:none">
    <button class="page-btn" id="btnPrev" onclick="goPage(currentPage-1)">← ก่อนหน้า</button>
    <span class="page-info" id="pageInfo">หน้า 1 / 1</span>
    <button class="page-btn" id="btnNext" onclick="goPage(currentPage+1)">ถัดไป →</button>
  </div>
</div>

<!-- DETAIL MODAL -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modalTitle">ใบขอซื้อ</h3>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="modal-section">
        <div class="modal-section-title">ข้อมูลทั่วไป</div>
        <div class="info-grid">
          <div class="info-item"><label>PR No.</label><span id="mDocNo">-</span></div>
          <div class="info-item"><label>วันที่</label><span id="mDate">-</span></div>
          <div class="info-item"><label>ผู้ขอซื้อ</label><span id="mRequester">-</span></div>
          <div class="info-item"><label>ผู้ซื้อ</label><span id="mBuyerName">-</span></div>
          <div class="info-item"><label>เบอร์โทร</label><span id="mPhone">-</span></div>
          <div class="info-item"><label>PO</label><span id="mPoNumber">-</span></div>
          <div class="info-item"><label>สถานะ</label><span id="mStatus">-</span></div>
          <div class="info-item"><label>ผู้อนุมัติ</label><span id="mActionBy">-</span></div>
          <div class="info-item"><label>วันที่อนุมัติ</label><span id="mActionDate">-</span></div>
        </div>
      </div>
      <div class="modal-section">
        <div class="modal-section-title">รายการสินค้า</div>
        <div class="table-scroll">
        <table class="modal-table">
          <thead>
            <tr>
              <th>#</th><th>รหัสสินค้า</th><th>ชื่อสินค้า</th><th>บริษัท</th>
              <th style="text-align:right">จำนวน</th><th style="text-align:right">ราคา/หน่วย</th><th>สกุล</th>
              <th style="text-align:right">รวม (฿)</th><th>รูปสินค้า</th><th>รูปเอกสาร</th>
            </tr>
          </thead>
          <tbody id="mItemsBody"></tbody>
          <tfoot>
            <tr>
              <td colspan="8" style="text-align:right;padding:6px 8px">รวมทั้งหมด</td>
              <td colspan="2" id="mTotal" style="text-align:right;padding:6px 8px;font-weight:700"></td>
            </tr>
          </tfoot>
        </table>
        </div>
      </div>
      <div class="modal-section">
        <div class="modal-section-title">เหตุผลที่ขอซื้อ</div>
        <div class="reason-box" id="mReason">-</div>
      </div>
      <div class="modal-section" id="mNoteSection" style="display:none">
        <div class="modal-section-title">หมายเหตุ</div>
        <div class="reason-box" id="mNote">-</div>
      </div>
      <div class="modal-section" id="mRejectSection" style="display:none">
        <div class="modal-section-title">เหตุผลที่ไม่อนุมัติ</div>
        <div class="reason-box" id="mRejectReason" style="background:#fff5f5;border-color:#dc2626;color:#dc2626"></div>
      </div>
    </div>
    <div class="modal-footer" id="modalFooter"></div>
  </div>
</div>

<!-- CONFIRM -->
<div class="confirm-overlay" id="confirmOverlay">
  <div class="confirm-box">
    <div class="confirm-header" id="confirmTitle">ยืนยัน</div>
    <div class="confirm-body" id="confirmBody"></div>
    <div class="confirm-footer">
      <button class="btn-modal-close" id="confirmCancelBtn" onclick="closeConfirm()">ยกเลิก</button>
      <button id="confirmOkBtn" class="btn-approve" onclick="confirmAction()">ยืนยัน</button>
    </div>
  </div>
</div>

<script>
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
const ROLE=@json($authRole);
const isAdmin=ROLE==='admin';

let allPR=[],filteredPR=[],currentPR=null,pendingAction=null;
let currentPage=1;
const ROWS_PER_PAGE=100;

const $=id=>document.getElementById(id);
function openSB(){$('sidebar').classList.add('open');$('sbOv').classList.add('open')}
function closeSB(){$('sidebar').classList.remove('open');$('sbOv').classList.remove('open')}

// ── โหลดข้อมูลจาก Laravel API ──
async function loadData(){
  try{
    const rows=await(await fetch('/api/pr')).json();
    allPR=(rows||[]).map((r,i)=>({
      _idx:i,
      pr_id:r.pr_id||"",requester:r.requester||"",buyer_name:r.buyer_name||"",
      phone:r.phone||"",po_number:r.po_number||"",date:r.date||"",
      reason:r.reason||"",note:r.note||"",
      items:Array.isArray(r.items)?r.items:[],
      status:r.status||"รอดำเนินการ",
      action_by:r.action_by||"",action_date:r.action_date||"",
      reject_reason:r.reject_reason||""
    }));
    applyFilter();updateStats();
  }catch(e){console.error('loadData:',e)}
}

function updateStats(){
  $('cntAll').textContent=allPR.length;
  $('cntPend').textContent=allPR.filter(r=>r.status==="รอดำเนินการ").length;
  $('cntAppr').textContent=allPR.filter(r=>r.status==="อนุมัติแล้ว").length;
  $('cntRej').textContent=allPR.filter(r=>r.status==="ไม่อนุมัติ").length;
}

function applyFilter(){
  const q=$('searchInput').value.trim().toLowerCase();
  const st=$('statusFilter').value;
  filteredPR=allPR.filter(r=>{
    const mSt=!st||r.status===st;
    const mQ=!q
      ||r.pr_id.toLowerCase().includes(q)
      ||r.requester.toLowerCase().includes(q)
      ||r.buyer_name.toLowerCase().includes(q)
      ||r.phone.includes(q)
      ||r.items.some(it=>(it.name||"").toLowerCase().includes(q)||(it.item_id||"").toLowerCase().includes(q));
    return mSt&&mQ;
  });
  currentPage=1;renderTable();
}
function clearFilter(){$('searchInput').value="";$('statusFilter').value="";applyFilter()}

function calcTotal(items){return items.reduce((s,it)=>s+(parseFloat(it.thb_price||it.thbPrice)||0)*(parseFloat(it.qty)||0),0)}
function badgeHtml(status){
  const cls={"รอดำเนินการ":"badge-pending","อนุมัติแล้ว":"badge-approved","ไม่อนุมัติ":"badge-rejected"}[status]||"";
  return `<span class="badge ${cls}">${status}</span>`;
}

function renderTable(){
  const tbody=$('prTableBody');tbody.innerHTML="";
  if(!filteredPR.length){
    tbody.innerHTML=`<tr class="empty-row"><td colspan="12">ไม่พบรายการ</td></tr>`;
    $('paginationBar').style.display="none";return;
  }
  const totalPages=Math.max(1,Math.ceil(filteredPR.length/ROWS_PER_PAGE));
  const start=(currentPage-1)*ROWS_PER_PAGE;
  filteredPR.slice(start,start+ROWS_PER_PAGE).forEach(r=>{
    const total=calcTotal(r.items);
    const names=r.items.map(it=>it.name).join(", ");
    const idsHtml=[...new Set(r.items.map(it=>it.item_id).filter(Boolean))].map(id=>`<span class="iditem-chip">${id}</span>`).join("");
    const tr=document.createElement('tr');
    tr.innerHTML=`
      <td style="white-space:nowrap;font-weight:600;font-size:13px">${r.pr_id}</td>
      <td style="white-space:nowrap;font-size:13px">${r.date}</td>
      <td style="font-size:13px">${r.requester}</td>
      <td style="font-size:13px">${r.buyer_name}</td>
      <td style="white-space:nowrap;font-size:13px">${r.phone}</td>
      <td style="white-space:nowrap;font-size:13px">${r.po_number||"-"}</td>
      <td>${idsHtml||"-"}</td>
      <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px" title="${names}">${names||"-"}</td>
      <td style="text-align:right;white-space:nowrap;font-size:13px">${total.toLocaleString('th-TH',{maximumFractionDigits:0})} ฿</td>
      <td>${badgeHtml(r.status)}</td>
      <td style="font-size:12px;color:#666">${r.action_by||"-"}</td>
      <td><button class="btn-view" onclick="openModal(${r._idx})">ดูรายละเอียด</button></td>
    `;
    tbody.appendChild(tr);
  });
  const bar=$('paginationBar');
  if(totalPages<=1){bar.style.display="none";return}
  bar.style.display="flex";
  $('pageInfo').textContent=`หน้า ${currentPage} / ${totalPages} (${filteredPR.length} รายการ)`;
  $('btnPrev').disabled=currentPage===1;
  $('btnNext').disabled=currentPage===totalPages;
}
function goPage(p){const t=Math.ceil(filteredPR.length/ROWS_PER_PAGE);if(p<1||p>t)return;currentPage=p;renderTable();window.scrollTo({top:0,behavior:'smooth'})}

// ── Modal ──
function openModal(idx){
  currentPR=allPR[idx];if(!currentPR)return;
  const r=currentPR;
  $('modalTitle').textContent="ใบขอซื้อ — "+r.pr_id;
  $('mDocNo').textContent=r.pr_id;
  $('mDate').textContent=r.date;
  $('mRequester').textContent=r.requester;
  $('mBuyerName').textContent=r.buyer_name;
  $('mPhone').textContent=r.phone;
  $('mPoNumber').textContent=r.po_number||"-";
  $('mStatus').innerHTML=badgeHtml(r.status);
  $('mActionBy').textContent=r.action_by||"-";
  $('mActionDate').textContent=r.action_date||"-";
  $('mReason').textContent=r.reason||"-";
  $('mNoteSection').style.display=r.note?"block":"none";
  if(r.note)$('mNote').textContent=r.note;
  $('mRejectSection').style.display=(r.status==="ไม่อนุมัติ"&&r.reject_reason)?"block":"none";
  if(r.reject_reason)$('mRejectReason').textContent=r.reject_reason;

  const tbody=$('mItemsBody');tbody.innerHTML="";
  let total=0;
  r.items.forEach((it,i)=>{
    const price=parseFloat(it.price)||0;
    const qty=parseFloat(it.qty)||0;
    const thb=parseFloat(it.thb_price||it.thbPrice)||0;
    const sub=thb*qty;
    total+=sub;

    const directUrl=driveViewUrl(it.image_url);
    const imgHtml=directUrl
      ?`<img class="img-thumb" src="${directUrl}" onclick="window.open('${it.image_url}','_blank')" title="ดูรูปขนาดเต็ม" onerror="this.src='';this.alt='โหลดไม่ได้';">`
      :"-";

    const docUrl=driveViewUrl(it.pic_doc);
    const docHtml=docUrl
      ?`<img class="img-thumb" src="${docUrl}" onclick="window.open('${it.pic_doc}','_blank')" title="ดูรูปเอกสาร" onerror="this.src='';this.alt='โหลดไม่ได้';">`
      :"-";

    tbody.innerHTML+=`
      <tr>
        <td>${i+1}</td>
        <td><span class="iditem-chip">${it.item_id||"-"}</span>${it.is_new?'<span style="background:#F59E0B;color:#000;font-size:9px;font-weight:700;padding:1px 3px;margin-left:3px">ใหม่</span>':''}</td>
        <td>${it.name}</td>
        <td>${it.company||"-"}</td>
        <td style="text-align:right">${qty}</td>
        <td style="text-align:right">${price.toLocaleString('th-TH')}</td>
        <td>${it.currency||"บาท"}</td>
        <td style="text-align:right">${sub.toLocaleString('th-TH',{maximumFractionDigits:0})}</td>
        <td>${imgHtml}</td>
        <td>${docHtml}</td>
      </tr>`;
  });
  $('mTotal').textContent=total.toLocaleString('th-TH',{maximumFractionDigits:0})+" ฿";

  const footer=$('modalFooter');
  footer.innerHTML=`<button class="btn-modal-close" onclick="closeModal()">ปิด</button>`;
  if(isAdmin&&r.status==="รอดำเนินการ"){
    footer.innerHTML=`
      <button class="btn-modal-close" onclick="closeModal()">ปิด</button>
      <button class="btn-reject" onclick="askAction('reject')">✕ ไม่อนุมัติ</button>
      <button class="btn-approve" onclick="askAction('approve')">✓ อนุมัติ</button>`;
  }
  $('modalOverlay').classList.add('active');
}
/** รูปจาก Drive → thumbnail (โหลดได้เสมอ), รูปจากที่อื่นใช้ URL ตรง */
function driveViewUrl(url){
  if(!url)return'';
  const m=url.match(/\/file\/d\/([a-zA-Z0-9_-]+)/);
  if(m)return'https://drive.google.com/thumbnail?id='+m[1]+'&sz=w200';
  return url;
}
function closeModal(){$('modalOverlay').classList.remove('active')}

// ── Confirm ──
function askAction(action){
  pendingAction=action;
  if(action==="approve"){
    $('confirmTitle').textContent="✓ ยืนยันการอนุมัติ";
    $('confirmBody').innerHTML=`อนุมัติใบขอซื้อ <b>${currentPR.pr_id}</b> ของ <b>${currentPR.requester}</b> ใช่หรือไม่?<br><small style="color:#666">ระบบจะสร้าง transaction stockin ต่อทุกรายการสินค้า</small>`;
    $('confirmOkBtn').className="btn-approve";
    $('confirmOkBtn').textContent="อนุมัติ";
  }else{
    $('confirmTitle').textContent="✕ ยืนยันการไม่อนุมัติ";
    $('confirmBody').innerHTML=`
      ไม่อนุมัติใบขอซื้อ <b>${currentPR.pr_id}</b> ของ <b>${currentPR.requester}</b><br><br>
      <label style="font-size:13px;font-weight:600;color:#555;display:block;margin-bottom:4px">เหตุผล (ถ้ามี):</label>
      <textarea id="rejectReasonInput" rows="3" style="width:100%;border:1px solid #ccc;padding:6px;font-size:14px;font-family:'Sarabun',Arial,sans-serif;resize:vertical" placeholder="กรอกเหตุผล..."></textarea>`;
    $('confirmOkBtn').className="btn-reject";
    $('confirmOkBtn').textContent="ยืนยัน ไม่อนุมัติ";
  }
  $('confirmOverlay').classList.add('active');
}
function closeConfirm(){$('confirmOverlay').classList.remove('active');pendingAction=null}

async function confirmAction(){
  if(!pendingAction||!currentPR)return;
  const okBtn=$('confirmOkBtn'),cancelBtn=$('confirmCancelBtn');
  okBtn.disabled=true;cancelBtn.disabled=true;
  okBtn.innerHTML=`<span class="spin-sm"></span>${pendingAction==="approve"?"กำลังอนุมัติ...":"กำลังดำเนินการ..."}`;

  try{
    let res;
    if(pendingAction==="approve"){
      res=await(await fetch('/api/pr/'+encodeURIComponent(currentPR.pr_id)+'/approve',{
        method:'POST',headers:{'X-CSRF-TOKEN':CSRF}
      })).json();
      okBtn.textContent="อนุมัติ";
      if(res.success){
        closeConfirm();closeModal();
        alert(`อนุมัติ ${currentPR.pr_id} เรียบร้อย\nTransaction stockin ถูกสร้างแล้ว`);
        loadData();
      }else alert("เกิดข้อผิดพลาด: "+(res.error||"unknown"));
    }else{
      const el=$('rejectReasonInput');
      const reason=el?el.value.trim():"";
      res=await(await fetch('/api/pr/'+encodeURIComponent(currentPR.pr_id)+'/reject',{
        method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body:JSON.stringify({reason})
      })).json();
      okBtn.textContent="ยืนยัน ไม่อนุมัติ";
      if(res.success){closeConfirm();closeModal();loadData()}
      else alert("เกิดข้อผิดพลาด: "+(res.error||"unknown"));
    }
  }catch(err){alert("Error: "+err.message)}
  okBtn.disabled=false;cancelBtn.disabled=false;
}

// ── Init ──
loadData();
</script>
</body>
</html>