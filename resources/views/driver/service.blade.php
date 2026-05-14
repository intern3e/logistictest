{{-- resources/views/driver/service.blade.php --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ระบบจัดการเซอร์วิสรถ</title>

{{-- Same fonts as oil page --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ═══════════════════════════════════════════════════════════════
   DESIGN TOKENS (เดียวกับหน้า oil)
═══════════════════════════════════════════════════════════════ */
:root{
  --bg:#f8fafc;
  --surface:#ffffff;
  --surface2:#f1f5f9;
  --border:#e2e8f0;
  --border-strong:#cbd5e1;
  --text:#0f172a;
  --text2:#475569;
  --text3:#94a3b8;
  --accent:#2563eb;
  --accent-hover:#1d4ed8;
  --green:#16a34a;
  --green-light:#dcfce7;
  --amber:#d97706;
  --red:#dc2626;
  --red-light:#fee2e2;
  --blue-light:#dbeafe;
  --shadow:0 1px 3px rgba(15,23,42,.04),0 1px 2px rgba(15,23,42,.06);
  --shadow-md:0 4px 12px rgba(15,23,42,.08);
  --radius:14px;
  --radius-sm:10px;
  --radius-xs:8px;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'IBM Plex Sans Thai','Inter',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;font-size:14px;-webkit-font-smoothing:antialiased}

/* ═══════ APP LAYOUT ═══════ */
.app{display:flex;min-height:100vh}

/* ═══════ SIDEBAR ═══════ */
.sidebar{
  width:260px;flex-shrink:0;background:var(--surface);border-right:1px solid var(--border);
  display:flex;flex-direction:column;position:sticky;top:0;height:100vh;overflow-y:auto;z-index:200;
}
.sidebar-brand{padding:20px 22px 18px;border-bottom:1px solid var(--border)}
.sidebar-brand .title{font-size:15px;font-weight:700;color:var(--text);letter-spacing:-.01em;display:flex;align-items:center;gap:8px}
.sidebar-brand .title .logo{width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,var(--accent) 0%,#3b82f6 100%);display:flex;align-items:center;justify-content:center;color:#fff;font-size:15px;flex-shrink:0}
.sidebar-brand .sub{font-size:11px;color:var(--text3);margin-top:6px;margin-left:38px}

.sidebar-add-btn{
  margin:14px 16px 6px;display:flex;align-items:center;justify-content:center;gap:7px;
  background:var(--accent);color:#fff;border:none;padding:11px 14px;border-radius:var(--radius-xs);
  font-size:13.5px;font-weight:600;cursor:pointer;font-family:inherit;transition:background .15s;
  width:calc(100% - 32px);box-shadow:0 2px 6px rgba(37,99,235,.2);
}
.sidebar-add-btn:hover{background:var(--accent-hover)}

.sidebar-section{padding:14px 16px 8px}
.sidebar-section .label{font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.08em;padding:0 8px 8px}

.nav-item{
  display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:var(--radius-xs);
  color:var(--text2);font-size:13.5px;font-weight:500;cursor:pointer;text-decoration:none;
  border:none;background:transparent;font-family:inherit;width:100%;text-align:left;
  transition:all .15s;margin-bottom:2px;
}
.nav-item:hover{background:var(--surface2);color:var(--text)}
.nav-item.active{background:rgba(37,99,235,.08);color:var(--accent);font-weight:600}
.nav-item .ic{font-size:15px;width:18px;text-align:center;flex-shrink:0}
.nav-item .badge-dot{margin-left:auto;width:6px;height:6px;border-radius:50%;background:var(--accent)}

.sidebar-footer{margin-top:auto;padding:12px 22px 18px;border-top:1px solid var(--border);font-size:11px;color:var(--text3)}
.sidebar-footer .live-time{display:flex;align-items:center;gap:6px;font-weight:500;color:var(--text2)}
.sidebar-footer .live-time::before{content:'';width:6px;height:6px;border-radius:50%;background:var(--green);animation:pulse 1.5s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.45}}

.content-wrap{flex:1;min-width:0}

/* Mobile sidebar */
.sidebar-toggle{display:none;position:fixed;top:14px;left:14px;z-index:250;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-xs);width:40px;height:40px;align-items:center;justify-content:center;font-size:18px;cursor:pointer;box-shadow:var(--shadow)}
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.4);z-index:190}
@media(max-width:900px){
  .sidebar{position:fixed;left:-280px;top:0;height:100vh;transition:left .25s ease}
  .sidebar.open{left:0;box-shadow:0 4px 20px rgba(0,0,0,.18)}
  .sidebar-toggle{display:flex}
  .sidebar-overlay.show{display:block}
  .content-wrap{padding-top:60px}
}

/* ═══════ MAIN ═══════ */
.main{padding:24px 28px;max-width:1600px;margin:0 auto}

.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px}
.page-title{font-size:22px;font-weight:700;color:var(--text);letter-spacing:-.01em}
.page-subtitle{font-size:13px;color:var(--text3);margin-top:4px}

