<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ระบบปริ้นเอกสารของบิล SO</title>
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

  --orange:#EA580C;
  --orange-soft:#FFF7ED;

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

/* ═══ PAGE ═══ */
.page{width:100%;padding:20px 22px;min-height:100vh}

/* ═══ TOPBAR ═══ */
.topbar{
  display:flex;align-items:center;justify-content:space-between;
  gap:14px;margin-bottom:18px;flex-wrap:wrap;
}
.topbar-brand{display:flex;align-items:center;gap:12px}
.brand-dot{
  width:36px;height:36px;border-radius:var(--r8);
  background:var(--teal);
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.brand-title{font-family:'Kanit',sans-serif;font-size:18px;font-weight:600;color:var(--ink);letter-spacing:-.3px}
.brand-sub{font-size:11px;color:var(--ash);margin-top:1px;font-weight:400}
.topbar-right{display:flex;align-items:center;gap:8px;flex-wrap:wrap}

/* ═══ BUTTONS ═══ */
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
.btn-rose{background:var(--rose);color:#fff;border-color:var(--rose)}
.btn-rose:hover{background:#C53030}
.btn-amber{background:var(--amber);color:#fff;border-color:var(--amber)}
.btn-amber:hover{background:#D97706}
.btn-indigo{background:var(--indigo);color:#fff;border-color:var(--indigo)}
.btn-indigo:hover{background:#283593}
.btn-orange{background:var(--orange);color:#fff;border-color:var(--orange)}
.btn-orange:hover{background:#C2410C}
.btn-emerald{background:var(--emerald);color:#fff;border-color:var(--emerald)}
.btn-emerald:hover{background:#047857}
.btn-sm{padding:4px 10px;font-size:11px;border-radius:var(--r6)}
.btn-ghost{
  padding:7px 16px;border-radius:var(--r8);
  border:1px solid var(--border);background:transparent;
  font-size:13px;font-weight:500;color:var(--steel);
  cursor:pointer;font-family:inherit;transition:all .15s;
}
.btn-ghost:hover{border-color:var(--border2);background:var(--pale)}

/* ═══ ACTION BAR ═══ */
.action-bar{
  background:var(--white);
  border:1px solid var(--border);
  border-radius:var(--r12);
  padding:10px 16px;
  margin-bottom:16px;
  display:flex;align-items:center;gap:10px;flex-wrap:wrap;
}
.f-divider{width:1px;height:22px;background:var(--haze);flex-shrink:0}
.f-search{
  display:flex;align-items:center;gap:7px;
  padding:6px 11px;border:1px solid var(--border);
  border-radius:var(--r6);background:var(--snow);
  transition:border .15s,box-shadow .15s;
}
.f-search:focus-within{border-color:var(--teal);background:var(--white);box-shadow:0 0 0 3px rgba(0,137,123,.1)}
.f-search input{border:none;background:transparent;font-size:13px;font-family:inherit;color:var(--ink);outline:none;width:180px}
.f-search input::placeholder{color:var(--mist)}

/* ═══ MAIN CARD ═══ */
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

/* ═══ TABLE ═══ */
.tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
table{width:100%;border-collapse:collapse;font-size:12px}
thead tr{background:var(--snow)}
th{
  padding:9px 10px;
  font-size:10px;font-weight:600;
  color:var(--mist);letter-spacing:.07em;
  text-transform:uppercase;
  border-bottom:1px solid var(--haze);
  text-align:left;white-space:nowrap;
}
td{
  padding:10px 10px;
  border-bottom:1px solid var(--haze);
  vertical-align:middle;
  color:var(--ink3);
}
tbody tr:last-child td{border-bottom:none}
tbody tr{transition:background .1s}
tbody tr:hover td{background:#F7FFFE}

.c-idx{font-size:11px;color:var(--fog);text-align:center;font-family:'IBM Plex Mono',monospace}
.c-code{font-family:'IBM Plex Mono','SFMono-Regular',monospace;font-size:11px;color:var(--mist)}
.c-sm{font-size:11px;color:var(--ash)}
.c-center{text-align:center}
.c-wrap{max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}

/* ═══ CHECKBOX ═══ */
.chk{
  width:16px;height:16px;cursor:pointer;
  accent-color:var(--teal);
}

/* ═══ FORM TYPE BADGES ═══ */
.badge{
  display:inline-flex;align-items:center;gap:4px;
  padding:2px 8px;border-radius:20px;
  font-size:10px;font-weight:600;white-space:nowrap;
}
.ft-red{background:#FEE2E2;color:#991B1B}
.ft-green{background:#D1FAE5;color:#065F46}
.ft-blue{background:#DBEAFE;color:#1E40AF}
.ft-purple{background:#EDE9FE;color:#5B21B6}
.ft-yellow{background:#FEF9C3;color:#713F12}
.ft-default{background:var(--pale);color:var(--ash)}
.b-product{background:#E0F2F1;color:#00695C}
.b-service{background:#E8EAF6;color:#283593}
.b-transport{background:#FEF3C7;color:#92400E}

.btn-copy{
  display:inline-flex;align-items:center;
  padding:3px 8px;border-radius:var(--r4);
  border:1px solid var(--border);background:var(--pale);
  color:var(--ash);cursor:pointer;transition:all .12s;
  flex-shrink:0;font-family:inherit;font-size:10px;font-weight:600;
  white-space:nowrap;
}
.btn-copy:hover{border-color:var(--teal);background:var(--teal-soft);color:var(--teal)}
.btn-copy.copied{border-color:var(--emerald);background:var(--emerald-soft);color:var(--emerald)}

/* ═══ INLINE ACTIONS CELL ═══ */
.cell-actions{display:flex;flex-direction:column;gap:5px;min-width:130px}
.cell-row{display:flex;gap:4px;flex-wrap:wrap;align-items:center}
.inp-sm{
  width:90px;height:26px;
  padding:2px 7px;
  border:1px solid var(--border);border-radius:var(--r6);
  font-size:11px;font-family:inherit;color:var(--ink);
  background:var(--snow);outline:none;
  transition:border .15s;
}
.inp-sm:focus{border-color:var(--teal);background:var(--white)}
.inp-sm[readonly]{background:var(--pale);color:var(--ash)}
.file-inp{font-size:10px;color:var(--ash);max-width:120px}

/* ═══ EMPTY STATE ═══ */
.empty{padding:60px 20px;text-align:center}
.empty-ico{width:52px;height:52px;border-radius:var(--r12);background:var(--pale);margin:0 auto 14px;display:flex;align-items:center;justify-content:center}
.empty h4{font-size:15px;font-weight:600;margin-bottom:5px;font-family:'Kanit',sans-serif}
.empty p{font-size:12px;color:var(--ash)}

/* ═══ MODAL ═══ */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(13,15,18,.55);z-index:1000;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(2px)}
.modal-bg.open{display:flex}
.modal{
  background:var(--white);border-radius:var(--r16);
  width:100%;max-width:900px;max-height:90vh;overflow-y:auto;
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
.modal-foot{padding:12px 22px;border-top:1px solid var(--haze);display:flex;justify-content:flex-end}

/* ═══ RESPONSIVE ═══ */
@media(max-width:768px){
  .page{padding:12px}
  th,td{padding:7px 8px}
  .f-search input{width:130px}
}
@media(max-width:480px){
  .brand-sub{display:none}
  .f-divider{display:none}
}
</style>
</head>
<body>
<div class="page">

  <!-- ═══ TOPBAR ═══ -->
  <div class="topbar">
    <div class="topbar-brand">
      <div class="brand-dot">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
          <rect x="2" y="3" width="14" height="12" rx="1.5" stroke="white" stroke-width="1.4"/>
          <path d="M5 7h8M5 10h5" stroke="white" stroke-width="1.4" stroke-linecap="round"/>
          <circle cx="13" cy="10" r="2" fill="white"/>
        </svg>
      </div>
      <div>
        <div class="brand-title">ระบบปริ้นเอกสาร SO</div>
        <div class="brand-sub">จัดการบิลและเอกสารการขาย</div>
      </div>
    </div>
    <div class="topbar-right">
      <a href="http://server_update:8000/solist" class="btn btn-dark">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M2 6.5L6.5 2 11 6.5V11.5H8V8.5H5V11.5H2V6.5Z" stroke="white" stroke-width="1.3" stroke-linejoin="round"/></svg>
        หน้าหลัก
      </a>
    </div>
  </div>

  <!-- ═══ ACTION BAR ═══ -->
  <div class="action-bar">
    <button class="btn btn-teal" onclick="updateStatuspdf()">
      <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M2 7l3.5 3.5L11 3" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      ปริ้นเอกสาร SO
    </button>
    <a href="history" class="btn btn-rose">
      <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><rect x="2" y="2" width="9" height="9" rx="1.5" stroke="white" stroke-width="1.3"/><path d="M5 2v9M8 2v9M2 5h9M2 8h9" stroke="white" stroke-width="1" stroke-linecap="round"/></svg>
      เปลี่ยนวันที่
    </a>
    <div class="f-divider"></div>
    <div class="f-search">
      <svg width="12" height="12" viewBox="0 0 13 13" fill="none"><circle cx="5.5" cy="5.5" r="3.5" stroke="#9CA3AF" stroke-width="1.2"/><path d="M8.5 8.5l3 3" stroke="#9CA3AF" stroke-width="1.2" stroke-linecap="round"/></svg>
      <input type="text" id="search-input" placeholder="ค้นหา เลขที่บิล" onkeyup="searchTable()">
    </div>
  </div>

  <!-- ═══ TABLE CARD ═══ -->
  <div class="main-card">
    <div class="card-bar">
      <div class="card-bar-left">
        <div class="card-ico">
          <svg width="14" height="14" viewBox="0 0 15 15" fill="none"><rect x="2" y="1.5" width="11" height="12" rx="2" stroke="#00897B" stroke-width="1.3"/><path d="M4.5 5h6M4.5 7.5h6M4.5 10h4" stroke="#00897B" stroke-width="1.3" stroke-linecap="round"/></svg>
        </div>
        <span class="card-ttl">รายการบิลรอปริ้น</span>
      </div>
      <label style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--ash);cursor:pointer">
        <input type="checkbox" id="checkAll" class="chk" onclick="toggleCheckboxes()">
        เลือกทั้งหมด
      </label>
    </div>

    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:36px;text-align:center">
              <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><rect x="1.5" y="1.5" width="9" height="9" rx="1.5" stroke="#9CA3AF" stroke-width="1.2"/><path d="M3.5 6l2 2 3-3" stroke="#9CA3AF" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </th>
            <th>เลขที่บิล</th>
            <th>ใบสั่งขาย</th>
            <th>รหัสลูกค้า</th>
            <th>วันที่จัดส่ง</th>
            <th>ผู้เปิดบิล</th>
            <th>ประเภทงาน</th>
            <th>Gmail</th>
          </tr>
        </thead>
        <tbody id="table-body">
          @php
            $bill = $bill ?? collect([]);

            $mockRows = collect([
              (object)[
                'id'=>1,'so_detail_id'=>'REF-2025-001','so_id'=>'SO-2025-0101',
                'ponum'=>'PO-9901','billid'=>'IV-250101',
                'formtype'=>'บิล/PO3','billtype'=>'สินค้า/บริการ',
                'customer_name'=>'บริษัท เทค โซลูชั่น จำกัด',
                'customer_tel'=>'02-111-2222','customer_id'=>'C-00001',
                'customer_email'=>'techsolution@gmail.com',
                'customer_address'=>'123 ถ.สุขุมวิท แขวงคลองเตย กรุงเทพฯ 10110',
                'date_of_dali'=>'2025-04-07','sale_name'=>'วิภา ส.',
                'emp_name'=>'สมศักดิ์ ก.','statuspdf'=>0,'statusdeli'=>0,
                'POdocument'=>null,'notes'=>null,'bill_issue_no'=>null,
              ],
              (object)[
                'id'=>2,'so_detail_id'=>'REF-2025-002','so_id'=>'SO-2025-0102',
                'ponum'=>'PO-9902','billid'=>'IV-250102',
                'formtype'=>'บิล/PO3/วางบิล','billtype'=>'บริการ',
                'customer_name'=>'โรงพยาบาล เมดิคอล พลัส',
                'customer_tel'=>'02-333-4444','customer_id'=>'C-00002',
                'customer_email'=>null,
                'customer_address'=>'1 ถ.เพชรบุรีตัดใหม่ ราชเทวี กรุงเทพฯ 10400',
                'date_of_dali'=>'2025-04-07','sale_name'=>'อรุณ ม.',
                'emp_name'=>'วิภา ส.','statuspdf'=>0,'statusdeli'=>0,
                'POdocument'=>'po_sample.pdf','notes'=>'ส่งด่วน',
                'bill_issue_no'=>'BI-2025-001',
              ],
              (object)[
                'id'=>3,'so_detail_id'=>'REF-2025-003','so_id'=>'SO-2025-0103',
                'ponum'=>'PO-9903','billid'=>'IV-250103',
                'formtype'=>'บิล/PO3/สำเนาหน้าบิล2','billtype'=>'สินค้า/ขนส่ง',
                'customer_name'=>'สำนักงานกฎหมาย เอเชีย พาร์ทเนอร์',
                'customer_tel'=>'02-555-6666','customer_id'=>'C-00003',
                'customer_email'=>'asiapartner@gmail.com',
                'customer_address'=>'Floor 12, 191 ถ.สีลม บางรัก กรุงเทพฯ 10500',
                'date_of_dali'=>'2025-04-07','sale_name'=>'สมศักดิ์ ก.',
                'emp_name'=>'อรุณ ม.','statuspdf'=>0,'statusdeli'=>0,
                'POdocument'=>null,'notes'=>'ลูกค้า VIP','bill_issue_no'=>null,
              ],
              (object)[
                'id'=>4,'so_detail_id'=>'REF-2025-004','so_id'=>'SO-2025-0104',
                'ponum'=>'PO-9904','billid'=>'IV-250104',
                'formtype'=>'บิล/PO3/บัญชี','billtype'=>'ขนส่ง',
                'customer_name'=>'บริษัท รีเทล โปร จำกัด',
                'customer_tel'=>'02-777-8888','customer_id'=>'C-00004',
                'customer_email'=>null,
                'customer_address'=>'200 ถ.นวมินทร์ บึงกุ่ม กรุงเทพฯ 10240',
                'date_of_dali'=>'2025-04-07','sale_name'=>'วิภา ส.',
                'emp_name'=>'สมศักดิ์ ก.','statuspdf'=>0,'statusdeli'=>0,
                'POdocument'=>'po_sample2.pdf','notes'=>null,
                'bill_issue_no'=>'BI-2025-002',
              ],
              (object)[
                'id'=>5,'so_detail_id'=>'REF-2025-005','so_id'=>'SO-2025-0105',
                'ponum'=>'PO-9905','billid'=>'IV-250105',
                'formtype'=>'บิล/PO3/วางบิล/สำเนาหน้าบิล2','billtype'=>'สินค้า/บริการ/ขนส่ง',
                'customer_name'=>'มหาวิทยาลัย เทคโนโลยี ราชมงคล',
                'customer_tel'=>'02-999-0000','customer_id'=>'C-00005',
                'customer_email'=>'rajamangala@gmail.com',
                'customer_address'=>'9 ถ.พระนคร พระนคร กรุงเทพฯ 10200',
                'date_of_dali'=>'2025-04-07','sale_name'=>'อรุณ ม.',
                'emp_name'=>'วิภา ส.','statuspdf'=>0,'statusdeli'=>0,
                'POdocument'=>null,'notes'=>null,'bill_issue_no'=>null,
              ],
            ]);

            $displayRows = $bill->isEmpty() ? $mockRows : $bill;
          @endphp
          @foreach($displayRows->sortBy('so_detail_id') as $item)
            @if($item->statuspdf == 0)
            @php
              $dateObj   = \Carbon\Carbon::parse($item->date_of_dali);
              $formatted = $dateObj->format('d/m/') . ($dateObj->year + 543);

              $typeMap = [
                'สินค้า'   => 'b-product',
                'บริการ'   => 'b-service',
                'ขนส่ง'    => 'b-transport',
              ];
              $rawTypes = $item->billtype ?? '';
              $types = is_array($rawTypes)
                ? $rawTypes
                : array_filter(array_map('trim', explode('/', $rawTypes)));
            @endphp
            <tr>
              <td class="c-center">
                <input type="checkbox" class="chk form-control1"
                  name="statupdf[]"
                  value="{{ $item->so_id }}"
                  id="checkbox_{{ $item->so_detail_id }}">
              </td>

              <td>
                <span class="c-code" style="font-size:12px;color:var(--ink3);font-weight:600">{{ $item->so_detail_id }}</span>
              </td>

              {{-- ใบสั่งขาย + ปุ่มคัดลอก --}}
              <td>
                <div style="display:flex;align-items:center;gap:5px;white-space:nowrap">
                  <span class="c-code" style="font-size:11px">{{ $item->so_id }}</span>
                  <button class="btn-copy"
                    onclick="cpText('{{ $item->so_id }}',this)">คัดลอก</button>
                </div>
              </td>

              {{-- รหัสลูกค้า --}}
              <td>
                @if(!empty($item->customer_id))
                  <div style="display:flex;align-items:center;gap:5px;white-space:nowrap">
                    <span class="c-code" style="font-size:11px;color:var(--ink3)">{{ $item->customer_id }}</span>
                    <button class="btn-copy" onclick="cpText('{{ $item->customer_id }}', this)">คัดลอก</button>
                  </div>
                @else
                  <span class="c-code" style="font-size:11px;color:var(--fog)">—</span>
                @endif
              </td>

              <td class="c-sm" style="white-space:nowrap">{{ $formatted }}</td>

              <td class="c-sm">{{ $item->emp_name }}</td>

              {{-- ประเภทงาน + ปุ่มคัดลอก + ช่องกรอกเลข + บันทึก --}}
              <td>
                <div style="display:flex;flex-direction:column;gap:5px">
                  <div style="display:flex;flex-wrap:wrap;gap:4px;align-items:center">
                    @forelse($types as $t)
                      <span class="badge {{ $typeMap[$t] ?? 'ft-default' }}" style="font-size:10px">{{ $t }}</span>
                    @empty
                      <span class="c-sm">—</span>
                    @endforelse
                    @if(count($types) > 0)
                      <button class="btn-copy"
                        onclick="cpText('{{ implode('/', $types) }}',this)">คัดลอก</button>
                    @endif
                  </div>
                  <div style="display:flex;align-items:center;gap:4px">
                    <input type="text" class="inp-sm"
                      id="typeinput_{{ $item->so_detail_id }}"
                      placeholder="กรอกเลข..."
                      style="width:100px">
                    <button class="btn btn-teal btn-sm"
                      onclick="saveTypeInput('{{ $item->so_detail_id }}',this)">
                      บันทึก
                    </button>
                  </div>
                </div>
              </td>

              {{-- ✅ Gmail แสดงตรงๆ + ปุ่มคัดลอก (ไม่พึ่ง JS render) --}}
              <td>
                @if(!empty($item->customer_email))
                  @php $email = $item->customer_email; @endphp
                  <div style="display:flex;align-items:center;gap:5px;flex-wrap:nowrap">
                    <svg width="12" height="12" viewBox="0 0 14 14" fill="none" style="flex-shrink:0">
                      <rect x="1.5" y="3" width="11" height="8" rx="1.5" stroke="#00897B" stroke-width="1.2"/>
                      <path d="M1.5 4.5L7 8l5.5-3.5" stroke="#00897B" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                    <span style="font-size:11px;color:var(--teal);font-weight:500">{{ $email }}</span>
                    <button class="btn-copy" onclick="cpText('{{ $email }}', this)">คัดลอก</button>
                  </div>
                @else
                  <span style="font-size:11px;color:var(--fog)">—</span>
                @endif
              </td>

            </tr>
            @endif
          @endforeach
        </tbody>
      </table>
    </div>

    @if(isset($message))
      <div style="text-align:center;padding:14px;font-size:12px;color:var(--ash)">{{ $message }}</div>
    @endif

  </div><!-- /main-card -->
</div><!-- /page -->

<!-- ═══ POPUP MODAL ═══ -->
<div class="modal-bg" id="popupModal">
  <div class="modal">
    <div class="modal-head">
      <h3 id="popup-title">ข้อมูลสินค้า</h3>
      <button class="modal-x" onclick="closePopup()">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 2l8 8M10 2L2 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="sec-ttl">ข้อมูลลูกค้า</div>
      <div class="tbl-wrap" style="margin-bottom:16px">
        <table>
          <thead>
            <tr>
              <th>เลขที่บิล</th>
              <th>ชื่อลูกค้า</th>
              <th>เบอร์โทร</th>
              <th>ที่อยู่จัดส่ง</th>
              <th>ผู้ขาย</th>
            </tr>
          </thead>
          <tbody id="popup-body-1"></tbody>
        </table>
      </div>
      <div class="sec-ttl">รายการสินค้า</div>
      <div class="tbl-wrap">
        <table>
          <thead>
            <tr>
              <th>รหัสสินค้า</th>
              <th>รายการ</th>
              <th style="text-align:center">จำนวน</th>
              <th style="text-align:right">ราคา/หน่วย</th>
            </tr>
          </thead>
          <tbody id="popup-body"></tbody>
        </table>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn-ghost" onclick="closePopup()">ปิด</button>
    </div>
  </div>
</div>

<script>
/* ── Pull PO outside ── */
document.getElementById('pullPoOutside') && document.getElementById('pullPoOutside').addEventListener('click',function(){
  fetch("{{ route('pull.pooutside') }}",{
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}
  }).then(r=>r.json()).then(d=>console.log(d.message)).catch(e=>console.error(e));
});

/* ── Search ── */
function searchTable(){
  const q=document.getElementById('search-input').value.toLowerCase();
  document.querySelectorAll('#table-body tr').forEach(row=>{
    const cell=row.cells[1];
    row.style.display=(cell&&cell.textContent.toLowerCase().includes(q))?'':'none';
  });
}

/* ── Toggle checkboxes ── */
function toggleCheckboxes(){
  const all=document.getElementById('checkAll').checked;
  document.querySelectorAll('input[name="statupdf[]"]').forEach(c=>c.checked=all);
}

/* ── Update statuspdf ── */
function updateStatuspdf(){
  const ids=[];
  document.querySelectorAll("input[name='statupdf[]']:checked").forEach(cb=>{
    const row=cb.closest('tr');
    ids.push(row.cells[1].textContent.trim());
  });
  if(!ids.length)return;
  fetch('/update-statuspdfso',{
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
    body:JSON.stringify({soDetailIds:ids})
  }).then(r=>r.json()).then(d=>{if(d.success)location.reload();}).catch(console.error);
}

/* ── Copy to clipboard (generic) ── */
function cpText(text,btn){
  const t=text.trim();if(!t)return;
  const orig=btn.textContent;
  const done=()=>{btn.classList.add('copied');btn.textContent='คัดลอกแล้ว ✓';setTimeout(()=>{btn.classList.remove('copied');btn.textContent=orig;},1500);};
  (navigator.clipboard?navigator.clipboard.writeText(t):Promise.reject()).then(done).catch(()=>{const i=document.createElement('input');i.value=t;document.body.appendChild(i);i.select();try{document.execCommand('copy');}catch(e){}document.body.removeChild(i);done();});
}

/* ── PDF functions ── */
function mergePdf(billid){
  fetch("{{ route('merge.pdf') }}",{
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json','Accept':'application/json'},
    body:JSON.stringify({billid})
  }).then(r=>r.json()).then(d=>{if(!d.success)console.error('merge failed:',d.message);}).catch(console.error);
}

function fallbackCopy(text){
  const t=document.createElement('textarea');t.value=text;t.style.position='fixed';t.style.top=0;t.style.left=0;
  t.style.width='1px';t.style.height='1px';t.style.opacity=0;
  document.body.appendChild(t);t.select();try{document.execCommand('copy');}catch(e){}document.body.removeChild(t);
}

function openFileInNewTab(url,ponum,so_detail_id,so_id,billid){
  const cb=document.getElementById('checkbox_'+so_detail_id);if(cb)cb.checked=true;
  const val=billid&&billid.trim()?billid:String(so_id||'').replace(/^SO/,'');
  (navigator.clipboard?navigator.clipboard.writeText(val):Promise.reject()).catch(()=>fallbackCopy(val));
  if(url&&url.trim()){const ts=Date.now();window.open(url.includes('?')?`${url}&t=${ts}`:`${url}?t=${ts}`,'_blank');}
}

function openFileInNewTabbill(url,ponum,so_detail_id,so_id,billid){
  const cb=document.getElementById('checkbox_'+so_detail_id);if(cb)cb.checked=true;
  const val=billid&&billid.trim()?billid:String(so_id||'').replace(/^SO/,'');
  (navigator.clipboard?navigator.clipboard.writeText(val):Promise.reject()).catch(()=>fallbackCopy(val));
  window.open(url,'_blank');
}

function openBillAndCheck(url,cbId){
  window.open(url,'_blank');
  const cb=document.getElementById(cbId);if(cb)cb.checked=true;
}

/* ── Add to documents ── */
function addSoDetailIdToPoDocument(id,doc){
  fetch(`/add-so-detail-id-to-pdf/${id}/${doc}`)
    .then(r=>r.json()).then(d=>{if(!d.success)console.error(d.error||'');}).catch(console.error);
}

function addIdToissueDocument(id,bill_issue_no){
  fetch(`/add-so-detail-id-to-billissue/${id}/${bill_issue_no}`)
    .then(r=>r.json()).then(d=>{if(!d.success)console.error(d.error||'');}).catch(console.error);
}

function checkBillTypeAndAddBill(so_detail_id,billid,billtype,so_id){
  if(billtype&&billtype.includes('งานบริการ'))addIdToDocument3(so_detail_id,billid,so_id);
  else if(billtype&&billtype.includes('งานเช่า'))addIdToDocument5(so_detail_id,billid,so_id);
  else addIdToDocument(so_detail_id,billid,so_id);
}

function _postBill(url,so_detail_id,billid,so_id){
  fetch(url,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
    body:JSON.stringify({so_detail_id,billid,so_id})})
    .then(r=>r.json()).then(d=>{if(!d.success)console.error(d.error||'');}).catch(console.error);
}
function addIdToDocument(id,billid,so_id){_postBill('/add-so-detail-id-to-bill',id,billid,so_id);}
function addIdToDocument3(id,billid,so_id){_postBill('/add-so-detail-id-to-bill-3',id,billid,so_id);}
function addIdToDocument5(id,billid,so_id){_postBill('/add-so-detail-id-to-bill-5',id,billid,so_id);}

/* ── Popup modal ── */
function openPopup(soDetailId,so_id,ponum,customer_name,customer_tel,customer_address,date_of_dali,sale_name){
  document.getElementById('popup-title').textContent='ข้อมูล: '+soDetailId;
  document.getElementById('popup-body-1').innerHTML=`
    <tr><td class="c-code">${soDetailId}</td><td>${customer_name}</td><td class="c-sm">${customer_tel}</td><td style="font-size:11px">${customer_address}</td><td class="c-sm">${sale_name}</td></tr>`;
  const pb=document.getElementById('popup-body');
  pb.innerHTML='<tr><td colspan="4" style="text-align:center;color:var(--mist);padding:16px">กำลังโหลด...</td></tr>';
  fetch(`/get-bill-detail/${soDetailId}`).then(r=>r.json()).then(data=>{
    pb.innerHTML=data.length
      ?data.map(it=>`<tr><td class="c-code">${it.item_id}</td><td>${it.item_name}</td><td class="c-center">${it.quantity}</td><td style="text-align:right">${parseFloat(it.unit_price||0).toLocaleString('th-TH',{minimumFractionDigits:2})}</td></tr>`).join('')
      :'<tr><td colspan="4" style="text-align:center;color:var(--mist);padding:16px">ไม่มีข้อมูล</td></tr>';
  }).catch(()=>{pb.innerHTML='<tr><td colspan="4" style="text-align:center;color:var(--rose);padding:16px">เกิดข้อผิดพลาด</td></tr>';});
  document.getElementById('popupModal').classList.add('open');
  document.body.style.overflow='hidden';
}
function closePopup(){document.getElementById('popupModal').classList.remove('open');document.body.style.overflow='';}
document.getElementById('popupModal').addEventListener('click',function(e){if(e.target===this)closePopup();});
document.addEventListener('keydown',e=>{if(e.key==='Escape')closePopup();});

/* ── Bill issue buttons (per-row, avoid single getElementById collision) ── */
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('[id^="billissuebut_"]').forEach(btn=>{
    const soDetailId=btn.getAttribute('data-sodetailid');
    const inp=document.getElementById('billissue_'+soDetailId);
    if(!inp)return;
    btn.addEventListener('click',function(){
      const val=inp.value.trim();if(!val)return;
      btn.disabled=true;
      fetch('/update-billissue',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},
        body:JSON.stringify({so_detail_id:soDetailId,bill_issue_no:val})
      }).then(async r=>{const d=await r.json();if(!r.ok)console.error(d.message);})
        .catch(console.error).finally(()=>btn.disabled=false);
    });
    inp.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();btn.click();}});
  });
});

/* ── Save type input ── */
function saveTypeInput(soDetailId,btn){
  const inp=document.getElementById('typeinput_'+soDetailId);
  const val=inp?inp.value.trim():'';
  if(!val)return;
  const orig=btn.textContent;
  btn.disabled=true;btn.textContent='...';
  fetch('/update-billtype',{
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},
    body:JSON.stringify({so_detail_id:soDetailId,billtype:val})
  }).then(async r=>{
    const d=await r.json();
    if(r.ok&&d.success){
      btn.style.background='var(--emerald)';btn.textContent='บันทึกแล้ว ✓';
      setTimeout(()=>{btn.disabled=false;btn.textContent=orig;btn.style.background='';},1500);
    } else {
      btn.textContent='ผิดพลาด';setTimeout(()=>{btn.disabled=false;btn.textContent=orig;},1500);
    }
  }).catch(()=>{btn.textContent='ผิดพลาด';setTimeout(()=>{btn.disabled=false;btn.textContent=orig;},1500);});
}
</script>
</body>
</html>