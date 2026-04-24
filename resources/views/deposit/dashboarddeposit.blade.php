<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ข้อมูลใบมัดจำ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&family=IBM+Plex+Sans+Thai:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --ink:#0D0F12;--ink2:#1E2228;--ink3:#2E3440;
  --steel:#4A5568;--ash:#6B7280;--mist:#9CA3AF;
  --fog:#D1D5DB;--haze:#E5E7EB;--pale:#F3F4F6;
  --snow:#F9FAFB;--white:#FFFFFF;

  --teal:#00897B;--teal-deep:#00695C;--teal-soft:#E0F2F1;--teal-mid:#4DB6AC;
  --indigo:#3949AB;--indigo-soft:#E8EAF6;
  --amber:#F59E0B;--amber-soft:#FEF3C7;
  --rose:#E53E3E;--rose-soft:#FEE2E2;
  --emerald:#059669;--emerald-soft:#D1FAE5;

  --bg:#F0F2F5;--surface:#FFFFFF;
  --border:#E2E5EA;--border2:#D1D5DB;

  --r4:4px;--r6:6px;--r8:8px;--r12:12px;--r16:16px;--r20:20px;
}

html,body{width:100%;min-height:100vh}
body{font-family:'IBM Plex Sans Thai','Kanit',sans-serif;font-size:14px;background:var(--bg);color:var(--ink);line-height:1.55}

.page{width:100%;padding:20px 22px;min-height:100vh}

.topbar{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:18px;flex-wrap:wrap}
.topbar-brand{display:flex;align-items:center;gap:12px}
.brand-dot{width:36px;height:36px;border-radius:var(--r8);background:var(--teal);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.brand-title{font-family:'Kanit',sans-serif;font-size:18px;font-weight:600;color:var(--ink);letter-spacing:-.3px}
.brand-sub{font-size:11px;color:var(--ash);margin-top:1px;font-weight:400}

.topbar-right{display:flex;align-items:center;gap:8px;flex-wrap:wrap}

.user-pill{display:inline-flex;align-items:center;gap:8px;padding:5px 12px 5px 6px;border:1px solid var(--border);border-radius:30px;background:var(--white);font-size:12px;color:var(--steel);font-weight:500}
.user-ava{width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,var(--teal) 0%,var(--teal-mid) 100%);display:flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:600;flex-shrink:0;font-family:'Kanit',sans-serif}

