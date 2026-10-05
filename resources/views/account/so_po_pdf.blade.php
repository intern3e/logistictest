<!DOCTYPE html>
<html><head><meta charset="UTF-8">
@php $m = fn($n)=>number_format((float)$n,2); @endphp
<style>
  @font-face{ font-family:"THSarabun"; font-weight:normal;
    src:url(data:font/truetype;charset=utf-8;base64,{!! $fontNormal !!}) format("truetype"); }
  @font-face{ font-family:"THSarabun"; font-weight:bold;
    src:url(data:font/truetype;charset=utf-8;base64,{!! $fontBold !!}) format("truetype"); }
  *{font-family:"THSarabun",sans-serif}
  @page{ margin:14mm 10mm; }
  body{font-size:14px;color:#1f2937;margin:0}
  h1{font-size:19px;margin:0 0 2px}
  .sub{color:#555;font-size:13px;margin-bottom:10px}
  h2{font-size:17px;margin:10px 0 4px;padding:5px 9px;border-radius:4px;color:#fff}
  h2.goods{background:#1d4ed8}
  h2.service{background:#b45309}
  table{width:100%;border-collapse:collapse}
  th,td{border:1px solid #d1d5db;padding:2px 6px;font-size:13.5px;vertical-align:top;line-height:1.12}
  th{background:#f1f5f9;font-weight:bold}
  td.r,th.r{text-align:right;white-space:nowrap}
  td.c,th.c{text-align:center;white-space:nowrap}
  td.ven{word-break:break-word;overflow-wrap:break-word}
  .it{font-size:11.5px;color:#334155;padding-left:6px;line-height:1.1}
  .itq{color:#2563eb;white-space:nowrap}
  .po-no{font-weight:bold;white-space:nowrap}
  .sub-tot td{background:#eef2ff;font-weight:bold}
  .grand td{background:#111827;color:#fff;font-weight:bold;font-size:15px}
</style></head><body>
  <h1>สรุป PO ที่เชื่อมกับ SO {{ $so }} <span style="font-size:13px;font-weight:normal;color:#777">(ไม่รวม PO ยกเลิก · {{ $totals['count'] }} PO · วันที่เอกสาร)</span></h1>

  @php
    $block = function($title, $cls, $list, $stot) use ($m) {
      return [$title,$cls,$list,$stot];
    };
  @endphp

  @foreach([['ขายสินค้า','goods',$goods,$goods_total],['ค่าแรง / ค่าบริการ','service',$service,$service_total]] as $sec)
    @php [$title,$cls,$list,$stot] = $sec; @endphp
    <h2 class="{{ $cls }}" @if($cls==='service') style="page-break-before:always" @endif>PO {{ $title }} ({{ $stot['count'] }} รายการ)</h2>
    <table>
      <thead>
        <tr>
          <th style="width:78px">PO</th>
          <th>ผู้ขาย / รายการสินค้า</th>
          <th class="c" style="width:72px">วันที่เอกสาร</th>
          <th class="r" style="width:80px">ก่อน VAT</th>
          <th class="r" style="width:64px">VAT</th>
          <th class="r" style="width:86px">รวม</th>
        </tr>
      </thead>
      <tbody>
        @forelse($list as $p)
          <tr>
            <td class="po-no">{{ $p['po'] }}</td>
            <td class="ven">
              <div style="font-weight:bold">{{ $p['vendor_name'] ?: '-' }} <span style="color:#777;font-weight:normal">({{ $p['vendor_id'] }})</span></div>
              @if(!empty($p['lines']))
                @foreach($p['lines'] as $ln)
                  <div class="it">• {{ $ln['name'] ?: '-' }} <span class="itq">[{{ rtrim(rtrim(number_format($ln['qty'],2),'0'),'.') }} x {{ $m($ln['price']) }} = {{ $m($ln['amount']) }}]</span></div>
                @endforeach
              @endif
            </td>
            <td class="c">{{ $p['date'] ?: '-' }}</td>
            <td class="r">{{ $m($p['before']) }}</td>
            <td class="r">{{ $m($p['vat']) }}</td>
            <td class="r">{{ $m($p['after']) }}</td>
          </tr>
        @empty
          <tr><td colspan="6" class="c" style="color:#999">- ไม่มี -</td></tr>
        @endforelse
        <tr class="sub-tot">
          <td colspan="3" class="r">รวม {{ $title }}</td>
          <td class="r">{{ $m($stot['before']) }}</td>
          <td class="r">{{ $m($stot['vat']) }}</td>
          <td class="r">{{ $m($stot['after']) }}</td>
        </tr>
      </tbody>
    </table>
  @endforeach

  <table style="margin-top:12px">
    <tr class="grand">
      <td colspan="3" class="r">รวมทั้งหมด {{ $totals['count'] }} PO</td>
      <td class="r">{{ $m($totals['before']) }}</td>
      <td class="r">{{ $m($totals['vat']) }}</td>
      <td class="r">{{ $m($totals['after']) }}</td>
    </tr>
  </table>
</body></html>
