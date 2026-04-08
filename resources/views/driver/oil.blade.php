{{-- resources/views/wrongitem/oil.blade.php --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ระบบติดตามน้ำมันรถ</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<style>
  
  
:root{--navy:#1a3a6b;--navy-light:#243258;--navy-dark:#122956;--accent:#4f8ef7;--accent2:#38c98a;--accent3:#f5a623;--accent4:#e85d5d;--bg:#f0f2f7;--surface:#fff;--surface2:#f8f9fc;--border:#e2e6f0;--text:#1a2744;--text2:#6b7a99;--text3:#9aa3bc;--shadow:0 2px 12px rgba(26,39,68,.08);--shadow-md:0 4px 24px rgba(26,39,68,.12);--radius:12px;--radius-sm:8px}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Sarabun',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}
.navbar{background:var(--navy);padding:0 28px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;box-shadow:0 2px 16px rgba(0,0,0,.18)}
.navbar-brand{display:flex;align-items:center;gap:10px;color:#fff;font-weight:600;font-size:16px}
.navbar-brand .icon{width:36px;height:36px;background:rgba(255,255,255,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px}
.navbar-brand .sub{font-size:11px;font-weight:300;opacity:.65;letter-spacing:1px}
.nav-date{color:rgba(255,255,255,.75);font-size:13px}
.nav-user{display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border-radius:20px;padding:5px 14px 5px 6px}
.nav-avatar{width:28px;height:28px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:12px;color:#fff}
.nav-user span{color:#fff;font-size:13px;font-weight:500}
.layout{display:flex;min-height:calc(100vh - 60px)}
.sidebar{width:220px;background:var(--navy-dark);flex-shrink:0;padding:20px 0}
.sidebar-menu{list-style:none}
.sidebar-menu a{display:flex;align-items:center;gap:10px;padding:11px 22px;color:rgba(255,255,255,.6);text-decoration:none;font-size:14px;transition:all .2s;border-left:3px solid transparent}
.sidebar-menu a:hover{color:rgba(255,255,255,.9);background:rgba(255,255,255,.05)}
.sidebar-menu a.active{color:#fff;background:rgba(79,142,247,.15);border-left-color:var(--accent);font-weight:500}
.sidebar-menu .icon{font-size:16px;width:20px;text-align:center}
.sidebar-section{font-size:10px;font-weight:600;letter-spacing:1.5px;color:rgba(255,255,255,.3);padding:20px 22px 6px;text-transform:uppercase}
.main{flex:1;overflow-x:hidden;padding:28px}
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.page-title{font-size:22px;font-weight:600;color:var(--navy)}
.page-subtitle{font-size:13px;color:var(--text2);margin-top:2px}
.btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:var(--radius-sm);font-family:'Sarabun',sans-serif;font-size:14px;font-weight:500;cursor:pointer;border:none;transition:all .2s;text-decoration:none}
.btn-primary{background:var(--accent);color:#fff}.btn-primary:hover{background:#3a7ce0}
.btn-outline{background:transparent;color:var(--text2);border:1px solid var(--border)}.btn-outline:hover{background:var(--surface2)}
.btn-danger{background:var(--accent4);color:#fff}.btn-danger:hover{background:#c94040}
.btn-sm{padding:5px 12px;font-size:12px}
.filter-bar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;background:var(--surface);padding:14px 18px;border-radius:var(--radius);border:1px solid var(--border);margin-bottom:20px;box-shadow:var(--shadow)}
.filter-bar select,.filter-bar input[type=month],.filter-bar input[type=date]{font-family:'Sarabun',sans-serif;font-size:13px;padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);outline:none;height:36px}
.view-tabs{display:flex;background:var(--surface2);border-radius:var(--radius-sm);padding:3px;gap:2px;border:1px solid var(--border)}
.view-tab{padding:6px 14px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;color:var(--text2);border:none;background:transparent;font-family:'Sarabun',sans-serif;transition:all .15s;white-space:nowrap;text-decoration:none}
.view-tab.active{background:var(--surface);color:var(--accent);box-shadow:0 1px 4px rgba(0,0,0,.08)}
.metrics{display:grid;grid-template-columns:repeat(auto-fit,minmax(155px,1fr));gap:14px;margin-bottom:22px}
.metric-card{background:var(--surface);border-radius:var(--radius);padding:18px 20px;border:1px solid var(--border);box-shadow:var(--shadow);position:relative;overflow:hidden}
.metric-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.metric-card.blue::before{background:var(--accent)}.metric-card.green::before{background:var(--accent2)}.metric-card.amber::before{background:var(--accent3)}.metric-card.navy::before{background:var(--navy)}
.metric-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;margin-bottom:12px}
.metric-icon.blue{background:rgba(79,142,247,.1)}.metric-icon.green{background:rgba(56,201,138,.1)}.metric-icon.amber{background:rgba(245,166,35,.12)}.metric-icon.navy{background:rgba(26,39,68,.08)}
.metric-label{font-size:12px;color:var(--text2);font-weight:500;margin-bottom:4px}
.metric-value{font-size:26px;font-weight:700;color:var(--text);line-height:1}
.metric-sub{font-size:11px;color:var(--text3);margin-top:4px}
.charts-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px}
@media(max-width:768px){.charts-grid{grid-template-columns:1fr}}
.chart-card{background:var(--surface);border-radius:var(--radius);padding:20px;border:1px solid var(--border);box-shadow:var(--shadow)}
.chart-card.full{grid-column:1/-1}
.chart-card-title{font-size:14px;font-weight:600;color:var(--text);margin-bottom:4px}
.chart-card-sub{font-size:12px;color:var(--text2);margin-bottom:16px}
.table-card{background:var(--surface);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;margin-bottom:20px}
.table-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border);gap:12px;flex-wrap:wrap}
.table-title{font-size:15px;font-weight:600;color:var(--text)}
.badge-count{background:var(--accent);color:#fff;font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px;margin-left:8px}
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
thead th{background:var(--surface2);padding:10px 14px;text-align:left;font-weight:600;font-size:12px;color:var(--text2);border-bottom:1px solid var(--border);white-space:nowrap}
tbody td{padding:11px 14px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:var(--surface2)}
.badge{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px}
.badge-green{background:rgba(56,201,138,.12);color:#1a7a4d}.badge-amber{background:rgba(245,166,35,.12);color:#a06a00}.badge-blue{background:rgba(79,142,247,.12);color:#1a52b0}
.km-val{font-weight:700;color:var(--accent2)}
.action-btns{display:flex;gap:6px}
.action-btn{width:28px;height:28px;border-radius:6px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;transition:all .15s}
.action-btn.edit{background:rgba(79,142,247,.1);color:var(--accent)}.action-btn.edit:hover{background:var(--accent);color:#fff}
.action-btn.del{background:rgba(232,93,93,.1);color:var(--accent4)}.action-btn.del:hover{background:var(--accent4);color:#fff}
.plate-tag{background:var(--surface2);border:1px solid var(--border);border-radius:4px;font-size:11px;color:var(--text2);padding:1px 6px;font-family:monospace;font-weight:600}

/* ======= MODAL BASE ======= */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(10,18,40,.55);z-index:500;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(2px)}
.modal-overlay.open{display:flex}
.modal{background:var(--surface);border-radius:16px;width:100%;max-width:640px;max-height:92vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalIn .2s ease}
@keyframes modalIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
.modal-header{padding:20px 24px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.modal-title{font-size:17px;font-weight:600;color:var(--text)}
.modal-close{width:32px;height:32px;border-radius:8px;border:none;background:var(--surface2);color:var(--text2);cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center}
.modal-body{padding:20px 24px}
.modal-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px}

/* ======= STEP INDICATOR ======= */
.step-indicator{display:flex;align-items:center;gap:0;margin-bottom:22px}
.step-item{display:flex;align-items:center;gap:8px;flex:1}
.step-circle{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;transition:all .3s}
.step-circle.active{background:var(--accent);color:#fff;box-shadow:0 0 0 4px rgba(79,142,247,.2)}
.step-circle.done{background:var(--accent2);color:#fff}
.step-circle.inactive{background:var(--border);color:var(--text3)}
.step-label{font-size:12px;font-weight:600;transition:color .3s}
.step-label.active{color:var(--accent)}
.step-label.done{color:var(--accent2)}
.step-label.inactive{color:var(--text3)}
.step-line{flex:1;height:2px;margin:0 8px;border-radius:2px;transition:background .3s}
.step-line.done{background:var(--accent2)}
.step-line.inactive{background:var(--border)}

/* ======= DRIVER CARD STEP 1 ======= */
.driver-card-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.driver-card-grid .full{grid-column:1/-1}
.driver-banner{background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);border-radius:var(--radius-sm);padding:16px 18px;margin-bottom:18px;display:flex;align-items:center;gap:14px;color:#fff}
.driver-avatar{width:48px;height:48px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
.driver-banner-name{font-size:18px;font-weight:700;line-height:1}
.driver-banner-plate{font-size:12px;opacity:.7;margin-top:4px;font-family:monospace;letter-spacing:.5px}
.time-row{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:8px}
.time-arrow{font-size:18px;color:var(--text3);text-align:center;padding-top:22px}

/* ======= FORM ======= */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-grid .full{grid-column:1/-1}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px}
.form-control{width:100%;padding:10px 13px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:'Sarabun',sans-serif;font-size:14px;color:var(--text);background:var(--surface);outline:none;transition:border-color .2s}
.form-control:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(79,142,247,.12)}
.form-control.is-invalid{border-color:var(--accent4)}
textarea.form-control{resize:vertical;min-height:70px}
.invalid-feedback{font-size:11px;color:var(--accent4);margin-top:4px}
.form-hint{font-size:11px;color:var(--text3);margin-top:4px}
.section-divider{font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:1px;padding:4px 0 8px;border-bottom:1px solid var(--border);margin:16px 0 12px;grid-column:1/-1}

/* ======= OIL PRICE BANNER ======= */
.oil-price-banner{background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);border-radius:var(--radius-sm);padding:12px 16px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;color:#fff}
.live-dot{width:7px;height:7px;border-radius:50%;background:var(--accent2);animation:pulse 1.5s infinite}
.live-dot.loading{background:var(--accent3)}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.calc-box{background:rgba(56,201,138,.06);border:1px solid rgba(56,201,138,.25);border-radius:var(--radius-sm);padding:14px 16px;display:none;margin-top:12px}
.calc-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}
.calc-item .lbl{font-size:11px;color:var(--text3)}
.calc-item .val{font-size:20px;font-weight:700;color:var(--accent2)}
.calc-item .val.amber{color:var(--accent3)}
.calc-item .unit{font-size:11px;color:var(--text3)}
.mileage-info{background:rgba(79,142,247,.06);border:1px solid rgba(79,142,247,.2);border-radius:var(--radius-sm);padding:10px 14px;margin-top:6px;font-size:12px;color:var(--text2);display:none}
.mileage-info strong{color:var(--accent)}
.alert{padding:12px 16px;border-radius:var(--radius-sm);margin-bottom:16px;font-size:14px}
.alert-success{background:rgba(56,201,138,.1);border:1px solid rgba(56,201,138,.3);color:#1a7a4d}
.alert-error{background:rgba(232,93,93,.1);border:1px solid rgba(232,93,93,.3);color:#c0392b}
.empty-state{text-align:center;padding:60px 20px;color:var(--text3)}
.empty-state .icon{font-size:48px;margin-bottom:12px;opacity:.4}

/* ======= STEP PANEL TRANSITIONS ======= */
.step-panel{display:none;animation:fadeSlide .25s ease}
.step-panel.active{display:block}
@keyframes fadeSlide{from{opacity:0;transform:translateX(12px)}to{opacity:1;transform:translateX(0)}}

/* ======= SUMMARY ROW (between steps) ======= */
.summary-row{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px}
.summary-chip{background:var(--surface2);border:1px solid var(--border);border-radius:20px;padding:5px 12px;font-size:12px;color:var(--text2);display:flex;align-items:center;gap:5px;font-weight:500}
.summary-chip strong{color:var(--navy)}

@media(max-width:640px){.form-grid{grid-template-columns:1fr}.form-grid .full{grid-column:1}.driver-card-grid{grid-template-columns:1fr}.sidebar{display:none}.main{padding:16px}.metrics{grid-template-columns:1fr 1fr}.time-row{grid-template-columns:1fr auto 1fr}}
</style>
</head>
<body>

<nav class="navbar">
  <div class="navbar-brand">
    <div class="icon">⛽</div>
    <div><div>ระบบจัดการเซอร์วิส</div><div class="sub">SERVICE MANAGEMENT SYSTEM</div></div>
  </div>
  <div style="display:flex;align-items:center;gap:16px">
    <div class="nav-date" id="navDate"></div>
    <div class="nav-user"><div class="nav-avatar">A</div><span>Admin</span></div>
  </div>
</nav>

<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-section">หลัก</div>
    <ul class="sidebar-menu">
      <li><a href="{{ route('oil') }}" class="active"><span class="icon">⛽</span>ติดตามน้ำมัน</a></li>
      <li><a href="{{ url('/service') }}"><span class="icon">🛠️</span>Service</a></li>
    </ul>
    <div class="sidebar-section">รายงาน</div>
    <ul class="sidebar-menu">
      <li><a href="#reportSection" onclick="document.getElementById('reportSection').scrollIntoView({behavior:'smooth'})"><span class="icon">📊</span>สรุปรายงาน</a></li>
    </ul>
  </aside>
  <main class="main">

    {{-- Flash message --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- =================== HEADER =================== --}}
    <div class="page-header">
      <div>
        <div class="page-title">ระบบติดตามน้ำมันรถ</div>
        <div class="page-subtitle">บันทึกและวิเคราะห์การใช้น้ำมันแต่ละคนขับ</div>
      </div>
      <button class="btn btn-primary" onclick="openModal()">+ เพิ่มข้อมูลน้ำมัน</button>
    </div>

    {{-- =================== FILTER =================== --}}
    <form method="GET" action="{{ route('oil') }}" id="filterForm">
      <div class="filter-bar">
        <div class="view-tabs">
          @foreach(['day'=>'รายวัน','month'=>'รายเดือน','year'=>'รายปี','all'=>'ทั้งหมด'] as $v=>$label)
          <button type="submit" name="view" value="{{ $v }}"
            class="view-tab {{ $view===$v ? 'active' : '' }}"
            onclick="setViewInput('{{ $v }}')">{{ $label }}</button>
          @endforeach
        </div>
        @if($view === 'day')
        <input type="date" name="date" value="{{ $filterDay }}" onchange="this.form.submit()"
          style="font-family:'Sarabun',sans-serif;font-size:13px;padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);outline:none;height:36px">
        @elseif($view === 'all')
        {{-- ไม่แสดง date filter --}}
        @else
        <input type="month" name="month" value="{{ $filterMonth }}" onchange="this.form.submit()"
          style="font-family:'Sarabun',sans-serif;font-size:13px;padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);outline:none;height:36px">
        @endif

        <select name="driver_name" onchange="this.form.submit()"
          style="font-family:'Sarabun',sans-serif;font-size:13px;padding:7px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);outline:none;height:36px">
          <option value="all" {{ $filterDriver==='all'?'selected':'' }}>คนขับทั้งหมด</option>
          @foreach($drivers as $d)
          <option value="{{ $d }}" {{ $filterDriver===$d?'selected':'' }}>{{ $d }}</option>
          @endforeach
        </select>
      </div>
    </form>

    {{-- =================== METRICS =================== --}}
    @if($metrics)
    <div class="metrics">
      <div class="metric-card blue"><div class="metric-icon blue">⛽</div><div class="metric-label">น้ำมันรวม</div><div class="metric-value">{{ $metrics['total_liters'] }}</div><div class="metric-sub">ลิตร</div></div>
      <div class="metric-card amber"><div class="metric-icon amber">💰</div><div class="metric-label">ค่าน้ำมันรวม</div><div class="metric-value">฿{{ number_format($metrics['total_price']) }}</div><div class="metric-sub">บาท</div></div>
      <div class="metric-card green"><div class="metric-icon green">📈</div><div class="metric-label">เฉลี่ย km/L</div><div class="metric-value">{{ $metrics['avg_km_per_liter'] }}</div><div class="metric-sub">กม./ลิตร</div></div>
      <div class="metric-card navy"><div class="metric-icon navy">⏱</div><div class="metric-label">ชม.ทำงาน</div><div class="metric-value">{{ $metrics['total_work_hours'] }}</div><div class="metric-sub">ชั่วโมง</div></div>
    </div>
    @endif

    {{-- =================== TABLE =================== --}}
    <div class="table-card">
      <div class="table-header">
        <div>
          <span class="table-title">รายการเติมน้ำมัน</span>
          <span class="badge-count">{{ $logs->count() }}</span>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th><th>วันที่ทำงาน</th><th>คนขับ / ทะเบียน</th>
              <th>เวลาทำงาน</th>
              <th>ระยะทาง</th><th>ลิตร</th><th>ค่าน้ำมัน (฿)</th>
              <th>km/L</th><th>บันทึกเมื่อ</th><th>จัดการ</th>
            </tr>
          </thead>
          <tbody>
            @forelse($logs as $i => $r)
            @php
              $kml  = $r['km_per_liter'] ?? 0;
              $dist = ($r['total_distance'] ?? 0) > 0
                  ? number_format($r['total_distance'], 2)
                  : (($r['distance'] ?? 0) > 0 ? number_format($r['distance'], 2) : '—');
              $wh   = $r['work_hours'] ?? 0;
            @endphp
            <tr>
              <td style="color:var(--text3)">{{ $i + 1 }}</td>
              <td>{{ $r['work_date'] }}</td>
              <td>
                <strong style="color:var(--navy)">{{ $r['driver_name'] }}</strong>
                <div><span class="plate-tag">{{ $r['vehicle_id'] ?? '—' }}</span></div>
              </td>
              <td style="font-size:12px;color:var(--text2)">
                {{ $r['start_time'] ?? '—' }} – {{ $r['end_time'] ?? '—' }}
                @if($wh > 0)<div style="font-size:11px;color:var(--accent3);font-weight:600">{{ $wh }} ชม.</div>@endif
              </td>
              <td>{{ $dist }}</td>
              <td>{{ $r['liters'] ? number_format($r['liters'], 2).' ล.' : '—' }}</td>
              <td style="font-weight:600">{{ $r['total_price'] ? '฿'.number_format($r['total_price'], 2) : '—' }}</td>
              <td>
                  @if($kml > 0)
                    <span class="km-val">{{ number_format($kml, 1) }}</span>
                  @else
                    <span style="color:var(--text3);font-size:11px">—</span>
                  @endif
              </td>
              <td style="font-size:11px;color:var(--text3)">{{ $r['created_at'] ?? '—' }}</td>
              <td>
                <div class="action-btns">
                  <button class="action-btn edit" onclick="openModal({{ $r['id'] }})">✏</button>
                  <form method="POST" action="{{ route('oil.destroy', $r['id']) }}"
                    onsubmit="return confirm('ยืนยันการลบรายการนี้?')" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="action-btn del">🗑</button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="11">
                <div class="empty-state"><div class="icon">⛽</div><p>ไม่พบรายการในช่วงนี้</p></div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- =================== CHARTS =================== --}}
    <div class="charts-grid">
      <div class="chart-card">
        <div class="chart-card-title">ค่าน้ำมัน (฿) ต่อคนขับ</div>
        <div class="chart-card-sub">รวม total_price แต่ละคน</div>
        <div style="position:relative;width:100%;height:230px"><canvas id="chartDriver"></canvas></div>
      </div>
      <div class="chart-card">
        <div class="chart-card-title">อัตราสิ้นเปลือง (km/L) ต่อคนขับ</div>
        <div class="chart-card-sub">เฉลี่ย km_per_liter</div>
        <div style="position:relative;width:100%;height:230px"><canvas id="chartKml"></canvas></div>
      </div>
    </div>

  </main>
</div>

{{-- =================== STEP 1 MODAL: ข้อมูลคนขับ =================== --}}
<div class="modal-overlay" id="step1Modal">
  <div class="modal" style="max-width:540px">
    <div class="modal-header">
      <div class="modal-title" id="step1Title">🚗 ข้อมูลคนขับรถ</div>
      <button type="button" class="modal-close" onclick="closeAllModals()">✕</button>
    </div>
    <div class="modal-body">

      {{-- STEP INDICATOR --}}
      <div class="step-indicator">
        <div class="step-item">
          <div class="step-circle active" id="s1circle">1</div>
          <div class="step-label active" id="s1label">ข้อมูลคนขับ</div>
        </div>
        <div class="step-line inactive" id="s1line"></div>
        <div class="step-item">
          <div class="step-circle inactive" id="s2circle">2</div>
          <div class="step-label inactive" id="s2label">ข้อมูลน้ำมัน</div>
        </div>
      </div>

      {{-- Driver banner preview --}}
      <div class="driver-banner" id="driverBanner" style="display:none">
        <div class="driver-avatar">👤</div>
        <div>
          <div class="driver-banner-name" id="bannerName">—</div>
          <div class="driver-banner-plate" id="bannerPlate">ทะเบียน: —</div>
        </div>
      </div>

      <div class="driver-card-grid">

        <div class="full">
          <label class="form-label">วันที่ทำงาน *</label>
          <input type="date" id="s1-work-date" class="form-control"
            value="{{ date('Y-m-d') }}" onchange="updateDriverBanner()">
          <div class="invalid-feedback" id="s1-err-date" style="display:none">กรุณาเลือกวันที่</div>
        </div>

        {{-- ===== DRIVER ===== --}}
        @php
          $driverList = ['บังเดช','แชม','กอล์ฟ','หรั่ง','เก่ง','เอ','ยุทร','แฟรงค์','เอ้'];
          foreach($drivers as $dbD) { if(!in_array($dbD,$driverList)) $driverList[] = $dbD; }
          $plateList = ['1 ฉผ 1276','1 ฉผ 3181','1ฉผ213','1ฉผศ7158','2 ฉธ 1620','2ฉมฎ3017','2ฉธ1619','2ฉธ1621','3ฉมก6071','3ฉมง3059','3ฉมณ6380','3ฉมย478','3ฉมห200','4กย1540','6762','City 8กค6309','City 9 กค4815','แจ๊ส 9กธ4830'];
          foreach($plates as $dbP) { if(!in_array($dbP,$plateList)) $plateList[] = $dbP; }
        @endphp

        <div>
          <label class="form-label">คนขับ *</label>
          <select id="s1-driver-select" class="form-control"
            onchange="onS1SelectOther(this,'s1-driver-name','s1-driver-other'); updateDriverBanner()">
            <option value="">— เลือกคนขับ —</option>
            @foreach($driverList as $d)
            <option value="{{ $d }}">{{ $d }}</option>
            @endforeach
            <option value="__other__">อื่นๆ (พิมพ์เอง)</option>
          </select>
          <input type="hidden" id="s1-driver-name" value="">
          <input type="text" id="s1-driver-other" class="form-control"
            style="margin-top:6px;display:none" placeholder="ระบุชื่อคนขับ"
            oninput="document.getElementById('s1-driver-name').value=this.value; updateDriverBanner()">
          <div class="invalid-feedback" id="s1-err-driver" style="display:none">กรุณาเลือกคนขับ</div>
        </div>

        <div>
          <label class="form-label">ทะเบียนรถ *</label>
          <select id="s1-plate-select" class="form-control"
            onchange="onS1SelectOther(this,'s1-vehicle-id','s1-plate-other'); updateDriverBanner()">
            <option value="">— เลือกทะเบียน —</option>
            @foreach($plateList as $p)
            <option value="{{ $p }}">{{ $p }}</option>
            @endforeach
            <option value="__other__">อื่นๆ (พิมพ์เอง)</option>
          </select>
          <input type="hidden" id="s1-vehicle-id" value="">
          <input type="text" id="s1-plate-other" class="form-control"
            style="margin-top:6px;display:none" placeholder="ระบุทะเบียน"
            oninput="document.getElementById('s1-vehicle-id').value=this.value; updateDriverBanner()">
          <div class="invalid-feedback" id="s1-err-plate" style="display:none">กรุณาเลือกทะเบียนรถ</div>
        </div>

        <div class="full" style="margin-top:4px">
          <label class="form-label">เวลาทำงาน</label>
          <div class="time-row">
            <div>
              <input type="time" id="s1-start-time" class="form-control"
                placeholder="เริ่ม" oninput="updateDriverBanner()">
              <div class="form-hint">เวลาเริ่มขับ</div>
            </div>
            <div class="time-arrow">→</div>
            <div>
              <input type="time" id="s1-end-time" class="form-control"
                placeholder="สิ้นสุด" oninput="updateDriverBanner()">
              <div class="form-hint">เวลาสิ้นสุด</div>
            </div>
          </div>
          {{-- work hours preview --}}
          <div id="s1-wh-preview" style="margin-top:8px;font-size:12px;color:var(--accent3);font-weight:600;display:none">
            ⏱ <span id="s1-wh-val">0</span> ชั่วโมง
          </div>
        </div>
      </div>
    </div>
    <div class="data-table-container" style="margin-top: 25px;">
    <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <h4 style="margin: 0; color: var(--accent3); font-size: 16px;">📋 รายการบันทึกวันนี้</h4>
        <span class="badge" style="background: var(--accent3); color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px;">
            <span id="record-count">0</span> รายการ
        </span>
    </div>

    <div class="table-responsive" style="overflow-x: auto; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <table class="custom-table" style="width: 100%; border-collapse: collapse; min-width: 500px;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #eee;">
                    <th style="padding: 12px; text-align: left; font-size: 14px;">เวลาทำงาน</th>
                    <th style="padding: 12px; text-align: left; font-size: 14px;">คนขับ</th>
                    <th style="padding: 12px; text-align: left; font-size: 14px;">ทะเบียน</th>
                    <th style="padding: 12px; text-align: center; font-size: 14px;">ชั่วโมง</th>
                    <th style="padding: 12px; text-align: center; font-size: 14px;">จัดการ</th>
                    <th style="padding: 12px; text-align: center;">สถานะ</th>
                    <th style="padding: 12px; text-align: center;">หมายเหตุ</th>
                </tr>
            </thead>
            <tbody id="data-table-body">
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #999;">ยังไม่มีข้อมูลบันทึก</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline" onclick="closeAllModals()">ยกเลิก</button>
      <button type="button" class="btn btn-primary" onclick="goToStep2()">
        ถัดไป — เพิ่มข้อมูลน้ำมัน →
      </button>
    </div>
  </div>
</div>

{{-- =================== STEP 2 MODAL: ข้อมูลน้ำมัน =================== --}}
<div class="modal-overlay" id="fuelModal">
  <div class="modal">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:10px">
        <button type="button" onclick="backToStep1()"
          style="width:32px;height:32px;border-radius:8px;border:none;background:var(--surface2);color:var(--text2);cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center"
          id="backBtn">←</button>
        <div class="modal-title" id="modalTitle">เพิ่มข้อมูลเติมน้ำมัน</div>
      </div>
      <button type="button" class="modal-close" onclick="closeAllModals()">✕</button>
    </div>

    <form id="fuelForm" method="POST" action="{{ route('oil.store') }}">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="">

      <div class="modal-body">

        {{-- Validation errors --}}
        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:14px">
          <ul style="list-style:none;padding:0;margin:0">
            @foreach($errors->all() as $err)
            <li>• {{ $err }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        {{-- STEP INDICATOR --}}
        <div class="step-indicator" style="margin-bottom:16px">
          <div class="step-item">
            <div class="step-circle done">✓</div>
            <div class="step-label done">ข้อมูลคนขับ</div>
          </div>
          <div class="step-line done"></div>
          <div class="step-item">
            <div class="step-circle active">2</div>
            <div class="step-label active">ข้อมูลน้ำมัน</div>
          </div>
        </div>
        

        {{-- Summary chips from step 1 --}}
        <div class="summary-row" id="summaryRow" style="display:none">
          <div class="summary-chip">👤 <strong id="chipDriver">—</strong></div>
          <div class="summary-chip">🚗 <strong id="chipPlate">—</strong></div>
          <div class="summary-chip">📅 <strong id="chipDate">—</strong></div>
          <div class="summary-chip" id="chipTimeWrap" style="display:none">
            

            ⏱ <strong id="chipTime">—</strong>
          </div>
        </div>

        {{-- hidden fields transferred from step 1 --}}
        <input type="hidden" name="work_date"    id="f-work-date">
        <input type="hidden" name="driver_name"  id="f-driver-name">
        <input type="hidden" name="vehicle_id"   id="f-vehicle-id">
        <input type="hidden" name="start_time"   id="f-start-time">
        <input type="hidden" name="end_time"     id="f-end-time">

        {{-- Oil Price Banner --}}
        <div class="oil-price-banner">
          <div style="width:100%">
            <div style="font-size:12px;opacity:.7" id="oilPriceLabel">ราคาน้ำมันดีเซล (PTT)</div>
            <div style="display:flex;align-items:baseline;gap:4px;margin:4px 0">
              <span style="font-size:26px;font-weight:700" id="oilPriceShow">—</span>
              <span style="font-size:12px;opacity:.7;margin-left:4px">บาท/ลิตร</span>
              <div style="margin-left:auto;display:flex;align-items:center;gap:6px">
                <div class="live-dot loading" id="liveDot"></div>
                <span style="font-size:12px;opacity:.7" id="liveLabel">กำลังดึง</span>
              </div>
            </div>
            <div style="font-size:12px;opacity:.6;font-style:italic;margin-bottom:10px" id="oilPriceStatus">กำลังโหลด...</div>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
              @php
                $oilBtns = [
                  'diesel'    => '⛽ ดีเซล',
                  '95'        => '⛽ 95',
                  'benzin95'  => '⛽ เบนซิน 95',
                  '91'        => '⛽ 91',
                  'e20'       => '⛽ E20',
                  'e85'       => '⛽ E85',
                ];
              @endphp
              @foreach($oilBtns as $oilKey => $oilLabel)
              <button type="button"
                onclick="switchOilType('{{ $oilKey }}')"
                id="btnOil-{{ $oilKey }}"
                class="oil-btn"
                style="font-family:'Sarabun',sans-serif;font-size:11px;font-weight:600;padding:5px 11px;border-radius:14px;cursor:pointer;border:2px solid {{ $oilKey==='diesel' ? '#fff' : 'transparent' }};background:{{ $oilKey==='diesel' ? 'rgba(255,255,255,.3)' : 'rgba(255,255,255,.1)' }};color:{{ $oilKey==='diesel' ? '#fff' : 'rgba(255,255,255,.7)' }}">
                {{ $oilLabel }}
              </button>
              @endforeach
              <button type="button" onclick="refreshOilPrice()" id="btnRefreshOil"
                style="font-family:'Sarabun',sans-serif;font-size:11px;font-weight:600;padding:5px 11px;border-radius:14px;border:2px solid rgba(255,255,255,.3);background:rgba(255,255,255,.05);color:rgba(255,255,255,.8);cursor:pointer">
                🔄
              </button>
            </div>
          </div>
        </div>

        <div class="form-grid">
          <div class="section-divider">⛽ ข้อมูลน้ำมัน</div>

          <div>
            <label class="form-label">จำนวนลิตร *</label>
            <input type="number" name="liters" id="f-liters" class="form-control {{ $errors->has('liters') ? 'is-invalid' : '' }}"
              step="0.01"
              value="{{ old('liters', $editLog?->liters) }}"
              oninput="calcPreview()" readonly>
            @error('liters')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div>
            <label class="form-label">ราคาต่อลิตร *</label>
            <input type="number" name="price_per_liter" id="f-price-per-liter"
              class="form-control" step="0.01"
              oninput="calcPreview()" readonly>
          </div>

          <div class="full">
            <label class="form-label">ค่าน้ำมัน (฿)</label>
            <input type="number" name="total_price" id="f-total-price" class="form-control {{ $errors->has('total_price') ? 'is-invalid' : '' }}"
              step="0.01"
              value="{{ old('total_price', $editLog?->total_price) }}"
              oninput="calcPreview()">
            @error('total_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="full">
            <label class="form-label">ระยะทางทั้งหมด (km)</label>
            <input type="number" name="total_distance" id="f-total-distance"
              class="form-control"
              value="{{ old('total_distance', $editLog?->total_distance) }}"
              oninput="calcPreview()">
          </div>

          <div class="full">
            <label class="form-label">หมายเหตุ</label>
            <textarea name="note" id="f-note" class="form-control">{{ old('note', $editLog?->note) }}</textarea>
          </div>
        </div>

        {{-- Preview box --}}
        <div class="calc-box" id="calcBox">
          <div style="font-size:12px;color:var(--text2);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px">📊 Preview</div>
          <div class="calc-grid">
            <div class="calc-item"><div class="lbl">work_hours</div><div class="val amber" id="calcWorkHours">—</div><div class="unit">ชั่วโมง</div></div>
            <div class="calc-item"><div class="lbl">liters / total_price</div><div class="val" id="calcLitersPreview">—</div><div class="unit">ล. / ฿</div></div>
            <div class="calc-item"><div class="lbl">km_per_liter (ประมาณ)</div><div class="val" id="calcKml">—</div><div class="unit">กม./ลิตร</div></div>
          </div>
          <div style="margin-top:8px;font-size:11px;color:var(--text3)">⚠️ distance / km_per_liter จริงคำนวณโดย Model PHP</div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="backToStep1()" id="backBtnFooter">← ย้อนกลับ</button>
        <button type="submit" class="btn btn-primary">💾 บันทึกข้อมูล</button>
      </div>
    </form>
  </div>
</div>
<script>
// เก็บข้อมูลราคาน้ำมันที่ดึงมาได้
let currentOilPrices = {};
let selectedOilType = 'diesel';

// 1. ฟังก์ชันดึงราคาน้ำมัน (ตัวอย่างการต่อ API หรือจำลองข้อมูล)
async function refreshOilPrice() {
    const liveDot = document.getElementById('liveDot');
    const liveLabel = document.getElementById('liveLabel');
    const statusText = document.getElementById('oilPriceStatus');
    
    liveDot.className = 'live-dot loading';
    liveLabel.innerText = 'กำลังดึง...';

    try {
        // ในสถานการณ์จริง คุณอาจจะ fetch จาก API ของคุณเองที่ไปดึงค่าจาก PTT อีกที
        // ตัวอย่างจำลองข้อมูล:
        const mockData = {
            diesel: 32.94,
            95: 38.55,
            benzin95: 46.44,
            91: 37.18,
            e20: 35.44,
            e85: 33.69
        };
        
        // จำลอง delay 800ms
        await new Promise(resolve => setTimeout(resolve, 800));
        
        currentOilPrices = mockData;
        updateOilUI();
        
        liveDot.className = 'live-dot';
        liveLabel.innerText = 'Live';
        statusText.innerText = 'อัปเดตล่าสุด: ' + new Date().toLocaleTimeString();
    } catch (error) {
        liveLabel.innerText = 'ผิดพลาด';
        statusText.innerText = 'ไม่สามารถดึงข้อมูลได้';
    }
}

// 2. เปลี่ยนประเภทน้ำมันและอัปเดตราคาในช่อง Input
function switchOilType(type) {
    selectedOilType = type;
    
    // อัปเดต UI ปุ่ม
    const buttons = document.querySelectorAll('.oil-btn');
    buttons.forEach(btn => {
        if (btn.id === `btnOil-${type}`) {
            btn.style.border = '2px solid #fff';
            btn.style.background = 'rgba(255,255,255,.3)';
            btn.style.color = '#fff';
        } else {
            btn.style.border = '2px solid transparent';
            btn.style.background = 'rgba(255,255,255,.1)';
            btn.style.color = 'rgba(255,255,255,.7)';
        }
    });

    updateOilUI();
}

function updateOilUI() {
    const price = currentOilPrices[selectedOilType] || 0;
    document.getElementById('oilPriceShow').innerText = price.toFixed(2);
    
    // ใส่ค่าลงในช่อง "ราคาต่อลิตร"
    const priceInput = document.getElementById('f-price-per-liter');
    priceInput.value = price.toFixed(2);
    
    // ล้างสถานะ readonly ชั่วคราวเพื่อให้คำนวณได้ง่ายขึ้น (หรือตั้งค่าไว้ตามต้องการ)
    calcPreview();
}

// 3. ระบบคำนวณอัตโนมัติ (Logic การคำนวณลิตร และ Preview)
function calcPreview() {
    const pricePerLiter = parseFloat(document.getElementById('f-price-per-liter').value) || 0;
    const totalPrice = parseFloat(document.getElementById('f-total-price').value) || 0;
    const totalDistance = parseFloat(document.getElementById('f-total-distance').value) || 0;
    const litersInput = document.getElementById('f-liters');

    // คำนวณจำนวนลิตรจาก (ราคารวม / ราคาต่อลิตร)
    let liters = 0;
    if (pricePerLiter > 0 && totalPrice > 0) {
        liters = totalPrice / pricePerLiter;
        litersInput.value = liters.toFixed(2);
    }

    // อัปเดต Preview Box
    document.getElementById('calcLitersPreview').innerText = `${liters.toFixed(2)} / ${totalPrice.toLocaleString()}`;
    
    // คำนวณ กม./ลิตร (Preview)
    const kmlElement = document.getElementById('calcKml');
    if (totalDistance > 0 && liters > 0) {
        const kml = totalDistance / liters;
        kmlElement.innerText = kml.toFixed(2);
    } else {
        kmlElement.innerText = '—';
    }

    // คำนวณชั่วโมงทำงาน (ดึงมาจาก Step 1 ถ้ามีข้อมูล)
    const startTime = document.getElementById('f-start-time').value;
    const endTime = document.getElementById('f-end-time').value;
    if (startTime && endTime) {
        const start = new Date(`2000-01-01 ${startTime}`);
        const end = new Date(`2000-01-01 ${endTime}`);
        let diff = (end - start) / (1000 * 60 * 60); // แปลงเป็นชั่วโมง
        if (diff < 0) diff += 24; // กรณีเลิกงานหลังเที่ยงคืน
        document.getElementById('calcWorkHours').innerText = diff.toFixed(1);
    }
}

// 4. ฟังก์ชันย้อนกลับ
function backToStep1() {
    // ใส่ Logic การเปลี่ยนการแสดงผล Modal กลับไปหน้าแรกของคุณที่นี่
    // เช่น: $('#step2').hide(); $('#step1').show();
    console.log("ย้อนกลับไปแก้ไขข้อมูลคนขับ");
}

// รันครั้งแรกเมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', () => {
    refreshOilPrice();
});
</script>

{{-- =================== SCRIPT =================== --}}
<script>
  
const COLORS        = ['#4f8ef7','#38c98a','#f5a623','#e85d5d','#a855f7','#06b6d4','#f59e0b','#10b981','#ef4444'];
const ROUTE_STORE   = '{{ route("oil.store") }}';
const ROUTE_UPDATE  = id => `{{ url("/oil/update") }}/${id}`;
const ROUTE_PREVMILE = '{{ route("oil.prevMileage") }}';
const CSRF          = document.querySelector('meta[name=csrf-token]').content;
let oilPrices       = {};
let currentOilType  = 'diesel';
let isEditMode      = false;
let editId          = null;
function toggleStatus(btn) {
    const isPending = btn.classList.contains('pending');
    
    if (isPending) {
        // เปลี่ยนเป็นสำเร็จ
        btn.classList.remove('pending');
        btn.classList.add('success');
        btn.innerHTML = '✅ สำเร็จ';
        btn.style.background = '#d4edda';
        btn.style.color = '#155724';
        btn.style.borderColor = '#c3e6cb';
    } else {
        // เปลี่ยนกลับเป็นรอดำเนินการ
        btn.classList.remove('success');
        btn.classList.add('pending');
        btn.innerHTML = '⏳ รอดำเนินการ';
        btn.style.background = '#fff3cd';
        btn.style.color = '#856404';
        btn.style.borderColor = '#ffeeba';
    }
}


// ======= NAV DATE =======
function updateNavDate() {
    const now=new Date(),days=['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัส','ศุกร์','เสาร์'],
    months=['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
    const el=document.getElementById('navDate');
    if(el) el.textContent=`วัน${days[now.getDay()]}ที่ ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()+543}`;
}

// ======= FILTER VIEW =======
function setViewInput(v) {
    const el = document.getElementById('viewInput');
    if (el) el.value = v;
}

// =============================================
// STEP 1 HELPERS
// =============================================
function onS1SelectOther(selectEl, hiddenId, textId) {
    const val    = selectEl.value;
    const textEl = document.getElementById(textId);
    const hidden = document.getElementById(hiddenId);
    if (val === '__other__') {
        textEl.style.display = 'block';
        textEl.focus();
        hidden.value = textEl.value;
    } else {
        textEl.style.display = 'none';
        textEl.value = '';
        hidden.value = val;
    }
}

function setS1SelectOther(selectId, hiddenId, textId, value) {
    const sel    = document.getElementById(selectId);
    const hidden = document.getElementById(hiddenId);
    const text   = document.getElementById(textId);
    if (!sel || !hidden || !text) return;
    hidden.value = value ?? '';
    const exists = Array.from(sel.options).some(o => o.value === value && o.value !== '__other__');
    if (exists) {
        sel.value          = value;
        text.style.display = 'none';
        text.value         = '';
    } else if (value) {
        sel.value          = '__other__';
        text.style.display = 'block';
        text.value         = value;
    } else {
        sel.value          = '';
        text.style.display = 'none';
        text.value         = '';
    }
}

// Update driver banner in step 1
function updateDriverBanner() {
    const driverName = document.getElementById('s1-driver-name').value ||
                       document.getElementById('s1-driver-select').value;
    const plate      = document.getElementById('s1-vehicle-id').value ||
                       document.getElementById('s1-plate-select').value;
    const startT     = document.getElementById('s1-start-time').value;
    const endT       = document.getElementById('s1-end-time').value;

    const banner = document.getElementById('driverBanner');
    if (driverName && driverName !== '__other__' && plate && plate !== '__other__') {
        document.getElementById('bannerName').textContent  = driverName;
        document.getElementById('bannerPlate').textContent = 'ทะเบียน: ' + plate;
        banner.style.display = 'flex';
    } else {
        banner.style.display = 'none';
    }

    // work hours preview
    const whPrev = document.getElementById('s1-wh-preview');
    if (startT && endT) {
        const [sh, sm] = startT.split(':').map(Number);
        const [eh, em] = endT.split(':').map(Number);
        const diff = (eh * 60 + em) - (sh * 60 + sm);
        if (diff > 0) {
            document.getElementById('s1-wh-val').textContent = (diff / 60).toFixed(2);
            whPrev.style.display = 'block';
            return;
        }
    }
    whPrev.style.display = 'none';
}

// Validate step 1 & go to step 2
function goToStep2() {
    let ok = true;

    const date   = document.getElementById('s1-work-date').value;
    const driver = document.getElementById('s1-driver-name').value;
    const plate  = document.getElementById('s1-vehicle-id').value;

    // clear errors
    ['s1-err-date','s1-err-driver','s1-err-plate'].forEach(id => {
        document.getElementById(id).style.display = 'none';
    });
    ['s1-work-date','s1-driver-select','s1-plate-select'].forEach(id => {
        document.getElementById(id).classList.remove('is-invalid');
    });

    if (!date) {
        document.getElementById('s1-err-date').style.display = 'block';
        document.getElementById('s1-work-date').classList.add('is-invalid');
        ok = false;
    }
    if (!driver || driver === '__other__') {
        document.getElementById('s1-err-driver').style.display = 'block';
        document.getElementById('s1-driver-select').classList.add('is-invalid');
        ok = false;
    }
    if (!plate || plate === '__other__') {
        document.getElementById('s1-err-plate').style.display = 'block';
        document.getElementById('s1-plate-select').classList.add('is-invalid');
        ok = false;
    }
    if (!ok) return;

    // Transfer values to step 2 hidden fields
    document.getElementById('f-work-date').value   = date;
    document.getElementById('f-driver-name').value = driver;
    document.getElementById('f-vehicle-id').value  = plate;
    document.getElementById('f-start-time').value  = document.getElementById('s1-start-time').value;
    document.getElementById('f-end-time').value    = document.getElementById('s1-end-time').value;

    // Update summary chips
    document.getElementById('chipDriver').textContent = driver;
    document.getElementById('chipPlate').textContent  = plate;
    document.getElementById('chipDate').textContent   = date;

    const st = document.getElementById('s1-start-time').value;
    const et = document.getElementById('s1-end-time').value;
    if (st && et) {
        document.getElementById('chipTime').textContent      = st + ' – ' + et;
        document.getElementById('chipTimeWrap').style.display = 'flex';
    } else {
        document.getElementById('chipTimeWrap').style.display = 'none';
    }
    document.getElementById('summaryRow').style.display = 'flex';

    // Close step 1, open step 2
    document.getElementById('step1Modal').classList.remove('open');
    document.getElementById('fuelModal').classList.add('open');

    // Load oil price
    loadOilPrice('diesel');
    calcPreview();

    // Fetch prev mileage
    fetchPrevMileage(plate, date);
}

function backToStep1() {
    document.getElementById('fuelModal').classList.remove('open');
    document.getElementById('step1Modal').classList.add('open');
}

// =============================================
// OPEN / CLOSE MODALS
// =============================================
function openModal(id = null) {
    isEditMode = !!id;
    editId     = id;

    if (id) {
        // EDIT — skip step 1, open step 2 directly
        const allLogs = @json($logs);
        const r = allLogs.find(l => l.id === id);
        if (!r) return;

        document.getElementById('modalTitle').textContent = 'แก้ไขข้อมูลเติมน้ำมัน';
        document.getElementById('fuelForm').action       = ROUTE_UPDATE(id);
        document.getElementById('formMethod').value      = 'PUT';

        // Populate step 2 form
        setF('f-work-date',       r.work_date);
        setF('f-driver-name',     r.driver_name);
        setF('f-vehicle-id',      r.vehicle_id);
        setF('f-start-time',      r.start_time);
        setF('f-end-time',        r.end_time);
        setF('f-liters',          r.liters);
        setF('f-total-distance',  r.total_distance);
        setF('f-total-price',     r.total_price);
        setF('f-note',            r.note);
        setF('f-price-per-liter', r.price_per_liter);

        // Show summary chips
        document.getElementById('chipDriver').textContent = r.driver_name ?? '—';
        document.getElementById('chipPlate').textContent  = r.vehicle_id  ?? '—';
        document.getElementById('chipDate').textContent   = r.work_date   ?? '—';
        if (r.start_time && r.end_time) {
            document.getElementById('chipTime').textContent      = r.start_time + ' – ' + r.end_time;
            document.getElementById('chipTimeWrap').style.display = 'flex';
        } else {
            document.getElementById('chipTimeWrap').style.display = 'none';
        }
        document.getElementById('summaryRow').style.display = 'flex';

        // Hide back button in edit mode
        document.getElementById('backBtn').style.display      = 'none';
        document.getElementById('backBtnFooter').style.display = 'none';

        document.getElementById('fuelModal').classList.add('open');
        loadOilPrice('diesel');
        fetchPrevMileage(r.vehicle_id, r.work_date, id);
        calcPreview();

    } else {
        // CREATE — show step 1 first
        document.getElementById('modalTitle').textContent    = 'เพิ่มข้อมูลเติมน้ำมัน';
        document.getElementById('fuelForm').action           = ROUTE_STORE;
        document.getElementById('formMethod').value          = '';
        document.getElementById('backBtn').style.display     = '';
        document.getElementById('backBtnFooter').style.display = '';

        // Reset step 1 fields
        document.getElementById('s1-work-date').value   = new Date().toISOString().slice(0,10);
        document.getElementById('s1-driver-select').value = '';
        document.getElementById('s1-driver-name').value   = '';
        document.getElementById('s1-driver-other').style.display = 'none';
        document.getElementById('s1-driver-other').value  = '';
        document.getElementById('s1-plate-select').value  = '';
        document.getElementById('s1-vehicle-id').value    = '';
        document.getElementById('s1-plate-other').style.display = 'none';
        document.getElementById('s1-plate-other').value   = '';
        document.getElementById('s1-start-time').value    = '';
        document.getElementById('s1-end-time').value      = '';
        document.getElementById('driverBanner').style.display = 'none';
        document.getElementById('s1-wh-preview').style.display = 'none';

        // Reset step 2 fields
        ['f-liters','f-total-price','f-total-distance','f-note','f-price-per-liter'].forEach(i => setF(i, ''));
        document.getElementById('calcBox').style.display = 'none';

        // Clear validation errors
        ['s1-err-date','s1-err-driver','s1-err-plate'].forEach(id => {
            document.getElementById(id).style.display = 'none';
        });
        ['s1-work-date','s1-driver-select','s1-plate-select'].forEach(id => {
            document.getElementById(id).classList.remove('is-invalid');
        });

        document.getElementById('step1Modal').classList.add('open');
    }
}

function closeAllModals() {
    document.getElementById('step1Modal').classList.remove('open');
    document.getElementById('fuelModal').classList.remove('open');
}

function setF(id, v) { const el=document.getElementById(id); if(el) el.value=v??''; }

// ======= PREV MILEAGE =======
async function fetchPrevMileage(vehicleId, workDate, excludeId = null) {
    try {
        const p = new URLSearchParams({ vehicle_id: vehicleId, work_date: workDate });
        if (excludeId) p.set('exclude_id', excludeId);
        const r = await fetch(`${ROUTE_PREVMILE}?${p}`, { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} });
        const d = await r.json();
        const el = document.getElementById('prevMileageInfo');
        if (el && d.data) {
            document.getElementById('prevMileageText').innerHTML =
                `🔖 Log ก่อนหน้า (${d.data.work_date}): เลขไมล์ <strong>${Number(d.data.start_mileage).toLocaleString()}</strong> กม.`;
            el.style.display = 'block';
        } else if (el) {
            el.style.display = 'none';
        }
    } catch(_) {}
    calcPreview();
}

// ======= CALC PREVIEW =======
function calcPreview() {
    const startT = document.getElementById('f-start-time')?.value ?? '';
    const endT   = document.getElementById('f-end-time')?.value   ?? '';
    const ppl    = parseFloat(document.getElementById('f-price-per-liter')?.value) || 0;
    const tp     = parseFloat(document.getElementById('f-total-price')?.value)     || 0;

    if (tp > 0 && ppl > 0) {
        setF('f-liters', (tp / ppl).toFixed(2));
    }

    const liters = parseFloat(document.getElementById('f-liters')?.value) || 0;
    if (!tp && liters > 0 && ppl > 0) setF('f-total-price', (liters * ppl).toFixed(2));

    let wh = 0;
    if (startT && endT) {
        const [sh,sm]=startT.split(':').map(Number),[eh,em]=endT.split(':').map(Number);
        const d=(eh*60+em)-(sh*60+sm); if(d>0) wh=d/60;
    }
    const show = wh>0||liters>0;
    document.getElementById('calcBox').style.display = show ? 'block' : 'none';
    if (show) {
        document.getElementById('calcWorkHours').textContent     = wh>0     ? wh.toFixed(2) : '—';
        document.getElementById('calcLitersPreview').textContent = liters>0 ? `${liters} / ฿${(tp||liters*ppl).toFixed(0)}` : '—';
        const dist   = parseFloat(document.getElementById('f-total-distance')?.value) || 0;
        const kmlEst = liters > 0 && dist > 0 ? dist / liters : 0;
        document.getElementById('calcKml').textContent = kmlEst > 0 ? kmlEst.toFixed(2) : '—';
    }
}

// ======= OIL PRICE =======
function switchOilType(type) {
    currentOilType = type;
    document.querySelectorAll('.oil-btn').forEach(b => {
        b.style.background  = 'rgba(255,255,255,.1)';
        b.style.borderColor = 'transparent';
        b.style.color       = 'rgba(255,255,255,.7)';
    });
    const active = document.getElementById('btnOil-' + type);
    if (active) {
        active.style.background  = 'rgba(255,255,255,.3)';
        active.style.borderColor = '#fff';
        active.style.color       = '#fff';
    }
    loadOilPrice(type);
}
async function refreshOilPrice() {
    const btn=document.getElementById('btnRefreshOil'); btn.disabled=true; btn.style.opacity='.5';
    await loadOilPrice(currentOilType);
    btn.disabled=false; btn.style.opacity='1';
}
async function loadOilPrice(type) {
    const config = {
        'diesel':   { label:'ดีเซล',          pttKey:'premium_diesel', matchName:'ดีเซล'          },
        '95':       { label:'แก๊สโซฮอล์ 95',  pttKey:'gasohol_95',     matchName:'แก๊สโซฮอล์ 95'  },
        'benzin95': { label:'เบนซิน 95',       pttKey:'gasoline_95',    matchName:'เบนซิน 95'      },
        '91':       { label:'แก๊สโซฮอล์ 91',  pttKey:'gasohol_91',     matchName:'แก๊สโซฮอล์ 91'  },
        'e20':      { label:'แก๊สโซฮอล์ E20', pttKey:'gasohol_e20',    matchName:'E20'             },
        'e85':      { label:'แก๊สโซฮอล์ E85', pttKey:'gasohol_e85',    matchName:'E85'             },
    };
    const cfg = config[type] ?? config['diesel'];

    document.getElementById('oilPriceLabel').textContent  = `ราคาน้ำมัน${cfg.label} (PTT)`;
    document.getElementById('oilPriceShow').textContent   = '...';
    document.getElementById('oilPriceStatus').textContent = '⏳ กำลังดึงราคาน้ำมัน...';
    document.getElementById('liveDot').className          = 'live-dot loading';
    document.getElementById('liveLabel').textContent      = 'กำลังดึง';
    document.getElementById('f-price-per-liter').value    = '';

    let fetched = null;
    try {
        const r = await Promise.race([
            fetch('https://api.chnwt.dev/thai-oil-api/latest'),
            new Promise((_,rj) => setTimeout(() => rj(new Error('timeout')), 8000))
        ]);
        if (r.ok) {
            const json = await r.json();
            const ptt  = json?.response?.stations?.ptt;
            if (ptt) {
                const direct = ptt[cfg.pttKey];
                if (direct?.price) {
                    const n = parseFloat(direct.price);
                    if (!isNaN(n) && n > 0) fetched = n;
                }
                if (!fetched) {
                    for (const fuel of Object.values(ptt)) {
                        if (fuel?.name?.includes(cfg.matchName) && fuel?.price) {
                            const n = parseFloat(fuel.price);
                            if (!isNaN(n) && n > 0) { fetched = n; break; }
                        }
                    }
                }
            }
        }
    } catch(_) {}

    const now = new Date().toLocaleTimeString('th-TH', { hour:'2-digit', minute:'2-digit' });
    if (fetched) {
        document.getElementById('oilPriceShow').textContent   = fetched.toFixed(2);
        document.getElementById('oilPriceStatus').textContent = `✅ PTT • ${now}`;
        document.getElementById('liveDot').className          = 'live-dot';
        document.getElementById('liveDot').style.background   = '';
        document.getElementById('liveLabel').textContent      = 'Live';
        document.getElementById('f-price-per-liter').value    = fetched.toFixed(2);
    } else {
        document.getElementById('oilPriceShow').textContent   = '—';
        document.getElementById('oilPriceStatus').textContent = `❌ ดึงราคาไม่ได้ • ${now}`;
        document.getElementById('liveDot').className          = 'live-dot';
        document.getElementById('liveDot').style.background   = 'var(--accent4)';
        document.getElementById('liveLabel').textContent      = 'ไม่มีข้อมูล';
        document.getElementById('f-price-per-liter').value    = '';
    }
    calcPreview();
}

// ======= CHARTS =======
const chartOpts = (yFmt, tipFmt) => ({
    responsive:true, maintainAspectRatio:false,
    plugins:{legend:{display:false},tooltip:{callbacks:{label:tipFmt}}},
    scales:{y:{beginAtZero:true,ticks:{font:{size:11},callback:yFmt},grid:{color:'rgba(0,0,0,.04)'}},x:{ticks:{font:{size:11}}}}
});
function initCharts() {
    const costByDriver = @json($costByDriver);
    const kmlByDriver  = @json($kmlByDriver);
    const col = arr => arr.map((_,i) => COLORS[i % COLORS.length]);
    if (costByDriver.length) {
        new Chart(document.getElementById('chartDriver'),{
            type:'bar',
            data:{labels:costByDriver.map(d=>d.driver),datasets:[{label:'฿',data:costByDriver.map(d=>d.total_price),backgroundColor:col(costByDriver),borderRadius:5}]},
            options:chartOpts(v=>'฿'+v.toLocaleString(),v=>`฿${v.raw.toLocaleString()}`)
        });
    }
    if (kmlByDriver.length) {
        new Chart(document.getElementById('chartKml'),{
            type:'bar',
            data:{labels:kmlByDriver.map(d=>d.driver),datasets:[{label:'km/L',data:kmlByDriver.map(d=>d.km_per_liter),backgroundColor:col(kmlByDriver),borderRadius:5}]},
            options:chartOpts(v=>v+' km/L',v=>`${v.raw} km/L`)
        });
    }
}

// ======= BOOT =======
document.addEventListener('DOMContentLoaded', () => {
    updateNavDate();
    initCharts();

    // Auto-open on validation error
    @if($errors->any())
    openModal({{ $editLog ? $editLog->id : 'null' }});
    @endif
});
</script>
</body>
</html>