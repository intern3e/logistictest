<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ข้อมูลจัดส่ง</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&family=IBM+Plex+Sans+Thai:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --ink:#0D0F12;
  --ink2:#1E2228;
  --ink3:#2E3440;
  --steel:#4A5568;
  --ash:#6B7280;
  --mist:#9CA3AF;
  --fog:#D1D5DB;
  --haze:#E5E7EB;
  --pale:#F3F4F6;
  --snow:#F9FAFB;
  --white:#FFFFFF;

  --teal:#00897B;
  --teal-deep:#00695C;
  --teal-soft:#E0F2F1;
  --teal-mid:#4DB6AC;

  --indigo:#3949AB;
  --indigo-soft:#E8EAF6;

  --amber:#F59E0B;
  --amber-soft:#FEF3C7;

  --rose:#E53E3E;
  --rose-soft:#FEE2E2;

  --emerald:#059669;
  --emerald-soft:#D1FAE5;

  --bg:#F0F2F5;
  --surface:#FFFFFF;
  --border:#E2E5EA;
  --border2:#D1D5DB;

  --r4:4px;--r6:6px;--r8:8px;--r12:12px;--r16:16px;--r20:20px;
}

html,body{width:100%;min-height:100vh}
body{
  font-family:'IBM Plex Sans Thai','Kanit',sans-serif;
  font-size:14px;
  background:var(--bg);
  color:var(--ink);
  line-height:1.55;
}

.page{width:100%;padding:20px 22px;min-height:100vh}

.topbar{
  display:flex;align-items:center;justify-content:space-between;
  gap:14px;margin-bottom:18px;flex-wrap:wrap;
}
.topbar-brand{display:flex;align-items:center;gap:12px}
.brand-dot{
  width:36px;height:36px;border-radius:var(--r8);
  background:var(--teal);
  display:flex;align-items:center;justify-content:center;
  flex-shrink:0;
}
.brand-title{font-family:'Kanit',sans-serif;font-size:18px;font-weight:600;color:var(--ink);letter-spacing:-.3px}
.brand-sub{font-size:11px;color:var(--ash);margin-top:1px;font-weight:400}

.topbar-right{display:flex;align-items:center;gap:8px;flex-wrap:wrap}

.user-pill{
  display:inline-flex;align-items:center;gap:8px;
  padding:5px 12px 5px 6px;
  border:1px solid var(--border);
  border-radius:30px;
  background:var(--white);
  font-size:12px;color:var(--steel);
  font-weight:500;
}
.user-ava{
  width:24px;height:24px;border-radius:50%;
  background:linear-gradient(135deg,var(--teal) 0%,var(--teal-mid) 100%);
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:10px;font-weight:600;flex-shrink:0;
  font-family:'Kanit',sans-serif;
}

.notif{
  position:relative;width:36px;height:36px;border-radius:var(--r8);
  border:1px solid var(--border);background:var(--white);
  display:flex;align-items:center;justify-content:center;
  text-decoration:none;color:var(--steel);
  transition:border-color .15s,background .15s;flex-shrink:0;
}
.notif:hover{border-color:var(--teal);background:var(--teal-soft);color:var(--teal)}
.notif-badge{
  position:absolute;top:-4px;right:-4px;
  min-width:16px;height:16px;border-radius:8px;
  background:var(--rose);color:#fff;font-size:9px;font-weight:700;
  display:none;align-items:center;justify-content:center;padding:0 3px;
  border:2px solid var(--bg);
}

