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
/* ============================================
RESET & BASE
============================================ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body {
font-family: 'Sarabun', 'IBM Plex Sans Thai', -apple-system, sans-serif;
background: #f5f6fa;
color: #212529;
font-size: 14px;
line-height: 1.5;
-webkit-font-smoothing: antialiased;
}
button { font-family: inherit; cursor: pointer; }
input, select, textarea { font-family: inherit; }
a { color: inherit; text-decoration: none; }
img { max-width: 100%; display: block; }
/* ============================================
TOP HEADER BAR
============================================ */
.sidebar {
position: fixed;
top: 0;
left: 0;
right: 0;
height: 64px;
width: 100% !important;
background: #ffffff;
border-bottom: 1px solid #e9ecef;
display: flex;
align-items: center;
justify-content: space-between;
padding: 0 24px;
z-index: 100;
box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.sb-toggle { display: none; }
.sb-logo {
display: flex;
align-items: center;
gap: 12px;
}
.sb-mark {
width: 40px;
height: 40px;
background: #3b5bdb;
color: #fff;
border-radius: 10px;
display: flex;
align-items: center;
justify-content: center;
font-weight: 900;
font-size: 15px;
letter-spacing: -0.5px;
}
.sb-title {
font-size: 15px;
font-weight: 700;
color: #212529;
line-height: 1.2;
}
.sb-sub {
font-size: 12px;
color: #868e96;
font-weight: 500;
}
/* Top Navigation */
.sb-tabs {
display: flex;
gap: 6px;
align-items: center;
}
.sb-tab {
display: flex;
align-items: center;
gap: 6px;
padding: 8px 14px;
background: transparent;
border: 1px solid transparent;
border-radius: 8px;
font-size: 13px;
font-weight: 600;
color: #495057;
transition: all 0.2s;
white-space: nowrap;
}
.sb-tab:hover {
background: #f8f9fa;
}
.sb-tab.active {
background: #e7f5ff;
color: #1971c2;
border-color: #74c0fc;
}
.sb-tab svg {
width: 16px;
height: 16px;
stroke: currentColor;
fill: none;
stroke-width: 2;
stroke-linecap: round;
stroke-linejoin: round;
}
.nav-badge-count {
background: #e9ecef;
color: #495057;
padding: 2px 8px;
border-radius: 12px;
font-size: 11px;
font-weight: 700;
min-width: 20px;
text-align: center;
}
.sb-tab.active .nav-badge-count {
background: rgba(25, 113, 194, 0.15);
color: #1971c2;
}
/* ============================================
MAIN CONTENT
============================================ */
.main {
margin-top: 64px;
padding: 24px;
max-width: 1440px;
margin-left: auto;
margin-right: auto;
}
.panel { display: none; }
.panel.active { display: block; animation: fadeIn 0.25s ease; }
@keyframes fadeIn {
from { opacity: 0; transform: translateY(6px); }
to { opacity: 1; transform: translateY(0); }
}
/* ============================================
PANEL HEADER
============================================ */
.panel-header {
display: flex;
align-items: flex-start;
justify-content: space-between;
gap: 16px;
margin-bottom: 20px;
flex-wrap: wrap;
}
.panel-title {
font-size: 20px;
font-weight: 800;
color: #212529;
letter-spacing: -0.3px;
}
/* ============================================
VIEW TABS
============================================ */
.view-tabs {
display: flex;
gap: 8px;
margin-bottom: 20px;
}
.dtab {
padding: 8px 16px;
background: #ffffff;
border: 1px solid #e9ecef;
border-radius: 20px;
font-size: 13px;
font-weight: 600;
color: #495057;
display: inline-flex;
align-items: center;
gap: 6px;
transition: all 0.2s;
}
.dtab:hover { border-color: #dee2e6; }
.dtab.active {
background: #3b5bdb;
color: #fff;
border-color: #3b5bdb;
}
.dtab .nav-badge-count {
background: rgba(255,255,255,0.25);
color: #fff;
}
.dtab:not(.active) .nav-badge-count {
background: #e9ecef;
color: #495057;
}
/* ============================================
FILTER BAR
============================================ */
.roster-filter {
display: flex;
gap: 12px;
align-items: flex-end;
margin-bottom: 20px;
flex-wrap: wrap;
background: #ffffff;
padding: 16px;
border-radius: 12px;
border: 1px solid #e9ecef;
}
.roster-filter-row {
display: flex;
flex-direction: column;
gap: 4px;
}
.roster-filter-label {
font-size: 11px;
font-weight: 700;
color: #868e96;
text-transform: uppercase;
letter-spacing: 0.5px;
}
.roster-search, .team-filter-search, .search-inp {
padding: 9px 14px;
border: 1px solid #e9ecef;
border-radius: 8px;
font-size: 13px;
min-width: 220px;
background: #fff;
outline: none;
transition: border-color 0.2s, box-shadow 0.2s;
}
.roster-search:focus, .team-filter-search:focus, .search-inp:focus {
border-color: #3b5bdb;
box-shadow: 0 0 0 3px rgba(59, 91, 219, 0.1);
}
.roster-skill-select, .team-skill-select {
padding: 9px 32px 9px 14px;
border: 1px solid #e9ecef;
border-radius: 8px;
font-size: 13px;
background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23868e96' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 12px center;
appearance: none;
outline: none;
cursor: pointer;
min-width: 140px;
}
.roster-filter-actions { margin-left: auto; }
/* ============================================
BUTTONS
============================================ */
.btn {
padding: 9px 16px;
border-radius: 8px;
font-size: 13px;
font-weight: 600;
border: none;
transition: all 0.2s;
display: inline-flex;
align-items: center;
gap: 6px;
white-space: nowrap;
}
.btn-primary {
background: #3b5bdb;
color: #fff;
}
.btn-primary:hover { background: #364fc7; transform: translateY(-1px); }
.btn-ghost {
background: transparent;
color: #495057;
border: 1px solid #e9ecef;
}
.btn-ghost:hover { background: #f8f9fa; border-color: #dee2e6; }
.btn-danger {
background: #fa5252;
color: #fff;
}
.btn-danger:hover { background: #e03131; }
.btn-solar {
background: #2b8a3e;
color: #fff;
}
.btn-solar:hover { background: #237a34; }
.btn-sm { padding: 5px 10px; font-size: 12px; }
.btn-other {
padding: 9px 14px;
background: #f1f3f5;
color: #495057;
border: 1px solid #e9ecef;
border-radius: 8px;
font-size: 13px;
font-weight: 600;
}
/* ============================================
EMPLOYEE CARDS GRID
============================================ */
.emp-card-grid {
display: grid;
grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
gap: 16px;
}
.emp-card {
background: #ffffff;
border: 1px solid #e9ecef;
border-radius: 12px;
padding: 16px;
display: flex;
flex-direction: column;
gap: 12px;
transition: all 0.2s;
cursor: pointer;
position: relative;
}
.emp-card:hover {
border-color: #dee2e6;
box-shadow: 0 4px 12px rgba(0,0,0,0.04);
transform: translateY(-2px);
}
.emp-card.is-head {
border-color: #b2f2bb;
}
.emp-card.is-head::before {
content: '';
position: absolute;
top: 0; left: 0; right: 0;
height: 3px;
background: #40c057;
border-radius: 12px 12px 0 0;
}
.overview-person-top {
display: flex;
justify-content: space-between;
align-items: flex-start;
gap: 12px;
}
.overview-person-copy { flex: 1; min-width: 0; }
.overview-person-id {
font-size: 11px;
color: #adb5bd;
font-weight: 600;
margin-bottom: 2px;
}
.overview-person-name {
font-size: 15px;
font-weight: 700;
color: #212529;
margin-bottom: 2px;
overflow: hidden;
text-overflow: ellipsis;
white-space: nowrap;
}
.overview-person-role {
font-size: 12px;
font-weight: 600;
color: #40c057;
margin-bottom: 4px;
}
.emp-card:not(.is-head) .overview-person-role {
color: #868e96;
}
.overview-person-phone {
font-size: 12px;
color: #868e96;
}
.overview-person-media {
display: flex;
flex-direction: column;
align-items: flex-end;
gap: 4px;
}
.overview-person-brand {
font-size: 10px;
font-weight: 800;
color: #adb5bd;
letter-spacing: 0.5px;
}
.overview-avatar {
width: 48px;
height: 48px;
border-radius: 12px;
background: #ffffff;
display: flex;
align-items: center;
justify-content: center;
overflow: hidden;
flex-shrink: 0;
border: 1px solid #e9ecef;
}
.overview-avatar .avatar-icon {
display: grid;
place-items: center;
width: 100%;
height: 100%;
}
.overview-avatar .avatar-icon svg {
width: 28px;
height: 28px;
stroke: #868e96;
fill: none;
stroke-width: 1.5;
}
/* Skill Tags */
.emp-card-skills {
display: flex;
flex-wrap: wrap;
gap: 6px;
}
.emp-skill-tag {
display: inline-flex;
align-items: center;
gap: 4px;
padding: 4px 10px;
background: #e7f5ff;
color: #1971c2;
border-radius: 6px;
font-size: 12px;
font-weight: 600;
border: 1px solid #d0ebff;
}
.emp-skill-tag svg {
width: 12px;
height: 12px;
stroke: currentColor;
fill: none;
stroke-width: 2.5;
}
.emp-skill-tag.plus-tag {
background: #f1f3f5;
color: #868e96;
border-color: #e9ecef;
}
/* ============================================
TEAM CARDS
============================================ */
.team-grid {
display: grid;
grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
gap: 16px;
}
.team-card {
background: #ffffff;
border: 1px solid #e9ecef;
border-radius: 12px;
overflow: hidden;
transition: all 0.2s;
}
.team-card:hover {
box-shadow: 0 4px 12px rgba(0,0,0,0.04);
}
.team-head-bar {
display: flex;
align-items: center;
gap: 12px;
padding: 14px 16px;
background: #f8f9fa;
border-bottom: 1px solid #e9ecef;
}
.team-title {
font-size: 15px;
font-weight: 700;
color: #212529;
}
.team-meta {
font-size: 12px;
color: #868e96;
margin-top: 2px;
}
.team-cal-btn {
padding: 6px 12px;
background: #fff;
border: 1px solid #e9ecef;
border-radius: 8px;
font-size: 12px;
font-weight: 600;
color: #495057;
display: flex;
align-items: center;
gap: 6px;
margin-left: auto;
}
.team-cal-btn:hover { background: #f1f3f5; }
.badge-count {
background: #e9ecef;
padding: 1px 7px;
border-radius: 10px;
font-size: 11px;
font-weight: 700;
}
.team-body {
padding: 8px;
display: flex;
flex-direction: column;
gap: 4px;
}
.member {
display: flex;
align-items: center;
gap: 12px;
padding: 10px 12px;
border-radius: 8px;
cursor: pointer;
transition: background 0.15s;
}
.member:hover { background: #f8f9fa; }
.m-av {
width: 40px;
height: 40px;
border-radius: 10px;
background: #f1f3f5;
overflow: hidden;
flex-shrink: 0;
}
.m-av img { width: 100%; height: 100%; object-fit: cover; }
.m-info { flex: 1; min-width: 0; }
.m-name-row {
display: flex;
align-items: center;
gap: 6px;
margin-bottom: 2px;
}
.m-name {
font-size: 13px;
font-weight: 700;
color: #212529;
overflow: hidden;
text-overflow: ellipsis;
white-space: nowrap;
}
.head-tag {
font-size: 10px;
font-weight: 700;
color: #2b8a3e;
background: #d3f9d8;
padding: 2px 6px;
border-radius: 4px;
}
.member-tag {
font-size: 10px;
font-weight: 600;
color: #868e96;
background: #f1f3f5;
padding: 2px 6px;
border-radius: 4px;
}
.m-role {
font-size: 11px;
color: #868e96;
}
.m-actions {
display: flex;
align-items: center;
gap: 6px;
}
.status-dot {
width: 8px;
height: 8px;
border-radius: 50%;
background: #40c057;
}
.status-dot.st-leave { background: #fa5252; }
/* ============================================
SCHEDULE BOARD
============================================ */
.sched-board-top {
display: flex;
justify-content: space-between;
align-items: flex-start;
gap: 16px;
margin-bottom: 20px;
flex-wrap: wrap;
}
.sched-eyebrow {
font-size: 11px;
font-weight: 700;
color: #3b5bdb;
letter-spacing: 0.8px;
margin-bottom: 4px;
}
.sched-board-title {
font-size: 26px;
font-weight: 800;
color: #212529;
letter-spacing: -0.5px;
margin-bottom: 4px;
}
.sched-board-sub {
font-size: 13px;
color: #868e96;
}
.sched-controls {
display: flex;
gap: 10px;
align-items: center;
}
.sched-nav-group {
display: flex;
align-items: center;
background: #fff;
border: 1px solid #e9ecef;
border-radius: 10px;
padding: 4px;
gap: 4px;
}
.sched-nav-btn {
width: 34px;
height: 34px;
border: none;
background: transparent;
border-radius: 8px;
font-size: 18px;
color: #495057;
display: flex;
align-items: center;
justify-content: center;
}
.sched-nav-btn:hover { background: #f1f3f5; }
.sched-control-month {
padding: 0 14px;
font-size: 14px;
font-weight: 700;
color: #212529;
min-width: 140px;
text-align: center;
}
/* Calendar Card */
.sched-calendar-card {
background: #fff;
border: 1px solid #e9ecef;
border-radius: 14px;
padding: 20px;
margin-bottom: 20px;
}
.sched-week-head {
display: grid;
grid-template-columns: repeat(7, 1fr);
gap: 8px;
margin-bottom: 10px;
}
.sched-week-head span {
text-align: center;
font-size: 12px;
font-weight: 700;
color: #868e96;
padding: 8px 0;
}
.sched-month-grid {
display: grid;
grid-template-columns: repeat(7, 1fr);
gap: 8px;
}
.sched-day {
aspect-ratio: 1;
background: #f8f9fa;
border-radius: 10px;
padding: 8px;
min-height: 90px;
display: flex;
flex-direction: column;
gap: 4px;
transition: background 0.15s;
position: relative;
}
.sched-day:hover { background: #f1f3f5; }
.sched-day.other {
background: transparent;
opacity: 0.4;
}
.sched-day.today {
background: #3b5bdb;
color: #fff;
}
.sched-day.today:hover { background: #364fc7; }
.sched-day-num {
font-size: 13px;
font-weight: 700;
margin-bottom: 4px;
}
.sched-day-count {
position: absolute;
top: 6px;
right: 6px;
background: rgba(255,255,255,0.3);
color: #fff;
padding: 1px 6px;
border-radius: 10px;
font-size: 10px;
font-weight: 700;
}
.sched-day.today .sched-day-count {
background: rgba(255,255,255,0.25);
}
.sched-event {
display: block;
width: 100%;
text-align: left;
padding: 4px 6px;
border-radius: 5px;
font-size: 11px;
border: none;
cursor: pointer;
margin-bottom: 2px;
overflow: hidden;
}
.sched-event-title {
display: block;
font-weight: 700;
overflow: hidden;
text-overflow: ellipsis;
white-space: nowrap;
}
.sched-event-meta {
display: flex;
flex-direction: column;
font-size: 10px;
opacity: 0.85;
}
.evc-install { background: #fff3bf; color: #e67700; }
.evc-wash { background: #d0ebff; color: #1971c2; }
.evc-maintenance { background: #d3f9d8; color: #2b8a3e; }
.evc-electrical { background: #ffe3e3; color: #c92a2a; }
.evc-civil { background: #e5dbff; color: #7048e8; }
.evc-general { background: #f1f3f5; color: #495057; }
.sched-more {
background: transparent;
border: none;
color: #1971c2;
font-size: 11px;
font-weight: 700;
cursor: pointer;
text-align: left;
padding: 2px 4px;
}
/* Job List */
.sched-list-card {
background: #fff;
border: 1px solid #e9ecef;
border-radius: 14px;
overflow: hidden;
}
.sched-list-head {
padding: 18px 20px;
border-bottom: 1px solid #e9ecef;
display: flex;
justify-content: space-between;
align-items: center;
gap: 16px;
flex-wrap: wrap;
}
.sched-list-eyebrow {
font-size: 11px;
font-weight: 700;
color: #3b5bdb;
letter-spacing: 0.8px;
margin-bottom: 2px;
}
.sched-list-title {
font-size: 16px;
font-weight: 700;
color: #212529;
}
.sched-list-count {
font-size: 13px;
color: #868e96;
font-weight: 500;
}
.sched-list-search {
min-width: 260px;
}
.sched-list-wrap { overflow-x: auto; }
.sched-list-table {
width: 100%;
border-collapse: collapse;
}
.sched-list-table th {
padding: 12px 16px;
text-align: left;
font-size: 11px;
font-weight: 700;
color: #868e96;
background: #f8f9fa;
border-bottom: 1px solid #e9ecef;
text-transform: uppercase;
letter-spacing: 0.3px;
}
.sched-list-table td {
padding: 14px 16px;
font-size: 13px;
border-bottom: 1px solid #f1f3f5;
color: #495057;
vertical-align: top;
}
.sched-list-table tbody tr:hover td { background: #f8f9fa; }
.sched-list-so {
font-family: 'SF Mono', Consolas, monospace;
font-weight: 700;
color: #1971c2;
font-size: 12px;
}
.sched-list-team {
font-weight: 600;
color: #212529;
}
.sched-list-job {
font-weight: 600;
color: #212529;
margin-bottom: 4px;
}
.sched-list-date { font-weight: 600; color: #212529; }
.sched-list-cust { font-weight: 600; color: #212529; }
.job-type-tag {
display: inline-block;
padding: 3px 8px;
background: #e7f5ff;
color: #1971c2;
border-radius: 5px;
font-size: 11px;
font-weight: 700;
}
.jt-solar_install { background: #fff3bf; color: #e67700; }
.jt-solar_wash { background: #d0ebff; color: #1971c2; }
.jt-solar_maintenance { background: #d3f9d8; color: #2b8a3e; }
.jt-electrical { background: #ffe3e3; color: #c92a2a; }
.jt-civil { background: #e5dbff; color: #7048e8; }
.jt-general { background: #f1f3f5; color: #495057; }
/* Status Select */
.sched-status-control {
display: inline-block;
padding: 3px 4px;
border-radius: 12px;
}
.sls-upcoming { background: #fff3bf; }
.sls-doing { background: #d0ebff; }
.sls-done { background: #d3f9d8; }
.sls-cancel { background: #ffe3e3; }
.sched-status-select {
border: none;
background: transparent;
font-size: 12px;
font-weight: 700;
color: inherit;
cursor: pointer;
outline: none;
padding: 2px 4px;
}
/* ============================================
TEAM COLOR SYSTEM
============================================ */
.team-color-dot {
display: inline-block;
width: 10px;
height: 10px;
border-radius: 50%;
margin-right: 6px;
vertical-align: middle;
box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.team-color-badge {
display: inline-flex;
align-items: center;
gap: 6px;
padding: 4px 10px;
border-radius: 20px;
font-size: 12px;
font-weight: 700;
color: #fff;
white-space: nowrap;
}
.sched-list-table tbody tr[data-team-color] {
border-left: 4px solid var(--team-color, #3b5bdb);
transition: background 0.15s;
}
.sched-list-table tbody tr[data-team-color]:hover td {
background: rgba(var(--team-color-rgb, 59, 91, 219), 0.04) !important;
}
.sched-event.team-colored {
border-left: 3px solid var(--team-color, currentColor);
}
.sched-event.team-colored .sched-event-title {
color: var(--team-color-text, inherit);
}
.team-color-legend {
display: flex;
flex-wrap: wrap;
gap: 8px;
margin-top: 12px;
padding-top: 12px;
border-top: 1px solid #e9ecef;
}
.team-color-legend-item {
display: inline-flex;
align-items: center;
gap: 6px;
padding: 4px 10px;
background: #f8f9fa;
border-radius: 6px;
font-size: 11px;
font-weight: 600;
color: #495057;
}
.team-color-reset-btn {
padding: 6px 12px;
background: #fff;
border: 1px solid #e9ecef;
border-radius: 6px;
font-size: 11px;
font-weight: 600;
color: #868e96;
cursor: pointer;
margin-left: auto;
}
.team-color-reset-btn:hover {
background: #ffe3e3;
border-color: #ffa8a8;
color: #c92a2a;
}
/* ============================================
CUSTOMERS SECTION
============================================ */
.customer-eyebrow {
font-size: 11px;
font-weight: 700;
color: #3b5bdb;
letter-spacing: 0.8px;
margin-bottom: 4px;
}
.customer-hero-sub {
font-size: 13px;
color: #868e96;
margin-top: 4px;
}
.customer-site-search-wrap {
margin-bottom: 20px;
}
.customer-site-search-label {
display: block;
font-size: 11px;
font-weight: 700;
color: #868e96;
text-transform: uppercase;
letter-spacing: 0.5px;
margin-bottom: 6px;
}
.customer-site-search {
width: 100%;
max-width: 400px;
padding: 10px 14px;
border: 1px solid #e9ecef;
border-radius: 10px;
font-size: 13px;
background: #fff;
outline: none;
}
.customer-site-search:focus {
border-color: #3b5bdb;
box-shadow: 0 0 0 3px rgba(59, 91, 219, 0.1);
}
/* Metrics */
.cust-metrics {
display: grid;
grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
gap: 12px;
margin-bottom: 20px;
}
.cust-metric {
background: #fff;
border: 1px solid #e9ecef;
border-radius: 12px;
padding: 16px;
}
.cust-metric-label {
font-size: 12px;
font-weight: 600;
color: #868e96;
margin-bottom: 4px;
}
.cust-metric-value {
font-size: 26px;
font-weight: 800;
color: #212529;
letter-spacing: -0.5px;
}
.cust-metric-note {
font-size: 11px;
color: #adb5bd;
margin-top: 2px;
}
/* Filter Chips */
.cust-filter-bar {
display: flex;
gap: 8px;
margin-bottom: 20px;
flex-wrap: wrap;
}
.cust-filter-btn {
padding: 7px 14px;
background: #fff;
border: 1px solid #e9ecef;
border-radius: 20px;
font-size: 13px;
font-weight: 600;
color: #495057;
display: inline-flex;
align-items: center;
gap: 6px;
}
.cust-filter-btn.active {
background: #3b5bdb;
color: #fff;
border-color: #3b5bdb;
}
.fbc {
background: #e9ecef;
padding: 1px 8px;
border-radius: 10px;
font-size: 11px;
font-weight: 700;
}
.cust-filter-btn.active .fbc {
background: rgba(255,255,255,0.25);
color: #fff;
}
/* Customer Table */
.customer-project-table-wrap {
background: #fff;
border: 1px solid #e9ecef;
border-radius: 14px;
overflow: hidden;
}
.customer-project-table {
width: 100%;
border-collapse: collapse;
}
.customer-project-table th {
padding: 12px 16px;
text-align: left;
font-size: 11px;
font-weight: 700;
color: #868e96;
background: #f8f9fa;
border-bottom: 1px solid #e9ecef;
text-transform: uppercase;
letter-spacing: 0.3px;
}
.customer-project-table td {
padding: 14px 16px;
font-size: 13px;
border-bottom: 1px solid #f1f3f5;
vertical-align: top;
}
.customer-project-table tbody tr:hover td { background: #f8f9fa; }
.cust-index {
display: inline-flex;
width: 24px;
height: 24px;
background: #f1f3f5;
border-radius: 6px;
align-items: center;
justify-content: center;
font-size: 11px;
font-weight: 700;
color: #495057;
}
.cust-name-btn {
background: none;
border: none;
font-size: 14px;
font-weight: 700;
color: #3b5bdb;
cursor: pointer;
text-align: left;
padding: 0;
}
.cust-name-btn:hover { text-decoration: underline; }
.cust-desc, .cust-contact {
font-size: 12px;
color: #868e96;
margin-top: 3px;
display: flex;
gap: 4px;
}
.cust-line-label {
font-weight: 700;
color: #adb5bd;
font-size: 11px;
}
.cust-type-stack {
display: flex;
flex-direction: column;
gap: 4px;
}
.cust-type-label {
font-size: 10px;
font-weight: 700;
color: #adb5bd;
text-transform: uppercase;
}
.cust-type-plain {
font-weight: 600;
color: #212529;
}
.cust-date-plain {
font-weight: 600;
color: #212529;
}
.cust-muted { color: #adb5bd; font-size: 12px; }
.wash-cycle-cell {
display: flex;
flex-direction: column;
gap: 2px;
}
.wash-cycle-chip {
display: inline-block;
padding: 3px 8px;
background: #e7f5ff;
color: #1971c2;
border-radius: 5px;
font-size: 11px;
font-weight: 700;
width: fit-content;
}
.wash-cycle-cell small {
font-size: 11px;
color: #868e96;
}
.cust-row-actions {
display: flex;
gap: 6px;
}
/* Wash Alert Bar */
.wash-alert-bar {
background: #fff9db;
border: 1px solid #ffe066;
border-radius: 12px;
padding: 14px 16px;
margin-bottom: 20px;
}
.wash-alert-title {
font-size: 13px;
font-weight: 700;
color: #e67700;
margin-bottom: 10px;
}
.wash-alert-scroll {
display: flex;
gap: 8px;
overflow-x: auto;
padding-bottom: 4px;
}
.wash-alert-chip {
background: #fff;
border: 1px solid #ffe066;
border-radius: 8px;
padding: 8px 12px;
min-width: 140px;
cursor: pointer;
}
.wash-alert-chip.overdue {
background: #ffe3e3;
border-color: #ffa8a8;
}
.wac-name {
font-size: 13px;
font-weight: 700;
color: #212529;
margin-bottom: 2px;
}
.wac-date {
font-size: 11px;
font-weight: 600;
color: #e67700;
}
.wac-date.overdue { color: #c92a2a; }
/* ============================================
ACCOUNTS TABLE
============================================ */
.account-monitoring-filter {
background: #fff;
border: 1px solid #e9ecef;
border-radius: 12px;
padding: 16px;
margin-bottom: 20px;
}
.table-wrap {
background: #fff;
border: 1px solid #e9ecef;
border-radius: 14px;
overflow: hidden;
}
table {
width: 100%;
border-collapse: collapse;
}
table th {
padding: 12px 16px;
text-align: left;
font-size: 11px;
font-weight: 700;
color: #868e96;
background: #f8f9fa;
border-bottom: 1px solid #e9ecef;
text-transform: uppercase;
letter-spacing: 0.3px;
}
table td {
padding: 12px 16px;
font-size: 13px;
border-bottom: 1px solid #f1f3f5;
color: #495057;
vertical-align: middle;
}
table tbody tr:hover td { background: #f8f9fa; }
.acc-pw-wrap {
display: flex;
align-items: center;
gap: 6px;
}
.acc-pw-text {
font-family: 'SF Mono', Consolas, monospace;
font-weight: 700;
color: #1971c2;
font-size: 13px;
}
/* ============================================
AIRCON SECTION
============================================ */
.aircon-shell {
background: #fff;
border: 1px solid #e9ecef;
border-radius: 14px;
padding: 20px;
}
.aircon-metrics {
display: grid;
grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
gap: 12px;
margin-bottom: 24px;
}
.aircon-metric {
display: flex;
align-items: center;
gap: 14px;
padding: 14px 16px;
background: #f8f9fa;
border-radius: 12px;
}
.aircon-metric-label {
font-size: 12px;
font-weight: 600;
color: #868e96;
margin-bottom: 2px;
}
.aircon-metric-value {
font-size: 24px;
font-weight: 800;
color: #212529;
}
.aircon-metric-icon {
width: 44px;
height: 44px;
border-radius: 10px;
display: flex;
align-items: center;
justify-content: center;
margin-left: auto;
}
.aircon-metric-icon svg {
width: 22px;
height: 22px;
stroke: currentColor;
fill: none;
stroke-width: 2;
}
.aircon-metric-icon.total { background: #d0ebff; color: #1971c2; }
.aircon-metric-icon.cleaned { background: #d3f9d8; color: #2b8a3e; }
.aircon-metric-icon.pending { background: #ffe3e3; color: #c92a2a; }
.aircon-list-head {
display: flex;
justify-content: space-between;
align-items: center;
gap: 16px;
margin-bottom: 16px;
flex-wrap: wrap;
}
.aircon-list-title {
font-size: 16px;
font-weight: 700;
color: #212529;
}
.aircon-history-filter {
display: flex;
gap: 10px;
align-items: center;
}
.aircon-history-search-row {
display: flex;
flex-direction: column;
gap: 4px;
}
.aircon-history-label {
font-size: 11px;
font-weight: 700;
color: #868e96;
text-transform: uppercase;
}
.aircon-history-search {
padding: 8px 12px;
border: 1px solid #e9ecef;
border-radius: 8px;
font-size: 13px;
min-width: 220px;
outline: none;
}
.aircon-add-btn {
display: flex;
align-items: center;
gap: 6px;
padding: 9px 14px;
background: #3b5bdb;
color: #fff;
border: none;
border-radius: 8px;
font-size: 13px;
font-weight: 600;
}
.aircon-add-btn svg {
width: 16px;
height: 16px;
stroke: currentColor;
fill: none;
stroke-width: 2.5;
}
.aircon-code-btn {
background: #e7f5ff;
color: #1971c2;
border: none;
padding: 5px 10px;
border-radius: 6px;
font-size: 12px;
font-weight: 700;
font-family: 'SF Mono', Consolas, monospace;
cursor: pointer;
}
.aircon-code-btn:hover { background: #d0ebff; }
.aircon-date-chip {
display: inline-block;
padding: 4px 10px;
border-radius: 6px;
font-size: 12px;
font-weight: 600;
}
.aircon-date-chip.latest { background: #d3f9d8; color: #2b8a3e; }
.aircon-date-chip.next { background: #fff3bf; color: #e67700; }
.aircon-date-chip.empty { background: #f1f3f5; color: #868e96; }
.aircon-status-select {
padding: 5px 10px;
border: 1px solid #e9ecef;
border-radius: 6px;
font-size: 12px;
font-weight: 600;
cursor: pointer;
outline: none;
}
.aircon-status-select.cleaned {
background: #d3f9d8;
color: #2b8a3e;
border-color: #b2f2bb;
}
.aircon-status-select.pending {
background: #ffe3e3;
color: #c92a2a;
border-color: #ffc9c9;
}
/* ============================================
CERTIFICATIONS
============================================ */
.cert-board {
background: #fff;
border: 1px solid #e9ecef;
border-radius: 14px;
padding: 24px;
}
.cert-kicker {
font-size: 11px;
font-weight: 700;
color: #3b5bdb;
letter-spacing: 0.8px;
margin-bottom: 4px;
}
.cert-title {
font-size: 22px;
font-weight: 800;
color: #212529;
margin-bottom: 4px;
}
.cert-sub {
font-size: 13px;
color: #868e96;
margin-bottom: 20px;
}
.cert-grid {
display: grid;
grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
gap: 14px;
}
.cert-card {
background: #fff;
border: 1px solid #e9ecef;
border-radius: 12px;
padding: 16px;
cursor: pointer;
transition: all 0.2s;
text-align: left;
width: 100%;
}
.cert-card:hover {
border-color: #dee2e6;
box-shadow: 0 4px 12px rgba(0,0,0,0.04);
transform: translateY(-2px);
}
.cert-card-top {
display: flex;
align-items: center;
gap: 12px;
margin-bottom: 12px;
}
.cert-icon {
width: 42px;
height: 42px;
background: #fff3bf;
border-radius: 10px;
display: flex;
align-items: center;
justify-content: center;
font-size: 20px;
color: #e67700;
flex-shrink: 0;
}
.cert-info { flex: 1; min-width: 0; }
.cert-name {
font-size: 14px;
font-weight: 700;
color: #212529;
margin-bottom: 2px;
}
.cert-count-text {
font-size: 12px;
color: #868e96;
}
.cert-count {
font-size: 22px;
font-weight: 800;
color: #3b5bdb;
}
.cert-people {
display: flex;
gap: 6px;
flex-wrap: wrap;
}
.cert-people span {
width: 32px;
height: 32px;
background: #e7f5ff;
border-radius: 8px;
display: flex;
align-items: center;
justify-content: center;
font-size: 11px;
font-weight: 700;
color: #1971c2;
}
/* ============================================
MODALS
============================================ */
.overlay {
position: fixed;
inset: 0;
background: rgba(33, 37, 41, 0.5);
backdrop-filter: blur(4px);
display: none;
align-items: center;
justify-content: center;
z-index: 1000;
padding: 20px;
overflow-y: auto;
}
.overlay.open { display: flex; animation: fadeIn 0.2s ease; }
/* ============================================
PROFILE MODAL
============================================ */
.pmodal {
position: relative;
background: #fff;
border-radius: 20px;
max-width: 1000px;
width: 95%;
box-shadow: 0 25px 80px rgba(0,0,0,0.18);
}
.modal-body {
background: #fff;
border-radius: 0 0 20px 20px;
overflow-y: auto;
max-height: calc(90vh - 70px);
}
.pv2-close-btn {
position: absolute;
top: 16px;
right: 16px;
width: 36px;
height: 36px;
border: none;
background: #fff;
border-radius: 10px;
font-size: 20px;
color: #495057;
display: flex;
align-items: center;
justify-content: center;
box-shadow: 0 2px 8px rgba(0,0,0,0.08);
transition: all 0.2s;
z-index: 10;
}
.pv2-close-btn:hover {
background: #f1f3f5;
transform: scale(1.05);
}
/* Profile Layout */
.profile-v2-layout {
display: grid;
grid-template-columns: 380px 1fr;
min-height: 560px;
}
/* Left Panel */
.profile-v2-left {
padding: 32px 28px;
background: #f8f9fa;
border-right: 1px solid #e9ecef;
position: relative;
display: flex;
flex-direction: column;
align-items: center;
}
/* Photo */
.profile-v2-photo {
width: 130px;
height: 130px;
border-radius: 20px;
background: #fff;
margin: 0 auto 20px;
overflow: hidden;
display: flex;
align-items: center;
justify-content: center;
border: 4px solid #fff;
box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}
.profile-v2-photo img {
width: 100%;
height: 100%;
object-fit: cover;
}
.profile-v2-photo .initials {
font-size: 36px;
font-weight: 800;
color: #868e96;
}
/* Name */
.profile-v2-name {
font-size: 24px;
font-weight: 800;
color: #212529;
text-align: center;
margin-bottom: 12px;
letter-spacing: -0.3px;
}
/* Status Badge */
.profile-v2-status {
display: inline-flex;
align-items: center;
gap: 6px;
padding: 6px 14px;
border-radius: 20px;
font-size: 13px;
font-weight: 600;
margin: 0 auto 20px;
}
.pv2-status-active {
background: #d3f9d8;
color: #2b8a3e;
border: 1px solid #b2f2bb;
}
.pv2-status-leave {
background: #ffe3e3;
color: #c92a2a;
border: 1px solid #ffc9c9;
}
.pv2-st-dot {
width: 7px;
height: 7px;
border-radius: 50%;
background: currentColor;
}
/* Role Card */
.profile-v2-rolecard {
background: #fff;
border-radius: 12px;
padding: 14px 16px;
margin-bottom: 16px;
width: 100%;
box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.pv2-rolerow {
display: flex;
justify-content: space-between;
padding: 8px 0;
font-size: 14px;
border-bottom: 1px solid #f1f3f5;
align-items: center;
}
.pv2-rolerow:last-child {
border-bottom: none;
}
.pv2-rolekey {
color: #868e96;
font-weight: 600;
}
.pv2-roleval {
color: #212529;
font-weight: 700;
text-align: right;
}
/* Info List */
.profile-v2-infolist {
display: flex;
flex-direction: column;
gap: 6px;
width: 100%;
}
.pv2-inforow {
display: flex;
justify-content: space-between;
padding: 12px 16px;
background: #fff;
border-radius: 10px;
font-size: 14px;
align-items: center;
box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.pv2-infokey {
color: #868e96;
font-weight: 600;
}
.pv2-infoval {
color: #212529;
font-weight: 700;
text-align: right;
}
/* Right Panel */
.profile-v2-right {
padding: 32px;
}
.pv2-section {
margin-bottom: 28px;
}
.pv2-section-label {
font-size: 12px;
font-weight: 700;
color: #3b5bdb;
letter-spacing: 1px;
text-transform: uppercase;
margin-bottom: 16px;
padding-bottom: 8px;
border-bottom: 2px solid #e7f5ff;
}
/* Combined Grid */
.pv2-combined-grid {
display: grid;
grid-template-columns: 1fr 1fr;
gap: 20px;
}
.pv2-combined-wide {
grid-column: 1 / -1;
}
.pv2-sub-label {
font-size: 13px;
font-weight: 700;
color: #495057;
margin-bottom: 10px;
}
/* Tags */
.pv2-tags {
display: flex;
flex-wrap: wrap;
gap: 8px;
}
.pv2-tag {
padding: 6px 14px;
background: #e7f5ff;
color: #1971c2;
border-radius: 8px;
font-size: 13px;
font-weight: 600;
border: 1px solid #d0ebff;
transition: all 0.2s;
}
.pv2-tag:hover {
background: #d0ebff;
transform: translateY(-1px);
}
.pv2-tag-sw {
background: #e5dbff;
color: #7048e8;
border-color: #d0bfff;
}
.pv2-tag-sw:hover {
background: #d0bfff;
}
/* Competencies Grid */
.pv2-comp-grid {
display: grid;
grid-template-columns: repeat(2, 1fr);
gap: 10px;
}
.pv2-comp-item {
display: flex;
justify-content: space-between;
align-items: center;
padding: 10px 14px;
background: #f8f9fa;
border-radius: 8px;
font-size: 13px;
border: 1px solid #e9ecef;
}
.pv2-comp-key {
color: #495057;
font-weight: 600;
}
.pv2-comp-val {
font-weight: 700;
font-size: 12px;
}
.cv-none { color: #adb5bd; }
.cv-basic { color: #e67700; font-weight: 700; }
.cv-skill { color: #1971c2; font-weight: 700; }
.cv-expert { color: #2b8a3e; font-weight: 700; }
/* Licenses */
.pv2-lic-item {
padding: 14px;
background: #f8f9fa;
border-radius: 10px;
margin-bottom: 10px;
border: 1px solid #e9ecef;
}
.pv2-lic-name {
font-size: 14px;
font-weight: 700;
color: #212529;
margin-bottom: 6px;
}
.pv2-lic-meta {
font-size: 12px;
color: #868e96;
}
.pv2-muted {
color: #adb5bd;
font-size: 14px;
}
/* ============================================
RESPONSIVE
============================================ */
@media (max-width: 1024px) {
.pmodal { max-width: 900px; }
.profile-v2-layout {
grid-template-columns: 340px 1fr;
}
}
@media (max-width: 900px) {
.pmodal { max-width: 100%; width: 98%; }
.profile-v2-layout {
grid-template-columns: 1fr;
}
.profile-v2-left {
border-right: none;
border-bottom: 1px solid #e9ecef;
padding: 24px 20px;
}
.profile-v2-right {
padding: 24px 20px;
}
.pv2-comp-grid {
grid-template-columns: 1fr;
}
}
@media (max-width: 640px) {
.profile-v2-photo {
width: 100px;
height: 100px;
}
.profile-v2-name {
font-size: 20px;
}
.pv2-comp-grid {
grid-template-columns: 1fr;
}
.pv2-combined-grid {
grid-template-columns: 1fr;
}
}
/* Flash Messages */
.flash {
padding: 12px 16px;
border-radius: 10px;
margin-bottom: 16px;
font-size: 13px;
font-weight: 600;
display: flex;
align-items: center;
gap: 8px;
}
.flash-success {
background: #d3f9d8;
color: #2b8a3e;
border: 1px solid #b2f2bb;
}
.flash-error {
background: #ffe3e3;
color: #c92a2a;
border: 1px solid #ffc9c9;
}
/* Empty State */
.empty-state {
text-align: center;
padding: 48px 20px;
color: #adb5bd;
font-size: 14px;
font-weight: 500;
}
/* ============================================
SCHEDULE FORM (TL-WRAP)
============================================ */
.tl-wrap {
background: #fff;
border: 1px solid #e9ecef;
border-radius: 12px;
padding: 16px;
}
.tl-header {
display: flex;
align-items: center;
gap: 12px;
margin-bottom: 16px;
}
.tl-mnav-btn {
width: 32px;
height: 32px;
border: 1px solid #e9ecef;
background: #fff;
border-radius: 8px;
font-size: 16px;
color: #495057;
display: flex;
align-items: center;
justify-content: center;
}
.tl-mnav-btn:hover { background: #f8f9fa; }
.tl-mname {
flex: 1;
text-align: center;
font-size: 14px;
font-weight: 700;
color: #212529;
}
.tl-today-btn {
padding: 6px 12px;
background: #e7f5ff;
border: 1px solid #74c0fc;
border-radius: 8px;
font-size: 12px;
font-weight: 600;
color: #1971c2;
}
.tl-today-btn:hover { background: #d0ebff; }
.tl-team-info {
padding: 10px 12px;
background: #f8f9fa;
border-radius: 8px;
font-size: 13px;
color: #868e96;
margin-bottom: 16px;
}
.tl-team-info.no-team {
background: #fff9db;
color: #e67700;
}
.tl-months {
display: grid;
grid-template-columns: 1fr 1fr;
gap: 16px;
margin-bottom: 16px;
}
.tl-month-block {
display: flex;
flex-direction: column;
gap: 8px;
}
.tl-month-title {
font-size: 13px;
font-weight: 700;
color: #212529;
text-align: center;
}
.tl-dhdrs {
display: grid;
grid-template-columns: repeat(7, 1fr);
gap: 4px;
}
.tl-dhdr {
text-align: center;
font-size: 11px;
font-weight: 700;
color: #868e96;
padding: 4px 0;
}
.tl-dhdr.weekend {
color: #fa5252;
}
.tl-grid {
display: grid;
grid-template-columns: repeat(7, 1fr);
gap: 4px;
}
.tl-cell {
aspect-ratio: 1;
background: #f8f9fa;
border-radius: 6px;
display: flex;
flex-direction: column;
align-items: center;
justify-content: center;
font-size: 12px;
font-weight: 600;
color: #495057;
cursor: pointer;
transition: all 0.15s;
position: relative;
}
.tl-cell:hover { background: #e9ecef; }
.tl-cell.tl-other {
background: transparent;
opacity: 0.3;
cursor: default;
}
.tl-cell.tl-today {
background: #e7f5ff;
color: #1971c2;
font-weight: 700;
}
.tl-cell.tl-busy {
background: #fff3bf;
color: #e67700;
}
.tl-cell.tl-sel-s,
.tl-cell.tl-sel-e {
background: #3b5bdb;
color: #fff;
font-weight: 700;
}
.tl-cell.tl-in-range {
background: #dbe4ff;
color: #3b5bdb;
}
.tl-d {
font-size: 12px;
}
.tl-busy-bar {
position: absolute;
bottom: 2px;
left: 4px;
right: 4px;
height: 2px;
background: #e67700;
border-radius: 1px;
}
.tl-jobs-count {
position: absolute;
top: 2px;
right: 2px;
font-size: 9px;
font-weight: 700;
color: #e67700;
}
.tl-summary {
display: flex;
justify-content: space-between;
align-items: center;
padding: 12px;
background: #f8f9fa;
border-radius: 8px;
margin-bottom: 12px;
}
.tl-summary-info {
font-size: 13px;
color: #495057;
}
.tl-summary-warn {
color: #fa5252;
font-weight: 600;
}
.tl-clear-btn {
padding: 6px 12px;
background: #fff;
border: 1px solid #e9ecef;
border-radius: 6px;
font-size: 12px;
font-weight: 600;
color: #495057;
}
.tl-clear-btn:hover { background: #f8f9fa; }
.tl-legend {
display: flex;
gap: 12px;
flex-wrap: wrap;
font-size: 11px;
color: #868e96;
}
.tl-leg {
display: flex;
align-items: center;
gap: 4px;
}
.tl-leg-box {
width: 12px;
height: 12px;
border-radius: 3px;
display: inline-block;
}
.tl-leg-box.today { background: #e7f5ff; }
.tl-leg-box.busy { background: #fff3bf; }
.tl-leg-box.sel { background: #3b5bdb; }
.tl-leg-box.range { background: #dbe4ff; }
/* ============================================
CUSTOMER DETAIL MODAL
============================================ */
.dtab-bar {
display: flex;
gap: 8px;
margin-bottom: 20px;
border-bottom: 1px solid #e9ecef;
padding-bottom: 12px;
}
.dtab-panel { display: none; }
.dtab-panel.active { display: block; }
.pinfo-grid {
display: grid;
grid-template-columns: repeat(2, 1fr);
gap: 12px;
margin-bottom: 16px;
}
.pinfo-card {
padding: 12px;
background: #f8f9fa;
border-radius: 8px;
}
.pinfo-label {
font-size: 11px;
font-weight: 700;
color: #868e96;
text-transform: uppercase;
margin-bottom: 4px;
}
.pinfo-val {
font-size: 14px;
font-weight: 600;
color: #212529;
}
.wash-log-tbl {
width: 100%;
border-collapse: collapse;
margin-bottom: 16px;
}
.wash-log-tbl th {
padding: 10px 12px;
text-align: left;
font-size: 11px;
font-weight: 700;
color: #868e96;
background: #f8f9fa;
border-bottom: 1px solid #e9ecef;
}
.wash-log-tbl td {
padding: 10px 12px;
font-size: 13px;
border-bottom: 1px solid #f1f3f5;
}
.finfo-box {
padding: 12px;
background: #e7f5ff;
border: 1px solid #74c0fc;
border-radius: 8px;
font-size: 12px;
color: #1971c2;
margin-bottom: 16px;
}
/* ============================================
AIRCON MODAL
============================================ */
.aircon-modal {
background: #fff;
border-radius: 16px;
max-width: 600px;
width: 100%;
max-height: 90vh;
overflow-y: auto;
box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}
.aircon-modal-head {
padding: 20px 24px;
border-bottom: 1px solid #e9ecef;
display: flex;
justify-content: space-between;
align-items: center;
}
.aircon-modal-title {
font-size: 18px;
font-weight: 700;
color: #212529;
}
.aircon-modal-sub {
font-size: 12px;
color: #868e96;
margin-top: 2px;
}
.aircon-modal-close {
width: 32px;
height: 32px;
border: none;
background: #f1f3f5;
border-radius: 8px;
font-size: 18px;
color: #495057;
display: flex;
align-items: center;
justify-content: center;
}
.aircon-modal-body {
padding: 24px;
}
.aircon-form-card {
display: flex;
flex-direction: column;
gap: 16px;
}
.aircon-field {
display: flex;
flex-direction: column;
gap: 6px;
}
.aircon-label {
font-size: 12px;
font-weight: 700;
color: #495057;
}
.aircon-label .req {
color: #fa5252;
}
.aircon-input {
padding: 10px 14px;
border: 1px solid #e9ecef;
border-radius: 8px;
font-size: 14px;
outline: none;
transition: border-color 0.2s, box-shadow 0.2s;
}
.aircon-input:focus {
border-color: #3b5bdb;
box-shadow: 0 0 0 3px rgba(59, 91, 219, 0.1);
}
.aircon-upload-stack {
display: flex;
flex-direction: column;
gap: 8px;
}
.aircon-file {
display: none;
}
.aircon-upload {
display: flex;
align-items: center;
gap: 8px;
padding: 12px 16px;
background: #f8f9fa;
border: 2px dashed #dee2e6;
border-radius: 8px;
cursor: pointer;
transition: all 0.2s;
}
.aircon-upload:hover {
border-color: #3b5bdb;
background: #f1f3f5;
}
.aircon-upload svg {
width: 20px;
height: 20px;
stroke: #868e96;
fill: none;
stroke-width: 2;
}
.aircon-upload span {
font-size: 13px;
font-weight: 600;
color: #495057;
}
.aircon-upload small {
font-size: 11px;
color: #868e96;
}
.aircon-status-group {
display: flex;
gap: 12px;
}
.aircon-status-option {
display: flex;
align-items: center;
gap: 6px;
padding: 8px 16px;
background: #f8f9fa;
border: 1px solid #e9ecef;
border-radius: 8px;
cursor: pointer;
font-size: 13px;
font-weight: 600;
color: #495057;
transition: all 0.2s;
}
.aircon-status-option:hover {
background: #f1f3f5;
}
.aircon-status-option input:checked + span {
color: #3b5bdb;
}
.aircon-note {
padding: 10px 14px;
border: 1px solid #e9ecef;
border-radius: 8px;
font-size: 14px;
outline: none;
resize: vertical;
min-height: 80px;
font-family: inherit;
}
.aircon-note:focus {
border-color: #3b5bdb;
box-shadow: 0 0 0 3px rgba(59, 91, 219, 0.1);
}
.aircon-form-actions {
display: flex;
justify-content: flex-end;
gap: 10px;
margin-top: 8px;
}
.aircon-cancel {
padding: 9px 16px;
background: #fff;
border: 1px solid #e9ecef;
border-radius: 8px;
font-size: 13px;
font-weight: 600;
color: #495057;
}
.aircon-cancel:hover { background: #f8f9fa; }
.aircon-save {
padding: 9px 16px;
background: #3b5bdb;
border: none;
border-radius: 8px;
font-size: 13px;
font-weight: 600;
color: #fff;
}
.aircon-save:hover { background: #364fc7; }
.aircon-form-error {
padding: 12px 16px;
background: #ffe3e3;
border: 1px solid #ffc9c9;
border-radius: 8px;
color: #c92a2a;
font-size: 13px;
font-weight: 600;
}
/* ============================================
AIRCON HISTORY MODAL
============================================ */
.aircon-history-modal {
background: #fff;
border-radius: 16px;
max-width: 800px;
width: 100%;
max-height: 90vh;
overflow-y: auto;
box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}
.aircon-history-head {
padding: 20px 24px;
border-bottom: 1px solid #e9ecef;
display: flex;
justify-content: space-between;
align-items: center;
}
.aircon-history-title {
font-size: 18px;
font-weight: 700;
color: #212529;
}
.aircon-history-sub {
font-size: 12px;
color: #868e96;
margin-top: 2px;
}
.aircon-history-close {
width: 32px;
height: 32px;
border: none;
background: #f1f3f5;
border-radius: 8px;
font-size: 18px;
color: #495057;
display: flex;
align-items: center;
justify-content: center;
}
.aircon-history-body {
padding: 24px;
}
.aircon-history-grid {
display: grid;
grid-template-columns: repeat(2, 1fr);
gap: 12px;
margin-bottom: 24px;
}
.aircon-history-card {
padding: 12px;
background: #f8f9fa;
border-radius: 8px;
}
.aircon-history-label {
font-size: 11px;
font-weight: 700;
color: #868e96;
text-transform: uppercase;
margin-bottom: 4px;
}
.aircon-history-value {
font-size: 14px;
font-weight: 600;
color: #212529;
}
.aircon-history-record {
margin-top: 20px;
}
.aircon-wash-card {
padding: 16px;
background: #f8f9fa;
border-radius: 12px;
margin-bottom: 12px;
}
.aircon-wash-top {
display: flex;
justify-content: space-between;
align-items: flex-start;
margin-bottom: 12px;
}
.aircon-wash-title {
font-size: 15px;
font-weight: 700;
color: #212529;
margin-bottom: 4px;
}
.aircon-wash-place {
font-size: 12px;
color: #868e96;
display: flex;
align-items: center;
gap: 4px;
}
.aircon-wash-pin {
width: 12px;
height: 12px;
background: #fa5252;
border-radius: 50%;
display: inline-block;
}
.aircon-wash-status {
padding: 4px 12px;
border-radius: 12px;
font-size: 12px;
font-weight: 600;
}
.aircon-wash-status.cleaned {
background: #d3f9d8;
color: #2b8a3e;
}
.aircon-wash-status.pending {
background: #ffe3e3;
color: #c92a2a;
}
.aircon-next-strip {
display: flex;
align-items: center;
gap: 8px;
padding: 10px 12px;
background: #fff3bf;
border-radius: 8px;
font-size: 13px;
font-weight: 600;
color: #e67700;
margin-bottom: 12px;
}
.aircon-next-mark {
width: 8px;
height: 8px;
background: #e67700;
border-radius: 50%;
display: inline-block;
}
.aircon-wash-meta {
display: flex;
gap: 16px;
margin-bottom: 12px;
font-size: 12px;
color: #868e96;
}
.aircon-meta-icon {
font-style: normal;
margin-right: 4px;
}
.aircon-wash-note {
margin-bottom: 12px;
}
.aircon-wash-note-label {
font-size: 11px;
font-weight: 700;
color: #868e96;
text-transform: uppercase;
margin-bottom: 4px;
}
.aircon-wash-note-text {
font-size: 13px;
color: #495057;
line-height: 1.6;
}
.aircon-wash-gallery-wrap {
margin-top: 12px;
}
.aircon-wash-gallery-label {
font-size: 11px;
font-weight: 700;
color: #868e96;
text-transform: uppercase;
margin-bottom: 8px;
}
.aircon-wash-gallery {
display: grid;
grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
gap: 8px;
}
.aircon-wash-gallery a {
display: block;
border-radius: 8px;
overflow: hidden;
aspect-ratio: 1;
}
.aircon-wash-gallery img {
width: 100%;
height: 100%;
object-fit: cover;
transition: transform 0.2s;
}
.aircon-wash-gallery img:hover {
transform: scale(1.05);
}
.aircon-wash-gallery-empty {
padding: 20px;
text-align: center;
color: #adb5bd;
font-size: 13px;
}
/* ============================================
CERT MODAL
============================================ */
.cert-modal {
background: #fff;
border-radius: 16px;
max-width: 700px;
width: 100%;
max-height: 90vh;
overflow-y: auto;
box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}
.cert-modal-head {
padding: 20px 24px;
border-bottom: 1px solid #e9ecef;
position: relative;
}
.cert-detail-close-btn {
position: absolute;
top: 16px;
right: 16px;
width: 32px;
height: 32px;
border: none;
background: #f1f3f5;
border-radius: 8px;
font-size: 18px;
color: #495057;
display: flex;
align-items: center;
justify-content: center;
}
.cert-modal-kicker {
font-size: 11px;
font-weight: 700;
color: #3b5bdb;
letter-spacing: 0.8px;
margin-bottom: 4px;
}
.cert-modal-title {
font-size: 20px;
font-weight: 800;
color: #212529;
margin-bottom: 4px;
}
.cert-modal-sub {
font-size: 13px;
color: #868e96;
}
.cert-holder-list {
padding: 24px;
display: flex;
flex-direction: column;
gap: 12px;
}
.cert-holder {
display: flex;
gap: 16px;
padding: 16px;
background: #f8f9fa;
border-radius: 12px;
align-items: flex-start;
}
.cert-holder-avatar {
width: 48px;
height: 48px;
background: #e7f5ff;
border-radius: 12px;
display: flex;
align-items: center;
justify-content: center;
font-size: 16px;
font-weight: 700;
color: #1971c2;
flex-shrink: 0;
}
.cert-holder-main {
flex: 1;
min-width: 0;
}
.cert-holder-name {
font-size: 15px;
font-weight: 700;
color: #212529;
margin-bottom: 6px;
}
.cert-holder-meta {
display: flex;
flex-wrap: wrap;
gap: 6px;
}
.cert-holder-chip {
padding: 3px 8px;
background: #fff;
border: 1px solid #e9ecef;
border-radius: 6px;
font-size: 11px;
font-weight: 600;
color: #495057;
}
.cert-holder-actions {
display: flex;
flex-direction: column;
gap: 8px;
align-items: flex-end;
}
.cert-file-link {
padding: 6px 12px;
background: #e7f5ff;
color: #1971c2;
border-radius: 6px;
font-size: 12px;
font-weight: 600;
text-decoration: none;
}
.cert-file-link:hover { background: #d0ebff; }
.cert-file-empty {
font-size: 12px;
color: #adb5bd;
}
.cert-attach-form {
display: flex;
flex-direction: column;
gap: 8px;
}
.cert-file-input {
display: none;
}
.cert-upload-trigger {
display: flex;
align-items: center;
gap: 6px;
padding: 6px 12px;
background: #f1f3f5;
border: 1px solid #e9ecef;
border-radius: 6px;
cursor: pointer;
font-size: 12px;
font-weight: 600;
color: #495057;
transition: all 0.2s;
}
.cert-upload-trigger:hover {
background: #e9ecef;
}
.cert-upload-trigger svg {
width: 14px;
height: 14px;
stroke: currentColor;
fill: none;
stroke-width: 2;
}
.cert-submit {
padding: 6px 12px;
background: #3b5bdb;
color: #fff;
border: none;
border-radius: 6px;
font-size: 12px;
font-weight: 600;
}
/* ============================================
TEAM CALENDAR OVERLAY
============================================ */
.tcal-overlay {
position: fixed;
inset: 0;
background: rgba(33, 37, 41, 0.5);
backdrop-filter: blur(4px);
display: none;
align-items: center;
justify-content: center;
z-index: 1000;
padding: 20px;
overflow-y: auto;
}
.tcal-overlay.open { display: flex; animation: fadeIn 0.2s ease; }
.tcal-fs {
background: #fff;
border-radius: 16px;
max-width: 1200px;
width: 100%;
max-height: 90vh;
overflow-y: auto;
box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}
.tcal-header {
padding: 20px 24px;
border-bottom: 1px solid #e9ecef;
display: flex;
align-items: center;
gap: 16px;
}
.tcal-icon {
width: 48px;
height: 48px;
background: #e7f5ff;
border-radius: 12px;
display: flex;
align-items: center;
justify-content: center;
flex-shrink: 0;
}
.tcal-icon svg {
width: 24px;
height: 24px;
stroke: #1971c2;
fill: none;
stroke-width: 2;
}
.tcal-title-block {
flex: 1;
}
.tcal-eyebrow {
font-size: 11px;
font-weight: 700;
color: #3b5bdb;
letter-spacing: 0.8px;
margin-bottom: 2px;
}
.tcal-title {
font-size: 20px;
font-weight: 800;
color: #212529;
margin-bottom: 2px;
}
.tcal-stat {
font-size: 13px;
color: #868e96;
font-weight: 600;
}
.tcal-close {
width: 36px;
height: 36px;
border: none;
background: #f1f3f5;
border-radius: 8px;
font-size: 20px;
color: #495057;
display: flex;
align-items: center;
justify-content: center;
}
.tcal-close:hover { background: #e9ecef; }
.tcal-body {
padding: 24px;
}
.sched-month-nav {
margin-bottom: 16px;
}
.sched-month-name {
font-size: 18px;
font-weight: 700;
color: #212529;
text-align: center;
}
/* ============================================
CALENDAR POPUP
============================================ */
.cal-popup-bg {
position: fixed;
inset: 0;
background: rgba(33, 37, 41, 0.3);
display: none;
align-items: center;
justify-content: center;
z-index: 999;
padding: 20px;
}
.cal-popup-bg.open { display: flex; }
.cal-popup {
background: #fff;
border-radius: 12px;
max-width: 500px;
width: 100%;
max-height: 80vh;
overflow-y: auto;
box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}
.cal-popup-strip {
height: 4px;
background: linear-gradient(90deg, #3b5bdb, #74c0fc);
border-radius: 12px 12px 0 0;
}
.cal-popup-head {
padding: 16px 20px;
border-bottom: 1px solid #e9ecef;
display: flex;
justify-content: space-between;
align-items: center;
}
.cal-popup-date {
font-size: 16px;
font-weight: 700;
color: #212529;
}
.cal-popup-count {
font-size: 13px;
color: #868e96;
font-weight: 600;
}
.cal-popup-close {
width: 28px;
height: 28px;
border: none;
background: #f1f3f5;
border-radius: 6px;
font-size: 16px;
color: #495057;
display: flex;
align-items: center;
justify-content: center;
}
.cal-popup-inner {
padding: 16px 20px;
display: flex;
flex-direction: column;
gap: 12px;
}
.cal-ev-card {
padding: 12px;
border-radius: 8px;
border: 1px solid #e9ecef;
cursor: pointer;
transition: all 0.2s;
text-align: left;
width: 100%;
}
.cal-ev-card:hover {
border-color: #dee2e6;
box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.cal-ev-top {
display: flex;
justify-content: space-between;
align-items: center;
margin-bottom: 6px;
}
.cal-so {
font-family: 'SF Mono', Consolas, monospace;
font-size: 12px;
font-weight: 700;
color: #1971c2;
}
.cal-ev-cust {
font-size: 13px;
font-weight: 600;
color: #212529;
margin-bottom: 4px;
}
.cal-ev-job {
font-size: 12px;
color: #495057;
margin-bottom: 8px;
}
.cal-ev-meta {
display: flex;
flex-wrap: wrap;
gap: 12px;
font-size: 11px;
color: #868e96;
}
.cal-ev-ml {
font-weight: 700;
color: #adb5bd;
}
.cal-ev-mv {
font-weight: 600;
color: #495057;
}
/* ============================================
AUTOCOMPLETE
============================================ */
.autocomp {
position: relative;
}
.autocomp-list {
position: absolute;
top: 100%;
left: 0;
right: 0;
background: #fff;
border: 1px solid #e9ecef;
border-radius: 8px;
box-shadow: 0 4px 12px rgba(0,0,0,0.08);
max-height: 240px;
overflow-y: auto;
z-index: 100;
display: none;
margin-top: 4px;
}
.autocomp-list.open { display: block; }
.ac-item {
padding: 10px 14px;
cursor: pointer;
border-bottom: 1px solid #f1f3f5;
transition: background 0.15s;
}
.ac-item:hover { background: #f8f9fa; }
.ac-item.ac-active { background: #e7f5ff; }
.ac-item:last-child { border-bottom: none; }
.ac-item-name {
font-size: 13px;
font-weight: 600;
color: #212529;
margin-bottom: 2px;
}
.ac-item-meta {
font-size: 12px;
color: #868e96;
}
.cust-banner {
padding: 10px 14px;
border-radius: 8px;
font-size: 12px;
font-weight: 600;
margin-top: 8px;
display: none;
}
.cust-banner-new {
background: #fff9db;
color: #e67700;
border: 1px solid #ffe066;
}
.cust-banner-old {
background: #d3f9d8;
color: #2b8a3e;
border: 1px solid #b2f2bb;
}
/* ============================================
SCHEDULE MAP PICKER
============================================ */
.sched-map-picker {
display: flex;
flex-direction: column;
gap: 8px;
}
.sched-map-toolbar {
display: flex;
gap: 8px;
}
.sched-map-toolbar .finput {
flex: 1;
}
.sched-map-btn {
padding: 9px 14px;
background: #fff;
border: 1px solid #e9ecef;
border-radius: 8px;
font-size: 13px;
font-weight: 600;
color: #495057;
white-space: nowrap;
}
.sched-map-btn:hover { background: #f8f9fa; }
.sched-map-hint {
font-size: 11px;
color: #868e96;
}
.sched-map {
width: 100%;
height: 300px;
border-radius: 8px;
overflow: hidden;
border: 1px solid #e9ecef;
background: #f8f9fa;
}
.sched-map iframe {
width: 100%;
height: 100%;
border: none;
}
.sched-map-fallback {
display: flex;
align-items: center;
justify-content: center;
height: 100%;
color: #868e96;
font-size: 13px;
}
.sched-map-coord-badge {
padding: 8px 12px;
background: #e7f5ff;
color: #1971c2;
font-size: 12px;
font-weight: 600;
border-radius: 6px;
margin-top: 8px;
}
/* ============================================
SW CUSTOM ROW
============================================ */
.sw-custom-row {
display: flex;
gap: 8px;
margin-bottom: 12px;
}
.sw-custom-row .finput {
flex: 1;
}
.sw-custom-tags {
display: flex;
flex-wrap: wrap;
gap: 6px;
margin-bottom: 12px;
}
.sw-tag {
display: inline-flex;
align-items: center;
gap: 6px;
padding: 4px 10px;
background: #e5dbff;
color: #7048e8;
border-radius: 6px;
font-size: 12px;
font-weight: 600;
}
.sw-tag .x {
cursor: pointer;
font-size: 14px;
line-height: 1;
}
.sw-tag .x:hover { color: #fa5252; }
/* ============================================
RESUME BADGE
============================================ */
.resume-badge-abs {
position: absolute;
top: -8px;
left: -8px;
padding: 2px 8px;
background: #3b5bdb;
color: #fff;
font-size: 10px;
font-weight: 700;
border-radius: 4px;
letter-spacing: 0.5px;
}
.photo-overlay {
position: absolute;
inset: 0;
background: rgba(0,0,0,0.5);
display: flex;
align-items: center;
justify-content: center;
opacity: 0;
transition: opacity 0.2s;
}
.photo-box:hover .photo-overlay {
opacity: 1;
}
.photo-overlay span {
color: #fff;
font-size: 12px;
font-weight: 600;
}
/* ============================================
FERR (FORM ERROR)
============================================ */
.ferr {
padding: 12px 16px;
background: #ffe3e3;
border: 1px solid #ffc9c9;
border-radius: 8px;
color: #c92a2a;
font-size: 13px;
font-weight: 600;
}
/* ============================================
SCHED GRID
============================================ */
.sched-grid {
display: flex;
flex-direction: column;
gap: 16px;
}
.sched-form-section {
font-size: 13px;
font-weight: 700;
color: #212529;
padding-bottom: 8px;
border-bottom: 1px solid #e9ecef;
margin-bottom: 4px;
}
.sched-full { grid-column: 1 / -1; }
.sched-third {
flex: 1;
}
.sched-third-row {
display: flex;
gap: 12px;
}
.sched-status-input {
width: 100%;
}
/* ============================================
RESPONSIVE
============================================ */
@media (max-width: 900px) {
.sb-tabs { gap: 4px; }
.sb-tab .label { display: none; }
.sb-tab { padding: 8px 10px; }
.profile-v2-layout { grid-template-columns: 1fr; }
.profile-v2-left { border-right: none; border-bottom: 1px solid #e9ecef; }
.resume-fields { grid-template-columns: 1fr; }
.fgrid { grid-template-columns: 1fr; }
.lic-grid { grid-template-columns: 1fr; }
.pv2-combined-grid { grid-template-columns: 1fr; }
.tl-months { grid-template-columns: 1fr; }
.aircon-history-grid { grid-template-columns: 1fr; }
.pinfo-grid { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
.main { padding: 16px; }
.emp-card-grid { grid-template-columns: 1fr; }
.team-grid { grid-template-columns: 1fr; }
.cert-grid { grid-template-columns: 1fr; }
.sched-month-grid { gap: 4px; }
.sched-day { min-height: 70px; padding: 4px; }
.sched-day-num { font-size: 11px; }
.roster-filter { flex-direction: column; align-items: stretch; }
.roster-filter-actions { margin-left: 0; }
.roster-search, .team-filter-search { min-width: 100%; }
.cust-metrics { grid-template-columns: repeat(2, 1fr); }
.aircon-metrics { grid-template-columns: 1fr; }
.sched-third-row { flex-direction: column; }
}
/* ============================================
MODAL BASE (pmodal-wide, strip, header)
============================================ */
.pmodal-wide {
max-width: 900px;
width: 95%;
}
.modal-header {
display: flex;
align-items: center;
justify-content: space-between;
padding: 18px 24px;
border-bottom: 1px solid #e9ecef;
background: #fff;
border-radius: 20px 20px 0 0;
position: sticky;
top: 0;
z-index: 10;
}
.modal-title {
font-size: 18px;
font-weight: 800;
color: #212529;
letter-spacing: -0.3px;
}
.modal-subtitle {
font-size: 12px;
color: #868e96;
margin-top: 2px;
}
.modal-close {
width: 34px;
height: 34px;
border: none;
background: #f1f3f5;
border-radius: 8px;
font-size: 18px;
color: #495057;
display: flex;
align-items: center;
justify-content: center;
transition: all 0.2s;
flex-shrink: 0;
}
.modal-close:hover {
background: #e9ecef;
transform: scale(1.05);
}
/* ============================================
RESUME TOP (รูป + ฟิลด์ข้อมูล)
============================================ */
.resume-top {
display: grid;
grid-template-columns: 200px 1fr;
gap: 24px;
padding: 22px;
align-items: start;
}
.photo-col {
display: flex;
flex-direction: column;
align-items: center;
gap: 10px;
}
.photo-box {
width: 160px;
height: 160px;
border-radius: 16px;
background: #f8f9fa;
border: 2px dashed #dee2e6;
overflow: hidden;
cursor: pointer;
position: relative;
display: flex;
align-items: center;
justify-content: center;
transition: all 0.2s;
}
.photo-box:hover {
border-color: #3b5bdb;
background: #f1f3f5;
}
.resume-img {
width: 100%;
height: 100%;
object-fit: cover;
display: none;
}
.resume-img.has-img {
display: block;
}
.photo-placeholder {
display: flex;
flex-direction: column;
align-items: center;
justify-content: center;
gap: 8px;
color: #adb5bd;
text-align: center;
padding: 16px;
}
.photo-placeholder svg {
width: 40px;
height: 40px;
stroke: #adb5bd;
fill: none;
stroke-width: 1.5;
}
.photo-placeholder div {
font-size: 12px;
font-weight: 600;
color: #868e96;
}
.photo-label {
font-size: 12px;
font-weight: 700;
color: #868e96;
text-transform: uppercase;
letter-spacing: 0.5px;
}
/* ============================================
RESUME FIELDS (ฟอร์มข้อมูล)
============================================ */
.resume-fields {
display: grid;
grid-template-columns: 1fr 1fr;
gap: 14px;
}
.frow {
display: flex;
flex-direction: column;
gap: 5px;
}
.fcol-full {
grid-column: 1 / -1;
}
.flabel {
font-size: 12px;
font-weight: 700;
color: #495057;
}
.finput {
padding: 9px 12px;
border: 1px solid #e9ecef;
border-radius: 8px;
font-size: 13px;
background: #fff;
outline: none;
transition: border-color 0.2s, box-shadow 0.2s;
font-family: inherit;
width: 100%;
}
.finput:focus {
border-color: #3b5bdb;
box-shadow: 0 0 0 3px rgba(59, 91, 219, 0.1);
}
.finput[readonly] {
background: #f8f9fa;
color: #868e96;
cursor: not-allowed;
}
select.finput {
appearance: none;
background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23868e96' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 12px center;
padding-right: 32px;
cursor: pointer;
}
textarea.finput {
resize: vertical;
min-height: 70px;
font-family: inherit;
}
.emp-id-note {
font-size: 11px;
color: #adb5bd;
font-weight: 500;
}
.dob-row {
display: flex;
gap: 10px;
align-items: center;
}
.dob-row .finput {
flex: 1;
}
.dob-be {
font-size: 12px;
font-weight: 700;
color: #3b5bdb;
background: #e7f5ff;
padding: 4px 10px;
border-radius: 6px;
white-space: nowrap;
}
/* ============================================
HEAD INFO BOX
============================================ */
.head-info-box {
padding: 10px 14px;
background: #fff9db;
border: 1px solid #ffe066;
border-radius: 8px;
font-size: 12px;
font-weight: 600;
color: #e67700;
}
/* ============================================
SECTION HEADINGS
============================================ */
.section-h {
font-size: 13px;
font-weight: 800;
color: #212529;
padding: 14px 0 10px;
border-bottom: 2px solid #e7f5ff;
margin-bottom: 12px;
letter-spacing: -0.2px;
}
.section-h:first-child {
padding-top: 0;
}
/* ============================================
SKILL GRID & CHECKBOXES
============================================ */
.skill-grid, .sw-grid {
display: grid;
grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
gap: 8px;
margin-bottom: 16px;
}
.skill-check {
display: flex;
align-items: center;
gap: 8px;
padding: 9px 12px;
background: #f8f9fa;
border: 1px solid #e9ecef;
border-radius: 8px;
cursor: pointer;
font-size: 13px;
font-weight: 600;
color: #495057;
transition: all 0.15s;
user-select: none;
}
.skill-check:hover {
background: #f1f3f5;
border-color: #dee2e6;
}
.skill-check.checked {
background: #e7f5ff;
border-color: #74c0fc;
color: #1971c2;
}
.skill-check input[type="checkbox"] {
width: 16px;
height: 16px;
accent-color: #3b5bdb;
cursor: pointer;
flex-shrink: 0;
}
/* ============================================
COMPETENCY GRID
============================================ */
.comp-grid {
display: grid;
grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
gap: 10px;
margin-bottom: 16px;
}
.comp-card {
background: #f8f9fa;
border: 1px solid #e9ecef;
border-radius: 10px;
padding: 12px;
display: flex;
flex-direction: column;
gap: 8px;
}
.comp-head {
display: flex;
justify-content: space-between;
align-items: center;
}
.comp-label {
font-size: 13px;
font-weight: 700;
color: #212529;
}
.comp-code {
font-size: 11px;
font-weight: 800;
color: #3b5bdb;
background: #e7f5ff;
padding: 2px 7px;
border-radius: 4px;
letter-spacing: 0.5px;
}
.comp-select {
padding: 7px 28px 7px 10px;
border: 1px solid #e9ecef;
border-radius: 6px;
font-size: 12px;
font-weight: 600;
background: #fff;
appearance: none;
background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%23868e96' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
background-repeat: no-repeat;
background-position: right 10px center;
cursor: pointer;
outline: none;
transition: border-color 0.2s;
}
.comp-select:focus {
border-color: #3b5bdb;
}
.comp-select.lv-none { color: #adb5bd; }
.comp-select.lv-basic { color: #e67700; font-weight: 700; }
.comp-select.lv-skill { color: #1971c2; font-weight: 700; }
.comp-select.lv-expert { color: #2b8a3e; font-weight: 700; }
/* ============================================
LICENSES
============================================ */
.lic-list {
display: flex;
flex-direction: column;
gap: 10px;
margin-bottom: 12px;
}
.lic-item {
background: #f8f9fa;
border: 1px solid #e9ecef;
border-radius: 10px;
padding: 12px;
}
.lic-item-head {
display: flex;
justify-content: space-between;
align-items: center;
margin-bottom: 10px;
}
.lic-num {
font-size: 12px;
font-weight: 800;
color: #3b5bdb;
background: #e7f5ff;
padding: 2px 8px;
border-radius: 4px;
}
.lic-del {
padding: 4px 10px;
background: #ffe3e3;
color: #c92a2a;
border: none;
border-radius: 6px;
font-size: 11px;
font-weight: 700;
cursor: pointer;
transition: all 0.2s;
}
.lic-del:hover {
background: #ffc9c9;
}
.lic-grid {
display: grid;
grid-template-columns: 2fr 1fr 1fr 1fr;
gap: 8px;
}
.lic-grid .finput {
font-size: 12px;
padding: 7px 10px;
}
.lic-file-link {
display: inline-block;
margin-top: 8px;
padding: 4px 10px;
background: #e7f5ff;
color: #1971c2;
border-radius: 6px;
font-size: 11px;
font-weight: 700;
text-decoration: none;
}
.lic-file-link:hover {
background: #d0ebff;
}
.btn-add-lic {
display: inline-flex;
align-items: center;
gap: 6px;
padding: 8px 14px;
background: #fff;
border: 1px dashed #3b5bdb;
border-radius: 8px;
font-size: 13px;
font-weight: 700;
color: #3b5bdb;
cursor: pointer;
transition: all 0.2s;
}
.btn-add-lic:hover {
background: #e7f5ff;
border-style: solid;
}
/* ============================================
FORM ACTIONS (ปุ่มท้ายฟอร์ม)
============================================ */
.factions {
display: flex;
justify-content: flex-end;
gap: 10px;
padding: 16px 22px;
border-top: 1px solid #e9ecef;
background: #f8f9fa;
border-radius: 0 0 20px 20px;
margin-top: 8px;
}
/* ============================================
SMALL MODAL (pmodal-sm)
============================================ */
.pmodal-sm {
max-width: 500px;
width: 95%;
}
/* ============================================
RESPONSIVE
============================================ */
@media (max-width: 768px) {
.resume-top {
grid-template-columns: 1fr;
}
.photo-col {
align-items: center;
}
.resume-fields {
grid-template-columns: 1fr;
}
.lic-grid {
grid-template-columns: 1fr;
}
.comp-grid {
grid-template-columns: 1fr;
}
.skill-grid, .sw-grid {
grid-template-columns: repeat(2, 1fr);
}
.pmodal-wide {
width: 98%;
max-width: 100%;
}
}
@media (max-width: 480px) {
.skill-grid, .sw-grid {
grid-template-columns: 1fr;
}
.modal-header {
padding: 14px 16px;
}
.resume-top {
padding: 16px;
}
.factions {
padding: 12px 16px;
}
}
/* ============================================
ทำให้ Modal เพิ่ม/แก้ไขช่าง โค้งทั้ง 2 ด้าน (บนและล่าง)
============================================ */
#modal-tech .pmodal-wide,
#modal-edit-tech .pmodal-wide {
border-radius: 20px !important;
}
#modal-tech .pmodal-strip,
#modal-edit-tech .pmodal-strip {
border-radius: 20px 20px 0 0 !important;
}
#modal-tech .modal-header,
#modal-edit-tech .modal-header {
border-radius: 20px 20px 0 0 !important;
}
#modal-tech .modal-body,
#modal-edit-tech .modal-body {
border-radius: 0 0 20px 20px !important;
}
#modal-tech .photo-box,
#modal-edit-tech .photo-box {
border-radius: 16px !important;
}
#modal-tech .finput,
#modal-edit-tech .finput {
border-radius: 8px !important;
}
#modal-tech .skill-check,
#modal-edit-tech .skill-check {
border-radius: 8px !important;
}
#modal-tech .comp-card,
#modal-edit-tech .comp-card {
border-radius: 10px !important;
}
#modal-tech .lic-item,
#modal-edit-tech .lic-item {
border-radius: 10px !important;
}
#modal-tech .btn,
#modal-edit-tech .btn,
#modal-tech .btn-add-lic,
#modal-edit-tech .btn-add-lic {
border-radius: 8px !important;
}
#modal-tech .modal-close,
#modal-edit-tech .modal-close {
border-radius: 8px !important;
}
#modal-tech .factions,
#modal-edit-tech .factions {
border-radius: 0 0 20px 20px !important;
}
/* แก้ไขให้ modal โค้งทั้ง 2 ด้าน */
.pmodal.profile-v2 {
border-radius: 20px !important;
overflow: hidden; /* สำคัญ! */
}
.pmodal.profile-v2 .profile-v2-left,
.pmodal.profile-v2 .profile-v2-right {
border-radius: 0; /* ลบ border-radius ของ child elements */
}
/* ทำให้ content ด้านในโค้งตาม modal */
.profile-v2-right {
border-radius: 0 0 20px 0;
}
/* ปรับ modal-body ให้โค้งด้านล่าง */
.pmodal.profile-v2 .modal-body {
border-radius: 0 0 20px 20px;
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
    <div class="view-tabs">
      <button class="dtab active" type="button" onclick="switchViewTab('all',this)">ทั้งหมด <span class="nav-badge-count">{{ $technicians->count() }}</span></button>
      <button class="dtab" type="button" onclick="switchViewTab('team',this)">ทีม <span class="nav-badge-count">{{ $teams->count() }}</span></button>
    </div>
    <div id="view-all">
      <div class="roster-board">
        <div class="roster-head"><div></div></div>
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
            <button class="btn btn-primary roster-add-tech-btn" type="button" onclick="openModal('modal-tech')">+ เพิ่มช่าง</button>
          </div>
        </div>
        <div class="emp-card-grid" id="roster-grid">
          @forelse($sortedTechnicians as $m)
            @php
              $skills = collect(explode(',', $m->emp_skill ?? ''))->map(fn($x) => trim($x))->filter()->values();
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
                  <div class="overview-person-name" title="{{ $m->emp_name ?: $m->emp_id }}">{{ $m->emp_name ?: $m->emp_id }}</div>
                  <div class="overview-person-role">{{ $isHead ? 'หัวหน้าทีม' : ($m->emp_position ?: 'ลูกทีม') }}</div>
                  <div class="overview-person-phone">โทร {{ $phoneDisplay }}</div>
                </div>
                <div class="overview-person-media">
                  <div class="overview-avatar">
                    @if($m->img)
                      <img src="{{ asset('storage/'.$m->img) }}" alt="{{ $m->emp_name }}" onerror="this.onerror=null;this.style.display='none';const icon=this.parentElement?.querySelector('.avatar-icon');if(icon)icon.style.display='grid';">
                      <div class="avatar-icon" style="display:none">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#868e96" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                      </div>
                    @else
                      <div class="avatar-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#868e96" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="overview-person-skills emp-card-skills">
                @forelse($skills as $sk)
                  <span class="emp-skill-tag"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8z"/></svg>{{ $sk }}</span>
                @empty
                  <span class="emp-skill-tag"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 2"/></svg>ทั่วไป</span>
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
          <button class="btn btn-primary roster-add-tech-btn" type="button" onclick="openModal('modal-tech')">+ เพิ่มช่าง</button>
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
                {{-- <button type="button" class="team-cal-btn" data-team="{{ $teamName }}" onclick="event.stopPropagation();openTeamCalendar(this.dataset.team)">ปฏิทิน <span class="badge-count">{{ $teamScheds->count() }}</span></button> --}}
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
                <th>วันที่ทำงาน</th>
                <th>ทีมช่าง</th>
                <th>รายละเอียดงาน</th>
                <th>สถานะงาน</th>
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
                  @else - @endif
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
                  if ($ac->cover_image && ! in_array($ac->cover_image, $airconImages, true)) { $airconImages[] = $ac->cover_image; }
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
                    'id' => $ac->id, 'aircon_code' => $ac->aircon_code, 'brand' => $ac->brand, 'model_name' => $ac->model_name, 'location' => $ac->location,
                    'service_date' => $serviceDate ? $serviceDate->format('Y-m-d') : '', 'next_service_date' => $nextServiceDate ? $nextServiceDate->format('Y-m-d') : '',
                    'image_count' => count($airconImages), 'image_urls' => $airconImageUrls, 'history_count' => $airconWashLogs->count(),
                    'wash_logs' => $airconWashLogs->values()->all(), 'status' => $status, 'status_text' => $statusText, 'notes' => $ac->notes ?? '',
                  ];
                @endphp
                <tr data-search="{{ trim($airconSearchBase.' '.strtolower($statusText)) }}" data-search-base="{{ $airconSearchBase }}">
                  <td>{{ $idx + 1 }}</td>
                  <td><button class="aircon-code-btn" type="button" data-aircon="{{ json_encode($airconPayload, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openAirconHistory(this)">{{ $ac->aircon_code }}</button></td>
                  <td><strong>{{ $ac->brand }}</strong><div style="font-size:12px;color:var(--muted);font-weight:800">{{ $ac->model_name }}</div></td>
                  <td>{{ $ac->location }}</td>
                  <td>@if($serviceDate)<span class="aircon-date-chip latest">{{ $serviceDate->format('d/m/') }}{{ $serviceDate->year + 543 }}</span>@else<span class="aircon-date-chip empty">-</span>@endif</td>
                  <td>@if($nextServiceDate)<span class="aircon-date-chip next">{{ $nextServiceDate->format('d/m/') }}{{ $nextServiceDate->year + 543 }}</span>@else<span class="aircon-date-chip empty">-</span>@endif</td>
                  <td>
                    <select class="aircon-status-select {{ $status }}" data-aircon-id="{{ $ac->id }}" data-prev="{{ $status }}" onchange="updateAirconStatus(this)">
                      <option value="cleaned" {{ $status === 'cleaned' ? 'selected' : '' }}>ล้างแล้ว</option>
                      <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>ยังไม่ได้ล้าง</option>
                    </select>
                  </td>
                </tr>
              @endforeach
              <tr id="aircon-empty-row" style="display:none"><td colspan="7" class="cust-empty-filter">ไม่พบข้อมูลตามคำค้นหา</td></tr>
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
          @if($errors->any() && old('_aircon_form'))<div class="aircon-form-error">{{ $errors->first() }}</div>@endif
          <div class="aircon-field"><label class="aircon-label">รหัสเครื่อง <span class="req">*</span></label><input class="aircon-input" type="text" name="aircon_code" value="{{ old('aircon_code') }}" placeholder="พิมพ์ ID เช่น AC-001" required></div>
          <div class="aircon-field"><label class="aircon-label">ยี่ห้อ <span class="req">*</span></label><input class="aircon-input" type="text" name="brand" value="{{ old('brand') }}" placeholder="เช่น Daikin, Mitsubishi, Samsung" required></div>
          <div class="aircon-field"><label class="aircon-label">ชื่อรุ่นแอร์ <span class="req">*</span></label><input class="aircon-input" type="text" name="model_name" value="{{ old('model_name') }}" placeholder="เช่น FTKM09SV2S, Inverter 12000BTU" required></div>
          <div class="aircon-field"><label class="aircon-label">จุดติดตั้ง <span class="req">*</span></label><input class="aircon-input" type="text" name="location" value="{{ old('location') }}" placeholder="เช่น ชั้น 2 ห้องประชุม" required></div>
          <div class="aircon-field"><label class="aircon-label">วันที่ล้าง / ตรวจ <span class="req">*</span></label><input class="aircon-input" type="date" name="service_date" value="{{ old('service_date', now()->toDateString()) }}" required></div>
          <div class="aircon-field">
            <label class="aircon-label">รูปเครื่องแอร์ (แนบได้หลายรูป)</label>
            <div class="aircon-upload-stack">
              <input class="aircon-file" id="aircon-gallery-images" type="file" name="images[]" accept="image/*" multiple>
              <label class="aircon-upload" for="aircon-gallery-images" data-file-label>
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                <span>เลือกจากแกลเลอรี</span><small>เลือกได้หลายรูป</small>
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
          <div class="aircon-field"><label class="aircon-label">หมายเหตุ</label><textarea class="aircon-note" name="notes" placeholder="รายละเอียดเพิ่มเติม เช่น น้ำหยด / เสียงดัง / ต้องนัดซ่อม">{{ old('notes') }}</textarea></div>
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
          <div class="aircon-history-card"><div class="aircon-history-label">รหัสเครื่อง</div><div class="aircon-history-value" id="aircon-history-code">-</div></div>
          <div class="aircon-history-card"><div class="aircon-history-label">ยี่ห้อ / รุ่น</div><div class="aircon-history-value" id="aircon-history-brand">-</div></div>
          <div class="aircon-history-card"><div class="aircon-history-label">จุดติดตั้ง</div><div class="aircon-history-value" id="aircon-history-location">-</div></div>
          <div class="aircon-history-card"><div class="aircon-history-label">วันที่ล้าง / ตรวจล่าสุด</div><div class="aircon-history-value" id="aircon-history-date">-</div></div>
        </div>
        <div class="aircon-history-record">
          <div class="aircon-history-label">ประวัติการล้าง</div>
          <div id="aircon-history-records"></div>
        </div>
      </div>
    </div>
  </div>
  @if($errors->any() && old('_aircon_form'))
    <script>document.addEventListener('DOMContentLoaded', () => openModal('modal-aircon'));</script>
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
      <div class="cert-modal-sub" id="cert-detail-sub">0 คนในองค์กร</div>
    </div>
    <div class="cert-holder-list" id="cert-holder-list"></div>
  </div>
</div>
<!-- === CODEX CERTIFICATIONS DETAIL MODAL END === -->

<div class="overlay" id="overlay">
  <div class="pmodal profile-v2" onclick="event.stopPropagation()">
    <button class="pv2-close-btn" type="button" onclick="closeModalById('overlay')">×</button>
    <div class="profile-v2-layout">
      <div class="profile-v2-left">
        <div class="profile-v2-photo">
          <img id="m-img" src="" alt="" style="display:none">
          <div id="m-photo-icon" style="display:grid;place-items:center">
            <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#868e96" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
        </div>
        <div class="profile-v2-name" id="m-name"></div>
        <div class="profile-v2-status pv2-status-active" id="m-status"><span class="pv2-st-dot pv2-dot-active" id="m-st-dot"></span><span id="m-st-text">พร้อมทำงาน</span></div>
        <div class="profile-v2-rolecard">
          <div class="pv2-rolerow"><span class="pv2-rolekey">ชื่ออังกฤษ :</span><span class="pv2-roleval profile-v2-nameeng" id="m-name-eng">-</span></div>
          <div class="pv2-rolerow"><span class="pv2-rolekey">ทีม :</span><span class="pv2-roleval" id="m-team">-</span></div>
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
              <div class="pv2-combined-group"><div class="pv2-sub-label">ทักษะ</div><div class="pv2-tags" id="m-skills"></div></div>
              <div class="pv2-combined-group"><div class="pv2-sub-label">Software & Tools</div><div class="pv2-tags" id="m-software"></div></div>
              <div class="pv2-combined-group pv2-combined-wide"><div class="pv2-sub-label">Core Competencies</div><div class="pv2-comp-grid" id="m-competencies"></div></div>
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
    <div class="pmodal-strip"></div>
    <div class="modal-header"><div class="modal-title">แก้ไขข้อมูลช่าง</div><button class="modal-close" type="button" onclick="closeModalById('modal-edit-tech')">×</button></div>
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

<!-- === MODAL ดูรายละเอียดงาน (View Only) === -->
<div class="overlay" id="modal-view-sched">
  <div class="pmodal" style="max-width: 520px; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);" onclick="event.stopPropagation()">
    
    <!-- Header -->
    <div style="background: #ffffff; padding: 20px 24px 16px; display: flex; align-items: flex-start; justify-content: space-between; border-bottom: 1px solid #f1f3f5;">
      <div>
        <div style="font-size: 18px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 8px;">
          <span>📋</span> รายละเอียดงาน
        </div>
        <div id="vs-so-number" style="font-size: 13px; font-weight: 600; color: #64748b; margin-top: 2px;">SO: -</div>
      </div>
      <button type="button" onclick="closeModalById('modal-view-sched')" style="background: #f8fafc; border: none; width: 32px; height: 32px; border-radius: 50%; font-size: 18px; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">×</button>
    </div>

    <!-- Body -->
    <div style="padding: 24px; background: #fafafa;">
      
      <!-- Card หลักรวมข้อมูลสำคัญ -->
      <div style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        
        <!-- ชื่องาน & สถานะ -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #f1f3f5;">
          <div>
            <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">ชื่องาน</div>
            <div id="vs-job-title" style="font-size: 18px; font-weight: 800; color: #0f172a;">-</div>
          </div>
          <div id="vs-status" style="flex-shrink: 0;"><!-- Badge สถานะ --></div>
        </div>

        <!-- รายละเอียด Grid ย่อย (ประเภทงาน & ทีม) -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">ประเภทงาน</div>
            <div id="vs-job-type" style="font-size: 14px; font-weight: 600; color: #334155;">-</div>
          </div>
          <div>
            <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">ทีมรับผิดชอบ</div>
            <div id="vs-team-name" style="font-size: 14px; font-weight: 700; color: #3b5bdb;">-</div>
          </div>
        </div>

        <!-- ช่วงวันที่ทำงาน (Highlight Box) -->
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
          <div style="font-size: 20px;">📅</div>
          <div>
            <div id="vs-date-range" style="font-size: 14px; font-weight: 700; color: #166534;">-</div>
            <div id="vs-duration" style="font-size: 12px; font-weight: 600; color: #15803d; margin-top: 1px;">-</div>
          </div>
        </div>

      </div>

    </div>

    <!-- Footer -->
    <div style="background: #ffffff; padding: 16px 24px; border-top: 1px solid #f1f3f5; display: flex; justify-content: flex-end;">
      <button type="button" class="btn btn-primary" onclick="closeModalById('modal-view-sched')" style="padding: 8px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">ปิด</button>
    </div>

  </div>
</div>

<div class="overlay" id="modal-cust">
  <div class="pmodal pmodal-wide" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-header"><div><div class="modal-title" id="cust-modal-title">เพิ่มลูกค้าใหม่</div><div class="modal-subtitle" id="cust-modal-sub"></div></div><button class="modal-close" type="button" onclick="closeModalById('modal-cust')">×</button></div>
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
    <div class="pmodal-strip"></div>
    <div class="modal-header"><div><div class="modal-title" id="cd-name">รายละเอียดลูกค้า</div><div style="margin-top:4px" id="cd-type-tag"></div></div><button class="modal-close" type="button" onclick="closeModalById('modal-cust-detail')">×</button></div>
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

<div class="overlay" id="modal-add-wash">
  <div class="pmodal pmodal-sm" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-header"><div class="modal-title">เพิ่มประวัติการล้างแผง</div><button class="modal-close" type="button" onclick="closeModalById('modal-add-wash')">×</button></div>
    <div class="modal-body">
      <form method="POST" id="form-add-wash" action="">@csrf
        <div class="frow"><label class="flabel">วันที่ล้าง *</label><input class="finput" type="date" name="wash_date" id="aw-date" required></div>
        <div class="frow"><label class="flabel">ทีม / ช่างที่ล้าง *</label><select class="finput" name="tech" id="aw-tech" required><option value="">-- เลือก --</option>@foreach($teams as $t)@php $tn = data_get($t, 'team_name', ''); @endphp<option value="{{ $tn }}">{{ $tn }}</option>@endforeach<option value="ช่างภายนอก">ช่างภายนอก</option><option value="อื่นๆ">อื่นๆ</option></select></div>
        <div class="frow"><label class="flabel">หมายเหตุ</label><textarea class="finput" name="note" id="aw-note" rows="2"></textarea></div>
        <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-add-wash')">ยกเลิก</button><button type="submit" class="btn btn-solar">บันทึก</button></div>
      </form>
    </div>
  </div>
</div>

<div class="overlay" id="modal-add-milestone">
  <div class="pmodal pmodal-sm" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-header"><div class="modal-title">เพิ่ม Milestone</div><button class="modal-close" type="button" onclick="closeModalById('modal-add-milestone')">×</button></div>
    <div class="modal-body">
      <form method="POST" id="form-add-milestone" action="">@csrf
        <div class="frow"><label class="flabel">วันที่ *</label><input class="finput" type="date" name="milestone_date" id="am-date" required></div>
        <div class="frow"><label class="flabel">รายละเอียด *</label><textarea class="finput" name="milestone_note" id="am-note" rows="3" required></textarea></div>
        <div class="frow"><label class="flabel">บันทึกโดย</label><input class="finput" type="text" name="milestone_by" id="am-by"></div>
        <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-add-milestone')">ยกเลิก</button><button type="submit" class="btn btn-primary">บันทึก</button></div>
      </form>
    </div>
  </div>
</div>

<div class="overlay" id="modal-account">
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-header"><div class="modal-title" id="acc-modal-title">เพิ่มบัญชีผู้ใช้ Solar</div><button class="modal-close" type="button" onclick="closeModalById('modal-account')">×</button></div>
    <div class="modal-body">
      <form method="POST" id="form-account" action="{{ route('account.store') }}">@csrf
        <div class="fgrid">
          <div class="frow"><label class="flabel">เลขที่ / รหัส</label><input class="finput" type="text" name="no" id="af-no" readonly></div>
          <div class="frow"><label class="flabel">Inverter / ยี่ห้อ</label><input class="finput" type="text" name="inverter" id="af-inverter"></div>
          <div class="frow fcol-full"><label class="flabel">ชื่อระบบ / Platform *</label><input class="finput" type="text" name="plane" id="af-plane" required></div>
          <div class="frow fcol-full"><label class="flabel">ลูกค้า / สถานที่ติดตั้ง</label><div class="autocomp"><input class="finput" type="text" name="customer" id="af-customer" autocomplete="off" oninput="accCustAutocomp(this.value)"><div class="autocomp-list" id="af-cust-list"></div></div></div>
          <div class="frow"><label class="flabel">Username</label><input class="finput" type="text" name="username" id="af-username" autocomplete="off"></div>
          <div class="frow"><label class="flabel">Password</label><div style="position:relative"><input class="finput" type="password" name="password" id="af-password" autocomplete="new-password" style="padding-right:44px"><button type="button" onclick="toggleInputPw('af-password',this)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;font-weight:900;color:var(--muted)">ดู</button></div></div>
          <div class="frow"><label class="flabel">Email</label><input class="finput" type="text" name="email" id="af-email"></div>
          <div class="frow"><label class="flabel">App Password</label><div style="position:relative"><input class="finput" type="password" name="app_password" id="af-app_password" autocomplete="new-password" style="padding-right:44px"><button type="button" onclick="toggleInputPw('af-app_password',this)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;font-weight:900;color:var(--muted)">ดู</button></div></div>
        </div>
        <div class="factions"><button type="button" class="btn btn-ghost" onclick="closeModalById('modal-account')">ยกเลิก</button><button type="submit" class="btn btn-solar">บันทึก</button></div>
      </form>
    </div>
  </div>
</div>

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
              <input type="search" class="search-inp sched-list-search" id="tcal-list-search" placeholder="ค้นหา SO / งาน..." oninput="TCAL.renderList()">
            </div>
            <div class="sched-list-wrap">
              <table class="sched-list-table">
                <thead>
                  <tr>
                    <th style="width:60px">#</th>
                    <th style="width:150px">เลขงาน (SO)</th>
                    <th>รายละเอียดงาน</th>
                    <th style="width:200px">วันที่ทำงาน</th>
                    <th style="width:120px">สถานะงาน</th>
                  </tr>
                </thead>
                <tbody id="tcal-list-tbody"></tbody>
              </table>
            </div>
          </div>
          
          <!-- ✅ Container สำหรับแสดงแถบสีทีมช่างใน Modal -->
          <div id="tcal-team-color-legend-container" style="margin-top: 20px;"></div>

          <div style="margin-top:16px;display:flex;justify-content:flex-end;gap:10px">
            <button class="btn btn-ghost" type="button" onclick="closeTeamCalendar()">ปิด</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="overlay" id="modal-edit-sched">
  <div class="pmodal pmodal-wide" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-header">
      <div>
        <div class="modal-title">แก้ไขงาน</div>
        <div class="modal-subtitle" id="es-so-number">SO: -</div>
      </div>
      <button class="modal-close" type="button" onclick="closeModalById('modal-edit-sched')">×</button>
    </div>
    <div class="modal-body" style="padding:24px">
      <form id="form-edit-sched" method="POST" action="">
        @csrf
        <input type="hidden" name="_edit_sched" value="1">
        <div class="fgrid">
          <div class="frow"><label class="flabel">SO Number</label><input class="finput" type="text" name="so_number" id="es-so_number"></div>
          <div class="frow"><label class="flabel">ชื่องาน</label><input class="finput" type="text" name="job_title" id="es-job_title"></div>
          <div class="frow"><label class="flabel">ลูกค้า</label><input class="finput" type="text" name="customer_name" id="es-customer_name"></div>
          <div class="frow"><label class="flabel">ทีม</label><input class="finput" type="text" name="team_name" id="es-team_name"></div>
          <div class="frow"><label class="flabel">ประเภทงาน</label>
            <select class="finput" name="job_type" id="es-job_type">
              @foreach($jobTypes as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="frow"><label class="flabel">สถานะ</label>
            <select class="finput" name="status" id="es-status">
              <option value="upcoming">กำลังจะมา</option>
              <option value="doing">กำลังทำ</option>
              <option value="done">เสร็จแล้ว</option>
              <option value="cancel">ยกเลิก</option>
            </select>
          </div>
          <div class="frow fcol-full"><label class="flabel">สถานที่</label><input class="finput" type="text" name="job_location" id="es-job_location"></div>
          <div class="frow fcol-full"><label class="flabel">พิกัด GPS</label><input class="finput" type="text" name="job_la_long" id="es-job_la_long"></div>
          <div class="frow fcol-full"><label class="flabel">หมายเหตุ</label><textarea class="finput" name="note" id="es-note" rows="3"></textarea></div>
        </div>
        <div class="factions">
          <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-edit-sched')">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึก</button>
        </div>
      </form>
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
  const title = job?.job_title || job?.so_number || '-';
  const team = job?.team_name || 'ไม่ระบุทีม';
  const startDate = job?.start_date ? fmtDate(job.start_date) : '';
  const endDate = job?.end_date && job.end_date !== job.start_date ? fmtDate(job.end_date) : '';
  const dateRange = endDate ? `${startDate} - ${endDate}` : startDate;

  return `
    <div class="sched-event-title" style="font-size:12px; font-weight:800;">${escHtml(title)}</div>
    <div class="sched-event-meta" style="font-size:10px; line-height:1.5; margin-top:4px;">
      <div>👥 ${escHtml(team)}</div>
      <div>📅 ${escHtml(dateRange)}</div>
    </div>
  `;
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
function openProfileModal(t){CURRENT_PROFILE_TECH=t;const set=(id,v)=>{const el=document.getElementById(id);if(el)el.textContent=(v==null||v==='')?'-':v};const img=document.getElementById('m-img'),icon=document.getElementById('m-photo-icon');if(img){if(t.img){img.src=`/storage/${t.img}`;img.style.display='block';if(icon)icon.style.display='none'}else{img.removeAttribute('src');img.style.display='none';if(icon)icon.style.display='grid'}}set('m-name',t.emp_name||t.emp_id);set('m-name-eng',t.emp_name_eng);set('m-position',t.emp_position||'ลูกทีม');set('m-team',t.emp_team);set('m-empid',t.emp_id);set('m-nickname',t.emp_nickname);set('m-phone', fmtPhone(t.emp_phone));set('m-dob',t.date_of_birth?fmtDate(t.date_of_birth):'-');const isLeave=t.status==='leave';const statusEl=document.getElementById('m-status'),dotEl=document.getElementById('m-st-dot'),txtEl=document.getElementById('m-st-text');if(statusEl)statusEl.className='profile-v2-status '+(isLeave?'pv2-status-leave':'pv2-status-active');if(dotEl)dotEl.className='pv2-st-dot '+(isLeave?'pv2-dot-leave':'pv2-dot-active');if(txtEl)txtEl.textContent=isLeave?'ลาออก':'พร้อมทำงาน';const skills=(t.emp_skill||'').split(',').map(s=>s.trim()).filter(Boolean);document.getElementById('m-skills').innerHTML=skills.length?skills.map(s=>`<span class="pv2-tag">${escHtml(s)}</span>`).join(''):'<span class="pv2-muted">-</span>';const lv={none:'ไม่มี',basic:'พื้นฐาน',skill:'ชำนาญ',expert:'เชี่ยวชาญ'};const comps=t.core_competencies||{};const compEntries=Object.entries(comps).filter(([k,v])=>v&&v!=='none');document.getElementById('m-competencies').innerHTML=compEntries.length?compEntries.map(([k,v])=>`<div class="pv2-comp-item"><span class="pv2-comp-key">${escHtml(k)}</span><span class="pv2-comp-val cv-${escHtml(v)}">${lv[v]||escHtml(v)}</span></div>`).join(''):'<span class="pv2-muted">-</span>';const lics=t.licenses||[];document.getElementById('m-licenses').innerHTML=lics.length?lics.map(l=>`<div class="pv2-lic-item"><div class="pv2-lic-name">${escHtml(l.title||'-')}</div><div class="pv2-lic-meta">${l.doc_no?'เลขที่: '+escHtml(l.doc_no):''}${l.date_issued?' · '+escHtml(l.date_issued):''}${l.file?` · <a href="/storage/${escHtml(l.file)}" target="_blank" style="color:var(--blue);font-weight:900">เปิดไฟล์</a>`:''}</div></div>`).join(''):'<span class="pv2-muted">-</span>';const sw=t.software_tools||[];document.getElementById('m-software').innerHTML=sw.length?sw.map(s=>`<span class="pv2-tag pv2-tag-sw">${escHtml(s)}</span>`).join(''):'<span class="pv2-muted">-</span>';openModal('overlay')}
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
html+=`<div class="sched-day ${ds===today?'today':''}"><div class="sched-day-num">${d}</div>${dayJobs.length>0?`<div class="sched-day-count">${dayJobs.length}</div>`:''}${visible.map(s=>{const tc=getTeamColor(s.team_name);return`<button type="button" class="sched-event ${this.eventClass(s)}" style="background:${tc.bg} !important;border-left:4px solid ${tc.border} !important;color:${tc.text} !important;" data-sched-id="${escHtml(s.id||'')}" data-sched='${JSON.stringify(s).replace(/'/g,"&#39;")}' onclick="event.stopPropagation();closeTeamCalendar();setTimeout(()=>openEditSchedFromEl(this),100)">${renderCalendarEventContent(s)}</button>`;}).join('')}${dayJobs.length>2?`<button type="button" class="sched-more" onclick="event.stopPropagation();openScheduleDayPopup('${ds}','team')">+${dayJobs.length-2} รายการ</button>`:''}</div>`;
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
window.SCHED_BOARD = {
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
const tbody = document.getElementById('sched-list-tbody');
const countEl = document.getElementById('sched-list-count');
if(!tbody) return;
const kw = (document.getElementById('sched-list-search')?.value || '').toLowerCase().trim();
let jobs = this.monthJobs();
if(kw) jobs = jobs.filter(s =>
(`${s.customer_name||''} ${s.job_title||''}`).toLowerCase().includes(kw)
);
jobs.sort((a,b) => (a.start_date||'').localeCompare(b.start_date||''));
if(countEl) countEl.textContent = `${jobs.length} งาน`;
if(!jobs.length){
tbody.innerHTML = `<tr><td colspan="5" class="sched-list-empty">ไม่มีงานในเดือนนี้</td></tr>`;
return;
}
tbody.innerHTML = jobs.map((s,i) => {
const st = this.jobStatus(s);
const sameDay = s.start_date === s.end_date;
const dateHtml = sameDay
? fmtDate(s.start_date)
: `${fmtDate(s.start_date)}<small>ถึง ${fmtDate(s.end_date)}</small>`;
return `<tr data-sched-id="${escHtml(s.id||'')}" data-sched='${JSON.stringify(s).replace(/'/g,"&#39;")}' onclick="closeTeamCalendar();setTimeout(()=>openEditSchedFromEl(this),100)" style="cursor:pointer">
<td>${i+1}</td>
<td><div class="sched-list-cust">${escHtml(s.customer_name||'-')}</div>${s.job_location?`<div style="font-size:11px;color:#64748b;font-weight:700">${escHtml(s.job_location)}</div>`:''}</td>
<td>
<div class="sched-list-job">${escHtml(s.job_title||'-')}</div>
<span class="job-type-tag jt-${escHtml(s.job_type||'general')}" style="margin-top:4px">${escHtml(JOB_TYPES[s.job_type||'general']||s.job_type||'-')}</span>
</td>
<td><div class="sched-list-date">${dateHtml}</div></td>
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
${visible.map(s=>{const tc=getTeamColor(s.team_name);return`<button type="button" class="sched-event ${this.eventClass(s)}" style="background:${tc.bg} !important;border-left:4px solid ${tc.border} !important;color:${tc.text} !important;" data-sched-id="${escHtml(s.id||'')}" data-sched='${JSON.stringify(s).replace(/'/g,"&#39;")}' onclick="openEditSchedFromEl(this)">${renderCalendarEventContent(s)}</button>`;}).join('')}
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
<select class="sched-status-select" data-sched-id="${escHtml(job.id || '')}" data-prev="${st.key}" onclick="event.stopPropagation()" onchange="updateScheduleStatus(this)">
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
headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
body: JSON.stringify({ status }),
});
if (!res.ok) throw new Error('status update failed');
// อัปเดตข้อมูลใน SCHED_DATA ด้วย
SCHED_DATA.forEach(job => {
if (String(job.id) === String(id)) job.status = status;
});
sel.dataset.prev = status;
// สั่ง Render ใหม่
if (window.SCHED_BOARD && typeof window.SCHED_BOARD.render === 'function') {
window.SCHED_BOARD.render();
}
if (document.getElementById('tcal-overlay')?.classList.contains('open')) {
if (window.TCAL && typeof window.TCAL.render === 'function') {
window.TCAL.jobs = SCHED_DATA.filter(job => job.team_name === window.TCAL.team);
window.TCAL.render();
}
}
} catch (err) {
alert('บันทึกสถานะไม่สำเร็จ');
sel.value = prev;
setScheduleStatusClass(sel, prev);
} finally {
sel.disabled = false;
}
};
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
// ✅ เพิ่ม: ถ้าไม่มี form แก้ไข ให้ fallback ไปเปิด View Mode
const editForm = document.getElementById('form-edit-sched');
if (!editForm) {
openViewSchedule(btn);
return;
}
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
editForm.action = URL_SCHED_UPDATE(s.id || btn.dataset.schedId);
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
openViewSchedule(btn);  // ← เปลี่ยนเป็นเปิด View Mode
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
<script>
/* === START: API FETCH WITH PROPER MAPPING (NO "งานทั่วไป") === */

// ✅ ชุดสีพื้นหลังสำหรับแต่ละทีม/คน (แยกสีอัตโนมัติ)
const TEAM_COLORS = [
  { bg: '#dbeafe', border: '#3b82f6', text: '#1e40af' },  // ฟ้า
  { bg: '#fce7f3', border: '#ec4899', text: '#9d174d' },  // ชมพู
  { bg: '#d1fae5', border: '#10b981', text: '#065f46' },  // เขียว
  { bg: '#fef3c7', border: '#f59e0b', text: '#92400e' },  // เหลือง
  { bg: '#ede9fe', border: '#8b5cf6', text: '#5b21b6' },  // ม่วง
  { bg: '#ffedd5', border: '#f97316', text: '#9a3412' },  // ส้ม
  { bg: '#e0f2fe', border: '#0ea5e9', text: '#0c4a6e' },  // ฟ้าอ่อน
  { bg: '#fecdd3', border: '#f43f5e', text: '#9f1239' },  // แดง
  { bg: '#f0fdf4', border: '#22c55e', text: '#14532d' },  // เขียวอ่อน
  { bg: '#fdf4ff', border: '#d946ef', text: '#86198f' },  // ม่วงชมพู
];

const _teamColorMap = {};
let _teamColorIdx = 0;

function getTeamColor(teamName) {
  const key = (teamName || '-').trim();
  if (!_teamColorMap[key]) {
    _teamColorMap[key] = TEAM_COLORS[_teamColorIdx % TEAM_COLORS.length];
    _teamColorIdx++;
  }
  return _teamColorMap[key];
}

// ✅ 1. ฟังก์ชัน Render ตารางจากข้อมูล API (6 คอลัมน์, ไม่มี SO, สถานะเป็น badge, ไม่มี "งานทั่วไป")
function renderScheduleFromAPI(data) {
  const tbody = document.getElementById('sched-list-tbody');
  const countEl = document.getElementById('sched-list-count');
  const grid = document.getElementById('sched-month-grid');
  if (!tbody) return;

  // เรียงตามวันที่
  data.sort((a, b) => (a.start_date || '').localeCompare(b.start_date || ''));

  // Update จำนวนงาน
  if (countEl) countEl.textContent = `${data.length} งาน`;

  // ✅ Render ตาราง 6 คอลัมน์ + สีพื้นหลังแต่ละทีม
  tbody.innerHTML = data.map((s, i) => {
    const sameDay = s.start_date === s.end_date;
    const dateHtml = sameDay
      ? fmtDate(s.start_date)
      : `${fmtDate(s.start_date)} <small>ถึง ${fmtDate(s.end_date)}</small>`;

    const statusKey = s.status || 'upcoming';
    const statusLabel = {
      'upcoming': 'กำลังจะมา',
      'doing': 'กำลังทำ',
      'done': 'เสร็จแล้ว',
      'cancel': 'ยกเลิก'
    }[statusKey] || statusKey;

    const statusClass = {
      'upcoming': 'sls-upcoming',
      'doing': 'sls-doing',
      'done': 'sls-done',
      'cancel': 'sls-cancel'
    }[statusKey] || 'sls-upcoming';

    // ✅ ลบคำว่า "งานทั่วไป" / "ทั่วไป" ออก
    let typeLabel = (window.JOB_TYPES && window.JOB_TYPES[s.job_type]) || s.job_type || '';
    if (typeLabel === 'งานทั่วไป' || typeLabel === 'ทั่วไป' || typeLabel === 'general') {
      typeLabel = '';
    }
    const typeClass = `jt-${s.job_type || 'general'}`;

    // ✅ สีพื้นหลังตามทีม
    const tc = getTeamColor(s.team_name);

    return `<tr data-sched-id="${escHtml(s.id)}" data-sched='${JSON.stringify(s).replace(/'/g,"&#39;")}' onclick="openViewSchedule(this)" style="cursor:pointer; border-left:4px solid ${tc.border}; background:${tc.bg}20;">
      <td>${i + 1}</td>
      <td><div class="sched-list-date">${dateHtml}</div></td>
      <td><span class="sched-list-team" style="color:${tc.text}; font-weight:600;">${escHtml(s.team_name || '-')}</span></td>
      <td>
        <div class="sched-list-job">${escHtml(s.job_title || '-')}</div>
        ${typeLabel ? `<span class="job-type-tag ${typeClass}" style="margin-top:4px">${escHtml(typeLabel)}</span>` : ''}
      </td>
      <td>
        <span class="sched-status-control ${statusClass}">
          ${escHtml(statusLabel)}
        </span>
      </td>
    </tr>`;
  }).join('');

  // ✅ Render ปฏิทิน
  if (grid) {
    renderCalendarFromAPI(data);
  }

  console.log("✅ renderScheduleFromAPI สำเร็จ!");
}

// ✅ 2. ฟังก์ชัน Render ปฏิทินจากข้อมูล API
function renderCalendarFromAPI(data) {
  const grid = document.getElementById('sched-month-grid');
  if (!grid) return;

  const firstJob = data[0];
  if (!firstJob) return;

  const jobDate = new Date(firstJob.start_date);
  const year = jobDate.getFullYear();
  const month = jobDate.getMonth();

  const thMonths = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน',
    'กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
  const monthText = `${thMonths[month]} ${year + 543}`;
  const controlMonth = document.getElementById('sched-board-control-month');
  if (controlMonth) controlMonth.textContent = monthText;

  const eyebrow = document.querySelector('.sched-eyebrow');
  if (eyebrow) eyebrow.textContent = `SCHEDULE · ${thMonths[month].toUpperCase()} ${year + 543}`;

  const first = new Date(year, month, 1).getDay();
  const total = new Date(year, month + 1, 0).getDate();
  const prev = new Date(year, month, 0).getDate();
  const today = ymd(new Date());

  let html = '';

  for (let i = first - 1; i >= 0; i--) {
    html += `<div class="sched-day other"><div class="sched-day-num">${prev - i}</div></div>`;
  }

  for (let d = 1; d <= total; d++) {
    const ds = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    const dayJobs = data.filter(s => s.start_date <= ds && s.end_date >= ds);
    const visible = dayJobs.slice(0, 2);

    html += `<div class="sched-day ${ds === today ? 'today' : ''}">
      <div class="sched-day-num">${d}</div>
      ${dayJobs.length > 0 ? `<div class="sched-day-count">${dayJobs.length}</div>` : ''}
      ${visible.map(s => {
        const typeClass = teamEventClass(s);

        // ✅ ลบคำว่า "งานทั่วไป" ในปฏิทินด้วย
        let typeLabel = (window.JOB_TYPES && window.JOB_TYPES[s.job_type]) || s.job_type || '';
        if (typeLabel === 'งานทั่วไป' || typeLabel === 'ทั่วไป' || typeLabel === 'general') {
          typeLabel = '';
        }

        const title = s.job_title || '-';
        const customer = s.customer_name || '-';
        const team = s.team_name || '-';
        const startDate = s.start_date ? fmtDate(s.start_date) : '';
        const endDate = s.end_date && s.end_date !== s.start_date ? fmtDate(s.end_date) : '';
        const dateRange = endDate ? `${startDate} - ${endDate}` : startDate;

        // ✅ สีพื้นหลังแยกตามทีม/คน
        const tc = getTeamColor(s.team_name);

        return `<button type="button" class="sched-event ${typeClass}"
          style="background:${tc.bg}; border-left:4px solid ${tc.border}; color:${tc.text};"
          data-sched-id="${escHtml(s.id)}"
          data-sched='${JSON.stringify(s).replace(/'/g,"&#39;")}'
          onclick="event.stopPropagation();openEditSchedFromEl(this)">
          <div class="sched-event-title" style="font-size:12px; font-weight:800;">${escHtml(title)}</div>
          <div class="sched-event-meta" style="font-size:10px; line-height:1.5; margin-top:4px;">
            <div>👥 ${escHtml(team)}</div>
            <div>📅 ${escHtml(dateRange)}</div>
          </div>
        </button>`;
      }).join('')}
      ${dayJobs.length > 2 ? `<button type="button" class="sched-more" onclick="event.stopPropagation();openScheduleDayPopup('${ds}','main')">+${dayJobs.length - 2} รายการ</button>` : ''}
    </div>`;
  }

  const rest = (7 - ((first + total) % 7)) % 7;
  for (let i = 1; i <= rest; i++) {
    html += `<div class="sched-day other"><div class="sched-day-num">${i}</div></div>`;
  }

  grid.innerHTML = html;
}

// ✅ 3. ฟังก์ชัน Fallback
function renderScheduleTableFallback(data) {
  renderScheduleFromAPI(data);
  console.log("✅ วาดตารางด้วย Fallback สำเร็จ!");
}

// ✅ 4. ดึงข้อมูลจาก API
document.addEventListener('DOMContentLoaded', async function() {
  console.log("🚀 เริ่มต้นดึงข้อมูลตารางงานจาก API...");

  const grid = document.getElementById('sched-month-grid');
  const tbody = document.getElementById('sched-list-tbody');
  const countEl = document.getElementById('sched-list-count');

  if (grid) grid.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:40px; color:#868e96;">🔄 กำลังโหลดข้อมูลจาก API...</div>';
  if (tbody) tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:40px; color:#868e96;">กำลังโหลดข้อมูล...</td></tr>';

  try {
    const response = await fetch("https://app-mobile.hikaripower.com/api/schedules", {
      method: "GET",
      headers: {
        "x-api-key": "hikari20259f3c6e1b0f2d9c9c0e5e0b4d8b4e6e9c0c6c2f3e7b8a9f1d2e3c4b5a6f7d8e9",
        "Accept": "application/json"
      }
    });

    if (!response.ok) throw new Error(`HTTP Error! สถานะ: ${response.status}`);
    const rawData = await response.json();
    const dataArray = Array.isArray(rawData) ? rawData : (rawData.data || rawData.schedules || []);

    console.log("📦 ข้อมูลดิบจาก API:", dataArray);

    if (dataArray.length === 0) {
      if (tbody) tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:40px;">ไม่พบข้อมูลตารางงาน</td></tr>';
      if (grid) grid.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:40px;">ไม่พบข้อมูล</div>';
      if (countEl) countEl.textContent = '0 งาน';
      return;
    }

    const mappedData = dataArray.map(item => {
      let startDate = item.start_date || item.date || new Date().toISOString().split('T')[0];
      let endDate = item.end_date || startDate;

      let uiStatus = 'upcoming';
      if (item.status === 'active' || item.status === 'doing') uiStatus = 'doing';
      else if (item.status === 'done' || item.status === 'completed') uiStatus = 'done';
      else if (item.status === 'cancel' || item.status === 'cancelled') uiStatus = 'cancel';

      return {
        id: String(item.id || `temp_${Math.random().toString(36).substr(2, 9)}`),
        job_title: item.work_detail || item.job_title || '-',
        team_name: item.supervisor || item.team_name || '-',
        customer_name: item.company || item.customer_name || '-',
        so_number: item.so_number || String(item.id) || '-',
        job_type: item.job_type || 'general',
        start_date: startDate,
        end_date: endDate,
        status: uiStatus,
        job_location: item.job_location || item.location || '',
        note: item.note || item.remark || ''
      };
    });

    console.log("✅ ข้อมูลที่ Map แล้ว:", mappedData);
    window.SCHED_DATA = [...window.SCHED_DATA, ...mappedData];

    setTimeout(() => {
      renderScheduleFromAPI(mappedData);
    }, 100);

  } catch (error) {
    console.error("❌ เกิดข้อผิดพลาด:", error);
    const errorMsg = error.message.includes('CORS') ? 'ติดปัญหา CORS (Browser บล็อก)' : error.message;
    if (grid) {
      grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:#fa5252;">❌ ไม่สามารถโหลดข้อมูลได้<br><small>${errorMsg}</small></div>`;
    }
    if (tbody) {
      tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:40px; color:#fa5252;">❌ โหลดข้อมูลไม่สำเร็จ</td></tr>`;
    }
  }
});
/* === END: API FETCH WITH PROPER MAPPING === */

// ✅ เปิด Modal ดูข้อมูลงาน (Read-only)
window.openViewSchedule = function(btn) {
  if (!btn) return;
  let s = null;
  try {
    s = btn.dataset.sched ? JSON.parse(btn.dataset.sched) : null;
  } catch (e) {
    s = null;
  }
  if (!s && btn.dataset.schedId) {
    s = window.SCHED_DATA.find(job => String(job.id) === String(btn.dataset.schedId));
  }
  if (!s) return;

  const setTxt = (id, val) => { const el = document.getElementById(id); if(el) el.textContent = val || '-'; };
  const setHtml = (id, val) => { const el = document.getElementById(id); if(el) el.innerHTML = val; };

  setTxt('vs-so-number', `SO: ${s.so_number || '-'}`);
  setTxt('vs-job-title', s.job_title);

  // ✅ ซ่อน customer_name ไม่แสดง
  const custEl = document.getElementById('vs-customer-name');
  if (custEl) {
    custEl.textContent = '';
    custEl.style.display = 'none';
    if (custEl.closest('.vs-row, .sched-view-row, [class*="row"]')) {
      custEl.closest('.vs-row, .sched-view-row, [class*="row"]').style.display = 'none';
    }
  }

  const jobTypeLabel = (window.JOB_TYPES && window.JOB_TYPES[s.job_type || 'general']) || s.job_type || 'ทั่วไป';
  setHtml('vs-job-type', `<span class="job-type-tag jt-${s.job_type || 'general'}">${jobTypeLabel}</span>`);

  const st = typeof resolveScheduleStatus === 'function' ? resolveScheduleStatus(s) : { cls: 'sls-upcoming', label: s.status || 'upcoming' };
  setHtml('vs-status', `<span class="sched-status-control ${st.cls}" style="padding:6px 12px">${st.label}</span>`);

  setTxt('vs-team-name', s.team_name);

  const startDate = typeof fmtDate === 'function' ? fmtDate(s.start_date) : s.start_date;
  const endDate = (s.end_date && s.end_date !== s.start_date) ? (typeof fmtDate === 'function' ? fmtDate(s.end_date) : s.end_date) : '';
  const dateRange = endDate ? `${startDate} - ${endDate}` : startDate;
  setTxt('vs-date-range', dateRange);

  if (s.start_date && s.end_date) {
    const days = typeof daysBetween === 'function' ? (daysBetween(s.start_date, s.end_date) + 1) : 1;
    setTxt('vs-duration', `ระยะเวลา ${days} วัน`);
  } else {
    setTxt('vs-duration', '');
  }

  if (typeof openModalById === 'function') {
    openModalById('modal-view-sched');
  } else if (typeof openModal === 'function') {
    openModal('modal-view-sched');
  }
};
</script>

</body>
</html>