.notif{position:relative;width:36px;height:36px;border-radius:var(--r8);border:1px solid var(--border);background:var(--white);display:flex;align-items:center;justify-content:center;text-decoration:none;color:var(--steel);transition:border-color .15s,background .15s;flex-shrink:0}
.notif:hover{border-color:var(--teal);background:var(--teal-soft);color:var(--teal)}
.notif-badge{position:absolute;top:-4px;right:-4px;min-width:16px;height:16px;border-radius:8px;background:var(--rose);color:#fff;font-size:9px;font-weight:700;display:none;align-items:center;justify-content:center;padding:0 3px;border:2px solid var(--bg)}

.btn{display:inline-flex;align-items:center;gap:6px;padding:7px 15px;border-radius:var(--r8);font-size:13px;font-weight:500;cursor:pointer;transition:all .15s;font-family:inherit;white-space:nowrap;border:1px solid transparent;text-decoration:none}
.btn-dark{background:var(--ink);color:#fff;border-color:var(--ink)}
.btn-dark:hover{background:var(--ink2)}

.filter-bar{background:var(--white);border:1px solid var(--border);border-radius:var(--r12);padding:10px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.f-label{font-size:11px;font-weight:600;color:var(--mist);letter-spacing:.06em;text-transform:uppercase;white-space:nowrap}
.f-search{display:flex;align-items:center;gap:7px;padding:6px 11px;border:1px solid var(--border);border-radius:var(--r6);background:var(--snow);transition:border .15s,box-shadow .15s}
.f-search:focus-within{border-color:var(--teal);background:var(--white);box-shadow:0 0 0 3px rgba(0,137,123,.1)}
.f-search input{border:none;background:transparent;font-size:13px;font-family:inherit;color:var(--ink);outline:none;width:200px}
.f-search input::placeholder{color:var(--mist)}

.main-card{background:var(--white);border:1px solid var(--border);border-radius:var(--r16);overflow:hidden}
.card-bar{padding:13px 18px;border-bottom:1px solid var(--haze);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;background:var(--white)}
.card-bar-left{display:flex;align-items:center;gap:10px}
.card-ico{width:30px;height:30px;border-radius:var(--r6);background:var(--teal-soft);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.card-ttl{font-size:14px;font-weight:600;color:var(--ink);font-family:'Kanit',sans-serif}
.pill{padding:2px 9px;border-radius:20px;background:var(--pale);font-size:11px;font-weight:600;color:var(--ash)}
.card-date{font-size:12px;color:var(--ash)}
.card-date strong{color:var(--ink);font-weight:600}

.tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
table{width:100%;border-collapse:collapse;font-size:13px}
thead tr{background:var(--snow)}
th{padding:9px 14px;font-size:10.5px;font-weight:600;color:var(--mist);letter-spacing:.07em;text-transform:uppercase;border-bottom:1px solid var(--haze);text-align:left;white-space:nowrap}
td{padding:11px 14px;border-bottom:1px solid var(--haze);vertical-align:middle;color:var(--ink3)}
tbody tr:last-child td{border-bottom:none}
tbody tr{transition:background .1s}
tbody tr:hover td{background:#F7FFFE}
tbody tr.confirmed td{background:#F0FDF9}

.c-num{text-align:center}
.c-wrap{max-width:190px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.c-sm{font-size:12px;color:var(--ash)}
.c-code{font-family:'IBM Plex Mono','SFMono-Regular',monospace;font-size:11.5px;color:var(--mist)}
.c-link{color:var(--indigo);font-weight:600;cursor:pointer;text-decoration:none;white-space:nowrap;transition:color .12s}
.c-link:hover{color:var(--teal);text-decoration:underline}
.c-idx{font-size:11px;color:var(--fog);text-align:center;font-family:'IBM Plex Mono',monospace}
.c-money{font-family:'IBM Plex Mono',monospace;font-size:12.5px;font-weight:600;color:var(--ink);text-align:right;white-space:nowrap}
.c-money-sub{font-family:'IBM Plex Mono',monospace;font-size:11px;color:var(--ash);text-align:right;white-space:nowrap}
.c-percent{font-family:'IBM Plex Mono',monospace;font-size:12px;color:var(--steel);text-align:right;white-space:nowrap}

.badge{display:inline-flex;align-items:center;gap:4px;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap}
.b-dot{width:5px;height:5px;border-radius:50%;flex-shrink:0}
.b-ok{background:var(--emerald-soft);color:#065F46}.b-ok .b-dot{background:var(--emerald)}
.b-wip{background:var(--indigo-soft);color:#1A237E}.b-wip .b-dot{background:var(--indigo)}
.b-wait{background:var(--amber-soft);color:#92400E}.b-wait .b-dot{background:var(--amber)}
.b-cancel{background:var(--rose-soft);color:#991B1B}.b-cancel .b-dot{background:var(--rose)}

.b-product{background:#E0F2F1;color:#00695C}
.b-service{background:#E8EAF6;color:#283593}
.b-transport{background:#FEF3C7;color:#92400E}

.empty{padding:60px 20px;text-align:center}
.empty-ico{width:52px;height:52px;border-radius:var(--r12);background:var(--pale);margin:0 auto 14px;display:flex;align-items:center;justify-content:center}
.empty h4{font-size:15px;font-weight:600;margin-bottom:5px;font-family:'Kanit',sans-serif}
.empty p{font-size:12px;color:var(--ash)}

.pg-bar{display:flex;align-items:center;justify-content:space-between;padding:11px 18px;border-top:1px solid var(--haze);flex-wrap:wrap;gap:8px}
.pg-info{font-size:12px;color:var(--ash)}
nav[role="navigation"]>div{display:flex!important;align-items:center!important;justify-content:center!important;gap:4px;flex-wrap:nowrap!important}
nav[role="navigation"]>div>p{display:none!important}
nav[role="navigation"] .sm\:hidden{display:none!important}
nav[role="navigation"] a[rel="prev"] span,nav[role="navigation"] a[rel="next"] span{display:none!important}
nav[role="navigation"] a[rel="prev"],nav[role="navigation"] a[rel="next"]{width:30px;height:30px;border-radius:var(--r6);background:var(--white);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--ash);transition:all .12s}
nav[role="navigation"] a[rel="prev"]:hover,nav[role="navigation"] a[rel="next"]:hover{border-color:var(--teal);color:var(--teal)}
nav[role="navigation"] svg{width:13px!important;height:13px!important}
nav[role="navigation"] a.relative,nav[role="navigation"] span.relative{min-width:30px;height:30px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:500;color:var(--indigo);border-radius:var(--r6);border:1px solid transparent;transition:all .12s}
nav[role="navigation"] a.relative:hover{background:var(--indigo-soft);border-color:#C5CAE9}
nav[role="navigation"] span[aria-current="page"]{background:var(--teal)!important;color:#fff!important;border-color:var(--teal)!important}

.modal-bg{display:none;position:fixed;inset:0;background:rgba(13,15,18,.55);z-index:1000;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(2px)}
.modal-bg.open{display:flex}
.modal{background:var(--white);border-radius:var(--r16);width:100%;max-width:680px;max-height:90vh;overflow-y:auto;border:1px solid var(--border);box-shadow:0 20px 60px rgba(0,0,0,.12)}
.modal-head{padding:18px 22px 14px;border-bottom:1px solid var(--haze);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:var(--white);z-index:1}
.modal-head h3{font-size:15px;font-weight:600;color:var(--ink);font-family:'Kanit',sans-serif}
.modal-x{width:28px;height:28px;border-radius:var(--r6);border:1px solid var(--border);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--ash);transition:all .12s;flex-shrink:0}
.modal-x:hover{background:var(--rose-soft);border-color:#FCA5A5;color:var(--rose)}
.modal-body{padding:18px 22px}
.sec-ttl{font-size:10px;font-weight:700;color:var(--mist);letter-spacing:.1em;text-transform:uppercase;margin-bottom:10px;padding-bottom:7px;border-bottom:1px solid var(--haze)}
.dg{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px}
.df{display:flex;flex-direction:column;gap:3px}
.df.full{grid-column:1/-1}
.dl{font-size:10px;font-weight:700;color:var(--mist);letter-spacing:.06em;text-transform:uppercase}
.dv{font-size:13px;color:var(--ink);font-weight:500}
.modal-foot{padding:12px 22px;border-top:1px solid var(--haze);display:flex;justify-content:flex-end}
.btn-ghost{padding:7px 16px;border-radius:var(--r8);border:1px solid var(--border);background:transparent;font-size:13px;font-weight:500;color:var(--steel);cursor:pointer;font-family:inherit;transition:all .15s}
.btn-ghost:hover{border-color:var(--border2);background:var(--pale)}

@media(max-width:768px){
  .page{padding:12px}
  .f-search input{width:140px}
  th,td{padding:8px 10px;font-size:12px}
}
@media(max-width:480px){
  .f-search input{width:100px}
  .brand-sub{display:none}
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
  $typeClass = [
    'product'  => 'b-product',
    'service'  => 'b-service',
    'shipping' => 'b-transport',
  ];
@endphp

<div class="page">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-brand">
      <div class="brand-dot">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
          <rect x="3" y="3" width="12" height="12" rx="1.5" stroke="white" stroke-width="1.4"/>
          <path d="M6 9l2 2 4-4" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div>
        <div class="brand-title">ข้อมูลใบมัดจำ</div>
        <div class="brand-sub">รายการใบมัดจำทั้งหมดในระบบ</div>
      </div>
    </div>
    <div class="topbar-right">
      <div class="user-pill">
        <div class="user-ava">{{ strtoupper(substr(request()->get('create_by','?'),0,2)) }}</div>
        {{ request()->get('create_by','Guest') }}
      </div>
      <a href="alertsale" class="notif" title="แจ้งเตือน">
        <svg width="15" height="15" viewBox="0 0 16 16" fill="none"><path d="M8 1.5A4.5 4.5 0 0 0 3.5 6v2.5L2 10h12l-1.5-1.5V6A4.5 4.5 0 0 0 8 1.5z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/><path d="M6.5 12.5a1.5 1.5 0 0 0 3 0" stroke="currentColor" stroke-width="1.3"/></svg>
        <span class="notif-badge" id="alertBadge">0</span>
      </a>
      <a href="http://server_update:8000/solist" class="btn btn-dark">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M2 6.5L6.5 2 11 6.5V11.5H8V8.5H5V11.5H2V6.5Z" stroke="white" stroke-width="1.3" stroke-linejoin="round"/></svg>
        หน้าหลัก
      </a>
    </div>
  </div>

  <!-- FILTER BAR -->
  <div class="filter-bar">
    <span class="f-label">ค้นหา</span>
    <form method="GET" action="{{ route('deposit.dashboard') }}">
      <input type="hidden" name="create_by" value="{{ request('create_by') }}">
      <input type="hidden" name="keyword" value="{{ request('keyword') }}">
      <div class="f-search">
        <svg width="12" height="12" viewBox="0 0 13 13" fill="none"><circle cx="5.5" cy="5.5" r="3.5" stroke="#9CA3AF" stroke-width="1.2"/><path d="M8.5 8.5l3 3" stroke="#9CA3AF" stroke-width="1.2" stroke-linecap="round"/></svg>
        <input type="text" name="so_keyword" placeholder="ค้นหา ใบสั่งขาย" value="{{ request('so_keyword') }}" oninput="dbs(this.form)">
      </div>
    </form>
    <form method="GET" action="{{ route('deposit.dashboard') }}">
      <input type="hidden" name="create_by" value="{{ request('create_by') }}">
      <input type="hidden" name="so_keyword" value="{{ request('so_keyword') }}">
      <div class="f-search">
        <svg width="12" height="12" viewBox="0 0 13 13" fill="none"><circle cx="5.5" cy="5.5" r="3.5" stroke="#9CA3AF" stroke-width="1.2"/><path d="M8.5 8.5l3 3" stroke="#9CA3AF" stroke-width="1.2" stroke-linecap="round"/></svg>
        <input type="text" name="keyword" placeholder="ค้นหา ชื่อลูกค้า / Sale" value="{{ request('keyword') }}" oninput="dbs(this.form)">
      </div>
    </form>
    @if(request('so_keyword') || request('keyword'))
      <a href="{{ route('deposit.dashboard') }}" style="font-size:12px;color:var(--rose);text-decoration:none;padding:6px 10px;border:1px solid var(--rose-soft);border-radius:var(--r6)">✕ ล้างการค้นหา</a>
    @endif
  </div>

  <!-- TABLE CARD -->
  <div class="main-card">
    <div class="card-bar">
      <div class="card-bar-left">
        <div class="card-ico">
          <svg width="14" height="14" viewBox="0 0 15 15" fill="none"><rect x="2" y="1.5" width="11" height="12" rx="2" stroke="#00897B" stroke-width="1.3"/><path d="M4.5 5h6M4.5 7.5h6M4.5 10h4" stroke="#00897B" stroke-width="1.3" stroke-linecap="round"/></svg>
        </div>
        <span class="card-ttl">รายการใบมัดจำทั้งหมด</span>
        <span class="pill">{{ $totalCount }} รายการ</span>
      </div>
      <div class="card-date">
        อัปเดตล่าสุด <strong>{{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMM YYYY HH:mm') }}</strong>
      </div>
    </div>

    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:38px;text-align:center">#</th>
            <th>ใบสั่งขาย</th>
            <th>ชื่อลูกค้า</th>
            <th>Sale</th>
            <th>วันที่</th>
            <th>ผู้เปิดบิล</th>
            <th>เวลาบันทึก</th>
            <th style="text-align:center">ประเภท</th>
            <th style="text-align:right">%</th>
            <th style="text-align:right">ยอดมัดจำ</th>
            <th style="text-align:right">ยอดคงเหลือ</th>
            <th style="text-align:center">สถานะ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($deposits as $item)
          @php
            $status = $item->status ?? 'รอยืนยัน';
            $isConfirmed = in_array($status, ['ยืนยันแล้ว', 'สำเร็จ', 'ออกบิลแล้ว']);
            $isCancelled = $status === 'ยกเลิก';

            $depType = $item->dep_type ?? '';
            $typeName = $typeLabel[$depType] ?? $depType;
            $typeCls = $typeClass[$depType] ?? '';
          @endphp
          <tr class="{{ $isConfirmed ? 'confirmed' : '' }}">
            <td class="c-idx">{{ ($deposits->currentPage() - 1) * $deposits->perPage() + $loop->iteration }}</td>

            <td>
              <a class="c-link" onclick="openDetail('{{ $item->so_id }}')">{{ $item->so_id }}</a>
            </td>

            <td class="c-wrap" title="{{ $item->customer_name }}">{{ $item->customer_name ?? '—' }}</td>
            <td class="c-wrap c-sm" title="{{ $item->sale_name }}">{{ $item->sale_name ?? '—' }}</td>
            <td class="c-sm">{{ $item->date_dep ? \Carbon\Carbon::parse($item->date_dep)->format('d/m/Y') : '—' }}</td>
            <td class="c-sm">{{ $item->emp_name ?? '—' }}</td>
            <td class="c-sm" style="white-space:nowrap">{{ $item->time ? \Carbon\Carbon::parse($item->time)->format('H:i d/m/Y') : '—' }}</td>

            <td style="text-align:center">
              @if($depType)
                <span class="badge {{ $typeCls }}" style="font-size:11px">{{ $typeName }}</span>
              @else
                <span class="c-sm">—</span>
              @endif
            </td>

            <td class="c-percent">{{ number_format((float)($item->dep_per ?? 0), 2) }}%</td>
            <td class="c-money">{{ number_format((float)($item->dep_price ?? 0), 2) }}</td>
            <td class="c-money-sub">{{ number_format((float)($item->grand_total ?? 0), 2) }}</td>

            <td style="text-align:center">
              @if($isCancelled)
                <span class="badge b-cancel"><span class="b-dot"></span>ยกเลิก</span>
              @elseif($isConfirmed)
                <span class="badge b-ok"><span class="b-dot"></span>{{ $status }}</span>
              @elseif($status === 'รอยืนยัน')
                <span class="badge b-wait"><span class="b-dot"></span>รอยืนยัน</span>
              @else
                <span class="badge b-wip"><span class="b-dot"></span>{{ $status }}</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="12">
              <div class="empty">
                <div class="empty-ico">
                  <svg width="22" height="22" viewBox="0 0 22 22" fill="none"><rect x="3" y="2" width="16" height="18" rx="2.5" stroke="#D1D5DB" stroke-width="1.5"/><path d="M7 8h8M7 11h8M7 14h5" stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round"/></svg>
                </div>
                <h4>ไม่พบข้อมูล</h4>
                <p>ยังไม่มีรายการใบมัดจำในระบบ</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($deposits->total() > 0)
    <div class="pg-bar">
      <span class="pg-info">แสดง {{ $deposits->firstItem() ?? 0 }}–{{ $deposits->lastItem() ?? 0 }} จาก {{ $deposits->total() }} รายการ</span>
      <div>{{ $deposits->appends(request()->query())->links() }}</div>
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
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 2l8 8M10 2L2 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
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
      <div class="tbl-wrap" style="margin-bottom:16px">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>ประเภท</th>
              <th style="text-align:right">%</th>
              <th style="text-align:right">ยอดมัดจำ</th>
              <th style="text-align:center">สถานะ</th>
            </tr>
          </thead>
          <tbody id="d-items">
            <tr><td colspan="5" style="text-align:center;color:var(--mist);padding:20px">กำลังโหลด...</td></tr>
          </tbody>
        </table>
      </div>

      <div class="sec-ttl">สรุปยอด</div>
      <div class="dg">
        <div class="df"><span class="dl">รวมยอดมัดจำ</span><span class="dv" id="d-total-dep" style="color:var(--teal);font-weight:600">—</span></div>
        <div class="df"><span class="dl">ยอดคงเหลือ</span><span class="dv" id="d-grand" style="color:var(--ink);font-weight:600">—</span></div>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn-ghost" onclick="closeDetail()">ปิด</button>
    </div>
  </div>
</div>

<script>
let _ck=false;
async function checkAlerts(){
  if(_ck)return;_ck=true;
  try{
    const r=await fetch('/alertsale/count',{headers:{'X-Requested-With':'XMLHttpRequest'}});
    if(!r.ok)throw 0;
    const d=await r.json();
    const b=document.getElementById('alertBadge');
    if(d.count>0){b.textContent=d.count;b.style.display='flex';}
    else b.style.display='none';
  }catch(e){}finally{_ck=false;}
}
checkAlerts();setInterval(checkAlerts,180000);

let _t;function dbs(f){clearTimeout(_t);_t=setTimeout(()=>f.submit(),600);}

const typeLabels = {product:'สินค้า', service:'บริการ', shipping:'ขนส่ง'};
const typeClasses = {product:'b-product', service:'b-service', shipping:'b-transport'};

function openDetail(soId){
  document.getElementById('modal-title').textContent='รายละเอียด '+soId;

  ['d-so','d-date','d-cust','d-contact','d-tel','d-addr','d-emp','d-sale','d-total-dep','d-grand']
    .forEach(id=>document.getElementById(id).textContent='—');

  const tb=document.getElementById('d-items');
  tb.innerHTML='<tr><td colspan="5" style="text-align:center;color:var(--mist);padding:16px">กำลังโหลด...</td></tr>';

  document.getElementById('detailModal').classList.add('open');
  document.body.style.overflow='hidden';

  fetch(`/deposit/detail/${encodeURIComponent(soId)}`,{headers:{'X-Requested-With':'XMLHttpRequest'}})
    .then(r=>r.json())
    .then(data=>{
      if(!data || !data.items || data.items.length===0){
        tb.innerHTML='<tr><td colspan="5" style="text-align:center;color:var(--mist);padding:16px">ไม่มีข้อมูล</td></tr>';
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
        const tClass = typeClasses[it.dep_type] || '';
        const tLabel = typeLabels[it.dep_type] || it.dep_type;
        const st = it.status || 'รอยืนยัน';
        let stClass = 'b-wait';
        if(st==='ยกเลิก') stClass='b-cancel';
        else if(['ยืนยันแล้ว','สำเร็จ','ออกบิลแล้ว'].includes(st)) stClass='b-ok';
        return `<tr>
          <td class="c-code">${it.id}</td>
          <td><span class="badge ${tClass}" style="font-size:11px">${tLabel}</span></td>
          <td style="text-align:right" class="c-percent">${parseFloat(it.dep_per||0).toFixed(2)}%</td>
          <td style="text-align:right" class="c-money">${parseFloat(it.dep_price||0).toLocaleString('th-TH',{minimumFractionDigits:2})}</td>
          <td style="text-align:center"><span class="badge ${stClass}" style="font-size:11px"><span class="b-dot"></span>${st}</span></td>
        </tr>`;
      }).join('');

      document.getElementById('d-total-dep').textContent = totalDep.toLocaleString('th-TH',{minimumFractionDigits:2}) + ' บาท';
      document.getElementById('d-grand').textContent     = parseFloat(first.grand_total||0).toLocaleString('th-TH',{minimumFractionDigits:2}) + ' บาท';
    })
    .catch(()=>{tb.innerHTML='<tr><td colspan="5" style="text-align:center;color:var(--rose);padding:16px">เกิดข้อผิดพลาด</td></tr>';});
}

function formatDate(d){
  const dt = new Date(d);
  if(isNaN(dt)) return d;
  return String(dt.getDate()).padStart(2,'0')+'/'+String(dt.getMonth()+1).padStart(2,'0')+'/'+dt.getFullYear();
}

function closeDetail(){document.getElementById('detailModal').classList.remove('open');document.body.style.overflow='';}
document.getElementById('detailModal').addEventListener('click',function(e){if(e.target===this)closeDetail();});
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeDetail();});
</script>
</body>
</html>