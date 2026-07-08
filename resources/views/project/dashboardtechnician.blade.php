<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Triple 3E Group — ระบบจัดการทักษะช่าง</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@php
  $teams = collect($teams ?? [])
    ->filter(fn($team) => trim((string) data_get($team, 'team_name', '')) !== '')
    ->unique(fn($team) => trim((string) data_get($team, 'team_name', '')))
    ->values();
  $technicians = collect($technicians ?? []);
  $schedules = collect($schedules ?? []);
  $customers = collect($customers ?? []);
  $accounts = collect($accounts ?? []);
  $aircons = collect($aircons ?? []);
  $washAlerts = collect($washAlerts ?? []);
  $stats = $stats ?? ['total_tech' => $technicians->count()];
  $availableTeams = collect($availableTeams ?? $teams->pluck('team_name')->filter()->values())
    ->filter()
    ->unique()
    ->values();
  $airconTotal = $aircons->count();
  $airconCleaned = $aircons->where('status', 'cleaned')->count();
  $airconPending = $aircons->where('status', 'pending')->count();

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
    foreach (($tech->licenses ?? []) as $licIndex => $lic) {
      $title = trim($lic['title'] ?? '');
      if ($title === '') continue;
      if (!$certGroups->has($title)) $certGroups[$title] = collect();
      $certGroups[$title]->push(['tech' => $tech, 'license' => $lic, 'license_index' => $licIndex]);
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
.sb-tab .label {
  flex: 1;
  min-width: 0;
}
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
.sb-tab .nav-badge-count {
  width: 34px;
  min-width: 34px;
  height: 22px;
  margin-left: auto;
  flex: 0 0 34px;
  line-height: 1;
  transform: none !important;
}
.sb-tab:hover .nav-badge-count { transform: none !important }
.sb-tab.active .nav-badge-count {
  background: rgba(255,255,255,.24); color: #fff; border-color: rgba(255,255,255,.28);
  animation: none;
}

.sb-end { padding: 14px 16px; border-top: 1px solid var(--line); display: grid; gap: 8px; background: var(--navy-25) }
.main   { margin-left: 260px; padding: 24px 32px }
.sb-toggle { display: none }

/* ============================================================
   BUTTONS
   ============================================================ */
.btn {
  border: 0; border-radius: var(--radius-sm);
font-weight: 600;
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
.search-inp, .finput, .roster-search, .borrow-input, .sched-select {
  border: 1px solid var(--line); background: var(--white);
  border-radius: var(--radius-sm); outline: none; color: var(--dk);
  font-weight: 500; transition: border-color .18s, box-shadow .18s;
}
.search-inp { height: 40px; padding: 0 14px; min-width: 250px }
.search-inp:focus, .finput:focus,
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
.jt-solar_install     { background: #e0f2fe; color: #075985; border: 1px solid #7dd3fc }
.jt-solar_wash        { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d }
.jt-solar_maintenance { background: #ccfbf1; color: #0f766e; border: 1px solid #5eead4 }
.jt-electrical        { background: #e0e7ff; color: #3730a3; border: 1px solid #a5b4fc }
.jt-civil             { background: #ede9fe; color: #5b21b6; border: 1px solid #c4b5fd }
.jt-general           { background: #ffe4e6; color: #be123c; border: 1px solid #fda4af }

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
.roster-filter {
  display: grid;
  grid-template-columns: minmax(260px, 1fr) minmax(220px, 320px) auto;
  gap: 12px;
  align-items: end;
  background: var(--navy-25);
  border: 1px solid var(--line);
  border-radius: var(--radius-md);
  padding: 14px 16px;
  margin-bottom: 20px;
}
#panel-accounts .account-monitoring-filter {
  grid-template-columns: minmax(260px, 1fr) auto;
}
#panel-accounts .account-monitoring-filter .btn-solar {
  min-height: 40px;
  padding: 0 16px;
  white-space: nowrap;
}
.roster-filter-row {
  display: grid;
  gap: 7px;
  padding: 0;
}
.roster-filter-actions {
  align-self: end;
}
.roster-filter-actions .roster-add-tech-btn {
  min-width: 120px;
}
.roster-filter-label { font-size: 11px; font-weight: 700; color: var(--muted); letter-spacing: .12em; text-transform: uppercase; min-width: 60px }
.roster-chip {
  border: 1px solid var(--line); border-radius: 999px; padding: 6px 14px;
  background: var(--white); color: var(--text); font-weight: 600; font-size: 13px; cursor: pointer; transition: all .18s;
}
.roster-chip:hover  { border-color: var(--navy-300); color: var(--navy-700); background: var(--navy-25) }
.roster-chip.active { background: var(--navy-900); border-color: var(--navy-900); color: #fff }
.roster-search,
.roster-skill-select,
.team-filter-search,
.team-skill-select {
  width: 100%;
  height: 40px;
  padding: 0 14px;
  border: 1px solid var(--line);
  border-radius: var(--radius-sm);
  background: #fff;
  color: var(--dk);
  font-weight: 800;
  outline: none;
}
.roster-search:focus,
.roster-skill-select:focus,
.team-filter-search:focus,
.team-skill-select:focus {
  border-color: var(--navy-500);
  box-shadow: 0 0 0 3px rgba(170,192,225,.35);
}
.roster-add-tech-btn { white-space: nowrap; height: 42px; padding: 0 18px }
#panel-teams > .panel-header .panel-actions { display: none !important }
@media (max-width: 768px) {
  .roster-filter { grid-template-columns: 1fr }
  #panel-accounts .account-monitoring-filter { grid-template-columns: 1fr }
  #panel-accounts .account-monitoring-filter .btn-solar { width: 100% }
}

/* ============================================================
   ROSTER GRID — Employee Cards (Dark Navy Folder Style)
   ============================================================ */
#roster-grid {
  display: grid;
  align-items: stretch;
}
@media (max-width: 1100px) { #roster-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) } }
@media (max-width: 600px)  { #roster-grid { grid-template-columns: 1fr } }

#roster-grid .emp-card {
flex-direction: column; height: 100%;
  cursor: pointer; animation: popIn .45s cubic-bezier(.34,1.4,.64,1) forwards;
  opacity: 0;
  transition: transform .22s ease;
}#roster-grid .emp-card .emp-card-stripe { display: none }

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

/* Folder tab */#roster-grid .emp-card.is-head .emp-card-body::before { background: #0a3fad }
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
text-align: center; overflow: hidden; text-overflow: ellipsis;
  grid-column: span 1;
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
  pointer-events: none;
}
.team-head-bar > div { position: relative; z-index: 1 }
.team-title { font-weight: 700; font-size: 17px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: 0 }
.team-meta  { font-size: 11px; color: rgba(255,255,255,.72); font-weight: 500; margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis }

.team-cal-btn {
  position: relative; z-index: 3; border: 1px solid rgba(255,255,255,.22);
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
.sched-board {
  min-height: calc(100vh - 48px);
  background: #ffffff;
  color: var(--text);
}
.sched-board-top {
  display: flex; justify-content: space-between; align-items: flex-start;
  gap: 16px; margin-bottom: 20px; padding: 20px 24px;
  background: #ffffff; border: 1px solid var(--line);
  border-radius: var(--radius-lg); box-shadow: var(--shadow-xs);
}
.sched-board-title { font-size: 24px; font-weight: 700; line-height: 1.2; color: var(--navy-900); letter-spacing: -.01em }
.sched-board-sub   { margin-top: 8px; color: var(--muted); font-size: 13px; font-weight: 500 }
.sched-board .sched-eyebrow { color: var(--navy-700); letter-spacing: .12em }
.sched-controls { display: flex; align-items: center; justify-content: flex-end; gap: 10px; flex-wrap: wrap }
.sched-select { height: 38px; min-width: 150px; font-weight: 600 }
.sched-add-job-btn { height: 42px; padding: 0 18px; white-space: nowrap }
.sched-nav-group {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 4px; border: 1px solid var(--line);
  border-radius: var(--radius-md); background: var(--navy-25);
}
.sched-control-month {
  min-width: 170px; height: 38px; display: inline-flex;
  align-items: center; justify-content: center; padding: 0 16px;
  border: 1px solid var(--line); border-radius: var(--radius-sm);
  background: #fff; color: var(--navy-900);
  font-size: 15px; font-weight: 800; white-space: nowrap;
}
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
  background: #ffffff; color: var(--text); font-size: 15px; font-weight: 600; cursor: pointer; transition: all .18s;
}
.sched-nav-btn:hover { border-color: var(--navy-400); color: var(--navy-700) }

.sched-calendar-card {
  background: #ffffff; border: 1px solid var(--line);
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
  background: #ffffff;
  border-right: 1px solid var(--line-soft); border-bottom: 1px solid var(--line-soft);
}
.sched-day:nth-child(7n) { border-right: 0 }
.sched-day.other { background: var(--bg-soft) }
.sched-day.other .sched-day-num { color: #cbd5e1 }
.sched-day-num { display: block; margin: 0 0 9px; font-size: 14px; font-weight: 700; color: var(--text) }
.sched-month-grid .sched-day:nth-child(7n+1) .sched-day-num { color: #ef4444 }
.sched-month-grid .sched-day:nth-child(7n) .sched-day-num { color: #60a5fa }
.sched-day.today .sched-day-num {
  width: auto; height: auto; display: block;
  background: transparent; color: #60a5fa; border-radius: 0;
}
.sched-day-count {
  position: absolute; top: 9px; right: 9px;
  min-width: 18px; height: 18px; display: grid; place-items: center;
  background: var(--navy-50); color: var(--navy-700);
  border-radius: 6px; padding: 0 6px; font-size: 11px; font-weight: 700;
}

/* Calendar events */
.sched-month-grid .sched-day { min-height: 136px }
.sched-event {
  display: flex; width: 100%; min-height: 52px;
  margin-bottom: 5px; padding: 6px 8px;
  border: 1px solid transparent; border-left: 4px solid transparent; border-radius: 6px;
  text-align: left; cursor: pointer; transition: filter .15s, transform .15s, box-shadow .15s;
  flex-direction: column; gap: 4px;
  box-shadow: none;
}
.sched-event:hover { filter: brightness(.98); transform: translateY(-1px); box-shadow: 0 10px 22px rgba(0,0,0,.26), inset 0 0 0 1px rgba(255,255,255,.44) }
.sched-event-title { font-size: 12px; font-weight: 900; line-height: 1.14; overflow: hidden; text-overflow: ellipsis; white-space: nowrap }
.sched-event-meta { display: flex; align-items: center; gap: 5px; min-width: 0 }
.sched-event-type {
  display: inline-flex; align-items: center; align-self: flex-start;
  max-width: 46%; min-height: 18px; padding: 2px 7px;
  border-radius: 5px; background: rgba(255,255,255,.58);
  border: 1px solid rgba(15,23,42,.08);
  font-size: 10px; font-weight: 900; line-height: 1.05;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.sched-event-customer,
.sched-event-team  { display: flex; align-items: center; gap: 4px; min-width: 0; font-size: 10.5px; font-weight: 900; opacity: .9; overflow: hidden; text-overflow: ellipsis; white-space: nowrap }
.sched-event-customer::before {
  content: ''; width: 5px; height: 5px; border-radius: 999px;
  background: currentColor; opacity: .55; flex: 0 0 5px;
}

.evc-install,
.evc-4, .evc-8, .evc-12 {
  background: #eaf2ff;
  color: #1d4ed8;
  border-color: #bfdbfe;
  border-left-color: #3b82f6;
}
.evc-wash,
.evc-3, .evc-7, .evc-11 {
  background: #e9fbea;
  color: #15803d;
  border-color: #bbf7d0;
  border-left-color: #22c55e;
}
.evc-maintenance,
.evc-2, .evc-6, .evc-10 {
  background: #fff3e8;
  color: #c2410c;
  border-color: #fed7aa;
  border-left-color: #f97316;
}
.evc-electrical {
  background: #f6edff;
  color: #7e22ce;
  border-color: #d8b4fe;
  border-left-color: #a855f7;
}
.evc-civil {
  background: #eef2ff;
  color: #4338ca;
  border-color: #c7d2fe;
  border-left-color: #6366f1;
}
.evc-general,
.evc-1, .evc-5, .evc-9 {
  background: #fff1f7;
  color: #be185d;
  border-color: #fbcfe8;
  border-left-color: #ec4899;
}
.evc-install .sched-event-type { background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe }
.evc-wash .sched-event-type { background: #dcfce7; color: #15803d; border-color: #bbf7d0 }
.evc-maintenance .sched-event-type { background: #ffedd5; color: #c2410c; border-color: #fed7aa }
.evc-electrical .sched-event-type { background: #ede9fe; color: #7e22ce; border-color: #d8b4fe }
.evc-civil .sched-event-type { background: #e0e7ff; color: #4338ca; border-color: #c7d2fe }
.evc-general .sched-event-type { background: #fce7f3; color: #be185d; border-color: #fbcfe8 }

.sched-more {
  display: inline-flex; align-items: center;
  margin-top: 2px; padding: 2px 6px;
  border: 0; border-radius: 6px; background: transparent;
  color: var(--navy-700); font-size: 11px; font-weight: 900;
  cursor: pointer;
}
.sched-more:hover { background: var(--navy-50); color: var(--navy-900); text-decoration: underline }
.cal-popup-bg { z-index: 780 !important }
.cal-popup-inner { max-height: min(64vh, 520px); overflow: auto }
.cal-ev-card {
  display: block; width: 100%;
  border-left-width: 5px !important;
  text-align: left; cursor: pointer;
  transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
}
.cal-ev-card:hover { transform: translateY(-1px); box-shadow: 0 10px 22px rgba(14,47,118,.10) }
.cal-ev-card::before { content: none !important; display: none !important }

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
.sls-cancel   { background: var(--danger-bg);  color: var(--danger-text) }
.sched-status-select {
  min-width: 118px; height: 32px;
  border: 1px solid transparent; border-radius: 999px;
  padding: 0 28px 0 12px; outline: none;
  font-size: 12px; font-weight: 800; cursor: pointer;
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.32);
}
.sched-status-select:disabled { opacity: .6; cursor: wait }
.sched-status-select.sls-doing    { background: var(--warn-bg);    color: var(--warn-text);   border-color: #fde68a }
.sched-status-select.sls-done     { background: var(--success-bg); color: var(--success-text); border-color: #86efac }
.sched-status-select.sls-upcoming { background: var(--info-bg);    color: var(--info-text);    border-color: var(--navy-100) }
.sched-status-select.sls-cancel   { background: var(--danger-bg);  color: var(--danger-text);  border-color: #fecaca }
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

.cust-name-btn    { border: 0; background: transparent;ursor: pointer; text-align: left;}.cust-date-chip, .wash-cycle-chip { display: inline-flex; align-items: center; border-radius: 999px; padding: 5px 11px; background: var(--navy-50); color: var(--navy-700); font-size: 12px; font-weight: 600; white-space: nowrap }
.wash-cycle-cell      { display: grid; gap: 3px }
.wash-cycle-cell small { color: var(--muted); font-size: 11px; font-weight: 500 }
#panel-customers .panel-actions {ex-wrap: wrap }
#panel-customers .panel-actions .btn { height: 40px; white-space: nowrap }

/* === CODEX CUSTOMER PROJECT REDESIGN START === */
#panel-customers {
  --cust-ink: #082766;
  --cust-blue: #0f4593;
  --cust-line: #dbe8fb;
  --cust-soft: #f6fbff;
}

#panel-customers .panel-header {
  min-height: 92px;
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  align-items: center;
  gap: 20px;
  padding: 22px 26px;
  margin-bottom: 16px;
  border-radius: 14px;
  background: #123b8d;
  box-shadow: 0 16px 34px rgba(14,47,118,.16);
}

#panel-customers .panel-header::after { display: none }
#panel-customers .panel-title { font-size: 22px; font-weight: 900; letter-spacing: 0 }

.customer-eyebrow {
  margin-bottom: 4px;
  color: rgba(255,255,255,.72);
  font-size: 11px;
  font-weight: 900;
  letter-spacing: .12em;
  text-transform: uppercase;
}

.customer-hero-sub {
  color: rgba(255,255,255,.72);
  font-size: 12px;
  font-weight: 800;
  margin-top: 4px;
}

#panel-customers .customer-site-search-wrap {
  display: grid;
  grid-template-columns: 1fr;
  gap: 7px;
  margin-bottom: 16px;
  background: var(--navy-25);
  border: 1px solid var(--line);
  border-radius: var(--radius-md);
  padding: 14px 16px;
}

#panel-customers .customer-site-search-label {
  color: var(--muted);
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
}

#panel-customers .customer-site-search {
  width: 100%;
  height: 40px;
  padding: 0 14px;
  border: 1px solid var(--line);
  border-radius: var(--radius-sm);
  background: #fff;
  color: var(--dk);
  font-weight: 800;
  outline: none;
}

#panel-customers .customer-site-search:focus {
  border-color: var(--navy-500);
  box-shadow: 0 0 0 3px rgba(170,192,225,.35);
}

#panel-customers .btn-solar {
  height: 46px;
  border-radius: 10px;
  padding: 0 18px;
  background: #0d5be1;
  box-shadow: 0 12px 26px rgba(13,91,225,.28);
  font-weight: 900;
}

.cust-metrics {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 12px;
  margin-bottom: 16px;
}

.cust-metric {
  min-height: 78px;
  padding: 14px 16px;
  border: 1px solid var(--cust-line);
  border-radius: 12px;
  background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
  box-shadow: 0 8px 20px rgba(14,47,118,.06);
}

.cust-metric-label {
  color: #64789d;
  font-size: 11px;
  font-weight: 900;
}

.cust-metric-value {
  margin-top: 3px;
  color: var(--cust-ink);
  font-size: 26px;
  font-weight: 900;
  line-height: 1;
}

.cust-metric-note {
  margin-top: 5px;
  color: #64789d;
  font-size: 11px;
  font-weight: 800;
}

#panel-customers .cust-filter-bar {
  position: sticky;
  top: 0;
  z-index: 4;
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 16px;
  padding: 12px;
  border: 1px solid var(--cust-line);
  border-radius: 12px;
  background: rgba(255,255,255,.94);
  box-shadow: 0 10px 26px rgba(14,47,118,.06);
}

#panel-customers .cust-filter-btn {
  min-height: 36px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  border-radius: 999px;
  padding: 7px 15px;
  border: 1px solid #cfe0f6;
  background: #fff;
  color: var(--cust-ink);
  font-weight: 900;
}

#panel-customers .cust-filter-btn.active {
  background: #123b8d;
  border-color: #123b8d;
  color: #fff;
}

#panel-customers .cust-filter-btn .fbc {
  min-width: 24px;
  height: 20px;
  display: inline-grid;
  place-items: center;
  margin-left: 0;
  padding: 0 7px;
  background: #eaf2ff;
  color: #174ea6;
  font-size: 11px;
  font-weight: 900;
}

#panel-customers .cust-filter-btn.active .fbc {
  background: rgba(255,255,255,.22);
  color: #fff;
}

#panel-customers .wash-alert-bar {
  border-radius: 12px;
  border-color: #fed7aa;
  background: #fffaf2;
  box-shadow: 0 10px 24px rgba(217,119,6,.08);
}

#panel-customers .wash-alert-title {
  color: #9a5b00;
  font-size: 13px;
  font-weight: 900;
}

#panel-customers .wash-alert-chip {
  border-radius: 10px;
  background: #fff;
}

.customer-project-table-wrap {
  border: 1px solid var(--cust-line);
  border-radius: 14px;
  background: #fff;
  overflow: auto;
  box-shadow: 0 14px 34px rgba(14,47,118,.08);
}

.customer-project-table {
  width: 100%;
  min-width: 1160px;
  border-collapse: separate;
  border-spacing: 0;
  table-layout: fixed;
}

.customer-project-table th {
  position: sticky;
  top: 0;
  z-index: 3;
  padding: 15px 18px;
  background: #123f8f;
  color: #fff;
  font-size: 12px;
  font-weight: 900;
  letter-spacing: 0;
  text-transform: none;
  white-space: nowrap;
  vertical-align: middle;
}

.customer-project-table td {
  padding: 16px 18px;
  border-bottom: 1px solid #e4edf9;
  color: var(--cust-ink);
  font-size: 13px;
  font-weight: 800;
  background: #fff;
  line-height: 1.35;
  vertical-align: top;
}

.customer-project-table tbody tr:hover td { background: var(--cust-soft) }
.customer-project-table tbody tr:last-child td { border-bottom: 0 }

.cust-index {
  width: 28px;
  height: 28px;
  display: inline-grid;
  place-items: center;
  border-radius: 8px;
  background: #eef5ff;
  color: #0f4593;
  font-size: 12px;
  font-weight: 900;
}

.cust-name-btn {
  display: inline-flex;
  max-width: 360px;
  padding: 0;
  border: 0;
  background: transparent;
  color: var(--cust-ink);
  cursor: pointer;
  font-size: 15px;
  font-weight: 900;
  line-height: 1.25;
  text-align: left;
}

.cust-name-btn:hover { color: #0d5be1; text-decoration: none }
.cust-desc, .cust-contact {
  display: flex;
  align-items: flex-start;
  gap: 7px;
  margin-top: 3px;
  color: #5f7399;
  font-size: 12px;
  font-weight: 800;
  line-height: 1.25;
  overflow-wrap: anywhere;
}

.cust-contact { font-size: 11px; color: #7184a4 }
.cust-line-label {
  flex: 0 0 auto;
  min-width: 48px;
  color: #0f4593;
  font-size: 11px;
  font-weight: 900;
}
.cust-line-text { min-width: 0 }
.cust-type-stack { display: grid; gap: 6px; justify-items: start }
.cust-type-label {
  color: #64789d;
  font-size: 10px;
  font-weight: 900;
}
.cust-type-plain {
  display: inline-flex;
  align-items: center;
  min-height: 28px;
  padding: 5px 10px;
  border: 1px solid #d9e6f8;
  border-radius: 8px;
  background: #f3f7ff;
  color: var(--cust-ink);
  font-size: 13px;
  font-weight: 900;
  line-height: 1.25;
}
.cust-st { font-size: 11px; font-weight: 900; border: 1px solid transparent }

#panel-customers .job-type-tag {
  min-height: 30px;
  padding: 6px 12px;
  border-radius: 999px;
  font-weight: 900;
}

#panel-customers .cust-date-chip,
#panel-customers .wash-cycle-chip {
  min-height: 30px;
  padding: 6px 12px;
  border-radius: 999px;
  background: #e8f0fb;
  color: #0f4593;
  border: 1px solid #d9e6f8;
  font-weight: 900;
}

#panel-customers .cust-date-plain {
  display: inline-flex;
  align-items: center;
  min-height: 28px;
  padding: 5px 10px;
  border: 1px solid #d9e6f8;
  border-radius: 8px;
  background: #f8fbff;
  color: var(--cust-ink);
  font-size: 13px;
  font-weight: 900;
  line-height: 1.25;
  white-space: nowrap;
}

#panel-customers .wash-cycle-cell {
  display: inline-grid;
  min-width: 220px;
  gap: 3px;
  align-content: start;
}


#panel-customers .wash-cycle-cell small {
  color: #5f7399;
  font-size: 11px;
  font-weight: 800;
}

.cust-muted {
  display: inline-flex;
  align-items: center;
  min-height: 28px;
  padding: 5px 10px;
  border: 1px solid #e4edf9;
  border-radius: 8px;
  background: #f8fbff;
  color: #7890b0;
  font-weight: 900;
  white-space: nowrap;
}

.cust-row-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  align-items: center;
}

.cust-row-actions .btn {
  min-width: 62px;
  height: 34px;
  border-radius: 8px;
  padding: 0 13px;
  font-weight: 900;
  white-space: nowrap;
}

.cust-row-actions .btn-ghost {
  border-color: #cfe0f6;
  color: #0f4593;
  background: #fff;
}

.cust-row-actions .btn-danger {
  color: #b91c1c;
  border-color: #fecaca;
  background: #fee2e2;
}

.cust-empty-filter {
  padding: 34px 16px !important;
  text-align: center;
  color: #64789d !important;
  font-weight: 900 !important;
}

@media (max-width: 1200px) {
  .cust-metrics { grid-template-columns: repeat(3, minmax(0, 1fr)) }
}

@media (max-width: 768px) {
  #panel-customers .panel-header {
    grid-template-columns: 1fr;
    align-items: stretch;
  }

  .cust-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)) }
}
#panel-customers .cust-metrics,
#panel-customers .cust-filter-bar {
  display: none !important;
}
/* === CODEX CUSTOMER PROJECT REDESIGN END === */

