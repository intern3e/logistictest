{{-- resources/views/driver/oil.blade.php — หน้าหลักติดตามน้ำมัน (รวม CSS+JS ครบในไฟล์เดียว) --}}
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ระบบติดตามน้ำมันรถ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<style>
/* ==========================================
   Tesla Topnav & Filters Unified CSS
   v7 — เปลี่ยนตารางบันทึกน้ำมันเป็น <table> จริง (border-collapse)
   เพื่อการันตีเส้นคอลัมน์หัวตาราง/แถวข้อมูลตรงกัน 100% โดยธรรมชาติ
   ========================================== */
*,*::before,*::after{box-sizing:border-box;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
html{overflow-y:auto;}

:root{
  --bg:#fafafa; --bg-card:#ffffff; --bg-subtle:#f4f4f5; --bg-subtle2:#fafafa;
  --separator:rgba(0,0,0,.06); --separator-strong:rgba(0,0,0,.10);
  --text:#18181b; --text2:#3f3f46; --text3:#71717a; --text4:#a1a1aa; --text5:#d4d4d8;
  --blue:#3e6ae1; --blue-hover:#3457b2; --blue-light:#eff6ff;
  --green:#10b981; --green-dark:#059669; --green-light:#d1fae5;
  --orange:#f59e0b; --red:#ef4444; --red-light:#fee2e2;
  --radius:10px;
  --font-thai:'IBM Plex Sans Thai','Inter',-apple-system,sans-serif;
  --font-mono:ui-monospace,'SF Mono',Menlo,monospace;
}

.tesla-topnav {
  background-color: #ffffff;
  border-bottom: 1px solid #eaeaea;
  position: sticky;
  top: 0;
  z-index: 1000;
  width: 100%;
}
.tesla-topnav-container {
  max-width: 100%;
  margin: 0 auto;
  padding: 0 20px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.tesla-brand {
  display: flex;
  align-items: center;
  gap: 10px;
}
.tesla-logo {
  font-size: 20px;
}
.tesla-title {
  font-weight: 600;
  font-size: 16px;
  color: #171a20;
}
.tesla-right {
  display: flex;
  align-items: center;
  gap: 16px;
}
.tesla-user-badge {
  display: flex;
  align-items: center;
  font-size: 14px;
  color: #4b5563;
  font-weight: 400;
  white-space: nowrap;
}
.tesla-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}
.tesla-btn {
  height: 36px;
  padding: 0 18px;
  text-decoration: none;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  box-sizing: border-box;
  font-size: clamp(11px, 0.55vw + 5px, 14px) !important;
  border-radius: 6px !important;
  transition: all 0.2s ease;
  white-space: nowrap;
  line-height: 1;
}
.tesla-btn-neutral {
  background-color: #3e6ae1;
  color: #fff;
  border: 1px solid #3e6ae1;
}
.tesla-btn-neutral:hover {
  background-color: #3457b2;
  border-color: #3457b2;
  color: #fff;
  text-decoration: none;
}
.tesla-btn svg {
  color: #ffffff;
  flex-shrink: 0;
}

/* Filter Bar */
.topnav-filters {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 12px 20px;
  background-color: #ffffff;
  border-bottom: 1px solid #eaeaea;
  flex-wrap: wrap;
  width: 100%;
  box-sizing: border-box;
}
.filter-group {
  display: flex;
  align-items: center;
  gap: 8px;
  position: relative;
}
.filter-group-label {
  font-weight: 500;
  color: #4b5563;
  font-size: clamp(11px, 0.55vw + 5px, 14px);
  white-space: nowrap;
}
.segmented {
  display: inline-flex;
  background: #f1f3f5;
  padding: 3px;
  border-radius: 8px;
  gap: 2px;
  border: 1px solid #eaeaea;
}
.seg-btn {
  background: transparent;
  border: none;
  height: 30px;
  padding: 0 12px;
  font-size: clamp(11px, 0.55vw + 5px, 14px);
  font-weight: 500;
  color: #4b5563;
  border-radius: 6px !important;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.seg-btn:hover {
  color: #111827;
}
.seg-btn.active {
  background: #3e6ae1;
  color: #ffffff;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.date-trigger-pill, .tesla-select, .pill-date {
  display: inline-flex;
  align-items: center;
  height: 36px;
  padding: 0 14px;
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 6px !important;
  font-size: clamp(11px, 0.55vw + 5px, 14px);
  font-weight: 500;
  color: #374151;
  cursor: pointer;
  outline: none;
  transition: all 0.2s ease;
  box-sizing: border-box;
}
.date-trigger-pill {
  min-width: 180px;
  justify-content: space-between;
}
.tesla-select {
  min-width: 150px;
}
.date-trigger-pill:hover, .tesla-select:hover, .tesla-select:focus {
  border-color: #3e6ae1;
}
.date-trigger-pill .arrow {
  margin-left: 8px;
  color: #6b7280;
}

/* Date Range Picker Popup */
.drp-popup {
  display: none;
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  width: 300px;
  padding: 16px;
  z-index: 1001;
  box-sizing: border-box;
  font-size: 13px;
}
.drp-popup.open,
.drp-popup.show {
  display: block !important;
}
.drp-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}
.drp-title {
  font-weight: 600;
  color: #111827;
}
.drp-nav-btn {
  background: transparent;
  border: 1px solid #e5e7eb;
  border-radius: 6px !important;
  width: 28px;
  height: 28px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: #374151;
}
.drp-nav-btn:hover { background: #f3f4f6; }
.drp-weekdays {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  text-align: center;
  font-size: 12px;
  font-weight: 600;
  color: #9ca3af;
  margin-bottom: 6px;
}
.drp-days {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 2px;
  text-align: center;
  margin-bottom: 12px;
}
.drp-day {
  background: transparent;
  border: none;
  height: 32px;
  width: 100%;
  border-radius: 4px;
  cursor: pointer;
  font-size: 13px;
  color: #374151;
  display: flex;
  align-items: center;
  justify-content: center;
}
.drp-day:hover { background: #f3f4f6; }
.drp-day.selected,
.drp-day.range-start,
.drp-day.range-end {
  background: #3e6ae1 !important;
  color: #ffffff !important;
  font-weight: 600;
}
.drp-day.in-range {
  background: #eff6ff !important;
  color: #3e6ae1 !important;
}
.drp-hint {
  font-size: 12px;
  color: #6b7280;
  text-align: center;
  margin-bottom: 10px;
}
.drp-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top: 1px solid #f3f4f6;
  padding-top: 10px;
}
.drp-presets { display: flex; gap: 4px; }
.drp-preset-btn {
  background: transparent;
  border: none;
  font-size: 12px;
  color: #3e6ae1;
  cursor: pointer;
  padding: 4px 6px;
  border-radius: 4px !important;
}
.drp-preset-btn:hover { background: #eff6ff; }
.drp-apply-btn {
  background: #3e6ae1;
  color: #fff;
  border: none;
  padding: 6px 14px;
  border-radius: 6px !important;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
}
.drp-apply-btn:hover { background: #3457b2; }

/* Toast */
.toast {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #ffffff;
  border-left: 4px solid #10b981;
  border-top: 1px solid #eaeaea;
  border-right: 1px solid #eaeaea;
  border-bottom: 1px solid #eaeaea;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  padding: 14px 16px;
  border-radius: 8px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  z-index: 9999;
  min-width: 280px;
  cursor: pointer;
  transition: all 0.28s ease;
}
.toast.hiding { opacity: 0; transform: translateY(10px); }
.toast-icon {
  background: #d1fae5;
  color: #059669;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: bold;
  flex-shrink: 0;
}
.toast-body { flex: 1; }
.toast-title { font-weight: 600; font-size: 14px; color: #111827; }
.toast-msg { font-size: 13px; color: #4b5563; margin-top: 2px; }
.toast-progress {
  position: absolute;
  bottom: 0;
  left: 0;
  height: 3px;
  background: #10b981;
  width: 100%;
}

/* Main Layout */
.main {
  max-width: 1600px;
  margin: 24px auto;
  padding: 0 28px;
  box-sizing: border-box;
}
.entry-layout {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 28px;
}
@media (max-width: 1024px) {
  .entry-layout { grid-template-columns: 1fr; gap: 20px; }
}

.entry-card, .jobs-panel {
  background: #ffffff;
  border: 1px solid #eaeaea;
  border-radius: 10px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  overflow: hidden;
}
.entry-card-head {
  padding: 22px 28px;
  border-bottom: 1px solid #eaeaea;
}
.entry-card-head-left {
  display: flex;
  align-items: center;
  gap: 14px;
  flex-wrap: wrap;
}
.entry-icon { font-size: 22px; }
.entry-titlewrap { display: flex; flex-direction: column; gap: 2px; }
.entry-title { font-weight: 700; font-size: 17px; color: #111827; }
.entry-sub { font-size: 13px; color: #6b7280; }

.entry-oil-mini {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f9fafb;
  padding: 6px 12px;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
  font-size: 13px;
  margin-left: auto;
}
.entry-oil-label { color: #6b7280; }
.entry-oil-num { font-weight: 600; color: #111827; }
.entry-oil-refresh {
  background: none;
  border: none;
  cursor: pointer;
  color: #6b7280;
  font-size: 14px;
  padding: 0 2px;
}
.entry-oil-refresh:hover { color: #3e6ae1; }

.entry-export-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #111827;
  color: #fff;
  border: none;
  height: 38px;
  padding: 0 16px;
  border-radius: 6px !important;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}
.entry-export-btn:hover { background: #1f2937; }
#ilBtnSaveAll:hover { background: #059669 !important; }
#ilBtnSaveAll:disabled { background: #9ca3af !important; cursor: not-allowed; }

.entry-oil-tabs {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 16px 28px;
  background: #f9fafb;
  border-bottom: 1px solid #eaeaea;
  overflow-x: auto;
}
.entry-oil-tab {
  background: #ffffff;
  border: 1px solid #d1d5db;
  height: 32px;
  padding: 0 14px;
  border-radius: 6px !important;
  font-size: 12px;
  color: #4b5563;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}
.entry-oil-tab.active {
  background: #3e6ae1;
  color: #fff;
  border-color: #3e6ae1;
  font-weight: 500;
}
.entry-oil-status { font-size: 12px; color: #6b7280; margin-left: auto; }
.entry-oil-live { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; color: #10b981; }
.entry-oil-live.loading { color: #f59e0b; }
.dot { width: 6px; height: 6px; background: currentColor; border-radius: 50%; }

.entry-loading-row {
  padding: 20px;
  text-align: center;
  font-size: 13px;
  color: #6b7280;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}
.spinner {
  width: 14px;
  height: 14px;
  border: 2px solid #d1d5db;
  border-top-color: #3e6ae1;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ==========================================
   ENTRY TABLE — native <table> (v7)
   ใช้ table+colgroup จริง เส้นคอลัมน์หัว/แถวตรงกันโดยธรรมชาติ 100%
   ========================================== */
.entry-rows-wrap {
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  overflow: hidden;
  overflow-x: auto;
}
.entry-table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
  min-width: 880px;
}
.entry-table col.col-driver  { width: 15%; }
.entry-table col.col-plate   { width: 11%; }
.entry-table col.col-time    { width: 11%; }
.entry-table col.col-price   { width: 10%; }
.entry-table col.col-dist    { width: 9%;  }
.entry-table col.col-extra   { width: 23%; }
.entry-table col.col-summary { width: 13%; }
.entry-table col.col-save    { width: 8%;  }

.entry-table thead th {
  font-size: 12px;
  font-weight: 700;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  background: #f9fafb;
  padding: 14px 12px;
  text-align: center;
  border-right: 1px solid #e5e7eb;
  border-bottom: 2px solid #e5e7eb;
}
.entry-table thead th:first-child { text-align: left; }
.entry-table thead th:last-child { border-right: none; }

.entry-table tbody td {
  padding: 16px 12px;
  border-right: 1px solid #e5e7eb;
  border-bottom: 1px solid #e5e7eb;
  vertical-align: middle;
  text-align: center;
}
.entry-table tbody td:first-child { text-align: left; }
.entry-table tbody td:last-child { border-right: none; }
.entry-table tbody tr:last-child td { border-bottom: none; }

.entry-row:hover { background-color: #f9fafb; }
.entry-row.focused { background-color: #eff6ff; box-shadow: inset 4px 0 0 #3e6ae1; }
.entry-row.saving { opacity: 0.7; pointer-events: none; }

.entry-empty { text-align: center; padding: 40px 20px; color: #9ca3af; font-size: 13px; }

.er-extra-costs {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 6px;
  width: 100%;
}
.er-extra-item {
  display: flex;
  flex-direction: column;
  gap: 3px;
}
.er-extra-item label {
  font-size: 10px;
  color: #9ca3af;
  font-weight: 600;
  text-align: center;
  white-space: nowrap;
}
.er-num-sm {
  height: 34px;
  padding: 0 6px;
  font-size: 12px;
  text-align: center;
}

.er-cell-center {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
}
.er-nofuel-spacer {
  height: 20px;
  display: block;
  width: 100%;
}

.er-driver { display: flex; align-items: center; gap: 12px; width: 100%; }
.er-driver-avatar {
  width: 36px;
  height: 36px;
  background: #e0e7ff;
  color: #3e6ae1;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 15px;
  flex-shrink: 0;
}
.er-driver-info { display: flex; flex-direction: column; gap: 2px; overflow: hidden; }
.er-driver-name { font-weight: 700; font-size: 14px; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.er-driver-jobs { font-size: 12px; color: #6b7280; }
.er-ok { color: #10b981; font-weight: 600; }
.er-fail { color: #ef4444; font-weight: 600; }

.er-plate-select, .er-num-input {
  width: 100%;
  height: 40px;
  padding: 0 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 13px;
  color: #374151;
  background: #fff;
  outline: none;
  transition: border-color 0.2s;
  box-sizing: border-box;
}
.er-plate-select:focus, .er-num-input:focus {
  border-color: #3e6ae1;
  box-shadow: 0 0 0 3px rgba(62, 106, 225, 0.12);
}

.er-time-pair,
.er-time-stack {
  display: flex !important;
  flex-direction: column !important;
  gap: 5px !important;
  width: 100% !important;
  max-width: 150px !important;
  margin: 0 auto !important;
}
.er-time-arrow { display: none !important; }
.er-draft-badge { display:block; text-align:center; font-size:10px; font-weight:600; color:#059669; white-space:nowrap; margin-top:2px; }

.time-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  width: 100%;
}
.er-dt-input {
  width: 100%;
  height: 34px;
  padding: 0 28px 0 8px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 12px;
  color: #1f2937;
  background: #ffffff;
  outline: none;
  transition: all 0.2s ease;
  box-sizing: border-box;
  font-family: inherit;
  font-weight: 500;
}
.er-dt-input:hover, .er-dt-input:focus { border-color: #3e6ae1; }
.er-dt-input::-webkit-calendar-picker-indicator {
  position: absolute;
  right: 0;
  top: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}
.time-input-wrapper::after {
  content: '';
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  width: 18px;
  height: 18px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%233e6ae1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: center;
  background-size: contain;
  pointer-events: none;
  opacity: 0.75;
}

.er-nofuel-check {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: #6b7280;
  cursor: pointer;
  margin-bottom: 4px;
  white-space: nowrap;
}
.er-nofuel-check input { accent-color: #3e6ae1; }

.er-summary {
  display: flex;
  flex-direction: column;
  gap: 5px;
  font-size: 12px;
  width: 100%;
}
.er-summary-row { display: flex; justify-content: space-between; }
.er-summary-label { color: #9ca3af; font-weight: 500; }
.er-summary-val { font-weight: 600; color: #374151; }
.er-summary-val.empty { color: #d1d5db; }
.er-summary-val.green { color: #10b981; }
.er-summary-val.red { color: #ef4444; }

.er-save-btn {
  width: 100%;
  height: 40px;
  background: #3e6ae1;
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}
.er-save-btn:hover { background: #3457b2; }
.er-save-btn:disabled { background: #9ca3af; cursor: not-allowed; }

/* Sidebar Jobs Panel */
.jobs-panel-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 24px;
  border-bottom: 1px solid #eaeaea;
  background: #f9fafb;
}
.jobs-panel-title { display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 15px; color: #111827; }
.job-date-chip { background: #f1f3f5; font-size: 11px; padding: 3px 10px; border-radius: 4px; color: #4b5563; }
.jobs-panel-body { padding: 20px 24px; }
.job-loading { text-align: center; color: #9ca3af; font-size: 13px; padding: 40px 0; line-height: 1.6; }

/* Dual Grid & Fuel Table */
.dual-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-top: 28px; }
.dual-grid.single-col { grid-template-columns: 1fr; }
@media (max-width: 1024px) { .dual-grid { grid-template-columns: 1fr; } }

.card {
  background: #fff;
  border: 1px solid #eaeaea;
  border-radius: 10px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}
.card-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 28px;
  border-bottom: 2px solid #eaeaea;
  flex-wrap: wrap;
  gap: 16px;
}
.card-title { font-weight: 700; font-size: 16px; color: #111827; display: flex; align-items: center; gap: 10px; }
.card-count { background: #f3f4f6; color: #4b5563; font-size: 12px; padding: 3px 10px; border-radius: 10px; font-weight: 600; }
.card-meta { font-size: 12px; color: #9ca3af; font-weight: 400; }

.search-pill { position: relative; display: flex; align-items: center; }
.search-pill .si { position: absolute; left: 12px; color: #9ca3af; pointer-events: none; }
.search-pill input {
  height: 38px;
  padding: 0 14px 0 36px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 13px;
  outline: none;
  width: 220px;
}
.search-pill input:focus { border-color: #3e6ae1; }

/* ตารางและตัวล๊อคสโครลบาร์ไม่ให้ซ้อนกัน */
.fuel-table-scroll {
  overflow-x: auto;
  overflow-y: auto;
  max-height: 650px;
  width: 100%;
}
.fuel-table-scroll::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.fuel-table-scroll::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}
.fuel-table-scroll::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.fuel-table-scroll::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

.fuel-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
  table-layout: auto;
}
.fuel-table th {
  position: sticky;
  top: 0;
  z-index: 5;
  text-align: left;
  padding: 14px 16px;
  font-weight: 700;
  color: #6b7280;
  background: #f9fafb;
  border-bottom: 2px solid #e5e7eb;
  border-right: 1px solid #e5e7eb;
  white-space: nowrap;
  font-size: 11px;
  text-transform: uppercase;
}
.fuel-table th:last-child { border-right: none; }
.fuel-table th.num { text-align: right; }
.fuel-table td {
  padding: 14px 16px;
  border-bottom: 1px solid #e5e7eb;
  border-right: 1px solid #e5e7eb;
  color: #374151;
  vertical-align: middle;
}
.fuel-table td:last-child { border-right: none; }
.fuel-table td.num { text-align: right; font-variant-numeric: tabular-nums; }
.fuel-table tbody tr:hover { background-color: #f9fafb; }

.date-pill, .time-pill, .hour-pill {
  display: inline-block;
  padding: 4px 10px;
  background: #f3f4f6;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  color: #4b5563;
  white-space: nowrap;
}
.driver-cell { display: flex; flex-direction: column; gap: 2px; }
.driver-name { font-weight: 700; color: #111827; font-size: 13px; word-break: break-word; }
.driver-plate { font-size: 11px; color: #6b7280; font-family: monospace; }
.carry-hint { font-size: 10px; color: #f59e0b; margin-top: 2px; }

.km-good { color: #10b981; font-weight: 600; }
.km-mid { color: #f59e0b; font-weight: 600; }
.km-bad { color: #ef4444; font-weight: 600; }
.thb-km-val { color: #3e6ae1; font-weight: 600; }

.empty-state { text-align: center; padding: 50px 20px; color: #9ca3af; }
.empty-state .icon { font-size: 36px; margin-bottom: 10px; opacity: 0.5; }

/* Ranking & Sort */
.sort-toggle { display: flex; align-items: center; gap: 10px; }
.sort-label { font-size: 12px; color: #6b7280; }
.sort-segmented { display: inline-flex; background: #f3f4f6; padding: 3px; border-radius: 6px; }
.sort-btn { background: transparent; border: none; padding: 5px 12px; font-size: 12px; font-weight: 500; color: #6b7280; border-radius: 4px; cursor: pointer; }
.sort-btn.active { background: #fff; color: #111827; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }

.driver-list {
  display: flex;
  flex-direction: column;
  max-height: 650px;
  overflow-y: auto;
}
.driver-list::-webkit-scrollbar {
  width: 6px;
}
.driver-list::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}
.driver-list::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.driver-list::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
.driver-row { display: flex; align-items: center; padding: 18px 28px; border-bottom: 1px solid #f3f4f6; }
.driver-row:hover { background: #f9fafb; }
.driver-row:last-child { border-bottom: none; }
.driver-rank { width: 34px; font-weight: 700; color: #9ca3af; font-size: 14px; }
.driver-row .body { flex: 1; }
.driver-row .name { font-weight: 700; font-size: 14px; color: #111827; }
.driver-row .stats { font-size: 12px; color: #6b7280; margin-top: 4px; display: flex; gap: 8px; }
.driver-row .right { text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 3px; }
.driver-row .price { font-weight: 700; font-size: 16px; color: #111827; }
.driver-row .kml { font-size: 12px; font-weight: 600; }
.driver-row .kml.warn { color: #ef4444; }
.driver-row .thb-km { font-size: 11px; color: #6b7280; }

/* Charts Grid - 1 แถวต่อ 1 ข้อมูลกราฟ */
.charts-grid {
  display: grid;
  grid-template-columns: 1fr !important;
  gap: 24px;
  margin-top: 28px;
  width: 100%;
}
.chart-card { background: #fff; border: 1px solid #eaeaea; border-radius: 10px; padding: 24px; width: 100%; }
.chart-head { margin-bottom: 20px; }
.chart-title { font-weight: 700; font-size: 16px; color: #111827; }
.chart-sub { font-size: 12px; color: #6b7280; margin-top: 4px; }

.vehicle-toggle { display: inline-flex; background: #f3f4f6; padding: 3px; border-radius: 6px; margin-top: 12px; }
.vt-btn { background: transparent; border: none; padding: 5px 12px; font-size: 12px; color: #6b7280; border-radius: 4px; cursor: pointer; }
.vt-btn.active { background: #fff; color: #111827; font-weight: 600; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }

.chart-canvas { position: relative; width: 100%; }
.chart-legend { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 18px; font-size: 12px; color: #4b5563; }
.chart-legend-item { display: flex; align-items: center; gap: 6px; }
.chart-legend-dot { width: 10px; height: 10px; border-radius: 50%; }

/* Jobs Panel Details */
.jobs-summary-bar { display: flex; gap: 8px; margin-bottom: 18px; flex-wrap: wrap; }
.jsb-chip { padding: 5px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; background: #f3f4f6; color: #4b5563; }
.jsb-chip.ok { background: #d1fae5; color: #065f46; }
.jsb-chip.fail { background: #fee2e2; color: #991b1b; }

.dgj-row { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px; margin-bottom: 12px; }
.dgj-top { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; flex-wrap: wrap; }
.dgj-bill { font-weight: 700; font-size: 13px; color: #111827; font-family: monospace; }
.dgj-customer { font-size: 13px; color: #4b5563; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dgj-status { font-size: 11px; padding: 3px 8px; border-radius: 4px; font-weight: 600; }
.dgj-status.ok { background: #d1fae5; color: #065f46; }
.dgj-status.fail { background: #fee2e2; color: #991b1b; }
.dgj-status.pending { background: #f3f4f6; color: #6b7280; }
.dgj-meta { display: flex; flex-wrap: wrap; gap: 4px 10px; font-size: 11px; color: #6b7280; }
.dgj-meta-item { display: flex; gap: 4px; }
.dgj-meta-label { color: #9ca3af; font-weight: 500; }
.dgj-note { color: #f59e0b; }

/* PDF Modal */
.pdf-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  backdrop-filter: blur(2px);
  z-index: 9999;
  display: none;
  align-items: center;
  justify-content: center;
}
.pdf-modal-overlay.open { display: flex; }
.pdf-modal {
  background: #fff;
  width: 100%;
  max-width: 420px;
  border-radius: 12px;
  box-shadow: 0 20px 50px rgba(0,0,0,0.15);
  overflow: hidden;
}
.pdf-modal-head { display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; border-bottom: 1px solid #eaeaea; font-weight: 700; font-size: 16px; }
.pdf-modal-x { background: none; border: none; font-size: 20px; color: #6b7280; cursor: pointer; padding: 4px; }
.pdf-modal-body { padding: 24px; }
.pdf-mode-tabs { display: flex; background: #f3f4f6; padding: 3px; border-radius: 8px; margin-bottom: 22px; }
.pdf-mode-btn { flex: 1; background: transparent; border: none; padding: 9px; font-size: 13px; font-weight: 500; color: #6b7280; border-radius: 6px; cursor: pointer; }
.pdf-mode-btn.active { background: #fff; color: #111827; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.pdf-field { margin-bottom: 18px; }
.pdf-field label { display: block; font-size: 12px; font-weight: 600; color: #4b5563; margin-bottom: 8px; }
.pdf-field input[type="date"] { width: 100%; height: 40px; padding: 0 14px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; outline: none; box-sizing: border-box; }
.pdf-modal-foot { display: flex; justify-content: flex-end; gap: 12px; padding: 18px 24px; background: #f9fafb; border-top: 1px solid #eaeaea; }
.pdf-btn-cancel { background: #fff; border: 1px solid #d1d5db; color: #374151; padding: 9px 18px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; }
.pdf-btn-go { background: #3e6ae1; border: 1px solid #3e6ae1; color: #fff; padding: 9px 22px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }

/* Save Toast */
.save-toast {
  position: fixed;
  top: 80px;
  right: 20px;
  background: #111827;
  color: #fff;
  padding: 14px 18px;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  display: flex;
  align-items: center;
  gap: 12px;
  z-index: 9999;
  font-size: 14px;
}
.save-toast.hiding { opacity: 0; transform: translateY(-10px); transition: all 0.25s ease; }
.save-toast-icon { background: #10b981; color: #fff; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; }

/* ==========================================
   RESPONSIVE — ปรับให้ใช้งานได้ทุกขนาดหน้าจอ
   ========================================== */

/* ----- Topnav & filter bar: บีบ/ห่อได้บนจอแคบ ----- */
@media (max-width: 640px) {
  .tesla-topnav-container { flex-wrap: wrap; height: auto; padding: 10px 14px; gap: 8px; }
  .tesla-right { flex-wrap: wrap; gap: 8px; width: 100%; justify-content: space-between; }
  .tesla-actions { flex-wrap: wrap; gap: 6px; width: 100%; }
  .tesla-btn { flex: 1 1 auto; justify-content: center; }
  .topnav-filters { padding: 10px 14px; gap: 10px; }
  .filter-group { width: 100%; }
  .filter-group-label { min-width: 56px; }
  .date-trigger-pill, .tesla-select { flex: 1; min-width: 0; width: 100%; }
  .segmented { width: 100%; }
  .seg-btn { flex: 1; }
  .search-pill input { width: 100%; }
  .card-head .search-pill { width: 100%; }
}

/* ----- .main padding บีบลงบนจอเล็ก ----- */
@media (max-width: 640px) {
  .main { padding: 0 12px; margin: 14px auto; }
  .entry-card-head { padding: 16px; }
  .entry-oil-tabs { padding: 12px 16px; }
  .card-head { padding: 16px; }
  .chart-card { padding: 16px; }
}

/* ----- ต่ำกว่า 900px: ตาราง -> การ์ดสแต็กแนวตั้ง ----- */
@media (max-width: 900px) {
  .entry-rows-wrap { overflow-x: visible; border: none; border-radius: 0; padding: 12px; }
  .entry-table, .entry-table tbody { display: block; width: 100%; min-width: 0; }
  .entry-table thead { display: none; }
  .entry-row {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 16px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    margin-bottom: 12px;
    background: #fff;
  }
  .entry-row:last-child { margin-bottom: 0; }
  .entry-row > td {
    display: block;
    width: 100% !important;
    border: none !important;
    text-align: left !important;
    padding: 0;
  }
  .entry-row > td[data-label]::before {
    content: attr(data-label);
    display: block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #9ca3af;
    margin-bottom: 6px;
  }
  .er-time-stack { max-width: 100% !important; }
  .er-extra-costs { max-width: 100%; }
  .er-save-btn { height: 44px; } /* ปุ่มกดง่ายขึ้นบนจอสัมผัส */
}

/* ----- ตารางรายการเติมน้ำมัน (fuel-table) + ranking: ปรับ padding เล็กลงบนจอเล็ก ----- */
@media (max-width: 640px) {
  .fuel-table th, .fuel-table td { padding: 10px 8px; font-size: 12px; }
  .driver-row { padding: 14px 16px; }
  .driver-row .stats { flex-wrap: wrap; }
}

/* ----- Charts: ลด min-height ของกราฟแนวนอน (kml/cost) บนจอเล็กจะได้ไม่สูงเกิน ----- */
@media (max-width: 640px) {
  .chart-canvas[style*="height:300px"] { height: 240px !important; }
}

/* ----- PDF modal: เต็มความกว้างจอบนมือถือ ----- */
@media (max-width: 480px) {
  .pdf-modal { max-width: 92vw; }
  .pdf-modal-body { padding: 18px; }
}

/* ----- จอเล็ก (<=900px): ย่อ "รายการงาน" ให้เล็กสุด (ตัวอักษร/padding/ความสูง)
   เพื่อให้ส่วน "บันทึกการเติมน้ำมัน" เด่นและกรอกง่ายที่สุด ไม่ต้องเลื่อนผ่านของยาวๆ ----- */
@media (max-width: 900px) {
  .entry-layout { gap: 14px; }

  .jobs-panel {
    order: 2; /* ให้แน่ใจว่าอยู่หลังฟอร์มบันทึกน้ำมันเสมอ */
  }
  .jobs-panel-head {
    padding: 8px 12px;
  }
  .jobs-panel-title {
    font-size: 12px;
    gap: 5px;
  }
  .jobs-panel-title .ico {
    font-size: 12px;
  }
  .job-date-chip {
    font-size: 10px;
    padding: 2px 7px;
  }
  .jobs-panel-body {
    padding: 8px 12px;
    max-height: 180px;   /* จำกัดความสูง กันไม่ให้รายการงานยาวดันหน้าลงจนต้องเลื่อนเยอะ */
    overflow-y: auto;
  }
  .job-loading {
    font-size: 11px;
    padding: 16px 0;
    line-height: 1.4;
  }
  .jobs-summary-bar {
    gap: 4px;
    margin-bottom: 8px;
  }
  .jsb-chip {
    font-size: 10px;
    padding: 2px 8px;
  }
  .dgj-row {
    padding: 8px;
    margin-bottom: 6px;
    border-radius: 6px;
  }
  .dgj-top {
    gap: 6px;
    margin-bottom: 4px;
  }
  .dgj-bill {
    font-size: 10px;
  }
  .dgj-customer {
    font-size: 10px;
  }
  .dgj-status {
    font-size: 9px;
    padding: 1px 6px;
  }
  .dgj-meta {
    font-size: 9px;
    gap: 2px 8px;
  }

  /* ให้ฟอร์มบันทึกการเติมน้ำมันเด่นและกรอกง่ายสุด */
  .entry-card {
    order: 1;
  }
  .entry-title { font-size: 16px; }
  .entry-sub { font-size: 12px; }
  .er-driver-name { font-size: 15px; }
  .er-num-input, .er-plate-select { font-size: 14px; height: 42px; }
  .er-save-btn { font-size: 14px; }
}

</style>
</head>
<body>

@php
  $currentUser = request()->filled('create_by') ? request('create_by') : 'Guest';
  $userQuery = $currentUser !== 'Guest' ? '?create_by='.urlencode($currentUser) : '';
  $privilegedUsers = ['จัน','kanitin2','test101','jun'];
  $isPrivileged = in_array(trim($currentUser), $privilegedUsers, true);
  $allowedDrivers = ['กอลฟ์','เก่ง','เอ้','แฟงค์','เอ','บังเดช','yuth','แซม','บอย','หรั่ง','บอยBTS','กบ','joey','แมน'];
  $driverOrderList = ['กอลฟ์','เก่ง','เอ้','เอ','บังเดช','แฟงค์','yuth','แซม','บอย','บอยBTS','กบ','joey','แมน'];
@endphp

<nav class="tesla-topnav">
  <div class="tesla-topnav-container">
    <div class="tesla-brand">
      <div class="tesla-logo">⛽</div>
      <span class="tesla-title">ติดตามน้ำมัน</span>
    </div>

    <div class="tesla-right">
      <div class="tesla-user-badge" title="ผู้ใช้ปัจจุบัน">
        👤 ผู้ใช้: {{ $currentUser }}
      </div>
      
      <div class="tesla-actions">
        <a href="{{ url('/oil/report').$userQuery }}" class="tesla-btn tesla-btn-neutral">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="3" y1="20" x2="21" y2="20"/></svg>
          รายงาน
        </a>
        <a href="{{ url('/oil/Deliveryfee').$userQuery }}" class="tesla-btn tesla-btn-neutral">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18.7 8.3l-6.2 6.2-3-3-3.5 3.5"/></svg>
          ตารางค่าวิ่ง
        </a>
        <a href="{{ url('/service').$userQuery }}" class="tesla-btn tesla-btn-neutral">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
          Service
        </a>
        <a href="http://server_update:8000/solist{{ $userQuery }}" class="tesla-btn tesla-btn-neutral">
          หน้าหลัก
        </a>
      </div>
    </div>
  </div>
</nav>

<div class="topnav-filters">
    <div class="filter-group">
      <span class="filter-group-label">มุมมอง</span>
      <div class="segmented">
        @foreach(['day'=>'รายวัน','month'=>'รายเดือน','year'=>'รายปี','all'=>'ทั้งหมด'] as $v=>$label)
        <button type="button" class="seg-btn {{ $view===$v?'active':''}}" onclick="switchView('{{ $v }}')">{{ $label }}</button>
        @endforeach
      </div>
    </div>

    @if($view==='day')
      @php  
        $dateFrom = request('date_from', request('date', $filterDay ?? date('Y-m-d')));
        $dateTo   = request('date_to',   request('date', $filterDay ?? date('Y-m-d')));
      @endphp
      <div class="filter-group">
        <span class="filter-group-label">ช่วงวันที่</span>
        <div class="drp-wrap" data-from="{{ $dateFrom }}" data-to="{{ $dateTo }}" style="position:relative">
          <button type="button" class="date-trigger-pill" id="drpTrigger" onclick="drpToggle(event)">
            <span id="drpLabel" style="flex:1">—</span>
            <span class="arrow">⌄</span>
          </button>
          <div class="drp-popup" id="drpPopup">
            <div class="drp-header">
              <button type="button" class="drp-nav-btn" onclick="drpNavMonth(-1)">‹</button>
              <div class="drp-title" id="drpMonthTitle">—</div>
              <button type="button" class="drp-nav-btn" onclick="drpNavMonth(1)">›</button>
            </div>
            <div class="drp-weekdays">
              <div class="drp-weekday">อา</div><div class="drp-weekday">จ</div><div class="drp-weekday">อ</div>
              <div class="drp-weekday">พ</div><div class="drp-weekday">พฤ</div><div class="drp-weekday">ศ</div>
              <div class="drp-weekday">ส</div>
            </div>
            <div class="drp-days" id="drpDays"></div>
            <div class="drp-hint" id="drpHint">เลือกวันเริ่มต้น</div>
            <div class="drp-footer">
              <div class="drp-presets">
                <button type="button" class="drp-preset-btn" onclick="drpPreset('today')">วันนี้</button>
                <button type="button" class="drp-preset-btn" onclick="drpPreset('7days')">7 วัน</button>
                <button type="button" class="drp-preset-btn" onclick="drpPreset('thismonth')">เดือนนี้</button>
              </div>
              <button type="button" class="drp-apply-btn" id="drpApplyBtn" onclick="drpApply()">ตกลง</button>
            </div>
          </div>
        </div>
      </div>
    @elseif($view==='year')
      <div class="filter-group">
        <span class="filter-group-label">ปี</span>
        <select class="tesla-select" id="yearPicker" onchange="onYearChange()">
          @php
            $savedYear = request('year', date('Y'));
            $yearMax = max((int)date('Y') + 2, (int)$savedYear);
          @endphp
          @for($y=$yearMax;$y>=2020;$y--)
          <option value="{{ $y }}" {{ $savedYear==$y?'selected':'' }}>{{ $y }}</option>
          @endfor
        </select>
      </div>
    @elseif($view!=='all')
      <div class="filter-group">
        <span class="filter-group-label">เดือน</span>
        <input type="month" class="tesla-select" id="monthPicker" value="{{ $filterMonth }}" onchange="submitFilter()">
      </div>
    @endif

    <div class="filter-group">
      <span class="filter-group-label">คนขับ</span>
      <select class="tesla-select" id="driverPicker" onchange="submitFilter()">
        <option value="all" {{ $filterDriver==='all'?'selected':'' }}>คนขับทั้งหมด</option>
        @php
          $normDrv = function($s){
            $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{0E4C}]/u', '', (string)$s);
            return mb_strtolower(trim(preg_replace('/\s+/', ' ', $s)));
          };
          $drvHasData = [];
          foreach($allLogs as $r){
            $nm = trim((string)($r['driver_name'] ?? ''));
            if($nm === '') continue;
            $price = (float)($r['total_price'] ?? 0);
            $dist  = (float)($r['total_distance'] ?? 0);
            if($price > 0 || $dist > 0) $drvHasData[$normDrv($nm)] = true;
          }
          $drvSource = collect($allLogs)->pluck('driver_name')->map(fn($n)=>trim((string)$n))
            ->filter(fn($n)=> $n !== '' && isset($drvHasData[$normDrv($n)]))
            ->unique()->values()->all();
          $seenDrv = [];
        @endphp
        @foreach($drvSource as $d)
          @php $nd = $normDrv($d); @endphp
          @if(!in_array($nd, $seenDrv, true))
            @php $seenDrv[] = $nd; @endphp
            <option value="{{ trim($d) }}" {{ trim($filterDriver)===trim($d)?'selected':'' }}>{{ trim($d) }}</option>
          @endif
        @endforeach
      </select>
    </div>

    <div class="filter-group">
      <span class="filter-group-label">ทะเบียน</span>
      <select class="tesla-select" id="platePicker" onchange="submitFilter()">
        <option value="all" {{ ($filterPlate ?? 'all')==='all'?'selected':'' }}>ทะเบียนทั้งหมด</option>
        @foreach($plates as $p)
        <option value="{{ $p }}" {{ ($filterPlate ?? 'all')===$p?'selected':'' }}>{{ $p }}</option>
        @endforeach
      </select>
    </div>
</div>

@if(session('success'))
<div class="toast" id="successToast">
  <div class="toast-icon">✓</div>
  <div class="toast-body">
    <div class="toast-title">บันทึกสำเร็จ</div>
    <div class="toast-msg">{{ session('success') }}</div>
  </div>
  <div class="toast-progress" id="toastProgress"></div>
</div>
<script>
(function(){
  var D=5000,t=document.getElementById('successToast'),b=document.getElementById('toastProgress');
  if(!t)return;
  b.style.transition='width '+D+'ms linear';
  requestAnimationFrame(function(){requestAnimationFrame(function(){b.style.width='0%';});});
  setTimeout(function(){t.classList.add('hiding');setTimeout(function(){t.remove();},280);},D);
  t.addEventListener('click',function(){t.classList.add('hiding');setTimeout(function(){t.remove();},280);});
})();
</script>
@endif

<main class="main">
  @php
    $defaultPlates = ['1 ฉผ 1276','1 ฉผ 3181','1ฉผ213','1ฉผศ7158','2 ฉธ 1620','2ฉมฎ3017','2ฉธ1619','2ฉธ1621','805','3ฉมก6071','3ฉมง3059','3ฉมณ6380','3ฉมย478','3ฉมห200','4กย1540','6762','5861','City 8กค6309','City 9 กค4815','แจ๊ส 9กธ4830'];
    $plateList = collect($defaultPlates)->merge($plates ?? [])->unique()->values()->all();
  @endphp

  @if($view === 'day' && $isPrivileged)
  <div class="entry-layout">
    <div class="entry-card">
      <div class="entry-card-head">
        <div class="entry-card-head-left">
          <div class="entry-icon"></div>
          <div class="entry-titlewrap">
            <span class="entry-title">บันทึกการเติมน้ำมัน</span>
            <span class="entry-sub" id="entrySub">เลือกวันที่เพื่อโหลดคนขับของวันนั้น</span>
          </div>
          <input type="hidden" id="il-work-date" value="{{ request('date_from', request('date', $filterDay ?? date('Y-m-d'))) }}">
          <div class="entry-oil-mini">
            <span class="entry-oil-label" id="ilOilPriceLabel">ราคาดีเซล</span>
            <span class="entry-oil-num">฿<span id="ilOilPriceShow">—</span></span>
            <button type="button" id="ilBtnRefresh" class="entry-oil-refresh" onclick="ilRefreshOilPrice()" title="รีเฟรช">↻</button>
          </div>
          <button type="button" class="entry-export-btn" id="ilBtnSaveAll" onclick="erSaveAllRows()" style="background:#10b981" title="บันทึกทุกคนที่กรอกข้อมูลครบในหน้านี้">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
            <span class="sa-label">บันทึกทั้งหมด</span>
          </button>
          <button type="button" class="entry-export-btn" onclick="openPdfRangeModal()" title="ดาวน์โหลดรายงาน PDF">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Export PDF</span>
          </button>
        </div>
      </div>

      <div class="entry-oil-tabs">
        @php $oilBtns = ['diesel'=>'ดีเซล','95'=>'95','benzin95'=>'เบนซิน 95','91'=>'91','e20'=>'E20','e85'=>'E85']; @endphp
        @foreach($oilBtns as $oilKey => $oilLabel)
        <button type="button" onclick="ilSwitchOilType('{{ $oilKey }}')" id="ilBtnOil-{{ $oilKey }}" class="entry-oil-tab {{ $oilKey==='diesel'?'active':'' }}">{{ $oilLabel }}</button>
        @endforeach
        <span class="entry-oil-status" id="ilOilPriceStatus">กำลังโหลด</span>
        <span class="entry-oil-live loading" id="ilLiveWrap">
          <span class="dot" id="ilLiveDot"></span>
          <span id="ilLiveLabel">กำลังดึง</span>
        </span>
      </div>

      <input type="hidden" id="il-price-per-liter" value="">

      <div class="entry-loading-row" id="entryLoadingHint" style="display:none">
        <span class="spinner"></span> กำลังโหลดข้อมูลคนขับ...
      </div>

      <div class="entry-rows-wrap">
        <table class="entry-table">
          <colgroup>
            <col class="col-driver"><col class="col-plate"><col class="col-time">
            <col class="col-price"><col class="col-dist"><col class="col-extra">
            <col class="col-summary"><col class="col-save">
          </colgroup>
          <thead>
            <tr>
              <th>คนขับ</th>
              <th>ทะเบียนรถ</th>
              <th>เวลา</th>
              <th>ค่าน้ำมัน (฿)</th>
              <th>ระยะ (KM)</th>
              <th>ค่าใช้จ่ายเพิ่มเติม (฿)</th>
              <th>สรุป</th>
              <th>บันทึก</th>
            </tr>
          </thead>
          <tbody id="entryRowsBody">
            <tr><td colspan="8" class="entry-empty">เลือกวันที่เพื่อแสดงรายชื่อคนขับ</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <aside class="jobs-panel" id="inlineJobsWrap">
      <div class="jobs-panel-head">
        <div class="jobs-panel-title">
          <span class="ico">📋</span>
          <span id="jobsPanelTitleText">รายการงาน</span>
        </div>
        <span id="ilJobDateChip" class="job-date-chip" style="display:none">วันนี้</span>
      </div>
      <div class="jobs-panel-body" id="inlineJobTableWrap">
        <div class="job-loading">คลิกที่แถวคนขับ<br>เพื่อดูรายการงานของคนนั้น</div>
      </div>
    </aside>
  </div>
  @endif

  <div class="dual-grid {{ $view === 'day' ? 'single-col' : '' }}">
    <div class="card">
      <div class="card-head">
        <div class="card-title">
          รายการเติมน้ำมัน
          <span class="card-count" id="oilCount">{{ $logs->count() }}</span>
          <span class="card-meta">เรียงตามเวลาทำงาน</span>
        </div>
        <div class="search-pill">
          <span class="si"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg></span>
          <input type="text" placeholder="ค้นหา" oninput="filterOilTable(this.value)">
        </div>
      </div>

      <div class="fuel-table-scroll">
      <table class="fuel-table">
        <colgroup>
          <col style="width:40px">
          <col style="width:70px">
          <col style="width:140px">
          <col style="width:95px">
          <col style="width:70px">
          <col style="width:80px">
          <col style="width:70px">
          <col style="width:85px">
          <col style="width:75px">
          <col style="width:80px">
        </colgroup>
        <thead><tr><th>#</th><th>วันที่</th><th>คนขับ / ทะเบียน</th><th>เวลา</th><th class="num">ชม.</th><th class="num">ระยะ</th><th class="num">ลิตร</th><th class="num">฿</th><th class="num">KM/L</th><th class="num">฿/km</th></tr></thead>
        <tbody id="oilTbody">
          @php
            $rowNo = 0;
            $allArr = $allLogs->all();
            $byKey = [];
            foreach($allArr as $idx => $r){ $k = $r['vehicle_id'] ?? ''; if(!isset($byKey[$k])) $byKey[$k] = []; $byKey[$k][] = $idx; }
            $effDistance = []; $effKml = []; $isCarryRow = [];
            foreach($byKey as $k => $indices){
              usort($indices, function($a, $b) use ($allArr){ $ra=$allArr[$a];$rb=$allArr[$b];$da=$ra['work_date']??'';$db=$rb['work_date']??'';if($da!==$db)return strcmp($da,$db);return((int)($ra['id']??0))<=>((int)($rb['id']??0)); });
              $pending = 0;
              foreach($indices as $idx){ $r=$allArr[$idx];$rid=(int)($r['id']??0);if(!$rid)continue;$price=(float)($r['total_price']??0);$thisDist=(float)($r['total_distance']??0);if($price<=0){$pending+=$thisDist;$isCarryRow[$rid]=true;$effDistance[$rid]=0;$effKml[$rid]=0;}else{$eff=$thisDist+$pending;$effDistance[$rid]=$eff;$liters=(float)($r['liters']??0);$effKml[$rid]=($liters>0&&$eff>0)?round($eff/$liters,2):0;$isCarryRow[$rid]=false;$pending=0;} }
            }
            $normName = function($s){ $s=preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{0E4C}]/u','',(string)$s); return mb_strtolower(trim(preg_replace('/\s+/',' ',$s))); };
            $orderMap = [];
            foreach($driverOrderList as $i => $nm){ $orderMap[$normName($nm)] = $i; }
            $logsArr2 = $logs->all();
            usort($logsArr2, function($a, $b) use ($orderMap, $normName){ $ia=$orderMap[$normName($a['driver_name']??'')]??999;$ib=$orderMap[$normName($b['driver_name']??'')]??999;if($ia!==$ib)return $ia-$ib;return strcmp($b['work_date']??'',$a['work_date']??''); });
            $logsSorted = collect($logsArr2);
            if($view !== 'day'){
              $logsSorted = $logsSorted->take(30);
            }
          @endphp
          @forelse($logsSorted as $r)
          @php
            $rowNo++;
            $rid=(int)($r['id']??0);
            $effDist=$effDistance[$rid]??((float)($r['total_distance']??0));
            $kml=$effKml[$rid]??($r['km_per_liter']??0);
            $rawDist=(float)($r['total_distance']??0);
            $carryAmt=$effDist-$rawDist;
            if($rawDist>0){$distHtml=number_format($rawDist).' km';if($carryAmt>0)$distHtml.='<div class="carry-hint" title="รวมระยะจากวันที่ไม่เติม">+'.number_format($carryAmt).' km สะสม</div>';}else{$distHtml='—';}
            $name=$r['driver_name']??'—';$plate=$r['vehicle_id']??'—';
            $kmlClass='km-mid';if($kml>=13)$kmlClass='km-good';elseif($kml>0&&$kml<9)$kmlClass='km-bad';
            $tStart=$r['start_time']??'';$tEnd=$r['end_time']??'';
            if(strlen($tStart)>=5)$tStart=substr($tStart,0,5);if(strlen($tEnd)>=5)$tEnd=substr($tEnd,0,5);
            $timeText=($tStart&&$tEnd)?$tStart.'-'.$tEnd:'—';
            $wh=(float)($r['work_hours']??0);$durText='';
            if($wh>0){$totalMin=(int)round($wh*60);$days=intdiv($totalMin,1440);$hh=intdiv($totalMin%1440,60);$mm=$totalMin%60;if($days>0){$durText=$days.' วัน';if($hh>0)$durText.=' '.$hh.' ชม.';if($mm>0)$durText.=' '.$mm.' น.';}elseif($hh>0&&$mm>0)$durText=$hh.' ชม. '.$mm.' น.';elseif($hh>0)$durText=$hh.' ชม.';else $durText=$mm.' น.';}
            $workDate=$r['work_date']??'';$dateText='—';$dateFull='';
            if($workDate){try{$dt=\Carbon\Carbon::parse($workDate);$dateText=$dt->format('d/m');$dateFull=$dt->format('d/m/Y');}catch(\Exception $e){$dateText='—';}}
            $thbPerKm=($effDist>0&&($r['total_price']??0)>0)?($r['total_price']/$effDist):0;
          @endphp
          <tr data-driver="{{ strtolower($name) }}">
            <td class="row-idx" data-label="#">{{ str_pad((string)$rowNo,2,'0',STR_PAD_LEFT) }}</td>
            <td data-label="วันที่"><span class="date-pill" title="{{ $dateFull }}">{{ $dateText }}</span></td>
            <td data-label="คนขับ"><div class="driver-cell"><div class="driver-name" title="{{ $name }}">{{ $name }}</div><div class="driver-plate" title="{{ $plate }}">{{ $plate }}</div></div></td>
            <td data-label="เวลา"><span class="time-pill">{{ $timeText }}</span></td>
            <td class="num" data-label="ชม.">{!! $durText?'<span class="hour-pill">'.$durText.'</span>':'<span style="color:var(--text4)">—</span>' !!}</td>
            <td class="num" data-label="ระยะ">{!! $distHtml !!}</td>
            <td class="num" data-label="ลิตร">{{ $r['liters']?rtrim(rtrim(number_format($r['liters'],2,'.',''),'0'),'.'):'—' }}</td>
            <td class="num" data-label="ค่าน้ำมัน">{{ $r['total_price']?'฿'.number_format($r['total_price']):'—' }}</td>
            <td class="num" data-label="KM/L">@if($kml>0)<span class="{{ $kmlClass }}">{{ rtrim(rtrim(number_format($kml,2,'.',''),'0'),'.') }}</span>@else<span style="color:var(--text4)">—</span>@endif</td>
            <td class="num" data-label="฿/km">@if($thbPerKm>0)<span class="thb-km-val">฿{{ number_format($thbPerKm,2) }}</span>@else<span style="color:var(--text4)">—</span>@endif</td>
          </tr>
          @empty
          <tr><td colspan="10"><div class="empty-state"><div class="icon">⛽</div><p>ไม่พบรายการ</p></div></td></tr>
          @endforelse
        </tbody>
      </table>
      </div>
    </div>

    @if($view !== 'day')
    <div class="card">
      <div class="card-head">
        <div class="card-title">
          อันดับคนขับ
          @php
            $uniqueDrivers=[];
            foreach($logs as $r){$n=$r['driver_name']??'';if(!isset($uniqueDrivers[$n]))$uniqueDrivers[$n]=['name'=>$n,'rounds'=>0,'distance'=>0,'liters'=>0,'price'=>0,'kml_sum'=>0,'kml_count'=>0];$uniqueDrivers[$n]['rounds']++;$uniqueDrivers[$n]['distance']+=$r['total_distance']??0;$uniqueDrivers[$n]['liters']+=$r['liters']??0;$uniqueDrivers[$n]['price']+=$r['total_price']??0;if(($r['km_per_liter']??0)>0){$uniqueDrivers[$n]['kml_sum']+=$r['km_per_liter'];$uniqueDrivers[$n]['kml_count']++;}}
            $driverOrderIdx = function($name) use ($orderMap, $normName) { return $orderMap[$normName($name)] ?? 999; };
            $byPrice=$uniqueDrivers;uasort($byPrice,fn($a,$b)=>$driverOrderIdx($a['name'])<=>$driverOrderIdx($b['name']) ?: strcmp($a['name'],$b['name']));
            $byDistance=$uniqueDrivers;uasort($byDistance,fn($a,$b)=>$driverOrderIdx($a['name'])<=>$driverOrderIdx($b['name']) ?: strcmp($a['name'],$b['name']));
          @endphp
          <span class="card-count">{{ count($uniqueDrivers) }}</span>
        </div>
        <div class="sort-toggle">
          <span class="sort-label">เรียงตาม</span>
          <div class="sort-segmented">
            <button type="button" class="sort-btn active" data-sort="price" onclick="switchRankSort('price')">฿ ค่าน้ำมัน</button>
            <button type="button" class="sort-btn" data-sort="distance" onclick="switchRankSort('distance')">km ระยะทาง</button>
          </div>
        </div>
      </div>

      <div class="driver-list" id="rankListPrice">
        @php $rankNo=0; @endphp
        @forelse($byPrice as $d)
        @php $rankNo++;$avgKmlD=$d['kml_count']>0?$d['kml_sum']/$d['kml_count']:0;$kmlBad=$avgKmlD>0&&$avgKmlD<9;$thbPerKmD=$d['distance']>0?$d['price']/$d['distance']:0; @endphp
        <div class="driver-row">
          <div class="driver-rank">{{ str_pad((string)$rankNo,2,'0',STR_PAD_LEFT) }}</div>
          <div class="body"><div class="name">{{ $d['name'] }}</div><div class="stats"><span>{{ $d['rounds'] }} รอบ</span><span>·</span><span>{{ number_format($d['distance']) }} km</span><span>·</span><span>{{ rtrim(rtrim(number_format($d['liters'],2,'.',''),'0'),'.') }} L</span></div></div>
          <div class="right"><div class="price">฿{{ number_format($d['price']) }}</div>@if($avgKmlD>0)<div class="kml {{ $kmlBad?'warn':'' }}">{{ rtrim(rtrim(number_format($avgKmlD,2,'.',''),'0'),'.') }} km/L</div>@endif @if($thbPerKmD>0)<div class="thb-km">฿{{ number_format($thbPerKmD,2) }}/km</div>@endif</div>
        </div>
        @empty
        <div class="empty-state"><div class="icon">👤</div><p>ไม่มีข้อมูล</p></div>
        @endforelse
      </div>

      <div class="driver-list" id="rankListDistance" style="display:none">
        @php $rankNo=0; @endphp
        @forelse($byDistance as $d)
        @php $rankNo++;$avgKmlD=$d['kml_count']>0?$d['kml_sum']/$d['kml_count']:0;$kmlBad=$avgKmlD>0&&$avgKmlD<9;$thbPerKmD=$d['distance']>0?$d['price']/$d['distance']:0; @endphp
        <div class="driver-row">
          <div class="driver-rank">{{ str_pad((string)$rankNo,2,'0',STR_PAD_LEFT) }}</div>
          <div class="body"><div class="name">{{ $d['name'] }}</div><div class="stats"><span>{{ $d['rounds'] }} รอบ</span><span>·</span><span>฿{{ number_format($d['price']) }}</span><span>·</span><span>{{ rtrim(rtrim(number_format($d['liters'],2,'.',''),'0'),'.') }} L</span></div></div>
          <div class="right"><div class="price">{{ number_format($d['distance']) }} <span style="font-size:14px;color:var(--text3);font-weight:500">km</span></div>@if($avgKmlD>0)<div class="kml {{ $kmlBad?'warn':'' }}">{{ rtrim(rtrim(number_format($avgKmlD,2,'.',''),'0'),'.') }} km/L</div>@endif @if($thbPerKmD>0)<div class="thb-km">฿{{ number_format($thbPerKmD,2) }}/km</div>@endif</div>
        </div>
        @empty
        <div class="empty-state"><div class="icon">👤</div><p>ไม่มีข้อมูล</p></div>
        @endforelse
      </div>
    </div>
    @endif
  </div>

  <div class="charts-grid">
    <div class="chart-card">
      <div class="chart-head"><div class="chart-title">รายการสมบูรณ์ / ผิดพลาด</div><div class="chart-sub">ประสิทธิภาพการส่งสินค้าแยกตามคนขับ</div></div>
      <div class="chart-canvas" style="height:300px"><div class="chart-inner" id="deliveryChartInner"><canvas id="deliveryChart"></canvas></div></div>
      <div class="chart-legend" id="dlvLegend"></div>
    </div>
    <div class="chart-card">
      <div class="chart-head"><div class="chart-title">น้ำมันต่อกิโล</div><div class="chart-sub">เฉลี่ย km/L แต่ละคน · เกณฑ์อัตโนมัติ (ค่าเฉลี่ยรวม)</div>
        <div class="vehicle-toggle" data-chart="kml"><button type="button" class="vt-btn active" data-type="car" onclick="switchVehicleType('kml','car')">🚗 รถยนต์</button><button type="button" class="vt-btn" data-type="moto" onclick="switchVehicleType('kml','moto')">🏍 มอเตอร์ไซค์</button></div>
      </div>
      <div class="chart-canvas"><div class="chart-inner" id="chartKmlInner"><canvas id="chartKml"></canvas></div></div>
      <div class="chart-legend" id="kmlLegend"></div>
    </div>
    <div class="chart-card">
      <div class="chart-head"><div class="chart-title">ต้นทุนต่อกิโล (฿/km)</div><div class="chart-sub">ค่าน้ำมันเฉลี่ยต่อระยะทาง 1 กิโลเมตร · ยิ่งน้อยยิ่งดี</div>
        <div class="vehicle-toggle" data-chart="cost"><button type="button" class="vt-btn active" data-type="car" onclick="switchVehicleType('cost','car')">🚗 รถยนต์</button><button type="button" class="vt-btn" data-type="moto" onclick="switchVehicleType('cost','moto')">🏍 มอเตอร์ไซค์</button></div>
      </div>
      <div class="chart-canvas"><div class="chart-inner" id="chartCostInner"><canvas id="chartCost"></canvas></div></div>
      <div class="chart-legend" id="costLegend"></div>
    </div>
  </div>

  <div class="pdf-modal-overlay" id="pdfModalOverlay" onclick="if(event.target===this)closePdfRangeModal()">
    <div class="pdf-modal">
      <div class="pdf-modal-head"><span>ดาวน์โหลดรายงาน PDF</span><button type="button" class="pdf-modal-x" onclick="closePdfRangeModal()">✕</button></div>
      <div class="pdf-modal-body">
        <div class="pdf-mode-tabs"><button type="button" class="pdf-mode-btn active" data-mode="range" onclick="setPdfMode('range')">ช่วงวันที่</button><button type="button" class="pdf-mode-btn" data-mode="single" onclick="setPdfMode('single')">วันเดียว</button></div>
        <div id="pdfRangeFields"><div class="pdf-field" style="margin-bottom:14px"><label>ตั้งแต่วันที่</label><input type="date" id="pdfDateFrom"></div><div class="pdf-field"><label>ถึงวันที่</label><input type="date" id="pdfDateTo"></div></div>
        <div id="pdfSingleFields" style="display:none"><div class="pdf-field"><label>เลือกวันที่</label><input type="date" id="pdfSingleDate" value="{{ date('Y-m-d') }}"></div></div>
      </div>
      <div class="pdf-modal-foot"><button type="button" class="pdf-btn-cancel" onclick="closePdfRangeModal()">ยกเลิก</button><button type="button" class="pdf-btn-go" onclick="confirmPdfExport()"> สร้าง PDF</button></div>
    </div>
  </div>
</main>

<script>
const ROUTE_STORE='{{ route("oil") }}';
const ROUTE_FILTER='{{ route("oil.filter") }}';
const ROUTE_SYNC_NG='{{ route("oil.syncNg") }}';
const ROUTE_SAVED_DRIVERS='{{ url("/oil/saved-drivers") }}';
const ROUTE_LAST_PLATES='{{ url("/oil/last-plates") }}';

/* ===== FIX: create_by หายเวลาเปลี่ยนวันที่/มุมมอง =====
   เดิม CURRENT_USER เป็น const มาจาก PHP ครั้งเดียวตอนโหลดหน้า
   ถ้า redirect ฝั่ง backend (route oil.filter) ทำ query string
   'create_by' หลุดไป ตัวแปรนี้จะกลายเป็น 'Guest' ถาวรทันที
   และทุกฟังก์ชันเปลี่ยนวันที่/มุมมองที่เรียก submitFilterForm()
   จะหยุดแนบ create_by ไปกับ request ต่อ ๆ ไปทั้งหมด
   -> เปลี่ยนเป็น let + จำค่าไว้ใน sessionStorage เป็นตัวกันสำรอง
      ถ้า backend ทำหาย ฝั่ง JS จะดึงค่าที่จำไว้กลับมาใช้เอง
   หมายเหตุ: ควรแก้ต้นเหตุที่ controller ของ route('oil.filter')
   ให้ redirect กลับไปพร้อม create_by เสมอด้วย (ดูคำอธิบายที่แชท) */
let CURRENT_USER=@json($currentUser);
const IS_PRIVILEGED=@json($isPrivileged);
(function _persistCreateBy(){
  try{
    if(CURRENT_USER && CURRENT_USER!=='Guest'){
      sessionStorage.setItem('oilCreateBy', CURRENT_USER);
    }else{
      const saved = sessionStorage.getItem('oilCreateBy');
      if(saved){
        CURRENT_USER = saved;
        // sync กลับเข้า URL ปัจจุบันด้วย เผื่อมีการ reload หรือกด back
        const url = new URL(window.location.href);
        if(!url.searchParams.get('create_by')){
          url.searchParams.set('create_by', saved);
          window.history.replaceState({}, '', url.toString());
        }
      }
    }
  }catch(e){}
})();
const CSRF_TOKEN=document.querySelector('meta[name="csrf-token"]')?.content??'';
const TZ='Asia/Bangkok';
const MAIN_VIEW=@json($view);
const ALLOWED_DRIVERS=@json($allowedDrivers);
const DRIVER_ORDER_LIST=@json($driverOrderList);
window.PLATE_LIST=@json($plateList);

function fmtN(v,max=2){return(+(+v).toFixed(max)).toString();}
function toggleTopMenu(){document.getElementById('topMenu')?.classList.toggle('open');}
function closeMobileMenu(){if(window.innerWidth>900)return;document.getElementById('topMenu')?.classList.remove('open');}
function nowThai(){return new Date(new Date().toLocaleString('en-US',{timeZone:TZ}));}
function todayStr(){const d=nowThai();return`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;}

function submitFilterForm(params){const form=document.createElement('form');form.method='POST';form.action=ROUTE_FILTER;form.style.display='none';const add=(n,v)=>{if(v==null||v==='')return;const i=document.createElement('input');i.type='hidden';i.name=n;i.value=v;form.appendChild(i);};add('_token',CSRF_TOKEN);if(CURRENT_USER&&CURRENT_USER!=='Guest')add('create_by',CURRENT_USER);Object.keys(params).forEach(k=>add(k,params[k]));document.body.appendChild(form);form.submit();}
function switchView(v){const params={view:v};const ds=document.getElementById('driverPicker');if(ds&&ds.value)params.driver_name=ds.value;const ps=document.getElementById('platePicker');if(ps&&ps.value)params.vehicle_id=ps.value;if(v==='month'){const el=document.getElementById('monthPicker');if(el&&el.value)params.month=el.value;}else if(v==='year'){const el=document.getElementById('yearPicker');if(el&&el.value)params.year=el.value;}submitFilterForm(params);}
function submitFilter(){const params={view:MAIN_VIEW};const ds=document.getElementById('driverPicker');if(ds&&ds.value)params.driver_name=ds.value;const ps=document.getElementById('platePicker');if(ps&&ps.value)params.vehicle_id=ps.value;const me=document.getElementById('monthPicker');if(me&&me.value)params.month=me.value;const ye=document.getElementById('yearPicker');if(ye&&ye.value)params.year=ye.value;if(MAIN_VIEW==='day'){const wrap=document.querySelector('.drp-wrap');const from=drpFrom||wrap?.dataset.from;const to=drpTo||wrap?.dataset.to;if(from)params.date_from=from;if(to)params.date_to=to;}submitFilterForm(params);}
function onYearChange(){const ye=document.getElementById('yearPicker');if(ye&&ye.value){try{sessionStorage.setItem('oilPickedYear',ye.value);}catch(e){}}submitFilter();}
(function restoreYear(){try{const s=sessionStorage.getItem('oilPickedYear');if(!s)return;document.addEventListener('DOMContentLoaded',()=>{const el=document.getElementById('yearPicker');if(!el)return;if(Array.from(el.options).some(o=>o.value===s)&&el.value!==s)el.value=s;});}catch(e){}})();

let oilSearchQuery='';
function filterOilTable(q){oilSearchQuery=q.toLowerCase();renderOilPage();}
function renderOilPage(){const rows=Array.from(document.querySelectorAll('#oilTbody tr[data-driver]'));let vis=0;rows.forEach(r=>{const show=!oilSearchQuery||r.dataset.driver.includes(oilSearchQuery);r.style.display=show?'':'none';if(show)vis++;});const c=document.getElementById('oilCount');if(c)c.textContent=vis;}
function switchRankSort(mode){document.querySelectorAll('.sort-btn').forEach(b=>b.classList.toggle('active',b.dataset.sort===mode));const lp=document.getElementById('rankListPrice'),ld=document.getElementById('rankListDistance');if(lp&&ld){lp.style.display=(mode==='price')?'':'none';ld.style.display=(mode==='distance')?'':'none';}try{sessionStorage.setItem('oilRankSort',mode);}catch(e){}}
(function restoreRankSort(){try{const s=sessionStorage.getItem('oilRankSort');if(!s)return;document.addEventListener('DOMContentLoaded',()=>{if(document.getElementById('rankListPrice')&&document.getElementById('rankListDistance'))switchRankSort(s);});}catch(e){}})();

function _normalizeName(s){if(!s)return'';return String(s).replace(/[\u200B-\u200D\uFEFF]/g,'').replace(/\s+/g,' ').trim().toLowerCase();}
const DRIVER_ALIASES={'กอลฟ':'กอลฟ','กอลฟ์':'กอลฟ','แฟงค':'แฟงค','แฟรงค':'แฟงค','yuth':'yuth','ยุทร':'yuth','ยุท':'yuth','joey':'joey','โจอี':'joey','แซม':'แซม','แชม':'แซม'};
function _normalizeDriver(s){let n=_normalizeName(s).replace(/\u0E4C/g,'');return DRIVER_ALIASES[n]||n;}
const _allowedSet=new Set(ALLOWED_DRIVERS.map(_normalizeDriver));
function isAllowedDriver(name){return _allowedSet.has(_normalizeDriver(name));}
const _allowedOrderList=DRIVER_ORDER_LIST.map(_normalizeDriver);
function driverOrderIndex(name){const i=_allowedOrderList.indexOf(_normalizeDriver(name));return i<0?999:i;}

// Date Range Picker Functions
const TH_MONTHS_SHORT=['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
let drpViewYear=null,drpViewMonth=null,drpFrom=null,drpTo=null;
function drpPad(n){return String(n).padStart(2,'0');}
function drpFmt(d){return`${d.getFullYear()}-${drpPad(d.getMonth()+1)}-${drpPad(d.getDate())}`;}
function drpParse(s){if(!s)return null;const p=s.split('-');return new Date(parseInt(p[0]),parseInt(p[1])-1,parseInt(p[2]));}
function drpFormatLabel(f,t){if(!f)return'เลือกช่วง';const fd=drpParse(f);if(!t||f===t)return`${fd.getDate()} ${TH_MONTHS_SHORT[fd.getMonth()]} ${fd.getFullYear()+543}`;const td=drpParse(t);if(fd.getFullYear()===td.getFullYear()&&fd.getMonth()===td.getMonth())return`${fd.getDate()}–${td.getDate()} ${TH_MONTHS_SHORT[fd.getMonth()]} ${fd.getFullYear()+543}`;return`${fd.getDate()} ${TH_MONTHS_SHORT[fd.getMonth()]} – ${td.getDate()} ${TH_MONTHS_SHORT[td.getMonth()]} ${fd.getFullYear()+543}`;}
function drpUpdateLabel(){const lbl=document.getElementById('drpLabel');if(lbl)lbl.textContent=drpFormatLabel(drpFrom,drpTo);}
function drpPositionPopup(){const pop=document.getElementById('drpPopup'),trg=document.getElementById('drpTrigger');if(!pop||!trg)return;const r=trg.getBoundingClientRect(),popW=300,popH=380;let top=r.bottom+6;if(top+popH>window.innerHeight-8)top=Math.max(8,r.top-popH-6);let left=r.left;if(left+popW>window.innerWidth-8)left=Math.max(8,window.innerWidth-popW-8);pop.style.top=top+'px';pop.style.left=left+'px';}
function drpToggle(e){
  if(e){e.preventDefault();e.stopPropagation();}
  const pop=document.getElementById('drpPopup'),trg=document.getElementById('drpTrigger');
  if(!pop||!trg)return;
  const isOpen = pop.classList.contains('open') || pop.classList.contains('show');
  if(isOpen){
    pop.classList.remove('open','show');
    trg.classList.remove('active');
  }else{
    const a=drpFrom?drpParse(drpFrom):new Date();
    drpViewYear=a.getFullYear();
    drpViewMonth=a.getMonth();
    drpRender();
    drpPositionPopup();
    pop.classList.add('open','show');
    trg.classList.add('active');
  }
}
window.addEventListener('resize',()=>{const p=document.getElementById('drpPopup');if(p&&(p.classList.contains('open')||p.classList.contains('show')))drpPositionPopup();});
window.addEventListener('scroll',()=>{const p=document.getElementById('drpPopup');if(p&&(p.classList.contains('open')||p.classList.contains('show')))drpPositionPopup();},true);
function drpNavMonth(d){drpViewMonth+=d;if(drpViewMonth<0){drpViewMonth=11;drpViewYear--;}if(drpViewMonth>11){drpViewMonth=0;drpViewYear++;}drpRender();}
function drpRender(){document.getElementById('drpMonthTitle').textContent=`${TH_MONTHS_SHORT[drpViewMonth]} ${drpViewYear+543}`;const grid=document.getElementById('drpDays');const fw=new Date(drpViewYear,drpViewMonth,1).getDay();const dim=new Date(drpViewYear,drpViewMonth+1,0).getDate();const pmd=new Date(drpViewYear,drpViewMonth,0).getDate();const ts=drpFmt(new Date());let h='';for(let i=fw-1;i>=0;i--){const d=pmd-i;const y=drpViewMonth===0?drpViewYear-1:drpViewYear;const m=drpViewMonth===0?11:drpViewMonth-1;h+=drpDayBtn(`${y}-${drpPad(m+1)}-${drpPad(d)}`,d,true,ts);}for(let d=1;d<=dim;d++){h+=drpDayBtn(`${drpViewYear}-${drpPad(drpViewMonth+1)}-${drpPad(d)}`,d,false,ts);}const rem=(7-((fw+dim)%7))%7;for(let d=1;d<=rem;d++){const y=drpViewMonth===11?drpViewYear+1:drpViewYear;const m=drpViewMonth===11?0:drpViewMonth+1;h+=drpDayBtn(`${y}-${drpPad(m+1)}-${drpPad(d)}`,d,true,ts);}grid.innerHTML=h;const hint=document.getElementById('drpHint');hint.textContent=!drpFrom?'เลือกวันเริ่มต้น':drpFormatLabel(drpFrom,drpTo);document.getElementById('drpApplyBtn').disabled=!drpFrom;}
function drpDayBtn(ds,d,muted,ts){const c=['drp-day'];if(muted)c.push('muted');if(ds===ts)c.push('today');if(drpFrom&&drpTo){if(ds===drpFrom&&ds===drpTo)c.push('selected');else if(ds===drpFrom)c.push('range-start');else if(ds===drpTo)c.push('range-end');else if(ds>drpFrom&&ds<drpTo)c.push('in-range');}else if(drpFrom&&ds===drpFrom)c.push('selected');return`<button type="button" class="${c.join(' ')}" data-date="${ds}">${d}</button>`;}

function drpPreset(p){const n=new Date();let f,t;if(p==='today'){f=t=drpFmt(n);}else if(p==='7days'){t=drpFmt(n);const d=new Date(n);d.setDate(d.getDate()-6);f=drpFmt(d);}else if(p==='thismonth'){f=drpFmt(new Date(n.getFullYear(),n.getMonth(),1));t=drpFmt(n);}drpFrom=f;drpTo=t;const fd=drpParse(f);drpViewYear=fd.getFullYear();drpViewMonth=fd.getMonth();drpRender();}
async function drpApply(){
  if(!drpFrom)return;
  const to=drpTo||drpFrom;
  const pop=document.getElementById('drpPopup'),trg=document.getElementById('drpTrigger');
  if(pop)pop.classList.remove('open','show');
  if(trg)trg.classList.remove('active');
  const wrap=document.querySelector('.drp-wrap[data-from]');
  const prevFrom=wrap?.dataset.from||'';
  const prevTo=wrap?.dataset.to||'';
  drpUpdateLabel();
  if(drpFrom===prevFrom && to===prevTo)return; // วันที่ไม่เปลี่ยน ไม่ต้องทำอะไร
  if(wrap){wrap.dataset.from=drpFrom;wrap.dataset.to=to;}
  const params={view:'day',date_from:drpFrom,date_to:to};
  const ds=document.getElementById('driverPicker');if(ds&&ds.value)params.driver_name=ds.value;
  const ps=document.getElementById('platePicker');if(ps&&ps.value)params.vehicle_id=ps.value;
  submitFilterForm(params);
}
document.addEventListener('click',(e)=>{const pop=document.getElementById('drpPopup'),trg=document.getElementById('drpTrigger');if(!pop||!(pop.classList.contains('open')||pop.classList.contains('show')))return;if(pop.contains(e.target)||trg.contains(e.target))return;pop.classList.remove('open','show');trg.classList.remove('active');});
function drpInit(){
  const wrap=document.querySelector('.drp-wrap[data-from]');if(!wrap)return;
  if(wrap.dataset.from)drpFrom=wrap.dataset.from;
  if(wrap.dataset.to)drpTo=wrap.dataset.to;
  drpUpdateLabel();
  const grid=document.getElementById('drpDays');
  if(grid){
    grid.addEventListener('click',(e)=>{
      const btn=e.target.closest('.drp-day');
      if(!btn)return;
      const ds=btn.dataset.date;
      if(!ds)return;
      if(!drpFrom || (drpFrom && drpTo)){
        drpFrom=ds;
        drpTo=ds;
        drpRender();
        setTimeout(()=>drpApply(), 200);
      }else if(drpFrom && !drpTo){
        if(ds < drpFrom){
          drpTo=drpFrom;
          drpFrom=ds;
        }else{
          drpTo=ds;
        }
        drpRender();
        setTimeout(()=>drpApply(), 200);
      }
    });
  }
}

// Oil Price Functions (แก้บั๊ก self-reference promise แล้ว)
let OIL_PRICE_CACHE = null;
let _oilFetchPromise = null;
async function _fetchOilOnce() {
  if (OIL_PRICE_CACHE) return OIL_PRICE_CACHE;
  if (_oilFetchPromise) return _oilFetchPromise;
  _oilFetchPromise = (async () => {
    try {
      const r = await Promise.race([fetch('/oil/oil-price-proxy'), new Promise((_, rj) => setTimeout(() => rj(new Error('timeout')), 8000))]);
      if (r.ok) {
        const json = await r.json();
        const data = Array.isArray(json) ? json[0] : json;
        if (data && data.OilList) {
          const oils = typeof data.OilList === 'string' ? JSON.parse(data.OilList) : data.OilList;
          OIL_PRICE_CACHE = oils;
        }
      }
    } catch (_) { OIL_PRICE_CACHE = null; }
    return OIL_PRICE_CACHE;
  })();
  const result = await _oilFetchPromise;
  _oilFetchPromise = null;
  return result;
}
function _extractPrice(oils, cfg) {
  if (!oils || !Array.isArray(oils)) return null;
  for (const oil of oils) {
    if (oil.OilName && oil.OilName.toLowerCase().includes(cfg.matchKey)) {
      const p = parseFloat(oil.PriceToday);
      if (!isNaN(p) && p > 0) return p;
    }
  }
  return null;
}
const OIL_CONFIG = {
  'diesel': { label: 'ดีเซล', matchKey: 'hi diesel s' },
  '95': { label: 'แก๊สโซฮอล์ 95', matchKey: 'gasohol 95' },
  'benzin95': { label: 'Hi Premium 98', matchKey: 'hi premium 98' },
  '91': { label: 'แก๊สโซฮอล์ 91', matchKey: 'gasohol 91' },
  'e20': { label: 'แก๊สโซฮอล์ E20', matchKey: 'gasohol e20' },
  'e85': { label: 'แก๊สโซฮอล์ E85', matchKey: 'gasohol e85' }
};
let ilCurrentOilType='diesel';
function ilSwitchOilType(t){ilCurrentOilType=t;document.querySelectorAll('.entry-oil-tab').forEach(b=>b.classList.remove('active'));document.getElementById('ilBtnOil-'+t)?.classList.add('active');ilLoadOilPrice(t);}
async function ilRefreshOilPrice(){OIL_PRICE_CACHE=null;_oilFetchPromise=null;const btn=document.getElementById('ilBtnRefresh');if(btn){btn.disabled=true;btn.style.opacity='.5';}await ilLoadOilPrice(ilCurrentOilType);if(btn){btn.disabled=false;btn.style.opacity='1';}}
async function ilLoadOilPrice(type){const cfg=OIL_CONFIG[type]??OIL_CONFIG['diesel'];const labelEl=document.getElementById('ilOilPriceLabel');if(labelEl)labelEl.textContent=`ราคา${cfg.label}`;const showEl=document.getElementById('ilOilPriceShow');if(showEl)showEl.textContent='...';const statusEl=document.getElementById('ilOilPriceStatus');if(statusEl)statusEl.textContent='กำลังดึง';const wrapEl=document.getElementById('ilLiveWrap');if(wrapEl)wrapEl.classList.add('loading');const liveLabel=document.getElementById('ilLiveLabel');if(liveLabel)liveLabel.textContent='กำลังดึง';const pplEl=document.getElementById('il-price-per-liter');if(pplEl)pplEl.value='';const stations=await _fetchOilOnce();const fetched=_extractPrice(stations,cfg);const now=new Date().toLocaleTimeString('th-TH',{timeZone:TZ,hour:'2-digit',minute:'2-digit',hour12:false});if(fetched){if(showEl)showEl.textContent=fetched.toFixed(2);if(statusEl)statusEl.textContent=`อัปเดต ${now}`;if(wrapEl)wrapEl.classList.remove('loading');if(liveLabel)liveLabel.textContent='Live';if(pplEl)pplEl.value=fetched.toFixed(2);}else{if(showEl)showEl.textContent='—';if(statusEl)statusEl.textContent=`ดึงไม่ได้ ${now}`;if(wrapEl)wrapEl.classList.remove('loading');if(liveLabel)liveLabel.textContent='ออฟไลน์';if(pplEl)pplEl.value='';}if(typeof erUpdateAllRows==='function')erUpdateAllRows();}

// Jobs & Saved Drivers
const JOB_API_BASE='http://server_update:8000/api/getDeliveryPersonByDate';
const jobFetched={};const JOBS_PROCESSED={};
async function fetchJobsByDate(dateStr){if(jobFetched[dateStr])return;jobFetched[dateStr]=true;let drivers=[];try{const res=await fetch(`${JOB_API_BASE}?date=${dateStr}`);if(!res.ok)throw new Error('HTTP '+res.status);const json=await res.json();drivers=(json.data||[]).map(b=>({driver_name:b.bill_out_by||'ไม่ระบุ',jobs:(b.jobs||[]).map(j=>({bill_no:j.bill_no||'',so_id:j.so_id||'',customer_name:j.customer_name||'',bill_in_by:j.bill_in_by||'',status:j.delivery_status||'',note:j.reason||''}))}));}catch(e){console.warn('fetchJobsByDate:',e);drivers=[];}const whitelist={},auto={};drivers.forEach(d=>{const rawName=(d.driver_name||'').trim();if(!rawName)return;const allowed=isAllowedDriver(rawName);const bucket=allowed?whitelist:auto;/* รวมชื่อที่สะกด/เว้นวรรค/อักขระที่มองไม่เห็นต่างกันเล็กน้อยให้เป็นคนเดียวกัน กันไม่ให้ขึ้นซ้ำเป็นสองแถว */const dedupKey=allowed?_normalizeDriver(rawName):_normalizeName(rawName);if(!bucket[dedupKey]){let displayName=rawName;if(allowed){const canon=ALLOWED_DRIVERS.find(nm=>_normalizeDriver(nm)===dedupKey);if(canon)displayName=canon;}bucket[dedupKey]={name:displayName,jobs:[]};}(d.jobs||[]).forEach(j=>bucket[dedupKey].jobs.push(j));});JOBS_PROCESSED[dateStr]={whitelist,auto};}

const SAVED_DRIVERS_CACHE={};const SESSION_SAVED={};
function _readSavedDriversFromDOM(date){const set=new Set();if(!date)return set;const parts=date.split('-');if(parts.length!==3)return set;const target=`${parts[2]}/${parts[1]}/${parts[0]}`;document.querySelectorAll('#oilTbody tr[data-driver]').forEach(tr=>{const dateEl=tr.querySelector('.date-pill'),nameEl=tr.querySelector('.driver-name');if(!dateEl||!nameEl)return;if((dateEl.getAttribute('title')||'').trim()===target){const name=(nameEl.textContent||'').trim();if(name&&name!=='—')set.add(name);}});return set;}
async function fetchSavedDrivers(date){if(!date)return new Set();if(SAVED_DRIVERS_CACHE[date])return SAVED_DRIVERS_CACHE[date];const fromDOM=_readSavedDriversFromDOM(date);try{const res=await fetch(`${ROUTE_SAVED_DRIVERS}?date=${encodeURIComponent(date)}`,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}).catch(()=>null);if(res&&res.ok){const data=await res.json();let raw=[];if(Array.isArray(data))raw=data;else if(Array.isArray(data.drivers))raw=data.drivers;else if(Array.isArray(data.data))raw=data.data;else if(Array.isArray(data.saved))raw=data.saved;else if(Array.isArray(data.result))raw=data.result;raw.forEach(item=>{let n='';if(typeof item==='string')n=item.trim();else if(item&&typeof item==='object')n=(item.driver_name||item.name||item.driver||'').toString().trim();if(n)fromDOM.add(n);});}}catch(e){}SAVED_DRIVERS_CACHE[date]=fromDOM;return fromDOM;}
function isDriverSaved(date,driverName){const n=_normalizeName(driverName);if(!n||!date)return false;for(const set of[SAVED_DRIVERS_CACHE[date],SESSION_SAVED[date]]){if(set){for(const saved of set){if(_normalizeName(saved)===n)return true;}}}return false;}
function markDriverSaved(date,driverName){const n=(driverName||'').trim();if(!n||!date)return;if(!SESSION_SAVED[date])SESSION_SAVED[date]=new Set();SESSION_SAVED[date].add(n);if(!SAVED_DRIVERS_CACHE[date])SAVED_DRIVERS_CACHE[date]=new Set();SAVED_DRIVERS_CACHE[date].add(n);}
function _jobStatusKind(j){const raw=(j.status||'').trim();const noteText=(j.note||'').trim();const eff=(noteText==='ส่งสำเร็จ'||noteText==='สำเร็จ')?'ส่งสำเร็จ':raw;if(eff.includes('สำเร็จ')&&!eff.includes('ไม่'))return'ok';if(eff.includes('ไม่สำเร็จ')||eff.toLowerCase()==='ng'||eff.toLowerCase()==='fail')return'fail';return'pending';}

// Entry Rows Functions
const driverRowState={};let ilIsLoadingDrivers=false,ilLastLoadedDate=null;
async function ilOnDateChange(){if(ilIsLoadingDrivers)return-1;const date=document.getElementById('il-work-date').value;if(!date)return-1;ilLastLoadedDate=date;ilIsLoadingDrivers=true;const hint=document.getElementById('entryLoadingHint');if(hint)hint.style.display='flex';document.getElementById('entryRowsBody').innerHTML='';document.getElementById('inlineJobTableWrap').innerHTML='<div class="job-loading">กำลังโหลด...</div>';let pendingCount=-1;try{delete SAVED_DRIVERS_CACHE[date];delete jobFetched[date];delete JOBS_PROCESSED[date];await Promise.all([fetchJobsByDate(date),fetchSavedDrivers(date)]);const proc=JOBS_PROCESSED[date]||{whitelist:{},auto:{}};const unsaved=Object.values(proc.whitelist).filter(d=>!isDriverSaved(date,d.name));pendingCount=unsaved.length;ilRenderDriverRows(date);ilResetJobsPanel();}catch(e){console.warn(e);document.getElementById('entryRowsBody').innerHTML='<tr><td colspan="8" class="entry-empty" style="color:var(--red)">โหลดข้อมูลไม่สำเร็จ</td></tr>';}finally{ilIsLoadingDrivers=false;if(hint)hint.style.display='none';}return pendingCount;}

function isLikelyDriverName(name){const n=(name||'').trim();if(!n||n.length>20)return false;const banned=['ลูกค้า','เซ็นบิล','เซ็น','บิล','สาขา','จำกัด','บริษัท','หจก','ร้าน','คุณ','ไป','ที่','กับ'];for(const w of banned){if(n.includes(w))return false;}if((n.match(/\d/g)||[]).length>=4)return false;return true;}
const _autoStoreInFlight=new Set();
async function ilAutoStoreNonWhitelist(date,driverList){
  if(!driverList||driverList.length===0||!IS_PRIVILEGED)return;
  for(const d of driverList){
    const name=d.name;
    if(!isLikelyDriverName(name))continue;
    if(!d.jobs||d.jobs.length===0)continue;
    if(isDriverSaved(date,name))continue;
    const fireKey=date+'|'+_normalizeName(name);
    if(_autoStoreInFlight.has(fireKey))continue;
    _autoStoreInFlight.add(fireKey);
    
    let okC=0,failC=0;
    d.jobs.forEach(j=>{const k=_jobStatusKind(j);if(k==='ok')okC++;else if(k==='fail')failC++;});
    
    const fd = new FormData();
    fd.append('_token', CSRF_TOKEN);
    fd.append('work_date', date);
    fd.append('driver_name', name);
    fd.append('vehicle_id', '-');
    fd.append('start_time', date + ' 09:00:00');
    fd.append('end_time', date + ' 18:00:00');
    fd.append('total_price', 0);
    fd.append('total_distance', 0);
    fd.append('liters', 0);
    fd.append('delivery_cost', 0);
    fd.append('ot_cost', 0);
    fd.append('handling_cost', 0);
    fd.append('ok', okC);
    fd.append('ng', failC);
    fd.append('create_by', (CURRENT_USER && CURRENT_USER !== 'Guest') ? String(CURRENT_USER) : 'system');

    try{
      const res=await fetch(ROUTE_STORE,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},body:fd});
      if(res.ok||res.status===302){
        markDriverSaved(date,name);
        ilAppendLogRow({date,driver_name:name,vehicle_id:'-',start_h:9,start_m:0,end_h:18,end_m:0,total_price:0,total_distance:0,liters:0,km_per_liter:0,ok_count:okC,fail_count:failC,delivery_cost:0,ot_cost:0,handling_cost:0});
        if(d.jobs.length>0)_syncNgJobs(date,name,d.jobs);
      }
    }catch(e){
      console.warn('auto-store error',name,e);
      _autoStoreInFlight.delete(fireKey);
    }
  }
}

function _syncNgJobs(date, driverName, jobs) {
  if (!date || !driverName || !Array.isArray(jobs) || jobs.length === 0) return;
  
  const payload = {
    date: String(date),
    create_by: (CURRENT_USER && CURRENT_USER !== 'Guest') ? String(CURRENT_USER) : 'system',
    jobs: jobs.map(j => {
      let st = j.status ? String(j.status).trim() : '';
      if (st === '') st = 'รอ'; 
      
      return {
        bill_no: String(j.bill_no || ''),
        so_id: String(j.so_id || ''),
        driver_name: String(driverName),
        bill_in_by: String(j.bill_in_by || ''),
        customer_name: String(j.customer_name || ''),
        status: st,
        note: String(j.note || '')
      };
    })
  };

  fetch(ROUTE_SYNC_NG, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': CSRF_TOKEN
    },
    body: JSON.stringify(payload)
  })
  .then(async res => {
    if (!res.ok) {
      const errText = await res.text().catch(() => 'Unknown error');
      console.warn('sync-ng failed:', res.status, errText);
    }
  })
  .catch(err => console.warn('sync-ng network error:', err));
}

let _lastPlatesCache = null;
async function _fetchLastPlates() {
  if (_lastPlatesCache) return _lastPlatesCache;
  try { const res = await fetch(ROUTE_LAST_PLATES); if (res.ok) _lastPlatesCache = await res.json(); } catch (e) { _lastPlatesCache = {}; }
  return _lastPlatesCache || {};
}
function _autoSelectPlates() {
  if (!_lastPlatesCache) return;
  document.querySelectorAll('.er-plate-select[data-key]').forEach(sel => {
    if (sel.value) return;
    const key = sel.dataset.key, s = driverRowState[key];
    if (!s) return;
    let plate = _lastPlatesCache[s.driverName];
    if (!plate) {
      const normTarget = _normalizeName(s.driverName);
      for (const [k, v] of Object.entries(_lastPlatesCache)) { if (_normalizeName(k) === normTarget) { plate = v; break; } }
    }
    if (plate) { for (const opt of sel.options) { if (opt.value === plate) { sel.value = plate; erUpdateRow(key); break; } } }
  });
}

// ===== ร่างข้อมูลทั้งแถว (Row Draft) — เก็บฝั่งเบราว์เซอร์ด้วย sessionStorage
// ทุกช่อง (เวลา/ทะเบียน/ค่าน้ำมัน/ระยะ/ค่าใช้จ่ายเพิ่มเติม/ไม่เติมน้ำมัน) จะถูกเก็บอัตโนมัติทันทีที่พิมพ์หรือเปลี่ยนค่า
// ไม่ต้องกดปุ่มอะไรเพิ่ม และไม่ยิง request ไป backend เลย — ต่อให้ "รีเฟรชหน้าเว็บ" ระหว่างกรอก ข้อมูลก็จะไม่หาย
// เพราะตอน render แถวใหม่ ระบบจะดึงร่างนี้กลับมาเติมในช่องให้อัตโนมัติ
// (ข้อมูลจริงจะถูกส่งไป backend พร้อมกันตอนกด "บันทึก" หรือ "บันทึกทั้งหมด" ตามปกติ แล้วร่างจะถูกล้างทิ้ง) =====
function _rowDraftKey(date,driverName){return `oilRowDraft::${date}::${driverName}`;}
function _getRowDraft(date,driverName){try{const raw=sessionStorage.getItem(_rowDraftKey(date,driverName));return raw?JSON.parse(raw):null;}catch(e){return null;}}
function _setRowDraft(date,driverName,data){try{sessionStorage.setItem(_rowDraftKey(date,driverName),JSON.stringify(data));}catch(e){}}
function _clearRowDraft(date,driverName){try{sessionStorage.removeItem(_rowDraftKey(date,driverName));}catch(e){}}

function erPersistDraft(key, markTimeEdited){
  const s=driverRowState[key];if(!s||!s.startDT||!s.endDT)return;
  const date=s.startDT.split('T')[0];
  const plate=document.querySelector(`.er-plate-select[data-key="${key}"]`)?.value||'';
  const priceRaw=document.getElementById(`${key}-price`)?.value||'';
  const distRaw=document.getElementById(`${key}-dist`)?.value||'';
  const delRaw=document.getElementById(`${key}-delivery`)?.value||'';
  const otRaw=document.getElementById(`${key}-ot`)?.value||'';
  const hanRaw=document.getElementById(`${key}-handling`)?.value||'';
  const existing=_getRowDraft(date,s.driverName);
  const timeEdited = markTimeEdited===true ? true : !!(existing&&existing.timeEdited);
  _setRowDraft(date,s.driverName,{
    startDT:s.startDT, endDT:s.endDT, plate, noFuel:!!s.noFuel,
    price:priceRaw, distance:distRaw, delivery:delRaw, ot:otRaw, handling:hanRaw,
    timeEdited
  });
}
// ซิงก์ state จาก DOM เฉยๆ ไม่แตะ badge/ไม่นับว่า "แก้เวลา" — ใช้ตอน render ครั้งแรกของแถว
function erSyncDateTimeState(key){
  const s=driverRowState[key];if(!s)return;
  s.startDT=document.getElementById(`${key}-start-dt`)?.value||'';
  s.endDT=document.getElementById(`${key}-end-dt`)?.value||'';
  if(s.startDT){const[h,m]=(s.startDT.split('T')[1]||'00:00').split(':').map(Number);s.sh=h||0;s.sm=m||0;}
  if(s.endDT){const[h,m]=(s.endDT.split('T')[1]||'00:00').split(':').map(Number);s.eh=h||0;s.em=m||0;}
}

function ilRenderDriverRows(date){
  const tbody=document.getElementById('entryRowsBody');if(!tbody)return;
  const proc=JOBS_PROCESSED[date]||{whitelist:{},auto:{}};
  const driversMap=proc.whitelist, autoDriversMap=proc.auto;
  ilAutoStoreNonWhitelist(date,Object.values(autoDriversMap));
  let driverList=Object.values(driversMap).filter(d=>!isDriverSaved(date,d.name));
  const totalCount=Object.keys(driversMap).length, savedCount=totalCount-driverList.length;

  /* เรียงรายชื่อคนขับตามลำดับ ALLOWED_DRIVERS */
  driverList.sort((a,b) => {
    const ia = DRIVER_ORDER_LIST.map(_normalizeDriver).indexOf(_normalizeDriver(a.name));
    const ib = DRIVER_ORDER_LIST.map(_normalizeDriver).indexOf(_normalizeDriver(b.name));
    const oa = ia < 0 ? 999 : ia;
    const ob = ib < 0 ? 999 : ib;
    if (oa !== ob) return oa - ob;
    return a.name.localeCompare(b.name, 'th'); // ชื่อที่ไม่อยู่ในลิสต์ ให้ต่อท้ายและเรียง ก-ฮ กันเอง
  });

  if(driverList.length===0){
    if(totalCount===0){tbody.innerHTML='<tr><td colspan="8" class="entry-empty">ไม่พบคนขับสำหรับวันที่นี้</td></tr>';document.getElementById('entrySub').textContent='ไม่พบคนขับของวันนี้';}
    else{tbody.innerHTML=`<tr><td colspan="8" class="entry-empty" style="color:var(--green-dark)">✓ บันทึกครบทุกคนแล้ว (${savedCount} คน)</td></tr>`;document.getElementById('entrySub').textContent=`บันทึกครบ ${savedCount} คน`;}
    erRefreshSaveAllBadge();
    return;
  }
  let subText=`${driverList.length} คนขับ · กรอกข้อมูลแล้วกดบันทึก`;
  if(savedCount>0)subText+=` · บันทึกแล้ว ${savedCount} คน`;
  document.getElementById('entrySub').textContent=subText;
  const plateOpts=(window.PLATE_LIST||[]).map(p=>`<option value="${p}">${p}</option>`).join('');
  const _wd=document.getElementById('il-work-date')?.value||todayStr();
  tbody.innerHTML=driverList.map((d,idx)=>{
    const key=`row_${idx}_${d.name.replace(/[^a-zA-Z0-9ก-๙]/g,'_')}`;
    let okC=0,failC=0;d.jobs.forEach(j=>{const k=_jobStatusKind(j);if(k==='ok')okC++;else if(k==='fail')failC++;});
    driverRowState[key]={
      driverName:d.name, jobs:d.jobs, okCount:okC, failCount:failC,
      sh:0, sm:0, eh:0, em:0, startDT:'', endDT:'', noFuel:false,
      deliveryCost:0, otCost:0, handlingCost:0
    };
    const ini=(d.name||'?').trim().charAt(0).toUpperCase();
    // ดึงร่างที่เคยกรอกไว้ (ถ้ามี) กลับมาเติมทุกช่องอัตโนมัติ กันข้อมูลหายตอนรีเฟรชหน้าเว็บ
    const _draft=_getRowDraft(_wd, d.name);
    const _startVal=(_draft&&_draft.startDT)?_draft.startDT:`${_wd}T09:00`;
    const _endVal=(_draft&&_draft.endDT)?_draft.endDT:`${_wd}T18:00`;
    const _dPrice=(_draft&&_draft.price)?_draft.price:'';
    const _dDist=(_draft&&_draft.distance)?_draft.distance:'';
    const _dDelivery=(_draft&&_draft.delivery)?_draft.delivery:'';
    const _dOt=(_draft&&_draft.ot)?_draft.ot:'';
    const _dHandling=(_draft&&_draft.handling)?_draft.handling:'';
    const _dNoFuel=!!(_draft&&_draft.noFuel);
    if(_draft)driverRowState[key].noFuel=_dNoFuel;
    const _dPlate=(_draft&&_draft.plate)?_draft.plate:'';
    const _plateOptsRow=(window.PLATE_LIST||[]).map(p=>`<option value="${p}" ${p===_dPlate?'selected':''}>${p}</option>`).join('');
    return`<tr class="entry-row" data-key="${key}" onclick="erFocusRow('${key}')">
      <td data-label="คนขับ"><div class="er-driver"><span class="er-driver-avatar">${ini}</span><div class="er-driver-info"><div class="er-driver-name" title="${d.name}">${d.name}</div><div class="er-driver-jobs">${d.jobs.length} งาน · <span class="er-ok">${okC} ✓</span>${failC>0?` · <span class="er-fail">${failC} ✕</span>`:''}</div></div></div></td>
      <td data-label="ทะเบียนรถ"><select class="er-plate-select" data-key="${key}" onchange="erUpdateRow('${key}')" onfocus="erFocusRow('${key}')"><option value="">— เลือกทะเบียน —</option>${_plateOptsRow}</select></td>
      <td data-label="เวลา">
        <div class="er-time-stack">
          <div class="time-input-wrapper">
            <input type="datetime-local" class="er-dt-input" id="${key}-start-dt" value="${_startVal}" onchange="erUpdateDateTime('${key}')" onfocus="erFocusRow('${key}')">
          </div>
          <div class="time-input-wrapper">
            <input type="datetime-local" class="er-dt-input" id="${key}-end-dt" value="${_endVal}" onchange="erUpdateDateTime('${key}')" onfocus="erFocusRow('${key}')">
          </div>
          <span class="er-draft-badge" id="${key}-draft-badge" style="display:${(_draft&&_draft.timeEdited)?'':'none'}">💾 จดจำเวลาไว้แล้ว</span>
        </div>
      </td>
      <td data-label="ค่าน้ำมัน (฿)">
        <div class="er-cell-center">
          <label class="er-nofuel-check"><input type="checkbox" ${_dNoFuel?'checked':''} onchange="erToggleNoFuel('${key}',this.checked)" onfocus="erFocusRow('${key}')"> ไม่เติมน้ำมัน</label>
          <input type="text" inputmode="decimal" class="er-num-input" id="${key}-price" placeholder="${_dNoFuel?'ไม่เติม':'ค่าน้ำมัน'}" value="${_dNoFuel?'':_dPrice}" ${_dNoFuel?'disabled':''} oninput="erSanitizeNum(this);erUpdateRow('${key}')" onfocus="erFocusRow('${key}')">
        </div>
      </td>
      <td data-label="ระยะ (KM)">
        <div class="er-cell-center">
          <div class="er-nofuel-spacer"></div>
          <input type="text" inputmode="decimal" class="er-num-input" id="${key}-dist" placeholder="250" value="${_dDist}" oninput="erSanitizeNum(this);erUpdateRow('${key}')" onfocus="erFocusRow('${key}')">
        </div>
      </td>
      <td data-label="ค่าใช้จ่ายเพิ่มเติม (฿)">
        <div class="er-extra-costs">
        <div class="er-extra-item">
          <label>ค่าวิ่ง</label>
          <input type="text" inputmode="decimal" class="er-num-input er-num-sm" id="${key}-delivery" placeholder="" value="${_dDelivery}" oninput="erSanitizeNum(this);erUpdateRow('${key}')" onfocus="erFocusRow('${key}')">
        </div>
        <div class="er-extra-item">
          <label>OT</label>
          <input type="text" inputmode="decimal" class="er-num-input er-num-sm" id="${key}-ot" placeholder="" value="${_dOt}" oninput="erSanitizeNum(this);erUpdateRow('${key}')" onfocus="erFocusRow('${key}')">
        </div>
        <div class="er-extra-item">
          <label>ค่ายก</label>
          <input type="text" inputmode="decimal" class="er-num-input er-num-sm" id="${key}-handling" placeholder="" value="${_dHandling}" oninput="erSanitizeNum(this);erUpdateRow('${key}')" onfocus="erFocusRow('${key}')">
        </div>
        </div>
      </td>
      <td data-label="สรุป" class="er-summary" id="${key}-summary">
        <div class="er-summary-row"><span class="er-summary-label">L:</span><span class="er-summary-val empty">—</span></div>
        <div class="er-summary-row"><span class="er-summary-label">km/L:</span><span class="er-summary-val empty">—</span></div>
        <div class="er-summary-row"><span class="er-summary-label">฿/km:</span><span class="er-summary-val empty">—</span></div>
      </td>
      <td data-label="บันทึก" style="text-align:center"><button type="button" class="er-save-btn" id="${key}-save" onclick="event.stopPropagation();erSaveRow('${key}')">บันทึก</button></td>
    </tr>`;
  }).join('');
  Object.keys(driverRowState).forEach(k=>{if(document.getElementById(`${k}-start-dt`))erSyncDateTimeState(k);if(document.getElementById(`${k}-price`))erUpdateRow(k);});
  _fetchLastPlates().then(() => _autoSelectPlates());
  erRefreshSaveAllBadge();
}

function erSanitizeNum(el){let v=el.value.replace(/[^0-9.]/g,'');const parts=v.split('.');if(parts.length>2)v=parts[0]+'.'+parts.slice(1).join('');el.value=v;}
function erToggleNoFuel(key,checked){const s=driverRowState[key];if(!s)return;s.noFuel=checked;const priceEl=document.getElementById(`${key}-price`);if(priceEl){if(checked){priceEl.value='';priceEl.disabled=true;priceEl.placeholder='ไม่เติม';}else{priceEl.disabled=false;priceEl.placeholder='ค่าน้ำมัน';}}erUpdateRow(key);}
function erUpdateRow(key){
  const s=driverRowState[key];if(!s)return;
  const priceEl=document.getElementById(`${key}-price`);
  const distEl=document.getElementById(`${key}-dist`);
  const deliveryEl=document.getElementById(`${key}-delivery`);
  const otEl=document.getElementById(`${key}-ot`);
  const handlingEl=document.getElementById(`${key}-handling`);
  
  const price=s.noFuel?0:(parseFloat(priceEl?.value)||0);
  const dist=parseFloat(distEl?.value)||0;
  const deliveryCost=parseFloat(deliveryEl?.value)||0;
  const otCost=parseFloat(otEl?.value)||0;
  const handlingCost=parseFloat(handlingEl?.value)||0;
  
  const ppl=parseFloat(document.getElementById('il-price-per-liter')?.value)||0;
  const liters=(price>0&&ppl>0)?(price/ppl):0;
  const kml=(dist>0&&liters>0)?(dist/liters):0;
  const thbKm=(price>0&&dist>0)?(price/dist):0;
  
  s.price=price; s.distance=dist; s.liters=liters; s.kml=kml; s.thbKm=thbKm;
  s.deliveryCost=deliveryCost; s.otCost=otCost; s.handlingCost=handlingCost;
  
  const sum=document.getElementById(`${key}-summary`);
  if(sum){
    const litersTxt=liters>0?fmtN(liters)+' L':'<span class="empty">—</span>';
    let kmlCls='empty',kmlTxt='—';
    if(kml>0){kmlTxt=fmtN(kml)+' km/L';kmlCls=kml>=12?'green':(kml<9?'red':'');}
    const thbKmTxt=thbKm>0?'฿'+fmtN(thbKm):'<span class="empty">—</span>';
    
    let extraCosts = [];
    if(deliveryCost > 0) extraCosts.push(`วิ่ง: ฿${fmtN(deliveryCost)}`);
    if(otCost > 0) extraCosts.push(`OT: ฿${fmtN(otCost)}`);
    if(handlingCost > 0) extraCosts.push(`ยก: ฿${fmtN(handlingCost)}`);
    const extraHtml = extraCosts.length > 0 ? `<div class="er-summary-row" style="margin-top:4px;border-top:1px dashed #e5e7eb;padding-top:4px;"><span class="er-summary-label" style="font-size:11px">อื่นๆ:</span><span class="er-summary-val" style="font-size:11px;color:#3e6ae1">${extraCosts.join(', ')}</span></div>` : '';

    sum.innerHTML=`
      <div class="er-summary-row"><span class="er-summary-label">L:</span><span class="er-summary-val ${liters>0?'':'empty'}">${litersTxt}</span></div>
      <div class="er-summary-row"><span class="er-summary-label">km/L:</span><span class="er-summary-val ${kmlCls}">${kmlTxt}</span></div>
      <div class="er-summary-row"><span class="er-summary-label">฿/km:</span><span class="er-summary-val ${thbKm>0?'':'empty'}">${thbKmTxt}</span></div>
      ${extraHtml}
    `;
  }
  erPersistDraft(key);
  erRefreshSaveAllBadge();
}
function erUpdateAllRows(){Object.keys(driverRowState).forEach(k=>erUpdateRow(k));}

// ---- ตรวจว่าแถวไหน "ข้อมูลครบพร้อมบันทึก" แล้ว (ใช้ทั้งตอนโชว์ตัวเลขบนปุ่ม และตอนบันทึกทั้งหมดจริง) ----
function erValidateRow(key){
  const s=driverRowState[key];
  if(!s)return{valid:false,reason:'ไม่พบข้อมูล'};
  const plate=document.querySelector(`.er-plate-select[data-key="${key}"]`)?.value||'';
  if(!plate)return{valid:false,plate,reason:'ไม่ได้เลือกทะเบียน'};
  if(!s.noFuel){
    const priceRaw=document.getElementById(`${key}-price`)?.value??'';
    if(priceRaw===''||isNaN(parseFloat(priceRaw)))return{valid:false,plate,reason:'ไม่ได้กรอกค่าน้ำมัน'};
    if(parseFloat(priceRaw)<0)return{valid:false,plate,reason:'ค่าน้ำมันติดลบ'};
  }
  const delRaw=document.getElementById(`${key}-delivery`)?.value??'';
  const otRaw=document.getElementById(`${key}-ot`)?.value??'';
  const hanRaw=document.getElementById(`${key}-handling`)?.value??'';
  if(delRaw!==''&&isNaN(parseFloat(delRaw)))return{valid:false,plate,reason:'ค่าวิ่งไม่ถูกต้อง'};
  if(otRaw!==''&&isNaN(parseFloat(otRaw)))return{valid:false,plate,reason:'ค่า OT ไม่ถูกต้อง'};
  if(hanRaw!==''&&isNaN(parseFloat(hanRaw)))return{valid:false,plate,reason:'ค่ายกไม่ถูกต้อง'};
  if(!s.startDT||!s.endDT)return{valid:false,plate,reason:'ไม่ได้เลือกวันเวลา'};
  if(new Date(s.endDT)<=new Date(s.startDT))return{valid:false,plate,reason:'เวลาสิ้นสุดต้องหลังเวลาเริ่ม'};
  return{valid:true,plate};
}
function erCountReadyRows(){return Object.keys(driverRowState).filter(k=>erValidateRow(k).valid).length;}
function erRefreshSaveAllBadge(){
  const btn=document.getElementById('ilBtnSaveAll');if(!btn||btn.dataset.saving==='1')return;
  const label=btn.querySelector('.sa-label');if(!label)return;
  const n=erCountReadyRows();
  label.textContent=n>0?`บันทึกทั้งหมด (${n})`:'บันทึกทั้งหมด';
}
function erUpdateDateTime(key){
  erSyncDateTimeState(key);
  erPersistDraft(key, true);
  const badge=document.getElementById(`${key}-draft-badge`);
  if(badge){badge.style.display='';badge.textContent='💾 จดจำเวลาไว้แล้ว';}
  erRefreshSaveAllBadge();
}
let _focusedRowKey=null;
function erFocusRow(key){if(_focusedRowKey===key)return;_focusedRowKey=key;document.querySelectorAll('.entry-row').forEach(r=>r.classList.toggle('focused',r.dataset.key===key));const s=driverRowState[key];if(!s)return;ilRenderJobsForDriver(s.driverName,s.jobs);}

async function erSaveRow(key){
  const s=driverRowState[key];if(!s)return;
  const plate=document.querySelector(`.er-plate-select[data-key="${key}"]`)?.value||'';
  const btn=document.getElementById(`${key}-save`), row=document.querySelector(`.entry-row[data-key="${key}"]`);
  const errors=[];
  if(!plate)errors.push('เลือกทะเบียนรถ');
  if(!s.noFuel){
    const priceRaw=document.getElementById(`${key}-price`)?.value??'';
    if(priceRaw===''||isNaN(parseFloat(priceRaw)))errors.push('ใส่ค่าน้ำมัน หรือติ๊ก "ไม่เติมน้ำมัน"');
    else if(parseFloat(priceRaw)<0)errors.push('ค่าน้ำมันติดลบไม่ได้');
  }
  
const delRaw = document.getElementById(`${key}-delivery`)?.value ?? '';
const otRaw = document.getElementById(`${key}-ot`)?.value ?? '';
const hanRaw = document.getElementById(`${key}-handling`)?.value ?? '';

if(delRaw !== '' && isNaN(parseFloat(delRaw)))errors.push('ค่าวิ่งไม่ถูกต้อง');
if(otRaw !== '' && isNaN(parseFloat(otRaw)))errors.push('ค่า OT ไม่ถูกต้อง');
if(hanRaw !== '' && isNaN(parseFloat(hanRaw)))errors.push('ค่ายกไม่ถูกต้อง');
  
  if(!s.startDT||!s.endDT)errors.push('เลือกวันเวลาเริ่ม-สิ้นสุด');
  else if(new Date(s.endDT)<=new Date(s.startDT))errors.push('เวลาสิ้นสุดต้องหลังเวลาเริ่ม');
  
  if(errors.length){btn.textContent=' '+errors[0];setTimeout(()=>{btn.innerHTML='บันทึก';},2200);return;}
  
  const date=s.startDT.split('T')[0];
  row?.classList.add('saving');btn.disabled=true;btn.innerHTML='<span class="ic">⏳</span> กำลังบันทึก...';
  const toBackendDT=v=>v?v.replace('T',' ')+':00':'';
  
  const fd=new FormData();
  fd.append('_token',CSRF_TOKEN);
  fd.append('work_date',date);
  fd.append('driver_name',s.driverName);
  fd.append('vehicle_id',plate);
  fd.append('start_time',toBackendDT(s.startDT));
  fd.append('end_time',toBackendDT(s.endDT));
  fd.append('total_price',s.price || 0);
  fd.append('total_distance',s.distance || 0);
  fd.append('delivery_cost', s.deliveryCost || 0);
  fd.append('ot_cost', s.otCost || 0);
  fd.append('handling_cost', s.handlingCost || 0);
  fd.append('ok',s.okCount || 0);
  fd.append('ng',s.failCount || 0);
  
  const ppl=parseFloat(document.getElementById('il-price-per-liter')?.value)||0;
  if(ppl>0)fd.append('price_per_liter',ppl);
  fd.append('liters', s.liters > 0 ? s.liters.toFixed(2) : 0);
  fd.append('create_by', (CURRENT_USER && CURRENT_USER !== 'Guest') ? String(CURRENT_USER) : 'system');

  try{
    const res=await fetch(ROUTE_STORE,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},body:fd});
    if(!res.ok&&res.status!==302)throw new Error('HTTP '+res.status);
    markDriverSaved(date,s.driverName);
    _clearRowDraft(date,s.driverName);
    ilAppendLogRow({
      date,driver_name:s.driverName,vehicle_id:plate,
      start_h:s.sh,start_m:s.sm,end_h:s.eh,end_m:s.em,
      start_dt:s.startDT,end_dt:s.endDT,
      total_price:s.price,total_distance:s.distance,liters:s.liters,
      km_per_liter:s.kml,ok_count:s.okCount,fail_count:s.failCount,
      delivery_cost:s.deliveryCost,ot_cost:s.otCost,handling_cost:s.handlingCost
    });
    if(s.jobs.length>0)_syncNgJobs(date,s.driverName,s.jobs);
    if(_focusedRowKey===key){_focusedRowKey=null;ilResetJobsPanel();}
    delete driverRowState[key];showSaveToast(s.driverName);ilRenderDriverRows(date);
  }catch(e){
    console.warn('save error',e);row?.classList.remove('saving');btn.disabled=false;btn.innerHTML='⚠ ลองอีกครั้ง';setTimeout(()=>{btn.innerHTML='บันทึก';},2200);
  }
}
function showSaveToast(driverName){document.getElementById('saveToast')?.remove();const toast=document.createElement('div');toast.id='saveToast';toast.className='save-toast';toast.innerHTML=`<span class="save-toast-icon">✓</span><div class="save-toast-body"><div class="save-toast-title">บันทึกสำเร็จ</div><div class="save-toast-msg">${driverName}</div></div>`;document.body.appendChild(toast);setTimeout(()=>{toast.classList.add('hiding');setTimeout(()=>toast.remove(),250);},2500);}
function showInfoToast(title,msg,warn){document.getElementById('saveToast')?.remove();const toast=document.createElement('div');toast.id='saveToast';toast.className='save-toast';toast.style.maxWidth='320px';toast.innerHTML=`<span class="save-toast-icon" style="background:${warn?'#f59e0b':'#10b981'}">${warn?'!':'✓'}</span><div class="save-toast-body"><div class="save-toast-title">${title}</div><div class="save-toast-msg" style="white-space:pre-line">${msg}</div></div>`;document.body.appendChild(toast);setTimeout(()=>{toast.classList.add('hiding');setTimeout(()=>toast.remove(),250);},4500);}

// บันทึกทุกแถวที่กรอกครบในหน้าเดียว (ข้ามแถวที่ข้อมูลไม่ครบ พร้อมสรุปผลให้)
async function erSaveAllRows(){
  const keys=Object.keys(driverRowState);
  if(keys.length===0){alert('ไม่มีรายการให้บันทึก');return;}

  const readyKeys=keys.filter(k=>erValidateRow(k).valid);
  const notReadyKeys=keys.filter(k=>!readyKeys.includes(k));

  if(readyKeys.length===0){
    alert('ยังไม่มีงานที่กรอกข้อมูลครบสำหรับบันทึก\nกรุณาเลือกทะเบียน กรอกค่าน้ำมัน (หรือติ๊ก "ไม่เติมน้ำมัน") และเวลาให้ครบก่อน');
    return;
  }

  const confirmMsg=notReadyKeys.length>0
    ? `พบข้อมูลครบพร้อมบันทึก ${readyKeys.length} งาน\nข้าม ${notReadyKeys.length} งานที่ข้อมูลยังไม่ครบ\n\nยืนยันบันทึก ${readyKeys.length} งานนี้หรือไม่?`
    : `พบข้อมูลครบพร้อมบันทึกทั้งหมด ${readyKeys.length} งาน\n\nยืนยันบันทึกหรือไม่?`;
  if(!confirm(confirmMsg))return;

  const btn=document.getElementById('ilBtnSaveAll');const orig=btn?btn.innerHTML:'';
  if(btn){btn.dataset.saving='1';btn.disabled=true;btn.innerHTML=`<span class="ic">⏳</span> กำลังบันทึก 0/${readyKeys.length}...`;}

  let successCount=0,failCount=0;
  const skippedNames=notReadyKeys.map(k=>{
    const s=driverRowState[k];const v=erValidateRow(k);
    const row=document.querySelector(`.entry-row[data-key="${k}"]`);
    if(row)row.style.boxShadow='inset 4px 0 0 #ef4444';
    return s?`${s.driverName} (${v.reason})`:`(${v.reason})`;
  });

  for(const key of readyKeys){
    const s=driverRowState[key];if(!s)continue;
    const{plate}=erValidateRow(key);
    const row=document.querySelector(`.entry-row[data-key="${key}"]`);
    const saveBtn=document.getElementById(`${key}-save`);

    row?.classList.add('saving');
    if(saveBtn){saveBtn.disabled=true;saveBtn.innerHTML='⏳';}

    const date=s.startDT.split('T')[0];
    const toBackendDT=v=>v?v.replace('T',' ')+':00':'';
    const fd=new FormData();
    fd.append('_token',CSRF_TOKEN);
    fd.append('work_date',date);
    fd.append('driver_name',s.driverName);
    fd.append('vehicle_id',plate);
    fd.append('start_time',toBackendDT(s.startDT));
    fd.append('end_time',toBackendDT(s.endDT));
    fd.append('total_price',s.price||0);
    fd.append('total_distance',s.distance||0);
    fd.append('delivery_cost',s.deliveryCost||0);
    fd.append('ot_cost',s.otCost||0);
    fd.append('handling_cost',s.handlingCost||0);
    fd.append('ok',s.okCount||0);
    fd.append('ng',s.failCount||0);
    const ppl=parseFloat(document.getElementById('il-price-per-liter')?.value)||0;
    if(ppl>0)fd.append('price_per_liter',ppl);
    fd.append('liters',s.liters>0?s.liters.toFixed(2):0);
    fd.append('create_by',(CURRENT_USER&&CURRENT_USER!=='Guest')?String(CURRENT_USER):'system');

    try{
      const res=await fetch(ROUTE_STORE,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},body:fd});
      if(!res.ok&&res.status!==302)throw new Error('HTTP '+res.status);
      markDriverSaved(date,s.driverName);
      _clearRowDraft(date,s.driverName);
      ilAppendLogRow({
        date,driver_name:s.driverName,vehicle_id:plate,
        start_h:s.sh,start_m:s.sm,end_h:s.eh,end_m:s.em,
        start_dt:s.startDT,end_dt:s.endDT,
        total_price:s.price,total_distance:s.distance,liters:s.liters,
        km_per_liter:s.kml,ok_count:s.okCount,fail_count:s.failCount,
        delivery_cost:s.deliveryCost,ot_cost:s.otCost,handling_cost:s.handlingCost
      });
      if(s.jobs.length>0)_syncNgJobs(date,s.driverName,s.jobs);
      successCount++;
      delete driverRowState[key];
    }catch(e){
      console.warn('bulk save error',s.driverName,e);
      failCount++;
      if(saveBtn){saveBtn.disabled=false;saveBtn.innerHTML='บันทึก';}
      row?.classList.remove('saving');
    }
    if(btn)btn.innerHTML=`<span class="ic">⏳</span> กำลังบันทึก ${successCount+failCount}/${readyKeys.length}...`;
  }

  if(_focusedRowKey&&!driverRowState[_focusedRowKey]){_focusedRowKey=null;ilResetJobsPanel();}

  const date=document.getElementById('il-work-date')?.value;
  if(btn)btn.dataset.saving='0';
  if(date)ilRenderDriverRows(date);

  if(btn){btn.disabled=false;btn.innerHTML=orig;erRefreshSaveAllBadge();}

  let msg=`บันทึกสำเร็จ ${successCount} จาก ${readyKeys.length} งานที่ข้อมูลครบ`;
  if(failCount>0)msg+=` · ผิดพลาด ${failCount} งาน`;
  if(skippedNames.length>0)msg+=` · ข้าม ${skippedNames.length} งาน (ข้อมูลไม่ครบ)`;
  const detail=skippedNames.length?('\n'+skippedNames.join('\n')):'';
  showInfoToast('บันทึกทั้งหมดเสร็จสิ้น',msg+detail,(failCount>0||skippedNames.length>0));
}

function ilAppendLogRow(r){
  const tbody=document.getElementById('oilTbody');if(!tbody)return;
  const emptyRow=tbody.querySelector('tr:not([data-driver])');if(emptyRow)emptyRow.remove();
  let durText='',totalMin=0;
  if(r.start_dt&&r.end_dt){totalMin=Math.round((new Date(r.end_dt)-new Date(r.start_dt))/60000);}
  else{let sm=(r.start_h||0)*60+(r.start_m||0),em=(r.end_h||0)*60+(r.end_m||0);if(em<sm)em+=1440;totalMin=em-sm;}
  if(totalMin>0){const days=Math.floor(totalMin/1440),hh=Math.floor((totalMin%1440)/60),mm=totalMin%60;if(days>0){durText=days+' วัน';if(hh>0)durText+=' '+hh+' ชม.';if(mm>0)durText+=' '+mm+' น.';}else if(hh>0&&mm>0)durText=hh+' ชม. '+mm+' น.';else if(hh>0)durText=hh+' ชม.';else durText=mm+' น.';}
  const pad=n=>String(n).padStart(2,'0');const timeText=`${pad(r.start_h||0)}:${pad(r.start_m||0)}-${pad(r.end_h||0)}:${pad(r.end_m||0)}`;
  const kml=r.km_per_liter||0;let kmlCls='km-mid';if(kml>=13)kmlCls='km-good';else if(kml>0&&kml<9)kmlCls='km-bad';
  const thbPerKm=(r.total_distance>0&&r.total_price>0)?(r.total_price/r.total_distance):0;
  const dp=(r.date||'').split('-');const dateText=dp.length===3?`${dp[2]}/${dp[1]}`:'—';const dateFull=dp.length===3?`${dp[2]}/${dp[1]}/${dp[0]}`:'';
  const tr=document.createElement('tr');tr.setAttribute('data-driver',(r.driver_name||'').toLowerCase());tr.style.background='rgba(59,130,246,.08)';
  tr.innerHTML=`<td class="row-idx" data-label="#">•</td><td data-label="วันที่"><span class="date-pill" title="${dateFull}">${dateText}</span></td><td data-label="คนขับ"><div class="driver-cell"><div class="driver-name" title="${r.driver_name}">${r.driver_name}</div><div class="driver-plate">${r.vehicle_id||'—'}</div></div></td><td data-label="เวลา"><span class="time-pill">${timeText}</span></td><td class="num" data-label="ชม.">${durText?'<span class="hour-pill">'+durText+'</span>':'<span style="color:var(--text4)">—</span>'}</td><td class="num" data-label="ระยะ">${r.total_distance>0?Math.round(r.total_distance).toLocaleString()+' km':'—'}</td><td class="num" data-label="ลิตร">${r.liters>0?fmtN(r.liters):'—'}</td><td class="num" data-label="ค่าน้ำมัน">${r.total_price>0?'฿'+Math.round(r.total_price).toLocaleString():'—'}</td><td class="num" data-label="KM/L">${kml>0?'<span class="'+kmlCls+'">'+fmtN(kml)+'</span>':'<span style="color:var(--text4)">—</span>'}</td><td class="num" data-label="฿/km">${thbPerKm>0?'<span class="thb-km-val">฿'+thbPerKm.toFixed(2)+'</span>':'<span style="color:var(--text4)">—</span>'}</td>`;
  tbody.insertBefore(tr,tbody.firstChild);
  setTimeout(()=>{tr.style.transition='background 1s';tr.style.background='';},100);
  const c=document.getElementById('oilCount');if(c)c.textContent=document.querySelectorAll('#oilTbody tr[data-driver]').length;
  ilUpdateChartsAfterSave(r);
}

function ilUpdateChartsAfterSave(r){
  const name=r.driver_name;if(!name)return;
  if(isAllowedDriver(name)){if(!DLV_BY_DRIVER[name])DLV_BY_DRIVER[name]={success:0,fail:0,plate:r.vehicle_id||''};DLV_BY_DRIVER[name].success+=(r.ok_count||0);DLV_BY_DRIVER[name].fail+=(r.fail_count||0);}
  const plate=r.vehicle_id||'ไม่ระบุ';const key=plate+'|'+name;
  if(r.km_per_liter>0){if(!KML_BY_DRIVER[key])KML_BY_DRIVER[key]={sum:0,count:0,plate,driver:name};KML_BY_DRIVER[key].sum+=r.km_per_liter;KML_BY_DRIVER[key].count++;}
  if(!COST_BY_DRIVER[key])COST_BY_DRIVER[key]={price:0,dist:0,plate,driver:name};
  COST_BY_DRIVER[key].price+=(r.total_price||0);COST_BY_DRIVER[key].dist+=(r.total_distance||0);
  try{renderDlv();renderKmlChart();renderCostChart();}catch(e){}
}

function ilResetJobsPanel(){const wrap=document.getElementById('inlineJobTableWrap');if(wrap)wrap.innerHTML='<div class="job-loading">คลิกที่แถวคนขับ<br>เพื่อดูรายการงานของคนนั้น</div>';const title=document.getElementById('jobsPanelTitleText');if(title)title.textContent='รายการงาน';const chip=document.getElementById('ilJobDateChip');if(chip)chip.style.display='none';}
function ilRenderJobsForDriver(driverName,jobs){
  const wrap=document.getElementById('inlineJobTableWrap');if(!wrap)return;
  const title=document.getElementById('jobsPanelTitleText');if(title)title.textContent=driverName;
  const date=document.getElementById('il-work-date')?.value||'';const chip=document.getElementById('ilJobDateChip');
  if(chip&&date){const dp=date.split('-');chip.textContent=dp.length===3?`${dp[2]}/${dp[1]}`:date;chip.style.display='';}
  if(!jobs||jobs.length===0){wrap.innerHTML='<div class="job-loading">ไม่มีรายการงาน</div>';return;}
  let okC=0,failC=0;jobs.forEach(j=>{const k=_jobStatusKind(j);if(k==='ok')okC++;else if(k==='fail')failC++;});
  let html=`<div class="jobs-summary-bar"><span class="jsb-chip"><strong>${jobs.length}</strong> งาน</span><span class="jsb-chip ok"><strong>${okC}</strong> สำเร็จ</span>${failC>0?`<span class="jsb-chip fail"><strong>${failC}</strong> ไม่สำเร็จ</span>`:''}</div>`;
  jobs.forEach(j=>{
    const kind=_jobStatusKind(j);const stTxt=kind==='ok'?'สำเร็จ':(kind==='fail'?'ไม่สำเร็จ':'รอ');
    const meta=[];
    if(j.so_id)meta.push(`<span class="dgj-meta-item"><span class="dgj-meta-label">SO</span> ${j.so_id}</span>`);
    if(j.bill_in_by)meta.push(`<span class="dgj-meta-item"><span class="dgj-meta-label">รับ</span> ${j.bill_in_by}</span>`);
    if(j.note)meta.push(`<span class="dgj-meta-item dgj-note"><span class="dgj-meta-label">หมายเหตุ</span> ${j.note}</span>`);
    html+=`<div class="dgj-row"><div class="dgj-main"><div class="dgj-top"><span class="dgj-bill">${j.bill_no||'—'}</span><span class="dgj-customer" title="${j.customer_name||''}">${j.customer_name||'—'}</span><span class="dgj-status ${kind}">${stTxt}</span></div>${meta.length?`<div class="dgj-meta">${meta.join('<span class="dgj-meta-sep">·</span>')}</div>`:''}</div></div>`;
  });
  wrap.innerHTML=html;
}

// Charts Functions
@php
  $deliveryByDriver=[];
  foreach($logs as $log){$driver=$log['driver_name']??'ไม่ระบุ';if(!isset($deliveryByDriver[$driver]))$deliveryByDriver[$driver]=['success'=>0,'fail'=>0,'plate'=>$log['vehicle_id']??''];$deliveryByDriver[$driver]['success']+=(int)($log['delivery_success']??$log['success_count']??$log['ok_count']??0);$deliveryByDriver[$driver]['fail']+=(int)($log['delivery_fail']??$log['fail_count']??$log['ng_count']??0);}
  $plateFilterActive=($filterPlate??'all')!=='all';
  $kmlByDriver=[];foreach($logs as $log){$rid=$log['id']??null;$plate=$log['vehicle_id']??'ไม่ระบุ';$driver=$log['driver_name']??'';$key=$plateFilterActive?$plate:($plate.'|'.$driver);$kml=($rid!==null&&isset($effKml[$rid]))?$effKml[$rid]:(float)($log['km_per_liter']??0);if($kml<=0)continue;if(!isset($kmlByDriver[$key]))$kmlByDriver[$key]=['sum'=>0,'count'=>0,'plate'=>$plate,'driver'=>($plateFilterActive?'':$driver)];$kmlByDriver[$key]['sum']+=$kml;$kmlByDriver[$key]['count']++;}
  $costByDriver2=[];foreach($logs as $log){$plate=$log['vehicle_id']??'ไม่ระบุ';$driver=$log['driver_name']??'';$key=$plateFilterActive?$plate:($plate.'|'.$driver);if(!isset($costByDriver2[$key]))$costByDriver2[$key]=['price'=>0,'dist'=>0,'plate'=>$plate,'driver'=>($plateFilterActive?'':$driver)];$costByDriver2[$key]['price']+=(float)($log['total_price']??0);$costByDriver2[$key]['dist']+=(float)($log['total_distance']??0);}
@endphp
const DLV_BY_DRIVER=@json($deliveryByDriver);
const KML_BY_DRIVER=@json($kmlByDriver);
const COST_BY_DRIVER=@json($costByDriver2);
const VEHICLE_TYPE={kml:'car',cost:'car'};
function isMoto(plate){const p=(plate||'').trim();return p.startsWith('มอเตอร์ไซด์')||p.startsWith('มอเตอร์ไซค์')||p.startsWith('มอ.')||p.startsWith('มอ ');}
function switchVehicleType(chart,type){VEHICLE_TYPE[chart]=type;document.querySelectorAll(`.vehicle-toggle[data-chart="${chart}"] .vt-btn`).forEach(b=>b.classList.toggle('active',b.dataset.type===type));if(chart==='kml')renderKmlChart();else if(chart==='cost')renderCostChart();}

let dlvChart=null;
function renderDlv(){const drivers=Object.keys(DLV_BY_DRIVER).filter(d=>isAllowedDriver(d));if(drivers.length===0){if(dlvChart)dlvChart.destroy();document.getElementById('dlvLegend').innerHTML='<span style="color:var(--text4)">ไม่มีข้อมูล</span>';return;}const orderIdx=name=>{const i=DRIVER_ORDER_LIST.map(_normalizeDriver).indexOf(_normalizeDriver(name));return i<0?999:i;};const sorted=drivers.map(d=>({name:d,s:DLV_BY_DRIVER[d].success,f:DLV_BY_DRIVER[d].fail})).sort((a,b)=>orderIdx(a.name)-orderIdx(b.name));const inner=document.getElementById('deliveryChartInner');if(inner){inner.style.width='100%';inner.style.height='100%';}if(dlvChart)dlvChart.destroy();dlvChart=new Chart(document.getElementById('deliveryChart'),{type:'bar',data:{labels:sorted.map(d=>d.name),datasets:[{label:'ส่งสำเร็จ',data:sorted.map(d=>d.s),backgroundColor:'#10b981',borderRadius:{topLeft:0,topRight:0,bottomLeft:6,bottomRight:6},borderSkipped:false,stack:'s',maxBarThickness:50},{label:'ส่งไม่สำเร็จ',data:sorted.map(d=>d.f),backgroundColor:'#ef4444',borderRadius:{topLeft:6,topRight:6,bottomLeft:0,bottomRight:0},borderSkipped:false,stack:'s',maxBarThickness:50}]},plugins:[ChartDataLabels],options:{responsive:true,maintainAspectRatio:false,layout:{padding:{top:20,left:10,right:10}},plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>`${ctx.dataset.label}: ${ctx.raw} รายการ`,footer:items=>'รวม: '+items.reduce((s,i)=>s+i.raw,0)+' รายการ'}},datalabels:{color:'#fff',font:{weight:'700',size:13,family:'Inter'},formatter:v=>v>0?v:'',display:ctx=>ctx.dataset.data[ctx.dataIndex]>0,anchor:'center',align:'center'}},scales:{x:{stacked:true,ticks:{font:{size:14,weight:'600',family:'IBM Plex Sans Thai'},color:'#18181b',autoSkip:true,maxRotation:0},grid:{display:false}},y:{stacked:true,beginAtZero:true,ticks:{font:{size:14,family:'Inter'},color:'#71717a',stepSize:2},grid:{color:'rgba(0,0,0,.05)'}}}}});document.getElementById('dlvLegend').innerHTML='<div class="chart-legend-item"><span class="chart-legend-dot" style="background:#10b981"></span>ส่งสำเร็จ</div><div class="chart-legend-item"><span class="chart-legend-dot" style="background:#ef4444"></span>ส่งไม่สำเร็จ</div>';}

let kmlChart=null;
function renderKmlChart(){const vType=VEHICLE_TYPE.kml||'car';const drivers=Object.keys(KML_BY_DRIVER).map(key=>({name:KML_BY_DRIVER[key].driver||'',plate:KML_BY_DRIVER[key].plate||key,avg:KML_BY_DRIVER[key].count>0?KML_BY_DRIVER[key].sum/KML_BY_DRIVER[key].count:0})).filter(d=>d.avg>0).filter(d=>vType==='moto'?isMoto(d.plate):!isMoto(d.plate)).sort((a,b)=>{const oa=driverOrderIndex(a.name),ob=driverOrderIndex(b.name);if(oa!==ob)return oa-ob;return(a.name||'').localeCompare(b.name||'','th');});if(drivers.length===0){if(kmlChart)kmlChart.destroy();document.getElementById('kmlLegend').innerHTML=`<span style="color:var(--text4)">ไม่มีข้อมูล${vType==='moto'?'มอเตอร์ไซค์':'รถยนต์'}</span>`;return;}const inner=document.getElementById('chartKmlInner');if(inner){inner.style.width='100%';inner.style.height=Math.max(drivers.length*44+40,300)+'px';}const labels=drivers.map(d=>[d.plate||d.name,d.name&&d.plate?d.name:'']);const data=drivers.map(d=>d.avg);const overallAvg=data.reduce((a,b)=>a+b,0)/data.length;const lowBand=overallAvg*0.9;const barColors=data.map(v=>v<lowBand?'#ef4444':(v<overallAvg?'#f59e0b':'#10b981'));const xMax=Math.ceil((Math.max(...data,overallAvg)+1)/2)*2;if(kmlChart)kmlChart.destroy();kmlChart=new Chart(document.getElementById('chartKml'),{type:'bar',data:{labels,datasets:[{label:'เฉลี่ย km/L',data,backgroundColor:barColors,borderRadius:6,borderSkipped:false,maxBarThickness:28}]},plugins:[ChartDataLabels,{id:'kmlThreshold',afterDatasetsDraw(chart){const{ctx,chartArea:{top,bottom},scales:{x}}=chart;const xPos=x.getPixelForValue(overallAvg);ctx.save();ctx.strokeStyle='#ef4444';ctx.setLineDash([6,4]);ctx.lineWidth=2;ctx.beginPath();ctx.moveTo(xPos,top);ctx.lineTo(xPos,bottom);ctx.stroke();ctx.setLineDash([]);ctx.fillStyle='#ef4444';ctx.font='600 11px Inter';ctx.textAlign='left';ctx.fillText('เกณฑ์เฉลี่ย '+fmtN(overallAvg),xPos+6,top+12);ctx.restore();}}],options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,layout:{padding:{top:10,right:50,left:6,bottom:6}},plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>`เฉลี่ย: ${fmtN(ctx.raw)} km/L`,afterLabel:ctx=>{const d=drivers[ctx.dataIndex];return d.name?`คนขับ: ${d.name}`:''}}},datalabels:{color:'#18181b',font:{weight:'700',size:11,family:'Inter'},anchor:'end',align:'right',offset:4,formatter:v=>fmtN(v)+' km/L'}},scales:{x:{beginAtZero:true,suggestedMax:xMax,ticks:{stepSize:2,font:{size:14,family:'Inter'},color:'#71717a'},grid:{color:'rgba(0,0,0,.05)'}},y:{grid:{display:false},ticks:{font:{size:14,weight:'600',family:'IBM Plex Sans Thai'},color:'#3f3f46',autoSkip:false,callback:function(value){const l=this.getLabelForValue(value);return Array.isArray(l)?l:[l];}}}}}});document.getElementById('kmlLegend').innerHTML=`<div class="chart-legend-item"><span class="chart-legend-dot" style="background:#10b981"></span>ดี (≥ เฉลี่ย)</div><div class="chart-legend-item"><span class="chart-legend-dot" style="background:#f59e0b"></span>ปกติ (ใกล้เฉลี่ย)</div><div class="chart-legend-item"><span class="chart-legend-dot" style="background:#ef4444"></span>ผิดปกติ (ต่ำกว่าเฉลี่ย 10%)</div><div class="chart-legend-item" style="margin-left:auto;color:var(--text4)">เฉลี่ย <strong style="color:var(--text);margin-left:4px">${fmtN(overallAvg)} km/L</strong></div>`;}

let costChart=null;
function renderCostChart(){const vType=VEHICLE_TYPE.cost||'car';const drivers=Object.keys(COST_BY_DRIVER).map(key=>({name:COST_BY_DRIVER[key].driver||'',plate:COST_BY_DRIVER[key].plate||key,cost:COST_BY_DRIVER[key].dist>0?COST_BY_DRIVER[key].price/COST_BY_DRIVER[key].dist:0,price:COST_BY_DRIVER[key].price,dist:COST_BY_DRIVER[key].dist})).filter(d=>d.cost>0).filter(d=>vType==='moto'?isMoto(d.plate):!isMoto(d.plate)).sort((a,b)=>{const oa=driverOrderIndex(a.name),ob=driverOrderIndex(b.name);if(oa!==ob)return oa-ob;return(a.name||'').localeCompare(b.name||'','th');});if(drivers.length===0){if(costChart)costChart.destroy();document.getElementById('costLegend').innerHTML=`<span style="color:var(--text4)">ไม่มีข้อมูล${vType==='moto'?'มอเตอร์ไซค์':'รถยนต์'}</span>`;return;}const inner=document.getElementById('chartCostInner');if(inner){inner.style.width='100%';inner.style.height=Math.max(drivers.length*44+40,300)+'px';}const labels=drivers.map(d=>[d.plate||d.name,d.name&&d.plate?d.name:'']);const data=drivers.map(d=>d.cost);const avg=data.reduce((a,b)=>a+b,0)/data.length;const barColors=data.map(v=>v<=avg*0.85?'#10b981':(v<=avg*1.05?'#f59e0b':'#ef4444'));const xMax=Math.ceil(Math.max(...data)*1.15);if(costChart)costChart.destroy();costChart=new Chart(document.getElementById('chartCost'),{type:'bar',data:{labels,datasets:[{label:'฿/km',data,backgroundColor:barColors,borderRadius:6,borderSkipped:false,maxBarThickness:28}]},plugins:[ChartDataLabels,{id:'costAvgLine',afterDatasetsDraw(chart){const{ctx,chartArea:{top,bottom},scales:{x}}=chart;const xPos=x.getPixelForValue(avg);ctx.save();ctx.strokeStyle='#3b82f6';ctx.setLineDash([6,4]);ctx.lineWidth=2;ctx.beginPath();ctx.moveTo(xPos,top);ctx.lineTo(xPos,bottom);ctx.stroke();ctx.setLineDash([]);ctx.fillStyle='#3b82f6';ctx.font='600 11px Inter';ctx.textAlign='left';ctx.fillText('เฉลี่ย ฿'+fmtN(avg),xPos+6,top+12);ctx.restore();}}],options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,layout:{padding:{top:10,right:70,left:6,bottom:6}},plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>`฿${fmtN(ctx.raw)} / km`,afterLabel:ctx=>{const d=drivers[ctx.dataIndex];const lines=[];if(d.name)lines.push(`คนขับ: ${d.name}`);lines.push(`รวม ฿${d.price.toLocaleString(undefined,{maximumFractionDigits:0})} / ${d.dist.toLocaleString()} km`);return lines;}}},datalabels:{color:'#18181b',font:{weight:'700',size:11,family:'Inter'},anchor:'end',align:'right',offset:4,formatter:v=>'฿'+fmtN(v)}},scales:{x:{beginAtZero:true,suggestedMax:xMax,ticks:{font:{size:14,family:'Inter'},color:'#71717a',callback:v=>'฿'+v},grid:{color:'rgba(0,0,0,.05)'}},y:{grid:{display:false},ticks:{font:{size:14,weight:'600',family:'IBM Plex Sans Thai'},color:'#3f3f46',autoSkip:false,callback:function(value){const l=this.getLabelForValue(value);return Array.isArray(l)?l:[l];}}}}}});document.getElementById('costLegend').innerHTML=`<div class="chart-legend-item"><span class="chart-legend-dot" style="background:#10b981"></span>ดี (ต่ำกว่าเฉลี่ย ≥15%)</div><div class="chart-legend-item"><span class="chart-legend-dot" style="background:#f59e0b"></span>ปกติ (±5–15%)</div><div class="chart-legend-item"><span class="chart-legend-dot" style="background:#ef4444"></span>สูง (สูงกว่าเฉลี่ย >5%)</div><div class="chart-legend-item" style="margin-left:auto;color:var(--text4)">เฉลี่ย <strong style="color:var(--text);margin-left:4px">฿${fmtN(avg)}/km</strong></div>`;}

// PDF Export Functions
@php
  $pdfLogsArr=$allLogs->map(function($l){return['driver'=>$l['driver_name']??'','plate'=>$l['vehicle_id']??'','date'=>$l['work_date']??'','start'=>$l['start_time']??'','end'=>$l['end_time']??'','price'=>(float)($l['total_price']??0),'distance'=>(float)($l['total_distance']??0),'liters'=>(float)($l['liters']??0),'kml'=>(float)($l['km_per_liter']??0),'hours'=>(float)($l['work_hours']??0)];})->values();
@endphp
const PDF_LOGS=@json($pdfLogsArr);
let pdfMode='range';
function openPdfRangeModal(){document.getElementById('pdfModalOverlay')?.classList.add('open');const wd=document.getElementById('il-work-date')?.value||todayStr();const f=document.getElementById('pdfDateFrom'),t=document.getElementById('pdfDateTo');if(f&&!f.value)f.value=wd;if(t&&!t.value)t.value=wd;}
function closePdfRangeModal(){document.getElementById('pdfModalOverlay')?.classList.remove('open');}
function setPdfMode(mode){pdfMode=mode;document.querySelectorAll('.pdf-mode-btn').forEach(b=>b.classList.toggle('active',b.dataset.mode===mode));document.getElementById('pdfRangeFields').style.display=mode==='range'?'':'none';document.getElementById('pdfSingleFields').style.display=mode==='single'?'':'none';}
async function confirmPdfExport(){let from,to,title;if(pdfMode==='single'){const d=document.getElementById('pdfSingleDate').value;if(!d){alert('เลือกวันที่');return;}from=to=d;title='รายงานประจำวันที่ '+_thDate(d);}else{from=document.getElementById('pdfDateFrom').value;to=document.getElementById('pdfDateTo').value;if(!from||!to){alert('เลือกช่วงวันที่');return;}if(from>to){const tmp=from;from=to;to=tmp;}title=(from===to)?('รายงานประจำวันที่ '+_thDate(from)):('รายงานช่วงวันที่ '+_thDate(from)+' – '+_thDate(to));}closePdfRangeModal();await exportPDF(from,to,title);}
function _thDate(d){const p=(d||'').split('-');if(p.length!==3)return d;return`${p[2]}/${p[1]}/${parseInt(p[0])+543}`;}
async function exportPDF(fromDate,toDate,reportTitle){
  const btn=document.querySelector('.entry-export-btn');const orig=btn?btn.innerHTML:'';
  if(btn){btn.disabled=true;btn.innerHTML='<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-opacity=".25"/><path d="M12 2a10 10 0 0 1 10 10"/></svg> กำลังสร้าง...';}
  try{
    const rows=PDF_LOGS.filter(l=>{const d=l.date||'';return d>=fromDate&&d<=toDate;});
    if(rows.length===0){alert('ไม่มีข้อมูลในช่วงวันที่นี้');if(btn){btn.disabled=false;btn.innerHTML=orig;}return;}
    const{jsPDF}=window.jspdf;const pdf=new jsPDF('l','mm','a4');const pageW=297,pageH=210,margin=10,usableW=pageW-margin*2,usableH=pageH-margin*2;
    const stage=document.createElement('div');stage.style.cssText='position:fixed;left:-99999px;top:0;background:#fff;font-family:"IBM Plex Sans Thai",sans-serif;padding:0;';document.body.appendChild(stage);
    const totPrice=rows.reduce((s,r)=>s+r.price,0),totDist=rows.reduce((s,r)=>s+r.distance,0),totLiters=rows.reduce((s,r)=>s+r.liters,0);
    const avgKml=totLiters>0?totDist/totLiters:0;
    const byDriver={};rows.forEach(r=>{const n=r.driver||'ไม่ระบุ';if(!byDriver[n])byDriver[n]={rows:[],price:0,dist:0,liters:0};byDriver[n].rows.push(r);byDriver[n].price+=r.price;byDriver[n].dist+=r.distance;byDriver[n].liters+=r.liters;});
    const driverNames=Object.keys(byDriver).filter(n=>byDriver[n].price>0||byDriver[n].dist>0).sort((a,b)=>{const ta=byDriver[a].dist>0?byDriver[a].price/byDriver[a].dist:0;const tb=byDriver[b].dist>0?byDriver[b].price/byDriver[b].dist:0;return tb-ta;});
    driverNames.forEach(n=>{byDriver[n].rows.sort((a,b)=>(a.date||'').localeCompare(b.date||''));});
    async function renderPage(el,isFirst){stage.appendChild(el);if(!isFirst)pdf.addPage();const canvas=await html2canvas(el,{scale:1.5,backgroundColor:'#fff',logging:false});const imgData=canvas.toDataURL('image/jpeg',0.80);const imgW=usableW,imgH=canvas.height*imgW/canvas.width;pdf.addImage(imgData,'JPEG',margin,margin,imgW,Math.min(imgH,usableH));stage.removeChild(el);}
    const p1=document.createElement('div');p1.style.cssText='width:1200px;background:#fff;padding:30px 34px;box-sizing:border-box;';
    const totHours=rows.reduce((s,r)=>s+(r.hours||0),0);
    let h1=`<div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #3b82f6;padding-bottom:10px;margin-bottom:14px;"><div><div style="font-size:20px;font-weight:700;color:#18181b;">${reportTitle}</div><div style="font-size:11px;color:#71717a;margin-top:2px;">ระบบติดตามน้ำมันรถ · ${CURRENT_USER}</div></div><div style="text-align:right;font-size:10px;color:#a1a1aa;">พิมพ์เมื่อ ${new Date().toLocaleString('th-TH',{timeZone:TZ})}</div></div>`;
    h1+=`<div style="display:grid;grid-template-columns:repeat(6,1fr);gap:7px;margin-bottom:14px;">${_pdfStat('ค่าน้ำมันรวม','฿'+Math.round(totPrice).toLocaleString())}${_pdfStat('ระยะทางรวม',Math.round(totDist).toLocaleString()+' km')}${_pdfStat('น้ำมันรวม',fmtN(totLiters)+' L')}${_pdfStat('เฉลี่ย km/L',fmtN(avgKml))}${_pdfStat('ชั่วโมงรวม',fmtN(totHours)+' ชม.')}${_pdfStat('จำนวน',rows.length+' รายการ · '+driverNames.length+' คน')}</div>`;
    const rankPrice=[...driverNames].sort((a,b)=>byDriver[b].price-byDriver[a].price);
    const rankDist=[...driverNames].sort((a,b)=>byDriver[b].dist-byDriver[a].dist);
    const rankHours=[...driverNames].sort((a,b)=>{const ha=byDriver[a].rows.reduce((s,r)=>s+(r.hours||0),0);const hb=byDriver[b].rows.reduce((s,r)=>s+(r.hours||0),0);return hb-ha;});
    const rankKml=[...driverNames].filter(n=>byDriver[n].liters>0).sort((a,b)=>(byDriver[b].dist/byDriver[b].liters)-(byDriver[a].dist/byDriver[a].liters));
    const medal=i=>i===0?'🥇':i===1?'🥈':i===2?'🥉':'';
    const _rankRow=(arr,valFn)=>arr.map((n,i)=>`<div style="display:flex;align-items:center;gap:6px;padding:4px 0;${i<arr.length-1?'border-bottom:1px solid #f4f4f5;':''}"><span style="font-size:13px;width:20px;text-align:center;">${medal(i)}</span><span style="font-size:11px;font-weight:600;color:#18181b;flex:1;">${n}</span><span style="font-size:11px;font-weight:700;color:#3f3f46;font-family:ui-monospace,monospace;">${valFn(n)}</span></div>`).join('');
    h1+=`<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:10px;margin-bottom:14px;"><div style="background:#fefce8;border:1px solid #fde68a;border-radius:8px;padding:10px 12px;"><div style="font-size:10px;font-weight:700;color:#92400e;margin-bottom:6px;">⛽ เติมน้ำมันมากสุด</div>${_rankRow(rankPrice,n=>'฿'+Math.round(byDriver[n].price).toLocaleString())}</div><div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 12px;"><div style="font-size:10px;font-weight:700;color:#1e40af;margin-bottom:6px;">🛣️ ขับรถไกลสุด</div>${_rankRow(rankDist,n=>Math.round(byDriver[n].dist).toLocaleString()+' km')}</div><div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 12px;"><div style="font-size:10px;font-weight:700;color:#166534;margin-bottom:6px;">⏱️ ใช้เวลามากสุด</div>${_rankRow(rankHours,n=>{const h=byDriver[n].rows.reduce((s,r)=>s+(r.hours||0),0);return fmtN(h)+' ชม.';})}</div><div style="background:#faf5ff;border:1px solid #e9d5ff;border-radius:8px;padding:10px 12px;"><div style="font-size:10px;font-weight:700;color:#6b21a8;margin-bottom:6px;">📊 ประหยัดสุด</div>${_rankRow(rankKml,n=>{const k=byDriver[n].dist/byDriver[n].liters;return fmtN(k)+' km/L';})}</div></div>`;
    p1.innerHTML=h1;await renderPage(p1,true);
    const pSum=document.createElement('div');pSum.style.cssText='width:1200px;background:#fff;padding:30px 34px;box-sizing:border-box;';
    let hSum=`<div style="font-size:16px;font-weight:700;color:#18181b;margin-bottom:14px;border-bottom:2px solid #3b82f6;padding-bottom:8px;">ค่าน้ำมันแยกตามคนขับ · ${reportTitle}</div><table style="width:100%;border-collapse:collapse;font-size:13px;"><thead><tr style="background:#f4f4f5;"><th style="padding:8px 10px;text-align:left;border-bottom:2px solid #e4e4e7;">คนขับ</th><th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;">ค่าน้ำมัน</th><th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;">ระยะ</th><th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;">ลิตร</th><th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;">km/L</th><th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;">฿/km</th><th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;">ชม.</th><th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e4e4e7;">รายการ</th></tr></thead><tbody>`;
    driverNames.forEach((n,i)=>{const d=byDriver[n];const kml=d.liters>0?d.dist/d.liters:0;const thbKm=d.dist>0?d.price/d.dist:0;const hrs=d.rows.reduce((s,r)=>s+(r.hours||0),0);hSum+=`<tr style="background:${i%2?'#fafafa':'#fff'};"><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;font-weight:600;">${n}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">฿${Math.round(d.price).toLocaleString()}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${Math.round(d.dist).toLocaleString()}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${fmtN(d.liters)}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;font-weight:600;">${kml>0?fmtN(kml):'—'}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${thbKm>0?'฿'+fmtN(thbKm):'—'}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${fmtN(hrs)}</td><td style="padding:7px 10px;border-bottom:1px solid #f4f4f5;text-align:right;">${d.rows.length}</td></tr>`;});
    hSum+=`</tbody></table>`;pSum.innerHTML=hSum;await renderPage(pSum,false);
    const ROWS_PER_PAGE=24;
    for(const drvName of driverNames){
      const drvRows=byDriver[drvName].rows;const drvPrice=byDriver[drvName].price;const drvDist=byDriver[drvName].dist;const drvLiters=byDriver[drvName].liters;
      const drvKml=drvLiters>0?drvDist/drvLiters:0;const drvThbKm=drvDist>0?drvPrice/drvDist:0;
      const chunks=[];for(let i=0;i<drvRows.length;i+=ROWS_PER_PAGE)chunks.push(drvRows.slice(i,i+ROWS_PER_PAGE));
      for(let ci=0;ci<chunks.length;ci++){
        const chunk=chunks[ci];const page=document.createElement('div');page.style.cssText='width:1200px;background:#fff;padding:28px 36px;box-sizing:border-box;';
        let html=`<div style="display:flex;align-items:center;justify-content:space-between;border-bottom:2px solid #3b82f6;padding-bottom:8px;margin-bottom:10px;"><div><div style="font-size:16px;font-weight:700;">${drvName}</div><div style="font-size:10px;color:#71717a;">${reportTitle}${chunks.length>1?' · ('+(ci+1)+'/'+chunks.length+')':''}</div></div><div style="display:flex;gap:14px;font-size:10px;"><span>฿<b style="font-size:13px">${Math.round(drvPrice).toLocaleString()}</b></span><span><b style="font-size:13px">${Math.round(drvDist).toLocaleString()}</b> km</span><span><b style="font-size:13px">${fmtN(drvLiters)}</b> L</span>${drvKml>0?`<span><b style="font-size:13px">${fmtN(drvKml)}</b> km/L</span>`:''}</div></div><table style="width:100%;border-collapse:collapse;font-size:11px;"><thead><tr style="background:#f4f4f5;"><th style="padding:6px;text-align:left;border-bottom:2px solid #e4e4e7;">วันที่</th><th style="padding:6px;text-align:left;border-bottom:2px solid #e4e4e7;">ทะเบียน</th><th style="padding:6px;text-align:left;border-bottom:2px solid #e4e4e7;">เวลา</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;">฿</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;">km</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;">L</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;">km/L</th><th style="padding:6px;text-align:right;border-bottom:2px solid #e4e4e7;">฿/km</th></tr></thead><tbody>`;
        chunk.forEach((r,i)=>{const dp=(r.date||'').split('-');const dateText=dp.length===3?`${dp[2]}/${dp[1]}/${dp[0]}`:'—';const thbKm=(r.distance>0&&r.price>0)?(r.price/r.distance):0;const startT=(r.start||'').substring(0,5);const endT=(r.end||'').substring(0,5);const timeT=(startT&&endT)?startT+'-'+endT:'—';html+=`<tr style="background:${i%2?'#fafafa':'#fff'};"><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;">${dateText}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;">${r.plate||'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;font-size:10px;color:#71717a;">${timeT}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;font-weight:600;">${r.price>0?'฿'+Math.round(r.price).toLocaleString():'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${r.distance>0?Math.round(r.distance).toLocaleString():'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${r.liters>0?fmtN(r.liters):'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;font-weight:600;">${r.kml>0?fmtN(r.kml):'—'}</td><td style="padding:5px 6px;border-bottom:1px solid #f4f4f5;text-align:right;">${thbKm>0?'฿'+fmtN(thbKm):'—'}</td></tr>`;});
        html+='</tbody>';
        if(ci===chunks.length-1){html+=`<tfoot><tr style="background:#f0f7ff;border-top:3px solid #3b82f6;"><td colspan="3" style="padding:10px;font-weight:700;font-size:14px;color:#1e40af;">รวม ${drvName}</td><td style="padding:10px;text-align:right;font-weight:700;font-size:14px;">฿${Math.round(drvPrice).toLocaleString()}</td><td style="padding:10px;text-align:right;font-weight:700;font-size:14px;">${Math.round(drvDist).toLocaleString()} km</td><td style="padding:10px;text-align:right;font-weight:700;font-size:14px;">${fmtN(drvLiters)} L</td><td style="padding:10px;text-align:right;font-weight:700;font-size:14px;">${drvKml>0?fmtN(drvKml)+' km/L':'—'}</td><td style="padding:10px;text-align:right;font-weight:700;font-size:14px;color:#1e40af;">${drvThbKm>0?'฿'+fmtN(drvThbKm)+'/km':'—'}</td></tr></tfoot>`;}
        html+='</table>';page.innerHTML=html;await renderPage(page,false);
      }
    }
    document.body.removeChild(stage);
    const fn=(fromDate===toDate)?`รายงานน้ำมัน_${fromDate}.pdf`:`รายงานน้ำมัน_${fromDate}_ถึง_${toDate}.pdf`;
    pdf.save(fn);
  }catch(e){console.error('PDF error',e);alert('สร้าง PDF ไม่สำเร็จ: '+e.message);}
  finally{if(btn){btn.disabled=false;btn.innerHTML=orig;}}
}
function _pdfStat(label,value){return`<div style="background:#f9fafb;border:1px solid #f0f0f0;border-radius:12px;padding:14px 16px;"><div style="font-size:12px;color:#71717a;margin-bottom:4px;">${label}</div><div style="font-size:19px;font-weight:700;color:#18181b;">${value}</div></div>`;}

// Initialization
document.addEventListener('DOMContentLoaded',function(){
  try{if(document.getElementById('deliveryChart'))renderDlv();}catch(e){console.warn('dlv',e);}
  try{if(document.getElementById('chartKml'))renderKmlChart();}catch(e){console.warn('kml',e);}
  try{if(document.getElementById('chartCost'))renderCostChart();}catch(e){console.warn('cost',e);}
  renderOilPage();drpInit();
  let _rt=null;window.addEventListener('resize',()=>{clearTimeout(_rt);_rt=setTimeout(()=>{try{if(kmlChart)renderKmlChart();if(costChart)renderCostChart();if(dlvChart)renderDlv();}catch(e){}},250);});
  if(IS_PRIVILEGED&&MAIN_VIEW==='day'&&document.getElementById('entryRowsBody')){
    ilLoadOilPrice('diesel');
    const date=document.getElementById('il-work-date')?.value||todayStr();
    (async()=>{
      const hint=document.getElementById('entryLoadingHint');if(hint)hint.style.display='flex';
      try{await Promise.all([fetchJobsByDate(date),fetchSavedDrivers(date)]);ilRenderDriverRows(date);}
      catch(e){console.warn('init entry',e);}
      finally{if(hint)hint.style.display='none';}
    })();
  }
});

</script>

</body>
</html>