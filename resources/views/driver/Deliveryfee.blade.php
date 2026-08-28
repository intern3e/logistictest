{{-- resources/views/driver/deliveryfee.blade.php — ตารางค่าวิ่ง / OT / ค่ายก แบบปฏิทิน --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ตารางค่าวิ่ง / OT / ค่ายก</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
html,body{height:100%;margin:0;overflow:hidden}
body{background:#e8eaed;font-family:Arial,'IBM Plex Sans Thai','Inter',-apple-system,sans-serif;color:#18181b;display:flex;flex-direction:column}

.tesla-topnav{background:#fff;border-bottom:1px solid #eaeaea;flex:0 0 auto}
.tesla-topnav-container{max-width:100%;margin:0 auto;padding:0 20px;height:64px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
.tesla-brand{display:flex;align-items:center;gap:10px}
.tesla-logo{font-size:20px}
.tesla-title{font-weight:600;font-size:16px;color:#171a20}
.tesla-right{display:flex;align-items:center;gap:16px}
.tesla-user-badge{font-size:14px;color:#4b5563;white-space:nowrap}
.tesla-btn{height:36px;padding:0 18px;text-decoration:none;font-weight:500;display:inline-flex;align-items:center;justify-content:center;gap:8px;font-size:14px;border-radius:6px;background:#3e6ae1;color:#fff;border:1px solid #3e6ae1;white-space:nowrap;cursor:pointer}
.tesla-btn:hover{background:#3457b2;border-color:#3457b2;color:#fff}
.tesla-btn.tesla-btn-print{background:#16a34a;border-color:#16a34a}
.tesla-btn.tesla-btn-print:hover{background:#128a3e;border-color:#128a3e}

.filters-bar{display:flex;align-items:flex-end;gap:14px;padding:10px 16px;background:#fff;border-bottom:1px solid #eaeaea;flex-wrap:wrap;flex:0 0 auto}
.mode-segmented{display:inline-flex;background:#f1f3f5;padding:3px;border-radius:8px;gap:2px;border:1px solid #eaeaea}
.mode-btn{background:transparent;border:none;height:34px;padding:0 16px;font-size:13px;font-weight:600;color:#4b5563;border-radius:6px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center}
.mode-btn.active{background:#111827;color:#fff}

.filter-group{display:flex;flex-direction:column;gap:6px}
.filter-group label{font-size:12px;font-weight:600;color:#4b5563}
.filter-group input[type="date"],.filter-group select{height:36px;padding:0 12px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;color:#374151;outline:none}
.filter-group input:focus,.filter-group select:focus{border-color:#3e6ae1}
.filter-apply{height:36px;padding:0 18px;background:#111827;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer}
.filter-apply:hover{background:#1f2937}

.main{flex:1 1 auto;min-height:0;margin:0;padding:8px 0;display:flex}

.summary-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px;max-width:1200px}
@media (max-width:800px){.summary-cards{grid-template-columns:repeat(2,1fr)}}
.stat-card{background:#fff;border:1px solid #eaeaea;border-radius:10px;padding:14px 18px}
.stat-card .lbl{font-size:12px;color:#71717a;margin-bottom:6px}
.stat-card .val{font-size:19px;font-weight:700;color:#111827}
.stat-card.total .val{color:#3e6ae1}

.grid-card{background:#fff;border:1px solid rgba(0,0,0,0.25);border-radius:0;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.08);flex:1 1 auto;min-width:0;display:flex}
.grid-scroll{overflow:auto;width:100%;height:100%;background:#f8f9fa}
.print-page-break{margin-top:20px}
html.print-mode .print-page-break{margin-top:0}

table.calgrid{border-collapse:collapse;table-layout:fixed;width:100%;height:1px;font-size:11px;font-family:Arial,'IBM Plex Sans Thai',sans-serif;background:#fff}
.calgrid th,.calgrid td{border:1px solid rgba(0,0,0,0.25);padding:0px 0px;text-align:center;white-space:normal;word-break:break-word;overflow:hidden}
.calgrid thead{position:sticky;top:0;z-index:5}

.calgrid thead th{background:#2b3a67;color:#fff;font-weight:700}
.calgrid thead tr.wk-row th{background:#3e6ae1;font-size:9px;font-weight:600}
.calgrid th.wkday{white-space:nowrap;padding:2px 1px;letter-spacing:0}
.calgrid thead th.weekend{background:#c0392b}
.calgrid thead tr.wk-row th.weekend{background:#e74c3c}

.calgrid tr.title-row td{background:#2b3a67;color:#fff;font-weight:700;font-size:12px;text-align:left;padding:5px 10px}
.calgrid tr.blank-row td{background:#fff;border-color:rgba(0,0,0,0.25);height:14px;padding:2px}
.calgrid tr.info-row td{background:#fff;color:#111827;font-weight:700;font-size:10px;text-align:left;padding:3px 8px;border-color:rgba(0,0,0,0.25)}
.calgrid tr.info-row .week-badge{display:inline-block;background:#fff6a3;border:1px solid #e6d84f;color:#3f3f00;font-weight:700;padding:1px 8px;border-radius:2px}
.calgrid tr.info-row .info-note{font-weight:400;font-style:italic;font-size:8.5px;color:#6b7280;margin-left:8px}
.calgrid tr.date-row td{background:#fff;color:#111827;font-weight:600;font-size:9px;border-color:rgba(0,0,0,0.25)}
.calgrid tr.hdr-week th{background:#3e6ae1;font-size:10px;font-weight:700}
.calgrid tr.hdr-week th.weekend{background:#c0392b}
.calgrid th.cyan-head,.calgrid td.cyan-cell{background:#3ce7e0!important;color:#003b3b!important}

.calgrid td.name-cell{background:#fdf6e3;text-align:center;font-weight:700;color:#111827;font-size:14px;line-height:1.3}
.calgrid td.name-cell .plate{display:block;font-weight:500;font-size:11px;color:#6b7280;font-family:monospace;white-space:normal}
.calgrid td.item-cell{background:#fffdf0;text-align:center;color:#4b5563;font-size:11.5px}
.calgrid td.num{background:#fffefa;text-align:center;font-variant-numeric:tabular-nums;color:#111827}
.calgrid td.num.empty{color:#6b7280;font-weight:600}
.calgrid td.tot-col{background:#d9e8fb;font-weight:700;color:#1e3a8a}
.calgrid td.person-tot{background:#fce4cf;font-weight:700;color:#7c2d12;font-size:13.5px}
.calgrid td.person-tot.cyan-cell{font-size:13.5px}

.calgrid tr.group-alt td.name-cell{background:#fbf1d6}
.calgrid tr.group-alt td.item-cell{background:#fffaea}
.calgrid tr.group-alt td.num{background:#fffcf5}

.calgrid tr.totals-row td.name-cell,.calgrid tr.totals-row td.item-cell{background:#dbeafe;color:#1e3a8a;font-weight:700}
.calgrid tr.totals-row td.num{background:#eef4ff;font-weight:600}
.calgrid tr.totals-row td.tot-col{background:#bfdbfe}

.calgrid tr.grand-row td{background:#111827;color:#fff;font-weight:700;font-size:11px;padding:4px 4px;border-color:rgba(0,0,0,0.25)}
.calgrid tr.grand-row td.grand-val{color:#93c5fd;font-size:12px}

/* ── Inline Edit ── */
.calgrid td.num.editable{cursor:pointer;position:relative;transition:background .15s}
.calgrid td.num.editable:hover{background:#e8f0fe!important;box-shadow:inset 0 0 0 1.5px #3e6ae1}
.calgrid td.num.editable.editing{padding:0!important}
.calgrid td.num .inline-input{width:100%;height:100%;border:2px solid #3e6ae1;border-radius:4px;
  text-align:center;font-size:11px;font-family:inherit;padding:2px 4px;
  font-variant-numeric:tabular-nums;background:#fffdf0;outline:none;box-sizing:border-box}
.calgrid td.num .inline-input:focus{border-color:#1e40af;box-shadow:0 0 0 3px rgba(62,106,225,.2)}
.calgrid td.num.saving{opacity:.5;pointer-events:none}
.calgrid td.num.save-success{animation:flashGreen .6s}
.calgrid td.num.save-error{animation:flashRed .6s}
@keyframes flashGreen{0%{background:#86efac}100%{background:inherit}}
@keyframes flashRed{0%{background:#fca5a5}100%{background:inherit}}
.calgrid td.num.tot-col,.calgrid td.num.person-tot,.calgrid td.num.cyan-cell{cursor:default!important}
.calgrid td.num.tot-col:hover,.calgrid td.num.person-tot:hover,.calgrid td.num.cyan-cell:hover{box-shadow:none!important;background:inherit!important}

.week-picker-wrap{position:relative;display:flex;flex-direction:column;gap:6px}
.week-picker-input{height:36px;padding:0 12px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;color:#374151;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:10px;min-width:190px;user-select:none}
.week-picker-input:hover{border-color:#3e6ae1}
.week-picker-input.open{border-color:#3e6ae1;box-shadow:0 0 0 3px rgba(62,106,225,.12)}
.week-picker-input .wp-icon{font-size:14px;opacity:.6}
.week-picker-dropdown{position:absolute;top:calc(100% + 6px);left:0;z-index:60;background:#fff;border:1px solid #d1d5db;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,.14);padding:12px;width:270px;display:none}
.week-picker-dropdown.open{display:block}
.wp-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.wp-month-label{font-weight:700;font-size:14px;color:#111827}
.wp-nav-btn{width:28px;height:28px;border:none;background:#f1f3f5;border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#374151;font-size:14px}
.wp-nav-btn:hover{background:#e5e7eb}
.wp-nav-group{display:flex;gap:4px}
.wp-weekdays{display:grid;grid-template-columns:repeat(7,1fr);gap:2px;margin-bottom:2px}
.wp-weekdays span{text-align:center;font-size:11px;font-weight:600;color:#9ca3af;padding:4px 0}
.wp-weekdays span.wp-weekend{color:#dc2626}
.wp-days{display:grid;grid-template-columns:repeat(7,1fr);gap:2px}
.wp-day-cell{position:relative;height:32px}
.wp-day-cell.in-week{background:#e8edfc}
.wp-day-cell.in-week.week-start{border-top-left-radius:8px;border-bottom-left-radius:8px}
.wp-day-cell.in-week.week-end{border-top-right-radius:8px;border-bottom-right-radius:8px}
.wp-day-btn{width:100%;height:100%;border:none;background:transparent;border-radius:6px;font-size:12px;color:#374151;cursor:pointer;position:relative;z-index:2}
.wp-day-btn.muted{color:#c4c8cd}
.wp-day-btn:hover{background:rgba(62,106,225,.18)}
.wp-day-btn.selected{background:#3e6ae1!important;color:#fff;font-weight:700}
.wp-day-btn.today:not(.selected){box-shadow:inset 0 0 0 1.5px #3e6ae1;color:#3e6ae1;font-weight:700}
.wp-footer{display:flex;justify-content:space-between;margin-top:10px;padding-top:8px;border-top:1px solid #eee}
.wp-footer button{background:none;border:none;color:#3e6ae1;font-size:12px;cursor:pointer;font-weight:600;padding:2px 4px}
.wp-footer button:hover{text-decoration:underline}
.wp-range-hint{font-size:10.5px;color:#71717a;margin-top:6px;text-align:center}

/* ── ย่อขนาดตารางให้พอดี 1 หน้ากระดาษตอนปริ้น ──
   ใช้ font-size/padding จริง (ไม่ใช่ transform) เพราะ transform ไม่มีผลต่อการตัดหน้าตอนปริ้น
   ค่า --print-scale จะถูกคำนวณและปรับโดย JS ก่อนสั่งพิมพ์ */
:root { --print-scale: 1; }

html.print-mode .grid-scroll { overflow: visible !important; }
html.print-mode table.calgrid { width: 100%; height: auto; font-size: calc(9.5px * var(--print-scale)); }
html.print-mode .calgrid th,
html.print-mode .calgrid td { padding: calc(5px * var(--print-scale)) calc(3px * var(--print-scale)); }
html.print-mode .calgrid tr.title-row td { font-size: calc(12px * var(--print-scale)); padding: calc(6px * var(--print-scale)) calc(8px * var(--print-scale)); }
html.print-mode .calgrid tr.date-row td { font-size: calc(9px * var(--print-scale)); }
html.print-mode .calgrid tr.hdr-week th,
html.print-mode .calgrid thead tr.wk-row th { font-size: calc(9px * var(--print-scale)); }
html.print-mode .calgrid th.wkday { padding: calc(2px * var(--print-scale)) calc(1px * var(--print-scale)); }
html.print-mode .calgrid td.name-cell { font-size: calc(13px * var(--print-scale)); line-height: 1.2; }
html.print-mode .calgrid td.name-cell .plate { font-size: calc(10px * var(--print-scale)); }
html.print-mode .calgrid td.item-cell { font-size: calc(10.5px * var(--print-scale)); }
html.print-mode .calgrid td.person-tot { font-size: calc(12px * var(--print-scale)); }
html.print-mode .calgrid tr.grand-row td { font-size: calc(10px * var(--print-scale)); padding: calc(5px * var(--print-scale)) calc(4px * var(--print-scale)); }
html.print-mode .calgrid tr.grand-row td.grand-val { font-size: calc(11px * var(--print-scale)); }

@media print {
  @page { size: A4 landscape; margin: 8mm; }
  html, body { height: auto; overflow: visible; background: #fff; }
  .tesla-topnav, .filters-bar, .no-print { display: none !important; }
  .main { padding: 0; display: block; }
  .grid-card { border: none; box-shadow: none; overflow: visible; display: block; }
  .grid-scroll { overflow: visible !important; background: #fff; }
  table.calgrid { height: auto; }
  .calgrid thead { position: static; }
  .calgrid tr, .calgrid td, .calgrid th { break-inside: avoid; page-break-inside: avoid; }
  .calgrid td.num.editable{cursor:default}
  .calgrid td.num.editable:hover{background:transparent!important;box-shadow:none}
  .print-page-break{break-before:page;page-break-before:always}

  /* บังคับให้เบราว์เซอร์พิมพ์สีพื้นหลัง/สีหัวตารางออกมาด้วย (ปกติจะถูกตัดทิ้งตอนปริ้น) */
  *, *::before, *::after {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color-adjust: exact !important;
  }
}
</style>

</head>
<body>

@php
  $currentUser = request()->filled('create_by') ? request('create_by') : 'Guest';
  $userQuery = $currentUser !== 'Guest' ? '?create_by='.urlencode($currentUser) : '';
  $qs = fn($extra) => http_build_query(array_merge($currentUser !== 'Guest' ? ['create_by'=>$currentUser] : [], $extra));

  $thWeekday = ['อา','จ','อ','พ','พฤ','ศ','ส'];
  $thMonths = ['', 'มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];

  $prevMonth = $selMonth - 1; $prevYear = $selYear;
  if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
  $nextMonth = $selMonth + 1; $nextYear = $selYear;
  if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

  $prevWeekStart = \Carbon\Carbon::parse($weekStart)->subDays(7)->format('Y-m-d');
  $nextWeekStart = \Carbon\Carbon::parse($weekStart)->addDays(7)->format('Y-m-d');
@endphp

<nav class="tesla-topnav">
  <div class="tesla-topnav-container">
    <div class="tesla-brand">
      <div class="tesla-logo">🧾</div>
      <span class="tesla-title">ตารางค่าวิ่ง / OT / ค่ายก</span>
    </div>
    <div class="tesla-right">
      <div class="tesla-user-badge">👤 ผู้ใช้: {{ $currentUser }}</div>
      <button type="button" id="printReportBtn" class="tesla-btn tesla-btn-print">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        ปริ้น / บันทึก PDF
      </button>
      <a href="{{ url('/oil').$userQuery }}" class="tesla-btn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        กลับหน้าน้ำมัน
      </a>
    </div>
  </div>
</nav>

<div class="filters-bar">
  <div class="mode-segmented">
    <a href="{{ url('/oil/Deliveryfee') }}?{{ $qs(['mode'=>'week','week_start'=>$weekStart]) }}" class="mode-btn {{ $mode==='week'?'active':'' }}">รายสัปดาห์</a>
    <a href="{{ url('/oil/Deliveryfee') }}?{{ $qs(['mode'=>'month','month'=>$selMonth,'year'=>$selYear]) }}" class="mode-btn {{ $mode==='month'?'active':'' }}">รายเดือน</a>
  </div>

  @if($mode === 'month')
    <form class="filters-bar" style="padding:0;gap:14px" method="GET" action="{{ url('/oil/Deliveryfee') }}">
      <input type="hidden" name="mode" value="month">
      @if($currentUser !== 'Guest')<input type="hidden" name="create_by" value="{{ $currentUser }}">@endif
      <div class="filter-group">
        <label>เดือน</label>
        <select name="month">
          @for($m=1;$m<=12;$m++)
          <option value="{{ $m }}" {{ $selMonth==$m?'selected':'' }}>{{ $thMonths[$m] }}</option>
          @endfor
        </select>
      </div>
      <div class="filter-group">
        <label>ปี</label>
        <select name="year">
          @for($y=now()->year+1;$y>=2023;$y--)
          <option value="{{ $y }}" {{ $selYear==$y?'selected':'' }}>{{ $y + 543 }}</option>
          @endfor
        </select>
      </div>
      <button type="submit" class="filter-apply" style="align-self:flex-end">ไป</button>
    </form>
  @else
    <form class="filters-bar" style="padding:0;gap:14px" method="GET" action="{{ url('/oil/Deliveryfee') }}" id="weekForm">
      <input type="hidden" name="mode" value="week">
      @if($currentUser !== 'Guest')<input type="hidden" name="create_by" value="{{ $currentUser }}">@endif
      <div class="filter-group">
        <label>เลือกวันในสัปดาห์</label>
        <div class="week-picker-wrap" id="weekPickerWrap">
          <input type="hidden" name="week_start" id="weekStartHidden" value="{{ $weekStart }}">
          <div class="week-picker-input" id="weekPickerInput" tabindex="0">
            <span id="weekPickerDisplay"></span>
            <span class="wp-icon">📅</span>
          </div>
          <div class="week-picker-dropdown" id="weekPickerDropdown">
            <div class="wp-header">
              <span class="wp-month-label" id="wpMonthLabel"></span>
              <div class="wp-nav-group">
                <button type="button" class="wp-nav-btn" id="wpPrevBtn">‹</button>
                <button type="button" class="wp-nav-btn" id="wpNextBtn">›</button>
              </div>
            </div>
            <div class="wp-weekdays">
              <span>จ</span><span>อ</span><span>พ</span><span>พฤ</span><span>ศ</span><span class="wp-weekend">อา</span>
            </div>
            <div class="wp-days" id="wpDaysGrid"></div>
            <div class="wp-footer">
              <button type="button" id="wpClearBtn">ล้าง</button>
              <button type="button" id="wpTodayBtn">วันนี้</button>
            </div>
            <div class="wp-range-hint" id="wpRangeHint"></div>
          </div>
        </div>
      </div>
      <button type="submit" class="filter-apply" style="align-self:flex-end">ไป</button>
    </form>
  @endif
</div>

<main class="main">
  <div class="grid-card">
    <div class="grid-scroll">
      @php
        if ($mode === 'week') {
          $wsForDays = \Carbon\Carbon::parse($weekStart)->startOfWeek(\Carbon\Carbon::MONDAY);
          $days = [];
          for ($i = 0; $i < 7; $i++) {
            $days[] = $wsForDays->copy()->addDays($i)->format('Y-m-d');
          }
        }
        $dayCount = count($days);
        $nameW = 10; $itemW = 7; $totW = 10; $personW = 11;
        $dayW = $dayCount > 0 ? (100 - $nameW - $itemW - $totW - $personW) / $dayCount : 0;

        // ── ลำดับคนขับที่ต้องการแสดง (ตรงกับหน้าน้ำมัน) ──
        $allowedDrivers = ['กอลฟ์','เก่ง','เอ้','เอ','บังเดช','แฟงค์','yuth','แซม','บอยBTS','กบ','joey','แมน'];

        // ── รายการคนขับที่ต้องการซ่อนแบบเจาะจง (เทียบทั้งชื่อ + ทะเบียน) ──
        $excludedDrivers = [
            ['label' => 'หรั่ง', 'plate' => '3ฉมย478'],
        ];

        // ── ฟังก์ชันช่วยล้างอักขระที่มองไม่เห็น (zero-width / BOM) ออกจากชื่อ ก่อนเทียบชื่อ
        //    ป้องกันกรณีชื่อในฐานข้อมูลมีอักขระแฝงติดมาแล้ว trim() ธรรมดาจับไม่ได้
        $cleanName = function ($s) {
          $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', (string) $s);
          return trim($s);
        };

        // ── ตัดแถวที่:
        //    1. ชื่อเป็น "บอย" (ไม่ใช่ "บอยBTS") → ซ่อนทั้งหมด
        //    2. ตรงกับ $excludedDrivers (หรั่ง 3ฉมย478)
        //    3. หรือมีทะเบียนเป็นมอเตอร์ไซค์ (ยกเว้นคนขับที่อยู่ใน $allowedDrivers)
        $driverGrid = array_values(array_filter($driverGrid, function ($dg) use ($excludedDrivers, $allowedDrivers, $cleanName) {
          $label = $cleanName($dg['label'] ?? '');
          $plate = trim((string) ($dg['plate'] ?? ''));

          // ตัด "บอย" ตัวเปล่าๆ ทิ้งเสมอ (เทียบแบบล้างอักขระแฝงแล้ว) แต่เก็บ "บอยBTS" ไว้
          if ($label === 'บอย') {
            return false;
          }

          foreach ($excludedDrivers as $ex) {
            if ($label === $ex['label'] && $plate === $ex['plate']) {
              return false;
            }
          }

          if (in_array($label, $allowedDrivers, true)) {
            return true;
          }

          $plateClean = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $plate);
          $isMotorcycle = mb_stripos($plateClean, 'มอเตอร์ไซค์') !== false
                       || mb_stripos($plateClean, 'มอเตอร์ไซด์') !== false;
          return !$isMotorcycle;
        }));

        // ── ล้างชื่อ label ที่เหลือให้เป็นค่าที่ trim/ล้างอักขระแฝงแล้ว เพื่อให้ส่วนต่อไป (เรียงลำดับ, สร้างแถวว่าง) ใช้ค่าที่ตรงกันเสมอ ──
        foreach ($driverGrid as $gi => $dg) {
          $driverGrid[$gi]['label'] = $cleanName($dg['label'] ?? '');
        }

        // ── สร้างแถวว่างสำหรับคนขับที่ไม่มีข้อมูล ──
        $existingLabels = array_map(function($dg) {
            return trim((string) ($dg['label'] ?? ''));
        }, $driverGrid);

        foreach ($allowedDrivers as $driverName) {
            if (!in_array($driverName, $existingLabels)) {
                $emptyDays = [];
                foreach ($days as $day) {
                    $emptyDays[$day] = ['delivery' => 0, 'ot' => 0, 'handling' => 0, 'has' => false];
                }
                $driverGrid[] = [
                    'label'       => $driverName,
                    'db_name'     => $driverName,  // ⭐ สำหรับ Inline Edit
                    'plate'       => '',
                    'days'        => $emptyDays,
                    'totDelivery' => 0,
                    'totOt'       => 0,
                    'totHandling' => 0,
                    'totAll'      => 0,
                ];
            }
        }

        usort($driverGrid, function($a, $b) use ($allowedDrivers) {
            $labelA = trim((string) ($a['label'] ?? ''));
            $labelB = trim((string) ($b['label'] ?? ''));

            $indexA = array_search($labelA, $allowedDrivers);
            $indexB = array_search($labelB, $allowedDrivers);

            if ($indexA === false) $indexA = PHP_INT_MAX;
            if ($indexB === false) $indexB = PHP_INT_MAX;

            if ($indexA !== $indexB) return $indexA <=> $indexB;
            return strcmp($labelA, $labelB);
        });

        // ── ฟังก์ชันช่วยคำนวณยอดรวมของกลุ่มคนขับกลุ่มใดกลุ่มหนึ่ง (ใช้แยกยอดรวมของแต่ละหน้า) ──
        $computeGroupTotals = function ($drivers) use ($days) {
          $t = [];
          foreach ($days as $day) { $t[$day] = ['delivery' => 0, 'ot' => 0, 'handling' => 0]; }
          $gd = 0; $go = 0; $gh = 0;
          foreach ($drivers as $dg) {
            foreach ($days as $day) {
              $t[$day]['delivery'] += $dg['days'][$day]['delivery'] ?? 0;
              $t[$day]['ot']       += $dg['days'][$day]['ot'] ?? 0;
              $t[$day]['handling'] += $dg['days'][$day]['handling'] ?? 0;
            }
            $gd += $dg['totDelivery'] ?? 0;
            $go += $dg['totOt'] ?? 0;
            $gh += $dg['totHandling'] ?? 0;
          }
          return [
            'dayTotals'     => $t,
            'grandDelivery' => $gd,
            'grandOt'       => $go,
            'grandHandling' => $gh,
            'grandTotal'    => $gd + $go + $gh,
          ];
        };

        // ── แบ่งคนขับออกเป็น 2 กลุ่มสำหรับปริ้น: กลุ่มหลัก กับกลุ่มมอเตอร์ไซค์ (กบ, joey, แมน) ──
        // ใช้ตารางแยก 2 ตัวแทนการพยายาม page-break-before บน <tr> เพราะเบราว์เซอร์ (Chrome)
        // ไม่รองรับการบังคับขึ้นหน้าใหม่กลางตารางแบบนั้นอย่างน่าเชื่อถือ แต่รองรับกับ <table>/<div> แน่นอน
        $splitIndex = null;
        foreach ($driverGrid as $idx => $dg) {
          if (trim((string) ($dg['label'] ?? '')) === 'กบ') { $splitIndex = $idx; break; }
        }
        if ($splitIndex === null) {
          $mainDrivers = $driverGrid;
          $motoDrivers = [];
        } else {
          $mainDrivers = array_slice($driverGrid, 0, $splitIndex, true);
          $motoDrivers = array_slice($driverGrid, $splitIndex, null, true);
        }

        // ── ยอดรวม "รวมทุกคน" ของแต่ละหน้าแยกกันเอง ไม่รวมข้ามหน้า ──
        $mainTotals = $computeGroupTotals($mainDrivers);
        $motoTotals = $computeGroupTotals($motoDrivers);
      @endphp
      @php
        // ── partial ย่อยสำหรับ render แถวคนขับ 3 บรรทัด (ค่าวิ่ง/OT/ค่ายก) ให้ใช้ซ้ำได้ทั้ง 2 ตาราง ──
        $renderDriverRows = function ($drivers) use ($days, $mode) {
          foreach ($drivers as $gi => $dg) {
            $altClass = $gi % 2 === 1 ? 'group-alt' : '';
            ?>
          <tr class="<?= $altClass ?>" data-driver-row="1">
            <td class="name-cell" rowspan="3"><?= e($dg['label']) ?><span class="plate"><?= e($dg['plate']) ?></span></td>
            <td class="item-cell">ค่าวิ่ง</td>
            <?php foreach ($days as $day): $cell = $dg['days'][$day] ?? []; $v = $cell['delivery'] ?? 0; $has = $cell['has'] ?? false; ?>
              <td class="num editable <?= $v==0?'empty':'' ?>"
                  data-driver="<?= e($dg['db_name'] ?? $dg['label']) ?>"
                  data-date="<?= e($day) ?>"
                  data-field="delivery"
                  data-value="<?= $v ?>"><?= !$has ? '' : ($v != 0 ? number_format($v) : '-') ?></td>
            <?php endforeach; ?>
            <td class="num tot-col"><?= $dg['totDelivery'] != 0 ? number_format($dg['totDelivery']) : '-' ?></td>
            <td class="person-tot <?= $mode==='week' ? 'cyan-cell' : '' ?>" rowspan="3">฿<?= number_format($dg['totAll']) ?></td>
          </tr>
          <tr class="<?= $altClass ?>" data-driver-row="1">
            <td class="item-cell">ค่า OT</td>
            <?php foreach ($days as $day): $cell = $dg['days'][$day] ?? []; $v = $cell['ot'] ?? 0; $has = $cell['has'] ?? false; ?>
              <td class="num editable <?= $v==0?'empty':'' ?>"
                  data-driver="<?= e($dg['db_name'] ?? $dg['label']) ?>"
                  data-date="<?= e($day) ?>"
                  data-field="ot"
                  data-value="<?= $v ?>"><?= !$has ? '' : ($v != 0 ? number_format($v) : '-') ?></td>
            <?php endforeach; ?>
            <td class="num tot-col"><?= $dg['totOt'] != 0 ? number_format($dg['totOt']) : '-' ?></td>
          </tr>
          <tr class="<?= $altClass ?>" data-driver-row="1">
            <td class="item-cell">ค่ายก</td>
            <?php foreach ($days as $day): $cell = $dg['days'][$day] ?? []; $v = $cell['handling'] ?? 0; $has = $cell['has'] ?? false; ?>
              <td class="num editable <?= $v==0?'empty':'' ?>"
                  data-driver="<?= e($dg['db_name'] ?? $dg['label']) ?>"
                  data-date="<?= e($day) ?>"
                  data-field="handling"
                  data-value="<?= $v ?>"><?= !$has ? '' : ($v != 0 ? number_format($v) : '-') ?></td>
            <?php endforeach; ?>
            <td class="num tot-col"><?= $dg['totHandling'] != 0 ? number_format($dg['totHandling']) : '-' ?></td>
          </tr>
            <?php
          }
        };

        // ── partial ย่อยสำหรับ render thead (ใช้ซ้ำทั้ง 2 ตาราง เพื่อให้หัวตารางขึ้นซ้ำในหน้า 2 ด้วย) ──
        $renderThead = function ($titleSuffix = '') use ($mode, $weekStart, $thMonths, $thWeekday, $days, $dayCount, $selMonth, $selYear) {
          if ($mode === 'week') {
            $wTitleStart = \Carbon\Carbon::parse($weekStart);
            $wTitleEnd   = $wTitleStart->copy()->addDays(6);
            if ($wTitleStart->year !== $wTitleEnd->year) {
              $weekRangeLabel = $wTitleStart->format('d') . ' ' . $thMonths[$wTitleStart->month] . ' ' . ($wTitleStart->year + 543)
                . ' – ' . $wTitleEnd->format('d') . ' ' . $thMonths[$wTitleEnd->month] . ' ' . ($wTitleEnd->year + 543);
            } elseif ($wTitleStart->month !== $wTitleEnd->month) {
              $weekRangeLabel = $wTitleStart->format('d') . ' ' . $thMonths[$wTitleStart->month]
                . ' – ' . $wTitleEnd->format('d') . ' ' . $thMonths[$wTitleEnd->month] . ' ' . ($wTitleEnd->year + 543);
            } else {
              $weekRangeLabel = $wTitleStart->format('d') . ' – ' . $wTitleEnd->format('d') . ' ' . $thMonths[$wTitleEnd->month] . ' ' . ($wTitleEnd->year + 543);
            }
            ?>
            <tr class="title-row">
              <td colspan="<?= $dayCount + 4 ?>">รายงานประจำสัปดาห์ — <?= e($weekRangeLabel) ?><?= $titleSuffix ? ' ' . e($titleSuffix) : '' ?></td>
            </tr>
            <tr class="date-row">
              <td colspan="2"></td>
              <?php foreach ($days as $day): ?>
                <td><?= \Carbon\Carbon::parse($day)->format('d/m') ?></td>
              <?php endforeach; ?>
              <td colspan="2"></td>
            </tr>
            <tr class="hdr-week">
              <th class="col-name">คนขับ</th>
              <th class="col-item">รายการ</th>
              <?php foreach ($days as $day): $dObj = \Carbon\Carbon::parse($day); $isWeekend = in_array((int)$dObj->format('w'), [0,6]); ?>
                <th class="wkday <?= $isWeekend ? 'weekend' : '' ?>"><?= $thWeekday[(int)$dObj->format('w')] ?>.</th>
              <?php endforeach; ?>
              <th>รวมสัปดาห์ (บาท)</th>
              <th class="cyan-head">รวมทั้งสิ้น(บาท)</th>
            </tr>
            <?php
          } else {
            ?>
            <tr>
              <th class="col-name" rowspan="2">คนขับ / ทะเบียน<?= $titleSuffix ? ' ' . e($titleSuffix) : '' ?></th>
              <th class="col-item" rowspan="2">รายการ</th>
              <?php foreach ($days as $day): $dObj = \Carbon\Carbon::parse($day); $isWeekend = in_array((int)$dObj->format('w'), [0,6]); ?>
                <th class="<?= $isWeekend ? 'weekend' : '' ?>"><?= $dObj->format('j') ?></th>
              <?php endforeach; ?>
              <th rowspan="2">รวมเดือน</th>
              <th rowspan="2">รวมของคนนี้</th>
            </tr>
            <tr class="wk-row">
              <?php foreach ($days as $day): $dObj = \Carbon\Carbon::parse($day); $isWeekend = in_array((int)$dObj->format('w'), [0,6]); ?>
                <th class="wkday <?= $isWeekend ? 'weekend' : '' ?>"><?= $thWeekday[(int)$dObj->format('w')] ?>.</th>
              <?php endforeach; ?>
            </tr>
            <?php
          }
        };
      @endphp
      <table class="calgrid">
        <colgroup>
          <col style="width:{{ $nameW }}%">
          <col style="width:{{ $itemW }}%">
          @for($i=0;$i<$dayCount;$i++)
            <col style="width:{{ $dayW }}%">
          @endfor
          <col style="width:{{ $totW }}%">
          <col style="width:{{ $personW }}%">
        </colgroup>
        <thead>
          @php $renderThead(); @endphp
        </thead>
        <tbody>
          @if(count($mainDrivers) > 0)
            @php $renderDriverRows($mainDrivers); @endphp
            <tr class="totals-row">
              <td class="name-cell" rowspan="3">รวมทุกคน</td>
              <td class="item-cell">ค่าวิ่ง</td>
              @foreach($days as $day)
                @php $v = $mainTotals['dayTotals'][$day]['delivery'] ?? 0; @endphp
                <td class="num" data-date="{{ $day }}" data-field="delivery">{{ $v != 0 ? number_format($v) : '-' }}</td>
              @endforeach
              <td class="num tot-col">{{ $mainTotals['grandDelivery'] != 0 ? number_format($mainTotals['grandDelivery']) : '-' }}</td>
              @if($mode==='week')<td class="num cyan-cell">{{ $mainTotals['grandDelivery'] != 0 ? number_format($mainTotals['grandDelivery']) : '-' }}</td>@endif
            </tr>
            <tr class="totals-row">
              <td class="item-cell">ค่า OT</td>
              @foreach($days as $day)
                @php $v = $mainTotals['dayTotals'][$day]['ot'] ?? 0; @endphp
                <td class="num" data-date="{{ $day }}" data-field="ot">{{ $v != 0 ? number_format($v) : '-' }}</td>
              @endforeach
              <td class="num tot-col">{{ $mainTotals['grandOt'] != 0 ? number_format($mainTotals['grandOt']) : '-' }}</td>
              @if($mode==='week')<td class="num cyan-cell">{{ $mainTotals['grandOt'] != 0 ? number_format($mainTotals['grandOt']) : '-' }}</td>@endif
            </tr>
            <tr class="totals-row">
              <td class="item-cell">ค่ายก</td>
              @foreach($days as $day)
                @php $v = $mainTotals['dayTotals'][$day]['handling'] ?? 0; @endphp
                <td class="num" data-date="{{ $day }}" data-field="handling">{{ $v != 0 ? number_format($v) : '-' }}</td>
              @endforeach
              <td class="num tot-col">{{ $mainTotals['grandHandling'] != 0 ? number_format($mainTotals['grandHandling']) : '-' }}</td>
              @if($mode==='week')<td class="num cyan-cell">{{ $mainTotals['grandHandling'] != 0 ? number_format($mainTotals['grandHandling']) : '-' }}</td>@endif
            </tr>
            <tr class="grand-row">
              <td colspan="{{ count($days) + 2 }}">{{ $mode==='month' ? 'รวมทั้งเดือน (หน้านี้)' : 'รวมทั้งสัปดาห์ (หน้านี้)' }}</td>
              <td class="grand-val">฿{{ number_format($mainTotals['grandTotal']) }}</td>
            </tr>
          @elseif(count($driverGrid) === 0)
          <tr>
            <td colspan="{{ count($days) + 4 }}" style="padding:30px;color:#9ca3af">ไม่พบข้อมูลคนขับ</td>
          </tr>
          @endif
        </tbody>
      </table>

      @if(count($motoDrivers) > 0)
      <div class="print-page-break">
        <table class="calgrid">
          <colgroup>
            <col style="width:{{ $nameW }}%">
            <col style="width:{{ $itemW }}%">
            @for($i=0;$i<$dayCount;$i++)
              <col style="width:{{ $dayW }}%">
            @endfor
            <col style="width:{{ $totW }}%">
            <col style="width:{{ $personW }}%">
          </colgroup>
          <thead>
            @php $renderThead('(ต่อ)'); @endphp
          </thead>
          <tbody>
            @php $renderDriverRows($motoDrivers); @endphp

            <tr class="totals-row">
              <td class="name-cell" rowspan="3">รวมทุกคน</td>
              <td class="item-cell">ค่าวิ่ง</td>
              @foreach($days as $day)
                @php $v = $motoTotals['dayTotals'][$day]['delivery'] ?? 0; @endphp
                <td class="num" data-date="{{ $day }}" data-field="delivery">{{ $v != 0 ? number_format($v) : '-' }}</td>
              @endforeach
              <td class="num tot-col">{{ $motoTotals['grandDelivery'] != 0 ? number_format($motoTotals['grandDelivery']) : '-' }}</td>
              @if($mode==='week')<td class="num cyan-cell">{{ $motoTotals['grandDelivery'] != 0 ? number_format($motoTotals['grandDelivery']) : '-' }}</td>@endif
            </tr>
            <tr class="totals-row">
              <td class="item-cell">ค่า OT</td>
              @foreach($days as $day)
                @php $v = $motoTotals['dayTotals'][$day]['ot'] ?? 0; @endphp
                <td class="num" data-date="{{ $day }}" data-field="ot">{{ $v != 0 ? number_format($v) : '-' }}</td>
              @endforeach
              <td class="num tot-col">{{ $motoTotals['grandOt'] != 0 ? number_format($motoTotals['grandOt']) : '-' }}</td>
              @if($mode==='week')<td class="num cyan-cell">{{ $motoTotals['grandOt'] != 0 ? number_format($motoTotals['grandOt']) : '-' }}</td>@endif
            </tr>
            <tr class="totals-row">
              <td class="item-cell">ค่ายก</td>
              @foreach($days as $day)
                @php $v = $motoTotals['dayTotals'][$day]['handling'] ?? 0; @endphp
                <td class="num" data-date="{{ $day }}" data-field="handling">{{ $v != 0 ? number_format($v) : '-' }}</td>
              @endforeach
              <td class="num tot-col">{{ $motoTotals['grandHandling'] != 0 ? number_format($motoTotals['grandHandling']) : '-' }}</td>
              @if($mode==='week')<td class="num cyan-cell">{{ $motoTotals['grandHandling'] != 0 ? number_format($motoTotals['grandHandling']) : '-' }}</td>@endif
            </tr>
            <tr class="grand-row">
              <td colspan="{{ count($days) + 2 }}">{{ $mode==='month' ? 'รวมทั้งเดือน (หน้านี้)' : 'รวมทั้งสัปดาห์ (หน้านี้)' }}</td>
              <td class="grand-val">฿{{ number_format($motoTotals['grandTotal']) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      @endif
    </div>
  </div>
</main>

<script>
(function () {
  var printBtn = document.getElementById('printReportBtn');
  var html = document.documentElement;

  // ── ปรับ font-size/padding จริง (ไม่ใช่ transform) ให้แต่ละ "หน้า" (แต่ละ table.calgrid) ──
  // เต็มพอดีกับกระดาษ A4 แนวนอน ไม่ใช่แค่บีบให้เล็กลงอย่างเดียว แต่ขยายขึ้นได้ด้วยถ้าเนื้อหาน้อย
  // เพื่อไม่ให้เหลือที่ว่างเยอะเกินไปตอนคนขับกลุ่มหลัง (กบ/joey/แมน) มีแค่ไม่กี่แถว
  // หมายเหตุ: ถ้ามี "กบ" ตารางจะถูกแบ่งเป็น 2 <table> (ดู .print-page-break) เพื่อบังคับขึ้นหน้าใหม่จริง ๆ
  // แต่ละตารางมีตัวแปร --print-scale ของตัวเอง (ตั้งแบบ inline) เพราะแต่ละหน้ามีปริมาณข้อมูลไม่เท่ากัน
  function fitScaleForTable(tableEl, pageH, minScale, maxScale, step) {
    var scale = 1;
    tableEl.style.setProperty('--print-scale', scale.toFixed(3));
    var height = tableEl.getBoundingClientRect().height;
    var guard = 0;

    if (height > pageH) {
      // เนื้อหาเกินหน้า → บีบลงจนพอดี
      while (height > pageH && scale > minScale && guard < 400) {
        scale = Math.max(minScale, scale - step);
        tableEl.style.setProperty('--print-scale', scale.toFixed(3));
        height = tableEl.getBoundingClientRect().height;
        guard++;
      }
    } else {
      // เนื้อหาน้อยกว่าหน้า → ขยายขึ้นจนเกือบเต็มหน้า (หยุดก่อนที่จะล้นหน้า)
      var lastGoodScale = scale;
      while (height <= pageH && scale < maxScale && guard < 400) {
        lastGoodScale = scale;
        scale = Math.min(maxScale, scale + step);
        tableEl.style.setProperty('--print-scale', scale.toFixed(3));
        height = tableEl.getBoundingClientRect().height;
        guard++;
      }
      if (height > pageH) {
        tableEl.style.setProperty('--print-scale', lastGoodScale.toFixed(3));
      }
    }
  }

  function fitTableToOnePage() {
    var tables = document.querySelectorAll('table.calgrid');
    var scrollEl = document.querySelector('.grid-scroll');
    if (!tables.length || !scrollEl) return;

    var mmToPx = 96 / 25.4;
    var margin = 8;
    var pageW = (297 - margin * 2) * mmToPx;
    var pageH = (210 - margin * 2) * mmToPx;

    html.classList.add('print-mode');
    scrollEl.style.width = pageW + 'px';
    html.style.setProperty('--print-scale', '1');

    tables.forEach(function (t) {
      fitScaleForTable(t, pageH, 0.55, 1.6, 0.02);
    });
  }

  function resetTableScale() {
    var scrollEl = document.querySelector('.grid-scroll');
    html.classList.remove('print-mode');
    html.style.removeProperty('--print-scale');
    if (scrollEl) scrollEl.style.width = '';
    document.querySelectorAll('table.calgrid').forEach(function (t) {
      t.style.removeProperty('--print-scale');
    });
  }

  printBtn.addEventListener('click', function () {
    fitTableToOnePage();
    window.print();
  });

  window.addEventListener('beforeprint', fitTableToOnePage);
  window.addEventListener('afterprint', resetTableScale);
  if (window.matchMedia) {
    var mql = window.matchMedia('print');
    if (mql.addEventListener) {
      mql.addEventListener('change', function (m) { if (m.matches) fitTableToOnePage(); else resetTableScale(); });
    } else if (mql.addListener) {
      mql.addListener(function (m) { if (m.matches) fitTableToOnePage(); else resetTableScale(); });
    }
  }
})();
</script>

@if($mode === 'week')
<script>
(function () {
  var THAI_MONTHS = ['', 'มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];

  var wrap = document.getElementById('weekPickerWrap');
  var inputBox = document.getElementById('weekPickerInput');
  var dropdown = document.getElementById('weekPickerDropdown');
  var display = document.getElementById('weekPickerDisplay');
  var hidden = document.getElementById('weekStartHidden');
  var monthLabel = document.getElementById('wpMonthLabel');
  var daysGrid = document.getElementById('wpDaysGrid');
  var prevBtn = document.getElementById('wpPrevBtn');
  var nextBtn = document.getElementById('wpNextBtn');
  var clearBtn = document.getElementById('wpClearBtn');
  var todayBtn = document.getElementById('wpTodayBtn');
  var rangeHint = document.getElementById('wpRangeHint');

  function parseISO(s) {
    var p = s.split('-');
    return new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
  }
  function toISO(d) {
    var y = d.getFullYear();
    var m = String(d.getMonth() + 1).padStart(2, '0');
    var day = String(d.getDate()).padStart(2, '0');
    return y + '-' + m + '-' + day;
  }
  function toThaiDMY(d) {
    var dd = String(d.getDate()).padStart(2, '0');
    var mm = String(d.getMonth() + 1).padStart(2, '0');
    return dd + '/' + mm + '/' + d.getFullYear();
  }
  function mondayOf(d) {
    var day = d.getDay();
    var diff = (day === 0) ? -6 : (1 - day);
    var m = new Date(d);
    m.setDate(d.getDate() + diff);
    m.setHours(0,0,0,0);
    return m;
  }
  function sameDate(a, b) {
    return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
  }

  var initial = hidden.value ? parseISO(hidden.value) : new Date();
  var selectedMonday = mondayOf(initial);
  var viewYear = selectedMonday.getFullYear();
  var viewMonth = selectedMonday.getMonth();
  var today = new Date(); today.setHours(0,0,0,0);

  function updateDisplay() {
    var sunday = new Date(selectedMonday);
    sunday.setDate(selectedMonday.getDate() + 6);
    display.textContent = toThaiDMY(selectedMonday);
    rangeHint.textContent = toThaiDMY(selectedMonday) + '  –  ' + toThaiDMY(sunday);
    hidden.value = toISO(selectedMonday);
  }

  function renderCalendar() {
    monthLabel.textContent = THAI_MONTHS[viewMonth + 1] + ' ' + (viewYear + 543);
    daysGrid.innerHTML = '';

    var firstOfMonth = new Date(viewYear, viewMonth, 1);
    var startOffset = (firstOfMonth.getDay() + 6) % 7;
    var gridStart = new Date(firstOfMonth);
    gridStart.setDate(firstOfMonth.getDate() - startOffset);

    var sundaySelected = new Date(selectedMonday);
    sundaySelected.setDate(selectedMonday.getDate() + 6);

    for (var i = 0; i < 42; i++) {
      var cellDate = new Date(gridStart);
      cellDate.setDate(gridStart.getDate() + i);
      cellDate.setHours(0,0,0,0);

      var cell = document.createElement('div');
      cell.className = 'wp-day-cell';

      var inWeek = cellDate >= selectedMonday && cellDate <= sundaySelected;
      if (inWeek) {
        cell.classList.add('in-week');
        if (sameDate(cellDate, selectedMonday)) cell.classList.add('week-start');
        if (sameDate(cellDate, sundaySelected)) cell.classList.add('week-end');
      }

      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'wp-day-btn';
      btn.textContent = cellDate.getDate();
      if (cellDate.getMonth() !== viewMonth) btn.classList.add('muted');
      if (sameDate(cellDate, today)) btn.classList.add('today');
      if (inWeek) btn.classList.add('selected');

      (function (d) {
        btn.addEventListener('click', function () {
          selectedMonday = mondayOf(d);
          viewYear = selectedMonday.getFullYear();
          viewMonth = selectedMonday.getMonth();
          updateDisplay();
          renderCalendar();
        });
      })(cellDate);

      cell.appendChild(btn);
      daysGrid.appendChild(cell);
    }
  }

  prevBtn.addEventListener('click', function () {
    viewMonth--;
    if (viewMonth < 0) { viewMonth = 11; viewYear--; }
    renderCalendar();
  });
  nextBtn.addEventListener('click', function () {
    viewMonth++;
    if (viewMonth > 11) { viewMonth = 0; viewYear++; }
    renderCalendar();
  });
  todayBtn.addEventListener('click', function () {
    var t = new Date(); t.setHours(0,0,0,0);
    selectedMonday = mondayOf(t);
    viewYear = selectedMonday.getFullYear();
    viewMonth = selectedMonday.getMonth();
    updateDisplay();
    renderCalendar();
  });
  clearBtn.addEventListener('click', function () {
    var t = new Date(); t.setHours(0,0,0,0);
    selectedMonday = mondayOf(t);
    viewYear = selectedMonday.getFullYear();
    viewMonth = selectedMonday.getMonth();
    updateDisplay();
    renderCalendar();
    closeDropdown();
  });

  function openDropdown() {
    dropdown.classList.add('open');
    inputBox.classList.add('open');
    document.addEventListener('mousedown', onOutsideClick);
  }
  function closeDropdown() {
    dropdown.classList.remove('open');
    inputBox.classList.remove('open');
    document.removeEventListener('mousedown', onOutsideClick);
  }
  function onOutsideClick(e) {
    if (!wrap.contains(e.target)) closeDropdown();
  }
  inputBox.addEventListener('click', function () {
    if (dropdown.classList.contains('open')) closeDropdown();
    else openDropdown();
  });

  updateDisplay();
  renderCalendar();
})();
</script>
@endif

<!-- ⭐ Inline Edit JavaScript -->
<script>
(function () {
  function parseNum(s) {
    if (!s && s !== 0) return 0;
    // เก็บเครื่องหมายลบ (-) ไว้ เพื่อรองรับค่าติดลบ (เช่น รายการหักเงิน/ปรับยอด)
    // ลบเฉพาะ comma (,) และช่องว่างออกเท่านั้น ห้ามลบ "-" ทิ้ง
    return parseFloat(String(s).replace(/,/g, '').replace(/\s/g, '')) || 0;
  }
  function fmtNum(n) {
    // แสดงผลทั้งค่าบวกและค่าลบ ส่วนค่า 0 ให้แสดงเป็น "-" แทนการเว้นว่าง
    return n !== 0 ? n.toLocaleString('en-US') : '-';
  }
  function recalcTotals() {
    document.querySelectorAll('table.calgrid').forEach(function (table) {
      var editableCells = table.querySelectorAll('td.num.editable');
      var dayMap = {};
      var driverMap = {};
      editableCells.forEach(function (cell) {
        var date = cell.getAttribute('data-date');
        var field = cell.getAttribute('data-field');
        var driver = cell.getAttribute('data-driver');
        var val = parseNum(cell.getAttribute('data-value'));
        if (!dayMap[date]) dayMap[date] = { delivery: 0, ot: 0, handling: 0 };
        if (!driverMap[driver]) driverMap[driver] = { delivery: 0, ot: 0, handling: 0 };
        dayMap[date][field] += val;
        driverMap[driver][field] += val;
      });

      // อัปเดตยอดรวมของแต่ละคนในตารางนี้ (tot-col และ person-tot)
      table.querySelectorAll('tbody tr[data-driver-row="1"]').forEach(function (tr) {
        var driverName = tr.querySelector('td.num.editable')?.getAttribute('data-driver');
        if (!driverName || !driverMap[driverName]) return;
        var fields = ['delivery', 'ot', 'handling'];
        var personTotal = 0;
        for (var fi = 0; fi < 3; fi++) {
          var baseIdx = Array.from(tr.parentElement.children).indexOf(tr);
          var rowIdx = baseIdx - (baseIdx % 3) + fi;
          var targetRow = tr.parentElement.children[rowIdx];
          if (!targetRow) continue;
          var totCell = targetRow.querySelector('td.tot-col');
          if (totCell) {
            var v = driverMap[driverName][fields[fi]];
            totCell.textContent = fmtNum(v);
            personTotal += v;
          }
        }
        var personCell = tr.querySelector('td.person-tot');
        if (personCell) personCell.textContent = '฿' + fmtNum(personTotal);
      });

      // อัปเดต totals-row ("รวมทุกคน") ของตารางนี้เท่านั้น ไม่รวมข้ามหน้า
      var grandDelivery = 0, grandOt = 0, grandHandling = 0;
      table.querySelectorAll('tr.totals-row').forEach(function (tr) {
        var cells = tr.querySelectorAll('td.num:not(.tot-col):not(.cyan-cell)[data-date]');
        if (!cells.length) return;
        var field = cells[0].getAttribute('data-field');
        var sum = 0;
        cells.forEach(function (cell) {
          var date = cell.getAttribute('data-date');
          var val = dayMap[date] ? dayMap[date][field] : 0;
          cell.textContent = fmtNum(val);
          sum += val;
        });
        var totCell = tr.querySelector('td.tot-col');
        if (totCell) totCell.textContent = fmtNum(sum);
        var cyanCell = tr.querySelector('td.cyan-cell');
        if (cyanCell) cyanCell.textContent = fmtNum(sum);
        if (field === 'delivery') grandDelivery = sum;
        if (field === 'ot') grandOt = sum;
        if (field === 'handling') grandHandling = sum;
      });

      // อัปเดต grand-row ของตารางนี้
      var grandVal = table.querySelector('tr.grand-row td.grand-val');
      if (grandVal) grandVal.textContent = '฿' + fmtNum(grandDelivery + grandOt + grandHandling);
    });
  }

  document.addEventListener('click', function (e) {
    var td = e.target.closest('td.num.editable');
    if (!td || td.querySelector('.inline-input')) return;
    if (document.querySelector('td.num.editable .inline-input')) return;

    var oldVal = parseNum(td.getAttribute('data-value'));
    var driverName = td.getAttribute('data-driver');
    var date = td.getAttribute('data-date');
    var field = td.getAttribute('data-field');

    td.classList.add('editing');
    td.classList.remove('empty');

    var input = document.createElement('input');
    // ใช้ type="text" + inputmode="numeric" แทน type="number" เพราะเบราว์เซอร์บาง
    // (เช่น Chrome) จะไม่ยอมให้พิมพ์เครื่องหมาย "-" เลยถ้า input มี min>=0 กำกับอยู่
    // การใช้ text ทำให้พิมพ์ "-" ได้ตามปกติ แล้วค่อยตรวจสอบ/แปลงเป็นตัวเลขตอนบันทึก (parseNum)
    input.type = 'text';
    input.className = 'inline-input';
    input.value = oldVal || '';
    input.inputMode = 'numeric';
    input.pattern = '-?[0-9]*\\.?[0-9]*';
    input.autocomplete = 'off';

    td.textContent = '';
    td.appendChild(input);
    input.focus();
    input.select();

    var finished = false;
    function finish(save) {
      if (finished) return;
      finished = true;

      var newVal = parseNum(input.value);
      td.classList.remove('editing');
      if (input.parentElement === td) td.removeChild(input);

      if (save && newVal !== oldVal) {
        td.classList.add('saving');
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrfMeta ? csrfMeta.content : '';

        var formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('driver_name', driverName);
        formData.append('work_date', date);
        formData.append('field', field);
        formData.append('value', newVal);

        fetch('/oil/Deliveryfee/update-cell', {
          method: 'POST',
          body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          td.classList.remove('saving');
          if (data.success) {
            td.setAttribute('data-value', newVal);
            td.textContent = fmtNum(newVal);
            if (newVal === 0) td.classList.add('empty');
            td.classList.add('save-success');
            setTimeout(function () { td.classList.remove('save-success'); }, 700);
            recalcTotals();
          } else {
            throw new Error('Save failed');
          }
        })
        .catch(function (err) {
          td.classList.remove('saving');
          td.textContent = fmtNum(oldVal);
          if (oldVal === 0) td.classList.add('empty');
          td.classList.add('save-error');
          setTimeout(function () { td.classList.remove('save-error'); }, 700);
          console.error('Inline edit error:', err);
        });
      } else {
        td.textContent = fmtNum(oldVal);
        if (oldVal === 0) td.classList.add('empty');
      }
    }

    input.addEventListener('keydown', function (ev) {
      if (ev.key === 'Enter') { ev.preventDefault(); finish(true); }
      if (ev.key === 'Escape') { ev.preventDefault(); finish(false); }
    });
    input.addEventListener('blur', function () {
      setTimeout(function () { finish(true); }, 150);
    });
  });
})();
</script>

</body>
</html>