/* === AIRCON WASH MENU START === */
#panel-aircons { color: #0e2f76 }
#panel-aircons .aircon-shell { display: grid; gap: 18px }
#panel-aircons .aircon-metrics {
  display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px;
}
#panel-aircons .aircon-metric {
  min-height: 80px; display: flex; align-items: center; justify-content: space-between; gap: 14px;
  background: #fff; border: 1px solid #d8e2f0; border-radius: 12px;
  padding: 14px 16px; box-shadow: var(--shadow-sm); overflow: hidden;
}
#panel-aircons .aircon-metric-copy { min-width: 0 }
#panel-aircons .aircon-metric-label { color: var(--muted); font-size: 12px; font-weight: 800 }
#panel-aircons .aircon-metric-value {
  margin-top: 4px; color: #0e2f76; font-size: 28px; line-height: 1; font-weight: 900;
}
#panel-aircons .aircon-metric-icon {
  width: 46px; height: 46px; flex: 0 0 46px; display: inline-flex; align-items: center; justify-content: center;
  border-radius: 14px; color: #fff; box-shadow: 0 10px 20px rgba(14,47,118,.16);
}
#panel-aircons .aircon-metric-icon svg {
  width: 24px; height: 24px; stroke: currentColor; fill: none; stroke-width: 2;
  stroke-linecap: round; stroke-linejoin: round;
}
#panel-aircons .aircon-metric-icon.total { background: #2563eb }
#panel-aircons .aircon-metric-icon.cleaned { background: #16a34a }
#panel-aircons .aircon-metric-icon.pending { background: #ef4444 }
#panel-aircons .aircon-form-wrap { display: grid; place-items: start center }
#panel-aircons .aircon-form-card {
  width: min(560px, 100%); display: grid; gap: 14px;
  background: #fff; border: 1px solid #d8e2f0; border-radius: 16px;
  padding: 20px; box-shadow: 0 8px 22px rgba(14,47,118,.08);
}
#panel-aircons .aircon-field { display: grid; gap: 7px }
#panel-aircons .aircon-label { color: #0e2f76; font-size: 13px; font-weight: 900 }
#panel-aircons .aircon-label .req { color: #dc2626 }
#panel-aircons .aircon-input,
#panel-aircons .aircon-select,
#panel-aircons .aircon-note {
  width: 100%; min-height: 44px; border: 1px solid #d8e2f0; border-radius: 10px;
  padding: 10px 13px; outline: none; color: #0e2f76; background: #fff; font-weight: 700;
}
#panel-aircons .aircon-note { min-height: 74px; resize: vertical }
#panel-aircons .aircon-input:focus,
#panel-aircons .aircon-select:focus,
#panel-aircons .aircon-note:focus {
  border-color: #4870c8; box-shadow: 0 0 0 3px rgba(170,192,225,.35);
}
#panel-aircons .aircon-upload-stack { display: grid; gap: 10px }
#panel-aircons .aircon-file {
  position: absolute; inline-size: 1px; block-size: 1px; opacity: 0; pointer-events: none;
}
#panel-aircons .aircon-upload {
  min-height: 72px; display: grid; place-items: center; gap: 4px;
  border: 1px dashed #aac0e1; border-radius: 10px; background: #f8fbff;
  color: #3158b5; cursor: pointer; font-size: 13px; font-weight: 900; text-align: center;
  transition: border-color .18s ease, background .18s ease, transform .18s ease;
}
#panel-aircons .aircon-upload:hover { border-color: #4870c8; background: #f0f6ff; transform: translateY(-1px) }
#panel-aircons .aircon-upload svg {
  width: 22px; height: 22px; stroke: currentColor; fill: none; stroke-width: 2;
}
#panel-aircons .aircon-upload small { color: var(--muted); font-size: 11px; font-weight: 700 }
#panel-aircons .aircon-status-group {
  display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px;
}
#panel-aircons .aircon-status-option {
  min-height: 46px; display: flex; align-items: center; justify-content: center; gap: 8px;
  border: 1px solid #d8e2f0; border-radius: 10px; background: #fff; color: #0e2f76;
  cursor: pointer; font-size: 13px; font-weight: 900;
}
#panel-aircons .aircon-status-option input { accent-color: #16a34a }
#panel-aircons .aircon-save {
  width: 100%; min-height: 48px; border: 0; border-radius: 10px;
  background: #2455dc; color: #fff; cursor: pointer; font-size: 15px; font-weight: 900;
  box-shadow: 0 10px 18px rgba(36,85,220,.2);
}
#panel-aircons .aircon-save:hover { background: #173b8e }
#panel-aircons .aircon-list-head {
  display: grid; gap: 12px; margin-top: 6px;
}
#panel-aircons .aircon-list-title { color: #0e2f76; font-size: 16px; font-weight: 900 }
#panel-aircons .aircon-history-filter {
  display: grid;
  grid-template-columns: minmax(260px, 1fr) auto;
  gap: 12px;
  align-items: end;
  background: var(--navy-25);
  border: 1px solid var(--line);
  border-radius: var(--radius-md);
  padding: 14px 16px;
}
#panel-aircons .aircon-history-search-row {
  display: grid;
  gap: 7px;
  min-width: 0;
}
#panel-aircons .aircon-history-label {
  color: var(--muted);
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .12em;
  text-transform: uppercase;
}
#panel-aircons .aircon-history-search {
  width: 100%;
  height: 40px;
  padding: 0 14px;
  border: 1px solid var(--line);
  border-radius: var(--radius-sm);
  background: #fff;
  color: var(--dk);
  font-weight: 800;
  outline: none;
}
#panel-aircons .aircon-history-search:focus {
  border-color: var(--navy-500);
  box-shadow: 0 0 0 3px rgba(170,192,225,.35);
}
#panel-aircons .aircon-status-tag {
  display: inline-flex; align-items: center; border-radius: 999px; padding: 5px 10px;
  font-size: 12px; font-weight: 900; white-space: nowrap;
}
#panel-aircons .aircon-status-tag.cleaned { background: #dcfce7; color: #166534 }
#panel-aircons .aircon-status-tag.pending { background: #fef3c7; color: #92400e }
#panel-aircons .aircon-status-select {
  min-width: 132px; height: 38px; border: 1px solid #d8e2f0; border-radius: 999px;
  padding: 0 32px 0 13px; outline: none; cursor: pointer; font-size: 12px; font-weight: 900;
  appearance: none; background-repeat: no-repeat; background-position: right 12px center; background-size: 10px 6px;
  background-image: linear-gradient(45deg, transparent 50%, currentColor 50%), linear-gradient(135deg, currentColor 50%, transparent 50%);
  background-position: calc(100% - 16px) 15px, calc(100% - 11px) 15px;
  background-size: 5px 5px, 5px 5px; box-shadow: 0 4px 10px rgba(14,47,118,.06);
}
#panel-aircons .aircon-status-select.cleaned {
  background-color: #dcfce7; color: #166534; border-color: #86efac;
}
#panel-aircons .aircon-status-select.pending {
  background-color: #fef3c7; color: #92400e; border-color: #fde68a;
}
#panel-aircons .aircon-status-select:disabled {
  opacity: .65; cursor: wait;
}
#panel-aircons .aircon-code-btn {
  border: 0;
  background: transparent;
  color: #082766;
  cursor: pointer;
  font: inherit;
  font-weight: 900;
  text-align: left;
  padding: 0;
  text-decoration: none;
}
#panel-aircons .aircon-code-btn:hover {
  color: #2455dc;
  text-decoration: underline;
}
#panel-aircons .aircon-date-chip {
  display: inline;
  font-size: 13px;
  font-weight: 900;
  white-space: nowrap;
}
#panel-aircons .aircon-date-chip.latest {
  color: #1d4ed8;
}
#panel-aircons .aircon-date-chip.next {
  color: #b91c1c;
}
#panel-aircons .aircon-date-chip.empty {
  color: #94a3b8;
}
#modal-aircon-history {
  padding: 24px !important;
}
#modal-aircon-history .aircon-history-modal {
  width: min(860px, calc(100vw - 48px));
  max-height: min(92vh, 760px);
  overflow: hidden;
  border-radius: 16px;
  background: #fff;
  border: 1px solid #cfe0f6;
  box-shadow: 0 24px 70px rgba(14,47,118,.28);
}
#modal-aircon-history .aircon-history-head {
  min-height: 78px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 20px 24px;
  background: #123b8d;
  color: #fff;
}
#modal-aircon-history .aircon-history-title {
  font-size: 22px;
  line-height: 1.2;
  font-weight: 900;
}
#modal-aircon-history .aircon-history-sub {
  margin-top: 4px;
  color: rgba(255,255,255,.78);
  font-size: 13px;
  font-weight: 800;
}
#modal-aircon-history .aircon-history-close {
  width: 42px;
  height: 42px;
  border: 1px solid rgba(255,255,255,.36);
  border-radius: 10px;
  background: rgba(255,255,255,.12);
  color: #fff;
  cursor: pointer;
  font-size: 26px;
  line-height: 1;
}
#modal-aircon-history .aircon-history-close:hover {
  background: #fff;
  color: #0e2f76;
}
#modal-aircon-history .aircon-history-body {
  max-height: calc(min(92vh, 760px) - 78px);
  overflow: auto;
  padding: 22px;
  background: #f5feff;
}
#modal-aircon-history .aircon-history-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
}
#modal-aircon-history .aircon-history-card,
#modal-aircon-history .aircon-history-note,
#modal-aircon-history .aircon-history-record {
  border: 1px solid #d8e2f0;
  border-radius: 12px;
  background: #fff;
  padding: 14px 16px;
}
#modal-aircon-history .aircon-history-label {
  color: #64789d;
  font-size: 12px;
  font-weight: 900;
}
#modal-aircon-history .aircon-history-value {
  margin-top: 5px;
  color: #082766;
  font-size: 16px;
  font-weight: 900;
  line-height: 1.35;
}
#modal-aircon-history .aircon-history-note,
#modal-aircon-history .aircon-history-record {
  margin-top: 12px;
}
#modal-aircon-history .aircon-history-record {
  display: grid;
  gap: 10px;
}
#modal-aircon-history .aircon-history-timeline {
  display: grid;
  grid-template-columns: 120px minmax(0, 1fr) auto;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border: 1px solid #d8e2f0;
  border-radius: 10px;
  background: #f9fbff;
}
#modal-aircon-history .aircon-history-status {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 30px;
  padding: 5px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 900;
}
#modal-aircon-history .aircon-history-status.cleaned {
  background: #dcfce7;
  color: #166534;
  border: 1px solid #86efac;
}
#modal-aircon-history .aircon-history-status.pending {
  background: #fef3c7;
  color: #92400e;
  border: 1px solid #fde68a;
}
#modal-aircon-history .aircon-wash-card {
  padding: 14px;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  background: #fff;
}
#modal-aircon-history .aircon-wash-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
}
#modal-aircon-history .aircon-wash-title {
  color: #061d4f;
  font-size: 16px;
  line-height: 1.25;
  font-weight: 900;
}
#modal-aircon-history .aircon-wash-status {
  flex: 0 0 auto;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 28px;
  padding: 4px 13px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 900;
}
#modal-aircon-history .aircon-wash-status.cleaned {
  background: #dcfce7;
  color: #15803d;
}
#modal-aircon-history .aircon-wash-status.pending {
  background: #fef3c7;
  color: #92400e;
}
#modal-aircon-history .aircon-wash-place {
  margin-top: 5px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #355078;
  font-size: 13px;
  font-weight: 800;
}
#modal-aircon-history .aircon-wash-pin {
  width: 7px;
  height: 7px;
  border-radius: 999px;
  background: #ec4899;
  box-shadow: 0 0 0 2px #fbcfe8;
}
#modal-aircon-history .aircon-next-strip {
  margin-top: 12px;
  min-height: 36px;
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 8px 12px;
  border-radius: 8px;
  background: #f0e3ff;
  color: #6d28d9;
  font-size: 13px;
  font-weight: 900;
}
#modal-aircon-history .aircon-next-mark {
  width: 10px;
  height: 14px;
  border: 1.7px solid currentColor;
  border-radius: 3px;
  position: relative;
}
#modal-aircon-history .aircon-next-mark:before,
#modal-aircon-history .aircon-next-mark:after {
  content: "";
  position: absolute;
  left: 2px;
  right: 2px;
  height: 1.5px;
  background: currentColor;
}
#modal-aircon-history .aircon-next-mark:before { top: 3px }
#modal-aircon-history .aircon-next-mark:after { bottom: 3px }
#modal-aircon-history .aircon-wash-meta {
  margin-top: 12px;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 12px;
  color: #5a6f92;
  font-size: 13px;
  font-weight: 800;
}
#modal-aircon-history .aircon-wash-meta span {
  display: inline-flex;
  align-items: center;
  gap: 5px;
}
#modal-aircon-history .aircon-meta-icon {
  width: 13px;
  height: 13px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 3px;
  border: 1px solid #cbd5e1;
  color: #64748b;
  font-size: 9px;
  line-height: 1;
}
#modal-aircon-history .aircon-wash-note {
  margin-top: 12px;
  padding: 10px 12px;
  border: 1px solid #d8e2f0;
  border-radius: 8px;
  background: #f8fbff;
}
#modal-aircon-history .aircon-wash-note-label,
#modal-aircon-history .aircon-wash-gallery-label {
  color: #64789d;
  font-size: 12px;
  font-weight: 900;
}
#modal-aircon-history .aircon-wash-note-text {
  margin-top: 4px;
  color: #082766;
  font-size: 14px;
  font-weight: 800;
  line-height: 1.5;
  white-space: pre-wrap;
}
#modal-aircon-history .aircon-wash-gallery-wrap {
  margin-top: 12px;
}
#modal-aircon-history .aircon-wash-gallery {
  margin-top: 8px;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(108px, 1fr));
  gap: 8px;
}
#modal-aircon-history .aircon-wash-gallery a {
  display: block;
  aspect-ratio: 4 / 3;
  overflow: hidden;
  border: 1px solid #d8e2f0;
  border-radius: 8px;
  background: #eef5ff;
}
#modal-aircon-history .aircon-wash-gallery img {
  width: 100%;
  height: 100%;
  display: block;
  object-fit: cover;
}
#modal-aircon-history .aircon-wash-gallery-empty {
  margin-top: 8px;
  color: #64789d;
  font-size: 13px;
  font-weight: 800;
}
@media (max-width: 768px) {
  #panel-aircons .aircon-metrics,
  #panel-aircons .aircon-status-group { grid-template-columns: 1fr }

  #modal-aircon-history .aircon-history-grid,
  #modal-aircon-history .aircon-history-timeline { grid-template-columns: 1fr }
  #modal-aircon-history .aircon-wash-top,
  #modal-aircon-history .aircon-wash-meta { align-items: flex-start; flex-direction: column }
}
#panel-aircons .aircon-panel-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
#panel-aircons .aircon-add-btn {
  min-height: 40px;
  padding: 0 16px;
  border: 0;
  border-radius: 10px;
  background: #2455dc;
  color: #fff;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 900;
  box-shadow: 0 10px 18px rgba(36,85,220,.18);
  transition: transform .18s ease, background .18s ease, box-shadow .18s ease;
}
#panel-aircons .aircon-add-btn:hover {
  background: #173b8e;
  transform: translateY(-1px);
  box-shadow: 0 14px 24px rgba(36,85,220,.24);
}
#panel-aircons .aircon-add-btn svg {
  width: 18px;
  height: 18px;
  stroke: currentColor;
  fill: none;
  stroke-width: 2.2;
}
#modal-aircon {
  padding: 24px !important;
}
#modal-aircon .aircon-modal {
  width: min(860px, calc(100vw - 48px));
  max-height: min(96vh, 880px);
  border-radius: 16px;
  overflow: hidden;
  background: #fff;
  border: 1px solid #cfe0f6;
  box-shadow: 0 24px 70px rgba(14,47,118,.28);
}
#modal-aircon .aircon-modal-head {
  min-height: 78px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  padding: 20px 24px;
  background: linear-gradient(135deg, #0e2f76 0%, #2455dc 100%);
  color: #fff;
}
#modal-aircon .aircon-modal-title {
  font-size: 22px;
  line-height: 1.2;
  font-weight: 900;
}
#modal-aircon .aircon-modal-sub {
  margin-top: 3px;
  color: rgba(255,255,255,.76);
  font-size: 14px;
  font-weight: 800;
}
#modal-aircon .aircon-modal-close {
  width: 44px;
  height: 44px;
  border: 1px solid rgba(255,255,255,.34);
  border-radius: 10px;
  background: rgba(255,255,255,.12);
  color: #fff;
  cursor: pointer;
  font-size: 30px;
  line-height: 1;
  display: grid;
  place-items: center;
}
#modal-aircon .aircon-modal-close:hover {
  background: #fff;
  color: #0e2f76;
}
#modal-aircon .aircon-modal-body {
  max-height: calc(min(96vh, 880px) - 78px);
  overflow: auto;
  padding: 22px;
  background: #f5feff;
}
#modal-aircon .aircon-form-card {
  width: 100%;
  display: grid;
  gap: 16px;
  background: #fff;
  border: 1px solid #d8e2f0;
  border-radius: 14px;
  padding: 22px;
  box-shadow: 0 12px 26px rgba(14,47,118,.08);
}
#modal-aircon .aircon-field { display: grid; gap: 7px }
#modal-aircon .aircon-label { color: #0e2f76; font-size: 15px; font-weight: 900 }
#modal-aircon .aircon-label .req { color: #dc2626 }
#modal-aircon .aircon-input,
#modal-aircon .aircon-note {
  width: 100%;
  min-height: 52px;
  border: 1px solid #d8e2f0;
  border-radius: 10px;
  padding: 12px 15px;
  outline: none;
  color: #0e2f76;
  background: #fff;
  font-size: 15px;
  line-height: 1.4;
  font-weight: 700;
}
#modal-aircon .aircon-note { min-height: 98px; resize: vertical }
#modal-aircon .aircon-input:focus,
#modal-aircon .aircon-note:focus {
  border-color: #4870c8;
  box-shadow: 0 0 0 3px rgba(170,192,225,.35);
}
#modal-aircon .aircon-upload-stack { display: grid; gap: 10px }
#modal-aircon .aircon-file {
  position: absolute;
  inline-size: 1px;
  block-size: 1px;
  opacity: 0;
  pointer-events: none;
}
#modal-aircon .aircon-upload {
  min-height: 88px;
  display: grid;
  place-items: center;
  gap: 4px;
  border: 1px dashed #8fb3ec;
  border-radius: 10px;
  background: #f8fbff;
  color: #2455dc;
  cursor: pointer;
  font-size: 15px;
  font-weight: 900;
  text-align: center;
  transition: border-color .18s ease, background .18s ease, transform .18s ease;
}
#modal-aircon .aircon-upload:hover {
  border-color: #2455dc;
  background: #edf4ff;
  transform: translateY(-1px);
}
#modal-aircon .aircon-upload svg {
  width: 23px;
  height: 23px;
  stroke: currentColor;
  fill: none;
  stroke-width: 2;
}
#modal-aircon .aircon-upload small { color: #6b7d9b; font-size: 13px; font-weight: 700 }
#modal-aircon .aircon-status-group {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}
#modal-aircon .aircon-status-option {
  min-height: 54px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border: 1px solid #d8e2f0;
  border-radius: 10px;
  background: #fff;
  color: #0e2f76;
  cursor: pointer;
  font-size: 15px;
  font-weight: 900;
}
#modal-aircon .aircon-status-option input { accent-color: #16a34a }
#modal-aircon .aircon-save {
  width: 100%;
  min-height: 54px;
  border: 0;
  border-radius: 10px;
  background: #2455dc;
  color: #fff;
  cursor: pointer;
  font-size: 16px;
  font-weight: 900;
  box-shadow: 0 10px 18px rgba(36,85,220,.2);
}
#modal-aircon .aircon-save:hover { background: #173b8e }
#modal-aircon .aircon-cancel {
  width: 100%;
  min-height: 54px;
  border: 1px solid #d8e2f0;
  border-radius: 10px;
  background: #fff;
  color: #0e2f76;
  cursor: pointer;
  font-size: 16px;
  font-weight: 900;
}
#modal-aircon .aircon-form-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}
#modal-aircon .aircon-form-error {
  padding: 10px 12px;
  border: 1px solid #fecaca;
  border-radius: 10px;
  background: #fee2e2;
  color: #991b1b;
  font-size: 14px;
  font-weight: 800;
}
@media (min-width: 820px) {
  #modal-aircon .aircon-form-card {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    align-items: start;
  }
  #modal-aircon .aircon-form-error,
  #modal-aircon .aircon-form-actions {
    grid-column: 1 / -1;
  }
  #modal-aircon .aircon-note {
    min-height: 116px;
  }
}
@media (max-width: 768px) {
  #panel-aircons .aircon-history-filter {
    grid-template-columns: 1fr;
  }
  #panel-aircons .aircon-panel-actions {
    width: 100%;
    align-items: stretch;
    flex-direction: column;
  }
  #panel-aircons .aircon-add-btn {
    width: 100%;
  }
  #modal-aircon { padding: 10px !important }
  #modal-aircon .aircon-modal {
    width: 100%;
    max-height: calc(100vh - 20px);
  }
  #modal-aircon .aircon-modal-body {
    max-height: calc(100vh - 98px);
    padding: 12px;
  }
  #modal-aircon .aircon-form-card {
    grid-template-columns: 1fr;
    padding: 14px;
  }
  #modal-aircon .aircon-status-group,
  #modal-aircon .aircon-form-actions {
    grid-template-columns: 1fr;
  }
}
/* === AIRCON WASH POPUP END === */
/* === AIRCON WASH MENU END === */
/* ============================================================
   CERTIFICATIONS
   ============================================================ */
#panel-certifications .cert-head { position: relative }
#panel-certifications .cert-head > * { position: relative; z-index: 1 }
.cert-kicker { background: rgba(255,255,255,.14); color: rgba(255,255,255,.95); border-color: rgba(255,255,255,.18) }
.cert-kicker::before { background: #fff }
.cert-title { font-size: 28px; font-weight: 900; color: #fff }
.cert-sub { color: rgba(255,255,255,.78) }
@media (max-width: 560px) { #panel-certifications .cert-grid { grid-template-columns: 1fr } }
.cert-card {
  min-height: 142px; border-radius: 16px; border: 1px solid #d8e7fb;
  background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
  box-shadow: 0 10px 24px rgba(14,47,118,.08);
  position: relative; text-align: left; cursor: pointer; overflow: hidden;
  transition: transform .2s, box-shadow .2s, border-color .2s;
}
.cert-card::before { content: ''; position: absolute; inset: 0 auto 0 0; width: 4px; background: linear-gradient(180deg, #1d4ed8, #60a5fa) }
.cert-card:hover { transform: translateY(-4px); border-color: #93c5fd; box-shadow: 0 16px 34px rgba(14,47,118,.16) }
.cert-card-top { min-height: 92px; padding: 18px 18px 14px 22px; display: flex; gap: 12px; align-items: flex-start; border-bottom: 0 }
.cert-icon {
  width: 44px; height: 44px; border-radius: 12px; background: #eaf3ff;
  border: 1px solid #cfe2ff; color: #174ea6; display: grid; place-items: center; font-size: 0;
}
.cert-icon::before { content: ''; width: 22px; height: 22px; background: currentColor; display: block; -webkit-mask: var(--cert-medal-mask, none) center / contain no-repeat; mask: var(--cert-medal-mask, none) center / contain no-repeat }
.cert-info { flex: 1; min-width: 0 }
.cert-name { color: #0e2f76; font-size: 16px; font-weight: 900; white-space: normal; line-height: 1.25 }
.cert-count-text { font-size: 12px; color: #64789d; font-weight: 700; margin-top: 3px }
.cert-count { margin-left: auto; font-size: 34px; font-weight: 900; color: #174ea6; line-height: 1 }
.cert-people {
  min-height: 48px; padding: 11px 18px 14px 22px; display: flex; gap: 6px; flex-wrap: wrap;
  border-top: 1px solid #e5eefb; background: #f7fbff;
}
.cert-people span { background: #eaf3ff; color: #174ea6; border: 1px solid #cfe2ff; border-radius: 8px; padding: 4px 8px; font-size: 10px; font-weight: 900 }

/* ============================================================
   MODALS / FORMS / PROFILE BASE
   ============================================================ */
.overlay, .cal-popup-bg {
  display: none; position: fixed; inset: 0; z-index: 500;
  background: rgba(14,47,118,.55); backdrop-filter: blur(8px);
  align-items: center; justify-content: center;
}
.overlay.open, .cal-popup-bg.open { display: flex }
.pmodal, .cert-modal, .borrow-modal, .borrow-form-modal, .cal-popup {
  background: var(--white); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);
  max-width: calc(100vw - 32px); max-height: 92vh; overflow: auto; font-family: var(--font-serif-thai);
}
.pmodal-strip, .cal-popup-strip { height: 4px; background: var(--navy-900) }
.modal-header {
  display: flex; justify-content: space-between; align-items: flex-start;
  gap: 16px; background: var(--navy-25); border-bottom: 1px solid var(--line);
}
.modal-title { font-weight: 700; color: var(--navy-900) }
.modal-subtitle { color: var(--muted); font-weight: 500; margin-top: 2px }
.modal-close, .cert-close, .borrow-x, .cal-popup-close, .tcal-close {
  border: 0; background: var(--white); color: var(--text); border-radius: 999px;
  cursor: pointer; font-weight: 600; display: grid; place-items: center; transition: all .18s;
}
.modal-close { border: 1px solid var(--line) }
.modal-close:hover, .cert-close:hover, .cal-popup-close:hover { background: var(--navy-900); color: #fff; border-color: var(--navy-900) }
.finput { width: 100% }
.flabel { display: block; font-weight: 700; color: var(--navy-700); letter-spacing: .08em; text-transform: uppercase }
.fgrid, .sched-grid, .resume-fields, .borrow-form-grid { display: grid; grid-template-columns: 1fr 1fr }
.fcol-full, .sched-full { grid-column: 1 / -1 }
.factions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 22px; padding-top: 18px; border-top: 1px solid var(--line) }
.ferr { background: var(--danger-bg); color: var(--danger-text); border: 1px solid #fca5a5; border-radius: var(--radius-sm); padding: 11px 15px; font-weight: 600; margin-bottom: 14px }
.finfo-box, .head-info-box { background: var(--navy-50); border: 1px solid var(--navy-100); color: var(--navy-700); border-radius: var(--radius-sm); padding: 11px 15px; font-size: 13px; font-weight: 500 }
.resume-top { display: flex; background: var(--navy-25); border-bottom: 1px solid var(--line) }
.photo-col { display: flex; flex-direction: column; align-items: center; gap: 8px }
.photo-box { border: 2px dashed var(--navy-400); border-radius: var(--radius-md); background: var(--white); display: grid; place-items: center; position: relative; overflow: hidden; cursor: pointer }
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
.section-h { font-weight: 700; color: var(--navy-700); padding-bottom: 8px; border-bottom: 1px solid var(--line); text-transform: uppercase; letter-spacing: .06em }
.skill-grid, .sw-grid { display: grid; grid-template-columns: repeat(3, 1fr) }
.skill-check { display: flex; align-items: center; gap: 8px; border: 1px solid var(--line); border-radius: var(--radius-sm); font-weight: 500; cursor: pointer; transition: all .18s }
.skill-check:hover { border-color: var(--navy-400); background: var(--navy-25) }
.skill-check.checked { border-color: var(--navy-900); background: var(--navy-900); color: #fff }
.comp-grid { display: grid; grid-template-columns: repeat(2, 1fr) }
.comp-card { border: 1px solid var(--line); border-radius: var(--radius-md); background: var(--white) }
.comp-head { display: flex; gap: 8px; align-items: center; margin-bottom: 8px }
.comp-label { font-weight: 700; color: var(--navy-900) }
.comp-code { margin-left: auto; background: var(--bg-soft); border-radius: 5px; padding: 2px 7px; font-size: 10px; color: var(--muted); font-weight: 700 }
.comp-select { width: 100%; border: 1px solid var(--line); border-radius: var(--radius-sm); padding: 9px }
.comp-select.lv-basic { background: var(--warn-bg); color: var(--warn-text); border-color: #fde68a }
.comp-select.lv-skill { background: var(--info-bg); color: var(--info-text); border-color: var(--navy-200) }
.comp-select.lv-expert { background: var(--success-bg); color: var(--success-text); border-color: #86efac; font-weight: 700 }
.sw-custom-row { display: flex; gap: 8px; margin-top: 10px }
.sw-custom-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px }
.sw-tag { background: var(--navy-900); color: #fff; border-radius: 999px; padding: 5px 13px; font-size: 12px; font-weight: 600 }
.sw-tag .x { cursor: pointer; margin-left: 6px }
.btn-add-lic { width: 100%; padding: 12px; margin-top: 10px }
.btn-other { padding: 10px 17px }
.lic-list { display: grid; gap: 10px }
.lic-item { background: var(--navy-25); border: 1px solid var(--line); border-radius: var(--radius-md); padding: 14px }
.lic-item-head { display: flex; align-items: center; gap: 8px; margin-bottom: 10px }
.lic-num { background: var(--navy-900); color: #fff; border-radius: 999px; padding: 3px 11px; font-size: 12px; font-weight: 700 }
.lic-del { margin-left: auto; background: var(--danger); color: #fff; border: 0; border-radius: 6px; padding: 5px 12px; font-weight: 600; font-size: 12px; cursor: pointer }
.lic-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 9px }
.lic-file-link { display: inline-block; margin-top: 8px; background: var(--success-bg); color: var(--success-text); border: 1px solid #86efac; border-radius: 6px; padding: 5px 12px; font-size: 12px; font-weight: 600; text-decoration: none }
.profile-v2 { padding: 0; overflow: hidden }
.profile-v2-layout { display: grid }
.profile-v2-left { display: flex; flex-direction: column; position: relative }
.profile-v2-left::after { content: ''; position: absolute; top: -100px; right: -100px; width: 280px; height: 280px; background: radial-gradient(circle, rgba(170,192,225,.18) 0%, transparent 70%); border-radius: 50%; pointer-events: none }
.profile-v2-left > * { position: relative; z-index: 1 }
.profile-v2-photo { border-radius: 50%; border: 4px solid rgba(255,255,255,.4); overflow: hidden; background: rgba(255,255,255,.15); display: grid; place-items: center; font-weight: 700; color: #fff; margin-bottom: 18px; flex: 0 0 auto }
.profile-v2-photo img { width: 100%; height: 100%; object-fit: cover; display: block }
.profile-v2-name { font-weight: 700; color: #fff; text-align: center }
.profile-v2-nameeng { color: rgba(255,255,255,.78); font-weight: 600; text-align: center }
.profile-v2-status { display: inline-flex; align-items: center; gap: 7px; border-radius: 999px; font-weight: 700 }
.pv2-status-active { background: var(--success-bg); color: var(--success-text) }
.pv2-status-leave { background: var(--danger-bg); color: var(--danger-text) }
.pv2-st-dot { width: 8px; height: 8px; border-radius: 50% }
.pv2-dot-active { background: var(--success) }
.pv2-dot-leave { background: var(--danger) }
.profile-v2-rolecard, .profile-v2-infolist { width: 100%; overflow: hidden; border: 1px solid rgba(255,255,255,.18) }
.pv2-rolerow, .pv2-inforow { background: rgba(255,255,255,.92); border-bottom: 1px solid #E8EFF8 }
.pv2-rolerow:last-child, .pv2-inforow:last-child { border-bottom: 0 }
.pv2-rolekey, .pv2-infokey { font-weight: 700; white-space: nowrap }
.pv2-roleval, .pv2-infoval { flex: 1; overflow: hidden; text-overflow: ellipsis }
.profile-v2-infolist { margin-top: 10px; display: flex; flex-direction: column; gap: 0 }
.pv2-inforow { justify-content: space-between; background: #fff; border-radius: 0; margin: 0 }
.pv2-inforow:nth-child(even) { background: #F5FEFF }
.profile-v2-right { overflow: auto }
.pv2-section-label { font-weight: 700; text-transform: uppercase }
.pv2-tags { display: flex; flex-wrap: wrap }
.pv2-tag { border: 1px solid var(--navy-100) }
.pv2-tag-sw { color: var(--navy-800) }
.pv2-comp-item { justify-content: space-between; align-items: center; border: 1px solid var(--line); gap: 10px }
.pv2-comp-val { white-space: nowrap }
.cv-none { background: var(--bg-soft); color: var(--muted) }
.cv-basic { background: var(--warn-bg); color: var(--warn-text) }
.cv-skill { background: var(--info-bg); color: var(--info-text) }
.cv-expert { background: var(--success-bg); color: var(--success-text) }
.pv2-lic-item { border: 1px solid var(--line); margin-bottom: 8px }
.pv2-lic-name { font-weight: 700; color: var(--navy-900) }
.pv2-lic-meta { color: var(--muted); font-weight: 500; margin-top: 4px }
.pv2-close-btn {
  position: absolute; border: 1px solid rgba(255,255,255,.25);
  background: rgba(255,255,255,.12); color: #fff; border-radius: 50%;
  cursor: pointer; font-weight: 600; display: grid; place-items: center; transition: background .18s; z-index: 2;
}
.pv2-close-btn:hover { background: rgba(255,255,255,.24) }
.dtab-panel { display: none }
.dtab-panel.active { display: block }
.pinfo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px }
.pinfo-card { background: var(--navy-25); border: 1px solid var(--line); border-radius: var(--radius-sm); padding: 13px }
.pinfo-label { font-size: 11px; font-weight: 700; color: var(--muted); letter-spacing: .06em; text-transform: uppercase }
.pinfo-val { font-weight: 600; color: var(--navy-900); margin-top: 3px }

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
  position: relative; aspect-ratio: 1;  border: 1px solid var(--line); border-radius: 6px; background: var(--white);
  display: grid; place-items: center; cursor: pointer; transition: all .15s;
}
.tl-cell:hover   { border-color: var(--navy-400); background: var(--navy-25) }
.tl-other        { opacity: .25; pointer-events: none }
.tl-today        { border-color: var(--navy-900); border-width: 2px }
.tl-busy         { background: repeating-linear-gradient(45deg,#fee2e2,#fee2e2 4px,#fecaca 4px,#fecaca 8px); border-color: #fca5a5; pointer-events: none }
.tl-sel-s, .tl-sel-e { background: var(--navy-900) !important; color: #fff; border-color: var(--navy-900) !important }
.tl-in-range     { background: var(--navy-100) !important; border-color: var(--navy-400) !important }
.tl-d {font-weight: 600 }
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
border-radius: var(--radius-lg);
  overflow: hidden; background: var(--white); border: 1px solid var(--line); box-shadow: var(--shadow-lg); position: relative;
}
.cert-close, .borrow-x { position: absolute; top: 20px; right: 20px;index: 2 }
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
.borrow-modal {osition: relative }
.borrow-head  { display: flex; justify-content: space-between; gap: 18px; margin-bottom: 16px }
.borrow-title { font-size: 22px; font-weight: 700; color: var(--navy-900) }
.borrow-sub   { color: var(--muted); font-weight: 500; margin-top: 4px }
.borrow-tools { display: flex; gap: 10px }
.borrow-input { height: 40px;}
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
.borrow-form-modal {osition: relative }
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
  background: var(--navy-900); color: #fff;  display: flex; gap: 16px; align-items: center; flex-wrap: wrap; position: relative; overflow: hidden;
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
.tcal-title   {font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis }
.tcal-stat    { display: inline-flex; margin-top: 5px; background: rgba(255,255,255,.18); border-radius: 999px; padding: 4px 13px; font-size: 12px; font-weight: 600 }
.tcal-close   { background: rgba(255,255,255,.14); color: #fff; border: 1px solid rgba(255,255,255,.22) }
.tcal-close:hover { background: rgba(255,255,255,.26) }
.tcal-body    { flex: 1; overflow: auto;}
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
border: 1px solid var(--line); border-radius: var(--radius-sm);
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
.cal-popup-strip { height: 4px; background: var(--navy-900) }
.cal-popup-head { display: flex; gap: 8px; align-items: center;border-bottom: 1px solid var(--line) }
.cal-popup-date  { flex: 1; background: var(--navy-50); color: var(--navy-700); border-radius: var(--radius-sm); padding: 7px 12px; text-align: center; font-weight: 700 }
.cal-popup-count { background: var(--navy-900); color: #fff; border-radius: 999px; padding: 4px 11px; font-size: 12px; font-weight: 600 }
.cal-popup-close {order: 1px solid var(--line) }.cal-ev-card { border: 1px solid var(--line); border-radius: var(--radius-md);margin-bottom: 9px; position: relative }
.cal-ev-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--navy-700); border-radius: var(--radius-md) 0 0 var(--radius-md) }
.cal-ev-top  { display: flex; justify-content: space-between; gap: 8px }
.cal-so      { background: var(--navy-50); color: var(--navy-700); border-radius: 6px; padding: 3px 8px; font-size: 10px; font-weight: 700 }
.cal-ev-cust { font-weight: 700; color: var(--navy-900) }
.cal-ev-job  {color: var(--muted); font-weight: 500; margin: 3px 0 8px }
.cal-ev-meta { display: grid; grid-template-columns: auto 1fr; gap: 4px 12px;}
.cal-ev-ml   { color: var(--muted); font-weight: 600 }
.cal-ev-mv   { font-weight: 500; color: var(--text) }

/* Ripple */
.sb-ripple { position: absolute; width: 10px; height: 10px; border-radius: 999px; background: rgba(255,255,255,.45); transform: translate(-50%, -50%) scale(1); animation: sbRipple .55s ease-out forwards; pointer-events: none; z-index: 2 }

/* ============================================================
   SCHEDULE FORM MAP PICKER
   ============================================================ */
#modal-sched .sched-grid,
#modal-edit-sched .sched-grid {
  grid-template-columns: repeat(6, minmax(0, 1fr));
  gap: 18px 20px !important;
  align-items: start;
}
#modal-sched .modal-body,
#modal-edit-sched .modal-body {
  padding: 28px 34px !important;
}
#modal-sched .frow,
#modal-edit-sched .frow {
  grid-column: span 3;
  margin-bottom: 0 !important;
  min-width: 0;
}
#modal-sched .sched-third,
#modal-edit-sched .sched-third {
  grid-column: span 2;
}
#modal-sched .sched-full,
#modal-edit-sched .sched-full {
  grid-column: 1 / -1;
}
#modal-sched .finput,
#modal-edit-sched .finput {
  height: 48px !important;
}
#modal-sched textarea.finput,
#modal-edit-sched textarea.finput {
  height: auto !important;
}
.sched-form-section {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 10px 0 0;
  color: var(--navy-900);
  font-size: 14px;
  font-weight: 900;
}
.sched-form-section:first-child {
  margin-top: 0;
}
.sched-form-section::after {
  content: '';
  height: 1px;
  flex: 1;
  background: var(--line);
}
.sched-field-compact {
  max-width: 100%;
}
.sched-map-picker {
  display: grid;
  gap: 10px;
}
.sched-map-toolbar {
  display: grid;
  grid-template-columns: minmax(260px, 1fr) auto;
  gap: 10px;
  align-items: stretch;
}
.sched-map-btn {
  white-space: nowrap;
}
.sched-map-hint {
  border: 1px solid var(--line);
  border-radius: var(--radius-sm);
  background: var(--navy-25);
  color: var(--navy-700);
  padding: 9px 12px;
  font-size: 12px;
  font-weight: 700;
}
.sched-map {
  height: 280px;
  border: 1px solid var(--line);
  border-radius: var(--radius-md);
  overflow: hidden;
  background: #eef6ff;
  position: relative;
  z-index: 1;
}
.sched-map iframe {
  width: 100%;
  height: 100%;
  border: 0;
  display: block;
}
.sched-map-coord-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  z-index: 3;
  max-width: calc(100% - 24px);
  padding: 8px 12px;
  border: 1px solid rgba(14,47,118,.18);
  border-radius: 999px;
  background: rgba(255,255,255,.94);
  color: var(--navy-900);
  font-size: 12px;
  font-weight: 900;
  box-shadow: 0 8px 20px rgba(14,47,118,.16);
  pointer-events: none;
}
.sched-map-fallback {
  height: 100%;
  display: grid;
  place-items: center;
  color: var(--muted);
  font-weight: 700;
  text-align: center;
  padding: 18px;
}
@media (max-width: 900px) {
  .sched-map-toolbar {
    grid-template-columns: 1fr;
  }
  .sched-map {
    height: 240px;
  }
}

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
  .sched-event { font-size: 11px }  .profile-v2-left   { padding-bottom: 22px }
  .profile-v2-right  { max-height: none }
  .cert-grid { grid-template-columns: repeat(2, 1fr) }
}@media (max-width: 768px) {
  .sidebar { transform: translateX(-100%); transition: .24s }
  .sidebar.open { transform: translateX(0) }
  .main { margin-left: 0; padding: 70px 16px 16px }
  .sb-toggle { display: grid; position: fixed; top: 14px; left: 14px; width: 44px; height: 44px; border: 1px solid var(--line); border-radius: var(--radius-sm); background: var(--white); z-index: 120; box-shadow: var(--shadow-md); place-items: center }
  .panel-header, .cert-head, .roster-head, .sched-board-top,
  .borrow-head, .borrow-tools, .sched-controls { flex-direction: column; align-items: stretch }
  .search-inp { width: 100%; min-width: 0 }
  .fgrid, .sched-grid, .resume-fields, .borrow-form-grid,
  .skill-grid, .sw-grid, .comp-grid, .pinfo-grid { grid-template-columns: 1fr }
  #modal-sched .frow,
  #modal-edit-sched .frow,
  #modal-sched .sched-third,
  #modal-edit-sched .sched-third {
    grid-column: 1 / -1;
  }
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

}.pv2-sections {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 0;
}.pv2-section {
  min-width: 0;
}.pv2-comp-grid {
  display: grid;
}.pv2-comp-item {
  min-width: 0;
}.pv2-comp-key {
  min-width: 0;
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
/* ============================================================
   TECHNICIAN ID CARD - Thai ID inspired redesign
   ============================================================ */
#roster-grid {
  grid-template-columns: repeat(2, minmax(320px, 1fr)) !important;
  gap: 18px !important;
  padding: 18px !important;
  background: #ffffff !important;
  border-radius: 10px !important;
}

#roster-grid .emp-card {
  padding: 0 !important;
  box-shadow: none !important;
  display: grid !important;
  position: relative !important;
  color: #0e2f76 !important;
}

#roster-grid .emp-card::before {
  position: absolute;
  line-height: 1;
  font-weight: 900;
  letter-spacing: 0;
  pointer-events: none;
}

#roster-grid .emp-card:hover {
  transform: translateY(-3px) !important;
  border-color: #d1e5ff !important;
  box-shadow: 0 16px 32px rgba(0,0,0,.24) !important;
}

#roster-grid .emp-card-stripe,
#roster-grid .emp-card-body,
#roster-grid .emp-card-top,
#roster-grid .emp-avatar-new,
#roster-grid .emp-card-info,
#roster-grid .emp-meta-row,
#roster-grid .emp-status-dot {
  all: unset;
}

#roster-grid .emp-id-header {
  min-width: 0;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}

#roster-grid .emp-id-brand {
  min-width: 0;
  display: inline-flex;
  align-items: center;
  font-size: 11px;
  font-weight: 900;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

#roster-grid .emp-id-mark {
  display: inline-grid;
  place-items: center;
  color: #fff;
  font-size: 10px;
  font-weight: 900;
  flex: 0 0 auto;
}

#roster-grid .emp-id-no {
  color: #ffe500;
  font-weight: 900;
  white-space: nowrap;
}

#roster-grid .emp-id-body {
  position: relative;
  z-index: 1;
  display: grid;
  min-width: 0;
}

#roster-grid .emp-id-photo {
  align-self: start;
  color: #0d4d9e;
  display: grid;
  place-items: center;
  overflow: hidden;
  position: relative;
}

#roster-grid .emp-id-photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

#roster-grid .emp-id-photo .initials {
  color: #0c397f;
  font-weight: 900;
  line-height: 1;
}

