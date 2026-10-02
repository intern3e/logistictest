{{-- resources/views/driver/billreceive.blade.php — ระบบรับเข้าบิล (ดึงจาก transaction_transport) --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>รับเข้าบิล — ระบบจัดการขนส่ง</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; }
:root {
  /* โทนสีชุดเดียวกับหน้า Deliverytrack (จ่ายงานขนส่งสินค้า) */
  --bg: #f0f2f5;
  --card: #ffffff;
  --line: #e9ecef;
  --line-strong: #dee2e6;
  --line-light: #f8f9fa;
  --ink: #1a2634;
  --ink2: #5c6b7a;
  --ink3: #8592a0;
  --ink4: #aab4bd;

  --primary: #2853d5;
  --primary-rgb: 40,83,213;
  --primary-hover: #1f42ab;
  --primary-light: #eaf0fc;
  --primary-dark: #2853d5;
  --primary-dark-hover: #1f42ab;

  --green: #2e7d32; --green-d: #1b5e20; --green-l: #e8f5e9;
  --amber: #ed6c02; --amber-d: #b45309; --amber-l: #fff4e5;
  --red: #c62828; --red-rgb: 198,40,40; --red-d: #a91f1f; --red-l: #ffebee;
  --violet: #6d28d9; --violet-d: #5b21b6; --violet-l: #f3eefc;

  --radius: 12px;
  --font: 'Sarabun', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  --font-mono: 'JetBrains Mono', 'Sarabun', monospace;
}

html, body { margin: 0; background: var(--bg); color: var(--ink); font-family: var(--font); -webkit-font-smoothing: antialiased; }
a { color: inherit; text-decoration: none; }

/* Topbar */
.topbar {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--line);
  position: sticky; top: 0; z-index: 50;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 24px; height: 68px;
}
.topbar .brand { font-weight: 700; font-size: 17px; display: flex; align-items: center; gap: 12px; color: var(--ink); }
.topbar .brand .tag { background: var(--primary-light); color: var(--primary); font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; letter-spacing: 0.5px; }
.topbar .right { display: flex; align-items: center; gap: 14px; font-size: 13px; color: var(--ink2); }
.topbar .user { display: inline-flex; align-items: center; gap: 8px; background: #fff; padding: 5px 14px 5px 5px; border-radius: 30px; font-weight: 600; color: var(--ink); border: 1px solid var(--line); box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.user-avatar { width: 26px; height: 26px; border-radius: 50%; background: var(--primary-dark); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0; }
.topbar a.back { color: var(--ink3); font-size: 13px; border: 1px solid var(--line); padding: 7px 14px; border-radius: 10px; transition: all 0.2s; font-weight: 500; }
.topbar a.back:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

/* Main Container */
.wrap { width: 100%; max-width: 1800px; margin: 24px auto; padding: 0 20px; }

/* Filters Section */
.filters {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius);
  padding: 18px 20px;
  display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap;
  margin-bottom: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -2px rgba(0, 0, 0, 0.02);
}
.fg { display: flex; flex-direction: column; gap: 6px; }
.fg label { font-size: 12px; font-weight: 600; color: var(--ink3); }
.fg input {
  height: 42px; padding: 0 14px; border: 1px solid var(--line); border-radius: 10px;
  font-family: inherit; font-size: 14px; outline: none; min-width: 200px; background: #fff;
  transition: all 0.2s; color: var(--ink);
}
.fg input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(var(--primary-rgb),.12); }
.fg input:disabled { background: var(--line-light); color: var(--ink4); cursor: not-allowed; }
.fg .all-dates { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 600; color: var(--ink2); cursor: pointer; user-select: none; }
.fg .all-dates input { min-width: 0; width: 16px; height: 16px; padding: 0; margin: 0; cursor: pointer; }

