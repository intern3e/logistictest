{{--
    resources/views/project/dashboardtechnician.blade.php

    ตัวแปรจาก TechnicianController@index :
      $technicians, $schedules, $teams, $availableTeams
      $stats, $teamFilter, $search
      $customers, $accounts, $washAlerts, $custSummary
      $skillOptions, $competencyList, $competencyLevels, $softwareOptions, $jobTypes
--}}
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Google Drive Dashboard - Technician Skills</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --google-blue: #1a73e8;
            --sidebar-blue: #0b57d0;
            --navy: #0b2149;
            --bg-light: #f8f9fa;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: white;
            margin: 0; padding: 0;
            height: 100vh;
            display: flex;
            overflow: hidden;
        }
        .main-container {
            background-color: white;
            width: 100%; height: 100%;
            display: flex; flex-direction: column;
            overflow: hidden;
            border-radius: 0;
        }
        .sidebar-blue { background-color: var(--sidebar-blue); border-top-right-radius: 40px; }
        .search-bar { background-color: #f1f3f4; }
        .sidebar-item { color: rgba(255,255,255,0.8); transition: all 0.2s; cursor: pointer; }
        .sidebar-item:hover { background-color: rgba(255,255,255,0.1); color: white; }
        .sidebar-item.active { background-color: rgba(255,255,255,0.2); color: white; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #dadce0; border-radius: 10px; }
        .content-section { display: none; }
        .content-section.active { display: block; }

        /* ───── VIEW TOGGLE (รายคน / รายทีม) ───── */
        .view-toggle {
            display: inline-flex;
            background: #f1f3f4;
            padding: 4px;
            border-radius: 999px;
            gap: 2px;
        }
        .view-toggle button {
            border: none;
            background: transparent;
            padding: 8px 20px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            color: #5f6368;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            font-family: inherit;
        }
        .view-toggle button:hover { color: var(--navy); }
        .view-toggle button.active {
            background: white;
            color: var(--google-blue);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .view-pane { display: none; }
        .view-pane.active { display: block; }

        /* ═══════════════════════════════════════════════
           PROFILE CARD (Resume-style — view รายคน)
        ═══════════════════════════════════════════════ */
        .profile-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        @media (max-width: 1200px) {
            .profile-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 700px) {
            .profile-grid { grid-template-columns: 1fr; }
        }

        .profile-card {
            position: relative;
            background: white;
            border: 1px solid #e8eaed;
            border-radius: 14px;
            padding: 14px 16px;
            overflow: hidden;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            gap: 10px;
            cursor: pointer;
        }
        .profile-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.07);
            border-color: #c2d4f5;
        }
        .profile-accent {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            border-radius: 14px 14px 0 0;
        }
        .profile-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-top: 4px;
        }
        .profile-avatar {
            position: relative;
            width: 56px; height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
            letter-spacing: 0.5px;
            flex-shrink: 0;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-name-block {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding-top: 2px;
        }
        .profile-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--navy);
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .profile-pos {
            font-size: 11px;
            color: #5f6368;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-auto-rows: 1fr;
            gap: 8px;
            padding: 10px 0 4px;
            border-top: 1px solid #f1f3f4;
        }
        .stat-box {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
            padding: 6px 10px;
            min-height: 44px;
            background: #f8fafe;
            border: 1px solid #e8eaed;
            border-radius: 8px;
            transition: all 0.15s;
        }
        .stat-box:hover { background: #eef4fc; border-color: #c2d4f5; }
        .stat-box.is-empty { background: #fafbfc; border-style: dashed; border-color: #e0e3e7; }
        .stat-box.is-empty:hover { background: #fafbfc; border-color: #e0e3e7; }
        .stat-key {
            font-size: 9px; font-weight: 700; color: #9aa0a6;
            letter-spacing: 0.5px; text-transform: uppercase;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .stat-val {
            font-size: 12px; font-weight: 700; color: var(--navy);
            line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .skill-tags {
            display: flex; flex-wrap: wrap; gap: 6px;
            padding-top: 6px; border-top: 1px solid #f1f3f4;
        }
        .skill-tag {
            font-size: 10px; color: #5f6368;
            background: #f8f9fa; padding: 3px 8px;
            border-radius: 6px; font-weight: 500;
        }
        .skill-tag-more { font-size: 10px; color: #9aa0a6; padding: 3px 4px; font-weight: 600; }
        .profile-card.is-head { background: linear-gradient(180deg, #fffbeb 0%, white 25%); }

        /* ═══════════════════════════════════════════════
           TEAM FOLDER CARD
        ═══════════════════════════════════════════════ */
        .folder-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 18px;
        }
        @media (max-width: 1300px) { .folder-grid { grid-template-columns: repeat(4, 1fr); } }
        @media (max-width: 1000px) { .folder-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 700px)  { .folder-grid { grid-template-columns: repeat(2, 1fr); } }
        .folder-card {
            position: relative; cursor: pointer;
            transition: transform 0.22s ease, filter 0.22s ease;
            min-height: 195px; color: white; padding: 28px 20px 18px;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300' preserveAspectRatio='none'><path d='M 0 18 Q 0 0 18 0 L 86 0 Q 100 0 106 14 L 112 26 Q 116 32 128 32 L 282 32 Q 300 32 300 50 L 300 282 Q 300 300 282 300 L 18 300 Q 0 300 0 282 Z' fill='%231a73e8'/></svg>");
            background-size: 100% 100%; background-repeat: no-repeat;
            filter: drop-shadow(0 6px 14px rgba(26, 115, 232, 0.28));
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .folder-card:hover {
            transform: translateY(-4px);
            filter: drop-shadow(0 12px 22px rgba(26, 115, 232, 0.40));
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300' preserveAspectRatio='none'><path d='M 0 18 Q 0 0 18 0 L 86 0 Q 100 0 106 14 L 112 26 Q 116 32 128 32 L 282 32 Q 300 32 300 50 L 300 282 Q 300 300 282 300 L 18 300 Q 0 300 0 282 Z' fill='%231565d8'/></svg>");
        }
        .folder-top { display: flex; flex-direction: column; gap: 14px; }
        .folder-team-info { margin-top: 4px; }
        .folder-team-label { font-size: 10px; font-weight: 600; color: rgba(255,255,255,0.78); margin-bottom: 4px; }
        .folder-team-name {
            font-size: 16px; font-weight: 700; color: white;
            line-height: 1.2; display: -webkit-box; -webkit-line-clamp: 1;
            -webkit-box-orient: vertical; overflow: hidden;
        }
        .folder-head-note {
            font-size: 10px; color: rgba(255,255,255,0.78); margin-top: 3px;
            display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;
        }
        .folder-bottom { margin-top: auto; }
        .folder-divider { height: 1px; background: rgba(255,255,255,0.22); margin: 0 0 10px; }
        .folder-icons { display: flex; align-items: center; gap: 6px; font-size: 11px; color: rgba(255,255,255,0.9); font-weight: 500; }
        .folder-icons svg { flex-shrink: 0; }
        .avatar-stack { display: flex; align-items: center; }
        .avatar-stack .av {
            width: 28px; height: 28px; border-radius: 50%;
            border: 2px solid var(--google-blue); background: #e8eaed;
            object-fit: cover; margin-left: -8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; color: var(--google-blue);
            overflow: hidden; flex-shrink: 0;
            transition: border-color 0.22s ease;
        }
        .avatar-stack .av:first-child { margin-left: 0; }
        .folder-card:hover .avatar-stack .av { border-color: #1565d8; }
        .avatar-stack .av.head-av {
            background: linear-gradient(135deg, #fde68a, #f59e0b);
            color: #78350f;
            box-shadow: 0 0 0 1.5px rgba(255,255,255,0.6);
        }
        .avatar-stack .av.more { background: #f1f3f4; color: #5f6368; font-size: 10px; }

        /* ═══════════════════════════════════════════════
           MEMBER FOLDER (in popup)
        ═══════════════════════════════════════════════ */
        .member-folder-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px; margin-bottom: 8px;
        }
        @media (max-width: 1100px) { .member-folder-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 800px)  { .member-folder-grid { grid-template-columns: repeat(2, 1fr); } }
        .member-folder {
            position: relative; cursor: pointer;
            transition: transform 0.22s ease, filter 0.22s ease;
            min-height: 180px; color: white; padding: 28px 20px 18px;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300' preserveAspectRatio='none'><path d='M 0 18 Q 0 0 18 0 L 86 0 Q 100 0 106 14 L 112 26 Q 116 32 128 32 L 282 32 Q 300 32 300 50 L 300 282 Q 300 300 282 300 L 18 300 Q 0 300 0 282 Z' fill='%231a74e8'/></svg>");
            background-size: 100% 100%; background-repeat: no-repeat;
            display: flex; flex-direction: column; justify-content: space-between;
            filter: drop-shadow(0 4px 10px rgba(26, 116, 232, 0.30));
        }
        .member-folder:hover { transform: translateY(-3px); filter: drop-shadow(0 10px 18px rgba(26, 116, 232, 0.45)); }
        .member-folder.is-head {
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300' preserveAspectRatio='none'><path d='M 0 18 Q 0 0 18 0 L 86 0 Q 100 0 106 14 L 112 26 Q 116 32 128 32 L 282 32 Q 300 32 300 50 L 300 282 Q 300 300 282 300 L 18 300 Q 0 300 0 282 Z' fill='%23f59e0b'/></svg>");
            filter: drop-shadow(0 4px 10px rgba(245, 158, 11, 0.35));
        }
        .member-folder.is-head:hover { filter: drop-shadow(0 10px 18px rgba(245, 158, 11, 0.50)); }
        .member-folder-avatar {
            width: 44px; height: 44px; border-radius: 50%;
            background: white; color: #1a74e8;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 16px; flex-shrink: 0;
            overflow: hidden; margin-bottom: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .member-folder-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .member-folder.is-head .member-folder-avatar { background: white; color: #92400e; }
        .member-folder-name {
            font-size: 14px; font-weight: 700; line-height: 1.25; color: white; margin-top: 8px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .member-folder-skill {
            font-size: 11px; color: rgba(255,255,255,0.85); margin-top: 4px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .member-folder-bottom { margin-top: auto; }
        .member-folder-divider { height: 1px; background: rgba(255,255,255,0.30); margin: 0 0 10px; }
        .member-folder-badge-row { display: flex; align-items: center; gap: 6px; }
        .member-folder-badge {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 10px; font-weight: 700; padding: 3px 8px;
            border-radius: 999px; text-transform: uppercase; letter-spacing: 0.3px;
            background: rgba(255,255,255,0.25); color: white;
        }
        .member-folder.is-head .member-folder-badge { background: rgba(255,255,255,0.35); }

        /* ═══════════════════════════════════════════════
           TEAM POPUP MODAL
        ═══════════════════════════════════════════════ */
        .team-modal-header {
            display: flex; align-items: center; justify-content: space-between;
            background: linear-gradient(135deg, #1a73e8 0%, #0b57d0 100%);
            color: white; padding: 24px 28px; margin: -24px -28px 36px;
            position: sticky; top: 6px; z-index: 5;
        }
        .team-modal-header-left { display: flex; align-items: center; gap: 16px; }
        .team-modal-icon {
            width: 52px; height: 52px; border-radius: 14px;
            background: rgba(255,255,255,0.22);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .team-modal-title { font-size: 20px; font-weight: 700; line-height: 1.2; }
        .team-modal-sub { font-size: 12px; opacity: 0.9; margin-top: 3px; }
        .team-modal-stats { display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }
        .team-modal-stat {
            background: rgba(255,255,255,0.2); padding: 5px 12px;
            border-radius: 999px; font-size: 11px; font-weight: 600; white-space: nowrap;
        }
        .team-modal-close-x {
            position: absolute; top: 14px; right: 18px;
            background: rgba(255,255,255,0.2); border: none; color: white;
            width: 32px; height: 32px; border-radius: 50%; cursor: pointer;
            font-size: 18px; display: flex; align-items: center; justify-content: center;
            line-height: 1; transition: background 0.15s;
        }
        .team-modal-close-x:hover { background: rgba(255,255,255,0.35); }
        .team-section-label {
            font-size: 11px; font-weight: 700; letter-spacing: 1px;
            color: #5f6368; text-transform: uppercase;
            margin: 18px 0 14px;
            display: flex; align-items: center; gap: 8px;
        }
        #tm-head-section > .team-section-label:first-child,
        #tm-member-section:first-of-type > .team-section-label:first-child { margin-top: 4px; }
        .team-section-label::after { content: ""; flex: 1; height: 1px; background: #e8eaed; }

        /* ═══════════════════════════════════════════════
           PROFILE PAGE V6 (Clean & Simple)
        ═══════════════════════════════════════════════ */

        /* ปุ่มกลับ */
        .pp-back {
            display: inline-flex; align-items: center; gap: 6px;
            background: white; border: 1px solid #e8eaed; color: #1a74e8;
            font-weight: 600; font-size: 13px;
            padding: 8px 16px; border-radius: 999px; cursor: pointer;
            font-family: inherit; margin-bottom: 16px;
            transition: all 0.15s;
        }
        .pp-back:hover { background: #e8f0fe; border-color: #1a74e8; }

        /* Header bar — รูป + ชื่อ + ตำแหน่ง */
        .pp6-header {
            display: flex; align-items: center; gap: 24px;
            padding: 24px 28px;
            background: white;
            border: 1px solid #e8eaed;
            border-radius: 14px;
            margin-bottom: 16px;
        }
        .pp6-avatar {
            width: 90px; height: 90px;
            border-radius: 16px;
            background: linear-gradient(135deg, #4a91e8, #1a74e8);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 36px; font-weight: 800;
            flex-shrink: 0; overflow: hidden;
        }
        .pp6-avatar.is-head { background: linear-gradient(135deg, #fbbf24, #d97706); }
        .pp6-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .pp6-head-info { flex: 1; min-width: 0; }
        .pp6-name {
            font-size: 22px; font-weight: 800; color: #0b2149;
            margin-bottom: 4px; line-height: 1.2;
        }
        .pp6-name-eng {
            font-size: 13px; color: #5f6368; font-weight: 500; margin-bottom: 8px;
        }
        .pp6-tags { display: flex; flex-wrap: wrap; gap: 6px; }
        .pp6-tag {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; font-weight: 600;
            padding: 4px 10px; border-radius: 999px;
            background: #f1f3f4; color: #5f6368;
        }
        .pp6-tag.head { background: #fef3c7; color: #92400e; }
        .pp6-tag.team { background: #e8f0fe; color: #1a74e8; }

        /* Content: 2 columns */
        .pp6-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 16px;
        }
        @media (max-width: 900px) {
            .pp6-grid { grid-template-columns: 1fr; }
        }
        .pp6-col { display: flex; flex-direction: column; gap: 16px; }

        /* Card */
        .pp6-card {
            background: white;
            border: 1px solid #e8eaed;
            border-radius: 12px;
            padding: 18px 20px;
        }
        .pp6-title {
            font-size: 13px; font-weight: 700; color: #0b2149;
            margin-bottom: 14px; padding-bottom: 10px;
            border-bottom: 1px solid #f1f3f4;
            display: flex; align-items: center; gap: 8px;
        }
        .pp6-title svg { width: 16px; height: 16px; color: #1a74e8; flex-shrink: 0; }

        /* Info rows */
        .pp6-rows { display: flex; flex-direction: column; }
        .pp6-row {
            display: grid;
            grid-template-columns: 110px 1fr;
            gap: 12px;
            padding: 8px 0;
            font-size: 13px;
            align-items: center;
        }
        .pp6-row + .pp6-row { border-top: 1px solid #f8f9fa; }
        .pp6-row-k { color: #5f6368; font-weight: 500; }
        .pp6-row-v { color: #0b2149; font-weight: 600; word-break: break-word; }

        /* Competency — flat simple */
        .pp6-comps { display: flex; flex-direction: column; gap: 6px; }
        .pp6-comp {
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px; padding: 10px 14px;
            background: #f8fafe; border-radius: 8px;
            font-size: 13px;
        }
        .pp6-comp-name { color: #0b2149; font-weight: 600; }
        .pp6-comp-lv {
            font-size: 11px; font-weight: 700;
            padding: 3px 12px; border-radius: 999px;
        }
        .pp6-comp-lv.lv1 { background: #1a74e8; color: white; }
        .pp6-comp-lv.lv2 { background: #93c5fd; color: #0b2149; }
        .pp6-comp-lv.lv3 { background: #dbeafe; color: #1e40af; }

        /* Chips — uniform size */
        .pp6-chips {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 6px;
        }
        .pp6-chip {
            display: flex; align-items: center; gap: 6px;
            padding: 8px 14px;
            background: #f1f3f4;
            border-radius: 8px;
            font-size: 12px; font-weight: 600; color: #0b2149;
            min-height: 36px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .pp6-chip::before { content: "✓"; color: #1a74e8; font-weight: 800; flex-shrink: 0; }
        .pp6-chip.sw::before { color: #7c3aed; }
        .pp6-chip.eq::before { color: #d97706; }

        /* License */
        .pp6-lic-list { display: flex; flex-direction: column; gap: 8px; }
        .pp6-lic {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px;
            background: #fefce8;
            border-radius: 8px;
            font-size: 13px;
        }
        .pp6-lic-star {
            width: 28px; height: 28px; border-radius: 50%;
            background: #fde047; color: #854d0e;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 14px;
        }
        .pp6-lic-body { flex: 1; min-width: 0; }
        .pp6-lic-name { color: #0b2149; font-weight: 700; }
        .pp6-lic-meta { font-size: 11px; color: #5f6368; margin-top: 1px; }

        .pp6-empty {
            text-align: center; color: #9aa0a6;
            font-size: 12px; font-style: italic;
            padding: 16px;
        }

        /* ═══════════════════════════════════════════════
           PEOPLE LIST
        ═══════════════════════════════════════════════ */
        .people-list { display: flex; flex-direction: column; gap: 8px; }
        .person-row {
            display: flex; align-items: center; gap: 14px;
            padding: 12px 16px; background: white;
            border: 1px solid #e8eaed; border-radius: 12px;
            cursor: pointer; transition: all 0.15s;
        }
        .person-row:hover {
            border-color: #1a74e8; background: #f8fbff;
            transform: translateX(2px); box-shadow: 0 2px 8px rgba(26,116,232,0.08);
        }
        .person-row.is-head { border-left: 4px solid #f59e0b; }
        .person-avatar {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, #4a91e8, #1a74e8);
            color: white; font-weight: 700; font-size: 16px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }
        .person-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .person-avatar.is-head-av { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
        .person-info { flex: 1; min-width: 0; }
        .person-name {
            font-size: 14px; font-weight: 700; color: var(--navy);
            display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
        }
        .person-meta {
            font-size: 12px; color: #5f6368; margin-top: 2px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .person-badge {
            display: inline-flex; align-items: center; gap: 3px;
            font-size: 10px; font-weight: 700; padding: 2px 8px;
            border-radius: 999px; text-transform: uppercase; letter-spacing: 0.3px;
        }
        .person-badge.head { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
        .person-action { color: #9aa0a6; flex-shrink: 0; transition: color 0.15s, transform 0.15s; }
        .person-row:hover .person-action { color: #1a74e8; transform: translateX(2px); }

        /* ───── MODAL ───── */
        .overlay {
            display: none; position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center; align-items: center;
        }
        .pmodal {
            background: white; border-radius: 16px;
            width: 95%; max-width: 1050px; max-height: 92vh;
            overflow-y: auto; position: relative;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        .pmodal-strip { height: 6px; background: var(--google-blue); border-radius: 16px 16px 0 0; }
        .modal-header {
            padding: 18px 28px; border-bottom: 1px solid #f1f3f4;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 6px; background: white; z-index: 10;
        }
        .modal-title { font-size: 1.25rem; font-weight: 700; color: var(--navy); }
        .modal-close {
            font-size: 28px; color: #5f6368;
            cursor: pointer; background: none; border: none; line-height: 1;
        }
        .modal-body { padding: 24px 28px 8px; }

        /* ───── TOP: PHOTO + FIELDS ───── */
        .resume-top { display: flex; gap: 36px; margin-bottom: 12px; }
        .photo-col  { width: 170px; text-align: center; flex-shrink: 0; }
        .photo-box {
            width: 170px; height: 200px;
            background: #f8f9fa; border: 2px dashed #b8c4dc;
            border-radius: 14px;
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            cursor: pointer; position: relative; overflow: hidden;
            color: #b8c4dc;
        }
        .photo-box:hover { border-color: var(--google-blue); background: #f0f7ff; }
        .photo-placeholder {
            color: var(--navy); font-size: 13px; font-weight: 500;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 18px;
        }
        .photo-placeholder svg { width: 44px; height: 44px; opacity: 0.85; stroke-width: 1.5; }
        .photo-preview { width: 100%; height: 100%; object-fit: cover; display: none; }
        .resume-badge-abs {
            position: absolute; top: 8px; right: 8px;
            background: var(--navy); color: white;
            font-size: 10px; font-weight: 800;
            padding: 3px 8px; border-radius: 4px; z-index: 10;
        }
        .photo-caption { margin-top: 10px; font-size: 11px; color: #5f6368; }
        .resume-fields {
            flex: 1; display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 28px; row-gap: 16px;
        }
        .frow { display: flex; flex-direction: column; gap: 6px; }
        .flabel { font-size: 13px; font-weight: 500; color: var(--navy); }
        .flabel .req { color: #d93025; }
        .finput {
            padding: 10px 14px;
            border: 1px solid #dadce0;
            border-radius: 10px;
            font-size: 14px;
            outline: none; background: white;
            font-family: inherit; color: var(--navy);
        }
        .finput::placeholder { color: #9aa0a6; }
        .finput:focus { border-color: var(--google-blue); box-shadow: 0 0 0 2px rgba(26,115,232,0.1); }
        .help-text { font-size: 11px; color: #9aa0a6; }
        .sect-bar {
            margin: 28px -28px 16px;
            padding: 0 28px 8px;
            border-bottom: 1px solid #dde3ee;
        }
        .sect-title {
            font-size: 13px; font-weight: 700; color: var(--navy);
            letter-spacing: 0.5px; text-transform: uppercase;
        }
        .check-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .check-card {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px;
            background: white; border: 1px solid #dde3ee;
            border-radius: 10px; font-size: 14px;
            cursor: pointer; color: var(--navy);
            transition: all 0.15s; user-select: none;
        }
        .check-card:hover { border-color: var(--google-blue); background: #f8fbff; }
        .check-card.checked { background: #e8f0fe; border-color: var(--google-blue); box-shadow: 0 0 0 1px var(--google-blue); }
        .check-card input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--google-blue); margin: 0; flex-shrink: 0; }
        .comp-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .comp-card {
            background: white; border: 1px solid #dde3ee;
            border-radius: 12px; padding: 14px 16px; transition: all 0.15s;
        }
        .comp-card.has-value { border-color: var(--google-blue); background: #f8fbff; }
        .comp-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .comp-label { font-size: 14px; font-weight: 700; color: var(--navy); }
        .comp-code { font-size: 10px; color: #5f6368; background: #f1f3f4; padding: 2px 8px; border-radius: 4px; }
        .comp-select {
            width: 100%; padding: 8px 12px; font-size: 13px;
            border-radius: 8px; border: 1px solid #dadce0;
            background: white; outline: none; color: var(--navy);
        }
        .lic-add-big {
            width: 100%; padding: 16px;
            background: #1a74e8; color: white; border: none;
            border-radius: 10px; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: background 0.15s;
        }
        .lic-add-big:hover { background: #1565d8; }
        .lic-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 12px; }
        .lic-row {
            display: grid;
            grid-template-columns: 2fr 1.2fr 1fr 1.4fr auto;
            gap: 8px; background: #f8f9fa; padding: 10px;
            border-radius: 10px; align-items: center;
        }
        .lic-row .finput { font-size: 13px; padding: 8px 12px; }
        .lic-del {
            background: #fce8e6; color: #d93025; border: none;
            border-radius: 8px; padding: 8px 12px; cursor: pointer;
            font-size: 12px; font-weight: 600;
        }
        .lic-del:hover { background: #fad2cf; }
        .sw-extra-row { display: flex; gap: 10px; margin-top: 14px; align-items: stretch; }
        .sw-extra-row .finput { flex: 1; }
        .btn-add-sw {
            padding: 8px 24px; background: white; color: #1a74e8;
            border: 1.5px solid #1a74e8; border-radius: 10px;
            font-size: 12px; font-weight: 700; cursor: pointer;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            line-height: 1.2; min-width: 68px;
        }
        .btn-add-sw:hover { background: #1a74e8; color: white; }
        .btn-add-sw .plus { font-size: 16px; font-weight: 700; }
        .factions {
            margin: 24px -28px 0; padding: 18px 28px;
            border-top: 1px solid #dde3ee;
            display: flex; justify-content: flex-end; gap: 12px;
            background: white; position: sticky; bottom: 0;
        }
        .btn {
            padding: 11px 28px; border-radius: 10px;
            font-size: 14px; font-weight: 600;
            cursor: pointer; border: none; font-family: inherit;
        }
        .btn-ghost { background: white; color: var(--navy); border: 1px solid #dde3ee; }
        .btn-ghost:hover { background: #f1f3f4; }
        .btn-primary { background: #1a74e8; color: white; }
        .btn-primary:hover { background: #1565d8; }
        .btn-primary:disabled { background: #c2d4f5; cursor: not-allowed; opacity: 0.7; }
        .btn-primary:disabled:hover { background: #c2d4f5; }

        /* TOAST */
        .toast-container {
            position: fixed; top: 24px; right: 24px;
            z-index: 2000; display: flex; flex-direction: column;
            gap: 8px; pointer-events: none;
        }
        .toast {
            min-width: 260px; max-width: 360px;
            padding: 12px 16px; border-radius: 10px;
            font-size: 13px; font-weight: 500;
            display: flex; align-items: center; gap: 10px;
            background: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-left: 4px solid;
            pointer-events: auto;
            animation: toastIn 0.25s ease-out, toastOut 0.3s ease-in 3.7s forwards;
        }
        .toast.success { border-left-color: #34a853; color: #1e7e34; }
        .toast.error   { border-left-color: #ea4335; color: #c5221f; }
        .toast .icon   { flex-shrink: 0; }
        .toast .close {
            margin-left: auto; background: transparent; border: none;
            cursor: pointer; color: inherit; opacity: 0.5;
            font-size: 16px; padding: 0 4px; line-height: 1;
        }
        .toast .close:hover { opacity: 1; }
        @keyframes toastIn  { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes toastOut { to   { transform: translateX(120%); opacity: 0; } }

        /* ═══════════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════════ */
        @media (max-width: 1024px) {
            .profile-grid { grid-template-columns: repeat(2, 1fr); }
            .folder-grid  { grid-template-columns: repeat(3, 1fr); }
            .member-folder-grid { grid-template-columns: repeat(3, 1fr); }
            .check-grid   { grid-template-columns: repeat(2, 1fr); }
            .comp-grid    { grid-template-columns: repeat(1, 1fr); }
            .resume-fields { column-gap: 18px; row-gap: 12px; }
            .lic-row { grid-template-columns: 1fr 1fr; gap: 6px; }
            .lic-row .lic-del { grid-column: 1 / -1; }
        }
        @media (max-width: 768px) {
            body { padding: 0; font-size: 14px; }
            .main-container { border-radius: 0; }
            aside { width: 64px !important; }
            aside .sidebar-item span, aside button { display: none !important; }
            aside .sidebar-item { justify-content: center; padding: 8px !important; }
            header { padding: 0 12px; height: 56px; }
            header .w-64 { width: auto; }
            header .max-w-2xl { padding: 0 8px; }
            header .gap-6 { gap: 12px; }
            header span.text-sm { display: none; }
            main { padding: 16px !important; }
            .profile-grid       { grid-template-columns: 1fr; gap: 12px; }
            .folder-grid        { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .member-folder-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .stat-grid          { grid-template-columns: repeat(2, 1fr); }
            .check-grid         { grid-template-columns: 1fr; }
            .pmodal { width: 96%; max-height: 95vh; border-radius: 12px; }
            .modal-body { padding: 16px; }
            .resume-top { flex-direction: column; gap: 20px; }
            .photo-col { width: 100%; }
            .photo-box { width: 140px; height: 165px; margin: 0 auto; }
            .resume-fields { grid-template-columns: 1fr; }
            .sect-bar { margin: 20px -16px 12px; padding: 0 16px 8px; }
            .factions { margin: 18px -16px 0; padding: 14px 16px; flex-direction: column-reverse; }
            .factions .btn { width: 100%; }
            .lic-row { grid-template-columns: 1fr; gap: 6px; }
            .sw-extra-row { flex-direction: column; }
            .btn-add-sw { width: 100%; flex-direction: row; gap: 6px; }
            #modal-team-members .pmodal { min-height: 70vh; max-height: 95vh; }
            .team-modal-header { padding: 16px; flex-direction: column; gap: 12px; align-items: flex-start; }
            .team-modal-stats  { justify-content: flex-start; }
            .view-toggle button { padding: 6px 14px; font-size: 12px; }
            .pp6-header { flex-direction: column; text-align: center; padding: 20px; }
            .pp6-row { grid-template-columns: 90px 1fr; gap: 8px; font-size: 12px; }
        }
        @media (max-width: 480px) {
            .folder-grid        { grid-template-columns: 1fr; }
            .member-folder-grid { grid-template-columns: 1fr; }
            .stat-grid          { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

{{-- ───── TOAST NOTIFICATIONS ───── --}}
<div class="toast-container" id="toast-container">
    @if(session('success'))
        <div class="toast success">
            <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
            <span>{{ session('success') }}</span>
            <button class="close" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif
    @if($errors->any())
        @foreach($errors->all() as $err)
            <div class="toast error">
                <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>{{ $err }}</span>
                <button class="close" onclick="this.parentElement.remove()">×</button>
            </div>
        @endforeach
    @endif
</div>

<div class="main-container">
    <header class="h-16 flex items-center justify-between px-8">
        <div class="flex items-center gap-3 w-64">
            <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Google_Drive_icon_%282020%29.svg" alt="Google Drive" class="w-6 h-6">
            <span class="text-xl font-medium text-gray-700">Google Drive</span>
        </div>
        <div class="flex-1 max-w-2xl px-4">
            <form method="GET" action="{{ route('technician.dashboard') }}" class="search-bar flex items-center px-5 py-2 rounded-full text-gray-400">
                <i data-lucide="search" class="w-4 h-4 mr-3 opacity-60"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search Drive..." class="bg-transparent border-none outline-none w-full text-sm">
            </form>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-5 text-gray-500">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <i data-lucide="help-circle" class="w-5 h-5"></i>
                <i data-lucide="settings" class="w-5 h-5"></i>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-gray-600">{{ auth()->user()->name ?? 'Jessica' }}</span>
                <div class="w-8 h-8 bg-blue-700 text-white rounded-full flex items-center justify-center font-bold text-xs">
                    {{ strtoupper(substr(auth()->user()->name ?? 'J', 0, 1)) }}
                </div>
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <aside class="w-64 sidebar-blue text-white flex flex-col">
            <div class="px-6 py-8 flex-1">
                <button class="w-full bg-white text-[#1a73e8] py-2.5 px-4 rounded-full font-bold text-sm mb-10 shadow-sm hover:bg-gray-100 transition-colors">
                    Upload New File
                </button>
                <nav class="space-y-1.5">
                    <a onclick="showContent('my-drive')" id="menu-my-drive" class="sidebar-item flex items-center gap-4 px-4 py-2 rounded-full">
                        <i data-lucide="compass" class="w-4 h-4"></i><span class="text-xs font-medium">My drive</span>
                    </a>
                    <a onclick="showContent('technician-skills')" id="menu-technician-skills" class="sidebar-item active flex items-center gap-4 px-4 py-2 rounded-full">
                        <i data-lucide="wrench" class="w-4 h-4"></i><span class="text-xs font-medium">ทักษะช่าง</span>
                    </a>
                    <a onclick="showContent('people')" id="menu-people" class="sidebar-item flex items-center gap-4 px-4 py-2 rounded-full" style="display:none;">
                        <i data-lucide="user-circle" class="w-4 h-4"></i><span class="text-xs font-medium">บุคคล</span>
                    </a>
                    <a href="#" class="sidebar-item flex items-center gap-4 px-4 py-2 rounded-full">
                        <i data-lucide="users" class="w-4 h-4"></i><span class="text-xs font-medium">Shared with me</span>
                    </a>
                    <a href="#" class="sidebar-item flex items-center gap-4 px-4 py-2 rounded-full">
                        <i data-lucide="clock" class="w-4 h-4"></i><span class="text-xs font-medium">Recent</span>
                    </a>
                    <a href="#" class="sidebar-item flex items-center gap-4 px-4 py-2 rounded-full">
                        <i data-lucide="star" class="w-4 h-4"></i><span class="text-xs font-medium">Starred</span>
                    </a>
                    <a href="#" class="sidebar-item flex items-center gap-4 px-4 py-2 rounded-full">
                        <i data-lucide="trash-2" class="w-4 h-4"></i><span class="text-xs font-medium">Trash</span>
                    </a>
                </nav>
            </div>
        </aside>

        <main class="flex-1 bg-white p-8 overflow-y-auto custom-scrollbar">
            @php
                try {
                    $techsCollection = $technicians ?? collect();
                    if (!($techsCollection instanceof \Illuminate\Support\Collection)) {
                        $techsCollection = collect($techsCollection);
                    }
                    $sortedTechs = $techsCollection->sortBy(function($t) {
                        $isHead = (($t->emp_position ?? '') === 'หัวหน้าทีม') ? 0 : 1;
                        return sprintf('%d|%s|%s', $isHead, $t->emp_team ?? '', $t->emp_name ?? '');
                    })->values();
                    $groupedByTeam = $techsCollection
                        ->groupBy(fn($t) => $t->emp_team ?: 'ไม่ระบุทีม')
                        ->map(function($members) {
                            return $members->sortBy(function($t) {
                                $isHead = (($t->emp_position ?? '') === 'หัวหน้าทีม') ? 0 : 1;
                                return sprintf('%d|%s', $isHead, $t->emp_name ?? '');
                            })->values();
                        })
                        ->sortBy(function($members, $teamName) {
                            $hasHead = $members->contains(fn($t) => ($t->emp_position ?? '') === 'หัวหน้าทีม') ? 0 : 1;
                            return sprintf('%d|%s', $hasHead, $teamName);
                        });
                } catch (\Throwable $e) {
                    $sortedTechs    = collect();
                    $groupedByTeam  = collect();
                }
            @endphp

            <div id="content-my-drive" class="content-section">
                <h1 class="text-2xl font-bold text-[#0b2149] mb-8">My Drive</h1>
            </div>

            <div id="content-people" class="content-section">
                <div id="people-profile-view"></div>
            </div>

            <div id="content-technician-skills" class="content-section active">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-[#0b2149]">ภาพรวมทักษะช่าง</h1>
                    <button onclick="openModal('modal-tech')" class="bg-[#1a73e8] text-white px-6 py-2 rounded-full font-bold text-sm flex items-center gap-2 shadow-md hover:bg-blue-700 transition-all">
                        <i data-lucide="plus" class="w-4 h-4"></i>เพิ่มช่าง
                    </button>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div class="view-toggle" id="view-toggle">
                        <button type="button" class="active" data-view="individual" onclick="switchView('individual')">
                            <i data-lucide="user" class="w-4 h-4"></i><span>รายคน</span>
                        </button>
                        <button type="button" data-view="team" onclick="switchView('team')">
                            <i data-lucide="users" class="w-4 h-4"></i><span>รายทีม</span>
                        </button>
                    </div>
                    <div class="text-xs text-gray-500" id="view-count-hint"></div>
                </div>

                @if(($technicians ?? collect())->isEmpty())
                    <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                        <i data-lucide="construction" class="w-16 h-16 mb-4 opacity-20"></i>
                        <p class="text-lg font-medium">ยังไม่มีข้อมูลช่าง</p>
                    </div>
                @else
                    <div id="view-individual" class="view-pane active">
                        <div class="profile-grid">
                            @php
                                $memberAccent = ['from' => '#4a91e8', 'to' => '#1a74e8'];
                                $headAccent   = ['from' => '#fbbf24', 'to' => '#f59e0b'];
                                $compToScore = [
                                    'เชี่ยวชาญ' => 95, 'ชำนาญ' => 80, 'พื้นฐาน' => 60, 'ไม่มี' => 0,
                                    'expert' => 95, 'advanced' => 80, 'intermediate' => 60, 'basic' => 40, 'none' => 0,
                                ];
                                $compToLabel = [
                                    'เชี่ยวชาญ' => 'เชี่ยวชาญ', 'ชำนาญ' => 'ชำนาญ', 'พื้นฐาน' => 'พื้นฐาน',
                                    'expert' => 'เชี่ยวชาญ', 'advanced' => 'ชำนาญ', 'intermediate' => 'พื้นฐาน', 'basic' => 'พื้นฐาน',
                                ];
                            @endphp

                            @foreach($sortedTechs as $idx => $t)
                                @php
                                    $isHead = ($t->emp_position === 'หัวหน้าทีม');
                                    $accent = $isHead ? $headAccent : $memberAccent;
                                    $comps = [];
                                    if (!empty($t->core_competencies)) {
                                        $comps = is_array($t->core_competencies)
                                            ? $t->core_competencies
                                            : (json_decode($t->core_competencies, true) ?: []);
                                    }
                                    $skillList = [];
                                    if (!empty($t->emp_skill)) {
                                        if (is_array($t->emp_skill)) $skillList = $t->emp_skill;
                                        else {
                                            $dec = json_decode($t->emp_skill, true);
                                            $skillList = is_array($dec) ? $dec : array_map('trim', explode(',', $t->emp_skill));
                                        }
                                    }
                                    $initials = mb_substr($t->emp_name ?? '?', 0, 1);
                                    if (!empty($t->emp_name_eng)) {
                                        $parts = explode(' ', trim($t->emp_name_eng));
                                        $initials = strtoupper(mb_substr($parts[0] ?? '', 0, 1) . mb_substr($parts[1] ?? '', 0, 1));
                                    }
                                @endphp

                                <div class="profile-card {{ $isHead ? 'is-head' : '' }}" onclick="goToPersonProfile('{{ $idx }}')">
                                    <div class="profile-accent" style="background: linear-gradient(135deg, {{ $accent['from'] }}, {{ $accent['to'] }});"></div>
                                    <div class="profile-header">
                                        <div class="profile-avatar" style="background: linear-gradient(135deg, {{ $accent['from'] }}, {{ $accent['to'] }});">
                                            @if($t->img)
                                                <img src="{{ asset('storage/' . $t->img) }}" alt="{{ $t->emp_name }}">
                                            @else
                                                <span>{{ $initials }}</span>
                                            @endif
                                        </div>
                                        <div class="profile-name-block">
                                            <div class="profile-name">{{ $t->emp_name }}</div>
                                            <div class="profile-pos">{{ $t->emp_position }}{{ $t->emp_team ? ' · ' . $t->emp_team : '' }}</div>
                                        </div>
                                    </div>

                                    @php
                                        $hasScore = []; $noScore = [];
                                        if (!empty($competencyList)) {
                                            foreach ($competencyList as $comp) {
                                                $key = $comp['key']; $label = $comp['label'] ?? $key;
                                                $level = $comps[$key] ?? null;
                                                $score = $compToScore[$level] ?? 0;
                                                $row = [
                                                    'key' => $key, 'label' => $label,
                                                    'score' => $score, 'level' => $level,
                                                    'levelLabel' => $score > 0 ? ($compToLabel[$level] ?? $level) : '—',
                                                    'empty' => $score === 0,
                                                ];
                                                if ($score > 0) $hasScore[] = $row; else $noScore[] = $row;
                                            }
                                            usort($hasScore, fn($a, $b) => $b['score'] <=> $a['score']);
                                        }
                                        $topComps = array_slice(array_merge($hasScore, $noScore), 0, 6);
                                    @endphp
                                    @if(!empty($topComps))
                                        <div class="stat-grid">
                                            @foreach($topComps as $c)
                                                <div class="stat-box {{ $c['empty'] ? 'is-empty' : '' }}">
                                                    @if(!$c['empty'])
                                                        <div class="stat-key">{{ $c['label'] }}</div>
                                                        <div class="stat-val">{{ $c['levelLabel'] }}</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(count($skillList) > 0)
                                        <div class="skill-tags">
                                            @foreach(array_slice($skillList, 0, 4) as $sk)
                                                <span class="skill-tag">{{ $sk }}</span>
                                            @endforeach
                                            @if(count($skillList) > 4)
                                                <span class="skill-tag-more">+{{ count($skillList) - 4 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="view-team" class="view-pane">
                        <div class="folder-grid">
                            @php $folderIndex = 0; @endphp
                            @foreach($groupedByTeam as $teamName => $members)
                                @php
                                    $head = $members->firstWhere('emp_position', 'หัวหน้าทีม');
                                    $memberCount = $members->where('emp_position', '!=', 'หัวหน้าทีม')->count();
                                    $teamSlug = \Illuminate\Support\Str::slug($teamName, '-') . '_' . $folderIndex;
                                    $folderIndex++;
                                    $stackMembers = $members->take(4);
                                    $moreCount = max(0, $members->count() - 4);
                                @endphp
                                <div class="folder-card" onclick="openTeamModal('{{ $teamSlug }}')">
                                    <div class="folder-top">
                                        <div class="avatar-stack">
                                            @foreach($stackMembers as $m)
                                                @php $mIsHead = ($m->emp_position === 'หัวหน้าทีม'); @endphp
                                                @if($m->img)
                                                    <img class="av {{ $mIsHead ? 'head-av' : '' }}" src="{{ asset('storage/' . $m->img) }}" alt="{{ $m->emp_name }}" title="{{ $m->emp_name }}">
                                                @else
                                                    <div class="av {{ $mIsHead ? 'head-av' : '' }}" title="{{ $m->emp_name }}">{{ mb_substr($m->emp_name ?? '?', 0, 1) }}</div>
                                                @endif
                                            @endforeach
                                            @if($moreCount > 0)<div class="av more">+{{ $moreCount }}</div>@endif
                                        </div>
                                        <div class="folder-team-info">
                                            <div class="folder-team-label">ทีม</div>
                                            <div class="folder-team-name">{{ $teamName }}</div>
                                            @if($head)
                                                <div class="folder-head-note">หัวหน้า: {{ $head->emp_name }}</div>
                                            @else
                                                <div class="folder-head-note" style="color:#fde68a">ยังไม่มีหัวหน้า</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="folder-bottom">
                                        <div class="folder-divider"></div>
                                        <div class="folder-icons">
                                            <i data-lucide="users" class="w-4 h-4"></i>
                                            <span>{{ $members->count() }} คน</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>

{{-- Team popup modal --}}
<div class="overlay" id="modal-team-members" onclick="closeTeamModal()">
    <div class="pmodal" onclick="event.stopPropagation()" style="max-width: 1200px; width: 96%; max-height: 96vh; min-height: 80vh;">
        <div class="pmodal-strip"></div>
        <button type="button" class="team-modal-close-x" onclick="closeTeamModal()">×</button>
        <div class="modal-body" style="padding-top: 0;">
            <div class="team-modal-header">
                <div class="team-modal-header-left">
                    <div class="team-modal-icon"><i data-lucide="users" class="w-6 h-6"></i></div>
                    <div>
                        <div class="team-modal-title" id="tm-title">ทีม</div>
                        <div class="team-modal-sub" id="tm-sub">-</div>
                    </div>
                </div>
                <div class="team-modal-stats">
                    <div class="team-modal-stat" id="tm-stat-total">0 คน</div>
                    <div class="team-modal-stat" id="tm-stat-member">0 ลูกทีม</div>
                </div>
            </div>
            <div id="tm-head-section">
                <div class="team-section-label">
                    <i data-lucide="crown" class="w-3.5 h-3.5"></i>หัวหน้าทีม
                </div>
                <div class="member-folder-grid" id="tm-head-grid"></div>
            </div>
            <div id="tm-member-section">
                <div class="team-section-label">
                    <i data-lucide="user" class="w-3.5 h-3.5"></i>ลูกทีม
                </div>
                <div class="member-folder-grid" id="tm-member-grid"></div>
            </div>
            <div style="height: 16px;"></div>
        </div>
    </div>
</div>

<script id="teams-data" type="application/json">
    @php
        $teamsForJs = [];
        try {
            if (isset($groupedByTeam)) {
                $idx = 0;
                foreach ($groupedByTeam as $teamName => $members) {
                    $slug = \Illuminate\Support\Str::slug($teamName, '-') . '_' . $idx;
                    $idx++;
                    $head = $members->firstWhere('emp_position', 'หัวหน้าทีม');
                    $teamsForJs[$slug] = [
                        'name' => $teamName,
                        'head_name' => $head->emp_name ?? null,
                        'total' => $members->count(),
                        'member_cnt' => $members->where('emp_position', '!=', 'หัวหน้าทีม')->count(),
                        'members' => $members->map(function($t) {
                            return [
                                'name' => $t->emp_name,
                                'img' => $t->img ? asset('storage/' . $t->img) : null,
                                'initial' => mb_substr($t->emp_name ?? '?', 0, 1),
                                'is_head' => ($t->emp_position === 'หัวหน้าทีม'),
                                'skill' => $t->emp_skill,
                                'position' => $t->emp_position,
                            ];
                        })->values()->all(),
                    ];
                }
            }
        } catch (\Throwable $e) { $teamsForJs = []; }
        echo json_encode($teamsForJs, JSON_UNESCAPED_UNICODE);
    @endphp
</script>

<script id="technicians-data" type="application/json">
    @php
        $techsForJs = [];
        try {
            if (isset($sortedTechs)) {
                foreach ($sortedTechs as $idx => $t) {
                    $skList = [];
                    if (!empty($t->emp_skill)) {
                        if (is_array($t->emp_skill)) $skList = $t->emp_skill;
                        else {
                            $dec = json_decode($t->emp_skill, true);
                            $skList = is_array($dec) ? $dec : array_map('trim', explode(',', $t->emp_skill));
                        }
                    }
                    $swList = [];
                    if (!empty($t->software_tools)) {
                        if (is_array($t->software_tools)) $swList = $t->software_tools;
                        else {
                            $dec = json_decode($t->software_tools, true);
                            $swList = is_array($dec) ? $dec : array_map('trim', explode(',', $t->software_tools));
                        }
                    }
                    $cc = [];
                    if (!empty($t->core_competencies)) {
                        $cc = is_array($t->core_competencies)
                            ? $t->core_competencies
                            : (json_decode($t->core_competencies, true) ?: []);
                    }
                    $lic = [];
                    if (!empty($t->licenses)) {
                        $lic = is_array($t->licenses)
                            ? $t->licenses
                            : (json_decode($t->licenses, true) ?: []);
                    }
                    $techsForJs[$idx] = [
                        'name' => $t->emp_name,
                        'name_eng' => $t->emp_name_eng ?? null,
                        'nickname' => $t->emp_nickname ?? null,
                        'emp_id' => $t->emp_id ?? null,
                        'phone' => $t->emp_phone ?? null,
                        'dob' => $t->date_of_birth ?? null,
                        'position' => $t->emp_position,
                        'team' => $t->emp_team,
                        'img' => $t->img ? asset('storage/' . $t->img) : null,
                        'initial' => mb_substr($t->emp_name ?? '?', 0, 1),
                        'is_head' => ($t->emp_position === 'หัวหน้าทีม'),
                        'skills' => array_values(array_filter($skList)),
                        'software' => array_values(array_filter($swList)),
                        'competencies' => $cc,
                        'licenses' => $lic,
                    ];
                }
            }
        } catch (\Throwable $e) { $techsForJs = []; }
        echo json_encode($techsForJs, JSON_UNESCAPED_UNICODE);
    @endphp
</script>

{{-- Add Tech Modal --}}
<div class="overlay" id="modal-tech" onclick="closeModal('modal-tech')">
    <div class="pmodal" onclick="event.stopPropagation()">
        <div class="pmodal-strip"></div>
        <div class="modal-header">
            <div class="modal-title">เพิ่มช่างใหม่</div>
            <button class="modal-close" type="button" onclick="closeModal('modal-tech')">×</button>
        </div>
        <div class="modal-body">
            <form id="form-add-tech" method="POST" action="{{ route('tech.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="resume-top">
                    <div class="photo-col">
                        <div style="position:relative">
                            <span class="resume-badge-abs">PHOTO</span>
                            <label class="photo-box" for="photo-input">
                                <div class="photo-placeholder" id="photo-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/>
                                    </svg>
                                    <div>คลิกอัปโหลดรูป</div>
                                </div>
                                <img id="photo-preview" class="photo-preview" alt="preview">
                            </label>
                            <input type="file" id="photo-input" name="img" accept="image/*" style="display:none" onchange="previewPhoto(event)">
                        </div>
                        <div class="photo-caption">รูปประจำตัว</div>
                    </div>
                    <div class="resume-fields">
                        <div class="frow">
                            <label class="flabel">รหัสพนักงาน <span class="req">*</span></label>
                            <input class="finput" type="text" name="emp_id" placeholder="3E-001" value="{{ old('emp_id') }}" required>
                            <span class="help-text">ตัวอักษร, ตัวเลข, -, _</span>
                        </div>
                        <div class="frow">
                            <label class="flabel">ชื่อ-นามสกุล (ไทย)</label>
                            <input class="finput" type="text" name="emp_name" id="input-emp-name" value="{{ old('emp_name') }}" placeholder="">
                        </div>
                        <div class="frow">
                            <label class="flabel">ชื่อ-นามสกุล (ENG)</label>
                            <input class="finput" type="text" name="emp_name_eng" value="{{ old('emp_name_eng') }}" placeholder="">
                        </div>
                        <div class="frow">
                            <label class="flabel">ชื่อเล่น</label>
                            <input class="finput" type="text" name="emp_nickname" value="{{ old('emp_nickname') }}" placeholder="">
                        </div>
                        <div class="frow">
                            <label class="flabel">เบอร์โทร</label>
                            <input class="finput" type="tel" name="emp_phone" value="{{ old('emp_phone') }}" placeholder="">
                        </div>
                        <div class="frow">
                            <label class="flabel">วันเกิด</label>
                            <input class="finput" type="date" name="date_of_birth" id="input-dob" value="{{ old('date_of_birth') }}">
                        </div>
                        <div class="frow">
                            <label class="flabel">ตำแหน่ง <span class="req">*</span></label>
                            <select class="finput" name="emp_position" id="select-position" required onchange="syncTeamWithPosition(); validateAddForm();">
                                <option value="">-- เลือก --</option>
                                <option value="ลูกทีม" {{ old('emp_position') === 'ลูกทีม' ? 'selected' : '' }}>ลูกทีม</option>
                                <option value="หัวหน้าทีม" {{ old('emp_position') === 'หัวหน้าทีม' ? 'selected' : '' }}>หัวหน้าทีม</option>
                            </select>
                        </div>
                        <div class="frow">
                            <label class="flabel">ทีม <span class="req">*</span> <span id="team-hint" class="text-[10px] font-normal text-blue-500" style="display:none;">(= ชื่อหัวหน้า)</span></label>
                            <select class="finput" name="emp_team" id="select-team" onchange="validateAddForm()">
                                <option value="">-- เลือกทีม --</option>
                                @foreach(($availableTeams ?? collect()) as $teamName)
                                    <option value="{{ $teamName }}" {{ old('emp_team') === $teamName ? 'selected' : '' }}>{{ $teamName }}</option>
                                @endforeach
                            </select>
                            <input class="finput" type="text" name="emp_team" id="input-team-head" value="{{ old('emp_team') }}" placeholder="ชื่อทีม = ชื่อหัวหน้า" readonly disabled style="display:none; background:#f0f7ff;">
                        </div>
                    </div>
                </div>

                <div class="sect-bar"><div class="sect-title">ทักษะ</div></div>
                <div class="check-grid">
                    @php $oldSkills = (array) old('emp_skill', []); @endphp
                    @foreach(($skillOptions ?? []) as $skill)
                        <label class="check-card {{ in_array($skill, $oldSkills) ? 'checked' : '' }}">
                            <input type="checkbox" name="emp_skill[]" value="{{ $skill }}" {{ in_array($skill, $oldSkills) ? 'checked' : '' }}>
                            <span>{{ $skill }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="sect-bar"><div class="sect-title">Core Competencies</div></div>
                <div class="comp-grid">
                    @php $oldComps = (array) old('core_competencies', []); @endphp
                    @foreach(($competencyList ?? []) as $comp)
                        @php $currentVal = $oldComps[$comp['key']] ?? 'none'; @endphp
                        <div class="comp-card {{ ($currentVal && $currentVal !== 'none') ? 'has-value' : '' }}">
                            <div class="comp-head">
                                <span class="comp-label">{{ $comp['label'] }}</span>
                                <span class="comp-code">{{ $comp['key'] }}</span>
                            </div>
                            <select class="comp-select" name="core_competencies[{{ $comp['key'] }}]" onchange="toggleCompCard(this)">
                                @foreach(($competencyLevels ?? []) as $val => $label)
                                    <option value="{{ $val }}" {{ $currentVal === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>

                <div class="sect-bar"><div class="sect-title">Licenses & Experience</div></div>
                <div class="lic-list" id="lic-list"></div>
                <button type="button" class="lic-add-big" onclick="addLicenseRow()">+ เพิ่มใบรับรอง</button>

                <div class="sect-bar"><div class="sect-title">Software & Tools</div></div>
                <div class="check-grid" id="software-options-container">
                    @php $oldSoftware = (array) old('software_tools', []); @endphp
                    @foreach(($softwareOptions ?? []) as $sw)
                        <label class="check-card {{ in_array($sw, $oldSoftware) ? 'checked' : '' }}">
                            <input type="checkbox" name="software_tools[]" value="{{ $sw }}" {{ in_array($sw, $oldSoftware) ? 'checked' : '' }}>
                            <span>{{ $sw }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="sw-extra-row">
                    <input class="finput" type="text" id="sw-extra-input" placeholder="เพิ่ม software อื่นๆ...">
                    <button type="button" class="btn-add-sw" onclick="addCustomSoftware()">
                        <span class="plus">+</span><span>เพิ่ม</span>
                    </button>
                </div>

                <div class="factions">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('modal-tech')">ยกเลิก</button>
                    <button type="submit" id="btn-submit-tech" class="btn btn-primary">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
    document.querySelectorAll('.toast').forEach(t => setTimeout(() => t.remove(), 4100));

    function showContent(sectionId) {
        document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
        document.getElementById('content-' + sectionId).classList.add('active');
        document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
        const menuEl = document.getElementById('menu-' + sectionId);
        if (menuEl) menuEl.classList.add('active');
        if (sectionId !== 'people') {
            const menuPeople = document.getElementById('menu-people');
            const profileView = document.getElementById('people-profile-view');
            if (menuPeople) menuPeople.style.display = 'none';
            if (profileView) profileView.innerHTML = '';
        }
    }
    function openModal(id)  { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    function switchView(view) {
        document.querySelectorAll('#view-toggle button').forEach(btn => btn.classList.toggle('active', btn.dataset.view === view));
        document.querySelectorAll('.view-pane').forEach(p => p.classList.remove('active'));
        const target = document.getElementById('view-' + view);
        if (target) target.classList.add('active');
        lucide.createIcons();
        const hint = document.getElementById('view-count-hint');
        if (hint) hint.textContent = view === 'individual' ? 'เรียงหัวหน้าทีมก่อน · ลูกทีมตามหลัง' : 'กดที่โฟลเดอร์ทีมเพื่อดูสมาชิกทั้งหมด';
    }

    let TEAMS_DATA = {};
    try { TEAMS_DATA = JSON.parse(document.getElementById('teams-data').textContent || '{}'); } catch (e) { TEAMS_DATA = {}; }
    let TECHS_DATA = {};
    try { TECHS_DATA = JSON.parse(document.getElementById('technicians-data').textContent || '{}'); } catch (e) { TECHS_DATA = {}; }

    function escapeHtml(s) {
        if (s == null) return '';
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function renderMemberCard(m) {
        const avatar = m.img
            ? `<div class="member-folder-avatar"><img src="${escapeHtml(m.img)}" alt=""></div>`
            : `<div class="member-folder-avatar">${escapeHtml(m.initial)}</div>`;
        const badge = m.is_head
            ? `<span class="member-folder-badge head"><i data-lucide="crown" class="w-3 h-3"></i>หัวหน้า</span>`
            : `<span class="member-folder-badge member">ลูกทีม</span>`;
        const skill = m.skill ? `<div class="member-folder-skill">${escapeHtml(m.skill)}</div>` : '';
        return `
            <div class="member-folder ${m.is_head ? 'is-head' : ''}">
                <div>${avatar}<div class="member-folder-name">${escapeHtml(m.name)}</div>${skill}</div>
                <div class="member-folder-bottom">
                    <div class="member-folder-divider"></div>
                    <div class="member-folder-badge-row">${badge}</div>
                </div>
            </div>
        `;
    }

    function openTeamModal(teamSlug) {
        const data = TEAMS_DATA[teamSlug]; if (!data) return;
        document.getElementById('tm-title').textContent = 'ทีม ' + data.name;
        document.getElementById('tm-sub').textContent = data.head_name ? ('หัวหน้าทีม: ' + data.head_name) : 'ยังไม่มีหัวหน้าทีม';
        document.getElementById('tm-stat-total').textContent = data.total + ' คน';
        document.getElementById('tm-stat-member').textContent = data.member_cnt + ' ลูกทีม';
        const heads = data.members.filter(m => m.is_head);
        const members = data.members.filter(m => !m.is_head);
        const headGrid = document.getElementById('tm-head-grid');
        const memberGrid = document.getElementById('tm-member-grid');
        const headSec = document.getElementById('tm-head-section');
        const memberSec = document.getElementById('tm-member-section');
        headGrid.innerHTML = heads.map(renderMemberCard).join('');
        memberGrid.innerHTML = members.map(renderMemberCard).join('');
        headSec.style.display = heads.length ? '' : 'none';
        memberSec.style.display = members.length ? '' : 'none';
        document.getElementById('modal-team-members').style.display = 'flex';
        lucide.createIcons();
    }
    function closeTeamModal() { document.getElementById('modal-team-members').style.display = 'none'; }

    function goToPersonProfile(idx) {
        const menuPeople = document.getElementById('menu-people');
        if (menuPeople) menuPeople.style.display = '';
        showContent('people');
        showPersonProfileInline(idx);
    }

    // ───── แสดง profile inline (V6 — Clean & Simple) ─────
    function showPersonProfileInline(idx) {
        const t = TECHS_DATA[idx];
        if (!t) return;
        const profileView = document.getElementById('people-profile-view');
        if (!profileView) return;

        const isHead = !!t.is_head;

        // คำนวณอายุ
        let age = null;
        if (t.dob) {
            const d = new Date(t.dob);
            if (!isNaN(d.getTime())) {
                const now = new Date();
                age = now.getFullYear() - d.getFullYear();
                const m = now.getMonth() - d.getMonth();
                if (m < 0 || (m === 0 && now.getDate() < d.getDate())) age--;
            }
        }

        const avatarHtml = t.img ? `<img src="${escapeHtml(t.img)}" alt="">` : escapeHtml(t.initial || '?');

        // Competency mapping
        const scoreMap = { 'เชี่ยวชาญ':95,'expert':95, 'ชำนาญ':80,'advanced':80, 'พื้นฐาน':60,'intermediate':60,'basic':60 };
        const levelTH  = { 'เชี่ยวชาญ':'เชี่ยวชาญ','expert':'เชี่ยวชาญ', 'ชำนาญ':'ชำนาญ','advanced':'ชำนาญ', 'พื้นฐาน':'พื้นฐาน','intermediate':'พื้นฐาน','basic':'พื้นฐาน' };
        const lvClass  = { 95:'lv1', 80:'lv2', 60:'lv3' };

        const comps = [];
        if (t.competencies && typeof t.competencies === 'object') {
            for (const [key, level] of Object.entries(t.competencies)) {
                const sc = scoreMap[level] ?? 0;
                if (sc > 0) comps.push({ key, score: sc, level: levelTH[level] || level });
            }
        }
        comps.sort((a, b) => b.score - a.score);

        const compsHtml = comps.length
            ? comps.map(c => `
                <div class="pp6-comp">
                    <span class="pp6-comp-name">${escapeHtml(c.key)}</span>
                    <span class="pp6-comp-lv ${lvClass[c.score] || 'lv3'}">${escapeHtml(c.level)}</span>
                </div>
            `).join('')
            : '<div class="pp6-empty">ยังไม่ได้ประเมิน</div>';

        const chipsHtml = (items, cls = '') => items && items.length
            ? items.map(s => `<span class="pp6-chip ${cls}">${escapeHtml(s)}</span>`).join('')
            : '<div class="pp6-empty">—</div>';

        const skillsHtml = chipsHtml(t.skills, '');
        const swHtml     = chipsHtml(t.software, 'sw');
        const eqHtml     = chipsHtml(t.equipment || [], 'eq');

        const licHtml = (t.licenses && t.licenses.length)
            ? t.licenses.map(l => `
                <div class="pp6-lic">
                    <div class="pp6-lic-star">★</div>
                    <div class="pp6-lic-body">
                        <div class="pp6-lic-name">${escapeHtml(l.title || '—')}</div>
                        <div class="pp6-lic-meta">
                            ${l.doc_no ? 'เลขที่: ' + escapeHtml(l.doc_no) : ''}
                            ${l.doc_no && l.date_issued ? ' · ' : ''}
                            ${l.date_issued ? escapeHtml(l.date_issued) : ''}
                        </div>
                    </div>
                </div>
            `).join('')
            : '<div class="pp6-empty">ยังไม่มีใบรับรอง</div>';

        const infoRows = [
            t.emp_id   && ['รหัสพนักงาน', t.emp_id],
            t.name     && ['ชื่อ (ไทย)', t.name],
            t.name_eng && ['ชื่อ (ENG)', t.name_eng],
            t.nickname && ['ชื่อเล่น', t.nickname],
            t.phone    && ['เบอร์โทร', t.phone],
            t.dob      && ['วันเกิด', t.dob],
            age !== null && ['อายุ', age + ' ปี'],
        ].filter(Boolean);

        const infoHtml = infoRows.map(([k, v]) => `
            <div class="pp6-row">
                <span class="pp6-row-k">${escapeHtml(k)}</span>
                <span class="pp6-row-v">${escapeHtml(v)}</span>
            </div>
        `).join('');

        profileView.innerHTML = `
            <button type="button" class="pp-back" onclick="backToSkillsPage()">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>กลับไปทักษะช่าง</span>
            </button>

            <!-- HEADER BAR -->
            <div class="pp6-header">
                <div class="pp6-avatar ${isHead ? 'is-head' : ''}">${avatarHtml}</div>
                <div class="pp6-head-info">
                    <div class="pp6-name">${escapeHtml(t.name || '—')}</div>
                    ${t.name_eng ? `<div class="pp6-name-eng">${escapeHtml(t.name_eng)}</div>` : ''}
                    <div class="pp6-tags">
                        <span class="pp6-tag ${isHead ? 'head' : ''}">${isHead ? '👑 หัวหน้าทีม' : '👤 ลูกทีม'}</span>
                        ${t.team ? `<span class="pp6-tag team">ทีม ${escapeHtml(t.team)}</span>` : ''}
                        ${t.emp_id ? `<span class="pp6-tag">${escapeHtml(t.emp_id)}</span>` : ''}
                    </div>
                </div>
            </div>

            <!-- 2 COLUMNS -->
            <div class="pp6-grid">
                <!-- LEFT: ประวัติส่วนตัว -->
                <div class="pp6-col">
                    <div class="pp6-card">
                        <div class="pp6-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"/><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/></svg>
                            ประวัติส่วนตัว
                        </div>
                        <div class="pp6-rows">${infoHtml || '<div class="pp6-empty">—</div>'}</div>
                    </div>
                </div>

                <!-- RIGHT: ทักษะและความสามารถ -->
                <div class="pp6-col">
                    <div class="pp6-card">
                        <div class="pp6-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            Core Competencies
                        </div>
                        <div class="pp6-comps">${compsHtml}</div>
                    </div>

                    <div class="pp6-card">
                        <div class="pp6-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                            ทักษะ
                        </div>
                        <div class="pp6-chips">${skillsHtml}</div>
                    </div>

                    <div class="pp6-card">
                        <div class="pp6-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/></svg>
                            Software & Tools
                        </div>
                        <div class="pp6-chips">${swHtml}</div>
                    </div>

                    <div class="pp6-card">
                        <div class="pp6-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            Licenses & Experience
                        </div>
                        <div class="pp6-lic-list">${licHtml}</div>
                    </div>

                    <div class="pp6-card">
                        <div class="pp6-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            อุปกรณ์
                        </div>
                        <div class="pp6-chips">${eqHtml}</div>
                    </div>
                </div>
            </div>
        `;

        lucide.createIcons();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function backToSkillsPage() {
        const menuPeople = document.getElementById('menu-people');
        if (menuPeople) menuPeople.style.display = 'none';
        const profileView = document.getElementById('people-profile-view');
        if (profileView) profileView.innerHTML = '';
        showContent('technician-skills');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const m = document.getElementById('modal-team-members');
            if (m && m.style.display === 'flex') closeTeamModal();
        }
    });

    document.addEventListener('DOMContentLoaded', () => switchView('individual'));

    document.addEventListener('change', e => {
        if (e.target.matches('.check-card input[type="checkbox"]')) {
            e.target.closest('.check-card').classList.toggle('checked', e.target.checked);
        }
    });

    function toggleCompCard(sel) {
        const card = sel.closest('.comp-card');
        card.classList.toggle('has-value', sel.value && sel.value !== 'none');
    }

    function previewPhoto(e) {
        const file = e.target.files[0]; if (!file) return;
        const r = new FileReader();
        r.onload = ev => {
            const img = document.getElementById('photo-preview');
            img.src = ev.target.result; img.style.display = 'block';
            document.getElementById('photo-placeholder').style.display = 'none';
        };
        r.readAsDataURL(file);
    }

    function syncTeamWithPosition() {
        const pos = document.getElementById('select-position').value;
        const selectTeam = document.getElementById('select-team');
        const inputTeam = document.getElementById('input-team-head');
        const hint = document.getElementById('team-hint');
        const empName = document.getElementById('input-emp-name').value.trim();
        if (pos === 'หัวหน้าทีม') {
            selectTeam.style.display = 'none'; selectTeam.disabled = true;
            inputTeam.style.display = ''; inputTeam.disabled = false;
            inputTeam.value = empName; hint.style.display = 'inline';
        } else {
            inputTeam.style.display = 'none'; inputTeam.disabled = true;
            selectTeam.style.display = ''; selectTeam.disabled = false;
            hint.style.display = 'none';
        }
        validateAddForm();
    }

    function validateAddForm() {
        const pos = document.getElementById('select-position').value;
        const selTeam = document.getElementById('select-team');
        const inputTeam = document.getElementById('input-team-head');
        const btn = document.getElementById('btn-submit-tech');
        if (!btn) return;
        let teamVal = '';
        if (pos === 'หัวหน้าทีม') teamVal = (inputTeam.value || '').trim();
        else if (pos === 'ลูกทีม') teamVal = selTeam.value;
        const ok = !!pos && !!teamVal;
        btn.disabled = !ok;
        btn.title = ok ? '' : 'กรุณาเลือก "ตำแหน่ง" และ "ทีม" ให้ครบก่อนบันทึก';
    }

    document.getElementById('input-emp-name').addEventListener('input', function () {
        if (document.getElementById('select-position').value === 'หัวหน้าทีม') {
            document.getElementById('input-team-head').value = this.value.trim();
            validateAddForm();
        }
    });

    document.getElementById('form-add-tech').addEventListener('submit', function(e) {
        const pos = document.getElementById('select-position').value;
        const selTeam = document.getElementById('select-team');
        const inputTeam = document.getElementById('input-team-head');
        let teamVal = '';
        if (pos === 'หัวหน้าทีม') teamVal = (inputTeam.value || '').trim();
        else if (pos === 'ลูกทีม') teamVal = selTeam.value;
        if (!pos || !teamVal) {
            e.preventDefault();
            alert('กรุณาเลือก "ตำแหน่ง" และ "ทีม" ให้ครบก่อนบันทึก');
            return false;
        }
    });

    syncTeamWithPosition();
    validateAddForm();

    let licIndex = 0;
    function addLicenseRow() {
        const idx = licIndex++;
        const row = document.createElement('div');
        row.className = 'lic-row';
        row.innerHTML = `
            <input class="finput" type="text" name="licenses[${idx}][title]" placeholder="ชื่อใบอนุญาต">
            <input class="finput" type="text" name="licenses[${idx}][doc_no]" placeholder="เลขที่เอกสาร">
            <input class="finput" type="text" name="licenses[${idx}][date_issued]" placeholder="วันที่ออก">
            <input class="finput" type="file" name="licenses[${idx}][file_upload]" accept=".pdf,.jpg,.jpeg,.png,.webp">
            <button type="button" class="lic-del" onclick="this.parentElement.remove()">ลบ</button>
        `;
        document.getElementById('lic-list').appendChild(row);
    }

    function addCustomSoftware() {
        const input = document.getElementById('sw-extra-input');
        const value = input.value.trim();
        if (!value) return;
        const exists = [...document.querySelectorAll('#software-options-container input')].some(i => i.value.toLowerCase() === value.toLowerCase());
        if (exists) { input.value = ''; return; }
        const label = document.createElement('label');
        label.className = 'check-card checked';
        label.innerHTML = `<input type="checkbox" name="software_tools[]" value="${value}" checked><span>${value}</span>`;
        document.getElementById('software-options-container').appendChild(label);
        input.value = '';
    }
    document.getElementById('sw-extra-input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); addCustomSoftware(); }
    });
</script>

</body>
</html>