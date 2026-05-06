<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ใบมัดจำที่ยืนยันแล้ว (Bot)</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  /* ===== ธีมทางการ — Slate/Charcoal + Navy ===== */
  --green-header:#1F2937;       /* Slate 800 - แถบหัว เกือบดำ */
  --green-header-2:#374151;     /* Slate 700 - ไล่เฉดเข้ม */
  --green-table:#334155;        /* Slate 700 - หัวตาราง */
  --green-row:#F1F5F9;           /* Slate 100 - hover */
  --green-row-alt:#F8FAFC;       /* Slate 50 */
  --green-row-hover:#E2E8F0;     /* Slate 200 */
  --green-line:#CBD5E1;          /* Slate 300 - เส้นคั่น */
  --green-text:#1E293B;          /* Slate 800 - ตัวอักษรเน้น */
  --green-link:#1E40AF;          /* Navy 800 - ลิงก์/accent */

  --red-btn:#991B1B;             /* Red 800 - ลึกขึ้น เป็นทางการ */
  --red-btn-hover:#7F1D1D;       /* Red 900 */

  --ink:#0F172A;
  --ink3:#1E293B;
  --steel:#334155;
  --ash:#64748B;
  --mist:#94A3B8;
  --fog:#CBD5E1;
  --pale:#F1F5F9;
  --snow:#F8FAFC;
  --white:#FFFFFF;

  --emerald:#047857;             /* Green 700 - เข้มขึ้น */
  --emerald-soft:#D1FAE5;
  --rose:#B91C1C;

  --border:#CBD5E1;
  --bg:#F1F5F9;                  /* พื้นหลัง Slate 100 */

  --r4:4px;--r6:6px;--r8:8px;
}

html,body{width:100%;min-height:100vh}
body{font-family:'Sarabun','Kanit',sans-serif;font-size:14px;background:var(--bg);color:var(--ink);line-height:1.55}

.page{width:100%;padding:0;min-height:100vh}

