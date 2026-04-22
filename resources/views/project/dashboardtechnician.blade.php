<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Triple 3E Group — ทีมช่างและตารางงาน</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  :root{
    --or:#f97316;--or2:#ea580c;--or3:#fff7ed;--or4:#ffedd5;--or5:#fed7aa;
    --dk:#1c1917;--md:#78716c;--lt:#f5f5f4;--wh:#ffffff;--bg:#f8f7f5;
    --bd:#e7e5e4;--gr:#22c55e;--bl:#3b82f6;--rd:#ef4444;--yl:#eab308;
  }
  body{font-family:'Sarabun',sans-serif;background:var(--bg);color:var(--dk);min-height:100vh}
  nav{background:var(--wh);border-bottom:3px solid var(--or);position:sticky;top:0;z-index:100;box-shadow:0 2px 10px rgba(249,115,22,0.09)}
  .nav-inner{max-width:1400px;margin:0 auto;display:flex;align-items:center;height:62px;padding:0 28px;gap:8px}
  .nav-logo{display:flex;align-items:center;gap:12px;margin-right:12px}
  .nav-mark{width:36px;height:36px;background:var(--or);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:#fff}
  .nav-title{font-size:15px;font-weight:800}
  .nav-sub{font-size:10px;color:var(--md);letter-spacing:0.07em}
  .nav-spacer{flex:1}
  .nav-badge{background:var(--or3);border:1px solid var(--or5);color:var(--or2);font-size:10px;font-weight:700;letter-spacing:0.08em;padding:4px 12px;border-radius:20px}
  .main{max-width:1400px;margin:0 auto;padding:24px}
  .flash{padding:12px 18px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px}
  .flash-success{background:#dcfce7;color:#15803d;border:1px solid #bbf7d0}
  .flash-error{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}

  .stats{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:20px}
  @media(max-width:900px){.stats{grid-template-columns:repeat(3,1fr)}}
  @media(max-width:500px){.stats{grid-template-columns:repeat(2,1fr)}}
  .stat-card{background:var(--wh);border:1px solid var(--bd);border-radius:12px;padding:14px 16px}
  .stat-lbl{font-size:10px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--md);margin-bottom:4px}
  .stat-n{font-size:26px;font-weight:300;color:var(--or);line-height:1}

  .filter-bar{background:var(--wh);border:1px solid var(--bd);border-radius:12px;padding:14px 18px;margin-bottom:18px;display:flex;gap:12px;flex-wrap:wrap;align-items:center}
  .filter-bar input,.filter-bar select{padding:8px 14px;border-radius:8px;border:1.5px solid var(--bd);background:var(--wh);font-size:13px;font-family:inherit;color:var(--dk);outline:none;transition:0.15s}
  .filter-bar input:focus,.filter-bar select:focus{border-color:var(--or);box-shadow:0 0 0 3px rgba(249,115,22,0.1)}
  .filter-bar input{min-width:200px}
  .btn{padding:8px 18px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:0.15s}
  .btn-primary{background:var(--or);color:#fff}
  .btn-primary:hover{background:var(--or2)}
  .btn-ghost{background:var(--lt);color:var(--md)}
  .btn-ghost:hover{background:var(--bd)}
  .btn-sm{padding:5px 12px;font-size:11px}
  .btn-danger{background:#fee2e2;color:#991b1b}
  .btn-danger:hover{background:#fecaca}

  .tabs{display:flex;gap:4px;margin-bottom:16px;border-bottom:2px solid var(--bd);align-items:flex-end}
  .tab{padding:10px 22px;font-size:13px;font-weight:700;color:var(--md);cursor:pointer;border:none;background:none;font-family:inherit;border-bottom:3px solid transparent;margin-bottom:-2px;transition:0.15s}
  .tab:hover{color:var(--or)}
  .tab.active{color:var(--or);border-bottom-color:var(--or)}
  .tab-actions{margin-left:auto;display:flex;gap:8px;padding-bottom:6px}
  .panel{display:none}
  .panel.active{display:block;animation:fi 0.2s ease}
  @keyframes fi{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:none}}

  .team-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:14px;margin-bottom:24px}
  .team-card{background:var(--wh);border:1px solid var(--bd);border-radius:14px;overflow:hidden;transition:0.2s}
  .team-card:hover{box-shadow:0 6px 20px rgba(249,115,22,0.1)}
  .team-head-bar{padding:14px 16px;background:var(--or3);border-bottom:2px solid var(--or5);display:flex;align-items:center;gap:10px}
  .team-title{font-size:14px;font-weight:800;color:var(--or2)}
  .team-meta{font-size:10px;color:var(--or);margin-top:2px;font-weight:600}
  .team-body{padding:6px 0}
  .member{display:flex;align-items:center;gap:10px;padding:9px 14px;border-bottom:1px solid #fafaf9;cursor:pointer;transition:0.15s}
  .member:last-child{border-bottom:none}
  .member:hover{background:var(--or3)}
  .m-av{width:33px;height:33px;border-radius:50%;border:1.5px solid var(--or5);overflow:hidden;background:var(--or4);flex-shrink:0}
  .m-av img{width:100%;height:100%;object-fit:cover}
  .m-name{font-size:12px;font-weight:700;display:flex;align-items:center;gap:6px}
  .m-role{font-size:11px;color:var(--md);margin-top:1px}
  .head-tag{background:var(--or);color:#fff;font-size:9px;font-weight:800;padding:2px 6px;border-radius:4px;letter-spacing:0.05em}
  .status-dot{width:8px;height:8px;border-radius:50%;margin-left:auto;flex-shrink:0}
  .st-active{background:var(--gr)}
  .st-leave{background:var(--rd)}

  .table-wrap{background:var(--wh);border:1px solid var(--bd);border-radius:14px;overflow-x:auto}
  table{width:100%;border-collapse:collapse;min-width:900px}
  th,td{padding:10px 12px;text-align:left;font-size:12px;border-bottom:1px solid var(--bd)}
  th{background:var(--lt);font-weight:700;font-size:11px;letter-spacing:0.05em;text-transform:uppercase;color:var(--md)}
  tbody tr:hover{background:var(--or3)}
  .so-code{font-family:monospace;font-weight:700;color:var(--or2);font-size:11px}
  .badge{padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;display:inline-block}
  .bg-pending{background:#fef3c7;color:#92400e}
  .bg-progress{background:#dbeafe;color:#1e40af}
  .bg-done{background:#dcfce7;color:#15803d}
  .bg-cancel{background:#fee2e2;color:#991b1b}
  .empty{text-align:center;padding:48px 20px;color:var(--md);font-size:13px}

  .overlay{display:none;position:fixed;inset:0;z-index:500;background:rgba(28,25,23,0.52);backdrop-filter:blur(5px);align-items:center;justify-content:center;padding:16px}
  .overlay.open{display:flex}
  .pmodal{background:var(--wh);border-radius:18px;width:680px;max-width:100%;overflow:hidden;box-shadow:0 22px 70px rgba(0,0,0,0.17);max-height:92vh;overflow-y:auto}
  .pmodal-strip{height:4px;background:linear-gradient(90deg,var(--or2),var(--or),#fbbf24)}
  .p-top{padding:18px 22px;display:flex;gap:14px;align-items:flex-start;border-bottom:1px solid var(--bd);position:relative}
  .p-photo{width:80px;height:80px;border-radius:14px;overflow:hidden;border:2px solid var(--or5);background:var(--or4);flex-shrink:0}
  .p-photo img{width:100%;height:100%;object-fit:cover}
  .p-company{font-size:10px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--or);margin-bottom:4px}
  .p-fullname{font-size:18px;font-weight:800;line-height:1.2}
  .p-role-tag{display:inline-block;margin-top:6px;background:var(--or3);border:1px solid var(--or5);color:var(--or2);font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px}
  .p-close{position:absolute;top:14px;right:14px;width:26px;height:26px;border-radius:50%;background:var(--lt);border:1px solid var(--bd);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;color:var(--md)}
  .p-close:hover{background:var(--or);color:#fff}
  .p-body{padding:18px 22px}
  .igrid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px}
  .ilabel{font-size:10px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#c4b5a0;margin-bottom:3px}
  .ival{font-size:13px;font-weight:700}
  .ival.phone{color:var(--or2)}
  .sk-wrap{display:flex;flex-wrap:wrap;gap:6px;margin-top:6px}
  .sk{font-size:11px;font-weight:600;padding:4px 10px;border-radius:16px;background:var(--or3);color:var(--or2);border:1px solid var(--or5)}

  .finput{width:100%;padding:8px 12px;border-radius:8px;border:1.5px solid var(--bd);font-family:'Sarabun',sans-serif;font-size:13px;color:var(--dk);outline:none;transition:0.15s;background:var(--wh)}
  .finput:focus{border-color:var(--or);box-shadow:0 0 0 3px rgba(249,115,22,0.1)}
  .frow{margin-bottom:14px}
  .ferr{background:#fee2e2;color:#991b1b;padding:10px 14px;border-radius:8px;font-size:12px;margin-bottom:14px;border:1px solid #fecaca}
  .factions{display:flex;gap:10px;justify-content:flex-end;margin-top:16px}

  .skill-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-top:6px}
  .skill-check{display:flex;align-items:center;gap:6px;padding:6px 10px;border-radius:8px;border:1.5px solid var(--bd);cursor:pointer;font-size:12px;font-weight:600;transition:0.15s;user-select:none}
  .skill-check:hover{border-color:var(--or);background:var(--or3)}
  .skill-check input[type=checkbox]{accent-color:var(--or);width:14px;height:14px}
  .skill-check.checked{border-color:var(--or);background:var(--or3);color:var(--or2)}

  .file-zone{border:2px dashed var(--bd);border-radius:10px;padding:16px;text-align:center;cursor:pointer;transition:0.15s;background:var(--lt)}
  .file-zone:hover{border-color:var(--or);background:var(--or3)}
  .file-zone input[type=file]{display:none}
  .file-preview{width:80px;height:80px;border-radius:10px;object-fit:cover;border:2px solid var(--or5);display:none;margin:0 auto 8px}
  .emp-id-note{font-size:10px;color:var(--md);margin-top:4px}
  .head-info-box{padding:10px 12px;background:var(--or3);border:1.5px solid var(--or5);border-radius:8px;font-size:12px;color:var(--or2);font-weight:600;line-height:1.6}

  .company-row{display:flex;gap:6px;align-items:center}
  .company-row .finput{flex:1}
  .btn-other{padding:8px 14px;border-radius:8px;border:1.5px solid var(--or5);background:var(--or3);color:var(--or2);font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;white-space:nowrap;transition:0.15s}
  .btn-other:hover{background:var(--or);color:#fff;border-color:var(--or)}

  .section-h{font-size:13px;font-weight:800;color:var(--or2);margin:18px 0 10px;padding-bottom:6px;border-bottom:2px solid var(--or5)}

  .comp-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:8px}
  .comp-card{border:1.5px solid var(--bd);border-radius:10px;padding:8px 10px;background:var(--wh)}
  .comp-head{display:flex;align-items:center;gap:6px;margin-bottom:6px}
  .comp-label{font-size:12px;font-weight:700}
  .comp-code{font-size:9px;color:var(--md);font-weight:700;background:var(--lt);padding:1px 5px;border-radius:3px;margin-left:auto}
  .comp-select{width:100%;padding:5px 8px;border-radius:6px;border:1.5px solid var(--bd);font-family:inherit;font-size:11px;background:var(--wh);cursor:pointer}
  .comp-select:focus{outline:none;border-color:var(--or)}
  .comp-select.lv-basic{background:#fef3c7;border-color:#fde68a}
  .comp-select.lv-skill{background:#dbeafe;border-color:#bfdbfe}
  .comp-select.lv-expert{background:#dcfce7;border-color:#bbf7d0;font-weight:700}

  .lic-list{display:flex;flex-direction:column;gap:10px}
  .lic-item{border:1.5px solid var(--bd);border-radius:10px;padding:12px;background:var(--lt)}
  .lic-item-head{display:flex;align-items:center;gap:6px;margin-bottom:8px}
  .lic-num{font-size:11px;font-weight:800;color:var(--or2);background:var(--or3);padding:2px 8px;border-radius:12px;border:1px solid var(--or5)}
  .lic-del{margin-left:auto;padding:3px 10px;background:#fee2e2;color:#991b1b;border:none;border-radius:6px;cursor:pointer;font-size:11px;font-weight:700;font-family:inherit}
  .lic-del:hover{background:#fecaca}
  .lic-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
  .lic-file-row{display:flex;gap:6px;align-items:center;margin-top:6px;flex-wrap:wrap}
  .lic-file-current{font-size:11px;color:var(--gr);font-weight:600;padding:3px 8px;background:#dcfce7;border-radius:4px;border:1px solid #bbf7d0;text-decoration:none}
  .lic-file-input{font-size:11px;flex:1}
  .btn-add-lic{width:100%;padding:10px;border:2px dashed var(--or5);background:var(--or3);color:var(--or2);border-radius:10px;cursor:pointer;font-size:12px;font-weight:700;font-family:inherit;transition:0.15s}
  .btn-add-lic:hover{border-color:var(--or);background:var(--or4)}

  .sw-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-top:6px}
  .sw-custom-row{display:flex;gap:6px;margin-top:8px}
  .sw-custom-row input{flex:1}
  .sw-custom-tags{display:flex;flex-wrap:wrap;gap:4px;margin-top:8px}
  .sw-tag{background:var(--bl);color:#fff;font-size:11px;font-weight:600;padding:3px 10px;border-radius:12px;display:inline-flex;align-items:center;gap:5px}
  .sw-tag .x{cursor:pointer;font-weight:800;opacity:0.85}
  .sw-tag .x:hover{opacity:1}

  .profile-section{margin-top:14px}
  .profile-comp-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:6px;margin-top:6px}
  .pc-card{display:flex;align-items:center;gap:6px;padding:6px 10px;border-radius:8px;background:var(--lt);border:1px solid var(--bd);font-size:11px}
  .pc-card.lv-basic{background:#fef3c7;border-color:#fde68a}
  .pc-card.lv-skill{background:#dbeafe;border-color:#bfdbfe}
  .pc-card.lv-expert{background:#dcfce7;border-color:#bbf7d0;font-weight:700}
  .pc-card.lv-none{opacity:0.5}
  .pc-lbl{flex:1}
  .pc-val{font-weight:700;font-size:10px}

  .profile-lic-item{padding:8px 12px;border:1px solid var(--bd);border-radius:8px;margin-bottom:6px;background:var(--lt)}
  .profile-lic-title{font-weight:700;font-size:12px;color:var(--or2)}
  .profile-lic-meta{font-size:11px;color:var(--md);margin-top:2px}
  .profile-lic-meta a{color:var(--bl);text-decoration:underline}

  /* DOB row: date picker + พ.ศ. display */
  .dob-row{display:flex;gap:6px;align-items:center}
  .dob-row input[type=date]{flex:1}
  .dob-be{font-size:11px;color:var(--or2);font-weight:700;background:var(--or3);padding:4px 10px;border-radius:6px;border:1px solid var(--or5);white-space:nowrap;min-width:130px;text-align:center}
</style>
</head>
<body>
<nav>
  <div class="nav-inner">
    <div class="nav-logo">
      <div class="nav-mark">3E</div>
      <div>
        <div class="nav-title">ทริปเปิ้ล อี เทรดดิ้ง</div>
        <div class="nav-sub">ทีมช่างและตารางงาน</div>
      </div>
    </div>
    <div class="nav-spacer"></div>
    <a href="{{ url('/project') }}" class="nav-badge">← โปรเจกต์</a>
  </div>
</nav>

<div class="main">
  @if(session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
  @endif
  @if($errors->has('delete'))
    <div class="flash flash-error">{{ $errors->first('delete') }}</div>
  @endif

  <div class="stats">
    <div class="stat-card"><div class="stat-lbl">ช่างทั้งหมด</div><div class="stat-n">{{ $stats['total_tech'] }}</div></div>
    <div class="stat-card"><div class="stat-lbl">หัวหน้าทีม</div><div class="stat-n">{{ $stats['total_heads'] }}</div></div>
    <div class="stat-card"><div class="stat-lbl">จำนวนทีม</div><div class="stat-n">{{ $stats['total_teams'] }}</div></div>
    <div class="stat-card"><div class="stat-lbl">งานที่ดำเนินการ</div><div class="stat-n">{{ $stats['active_jobs'] }}</div></div>
    <div class="stat-card"><div class="stat-lbl">งานรอดำเนินการ</div><div class="stat-n">{{ $stats['pending_jobs'] }}</div></div>
    <div class="stat-card"><div class="stat-lbl">งานเสร็จสิ้น</div><div class="stat-n">{{ $stats['done_jobs'] }}</div></div>
  </div>

  <form method="GET" class="filter-bar" action="{{ route('technician.dashboard') }}">
    <input type="text" name="search" placeholder="ค้นหาช่าง/ตำแหน่ง/ทักษะ..." value="{{ $search }}">
    <select name="company">
      <option value="">ทุกบริษัท</option>
      @foreach($companies as $c)
        <option value="{{ $c }}" @selected($companyFilter===$c)>{{ $c }}</option>
      @endforeach
    </select>
    <select name="team">
      <option value="">ทุกทีม</option>
      @foreach($teams as $t)
        <option value="{{ $t['team_name'] }}" @selected($teamFilter===$t['team_name'])>
          {{ $t['team_name'] }} ({{ $t['company'] }})
        </option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-primary">กรองข้อมูล</button>
    <a href="{{ route('technician.dashboard') }}" class="btn btn-ghost" style="text-decoration:none;display:inline-block">ล้าง</a>
  </form>

  <div class="tabs">
    <button class="tab active" onclick="switchTab('teams',this)">ทีมช่าง ({{ $teams->count() }})</button>
    <button class="tab" onclick="switchTab('schedules',this)">ตารางงาน ({{ $schedules->count() }})</button>
    <div class="tab-actions">
      <button class="btn btn-primary" onclick="openModal('modal-tech')">+ เพิ่มช่าง</button>
      <button class="btn btn-primary" onclick="openModal('modal-sched')">+ เพิ่มงาน</button>
    </div>
  </div>

  {{-- TAB: ทีมช่าง --}}
  <div class="panel active" id="panel-teams">
    @if($teams->count() === 0)
      <div class="empty">ยังไม่มีทีมช่างในระบบ</div>
    @else
      <div class="team-grid">
        @foreach($teams as $team)
          @php
            $members = $technicians->where('emp_team', $team['team_name']);
            $head    = $members->firstWhere('emp_position', 'หัวหน้าทีม');
            $others  = $members->where('emp_position', '!=', 'หัวหน้าทีม');
          @endphp
          <div class="team-card">
            <div class="team-head-bar">
              <div style="flex:1">
                <div class="team-title">{{ $team['team_name'] }}</div>
                <div class="team-meta">{{ $team['company'] }} · สมาชิก {{ $members->count() }} คน</div>
              </div>
            </div>
            <div class="team-body">
              @if($head)
                <div class="member" data-tech="{{ json_encode($head, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openProfileFromEl(this)">
                  <div class="m-av">
                    <img src="{{ $head->img ? asset('storage/'.$head->img) : 'https://via.placeholder.com/64/fed7aa/f97316?text=3E' }}"
                         onerror="this.src='https://via.placeholder.com/64/fed7aa/f97316?text=3E'">
                  </div>
                  <div style="flex:1">
                    <div class="m-name">{{ $head->emp_name ?: $head->emp_id }} <span class="head-tag">หัวหน้า</span></div>
                    <div class="m-role">{{ $head->emp_id }} · {{ $head->emp_company }}</div>
                  </div>
                  <div class="status-dot st-{{ $head->status }}"></div>
                  <div style="display:flex;gap:4px;margin-left:8px" onclick="event.stopPropagation()">
                    <button type="button" class="btn btn-sm btn-ghost" onclick="openEditTechFromEl(this.closest('.member'))">แก้ไข</button>
                    <form method="POST" action="{{ route('tech.delete', $head->emp_id) }}"
                          onsubmit="return confirm('ลบ {{ $head->emp_name ?: $head->emp_id }} ?')">
                      @csrf @method('POST')
                      <button type="submit" class="btn btn-sm btn-danger">ลบ</button>
                    </form>
                  </div>
                </div>
              @endif
              @foreach($others as $m)
                <div class="member" data-tech="{{ json_encode($m, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openProfileFromEl(this)">
                  <div class="m-av">
                    <img src="{{ $m->img ? asset('storage/'.$m->img) : 'https://via.placeholder.com/64/fed7aa/f97316?text=3E' }}"
                         onerror="this.src='https://via.placeholder.com/64/fed7aa/f97316?text=3E'">
                  </div>
                  <div style="flex:1">
                    <div class="m-name">{{ $m->emp_name ?: $m->emp_id }}</div>
                    <div class="m-role">{{ $m->emp_id }} · {{ $m->emp_company }}</div>
                  </div>
                  <div class="status-dot st-{{ $m->status }}"></div>
                  <div style="display:flex;gap:4px;margin-left:8px" onclick="event.stopPropagation()">
                    <button type="button" class="btn btn-sm btn-ghost" onclick="openEditTechFromEl(this.closest('.member'))">แก้ไข</button>
                    <form method="POST" action="{{ route('tech.delete', $m->emp_id) }}"
                          onsubmit="return confirm('ลบ {{ $m->emp_name ?: $m->emp_id }} ?')">
                      @csrf @method('POST')
                      <button type="submit" class="btn btn-sm btn-danger">ลบ</button>
                    </form>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- TAB: ตารางงาน --}}
  <div class="panel" id="panel-schedules">
    @if($schedules->count() === 0)
      <div class="empty">ยังไม่มีงานในระบบ</div>
    @else
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>SO</th><th>ลูกค้า / งาน</th><th>สถานที่</th><th>ทีมที่รับผิดชอบ</th>
              <th>วันเริ่ม</th><th>วันสิ้นสุด</th><th>สถานะ</th><th>หมายเหตุ</th><th>จัดการ</th>
            </tr>
          </thead>
          <tbody>
            @foreach($schedules as $s)
              @php
                $statusMap = [
                  'pending'     => ['label'=>'รอดำเนินการ','class'=>'bg-pending'],
                  'in_progress' => ['label'=>'กำลังทำ',    'class'=>'bg-progress'],
                  'done'        => ['label'=>'เสร็จสิ้น',  'class'=>'bg-done'],
                  'cancelled'   => ['label'=>'ยกเลิก',     'class'=>'bg-cancel'],
                ];
                $st = $statusMap[$s->status] ?? $statusMap['pending'];
              @endphp
              <tr>
                <td><span class="so-code">{{ $s->so_number }}</span></td>
                <td><strong>{{ $s->customer_name }}</strong><br><small style="color:var(--md)">{{ $s->job_title }}</small></td>
                <td>{{ $s->job_location }}</td>
                <td><strong style="color:var(--or2)">{{ $s->team_name }}</strong></td>
                <td>{{ \Carbon\Carbon::parse($s->start_date)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($s->end_date)->format('d/m/Y') }}</td>
                <td><span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span></td>
                <td><small style="color:var(--md)">{{ $s->note }}</small></td>
                <td>
                  <div style="display:flex;gap:4px">
                    <button type="button" class="btn btn-sm btn-ghost" data-sched="{{ json_encode($s, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openEditSchedFromEl(this)">แก้ไข</button>
                    <form method="POST" action="{{ route('sched.delete', $s->id) }}"
                          onsubmit="return confirm('ลบงาน {{ $s->so_number }} ?')">
                      @csrf @method('POST')
                      <button type="submit" class="btn btn-sm btn-danger">ลบ</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>

{{-- ================================================================
     MODAL: ดูโปรไฟล์ช่าง
================================================================ --}}
<div class="overlay" id="overlay" onclick="closeModal(event)">
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="p-top">
      <div class="p-photo"><img id="m-img" src="" alt=""></div>
      <div>
        <div class="p-company" id="m-company"></div>
        <div class="p-fullname" id="m-name"></div>
        <div id="m-name-eng" style="font-size:12px;color:var(--md);font-weight:500;margin-top:2px"></div>
        <div class="p-role-tag" id="m-position"></div>
      </div>
      <div class="p-close" onclick="document.getElementById('overlay').classList.remove('open')">✕</div>
    </div>
    <div class="p-body">
      <div class="igrid">
        <div><div class="ilabel">รหัสพนักงาน</div><div class="ival" id="m-empid"></div></div>
        <div><div class="ilabel">ทีม</div><div class="ival" id="m-team"></div></div>
        <div><div class="ilabel">เบอร์โทร</div><div class="ival phone" id="m-phone"></div></div>
        <div><div class="ilabel">วันเกิด (พ.ศ.)</div><div class="ival" id="m-dob"></div></div>
      </div>
      <div><div class="ilabel">สถานะ</div><div class="ival" id="m-status"></div></div>

      <div class="profile-section">
        <div class="ilabel">ทักษะ</div>
        <div class="sk-wrap" id="m-skills"></div>
      </div>

      <div class="profile-section">
        <div class="ilabel">Core Competencies</div>
        <div class="profile-comp-grid" id="m-competencies"></div>
      </div>

      <div class="profile-section">
        <div class="ilabel">Licenses &amp; Experience</div>
        <div id="m-licenses"></div>
      </div>

      <div class="profile-section">
        <div class="ilabel">Software &amp; Tools</div>
        <div class="sk-wrap" id="m-software"></div>
      </div>
    </div>
  </div>
</div>

{{-- ================================================================
     MODAL: เพิ่มช่าง
================================================================ --}}
<div class="overlay" id="modal-tech" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="p-top">
      <div><div class="p-fullname">เพิ่มช่างใหม่</div></div>
      <div class="p-close" onclick="closeModalById('modal-tech')">✕</div>
    </div>
    <div class="p-body">
      @if($errors->any() && !old('_edit_tech') && !old('_edit_sched') && !old('so_number'))
        <div class="ferr">{{ $errors->first() }}</div>
      @endif
      <form method="POST" action="{{ route('tech.store') }}" enctype="multipart/form-data" id="form-add-tech">
        @csrf

        <div class="section-h">ข้อมูลพื้นฐาน</div>
        <div class="igrid">
          <div class="frow">
            <div class="ilabel">รหัสพนักงาน *</div>
            <input class="finput" type="text" name="emp_id" value="{{ old('emp_id') }}" required placeholder="เช่น 3E-001">
            <div class="emp-id-note">ใช้ได้: ตัวอักษรอังกฤษ, ตัวเลข, - , _</div>
          </div>
          <div class="frow">
            <div class="ilabel">ชื่อ-นามสกุล (ไทย)</div>
            <input class="finput" type="text" name="emp_name" id="add-emp_name" value="{{ old('emp_name') }}" placeholder="ชื่อ นามสกุล">
          </div>
          <div class="frow">
            <div class="ilabel">ชื่อ-นามสกุล (Eng)</div>
            <input class="finput" type="text" name="emp_name_eng" value="{{ old('emp_name_eng') }}" placeholder="First Last">
          </div>
          <div class="frow">
            <div class="ilabel">เบอร์โทร</div>
            <input class="finput" type="text" name="emp_phone" value="{{ old('emp_phone') }}" placeholder="0xx-xxx-xxxx">
          </div>
          <div class="frow">
            <div class="ilabel">วันเกิด</div>
            <div class="dob-row">
              <input class="finput" type="date" name="date_of_birth" id="add-dob" value="{{ old('date_of_birth') }}" onchange="updateBE('add')">
              <span class="dob-be" id="add-dob-be">พ.ศ. -</span>
            </div>
            <div class="emp-id-note">เลือกจากปฏิทิน (ป้าย พ.ศ. อัปเดตอัตโนมัติ)</div>
          </div>

          <div class="frow">
            <div class="ilabel">บริษัท</div>
            @php
              $oldCompany = old('emp_company','');
              $knownCodes = collect($companyList)->pluck('code')->toArray();
              $isCustomComp = $oldCompany !== '' && !in_array($oldCompany, $knownCodes, true);
            @endphp
            <div class="company-row" id="add-company-select-row" style="{{ $isCustomComp ? 'display:none' : '' }}">
              <select class="finput" name="emp_company" id="add-emp_company" {{ $isCustomComp ? 'disabled' : '' }}>
                <option value="">-- เลือกบริษัท --</option>
                @foreach($companyList as $co)
                  <option value="{{ $co['code'] }}" {{ $oldCompany===$co['code']?'selected':'' }}>
                    {{ $co['code'] }} – {{ $co['label'] }}
                  </option>
                @endforeach
              </select>
              <button type="button" class="btn-other" onclick="showCustomCompany('add')">+ อื่นๆ</button>
            </div>
            <div class="company-row" id="add-company-custom-row" style="{{ $isCustomComp ? '' : 'display:none' }}">
              <input class="finput" type="text" name="emp_company" id="add-company-custom"
                     value="{{ $isCustomComp ? $oldCompany : '' }}"
                     placeholder="พิมพ์ชื่อบริษัทใหม่"
                     {{ $isCustomComp ? '' : 'disabled' }}>
              <button type="button" class="btn-other" onclick="hideCustomCompany('add')">← กลับ</button>
            </div>
          </div>

          <div class="frow">
            <div class="ilabel">ตำแหน่ง</div>
            <select class="finput" name="emp_position" id="add-emp_position" onchange="handlePositionChange('add')">
              <option value="">-- เลือกตำแหน่ง --</option>
              <option value="ลูกทีม" {{ old('emp_position')==='ลูกทีม'?'selected':'' }}>ลูกทีม</option>
              <option value="หัวหน้าทีม" {{ old('emp_position')==='หัวหน้าทีม'?'selected':'' }}>หัวหน้าทีม</option>
            </select>
          </div>

          <div class="frow" id="add-team-wrap" style="{{ old('emp_position')==='หัวหน้าทีม' ? 'display:none' : '' }}">
            <div class="ilabel">ทีม</div>
            <select class="finput" name="emp_team" id="add-team-select">
              <option value="">-- เลือกทีม --</option>
              @foreach($availableTeams as $tn)
                <option value="{{ $tn }}" {{ old('emp_team')===$tn?'selected':'' }}>{{ $tn }}</option>
              @endforeach
            </select>
            <div class="emp-id-note">เลือกทีมที่มีหัวหน้าอยู่แล้ว</div>
          </div>
        </div>

        <div class="frow" id="add-head-info" style="{{ old('emp_position')==='หัวหน้าทีม' ? '' : 'display:none' }}">
          <div class="head-info-box">ชื่อทีมจะถูกตั้งเป็น <strong>ชื่อพนักงาน</strong> โดยอัตโนมัติ (ต้องกรอกชื่อก่อน)</div>
        </div>

        <div class="frow">
          <div class="ilabel">รูปภาพ</div>
          <div class="file-zone" onclick="document.getElementById('add-img-input').click()">
            <img id="add-img-preview" class="file-preview" src="" alt="">
            <div id="add-img-text" style="font-size:12px;color:var(--md)">คลิกเพื่ออัปโหลดรูป (JPG/PNG)</div>
          </div>
          <input type="file" id="add-img-input" name="img" accept="image/*" onchange="previewImg(this,'add-img-preview','add-img-text')">
        </div>

        <div class="section-h">ทักษะ</div>
        @php
          $oldSkills = old('emp_skill', []);
          if (is_string($oldSkills)) $oldSkills = array_filter(array_map('trim', explode(',', $oldSkills)));
          $oldSkills = is_array($oldSkills) ? $oldSkills : [];
        @endphp
        <div class="skill-grid">
          @foreach($skillOptions as $sk)
            <label class="skill-check {{ in_array($sk, $oldSkills) ? 'checked' : '' }}">
              <input type="checkbox" name="emp_skill[]" value="{{ $sk }}"
                {{ in_array($sk, $oldSkills) ? 'checked' : '' }}
                onchange="this.closest('label').classList.toggle('checked',this.checked)">
              {{ $sk }}
            </label>
          @endforeach
        </div>

        <div class="section-h">Core Competencies</div>
        @php $oldComp = old('core_competencies', []); @endphp
        <div class="comp-grid">
          @foreach($competencyList as $c)
            @php $val = $oldComp[$c['key']] ?? 'none'; @endphp
            <div class="comp-card">
              <div class="comp-head">
                <span class="comp-label">{{ $c['label'] }}</span>
                <span class="comp-code">{{ $c['key'] }}</span>
              </div>
              <select class="comp-select lv-{{ $val }}" name="core_competencies[{{ $c['key'] }}]" onchange="updateCompClass(this)">
                @foreach($competencyLevels as $lv=>$lvLabel)
                  <option value="{{ $lv }}" {{ $val===$lv?'selected':'' }}>{{ $lvLabel }}</option>
                @endforeach
              </select>
            </div>
          @endforeach
        </div>

        <div class="section-h">Licenses &amp; Experience</div>
        <div class="lic-list" id="add-lic-list"></div>
        <button type="button" class="btn-add-lic" onclick="addLicense('add')" style="margin-top:10px">+ เพิ่มใบรับรอง / ประสบการณ์</button>

        <div class="section-h">Software &amp; Tools</div>
        @php $oldSw = old('software_tools', []); if(!is_array($oldSw)) $oldSw=[]; @endphp
        <div class="sw-grid">
          @foreach($softwareOptions as $sw)
            <label class="skill-check {{ in_array($sw, $oldSw) ? 'checked' : '' }}">
              <input type="checkbox" name="software_tools[]" value="{{ $sw }}"
                {{ in_array($sw, $oldSw) ? 'checked' : '' }}
                onchange="this.closest('label').classList.toggle('checked',this.checked)">
              {{ $sw }}
            </label>
          @endforeach
        </div>
        <div class="sw-custom-row">
          <input type="text" class="finput" id="add-sw-custom" placeholder="เพิ่ม Software อื่นๆ..." onkeydown="if(event.key==='Enter'){event.preventDefault();addCustomSw('add')}">
          <button type="button" class="btn-other" onclick="addCustomSw('add')">+ เพิ่ม</button>
        </div>
        <div class="sw-custom-tags" id="add-sw-custom-tags">
          @foreach($oldSw as $sw)
            @if(!in_array($sw, $softwareOptions, true))
              <span class="sw-tag">
                <input type="hidden" name="software_tools[]" value="{{ $sw }}">
                {{ $sw }}<span class="x" onclick="this.parentElement.remove()">✕</span>
              </span>
            @endif
          @endforeach
        </div>

        <div class="factions">
          <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-tech')">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึก</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ================================================================
     MODAL: แก้ไขช่าง
================================================================ --}}
<div class="overlay" id="modal-edit-tech" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="p-top">
      <div><div class="p-fullname">แก้ไขข้อมูลช่าง</div></div>
      <div class="p-close" onclick="closeModalById('modal-edit-tech')">✕</div>
    </div>
    <div class="p-body">
      @if($errors->any() && old('_edit_tech'))
        <div class="ferr">{{ $errors->first() }}</div>
      @endif
      <form method="POST" id="form-edit-tech" action="" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_edit_tech" value="1">

        <div class="section-h">ข้อมูลพื้นฐาน</div>
        <div class="igrid">
          <div class="frow">
            <div class="ilabel">รหัสพนักงาน</div>
            <input class="finput" type="text" id="et-emp_id" readonly style="background:var(--lt);color:var(--md)">
          </div>
          <div class="frow">
            <div class="ilabel">ชื่อ-นามสกุล (ไทย)</div>
            <input class="finput" type="text" name="emp_name" id="et-emp_name">
          </div>
          <div class="frow">
            <div class="ilabel">ชื่อ-นามสกุล (Eng)</div>
            <input class="finput" type="text" name="emp_name_eng" id="et-emp_name_eng">
          </div>
          <div class="frow">
            <div class="ilabel">เบอร์โทร</div>
            <input class="finput" type="text" name="emp_phone" id="et-emp_phone">
          </div>
          <div class="frow">
            <div class="ilabel">วันเกิด</div>
            <div class="dob-row">
              <input class="finput" type="date" name="date_of_birth" id="et-dob" onchange="updateBE('et')">
              <span class="dob-be" id="et-dob-be">พ.ศ. -</span>
            </div>
          </div>

          <div class="frow">
            <div class="ilabel">บริษัท</div>
            <div class="company-row" id="et-company-select-row">
              <select class="finput" name="emp_company" id="et-emp_company">
                <option value="">-- เลือกบริษัท --</option>
                @foreach($companyList as $co)
                  <option value="{{ $co['code'] }}">{{ $co['code'] }} – {{ $co['label'] }}</option>
                @endforeach
              </select>
              <button type="button" class="btn-other" onclick="showCustomCompany('et')">+ อื่นๆ</button>
            </div>
            <div class="company-row" id="et-company-custom-row" style="display:none">
              <input class="finput" type="text" name="emp_company" id="et-company-custom" placeholder="พิมพ์ชื่อบริษัทใหม่" disabled>
              <button type="button" class="btn-other" onclick="hideCustomCompany('et')">← กลับ</button>
            </div>
          </div>

          <div class="frow">
            <div class="ilabel">ตำแหน่ง</div>
            <select class="finput" name="emp_position" id="et-emp_position" onchange="handlePositionChange('et')">
              <option value="">-- เลือกตำแหน่ง --</option>
              <option value="ลูกทีม">ลูกทีม</option>
              <option value="หัวหน้าทีม">หัวหน้าทีม</option>
            </select>
          </div>

          <div class="frow" id="et-team-wrap">
            <div class="ilabel">ทีม</div>
            <select class="finput" name="emp_team" id="et-team-select">
              <option value="">-- เลือกทีม --</option>
              @foreach($availableTeams as $tn)
                <option value="{{ $tn }}">{{ $tn }}</option>
              @endforeach
            </select>
          </div>

          <div class="frow">
            <div class="ilabel">สถานะ</div>
            <select class="finput" name="status" id="et-status">
              <option value="active">พร้อมทำงาน (Active)</option>
              <option value="leave">ลาออก (ไม่แสดงในระบบ)</option>
            </select>
            <div class="emp-id-note" style="color:var(--rd)">เลือก "ลาออก" จะทำให้ไม่ปรากฏในระบบ</div>
          </div>
        </div>

        <div class="frow" id="et-head-info" style="display:none">
          <div class="head-info-box">ชื่อทีมจะถูกตั้งเป็น <strong>ชื่อพนักงาน</strong> โดยอัตโนมัติ</div>
        </div>

        <div class="frow">
          <div class="ilabel">รูปภาพใหม่ (เว้นว่างถ้าไม่ต้องการเปลี่ยน)</div>
          <div class="file-zone" onclick="document.getElementById('et-img-input').click()">
            <img id="et-img-preview" class="file-preview" src="" alt="">
            <div id="et-img-text" style="font-size:12px;color:var(--md)">คลิกเพื่อเปลี่ยนรูป</div>
          </div>
          <input type="file" id="et-img-input" name="img" accept="image/*" onchange="previewImg(this,'et-img-preview','et-img-text')">
        </div>

        <div class="section-h">ทักษะ</div>
        <div class="skill-grid" id="et-skill-grid">
          @foreach($skillOptions as $sk)
            <label class="skill-check" data-skill="{{ $sk }}">
              <input type="checkbox" name="emp_skill[]" value="{{ $sk }}" onchange="this.closest('label').classList.toggle('checked',this.checked)">
              {{ $sk }}
            </label>
          @endforeach
        </div>

        <div class="section-h">Core Competencies</div>
        <div class="comp-grid" id="et-comp-grid">
          @foreach($competencyList as $c)
            <div class="comp-card">
              <div class="comp-head">
                <span class="comp-label">{{ $c['label'] }}</span>
                <span class="comp-code">{{ $c['key'] }}</span>
              </div>
              <select class="comp-select lv-none" data-comp="{{ $c['key'] }}" name="core_competencies[{{ $c['key'] }}]" onchange="updateCompClass(this)">
                @foreach($competencyLevels as $lv=>$lvLabel)
                  <option value="{{ $lv }}">{{ $lvLabel }}</option>
                @endforeach
              </select>
            </div>
          @endforeach
        </div>

        <div class="section-h">Licenses &amp; Experience</div>
        <div class="lic-list" id="et-lic-list"></div>
        <button type="button" class="btn-add-lic" onclick="addLicense('et')" style="margin-top:10px">+ เพิ่มใบรับรอง / ประสบการณ์</button>

        <div class="section-h">Software &amp; Tools</div>
        <div class="sw-grid" id="et-sw-grid">
          @foreach($softwareOptions as $sw)
            <label class="skill-check" data-sw="{{ $sw }}">
              <input type="checkbox" name="software_tools[]" value="{{ $sw }}" onchange="this.closest('label').classList.toggle('checked',this.checked)">
              {{ $sw }}
            </label>
          @endforeach
        </div>
        <div class="sw-custom-row">
          <input type="text" class="finput" id="et-sw-custom" placeholder="เพิ่ม Software อื่นๆ..." onkeydown="if(event.key==='Enter'){event.preventDefault();addCustomSw('et')}">
          <button type="button" class="btn-other" onclick="addCustomSw('et')">+ เพิ่ม</button>
        </div>
        <div class="sw-custom-tags" id="et-sw-custom-tags"></div>

        <div class="factions">
          <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-edit-tech')">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึก</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODAL: เพิ่มงาน --}}
<div class="overlay" id="modal-sched" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="p-top">
      <div><div class="p-fullname">เพิ่มงานใหม่</div></div>
      <div class="p-close" onclick="closeModalById('modal-sched')">✕</div>
    </div>
    <div class="p-body">
      @if($errors->any() && !old('_edit_sched') && old('so_number'))
        <div class="ferr">{{ $errors->first() }}</div>
      @endif
      <form method="POST" action="{{ route('sched.store') }}">
        @csrf
        <div class="igrid">
          <div class="frow"><div class="ilabel">เลข SO *</div><input class="finput" type="text" name="so_number" value="{{ old('so_number') }}" required placeholder="SO-XXXX"></div>
          <div class="frow"><div class="ilabel">ชื่อลูกค้า *</div><input class="finput" type="text" name="customer_name" value="{{ old('customer_name') }}" required></div>
          <div class="frow"><div class="ilabel">ชื่องาน *</div><input class="finput" type="text" name="job_title" value="{{ old('job_title') }}" required></div>
          <div class="frow"><div class="ilabel">สถานที่</div><input class="finput" type="text" name="job_location" value="{{ old('job_location') }}"></div>
          <div class="frow">
            <div class="ilabel">ทีมที่รับผิดชอบ *</div>
            <select class="finput" name="team_name" required>
              <option value="">-- เลือกทีม --</option>
              @foreach($teams as $t)
                <option value="{{ $t['team_name'] }}" {{ old('team_name')===$t['team_name']?'selected':'' }}>{{ $t['team_name'] }} ({{ $t['company'] }})</option>
              @endforeach
            </select>
          </div>
          <div class="frow">
            <div class="ilabel">สถานะ</div>
            <select class="finput" name="status">
              <option value="pending"     {{ old('status','pending')==='pending'?'selected':'' }}>รอดำเนินการ</option>
              <option value="in_progress" {{ old('status')==='in_progress'?'selected':'' }}>กำลังทำ</option>
              <option value="done"        {{ old('status')==='done'?'selected':'' }}>เสร็จสิ้น</option>
              <option value="cancelled"   {{ old('status')==='cancelled'?'selected':'' }}>ยกเลิก</option>
            </select>
          </div>
          <div class="frow"><div class="ilabel">วันเริ่มงาน *</div><input class="finput" type="date" name="start_date" value="{{ old('start_date') }}" required></div>
          <div class="frow"><div class="ilabel">วันสิ้นสุด *</div><input class="finput" type="date" name="end_date" value="{{ old('end_date') }}" required></div>
        </div>
        <div class="frow"><div class="ilabel">หมายเหตุ</div><textarea class="finput" name="note" rows="2" style="resize:vertical">{{ old('note') }}</textarea></div>
        <div class="factions">
          <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-sched')">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึก</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODAL: แก้ไขงาน --}}
<div class="overlay" id="modal-edit-sched" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="p-top">
      <div><div class="p-fullname">แก้ไขข้อมูลงาน</div></div>
      <div class="p-close" onclick="closeModalById('modal-edit-sched')">✕</div>
    </div>
    <div class="p-body">
      @if($errors->any() && old('_edit_sched'))
        <div class="ferr">{{ $errors->first() }}</div>
      @endif
      <form method="POST" id="form-edit-sched" action="">
        @csrf
        <input type="hidden" name="_edit_sched" value="1">
        <div class="igrid">
          <div class="frow"><div class="ilabel">เลข SO *</div><input class="finput" type="text" name="so_number" id="es-so_number" required></div>
          <div class="frow"><div class="ilabel">ชื่อลูกค้า *</div><input class="finput" type="text" name="customer_name" id="es-customer_name" required></div>
          <div class="frow"><div class="ilabel">ชื่องาน *</div><input class="finput" type="text" name="job_title" id="es-job_title" required></div>
          <div class="frow"><div class="ilabel">สถานที่</div><input class="finput" type="text" name="job_location" id="es-job_location"></div>
          <div class="frow">
            <div class="ilabel">ทีมที่รับผิดชอบ *</div>
            <select class="finput" name="team_name" id="es-team_name" required>
              <option value="">-- เลือกทีม --</option>
              @foreach($teams as $t)
                <option value="{{ $t['team_name'] }}">{{ $t['team_name'] }} ({{ $t['company'] }})</option>
              @endforeach
            </select>
          </div>
          <div class="frow">
            <div class="ilabel">สถานะ</div>
            <select class="finput" name="status" id="es-status">
              <option value="pending">รอดำเนินการ</option>
              <option value="in_progress">กำลังทำ</option>
              <option value="done">เสร็จสิ้น</option>
              <option value="cancelled">ยกเลิก</option>
            </select>
          </div>
          <div class="frow"><div class="ilabel">วันเริ่มงาน *</div><input class="finput" type="date" name="start_date" id="es-start_date" required></div>
          <div class="frow"><div class="ilabel">วันสิ้นสุด *</div><input class="finput" type="date" name="end_date" id="es-end_date" required></div>
        </div>
        <div class="frow"><div class="ilabel">หมายเหตุ</div><textarea class="finput" name="note" id="es-note" rows="2" style="resize:vertical"></textarea></div>
        <div class="factions">
          <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-edit-sched')">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึก</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const KNOWN_COMPANIES = @json(collect($companyList)->pluck('code')->values());
const KNOWN_SOFTWARE  = @json(collect($softwareOptions)->values());
const COMPETENCY_LIST = @json($competencyList);
const COMPETENCY_LEVELS = @json($competencyLevels);
const STORAGE_URL = '{{ asset("storage") }}/';

function switchTab(name, btn){
  document.querySelectorAll('.panel').forEach(p=>p.classList.remove('active'));
  document.getElementById('panel-'+name).classList.add('active');
  document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));
  btn.classList.add('active');
}

function openModal(id){ document.getElementById(id).classList.add('open'); }
function closeModalById(id){ document.getElementById(id).classList.remove('open'); }
function closeModal(e){ if(!e || e.target===document.getElementById('overlay')) document.getElementById('overlay').classList.remove('open'); }

// Wrappers: read JSON from data attribute (ปลอดภัยกว่า inline onclick)
function openProfileFromEl(el){
  try {
    const data = JSON.parse(el.dataset.tech);
    openProfile(data);
  } catch(err){
    console.error('openProfile parse error:', err, el.dataset.tech);
    alert('เกิดข้อผิดพลาดในการอ่านข้อมูล');
  }
}
function openEditTechFromEl(el){
  try {
    const data = JSON.parse(el.dataset.tech);
    openEditTech(data);
  } catch(err){
    console.error('openEditTech parse error:', err, el.dataset.tech);
    alert('เกิดข้อผิดพลาดในการอ่านข้อมูล');
  }
}
function openEditSchedFromEl(el){
  try {
    const data = JSON.parse(el.dataset.sched);
    openEditSched(data);
  } catch(err){
    console.error('openEditSched parse error:', err, el.dataset.sched);
    alert('เกิดข้อผิดพลาดในการอ่านข้อมูล');
  }
}

function previewImg(input, previewId, textId){
  const file = input.files[0]; if(!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const img = document.getElementById(previewId);
    img.src = e.target.result; img.style.display = 'block';
    document.getElementById(textId).style.display = 'none';
  };
  reader.readAsDataURL(file);
}

function showCustomCompany(prefix){
  document.getElementById(prefix + '-company-select-row').style.display = 'none';
  document.getElementById(prefix + '-company-custom-row').style.display = '';
  document.getElementById(prefix + '-emp_company').disabled = true;
  const c = document.getElementById(prefix + '-company-custom');
  c.disabled = false; c.focus();
}
function hideCustomCompany(prefix){
  document.getElementById(prefix + '-company-select-row').style.display = '';
  document.getElementById(prefix + '-company-custom-row').style.display = 'none';
  document.getElementById(prefix + '-emp_company').disabled = false;
  const c = document.getElementById(prefix + '-company-custom');
  c.disabled = true; c.value = '';
}

function handlePositionChange(prefix){
  const posEl = document.getElementById(prefix + '-emp_position');
  if(!posEl) return;
  const teamWrap = document.getElementById(prefix + '-team-wrap');
  const headInfo = document.getElementById(prefix + '-head-info');
  const teamSelect = document.getElementById(prefix + '-team-select');
  const isHead = posEl.value === 'หัวหน้าทีม';
  if(isHead){
    if(teamWrap) teamWrap.style.display = 'none';
    if(headInfo) headInfo.style.display = '';
    if(teamSelect) teamSelect.disabled = true;
  } else {
    if(teamWrap) teamWrap.style.display = '';
    if(headInfo) headInfo.style.display = 'none';
    if(teamSelect) teamSelect.disabled = false;
  }
}

function attachHeadAutoFill(formId){
  const form = document.getElementById(formId);
  if(!form) return;
  form.addEventListener('submit', function(e){
    const posEl = form.querySelector('select[name="emp_position"]');
    if(!posEl || posEl.value !== 'หัวหน้าทีม') return;
    const nameEl = form.querySelector('input[name="emp_name"]');
    const teamName = (nameEl?.value || '').trim();
    if(!teamName){
      e.preventDefault();
      alert('กรุณากรอกชื่อ-นามสกุลก่อน (จะใช้เป็นชื่อทีม)');
      nameEl?.focus(); return;
    }
    form.querySelectorAll('input[data-auto-head]').forEach(el => el.remove());
    const h = document.createElement('input');
    h.type='hidden'; h.name='emp_team'; h.value=teamName; h.setAttribute('data-auto-head','1');
    form.appendChild(h);
  });
}
attachHeadAutoFill('form-add-tech');
attachHeadAutoFill('form-edit-tech');

function updateCompClass(sel){
  sel.classList.remove('lv-none','lv-basic','lv-skill','lv-expert');
  sel.classList.add('lv-' + sel.value);
}

// DOB: update พ.ศ. label from date input
function updateBE(prefix){
  const input = document.getElementById(prefix + '-dob');
  const label = document.getElementById(prefix + '-dob-be');
  if(!input || !label) return;
  if(input.value){
    const parts = input.value.split('-');
    if(parts.length === 3){
      const beYear = parseInt(parts[0],10) + 543;
      label.textContent = `พ.ศ. ${parts[2]}/${parts[1]}/${beYear}`;
      return;
    }
  }
  label.textContent = 'พ.ศ. -';
}

let licCounter = { add: 0, et: 0 };

function addLicense(prefix, data){
  data = data || {};
  const idx = licCounter[prefix]++;
  const list = document.getElementById(prefix + '-lic-list');
  const item = document.createElement('div');
  item.className = 'lic-item';
  item.dataset.idx = idx;

  const fileBlock = data.file
    ? `<div class="lic-file-row">
         <a href="${STORAGE_URL}${data.file}" target="_blank" class="lic-file-current">ดูไฟล์เดิม</a>
         <input type="hidden" name="licenses[${idx}][existing_file]" value="${escapeAttr(data.file)}">
         <input type="file" class="lic-file-input" name="licenses[${idx}][file_upload]" accept=".jpg,.jpeg,.png,.webp,.pdf">
       </div>`
    : `<div class="lic-file-row">
         <input type="file" class="lic-file-input" name="licenses[${idx}][file_upload]" accept=".jpg,.jpeg,.png,.webp,.pdf">
       </div>`;

  item.innerHTML = `
    <div class="lic-item-head">
      <span class="lic-num">#${idx+1}</span>
      <button type="button" class="lic-del" onclick="this.closest('.lic-item').remove()">ลบ</button>
    </div>
    <div class="lic-grid">
      <div><div class="ilabel">Title / ชื่อใบรับรอง</div>
        <input class="finput" type="text" name="licenses[${idx}][title]" value="${escapeAttr(data.title||'')}" placeholder="เช่น ใบรับรองช่างไฟฟ้า ระดับ 1"></div>
      <div><div class="ilabel">เลขเอกสาร</div>
        <input class="finput" type="text" name="licenses[${idx}][doc_no]" value="${escapeAttr(data.doc_no||'')}" placeholder="เช่น EL-2567-001"></div>
      <div><div class="ilabel">วันที่ได้รับ</div>
        <input class="finput" type="text" name="licenses[${idx}][date_issued]" value="${escapeAttr(data.date_issued||'')}" placeholder="DD/MM/YYYY (พ.ศ.)"></div>
      <div><div class="ilabel">ไฟล์แนบ (รูป / PDF)</div>${fileBlock}</div>
    </div>
  `;
  list.appendChild(item);
}

function escapeAttr(s){ return String(s).replace(/"/g,'&quot;').replace(/</g,'&lt;'); }

function addCustomSw(prefix){
  const input = document.getElementById(prefix + '-sw-custom');
  const val = input.value.trim();
  if(!val) return;
  const modalId = prefix === 'add' ? 'modal-tech' : 'modal-edit-tech';
  const knownLabels = document.querySelectorAll(`#${modalId} .sw-grid .skill-check`);
  for(const lb of knownLabels){
    const cb = lb.querySelector('input[type=checkbox]');
    if(cb && cb.value === val){
      if(!cb.checked){ cb.checked = true; lb.classList.add('checked'); }
      input.value = ''; return;
    }
  }
  const tags = document.getElementById(prefix + '-sw-custom-tags');
  for(const t of tags.querySelectorAll('input[type=hidden]')){
    if(t.value === val){ input.value = ''; return; }
  }
  const tag = document.createElement('span');
  tag.className = 'sw-tag';
  tag.innerHTML = `<input type="hidden" name="software_tools[]" value="${escapeAttr(val)}">${escapeAttr(val)}<span class="x" onclick="this.parentElement.remove()">✕</span>`;
  tags.appendChild(tag);
  input.value = '';
}

function parseJSON(v){
  if(!v) return null;
  if(typeof v === 'object') return v;
  try { return JSON.parse(v); } catch(e){ return null; }
}

function ceToBeStr(dateStr){
  if(!dateStr) return '-';
  const d = String(dateStr).substring(0,10);
  const parts = d.split('-');
  if(parts.length !== 3) return '-';
  const y = parseInt(parts[0],10) + 543;
  return `${parts[2]}/${parts[1]}/${y}`;
}

function openProfile(m){
  const statusTxt = {active:'พร้อมทำงาน',leave:'ลาออก'};
  const imgSrc = m.img ? STORAGE_URL + m.img : 'https://via.placeholder.com/160/fed7aa/f97316?text=3E';
  document.getElementById('m-img').src = imgSrc;
  document.getElementById('m-img').onerror = function(){ this.src='https://via.placeholder.com/160/fed7aa/f97316?text=3E'; };
  document.getElementById('m-company').textContent  = m.emp_company||'';
  document.getElementById('m-name').textContent     = m.emp_name||m.emp_id||'-';
  document.getElementById('m-name-eng').textContent = m.emp_name_eng||'';
  document.getElementById('m-position').textContent = m.emp_position||'-';
  document.getElementById('m-empid').textContent    = m.emp_id||'-';
  document.getElementById('m-team').textContent     = m.emp_team||'-';
  document.getElementById('m-phone').textContent    = m.emp_phone||'-';
  document.getElementById('m-status').textContent   = statusTxt[m.status]||m.status||'-';
  document.getElementById('m-dob').textContent      = ceToBeStr(m.date_of_birth);

  const skills = (m.emp_skill||'').split(',').map(s=>s.trim()).filter(Boolean);
  document.getElementById('m-skills').innerHTML = skills.length
    ? skills.map(s=>`<span class="sk">${escapeAttr(s)}</span>`).join('')
    : '<span style="color:var(--md);font-size:12px">-</span>';

  const compEl = document.getElementById('m-competencies');
  const comps = parseJSON(m.core_competencies) || {};
  compEl.innerHTML = COMPETENCY_LIST.map(c=>{
    const lv = comps[c.key] || 'none';
    const lvLabel = COMPETENCY_LEVELS[lv] || 'ไม่มี';
    return `<div class="pc-card lv-${lv}">
      <span class="pc-lbl">${c.label}</span>
      <span class="pc-val">${lvLabel}</span>
    </div>`;
  }).join('');

  const licEl = document.getElementById('m-licenses');
  const licenses = parseJSON(m.licenses) || [];
  if(licenses.length){
    licEl.innerHTML = licenses.map(l=>{
      const fileLink = l.file ? `<a href="${STORAGE_URL}${l.file}" target="_blank">ดูไฟล์</a>` : '';
      const parts = [];
      if(l.doc_no) parts.push(`เลขที่: ${escapeAttr(l.doc_no)}`);
      if(l.date_issued) parts.push(`วันที่: ${escapeAttr(l.date_issued)}`);
      if(fileLink) parts.push(fileLink);
      return `<div class="profile-lic-item">
        <div class="profile-lic-title">${escapeAttr(l.title || '(ไม่มีชื่อ)')}</div>
        <div class="profile-lic-meta">${parts.join(' · ') || '-'}</div>
      </div>`;
    }).join('');
  } else {
    licEl.innerHTML = '<span style="color:var(--md);font-size:12px">ไม่มีข้อมูล</span>';
  }

  const sw = parseJSON(m.software_tools) || [];
  document.getElementById('m-software').innerHTML = sw.length
    ? sw.map(s=>`<span class="sk">${escapeAttr(s)}</span>`).join('')
    : '<span style="color:var(--md);font-size:12px">-</span>';

  openModal('overlay');
}

function openEditTech(m){
  document.getElementById('form-edit-tech').action = `/technicians/${m.emp_id}/update`;
  document.getElementById('et-emp_id').value       = m.emp_id||'';
  document.getElementById('et-emp_name').value     = m.emp_name||'';
  document.getElementById('et-emp_name_eng').value = m.emp_name_eng||'';
  document.getElementById('et-emp_phone').value    = m.emp_phone||'';

  // DOB — set date picker (CE format)
  const dob = m.date_of_birth ? String(m.date_of_birth).substring(0,10) : '';
  document.getElementById('et-dob').value = dob;
  updateBE('et');

  document.getElementById('et-emp_position').value = m.emp_position||'';
  document.getElementById('et-team-select').value  = m.emp_team||'';
  document.getElementById('et-status').value       = m.status||'active';

  const company = m.emp_company || '';
  if(company && !KNOWN_COMPANIES.includes(company)){
    showCustomCompany('et');
    document.getElementById('et-company-custom').value = company;
  } else {
    hideCustomCompany('et');
    document.getElementById('et-emp_company').value = company;
  }

  const preview = document.getElementById('et-img-preview');
  const imgText = document.getElementById('et-img-text');
  if(m.img){
    preview.src = STORAGE_URL + m.img; preview.style.display = 'block'; imgText.style.display = 'none';
  } else {
    preview.src = ''; preview.style.display = 'none'; imgText.style.display = 'block';
  }
  document.getElementById('et-img-input').value = '';

  const currentSkills = (m.emp_skill||'').split(',').map(s=>s.trim()).filter(Boolean);
  document.querySelectorAll('#et-skill-grid .skill-check').forEach(label => {
    const cb = label.querySelector('input[type=checkbox]');
    const checked = currentSkills.includes(label.dataset.skill);
    cb.checked = checked; label.classList.toggle('checked', checked);
  });

  const comps = parseJSON(m.core_competencies) || {};
  document.querySelectorAll('#et-comp-grid .comp-select').forEach(sel => {
    const key = sel.dataset.comp;
    sel.value = comps[key] || 'none';
    updateCompClass(sel);
  });

  licCounter.et = 0;
  document.getElementById('et-lic-list').innerHTML = '';
  const licenses = parseJSON(m.licenses) || [];
  licenses.forEach(l => addLicense('et', l));

  const sw = parseJSON(m.software_tools) || [];
  document.querySelectorAll('#et-sw-grid .skill-check').forEach(label => {
    const cb = label.querySelector('input[type=checkbox]');
    const checked = sw.includes(label.dataset.sw);
    cb.checked = checked; label.classList.toggle('checked', checked);
  });
  const etTags = document.getElementById('et-sw-custom-tags');
  etTags.innerHTML = '';
  sw.forEach(s => {
    if(!KNOWN_SOFTWARE.includes(s)){
      const tag = document.createElement('span');
      tag.className = 'sw-tag';
      tag.innerHTML = `<input type="hidden" name="software_tools[]" value="${escapeAttr(s)}">${escapeAttr(s)}<span class="x" onclick="this.parentElement.remove()">✕</span>`;
      etTags.appendChild(tag);
    }
  });

  handlePositionChange('et');
  openModal('modal-edit-tech');
}

function openEditSched(s){
  document.getElementById('form-edit-sched').action = `/schedules/${s.id}/update`;
  document.getElementById('es-so_number').value      = s.so_number||'';
  document.getElementById('es-customer_name').value  = s.customer_name||'';
  document.getElementById('es-job_title').value      = s.job_title||'';
  document.getElementById('es-job_location').value   = s.job_location||'';
  document.getElementById('es-team_name').value      = s.team_name||'';
  document.getElementById('es-start_date').value     = s.start_date ? s.start_date.substring(0,10) : '';
  document.getElementById('es-end_date').value       = s.end_date   ? s.end_date.substring(0,10)   : '';
  document.getElementById('es-status').value         = s.status||'pending';
  document.getElementById('es-note').value           = s.note||'';
  openModal('modal-edit-sched');
}

// Init DOB labels from old input
updateBE('add');

@if($errors->any() && !old('_edit_tech') && !old('_edit_sched') && old('emp_id'))
  openModal('modal-tech');
  handlePositionChange('add');
@elseif($errors->any() && old('_edit_tech'))
  openModal('modal-edit-tech');
  handlePositionChange('et');
@endif

@if($errors->any() && !old('_edit_sched') && old('so_number') && !old('emp_id'))
  openModal('modal-sched');
@elseif($errors->any() && old('_edit_sched'))
  openModal('modal-edit-sched');
@endif
</script>
</body>
</html>