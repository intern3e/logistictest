<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OT Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
:root {
  --bg: #f5f7fa;
  --panel: #ffffff;
  --ink: #1b2d4f;
  --ink-2: #374151;
  --ink-3: #6b7280;
  --ink-4: #9ca3af;
  --line: #e5e7eb;
  --line-2: #d1d5db;
  --hover: #f0f2f5;
  --link: #3E6AE1;
  --accent: #3E6AE1;
  --accent-hover: #2f56c4;
  --accent-bg: #eef2fd;
  --accent-border: #c7d5f5;
  --green: #16a34a;
  --green-bg: #dcfce7;
  --green-border: #86efac;
  --amber: #b45309;
  --amber-bg: #fef3c7;
  --amber-border: #fde68a;
  --red: #dc2626;
  --red-bg: #fee2e2;
  --red-border: #fecaca;
  --blue: #3E6AE1;
  --blue-bg: #eef2fd;
  --blue-border: #c7d5f5;
  --radius: 6px;
  --radius-sm: 4px;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; }
body {
  font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, system-ui, sans-serif;
  background: var(--bg); color: var(--ink);
  font-size: 14px; line-height: 1.5;
  -webkit-font-smoothing: antialiased;
}
.header {
  background: var(--panel); border-bottom: 1px solid var(--line);
  padding: 0 clamp(12px, 2vw, 24px); position: sticky; top: 0; z-index: 50;
  box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.header-inner {
  max-width: 1800px; margin: 0 auto; height: 56px;
  display: flex; align-items: center; justify-content: space-between; gap: 16px;
}
.brand { display: flex; align-items: center; gap: 10px; min-width: 0; }
.brand-logo {
  width: 26px; height: 26px; background: var(--accent); color: white;
  border-radius: 6px; display: grid; place-items: center;
  font-size: 11px; font-weight: 700; flex-shrink: 0;
}
.brand h1 { font-size: 14px; font-weight: 700; letter-spacing: -0.01em; }
.brand-sep { width: 1px; height: 14px; background: var(--line-2); margin: 0 4px; }
.brand-sub { font-size: 13px; color: var(--ink-3); white-space: nowrap; }
.header-actions { display: flex; gap: 6px; align-items: center; flex-shrink: 0; }
@media (max-width: 540px) { .brand-sep, .brand-sub { display: none; } .btn-text-hide { display: none; } }
.btn {
  height: 30px; padding: 0 12px; border: 1px solid var(--line-2);
  border-radius: var(--radius); background: white; color: var(--ink);
  font-size: 13px; font-weight: 600; font-family: inherit; cursor: pointer;
  display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;
  transition: all .2s;
}
.btn:hover { background: var(--hover); border-color: var(--ink-3); }
.btn:disabled { opacity: 0.5; cursor: not-allowed; }
.btn svg { width: 14px; height: 14px; flex-shrink: 0; }
.btn-primary { background: var(--accent); color: white; border-color: var(--accent); }
.btn-primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
.btn-danger { background: var(--red); color: white; border-color: var(--red); }
.btn-danger:hover { background: #dc2626; border-color: #dc2626; }
.btn-refresh { background: var(--accent); color: #fff; border-color: var(--accent); }
.btn-refresh:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
.btn-export { background: var(--green); color: #fff; border-color: var(--green); }
.btn-export:hover { background: #15803d; border-color: #15803d; }
.btn-home { background: #f59e0b; color: #fff; border-color: #f59e0b; }
.btn-home:hover { background: #d97706; border-color: #d97706; }
.container { max-width: 1800px; margin: 0 auto; padding: 20px clamp(12px, 2vw, 24px) 32px; }
.stats {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 1px; background: var(--line); border: 1px solid var(--line);
  border-radius: 12px; overflow: hidden; margin-bottom: 20px;
  box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.stat { background: var(--panel); padding: 14px 16px; }
.stat-label {
  font-size: 11px; color: var(--ink-3); font-weight: 600;
  text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px; white-space: nowrap;
}
.stat-value { font-size: 22px; font-weight: 700; font-variant-numeric: tabular-nums; line-height: 1.1; color: var(--ink); }
.stat-sub { font-size: 12px; color: var(--ink-3); font-weight: 400; margin-left: 4px; }
.stat-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; margin-right: 6px; vertical-align: middle; }
.stat-dot.amber { background: #d97706; } .stat-dot.green { background: var(--green); } .stat-dot.blue { background: var(--blue); }
.filter-bar {
  background: var(--panel); border: 1px solid var(--line); border-bottom: none;
  border-radius: 12px 12px 0 0; padding: 10px 12px;
  display: flex; gap: 6px; flex-wrap: wrap; align-items: center;
}
.input, .select {
  height: 30px; padding: 0 10px; border: 1px solid var(--line-2);
  border-radius: var(--radius); font-size: 13px; font-family: inherit;
  background: white; color: var(--ink); min-width: 120px;
  transition: all .2s;
}
.input:focus, .select:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(91,101,243,.1); }
.input.search {
  flex: 1; min-width: 220px; padding-left: 30px;
  background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><circle cx='11' cy='11' r='8'/><path d='m21 21-4.3-4.3'/></svg>");
  background-repeat: no-repeat; background-position: 10px center;
}
.select {
  padding-right: 26px; appearance: none; -webkit-appearance: none;
  background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
  background-repeat: no-repeat; background-position: right 8px center;
}
.select.active-filter { border-color: var(--accent); background-color: var(--accent-bg); font-weight: 600; color: var(--accent-hover); }
.filter-spacer { flex: 1; min-width: 4px; }
.filter-count { font-size: 12px; color: var(--ink-3); font-variant-numeric: tabular-nums; padding-left: 4px; }
.icon-clear {
  width: 30px; height: 30px; border: 1px solid var(--line-2); border-radius: var(--radius);
  background: white; color: var(--ink-3); cursor: pointer;
  display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-family: inherit;
}
.icon-clear:hover { background: var(--hover); color: var(--ink); }
.refreshing-bar {
  display: flex; align-items: center; gap: 8px; padding: 7px 14px;
  background: var(--accent-bg); border: 1px solid var(--line); border-top: none;
  font-size: 12.5px; color: var(--accent-hover); font-weight: 600;
}
.refreshing-bar .spinner { width:12px;height:12px;border-color: var(--accent-border); border-top-color: var(--accent-hover); margin: 0; }
.table-wrap {
  background: var(--panel); border: 1px solid var(--line);
  border-radius: 0 0 12px 12px;
  overflow-x: auto; overflow-y: auto; max-height: calc(100vh - 230px);
  box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; }
thead { position: sticky; top: 0; z-index: 2; background: #f9fafb; }
thead th {
  padding: 8px 9px; text-align: left; font-size: 11px; font-weight: 700;
  color: var(--ink-3); text-transform: uppercase; letter-spacing: 0.02em;
  border-bottom: 1px solid var(--line); white-space: nowrap;
}
tbody td { padding: 10px 9px; border-bottom: 1px solid var(--line); vertical-align: middle; color: var(--ink); }
tbody tr:hover td { background: #f9fafb; }
tbody tr:last-child td { border-bottom: none; }
td.mono { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--ink-3); }
td.col-name { font-weight: 600; }
td.col-sup { color: var(--ink-2); font-size: 12.5px; }
td.col-time { font-variant-numeric: tabular-nums; white-space: nowrap; }
td.col-time .arr { color: var(--ink-4); margin: 0 4px; }
td.col-hours { font-weight: 600; font-variant-numeric: tabular-nums; white-space: nowrap; }
td.col-detail .clip {
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
  overflow: hidden; line-height: 1.4; color: var(--ink-2); max-width: 220px;
}
.company-tag {
  display: inline-block; padding: 2px 8px; border-radius: var(--radius-sm);
  background: var(--accent-bg); color: var(--accent-hover); font-size: 12px; font-weight: 600; white-space: nowrap;
  border: 1px solid var(--accent-border);
}
.allowance-tag {
  display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px;
  border-radius: 980px; background: var(--amber-bg); color: var(--amber);
  border: 1px solid var(--amber-border); font-size: 12px; font-weight: 600; white-space: nowrap;
}
.allowance-tag.zero { background: #f3f4f6; color: var(--ink-3); border-color: var(--line-2); }
@media (max-width: 900px) { .hide-md { display: none !important; } }
@media (max-width: 640px) { .hide-sm { display: none !important; } }
.badge {
  display: inline-flex; align-items: center; gap: 5px; padding: 2px 8px;
  border-radius: 980px; font-size: 12px; font-weight: 600; white-space: nowrap; border: 1px solid transparent;
}
.badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
.badge.b-working { color: var(--blue); background: var(--blue-bg); border-color: var(--blue-border); }
.badge.b-working::before { background: #3b82f6; animation: pulse-dot 1.6s ease-in-out infinite; }
.badge.b-pending { color: var(--amber); background: var(--amber-bg); border-color: var(--amber-border); }
.badge.b-pending::before { background: #d97706; }
.badge.b-approved { color: #065f46; background: var(--green-bg); border-color: var(--green-border); }
.badge.b-approved::before { background: #10b981; }
.badge.b-rejected { color: #991b1b; background: var(--red-bg); border-color: var(--red-border); }
.badge.b-rejected::before { background: #ef4444; }
@keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
.btn-view {
  display: inline-flex; align-items: center; gap: 6px; height: 26px; padding: 0 10px;
  border: 1px solid var(--line-2); border-radius: var(--radius); background: white;
  color: var(--ink-2); font-size: 12px; font-weight: 600; font-family: inherit; cursor: pointer; white-space: nowrap;
  transition: all .2s;
}
.btn-view:hover { background: var(--hover); border-color: var(--ink-3); color: var(--ink); }
.btn-view svg { width: 12px; height: 12px; }
.view-dots { display: inline-flex; gap: 3px; margin-left: 2px; }
.dot { width: 5px; height: 5px; border-radius: 50%; }
.dot-in { background: #10b981; } .dot-out { background: #d97706; }
.row-actions { display: flex; gap: 4px; justify-content: flex-end; }
.icon-btn {
  width: 26px; height: 26px; border: 1px solid var(--line-2); border-radius: var(--radius);
  background: white; cursor: pointer; display: inline-flex; align-items: center;
  justify-content: center; color: var(--ink-3); font-family: inherit;
  transition: all .2s;
}
.icon-btn:hover { background: var(--hover); color: var(--ink); }
.icon-btn svg { width: 13px; height: 13px; }
.icon-btn.approve { color: var(--green); }
.icon-btn.approve:hover { background: var(--green-bg); border-color: var(--green-border); }
.icon-btn.reject { color: var(--red); }
.icon-btn.reject:hover { background: var(--red-bg); border-color: var(--red-border); }
.icon-btn.delete:hover { background: var(--red-bg); color: var(--red); border-color: var(--red-border); }
.dim { color: var(--ink-4); }
.empty { padding: 64px 20px; text-align: center; color: var(--ink-3); }
.empty-icon {
  width: 36px; height: 36px; margin: 0 auto 12px; display: grid; place-items: center;
  border: 1px solid var(--line); border-radius: 8px; color: var(--ink-4);
}
.empty-icon svg { width: 18px; height: 18px; }
.empty-title { font-size: 14px; font-weight: 700; color: var(--ink); margin-bottom: 4px; }
.empty-desc { font-size: 13px; margin-bottom: 12px; }
.empty a { color: var(--link); text-decoration: none; font-weight: 600; }
.spinner {
  display: inline-block; width: 18px; height: 18px; border: 2px solid var(--line);
  border-top-color: var(--accent); border-radius: 50%; animation: spin 0.7s linear infinite; margin-bottom: 10px;
}
@keyframes spin { to { transform: rotate(360deg); } }
.modal-overlay {
  position: fixed; inset: 0; background: rgba(10, 10, 10, 0.4);
  display: none; align-items: center; justify-content: center; z-index: 600; padding: 20px;
  backdrop-filter: blur(4px);
}
.modal-overlay.show { display: flex; }
.modal {
  background: white; border-radius: 12px; max-width: 460px; width: 100%;
  max-height: 90vh; overflow: auto; box-shadow: 0 12px 40px rgba(0,0,0,0.18);
}
.image-lightbox {
  position: fixed; inset: 0; background: rgba(0, 0, 0, 0.92);
  display: none; align-items: center; justify-content: center; z-index: 700; cursor: zoom-out;
}
.image-lightbox.show { display: flex; }
.image-lightbox img {
  max-width: 96vw; max-height: 96vh; display: block; user-select: none;
  transition: transform 200ms ease; cursor: zoom-in;
}
.image-lightbox img.zoomed { cursor: zoom-out; transform: scale(1.8); }
.lightbox-close {
  position: fixed; top: 16px; right: 16px; width: 36px; height: 36px; border-radius: 50%;
  border: none; background: rgba(255, 255, 255, 0.12); color: white; cursor: pointer;
  display: grid; place-items: center; z-index: 701;
}
.lightbox-close:hover { background: rgba(255, 255, 255, 0.22); }
.lightbox-close svg { width: 18px; height: 18px; }
.modal-header {
  padding: 14px 18px; border-bottom: 1px solid var(--line);
  display: flex; align-items: center; justify-content: space-between;
  background: #f9fafb; border-radius: 12px 12px 0 0;
}
.modal-header h3 { font-size: 14px; font-weight: 700; }
.modal-header .sub { font-size: 12px; font-weight: 400; color: var(--ink-3); margin-left: 6px; }
.modal-close {
  width: 26px; height: 26px; border: none; background: transparent; cursor: pointer;
  color: var(--ink-3); border-radius: var(--radius); display: grid; place-items: center;
}
.modal-close:hover { background: var(--hover); color: var(--ink); }
.modal-close svg { width: 14px; height: 14px; }
.modal-body { padding: 18px; }
.modal-body label { display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--ink-2); }
.modal-body textarea {
  width: 100%; padding: 8px 10px; border: 1px solid var(--line-2); border-radius: var(--radius);
  font-family: inherit; font-size: 13px; min-height: 76px; resize: vertical; line-height: 1.5; color: var(--ink);
}
.modal-body textarea:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(91,101,243,.1); }
.modal-footer {
  padding: 12px 18px; border-top: 1px solid var(--line);
  display: flex; gap: 6px; justify-content: flex-end;
}
.info-row { display: flex; gap: 10px; font-size: 13px; padding: 4px 0; }
.info-row .k { color: var(--ink-3); min-width: 70px; font-weight: 400; }
.info-row .v { color: var(--ink); font-weight: 600; }
.photo-modal {
  background: white; border-radius: 12px; max-width: 880px; width: 100%;
  max-height: 92vh; overflow: hidden; display: flex; flex-direction: column;
  box-shadow: 0 12px 40px rgba(0,0,0,0.18);
}
.photo-modal-body { padding: 18px; overflow-y: auto; }
.photo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media (max-width: 680px) { .photo-grid { grid-template-columns: 1fr; } }
.photo-block {
  border: 1px solid var(--line); border-radius: 10px; overflow: hidden;
  background: white; display: flex; flex-direction: column;
}
.photo-block-header {
  padding: 8px 12px; font-size: 12px; font-weight: 600; border-bottom: 1px solid var(--line);
  display: flex; align-items: center; gap: 8px; background: #f9fafb;
}
.photo-block-header .marker { width: 8px; height: 8px; border-radius: 50%; }
.photo-block.in .marker { background: #10b981; } .photo-block.out .marker { background: #d97706; }
.photo-block-img {
  width: 100%; aspect-ratio: 1; object-fit: cover; background: #f5f5f5; display: block; cursor: zoom-in;
}
.photo-block-img:hover { opacity: 0.95; }
.photo-block-empty {
  width: 100%; aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
  color: var(--ink-4); font-size: 13px; flex-direction: column; gap: 8px; background: #fafafa;
}
.photo-block-empty svg { width: 22px; height: 22px; opacity: 0.6; }
.map-embed { position: relative; width: 100%; aspect-ratio: 16 / 10; background: #f5f5f5; border-top: 1px solid var(--line); overflow: hidden; }
.map-embed iframe { width: 100%; height: 100%; border: 0; display: block; }
.map-embed-empty {
  width: 100%; aspect-ratio: 16 / 10; display: flex; align-items: center; justify-content: center;
  color: var(--ink-4); font-size: 12.5px; flex-direction: column; gap: 8px; background: #fafafa; border-top: 1px solid var(--line);
}
.map-embed-empty svg { width: 18px; height: 18px; opacity: 0.6; }
.map-open-btn {
  position: absolute; top: 8px; right: 8px; background: white; border: 1px solid var(--line-2);
  border-radius: var(--radius); padding: 4px 8px; font-size: 11.5px; font-weight: 600;
  color: var(--ink); text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.map-open-btn:hover { background: var(--hover); }
.map-open-btn svg { width: 11px; height: 11px; }
.photo-block-footer { padding: 10px 12px; font-size: 12px; background: white; border-top: 1px solid var(--line); display: flex; flex-direction: column; gap: 4px; }
.photo-block-footer .row { display: flex; align-items: center; gap: 6px; color: var(--ink-2); }
.photo-block-footer .row svg { width: 12px; height: 12px; flex-shrink: 0; }
.photo-block-footer .gps { color: var(--ink-4); font-size: 11px; font-family: 'JetBrains Mono', monospace; }
.toast {
  position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%) translateY(20px);
  background: var(--ink); color: white; padding: 10px 16px; border-radius: var(--radius);
  font-size: 13px; font-weight: 600; box-shadow: 0 8px 20px rgba(0,0,0,0.2); z-index: 200; opacity: 0;
  transition: opacity 200ms, transform 200ms; pointer-events: none; display: flex; align-items: center; gap: 8px;
}
.toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
.toast.success { background: #10b981; } .toast.error { background: #ef4444; }
.login-gate {
  position: fixed; inset: 0; background: var(--bg); z-index: 500;
  display: flex; align-items: center; justify-content: center; padding: 20px;
}
.login-card {
  background: var(--panel); border: 1px solid var(--line); border-radius: 12px;
  padding: 32px 28px; width: 100%; max-width: 360px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.06);
}
.login-logo {
  width: 44px; height: 44px; background: var(--accent); color: white; border-radius: 10px;
  display: grid; place-items: center; font-size: 16px; font-weight: 700; margin: 0 auto 16px;
}
.login-card h2 { font-size: 17px; font-weight: 700; text-align: center; margin-bottom: 4px; }
.login-card .sub { font-size: 13px; color: var(--ink-3); text-align: center; margin-bottom: 22px; }
.login-card label { display: block; font-size: 12.5px; font-weight: 600; color: var(--ink-2); margin-bottom: 6px; }
.login-card .field { margin-bottom: 14px; }
.login-card input {
  width: 100%; height: 40px; padding: 0 12px; border: 1px solid var(--line-2);
  border-radius: var(--radius); font-size: 14px; font-family: inherit; color: var(--ink);
}
.login-card input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(91,101,243,.1); }
.login-btn {
  width: 100%; height: 42px; border: none; border-radius: var(--radius);
  background: var(--accent); color: white; font-size: 14px; font-weight: 700;
  font-family: inherit; cursor: pointer; margin-top: 4px; transition: all .2s;
}
.login-btn:hover { background: var(--accent-hover); }
.login-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.login-error { color: var(--red); font-size: 13px; text-align: center; margin-top: 12px; min-height: 18px; }
.tabs { display: flex; gap: 4px; margin-left: 8px; }
.tab {
  height: 30px; padding: 0 14px; border: 1px solid transparent; border-radius: var(--radius);
  background: transparent; color: var(--ink-3); font-size: 13px; font-weight: 600;
  font-family: inherit; cursor: pointer; white-space: nowrap;
}
.tab:hover { background: var(--hover); color: var(--ink); }
.tab.active { background: var(--accent); color: white; }
@media (max-width: 600px) { .brand-sub, .brand-sep { display: none; } }
.user-chip {
  display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--ink-3);
  padding: 0 4px;
}
.user-chip b { color: var(--ink); font-weight: 700; }
.page { display: none; }
.page.active { display: block; }
.company-pills {
  display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px;
}
.cpill {
  height: 34px; padding: 0 14px; border: 1px solid var(--line-2); border-radius: 980px;
  background: white; color: var(--ink-2); font-size: 13px; font-weight: 600;
  font-family: inherit; cursor: pointer; display: inline-flex; align-items: center; gap: 7px;
  white-space: nowrap; transition: all 120ms;
}
.cpill:hover { background: var(--hover); border-color: var(--ink-3); }
.cpill.active { background: var(--accent); color: white; border-color: var(--accent); }
.cpill .cnt {
  font-size: 11px; font-weight: 700; padding: 1px 7px; border-radius: 980px;
  background: #f3f4f6; color: var(--ink-3); font-variant-numeric: tabular-nums;
}
.cpill.active .cnt { background: rgba(255,255,255,0.22); color: white; }
.cpill.all { font-weight: 700; }
.company-gate {
  position: fixed; inset: 0; background: var(--bg); z-index: 400;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 24px; overflow-y: auto;
}
.company-gate-inner { width: 100%; max-width: 720px; }
.company-gate .cg-logo {
  width: 52px; height: 52px; background: var(--accent); color: white; border-radius: 12px;
  display: grid; place-items: center; font-size: 19px; font-weight: 700; margin: 0 auto 18px;
}
.company-gate h2 { font-size: 21px; font-weight: 700; text-align: center; margin-bottom: 6px; letter-spacing: -0.02em; }
.company-gate .cg-sub { font-size: 14px; color: var(--ink-3); text-align: center; margin-bottom: 28px; }
.company-grid {
  display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px;
}
.company-tile {
  border: 1px solid var(--line-2); border-radius: 12px; background: white;
  padding: 20px 16px; cursor: pointer; text-align: center; transition: all 140ms;
  display: flex; flex-direction: column; align-items: center; gap: 10px;
}
.company-tile:hover { border-color: var(--accent); box-shadow: 0 6px 20px rgba(91,101,243,0.12); transform: translateY(-2px); }
.company-tile .ct-icon {
  width: 72px; height: 72px; border-radius: 14px; display: grid; place-items: center;
  background: #f3f4f6; font-size: 24px; font-weight: 700; color: var(--ink-2);
}
.company-tile .ct-name { font-size: 14px; font-weight: 700; color: var(--ink); line-height: 1.3; }
.company-tile .ct-count { font-size: 12px; color: var(--ink-3); }
.company-tile.all-tile .ct-icon { background: var(--accent); color: white; }
.company-tile .ct-logo {
  width: 72px; height: 72px; object-fit: contain; display: block;
}
.hide-allowance .col-allowance { display: none; }
.cleaning-mode .col-sup-head, .cleaning-mode .col-sup-cell,
.cleaning-mode .col-comp-head, .cleaning-mode .col-comp-cell { display: none; }
.cleaning-grid {
  display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 14px; margin-top: 18px;
}
.ce-card {
  background: var(--panel); border: 1px solid var(--line); border-radius: 12px;
  overflow: hidden; display: flex; flex-direction: column;
  box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.ce-card-head {
  display: flex; align-items: center; gap: 10px; padding: 12px 14px;
  border-bottom: 1px solid var(--line); background: #fafbfc;
}
.ce-avatar {
  width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
  background: var(--accent-bg); color: var(--accent-hover); border: 1px solid var(--accent-border);
  display: grid; place-items: center; font-size: 13px; font-weight: 700;
}
.ce-name { font-size: 14px; font-weight: 700; color: var(--ink); line-height: 1.25; }
.ce-code { font-size: 11.5px; color: var(--ink-3); font-family: 'JetBrains Mono', monospace; }
.ce-count {
  margin-left: auto; font-size: 11px; font-weight: 700; color: var(--ink-3);
  background: var(--hover); padding: 2px 8px; border-radius: 980px; white-space: nowrap;
}
.ce-body { padding: 8px 10px; display: flex; flex-direction: column; gap: 6px; }
.ce-empty { padding: 16px 6px; text-align: center; color: var(--ink-4); font-size: 12.5px; }
.ce-row {
  display: flex; align-items: center; gap: 8px; padding: 8px 8px;
  border: 1px solid var(--line); border-radius: 8px; font-size: 12.5px;
  flex-wrap: wrap;
}
.ce-row .ce-date { font-weight: 600; color: var(--ink-3); white-space: nowrap; font-size: 11.5px; }
.ce-row .ce-zone { font-weight: 700; color: var(--ink); white-space: nowrap; font-size: 13.5px; }
.ce-row .ce-time { color: var(--ink-2); font-variant-numeric: tabular-nums; white-space: nowrap; }
.ce-row .ce-hours { color: var(--ink-3); white-space: nowrap; }
.ce-row .ce-spacer { flex: 1; }
.ce-row .ce-actions { display: flex; gap: 4px; }
  </style>
</head>
<body>
<div class="company-gate" id="companyGate">
  <div class="company-gate-inner">
    <div class="cg-logo" style="padding:0;overflow:hidden;"><img src="https://ot-drive.devintern3e.workers.dev/img?id=1kT1piF4NBZkdrZIYHp55kosM_rzvRfIp" alt="OT" style="width:100%;height:100%;object-fit:cover;" onerror="this.parentElement.textContent='OT'"></div>
    <h2>เลือกบริษัท</h2>
    <div class="cg-sub">เลือกบริษัทที่ต้องการดูคำขอ OT</div>
    <div class="company-grid" id="companyGrid">
      <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--ink-3);">
        <div class="spinner"></div><div>กำลังโหลด…</div>
      </div>
    </div>
  </div>
</div>

<div class="login-gate" id="loginGate" style="display:none;">
  <div class="login-card">
    <div class="login-logo" style="padding:0;overflow:hidden;"><img src="https://ot-drive.devintern3e.workers.dev/img?id=1kT1piF4NBZkdrZIYHp55kosM_rzvRfIp" alt="OT" style="width:100%;height:100%;object-fit:cover;" onerror="this.parentElement.textContent='OT'"></div>
    <h2>เข้าสู่ระบบผู้ดูแล</h2>
    <div class="sub">หน้าพนักงานสำหรับผู้ดูแลเท่านั้น</div>
    <div class="field">
      <label for="loginCode">รหัสพนักงาน</label>
      <input type="text" id="loginCode" placeholder="เช่น 46001" autocomplete="off"
             onkeydown="if(event.key==='Enter'){document.getElementById('loginPass').focus();}">
    </div>
    <div class="field">
      <label for="loginPass">รหัสผ่าน</label>
      <input type="password" id="loginPass" placeholder="••••" autocomplete="off"
             onkeydown="if(event.key==='Enter') doLogin();">
    </div>
    <button class="login-btn" id="loginBtn" onclick="doLogin()">เข้าสู่ระบบ</button>
    <button class="btn" style="width:100%;height:38px;margin-top:8px;justify-content:center;" onclick="cancelLogin()">ยกเลิก</button>
    <div class="login-error" id="loginError"></div>
  </div>
</div>

<div class="header">
  <div class="header-inner">
    <div class="brand">
      <div class="brand-logo" style="padding:0;overflow:hidden;"><img src="https://ot-drive.devintern3e.workers.dev/img?id=1kT1piF4NBZkdrZIYHp55kosM_rzvRfIp" alt="OT" style="width:100%;height:100%;object-fit:cover;" onerror="this.parentElement.textContent='OT'"></div>
      <h1>Admin</h1>
      <div class="brand-sep"></div>
      <span class="brand-sub" id="pageTitle">คำขอ OT</span>
    </div>
    <div class="header-actions">
      <span class="user-chip" id="userChip"></span>
      <button class="btn btn-refresh" id="btnRefresh" onclick="loadData()" title="รีเฟรชข้อมูล">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
        <span class="btn-text-hide">รีเฟรช</span>
      </button>
      <button class="btn btn-export" id="btnExport" onclick="exportCSV()" title="ดาวน์โหลด CSV">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        <span class="btn-text-hide">ดาวน์โหลด</span>
      </button>
      <button class="btn btn-home" id="btnChangeCompany" onclick="backToCompanyGate()" title="หน้าแรก">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span class="btn-text-hide">หน้าแรก</span>
      </button>
    </div>
  </div>
</div>

<div class="container">
  <div class="page active" id="pageOt">
  <div class="stats" id="statsBar">
    <div class="stat"><div class="stat-label">ทั้งหมด</div><div class="stat-value" id="sumTotal">— <span class="stat-sub">คำขอ</span></div></div>
    <div class="stat"><div class="stat-label"><span class="stat-dot blue"></span>กำลังทำงาน</div><div class="stat-value" id="sumWorking">— <span class="stat-sub">คน</span></div></div>
    <div class="stat"><div class="stat-label"><span class="stat-dot amber"></span>รออนุมัติ</div><div class="stat-value" id="sumPending">— <span class="stat-sub">คำขอ</span></div></div>
    <div class="stat"><div class="stat-label"><span class="stat-dot green"></span>อนุมัติแล้ว</div><div class="stat-value" id="sumApproved">— <span class="stat-sub">คำขอ</span></div></div>
    <div class="stat"><div class="stat-label">เวลารวมที่อนุมัติ</div><div class="stat-value" id="sumHours" style="font-size: 18px;">—</div></div>
  </div>

  <div class="filter-bar">
    <input type="search" id="searchInput" class="input search" placeholder="ค้นหาชื่อ บริษัท หรืองาน…" oninput="onSearchInput()">
    <select id="statusFilter" class="select" onchange="onStatusFilterChange()">
      <option value="__open__">ยังไม่ปิดเคส</option>
      <option value="">ทุกสถานะ</option>
      <option value="กำลังทำงาน">กำลังทำงาน</option>
      <option value="รออนุมัติ">รออนุมัติ</option>
      <option value="อนุมัติ">อนุมัติ</option>
      <option value="ไม่อนุมัติ">ไม่อนุมัติ</option>
    </select>
    <select id="companyFilter" class="select" onchange="onCompanyFilterChange()" style="display:none;"><option value="">ทุกบริษัท</option></select>
    <select id="supervisorFilter" class="select" onchange="renderTable()"><option value="">ทุกหัวหน้า</option></select>
    <select id="allowanceFilter" class="select" onchange="renderTable()">
      <option value="">เบี้ยเลี้ยง: ทั้งหมด</option>
      <option value="yes">เฉพาะที่ขอเบี้ยเลี้ยง</option>
    </select>
    <input type="date" id="dateFilter" class="input" style="min-width:140px;" onchange="renderTable()">
    <button class="icon-clear" type="button" onclick="clearDateFilter()" id="clearDateBtn" style="display:none;" title="ล้างวันที่">✕</button>
    <div class="filter-spacer"></div>
    <span class="filter-count" id="recordCount">0 รายการ</span>
  </div>

  <div class="refreshing-bar" id="refreshingBar" style="display:none;">
    <span class="spinner"></span><span class="rb-text">กำลังอัปเดต…</span>
  </div>

  <div class="table-wrap" id="otTableWrap">
    <table id="otTable">
      <thead>
        <tr>
          <th class="hide-md">ส่งเมื่อ</th>
          <th>วัน OT</th>
          <th>ชื่อ</th>
          <th class="hide-sm col-sup-head">หัวหน้า</th>
          <th class="col-comp-head">บริษัท</th>
          <th>เวลา</th>
          <th>รวม</th>
          <th class="hide-sm">งานที่ทำ</th>
          <th class="col-allowance">เบี้ยเลี้ยง</th>
          <th>หลักฐาน</th>
          <th>สถานะ</th>
          <th class="hide-md">หมายเหตุ</th>
          <th style="text-align:right;">การจัดการ</th>
        </tr>
      </thead>
      <tbody id="tableBody">
        <tr><td colspan="13"><div class="empty"><div class="spinner"></div><div class="empty-desc">กำลังโหลดข้อมูล…</div></div></td></tr>
      </tbody>
    </table>
  </div>

  <div class="cleaning-grid" id="cleaningGrid" style="display:none;"></div>
  </div><!-- /pageOt -->

  <div class="page" id="pageEmp">
    <div class="filter-bar" style="border-radius: 12px;">
      <input type="search" id="empSearch" class="input search" placeholder="ค้นหารหัส ชื่อ หรือตำแหน่ง…" oninput="renderEmpTable()">
      <select id="empPosFilter" class="select" onchange="renderEmpTable()"><option value="">ทุกตำแหน่ง</option></select>
      <div class="filter-spacer"></div>
      <span class="filter-count" id="empCount">0 คน</span>
      <button class="btn btn-primary" onclick="openEmpAdd()" style="margin-left:6px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        เพิ่มพนักงาน
      </button>
    </div>
    <div class="table-wrap" style="border-radius: 0 0 12px 12px; max-height: calc(100vh - 180px);">
      <table>
        <thead>
          <tr>
            <th>รหัสพนักงาน</th>
            <th>ชื่อ-นามสกุล</th>
            <th>ตำแหน่ง</th>
            <th class="hide-sm">เบอร์โทร</th>
            <th>รหัสผ่าน</th>
            <th style="text-align:right;">การจัดการ</th>
          </tr>
        </thead>
        <tbody id="empTableBody">
          <tr><td colspan="6"><div class="empty"><div class="spinner"></div><div class="empty-desc">กำลังโหลด…</div></div></td></tr>
        </tbody>
      </table>
    </div>
  </div><!-- /pageEmp -->
</div>

<div class="modal-overlay" id="empModal" onclick="if(event.target===this) closeEmpModal()">
  <div class="modal">
    <div class="modal-header">
      <h3 id="empModalTitle">เพิ่มพนักงาน</h3>
      <button class="modal-close" onclick="closeEmpModal()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="modal-body">
      <div style="margin-bottom:12px;">
        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--ink-2);margin-bottom:6px;">รหัสพนักงาน</label>
        <input type="text" id="empCodeInput" class="input" style="width:100%;height:38px;" placeholder="เช่น 46001">
      </div>
      <div style="margin-bottom:12px;">
        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--ink-2);margin-bottom:6px;">ชื่อ-นามสกุล</label>
        <input type="text" id="empNameInput" class="input" style="width:100%;height:38px;" placeholder="ชื่อ นามสกุล">
      </div>
      <div style="margin-bottom:12px;">
        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--ink-2);margin-bottom:6px;">ตำแหน่ง <span style="color:var(--ink-4);font-weight:400;">(เลือกหรือพิมพ์ใหม่)</span></label>
        <input type="text" id="empPosInput" class="input" style="width:100%;height:38px;" list="posOptions" placeholder="เช่น พนักงานขาย, admin" autocomplete="off">
        <datalist id="posOptions"></datalist>
      </div>
      <div style="margin-bottom:12px;">
        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--ink-2);margin-bottom:6px;">เบอร์โทร <span style="color:var(--ink-4);font-weight:400;">(ถ้ามี)</span></label>
        <input type="text" id="empPhoneInput" class="input" style="width:100%;height:38px;" placeholder="08x-xxx-xxxx">
      </div>
      <div>
        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--ink-2);margin-bottom:6px;" id="empPassLabel">รหัสผ่าน (4 หลัก)</label>
        <input type="text" id="empPassInput" class="input" style="width:100%;height:38px;" maxlength="4" placeholder="••••">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closeEmpModal()">ยกเลิก</button>
      <button class="btn btn-primary" id="empSaveBtn" onclick="saveEmp()">บันทึก</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="actionModal" onclick="if(event.target===this) closeModal()">
  <div class="modal">
    <div class="modal-header">
      <h3 id="actionTitle">อนุมัติคำขอ</h3>
      <button class="modal-close" onclick="closeModal()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="modal-body">
      <div class="info-row"><span class="k">รหัส</span><span class="v" id="actionReqId" style="font-family:'JetBrains Mono',monospace;font-size:12px;"></span></div>
      <div class="info-row"><span class="k">ชื่อ</span><span class="v" id="actionName"></span></div>
      <div class="info-row"><span class="k">หัวหน้า</span><span class="v" id="actionSupervisor"></span></div>
      <div class="info-row"><span class="k">บริษัท</span><span class="v" id="actionCompany"></span></div>
      <div class="info-row"><span class="k">เบี้ยเลี้ยง</span><span class="v" id="actionAllowance"></span></div>
      <div class="info-row"><span class="k">เวลา</span><span class="v" id="actionTime"></span></div>
      <div class="info-row"><span class="k">รวม</span><span class="v" id="actionHours"></span></div>
      <div class="info-row" style="margin-bottom: 12px;"><span class="k">วันที่</span><span class="v" id="actionDate"></span></div>
      <label for="noteInput">หมายเหตุ <span style="color:var(--ink-4);font-weight:400;">(ถ้ามี)</span></label>
      <textarea id="noteInput" placeholder="เพิ่มหมายเหตุ…"></textarea>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closeModal()">ยกเลิก</button>
      <button class="btn" id="confirmBtn" onclick="confirmAction()">ยืนยัน</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="photoInfoModal" onclick="if(event.target===this) closePhotoModal()">
  <div class="photo-modal">
    <div class="modal-header">
      <h3>หลักฐาน<span class="sub" id="photoModalSub"></span></h3>
      <button class="modal-close" onclick="closePhotoModal()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="photo-modal-body"><div class="photo-grid" id="photoGrid"></div></div>
  </div>
</div>

<div class="modal-overlay" id="exportModal" onclick="if(event.target===this) closeExportModal()">
  <div class="modal">
    <div class="modal-header">
      <h3>ดาวน์โหลดข้อมูล</h3>
      <button class="modal-close" onclick="closeExportModal()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="modal-body">
      <label>ช่วงวันที่</label>
      <div style="display:flex; gap:8px; align-items:center; margin-bottom:14px;">
        <input type="date" id="expDateFrom" class="input" style="flex:1; height:38px;" onchange="updateExportCount()">
        <span style="color:var(--ink-3); font-size:13px;">ถึง</span>
        <input type="date" id="expDateTo" class="input" style="flex:1; height:38px;" onchange="updateExportCount()">
        <button class="icon-clear" type="button" onclick="clearExportDates()" title="ล้างวันที่">✕</button>
      </div>
      <label for="expCompany">บริษัท</label>
      <select id="expCompany" class="select" style="width:100%; height:38px; margin-bottom:14px;" onchange="onExpCompanyChange()"><option value="">ทุกบริษัท</option></select>
      <label for="expEmployee">พนักงาน</label>
      <select id="expEmployee" class="select" style="width:100%; height:38px; margin-bottom:14px;" onchange="updateExportCount()"><option value="">ทุกคน</option></select>
      <label for="expStatus">สถานะ</label>
      <select id="expStatus" class="select" style="width:100%; height:38px; margin-bottom:6px;" onchange="updateExportCount()">
        <option value="">ทุกสถานะ</option>
        <option value="กำลังทำงาน">กำลังทำงาน</option>
        <option value="รออนุมัติ">รออนุมัติ</option>
        <option value="อนุมัติ">อนุมัติ</option>
        <option value="ไม่อนุมัติ">ไม่อนุมัติ</option>
      </select>
      <div id="exportPreview" style="font-size:13px; color:var(--ink-3); margin-top:10px; padding:10px 12px; background:#f9fafb; border:1px solid var(--line); border-radius:var(--radius);">
        จะดาวน์โหลด <b id="exportCount">0</b> รายการ
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="closeExportModal()">ยกเลิก</button>
      <button class="btn btn-primary" onclick="confirmExport()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        ดาวน์โหลด CSV
      </button>
    </div>
  </div>
</div>

<div class="image-lightbox" id="imageModal" onclick="closeImageModal()">
  <button class="lightbox-close" onclick="event.stopPropagation(); closeImageModal()" title="ปิด (Esc)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
  <img id="modalImg" src="" alt="" onclick="event.stopPropagation(); toggleZoom(this);">
</div>

<div class="toast" id="toast"></div>

<script>
// ══════════════════════════════════════════════════════════════
//  VPS REST API (แทน Apps Script เดิม)
// ══════════════════════════════════════════════════════════════
const API_BASE = 'http://102.129.229.6:3001';
const TXN_API  = API_BASE + '/transaction';
const EMP_API  = API_BASE + '/emp';
const API_KEY  = 'hikari20259f3c6e1b0f2d9c9c0e5e0b4d8b4e6e9c0c6c2f3e7b8a9f1d2e3c4b5a6f7d8e9';
const IMG_PROXY = 'https://ot-drive.devintern3e.workers.dev/img?id=';

const HEADERS = { 'Content-Type': 'application/json', 'x-api-key': API_KEY };

let allData = [];
let currentAction = null;
let empData = [];
let empEditId = null;
let adminUser = null;
let cleaningOnly = false;

function normStatus(s) {
  s = String(s || '').trim();
  const map = {
    'active': 'กำลังทำงาน',
    'pending': 'รออนุมัติ',
    'approved': 'อนุมัติ',
    'rejected': 'ไม่อนุมัติ'
  };
  return map[s.toLowerCase()] || s;
}

// ══════════════════════════════════════════════════════════════
//  ★★★ FIX (2026-08-07): เวลา/วันที่ — เดิมบวก +7 ชั่วโมงให้ทุก timestamp
//  โดยไม่เช็คก่อนว่าเป็น UTC จริงไหม ถ้า backend เก็บเป็นเวลาไทย local
//  อยู่แล้ว (ไม่มี Z/offset) จะบวกซ้ำ ทำให้บางรายการเลื่อนไปอีกวัน
//  → ตอนนี้ shift เฉพาะ timestamp ที่มี timezone info ชัดเจนเท่านั้น
// ══════════════════════════════════════════════════════════════
function _hasTZInfo(v) {
  const s = String(v).trim();
  return /Z$/i.test(s) || /[+-]\d{2}:?\d{2}$/.test(s);
}
function _parseNaive(v) {
  const m = String(v).match(/^(\d{4})-(\d{2})-(\d{2})(?:[T ](\d{2}):(\d{2}):(\d{2}))?/);
  if (!m) return null;
  return { y: +m[1], mo: +m[2], d: +m[3], h: +(m[4] || 0), mi: +(m[5] || 0), s: +(m[6] || 0) };
}
function _shift7(v) {
  if (v === null || v === undefined || v === '') return null;
  const s = String(v).trim();
  if (_hasTZInfo(s)) {
    // มี timezone ชัดเจน (UTC/offset) → แปลงเป็นเวลาไทยจริง (+7)
    const d = new Date(s);
    if (isNaN(d.getTime())) return null;
    d.setUTCHours(d.getUTCHours() + 7);
    return d;
  }
  // ไม่มี timezone info → ถือว่า backend เก็บเป็นเวลาไทย local อยู่แล้ว ไม่ต้อง shift ซ้ำ
  const p = _parseNaive(s);
  if (p) return new Date(Date.UTC(p.y, p.mo - 1, p.d, p.h, p.mi, p.s));
  const d = new Date(s);
  return isNaN(d.getTime()) ? null : d;
}
function _pad(n) { return String(n).padStart(2, '0'); }
function _fmtTime(v) {
  if (!v) return '';
  const d = _shift7(v);
  if (d) return _pad(d.getUTCHours()) + ':' + _pad(d.getUTCMinutes());
  v = String(v);
  return (v.indexOf(':') !== -1 && v.length >= 5) ? v.substring(0, 5) : v;
}
function _fmtDate(v) {
  if (!v) return '';
  const d = _shift7(v);
  if (d) return _pad(d.getUTCDate()) + '/' + _pad(d.getUTCMonth() + 1) + '/' + d.getUTCFullYear();
  v = String(v);
  const iso = v.length >= 10 ? v.substring(0, 10) : v;
  const p = iso.split('-');
  if (p.length === 3) return p[2] + '/' + p[1] + '/' + p[0];
  return iso;
}
function _fmtDateTime(v) {
  if (!v) return '';
  const d = _shift7(v);
  if (!d) return String(v);
  return _pad(d.getUTCDate()) + '/' + _pad(d.getUTCMonth() + 1) + '/' + d.getUTCFullYear() +
    ' ' + _pad(d.getUTCHours()) + ':' + _pad(d.getUTCMinutes());
}

function _driveId(url) {
  if (!url) return null;
  url = String(url);
  let m = url.match(/\/file\/d\/([^/]+)/); if (m) return m[1];
  m = url.match(/[?&]id=([^&]+)/); if (m) return m[1];
  m = url.match(/\/d\/([a-zA-Z0-9_-]{10,})/); if (m) return m[1];
  return null;
}
function _imgSrc(raw) {
  if (!raw) return '';
  raw = String(raw).trim();
  if (raw.indexOf('data:') === 0) return raw;
  const id = _driveId(raw);
  if (id) return IMG_PROXY + id;
  if (raw.indexOf('http') === 0) return raw;
  return 'data:image/jpeg;base64,' + raw;
}

function _companyKey(s) {
  return String(s || '').toLowerCase().replace(/[\s.\-_&]/g, '');
}
const COMPANY_ALIASES = {
  'tripleetrading': 'Triple E Trading',
  '3etrading': 'Triple E Trading',
  'tripleeinnovation': 'Triple E Innovation',
  '3einnovation': 'Triple E Innovation',
  'tripleeempiregroup': 'Triple E Empire Group',
  'tripleeempire': 'Triple E Empire Group',
  '3eempiregroup': 'Triple E Empire Group',
  '3eempire': 'Triple E Empire Group',
  'tripleelighting': 'Triple E Lighting',
  '3elighting': 'Triple E Lighting',
  'hikaridenki': 'Hikari Denki',
  'eitapaul': 'Eita & Paul',
  'triplepfactoryeng': 'Triple P Factory & Eng',
  'triplepfactoryengineering': 'Triple P Factory & Eng',
  'aetinternational': 'AE&T International',
  'aet': 'AE&T International',
};
function _canonCompany(s) {
  const clean = _cleanName(s);
  const key = _companyKey(clean);
  if (COMPANY_ALIASES[key]) return COMPANY_ALIASES[key];
  return clean;
}

function normalizeRow(e, i) {
  const start = _fmtTime(e.start_time);
  const end = _fmtTime(e.end_time);
  const mins = minutesBetween(start, end);
  const hoursStr = e.total_hours ? String(e.total_hours).replace(/\.0+$/, '') + ' ชม.'
                                 : (mins > 0 ? formatDurationMin(mins) : '');
  return {
    id: e.id,
    requestId: 'OT-' + e.id,
    submittedAt: _fmtDateTime(e.record_date),
    // ★ FIX: เก็บ record_date ที่แปลงเป็นเวลาไทยแล้ว (Date object) ไว้ใช้กรองวันที่
    //   เฉพาะโหมด "ทำความสะอาด" — งานแม่บ้านอิงตามเวลาที่ส่งจริง ไม่ใช่ ot_date
    recordDate: _shift7(e.record_date),
    otDate: _fmtDate(e.ot_date),
    employeeName: e.emp_name || '',
    supervisor: e.supervisor_name || '',
    company: _canonCompany(e.company || ''),
    department: e.department || '',
    startTime: start,
    endTime: end,
    hours: hoursStr,
    workDetail: _cleanPhotosMarker(e.work_detail || ''),
    status: normStatus(e.status),
    note: e.approver_remark || '',
    allowance: e.allowance || '',
    selfieIn: _imgSrc(e.selfie_start),
    mapIn: e.map_start || '',
    gpsIn: e.gps_start || '',
    photoTimeIn: _fmtTime(e.photo_time_start),
    selfieOut: _imgSrc(e.selfie_end),
    mapOut: _cleanPhotosMarker(e.map_end || ''),
    gpsOut: e.gps_end || '',
    photoTimeOut: _fmtTime(e.photo_time_end),
    extraPhotos: _parseExtraPhotos(e.extra_photos)
                   .concat(_parsePhotosFromField(e.map_end))
                   .concat(_parsePhotosFromField(e.work_detail))
                   .filter((v, i, a) => a.indexOf(v) === i),
  };
}

function _parseExtraPhotos(raw) {
  if (!raw) return [];
  if (Array.isArray(raw)) return raw.map(u => _imgSrc(u)).filter(u => u);
  const s = String(raw).trim();
  if (!s || s === '[]') return [];
  try {
    const arr = JSON.parse(s);
    if (Array.isArray(arr)) return arr.map(u => _imgSrc(u)).filter(u => u);
  } catch (_) {}
  if (s.indexOf('http') === 0 || s.indexOf('data:') === 0) return [_imgSrc(s)];
  return [];
}

function _parsePhotosFromField(val) {
  if (!val) return [];
  const s = String(val);
  const marker = '##PHOTOS##';
  const idx = s.indexOf(marker);
  if (idx === -1) return [];
  try {
    const jsonPart = s.substring(idx + marker.length).trim();
    const arr = JSON.parse(jsonPart);
    if (Array.isArray(arr)) return arr.map(u => _imgSrc(u)).filter(u => u);
  } catch (_) {}
  return [];
}

function _cleanPhotosMarker(val) {
  if (!val) return '';
  const idx = String(val).indexOf('##PHOTOS##');
  return idx === -1 ? String(val) : String(val).substring(0, idx).trim();
}

// ══════════════════════════════════════════════════════════════
//  API calls → VPS REST
// ══════════════════════════════════════════════════════════════
async function fetchAll() {
  const res = await fetch(TXN_API, { method: 'GET', headers: HEADERS });
  if (!res.ok) throw new Error('HTTP ' + res.status);
  const j = await res.json();
  let list = [];
  if (Array.isArray(j)) list = j;
  else if (j && Array.isArray(j.data)) list = j.data;
  else if (j && Array.isArray(j.records)) list = j.records;
  return list.map(normalizeRow);
}

async function updateStatus(id, status, note) {
  let url, body;
  if (status === 'อนุมัติ') {
    url = TXN_API + '/' + id + '/approve';
    body = { remark: note || '' };
    const r = await fetch(url, { method: 'PUT', headers: HEADERS, body: JSON.stringify(body) });
    return r.ok;
  } else if (status === 'ไม่อนุมัติ') {
    url = TXN_API + '/' + id + '/reject';
    body = { remark: note || '' };
    const r = await fetch(url, { method: 'PUT', headers: HEADERS, body: JSON.stringify(body) });
    return r.ok;
  } else {
    const map = { 'รออนุมัติ': 'pending', 'กำลังทำงาน': 'active', 'อนุมัติ': 'approved', 'ไม่อนุมัติ': 'rejected' };
    url = TXN_API + '/' + id;
    body = { status: map[status] || status, approver_remark: note || '' };
    const r = await fetch(url, { method: 'PUT', headers: HEADERS, body: JSON.stringify(body) });
    return r.ok;
  }
}

async function deleteTxn(id) {
  const r = await fetch(TXN_API + '/' + id, { method: 'DELETE', headers: HEADERS });
  return r.ok;
}

function onStatusFilterChange() { updateStatusFilterStyle(); renderTable(); }
function updateStatusFilterStyle() {
  const sel = document.getElementById('statusFilter');
  sel.classList.toggle('active-filter', sel.value === '__open__');
}
function clearDateFilter() { document.getElementById('dateFilter').value = ''; renderTable(); }

let searchTimer = null;
function onSearchInput() { clearTimeout(searchTimer); searchTimer = setTimeout(renderTable, 180); }

async function loadData() {
  const body = document.getElementById('tableBody');
  const hasData = allData.length > 0;
  if (!hasData) {
    body.innerHTML = `<tr><td colspan="13"><div class="empty"><div class="spinner"></div><div class="empty-desc">กำลังโหลดข้อมูล…</div></div></td></tr>`;
  } else {
    showRefreshing(true);
  }
  try {
    const incoming = await fetchAll();
    incoming.sort((a, b) => (b.id || 0) - (a.id || 0));
    allData = incoming;
    populateSupervisorFilter();
    populateCompanyFilter();
    renderTable();
    computeSummary();
    const cg = document.getElementById('companyGate');
    if (cg && cg.style.display !== 'none') renderCompanyGate();
  } catch (err) {
    if (allData.length === 0) {
      body.innerHTML = `<tr><td colspan="13"><div class="empty">
        <div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></div>
        <div class="empty-title">โหลดไม่สำเร็จ</div>
        <div class="empty-desc">${escapeHtml(err.message || err)}<br><span style="font-size:11px;">ตรวจว่าเปิดไฟล์แบบ file:// และเชื่อมต่อ VPS ได้</span></div>
        <button class="btn" onclick="loadData()">ลองใหม่</button>
      </div></td></tr>`;
    } else {
      showToast('อัปเดตไม่สำเร็จ — แสดงข้อมูลล่าสุด', 'error');
    }
  }
  showRefreshing(false);
}

function showRefreshing(on) {
  const el = document.getElementById('refreshingBar');
  if (!el) return;
  el.style.display = on ? 'flex' : 'none';
}

function minutesBetween(startTime, endTime) {
  if (!startTime || !endTime) return 0;
  const sp = String(startTime).split(':').map(Number);
  const ep = String(endTime).split(':').map(Number);
  if (sp.length < 2 || ep.length < 2) return 0;
  let s = sp[0] * 60 + sp[1];
  let e = ep[0] * 60 + ep[1];
  if (e <= s) e += 24 * 60;
  return e - s;
}
function formatDurationMin(total) {
  if (!total || total <= 0) return '0';
  const h = Math.floor(total / 60), m = total % 60;
  if (h === 0) return m + ' นาที';
  if (m === 0) return h + ' ชม.';
  return h + ' ชม. ' + m + ' นาที';
}

const COMPANY_ABBR = {
  'Triple E Trading': '3E',
  'Triple E Innovation': '3IN',
  'Triple E Empire Group': '3EM',
  'Triple E Lighting': '3EL',
  'Hikari Denki': 'HD',
  'Eita & Paul': 'EP',
  'Triple P Factory & Eng': '3P',
  'AE&T International': 'AE&T',
};
function companyAbbr(c) {
  return COMPANY_ABBR[c] || c;
}

function computeSummary() {
  const otData = allData.filter(r => !isCleaning(r));
  let total = otData.length, working = 0, pending = 0, approved = 0;
  let pendingMin = 0, approvedMin = 0;
  otData.forEach(r => {
    const mins = minutesBetween(r.startTime, r.endTime);
    if (r.status === 'กำลังทำงาน') working++;
    else if (r.status === 'อนุมัติ') { approved++; approvedMin += mins; }
    else if (r.status === 'ไม่อนุมัติ') {}
    else { pending++; pendingMin += mins; }
  });
  document.getElementById('sumTotal').innerHTML = total + ' <span class="stat-sub">คำขอ</span>';
  document.getElementById('sumWorking').innerHTML = working + ' <span class="stat-sub">คน</span>';
  document.getElementById('sumPending').innerHTML = pending + ' <span class="stat-sub">· ' + formatDurationMin(pendingMin) + '</span>';
  document.getElementById('sumApproved').innerHTML = approved + ' <span class="stat-sub">คำขอ</span>';
  document.getElementById('sumHours').innerHTML = formatDurationMin(approvedMin);
}

const SUPERVISOR_ALIASES = [
  { match: ['ประกอบ', 'ประกอล', 'ประกอข', 'ปรกอบ', 'ประกแบ', 'ประกอช', 'ประกอบช'], name: 'ประกอบ' },
  { match: ['สุเมธ', 'สุเมฆ', 'สถเมธ', 'พิมพ์จันทร์', 'พิมจันทร์'], name: 'สุเมธ พิมพ์จันทร์' },
  { match: ['ธัญวรัตน์', 'ธัญรัตน์', 'ปานสีทา', 'ปามสีทา'], name: 'ธัญวรัตน์ ปานสีทา' },
  { match: ['สกุลชัย', 'สกลุชัย', 'จำปาแดง', 'จ_ปาแดง'], name: 'สกุลชัย จำปาแดง' },
];

function _cleanName(s) {
  return String(s || '')
    .replace(/[\u200B-\u200D\uFEFF]/g, '')
    .replace(/\s+/g, ' ')
    .trim();
}

function _canonName(s) {
  const clean = _cleanName(s);
  const low = clean.toLowerCase();
  for (const a of SUPERVISOR_ALIASES) {
    if (a.match.some(m => low.indexOf(m.toLowerCase()) !== -1)) return a.name;
  }
  return clean;
}

const EMP_ALIASES = [
  { match: ['กิตติชัย ขัติลาย', 'กิตตชัย ขัติลาย'], name: 'กิตติชัย ขัติลาย' },
  { match: ['แก้ววงษ์วาลย์', 'แก้วงษ์วาลย์'], name: 'กิตติชัย แก้ววงษ์วาลย์' },
  { match: ['วัลลภ'], name: 'วัลลภ ทับทิมทอง' },
  { match: ['ไพศาล'], name: 'ไพศาล ทับทิมทอง' },
  { match: ['ประภัสสร'], name: 'ประภัสสร ไพพินิจ' },
  { match: ['ธัญวรัตน์', 'ธัญรัตน์'], name: 'ธัญวรัตน์ ปานสีทา' },
  { match: ['ปองลา'], name: 'ปองลา คำบุญเรือง' },
  { match: ['คุณาสิน'], name: 'คุณาสิน สอนทรัพย์' },
  { match: ['สุภัค'], name: 'สุภัค วัฒนรัตน์' },
  { match: ['วัชระ'], name: 'วัชระ นระสิงห์' },
  { match: ['กฤษฎา'], name: 'กฤษฎา อินทร์ทองคุ้ม' },
  { match: ['พงศกร'], name: 'พงศกร มีศิริ' },
  { match: ['ทนงศักดิ์'], name: 'ทนงศักดิ์ วงษ์ภักดี' },
  { match: ['กรรณิการ์'], name: 'กรรณิการ์ ปลื้มใจ' },
  { match: ['เจนจิรา'], name: 'เจนจิรา พงษ์นาวิน' },
  { match: ['ธนพล ผลสอน'], name: 'ธนพล ผลสอน' },
  { match: ['ธนพล สุวอ'], name: 'ธนพล สุวอ' },
  { match: ['เขมทิน'], name: 'เขมทิน ศรีทองเกิด' },
  { match: ['สุรี'], name: 'สุรี จำปาทอง' },
  { match: ['อาทิตย์'], name: 'อาทิตย์ อำพนธ์' },
  { match: ['ทิว'], name: 'ณัฐวัตร ทิว' },
  { match: ['ณัฐวัตร', 'ณัฐัวตร', 'ณัฐวัต'], name: 'ณัฐวัตร ชูช่วย' },
  { match: ['บิลลี้'], name: 'บิลลี้ พรหมจักร' },
  { match: ['ธนพล'], name: 'ธนพล สุวอ' },
];
const NICK_GROUPS = [
  { match: ['เวิล', 'เวิน', 'เวือ', 'เวอู', 'แวอู', 'เวืน'], name: 'เวิน' },
  { match: ['ตาโซ', 'ตาโ'], name: 'ตาโซ' },
  { match: ['ซูซู', 'ซูซุ', 'ซูๆ', 'ซู'], name: 'ซูซู' },
  { match: ['ซานดา', 'ซานด', 'ซานดม'], name: 'ซานดา' },
  { match: ['โซดา', 'โซด', 'โซาด'], name: 'โซดา' },
  { match: ['เอล่วน', 'เอล่ว', 'เอล่ววน'], name: 'เอล่วน' },
  { match: ['คินมาร์', 'คินมา', 'คิมมาร์', 'คิมมา', 'คิมมา'], name: 'คินมาร์' },
  { match: ['คีม', 'คิม', 'คืม'], name: 'คีม' },
  { match: ['อากาก'], name: 'อากาก' },
  { match: ['เลออง', 'เลออ', 'เรอง', 'เรออง', 'เลอง'], name: 'เลออง' },
  { match: ['มิวมิว', 'มิวๆ', 'มิว'], name: 'มิวมิว' },
  { match: ['พิวเวอาว', 'พิวเวออง', 'พิววีเอา', 'พิวเวอ'], name: 'พิววีเอา' },
  { match: ['ซานเลออ', 'เลออ'], name: 'ซานเลออ' },
  { match: ['เอามิวตู'], name: 'เอามิวตู' },
];

function _canonEmp(s) {
  const clean = _cleanName(s);
  const low = clean.toLowerCase();
  for (const a of EMP_ALIASES) {
    if (a.match.some(m => low.indexOf(m.toLowerCase()) !== -1)) return a.name;
  }
  const core = low.replace(/[()\s.]/g, '')
    .replace(/ท่[าสน]?จี[นย]?/g, '')
    .replace(/มศ?ว/g, '');
  if (core.length > 0) {
    for (const g of NICK_GROUPS) {
      if (g.match.some(m => core.indexOf(m.toLowerCase()) === 0)) {
        return g.name;
      }
    }
  }
  return clean;
}

function _normName(s) {
  return _canonName(s).toLowerCase();
}

function populateSupervisorFilter() {
  const sel = document.getElementById('supervisorFilter');
  const current = sel.value;
  const companyF = (document.getElementById('companyFilter').value || '').trim();
  const source = companyF
    ? allData.filter(r => (r.company || '').trim() === companyF)
    : allData;
  const set = new Set();
  source.forEach(r => {
    const canon = _canonName(r.supervisor);
    if (canon) set.add(canon);
  });
  const names = Array.from(set).sort((a, b) => a.localeCompare(b, 'th'));
  sel.innerHTML = '<option value="">ทุกหัวหน้า</option>' +
    names.map(n => `<option value="${escapeHtml(n)}">${escapeHtml(n)}</option>`).join('');
  if (current && names.indexOf(current) !== -1) sel.value = current;
}
function populateCompanyFilter() {
  const sel = document.getElementById('companyFilter');
  const current = sel.value;
  const order = ['Triple E Empire Group', 'Triple E Trading', 'Triple E Innovation', 'Triple E Lighting', 'AE&T International', 'Eita & Paul', 'Triple P Factory & Eng', 'Hikari Denki', 'Chavest'];
  const fromData = Array.from(new Set(allData.map(r => (r.company || '').trim()).filter(n => n.length > 0)));
  const ordered = [];
  order.forEach(c => { if (fromData.indexOf(c) !== -1) ordered.push(c); });
  fromData.forEach(c => { if (ordered.indexOf(c) === -1) ordered.push(c); });
  sel.innerHTML = '<option value="">ทุกบริษัท</option>' +
    ordered.map(n => `<option value="${escapeHtml(n)}">${escapeHtml(n)}</option>`).join('');
  if (current && ordered.indexOf(current) !== -1) sel.value = current;
}

function onCompanyFilterChange() {
  const sel = document.getElementById('companyFilter');
  sel.classList.toggle('active-filter', sel.value !== '');
  populateSupervisorFilter();
  renderTable();
}

function getOrderedCompanies() {
  const order = ['Triple E Empire Group', 'Triple E Trading', 'Triple E Innovation', 'Triple E Lighting', 'AE&T International', 'Eita & Paul', 'Triple P Factory & Eng', 'Hikari Denki', 'Chavest'];
  const fromData = Array.from(new Set(allData.map(r => (r.company || '').trim()).filter(n => n.length > 0)));
  const ordered = [];
  order.forEach(c => { if (fromData.indexOf(c) !== -1) ordered.push(c); });
  fromData.forEach(c => { if (ordered.indexOf(c) === -1) ordered.push(c); });
  return ordered;
}

function isAllowanceCompany(r) {
  return String(r.company || '').trim().toLowerCase() === 'triple e innovation';
}
function isFieldAllowance(r) {
  const a = String(r.allowance || '');
  const w = String(r.workDetail || '');
  return a.indexOf('ออกหน้างาน') !== -1 || w.indexOf('ออกหน้างาน') !== -1;
}
function allowanceAmount(r) {
  if (!isAllowanceCompany(r)) return 0;
  const raw = String(r.allowance == null ? '' : r.allowance).trim();
  if (raw !== '' && !isNaN(Number(raw))) return Number(raw);
  if (isFieldAllowance(r)) return 80;
  if (raw.indexOf('รับเบี้ยเลี้ยง') !== -1) return 80;
  if (raw.indexOf('ไม่ได้รับ') !== -1) return 0;
  return minutesBetween(r.startTime, r.endTime) >= 180 ? 80 : 0;
}
function hasAllowance(r) {
  return allowanceAmount(r) > 0;
}

function renderTable() {
  const search = document.getElementById('searchInput').value.toLowerCase().trim();
  const statusF = document.getElementById('statusFilter').value;
  const supervisorF = document.getElementById('supervisorFilter').value;
  const companyF = document.getElementById('companyFilter').value;
  const allowanceF = document.getElementById('allowanceFilter').value;
  const dateF = document.getElementById('dateFilter').value;
  document.getElementById('clearDateBtn').style.display = dateF ? 'inline-flex' : 'none';

  let filtered = allData.filter(r => {
    if (search) {
      const hay = (r.requestId + ' ' + r.employeeName + ' ' + (r.supervisor || '') + ' ' + (r.company || '') + ' ' + r.workDetail + ' ' + r.department).toLowerCase();
      if (!hay.includes(search)) return false;
    }
    if (statusF === '__open__') {
      if (r.status !== 'รออนุมัติ' && r.status !== 'กำลังทำงาน') return false;
    } else if (statusF && r.status !== statusF) {
      return false;
    }
    if (supervisorF && _normName(r.supervisor) !== _normName(supervisorF)) return false;
    if (companyF && (r.company || '').trim() !== companyF) return false;
    if (cleaningOnly && !isCleaning(r)) return false;
    if (!cleaningOnly && isCleaning(r)) return false;
    if (allowanceF === 'yes' && !hasAllowance(r)) return false;
    if (dateF) {
      const target = new Date(dateF);
      const ts = new Date(target.getFullYear(), target.getMonth(), target.getDate()).getTime();
      const te = ts + 86400000 - 1;
      if (cleaningOnly) {
        // ★ FIX: โหมดทำความสะอาด — กรองด้วยวันที่ "ส่งข้อมูลจริง" (record_date, เวลาไทย)
        //   ไม่ใช่ ot_date เพราะงานแม่บ้านเช็คอินตามเวลาจริง ไม่ได้กรอกวัน OT เอง
        if (!r.recordDate) return false;
        const rd = new Date(r.recordDate.getUTCFullYear(), r.recordDate.getUTCMonth(), r.recordDate.getUTCDate()).getTime();
        if (rd < ts || rd > te) return false;
      } else {
        const otDate = parseDDMMYYYY(r.otDate);
        if (otDate < ts || otDate > te) return false;
      }
    }
    return true;
  });

  document.getElementById('recordCount').textContent = filtered.length + ' รายการ';

  const showAllowance = String(companyF || '').trim().toLowerCase() === 'triple e innovation';
  const otTable = document.getElementById('otTable');
  if (otTable) {
    otTable.classList.toggle('hide-allowance', !showAllowance);
    otTable.classList.toggle('cleaning-mode', cleaningOnly);
  }
  const allowFilterEl = document.getElementById('allowanceFilter');
  if (allowFilterEl) {
    allowFilterEl.style.display = (showAllowance && !cleaningOnly) ? '' : 'none';
    if (!showAllowance) allowFilterEl.value = '';
  }
  const supervisorFilterEl = document.getElementById('supervisorFilter');
  if (supervisorFilterEl) supervisorFilterEl.style.display = cleaningOnly ? 'none' : '';

  const otTableWrap = document.getElementById('otTableWrap');
  const cleaningGridEl = document.getElementById('cleaningGrid');
  const statsBarEl = document.getElementById('statsBar');
  if (cleaningOnly) {
    if (otTableWrap) otTableWrap.style.display = 'none';
    if (cleaningGridEl) cleaningGridEl.style.display = 'grid';
    if (statsBarEl) statsBarEl.style.display = 'none';
    renderCleaningGrid(filtered, search);
    return;
  } else {
    if (otTableWrap) otTableWrap.style.display = '';
    if (cleaningGridEl) cleaningGridEl.style.display = 'none';
    if (statsBarEl) statsBarEl.style.display = '';
  }

  const body = document.getElementById('tableBody');

  if (filtered.length === 0) {
    if (statusF === '__open__') {
      body.innerHTML = `<tr><td colspan="13"><div class="empty">
        <div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
        <div class="empty-title">ไม่มีงานค้างอยู่</div>
        <div class="empty-desc">ทุกคำขอได้รับการดำเนินการแล้ว</div>
        <a href="#" onclick="document.getElementById('statusFilter').value=''; onStatusFilterChange(); return false;">ดูทั้งหมด →</a>
      </div></td></tr>`;
    } else {
      body.innerHTML = `<tr><td colspan="13"><div class="empty">
        <div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg></div>
        <div class="empty-title">ไม่พบข้อมูล</div>
        <div class="empty-desc">ลองปรับตัวกรอง หรือล้างคำค้นหา</div>
      </div></td></tr>`;
    }
    return;
  }

  body.innerHTML = filtered.map(r => {
    const badgeClass = r.status === 'อนุมัติ' ? 'b-approved'
                    : r.status === 'ไม่อนุมัติ' ? 'b-rejected'
                    : r.status === 'กำลังทำงาน' ? 'b-working'
                    : 'b-pending';
    const hasIn = !!(r.selfieIn || r.mapIn);
    const hasOut = !!(r.selfieOut || r.mapOut);
    const hasExtra = r.extraPhotos && r.extraPhotos.length > 0;
    let viewBtn;
    if (!hasIn && !hasOut && !hasExtra) {
      viewBtn = '<span class="dim">—</span>';
    } else {
      const dots = (hasIn ? '<span class="dot dot-in"></span>' : '') + (hasOut ? '<span class="dot dot-out"></span>' : '');
      const extraLabel = hasExtra ? ' <span style="font-size:11px;color:var(--ink-3);">+' + r.extraPhotos.length + '</span>' : '';
      viewBtn = `<button class="btn-view" onclick="openPhotoModalById(${r.id})">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><polyline points="21 15 16 10 5 21"/></svg>
        <span>ดูรูป</span><span class="view-dots">${dots}</span>${extraLabel}</button>`;
    }

    const isPending = r.status === 'รออนุมัติ';
    const isWorking = r.status === 'กำลังทำงาน';
    let timeRange;
    if (isWorking) {
      timeRange = `<b>${escapeHtml(r.startTime)}</b><span class="arr">→</span><span style="color:var(--blue);">●</span>`;
    } else if (r.startTime && r.endTime) {
      timeRange = `<b>${escapeHtml(r.startTime)}</b><span class="arr">→</span><b>${escapeHtml(r.endTime)}</b>`;
    } else { timeRange = '<span class="dim">—</span>'; }

    let actions = '';
    if (isPending) {
      actions = `
        <button class="icon-btn approve" title="อนุมัติ" onclick="openActionById(${r.id}, 'อนุมัติ')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></button>
        <button class="icon-btn reject" title="ไม่อนุมัติ" onclick="openActionById(${r.id}, 'ไม่อนุมัติ')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>`;
    } else if (!isWorking) {
      actions = `<button class="icon-btn" title="เปลี่ยนสถานะ" onclick="openActionById(${r.id}, 'เปลี่ยน')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>`;
    }
    actions += `<button class="icon-btn delete" title="ลบ" onclick="deleteRow(${r.id})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-2 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg></button>`;

    const supCell = r.supervisor ? escapeHtml(r.supervisor) : '<span class="dim">—</span>';
    const compCell = r.company ? `<span class="company-tag" title="${escapeHtml(r.company)}">${escapeHtml(companyAbbr(r.company))}</span>` : '<span class="dim">—</span>';
    let allowCell;
    if (hasAllowance(r)) {
      const amt = allowanceAmount(r);
      allowCell = amt > 0
        ? `<span class="allowance-tag">${isFieldAllowance(r) ? '💰 +80 · ออกหน้างาน' : '💰 +80'}</span>`
        : `<span class="allowance-tag zero">+0</span>`;
    } else { allowCell = '<span class="dim">—</span>'; }

    return `<tr>
      <td class="mono hide-md">${escapeHtml(r.submittedAt)}</td>
      <td>${escapeHtml(r.otDate)}</td>
      <td class="col-name">${escapeHtml(r.employeeName || '—')}</td>
      <td class="col-sup hide-sm col-sup-cell">${supCell}</td>
      <td class="col-comp-cell">${compCell}</td>
      <td class="col-time">${timeRange}</td>
      <td class="col-hours">${escapeHtml(r.hours || '—')}</td>
      <td class="col-detail hide-sm"><div class="clip" title="${escapeHtml(r.workDetail || '')}">${escapeHtml(r.workDetail || '—')}</div></td>
      <td class="col-allowance">${allowCell}</td>
      <td>${viewBtn}</td>
      <td><span class="badge ${badgeClass}">${escapeHtml(r.status)}</span></td>
      <td class="hide-md" style="color:var(--ink-2);font-size:12px;max-width:160px;">${escapeHtml(r.note || '—')}</td>
      <td><div class="row-actions">${actions}</div></td>
    </tr>`;
  }).join('');
}

function parseDDMMYYYY(s) {
  if (!s) return 0;
  const parts = s.split('/');
  if (parts.length !== 3) return 0;
  return new Date(+parts[2], +parts[1]-1, +parts[0]).getTime();
}
function findRecord(id) { return allData.find(r => r.id === id); }

function openActionById(id, status) {
  const record = findRecord(id);
  if (!record) { showToast('ไม่พบข้อมูลแถวนี้', 'error'); return; }
  openAction(id, status, record);
}
function openPhotoModalById(id) {
  const record = findRecord(id);
  if (!record) { showToast('ไม่พบข้อมูลแถวนี้', 'error'); return; }
  if (!record.extraPhotos || record.extraPhotos.length === 0) {
    fetch(TXN_API + '/' + id, { method: 'GET', headers: HEADERS })
      .then(r => r.ok ? r.json() : null)
      .then(j => {
        if (!j) { openPhotoModal(record); return; }
        const raw = j.data || j;
        const ep = raw.extra_photos || raw.extraPhotos;
        if (ep) {
          record.extraPhotos = _parseExtraPhotos(ep);
        }
        openPhotoModal(record);
      })
      .catch(() => openPhotoModal(record));
  } else {
    openPhotoModal(record);
  }
}

function openAction(id, status, record) {
  currentAction = { id, status, record };
  const titleEl = document.getElementById('actionTitle');
  const confirmBtn = document.getElementById('confirmBtn');
  if (status === 'อนุมัติ') {
    titleEl.textContent = 'อนุมัติคำขอ'; confirmBtn.textContent = 'อนุมัติ'; confirmBtn.className = 'btn btn-primary';
  } else if (status === 'ไม่อนุมัติ') {
    titleEl.textContent = 'ไม่อนุมัติคำขอ'; confirmBtn.textContent = 'ไม่อนุมัติ'; confirmBtn.className = 'btn btn-danger';
  } else {
    titleEl.textContent = 'เปลี่ยนสถานะ'; confirmBtn.textContent = 'บันทึก'; confirmBtn.className = 'btn btn-primary';
    const choice = prompt('เปลี่ยนสถานะเป็น:\n1 = รออนุมัติ\n2 = อนุมัติ\n3 = ไม่อนุมัติ', '1');
    if (!choice) { currentAction = null; return; }
    const map = { '1': 'รออนุมัติ', '2': 'อนุมัติ', '3': 'ไม่อนุมัติ' };
    if (!map[choice]) { showToast('ตัวเลือกไม่ถูกต้อง', 'error'); currentAction = null; return; }
    currentAction.status = map[choice];
  }
  document.getElementById('actionReqId').textContent = record.requestId;
  document.getElementById('actionName').textContent = record.employeeName;
  document.getElementById('actionSupervisor').textContent = record.supervisor || '—';
  document.getElementById('actionCompany').textContent = record.company || '—';
  document.getElementById('actionAllowance').textContent =
    hasAllowance(record) ? (allowanceAmount(record) > 0
      ? (isFieldAllowance(record) ? '💰 +80 บาท (ออกหน้างาน)' : '💰 +80 บาท') : '+0 (ไม่ถึง 3 ชม.)') : '—';
  document.getElementById('actionTime').textContent =
    (record.startTime && record.endTime) ? (record.startTime + ' → ' + record.endTime) : '—';
  document.getElementById('actionHours').textContent = record.hours || '—';
  document.getElementById('actionDate').textContent = record.otDate;
  document.getElementById('noteInput').value = record.note || '';
  document.getElementById('actionModal').classList.add('show');
  document.getElementById('noteInput').focus();
}
function closeModal() { document.getElementById('actionModal').classList.remove('show'); currentAction = null; }

async function confirmAction() {
  if (!currentAction) return;
  const note = document.getElementById('noteInput').value.trim();
  const btn = document.getElementById('confirmBtn');
  const original = btn.textContent;
  btn.disabled = true; btn.textContent = 'กำลังบันทึก…';
  try {
    const ok = await updateStatus(currentAction.id, currentAction.status, note);
    btn.disabled = false; btn.textContent = original;
    if (ok) {
      const row = allData.find(r => r.id === currentAction.id);
      if (row) { row.status = currentAction.status; row.note = note; }
      closeModal();
      showToast('บันทึกสำเร็จ', 'success');
      renderTable(); computeSummary();
    } else { showToast('บันทึกไม่สำเร็จ', 'error'); }
  } catch (err) {
    btn.disabled = false; btn.textContent = original;
    showToast(err.message, 'error');
  }
}

async function deleteRow(id) {
  const r = findRecord(id);
  if (!confirm('ลบคำขอ ' + (r ? r.requestId : id) + ' ?')) return;
  try {
    const ok = await deleteTxn(id);
    if (ok) {
      showToast('ลบสำเร็จ', 'success');
      allData = allData.filter(x => x.id !== id);
      renderTable(); computeSummary();
    } else { showToast('ลบไม่สำเร็จ', 'error'); }
  } catch (err) { showToast(err.message, 'error'); }
}

function showImage(url) {
  const modal = document.getElementById('imageModal');
  const img = document.getElementById('modalImg');
  img.classList.remove('zoomed'); img.src = url;
  modal.classList.add('show'); document.body.style.overflow = 'hidden';
}
function closeImageModal() {
  const modal = document.getElementById('imageModal');
  const img = document.getElementById('modalImg');
  modal.classList.remove('show'); img.classList.remove('zoomed');
  document.body.style.overflow = '';
}
function toggleZoom(img) { img.classList.toggle('zoomed'); }
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    const lightbox = document.getElementById('imageModal');
    if (lightbox && lightbox.classList.contains('show')) closeImageModal();
  }
});

function openPhotoModal(record) {
  const grid = document.getElementById('photoGrid');
  document.getElementById('photoModalSub').textContent =
    ' · ' + (record.employeeName || '') + ' · ' + (record.otDate || '');

  function buildBlock(kind, label, selfie, mapUrl, gps, photoTime) {
    const photoHtml = selfie
      ? `<img class="photo-block-img" src="${escapeHtml(selfie)}"
              onclick="showImage('${escapeJs(selfie)}')"
              onerror="this.style.display='none';this.nextElementSibling.style.display='flex';" alt="${escapeHtml(label)}">
         <div class="photo-block-empty" style="display:none;">
           <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><polyline points="21 15 16 10 5 21"/></svg>
           <div>โหลดรูปไม่ได้</div></div>`
      : `<div class="photo-block-empty">
           <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><polyline points="21 15 16 10 5 21"/></svg>
           <div>ยังไม่มีรูป</div></div>`;

    const timeRow = photoTime ? `<div class="row"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>${escapeHtml(photoTime)}</span></div>` : '';

    return `<div class="photo-block ${kind}">
      <div class="photo-block-header"><span class="marker"></span>${label}</div>
      ${photoHtml}
      <div class="photo-block-footer">${timeRow}</div></div>`;
  }

  grid.innerHTML =
    buildBlock('in', 'เริ่มงาน', record.selfieIn, record.mapIn, record.gpsIn, record.photoTimeIn) +
    buildBlock('out', 'เลิกงาน', record.selfieOut, record.mapOut, record.gpsOut, record.photoTimeOut);

  const extras = record.extraPhotos || [];
  const detail = (record.workDetail || '').trim();

  if (detail || extras.length > 0) {
    let workHtml = `<div style="grid-column:1/-1;border-top:1px solid var(--line);padding-top:16px;margin-top:6px;">
      <div style="font-size:13px;font-weight:700;color:var(--ink);margin-bottom:12px;display:flex;align-items:center;gap:7px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        งานที่ทำ
      </div>`;

    if (detail) {
      workHtml += `<div style="padding:12px 14px;background:#f9fafb;border:1px solid var(--line);border-radius:8px;font-size:13px;color:var(--ink);line-height:1.6;margin-bottom:${extras.length > 0 ? '14' : '0'}px;white-space:pre-wrap;">${escapeHtml(detail)}</div>`;
    }

    if (extras.length > 0) {
      workHtml += `<div style="font-size:12px;font-weight:600;color:var(--ink-3);margin-bottom:8px;display:flex;align-items:center;gap:6px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><polyline points="21 15 16 10 5 21"/></svg>
        รูปประกอบ ${extras.length} รูป <span style="color:var(--ink-4);">· กดเพื่อดูขยาย</span>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:8px;">`;
      extras.forEach((url, i) => {
        workHtml += `<div style="border:1px solid var(--line);border-radius:8px;overflow:hidden;background:white;">
          <img src="${escapeHtml(url)}"
               style="width:100%;aspect-ratio:1;object-fit:cover;display:block;cursor:zoom-in;background:#f5f5f5;"
               onclick="showImage('${escapeJs(url)}')"
               onerror="this.outerHTML='<div style=\\'width:100%;aspect-ratio:1;display:flex;align-items:center;justify-content:center;color:var(--ink-4);font-size:12px;flex-direction:column;gap:6px;background:#fafafa;\\'><svg width=\\'20\\' height=\\'20\\' viewBox=\\'0 0 24 24\\' fill=\\'none\\' stroke=\\'currentColor\\' stroke-width=\\'2\\' stroke-linecap=\\'round\\' stroke-linejoin=\\'round\\'><rect x=\\'3\\' y=\\'3\\' width=\\'18\\' height=\\'18\\' rx=\\'2\\' ry=\\'2\\'/><circle cx=\\'9\\' cy=\\'9\\' r=\\'2\\'/><polyline points=\\'21 15 16 10 5 21\\'/></svg><div>โหลดไม่ได้</div></div>'"
               alt="รูปประกอบ ${i + 1}">
          <div style="padding:5px 8px;font-size:11px;color:var(--ink-3);text-align:center;border-top:1px solid var(--line);">รูปที่ ${i + 1}</div>
        </div>`;
      });
      workHtml += '</div>';
    }

    workHtml += '</div>';
    grid.innerHTML += workHtml;
  }

  document.getElementById('photoInfoModal').classList.add('show');
}
function closePhotoModal() { document.getElementById('photoInfoModal').classList.remove('show'); }

function showToast(msg, type) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast show ' + (type || '');
  setTimeout(() => t.className = 'toast ' + (type || ''), 2500);
}

function exportCSV() {
  if (!allData.length) { showToast('ไม่มีข้อมูล', 'error'); return; }
  const now = new Date();
  const firstOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
  document.getElementById('expDateFrom').value = firstOfMonth.toISOString().slice(0, 10);
  document.getElementById('expDateTo').value = now.toISOString().slice(0, 10);

  const compSel = document.getElementById('expCompany');
  const order = ['Triple E Empire Group', 'Triple E Trading', 'Triple E Innovation', 'Triple E Lighting', 'AE&T International', 'Eita & Paul', 'Triple P Factory & Eng', 'Hikari Denki', 'Chavest'];
  const fromData = Array.from(new Set(allData.map(r => (r.company || '').trim()).filter(n => n)));
  const ordered = [];
  order.forEach(c => { if (fromData.indexOf(c) !== -1) ordered.push(c); });
  fromData.forEach(c => { if (ordered.indexOf(c) === -1) ordered.push(c); });
  compSel.innerHTML = '<option value="">ทุกบริษัท</option>' + ordered.map(n => '<option value="' + escapeHtml(n) + '">' + escapeHtml(n) + '</option>').join('');

  const currentCompany = (document.getElementById('companyFilter').value || '').trim();
  if (cleaningOnly) {
    compSel.value = '';
    compSel.disabled = true;
  } else if (currentCompany) {
    compSel.value = currentCompany;
    compSel.disabled = true;
  } else {
    compSel.disabled = false;
  }

  populateExpEmployees();
  document.getElementById('expStatus').value = '';
  updateExportCount();
  document.getElementById('exportModal').classList.add('show');
}

function populateExpEmployees() {
  const sel = document.getElementById('expEmployee');
  const cur = sel.value;
  const comp = (document.getElementById('expCompany').value || '').trim();
  let source = allData;
  if (cleaningOnly) source = allData.filter(r => isCleaning(r));
  else if (comp) source = allData.filter(r => (r.company || '').trim() === comp);
  const set = new Set();
  source.forEach(r => { const c = _canonEmp(r.employeeName); if (c) set.add(c); });
  const names = Array.from(set).sort((a, b) => a.localeCompare(b, 'th'));
  sel.innerHTML = '<option value="">ทุกคน</option>' +
    names.map(n => `<option value="${escapeHtml(n)}">${escapeHtml(n)}</option>`).join('');
  if (cur && names.indexOf(cur) !== -1) sel.value = cur;
}

function onExpCompanyChange() {
  populateExpEmployees();
  updateExportCount();
}
function clearExportDates() { document.getElementById('expDateFrom').value = ''; document.getElementById('expDateTo').value = ''; updateExportCount(); }
function closeExportModal() { document.getElementById('exportModal').classList.remove('show'); }
function getExportFiltered() {
  const fromVal = document.getElementById('expDateFrom').value;
  const toVal = document.getElementById('expDateTo').value;
  const compVal = document.getElementById('expCompany').value;
  const empVal = (document.getElementById('expEmployee').value || '').trim();
  const statusVal = document.getElementById('expStatus').value;
  let fromTs = null, toTs = null;
  if (fromVal) fromTs = new Date(fromVal + 'T00:00:00').getTime();
  if (toVal) toTs = new Date(toVal + 'T23:59:59.999').getTime();
  return allData.filter(r => {
    if (cleaningOnly && !isCleaning(r)) return false;
    if (!cleaningOnly && isCleaning(r)) return false;
    if (compVal && (r.company || '').trim() !== compVal) return false;
    if (empVal && _canonEmp(r.employeeName) !== empVal) return false;
    if (statusVal && r.status !== statusVal) return false;
    if (fromTs || toTs) {
      const d = parseDDMMYYYY(r.otDate);
      if (!d) return false;
      if (fromTs && d < fromTs) return false;
      if (toTs && d > toTs) return false;
    }
    return true;
  });
}
function updateExportCount() {
  const n = getExportFiltered().length;
  document.getElementById('exportCount').textContent = n;
  const btn = document.querySelector('#exportModal .btn-primary');
  if (btn) btn.disabled = (n === 0);
}
function confirmExport() {
  const filtered = getExportFiltered();
  if (!filtered.length) { showToast('ไม่มีข้อมูลตามที่เลือก', 'error'); return; }
  const headers = ['รหัสคำขอ','ส่งเมื่อ','วันที่ OT','ชื่อ','หัวหน้า','บริษัท','แผนก','เบี้ยเลี้ยง','ยอดเบี้ยเลี้ยง','เวลาเริ่ม','เวลาเลิก','เวลารวม','งานที่ทำ','สถานะ','หมายเหตุ'];
  const rows = filtered.map(r => [
    r.requestId, r.submittedAt, r.otDate, r.employeeName, r.supervisor || '', r.company || '', r.department,
    (r.allowance || ''), (hasAllowance(r) ? '+' + allowanceAmount(r) : ''),
    r.startTime, r.endTime, r.hours, r.workDetail, r.status, r.note
  ]);
  const csv = [headers, ...rows].map(row => row.map(cell => '"' + String(cell || '').replace(/"/g, '""') + '"').join(',')).join('\n');
  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const fromVal = document.getElementById('expDateFrom').value;
  const toVal = document.getElementById('expDateTo').value;
  const compVal = document.getElementById('expCompany').value;
  const empVal = (document.getElementById('expEmployee').value || '').trim();
  let fname = 'ot';
  if (cleaningOnly) fname += '_ทำความสะอาด';
  else if (compVal) fname += '_' + compVal.replace(/[^\w]/g, '');
  if (empVal) fname += '_' + empVal.replace(/\s+/g, '');
  if (fromVal || toVal) fname += '_' + (fromVal || 'start') + '_ถึง_' + (toVal || 'end');
  else fname += '_ทั้งหมด';
  const a = document.createElement('a');
  a.href = url; a.download = fname + '.csv'; a.click();
  URL.revokeObjectURL(url);
  closeExportModal();
  showToast('ดาวน์โหลด ' + filtered.length + ' รายการแล้ว', 'success');
}

function escapeHtml(s) {
  if (s === null || s === undefined) return '';
  return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]);
}
function escapeJs(s) { return String(s || '').replace(/['"\\]/g, '\\$&'); }

let _refreshTimer = null;

function renderCompanyGate() {
  const grid = document.getElementById('companyGrid');
  const ordered = getOrderedCompanies();
  const counts = {};
  allData.forEach(r => { const c = (r.company || '').trim(); if (c) counts[c] = (counts[c] || 0) + 1; });
  let html = '';
  html += `<div class="company-tile emp-tile" onclick="openEmpFromGate()">`
    + `<div class="ct-icon" style="background:#1e293b;color:white;">`
    + `<svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`
    + `</div>`
    + `<div class="ct-name">พนักงาน</div>`
    + `<div class="ct-count">จัดการรายชื่อ</div></div>`;
  const cleaningCount = allData.filter(r => isCleaning(r)).length;
  html += `<div class="company-tile" onclick="enterCleaning()">`
    + `<div class="ct-icon" style="background:#0e7490;color:white;">`
    + `<svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V10l7-7 7 7v11"/><path d="M9 21v-6h6v6"/></svg>`
    + `</div>`
    + `<div class="ct-name">ทำความสะอาด</div>`
    + `<div class="ct-count">${cleaningCount} คำขอ</div></div>`;
  html += `<div class="company-tile all-tile" onclick="enterCompany('')">`
    + `<div class="ct-icon">ALL</div>`
    + `<div class="ct-name">ทุกบริษัท</div>`
    + `<div class="ct-count">${allData.length} คำขอ</div></div>`;
  ordered.forEach(c => {
    const initials = c.replace(/[^A-Za-z0-9ก-๙]/g, '').substring(0, 2).toUpperCase() || 'OT';
    const logo = companyLogo(c);
    const iconHtml = logo
      ? `<img class="ct-logo" src="${logo}" alt="" onerror="this.outerHTML='<div class=\\'ct-icon\\'>${escapeJs(initials)}</div>'">`
      : `<div class="ct-icon">${escapeHtml(initials)}</div>`;
    html += `<div class="company-tile" onclick="enterCompany('${escapeJs(c)}')">`
      + iconHtml
      + `<div class="ct-name">${escapeHtml(c)}</div>`
      + `<div class="ct-count">${counts[c] || 0} คำขอ</div></div>`;
  });
  grid.innerHTML = html;
}

const COMPANY_LOGOS = {
  'triple e innovation': '1uk2c1B7OUEsDROo80r87vOtr8NcuAl4g',
  'triple e trading': '1qruaZSyb6gXrJ1Bc_l-p50LdZ6mszbE0',
  'triple e empire group': '1suv0dKh7_FIPamBvZLv-Iza0T9nkcYNo',
  'ae&t international': '1HGo9tq8McRab8Zej2ydhDDFoz5WwCuZZ',
  'chavest': '162qxrHZ9n9K4HbYbzUjRCBQq-VYpk6Pn',
};
function companyLogo(company) {
  const id = COMPANY_LOGOS[String(company || '').trim().toLowerCase()];
  return id ? (IMG_PROXY + id) : null;
}

const CLEANING_ZONE_SETS = {
  paMoo: ['ชั้นล่าง ฝั่งเฮีย Top', 'ห้องน้ำหลัง Store ห้องที่ 1', 'ห้องน้ำหลัง Store ห้องที่ 2', 'รอบโกดัง', 'โรงจอดรถ'],
  floor234: ['ชั้น 2', 'ชั้น 3', 'ชั้น 4', 'ห้องน้ำ ชั้น 2', 'ห้องน้ำ ชั้น 3', 'ห้องน้ำ ชั้น 4', 'ตึกใหม่ ชั้นล่าง'],
  floor1: ['ชั้น 1 ตึก', 'ห้องน้ำ ชั้น 1 ห้องที่ 1', 'ห้องน้ำ ชั้น 1 ห้องที่ 2'],
  defaultZones: ['ล็อบบี้', 'ห้องประชุม', 'ห้องน้ำ ชั้น 1', 'ห้องน้ำ ชั้น 2', 'โรงอาหาร', 'สำนักงาน', 'ลานจอดรถ', 'พื้นที่ภายนอก'],
};
function zonesForEmployee(e) {
  const empId = String(e.empCode || '').trim();
  const all = String(e.fullname || '').trim().toLowerCase();
  if (empId.indexOf('59044') !== -1 || all.indexOf('เพียงใจ') !== -1 || all.indexOf('ปราบวงษา') !== -1 || all.indexOf('หมู') !== -1) {
    return CLEANING_ZONE_SETS.paMoo;
  }
  if (empId.indexOf('69008') !== -1 || all.indexOf('vanda') !== -1 || all.indexOf('วันดา') !== -1) {
    return CLEANING_ZONE_SETS.floor234;
  }
  if (empId.indexOf('57004') !== -1 || all.indexOf('วิรดา') !== -1 || all.indexOf('สนธิ') !== -1 || all.indexOf('อ้อย') !== -1) {
    return CLEANING_ZONE_SETS.floor1;
  }
  return CLEANING_ZONE_SETS.defaultZones;
}

function renderCleaningGrid(filtered, search) {
  const grid = document.getElementById('cleaningGrid');
  if (!grid) return;

  let staff = empData.filter(e => String(e.position || '').indexOf('แม่บ้าน') !== -1);
  if (search) {
    staff = staff.filter(e => (e.empCode + ' ' + e.fullname).toLowerCase().includes(search));
  }
  staff = staff.slice().sort((a, b) => String(a.fullname || '').localeCompare(String(b.fullname || ''), 'th'));

  if (staff.length === 0) {
    grid.innerHTML = `<div class="empty" style="grid-column:1/-1;">
      <div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg></div>
      <div class="empty-title">ไม่พบพนักงานแม่บ้าน</div>
      <div class="empty-desc">ยังไม่มีพนักงานที่ตั้งตำแหน่งเป็น "พนักงานแม่บ้าน" ในทะเบียนพนักงาน</div>
    </div>`;
    return;
  }

  grid.innerHTML = staff.map(e => {
    const canon = _canonEmp(e.fullname);
    const rows = filtered
      .filter(r => _canonEmp(r.employeeName) === canon)
      .sort((a, b) => (b.id || 0) - (a.id || 0));
    const initials = String(e.fullname || '?').trim().charAt(0) || '?';

    const zoneNames = zonesForEmployee(e);
    const doneCount = zoneNames.filter(zn => rows.some(r => (r.workDetail || '').trim() === zn)).length;

    const rowsHtml = zoneNames.map(zn => {
      const match = rows.find(r => (r.workDetail || '').trim() === zn);
      if (!match) {
        return `<div class="ce-row">
          <span class="ce-zone">${escapeHtml(zn)}</span>
          <div class="ce-spacer"></div>
          <span class="badge b-pending">ยังไม่ได้ทำ</span>
        </div>`;
      }
      let actions = '';
      const hasIn = !!(match.selfieIn || match.mapIn);
      const hasOut = !!(match.selfieOut || match.mapOut);
      const hasExtra = match.extraPhotos && match.extraPhotos.length > 0;
      if (hasIn || hasOut || hasExtra) {
        actions += `<button class="icon-btn" style="width:22px;height:22px;" title="ดูรูป" onclick="openPhotoModalById(${match.id})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><polyline points="21 15 16 10 5 21"/></svg></button>`;
      }
      actions += `<button class="icon-btn delete" style="width:22px;height:22px;" title="ลบ" onclick="deleteRow(${match.id})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-2 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg></button>`;

      return `<div class="ce-row">
        <span class="ce-zone">${escapeHtml(zn)}</span>
        <div class="ce-spacer"></div>
        <span class="badge b-approved">✓ ทำแล้ว</span>
        <div class="ce-actions">${actions}</div>
      </div>`;
    }).join('');

    return `<div class="ce-card">
      <div class="ce-card-head">
        <div class="ce-avatar">${escapeHtml(initials)}</div>
        <div>
          <div class="ce-name">${escapeHtml(e.fullname || '—')}</div>
          <div class="ce-code">${escapeHtml(e.empCode || '')}</div>
        </div>
        <span class="ce-count">${doneCount}/${zoneNames.length} โซน</span>
      </div>
      <div class="ce-body">${rowsHtml}</div>
    </div>`;
  }).join('');
}

function cleaningEmpSet() {
  const set = new Set();
  empData.forEach(e => {
    if (String(e.position || '').indexOf('แม่บ้าน') !== -1) set.add(_canonEmp(e.fullname));
  });
  return set;
}

function isCleaning(r) {
  if (empData.length > 0) return cleaningEmpSet().has(_canonEmp(r.employeeName));
  return String(r.department || '').indexOf('แม่บ้าน') !== -1;
}

function enterCleaning() {
  cleaningOnly = true;
  document.getElementById('companyFilter').value = '';
  const cg = document.getElementById('companyGate');
  cg.style.display = 'none';
  cg.style.pointerEvents = 'none';
  document.getElementById('loginGate').style.display = 'none';
  document.getElementById('pageTitle').textContent = 'ทำความสะอาด';
  _showPage('ot');
  document.getElementById('pageTitle').textContent = 'ทำความสะอาด';
  if (empData.length === 0) {
    loadEmployees().then(renderTable);
  } else {
    renderTable();
  }
}

function openEmpFromGate() {
  if (adminUser) {
    document.getElementById('companyGate').style.display = 'none';
    _showPage('emp');
    if (empData.length === 0) loadEmployees();
  } else {
    document.getElementById('loginGate').style.display = 'flex';
    document.getElementById('loginError').textContent = '';
    document.getElementById('loginCode').value = '';
    setTimeout(() => document.getElementById('loginCode').focus(), 50);
  }
}

function enterCompany(company) {
  cleaningOnly = false;
  const sel = document.getElementById('companyFilter');
  sel.value = company;
  sel.classList.toggle('active-filter', company !== '');
  const cg = document.getElementById('companyGate');
  cg.style.display = 'none';
  cg.style.pointerEvents = 'none';
  document.getElementById('loginGate').style.display = 'none';
  _showPage('ot');
  populateSupervisorFilter();
  renderTable();
}

function backToCompanyGate() {
  renderCompanyGate();
  const cg = document.getElementById('companyGate');
  cg.style.pointerEvents = '';
  cg.style.display = 'flex';
}

async function doLogin() {
  const code = document.getElementById('loginCode').value.trim();
  const pass = document.getElementById('loginPass').value.trim();
  const errEl = document.getElementById('loginError');
  const btn = document.getElementById('loginBtn');
  errEl.textContent = '';
  if (!code || !pass) { errEl.textContent = 'กรุณากรอกรหัสพนักงานและรหัสผ่าน'; return; }
  btn.disabled = true; btn.textContent = 'กำลังตรวจสอบ…';
  try {
    const res = await fetch(EMP_API + '/code/' + encodeURIComponent(code), { headers: HEADERS });
    if (res.status === 404) { errEl.textContent = 'ไม่พบรหัสพนักงานนี้'; btn.disabled = false; btn.textContent = 'เข้าสู่ระบบ'; return; }
    const j = await res.json();
    const emp = (j && j.data) ? j.data : j;
    if (String(emp.password) !== pass) { errEl.textContent = 'รหัสผ่านไม่ถูกต้อง'; btn.disabled = false; btn.textContent = 'เข้าสู่ระบบ'; return; }
    const pos = String(emp.position || '').toLowerCase();
    if (pos.indexOf('admin') === -1) { errEl.textContent = 'บัญชีนี้ไม่มีสิทธิ์ผู้ดูแล'; btn.disabled = false; btn.textContent = 'เข้าสู่ระบบ'; return; }
    adminUser = { code: emp.emp_code, name: emp.fullname, position: emp.position };
    document.getElementById('userChip').innerHTML = '<b>' + escapeHtml(emp.fullname || emp.emp_code) + '</b>';
    document.getElementById('loginGate').style.display = 'none';
    document.getElementById('companyGate').style.display = 'none';
    document.getElementById('loginPass').value = '';
    document.getElementById('loginBtn').disabled = false;
    document.getElementById('loginBtn').textContent = 'เข้าสู่ระบบ';
    _showPage('emp');
    if (empData.length === 0) loadEmployees();
  } catch (e) {
    errEl.textContent = 'เชื่อมต่อไม่ได้: ' + (e.message || e);
    btn.disabled = false; btn.textContent = 'เข้าสู่ระบบ';
  }
}

function cancelLogin() {
  document.getElementById('loginGate').style.display = 'none';
  document.getElementById('loginError').textContent = '';
  document.getElementById('loginPass').value = '';
  _showPage('ot');
}

function _showPage(page) {
  const isOt = page === 'ot';
  document.getElementById('pageOt').classList.toggle('active', isOt);
  document.getElementById('pageEmp').classList.toggle('active', !isOt);
  document.getElementById('pageTitle').textContent = isOt ? 'คำขอ OT' : 'พนักงาน';
  document.getElementById('btnRefresh').style.display = isOt ? '' : 'none';
  document.getElementById('btnExport').style.display = isOt ? '' : 'none';
  document.getElementById('btnChangeCompany').style.display = '';
}

async function loadEmployees() {
  const body = document.getElementById('empTableBody');
  body.innerHTML = '<tr><td colspan="6"><div class="empty"><div class="spinner"></div><div class="empty-desc">กำลังโหลด…</div></div></td></tr>';
  try {
    const res = await fetch(EMP_API, { headers: HEADERS });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const j = await res.json();
    let list = Array.isArray(j) ? j : (j.data || j.records || []);
    empData = list.map(e => ({
      id: e.id,
      empCode: e.emp_code || '',
      fullname: e.fullname || '',
      position: e.position || '',
      phone: e.phone || '',
      password: e.password || '',
    }));
    empData.sort((a, b) => String(a.empCode).localeCompare(String(b.empCode), 'th', { numeric: true }));
    populateEmpPosFilter();
    renderEmpTable();
  } catch (e) {
    body.innerHTML = '<tr><td colspan="6"><div class="empty"><div class="empty-title">โหลดไม่สำเร็จ</div><div class="empty-desc">' + escapeHtml(e.message || e) + '</div><button class="btn" onclick="loadEmployees()">ลองใหม่</button></div></td></tr>';
  }
}

function populateEmpPosFilter() {
  const sel = document.getElementById('empPosFilter');
  const cur = sel.value;
  const positions = Array.from(new Set(empData.map(e => e.position.trim()).filter(p => p)))
    .sort((a, b) => a.localeCompare(b, 'th'));
  sel.innerHTML = '<option value="">ทุกตำแหน่ง</option>' +
    positions.map(p => '<option value="' + escapeHtml(p) + '">' + escapeHtml(p) + '</option>').join('');
  if (cur && positions.indexOf(cur) !== -1) sel.value = cur;
}

function renderEmpTable() {
  const search = document.getElementById('empSearch').value.toLowerCase().trim();
  const posF = document.getElementById('empPosFilter').value;
  let filtered = empData.filter(e => {
    if (posF && e.position.trim() !== posF) return false;
    if (search) {
      const hay = (e.empCode + ' ' + e.fullname + ' ' + e.position + ' ' + e.phone).toLowerCase();
      if (!hay.includes(search)) return false;
    }
    return true;
  });
  document.getElementById('empCount').textContent = filtered.length + ' คน';
  const body = document.getElementById('empTableBody');
  if (filtered.length === 0) {
    body.innerHTML = '<tr><td colspan="6"><div class="empty"><div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg></div><div class="empty-title">ไม่พบพนักงาน</div><div class="empty-desc">ลองปรับคำค้นหา</div></div></td></tr>';
    return;
  }
  body.innerHTML = filtered.map(e => {
    const isAdmin = e.position.toLowerCase().indexOf('admin') !== -1;
    const posTag = isAdmin
      ? '<span class="badge b-approved">' + escapeHtml(e.position) + '</span>'
      : '<span class="company-tag">' + escapeHtml(e.position || '—') + '</span>';
    return '<tr>' +
      '<td class="mono" style="font-weight:700;color:var(--ink);">' + escapeHtml(e.empCode) + '</td>' +
      '<td class="col-name">' + escapeHtml(e.fullname || '—') + '</td>' +
      '<td>' + posTag + '</td>' +
      '<td class="hide-sm" style="color:var(--ink-2);">' + escapeHtml(e.phone || '—') + '</td>' +
      '<td class="mono"><span class="pw" data-id="' + e.id + '">••••</span> ' +
        '<button class="icon-btn" style="width:22px;height:22px;" title="แสดง/ซ่อน" onclick="togglePw(' + e.id + ',\'' + escapeJs(e.password) + '\')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg></button></td>' +
      '<td><div class="row-actions">' +
        '<button class="icon-btn" title="แก้ไข" onclick="openEmpEdit(' + e.id + ')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>' +
        '<button class="icon-btn" title="รีเซ็ตรหัสผ่าน" onclick="resetPw(' + e.id + ')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg></button>' +
        '<button class="icon-btn delete" title="ลบ" onclick="deleteEmp(' + e.id + ')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-2 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L5 6"/></svg></button>' +
      '</div></td></tr>';
  }).join('');
}

function togglePw(id, pw) {
  const el = document.querySelector('.pw[data-id="' + id + '"]');
  if (!el) return;
  el.textContent = (el.textContent === '••••') ? pw : '••••';
}

function findEmp(id) { return empData.find(e => e.id === id); }

function fillPositionOptions() {
  const dl = document.getElementById('posOptions');
  if (!dl) return;
  const positions = Array.from(new Set(empData.map(e => (e.position || '').trim()).filter(p => p)))
    .sort((a, b) => a.localeCompare(b, 'th'));
  dl.innerHTML = positions.map(p => `<option value="${escapeHtml(p)}"></option>`).join('');
}

function openEmpAdd() {
  empEditId = null;
  fillPositionOptions();
  document.getElementById('empModalTitle').textContent = 'เพิ่มพนักงาน';
  document.getElementById('empCodeInput').value = '';
  document.getElementById('empCodeInput').disabled = false;
  document.getElementById('empNameInput').value = '';
  document.getElementById('empPosInput').value = '';
  document.getElementById('empPhoneInput').value = '';
  document.getElementById('empPassInput').value = String(Math.floor(1000 + Math.random() * 9000));
  document.getElementById('empPassLabel').textContent = 'รหัสผ่าน (4 หลัก)';
  document.getElementById('empModal').classList.add('show');
}

function openEmpEdit(id) {
  const e = findEmp(id);
  if (!e) return;
  empEditId = id;
  fillPositionOptions();
  document.getElementById('empModalTitle').textContent = 'แก้ไขพนักงาน';
  document.getElementById('empCodeInput').value = e.empCode;
  document.getElementById('empCodeInput').disabled = true;
  document.getElementById('empNameInput').value = e.fullname;
  document.getElementById('empPosInput').value = e.position;
  document.getElementById('empPhoneInput').value = e.phone;
  document.getElementById('empPassInput').value = '';
  document.getElementById('empPassLabel').textContent = 'รหัสผ่านใหม่ (ว่าง = ไม่เปลี่ยน)';
  document.getElementById('empModal').classList.add('show');
}

function closeEmpModal() { document.getElementById('empModal').classList.remove('show'); }

async function saveEmp() {
  const code = document.getElementById('empCodeInput').value.trim();
  const name = document.getElementById('empNameInput').value.trim();
  const pos = document.getElementById('empPosInput').value.trim();
  const phone = document.getElementById('empPhoneInput').value.trim();
  const pass = document.getElementById('empPassInput').value.trim();
  const btn = document.getElementById('empSaveBtn');
  if (!name || !pos) { showToast('กรุณากรอกชื่อและตำแหน่ง', 'error'); return; }
  btn.disabled = true; btn.textContent = 'กำลังบันทึก…';
  try {
    let res;
    if (empEditId === null) {
      if (!code) { showToast('กรุณากรอกรหัสพนักงาน', 'error'); btn.disabled = false; btn.textContent = 'บันทึก'; return; }
      res = await fetch(EMP_API, {
        method: 'POST', headers: HEADERS,
        body: JSON.stringify({ emp_code: code, fullname: name, position: pos, phone: phone, password: pass || '0000' })
      });
    } else {
      const bodyObj = { fullname: name, position: pos, phone: phone };
      if (pass) bodyObj.password = pass;
      res = await fetch(EMP_API + '/' + empEditId, {
        method: 'PUT', headers: HEADERS, body: JSON.stringify(bodyObj)
      });
    }
    btn.disabled = false; btn.textContent = 'บันทึก';
    if (res.ok) {
      closeEmpModal();
      showToast('บันทึกสำเร็จ', 'success');
      loadEmployees();
    } else {
      const j = await res.json().catch(() => ({}));
      showToast(j.message || j.error || 'บันทึกไม่สำเร็จ (' + res.status + ')', 'error');
    }
  } catch (e) {
    btn.disabled = false; btn.textContent = 'บันทึก';
    showToast(e.message || e, 'error');
  }
}

async function resetPw(id) {
  const e = findEmp(id);
  if (!e) return;
  const newPw = String(Math.floor(1000 + Math.random() * 9000));
  if (!confirm('รีเซ็ตรหัสผ่านของ "' + e.fullname + '" เป็น ' + newPw + ' ?')) return;
  try {
    const res = await fetch(EMP_API + '/' + id, {
      method: 'PUT', headers: HEADERS, body: JSON.stringify({ password: newPw })
    });
    if (res.ok) {
      e.password = newPw;
      showToast('รหัสผ่านใหม่: ' + newPw, 'success');
      renderEmpTable();
    } else { showToast('รีเซ็ตไม่สำเร็จ', 'error'); }
  } catch (err) { showToast(err.message || err, 'error'); }
}

async function deleteEmp(id) {
  const e = findEmp(id);
  if (!e) return;
  if (!confirm('ลบพนักงาน "' + e.fullname + '" (' + e.empCode + ') ?\nลบแล้วกู้คืนไม่ได้')) return;
  try {
    const res = await fetch(EMP_API + '/' + id, { method: 'DELETE', headers: HEADERS });
    if (res.ok) {
      empData = empData.filter(x => x.id !== id);
      showToast('ลบสำเร็จ', 'success');
      renderEmpTable();
    } else { showToast('ลบไม่สำเร็จ', 'error'); }
  } catch (err) { showToast(err.message || err, 'error'); }
}

// ══════════════════════════════════════════════════════════════
//  ★★★ FIX (2026-08-07): ตั้งค่า default ของช่องกรองวันที่ให้ตรงกับ
//  "วันนี้" ตามเวลาไทย (+7) เสมอ ไม่อิงตาม timezone ของเครื่อง/เบราว์เซอร์
//  ผู้ใช้ (เดิมใช้ new Date() ตรงๆ ซึ่งถ้าเครื่องตั้ง timezone ไม่ตรงไทย
//  จะกรองไม่เจอรายการของ "วันนี้")
// ══════════════════════════════════════════════════════════════
(function setDefaultDateFilter() {
  // ★ FIX: บวก +7 ชม. จาก UTC epoch ตรงๆ แล้วอ่านค่าด้วย getUTC* เท่านั้น
  //   (ห้ามผสมกับ getTimezoneOffset()/getFullYear() ของเครื่อง เพราะถ้าเครื่อง
  //   ตั้ง timezone เป็นไทยอยู่แล้ว จะบวกซ้อนกันเป็น 14 ชม. แทนที่จะเป็น 7 ชม.
  //   ทำให้ข้ามไปอีกวันแบบที่เจอ — วิธีนี้ไม่พึ่งพา timezone ของเครื่องเลย)
  const now = new Date();
  const bd = new Date(now.getTime() + 7 * 3600000);
  const iso = bd.getUTCFullYear() + '-' + String(bd.getUTCMonth() + 1).padStart(2, '0') + '-' + String(bd.getUTCDate()).padStart(2, '0');
  const el = document.getElementById('dateFilter');
  if (el) el.value = iso;
})();
updateStatusFilterStyle();
loadData();
setInterval(loadData, 60000);
loadEmployees().then(() => {
  const cg = document.getElementById('companyGate');
  if (cg && cg.style.display !== 'none') renderCompanyGate();
  renderTable();
  computeSummary();
}).catch(() => {});
</script>
</body>
</html>