.btn{
  display:inline-flex;align-items:center;gap:6px;
  padding:7px 15px;border-radius:var(--r8);
  font-size:13px;font-weight:500;
  cursor:pointer;transition:all .15s;
  font-family:inherit;white-space:nowrap;
  border:1px solid transparent;text-decoration:none;
}
.btn-teal{background:var(--teal);color:#fff;border-color:var(--teal)}
.btn-teal:hover{background:var(--teal-deep);border-color:var(--teal-deep)}
.btn-dark{background:var(--ink);color:#fff;border-color:var(--ink)}
.btn-dark:hover{background:var(--ink2)}

.filter-bar{
  background:var(--white);
  border:1px solid var(--border);
  border-radius:var(--r12);
  padding:10px 16px;
  margin-bottom:16px;
  display:flex;align-items:center;gap:10px;flex-wrap:wrap;
}
.f-label{font-size:11px;font-weight:600;color:var(--mist);letter-spacing:.06em;text-transform:uppercase;white-space:nowrap}
.f-date{
  padding:6px 11px;border:1px solid var(--border);
  border-radius:var(--r6);background:var(--snow);
  color:var(--ink);font-size:13px;font-family:inherit;outline:none;
  transition:border .15s,box-shadow .15s;
}
.f-date:focus{border-color:var(--teal);background:var(--white);box-shadow:0 0 0 3px rgba(0,137,123,.1)}
.f-divider{width:1px;height:22px;background:var(--haze);flex-shrink:0}
.f-search{
  display:flex;align-items:center;gap:7px;
  padding:6px 11px;border:1px solid var(--border);
  border-radius:var(--r6);background:var(--snow);
  transition:border .15s,box-shadow .15s;
}
.f-search:focus-within{border-color:var(--teal);background:var(--white);box-shadow:0 0 0 3px rgba(0,137,123,.1)}
.f-search input{border:none;background:transparent;font-size:13px;font-family:inherit;color:var(--ink);outline:none;width:165px}
.f-search input::placeholder{color:var(--mist)}

.main-card{
  background:var(--white);
  border:1px solid var(--border);
  border-radius:var(--r16);
  overflow:hidden;
}
.card-bar{
  padding:13px 18px;
  border-bottom:1px solid var(--haze);
  display:flex;align-items:center;justify-content:space-between;
  gap:12px;flex-wrap:wrap;
  background:var(--white);
}
.card-bar-left{display:flex;align-items:center;gap:10px}
.card-ico{
  width:30px;height:30px;border-radius:var(--r6);
  background:var(--teal-soft);
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.card-ttl{font-size:14px;font-weight:600;color:var(--ink);font-family:'Kanit',sans-serif}
.pill{
  padding:2px 9px;border-radius:20px;
  background:var(--pale);
  font-size:11px;font-weight:600;color:var(--ash);
}
.card-date{font-size:12px;color:var(--ash)}
.card-date strong{color:var(--ink);font-weight:600}

.tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
table{width:100%;border-collapse:collapse;font-size:13px}
thead tr{background:var(--snow)}
th{
  padding:9px 14px;
  font-size:10.5px;font-weight:600;
  color:var(--mist);letter-spacing:.07em;
  text-transform:uppercase;
  border-bottom:1px solid var(--haze);
  text-align:left;white-space:nowrap;
}
td{
  padding:11px 14px;
  border-bottom:1px solid var(--haze);
  vertical-align:middle;
  color:var(--ink3);
}
tbody tr:last-child td{border-bottom:none}
tbody tr{transition:background .1s}
tbody tr:hover td{background:#F7FFFE}
tbody tr.delivered td{background:#F0FDF9}

.c-num{text-align:center}
.c-wrap{max-width:190px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.c-sm{font-size:12px;color:var(--ash)}
.c-code{font-family:'IBM Plex Mono','SFMono-Regular',monospace;font-size:11.5px;color:var(--mist)}
.c-link{color:var(--indigo);font-weight:600;cursor:pointer;text-decoration:none;white-space:nowrap;transition:color .12s}
.c-link:hover{color:var(--teal);text-decoration:underline}
.c-link-green{color:var(--teal);font-weight:700}
.c-idx{font-size:11px;color:var(--fog);text-align:center;font-family:'IBM Plex Mono',monospace}

.badge{
  display:inline-flex;align-items:center;gap:4px;
  padding:2px 9px;border-radius:20px;
  font-size:11px;font-weight:600;white-space:nowrap;
}
.b-dot{width:5px;height:5px;border-radius:50%;flex-shrink:0}
.b-ok{background:var(--emerald-soft);color:#065F46}.b-ok .b-dot{background:var(--emerald)}
.b-wip{background:var(--indigo-soft);color:#1A237E}.b-wip .b-dot{background:var(--indigo)}
.b-cancel{background:var(--rose-soft);color:#991B1B}.b-cancel .b-dot{background:var(--rose)}

/* ประเภทงาน badges */
.b-product{background:#E0F2F1;color:#00695C}
.b-service{background:#E8EAF6;color:#283593}
.b-transport{background:#FEF3C7;color:#92400E}
.work-types{display:flex;flex-wrap:wrap;gap:4px;align-items:center;min-width:90px}

.a-dep{
  display:inline-flex;align-items:center;gap:4px;
  padding:4px 10px;border-radius:var(--r6);
  font-size:12px;font-weight:600;
  border:1px solid #B2DFDB;background:var(--teal-soft);color:var(--teal-deep);
  text-decoration:none;transition:all .12s;white-space:nowrap;font-family:inherit;cursor:pointer;
}
.a-dep:hover{background:#B2DFDB;border-color:var(--teal)}
.a-info{
  display:inline-flex;align-items:center;gap:4px;
  padding:4px 10px;border-radius:var(--r6);
  font-size:12px;font-weight:600;
  border:1px solid #C5CAE9;background:var(--indigo-soft);color:var(--indigo);
  transition:all .12s;white-space:nowrap;font-family:inherit;cursor:pointer;
}
.a-info:hover{background:#C5CAE9}

.empty{padding:60px 20px;text-align:center}
.empty-ico{width:52px;height:52px;border-radius:var(--r12);background:var(--pale);margin:0 auto 14px;display:flex;align-items:center;justify-content:center}
.empty h4{font-size:15px;font-weight:600;margin-bottom:5px;font-family:'Kanit',sans-serif}
.empty p{font-size:12px;color:var(--ash)}

.pg-bar{
  display:flex;align-items:center;justify-content:space-between;
  padding:11px 18px;border-top:1px solid var(--haze);
  flex-wrap:wrap;gap:8px;
}
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
.modal{
  background:var(--white);border-radius:var(--r16);
  width:100%;max-width:680px;max-height:90vh;overflow-y:auto;
  border:1px solid var(--border);
  box-shadow:0 20px 60px rgba(0,0,0,.12);
}
.modal-head{
  padding:18px 22px 14px;border-bottom:1px solid var(--haze);
  display:flex;align-items:center;justify-content:space-between;
  position:sticky;top:0;background:var(--white);z-index:1;
}
.modal-head h3{font-size:15px;font-weight:600;color:var(--ink);font-family:'Kanit',sans-serif}
.modal-x{
  width:28px;height:28px;border-radius:var(--r6);
  border:1px solid var(--border);background:transparent;
  cursor:pointer;display:flex;align-items:center;justify-content:center;
  color:var(--ash);transition:all .12s;flex-shrink:0;
}
.modal-x:hover{background:var(--rose-soft);border-color:#FCA5A5;color:var(--rose)}
.modal-body{padding:18px 22px}
.sec-ttl{
  font-size:10px;font-weight:700;color:var(--mist);
  letter-spacing:.1em;text-transform:uppercase;
  margin-bottom:10px;padding-bottom:7px;
  border-bottom:1px solid var(--haze);
}
.dg{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px}
.df{display:flex;flex-direction:column;gap:3px}
.df.full{grid-column:1/-1}
.dl{font-size:10px;font-weight:700;color:var(--mist);letter-spacing:.06em;text-transform:uppercase}
.dv{font-size:13px;color:var(--ink);font-weight:500}
.notes-area{
  width:100%;padding:9px 12px;border:1px solid var(--border);
  border-radius:var(--r8);background:var(--snow);
  font-size:13px;font-family:inherit;color:var(--ink);
  resize:vertical;min-height:56px;
}
.modal-foot{padding:12px 22px;border-top:1px solid var(--haze);display:flex;justify-content:flex-end}
.btn-ghost{
  padding:7px 16px;border-radius:var(--r8);
  border:1px solid var(--border);background:transparent;
  font-size:13px;font-weight:500;color:var(--steel);
  cursor:pointer;font-family:inherit;transition:all .15s;
}
.btn-ghost:hover{border-color:var(--border2);background:var(--pale)}

@media(max-width:768px){
  .page{padding:12px}
  .f-search input{width:120px}
  th,td{padding:8px 10px;font-size:12px}
}
@media(max-width:480px){
  .f-divider{display:none}
  .f-search input{width:100px}
  .brand-sub{display:none}
}
</style>
</head>
<body>

@php
  $bill = $bill ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);

  $mockRows = collect([
    (object)['billid'=>'IV-250001','so_id'=>'SO-2025-0047','ponum'=>'PO-8841','so_detail_id'=>'REF-001','customer_name'=>'บริษัท เทค โซลูชั่น จำกัด','date_of_dali'=>'2025-04-07','emp_name'=>'สมศักดิ์ ก.','billtype'=>'ใบส่งของ','time'=>'2025-04-07 08:32:00','formtype'=>'สินค้า/บริการ','statuspdf'=>1,'statusdeli'=>1,'customer_address'=>'123 ถ.สุขุมวิท แขวงคลองเตย กรุงเทพฯ 10110','sale_name'=>'วิภา ส.','POdocument'=>'','notes'=>'ส่งด่วน แจ้งผู้รับล่วงหน้า'],
    (object)['billid'=>'IV-250002','so_id'=>'SO-2025-0046','ponum'=>'PO-8842','so_detail_id'=>'REF-002','customer_name'=>'โรงพยาบาล เมดิคอล พลัส','date_of_dali'=>'2025-04-07','emp_name'=>'วิภา ส.','billtype'=>'ใบกำกับภาษี','time'=>'2025-04-07 09:15:00','formtype'=>'บริการ','statuspdf'=>0,'statusdeli'=>0,'customer_address'=>'1 ถ.เพชรบุรีตัดใหม่ ราชเทวี กรุงเทพฯ 10400','sale_name'=>'สมศักดิ์ ก.','POdocument'=>'','notes'=>''],
    (object)['billid'=>'IV-250003','so_id'=>'SO-2025-0045','ponum'=>'PO-8843','so_detail_id'=>'REF-003','customer_name'=>'สำนักงานกฎหมาย เอเชีย พาร์ทเนอร์','date_of_dali'=>'2025-04-07','emp_name'=>'อรุณ ม.','billtype'=>'ใบส่งของ','time'=>'2025-04-07 10:05:00','formtype'=>'สินค้า/ขนส่ง','statuspdf'=>1,'statusdeli'=>1,'customer_address'=>'Floor 12, 191 ถ.สีลม บางรัก กรุงเทพฯ 10500','sale_name'=>'วิภา ส.','POdocument'=>'','notes'=>'ลูกค้า VIP'],
    (object)['billid'=>'IV-250004','so_id'=>'SO-2025-0044','ponum'=>'PO-8844','so_detail_id'=>'REF-004','customer_name'=>'บริษัท รีเทล โปร จำกัด','date_of_dali'=>'2025-04-07','emp_name'=>'สมศักดิ์ ก.','billtype'=>'ใบกำกับภาษี','time'=>'2025-04-07 11:30:00','formtype'=>'ขนส่ง','statuspdf'=>6,'statusdeli'=>0,'customer_address'=>'200 ถ.นวมินทร์ บึงกุ่ม กรุงเทพฯ 10240','sale_name'=>'อรุณ ม.','POdocument'=>'','notes'=>'ยกเลิกตามคำขอลูกค้า'],
    (object)['billid'=>'IV-250005','so_id'=>'SO-2025-0043','ponum'=>'PO-8845','so_detail_id'=>'REF-005','customer_name'=>'มหาวิทยาลัย เทคโนโลยี ราชมงคล','date_of_dali'=>'2025-04-07','emp_name'=>'วิภา ส.','billtype'=>'ใบส่งของ','time'=>'2025-04-07 14:20:00','formtype'=>'สินค้า/บริการ/ขนส่ง','statuspdf'=>1,'statusdeli'=>0,'customer_address'=>'9 ถ.พระนคร พระนคร กรุงเทพฯ 10200','sale_name'=>'สมศักดิ์ ก.','POdocument'=>'','notes'=>'รอลายเซ็นอนุมัติ'],
  ]);

  $displayRows = $bill->isEmpty() ? $mockRows : $bill;
  $totalCount  = $bill->isEmpty() ? $mockRows->count() : $bill->total();

  $typeMap = [
    'สินค้า'  => 'b-product',
    'บริการ'  => 'b-service',
    'ขนส่ง'   => 'b-transport',
  ];
@endphp

<div class="page">

  <!-- TOPBAR -->
  <div class="topbar">
    <div class="topbar-brand">
      <div class="brand-dot">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
          <rect x="2" y="5" width="11" height="9" rx="1.5" stroke="white" stroke-width="1.4"/>
          <path d="M13 8l3 2v4h-3" stroke="white" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="4.5" cy="15" r="1.2" stroke="white" stroke-width="1.2"/>
          <circle cx="11.5" cy="15" r="1.2" stroke="white" stroke-width="1.2"/>
        </svg>
      </div>
      <div>
        <div class="brand-title">ข้อมูลจัดส่ง</div>
        <div class="brand-sub">รายการบิลและสถานะการจัดส่งประจำวัน</div>
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
    <svg width="13" height="13" viewBox="0 0 14 14" fill="none"><rect x="1.5" y="1.5" width="11" height="11" rx="2" stroke="#9CA3AF" stroke-width="1.2"/><path d="M4.5 5h5M4.5 7h5M4.5 9h3" stroke="#9CA3AF" stroke-width="1.2" stroke-linecap="round"/></svg>
    <span class="f-label">วันที่</span>
    <form method="GET" action="{{ route('sale.dashboard') }}" id="dateForm">
      <input type="hidden" name="create_by" value="{{ request('create_by') }}">
      <input type="hidden" name="so_keyword" value="{{ request('so_keyword') }}">
      <input type="hidden" name="keyword" value="{{ request('keyword') }}">
      <input type="date" name="date" id="dateInput" class="f-date"
        value="{{ request('date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
    </form>
    <div class="f-divider"></div>
    <form method="GET" action="{{ route('sale.dashboard') }}">
      <input type="hidden" name="create_by" value="{{ request('create_by') }}">
      <input type="hidden" name="date" value="{{ request('date') }}">
      <input type="hidden" name="keyword" value="{{ request('keyword') }}">
      <div class="f-search">
        <svg width="12" height="12" viewBox="0 0 13 13" fill="none"><circle cx="5.5" cy="5.5" r="3.5" stroke="#9CA3AF" stroke-width="1.2"/><path d="M8.5 8.5l3 3" stroke="#9CA3AF" stroke-width="1.2" stroke-linecap="round"/></svg>
        <input type="text" name="so_keyword" placeholder="ค้นหา ใบสั่งขาย" value="{{ request('so_keyword') }}" oninput="dbs(this.form)">
      </div>
    </form>
    <form method="GET" action="{{ route('sale.dashboard') }}">
      <input type="hidden" name="create_by" value="{{ request('create_by') }}">
      <input type="hidden" name="date" value="{{ request('date') }}">
      <input type="hidden" name="so_keyword" value="{{ request('so_keyword') }}">
      <div class="f-search">
        <svg width="12" height="12" viewBox="0 0 13 13" fill="none"><circle cx="5.5" cy="5.5" r="3.5" stroke="#9CA3AF" stroke-width="1.2"/><path d="M8.5 8.5l3 3" stroke="#9CA3AF" stroke-width="1.2" stroke-linecap="round"/></svg>
        <input type="text" name="keyword" placeholder="ค้นหา เลขที่บิล" value="{{ request('keyword') }}" oninput="dbs(this.form)">
      </div>
    </form>
  </div>

  <!-- TABLE CARD -->
  <div class="main-card">
    <div class="card-bar">
      <div class="card-bar-left">
        <div class="card-ico">
          <svg width="14" height="14" viewBox="0 0 15 15" fill="none"><rect x="2" y="1.5" width="11" height="12" rx="2" stroke="#00897B" stroke-width="1.3"/><path d="M4.5 5h6M4.5 7.5h6M4.5 10h4" stroke="#00897B" stroke-width="1.3" stroke-linecap="round"/></svg>
        </div>
        <span class="card-ttl">รายการบิล</span>
        <span class="pill">{{ $totalCount }} รายการ</span>
      </div>
      <div class="card-date">
        วันที่ <strong>{{ \Carbon\Carbon::parse(request('date', today()))->locale('th')->isoFormat('D MMM YYYY') }}</strong>
      </div>
    </div>

    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:38px;text-align:center">#</th>
            <th>อ้างอิงใบส่งของ</th>
            <th>อ้างอิงใบสั่งขาย</th>
            <th>REF</th>
            <th>ชื่อลูกค้า</th>
            <th>วันที่จัดส่ง</th>
            <th>ผู้เปิดบิล</th>
            <th>เวลาออกบิล</th>
            <th>ประเภทงาน</th>
            <th style="text-align:center">สถานะ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($displayRows as $item)
          @php
            $hasPdf      = isset($item->billid) && \Illuminate\Support\Facades\Storage::disk('public')->exists("doc_document/{$item->billid}.pdf");
            $isDelivered = isset($item->statusdeli) && $item->statusdeli == 1;

            $rawTypes = $item->formtype ?? '';
            $types = is_array($rawTypes)
              ? $rawTypes
              : array_filter(array_map('trim', explode('/', $rawTypes)));
          @endphp
          <tr class="{{ $isDelivered ? 'delivered' : '' }}">
            <td class="c-idx">{{ $loop->iteration }}</td>

            {{-- บิล ID --}}
            <td>
              @if($hasPdf)
                <a class="c-link" onclick="mergeAndOpenPdfs('{{ $item->billid }}')">{{ $item->billid }}</a>
              @elseif($isDelivered)
                <a class="c-link c-link-green"
                   href="https://drive.google.com/drive/u/0/search?q={{ $item->billid }}+parent:1WyDB1b01cDQ53Ap7B03UIGFbL6a2Y6WB"
                   target="_blank">{{ $item->billid }}</a>
              @else
                <span class="c-code">{{ $item->billid }}</span>
              @endif
            </td>

            <td><a class="c-link" onclick="openPopup3E('{{ $item->so_id }}')">{{ $item->so_id }}</a></td>
            <td style="font-size:11px;color:var(--fog);max-width:88px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $item->so_detail_id }}">{{ $item->so_detail_id }}</td>
            <td class="c-wrap" title="{{ $item->customer_name }}">{{ $item->customer_name }}</td>
            <td class="c-sm">{{ \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') }}</td>
            <td class="c-sm">{{ $item->emp_name }}</td>
            <td class="c-sm" style="white-space:nowrap">{{ \Carbon\Carbon::parse($item->time)->format('H:i d/m/Y') }}</td>

            {{-- ประเภทงาน (หลายประเภท) --}}
            <td>
              <div class="work-types">
                @forelse($types as $t)
                  @php $cls = $typeMap[$t] ?? ''; @endphp
                  <span class="badge {{ $cls }}" style="font-size:11px">{{ $t }}</span>
                @empty
                  <span class="c-sm">—</span>
                @endforelse
              </div>
            </td>

            {{-- สถานะ --}}
            <td style="text-align:center">
              @if(isset($item->statuspdf) && $item->statuspdf == 6)
                <span class="badge b-cancel"><span class="b-dot"></span>ยกเลิก</span>
              @elseif(!isset($item->statuspdf) || $item->statuspdf == 0)
                <span class="badge b-wip"><span class="b-dot"></span>กำลังดำเนินการ</span>
              @else
                <span class="badge b-ok"><span class="b-dot"></span>ปริ้นสำเร็จ</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="10">
              <div class="empty">
                <div class="empty-ico">
                  <svg width="22" height="22" viewBox="0 0 22 22" fill="none"><rect x="3" y="2" width="16" height="18" rx="2.5" stroke="#D1D5DB" stroke-width="1.5"/><path d="M7 8h8M7 11h8M7 14h5" stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round"/></svg>
                </div>
                <h4>ไม่พบข้อมูล</h4>
                <p>ไม่มีรายการบิลสำหรับวันที่นี้</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if(isset($message))
      <div style="text-align:center;padding:14px;font-size:12px;color:var(--ash)">{{ $message }}</div>
    @endif

    @if(!$bill->isEmpty())
    <div class="pg-bar">
      <span class="pg-info">แสดง {{ $bill->firstItem() ?? 0 }}–{{ $bill->lastItem() ?? 0 }} จาก {{ $bill->total() ?? 0 }} รายการ</span>
      <div>{{ $bill->appends(request()->query())->links() }}</div>
    </div>
    @endif
  </div>

</div>

<!-- MODAL -->
<div class="modal-bg" id="detailModal">
  <div class="modal">
    <div class="modal-head">
      <h3 id="modal-title">รายละเอียดบิล</h3>
      <button class="modal-x" onclick="closeDetail()">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 2l8 8M10 2L2 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="sec-ttl">ข้อมูลทั่วไป</div>
      <div class="dg">
        <div class="df"><span class="dl">REF</span><span class="dv" id="d-ref">—</span></div>
        <div class="df"><span class="dl">ใบสั่งขาย</span><span class="dv" id="d-so">—</span></div>
        <div class="df"><span class="dl">ใบสั่งซื้อ</span><span class="dv" id="d-po">—</span></div>
        <div class="df"><span class="dl">วันที่จัดส่ง</span><span class="dv" id="d-date">—</span></div>
        <div class="df full"><span class="dl">ชื่อลูกค้า</span><span class="dv" id="d-cust">—</span></div>
        <div class="df full"><span class="dl">ที่อยู่จัดส่ง</span><span class="dv" id="d-addr">—</span></div>
        <div class="df"><span class="dl">ผู้เปิดบิล</span><span class="dv" id="d-sale">—</span></div>
        <div class="df"><span class="dl">เอกสาร PO</span><span class="dv"><a id="d-po-link" href="#" target="_blank" style="color:var(--indigo)">ดูไฟล์</a></span></div>
      </div>

      <div class="sec-ttl">รายการสินค้า</div>
      <div class="tbl-wrap" style="margin-bottom:16px">
        <table>
          <thead>
            <tr>
              <th>รหัสสินค้า</th><th>รายการ</th>
              <th style="text-align:center">จำนวน</th>
              <th style="text-align:right">ราคา/หน่วย</th>
            </tr>
          </thead>
          <tbody id="d-items">
            <tr><td colspan="4" style="text-align:center;color:var(--mist);padding:20px">กำลังโหลด...</td></tr>
          </tbody>
        </table>
      </div>

      <div class="sec-ttl">หมายเหตุ</div>
      <textarea class="notes-area" id="d-notes" readonly></textarea>
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
  try{const r=await fetch('/alertsale/count',{headers:{'X-Requested-With':'XMLHttpRequest'}});if(!r.ok)throw 0;const d=await r.json();const b=document.getElementById('alertBadge');if(d.count>0){b.textContent=d.count;b.style.display='flex';}else b.style.display='none';}catch(e){}finally{_ck=false;}
}
checkAlerts();setInterval(checkAlerts,180000);

document.getElementById('dateInput').addEventListener('change',()=>document.getElementById('dateForm').submit());
window.addEventListener('load',()=>{if(!sessionStorage.getItem('_a')){sessionStorage.setItem('_a','1');document.getElementById('dateForm').submit();}});

let _t;function dbs(f){clearTimeout(_t);_t=setTimeout(()=>f.submit(),600);}

function mergeAndOpenPdfs(id){window.open("{{ asset('storage/doc_document') }}/"+id+".pdf","_blank");fetch("{{ route('merge.pdf') }}",{method:"POST",headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({billid:id})}).then(r=>r.json()).then(d=>{if(!d.success)alert("❌ "+(d.message||"เกิดข้อผิดพลาด"));});return false;}
function openBillOnly(id){window.open("{{ asset('storage/bill_document') }}/"+id+".pdf","_blank");fetch("{{ route('merge.pdf') }}",{method:"POST",headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({billid:id})}).then(r=>r.json()).then(d=>{if(!d.success)alert("❌ "+(d.message||"เกิดข้อผิดพลาด"));});return false;}

function openPopup3E(s){const url=`http://server-3e/3e/store_report.php?so=${encodeURIComponent(s)}&po=&search=Search&rowPerPage=25&currentPage=0`;const w=1100,h=500,l=Math.max((window.screenLeft??0)+window.innerWidth-w-16,0),t=Math.max((window.screenTop??0)+window.innerHeight-h-16,0);const win=window.open(url,'_blank',`toolbar=no,location=no,scrollbars=yes,resizable=yes,width=${w},height=${h},top=${t},left=${l},noopener=yes`);if(!win)window.open(url,'_blank','noopener');return false;}

function openDetail(ref,so,po,cust,addr,date,sale,podoc,notes){
  document.getElementById('modal-title').textContent='รายละเอียด '+ref;
  ['d-ref','d-so','d-po','d-date','d-cust','d-addr','d-sale'].forEach((id,i)=>{
    document.getElementById(id).textContent=[ref,so,po,date,cust,addr,sale][i]||'—';
  });
  document.getElementById('d-notes').value=notes||'';
  const pl=document.getElementById('d-po-link');
  if(podoc){pl.href='/storage/po_documents/'+podoc;pl.style.display='';}else pl.style.display='none';
  const tb=document.getElementById('d-items');
  tb.innerHTML='<tr><td colspan="4" style="text-align:center;color:var(--mist);padding:16px">กำลังโหลด...</td></tr>';
  fetch(`/get-bill-detail/${ref}`)
    .then(r=>r.json())
    .then(data=>{
      tb.innerHTML=data.length
        ?data.map(it=>`<tr><td class="c-code">${it.item_id}</td><td>${it.item_name}</td><td style="text-align:center">${it.quantity}</td><td style="text-align:right">${parseFloat(it.unit_price||0).toLocaleString('th-TH',{minimumFractionDigits:2})}</td></tr>`).join('')
        :'<tr><td colspan="4" style="text-align:center;color:var(--mist);padding:16px">ไม่มีข้อมูล</td></tr>';
    })
    .catch(()=>{tb.innerHTML='<tr><td colspan="4" style="text-align:center;color:var(--rose);padding:16px">เกิดข้อผิดพลาด</td></tr>';});
  document.getElementById('detailModal').classList.add('open');
  document.body.style.overflow='hidden';
}
function closeDetail(){document.getElementById('detailModal').classList.remove('open');document.body.style.overflow='';}
document.getElementById('detailModal').addEventListener('click',function(e){if(e.target===this)closeDetail();});
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeDetail();});
</script>
</body>
</html>