/* ===== TOPBAR ===== */
.topbar{
  background:linear-gradient(180deg,var(--green-header) 0%,var(--green-header-2) 100%);
  padding:14px 22px;
  display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;
  border-bottom:3px solid #0F172A;
}
.topbar-brand{display:flex;align-items:center;gap:12px}
.brand-title{
  font-family:'Kanit',sans-serif;
  font-size:22px;font-weight:600;color:#fff;
  letter-spacing:-.3px;
  text-shadow:0 1px 2px rgba(0,0,0,.15);
}
.brand-badge{
  display:inline-flex;align-items:center;gap:5px;
  padding:3px 10px;border-radius:20px;
  background:rgba(255,255,255,.18);
  font-size:11px;color:#fff;font-weight:600;
  margin-left:8px;letter-spacing:.05em;
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

.btn-back{
  display:inline-flex;align-items:center;gap:6px;
  padding:7px 16px;border-radius:6px;
  background:rgba(255,255,255,.12);color:#fff;
  border:1px solid rgba(255,255,255,.25);
  font-size:13px;font-weight:500;cursor:pointer;text-decoration:none;
  transition:background .15s;font-family:inherit;
}
.btn-back:hover{background:rgba(255,255,255,.2)}

/* ===== ACTION BAR ===== */
.action-bar{
  background:var(--bg);
  padding:12px 22px;
  display:flex;align-items:center;gap:10px;flex-wrap:wrap;
}

.btn{
  display:inline-flex;align-items:center;gap:6px;
  padding:7px 15px;border-radius:var(--r6);
  font-size:13px;font-weight:500;
  cursor:pointer;transition:all .15s;
  font-family:inherit;white-space:nowrap;
  border:1px solid transparent;text-decoration:none;
}
.btn-primary{background:var(--green-table);color:#fff;border-color:var(--green-table)}
.btn-primary:hover{background:var(--green-header-2);border-color:var(--green-header-2)}
.btn-sm{padding:4px 10px;font-size:11px;border-radius:var(--r4)}

.f-divider{width:1px;height:24px;background:var(--border);flex-shrink:0}
.f-search{
  display:flex;align-items:center;gap:7px;
  padding:6px 12px;border:1px solid var(--border);
  border-radius:var(--r4);background:#fff;
  transition:border .15s,box-shadow .15s;
}
.f-search:focus-within{border-color:var(--green-table);box-shadow:0 0 0 3px rgba(46,139,87,.1)}
.f-search input{
  border:none;background:transparent;font-size:13px;font-family:inherit;
  color:var(--ink);outline:none;width:200px;text-align:right;direction:rtl;
}
.f-search input::placeholder{color:var(--mist)}

.action-right{margin-left:auto;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.checkall-label{
  display:flex;align-items:center;gap:7px;
  font-size:13px;color:var(--steel);cursor:pointer;
  padding:6px 12px;background:#fff;border:1px solid var(--border);border-radius:var(--r4);
  user-select:none;
}
.checkall-label:hover{background:var(--green-row);border-color:var(--green-table);color:var(--green-text)}
.f-stats{
  display:inline-flex;align-items:center;gap:8px;
  background:#fff;border:1px solid var(--green-line);
  padding:6px 14px;border-radius:var(--r4);font-size:13px;color:var(--steel);
}
.f-stats .num{
  background:var(--green-table);color:#fff;
  padding:2px 10px;border-radius:12px;
  font-weight:700;font-size:12px;
}

/* ===== TABLE ===== */
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
tbody tr:hover{background:var(--green-row)}
tbody tr.hidden-row{display:none}
tbody tr.processed{background:#EFF6FF}
tbody tr.processed:hover{background:#DBEAFE}

td{
  padding:10px 12px;
  border-bottom:1px solid var(--green-line);
  border-right:1px solid var(--green-line);
  vertical-align:middle;color:var(--ink3);
  text-align:center;
}
td:last-child{border-right:none}
tbody tr:last-child td{border-bottom:none}

.c-code{font-family:'Sarabun','SFMono-Regular',monospace;font-size:12px;color:var(--ink3);font-weight:600}
.c-code-light{font-family:'Sarabun','SFMono-Regular',monospace;font-size:12px;color:var(--steel)}
.c-sm{font-size:12px;color:var(--ash)}
.c-percent{font-family:'Sarabun',monospace;font-weight:500;color:var(--ink3);white-space:nowrap}
.c-money{font-family:'Sarabun',monospace;font-weight:600;color:var(--green-text);white-space:nowrap;text-align:right}

/* ===== CHECKBOX ===== */
.chk{
  width:16px;height:16px;cursor:pointer;
  accent-color:var(--green-table);
}

/* ===== TYPE BADGES ===== */
.badge{
  display:inline-flex;align-items:center;
  padding:3px 10px;border-radius:20px;
  font-size:11px;font-weight:600;white-space:nowrap;
}
.b-product{background:#E0F2F1;color:#00695C}
.b-service{background:#E8EAF6;color:#283593}
.b-transport{background:#FEF3C7;color:#92400E}
.ft-default{background:var(--pale);color:var(--ash)}

/* ===== COPY BUTTON ===== */
.btn-copy{
  display:inline-flex;align-items:center;
  padding:3px 9px;border-radius:var(--r4);
  border:1px solid var(--border);background:var(--snow);
  color:var(--ash);cursor:pointer;transition:all .12s;
  flex-shrink:0;font-family:inherit;font-size:10px;font-weight:600;
  white-space:nowrap;
}
.btn-copy:hover{border-color:var(--green-table);background:var(--green-row);color:var(--green-text)}
.btn-copy.copied{border-color:var(--emerald);background:var(--emerald-soft);color:var(--emerald)}

/* ===== INLINE INPUT ===== */
.inp-sm{
  width:110px;height:28px;
  padding:2px 8px;
  border:1px solid var(--border);border-radius:var(--r4);
  font-size:12px;font-family:inherit;color:var(--ink);
  background:#fff;outline:none;
  transition:border .15s;
}
.inp-sm:focus{border-color:var(--green-table);box-shadow:0 0 0 2px rgba(46,139,87,.1)}

.processed-badge{
  display:inline-flex;align-items:center;gap:4px;
  padding:4px 10px;border-radius:12px;
  background:#DBEAFE;color:#1E40AF;
  font-size:11px;font-weight:600;
}

/* ===== EMPTY ===== */
.empty,.no-match{padding:60px 20px;text-align:center;background:#fff}
.empty-ico{
  width:64px;height:64px;border-radius:12px;
  background:var(--green-row);margin:0 auto 14px;
  display:flex;align-items:center;justify-content:center;
}
.empty h4,.no-match h4{font-size:16px;font-weight:600;margin-bottom:5px;font-family:'Kanit',sans-serif;color:var(--green-text)}
.empty p,.no-match p{font-size:13px;color:var(--ash);max-width:400px;margin:0 auto;line-height:1.6}

/* ===== Pagination ===== */
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

/* ===== Toast ===== */
.toast{
  position:fixed;bottom:24px;left:50%;
  transform:translateX(-50%) translateY(100px);
  background:var(--green-header);color:#fff;
  padding:12px 22px;border-radius:8px;
  font-size:13px;font-weight:500;
  box-shadow:0 8px 24px rgba(0,0,0,.2);
  z-index:3000;opacity:0;
  transition:all .25s ease-out;
}
.toast.show{transform:translateX(-50%) translateY(0);opacity:1}
.toast.error{background:#C62828}

@media(max-width:768px){
  .action-bar,.tbl-wrap{margin-left:10px;margin-right:10px}
  .action-bar{padding:12px 10px}
  .tbl-wrap{margin:0 10px 10px}
  .f-search input{width:140px}
  th,td{padding:8px;font-size:12px}
  .brand-title{font-size:18px}
  .brand-badge{display:none}
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
  $typeMap = [
    'product'  => 'b-product',
    'service'  => 'b-service',
    'shipping' => 'b-transport',
    'สินค้า'   => 'b-product',
    'บริการ'   => 'b-service',
    'ขนส่ง'    => 'b-transport',
  ];

  $cb = trim(request()->get('create_by', ''));
@endphp

<div class="page">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-brand">
      <div class="brand-title">ใบมัดจำที่ยืนยันแล้ว
        <span class="brand-badge">
          <svg width="11" height="11" viewBox="0 0 12 12" fill="none">
            <path d="M2 6.5l2.5 2.5L10 4" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          BOT
        </span>
      </div>
    </div>
    <div class="topbar-right">
      <div class="user-pill">
        @if($cb !== '')
          <div class="user-ava">{{ strtoupper(substr($cb, 0, 2)) }}</div>
          {{ $cb }}
        @else
          <span style="font-size:14px">👤</span>
          <span>ผู้ใช้:</span>
        @endif
      </div>
      <a href="{{ route('deposit.dashboard') }}{{ $cb !== '' ? '?create_by='.$cb : '' }}" class="btn-back">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
          <path d="M8 2L3.5 6.5L8 11" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        ใบมัดจำ
      </a>
      <a href="/solist" class="btn-home">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
          <path d="M2 6.5L6.5 2 11 6.5V11.5H8V8.5H5V11.5H2V6.5Z" stroke="white" stroke-width="1.4" stroke-linejoin="round"/>
        </svg>
        หน้าหลัก
      </a>
    </div>
  </div>

  <!-- ACTION BAR -->
  <div class="action-bar">
    <button class="btn btn-primary" onclick="markSelectedPrinted()">
      <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
        <rect x="3" y="2" width="7" height="3" stroke="white" stroke-width="1.3"/>
        <rect x="2" y="5" width="9" height="5" rx="1" stroke="white" stroke-width="1.3"/>
        <rect x="4" y="7" width="5" height="3" stroke="white" stroke-width="1.3"/>
      </svg>
      บันทึกการพิมพ์
    </button>

    <div class="f-divider"></div>

    <div class="f-stats">
      <span>รายการที่ยืนยันแล้ว</span>
      <span class="num">{{ $totalCount }}</span>
    </div>

    <div class="f-divider"></div>

    <div class="f-search">
      <svg width="12" height="12" viewBox="0 0 13 13" fill="none">
        <circle cx="5.5" cy="5.5" r="3.5" stroke="#9CA3AF" stroke-width="1.2"/>
        <path d="M8.5 8.5l3 3" stroke="#9CA3AF" stroke-width="1.2" stroke-linecap="round"/>
      </svg>
      <input type="text" id="search-input" placeholder="ค้นหา ใบสั่งขาย" oninput="searchTable()">
    </div>

    <div class="action-right">
      <label class="checkall-label">
        <input type="checkbox" id="checkAll" class="chk" onclick="toggleCheckboxes()">
        เลือกทั้งหมด
      </label>
    </div>
  </div>

  <!-- TABLE -->
  <div class="tbl-wrap">
    <table name = "table-dashboard" id="table-dashboard">
      <thead>
        <tr>
          <th style="width:42px">
            <svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="display:block;margin:0 auto">
              <rect x="1.5" y="1.5" width="9" height="9" rx="1.5" stroke="white" stroke-width="1.3"/>
              <path d="M3.5 6l2 2 3-3" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </th>
          <th>เลขที่บิล</th>
          <th>ใบสั่งขาย</th>
          <th>รหัสลูกค้า</th>
          <th>วันที่จัดส่ง</th>
          <th>ผู้เปิดบิล</th>
          <th>%</th>
          <th>ยอดมัดจำ</th>
          <th>ประเภทงาน</th>
        </tr>
      </thead>
      <tbody id="table-body">
        @forelse($deposits as $item)
        @php
          $depType = $item->dep_type ?? '';
          $typeName = $typeLabel[$depType] ?? $depType;
          $typeCls  = $typeMap[$depType] ?? 'ft-default';

          $billRef = 'REF-' . str_pad($item->id ?? 0, 6, '0', STR_PAD_LEFT);
          $deliDate = $item->date_dep ? \Carbon\Carbon::parse($item->date_dep) : null;
          $formatted = $deliDate ? ($deliDate->format('d/m/') . ($deliDate->year + 543)) : '—';

          $isPrinted = !empty($item->print_time);
        @endphp
        <tr class="{{ $isPrinted ? 'processed' : '' }}"
            data-so="{{ strtolower($item->so_id ?? '') }}"
            data-id="{{ $item->id ?? '' }}">
          <td>
            <input type="checkbox" class="chk row-chk"
              name="markprint[]"
              value="{{ $item->id }}"
              data-so="{{ $item->so_id }}"
              {{ $isPrinted ? 'disabled' : '' }}>
          </td>

          {{-- เลขที่บิล --}}
          <td>
            <span class="c-code">{{ $billRef }}</span>
          </td>

          {{-- ใบสั่งขาย + คัดลอก --}}
          <td>
            <div style="display:flex;align-items:center;gap:5px;justify-content:center;white-space:nowrap">
              <span class="c-code-light">{{ $item->so_id ?? '—' }}</span>
              @if(!empty($item->so_id))
                <button class="btn-copy" onclick="cpText('{{ $item->so_id }}',this)">คัดลอก</button>
              @endif
            </div>
          </td>

          {{-- รหัสลูกค้า + คัดลอก --}}
          <td>
            @if(!empty($item->customer_id))
              <div style="display:flex;align-items:center;gap:5px;justify-content:center;white-space:nowrap">
                <span class="c-code-light">{{ $item->customer_id }}</span>
                <button class="btn-copy" onclick="cpText('{{ $item->customer_id }}', this)">คัดลอก</button>
              </div>
            @else
              <span class="c-sm" style="color:var(--fog)">—</span>
            @endif
          </td>

          {{-- วันที่จัดส่ง --}}
          <td class="c-sm" style="white-space:nowrap">{{ $formatted }}</td>

          {{-- ผู้เปิดบิล --}}
          <td class="c-sm">{{ $item->emp_name ?? '—' }}</td>

          {{-- % --}}
          <td>
            <div style="display:flex;align-items:center;gap:5px;justify-content:center;white-space:nowrap">
              <span class="c-percent">{{ number_format((float)($item->dep_per ?? 0), 2) }}%</span>
              <button class="btn-copy" onclick="cpText('{{ number_format((float)($item->dep_per ?? 0), 2) }}',this)">คัดลอก</button>
            </div>
          </td>

          {{-- ยอดมัดจำ --}}
          <td>
            <div style="display:flex;align-items:center;gap:5px;justify-content:center;white-space:nowrap">
              <span class="c-money">{{ number_format((float)($item->dep_price ?? 0), 2) }}</span>
              <button class="btn-copy" onclick="cpText('{{ number_format((float)($item->dep_price ?? 0), 2) }}',this)">คัดลอก</button>
            </div>
          </td>

          {{-- ประเภทงาน + คัดลอก + กรอกเลข + บันทึก --}}
          <td>
            <div style="display:flex;flex-direction:column;gap:5px;align-items:center">
              <div style="display:flex;flex-wrap:wrap;gap:4px;align-items:center;justify-content:center">
                @if($depType)
                  <span class="badge {{ $typeCls }}">{{ $typeName }}</span>
                  <button class="btn-copy" onclick="cpText('{{ $typeName }}',this)">คัดลอก</button>
                @else
                  <span class="c-sm">—</span>
                @endif
              </div>
              @if($isPrinted)
                <span class="processed-badge">
                  <svg width="11" height="11" viewBox="0 0 12 12" fill="none">
                    <path d="M2 6l2.5 2.5L10 4" stroke="#1E40AF" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  พิมพ์แล้ว
                </span>
              @else
                <div style="display:flex;align-items:center;gap:4px">
                  <input type="text" class="inp-sm"
                    id="bill_input_{{ $item->id }}"
                    placeholder="กรอกเลขบิล...">
                  <button class="btn btn-primary btn-sm"
                    onclick="saveBillNo('{{ $item->id }}',this)">
                    บันทึก
                  </button>
                </div>
              @endif
            </div>
          </td>

        </tr>
        @empty
        <tr>
          <td colspan="9" style="background:#fff;border-right:none">
            <div class="empty">
              <div class="empty-ico">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                  <path d="M5 9.5l4.5 4.5L23 6" stroke="#1E293B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <rect x="3" y="3" width="22" height="22" rx="3" stroke="#1E293B" stroke-width="1.5" opacity=".3"/>
                </svg>
              </div>
              <h4>ยังไม่มีใบมัดจำที่ยืนยัน</h4>
              <p>รายการจะมาแสดงในหน้านี้เมื่อ admin กดยืนยันสถานะที่หน้าใบมัดจำหลัก</p>
            </div>
          </td>
        </tr>
        @endforelse

        <!-- ไม่พบจากการค้นหา -->
        <tr class="no-match-row" id="noMatchRow" style="display:none">
          <td colspan="9" style="background:#fff;border-right:none">
            <div class="no-match">
              <div class="empty-ico">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                  <circle cx="12" cy="12" r="7" stroke="#1E293B" stroke-width="1.8"/>
                  <path d="M17 17l5 5" stroke="#1E293B" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
              </div>
              <h4>ไม่พบรายการ</h4>
              <p>ไม่มีรายการที่ตรงกับเลขใบสั่งขายที่ค้นหา</p>
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

<!-- Toast -->
<div class="toast" id="toast">
  <span id="toastMsg">—</span>
</div>

<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';
const CURRENT_USER = '{{ $cb }}';

/* ===== Toast ===== */
function showToast(msg, isError){
  const t = document.getElementById('toast');
  document.getElementById('toastMsg').textContent = msg;
  t.classList.toggle('error', !!isError);
  t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'), 2800);
}

/* ===== Search (เลขใบสั่งขาย) ===== */
function searchTable(){
  const q = document.getElementById('search-input').value.trim().toLowerCase();
  const rows = document.querySelectorAll('#table-body tr[data-so]');
  let visibleCount = 0;
  rows.forEach(r => {
    const soId = r.getAttribute('data-so');
    const show = !q || soId.includes(q);
    r.style.display = show ? '' : 'none';
    if(show) visibleCount++;
  });
  const noMatch = document.getElementById('noMatchRow');
  if(rows.length > 0 && visibleCount === 0 && q){
    noMatch.style.display = 'table-row';
  } else {
    noMatch.style.display = 'none';
  }
}

/* ===== Toggle checkboxes ===== */
function toggleCheckboxes(){
  const all = document.getElementById('checkAll').checked;
  document.querySelectorAll('input[name="markprint[]"]:not([disabled])').forEach(c=>c.checked=all);
}

/* ===== Copy ===== */
function cpText(text, btn){
  const t = String(text||'').trim(); if(!t) return;
  const orig = btn.textContent;
  const done = ()=>{
    btn.classList.add('copied');
    btn.textContent='คัดลอกแล้ว ✓';
    setTimeout(()=>{btn.classList.remove('copied');btn.textContent=orig;}, 1500);
  };
  (navigator.clipboard ? navigator.clipboard.writeText(t) : Promise.reject())
    .then(done).catch(()=>{
      const i=document.createElement('input');i.value=t;
      document.body.appendChild(i);i.select();
      try{document.execCommand('copy');}catch(e){}
      document.body.removeChild(i);done();
    });
}

/* ===== บันทึกเลขบิล (per row) ===== */
async function saveBillNo(depositId, btn){
  const inp = document.getElementById('bill_input_'+depositId);
  const val = inp ? inp.value.trim() : '';
  if(!val){ showToast('กรุณากรอกเลขบิล', true); return; }

  const orig = btn.textContent;
  btn.disabled = true;
  btn.textContent = '...';

  try {
    const res = await fetch('/deposit/mark-printed', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        deposit_id: depositId,
        bill_no: val,
        printed_by: CURRENT_USER
      })
    });

    if(!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json().catch(()=>({success:true}));

    if(data.success === false){
      throw new Error(data.message || 'บันทึกไม่สำเร็จ');
    }

    btn.style.background = 'var(--emerald)';
    btn.style.borderColor = 'var(--emerald)';
    btn.textContent = 'บันทึกแล้ว ✓';
    showToast('บันทึกเลขบิล ' + val + ' สำเร็จ');
    setTimeout(()=>location.reload(), 800);

  } catch(err) {
    console.error(err);
    showToast('เกิดข้อผิดพลาด: ' + err.message, true);
    btn.disabled = false;
    btn.textContent = orig;
  }
}

/* ===== บันทึกหลายรายการพร้อมกัน ===== */
async function markSelectedPrinted(){
  const checked = Array.from(document.querySelectorAll('input[name="markprint[]"]:checked'));
  if(!checked.length){ showToast('กรุณาเลือกรายการที่ต้องการบันทึก', true); return; }

  if(!confirm(`ยืนยันการบันทึกพิมพ์ ${checked.length} รายการ?`)) return;

  const ids = checked.map(c => c.value);

  try {
    const res = await fetch('/deposit/mark-printed-bulk', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        deposit_ids: ids,
        printed_by: CURRENT_USER
      })
    });

    if(!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json().catch(()=>({success:true}));

    if(data.success === false) throw new Error(data.message || 'บันทึกไม่สำเร็จ');

    showToast(`บันทึก ${ids.length} รายการสำเร็จ`);
    setTimeout(()=>location.reload(), 600);

  } catch(err) {
    console.error(err);
    showToast('เกิดข้อผิดพลาด: ' + err.message, true);
  }
}
</script>
</body>
</html>