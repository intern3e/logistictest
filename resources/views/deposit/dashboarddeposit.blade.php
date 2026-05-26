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
  --navy:#0B2447;--navy2:#19376D;--ntbl:#19376D;
  --rh:#EAF1FB;--rhov:#D6E4F5;--line:#C5D6EC;--txt:#0B2447;--link:#1E40AF;
  --red:#9B1B1B;--red-h:#7F1717;--red-lt:#FEE2E2;--red-bd:#FCA5A5;
  --ink:#1F2937;--steel:#374151;--ash:#6B7280;--mist:#9CA3AF;
  --border:#D6D9DD;--bg:#EEF2F7;
}
body{font-family:'Sarabun','Kanit',sans-serif;font-size:14px;background:var(--bg);color:var(--ink);line-height:1.5}
.topbar{background:linear-gradient(180deg,var(--navy),var(--navy2));padding:11px 16px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;border-bottom:3px solid #061B3A}
.brand{font-family:'Kanit';font-size:19px;font-weight:600;color:#fff}
.topbar-r{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.pill{display:inline-flex;align-items:center;gap:5px;padding:4px 10px 4px 6px;border-radius:5px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);font-size:11px;color:#fff;font-weight:500}
.ava{width:18px;height:18px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;color:var(--navy);font-size:9px;font-weight:700;font-family:'Kanit'}
.btn-home{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:5px;background:var(--red);color:#fff;font-size:11px;font-weight:500;border:none;cursor:pointer;text-decoration:none;font-family:inherit}
.btn-home:hover{background:var(--red-h)}
.fbar{padding:8px 16px;display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.fsearch{display:flex;align-items:center;background:#fff;border:1px solid var(--border);border-radius:4px;padding:5px 10px;flex:1;max-width:380px;min-width:0}
.fsearch input{border:none;outline:none;background:none;font-size:12px;font-family:inherit;color:var(--ink);width:100%}
.fsearch input::placeholder{color:var(--mist)}
.fcount{font-size:11px;color:var(--ash);margin-left:auto;white-space:nowrap}
.tw{background:#fff;margin:0 16px 16px;border:1px solid var(--line);overflow-x:auto;-webkit-overflow-scrolling:touch}
table{width:100%;border-collapse:collapse;font-size:12px}
thead tr{background:var(--ntbl)}
th{padding:8px 8px;font-size:11px;font-weight:600;color:#fff;border-right:1px solid rgba(255,255,255,.12);text-align:center;white-space:nowrap;font-family:'Kanit'}
th:last-child{border-right:none}
tbody tr{background:#fff;transition:background .08s}
tbody tr:hover{background:var(--rh)}
tbody tr.hidden-row{display:none}
td{padding:7px 8px;border-bottom:1px solid var(--line);border-right:1px solid var(--line);vertical-align:middle;text-align:center}
td:last-child{border-right:none}
tbody tr:last-child td{border-bottom:none}
.cL{text-align:left}.cR{text-align:right}.cM{font-family:'Sarabun',monospace;font-weight:500;text-align:right;white-space:nowrap}
.cB{font-weight:700;color:var(--txt)}.cN{white-space:nowrap}.cS{font-size:10.5px;color:var(--ash)}
.bg{display:inline-block;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;white-space:nowrap}
.bg-ok{background:#D1FAE5;color:#047857}
.bg-wait{background:#FEF3C7;color:#92400E}
.bg-cancel{background:var(--red-lt);color:var(--red)}
.bg-wip{background:#DBEAFE;color:var(--link)}
.bg-wht{background:#EDE9FE;color:#6D28D9}
.act{display:flex;align-items:center;gap:4px;justify-content:center;flex-wrap:wrap}
.ab{display:inline-flex;align-items:center;justify-content:center;gap:3px;padding:3px 7px;border-radius:3px;font-size:10px;font-weight:600;border:1px solid;cursor:pointer;transition:all .12s;text-decoration:none;font-family:inherit;white-space:nowrap;line-height:1.3}
.ab-pdf{background:var(--red-lt);border-color:var(--red-bd);color:var(--red)}
.ab-pdf:hover{background:var(--red);color:#fff;border-color:var(--red)}
.ab-pdf.ok{background:#D1FAE5;border-color:#6EE7B7;color:#047857}
.ab-pdf.ok:hover{background:#047857;color:#fff}
.ab-slip{background:#ECFDF5;border-color:#6EE7B7;color:#047857}
.ab-slip:hover{background:#047857;color:#fff;border-color:#047857}
.ab-add{background:#FFF7ED;border-color:#FDBA74;color:#C2410C}
.ab-add:hover{background:#C2410C;color:#fff;border-color:#C2410C}
.ab-info{background:var(--rh);border-color:var(--line);color:var(--ntbl)}
.ab-info:hover{background:var(--ntbl);color:#fff}

/* ===== Inline edit (fee/wht) ===== */
.ie{display:flex;align-items:center;gap:3px;justify-content:center}
.ie input{width:90px;border:1px solid var(--border);border-radius:3px;padding:4px 6px;font-size:11px;font-family:inherit;color:var(--ink);outline:none;text-align:center;transition:border .12s,background .12s}
.ie input:focus{border-color:var(--ntbl);box-shadow:0 0 0 2px rgba(25,55,109,.1)}
.ie input.dirty{border-color:#D97706;background:#FFFBEB}
/* ✅ ปุ่ม save */
.ie-sv{width:24px;height:24px;display:flex;align-items:center;justify-content:center;background:var(--ntbl);color:#fff;border:none;border-radius:3px;cursor:pointer;flex-shrink:0;transition:all .12s}
.ie-sv:hover{background:var(--navy)}
.ie-sv:disabled{opacity:.4;cursor:not-allowed}
.ie-sv.ok{background:#047857}
/* ✅ ปุ่ม clear (ลบค่า) */
.ie-cl{width:24px;height:24px;display:flex;align-items:center;justify-content:center;background:var(--red-lt);color:var(--red);border:1px solid var(--red-bd);border-radius:3px;cursor:pointer;flex-shrink:0;transition:all .12s;font-size:12px;font-weight:700;line-height:1}
.ie-cl:hover{background:var(--red);color:#fff;border-color:var(--red)}
.ie-cl:disabled{opacity:.3;cursor:not-allowed}

.btn-del{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:5px;background:var(--red-lt);border:1px solid var(--red-bd);color:var(--red);font-size:11px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .12s}
.btn-del:hover{background:var(--red);color:#fff}
.btn-st{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:5px;font-size:11px;font-weight:600;cursor:pointer;font-family:inherit;border:1px solid;transition:all .12s}
.btn-st.cfm{background:#D1FAE5;border-color:#6EE7B7;color:#047857}
.btn-st.cfm:hover{background:#047857;color:#fff}
.btn-st.bk{background:#FEF3C7;border-color:#FDE68A;color:#92400E}
.btn-st.bk:hover{background:#D97706;color:#fff}
.pgb{display:flex;align-items:center;justify-content:space-between;padding:8px 14px;border-top:1px solid var(--line);flex-wrap:wrap;gap:6px;background:#fff}
.pgi{font-size:11px;color:var(--ash)}
nav[role="navigation"]>div{display:flex!important;align-items:center!important;justify-content:center!important;gap:3px;flex-wrap:wrap!important}
nav[role="navigation"]>div>p{display:none!important}
nav[role="navigation"] .sm\:hidden{display:none!important}
nav[role="navigation"] a[rel="prev"] span,nav[role="navigation"] a[rel="next"] span{display:none!important}
nav[role="navigation"] a[rel="prev"],nav[role="navigation"] a[rel="next"]{width:26px;height:26px;border-radius:3px;background:#fff;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--ash)}
nav[role="navigation"] svg{width:11px!important;height:11px!important}
nav[role="navigation"] a.relative,nav[role="navigation"] span.relative{min-width:26px;height:26px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:500;color:var(--link);border-radius:3px;border:1px solid transparent}
nav[role="navigation"] span[aria-current="page"]{background:var(--ntbl)!important;color:#fff!important;border-color:var(--ntbl)!important}

/* ===== Modal (ใหญ่ขึ้น) ===== */
.mo-bg{display:none;position:fixed;inset:0;background:rgba(13,15,18,.5);z-index:1000;align-items:center;justify-content:center;padding:10px;backdrop-filter:blur(2px)}
.mo-bg.open{display:flex}
.mo{background:#fff;border-radius:10px;width:100%;max-width:720px;max-height:92vh;overflow-y:auto;border:1px solid var(--border);box-shadow:0 16px 48px rgba(0,0,0,.18);animation:pop .18s ease-out}
@keyframes pop{from{transform:scale(.93);opacity:0}to{transform:scale(1);opacity:1}}
.mo-h{padding:14px 20px;background:var(--navy);color:#fff;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:1;border-radius:10px 10px 0 0}
.mo-h h3{font-size:15px;font-weight:600;font-family:'Kanit'}
.mo-x{width:28px;height:28px;border-radius:4px;border:1px solid rgba(255,255,255,.2);background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#fff}
.mo-x:hover{background:rgba(255,255,255,.15)}
.mo-b{padding:18px 22px}
.sttl{font-size:10px;font-weight:700;color:var(--txt);letter-spacing:.06em;text-transform:uppercase;margin-bottom:10px;padding-bottom:6px;border-bottom:2px solid var(--ntbl)}
.dg{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px}
.dg3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:16px}
.df{display:flex;flex-direction:column;gap:3px}
.df.full{grid-column:1/-1}
.dl{font-size:9px;font-weight:700;color:var(--ash);letter-spacing:.05em;text-transform:uppercase}
.dv{font-size:13px;color:var(--ink);font-weight:500}
.mo-f{padding:12px 22px;border-top:1px solid var(--line);display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end}
.btn-g{padding:7px 16px;border-radius:5px;border:1px solid var(--border);background:none;font-size:12px;font-weight:500;color:var(--steel);cursor:pointer;font-family:inherit}
.btn-g:hover{border-color:var(--ntbl);color:var(--ntbl);background:var(--rh)}

/* ===== Money highlight box ===== */
.money-box{background:linear-gradient(135deg,#EAF1FB,#F0F4FA);border:2px solid var(--ntbl);border-radius:8px;padding:14px 18px;text-align:center;margin-top:8px}
.money-box .dl{font-size:11px;color:var(--ntbl);margin-bottom:4px}
.money-box .dv{font-size:22px;font-weight:800;color:var(--navy)}

/* Confirm */
.cf-bg{display:none;position:fixed;inset:0;background:rgba(13,15,18,.6);z-index:2000;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(3px)}
.cf-bg.open{display:flex}
.cf-box{background:#fff;border-radius:12px;width:100%;max-width:380px;box-shadow:0 20px 50px rgba(0,0,0,.25);animation:pop .18s ease-out;text-align:center;padding:22px 20px}
.cf-ico{width:48px;height:48px;border-radius:50%;margin:0 auto 10px;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700}
.cf-ico.w{background:#FEF3C7;color:#D97706}.cf-ico.d{background:var(--red-lt);color:var(--red)}.cf-ico.g{background:#D1FAE5;color:#047857}
.cf-t{font-size:15px;font-weight:600;color:var(--ink);font-family:'Kanit';margin-bottom:5px}
.cf-m{font-size:12px;color:var(--ash);line-height:1.5;margin-bottom:14px}
.cf-m b{color:var(--ink)}
.cf-btns{display:flex;gap:8px}
.cf-btns button{flex:1;padding:9px;border-radius:7px;font-size:12px;font-weight:600;font-family:inherit;cursor:pointer;border:none;transition:all .12s}
.cf-cn{background:#F3F4F6;color:var(--steel)}.cf-cn:hover{background:#E5E7EB}
.cf-ok{background:var(--ntbl);color:#fff}.cf-ok:hover{background:var(--navy)}
.cf-dl{background:var(--red);color:#fff}.cf-dl:hover{background:var(--red-h)}
.cf-ok:disabled,.cf-dl:disabled{opacity:.5;cursor:not-allowed}
/* Slip */
.sl-df{margin-bottom:12px}
.sl-df label{display:block;font-size:9px;font-weight:700;color:var(--txt);letter-spacing:.04em;margin-bottom:4px;text-transform:uppercase}
.sl-df input[type=date]{width:100%;border:1px solid var(--border);border-radius:5px;padding:7px 10px;font-size:12px;font-family:inherit;color:var(--ink);outline:none}
.sl-drop{display:block;width:100%;border:2px dashed var(--line);border-radius:8px;padding:20px 14px;text-align:center;background:#FAFBFE;cursor:pointer;transition:all .12s;margin:0}
.sl-drop:hover,.sl-drop.dov{border-color:var(--ntbl);background:var(--rh)}
.sl-drop input[type=file]{position:absolute;width:0;height:0;opacity:0;pointer-events:none}
.sl-prev{margin-top:10px;display:none}
.sl-prev.show{display:block}
.sl-prev img{max-width:100%;max-height:220px;border-radius:5px;border:1px solid var(--line)}
.toast{position:fixed;bottom:18px;left:50%;transform:translateX(-50%) translateY(80px);background:var(--navy);color:#fff;padding:9px 18px;border-radius:7px;font-size:11px;font-weight:500;box-shadow:0 6px 20px rgba(0,0,0,.2);z-index:3000;opacity:0;transition:all .22s;max-width:90vw}
.toast.show{transform:translateX(-50%) translateY(0);opacity:1}
.toast.err{background:var(--red)}
.empty{padding:40px 16px;text-align:center;background:#fff}
.empty h4{font-size:13px;font-weight:600;font-family:'Kanit';color:var(--txt);margin-bottom:3px}
.empty p{font-size:11px;color:var(--ash)}
.nmr{display:none}.nmr.show{display:table-row}
</style>
</head>
<body>
@php
  $deposits = $deposits ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 100);
  $tl = ['product'=>'สินค้า','service'=>'บริการ','shipping'=>'ขนส่ง'];
  $cu = strtolower(request()->get('create_by', ''));
  $adm = in_array($cu, ['kanitin2','dev','aom']);
@endphp

<div class="topbar">
  <div class="brand">ข้อมูลใบมัดจำ</div>
  <div class="topbar-r">
    <div class="pill">
      @php $cb=trim(request()->get('create_by','')); @endphp
      @if($cb!=='')
        <div class="ava">{{ strtoupper(substr($cb,0,2)) }}</div>{{ $cb }}
      @else
        <span style="font-size:12px">👤</span><span>ผู้ใช้</span>
      @endif
    </div>
    <a href="http://server_update:8000/solist" class="btn-home">🏠 หน้าหลัก</a>
  </div>
</div>

<div class="fbar">
  <div class="fsearch">
    <svg width="13" height="13" viewBox="0 0 14 14" fill="none" style="flex-shrink:0;margin-right:5px"><circle cx="6" cy="6" r="4.5" stroke="#9CA3AF" stroke-width="1.2"/><path d="M9.5 9.5l3 3" stroke="#9CA3AF" stroke-width="1.2" stroke-linecap="round"/></svg>
    <input type="text" id="fQ" placeholder="ค้นหา ใบสั่งขาย / ใบมัดจำ / PO / ลูกค้า" oninput="doFilter()">
  </div>
  <span class="fcount" id="fC">{{ $deposits->total() }} รายการ</span>
</div>

<div class="tw">
<table>
<thead><tr>
  <th>ลำดับ</th>
  <th>ใบมัดจำ</th>
  <th>ใบสั่งขาย</th>
  <th>ลูกค้า</th>
  <th>ชำระคงเหลือ<br><span style="font-size:8px;font-weight:400;opacity:.75">รวม 7% VAT</span></th>
  <th>ยอดมัดจำ<br><span style="font-size:8px;font-weight:400;opacity:.75">รวม 7% VAT</span></th>
  <th>ค่าธรรมเนียม</th>
  <th>WHT</th>
  <th>สถานะ</th>
</tr></thead>
<tbody id="tb">
@forelse($deposits as $item)
@php
  $st = $item->status ?? 'รอยืนยัน';
  $isCfm = in_array($st,['ยืนยัน','ยืนยันแล้ว','สำเร็จ','ออกบิลแล้ว','ปรับสำเร็จ']);
  $isWht = $st === 'มี WHT';
  $isCnl = $st==='ยกเลิก';
  $dt = $item->dep_type ?? '';
  $tn = $tl[$dt] ?? $dt;
  $bid = $item->deposit_bill_id ?? '';
  $cpo = $item->po_document ?? $item->billid ?? '';
  $fee = (float)($item->fee_amount ?? 0);
  $dpv = (float)($item->dep_price ?? 0) * 1.07;
  $gtv = (float)($item->grand_total ?? 0) * 1.07;
  $net = max(0, $dpv - $fee);
  $wht = $item->wht_doc_no ?? '';
  $remaining = max(0, $gtv - $dpv);

  $pdfRow = $bid ? \App\Models\deposit::where('deposit_bill_id',$bid)->select('deposit_bill','status_bill','deposit_slip','slip_time')->first() : null;
  $pdfF = $pdfRow->deposit_bill ?? null;
  $pdfOk = ($pdfRow->status_bill ?? null)==='ok';
  $slipF = $pdfRow->deposit_slip ?? null;

  $timeFormatted = $item->time ? \Carbon\Carbon::parse($item->time)->setTimezone('Asia/Bangkok')->format('H:i d/m/Y') : '';

  $rj = json_encode([
    'id'=>$item->id,'so_id'=>$item->so_id??'','deposit_bill_id'=>$bid,'cust_po'=>$cpo,
    'customer_name'=>$item->customer_name??'','customer_id'=>$item->customer_id??'',
    'sale_name'=>$item->sale_name??'','emp_name'=>$item->emp_name??'',
    'time'=>$timeFormatted,
    'dep_type_name'=>$tn,'dep_per'=>(float)($item->dep_per??0),
    'dep_price'=>(float)($item->dep_price??0),
    'dep_price_vat'=>$dpv,'grand_total'=>(float)($item->grand_total??0),'grand_total_vat'=>$gtv,
    'fee_amount'=>$fee,'net'=>$net,'wht_doc_no'=>$wht,
    'remaining'=>$remaining,
    'status'=>$st,'is_confirmed'=>$isCfm,'is_wht'=>$isWht,'is_cancelled'=>$isCnl,
    'time_check'=>$item->time_check ? \Carbon\Carbon::parse($item->time_check)->setTimezone('Asia/Bangkok')->format('H:i d/m/Y') : '',
    'slip_time'=>$item->slip_time ? \Carbon\Carbon::parse($item->slip_time)->setTimezone('Asia/Bangkok')->format('d/m/Y') : '',
    'print_time'=>$item->print_time ? \Carbon\Carbon::parse($item->print_time)->setTimezone('Asia/Bangkok')->format('H:i d/m/Y') : '',
    'status_bill'=>$item->status_bill??'',
    'has_slip'=>(bool)($slipF || $item->slip_time),
    'contactso'=>$item->contactso??'','customer_tel'=>$item->customer_tel??'',
    'customer_address'=>$item->customer_address??'','note'=>$item->note??'',
  ], JSON_UNESCAPED_UNICODE|JSON_HEX_APOS);
@endphp
<tr data-q="{{ strtolower(($item->so_id??'').' '.$bid.' '.$cpo.' '.($item->customer_name??'')) }}">
  <td class="cS row-idx">{{ ($deposits->currentPage()-1)*$deposits->perPage()+$loop->iteration }}</td>
  <td><span class="cB cN" style="font-size:11px">{{ $bid ?: '—' }}
        <div class="act">
      @if($pdfF)
        <a href="{{ asset('storage/deposit_templates/'.$pdfF) }}" target="_blank" class="ab ab-pdf {{ $pdfOk?'ok':'' }}" onclick="event.stopPropagation()">PDF</a>
      @endif
      @if($slipF)
        <a href="{{ asset('storage/deposit_templates/deposit_slip/'.$slipF) }}" target="_blank" class="ab ab-slip" onclick="event.stopPropagation()">หลักฐานการชำระ</a>
      @elseif($bid)
        <button class="ab ab-add" onclick="event.stopPropagation();openSlip('{{ $bid }}')">+หลักฐานการชำระ</button>
      @endif
      <button class="ab ab-info" onclick="event.stopPropagation();openInfo({!! htmlspecialchars($rj, ENT_QUOTES) !!})">ข้อมูล</button>
    </div></span></td>
  <td class="cN" style="font-size:11px">{{ $item->so_id ?? '—' }}</td>
  <td class="cL" style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $item->customer_name }}">{{ $item->customer_name ?? '—' }}</td>
  <td class="cM">{{ number_format($gtv,2) }}</td>
  <td class="cM">{{ number_format($dpv,2) }}</td>

  {{-- ✅ ค่าธรรมเนียม — มีปุ่ม clear (×) ให้ลบค่าได้ --}}
  <td>
    @if($adm)
      <div class="ie">
        <input type="number" step="0.01" min="0" id="fee-{{$item->id}}" value="{{ $fee>0?$fee:'' }}" placeholder="0" data-o="{{ $fee }}" oninput="chg(this,'fee-sv-{{$item->id}}','fee-cl-{{$item->id}}')">
        <button class="ie-sv" id="fee-sv-{{$item->id}}" disabled onclick="saveFee({{$item->id}})" title="บันทึกค่าธรรมเนียม">
          <svg width="11" height="11" viewBox="0 0 14 14" fill="none"><path d="M3 7l3 3 5-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button class="ie-cl" id="fee-cl-{{$item->id}}" {{ $fee>0?'':'disabled' }} onclick="clearFee({{$item->id}})" title="ลบค่าธรรมเนียม">×</button>
      </div>
    @else
      @if($fee>0)<span style="font-size:11px;color:#92400E;font-weight:600">{{ number_format($fee,2) }}</span>@else<span class="cS">—</span>@endif
    @endif
  </td>

  {{-- ✅ WHT — มีปุ่ม clear (×) ให้ลบค่าได้ --}}
  <td>
    @if($adm)
      <div class="ie">
        <input type="text" id="wht-{{$item->id}}" value="{{ $wht }}" placeholder="—" maxlength="60" data-o="{{ $wht }}" oninput="chg(this,'wht-sv-{{$item->id}}','wht-cl-{{$item->id}}')">
        <button class="ie-sv" id="wht-sv-{{$item->id}}" disabled onclick="saveWht({{$item->id}},'{{ $item->so_id }}')" title="บันทึก WHT (สถานะจะเปลี่ยนเป็น มี WHT)">
          <svg width="11" height="11" viewBox="0 0 14 14" fill="none"><path d="M3 7l3 3 5-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button class="ie-cl" id="wht-cl-{{$item->id}}" {{ $wht?'':'disabled' }} onclick="clearWht({{$item->id}},'{{ $item->so_id }}')" title="ลบ WHT">×</button>
      </div>
    @else
      @if($wht)<span style="font-size:11px;color:#6D28D9;font-weight:600">{{ $wht }}</span>@else<span class="cS">—</span>@endif
    @endif
  </td>

  {{-- สถานะ --}}
  <td>
    @if($isCnl)<span class="bg bg-cancel">ยกเลิก</span>
    @elseif($isWht)<span class="bg bg-wht">มี WHT</span>
    @elseif($isCfm)<span class="bg bg-ok">{{ $st }}</span>
    @elseif($st==='รอยืนยัน')<span class="bg bg-wait">รอยืนยัน</span>
    @else<span class="bg bg-wip">{{ $st }}</span>@endif
  </td>
</tr>
@empty
<tr><td colspan="9" style="border-right:none"><div class="empty"><h4>ไม่พบข้อมูล</h4><p>ยังไม่มีรายการใบมัดจำ</p></div></td></tr>
@endforelse
<tr class="nmr" id="nmr"><td colspan="9" style="border-right:none"><div class="empty"><h4>ไม่พบรายการ</h4><p>ลองเปลี่ยนคำค้นหา</p></div></td></tr>
</tbody>
</table>
@if($deposits->total()>0)
<div class="pgb">
  <span class="pgi">แสดง {{ $deposits->firstItem()??0 }}–{{ $deposits->lastItem()??0 }} จาก {{ $deposits->total() }}</span>
  <div>{{ $deposits->appends(['create_by'=>request('create_by')])->links() }}</div>
</div>
@endif
</div>

<!-- ✅ INFO POPUP (ใหญ่ขึ้น max-width:720px, เพิ่มยอดคงเหลือ) -->
<div class="mo-bg" id="infoM">
<div class="mo">
  <div class="mo-h"><h3 id="im-t">รายละเอียด</h3><button class="mo-x" onclick="closeInfo()"><svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 2l8 8M10 2L2 10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></button></div>
  <div class="mo-b">
    <div class="sttl">ข้อมูลทั่วไป</div>
    <div class="dg3">
      <div class="df"><span class="dl">เลขใบมัดจำ</span><span class="dv cB" id="im-bill">—</span></div>
      <div class="df"><span class="dl">ใบสั่งขาย</span><span class="dv" id="im-so">—</span></div>
      <div class="df"><span class="dl">PO ลูกค้า</span><span class="dv cB" id="im-po">—</span></div>
    </div>
    <div class="dg">
      <div class="df"><span class="dl">วันที่บันทึกลงระบบ</span><span class="dv" id="im-date">—</span></div>
      <div class="df full"><span class="dl">ลูกค้า</span><span class="dv" id="im-cust">—</span></div>
      <div class="df"><span class="dl">ผู้ติดต่อ</span><span class="dv" id="im-con">—</span></div>
      <div class="df"><span class="dl">เบอร์โทร</span><span class="dv" id="im-tel">—</span></div>
      <div class="df full"><span class="dl">ที่อยู่</span><span class="dv" id="im-addr">—</span></div>
      <div class="df"><span class="dl">ผู้บันทึกลงระบบ</span><span class="dv" id="im-emp">—</span></div>
      <div class="df"><span class="dl">Sale</span><span class="dv" id="im-sale">—</span></div>
      <div class="df"><span class="dl">ประเภท / %</span><span class="dv" id="im-type">—</span></div>
      <div class="df full"><span class="dl">หมายเหตุ</span><span class="dv" id="im-note" style="color:var(--ash)">—</span></div>
    </div>

    <div class="sttl">ยอดเงิน</div>
    <div class="dg3">
      <div class="df"><span class="dl">ราคาเต็ม (VAT)</span><span class="dv" id="im-grand" style="font-size:14px;font-weight:700">—</span></div>
      <div class="df"><span class="dl">ยอดมัดจำ (VAT)</span><span class="dv cB" id="im-dep" style="font-size:14px">—</span></div>
      <div class="df"><span class="dl">ค่าธรรมเนียม</span><span class="dv" id="im-fee" style="color:#92400E;font-size:14px">—</span></div>
    </div>
    <div class="dg">
      <div class="df"><span class="dl">ยอดที่ถูกชำระ</span><span class="dv cB" id="im-net" style="color:#047857;font-size:14px">—</span></div>
      <div class="df"><span class="dl">WHT</span><span class="dv" id="im-wht2" style="color:#6D28D9;font-weight:600">—</span></div>
    </div>
    {{-- ✅ ยอดคงเหลือ เด่นชัด --}}
    <div class="money-box">
      <div class="dl">ยอดคงเหลือที่ต้องชำระ (ราคาเต็ม − มัดจำ)</div>
      <div class="dv" id="im-remain">—</div>
    </div>

    <div class="sttl" style="margin-top:16px">สถานะ</div>
    <div class="dg3">
      <div class="df"><span class="dl">สถานะ</span><span class="dv" id="im-st">—</span></div>
      <div class="df"><span class="dl">ยืนยันเมื่อ</span><span class="dv" id="im-tc">—</span></div>
      <div class="df"><span class="dl">แนบหลักฐานการชำระ</span><span class="dv" id="im-sl">—</span></div>
    </div>
  </div>
  <div class="mo-f" id="im-f"><button class="btn-g" onclick="closeInfo()">ปิด</button></div>
</div>
</div>

<!-- CONFIRM -->
<div class="cf-bg" id="cfM">
<div class="cf-box">
  <div class="cf-ico" id="cf-ico">!</div>
  <div class="cf-t" id="cf-t">—</div>
  <div class="cf-m" id="cf-m">—</div>
  <div class="cf-btns"><button class="cf-cn" onclick="closeCf()">ยกเลิก</button><button class="cf-ok" id="cf-btn" onclick="doCf()">ยืนยัน</button></div>
</div>
</div>

<!-- SLIP -->
<div class="mo-bg" id="slM">
<div class="mo" style="max-width:440px">
  <div class="mo-h"><h3>แนบหลักฐานการชำระ <span id="sl-bid" style="font-weight:400;opacity:.8;font-size:11px"></span></h3><button class="mo-x" onclick="closeSl()"><svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M2 2l8 8M10 2L2 10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></button></div>
  <div style="padding:14px 16px">
    <div class="sl-df"><label>วันที่โอน</label><input type="date" id="slD" value="{{ date('Y-m-d') }}"></div>
    <label class="sl-drop" id="slDrop">
      <input type="file" id="slF" accept=".pdf,.jpg,.jpeg,.png,.webp" onchange="onSlF(event)">
      <div style="font-size:16px;margin-bottom:4px">📎</div>
      <div style="font-size:11px;color:var(--steel)">คลิกเลือกไฟล์ หรือ ลากวาง</div>
      <div style="font-size:10px;color:var(--ash)">JPG, PNG, PDF (ไม่เกิน 10 MB)</div>
    </label>
    <div class="sl-prev" id="slP"></div>
  </div>
  <div class="mo-f" style="gap:6px"><button class="btn-g" onclick="closeSl()">ยกเลิก</button><button class="ie-sv" id="slBtn" onclick="doSl()" disabled style="width:auto;padding:6px 14px;border-radius:5px;font-size:11px;font-weight:600">อัปโหลด</button></div>
</div>
</div>

<div class="toast" id="toast"><span id="toastM">—</span></div>

<script>
const ADM={{ $adm?'true':'false' }},CU='{{ $cu }}',CS=document.querySelector('meta[name="csrf-token"]')?.content||'';
const H=()=>({'Content-Type':'application/json','X-CSRF-TOKEN':CS,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'});

function doFilter(){
  const q=document.getElementById('fQ').value.trim().toLowerCase();
  const rs=document.querySelectorAll('#tb tr[data-q]');
  let v=0,i=0;
  rs.forEach(r=>{const m=!q||r.dataset.q.includes(q);r.classList.toggle('hidden-row',!m);if(m){v++;i++;const c=r.querySelector('.row-idx');if(c)c.textContent=i}});
  document.getElementById('fC').textContent=v+' รายการ';
  document.getElementById('nmr').classList.toggle('show',rs.length>0&&v===0&&!!q);
}

// ✅ chg — รับ clBtnId เพื่อ enable/disable ปุ่ม clear ด้วย
function chg(inp,svBtnId,clBtnId){
  const svBtn=document.getElementById(svBtnId);
  const clBtn=clBtnId?document.getElementById(clBtnId):null;
  const dirty=inp.value!==inp.dataset.o;
  inp.classList.toggle('dirty',dirty);
  if(svBtn){svBtn.disabled=!dirty;svBtn.classList.remove('ok')}
  // enable clear ถ้ามีค่า (ไม่ว่าจะ dirty หรือไม่)
  if(clBtn){clBtn.disabled=!inp.value.trim()}
}

async function saveFee(id){
  if(!ADM)return;
  const inp=document.getElementById('fee-'+id),btn=document.getElementById('fee-sv-'+id);
  btn.disabled=true;
  try{
    const res=await fetch('/deposit/update-fee',{method:'POST',headers:H(),body:JSON.stringify({deposit_id:id,fee_amount:parseFloat(inp.value)||0,saved_by:CU})});
    const d=await res.json().catch(()=>({success:false}));
    if(!res.ok||!d.success)throw new Error(d.message||'HTTP '+res.status);
    inp.dataset.o=inp.value;inp.classList.remove('dirty');btn.classList.add('ok');
    toast('บันทึกค่าธรรมเนียมสำเร็จ');setTimeout(()=>location.reload(),800);
  }catch(e){toast('ล้มเหลว: '+e.message,1);btn.disabled=false}
}

// ✅ ลบค่าธรรมเนียม (เซ็ตเป็น 0)
async function clearFee(id){
  if(!ADM||!confirm('ลบค่าธรรมเนียมของรายการนี้?'))return;
  try{
    const res=await fetch('/deposit/update-fee',{method:'POST',headers:H(),body:JSON.stringify({deposit_id:id,fee_amount:0,saved_by:CU})});
    const d=await res.json().catch(()=>({success:false}));
    if(!res.ok||!d.success)throw new Error(d.message||'HTTP '+res.status);
    toast('ลบค่าธรรมเนียมสำเร็จ');setTimeout(()=>location.reload(),600);
  }catch(e){toast('ล้มเหลว: '+e.message,1)}
}

async function saveWht(id,soId){
  if(!ADM)return;
  const inp=document.getElementById('wht-'+id),btn=document.getElementById('wht-sv-'+id);
  btn.disabled=true;
  try{
    const res=await fetch('/deposit/update-wht',{method:'POST',headers:H(),body:JSON.stringify({deposit_id:id,so_id:soId,wht_doc_no:inp.value.trim(),saved_by:CU})});
    const d=await res.json().catch(()=>({success:false}));
    if(!res.ok||!d.success)throw new Error(d.message||'HTTP '+res.status);
    inp.dataset.o=inp.value;inp.classList.remove('dirty');btn.classList.add('ok');
    toast('บันทึก WHT สำเร็จ — สถานะเปลี่ยนเป็น "มี WHT"');setTimeout(()=>location.reload(),800);
  }catch(e){toast('ล้มเหลว: '+e.message,1);btn.disabled=false}
}

// ✅ ลบ WHT (เซ็ตเป็นว่าง)
async function clearWht(id,soId){
  if(!ADM||!confirm('ลบ WHT ของรายการนี้?'))return;
  try{
    const res=await fetch('/deposit/update-wht',{method:'POST',headers:H(),body:JSON.stringify({deposit_id:id,so_id:soId,wht_doc_no:'',saved_by:CU})});
    const d=await res.json().catch(()=>({success:false}));
    if(!res.ok||!d.success)throw new Error(d.message||'HTTP '+res.status);
    toast('ลบ WHT สำเร็จ');setTimeout(()=>location.reload(),600);
  }catch(e){toast('ล้มเหลว: '+e.message,1)}
}

function openInfo(d){
  pInfoHasSlip=!!d.has_slip;  
  const $=i=>document.getElementById(i);
  $('im-t').textContent=d.deposit_bill_id||d.so_id||'รายละเอียด';
  $('im-bill').textContent=d.deposit_bill_id||'—';
  $('im-so').textContent=d.so_id||'—';
  $('im-po').textContent=d.cust_po||'—';
  $('im-date').textContent=d.time||'—';
  $('im-cust').textContent=d.customer_name||'—';
  $('im-con').textContent=d.contactso||'—';
  $('im-tel').textContent=d.customer_tel||'—';
  $('im-addr').textContent=d.customer_address||'—';
  $('im-emp').textContent=d.emp_name||'—';
  $('im-sale').textContent=d.sale_name||'—';
  $('im-type').textContent=(d.dep_type_name||'—')+' / '+d.dep_per.toFixed(2)+'%';
  $('im-note').textContent=d.note||'—';
  $('im-grand').textContent=fM(d.grand_total_vat)+' ฿';
  $('im-dep').textContent=fM(d.dep_price_vat)+' ฿';
  $('im-fee').textContent=d.fee_amount>0?fM(d.fee_amount)+' ฿':'—';
  $('im-net').textContent=fM(d.net)+' ฿';
  $('im-wht2').textContent=d.wht_doc_no||'—';
  $('im-remain').textContent=fM(d.remaining)+' ฿';
  let sb='';
  if(d.is_cancelled)sb='<span class="bg bg-cancel">ยกเลิก</span>';
  else if(d.is_wht)sb='<span class="bg bg-wht">มี WHT</span>';
  else if(d.is_confirmed)sb='<span class="bg bg-ok">'+d.status+'</span>';
  else if(d.status==='รอยืนยัน')sb='<span class="bg bg-wait">รอยืนยัน</span>';
  else sb='<span class="bg bg-wip">'+d.status+'</span>';
  $('im-st').innerHTML=sb;
  $('im-tc').textContent=d.time_check||'—';
  $('im-sl').textContent=d.slip_time||'ยังไม่แนบ';
let f='';
  if(ADM&&!d.is_cancelled){
    if(d.is_confirmed||d.is_wht){
      f+=`<button class="btn-st bk" onclick="askSt('${d.so_id}',${d.id},'${d.status}','รอยืนยัน')">↩ กลับรอ</button>`;
    } else if(d.status==='รอยืนยัน'){
      if(d.has_slip){
        f+=`<button class="btn-st cfm" onclick="askSt('${d.so_id}',${d.id},'${d.status}','ยืนยัน')">✓ ยืนยัน</button>`;
      } else {
        // ยังไม่แนบหลักฐานการชำระ — แสดงปุ่มเทากดไม่ได้ + บอกเหตุผล
        f+=`<button class="btn-st cfm" disabled style="opacity:.45;cursor:not-allowed" title="ต้องแนบหลักฐานการชำระก่อนจึงจะยืนยันได้">✓ ยืนยัน</button>`;
        f+=`<span style="font-size:10px;color:var(--red);font-weight:600;display:inline-flex;align-items:center;gap:3px">⚠ ต้องแนบหลักฐานการชำระก่อน</span>`;
      }
    }
    f+=`<button class="btn-del" onclick="askDel(${d.id},'${esc(d.so_id)}','${esc(d.customer_name)}')">🗑 ลบ</button>`;
  }
  f+='<div style="flex:1"></div><button class="btn-g" onclick="closeInfo()">ปิด</button>';
  $('im-f').innerHTML=f;
  $('infoM').classList.add('open');document.body.style.overflow='hidden';
}
function closeInfo(){document.getElementById('infoM').classList.remove('open');document.body.style.overflow=''}
document.getElementById('infoM').addEventListener('click',function(e){if(e.target===this)closeInfo()});
let pInfoHasSlip=null;
let pA=null;
function askSt(so,id,cur,nxt){
    if(nxt==='ยืนยัน' && pInfoHasSlip===false){
    toast('ต้องแนบหลักฐานการชำระก่อนจึงจะยืนยันได้',1);
    return;
  }
  pA={t:'status',so,id,cur,nxt};
  const $=i=>document.getElementById(i);
  if(nxt==='ยืนยัน'){$('cf-ico').className='cf-ico g';$('cf-ico').textContent='✓';$('cf-t').textContent='ยืนยัน?';$('cf-m').innerHTML='<b>รอยืนยัน</b> → <b style="color:#047857">ยืนยัน</b>';$('cf-btn').className='cf-ok';$('cf-btn').textContent='ยืนยัน'}
  else{$('cf-ico').className='cf-ico w';$('cf-ico').textContent='!';$('cf-t').textContent='เปลี่ยนกลับ?';$('cf-m').innerHTML='<b>'+cur+'</b> → <b style="color:#D97706">รอยืนยัน</b>';$('cf-btn').className='cf-ok';$('cf-btn').style.background='#D97706';$('cf-btn').textContent='เปลี่ยนกลับ'}
  $('cf-btn').disabled=false;$('cfM').classList.add('open');
}
function askDel(id,so,cn){
  pA={t:'delete',id,so,cn};
  const $=i=>document.getElementById(i);
  $('cf-ico').className='cf-ico d';$('cf-ico').textContent='🗑';$('cf-t').textContent='ลบรายการนี้?';
  $('cf-m').innerHTML='<b>ลบถาวร</b> ไม่กู้คืน<br>SO: <b>'+so+'</b>';
  $('cf-btn').className='cf-dl';$('cf-btn').textContent='ลบถาวร';$('cf-btn').disabled=false;$('cfM').classList.add('open');
}
function closeCf(){document.getElementById('cfM').classList.remove('open');pA=null;document.getElementById('cf-btn').style.background=''}
async function doCf(){
  if(!pA)return;const btn=document.getElementById('cf-btn');btn.disabled=true;btn.textContent='กำลังดำเนินการ...';
  try{
    let url,body;
    if(pA.t==='delete'){url='/deposit/delete';body={deposit_id:pA.id,deleted_by:CU}}
    else{url='/deposit/update-status';body={so_id:pA.so,deposit_id:pA.id,new_status:pA.nxt,changed_by:CU}}
    const res=await fetch(url,{method:'POST',headers:H(),body:JSON.stringify(body)});
    const d=await res.json().catch(()=>({success:false}));
    if(!res.ok||!d.success)throw new Error(d.message||'HTTP '+res.status);
    toast((pA.t==='delete'?'ลบ':'เปลี่ยนสถานะ')+'สำเร็จ');closeCf();closeInfo();setTimeout(()=>location.reload(),600);
  }catch(e){toast('ล้มเหลว: '+e.message,1);btn.disabled=false;btn.textContent=pA.t==='delete'?'ลบถาวร':'ยืนยัน'}
}
document.getElementById('cfM').addEventListener('click',function(e){if(e.target===this)closeCf()});

let sBid=null,sFile=null;
function openSlip(bid){sBid=bid;sFile=null;document.getElementById('sl-bid').textContent='('+bid+')';document.getElementById('slF').value='';const t=new Date();document.getElementById('slD').value=t.getFullYear()+'-'+String(t.getMonth()+1).padStart(2,'0')+'-'+String(t.getDate()).padStart(2,'0');document.getElementById('slP').classList.remove('show');document.getElementById('slP').innerHTML='';document.getElementById('slBtn').disabled=true;document.getElementById('slM').classList.add('open')}
function closeSl(){document.getElementById('slM').classList.remove('open');sBid=null;sFile=null}
function onSlF(e){const f=e.target.files[0];if(!f)return;if(f.size>10*1024*1024){toast('ไฟล์เกิน 10 MB',1);e.target.value='';return}sFile=f;const p=document.getElementById('slP');p.classList.add('show');if(f.type.startsWith('image/')){const r=new FileReader();r.onload=v=>{p.innerHTML=`<img src="${v.target.result}">`};r.readAsDataURL(f)}else{p.innerHTML=`<div style="padding:10px;background:var(--rh);border-radius:5px;font-size:11px">📄 ${f.name}</div>`}document.getElementById('slBtn').disabled=false}
const slDr=document.getElementById('slDrop');
if(slDr){['dragover','dragenter'].forEach(v=>{slDr.addEventListener(v,e=>{e.preventDefault();slDr.classList.add('dov')})});['dragleave','drop'].forEach(v=>{slDr.addEventListener(v,e=>{e.preventDefault();slDr.classList.remove('dov')})});slDr.addEventListener('drop',e=>{const f=e.dataTransfer.files[0];if(!f)return;const inp=document.getElementById('slF');const dt=new DataTransfer();dt.items.add(f);inp.files=dt.files;onSlF({target:inp})})}
async function doSl(){
  if(!sBid||!sFile){toast('เลือกไฟล์',1);return}const sd=document.getElementById('slD').value;if(!sd){toast('เลือกวันที่',1);return}
  const btn=document.getElementById('slBtn');btn.disabled=true;btn.textContent='กำลังอัปโหลด...';
  const fd=new FormData();fd.append('deposit_bill_id',sBid);fd.append('slip_file',sFile);fd.append('slip_date',sd);fd.append('uploaded_by',CU);
  try{const res=await fetch('/deposit/upload-slip',{method:'POST',headers:{'X-CSRF-TOKEN':CS,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},body:fd});const d=await res.json().catch(()=>({success:false}));if(!res.ok||!d.success)throw new Error(d.message||'HTTP '+res.status);toast('อัปโหลดสำเร็จ');closeSl();setTimeout(()=>location.reload(),600)}
  catch(e){toast('ล้มเหลว: '+e.message,1);btn.disabled=false;btn.textContent='อัปโหลด'}
}
document.getElementById('slM').addEventListener('click',function(e){if(e.target===this)closeSl()});

function esc(s){return(s||'').replace(/'/g,"\\'").replace(/"/g,'&quot;')}
function fD(d){if(!d)return'—';const t=new Date(d);if(isNaN(t))return d;return String(t.getDate()).padStart(2,'0')+'/'+String(t.getMonth()+1).padStart(2,'0')+'/'+t.getFullYear()}
function fM(v){return parseFloat(v||0).toLocaleString('th-TH',{minimumFractionDigits:2,maximumFractionDigits:2})}
function toast(m,e){const t=document.getElementById('toast');document.getElementById('toastM').textContent=m;t.classList.toggle('err',!!e);t.classList.add('show');setTimeout(()=>t.classList.remove('show'),2600)}
document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeInfo();closeCf();closeSl()}});
</script>
</body>
</html>