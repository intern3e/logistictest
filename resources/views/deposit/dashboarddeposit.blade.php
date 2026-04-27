<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ข้อมูลใบมัดจำ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  /* ===== ธีม Navy ทางการ (ธนาคาร) ===== */
  --green-header:#0B2447;       /* Navy เข้ม - แถบหัวเรื่อง */
  --green-header-2:#19376D;     /* Navy 2 - ไล่เฉด */
  --green-table:#19376D;        /* หัวตาราง */
  --green-row:#EAF1FB;           /* แถวอ่อน hover */
  --green-row-alt:#F5F8FD;       /* แถวสลับ */
  --green-row-hover:#D6E4F5;     /* hover เข้มขึ้น */
  --green-line:#C5D6EC;          /* เส้นคั่น */
  --green-confirmed:#DCE7F5;     /* แถวยืนยันแล้ว */
  --green-text:#0B2447;          /* ตัวอักษรเน้น */
  --green-link:#1E40AF;          /* ลิงก์ */

  --red-btn:#9B1B1B;             /* แดงคล้ำ ทางการ */
  --red-btn-hover:#7F1717;

  --ink:#1F2937;
  --steel:#374151;
  --ash:#6B7280;
  --mist:#9CA3AF;
  --border:#D6D9DD;
  --bg:#EEF2F7;                  /* พื้นหลังฟ้า-เทาอ่อน */
  --white:#FFFFFF;

  --r4:4px;--r6:6px;--r8:8px;
}

html,body{width:100%;min-height:100vh}
body{font-family:'Sarabun','Kanit',sans-serif;font-size:14px;background:var(--bg);color:var(--ink);line-height:1.5}

.page{width:100%;padding:0;min-height:100vh}

.topbar{
  background:linear-gradient(180deg,var(--green-header) 0%,var(--green-header-2) 100%);
  padding:14px 22px;
  display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;
  border-bottom:3px solid #061B3A;
}
.topbar-brand{display:flex;align-items:center;gap:12px}
.brand-title{
  font-family:'Kanit',sans-serif;
  font-size:22px;font-weight:600;color:#fff;
  letter-spacing:-.3px;
  text-shadow:0 1px 2px rgba(0,0,0,.15);
}
.topbar-right{display:flex;align-items:center;gap:10px;flex-wrap:wrap}

.user-pill{
  display:inline-flex;align-items:center;gap:8px;
  padding:6px 14px 6px 8px;border-radius:6px;
  background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);
  font-size:13px;color:#fff;font-weight:500;
}
.user-ava{
  width:22px;height:22px;border-radius:50%;background:#fff;
  display:flex;align-items:center;justify-content:center;
  color:var(--green-header);font-size:11px;font-weight:700;
  font-family:'Kanit',sans-serif;
}

.btn-home{
  display:inline-flex;align-items:center;gap:6px;
  padding:7px 16px;border-radius:6px;
  background:var(--red-btn);color:#fff;
  font-size:13px;font-weight:500;border:none;cursor:pointer;text-decoration:none;
  transition:background .15s;font-family:inherit;
}
.btn-home:hover{background:var(--red-btn-hover)}