#roster-grid .emp-id-photo small {
  position: absolute;
  left: 0;
  right: 0;
  text-align: center;
  color: #0d4d9e;
  font-weight: 900;
}

#roster-grid .emp-id-photo img ~ small {
  display: none;
}

#roster-grid .emp-id-info {
  min-width: 0;
}

#roster-grid .emp-id-label {
  color: #667795;
  font-weight: 800;
  line-height: 1.1;
}

#roster-grid .emp-id-name {
  color: #0d347a;
  font-weight: 900;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  border-bottom: 1px solid #e5ebf4;
}

#roster-grid .emp-id-eng {
  color: #536b95;
  font-size: 11px;
  font-weight: 800;
  line-height: 1.2;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-top: 4px;
}

#roster-grid .emp-id-row {
  display: grid;
  gap: 8px;
  align-items: baseline;
  border-bottom: 1px solid #e5ebf4;
}

#roster-grid .emp-id-row span {
  font-weight: 800;
}

#roster-grid .emp-id-row strong {
  min-width: 0;
  font-weight: 900;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

#roster-grid .emp-id-skill-strip {
  position: relative;
  z-index: 1;
  display: flex;
  border-top: 1px solid #dfe7f2;
  overflow: hidden;
}

#roster-grid .emp-skill-tag {
  width: auto !important;
  border-radius: 5px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  background: #fff !important;
  border: 1px solid #9fc8ff !important;
  color: #0d4d9e !important;
  font-weight: 900 !important;
  white-space: nowrap !important;
}

#roster-grid .emp-skill-tag:first-child {
  background: #0c347f !important;
  border-color: #0c347f !important;
  color: #fff !important;
}

#roster-grid .emp-id-footer {
  position: relative;
  z-index: 1;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  color: #fff;
}

#roster-grid .emp-id-status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: #fff;
  font-weight: 900;
  white-space: nowrap;
}

#roster-grid .emp-id-status i {
  width: 7px;
  height: 7px;
  border-radius: 999px;
  background: #20d363;
  flex: 0 0 7px;
}

#roster-grid .emp-id-status.is-leave i {
  background: #ef4444;
}

#roster-grid .emp-id-role {
  display: inline-flex;
  align-items: center;
  border-radius: 4px;
  background: #ffe22e;
  font-weight: 900;
  white-space: nowrap;
}

@media (max-width: 900px) {
  #roster-grid {
    grid-template-columns: 1fr !important;
  }
}

/* ID card v2 - more like an official identity card */
#roster-grid .emp-card {
  background:
    radial-gradient(circle at 88% 22%, rgba(25,77,170,.08), transparent 26%),
    linear-gradient(180deg, #ffffff 0%, #f7fbff 100%) !important;
}

#roster-grid .emp-card::before {
  content: '3E';
  right: 16px;
  bottom: 56px;
  font-size: 46px;
  color: #e6edf8;
  opacity: .9;
}

#roster-grid .emp-id-header {
  height: 48px;
  background: linear-gradient(90deg, #123d91 0%, #1857c7 100%);
  border-bottom: 3px solid #c9dcff;
}

#roster-grid .emp-id-brand {
  gap: 10px;
}

#roster-grid .emp-id-mark {
  width: 28px;
  height: 28px;
  border-radius: 7px;
  background: rgba(255,255,255,.22);
}

#roster-grid .emp-id-brand-text {
  display: grid;
  gap: 1px;
  min-width: 0;
}

#roster-grid .emp-id-brand-text b {
  color: #fff;
  font-weight: 900;
  line-height: 1;
  white-space: nowrap;
}

#roster-grid .emp-id-brand-text small {
  color: rgba(255,255,255,.78);
  font-weight: 900;
  letter-spacing: .08em;
  line-height: 1;
}

#roster-grid .emp-id-no {
  display: inline-flex;
  align-items: center;
  border-radius: 5px;
  background: rgba(255,229,0,.16);
  border: 1px solid rgba(255,229,0,.42);
}

#roster-grid .emp-id-photo {
  border: 1px solid #8dbdff;
  background: linear-gradient(180deg, #eef7ff, #dceeff);
  box-shadow: inset 0 0 0 3px #f7fbff;
}

#roster-grid .emp-id-photo small {
  bottom: 10px;
}

#roster-grid .emp-id-number-row {
  grid-template-columns: 82px 1fr;
  padding-top: 0;
}

#roster-grid .emp-id-number-row strong {
  font-family: Consolas, 'SF Mono', monospace;
  letter-spacing: .04em;
}

#roster-grid .emp-id-label {
  margin-top: 2px;
}

#roster-grid .emp-id-name {
  padding-bottom: 6px;
}

#roster-grid .emp-id-row span {
  color: #6d7d98;
}

#roster-grid .emp-id-row strong {
  color: #123d91;
}

#roster-grid .emp-id-skill-strip {
  background: #f2f7ff;
  border-top-color: #d7e6fb;
}

#roster-grid .emp-id-footer {
  background: #123d91;
}

/* ID card v3 - larger readable typography */
#roster-grid .emp-card {
  min-height: 286px !important;
}

#roster-grid .emp-id-brand-text b {
  font-size: 17px !important;
}

#roster-grid .emp-id-brand-text small {
  font-size: 11px !important;
}

#roster-grid .emp-id-no {
  font-size: 14px !important;
  min-height: 26px !important;
  padding: 3px 10px !important;
}

#roster-grid .emp-id-label,
#roster-grid .emp-id-row span {
  font-size: 13px !important;
}

#roster-grid .emp-id-name {
  font-size: 20px !important;
  line-height: 1.25 !important;
}

#roster-grid .emp-id-row strong {
  font-size: 16px !important;
}

#roster-grid .emp-id-number-row strong {
  font-size: 17px !important;
}

#roster-grid .emp-skill-tag {
  min-height: 28px !important;
  padding: 5px 14px !important;
  font-size: 14px !important;
}

#roster-grid .emp-id-status,
#roster-grid .emp-id-role {
  font-size: 13px !important;
}

#roster-grid .emp-id-role {
  min-height: 22px !important;
  padding: 4px 14px !important;
  border: 1px solid rgba(255,255,255,.85) !important;
  box-shadow: 0 2px 6px rgba(0,0,0,.18) !important;
  letter-spacing: 0 !important;
  color: #111827 !important;
  text-shadow: none !important;
}

#roster-grid .emp-card.is-head .emp-id-role {
  border-color: #fde047 !important;
}

#roster-grid .emp-card:not(.is-head) .emp-id-role {
  border-color: #7dd3fc !important;
}

#roster-grid .emp-id-photo .initials {
  font-size: 30px !important;
}

#roster-grid .emp-id-photo small {
  font-size: 10px !important;
}

/* ============================================================
   MODAL SIZE BOOST - make every popup roomier
   ============================================================ */
.overlay,
.cal-popup-bg {
  padding: 28px !important;
}

.pmodal {
  width: min(980px, calc(100vw - 56px)) !important;
  max-height: 94vh !important;
}

.pmodal-wide {
  width: min(1240px, calc(100vw - 56px)) !important;
}

.pmodal-sm {
  width: min(720px, calc(100vw - 56px)) !important;
}

.profile-v2 {
  max-height: 94vh !important;
}

.cert-modal {
  width: min(1040px, calc(100vw - 56px)) !important;
}

.borrow-modal {
  width: min(1280px, calc(100vw - 56px)) !important;
  padding: 34px !important;
}

.borrow-form-modal {
  width: min(860px, calc(100vw - 56px)) !important;
  padding: 34px !important;
}

.cal-popup {
  width: min(720px, calc(100vw - 56px)) !important;
}

.modal-header {
  padding: 26px 34px 20px !important;
}

.modal-title {
  font-size: 23px !important;
}

.modal-subtitle {
  font-size: 15px !important;
}

.modal-close,
.cert-close,
.borrow-x,
.cal-popup-close,
.tcal-close {
  width: 42px !important;
  height: 42px !important;
  font-size: 20px !important;
}

.modal-body {
  padding: 30px 34px !important;
}

.resume-top {
  padding: 30px 34px !important;
  gap: 34px !important;
}

.resume-fields,
.fgrid,
.sched-grid,
.borrow-form-grid {
  gap: 20px !important;
}

.frow {
  margin-bottom: 18px !important;
}

.flabel {
  font-size: 13px !important;
  margin-bottom: 8px !important;
}

.finput,
.sched-select,
.borrow-input {
  min-height: 46px !important;
  padding: 13px 16px !important;
  font-size: 16px !important;
}

textarea.finput {
  min-height: 110px !important;
}

.btn {
  min-height: 44px !important;
  padding: 11px 22px !important;
  font-size: 16px !important;
}

.btn-sm {
  min-height: 34px !important;
  padding: 7px 14px !important;
  font-size: 13px !important;
}

.section-h {
  font-size: 16px !important;
  margin: 26px 0 14px !important;
}

.skill-grid,
.sw-grid {
  gap: 10px !important;
}

.skill-check {
  min-height: 46px !important;
  padding: 12px 14px !important;
  font-size: 15px !important;
}

.comp-grid {
  gap: 14px !important;
}

.comp-card {
  padding: 16px !important;
}

.comp-label {
  font-size: 15px !important;
}

.comp-select {
  min-height: 42px !important;
  font-size: 15px !important;
}

.photo-box {
  width: 150px !important;
  height: 184px !important;
}

.profile-v2-photo {
  font-size: 52px !important;
}

.profile-v2-right {
  max-height: 88vh !important;
}

.tl-cell {
  min-height: 52px !important;
}

.tl-d,
.cal-di {
  font-size: 16px !important;
}

.cal-day {
  min-height: 68px !important;
}

.cal-popup-head {
  padding: 18px 24px !important;
}

.cal-popup-date {
  font-size: 17px !important;
}

.cal-popup-inner {
  padding: 24px !important;
}

.cal-ev-card {
  padding: 18px 18px 18px 24px !important;
}

.cal-ev-cust {
  font-size: 17px !important;
}

.cal-ev-job,
.cal-ev-meta {
  font-size: 15px !important;
}

.tcal-header {
  padding: 26px 38px !important;
}

.tcal-title {
  font-size: 28px !important;
}

.tcal-body {
  padding: 32px !important;
}

@media (max-width: 900px) {
  .overlay,
  .cal-popup-bg {
    padding: 12px !important;
  }

  .pmodal,
  .pmodal-wide,
  .pmodal-sm,
  .profile-v2,
  .cert-modal,
  .borrow-modal,
  .borrow-form-modal,
  .cal-popup {
    width: calc(100vw - 24px) !important;
    max-height: 96vh !important;
  }

  .profile-v2-layout {
    grid-template-columns: 1fr !important;
  }

  .modal-header,
  .modal-body,
  .resume-top,
  .profile-v2-right {
    padding-left: 20px !important;
    padding-right: 20px !important;
  }
}

/* PROFILE POPUP CLEANUP - clearer identity layout */
.profile-v2 {
  width: min(1320px, calc(100vw - 76px)) !important;
  border-radius: 18px !important;
  background: #fff !important;
}

.profile-v2-layout {
  grid-template-columns: 370px minmax(0, 1fr) !important;
  min-height: min(760px, calc(100vh - 96px));
}

.profile-v2-left {
  align-items: stretch !important;
  padding: 38px 34px !important;
  background:
    linear-gradient(180deg, rgba(255,255,255,.08) 0%, rgba(255,255,255,0) 42%),
    linear-gradient(180deg, #133a86 0%, #0e2f76 100%) !important;
}

.profile-v2-photo {
  width: 176px !important;
  height: 176px !important;
  margin: 10px auto 22px !important;
  border-width: 5px !important;
  box-shadow: 0 18px 34px rgba(0,0,0,.22);
}

.profile-v2-name {
  max-width: 100%;
  font-size: 30px !important;
  line-height: 1.2 !important;
  margin-bottom: 6px !important;
  overflow-wrap: anywhere;
}

.profile-v2-nameeng {
  font-size: 18px !important;
  line-height: 1.3 !important;
  margin-bottom: 18px !important;
}

.profile-v2-rolecard .profile-v2-nameeng {
  color: #0e2f76 !important;
  font-size: 16px !important;
  line-height: 1.25 !important;
  margin: 0 !important;
  text-align: left !important;
}

.profile-v2-status {
  align-self: center !important;
  min-height: 46px;
  padding: 10px 24px !important;
  font-size: 17px !important;
  margin-bottom: 26px !important;
}

.profile-v2-rolecard,
.profile-v2-infolist {
  border-radius: 12px !important;
  border-color: rgba(255,255,255,.25) !important;
  box-shadow: 0 12px 24px rgba(0,0,0,.12);
}

.profile-v2-rolecard {
  margin-top: 0 !important;
  margin-bottom: 0 !important;
  border-bottom: 0 !important;
  border-radius: 12px 12px 0 0 !important;
  box-shadow: none !important;
}

.profile-v2-infolist {
  margin-top: 0 !important;
  border-top: 0 !important;
  border-radius: 0 0 12px 12px !important;
}

.profile-v2-rolecard + .profile-v2-infolist {
  margin-top: 0 !important;
}

.profile-v2-rolecard .pv2-rolerow:last-child {
  border-bottom: 1px solid #E8EFF8 !important;
}

.profile-v2-rolecard .pv2-rolerow,
.profile-v2-infolist .pv2-inforow,
.profile-v2-infolist .pv2-inforow:nth-child(even) {
  background: #fff !important;
}

.pv2-rolerow,
.pv2-inforow {
  display: grid !important;
  grid-template-columns: 96px minmax(0, 1fr) !important;
  gap: 12px !important;
  align-items: center !important;
  min-height: 54px !important;
  padding: 13px 16px !important;
}

.pv2-rolekey,
.pv2-infokey {
  min-width: 0 !important;
  color: #5b6f95 !important;
  font-size: 13px !important;
  letter-spacing: 0 !important;
  text-transform: none !important;
}

.pv2-roleval,
.pv2-infoval {
  color: #0e2f76 !important;
  font-size: 16px !important;
  font-weight: 900 !important;
  text-align: left !important;
  white-space: nowrap !important;
}

.profile-v2-right {
  padding: 32px !important;
  background: #f8fbff !important;
}

.profile-v2-right > div {
  display: grid !important;
  grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
  gap: 22px !important;
  align-content: start !important;
}

.profile-v2-right .pv2-sections {
  display: contents !important;
}

.pv2-section {
  min-height: 230px !important;
  padding: 26px !important;
  border-radius: 16px !important;
  background: #fff !important;
  border: 1px solid #cfe0f6 !important;
  box-shadow: 0 12px 26px rgba(14,47,118,.07);
}

.pv2-profile-summary,
.pv2-license-section {
  grid-column: 1 / -1 !important;
  min-height: 0 !important;
}

.pv2-section-label {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  color: #0e2f76 !important;
  font-size: 15px !important;
  letter-spacing: .08em !important;
  margin-bottom: 18px !important;
}

.pv2-section-label::before {
  content: '';
  width: 8px;
  height: 8px;
  border-radius: 999px;
  background: #1d4ed8;
  flex: 0 0 auto;
}

.pv2-combined-grid {
  display: grid !important;
  grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
  gap: 14px !important;
}

.pv2-combined-group {
  min-width: 0 !important;
  padding: 16px !important;
  border: 1px solid #d8e7fb !important;
  border-radius: 12px !important;
  background: #f9fbff !important;
}

.pv2-combined-wide {
  grid-column: 1 / -1 !important;
}

.pv2-sub-label {
  margin-bottom: 12px !important;
  color: #5b6f95 !important;
  font-size: 12px !important;
  font-weight: 900 !important;
  letter-spacing: .08em !important;
  text-transform: uppercase !important;
}

.pv2-profile-summary .pv2-tags {
  display: flex !important;
  flex-wrap: wrap !important;
}

.pv2-tags {
  gap: 10px !important;
  align-content: start !important;
}

.pv2-tag {
  min-height: 40px;
  padding: 9px 15px !important;
  border-radius: 9px !important;
  background: #edf4ff !important;
  border-color: #bdd4f5 !important;
  color: #103c8d !important;
  font-size: 16px !important;
  font-weight: 900 !important;
}

.pv2-tag-sw {
  background: #e7f6fb !important;
  border-color: #b8ddea !important;
}

.pv2-comp-grid {
  grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
  gap: 12px !important;
}

.pv2-comp-item {
  display: grid !important;
  grid-template-columns: minmax(0, 1fr) auto !important;
  min-height: 54px !important;
  padding: 13px 14px !important;
  border-radius: 10px !important;
  background: #f9fbff !important;
  border-color: #d6e4f5 !important;
}

.pv2-comp-key {
  color: #0e2f76 !important;
  font-size: 16px !important;
  font-weight: 900 !important;
  line-height: 1.25 !important;
  overflow: visible !important;
  text-overflow: clip !important;
  white-space: normal !important;
}

.pv2-comp-val {
  padding: 6px 12px !important;
  border-radius: 8px !important;
  font-size: 14px !important;
  font-weight: 900 !important;
}

#m-licenses {
  display: grid;
  gap: 12px;
}

.pv2-lic-item {
  margin: 0 !important;
  padding: 16px 18px !important;
  border-radius: 12px !important;
  background: #f9fbff !important;
  border-color: #d6e4f5 !important;
}

.pv2-lic-name {
  font-size: 17px !important;
}

.pv2-lic-meta {
  font-size: 14px !important;
}

.pv2-muted {
  display: block;
  padding: 16px 18px;
  border: 1px dashed #bdd4f5;
  border-radius: 12px;
  background: #f9fbff;
  color: #5b6f95 !important;
  font-size: 16px !important;
  font-weight: 800 !important;
}

.pv2-close-btn {
  top: 18px !important;
  right: 18px !important;
  width: 44px !important;
  height: 44px !important;
  font-size: 24px !important;
}

@media (max-width: 1100px) {
  .profile-v2 {
    width: calc(100vw - 32px) !important;
  }

  .profile-v2-layout {
    grid-template-columns: 1fr !important;
  }

  .profile-v2-right > div {
    grid-template-columns: 1fr !important;
  }
}

@media (max-width: 700px) {
  .profile-v2-left,
  .profile-v2-right {
    padding: 24px 18px !important;
  }

  .pv2-rolerow,
  .pv2-inforow,
  .pv2-combined-grid,
  .pv2-comp-grid {
    grid-template-columns: 1fr !important;
  }

  .pv2-combined-wide {
    grid-column: auto !important;
  }
}

/* ID card name field in one line */
#roster-grid .emp-id-fullname-row {
  text-align: left !important;
}

#roster-grid .emp-id-fullname-row strong {
  text-align: left !important;
}

/* Remove yellow role badge on technician ID card */
#roster-grid .emp-card.is-head .emp-id-role,
#roster-grid .emp-card:not(.is-head) .emp-id-role {
  background: rgba(255,255,255,.18) !important;
  color: #fff !important;
  border: 1px solid rgba(255,255,255,.45) !important;
  box-shadow: none !important;
}

/* Make every skill chip on the ID card use the same white style */
#roster-grid .emp-id-skill-strip .emp-skill-tag,
#roster-grid .emp-id-skill-strip .emp-skill-tag:first-child {
  background: #fff !important;
  border-color: #9fc8ff !important;
  color: #0d4d9e !important;
}

/* Even spacing and borders for technician ID card */
#roster-grid .emp-card {
  border-radius: 12px !important;
  border: 1px solid #7fb6ff !important;
  overflow: hidden !important;
}

#roster-grid .emp-id-header {
  min-height: 58px !important;
  padding: 0 18px !important;
}

#roster-grid .emp-id-body {
  grid-template-columns: 120px minmax(0, 1fr) !important;
  gap: 22px !important;
  padding: 26px 20px 10px !important;
}

#roster-grid .emp-id-photo {
  width: 108px !important;
  height: 134px !important;
  border-radius: 5px !important;
}

#roster-grid .emp-id-info {
  padding-right: 0 !important;
}

#roster-grid .emp-id-row {
  grid-template-columns: 118px minmax(0, 1fr) !important;
  min-height: 49px !important;
  padding: 9px 0 !important;
}

#roster-grid .emp-id-skill-strip {
  min-height: 62px !important;
  padding: 10px 18px !important;
  gap: 8px !important;
  align-items: center !important;
}

#roster-grid .emp-id-skill-strip .emp-skill-tag {
  min-height: 40px !important;
  padding: 8px 18px !important;
  border-radius: 6px !important;
}

#roster-grid .emp-id-footer {
  min-height: 44px !important;
  padding: 0 18px !important;
}

/* Decorative line icons for technician ID card */
#roster-grid .emp-id-row span {
  display: inline-flex !important;
  align-items: center !important;
  gap: 8px !important;
}

#roster-grid .emp-id-row span svg {
  width: 17px !important;
  height: 17px !important;
  flex: 0 0 17px !important;
  stroke: currentColor !important;
  fill: none !important;
  stroke-width: 2.2 !important;
  stroke-linecap: round !important;
  stroke-linejoin: round !important;
  opacity: .85;
}

#roster-grid .emp-id-brand {
  width: 100% !important;
}

#roster-grid .emp-id-skill-strip .emp-skill-tag {
  gap: 8px !important;
}

#roster-grid .emp-id-skill-strip .emp-skill-tag svg {
  width: 16px !important;
  height: 16px !important;
  flex: 0 0 16px !important;
  stroke: currentColor !important;
  fill: none !important;
  stroke-width: 2.2 !important;
  stroke-linecap: round !important;
  stroke-linejoin: round !important;
  opacity: .9;
}

#roster-grid .emp-id-brand::after {
  width: 34px;
  height: 22px;
  margin-left: auto;
  border: 1px solid rgba(255,255,255,.42);
  border-radius: 5px;
  background:
    linear-gradient(90deg, transparent 11px, rgba(255,255,255,.38) 11px, rgba(255,255,255,.38) 12px, transparent 12px),
    linear-gradient(180deg, rgba(255,255,255,.20), rgba(255,255,255,.08));
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.08);
}

/* Remove the marked chip and status bar from technician ID cards */
#roster-grid .emp-id-brand::after {
  content: none !important;
  display: none !important;
}

#roster-grid .emp-id-footer {
  display: none !important;
}

#roster-grid .emp-card {
  grid-template-rows: 58px 1fr auto !important;
}

/* === CODEX SCHEDULE JOB LIST REAL FIX START === */
/* CODEX JOB LIST TABLE + STATUS DROPDOWN */
#panel-schedules .sched-list-card,
.tcal-overlay .sched-list-card {
  margin-top: 20px !important;
  background: #fff !important;
  border: 1px solid #cfe0f6 !important;
  border-radius: 16px !important;
  overflow: hidden !important;
  box-shadow: 0 14px 30px rgba(14,47,118,.08) !important;
}

#panel-schedules .sched-list-head,
.tcal-overlay .sched-list-head {
  min-height: 86px !important;
  display: grid !important;
  grid-template-columns: minmax(0, 1fr) minmax(260px, 280px) !important;
  align-items: center !important;
  gap: 18px !important;
  padding: 20px 24px !important;
  background: #f4fbff !important;
  border-bottom: 1px solid #dbeafe !important;
}

#panel-schedules .sched-list-eyebrow,
.tcal-overlay .sched-list-eyebrow {
  color: #0e4595 !important;
  font-size: 12px !important;
  font-weight: 900 !important;
  letter-spacing: .12em !important;
  text-transform: uppercase !important;
}

#panel-schedules .sched-list-title,
.tcal-overlay .sched-list-title {
  margin-top: 4px !important;
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
  color: #0e2f76 !important;
  font-size: 18px !important;
  font-weight: 900 !important;
}