/* Modal ส่งใหม่ */
.redo-opt { display: flex; gap: 10px; align-items: flex-start; border: 1px solid var(--line); border-radius: 10px; padding: 12px 14px; margin-bottom: 10px; cursor: pointer; }
.redo-opt:hover { border-color: var(--primary); }
.redo-opt.active { border-color: var(--primary); background: var(--primary-light); }
.redo-opt input { margin-top: 3px; width: 16px; height: 16px; cursor: pointer; flex-shrink: 0; }
.redo-opt b { display: block; font-size: 14px; color: var(--ink); }
.redo-opt span { font-size: 12.5px; color: var(--ink3); }
.redo-fields { display: none; padding: 4px 2px 6px; }
.redo-fields.open { display: block; }
.redo-fields label { display: block; font-size: 12.5px; font-weight: 700; color: var(--ink3); margin: 10px 0 6px; }
.redo-fields input, .redo-fields select { width: 100%; height: 40px; padding: 0 12px; border: 1px solid var(--line-strong); border-radius: 8px; font-family: inherit; font-size: 14px; color: var(--ink); background: #fff; }
.redo-fields input:focus, .redo-fields select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(var(--primary-rgb),.12); }
/* เหตุผลส่งใหม่ */
.redo-reason { border: 1px solid var(--line); border-radius: 10px; padding: 10px 14px; margin-bottom: 12px; background: #fffaf5; }
.redo-reason-title { font-size: 13px; font-weight: 700; color: var(--ink); margin-bottom: 6px; }
.redo-reason-opts { display: flex; gap: 18px; margin-bottom: 8px; }
.reason-opt { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600; color: var(--ink2); cursor: pointer; }
.reason-opt input { width: 16px; height: 16px; cursor: pointer; }
#redoReasonText { width: 100%; height: 38px; padding: 0 12px; border: 1px solid var(--line-strong); border-radius: 8px; font-family: inherit; font-size: 14px; color: var(--ink); background: #fff; }
#redoReasonText:disabled { background: var(--line-light); cursor: not-allowed; }
#redoReasonText:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(var(--primary-rgb),.12); }

.btn {
  height: 42px; padding: 0 18px; border-radius: 10px; border: 1px solid var(--line-strong);
  background: #fff; font-family: inherit; font-size: 13.5px; font-weight: 600; color: var(--ink2);
  cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 6px;
}
.btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
.btn-primary { background: var(--primary-dark); border-color: var(--primary-dark); color: #fff; }
.btn-primary:hover { background: var(--primary-dark-hover); border-color: var(--primary-dark-hover); color: #fff; }
.hint { font-size: 12px; color: var(--ink4); margin-left: auto; align-self: center; font-weight: 500; }

.count-bar { font-size: 13.5px; color: var(--ink3); margin: 0 4px 14px; font-weight: 500; }
.count-bar b { color: var(--ink); font-weight: 700; }

/* Job Card */
.job {
  background: var(--card); border: 1px solid var(--line); border-radius: var(--radius);
  padding: 18px 20px; margin-bottom: 12px;
  display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;
  box-shadow: 0 2px 4px rgba(0,0,0,0.01);
  transition: all 0.2s ease;
}
.job:hover { border-color: #cbd5e1; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04); }
.job.done { opacity: 0.78; background: #fafafa; border-style: dashed; }
/* ทำสีเฉพาะป้ายประเภท (type label) ไม่ระบายทั้งกล่อง */
.job.type-company .job-type, .job.type-private .job-type { background: #d8e6ff; color: #1e40af; border-color: #9ec0ff; }
.job.type-doc .job-type { background: rgb(255, 247, 237); color: #b45309; border-color: #f3d3ac; }
.job-main { flex: 1 1 380px; min-width: 280px; }

.job-line1 { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 6px; }
.job-type { font-size: 12px; font-weight: 700; color: var(--ink3); padding: 3px 10px; border-radius: 6px; border: 1px solid var(--line); background: var(--line-light); }
.job-bill { font-weight: 700; font-size: 16px; color: var(--ink); }
.job-code { font-family: var(--font-mono); font-size: 12px; color: var(--primary); background: var(--primary-light); padding: 3px 9px; border-radius: 6px; font-weight: 600; }

.badge { font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 30px; letter-spacing: 0.3px; }
.badge.pending { background: var(--line-light); color: var(--ink3); border: 1px solid var(--line); }
.badge.ok { background: var(--green-l); color: var(--green-d); }
.badge.hold { background: var(--primary-light); color: var(--primary); }   /* ค้างบิล = สีฟ้า */
.badge.wrong { background: var(--red-l); color: var(--red-d); }

.job-cust { font-size: 14.5px; color: var(--ink2); font-weight: 600; margin-bottom: 10px; }
.job-meta { display: flex; flex-wrap: wrap; gap: 8px 18px; font-size: 12.5px; color: var(--ink3); }
.job-meta .mi b { color: var(--ink2); font-weight: 600; }
.job-note { margin-top: 8px; font-size: 12.5px; color: var(--red-d); background: var(--red-l); padding: 8px 12px; border-radius: 8px; border-left: 3px solid var(--red); font-weight: 500; }
.job-linked { margin-top: 8px; font-size: 12.5px; color: #b45309; background: #fff7ed; padding: 8px 12px; border-radius: 8px; border-left: 3px solid #f0b374; font-weight: 600; }

/* Actions */
.job-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; align-self: stretch; padding-left: 20px; border-left: 1px solid var(--line); }
.act {
  height: 38px; padding: 0 16px; border-radius: 9px; border: 1px solid var(--line-strong);
  background: #fff; font-family: inherit; font-size: 13px; font-weight: 700; cursor: pointer; color: var(--ink2);
  white-space: nowrap; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center;
}
.act:hover:not(:disabled) { background: var(--line-light); border-color: var(--ink3); }
.act:disabled { opacity: .5; cursor: not-allowed; }

/* ปุ่มสถานะเป็นปุ่มสีทึบ อ่านง่ายสำหรับหน้างานในสำนักงาน */
.act.ok, .act.hold, .act.redo, .act.wrong { color: #fff; border-color: transparent; box-shadow: 0 1px 2px rgba(0,0,0,0.06); }
.act.ok { background: var(--green); }
.act.ok:hover:not(:disabled) { background: var(--green-d); }
.act.hold { background: var(--amber); }
.act.hold:hover:not(:disabled) { background: var(--amber-d); }
.act.redo { background: var(--primary); }
.act.redo:hover:not(:disabled) { background: var(--primary-hover); }
.act.wrong { background: var(--red); }
.act.wrong:hover:not(:disabled) { background: var(--red-d); }
.job-confirmed { font-size: 12.5px; color: var(--green-d); font-weight: 600; align-self: center; background: var(--green-l); padding: 8px 14px; border-radius: 8px; line-height: 1.5; text-align: left; }
/* กล่องผลรับเข้า สีตามสถานะ */
.job-result { font-size: 12.5px; font-weight: 600; align-self: center; padding: 8px 14px; border-radius: 8px; line-height: 1.5; text-align: left; }
.job-result.ok { color: var(--green-d); background: var(--green-l); }
.job-result.hold { color: var(--primary); background: var(--primary-light); }      /* ค้างบิล = ฟ้า */
.job-result.wrong { color: var(--red-d); background: var(--red-l); }                 /* สินค้าผิด = แดง */
.job-redispatched { font-size: 12.5px; color: var(--amber-d); font-weight: 600; align-self: center; background: var(--amber-l); padding: 8px 14px; border-radius: 8px; line-height: 1.5; text-align: left; }

/* Wrong Box form toggle */
.wrong-box { flex: 1 1 100%; display: none; gap: 10px; margin-top: 12px; padding-top: 12px; border-top: 1px dashed var(--line); align-items: center; flex-wrap: wrap; }
.wrong-box.open { display: flex; animation: fadeIn 0.2s ease; }
.wrong-box input { flex: 1 1 240px; height: 38px; padding: 0 12px; border: 1px solid var(--line); border-radius: 8px; font-family: inherit; font-size: 13px; outline: none; }
.wrong-box input:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(var(--red-rgb), 0.12); }

@keyframes fadeIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }

.state { text-align: center; padding: 60px 16px; color: var(--ink4); font-size: 14px; font-weight: 500; }
.spinner { width: 18px; height: 18px; border: 2.5px solid var(--line); border-top-color: var(--primary); border-radius: 50%; display: inline-block; animation: spin .6s linear infinite; vertical-align: -3px; margin-right: 8px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Toast Notifications */
.toast-wrap { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; pointer-events: none; }
.toast {
  background: #fff; color: var(--ink); border: 1px solid var(--line); border-left: 4px solid var(--green);
  box-shadow: 0 8px 24px rgba(0,0,0,.15); padding: 14px 18px; border-radius: 12px;
  font-size: 13.5px; font-weight: 500; min-width: 260px; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  pointer-events: auto; transform: translateY(0); opacity: 1;
}
.toast.err { border-left-color: var(--red); }
.toast.hide { opacity: 0; transform: translateY(12px); }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 9998; padding: 16px; }
.modal-overlay.open { display: flex; animation: fadeIn 0.2s ease; }
.modal-box { background: #fff; border-radius: 16px; padding: 26px; width: 100%; max-width: 400px; box-shadow: 0 25px 50px -12px rgba(0,0,0,.25); border: 1px solid var(--line); }
.modal-title { font-weight: 700; font-size: 18px; color: var(--ink); }
.modal-sub { font-size: 13.5px; color: var(--ink3); margin: 6px 0 18px; line-height: 1.5; }
.modal-label { display: block; font-size: 12.5px; font-weight: 600; color: var(--ink3); margin-bottom: 6px; }
.modal-date { width: 100%; height: 44px; padding: 0 14px; border: 1px solid var(--line); border-radius: 10px; font-family: inherit; font-size: 15px; color: var(--ink); }
.modal-date:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(var(--primary-rgb),.12); }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 22px; }
.modal-actions .btn { height: 42px; }

/* ===== โหมดกะทัดรัด: ย่อ UI ให้เห็นงานได้มากขึ้นต่อจอ (เลื่อนน้อยลง) ===== */
.topbar { height: 48px; padding: 0 14px; }
.topbar .brand { font-size: 15px; gap: 8px; }
.topbar .brand .tag { font-size: 10px; padding: 2px 8px; }
.topbar a.back { padding: 4px 10px; font-size: 12px; border-radius: 8px; }
.topbar .user { padding: 3px 10px 3px 3px; font-size: 12px; }
.user-avatar { width: 22px; height: 22px; font-size: 11px; }
.wrap { margin: 8px auto; padding: 0 10px; }

.filters { padding: 10px 14px; gap: 8px 12px; margin-bottom: 8px; border-radius: 10px; }
.fg { gap: 3px; }
.fg label { font-size: 12px; }
.fg input { height: 36px; min-width: 160px; padding: 0 12px; font-size: 13.5px; border-radius: 8px; }
.fg .all-dates { font-size: 12px; gap: 5px; }
.fg .all-dates input { width: 15px; height: 15px; }
#fStatus { height: 36px !important; }
.btn { height: 36px; padding: 0 14px; font-size: 13px; border-radius: 8px; }
.hint { display: none; }
.count-bar { font-size: 13px; margin: 0 2px 8px; }

/* งาน 1 ใบ = 1 แถวตาราง (ขนาดเท่า td หน้าเก่า bills_billIn: ตัวอักษร 11pt/10pt, 2 บรรทัด, ช่องไฟชิด) */
#list { display: flex; flex-direction: column; gap: 0; border: 1px solid var(--line-strong); border-radius: 6px; overflow: hidden; background: var(--card); }
#list > .state { border: none; }
/* ขอบเฉพาะเส้นล่าง (กว้าง 0 ด้านอื่น) — กันกฎเดิม .job.done { border-style: dashed } ทำให้ขอบหนาสีเข้มโผล่รอบแถว */
.job { padding: 8px 12px; margin: 0; gap: 12px; border: 0 solid var(--line); border-bottom-width: 1px; border-radius: 0; box-shadow: none; align-items: center; flex-wrap: nowrap; }
.job:last-child { border-bottom-width: 0; }
.job:nth-child(even) { background: #f7f9fc; }                  /* สลับสีแถวแบบตาราง */
.job:hover { background: #eef3ff; border-color: var(--line); box-shadow: none; }
.job.done { opacity: .7; border-style: solid; border-color: var(--line); background: #fafafa; }
.job-main { flex: 1 1 auto; min-width: 0; line-height: 1.5; }
.job-line1 { gap: 8px; margin-bottom: 2px; flex-wrap: nowrap; min-width: 0; }
.job-type, .job-code { font-size: 12px; padding: 1px 7px; border-radius: 4px; white-space: nowrap; flex-shrink: 0; }
.job-bill { font-size: 15px; white-space: nowrap; flex-shrink: 0; }
.job-cust { font-size: 15px; font-weight: 500; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0; }
.job-cust b { color: var(--ink); }
.job-meta { gap: 0 16px; font-size: 13.5px; line-height: 1.5; }
.job-note, .job-linked { margin: 2px 0; padding: 1px 8px; font-size: 13px; border-radius: 4px; border-left-width: 2px; }
.job-actions { gap: 5px; padding-left: 12px; flex-wrap: nowrap; flex-shrink: 0; align-self: center; border-left: 1px solid var(--line); }
.act { height: 32px; padding: 0 11px; font-size: 13px; border-radius: 6px; }
.act.ok.main { min-width: 90px; height: 34px; font-size: 14px; }
.act.arm { background: #0f172a !important; color: #fff !important; border-color: #0f172a !important; }
.job-result, .job-redispatched { font-size: 13px; padding: 4px 10px; border-radius: 5px; line-height: 1.4; max-width: 440px; }
.wrong-box { margin-top: 4px; padding-top: 4px; gap: 5px; }
.wrong-box input { height: 30px; font-size: 13.5px; }
.job-chk { width: 16px !important; height: 16px !important; margin-right: 0 !important; }

@media(max-width: 640px) {
  .filters { padding: 8px; }
  .job { flex-wrap: wrap; }
  .job-actions { flex: 1 1 100%; flex-wrap: wrap; justify-content: flex-start; padding-left: 0; padding-top: 6px; border-left: none; border-top: 1px dashed var(--line); }
}
</style>
</head>
<body>

<div class="topbar">
  <div class="brand">รับเข้าบิล <span class="tag">BILL RECEIVE</span></div>
  <div class="right">
    <a class="back" href="{{ route('billreceive.monitor') }}" target="_blank" style="border-color:var(--primary);color:var(--primary);background:var(--primary-light);font-weight:700;">📋 Monitor บิลค้างรับเข้า</a>
    <a class="back" href="{{ route('oil') }}">← กลับหน้าน้ำมัน</a>
    <span class="user"><span class="user-avatar">{{ mb_strtoupper(mb_substr($loggedInName, 0, 1)) }}</span>{{ $loggedInName }}</span>
  </div>
</div>

<div class="wrap">
  <div class="filters">
    <div class="fg">
      <label class="all-dates" title="ติ๊กแล้วค้นหาทุกวัน ไม่สนวันที่"><input type="checkbox" id="fAllDates"> ไม่จำกัดวันที่</label>
      <label for="fDate">วันที่จ่ายงาน</label>
      <input type="date" id="fDate">
    </div>
    <div class="fg">
      <label for="fBill">ค้นหาเลขบิล</label>
      <input type="text" id="fBill"autocomplete="off">
    </div>
    <div class="fg">
      <label for="fCust">รหัสลูกค้า</label>
      <input type="text" id="fCust"autocomplete="off">
    </div>
    <div class="fg">
      <label for="fCustName">ชื่อลูกค้า</label>
      <input type="text" id="fCustName" autocomplete="off">
    </div>
    <div class="fg">
      <label for="fDriver">คนขับ</label>
      <input type="text" id="fDriver" placeholder="พิมพ์ชื่อคนขับ" autocomplete="off">
    </div>
    <div class="fg">
      <label for="fKind">ชนิดบิล</label>
      <select id="fKind" style="height:38px;padding:0 10px;border:1px solid var(--line);border-radius:8px;font-family:inherit;font-size:13px;">
        <option value="">ทั้งหมด</option>
        <option value="bill">บิลส่งของ</option>
        <option value="doc">บิลชั่วคราว</option>
      </select>
    </div>
    <div class="fg">
      <label for="fStatus">สถานะบิล</label>
      <select id="fStatus" style="height:38px;padding:0 10px;border:1px solid var(--line);border-radius:8px;font-family:inherit;font-size:13px;">
        <option value="">ทั้งหมด</option>
        <option value="pending">รอรับเข้า</option>
        <option value="ok">สำเร็จ</option>
        <option value="hold">ค้างบิล</option>
        <option value="wrong">สินค้าผิด</option>
      </select>
    </div>
    <div class="fg">
      <label for="fHeadcom">บริษัทผู้ส่ง <span style="color:var(--ink3);font-weight:500;">(บิลชั่วคราว)</span></label>
      <select id="fHeadcom" style="height:38px;padding:0 10px;border:1px solid var(--line);border-radius:8px;font-family:inherit;font-size:13px;min-width:220px;">
        <option value="">ทั้งหมด</option>
        <option value="บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด">บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด</option>
        <option value="บริษัท ทริปเปิ้ล อี อินโนเวชั่น จำกัด">บริษัท ทริปเปิ้ล อี อินโนเวชั่น จำกัด</option>
        <option value="บริษัท ทริบเปิ้ล พี แฟคตอรี่ แอนด์ เอ็นจิเนียริ่ง จำกัด">บริษัท ทริบเปิ้ล พี แฟคตอรี่ แอนด์ เอ็นจิเนียริ่ง จำกัด</option>
        <option value="บริษัท เอตะ แอนด์ พอล อินโนเวชั่น จำกัด">บริษัท เอตะ แอนด์ พอล อินโนเวชั่น จำกัด</option>
        <option value="บริษัท ฮิคาริ เดงกิ จำกัด">บริษัท ฮิคาริ เดงกิ จำกัด</option>
        <option value="บริษัท เอ อี แอนด์ ที อินเตอร์เนชั่นแนล จำกัด">บริษัท เอ อี แอนด์ ที อินเตอร์เนชั่นแนล จำกัด</option>
        <option value="บริษัท ทริปเปิ้ล อี ไลท์ติ้ง จำกัด">บริษัท ทริปเปิ้ล อี ไลท์ติ้ง จำกัด</option>
        <option value="บริษัท ทริปเปิ้ล อี เอ็มไพร์ กรุ๊ป จำกัด">บริษัท ทริปเปิ้ล อี เอ็มไพร์ กรุ๊ป จำกัด</option>
        <option value="บริษัท ชาเวสต์ เรียลเอสเตท จำกัด">บริษัท ชาเวสต์ เรียลเอสเตท จำกัด</option>
        <option value="บริษัท เทคเพียร์ เอ็นจิเนียริ่ง จำกัด">บริษัท เทคเพียร์ เอ็นจิเนียริ่ง จำกัด</option>
        <option value="บริษัท เดชา อิเล็คทริค แอนด์ คอนสตรัคชั่น จำกัด">บริษัท เดชา อิเล็คทริค แอนด์ คอนสตรัคชั่น จำกัด</option>
      </select>
    </div>
    <button type="button" class="btn btn-primary" id="btnSearch">ค้นหา</button>
    <button type="button" class="btn" id="btnClear">ล้าง</button>
    <span class="hint">เลือกวันที่ = ค้นเฉพาะงานที่จ่ายวันนั้น · ติ๊ก "ไม่จำกัดวันที่" = ค้นทุกวัน</span>
  </div>

  <div class="count-bar" id="countBar"></div>
  <div id="list"></div>
  <div id="pager" style="display:flex;justify-content:center;align-items:center;gap:8px;flex-wrap:wrap;margin:18px 0 6px;"></div>
</div>

<!-- แถบเลือกหลายรายการ (bulk) -->
<div id="bulkBar" style="display:none;position:fixed;left:50%;bottom:20px;transform:translateX(-50%);z-index:900;background:#0f172a;color:#fff;border-radius:30px;box-shadow:0 10px 25px -5px rgba(0,0,0,.3);padding:10px 18px;display:none;align-items:center;gap:12px;">
  <span>เลือก <b id="bulkCount">0</b> รายการ</span>
  <button type="button" class="act ok" onclick="bulkSetStatus('ok')">สำเร็จ</button>
  <button type="button" class="act redo" onclick="bulkRedo()">จัดส่งใหม่</button>
  <button type="button" class="act" style="background:rgba(255,255,255,.15);color:#fff;border:none;" onclick="clearBulk()">ล้างเลือก</button>
</div>


<!-- Modal เปลี่ยนคนขับ/ขนส่ง -->
<div id="changeModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center;padding:16px;">
  <div style="background:#fff;border-radius:14px;padding:22px;width:min(94vw,440px);box-shadow:0 20px 50px rgba(0,0,0,.3);">
    <div style="font-weight:700;font-size:17px;margin-bottom:4px;">เปลี่ยนคนขับ / ขนส่ง</div>
    <div id="changeBillLabel" style="color:#64748b;font-size:12.5px;margin-bottom:16px;line-height:1.5;"></div>
    <label style="display:block;font-size:12.5px;font-weight:700;color:#64748b;margin-bottom:6px;">คนขับ (ผู้รับผิดชอบ)</label>
    <input type="text" id="changeDriver" list="changeDriverList" autocomplete="off" placeholder="เลือกหรือพิมพ์ชื่อ (เว้นว่าง = คงเดิม)" style="width:100%;padding:10px 12px;border:1px solid #dee2e6;border-radius:8px;font-family:inherit;font-size:14px;margin-bottom:6px;">
    <datalist id="changeDriverList"></datalist>
    <div id="changeDriverHint" style="display:none;font-size:11.5px;color:#0ea5e9;margin-bottom:12px;">* เลือก "เซลล์ไปส่งเอง" พิมพ์ชื่อเซลล์ได้อิสระ</div>
    <label style="display:block;font-size:12.5px;font-weight:700;color:#64748b;margin-bottom:6px;">ขนส่ง (วิธีการจัดส่ง)</label>
    <select id="changeTransport" style="width:100%;padding:10px 12px;border:1px solid #dee2e6;border-radius:8px;font-family:inherit;font-size:14px;margin-bottom:8px;"></select>
    <div style="font-size:12px;color:#94a3b8;margin-bottom:16px;">* ยืนยันแล้วจะบันทึกงานนี้เป็น "จัดส่งสำเร็จ" พร้อมจดว่าเปลี่ยนคนขับ/ขนส่งจากใครเป็นใคร</div>
    <div style="display:flex;gap:10px;justify-content:flex-end;">
      <button type="button" class="act" onclick="closeChangeDriver()">ยกเลิก</button>
      <button type="button" class="act ok" id="changeConfirmBtn" onclick="confirmChangeDriver()">ยืนยัน (บันทึกสำเร็จ)</button>
    </div>
  </div>
</div>

<!-- Modal ส่งใหม่: จ่ายใหม่ที่นี่เลย หรือ คืนไปเลือกใหม่ที่หน้าจ่ายงานขนส่ง -->
<div class="modal-overlay" id="redoModal">
  <div class="modal-box" style="max-width:460px;">
    <div class="modal-title">ส่งใหม่</div>
    <div class="modal-sub" id="redoLabel"></div>

    <!-- เหตุผลที่ส่งใหม่ (บังคับเลือก): ไปไม่ทัน = จบ / อื่นๆ = ต้องพิมพ์ระบุ -->
    <div class="redo-reason">
      <div class="redo-reason-title">เหตุผลที่ส่งใหม่ <span style="color:var(--red)">*</span></div>
      <div class="redo-reason-opts">
        <label class="reason-opt"><input type="radio" name="redoReason" value="ไปไม่ทัน"> ไปไม่ทัน</label>
        <label class="reason-opt"><input type="radio" name="redoReason" value="other"> อื่นๆ (ระบุ)</label>
      </div>
      <input type="text" id="redoReasonText" placeholder="ระบุเหตุผล..." maxlength="400" disabled>
    </div>

    <label class="redo-opt active" id="redoOptAssign">
      <input type="radio" name="redoMode" value="assign" checked>
      <div><b>เลือกเองเลย</b><span>กำหนดผู้รับผิดชอบ วิธีการจัดส่ง และวันที่ไปส่งที่นี่</span></div>
    </label>
    <div class="redo-fields open" id="redoFields">
      <label for="redoDriver">ผู้รับผิดชอบ</label>
      <input type="text" id="redoDriver" list="redoDriverList" placeholder="เลือกหรือพิมพ์ชื่อ (เว้นว่างได้)" autocomplete="off">
      <datalist id="redoDriverList"></datalist>
      <label for="redoTransport">วิธีการจัดส่ง</label>
      <select id="redoTransport"></select>
      <label for="redoDate">วันที่ไปส่ง</label>
      <input type="date" id="redoDate">
    </div>

    <label class="redo-opt" id="redoOptReturn">
      <input type="radio" name="redoMode" value="return">
      <div><b>กลับไปเลือกใหม่ที่หน้าจ่ายงานขนส่ง</b><span>คืนงานไปหน้าจ่ายงาน แล้วค่อยเลือกคนขับ/วันที่ที่นั่น</span></div>
    </label>

    <div class="modal-actions">
      <button type="button" class="btn" onclick="closeRedo()">ยกเลิก</button>
      <button type="button" class="btn btn-primary" id="redoConfirmBtn" onclick="confirmRedo()">ยืนยันส่งใหม่</button>
    </div>
  </div>
</div>

<div class="toast-wrap" id="toastWrap"></div>

<script>
const DATA_URL    = "{{ route('billreceive.data') }}";
const CONFIRM_URL = "{{ route('billreceive.confirm') }}";
const CHANGE_URL  = "{{ route('billreceive.changeDriver') }}";
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const CAN_EDIT = {{ ($canEdit ?? false) ? 'true' : 'false' }};   // admin/store/accounting = รับเข้า/เปลี่ยนคนขับได้
const DELIVERY_METHODS    = @json($deliveryMethods ?? []);
const RESPONSIBLE_PERSONS = @json($responsiblePersons ?? []);

const fDate = document.getElementById('fDate');
const fBill = document.getElementById('fBill');
const fAllDates = document.getElementById('fAllDates');
const listEl = document.getElementById('list');
const countBar = document.getElementById('countBar');
let busy = false;       // กันกดซ้ำระหว่างบันทึก

// ปุ่ม "สำเร็จ": คลิกครั้งแรก = "กดอีกครั้ง ✓" ที่ตำแหน่งเดิม, คลิกซ้ำที่เดิม = บันทึก
//   (แทน popup confirm ที่เด้งกลางจอ ต้องเลื่อนเมาส์ไปกด)
function armClick(btn, i, action){
  if(btn.dataset.armed === '1'){ clearTimeout(btn._t); disarm(btn); doAction(i, action); return; }
  document.querySelectorAll('.act.arm').forEach(disarm);
  btn.dataset.armed = '1'; btn.dataset.label = btn.textContent; btn.textContent = 'กดอีกครั้ง ✓';
  btn.classList.add('arm');
  btn._t = setTimeout(()=>disarm(btn), 3000);
}
function disarm(b){ b.dataset.armed = ''; if(b.dataset.label) b.textContent = b.dataset.label; b.classList.remove('arm'); }

function esc(s){ return (s==null?'':String(s)).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

function toast(msg, err){
  const w = document.getElementById('toastWrap');
  const t = document.createElement('div');
  t.className = 'toast' + (err?' err':'');
  t.textContent = msg;
  w.appendChild(t);
  setTimeout(()=>{ t.classList.add('hide'); setTimeout(()=>t.remove(),300); }, 3200);
}

function statusInfo(st){
  const s = (st||'').trim();
  if(s === 'จัดส่งสำเร็จ') return {cls:'ok', txt:'สำเร็จ'};
  if(s === 'ค้างบิล')     return {cls:'hold', txt:'ค้างบิล'};
  if(s === 'สินค้าผิด')    return {cls:'wrong', txt:'สินค้าผิด'};
  if(s === 'ส่งใหม่วันพรุ่งนี้') return {cls:'hold', txt:'ส่งใหม่'};
  if(s === 'ส่งใหม่')            return {cls:'wrong', txt:'ส่งใหม่'};
  return {cls:'pending', txt:'รอส่ง'};
}

// "รับเข้าแล้ว" = สถานะเป็นผลจริง (สำเร็จ/ค้างบิล/สินค้าผิด) เท่านั้น — ไม่ดูแค่ check_time
function isReceived(r){ return ['จัดส่งสำเร็จ','ค้างบิล','สินค้าผิด'].includes(((r&&r.status)||'').trim()); }

let currentRows = [];
let currentPage = 1;
let pageMeta = { page:1, last_page:1, total:0 };

// ค้นหา/กรองใหม่ = กลับหน้า 1 เสมอ
function doSearch(){ currentPage = 1; loadData(); }
// เปลี่ยนหน้า (pagination)
function goPage(p){
  const lp = pageMeta.last_page || 1;
  currentPage = Math.max(1, Math.min(p, lp));
  loadData();
  window.scrollTo({ top:0, behavior:'smooth' });
}
function renderPager(){
  const pager = document.getElementById('pager');
  if(!pager) return;
  const lp = pageMeta.last_page || 1, pg = pageMeta.page || 1, total = pageMeta.total || 0;
  if(lp <= 1){ pager.innerHTML = total ? `<span style="color:var(--ink3);font-size:13px;">ทั้งหมด ${total} บิล</span>` : ''; return; }
  const btn = (label, target, disabled, active) =>
    `<button type="button" onclick="goPage(${target})" ${disabled?'disabled':''} class="btn${active?' btn-primary':''}" style="min-width:38px;height:34px;padding:0 10px;${disabled?'opacity:.45;cursor:not-allowed;':''}">${label}</button>`;
  let html = btn('‹ ก่อนหน้า', pg-1, pg<=1, false);
  // เลขหน้ารอบๆ หน้าปัจจุบัน
  const from = Math.max(1, pg-2), to = Math.min(lp, pg+2);
  if(from > 1) html += btn('1', 1, false, pg===1) + (from>2?'<span style="color:var(--ink3);">…</span>':'');
  for(let i=from;i<=to;i++) html += btn(String(i), i, false, i===pg);
  if(to < lp) html += (to<lp-1?'<span style="color:var(--ink3);">…</span>':'') + btn(String(lp), lp, false, pg===lp);
  html += btn('ถัดไป ›', pg+1, pg>=lp, false);
  html += `<span style="color:var(--ink3);font-size:13px;margin-left:8px;">หน้า ${pg}/${lp} · ${total} บิล</span>`;
  pager.innerHTML = html;
}
let selectedBulk = new Set();   // เก็บ index (ของ currentRows) ที่ติ๊กเลือกไว้

// map สถานะจริง -> key สำหรับ filter
function statusKey(r){
  const s = ((r&&r.status)||'').trim();
  if(s === 'จัดส่งสำเร็จ') return 'ok';
  if(s === 'ค้างบิล')     return 'hold';
  if(s === 'สินค้าผิด')    return 'wrong';
  return 'pending';   // รอส่ง / ส่งใหม่
}

// กรอง client-side: รหัสลูกค้า / ชื่อลูกค้า / สถานะ — คืน [{r, i}] (i = index จริงใน currentRows)
function getFilteredRows(){
  const cust  = (document.getElementById('fCust').value||'').trim().toLowerCase();
  const cname = (document.getElementById('fCustName').value||'').trim().toLowerCase();
  const drv   = (document.getElementById('fDriver').value||'').trim().toLowerCase();
  const st    = document.getElementById('fStatus').value;
  return currentRows.map((r,i)=>({r,i})).filter(({r})=>{
    if(cust  && !((r.customer_code||'').toLowerCase().includes(cust)))  return false;
    if(cname && !((r.customer_name||'').toLowerCase().includes(cname))) return false;
    if(drv   && !((r.driver_name||'').toLowerCase().includes(drv)))     return false;
    if(st    && statusKey(r) !== st) return false;
    return true;
  });
}

function toggleBulk(i, checked){ if(checked) selectedBulk.add(i); else selectedBulk.delete(i); updateBulkBar(); }
function clearBulk(){ selectedBulk.clear(); render(); }
function updateBulkBar(){
  const bar = document.getElementById('bulkBar');
  document.getElementById('bulkCount').textContent = selectedBulk.size;
  bar.style.display = selectedBulk.size ? 'flex' : 'none';
}
async function bulkSetStatus(action){
  const idxs = Array.from(selectedBulk);
  if(!idxs.length) return;
  const label = action==='ok' ? 'สำเร็จ' : 'ค้างบิล';
  let note = '';
  if(action==='hold'){
    note = (prompt(`หมายเหตุค้างบิล (ใช้กับ ${idxs.length} รายการที่เลือก):`, '') || '').trim();
    if(!note){ toast('กรุณากรอกหมายเหตุค้างบิล', true); return; }
  }
  if(!confirm(`ยืนยันตั้งสถานะ "${label}" ให้ ${idxs.length} รายการที่เลือก?`)) return;
  let okN=0, failN=0;
  for(const i of idxs){
    const r = currentRows[i];
    if(!r) continue;
    try{
      const data = await postConfirm({ job_key:r.job_key, action, note, tx_ids:r.tx_ids });
      r.status = data.status; r.check_name = data.check_name; r.check_time = data.check_time;
      if(note) r.note = note;
      okN++;
    }catch(e){ failN++; }
  }
  selectedBulk.clear();
  toast(`ตั้งสถานะสำเร็จ ${okN} รายการ${failN?` · ล้มเหลว ${failN}`:''}`, failN>0);
  render();
}

// bulk จัดส่งใหม่ = เปิด modal เลือกวิธี (จ่ายใหม่ที่นี่ / คืนไปหน้าจ่ายงาน) ใช้กับทุกรายการที่เลือก
function bulkRedo(){
  const idxs = Array.from(selectedBulk);
  if(!idxs.length) return;
  openRedo(idxs);
}

async function loadData(){
  const q      = fBill.value.trim();
  const cust   = document.getElementById('fCust').value.trim();
  const cname  = document.getElementById('fCustName').value.trim();
  const driver = document.getElementById('fDriver').value.trim();
  const status = document.getElementById('fStatus').value;
  const headcom = document.getElementById('fHeadcom').value;
  const kind = document.getElementById('fKind').value;
  const params = new URLSearchParams();
  if(q) params.set('q', q);
  if(cust) params.set('cust', cust);
  if(cname) params.set('cname', cname);
  if(driver) params.set('driver', driver);
  if(status) params.set('status', status);
  if(headcom) params.set('headcom', headcom);   // บริษัทผู้ส่ง (เฉพาะบิลชั่วคราว)
  if(kind) params.set('kind', kind);            // ชนิดบิล: บิลส่งของ/บิลชั่วคราว
  // เลือกวันที่ = ค้น/กรองเฉพาะวันนั้น ; ติ๊กไม่จำกัดวันที่ = ค้นทุกวัน
  params.set('date', fAllDates.checked ? 'all' : (fDate.value || ''));
  params.set('page', currentPage);   // แบ่งหน้า (หน้าละ 100)
  listEl.innerHTML = '<div class="state"><span class="spinner"></span>กำลังโหลดข้อมูล...</div>';
  countBar.textContent = '';
  document.getElementById('pager').innerHTML = '';
  try{
    const res = await fetch(`${DATA_URL}?${params.toString()}`, {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
    const data = await res.json();
    if(!res.ok || !data.ok){ throw new Error(data.message || 'โหลดข้อมูลไม่สำเร็จ'); }
    currentRows = data.rows || [];
    pageMeta = { page:data.page||1, last_page:data.last_page||1, total:data.total||0, stats:data.stats||null };
    currentPage = pageMeta.page;
    selectedBulk.clear();
    render();
    renderPager();
  }catch(e){
    listEl.innerHTML = `<div class="state">เกิดข้อผิดพลาด: ${esc(e.message)}</div>`;
  }
}

function render(){
  const rows = getFilteredRows();
  if(!rows.length){
    listEl.innerHTML = '<div class="state">ไม่พบรายการตามเงื่อนไข</div>';
    countBar.textContent = '';
    updateBulkBar();
    return;
  }
  const doneN = rows.filter(({r})=>isReceived(r)).length;
  const histN = rows.filter(({r})=>!!r.cancelled).length;   // แถวประวัติ (ไม่นับเป็นคงเหลือ)
  const pendN = rows.length - doneN - histN;
  // ยอดรวม "ทุกหน้า" จาก backend (ถ้ามี) — ไม่งั้น fallback เป็นเฉพาะหน้านี้
  const st = pageMeta.stats;
  if(st){
    const parts = [`รับเข้าแล้ว <b>${st.done}</b>`, `ยังไม่รับเข้า <b style="color:#c0392b;">${st.pending}</b>`];
    if(st.history) parts.push(`ประวัติ <b>${st.history}</b>`);
    let bar = `ทั้งหมด <b>${st.all}</b> บิล · ` + parts.join(' · ');
    if((pageMeta.last_page||1) > 1) bar += ` <span style="color:var(--ink3);">(หน้านี้แสดง ${rows.length})</span>`;
    countBar.innerHTML = bar;
  } else {
    countBar.innerHTML = `แสดง <b>${rows.length}</b> บิล · รับเข้าแล้ว <b>${doneN}</b> · คงเหลือ <b>${pendN<0?0:pendN}</b>`
      + (histN?` · ประวัติ <b>${histN}</b>`:'');
  }

  listEl.innerHTML = rows.map(({r,i})=>{
    const si = statusInfo(r.status);
    const typeLabel = r.type==='doc' ? 'ชั่วคราว' : (r.type==='private' ? 'เอกชน' : 'บริษัท');
    const typeTitle = r.type==='doc' ? 'บิลชั่วคราว' : (r.type==='private' ? 'บิล · ขนส่งเอกชน' : 'บิล · ส่งโดยบริษัท');
    // บรรทัด 2: คนขับ · ขนส่ง · วันส่ง · จ่ายโดย/เมื่อ (ข้อความสั้น)
    const meta = [];
    if(r.driver_name)   meta.push(`<span class="mi"><b>${esc(r.driver_name)}</b></span>`);
    if(r.transport_name)meta.push(`<span class="mi">${esc(r.transport_name)}</span>`);
    if(r.delivery_date) meta.push(`<span class="mi">ส่ง ${esc(r.delivery_date)}</span>`);
    meta.push(`<span class="mi">จ่าย ${esc(r.name_pick||'-')}${r.time_pick?' '+esc(r.time_pick):''}</span>`);

    const received = isReceived(r);
    const isHistory = !!r.cancelled;                          // แถวประวัติ (รอบเก่าที่ถูกแทนที่/ยกเลิก) -> อ่านอย่างเดียว
    const redispatched = !received && !isHistory && !!r.redispatched_to;   // ถูกจ่ายใหม่ไปวันหลังแล้ว
    let actions;
    if(isHistory){
      // ประวัติรอบเก่า: เช่น สินค้าผิด/ส่งใหม่ -> ถูกจ่ายใหม่แล้ว = แสดงผลลัพธ์รอบนั้น ไม่มีปุ่ม/ติ๊ก
      const hi = statusInfo(r.status);
      actions = `<div class="job-redispatched" title="${r.cancelled_at?'ถูกแทนที่/จ่ายใหม่เมื่อ '+esc(r.cancelled_at)+(r.cancelled_by?' โดย '+esc(r.cancelled_by):''):''}">↻ ประวัติ: ${esc(hi.txt)}`
        + `${r.check_name?' · '+esc(r.check_name):''}${r.check_time?' · '+esc(r.check_time):''}</div>`;
    } else if(received){
      // รับเข้าแล้ว -> แสดงผลตามสถานะ + ให้กลับมากด "สำเร็จ" ได้ (เช่น ค้างบิล/สินค้าผิด -> เปลี่ยนเป็นสำเร็จภายหลัง)
      const noteLine = (r.note && (si.cls==='wrong' || si.cls==='hold')) ? ` · ${esc(r.note)}` : '';
      // "เปลี่ยนเป็นสำเร็จ" แสดงเฉพาะงานที่ค้างบิลเท่านั้น
      const canReSuccess = (((r.status||'').trim()) === 'ค้างบิล');
      actions = `<div class="job-result ${si.cls}">✓ ${esc(si.txt)} · ${esc(r.check_name||'-')}${r.check_time?' · '+esc(r.check_time):''}${noteLine}</div>`
        + ((CAN_EDIT && canReSuccess) ? `<button type="button" class="act ok" onclick="armClick(this,${i},'ok')">เปลี่ยนเป็นสำเร็จ</button>` : '');
    } else if(redispatched){
      // งานต้นทางที่ถูกจ่ายใหม่ไปวันอื่นแล้ว -> ไม่มีปุ่ม แสดงว่าย้ายไปวันไหน
      actions = `<div class="job-redispatched">↻ จ่ายใหม่ไปวันที่ ${esc(r.redispatched_to)}</div>`;
    } else if(CAN_EDIT){
      actions = `<button type="button" class="act ok main" onclick="armClick(this,${i},'ok')" title="คลิก 2 ครั้งที่เดิม = บันทึกสำเร็จ">✓ สำเร็จ</button>
         <button type="button" class="act hold"  onclick="openNote(${i},'hold')">ค้างบิล</button>
         <button type="button" class="act redo"  onclick="doRedo(${i})" title="ส่งใหม่ (จ่ายงานใหม่)">ส่งใหม่</button>
         <button type="button" class="act wrong" onclick="openNote(${i},'wrong')">สินค้าผิด</button>
         <button type="button" class="act" style="border-color:#2853d5;color:#2853d5;" onclick="openChangeDriver(${i})" title="เปลี่ยนคนขับ/ขนส่ง">เปลี่ยนคนขับ</button>`;
    } else {
      // viewer (sale/support/sale_assistant) — ดูอย่างเดียว
      actions = `<div class="job-result pending" style="color:#6b7280;background:#f1f5f9;">รอรับเข้า</div>`;
    }

    // เช็คบ็อกซ์ (bulk) เฉพาะ editor + งานที่ยังไม่รับเข้า ; สำเร็จ/ค้างบิล/สินค้าผิด/ถูกจ่ายใหม่ = ติ๊กไม่ได้
    const bulkable = CAN_EDIT && !redispatched && !received && !isHistory;
    const chk = bulkable
      ? `<input type="checkbox" class="job-chk" ${selectedBulk.has(i)?'checked':''} onchange="toggleBulk(${i},this.checked)" title="เลือกเพื่อตั้งสถานะพร้อมกัน" style="width:16px;height:16px;align-self:center;cursor:pointer;flex-shrink:0;">`
      : '';
    return `<div class="job type-${r.type} ${received||redispatched||isHistory?'done':''}" id="job-${i}">
      ${chk}
      <div class="job-main">
        <div class="job-line1">
          <span class="job-type" title="${esc(typeTitle)}">${esc(typeLabel)}</span>
          <span class="job-bill">${esc(r.bill_no||'-')}</span>
          ${r.customer_code?`<span class="job-code">${esc(r.customer_code)}</span>`:''}
          <span class="job-cust" title="${esc((r.so_id?'SO '+r.so_id+' · ':'')+(r.customer_name||''))}">${r.so_id?`<b>SO ${esc(r.so_id)}</b> · `:''}${esc(r.customer_name||'-')}</span>
        </div>
        <div class="job-meta">${meta.join('')}</div>
        ${(r.linked_bills && r.linked_bills.length)?`<div class="job-linked">เชื่อมกัน ${r.linked_bills.length} บิลค้าง: ${r.linked_bills.map(esc).join(', ')}</div>`:''}
        ${((!received || isHistory) && r.note)?`<div class="job-note">หมายเหตุ: ${esc(r.note)}</div>`:''}
        <div class="wrong-box" id="wrong-${i}" data-action="wrong">
          <input type="text" id="wrongnote-${i}" placeholder="ระบุหมายเหตุ..."
                 onkeydown="noteKey(event,${i})">
          <button type="button" class="act wrong" id="notesave-${i}" onclick="submitNote(${i})">บันทึก</button>
          <button type="button" class="act" onclick="closeNote(${i})">ยกเลิก</button>
        </div>
      </div>
      <div class="job-actions">${actions}</div>
    </div>`;
  }).join('');
}

// เปิดกล่องหมายเหตุ (ใช้ได้ทั้ง ค้างบิล และ สินค้าผิด) — ต้องกรอกหมายเหตุก่อนบันทึก
function openNote(i, action){
  const box  = document.getElementById('wrong-'+i);
  const inp  = document.getElementById('wrongnote-'+i);
  const save = document.getElementById('notesave-'+i);
  if(!box) return;
  box.dataset.action = action;
  const label = action==='hold' ? 'ค้างบิล' : 'สินค้าผิด';
  if(inp)  inp.placeholder = action==='hold' ? 'ระบุหมายเหตุค้างบิล...' : 'ระบุรายละเอียดสินค้าผิด...';
  if(save){ save.textContent = 'บันทึก'+label; save.className = 'act ' + (action==='hold' ? 'hold' : 'wrong'); }
  box.classList.add('open');
  inp?.focus();
}
function closeNote(i){ document.getElementById('wrong-'+i)?.classList.remove('open'); }
// ช่องหมายเหตุ: Enter = บันทึก, Esc = ยกเลิก
function noteKey(e, i){
  if(e.key === 'Enter'){ e.preventDefault(); submitNote(i); }
  else if(e.key === 'Escape'){ e.preventDefault(); closeNote(i); }
}

async function postConfirm(payload){
  const res = await fetch(CONFIRM_URL, {
    method:'POST',
    headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'},
    body: JSON.stringify(payload),
  });
  const data = await res.json().catch(()=>null);
  if(!res.ok || !data || !data.ok) throw new Error((data && data.message) || 'บันทึกไม่สำเร็จ');
  return data;
}

// บันทึกสถานะ (ไม่มี popup confirm แล้ว — การยืนยันคือ Enter บนการ์ดที่เลือก / คลิกซ้ำที่ปุ่มเดิม)
async function doAction(i, action){
  const r = currentRows[i];
  if(!r || busy) return;
  const labels = {ok:'สำเร็จ', hold:'ค้างบิล'};
  busy = true;
  try{
    const data = await postConfirm({ job_key:r.job_key, action, tx_ids:r.tx_ids });
    toast(`✓ บิล ${r.bill_no} : ${labels[action] || data.status}`);
    r.status = data.status; r.check_name = data.check_name; r.check_time = data.check_time;
    render();
  }catch(e){ toast('ผิดพลาด: '+e.message, true); }
  finally{ busy = false; }
}

async function submitNote(i){
  const r = currentRows[i];
  if(!r || busy) return;
  const box = document.getElementById('wrong-'+i);
  const action = (box && box.dataset.action) || 'wrong';
  const label = action==='hold' ? 'ค้างบิล' : 'สินค้าผิด';
  const note = (document.getElementById('wrongnote-'+i)?.value || '').trim();
  if(!note){ toast('กรุณากรอกหมายเหตุ'+label, true); document.getElementById('wrongnote-'+i)?.focus(); return; }
  busy = true;
  try{
    const data = await postConfirm({ job_key:r.job_key, action, note, tx_ids:r.tx_ids });
    toast(`✓ บิล ${r.bill_no} : ${label}`);
    r.status = data.status; r.check_name = data.check_name; r.check_time = data.check_time; r.note = note;
    render();
  }catch(e){ toast('ผิดพลาด: '+e.message, true); }
  finally{ busy = false; }
}

/* ===== ส่งใหม่ =====
   เลือกได้ 2 แบบ:
   - assign: เลือกผู้รับผิดชอบ / วิธีการจัดส่ง / วันที่ไปส่ง ที่นี่เลย -> จ่ายงานใหม่ทันที
   - return: คืนงานไปหน้าจ่ายงานขนส่ง (deliverytrack) แล้วค่อยเลือกคนขับ/วันใหม่ที่นั่น */
let redoIdxs = [];
function redoMode(){ return (document.querySelector('input[name="redoMode"]:checked')||{}).value || 'assign'; }
function syncRedoMode(){
  const m = redoMode();
  document.getElementById('redoOptAssign').classList.toggle('active', m==='assign');
  document.getElementById('redoOptReturn').classList.toggle('active', m==='return');
  document.getElementById('redoFields').classList.toggle('open', m==='assign');
}
document.querySelectorAll('input[name="redoMode"]').forEach(r => r.addEventListener('change', syncRedoMode));

// เหตุผลส่งใหม่: "ไปไม่ทัน" = จบ ไม่ต้องพิมพ์ / "อื่นๆ" = เปิดช่องให้พิมพ์ (บังคับกรอก)
function redoReasonChoice(){ return (document.querySelector('input[name="redoReason"]:checked')||{}).value || ''; }
function syncRedoReason(){
  const txt = document.getElementById('redoReasonText');
  const isOther = redoReasonChoice() === 'other';
  txt.disabled = !isOther;
  if(isOther) txt.focus(); else txt.value = '';
}
document.querySelectorAll('input[name="redoReason"]').forEach(r => r.addEventListener('change', syncRedoReason));

function tomorrowISO(){
  const d = new Date(); d.setDate(d.getDate()+1);
  return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
}
function openRedo(idxs){
  if(!CAN_EDIT) return;
  redoIdxs = idxs.filter(i => currentRows[i]);
  if(!redoIdxs.length) return;
  const first = currentRows[redoIdxs[0]];
  document.getElementById('redoLabel').innerHTML = redoIdxs.length === 1
    ? `บิล <b>${esc(first.bill_no||'-')}</b> · คนขับเดิม <b>${esc(first.driver_name||'-')}</b> · ขนส่งเดิม <b>${esc(first.transport_name||'-')}</b>`
    : `ใช้กับ <b>${redoIdxs.length}</b> รายการที่เลือก`;
  // ค่าเริ่มต้น = คนขับ/ขนส่งเดิม, วันที่ = พรุ่งนี้
  document.getElementById('redoDriverList').innerHTML = RESPONSIBLE_PERSONS.map(o => `<option value="${esc(o)}">`).join('');
  document.getElementById('redoDriver').value = redoIdxs.length === 1 ? (first.driver_name||'') : '';
  const cur = redoIdxs.length === 1 ? (first.transport_name||'').trim() : '';
  const methods = DELIVERY_METHODS.slice();
  if(cur && methods.indexOf(cur) === -1) methods.unshift(cur);
  document.getElementById('redoTransport').innerHTML = '<option value="">— เลือกวิธีการจัดส่ง —</option>'
    + methods.map(o => `<option value="${esc(o)}" ${o===cur?'selected':''}>${esc(o)}</option>`).join('');
  document.getElementById('redoDate').value = tomorrowISO();
  document.querySelector('input[name="redoMode"][value="assign"]').checked = true;
  syncRedoMode();
  // ล้างเหตุผลทุกครั้งที่เปิด -> ต้องเลือกใหม่
  document.querySelectorAll('input[name="redoReason"]').forEach(r => { r.checked = false; });
  syncRedoReason();
  document.getElementById('redoModal').classList.add('open');
}
function closeRedo(){ document.getElementById('redoModal').classList.remove('open'); redoIdxs = []; }
document.getElementById('redoModal').addEventListener('click', function(e){ if(e.target===this) closeRedo(); });

// ปุ่ม "ส่งใหม่" ของแต่ละบิล
function doRedo(i){ openRedo([i]); }

async function confirmRedo(){
  if(!redoIdxs.length) return;
  const mode = redoMode();
  // เหตุผลส่งใหม่ (บังคับ)
  const choice = redoReasonChoice();
  if(!choice){ toast('กรุณาเลือกเหตุผลที่ส่งใหม่', true); return; }
  let reason = choice;
  if(choice === 'other'){
    reason = document.getElementById('redoReasonText').value.trim();
    if(!reason){ toast('กรุณาระบุเหตุผลที่ส่งใหม่', true); document.getElementById('redoReasonText').focus(); return; }
  }
  const payloadExtra = { redo_mode: mode, redo_reason: reason };
  if(mode === 'assign'){
    const driver    = document.getElementById('redoDriver').value.trim();
    const transport = document.getElementById('redoTransport').value.trim();
    const date      = document.getElementById('redoDate').value;
    if(!transport){ toast('กรุณาเลือกวิธีการจัดส่ง', true); return; }
    if(!date){ toast('กรุณาเลือกวันที่ไปส่ง', true); return; }
    if(transport === 'เซลล์ไปส่งเอง' && !driver){ toast('เลือก "เซลล์ไปส่งเอง" กรุณาระบุชื่อเซลล์ที่ไปส่งเอง', true); return; }
    if(transport !== 'เซลล์ไปส่งเอง' && driver && RESPONSIBLE_PERSONS.indexOf(driver) === -1){
      toast('กรุณาเลือกผู้รับผิดชอบจากรายการที่มีให้', true); return;
    }
    Object.assign(payloadExtra, { redo_driver: driver, redo_transport: transport, redo_date: date });
  }
  const btn = document.getElementById('redoConfirmBtn'); btn.disabled = true;
  let okN = 0, failN = 0, lastMsg = '', lastErr = '';
  for(const i of redoIdxs){
    const r = currentRows[i];
    try{
      const data = await postConfirm(Object.assign({ job_key:r.job_key, action:'redo', tx_ids:r.tx_ids }, payloadExtra));
      okN++; lastMsg = data.message || '';
    }catch(e){ failN++; lastErr = e.message; }
  }
  btn.disabled = false;
  const n = redoIdxs.length;
  closeRedo();
  selectedBulk.clear();
  if(n === 1) toast(failN ? ('ผิดพลาด: ' + lastErr) : (lastMsg || 'ส่งใหม่แล้ว'), failN > 0);
  else toast(`ส่งใหม่ ${okN} รายการ${failN?` · ล้มเหลว ${failN}`:''}`, failN > 0);
  loadData();
}

/* ===== เปลี่ยนคนขับ/ขนส่ง ===== */
let changeIdx = null;
function fillChangeSelect(id, options, current){
  const sel = document.getElementById(id);
  const cur = (current||'').trim();
  let html = '<option value="">— ไม่เปลี่ยน (คงเดิม) —</option>';
  const list = options.slice();
  // ถ้าค่าปัจจุบันไม่มีใน list -> ใส่ไว้ให้เลือกได้ (จะได้เห็นค่าเดิม)
  if(cur && list.indexOf(cur) === -1) list.unshift(cur);
  html += list.map(o => `<option value="${esc(o)}" ${o===cur?'selected':''}>${esc(o)}</option>`).join('');
  sel.innerHTML = html;
}
function openChangeDriver(i){
  if(!CAN_EDIT) return;
  const r = currentRows[i];
  if(!r) return;
  changeIdx = i;
  document.getElementById('changeBillLabel').innerHTML =
    `บิล <b>${esc(r.bill_no||'-')}</b><br>คนขับเดิม: <b>${esc(r.driver_name||'-')}</b> · ขนส่งเดิม: <b>${esc(r.transport_name||'-')}</b>`;
  // คนขับ = input พิมพ์ได้ (มี datalist แนะนำ) ; เลือก "เซลล์ไปส่งเอง" พิมพ์ชื่อเซลล์เองได้
  document.getElementById('changeDriverList').innerHTML = RESPONSIBLE_PERSONS.map(o => `<option value="${esc(o)}">`).join('');
  document.getElementById('changeDriver').value = r.driver_name || '';
  fillChangeSelect('changeTransport', DELIVERY_METHODS, r.transport_name);
  syncChangeDriverHint();
  document.getElementById('changeModal').style.display = 'flex';
}
// แสดงคำใบ้เมื่อขนส่ง = เซลล์ไปส่งเอง (พิมพ์ชื่อได้อิสระ)
function syncChangeDriverHint(){
  const t = (document.getElementById('changeTransport').value || '').trim();
  document.getElementById('changeDriverHint').style.display = (t === 'เซลล์ไปส่งเอง') ? 'block' : 'none';
}
document.getElementById('changeTransport').addEventListener('change', syncChangeDriverHint);
function closeChangeDriver(){ document.getElementById('changeModal').style.display = 'none'; changeIdx = null; }
async function confirmChangeDriver(){
  if(changeIdx === null) return;
  const r = currentRows[changeIdx];
  const driver    = document.getElementById('changeDriver').value.trim();
  const transport = document.getElementById('changeTransport').value.trim();
  if(!driver && !transport){ toast('กรุณาเลือกคนขับหรือขนส่งใหม่', true); return; }
  // ขนส่งผลลัพธ์จริง = ที่เลือกใหม่ หรือคงเดิมถ้าไม่เปลี่ยน
  const effTransport = transport || (r.transport_name||'').trim();
  if(effTransport === 'เซลล์ไปส่งเอง'){
    if(!driver){ toast('เลือก "เซลล์ไปส่งเอง" กรุณาระบุชื่อเซลล์ที่ไปส่งเอง', true); return; }
  } else if(driver && RESPONSIBLE_PERSONS.indexOf(driver) === -1){
    toast('กรุณาเลือกผู้รับผิดชอบจากรายการที่มีให้', true); return;
  }
  if(!confirm('ยืนยันเปลี่ยนคนขับ/ขนส่ง และบันทึกงานนี้เป็น "จัดส่งสำเร็จ" ?')) return;
  const btn = document.getElementById('changeConfirmBtn'); btn.disabled = true;
  try{
    const res = await fetch(CHANGE_URL, {
      method:'POST',
      headers:{'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},
      body: JSON.stringify({ job_key:r.job_key, tx_ids:r.tx_ids, driver_name:driver, transport_name:transport })
    });
    const data = await res.json().catch(()=>null);
    if(!res.ok || !data || !data.ok){ toast((data&&data.message)||'บันทึกไม่สำเร็จ', true); btn.disabled=false; return; }
    toast(data.message || 'เปลี่ยนคนขับ/ขนส่ง และบันทึกสำเร็จแล้ว');
    closeChangeDriver();
    loadData();
  }catch(e){ toast('ผิดพลาด: '+e.message, true); }
  finally{ btn.disabled = false; }
}
document.getElementById('changeModal').addEventListener('click', function(e){ if(e.target===this) closeChangeDriver(); });

document.getElementById('btnSearch').addEventListener('click', doSearch);
document.getElementById('btnClear').addEventListener('click', ()=>{
  fBill.value=''; fDate.value = new Date().toISOString().split('T')[0];
  fAllDates.checked = false; fDate.disabled = false;
  document.getElementById('fCust').value=''; document.getElementById('fCustName').value='';
  document.getElementById('fDriver').value=''; document.getElementById('fStatus').value='';
  document.getElementById('fHeadcom').value=''; document.getElementById('fKind').value='';
  doSearch();
});
fBill.addEventListener('keydown', e=>{ if(e.key==='Enter') doSearch(); });
fDate.addEventListener('change', doSearch);
fAllDates.addEventListener('change', ()=>{ fDate.disabled = fAllDates.checked; doSearch(); });
// filter รหัส/ชื่อลูกค้า/คนขับ = โหลดใหม่จาก server (ตามวันที่ที่เลือก หรือทุกวันถ้าไม่จำกัด) + render ทันทีระหว่างพิมพ์
let _filterTimer = null;
['fCust','fCustName','fDriver'].forEach(id => document.getElementById(id).addEventListener('input', ()=>{
  render();  // กรองชุดที่โหลดมาทันที
  clearTimeout(_filterTimer);
  _filterTimer = setTimeout(doSearch, 400);  // แล้วค่อยโหลดข้ามวันจาก server (กลับหน้า 1)
}));
document.getElementById('fStatus').addEventListener('change', doSearch);
document.getElementById('fHeadcom').addEventListener('change', doSearch);   // เลือกบริษัทผู้ส่ง = โหลดใหม่ (เฉพาะบิลชั่วคราว)
document.getElementById('fKind').addEventListener('change', doSearch);      // เลือกชนิดบิล = โหลดใหม่

document.addEventListener('DOMContentLoaded', ()=>{
  fDate.value = new Date().toISOString().split('T')[0];
  doSearch();
});
</script>
</body>
</html>