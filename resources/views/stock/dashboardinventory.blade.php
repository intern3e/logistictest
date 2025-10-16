<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    :root { --brand:#0b2a6b; --accent:#f59e0b; }
    *{box-sizing:border-box}
    body{font-family:Prompt,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:0;background:#f8fafc;color:#0f172a}
    header{background:linear-gradient(135deg,#0a2356,#0b2a6b 55%,#0f4c75);color:#fff;padding:18px 20px}
    header .row{display:flex;gap:14px;align-items:center;justify-content:space-between;flex-wrap:wrap}
    header a{color:#fff;text-decoration:none;font-weight:600;opacity:.9}
    header a:hover{opacity:1}
    .container{padding:18px 20px}
    .tools{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:12px}
    .search{display:flex;gap:8px}
    .search input{padding:10px 12px;border:1px solid #cbd5e1;border-radius:10px;min-width:260px}
    .search button{padding:10px 14px;border-radius:10px;border:1px solid #d97706;background:var(--accent);font-weight:600}
    .table-wrap{overflow:auto;border:1px solid #e2e8f0;border-radius:12px;background:#fff}
    table{border-collapse:collapse;width:100%;min-width:860px}
    th,td{padding:12px 14px;border-bottom:1px solid #e2e8f0;text-align:left}
    th{background:#f1f5f9;font-weight:600;color:#0b2a6b;position:sticky;top:0}
    tr:hover td{background:#fafafa}
    .qty{font-variant-numeric:tabular-nums}
    .badge{display:inline-block;background:#eef2ff;color:#3730a3;border:1px solid #c7d2fe;padding:2px 8px;border-radius:999px;font-size:12px}
    .paginate{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-top:14px}
    .paginate a,.paginate span{padding:8px 12px;border-radius:10px;border:1px solid #e2e8f0;background:#fff;text-decoration:none;color:#0f172a}
    .paginate .active{border-color:#0b2a6b;background:#0b2a6b;color:#fff}
  </style>
</head>
<body>
  <header>
    <div class="row">
      <div>
        <strong>Inventory</strong>
      </div>
      <nav>
        <a href="{{ route('stock.dashboard') }}">← กลับหน้า Dashboard</a>
      </nav>
    </div>
  </header>

  <div class="container">
    <form class="tools" method="GET" action="{{ route('inventory.dashboard') }}">
      <div class="search">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหา: iditem / model / brand / location">
        <button type="submit">ค้นหา</button>
      </div>
      @if(request('q'))
        <a href="{{ route('inventory.dashboard') }}">ล้างตัวกรอง</a>
      @endif
    </form>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:180px">ID Item</th>
            <th>Model</th>
            <th>Brand</th>
            <th style="width:120px">Quantity</th>
            <th>Location</th>
          </tr>
        </thead>
        <tbody>
        @forelse($items as $it)
          <tr>
            <td><code>{{ $it->iditem }}</code></td>
            <td>{{ $it->model }}</td>
            <td><span class="badge">{{ $it->brand ?? '—' }}</span></td>
            <td class="qty">{{ number_format((int) $it->quantity) }}</td>
            <td>{{ $it->inven_location ?? '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="5">ไม่พบข้อมูล</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    @if(method_exists($items, 'hasPages') && $items->hasPages())
      <div class="paginate">
        {{-- ปุ่มเพจแบบง่าย ๆ --}}
        @if($items->onFirstPage())
          <span>«</span>
        @else
          <a href="{{ $items->previousPageUrl() }}">«</a>
        @endif

        <span class="active">หน้า {{ $items->currentPage() }} / {{ $items->lastPage() }}</span>

        @if($items->hasMorePages())
          <a href="{{ $items->nextPageUrl() }}">»</a>
        @else
          <span>»</span>
        @endif
      </div>
    @endif
  </div>
</body>
</html>