/* ═══════ BUTTONS ═══════ */
.btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:var(--radius-xs);font-family:inherit;font-size:13.5px;font-weight:600;cursor:pointer;border:none;transition:all .15s}
.btn-primary{background:var(--accent);color:#fff}
.btn-primary:hover{background:var(--accent-hover)}
.btn-outline{background:transparent;color:var(--text2);border:1px solid var(--border)}
.btn-outline:hover{background:var(--surface2);color:var(--text)}
.btn:disabled{opacity:.55;cursor:not-allowed}

/* ═══════ METRICS — เหมือนหน้า oil ═══════ */
.metrics{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:20px}
.metric-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px 20px;position:relative;overflow:hidden;box-shadow:var(--shadow)}
.metric-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.metric-card.green::before{background:var(--green)}
.metric-card.blue::before{background:var(--accent)}
.metric-card.amber::before{background:#eab308}
.metric-card.navy::before{background:#1e3a8a}
.metric-label{font-size:13px;color:var(--text2);font-weight:500;margin-bottom:8px}
.metric-row{display:flex;align-items:baseline;gap:6px}
.metric-value{font-size:30px;font-weight:700;color:var(--text);letter-spacing:-.02em;line-height:1;font-family:'Inter','IBM Plex Sans Thai',sans-serif}
.metric-unit{font-size:13px;color:var(--text2);font-weight:500}

/* ═══════ FILTER BAR ═══════ */
.filter-bar{
  display:flex;align-items:center;gap:10px;flex-wrap:wrap;
  background:var(--surface);padding:14px 18px;border-radius:var(--radius);
  border:1px solid var(--border);margin-bottom:18px;box-shadow:var(--shadow);
}
.filter-bar select,.filter-bar input{
  font-family:inherit;font-size:13px;padding:8px 12px;border:1px solid var(--border);
  border-radius:var(--radius-xs);background:var(--surface);color:var(--text);outline:none;height:38px;
  transition:border-color .15s, box-shadow .15s;
}
.filter-bar select:focus,.filter-bar input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(37,99,235,.1)}
.srch-wrap{position:relative;display:flex;align-items:center;flex:1;min-width:240px;max-width:380px}
.srch-wrap .si{position:absolute;left:12px;font-size:13px;color:var(--text3);pointer-events:none}
.srch-wrap input{padding-left:34px!important;width:100%}

/* ═══════ TABLE PANEL ═══════ */
.panel{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);margin-bottom:18px}
.panel-header{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--border);gap:12px;flex-wrap:wrap}
.panel-title{font-size:15px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:10px}
.count-badge{
  background:var(--surface2);color:var(--text2);font-size:12px;font-weight:600;
  padding:2px 9px;border-radius:10px;border:1px solid var(--border);
  font-family:'Inter',sans-serif;
}
.table-wrap{overflow-x:auto;max-height:640px;overflow-y:auto}
.table-wrap thead th{position:sticky;top:0;background:var(--surface);z-index:2}
table{width:100%;border-collapse:collapse;font-size:13px}
thead th{
  padding:11px 16px;text-align:left;font-weight:600;font-size:11.5px;color:var(--text3);
  border-bottom:1px solid var(--border);white-space:nowrap;text-transform:uppercase;letter-spacing:.04em;
}
tbody td{padding:11px 16px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:var(--surface2)}
.row-idx{color:var(--text3);font-family:'Inter',sans-serif;font-weight:600;font-size:12px}
.driver-name{font-weight:600;color:var(--text);font-size:13.5px}
.driver-plate{font-size:11px;color:var(--text3);font-family:'Inter',monospace;margin-top:2px;letter-spacing:.02em}

