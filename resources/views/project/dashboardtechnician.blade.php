<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Triple 3E Group — ระบบจัดการทักษะช่าง</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@php
  $teams = collect($teams ?? []);
  $technicians = collect($technicians ?? []);
  $schedules = collect($schedules ?? []);
  $customers = collect($customers ?? []);
  $accounts = collect($accounts ?? []);
  $washAlerts = collect($washAlerts ?? []);
  $stats = $stats ?? ['total_tech' => $technicians->count()];
  $availableTeams = collect($availableTeams ?? $teams->pluck('team_name')->filter()->values());

  $jobTypes = $jobTypes ?? [
    'solar_install' => 'ติดตั้ง Solar',
    'solar_wash' => 'ล้างแผง Solar',
    'solar_maintenance' => 'ซ่อมบำรุง Solar',
    'electrical' => 'งานไฟฟ้า',
    'civil' => 'งานโยธา',
    'general' => 'งานทั่วไป',
  ];

  $skillOptions = $skillOptions ?? ['Solar', 'Electrical', 'Civil', 'PLC', 'Inverter', 'Safety', 'Wiring', 'Maintenance'];
  $softwareOptions = $softwareOptions ?? ['AutoCAD', 'SketchUp', 'Excel', 'FusionSolar', 'SolarmanPV', 'MS Project'];
  $competencyList = $competencyList ?? [
    ['key' => 'TEC', 'label' => 'Technical'],
    ['key' => 'EXE', 'label' => 'Execution'],
    ['key' => 'SAF', 'label' => 'Safety'],
    ['key' => 'COM', 'label' => 'Communication'],
    ['key' => 'LDR', 'label' => 'Leadership'],
    ['key' => 'INN', 'label' => 'Innovation'],
  ];
  $competencyLevels = $competencyLevels ?? ['none' => 'ไม่มี', 'basic' => 'พื้นฐาน', 'skill' => 'ชำนาญ', 'expert' => 'เชี่ยวชาญ'];

  $custSummary = $custSummary ?? [
    'solar' => $customers->filter(fn($c) => str_starts_with((string)($c->type_project ?? ''), 'solar')),
    'electrical' => $customers->where('type_project', 'electrical'),
    'civil' => $customers->where('type_project', 'civil'),
    'general' => $customers->filter(fn($c) => !str_starts_with((string)($c->type_project ?? ''), 'solar') && !in_array(($c->type_project ?? ''), ['electrical','civil'], true)),
  ];

  $teamColors = ['#04009A', '#77ACF1', '#3EDBF0', '#0d66d0', '#0aa8bd', '#6461ff', '#1483cc', '#11bfd0'];
  $teamColorMap = [];
  foreach ($teams as $ti => $team) {
    $tn = data_get($team, 'team_name', '');
    if ($tn) $teamColorMap[$tn] = $teamColors[$ti % count($teamColors)];
  }

 $sortedTechnicians = $technicians->sort(function($a, $b) {
    $aHead = ($a->emp_position ?? '') === 'หัวหน้าทีม' ? 0 : 1;
    $bHead = ($b->emp_position ?? '') === 'หัวหน้าทีม' ? 0 : 1;
    // 1) หัวหน้าทั้งหมดขึ้นก่อน ลูกทีมตามทีหลัง
    if ($aHead !== $bHead) return $aHead - $bHead;
    // 2) ภายในกลุ่มเดียวกัน เรียงตามทีม
    $aTeam = $a->emp_team ?? '';
    $bTeam = $b->emp_team ?? '';
    if ($aTeam !== $bTeam) return strcmp($aTeam, $bTeam);
    // 3) สุดท้ายเรียงตามชื่อ
    return strcmp($a->emp_name ?? $a->emp_id ?? '', $b->emp_name ?? $b->emp_id ?? '');
})->values();

  $skillFilters = $technicians
    ->flatMap(fn($t) => collect(explode(',', $t->emp_skill ?? ''))->map(fn($x) => trim($x))->filter())
    ->merge($skillOptions)
    ->unique()
    ->sort()
    ->values();

  $certGroups = collect();
  foreach ($technicians as $tech) {
    foreach (($tech->licenses ?? []) as $lic) {
      $title = trim($lic['title'] ?? '');
      if ($title === '') continue;
      if (!$certGroups->has($title)) $certGroups[$title] = collect();
      $certGroups[$title]->push(['tech' => $tech, 'license' => $lic]);
    }
  }
  $certTotal = $technicians->flatMap(fn($t) => $t->licenses ?? [])->count();
@endphp
<style>
/* ============================================================
   TRIPLE 3E — SANTORINI BLUE DESIGN SYSTEM
   Palette: #F5FEFF (surface) · #AAC0E1 (soft) · #0E2F76 (deep)
   ============================================================ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0 }

:root {
  --navy-900: #0E2F76;
  --navy-800: #173B8E;
  --navy-700: #2049A1;
  --navy-600: #3158B5;
  --navy-500: #4870C8;
  --navy-400: #7090CD;
  --navy-300: #94ABD8;
  --navy-200: #AAC0E1;
  --navy-100: #CFDCEF;
  --navy-50:  #E8EFF8;
  --navy-25:  #F5FEFF;

  --brand-900: var(--navy-900);
  --brand-700: var(--navy-700);
  --brand-500: var(--navy-500);
  --brand-400: var(--navy-400);
  --brand-100: var(--navy-100);
  --brand-50:  var(--navy-50);
  --accent-500: var(--navy-500);
  --accent-100: var(--navy-100);

  --dk:        #0E2F76;
  --text:      #2D3E5F;
  --muted:     #6B7D9B;
  --line:      #D8E2F0;
  --line-soft: #E8EFF8;
  --bg:        #F5FEFF;
  --bg-soft:   #EFF5FB;
  --white:     #FFFFFF;
  --blue:      var(--navy-700);

  --success: #16a34a; --success-bg: #dcfce7; --success-text: #166534;
  --warn:    #d97706; --warn-bg:    #fef3c7; --warn-text:    #92400e;
  --danger:  #dc2626; --danger-bg:  #fee2e2; --danger-text:  #991b1b;
  --info:    #2049A1; --info-bg:    #E8EFF8; --info-text:    #0E2F76;

  --shadow-xs: 0 1px 2px rgba(14,47,118,.04);
  --shadow-sm: 0 2px 6px rgba(14,47,118,.06), 0 1px 2px rgba(14,47,118,.04);
  --shadow-md: 0 6px 16px rgba(14,47,118,.08), 0 2px 6px rgba(14,47,118,.05);
  --shadow-lg: 0 16px 40px rgba(14,47,118,.14), 0 6px 16px rgba(14,47,118,.08);

  --grad-brand: linear-gradient(135deg, #0E2F76 0%, #3158B5 100%);
  --grad-soft:  linear-gradient(135deg, #AAC0E1 0%, #CFDCEF 100%);

  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --radius-xl: 20px;

  --font-serif-thai: 'Noto Serif Thai', 'Sarabun', serif;
}

html { font-size: 16px }
body {
  font-family: 'Noto Sans Thai', sans-serif;
  background: var(--bg);
  color: var(--dk);
  min-height: 100vh;
  line-height: 1.55;
  -webkit-font-smoothing: antialiased;
}
button, input, select, textarea { font-family: 'Noto Sans Thai', sans-serif }
a { color: inherit }

/* ============================================================
   SIDEBAR
   ============================================================ */
.sidebar {
  position: fixed; inset: 0 auto 0 0; width: 260px;
  background: linear-gradient(180deg, #fff 0%, #f7fbff 100%);
  border-right: 1px solid #dbe8fb;
  box-shadow: 10px 0 34px rgba(14,47,118,.07);
  z-index: 100; display: flex; flex-direction: column;
}
.sb-logo {
  display: flex; gap: 12px; align-items: center;
  padding: 24px 20px;
  border-bottom: 1px solid var(--line);
  background: linear-gradient(180deg, #fff 0%, #f4f8ff 100%);
}
.sb-mark {
  width: 46px; height: 46px; border-radius: 16px;
  background: linear-gradient(135deg, #0e2f76 0%, #1d4ed8 100%);
  display: grid; place-items: center;
  color: #fff; font-weight: 800; font-size: 16px;
  box-shadow: 0 12px 26px rgba(14,47,118,.22);
  letter-spacing: .5px;
  transition: transform .28s ease, box-shadow .28s ease;
}
.sb-logo:hover .sb-mark {
  transform: rotate(-4deg) scale(1.06);
  box-shadow: 0 16px 32px rgba(14,47,118,.30);
}
.sb-title { font-weight: 700; font-size: 15px; line-height: 1.2; color: var(--navy-900) }
.sb-sub   { font-size: 11px; color: var(--muted); font-weight: 600; margin-top: 3px }

.sb-tabs { flex: 1; padding: 16px 12px; overflow: auto; display: flex; flex-direction: column; gap: 8px }
.sb-tab {
  width: 100%; display: flex; align-items: center; gap: 12px;
  border: 0; border-radius: 14px; padding: 12px 14px; cursor: pointer;
  font-weight: 600; font-size: 14px; text-align: left;
  color: #5f7399; min-height: 46px;
  position: relative; overflow: hidden; isolation: isolate;
  transition: transform .22s ease, background .22s ease, color .22s ease, box-shadow .22s ease;
}
.sb-tab svg  { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2; flex: 0 0 auto; transition: transform .22s ease }
.sb-tab .label { flex: 1 }
/* indicator bar */
.sb-tab::before {
  content: ''; position: absolute; left: 7px; top: 50%;
  width: 4px; height: 0; border-radius: 999px; background: #fff;
  transform: translateY(-50%); transition: height .24s ease; z-index: 2;
}
/* shine sweep */
.sb-tab::after {
  content: ''; position: absolute; inset: 0;
  background: linear-gradient(110deg, transparent 0%, rgba(255,255,255,.28) 45%, transparent 60%);
  transform: translateX(-120%); transition: transform .75s ease; z-index: 1;
}
.sb-tab > * { position: relative; z-index: 3 }
.sb-tab:hover {
  transform: translateX(5px); background: #eef6ff; color: #0e2f76;
  box-shadow: 0 8px 18px rgba(14,47,118,.08);
}
.sb-tab:hover svg { transform: scale(1.12) rotate(-5deg) }
.sb-tab.active {
  color: #fff;
  background: linear-gradient(135deg, #0e2f76 0%, #174ea6 58%, #2563eb 100%);
  box-shadow: 0 14px 28px rgba(14,47,118,.28);
  animation: activeMenuBreath 2.8s ease-in-out infinite;
}
.sb-tab.active::before { height: 24px }
.sb-tab.active::after  { animation: menuShine 3.2s ease-in-out infinite }
.sb-tab.active svg     { transform: scale(1.08) }

.nav-badge-count {
  min-width: 30px; height: 20px; display: inline-grid; place-items: center;
  border-radius: 999px; font-size: 11px; font-weight: 900;
  background: #e8f1ff; color: #174ea6; border: 1px solid #cfe2ff;
  transition: transform .22s ease, background .22s ease, color .22s ease;
}
.sb-tab:hover .nav-badge-count { transform: scale(1.1) }
.sb-tab.active .nav-badge-count {
  background: rgba(255,255,255,.24); color: #fff; border-color: rgba(255,255,255,.28);
  animation: badgePulse 1.9s ease-in-out infinite;
}

.sb-end { padding: 14px 16px; border-top: 1px solid var(--line); display: grid; gap: 8px; background: var(--navy-25) }
.main   { margin-left: 260px; padding: 24px 32px }
.sb-toggle { display: none }

/* ============================================================
   BUTTONS
   ============================================================ */
.btn {
  border: 0; border-radius: var(--radius-sm);
  padding: 10px 18px; font-size: 14px; font-weight: 600;
  cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  transition: all .18s ease; text-decoration: none;
}
.btn:hover { transform: translateY(-1px) }
.btn-primary, .btn-solar, .borrow-add, .btn-add-lic, .btn-other {
  background: #0d4acf; color: #fff; box-shadow: var(--shadow-sm);
}
.btn-primary:hover, .btn-solar:hover, .borrow-add:hover, .btn-add-lic:hover, .btn-other:hover {
  background: var(--navy-800); box-shadow: var(--shadow-md);
}
.btn-ghost { background: var(--white); color: var(--text); border: 1px solid var(--line) }
.btn-ghost:hover { background: var(--navy-25); border-color: var(--navy-300); color: var(--navy-700) }
.btn-danger { background: var(--danger-bg); color: var(--danger-text); border: 1px solid #fecaca }
.btn-danger:hover { background: #fecaca }
.btn-sm { padding: 6px 12px; font-size: 12px }

/* ============================================================
   FLASH
   ============================================================ */
.flash {
  padding: 13px 18px; border-radius: var(--radius-md);
  margin-bottom: 16px; font-weight: 600;
  transition: opacity .35s, transform .35s, max-height .35s, margin .35s, padding .35s;
}
.flash-success { background: var(--success-bg); color: var(--success-text); border: 1px solid #86efac }
.flash-error   { background: var(--danger-bg);  color: var(--danger-text);  border: 1px solid #fca5a5 }
.flash.is-hiding {
  opacity: 0; transform: translateY(-6px); max-height: 0;
  margin: 0; padding-top: 0; padding-bottom: 0; overflow: hidden;
}

/* ============================================================
   PANELS
   ============================================================ */
.panel { display: none }
.panel.active { display: block; animation: fadeUp .22s ease }

.panel-header {
  display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
  margin-bottom: 20px; padding: 22px 26px;
  background: var(--navy-900); border-radius: var(--radius-lg);
  color: #fff; box-shadow: var(--shadow-md);
  position: relative; overflow: hidden;
}
.panel-header::after {
  content: ''; position: absolute; top: -80px; right: -80px;
  width: 260px; height: 260px;
  background: radial-gradient(circle, rgba(170,192,225,.2) 0%, transparent 70%);
  border-radius: 50%; pointer-events: none;
}
.panel-title   { font-size: 20px; font-weight: 700; flex: 1; position: relative; z-index: 1; letter-spacing: -.01em }
.panel-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1 }

/* ============================================================
   INPUTS
   ============================================================ */
.search-inp, .finput, .cert-search, .roster-search, .borrow-input, .sched-select {
  border: 1px solid var(--line); background: var(--white);
  border-radius: var(--radius-sm); outline: none; color: var(--dk);
  font-weight: 500; transition: border-color .18s, box-shadow .18s;
}
.search-inp { height: 40px; padding: 0 14px; min-width: 250px }
.search-inp:focus, .finput:focus, .cert-search:focus,
.roster-search:focus, .borrow-input:focus, .sched-select:focus {
  border-color: var(--navy-500); box-shadow: 0 0 0 3px rgba(170,192,225,.35);
}
.empty-state {
  text-align: center; padding: 48px 20px; color: var(--muted); font-weight: 500;
  background: var(--white); border: 1px dashed var(--line); border-radius: var(--radius-md);
}

/* ============================================================
   TABLES
   ============================================================ */
.table-wrap {
  background: var(--white); border: 1px solid var(--line);
  border-radius: var(--radius-lg); overflow: auto; box-shadow: var(--shadow-xs);
}
table { width: 100%; min-width: 900px; border-collapse: collapse }
th, td { padding: 13px 16px; text-align: left; border-bottom: 1px solid var(--line-soft); font-size: 14px; vertical-align: middle }
th {
  background: #16408d; color: #fff;
  font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
}
tbody tr:hover td { background: var(--navy-25) }
tbody tr:last-child td { border-bottom: 0 }

/* ============================================================
   BADGES & TAGS
   ============================================================ */
.badge, .job-type-tag, .cust-st, .wash-status-tag, .rtag, .sk, .profile-badge {
  display: inline-flex; align-items: center; gap: 5px;
  border-radius: 999px; padding: 4px 10px; font-size: 12px; font-weight: 600; white-space: nowrap;
}
.jt-solar_install     { background: var(--navy-50);   color: var(--navy-700) }
.jt-solar_wash        { background: var(--navy-100);  color: var(--navy-800) }
.jt-solar_maintenance { background: var(--navy-200);  color: var(--navy-900) }
.jt-electrical { background: #fef3c7; color: #92400e }
.jt-civil      { background: #dcfce7; color: #166534 }
.jt-general    { background: var(--bg-soft); color: var(--muted) }

.cst-quote                     { background: var(--info-bg);    color: var(--info-text) }
.cst-active, .cst-installing   { background: var(--warn-bg);    color: var(--warn-text) }
.cst-done, .cst-success, .cst-closed { background: var(--success-bg); color: var(--success-text) }
.cst-cancel { background: var(--danger-bg); color: var(--danger-text) }
.cst-other  { background: var(--bg-soft);   color: var(--muted) }

/* ============================================================
   VIEW TABS (pill switcher)
   ============================================================ */
.view-tabs {
  display: inline-flex; background: var(--white);
  border: 1px solid var(--line); border-radius: 12px;
  padding: 4px; margin-bottom: 20px; box-shadow: var(--shadow-xs);
}
.view-tabs .dtab {
  border: 0; background: transparent; padding: 9px 20px;
  font-weight: 600; color: var(--muted); cursor: pointer; font-size: 13px;
  border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; transition: all .18s;
}
.view-tabs .dtab:hover { color: var(--navy-700) }
.view-tabs .dtab.active { background: var(--navy-900); color: #fff; font-weight: 700; box-shadow: var(--shadow-sm) }
.view-tabs .dtab.active .nav-badge-count { background: rgba(255,255,255,.22); color: #fff; border-color: rgba(255,255,255,.18) }

/* Customer modal tab bar */
.dtab-bar { display: flex; gap: 0; border-bottom: 1px solid var(--line); margin-bottom: 16px; overflow: auto }
.dtab-bar .dtab {
  border: 0; background: transparent; padding: 10px 18px;
  font-weight: 600; color: var(--muted); cursor: pointer; white-space: nowrap; font-size: 14px; transition: color .18s;
}
.dtab-bar .dtab.active { color: var(--navy-700); border-bottom: 2px solid var(--navy-700); font-weight: 700 }
.dtab-bar .dtab:hover  { color: var(--navy-700) }

/* ============================================================
   ROSTER BOARD
   ============================================================ */
.roster-board, .cert-board {
  background: var(--white); border: 1px solid var(--line);
  border-radius: var(--radius-lg); padding: 24px; margin-bottom: 22px; box-shadow: var(--shadow-xs);
}
.roster-head, .cert-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 20px }
.roster-kicker, .cert-kicker, .sched-eyebrow {
  display: inline-flex; align-items: center; gap: 8px;
  color: var(--navy-700); font-size: 11px; font-weight: 700;
  letter-spacing: .16em; text-transform: uppercase;
  background: var(--navy-50); padding: 5px 12px; border-radius: 999px;
  border: 1px solid var(--navy-100); margin-bottom: 8px;
}
.roster-kicker::before, .cert-kicker::before {
  content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--navy-700);
}
.roster-title, .cert-title { font-size: 26px; font-weight: 700; line-height: 1.15; color: var(--navy-900); margin-top: 4px; letter-spacing: -.015em }
.roster-sub,   .cert-sub   { color: var(--muted); font-size: 14px; font-weight: 500; margin-top: 8px; max-width: 600px }

/* Filter bar */
.roster-filter { background: var(--navy-25); border: 1px solid var(--line); border-radius: var(--radius-md); padding: 14px 16px; margin-bottom: 20px }
.roster-filter-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 7px 0; border-bottom: 1px solid var(--line-soft) }
.roster-filter-row:last-child { border-bottom: 0 }
.roster-filter-label { font-size: 11px; font-weight: 700; color: var(--muted); letter-spacing: .12em; text-transform: uppercase; min-width: 60px }
.roster-chip {
  border: 1px solid var(--line); border-radius: 999px; padding: 6px 14px;
  background: var(--white); color: var(--text); font-weight: 600; font-size: 13px; cursor: pointer; transition: all .18s;
}
.roster-chip:hover  { border-color: var(--navy-300); color: var(--navy-700); background: var(--navy-25) }
.roster-chip.active { background: var(--navy-900); border-color: var(--navy-900); color: #fff }
.roster-search { height: 38px; min-width: 280px; flex: 1; padding: 0 14px; border: 1px solid var(--line) }
.roster-add-tech-btn { white-space: nowrap; height: 42px; padding: 0 18px }

/* ============================================================
   ROSTER GRID — Employee Cards (Dark Navy Folder Style)
   ============================================================ */
#roster-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 16px;
  align-items: stretch;
}
@media (max-width: 1100px) { #roster-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) } }
@media (max-width: 600px)  { #roster-grid { grid-template-columns: 1fr } }

#roster-grid .emp-card {
  background: transparent; border: none; box-shadow: none;
  padding: 22px 0 0; display: flex; flex-direction: column; height: 100%;
  cursor: pointer; overflow: visible; position: relative;
  animation: popIn .45s cubic-bezier(.34,1.4,.64,1) forwards;
  opacity: 0;
  transition: transform .22s ease;
}
#roster-grid .emp-card:hover { transform: translateY(-4px) }
#roster-grid .emp-card .emp-card-stripe { display: none }

#roster-grid .emp-card-body {
  flex: 1; position: relative;
  background: #0C3E9B;
  border: none; border-radius: 0 16px 16px 16px;
  padding: 18px 18px 16px;
  display: flex; flex-direction: column; gap: 12px;
  box-shadow: 0 8px 24px rgba(12,62,155,.35);
  overflow: visible;
}
#roster-grid .emp-card.is-head .emp-card-body {
  background: #0a3fad;
  box-shadow: 0 8px 24px rgba(10,46,122,.45);
}

/* Folder tab */
#roster-grid .emp-card-body::before {
  content: ''; position: absolute; top: -20px; left: 0;
  width: 50%; height: 22px; background: #0f2b5f;
  border-radius: 10px 10px 0 0; z-index: 1;
}
#roster-grid .emp-card.is-head .emp-card-body::before { background: #0a3fad }
#roster-grid .emp-card-body::after { display: none }

/* Hover */
#roster-grid .emp-card:hover .emp-card-body { box-shadow: 0 12px 32px rgba(12,62,155,.5) }

/* Avatar */
#roster-grid .emp-avatar-new {
  width: 48px; height: 48px; border-radius: 50%;
  background: #1e56c0; border: 2px solid rgba(255,255,255,.25);
  color: #fff; font-size: 15px; font-weight: 700;
  flex: 0 0 48px; display: grid; place-items: center; overflow: hidden;
}

/* Top row */
#roster-grid .emp-card-top { display: flex; align-items: flex-start; gap: 12px; position: relative; margin: 0 }
#roster-grid .emp-card-name {
  font-size: 16px; font-weight: 700; color: #fff;
  line-height: 1.25; margin: 0 0 8px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
#roster-grid .emp-card-id {
  font-size: 11px; font-weight: 600; color: #4A6898;
  font-family: 'SF Mono', Consolas, monospace; letter-spacing: .06em; margin: 0;
}
#roster-grid .emp-meta-row {
  display: flex; align-items: center; gap: 6px;
  background: rgba(255,255,255,.13); border-radius: 8px;
  padding: 5px 10px; margin-bottom: 5px; font-size: 12px;
}
#roster-grid .emp-meta-row span  { color: rgba(255,255,255,.65); font-weight: 600; min-width: 30px; font-size: 11px }
#roster-grid .emp-meta-row strong { color: #fff; font-weight: 700; font-size: 13px }

#roster-grid .emp-status-dot {
  position: absolute; top: 2px; right: 2px;
  width: 11px; height: 11px; border-radius: 50%;
  box-shadow: 0 0 0 2.5px #0C3E9B;
}
#roster-grid .emp-card.is-head .emp-status-dot { box-shadow: 0 0 0 2.5px #0a2e7a }

/* Tags */
#roster-grid .emp-card-tags { display: flex; flex-wrap: wrap; gap: 6px; margin: 0 }
#roster-grid .emp-tag {
  font-size: 12px; font-weight: 600; padding: 5px 14px;
  border-radius: 999px; display: inline-flex; align-items: center; gap: 5px;
}
#roster-grid .emp-tag-team,
#roster-grid .emp-tag-head,
#roster-grid .emp-tag-member   { background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.25) }
#roster-grid .emp-tag-active   { background: rgba(34,197,94,.2);   color: #6EE7A0; border: 1px solid rgba(34,197,94,.35) }
#roster-grid .emp-tag-leave    { background: rgba(239,68,68,.2);   color: #FCA5A5; border: 1px solid rgba(239,68,68,.35) }
#roster-grid .ec-nick          { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; color: rgba(255,255,255,.75) }

/* Skills */
#roster-grid .emp-card-skills {
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 6px; margin-top: auto; padding-top: 14px;
  border-top: 1px solid rgba(255,255,255,.15);
  overflow: hidden; align-content: start;
}
#roster-grid .emp-card-skills::before { display: none }

#roster-grid .emp-skill-tag {
  display: flex; align-items: center; justify-content: center;
  background: #fff; color: #0C3E9B;
  border: 1px solid #fff; border-radius: 10px;
  padding: 7px 10px; font-size: 12px; font-weight: 800;
  text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  min-height: 34px; grid-column: span 1;
}
#roster-grid .emp-skill-tag.plus-tag {
  grid-column: 1 / -1; background: rgba(255,255,255,.08); color: rgba(255,255,255,.6);
  border-color: rgba(255,255,255,.12); font-weight: 600;
}
#roster-grid .emp-card:hover .emp-skill-tag {
  background: rgba(255,255,255,.22); border-color: rgba(255,255,255,.35); color: #fff;
}
#roster-grid .emp-card:hover .emp-skill-tag.plus-tag {
  background: rgba(255,255,255,.08); color: rgba(255,255,255,.6);
}

/* ============================================================
   TEAM CARDS (View ทีม)
   ============================================================ */
