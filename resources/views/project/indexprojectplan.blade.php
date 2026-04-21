<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RabbitSolar — ระบบจัดการแผงโซล่าเซล</title>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<style>
:root {
  --bg:        #eef2f9;
  --surface:   #ffffff;
  --surface2:  #f5f7fc;
  --border:    #d8e0f0;
  --border-md: #b8c6e0;

  --blue:      #1646c8;
  --blue-lt:   #2d60f0;
  --blue-dk:   #0e34a0;
  --blue-bg:   rgba(22,70,200,0.07);
  --blue-bg2:  rgba(22,70,200,0.13);

  --navy:      #0b1d50;
  --slate:     #2e3f6e;
  --muted:     #5c6e99;
  --hint:      #8898bb;

  --green:     #0a8a5c;
  --green-bg:  rgba(10,138,92,0.10);
  --green-bd:  rgba(10,138,92,0.30);

  --red:       #c0192a;
  --red-bg:    rgba(192,25,42,0.09);
  --red-bd:    rgba(192,25,42,0.30);

  --amber:     #a05c00;
  --amber-bg:  rgba(160,92,0,0.09);
  --amber-bd:  rgba(160,92,0,0.28);

  --sidebar-w: 224px;
  --r-sm: 6px; --r-md: 10px; --r-lg: 14px; --r-xl: 18px;
  --sh-sm: 0 1px 4px rgba(11,29,80,0.07);
  --sh-md: 0 4px 18px rgba(11,29,80,0.09), 0 1px 4px rgba(11,29,80,0.05);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { font-size: 15px; }
body {
  font-family: 'Noto Sans Thai', sans-serif;
  background: var(--bg);
  color: var(--navy);
  line-height: 1.55;
  min-height: 100vh;
  -webkit-font-smoothing: antialiased;
}
body::before {
  content: '';
  position: fixed; inset: 0; z-index: 0;
  background-image: radial-gradient(circle, rgba(22,70,200,0.055) 1px, transparent 1px);
  background-size: 28px 28px;
  pointer-events: none;
}
.app { position: relative; z-index: 1; display: flex; min-height: 100vh; }

/* ════ SIDEBAR ════════════════════════════════ */
.sidebar {
  width: var(--sidebar-w);
  background: var(--navy);
  display: flex; flex-direction: column;
  position: fixed; top: 0; left: 0; bottom: 0; z-index: 200;
  transition: transform 0.25s cubic-bezier(.4,0,.2,1);
}
.sidebar-top { padding: 22px 18px 18px; border-bottom: 1px solid rgba(255,255,255,0.07); }
.logo-wrap { display: flex; align-items: center; gap: 10px; }
.logo-icon {
  width: 36px; height: 36px; flex-shrink: 0;
  background: var(--blue); border-radius: var(--r-md);
  display: flex; align-items: center; justify-content: center;
}
.logo-icon svg { width: 19px; height: 19px; stroke: #fff; }
.logo-title { font-size: 14.5px; font-weight: 800; color: #fff; line-height: 1.15; }
.logo-sub { font-size: 10px; color: rgba(255,255,255,0.38); margin-top: 1px; }
.sidebar-nav { padding: 14px 10px; flex: 1; overflow-y: auto; }
.nav-label {
  font-size: 9px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase;
  color: rgba(255,255,255,0.22); padding: 10px 10px 5px;
}
.nav-btn {
  width: 100%; display: flex; align-items: center; gap: 9px;
  padding: 9px 11px; border-radius: var(--r-md);
  background: transparent; border: none;
  color: rgba(255,255,255,0.48); font-family: inherit; font-size: 13.5px;
  cursor: pointer; transition: all 0.14s; text-align: left; margin-bottom: 1px;
}
.nav-btn svg { width: 15px; height: 15px; flex-shrink: 0; }
.nav-btn:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.82); }
.nav-btn.active { background: var(--blue); color: #fff; font-weight: 700; box-shadow: 0 3px 10px rgba(22,70,200,0.4); }
.sidebar-foot { padding: 14px 18px; border-top: 1px solid rgba(255,255,255,0.07); }
#clk { font-family: 'Space Mono', monospace; font-size: 19px; font-weight: 700; color: #fff; letter-spacing: 1.5px; display: block; }
#clk-date { font-size: 10.5px; color: rgba(255,255,255,0.32); margin-top: 4px; display: block; }

/* hamburger */
.hamburger {
  display: none;
  position: fixed; top: 14px; left: 14px; z-index: 300;
  width: 38px; height: 38px;
  background: var(--navy); border: none; border-radius: var(--r-md);
  cursor: pointer; align-items: center; justify-content: center;
  box-shadow: var(--sh-md);
}
.hamburger svg { width: 20px; height: 20px; stroke: #fff; }
.sidebar-overlay {
  display: none; position: fixed; inset: 0; z-index: 150;
  background: rgba(11,29,80,0.45); backdrop-filter: blur(2px);
}

/* ════ MAIN ════════════════════════════════ */
.main {
  margin-left: var(--sidebar-w);
  padding: 28px 32px;
  min-height: 100vh;
  width: calc(100% - var(--sidebar-w));
}
.page-hd {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 22px; flex-wrap: wrap; gap: 10px;
}
.page-hd h2 { font-size: 20px; font-weight: 800; color: var(--navy); letter-spacing: -0.2px; }
.page-hd p { font-size: 12.5px; color: var(--muted); margin-top: 2px; }
.status-chip {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 12px; background: var(--surface);
  border: 1px solid var(--border); border-radius: 20px;
  font-size: 11.5px; color: var(--muted); box-shadow: var(--sh-sm);
}
.status-chip strong { color: var(--navy); font-weight: 700; }
.online-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--green); flex-shrink: 0; }

/* metric cards */
.metrics { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; margin-bottom: 20px; }
.m-card {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: var(--r-xl); padding: 18px;
  box-shadow: var(--sh-sm); transition: box-shadow 0.2s, transform 0.2s;
  position: relative; overflow: hidden;
}
.m-card::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
  background: linear-gradient(90deg, var(--blue), var(--blue-lt));
}
.m-card:hover { box-shadow: var(--sh-md); transform: translateY(-2px); }
.m-icon { width: 34px; height: 34px; background: var(--blue-bg); border-radius: var(--r-md); display: flex; align-items: center; justify-content: center; margin-bottom: 12px; }
.m-icon svg { width: 16px; height: 16px; stroke: var(--blue); }
.m-val { font-size: 28px; font-weight: 800; color: var(--navy); line-height: 1; letter-spacing: -0.5px; font-family: 'Space Mono', monospace; }
.m-lbl { font-size: 11.5px; color: var(--muted); margin-top: 6px; }

