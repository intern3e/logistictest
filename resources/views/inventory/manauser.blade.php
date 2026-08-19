<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>จัดการผู้ใช้งาน - 3E TRADING</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Sarabun',Arial,sans-serif;background:#f9fafb;min-height:100vh;padding-bottom:40px;color:#1f2937}

        /* Loading Overlay */
        .ov{position:fixed;inset:0;background:rgba(0,0,0,.85);display:flex;justify-content:center;align-items:center;z-index:9999;opacity:0;visibility:hidden;transition:opacity .3s,visibility .3s;backdrop-filter:blur(4px)}
        .ov.on{opacity:1;visibility:visible}
        .progress-container{width:320px;text-align:center}
        .ov-text{color:#fff;font-size:18px;font-weight:600;margin-bottom:20px;letter-spacing:.5px}
        .progress-track{width:100%;height:32px;background:rgba(255,255,255,.1);border:2px solid rgba(255,255,255,.3);border-radius:4px;overflow:hidden;margin-bottom:12px;box-shadow:inset 0 2px 4px rgba(0,0,0,.3)}
        .progress-fill{width:0%;height:100%;background-color:#5B65F3;background-image:repeating-linear-gradient(90deg,#5B65F3 0px,#5B65F3 18px,rgba(255,255,255,.15) 18px,rgba(255,255,255,.15) 20px);background-size:20px 100%;transition:width .15s ease-out;box-shadow:0 0 15px rgba(91,101,243,.6)}
        .ov-percent{color:#fff;font-size:16px;font-weight:700;font-variant-numeric:tabular-nums;text-shadow:0 2px 4px rgba(0,0,0,.3)}

        /* Topbar */
        .topbar{height:64px;background:#fff;display:flex;align-items:center;gap:12px;padding:0 20px;position:fixed;top:0;left:0;right:0;z-index:2000;border-bottom:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.05)}
        .hamburger{background:none;border:none;cursor:pointer;padding:6px;display:flex;flex-direction:column;gap:4px;flex-shrink:0;border-radius:6px}
        .hamburger:hover{background:#f3f4f6}
        .hamburger span{display:block;width:20px;height:2px;background:#374151;border-radius:1px}
        .topbar-logo{height:36px;border-radius:6px}
        .topbar-title{font-size:18px;font-weight:700;color:#111827;flex:1;letter-spacing:-0.025em}
        .topbar-right{display:flex;align-items:center;gap:12px}
        .topbar-name{font-size:14px;color:#6b7280;font-weight:500}
        .topbar-badge{font-size:12px;padding:4px 10px;font-weight:600;color:#5B65F3;background:#EEF2FF;border-radius:6px;border:1px solid #C7D2FE}
        .btn-logout{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;font-family:'Sarabun',sans-serif;font-size:14px;font-weight:600;color:#fff;text-decoration:none;cursor:pointer;white-space:nowrap;background:#ef4444;border-radius:8px;box-shadow:0 1px 3px rgba(239,68,68,.3);transition:all .2s;border:none}
        .btn-logout:hover{background:#dc2626;box-shadow:0 4px 6px rgba(239,68,68,.2);transform:translateY(-1px)}
        .btn-logout:active{transform:translateY(0)}

        /* Sidebar */
        .sb-ov{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1500;opacity:0;pointer-events:none;transition:opacity .2s}
        .sb-ov.open{opacity:1;pointer-events:all}
        .sidebar{position:fixed;top:0;left:-260px;width:240px;height:100vh;z-index:1600;transition:left .25s ease;display:flex;flex-direction:column;background:#fff;border-right:1px solid #e5e7eb;box-shadow:3px 0 16px rgba(0,0,0,.08)}
        .sidebar.open{left:0}
        .sb-head{display:flex;align-items:center;gap:8px;padding:12px 14px;background:#5B65F3;min-height:64px}
        .sb-head img{height:30px;border-radius:6px}
        .sb-head span{font-size:16px;font-weight:700;color:#fff;flex:1}
        .sb-close{background:none;border:none;color:rgba(255,255,255,.85);cursor:pointer;font-size:18px;font-weight:bold;padding:0 4px}
        .sb-nav{flex:1;overflow-y:auto;padding:10px 0}
        .sb-sec{padding:10px 16px 4px;font-size:11px;font-weight:700;color:#9ca3af;letter-spacing:.8px;text-transform:uppercase}
        .sb-item{display:flex;align-items:center;gap:10px;padding:10px 16px;color:#374151;cursor:pointer;font-size:14px;font-weight:500;border-left:3px solid transparent;user-select:none;text-decoration:none}
        .sb-item:hover{background:#f3f4f6;border-left-color:#c7d2fe}
        .sb-item.cur{background:#EEF2FF;border-left-color:#5B65F3;color:#4F46E5;font-weight:700}
        .sb-item svg{flex-shrink:0}
        .sb-div{height:1px;background:#e5e7eb;margin:6px 14px}

        /* Content */
        #content{padding:88px 16px 24px;width:100%}
        .card{margin-bottom:20px;padding:20px;background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.05)}
        .card-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:4px}
        .card h2{font-size:20px;font-weight:700;color:#111827;letter-spacing:-0.025em}

        .btn{padding:10px 20px;font-size:14px;font-weight:600;font-family:'Sarabun',sans-serif;cursor:pointer;white-space:nowrap;border:none;border-radius:8px;transition:all .2s}
        .btn-add{background:#10b981;color:#fff}
        .btn-add:hover{background:#059669}
        .btn-edit{background:#5B65F3;color:#fff}
        .btn-edit:hover{background:#4F46E5}
        .btn-save{background:#10b981;color:#fff}
        .btn-save:hover{background:#059669}
        .btn-del{background:#ef4444;color:#fff}
        .btn-del:hover{background:#dc2626}
        .btn-can{background:#e5e7eb;color:#374151}
        .btn-can:hover{background:#d1d5db}

        /* Table */
        .tbl-wrap{background:#fff;overflow-x:auto;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
        table{width:100%;border-collapse:collapse;font-size:14px}
        thead{background:#f9fafb}
        th{color:#374151;padding:14px 16px;text-align:left;font-weight:600;font-size:13px;white-space:nowrap;position:sticky;top:0;background:#f9fafb;z-index:5;border:1px solid #e5e7eb;border-top:none}
        th:first-child{border-left:none}
        th:last-child{border-right:none}
        td{padding:12px 16px;border:1px solid #e5e7eb;color:#1f2937;font-size:14px;vertical-align:middle}
        td:first-child{border-left:none}
        td:last-child{border-right:none}
        tbody tr:hover{background:#f9fafb}

        /* Badges */
        .badge{display:inline-block;padding:4px 10px;font-size:12px;font-weight:600;white-space:nowrap;border-radius:6px}
        .b-admin{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
        .b-user{background:#dbeafe;color:#1d4ed8;border:1px solid #93c5fd}
        .b-viewer{background:#f3f4f6;color:#374151;border:1px solid #d1d5db}
        .b-role{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
        .b-page{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
        .b-perm{background:#fef3c7;color:#92400e;border:1px solid #fde68a}
        .perm-wrap{display:flex;flex-wrap:wrap;gap:4px;max-width:260px}

        /* Edit Inputs */
        .finput{padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;background:#fff;font-family:'Sarabun',sans-serif;width:100%;transition:all .2s}
        .finput:focus{outline:none;border-color:#5B65F3;box-shadow:0 0 0 3px rgba(91,101,243,.1)}

        /* Permission checkbox list */
        .perm-box{max-height:150px;overflow-y:auto;border:1px solid #d1d5db;border-radius:6px;padding:6px 8px;background:#fff;min-width:190px}
        .perm-box label{display:flex;align-items:center;gap:6px;font-size:12.5px;padding:3px 0;color:#374151;cursor:pointer;white-space:nowrap}
        .perm-box input{cursor:pointer}
        .perm-empty{font-size:12px;color:#9ca3af;padding:2px 0}

        /* Actions */
        .act-btns{display:flex;gap:6px;flex-wrap:wrap}
        .act-btns button{padding:6px 12px;font-size:13px;border-radius:6px}

        /* Toast */
        .toast{position:fixed;bottom:24px;right:24px;padding:12px 24px;font-size:14px;font-weight:600;z-index:9999;color:#fff;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.15);opacity:0;transition:opacity .3s}

        @media(max-width:768px){#content{padding:80px 8px 16px}}
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="ov" id="ov">
        <div class="progress-container">
            <p class="ov-text" id="ovText">กำลังโหลดข้อมูล...</p>
            <div class="progress-track">
                <div class="progress-fill" id="progressBar"></div>
            </div>
            <p class="ov-percent" id="ovPercent">0%</p>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast"></div>

    <!-- Sidebar -->
    <div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
    <div class="sidebar" id="sidebar">
        <div class="sb-head">
            <img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo">
            <span>3E TRADING</span>
            <button class="sb-close" onclick="closeSB()">&#10005;</button>
        </div>
        <div class="sb-nav">
            <div class="sb-sec">เมนูหลัก</div>
            <a class="sb-item" target="_blank" href="{{ route('inventory.transaction') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                รายการสินค้า เข้า-ออก
            </a>
            <a class="sb-item" target="_blank" href="{{ route('inventory.item') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
                ค้นหาสินค้า
            </a>
            <div class="sb-div"></div>
            <div class="sb-sec">จัดการระบบ</div>
            <a class="sb-item cur" target="_blank" href="{{ route('inventory.users') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                จัดการ User
            </a>
        </div>
    </div>

    <!-- Topbar -->
    <div class="topbar">
        <button class="hamburger" onclick="openSB()"><span></span><span></span><span></span></button>
        <img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo" class="topbar-logo">
        <span class="topbar-title">จัดการผู้ใช้งาน</span>
        <div class="topbar-right">
            <span class="topbar-name">ผู้ใช้: {{ $authUser['name'] ?? 'Admin' }}</span>
            <span class="topbar-badge">ADMIN</span>
            <a href="{{ route('logout') }}" class="btn-logout">ออกจากระบบ</a>
        </div>
    </div>

    <!-- Content -->
    <div id="content">
        <div class="card">
            <div class="card-header">
                <h2>จัดการผู้ใช้งาน</h2>
                <button class="btn btn-add" onclick="addNewUserRow()">+ เพิ่มผู้ใช้ใหม่</button>
            </div>
        </div>

        <div class="tbl-wrap">
            <table id="userTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Name</th>
                        <th>Auth</th>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Page</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let users = [];
    let roleCatalog = {};
    let progressInterval = null;

    // ============================================
    // Permission Catalog (fixed list, matches DB data)
    // แก้/เพิ่มรายการสิทธิ์ตรงนี้ได้เลยถ้ามีโมดูลอื่นเพิ่ม
    // ============================================
    const permissionCatalog = [
        'disbrument_create',
        'disbrument_read',
        'disbrument_update',
        'disbrument_delete',
        'disbrument_clear',
    ];

    // ============================================
    // Sidebar
    // ============================================
    function openSB(){ document.getElementById('sidebar').classList.add('open'); document.getElementById('sbOv').classList.add('open'); }
    function closeSB(){ document.getElementById('sidebar').classList.remove('open'); document.getElementById('sbOv').classList.remove('open'); }

    // ============================================
    // Loading Overlay (progress bar)
    // ============================================
    function showOv(t){
        document.getElementById('ovText').textContent = t || 'กำลังโหลดข้อมูล...';
        document.getElementById('ov').classList.add('on');
        startProgressSimulation();
    }

    function hideOv(){
        clearInterval(progressInterval);
        document.getElementById('progressBar').style.width = '100%';
        document.getElementById('ovPercent').textContent = '100%';

        setTimeout(() => {
            document.getElementById('ov').classList.remove('on');
            setTimeout(() => {
                document.getElementById('progressBar').style.width = '0%';
                document.getElementById('ovPercent').textContent = '0%';
            }, 300);
        }, 200);
    }

    function startProgressSimulation(){
        let progress = 0;
        const bar = document.getElementById('progressBar');
        const text = document.getElementById('ovPercent');

        clearInterval(progressInterval);
        progressInterval = setInterval(() => {
            if (progress < 30) progress += Math.random() * 15;
            else if (progress < 70) progress += Math.random() * 8;
            else if (progress < 90) progress += Math.random() * 3;
            else progress = 90;

            if (progress > 90) progress = 90;
            bar.style.width = Math.floor(progress) + '%';
            text.textContent = Math.floor(progress) + '%';
        }, 150);
    }

    function toast(m, isError = false) {
        const t = document.getElementById('toast');
        t.textContent = m;
        t.style.background = isError ? '#ef4444' : '#10b981';
        t.style.opacity = '1';
        clearTimeout(t._t);
        t._t = setTimeout(() => t.style.opacity = '0', 2500);
    }

    function esc(s) {
        return (s || '').toString().replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // Normalize a user's permissions value (could be null, JSON string, or array) into an array
    function permArray(val){
        if (!val) return [];
        if (Array.isArray(val)) return val;
        try {
            const parsed = JSON.parse(val);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    }

    // ============================================
    // Load Data
    // ============================================
    async function loadRoleCatalog() {
        try {
            const res = await (await fetch('/api/role-catalog')).json();
            roleCatalog = res.roles || {};
        } catch (e) {
            console.error('Failed to load role catalog:', e);
        }
    }

    async function loadUsers() {
        showOv();
        try {
            users = await (await fetch('/api/users')).json();
            renderTable();
        } catch (e) {
            console.error(e);
            toast('เกิดข้อผิดพลาดในการโหลดข้อมูล', true);
        }
        hideOv();
    }

    // ============================================
    // Select / Checkbox Helpers
    // ============================================
    function authSelect(id, val) {
        return `<select id="${id}" class="finput">
            <option value="admin" ${val === 'admin' ? 'selected' : ''}>Admin</option>
            <option value="user" ${val === 'user' || !val ? 'selected' : ''}>User</option>
            <option value="viewer" ${val === 'viewer' ? 'selected' : ''}>Viewer</option>
        </select>`;
    }

    function roleSelect(id, val) {
        let opts = `<option value="">- ไม่กำหนด -</option>`;
        for (const key of Object.keys(roleCatalog)) {
            opts += `<option value="${key}" ${val === key ? 'selected' : ''}>${key}</option>`;
        }
        return `<select id="${id}" class="finput">${opts}</select>`;
    }

    // Checkbox group for permissions, independent from role
    function permissionCheckboxes(idPrefix, selectedArr) {
        if (!permissionCatalog.length) {
            return `<div class="perm-box"><div class="perm-empty">ไม่มีสิทธิ์ในระบบ</div></div>`;
        }
        const selected = new Set(selectedArr || []);
        let html = `<div class="perm-box" id="${idPrefix}-box">`;
        permissionCatalog.forEach((perm, i) => {
            html += `<label>
                <input type="checkbox" class="${idPrefix}-cb" value="${esc(perm)}" ${selected.has(perm) ? 'checked' : ''}>
                ${esc(perm)}
            </label>`;
        });
        html += `</div>`;
        return html;
    }

    function readCheckedPermissions(idPrefix) {
        return Array.from(document.querySelectorAll(`.${idPrefix}-cb:checked`)).map(cb => cb.value);
    }

    function authBadgeClass(auth) {
        return auth === 'admin' ? 'b-admin' : auth === 'viewer' ? 'b-viewer' : 'b-user';
    }

    // ============================================
    // Render Table
    // ============================================
    function renderTable() {
        const tbody = document.querySelector('#userTable tbody');
        tbody.innerHTML = '';

        if (!users.length) {
            tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:40px;color:#9ca3af">ไม่มีข้อมูลผู้ใช้งาน</td></tr>`;
            return;
        }

        users.forEach((user, index) => {
            const perms = permArray(user.permissions);
            const permHtml = perms.length
                ? `<div class="perm-wrap">${perms.map(p => `<span class="badge b-perm">${esc(p)}</span>`).join('')}</div>`
                : '-';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${esc(user.id_emp)}</strong></td>
                <td><strong>${esc(user.username)}</strong></td>
                <td>${esc(user.password)}</td>
                <td>${esc(user.name)}</td>
                <td><span class="badge ${authBadgeClass(user.auth)}">${esc((user.auth || '').toUpperCase())}</span></td>
                <td>${user.role ? `<span class="badge b-role">${esc(user.role)}</span>` : '-'}</td>
                <td>${permHtml}</td>
                <td>${user.page ? `<span class="badge b-page">${esc(user.page)}</span>` : '-'}</td>
                <td class="act-btns">
                    <button class="btn btn-edit" onclick="editRow(${index})">แก้ไข</button>
                    <button class="btn btn-del" onclick="deleteUser(${index})">ลบ</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // ============================================
    // Edit Row
    // ============================================
    function editRow(index) {
        const user = users[index];
        const tr = document.querySelector('#userTable tbody').children[index];
        if (!tr) return;

        tr.style.background = '#EEF2FF';
        tr.innerHTML = `
            <td style="color:#059669;font-weight:700">${esc(user.id_emp)}</td>
            <td><input class="finput" id="e-username-${index}" value="${esc(user.username)}"></td>
            <td><input class="finput" id="e-password-${index}" value="${esc(user.password)}"></td>
            <td><input class="finput" id="e-name-${index}" value="${esc(user.name)}"></td>
            <td>${authSelect('e-auth-' + index, user.auth)}</td>
            <td>${roleSelect('e-role-' + index, user.role)}</td>
            <td>${permissionCheckboxes('e-perm-' + index, permArray(user.permissions))}</td>
            <td><input class="finput" id="e-page-${index}" value="${esc(user.page)}" placeholder="เช่น pr"></td>
            <td class="act-btns">
                <button class="btn btn-save" onclick="saveEdit(${index})">บันทึก</button>
                <button class="btn btn-can" onclick="renderTable()">ยกเลิก</button>
            </td>
        `;
    }

    async function saveEdit(index) {
        const user = users[index];
        const d = {
            username: document.getElementById(`e-username-${index}`).value.trim(),
            password: document.getElementById(`e-password-${index}`).value.trim(),
            name: document.getElementById(`e-name-${index}`).value.trim(),
            auth: document.getElementById(`e-auth-${index}`).value,
            role: document.getElementById(`e-role-${index}`).value,
            permissions: readCheckedPermissions('e-perm-' + index),
            page: document.getElementById(`e-page-${index}`).value.trim(),
        };

        if (!d.username || !d.password || !d.name) {
            toast('กรุณากรอกข้อมูลให้ครบ (Username / Password / Name)', true);
            return;
        }

        showOv();
        try {
            const res = await (await fetch('/api/users/' + encodeURIComponent(user.id_emp), {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify(d)
            })).json();

            if (res.success) {
                toast('อัปเดตข้อมูลเรียบร้อยแล้ว');
                await loadUsers();
            } else {
                toast('เกิดข้อผิดพลาด: ' + (res.error || 'unknown'), true);
                hideOv();
            }
        } catch (e) {
            toast('เกิดข้อผิดพลาดในการอัปเดตข้อมูล', true);
            hideOv();
        }
    }

    // ============================================
    // Delete User
    // ============================================
    async function deleteUser(index) {
        const user = users[index];
        if (!confirm(`ลบผู้ใช้ ${user.username}?`)) return;

        showOv();
        try {
            const res = await (await fetch('/api/users/' + encodeURIComponent(user.id_emp), {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF }
            })).json();

            if (res.success) {
                toast('ลบข้อมูลสำเร็จ');
                await loadUsers();
            } else {
                toast('เกิดข้อผิดพลาด: ' + (res.error || 'unknown'), true);
                hideOv();
            }
        } catch (e) {
            toast('เกิดข้อผิดพลาดในการลบข้อมูล', true);
            hideOv();
        }
    }

    // ============================================
    // Add New User
    // ============================================
    function addNewUserRow() {
        if (document.getElementById('new-username')) {
            toast('มีแถวเพิ่มผู้ใช้อยู่แล้ว', true);
            return;
        }

        const tbody = document.querySelector('#userTable tbody');
        const tr = document.createElement('tr');
        tr.style.background = '#fef9c3';
        tr.innerHTML = `
            <td><em style="color:#6b7280;font-size:12px">auto</em></td>
            <td><input class="finput" id="new-username" placeholder="Username"></td>
            <td><input class="finput" id="new-password" placeholder="Password"></td>
            <td><input class="finput" id="new-name" placeholder="ชื่อ"></td>
            <td>${authSelect('new-auth', 'user')}</td>
            <td>${roleSelect('new-role', '')}</td>
            <td>${permissionCheckboxes('new-perm', [])}</td>
            <td><input class="finput" id="new-page" placeholder="เช่น pr (เว้นว่างได้)"></td>
            <td class="act-btns">
                <button class="btn btn-save" onclick="saveNewUser()">บันทึก</button>
                <button class="btn btn-can" onclick="renderTable()">ยกเลิก</button>
            </td>
        `;
        tbody.prepend(tr);
    }

    async function saveNewUser() {
        const d = {
            username: document.getElementById('new-username').value.trim(),
            password: document.getElementById('new-password').value.trim(),
            name: document.getElementById('new-name').value.trim(),
            auth: document.getElementById('new-auth').value,
            role: document.getElementById('new-role').value,
            permissions: readCheckedPermissions('new-perm'),
            page: document.getElementById('new-page').value.trim(),
        };

        if (!d.username || !d.password || !d.name) {
            toast('กรุณากรอกข้อมูลให้ครบ (Username / Password / Name)', true);
            return;
        }

        showOv();
        try {
            const res = await (await fetch('/api/users', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify(d)
            })).json();

            if (res.success) {
                toast('เพิ่มผู้ใช้ใหม่เรียบร้อย');
                await loadUsers();
            } else {
                toast('เกิดข้อผิดพลาด: ' + (res.error || 'unknown'), true);
                hideOv();
            }
        } catch (e) {
            toast('เกิดข้อผิดพลาดในการเพิ่มผู้ใช้', true);
            hideOv();
        }
    }

    // ============================================
    // Initialize
    // ============================================
    loadRoleCatalog().then(() => loadUsers());
</script>
</body>
</html>