#panel-schedules .sched-list-count,
.tcal-overlay .sched-list-count {
  min-height: 26px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  padding: 3px 14px !important;
  border-radius: 999px !important;
  background: #eaf2ff !important;
  color: #174ea6 !important;
  border: 1px solid #cfe0f6 !important;
  font-size: 12px !important;
  font-weight: 900 !important;
  white-space: nowrap !important;
}

#panel-schedules .sched-list-search,
.tcal-overlay .sched-list-search {
  width: 100% !important;
  min-width: 0 !important;
  height: 44px !important;
  border-radius: 9px !important;
  background: #fff !important;
}

#panel-schedules .sched-list-wrap,
.tcal-overlay .sched-list-wrap {
  width: 100% !important;
  overflow: auto !important;
}

#panel-schedules .sched-list-table,
.tcal-overlay .sched-list-table {
  width: 100% !important;
  min-width: 1120px !important;
  border-collapse: separate !important;
  border-spacing: 0 !important;
}

#panel-schedules .sched-list-table {
  min-width: 1260px !important;
  table-layout: fixed !important;
}

#panel-schedules .sched-list-table td,
#panel-schedules .sched-list-table td * {
  overflow-wrap: anywhere !important;
}

#panel-schedules .sched-list-table th,
.tcal-overlay .sched-list-table th {
  position: sticky !important;
  top: 0 !important;
  z-index: 2 !important;
  padding: 15px 18px !important;
  background: #0f4593 !important;
  color: #fff !important;
  text-align: left !important;
  font-size: 13px !important;
  font-weight: 900 !important;
  letter-spacing: 0 !important;
  text-transform: none !important;
  white-space: nowrap !important;
}

#panel-schedules .sched-list-table td,
.tcal-overlay .sched-list-table td {
  height: 80px !important;
  padding: 16px 18px !important;
  border-bottom: 1px solid #dfeafa !important;
  color: #0e2f76 !important;
  font-size: 14px !important;
  font-weight: 800 !important;
  vertical-align: middle !important;
  background: #fff !important;
}

#panel-schedules .sched-list-table tbody tr,
.tcal-overlay .sched-list-table tbody tr {
  cursor: pointer !important;
}

#panel-schedules .sched-list-table tbody tr:hover td,
.tcal-overlay .sched-list-table tbody tr:hover td {
  background: #f7fbff !important;
}

#panel-schedules .sched-list-table tbody tr:last-child td,
.tcal-overlay .sched-list-table tbody tr:last-child td {
  border-bottom: 0 !important;
}

#panel-schedules .sched-list-so,
.tcal-overlay .sched-list-so {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  min-height: 26px !important;
  padding: 3px 10px !important;
  border-radius: 6px !important;
  background: #e8eef7 !important;
  color: #0645ad !important;
  font-family: Consolas, "SF Mono", monospace !important;
  font-size: 12px !important;
  font-weight: 900 !important;
  white-space: nowrap !important;
}

#panel-schedules .sched-list-cust,
#panel-schedules .sched-list-job,
.tcal-overlay .sched-list-cust,
.tcal-overlay .sched-list-job {
  color: #082766 !important;
  font-size: 14px !important;
  font-weight: 900 !important;
  line-height: 1.3 !important;
}

#panel-schedules .sched-list-team,
.tcal-overlay .sched-list-team {
  display: inline-flex !important;
  align-items: center !important;
  min-height: 28px !important;
  max-width: 150px !important;
  padding: 4px 12px !important;
  border-radius: 6px !important;
  background: #e8eef7 !important;
  color: #0645ad !important;
  font-size: 12px !important;
  font-weight: 900 !important;
  line-height: 1.25 !important;
  white-space: normal !important;
}

#panel-schedules .sched-list-date,
.tcal-overlay .sched-list-date {
  color: #082766 !important;
  font-size: 15px !important;
  font-weight: 900 !important;
  line-height: 1.25 !important;
  white-space: nowrap !important;
}

#panel-schedules .sched-list-date small,
.tcal-overlay .sched-list-date small {
  display: block !important;
  margin-top: 4px !important;
  color: #526b96 !important;
  font-size: 12px !important;
  font-weight: 800 !important;
}

#panel-schedules .sched-status-control,
.tcal-overlay .sched-status-control {
  position: relative !important;
  display: inline-flex !important;
  align-items: center !important;
  min-width: 126px !important;
  height: 34px !important;
  border: 1px solid !important;
  border-radius: 999px !important;
  overflow: hidden !important;
  color: #174ea6 !important;
  background: #e8eff8 !important;
}

#panel-schedules .sched-status-control::after,
.tcal-overlay .sched-status-control::after {
  content: "" !important;
  position: absolute !important;
  right: 13px !important;
  top: 50% !important;
  width: 8px !important;
  height: 8px !important;
  border-right: 2px solid currentColor !important;
  border-bottom: 2px solid currentColor !important;
  transform: translateY(-65%) rotate(45deg) !important;
  pointer-events: none !important;
}

#panel-schedules .sched-status-select,
.tcal-overlay .sched-status-select {
  appearance: none !important;
  -webkit-appearance: none !important;
  width: 100% !important;
  height: 100% !important;
  border: 0 !important;
  outline: 0 !important;
  background: transparent !important;
  color: inherit !important;
  padding: 0 34px 0 18px !important;
  font-size: 13px !important;
  font-weight: 900 !important;
  cursor: pointer !important;
  box-shadow: none !important;
}

#panel-schedules .sched-status-select:disabled,
.tcal-overlay .sched-status-select:disabled {
  opacity: .65 !important;
  cursor: wait !important;
}

#panel-schedules .sched-status-control.sls-doing,
.tcal-overlay .sched-status-control.sls-doing {
  background: #fff3cd !important;
  color: #9a5b00 !important;
  border-color: #f4c653 !important;
}

#panel-schedules .sched-status-control.sls-done,
.tcal-overlay .sched-status-control.sls-done {
  background: #dcfce7 !important;
  color: #15803d !important;
  border-color: #63d98b !important;
}

#panel-schedules .sched-status-control.sls-upcoming,
.tcal-overlay .sched-status-control.sls-upcoming {
  background: #e8eff8 !important;
  color: #174ea6 !important;
  border-color: #cbd9ee !important;
}

#panel-schedules .sched-status-control.sls-cancel,
.tcal-overlay .sched-status-control.sls-cancel {
  background: #fee2e2 !important;
  color: #b91c1c !important;
  border-color: #f7a8a8 !important;
}

#panel-schedules .sched-list-empty,
.tcal-overlay .sched-list-empty {
  text-align: center !important;
  padding: 44px 16px !important;
  color: #6b7d9b !important;
  font-weight: 800 !important;
}

@media (max-width: 768px) {
  #panel-schedules .sched-list-head,
  .tcal-overlay .sched-list-head {
    grid-template-columns: 1fr !important;
    align-items: stretch !important;
  }

  #panel-schedules .sched-list-table,
  .tcal-overlay .sched-list-table {
    min-width: 980px !important;
  }
}
/* === CODEX SCHEDULE JOB LIST REAL FIX END === */

/* === CODEX CERTIFICATIONS REDESIGN START === */
#panel-certifications .cert-board {
  background: transparent !important;
  border: 0 !important;
  padding: 0 !important;
  box-shadow: none !important;
}

#panel-certifications .cert-head {
  min-height: 172px !important;
  display: grid !important;
  grid-template-columns: 1fr !important;
  align-items: end !important;
  gap: 22px !important;
  padding: 30px 34px !important;
  border-radius: 18px !important;
  background:
    linear-gradient(135deg, rgba(255,255,255,.08), rgba(255,255,255,0) 44%),
    linear-gradient(135deg, #0e2f76 0%, #174ea6 58%, #2563eb 100%) !important;
  box-shadow: 0 18px 42px rgba(14,47,118,.20) !important;
  overflow: hidden !important;
}

#panel-certifications .cert-head::before {
  content: "";
  position: absolute;
  right: 34px;
  top: 26px;
  width: 92px;
  height: 92px;
  border: 1px solid rgba(255,255,255,.28);
  border-radius: 18px;
  background: linear-gradient(135deg, rgba(255,255,255,.22), rgba(255,255,255,.05));
  transform: rotate(6deg);
}

#panel-certifications .cert-head::after {
  content: "";
  position: absolute;
  inset: auto -80px -130px auto;
  width: 330px;
  height: 330px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(255,255,255,.22), transparent 64%);
  pointer-events: none;
}

#panel-certifications .cert-head > * {
  position: relative;
  z-index: 1;
}

#panel-certifications .cert-kicker {
  width: max-content !important;
  max-width: 100% !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 9px !important;
  margin-bottom: 12px !important;
  padding: 6px 13px !important;
  border: 1px solid rgba(255,255,255,.22) !important;
  border-radius: 999px !important;
  background: rgba(255,255,255,.14) !important;
  color: rgba(255,255,255,.94) !important;
  font-size: 11px !important;
  font-weight: 900 !important;
  letter-spacing: .13em !important;
}

#panel-certifications .cert-kicker::before {
  content: "";
  width: 7px;
  height: 7px;
  border-radius: 999px;
  background: #fff;
}

#panel-certifications .cert-title {
  color: #fff !important;
  font-size: 32px !important;
  line-height: 1.15 !important;
  font-weight: 900 !important;
  letter-spacing: 0 !important;
}

#panel-certifications .cert-sub {
  max-width: 620px !important;
  margin-top: 10px !important;
  color: rgba(255,255,255,.78) !important;
  font-size: 15px !important;
  font-weight: 800 !important;
}

#panel-certifications .cert-grid {
  display: grid !important;
  grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
  gap: 18px !important;
  margin-top: 20px !important;
}

#panel-certifications .cert-card {
  min-height: 188px !important;
  width: 100% !important;
  display: grid !important;
  grid-template-rows: 1fr auto !important;
  border: 1px solid #cfe0f6 !important;
  border-radius: 16px !important;
  background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%) !important;
  box-shadow: 0 12px 28px rgba(14,47,118,.08) !important;
  text-align: left !important;
  overflow: hidden !important;
  cursor: pointer !important;
  position: relative !important;
  transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease !important;
}

#panel-certifications .cert-card::before {
  content: "" !important;
  position: absolute !important;
  inset: 0 auto 0 0 !important;
  width: 5px !important;
  background: linear-gradient(180deg, #174ea6, #3edbf0) !important;
}

#panel-certifications .cert-card::after {
  content: "\0E04\0E25\0E34\0E01\0E14\0E39\0E02\0E49\0E2D\0E21\0E39\0E25" !important;
  position: absolute !important;
  right: 16px !important;
  bottom: 15px !important;
  color: #174ea6 !important;
  font-size: 12px !important;
  font-weight: 900 !important;
}

#panel-certifications .cert-card:hover {
  transform: translateY(-4px) !important;
  border-color: #8cbfff !important;
  box-shadow: 0 18px 38px rgba(14,47,118,.16) !important;
}

#panel-certifications .cert-card-top {
  min-height: 118px !important;
  display: grid !important;
  grid-template-columns: 54px minmax(0, 1fr) auto !important;
  gap: 14px !important;
  align-items: start !important;
  padding: 22px 20px 10px 24px !important;
  border-bottom: 0 !important;
}

#panel-certifications .cert-icon {
  width: 54px !important;
  height: 54px !important;
  border-radius: 14px !important;
  display: grid !important;
  place-items: center !important;
  background: #eaf3ff !important;
  border: 1px solid #bdd7ff !important;
  color: #174ea6 !important;
  font-size: 0 !important;
}

#panel-certifications .cert-icon::before {
  content: "" !important;
  width: 28px !important;
  height: 28px !important;
  display: block !important;
  border-radius: 0 !important;
  background: #174ea6 !important;
  box-shadow: none !important;
  -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='8' r='6'/%3E%3Cpath d='M15.477 12.89 17 22l-5-3-5 3 1.523-9.11'/%3E%3C/svg%3E") center / contain no-repeat !important;
  mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='8' r='6'/%3E%3Cpath d='M15.477 12.89 17 22l-5-3-5 3 1.523-9.11'/%3E%3C/svg%3E") center / contain no-repeat !important;
}

#panel-certifications .cert-info {
  min-width: 0 !important;
}

#panel-certifications .cert-name {
  color: #0e2f76 !important;
  font-size: 18px !important;
  line-height: 1.28 !important;
  font-weight: 900 !important;
  word-break: break-word !important;
}

#panel-certifications .cert-count-text {
  margin-top: 6px !important;
  color: #64789d !important;
  font-size: 13px !important;
  font-weight: 800 !important;
}

#panel-certifications .cert-count {
  min-width: 48px !important;
  height: 48px !important;
  display: grid !important;
  place-items: center !important;
  border-radius: 14px !important;
  background: #0e2f76 !important;
  color: #fff !important;
  font-size: 26px !important;
  font-weight: 900 !important;
  line-height: 1 !important;
  box-shadow: 0 10px 20px rgba(14,47,118,.18) !important;
}

#panel-certifications .cert-people {
  min-height: 62px !important;
  display: flex !important;
  flex-wrap: wrap !important;
  align-items: center !important;
  gap: 7px !important;
  padding: 12px 120px 16px 24px !important;
  border-top: 1px solid #e4eefb !important;
  background: #f4f8ff !important;
}

#panel-certifications .cert-people span {
  min-width: 32px !important;
  height: 28px !important;
  display: inline-grid !important;
  place-items: center !important;
  border-radius: 8px !important;
  background: #fff !important;
  color: #174ea6 !important;
  border: 1px solid #cfe0f6 !important;
  padding: 3px 8px !important;
  font-size: 11px !important;
  font-weight: 900 !important;
}

#cert-detail-overlay {
  z-index: 920 !important;
}

#cert-detail-overlay .cert-modal {
  width: min(1040px, calc(100vw - 56px)) !important;
  max-height: 92vh !important;
  position: relative !important;
  border: 1px solid #cfe0f6 !important;
  border-radius: 18px !important;
  overflow: hidden !important;
  background: #fff !important;
  box-shadow: 0 24px 60px rgba(14,47,118,.22) !important;
}

#cert-detail-overlay .cert-modal-head {
  min-height: 168px !important;
  padding: 30px 34px !important;
  background:
    linear-gradient(135deg, rgba(255,255,255,.10), rgba(255,255,255,0) 42%),
    linear-gradient(135deg, #0e2f76 0%, #174ea6 100%) !important;
}

#cert-detail-overlay .cert-detail-close-btn {
  position: absolute !important;
  top: 22px !important;
  right: 28px !important;
  width: 44px !important;
  height: 44px !important;
  min-height: 44px !important;
  padding: 0 !important;
  display: grid !important;
  place-items: center !important;
  border: 1px solid rgba(255,255,255,.55) !important;
  border-radius: 9px !important;
  background: rgba(255,255,255,.12) !important;
  color: #fff !important;
  font-size: 32px !important;
  line-height: 1 !important;
  font-weight: 900 !important;
  cursor: pointer !important;
  box-shadow: none !important;
}

#cert-detail-overlay .cert-detail-close-btn:hover {
  background: rgba(255,255,255,.26) !important;
}

#cert-detail-overlay .cert-modal-kicker {
  color: rgba(255,255,255,.78) !important;
  font-size: 11px !important;
  font-weight: 900 !important;
  letter-spacing: .14em !important;
  text-transform: uppercase !important;
}

#cert-detail-overlay .cert-modal-title {
  margin-top: 8px !important;
  padding-right: 58px !important;
  color: #fff !important;
  font-size: 28px !important;
  line-height: 1.2 !important;
  font-weight: 900 !important;
}

#cert-detail-overlay .cert-modal-sub {
  margin-top: 14px !important;
  display: inline-flex !important;
  align-items: center !important;
  min-height: 30px !important;
  padding: 5px 14px !important;
  border-radius: 999px !important;
  background: rgba(255,255,255,.18) !important;
  border: 1px solid rgba(255,255,255,.26) !important;
  color: #fff !important;
  font-size: 13px !important;
  font-weight: 900 !important;
}

#cert-detail-overlay .cert-close {
  position: absolute !important;
  top: 22px !important;
  right: 22px !important;
  width: 42px !important;
  height: 42px !important;
  border: 1px solid rgba(255,255,255,.25) !important;
  border-radius: 999px !important;
  background: rgba(255,255,255,.14) !important;
  color: #fff !important;
  font-size: 24px !important;
  font-weight: 900 !important;
  cursor: pointer !important;
}

#cert-detail-overlay .cert-close:hover {
  background: rgba(255,255,255,.26) !important;
}

#cert-detail-overlay .cert-holder-list {
  max-height: calc(92vh - 190px) !important;
  overflow: auto !important;
  display: grid !important;
  gap: 12px !important;
  padding: 24px !important;
  background: #f7fbff !important;
}

#cert-detail-overlay .cert-holder {
  display: grid !important;
  grid-template-columns: 58px minmax(0, 1fr) auto !important;
  gap: 16px !important;
  align-items: center !important;
  min-height: 86px !important;
  padding: 16px !important;
  border: 1px solid #d6e4f5 !important;
  border-radius: 14px !important;
  background: #fff !important;
  box-shadow: 0 8px 18px rgba(14,47,118,.06) !important;
}

#cert-detail-overlay .cert-holder-avatar {
  width: 58px !important;
  height: 58px !important;
  border-radius: 14px !important;
  display: grid !important;
  place-items: center !important;
  background: #eaf3ff !important;
  color: #174ea6 !important;
  border: 1px solid #bdd7ff !important;
  font-size: 16px !important;
  font-weight: 900 !important;
}

#cert-detail-overlay .cert-holder-main {
  min-width: 0 !important;
}

#cert-detail-overlay .cert-holder-name {
  color: #0e2f76 !important;
  font-size: 17px !important;
  font-weight: 900 !important;
  line-height: 1.25 !important;
  white-space: normal !important;
}

#cert-detail-overlay .cert-holder-meta {
  display: flex !important;
  flex-wrap: wrap !important;
  gap: 7px !important;
  margin-top: 8px !important;
  color: #526b96 !important;
  font-size: 12px !important;
  font-weight: 800 !important;
  white-space: normal !important;
}

#cert-detail-overlay .cert-holder-chip {
  display: inline-flex !important;
  align-items: center !important;
  min-height: 25px !important;
  padding: 3px 9px !important;
  border-radius: 999px !important;
  background: #edf4ff !important;
  color: #174ea6 !important;
  border: 1px solid #cfe0f6 !important;
  font-size: 12px !important;
  font-weight: 900 !important;
}

#cert-detail-overlay .cert-file-link {
  min-height: 40px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  padding: 9px 16px !important;
  border-radius: 10px !important;
  background: #0e2f76 !important;
  color: #fff !important;
  text-decoration: none !important;
  font-size: 13px !important;
  font-weight: 900 !important;
  white-space: nowrap !important;
}

#cert-detail-overlay .cert-file-link:hover {
  background: #174ea6 !important;
}
#cert-detail-overlay .cert-holder-actions {
  display: flex !important;
  align-items: center !important;
  justify-content: flex-end !important;
  flex-wrap: wrap !important;
  gap: 8px !important;
}

#cert-detail-overlay .cert-file-empty {
  min-height: 36px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  padding: 8px 13px !important;
  border-radius: 999px !important;
  background: #f1f6ff !important;
  border: 1px solid #cfe0f6 !important;
  color: #174ea6 !important;
  font-size: 12px !important;
  font-weight: 900 !important;
  white-space: nowrap !important;
}

#cert-detail-overlay .cert-attach-form {
  display: inline-flex !important;
  align-items: center !important;
  gap: 8px !important;
  margin: 0 !important;
}

#cert-detail-overlay .cert-file-input {
  position: absolute !important;
  inline-size: 1px !important;
  block-size: 1px !important;
  opacity: 0 !important;
  pointer-events: none !important;
}

#cert-detail-overlay .cert-upload-trigger,
#cert-detail-overlay .cert-submit {
  min-height: 40px !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 7px !important;
  border-radius: 10px !important;
  padding: 9px 13px !important;
  font-family: inherit !important;
  font-size: 12px !important;
  font-weight: 900 !important;
  cursor: pointer !important;
  white-space: nowrap !important;
}

#cert-detail-overlay .cert-upload-trigger {
  max-width: 180px !important;
  border: 1px dashed #8fb5ec !important;
  background: #f6faff !important;
  color: #174ea6 !important;
}

#cert-detail-overlay .cert-upload-trigger:hover {
  background: #eaf3ff !important;
  border-color: #4870c8 !important;
}

#cert-detail-overlay .cert-upload-trigger svg {
  width: 16px !important;
  height: 16px !important;
  stroke: currentColor !important;
  fill: none !important;
  stroke-width: 2 !important;
  flex: 0 0 16px !important;
}

#cert-detail-overlay .cert-upload-name {
  min-width: 0 !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}

#cert-detail-overlay .cert-submit {
  border: 0 !important;
  background: #2455dc !important;
  color: #fff !important;
}

#cert-detail-overlay .cert-submit[hidden],
#cert-detail-overlay .cert-attach-form:not(.is-ready) .cert-submit {
  display: none !important;
}

#cert-detail-overlay .cert-attach-form.is-ready .cert-submit {
  display: inline-flex !important;
}

#cert-detail-overlay .cert-submit:hover {
  background: #173b8e !important;
}

@media (max-width: 1200px) {
  #panel-certifications .cert-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
  }
}

@media (max-width: 860px) {
  #panel-certifications .cert-head {
    grid-template-columns: 1fr !important;
    align-items: stretch !important;
  }

  #panel-certifications .cert-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
  }
}

@media (max-width: 620px) {
  #panel-certifications .cert-grid {
    grid-template-columns: 1fr !important;
  }

  #cert-detail-overlay .cert-modal {
    width: calc(100vw - 24px) !important;
  }

  #cert-detail-overlay .cert-holder {
    grid-template-columns: 48px minmax(0, 1fr) !important;
  }

  #cert-detail-overlay .cert-holder-actions {
    grid-column: 1 / -1 !important;
    justify-content: stretch !important;
  }

  #cert-detail-overlay .cert-file-link,
  #cert-detail-overlay .cert-file-empty,
  #cert-detail-overlay .cert-attach-form,
  #cert-detail-overlay .cert-upload-trigger,
  #cert-detail-overlay .cert-submit {
    width: 100% !important;
  }
}
/* === CODEX CERTIFICATIONS REDESIGN END === */

/* CODEX OVERVIEW TEAM CARD REDESIGN START */
#roster-grid {
  grid-template-columns: repeat(auto-fill, minmax(320px, 340px)) !important;
  justify-content: start !important;
  align-items: start !important;
  gap: 18px !important;
  padding: 18px !important;
  background: #ffffff !important;
  border-radius: 10px !important;
}

#roster-grid .emp-card {
  width: 100% !important;
  min-height: 188px !important;
  display: flex !important;
  grid-template-rows: none !important;
  flex-direction: column !important;
  padding: 0 !important;
  border: 0 !important;
  border-radius: 14px !important;
  overflow: hidden !important;
  background: #aac0e1 !important;
  color: #fff !important;
  box-shadow: 0 12px 26px rgba(14,47,118,.18) !important;
  cursor: pointer !important;
  opacity: 1 !important;
  animation: popIn .45s cubic-bezier(.34,1.4,.64,1) forwards;
  transition: transform .18s ease, box-shadow .18s ease !important;
}

#roster-grid .emp-card::before,
#roster-grid .emp-card::after {
  content: none !important;
  display: none !important;
}

#roster-grid .emp-card:hover {
  transform: translateY(-2px) !important;
  border-color: transparent !important;
  box-shadow: 0 18px 34px rgba(14,47,118,.24) !important;
}

#roster-grid .overview-person-top {
  min-height: 110px;
  display: grid;
  grid-template-columns: minmax(0, 1fr) 108px;
  gap: 12px;
  padding: 18px 18px 14px;
  background: linear-gradient(90deg, #17387f 0%, #17387f 62%, #1d4693 62%, #1d4693 100%);
  color: #fff;
}

#roster-grid .overview-person-copy {
  min-width: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

#roster-grid .overview-person-id {
  color: rgba(255,255,255,.66);
  font-size: 11px;
  font-weight: 800;
  line-height: 1.2;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

#roster-grid .overview-person-name {
  color: #fff;
  font-size: 18px;
  font-weight: 900;
  line-height: 1.18;
  margin-top: 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

#roster-grid .overview-person-role,
#roster-grid .overview-person-phone {
  color: rgba(255,255,255,.82);
  font-size: 12px;
  font-weight: 800;
  line-height: 1.28;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

#roster-grid .overview-person-role {
  margin-top: 2px;
}

#roster-grid .overview-person-phone {
  color: rgba(255,255,255,.68);
}

#roster-grid .overview-person-media {
  position: relative;
  display: grid;
  place-items: end center;
  align-content: end;
}

#roster-grid .overview-person-brand {
  position: absolute;
  top: -12px;
  right: -12px;
  width: 28px;
  height: 28px;
  display: grid;
  place-items: center;
  border-radius: 8px;
  background: #4163a7;
  color: #fff;
  font-size: 12px;
  font-weight: 900;
}

#roster-grid .overview-avatar {
  width: 74px;
  height: 74px;
  border-radius: 50%;
  overflow: hidden;
  display: grid;
  place-items: center;
  background: #3e5b9a;
  color: #fff;
  font-size: 22px;
  font-weight: 900;
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.08);
}

#roster-grid .overview-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

#roster-grid .overview-avatar .initials {
  width: 100%;
  height: 100%;
  display: grid;
  place-items: center;
}

#roster-grid .overview-person-skills {
  display: flex !important;
  flex-wrap: wrap !important;
  align-items: flex-start !important;
  gap: 8px !important;
  min-height: 76px !important;
  margin: 0 !important;
  padding: 10px 16px 14px !important;
  border: 0 !important;
  background: #aac0e1 !important;
  overflow: hidden !important;
}

#roster-grid .overview-person-skills::before {
  display: none !important;
}

#roster-grid .emp-skill-tag,
#roster-grid .emp-id-skill-strip .emp-skill-tag,
#roster-grid .emp-id-skill-strip .emp-skill-tag:first-child {
  min-height: 24px !important;
  display: inline-flex !important;
  grid-column: auto !important;
  align-items: center !important;
  gap: 5px !important;
  padding: 4px 10px !important;
  border: 0 !important;
  border-radius: 999px !important;
  background: #f5feff !important;
  color: #0e2f76 !important;
  font-size: 12px !important;
  font-weight: 800 !important;
  line-height: 1 !important;
  text-align: left !important;
  box-shadow: 0 1px 0 rgba(255,255,255,.55) !important;
}

#roster-grid .emp-skill-tag svg {
  width: 12px !important;
  height: 12px !important;
  flex: 0 0 12px !important;
  stroke: currentColor !important;
  fill: none !important;
  stroke-width: 2 !important;
  stroke-linecap: round !important;
  stroke-linejoin: round !important;
}

#roster-grid .emp-skill-tag.plus-tag {
  background: rgba(255,255,255,.72) !important;
  color: #17387f !important;
}

@media (max-width: 780px) {
  #roster-grid {
    grid-template-columns: minmax(0, 1fr) !important;
  }

  #roster-grid .overview-person-top {
    grid-template-columns: minmax(0, 1fr) 96px;
    padding: 16px 16px 13px;
  }
}

@media (max-width: 420px) {
  #roster-grid .overview-person-top {
    grid-template-columns: minmax(0, 1fr) 82px;
    gap: 8px;
  }

  #roster-grid .overview-person-name {
    font-size: 16px;
  }

  #roster-grid .overview-person-id,
  #roster-grid .overview-person-role,
  #roster-grid .overview-person-phone {
    font-size: 11px;
  }

  #roster-grid .overview-avatar {
    width: 62px;
    height: 62px;
    font-size: 18px;
  }
}
/* CODEX OVERVIEW TEAM CARD REDESIGN END */

/* CODEX TEAM DRAG DROP START */
#view-team .team-card {
  position: relative;
  border-color: #b8d5ff;
}

#view-team .team-card::after {
  content: '\0E27\0E32\0E07\0E25\0E39\0E01\0E17\0E35\0E21\0E17\0E35\0E48\0E19\0E35\0E48\0E40\0E1E\0E37\0E48\0E2D\0E22\0E49\0E32\0E22\0E17\0E35\0E21';
  position: absolute;
  inset: 74px 16px 16px;
  display: grid;
  place-items: center;
  border: 2px dashed #7fb6ff;
  border-radius: 12px;
  background: rgba(232, 241, 255, .82);
  color: #0e2f76;
  font-size: 13px;
  font-weight: 900;
  opacity: 0;
  pointer-events: none;
  transform: scale(.98);
  transition: opacity .16s ease, transform .16s ease;
  z-index: 4;
}

#view-team .team-card.team-drop-over {
  border-color: #1d4ed8;
  box-shadow: 0 18px 38px rgba(29, 78, 216, .18), 0 0 0 4px rgba(125, 179, 255, .25);
}

#view-team .team-card.team-drop-over::after {
  opacity: 1;
  transform: scale(1);
}

#view-team .team-body {
  min-height: 168px;
}

#view-team .member.member-draggable {
  cursor: grab;
}

#view-team .member.member-draggable:active {
  cursor: grabbing;
}

#view-team .member.member-dragging {
  opacity: .42;
  transform: scale(.985);
}

#view-team .member.member-dragging::before {
  opacity: 1;
}

.team-dnd-toast {
  position: fixed;
  top: 18px;
  right: 22px;
  z-index: 1200;
  min-width: 220px;
  max-width: calc(100vw - 44px);
  padding: 12px 16px;
  border-radius: 10px;
  background: #0e2f76;
  color: #fff;
  box-shadow: 0 18px 38px rgba(14, 47, 118, .24);
  font-weight: 800;
  font-size: 14px;
  transform: translateY(-10px);
  opacity: 0;
  transition: opacity .18s ease, transform .18s ease;
}

.team-dnd-toast.show {
  opacity: 1;
  transform: translateY(0);
}

