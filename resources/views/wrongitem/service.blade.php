<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการเซอร์วิส</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:    #1a3a6b;
            --primary-d:  #122a52;
            --primary-l:  #e8edf6;
            --accent:     #c8932a;
            --border:     #d0d5dd;
            --border-l:   #e4e7ec;
            --bg:         #f5f6f8;
            --white:      #ffffff;
            --text:       #101828;
            --text-2:     #344054;
            --muted:      #667085;
            --muted-l:    #98a2b3;
            --success:    #027a48;
            --success-bg: #ecfdf3;
            --success-bd: #abefc6;
            --warn:       #b54708;
            --warn-bg:    #fef6ee;
            --warn-bd:    #f9dbaf;
            --danger:     #b42318;
            --danger-bg:  #fef3f2;
            --danger-bd:  #fecdca;
            --info:       #175cd3;
            --info-bg:    #eff4ff;
            --info-bd:    #b2ccff;
            --mono:       'IBM Plex Mono', monospace;
            --font:       'Noto Sans Thai', sans-serif;
            --radius:     6px;
            --shadow-sm:  0 1px 3px rgba(16,24,40,.08), 0 1px 2px rgba(16,24,40,.06);
        }

        html, body { height: 100%; }
        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            display: flex;
            flex-direction: column;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: var(--primary);
            height: 60px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,.18);
        }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand-logo {
            width: 34px; height: 34px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: var(--radius);
            display: flex; align-items: center; justify-content: center;
        }
        .brand-logo svg { color: #fff; }
        .brand-name { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.2; }
        .brand-sub  { font-size: 10px; color: rgba(255,255,255,.5); letter-spacing: .06em; text-transform: uppercase; font-weight: 500; }
        .topbar-divider { width: 1px; height: 28px; background: rgba(255,255,255,.15); }
        .topbar-right { display: flex; align-items: center; gap: 10px; }
        .hdr-date { font-size: 12px; color: rgba(255,255,255,.65); font-family: var(--mono); }
        .hdr-user {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 20px; padding: 4px 12px 4px 6px;
        }
        .hdr-avatar {
            width: 24px; height: 24px; border-radius: 50%;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 700; color: #fff;
        }
        .hdr-username { font-size: 12px; color: rgba(255,255,255,.85); font-weight: 500; }

        /* ── MAIN ── */
        main {
            flex: 1; display: flex; flex-direction: column;
            padding: 20px 28px; min-height: 0;
        }

        /* ── TABLE CARD ── */
        .table-card {
            background: var(--white); border: 1px solid var(--border-l);
            border-radius: var(--radius); box-shadow: var(--shadow-sm);
            flex: 1; display: flex; flex-direction: column;
            min-height: 0; overflow: hidden;
        }
        .table-card-head {
            padding: 11px 18px; border-bottom: 1px solid var(--border-l);
            display: flex; align-items: center; justify-content: space-between;
            background: #fafafa; flex-shrink: 0; flex-wrap: wrap; gap: 10px;
        }
        .table-card-head-left { display: flex; align-items: center; gap: 10px; }
        .table-card-title { font-size: 13px; font-weight: 700; color: var(--text); }
        .record-badge {
            background: var(--primary-l); color: var(--primary);
            border: 1px solid #c5d3ec; border-radius: 20px; padding: 1px 9px;
            font-size: 11px; font-weight: 700; font-family: var(--mono);
        }
        .search-wrap { position: relative; }
        .search-wrap svg {
            position: absolute; left: 10px; top: 50%;
            transform: translateY(-50%); color: var(--muted-l); pointer-events: none;
        }
        .search-wrap input {
            height: 32px; border: 1px solid var(--border); border-radius: var(--radius);
            padding: 0 12px 0 32px; font-family: var(--font); font-size: 12px;
            color: var(--text); background: var(--white); outline: none;
            width: 220px; transition: all .15s;
            box-shadow: 0 1px 2px rgba(16,24,40,.05);
        }
        .search-wrap input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26,58,107,.08);
            width: 260px;
        }

        /* ── BUTTONS ── */
        .btn-primary {
            height: 36px; background: var(--primary); color: #fff;
            border: 1px solid var(--primary-d); border-radius: var(--radius);
            padding: 0 16px; font-family: var(--font); font-size: 13px; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; gap: 7px;
            transition: all .15s; box-shadow: 0 1px 2px rgba(16,24,40,.1);
            white-space: nowrap;
        }
        .btn-primary:hover { background: var(--primary-d); }
        .btn-primary:active { transform: translateY(1px); }
        .btn-secondary {
            height: 36px; background: var(--white); color: var(--text-2);
            border: 1px solid var(--border); border-radius: var(--radius);
            padding: 0 14px; font-family: var(--font); font-size: 13px; font-weight: 500;
            cursor: pointer; display: flex; align-items: center; gap: 6px;
            transition: all .15s; box-shadow: 0 1px 2px rgba(16,24,40,.05);
        }
        .btn-secondary:hover { background: #f9fafb; border-color: #9ca3af; }
        .btn-danger {
            height: 28px; background: var(--danger-bg); color: var(--danger);
            border: 1px solid var(--danger-bd); border-radius: 4px;
            padding: 0 10px; font-family: var(--font); font-size: 11px; font-weight: 600;
            cursor: pointer; display: inline-flex; align-items: center; gap: 4px;
            transition: all .15s;
        }
        .btn-danger:hover { background: var(--danger); color: #fff; }

        /* ── TABLE ── */
        .table-scroll { flex: 1; overflow: auto; }
        table { width: 100%; border-collapse: collapse; }

        thead th {
            padding: 10px 14px;
            text-align: left;
            font-size: 11px; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: .07em;
            background: #f9fafb;
            border-bottom: 2px solid var(--border-l);
            white-space: nowrap;
            position: sticky; top: 0; z-index: 2;
        }
        thead th.th-center { text-align: center; }

        tbody tr { border-bottom: 1px solid var(--border-l); transition: background .1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:nth-child(even) { background: #fafbfc; }
        tbody tr:hover { background: #edf2fb !important; }

        tbody td {
            padding: 10px 14px;
            font-size: 13px; vertical-align: middle;
            color: var(--text-2);
        }
        tbody td.td-center { text-align: center; }

        .cell-date { font-family: var(--mono); font-size: 12px; color: var(--muted); }
        .cell-name { font-weight: 600; color: var(--text); }
        .cell-code {
            font-family: var(--mono); font-size: 11px;
            color: var(--info); font-weight: 600;
            background: var(--info-bg); border: 1px solid var(--info-bd);
            border-radius: 4px; padding: 2px 8px; display: inline-block;
        }
        .cell-amount {
            font-family: var(--mono); font-size: 12px; font-weight: 600;
            color: var(--success);
        }
        .table-img {
            width: 46px; height: 46px; object-fit: cover;
            border-radius: 6px; border: 1px solid var(--border-l);
            display: block; margin: 0 auto;
        }
        .no-img {
            width: 46px; height: 46px; background: var(--bg);
            border: 1px solid var(--border-l); border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; color: var(--muted-l); margin: 0 auto;
            text-align: center; line-height: 1.3;
        }

        /* ── TYPE BADGE ── */
        .type-badge {
            display: inline-flex; align-items: center; gap: 5px;
            border-radius: 20px; padding: 3px 10px;
            font-size: 11px; font-weight: 700; border: 1px solid;
        }
        .type-check  { background: var(--success-bg); color: var(--success); border-color: var(--success-bd); }
        .type-fix    { background: var(--warn-bg);    color: var(--warn);    border-color: var(--warn-bd);    }
        .type-claim  { background: var(--info-bg);    color: var(--info);    border-color: var(--info-bd);    }
        .type-urgent { background: var(--danger-bg);  color: var(--danger);  border-color: var(--danger-bd);  }
        .type-other  { background: #f9fafb;            color: #4b5563;        border-color: var(--border);     }

        /* ── TABLE FOOTER ── */
        .table-card-foot {
            padding: 9px 18px; border-top: 1px solid var(--border-l);
            background: #fafafa;
            display: flex; justify-content: space-between; align-items: center;
            font-size: 11px; color: var(--muted); font-family: var(--mono);
            flex-shrink: 0;
        }

        /* ── EMPTY ── */
        .empty-cell { padding: 64px 24px !important; text-align: center !important; }
        .empty-icon {
            width: 50px; height: 50px; background: var(--bg);
            border: 1px solid var(--border-l); border-radius: var(--radius);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px; color: var(--muted-l);
        }
        .empty-t { font-size: 13px; font-weight: 600; color: var(--text-2); margin-bottom: 4px; }
        .empty-s { font-size: 12px; color: var(--muted); }

        /* ── MODAL ── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 200;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none;
            transition: opacity .2s;
        }
        .modal-overlay.show { opacity: 1; pointer-events: all; }
        .modal {
            background: var(--white);
            border-radius: 10px;
            width: 520px;
            max-width: calc(100vw - 32px);
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            transform: translateY(10px) scale(.98);
            transition: transform .2s;
            overflow: hidden;
        }
        .modal-overlay.show .modal { transform: translateY(0) scale(1); }
        .modal-head {
            padding: 18px 22px 14px;
            border-bottom: 1px solid var(--border-l);
            display: flex; align-items: center; gap: 12px;
        }
        .modal-icon {
            width: 40px; height: 40px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            background: var(--primary-l); color: var(--primary);
            border: 1px solid #c5d3ec;
        }
        .modal-title { font-size: 15px; font-weight: 700; color: var(--text); }
        .modal-sub   { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .modal-close {
            margin-left: auto; background: none; border: none;
            cursor: pointer; color: var(--muted-l); padding: 4px;
            border-radius: 4px; transition: all .15s;
        }
        .modal-close:hover { background: var(--bg); color: var(--text); }
        .modal-body { padding: 20px 22px; display: flex; flex-direction: column; gap: 14px; }
        .modal-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal-field { display: flex; flex-direction: column; gap: 5px; }
        .modal-field label { font-size: 12px; font-weight: 600; color: var(--text-2); }
        .modal-field input,
        .modal-field select,
        .modal-field textarea {
            border: 1px solid var(--border); border-radius: var(--radius);
            padding: 0 12px; font-family: var(--font); font-size: 13px; color: var(--text);
            background: var(--white); outline: none; width: 100%;
            transition: border-color .15s, box-shadow .15s;
            height: 36px;
        }
        .modal-field textarea { height: 70px; padding: 8px 12px; resize: none; }
        .modal-field input:focus,
        .modal-field select:focus,
        .modal-field textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26,58,107,.1);
        }

        /* ── IMAGE UPLOAD ── */
        .upload-box {
            height: 80px;
            border: 2px dashed var(--border);
            border-radius: var(--radius);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 4px;
            cursor: pointer;
            background: #fafafa;
            color: var(--muted);
            font-size: 12px;
            transition: all .15s;
            overflow: hidden;
            position: relative;
        }
        .upload-box:hover { border-color: var(--primary); background: var(--primary-l); color: var(--primary); }
        .upload-box img { width: 100%; height: 100%; object-fit: cover; display: none; position: absolute; inset: 0; }
        .upload-box.has-img img { display: block; }
        .upload-box.has-img .upload-placeholder { display: none; }

        .modal-foot {
            padding: 14px 22px;
            border-top: 1px solid var(--border-l);
            background: #fafafa;
            display: flex; justify-content: flex-end; gap: 10px;
        }

        /* ── TOAST ── */
        .toast {
            position: fixed; bottom: 28px; left: 50%;
            transform: translateX(-50%) translateY(12px);
            background: var(--text); color: #fff;
            padding: 10px 20px; border-radius: var(--radius);
            font-size: 12px; font-weight: 600; opacity: 0;
            transition: all .22s ease; z-index: 999; pointer-events: none;
            white-space: nowrap; box-shadow: 0 4px 12px rgba(0,0,0,.18);
            border-left: 3px solid var(--accent);
        }
        .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

        @media (max-width: 768px) {
            .topbar, main { padding-left: 16px; padding-right: 16px; }
            .modal-row { grid-template-columns: 1fr; }
            .hdr-date { display: none; }
        }
    </style>
</head>
<body>

<header class="topbar">
    <div class="brand">
        <div class="brand-logo">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
            </svg>
        </div>
        <div>
            <div class="brand-name">ระบบจัดการเซอร์วิส</div>
            <div class="brand-sub">Service Management System</div>
        </div>
    </div>
    <div class="topbar-right">
        <span class="hdr-date" id="hdr-date"></span>
        <div class="topbar-divider"></div>
        <div class="hdr-user">
            <div class="hdr-avatar">A</div>
            <span class="hdr-username">Admin</span>
        </div>
    </div>
</header>

<main>
    <div class="table-card">
        <div class="table-card-head">
            <div class="table-card-head-left">
                <span class="table-card-title">ตารางข้อมูลการบำรุงรักษารถยนต์</span>
                <span class="record-badge" id="tbl-count">0 รายการ</span>
            </div>
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <div class="search-wrap">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" placeholder="ค้นหาในตาราง..." oninput="doSearch(this.value)">
                </div>
                <button class="btn-primary" onclick="openModal()">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    เพิ่มข้อมูลเซอร์วิส
                </button>
            </div>
        </div>

        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>วันที่</th>
                        <th>รหัส</th>
                        <th>ชื่อ</th>
                        <th>ประเภท</th>
                        <th>ค่าใช้จ่าย</th>
                        <th class="th-center">รูปภาพ</th>
                        <th>หมายเหตุ</th>
                        <th class="th-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    <tr><td colspan="9" class="empty-cell">
                        <div class="empty-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                        </div>
                        <div class="empty-t">ยังไม่มีข้อมูล</div>
                        <div class="empty-s">กดปุ่ม "เพิ่มข้อมูลเซอร์วิส" เพื่อเริ่มต้น</div>
                    </td></tr>
                </tbody>
            </table>
        </div>

        <div class="table-card-foot">
            <span id="ft-info">—</span>
            <span id="ft-time">—</span>
        </div>
    </div>
</main>

<!-- ── MODAL เพิ่มข้อมูล ── -->
<div class="modal-overlay" id="serviceModal">
    <div class="modal">
        <div class="modal-head">
            <div class="modal-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
                </svg>
            </div>
            <div>
                <div class="modal-title">เพิ่มข้อมูลการเข้ารับบริการ</div>
                <div class="modal-sub">กรอกข้อมูลให้ครบถ้วนก่อนบันทึก</div>
            </div>
            <button class="modal-close" onclick="closeModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-row">
                <div class="modal-field">
                    <label>วันที่</label>
                    <input type="date" id="inputDate">
                </div>
                <div class="modal-field">
                    <label>ชื่อ คนขับ</label>
                    <select id="inputName">
                        <option value="">— เลือก คนขับ —</option>
                        <option value="บังเดช">บังเดช</option>
                        <option value="แชม">แชม</option>
                        <option value="กอล์ฟ">กอล์ฟ</option>
                        <option value="หรั่ง">หรั่ง</option>
                        <option value="เก่ง">เก่ง</option>
                        <option value="เอ">เอ</option>
                        <option value="ยุทร">ยุทร</option>
                        <option value="แฟรงค์">แฟรงค์</option>
                        <option value="เอ้">เอ้</option>
                    </select>
                </div>
            </div>
            <div class="modal-row">
                <div class="modal-field">
                    <label>ประเภทงาน</label>
                    <select id="inputType">
                        <option value="check">เช็คระยะทั่วไป</option>
                        <option value="fix">ซ่อมบำรุง</option>
                        <option value="claim">เคลมประกัน</option>
                        <option value="urgent">ฉุกเฉิน</option>
                        <option value="other">อื่นๆ</option>
                    </select>
                </div>
                <div class="modal-field">
                    <label>ค่าใช้จ่าย (บาท)</label>
                    <input type="number" id="inputAmount" placeholder="เช่น 5000">
                </div>
            </div>
            <div class="modal-field">
                <label>หมายเหตุ</label>
                <textarea id="inputNote" placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
            </div>
            <div class="modal-field">
                <label>รูปภาพ (ถ้ามี)</label>
                <div class="upload-box" id="uploadBox" onclick="document.getElementById('fileInput').click()">
                    <div class="upload-placeholder" style="display:flex;flex-direction:column;align-items:center;gap:4px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        <span>คลิกเพื่อเลือกรูป</span>
                    </div>
                    <img id="previewImg" alt="preview">
                </div>
                <input type="file" id="fileInput" style="display:none;" accept="image/*" onchange="previewImage(event)">
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn-secondary" onclick="closeModal()">ยกเลิก</button>
            <button class="btn-primary" onclick="submitData()">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                บันทึกข้อมูล
            </button>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
let records = [];
let currentImageSrc = null;
let searchQ = '';

const TYPE_MAP = {
    check:  { label: 'เช็คระยะทั่วไป', cls: 'type-check'  },
    fix:    { label: 'ซ่อมบำรุง',       cls: 'type-fix'   },
    claim:  { label: 'เคลมประกัน',      cls: 'type-claim' },
    urgent: { label: 'ฉุกเฉิน',          cls: 'type-urgent'},
    other:  { label: 'อื่นๆ',            cls: 'type-other' },
};

/* ── INIT ── */
(function init() {
    const now = new Date();
    document.getElementById('hdr-date').textContent =
        now.toLocaleDateString('th-TH', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
    document.getElementById('inputDate').value = now.toISOString().slice(0,10);
    render();
})();

/* ── RENDER ── */
function render() {
    const q = searchQ.toLowerCase().trim();
    const data = q ? records.filter(r =>
        r.name.toLowerCase().includes(q) ||
        r.code.toLowerCase().includes(q) ||
        r.note.toLowerCase().includes(q) ||
        TYPE_MAP[r.type]?.label.includes(q)
    ) : records;

    document.getElementById('tbl-count').textContent = records.length + ' รายการ';
    const t = new Date();
    document.getElementById('ft-info').textContent = `แสดง ${data.length} จาก ${records.length} รายการ`;
    document.getElementById('ft-time').textContent = `อัพเดต ${t.toLocaleTimeString('th-TH')}`;

    if (!data.length) {
        document.getElementById('tbody').innerHTML = records.length === 0
            ? `<tr><td colspan="9" class="empty-cell">
                <div class="empty-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg></div>
                <div class="empty-t">ยังไม่มีข้อมูล</div>
                <div class="empty-s">กดปุ่ม "เพิ่มข้อมูลเซอร์วิส" เพื่อเริ่มต้น</div>
              </td></tr>`
            : `<tr><td colspan="9" class="empty-cell">
                <div class="empty-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>
                <div class="empty-t">ไม่พบรายการ</div>
                <div class="empty-s">ไม่มีข้อมูลที่ตรงกับคำค้นหา</div>
              </td></tr>`;
        return;
    }

    document.getElementById('tbody').innerHTML = data.map((r, i) => {
        const typeInfo = TYPE_MAP[r.type] || TYPE_MAP.other;
        const imgHTML  = r.img
            ? `<img src="${r.img}" class="table-img" alt="รูป">`
            : `<div class="no-img">ไม่มีรูป</div>`;
        const amountHTML = r.amount
            ? `<span class="cell-amount">฿${Number(r.amount).toLocaleString()}</span>`
            : `<span style="color:var(--muted-l)">—</span>`;

        return `<tr>
            <td style="font-family:var(--mono);font-size:11px;color:var(--muted-l)">${i+1}</td>
            <td><span class="cell-date">${r.date}</span></td>
            <td><span class="cell-code">${r.code}</span></td>
            <td><span class="cell-name">${r.name}</span></td>
            <td><span class="type-badge ${typeInfo.cls}">${typeInfo.label}</span></td>
            <td>${amountHTML}</td>
            <td class="td-center">${imgHTML}</td>
            <td style="font-size:12px;color:var(--muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${r.note}">${r.note || '—'}</td>
            <td class="td-center">
                <button class="btn-danger" onclick="deleteRecord('${r.id}')">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                    ลบ
                </button>
            </td>
        </tr>`;
    }).join('');
}

/* ── SEARCH ── */
function doSearch(q) {
    searchQ = q;
    render();
}

/* ── MODAL ── */
function openModal() {
    currentImageSrc = null;
    document.getElementById('inputDate').value  = new Date().toISOString().slice(0,10);
    document.getElementById('inputName').value  = '';
    document.getElementById('inputType').value  = 'check';
    document.getElementById('inputAmount').value = '';
    document.getElementById('inputNote').value  = '';
    document.getElementById('fileInput').value  = '';
    document.getElementById('previewImg').src   = '';
    document.getElementById('uploadBox').classList.remove('has-img');
    document.getElementById('serviceModal').classList.add('show');
}

function closeModal() {
    document.getElementById('serviceModal').classList.remove('show');
}

document.getElementById('serviceModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

/* ── IMAGE PREVIEW ── */
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        currentImageSrc = e.target.result;
        document.getElementById('previewImg').src = currentImageSrc;
        document.getElementById('uploadBox').classList.add('has-img');
    };
    reader.readAsDataURL(file);
}

/* ── SUBMIT ── */
function submitData() {
    const dateVal   = document.getElementById('inputDate').value;
    const nameVal   = document.getElementById('inputName').value;
    const typeVal   = document.getElementById('inputType').value;
    const amountVal = document.getElementById('inputAmount').value;
    const noteVal   = document.getElementById('inputNote').value.trim();

    if (!dateVal) { showToast('⚠ กรุณาเลือกวันที่'); return; }
    if (!nameVal) { showToast('⚠ กรุณาเลือกชื่อคนขับ'); return; }

    const [y, m, d] = dateVal.split('-');
    const code = 'SVC-' + Math.floor(10000 + Math.random() * 90000);

    records.push({
        id:     code,
        date:   `${d}/${m}/${y}`,
        code,
        name:   nameVal,
        type:   typeVal,
        amount: amountVal,
        note:   noteVal,
        img:    currentImageSrc,
    });

    render();
    closeModal();
    showToast(`✓ เพิ่มรายการ ${nameVal} (${code}) เรียบร้อย`);
}

/* ── DELETE ── */
function deleteRecord(id) {
    if (!confirm('คุณต้องการลบข้อมูลนี้ใช่หรือไม่?')) return;
    records = records.filter(r => r.id !== id);
    render();
    showToast('✓ ลบรายการเรียบร้อยแล้ว');
}

/* ── TOAST ── */
function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
}
</script>
</body>
</html>