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
    .tag-bill{color:var(--bill)} .tag-bill .cnt{background:var(--bill-soft);color:var(--bill);border:1px solid var(--bill-bd)}
    .tag-doc{color:var(--doc)} .tag-doc .cnt{background:var(--doc-soft);color:var(--doc);border:1px solid var(--doc-bd)}
    /* ภายในกล่องวัน: แต่ละกลุ่มคนขับเป็นแค่หัวข้อบรรทัด (ไม่มีกล่องซ้อน) */
    .day-body{padding:0}
    .grp-row{padding:10px 16px;border-top:1px solid var(--line)}
    .grp-row:first-child{border-top:none}
    .grp-label{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:7px}
    .grp-transport{font-weight:800;font-size:14px;color:var(--primary)}
    .grp-driver{font-size:13px;font-weight:600;color:var(--ink2)}
    .grp-cnt{margin-left:auto;font-size:12px;font-weight:700;color:var(--ink2);background:var(--primary-soft);padding:2px 10px;border-radius:999px}
    .grp-chips{display:flex;flex-wrap:wrap;gap:6px}
    .chips{display:flex;flex-wrap:wrap;gap:6px}
    .chip{font-family:'JetBrains Mono','Sarabun',monospace;font-size:13px;font-weight:600;padding:4px 10px;border-radius:7px;border:1px solid var(--line-strong);background:#fff}
    .chip.bill{background:var(--bill-soft);border-color:var(--bill-bd);color:var(--bill)}
    .chip.doc{background:var(--doc-soft);border-color:var(--doc-bd);color:var(--doc)}
    .none{color:var(--ink3);font-size:13px;font-style:italic}
    /* สรุปแยก อดีต/อนาคต */
    .summary-cards{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px}
    @media(max-width:700px){.summary-cards{grid-template-columns:1fr}}
    .sum-card{border:1px solid var(--line-strong);border-radius:14px;padding:14px 18px;background:var(--card)}
    .sum-card.past{background:#fef2f2;border-color:#fca5a5}
    .sum-card.future{background:#eff6ff;border-color:#93c5fd}
    .sum-card-title{font-size:14px;font-weight:800;margin-bottom:10px}
    .sum-card.past .sum-card-title{color:#b91c1c}
    .sum-card.future .sum-card-title{color:#1d4ed8}
    .sum-card-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;font-size:14px;color:var(--ink2)}
    .sum-card-row .big{font-size:20px;font-weight:800;color:var(--ink)}
    /* สรุปแยกชนิด */
    .sum-split{display:inline-flex;gap:8px;margin-left:auto;flex-wrap:wrap}
    .pill{font-size:13px;font-weight:700;padding:4px 12px;border-radius:999px;border:1px solid}
    .pill.tag-bill{background:var(--bill-soft);color:var(--bill);border-color:var(--bill-bd)}
    .pill.tag-doc{background:var(--doc-soft);color:var(--doc);border-color:var(--doc-bd)}
    /* แบ่งครึ่งจอ บน=อดีต ล่าง=อนาคต */
    .split{display:flex;flex-direction:column;gap:18px}
    .pane{background:var(--card);border:1px solid var(--line-strong);border-radius:14px;overflow:hidden}
    .pane.past{background:#fef2f2;border-color:#fca5a5}
    .pane.past .pane-body .day{border-color:#fecaca}
    .pane-head{padding:11px 16px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:10px;flex-wrap:wrap}
    .pane-head.past{background:#fef2f2}
    .pane-head.future{background:#eff6ff}
    .pane-head .sh-title{font-size:15px;font-weight:800}
    .pane-head.past .sh-title{color:#b91c1c}
    .pane-head.future .sh-title{color:#1d4ed8}
    .pane-head .sh-sub{font-size:12px;color:var(--ink3)}
    .pane-head .sh-cnt{margin-left:auto;font-size:12px;font-weight:700;padding:3px 11px;border-radius:999px;background:#fff;border:1px solid var(--line-strong)}
    .pane-body{padding:14px}
    .pane-body .day:last-child{margin-bottom:0}
  </style>
</head>
<body>
  <div class="topbar">
    <h1>Monitor · บิลค้างรับเข้า</h1>
    <span style="font-size:13px;color:var(--ink3)">ผู้ใช้: {{ $loggedInName ?: '-' }}</span>
    <a href="{{ route('billreceive') }}" class="btn btn-primary">&larr; กลับหน้ารับเข้าบิล</a>
  </div>

  <main>
    <div class="summary-cards">
      <div class="sum-card past">
        <div class="sum-card-title">① อดีตที่ยังไม่รับเข้า</div>
        <div class="sum-card-row">
          <span><b class="big">{{ $pastDayCount }}</b> วัน</span>
          <span>ค้าง <b class="big">{{ $pastTotal }}</b> บิล</span>
          <span class="pill tag-bill">ส่งของ <b>{{ $pastBills }}</b></span>
          <span class="pill tag-doc">ชั่วคราว <b>{{ $pastDocs }}</b></span>
        </div>
      </div>
      <div class="sum-card future">
        <div class="sum-card-title">② งานอนาคต (จ่ายแล้ว ยังไม่รับ)</div>
        <div class="sum-card-row">
          <span><b class="big">{{ $futureDayCount }}</b> วัน</span>
          <span>ค้าง <b class="big">{{ $futureTotal }}</b> บิล</span>
          <span class="pill tag-bill">ส่งของ <b>{{ $futureBills }}</b></span>
          <span class="pill tag-doc">ชั่วคราว <b>{{ $futureDocs }}</b></span>
        </div>
      </div>
    </div>

    <div class="split">
      {{-- ครึ่งบน: อดีตที่ยังไม่รับเข้า --}}
      <div class="pane past">
        <div class="pane-head past">
          <span class="sh-title">① อดีตที่ยังไม่รับเข้า</span>
          <span class="sh-sub">เลยกำหนดแล้วแต่ยังไม่ได้รับเข้า</span>
          <span class="sh-cnt">{{ count($pastDays) }} วัน</span>
        </div>
        <div class="pane-body">
          @forelse ($pastDays as $d)
            @include('driver._monitor_day', ['d' => $d])
          @empty
            <div class="none" style="padding:20px;text-align:center;">— ไม่มีบิลอดีตค้างรับเข้า —</div>
          @endforelse
        </div>
      </div>

      {{-- ครึ่งล่าง: งานอนาคตที่จ่ายไปแล้ว แต่ยังไม่ได้รับ --}}
      <div class="pane">
        <div class="pane-head future">
          <span class="sh-title">② งานอนาคตที่จ่ายไปแล้ว แต่ยังไม่ได้รับ</span>
          <span class="sh-sub">ตั้งแต่วันนี้เป็นต้นไป</span>
          <span class="sh-cnt">{{ count($futureDays) }} วัน</span>
        </div>
        <div class="pane-body">
          @forelse ($futureDays as $d)
            @include('driver._monitor_day', ['d' => $d])
          @empty
            <div class="none" style="padding:20px;text-align:center;">— ไม่มีงานอนาคตค้างรับเข้า —</div>
          @endforelse
        </div>
      </div>
    </div>
  </main>
</body>
</html>
