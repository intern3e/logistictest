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
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(10,18,40,.55);z-index:500;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(2px)}
.modal-overlay.open{display:flex}
.modal{background:var(--surface);border-radius:16px;width:100%;max-width:640px;max-height:92vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalIn .2s ease}
@keyframes modalIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
.modal-header{padding:20px 24px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.modal-title{font-size:17px;font-weight:600;color:var(--text)}
.modal-close{width:32px;height:32px;border-radius:8px;border:none;background:var(--surface2);color:var(--text2);cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center}
.modal-body{padding:20px 24px}
.modal-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px}
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
@media(max-width:640px){.form-grid{grid-template-columns:1fr}.form-grid .full{grid-column:1}.sidebar{display:none}.main{padding:16px}.metrics{grid-template-columns:1fr 1fr}}
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
    {{-- filter ใช้ GET form submit ปกติ --}}
    <form method="GET" action="{{ route('oil') }}" id="filterForm">
      <div class="filter-bar">
        <div class="view-tabs">
          @foreach(['day'=>'รายวัน','month'=>'รายเดือน','year'=>'รายปี','all'=>'ทั้งหมด'] as $v=>$label)
          <button type="submit" name="view" value="{{ $v }}"
            class="view-tab {{ $view===$v ? 'active' : '' }}"
            onclick="setViewInput('{{ $v }}')">{{ $label }}</button>
          @endforeach
        </div>

        <input type="hidden" name="view" id="viewInput" value="{{ $view }}">

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
              <th>เวลาทำงาน</th><th>เลขไมล์เริ่ม</th>
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
              <td style="font-weight:600">{{ $r['start_mileage'] ? number_format($r['start_mileage']) : '—' }}</td>
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
      <div class="chart-card full">
        <div class="chart-card-title">แนวโน้มค่าน้ำมัน (฿) รายเดือน</div>
        <div class="chart-card-sub">ภาพรวม total_price ทั้งทีม</div>
        <div style="position:relative;width:100%;height:180px"><canvas id="chartTrend"></canvas></div>
      </div>
    </div>

    {{-- =================== REPORT =================== --}}
    <div id="reportSection" class="chart-card" style="margin-bottom:16px">
      <div class="chart-card-title" style="margin-bottom:16px">📊 สรุปรายงาน — ค่าน้ำมันสะสมรายคนขับ</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:center">
        <div style="position:relative;height:260px"><canvas id="chartReport"></canvas></div>
        <table style="font-size:13px">
          <thead>
            <tr>
              <th style="padding:8px;text-align:left;color:var(--text2)">คนขับ</th>
              <th style="padding:8px;text-align:right;color:var(--text2)">ค่าน้ำมัน</th>
              <th style="padding:8px;text-align:right;color:var(--text2)">ลิตร</th>
              <th style="padding:8px;text-align:right;color:var(--text2)">ชม.</th>
            </tr>
          </thead>
          <tbody>
            @foreach($reportByDriver as $r)
            <tr>
              <td style="padding:7px 8px;font-weight:600;color:var(--navy)">{{ $r['driver_name'] }}</td>
              <td style="padding:7px 8px;text-align:right">฿{{ number_format($r['total_price']) }}</td>
              <td style="padding:7px 8px;text-align:right">{{ $r['total_liters'] }}</td>
              <td style="padding:7px 8px;text-align:right">{{ $r['total_work_hours'] }}</td>
            </tr>
            @endforeach
            @if($reportByDriver->count() > 0)
            <tr style="border-top:2px solid var(--border);font-weight:700">
              <td style="padding:8px">รวม</td>
              <td style="padding:8px;text-align:right">฿{{ number_format($reportAll->sum('total_price')) }}</td>
              <td style="padding:8px;text-align:right">{{ round($reportAll->sum('liters'),1) }}</td>
              <td style="padding:8px;text-align:right">{{ round($reportAll->sum(fn($l)=>$l['work_hours']),1) }}</td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

