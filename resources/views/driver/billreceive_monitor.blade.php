<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Monitor บิลค้างรับเข้า</title>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#eef2f7; --card:#ffffff; --line:#e4e8ee; --line-strong:#d6dce4;
      --ink:#141f2c; --ink2:#54657a; --ink3:#8a97a8;
      --primary:#2853d5; --primary-soft:#eaeefb;
      --bill:#1d4ed8; --bill-soft:#dbeafe; --bill-bd:#93c5fd;
      --doc:#9a3412; --doc-soft:#ffedd5; --doc-bd:#fdba74;
      --green:#16a34a;
    }
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Sarabun',Arial,sans-serif;background:var(--bg);color:var(--ink);font-size:15px;line-height:1.5;padding-bottom:40px}
    .topbar{background:var(--card);border-bottom:1px solid var(--line);padding:14px 22px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;position:sticky;top:0;z-index:10}
    .topbar h1{font-size:19px;font-weight:800;flex:1;display:flex;align-items:center;gap:8px}
    .topbar h1::before{content:'';width:4px;height:20px;background:var(--primary);border-radius:2px}
    .btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;border:1px solid var(--line-strong);background:#fff;color:var(--ink2);font-size:13px;font-weight:700;text-decoration:none;cursor:pointer;font-family:inherit}
    .btn:hover{background:var(--bg)}
    .btn-primary{background:var(--primary);border-color:var(--primary);color:#fff}
    .btn-primary:hover{background:#1f41a6}
    main{max-width:1100px;margin:0 auto;padding:22px 16px}
    .summary{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:18px;font-size:15px;color:var(--ink2)}
    .summary .big{font-size:22px;font-weight:800;color:var(--primary)}
    .empty{background:var(--card);border:1px dashed var(--line-strong);border-radius:14px;padding:50px 20px;text-align:center;color:var(--ink3)}
    .empty .ok{font-size:40px;color:var(--green);margin-bottom:8px}
    .day{background:var(--card);border:1px solid var(--line-strong);border-radius:14px;margin-bottom:16px;overflow:hidden}
    .day-head{display:flex;align-items:center;gap:12px;padding:14px 18px;border-bottom:1px solid var(--line);background:#f7f9fc;flex-wrap:wrap}
    .day-date{font-size:17px;font-weight:800}
    .day-total{margin-left:auto;font-size:13px;font-weight:700;color:#fff;background:var(--primary);padding:4px 12px;border-radius:999px}
    .day-body{display:grid;grid-template-columns:1fr 1fr;gap:0}
    @media(max-width:700px){.day-body{grid-template-columns:1fr}}
    .col{padding:14px 18px}
    .col+.col{border-left:1px solid var(--line)}
    @media(max-width:700px){.col+.col{border-left:none;border-top:1px solid var(--line)}}
    .col-title{font-size:13px;font-weight:800;letter-spacing:.02em;margin-bottom:10px;display:flex;align-items:center;gap:8px}
    .col-title .cnt{font-size:12px;font-weight:700;padding:2px 9px;border-radius:999px}
    .tag-bill{color:var(--bill)} .tag-bill .cnt{background:var(--bill-soft);color:var(--bill);border:1px solid var(--bill-bd)}
    .tag-doc{color:var(--doc)} .tag-doc .cnt{background:var(--doc-soft);color:var(--doc);border:1px solid var(--doc-bd)}
    .chips{display:flex;flex-wrap:wrap;gap:6px}
    .chip{font-family:'JetBrains Mono','Sarabun',monospace;font-size:13px;font-weight:600;padding:4px 10px;border-radius:7px;border:1px solid var(--line-strong);background:#fff}
    .chip.bill{background:var(--bill-soft);border-color:var(--bill-bd);color:var(--bill)}
    .chip.doc{background:var(--doc-soft);border-color:var(--doc-bd);color:var(--doc)}
    .none{color:var(--ink3);font-size:13px;font-style:italic}
  </style>
</head>
<body>
  <div class="topbar">
    <h1>Monitor · บิลค้างรับเข้า</h1>
    <span style="font-size:13px;color:var(--ink3)">ผู้ใช้: {{ $loggedInName ?: '-' }}</span>
    <a href="{{ route('billreceive') }}" class="btn btn-primary">&larr; กลับหน้ารับเข้าบิล</a>
  </div>

  <main>
    <div class="summary">
      <span>วันที่มีบิลค้างรับเข้า <b class="big">{{ count($days) }}</b> วัน</span>
      <span>·</span>
      <span>รวมค้างรับเข้า <b class="big">{{ $grandTotal }}</b> บิล</span>
    </div>

    @forelse ($days as $d)
      <div class="day">
        <div class="day-head">
          <span class="day-date">{{ $d['date_thai'] }}</span>
          <span class="day-total">ค้าง {{ $d['total'] }} บิล</span>
        </div>
        <div class="day-body">
          <div class="col">
            <div class="col-title tag-bill">บิลส่งของ <span class="cnt">{{ $d['bill_count'] }}</span></div>
            @if (count($d['bills']))
              <div class="chips">
                @foreach ($d['bills'] as $no)
                  <span class="chip bill">{{ $no }}</span>
                @endforeach
              </div>
            @else
              <div class="none">— ไม่มีบิลส่งของค้าง —</div>
            @endif
          </div>
          <div class="col">
            <div class="col-title tag-doc">บิลชั่วคราว <span class="cnt">{{ $d['doc_count'] }}</span></div>
            @if (count($d['docs']))
              <div class="chips">
                @foreach ($d['docs'] as $no)
                  <span class="chip doc">{{ $no }}</span>
                @endforeach
              </div>
            @else
              <div class="none">— ไม่มีบิลชั่วคราวค้าง —</div>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="empty">
        <div class="ok">&#10003;</div>
        <div>ไม่มีบิลค้างรับเข้า — รับเข้าครบทุกวันแล้ว</div>
      </div>
    @endforelse
  </main>
</body>
</html>