.team-dnd-toast.error {
  background: #991b1b;
}
#roster-grid .overview-person-skills {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: flex-start !important;
    gap: 8px !important;
    min-height: 76px !important;
    margin: 0 !important;
    padding: 10px 16px 14px !important;
    border: 0 !important;
    background: #ffffff !important;
    overflow: hidden !important;
}
#roster-grid .emp-card {
    width: 100% !important;
    min-height: 188px !important;
    display: flex !important;
    grid-template-rows: none !important;
    flex-direction: column !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 14px !important;
    overflow: hidden !important;
    background: #aac0e1 !important;
    color: #fff !important;
    box-shadow: 0 12px 26px rgba(14, 47, 118, .18) !important;
    cursor: pointer !important;
    opacity: 1 !important;
    animation: popIn .45s cubic-bezier(.34, 1.4, .64, 1) forwards;
    transition: transform .18s ease, box-shadow .18s ease !important;
}
#roster-grid .emp-card {
    width: 100% !important;
    min-height: 188px !important;
    display: flex !important;
    grid-template-rows: none !important;
    flex-direction: column !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 14px !important;
    overflow: hidden !important;
    background: #fdfdfd !important;
    color: #fff !important;
    box-shadow: 0 12px 26px rgba(14, 47, 118, .18) !important;
    cursor: pointer !important;
    opacity: 1 !important;
    animation: popIn .45s cubic-bezier(.34, 1.4, .64, 1) forwards;
    transition: transform .18s ease, box-shadow .18s ease !important;
}
/* CODEX TEAM DRAG DROP END */
</style>
</head>
<body>
<button class="sb-toggle" type="button" onclick="document.querySelector('.sidebar').classList.toggle('open')">☰</button>
<aside class="sidebar">
  <div class="sb-logo">
    <div class="sb-mark">3E</div>
    <div>
      <div class="sb-title">ทริปเปิ้ล อี เทรดดิ้ง</div>
      <div class="sb-sub">ระบบจัดการช่าง</div>
    </div>
  </div>
  <div class="sb-tabs">
    <button class="sb-tab active" type="button" onclick="switchTab('teams',this)">
      <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      <span class="label">ทีมช่าง</span><span class="nav-badge-count">{{ $technicians->count() }}</span>
    </button>
    <button class="sb-tab" type="button" onclick="switchTab('schedules',this)">
      <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      <span class="label">ตารางงาน</span><span class="nav-badge-count">{{ $schedules->count() }}</span>
    </button>
    <button class="sb-tab" type="button" onclick="switchTab('customers',this)">
      <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      <span class="label">ลูกค้าและไซต์งาน</span><span class="nav-badge-count">{{ $customers->count() }}</span>
    </button>
    <button class="sb-tab" type="button" onclick="switchTab('certifications',this)">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M8.8 13.1 7 22l5-3 5 3-1.8-8.9"/></svg>
      <span class="label">ใบรับรอง</span><span class="nav-badge-count">{{ $certTotal }}</span>
    </button>
    <button class="sb-tab" type="button" onclick="switchTab('aircons',this)">
      <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="10" rx="2"/><path d="M7 19h10"/><path d="M9 15v4"/><path d="M15 15v4"/><path d="M7 9h10"/></svg>
      <span class="label">ล้างแอร์</span><span class="nav-badge-count">{{ $airconTotal }}</span>
    </button>
  </div>
</aside>
<main class="main">
  @if(session('success'))<div class="flash flash-success">{{ session('success') }}</div>@endif
  @if($errors->has('delete'))<div class="flash flash-error">{{ $errors->first('delete') }}</div>@endif
<section class="panel active" id="panel-teams">
    <div class="panel-header">
      <div class="panel-title">ทีมช่าง ({{ $teams->count() }} ทีม · {{ $stats['total_tech'] ?? $technicians->count() }} คน)</div>
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
    <div class="roster-title">ภาพรวมทักษะช่าง</div>
    
  </div>
</div>
        <div class="roster-filter">
          <div class="roster-filter-row">
            <label class="roster-filter-label" for="roster-name-search">ค้นหาชื่อ</label>
            <input id="roster-name-search" class="roster-search" type="search" placeholder="ค้นหาชื่อช่าง / รหัส / ชื่อเล่น..." oninput="filterRosterSearch(this.value)">
          </div>
          <div class="roster-filter-row">
            <label class="roster-filter-label" for="roster-skill-filter">ทักษะ</label>
            <select id="roster-skill-filter" class="roster-skill-select" onchange="filterRosterSkill(this.value)">
              <option value="all">ทุกทักษะ</option>
              @foreach($skillFilters as $skill)
                <option value="{{ $skill }}">{{ $skill }}</option>
              @endforeach
            </select>
          </div>
          <div class="roster-filter-row roster-filter-actions">
            <button class="btn btn-primary roster-add-tech-btn" type="button" onclick="openModal('modal-tech')">
              + เพิ่มช่าง
            </button>
          </div>
        </div>
<div class="emp-card-grid" id="roster-grid">
          @forelse($sortedTechnicians as $m)
            @php
              $skills = collect(explode(',', $m->emp_skill ?? ''))->map(fn($x) => trim($x))->filter()->values();
              $initial = mb_substr($m->emp_name ?: $m->emp_id, 0, 2);
              $isHead = ($m->emp_position ?? '') === 'หัวหน้าทีม';
              $phoneDigits = preg_replace('/\D+/', '', $m->emp_phone ?? '');
              $phoneDisplay = '-';

              if (strlen($phoneDigits) === 10) {
                $phoneDisplay = preg_replace('/^(\d{3})(\d{3})(\d{4})$/', '$1-$2-$3', $phoneDigits);
              } elseif (strlen($phoneDigits) === 9) {
                $phoneDisplay = preg_replace('/^(\d{3})(\d{3})(\d{3})$/', '$1-$2-$3', $phoneDigits);
              } elseif ($m->emp_phone) {
                $phoneDisplay = $m->emp_phone;
              }

              $isLeave = ($m->status ?? 'active') === 'leave';
            @endphp
            <article class="emp-card {{ $isHead ? 'is-head' : '' }}"
              data-team="{{ $m->emp_team }}"
              data-skill="{{ strtolower($skills->implode(' ')) }}"
              data-name="{{ strtolower(($m->emp_name ?? '').' '.($m->emp_name_eng ?? '').' '.($m->emp_nickname ?? '').' '.($m->emp_id ?? '')) }}"
              data-search="{{ strtolower(($m->emp_name ?? '').' '.($m->emp_name_eng ?? '').' '.($m->emp_nickname ?? '').' '.($m->emp_id ?? '').' '.($m->emp_team ?? '').' '.($m->emp_skill ?? '').' '.collect($m->software_tools ?? [])->implode(' ')) }}"
              data-tech="{{ json_encode($m, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}"
              onclick="openProfileFromEl(this)">
              <div class="overview-person-top">
                <div class="overview-person-copy">
                  <div class="overview-person-id">เลขประจำตัว {{ $m->emp_id ?: '-' }}</div>
                  <div class="overview-person-name" title="{{ $m->emp_name ?: $m->emp_id }}">{{ $m->emp_name ?: $m->emp_id }}</div>
                  <div class="overview-person-role">{{ $isHead ? 'หัวหน้าทีม' : ($m->emp_position ?: 'ลูกทีม') }}</div>
                  <div class="overview-person-phone">โทร {{ $phoneDisplay }}</div>
                </div>
                <div class="overview-person-media">
                  <div class="overview-person-brand">3E</div>
                  <div class="overview-avatar">
                    @if($m->img)
                      <img
                        src="{{ asset('storage/'.$m->img) }}"
                        alt="{{ $m->emp_name }}"
                        onerror="this.onerror=null;this.style.display='none';const initials=this.parentElement?.querySelector('.initials');if(initials)initials.style.display='grid';"
                      >
                      <span class="initials" style="display:none">{{ $initial }}</span>
                    @else
                      <span class="initials">{{ $initial }}</span>
                    @endif
                  </div>
                </div>
              </div>

              <div class="overview-person-skills emp-card-skills">
                @forelse($skills as $sk)
                  <span class="emp-skill-tag">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8z"/></svg>
                    {{ $sk }}
                  </span>
                @empty
                  <span class="emp-skill-tag">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 2"/></svg>
                    ทั่วไป
                  </span>
                @endforelse
              </div>
            </article>
          @empty
            <div class="empty-state" style="grid-column:1/-1">ยังไม่มีข้อมูลช่าง</div>
          @endforelse
        </div>
      </div>
    </div>
    <div id="view-team" style="display:none">
      <div class="roster-filter">
        <div class="roster-filter-row">
          <label class="roster-filter-label" for="team-name-search">ค้นหาชื่อ</label>
          <input id="team-name-search" class="team-filter-search" type="search" placeholder="ค้นหาชื่อช่าง / รหัส / ชื่อเล่น..." oninput="filterTeamSearch(this.value)">
        </div>
        <div class="roster-filter-row">
          <label class="roster-filter-label" for="team-skill-filter">ทักษะ</label>
          <select id="team-skill-filter" class="team-skill-select" onchange="filterTeamSkill(this.value)">
            <option value="all">ทุกทักษะ</option>
            @foreach($skillFilters as $skill)
              <option value="{{ $skill }}">{{ $skill }}</option>
            @endforeach
          </select>
        </div>
        <div class="roster-filter-row roster-filter-actions">
          <button class="btn btn-primary roster-add-tech-btn" type="button" onclick="openModal('modal-tech')">
            + เพิ่มช่าง
          </button>
        </div>
      </div>
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
              $teamSearch = strtolower(trim($teamName.' '.$allMbr->map(fn($m) => trim(($m->emp_name ?? '').' '.($m->emp_name_eng ?? '').' '.($m->emp_nickname ?? '').' '.($m->emp_id ?? '').' '.($m->emp_position ?? '')))->implode(' ')));
              $teamSkillSearch = strtolower($allMbr->flatMap(fn($m) => collect(explode(',', $m->emp_skill ?? ''))->map(fn($x) => trim($x))->filter())->implode(' '));
            @endphp
            <article class="team-card" data-search="{{ $teamSearch }}" data-skill="{{ $teamSkillSearch }}">
              <div class="team-head-bar">
                <div style="flex:1;min-width:0">
                  <div class="team-title">{{ $teamName ?: '-' }}</div>
                  <div class="team-meta">สมาชิก {{ $members->count() }} คน · หัวหน้าขึ้นก่อน</div>
                </div>
                <button type="button" class="team-cal-btn" data-team="{{ $teamName }}" onclick="event.stopPropagation();openTeamCalendar(this.dataset.team)">ปฏิทิน <span class="badge-count">{{ $teamScheds->count() }}</span></button>
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
        <div class="empty-state" id="team-empty-filter" style="display:none">ไม่พบทีมตามเงื่อนไขที่ค้นหา</div>
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
          <button class="btn btn-primary sched-add-job-btn" type="button" onclick="openAddSchedModal()">+ เพิ่มงาน</button>
          <div class="sched-nav-group">
            <button class="sched-nav-btn" type="button" onclick="SCHED_BOARD.nav(-1)">‹</button>
            <div class="sched-control-month" id="sched-board-control-month">-</div>
            <button class="sched-nav-btn" type="button" onclick="SCHED_BOARD.nav(1)">›</button>
          </div>
        </div>
      </div>
      <div class="sched-calendar-card">
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
            <colgroup>
              <col style="width:60px">
              <col style="width:calc((100% - 60px) / 6)">
              <col style="width:calc((100% - 60px) / 6)">
              <col style="width:calc((100% - 60px) / 6)">
              <col style="width:calc((100% - 60px) / 6)">
              <col style="width:calc((100% - 60px) / 6)">
              <col style="width:calc((100% - 60px) / 6)">
            </colgroup>
            <thead>
              <tr>
                <th style="width:60px">#</th>
                <th>&#3623;&#3633;&#3609;&#3607;&#3637;&#3656;&#3607;&#3635;&#3591;&#3634;&#3609;</th>
                <th>&#3607;&#3637;&#3617;&#3594;&#3656;&#3634;&#3591;</th>
                <th>&#3619;&#3634;&#3618;&#3621;&#3632;&#3648;&#3629;&#3637;&#3618;&#3604;&#3591;&#3634;&#3609;</th>
                <th>&#3648;&#3621;&#3586;&#3591;&#3634;&#3609; (SO)</th>
                <th>&#3594;&#3639;&#3656;&#3629;&#3621;&#3641;&#3585;&#3588;&#3657;&#3634;</th>
                <th>&#3626;&#3606;&#3634;&#3609;&#3632;&#3591;&#3634;&#3609;</th>
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
    <div>
      <div class="customer-eyebrow">CUSTOMER / SITE</div>
      <div class="panel-title">ลูกค้า / ไซต์งาน ({{ $customers->count() }} ราย)</div>
      <div class="customer-hero-sub">รวมข้อมูลลูกค้า สถานที่ติดตั้ง รอบดูแล และสถานะงาน</div>
    </div>
  </div>
    <div class="customer-site-search-wrap">
      <label class="customer-site-search-label" for="cust-search">ค้นหาข้อมูล</label>
      <input type="search" class="customer-site-search" id="cust-search" placeholder="ค้นหาชื่อลูกค้า / ไซต์งาน / ผู้ติดต่อ / เบอร์โทร..." oninput="filterCustTable(this.value)">
    </div>
    <div class="cust-metrics">
      <div class="cust-metric">
        <div class="cust-metric-label">ทั้งหมด</div>
        <div class="cust-metric-value">{{ $customers->count() }}</div>
        <div class="cust-metric-note">รายการลูกค้า/ไซต์งาน</div>
      </div>
      <div class="cust-metric">
        <div class="cust-metric-label">Solar</div>
        <div class="cust-metric-value">{{ $custSummary['solar']->count() }}</div>
        <div class="cust-metric-note">ติดตั้ง / ล้าง / ซ่อม</div>
      </div>
      <div class="cust-metric">
        <div class="cust-metric-label">ไฟฟ้า</div>
        <div class="cust-metric-value">{{ $custSummary['electrical']->count() }}</div>
        <div class="cust-metric-note">งานไฟฟ้า</div>
      </div>
      <div class="cust-metric">
        <div class="cust-metric-label">โยธา</div>
        <div class="cust-metric-value">{{ $custSummary['civil']->count() }}</div>
        <div class="cust-metric-note">งานโยธา</div>
      </div>
      <div class="cust-metric">
        <div class="cust-metric-label">ทั่วไป</div>
        <div class="cust-metric-value">{{ $custSummary['general']->count() }}</div>
        <div class="cust-metric-note">งานทั่วไป</div>
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
      <div class="customer-project-table-wrap">
        <table class="customer-project-table">
          <thead><tr><th style="width:68px">ลำดับ</th><th>ลูกค้า / ไซต์งาน</th><th>ประเภทงาน</th><th>วันที่เสร็จงาน</th><th>รอบดูแล Solar</th><th>การทำงาน</th></tr></thead>
          <tbody id="cust-tbody">
            @foreach($customers as $idx => $c)
              @php
                $cat = method_exists($c, 'getCategory') ? $c->getCategory() : (str_starts_with((string)($c->type_project ?? ''), 'solar') ? 'solar' : (($c->type_project ?? '') ?: 'general'));
                $isSolar = str_starts_with((string)($c->type_project ?? ''), 'solar');
                $custContactText = trim(($c->contact_name ?? '').((($c->contact_name ?? '') && ($c->phone ?? '')) ? ' · ' : '').(($c->phone ?? '') ? 'โทร '.($c->phone ?? '') : ''));
              @endphp
              <tr class="cust-row" data-cat="{{ $cat }}" data-search="{{ strtolower(($c->name ?? '').' '.($c->desc ?? '').' '.($c->contact_name ?? '').' '.($c->phone ?? '').' '.($c->status ?? '').' '.($c->type_project ?? '')) }}">
                <td><span class="cust-index">{{ $idx + 1 }}</span></td>
                <td>
                  <button class="cust-name-btn" type="button" data-cust="{{ json_encode($c, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openCustDetail(this)">{{ $c->name }}</button>
                  @if($c->desc)<div class="cust-desc"><span class="cust-line-label">ไซต์งาน</span><span class="cust-line-text">{{ $c->desc }}</span></div>@endif
                  @if($custContactText)
                    <div class="cust-contact"><span class="cust-line-label">ติดต่อ</span><span class="cust-line-text">{{ $custContactText }}</span></div>
                  @endif
                </td>
                <td>
                  <div class="cust-type-stack">
                    <span class="cust-type-label">ประเภทงาน</span>
                    <span class="cust-type-plain">{{ $jobTypes[$c->type_project ?? 'general'] ?? ($c->type_project ?: 'ทั่วไป') }}</span>
                  </div>
                </td>
                <td>
                  @if($c->supervisor)
                    <span class="cust-date-plain">{{ \Carbon\Carbon::parse($c->supervisor)->format('d/m/') }}{{ \Carbon\Carbon::parse($c->supervisor)->year + 543 }}</span>
                  @else
                    <span class="cust-muted">ยังไม่ระบุ</span>
                  @endif
                </td>
                <td>
                  @if($isSolar)
                    <div class="wash-cycle-cell">
                      <span class="wash-cycle-chip">ทุก {{ $c->wash_cycle ?? 6 }} เดือน</span>
                      @if($c->wash_next)
                        <small>รอบถัดไป {{ \Carbon\Carbon::parse($c->wash_next)->format('d/m/') }}{{ \Carbon\Carbon::parse($c->wash_next)->year + 543 }}</small>
                      @else
                        <small>ยังไม่กำหนดวันถัดไป</small>
                      @endif
                    </div>
                  @else
                    <span class="cust-muted">ไม่ใช่งาน Solar</span>
                  @endif
                </td>
                <td>
                  <div class="cust-row-actions">
                    <button class="btn btn-sm btn-ghost" type="button" data-cust="{{ json_encode($c, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openCustEdit(this)">แก้ไข</button>
                    <form method="POST" action="{{ route('cust.delete', $c->id) }}" onsubmit="return confirm('ต้องการลบลูกค้า/ไซต์งาน {{ addslashes($c->name) }} ?')">@csrf<button class="btn btn-sm btn-danger" type="submit">ลบ</button></form>
                  </div>
                </td>
              </tr>
            @endforeach
            <tr id="cust-empty-row" style="display:none">
              <td colspan="6" class="cust-empty-filter">ไม่พบข้อมูลลูกค้า/ไซต์งานตามคำค้นหา</td>
            </tr>
          </tbody>
        </table>
      </div>
    @endif
  </section>
  <section class="panel" id="panel-accounts">
    <div class="panel-header">
      <div class="panel-title">บัญชีผู้ใช้ Solar / Monitoring ({{ $accounts->count() }} บัญชี)</div>
    </div>
    <div class="roster-filter account-monitoring-filter">
      <div class="roster-filter-row">
        <label class="roster-filter-label" for="account-monitoring-search">ค้นหาชื่อ</label>
        <input id="account-monitoring-search" class="roster-search" type="search" placeholder="ค้นหาชื่อระบบ / ลูกค้า / Inverter / Username / Email..." oninput="filterTable('acc-tbody',this.value)">
      </div>
      <div class="roster-filter-row roster-filter-actions">
        <button class="btn btn-solar" type="button" onclick="openAccAdd()">+ เพิ่มบัญชี</button>
      </div>
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
  <section class="panel" id="panel-aircons">
    <div class="panel-header">
      <div class="panel-title">ล้างแอร์ ({{ $airconTotal }} เครื่อง)</div>
    </div>

    <div class="aircon-shell">
      <div class="aircon-metrics">
        <div class="aircon-metric">
          <div class="aircon-metric-copy">
            <div class="aircon-metric-label">เครื่องทั้งหมด</div>
            <div class="aircon-metric-value" id="aircon-metric-total">{{ $airconTotal }}</div>
          </div>
          <div class="aircon-metric-icon total"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="8" rx="2"/><text x="12" y="10.7" text-anchor="middle" font-size="5" font-weight="900" fill="currentColor" stroke="none">AC</text><path d="M7 16h10"/><path d="M9 19h6"/></svg></div>
        </div>
        <div class="aircon-metric">
          <div class="aircon-metric-copy">
            <div class="aircon-metric-label">ล้างแล้ว</div>
            <div class="aircon-metric-value" id="aircon-metric-cleaned">{{ $airconCleaned }}</div>
          </div>
          <div class="aircon-metric-icon cleaned"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12.5 2.6 2.6L16.5 9"/></svg></div>
        </div>
        <div class="aircon-metric">
          <div class="aircon-metric-copy">
            <div class="aircon-metric-label">ยังไม่ได้ล้าง</div>
            <div class="aircon-metric-value" id="aircon-metric-pending">{{ $airconPending }}</div>
          </div>
          <div class="aircon-metric-icon pending"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m9 9 6 6"/><path d="m15 9-6 6"/></svg></div>
        </div>
      </div>

      <div class="aircon-list-head">
        <div class="aircon-list-title">ประวัติงานล้างแอร์</div>
        <div class="aircon-history-filter">
          <div class="aircon-history-search-row">
            <label class="aircon-history-label" for="aircon-history-search">ค้นหาข้อมูล</label>
            <input id="aircon-history-search" class="aircon-history-search" type="search" placeholder="ค้นหารหัสเครื่อง / ยี่ห้อ / รุ่น / จุดติดตั้ง..." oninput="filterAirconTable(this.value)">
          </div>
          <button class="aircon-add-btn" type="button" onclick="openAirconAdd()">
            <svg viewBox="0 0 24 24"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
            <span>เพิ่มเครื่องแอร์</span>
          </button>
        </div>
      </div>

      @if($aircons->count() === 0)
        <div class="empty-state">ยังไม่มีข้อมูลเครื่องแอร์</div>
      @else
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th style="width:52px">#</th>
                <th>รหัสเครื่อง</th>
                <th>ยี่ห้อ / รุ่นแอร์</th>
                <th>จุดติดตั้ง</th>
                <th>วันที่ล้างล่าสุด</th>
                <th>ล้างครั้งถัดไป</th>
                <th>ผลการล้าง</th>
              </tr>
            </thead>
            <tbody id="aircon-tbody">
              @foreach($aircons as $idx => $ac)
                @php
                  $airconImages = is_array($ac->images) ? array_values(array_filter($ac->images)) : [];
                  if ($ac->cover_image && ! in_array($ac->cover_image, $airconImages, true)) {
                    $airconImages[] = $ac->cover_image;
                  }
                  $airconImageUrl = function ($image) {
                    $image = trim((string) $image);
                    if ($image === '') return null;
                    if (preg_match('/^https?:\/\//i', $image)) return $image;
                    $imagePath = ltrim($image, '/');
                    return preg_match('/^(storage|uploads)\//i', $imagePath) ? asset($imagePath) : asset('storage/'.$imagePath);
                  };
                  $airconImageUrls = collect($airconImages)->map($airconImageUrl)->filter()->values()->all();
                  $airconWashLogs = collect(is_array($ac->wash_logs) ? $ac->wash_logs : [])->map(function ($log) use ($airconImageUrl) {
                    $logDate = $log['date'] ?? $log['service_date'] ?? '';
                    $logStatus = $log['status'] ?? 'pending';
                    $logImages = collect($log['images'] ?? [])->map($airconImageUrl)->filter()->values()->all();
                    return [
                      'date' => $logDate,
                      'next_service_date' => ! empty($log['next_date']) ? $log['next_date'] : ($logDate ? \Carbon\Carbon::parse($logDate)->copy()->addDays(365)->format('Y-m-d') : ''),
                      'status' => $logStatus,
                      'status_text' => $log['status_text'] ?? ($logStatus === 'cleaned' ? 'ล้างแล้ว' : 'ยังไม่ได้ล้าง'),
                      'notes' => $log['notes'] ?? '',
                      'image_urls' => $logImages,
                      'image_count' => count($logImages),
                    ];
                  })->filter(fn ($log) => ! empty($log['date']) || ! empty($log['notes']) || ! empty($log['image_urls']))->sortByDesc('date')->values();
                  $legacyServiceDate = $ac->service_date ?: $ac->updated_at;
                  if ($airconWashLogs->isEmpty()) {
                    $legacyStatus = $ac->status ?? 'pending';
                    $legacyDateText = $legacyServiceDate ? \Carbon\Carbon::parse($legacyServiceDate)->format('Y-m-d') : '';
                    $airconWashLogs = collect([[
                      'date' => $legacyDateText,
                      'next_service_date' => $legacyServiceDate ? \Carbon\Carbon::parse($legacyServiceDate)->copy()->addDays(365)->format('Y-m-d') : '',
                      'status' => $legacyStatus,
                      'status_text' => $legacyStatus === 'cleaned' ? 'ล้างแล้ว' : 'ยังไม่ได้ล้าง',
                      'notes' => $ac->notes ?? '',
                      'image_urls' => $airconImageUrls,
                      'image_count' => count($airconImageUrls),
                    ]]);
                  }
                  $latestAirconLog = $airconWashLogs->first();
                  $status = $latestAirconLog['status'] ?? ($ac->status ?? 'pending');
                  $statusText = $latestAirconLog['status_text'] ?? ($status === 'cleaned' ? 'ล้างแล้ว' : 'ยังไม่ได้ล้าง');
                  $serviceDate = ! empty($latestAirconLog['date']) ? \Carbon\Carbon::parse($latestAirconLog['date']) : $legacyServiceDate;
                  $nextServiceDate = ! empty($latestAirconLog['next_service_date']) ? \Carbon\Carbon::parse($latestAirconLog['next_service_date']) : ($serviceDate ? \Carbon\Carbon::parse($serviceDate)->copy()->addDays(365) : null);
                  $airconSearchBase = strtolower(($ac->aircon_code ?? '').' '.($ac->brand ?? '').' '.($ac->model_name ?? '').' '.($ac->location ?? '').' '.($serviceDate ? $serviceDate->format('Y-m-d') : '').' '.($nextServiceDate ? $nextServiceDate->format('Y-m-d') : ''));
                  $airconPayload = [
                    'id' => $ac->id,
                    'aircon_code' => $ac->aircon_code,
                    'brand' => $ac->brand,
                    'model_name' => $ac->model_name,
                    'location' => $ac->location,
                    'service_date' => $serviceDate ? $serviceDate->format('Y-m-d') : '',
                    'next_service_date' => $nextServiceDate ? $nextServiceDate->format('Y-m-d') : '',
                    'image_count' => count($airconImages),
                    'image_urls' => $airconImageUrls,
                    'history_count' => $airconWashLogs->count(),
                    'wash_logs' => $airconWashLogs->values()->all(),
                    'status' => $status,
                    'status_text' => $statusText,
                    'notes' => $ac->notes ?? '',
                  ];
                @endphp
                <tr data-search="{{ trim($airconSearchBase.' '.strtolower($statusText)) }}" data-search-base="{{ $airconSearchBase }}">
                  <td>{{ $idx + 1 }}</td>
                  <td>
                    <button class="aircon-code-btn" type="button" data-aircon="{{ json_encode($airconPayload, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openAirconHistory(this)">{{ $ac->aircon_code }}</button>
                  </td>
                  <td><strong>{{ $ac->brand }}</strong><div style="font-size:12px;color:var(--muted);font-weight:800">{{ $ac->model_name }}</div></td>
                  <td>{{ $ac->location }}</td>
                  <td>
                    @if($serviceDate)
                      <span class="aircon-date-chip latest">{{ $serviceDate->format('d/m/') }}{{ $serviceDate->year + 543 }}</span>
                    @else
                      <span class="aircon-date-chip empty">-</span>
                    @endif
                  </td>
                  <td>
                    @if($nextServiceDate)
                      <span class="aircon-date-chip next">{{ $nextServiceDate->format('d/m/') }}{{ $nextServiceDate->year + 543 }}</span>
                    @else
                      <span class="aircon-date-chip empty">-</span>
                    @endif
                  </td>
                  <td>
                    <select class="aircon-status-select {{ $status }}" data-aircon-id="{{ $ac->id }}" data-prev="{{ $status }}" onchange="updateAirconStatus(this)">
                      <option value="cleaned" {{ $status === 'cleaned' ? 'selected' : '' }}>ล้างแล้ว</option>
                      <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>ยังไม่ได้ล้าง</option>
                    </select>
                  </td>
                </tr>
              @endforeach
              <tr id="aircon-empty-row" style="display:none">
                <td colspan="7" class="cust-empty-filter">ไม่พบข้อมูลตามคำค้นหา</td>
              </tr>
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </section>
<div class="overlay" id="modal-aircon">
  <div class="aircon-modal" onclick="event.stopPropagation()">
    <div class="aircon-modal-head">
      <div>
        <div class="aircon-modal-title" id="aircon-modal-title">เพิ่มข้อมูลเครื่องแอร์</div>
        <div class="aircon-modal-sub" id="aircon-modal-sub">ล้างแอร์</div>
      </div>
      <button class="aircon-modal-close" type="button" onclick="closeModalById('modal-aircon')">×</button>
    </div>
    <div class="aircon-modal-body">
      <form class="aircon-form-card" id="form-aircon" method="POST" action="{{ route('aircons.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_aircon_form" value="1">
        @if($errors->any() && old('_aircon_form'))
          <div class="aircon-form-error">{{ $errors->first() }}</div>
        @endif
        <div class="aircon-field">
          <label class="aircon-label">รหัสเครื่อง <span class="req">*</span></label>
          <input class="aircon-input" type="text" name="aircon_code" value="{{ old('aircon_code') }}" placeholder="พิมพ์ ID เช่น AC-001" required>
        </div>
        <div class="aircon-field">
          <label class="aircon-label">ยี่ห้อ <span class="req">*</span></label>
          <input class="aircon-input" type="text" name="brand" value="{{ old('brand') }}" placeholder="เช่น Daikin, Mitsubishi, Samsung" required>
        </div>
        <div class="aircon-field">
          <label class="aircon-label">ชื่อรุ่นแอร์ <span class="req">*</span></label>
          <input class="aircon-input" type="text" name="model_name" value="{{ old('model_name') }}" placeholder="เช่น FTKM09SV2S, Inverter 12000BTU" required>
        </div>
        <div class="aircon-field">
          <label class="aircon-label">จุดติดตั้ง <span class="req">*</span></label>
          <input class="aircon-input" type="text" name="location" value="{{ old('location') }}" placeholder="เช่น ชั้น 2 ห้องประชุม" required>
        </div>
        <div class="aircon-field">
          <label class="aircon-label">วันที่ล้าง / ตรวจ <span class="req">*</span></label>
          <input class="aircon-input" type="date" name="service_date" value="{{ old('service_date', now()->toDateString()) }}" required>
        </div>
        <div class="aircon-field">
          <label class="aircon-label">รูปเครื่องแอร์ (แนบได้หลายรูป)</label>
          <div class="aircon-upload-stack">
            <input class="aircon-file" id="aircon-gallery-images" type="file" name="images[]" accept="image/*" multiple>
            <label class="aircon-upload" for="aircon-gallery-images" data-file-label>
              <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
              <span>เลือกจากแกลเลอรี</span>
              <small>เลือกได้หลายรูป</small>
            </label>
          </div>
        </div>
        <div class="aircon-field">
          <label class="aircon-label">สถานะการล้าง <span class="req">*</span></label>
          @php $oldAirconStatus = old('status', 'cleaned'); @endphp
          <div class="aircon-status-group">
            <label class="aircon-status-option"><input type="radio" name="status" value="cleaned" {{ $oldAirconStatus === 'cleaned' ? 'checked' : '' }}><span>ล้างแล้ว</span></label>
            <label class="aircon-status-option"><input type="radio" name="status" value="pending" {{ $oldAirconStatus === 'pending' ? 'checked' : '' }}><span>ยังไม่ได้ล้าง</span></label>
          </div>
        </div>
        <div class="aircon-field">
          <label class="aircon-label">หมายเหตุ</label>
          <textarea class="aircon-note" name="notes" placeholder="รายละเอียดเพิ่มเติม เช่น น้ำหยด / เสียงดัง / ต้องนัดซ่อม">{{ old('notes') }}</textarea>
        </div>
        <div class="aircon-form-actions">
          <button class="aircon-cancel" type="button" onclick="closeModalById('modal-aircon')">ยกเลิก</button>
          <button class="aircon-save" id="aircon-save-btn" type="submit">บันทึกข้อมูล</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="overlay" id="modal-aircon-history">
  <div class="aircon-history-modal" onclick="event.stopPropagation()">
    <div class="aircon-history-head">
      <div>
        <div class="aircon-history-title" id="aircon-history-title">ประวัติการล้างแอร์</div>
        <div class="aircon-history-sub" id="aircon-history-sub">-</div>
      </div>
      <button class="aircon-history-close" type="button" onclick="closeModalById('modal-aircon-history')">&times;</button>
    </div>
    <div class="aircon-history-body">
      <div class="aircon-history-grid">
        <div class="aircon-history-card">
          <div class="aircon-history-label">รหัสเครื่อง</div>
          <div class="aircon-history-value" id="aircon-history-code">-</div>
        </div>
        <div class="aircon-history-card">
          <div class="aircon-history-label">ยี่ห้อ / รุ่น</div>
          <div class="aircon-history-value" id="aircon-history-brand">-</div>
        </div>
        <div class="aircon-history-card">
          <div class="aircon-history-label">จุดติดตั้ง</div>
          <div class="aircon-history-value" id="aircon-history-location">-</div>
        </div>
        <div class="aircon-history-card">
          <div class="aircon-history-label">วันที่ล้าง / ตรวจล่าสุด</div>
          <div class="aircon-history-value" id="aircon-history-date">-</div>
        </div>
      </div>
      <div class="aircon-history-record">
        <div class="aircon-history-label">ประวัติการล้าง</div>
        <div id="aircon-history-records"></div>
      </div>
    </div>
  </div>