/* Service type badge — flat color tag */
.svc-badge{
  display:inline-flex;align-items:center;gap:4px;padding:3px 10px;font-size:12px;font-weight:600;
  white-space:nowrap;border-radius:6px;line-height:1.5;
}
.svc-oil{background:#fef3c7;color:#92400e}
.svc-tire{background:#e0e7ff;color:#3730a3}
.svc-brake{background:#fee2e2;color:#991b1b}
.svc-engine{background:#fce7f3;color:#9d174d}
.svc-ac{background:#dbeafe;color:#1e40af}
.svc-battery{background:#dcfce7;color:#166534}
.svc-wash{background:#cffafe;color:#155e75}
.svc-glass{background:#f3e8ff;color:#6b21a8}
.svc-light{background:#ffedd5;color:#9a3412}
.svc-other{background:var(--surface2);color:var(--text2)}

.cost-cell{font-weight:700;color:var(--text);font-family:'Inter',sans-serif;text-align:right}

/* image thumbs */
.img-thumbs{display:flex;gap:4px;align-items:center;flex-wrap:wrap}
.img-thumb{width:38px;height:38px;border-radius:6px;object-fit:cover;border:1px solid var(--border);cursor:pointer;transition:transform .15s}
.img-thumb:hover{transform:scale(1.12);z-index:2;box-shadow:0 2px 8px rgba(0,0,0,.15)}
.no-img{width:38px;height:38px;border-radius:6px;background:var(--surface2);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:16px;color:var(--text3)}
.more-badge{font-size:10px;font-weight:700;color:var(--text2);background:var(--surface2);border:1px solid var(--border);border-radius:5px;padding:3px 6px;font-family:'Inter',sans-serif}

/* action buttons */
.action-btns{display:flex;gap:6px}
.action-btn{width:30px;height:30px;border-radius:7px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;transition:all .15s}
.action-btn.edit{background:rgba(37,99,235,.08);color:var(--accent)}
.action-btn.edit:hover{background:var(--accent);color:#fff}
.action-btn.del{background:var(--red-light);color:var(--red)}
.action-btn.del:hover{background:var(--red);color:#fff}

/* ═══════ MODAL ═══════ */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:500;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(2px)}
.modal-overlay.open{display:flex}
.modal{background:var(--surface);border-radius:var(--radius);width:100%;max-width:620px;max-height:92vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(15,23,42,.25);animation:modalIn .2s ease}
@keyframes modalIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
.modal-header{padding:18px 22px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
.modal-title{font-size:16px;font-weight:700;color:var(--text)}
.modal-close{width:32px;height:32px;border-radius:8px;border:none;background:var(--surface2);color:var(--text2);cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;transition:all .15s}
.modal-close:hover{background:var(--border);color:var(--text)}
.modal-body{padding:18px 22px;overflow-y:auto;flex:1}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;flex-shrink:0}

/* ═══════ FORM ═══════ */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-grid .full{grid-column:1/-1}
.form-label{display:block;font-size:11.5px;font-weight:700;color:var(--text2);margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
.form-control{
  width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--radius-xs);
  font-family:inherit;font-size:14px;color:var(--text);background:var(--surface);outline:none;
  transition:border-color .15s, box-shadow .15s;
}
.form-control:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(37,99,235,.1)}
textarea.form-control{resize:vertical;min-height:64px}

/* ═══════ IMAGE UPLOAD ═══════ */
.img-upload-wrap{
  border:2px dashed var(--border);border-radius:var(--radius-xs);padding:24px;text-align:center;
  cursor:pointer;transition:all .2s;background:var(--surface2);position:relative;
}
.img-upload-wrap:hover{border-color:var(--accent);background:rgba(37,99,235,.04)}
.img-upload-wrap input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.preview-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:8px;margin-top:10px}
.preview-item{position:relative;aspect-ratio:1;border-radius:8px;overflow:hidden;border:2px solid var(--border);transition:border-color .15s}
.preview-item:hover{border-color:var(--accent)}
.preview-item img{width:100%;height:100%;object-fit:cover;cursor:pointer}
.preview-item .rm{position:absolute;top:3px;right:3px;width:20px;height:20px;background:rgba(220,38,38,.95);border:none;border-radius:50%;color:#fff;font-size:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1}
.preview-item.existing{border-color:var(--green)}

/* ═══════ LIGHTBOX ═══════ */
.lightbox{display:none;position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:900;align-items:center;justify-content:center;flex-direction:column}
.lightbox.open{display:flex}
.lightbox img{max-width:90vw;max-height:85vh;border-radius:8px;object-fit:contain;box-shadow:0 8px 40px rgba(0,0,0,.4)}
.lb-close{position:absolute;top:16px;right:20px;color:#fff;font-size:18px;cursor:pointer;background:rgba(255,255,255,.12);border:none;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center}
.lb-caption{color:rgba(255,255,255,.7);font-size:12px;margin-top:12px}

/* ═══════ EMPTY ═══════ */
.empty-state{text-align:center;padding:60px 20px;color:var(--text3)}
.empty-state .icon{font-size:42px;margin-bottom:8px;opacity:.4}
.empty-state p{font-size:13px;color:var(--text3)}

/* ═══════ TOAST ═══════ */
.toast{position:fixed;bottom:24px;right:24px;background:var(--text);color:#fff;padding:13px 20px;border-radius:10px;font-size:13.5px;font-weight:500;z-index:999;opacity:0;transform:translateY(10px);transition:all .25s;pointer-events:none;box-shadow:0 8px 24px rgba(15,23,42,.2)}
.toast.show{opacity:1;transform:translateY(0)}

@media(max-width:640px){.form-grid{grid-template-columns:1fr}.main{padding:14px}}
</style>
</head>
<body>

{{-- Mobile sidebar toggle --}}
<button type="button" class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
<div class="sidebar-overlay" id="sbOverlay" onclick="toggleSidebar()"></div>

<div class="app">

{{-- ═══════════════════════════════════════════════════════════════
     SIDEBAR (เหมือนหน้า oil)
═══════════════════════════════════════════════════════════════ --}}
<aside class="sidebar" id="appSidebar">
  <div class="sidebar-brand">
    <div class="title">
      <div class="logo">🛠️</div>
      <div>ระบบจัดการเซอร์วิส</div>
    </div>
    <div class="sub">บันทึกและติดตามประวัติการซ่อมบำรุง</div>
  </div>

  {{-- Add button --}}
  <button class="sidebar-add-btn" onclick="openSvcModal()">
    <span style="font-size:15px">＋</span> เพิ่มข้อมูลเซอร์วิส
  </button>

  {{-- Main menu --}}
  <div class="sidebar-section">
    <div class="label">เมนูหลัก</div>
    <a class="nav-item" href="{{ route('oil') }}">
      <span class="ic">⛽</span>
      <span>ติดตามน้ำมัน</span>
    </a>
    <a class="nav-item active" href="#">
      <span class="ic">🛠️</span>
      <span>จัดการเซอร์วิส</span>
      <span class="badge-dot"></span>
    </a>
    <a class="nav-item" href="{{ url('/SOlist') }}" style="display: flex; align-items: center; padding: 10px 15px; text-decoration: none; color: inherit;">
      <span class="ic" style="margin-right: 12px; display: flex; align-items: center;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
          <polyline points="10 17 15 12 10 7"></polyline>
          <line x1="15" y1="12" x2="3" y2="12"></line>
        </svg>
      </span>
      <span style="font-weight: 500;">(SO List)</span>
    </a>
  </div>

  {{-- Footer --}}
  <div class="sidebar-footer">
    <div class="live-time">อัปเดต <span id="navDate">—</span> น.</div>
    <div style="margin-top:4px">© Service Manager</div>
  </div>
</aside>

<div class="content-wrap">

<div class="main">

  {{-- Page header --}}
  <div class="page-header">
    <div>
      <div class="page-title">ระบบจัดการเซอร์วิสรถ</div>
      <div class="page-subtitle">บันทึกและติดตามประวัติการซ่อมบำรุง</div>
    </div>
    <button class="btn btn-primary" onclick="openSvcModal()">＋ เพิ่มข้อมูลเซอร์วิส</button>
  </div>

  {{-- Metrics --}}
  <div class="metrics">
    <div class="metric-card blue">
      <div class="metric-label">รายการทั้งหมด</div>
      <div class="metric-row"><div class="metric-value" id="mTotal">—</div><div class="metric-unit">รายการ</div></div>
    </div>
    <div class="metric-card green">
      <div class="metric-label">ค่าใช้จ่ายรวม</div>
      <div class="metric-row"><div class="metric-value" id="mCost">—</div><div class="metric-unit">บาท</div></div>
    </div>
    <div class="metric-card amber">
      <div class="metric-label">เฉลี่ย/ครั้ง</div>
      <div class="metric-row"><div class="metric-value" id="mAvg">—</div><div class="metric-unit">บาท</div></div>
    </div>
    <div class="metric-card navy">
      <div class="metric-label">รถที่ซ่อม</div>
      <div class="metric-row"><div class="metric-value" id="mCars">—</div><div class="metric-unit">คัน</div></div>
    </div>
  </div>

  {{-- Filter bar --}}
  <div class="filter-bar">
    <div class="srch-wrap">
      <span class="si">🔍</span>
      <input type="text" id="svcSearch" placeholder="ค้นหาทะเบียน / คนขับ..." oninput="debounceLoad()">
    </div>
    <select id="typeFilter" onchange="loadRecords()">
      <option value="">ประเภทงานทั้งหมด</option>
      <option value="เปลี่ยนถ่ายน้ำมันเครื่อง">เปลี่ยนถ่ายน้ำมันเครื่อง</option>
      <option value="เปลี่ยนยาง">เปลี่ยนยาง</option>
      <option value="ตรวจ/เปลี่ยนเบรก">ตรวจ/เปลี่ยนเบรก</option>
      <option value="ซ่อมเครื่องยนต์">ซ่อมเครื่องยนต์</option>
      <option value="ล้าง/ซ่อมแอร์">ล้าง/ซ่อมแอร์</option>
      <option value="เปลี่ยนแบตเตอรี่">เปลี่ยนแบตเตอรี่</option>
      <option value="ล้างรถ">ล้างรถ</option>
      <option value="ซ่อม/เปลี่ยนกระจก">ซ่อม/เปลี่ยนกระจก</option>
      <option value="ซ่อม/เปลี่ยนไฟ">ซ่อม/เปลี่ยนไฟ</option>
      <option value="อื่นๆ">อื่นๆ</option>
    </select>
  </div>

  {{-- Table panel --}}
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">
        ประวัติการเซอร์วิส
        <span class="count-badge" id="tblCount">0</span>
      </div>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:48px">#</th>
            <th>วันที่</th>
            <th>คนขับ / ทะเบียน</th>
            <th>ประเภทงาน</th>
            <th>รายละเอียด</th>
            <th style="text-align:right">ค่าใช้จ่าย (฿)</th>
            <th>รูปภาพ</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="svcTbody">
          <tr><td colspan="8"><div class="empty-state"><div class="icon">⏳</div><p>กำลังโหลด...</p></div></td></tr>
        </tbody>
      </table>
    </div>
  </div>

</div>{{-- /.main --}}
</div>{{-- /.content-wrap --}}
</div>{{-- /.app --}}

<!-- LIGHTBOX -->
<div class="lightbox" id="lightbox" onclick="closeLb()">
  <button class="lb-close" onclick="closeLb()">✕</button>
  <img id="lbImg" src="" alt="" onclick="event.stopPropagation()">
  <div class="lb-caption" id="lbCaption"></div>
</div>

<!-- SERVICE MODAL -->
<div class="modal-overlay" id="svcModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="svcModalTitle">🛠️ เพิ่มข้อมูลเซอร์วิส</div>
      <button type="button" class="modal-close" onclick="closeSvcModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div>
          <label class="form-label">วันที่ *</label>
          <input type="date" id="sv-date" class="form-control">
        </div>
        <div>
          <label class="form-label">คนขับ *</label>
          <select id="sv-driver" class="form-control" onchange="toggleOther('sv-driver','sv-driver-other')">
            <option value="">— เลือกคนขับ —</option>
            <option value="บังเดช">บังเดช</option>
            <option value="แชม">แชม</option>
            <option value="กอล์ฟ">กอล์ฟ</option>
            <option value="หรั่ง">หรั่ง</option>
            <option value="เก่ง">เก่ง</option>
            <option value="เอ">เอ</option>
            <option value="ยุทร">ยุทร</option>
            <option value="แฟรงค์">แฟรงค์</option>
            <option value="เอ้">เอ้</option>
            <option value="__other__">อื่นๆ (พิมพ์เอง)</option>
          </select>
          <input type="text" id="sv-driver-other" class="form-control" style="margin-top:6px;display:none" placeholder="ระบุชื่อคนขับ">
        </div>
        <div>
          <label class="form-label">ทะเบียนรถ *</label>
          <select id="sv-plate" class="form-control" onchange="toggleOther('sv-plate','sv-plate-other')">
            <option value="">— เลือกทะเบียน —</option>
            <option value="1 ฉผ 1276">1 ฉผ 1276</option>
            <option value="1 ฉผ 3181">1 ฉผ 3181</option>
            <option value="1ฉผ213">1ฉผ213</option>
            <option value="2 ฉธ 1620">2 ฉธ 1620</option>
            <option value="2ฉธ1619">2ฉธ1619</option>
            <option value="3ฉมก6071">3ฉมก6071</option>
            <option value="3ฉมง3059">3ฉมง3059</option>
            <option value="805">805</option>
            <option value="City 8กค6309">City 8กค6309</option>
            <option value="City 9 กค4815">City 9 กค4815</option>
            <option value="แจ๊ส 9กธ4830">แจ๊ส 9กธ4830</option>
            <option value="__other__">อื่นๆ (พิมพ์เอง)</option>
          </select>
          <input type="text" id="sv-plate-other" class="form-control" style="margin-top:6px;display:none" placeholder="ระบุทะเบียน">
        </div>
        <div>
          <label class="form-label">ประเภทงาน *</label>
          <select id="sv-type" class="form-control">
            <option value="">— เลือกประเภท —</option>
            <option value="เปลี่ยนถ่ายน้ำมันเครื่อง">เปลี่ยนถ่ายน้ำมันเครื่อง</option>
            <option value="เปลี่ยนยาง">เปลี่ยนยาง</option>
            <option value="ตรวจ/เปลี่ยนเบรก">ตรวจ/เปลี่ยนเบรก</option>
            <option value="ซ่อมเครื่องยนต์">ซ่อมเครื่องยนต์</option>
            <option value="ล้าง/ซ่อมแอร์">ล้าง/ซ่อมแอร์</option>
            <option value="เปลี่ยนแบตเตอรี่">เปลี่ยนแบตเตอรี่</option>
            <option value="ล้างรถ">ล้างรถ</option>
            <option value="ซ่อม/เปลี่ยนกระจก">ซ่อม/เปลี่ยนกระจก</option>
            <option value="ซ่อม/เปลี่ยนไฟ">ซ่อม/เปลี่ยนไฟ</option>
            <option value="อื่นๆ">อื่นๆ</option>
          </select>
        </div>
        <div>
          <label class="form-label">ค่าใช้จ่าย (฿)</label>
          <input type="number" id="sv-cost" class="form-control" step="0.01" min="0" placeholder="0.00">
        </div>
        <div class="full">
          <label class="form-label">รายละเอียด</label>
          <textarea id="sv-detail" class="form-control" rows="2" placeholder="รายละเอียดงานซ่อม..."></textarea>
        </div>
        <div class="full">
          <label class="form-label">รูปภาพ (เลือกได้หลายรูป)</label>
          <div class="img-upload-wrap">
            <input type="file" accept="image/*" multiple onchange="addNewImages(this)" id="imgInput">
            <div style="font-size:30px;margin-bottom:8px">📷</div>
            <div style="font-size:13.5px;color:var(--text2);font-weight:600">คลิกหรือลากรูปมาวางที่นี่</div>
            <div style="font-size:11.5px;color:var(--text3);margin-top:4px">JPG, PNG, WEBP (ขนาดไม่เกิน 5MB ต่อรูป)</div>
          </div>
          <div class="preview-grid" id="previewGrid"></div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline" onclick="closeSvcModal()">ยกเลิก</button>
      <button type="button" class="btn btn-primary" id="saveBtn" onclick="saveSvc()">💾 บันทึก</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

/* ── Laravel route URLs ── */
const LIST_URL    = "{{ url('/service/list') }}";
const STORE_URL   = "{{ url('/service') }}";
const UPDATE_BASE = "{{ url('/service') }}";

const TYPE_CSS = {
  'เปลี่ยนถ่ายน้ำมันเครื่อง':'svc-oil','เปลี่ยนยาง':'svc-tire',
  'ตรวจ/เปลี่ยนเบรก':'svc-brake','ซ่อมเครื่องยนต์':'svc-engine',
  'ล้าง/ซ่อมแอร์':'svc-ac','เปลี่ยนแบตเตอรี่':'svc-battery',
  'ล้างรถ':'svc-wash','ซ่อม/เปลี่ยนกระจก':'svc-glass',
  'ซ่อม/เปลี่ยนไฟ':'svc-light','อื่นๆ':'svc-other'
};

/* state */
let editId       = null;
let existingImgs = [];
let newFiles     = [];

/* ── SIDEBAR ── */
function toggleSidebar(){
  const sb = document.getElementById('appSidebar');
  const ov = document.getElementById('sbOverlay');
  if(!sb) return;
  const isOpen = sb.classList.toggle('open');
  ov.classList.toggle('show', isOpen);
}

/* ── NAV TIME ── */
function updateNavDate(){
  const now = new Date();
  const time = now.toLocaleTimeString('th-TH',{hour:'2-digit',minute:'2-digit',hour12:false});
  const el = document.getElementById('navDate'); if(el) el.textContent = time;
}

/* ── TOAST ── */
function showToast(msg, dur=2400){
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), dur);
}

/* ── HELPERS ── */
function toggleOther(selId, otherId){
  const sel = document.getElementById(selId);
  const oth = document.getElementById(otherId);
  oth.style.display = sel.value === '__other__' ? 'block' : 'none';
  if(sel.value === '__other__') oth.focus();
}

function getVal(selId, otherId){
  const sel = document.getElementById(selId);
  if(sel.value === '__other__') return document.getElementById(otherId).value.trim();
  return sel.value;
}

function todayStr(){
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}

function setSelectOrOther(selId, otherId, val){
  const sel = document.getElementById(selId);
  const oth = document.getElementById(otherId);
  const opt = Array.from(sel.options).find(o => o.value === val);
  if(opt){ sel.value = val; oth.style.display = 'none'; }
  else   { sel.value = '__other__'; oth.style.display = 'block'; oth.value = val; }
}

/* ── LOAD ── */
let debTimer = null;
function debounceLoad(){ clearTimeout(debTimer); debTimer = setTimeout(loadRecords, 350); }

async function loadRecords(){
  const q      = document.getElementById('svcSearch').value;
  const type   = document.getElementById('typeFilter').value;
  const params = new URLSearchParams({q, type});

  try {
    const res  = await fetch(`${LIST_URL}?${params}`);
    const data = await res.json();
    renderMetrics(data.metrics);
    renderTable(data.records);
  } catch(e){
    document.getElementById('svcTbody').innerHTML =
      `<tr><td colspan="8"><div class="empty-state"><div class="icon">⚠️</div><p>โหลดข้อมูลไม่สำเร็จ</p></div></td></tr>`;
  }
}

function renderMetrics(m){
  document.getElementById('mTotal').textContent = m.total.toLocaleString();
  document.getElementById('mCost').textContent  = '฿' + Number(m.totalCost).toLocaleString();
  document.getElementById('mAvg').textContent   = '฿' + Number(m.avg).toLocaleString();
  document.getElementById('mCars').textContent  = m.cars.toLocaleString();
}

function renderTable(records){
  document.getElementById('tblCount').textContent = records.length;
  const tbody = document.getElementById('svcTbody');
  if(!records.length){
    tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><div class="icon">🛠️</div><p>ไม่พบรายการ</p></div></td></tr>`;
    return;
  }
  tbody.innerHTML = records.map((r, fi) => {
    const typeCss   = TYPE_CSS[r.type] || 'svc-other';
    const urls      = r.image_urls || [];

    let imgHtml = '';
    if(urls.length){
      const show = urls.slice(0, 3);
      imgHtml = `<div class="img-thumbs">` +
        show.map((u, si) => `<img class="img-thumb" src="${u}" onclick="openLb('${u}','รูปที่ ${si+1}')">`).join('') +
        (urls.length > 3 ? `<span class="more-badge">+${urls.length - 3}</span>` : '') +
        `</div>`;
    } else {
      imgHtml = `<div class="no-img">📷</div>`;
    }

    return `<tr>
      <td class="row-idx">${String(fi+1).padStart(2,'0')}</td>
      <td style="font-size:12.5px;white-space:nowrap;color:var(--text2)">${r.date}</td>
      <td>
        <div class="driver-name">${r.driver}</div>
        <div class="driver-plate">${r.plate}</div>
      </td>
      <td><span class="svc-badge ${typeCss}">${r.type}</span></td>
      <td style="font-size:12.5px;color:var(--text2);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${r.detail||''}">${r.detail||'—'}</td>
      <td class="cost-cell">${r.cost ? '฿'+Number(r.cost).toLocaleString() : '—'}</td>
      <td>${imgHtml}</td>
      <td><div class="action-btns">
        <button class="action-btn edit" onclick="openSvcModal(${JSON.stringify(r).split('"').join('&quot;')})" title="แก้ไข">✏️</button>
        <button class="action-btn del"  onclick="deleteSvc(${r.id})" title="ลบ">🗑️</button>
      </div></td>
    </tr>`;
  }).join('');
}

/* ── IMAGE HANDLING ── */
function addNewImages(input){
  Array.from(input.files).forEach(file => {
    if(file.size > 5*1024*1024){ alert(`${file.name} ใหญ่เกิน 5MB`); return; }
    newFiles.push(file);
  });
  input.value = '';
  renderPreviewGrid();
}

function removeExisting(idx){
  existingImgs.splice(idx, 1);
  renderPreviewGrid();
}

function removeNew(idx){
  newFiles.splice(idx, 1);
  renderPreviewGrid();
}

function renderPreviewGrid(){
  const grid = document.getElementById('previewGrid');
  const existHtml = existingImgs.map((img, i) => `
    <div class="preview-item existing" title="รูปเดิม">
      <img src="${img.url}" onclick="openLb('${img.url}','รูปเดิม')">
      <button class="rm" onclick="removeExisting(${i})">✕</button>
    </div>`).join('');

  const newHtml = newFiles.map((f, i) => {
    const url = URL.createObjectURL(f);
    return `<div class="preview-item">
      <img src="${url}" onclick="openLb('${url}','${f.name}')">
      <button class="rm" onclick="removeNew(${i})">✕</button>
    </div>`;
  }).join('');

  grid.innerHTML = existHtml + newHtml;
}

/* ── MODAL ── */
function openSvcModal(record = null){
  editId       = null;
  existingImgs = [];
  newFiles     = [];
  renderPreviewGrid();

  if(record){
    editId = record.id;
    document.getElementById('svcModalTitle').textContent = '🛠️ แก้ไขข้อมูลเซอร์วิส';
    document.getElementById('sv-date').value   = record.date;
    document.getElementById('sv-type').value   = record.type;
    document.getElementById('sv-cost').value   = record.cost;
    document.getElementById('sv-detail').value = record.detail || '';
    setSelectOrOther('sv-driver','sv-driver-other', record.driver);
    setSelectOrOther('sv-plate', 'sv-plate-other',  record.plate);

    const paths = record.images || [];
    const urls  = record.image_urls || [];
    existingImgs = paths.map((p, i) => ({ path: p, url: urls[i] || '' }));
    renderPreviewGrid();
  } else {
    document.getElementById('svcModalTitle').textContent = '🛠️ เพิ่มข้อมูลเซอร์วิส';
    document.getElementById('sv-date').value   = todayStr();
    document.getElementById('sv-driver').value = '';
    document.getElementById('sv-plate').value  = '';
    document.getElementById('sv-type').value   = '';
    document.getElementById('sv-cost').value   = '';
    document.getElementById('sv-detail').value = '';
    ['sv-driver-other','sv-plate-other'].forEach(id => {
      const el = document.getElementById(id); el.style.display = 'none'; el.value = '';
    });
  }
  document.getElementById('svcModal').classList.add('open');
}

function closeSvcModal(){ document.getElementById('svcModal').classList.remove('open'); }

async function saveSvc(){
  const date   = document.getElementById('sv-date').value;
  const driver = getVal('sv-driver','sv-driver-other');
  const plate  = getVal('sv-plate','sv-plate-other');
  const type   = document.getElementById('sv-type').value;
  const cost   = document.getElementById('sv-cost').value;
  const detail = document.getElementById('sv-detail').value;

  if(!date)  { alert('กรุณาเลือกวันที่'); return; }
  if(!driver){ alert('กรุณาเลือกหรือระบุชื่อคนขับ'); return; }
  if(!plate) { alert('กรุณาเลือกหรือระบุทะเบียนรถ'); return; }
  if(!type)  { alert('กรุณาเลือกประเภทงาน'); return; }

  const btn = document.getElementById('saveBtn');
  btn.disabled = true; btn.textContent = '⏳ กำลังบันทึก...';

  const fd = new FormData();
  fd.append('date',   date);
  fd.append('driver', driver);
  fd.append('plate',  plate);
  fd.append('type',   type);
  fd.append('cost',   cost || 0);
  fd.append('status', 'เสร็จแล้ว');
  fd.append('detail', detail);
  newFiles.forEach(f => fd.append('images[]', f));

  let url    = STORE_URL;
  let method = 'POST';

  if(editId){
    url    = `${UPDATE_BASE}/${editId}`;
    method = 'POST';
    fd.append('_method', 'POST');
    fd.append('keep_images', JSON.stringify(existingImgs.map(i => i.path)));
  }

  try {
    const res  = await fetch(url, { method, headers:{'X-CSRF-TOKEN': CSRF, 'Accept':'application/json'}, body: fd });

    // Read response as text first, then try to parse JSON (server may return HTML error page)
    const responseText = await res.text();
    let data;
    try {
      data = JSON.parse(responseText);
    } catch(parseErr){
      console.error('=== Server response (not JSON) ===');
      console.error('URL:', url);
      console.error('Status:', res.status, res.statusText);
      console.error('Body:', responseText.substring(0, 500));
      alert(`Server ตอบกลับไม่ใช่ JSON\n\nURL: ${url}\nStatus: ${res.status} ${res.statusText}\n\nคำตอบ: ${responseText.substring(0, 200)}`);
      return;
    }

    if(!res.ok){
      console.error('HTTP error:', res.status, data);
      alert(`HTTP ${res.status}: ${data.message || JSON.stringify(data.errors || data)}`);
      return;
    }

    if(data.success){
      closeSvcModal();
      showToast(editId ? '✅ แก้ไขสำเร็จ' : '✅ บันทึกสำเร็จ');
      loadRecords();
    } else {
      alert('เกิดข้อผิดพลาด: ' + JSON.stringify(data.errors || data));
    }
  } catch(e){
    console.error('=== Network/Fetch error ===');
    console.error('URL:', url);
    console.error('Error:', e);
    alert(`ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้\n\nURL: ${url}\nError: ${e.message}\n\n(เปิด Console (F12) เพื่อดูรายละเอียด)`);
  } finally {
    btn.disabled = false; btn.textContent = '💾 บันทึก';
  }
}

/* ── DELETE ── */
async function deleteSvc(id){
  if(!confirm('ยืนยันการลบรายการนี้?')) return;
  try {
    const res  = await fetch(`${UPDATE_BASE}/${id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type':'application/json' }
    });
    const data = await res.json();
    if(data.success){ showToast('🗑️ ลบสำเร็จ'); loadRecords(); }
  } catch(e){ alert('ลบไม่สำเร็จ'); }
}

/* ── LIGHTBOX ── */
function openLb(src, cap){
  document.getElementById('lbImg').src = src;
  document.getElementById('lbCaption').textContent = cap || '';
  document.getElementById('lightbox').classList.add('open');
}
function closeLb(){ document.getElementById('lightbox').classList.remove('open'); }
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeLb(); });

/* ── INIT ── */
document.addEventListener('DOMContentLoaded', () => {
  updateNavDate();
  setInterval(updateNavDate, 60000);
  loadRecords();
});
</script>
</body>
</html>