<!DOCTYPE html>
<html><head><meta charset="UTF-8">
@php $m = fn($n)=>number_format((float)$n,2); @endphp
<style>
  @font-face{ font-family:"THSarabun"; font-weight:normal;
    src:url(data:font/truetype;charset=utf-8;base64,{!! $fontNormal !!}) format("truetype"); }
  @font-face{ font-family:"THSarabun"; font-weight:bold;
    src:url(data:font/truetype;charset=utf-8;base64,{!! $fontBold !!}) format("truetype"); }
  *{font-family:"THSarabun",sans-serif}
  body{font-size:17px;color:#1f2937;margin:0}
  h1{font-size:20px;margin:0 0 2px}
  .sub{color:#555;font-size:14px;margin-bottom:8px}
  .po{border:1px solid #cbd5e1;border-radius:4px;margin-bottom:8px;padding:6px 8px;page-break-inside:avoid;break-inside:avoid}
  tr{page-break-inside:avoid}
  .po-sum{page-break-inside:avoid}
  .po-head{background:#eff6ff;padding:5px 7px;border-radius:3px;font-weight:bold;font-size:16px}
  .po-head .r{float:right}
  table{width:100%;border-collapse:collapse;margin-top:5px;table-layout:fixed}
  th,td{border:1px solid #d1d5db;padding:5px 7px;font-size:14.5px;word-wrap:break-word;word-break:break-word;overflow-wrap:break-word}
  th{background:#f1f5f9}
  td.r,th.r{text-align:right}
  td.name{white-space:normal}
  .po-sum{margin-top:5px;font-size:15px;font-weight:bold}
  .po-sum table{width:300px;margin-left:auto;margin-top:0;table-layout:auto}
  .po-sum td{border:none;padding:2px 6px}
  .po-sum td.k{text-align:left;color:#555;font-weight:normal}
  .po-sum td.val{text-align:right}
  .po-sum .b{color:#2563eb}.po-sum .v{color:#d97706}.po-sum .s{color:#16a34a}
  .grand{margin-top:10px;background:#111827;color:#fff;padding:10px;border-radius:4px;font-weight:bold;font-size:16px}
  .grand .r{float:right}
</style></head><body>
  <h1>สรุป PO ที่เชื่อมกับ SO {{ $so }}</h1>
  <div class="sub">ไม่รวม PO ที่ยกเลิก · จำนวน {{ $totals['count'] }} PO · พิมพ์ {{ $printed_at }}</div>

  @foreach($pos as $p)
  <div class="po">
    <div class="po-head">
      {{ $p['po'] }} — {{ $p['vendor_name'] }} ({{ $p['vendor_id'] }})
      <span class="r">วันที่เอกสาร {{ $p['date'] ?: '-' }}</span>
    </div>
    <table>
      <thead><tr><th>รายการสินค้า</th><th class="r" style="width:70px">จำนวน</th><th class="r" style="width:110px">ราคา/หน่วย</th><th class="r" style="width:120px">ยอด</th></tr></thead>
      <tbody>
        @forelse($p['lines'] as $ln)
        <tr>
          <td class="name">{{ $ln['name'] ?: '-' }}</td>
          <td class="r">{{ rtrim(rtrim(number_format($ln['qty'],4),'0'),'.') }}</td>
          <td class="r">{{ $m($ln['price']) }}</td>
          <td class="r">{{ $m($ln['amount']) }}</td>
        </tr>
        @empty
        <tr><td colspan="4">- ไม่มีรายการ -</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="po-sum">
      <table>
        <tr><td class="k">ก่อน VAT</td><td class="val b">{{ $m($p['before']) }}</td></tr>
        <tr><td class="k">VAT</td><td class="val v">{{ $m($p['vat']) }}</td></tr>
        <tr><td class="k">รวม</td><td class="val s">{{ $m($p['after']) }}</td></tr>
      </table>
    </div>
  </div>
  @endforeach

  <div class="grand">
    <div style="margin-bottom:4px">รวมทั้งหมด {{ $totals['count'] }} PO</div>
    <div>ก่อน VAT : {{ $m($totals['before']) }}</div>
    <div>VAT : {{ $m($totals['vat']) }}</div>
    <div>รวมทั้งสิ้น : {{ $m($totals['after']) }}</div>
  </div>
</body></html>
