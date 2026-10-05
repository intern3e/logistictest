<!DOCTYPE html>
<html lang="th"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PO ของ SO {{ $so }}</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&family=JetBrains+Mono&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box}
body{font-family:'Sarabun',sans-serif;background:#f1f5f9;color:#1f2937;margin:0;padding:18px}
.wrap{max-width:1100px;margin:0 auto}
h1{font-size:20px;margin:0 0 2px}
.sub{color:#64748b;font-size:13px;margin-bottom:14px}
.bar{display:flex;gap:10px;margin-bottom:16px}
.btn{padding:9px 18px;border-radius:9px;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;border:1px solid #cbd5e1;color:#1f2937;background:#fff}
.btn-x{background:#16a34a;color:#fff;border-color:#16a34a}
.btn-p{background:#dc2626;color:#fff;border-color:#dc2626}
.po{background:#fff;border:1px solid #e5e7eb;border-radius:12px;margin-bottom:14px;overflow:hidden}
.po-head{background:#eff6ff;padding:10px 14px;font-weight:700;display:flex;justify-content:space-between;flex-wrap:wrap;gap:6px}
.po-head .po-no{color:#2563eb}
table{width:100%;border-collapse:collapse}
th,td{padding:8px 12px;border-bottom:1px solid #eef2f7;font-size:13px}
th{background:#f8fafc;color:#64748b;text-align:right}
th.l{text-align:left}
td.r{text-align:right;font-family:'JetBrains Mono',monospace}
.po-sum{padding:10px 14px;text-align:right;font-weight:700;font-family:'JetBrains Mono',monospace}
.b{color:#2563eb}.v{color:#d97706}.s{color:#16a34a}
.grand{background:#111827;color:#fff;border-radius:12px;padding:14px 16px;font-weight:700;display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px}
</style></head><body>
@php $m = fn($n)=>number_format((float)$n,2); @endphp
<div class="wrap">
  <h1>PO ที่เชื่อมกับ SO {{ $so }}</h1>
  <div class="sub">ไม่รวม PO ที่ยกเลิก · {{ $totals['count'] }} PO · พิมพ์ {{ $printed_at }}</div>
  <div class="bar">
    <a class="btn btn-x" href="{{ route('sopoexport.excel', ['SONum'=>$so]) }}">ดาวน์โหลด Excel</a>
    <a class="btn btn-p" href="{{ route('sopoexport.pdf', ['SONum'=>$so]) }}">ดาวน์โหลด PDF</a>
  </div>

  @foreach($pos as $p)
  <div class="po">
    <div class="po-head">
      <span><span class="po-no">{{ $p['po'] }}</span> — {{ $p['vendor_name'] }} ({{ $p['vendor_id'] }})</span>
      <span>{{ $p['date'] }}</span>
    </div>
    <table>
      <thead><tr><th class="l">รายการสินค้า</th><th style="width:80px">จำนวน</th><th style="width:120px">ราคา/หน่วย</th><th style="width:130px">ยอด</th></tr></thead>
      <tbody>
        @forelse($p['lines'] as $ln)
        <tr><td>{{ $ln['name'] ?: '-' }}</td><td class="r">{{ rtrim(rtrim(number_format($ln['qty'],4),'0'),'.') }}</td><td class="r">{{ $m($ln['price']) }}</td><td class="r">{{ $m($ln['amount']) }}</td></tr>
        @empty
        <tr><td colspan="4" style="color:#94a3b8">- ไม่มีรายการ -</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="po-sum"><span class="b">ก่อน VAT {{ $m($p['before']) }}</span> · <span class="v">VAT {{ $m($p['vat']) }}</span> · <span class="s">รวม {{ $m($p['after']) }}</span></div>
  </div>
  @endforeach

  <div class="grand">
    <span>รวมทั้งหมด {{ $totals['count'] }} PO</span>
    <span>ก่อน VAT {{ $m($totals['before']) }} · VAT {{ $m($totals['vat']) }} · รวม {{ $m($totals['after']) }}</span>
  </div>
</div>
</body></html>
