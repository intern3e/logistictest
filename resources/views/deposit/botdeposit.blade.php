<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ใบมัดจำ — Bot Queue</title>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --hd:#1F2937;--hd2:#374151;--tbl:#334155;
  --rh:#F1F5F9;--line:#CBD5E1;--txt:#1E293B;--lnk:#1E40AF;
  --red:#991B1B;--red-h:#7F1D1D;
  --ink:#0F172A;--steel:#334155;--ash:#64748B;--mist:#94A3B8;
  --border:#CBD5E1;--bg:#F1F5F9;
  --emerald:#047857;--emerald-s:#D1FAE5;
  --purple:#6D28D9;--purple-s:#EDE9FE;
}
body{font-family:'Sarabun','Kanit',sans-serif;font-size:14px;background:var(--bg);color:var(--ink);line-height:1.55}
.topbar{background:linear-gradient(180deg,var(--hd),var(--hd2));padding:14px 22px;display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;border-bottom:3px solid #0F172A}
.brand{font-family:'Kanit';font-size:22px;font-weight:600;color:#fff}
.brand-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;background:rgba(255,255,255,.18);font-size:11px;color:#fff;font-weight:600;margin-left:8px}
.topbar-r{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.pill{display:inline-flex;align-items:center;gap:6px;padding:5px 12px 5px 7px;border-radius:6px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.22);font-size:12px;color:#fff;font-weight:500}
.ava{width:20px;height:20px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;color:var(--hd);font-size:10px;font-weight:700;font-family:'Kanit'}
.btn-nav{display:inline-flex;align-items:center;gap:5px;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:500;cursor:pointer;text-decoration:none;font-family:inherit;border:none;transition:background .12s}
.btn-back{background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.22)}
.btn-back:hover{background:rgba(255,255,255,.2)}
.btn-home{background:var(--red);color:#fff}
.btn-home:hover{background:var(--red-h)}

/* Section titles */
.sec-header{padding:14px 22px 8px;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.sec-title{font-family:'Kanit';font-size:16px;font-weight:600;color:var(--txt);display:flex;align-items:center;gap:8px}
.sec-count{background:var(--tbl);color:#fff;padding:2px 10px;border-radius:12px;font-size:11px;font-weight:700}
.sec-count.wht{background:var(--purple)}
.sec-desc{font-size:11px;color:var(--ash);margin-left:auto}

/* Action bar */
.abar{padding:4px 22px 12px;display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.btn-print{display:inline-flex;align-items:center;gap:5px;padding:6px 14px;border-radius:6px;background:var(--tbl);color:#fff;font-size:12px;font-weight:500;cursor:pointer;border:none;font-family:inherit;transition:background .12s}
.btn-print:hover{background:var(--hd2)}
.chk-all-label{display:flex;align-items:center;gap:6px;font-size:12px;color:var(--steel);cursor:pointer;padding:5px 10px;background:#fff;border:1px solid var(--border);border-radius:4px}
.f-search{display:flex;align-items:center;gap:6px;padding:5px 10px;border:1px solid var(--border);border-radius:4px;background:#fff;margin-left:auto}
.f-search input{border:none;background:none;font-size:12px;font-family:inherit;color:var(--ink);outline:none;width:180px}
.f-search input::placeholder{color:var(--mist)}

/* Tables */
.tw{background:#fff;margin:0 22px 22px;border:1px solid var(--line);overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
thead tr{background:var(--tbl)}
/* ✅ Bot จับ: table ที่ 2 (WHT) มี thead สีม่วง */
thead tr.wht-head{background:var(--purple)}
th{padding:10px 12px;font-size:12px;font-weight:600;color:#fff;border-right:1px solid rgba(255,255,255,.12);text-align:center;white-space:nowrap;font-family:'Kanit'}
th:last-child{border-right:none}
tbody tr{background:#fff;transition:background .08s}
tbody tr:hover{background:var(--rh)}
tbody tr.processed{background:#EFF6FF}
td{padding:10px 12px;border-bottom:1px solid var(--line);border-right:1px solid var(--line);vertical-align:middle;text-align:center}
td:last-child{border-right:none}
tbody tr:last-child td{border-bottom:none}
.chk{width:16px;height:16px;cursor:pointer;accent-color:var(--tbl)}
.c-code{font-family:'Sarabun',monospace;font-size:12px;font-weight:600;color:var(--txt)}
.c-sm{font-size:12px;color:var(--ash)}
.c-money{font-family:'Sarabun',monospace;font-weight:600;color:var(--txt);white-space:nowrap;text-align:right}
.c-percent{font-family:'Sarabun',monospace;font-weight:500;white-space:nowrap}
.btn-copy{display:inline-flex;align-items:center;padding:3px 8px;border-radius:3px;border:1px solid var(--border);background:#F8FAFC;color:var(--ash);cursor:pointer;font-family:inherit;font-size:10px;font-weight:600;white-space:nowrap}
.btn-copy:hover{border-color:var(--tbl);background:var(--rh);color:var(--txt)}
.btn-copy.copied{border-color:var(--emerald);background:var(--emerald-s);color:var(--emerald)}

.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap}
.b-product{background:#E0F2F1;color:#00695C}
.b-service{background:#E8EAF6;color:#283593}
.b-transport{background:#FEF3C7;color:#92400E}
.b-default{background:var(--rh);color:var(--ash)}
/* ✅ Bot จับ: badge WHT สีม่วง */
.b-wht{background:var(--purple-s);color:var(--purple);font-weight:700;border:1px solid #C4B5FD}

.processed-badge{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:12px;background:#DBEAFE;color:#1E40AF;font-size:11px;font-weight:600}
.file-input{cursor:pointer;background:#fff}
.file-input::-webkit-file-upload-button{background:var(--tbl);color:#fff;border:none;padding:3px 8px;border-radius:3px;font-size:10px;cursor:pointer;margin-right:6px;font-family:inherit}
.file-input:disabled{opacity:.5;cursor:wait}

.slip-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:12px;background:#FFF7ED;color:#C2410C;border:1px solid #FDBA74;font-size:10.5px;font-weight:600;white-space:nowrap}
.no-slip{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:12px;background:var(--rh);color:var(--mist);border:1px solid #E2E8F0;font-size:10.5px;font-weight:500;white-space:nowrap}

.empty{padding:40px 20px;text-align:center;background:#fff}
.empty h4{font-size:15px;font-weight:600;font-family:'Kanit';color:var(--txt);margin-bottom:4px}
.empty p{font-size:12px;color:var(--ash)}

.pgb{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-top:1px solid var(--line);flex-wrap:wrap;gap:6px;background:#fff}
.pgi{font-size:11px;color:var(--ash)}
nav[role="navigation"]>div{display:flex!important;align-items:center!important;gap:3px;flex-wrap:wrap!important}
nav[role="navigation"]>div>p{display:none!important}
nav[role="navigation"] .sm\:hidden{display:none!important}
nav[role="navigation"] a[rel="prev"] span,nav[role="navigation"] a[rel="next"] span{display:none!important}
nav[role="navigation"] a[rel="prev"],nav[role="navigation"] a[rel="next"]{width:28px;height:28px;border-radius:4px;background:#fff;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--ash)}
nav[role="navigation"] svg{width:12px!important;height:12px!important}
nav[role="navigation"] a.relative,nav[role="navigation"] span.relative{min-width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:500;color:var(--lnk);border-radius:4px;border:1px solid transparent}
nav[role="navigation"] span[aria-current="page"]{background:var(--tbl)!important;color:#fff!important;border-color:var(--tbl)!important}

.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(100px);background:var(--hd);color:#fff;padding:12px 22px;border-radius:8px;font-size:13px;font-weight:500;box-shadow:0 8px 24px rgba(0,0,0,.2);z-index:3000;opacity:0;transition:all .25s}
.toast.show{transform:translateX(-50%) translateY(0);opacity:1}
.toast.error{background:#C62828}

@media(max-width:768px){
  .sec-header,.abar,.tw{margin-left:10px;margin-right:10px}
  .tw{margin-bottom:10px}
  th,td{padding:8px;font-size:12px}
  .brand{font-size:18px}.brand-badge{display:none}
}
</style>
</head>
<body>
@php
  // ✅ แยก 2 กลุ่มตาม flow จริง:
  //   งานใหม่: status = ยืนยัน + ยังไม่ผ่าน bot (status_bill ว่าง)
  //   งาน WHT: status = มี WHT → bot เคยทำแล้ว admin มาใส่ WHT ทีหลัง
  $newJobs = $deposits->filter(fn($d) => ($d->status ?? '') === 'ยืนยัน' && empty($d->status_bill));
  $whtJobs = $deposits->filter(fn($d) => ($d->status ?? '') === 'มี WHT');

  $tl = ['product'=>'สินค้า','service'=>'บริการ','shipping'=>'ขนส่ง'];
  $tm = ['product'=>'b-product','service'=>'b-service','shipping'=>'b-transport'];
  $cb = trim(request()->get('create_by',''));
@endphp

<div class="topbar">
  <div class="brand">ใบมัดจำ — Bot Queue
    <span class="brand-badge">✓ BOT</span>
  </div>
  <div class="topbar-r">
    <div class="pill">
      @if($cb!=='')
        <div class="ava">{{ strtoupper(substr($cb,0,2)) }}</div>{{ $cb }}
      @else
        <span style="font-size:13px">👤</span><span>ผู้ใช้</span>
      @endif
    </div>
    <a href="{{ route('deposit.dashboard') }}{{ $cb!==''?'?create_by='.$cb:'' }}" class="btn-nav btn-back">← ใบมัดจำ</a>
    <a href="/solist" class="btn-nav btn-home">🏠 หน้าหลัก</a>
  </div>
</div>

{{-- ============================================================
     ส่วนที่ 1: งานสร้างเอกสารใหม่ (status = ยืนยัน, ไม่มี status_bill)
     Bot (selenium) จับ: id="table-new"
     ============================================================ --}}
<div class="sec-header">
  <div class="sec-title">
    📄 สร้างเอกสารใหม่
    <span class="sec-count">{{ $newJobs->count() }}</span>
  </div>
  <span class="sec-desc">รายการที่ยืนยันแล้ว — Bot จะสร้างเอกสารใหม่</span>
</div>
<div class="abar">
  <button class="btn-print" onclick="markSelectedPrinted('new')">🖨 บันทึกการพิมพ์</button>
  <label class="chk-all-label"><input type="checkbox" class="chk" onclick="toggleAll('new',this.checked)"> เลือกทั้งหมด</label>
  <div class="f-search">
    <svg width="12" height="12" viewBox="0 0 13 13" fill="none"><circle cx="5.5" cy="5.5" r="3.5" stroke="#9CA3AF" stroke-width="1.2"/><path d="M8.5 8.5l3 3" stroke="#9CA3AF" stroke-width="1.2" stroke-linecap="round"/></svg>
    <input type="text" placeholder="ค้นหา SO" oninput="searchTbl('table-new',this.value)">
  </div>
</div>
<div class="tw">
<table id="table-new" name="table-new">
<thead><tr>
  <th style="width:40px">☑</th>
  <th>เลขที่บิล</th>
  <th>ใบสั่งขาย</th>
  <th>PO</th>
  <th>รหัสลูกค้า</th>
  <th>วันที่</th>
  <th>ผู้เปิดบิล</th>
  <th>%</th>
  <th>ยอดมัดจำ</th>
  <th>ยอดคงเหลือ</th>
  <th>ประเภท</th>
  <th>สลิป</th>
</tr></thead>
<tbody>
@forelse($newJobs as $item)
  @php
    $dt=$item->dep_type??'';$tn=$tl[$dt]??$dt;$tc=$tm[$dt]??'b-default';
    $dd=$item->date_dep?\Carbon\Carbon::parse($item->date_dep):null;
    $df=$dd?($dd->format('d/m/').($dd->year+543)):'—';
    $ip=!empty($item->print_time);
  @endphp
  <tr class="{{ $ip?'processed':'' }}" data-so="{{ strtolower($item->so_id??'') }}">
    <td><input type="checkbox" class="chk chk-new" name="markprint-new[]" value="{{ $item->id }}" {{ $ip?'disabled':'' }}></td>
    <td>
      <div style="display:flex;align-items:center;gap:4px;justify-content:center;white-space:nowrap">
        <span class="c-code">{{ $item->deposit_bill_id??'—' }}</span>
        @if($item->deposit_bill_id)<button class="btn-copy" onclick="cpText('{{ $item->deposit_bill_id }}',this)">คัดลอก</button>@endif
      </div>
    </td>
    <td>
      <div style="display:flex;align-items:center;gap:4px;justify-content:center;white-space:nowrap">
        <span style="font-size:12px;color:var(--steel)">{{ $item->so_id??'—' }}</span>
        @if($item->so_id)<button class="btn-copy" onclick="cpText('{{ $item->so_id }}',this)">คัดลอก</button>@endif
      </div>
    </td>
    <td>
      <div style="display:flex;align-items:center;gap:4px;justify-content:center;white-space:nowrap">
        <span style="font-size:12px;color:var(--steel)">{{ $item->po_document??'—' }}</span>
        @if($item->po_document)<button class="btn-copy" onclick="cpText('{{ $item->po_document }}',this)">คัดลอก</button>@endif
      </div>
    </td>
    <td>
      @if($item->customer_id)
        <div style="display:flex;align-items:center;gap:4px;justify-content:center;white-space:nowrap">
          <span style="font-size:12px;color:var(--steel)">{{ $item->customer_id }}</span>
          <button class="btn-copy" onclick="cpText('{{ $item->customer_id }}',this)">คัดลอก</button>
        </div>
      @else<span class="c-sm">—</span>@endif
    </td>
    <td class="c-sm" style="white-space:nowrap">{{ $df }}</td>
    <td class="c-sm">{{ $item->emp_name??'—' }}</td>
    <td>
      <div style="display:flex;align-items:center;gap:4px;justify-content:center">
        <span class="c-percent">{{ number_format((float)($item->dep_per??0),2) }}%</span>
        <button class="btn-copy" onclick="cpText('{{ number_format((float)($item->dep_per??0),2) }}',this)">คัดลอก</button>
      </div>
    </td>
    <td>
      <div style="display:flex;align-items:center;gap:4px;justify-content:center">
        <span class="c-money">{{ number_format((float)($item->dep_price??0),2) }}</span>
        <button class="btn-copy" onclick="cpText('{{ number_format((float)($item->dep_price??0),2) }}',this)">คัดลอก</button>
      </div>
    </td>
    <td>
      <div style="display:flex;align-items:center;gap:4px;justify-content:center">
        <span class="c-money">{{ number_format((float)($item->grand_total??0),2) }}</span>
        <button class="btn-copy" onclick="cpText('{{ number_format((float)($item->grand_total??0)+(float)($item->dep_price??0),2) }}',this)">คัดลอก</button>
      </div>
    </td>
    <td>
      <div style="display:flex;flex-direction:column;gap:4px;align-items:center">
        @if($dt)<span class="badge {{ $tc }}">{{ $tn }}</span>@else<span class="c-sm">—</span>@endif
        @if($ip)
          <span class="processed-badge">✓ พิมพ์แล้ว</span>
        @else
          <input type="file" class="file-input" accept="application/pdf" style="width:140px;font-size:11px;padding:3px"
            onchange="uploadPdf('{{ $item->id }}','{{ $item->deposit_bill_id }}',this)">
        @endif
      </div>
    </td>
    <td>
      @if(!empty($item->slip_time))
        <span class="slip-badge">🕐 {{ \Carbon\Carbon::parse($item->slip_time)->setTimezone('Asia/Bangkok')->format('H:i d/m/Y') }}</span>
      @else
        <span class="no-slip">✕ ยังไม่มี</span>
      @endif
    </td>
  </tr>
@empty
  <tr><td colspan="12"><div class="empty"><h4>ไม่มีงานสร้างเอกสารใหม่</h4><p>รอ admin ยืนยัน</p></div></td></tr>
@endforelse
</tbody>
</table>
</div>

{{-- ============================================================
     ส่วนที่ 2: งานแก้ไขเอกสาร (status = มี WHT)
     Bot (selenium) จับ: id="table-wht", thead สีม่วง
     ============================================================ --}}
<div class="sec-header">
  <div class="sec-title">
    📝 แก้ไขเอกสาร (มี WHT)
    <span class="sec-count wht">{{ $whtJobs->count() }}</span>
  </div>
  <span class="sec-desc">รายการที่มี WHT — Bot จะแก้ไขเอกสารเดิม</span>
</div>
<div class="abar">
  <button class="btn-print" style="background:var(--purple)" onclick="markSelectedPrinted('wht')">🖨 บันทึกการพิมพ์ (WHT)</button>
  <label class="chk-all-label"><input type="checkbox" class="chk" onclick="toggleAll('wht',this.checked)"> เลือกทั้งหมด</label>
</div>
<div class="tw">
<table id="table-wht" name="table-wht">
<thead><tr class="wht-head">
  <th style="width:40px">☑</th>
  <th>เลขที่บิล</th>
  <th>ใบสั่งขาย</th>
  <th>PO</th>
  <th>รหัสลูกค้า</th>
  <th>WHT เลขเอกสาร</th>
  <th>%</th>
  <th>ยอดมัดจำ</th>
  <th>ยอดคงเหลือ</th>
  <th>ประเภท</th>
  <th>สลิป</th>
</tr></thead>
<tbody>
@forelse($whtJobs as $item)
  @php
    $dt=$item->dep_type??'';$tn=$tl[$dt]??$dt;$tc=$tm[$dt]??'b-default';
    // ✅ งาน WHT ถือว่า "เสร็จแล้ว" ต่อเมื่อ bot กลับมาพิมพ์ใหม่หลังจากใส่ WHT
    // เช็คว่า print_time ใหม่กว่า wht_time (bot กลับมาทำหลังใส่ WHT)
    $whtTime = $item->wht_time ? \Carbon\Carbon::parse($item->wht_time) : null;
    $printTime = $item->print_time ? \Carbon\Carbon::parse($item->print_time) : null;
    $ip = $whtTime && $printTime && $printTime->greaterThan($whtTime);
  @endphp
  <tr class="{{ $ip?'processed':'' }}" data-so="{{ strtolower($item->so_id??'') }}">
    <td><input type="checkbox" class="chk chk-wht" name="markprint-wht[]" value="{{ $item->id }}" {{ $ip?'disabled':'' }}></td>
    <td>
      <div style="display:flex;align-items:center;gap:4px;justify-content:center;white-space:nowrap">
        <span class="c-code">{{ $item->deposit_bill_id??'—' }}</span>
        @if($item->deposit_bill_id)<button class="btn-copy" onclick="cpText('{{ $item->deposit_bill_id }}',this)">คัดลอก</button>@endif
      </div>
    </td>
    <td style="font-size:12px;color:var(--steel)">{{ $item->so_id??'—' }}</td>
    <td style="font-size:12px;color:var(--steel)">{{ $item->po_document??'—' }}</td>
    <td style="font-size:12px;color:var(--steel)">{{ $item->customer_id??'—' }}</td>
    {{-- ✅ Bot จับคอลัมน์นี้: เลข WHT --}}
    <td>
      <div style="display:flex;align-items:center;gap:4px;justify-content:center">
        <span class="badge b-wht" data-wht="{{ $item->wht_doc_no }}">{{ $item->wht_doc_no ?? '—' }}</span>
        @if($item->wht_doc_no)<button class="btn-copy" onclick="cpText('{{ $item->wht_doc_no }}',this)">คัดลอก</button>@endif
      </div>
    </td>
    <td class="c-percent">{{ number_format((float)($item->dep_per??0),2) }}%</td>
    <td class="c-money">{{ number_format((float)($item->dep_price??0),2) }}</td>
    <td class="c-money">{{ number_format((float)($item->grand_total??0),2) }}</td>
    <td>
      <div style="display:flex;flex-direction:column;gap:4px;align-items:center">
        @if($dt)<span class="badge {{ $tc }}">{{ $tn }}</span>@else<span class="c-sm">—</span>@endif
        @if($ip)
          <span class="processed-badge">✓ พิมพ์แล้ว</span>
        @else
          <input type="file" class="file-input" accept="application/pdf" style="width:140px;font-size:11px;padding:3px"
            onchange="uploadPdf('{{ $item->id }}','{{ $item->deposit_bill_id }}',this)">
        @endif
      </div>
    </td>
    <td>
      @if(!empty($item->slip_time))
        <span class="slip-badge">🕐 {{ \Carbon\Carbon::parse($item->slip_time)->setTimezone('Asia/Bangkok')->format('H:i d/m/Y') }}</span>
      @else
        <span class="no-slip">✕ ยังไม่มี</span>
      @endif
    </td>
  </tr>
@empty
  <tr><td colspan="11"><div class="empty"><h4>ไม่มีงาน WHT</h4><p>ยังไม่มีรายการที่ต้องแก้ไขเอกสาร</p></div></td></tr>
@endforelse
</tbody>
</table>
</div>

@if($deposits->total()>0)
<div style="margin:0 22px 22px">
<div class="pgb" style="border:1px solid var(--line);border-radius:0 0 6px 6px">
  <span class="pgi">แสดง {{ $deposits->firstItem()??0 }}–{{ $deposits->lastItem()??0 }} จาก {{ $deposits->total() }}</span>
  <div>{{ $deposits->appends(['create_by'=>request('create_by')])->links() }}</div>
</div>
</div>
@endif

<div class="toast" id="toast"><span id="toastMsg">—</span></div>

<script>
const CS=document.querySelector('meta[name="csrf-token"]')?.content||'',CU='{{ $cb }}';
function showToast(m,e){const t=document.getElementById('toast');document.getElementById('toastMsg').textContent=m;t.classList.toggle('error',!!e);t.classList.add('show');setTimeout(()=>t.classList.remove('show'),2800)}

function searchTbl(tblId,q){
  q=q.trim().toLowerCase();
  document.querySelectorAll('#'+tblId+' tbody tr[data-so]').forEach(r=>{
    r.style.display=(!q||r.dataset.so.includes(q))?'':'none';
  });
}

function toggleAll(group,checked){
  const sel=group==='wht'?'.chk-wht:not([disabled])':'.chk-new:not([disabled])';
  document.querySelectorAll(sel).forEach(c=>c.checked=checked);
}

function cpText(text,btn){
  const t=String(text||'').trim();if(!t)return;
  const orig=btn.textContent;
  const done=()=>{btn.classList.add('copied');btn.textContent='✓';setTimeout(()=>{btn.classList.remove('copied');btn.textContent=orig},1500)};
  (navigator.clipboard?navigator.clipboard.writeText(t):Promise.reject()).then(done).catch(()=>{const i=document.createElement('input');i.value=t;document.body.appendChild(i);i.select();try{document.execCommand('copy')}catch(e){}document.body.removeChild(i);done()});
}

async function uploadPdf(depositId,depositBillId,inputEl){
  const file=inputEl.files[0];if(!file)return;
  if(file.type!=='application/pdf'&&!file.name.toLowerCase().endsWith('.pdf')){showToast('PDF เท่านั้น',true);inputEl.value='';return}
  const expected=depositBillId+'.pdf';
  if(file.name.trim()!==expected){showToast('ชื่อไฟล์ต้องเป็น "'+expected+'"',true);inputEl.value='';return}
  if(file.size>10*1024*1024){showToast('ไฟล์เกิน 10 MB',true);inputEl.value='';return}
  const fd=new FormData();fd.append('deposit_id',depositId);fd.append('deposit_bill_id',depositBillId);fd.append('printed_by',CU);fd.append('pdf_file',file);
  inputEl.disabled=true;showToast('กำลังอัปโหลด...');
  try{
    const res=await fetch('/deposit/upload-bill-pdf',{method:'POST',headers:{'X-CSRF-TOKEN':CS,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},body:fd});
    const raw=await res.text();let data;try{data=JSON.parse(raw)}catch(e){throw new Error('Non-JSON ('+res.status+')')}
    if(!res.ok||data.success===false)throw new Error(data.message||'HTTP '+res.status);
    showToast('อัปโหลดสำเร็จ');setTimeout(()=>location.reload(),700);
  }catch(e){showToast('ผิดพลาด: '+e.message,true);inputEl.disabled=false;inputEl.value=''}
}

async function markSelectedPrinted(group){
  const sel=group==='wht'?'input[name="markprint-wht[]"]:checked':'input[name="markprint-new[]"]:checked';
  const checked=Array.from(document.querySelectorAll(sel));
  if(!checked.length){showToast('เลือกรายการก่อน',true);return}
  if(!confirm('บันทึกพิมพ์ '+checked.length+' รายการ?'))return;
  const ids=checked.map(c=>c.value);
  try{
    const res=await fetch('/deposit/mark-printed-bulk',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CS,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},body:JSON.stringify({deposit_ids:ids,printed_by:CU})});
    const d=await res.json().catch(()=>({success:true}));
    if(!res.ok||d.success===false)throw new Error(d.message||'HTTP '+res.status);
    showToast('บันทึก '+ids.length+' รายการสำเร็จ');setTimeout(()=>location.reload(),600);
  }catch(e){showToast('ผิดพลาด: '+e.message,true)}
}
</script>
</body>
</html>