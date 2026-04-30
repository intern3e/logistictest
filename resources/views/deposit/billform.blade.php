<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ใบมัดจำ {{ $deposit_bill_id }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --navy:#0B2447; --navy-mid:#19376D; --navy-light:#EAF1FB; --navy-border:#C5D6EC;
  --text:#0F172A; --text-secondary:#475569; --text-muted:#94A3B8;
  --border:#C5D6EC; --border-light:#E5EBF3;
  --bg:#EEF2F7; --surface:#FFFFFF;
  --red:#9B1B1B;
}
html,body{font-family:'Sarabun',system-ui,sans-serif;color:var(--text);background:var(--bg);font-size:14px;line-height:1.5}
.toolbar{position:sticky;top:0;z-index:100;background:var(--surface);border-bottom:1px solid var(--border);padding:12px 24px;display:flex;justify-content:space-between;align-items:center;gap:12px;box-shadow:0 1px 3px rgba(11,36,71,.08)}
.tb-left{display:flex;align-items:center;gap:10px}
.tb-title{font-size:14px;font-weight:700;color:var(--navy)}
.tb-sub{font-size:12px;color:var(--text-muted)}
.tb-actions{display:flex;gap:8px}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:1px solid var(--border);background:var(--surface);color:var(--text-secondary);font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:.15s}
.btn:hover{border-color:var(--navy);color:var(--navy);background:#F8FAFC}
.btn-primary{background:var(--navy);color:#fff;border-color:var(--navy)}
.btn-primary:hover{background:var(--navy-mid);color:#fff}

/* ===== Document ===== */
.page{max-width:820px;margin:24px auto;background:var(--surface);box-shadow:0 4px 20px rgba(11,36,71,.10);padding:48px 56px;border:1px solid var(--border)}
.doc-head{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px double var(--navy);padding-bottom:18px;margin-bottom:22px}
.doc-head-left h1{font-size:22px;font-weight:800;color:var(--navy);letter-spacing:-.3px;margin-bottom:4px}
.doc-head-left .sub{font-size:13px;color:var(--text-secondary);font-weight:600}
.doc-head-right{text-align:right;font-size:13px}
.doc-head-right .label{color:var(--text-muted);font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px}
.doc-head-right .billno{font-size:20px;font-weight:800;color:var(--navy);font-family:'Sarabun',monospace;letter-spacing:.5px}
.doc-head-right .date{margin-top:6px;color:var(--text-secondary)}

.section-title{font-size:11px;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;padding-bottom:5px;border-bottom:1px solid var(--border-light)}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px}
.info-block .row{display:flex;padding:5px 0;font-size:13px;line-height:1.5}
.info-block .k{color:var(--text-muted);min-width:90px;flex-shrink:0}
.info-block .v{color:var(--text);font-weight:500;flex:1}

table.dep-table{width:100%;border-collapse:collapse;margin-bottom:18px;font-size:13px}
table.dep-table thead{background:var(--navy);color:#fff}
table.dep-table th{padding:10px 12px;text-align:left;font-size:12px;font-weight:700;letter-spacing:.03em}
table.dep-table th.num{text-align:right}
table.dep-table th.center{text-align:center}
table.dep-table td{padding:10px 12px;border-bottom:1px solid var(--border-light);vertical-align:top}
table.dep-table td.num{text-align:right;font-variant-numeric:tabular-nums;font-weight:600}
table.dep-table td.center{text-align:center}
table.dep-table tbody tr:last-child td{border-bottom:1px solid var(--border)}
.type-badge{display:inline-block;padding:2px 8px;font-size:11px;font-weight:700;background:var(--navy-light);color:var(--navy);border:1px solid var(--navy-border)}

.summary{display:flex;justify-content:flex-end;margin-bottom:32px}
.summary-box{width:340px;border:1px solid var(--border)}
.summary-box .row{display:flex;justify-content:space-between;padding:8px 14px;font-size:13px;border-bottom:1px solid var(--border-light)}
.summary-box .row:last-child{border-bottom:none}
.summary-box .row .lbl{color:var(--text-secondary)}
.summary-box .row .val{font-weight:600;font-variant-numeric:tabular-nums}
.summary-box .row.total{background:var(--navy);color:#fff;padding:12px 14px}
.summary-box .row.total .lbl{color:#fff;font-weight:700}
.summary-box .row.total .val{color:#fff;font-weight:800;font-size:16px}
.summary-box .row.deposit .val{color:var(--red)}

.amount-words{padding:10px 14px;background:var(--navy-light);border:1px solid var(--navy-border);font-size:13px;margin-bottom:32px;display:flex;gap:10px;align-items:center}
.amount-words .lbl{font-size:11px;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:.05em}
.amount-words .val{font-weight:600;color:var(--text)}

.signatures{display:grid;grid-template-columns:1fr 1fr;gap:48px;margin-top:48px;padding-top:24px}
.sig-block{text-align:center}
.sig-line{border-bottom:1px dotted var(--text-muted);height:60px;margin-bottom:8px}
.sig-label{font-size:13px;color:var(--text-secondary);font-weight:600}
.sig-name{font-size:12px;color:var(--text-muted);margin-top:3px}

.footer{margin-top:36px;padding-top:14px;border-top:1px solid var(--border-light);text-align:center;font-size:11px;color:var(--text-muted)}

.status-stamp{position:absolute;top:130px;right:80px;border:3px solid;padding:8px 24px;font-size:18px;font-weight:800;letter-spacing:.1em;transform:rotate(-12deg);opacity:.85;text-transform:uppercase}
.status-confirmed{color:#16A34A;border-color:#16A34A}
.status-pending{color:#D97706;border-color:#D97706}

/* ===== Print ===== */
@media print {
  @page { size: A4; margin: 12mm 14mm; }
  body { background: white; font-size: 12pt; }
  .toolbar { display: none !important; }
  .page { box-shadow:none !important; border:none !important; margin:0 !important; padding:0 !important; max-width:100% !important; }
  .status-stamp { right: 40px; top: 100px; }
}

@media (max-width:768px){
  .page{padding:24px 20px;margin:12px}
  .doc-head{flex-direction:column;gap:14px}
  .doc-head-right{text-align:left}
  .info-grid{grid-template-columns:1fr;gap:14px}
  .summary-box{width:100%}
  .signatures{grid-template-columns:1fr;gap:24px}
  .toolbar{padding:10px 14px;flex-wrap:wrap}
}
</style>
</head>
<body>

<div class="toolbar">
  <div class="tb-left">
    <div>
      <div class="tb-title">ใบมัดจำ {{ $deposit_bill_id }}</div>
      <div class="tb-sub">SO: {{ $header->so_id }} · {{ $header->customer_name }}</div>
    </div>
  </div>
  <div class="tb-actions">
    <button type="button" class="btn" onclick="history.back()">
      <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M9 11L5 7L9 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
      ย้อนกลับ
    </button>
    <button type="button" class="btn btn-primary" onclick="window.print()">
      <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 5V2h8v3M3 9H2V5h10v4h-1M4 8h6v4H4z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
      พิมพ์ / บันทึก PDF
    </button>
  </div>
</div>

<div class="page" style="position:relative">

  @if($header->status === 'ยืนยัน')
    <div class="status-stamp status-confirmed">ยืนยันแล้ว</div>
  @else
    <div class="status-stamp status-pending">รอยืนยัน</div>
  @endif

  {{-- Header --}}
  <div class="doc-head">
    <div class="doc-head-left">
      <h1>ใบรับเงินมัดจำ</h1>
      <div class="sub">DEPOSIT RECEIPT</div>
    </div>
    <div class="doc-head-right">
      <div class="label">เลขที่เอกสาร</div>
      <div class="billno">{{ $deposit_bill_id }}</div>
      <div class="date">
        วันที่ออก:
        @php
          $issueDate = $header->date_dep ? \Carbon\Carbon::parse($header->date_dep) : \Carbon\Carbon::parse($header->time);
        @endphp
        {{ $issueDate->format('d/m/') }}{{ $issueDate->year + 543 }}
      </div>
      <div class="date">SO: <strong>{{ $header->so_id }}</strong></div>
    </div>
  </div>

  {{-- Customer info --}}
  <div class="info-grid">
    <div class="info-block">
      <div class="section-title">ข้อมูลลูกค้า</div>
      <div class="row"><span class="k">รหัสลูกค้า</span><span class="v">{{ $header->customer_id ?: '—' }}</span></div>
      <div class="row"><span class="k">ชื่อ</span><span class="v">{{ $header->customer_name ?: '—' }}</span></div>
      <div class="row"><span class="k">ผู้ติดต่อ</span><span class="v">{{ $header->contactso ?: '—' }}</span></div>
      <div class="row"><span class="k">โทรศัพท์</span><span class="v">{{ $header->customer_tel ?: '—' }}</span></div>
    </div>
    <div class="info-block">
      <div class="section-title">ที่อยู่ / เพิ่มเติม</div>
      <div class="row"><span class="k">ที่อยู่</span><span class="v">{{ $header->customer_address ?: '—' }}</span></div>
      <div class="row"><span class="k">พนักงานขาย</span><span class="v">{{ $header->sale_name ?: '—' }}</span></div>
      <div class="row"><span class="k">ผู้สร้างเอกสาร</span><span class="v">{{ $header->emp_name ?: '—' }}</span></div>
      @if($header->time_check)
        <div class="row"><span class="k">ยืนยันเมื่อ</span>
          <span class="v">{{ \Carbon\Carbon::parse($header->time_check)->format('d/m/Y H:i') }} น.</span>
        </div>
      @endif
    </div>
  </div>

  {{-- Deposit table --}}
  <div class="section-title" style="margin-bottom:10px">รายการมัดจำ</div>
  <table class="dep-table">
    <thead>
      <tr>
        <th class="center" style="width:50px">ลำดับ</th>
        <th>ประเภท</th>
        <th class="num" style="width:110px">เปอร์เซ็นต์</th>
        <th class="num" style="width:160px">จำนวนเงิน (บาท)</th>
      </tr>
    </thead>
    <tbody>
      @php
        $typeLabel = ['product'=>'มัดจำสินค้า','service'=>'มัดจำบริการ','shipping'=>'มัดจำค่าขนส่ง'];
      @endphp
      @foreach($items as $i => $item)
        <tr>
          <td class="center">{{ $i + 1 }}</td>
          <td>
            <span class="type-badge">{{ $typeLabel[$item->dep_type] ?? $item->dep_type }}</span>
          </td>
          <td class="num">{{ number_format((float)$item->dep_per, 2) }}%</td>
          <td class="num">{{ number_format((float)$item->dep_price, 2) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  {{-- Summary --}}
  <div class="summary">
    <div class="summary-box">
      <div class="row">
        <span class="lbl">ยอดรวมตามใบสั่งขาย</span>
        <span class="val">฿ {{ number_format($grandTotal, 2) }}</span>
      </div>
      <div class="row deposit">
        <span class="lbl">รวมมัดจำที่ต้องชำระ</span>
        <span class="val">฿ {{ number_format($totalDeposit, 2) }}</span>
      </div>
      <div class="row">
        <span class="lbl">ยอดคงเหลือ</span>
        <span class="val">฿ {{ number_format($netRemaining, 2) }}</span>
      </div>
      <div class="row total">
        <span class="lbl">ยอดมัดจำสุทธิ</span>
        <span class="val">฿ {{ number_format($totalDeposit, 2) }}</span>
      </div>
    </div>
  </div>

  {{-- Amount in words --}}
  @php
    function bahtText($amount) {
      $txtnum1 = ['ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า'];
      $txtnum2 = ['','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน'];
      $number = number_format($amount, 2, '.', '');
      [$int, $dec] = explode('.', $number);
      $strBaht = ''; $strSatang = '';
      $intLen = strlen($int);
      for ($i = 0; $i < $intLen; $i++) {
          $d = (int)$int[$i];
          $pos = $intLen - $i - 1;
          if ($d == 0) continue;
          if ($pos == 0 && $d == 1 && $intLen > 1) { $strBaht .= 'เอ็ด'; }
          elseif ($pos == 1 && $d == 2) { $strBaht .= 'ยี่สิบ'; }
          elseif ($pos == 1 && $d == 1) { $strBaht .= 'สิบ'; }
          else { $strBaht .= $txtnum1[$d] . $txtnum2[$pos]; }
      }
      $strBaht .= 'บาท';
      $decN = (int)$dec;
      if ($decN == 0) { $strSatang = 'ถ้วน'; }
      else {
          $decLen = strlen($dec);
          for ($i = 0; $i < $decLen; $i++) {
              $d = (int)$dec[$i];
              $pos = $decLen - $i - 1;
              if ($d == 0) continue;
              if ($pos == 0 && $d == 1 && $decLen > 1) { $strSatang .= 'เอ็ด'; }
              elseif ($pos == 1 && $d == 2) { $strSatang .= 'ยี่สิบ'; }
              elseif ($pos == 1 && $d == 1) { $strSatang .= 'สิบ'; }
              else { $strSatang .= $txtnum1[$d] . $txtnum2[$pos]; }
          }
          $strSatang .= 'สตางค์';
      }
      return $strBaht . $strSatang;
    }
  @endphp
  <div class="amount-words">
    <span class="lbl">จำนวนเงิน (ตัวอักษร):</span>
    <span class="val">({{ bahtText($totalDeposit) }})</span>
  </div>

  {{-- Signatures --}}
  <div class="signatures">
    <div class="sig-block">
      <div class="sig-line"></div>
      <div class="sig-label">ผู้รับเงิน / ผู้มีอำนาจลงนาม</div>
      <div class="sig-name">วันที่ ............. / ............. / .............</div>
    </div>
    <div class="sig-block">
      <div class="sig-line"></div>
      <div class="sig-label">ผู้ชำระเงิน / ลูกค้า</div>
      <div class="sig-name">วันที่ ............. / ............. / .............</div>
    </div>
  </div>

  <div class="footer">
    เอกสารนี้ออกโดยระบบจัดการเอกสาร · พิมพ์เมื่อ {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} น.
  </div>

</div>

</body>
</html>