.team-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 24px;
}
.team-card {
  background: var(--white); border: 1px solid var(--line);
  border-radius: var(--radius-lg); box-shadow: var(--shadow-xs);
  transition: all .2s ease; overflow: hidden;
  display: flex; flex-direction: column; min-height: 280px;
  position: relative;
  animation: teamCardPop .42s ease both;
}
.team-card::before {
  content: ''; position: absolute; inset: 0; pointer-events: none;
  background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,.18) 42%, transparent 58%);
  transform: translateX(-120%); transition: transform .7s ease; z-index: 2;
}
.team-card:hover::before { transform: translateX(120%) }
.team-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); border-color: var(--navy-300) }

.team-head-bar {
  padding: 16px 18px;
  background: linear-gradient(135deg, #0e41af 0%, #043bb7 100%);
  color: #fff; display: flex; align-items: center; gap: 10px;
  position: relative; overflow: hidden;
}
.team-head-bar::after {
  content: ''; position: absolute; top: -50px; right: -50px;
  width: 160px; height: 160px;
  background: radial-gradient(circle, rgba(170,192,225,.22) 0%, transparent 70%);
  border-radius: 50%;
}
.team-head-bar > div { position: relative; z-index: 1 }
.team-title { font-weight: 700; font-size: 17px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: 0 }
.team-meta  { font-size: 11px; color: rgba(255,255,255,.72); font-weight: 500; margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis }

.team-cal-btn {
  position: relative; z-index: 1; border: 1px solid rgba(255,255,255,.22);
  border-radius: 8px; background: rgba(255,255,255,.18); color: #fff;
  font-weight: 600; padding: 8px 13px; font-size: 12px; cursor: pointer; white-space: nowrap;
  flex: 0 0 auto; transition: all .18s; box-shadow: 0 6px 14px rgba(0,0,0,.12);
}
.team-cal-btn:hover { background: #fff; color: #0e2f76; transform: translateY(-1px) }
.badge-count { background: rgba(255,255,255,.22); padding: 2px 9px; border-radius: 999px; font-size: 11px; margin-left: 5px; font-weight: 700 }

.team-body { flex: 1; max-height: 380px; overflow-y: auto; overflow-x: hidden }
.team-body::-webkit-scrollbar { width: 6px }
.team-body::-webkit-scrollbar-thumb { background: var(--navy-100); border-radius: 3px }
.team-body::-webkit-scrollbar-thumb:hover { background: var(--navy-200) }

.member {
  display: grid; grid-template-columns: 40px 1fr auto;
  align-items: center; gap: 12px; padding: 12px 18px;
  border-bottom: 1px solid var(--line-soft); cursor: pointer;
  position: relative; transition: background .18s ease, transform .18s ease;
}
.member:last-child { border-bottom: 0 }
.member:hover { background: var(--navy-25); transform: translateX(4px) }
.member::before {
  content: ''; position: absolute; left: 0; top: 12px; bottom: 12px;
  width: 3px; border-radius: 999px; background: #3b82f6; opacity: 0; transition: opacity .18s;
}
.member:hover::before { opacity: 1 }

.m-av {
  width: 40px; height: 40px; border-radius: 50%; overflow: hidden;
  border: 2px solid var(--navy-100); background: var(--navy-50);
  box-shadow: 0 0 0 3px #e8eff8; transition: transform .18s ease, box-shadow .18s ease;
}
.m-av img { width: 100%; height: 100%; object-fit: cover }
.member:hover .m-av { transform: scale(1.06); box-shadow: 0 0 0 3px #aac0e1 }

.m-info { min-width: 0; display: flex; flex-direction: column; gap: 3px }
.m-name      { font-weight: 600; font-size: 14px; color: var(--navy-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; line-height: 1.3 }
.m-name-row  { display: flex; align-items: center; gap: 6px; min-width: 0 }
.m-role      { font-size: 11px; color: var(--muted); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis }
.m-actions   { display: flex; align-items: center; gap: 6px; flex: 0 0 auto }

.head-tag   { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 5px; flex: 0 0 auto; background: #0e2f76; color: #fff }
.member-tag { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 5px; flex: 0 0 auto; background: #dbeafe; color: #0e2f76 }

.status-dot  { width: 9px; height: 9px; border-radius: 50% }
.st-active { background: var(--success); animation: statusPulse 1.6s infinite }
.st-leave  { background: var(--danger) }
.member .btn-sm { padding: 5px 10px; font-size: 11px; font-weight: 600 }

@media (max-width: 1100px) { .team-grid { grid-template-columns: repeat(2, 1fr) } }
@media (max-width: 640px)  { .team-grid { grid-template-columns: 1fr } }

/* ============================================================
   SCHEDULE BOARD
   ============================================================ */
.sched-board { min-height: calc(100vh - 48px) }
.sched-board-top {
  display: flex; justify-content: space-between; align-items: flex-start;
  gap: 16px; margin-bottom: 20px; padding: 20px 24px;
  background: var(--white); border: 1px solid var(--line);
  border-radius: var(--radius-lg); box-shadow: var(--shadow-xs);
}
.sched-board-title { font-size: 24px; font-weight: 700; line-height: 1.2; color: var(--navy-900); letter-spacing: -.01em }
.sched-board-sub   { margin-top: 8px; color: var(--muted); font-size: 13px; font-weight: 500 }
.sched-controls { display: flex; align-items: center; gap: 8px; flex-wrap: wrap }
.sched-select { height: 38px; min-width: 130px; padding: 0 12px; font-size: 13px; font-weight: 600 }
.sched-mode-group {
  height: 38px; display: flex; overflow: hidden;
  border: 1px solid var(--line); border-radius: var(--radius-sm); background: var(--white);
}
.sched-mode {
  height: 36px; padding: 0 18px; border: 0; background: transparent;
  color: var(--muted); font-size: 13px; font-weight: 600; cursor: pointer; transition: all .18s;
}
.sched-mode:hover  { color: var(--navy-700) }
.sched-mode.active { background: var(--navy-900); color: #fff }
.sched-nav-btn {
  min-width: 38px; height: 38px; padding: 0 14px;
  border: 1px solid var(--line); border-radius: var(--radius-sm);
  background: var(--white); color: var(--text); font-size: 15px; font-weight: 600; cursor: pointer; transition: all .18s;
}
.sched-nav-btn:hover { border-color: var(--navy-400); color: var(--navy-700) }

.sched-calendar-card {
  background: var(--white); border: 1px solid var(--line);
  border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-xs);
}
.sched-month-nav {
  height: 58px; display: flex; align-items: center; padding: 0 24px;
  background: var(--navy-25); border-bottom: 1px solid var(--line);
}
.sched-month-name { flex: 1; font-size: 17px; font-weight: 700; color: var(--navy-900) }

.sched-week-head, .sched-month-grid { display: grid; grid-template-columns: repeat(7, 1fr) }
.sched-week-head {
  background: var(--bg-soft); border-bottom: 1px solid var(--line);
}
.sched-week-head span {
  padding: 13px 4px; text-align: center;
  color: var(--muted); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
}

.sched-day {
  min-height: 124px; position: relative; padding: 11px 9px;
  background: var(--white);
  border-right: 1px solid var(--line-soft); border-bottom: 1px solid var(--line-soft);
}
.sched-day:nth-child(7n) { border-right: 0 }
.sched-day.other { background: var(--bg-soft) }
.sched-day.other .sched-day-num { color: #cbd5e1 }
.sched-day-num { display: block; margin: 0 0 9px; font-size: 14px; font-weight: 700; color: var(--text) }
.sched-day.today .sched-day-num {
  width: 26px; height: 26px; display: grid; place-items: center;
  background: #0a27f9; color: #fff; border-radius: 999px;
}
.sched-day-count {
  position: absolute; top: 9px; right: 9px;
  min-width: 18px; height: 18px; display: grid; place-items: center;
  background: var(--navy-50); color: var(--navy-700);
  border-radius: 6px; padding: 0 6px; font-size: 11px; font-weight: 700;
}

/* Calendar events */
.sched-event {
  display: block; width: 100%; min-height: 42px;
  margin-bottom: 4px; padding: 6px 8px;
  border: 0; border-left: 4px solid transparent; border-radius: 8px;
  text-align: left; cursor: pointer; transition: filter .15s;
  display: flex; flex-direction: column; gap: 2px;
}
.sched-event:hover { filter: brightness(.95) }
.sched-event-title { font-size: 12px; font-weight: 900; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap }
.sched-event-team  { font-size: 10.5px; font-weight: 800; opacity: .75; overflow: hidden; text-overflow: ellipsis; white-space: nowrap }

.evc-1, .evc-5, .evc-9  { background: #e8f1ff; color: #0e2f76; border-left-color: #174ea6 }
.evc-2, .evc-6, .evc-10 { background: #fff0db; color: #8a3a00; border-left-color: #f97316 }
.evc-3, .evc-7, .evc-11 { background: #e0f7ff; color: #075985; border-left-color: #0ea5e9 }
.evc-4, .evc-8, .evc-12 { background: #fff7cc; color: #6f4e00; border-left-color: #eab308 }

.sched-more { padding-left: 4px; color: var(--muted); font-size: 11px; font-weight: 600 }

/* Schedule list */
.sched-list-card { margin-top: 18px; background: var(--white); border: 1px solid var(--line); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-xs) }
.sched-list-head {
  display: flex; justify-content: space-between; align-items: center;
  gap: 14px; padding: 18px 24px; background: var(--navy-25); border-bottom: 1px solid var(--line); flex-wrap: wrap;
}
.sched-list-eyebrow { font-size: 11px; font-weight: 700; color: var(--navy-700); letter-spacing: .14em; text-transform: uppercase }
.sched-list-title   { font-size: 17px; font-weight: 700; color: var(--navy-900); margin-top: 4px; display: flex; align-items: center; gap: 10px }
.sched-list-count   { background: var(--navy-50); color: var(--navy-700); border: 1px solid var(--navy-100); border-radius: 999px; padding: 3px 12px; font-size: 12px; font-weight: 600 }
.sched-list-search  { height: 38px; min-width: 280px }
.sched-list-wrap    { overflow: auto }
.sched-list-table   { width: 100%; min-width: 900px; border-collapse: collapse }
.sched-list-table th {
  background: #093c89; color: #fff;
  font-size: 12px; font-weight: 700; padding: 13px 16px; text-align: left; white-space: nowrap; text-transform: uppercase; letter-spacing: .04em;
}
.sched-list-table td    { padding: 14px 16px; border-bottom: 1px solid var(--line-soft); font-size: 13px; vertical-align: middle }
.sched-list-table tbody tr { cursor: pointer; transition: background .15s }
.sched-list-table tbody tr:hover td { background: var(--navy-25) }
.sched-list-table tbody tr:last-child td { border-bottom: 0 }

.sched-list-so   { font-family: 'SF Mono', Consolas, monospace; font-weight: 700; color: var(--navy-700); background: var(--navy-50); border-radius: 5px; padding: 3px 8px; display: inline-block; font-size: 12px }
.sched-list-cust { font-weight: 700; color: var(--navy-900) }
.sched-list-job  { color: var(--text); font-weight: 500 }
.sched-list-team { display: inline-block; background: var(--navy-50); color: var(--navy-700); border-radius: 6px; padding: 3px 10px; font-size: 12px; font-weight: 600 }
.sched-list-date { font-weight: 600; color: var(--text); white-space: nowrap; font-size: 13px }
.sched-list-date small { display: block; color: var(--muted); font-size: 11px; font-weight: 500; margin-top: 2px }
.sched-list-status { display: inline-flex; align-items: center; gap: 5px; border-radius: 999px; padding: 4px 11px; font-size: 11px; font-weight: 600; white-space: nowrap }
.sls-doing    { background: var(--warn-bg);    color: var(--warn-text) }
.sls-done     { background: var(--success-bg); color: var(--success-text) }
.sls-upcoming { background: var(--info-bg);    color: var(--info-text) }
.sched-list-empty { text-align: center; padding: 40px 16px; color: var(--muted); font-weight: 500 }

/* ============================================================
   CUSTOMER PANEL
   ============================================================ */
.cust-filter-bar {
  display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; padding: 12px;
  background: var(--white); border: 1px solid var(--line); border-radius: var(--radius-md); box-shadow: var(--shadow-xs);
}
.cust-filter-btn {
  border: 1px solid var(--line); background: var(--white); color: var(--text);
  border-radius: 999px; padding: 8px 17px; font-weight: 600; font-size: 13px; cursor: pointer; transition: all .18s;
}
.cust-filter-btn:hover  { border-color: var(--navy-400); color: var(--navy-700); background: var(--navy-25) }
.cust-filter-btn.active { background: var(--navy-900); border-color: var(--navy-900); color: #fff }
.fbc { background: rgba(255,255,255,.22); padding: 1px 7px; border-radius: 999px; font-size: 11px; margin-left: 5px }
.cust-filter-btn:not(.active) .fbc { background: var(--navy-50); color: var(--navy-700) }

.wash-alert-bar { background: var(--white); border: 1px solid var(--line); border-radius: var(--radius-md); padding: 14px 16px; margin-bottom: 16px; box-shadow: var(--shadow-xs) }
.wash-alert-title  { font-size: 13px; color: var(--navy-700); font-weight: 700; margin-bottom: 10px }
.wash-alert-scroll { display: flex; gap: 10px; overflow: auto; padding-bottom: 4px }
.wash-alert-chip { min-width: 200px; background: var(--bg-soft); border: 1px solid var(--line); border-radius: var(--radius-sm); padding: 11px 14px; cursor: pointer; transition: all .18s }
.wash-alert-chip:hover          { border-color: var(--navy-400); transform: translateY(-1px) }
.wash-alert-chip.overdue        { border-color: #fca5a5; background: #fff5f5 }
.wac-name        { font-weight: 700; font-size: 13px; color: var(--navy-900) }
.wac-date        { font-size: 11px; font-weight: 600; margin-top: 3px }
.wac-date.overdue { color: var(--danger) }
.wac-date.soon   { color: var(--warn) }

.cust-name-btn    { border: 0; background: transparent; color: var(--navy-700); font-weight: 700; cursor: pointer; text-align: left; font-size: 14px }
.cust-name-btn:hover { text-decoration: underline }
.cust-date-chip, .wash-cycle-chip { display: inline-flex; align-items: center; border-radius: 999px; padding: 5px 11px; background: var(--navy-50); color: var(--navy-700); font-size: 12px; font-weight: 600; white-space: nowrap }
.wash-cycle-cell      { display: grid; gap: 3px }
.wash-cycle-cell small { color: var(--muted); font-size: 11px; font-weight: 500 }
.cust-muted { color: var(--muted); font-weight: 500 }

#panel-customers .panel-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap }
#panel-customers .panel-actions .btn { height: 40px; white-space: nowrap }

/* ============================================================
   CERTIFICATIONS
   ============================================================ */
#panel-certifications .cert-board { background: transparent; border: 0; padding: 0; box-shadow: none }
#panel-certifications .cert-head {
  border-radius: 18px; padding: 24px 28px;
  background: linear-gradient(135deg, #0e2f76 0%, #1657c4 100%);
  box-shadow: 0 18px 38px rgba(14,47,118,.20);
  position: relative; overflow: hidden; align-items: flex-start;
}
#panel-certifications .cert-head::after {
  content: ''; position: absolute; top: -80px; right: -80px;
  width: 280px; height: 280px;
  background: radial-gradient(circle, rgba(170,192,225,.2) 0%, transparent 70%);
  border-radius: 50%;
}
#panel-certifications .cert-head > *, #panel-certifications .cert-head input { position: relative; z-index: 1 }

.cert-kicker { background: rgba(255,255,255,.14); color: rgba(255,255,255,.95); border-color: rgba(255,255,255,.18) }
.cert-kicker::before { background: #fff }
.cert-title  { font-size: 28px; font-weight: 900; color: #fff }
.cert-sub    { color: rgba(255,255,255,.78) }
.cert-search { height: 44px; width: 320px; padding: 0 16px; border: 1px solid rgba(255,255,255,.35); background: rgba(255,255,255,.96); border-radius: 12px }

#panel-certifications .cert-grid {
  display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-top: 18px;
}
@media (max-width: 1200px) { #panel-certifications .cert-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) } }
@media (max-width: 860px)  { #panel-certifications .cert-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) } }
@media (max-width: 560px)  { #panel-certifications .cert-grid { grid-template-columns: 1fr } }

.cert-card {
  min-height: 142px; border-radius: 16px;
  border: 1px solid #d8e7fb;
  background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
  box-shadow: 0 10px 24px rgba(14,47,118,.08);
  position: relative; text-align: left; cursor: pointer;
  overflow: hidden; transition: transform .2s, box-shadow .2s, border-color .2s;
}
.cert-card::before {
  content: ''; position: absolute; inset: 0 auto 0 0;
  width: 4px; background: linear-gradient(180deg, #1d4ed8, #60a5fa);
}
.cert-card:hover { transform: translateY(-4px); border-color: #93c5fd; box-shadow: 0 16px 34px rgba(14,47,118,.16) }

.cert-card-top {
  min-height: 92px; padding: 18px 18px 14px 22px;
  display: flex; gap: 12px; align-items: flex-start; border-bottom: 0;
}
.cert-icon {
  width: 44px; height: 44px; border-radius: 12px;
  background: #eaf3ff; border: 1px solid #cfe2ff; color: #174ea6;
  display: grid; place-items: center; font-size: 0;
}
.cert-icon::before { content: '★'; font-size: 18px }
.cert-info { flex: 1; min-width: 0 }
.cert-name       { color: #0e2f76; font-size: 16px; font-weight: 900; white-space: normal; line-height: 1.25 }
.cert-count-text { font-size: 12px; color: #64789d; font-weight: 700; margin-top: 3px }
.cert-count      { margin-left: auto; font-size: 34px; font-weight: 900; color: #174ea6; line-height: 1 }
.cert-people {
  min-height: 48px; padding: 11px 18px 14px 22px;
  display: flex; gap: 6px; flex-wrap: wrap;
  border-top: 1px solid #e5eefb; background: #f7fbff;
}
.cert-people span { background: #eaf3ff; color: #174ea6; border: 1px solid #cfe2ff; border-radius: 8px; padding: 4px 8px; font-size: 10px; font-weight: 900 }

/* ============================================================
   MODALS / OVERLAYS
   ============================================================ */
.overlay, .cal-popup-bg {
  display: none; position: fixed; inset: 0;
  background: rgba(14,47,118,.55); backdrop-filter: blur(8px);
  align-items: center; justify-content: center; padding: 16px; z-index: 500;
}
.overlay.open, .cal-popup-bg.open { display: flex }

.pmodal, .cert-modal, .borrow-modal, .borrow-form-modal, .cal-popup {
  background: var(--white); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);
  max-width: calc(100vw - 32px); max-height: 92vh; overflow: auto;
  font-family: var(--font-serif-thai);
}
.pmodal { width: 780px }
.pmodal-wide { width: 980px }
.pmodal-sm   { width: 520px }
.pmodal-strip { height: 4px; background: var(--navy-900) }

.modal-header {
  display: flex; justify-content: space-between; align-items: flex-start;
  gap: 16px; padding: 20px 26px; background: var(--navy-25); border-bottom: 1px solid var(--line);
}
.modal-title    { font-size: 19px; font-weight: 700; color: var(--navy-900) }
.modal-subtitle { font-size: 12px; color: var(--muted); font-weight: 500; margin-top: 2px }

.modal-close, .cert-close, .borrow-x, .cal-popup-close, .tcal-close {
  border: 0; background: var(--white); color: var(--text);
  border-radius: 999px; cursor: pointer; font-weight: 600; display: grid; place-items: center; transition: all .18s;
}
.modal-close { width: 34px; height: 34px; border: 1px solid var(--line) }
.modal-close:hover, .cert-close:hover, .cal-popup-close:hover { background: var(--navy-900); color: #fff; border-color: var(--navy-900) }

.modal-body { padding: 24px 26px }
.finput { width: 100%; padding: 11px 14px; font-size: 14px }
.frow   { margin-bottom: 14px }
.flabel { display: block; font-size: 11px; font-weight: 700; color: var(--navy-700); letter-spacing: .08em; text-transform: uppercase; margin-bottom: 6px }

.fgrid, .sched-grid, .resume-fields, .borrow-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px }
.fcol-full, .sched-full { grid-column: 1 / -1 }
.factions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 22px; padding-top: 18px; border-top: 1px solid var(--line) }
.ferr       { background: var(--danger-bg); color: var(--danger-text); border: 1px solid #fca5a5; border-radius: var(--radius-sm); padding: 11px 15px; font-weight: 600; margin-bottom: 14px }
.finfo-box, .head-info-box { background: var(--navy-50); border: 1px solid var(--navy-100); color: var(--navy-700); border-radius: var(--radius-sm); padding: 11px 15px; font-size: 13px; font-weight: 500 }

/* Resume form */
.resume-top   { display: flex; gap: 24px; padding: 22px 26px; background: var(--navy-25); border-bottom: 1px solid var(--line) }
.photo-col    { display: flex; flex-direction: column; align-items: center; gap: 8px }
.photo-box    { width: 114px; height: 140px; border: 2px dashed var(--navy-400); border-radius: var(--radius-md); background: var(--white); display: grid; place-items: center; position: relative; overflow: hidden; cursor: pointer }
.photo-box img.resume-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: none }
.photo-box img.resume-img.has-img { display: block }
.photo-overlay { position: absolute; inset: 0; background: rgba(14,47,118,.72); display: grid; place-items: center; color: #fff; font-size: 12px; font-weight: 600; opacity: 0; transition: opacity .18s }
.photo-box:hover .photo-overlay { opacity: 1 }
.photo-placeholder { text-align: center; color: var(--muted); font-size: 11px; font-weight: 600 }
.photo-placeholder svg { width: 30px; height: 30px }
.photo-label, .emp-id-note { font-size: 11px; color: var(--muted); font-weight: 500 }
.resume-badge-abs { position: absolute; top: -7px; right: -7px; background: var(--navy-900); color: #fff; font-size: 9px; font-weight: 700; padding: 3px 7px; border-radius: 6px; z-index: 2 }
.dob-row { display: flex; gap: 8px }
.dob-be { min-width: 124px; background: var(--navy-50); color: var(--navy-700); border: 1px solid var(--navy-100); border-radius: var(--radius-sm); padding: 11px; text-align: center; font-size: 13px; font-weight: 600 }

.section-h { font-size: 13px; font-weight: 700; color: var(--navy-700); margin: 20px 0 12px; padding-bottom: 8px; border-bottom: 1px solid var(--line); text-transform: uppercase; letter-spacing: .06em }

.skill-grid, .sw-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 7px }
.skill-check { display: flex; align-items: center; gap: 8px; border: 1px solid var(--line); border-radius: var(--radius-sm); padding: 10px 12px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all .18s }
.skill-check:hover   { border-color: var(--navy-400); background: var(--navy-25) }
.skill-check.checked { border-color: var(--navy-900); background: var(--navy-900); color: #fff }

.comp-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px }
.comp-card { border: 1px solid var(--line); border-radius: var(--radius-md); padding: 13px; background: var(--white) }
.comp-head  { display: flex; gap: 8px; align-items: center; margin-bottom: 8px }
.comp-label { font-size: 13px; font-weight: 700; color: var(--navy-900) }
.comp-code  { margin-left: auto; background: var(--bg-soft); border-radius: 5px; padding: 2px 7px; font-size: 10px; color: var(--muted); font-weight: 700 }
.comp-select { width: 100%; border: 1px solid var(--line); border-radius: var(--radius-sm); padding: 9px; font-size: 13px }
.comp-select.lv-basic  { background: var(--warn-bg);    color: var(--warn-text);    border-color: #fde68a }
.comp-select.lv-skill  { background: var(--info-bg);    color: var(--info-text);    border-color: var(--navy-200) }
.comp-select.lv-expert { background: var(--success-bg); color: var(--success-text); border-color: #86efac; font-weight: 700 }

.sw-custom-row  { display: flex; gap: 8px; margin-top: 10px }
.sw-custom-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px }
.sw-tag { background: var(--navy-900); color: #fff; border-radius: 999px; padding: 5px 13px; font-size: 12px; font-weight: 600 }
.sw-tag .x { cursor: pointer; margin-left: 6px }

.btn-add-lic { width: 100%; padding: 12px; margin-top: 10px }
.btn-other   { padding: 10px 17px }

.lic-list { display: grid; gap: 10px }
.lic-item { background: var(--navy-25); border: 1px solid var(--line); border-radius: var(--radius-md); padding: 14px }
.lic-item-head { display: flex; align-items: center; gap: 8px; margin-bottom: 10px }
.lic-num  { background: var(--navy-900); color: #fff; border-radius: 999px; padding: 3px 11px; font-size: 12px; font-weight: 700 }
.lic-del  { margin-left: auto; background: var(--danger); color: #fff; border: 0; border-radius: 6px; padding: 5px 12px; font-weight: 600; font-size: 12px; cursor: pointer }
.lic-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 9px }
.lic-file-link { display: inline-block; margin-top: 8px; background: var(--success-bg); color: var(--success-text); border: 1px solid #86efac; border-radius: 6px; padding: 5px 12px; font-size: 12px; font-weight: 600; text-decoration: none }

/* ============================================================
   PROFILE V2 MODAL
   ============================================================ */
.profile-v2 { width: 1020px; padding: 0; overflow: hidden; border-radius: var(--radius-lg) }
.profile-v2-layout { display: grid; grid-template-columns: 290px 1fr }

.profile-v2-left {
  background: var(--navy-900); padding: 32px 24px;
  display: flex; flex-direction: column; align-items: center; position: relative;
}
.profile-v2-left::after {
  content: ''; position: absolute; top: -100px; right: -100px;
  width: 280px; height: 280px;
  background: radial-gradient(circle, rgba(170,192,225,.18) 0%, transparent 70%);
  border-radius: 50%; pointer-events: none;
}
.profile-v2-left > * { position: relative; z-index: 1 }

.profile-v2-photo {
  width: 132px; height: 132px; border-radius: 50%;
  border: 4px solid rgba(255,255,255,.4); overflow: hidden;
  background: rgba(255,255,255,.15); display: grid; place-items: center;
  font-size: 42px; font-weight: 700; color: #fff; margin-bottom: 18px; flex: 0 0 auto;
}
.profile-v2-photo img { width: 100%; height: 100%; object-fit: cover; display: block }
.profile-v2-name    { font-size: 22px; font-weight: 700; color: #fff; text-align: center; margin-bottom: 4px; line-height: 1.25 }
.profile-v2-nameeng { font-size: 14px; color: rgba(255,255,255,.78); font-weight: 600; text-align: center; margin-bottom: 16px }
.profile-v2-status  { display: inline-flex; align-items: center; gap: 7px; border-radius: 999px; padding: 7px 17px; font-size: 13px; font-weight: 700; margin-bottom: 18px }
.pv2-status-active { background: var(--success-bg); color: var(--success-text) }
.pv2-status-leave  { background: var(--danger-bg);  color: var(--danger-text) }
.pv2-st-dot        { width: 8px; height: 8px; border-radius: 50% }
.pv2-dot-active    { background: var(--success) }
.pv2-dot-leave     { background: var(--danger) }

.profile-v2-rolecard { width: 100%; margin-top: 6px; border-radius: 10px; overflow: hidden; border: 1px solid rgba(255,255,255,.18) }
.pv2-rolerow {
  display: flex; align-items: center; gap: 0; padding: 10px 13px;
  background: rgba(255,255,255,.92); border-bottom: 1px solid #E8EFF8;
}
.pv2-rolerow:last-child { border-bottom: 0 }
.pv2-rolekey { font-size: 11px; font-weight: 700; color: #6B7D9B; letter-spacing: .08em; text-transform: uppercase; white-space: nowrap; min-width: 64px }
.pv2-roleval { font-size: 13px; font-weight: 700; color: #0E2F76; text-align: left; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap }

.profile-v2-infolist { width: 100%; margin-top: 10px; border-radius: 10px; overflow: hidden; border: 1px solid rgba(255,255,255,.18); display: flex; flex-direction: column; gap: 0 }
.pv2-inforow {
  display: flex; align-items: center; justify-content: space-between;
  padding: 10px 13px; background: #fff; border-bottom: 1px solid #E8EFF8; border-radius: 0; margin: 0;
}
.pv2-inforow:nth-child(even) { background: #F5FEFF }
.pv2-inforow:last-child { border-bottom: 0 }
.pv2-infokey { font-size: 11px; font-weight: 700; color: #6B7D9B; letter-spacing: .08em; text-transform: uppercase }
.pv2-infoval { font-size: 13px; font-weight: 700; color: #0E2F76; text-align: left; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap }

.profile-v2-right  { background: var(--white); padding: 20px; overflow: auto; max-height: 80vh }
.pv2-sections      { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 0 }
.pv2-section       { background: var(--navy-25); border: 1px solid var(--line); border-radius: var(--radius-md); padding: 16px }
.pv2-section-label { font-size: 11px; font-weight: 700; color: var(--muted); letter-spacing: .10em; margin-bottom: 12px; text-transform: uppercase }
.pv2-tags          { display: flex; flex-wrap: wrap; gap: 6px }
.pv2-tag    { background: var(--navy-50);  color: var(--navy-700); border: 1px solid var(--navy-100); border-radius: 6px; padding: 5px 11px; font-size: 13px; font-weight: 600 }
.pv2-tag-sw { background: var(--navy-100); color: var(--navy-800); border-color: var(--navy-200) }

.pv2-comp-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 7px }
.pv2-comp-item { display: flex; justify-content: space-between; align-items: center; border-radius: var(--radius-sm); padding: 9px 12px; background: var(--white); border: 1px solid var(--line); gap: 10px }
.pv2-comp-key  { font-size: 13px; font-weight: 700; color: var(--navy-900) }
.pv2-comp-val  { font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 5px; white-space: nowrap }
.cv-none   { background: var(--bg-soft);   color: var(--muted) }
.cv-basic  { background: var(--warn-bg);    color: var(--warn-text) }
.cv-skill  { background: var(--info-bg);    color: var(--info-text) }
.cv-expert { background: var(--success-bg); color: var(--success-text) }

.pv2-lic-item { background: var(--navy-25); border: 1px solid var(--line); border-radius: var(--radius-sm); padding: 13px 15px; margin-bottom: 8px }
.pv2-lic-name { font-weight: 700; font-size: 14px; color: var(--navy-900) }
.pv2-lic-meta { font-size: 12px; color: var(--muted); font-weight: 500; margin-top: 4px }
.pv2-muted    { color: var(--muted); font-weight: 500; font-size: 13px }

.pv2-close-btn {
  position: absolute; top: 14px; right: 14px;
  width: 36px; height: 36px; border: 1px solid rgba(255,255,255,.25);
  background: rgba(255,255,255,.12); color: #fff; border-radius: 50%;
  cursor: pointer; font-weight: 600; font-size: 18px; display: grid; place-items: center;
  transition: background .18s; z-index: 2;
}
.pv2-close-btn:hover { background: rgba(255,255,255,.24) }

.dtab-panel { display: none }
.dtab-panel.active { display: block }
.pinfo-grid   { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px }
.pinfo-card   { background: var(--navy-25); border: 1px solid var(--line); border-radius: var(--radius-sm); padding: 13px }
.pinfo-label  { font-size: 11px; font-weight: 700; color: var(--muted); letter-spacing: .06em; text-transform: uppercase }
.pinfo-val    { font-weight: 600; color: var(--navy-900); margin-top: 3px }

/* ============================================================
   TIMELINE CALENDAR
   ============================================================ */
.tl-wrap   { background: var(--white); border: 1px solid var(--line); border-radius: var(--radius-md); overflow: hidden }
.tl-header {
  display: flex; align-items: center; gap: 9px; padding: 12px 16px;
  background: var(--navy-25); border-bottom: 1px solid var(--line);
}
.tl-mnav-btn, .tl-today-btn, .tl-clear-btn {
  border: 1px solid var(--line); background: var(--white); border-radius: var(--radius-sm); font-weight: 600; cursor: pointer; transition: all .18s;
}
.tl-mnav-btn           { width: 34px; height: 34px }
.tl-today-btn, .tl-clear-btn { padding: 8px 13px; font-size: 13px }
.tl-mnav-btn:hover, .tl-today-btn:hover, .tl-clear-btn:hover { border-color: var(--navy-400); color: var(--navy-700) }
.tl-mname    { flex: 1; text-align: center; font-weight: 700; color: var(--navy-900) }
.tl-team-info    { padding: 10px 16px; background: var(--navy-50); border-bottom: 1px solid var(--line); font-size: 12px; color: var(--navy-700); font-weight: 600 }
.tl-team-info.no-team { background: var(--warn-bg); color: var(--warn-text) }

.tl-months       { display: grid; grid-template-columns: 1fr 1fr }
.tl-month-block  { padding: 14px }
.tl-month-block:first-child { border-right: 1px solid var(--line) }
.tl-month-title  { text-align: center; font-weight: 700; margin-bottom: 8px; color: var(--navy-900) }
.tl-dhdrs, .tl-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 3px }
.tl-dhdr         { text-align: center; color: var(--muted); font-size: 11px; font-weight: 700 }
.tl-dhdr.weekend { color: var(--danger) }
.tl-cell {
  position: relative; aspect-ratio: 1; min-height: 40px;
  border: 1px solid var(--line); border-radius: 6px; background: var(--white);
  display: grid; place-items: center; cursor: pointer; transition: all .15s;
}
.tl-cell:hover   { border-color: var(--navy-400); background: var(--navy-25) }
.tl-other        { opacity: .25; pointer-events: none }
.tl-today        { border-color: var(--navy-900); border-width: 2px }
.tl-busy         { background: repeating-linear-gradient(45deg,#fee2e2,#fee2e2 4px,#fecaca 4px,#fecaca 8px); border-color: #fca5a5; pointer-events: none }
.tl-sel-s, .tl-sel-e { background: var(--navy-900) !important; color: #fff; border-color: var(--navy-900) !important }
.tl-in-range     { background: var(--navy-100) !important; border-color: var(--navy-400) !important }
.tl-d { font-size: 13px; font-weight: 600 }
.tl-busy-bar     { position: absolute; bottom: 3px; left: 3px; right: 3px; height: 3px; background: var(--danger); border-radius: 2px }
.tl-jobs-count   { position: absolute; top: 2px; right: 2px; background: var(--white); color: var(--danger); border-radius: 6px; padding: 0 4px; font-size: 9px; font-weight: 700 }

.tl-summary, .tl-legend { padding: 12px 16px; border-top: 1px solid var(--line); display: flex; gap: 10px; align-items: center; flex-wrap: wrap }
.tl-summary-info { flex: 1; color: var(--muted); font-size: 13px; font-weight: 500 }
.tl-summary-info strong { color: var(--navy-700); font-weight: 700 }
.tl-summary-warn { color: var(--danger); font-weight: 600 }
.tl-legend       { font-size: 11px; color: var(--muted); font-weight: 500 }
.tl-leg          { display: inline-flex; gap: 5px; align-items: center }
.tl-leg-box      { width: 14px; height: 14px; border-radius: 4px; border: 1px solid var(--line) }
.tl-leg-box.busy  { background: #fecaca }
.tl-leg-box.sel   { background: var(--navy-900) }
.tl-leg-box.range { background: var(--navy-100) }
.tl-leg-box.today { border-color: var(--navy-900); border-width: 2px }

/* ============================================================
   CERT MODAL
   ============================================================ */
.cert-modal {
  width: min(860px, calc(100vw - 32px)); border-radius: var(--radius-lg);
  overflow: hidden; background: var(--white); border: 1px solid var(--line); box-shadow: var(--shadow-lg); position: relative;
}
.cert-close, .borrow-x { position: absolute; top: 20px; right: 20px; width: 40px; height: 40px; font-size: 22px; z-index: 2 }
.cert-modal-head {
  padding: 28px 32px 26px; background: var(--navy-900); color: #fff; position: relative; overflow: hidden;
}
.cert-modal-head::after {
  content: ''; position: absolute; top: -80px; right: -80px; width: 280px; height: 280px;
  background: radial-gradient(circle, rgba(170,192,225,.18) 0%, transparent 70%); border-radius: 50%;
}
.cert-modal-head > * { position: relative; z-index: 1 }
.cert-modal-kicker { color: rgba(255,255,255,.78); font-size: 11px; font-weight: 700; letter-spacing: .16em }
.cert-modal-title  { margin-top: 5px; color: #fff; font-size: 24px; font-weight: 700; line-height: 1.2; padding-right: 50px }
.cert-modal-sub    { display: inline-flex; margin-top: 13px; padding: 5px 14px; border-radius: 999px; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.22); color: #fff; font-size: 12px; font-weight: 600 }
.cert-holder-list  { padding: 22px; display: grid; gap: 10px; max-height: 60vh; overflow: auto }
.cert-holder {
  display: grid; grid-template-columns: 48px 1fr auto;
  align-items: center; gap: 14px; padding: 14px;
  background: var(--white); border: 1px solid var(--line); border-radius: var(--radius-md); transition: all .18s;
}
.cert-holder:hover { border-color: var(--navy-400); background: var(--navy-25) }
.cert-holder-avatar { width: 48px; height: 48px; border-radius: 12px; background: var(--navy-50); color: var(--navy-700); display: grid; place-items: center; font-size: 14px; font-weight: 700; border: 1px solid var(--navy-100) }
.cert-holder-main { min-width: 0 }
.cert-holder-name { color: var(--navy-900); font-size: 14px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis }
.cert-holder-meta { margin-top: 3px; color: var(--muted); font-size: 12px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis }
.cert-file-link {
  display: inline-flex; align-items: center; justify-content: center;
  min-height: 36px; padding: 9px 16px; border-radius: var(--radius-sm);
  background: var(--navy-900); color: #fff; text-decoration: none; font-size: 12px; font-weight: 600; transition: background .18s;
}
.cert-file-link:hover { background: var(--navy-800) }

/* ============================================================
   BORROW MODAL
   ============================================================ */
.borrow-modal { width: 1120px; padding: 26px; position: relative }
.borrow-head  { display: flex; justify-content: space-between; gap: 18px; margin-bottom: 16px }
.borrow-title { font-size: 22px; font-weight: 700; color: var(--navy-900) }
.borrow-sub   { color: var(--muted); font-weight: 500; margin-top: 4px }
.borrow-tools { display: flex; gap: 10px }
.borrow-input { height: 40px; padding: 0 12px }
.borrow-add   { border: 0; border-radius: var(--radius-sm); padding: 10px 17px; font-weight: 600; margin-bottom: 14px; cursor: pointer }
.borrow-table-wrap { overflow: auto; border: 1px solid var(--line); border-radius: var(--radius-sm) }
.borrow-table    { min-width: 1040px }
.borrow-table th { background: var(--navy-25); color: var(--navy-700) }
.borrow-status { border-radius: 6px; padding: 4px 10px; font-size: 12px; font-weight: 600 }
.borrow-ok   { background: var(--success-bg); color: var(--success-text) }
.borrow-wait { background: var(--warn-bg);    color: var(--warn-text) }
.borrow-no   { background: var(--danger-bg);  color: var(--danger-text) }
.borrow-action { width: 31px; height: 31px; border: 0; border-radius: 6px; color: #fff; cursor: pointer; font-weight: 600 }
.borrow-edit   { background: var(--navy-700) }
.borrow-delete { background: var(--danger) }
.borrow-foot   { display: flex; justify-content: space-between; margin-top: 16px; color: var(--muted); font-weight: 500 }
.borrow-page   { background: var(--navy-700); color: #fff; border: 0; border-radius: 6px; padding: 7px 13px }
.borrow-nested { z-index: 900 }
.borrow-form-modal { width: 690px; padding: 26px; position: relative }
.borrow-form-title { font-size: 20px; font-weight: 700; color: var(--navy-700); margin-bottom: 16px }

/* ============================================================
   AUTOCOMPLETE
   ============================================================ */
.autocomp { position: relative }
.autocomp-list {
  display: none; position: absolute; top: 100%; left: 0; right: 0;
  background: var(--white); border: 1px solid var(--navy-500); border-top: 0;
  border-radius: 0 0 var(--radius-sm) var(--radius-sm); box-shadow: var(--shadow-md); z-index: 20; max-height: 220px; overflow: auto;
}
.autocomp-list.open { display: block }
.ac-item { padding: 11px 14px; border-bottom: 1px solid var(--line-soft); cursor: pointer; transition: background .15s }
.ac-item:last-child { border-bottom: 0 }
.ac-item:hover, .ac-item.ac-active { background: var(--navy-900); color: #fff }
.ac-item-name { font-weight: 600 }
.ac-item-meta { font-size: 12px; color: var(--muted); margin-top: 2px }
.ac-item:hover .ac-item-meta, .ac-active .ac-item-meta { color: rgba(255,255,255,.78) }
.cust-banner { display: none; margin-top: 8px; border-radius: var(--radius-sm); padding: 11px 14px; font-size: 13px; font-weight: 500 }
.cust-banner-old { background: var(--warn-bg);    color: var(--warn-text);    border: 1px solid #fde68a }
.cust-banner-new { background: var(--success-bg); color: var(--success-text); border: 1px solid #86efac }
.acc-pw-wrap { display: flex; gap: 6px; align-items: center; flex-wrap: wrap }

/* ============================================================
   TEAM CALENDAR (FULLSCREEN)
   ============================================================ */
.tcal-overlay { display: none; position: fixed; inset: 0; z-index: 700; background: rgba(14,47,118,.78) }
.tcal-overlay.open { display: flex }
.tcal-fs { width: 100%; height: 100%; background: var(--bg); display: flex; flex-direction: column }
.tcal-header {
  background: var(--navy-900); color: #fff; padding: 20px 30px;
  display: flex; gap: 16px; align-items: center; flex-wrap: wrap; position: relative; overflow: hidden;
}
.tcal-header::after {
  content: ''; position: absolute; top: -80px; right: -80px; width: 280px; height: 280px;
  background: radial-gradient(circle, rgba(170,192,225,.18) 0%, transparent 70%); border-radius: 50%;
}
.tcal-header > * { position: relative; z-index: 1 }
.tcal-icon { width: 48px; height: 48px; border-radius: 12px; background: rgba(255,255,255,.18); display: grid; place-items: center; backdrop-filter: blur(8px) }
.tcal-icon svg { width: 22px; height: 22px; stroke: #fff; fill: none; stroke-width: 2 }
.tcal-title-block { flex: 1; min-width: 0 }
.tcal-eyebrow { font-size: 11px; color: rgba(255,255,255,.78); font-weight: 700; letter-spacing: .12em; text-transform: uppercase }
.tcal-title   { font-size: 22px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis }
.tcal-stat    { display: inline-flex; margin-top: 5px; background: rgba(255,255,255,.18); border-radius: 999px; padding: 4px 13px; font-size: 12px; font-weight: 600 }
.tcal-close   { width: 42px; height: 42px; background: rgba(255,255,255,.14); color: #fff; border: 1px solid rgba(255,255,255,.22) }
.tcal-close:hover { background: rgba(255,255,255,.26) }
.tcal-body    { flex: 1; overflow: auto; padding: 24px }
.tcal-content { max-width: 1400px; margin: 0 auto }
.tcal-content .sched-board { background: transparent; min-height: 0; padding: 0 }

/* Cal selector */
.cal-sel-bar { display: flex; background: var(--white); border: 1px solid var(--line); border-radius: var(--radius-md); overflow: hidden; margin-bottom: 14px }
.cal-sel-btn { flex: 1; border: 0; background: var(--white); padding: 14px 16px; display: flex; gap: 10px; align-items: center; cursor: pointer; text-align: left; transition: background .15s }
.cal-sel-btn:hover  { background: var(--navy-25) }
.cal-sel-btn.active { background: var(--navy-50) }
.cal-sel-icon { width: 32px; height: 32px; border-radius: 8px; background: var(--navy-700); display: grid; place-items: center }
.cal-sel-icon.gray  { background: var(--bg-soft) }
.cal-sel-icon svg   { width: 15px; height: 15px; stroke: #fff; fill: none; stroke-width: 2 }
.cal-sel-icon.gray svg { stroke: var(--muted) }
.cal-sel-lbl { font-size: 13px; font-weight: 700; color: var(--navy-900) }
.cal-sel-sub { font-size: 11px; color: var(--muted); font-weight: 500 }

.cal-wrap      { background: var(--white); border: 1px solid var(--line); border-radius: var(--radius-lg); overflow: hidden }
.tcal-info-strip { background: var(--navy-50); border-bottom: 1px solid var(--line); color: var(--navy-700); font-weight: 600; padding: 11px 16px }
.cal-months    { display: grid; grid-template-columns: 1fr 1fr }
.cal-block     { padding: 16px }
.cal-block:first-child { border-right: 1px solid var(--line) }
.cal-mnav      { display: flex; align-items: center; gap: 8px; margin-bottom: 10px }
.cal-mname     { flex: 1; text-align: center; font-weight: 700; color: var(--navy-900) }
.cal-mnav-btn  { width: 32px; height: 32px; border: 1px solid var(--line); border-radius: var(--radius-sm); background: var(--white); cursor: pointer; font-weight: 600 }
.cal-mnav-btn:hover      { border-color: var(--navy-400); color: var(--navy-700) }
.cal-mnav-btn.invisible  { visibility: hidden }
.cal-dhdrs, .cal-dgrid  { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px }
.cal-dhdr { text-align: center; font-size: 12px; font-weight: 700; color: var(--muted) }
.cal-day {
  min-height: 54px; border: 1px solid var(--line); border-radius: var(--radius-sm);
  display: grid; place-items: center; position: relative; cursor: pointer; background: var(--white); transition: all .15s;
}
.cal-day:hover, .cal-day.cal-sel-s, .cal-day.cal-sel-e { background: var(--navy-50); border-color: var(--navy-700) }
.cal-other { border-color: transparent; pointer-events: none }
.cal-other .cal-di { display: none }
.cal-di     { font-weight: 600; color: var(--text) }
.cal-today  { border-color: var(--navy-900); border-width: 2px }
.cal-in-range { background: var(--navy-100); border-color: var(--navy-400) }
.cal-evdots { position: absolute; bottom: 7px; left: 50%; transform: translateX(-50%); display: flex; gap: 2px; align-items: center }
.cal-evdot  { width: 6px; height: 6px; border-radius: 50%; background: var(--navy-700) }
.cal-evcount { font-size: 9px; color: var(--navy-700); font-weight: 700 }
.cal-legend, .cal-footer { display: flex; gap: 14px; align-items: center; flex-wrap: wrap; padding: 12px 16px; border-top: 1px solid var(--line); font-size: 12px; color: var(--muted); font-weight: 500 }
.cal-footer-note { flex: 1 }

.cal-popup      { width: 520px }
.cal-popup-strip { height: 4px; background: var(--navy-900) }
.cal-popup-head { display: flex; gap: 8px; align-items: center; padding: 14px 18px; border-bottom: 1px solid var(--line) }
.cal-popup-date  { flex: 1; background: var(--navy-50); color: var(--navy-700); border-radius: var(--radius-sm); padding: 7px 12px; text-align: center; font-weight: 700 }
.cal-popup-count { background: var(--navy-900); color: #fff; border-radius: 999px; padding: 4px 11px; font-size: 12px; font-weight: 600 }
.cal-popup-close { width: 30px; height: 30px; border: 1px solid var(--line) }
.cal-popup-inner { padding: 16px }
.cal-ev-card { border: 1px solid var(--line); border-radius: var(--radius-md); padding: 13px 14px 13px 19px; margin-bottom: 9px; position: relative }
.cal-ev-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--navy-700); border-radius: var(--radius-md) 0 0 var(--radius-md) }
.cal-ev-top  { display: flex; justify-content: space-between; gap: 8px }
.cal-so      { background: var(--navy-50); color: var(--navy-700); border-radius: 6px; padding: 3px 8px; font-size: 10px; font-weight: 700 }
.cal-ev-cust { font-weight: 700; color: var(--navy-900) }
.cal-ev-job  { font-size: 12px; color: var(--muted); font-weight: 500; margin: 3px 0 8px }
.cal-ev-meta { display: grid; grid-template-columns: auto 1fr; gap: 4px 12px; font-size: 12px }
.cal-ev-ml   { color: var(--muted); font-weight: 600 }
.cal-ev-mv   { font-weight: 500; color: var(--text) }

/* Ripple */
.sb-ripple { position: absolute; width: 10px; height: 10px; border-radius: 999px; background: rgba(255,255,255,.45); transform: translate(-50%, -50%) scale(1); animation: sbRipple .55s ease-out forwards; pointer-events: none; z-index: 2 }

/* ============================================================
   KEYFRAMES
   ============================================================ */
@keyframes fadeUp       { from { opacity: 0; transform: translateY(8px) } to { opacity: 1; transform: none } }
@keyframes popIn        { 0% { opacity: 0; transform: scale(.75) translateY(14px) } 65% { opacity: 1; transform: scale(1.03) translateY(-3px) } 100% { opacity: 1; transform: scale(1) translateY(0) } }
@keyframes teamCardPop  { from { opacity: 0; transform: translateY(10px) scale(.98) } to { opacity: 1; transform: none } }
@keyframes menuShine    { 0%, 35% { transform: translateX(-120%) } 65%, 100% { transform: translateX(120%) } }
@keyframes activeMenuBreath { 0%, 100% { box-shadow: 0 14px 28px rgba(14,47,118,.26) } 50% { box-shadow: 0 18px 36px rgba(14,47,118,.36) } }
@keyframes badgePulse   { 0%, 100% { transform: scale(1) } 50% { transform: scale(1.08) } }
@keyframes sbRipple     { to { opacity: 0; transform: translate(-50%, -50%) scale(24) } }
@keyframes statusPulse  { 0% { box-shadow: 0 0 0 0 rgba(22,163,74,.45) } 70% { box-shadow: 0 0 0 7px rgba(22,163,74,0) } 100% { box-shadow: 0 0 0 0 rgba(22,163,74,0) } }

@media (prefers-reduced-motion: reduce) {
  .sb-tab, .sb-tab::after, .nav-badge-count, .sb-mark { animation: none !important; transition: none !important }
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 1100px) {
  .sched-day { min-height: 100px }
  .sched-event { font-size: 11px }
  .profile-v2-layout { grid-template-columns: 1fr }
  .profile-v2-left   { padding-bottom: 22px }
  .profile-v2-right  { max-height: none }
  .cert-grid { grid-template-columns: repeat(2, 1fr) }
}@media (max-width: 768px) {
  .sidebar { transform: translateX(-100%); transition: .24s }
  .sidebar.open { transform: translateX(0) }
  .main { margin-left: 0; padding: 70px 16px 16px }
  .sb-toggle { display: grid; position: fixed; top: 14px; left: 14px; width: 44px; height: 44px; border: 1px solid var(--line); border-radius: var(--radius-sm); background: var(--white); z-index: 120; box-shadow: var(--shadow-md); place-items: center }
  .panel-header, .cert-head, .roster-head, .sched-board-top,
  .borrow-head, .borrow-tools, .sched-controls { flex-direction: column; align-items: stretch }
  .search-inp, .cert-search { width: 100%; min-width: 0 }
  .fgrid, .sched-grid, .resume-fields, .borrow-form-grid,
  .skill-grid, .sw-grid, .comp-grid, .pinfo-grid { grid-template-columns: 1fr }
  .resume-top { flex-direction: column }
  .team-grid, .cert-grid { grid-template-columns: 1fr }
  .cal-months, .tl-months { grid-template-columns: 1fr }
  .cal-block:first-child, .tl-month-block:first-child { border-right: 0; border-bottom: 1px solid var(--line) }
  .sched-calendar-card { overflow: auto }
  .sched-week-head, .sched-month-grid { min-width: 920px }
  .factions .btn { flex: 1 }
  .pv2-sections { grid-template-columns: 1fr }
  .profile-v2 { width: calc(100vw - 32px) }
  .tcal-header { padding: 14px 18px; gap: 10px }
  .tcal-body   { padding: 14px }
  .sched-list-head { flex-direction: column; align-items: stretch }
  .sched-list-search { min-width: 0; width: 100% }
  .roster-add-tech-btn { width: 100% }
  #panel-customers .panel-actions .search-inp,
  #panel-customers .panel-actions .btn { width: 100% }
}#roster-grid .emp-card-body::before {
    content: '';
    position: absolute;
    top: -20px;
    left: 0;
    width: 50%;
    height: 22px;
    background: #16459d;
    border-radius: 10px 10px 0 0;
    z-index: 1;
}.pv2-sections {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 0;
}.pv2-section {
  min-width: 0;
}.pv2-comp-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 7px;
}.pv2-comp-item {
  min-width: 0;
}.pv2-comp-key {
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}#roster-grid .emp-card-body::before {
    content: '';
    position: absolute;
    top: -20px;
    left: 0;
    width: 50%;
    height: 22px;
    background: #0C3E9B;
    border-radius: 10px 10px 0 0;
    z-index: 1;
}#roster-grid .emp-card .emp-tag.emp-tag-head {
  background: #fde047;
  color: #78350f;
  border: 1px solid #facc15;
  text-shadow: none;
  font-weight: 800;
}#roster-grid .emp-card .emp-tag.emp-tag-member {
  background: #dbeafe;
  color: #0e2f76;
  border: 1px solid #93c5fd;
  text-shadow: none;
  font-weight: 800;
}#roster-grid .emp-card .emp-tag.emp-tag-head svg,
#roster-grid .emp-card .emp-tag.emp-tag-member svg {
  stroke: currentColor !important;
  opacity: 1 !important;
}.flash.is-hiding {
  opacity: 0;
  transform: translateY(-6px);
  max-height: 0;
  margin: 0;
  padding-top: 0;
  padding-bottom: 0;
  overflow: hidden;
}
</style>
</head>
<body>
<button class="sb-toggle" type="button" onclick="document.querySelector('.sidebar').classList.toggle('open')">☰</button>
<aside class="sidebar">
  <div class="sb-logo">
    <div class="sb-mark">3E</div>
    <div>
      <div class="sb-title">ทริปเปิ้ล อี เทรดดิ้ง</div>
      <div class="sb-sub">ระบบจัดการทักษะช่าง</div>
    </div>
  </div>
  <div class="sb-tabs">
    <button class="sb-tab active" type="button" onclick="switchTab('teams',this)">
      <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      <span class="label">ทักษะช่าง</span><span class="nav-badge-count">{{ $technicians->count() }}</span>
    </button>
    <button class="sb-tab" type="button" onclick="switchTab('schedules',this)">
      <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      <span class="label">ตารางงาน</span><span class="nav-badge-count">{{ $schedules->count() }}</span>
    </button>
    <button class="sb-tab" type="button" onclick="switchTab('customers',this)">
      <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      <span class="label">ลูกค้า PROJECT</span><span class="nav-badge-count">{{ $customers->count() }}</span>
    </button>
    <button class="sb-tab" type="button" onclick="switchTab('accounts',this)">
      <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      <span class="label">บัญชี Solar</span><span class="nav-badge-count">{{ $accounts->count() }}</span>
    </button>
    <button class="sb-tab" type="button" onclick="switchTab('certifications',this)">
      <svg viewBox="0 0 24 24"><path d="M12 2l2.8 6 6.2.8-4.5 4.4 1.1 6.2L12 16.4 6.4 19.4l1.1-6.2L3 8.8 9.2 8z"/></svg>
      <span class="label">ใบรับรอง</span><span class="nav-badge-count">{{ $certTotal }}</span>
    </button>
  </div>
</aside>
<main class="main">
  @if(session('success'))<div class="flash flash-success">{{ session('success') }}</div>@endif
  @if($errors->has('delete'))<div class="flash flash-error">{{ $errors->first('delete') }}</div>@endif
<section class="panel active" id="panel-teams">
    <div class="panel-header">
      <div class="panel-title">ทักษะช่าง ({{ $teams->count() }} ทีม · {{ $stats['total_tech'] ?? $technicians->count() }} คน)</div>
      <div class="panel-actions"><input type="search" class="search-inp" placeholder="ค้นหาช่าง / ทักษะ..." oninput="filterTeams(this.value);filterRosterSearch(this.value)"></div>
    </div>
    <div class="view-tabs">
      <button class="dtab active" type="button" onclick="switchViewTab('all',this)">ทั้งหมด <span class="nav-badge-count">{{ $technicians->count() }}</span></button>
      <button class="dtab" type="button" onclick="switchViewTab('team',this)">ทีม <span class="nav-badge-count">{{ $teams->count() }}</span></button>
    </div>
    <div id="view-all">
      <div class="roster-board">
        <div class="roster-head">
  <div>
    <div class="roster-kicker" id="roster-count">ทักษะ · {{ $sortedTechnicians->count() }} / {{ $sortedTechnicians->count() }}</div>
    <div class="roster-title">ภาพรวมทักษะช่าง</div>
    <div class="roster-sub">หัวหน้าทีมขึ้นก่อน แล้วตามด้วยลูกทีม · ใช้ข้อมูลเดิมทั้งหมด · คลิกการ์ดเพื่อดูโปรไฟล์</div>
  </div>

  <button class="btn btn-primary roster-add-tech-btn" type="button" onclick="openModal('modal-tech')">
    + เพิ่มช่าง
  </button>
</div>
        <div class="roster-filter">
          <div class="roster-filter-row">
            <span class="roster-filter-label">ทักษะ</span>
            <button class="roster-chip active" type="button" onclick="filterRosterSkill('all',this)">ทุกทักษะ</button>
            @foreach($skillFilters as $skill)
              <button class="roster-chip" type="button" onclick="filterRosterSkill(@js($skill),this)">{{ $skill }}</button>
            @endforeach
          </div>
          <div class="roster-filter-row">
            <span class="roster-filter-label">ค้นหา</span>
            <input class="roster-search" placeholder="ค้นหาช่าง, ทีม, ทักษะ, Software..." oninput="filterRosterSearch(this.value)">
          </div>
        </div>
<div class="emp-card-grid" id="roster-grid">
          @forelse($sortedTechnicians as $m)
            @php
              $skills = collect(explode(',', $m->emp_skill ?? ''))->map(fn($x) => trim($x))->filter()->values();
              $initial = mb_substr($m->emp_name ?: $m->emp_id, 0, 2);
              $isHead = ($m->emp_position ?? '') === 'หัวหน้าทีม';
              $stripeColor = $teamColorMap[$m->emp_team ?? ''] ?? '#04009A';
              $avatarStyle = 'background:'.($isHead ? '#C0FEFC' : '#e8fffe').';color:#04009A';
            @endphp
            <article class="emp-card {{ $isHead ? 'is-head' : '' }}"
              data-team="{{ $m->emp_team }}"
              data-skill="{{ strtolower($skills->implode(' ')) }}"
              data-search="{{ strtolower(($m->emp_name ?? '').' '.($m->emp_name_eng ?? '').' '.($m->emp_nickname ?? '').' '.($m->emp_id ?? '').' '.($m->emp_team ?? '').' '.($m->emp_skill ?? '').' '.collect($m->software_tools ?? [])->implode(' ')) }}"
              data-tech="{{ json_encode($m, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}"
              onclick="openProfileFromEl(this)">
              <div class="emp-card-stripe" style="background:{{ $stripeColor }}"></div>
              <div class="emp-card-body">

                <div class="emp-card-top">
  <div class="emp-avatar-new" style="{{ $avatarStyle }}">
  @if($m->img)
    <img
      src="{{ asset('storage/'.$m->img) }}"
      alt="{{ $m->emp_name }}"
      style="width:100%;height:100%;object-fit:cover;display:block"
      onerror="this.remove();this.parentElement.querySelector('.initials').style.display='grid'"
    >
    <span class="initials" style="display:none">{{ $initial }}</span>
  @else
    <span class="initials">{{ $initial }}</span>
  @endif
</div>
  <div class="emp-card-info emp-info-redesign">
    <div class="emp-card-name {{ $isHead ? 'emp-card-name-head' : '' }}">
      {{ $m->emp_name ?: $m->emp_id }}
    </div>
    <div class="emp-meta-row">
  <span>
    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:-1px;opacity:.7">
      <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
    </svg>
    ID
  </span>
  <strong>{{ $m->emp_id }}</strong>
</div>
<div class="emp-meta-row">
  <span>
    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:-1px;opacity:.7">
      <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
    </svg>
    เบอร์
  </span>
 @php
  $phoneDigits = preg_replace('/\D+/', '', $m->emp_phone ?? '');
  $phoneDisplay = '-';

  if (strlen($phoneDigits) === 10) {
    $phoneDisplay = preg_replace('/^(\d{3})(\d{3})(\d{4})$/', '$1-$2-$3', $phoneDigits);
  } elseif (strlen($phoneDigits) === 9) {
    $phoneDisplay = preg_replace('/^(\d{3})(\d{3})(\d{3})$/', '$1-$2-$3', $phoneDigits);
  } elseif ($m->emp_phone) {
    $phoneDisplay = $m->emp_phone;
  }
@endphp
<strong>{{ $phoneDisplay }}</strong>
</div>
<div class="emp-meta-row" style="margin-top:3px;gap:5px;flex-wrap:wrap">
  @if($m->emp_nickname)
    <span class="ec-nick">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:-1px;opacity:.8">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
      </svg>
      {{ $m->emp_nickname }}
    </span>
  @endif
  <span class="emp-tag {{ $isHead ? 'emp-tag-head' : 'emp-tag-member' }}">
    @if($isHead)
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:-1px">
        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
      </svg>
      หัวหน้า
    @else
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:-1px">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
      ลูกทีม
    @endif
  </span>
</div>
      </div>
      <div class="emp-status-dot"
        style="background:{{ ($m->status ?? 'active') === 'leave' ? '#ef4444' : '#22c55e' }}">
      </div>
    </div>          
                <div class="emp-card-skills">
  @foreach($skills->take(4) as $sk)
    <span class="emp-skill-tag">#{{ $sk }}</span>
  @endforeach
</div>
              </div>
            </article>
          @empty
            <div class="empty-state" style="grid-column:1/-1">ยังไม่มีข้อมูลช่าง</div>
          @endforelse
        </div>
      </div>
    </div>
    <div id="view-team" style="display:none">
      @if($teams->count() === 0)
        <div class="empty-state">ยังไม่มีทีมช่างในระบบ</div>
      @else
        <div class="team-grid" id="team-grid-wrap">
          @foreach($teams as $team)
            @php
              $teamName = data_get($team, 'team_name', '');
              $members = $technicians->where('emp_team', $teamName);
              $allMbr = $members->sort(function($a, $b) {
                $aHead = ($a->emp_position ?? '') === 'หัวหน้าทีม' ? 0 : 1;
                $bHead = ($b->emp_position ?? '') === 'หัวหน้าทีม' ? 0 : 1;
                if ($aHead !== $bHead) return $aHead - $bHead;
                return strcmp($a->emp_name ?? $a->emp_id ?? '', $b->emp_name ?? $b->emp_id ?? '');
              })->values();
              $teamScheds = $schedules->where('team_name',$teamName)->values();
            @endphp
            <article class="team-card">
              <div class="team-head-bar">
                <div style="flex:1;min-width:0">
                  <div class="team-title">{{ $teamName ?: '-' }}</div>
                  <div class="team-meta">สมาชิก {{ $members->count() }} คน · หัวหน้าขึ้นก่อน</div>
                </div>
                <button type="button" class="team-cal-btn" onclick="openTeamCalendar(@js($teamName))">ปฏิทิน <span class="badge-count">{{ $teamScheds->count() }}</span></button>
              </div>
              <div class="team-body">
                @foreach($allMbr as $m)
                  @php $isHead = ($m->emp_position ?? '') === 'หัวหน้าทีม'; @endphp
                 <div class="member" data-tech="{{ json_encode($m, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openProfileFromEl(this)">
                    <div class="m-av">
                      <img src="{{ $m->img ? asset('storage/'.$m->img) : '' }}" alt="" onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2264%22 height=%2264%22%3E%3Crect width=%2264%22 height=%2264%22 fill=%22%23C0FEFC%22/%3E%3Ctext x=%2250%25%22 y=%2254%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2216%22 fill=%22%2304009A%22 font-weight=%22bold%22%3E{{ mb_substr($m->emp_name ?: $m->emp_id, 0, 2) }}%3C/text%3E%3C/svg%3E'">
                    </div>
                    <div class="m-info">
                      <div class="m-name-row">
                        <span class="m-name" title="{{ $m->emp_name ?: $m->emp_id }}">{{ $m->emp_name ?: $m->emp_id }}</span>
                        <span class="{{ $isHead ? 'head-tag' : 'member-tag' }}">{{ $isHead ? 'หัวหน้า' : 'ลูกทีม' }}</span>
                      </div>
                      <div class="m-role">{{ $m->emp_id }}@if($m->emp_nickname) · {{ $m->emp_nickname }}@endif</div>
                    </div>
                    <div class="m-actions" onclick="event.stopPropagation()">
                      <span class="status-dot st-{{ $m->status ?: 'active' }}"></span>
                      <button class="btn btn-sm btn-ghost" type="button" onclick="openEditTechFromEl(this.closest('.member'))">แก้ไข</button>
                      <form method="POST" action="{{ route('tech.delete', $m->emp_id) }}" onsubmit="return confirm('ลบ {{ addslashes($m->emp_name ?: $m->emp_id) }} ?')">@csrf<button class="btn btn-sm btn-danger" type="submit">ลบ</button></form>
                    </div>
                  </div>
                @endforeach
              </div>
            </article>
          @endforeach
        </div>
      @endif
    </div>
  </section>
 <section class="panel" id="panel-schedules">
    <div class="sched-board">
      <div class="sched-board-top">
        <div>
          <div class="sched-eyebrow">SCHEDULE · {{ strtoupper(now()->locale('en')->isoFormat('MMM YYYY')) }}</div>
          <div class="sched-board-title">ตารางแผนงาน</div>
          <div class="sched-board-sub">ใช้ข้อมูลเดิม · คลิกงานเพื่อแก้ไขรายละเอียด</div>
        </div>
        <div class="sched-controls">
          <select class="sched-select" id="sched-type-filter" onchange="SCHED_BOARD.render()">
            <option value="all">ทุกโปรเจค</option>
            @foreach($jobTypes as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
          </select>
          <div class="sched-mode-group">
            <button class="sched-mode active" type="button" onclick="SCHED_BOARD.setMode('month',this)">Month</button>
            <button class="sched-mode" type="button" onclick="SCHED_BOARD.setMode('week',this)">Week</button>
          </div>
          <button class="sched-nav-btn" type="button" onclick="SCHED_BOARD.nav(-1)">‹</button>
          <button class="sched-nav-btn" type="button" onclick="SCHED_BOARD.nav(1)">›</button>
        </div>
      </div>
      <div class="sched-calendar-card">
        <div class="sched-month-nav"><div class="sched-month-name" id="sched-board-month">-</div></div>
        <div class="sched-week-head"><span>อา</span><span>จันทร์</span><span>อังคาร</span><span>พุธ</span><span>พฤหัส</span><span>ศุกร์</span><span>เสาร์</span></div>
        <div class="sched-month-grid" id="sched-month-grid"></div>
      </div>
      <div class="sched-list-card">
        <div class="sched-list-head">
          <div>
            <div class="sched-list-eyebrow">JOB LIST</div>
            <div class="sched-list-title">รายการงานในเดือนนี้ <span class="sched-list-count" id="sched-list-count">0 งาน</span></div>
          </div>
          <input type="search" class="search-inp sched-list-search" id="sched-list-search" placeholder="ค้นหา SO / ลูกค้า / งาน / ทีม..." oninput="SCHED_BOARD.renderList()">
        </div>
        <div class="sched-list-wrap">
          <table class="sched-list-table">
            <thead>
              <tr>
                <th style="width:60px">#</th>
                <th style="width:130px">SO</th>
                <th>ลูกค้า</th>
                <th>งาน</th>
                <th style="width:140px">ทีม</th>
                <th style="width:200px">วันที่</th>
                <th style="width:120px">สถานะ</th>
              </tr>
            </thead>
            <tbody id="sched-list-tbody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
  <section class="panel" id="panel-customers">
  <div class="panel-header">
    <div class="panel-title">ลูกค้า PROJECT ({{ $customers->count() }} ราย)</div>
    <div class="panel-actions">
      <input type="search" class="search-inp" id="cust-search" placeholder="ค้นหาลูกค้า..." oninput="filterCustTable(this.value)">
      <button class="btn btn-solar" type="button" onclick="openAddSchedModal()">+ เพิ่มงาน</button>
    </div>
  </div>
    @if($washAlerts->count() > 0)
      <div class="wash-alert-bar">
        <div class="wash-alert-title">แจ้งเตือนล้างแผง Solar ({{ $washAlerts->count() }} ราย)</div>
        <div class="wash-alert-scroll">
          @foreach($washAlerts as $wa)
            @php
              $daysLeft = method_exists($wa, 'daysUntilWash') ? $wa->daysUntilWash() : null;
              $isOver = method_exists($wa, 'isWashOverdue') ? $wa->isWashOverdue() : false;
              $dateText = $daysLeft === null ? 'รอตั้งกำหนด' : ($isOver ? 'เลยกำหนด '.abs($daysLeft).' วัน' : ($daysLeft === 0 ? 'ถึงกำหนดวันนี้' : 'อีก '.$daysLeft.' วัน'));
            @endphp
            <div class="wash-alert-chip {{ $isOver ? 'overdue' : '' }}" data-cust="{{ json_encode($wa, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openCustDetail(this)">
              <div class="wac-name" title="{{ $wa->name }}">{{ $wa->name }}</div>
              <div class="wac-date {{ $isOver ? 'overdue' : 'soon' }}">{{ $dateText }}</div>
            </div>
          @endforeach
        </div>
      </div>
    @endif
    <div class="cust-filter-bar">
      <button class="cust-filter-btn active" type="button" onclick="filterCustCat('all',this)">ทั้งหมด <span class="fbc">{{ $customers->count() }}</span></button>
      <button class="cust-filter-btn" type="button" onclick="filterCustCat('solar',this)">Solar <span class="fbc">{{ $custSummary['solar']->count() }}</span></button>
      <button class="cust-filter-btn" type="button" onclick="filterCustCat('electrical',this)">ไฟฟ้า <span class="fbc">{{ $custSummary['electrical']->count() }}</span></button>
      <button class="cust-filter-btn" type="button" onclick="filterCustCat('civil',this)">โยธา <span class="fbc">{{ $custSummary['civil']->count() }}</span></button>
      <button class="cust-filter-btn" type="button" onclick="filterCustCat('general',this)">ทั่วไป <span class="fbc">{{ $custSummary['general']->count() }}</span></button>
    </div>
    @if($customers->count() === 0)
      <div class="empty-state">ยังไม่มีลูกค้าในระบบ</div>
    @else
      <div class="table-wrap">
        <table>
          <thead><tr><th style="width:52px">#</th><th>ชื่อลูกค้า</th><th>ประเภท</th><th>วันติดตั้งสำเร็จ</th><th>รอบล้างแผง</th><th>จัดการ</th></tr></thead>
          <tbody id="cust-tbody">
            @foreach($customers as $idx => $c)
              @php
                $cat = method_exists($c, 'getCategory') ? $c->getCategory() : (str_starts_with((string)($c->type_project ?? ''), 'solar') ? 'solar' : (($c->type_project ?? '') ?: 'general'));
                $isSolar = str_starts_with((string)($c->type_project ?? ''), 'solar');
              @endphp
              <tr data-cat="{{ $cat }}" data-search="{{ strtolower(($c->name ?? '').' '.($c->desc ?? '').' '.($c->contact_name ?? '')) }}">
                <td>{{ $idx + 1 }}</td>
                <td>
                  <button class="cust-name-btn" type="button" data-cust="{{ json_encode($c, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openCustDetail(this)">{{ $c->name }}</button>
                  @if($c->desc)<div style="color:var(--muted);font-size:12px;font-weight:700">{{ $c->desc }}</div>@endif
                  @if($c->contact_name)<div style="color:var(--muted);font-size:11px;font-weight:700">{{ $c->contact_name }}@if($c->phone) · {{ $c->phone }}@endif</div>@endif
                </td>
                <td><span class="job-type-tag jt-{{ $c->type_project ?: 'general' }}">{{ $jobTypes[$c->type_project ?? 'general'] ?? ($c->type_project ?: 'ทั่วไป') }}</span></td>
                <td>
                  @if($c->supervisor)
                    <span class="cust-date-chip">{{ \Carbon\Carbon::parse($c->supervisor)->format('d/m/') }}{{ \Carbon\Carbon::parse($c->supervisor)->year + 543 }}</span>
                  @else
                    <span class="cust-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($isSolar)
                    <div class="wash-cycle-cell">
                      <span class="wash-cycle-chip">{{ $c->wash_cycle ?? 6 }} เดือน</span>
                      @if($c->wash_next)
                        <small>ครั้งถัดไป {{ \Carbon\Carbon::parse($c->wash_next)->format('d/m/') }}{{ \Carbon\Carbon::parse($c->wash_next)->year + 543 }}</small>
                      @else
                        <small>ยังไม่ตั้งกำหนด</small>
                      @endif
                    </div>
                  @else
                    <span class="cust-muted">-</span>
                  @endif
                </td>
                <td>
                  <div style="display:flex;gap:6px;flex-wrap:wrap">
                    <button class="btn btn-sm btn-ghost" type="button" data-cust="{{ json_encode($c, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openCustEdit(this)">แก้ไข</button>
                    <form method="POST" action="{{ route('cust.delete', $c->id) }}" onsubmit="return confirm('ลบลูกค้า {{ addslashes($c->name) }} ?')">@csrf<button class="btn btn-sm btn-danger" type="submit">ลบ</button></form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </section>
  <section class="panel" id="panel-accounts">
    <div class="panel-header">
      <div class="panel-title">บัญชีผู้ใช้ Solar / Monitoring ({{ $accounts->count() }} บัญชี)</div>
      <div class="panel-actions"><input type="search" class="search-inp" placeholder="ค้นหาบัญชี..." oninput="filterTable('acc-tbody',this.value)"><button class="btn btn-solar" type="button" onclick="openAccAdd()">+ เพิ่มบัญชี</button></div>
    </div>
    @if($accounts->count() === 0)
      <div class="empty-state">ยังไม่มีบัญชีในระบบ</div>
    @else
      <div class="table-wrap">
        <table>
          <thead><tr><th>#</th><th>ชื่อระบบ / Platform</th><th>ลูกค้า / Inverter</th><th>Username / Email</th><th>Password</th><th>จัดการ</th></tr></thead>
          <tbody id="acc-tbody">
            @foreach($accounts as $idx => $a)
              <tr data-search="{{ strtolower(($a->plane ?? '').' '.($a->customer ?? '').' '.($a->inverter ?? '').' '.($a->username ?? '').' '.($a->email ?? '')) }}">
                <td>{{ $idx + 1 }}</td>
                <td><strong>{{ $a->plane ?: '-' }}</strong>@if($a->inverter)<div style="font-size:12px;color:var(--muted);font-weight:700">{{ $a->inverter }}</div>@endif</td>
                <td>{{ $a->customer ?: '-' }}</td>
                <td>@if($a->username)<div style="font-family:Consolas,monospace;font-weight:900;color:var(--blue)">{{ $a->username }}</div>@endif @if($a->email)<div style="font-size:12px;color:var(--muted);font-weight:700">{{ $a->email }}</div>@endif</td>
                <td>
                  @if($a->password)
                    <div class="acc-pw-wrap"><span class="acc-pw-text" data-pw="{{ $a->password }}" style="font-family:Consolas,monospace;font-weight:900">••••••••</span><button class="btn btn-sm btn-ghost" type="button" onclick="togglePw(this)">แสดง</button><button class="btn btn-sm btn-ghost" type="button" onclick="copyText(@js($a->password),this)">คัดลอก</button></div>
                  @else -
                  @endif
                </td>
                <td><div style="display:flex;gap:6px"><button class="btn btn-sm btn-ghost" type="button" data-acc="{{ json_encode($a, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openAccEdit(this)">แก้ไข</button><form method="POST" action="{{ route('account.delete', $a->id) }}" onsubmit="return confirm('ลบบัญชีนี้?')">@csrf<button class="btn btn-sm btn-danger" type="submit">ลบ</button></form></div></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </section>
  <section class="panel" id="panel-certifications">
    <div class="cert-board">
      <div class="cert-head">
        <div><div class="cert-kicker">CERTIFICATIONS · {{ $certGroups->count() }} UNIQUE</div><div class="cert-title">รวมใบรับรององค์กร</div><div class="cert-sub">ใบรับรองวิชาชีพรวม {{ $certTotal }} ฉบับ</div></div>
        <input class="cert-search" placeholder="ค้นหาใบรับรอง..." oninput="filterCertCards(this.value)">
      </div>
      <div class="cert-grid" id="cert-grid">
        @forelse($certGroups as $certName => $items)
          @php
            $abbrs = $items->map(fn($item) => mb_substr(preg_split('/\s+/u', trim($item['tech']->emp_name ?: $item['tech']->emp_id))[0] ?? ($item['tech']->emp_name ?: $item['tech']->emp_id), 0, 2))->unique()->take(4)->values();
            $payload = $items->map(fn($item) => ['tech' => $item['tech'], 'license' => $item['license']])->values();
          @endphp
          <button class="cert-card" type="button" data-cert-search="{{ strtolower($certName) }}" data-cert-name="{{ $certName }}" data-cert-items="{{ json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openCertDetail(this)">
            <div class="cert-card-top"><div class="cert-icon">☆</div><div class="cert-info"><div class="cert-name">{{ $certName }}</div><div class="cert-count-text">{{ $items->count() }} คนในองค์กร</div></div><div class="cert-count">{{ $items->count() }}</div></div>
            <div class="cert-people">@foreach($abbrs as $abbr)<span>{{ $abbr }}</span>@endforeach</div>
          </button>
        @empty
          <div class="empty-state" style="grid-column:1/-1">ยังไม่มีข้อมูลใบรับรอง</div>
        @endforelse
      </div>
    </div>
  </section>
</main>
<div class="overlay" id="overlay">
  <div class="pmodal profile-v2" onclick="event.stopPropagation()">
    <div class="profile-v2-layout">
      <div class="profile-v2-left">
        <button class="pv2-close-btn" type="button" onclick="closeModalById('overlay')">×</button>
        <div class="profile-v2-photo"><img id="m-img" src="" alt="" style="display:none"><span id="m-initial">3E</span></div>
        <div class="profile-v2-name" id="m-name"></div>
        <div class="profile-v2-nameeng" id="m-name-eng"></div>
        <div class="profile-v2-status pv2-status-active" id="m-status"><span class="pv2-st-dot pv2-dot-active" id="m-st-dot"></span><span id="m-st-text">พร้อมทำงาน</span></div>
       <div class="profile-v2-rolecard">
  <div class="pv2-rolerow">
    <span class="pv2-rolekey" >ตำแหน่ง :</span><span class="pv2-roleval" id="m-position">-</span>
  </div>
  <div class="pv2-rolerow">
    <span class="pv2-rolekey">ทีม :</span>
    <span class="pv2-roleval" id="m-team">-</span>
  </div>
</div>
<div class="profile-v2-infolist">
  <div class="pv2-inforow"><span class="pv2-infokey">รหัส</span><span class="pv2-infoval" id="m-empid">-</span></div>
  <div class="pv2-inforow"><span class="pv2-infokey">ชื่อเล่น</span><span class="pv2-infoval" id="m-nickname">-</span></div>
  <div class="pv2-inforow"><span class="pv2-infokey">โทร</span><span class="pv2-infoval" id="m-phone">-</span></div>
  <div class="pv2-inforow"><span class="pv2-infokey">วันเกิด</span><span class="pv2-infoval" id="m-dob">-</span></div>
</div>
      </div>
      <div class="profile-v2-right">
  <div style="display:flex;flex-direction:column;gap:16px">
    <div class="pv2-sections">
          <div class="pv2-section"><div class="pv2-section-label">ทักษะ</div><div class="pv2-tags" id="m-skills"></div></div>
          <div class="pv2-section"><div class="pv2-section-label">Software & Tools</div><div class="pv2-tags" id="m-software"></div></div>
        </div>
        <div class="pv2-sections">
          <div class="pv2-section"><div class="pv2-section-label">Core Competencies</div><div class="pv2-comp-grid" id="m-competencies"></div></div>
          <div class="pv2-section"><div class="pv2-section-label">Licenses & Experience</div><div id="m-licenses"></div></div>
      </div>
    </div>
  </div>
</div>
</div>
<div class="overlay" id="modal-tech">
  <div class="pmodal pmodal-wide" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-header"><div class="modal-title">เพิ่มช่างใหม่</div><button class="modal-close" type="button" onclick="closeModalById('modal-tech')">×</button></div>
    <div class="modal-body" style="padding:0">
      @if($errors->any() && !old('_edit_tech') && !old('_edit_sched') && !old('so_number'))<div class="ferr" style="margin:16px 22px 0">{{ $errors->first() }}</div>@endif
      @php
        $oldSkills = old('emp_skill', []); if (is_string($oldSkills)) $oldSkills = array_filter(array_map('trim', explode(',', $oldSkills))); if (!is_array($oldSkills)) $oldSkills = [];
        $oldComp = old('core_competencies', []); if (!is_array($oldComp)) $oldComp = [];
        $oldSw = old('software_tools', []); if (!is_array($oldSw)) $oldSw = [];
      @endphp
      <form method="POST" action="{{ route('tech.store') }}" enctype="multipart/form-data" id="form-add-tech">@csrf
        <div class="resume-top">
          <div class="photo-col">
            <div style="position:relative">
              <span class="resume-badge-abs">PHOTO</span>
              <div class="photo-box" onclick="document.getElementById('add-img-input').click()">
                <img id="add-img-preview" class="resume-img" src="" alt="">
                <div class="photo-overlay"><span>เปลี่ยนรูป</span></div>
                <div class="photo-placeholder" id="add-img-ph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg><div>คลิกอัปโหลดรูป</div></div>
              </div>
            </div>
            <div class="photo-label">รูปประจำตัว</div>
            <input type="file" id="add-img-input" name="img" hidden accept="image/*" onchange="resumePreview(this,'add')">
          </div>
          <div class="resume-fields">
            <div class="frow"><label class="flabel">รหัสพนักงาน *</label><input class="finput" type="text" name="emp_id" value="{{ old('emp_id') }}" required placeholder="3E-001"><div class="emp-id-note">ตัวอักษร, ตัวเลข, -, _</div></div>
            <div class="frow"><label class="flabel">ชื่อ-นามสกุล (ไทย)</label><input class="finput" type="text" name="emp_name" value="{{ old('emp_name') }}"></div>
            <div class="frow"><label class="flabel">ชื่อ-นามสกุล (Eng)</label><input class="finput" type="text" name="emp_name_eng" value="{{ old('emp_name_eng') }}"></div>
            <div class="frow"><label class="flabel">ชื่อเล่น</label><input class="finput" type="text" name="emp_nickname" value="{{ old('emp_nickname') }}"></div>
            <div class="frow"><label class="flabel">เบอร์โทร</label><input class="finput" type="text" name="emp_phone" value="{{ old('emp_phone') }}"></div>
            <div class="frow"><label class="flabel">วันเกิด</label><div class="dob-row"><input class="finput" type="date" name="date_of_birth" id="add-dob" value="{{ old('date_of_birth') }}" onchange="updateBE('add')"><span class="dob-be" id="add-dob-be">พ.ศ. -</span></div></div>
            <div class="frow"><label class="flabel">ตำแหน่ง</label><select class="finput" name="emp_position" id="add-emp_position" onchange="handlePositionChange('add')"><option value="">-- เลือก --</option><option value="ลูกทีม" {{ old('emp_position')==='ลูกทีม'?'selected':'' }}>ลูกทีม</option><option value="หัวหน้าทีม" {{ old('emp_position')==='หัวหน้าทีม'?'selected':'' }}>หัวหน้าทีม</option></select></div>
            <div class="frow" id="add-team-wrap" style="{{ old('emp_position')==='หัวหน้าทีม'?'display:none':'' }}"><label class="flabel">ทีม</label><select class="finput" name="emp_team" id="add-team-select"><option value="">-- เลือกทีม --</option>@foreach($availableTeams as $tn)<option value="{{ $tn }}" {{ old('emp_team')===$tn?'selected':'' }}>{{ $tn }}</option>@endforeach</select></div>
          </div>
        </div>
        <div style="padding:12px 22px 0"><div id="add-head-info" style="{{ old('emp_position')==='หัวหน้าทีม'?'':'display:none' }}"><div class="head-info-box">ชื่อทีมจะถูกตั้งเป็นชื่อพนักงานอัตโนมัติ</div></div></div>
        <div style="padding:0 22px 20px">
          <div class="section-h">ทักษะ</div>
          <div class="skill-grid">@foreach($skillOptions as $sk)<label class="skill-check {{ in_array($sk,$oldSkills)?'checked':'' }}"><input type="checkbox" name="emp_skill[]" value="{{ $sk }}" {{ in_array($sk,$oldSkills)?'checked':'' }} onchange="this.closest('label').classList.toggle('checked',this.checked)"> {{ $sk }}</label>@endforeach</div>
          <div class="section-h">Core Competencies</div>
          <div class="comp-grid">@foreach($competencyList as $c)@php $compKey = $c['key']; $compVal = $oldComp[$compKey] ?? 'none'; @endphp<div class="comp-card"><div class="comp-head"><span class="comp-label">{{ $c['label'] }}</span><span class="comp-code">{{ $compKey }}</span></div><select class="comp-select lv-{{ $compVal }}" name="core_competencies[{{ $compKey }}]" onchange="updateCompClass(this)">@foreach($competencyLevels as $lv => $lvL)<option value="{{ $lv }}" {{ $compVal===$lv?'selected':'' }}>{{ $lvL }}</option>@endforeach</select></div>@endforeach</div>
          <div class="section-h">Licenses & Experience</div><div class="lic-list" id="add-lic-list"></div><button type="button" class="btn-add-lic" onclick="addLicense('add')">+ เพิ่มใบรับรอง</button>
          <div class="section-h">Software & Tools</div>
          <div class="sw-grid">@foreach($softwareOptions as $sw)<label class="skill-check {{ in_array($sw,$oldSw)?'checked':'' }}"><input type="checkbox" name="software_tools[]" value="{{ $sw }}" {{ in_array($sw,$oldSw)?'checked':'' }} onchange="this.closest('label').classList.toggle('checked',this.checked)"> {{ $sw }}</label>@endforeach</div>
          <div class="sw-custom-row"><input type="text" class="finput" id="add-sw-custom" placeholder="เพิ่ม software อื่นๆ..." onkeydown="if(event.key==='Enter'){event.preventDefault();addCustomSw('add')}"><button type="button" class="btn-other" onclick="addCustomSw('add')">+ เพิ่ม</button></div><div class="sw-custom-tags" id="add-sw-custom-tags"></div>
          <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-tech')">ยกเลิก</button><button type="submit" class="btn btn-primary">บันทึกข้อมูล</button></div>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="overlay" id="modal-edit-tech">
  <div class="pmodal pmodal-wide" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div><div class="modal-header"><div class="modal-title">แก้ไขข้อมูลช่าง</div><button class="modal-close" type="button" onclick="closeModalById('modal-edit-tech')">×</button></div>
    <div class="modal-body" style="padding:0">
      @if($errors->any() && old('_edit_tech'))<div class="ferr" style="margin:16px 22px 0">{{ $errors->first() }}</div>@endif
      <form method="POST" id="form-edit-tech" action="" enctype="multipart/form-data">@csrf<input type="hidden" name="_edit_tech" value="1">
        <div class="resume-top">
          <div class="photo-col">
            <div style="position:relative"><span class="resume-badge-abs">PHOTO</span><div class="photo-box" onclick="document.getElementById('et-img-input').click()"><img id="et-img-preview" class="resume-img" src="" alt=""><div class="photo-overlay"><span>เปลี่ยนรูป</span></div><div class="photo-placeholder" id="et-img-ph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg><div>คลิกเปลี่ยนรูป</div></div></div></div>
            <div class="photo-label">รูปประจำตัว</div><input type="file" id="et-img-input" name="img" hidden accept="image/*" onchange="resumePreview(this,'et')">
          </div>
          <div class="resume-fields">
            <div class="frow"><label class="flabel">รหัสพนักงาน</label><input class="finput" type="text" id="et-emp_id" readonly></div>
            <div class="frow"><label class="flabel">ชื่อ-นามสกุล (ไทย)</label><input class="finput" type="text" name="emp_name" id="et-emp_name"></div>
            <div class="frow"><label class="flabel">ชื่อ-นามสกุล (Eng)</label><input class="finput" type="text" name="emp_name_eng" id="et-emp_name_eng"></div>
            <div class="frow"><label class="flabel">ชื่อเล่น</label><input class="finput" type="text" name="emp_nickname" id="et-emp_nickname"></div>
            <div class="frow"><label class="flabel">เบอร์โทร</label><input class="finput" type="text" name="emp_phone" id="et-emp_phone"></div>
            <div class="frow"><label class="flabel">วันเกิด</label><div class="dob-row"><input class="finput" type="date" name="date_of_birth" id="et-dob" onchange="updateBE('et')"><span class="dob-be" id="et-dob-be">พ.ศ. -</span></div></div>
            <div class="frow"><label class="flabel">ตำแหน่ง</label><select class="finput" name="emp_position" id="et-emp_position" onchange="handlePositionChange('et')"><option value="">-- เลือก --</option><option value="ลูกทีม">ลูกทีม</option><option value="หัวหน้าทีม">หัวหน้าทีม</option></select></div>
            <div class="frow" id="et-team-wrap"><label class="flabel">ทีม</label><select class="finput" name="emp_team" id="et-team-select"><option value="">-- เลือกทีม --</option>@foreach($availableTeams as $tn)<option value="{{ $tn }}">{{ $tn }}</option>@endforeach</select></div>
            <div class="frow"><label class="flabel">สถานะ</label><select class="finput" name="status" id="et-status"><option value="active">พร้อมทำงาน</option><option value="leave">ลาออก</option></select></div>
          </div>
        </div>
        <div style="padding:12px 22px 0"><div id="et-head-info" style="display:none"><div class="head-info-box">ชื่อทีมจะถูกตั้งเป็นชื่อพนักงานอัตโนมัติ</div></div></div>
        <div style="padding:0 22px 20px">
          <div class="section-h">ทักษะ</div><div class="skill-grid" id="et-skill-grid">@foreach($skillOptions as $sk)<label class="skill-check" data-skill="{{ $sk }}"><input type="checkbox" name="emp_skill[]" value="{{ $sk }}" onchange="this.closest('label').classList.toggle('checked',this.checked)"> {{ $sk }}</label>@endforeach</div>
          <div class="section-h">Core Competencies</div><div class="comp-grid" id="et-comp-grid">@foreach($competencyList as $c)<div class="comp-card"><div class="comp-head"><span class="comp-label">{{ $c['label'] }}</span><span class="comp-code">{{ $c['key'] }}</span></div><select class="comp-select lv-none" data-comp="{{ $c['key'] }}" name="core_competencies[{{ $c['key'] }}]" onchange="updateCompClass(this)">@foreach($competencyLevels as $lv=>$lvL)<option value="{{ $lv }}">{{ $lvL }}</option>@endforeach</select></div>@endforeach</div>
          <div class="section-h">Licenses & Experience</div><div class="lic-list" id="et-lic-list"></div><button type="button" class="btn-add-lic" onclick="addLicense('et')">+ เพิ่มใบรับรอง</button>
          <div class="section-h">Software & Tools</div><div class="sw-grid" id="et-sw-grid">@foreach($softwareOptions as $sw)<label class="skill-check" data-sw="{{ $sw }}"><input type="checkbox" name="software_tools[]" value="{{ $sw }}" onchange="this.closest('label').classList.toggle('checked',this.checked)"> {{ $sw }}</label>@endforeach</div>
          <div class="sw-custom-row"><input type="text" class="finput" id="et-sw-custom" placeholder="เพิ่ม software อื่นๆ..." onkeydown="if(event.key==='Enter'){event.preventDefault();addCustomSw('et')}"><button type="button" class="btn-other" onclick="addCustomSw('et')">+ เพิ่ม</button></div><div class="sw-custom-tags" id="et-sw-custom-tags"></div>
          <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-edit-tech')">ยกเลิก</button><button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button></div>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="overlay" id="modal-sched">
  <div class="pmodal pmodal-wide" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div><div class="modal-header"><div class="modal-title">เพิ่มงานใหม่</div><button class="modal-close" type="button" onclick="closeModalById('modal-sched')">×</button></div>
    <div class="modal-body">
      @if($errors->any() && !old('_edit_sched') && old('so_number') && !old('emp_id'))<div class="ferr">{{ $errors->first() }}</div>@endif
      <form method="POST" action="{{ route('sched.store') }}" id="form-add-sched">@csrf<input type="hidden" name="customer_id" id="add-customer_id" value="">
        <div class="sched-grid">
          <div class="frow"><label class="flabel">ประเภทงาน *</label><select class="finput" name="job_type" id="add-job_type" required><option value="">-- เลือกประเภท --</option>@foreach($jobTypes as $key=>$label)<option value="{{ $key }}" {{ old('job_type')===$key?'selected':'' }}>{{ $label }}</option>@endforeach</select></div>
          <div class="frow"><label class="flabel">เลข SO *</label><input class="finput" type="text" name="so_number" value="{{ old('so_number') }}" required placeholder="SO-2026-001"></div>
          <div class="frow sched-full autocomp"><label class="flabel">ชื่อลูกค้า *</label><input class="finput" type="text" name="customer_name" id="add-customer_name" value="{{ old('customer_name') }}" required autocomplete="off" placeholder="พิมพ์ชื่อลูกค้า..." oninput="custAutocomp(this.value,'add')" onkeydown="custAutocompKey(event,'add')"><div class="autocomp-list" id="add-ac-list"></div><div class="cust-banner cust-banner-old" id="add-cust-banner"></div></div>
          <div class="frow" id="add-ncf-1" style="display:none"><label class="flabel">รายละเอียดโครงการ</label><input class="finput" type="text" name="cust_desc"></div>
          <div class="frow" id="add-ncf-2" style="display:none"><label class="flabel">ชื่อผู้ติดต่อ</label><input class="finput" type="text" name="cust_contact_name"></div>
          <div class="frow" id="add-ncf-3" style="display:none"><label class="flabel">เบอร์โทรลูกค้า</label><input class="finput" type="text" name="cust_phone"></div>
          <div class="frow" id="add-ncf-4" style="display:none"><label class="flabel">ขนาดติดตั้ง</label><input class="finput" type="text" name="cust_size"></div>
          <div class="frow"><label class="flabel">ทีมที่รับผิดชอบ *</label><select class="finput" name="team_name" id="add-team_name" required onchange="TL.onTeamChange('add')"><option value="">-- เลือกทีม --</option>@foreach($teams as $t)@php $tn = data_get($t, 'team_name', ''); @endphp<option value="{{ $tn }}" {{ old('team_name')===$tn?'selected':'' }}>{{ $tn }}</option>@endforeach</select></div>
          <div class="frow"><label class="flabel">ชื่องาน *</label><input class="finput" type="text" name="job_title" value="{{ old('job_title') }}" required></div>
          <div class="frow"><label class="flabel">สถานที่</label><input class="finput" type="text" name="job_location" id="add-job_location" value="{{ old('job_location') }}"></div>
          <div class="frow"><label class="flabel">ละติจูด,ลองจิจูด</label><input class="finput" type="text" name="job_la_long" id="add-job_la_long" value="{{ old('job_la_long') }}"></div>
          <div class="frow sched-full"><label class="flabel">ช่วงวันที่ทำงาน *</label><div class="tl-wrap" id="add-tl-wrap"><div class="tl-header"><button type="button" class="tl-mnav-btn" data-tl-nav="prev" data-tl-prefix="add">‹</button><div class="tl-mname" id="add-tl-mname"></div><button type="button" class="tl-today-btn" onclick="TL.gotoToday('add')">วันนี้</button><button type="button" class="tl-mnav-btn" data-tl-nav="next" data-tl-prefix="add">›</button></div><div class="tl-team-info no-team" id="add-tl-team-info">เลือกทีมก่อนเพื่อดูวันที่ทีมว่าง</div><div class="tl-months"><div class="tl-month-block"><div class="tl-month-title" id="add-tl-mname-left"></div><div class="tl-dhdrs" id="add-tl-dhdrs-left"></div><div class="tl-grid" id="add-tl-grid-left"></div></div><div class="tl-month-block"><div class="tl-month-title" id="add-tl-mname-right"></div><div class="tl-dhdrs" id="add-tl-dhdrs-right"></div><div class="tl-grid" id="add-tl-grid-right"></div></div></div><div class="tl-summary"><div class="tl-summary-info" id="add-tl-summary">กรุณาเลือกช่วงวันที่</div><button type="button" class="tl-clear-btn" onclick="TL.clear('add')">ล้าง</button></div><div class="tl-legend"><span class="tl-leg"><i class="tl-leg-box today"></i>วันนี้</span><span class="tl-leg"><i class="tl-leg-box busy"></i>ทีมมีงาน</span><span class="tl-leg"><i class="tl-leg-box sel"></i>เลือก</span><span class="tl-leg"><i class="tl-leg-box range"></i>ช่วงเลือก</span></div></div></div>
          <div class="frow sched-full"><label class="flabel">หมายเหตุ</label><textarea class="finput" name="note" rows="3">{{ old('note') }}</textarea></div>
        </div>
        <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-sched')">ยกเลิก</button><button type="submit" class="btn btn-primary">บันทึกงาน</button></div>
      </form>
    </div>
  </div>
</div>
<div class="overlay" id="modal-edit-sched">
  <div class="pmodal pmodal-wide" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div><div class="modal-header"><div class="modal-title">แก้ไขงาน</div><button class="modal-close" type="button" onclick="closeModalById('modal-edit-sched')">×</button></div>
    <div class="modal-body">
      @if($errors->any() && old('_edit_sched'))<div class="ferr">{{ $errors->first() }}</div>@endif
      <form method="POST" id="form-edit-sched" action="">@csrf<input type="hidden" name="_edit_sched" value="1">
        <div class="sched-grid">
          <div class="frow"><label class="flabel">เลข SO *</label><input class="finput" type="text" name="so_number" id="es-so_number" required></div>
          <div class="frow"><label class="flabel">ชื่อลูกค้า *</label><input class="finput" type="text" name="customer_name" id="es-customer_name" required></div>
          <div class="frow"><label class="flabel">ประเภทงาน</label><select class="finput" name="job_type" id="es-job_type"><option value="">-- เลือกประเภท --</option>@foreach($jobTypes as $key=>$label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
          <div class="frow"><label class="flabel">ชื่องาน *</label><input class="finput" type="text" name="job_title" id="es-job_title" required></div>
          <div class="frow"><label class="flabel">สถานที่</label><input class="finput" type="text" name="job_location" id="es-job_location"></div>
          <div class="frow"><label class="flabel">ละติจูด,ลองจิจูด</label><input class="finput" type="text" name="job_la_long" id="es-job_la_long"></div>
          <div class="frow"><label class="flabel">ทีม *</label><select class="finput" name="team_name" id="es-team_name" required onchange="TL.onTeamChange('es')"><option value="">-- เลือกทีม --</option>@foreach($teams as $t)@php $tn = data_get($t, 'team_name', ''); @endphp<option value="{{ $tn }}">{{ $tn }}</option>@endforeach</select></div>
          <div class="frow"></div>
          <div class="frow sched-full"><label class="flabel">ช่วงวันที่ทำงาน *</label><div class="tl-wrap" id="es-tl-wrap"><div class="tl-header"><button type="button" class="tl-mnav-btn" data-tl-nav="prev" data-tl-prefix="es">‹</button><div class="tl-mname" id="es-tl-mname"></div><button type="button" class="tl-today-btn" onclick="TL.gotoToday('es')">วันนี้</button><button type="button" class="tl-mnav-btn" data-tl-nav="next" data-tl-prefix="es">›</button></div><div class="tl-team-info no-team" id="es-tl-team-info">เลือกทีมก่อน</div><div class="tl-months"><div class="tl-month-block"><div class="tl-month-title" id="es-tl-mname-left"></div><div class="tl-dhdrs" id="es-tl-dhdrs-left"></div><div class="tl-grid" id="es-tl-grid-left"></div></div><div class="tl-month-block"><div class="tl-month-title" id="es-tl-mname-right"></div><div class="tl-dhdrs" id="es-tl-dhdrs-right"></div><div class="tl-grid" id="es-tl-grid-right"></div></div></div><div class="tl-summary"><div class="tl-summary-info" id="es-tl-summary">กรุณาเลือกช่วงวันที่</div><button type="button" class="tl-clear-btn" onclick="TL.clear('es')">ล้าง</button></div><div class="tl-legend"><span class="tl-leg"><i class="tl-leg-box today"></i>วันนี้</span><span class="tl-leg"><i class="tl-leg-box busy"></i>ทีมมีงาน</span><span class="tl-leg"><i class="tl-leg-box sel"></i>เลือก</span><span class="tl-leg"><i class="tl-leg-box range"></i>ช่วงเลือก</span></div></div></div>
          <div class="frow sched-full"><label class="flabel">หมายเหตุ</label><textarea class="finput" name="note" id="es-note" rows="3"></textarea></div>
        </div>
        <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-edit-sched')">ยกเลิก</button><button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button></div>
      </form>
    </div>
  </div>
</div>
<div class="overlay" id="modal-cust">
  <div class="pmodal pmodal-wide" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div><div class="modal-header"><div><div class="modal-title" id="cust-modal-title">เพิ่มลูกค้าใหม่</div><div class="modal-subtitle" id="cust-modal-sub"></div></div><button class="modal-close" type="button" onclick="closeModalById('modal-cust')">×</button></div>
    <div class="modal-body">
      <form method="POST" id="form-cust" action="{{ route('cust.store') }}">@csrf
        <div class="fgrid">
          <div class="frow"><label class="flabel">ประเภทงาน *</label><select class="finput" name="type_project" id="cf-type_project" onchange="onCustTypeChange(this.value)"><option value="">-- เลือกประเภทงาน --</option>@foreach($jobTypes as $key=>$label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
          <div class="frow"><label class="flabel">สถานะ</label><select class="finput" name="status" id="cf-status"><option value="เสนอ">เสนอ</option></select></div>
          <div class="frow fcol-full"><label class="flabel">ชื่อลูกค้า / สถานที่ *</label><input class="finput" type="text" name="name" id="cf-name" required></div>
          <div class="frow"><label class="flabel">รายละเอียด</label><input class="finput" type="text" name="desc" id="cf-desc"></div>
          <div class="frow"><label class="flabel">ผู้ติดต่อ</label><input class="finput" type="text" name="contact_name" id="cf-contact_name"></div>
          <div class="frow"><label class="flabel">เบอร์โทร</label><input class="finput" type="text" name="phone" id="cf-phone"></div>
          <div class="frow"><label class="flabel" id="cf-size-lbl">ขนาดติดตั้ง</label><input class="finput" type="text" name="size" id="cf-size"></div>
          <div class="frow"><label class="flabel">ราคา</label><input class="finput" type="number" step="0.01" name="price" id="cf-price"></div>
          <div class="frow"><label class="flabel">พิกัด</label><input class="finput" type="text" name="loc" id="cf-loc"></div>
          <div class="frow" id="cf-finish-wrap"><label class="flabel" id="cf-finish-lbl">วันสิ้นสุด / ติดตั้งเสร็จ</label><input class="finput" type="date" name="supervisor" id="cf-finish_date"></div>
          <div class="frow" id="cf-wash-wrap" style="display:none"><label class="flabel">รอบล้างแผง</label><select class="finput" name="wash_cycle" id="cf-wash_cycle"><option value="6">6 เดือน</option><option value="12">12 เดือน</option></select></div>
          <div class="frow fcol-full"><label class="flabel">หมายเหตุ</label><textarea class="finput" name="notes" id="cf-notes" rows="3"></textarea></div>
        </div>
        <div class="finfo-box" id="cf-solar-info" style="display:none">เมื่อสถานะ "ติดตั้งสำเร็จ" ระบบจะใช้วันติดตั้งเสร็จและรอบล้างแผงเพื่อคำนวณนัดล้างครั้งถัดไป</div>
        <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-cust')">ยกเลิก</button><button type="submit" class="btn btn-solar">บันทึก</button></div>
      </form>
    </div>
  </div>
</div>
<div class="overlay" id="modal-cust-detail">
  <div class="pmodal pmodal-wide" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div><div class="modal-header"><div><div class="modal-title" id="cd-name">รายละเอียดลูกค้า</div><div style="margin-top:4px" id="cd-type-tag"></div></div><button class="modal-close" type="button" onclick="closeModalById('modal-cust-detail')">×</button></div>
    <div class="modal-body">
      <div class="dtab-bar"><button class="dtab active" type="button" id="dtab-btn-info" onclick="switchDTab('info',this)">ข้อมูล</button><button class="dtab" type="button" id="dtab-btn-wash" onclick="switchDTab('wash',this)" style="display:none">ล้างแผง</button><button class="dtab" type="button" id="dtab-btn-milestone" onclick="switchDTab('milestone',this)" style="display:none">Timeline</button><button class="dtab" type="button" id="dtab-btn-sched" onclick="switchDTab('sched',this)">งานที่เกี่ยวข้อง</button></div>
      <div class="dtab-panel active" id="dtab-info"><div id="cd-wash-countdown" style="display:none"></div><div class="pinfo-grid"><div class="pinfo-card"><div class="pinfo-label">รายละเอียด</div><div class="pinfo-val" id="cd-desc">-</div></div><div class="pinfo-card"><div class="pinfo-label">สถานะ</div><div class="pinfo-val" id="cd-status">-</div></div><div class="pinfo-card"><div class="pinfo-label">ผู้ติดต่อ</div><div class="pinfo-val" id="cd-contact">-</div></div><div class="pinfo-card"><div class="pinfo-label">เบอร์โทร</div><div class="pinfo-val" id="cd-phone">-</div></div><div class="pinfo-card"><div class="pinfo-label" id="cd-size-lbl">ขนาด</div><div class="pinfo-val" id="cd-size">-</div></div><div class="pinfo-card"><div class="pinfo-label">ราคา</div><div class="pinfo-val" id="cd-price">-</div></div><div class="pinfo-card"><div class="pinfo-label">พิกัด</div><div class="pinfo-val" id="cd-loc">-</div></div><div class="pinfo-card"><div class="pinfo-label" id="cd-finish-lbl">วันสิ้นสุด</div><div class="pinfo-val" id="cd-finish_date">-</div></div></div><div class="pinfo-card"><div class="pinfo-label">หมายเหตุ</div><div class="pinfo-val" id="cd-notes" style="white-space:pre-wrap">-</div></div></div>
      <div class="dtab-panel" id="dtab-wash"><div style="margin-bottom:10px;font-size:13px;color:var(--muted);font-weight:800">ประวัติการล้างแผง <span id="cd-wash-count"></span></div><div id="cd-wash-body"></div><button type="button" class="btn-add-lic" onclick="openAddWashModal()">+ เพิ่มประวัติการล้างแผง</button></div>
      <div class="dtab-panel" id="dtab-milestone"><div style="display:flex;justify-content:space-between;gap:8px;align-items:center;margin-bottom:12px"><span style="font-size:13px;font-weight:900;color:var(--muted)">Timeline ความคืบหน้าโครงการ</span><button type="button" class="btn btn-primary btn-sm" onclick="openAddMilestoneModal()">+ เพิ่ม milestone</button></div><div id="cd-milestone-body"></div></div>
      <div class="dtab-panel" id="dtab-sched"><div id="cd-schedules" style="font-size:13px;color:var(--muted);font-weight:800">ยังไม่มีงานที่ผูกกับลูกค้านี้</div></div>
      <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-cust-detail')">ปิด</button><button type="button" class="btn btn-solar" onclick="editFromDetail()">แก้ไข</button></div>
    </div>
  </div>
</div>

<div class="overlay" id="modal-add-wash"><div class="pmodal pmodal-sm" onclick="event.stopPropagation()"><div class="pmodal-strip"></div><div class="modal-header"><div class="modal-title">เพิ่มประวัติการล้างแผง</div><button class="modal-close" type="button" onclick="closeModalById('modal-add-wash')">×</button></div><div class="modal-body"><form method="POST" id="form-add-wash" action="">@csrf<div class="frow"><label class="flabel">วันที่ล้าง *</label><input class="finput" type="date" name="wash_date" id="aw-date" required></div><div class="frow"><label class="flabel">ทีม / ช่างที่ล้าง *</label><select class="finput" name="tech" id="aw-tech" required><option value="">-- เลือก --</option>@foreach($teams as $t)@php $tn = data_get($t, 'team_name', ''); @endphp<option value="{{ $tn }}">{{ $tn }}</option>@endforeach<option value="ช่างภายนอก">ช่างภายนอก</option><option value="อื่นๆ">อื่นๆ</option></select></div><div class="frow"><label class="flabel">หมายเหตุ</label><textarea class="finput" name="note" id="aw-note" rows="2"></textarea></div><div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-add-wash')">ยกเลิก</button><button type="submit" class="btn btn-solar">บันทึก</button></div></form></div></div></div>
<div class="overlay" id="modal-add-milestone"><div class="pmodal pmodal-sm" onclick="event.stopPropagation()"><div class="pmodal-strip"></div><div class="modal-header"><div class="modal-title">เพิ่ม Milestone</div><button class="modal-close" type="button" onclick="closeModalById('modal-add-milestone')">×</button></div><div class="modal-body"><form method="POST" id="form-add-milestone" action="">@csrf<div class="frow"><label class="flabel">วันที่ *</label><input class="finput" type="date" name="milestone_date" id="am-date" required></div><div class="frow"><label class="flabel">รายละเอียด *</label><textarea class="finput" name="milestone_note" id="am-note" rows="3" required></textarea></div><div class="frow"><label class="flabel">บันทึกโดย</label><input class="finput" type="text" name="milestone_by" id="am-by"></div><div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-add-milestone')">ยกเลิก</button><button type="submit" class="btn btn-primary">บันทึก</button></div></form></div></div></div>
<div class="overlay" id="modal-account"><div class="pmodal" onclick="event.stopPropagation()"><div class="pmodal-strip"></div><div class="modal-header"><div class="modal-title" id="acc-modal-title">เพิ่มบัญชีผู้ใช้ Solar</div><button class="modal-close" type="button" onclick="closeModalById('modal-account')">×</button></div><div class="modal-body"><form method="POST" id="form-account" action="{{ route('account.store') }}">@csrf<div class="fgrid"><div class="frow"><label class="flabel">เลขที่ / รหัส</label><input class="finput" type="text" name="no" id="af-no" readonly></div><div class="frow"><label class="flabel">Inverter / ยี่ห้อ</label><input class="finput" type="text" name="inverter" id="af-inverter"></div><div class="frow fcol-full"><label class="flabel">ชื่อระบบ / Platform *</label><input class="finput" type="text" name="plane" id="af-plane" required></div><div class="frow fcol-full"><label class="flabel">ลูกค้า / สถานที่ติดตั้ง</label><div class="autocomp"><input class="finput" type="text" name="customer" id="af-customer" autocomplete="off" oninput="accCustAutocomp(this.value)"><div class="autocomp-list" id="af-cust-list"></div></div></div><div class="frow"><label class="flabel">Username</label><input class="finput" type="text" name="username" id="af-username" autocomplete="off"></div><div class="frow"><label class="flabel">Password</label><div style="position:relative"><input class="finput" type="password" name="password" id="af-password" autocomplete="new-password" style="padding-right:44px"><button type="button" onclick="toggleInputPw('af-password',this)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;font-weight:900;color:var(--muted)">ดู</button></div></div><div class="frow"><label class="flabel">Email</label><input class="finput" type="text" name="email" id="af-email"></div><div class="frow"><label class="flabel">App Password</label><div style="position:relative"><input class="finput" type="password" name="app_password" id="af-app_password" autocomplete="new-password" style="padding-right:44px"><button type="button" onclick="toggleInputPw('af-app_password',this)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;font-weight:900;color:var(--muted)">ดู</button></div></div></div><div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-account')">ยกเลิก</button><button type="submit" class="btn btn-solar">บันทึก</button></div></form></div></div></div>

<div class="tcal-overlay" id="tcal-overlay">
  <div class="tcal-fs">
    <div class="tcal-header">
      <div class="tcal-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
      <div class="tcal-title-block">
        <div class="tcal-eyebrow">ตารางเวลางาน</div>
        <div class="tcal-title" id="tcal-team-name">-</div>
        <span class="tcal-stat" id="tcal-job-count">0 งาน</span>
      </div>
      <button class="tcal-close" type="button" onclick="closeTeamCalendar()">×</button>
    </div>

    
    <div class="tcal-body">
      <div class="tcal-content">
        <div class="sched-board" style="min-height:0">
          <div class="sched-board-top">
            <div>
              <div class="sched-eyebrow" id="tcal-eyebrow">SCHEDULE</div>
              <div class="sched-board-title">งานของทีม</div>
              <div class="sched-board-sub">คลิกงานเพื่อดู/แก้ไขรายละเอียด · เปลี่ยนเดือนเพื่อดูงานเดือนอื่น</div>
            </div>
            <div class="sched-controls">
              <select class="sched-select" id="tcal-type-filter" onchange="TCAL.render()">
                <option value="all">ทุกโปรเจค</option>
              </select>
              <button class="sched-nav-btn" type="button" onclick="TCAL.nav(-1)">‹</button>
              <button class="sched-nav-btn" type="button" onclick="TCAL.gotoToday()">วันนี้</button>
              <button class="sched-nav-btn" type="button" onclick="TCAL.nav(1)">›</button>
            </div>
          </div>
          <div class="sched-calendar-card">
            <div class="sched-month-nav"><div class="sched-month-name" id="tcal-month-name">-</div></div>
            <div class="sched-week-head"><span>อา</span><span>จันทร์</span><span>อังคาร</span><span>พุธ</span><span>พฤหัส</span><span>ศุกร์</span><span>เสาร์</span></div>
            <div class="sched-month-grid" id="tcal-month-grid"></div>
          </div>

          <div class="sched-list-card">
            <div class="sched-list-head">
              <div>
                <div class="sched-list-eyebrow">JOB LIST</div>
                <div class="sched-list-title">รายการงานในเดือนนี้ <span class="sched-list-count" id="tcal-list-count">0 งาน</span></div>
              </div>
              <input type="search" class="search-inp sched-list-search" id="tcal-list-search" placeholder="ค้นหา SO / ลูกค้า / งาน..." oninput="TCAL.renderList()">
            </div>
            <div class="sched-list-wrap">
              <table class="sched-list-table">
                <thead>
                  <tr>
                    <th style="width:60px">#</th>
                    <th style="width:130px">SO</th>
                    <th>ลูกค้า</th>
                    <th>งาน</th>
                    <th style="width:200px">วันที่</th>
                    <th style="width:120px">สถานะ</th>
                  </tr>
                </thead>
                <tbody id="tcal-list-tbody"></tbody>
              </table>
            </div>
          </div>

          <div style="margin-top:16px;display:flex;justify-content:flex-end;gap:10px">
            <button class="btn btn-ghost" type="button" onclick="closeTeamCalendar()">ปิด</button>
            <button class="btn btn-primary" type="button" onclick="closeTeamCalendar();openAddSchedModal()">+ เพิ่มงาน</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const URL_TECH_UPDATE = (id) => `/technicians/${encodeURIComponent(id)}/update`;
const URL_SCHED_UPDATE = (id) => `/schedules/${encodeURIComponent(id)}/update`;
const URL_CUST_STORE = "{{ route('cust.store') }}";
const URL_CUST_UPDATE = (id) => `/customers/${encodeURIComponent(id)}/update`;
const URL_WASH_STORE = (id) => `/customers/${encodeURIComponent(id)}/wash/store`;
const URL_WASH_DEL = (id, num) => `/customers/${encodeURIComponent(id)}/wash/${encodeURIComponent(num)}/delete`;
const URL_MILESTONE_STORE = (id) => `/customers/${encodeURIComponent(id)}/milestone/store`;
const URL_MILESTONE_DEL = (id, idx) => `/customers/${encodeURIComponent(id)}/milestone/${encodeURIComponent(idx)}/delete`;
const URL_ACC_STORE = "{{ route('account.store') }}";
const URL_ACC_UPDATE = (id) => `/solar-accounts/${encodeURIComponent(id)}/update`;
const CSRF = "{{ csrf_token() }}";
const TECH_DATA = @json($technicians, JSON_UNESCAPED_UNICODE);
const SCHED_DATA = @json($schedules, JSON_UNESCAPED_UNICODE);
const CUST_DATA = @json($customers, JSON_UNESCAPED_UNICODE);
const JOB_TYPES = @json($jobTypes, JSON_UNESCAPED_UNICODE);
function fmtPhone(v) {
  const d = String(v || '').replace(/\D/g, '');
  if (!d) return '-';
  if (d.length <= 3) return d;
  if (d.length <= 6) return `${d.slice(0, 3)}-${d.slice(3)}`;
  return `${d.slice(0, 3)}-${d.slice(3, 6)}-${d.slice(6, 9)}`;
}
function escHtml(s){return String(s??'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}
function fmtDate(d){if(!d)return '-';const dt=new Date(d);if(isNaN(dt))return d;return `${dt.getDate()}/${dt.getMonth()+1}/${dt.getFullYear()+543}`}
function ymd(d){const dt=(d instanceof Date)?d:new Date(d);return `${dt.getFullYear()}-${String(dt.getMonth()+1).padStart(2,'0')}-${String(dt.getDate()).padStart(2,'0')}`}
function daysBetween(a,b){return Math.round((new Date(b)-new Date(a))/86400000)}
function normalizeDate(v){return v?ymd(v):''}
function getCategory(type){if(!type)return 'general';if(String(type).startsWith('solar'))return 'solar';if(type==='electrical')return 'electrical';if(type==='civil')return 'civil';return 'general'}
function switchTab(tab,el){document.querySelectorAll('.panel').forEach(p=>p.classList.remove('active'));document.querySelectorAll('.sb-tab').forEach(t=>t.classList.remove('active'));document.getElementById('panel-'+tab)?.classList.add('active');el?.classList.add('active');if(tab==='schedules')SCHED_BOARD.render();if(innerWidth<=768)document.querySelector('.sidebar')?.classList.remove('open')}
function openModal(id){document.getElementById(id)?.classList.add('open');document.body.style.overflow='hidden'}
function closeModalById(id){document.getElementById(id)?.classList.remove('open');if(!document.querySelector('.overlay.open,.tcal-overlay.open,.cal-popup-bg.open'))document.body.style.overflow=''}
document.addEventListener('click',e=>{if(e.target.classList?.contains('overlay'))closeModalById(e.target.id);if(e.target.id==='cal-popup-bg')e.target.classList.remove('open')})
document.addEventListener('keydown',e=>{if(e.key==='Escape'){document.querySelectorAll('.overlay.open,.tcal-overlay.open,.cal-popup-bg.open').forEach(el=>el.classList.remove('open'));document.body.style.overflow=''}})
function filterTable(tbodyId,q){const kw=(q||'').toLowerCase().trim();document.querySelectorAll('#'+tbodyId+' tr[data-search]').forEach(r=>r.style.display=(!kw||(r.dataset.search||'').includes(kw))?'':'none')}
function filterTeams(q){const kw=(q||'').toLowerCase().trim();document.querySelectorAll('#team-grid-wrap .team-card').forEach(card=>card.style.display=(!kw||(card.textContent||'').toLowerCase().includes(kw))?'':'none')}
function switchViewTab(tab,btn){document.querySelectorAll('.view-tabs .dtab').forEach(b=>b.classList.remove('active'));btn.classList.add('active');document.getElementById('view-all').style.display=tab==='all'?'':'none';document.getElementById('view-team').style.display=tab==='team'?'':'none'}

const TL=(()=>{const state={};function init(prefix){if(state[prefix])return state[prefix];const t=new Date();return state[prefix]={year:t.getFullYear(),month:t.getMonth(),start:null,end:null,team:'',busyDays:{},isDragging:false,editingId:null}}function getTeamSchedules(team){return team?SCHED_DATA.filter(s=>s.team_name===team):[]}function buildBusyDays(prefix,excludeId){const st=state[prefix];st.busyDays={};getTeamSchedules(st.team).forEach(s=>{if(excludeId&&String(s.id)===String(excludeId))return;let d=new Date(s.start_date),end=new Date(s.end_date);while(d<=end){const k=ymd(d);st.busyDays[k]=(st.busyDays[k]||0)+1;d.setDate(d.getDate()+1)}})}function onTeamChange(prefix){const st=init(prefix),sel=document.getElementById(prefix==='add'?'add-team_name':'es-team_name');st.team=sel?.value||'';const info=document.getElementById(prefix+'-tl-team-info');if(info){if(st.team){info.classList.remove('no-team');info.innerHTML=`ทีม <strong>${escHtml(st.team)}</strong> มีงาน ${getTeamSchedules(st.team).length} งาน`}else{info.classList.add('no-team');info.textContent='เลือกทีมก่อนเพื่อดูวันที่ทีมว่าง'}}buildBusyDays(prefix,prefix==='es'?state.es?.editingId:null);render(prefix)}function gotoToday(prefix){const st=init(prefix),t=new Date();st.year=t.getFullYear();st.month=t.getMonth();render(prefix)}function clear(prefix){const st=init(prefix);st.start=null;st.end=null;syncHidden(prefix);render(prefix)}function nav(prefix,dir){const st=init(prefix);st.month+=dir;if(st.month<0){st.month=11;st.year--}if(st.month>11){st.month=0;st.year++}render(prefix)}function selectDate(prefix,dateStr){const st=init(prefix);if(!st.start||(st.start&&st.end)){st.start=dateStr;st.end=null}else if(dateStr<st.start){st.end=st.start;st.start=dateStr}else st.end=dateStr;syncHidden(prefix);render(prefix)}function startDrag(prefix,dateStr){const st=init(prefix);st.isDragging=true;st.start=dateStr;st.end=dateStr;syncHidden(prefix);render(prefix)}function dragOver(prefix,dateStr){const st=init(prefix);if(!st.isDragging)return;if(dateStr<st.start){st.end=st.start;st.start=dateStr}else st.end=dateStr;render(prefix)}function endDrag(prefix){const st=init(prefix);st.isDragging=false;syncHidden(prefix)}function syncHidden(prefix){const st=state[prefix],form=document.getElementById(prefix==='add'?'form-add-sched':'form-edit-sched');if(!st||!form)return;let s=form.querySelector('input[name="start_date"]'),e=form.querySelector('input[name="end_date"]');if(!s){s=document.createElement('input');s.type='hidden';s.name='start_date';form.appendChild(s)}if(!e){e=document.createElement('input');e.type='hidden';e.name='end_date';form.appendChild(e)}s.value=st.start||'';e.value=st.end||st.start||''}function render(prefix){const st=init(prefix),months=['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'],hdr=['อา','จ','อ','พ','พฤ','ศ','ส'];const mname=document.getElementById(prefix+'-tl-mname');if(mname)mname.textContent=`${months[st.month]} ${st.year+543}`;function renderMonth(yr,mo,side){document.getElementById(`${prefix}-tl-mname-${side}`).textContent=`${months[mo]} ${yr+543}`;document.getElementById(`${prefix}-tl-dhdrs-${side}`).innerHTML=hdr.map((d,i)=>`<div class="tl-dhdr ${i===0||i===6?'weekend':''}">${d}</div>`).join('');const grid=document.getElementById(`${prefix}-tl-grid-${side}`);const first=new Date(yr,mo,1).getDay(),days=new Date(yr,mo+1,0).getDate(),today=ymd(new Date());let html='';for(let i=0;i<first;i++)html+='<div class="tl-cell tl-other"></div>';for(let d=1;d<=days;d++){const ds=`${yr}-${String(mo+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`,busy=st.busyDays[ds]||0,cls=['tl-cell'];if(ds===today)cls.push('tl-today');if(busy)cls.push('tl-busy');if(st.start&&ds===st.start)cls.push('tl-sel-s');if(st.end&&ds===st.end)cls.push('tl-sel-e');if(st.start&&st.end&&ds>st.start&&ds<st.end)cls.push('tl-in-range');html+=`<div class="${cls.join(' ')}" data-date="${ds}" ${busy?'':`onmousedown="TL.startDrag('${prefix}','${ds}');event.preventDefault()" onmouseenter="TL.dragOver('${prefix}','${ds}')" onclick="TL.selectDate('${prefix}','${ds}')"`}><div class="tl-d">${d}</div>${busy?`<div class="tl-busy-bar"></div><div class="tl-jobs-count">${busy}</div>`:''}</div>`}grid.innerHTML=html}const nextMo=st.month===11?0:st.month+1,nextYr=st.month===11?st.year+1:st.year;renderMonth(st.year,st.month,'left');renderMonth(nextYr,nextMo,'right');const summary=document.getElementById(prefix+'-tl-summary');if(summary){if(st.start&&st.end){let conflict=0,cur=new Date(st.start),end=new Date(st.end);while(cur<=end){if(st.busyDays[ymd(cur)])conflict++;cur.setDate(cur.getDate()+1)}summary.innerHTML=`เลือก: <strong>${fmtDate(st.start)}</strong> ถึง <strong>${fmtDate(st.end)}</strong> (${daysBetween(st.start,st.end)+1} วัน) ${conflict?`<span class="tl-summary-warn">ทับซ้อน ${conflict} วัน</span>`:''}`}else if(st.start)summary.innerHTML=`เริ่ม: <strong>${fmtDate(st.start)}</strong> — เลือกวันสิ้นสุด`;else summary.textContent='กรุณาเลือกช่วงวันที่'}}function setRange(prefix,start,end,team,id){const st=init(prefix);st.team=team||'';st.start=normalizeDate(start)||null;st.end=normalizeDate(end)||null;st.editingId=id;if(st.start){const d=new Date(st.start);st.year=d.getFullYear();st.month=d.getMonth()}buildBusyDays(prefix,id);syncHidden(prefix);render(prefix);const info=document.getElementById(prefix+'-tl-team-info');if(info&&team){info.classList.remove('no-team');info.innerHTML=`ทีม <strong>${escHtml(team)}</strong> มีงาน ${getTeamSchedules(team).length} งาน`}}return{init,onTeamChange,gotoToday,clear,nav,selectDate,startDrag,dragOver,endDrag,setRange,render,_state:state}})();
document.addEventListener('mouseup',()=>{TL.endDrag('add');TL.endDrag('es')});

function openProfileFromEl(el){if(!el?.dataset.tech)return;try{openProfileModal(JSON.parse(el.dataset.tech))}catch(e){}}
let CURRENT_PROFILE_TECH = null;
function openProfileModal(t){CURRENT_PROFILE_TECH=t;const set=(id,v)=>{const el=document.getElementById(id);if(el)el.textContent=(v==null||v==='')?'-':v};const img=document.getElementById('m-img'),initial=document.getElementById('m-initial');if(img){if(t.img){img.src=`/storage/${t.img}`;img.style.display='block';if(initial)initial.style.display='none'}else{img.removeAttribute('src');img.style.display='none';if(initial){initial.style.display='block';initial.textContent=(t.emp_name||t.emp_id||'3E').substring(0,2)}}}set('m-name',t.emp_name||t.emp_id);set('m-name-eng',t.emp_name_eng);set('m-position',t.emp_position||'ลูกทีม');set('m-team',t.emp_team);set('m-empid',t.emp_id);set('m-nickname',t.emp_nickname);set('m-phone', fmtPhone(t.emp_phone));set('m-dob',t.date_of_birth?fmtDate(t.date_of_birth):'-');const isLeave=t.status==='leave';const statusEl=document.getElementById('m-status'),dotEl=document.getElementById('m-st-dot'),txtEl=document.getElementById('m-st-text');if(statusEl)statusEl.className='profile-v2-status '+(isLeave?'pv2-status-leave':'pv2-status-active');if(dotEl)dotEl.className='pv2-st-dot '+(isLeave?'pv2-dot-leave':'pv2-dot-active');if(txtEl)txtEl.textContent=isLeave?'ลาออก':'พร้อมทำงาน';const skills=(t.emp_skill||'').split(',').map(s=>s.trim()).filter(Boolean);document.getElementById('m-skills').innerHTML=skills.length?skills.map(s=>`<span class="pv2-tag">${escHtml(s)}</span>`).join(''):'<span class="pv2-muted">-</span>';const lv={none:'ไม่มี',basic:'พื้นฐาน',skill:'ชำนาญ',expert:'เชี่ยวชาญ'};const comps=t.core_competencies||{};const compEntries=Object.entries(comps).filter(([k,v])=>v&&v!=='none');document.getElementById('m-competencies').innerHTML=compEntries.length?compEntries.map(([k,v])=>`<div class="pv2-comp-item"><span class="pv2-comp-key">${escHtml(k)}</span><span class="pv2-comp-val cv-${escHtml(v)}">${lv[v]||escHtml(v)}</span></div>`).join(''):'<span class="pv2-muted">-</span>';const lics=t.licenses||[];document.getElementById('m-licenses').innerHTML=lics.length?lics.map(l=>`<div class="pv2-lic-item"><div class="pv2-lic-name">${escHtml(l.title||'-')}</div><div class="pv2-lic-meta">${l.doc_no?'เลขที่: '+escHtml(l.doc_no):''}${l.date_issued?' · '+escHtml(l.date_issued):''}${l.file?` · <a href="/storage/${escHtml(l.file)}" target="_blank" style="color:var(--blue);font-weight:900">เปิดไฟล์</a>`:''}</div></div>`).join(''):'<span class="pv2-muted">-</span>';const sw=t.software_tools||[];document.getElementById('m-software').innerHTML=sw.length?sw.map(s=>`<span class="pv2-tag pv2-tag-sw">${escHtml(s)}</span>`).join(''):'<span class="pv2-muted">-</span>';openModal('overlay')}
function updateBE(prefix){const inp=document.getElementById(prefix+'-dob'),lbl=document.getElementById(prefix+'-dob-be');if(!inp||!lbl)return;if(inp.value){const d=new Date(inp.value);if(!isNaN(d)){lbl.textContent=`พ.ศ. ${d.getFullYear()+543}`;return}}lbl.textContent='พ.ศ. -'}
function handlePositionChange(prefix){const isHead=document.getElementById(prefix+'-emp_position')?.value==='หัวหน้าทีม';const team=document.getElementById(prefix+'-team-wrap'),info=document.getElementById(prefix+'-head-info');if(team)team.style.display=isHead?'none':'';if(info)info.style.display=isHead?'':'none'}
function updateCompClass(sel){
  if(!sel)return;
  const lvls=['none','basic','skill','expert'];
  lvls.forEach(lv=>sel.classList.remove('lv-'+lv));
  sel.classList.add('lv-'+(sel.value||'none'));
}
function updateAllCompClasses(scope){
  const root=scope||document;
  root.querySelectorAll('.comp-select').forEach(updateCompClass);
}
function resumePreview(input,prefix){const file=input.files?.[0];const img=document.getElementById(prefix+'-img-preview');const ph=document.getElementById(prefix+'-img-ph');if(!file||!img)return;const r=new FileReader();r.onload=e=>{img.src=e.target.result;img.style.display='block';img.classList.add('has-img');if(ph)ph.style.display='none'};r.readAsDataURL(file)}
let _licIdx={add:0,et:0};
function addLicense(prefix,lic=null){const i=_licIdx[prefix]++,list=document.getElementById(prefix+'-lic-list');if(!list)return;const row=document.createElement('div');row.className='lic-item';row.innerHTML=`<div class="lic-item-head"><span class="lic-num">#${i+1}</span><button type="button" class="lic-del" onclick="this.closest('.lic-item').remove()">ลบ</button></div><div class="lic-grid"><input class="finput" name="licenses[${i}][title]" placeholder="ชื่อใบรับรอง" value="${escHtml(lic?.title||'')}"><input class="finput" name="licenses[${i}][doc_no]" placeholder="เลขที่" value="${escHtml(lic?.doc_no||'')}"><input class="finput" name="licenses[${i}][date_issued]" placeholder="วันที่ออก (YYYY-MM-DD)" value="${escHtml(lic?.date_issued||'')}"><input type="file" name="licenses[${i}][file_upload]" accept=".jpg,.jpeg,.png,.webp,.pdf"></div>${lic?.file?`<input type="hidden" name="licenses[${i}][existing_file]" value="${escHtml(lic.file)}"><a href="/storage/${escHtml(lic.file)}" target="_blank" class="lic-file-link">ไฟล์เดิม</a>`:''}`;list.appendChild(row)}
function addCustomSw(prefix){const inp=document.getElementById(prefix+'-sw-custom'),tags=document.getElementById(prefix+'-sw-custom-tags');const val=inp?.value.trim();if(!val||!tags)return;const tag=document.createElement('span');tag.className='sw-tag';tag.innerHTML=`<input type="hidden" name="software_tools[]" value="${escHtml(val)}">${escHtml(val)}<span class="x" onclick="this.parentElement.remove()">×</span>`;tags.appendChild(tag);inp.value=''}
function openEditTechFromEl(memberEl){if(!memberEl?.dataset.tech)return;let t;try{t=JSON.parse(memberEl.dataset.tech)}catch(e){return}document.getElementById('form-edit-tech').action=URL_TECH_UPDATE(t.emp_id);const v=(id,val)=>{const el=document.getElementById(id);if(el)el.value=val??''};v('et-emp_id',t.emp_id);v('et-emp_name',t.emp_name);v('et-emp_name_eng',t.emp_name_eng);v('et-emp_nickname',t.emp_nickname);v('et-emp_phone',t.emp_phone);v('et-dob',normalizeDate(t.date_of_birth));v('et-emp_position',t.emp_position||'ลูกทีม');v('et-team-select',t.emp_team);v('et-status',t.status||'active');updateBE('et');handlePositionChange('et');const img=document.getElementById('et-img-preview'),ph=document.getElementById('et-img-ph');if(img){if(t.img){img.src=`/storage/${t.img}`;img.style.display='block';img.classList.add('has-img');if(ph)ph.style.display='none'}else{img.removeAttribute('src');img.style.display='none';img.classList.remove('has-img');if(ph)ph.style.display='grid'}}const skills=(t.emp_skill||'').split(',').map(s=>s.trim()).filter(Boolean);document.querySelectorAll('#et-skill-grid label').forEach(l=>{const cb=l.querySelector('input');cb.checked=skills.includes(cb.value);l.classList.toggle('checked',cb.checked)});const comps=t.core_competencies||{};document.querySelectorAll('#et-comp-grid select[data-comp]').forEach(s=>{s.value=comps[s.dataset.comp]||'none';updateCompClass(s)});const sw=t.software_tools||[];document.querySelectorAll('#et-sw-grid label').forEach(l=>{const cb=l.querySelector('input');cb.checked=sw.includes(cb.value);l.classList.toggle('checked',cb.checked)});const tags=document.getElementById('et-sw-custom-tags');tags.innerHTML='';const predefined=Array.from(document.querySelectorAll('#et-sw-grid input')).map(i=>i.value);sw.forEach(s=>{if(!predefined.includes(s)){const tag=document.createElement('span');tag.className='sw-tag';tag.innerHTML=`<input type="hidden" name="software_tools[]" value="${escHtml(s)}">${escHtml(s)}<span class="x" onclick="this.parentElement.remove()">×</span>`;tags.appendChild(tag)}});document.getElementById('et-lic-list').innerHTML='';_licIdx.et=0;(t.licenses||[]).forEach(l=>addLicense('et',l));openModal('modal-edit-tech')}

function openAddSchedModal(){document.getElementById('form-add-sched')?.reset();document.getElementById('add-customer_id').value='';['add-ncf-1','add-ncf-2','add-ncf-3','add-ncf-4'].forEach(id=>{const el=document.getElementById(id);if(el)el.style.display='none'});document.getElementById('add-cust-banner').style.display='none';TL.init('add');TL._state.add.start=null;TL._state.add.end=null;TL._state.add.team='';TL.gotoToday('add');TL.onTeamChange('add');openModal('modal-sched')}
function openEditSchedFromEl(btn){if(!btn?.dataset.sched)return;let s;try{s=JSON.parse(btn.dataset.sched)}catch(e){return}document.getElementById('form-edit-sched').action=URL_SCHED_UPDATE(s.id);const v=(id,val)=>{const el=document.getElementById(id);if(el)el.value=val??''};v('es-so_number',s.so_number);v('es-customer_name',s.customer_name);v('es-job_type',s.job_type||'general');v('es-job_title',s.job_title);v('es-job_location',s.job_location);v('es-job_la_long',s.job_la_long);v('es-team_name',s.team_name);v('es-note',s.note);TL.setRange('es',s.start_date,s.end_date,s.team_name,s.id);openModal('modal-edit-sched')}
let _acIdx=-1;
function custAutocomp(q,prefix){const list=document.getElementById(prefix+'-ac-list'),cid=document.getElementById(prefix+'-customer_id'),banner=document.getElementById(prefix+'-cust-banner'),kw=(q||'').toLowerCase().trim();if(!list)return;if(!kw){list.classList.remove('open');if(cid)cid.value='';if(banner)banner.style.display='none';showNewCustFields(prefix,false);return}const matches=CUST_DATA.filter(c=>(c.name||'').toLowerCase().includes(kw)||(c.desc||'').toLowerCase().includes(kw)).slice(0,6);if(!matches.length){list.classList.remove('open');list.innerHTML='';if(cid)cid.value='';if(banner){banner.className='cust-banner cust-banner-new';banner.style.display='flex';banner.textContent='ลูกค้าใหม่ — กรุณากรอกรายละเอียดเพิ่มเติม'}showNewCustFields(prefix,true);return}list.innerHTML=matches.map((c,i)=>`<div class="ac-item" data-idx="${i}" onclick="pickCust('${prefix}',${Number(c.id)})"><div class="ac-item-name">${escHtml(c.name)}</div>${c.desc?`<div class="ac-item-meta">${escHtml(c.desc)}</div>`:''}</div>`).join('');list.classList.add('open');_acIdx=-1}
function custAutocompKey(e,prefix){const list=document.getElementById(prefix+'-ac-list');if(!list?.classList.contains('open'))return;const items=list.querySelectorAll('.ac-item');if(!items.length)return;if(e.key==='ArrowDown'){e.preventDefault();_acIdx=Math.min(_acIdx+1,items.length-1)}else if(e.key==='ArrowUp'){e.preventDefault();_acIdx=Math.max(_acIdx-1,0)}else if(e.key==='Enter'&&_acIdx>=0){e.preventDefault();items[_acIdx].click();return}else if(e.key==='Escape'){list.classList.remove('open');return}else return;items.forEach(i=>i.classList.remove('ac-active'));items[_acIdx]?.classList.add('ac-active')}
function pickCust(prefix,id){const c=CUST_DATA.find(x=>Number(x.id)===Number(id));if(!c)return;document.getElementById(prefix+'-customer_name').value=c.name||'';document.getElementById(prefix+'-customer_id').value=c.id||'';document.getElementById(prefix+'-ac-list').classList.remove('open');const banner=document.getElementById(prefix+'-cust-banner');banner.className='cust-banner cust-banner-old';banner.style.display='flex';banner.innerHTML=`ลูกค้าเดิม: <strong>${escHtml(c.name)}</strong>${c.desc?' · '+escHtml(c.desc):''}`;showNewCustFields(prefix,false);const ll=document.getElementById(prefix+'-job_la_long'),loc=document.getElementById(prefix+'-job_location');if(ll&&!ll.value&&c.loc)ll.value=c.loc;if(loc&&!loc.value)loc.value=c.desc?`${c.name} · ${c.desc}`:c.name}
function showNewCustFields(prefix,show){if(prefix!=='add')return;['add-ncf-1','add-ncf-2','add-ncf-3','add-ncf-4'].forEach(id=>{const el=document.getElementById(id);if(el)el.style.display=show?'':'none'})}
const STATUS_OPTS={solar:['เสนอ','ดำเนินการ','เสร็จสิ้น','ยกเลิก'],electrical:['เสนอ','ดำเนินการ','เสร็จสิ้น','ยกเลิก'],civil:['เสนอ','ดำเนินการ','เสร็จสิ้น','ยกเลิก'],general:['เสนอ','ดำเนินการ','เสร็จสิ้น','ยกเลิก']};
function onCustTypeChange(typeVal){const cat=getCategory(typeVal),sel=document.getElementById('cf-status'),cur=sel?.value;if(sel)sel.innerHTML=(STATUS_OPTS[cat]||STATUS_OPTS.general).map(s=>`<option value="${s}"${s===cur?' selected':''}>${s}</option>`).join('');document.getElementById('cf-wash-wrap').style.display=cat==='solar'?'':'none';document.getElementById('cf-solar-info').style.display=cat==='solar'?'':'none';document.getElementById('cf-finish-lbl').textContent=cat==='solar'?'วันติดตั้งสำเร็จ':'วันสิ้นสุด';document.getElementById('cf-size-lbl').textContent=cat==='solar'?'ขนาดติดตั้ง (kW)':cat==='electrical'?'ขนาดงาน':cat==='civil'?'พื้นที่/ขอบเขต':'ขนาด/ปริมาณ'}
function openCustAdd(){document.getElementById('cust-modal-title').textContent='เพิ่มลูกค้าใหม่';const f=document.getElementById('form-cust');f.action=URL_CUST_STORE;f.reset();document.getElementById('cf-type_project').value='solar_install';onCustTypeChange('solar_install');openModal('modal-cust')}
function openCustEdit(btn){if(!btn?.dataset.cust)return;let c;try{c=JSON.parse(btn.dataset.cust)}catch(e){return}document.getElementById('cust-modal-title').textContent='แก้ไขลูกค้า: '+(c.name||'');document.getElementById('form-cust').action=URL_CUST_UPDATE(c.id);const v=(id,val)=>{const el=document.getElementById(id);if(el)el.value=val??''};v('cf-type_project',c.type_project||'solar_install');onCustTypeChange(c.type_project||'solar_install');v('cf-name',c.name);v('cf-desc',c.desc);v('cf-contact_name',c.contact_name);v('cf-phone',c.phone);v('cf-size',c.size);v('cf-price',c.price);v('cf-loc',c.loc);v('cf-finish_date',normalizeDate(c.supervisor));v('cf-wash_cycle',c.wash_cycle||6);v('cf-notes',c.notes);v('cf-status',c.status||'เสนอ');openModal('modal-cust')}
let _custCat='all',_custKw='';
function filterCustCat(cat,btn){_custCat=cat;document.querySelectorAll('.cust-filter-btn').forEach(b=>b.classList.remove('active'));btn?.classList.add('active');applyCustFilter()}
function filterCustTable(q){_custKw=(q||'').toLowerCase().trim();applyCustFilter()}
function applyCustFilter(){document.querySelectorAll('#cust-tbody tr[data-cat]').forEach(r=>{const ok=(_custCat==='all'||r.dataset.cat===_custCat)&&(!_custKw||(r.dataset.search||'').includes(_custKw));r.style.display=ok?'':'none'})}
let _detailCust=null;
function custStatusClass(status){if(status==='เสนอ'||status==='เสนอราคา')return'cst-quote';if(status==='ดำเนินการ'||status==='กำลังติดตั้ง')return'cst-active';if(status==='เสร็จสิ้น'||status==='ติดตั้งสำเร็จ'||status==='ปิดการขาย')return'cst-done';if(status==='ยกเลิก')return'cst-cancel';return'cst-other'}
function openCustDetail(btn){if(!btn?.dataset.cust)return;let c;try{c=JSON.parse(btn.dataset.cust)}catch(e){return}_detailCust=c;const cat=getCategory(c.type_project);document.getElementById('cd-name').textContent=c.name||'-';document.getElementById('cd-type-tag').innerHTML=`<span class="job-type-tag jt-${escHtml(c.type_project||'general')}">${escHtml(JOB_TYPES[c.type_project||'general']||c.type_project||'ทั่วไป')}</span> <span class="cust-st ${custStatusClass(c.status)}">${escHtml(c.status||'-')}</span>`;const set=(id,val)=>{const el=document.getElementById(id);if(el)el.textContent=(val==null||val==='')?'-':val};set('cd-desc',c.desc);set('cd-status',c.status);set('cd-contact',c.contact_name);set('cd-phone',c.phone);set('cd-size',c.size);set('cd-price',c.price?Number(c.price).toLocaleString()+' ฿':'-');set('cd-loc',c.loc);set('cd-finish_date',c.supervisor?fmtDate(c.supervisor):'-');set('cd-notes',c.notes);document.getElementById('cd-size-lbl').textContent=cat==='solar'?'ขนาดติดตั้ง':'ขนาด';document.getElementById('cd-finish-lbl').textContent=cat==='solar'?'วันติดตั้งเสร็จ':'วันสิ้นสุด';document.getElementById('dtab-btn-wash').style.display=cat==='solar'?'':'none';document.getElementById('dtab-btn-milestone').style.display=cat==='solar'?'none':'';const wlogs=(c.wash_logs||[]).filter(w=>!w.type||w.type==='wash');document.getElementById('cd-wash-count').textContent=`(${wlogs.length} ครั้ง)`;document.getElementById('cd-wash-body').innerHTML=wlogs.length?`<table class="wash-log-tbl"><thead><tr><th>#</th><th>วันที่</th><th>ทีม/ช่าง</th><th>หมายเหตุ</th><th></th></tr></thead><tbody>${wlogs.map(w=>`<tr><td>${escHtml(w.num)}</td><td>${fmtDate(w.date)}</td><td>${escHtml(w.tech||'-')}</td><td>${escHtml(w.note||'-')}</td><td><form method="POST" action="${URL_WASH_DEL(c.id,w.num)}" onsubmit="return confirm('ลบประวัติ #${escHtml(w.num)}?')"><input type="hidden" name="_token" value="${CSRF}"><button class="btn btn-sm btn-danger" type="submit">ลบ</button></form></td></tr>`).join('')}</tbody></table>`:'<div class="empty-state">ยังไม่มีประวัติการล้าง</div>';document.getElementById('form-add-wash').action=URL_WASH_STORE(c.id);const mlogs=(c.wash_logs||[]).filter(w=>w.type==='milestone');document.getElementById('cd-milestone-body').innerHTML=mlogs.length?mlogs.map((ms,i)=>`<div class="pinfo-card" style="margin-bottom:10px"><div style="font-size:11px;color:#64748b;font-weight:900">${fmtDate(ms.date)}</div><div style="font-weight:900">${escHtml(ms.note||'-')}</div>${ms.by?`<div style="font-size:11px;color:#64748b;font-weight:800">โดย: ${escHtml(ms.by)}</div>`:''}<form method="POST" action="${URL_MILESTONE_DEL(c.id,i)}" onsubmit="return confirm('ลบ?')" style="margin-top:6px"><input type="hidden" name="_token" value="${CSRF}"><button class="btn btn-sm btn-danger" type="submit">ลบ</button></form></div>`).join(''):'<div class="empty-state">ยังไม่มี milestone</div>';document.getElementById('form-add-milestone').action=URL_MILESTONE_STORE(c.id);const linked=SCHED_DATA.filter(s=>s.customer_name===c.name);document.getElementById('cd-schedules').innerHTML=linked.length?`<div class="table-wrap"><table><thead><tr><th>SO</th><th>งาน</th><th>ประเภท</th><th>วันเริ่ม</th><th>วันสิ้นสุด</th><th>ทีม</th></tr></thead><tbody>${linked.map(s=>`<tr><td>${escHtml(s.so_number)}</td><td>${escHtml(s.job_title)}</td><td>${escHtml(JOB_TYPES[s.job_type||'general']||s.job_type)}</td><td>${fmtDate(s.start_date)}</td><td>${fmtDate(s.end_date)}</td><td>${escHtml(s.team_name)}</td></tr>`).join('')}</tbody></table></div>`:'<div class="empty-state">ยังไม่มีงานที่ผูกกับลูกค้านี้</div>';switchDTab('info',document.getElementById('dtab-btn-info'));openModal('modal-cust-detail')}
function switchDTab(name,btn){document.querySelectorAll('.dtab-panel').forEach(p=>p.classList.remove('active'));document.querySelectorAll('.dtab').forEach(b=>b.classList.remove('active'));document.getElementById('dtab-'+name)?.classList.add('active');btn?.classList.add('active')}
function editFromDetail(){if(!_detailCust)return;closeModalById('modal-cust-detail');openCustEdit({dataset:{cust:JSON.stringify(_detailCust)}})}
function openAddWashModal(){if(!_detailCust)return;document.getElementById('form-add-wash').reset();document.getElementById('form-add-wash').action=URL_WASH_STORE(_detailCust.id);openModal('modal-add-wash')}
function openAddMilestoneModal(){if(!_detailCust)return;document.getElementById('form-add-milestone').reset();document.getElementById('form-add-milestone').action=URL_MILESTONE_STORE(_detailCust.id);openModal('modal-add-milestone')}

function openAccAdd(){document.getElementById('acc-modal-title').textContent='เพิ่มบัญชีผู้ใช้ Solar';const f=document.getElementById('form-account');f.action=URL_ACC_STORE;f.reset();openModal('modal-account')}
function openAccEdit(btn){if(!btn?.dataset.acc)return;let a;try{a=JSON.parse(btn.dataset.acc)}catch(e){return}document.getElementById('acc-modal-title').textContent='แก้ไขบัญชี';document.getElementById('form-account').action=URL_ACC_UPDATE(a.id);['no','plane','username','password','email','app_password','customer','inverter'].forEach(k=>{const el=document.getElementById('af-'+k);if(el)el.value=a[k]??''});openModal('modal-account')}
function togglePw(btn){const span=btn.previousElementSibling;if(!span?.classList.contains('acc-pw-text'))return;if(span.textContent==='••••••••'){span.textContent=span.dataset.pw;btn.textContent='ซ่อน'}else{span.textContent='••••••••';btn.textContent='แสดง'}}
function toggleInputPw(id,btn){const inp=document.getElementById(id);if(!inp)return;inp.type=inp.type==='password'?'text':'password';btn.textContent=inp.type==='password'?'ดู':'ซ่อน'}
function copyText(text,btn){navigator.clipboard?.writeText(text).then(()=>{const old=btn.textContent;btn.textContent='คัดลอกแล้ว';setTimeout(()=>btn.textContent=old,1000)}).catch(()=>alert('คัดลอกไม่สำเร็จ'))}
function accCustAutocomp(q){const list=document.getElementById('af-cust-list'),kw=(q||'').toLowerCase().trim();if(!kw){list.classList.remove('open');return}const matches=CUST_DATA.filter(c=>(c.name||'').toLowerCase().includes(kw)).slice(0,6);if(!matches.length){list.classList.remove('open');return}list.innerHTML=matches.map(c=>`<div class="ac-item" onclick="document.getElementById('af-customer').value=${JSON.stringify(c.name||'')};this.parentElement.classList.remove('open')"><div class="ac-item-name">${escHtml(c.name)}</div>${c.desc?`<div class="ac-item-meta">${escHtml(c.desc)}</div>`:''}</div>`).join('');list.classList.add('open')}

const TCAL={
  team:'',
  date:new Date(),
  jobs:[],
  eventClass(job){const seed=String(job.so_number||job.id||job.job_title||'');let hash=0;for(let i=0;i<seed.length;i++){hash=((hash<<5)-hash)+seed.charCodeAt(i);hash|=0}return'evc-'+((Math.abs(hash)%12)+1)},
  eventCode(type){return{solar_install:'SI',solar_wash:'SW',solar_maintenance:'SM',electrical:'EL',civil:'CV',general:'GN'}[type]||'GN'},
  jobStatus(job){
    const today=ymd(new Date());
    if(job.end_date<today)return{label:'เสร็จแล้ว',cls:'sls-done'};
    if(job.start_date>today)return{label:'กำลังจะมา',cls:'sls-upcoming'};
    return{label:'กำลังทำ',cls:'sls-doing'};
  },
  nav(step){this.date.setMonth(this.date.getMonth()+step);this.render()},
  gotoToday(){this.date=new Date();this.render()},
  monthJobs(){
    const y=this.date.getFullYear(),m=this.date.getMonth();
    const monthStart=`${y}-${String(m+1).padStart(2,'0')}-01`;
    const monthEnd=`${y}-${String(m+1).padStart(2,'0')}-${String(new Date(y,m+1,0).getDate()).padStart(2,'0')}`;
    const filter=document.getElementById('tcal-type-filter')?.value||'all';
    return this.jobs.filter(s=>{
      if(s.end_date<monthStart||s.start_date>monthEnd)return false;
      if(filter!=='all'&&(s.job_type||'general')!==filter)return false;
      return true;
    });
  },
  renderList(){
    const tbody=document.getElementById('tcal-list-tbody'),countEl=document.getElementById('tcal-list-count');
    if(!tbody)return;
    const kw=(document.getElementById('tcal-list-search')?.value||'').toLowerCase().trim();
    let jobs=this.monthJobs();
    if(kw)jobs=jobs.filter(s=>(`${s.so_number||''} ${s.customer_name||''} ${s.job_title||''}`).toLowerCase().includes(kw));
    jobs.sort((a,b)=>(a.start_date||'').localeCompare(b.start_date||''));
    if(countEl)countEl.textContent=`${jobs.length} งาน`;
    if(!jobs.length){tbody.innerHTML=`<tr><td colspan="6" class="sched-list-empty">ไม่มีงานในเดือนนี้</td></tr>`;return}
    tbody.innerHTML=jobs.map((s,i)=>{
      const st=this.jobStatus(s);
      const sameDay=s.start_date===s.end_date;
      const dateHtml=sameDay?fmtDate(s.start_date):`${fmtDate(s.start_date)}<small>ถึง ${fmtDate(s.end_date)}</small>`;
      return `<tr data-sched='${JSON.stringify(s).replace(/'/g,"&#39;")}' onclick="closeTeamCalendar();setTimeout(()=>openEditSchedFromEl(this),100)" style="cursor:pointer">
        <td>${i+1}</td>
        <td><span class="sched-list-so">${escHtml(s.so_number||'-')}</span></td>
        <td><div class="sched-list-cust">${escHtml(s.customer_name||'-')}</div>${s.job_location?`<div style="font-size:11px;color:#64748b;font-weight:700">${escHtml(s.job_location)}</div>`:''}</td>
        <td><div class="sched-list-job">${escHtml(s.job_title||'-')}</div><span class="job-type-tag jt-${escHtml(s.job_type||'general')}" style="margin-top:4px">${escHtml(JOB_TYPES[s.job_type||'general']||s.job_type||'-')}</span></td>
        <td><div class="sched-list-date">${dateHtml}</div></td>
        <td><span class="sched-list-status ${st.cls}">${st.label}</span></td>
      </tr>`;
    }).join('');
  },
  render(){
    const grid=document.getElementById('tcal-month-grid');if(!grid)return;
    const thMonths=['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
    const y=this.date.getFullYear(),m=this.date.getMonth();
    const filter=document.getElementById('tcal-type-filter')?.value||'all';
    document.getElementById('tcal-month-name').textContent=`${thMonths[m]} ${y+543}`;
    const eyebrow=document.getElementById('tcal-eyebrow');
    if(eyebrow)eyebrow.textContent=`SCHEDULE · ${thMonths[m].toUpperCase()} ${y+543}`;
    const first=new Date(y,m,1).getDay(),total=new Date(y,m+1,0).getDate(),prev=new Date(y,m,0).getDate(),today=ymd(new Date());
    let html='';
    for(let i=first-1;i>=0;i--)html+=`<div class="sched-day other"><div class="sched-day-num">${prev-i}</div></div>`;
    for(let d=1;d<=total;d++){
      const ds=`${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
      let dayJobs=this.jobs.filter(s=>s.start_date<=ds&&s.end_date>=ds).map(s=>({...s,job_type:s.job_type||'general'}));
      if(filter!=='all')dayJobs=dayJobs.filter(s=>s.job_type===filter);
      const visible=dayJobs.slice(0,4);
      html+=`<div class="sched-day ${ds===today?'today':''}"><div class="sched-day-num">${d}</div>${dayJobs.length>0?`<div class="sched-day-count">${dayJobs.length}</div>`:''}${visible.map(s=>`<button type="button" class="sched-event ${this.eventClass(s)}" data-sched='${JSON.stringify(s).replace(/'/g,"&#39;")}' onclick="event.stopPropagation();closeTeamCalendar();setTimeout(()=>openEditSchedFromEl(this),100)"><span class="ev-code">${this.eventCode(s.job_type)}</span>${escHtml(s.customer_name||s.job_title||s.so_number)}</button>`).join('')}${dayJobs.length>4?`<div class="sched-more">+${dayJobs.length-4} รายการ</div>`:''}</div>`;
    }
    const rest=(7-((first+total)%7))%7;
    for(let i=1;i<=rest;i++)html+=`<div class="sched-day other"><div class="sched-day-num">${i}</div></div>`;
    grid.innerHTML=html;
    this.renderList();
  }
};

function openTeamCalendar(team){
  TCAL.team=team;
  TCAL.date=new Date();
  TCAL.jobs=SCHED_DATA.filter(s=>s.team_name===team);
  document.getElementById('tcal-team-name').textContent=team||'-';
  document.getElementById('tcal-job-count').textContent=`${TCAL.jobs.length} งาน`;
  // populate filter options จาก JOB_TYPES
  const filterSel=document.getElementById('tcal-type-filter');
  if(filterSel&&filterSel.options.length<=1){
    Object.entries(JOB_TYPES).forEach(([k,v])=>{
      const opt=document.createElement('option');opt.value=k;opt.textContent=v;filterSel.appendChild(opt);
    });
  }
  if(filterSel)filterSel.value='all';
  const searchEl=document.getElementById('tcal-list-search');if(searchEl)searchEl.value='';
  TCAL.render();
  document.getElementById('tcal-overlay').classList.add('open');
  document.body.style.overflow='hidden';
}

function closeTeamCalendar(){
  document.getElementById('tcal-overlay').classList.remove('open');
  if(!document.querySelector('.overlay.open,.tcal-overlay.open'))document.body.style.overflow='';
}

const BORROW_KEY='triple3e_borrow_rows';let BORROW_ROWS=[];
function seedBorrowRows(){const saved=localStorage.getItem(BORROW_KEY);if(saved){try{BORROW_ROWS=JSON.parse(saved)||[]}catch(e){BORROW_ROWS=[]}return}const first=TECH_DATA[0]||{};BORROW_ROWS=[{id:Date.now()+1,tech_id:first.emp_id||'',tech_name:first.emp_name||first.emp_id||'-',date:ymd(new Date()),time:'09:00',item:'สว่านไฟฟ้า 13 มม.',qty:1,unit:'เครื่อง',purpose:'เจาะยึดโครงสร้าง',status:'อนุมัติแล้ว'}];persistBorrowRows()}
function persistBorrowRows(){localStorage.setItem(BORROW_KEY,JSON.stringify(BORROW_ROWS))}
function openBorrowModal(){seedBorrowRows();fillBorrowTechOptions();renderBorrowRows();openModal('borrow-overlay')}
function closeBorrowModal(){closeModalById('borrow-overlay')}
function fillBorrowTechOptions(){const sel=document.getElementById('bf-tech');sel.innerHTML='<option value="">-- เลือกช่าง --</option>'+TECH_DATA.map(t=>`<option value="${escHtml(t.emp_id)}">${escHtml((t.emp_name||t.emp_id||'-')+(t.emp_team?' ('+t.emp_team+')':''))}</option>`).join('')}
function renderBorrowRows(){const tbody=document.getElementById('borrow-tbody'),kw=(document.getElementById('borrow-search')?.value||'').toLowerCase().trim(),date=document.getElementById('borrow-filter-date')?.value||'';const rows=BORROW_ROWS.filter(r=>(!kw||`${r.tech_name} ${r.item} ${r.purpose} ${r.status}`.toLowerCase().includes(kw))&&(!date||r.date===date));tbody.innerHTML=rows.length?rows.map((r,i)=>{const cls=r.status==='อนุมัติแล้ว'?'borrow-ok':r.status==='ไม่อนุมัติ'?'borrow-no':'borrow-wait';return `<tr><td>${i+1}</td><td>${fmtDate(r.date)}<br><small>${escHtml(r.time||'-')}</small></td><td>${escHtml(r.tech_name)}</td><td>${escHtml(r.item)}</td><td>${escHtml(r.qty)}</td><td>${escHtml(r.unit)}</td><td>${escHtml(r.purpose||'-')}</td><td><span class="borrow-status ${cls}">${escHtml(r.status)}</span></td><td><button class="borrow-action borrow-edit" onclick="openBorrowForm(${r.id})">✎</button> <button class="borrow-action borrow-delete" onclick="deleteBorrowRow(${r.id})">×</button></td></tr>`}).join(''):'<tr><td colspan="9" style="text-align:center;padding:28px;color:#64748b">ยังไม่มีรายการเบิกของ</td></tr>';document.getElementById('borrow-count').textContent=`แสดง ${rows.length} รายการ`}
function openBorrowForm(id=null){fillBorrowTechOptions();const row=BORROW_ROWS.find(r=>r.id===id)||{};document.getElementById('borrow-edit-id').value=id||'';document.getElementById('borrow-form-title').textContent=id?'แก้ไขรายการเบิกของ':'เพิ่มรายการเบิกของ';document.getElementById('bf-tech').value=row.tech_id||'';document.getElementById('bf-item').value=row.item||'';document.getElementById('bf-date').value=row.date||ymd(new Date());document.getElementById('bf-time').value=row.time||new Date().toTimeString().slice(0,5);document.getElementById('bf-qty').value=row.qty||1;document.getElementById('bf-unit').value=row.unit||'';document.getElementById('bf-purpose').value=row.purpose||'';document.getElementById('bf-status').value=row.status||'อนุมัติแล้ว';syncBorrowUnit();openModal('borrow-form-overlay')}
function closeBorrowForm(){closeModalById('borrow-form-overlay')}
function syncBorrowUnit(){const item=document.getElementById('bf-item'),unit=document.getElementById('bf-unit'),selected=item.options[item.selectedIndex];if(selected?.dataset.unit&&!unit.value)unit.value=selected.dataset.unit}
function saveBorrowRow(){const techId=document.getElementById('bf-tech').value,item=document.getElementById('bf-item').value,date=document.getElementById('bf-date').value,time=document.getElementById('bf-time').value,qty=document.getElementById('bf-qty').value;if(!techId||!item||!date||!time||!qty){alert('กรุณากรอกข้อมูลให้ครบ');return}const tech=TECH_DATA.find(t=>String(t.emp_id)===String(techId)),id=Number(document.getElementById('borrow-edit-id').value),payload={id:id||Date.now(),tech_id:techId,tech_name:tech?.emp_name||tech?.emp_id||'-',date,time,item,qty,unit:document.getElementById('bf-unit').value||'-',purpose:document.getElementById('bf-purpose').value||'-',status:document.getElementById('bf-status').value};BORROW_ROWS=id?BORROW_ROWS.map(r=>r.id===id?payload:r):[payload,...BORROW_ROWS];persistBorrowRows();closeBorrowForm();renderBorrowRows()}
function deleteBorrowRow(id){if(!confirm('ลบรายการนี้?'))return;BORROW_ROWS=BORROW_ROWS.filter(r=>r.id!==id);persistBorrowRows();renderBorrowRows()}

const SCHED_BOARD={
  date:new Date(),mode:'month',
  setMode(m,btn){this.mode=m;document.querySelectorAll('.sched-mode').forEach(b=>b.classList.remove('active'));btn.classList.add('active');this.render()},
  nav(step){this.date.setMonth(this.date.getMonth()+step);this.render()},
  eventClass(job){const seed=String(job.so_number||job.id||job.job_title||'');let hash=0;for(let i=0;i<seed.length;i++){hash=((hash<<5)-hash)+seed.charCodeAt(i);hash|=0}return'evc-'+((Math.abs(hash)%12)+1)},
  eventCode(type){return{solar_install:'SI',solar_wash:'SW',solar_maintenance:'SM',electrical:'EL',civil:'CV',general:'GN',design:'DS',site:'ST',commission:'CO',testing:'TS',meeting:'MT',survey:'SV',report:'RP'}[type]||'GN'},
  jobStatus(job){
    const today=ymd(new Date());
    if(job.end_date<today)return{key:'done',label:'เสร็จแล้ว',cls:'sls-done'};
    if(job.start_date>today)return{key:'upcoming',label:'กำลังจะมา',cls:'sls-upcoming'};
    return{key:'doing',label:'กำลังทำ',cls:'sls-doing'};
  },
  monthJobs(){
    const y=this.date.getFullYear(),m=this.date.getMonth();
    const monthStart=`${y}-${String(m+1).padStart(2,'0')}-01`;
    const monthEnd=`${y}-${String(m+1).padStart(2,'0')}-${String(new Date(y,m+1,0).getDate()).padStart(2,'0')}`;
    const filter=document.getElementById('sched-type-filter')?.value||'all';
    return SCHED_DATA.filter(s=>{
      if(s.end_date<monthStart||s.start_date>monthEnd)return false;
      if(filter!=='all'&&(s.job_type||'general')!==filter)return false;
      return true;
    });
  },
  renderList(){
    const tbody=document.getElementById('sched-list-tbody');
    const countEl=document.getElementById('sched-list-count');
    if(!tbody)return;
    const kw=(document.getElementById('sched-list-search')?.value||'').toLowerCase().trim();
    let jobs=this.monthJobs();
    if(kw)jobs=jobs.filter(s=>(`${s.so_number||''} ${s.customer_name||''} ${s.job_title||''} ${s.team_name||''}`).toLowerCase().includes(kw));
    jobs.sort((a,b)=>(a.start_date||'').localeCompare(b.start_date||''));
    if(countEl)countEl.textContent=`${jobs.length} งาน`;
    if(!jobs.length){tbody.innerHTML=`<tr><td colspan="7" class="sched-list-empty">ไม่มีงานในเดือนนี้</td></tr>`;return}
    tbody.innerHTML=jobs.map((s,i)=>{
      const st=this.jobStatus(s);
      const sameDay=s.start_date===s.end_date;
      const dateHtml=sameDay?fmtDate(s.start_date):`${fmtDate(s.start_date)}<small>ถึง ${fmtDate(s.end_date)}</small>`;
      return `<tr data-sched='${JSON.stringify(s).replace(/'/g,"&#39;")}' onclick="openEditSchedFromEl(this)">
        <td>${i+1}</td>
        <td><span class="sched-list-so">${escHtml(s.so_number||'-')}</span></td>
        <td><div class="sched-list-cust">${escHtml(s.customer_name||'-')}</div>${s.job_location?`<div style="font-size:11px;color:#64748b;font-weight:700">${escHtml(s.job_location)}</div>`:''}</td>
        <td><div class="sched-list-job">${escHtml(s.job_title||'-')}</div><span class="job-type-tag jt-${escHtml(s.job_type||'general')}" style="margin-top:4px">${escHtml(JOB_TYPES[s.job_type||'general']||s.job_type||'-')}</span></td>
        <td><span class="sched-list-team">${escHtml(s.team_name||'-')}</span></td>
        <td><div class="sched-list-date">${dateHtml}</div></td>
        <td><span class="sched-list-status ${st.cls}">${st.label}</span></td>
      </tr>`;
    }).join('');
  },
  render(){
    const grid=document.getElementById('sched-month-grid');if(!grid)return;
    const thMonthsFull=['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
    const y=this.date.getFullYear(),m=this.date.getMonth();
    const filter=document.getElementById('sched-type-filter')?.value||'all';
    document.getElementById('sched-board-month').textContent=`${thMonthsFull[m]} ${y+543}`;
    const eyebrow=document.querySelector('.sched-eyebrow');
    if(eyebrow)eyebrow.textContent=`SCHEDULE · ${thMonthsFull[m].toUpperCase()} ${y+543}`;
    const first=new Date(y,m,1).getDay(),total=new Date(y,m+1,0).getDate(),prev=new Date(y,m,0).getDate(),today=ymd(new Date());
    let html='';
    for(let i=first-1;i>=0;i--)html+=`<div class="sched-day other"><div class="sched-day-num">${prev-i}</div></div>`;
    for(let d=1;d<=total;d++){
      const ds=`${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
      let jobs=SCHED_DATA.filter(s=>s.start_date<=ds&&s.end_date>=ds).map(s=>({...s,job_type:s.job_type||'general'}));
      if(filter!=='all')jobs=jobs.filter(s=>s.job_type===filter);
      const visible=jobs.slice(0,4);
      html+=`<div class="sched-day ${ds===today?'today':''}"><div class="sched-day-num">${d}</div>${jobs.length>0?`<div class="sched-day-count">${jobs.length}</div>`:''}
      ${visible.map(s=>`<button type="button" class="sched-event ${this.eventClass(s)}" data-sched='${JSON.stringify(s).replace(/'/g,"&#39;")}' onclick="openEditSchedFromEl(this)">
  <span class="sched-event-title">${escHtml(s.job_title || s.customer_name || s.so_number || '-')}</span>
  <span class="sched-event-team">ทีม: ${escHtml(s.team_name || '-')}</span>
</button>`).join('')}

      ${jobs.length>4?`<div class="sched-more">+${jobs.length-4} รายการ</div>`:''}</div>`;
    }
    const rest=(7-((first+total)%7))%7;
    for(let i=1;i<=rest;i++)html+=`<div class="sched-day other"><div class="sched-day-num">${i}</div></div>`;
    grid.innerHTML=html;
    this.renderList();
  }
};

let ROSTER_SKILL='all',ROSTER_SEARCH='';
function filterRosterSkill(skill,btn){ROSTER_SKILL=skill;btn.closest('.roster-filter-row')?.querySelectorAll('.roster-chip').forEach(b=>b.classList.remove('active'));btn.classList.add('active');applyRosterFilter()}
function filterRosterSearch(q){ROSTER_SEARCH=(q||'').toLowerCase().trim();applyRosterFilter()}
function applyRosterFilter(){let shown=0,total=0;document.querySelectorAll('#roster-grid .emp-card').forEach(card=>{total++;const skillData=(card.dataset.skill||'').toLowerCase();const searchData=(card.dataset.search||'').toLowerCase();const ok=(ROSTER_SKILL==='all'||skillData.includes(String(ROSTER_SKILL).toLowerCase()))&&(!ROSTER_SEARCH||searchData.includes(ROSTER_SEARCH));card.style.display=ok?'':'none';if(ok)shown++});const c=document.getElementById('roster-count');if(c)c.textContent=`ทักษะ · ${shown} / ${total}`}
function filterCertCards(q){const kw=(q||'').toLowerCase().trim();document.querySelectorAll('#cert-grid .cert-card').forEach(card=>card.style.display=!kw||(card.dataset.certSearch||'').includes(kw)?'':'none')}
function openCertDetail(btn){let items=[];try{items=JSON.parse(btn.dataset.certItems||'[]')}catch(e){}document.getElementById('cert-detail-title').textContent=btn.dataset.certName||'-';document.getElementById('cert-detail-sub').textContent=`${items.length} คนในองค์กร`;document.getElementById('cert-holder-list').innerHTML=items.length?items.map(item=>{const tech=item.tech||{},lic=item.license||{},name=tech.emp_name||tech.emp_id||'-';return `<div class="cert-holder"><div class="cert-holder-avatar">${escHtml(String(name).slice(0,2))}</div><div class="cert-holder-main"><div class="cert-holder-name">${escHtml(name)}</div><div class="cert-holder-meta">${escHtml(tech.emp_team||'-')}${lic.doc_no?' · เลขที่ '+escHtml(lic.doc_no):''}${lic.date_issued?' · ออก: '+escHtml(lic.date_issued):''}</div></div>${lic.file?`<a class="cert-file-link" href="/storage/${escHtml(lic.file)}" target="_blank">เปิดไฟล์</a>`:''}</div>`}).join(''):'<div class="empty-state">ยังไม่มีข้อมูลใบรับรอง</div>';openModal('cert-detail-overlay')}
function closeCertDetail(){closeModalById('cert-detail-overlay')}
document.addEventListener('click',e=>{
  const tl=e.target.closest('[data-tl-nav]');
  if(tl){e.stopPropagation();TL.nav(tl.dataset.tlPrefix,tl.dataset.tlNav==='prev'?-1:1);return}
  document.querySelectorAll('.autocomp-list.open').forEach(list=>{
    if(!list.parentElement.contains(e.target))list.classList.remove('open')
  })
});

/* คงโค้ด JS เดิมทั้งหมดด้านบนไว้ แล้ววางส่วนนี้แทน normalizeSkillTags ที่ซ้ำ/พัง */

function normalizeSkillTags() {
  const MAX = 4;

  document.querySelectorAll('#roster-grid .emp-card-skills').forEach(container => {
    container.querySelectorAll('.plus-tag').forEach(tag => tag.remove());

    const tags = Array.from(container.querySelectorAll('.emp-skill-tag'));
    tags.forEach(tag => {
      tag.style.display = '';
      tag.classList.remove('plus-tag');
    });

    if (tags.length > MAX) {
      const hidden = tags.length - (MAX - 1);

      tags.slice(MAX - 1).forEach(tag => {
        tag.style.display = 'none';
      });

      const plus = document.createElement('span');
      plus.className = 'emp-skill-tag plus-tag';
      plus.textContent = '+' + hidden;
      container.appendChild(plus);
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  normalizeSkillTags();

  document.querySelectorAll('#roster-grid .emp-card').forEach((card, i) => {
    card.style.animationDelay = `${i * 0.07}s`;
  });
});
function filterRosterSkill(skill, btn) {
  ROSTER_SKILL = skill;
  btn.closest('.roster-filter-row')?.querySelectorAll('.roster-chip').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  applyRosterFilter();
}

function filterRosterSearch(q) {
  ROSTER_SEARCH = (q || '').toLowerCase().trim();
  applyRosterFilter();
}

function applyRosterFilter() {
  let shown = 0, total = 0;
  document.querySelectorAll('#roster-grid .emp-card').forEach(card => {
    total++;
    const skillData = (card.dataset.skill || '').toLowerCase();
    const searchData = (card.dataset.search || '').toLowerCase();
    const ok = (ROSTER_SKILL === 'all' || skillData.includes(String(ROSTER_SKILL).toLowerCase()))
             && (!ROSTER_SEARCH || searchData.includes(ROSTER_SEARCH));
    card.style.display = ok ? '' : 'none';
    if (ok) shown++;
  });
  const c = document.getElementById('roster-count');
  if (c) c.textContent = `ทักษะ · ${shown} / ${total}`;
}
</script>
<script>
(() => {
  const state = { skill: 'all', search: '' };

  const norm = (v) =>
    String(v || '').replace(/\s+/g, ' ').trim().toLowerCase();

  function cardSkills(card) {
    const dataSkill = card.getAttribute('data-skill') || '';
    const chipSkill = Array.from(card.querySelectorAll('.emp-skill-tag'))
      .map(el => el.textContent.replace('#', ''))
      .join(' ');

    return norm(dataSkill + ' ' + chipSkill);
  }

  function cardSearch(card) {
    return norm(
      (card.getAttribute('data-search') || '') + ' ' + card.textContent
    );
  }

  function applyRosterFilter() {
    let shown = 0;
    let total = 0;

    document.querySelectorAll('#roster-grid .emp-card').forEach(card => {
      total++;

      const skillOk =
        state.skill === 'all' || cardSkills(card).includes(state.skill);

      const searchOk =
        !state.search || cardSearch(card).includes(state.search);

      const visible = skillOk && searchOk;

      card.style.removeProperty('display');
      if (!visible) card.style.setProperty('display', 'none', 'important');

      if (visible) shown++;
    });

    const count = document.getElementById('roster-count');
    if (count) count.textContent = `ทักษะ · ${shown} / ${total}`;
  }

  function setSkillFromButton(btn) {
    let label = norm(btn.textContent).replace(/^#/, '');

    if (label.includes('ทุกทักษะ')) label = 'all';

    state.skill = label;

    document
      .querySelectorAll('#panel-teams .roster-chip')
      .forEach(chip => chip.classList.remove('active'));

    btn.classList.add('active');
    applyRosterFilter();
  }

  function setSearch(value) {
    state.search = norm(value);

    document
      .querySelectorAll('#panel-teams .search-inp, #panel-teams .roster-search')
      .forEach(input => {
        if (input.value !== value) input.value = value;
      });

    applyRosterFilter();
  }

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('#panel-teams .roster-chip');
    if (!btn) return;

    e.preventDefault();
    e.stopImmediatePropagation();
    setSkillFromButton(btn);
  }, true);

  document.addEventListener('input', (e) => {
    if (!e.target.matches('#panel-teams .search-inp, #panel-teams .roster-search')) return;

    e.stopImmediatePropagation();
    setSearch(e.target.value);
  }, true);

  window.filterRosterSkill = (skill, btn) => {
    state.skill = norm(skill) || 'all';
    if (btn) setSkillFromButton(btn);
    else applyRosterFilter();
  };

  window.filterRosterSearch = setSearch;
  window.applyRosterFilter = applyRosterFilter;

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', applyRosterFilter);
  } else {
    applyRosterFilter();
  }
})();
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.team-card').forEach((card, i) => {
    card.style.animationDelay = `${i * 0.08}s`;
  });
});

</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('#overlay .overlay[id^="modal-"]').forEach(modal => {
    document.body.appendChild(modal);
  });
});

window.openModal = function(id) {
  const modal = document.getElementById(id);
  if (!modal) return;

  if (modal.closest('#overlay') && modal.id !== 'overlay') {
    document.body.appendChild(modal);
  }

  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
};

window.openAddSchedModal = window.openAddSchedModal || function() {
  openModal('modal-sched');
};
function fmtPhone(v) {
  const d = String(v || '').replace(/\D/g, '');
  if (!d) return '-';
  if (d.length === 10) return `${d.slice(0, 3)}-${d.slice(3, 6)}-${d.slice(6, 10)}`;
  if (d.length === 9) return `${d.slice(0, 3)}-${d.slice(3, 6)}-${d.slice(6, 9)}`;
  return v || '-';
}
function setHeadTeamBeforeSubmit(prefix) {
  const pos = document.getElementById(prefix + '-emp_position')?.value;
  if (pos !== 'หัวหน้าทีม') return true;

  const form = document.getElementById(prefix === 'add' ? 'form-add-tech' : 'form-edit-tech');
  const teamSelect = document.getElementById(prefix === 'add' ? 'add-team-select' : 'et-team-select');
  const nameInput = form?.querySelector('input[name="emp_name"]');
  const empIdInput = prefix === 'add'
    ? form?.querySelector('input[name="emp_id"]')
    : document.getElementById('et-emp_id');

  const teamName = (nameInput?.value || empIdInput?.value || '').trim();

  if (!teamName) return true;

  let opt = Array.from(teamSelect.options).find(o => o.value === teamName);
  if (!opt) {
    opt = new Option(teamName, teamName, true, true);
    teamSelect.add(opt);
  }

  teamSelect.value = teamName;
  return true;
}

document.getElementById('form-add-tech')?.addEventListener('submit', () => {
  setHeadTeamBeforeSubmit('add');
});

document.getElementById('form-edit-tech')?.addEventListener('submit', () => {
  setHeadTeamBeforeSubmit('et');
});
function handlePositionChange(prefix) {
  const isHead = document.getElementById(prefix + '-emp_position')?.value === 'หัวหน้าทีม';
  const team = document.getElementById(prefix + '-team-wrap');
  const info = document.getElementById(prefix + '-head-info');

  if (team) team.style.display = isHead ? 'none' : '';
  if (info) info.style.display = isHead ? '' : 'none';

  if (isHead) setHeadTeamBeforeSubmit(prefix);
}
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    document.querySelectorAll('.flash').forEach(el => {
      el.classList.add('is-hiding');

      setTimeout(() => {
        el.remove();
      }, 400);
    });
  }, 5000);
});
</script>
</body>
</html>