</div>
@if($errors->any() && old('_aircon_form'))
  <script>
    document.addEventListener('DOMContentLoaded', () => openModal('modal-aircon'));
  </script>
@endif
  <section class="panel" id="panel-certifications">
    <div class="cert-board">
      <div class="cert-head">
        <div><div class="cert-kicker">CERTIFICATIONS · {{ $certGroups->count() }} UNIQUE</div><div class="cert-title">รวมใบรับรององค์กร</div><div class="cert-sub">ใบรับรองวิชาชีพรวม {{ $certTotal }} ฉบับ</div></div>
      </div>
      <div class="cert-grid" id="cert-grid">
        @forelse($certGroups as $certName => $items)
          @php
            $abbrs = $items->map(fn($item) => mb_substr(preg_split('/\s+/u', trim($item['tech']->emp_name ?: $item['tech']->emp_id))[0] ?? ($item['tech']->emp_name ?: $item['tech']->emp_id), 0, 2))->unique()->take(4)->values();
            $payload = $items->map(fn($item) => ['tech' => $item['tech'], 'license' => $item['license'], 'license_index' => $item['license_index']])->values();
          @endphp
          <button class="cert-card" type="button" data-cert-name="{{ $certName }}" data-cert-items="{{ json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openCertDetail(this)">
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
<!-- === CODEX CERTIFICATIONS DETAIL MODAL START === -->
<div class="overlay" id="cert-detail-overlay">
  <div class="cert-modal" onclick="event.stopPropagation()">
    <div class="cert-modal-head">
      <button class="cert-detail-close-btn" type="button" onclick="closeCertDetail()">&times;</button>
      <div class="cert-modal-kicker">CERTIFICATE DETAIL</div>
      <div class="cert-modal-title" id="cert-detail-title">-</div>
      <div class="cert-modal-sub" id="cert-detail-sub">0 &#x0E04;&#x0E19;&#x0E43;&#x0E19;&#x0E2D;&#x0E07;&#x0E04;&#x0E4C;&#x0E01;&#x0E23;</div>
    </div>
    <div class="cert-holder-list" id="cert-holder-list"></div>
  </div>
