<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head><body>
@php $m = fn($n)=>number_format((float)$n,2); @endphp
<table border="1">
  <tr><th colspan="6" style="font-size:16px;background:#1f2937;color:#fff">สรุป PO ที่เชื่อมกับ SO {{ $so }} (ไม่รวม PO ยกเลิก) — {{ $printed_at }}</th></tr>
  <tr style="background:#e5e7eb;font-weight:bold">
    <td>PO</td><td>ผู้ขาย (รหัส)</td><td>ผู้ขาย</td><td>วันที่เอกสาร</td><td>รายการ</td><td>ยอด</td>
  </tr>
  @foreach($pos as $p)
    <tr style="background:#dbeafe;font-weight:bold">
      <td>{{ $p['po'] }}</td>
      <td>{{ $p['vendor_id'] }}</td>
      <td>{{ $p['vendor_name'] }}</td>
      <td>{{ $p['date'] }}</td>
      <td>{{ count($p['lines']) }} รายการ</td>
      <td>&nbsp;</td>
    </tr>
    <tr style="background:#f1f5f9;font-style:italic">
      <td>&nbsp;</td><td>ชื่อสินค้า</td><td>จำนวน</td><td>ราคา/หน่วย</td><td>&nbsp;</td><td>ยอด</td>
    </tr>
    @forelse($p['lines'] as $ln)
    <tr>
      <td>&nbsp;</td>
      <td>{{ $ln['name'] }}</td>
      <td>{{ rtrim(rtrim(number_format($ln['qty'],4),'0'),'.') }}</td>
      <td>{{ $m($ln['price']) }}</td>
      <td>&nbsp;</td>
      <td>{{ $m($ln['amount']) }}</td>
    </tr>
    @empty
    <tr><td>&nbsp;</td><td colspan="5">- ไม่มีรายการ -</td></tr>
    @endforelse
    <tr style="font-weight:bold"><td>&nbsp;</td><td colspan="4" style="text-align:right">ก่อน VAT ของ PO {{ $p['po'] }}</td><td>{{ $m($p['before']) }}</td></tr>
    <tr style="font-weight:bold"><td>&nbsp;</td><td colspan="4" style="text-align:right">VAT</td><td>{{ $m($p['vat']) }}</td></tr>
    <tr style="font-weight:bold"><td>&nbsp;</td><td colspan="4" style="text-align:right">รวม</td><td>{{ $m($p['after']) }}</td></tr>
    <tr><td colspan="6">&nbsp;</td></tr>
  @endforeach
  <tr style="background:#111827;color:#fff;font-weight:bold"><td colspan="5" style="text-align:right">รวมทั้งหมด {{ $totals['count'] }} PO — ก่อน VAT</td><td>{{ $m($totals['before']) }}</td></tr>
  <tr style="background:#111827;color:#fff;font-weight:bold"><td colspan="5" style="text-align:right">VAT</td><td>{{ $m($totals['vat']) }}</td></tr>
  <tr style="background:#111827;color:#fff;font-weight:bold"><td colspan="5" style="text-align:right">รวมทั้งสิ้น</td><td>{{ $m($totals['after']) }}</td></tr>
</table>
</body></html>