/* card */
.card {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: var(--r-xl); padding: 20px 22px;
  margin-bottom: 14px; box-shadow: var(--sh-sm);
}
.card-title {
  font-size: 10.5px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;
  color: var(--blue); margin-bottom: 14px; display: flex; align-items: center; gap: 7px;
}
.card-title svg { width: 12px; height: 12px; stroke: var(--blue); }

/* grids */
.g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }

/* forms */
.fg { display: flex; flex-direction: column; gap: 4px; margin-bottom: 10px; }
.fg label { font-size: 10.5px; font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; color: var(--slate); }
.fg input, .fg select {
  font-family: inherit; font-size: 13.5px;
  padding: 8px 11px; background: var(--surface2);
  border: 1.5px solid var(--border); border-radius: var(--r-md);
  color: var(--navy); outline: none; transition: all 0.14s; width: 100%;
}
.fg input::placeholder { color: var(--hint); }
.fg input:focus, .fg select:focus { border-color: var(--blue); background: #fff; box-shadow: 0 0 0 3px rgba(22,70,200,0.10); }
.form-row { display: grid; gap: 10px; margin-bottom: 10px; }
.form-row.c2 { grid-template-columns: 1fr 1fr; }
.form-row.c3 { grid-template-columns: 1fr 1fr 1fr; }
.form-row.c4 { grid-template-columns: 1fr 1fr 1fr 1fr; }
.form-row .fg { margin: 0; }

/* buttons */
.btn {
  font-family: inherit; font-size: 12.5px; font-weight: 600;
  padding: 8px 16px; border-radius: var(--r-md);
  cursor: pointer; border: 1.5px solid var(--border);
  background: var(--surface); color: var(--muted);
  transition: all 0.14s; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;
}
.btn:hover { border-color: var(--blue); color: var(--blue); background: var(--blue-bg); }
.btn-blue { background: var(--blue); border-color: var(--blue); color: #fff; font-weight: 700; box-shadow: 0 2px 8px rgba(22,70,200,0.28); }
.btn-blue:hover { background: var(--blue-dk); border-color: var(--blue-dk); color: #fff; }
.btn-ok { background: var(--green-bg); border-color: var(--green-bd); color: var(--green); font-weight: 700; }
.btn-ok:hover { background: rgba(10,138,92,0.16); border-color: var(--green); }
.btn-cancel { background: var(--red-bg); border-color: var(--red-bd); color: var(--red); font-weight: 700; }
.btn-cancel:hover { background: rgba(192,25,42,0.15); border-color: var(--red); color: var(--red); }
.btn-sm { font-size: 11.5px; padding: 5px 11px; }
.btn-full { width: 100%; justify-content: center; padding: 10px; font-size: 13.5px; }

/* table */
.tbl-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: var(--r-lg); border: 1px solid var(--border); }
table { width: 100%; border-collapse: collapse; font-size: 13px; min-width: 580px; }
thead { background: var(--surface2); }
th { text-align: left; padding: 10px 14px; font-size: 10px; font-weight: 800; letter-spacing: 1.2px; text-transform: uppercase; color: var(--hint); white-space: nowrap; border-bottom: 1px solid var(--border); }
td { padding: 11px 14px; border-bottom: 1px solid rgba(216,224,240,0.55); vertical-align: middle; }
tbody tr:last-child td { border-bottom: none; }
tbody tr:hover td { background: var(--blue-bg); }

/* badges — bold solid-ish */
.badge {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 3px 10px; border-radius: 20px;
  font-size: 11px; font-weight: 800; letter-spacing: 0.3px; white-space: nowrap;
}
.b-done { background: var(--green-bg); color: var(--green); border: 1.5px solid var(--green-bd); }
.b-wait { background: var(--blue-bg); color: var(--blue); border: 1.5px solid rgba(22,70,200,0.25); }
.b-over { background: var(--red-bg); color: var(--red); border: 1.5px solid var(--red-bd); }

/* tag */
.tag { display: inline-block; padding: 2px 9px; border-radius: 5px; font-size: 11px; font-weight: 700; background: var(--blue-bg2); color: var(--blue); border: 1px solid rgba(22,70,200,0.22); }

/* alert */
.alert { padding: 11px 15px; border-radius: var(--r-md); font-size: 13px; margin-bottom: 14px; display: flex; align-items: center; gap: 9px; font-weight: 500; }
.al-warn { background: var(--red-bg); border: 1px solid var(--red-bd); color: var(--red); }
.al-ok   { background: var(--green-bg); border: 1px solid var(--green-bd); color: var(--green); }

/* divider */
.div { height: 1px; background: var(--border); margin: 12px 0; }

/* calendar */
.cal-hdr { display: grid; grid-template-columns: repeat(7,1fr); gap: 2px; margin-bottom: 4px; }
.cal-dn { font-size: 9.5px; font-weight: 800; letter-spacing: 0.5px; color: var(--hint); text-align: center; padding: 3px; }
.cal-grid { display: grid; grid-template-columns: repeat(7,1fr); gap: 3px; }
.cal-cell { aspect-ratio: 1; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 500; color: var(--muted); border: 1px solid transparent; transition: all 0.1s; }
.cal-cell.ev { background: var(--blue-bg2); color: var(--blue); border-color: rgba(22,70,200,0.22); font-weight: 700; }
.cal-cell.td { background: var(--blue) !important; color: #fff !important; border-color: var(--blue) !important; font-weight: 800; }
.cal-cell.ot { opacity: 0.18; }

/* progress */
.prog { height: 7px; background: var(--surface2); border-radius: 4px; overflow: hidden; border: 1px solid var(--border); }
.prog-fill { height: 100%; background: linear-gradient(90deg, var(--blue), var(--blue-lt)); border-radius: 4px; transition: width 0.6s cubic-bezier(.4,0,.2,1); }

/* section */
.sec { display: none; }
.sec.on { display: block; animation: fadeUp 0.2s ease-out; }
@keyframes fadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

/* dots */
.dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
.dot-g { background: var(--green); }
.dot-d { background: var(--hint); }

::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--border-md); border-radius: 3px; }

/* ══════════════════════
   RESPONSIVE
══════════════════════ */
@media (max-width: 1100px) {
  .metrics { grid-template-columns: repeat(2,1fr); }
  .g2 { grid-template-columns: 1fr; }
}
@media (max-width: 860px) {
  .sidebar { transform: translateX(-224px); }
  .sidebar.open { transform: translateX(0); }
  .sidebar-overlay.open { display: block; }
  .hamburger { display: flex; }
  .main { margin-left: 0; width: 100%; padding: 62px 16px 24px; }
  .page-hd h2 { font-size: 18px; }
  .form-row.c4 { grid-template-columns: 1fr 1fr; }
  .form-row.c3 { grid-template-columns: 1fr 1fr; }
  .form-row.c2 { grid-template-columns: 1fr; }
}
@media (max-width: 500px) {
  .metrics { grid-template-columns: 1fr 1fr; gap: 8px; }
  .m-val { font-size: 22px; }
  .form-row.c4, .form-row.c3 { grid-template-columns: 1fr; }
  .card { padding: 16px; }
  .main { padding: 56px 12px 24px; }
}
</style>
</head>
<body>
<div class="app">

<button class="hamburger" id="hamburger" onclick="toggleSidebar()" aria-label="เมนู">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
  </svg>
</button>
<div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

<!-- ═══ SIDEBAR ═══ -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-top">
    <div class="logo-wrap">
      <div class="logo-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <circle cx="12" cy="12" r="4" stroke="currentColor"/>
          <line x1="12" y1="2" x2="12" y2="5" stroke="currentColor"/>
          <line x1="12" y1="19" x2="12" y2="22" stroke="currentColor"/>
          <line x1="4.22" y1="4.22" x2="6.34" y2="6.34" stroke="currentColor"/>
          <line x1="17.66" y1="17.66" x2="19.78" y2="19.78" stroke="currentColor"/>
          <line x1="2" y1="12" x2="5" y2="12" stroke="currentColor"/>
          <line x1="19" y1="12" x2="22" y2="12" stroke="currentColor"/>
          <line x1="4.22" y1="19.78" x2="6.34" y2="17.66" stroke="currentColor"/>
          <line x1="17.66" y1="6.34" x2="19.78" y2="4.22" stroke="currentColor"/>
        </svg>
      </div>
      <div>
        <div class="logo-title">RabbitSolar</div>
        <div class="logo-sub">ระบบจัดการแผงโซล่าเซล</div>
      </div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-label">เมนูหลัก</div>
    <button class="nav-btn active" onclick="go('dashboard',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
      แดชบอร์ด
    </button>
    <button class="nav-btn" onclick="go('schedule',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      จัดตารางเวลา
    </button>
    <button class="nav-btn" onclick="go('log',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      บันทึกการทำงาน
    </button>
    <button class="nav-btn" onclick="go('report',this)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><polyline points="2 20 22 20"/></svg>
      รายงานสรุป
    </button>
  </nav>
  <div class="sidebar-foot">
    <span id="clk">--:--:--</span>
    <span id="clk-date">—</span>
  </div>
</aside>

<!-- ═══ MAIN ═══ -->
<main class="main">

<!-- DASHBOARD -->
<div id="tab-dashboard" class="sec on">
  <div class="page-hd">
    <div><h2>ภาพรวมระบบ</h2><p>สรุปสถานะการดูแลแผงโซล่าเซล</p></div>
    <div class="status-chip"><span class="online-dot"></span>ออนไลน์ · <strong>24 แผง</strong></div>
  </div>
  <div class="metrics">
    <div class="m-card">
      <div class="m-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" stroke="currentColor"/><line x1="12" y1="17" x2="12" y2="21" stroke="currentColor"/><line x1="8" y1="21" x2="16" y2="21" stroke="currentColor"/></svg></div>
      <div class="m-val">24</div><div class="m-lbl">แผงทั้งหมด</div>
    </div>
    <div class="m-card">
      <div class="m-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><polyline points="20 6 9 17 4 12" stroke="currentColor"/></svg></div>
      <div class="m-val" id="m-done">0</div><div class="m-lbl">ทำความสะอาดเดือนนี้</div>
    </div>
    <div class="m-card">
      <div class="m-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="12" cy="12" r="10" stroke="currentColor"/><polyline points="12 6 12 12 16 14" stroke="currentColor"/></svg></div>
      <div class="m-val" id="m-next" style="font-size:17px;letter-spacing:0">—</div><div class="m-lbl">นัดถัดไป</div>
    </div>
    <div class="m-card">
      <div class="m-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6" stroke="currentColor"/><line x1="8" y1="12" x2="21" y2="12" stroke="currentColor"/><line x1="8" y1="18" x2="21" y2="18" stroke="currentColor"/><circle cx="3" cy="6" r="1" fill="currentColor"/><circle cx="3" cy="12" r="1" fill="currentColor"/><circle cx="3" cy="18" r="1" fill="currentColor"/></svg></div>
      <div class="m-val" id="m-logs">0</div><div class="m-lbl">บันทึกทั้งหมด</div>
    </div>
  </div>
  <div id="alerts"></div>
  <div class="g2">
    <div class="card">
      <div class="card-title">
        <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/></svg>
        ตารางเดือนนี้
      </div>
      <div class="cal-hdr">
        <div class="cal-dn">อา</div><div class="cal-dn">จ</div><div class="cal-dn">อ</div>
        <div class="cal-dn">พ</div><div class="cal-dn">พฤ</div><div class="cal-dn">ศ</div><div class="cal-dn">ส</div>
      </div>
      <div class="cal-grid" id="mini-cal"></div>
      <div style="display:flex;gap:14px;margin-top:10px;flex-wrap:wrap">
        <span style="font-size:11px;color:var(--muted);display:flex;align-items:center;gap:5px">
          <span style="width:10px;height:10px;background:var(--blue-bg2);border:1.5px solid rgba(22,70,200,0.22);border-radius:3px;display:inline-block"></span>มีกำหนดการ
        </span>
        <span style="font-size:11px;color:var(--muted);display:flex;align-items:center;gap:5px">
          <span style="width:10px;height:10px;background:var(--blue);border-radius:3px;display:inline-block"></span>วันนี้
        </span>
      </div>
    </div>
    <div class="card">
      <div class="card-title">
        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="16" r="0.8" fill="currentColor"/></svg>
        งานวันนี้ & ประสิทธิภาพ
      </div>
      <div id="today-tasks" style="font-size:13px;min-height:52px"><span style="color:var(--hint)">ไม่มีงานกำหนดวันนี้</span></div>
      <div class="div"></div>
      <div style="font-size:10px;font-weight:800;letter-spacing:1.2px;text-transform:uppercase;color:var(--hint);margin-bottom:8px">ประสิทธิภาพการดูแล</div>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:7px">
        <span style="font-size:13px;color:var(--muted)" id="eff-lbl">—</span>
        <span style="font-size:16px;font-weight:800;color:var(--blue);font-family:'Space Mono',monospace" id="eff-val">0%</span>
      </div>
      <div class="prog"><div class="prog-fill" id="eff-bar" style="width:0%"></div></div>
      <div style="display:flex;justify-content:space-between;margin-top:12px;font-size:13px">
        <span style="color:var(--muted)">กำหนดการรอดำเนินการ</span>
        <span style="font-weight:800;color:var(--navy);font-family:'Space Mono',monospace" id="pend-n">0</span>
      </div>
    </div>
  </div>
</div>

<!-- SCHEDULE -->
<div id="tab-schedule" class="sec">
  <div class="page-hd"><div><h2>จัดตารางเวลา</h2><p>เพิ่มและบริหารกำหนดการทำความสะอาด</p></div></div>
  <div class="g2" style="align-items:start">
    <div class="card">
      <div class="card-title">เพิ่มกำหนดการ</div>
      <div class="form-row c4">
        <div class="fg"><label>วันที่</label><input type="date" id="s-date"></div>
        <div class="fg"><label>เวลา</label><input type="time" id="s-time" value="08:00"></div>
        <div class="fg"><label>โซน</label>
          <select id="s-zone"><option value="ทั้งหมด">ทั้งหมด</option><option value="โซน A">โซน A</option><option value="โซน B">โซน B</option><option value="โซน C">โซน C</option></select>
        </div>
        <div class="fg"><label>ประเภท</label>
          <select id="s-type"><option value="ล้างน้ำ">ล้างน้ำ</option><option value="เช็ดแห้ง">เช็ดแห้ง</option><option value="ทำความสะอาดเชิงลึก">เชิงลึก</option></select>
        </div>
      </div>
      <div class="fg"><label>หมายเหตุ</label><input type="text" id="s-note" placeholder="รายละเอียดเพิ่มเติม..."></div>
      <button class="btn btn-blue btn-full" onclick="addSch()">+ เพิ่มกำหนดการ</button>
    </div>
    <div class="card">
      <div class="card-title">ตารางซ้ำอัตโนมัติ</div>
      <div class="form-row c3">
        <div class="fg"><label>ความถี่</label>
          <select id="r-freq"><option value="weekly">ทุกสัปดาห์</option><option value="biweekly">ทุก 2 สัปดาห์</option><option value="monthly">ทุกเดือน</option></select>
        </div>
        <div class="fg"><label>วัน</label>
          <select id="r-day"><option value="1">จันทร์</option><option value="2">อังคาร</option><option value="3">พุธ</option><option value="4">พฤหัสบดี</option><option value="5">ศุกร์</option><option value="6">เสาร์</option><option value="0">อาทิตย์</option></select>
        </div>
        <div class="fg"><label>เวลา</label><input type="time" id="r-time" value="07:30"></div>
      </div>
      <div class="form-row c3">
        <div class="fg"><label>โซน</label>
          <select id="r-zone"><option value="ทั้งหมด">ทั้งหมด</option><option value="โซน A">โซน A</option><option value="โซน B">โซน B</option><option value="โซน C">โซน C</option></select>
        </div>
        <div class="fg"><label>จำนวนครั้ง</label><input type="number" id="r-cnt" value="4" min="1" max="24"></div>
        <div class="fg" style="justify-content:flex-end">
          <label style="opacity:0;pointer-events:none">-</label>
          <button class="btn btn-blue" onclick="addRepeat()" style="width:100%;justify-content:center">สร้างตาราง</button>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:8px">
      <div class="card-title" style="margin:0">กำหนดการทั้งหมด</div>
      <span id="sch-cnt" style="font-size:11px;color:var(--hint);font-weight:700;font-family:'Space Mono',monospace">0 รายการ</span>
    </div>
    <div class="tbl-wrap">
      <table>
        <thead><tr><th>วันที่</th><th>เวลา</th><th>โซน</th><th>ประเภท</th><th>หมายเหตุ</th><th>สถานะ</th><th>การดำเนินการ</th></tr></thead>
        <tbody id="sch-body"></tbody>
      </table>
    </div>
  </div>
</div>

<!-- LOG -->
<div id="tab-log" class="sec">
  <div class="page-hd"><div><h2>บันทึกการทำงาน</h2><p>บันทึกและดูประวัติการทำความสะอาด</p></div></div>
  <div class="card">
    <div class="card-title">บันทึกการทำความสะอาดครั้งใหม่</div>
    <div class="form-row c4">
      <div class="fg"><label>วันที่</label><input type="date" id="l-date"></div>
      <div class="fg"><label>เวลา</label><input type="time" id="l-time" value="09:00"></div>
      <div class="fg"><label>โซน</label>
        <select id="l-zone"><option value="ทั้งหมด">ทั้งหมด</option><option value="โซน A">โซน A</option><option value="โซน B">โซน B</option><option value="โซน C">โซน C</option></select>
      </div>
      <div class="fg"><label>ประเภท</label>
        <select id="l-type"><option value="ล้างน้ำ">ล้างน้ำ</option><option value="เช็ดแห้ง">เช็ดแห้ง</option><option value="ทำความสะอาดเชิงลึก">เชิงลึก</option></select>
      </div>
    </div>
    <div class="form-row c3">
      <div class="fg"><label>ผู้ดำเนินการ</label><input type="text" id="l-who" placeholder="ชื่อช่าง / ทีม"></div>
      <div class="fg"><label>สภาพแผง (ก่อน)</label>
        <select id="l-cond"><option value="สกปรกมาก">สกปรกมาก</option><option value="สกปรกปานกลาง">ปานกลาง</option><option value="สกปรกน้อย">สกปรกน้อย</option></select>
      </div>
      <div class="fg"><label>หมายเหตุ</label><input type="text" id="l-note" placeholder="สังเกตพิเศษ..."></div>
    </div>
    <button class="btn btn-blue" onclick="addLog()" style="margin-top:4px">+ บันทึกการทำความสะอาด</button>
  </div>
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:8px">
      <div class="card-title" style="margin:0">ประวัติทั้งหมด</div>
      <div style="display:flex;gap:7px;flex-wrap:wrap">
        <select id="f-zone" onchange="renderLog()" style="font-size:12px;padding:6px 10px;background:var(--surface2);border:1.5px solid var(--border);border-radius:var(--r-md);color:var(--navy);font-family:inherit">
          <option value="">ทุกโซน</option><option value="ทั้งหมด">ทั้งหมด</option><option value="โซน A">โซน A</option><option value="โซน B">โซน B</option><option value="โซน C">โซน C</option>
        </select>
        <select id="f-month" onchange="renderLog()" style="font-size:12px;padding:6px 10px;background:var(--surface2);border:1.5px solid var(--border);border-radius:var(--r-md);color:var(--navy);font-family:inherit">
          <option value="">ทุกเดือน</option>
        </select>
      </div>
    </div>
    <div class="tbl-wrap">
      <table>
        <thead><tr><th>วันที่</th><th>เวลา</th><th>โซน</th><th>ประเภท</th><th>สภาพ</th><th>ผู้ดำเนินการ</th><th>หมายเหตุ</th><th>ลบ</th></tr></thead>
        <tbody id="log-body"></tbody>
      </table>
    </div>
  </div>
</div>

<!-- REPORT -->
<div id="tab-report" class="sec">
  <div class="page-hd"><div><h2>รายงานสรุป</h2><p>วิเคราะห์ผลการดูแลแผงโซล่าเซล</p></div></div>
  <div class="metrics">
    <div class="m-card">
      <div class="m-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><polyline points="20 6 9 17 4 12" stroke="currentColor"/></svg></div>
      <div class="m-val" id="r-tot">0</div><div class="m-lbl">ครั้งทั้งหมด</div>
    </div>
    <div class="m-card">
      <div class="m-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor"/><line x1="3" y1="10" x2="21" y2="10" stroke="currentColor"/></svg></div>
      <div class="m-val" id="r-mon">0</div><div class="m-lbl">เดือนนี้</div>
    </div>
    <div class="m-card">
      <div class="m-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="12" cy="12" r="10" stroke="currentColor"/><line x1="12" y1="8" x2="12" y2="12" stroke="currentColor"/></svg></div>
      <div class="m-val" id="r-avg" style="font-size:22px">—</div><div class="m-lbl">เฉลี่ย (วัน/ครั้ง)</div>
    </div>
    <div class="m-card">
      <div class="m-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor"/><circle cx="9" cy="7" r="4" stroke="currentColor"/></svg></div>
      <div class="m-val" id="r-who" style="font-size:22px">—</div><div class="m-lbl">ผู้ดำเนินการ</div>
    </div>
  </div>
  <div class="g2">
    <div class="card">
      <div class="card-title">จำนวนการทำความสะอาดรายเดือน</div>
      <div style="position:relative;height:210px"><canvas id="c-bar"></canvas></div>
    </div>
    <div class="card">
      <div class="card-title">สัดส่วนประเภท</div>
      <div style="position:relative;height:210px"><canvas id="c-pie"></canvas></div>
    </div>
  </div>
  <div class="card">
    <div class="card-title">สรุปรายโซน</div>
    <div class="tbl-wrap">
      <table>
        <thead><tr><th>โซน</th><th>ครั้งทั้งหมด</th><th>ล้างน้ำ</th><th>เช็ดแห้ง</th><th>เชิงลึก</th><th>ครั้งล่าสุด</th></tr></thead>
        <tbody id="zone-body"></tbody>
      </table>
    </div>
  </div>
</div>

</main>
</div>

<script>
let S = JSON.parse(localStorage.getItem('rs3_s')||'[]');
let L = JSON.parse(localStorage.getItem('rs3_l')||'[]');
let cBar=null, cPie=null;

const sv  = () => { localStorage.setItem('rs3_s',JSON.stringify(S)); localStorage.setItem('rs3_l',JSON.stringify(L)); };
const tod  = () => new Date().toISOString().slice(0,10);
const tmon = () => new Date().toISOString().slice(0,7);

function fDate(d){ const p=d.split('-'); return ${p[2]}/${p[1]}/${+p[0]+543}; }
function fMon(ym){ const p=ym.split('-'); const mn=['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.']; return ${mn[+p[1]]} ${+p[0]+543}; }

/* clock */
(function tick(){
  const n=new Date(), pad=v=>String(v).padStart(2,'0');
  document.getElementById('clk').textContent=`${pad(n.getHours())}:${pad(n.getMinutes())}:${pad(n.getSeconds())}`;
  const days=['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'];
  const mons=['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
  document.getElementById('clk-date').textContent=`${days[n.getDay()]} ${n.getDate()} ${mons[n.getMonth()]} ${n.getFullYear()+543}`;
  setTimeout(tick,1000);
})();

/* sidebar mobile */
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('overlay').classList.toggle('open');
}

/* navigation */
function go(name,el){
  document.querySelectorAll('.sec').forEach(s=>s.classList.remove('on'));
  document.querySelectorAll('.nav-btn').forEach(b=>b.classList.remove('active'));
  document.getElementById('tab-'+name).classList.add('on');
  el.classList.add('active');
  if(window.innerWidth<=860){ document.getElementById('sidebar').classList.remove('open'); document.getElementById('overlay').classList.remove('open'); }
  if(name==='report') rptRender();
  if(name==='dashboard') dashRender();
}

document.getElementById('s-date').value=tod();
document.getElementById('l-date').value=tod();

/* SCHEDULE */
function addSch(){
  const d=document.getElementById('s-date').value;
  if(!d){alert('กรุณาเลือกวันที่');return;}
  S.push({id:Date.now(),date:d,time:document.getElementById('s-time').value,zone:document.getElementById('s-zone').value,type:document.getElementById('s-type').value,note:document.getElementById('s-note').value,status:'pending'});
  S.sort((a,b)=>a.date.localeCompare(b.date));
  document.getElementById('s-note').value='';
  sv();schRender();dashRender();
}
function addRepeat(){
  const freq=document.getElementById('r-freq').value,day=+document.getElementById('r-day').value,time=document.getElementById('r-time').value,zone=document.getElementById('r-zone').value,cnt=+document.getElementById('r-cnt').value||4;
  const step=freq==='biweekly'?14:freq==='monthly'?28:7;
  let added=0;
  for(let w=1;w<=104&&added<cnt;w++){
    const d=new Date();d.setDate(d.getDate()+w*step);
    if(d.getDay()===day){
      const ds=d.toISOString().slice(0,10);
      if(!S.find(s=>s.date===ds&&s.zone===zone)){S.push({id:Date.now()+added,date:ds,time,zone,type:'ล้างน้ำ',note:'ตารางซ้ำ',status:'pending'});added++;}
    }
  }
  S.sort((a,b)=>a.date.localeCompare(b.date));
  sv();schRender();dashRender();
  alert(สร้างกำหนดการ ${added} รายการเรียบร้อยแล้ว);
}
function markSch(id){
  const s=S.find(x=>x.id===id);
  if(s){s.status=s.status==='done'?'pending':'done';sv();schRender();dashRender();}
}
function delSch(id){
  if(!confirm('ยืนยันการลบ?'))return;
  S=S.filter(x=>x.id!==id);sv();schRender();dashRender();
}
function schRender(){
  document.getElementById('sch-cnt').textContent=S.length+' รายการ';
  const tb=document.getElementById('sch-body');
  if(!S.length){tb.innerHTML='<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--hint)">ยังไม่มีกำหนดการ</td></tr>';return;}
  const t=tod();
  tb.innerHTML=S.map(s=>{
    const ov=s.date<t&&s.status==='pending';
    const bc=s.status==='done'?'b-done':ov?'b-over':'b-wait';
    const bl=s.status==='done'?'✓ เสร็จแล้ว':ov?'⚠ เกินกำหนด':'● รอดำเนินการ';
    const markLbl=s.status==='done'?'ยกเลิก':'✓ เสร็จ';
    const markCls=s.status==='done'?'btn btn-sm btn-cancel':'btn btn-sm btn-ok';
    return`<tr>
      <td style="font-weight:600">${fDate(s.date)}</td>
      <td style="font-family:'Space Mono',monospace;font-size:12px;color:var(--muted)">${s.time}</td>
      <td><span class="tag">${s.zone}</span></td>
      <td>${s.type}</td>
      <td style="color:var(--muted);max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${s.note||'—'}</td>
      <td><span class="badge ${bc}">${bl}</span></td>
      <td><div style="display:flex;gap:5px">
        <button class="${markCls}" onclick="markSch(${s.id})">${markLbl}</button>
        <button class="btn btn-sm btn-cancel" onclick="delSch(${s.id})">ลบ</button>
      </div></td>
    </tr>`;
  }).join('');
}

/* LOG */
function addLog(){
  const d=document.getElementById('l-date').value;
  if(!d){alert('กรุณาเลือกวันที่');return;}
  L.unshift({id:Date.now(),date:d,time:document.getElementById('l-time').value,zone:document.getElementById('l-zone').value,type:document.getElementById('l-type').value,who:document.getElementById('l-who').value,cond:document.getElementById('l-cond').value,note:document.getElementById('l-note').value});
  sv();renderLog();dashRender();updateMonths();
}
function delLog(id){
  if(!confirm('ยืนยันการลบ?'))return;
  L=L.filter(x=>x.id!==id);sv();renderLog();dashRender();
}
function renderLog(){
  const fz=document.getElementById('f-zone').value,fm=document.getElementById('f-month').value;
  let data=L;
  if(fz) data=data.filter(l=>l.zone===fz);
  if(fm) data=data.filter(l=>l.date.slice(0,7)===fm);
  const tb=document.getElementById('log-body');
  if(!data.length){tb.innerHTML='<tr><td colspan="8" style="text-align:center;padding:32px;color:var(--hint)">ยังไม่มีบันทึก</td></tr>';return;}
  tb.innerHTML=data.map(l=>`<tr>
    <td style="font-weight:600">${fDate(l.date)}</td>
    <td style="font-family:'Space Mono',monospace;font-size:12px;color:var(--muted)">${l.time}</td>
    <td><span class="tag">${l.zone}</span></td>
    <td>${l.type}</td><td>${l.cond}</td>
    <td>${l.who||'—'}</td>
    <td style="color:var(--muted);max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${l.note||'—'}</td>
    <td><button class="btn btn-sm btn-cancel" onclick="delLog(${l.id})">ลบ</button></td>
  </tr>`).join('');
}
function updateMonths(){
  const sel=document.getElementById('f-month');
  const mons=[...new Set(L.map(l=>l.date.slice(0,7)))].sort().reverse();
  const cur=sel.value;
  sel.innerHTML='<option value="">ทุกเดือน</option>'+mons.map(m=>`<option value="${m}"${m===cur?' selected':''}>${fMon(m)}</option>`).join('');
}

/* DASHBOARD */
function dashRender(){
  const mon=tmon(),t=tod();
  const done=L.filter(l=>l.date.slice(0,7)===mon).length;
  document.getElementById('m-done').textContent=done;
  document.getElementById('m-logs').textContent=L.length;
  const nxt=S.filter(s=>s.date>=t&&s.status==='pending').sort((a,b)=>a.date.localeCompare(b.date))[0];
  document.getElementById('m-next').textContent=nxt?fDate(nxt.date):'—';
  document.getElementById('pend-n').textContent=S.filter(s=>s.status==='pending').length;
  const eff=Math.min(100,Math.round(40+done*12));
  document.getElementById('eff-val').textContent=eff+'%';
  document.getElementById('eff-bar').style.width=eff+'%';
  document.getElementById('eff-lbl').textContent=eff>=80?'ระดับดีเยี่ยม':eff>=50?'ระดับดี':'ควรปรับปรุง';
  const tasks=S.filter(s=>s.date===t);
  const tt=document.getElementById('today-tasks');
  if(tasks.length){
    tt.innerHTML=tasks.map(s=>`<div style="display:flex;align-items:center;gap:9px;padding:8px 0;border-bottom:1px solid var(--border)">
      <span class="dot ${s.status==='done'?'dot-g':'dot-d'}"></span>
      <span style="flex:1;font-size:13px;color:var(--slate)">${s.time} · ${s.zone} · ${s.type}</span>
      <span class="badge ${s.status==='done'?'b-done':'b-wait'}">${s.status==='done'?'✓ เสร็จ':'● รอ'}</span>
    </div>`).join('');
  } else { tt.innerHTML='<span style="color:var(--hint)">ไม่มีงานกำหนดวันนี้</span>'; }
  const now=new Date(),y=now.getFullYear(),m=now.getMonth();
  const first=new Date(y,m,1).getDay(),days=new Date(y,m+1,0).getDate();
  const evDates=new Set(S.map(s=>s.date));
  let cal='';
  for(let i=0;i<first;i++) cal+=`<div class="cal-cell ot"></div>`;
  for(let d=1;d<=days;d++){
    const ds=`${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    cal+=`<div class="cal-cell${evDates.has(ds)?' ev':''}${ds===t?' td':''}">${d}</div>`;
  }
  document.getElementById('mini-cal').innerHTML=cal;
  const ov=S.filter(s=>s.date<t&&s.status==='pending');
  const ar=document.getElementById('alerts');
  if(ov.length){
    ar.innerHTML=`<div class="alert al-warn"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>มีกำหนดการค้างอยู่ <strong>${ov.length} รายการ</strong> ที่ยังไม่ได้ดำเนินการ</div>`;
  } else if(done>=1){
    ar.innerHTML=`<div class="alert al-ok"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>เดือนนี้ดำเนินการทำความสะอาดแล้ว <strong>${done} ครั้ง</strong> — ระบบปกติ</div>`;
  } else ar.innerHTML='';
}

/* REPORT */
function rptRender(){
  document.getElementById('r-tot').textContent=L.length;
  document.getElementById('r-mon').textContent=L.filter(l=>l.date.slice(0,7)===tmon()).length;
  const uniq=[...new Set(L.map(l=>l.who).filter(Boolean))];
  document.getElementById('r-who').textContent=uniq.length||'—';
  if(L.length>1){
    const so=[...L].sort((a,b)=>a.date.localeCompare(b.date));
    const diff=(new Date(so[so.length-1].date)-new Date(so[0].date))/86400000;
    document.getElementById('r-avg').textContent=diff>0?Math.round(diff/(L.length-1)):'—';
  }
  const monMap={};
  L.forEach(l=>{const m=l.date.slice(0,7);monMap[m]=(monMap[m]||0)+1;});
  const mKeys=Object.keys(monMap).sort();
  if(cBar)cBar.destroy();
  cBar=new Chart(document.getElementById('c-bar'),{type:'bar',data:{labels:mKeys.map(m=>fMon(m)),datasets:[{label:'ครั้ง',data:mKeys.map(k=>monMap[k]),backgroundColor:'rgba(22,70,200,0.72)',borderColor:'#1646c8',borderWidth:1,borderRadius:5,borderSkipped:false}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1,color:'#8898bb'},grid:{color:'rgba(216,224,240,0.55)'},border:{color:'rgba(216,224,240,0.55)'}},x:{ticks:{color:'#5c6e99'},grid:{display:false},border:{color:'rgba(216,224,240,0.55)'}}}}});
  const typeMap={};
  L.forEach(l=>{typeMap[l.type]=(typeMap[l.type]||0)+1;});
  if(cPie)cPie.destroy();
  cPie=new Chart(document.getElementById('c-pie'),{type:'doughnut',data:{labels:Object.keys(typeMap),datasets:[{data:Object.values(typeMap),backgroundColor:['#1646c8','#2d60f0','#0a8a5c'],borderColor:'#fff',borderWidth:3}]},options:{responsive:true,maintainAspectRatio:false,cutout:'62%',plugins:{legend:{position:'bottom',labels:{color:'#5c6e99',font:{size:12,family:"'Noto Sans Thai'"},padding:16,boxWidth:12}}}}});
  const zones=['ทั้งหมด','โซน A','โซน B','โซน C'];
  document.getElementById('zone-body').innerHTML=zones.map(z=>{
    const zl=L.filter(l=>l.zone===z);
    const last=[...zl].sort((a,b)=>b.date.localeCompare(a.date))[0];
    return`<tr><td><span class="tag">${z}</span></td><td style="font-weight:800;color:var(--blue);font-family:'Space Mono',monospace">${zl.length}</td><td>${zl.filter(l=>l.type==='ล้างน้ำ').length}</td><td>${zl.filter(l=>l.type==='เช็ดแห้ง').length}</td><td>${zl.filter(l=>l.type==='ทำความสะอาดเชิงลึก').length}</td><td style="color:var(--muted)">${last?fDate(last.date):'—'}</td></tr>`;
  }).join('');
}

schRender(); renderLog(); dashRender(); updateMonths();
</script>
</body>
</html>