</div>
<!-- === CODEX CERTIFICATIONS DETAIL MODAL END === -->
<div class="overlay" id="overlay">
  <div class="pmodal profile-v2" onclick="event.stopPropagation()">
    <div class="profile-v2-layout">
      <div class="profile-v2-left">
        <button class="pv2-close-btn" type="button" onclick="closeModalById('overlay')">×</button>
        <div class="profile-v2-photo"><img id="m-img" src="" alt="" style="display:none"><span id="m-initial">3E</span></div>
        <div class="profile-v2-name" id="m-name"></div>
        <div class="profile-v2-status pv2-status-active" id="m-status"><span class="pv2-st-dot pv2-dot-active" id="m-st-dot"></span><span id="m-st-text">พร้อมทำงาน</span></div>
       <div class="profile-v2-rolecard">
  <div class="pv2-rolerow">
    <span class="pv2-rolekey" >ชื่ออังกฤษ :</span><span class="pv2-roleval profile-v2-nameeng" id="m-name-eng">-</span>
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
    <div class="pv2-section pv2-profile-summary">
      <div class="pv2-section-label">ทักษะและความสามารถ</div>
      <div class="pv2-combined-grid">
        <div class="pv2-combined-group">
          <div class="pv2-sub-label">ทักษะ</div>
          <div class="pv2-tags" id="m-skills"></div>
        </div>
        <div class="pv2-combined-group">
          <div class="pv2-sub-label">Software & Tools</div>
          <div class="pv2-tags" id="m-software"></div>
        </div>
        <div class="pv2-combined-group pv2-combined-wide">
          <div class="pv2-sub-label">Core Competencies</div>
          <div class="pv2-comp-grid" id="m-competencies"></div>
        </div>
      </div>
    </div>
    <div class="pv2-section pv2-license-section"><div class="pv2-section-label">Licenses & Experience</div><div id="m-licenses"></div></div>
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
          <div class="sched-form-section sched-full">ข้อมูลงาน</div>
          <div class="frow sched-third"><label class="flabel">ประเภทงาน *</label><select class="finput" name="job_type" id="add-job_type" required><option value="">-- เลือกประเภท --</option>@foreach($jobTypes as $key=>$label)<option value="{{ $key }}" {{ old('job_type')===$key?'selected':'' }}>{{ $label }}</option>@endforeach</select></div>
          <div class="frow sched-third"><label class="flabel">สถานะ</label><select class="finput sched-status-input" name="status" id="add-status"><option value="">อัตโนมัติตามวันที่</option><option value="upcoming" {{ old('status')==='upcoming'?'selected':'' }}>กำลังจะมา</option><option value="doing" {{ old('status')==='doing'?'selected':'' }}>กำลังทำ</option><option value="done" {{ old('status')==='done'?'selected':'' }}>เสร็จแล้ว</option><option value="cancel" {{ old('status')==='cancel'?'selected':'' }}>ยกเลิก</option></select></div>
          <div class="frow sched-third"><label class="flabel">เลข SO *</label><input class="finput" type="text" name="so_number" value="{{ old('so_number') }}" required placeholder="SO-2026-001"></div>
          <div class="sched-form-section sched-full">ข้อมูลลูกค้า</div>
          <div class="frow sched-full autocomp"><label class="flabel">ชื่อลูกค้า *</label><input class="finput" type="text" name="customer_name" id="add-customer_name" value="{{ old('customer_name') }}" required autocomplete="off" placeholder="พิมพ์ชื่อลูกค้า..." oninput="custAutocomp(this.value,'add')" onkeydown="custAutocompKey(event,'add')"><div class="autocomp-list" id="add-ac-list"></div><div class="cust-banner cust-banner-old" id="add-cust-banner"></div></div>
          <div class="frow" id="add-ncf-1" style="display:none"><label class="flabel">รายละเอียดโครงการ</label><input class="finput" type="text" name="cust_desc"></div>
          <div class="frow" id="add-ncf-2" style="display:none"><label class="flabel">ชื่อผู้ติดต่อ</label><input class="finput" type="text" name="cust_contact_name"></div>
          <div class="frow" id="add-ncf-3" style="display:none"><label class="flabel">เบอร์โทรลูกค้า</label><input class="finput" type="text" name="cust_phone"></div>
          <div class="frow" id="add-ncf-4" style="display:none"><label class="flabel">ขนาดติดตั้ง</label><input class="finput" type="text" name="cust_size"></div>
          <div class="sched-form-section sched-full">ทีมและสถานที่</div>
          <div class="frow"><label class="flabel">ทีมที่รับผิดชอบ *</label><select class="finput" name="team_name" id="add-team_name" required onchange="TL.onTeamChange('add')"><option value="">-- เลือกทีม --</option>@foreach($teams as $t)@php $tn = data_get($t, 'team_name', ''); @endphp<option value="{{ $tn }}" {{ old('team_name')===$tn?'selected':'' }}>{{ $tn }}</option>@endforeach</select></div>
          <div class="frow"><label class="flabel">ชื่องาน *</label><input class="finput" type="text" name="job_title" value="{{ old('job_title') }}" required></div>
          <div class="frow sched-full"><label class="flabel">สถานที่</label><input class="finput" type="text" name="job_location" id="add-job_location" value="{{ old('job_location') }}"></div>
          <div class="frow sched-full"><label class="flabel">ละติจูด,ลองจิจูด</label><div class="sched-map-picker"><div class="sched-map-toolbar"><input class="finput" type="text" name="job_la_long" id="add-job_la_long" value="{{ old('job_la_long') }}" oninput="scheduleMapInputChanged('add')" onchange="showGoogleScheduleMap('add')"><button class="btn btn-ghost sched-map-btn" type="button" onclick="openScheduleGoogleMap('add')">Google Map</button></div><div class="sched-map-hint">ใส่พิกัดรูปแบบ ละติจูด,ลองจิจูด แล้ว Google Maps จะแสดงตำแหน่งนั้น</div><div class="sched-map" id="add-map-picker"></div></div></div>
          <div class="sched-form-section sched-full">ช่วงวันที่และหมายเหตุ</div>
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
          <div class="sched-form-section sched-full">ข้อมูลงาน</div>
          <div class="frow sched-third"><label class="flabel">เลข SO *</label><input class="finput" type="text" name="so_number" id="es-so_number" required></div>
          <div class="frow sched-third"><label class="flabel">ชื่อลูกค้า *</label><input class="finput" type="text" name="customer_name" id="es-customer_name" required></div>
          <div class="frow sched-third"><label class="flabel">ประเภทงาน</label><select class="finput" name="job_type" id="es-job_type"><option value="">-- เลือกประเภท --</option>@foreach($jobTypes as $key=>$label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
          <div class="frow"><label class="flabel">สถานะ</label><select class="finput sched-status-input" name="status" id="es-status"><option value="">อัตโนมัติตามวันที่</option><option value="upcoming">กำลังจะมา</option><option value="doing">กำลังทำ</option><option value="done">เสร็จแล้ว</option><option value="cancel">ยกเลิก</option></select></div>
          <div class="frow"><label class="flabel">ชื่องาน *</label><input class="finput" type="text" name="job_title" id="es-job_title" required></div>
          <div class="sched-form-section sched-full">ทีมและสถานที่</div>
          <div class="frow"><label class="flabel">ทีม *</label><select class="finput" name="team_name" id="es-team_name" required onchange="TL.onTeamChange('es')"><option value="">-- เลือกทีม --</option>@foreach($teams as $t)@php $tn = data_get($t, 'team_name', ''); @endphp<option value="{{ $tn }}">{{ $tn }}</option>@endforeach</select></div>
          <div class="frow"><label class="flabel">สถานที่</label><input class="finput" type="text" name="job_location" id="es-job_location"></div>
          <div class="frow sched-full"><label class="flabel">ละติจูด,ลองจิจูด</label><div class="sched-map-picker"><div class="sched-map-toolbar"><input class="finput" type="text" name="job_la_long" id="es-job_la_long" oninput="scheduleMapInputChanged('es')" onchange="showGoogleScheduleMap('es')"><button class="btn btn-ghost sched-map-btn" type="button" onclick="openScheduleGoogleMap('es')">Google Map</button></div><div class="sched-map-hint">ใส่พิกัดรูปแบบ ละติจูด,ลองจิจูด แล้ว Google Maps จะแสดงตำแหน่งนั้น</div><div class="sched-map" id="es-map-picker"></div></div></div>
          <div class="sched-form-section sched-full">ช่วงวันที่และหมายเหตุ</div>
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

<div class="cal-popup-bg" id="cal-popup-bg">
  <div class="cal-popup" onclick="event.stopPropagation()">
    <div class="cal-popup-strip"></div>
    <div class="cal-popup-head">
      <div class="cal-popup-date" id="cal-popup-date">-</div>
      <span class="cal-popup-count" id="cal-popup-count">0 งาน</span>
      <button class="cal-popup-close" type="button" onclick="closeScheduleDayPopup()">×</button>
    </div>
    <div class="cal-popup-inner" id="cal-popup-body"></div>
  </div>
</div>

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
                    <th style="width:150px">&#3648;&#3621;&#3586;&#3591;&#3634;&#3609; (SO)</th>
                    <th>&#3594;&#3639;&#3656;&#3629;&#3621;&#3641;&#3585;&#3588;&#3657;&#3634;</th>
                    <th>&#3619;&#3634;&#3618;&#3621;&#3632;&#3648;&#3629;&#3637;&#3618;&#3604;&#3591;&#3634;&#3609;</th>
                    <th style="width:200px">&#3623;&#3633;&#3609;&#3607;&#3637;&#3656;&#3607;&#3635;&#3591;&#3634;&#3609;</th>
                    <th style="width:120px">&#3626;&#3606;&#3634;&#3609;&#3632;&#3591;&#3634;&#3609;</th>
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
const URL_SCHED_STATUS = (id) => `/schedules/${encodeURIComponent(id)}/status`;
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
window.SCHED_DATA = SCHED_DATA;
window.JOB_TYPES = JOB_TYPES;
const SCHED_STATUS_OPTIONS = {
  upcoming: 'กำลังจะมา',
  doing: 'กำลังทำ',
  done: 'เสร็จแล้ว',
  cancel: 'ยกเลิก',
};
const SCHED_STATUS_CLASSES = {
  upcoming: 'sls-upcoming',
  doing: 'sls-doing',
  done: 'sls-done',
  cancel: 'sls-cancel',
};
function schedStatusKey(v) {
  const s = String(v || '').trim();
  const aliases = {
    'กำลังจะมา': 'upcoming',
    'รอดำเนินการ': 'upcoming',
    'กำลังทำ': 'doing',
    'กำลังดำเนินการ': 'doing',
    'เสร็จแล้ว': 'done',
    'เสร็จสิ้น': 'done',
    'ยกเลิก': 'cancel',
  };
  return SCHED_STATUS_OPTIONS[s] ? s : (aliases[s] || '');
}
function schedDateKey(v) {
  if (!v) return '';
  const s = String(v).trim();
  const m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
  if (m) return `${m[1]}-${m[2]}-${m[3]}`;
  const d = new Date(s);
  return isNaN(d) ? '' : ymd(d);
}
function dateLinkedScheduleStatusKey(job) {
  const start = schedDateKey(job?.start_date);
  const end = schedDateKey(job?.end_date || job?.start_date);
  const today = ymd(new Date());
  if (end && end < today) return 'done';
  if (start && start > today) return 'upcoming';
  if (start || end) return 'doing';
  return '';
}
function resolveScheduleStatus(job) {
  const explicit = schedStatusKey(job?.status);
  const key = explicit || dateLinkedScheduleStatusKey(job) || 'upcoming';
  return {
    key,
    label: SCHED_STATUS_OPTIONS[key] || SCHED_STATUS_OPTIONS.upcoming,
    cls: SCHED_STATUS_CLASSES[key] || SCHED_STATUS_CLASSES.upcoming,
  };
}
function schedStatusOptionsHtml(active) {
  return Object.entries(SCHED_STATUS_OPTIONS)
    .map(([key, label]) => `<option value="${key}" ${active === key ? 'selected' : ''}>${label}</option>`)
    .join('');
}
function teamEventClass(job) {
  const type = String(job?.job_type || 'general');
  const map = {
    solar_install: 'evc-install',
    solar_wash: 'evc-wash',
    solar_maintenance: 'evc-maintenance',
    electrical: 'evc-electrical',
    civil: 'evc-civil',
    general: 'evc-general',
  };
  return map[type] || 'evc-general';
}
function renderCalendarEventContent(job) {
  const typeKey = job?.job_type || 'general';
  const typeLabel = JOB_TYPES[typeKey] || typeKey || '\u0E07\u0E32\u0E19\u0E17\u0E31\u0E48\u0E27\u0E44\u0E1B';
  const title = job?.job_title || job?.so_number || '-';
  const customer = job?.customer_name || '-';
  return `<span class="sched-event-title">${escHtml(title)}</span><span class="sched-event-meta"><span class="sched-event-type">${escHtml(typeLabel)}</span><span class="sched-event-customer">${escHtml(customer)}</span></span>`;
}
function scheduleJobsForDate(dateStr,source='main'){
  const pool=source==='team'&&window.TCAL?.jobs?window.TCAL.jobs:SCHED_DATA;
  const filterId=source==='team'?'tcal-type-filter':'sched-type-filter';
  const filter=document.getElementById(filterId)?.value||'all';
  return (pool||[])
    .filter(s=>s.start_date<=dateStr&&s.end_date>=dateStr)
    .map(s=>({...s,job_type:s.job_type||'general'}))
    .filter(s=>filter==='all'||s.job_type===filter)
    .sort((a,b)=>(a.start_date||'').localeCompare(b.start_date||'')||(a.so_number||'').localeCompare(b.so_number||''));
}
function closeScheduleDayPopup(){
  document.getElementById('cal-popup-bg')?.classList.remove('open');
  if(!document.querySelector('.overlay.open,.tcal-overlay.open,.cal-popup-bg.open'))document.body.style.overflow='';
}
function openScheduleFromDayPopup(btn,source='main'){
  closeScheduleDayPopup();
  if(source==='team'&&document.getElementById('tcal-overlay')?.classList.contains('open')){
    closeTeamCalendar();
    setTimeout(()=>openEditSchedFromEl(btn),100);
    return;
  }
  openEditSchedFromEl(btn);
}
function openScheduleEditFromCalendar(event,btn,source='main'){
  if(event){
    event.preventDefault();
    event.stopPropagation();
  }
  if(!btn)return false;
  closeScheduleDayPopup();
  if(source==='team'&&document.getElementById('tcal-overlay')?.classList.contains('open')){
    closeTeamCalendar();
    setTimeout(()=>openEditSchedFromEl(btn),100);
    return false;
  }
  openEditSchedFromEl(btn);
  return false;
}
function openScheduleDayPopup(dateStr,source='main'){
  const popup=document.getElementById('cal-popup-bg'),body=document.getElementById('cal-popup-body');
  if(!popup||!body)return;
  const jobs=scheduleJobsForDate(dateStr,source);
  const dateEl=document.getElementById('cal-popup-date'),countEl=document.getElementById('cal-popup-count');
  if(dateEl)dateEl.textContent=fmtDate(dateStr);
  if(countEl)countEl.textContent=`${jobs.length} งาน`;
  body.innerHTML=jobs.length?jobs.map(s=>{
    const typeKey=s.job_type||'general';
    const typeLabel=JOB_TYPES[typeKey]||typeKey||'งานทั่วไป';
    const dateText=s.start_date===s.end_date?fmtDate(s.start_date):`${fmtDate(s.start_date)} - ${fmtDate(s.end_date)}`;
    return `<button type="button" class="cal-ev-card ${teamEventClass(s)}" data-sched-source="${escHtml(source)}" data-sched-id="${escHtml(s.id||'')}" data-sched='${JSON.stringify(s).replace(/'/g,"&#39;")}' onclick="return openScheduleEditFromCalendar(event,this,'${source}')">
      <div class="cal-ev-top"><span class="cal-so">${escHtml(s.so_number||'-')}</span><span class="job-type-tag jt-${escHtml(typeKey)}">${escHtml(typeLabel)}</span></div>
      <div class="cal-ev-cust">${escHtml(s.customer_name||'-')}</div>
      <div class="cal-ev-job">${escHtml(s.job_title||'-')}</div>
      <div class="cal-ev-meta"><span class="cal-ev-ml">ทีม</span><span class="cal-ev-mv">${escHtml(s.team_name||'-')}</span><span class="cal-ev-ml">วันที่</span><span class="cal-ev-mv">${escHtml(dateText)}</span></div>
    </button>`;
  }).join(''):'<div class="empty-state">ไม่มีงานในวันนี้</div>';
  popup.classList.add('open');
  document.body.style.overflow='hidden';
}
function escHtml(s){return String(s??'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}
function fmtDate(d){if(!d)return '-';const dt=new Date(d);if(isNaN(dt))return d;return `${dt.getDate()}/${dt.getMonth()+1}/${dt.getFullYear()+543}`}
function fmtDateCE(d){if(!d)return '-';const dt=new Date(d);if(isNaN(dt))return d;return `${String(dt.getDate()).padStart(2,'0')}/${String(dt.getMonth()+1).padStart(2,'0')}/${dt.getFullYear()}`}
function airconNextDueText(d){if(!d)return '';const dt=new Date(d);if(isNaN(dt))return '';const target=new Date(dt.getFullYear(),dt.getMonth(),dt.getDate());const now=new Date();const today=new Date(now.getFullYear(),now.getMonth(),now.getDate());const diff=Math.ceil((target-today)/86400000);if(diff>0)return `(อีก ${diff.toLocaleString('en-US')} วัน)`;if(diff===0)return '(วันนี้)';return `(เลยกำหนด ${Math.abs(diff).toLocaleString('en-US')} วัน)`}
function ymd(d){const dt=(d instanceof Date)?d:new Date(d);return `${dt.getFullYear()}-${String(dt.getMonth()+1).padStart(2,'0')}-${String(dt.getDate()).padStart(2,'0')}`}
function daysBetween(a,b){return Math.round((new Date(b)-new Date(a))/86400000)}
function normalizeDate(v){return v?ymd(v):''}
function getCategory(type){if(!type)return 'general';if(String(type).startsWith('solar'))return 'solar';if(type==='electrical')return 'electrical';if(type==='civil')return 'civil';return 'general'}
const DASHBOARD_TABS = ['teams','schedules','customers','accounts','certifications','aircons'];
function rememberDashboardTab(tab){
  if (!DASHBOARD_TABS.includes(tab)) return;
  const url = new URL(window.location.href);
  url.searchParams.set('tab', tab);
  window.history.replaceState(null, '', url.toString());
}
function switchTab(tab,el){document.querySelectorAll('.panel').forEach(p=>p.classList.remove('active'));document.querySelectorAll('.sb-tab').forEach(t=>t.classList.remove('active'));document.getElementById('panel-'+tab)?.classList.add('active');el?.classList.add('active');rememberDashboardTab(tab);if(tab==='schedules')SCHED_BOARD.render();if(innerWidth<=768)document.querySelector('.sidebar')?.classList.remove('open')}
function closeModalById(id){document.getElementById(id)?.classList.remove('open');if(!document.querySelector('.overlay.open,.tcal-overlay.open,.cal-popup-bg.open'))document.body.style.overflow=''}
document.addEventListener('click',e=>{if(e.target.classList?.contains('overlay')||e.target.id==='cal-popup-bg'){e.preventDefault();e.stopPropagation()}})
document.addEventListener('keydown',e=>{if(e.key==='Escape'){if(document.getElementById('tcal-overlay')?.classList.contains('open'))closeTeamCalendar();document.querySelectorAll('.overlay.open,.cal-popup-bg.open').forEach(el=>el.classList.remove('open'));document.body.style.overflow=''}})
function filterTable(tbodyId,q){const kw=(q||'').toLowerCase().trim();document.querySelectorAll('#'+tbodyId+' tr[data-search]').forEach(r=>r.style.display=(!kw||(r.dataset.search||'').includes(kw))?'':'none')}
function filterAirconTable(q){const kw=(q||'').toLowerCase().trim();const rows=Array.from(document.querySelectorAll('#aircon-tbody tr[data-search]'));let shown=0;rows.forEach(r=>{const visible=!kw||(r.dataset.search||'').includes(kw);r.style.display=visible?'':'none';if(visible)shown++});const empty=document.getElementById('aircon-empty-row');if(empty)empty.style.display=rows.length&&shown===0?'':'none'}
function airconStatusLabel(status){return status==='cleaned'?'ล้างแล้ว':'ยังไม่ได้ล้าง'}
function cleanAirconHistoryNotes(value){
  return String(value||'')
    .split(/\r?\n/)
    .map(line=>line.trim())
    .filter(line=>line && !(/^นำเข้าจาก\s*CSV\s*แอร์/i.test(line)||/^วันที่บันทึก\s*:/i.test(line)||/^รอบล้างถัดไป\s*:/i.test(line)||/^จำนวนรูป\s*:/i.test(line)))
    .join('\n');
}
function airconStorageUrl(src){
  const value=String(src||'').trim();
  if(!value)return '';
  if(/^(https?:)?\/\//i.test(value)||/^(data|blob):/i.test(value))return value;
  const base=window.location.pathname.split('/dashboardtechnician')[0]||'';
  if(value.startsWith('/storage/')||value.startsWith('/uploads/'))return `${base}${value}`;
  if(value.startsWith('/'))return `${base}${value}`;
  return `${base}/storage/${value.replace(/^\/+/,'')}`;
}
function airconWashGalleryHtml(images){
  const urls=(Array.isArray(images)?images:[]).map(airconStorageUrl).filter(Boolean);
  if(!urls.length)return '<div class="aircon-wash-gallery-empty">ไม่มีรูปแนบ</div>';
  return `<div class="aircon-wash-gallery">${urls.map((url,index)=>`<a href="${escHtml(url)}" target="_blank" rel="noopener"><img src="${escHtml(url)}" alt="รูปประวัติการล้าง ${index+1}" loading="lazy"></a>`).join('')}</div>`;
}
function openAirconHistory(btn){
  if(!btn?.dataset.aircon)return;
  let data={};
  try{data=JSON.parse(btn.dataset.aircon||'{}')}catch(e){data={}}
  const row=btn.closest('tr');
  const statusSelect=row?.querySelector('.aircon-status-select');
  if(statusSelect){
    data.status=statusSelect.value||data.status||'pending';
    data.status_text=airconStatusLabel(data.status);
  }
  const set=(id,val)=>{const el=document.getElementById(id);if(el)el.textContent=(val==null||val==='')?'-':val};
  const brandModel=[data.brand,data.model_name].filter(Boolean).join(' / ');
  const dateText=data.service_date?fmtDate(data.service_date):'-';
  const status=data.status||'pending';
  const statusClass=status==='cleaned'?'cleaned':'pending';
  const statusText=data.status_text||airconStatusLabel(statusClass);
  set('aircon-history-title',`ประวัติการล้างแอร์ ${data.aircon_code||''}`.trim());
  set('aircon-history-sub',brandModel||'-');
  set('aircon-history-code',data.aircon_code);
  set('aircon-history-brand',brandModel);
  set('aircon-history-location',data.location);
  set('aircon-history-date',dateText);
  const records=document.getElementById('aircon-history-records');
  if(records){
    const recordTitle=`${data.aircon_code||'-'} · ${data.brand||'-'} ${data.model_name||'-'}`;
    const fallbackImages=Array.isArray(data.image_urls)?data.image_urls:(Array.isArray(data.images)?data.images:[]);
    const logs=(Array.isArray(data.wash_logs)&&data.wash_logs.length?data.wash_logs:[{
      date:data.service_date,
      next_service_date:data.next_service_date,
      status:data.status,
      status_text:data.status_text,
      notes:data.notes,
      image_urls:fallbackImages,
      image_count:data.image_count
    }]);
    records.innerHTML=logs.map((log,index)=>{
      const isLatest=index===0;
      const logStatus=isLatest?(data.status||log.status||'pending'):(log.status||'pending');
      const logStatusClass=logStatus==='cleaned'?'cleaned':'pending';
      const logStatusText=(isLatest?data.status_text:null)||log.status_text||airconStatusLabel(logStatus);
      const logDate=log.date||log.service_date||'';
      const latestDate=logDate?fmtDateCE(logDate):'-';
      const nextRaw=log.next_service_date||log.next_date||(isLatest?data.next_service_date:'');
      const nextDate=nextRaw?fmtDateCE(nextRaw):'-';
      const nextDue=nextRaw?airconNextDueText(nextRaw):'';
      const imageUrls=Array.isArray(log.image_urls)?log.image_urls:(Array.isArray(log.images)?log.images:[]);
      const imageCount=Number(log.image_count||imageUrls.length||0);
      const cleanNotes=cleanAirconHistoryNotes(log.notes||'')||'-';
      const roundNumber=logs.length-index;
      const title=logs.length>1?`${recordTitle} · ครั้งที่ ${roundNumber}`:recordTitle;
      return `<div class="aircon-wash-card">
      <div class="aircon-wash-top">
        <div>
          <div class="aircon-wash-title">${escHtml(title)}</div>
          <div class="aircon-wash-place"><span class="aircon-wash-pin"></span><span>${escHtml(data.location||'-')}</span></div>
        </div>
        <span class="aircon-wash-status ${escHtml(logStatusClass)}">${escHtml(logStatusText)}</span>
      </div>
      <div class="aircon-next-strip"><span class="aircon-next-mark"></span><span>รอบถัดไป ${escHtml(nextDate)} ${escHtml(nextDue)}</span></div>
      <div class="aircon-wash-meta">
        <span><i class="aircon-meta-icon">1</i> ล้างล่าสุด ${escHtml(latestDate)}</span>
        <span><i class="aircon-meta-icon"></i> ${imageCount.toLocaleString('en-US')} รูป</span>
        <span><i class="aircon-meta-icon"></i> ${roundNumber.toLocaleString('en-US')} ครั้ง</span>
      </div>
      <div class="aircon-wash-note">
        <div class="aircon-wash-note-label">หมายเหตุ</div>
        <div class="aircon-wash-note-text">${escHtml(cleanNotes)}</div>
      </div>
      <div class="aircon-wash-gallery-wrap">
        <div class="aircon-wash-gallery-label">รูปภาพ</div>
        ${airconWashGalleryHtml(imageUrls)}
      </div>
    </div>`;
    }).join('');
  }
  openModal('modal-aircon-history');
}
window.openAirconHistory=openAirconHistory;
function setAirconStatusClass(sel,status){sel.classList.remove('cleaned','pending');sel.classList.add(status)}
function updateAirconMetric(id,value){const el=document.getElementById(id);if(el&&value!=null)el.textContent=value}
async function updateAirconStatus(sel){
  const id=sel.dataset.airconId,prev=sel.dataset.prev||'pending',status=sel.value;
  if(!id)return;
  sel.disabled=true;
  setAirconStatusClass(sel,status);
  try{
    const base=window.location.pathname.split('/dashboardtechnician')[0]||'';
    const res=await fetch(`${base}/aircons/${encodeURIComponent(id)}/status`,{
      method:'POST',
      headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF},
      body:JSON.stringify({status})
    });
    if(!res.ok)throw new Error('aircon status update failed');
    const data=await res.json();
    const finalStatus=data.status||status,finalLabel=data.label||airconStatusLabel(finalStatus);
    sel.value=finalStatus;sel.dataset.prev=finalStatus;setAirconStatusClass(sel,finalStatus);
    const row=sel.closest('tr');
    if(row)row.dataset.search=`${row.dataset.searchBase||''} ${finalLabel}`.toLowerCase().trim();
    updateAirconMetric('aircon-metric-total',data.counts?.total);
    updateAirconMetric('aircon-metric-cleaned',data.counts?.cleaned);
    updateAirconMetric('aircon-metric-pending',data.counts?.pending);
  }catch(err){
    sel.value=prev;setAirconStatusClass(sel,prev);
    alert('บันทึกสถานะไม่สำเร็จ');
  }finally{
    sel.disabled=false;
  }
}
document.addEventListener('change',e=>{if(!e.target.matches('#modal-aircon .aircon-file'))return;const input=e.target;const label=document.querySelector(`label[for="${input.id}"]`);const text=label?.querySelector('span');if(!text||!input.files||input.files.length===0)return;text.textContent=input.files.length===1?input.files[0].name:`เลือกแล้ว ${input.files.length} รูป`});
let TEAM_FILTER_SEARCH = '';
let TEAM_FILTER_SKILL = 'all';
function applyTeamFilters(){
  let shown=0;
  let total=0;
  document.querySelectorAll('#team-grid-wrap .team-card').forEach(card=>{
    total++;
    const haystack=`${card.dataset.search||''} ${card.textContent||''}`.toLowerCase();
    const skills=(card.dataset.skill||'').toLowerCase();
    const searchOk=!TEAM_FILTER_SEARCH||haystack.includes(TEAM_FILTER_SEARCH);
    const skillOk=TEAM_FILTER_SKILL==='all'||skills.includes(TEAM_FILTER_SKILL);
    const visible=searchOk&&skillOk;
    card.style.display=visible?'':'none';
    if(visible)shown++;
  });
  const empty=document.getElementById('team-empty-filter');
  if(empty)empty.style.display=total&&shown===0?'':'none';
}
function filterTeams(q){TEAM_FILTER_SEARCH=(q||'').toLowerCase().trim();const search=document.getElementById('team-name-search');if(search&&search.value!==q)search.value=q||'';applyTeamFilters()}
function filterTeamSearch(q){filterTeams(q)}
function filterTeamSkill(skill){TEAM_FILTER_SKILL=(skill||'all').toLowerCase().trim()||'all';const select=document.getElementById('team-skill-filter');if(select&&select.value!==skill)select.value=skill||'all';applyTeamFilters()}
function switchViewTab(tab,btn){document.querySelectorAll('.view-tabs .dtab').forEach(b=>b.classList.remove('active'));btn.classList.add('active');document.getElementById('view-all').style.display=tab==='all'?'':'none';document.getElementById('view-team').style.display=tab==='team'?'':'none'}
function showTeamRosterView(){
  document.querySelectorAll('.panel').forEach(p=>p.classList.remove('active'));
  document.getElementById('panel-teams')?.classList.add('active');
  document.querySelectorAll('.sb-tab').forEach(t=>t.classList.remove('active'));
  document.querySelector('.sb-tab[onclick*="teams"]')?.classList.add('active');

  const allView=document.getElementById('view-all');
  const teamView=document.getElementById('view-team');
  if(allView)allView.style.display='none';
  if(teamView)teamView.style.display='';

  const viewTabs=Array.from(document.querySelectorAll('.view-tabs .dtab'));
  viewTabs.forEach(b=>b.classList.remove('active'));
  const teamTab=viewTabs.find(b=>(b.getAttribute('onclick')||'').includes("'team'"))||viewTabs[1];
  teamTab?.classList.add('active');

  if(innerWidth<=768)document.querySelector('.sidebar')?.classList.remove('open');
}

const TL=(()=>{const state={};function init(prefix){if(state[prefix])return state[prefix];const t=new Date();return state[prefix]={year:t.getFullYear(),month:t.getMonth(),start:null,end:null,team:'',busyDays:{},isDragging:false,editingId:null}}function getTeamSchedules(team){return team?SCHED_DATA.filter(s=>s.team_name===team):[]}function buildBusyDays(prefix,excludeId){const st=state[prefix];st.busyDays={};getTeamSchedules(st.team).forEach(s=>{if(excludeId&&String(s.id)===String(excludeId))return;let d=new Date(s.start_date),end=new Date(s.end_date);while(d<=end){const k=ymd(d);st.busyDays[k]=(st.busyDays[k]||0)+1;d.setDate(d.getDate()+1)}})}function onTeamChange(prefix){const st=init(prefix),sel=document.getElementById(prefix==='add'?'add-team_name':'es-team_name');st.team=sel?.value||'';const info=document.getElementById(prefix+'-tl-team-info');if(info){if(st.team){info.classList.remove('no-team');info.innerHTML=`ทีม <strong>${escHtml(st.team)}</strong> มีงาน ${getTeamSchedules(st.team).length} งาน`}else{info.classList.add('no-team');info.textContent='เลือกทีมก่อนเพื่อดูวันที่ทีมว่าง'}}buildBusyDays(prefix,prefix==='es'?state.es?.editingId:null);render(prefix)}function gotoToday(prefix){const st=init(prefix),t=new Date();st.year=t.getFullYear();st.month=t.getMonth();render(prefix)}function clear(prefix){const st=init(prefix);st.start=null;st.end=null;syncHidden(prefix);render(prefix)}function nav(prefix,dir){const st=init(prefix);st.month+=dir;if(st.month<0){st.month=11;st.year--}if(st.month>11){st.month=0;st.year++}render(prefix)}function selectDate(prefix,dateStr){const st=init(prefix);if(!st.start||(st.start&&st.end)){st.start=dateStr;st.end=null}else if(dateStr<st.start){st.end=st.start;st.start=dateStr}else st.end=dateStr;syncHidden(prefix);render(prefix)}function startDrag(prefix,dateStr){const st=init(prefix);st.isDragging=true;st.start=dateStr;st.end=dateStr;syncHidden(prefix);render(prefix)}function dragOver(prefix,dateStr){const st=init(prefix);if(!st.isDragging)return;if(dateStr<st.start){st.end=st.start;st.start=dateStr}else st.end=dateStr;render(prefix)}function endDrag(prefix){const st=init(prefix);st.isDragging=false;syncHidden(prefix)}function syncHidden(prefix){const st=state[prefix],form=document.getElementById(prefix==='add'?'form-add-sched':'form-edit-sched');if(!st||!form)return;let s=form.querySelector('input[name="start_date"]'),e=form.querySelector('input[name="end_date"]');if(!s){s=document.createElement('input');s.type='hidden';s.name='start_date';form.appendChild(s)}if(!e){e=document.createElement('input');e.type='hidden';e.name='end_date';form.appendChild(e)}s.value=st.start||'';e.value=st.end||st.start||''}function render(prefix){const st=init(prefix),months=['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'],hdr=['อา','จ','อ','พ','พฤ','ศ','ส'];const mname=document.getElementById(prefix+'-tl-mname');if(mname)mname.textContent=`${months[st.month]} ${st.year+543}`;function renderMonth(yr,mo,side){document.getElementById(`${prefix}-tl-mname-${side}`).textContent=`${months[mo]} ${yr+543}`;document.getElementById(`${prefix}-tl-dhdrs-${side}`).innerHTML=hdr.map((d,i)=>`<div class="tl-dhdr ${i===0||i===6?'weekend':''}">${d}</div>`).join('');const grid=document.getElementById(`${prefix}-tl-grid-${side}`);const first=new Date(yr,mo,1).getDay(),days=new Date(yr,mo+1,0).getDate(),today=ymd(new Date());let html='';for(let i=0;i<first;i++)html+='<div class="tl-cell tl-other"></div>';for(let d=1;d<=days;d++){const ds=`${yr}-${String(mo+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`,busy=st.busyDays[ds]||0,cls=['tl-cell'];if(ds===today)cls.push('tl-today');if(busy)cls.push('tl-busy');if(st.start&&ds===st.start)cls.push('tl-sel-s');if(st.end&&ds===st.end)cls.push('tl-sel-e');if(st.start&&st.end&&ds>st.start&&ds<st.end)cls.push('tl-in-range');html+=`<div class="${cls.join(' ')}" data-date="${ds}" ${busy?'':`onmousedown="TL.startDrag('${prefix}','${ds}');event.preventDefault()" onmouseenter="TL.dragOver('${prefix}','${ds}')" onclick="TL.selectDate('${prefix}','${ds}')"`}><div class="tl-d">${d}</div>${busy?`<div class="tl-busy-bar"></div><div class="tl-jobs-count">${busy}</div>`:''}</div>`}grid.innerHTML=html}const nextMo=st.month===11?0:st.month+1,nextYr=st.month===11?st.year+1:st.year;renderMonth(st.year,st.month,'left');renderMonth(nextYr,nextMo,'right');const summary=document.getElementById(prefix+'-tl-summary');if(summary){if(st.start&&st.end){let conflict=0,cur=new Date(st.start),end=new Date(st.end);while(cur<=end){if(st.busyDays[ymd(cur)])conflict++;cur.setDate(cur.getDate()+1)}summary.innerHTML=`เลือก: <strong>${fmtDate(st.start)}</strong> ถึง <strong>${fmtDate(st.end)}</strong> (${daysBetween(st.start,st.end)+1} วัน) ${conflict?`<span class="tl-summary-warn">ทับซ้อน ${conflict} วัน</span>`:''}`}else if(st.start)summary.innerHTML=`เริ่ม: <strong>${fmtDate(st.start)}</strong> — เลือกวันสิ้นสุด`;else summary.textContent='กรุณาเลือกช่วงวันที่'}}function setRange(prefix,start,end,team,id){const st=init(prefix);st.team=team||'';st.start=normalizeDate(start)||null;st.end=normalizeDate(end)||null;st.editingId=id;if(st.start){const d=new Date(st.start);st.year=d.getFullYear();st.month=d.getMonth()}buildBusyDays(prefix,id);syncHidden(prefix);render(prefix);const info=document.getElementById(prefix+'-tl-team-info');if(info&&team){info.classList.remove('no-team');info.innerHTML=`ทีม <strong>${escHtml(team)}</strong> มีงาน ${getTeamSchedules(team).length} งาน`}}return{init,onTeamChange,gotoToday,clear,nav,selectDate,startDrag,dragOver,endDrag,setRange,render,_state:state}})();
document.addEventListener('mouseup',()=>{TL.endDrag('add');TL.endDrag('es')});

const SCHEDULE_MAP_INPUT_TIMERS = {};

function scheduleLatLngInput(prefix) {
  return document.getElementById(prefix + '-job_la_long');
}

function parseScheduleLatLng(value) {
  const text = String(value || '').trim();
  const match = text.match(/(-?\d+(?:\.\d+)?)\s*[, ]\s*(-?\d+(?:\.\d+)?)/);
  if (!match) return null;
  const lat = Number(match[1]);
  const lng = Number(match[2]);
  if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null;
  if (Math.abs(lat) > 90 || Math.abs(lng) > 180) return null;
  return { lat, lng };
}

function writeScheduleLatLng(prefix, lat, lng) {
  const input = scheduleLatLngInput(prefix);
  if (input) input.value = `${Number(lat).toFixed(6)},${Number(lng).toFixed(6)}`;
}

function scheduleMapPoint(prefix) {
  return parseScheduleLatLng(scheduleLatLngInput(prefix)?.value);
}

function scheduleForm(prefix) {
  return document.getElementById(prefix === 'add' ? 'form-add-sched' : 'form-edit-sched');
}

function scheduleFieldValue(prefix, name) {
  const form = scheduleForm(prefix);
  const field = document.getElementById(`${prefix}-${name}`) || form?.querySelector(`[name="${name}"]`);
  return String(field?.value || '').trim();
}

function compactScheduleSearchText(parts) {
  const seen = new Set();
  return parts
    .map(value => String(value || '').trim())
    .filter(Boolean)
    .filter(value => {
      const key = value.toLowerCase();
      if (seen.has(key)) return false;
      seen.add(key);
      return true;
    })
    .join(' ');
}

function scheduleMapSearchQuery(prefix) {
  const rawLatLng = String(scheduleLatLngInput(prefix)?.value || '').trim();
  return compactScheduleSearchText([
    scheduleFieldValue(prefix, 'job_location'),
    scheduleFieldValue(prefix, 'customer_name'),
    scheduleFieldValue(prefix, 'job_title'),
    scheduleFieldValue(prefix, 'so_number'),
    scheduleMapPoint(prefix) ? '' : rawLatLng,
  ]);
}

function googleScheduleMapSrc(point) {
  const lat = Number(point.lat).toFixed(6);
  const lng = Number(point.lng).toFixed(6);
  return `https://maps.google.com/maps?q=${encodeURIComponent(`${lat},${lng}`)}&z=16&output=embed`;
}

function googleScheduleSearchUrl(query) {
  return query
    ? `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(query)}`
    : 'https://www.google.com/maps';
}

function scheduleMapCoordBadge(point) {
  const lat = Number(point.lat).toFixed(6);
  const lng = Number(point.lng).toFixed(6);
  return `<div class="sched-map-coord-badge">พิกัด: ${lat}, ${lng}</div>`;
}

function showGoogleScheduleMap(prefix) {
  const mapEl = document.getElementById(prefix + '-map-picker');
  if (!mapEl) return;
  let point = scheduleMapPoint(prefix);
  if (!point) {
    mapEl.innerHTML = '<div class="sched-map-fallback">ใส่พิกัดละติจูด,ลองจิจูด แล้ว Google Maps จะแสดงตำแหน่งนั้น</div>';
    return;
  }

  mapEl.innerHTML = `<iframe title="Google Maps" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="${googleScheduleMapSrc(point)}"></iframe>${scheduleMapCoordBadge(point)}`;
}

function openScheduleGoogleMap(prefix) {
  const point = scheduleMapPoint(prefix);
  if (!point) {
    window.open(googleScheduleSearchUrl(scheduleMapSearchQuery(prefix)), '_blank', 'noopener');
    return;
  }
  const lat = Number(point.lat).toFixed(6);
  const lng = Number(point.lng).toFixed(6);
  window.open(`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(`${lat},${lng}`)}`, '_blank', 'noopener');
}

function scheduleMapInputChanged(prefix) {
  clearTimeout(SCHEDULE_MAP_INPUT_TIMERS[prefix]);
  SCHEDULE_MAP_INPUT_TIMERS[prefix] = setTimeout(() => showGoogleScheduleMap(prefix), 450);
}

function initScheduleMapPicker(prefix) {
  showGoogleScheduleMap(prefix);
}

function refreshScheduleMapFromInput(prefix) {
  showGoogleScheduleMap(prefix);
}

function openProfileFromEl(el){if(!el?.dataset.tech)return;try{const t=JSON.parse(el.dataset.tech);openProfileModal(t);const statusEl=document.getElementById('m-status'),dotEl=document.getElementById('m-st-dot'),txtEl=document.getElementById('m-st-text');if(statusEl)statusEl.className='profile-v2-status pv2-status-active';if(dotEl)dotEl.className='pv2-st-dot pv2-dot-active';if(txtEl)txtEl.textContent=t.emp_position||'ลูกทีม'}catch(e){}}
let CURRENT_PROFILE_TECH = null;
function openProfileModal(t){CURRENT_PROFILE_TECH=t;const set=(id,v)=>{const el=document.getElementById(id);if(el)el.textContent=(v==null||v==='')?'-':v};const img=document.getElementById('m-img'),initial=document.getElementById('m-initial');if(img){if(t.img){img.src=`/storage/${t.img}`;img.style.display='block';if(initial)initial.style.display='none'}else{img.removeAttribute('src');img.style.display='none';if(initial){initial.style.display='block';initial.textContent=(t.emp_name||t.emp_id||'3E').substring(0,2)}}}set('m-name',t.emp_name||t.emp_id);set('m-name-eng',t.emp_name_eng);set('m-position',t.emp_position||'ลูกทีม');set('m-team',t.emp_team);set('m-empid',t.emp_id);set('m-nickname',t.emp_nickname);set('m-phone', fmtPhone(t.emp_phone));set('m-dob',t.date_of_birth?fmtDate(t.date_of_birth):'-');const isLeave=t.status==='leave';const statusEl=document.getElementById('m-status'),dotEl=document.getElementById('m-st-dot'),txtEl=document.getElementById('m-st-text');if(statusEl)statusEl.className='profile-v2-status '+(isLeave?'pv2-status-leave':'pv2-status-active');if(dotEl)dotEl.className='pv2-st-dot '+(isLeave?'pv2-dot-leave':'pv2-dot-active');if(txtEl)txtEl.textContent=isLeave?'ลาออก':'พร้อมทำงาน';const skills=(t.emp_skill||'').split(',').map(s=>s.trim()).filter(Boolean);document.getElementById('m-skills').innerHTML=skills.length?skills.map(s=>`<span class="pv2-tag">${escHtml(s)}</span>`).join(''):'<span class="pv2-muted">-</span>';const lv={none:'ไม่มี',basic:'พื้นฐาน',skill:'ชำนาญ',expert:'เชี่ยวชาญ'};const comps=t.core_competencies||{};const compEntries=Object.entries(comps).filter(([k,v])=>v&&v!=='none');document.getElementById('m-competencies').innerHTML=compEntries.length?compEntries.map(([k,v])=>`<div class="pv2-comp-item"><span class="pv2-comp-key">${escHtml(k)}</span><span class="pv2-comp-val cv-${escHtml(v)}">${lv[v]||escHtml(v)}</span></div>`).join(''):'<span class="pv2-muted">-</span>';const lics=t.licenses||[];document.getElementById('m-licenses').innerHTML=lics.length?lics.map(l=>`<div class="pv2-lic-item"><div class="pv2-lic-name">${escHtml(l.title||'-')}</div><div class="pv2-lic-meta">${l.doc_no?'เลขที่: '+escHtml(l.doc_no):''}${l.date_issued?' · '+escHtml(l.date_issued):''}${l.file?` · <a href="/storage/${escHtml(l.file)}" target="_blank" style="color:var(--blue);font-weight:900">เปิดไฟล์</a>`:''}</div></div>`).join(''):'<span class="pv2-muted">-</span>';const sw=t.software_tools||[];document.getElementById('m-software').innerHTML=sw.length?sw.map(s=>`<span class="pv2-tag pv2-tag-sw">${escHtml(s)}</span>`).join(''):'<span class="pv2-muted">-</span>';openModal('overlay')}
function updateBE(prefix){const inp=document.getElementById(prefix+'-dob'),lbl=document.getElementById(prefix+'-dob-be');if(!inp||!lbl)return;if(inp.value){const d=new Date(inp.value);if(!isNaN(d)){lbl.textContent=`พ.ศ. ${d.getFullYear()+543}`;return}}lbl.textContent='พ.ศ. -'}
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
function addLicense(prefix,lic=null){const i=_licIdx[prefix]++,list=document.getElementById(prefix+'-lic-list');if(!list)return;const row=document.createElement('div');row.className='lic-item';row.innerHTML=`<div class="lic-item-head"><span class="lic-num">#${i+1}</span><button type="button" class="lic-del" onclick="this.closest('.lic-item').remove()">ลบ</button></div><div class="lic-grid"><input class="finput" name="licenses[${i}][title]" placeholder="ชื่อใบรับรอง" value="${escHtml(lic?.title||'')}"><input class="finput" name="licenses[${i}][doc_no]" placeholder="เลขที่" value="${escHtml(lic?.doc_no||'')}"><input class="finput" type="date" name="licenses[${i}][date_issued]" aria-label="วันที่ออก" value="${escHtml(normalizeDate(lic?.date_issued||''))}"><input type="file" name="licenses[${i}][file_upload]" accept=".jpg,.jpeg,.png,.webp,.pdf"></div>${lic?.file?`<input type="hidden" name="licenses[${i}][existing_file]" value="${escHtml(lic.file)}"><a href="/storage/${escHtml(lic.file)}" target="_blank" class="lic-file-link">ไฟล์เดิม</a>`:''}`;list.appendChild(row)}
function addCustomSw(prefix){const inp=document.getElementById(prefix+'-sw-custom'),tags=document.getElementById(prefix+'-sw-custom-tags');const val=inp?.value.trim();if(!val||!tags)return;const tag=document.createElement('span');tag.className='sw-tag';tag.innerHTML=`<input type="hidden" name="software_tools[]" value="${escHtml(val)}">${escHtml(val)}<span class="x" onclick="this.parentElement.remove()">×</span>`;tags.appendChild(tag);inp.value=''}
function openEditTechFromEl(memberEl){if(!memberEl?.dataset.tech)return;let t;try{t=JSON.parse(memberEl.dataset.tech)}catch(e){return}document.getElementById('form-edit-tech').action=URL_TECH_UPDATE(t.emp_id);const v=(id,val)=>{const el=document.getElementById(id);if(el)el.value=val??''};v('et-emp_id',t.emp_id);v('et-emp_name',t.emp_name);v('et-emp_name_eng',t.emp_name_eng);v('et-emp_nickname',t.emp_nickname);v('et-emp_phone',t.emp_phone);v('et-dob',normalizeDate(t.date_of_birth));v('et-emp_position',t.emp_position||'ลูกทีม');v('et-team-select',t.emp_team);v('et-status',t.status||'active');updateBE('et');handlePositionChange('et');const img=document.getElementById('et-img-preview'),ph=document.getElementById('et-img-ph');if(img){if(t.img){img.src=`/storage/${t.img}`;img.style.display='block';img.classList.add('has-img');if(ph)ph.style.display='none'}else{img.removeAttribute('src');img.style.display='none';img.classList.remove('has-img');if(ph)ph.style.display='grid'}}const skills=(t.emp_skill||'').split(',').map(s=>s.trim()).filter(Boolean);document.querySelectorAll('#et-skill-grid label').forEach(l=>{const cb=l.querySelector('input');cb.checked=skills.includes(cb.value);l.classList.toggle('checked',cb.checked)});const comps=t.core_competencies||{};document.querySelectorAll('#et-comp-grid select[data-comp]').forEach(s=>{s.value=comps[s.dataset.comp]||'none';updateCompClass(s)});const sw=t.software_tools||[];document.querySelectorAll('#et-sw-grid label').forEach(l=>{const cb=l.querySelector('input');cb.checked=sw.includes(cb.value);l.classList.toggle('checked',cb.checked)});const tags=document.getElementById('et-sw-custom-tags');tags.innerHTML='';const predefined=Array.from(document.querySelectorAll('#et-sw-grid input')).map(i=>i.value);sw.forEach(s=>{if(!predefined.includes(s)){const tag=document.createElement('span');tag.className='sw-tag';tag.innerHTML=`<input type="hidden" name="software_tools[]" value="${escHtml(s)}">${escHtml(s)}<span class="x" onclick="this.parentElement.remove()">×</span>`;tags.appendChild(tag)}});document.getElementById('et-lic-list').innerHTML='';_licIdx.et=0;(t.licenses||[]).forEach(l=>addLicense('et',l));openModal('modal-edit-tech')}

function openAddSchedModal(){document.getElementById('form-add-sched')?.reset();document.getElementById('add-customer_id').value='';['add-ncf-1','add-ncf-2','add-ncf-3','add-ncf-4'].forEach(id=>{const el=document.getElementById(id);if(el)el.style.display='none'});document.getElementById('add-cust-banner').style.display='none';TL.init('add');TL._state.add.start=null;TL._state.add.end=null;TL._state.add.team='';TL.gotoToday('add');TL.onTeamChange('add');openModal('modal-sched');setTimeout(()=>initScheduleMapPicker('add'),160)}
let _acIdx=-1;
function custAutocomp(q,prefix){const list=document.getElementById(prefix+'-ac-list'),cid=document.getElementById(prefix+'-customer_id'),banner=document.getElementById(prefix+'-cust-banner'),kw=(q||'').toLowerCase().trim();if(!list)return;if(!kw){list.classList.remove('open');if(cid)cid.value='';if(banner)banner.style.display='none';showNewCustFields(prefix,false);return}const matches=CUST_DATA.filter(c=>(c.name||'').toLowerCase().includes(kw)||(c.desc||'').toLowerCase().includes(kw)).slice(0,6);if(!matches.length){list.classList.remove('open');list.innerHTML='';if(cid)cid.value='';if(banner){banner.className='cust-banner cust-banner-new';banner.style.display='flex';banner.textContent='ลูกค้าใหม่ — กรุณากรอกรายละเอียดเพิ่มเติม'}showNewCustFields(prefix,true);return}list.innerHTML=matches.map((c,i)=>`<div class="ac-item" data-idx="${i}" onclick="pickCust('${prefix}',${Number(c.id)})"><div class="ac-item-name">${escHtml(c.name)}</div>${c.desc?`<div class="ac-item-meta">${escHtml(c.desc)}</div>`:''}</div>`).join('');list.classList.add('open');_acIdx=-1}
function custAutocompKey(e,prefix){const list=document.getElementById(prefix+'-ac-list');if(!list?.classList.contains('open'))return;const items=list.querySelectorAll('.ac-item');if(!items.length)return;if(e.key==='ArrowDown'){e.preventDefault();_acIdx=Math.min(_acIdx+1,items.length-1)}else if(e.key==='ArrowUp'){e.preventDefault();_acIdx=Math.max(_acIdx-1,0)}else if(e.key==='Enter'&&_acIdx>=0){e.preventDefault();items[_acIdx].click();return}else if(e.key==='Escape'){list.classList.remove('open');return}else return;items.forEach(i=>i.classList.remove('ac-active'));items[_acIdx]?.classList.add('ac-active')}
function pickCust(prefix,id){const c=CUST_DATA.find(x=>Number(x.id)===Number(id));if(!c)return;document.getElementById(prefix+'-customer_name').value=c.name||'';document.getElementById(prefix+'-customer_id').value=c.id||'';document.getElementById(prefix+'-ac-list').classList.remove('open');const banner=document.getElementById(prefix+'-cust-banner');banner.className='cust-banner cust-banner-old';banner.style.display='flex';banner.innerHTML=`ลูกค้าเดิม: <strong>${escHtml(c.name)}</strong>${c.desc?' · '+escHtml(c.desc):''}`;showNewCustFields(prefix,false);const ll=document.getElementById(prefix+'-job_la_long'),loc=document.getElementById(prefix+'-job_location');if(ll&&!ll.value&&c.loc)ll.value=c.loc;if(loc&&!loc.value)loc.value=c.desc?`${c.name} · ${c.desc}`:c.name;refreshScheduleMapFromInput(prefix)}
function showNewCustFields(prefix,show){if(prefix!=='add')return;['add-ncf-1','add-ncf-2','add-ncf-3','add-ncf-4'].forEach(id=>{const el=document.getElementById(id);if(el)el.style.display=show?'':'none'})}
const STATUS_OPTS={solar:['เสนอ','ดำเนินการ','เสร็จสิ้น','ยกเลิก'],electrical:['เสนอ','ดำเนินการ','เสร็จสิ้น','ยกเลิก'],civil:['เสนอ','ดำเนินการ','เสร็จสิ้น','ยกเลิก'],general:['เสนอ','ดำเนินการ','เสร็จสิ้น','ยกเลิก']};
function onCustTypeChange(typeVal){const cat=getCategory(typeVal),sel=document.getElementById('cf-status'),cur=sel?.value;if(sel)sel.innerHTML=(STATUS_OPTS[cat]||STATUS_OPTS.general).map(s=>`<option value="${s}"${s===cur?' selected':''}>${s}</option>`).join('');document.getElementById('cf-wash-wrap').style.display=cat==='solar'?'':'none';document.getElementById('cf-solar-info').style.display=cat==='solar'?'':'none';document.getElementById('cf-finish-lbl').textContent=cat==='solar'?'วันติดตั้งสำเร็จ':'วันสิ้นสุด';document.getElementById('cf-size-lbl').textContent=cat==='solar'?'ขนาดติดตั้ง (kW)':cat==='electrical'?'ขนาดงาน':cat==='civil'?'พื้นที่/ขอบเขต':'ขนาด/ปริมาณ'}
function openCustAdd(){document.getElementById('cust-modal-title').textContent='เพิ่มลูกค้าใหม่';const f=document.getElementById('form-cust');f.action=URL_CUST_STORE;f.reset();document.getElementById('cf-type_project').value='solar_install';onCustTypeChange('solar_install');openModal('modal-cust')}
function openCustEdit(btn){if(!btn?.dataset.cust)return;let c;try{c=JSON.parse(btn.dataset.cust)}catch(e){return}document.getElementById('cust-modal-title').textContent='แก้ไขลูกค้า: '+(c.name||'');document.getElementById('form-cust').action=URL_CUST_UPDATE(c.id);const v=(id,val)=>{const el=document.getElementById(id);if(el)el.value=val??''};v('cf-type_project',c.type_project||'solar_install');onCustTypeChange(c.type_project||'solar_install');v('cf-name',c.name);v('cf-desc',c.desc);v('cf-contact_name',c.contact_name);v('cf-phone',c.phone);v('cf-size',c.size);v('cf-price',c.price);v('cf-loc',c.loc);v('cf-finish_date',normalizeDate(c.supervisor));v('cf-wash_cycle',c.wash_cycle||6);v('cf-notes',c.notes);v('cf-status',c.status||'เสนอ');openModal('modal-cust')}
let _custCat='all',_custKw='';
function filterCustCat(cat,btn){_custCat=cat;document.querySelectorAll('.cust-filter-btn').forEach(b=>b.classList.remove('active'));btn?.classList.add('active');applyCustFilter()}
function filterCustTable(q){_custKw=(q||'').toLowerCase().trim();applyCustFilter()}
function applyCustFilter(){let shown=0;document.querySelectorAll('#cust-tbody tr[data-cat]').forEach(r=>{const ok=(_custCat==='all'||r.dataset.cat===_custCat)&&(!_custKw||(r.dataset.search||'').includes(_custKw));r.style.display=ok?'':'none';if(ok)shown++});const empty=document.getElementById('cust-empty-row');if(empty)empty.style.display=shown?'none':''}
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
  eventClass(job){return teamEventClass(job)},
  eventCode(type){return{solar_install:'SI',solar_wash:'SW',solar_maintenance:'SM',electrical:'EL',civil:'CV',general:'GN'}[type]||'GN'},
  jobStatus(job){return resolveScheduleStatus(job)},
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
      return `<tr data-sched-id="${escHtml(s.id||'')}" data-sched='${JSON.stringify(s).replace(/'/g,"&#39;")}' onclick="closeTeamCalendar();setTimeout(()=>openEditSchedFromEl(this),100)" style="cursor:pointer">
        <td>${i+1}</td>
        <td><span class="sched-list-so">${escHtml(s.so_number||'-')}</span></td>
        <td><div class="sched-list-cust">${escHtml(s.customer_name||'-')}</div>${s.job_location?`<div style="font-size:11px;color:#64748b;font-weight:700">${escHtml(s.job_location)}</div>`:''}</td>
        <td><div class="sched-list-job">${escHtml(s.job_title||'-')}</div><span class="job-type-tag jt-${escHtml(s.job_type||'general')}" style="margin-top:4px">${escHtml(JOB_TYPES[s.job_type||'general']||s.job_type||'-')}</span></td>
        <td><div class="sched-list-date">${dateHtml}</div></td>
        <td onclick="event.stopPropagation()">${renderScheduleStatusSelect(s)}</td>
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
      const visible=dayJobs.slice(0,2);
      html+=`<div class="sched-day ${ds===today?'today':''}"><div class="sched-day-num">${d}</div>${dayJobs.length>0?`<div class="sched-day-count">${dayJobs.length}</div>`:''}${visible.map(s=>`<button type="button" class="sched-event ${this.eventClass(s)}" data-sched-id="${escHtml(s.id||'')}" data-sched='${JSON.stringify(s).replace(/'/g,"&#39;")}' onclick="event.stopPropagation();closeTeamCalendar();setTimeout(()=>openEditSchedFromEl(this),100)">${renderCalendarEventContent(s)}</button>`).join('')}${dayJobs.length>2?`<button type="button" class="sched-more" onclick="event.stopPropagation();openScheduleDayPopup('${ds}','team')">+${dayJobs.length-2} รายการ</button>`:''}</div>`;
    }
    const rest=(7-((first+total)%7))%7;
    for(let i=1;i<=rest;i++)html+=`<div class="sched-day other"><div class="sched-day-num">${i}</div></div>`;
    grid.innerHTML=html;
    this.renderList();
  }
};
window.TCAL = TCAL;
function openTeamCalendar(team){
  const overlay=document.getElementById('tcal-overlay');
  if(overlay&&overlay.parentElement!==document.body)document.body.appendChild(overlay);
  showTeamRosterView();
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
  overlay?.classList.add('open');
  document.querySelector('.tcal-body')?.scrollTo({top:0,left:0});
  document.body.style.overflow='hidden';
  try {
    TCAL.render();
  } catch (err) {
    console.error('Team calendar render failed', err);
  }
}

function openTeamCalendarFromButton(btn){
  const team = btn?.dataset?.team
    || btn?.closest('.team-card')?.querySelector('.team-title')?.textContent?.trim()
    || '';
  openTeamCalendar(team);
}

document.addEventListener('click', e => {
  const btn = e.target.closest('.team-cal-btn');
  if (!btn) return;
  e.preventDefault();
  e.stopPropagation();
  e.stopImmediatePropagation();
  openTeamCalendarFromButton(btn);
}, true);

function closeTeamCalendar(){
  document.getElementById('tcal-overlay')?.classList.remove('open');
  showTeamRosterView();
  if(!document.querySelector('.overlay.open,.tcal-overlay.open'))document.body.style.overflow='';
  const teamCard=Array.from(document.querySelectorAll('.team-card')).find(card=>(card.querySelector('.team-title')?.textContent||'').trim()===String(TCAL.team||'').trim());
  setTimeout(()=>teamCard?.scrollIntoView({block:'nearest'}),0);
}

window.openTeamCalendar = openTeamCalendar;
window.openTeamCalendarFromButton = openTeamCalendarFromButton;
window.closeTeamCalendar = closeTeamCalendar;
window.showTeamRosterView = showTeamRosterView;

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
  eventClass(job){return teamEventClass(job)},
  eventCode(type){return{solar_install:'SI',solar_wash:'SW',solar_maintenance:'SM',electrical:'EL',civil:'CV',general:'GN',design:'DS',site:'ST',commission:'CO',testing:'TS',meeting:'MT',survey:'SV',report:'RP'}[type]||'GN'},
  jobStatus(job){return resolveScheduleStatus(job)},
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
      return `<tr data-sched-id="${escHtml(s.id||'')}" data-sched='${JSON.stringify(s).replace(/'/g,"&#39;")}' onclick="openEditSchedFromEl(this)">
        <td>${i+1}</td>
        <td><div class="sched-list-date">${dateHtml}</div></td>
        <td><span class="sched-list-team">${escHtml(s.team_name||'-')}</span></td>
        <td><div class="sched-list-job">${escHtml(s.job_title||'-')}</div><span class="job-type-tag jt-${escHtml(s.job_type||'general')}" style="margin-top:4px">${escHtml(JOB_TYPES[s.job_type||'general']||s.job_type||'-')}</span></td>
        <td><span class="sched-list-so">${escHtml(s.so_number||'-')}</span></td>
        <td><div class="sched-list-cust">${escHtml(s.customer_name||'-')}</div>${s.job_location?`<div style="font-size:11px;color:#64748b;font-weight:700">${escHtml(s.job_location)}</div>`:''}</td>
        <td onclick="event.stopPropagation()">${renderScheduleStatusSelect(s)}</td>
      </tr>`;
    }).join('');
  },
  render(){
    const grid=document.getElementById('sched-month-grid');if(!grid)return;
    const thMonthsFull=['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
    const y=this.date.getFullYear(),m=this.date.getMonth();
    const filter=document.getElementById('sched-type-filter')?.value||'all';
    const monthText=`${thMonthsFull[m]} ${y+543}`;
    ['sched-board-month','sched-board-control-month'].forEach(id=>{
      const el=document.getElementById(id);
      if(el)el.textContent=monthText;
    });
    const eyebrow=document.querySelector('.sched-eyebrow');
    if(eyebrow)eyebrow.textContent=`SCHEDULE · ${thMonthsFull[m].toUpperCase()} ${y+543}`;
    const first=new Date(y,m,1).getDay(),total=new Date(y,m+1,0).getDate(),prev=new Date(y,m,0).getDate(),today=ymd(new Date());
    let html='';
    for(let i=first-1;i>=0;i--)html+=`<div class="sched-day other"><div class="sched-day-num">${prev-i}</div></div>`;
    for(let d=1;d<=total;d++){
      const ds=`${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
      let jobs=SCHED_DATA.filter(s=>s.start_date<=ds&&s.end_date>=ds).map(s=>({...s,job_type:s.job_type||'general'}));
      if(filter!=='all')jobs=jobs.filter(s=>s.job_type===filter);
      const visible=jobs.slice(0,2);
      html+=`<div class="sched-day ${ds===today?'today':''}"><div class="sched-day-num">${d}</div>${jobs.length>0?`<div class="sched-day-count">${jobs.length}</div>`:''}
      ${visible.map(s=>`<button type="button" class="sched-event ${this.eventClass(s)}" data-sched-id="${escHtml(s.id||'')}" data-sched='${JSON.stringify(s).replace(/'/g,"&#39;")}' onclick="openEditSchedFromEl(this)">
  ${renderCalendarEventContent(s)}
