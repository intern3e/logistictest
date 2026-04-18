<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Triple 3E Group — ระบบจัดการองค์กร</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --or:#f97316;--or2:#ea580c;--or3:#fff7ed;--or4:#ffedd5;--or5:#fed7aa;
  --dk:#1c1917;--md:#78716c;--lt:#f5f5f4;--wh:#ffffff;--bg:#f8f7f5;
  --bd:#e7e5e4;
}
body{font-family:'Sarabun',sans-serif;background:var(--bg);color:var(--dk);min-height:100vh}

/* NAV */
nav{background:var(--wh);border-bottom:3px solid var(--or);position:sticky;top:0;z-index:100;box-shadow:0 2px 10px rgba(249,115,22,0.09)}
    .nav-inner{max-width:1400px;margin:0 auto;display:flex;align-items:center;height:62px;padding:0 28px;gap:8px}
.nav-logo{display:flex;align-items:center;gap:12px;margin-right:12px;flex-shrink:0}
.nav-mark{width:36px;height:36px;background:var(--or);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:#fff}
.nav-title{font-size:15px;font-weight:800;color:var(--dk)}
.nav-sub{font-size:10px;color:var(--md);letter-spacing:0.07em}
.nav-div{width:1px;height:30px;background:var(--bd);margin:0 6px;flex-shrink:0}
.nav-links{display:flex;gap:2px;flex:1}
.nav-btn{padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;color:var(--md);cursor:pointer;border:none;background:none;font-family:inherit;transition:0.15s;white-space:nowrap;display:flex;align-items:center;gap:6px}
.nav-btn:hover{background:var(--or3);color:var(--or2)}
.nav-btn.active{background:var(--or3);color:var(--or2);border:1px solid var(--or5)}
.nav-btn svg{width:15px;height:15px;flex-shrink:0}
.nav-spacer{flex:1}
.nav-badge{background:var(--or3);border:1px solid var(--or5);color:var(--or2);font-size:10px;font-weight:700;letter-spacing:0.08em;padding:4px 12px;border-radius:20px;white-space:nowrap}

/* PAGES */
.page{display:none;max-width:1400px;margin:0 auto;padding:24px}
.page.active{display:block;animation:fi 0.25s ease}
@keyframes fi{from{opacity:0}to{opacity:1}}

/* CO TABS */
.co-tabs{display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap}
.co-tab{padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;color:var(--md);cursor:pointer;border:1px solid var(--bd);background:var(--wh);font-family:inherit;transition:0.15s}
.co-tab:hover{border-color:var(--or5);color:var(--or)}
.co-tab.active{background:var(--or);color:#fff;border-color:var(--or)}

/* CO HEADER */
.co-header{background:var(--wh);border:1px solid var(--bd);border-radius:14px;padding:20px 24px;margin-bottom:20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.co-pill{background:var(--or);color:#fff;font-size:10px;font-weight:800;letter-spacing:0.1em;text-transform:uppercase;padding:4px 12px;border-radius:4px;flex-shrink:0;align-self:flex-start;margin-top:4px}
.co-name{font-size:24px;font-weight:800;color:var(--dk);line-height:1.2}
.co-name em{font-style:normal;color:var(--or)}
.co-sub{font-size:12px;color:var(--md);margin-top:4px}
.co-stat{margin-left:auto;text-align:right;flex-shrink:0}
.co-stat .n{font-size:42px;font-weight:300;color:var(--or);line-height:1}
.co-stat .l{font-size:10px;color:var(--md);letter-spacing:0.08em;text-transform:uppercase;font-weight:600}

/* ORG */
.ceo-wrap{display:flex;flex-direction:column;align-items:center;margin-bottom:14px}
.ceo-node{background:var(--or);color:#fff;border-radius:12px;padding:14px 20px;display:flex;align-items:center;gap:14px;cursor:pointer;transition:0.2s;box-shadow:0 4px 18px rgba(249,115,22,0.25);min-width:280px}
.ceo-node:hover{background:var(--or2);transform:translateY(-2px);box-shadow:0 8px 24px rgba(249,115,22,0.38)}
.ceo-av{width:46px;height:46px;border-radius:50%;border:2px solid rgba(255,255,255,0.4);overflow:hidden;flex-shrink:0;background:#fed7aa}
.ceo-av img{width:100%;height:100%;object-fit:cover}
.ceo-name{font-size:16px;font-weight:700}
.ceo-role{font-size:11px;opacity:0.85;margin-top:2px}
.ceo-arr{margin-left:auto;font-size:18px;opacity:0.7}
.v-line{width:2px;height:24px;background:linear-gradient(180deg,var(--or),var(--or5));margin:0 auto}

.dept-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
@media(max-width:900px){.dept-grid{grid-template-columns:1fr 1fr}}
@media(max-width:600px){.dept-grid{grid-template-columns:1fr}}
.dept-card{background:var(--wh);border:1px solid var(--bd);border-radius:14px;overflow:hidden;transition:box-shadow 0.2s}
.dept-card:hover{box-shadow:0 6px 20px rgba(249,115,22,0.1)}
.dept-head{padding:14px 16px 12px;background:var(--or3);border-bottom:2px solid var(--or5);display:flex;align-items:center;gap:10px}
.dept-ico{font-size:18px;flex-shrink:0}
.dept-title{font-size:14px;font-weight:800;color:var(--or2)}
.dept-cnt{font-size:11px;color:var(--or);margin-top:2px;font-weight:600}
.m-item{display:flex;align-items:center;gap:10px;padding:9px 14px;border-bottom:1px solid #fafaf9;cursor:pointer;transition:background 0.15s}
.m-item:last-child{border-bottom:none}
.m-item:hover{background:var(--or3)}
.m-av{width:33px;height:33px;border-radius:50%;border:1.5px solid var(--or5);overflow:hidden;flex-shrink:0;background:var(--or4)}
.m-av img{width:100%;height:100%;object-fit:cover}
.m-name{font-size:12px;font-weight:700;color:var(--dk)}
.m-role{font-size:11px;color:var(--md);margin-top:1px}
.m-arr{margin-left:auto;color:var(--or5);font-size:14px;transition:0.15s}
.m-item:hover .m-arr{color:var(--or);transform:translateX(2px)}

/* SCHED */
.sched-top{display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap}
.sched-search{padding:10px 18px;border-radius:24px;border:1.5px solid var(--bd);background:var(--wh);font-size:14px;font-family:inherit;color:var(--dk);outline:none;width:240px;transition:0.2s}
.sched-search:focus{border-color:var(--or);box-shadow:0 0 0 3px rgba(249,115,22,0.1)}
.sf-btns{display:flex;gap:6px;flex-wrap:wrap}
.sf-btn{padding:7px 14px;border-radius:20px;border:1px solid var(--bd);background:var(--wh);font-size:12px;font-weight:600;color:var(--md);cursor:pointer;font-family:inherit;transition:0.15s}
.sf-btn:hover{border-color:var(--or5);color:var(--or)}
.sf-btn.active{background:var(--or);color:#fff;border-color:var(--or)}
.table-wrap{background:var(--wh);border:1px solid var(--bd);border-radius:14px;overflow-x:auto}
table{width:100%;border-collapse:collapse;min-width:940px}
th,td{border:1px solid var(--bd);padding:10px 12px;text-align:center;font-size:12px}
.td-name{text-align:left;background:var(--lt);min-width:155px}
.th-day{font-size:11px;font-weight:700;padding:10px 8px}
.th-mon{background:#fef9c3;color:#854d0e}
.th-tue{background:#fce7f3;color:#9d174d}
.th-wed{background:#dcfce7;color:#166534}
.th-thu{background:#ffedd5;color:#9a3412}
.th-fri{background:#dbeafe;color:#1e3a8a}
.th-sat{background:#f3e8ff;color:#581c87}
.th-sun{background:#fee2e2;color:#991b1b}
.time-box{display:block;font-size:11px;font-weight:600;padding:3px 6px;border-radius:4px;white-space:nowrap}
.work-t{background:#dcfce7;color:#15803d}
.off-t{background:#fee2e2;color:#b91c1c}
.stat-badge{padding:3px 10px;border-radius:20px;font-size:11px;color:#fff;font-weight:700;display:inline-block}
.bg-ok{background:#22c55e}.bg-off{background:#ef4444}

/* SUMMARY */
.sum-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px}
@media(max-width:600px){.sum-cards{grid-template-columns:1fr}}
.sum-card{background:var(--wh);border:1px solid var(--bd);border-radius:14px;padding:16px 20px}
.sum-card-label{font-size:10px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--md);margin-bottom:8px}
.sum-big{font-size:38px;font-weight:300;color:var(--or);line-height:1}
.sum-sub{font-size:11px;color:var(--md);margin-top:4px}
.chart-section{background:var(--wh);border:1px solid var(--bd);border-radius:14px;padding:20px;margin-bottom:16px}
.chart-title{font-size:14px;font-weight:700;color:var(--dk);margin-bottom:14px}
.chart-2col{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
@media(max-width:700px){.chart-2col{grid-template-columns:1fr}}
.bar-row{display:flex;align-items:center;gap:8px;margin-bottom:8px}
.bar-label{font-size:11px;color:var(--md);width:170px;text-align:right;flex-shrink:0;line-height:1.3}
.bar-track{flex:1;height:22px;background:var(--lt);border-radius:6px;overflow:hidden}
.bar-fill{height:100%;border-radius:6px;background:var(--or);display:flex;align-items:center;justify-content:flex-end;padding-right:7px;transition:width 0.7s ease}
.bar-fill span{font-size:10px;font-weight:700;color:#fff}
.pie-row{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
@media(max-width:600px){.pie-row{grid-template-columns:1fr}}
.pie-item{text-align:center}
.donut-wrap{position:relative;width:120px;height:120px;margin:0 auto 10px}
.donut-wrap svg{transform:rotate(-90deg)}
.donut-center{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center}
.donut-pct{font-size:22px;font-weight:300;color:var(--or)}
.donut-lbl{font-size:10px;color:var(--md)}
.pie-co-name{font-size:13px;font-weight:700;color:var(--dk)}
.pie-co-sub{font-size:11px;color:var(--md);margin-top:2px}

/* MODAL */
.overlay{display:none;position:fixed;inset:0;z-index:500;background:rgba(28,25,23,0.52);backdrop-filter:blur(5px);align-items:center;justify-content:center}
.overlay.open{display:flex;animation:fo 0.2s ease}
@keyframes fo{from{opacity:0}to{opacity:1}}
.pmodal{background:var(--wh);border-radius:22px;width:490px;max-width:94vw;overflow:hidden;box-shadow:0 22px 70px rgba(0,0,0,0.17);position:relative;animation:su 0.3s cubic-bezier(.34,1.45,.64,1)}
@keyframes su{from{opacity:0;transform:translateY(20px) scale(0.97)}to{opacity:1;transform:none}}
.pmodal-strip{height:4px;background:linear-gradient(90deg,var(--or2),var(--or),#fbbf24)}
.p-top{padding:22px;display:flex;gap:16px;align-items:flex-start;border-bottom:1px solid var(--bd);position:relative}
.p-photo{width:90px;height:90px;border-radius:14px;overflow:hidden;border:2.5px solid var(--or5);background:var(--or4);flex-shrink:0}
.p-photo img{width:100%;height:100%;object-fit:cover}
.p-company{font-size:10px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--or);margin-bottom:4px}
.p-fullname{font-size:20px;font-weight:800;color:var(--dk);line-height:1.2}
.p-role-tag{display:inline-block;margin-top:6px;background:var(--or3);border:1px solid var(--or5);color:var(--or2);font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px}
.p-dept-txt{font-size:11px;color:var(--md);margin-top:4px}
.p-close{position:absolute;top:14px;right:14px;width:28px;height:28px;border-radius:50%;background:var(--lt);border:1px solid var(--bd);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;color:var(--md);transition:0.15s}
.p-close:hover{background:var(--or);color:#fff;border-color:var(--or)}
.p-body{padding:18px 22px}
.igrid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px}
.ilabel{font-size:10px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#c4b5a0;margin-bottom:3px}
.ival{font-size:14px;font-weight:700;color:var(--dk)}
.ival.phone{color:var(--or2)}
.ival.mono{font-size:12px;letter-spacing:0.04em;color:#5c5551}
.sk-label{font-size:10px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#c4b5a0;margin-bottom:8px}
.sk-wrap{display:flex;flex-wrap:wrap;gap:6px}
.sk{font-size:11px;font-weight:600;padding:4px 10px;border-radius:16px;background:var(--or3);color:var(--or2);border:1px solid var(--or5)}
.sk.filled{background:var(--or);color:#fff;border-color:var(--or)}
.p-foot{display:flex;align-items:center;gap:8px;padding:12px 22px;background:var(--lt);border-top:1px solid var(--bd)}
.p-dot{width:7px;height:7px;border-radius:50%;background:var(--or);flex-shrink:0}
.p-status{font-size:11px;color:var(--md);flex:1}
.p-btn{font-family:inherit;font-size:12px;font-weight:700;padding:8px 18px;border-radius:8px;background:var(--or);color:#fff;border:none;cursor:pointer;transition:0.15s}
.p-btn:hover{background:var(--or2)}

/* FOOTER */
footer{background:var(--wh);border-top:3px solid var(--or);padding:36px 0 20px;margin-top:36px}
.footer-inner{max-width:1400px;margin:0 auto;padding:0 28px}
.footer-top{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:32px;margin-bottom:28px}
@media(max-width:800px){.footer-top{grid-template-columns:1fr 1fr}}
@media(max-width:480px){.footer-top{grid-template-columns:1fr}}
.f-logo{display:flex;align-items:center;gap:10px;margin-bottom:10px}
.f-mark{width:38px;height:38px;background:var(--or);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff}
.f-brand{font-size:15px;font-weight:800;color:var(--dk)}
.f-sub{font-size:10px;color:var(--md);letter-spacing:0.07em}
.f-desc{font-size:12px;color:var(--md);line-height:1.7;margin-bottom:10px}
.f-badges{display:flex;gap:6px;flex-wrap:wrap}
.f-badge{font-size:10px;font-weight:700;letter-spacing:0.07em;padding:4px 10px;border-radius:20px;background:var(--or3);color:var(--or2);border:1px solid var(--or5)}
.footer-col-title{font-size:11px;font-weight:800;letter-spacing:0.1em;text-transform:uppercase;color:var(--or);margin-bottom:12px}
.footer-col-item{font-size:13px;color:var(--md);margin-bottom:7px;cursor:pointer;transition:color 0.15s;padding:0}
.footer-col-item:hover{color:var(--or)}
.f-stat{display:flex;align-items:baseline;gap:6px;margin-bottom:8px}
.f-stat-n{font-size:28px;font-weight:300;color:var(--or)}
.f-stat-l{font-size:11px;color:var(--md)}
.footer-hr{border:none;border-top:1px solid var(--bd);margin-bottom:16px}
.footer-bottom{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
.footer-copy{font-size:11px;color:var(--md);letter-spacing:0.05em}
.footer-right{display:flex;gap:6px}
</style>
</head>
<body>

<nav>
  <div class="nav-inner">
    <div class="nav-logo">
      <div class="nav-mark">3E</div>
      <div>
        <div class="nav-title">ทริปเปิ้ล อี เทรดดิ้ง</div>
        <div class="nav-sub">ระบบจัดการองค์กร</div>
      </div>
    </div>
    <div class="nav-div"></div>
    <div class="nav-links">
      <button class="nav-btn active" onclick="showPage('org',this)">
        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="6" y="1" width="4" height="3" rx="1"/><rect x="1" y="10" width="4" height="3" rx="1"/><rect x="6" y="10" width="4" height="3" rx="1"/><rect x="11" y="10" width="4" height="3" rx="1"/><path d="M8 4v3M3 10V8.5a.5.5 0 01.5-.5h9a.5.5 0 01.5.5V10"/><line x1="8" y1="7.5" x2="8" y2="9.5"/></svg>
        โครงสร้างองค์กร
      </button>
      <button class="nav-btn" onclick="showPage('sched',this)">
        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="2" width="14" height="13" rx="2"/><path d="M5 1v2M11 1v2M1 6h14"/><rect x="4" y="9" width="2" height="2" rx="0.5"/><rect x="7" y="9" width="2" height="2" rx="0.5"/><rect x="10" y="9" width="2" height="2" rx="0.5"/></svg>
        ตารางเวร
      </button>
      <button class="nav-btn" onclick="showPage('summary',this)">
        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 12L5 8l3 3 3-4 3 2"/><rect x="1" y="1" width="14" height="14" rx="2"/></svg>
        สรุปการเข้างาน
      </button>
    </div>
    <div class="nav-spacer"></div>
    <div class="nav-badge">เฉพาะบุคลากรภายใน</div>
  </div>
</nav>

<!-- ORG PAGE -->
<div class="page active" id="page-org">
  <div class="co-tabs" id="coTabs"></div>
  <div id="coContent"></div>
</div>

<!-- SCHED PAGE -->
<div class="page" id="page-sched">
  <div class="sched-top">
    <input class="sched-search" id="schedSearch" placeholder="ค้นหาชื่อหรือตำแหน่ง..." oninput="renderSched()">
    <div class="sf-btns" id="schedFilter"></div>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="text-align:left;background:var(--lt)">ชื่อ – ตำแหน่ง</th>
          <th style="background:var(--lt);font-size:11px">บริษัท</th>
          <th style="background:var(--lt);font-size:11px">แผนก</th>
          <th style="background:var(--lt);font-size:11px">สถานะวันนี้</th>
          <th class="th-day th-mon">จันทร์</th>
          <th class="th-day th-tue">อังคาร</th>
          <th class="th-day th-wed">พุธ</th>
          <th class="th-day th-thu">พฤหัสบดี</th>
          <th class="th-day th-fri">ศุกร์</th>
          <th class="th-day th-sat">เสาร์</th>
          <th class="th-day th-sun">อาทิตย์</th>
        </tr>
      </thead>
      <tbody id="schedBody"></tbody>
    </table>
  </div>
</div>

<!-- SUMMARY PAGE -->
<div class="page" id="page-summary">
  <div class="sum-cards" id="sumCards"></div>
  <div class="chart-2col">
    <div class="chart-section">
      <div class="chart-title">อัตราเข้างานเฉลี่ยต่อสัปดาห์ แยกตามแผนก</div>
      <div id="deptBars"></div>
    </div>
    <div class="chart-section">
      <div class="chart-title">อัตราเข้างานรายวัน (ทุกบริษัทรวม)</div>
      <div id="dayBars"></div>
    </div>
  </div>
  <div class="chart-section">
    <div class="chart-title">สัดส่วนการเข้างาน แยกตามบริษัท</div>
    <div class="pie-row" id="pieSection"></div>
  </div>
</div>

<!-- MODAL -->
<div class="overlay" id="overlay" onclick="closeModal(event)">
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="p-top">
      <div class="p-photo"><img id="m-img" src="" alt="" onerror="this.src='https://randomuser.me/api/portraits/lego/1.jpg'"></div>
      <div class="p-info">
        <div class="p-company" id="m-co"></div>
        <div class="p-fullname" id="m-name"></div>
        <div class="p-role-tag" id="m-role"></div>
        <div class="p-dept-txt" id="m-dept"></div>
      </div>
      <div class="p-close" onclick="closeModal()">✕</div>
    </div>
    <div class="p-body">
      <div class="igrid">
        <div><div class="ilabel">เบอร์โทรศัพท์</div><div class="ival phone" id="m-phone"></div></div>
        <div><div class="ilabel">รหัสพนักงาน</div><div class="ival mono" id="m-eid"></div></div>
        <div style="grid-column:1/-1"><div class="ilabel">เลขบัตรประชาชน</div><div class="ival mono" id="m-cid"></div></div>
      </div>
      <div class="sk-label">ทักษะและความเชี่ยวชาญ</div>
      <div class="sk-wrap" id="m-skills"></div>
    </div>
    <div class="p-foot">
      <div class="p-dot"></div>
      <div class="p-status">สถานะ: ปฏิบัติงานอยู่</div>
      <button class="p-btn" onclick="closeModal()">ปิดหน้าต่าง</button>
    </div>
  </div>
</div>

<footer>
  <div class="footer-inner">
    <div class="footer-top">
      <div>
        <div class="f-logo">
          <div class="f-mark">3E</div>
          <div>
            <div class="f-brand">Triple 3E Group</div>
            <div class="f-sub">กลุ่มบริษัทไฟฟ้าและพลังงาน</div>
          </div>
        </div>
        <div class="f-desc">ระบบจัดการองค์กรแบบรวมศูนย์สำหรับกลุ่มบริษัทในเครือ<br>เข้าถึงได้เฉพาะบุคลากรภายในที่ได้รับอนุญาตเท่านั้น</div>
        <div class="f-badges">
          <span class="f-badge">ทริปเปิ้ล อี เทรดดิ้ง</span>
          <span class="f-badge">ฮิคาริ เดงกิ</span>
          <span class="f-badge">โซล่าเเรพบิท</span>
        </div>
      </div>
      <div>
        <div class="footer-col-title">บริษัทในเครือ</div>
        <div class="footer-col-item" onclick="showPage('org',null);switchCo(0)">ทริปเปิ้ล อี เทรดดิ้ง</div>
        <div class="footer-col-item" onclick="showPage('org',null);switchCo(1)">ฮิคาริ เดงกิ</div>
        <div class="footer-col-item" onclick="showPage('org',null);switchCo(2)">โซล่าเเรพบิท</div>
      </div>
      <div>
        <div class="footer-col-title">ระบบงาน</div>
        <div class="footer-col-item" onclick="showPage('org',null)">โครงสร้างองค์กร</div>
        <div class="footer-col-item" onclick="showPage('sched',null)">ตารางเวรพนักงาน</div>
        <div class="footer-col-item" onclick="showPage('summary',null)">สรุปการเข้างาน</div>
      </div>
      <div>
        <div class="footer-col-title">สถิติรวม</div>
        <div class="f-stat"><div class="f-stat-n">3</div><div class="f-stat-l">บริษัทในเครือ</div></div>
        <div class="f-stat"><div class="f-stat-n" id="f-total-emp">90</div><div class="f-stat-l">พนักงานทั้งหมด</div></div>
        <div class="f-stat"><div class="f-stat-n" id="f-total-dept">9</div><div class="f-stat-l">แผนกทั้งหมด</div></div>
      </div>
    </div>
    <hr class="footer-hr">
    <div class="footer-bottom">
      <div class="footer-copy"> กลุ่มบริษัทไฟฟ้า · ระบบจัดการองค์กร · สงวนสิทธิ์ทุกประการ</div>
      <div class="footer-right">
        <span class="f-badge"></span>
      </div>
    </div>
  </div>
</footer>

<script>
/* ── PHOTO HELPERS ── */
const menIdx=[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,33,34,35,36,37,38,39,40,41,42,43,45,46,47,48,49,50,51,52];
const womenIdx=[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,33,34,35,36,37,38,39,40,41,42,43,45,46,47,48,49,50];
let mi=0,wi=0;
const mP=()=>`https://randomuser.me/api/portraits/men/${menIdx[mi++%menIdx.length]}.jpg`;
const wP=()=>`https://randomuser.me/api/portraits/women/${womenIdx[wi++%womenIdx.length]}.jpg`;
const pid=()=>'EMP-'+String(Math.floor(10000+Math.random()*90000));
const cidGen=()=>{let n='';for(let i=0;i<13;i++)n+=Math.floor(Math.random()*10);return n.replace(/(\d)(\d{4})(\d{5})(\d{2})(\d)/,'$1-$2-$3-$4-$5');};
const ph=()=>'0'+[6,8,9][~~(Math.random()*3)]+''+~~(10000000+Math.random()*90000000);

/* ── SKILL SETS ── */
const SK={
  ช่าง:['ระบบไฟฟ้ากำลัง','ติดตั้งสายไฟ','ทดสอบและเดินระบบ','อ่านแบบไฟฟ้า','ตู้ MDB/DB'],
  บำรุง:['บำรุงรักษาเชิงป้องกัน','วิเคราะห์ความผิดปกติ','ถ่ายภาพความร้อน','คุณภาพไฟฟ้า','ระบบ CMMS'],
  วศ:['AutoCAD Electrical','วิเคราะห์วงจรไฟฟ้า','คำนวณกระแสลัดวงจร','วิเคราะห์โหลดโฟลว์','โปรแกรม ETAP'],
  แบบ:['Revit MEP','ประสานงาน BIM','แบบก่อสร้าง','แบบ As-built','ประมาณราคา'],
  โปร:['วางแผนโครงการ','MS Project','ถอดปริมาณ BOQ','บริหารหน้างาน','ควบคุมคุณภาพ'],
  qa:['มาตรฐาน IEC 60364','มาตรฐาน NFPA 70','ทดสอบฉนวน Megger','ทดสอบสายเคเบิล','รายงานตรวจสอบ'],
  hr:['ระบบ HR SAP','การจ่ายเงินเดือน','แรงงานสัมพันธ์','มาตรฐาน ISO 9001','ใบอนุญาตแรงงาน'],
  driver:['ขับรถบรรทุก 10 ล้อ','ระบบ GPS','ใบขับขี่รถยก','ขนส่งวัสดุอันตราย','ซ่อมบำรุงเบื้องต้น'],
  solar:['บริหารโครงการ','PVSyst','ออกแบบระบบ PV','มาตรฐานการติดตั้ง','ใบอนุญาตดัดแปลงอาคาร'],
};

/* ── COMPANY DATA ──
   member format: [name, role, skills[], gender'm/w', timeIn, sched[mon..sun]]
*/
const COMPANIES=[
{name:'ทริปเปิ้ล อี เทรดดิ้ง',short:'3E Trading',pill:'บริษัทที่ 01',color:'#f97316',
 ceo:{name:'คุณวิชัย มงคลศิริ',role:'ผู้อำนวยการบริหาร',photo:'https://randomuser.me/api/portraits/men/32.jpg',co:'บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด',dept:'ผู้บริหาร',skills:SK.วศ},
 sub:'บริษัทการขายส่งอุปกรณ์และชิ้นส่วนอิเล็กทรอนิกส์',
 depts:[
  {title:'ชุดปฏิบัติการ ก',ico:'⚡',members:[
    ['คุณสมชาย วงศ์ดี','หัวหน้าทีม ก',SK.ช่าง,'m','08:00',[1,1,1,1,1,0,0]],
    ['คุณกิตติ ทองดี','ช่างไฟฟ้าอาวุโส',SK.ช่าง,'m','08:00',[1,1,1,1,1,0,0]],
    ['คุณประมวล แสงทอง','ช่างไฟฟ้า',SK.ช่าง,'m','07:00',[1,1,0,1,1,1,0]],
    ['คุณอนุชา ใจดี','ช่างไฟฟ้าระดับต้น',SK.ช่าง,'m','07:00',[0,1,1,1,1,1,0]],
    ['คุณวรพล ศรีสุข','ผู้ช่วยช่าง',SK.ช่าง,'m','07:00',[1,0,1,1,1,1,0]],
    ['คุณณัฐพล มั่นคง','ผู้ช่วยช่าง',SK.ช่าง,'m','08:00',[1,1,1,0,1,0,1]],
    ['คุณปรีชา บุญชู','เจ้าหน้าที่ความปลอดภัย',['ความปลอดภัยหน้างาน','ปฐมพยาบาล','ตรวจสอบ PPE','ความปลอดภัยอัคคีภัย','แผนฉุกเฉิน'],'m','08:00',[1,1,1,1,0,1,1]],
    ['คุณธวัช สุวรรณ','พนักงานขับรถ-ช่าง',SK.driver,'m','08:00',[0,1,1,1,1,1,0]],
  ]},
  {title:'ชุดปฏิบัติการ ข',ico:'🔌',members:[
    ['คุณพรชัย เจริญ','หัวหน้าทีม ข',SK.ช่าง,'m','08:00',[1,1,1,1,1,0,0]],
    ['คุณสุรพล จันทร์ศรี','ช่างไฟฟ้าอาวุโส',SK.ช่าง,'m','08:00',[1,0,1,1,1,1,0]],
    ['คุณธีระ พงษ์ดี','ช่างไฟฟ้า',SK.ช่าง,'m','07:00',[1,1,0,1,1,1,0]],
    ['คุณมานพ รักดี','ช่างไฟฟ้า',SK.ช่าง,'m','08:00',[1,1,1,0,1,0,1]],
    ['คุณชัยวัฒน์ แจ่มใส','ช่างไฟฟ้า',SK.ช่าง,'m','19:00',[1,1,1,1,0,1,1]],
    ['คุณวิโรจน์ สมบัติ','ช่างไฟฟ้าระดับต้น',SK.ช่าง,'m','19:00',[0,1,1,1,1,1,0]],
    ['คุณเอกชัย ทรัพย์ดี','ผู้ช่วยช่าง',SK.ช่าง,'m','08:00',[1,1,1,1,0,1,0]],
    ['คุณไพรัตน์ พิมพ์ดี','ผู้ช่วยช่าง',SK.ช่าง,'m','08:00',[1,1,0,1,1,0,1]],
    ['คุณสุดา บุญมี','ผู้ประสานงานสนาม',['ประสานงานหน้างาน','เขียนรายงาน','อัปเดตแบบ','จัดการ NCR','ตรวจสอบ'],'w','08:00',[1,1,1,1,1,0,0]],
    ['คุณบัญชา ลมดี','พนักงานขับรถ-ช่าง',SK.driver,'m','07:00',[1,0,1,1,1,1,0]],
  ]},
  {title:'งานบำรุงรักษา',ico:'🔧',members:[
    ['คุณวิรัตน์ สมบูรณ์','วิศวกรบำรุงรักษาหลัก',SK.บำรุง,'m','08:00',[1,1,1,1,1,0,0]],
    ['คุณสมศักดิ์ เดชา','ช่างบำรุงรักษา',SK.บำรุง,'m','07:00',[1,1,0,1,1,1,0]],
    ['คุณอำนาจ ใจกล้า','ช่างบำรุงรักษา',SK.บำรุง,'m','07:00',[0,1,1,1,1,1,0]],
    ['คุณประเสริฐ ชัยดี','ช่างบำรุงรักษา',SK.บำรุง,'m','08:00',[1,0,1,1,1,1,0]],
    ['คุณวีระ ขยันดี','ช่างบำรุงรักษา',SK.บำรุง,'m','08:00',[1,1,1,0,1,0,1]],
    ['คุณนิรันดร์ ยืนยง','ช่างเครื่องมือวัด',SK.บำรุง,'m','08:00',[1,1,1,1,0,1,1]],
    ['คุณพิทักษ์ รักษ์ดี','ช่างเครื่องมือวัด',SK.บำรุง,'m','08:00',[0,1,1,1,1,1,0]],
    ['คุณสุรชัย แน่วดี','นักวางแผนบำรุงรักษา',['โมดูล SAP PM','วางแผนบำรุงรักษา','รายงาน KPI','แผนหยุดซ่อม','บริหารอะไหล่'],'m','08:00',[1,1,1,1,1,0,0]],
    ['คุณสรายุทธ ก้าวหน้า','ช่างบำรุงรักษา',SK.บำรุง,'m','19:00',[1,1,1,1,0,1,1]],
  ]},
]},
{name:'ฮิคาริ เดงกิ',short:'Hikari Denki',pill:'บริษัทที่ 02',color:'#3b82f6',
 ceo:{name:'คุณอานนท์ สุวรรณชัย',role:'หัวหน้าวิศวกรและผู้อำนวยการ',photo:'https://randomuser.me/api/portraits/men/55.jpg',co:'บริษัท ฮิคาริ เดงกิ จำกัด',dept:'ผู้บริหาร',skills:SK.วศ},
 sub:'บริษัทการขายส่งเครื่องใช้ไฟฟ้าและอิเล็กทรอนิกส์ชนิดใช้ในครัวเรือน',
 depts:[
  {title:'วิศวกรรม / ออกแบบ',ico:'📐',members:[
    ['คุณณรงค์ ชาญวิศว์','วิศวกรอาวุโส',SK.วศ,'m','08:00',[1,1,1,1,1,0,0]],
    ['คุณจิรา พิมพ์สวย','ช่างเขียนแบบอาวุโส',SK.แบบ,'w','08:00',[1,1,1,1,1,0,0]],
    ['คุณปิยะ คำนวณดี','นักประมาณราคา',SK.แบบ,'m','08:00',[1,1,0,1,1,1,0]],
    ['คุณกฤษณะ แบบดี','วิศวกรออกแบบ',SK.วศ,'m','08:00',[0,1,1,1,1,1,0]],
    ['คุณวิภาวี แม่นมือ','ช่างเขียนแบบ',SK.แบบ,'w','08:00',[1,0,1,1,1,1,0]],
    ['คุณอรุณ ฉลาดดี','ผู้เชี่ยวชาญ BIM',SK.แบบ,'m','08:00',[1,1,1,0,1,0,1]],
    ['คุณปณิธาน ตรงใจ','วิศวกรออกแบบ',SK.วศ,'m','08:00',[1,1,1,1,0,1,1]],
    ['คุณดวงตา ละเอียด','ช่างเขียนแบบระดับต้น',SK.แบบ,'w','08:00',[0,1,1,1,1,1,0]],
    ['คุณภาณุ สร้างสรรค์','ผู้เชี่ยวชาญ Revit',SK.แบบ,'m','08:00',[1,1,1,1,1,0,0]],
    ['คุณยุทธนา คุ้มดี','วิศวกรต้นทุน',['จัดทำ BOQ','ถอดปริมาณวัสดุ','ควบคุมต้นทุน','วิศวกรรมคุณค่า','ประเมินผู้ขาย'],'m','08:00',[1,1,0,1,1,1,0]],
  ]},
  {title:'งานโครงการ',ico:'📊',members:[
    ['คุณมงคล สำเร็จ','ผู้จัดการโครงการ',SK.โปร,'m','08:00',[1,1,1,1,1,0,0]],
    ['คุณชาตรี แผนดี','วิศวกรโครงการ',SK.โปร,'m','08:00',[1,1,1,1,0,1,1]],
    ['คุณธิดา ควบคุมดี','วิศวกรโครงการ',SK.โปร,'w','08:00',[0,1,1,1,1,1,0]],
    ['คุณสันติ ร่วมมือ','วิศวกรหน้างาน',SK.โปร,'m','07:00',[1,1,0,1,1,1,0]],
    ['คุณอรทัย รายงานดี','ผู้ประสานงานโครงการ',SK.โปร,'w','08:00',[1,0,1,1,1,1,0]],
    ['คุณเกียรติศักดิ์ นำทาง','ผู้ควบคุมงานสนาม',SK.โปร,'m','07:00',[1,1,1,0,1,0,1]],
    ['คุณวันชัย มุ่งมั่น','ผู้ควบคุมงานสนาม',SK.โปร,'m','07:00',[1,1,1,1,0,1,1]],
    ['คุณจีรนันท์ ทำได้','ผู้ควบคุมเอกสาร',['ควบคุมเอกสาร','ISO 9001','จัดการ RFI','ติดตาม Submittal','จัดเก็บโครงการ'],'w','08:00',[0,1,1,1,1,1,0]],
    ['คุณดิษฐ์ เร็วดี','นักวางแผนตารางงาน',['Primavera P6','MS Project','วางแผน WBS','บรรจุทรัพยากร','รายงานความคืบหน้า'],'m','08:00',[1,1,1,1,1,0,0]],
    ['คุณปัญญา แก้ปัญหา','นักสำรวจปริมาณงาน',SK.โปร,'m','08:00',[1,1,0,1,1,1,0]],
  ]},
  {title:'ควบคุมคุณภาพ',ico:'✅',members:[
    ['คุณพัชรา ตรวจสอบ','ผู้จัดการควบคุมคุณภาพ',SK.qa,'w','08:00',[1,1,1,1,1,0,0]],
    ['คุณเรืองฤทธิ์ แม่นยำ','วิศวกรควบคุมคุณภาพ',SK.qa,'m','08:00',[1,1,1,1,0,1,1]],
    ['คุณนภา ตั้งใจ','ผู้ตรวจสอบคุณภาพ',SK.qa,'w','07:00',[0,1,1,1,1,1,0]],
    ['คุณสุวิทย์ ยืนยัน','ผู้ตรวจสอบคุณภาพ',SK.qa,'m','08:00',[1,1,0,1,1,1,0]],
    ['คุณชนกนันท์ ตรวจแน่','ผู้ตรวจสอบคุณภาพ',SK.qa,'w','08:00',[1,0,1,1,1,1,0]],
    ['คุณพิเชษฐ์ ทดสอบ','วิศวกรทดสอบ',SK.qa,'m','08:00',[1,1,1,0,1,0,1]],
    ['คุณอุบล ตามมาตรฐาน','ช่างสอบเทียบ',SK.qa,'w','08:00',[1,1,1,1,0,1,1]],
    ['คุณสุมาลี รายงาน','ผู้ควบคุม NCR',['จัดการ NCR','กระบวนการ CAPA','ตรวจสอบภายใน','เอกสาร ISO','บันทึกสอบเทียบ'],'w','08:00',[0,1,1,1,1,1,0]],
    ['คุณอาทิตย์ ถูกต้อง','ผู้ตรวจสอบคุณภาพ',SK.qa,'m','19:00',[1,1,1,1,1,0,0]],
    ['คุณไพโรจน์ ปลอดภัย','เจ้าหน้าที่ความปลอดภัยและคุณภาพ',['ตรวจสอบความปลอดภัย','JSA/HIRA','รายงานอุบัติเหตุ','บริหาร PPE','ฝึกอบรมความปลอดภัย'],'m','08:00',[1,1,0,1,1,1,0]],
  ]},
]},
{name:'โซล่าเเรพบิท',short:'Solar Rabbit',pill:'บริษัทที่ 03',color:'#22c55e',
 ceo:{name:'คุณรัตนา พงษ์ประเสริฐ',role:'ผู้จัดการทั่วไป',photo:'https://randomuser.me/api/portraits/women/44.jpg',co:'บริษัท โซล่าเเรพบิท จำกัด',dept:'ผู้บริหาร',skills:SK.hr},
 sub:'บริษัทที่ให้บริการติดตั้งโซล่าเซลล์แบบครบวงจร มีทีมวิศวกรพลังงานที่มีความสามารถ',
 depts:[
  {title:'พลังงานแสงอาทิตย์ / โซล่าเซลล์',ico:'☀️',members:[
    ['คุณวิชัย แสงอาทิตย์','ผู้จัดการโครงการ Solar',SK.solar,'m','08:00',[1,1,1,1,1,0,0]],
    ['คุณธีรศักดิ์ ออกแบบ','วิศวกรออกแบบระบบ',['AutoCAD','SketchUp','คำนวณโหลดไฟฟ้า','Single Line Diagram','จำลองผลผลิตพลังงาน'],'m','08:00',[1,1,0,1,1,1,0]],
    ['คุณสมชาย ติดตั้ง','หัวหน้าทีมติดตั้ง',['งานโครงสร้างเหล็ก','ติดตั้งแผง PV','เดินสาย DC/AC','ความปลอดภัยในการทำงานบนที่สูง','LOTO'],'m','07:00',[1,1,1,1,0,1,1]],
    ['คุณธนา สายไฟ','ช่างไฟฟ้าอาวุโส',['ติดตั้ง Inverter','ตู้ MDB','ระบบ Grounding','ทดสอบระบบไฟฟ้า','Commissioning'],'m','07:00',[0,1,1,1,1,1,0]],
    ['คุณเมษา ตรวจสอบ','เจ้าหน้าที่ QC',['ตรวจสอบจุดร้อน','วัดประสิทธิภาพแผง','ตรวจสอบรอยรั่วหลังคา','มาตรฐาน IEC','รายงานการทดสอบ'],'w','08:00',[1,0,1,1,1,1,0]],
    ['คุณเอกชัย บำรุง','ช่างซ่อมบำรุง Solar',['ล้างแผงโซล่าเซลล์','ตรวจสอบความแน่นจุดต่อ','Monitor ระบบออนไลน์','แก้ไขปัญหา Inverter','Preventive Maintenance'],'m','08:00',[1,1,1,0,1,0,1]],
    ['คุณนารี ประสานงาน','เจ้าหน้าที่ขออนุญาต',['ยื่นขนานไฟ (MEA/PEA)','เอกสาร กกพ.','ประสานงานเทศบาล','BOI','สิทธิประโยชน์ภาษี'],'w','08:00',[1,1,1,1,0,1,1]],
    ['คุณพีระ วิเคราะห์','นักวิเคราะห์ความคุ้มค่า',['คำนวณระยะเวลาคืนทุน','วิเคราะห์ค่าไฟฟ้า','ข้อเสนอโครงการ','สัญญา ESCO','Carbon Credit'],'m','08:00',[0,1,1,1,1,1,0]],
    ['คุณชัชวาลย์ คลังโซล่า','ธุรการพัสดุโซล่า',['สต็อกแผง PV','อุปกรณ์ยึดจับ','บริหารเครื่องมือวัด','จัดชุดอุปกรณ์หน้างาน','ตรวจสอบ Lot สินค้า'],'m','08:00',[1,1,1,1,1,0,0]],
    ['คุณสุดา ดูแลลูกค้า','เจ้าหน้าที่บริการหลังการขาย',['รับแจ้งปัญหา','นัดหมายเข้าบริการ','สอนการใช้งาน App','รายงานสรุปผลประหยัด','CRM'],'w','08:00',[1,1,0,1,1,1,0]],
  ]},
  {title:'ขายและสำรวจ / โซล่าเซลล์',ico:'🔍',members:[
    ['คุณเกรียงไกร ขายเก่ง','ผู้จัดการฝ่ายขาย',['กลยุทธ์การตลาดโซล่า','เจรจาต่อรอง','บริหารตัวแทนขาย','วิเคราะห์คู่แข่ง','ปิดการขาย B2B'],'m','08:00',[1,1,1,1,1,0,0]],
    ['คุณวิมล สำรวจเก่ง','วิศวกรสำรวจหน้างาน',['โดรนสำรวจ','วัดพื้นที่หลังคา','เช็คทิศทางแสง','ประเมินเงาบัง','Compass & Clinometer'],'w','08:00',[1,1,0,1,1,1,0]],
    ['คุณอานนท์ ตีราคา','เจ้าหน้าที่ประมาณราคา',['ถอดแบบวัสดุ BOQ','เปรียบเทียบราคาซัพพลายเออร์','ควบคุมกำไรโครงการ','วิเคราะห์จุดคุ้มทุน','จัดทำใบเสนอราคา'],'m','08:00',[0,1,1,1,1,1,0]],
    ['คุณสิรินทร์ ประสานขาย','ธุรการฝ่ายขาย',['ดูแลเอกสารสัญญา','นัดหมายลูกค้า','ระบบ CRM','ติดตามสถานะชำระเงิน','ประสานงานฝ่ายติดตั้ง'],'w','08:00',[1,1,1,0,1,0,1]],
    ['คุณพงศกร ประชาสัมพันธ์','เจ้าหน้าที่การตลาดดิจิทัล',['คอนเทนต์ประหยัดไฟ','ยิงโฆษณา Facebook/Google','ดูแลเว็บไซต์','กราฟิกสินค้าโซล่า','จัดอีเวนต์สัมมนา'],'m','08:00',[1,1,1,1,0,1,1]],
  ]},
  {title:'โครงสร้างและเทคโนโลยี',ico:'🏗️',members:[
    ['คุณธนพงศ์ แข็งแรง','วิศวกรโครงสร้าง',['คำนวณน้ำหนักบรรทุก','เสริมกำลังหลังคา','มาตรฐานความปลอดภัย','เซ็นรับรองโครงสร้าง','ออกแบบฐานราก Solar Farm'],'m','08:00',[1,1,1,1,1,0,0]],
    ['คุณมานะ ระบบดี','ผู้เชี่ยวชาญระบบ Monitoring',['IoT Sensors','Setup Gateway','Configuration App','วิเคราะห์ Big Data พลังงาน','ระบบแจ้งเตือน Error'],'m','08:00',[1,1,0,1,1,1,0]],
    ['คุณสายชล กันรั่ว','หัวหน้าทีมเทคนิคหลังคา',['งานกันซึม','ติดตั้ง Flashings','ตรวจสอบรอยน็อต','ซ่อมแซมกระเบื้อง','เทคนิคการเจาะหลังคาเหล็ก'],'m','07:00',[0,1,1,1,1,1,0]],
    ['คุณยุพา วางแผน','เจ้าหน้าที่วางแผนการผลิต',['จัดตารางงานช่าง','บริหารรถเครน','ควบคุมระยะเวลาก่อสร้าง','รายงานความคืบหน้า','จัดหาซับคอนแทรคเตอร์'],'w','08:00',[1,0,1,1,1,1,0]],
    ['คุณวิรัตน์ ปลอดภัย','เจ้าหน้าที่ความปลอดภัย จป.',['ตรวจอุปกรณ์ Harness','อบรมความปลอดภัย','เช็คสภาพนั่งร้าน','แผนฉุกเฉินหน้างาน','ตรวจสอบชุด PPE'],'m','08:00',[1,1,1,0,1,0,1]],
  ]},
]},
];

/* ── ASSIGN PHOTOS & IDs ── */
COMPANIES.forEach(co=>{
  co.ceo.phone=ph();co.ceo.eid=pid();co.ceo.cid=cidGen();
  co.depts.forEach(d=>d.members.forEach(m=>{
    m.photo=m[3]==='w'?wP():mP();
    m.phone=ph();m.eid=pid();m.cid=cidGen();
  }));
});

/* ── PAGE SWITCH ── */
function showPage(id,btn){
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.getElementById('page-'+id).classList.add('active');
  if(btn){document.querySelectorAll('.nav-btn').forEach(b=>b.classList.remove('active'));btn.classList.add('active');}
  if(id==='summary')buildSummary();
  if(id==='sched'){buildSchedFilters();renderSched();}
}

/* ── ORG ── */
function buildOrg(){
  const t=document.getElementById('coTabs');
  t.innerHTML=COMPANIES.map((c,i)=>`<button class="co-tab${i===0?' active':''}" onclick="switchCo(${i})">${c.pill}: ${c.name}</button>`).join('');
  showCo(0);
  updateFooterStats();
}

function switchCo(i){
  document.querySelectorAll('.co-tab').forEach((t,idx)=>t.classList.toggle('active',idx===i));
  showCo(i);
}

function showCo(i){
  const co=COMPANIES[i];
  const total=co.depts.reduce((s,d)=>s+d.members.length,0)+1;
  document.getElementById('coContent').innerHTML=`
    <div class="co-header">
      <div class="co-pill">${co.pill}</div>
      <div><div class="co-name">${co.name}</div><div class="co-sub">${co.sub}</div></div>
      <div class="co-stat"><div class="l">พนักงานทั้งหมด</div><div class="n">${total}</div></div>
    </div>
    <div class="ceo-wrap">
      <div class="ceo-node" onclick='openProfile(${JSON.stringify(co.ceo)})'>
        <div class="ceo-av"><img src="${co.ceo.photo}" onerror="this.src='https://randomuser.me/api/portraits/lego/1.jpg'"></div>
        <div><div class="ceo-name">${co.ceo.name}</div><div class="ceo-role">${co.ceo.role}</div></div>
        <div class="ceo-arr">›</div>
      </div>
      <div class="v-line"></div>
    </div>
    <div class="dept-grid">${co.depts.map(d=>deptCardHTML(d,co)).join('')}</div>`;
}

function deptCardHTML(dept,co){
  return`<div class="dept-card">
    <div class="dept-head"><div class="dept-ico">${dept.ico}</div>
      <div><div class="dept-title">${dept.title}</div><div class="dept-cnt">${dept.members.length} คน</div></div>
    </div>
    ${dept.members.map(m=>{
      const p={name:m[0],role:m[1],co:co.name,dept:dept.title,photo:m.photo,phone:m.phone,eid:m.eid,cid:m.cid,skills:m[2]};
      return`<div class="m-item" onclick='openProfile(${JSON.stringify(p)})'>
        <div class="m-av"><img src="${m.photo}" onerror="this.src='https://randomuser.me/api/portraits/lego/1.jpg'"></div>
        <div><div class="m-name">${m[0]}</div><div class="m-role">${m[1]}</div></div>
        <div class="m-arr">›</div>
      </div>`;
    }).join('')}
  </div>`;
}

/* ── SCHED ── */
let schedCoFilter='all';
function buildSchedFilters(){
  const el=document.getElementById('schedFilter');
  if(el.children.length>0)return;
  el.innerHTML=`<button class="sf-btn active" onclick="setSchedFilter('all',this)">ทุกบริษัท</button>`+
    COMPANIES.map((c,i)=>`<button class="sf-btn" onclick="setSchedFilter(${i},this)">${c.short}</button>`).join('');
}
function setSchedFilter(v,btn){
  schedCoFilter=v;
  document.querySelectorAll('.sf-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  renderSched();
}
function renderSched(){
  const q=(document.getElementById('schedSearch')||{value:''}).value.toLowerCase();
  const tbody=document.getElementById('schedBody');
  if(!tbody)return;
  tbody.innerHTML='';
  COMPANIES.forEach((co,ci)=>{
    if(schedCoFilter!=='all'&&String(schedCoFilter)!=String(ci))return;
    co.depts.forEach(d=>{
      d.members.forEach(m=>{
        if(q&&!m[0].toLowerCase().includes(q)&&!m[1].toLowerCase().includes(q))return;
        const tr=document.createElement('tr');
        const days=m[5].map(w=>`<td><span class="time-box ${w?'work-t':'off-t'}">${w?m[4]:'หยุด'}</span></td>`).join('');
        tr.innerHTML=`<td class="td-name"><strong>${m[0]}</strong><br><small style="color:var(--md);font-size:11px">${m[1]}</small></td>
          <td style="font-size:11px;white-space:nowrap">${co.short}</td>
          <td style="font-size:11px;max-width:120px">${d.title}</td>
          <td><span class="stat-badge ${m[5][0]?'bg-ok':'bg-off'}">${m[5][0]?'เข้างาน':'หยุด'}</span></td>
          ${days}`;
        tbody.appendChild(tr);
      });
    });
  });
}

/* ── SUMMARY ── */
function buildSummary(){
  let totEmp=0,totWork=0,totDept=0;
  const deptData=[],dayT=[0,0,0,0,0,0,0],dayN=[0,0,0,0,0,0,0];
  COMPANIES.forEach(co=>{
    totDept+=co.depts.length;
    co.depts.forEach(d=>{
      let dW=0;
      d.members.forEach(m=>{
        totEmp++;if(m[5][0])totWork++;
        dW+=m[5].reduce((s,v)=>s+v,0);
        m[5].forEach((v,i)=>{dayT[i]+=v;dayN[i]++;});
      });
      deptData.push({label:d.title.length>20?d.title.substring(0,20)+'…':d.title,pct:Math.round(dW/(d.members.length*7)*100),color:co.color});
    });
  });
  document.getElementById('f-total-emp').textContent=totEmp;
  document.getElementById('f-total-dept').textContent=totDept;

  document.getElementById('sumCards').innerHTML=`
    <div class="sum-card"><div class="sum-card-label">พนักงานทั้งหมด</div><div class="sum-big">${totEmp}</div><div class="sum-sub">ใน 3 บริษัท ${totDept} แผนก</div></div>
    <div class="sum-card"><div class="sum-card-label">เข้างานวันนี้ (จันทร์)</div><div class="sum-big">${totWork}</div><div class="sum-sub">${Math.round(totWork/totEmp*100)}% ของพนักงานทั้งหมด</div></div>
    <div class="sum-card"><div class="sum-card-label">จำนวนแผนกทั้งหมด</div><div class="sum-big">${totDept}</div><div class="sum-sub">ครอบคลุม 3 กลุ่มธุรกิจ</div></div>`;

  const sortedD=[...deptData].sort((a,b)=>b.pct-a.pct);
  document.getElementById('deptBars').innerHTML=sortedD.map(d=>`
    <div class="bar-row">
      <div class="bar-label">${d.label}</div>
      <div class="bar-track"><div class="bar-fill" style="width:${d.pct}%;background:${d.color}"><span>${d.pct}%</span></div></div>
    </div>`).join('');

  const days=['จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์','อาทิตย์'];
  document.getElementById('dayBars').innerHTML=days.map((day,i)=>{
    const pct=Math.round(dayT[i]/dayN[i]*100);
    return`<div class="bar-row">
      <div class="bar-label">${day}</div>
      <div class="bar-track"><div class="bar-fill" style="width:${pct}%"><span>${pct}%</span></div></div>
    </div>`;
  }).join('');

  document.getElementById('pieSection').innerHTML=COMPANIES.map(co=>{
    let cW=0,cT=0;
    co.depts.forEach(d=>d.members.forEach(m=>{cT+=7;cW+=m[5].reduce((s,v)=>s+v,0);}));
    const pct=Math.round(cW/cT*100),r=48,circ=2*Math.PI*r,dash=Math.round(circ*pct/100);
    const empCount=co.depts.reduce((s,d)=>s+d.members.length,0)+1;
    return`<div class="pie-item">
      <div class="donut-wrap">
        <svg width="120" height="120" viewBox="0 0 120 120">
          <circle cx="60" cy="60" r="${r}" fill="none" stroke="#f5f5f4" stroke-width="12"/>
          <circle cx="60" cy="60" r="${r}" fill="none" stroke="${co.color}" stroke-width="12"
            stroke-dasharray="${dash} ${Math.round(circ)}" stroke-linecap="round"/>
        </svg>
        <div class="donut-center">
          <div class="donut-pct" style="color:${co.color}">${pct}%</div>
          <div class="donut-lbl">เฉลี่ย/สัปดาห์</div>
        </div>
      </div>
      <div class="pie-co-name">${co.name}</div>
      <div class="pie-co-sub">${empCount} คน · ${co.depts.length} แผนก</div>
    </div>`;
  }).join('');
}

function updateFooterStats(){
  let t=0,d=0;
  COMPANIES.forEach(co=>{t+=co.depts.reduce((s,dept)=>s+dept.members.length,0)+1;d+=co.depts.length;});
  document.getElementById('f-total-emp').textContent=t;
  document.getElementById('f-total-dept').textContent=d;
}

/* ── MODAL ── */
function openProfile(p){
  if(typeof p==='string')p=JSON.parse(p);
  document.getElementById('m-img').src=p.photo||'';
  document.getElementById('m-co').textContent=p.co||'';
  document.getElementById('m-name').textContent=p.name||'';
  document.getElementById('m-role').textContent=p.role||'';
  document.getElementById('m-dept').textContent='แผนก: '+(p.dept||'');
  document.getElementById('m-phone').textContent=p.phone||'';
  document.getElementById('m-eid').textContent=p.eid||'';
  document.getElementById('m-cid').textContent=p.cid||'';
  document.getElementById('m-skills').innerHTML=(p.skills||[]).map((s,i)=>`<span class="sk${i%3===0?' filled':''}">${s}</span>`).join('');
  document.getElementById('overlay').classList.add('open');
}
function closeModal(e){
  if(!e||e.target===document.getElementById('overlay'))
    document.getElementById('overlay').classList.remove('open');
}

/* ── INIT ── */
buildOrg();
</script>
</body>
</html>