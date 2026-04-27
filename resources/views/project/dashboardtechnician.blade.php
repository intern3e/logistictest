<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Triple 3E Group — ทีมช่างและตารางงาน</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --or:   #f04709;
    --or2:  #ffffff;
    --or3:  #f78833;
    --or4:  #ff9100;
    --or5:  #fa8c0e;
    --or6:  #f68812;
    --dk:   #1a1512;
    --dk2:  #2d2521;
    --md:   #6b6560;
    --lt:   #f5f3f0;
    --wh:   #ffffff;
    --bg:   #f4f1ec;
    --bd:   #e3ddd6;
    --bd2:  #d4cdc4;
    --gr:   #06fa60;
    --bl:   #2563eb;
    --rd:   #dc2626;
    --yl:   #fabd78;
    --shadow-sm: 0 1px 3px rgba(26,21,18,0.08), 0 1px 2px rgba(26,21,18,0.04);
    --shadow-md: 0 4px 12px rgba(26,21,18,0.10), 0 2px 4px rgba(26,21,18,0.06);
    --shadow-lg: 0 12px 32px rgba(26,21,18,0.12), 0 4px 10px rgba(26,21,18,0.07);
    --shadow-or: 0 4px 16px rgba(249,115,22,0.22);
    --radius-sm: 8px;
    --radius-md: 14px;
    --radius-lg: 18px;
    --radius-xl: 24px;
  }

  html { font-size: 16px; }
  body {
    font-family: 'IBM Plex Sans Thai', 'Sarabun', sans-serif;
    background: var(--bg);
    color: var(--dk);
    min-height: 100vh;
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
  }

  /* NAV */
  nav { background: var(--wh); border-bottom: 3px solid var(--or); position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 16px rgba(249,115,22,0.10); }
  .nav-inner { max-width: 1440px; margin: 0 auto; display: flex; align-items: center; height: 70px; padding: 0 32px; gap: 10px; }
  .nav-logo { display: flex; align-items: center; gap: 14px; margin-right: 14px; }
  .nav-mark { width: 44px; height: 44px; background: linear-gradient(135deg, var(--or), #ea580c); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 17px; font-weight: 900; color: #fff; box-shadow: var(--shadow-or); letter-spacing: -0.5px; }
  .nav-title { font-size: 17px; font-weight: 800; color: var(--dk); letter-spacing: -0.2px; }
  .nav-sub { font-size: 12px; color: var(--md); font-weight: 500; margin-top: 1px; }
  .nav-spacer { flex: 1; }
  .nav-badge { background: var(--or3); border: 1.5px solid var(--or5); color: var(--or2); font-size: 13px; font-weight: 700; letter-spacing: 0.02em; padding: 8px 18px; border-radius: 24px; text-decoration: none; transition: all 0.18s; display: inline-flex; align-items: center; gap: 6px; }
  .nav-badge:hover { background: var(--or); color: #fff; border-color: var(--or); box-shadow: var(--shadow-or); }

  /* MAIN */
  .main { max-width: 1440px; margin: 0 auto; padding: 28px 32px; }

  /* FLASH */
  .flash {
    padding: 14px 20px;
    border-radius: var(--radius-md);
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    overflow: hidden;
    animation: flashIn 0.3s ease, flashOut 0.5s ease 4.5s forwards;
  }
  .flash::before { font-size: 18px; }
  .flash::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: currentColor;
    opacity: 0.4;
    animation: flashProgress 5s linear forwards;
  }
  .flash-success { background: #dcfce7; color: #14532d; border: 1.5px solid #86efac; }
  .flash-success::before { content: '✓'; }
  .flash-error { background: #fee2e2; color: #7f1d1d; border: 1.5px solid #fca5a5; }
  .flash-error::before { content: '✕'; }
  @keyframes flashIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
  @keyframes flashOut {
    from { opacity: 1; transform: translateY(0); max-height: 100px; margin-bottom: 20px; padding-top: 14px; padding-bottom: 14px; }
    to { opacity: 0; transform: translateY(-10px); max-height: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; border-width: 0; }
  }
  @keyframes flashProgress { from { width: 100%; } to { width: 0%; } }

  /* FILTER BAR */
  .filter-bar { background: var(--wh); border: 1.5px solid var(--bd); border-radius: var(--radius-md); padding: 16px 20px; margin-bottom: 22px; display: flex; gap: 12px; flex-wrap: wrap; align-items: center; box-shadow: var(--shadow-sm); }
  .filter-bar input, .filter-bar select { padding: 10px 16px; border-radius: var(--radius-sm); border: 1.5px solid var(--bd); background: var(--lt); font-size: 14px; font-family: inherit; color: var(--dk); outline: none; transition: all 0.18s; font-weight: 500; }
  .filter-bar input:focus, .filter-bar select:focus { border-color: var(--or); background: var(--wh); box-shadow: 0 0 0 3px rgba(249,115,22,0.12); }
  .filter-bar input { min-width: 220px; }
  .filter-bar input::placeholder { color: #b5aea7; }

  /* BUTTONS */
  .btn { padding: 10px 22px; border-radius: var(--radius-sm); border: none; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all 0.18s; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.01em; }
  .btn-primary { background: linear-gradient(135deg, var(--or), #ea580c); color: #fff; box-shadow: 0 2px 8px rgba(249,115,22,0.30); }
  .btn-primary:hover { background: linear-gradient(135deg, #ea580c, #c2410c); box-shadow: var(--shadow-or); transform: translateY(-1px); }
  .btn-ghost { background: var(--lt); color: var(--md); border: 1.5px solid var(--bd); }
  .btn-ghost:hover { background: var(--bd); color: var(--dk); }
  .btn-sm { padding: 6px 14px; font-size: 12px; border-radius: 7px; }
  .btn-danger { background: #fee2e2; color: #991b1b; border: 1.5px solid #fca5a5; }
  .btn-danger:hover { background: #fca5a5; color: #7f1d1d; }

  /* TABS */
  .tabs { display: flex; gap: 2px; margin-bottom: 20px; border-bottom: 2px solid var(--bd); align-items: flex-end; }
  .tab { padding: 12px 26px; font-size: 15px; font-weight: 700; color: var(--md); cursor: pointer; border: none; background: none; font-family: inherit; border-bottom: 3px solid transparent; margin-bottom: -2px; transition: all 0.18s; letter-spacing: 0.01em; }
  .tab:hover { color: var(--or); }
  .tab.active { color: var(--or); border-bottom-color: var(--or); }
  .tab-actions { margin-left: auto; display: flex; gap: 10px; padding-bottom: 8px; }
  .panel { display: none; }
  .panel.active { display: block; animation: fi 0.22s ease; }
  @keyframes fi { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

  /* TEAM CARDS */
  .team-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 16px; margin-bottom: 28px; }
  .team-card { background: var(--wh); border: 1.5px solid var(--bd); border-radius: var(--radius-lg); overflow: hidden; transition: all 0.22s; box-shadow: var(--shadow-sm); }
  .team-card:hover { box-shadow: var(--shadow-lg); border-color: var(--or5); transform: translateY(-3px); }
  .team-head-bar { padding: 16px 18px; background: linear-gradient(135deg, #454444 0%, #454444 100%); border-bottom: 2px solid var(--or5); display: flex; align-items: center; gap: 12px; }
  .team-title { font-size: 16px; font-weight: 800; color: var(--or2); letter-spacing: -0.2px; }
  .team-meta { font-size: 12px; color: var(--or); margin-top: 3px; font-weight: 600; }
  .team-cal-btn {
    background: linear-gradient(135deg, var(--or), #ea580c);
    color: #fff;
    border: none;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.18s;
    box-shadow: 0 2px 8px rgba(249,115,22,0.35);
    font-family: inherit;
    letter-spacing: 0.02em;
    flex-shrink: 0;
  }
  .team-cal-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(249,115,22,0.45); }
  .team-cal-btn svg { width: 14px; height: 14px; stroke: #fff; fill: none; stroke-width: 2; }
  .team-cal-btn .badge-count {
    background: rgba(255,255,255,0.25);
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 10px;
    margin-left: 2px;
  }
  .team-body { padding: 4px 0; }
  .member { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-bottom: 1px solid #f5f2ee; cursor: pointer; transition: background 0.15s; }
  .member:last-child { border-bottom: none; }
  .member:hover { background: #fff7ed; }
  .m-av { width: 38px; height: 38px; border-radius: 50%; border: 2px solid var(--or5); overflow: hidden; background: var(--or4); flex-shrink: 0; }
  .m-av img { width: 100%; height: 100%; object-fit: cover; }
  .m-name { font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 7px; color: var(--dk); }
  .m-role { font-size: 12px; color: var(--md); margin-top: 2px; font-weight: 500; }
  .head-tag { background: linear-gradient(135deg, var(--or), #f90b0b); color: #f8f8f8; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 5px; letter-spacing: 0.06em; }
  .status-dot { width: 9px; height: 9px; border-radius: 50%; margin-left: auto; flex-shrink: 0; box-shadow: 0 0 0 2px #fdfdfd; }
  .st-active { background: var(--gr); }
  .st-leave  { background: var(--rd); }

  /* TABLE */
  .table-wrap { background: var(--wh); border: 1.5px solid var(--bd); border-radius: var(--radius-lg); overflow-x: auto; box-shadow: var(--shadow-sm); }
  table { width: 100%; border-collapse: collapse; min-width: 960px; }
  th, td { padding: 13px 16px; text-align: left; font-size: 14px; border-bottom: 1px solid var(--bd); }
  th { background: linear-gradient(to bottom, #f9f7f4, var(--lt)); font-weight: 800; font-size: 12px; letter-spacing: 0.06em; text-transform: uppercase; color: var(--md); border-bottom: 2px solid var(--bd2); }
  tbody tr:hover { background: #fff8f4; }
  tbody tr:last-child td { border-bottom: none; }
  .so-code { font-family: 'Courier New', monospace; font-weight: 800; color: var(--or2); font-size: 13px; background: var(--or3); padding: 3px 9px; border-radius: 6px; border: 1px solid var(--or5); letter-spacing: 0.04em; }
  .badge { padding: 5px 13px; border-radius: 24px; font-size: 12px; font-weight: 700; display: inline-block; letter-spacing: 0.02em; }
  .bg-pending  { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
  .bg-progress { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
  .bg-done     { background: #dcfce7; color: #14532d; border: 1px solid #86efac; }
  .bg-cancel   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
  .empty { text-align: center; padding: 60px 20px; color: var(--md); font-size: 16px; font-weight: 500; }

  /* MODALS */
  .overlay { display: none; position: fixed; inset: 0; z-index: 500; background: rgba(26,21,18,0.65); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 16px; }
  .overlay.open { display: flex; animation: fadeIn 0.2s ease; }
  @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
  .pmodal { background: var(--wh); border-radius: var(--radius-xl); width: 760px; max-width: 100%; overflow: hidden; box-shadow: 0 28px 80px rgba(0,0,0,0.22), 0 0 0 1px rgba(241,71,4,0.08); max-height: 92vh; overflow-y: auto; animation: slideUp 0.22s ease; }
  @keyframes slideUp { from { opacity: 0; transform: translateY(20px) scale(0.98); } to { opacity: 1; transform: none; } }
  .pmodal::-webkit-scrollbar { width: 6px; }
  .pmodal::-webkit-scrollbar-track { background: var(--lt); }
  .pmodal::-webkit-scrollbar-thumb { background: var(--or5); border-radius: 3px; }
  .pmodal-strip { height: 5px; background: linear-gradient(90deg, var(--or2), var(--or), #fbbf24, var(--or6)); }
  .p-top { padding: 20px 24px; display: flex; gap: 16px; align-items: flex-start; border-bottom: 1.5px solid var(--bd); position: relative; background: linear-gradient(to bottom, #fffbf7, var(--wh)); }
  .p-photo { width: 88px; height: 88px; border-radius: 16px; overflow: hidden; border: 2.5px solid var(--or5); background: var(--or4); flex-shrink: 0; box-shadow: var(--shadow-md); }
  .p-photo img { width: 100%; height: 100%; object-fit: cover; }
  .p-company { font-size: 11px; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: var(--or); margin-bottom: 5px; }
  .p-fullname { font-size: 20px; font-weight: 800; line-height: 1.25; letter-spacing: -0.3px; color: var(--dk); }
  #m-name-eng { font-size: 13px; color: var(--md) !important; font-weight: 500; margin-top: 3px; }
  .p-role-tag { display: inline-block; margin-top: 8px; background: var(--or3); border: 1.5px solid var(--or5); color: var(--or2); font-size: 12px; font-weight: 700; padding: 4px 14px; border-radius: 24px; }
  .p-close { position: absolute; top: 16px; right: 16px; width: 32px; height: 32px; border-radius: 50%; background: var(--lt); border: 1.5px solid var(--bd); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; color: var(--md); transition: all 0.18s; font-weight: 700; }
  .p-close:hover { background: var(--or); color: #fff; border-color: var(--or); }
  .p-body { padding: 22px 24px; background: var(--wh); }
  .igrid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
  .ilabel { font-size: 11px; font-weight: 800; letter-spacing: 0.10em; text-transform: uppercase; color: #f4840c; margin-bottom: 4px; }
  .ival { font-size: 15px; font-weight: 700; color: var(--dk); }
  .ival.phone { color: var(--or2); }
  #m-status { color: var(--gr); font-weight: 700; font-size: 15px; }
  .sk-wrap { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
  .sk { font-size: 13px; font-weight: 600; padding: 5px 13px; border-radius: 20px; background: var(--or3); color: var(--or2); border: 1.5px solid var(--or5); }

  /* FORM */
  .finput { width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1.5px solid var(--bd); font-family: inherit; font-size: 14px; color: var(--dk); outline: none; transition: all 0.18s; background: var(--wh); font-weight: 500; }
  .finput:focus { border-color: var(--or); box-shadow: 0 0 0 3px rgba(249,115,22,0.12); }
  .finput::placeholder { color: #c4bdb6; }
  .frow { margin-bottom: 16px; }
  .ferr { background: #fee2e2; color: #f30909; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 14px; margin-bottom: 16px; border: 1.5px solid #fca5a5; font-weight: 600; }
  .factions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px; padding-top: 16px; border-top: 1.5px solid var(--bd); }

  .skill-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 8px; }
  .skill-check { display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: var(--radius-sm); border: 1.5px solid var(--bd); cursor: pointer; font-size: 13px; font-weight: 600; transition: all 0.18s; user-select: none; color: var(--dk); }
  .skill-check:hover { border-color: var(--or); background: var(--or3); color: var(--or2); }
  .skill-check input[type=checkbox] { accent-color: var(--or); width: 15px; height: 15px; }
  .skill-check.checked { border-color: var(--or); background: var(--or3); color: var(--or2); }

  .company-row { display: flex; gap: 8px; align-items: center; }
  .company-row .finput { flex: 1; }
  .btn-other { padding: 10px 16px; border-radius: var(--radius-sm); border: 1.5px solid var(--or5); background: var(--or3); color: var(--or2); font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit; white-space: nowrap; transition: all 0.18s; }
  .btn-other:hover { background: var(--or); color: #fff; border-color: var(--or); }

  .section-h { font-size: 14px; font-weight: 800; color: var(--or2); margin: 22px 0 12px; padding-bottom: 8px; border-bottom: 2px solid var(--or5); letter-spacing: 0.02em; display: flex; align-items: center; gap: 8px; }
  .section-h::before { content: ''; display: inline-block; width: 4px; height: 16px; background: var(--or); border-radius: 2px; }

  .comp-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
  .comp-card { border: 1.5px solid var(--bd); border-radius: var(--radius-md); padding: 10px 12px; background: var(--wh); transition: border-color 0.18s; }
  .comp-card:hover { border-color: var(--or5); }
  .comp-head { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
  .comp-label { font-size: 13px; font-weight: 700; color: var(--dk); }
  .comp-code { font-size: 10px; color: var(--md); font-weight: 700; background: var(--lt); padding: 2px 7px; border-radius: 4px; margin-left: auto; letter-spacing: 0.05em; }
  .comp-select { width: 100%; padding: 7px 10px; border-radius: var(--radius-sm); border: 1.5px solid var(--bd); font-family: inherit; font-size: 13px; font-weight: 600; background: var(--wh); cursor: pointer; transition: all 0.18s; }
  .comp-select:focus { outline: none; border-color: var(--or); }
  .comp-select.lv-basic  { background: #fef3c7; border-color: #fde68a; color: #92400e; }
  .comp-select.lv-skill  { background: #dbeafe; border-color: #bfdbfe; color: #1e40af; }
  .comp-select.lv-expert { background: #28ed42e3; border-color: #15ee65; color: #000000; font-weight: 800; }

  .lic-list { display: flex; flex-direction: column; gap: 12px; }
  .lic-item { border: 1.5px solid var(--bd); border-radius: var(--radius-md); padding: 14px 16px; background: var(--lt); transition: border-color 0.18s; }
  .lic-item:hover { border-color: var(--or5); }
  .lic-item-head { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
  .lic-num { font-size: 12px; font-weight: 800; color: var(--or2); background: var(--or3); padding: 3px 10px; border-radius: 14px; border: 1px solid var(--or5); }
  .lic-del { margin-left: auto; padding: 4px 12px; background: #ec0101; color: #191513; border: none; border-radius: 7px; cursor: pointer; font-size: 12px; font-weight: 700; font-family: inherit; transition: all 0.18s; }
  .lic-del:hover { background: #fca5a5; }
  .lic-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
  .lic-file-row { display: flex; gap: 8px; align-items: center; margin-top: 8px; flex-wrap: wrap; }
  .lic-file-current { font-size: 12px; color: var(--gr); font-weight: 700; padding: 4px 10px; background: #dcfce7; border-radius: 6px; border: 1px solid #13cc57; text-decoration: none; }
  .lic-file-input { font-size: 12px; flex: 1; }
  .btn-add-lic { width: 100%; padding: 12px; border: 2px dashed var(--or5); background: var(--or3); color: var(--or2); border-radius: var(--radius-md); cursor: pointer; font-size: 14px; font-weight: 700; font-family: inherit; transition: all 0.18s; margin-top: 12px; }
  .btn-add-lic:hover { border-color: var(--or); background: var(--or4); }

  .sw-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 8px; }
  .sw-custom-row { display: flex; gap: 8px; margin-top: 10px; }
  .sw-custom-row input { flex: 1; }
  .sw-custom-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
  .sw-tag { background: var(--bl); color: #fff; font-size: 13px; font-weight: 600; padding: 4px 13px; border-radius: 14px; display: inline-flex; align-items: center; gap: 7px; }
  .sw-tag .x { cursor: pointer; font-weight: 800; opacity: 0.85; font-size: 11px; }
  .sw-tag .x:hover { opacity: 1; }

  .profile-section { margin-top: 16px; }
  .profile-comp-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; margin-top: 8px; }
  .pc-card { display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: var(--radius-sm); background: var(--lt); border: 1.5px solid var(--bd); font-size: 13px; font-weight: 500; }
  .pc-card.lv-basic  { background: #fef3c7; border-color: #fde68a; }
  .pc-card.lv-skill  { background: #dbeafe; border-color: #bfdbfe; }
  .pc-card.lv-expert { background: #28ed42e3; border-color: #ffffff; font-weight: 700; }
  .pc-card.lv-none   { opacity: 0.45; }
  .pc-lbl { flex: 1; color: var(--dk); }
  .pc-val { font-weight: 800; font-size: 11px; letter-spacing: 0.03em; color: var(--dk); }
  .profile-lic-item { padding: 10px 14px; border: 1.5px solid var(--bd); border-radius: var(--radius-sm); margin-bottom: 8px; background: var(--lt); }
  .profile-lic-title { font-weight: 800; font-size: 14px; color: var(--or2); }
  .profile-lic-meta { font-size: 12px; color: var(--md); margin-top: 3px; font-weight: 500; }
  .profile-lic-meta a { color: var(--bl); text-decoration: underline; }

  .dob-row { display: flex; gap: 8px; align-items: center; }
  .dob-row input[type=date] { flex: 1; }
  .dob-be { font-size: 12px; color: var(--or2); font-weight: 700; background: var(--or3); padding: 6px 12px; border-radius: var(--radius-sm); border: 1.5px solid var(--or5); white-space: nowrap; min-width: 140px; text-align: center; }
  .emp-id-note { font-size: 12px; color: var(--md); margin-top: 5px; font-weight: 500; }
  .head-info-box { padding: 12px 16px; background: var(--or3); border: 1.5px solid var(--or5); border-radius: var(--radius-sm); font-size: 14px; color: var(--or2); font-weight: 600; line-height: 1.6; }

  .modal-form-header { padding: 18px 24px 14px; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(to bottom, #fffbf7, var(--wh)); border-bottom: 1.5px solid var(--bd); }
  .modal-form-title { font-size: 20px; font-weight: 800; color: var(--dk); letter-spacing: -0.3px; }

  .resume-top { display: flex; flex-direction: row; gap: 24px; align-items: flex-start; padding: 20px 24px; background: linear-gradient(135deg, #fffbf7 0%, #fff8f0 100%); border-bottom: 1.5px solid var(--bd); }
  .resume-photo-col { flex-shrink: 0; order: -1; display: flex; flex-direction: column; align-items: center; gap: 8px; padding-top: 2px; padding-bottom: 4px; }
  .resume-fields { flex: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .resume-fields .frow { margin-bottom: 0; }
  .resume-photo-box { width: 112px; height: 140px; border: 2.5px dashed var(--or5); border-radius: 12px; overflow: hidden; cursor: pointer; background: #fff8f0; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: all 0.20s; position: relative; box-shadow: 0 4px 14px rgba(249,115,22,0.12); }
  .resume-photo-box:hover { border-color: var(--or); background: var(--or3); box-shadow: var(--shadow-or); transform: translateY(-2px); }
  .resume-photo-box:hover .resume-photo-overlay { opacity: 1; }
  .resume-photo-box img.resume-img { width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0; display: none; border-radius: 10px; }
  .resume-photo-overlay { position: absolute; inset: 0; background: rgba(240,71,9,0.72); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.18s; border-radius: 10px; z-index: 2; }
  .resume-photo-overlay span { color: #fff; font-size: 12px; font-weight: 800; letter-spacing: 0.03em; text-align: center; line-height: 1.4; padding: 0 8px; }
  .resume-photo-placeholder { display: flex; flex-direction: column; align-items: center; gap: 6px; color: var(--md); }
  .resume-photo-placeholder svg { width: 32px; height: 32px; opacity: 0.55; }
  .resume-photo-placeholder span { font-size: 11px; font-weight: 700; text-align: center; line-height: 1.4; color: var(--md); letter-spacing: 0.02em; }
  .resume-photo-label { font-size: 11px; font-weight: 700; color: var(--or3); letter-spacing: 0.08em; text-transform: uppercase; text-align: center; }
  .resume-badge { position: absolute; top: -6px; right: -6px; background: var(--or); color: #fff; font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 6px; letter-spacing: 0.06em; z-index: 3; box-shadow: 0 2px 6px rgba(240,71,9,0.35); }
  input[type=file]#add-img-input, input[type=file]#et-img-input { display: none; }

  /* ===================== TEAM CALENDAR FULLSCREEN ===================== */
  .tcal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 700;
    background: rgba(26,21,18,0.78);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
  }
  .tcal-overlay.open {
    display: flex;
    flex-direction: column;
    animation: fadeIn 0.22s ease;
  }
  .tcal-fullscreen {
    background: var(--bg);
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: tcalSlideUp 0.28s ease;
  }
  @keyframes tcalSlideUp {
    from { opacity: 0; transform: translateY(30px) scale(0.985); }
    to { opacity: 1; transform: none; }
  }

  .tcal-header {
    background: linear-gradient(135deg, #2d2521 0%, #1a1512 100%);
    border-bottom: 4px solid var(--or);
    padding: 20px 32px;
    display: flex;
    align-items: center;
    gap: 18px;
    flex-shrink: 0;
    box-shadow: 0 4px 16px rgba(0,0,0,0.25);
  }
  .tcal-icon-box {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--or), #ea580c);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: var(--shadow-or);
  }
  .tcal-icon-box svg { width: 24px; height: 24px; stroke: #fff; fill: none; stroke-width: 2; }
  .tcal-title-block { flex: 1; min-width: 0; }
  .tcal-title-eyebrow {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--or3);
    margin-bottom: 3px;
  }
  .tcal-title {
    font-size: 22px;
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.3px;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .tcal-sub {
    font-size: 13px;
    color: #d4cdc4;
    font-weight: 500;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }
  .tcal-sub-stat {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(255,255,255,0.08);
    padding: 3px 10px;
    border-radius: 14px;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
  }
  .tcal-sub-stat.has-jobs {
    background: rgba(240,71,9,0.25);
    border: 1px solid rgba(240,71,9,0.45);
  }
  .tcal-close {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    border: 1.5px solid rgba(255,255,255,0.2);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #fff;
    transition: all 0.18s;
    font-weight: 700;
    flex-shrink: 0;
  }
  .tcal-close:hover { background: var(--or); border-color: var(--or); transform: rotate(90deg); }

  .tcal-body {
    flex: 1;
    overflow-y: auto;
    padding: 24px 32px;
  }
  .tcal-body::-webkit-scrollbar { width: 8px; }
  .tcal-body::-webkit-scrollbar-track { background: var(--lt); }
  .tcal-body::-webkit-scrollbar-thumb { background: var(--or5); border-radius: 4px; }

  .tcal-content { max-width: 1200px; margin: 0 auto; }

  .cal-selector-bar {
    background: var(--wh); border-radius: var(--radius-md); padding: 6px;
    display: flex; gap: 6px; margin-bottom: 14px;
    box-shadow: var(--shadow-sm); border: 1.5px solid var(--bd);
  }
  .cal-sel-btn {
    flex: 1; padding: 10px 14px; border-radius: 10px; border: none;
    background: none; cursor: pointer; font-family: inherit;
    display: flex; align-items: center; gap: 10px; transition: background .15s; text-align: left;
  }
  .cal-sel-btn.active { background: #fff0e6; }
  .cal-sel-btn:hover:not(.active) { background: var(--lt); }
  .cal-sel-icon {
    width: 32px; height: 32px; border-radius: 8px; background: var(--or);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    transition: background .2s;
  }
  .cal-sel-icon svg { width: 15px; height: 15px; stroke: #fff; fill: none; stroke-width: 1.8; }
  .cal-sel-icon.gray { background: #e2e2e2; }
  .cal-sel-icon.gray svg { stroke: #888; }
  .cal-sel-lbl { font-size: 13px; font-weight: 700; color: var(--dk); }
  .cal-sel-sub { font-size: 11px; color: var(--md); margin-top: 1px; font-weight: 500; }

  .cal-wrap {
    background: var(--wh); border: 1.5px solid var(--bd);
    border-radius: var(--radius-lg); overflow: hidden;
    box-shadow: var(--shadow-sm);
  }
  .cal-months { display: grid; grid-template-columns: 1fr 1fr; }
  .cal-month-block { padding: 18px 18px 14px; }
  .cal-month-block:first-child { border-right: 1px solid var(--bd); }
  .cal-mnav { display: flex; align-items: center; margin-bottom: 14px; }
  .cal-mnav-btn {
    width: 28px; height: 28px; border-radius: 50%; border: none;
    background: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
    font-size: 17px; color: var(--md); transition: background .15s;
  }
  .cal-mnav-btn:hover { background: var(--lt); }
  .cal-mnav-btn.invisible { visibility: hidden; }
  .cal-mname { flex: 1; text-align: center; font-size: 14px; font-weight: 700; color: var(--dk); }
  .cal-dhdrs { display: grid; grid-template-columns: repeat(7,1fr); margin-bottom: 6px; }
  .cal-dhdr { text-align: center; font-size: 11px; font-weight: 600; color: var(--md); padding: 3px 0; }
  .cal-dgrid { display: grid; grid-template-columns: repeat(7,1fr); row-gap: 4px; }

  .cal-day {
    position: relative; text-align: center; cursor: pointer; user-select: none;
    padding: 4px 0 14px;
  }
  .cal-di {
    width: 33px; height: 33px; margin: 0 auto; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 500; color: var(--dk);
    transition: background .12s, color .12s; position: relative; z-index: 2;
  }
  .cal-day:hover .cal-di { background: var(--lt); }
  .cal-day.cal-other .cal-di { color: #ccc; }
  .cal-day.cal-other { pointer-events: none; }
  .cal-day.cal-today .cal-di { border: 2px solid var(--or); color: var(--or); font-weight: 700; }
  .cal-day.cal-sel-s .cal-di, .cal-day.cal-sel-e .cal-di { background: var(--or); color: #fff; font-weight: 700; }
  .cal-day.cal-in-range::before { content: ''; position: absolute; top: 8px; height: 33px; background: #ffe1cc; left: 0; right: 0; z-index: 1; }
  .cal-day.cal-sel-s::before { content: ''; position: absolute; top: 8px; height: 33px; background: #ffe1cc; left: 50%; right: 0; z-index: 1; }
  .cal-day.cal-sel-e::before { content: ''; position: absolute; top: 8px; height: 33px; background: #ffe1cc; left: 0; right: 50%; z-index: 1; }
  .cal-day.cal-sel-s.cal-sel-e::before { display: none; }

  .cal-evdots {
    position: absolute; bottom: 2px; left: 50%; transform: translateX(-50%);
    display: flex; gap: 3px; z-index: 3; align-items: center;
    height: 8px;
  }
  .cal-evdot {
    width: 6px; height: 6px; border-radius: 50%;
    flex-shrink: 0; transition: transform .12s;
    box-shadow: 0 0 0 1px rgba(255,255,255,0.7);
    background: var(--or);
  }
  .cal-day.has-event:hover .cal-evdot { transform: scale(1.4); }
  .cal-evcount {
    font-size: 8px; font-weight: 800;
    color: var(--md); margin-left: 1px; line-height: 1;
  }
  .cal-day.has-event:hover .cal-di {
    background: #fff8f0; color: var(--dk);
  }

  .cal-legend { display: flex; gap: 14px; flex-wrap: wrap; padding: 10px 18px; border-top: 1px solid var(--bd); background: #fafaf9; font-size: 11px; color: var(--md); }
  .cal-leg { display: flex; align-items: center; gap: 5px; }
  .cal-leg-dot { width: 8px; height: 8px; border-radius: 50%; }
  .cal-footer { padding: 12px 18px; border-top: 1px solid var(--bd); background: #fafaf9; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
  .cal-footer-note { font-size: 12px; color: var(--md); flex: 1; min-width: 160px; }

  /* Calendar Day Popup */
  .cal-popup-bg { display: none; position: fixed; inset: 0; background: rgba(26,21,18,.62); backdrop-filter: blur(7px); -webkit-backdrop-filter: blur(7px); align-items: center; justify-content: center; padding: 16px; z-index: 800; }
  .cal-popup-bg.open { display: flex; animation: calFI .18s ease; }
  @keyframes calFI { from { opacity: 0; } to { opacity: 1; } }
  .cal-popup { background: var(--wh); border-radius: var(--radius-xl); width: 520px; max-width: 100%; box-shadow: 0 24px 60px rgba(0,0,0,.22); overflow: hidden; animation: calSU .2s ease; max-height: 88vh; display: flex; flex-direction: column; }
  @keyframes calSU { from { opacity: 0; transform: translateY(14px) scale(.98); } to { opacity: 1; transform: none; } }
  .cal-popup-body { overflow-y: auto; flex: 1; }
  .cal-popup-body::-webkit-scrollbar { width: 5px; }
  .cal-popup-body::-webkit-scrollbar-thumb { background: var(--or5); border-radius: 3px; }
  .cal-popup-strip { height: 4px; background: linear-gradient(90deg, var(--or), #f78833, var(--or6)); flex-shrink: 0; }
  .cal-popup-head { padding: 14px 18px; border-bottom: 1px solid var(--bd); display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
  .cal-popup-date { background: #fff0e6; color: #c84b07; font-size: 13px; font-weight: 700; padding: 6px 14px; border-radius: 8px; border: 1px solid #fdc59f; flex: 1; text-align: center; }
  .cal-popup-count { background: var(--or3); color: var(--or2); font-size: 12px; font-weight: 800; padding: 4px 12px; border-radius: 20px; border: 1px solid var(--or5); white-space: nowrap; }
  .cal-popup-close { width: 28px; height: 28px; border-radius: 50%; border: 1px solid var(--bd); background: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 13px; color: var(--md); transition: all .15s; flex-shrink: 0; }
  .cal-popup-close:hover { background: var(--or); color: #fff; border-color: var(--or); }
  .cal-popup-inner { padding: 14px 18px; }

  /* ========== IMPROVED EVENT CARD ========== */
  .cal-ev-card {
    border: 1.5px solid var(--bd); border-radius: var(--radius-md);
    padding: 14px 14px 14px 20px; margin-bottom: 10px;
    transition: border-color .15s, box-shadow .15s;
    position: relative; overflow: hidden;
    background: var(--wh);
  }
  .cal-ev-card::before {
    content: ''; position: absolute; left: 0; top: 0; bottom: 0;
    width: 5px; background: var(--or);
  }
  .cal-ev-card:last-child { margin-bottom: 0; }
  .cal-ev-card:hover { border-color: var(--or5); box-shadow: var(--shadow-sm); }
  .cal-ev-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin-bottom: 8px; }
  .cal-so { display: inline-block; background: #fff0e6; color: #c84b07; border: 1px solid #fdc59f; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 5px; letter-spacing: .05em; font-family: 'Courier New', monospace; }
  /* Customer name prominently displayed */
  .cal-ev-customer {
    font-size: 16px; font-weight: 800; color: var(--dk);
    margin-bottom: 3px;
    display: flex; align-items: center; gap: 8px;
    line-height: 1.3;
  }
  .cal-ev-customer-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; box-shadow: 0 0 0 1.5px rgba(255,255,255,0.8); background: var(--or); }
  .cal-ev-job { font-size: 12px; color: var(--md); margin-bottom: 10px; padding-left: 18px; font-weight: 500; }
  .cal-ev-meta { display: grid; grid-template-columns: auto 1fr; gap: 5px 12px; font-size: 12px; }
  .cal-ev-ml { color: var(--md); font-weight: 600; white-space: nowrap; display: flex; align-items: center; gap: 4px; }
  .cal-ev-mv { color: var(--dk); font-weight: 600; }
  .cal-ev-status { display: inline-flex; align-items: center; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-top: 10px; }
  .cst-progress { background: #dbeafe; color: #1e40af; }
  .cst-pending  { background: #fef3c7; color: #92400e; }
  .cst-done     { background: #dcfce7; color: #14532d; }
  .cst-cancelled{ background: #fee2e2; color: #991b1b; }
  .cal-empty { text-align: center; padding: 40px 20px; color: var(--md); font-size: 14px; font-weight: 500; }
  .cal-empty-icon { font-size: 36px; margin-bottom: 8px; opacity: 0.5; }

  .cal-popup-range-bar {
    padding: 8px 18px; background: #fff7ee; border-bottom: 1px solid #ffe1cc;
    font-size: 11px; color: #c84b07; font-weight: 600; flex-shrink: 0;
    display: flex; align-items: center; gap: 6px;
  }

  /* ============================================================
     DATE RANGE PICKER (Booking.com style)
  ============================================================ */
  .drp-trigger-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    border: 1.5px solid var(--bd);
    border-radius: var(--radius-md);
    overflow: visible;
    background: var(--wh);
    transition: border-color 0.18s, box-shadow 0.18s;
    cursor: pointer;
    position: relative;
  }
  .drp-trigger-row:hover { border-color: var(--or5); }
  .drp-trigger-row.open { border-color: var(--or); box-shadow: 0 0 0 3px rgba(249,115,22,0.12); }
  .drp-trigger-cell {
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--wh);
    transition: background 0.15s;
    position: relative;
  }
  .drp-trigger-cell:first-child { border-right: 1.5px solid var(--bd); border-radius: var(--radius-md) 0 0 var(--radius-md); }
  .drp-trigger-cell:last-child { border-radius: 0 var(--radius-md) var(--radius-md) 0; }
  .drp-trigger-cell.active { background: #fff7ee; }
  .drp-trigger-icon {
    width: 30px; height: 30px;
    border-radius: 8px;
    background: var(--or);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .drp-trigger-icon.gray { background: #d4cdc4; color: #fff; }
  .drp-trigger-icon svg { width: 14px; height: 14px; stroke: #fff; fill: none; stroke-width: 1.8; }
  .drp-trigger-text { flex: 1; min-width: 0; }
  .drp-trigger-main {
    font-size: 14px;
    font-weight: 700;
    color: var(--dk);
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .drp-trigger-main.empty { color: #b5aea7; font-weight: 500; }
  .drp-trigger-sub {
    font-size: 11px;
    color: var(--md);
    margin-top: 2px;
    font-weight: 500;
  }

  .drp-popover {
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    background: var(--wh);
    border: 1.5px solid var(--bd);
    border-radius: var(--radius-lg);
    box-shadow: 0 12px 40px rgba(0,0,0,0.18);
    z-index: 600;
    overflow: hidden;
    animation: drpIn 0.18s ease;
    min-width: 560px;
  }
  .drp-popover.open { display: block; }
  @keyframes drpIn {
    from { opacity: 0; transform: translateY(-6px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .drp-months { display: grid; grid-template-columns: 1fr 1fr; }
  .drp-month-block { padding: 14px; }
  .drp-month-block:first-child { border-right: 1px solid var(--bd); }
  .drp-mnav { display: flex; align-items: center; margin-bottom: 10px; }
  .drp-mnav-btn {
    width: 26px; height: 26px; border-radius: 50%; border: none;
    background: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
    font-size: 16px; color: var(--md); transition: background .15s;
  }
  .drp-mnav-btn:hover { background: var(--lt); color: var(--or); }
  .drp-mnav-btn.invisible { visibility: hidden; }
  .drp-mname { flex: 1; text-align: center; font-size: 13px; font-weight: 700; color: var(--dk); }
  .drp-dhdrs { display: grid; grid-template-columns: repeat(7,1fr); margin-bottom: 4px; }
  .drp-dhdr { text-align: center; font-size: 10px; font-weight: 600; color: var(--md); padding: 3px 0; }
  .drp-dgrid { display: grid; grid-template-columns: repeat(7,1fr); row-gap: 2px; }

  .drp-day {
    position: relative; text-align: center; cursor: pointer; user-select: none;
    padding: 2px 0;
    height: 32px;
  }
  .drp-di {
    width: 28px; height: 28px; margin: 0 auto; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 500; color: var(--dk);
    transition: background .12s, color .12s; position: relative; z-index: 2;
  }
  .drp-day:hover .drp-di { background: var(--lt); }
  .drp-day.drp-other .drp-di { color: #ccc; }
  .drp-day.drp-other { pointer-events: none; }
  .drp-day.drp-today .drp-di { border: 2px solid var(--or); color: var(--or); font-weight: 700; }
  .drp-day.drp-sel-s .drp-di, .drp-day.drp-sel-e .drp-di { background: var(--or); color: #fff; font-weight: 700; }
  .drp-day.drp-in-range::before { content: ''; position: absolute; top: 50%; transform: translateY(-50%); height: 28px; background: #ffe1cc; left: 0; right: 0; z-index: 1; }
  .drp-day.drp-sel-s::before { content: ''; position: absolute; top: 50%; transform: translateY(-50%); height: 28px; background: #ffe1cc; left: 50%; right: 0; z-index: 1; }
  .drp-day.drp-sel-e::before { content: ''; position: absolute; top: 50%; transform: translateY(-50%); height: 28px; background: #ffe1cc; left: 0; right: 50%; z-index: 1; }
  .drp-day.drp-sel-s.drp-sel-e::before { display: none; }

  .drp-footer {
    display: flex; gap: 8px; align-items: center;
    padding: 10px 14px;
    border-top: 1px solid var(--bd);
    background: #fafaf9;
  }
  .drp-footer-info { flex: 1; font-size: 12px; color: var(--md); font-weight: 600; }
  .drp-footer-info strong { color: var(--or); }
  .drp-clear-btn {
    padding: 6px 12px;
    background: var(--lt);
    border: 1.5px solid var(--bd);
    border-radius: 7px;
    cursor: pointer;
    font-family: inherit;
    font-size: 12px;
    font-weight: 700;
    color: var(--md);
    transition: all 0.15s;
  }
  .drp-clear-btn:hover { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
  .drp-done-btn {
    padding: 6px 16px;
    background: linear-gradient(135deg, var(--or), #ea580c);
    border: none;
    border-radius: 7px;
    cursor: pointer;
    font-family: inherit;
    font-size: 12px;
    font-weight: 800;
    color: #fff;
    box-shadow: 0 2px 6px rgba(249,115,22,0.30);
    transition: all 0.15s;
  }
  .drp-done-btn:hover { box-shadow: 0 4px 10px rgba(249,115,22,0.40); transform: translateY(-1px); }

  /* SCHED FORM GRID - full width */
  .sched-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
  }
  .sched-form-grid .frow { margin-bottom: 0; }
  .sched-full { grid-column: 1 / -1; }

  /* RESPONSIVE */
  @media(max-width:768px) {
    .main { padding: 16px; }
    .nav-inner { padding: 0 16px; height: 62px; }
    .igrid { grid-template-columns: 1fr; }
    .skill-grid, .sw-grid, .comp-grid { grid-template-columns: repeat(2, 1fr); }
    .team-grid { grid-template-columns: 1fr; }
    .resume-top { flex-direction: column; align-items: center; }
    .resume-photo-col { order: 0; }
    .resume-fields { grid-template-columns: 1fr; width: 100%; }
    .cal-months { grid-template-columns: 1fr; }
    .cal-month-block:first-child { border-right: none; border-bottom: 1px solid var(--bd); }
    .tcal-header { padding: 14px 18px; gap: 12px; }
    .tcal-title { font-size: 17px; }
    .tcal-icon-box { width: 40px; height: 40px; }
    .tcal-body { padding: 16px; }
    .drp-months { grid-template-columns: 1fr; }
    .drp-month-block:first-child { border-right: none; border-bottom: 1px solid var(--bd); }
    .drp-popover { min-width: unset; left: -10px; right: -10px; }
    .sched-form-grid { grid-template-columns: 1fr; }
  }
  @media(max-width:480px) {
    .skill-grid, .sw-grid { grid-template-columns: 1fr 1fr; }
    .comp-grid { grid-template-columns: 1fr; }
    .tab { padding: 10px 14px; font-size: 13px; }
    .team-cal-btn { padding: 6px 10px; font-size: 11px; }
  }
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
    <div class="flash flash-success" id="flash-msg">{{ session('success') }}</div>
  @endif
  @if($errors->has('delete'))
    <div class="flash flash-error" id="flash-msg-err">{{ $errors->first('delete') }}</div>
  @endif

  <form method="GET" class="filter-bar" action="{{ route('technician.dashboard') }}">
    <input type="text" name="search" placeholder="ค้นหาช่าง / ตำแหน่ง / ทักษะ..." value="{{ $search }}">
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
  </form>

  <div class="tabs">
    <button class="tab active" onclick="switchTab('teams',this)">ทีมช่าง ({{ $teams->count() }})</button>
    <button class="tab" onclick="switchTab('schedules',this)">ตารางงาน ({{ $schedules->count() }})</button>
    <div class="tab-actions">
      <button class="btn btn-primary" onclick="openModal('modal-tech')">+ เพิ่มช่าง</button>
      <button class="btn btn-primary" onclick="openAddSchedModal()">+ เพิ่มงาน</button>
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
            $teamSchedules = $schedules->where('team_name', $team['team_name'])->values();
          @endphp
          <div class="team-card">
            <div class="team-head-bar">
              <div style="flex:1;min-width:0">
                <div class="team-title">{{ $team['team_name'] }}</div>
                <div class="team-meta">{{ $team['company'] }} · สมาชิก {{ $members->count() }} คน</div>
              </div>
              <button type="button" class="team-cal-btn"
                      onclick="openTeamCalendar('{{ addslashes($team['team_name']) }}', '{{ addslashes($team['company']) }}')"
                      title="ดูเวลางาน">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>ดูเวลางาน</span>
                <span class="badge-count team-job-count" data-team="{{ $team['team_name'] }}">{{ $teamSchedules->count() }}</span>
              </button>
            </div>
            <div class="team-body">
              @if($head)
                <div class="member" data-tech="{{ json_encode($head, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openProfileFromEl(this)">
                  <div class="m-av">
                    <img src="{{ $head->img ? asset('storage/'.$head->img) : '' }}"
                         onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'64\' height=\'64\'%3E%3Crect width=\'64\' height=\'64\' fill=\'%23fed7aa\'/%3E%3Ctext x=\'50%25\' y=\'54%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'sans-serif\' font-weight=\'bold\' font-size=\'16\' fill=\'%23f97316\'%3E3E%3C/text%3E%3C/svg%3E'">
                  </div>
                  <div style="flex:1">
                    <div class="m-name">{{ $head->emp_name ?: $head->emp_id }} <span class="head-tag">หัวหน้า</span></div>
                    <div class="m-role">{{ $head->emp_id }} · {{ $head->emp_company }}</div>
                  </div>
                  <div class="status-dot st-{{ $head->status }}"></div>
                  <div style="display:flex;gap:6px;margin-left:10px" onclick="event.stopPropagation()">
                    <button type="button" class="btn btn-sm btn-ghost" onclick="openEditTechFromEl(this.closest('.member'))">แก้ไข</button>
                    <form method="POST" action="{{ route('tech.delete', $head->emp_id) }}" onsubmit="return confirm('ลบ {{ $head->emp_name ?: $head->emp_id }} ?')">
                      @csrf @method('POST')
                      <button type="submit" class="btn btn-sm btn-danger">ลบ</button>
                    </form>
                  </div>
                </div>
              @endif
              @foreach($others as $m)
                <div class="member" data-tech="{{ json_encode($m, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}" onclick="openProfileFromEl(this)">
                  <div class="m-av">
                    <img src="{{ $m->img ? asset('storage/'.$m->img) : '' }}"
                         onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'64\' height=\'64\'%3E%3Crect width=\'64\' height=\'64\' fill=\'%23fed7aa\'/%3E%3Ctext x=\'50%25\' y=\'54%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'sans-serif\' font-weight=\'bold\' font-size=\'16\' fill=\'%23f97316\'%3E3E%3C/text%3E%3C/svg%3E'">
                  </div>
                  <div style="flex:1">
                    <div class="m-name">{{ $m->emp_name ?: $m->emp_id }}</div>
                    <div class="m-role">{{ $m->emp_id }} · {{ $m->emp_company }}</div>
                  </div>
                  <div class="status-dot st-{{ $m->status }}"></div>
                  <div style="display:flex;gap:6px;margin-left:10px" onclick="event.stopPropagation()">
                    <button type="button" class="btn btn-sm btn-ghost" onclick="openEditTechFromEl(this.closest('.member'))">แก้ไข</button>
                    <form method="POST" action="{{ route('tech.delete', $m->emp_id) }}" onsubmit="return confirm('ลบ {{ $m->emp_name ?: $m->emp_id }} ?')">
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
                <td>
                  <strong style="font-size:14px;color:var(--dk)">{{ $s->customer_name }}</strong><br>
                  <small style="color:var(--md);font-size:13px">{{ $s->job_title }}</small>
                </td>
                <td style="font-size:14px">{{ $s->job_location }}</td>
                <td><strong style="color:var(--or2);font-size:14px">{{ $s->team_name }}</strong></td>
                <td style="font-size:14px;font-weight:600">{{ \Carbon\Carbon::parse($s->start_date)->format('d/m/Y') }}</td>
                <td style="font-size:14px;font-weight:600">{{ \Carbon\Carbon::parse($s->end_date)->format('d/m/Y') }}</td>
                <td><span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span></td>
                <td><small style="color:var(--md);font-size:13px">{{ $s->note }}</small></td>
                <td>
                  <div style="display:flex;gap:6px">
                    <button type="button" class="btn btn-sm btn-ghost"
                            data-sched="{{ json_encode($s, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP) }}"
                            onclick="openEditSchedFromEl(this)">แก้ไข</button>
                    <form method="POST" action="{{ route('sched.delete', $s->id) }}" onsubmit="return confirm('ลบงาน {{ $s->so_number }} ?')">
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

</div>{{-- /main --}}

{{-- ================================================================
     FULLSCREEN TEAM CALENDAR MODAL
================================================================ --}}
<div class="tcal-overlay" id="tcal-overlay">
  <div class="tcal-fullscreen">
    <div class="tcal-header">
      <div class="tcal-icon-box">
        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
      <div class="tcal-title-block">
        <div class="tcal-title-eyebrow">ตารางเวลางาน</div>
        <div class="tcal-title" id="tcal-team-name">—</div>
        <div class="tcal-sub">
          <span id="tcal-company">—</span>
          <span class="tcal-sub-stat" id="tcal-job-count">0 งาน</span>
        </div>
      </div>
      <button class="tcal-close" onclick="closeTeamCalendar()">✕</button>
    </div>
    <div class="tcal-body">
      <div class="tcal-content">
        <div class="cal-selector-bar">
          <button class="cal-sel-btn active" id="cal-start-btn">
            <div class="cal-sel-icon">
              <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
              <div class="cal-sel-lbl" id="cal-start-lbl">เลือกวันเริ่มต้น</div>
              <div class="cal-sel-sub" id="cal-start-sub">วันที่เริ่มงาน</div>
            </div>
          </button>
          <button class="cal-sel-btn" id="cal-end-btn">
            <div class="cal-sel-icon gray" id="cal-end-icon">
              <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
              <div class="cal-sel-lbl" id="cal-end-lbl">เลือกวันสิ้นสุด</div>
              <div class="cal-sel-sub" id="cal-end-sub">วันที่สิ้นสุดงาน</div>
            </div>
          </button>
        </div>

        <div class="cal-wrap">
          <div class="cal-months">
            <div class="cal-month-block">
              <div class="cal-mnav">
                <button class="cal-mnav-btn" id="cal-prev">&#8249;</button>
                <div class="cal-mname" id="cal-left-name"></div>
                <button class="cal-mnav-btn invisible">&#8250;</button>
              </div>
              <div class="cal-dhdrs" id="cal-left-hdrs"></div>
              <div class="cal-dgrid" id="cal-left-grid"></div>
            </div>
            <div class="cal-month-block">
              <div class="cal-mnav">
                <button class="cal-mnav-btn invisible">&#8249;</button>
                <div class="cal-mname" id="cal-right-name"></div>
                <button class="cal-mnav-btn" id="cal-next">&#8250;</button>
              </div>
              <div class="cal-dhdrs" id="cal-right-hdrs"></div>
              <div class="cal-dgrid" id="cal-right-grid"></div>
            </div>
          </div>

          <div class="cal-legend">
            <div class="cal-leg"><div class="cal-leg-dot" style="background:var(--or)"></div>วันที่เลือก / มีงาน</div>
            <div class="cal-leg"><div class="cal-leg-dot" style="background:#ffe1cc"></div>ช่วงที่เลือก</div>
            <div class="cal-leg"><div class="cal-leg-dot" style="background:#e2e2e2;border:1.5px solid var(--or)"></div>วันนี้</div>
          </div>
          <div class="cal-footer">
            <div class="cal-footer-note" id="cal-footer-note">กรุณาเลือกช่วงวันที่ต้องการดูงาน</div>
            <button class="btn btn-ghost" id="cal-reset-btn" onclick="calReset()" style="display:none">ล้างการเลือก</button>
            <button class="btn btn-primary" onclick="closeTeamCalendar();openAddSchedModal()">+ เพิ่มงาน</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Day Popup --}}
<div class="cal-popup-bg" id="cal-popup-bg" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="cal-popup">
    <div class="cal-popup-strip"></div>
    <div class="cal-popup-head">
      <div class="cal-popup-date" id="cal-popup-date"></div>
      <div class="cal-popup-count" id="cal-popup-count" style="display:none"></div>
      <button class="cal-popup-close" onclick="document.getElementById('cal-popup-bg').classList.remove('open')">✕</button>
    </div>
    <div class="cal-popup-range-bar" id="cal-popup-range-bar" style="display:none">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <span id="cal-popup-range-text"></span>
    </div>
    <div class="cal-popup-body">
      <div class="cal-popup-inner" id="cal-popup-inner"></div>
    </div>
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
        <div id="m-name-eng"></div>
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
      <div><div class="ilabel">สถานะ</div><div id="m-status"></div></div>
      <div class="profile-section"><div class="ilabel">ทักษะ</div><div class="sk-wrap" id="m-skills"></div></div>
      <div class="profile-section"><div class="ilabel">Core Competencies</div><div class="profile-comp-grid" id="m-competencies"></div></div>
      <div class="profile-section"><div class="ilabel">Licenses &amp; Experience</div><div id="m-licenses"></div></div>
      <div class="profile-section"><div class="ilabel">Software &amp; Tools</div><div class="sk-wrap" id="m-software"></div></div>
    </div>
  </div>
</div>

{{-- ================================================================
     MODAL: เพิ่มช่าง
================================================================ --}}
<div class="overlay" id="modal-tech" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-form-header">
      <div class="p-fullname">เพิ่มช่างใหม่</div>
      <div class="p-close" style="position:static;" onclick="closeModalById('modal-tech')">✕</div>
    </div>
    <div class="p-body" style="padding-top:16px;">
      @if($errors->any() && !old('_edit_tech') && !old('_edit_sched') && !old('so_number'))
        <div class="ferr">{{ $errors->first() }}</div>
      @endif
      <form method="POST" action="{{ route('tech.store') }}" enctype="multipart/form-data" id="form-add-tech">
        @csrf
        <div class="resume-top">
          <div class="resume-photo-col">
            <div style="position:relative;">
              <span class="resume-badge">PHOTO</span>
              <div class="resume-photo-box" onclick="document.getElementById('add-img-input').click()" title="คลิกเพื่ออัปโหลดรูป">
                <img id="add-img-preview" class="resume-img" src="" alt="">
                <div class="resume-photo-overlay"><span>เปลี่ยน<br>รูป</span></div>
                <div class="resume-photo-placeholder" id="add-img-placeholder">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  <span>อัปโหลด<br>รูปภาพ</span>
                </div>
              </div>
            </div>
            <div class="resume-photo-label">รูปประจำตัว</div>
            <div style="font-size:10px;color:var(--md);text-align:center;line-height:1.5;">JPG / PNG<br>แนะนำ 3:4</div>
            <input type="file" id="add-img-input" name="img" accept="image/*" onchange="resumePreview(this,'add')">
          </div>
          <div class="resume-fields">
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
                    <option value="{{ $co['code'] }}" {{ $oldCompany===$co['code']?'selected':'' }}>{{ $co['code'] }} – {{ $co['label'] }}</option>
                  @endforeach
                </select>
                <button type="button" class="btn-other" onclick="showCustomCompany('add')">+ อื่นๆ</button>
              </div>
              <div class="company-row" id="add-company-custom-row" style="{{ $isCustomComp ? '' : 'display:none' }}">
                <input class="finput" type="text" name="emp_company" id="add-company-custom" value="{{ $isCustomComp ? $oldCompany : '' }}" placeholder="พิมพ์ชื่อบริษัทใหม่" {{ $isCustomComp ? '' : 'disabled' }}>
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
        </div>
        <div style="padding:12px 24px 0;">
          <div class="frow" id="add-head-info" style="{{ old('emp_position')==='หัวหน้าทีม' ? '' : 'display:none' }}">
            <div class="head-info-box">ชื่อทีมจะถูกตั้งเป็น <strong>ชื่อพนักงาน</strong> โดยอัตโนมัติ (ต้องกรอกชื่อก่อน)</div>
          </div>
        </div>
        <div style="padding:0 24px 20px;">
          <div class="section-h">ทักษะ</div>
          @php $oldSkills = old('emp_skill', []); if (is_string($oldSkills)) $oldSkills = array_filter(array_map('trim', explode(',', $oldSkills))); $oldSkills = is_array($oldSkills) ? $oldSkills : []; @endphp
          <div class="skill-grid">
            @foreach($skillOptions as $sk)
              <label class="skill-check {{ in_array($sk, $oldSkills) ? 'checked' : '' }}">
                <input type="checkbox" name="emp_skill[]" value="{{ $sk }}" {{ in_array($sk, $oldSkills) ? 'checked' : '' }} onchange="this.closest('label').classList.toggle('checked',this.checked)">
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
                <div class="comp-head"><span class="comp-label">{{ $c['label'] }}</span><span class="comp-code">{{ $c['key'] }}</span></div>
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
          <button type="button" class="btn-add-lic" onclick="addLicense('add')">+ เพิ่มใบรับรอง / ประสบการณ์</button>
          <div class="section-h">Software &amp; Tools</div>
          @php $oldSw = old('software_tools', []); if(!is_array($oldSw)) $oldSw=[]; @endphp
          <div class="sw-grid">
            @foreach($softwareOptions as $sw)
              <label class="skill-check {{ in_array($sw, $oldSw) ? 'checked' : '' }}">
                <input type="checkbox" name="software_tools[]" value="{{ $sw }}" {{ in_array($sw, $oldSw) ? 'checked' : '' }} onchange="this.closest('label').classList.toggle('checked',this.checked)">
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
                <span class="sw-tag"><input type="hidden" name="software_tools[]" value="{{ $sw }}">{{ $sw }}<span class="x" onclick="this.parentElement.remove()">✕</span></span>
              @endif
            @endforeach
          </div>
          <div class="factions">
            <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-tech')">ยกเลิก</button>
            <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
          </div>
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
    <div class="modal-form-header">
      <div class="p-fullname">แก้ไขข้อมูลช่าง</div>
      <div class="p-close" style="position:static;" onclick="closeModalById('modal-edit-tech')">✕</div>
    </div>
    <div class="p-body" style="padding-top:16px;">
      @if($errors->any() && old('_edit_tech'))
        <div class="ferr">{{ $errors->first() }}</div>
      @endif
      <form method="POST" id="form-edit-tech" action="" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_edit_tech" value="1">
        <div class="resume-top">
          <div class="resume-photo-col">
            <div style="position:relative;">
              <span class="resume-badge">PHOTO</span>
              <div class="resume-photo-box" onclick="document.getElementById('et-img-input').click()" title="คลิกเพื่อเปลี่ยนรูป">
                <img id="et-img-preview" class="resume-img" src="" alt="">
                <div class="resume-photo-overlay"><span>เปลี่ยน<br>รูป</span></div>
                <div class="resume-photo-placeholder" id="et-img-placeholder">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  <span>คลิกเพื่อ<br>เปลี่ยนรูป</span>
                </div>
              </div>
            </div>
            <div class="resume-photo-label">รูปประจำตัว</div>
            <div style="font-size:10px;color:var(--md);text-align:center;line-height:1.5;">JPG / PNG<br>แนะนำ 3:4</div>
            <input type="file" id="et-img-input" name="img" accept="image/*" onchange="resumePreview(this,'et')">
          </div>
          <div class="resume-fields">
            <div class="frow"><div class="ilabel">รหัสพนักงาน</div><input class="finput" type="text" id="et-emp_id" readonly style="background:var(--lt);color:var(--md);cursor:not-allowed"></div>
            <div class="frow"><div class="ilabel">ชื่อ-นามสกุล (ไทย)</div><input class="finput" type="text" name="emp_name" id="et-emp_name"></div>
            <div class="frow"><div class="ilabel">ชื่อ-นามสกุล (Eng)</div><input class="finput" type="text" name="emp_name_eng" id="et-emp_name_eng"></div>
            <div class="frow"><div class="ilabel">เบอร์โทร</div><input class="finput" type="text" name="emp_phone" id="et-emp_phone"></div>
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
            <div class="frow" style="grid-column:1/-1;">
              <div class="ilabel">สถานะ</div>
              <select class="finput" name="status" id="et-status">
                <option value="active">พร้อมทำงาน (Active)</option>
                <option value="leave">ลาออก (ไม่แสดงในระบบ)</option>
              </select>
              <div class="emp-id-note" style="color:var(--rd)">เลือก "ลาออก" จะทำให้ไม่ปรากฏในระบบ</div>
            </div>
          </div>
        </div>
        <div style="padding:12px 24px 0;">
          <div class="frow" id="et-head-info" style="display:none">
            <div class="head-info-box">ชื่อทีมจะถูกตั้งเป็น <strong>ชื่อพนักงาน</strong> โดยอัตโนมัติ</div>
          </div>
        </div>
        <div style="padding:0 24px 20px;">
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
                <div class="comp-head"><span class="comp-label">{{ $c['label'] }}</span><span class="comp-code">{{ $c['key'] }}</span></div>
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
          <button type="button" class="btn-add-lic" onclick="addLicense('et')">+ เพิ่มใบรับรอง / ประสบการณ์</button>
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
            <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ================================================================
     MODAL: เพิ่มงาน
================================================================ --}}
<div class="overlay" id="modal-sched" onclick="if(event.target===this)closeModalById('modal-sched')">
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-form-header">
      <div class="p-fullname">เพิ่มงานใหม่</div>
      <div class="p-close" style="position:static;" onclick="closeModalById('modal-sched')">✕</div>
    </div>
    <div class="p-body" style="padding-top:16px;">
      @if($errors->any() && !old('_edit_sched') && old('so_number') && !old('emp_id'))
        <div class="ferr">{{ $errors->first() }}</div>
      @endif
      <form method="POST" action="{{ route('sched.store') }}" id="form-add-sched">
        @csrf
        <div class="sched-form-grid">
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
            <div class="ilabel">เลข SO *</div>
            <input class="finput" type="text" name="so_number" value="{{ old('so_number') }}" required placeholder="SO-XXXX">
          </div>
          <div class="frow">
            <div class="ilabel">ชื่อลูกค้า *</div>
            <input class="finput" type="text" name="customer_name" value="{{ old('customer_name') }}" required placeholder="ชื่อลูกค้า">
          </div>
          <div class="frow">
            <div class="ilabel">ชื่องาน *</div>
            <input class="finput" type="text" name="job_title" value="{{ old('job_title') }}" required placeholder="รายละเอียดงาน">
          </div>
          <div class="frow">
            <div class="ilabel">สถานที่</div>
            <input class="finput" type="text" name="job_location" value="{{ old('job_location') }}" placeholder="ที่อยู่ / จังหวัด">
          </div>
          <div class="frow">
            <div class="ilabel">สถานะ</div>
            <select class="finput" name="status">
              <option value="pending" {{ old('status','pending')==='pending'?'selected':'' }}>รอดำเนินการ</option>
              <option value="in_progress" {{ old('status')==='in_progress'?'selected':'' }}>กำลังทำ</option>
              <option value="done" {{ old('status')==='done'?'selected':'' }}>เสร็จสิ้น</option>
              <option value="cancelled" {{ old('status')==='cancelled'?'selected':'' }}>ยกเลิก</option>
            </select>
          </div>

          {{-- Date Range Picker - full width --}}
          <div class="frow sched-full">
            <div class="ilabel">ช่วงวันที่ทำงาน *</div>
            <div class="drp-trigger-row" id="add-drp-trigger">
              <div class="drp-trigger-cell active" id="add-drp-cell-start" onclick="DRP.focusCell('add','start',event)">
                <div class="drp-trigger-icon">
                  <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div class="drp-trigger-text">
                  <div class="drp-trigger-main empty" id="add-drp-start-main">เลือกวันเริ่ม</div>
                  <div class="drp-trigger-sub" id="add-drp-start-sub">วันเริ่มงาน</div>
                </div>
              </div>
              <div class="drp-trigger-cell" id="add-drp-cell-end" onclick="DRP.focusCell('add','end',event)">
                <div class="drp-trigger-icon gray" id="add-drp-end-icon">
                  <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div class="drp-trigger-text">
                  <div class="drp-trigger-main empty" id="add-drp-end-main">เลือกวันสิ้นสุด</div>
                  <div class="drp-trigger-sub" id="add-drp-end-sub">วันสิ้นสุดงาน</div>
                </div>
              </div>
              <div class="drp-popover" id="add-drp-popover" onclick="event.stopPropagation()">
                <div class="drp-months">
                  <div class="drp-month-block">
                    <div class="drp-mnav">
                      <button type="button" class="drp-mnav-btn" onclick="DRP.prev('add')">&#8249;</button>
                      <div class="drp-mname" id="add-drp-left-name"></div>
                      <button type="button" class="drp-mnav-btn invisible">&#8250;</button>
                    </div>
                    <div class="drp-dhdrs" id="add-drp-left-hdrs"></div>
                    <div class="drp-dgrid" id="add-drp-left-grid"></div>
                  </div>
                  <div class="drp-month-block">
                    <div class="drp-mnav">
                      <button type="button" class="drp-mnav-btn invisible">&#8249;</button>
                      <div class="drp-mname" id="add-drp-right-name"></div>
                      <button type="button" class="drp-mnav-btn" onclick="DRP.next('add')">&#8250;</button>
                    </div>
                    <div class="drp-dhdrs" id="add-drp-right-hdrs"></div>
                    <div class="drp-dgrid" id="add-drp-right-grid"></div>
                  </div>
                </div>
                <div class="drp-footer">
                  <div class="drp-footer-info" id="add-drp-info">เลือกวันเริ่มต้น</div>
                  <button type="button" class="drp-clear-btn" onclick="DRP.clear('add')">ล้าง</button>
                  <button type="button" class="drp-done-btn" onclick="DRP.close('add')">เสร็จสิ้น ✓</button>
                </div>
              </div>
            </div>
            <input type="hidden" name="start_date" id="add-start_date" value="{{ old('start_date') }}" required>
            <input type="hidden" name="end_date" id="add-end_date" value="{{ old('end_date') }}" required>
          </div>

          <div class="frow sched-full">
            <div class="ilabel">หมายเหตุ</div>
            <textarea class="finput" name="note" rows="3" style="resize:vertical">{{ old('note') }}</textarea>
          </div>
        </div>
        <div class="factions">
          <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-sched')">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึกงาน</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ================================================================
     MODAL: แก้ไขงาน
================================================================ --}}
<div class="overlay" id="modal-edit-sched" onclick="if(event.target===this)closeModalById('modal-edit-sched')">
  <div class="pmodal" onclick="event.stopPropagation()">
    <div class="pmodal-strip"></div>
    <div class="modal-form-header">
      <div class="p-fullname">แก้ไขข้อมูลงาน</div>
      <div class="p-close" style="position:static;" onclick="closeModalById('modal-edit-sched')">✕</div>
    </div>
    <div class="p-body" style="padding-top:16px;">
      @if($errors->any() && old('_edit_sched'))
        <div class="ferr">{{ $errors->first() }}</div>
      @endif
      <form method="POST" id="form-edit-sched" action="">
        @csrf
        <input type="hidden" name="_edit_sched" value="1">
        <div class="sched-form-grid">
          <div class="frow">
            <div class="ilabel">เลข SO *</div>
            <input class="finput" type="text" name="so_number" id="es-so_number" required>
          </div>
          <div class="frow">
            <div class="ilabel">ชื่อลูกค้า *</div>
            <input class="finput" type="text" name="customer_name" id="es-customer_name" required>
          </div>
          <div class="frow">
            <div class="ilabel">ชื่องาน *</div>
            <input class="finput" type="text" name="job_title" id="es-job_title" required>
          </div>
          <div class="frow">
            <div class="ilabel">สถานที่</div>
            <input class="finput" type="text" name="job_location" id="es-job_location">
          </div>
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

          {{-- Date Range Picker - full width --}}
          <div class="frow sched-full">
            <div class="ilabel">ช่วงวันที่ทำงาน *</div>
            <div class="drp-trigger-row" id="es-drp-trigger">
              <div class="drp-trigger-cell active" id="es-drp-cell-start" onclick="DRP.focusCell('es','start',event)">
                <div class="drp-trigger-icon">
                  <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div class="drp-trigger-text">
                  <div class="drp-trigger-main empty" id="es-drp-start-main">เลือกวันเริ่ม</div>
                  <div class="drp-trigger-sub" id="es-drp-start-sub">วันเริ่มงาน</div>
                </div>
              </div>
              <div class="drp-trigger-cell" id="es-drp-cell-end" onclick="DRP.focusCell('es','end',event)">
                <div class="drp-trigger-icon gray" id="es-drp-end-icon">
                  <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div class="drp-trigger-text">
                  <div class="drp-trigger-main empty" id="es-drp-end-main">เลือกวันสิ้นสุด</div>
                  <div class="drp-trigger-sub" id="es-drp-end-sub">วันสิ้นสุดงาน</div>
                </div>
              </div>
              <div class="drp-popover" id="es-drp-popover" onclick="event.stopPropagation()">
                <div class="drp-months">
                  <div class="drp-month-block">
                    <div class="drp-mnav">
                      <button type="button" class="drp-mnav-btn" onclick="DRP.prev('es')">&#8249;</button>
                      <div class="drp-mname" id="es-drp-left-name"></div>
                      <button type="button" class="drp-mnav-btn invisible">&#8250;</button>
                    </div>
                    <div class="drp-dhdrs" id="es-drp-left-hdrs"></div>
                    <div class="drp-dgrid" id="es-drp-left-grid"></div>
                  </div>
                  <div class="drp-month-block">
                    <div class="drp-mnav">
                      <button type="button" class="drp-mnav-btn invisible">&#8249;</button>
                      <div class="drp-mname" id="es-drp-right-name"></div>
                      <button type="button" class="drp-mnav-btn" onclick="DRP.next('es')">&#8250;</button>
                    </div>
                    <div class="drp-dhdrs" id="es-drp-right-hdrs"></div>
                    <div class="drp-dgrid" id="es-drp-right-grid"></div>
                  </div>
                </div>
                <div class="drp-footer">
                  <div class="drp-footer-info" id="es-drp-info">เลือกวันเริ่มต้น</div>
                  <button type="button" class="drp-clear-btn" onclick="DRP.clear('es')">ล้าง</button>
                  <button type="button" class="drp-done-btn" onclick="DRP.close('es')">เสร็จสิ้น ✓</button>
                </div>
              </div>
            </div>
            <input type="hidden" name="start_date" id="es-start_date" required>
            <input type="hidden" name="end_date" id="es-end_date" required>
          </div>

          <div class="frow sched-full">
            <div class="ilabel">หมายเหตุ</div>
            <textarea class="finput" name="note" id="es-note" rows="3" style="resize:vertical"></textarea>
          </div>
        </div>
        <div class="factions">
          <button type="button" class="btn btn-ghost" onclick="closeModalById('modal-edit-sched')">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const KNOWN_COMPANIES  = @json(collect($companyList)->pluck('code')->values());
const KNOWN_SOFTWARE   = @json(collect($softwareOptions)->values());
const COMPETENCY_LIST  = @json($competencyList);
const COMPETENCY_LEVELS= @json($competencyLevels);
const STORAGE_URL      = '{{ asset("storage") }}/';

let ALL_SCHEDULES = @json($schedules);

/* ===== FLASH AUTO-HIDE 5s ===== */
(function(){
  ['flash-msg','flash-msg-err'].forEach(id=>{
    const el = document.getElementById(id);
    if(!el) return;
    setTimeout(()=>{ if(el && el.parentNode) el.parentNode.removeChild(el); }, 5000);
  });
})();

/* ===== TABS ===== */
function switchTab(name, btn){
  document.querySelectorAll('.panel').forEach(p=>p.classList.remove('active'));
  document.getElementById('panel-'+name).classList.add('active');
  document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));
  btn.classList.add('active');
}

/* ===== MODALS ===== */
function openModal(id){ document.getElementById(id).classList.add('open'); }
function closeModalById(id){
  document.getElementById(id).classList.remove('open');
  document.querySelectorAll('.drp-popover.open').forEach(p=>p.classList.remove('open'));
  document.querySelectorAll('.drp-trigger-row.open').forEach(p=>p.classList.remove('open'));
}
function closeModal(e){ if(!e||e.target===document.getElementById('overlay')) document.getElementById('overlay').classList.remove('open'); }

/* Open add schedule modal — always reset DRP first */
function openAddSchedModal(){
  DRP.reset('add');
  DRP.setValues('add', '{{ old("start_date") }}', '{{ old("end_date") }}');
  openModal('modal-sched');
}

function openProfileFromEl(el){ try{ openProfile(JSON.parse(el.dataset.tech)); }catch(err){ alert('เกิดข้อผิดพลาด'); } }
function openEditTechFromEl(el){ try{ openEditTech(JSON.parse(el.dataset.tech)); }catch(err){ alert('เกิดข้อผิดพลาด'); } }
function openEditSchedFromEl(el){ try{ openEditSched(JSON.parse(el.dataset.sched)); }catch(err){ alert('เกิดข้อผิดพลาด'); } }

/* ===== PHOTO PREVIEW ===== */
function resumePreview(input, prefix){
  const file=input.files[0]; if(!file) return;
  const reader=new FileReader();
  reader.onload=e=>{
    const img=document.getElementById(prefix+'-img-preview');
    const ph=document.getElementById(prefix+'-img-placeholder');
    img.src=e.target.result; img.style.display='block';
    if(ph) ph.style.display='none';
  };
  reader.readAsDataURL(file);
}

/* ===== COMPANY TOGGLE ===== */
function showCustomCompany(p){ document.getElementById(p+'-company-select-row').style.display='none'; document.getElementById(p+'-company-custom-row').style.display=''; document.getElementById(p+'-emp_company').disabled=true; const c=document.getElementById(p+'-company-custom'); c.disabled=false; c.focus(); }
function hideCustomCompany(p){ document.getElementById(p+'-company-select-row').style.display=''; document.getElementById(p+'-company-custom-row').style.display='none'; document.getElementById(p+'-emp_company').disabled=false; const c=document.getElementById(p+'-company-custom'); c.disabled=true; c.value=''; }

/* ===== POSITION CHANGE ===== */
function handlePositionChange(p){
  const posEl=document.getElementById(p+'-emp_position'); if(!posEl) return;
  const isHead=posEl.value==='หัวหน้าทีม';
  const tw=document.getElementById(p+'-team-wrap'), hi=document.getElementById(p+'-head-info'), ts=document.getElementById(p+'-team-select');
  if(isHead){ if(tw) tw.style.display='none'; if(hi) hi.style.display=''; if(ts) ts.disabled=true; }
  else { if(tw) tw.style.display=''; if(hi) hi.style.display='none'; if(ts) ts.disabled=false; }
}

function attachHeadAutoFill(formId){
  const form=document.getElementById(formId); if(!form) return;
  form.addEventListener('submit',function(e){
    const posEl=form.querySelector('select[name="emp_position"]');
    if(!posEl||posEl.value!=='หัวหน้าทีม') return;
    const nameEl=form.querySelector('input[name="emp_name"]');
    const teamName=(nameEl?.value||'').trim();
    if(!teamName){ e.preventDefault(); alert('กรุณากรอกชื่อ-นามสกุลก่อน'); nameEl?.focus(); return; }
    form.querySelectorAll('input[data-auto-head]').forEach(el=>el.remove());
    const h=document.createElement('input'); h.type='hidden'; h.name='emp_team'; h.value=teamName; h.setAttribute('data-auto-head','1');
    form.appendChild(h);
  });
}
attachHeadAutoFill('form-add-tech');
attachHeadAutoFill('form-edit-tech');

function updateCompClass(sel){ sel.classList.remove('lv-none','lv-basic','lv-skill','lv-expert'); sel.classList.add('lv-'+sel.value); }

function updateBE(p){
  const input=document.getElementById(p+'-dob'), label=document.getElementById(p+'-dob-be'); if(!input||!label) return;
  if(input.value){ const pts=input.value.split('-'); if(pts.length===3){ label.textContent=`พ.ศ. ${pts[2]}/${pts[1]}/${parseInt(pts[0],10)+543}`; return; } }
  label.textContent='พ.ศ. -';
}

let licCounter={add:0,et:0};
function addLicense(prefix,data){
  data=data||{}; const idx=licCounter[prefix]++;
  const list=document.getElementById(prefix+'-lic-list');
  const item=document.createElement('div'); item.className='lic-item'; item.dataset.idx=idx;
  const fileBlock=data.file
    ?`<div class="lic-file-row"><a href="${STORAGE_URL}${data.file}" target="_blank" class="lic-file-current">ดูไฟล์เดิม</a><input type="hidden" name="licenses[${idx}][existing_file]" value="${escapeAttr(data.file)}"><input type="file" class="lic-file-input" name="licenses[${idx}][file_upload]" accept=".jpg,.jpeg,.png,.webp,.pdf"></div>`
    :`<div class="lic-file-row"><input type="file" class="lic-file-input" name="licenses[${idx}][file_upload]" accept=".jpg,.jpeg,.png,.webp,.pdf"></div>`;
  item.innerHTML=`<div class="lic-item-head"><span class="lic-num">#${idx+1}</span><button type="button" class="lic-del" onclick="this.closest('.lic-item').remove()">ลบ</button></div>
    <div class="lic-grid">
      <div><div class="ilabel">Title / ชื่อใบรับรอง</div><input class="finput" type="text" name="licenses[${idx}][title]" value="${escapeAttr(data.title||'')}" placeholder="เช่น ใบรับรองช่างไฟฟ้า ระดับ 1"></div>
      <div><div class="ilabel">เลขเอกสาร</div><input class="finput" type="text" name="licenses[${idx}][doc_no]" value="${escapeAttr(data.doc_no||'')}" placeholder="เช่น EL-2567-001"></div>
      <div><div class="ilabel">วันที่ได้รับ</div><input class="finput" type="text" name="licenses[${idx}][date_issued]" value="${escapeAttr(data.date_issued||'')}" placeholder="DD/MM/YYYY (พ.ศ.)"></div>
      <div><div class="ilabel">ไฟล์แนบ (รูป / PDF)</div>${fileBlock}</div>
    </div>`;
  list.appendChild(item);
}

function escapeAttr(s){ return String(s).replace(/"/g,'&quot;').replace(/</g,'&lt;'); }

function addCustomSw(p){
  const input=document.getElementById(p+'-sw-custom'), val=input.value.trim(); if(!val) return;
  const modalId=p==='add'?'modal-tech':'modal-edit-tech';
  for(const lb of document.querySelectorAll(`#${modalId} .sw-grid .skill-check`)){
    const cb=lb.querySelector('input[type=checkbox]');
    if(cb&&cb.value===val){ if(!cb.checked){cb.checked=true;lb.classList.add('checked');} input.value=''; return; }
  }
  const tags=document.getElementById(p+'-sw-custom-tags');
  for(const t of tags.querySelectorAll('input[type=hidden]')){ if(t.value===val){input.value='';return;} }
  const tag=document.createElement('span'); tag.className='sw-tag';
  tag.innerHTML=`<input type="hidden" name="software_tools[]" value="${escapeAttr(val)}">${escapeAttr(val)}<span class="x" onclick="this.parentElement.remove()">✕</span>`;
  tags.appendChild(tag); input.value='';
}

function parseJSON(v){ if(!v) return null; if(typeof v==='object') return v; try{ return JSON.parse(v); }catch(e){ return null; } }

function ceToBeStr(ds){
  if(!ds) return '-'; const d=String(ds).substring(0,10); const p=d.split('-'); if(p.length!==3) return '-';
  return `${p[2]}/${p[1]}/${parseInt(p[0],10)+543}`;
}

function openProfile(m){
  const FALLBACK="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Crect width='160' height='160' fill='%23ffedd5'/%3E%3Ctext x='50%25' y='54%25' dominant-baseline='middle' text-anchor='middle' font-family='sans-serif' font-weight='bold' font-size='40' fill='%23f97316'%3E3E%3C/text%3E%3C/svg%3E";
  const imgEl=document.getElementById('m-img');
  imgEl.src=m.img?STORAGE_URL+m.img:FALLBACK;
  imgEl.onerror=function(){ this.onerror=null; this.src=FALLBACK; };
  document.getElementById('m-company').textContent=m.emp_company||'';
  document.getElementById('m-name').textContent=m.emp_name||m.emp_id||'-';
  document.getElementById('m-name-eng').textContent=m.emp_name_eng||'';
  document.getElementById('m-position').textContent=m.emp_position||'-';
  document.getElementById('m-empid').textContent=m.emp_id||'-';
  document.getElementById('m-team').textContent=m.emp_team||'-';
  document.getElementById('m-phone').textContent=m.emp_phone||'-';
  const statusEl=document.getElementById('m-status');
  statusEl.textContent={active:'พร้อมทำงาน',leave:'ลาออก'}[m.status]||m.status||'-';
  statusEl.style.color=m.status==='active'?'#16a34a':'#dc2626'; statusEl.style.fontWeight='700'; statusEl.style.fontSize='15px';
  document.getElementById('m-dob').textContent=ceToBeStr(m.date_of_birth);
  const skills=(m.emp_skill||'').split(',').map(s=>s.trim()).filter(Boolean);
  document.getElementById('m-skills').innerHTML=skills.length?skills.map(s=>`<span class="sk">${escapeAttr(s)}</span>`).join(''):'<span style="color:var(--md);font-size:13px">-</span>';
  const comps=parseJSON(m.core_competencies)||{};
  document.getElementById('m-competencies').innerHTML=COMPETENCY_LIST.map(c=>{
    const lv=comps[c.key]||'none'; const lvLabel=COMPETENCY_LEVELS[lv]||'ไม่มี';
    return `<div class="pc-card lv-${lv}"><span class="pc-lbl">${c.label}</span><span class="pc-val">${lvLabel}</span></div>`;
  }).join('');
  const licEl=document.getElementById('m-licenses');
  const licenses=parseJSON(m.licenses)||[];
  licEl.innerHTML=licenses.length?licenses.map(l=>{
    const fileLink=l.file?`<a href="${STORAGE_URL}${l.file}" target="_blank">ดูไฟล์</a>`:'';
    const parts=[]; if(l.doc_no) parts.push(`เลขที่: ${escapeAttr(l.doc_no)}`); if(l.date_issued) parts.push(`วันที่: ${escapeAttr(l.date_issued)}`); if(fileLink) parts.push(fileLink);
    return `<div class="profile-lic-item"><div class="profile-lic-title">${escapeAttr(l.title||'(ไม่มีชื่อ)')}</div><div class="profile-lic-meta">${parts.join(' · ')||'-'}</div></div>`;
  }).join(''):'<span style="color:var(--md);font-size:13px">ไม่มีข้อมูล</span>';
  const sw=parseJSON(m.software_tools)||[];
  document.getElementById('m-software').innerHTML=sw.length?sw.map(s=>`<span class="sk">${escapeAttr(s)}</span>`).join(''):'<span style="color:var(--md);font-size:13px">-</span>';
  openModal('overlay');
}

function openEditTech(m){
  document.getElementById('form-edit-tech').action=`/technicians/${m.emp_id}/update`;
  document.getElementById('et-emp_id').value=m.emp_id||'';
  document.getElementById('et-emp_name').value=m.emp_name||'';
  document.getElementById('et-emp_name_eng').value=m.emp_name_eng||'';
  document.getElementById('et-emp_phone').value=m.emp_phone||'';
  const dob=m.date_of_birth?String(m.date_of_birth).substring(0,10):'';
  document.getElementById('et-dob').value=dob; updateBE('et');
  document.getElementById('et-emp_position').value=m.emp_position||'';
  document.getElementById('et-team-select').value=m.emp_team||'';
  document.getElementById('et-status').value=m.status||'active';
  const company=m.emp_company||'';
  if(company&&!KNOWN_COMPANIES.includes(company)){ showCustomCompany('et'); document.getElementById('et-company-custom').value=company; }
  else { hideCustomCompany('et'); document.getElementById('et-emp_company').value=company; }
  const preview=document.getElementById('et-img-preview'), ph=document.getElementById('et-img-placeholder');
  if(m.img){ preview.src=STORAGE_URL+m.img; preview.style.display='block'; if(ph) ph.style.display='none'; }
  else { preview.src=''; preview.style.display='none'; if(ph) ph.style.display='flex'; }
  document.getElementById('et-img-input').value='';
  const currentSkills=(m.emp_skill||'').split(',').map(s=>s.trim()).filter(Boolean);
  document.querySelectorAll('#et-skill-grid .skill-check').forEach(label=>{ const cb=label.querySelector('input[type=checkbox]'); const checked=currentSkills.includes(label.dataset.skill); cb.checked=checked; label.classList.toggle('checked',checked); });
  const comps=parseJSON(m.core_competencies)||{};
  document.querySelectorAll('#et-comp-grid .comp-select').forEach(sel=>{ const key=sel.dataset.comp; sel.value=comps[key]||'none'; updateCompClass(sel); });
  licCounter.et=0; document.getElementById('et-lic-list').innerHTML='';
  (parseJSON(m.licenses)||[]).forEach(l=>addLicense('et',l));
  const sw=parseJSON(m.software_tools)||[];
  document.querySelectorAll('#et-sw-grid .skill-check').forEach(label=>{ const cb=label.querySelector('input[type=checkbox]'); const checked=sw.includes(label.dataset.sw); cb.checked=checked; label.classList.toggle('checked',checked); });
  const etTags=document.getElementById('et-sw-custom-tags'); etTags.innerHTML='';
  sw.forEach(s=>{ if(!KNOWN_SOFTWARE.includes(s)){ const tag=document.createElement('span'); tag.className='sw-tag'; tag.innerHTML=`<input type="hidden" name="software_tools[]" value="${escapeAttr(s)}">${escapeAttr(s)}<span class="x" onclick="this.parentElement.remove()">✕</span>`; etTags.appendChild(tag); } });
  handlePositionChange('et'); openModal('modal-edit-tech');
}

function openEditSched(s){
  window._currentEditingSched = s;
  document.getElementById('form-edit-sched').action=`/schedules/${s.id}/update`;
  document.getElementById('es-so_number').value=s.so_number||'';
  document.getElementById('es-customer_name').value=s.customer_name||'';
  document.getElementById('es-job_title').value=s.job_title||'';
  document.getElementById('es-job_location').value=s.job_location||'';
  document.getElementById('es-team_name').value=s.team_name||'';
  const sd = s.start_date ? String(s.start_date).substring(0,10) : '';
  const ed = s.end_date ? String(s.end_date).substring(0,10) : '';
  document.getElementById('es-status').value=s.status||'pending';
  document.getElementById('es-note').value=s.note||'';
  DRP.setValues('es', sd, ed);
  openModal('modal-edit-sched');
}

/* Sync ALL_SCHEDULES on edit */
function updateScheduleInMemory(updatedSched){
  const idx = ALL_SCHEDULES.findIndex(s => s.id == updatedSched.id);
  if(idx >= 0) ALL_SCHEDULES[idx] = {...ALL_SCHEDULES[idx], ...updatedSched};
  refreshTeamJobCounts();
}
function refreshTeamJobCounts(){
  document.querySelectorAll('.team-job-count').forEach(badge=>{
    const teamName = badge.dataset.team;
    badge.textContent = ALL_SCHEDULES.filter(s=>s.team_name===teamName).length;
  });
}

(function(){
  const form = document.getElementById('form-edit-sched');
  if(!form) return;
  form.addEventListener('submit', function(){
    const cur = window._currentEditingSched;
    if(!cur) return;
    updateScheduleInMemory({
      ...cur,
      so_number: document.getElementById('es-so_number').value,
      customer_name: document.getElementById('es-customer_name').value,
      job_title: document.getElementById('es-job_title').value,
      job_location: document.getElementById('es-job_location').value,
      team_name: document.getElementById('es-team_name').value,
      start_date: document.getElementById('es-start_date').value,
      end_date: document.getElementById('es-end_date').value,
      status: document.getElementById('es-status').value,
      note: document.getElementById('es-note').value,
    });
  });
})();

updateBE('add');

/* Auto-open modal on validation errors */
@if($errors->any() && !old('_edit_tech') && !old('_edit_sched') && old('emp_id'))
  openModal('modal-tech'); handlePositionChange('add');
@elseif($errors->any() && old('_edit_tech'))
  openModal('modal-edit-tech'); handlePositionChange('et');
@endif
@if($errors->any() && !old('_edit_sched') && old('so_number') && !old('emp_id'))
  openAddSchedModal();
@elseif($errors->any() && old('_edit_sched'))
  openModal('modal-edit-sched');
@endif

/* =============================================
   DATE RANGE PICKER
============================================= */
const DRP = (function(){
  const MONTHS_TH = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน',
                     'กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
  const DAYS_HDR  = ['จ.','อ.','พ.','พฤ.','ศ.','ส.','อา.'];
  const MONTHS_SHORT=['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
  const DAYS_FULL = ['อา.','จ.','อ.','พ.','พฤ.','ศ.','ส.'];

  const state = {};

  function pad(n){ return String(n).padStart(2,'0'); }
  function iso(y,m,d){ return `${y}-${pad(m+1)}-${pad(d)}`; }
  function todayIso(){ const t=new Date(); return iso(t.getFullYear(),t.getMonth(),t.getDate()); }
  function parseIso(s){ const p=String(s).substring(0,10).split('-'); return new Date(+p[0],+p[1]-1,+p[2]); }

  function fmtBE(ds){
    if(!ds) return '';
    const p=ds.split('-');
    const d=parseInt(p[2]), m=parseInt(p[1])-1, y=parseInt(p[0])+543;
    return `${d} ${MONTHS_SHORT[m]} ${y}`;
  }
  function fmtDayName(ds){
    if(!ds) return '';
    return DAYS_FULL[parseIso(ds).getDay()];
  }

  function ensureState(prefix){
    if(state[prefix]) return state[prefix];
    const today = new Date();
    state[prefix] = { year: today.getFullYear(), month: today.getMonth(), start: null, end: null, hover: null, focus: 'start' };
    return state[prefix];
  }

  function buildMonth(prefix, year, month, gridId, hdrId, nameId){
    const st = ensureState(prefix);
    const nameEl = document.getElementById(nameId);
    if(!nameEl) return;
    nameEl.textContent = `${MONTHS_TH[month]} ${year+543}`;

    const hdrEl = document.getElementById(hdrId);
    hdrEl.innerHTML = '';
    DAYS_HDR.forEach(d=>{ const el=document.createElement('div'); el.className='drp-dhdr'; el.textContent=d; hdrEl.appendChild(el); });

    const grid = document.getElementById(gridId); grid.innerHTML='';
    const tod  = todayIso();
    let firstDow = new Date(year, month, 1).getDay() - 1;
    if(firstDow < 0) firstDow = 6;
    const dim      = new Date(year, month+1, 0).getDate();
    const prevDim  = new Date(year, month, 0).getDate();
    const eff_end  = st.end || st.hover;

    for(let i=0; i<firstDow; i++){
      const el=document.createElement('div'); el.className='drp-day drp-other';
      el.innerHTML=`<div class="drp-di">${prevDim-firstDow+1+i}</div>`; grid.appendChild(el);
    }
    for(let d=1; d<=dim; d++){
      const ds = iso(year, month, d);
      let cls = 'drp-day';
      if(ds === tod)      cls += ' drp-today';
      if(ds === st.start) cls += ' drp-sel-s';
      if(ds === st.end)   cls += ' drp-sel-e';
      if(st.start && eff_end && st.start !== eff_end){
        const lo = st.start < eff_end ? st.start : eff_end;
        const hi = st.start < eff_end ? eff_end  : st.start;
        if(ds > lo && ds < hi) cls += ' drp-in-range';
      }
      const el = document.createElement('div'); el.className = cls;
      el.innerHTML = `<div class="drp-di">${d}</div>`;
      el.addEventListener('click', (ev) => { ev.stopPropagation(); handleDayClick(prefix, ds); });
      el.addEventListener('mouseenter', () => { st.hover = ds; renderAll(prefix); });
      el.addEventListener('mouseleave', () => { st.hover = null; renderAll(prefix); });
      grid.appendChild(el);
    }
    const total = firstDow + dim;
    const trail = (7 - total % 7) % 7;
    for(let i=1; i<=trail; i++){
      const el=document.createElement('div'); el.className='drp-day drp-other';
      el.innerHTML=`<div class="drp-di">${i}</div>`; grid.appendChild(el);
    }
  }

  function renderAll(prefix){
    const st = ensureState(prefix);
    const ry = st.month===11 ? st.year+1 : st.year;
    const rm = st.month===11 ? 0 : st.month+1;
    buildMonth(prefix, st.year, st.month, prefix+'-drp-left-grid',  prefix+'-drp-left-hdrs',  prefix+'-drp-left-name');
    buildMonth(prefix, ry,      rm,       prefix+'-drp-right-grid', prefix+'-drp-right-hdrs', prefix+'-drp-right-name');
    updateTriggers(prefix);
    updateInfo(prefix);
  }

  function handleDayClick(prefix, ds){
    const st = ensureState(prefix);
    if(st.focus === 'start' || !st.start || (st.start && st.end)){
      st.start = ds; st.end = null; st.focus = 'end';
    } else {
      if(ds < st.start){ st.end = st.start; st.start = ds; }
      else if(ds === st.start){ st.end = ds; }
      else { st.end = ds; }
      st.focus = 'start';
    }
    document.getElementById(prefix+'-start_date').value = st.start || '';
    document.getElementById(prefix+'-end_date').value   = st.end   || '';
    renderAll(prefix);
  }

  function updateTriggers(prefix){
    const st = ensureState(prefix);
    const sm = document.getElementById(prefix+'-drp-start-main');
    const ss = document.getElementById(prefix+'-drp-start-sub');
    const em = document.getElementById(prefix+'-drp-end-main');
    const es = document.getElementById(prefix+'-drp-end-sub');
    const ei = document.getElementById(prefix+'-drp-end-icon');
    const cellStart = document.getElementById(prefix+'-drp-cell-start');
    const cellEnd   = document.getElementById(prefix+'-drp-cell-end');
    if(!sm) return;

    if(st.start){ sm.textContent = fmtBE(st.start); sm.classList.remove('empty'); ss.textContent = fmtDayName(st.start); }
    else { sm.textContent = 'เลือกวันเริ่ม'; sm.classList.add('empty'); ss.textContent = 'วันเริ่มงาน'; }
    if(st.end){ em.textContent = fmtBE(st.end); em.classList.remove('empty'); es.textContent = fmtDayName(st.end); if(ei) ei.classList.remove('gray'); }
    else { em.textContent = 'เลือกวันสิ้นสุด'; em.classList.add('empty'); es.textContent = 'วันสิ้นสุดงาน'; if(ei) ei.classList.add('gray'); }
    if(cellStart) cellStart.classList.toggle('active', st.focus === 'start');
    if(cellEnd)   cellEnd.classList.toggle('active',   st.focus === 'end');
  }

  function updateInfo(prefix){
    const st = ensureState(prefix);
    const info = document.getElementById(prefix+'-drp-info');
    if(!info) return;
    if(st.start && st.end){
      const days = Math.round((parseIso(st.end)-parseIso(st.start))/864e5)+1;
      info.innerHTML = `เลือกแล้ว <strong>${days}</strong> วัน`;
    } else if(st.start){ info.textContent = 'เลือกวันสิ้นสุด'; }
    else { info.textContent = 'เลือกวันเริ่มต้น'; }
  }

  return {
    open: function(prefix, ev){
      if(ev) ev.stopPropagation();
      ensureState(prefix);
      const trigger = document.getElementById(prefix+'-drp-trigger');
      const popover = document.getElementById(prefix+'-drp-popover');
      if(!trigger || !popover) return;
      const isOpen = popover.classList.contains('open');
      document.querySelectorAll('.drp-popover.open').forEach(p=>{ if(p!==popover){ p.classList.remove('open'); } });
      document.querySelectorAll('.drp-trigger-row.open').forEach(p=>{ if(p!==trigger){ p.classList.remove('open'); } });
      if(!isOpen){ popover.classList.add('open'); trigger.classList.add('open'); renderAll(prefix); }
    },
    close: function(prefix){
      const pop = document.getElementById(prefix+'-drp-popover');
      const tr  = document.getElementById(prefix+'-drp-trigger');
      if(pop) pop.classList.remove('open');
      if(tr)  tr.classList.remove('open');
    },
    focusCell: function(prefix, which, ev){
      if(ev) ev.stopPropagation();
      const st = ensureState(prefix);
      st.focus = which;
      const trigger = document.getElementById(prefix+'-drp-trigger');
      const popover = document.getElementById(prefix+'-drp-popover');
      if(!trigger || !popover) return;
      if(!popover.classList.contains('open')){
        document.querySelectorAll('.drp-popover.open').forEach(p=>{ if(p!==popover) p.classList.remove('open'); });
        document.querySelectorAll('.drp-trigger-row.open').forEach(p=>{ if(p!==trigger) p.classList.remove('open'); });
        popover.classList.add('open');
        trigger.classList.add('open');
      }
      renderAll(prefix);
    },
    prev: function(prefix){
      const st = ensureState(prefix);
      st.month--; if(st.month<0){ st.month=11; st.year--; }
      renderAll(prefix);
    },
    next: function(prefix){
      const st = ensureState(prefix);
      st.month++; if(st.month>11){ st.month=0; st.year++; }
      renderAll(prefix);
    },
    clear: function(prefix){
      const st = ensureState(prefix);
      st.start = null; st.end = null; st.focus='start';
      const si = document.getElementById(prefix+'-start_date');
      const ei = document.getElementById(prefix+'-end_date');
      if(si) si.value = '';
      if(ei) ei.value = '';
      renderAll(prefix);
    },
    reset: function(prefix){
      /* Fully reset state (for re-opening modal fresh) */
      if(state[prefix]){
        const today = new Date();
        state[prefix] = { year: today.getFullYear(), month: today.getMonth(), start: null, end: null, hover: null, focus: 'start' };
      }
    },
    setValues: function(prefix, sd, ed){
      const st = ensureState(prefix);
      st.start = sd || null;
      st.end = ed || null;
      st.focus = st.start && !st.end ? 'end' : 'start';
      if(st.start){ const d = parseIso(st.start); st.year = d.getFullYear(); st.month = d.getMonth(); }
      else { const t = new Date(); st.year = t.getFullYear(); st.month = t.getMonth(); }
      const siEl = document.getElementById(prefix+'-start_date');
      const eiEl = document.getElementById(prefix+'-end_date');
      if(siEl) siEl.value = st.start || '';
      if(eiEl) eiEl.value = st.end   || '';
      updateTriggers(prefix);
      updateInfo(prefix);
    }
  };
})();

/* Initialize DRP triggers for both modals */
DRP.setValues('add', '{{ old("start_date") }}', '{{ old("end_date") }}');

/* Click outside DRP closes it */
document.addEventListener('click', function(e){
  document.querySelectorAll('.drp-trigger-row.open').forEach(trigger=>{
    if(!trigger.contains(e.target)){
      trigger.classList.remove('open');
      const pop = trigger.querySelector('.drp-popover');
      if(pop) pop.classList.remove('open');
    }
  });
});

/* =============================================
   TEAM CALENDAR — FULLSCREEN
============================================= */
const TeamCal = (function(){
  const MONTHS_TH = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน',
                     'กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
  const DAYS_TH   = ['อา.','จ.','อ.','พ.','พฤ.','ศ.','ส.'];
  const MONTHS_SHORT=['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
  const STATUS_MAP = {
    in_progress : {label:'กำลังทำ',       cls:'cst-progress'},
    pending     : {label:'รอดำเนินการ',   cls:'cst-pending'},
    done        : {label:'เสร็จสิ้น',     cls:'cst-done'},
    cancelled   : {label:'ยกเลิก',        cls:'cst-cancelled'},
  };

  let teamSchedules = [];
  let currentTeamName = '';
  let currentCompany = '';
  const now = new Date();
  let calYear  = now.getFullYear();
  let calMonth = now.getMonth();
  let calStart = null, calEnd = null, calHover = null;
  let listenersAttached = false;

  function pad(n){ return String(n).padStart(2,'0'); }
  function iso(y,m,d){ return `${y}-${pad(m+1)}-${pad(d)}`; }
  function todayIso(){ return iso(now.getFullYear(), now.getMonth(), now.getDate()); }
  function parseIso(s){ const p=String(s).substring(0,10).split('-'); return new Date(+p[0],+p[1]-1,+p[2]); }

  function eventsOnDate(ds){
    const cd = parseIso(ds);
    return teamSchedules.filter(s=>{
      const sd=parseIso(s.start_date), ed=parseIso(s.end_date);
      return cd>=sd && cd<=ed;
    });
  }
  function eventsInRange(lo, hi){
    const ld=parseIso(lo), hd=parseIso(hi);
    return teamSchedules.filter(s=>{ return parseIso(s.start_date)<=hd && parseIso(s.end_date)>=ld; });
  }

  function fmtTH(ds){
    const p=ds.split('-');
    const d=parseInt(p[2]), m=parseInt(p[1])-1, y=parseInt(p[0])+543;
    const dow=DAYS_TH[parseIso(ds).getDay()];
    return `${dow} ${d} ${MONTHS_TH[m]} ${y}`;
  }

  function buildMonth(year, month, gridId, hdrId, nameId){
    document.getElementById(nameId).textContent = `${MONTHS_TH[month]} ${year+543}`;
    const hdrEl = document.getElementById(hdrId);
    hdrEl.innerHTML = '';
    DAYS_TH.forEach(d=>{ const el=document.createElement('div'); el.className='cal-dhdr'; el.textContent=d; hdrEl.appendChild(el); });
    const grid = document.getElementById(gridId); grid.innerHTML='';
    const tod  = todayIso();
    const firstDow = new Date(year, month, 1).getDay();
    const dim      = new Date(year, month+1, 0).getDate();
    const prevDim  = new Date(year, month, 0).getDate();
    const eff_end  = calEnd || calHover;

    for(let i=0; i<firstDow; i++){
      const el=document.createElement('div'); el.className='cal-day cal-other';
      el.innerHTML=`<div class="cal-di">${prevDim-firstDow+1+i}</div>`; grid.appendChild(el);
    }
    for(let d=1; d<=dim; d++){
      const ds   = iso(year, month, d);
      const evs  = eventsOnDate(ds);
      const cnt  = evs.length;
      let cls = 'cal-day';
      if(ds === tod)      cls += ' cal-today';
      if(ds === calStart) cls += ' cal-sel-s';
      if(ds === calEnd)   cls += ' cal-sel-e';
      if(cnt > 0)         cls += ' has-event';
      if(calStart && eff_end){
        const lo = calStart < eff_end ? calStart : eff_end;
        const hi = calStart < eff_end ? eff_end  : calStart;
        if(ds > lo && ds < hi) cls += ' cal-in-range';
      }
      const el = document.createElement('div'); el.className = cls;
      let dotsHtml = '';
      if(cnt > 0){
        const show = Math.min(cnt, 4);
        let dots = '';
        for(let i=0; i<show; i++) dots += `<div class="cal-evdot"></div>`;
        const extra = cnt > 4 ? `<span class="cal-evcount">+${cnt-4}</span>` : '';
        dotsHtml = `<div class="cal-evdots">${dots}${extra}</div>`;
      }
      el.innerHTML = `<div class="cal-di">${d}</div>${dotsHtml}`;
      el.addEventListener('click', () => calHandleClick(ds, evs));
      el.addEventListener('mouseenter', () => { calHover = ds; renderAll(); });
      el.addEventListener('mouseleave', () => { calHover = null; renderAll(); });
      grid.appendChild(el);
    }
    const total = firstDow + dim;
    const trail = (7 - total % 7) % 7;
    for(let i=1; i<=trail; i++){
      const el=document.createElement('div'); el.className='cal-day cal-other';
      el.innerHTML=`<div class="cal-di">${i}</div>`; grid.appendChild(el);
    }
  }

  function renderAll(){
    const ry = calMonth===11 ? calYear+1 : calYear;
    const rm = calMonth===11 ? 0 : calMonth+1;
    buildMonth(calYear, calMonth, 'cal-left-grid',  'cal-left-hdrs',  'cal-left-name');
    buildMonth(ry,      rm,       'cal-right-grid', 'cal-right-hdrs', 'cal-right-name');
    updateBar();
    updateFooter();
  }

  function calHandleClick(ds, evs){
    if(evs && evs.length > 0){
      if(!calStart || (calStart && calEnd)){ calStart=ds; calEnd=null; }
      else if(ds === calStart){ calStart=null; calEnd=null; }
      else if(ds < calStart){ calEnd=calStart; calStart=ds; }
      else { calEnd=ds; }
      renderAll();
      showDayPopup(ds, evs);
      return;
    }
    if(!calStart || (calStart && calEnd)){ calStart=ds; calEnd=null; }
    else if(ds === calStart){ calStart=null; calEnd=null; }
    else if(ds < calStart){ calEnd=calStart; calStart=ds; }
    else { calEnd=ds; }
    renderAll();
  }

  function showDayPopup(ds, evs){
    document.getElementById('cal-popup-date').textContent = fmtTH(ds);
    const popCount = document.getElementById('cal-popup-count');
    popCount.textContent = `${evs.length} งาน`;
    popCount.style.display = '';
    document.getElementById('cal-popup-range-bar').style.display = 'none';
    document.getElementById('cal-popup-inner').innerHTML = buildEventCards(evs);
    document.getElementById('cal-popup-bg').classList.add('open');
  }

  function buildEventCards(evs){
    if(!evs || evs.length === 0){
      return `<div class="cal-empty"><div class="cal-empty-icon">📅</div>ไม่มีงานในวันนี้</div>`;
    }
    return evs.map(ev=>{
      const st     = STATUS_MAP[ev.status] || STATUS_MAP.pending;
      const s_fmt  = String(ev.start_date).substring(0,10).split('-').reverse().join('/');
      const e_fmt  = String(ev.end_date).substring(0,10).split('-').reverse().join('/');
      const noteRow = ev.note
        ? `<span class="cal-ev-ml">📝 หมายเหตุ</span><span class="cal-ev-mv">${escapeHtml(ev.note)}</span>`
        : '';
      return `<div class="cal-ev-card">
        <div class="cal-ev-card-top">
          <span class="cal-so">SO: ${escapeHtml(ev.so_number||'-')}</span>
          <span class="cal-ev-status ${st.cls}">${st.label}</span>
        </div>
        <div class="cal-ev-customer">
          <span class="cal-ev-customer-dot"></span>
          ${escapeHtml(ev.customer_name||'(ไม่ระบุชื่อลูกค้า)')}
        </div>
        <div class="cal-ev-job">${escapeHtml(ev.job_title||'-')}</div>
        <div class="cal-ev-meta">
          <span class="cal-ev-ml">📍 สถานที่</span>
          <span class="cal-ev-mv">${escapeHtml(ev.job_location||'-')}</span>
          <span class="cal-ev-ml">👥 ทีม</span>
          <span class="cal-ev-mv">${escapeHtml(ev.team_name||'-')}</span>
          <span class="cal-ev-ml">📅 ช่วงงาน</span>
          <span class="cal-ev-mv">${s_fmt} → ${e_fmt}</span>
          ${noteRow}
        </div>
      </div>`;
    }).join('');
  }

  function escapeHtml(s){
    return String(s)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }

  function updateBar(){
    const sl=document.getElementById('cal-start-lbl'), ss=document.getElementById('cal-start-sub');
    const el=document.getElementById('cal-end-lbl'),   es=document.getElementById('cal-end-sub');
    const ei=document.getElementById('cal-end-icon');
    if(calStart){ sl.textContent = fmtTH(calStart).replace(/^[^ ]+ /,''); ss.textContent = calStart.split('-').reverse().join('/'); }
    else { sl.textContent='เลือกวันเริ่มต้น'; ss.textContent='วันที่เริ่มงาน'; }
    if(calEnd){ el.textContent = fmtTH(calEnd).replace(/^[^ ]+ /,''); es.textContent = calEnd.split('-').reverse().join('/'); ei.classList.remove('gray'); document.getElementById('cal-end-btn').classList.add('active'); }
    else { el.textContent='เลือกวันสิ้นสุด'; es.textContent='วันที่สิ้นสุดงาน'; ei.classList.add('gray'); document.getElementById('cal-end-btn').classList.remove('active'); }
  }

  function updateFooter(){
    const note    = document.getElementById('cal-footer-note');
    const resetBtn= document.getElementById('cal-reset-btn');
    if(calStart && calEnd){
      const lo=calStart<calEnd?calStart:calEnd, hi=calStart<calEnd?calEnd:calStart;
      const days=Math.round((parseIso(hi)-parseIso(lo))/864e5)+1;
      const cnt=eventsInRange(lo,hi).length;
      note.textContent=`${days} วัน · พบ ${cnt} งานในช่วงนี้`;
      if(resetBtn) resetBtn.style.display='';
    } else if(calStart){
      const dayEvs=eventsOnDate(calStart);
      note.textContent = dayEvs.length ? `${fmtTH(calStart).replace(/^[^ ]+ /,'')} · ${dayEvs.length} งาน` : 'เลือกวันสิ้นสุดเพื่อดูงานในช่วง';
      if(resetBtn) resetBtn.style.display='';
    } else {
      note.textContent='กรุณาเลือกช่วงวันที่ต้องการดูงาน';
      if(resetBtn) resetBtn.style.display='none';
    }
  }

  function attachListeners(){
    if(listenersAttached) return;
    document.getElementById('cal-prev').addEventListener('click',()=>{ calMonth--; if(calMonth<0){calMonth=11;calYear--;} renderAll(); });
    document.getElementById('cal-next').addEventListener('click',()=>{ calMonth++; if(calMonth>11){calMonth=0;calYear++;} renderAll(); });
    listenersAttached = true;
  }

  return {
    open: function(teamName, company){
      currentTeamName = teamName;
      currentCompany = company;
      teamSchedules = ALL_SCHEDULES.filter(s => s.team_name === teamName);
      calStart = null; calEnd = null; calHover = null;
      const today = new Date();
      calYear = today.getFullYear();
      calMonth = today.getMonth();
      document.getElementById('tcal-team-name').textContent = teamName;
      document.getElementById('tcal-company').textContent = company || '—';
      const cnt = teamSchedules.length;
      const cntEl = document.getElementById('tcal-job-count');
      cntEl.textContent = `${cnt} งาน`;
      cntEl.classList.toggle('has-jobs', cnt > 0);
      attachListeners();
      renderAll();
      document.getElementById('tcal-overlay').classList.add('open');
      document.body.style.overflow = 'hidden';
    },
    close: function(){
      document.getElementById('tcal-overlay').classList.remove('open');
      document.getElementById('cal-popup-bg').classList.remove('open');
      document.body.style.overflow = '';
    },
    reset: function(){
      calStart = null; calEnd = null; renderAll();
    }
  };
})();

function openTeamCalendar(teamName, company){ TeamCal.open(teamName, company); }
function closeTeamCalendar(){ TeamCal.close(); }
function calReset(){ TeamCal.reset(); }

/* ESC closes things */
document.addEventListener('keydown', function(e){
  if(e.key === 'Escape'){
    const openDRP = document.querySelector('.drp-popover.open');
    if(openDRP){
      openDRP.classList.remove('open');
      const tr = openDRP.closest('.drp-trigger-row');
      if(tr) tr.classList.remove('open');
      return;
    }
    const tc = document.getElementById('tcal-overlay');
    const pop = document.getElementById('cal-popup-bg');
    if(pop.classList.contains('open')){ pop.classList.remove('open'); }
    else if(tc.classList.contains('open')){ closeTeamCalendar(); }
  }
});
</script>
</body>
</html>