{{-- resources/views/account/bill_doc_report.blade.php — PDF สรุปยอดบิล (A4) --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สรุปยอดบิล {{ $period_thai }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
@page { size: A4 portrait; margin: 16mm 14mm; }
*{box-sizing:border-box}
body{font-family:'Sarabun',sans-serif;color:#1f2937;margin:0;background:#eef1f5}
.sheet{background:#fff;width:210mm;min-height:297mm;margin:16px auto;padding:18mm 16mm;box-shadow:0 2px 12px rgba(0,0,0,.12)}
.head{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #1f2937;padding-bottom:12px;margin-bottom:6px}
h1{font-size:22px;margin:0 0 2px}
.sub{color:#64748b;font-size:13px}
.meta{text-align:right;font-size:12px;color:#64748b;line-height:1.7}
table{width:100%;border-collapse:collapse;margin-top:18px;font-size:14px}
th,td{border:1px solid #cbd5e1;padding:10px 12px;text-align:right}
th{background:#1f2937;color:#fff;text-align:center;font-weight:600}
td.l,th.l{text-align:left}
td.c{text-align:center}
.row-goods td{background:#eff6ff}
.row-service td{background:#fdf2f8}
.row-cancel td{background:#fef2f2;color:#b91c1c}
.row-total td{background:#f1f5f9;font-weight:700;font-size:15px}
.row-grand td{background:#111827;color:#fff;font-weight:700;font-size:16px;border-color:#111827}
.money{font-family:'Sarabun',sans-serif;white-space:nowrap}
.note{margin-top:14px;font-size:12px;color:#64748b}
.docbar{display:flex;gap:10px;margin:18px 0 0}
.docbox{flex:1;border:1px solid #cbd5e1;border-radius:8px;padding:10px 14px}
.docbox .k{font-size:12px;color:#64748b}
.docbox .v{font-size:20px;font-weight:700}
.sign{display:flex;justify-content:space-between;margin-top:48px;font-size:13px;color:#334155}
.sign div{width:45%;text-align:center}
.sign .line{margin-top:40px;border-top:1px dotted #94a3b8;padding-top:6px}
.toolbar{max-width:210mm;margin:12px auto 0;text-align:center}
.btn{background:#3E6AE1;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-family:inherit;font-size:15px;font-weight:600;cursor:pointer}
@media print{ body{background:#fff} .sheet{box-shadow:none;margin:0;width:auto} .toolbar{display:none} }
</style>
</head>
<body>
<div class="toolbar"><button class="btn" onclick="window.print()">🖨️ พิมพ์ / บันทึกเป็น PDF</button></div>

@php
  function _m($n){ return number_format((float)$n, 2); }
@endphp

<div class="sheet">
  <div class="head">
    <div>
      <h1>สรุปยอดบิลขาย</h1>
      <div class="sub">ประจำเดือน {{ $period_thai }}</div>
    </div>
    <div class="meta">
      พิมพ์โดย: {{ $printed_by }}<br>
      วันที่พิมพ์: {{ $printed_at }}
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th class="l" style="width:34%">ประเภท</th>
        <th style="width:12%">จำนวนบิล</th>
        <th style="width:18%">ก่อน VAT</th>
        <th style="width:18%">VAT</th>
        <th style="width:18%">รวม (หลัง VAT)</th>
      </tr>
    </thead>
    <tbody>
      <tr class="row-goods">
        <td class="l">ขายสินค้า</td>
        <td class="c">{{ number_format($goods['count']) }}</td>
        <td class="money">{{ _m($goods['before']) }}</td>
        <td class="money">{{ _m($goods['vat']) }}</td>
        <td class="money">{{ _m($goods['after']) }}</td>
      </tr>
      <tr class="row-service">
        <td class="l">งานบริการ</td>
        <td class="c">{{ number_format($service['count']) }}</td>
        <td class="money">{{ _m($service['before']) }}</td>
        <td class="money">{{ _m($service['vat']) }}</td>
        <td class="money">{{ _m($service['after']) }}</td>
      </tr>
      @if($untyped['count'] > 0)
      <tr>
        <td class="l">ยังไม่ระบุประเภท</td>
        <td class="c">{{ number_format($untyped['count']) }}</td>
        <td class="money">{{ _m($untyped['before']) }}</td>
        <td class="money">{{ _m($untyped['vat']) }}</td>
        <td class="money">{{ _m($untyped['after']) }}</td>
      </tr>
      @endif
      <tr class="row-total">
        <td class="l">รวมยอดขาย (ไม่รวมยกเลิก)</td>
        <td class="c">{{ number_format($valid_total['count']) }}</td>
        <td class="money">{{ _m($valid_total['before']) }}</td>
        <td class="money">{{ _m($valid_total['vat']) }}</td>
        <td class="money">{{ _m($valid_total['after']) }}</td>
      </tr>
      <tr class="row-cancel">
        <td class="l">บิลยกเลิก — ขายสินค้า</td>
        <td class="c">{{ number_format($cancelled_goods['count']) }}</td>
        <td class="money">{{ _m($cancelled_goods['before']) }}</td>
        <td class="money">{{ _m($cancelled_goods['vat']) }}</td>
        <td class="money">{{ _m($cancelled_goods['after']) }}</td>
      </tr>
      <tr class="row-cancel">
        <td class="l">บิลยกเลิก — บริการ</td>
        <td class="c">{{ number_format($cancelled_service['count']) }}</td>
        <td class="money">{{ _m($cancelled_service['before']) }}</td>
        <td class="money">{{ _m($cancelled_service['vat']) }}</td>
        <td class="money">{{ _m($cancelled_service['after']) }}</td>
      </tr>
      <tr class="row-cancel" style="font-weight:700">
        <td class="l">รวมบิลยกเลิก</td>
        <td class="c">{{ number_format($cancelled['count']) }}</td>
        <td class="money">{{ _m($cancelled['before']) }}</td>
        <td class="money">{{ _m($cancelled['vat']) }}</td>
        <td class="money">{{ _m($cancelled['after']) }}</td>
      </tr>
    </tbody>
  </table>

  <div class="docbar">
    <div class="docbox"><div class="k">มีเอกสารแล้ว</div><div class="v" style="color:#16a34a">{{ number_format($has_document) }}</div></div>
    <div class="docbox"><div class="k">พบแต่ไม่ได้เซ็นบิล</div><div class="v" style="color:#d97706">{{ number_format($not_signed) }}</div></div>
    <div class="docbox"><div class="k">ยังไม่มีเอกสาร</div><div class="v" style="color:#dc2626">{{ number_format($missing_doc) }}</div></div>
    <div class="docbox"><div class="k">ไม่พบเอกสาร (ไม่คิดยอด)</div><div class="v" style="color:#6b7280">{{ number_format($not_found) }}</div></div>
  </div>

  <div class="note">
    * ยอดขายคำนวณจากบิลที่ไม่ถูกยกเลิก และไม่ใช่ "ไม่พบเอกสาร" · ประเภทสินค้า/บริการแยกจากชื่อรายการ · มูลค่ารวม VAT จาก NetAmnt
  </div>

  <div class="sign">
    <div><div class="line">ผู้จัดทำ</div></div>
    <div><div class="line">ผู้ตรวจสอบ</div></div>
  </div>
</div>
</body>
</html>