</button>`).join('')}
      ${jobs.length>2?`<button type="button" class="sched-more" onclick="event.stopPropagation();openScheduleDayPopup('${ds}','main')">+${jobs.length-2} รายการ</button>`:''}</div>`;
    }
    const rest=(7-((first+total)%7))%7;
    for(let i=1;i<=rest;i++)html+=`<div class="sched-day other"><div class="sched-day-num">${i}</div></div>`;
    grid.innerHTML=html;
    this.renderList();
  }
};
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
      card.getAttribute('data-name') || card.getAttribute('data-search') || ''
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
    setSkill(label);
  }
  function setSkill(value) {
    state.skill = norm(value) || 'all';
    document
      .querySelectorAll('#panel-teams .roster-chip')
      .forEach(chip => chip.classList.remove('active'));
    const select = document.getElementById('roster-skill-filter');
    if (select && select.value !== value) select.value = value || 'all';
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
    if (typeof window.filterTeams === 'function') window.filterTeams(value);
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
  document.addEventListener('change', (e) => {
    if (!e.target.matches('#roster-skill-filter')) return;
    e.stopImmediatePropagation();
    setSkill(e.target.value);
  }, true);

  window.filterRosterSkill = (skill, btn) => {
    if (btn) setSkillFromButton(btn);
    else setSkill(skill);
  };

  window.filterRosterSearch = setSearch;
  window.applyRosterFilter = applyRosterFilter;

  function initSearchFilter() {
    const firstSearch = document.querySelector('#panel-teams .search-inp, #panel-teams .roster-search');
    setSearch(firstSearch?.value || '');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSearchFilter);
  } else {
    initSearchFilter();
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
  const teamCalendar = document.getElementById('tcal-overlay');
  if (teamCalendar && teamCalendar.parentElement !== document.body) {
    document.body.appendChild(teamCalendar);
  }
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

<script>
/* === CODEX SCHEDULE STATUS DROPDOWN REAL FIX START === */
window.renderScheduleStatusSelect = function(job) {
  const st = resolveScheduleStatus(job);
  return `
    <span class="sched-status-control ${st.cls}" onclick="event.stopPropagation()">
      <select
        class="sched-status-select"
        data-sched-id="${escHtml(job.id || '')}"
        data-prev="${st.key}"
        onclick="event.stopPropagation()"
        onchange="updateScheduleStatus(this)"
      >
        ${schedStatusOptionsHtml(st.key)}
      </select>
    </span>
  `;
};

window.setScheduleStatusClass = function(sel, key) {
  const wrap = sel.closest('.sched-status-control');
  const nextClass = SCHED_STATUS_CLASSES[key] || SCHED_STATUS_CLASSES.upcoming;
  const classes = Object.values(SCHED_STATUS_CLASSES);
  if (wrap) {
    wrap.classList.remove(...classes);
    wrap.classList.add(nextClass);
  }
};

window.updateScheduleStatus = async function(sel) {
  const id = sel.dataset.schedId;
  const status = sel.value;
  const prev = sel.dataset.prev || '';
  if (!id) return;

  sel.disabled = true;
  setScheduleStatusClass(sel, status);

  try {
    const res = await fetch(URL_SCHED_STATUS(id), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF,
      },
      body: JSON.stringify({ status }),
    });

    if (!res.ok) throw new Error('status update failed');

    SCHED_DATA.forEach(job => {
      if (String(job.id) === String(id)) job.status = status;
    });

    sel.dataset.prev = status;
    SCHED_BOARD.render();

    if (document.getElementById('tcal-overlay')?.classList.contains('open')) {
      TCAL.jobs = SCHED_DATA.filter(job => job.team_name === TCAL.team);
      TCAL.render();
    }
  } catch (err) {
    alert('\u0E1A\u0E31\u0E19\u0E17\u0E36\u0E01\u0E2A\u0E16\u0E32\u0E19\u0E30\u0E44\u0E21\u0E48\u0E2A\u0E33\u0E40\u0E23\u0E47\u0E08');
    sel.value = prev;
    setScheduleStatusClass(sel, prev);
  } finally {
    sel.disabled = false;
  }
};
document.addEventListener('DOMContentLoaded', () => {
  if (window.SCHED_BOARD && typeof window.SCHED_BOARD.render === 'function') {
    window.SCHED_BOARD.render();
  }
});
/* === CODEX SCHEDULE STATUS DROPDOWN REAL FIX END === */
</script>
<script>
/* === CODEX CERTIFICATIONS CLICK DETAIL START === */
window.openCertDetail = function(btn) {
  let items = [];
  try {
    items = JSON.parse(btn.dataset.certItems || '[]');
  } catch (err) {
    items = [];
  }
  const txtPeople = '\u0E04\u0E19\u0E43\u0E19\u0E2D\u0E07\u0E04\u0E4C\u0E01\u0E23';
  const txtNoFile = '\u0E44\u0E21\u0E48\u0E21\u0E35\u0E44\u0E1F\u0E25\u0E4C\u0E41\u0E19\u0E1A';
  const txtOpenFile = '\u0E40\u0E1B\u0E34\u0E14\u0E44\u0E1F\u0E25\u0E4C';
  const txtAttachFile = '\u0E41\u0E19\u0E1A\u0E44\u0E1F\u0E25\u0E4C/\u0E23\u0E39\u0E1B';
  const txtChangeFile = '\u0E40\u0E1B\u0E25\u0E35\u0E48\u0E22\u0E19\u0E44\u0E1F\u0E25\u0E4C';
  const txtSaveFile = '\u0E1A\u0E31\u0E19\u0E17\u0E36\u0E01';
  const txtTeam = '\u0E17\u0E35\u0E21';
  const txtPosition = '\u0E15\u0E33\u0E41\u0E2B\u0E19\u0E48\u0E07';
  const txtDocNo = '\u0E40\u0E25\u0E02\u0E17\u0E35\u0E48';
  const txtIssued = '\u0E2D\u0E2D\u0E01\u0E27\u0E31\u0E19\u0E17\u0E35\u0E48';
  const txtEmpty = '\u0E22\u0E31\u0E07\u0E44\u0E21\u0E48\u0E21\u0E35\u0E02\u0E49\u0E2D\u0E21\u0E39\u0E25\u0E43\u0E1A\u0E23\u0E31\u0E1A\u0E23\u0E2D\u0E07';
  const appBase = window.location.pathname.split('/dashboardtechnician')[0] || '';

  const title = btn.dataset.certName || '-';
  const titleEl = document.getElementById('cert-detail-title');
  const subEl = document.getElementById('cert-detail-sub');
  const listEl = document.getElementById('cert-holder-list');

  if (titleEl) titleEl.textContent = title;
  if (subEl) subEl.textContent = `${items.length} ${txtPeople}`;

  if (listEl) {
    listEl.innerHTML = items.length
      ? items.map((item, rowIndex) => {
          const tech = item.tech || {};
          const lic = item.license || {};
          const name = tech.emp_name || tech.emp_id || '-';
          const empId = tech.emp_id || '';
          const licenseIndex = item.license_index ?? item.licenseIndex ?? '';
          const team = tech.emp_team || '-';
          const position = tech.emp_position || '-';
          const docNo = lic.doc_no || '-';
          const issued = lic.date_issued ? fmtDate(lic.date_issued) : '-';
          const safeEmp = String(empId).replace(/[^A-Za-z0-9_-]/g, '_');
          const safeLic = String(licenseIndex).replace(/[^A-Za-z0-9_-]/g, '_');
          const inputId = `cert-file-${rowIndex}-${safeEmp}-${safeLic}`;
          const fileUrl = lic.file ? `${appBase}/storage/${String(lic.file).replace(/^\/+/, '')}` : '';
          const fileState = lic.file
            ? `<a class="cert-file-link" href="${escHtml(fileUrl)}" target="_blank" onclick="event.stopPropagation()">${txtOpenFile}</a>`
            : `<span class="cert-file-empty">${txtNoFile}</span>`;
          const uploadForm = empId !== '' && licenseIndex !== ''
            ? `<form class="cert-attach-form" method="POST" action="${appBase}/technicians/${encodeURIComponent(empId)}/licenses/${encodeURIComponent(licenseIndex)}/file" enctype="multipart/form-data" onclick="event.stopPropagation()">
                <input type="hidden" name="_token" value="${CSRF}">
                <input class="cert-file-input" id="${escHtml(inputId)}" type="file" name="cert_file" accept=".jpg,.jpeg,.png,.webp,.pdf" onchange="handleCertAttachFile(this)">
                <label class="cert-upload-trigger" for="${escHtml(inputId)}">
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14"/><path d="M5 12h14"/><path d="M19 21H5a2 2 0 0 1-2-2V7"/></svg>
                  <span class="cert-upload-name" data-default-label="${escHtml(lic.file ? txtChangeFile : txtAttachFile)}">${lic.file ? txtChangeFile : txtAttachFile}</span>
                </label>
                <button class="cert-submit" type="submit" disabled hidden>${txtSaveFile}</button>
              </form>`
            : '';

          return `
            <div class="cert-holder">
              <div class="cert-holder-avatar">${escHtml(String(name).slice(0, 2))}</div>
              <div class="cert-holder-main">
                <div class="cert-holder-name">${escHtml(name)}</div>
                <div class="cert-holder-meta">
                  <span class="cert-holder-chip">${txtTeam}: ${escHtml(team)}</span>
                  <span class="cert-holder-chip">${txtPosition}: ${escHtml(position)}</span>
                  <span class="cert-holder-chip">${txtDocNo}: ${escHtml(docNo)}</span>
                  <span class="cert-holder-chip">${txtIssued}: ${escHtml(issued)}</span>
                </div>
              </div>
              <div class="cert-holder-actions">${fileState}${uploadForm}</div>
            </div>
          `;
        }).join('')
      : `<div class="empty-state">${txtEmpty}</div>`;
  }

  openModal('cert-detail-overlay');
};

window.handleCertAttachFile = function(input) {
  const form = input.closest('.cert-attach-form');
  const hasFile = !!(input.files && input.files.length);
  const name = hasFile ? input.files[0].name : '';
  const label = form?.querySelector('.cert-upload-name');
  const submit = form?.querySelector('.cert-submit');
  if (label) label.textContent = hasFile ? name : (label.dataset.defaultLabel || label.textContent);
  if (submit) {
    submit.disabled = !hasFile;
    submit.hidden = !hasFile;
  }
  if (form) form.classList.toggle('is-ready', hasFile);
};

window.closeCertDetail = function() {
  closeModalById('cert-detail-overlay');
};
/* === CODEX CERTIFICATIONS CLICK DETAIL END === */
</script>

<script>
/* === CODEX SCHEDULE JOB TYPE FIX START === */
function schedCleanNote(note) {
  return String(note || '').replace(/^\s*\[[a-zA-Z0-9_-]+\]\s*/, '').trim();
}

function schedJobType(job) {
  if (job && job.job_type) return job.job_type;
  const match = String(job?.note || '').match(/^\s*\[([a-zA-Z0-9_-]+)\]/);
  return match ? match[1] : 'general';
}

window.openEditSchedFromEl = function(btn) {
  if (!btn) return;
  let s = null;
  try {
    s = btn.dataset.sched ? JSON.parse(btn.dataset.sched) : null;
  } catch (e) {
    s = null;
  }
  if (!s && btn.dataset.schedId) {
    s = SCHED_DATA.find(job => String(job.id) === String(btn.dataset.schedId));
  }
  if (!s) return;

  document.getElementById('form-edit-sched').action = URL_SCHED_UPDATE(s.id || btn.dataset.schedId);
  const v = (id, val) => {
    const el = document.getElementById(id);
    if (el) el.value = val ?? '';
  };

  v('es-so_number', s.so_number);
  v('es-customer_name', s.customer_name);
  v('es-job_type', schedJobType(s));
  v('es-status', resolveScheduleStatus(s).key);
  v('es-job_title', s.job_title);
  v('es-job_location', s.job_location);
  v('es-job_la_long', s.job_la_long);
  v('es-team_name', s.team_name);
  v('es-note', s.clean_note ?? schedCleanNote(s.note));

  TL.setRange('es', s.start_date, s.end_date, s.team_name, s.id);
  openModal('modal-edit-sched');
  setTimeout(() => initScheduleMapPicker('es'), 160);
};

document.addEventListener('click', event => {
  const btn = event.target.closest('.sched-event[data-sched-id], .sched-event[data-sched], .cal-ev-card[data-sched-id], .cal-ev-card[data-sched]');
  if (!btn || event.target.closest('.sched-more, .sched-status-select')) return;
  event.preventDefault();
  event.stopPropagation();

  const source = btn.dataset.schedSource || (btn.closest('#tcal-overlay') ? 'team' : 'main');
  openScheduleEditFromCalendar(event, btn, source);
}, true);

document.getElementById('form-edit-sched')?.addEventListener('submit', () => {
  const note = document.getElementById('es-note');
  if (note) note.value = schedCleanNote(note.value);
});
/* === CODEX SCHEDULE JOB TYPE FIX END === */
</script>

<script>
/* === CODEX RETURN TO CUSTOMERS TAB START === */
document.addEventListener('DOMContentLoaded', () => {
  const tab = new URLSearchParams(window.location.search).get('tab');
  const allowedTabs = ['customers', 'schedules', 'teams', 'accounts', 'aircons', 'certifications'];
  if (!allowedTabs.includes(tab)) return;

  const btn = Array.from(document.querySelectorAll('.sb-tab'))
    .find(el => (el.getAttribute('onclick') || '').includes(`'${tab}'`));

  if (typeof switchTab === 'function') {
    switchTab(tab, btn || undefined);
  } else {
    document.querySelectorAll('.panel').forEach(panel => panel.classList.remove('active'));
    document.getElementById('panel-' + tab)?.classList.add('active');
    document.querySelectorAll('.sb-tab').forEach(tabBtn => tabBtn.classList.remove('active'));
    btn?.classList.add('active');
  }
});
/* === CODEX RETURN TO CUSTOMERS TAB END === */
</script>
<!-- CODEX TEAM DRAG DROP JS START -->
<script>
(() => {
  const TXT = {
    members: '\u0E2A\u0E21\u0E32\u0E0A\u0E34\u0E01',
    people: '\u0E04\u0E19',
    headsFirst: '\u0E2B\u0E31\u0E27\u0E2B\u0E19\u0E49\u0E32\u0E02\u0E36\u0E49\u0E19\u0E01\u0E48\u0E2D\u0E19',
    moved: '\u0E22\u0E49\u0E32\u0E22\u0E25\u0E39\u0E01\u0E17\u0E35\u0E21\u0E41\u0E25\u0E49\u0E27',
    failed: '\u0E22\u0E49\u0E32\u0E22\u0E44\u0E21\u0E48\u0E2A\u0E33\u0E40\u0E23\u0E47\u0E08',
    sameTeam: '\u0E2D\u0E22\u0E39\u0E48\u0E17\u0E35\u0E21\u0E19\u0E35\u0E49\u0E41\u0E25\u0E49\u0E27',
  };

  let dragMember = null;
  let suppressClick = false;

  function parseTech(member) {
    try {
      return JSON.parse(member?.dataset?.tech || '{}');
    } catch (err) {
      return {};
    }
  }

  function teamName(card) {
    return (card?.querySelector('.team-title')?.textContent || '').trim();
  }

  function toast(message, isError = false) {
    let box = document.querySelector('.team-dnd-toast');
    if (!box) {
      box = document.createElement('div');
      box.className = 'team-dnd-toast';
      document.body.appendChild(box);
    }
    box.textContent = message;
    box.classList.toggle('error', isError);
    box.classList.add('show');
    clearTimeout(box._timer);
    box._timer = setTimeout(() => box.classList.remove('show'), 1800);
  }

  function refreshCounts() {
    document.querySelectorAll('#view-team .team-card').forEach(card => {
      const count = card.querySelectorAll('.team-body .member').length;
      const meta = card.querySelector('.team-meta');
      if (meta) meta.textContent = `${TXT.members} ${count} ${TXT.people} \u00B7 ${TXT.headsFirst}`;
    });
  }

  function markMembers() {
    document.querySelectorAll('#view-team .member').forEach(member => {
      member.draggable = true;
      member.classList.add('member-draggable');
    });
  }

  async function persistMove(member, targetTeam) {
    const tech = parseTech(member);
    if (!tech.emp_id) throw new Error('Missing employee id');
    const res = await fetch(`/technicians/${encodeURIComponent(tech.emp_id)}/move-team`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': window.CSRF || CSRF,
      },
      body: JSON.stringify({ team_name: targetTeam }),
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok || data.success === false) throw new Error(data.message || 'Move failed');
    tech.emp_team = targetTeam;
    if (data.emp_position) tech.emp_position = data.emp_position;
    member.dataset.tech = JSON.stringify(tech);
  }

  function initTeamDragDrop() {
    markMembers();
    refreshCounts();

    document.querySelectorAll('#view-team .team-card').forEach(card => {
      card.classList.add('team-drop-target');
    });
  }

  document.addEventListener('dragstart', event => {
    const member = event.target.closest('#view-team .member.member-draggable');
    if (!member || event.target.closest('button, form, a, input, select, textarea')) return;
    dragMember = member;
    suppressClick = true;
    const card = member.closest('.team-card');
    const payload = {
      emp_id: parseTech(member).emp_id || '',
      from_team: teamName(card),
    };
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('application/json', JSON.stringify(payload));
    setTimeout(() => member.classList.add('member-dragging'), 0);
  }, true);

  document.addEventListener('dragend', () => {
    document.querySelectorAll('#view-team .team-drop-over').forEach(card => card.classList.remove('team-drop-over'));
    dragMember?.classList.remove('member-dragging');
    dragMember = null;
    setTimeout(() => { suppressClick = false; }, 60);
  }, true);

  document.addEventListener('dragover', event => {
    const card = event.target.closest('#view-team .team-card');
    if (!card || !dragMember) return;
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
    document.querySelectorAll('#view-team .team-drop-over').forEach(other => {
      if (other !== card) other.classList.remove('team-drop-over');
    });
    card.classList.add('team-drop-over');
  }, true);

  document.addEventListener('dragleave', event => {
    const card = event.target.closest('#view-team .team-card');
    if (!card || card.contains(event.relatedTarget)) return;
    card.classList.remove('team-drop-over');
  }, true);

  document.addEventListener('drop', async event => {
    const card = event.target.closest('#view-team .team-card');
    if (!card || !dragMember) return;
    event.preventDefault();
    card.classList.remove('team-drop-over');

    const targetTeam = teamName(card);
    const sourceCard = dragMember.closest('.team-card');
    const sourceTeam = teamName(sourceCard);

    if (!targetTeam || targetTeam === sourceTeam) {
      toast(TXT.sameTeam);
      return;
    }

    const oldBody = sourceCard?.querySelector('.team-body');
    const targetBody = card.querySelector('.team-body');
    if (!targetBody) return;

    const member = dragMember;
    targetBody.appendChild(member);
    refreshCounts();

    try {
      await persistMove(member, targetTeam);
      markMembers();
      toast(TXT.moved);
    } catch (err) {
      oldBody?.appendChild(member);
      refreshCounts();
      toast(`${TXT.failed}: ${err.message}`, true);
    }
  }, true);

  document.addEventListener('click', event => {
    if (!suppressClick) return;
    if (!event.target.closest('#view-team .member')) return;
    event.preventDefault();
    event.stopImmediatePropagation();
  }, true);

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTeamDragDrop);
  } else {
    initTeamDragDrop();
  }

  window.initTeamDragDrop = initTeamDragDrop;
})();
</script>
<!-- CODEX TEAM DRAG DROP JS END -->
<script>
/* === CODEX AIRCON EDIT ACTION START === */
(function(){
  const STORE_URL = "{{ route('aircons.store') }}";
  const TXT_ADD_TITLE = 'เพิ่มข้อมูลเครื่องแอร์';
  const TXT_EDIT_TITLE = 'แก้ไขข้อมูลเครื่องแอร์';
  const TXT_SUB = 'ล้างแอร์';
  const TXT_SAVE_ADD = 'บันทึกข้อมูล';
  const TXT_SAVE_EDIT = 'บันทึกการแก้ไข';
  const TXT_GALLERY = 'เลือกจากแกลเลอรี';

  function appBase(){
    return window.location.pathname.split('/dashboardtechnician')[0] || '';
  }

  function modalOpen(){
    const modal = document.getElementById('modal-aircon');
    if (modal) modal.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function setText(id, text){
    const el = document.getElementById(id);
    if (el) el.textContent = text;
  }

  function setField(form, name, value){
    const el = form?.querySelector(`[name="${name}"]`);
    if (el) el.value = value ?? '';
  }

  function cleanAirconImportNotes(value){
    return String(value || '')
      .split(/\r?\n/)
      .map(line => line.trim())
      .filter(line => {
        if (!line) return false;
        return !(
          /^นำเข้าจาก\s*CSV\s*แอร์/i.test(line) ||
          /^วันที่บันทึก\s*:/i.test(line) ||
          /^รอบล้างถัดไป\s*:/i.test(line) ||
          /^จำนวนรูป\s*:/i.test(line)
        );
      })
      .join('\n');
  }

  function setStatus(form, status){
    const target = status === 'pending' ? 'pending' : 'cleaned';
    form?.querySelectorAll('[name="status"]').forEach(input => {
      input.checked = input.value === target;
    });
  }

  function resetAirconFileLabels(){
    document.querySelectorAll('#modal-aircon .aircon-upload').forEach(label => {
      const text = label.querySelector('span');
      if (!text) return;
      text.textContent = TXT_GALLERY;
    });
  }

  window.openAirconAdd = function(){
    const form = document.getElementById('form-aircon');
    if (!form) return;
    form.reset();
    form.action = STORE_URL;
    setText('aircon-modal-title', TXT_ADD_TITLE);
    setText('aircon-modal-sub', TXT_SUB);
    setText('aircon-save-btn', TXT_SAVE_ADD);
    setStatus(form, 'cleaned');
    resetAirconFileLabels();
    modalOpen();
    setTimeout(() => form.querySelector('[name="aircon_code"]')?.focus(), 80);
  };

  window.openAirconEdit = function(btn){
    const form = document.getElementById('form-aircon');
    if (!form || !btn?.dataset.aircon) return;
    let data = {};
    try { data = JSON.parse(btn.dataset.aircon || '{}'); } catch (err) { data = {}; }
    const rowStatus = btn.closest('tr')?.querySelector('.aircon-status-select')?.value;
    if (rowStatus) data.status = rowStatus;

    form.reset();
    form.action = `${appBase()}/aircons/${encodeURIComponent(data.id || '')}/update`;
    setText('aircon-modal-title', TXT_EDIT_TITLE);
    setText('aircon-modal-sub', `${TXT_SUB} · ${data.aircon_code || ''}`);
    setText('aircon-save-btn', TXT_SAVE_EDIT);
    setField(form, 'aircon_code', data.aircon_code || '');
    setField(form, 'brand', data.brand || '');
    setField(form, 'model_name', data.model_name || '');
    setField(form, 'location', data.location || '');
    setField(form, 'service_date', data.service_date || '');
    setField(form, 'notes', cleanAirconImportNotes(data.notes));
    setStatus(form, data.status || 'cleaned');
    resetAirconFileLabels();
    modalOpen();
    setTimeout(() => form.querySelector('[name="aircon_code"]')?.focus(), 80);
  };
})();

/* === CODEX AIRCON EDIT ACTION END === */
</script>
</body>
</html>