.filter-bar{
  background:var(--bg);padding:12px 22px;
  display:flex;align-items:center;gap:12px;flex-wrap:wrap;
}
.f-date{
  display:inline-flex;align-items:center;gap:8px;
  background:#fff;border:1px solid var(--border);
  padding:6px 12px;border-radius:4px;font-size:13px;
}
.f-date-icon{
  width:28px;height:28px;border-radius:4px;
  background:var(--green-table);color:#fff;
  display:flex;align-items:center;justify-content:center;
  font-size:11px;font-weight:700;
}
.f-date input{
  border:none;outline:none;background:transparent;
  font-size:13px;font-family:inherit;color:var(--ink);
}
.f-clear{
  margin-left:6px;width:20px;height:20px;border-radius:50%;
  background:#FEE2E2;color:#9B1B1B;border:none;cursor:pointer;
  font-size:11px;font-weight:700;display:none;align-items:center;justify-content:center;
  transition:background .15s;
}
.f-clear.show{display:flex}
.f-clear:hover{background:#FECACA}

.f-search-wrap{
  display:flex;align-items:center;gap:10px;margin-left:auto;flex-wrap:wrap;
}
.f-search{
  display:flex;align-items:center;
  background:#fff;border:1px solid var(--border);
  border-radius:4px;padding:6px 12px;
}
.f-search input{
  border:none;outline:none;background:transparent;
  font-size:13px;font-family:inherit;color:var(--ink);
  width:220px;text-align:right;direction:rtl;
}
.f-search input::placeholder{color:var(--mist)}

.filter-info{
  background:#fff;
  margin:0 22px;
  padding:8px 14px;
  border:1px solid var(--green-line);
  border-bottom:none;
  font-size:12px;color:var(--ash);
  display:none;
  align-items:center;gap:8px;flex-wrap:wrap;
}
.filter-info.show{display:flex}
.filter-info b{color:var(--green-text);font-weight:600}
.filter-info .count{
  background:var(--green-row);color:var(--green-text);
  padding:2px 10px;border-radius:12px;font-weight:600;
}

.tbl-wrap{
  background:#fff;
  margin:0 22px 22px;
  border:1px solid var(--green-line);
  overflow-x:auto;-webkit-overflow-scrolling:touch;
}
table{width:100%;border-collapse:collapse;font-size:13px}

thead tr{background:var(--green-table)}
th{
  padding:10px 12px;
  font-size:13px;font-weight:600;color:#fff;
  border-right:1px solid rgba(255,255,255,.15);
  text-align:center;white-space:nowrap;
  font-family:'Kanit',sans-serif;
}
th:last-child{border-right:none}

tbody tr{background:#fff;transition:background .1s}
tbody tr:nth-child(even){background:#fff}
tbody tr:hover{background:var(--green-row)}
tbody tr.confirmed{background:#fff}
tbody tr.hidden-row{display:none}

td{
  padding:10px 12px;
  border-bottom:1px solid var(--green-line);
  border-right:1px solid var(--green-line);
  vertical-align:middle;color:var(--ink);text-align:center;
}
td:last-child{border-right:none}
tbody tr:last-child td{border-bottom:none}

.c-link{
  color:var(--green-link);font-weight:500;
  cursor:pointer;text-decoration:none;
  white-space:nowrap;transition:color .12s;
}
.c-link:hover{color:#1E3A8A;text-decoration:underline}

.c-cust{text-align:left;max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.c-emp{text-align:center}

.c-money{font-family:'Sarabun',monospace;font-weight:500;text-align:right;white-space:nowrap}
.c-money-sub{font-family:'Sarabun',monospace;text-align:right;white-space:nowrap;color:var(--steel)}
.c-percent{font-family:'Sarabun',monospace;text-align:center;white-space:nowrap}

.type-text{color:var(--green-text);font-weight:500}
.type-text .plus{color:var(--green-link);font-weight:700;margin-left:4px}

.status-cell{text-align:center;line-height:1.35}
.status-cell .st{color:var(--green-text);font-weight:600;font-size:13px;display:block}
.status-cell .st-time{color:var(--ash);font-size:11px;display:block;margin-top:2px}
.status-cancel{color:#9B1B1B;font-weight:600}
.status-wait{color:#D97706;font-weight:600}
.status-wip{color:var(--green-link);font-weight:600}

/* ===== สถานะแบบกดได้ (admin) ===== */
.status-clickable{
  cursor:pointer;
  display:inline-block;
  padding:4px 12px;
  border-radius:14px;
  border:1.5px solid transparent;
  transition:all .15s;
  user-select:none;
}
.status-clickable:hover{
  background:var(--green-row);
  border-color:var(--green-table);
  transform:translateY(-1px);
  box-shadow:0 2px 6px rgba(25,55,109,.2);
}
.status-clickable.is-wait:hover{
  background:#FEF3C7;
  border-color:#D97706;
  box-shadow:0 2px 6px rgba(217,119,6,.2);
}
.status-clickable .pen-ico{
  display:inline-block;margin-left:5px;opacity:.5;
  font-size:10px;
}
.status-clickable:hover .pen-ico{opacity:1}

/* ===== Confirm Modal ===== */
.confirm-bg{display:none;position:fixed;inset:0;background:rgba(13,15,18,.6);z-index:2000;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(3px)}
.confirm-bg.open{display:flex}
.confirm-box{
  background:#fff;border-radius:12px;
  width:100%;max-width:420px;
  box-shadow:0 24px 60px rgba(0,0,0,.25);
  overflow:hidden;
  animation:popIn .2s ease-out;
}
@keyframes popIn{from{transform:scale(.92);opacity:0}to{transform:scale(1);opacity:1}}
.confirm-head{
  padding:20px 22px 14px;
  text-align:center;
}
.confirm-ico{
  width:56px;height:56px;border-radius:50%;
  background:#FEF3C7;color:#D97706;
  margin:0 auto 12px;
  display:flex;align-items:center;justify-content:center;
  font-size:28px;font-weight:700;
}
.confirm-ico.to-confirm{background:var(--green-row);color:var(--green-table)}
.confirm-title{
  font-size:17px;font-weight:600;color:var(--ink);
  font-family:'Kanit',sans-serif;margin-bottom:6px;
}
.confirm-msg{font-size:13px;color:var(--ash);line-height:1.5}
.confirm-msg b{color:var(--ink);font-weight:600}
.confirm-detail{
  margin:14px 22px;padding:10px 14px;
  background:var(--green-row);border-radius:6px;
  font-size:13px;color:var(--green-text);
  display:flex;justify-content:space-between;gap:8px;
}
.confirm-detail .label{color:var(--ash);font-size:11px;text-transform:uppercase;letter-spacing:.05em}
.confirm-foot{
  padding:14px 22px 18px;
  display:flex;gap:10px;
}
.confirm-foot button{
  flex:1;padding:10px;border-radius:8px;
  font-size:14px;font-weight:600;font-family:inherit;
  cursor:pointer;border:none;transition:all .15s;
}
.btn-cancel{background:#F3F4F6;color:var(--steel)}
.btn-cancel:hover{background:#E5E7EB}
.btn-confirm{background:var(--green-table);color:#fff}
.btn-confirm:hover{background:var(--green-header)}
.btn-confirm.to-wait{background:#D97706}
.btn-confirm.to-wait:hover{background:#B45309}
.btn-confirm:disabled{opacity:.6;cursor:not-allowed}

/* Toast แจ้งผล */
.toast{
  position:fixed;bottom:24px;left:50%;
  transform:translateX(-50%) translateY(100px);
  background:var(--green-header);color:#fff;
  padding:12px 22px;border-radius:8px;
  font-size:13px;font-weight:500;
  box-shadow:0 8px 24px rgba(0,0,0,.2);
  z-index:3000;opacity:0;
  transition:all .25s ease-out;
  display:flex;align-items:center;gap:8px;
}
.toast.show{transform:translateX(-50%) translateY(0);opacity:1}
.toast.error{background:#9B1B1B}

.empty,.no-match{padding:60px 20px;text-align:center;background:#fff}
.empty-ico{
  width:52px;height:52px;border-radius:8px;
  background:var(--green-row);margin:0 auto 14px;
  display:flex;align-items:center;justify-content:center;
}
.empty h4,.no-match h4{font-size:15px;font-weight:600;margin-bottom:5px;font-family:'Kanit',sans-serif;color:var(--green-text)}
.empty p,.no-match p{font-size:12px;color:var(--ash)}
.no-match-row{display:none}
.no-match-row.show{display:table-row}

.pg-bar{
  display:flex;align-items:center;justify-content:space-between;
  padding:11px 18px;border-top:1px solid var(--green-line);
  flex-wrap:wrap;gap:8px;background:#fff;
}
.pg-info{font-size:12px;color:var(--ash)}

nav[role="navigation"]>div{display:flex!important;align-items:center!important;justify-content:center!important;gap:4px;flex-wrap:nowrap!important}
nav[role="navigation"]>div>p{display:none!important}
nav[role="navigation"] .sm\:hidden{display:none!important}
nav[role="navigation"] a[rel="prev"] span,nav[role="navigation"] a[rel="next"] span{display:none!important}
nav[role="navigation"] a[rel="prev"],nav[role="navigation"] a[rel="next"]{
  width:30px;height:30px;border-radius:4px;
  background:#fff;border:1px solid var(--border);
  display:flex;align-items:center;justify-content:center;color:var(--ash);transition:all .12s;
}
nav[role="navigation"] a[rel="prev"]:hover,nav[role="navigation"] a[rel="next"]:hover{
  border-color:var(--green-table);color:var(--green-table);background:var(--green-row);
}
nav[role="navigation"] svg{width:13px!important;height:13px!important}
nav[role="navigation"] a.relative,nav[role="navigation"] span.relative{
  min-width:30px;height:30px;display:flex;align-items:center;justify-content:center;
  font-size:13px;font-weight:500;color:var(--green-link);
  border-radius:4px;border:1px solid transparent;transition:all .12s;
}
nav[role="navigation"] a.relative:hover{background:var(--green-row);border-color:var(--green-line)}
nav[role="navigation"] span[aria-current="page"]{
  background:var(--green-table)!important;color:#fff!important;border-color:var(--green-table)!important;
}

.modal-bg{display:none;position:fixed;inset:0;background:rgba(13,15,18,.55);z-index:1000;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(2px)}
.modal-bg.open{display:flex}
.modal{
  background:#fff;border-radius:8px;
  width:100%;max-width:680px;max-height:90vh;overflow-y:auto;
  border:1px solid var(--border);box-shadow:0 20px 60px rgba(0,0,0,.18);
}
.modal-head{
  padding:14px 22px;
  background:var(--green-header);color:#fff;
  display:flex;align-items:center;justify-content:space-between;
  position:sticky;top:0;z-index:1;
}
.modal-head h3{font-size:15px;font-weight:600;font-family:'Kanit',sans-serif}
.modal-x{
  width:28px;height:28px;border-radius:4px;
  border:1px solid rgba(255,255,255,.25);background:transparent;
  cursor:pointer;display:flex;align-items:center;justify-content:center;
  color:#fff;transition:all .12s;flex-shrink:0;
}
.modal-x:hover{background:rgba(255,255,255,.15)}
.modal-body{padding:18px 22px}
.sec-ttl{
  font-size:11px;font-weight:700;color:var(--green-text);
  letter-spacing:.06em;text-transform:uppercase;
  margin-bottom:10px;padding-bottom:7px;
  border-bottom:2px solid var(--green-table);
}
.dg{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px}
.df{display:flex;flex-direction:column;gap:3px}
.df.full{grid-column:1/-1}
.dl{font-size:10px;font-weight:700;color:var(--ash);letter-spacing:.06em;text-transform:uppercase}
.dv{font-size:13px;color:var(--ink);font-weight:500}
.modal-foot{padding:12px 22px;border-top:1px solid var(--green-line);display:flex;justify-content:flex-end}
.btn-ghost{
  padding:7px 16px;border-radius:6px;
  border:1px solid var(--border);background:transparent;
  font-size:13px;font-weight:500;color:var(--steel);
  cursor:pointer;font-family:inherit;transition:all .15s;
}
.btn-ghost:hover{border-color:var(--green-table);color:var(--green-table);background:var(--green-row)}

@media(max-width:768px){
  .filter-bar,.tbl-wrap,.filter-info{margin-left:10px;margin-right:10px}
  .tbl-wrap{margin:0 10px 10px}
  .f-search input{width:160px}
  th,td{padding:8px 8px;font-size:12px}
  .brand-title{font-size:18px}
}
</style>
</head>
<body>

@php
  $deposits = $deposits ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
  $totalCount = $deposits->total();

  $typeLabel = [
    'product'  => 'สินค้า',
    'service'  => 'บริการ',
    'shipping' => 'ขนส่ง',
  ];

  // ===== ตรวจสอบสิทธิ์ admin (kanitin2, dev) =====
  $currentUser = strtolower(request()->get('create_by', ''));
  $adminUsers  = ['kanitin2', 'dev'];
  $isAdmin     = in_array($currentUser, $adminUsers);
@endphp

<div class="page">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-brand">
      <div class="brand-title">ข้อมูลใบมัดจำ</div>
    </div>
    <div class="topbar-right">
      <div class="user-pill">
        @php $cb = trim(request()->get('create_by', '')); @endphp
        @if($cb !== '')
          <div class="user-ava">{{ strtoupper(substr($cb, 0, 2)) }}</div>
          {{ $cb }}
        @else
          <span style="font-size:14px">👤</span>
          <span>ผู้ใช้:</span>
        @endif
      </div>
      <a href="/solist" class="btn-home">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
          <path d="M2 6.5L6.5 2 11 6.5V11.5H8V8.5H5V11.5H2V6.5Z" stroke="white" stroke-width="1.4" stroke-linejoin="round"/>
        </svg>
        หน้าหลัก
      </a>
    </div>
  </div>

  <!-- FILTER -->
  <div class="filter-bar">
    <div class="f-date">
      <div class="f-date-icon">17</div>
      <span style="color:var(--ash);font-size:12px">วันที่ : เดือน / วัน / ปี</span>
      <input type="date" id="filterDate" value="{{ date('Y-m-d') }}" oninput="applyFilter()">
      <button type="button" class="f-clear" id="dateClear" onclick="clearDate()" title="ล้างวันที่">✕</button>
    </div>

    <div class="f-search-wrap">
      <div class="f-search">
        <input type="text" id="filterSO" placeholder="ค้นหา ใบสั่งขาย" oninput="applyFilter()">
      </div>
    </div>
  </div>

  <!-- ข้อความแสดงสถานะการกรอง -->
  <div class="filter-info" id="filterInfo">
    <span>กำลังกรอง:</span>
    <span id="filterDateText"></span>
    <span id="filterSOText"></span>
    <span class="count" id="filterCount">0 รายการ</span>
    <button type="button" onclick="clearAll()" style="background:none;border:none;color:var(--red-btn);cursor:pointer;font-size:12px;text-decoration:underline;font-family:inherit">ล้างทั้งหมด</button>
  </div>

  <!-- TABLE -->
  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:50px">ลำดับ</th>
          <th>ใบสั่งขาย</th>
          <th>ชื่อลูกค้า</th>
          <th>Sale</th>
          <th>วันที่</th>
          <th>ผู้เปิดบิล</th>
          <th>เวลาบันทึก</th>
          <th>ประเภท</th>
          <th>%</th>
          <th>ยอดมัดจำ</th>
          <th>ยอดคงเหลือ</th>
          <th>สถานะ</th>
        </tr>
      </thead>
      <tbody id="tableBody">
        @forelse($deposits as $item)
        @php
          $status = $item->status ?? 'รอยืนยัน';
          $isConfirmed = in_array($status, ['ยืนยันแล้ว', 'สำเร็จ', 'ออกบิลแล้ว', 'ปรับสำเร็จ']);
          $isCancelled = $status === 'ยกเลิก';
          $depType = $item->dep_type ?? '';
          $typeName = $typeLabel[$depType] ?? $depType;
          $recordDate = $item->time ? \Carbon\Carbon::parse($item->time)->setTimezone('Asia/Bangkok')->format('Y-m-d') : '';
        @endphp
        <tr class="{{ $isConfirmed ? 'confirmed' : '' }}"
            data-record-date="{{ $recordDate }}"
            data-so="{{ strtolower($item->so_id ?? '') }}">
          <td class="row-idx">{{ ($deposits->currentPage() - 1) * $deposits->perPage() + $loop->iteration }}</td>
          <td>
            <a class="c-link" onclick="openDetail('{{ $item->so_id }}')">{{ $item->so_id }}</a>
          </td>
          <td class="c-cust" title="{{ $item->customer_name }}">{{ $item->customer_name ?? '—' }}</td>
          <td class="c-emp" title="{{ $item->sale_name }}">{{ $item->sale_name ?? '—' }}</td>
          <td>{{ $item->date_dep ? \Carbon\Carbon::parse($item->date_dep)->format('d/m/Y') : '—' }}</td>
          <td class="c-emp">{{ $item->emp_name ?? '—' }}</td>
          <td style="white-space:nowrap">{{ $item->time ? \Carbon\Carbon::parse($item->time)->setTimezone('Asia/Bangkok')->format('H:i d/m/Y') : '—' }}</td>
          <td>
            @if($depType)
              <span class="type-text">{{ $typeName }}<span class="plus">+</span></span>
            @else
              —
            @endif
          </td>
          <td class="c-percent">{{ number_format((float)($item->dep_per ?? 0), 2) }}%</td>
          <td class="c-money">{{ number_format((float)($item->dep_price ?? 0), 2) }}</td>
          <td class="c-money-sub">{{ number_format((float)($item->grand_total ?? 0), 2) }}</td>
          <td class="status-cell">
            @if($isCancelled)
              <span class="st status-cancel">ยกเลิก</span>
            @elseif($isAdmin && ($isConfirmed || $status === 'รอยืนยัน'))
              {{-- Admin คลิกได้ --}}
              @php
                $nextStatus = $isConfirmed ? 'รอยืนยัน' : 'ยืนยัน';
                $waitClass  = $status === 'รอยืนยัน' ? 'is-wait' : '';
              @endphp
              <span class="st status-clickable {{ $waitClass }}"
                    style="{{ $isConfirmed ? '' : 'color:#D97706' }}"
                    onclick="askChangeStatus(
                      '{{ $item->so_id }}',
                      '{{ $item->id ?? '' }}',
                      '{{ $status }}',
                      '{{ $nextStatus }}',
                      '{{ addslashes($item->customer_name ?? '') }}'
                    )"
                    title="คลิกเพื่อเปลี่ยนสถานะ">
                {{ $status }}
                <span class="pen-ico">✎</span>
              </span>
            @elseif($isConfirmed)
              <span class="st">{{ $status }}</span>
            @elseif($status === 'รอยืนยัน')
              <span class="st status-wait">รอยืนยัน</span>
            @else
              <span class="st status-wip">{{ $status }}</span>
            @endif
            <span class="st-time">{{ $item->time ? \Carbon\Carbon::parse($item->time)->setTimezone('Asia/Bangkok')->format('H:i d/m/Y') : '' }}</span>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="12" style="background:#fff;border-right:none">
            <div class="empty">
              <div class="empty-ico">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
                  <rect x="3" y="2" width="16" height="18" rx="2.5" stroke="#0B2447" stroke-width="1.5"/>
                  <path d="M7 8h8M7 11h8M7 14h5" stroke="#0B2447" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
              </div>
              <h4>ไม่พบข้อมูล</h4>
              <p>ยังไม่มีรายการใบมัดจำในระบบ</p>
            </div>
          </td>
        </tr>
        @endforelse

        <!-- แถวแสดงเมื่อกรองแล้วไม่พบ -->
        <tr class="no-match-row" id="noMatchRow">
          <td colspan="12" style="background:#fff;border-right:none">
            <div class="no-match">
              <div class="empty-ico">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
                  <circle cx="10" cy="10" r="6" stroke="#0B2447" stroke-width="1.5"/>
                  <path d="M14.5 14.5l4 4" stroke="#0B2447" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
              </div>
              <h4>ไม่พบรายการที่ตรงกับเงื่อนไข</h4>
              <p>ลองเปลี่ยนวันที่ หรือ เลขใบสั่งขายดูใหม่</p>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    @if($deposits->total() > 0)
    <div class="pg-bar">
      <span class="pg-info">แสดง {{ $deposits->firstItem() ?? 0 }}–{{ $deposits->lastItem() ?? 0 }} จาก {{ $deposits->total() }} รายการ</span>
      <div>{{ $deposits->appends(['create_by' => request('create_by')])->links() }}</div>
    </div>
    @endif
  </div>

</div>

<!-- MODAL -->
<div class="modal-bg" id="detailModal">
  <div class="modal">
    <div class="modal-head">
      <h3 id="modal-title">รายละเอียดใบมัดจำ</h3>
      <button class="modal-x" onclick="closeDetail()">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
          <path d="M2 2l8 8M10 2L2 10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="sec-ttl">ข้อมูลทั่วไป</div>
      <div class="dg">
        <div class="df"><span class="dl">ใบสั่งขาย</span><span class="dv" id="d-so">—</span></div>
        <div class="df"><span class="dl">วันที่</span><span class="dv" id="d-date">—</span></div>
        <div class="df full"><span class="dl">ชื่อลูกค้า</span><span class="dv" id="d-cust">—</span></div>
        <div class="df"><span class="dl">ผู้ติดต่อ</span><span class="dv" id="d-contact">—</span></div>
        <div class="df"><span class="dl">เบอร์โทร</span><span class="dv" id="d-tel">—</span></div>
        <div class="df full"><span class="dl">ที่อยู่</span><span class="dv" id="d-addr">—</span></div>
        <div class="df"><span class="dl">ผู้เปิดบิล</span><span class="dv" id="d-emp">—</span></div>
        <div class="df"><span class="dl">พนักงานขาย</span><span class="dv" id="d-sale">—</span></div>
      </div>

      <div class="sec-ttl">รายการมัดจำทั้งหมดของใบสั่งขายนี้</div>
      <div class="tbl-wrap" style="margin:0 0 16px">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>ประเภท</th>
              <th>%</th>
              <th>ยอดมัดจำ</th>
              <th>สถานะ</th>
            </tr>
          </thead>
          <tbody id="d-items">
            <tr><td colspan="5" style="background:#fff;color:var(--mist);padding:20px">กำลังโหลด...</td></tr>
          </tbody>
        </table>
      </div>

      <div class="sec-ttl">สรุปยอด</div>
      <div class="dg">
        <div class="df"><span class="dl">รวมยอดมัดจำ</span><span class="dv" id="d-total-dep" style="color:var(--green-text);font-weight:700;font-size:15px">—</span></div>
        <div class="df"><span class="dl">ยอดคงเหลือ</span><span class="dv" id="d-grand" style="color:var(--ink);font-weight:700;font-size:15px">—</span></div>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn-ghost" onclick="closeDetail()">ปิด</button>
    </div>
  </div>
</div>

<!-- ===== Confirm Modal: เปลี่ยนสถานะ ===== -->
<div class="confirm-bg" id="confirmModal">
  <div class="confirm-box">
    <div class="confirm-head">
      <div class="confirm-ico" id="confirmIco">!</div>
      <div class="confirm-title" id="confirmTitle">ยืนยันการเปลี่ยนสถานะ</div>
      <div class="confirm-msg" id="confirmMsg">—</div>
    </div>
    <div class="confirm-detail" id="confirmDetail">
      <div>
        <div class="label">ใบสั่งขาย</div>
        <div id="confirmSO" style="font-weight:600">—</div>
      </div>
      <div style="text-align:right">
        <div class="label">ลูกค้า</div>
        <div id="confirmCust" style="font-weight:500">—</div>
      </div>
    </div>
    <div class="confirm-foot">
      <button class="btn-cancel" onclick="closeConfirm()">ยกเลิก</button>
      <button class="btn-confirm" id="confirmBtn" onclick="doChangeStatus()">ยืนยัน</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">
  <span id="toastMsg">—</span>
</div>

<script>
/* ===== ข้อมูล user/admin ส่งจาก server ===== */
const IS_ADMIN     = {{ $isAdmin ? 'true' : 'false' }};
const CURRENT_USER = '{{ $currentUser }}';
const CSRF_TOKEN   = document.querySelector('meta[name="csrf-token"]')?.content || '';
/* ===== Client-side Filter ===== */
function formatDateThai(ymd){
  if(!ymd) return '';
  const [y,m,d] = ymd.split('-');
  return `${d}/${m}/${y}`;
}

function applyFilter(){
  const dateVal = document.getElementById('filterDate').value;
  const soVal   = document.getElementById('filterSO').value.trim().toLowerCase();

  document.getElementById('dateClear').classList.toggle('show', !!dateVal);

  const rows = document.querySelectorAll('#tableBody tr[data-record-date]');
  let visibleCount = 0;
  let runningIdx = 0;

  rows.forEach(r => {
    const recDate = r.getAttribute('data-record-date');
    const soId    = r.getAttribute('data-so');

    let show = true;
    if(dateVal && recDate !== dateVal) show = false;
    if(soVal && !soId.includes(soVal)) show = false;

    r.classList.toggle('hidden-row', !show);
    if(show){
      visibleCount++;
      runningIdx++;
      const idxCell = r.querySelector('.row-idx');
      if(idxCell) idxCell.textContent = runningIdx;
    }
  });

  const info  = document.getElementById('filterInfo');
  const dText = document.getElementById('filterDateText');
  const sText = document.getElementById('filterSOText');
  const cText = document.getElementById('filterCount');

  if(dateVal || soVal){
    info.classList.add('show');
    dText.innerHTML = dateVal ? `<b>วันที่บันทึก:</b> ${formatDateThai(dateVal)}` : '';
    sText.innerHTML = soVal   ? `<b>ใบสั่งขาย:</b> ${soVal}` : '';
    cText.textContent = `${visibleCount} รายการ`;
  } else {
    info.classList.remove('show');
  }

  const noMatch = document.getElementById('noMatchRow');
  if(rows.length > 0 && visibleCount === 0 && (dateVal || soVal)){
    noMatch.classList.add('show');
  } else {
    noMatch.classList.remove('show');
  }
}

function clearDate(){
  document.getElementById('filterDate').value = '';
  applyFilter();
}

function clearAll(){
  document.getElementById('filterDate').value = '';
  document.getElementById('filterSO').value = '';
  applyFilter();
}

document.addEventListener('DOMContentLoaded', applyFilter);

/* ===== Modal ===== */
const typeLabels = {product:'สินค้า', service:'บริการ', shipping:'ขนส่ง'};

function openDetail(soId){
  document.getElementById('modal-title').textContent='รายละเอียด '+soId;

  ['d-so','d-date','d-cust','d-contact','d-tel','d-addr','d-emp','d-sale','d-total-dep','d-grand']
    .forEach(id=>document.getElementById(id).textContent='—');

  const tb=document.getElementById('d-items');
  tb.innerHTML='<tr><td colspan="5" style="background:#fff;color:var(--mist);padding:16px">กำลังโหลด...</td></tr>';

  document.getElementById('detailModal').classList.add('open');
  document.body.style.overflow='hidden';

  fetch(`/deposit/detail/${encodeURIComponent(soId)}`,{headers:{'X-Requested-With':'XMLHttpRequest'}})
    .then(r=>r.json())
    .then(data=>{
      if(!data || !data.items || data.items.length===0){
        tb.innerHTML='<tr><td colspan="5" style="background:#fff;color:var(--mist);padding:16px">ไม่มีข้อมูล</td></tr>';
        return;
      }
      const first = data.items[0];
      document.getElementById('d-so').textContent      = first.so_id || '—';
      document.getElementById('d-date').textContent    = first.date_dep ? formatDate(first.date_dep) : '—';
      document.getElementById('d-cust').textContent    = first.customer_name || '—';
      document.getElementById('d-contact').textContent = first.contactso || '—';
      document.getElementById('d-tel').textContent     = first.customer_tel || '—';
      document.getElementById('d-addr').textContent    = first.customer_address || '—';
      document.getElementById('d-emp').textContent     = first.emp_name || '—';
      document.getElementById('d-sale').textContent    = first.sale_name || '—';

      let totalDep = 0;
      tb.innerHTML = data.items.map(it=>{
        totalDep += parseFloat(it.dep_price||0);
        const tLabel = typeLabels[it.dep_type] || it.dep_type;
        const st = it.status || 'รอยืนยัน';
        let stCls = 'status-wait';
        if(st==='ยกเลิก') stCls='status-cancel';
        else if(['ยืนยันแล้ว','สำเร็จ','ออกบิลแล้ว','ปรับสำเร็จ'].includes(st)) stCls='';
        return `<tr>
          <td>${it.id}</td>
          <td><span class="type-text">${tLabel}</span></td>
          <td class="c-percent">${parseFloat(it.dep_per||0).toFixed(2)}%</td>
          <td class="c-money">${parseFloat(it.dep_price||0).toLocaleString('th-TH',{minimumFractionDigits:2})}</td>
          <td><span class="${stCls}" style="font-weight:600;color:${stCls?'':'var(--green-text)'}">${st}</span></td>
        </tr>`;
      }).join('');

      document.getElementById('d-total-dep').textContent = totalDep.toLocaleString('th-TH',{minimumFractionDigits:2}) + ' บาท';
      document.getElementById('d-grand').textContent     = parseFloat(first.grand_total||0).toLocaleString('th-TH',{minimumFractionDigits:2}) + ' บาท';
    })
    .catch(()=>{tb.innerHTML='<tr><td colspan="5" style="background:#fff;color:#9B1B1B;padding:16px">เกิดข้อผิดพลาด</td></tr>';});
}

function formatDate(d){
  const dt = new Date(d);
  if(isNaN(dt)) return d;
  return String(dt.getDate()).padStart(2,'0')+'/'+String(dt.getMonth()+1).padStart(2,'0')+'/'+dt.getFullYear();
}

function closeDetail(){document.getElementById('detailModal').classList.remove('open');document.body.style.overflow='';}
document.getElementById('detailModal').addEventListener('click',function(e){if(e.target===this)closeDetail();});
document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeDetail();closeConfirm();}});

/* ===== เปลี่ยนสถานะ (Admin เท่านั้น) ===== */
let pendingChange = null;

function askChangeStatus(soId, depId, currentStatus, nextStatus, custName){
  if(!IS_ADMIN){
    showToast('คุณไม่มีสิทธิ์เปลี่ยนสถานะ', true);
    return;
  }

  pendingChange = { soId, depId, currentStatus, nextStatus, custName };

  const ico   = document.getElementById('confirmIco');
  const btn   = document.getElementById('confirmBtn');
  const title = document.getElementById('confirmTitle');
  const msg   = document.getElementById('confirmMsg');

  if(nextStatus === 'ยืนยัน'){
    ico.textContent = '✓';
    ico.classList.add('to-confirm');
    btn.classList.remove('to-wait');
    btn.textContent = 'ยืนยัน';
    title.textContent = 'ยืนยันรายการนี้?';
    msg.innerHTML = `เปลี่ยนสถานะจาก <b>รอยืนยัน</b> → <b style="color:var(--green-table)">ยืนยัน</b>`;
  } else {
    ico.textContent = '!';
    ico.classList.remove('to-confirm');
    btn.classList.add('to-wait');
    btn.textContent = 'เปลี่ยนกลับ';
    title.textContent = 'เปลี่ยนกลับเป็นรอยืนยัน?';
    msg.innerHTML = `เปลี่ยนสถานะจาก <b>${currentStatus}</b> → <b style="color:#D97706">รอยืนยัน</b>`;
  }

  document.getElementById('confirmSO').textContent   = soId || '—';
  document.getElementById('confirmCust').textContent = (custName || '—').length > 24
    ? (custName.substring(0,22) + '...') : (custName || '—');

  document.getElementById('confirmModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeConfirm(){
  document.getElementById('confirmModal').classList.remove('open');
  document.body.style.overflow = '';
  pendingChange = null;
  document.getElementById('confirmBtn').disabled = false;
}

async function doChangeStatus(){
  if(!pendingChange) return;

  const btn = document.getElementById('confirmBtn');
  btn.disabled = true;
  btn.textContent = 'กำลังบันทึก...';

  try {
    const res = await fetch('/deposit/update-status', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        so_id:      pendingChange.soId,
        deposit_id: pendingChange.depId,
        new_status: pendingChange.nextStatus,
        changed_by: CURRENT_USER
      })
    });

    if(!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json().catch(()=>({success:true}));

    if(data.success === false){
      throw new Error(data.message || 'บันทึกไม่สำเร็จ');
    }

    showToast(`เปลี่ยนสถานะเป็น "${pendingChange.nextStatus}" สำเร็จ`);
    closeConfirm();

    setTimeout(()=>location.reload(), 600);

  } catch(err) {
    console.error(err);
    showToast('เกิดข้อผิดพลาด: ' + err.message, true);
    btn.disabled = false;
    btn.textContent = pendingChange.nextStatus === 'ยืนยัน' ? 'ยืนยัน' : 'เปลี่ยนกลับ';
  }
}

function showToast(msg, isError){
  const t = document.getElementById('toast');
  document.getElementById('toastMsg').textContent = msg;
  t.classList.toggle('error', !!isError);
  t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'), 2800);
}

document.getElementById('confirmModal').addEventListener('click', function(e){
  if(e.target === this) closeConfirm();
});
</script>
</body>
</html>