{{-- =================== MODAL FORM =================== --}}
{{-- ใช้ form POST ธรรมดา ไม่ใช้ fetch --}}
<div class="modal-overlay" id="fuelModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="modalTitle">เพิ่มข้อมูลเติมน้ำมัน</div>
      <button type="button" class="modal-close" onclick="closeModal()">✕</button>
    </div>

    <form id="fuelForm" method="POST" action="{{ route('oil.store') }}">
      @csrf
      {{-- สำหรับ edit จะเปลี่ยน action + เพิ่ม _method=PUT ผ่าน JS --}}
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
            {{-- ปุ่มเลือกประเภทน้ำมัน PTT --}}
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
          <div class="section-divider">📋 ข้อมูลหลัก</div>

          <div>
            <label class="form-label">วันที่ทำงาน *</label>
            <input type="date" name="work_date" id="f-work-date" class="form-control {{ $errors->has('work_date') ? 'is-invalid' : '' }}"
              value="{{ old('work_date', $editLog?->work_date?->toDateString() ?? date('Y-m-d')) }}"
              onchange="onVehicleChange()">
            @error('work_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- ===== DRIVER ===== --}}
          @php
            $driverList = ['บังเดช','แชม','กอล์ฟ','หรั่ง','เก่ง','เอ','ยุทร','แฟรงค์','เอ้'];
            foreach($drivers as $dbD) { if(!in_array($dbD,$driverList)) $driverList[] = $dbD; }
            $curDriver   = old('driver_name', $editLog?->driver_name ?? '');
            $driverOther = $curDriver && !in_array($curDriver,$driverList);
            $plateList   = ['1 ฉผ 1276','1 ฉผ 3181','1ฉผ213','1ฉผศ7158','2 ฉธ 1620','2ฉมฎ3017','2ฉธ1619','2ฉธ1621','3ฉมก6071','3ฉมง3059','3ฉมณ6380','3ฉมย478','3ฉมห200','4กย1540','6762','City 8กค6309','City 9 กค4815','แจ๊ส 9กธ4830'];
            foreach($plates as $dbP) { if(!in_array($dbP,$plateList)) $plateList[] = $dbP; }
            $curPlate   = old('vehicle_id', $editLog?->vehicle_id ?? '');
            $plateOther = $curPlate && !in_array($curPlate,$plateList);
          @endphp
          <div>
            <label class="form-label">คนขับ</label>
            <select id="f-driver-select" class="form-control {{ $errors->has('driver_name') ? 'is-invalid' : '' }}"
              onchange="onSelectOther(this,'f-driver-name','f-driver-other')">
              <option value="">— เลือกคนขับ —</option>
              @foreach($driverList as $d)
              <option value="{{ $d }}" {{ $curDriver === $d ? 'selected' : '' }}>{{ $d }}</option>
              @endforeach
              <option value="__other__" {{ $driverOther ? 'selected' : '' }}>อื่นๆ (พิมพ์เอง)</option>
            </select>
            <input type="hidden" name="driver_name" id="f-driver-name" value="{{ $curDriver }}">
            <input type="text" id="f-driver-other" 
              class="form-control" style="margin-top:6px;{{ $driverOther ? '' : 'display:none' }}"
              value="{{ $driverOther ? $curDriver : '' }}"
              oninput="document.getElementById('f-driver-name').value=this.value">
            @error('driver_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          {{-- ===== PLATE ===== --}}
          <div class="full">
            <label class="form-label">ทะเบียนรถ*</label>
            <select id="f-plate-select" class="form-control {{ $errors->has('vehicle_id') ? 'is-invalid' : '' }}"
              onchange="onSelectOther(this,'f-vehicle-id','f-plate-other'); onVehicleChange()">
              <option value="">— เลือกทะเบียน —</option>
              @foreach($plateList as $p)
              <option value="{{ $p }}" {{ $curPlate === $p ? 'selected' : '' }}>{{ $p }}</option>
              @endforeach
              <option value="__other__" {{ $plateOther ? 'selected' : '' }}>อื่นๆ (พิมพ์เอง)</option>
            </select>
            <input type="hidden" name="vehicle_id" id="f-vehicle-id" value="{{ $curPlate }}">
            <input type="text" id="f-plate-other" 
              class="form-control" style="margin-top:6px;{{ $plateOther ? '' : 'display:none' }}"
              value="{{ $plateOther ? $curPlate : '' }}"
              oninput="document.getElementById('f-vehicle-id').value=this.value; onVehicleChange()">
            @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="section-divider">เวลาทำงาน</div>

          <div>
            <label class="form-label">เวลาเริ่มขับ</label>
            <input type="time" name="start_time" id="f-start-time" class="form-control {{ $errors->has('start_time') ? 'is-invalid' : '' }}"
              value="{{ old('start_time', $editLog?->start_time?->format('H:i')) }}"
              oninput="calcPreview()">
            @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div>
            <label class="form-label">เวลาสิ้นสุด</label>
            <input type="time" name="end_time" id="f-end-time" class="form-control {{ $errors->has('end_time') ? 'is-invalid' : '' }}"
              value="{{ old('end_time', $editLog?->end_time?->format('H:i')) }}"
              oninput="calcPreview()">
            @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="section-divider">ข้อมูลน้ำมัน</div>

          <div>
            <label class="form-label">จำนวนลิตร*</label>
            <input type="number" name="liters" id="f-liters" class="form-control {{ $errors->has('liters') ? 'is-invalid' : '' }}"
              step="0.01" 
              value="{{ old('liters', $editLog?->liters) }}"
              oninput="calcPreview()" readonly>
            @error('liters')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="form-label">ราคาต่อลิตร*</label>
            <input type="number" name="price_per_liter" id="f-price-per-liter"
              class="form-control" step="0.01"
              oninput="calcPreview()"  readonly>
          </div>

          <div class="full">
            <label class="form-label">ค่าน้ำมัน</label>
            <input type="number" name="total_price" id="f-total-price" class="form-control {{ $errors->has('total_price') ? 'is-invalid' : '' }}"
              step="0.01" 
              value="{{ old('total_price', $editLog?->total_price) }}"
              oninput="calcPreview()">
            @error('total_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="section-divider">เลขไมล์</div>

          <div class="full">
            <label class="form-label">เลขไมล์เริ่มต้น*</label>
            <input type="number" name="start_mileage" id="f-start-mileage" class="form-control {{ $errors->has('start_mileage') ? 'is-invalid' : '' }}"
              value="{{ old('start_mileage', $editLog?->start_mileage) }}"
              oninput="calcPreview()">
            <div class="mileage-info" id="prevMileageInfo">
              <span id="prevMileageText"></span>
            </div>
            @error('start_mileage')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
            <textarea name="note" id="f-note" class="form-control" >{{ old('note', $editLog?->note) }}</textarea>
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
        <button type="button" class="btn btn-outline" onclick="closeModal()">ยกเลิก</button>
        <button type="submit" class="btn btn-primary">💾 บันทึกข้อมูล</button>
      </div>
    </form>
  </div>
</div>

{{-- =================== SCRIPT =================== --}}
<script>
const COLORS    = ['#4f8ef7','#38c98a','#f5a623','#e85d5d','#a855f7','#06b6d4','#f59e0b','#10b981','#ef4444'];
const ROUTE_STORE   = '{{ route("oil.store") }}';
const ROUTE_UPDATE  = id => `{{ url("/oil/update") }}/${id}`;
const ROUTE_PREVMILE = '{{ route("oil.prevMileage") }}';
const CSRF      = document.querySelector('meta[name=csrf-token]').content;
let oilPrices   = {};
let currentOilType = 'diesel';

// ======= NAV DATE =======
function updateNavDate() {
    const now=new Date(),days=['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัส','ศุกร์','เสาร์'],
    months=['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
    const el=document.getElementById('navDate');
    if(el) el.textContent=`วัน${days[now.getDay()]}ที่ ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()+543}`;
}

// ======= FILTER VIEW =======
function setViewInput(v) {
    document.getElementById('viewInput').value = v;
}

// ======= MODAL =======
// editData ที่ PHP render ลง JS (ถ้ามี editLog)
const EDIT_LOG = @json($editLogData);

function openModal(id = null) {
    const form   = document.getElementById('fuelForm');
    const title  = document.getElementById('modalTitle');
    const method = document.getElementById('formMethod');

    if (id) {
        // ---- EDIT ----
        // ดึงข้อมูลจาก row ที่คลิก (ผ่าน data attribute)
        const row = document.querySelector(`[data-id="${id}"]`);
        title.textContent  = 'แก้ไขข้อมูลเติมน้ำมัน';
        form.action        = ROUTE_UPDATE(id);
        method.value       = 'PUT';
        // เปิด modal แล้ว fetch ข้อมูล row จาก hidden data
        fillFormFromRow(id);
    } else {
        // ---- CREATE ----
        title.textContent = 'เพิ่มข้อมูลเติมน้ำมัน';
        form.action       = ROUTE_STORE;
        method.value      = '';
        resetForm();
    }

    document.getElementById('fuelModal').classList.add('open');
    loadOilPrice('diesel');
}

function fillFormFromRow(id) {
    const allLogs = @json($logs);
    const r = allLogs.find(l => l.id === id);
    if (!r) return;
    setF('f-work-date',     r.work_date);
    setSelectOther('f-driver-select', 'f-driver-name', 'f-driver-other', r.driver_name);
    setSelectOther('f-plate-select',  'f-vehicle-id',  'f-plate-other',  r.vehicle_id);
    setF('f-start-time',    r.start_time);
    setF('f-end-time',      r.end_time);
    setF('f-start-mileage', r.start_mileage);
    setF('f-liters',        r.liters);
    setF('f-total-distance', r.total_distance);
    setF('f-total-price',   r.total_price);
    setF('f-note',          r.note);
    setF('f-price-per-liter', r.price_per_liter);
    fetchPrevMileage(r.vehicle_id, r.work_date, r.id);
    calcPreview();
}

function resetForm() {
    ['f-start-time','f-end-time','f-start-mileage','f-liters','f-total-price','f-note']
        .forEach(id => setF(id, ''));
    setF('f-work-date', new Date().toISOString().slice(0,10));
    setSelectOther('f-driver-select', 'f-driver-name', 'f-driver-other', '');
    setSelectOther('f-plate-select',  'f-vehicle-id',  'f-plate-other',  '');
    setF('f-total-distance', '');
    document.getElementById('prevMileageInfo').style.display = 'none';
    document.getElementById('calcBox').style.display         = 'none';
}

function closeModal() { document.getElementById('fuelModal').classList.remove('open'); }
function setF(id, v)  { const el=document.getElementById(id); if(el) el.value=v??''; }

// ======= SELECT + OTHER =======
/**
 * เมื่อเลือก "อื่นๆ (พิมพ์เอง)" จะแสดง input text
 * selectEl   = select element
 * hiddenId   = id ของ hidden input ที่ส่งไป form (name จริง)
 * textId     = id ของ text input สำหรับพิมพ์เอง
 */
function onSelectOther(selectEl, hiddenId, textId) {
    const val     = selectEl.value;
    const textEl  = document.getElementById(textId);
    const hidden  = document.getElementById(hiddenId);
    if (val === '__other__') {
        textEl.style.display = 'block';
        textEl.focus();
        hidden.value = textEl.value; // ยังเป็นค่าเดิมในกล่อง
    } else {
        textEl.style.display = 'none';
        textEl.value = '';
        hidden.value = val;
    }
}

/**
 * ตั้งค่า select+other field จาก value ที่มีอยู่
 * ใช้ตอน fillFormFromRow (edit)
 */
function setSelectOther(selectId, hiddenId, textId, value) {
    const sel    = document.getElementById(selectId);
    const hidden = document.getElementById(hiddenId);
    const text   = document.getElementById(textId);
    if (!sel || !hidden || !text) return;
    hidden.value = value ?? '';
    // ตรวจว่า value อยู่ใน option ไหม
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

// ======= PREV MILEAGE =======
function onVehicleChange() {
    const vid  = document.getElementById('f-vehicle-id')?.value;
    const date = document.getElementById('f-work-date')?.value;
    if (vid && date) fetchPrevMileage(vid, date);
}
async function fetchPrevMileage(vehicleId, workDate, excludeId = null) {
    try {
        const p = new URLSearchParams({ vehicle_id: vehicleId, work_date: workDate });
        if (excludeId) p.set('exclude_id', excludeId);
        const r = await fetch(`${ROUTE_PREVMILE}?${p}`, { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} });
        const d = await r.json();
        const el = document.getElementById('prevMileageInfo');
        if (d.data) {
            document.getElementById('prevMileageText').innerHTML =
                `🔖 Log ก่อนหน้า (${d.data.work_date}): เลขไมล์ <strong>${Number(d.data.start_mileage).toLocaleString()}</strong> กม.`;
            el.style.display = 'block';
        } else {
            el.style.display = 'none';
        }
    } catch(_) { document.getElementById('prevMileageInfo').style.display='none'; }
    calcPreview();
}

// ======= CALC PREVIEW =======
function calcPreview() {
    const startT = document.getElementById('f-start-time')?.value ?? '';
    const endT   = document.getElementById('f-end-time')?.value   ?? '';
    const ppl    = parseFloat(document.getElementById('f-price-per-liter')?.value) || 0;
    const tp     = parseFloat(document.getElementById('f-total-price')?.value)     || 0;
    const sm     = parseFloat(document.getElementById('f-start-mileage')?.value)   || 0;

    // ✅ คำนวณ liters ก่อน แล้วค่อย parse
    if (tp > 0 && ppl > 0) {
        setF('f-liters', (tp / ppl).toFixed(2));
    }

    const liters = parseFloat(document.getElementById('f-liters')?.value) || 0;

    // auto-fill total_price
    if (!tp && liters > 0 && ppl > 0) setF('f-total-price', (liters * ppl).toFixed(2));

    // work_hours
    let wh = 0;
    if (startT && endT) {
        const [sh,sm2]=startT.split(':').map(Number),[eh,em]=endT.split(':').map(Number);
        const d=(eh*60+em)-(sh*60+sm2); if(d>0) wh=d/60;
    }

    // distance estimate
    const prevText  = document.getElementById('prevMileageText')?.textContent ?? '';
    const prevMatch = prevText.match(/[\d,]+(?=\s*กม)/);
    const prevSm    = prevMatch ? parseInt(prevMatch[0].replace(/,/g,''),10) : 0;
    const distEst   = sm>0&&prevSm>0 ? sm-prevSm : 0;
    const kmlEst    = liters>0&&distEst>0 ? distEst/liters : 0;

    const show = wh>0||liters>0;
    document.getElementById('calcBox').style.display = show ? 'block' : 'none';
    if (show) {
        document.getElementById('calcWorkHours').textContent     = wh>0     ? wh.toFixed(2)                              : '—';
        document.getElementById('calcLitersPreview').textContent = liters>0 ? `${liters} / ฿${(tp||liters*ppl).toFixed(0)}` : '—';
        document.getElementById('calcKml').textContent           = kmlEst>0 ? kmlEst.toFixed(2)                          : '—';
    }
}

// ======= OIL PRICE =======
function switchOilType(type) {
    currentOilType = type;
    // reset ทุกปุ่มก่อน
    document.querySelectorAll('.oil-btn').forEach(b => {
        b.style.background   = 'rgba(255,255,255,.1)';
        b.style.borderColor  = 'transparent';
        b.style.color        = 'rgba(255,255,255,.7)';
    });
    // highlight ปุ่มที่เลือก
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
        'diesel':   { label:'ดีเซล',          pttKey:'premium_diesel',  matchName:'ดีเซล',         },
        '95':       { label:'แก๊สโซฮอล์ 95',  pttKey:'gasohol_95',      matchName:'แก๊สโซฮอล์ 95', },
        'benzin95': { label:'เบนซิน 95',       pttKey:'gasoline_95',     matchName:'เบนซิน 95',     },
        '91':       { label:'แก๊สโซฮอล์ 91',  pttKey:'gasohol_91',      matchName:'แก๊สโซฮอล์ 91', },
        'e20':      { label:'แก๊สโซฮอล์ E20', pttKey:'gasohol_e20',     matchName:'E20',            },
        'e85':      { label:'แก๊สโซฮอล์ E85', pttKey:'gasohol_e85',     matchName:'E85',            },
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
                // วิธีที่ 1: ใช้ key ตรง
                const direct = ptt[cfg.pttKey];
                if (direct?.price) {
                    const n = parseFloat(direct.price);
                    if (!isNaN(n) && n > 0) fetched = n;
                }
                // วิธีที่ 2: scan หา name ที่ match
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
    const trend        = @json($trend);
    const reportDriver = @json($reportByDriver);

    const col = (arr) => arr.map((_,i) => COLORS[i % COLORS.length]);

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
    if (trend.length) {
        const mLabels = trend.map(t=>{const[y,m]=t.month.split('-');return `${m}/${y.slice(2)}`;});
        new Chart(document.getElementById('chartTrend'),{
            type:'line',
            data:{labels:mLabels,datasets:[{data:trend.map(t=>t.total_price),borderColor:'#4f8ef7',backgroundColor:'rgba(79,142,247,.07)',fill:true,tension:.4,pointRadius:5,pointBackgroundColor:'#4f8ef7',pointBorderColor:'#fff',pointBorderWidth:2}]},
            options:chartOpts(v=>'฿'+v.toLocaleString(),v=>`฿${v.raw.toLocaleString()}`)
        });
    }
    if (reportDriver.length) {
        new Chart(document.getElementById('chartReport'),{
            type:'doughnut',
            data:{labels:reportDriver.map(d=>d.driver_name),datasets:[{data:reportDriver.map(d=>d.total_price),backgroundColor:col(reportDriver),borderWidth:2,borderColor:'#fff'}]},
            options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'right',labels:{font:{family:'Sarabun',size:12},padding:12}},tooltip:{callbacks:{label:v=>`${v.label}: ฿${v.raw.toLocaleString()}`}}}}
        });
    }
}

// ======= BOOT =======
document.addEventListener('DOMContentLoaded', () => {
    updateNavDate();
    initCharts();

    // เปิด modal auto ถ้ามี validation error (หมายความว่า form เพิ่งถูก submit แต่ fail)
    @if($errors->any())
    openModal({{ $editLog ? $editLog->id : 'null' }});
    @endif
});
</script>
</body>
</html>