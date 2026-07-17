<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>จัดการ User - 3E TRADING</title>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Sarabun',Arial,sans-serif;background:#ece9d8;min-height:100vh;padding-bottom:40px}
    .ov{position:fixed;inset:0;background:rgba(0,0,0,.8);display:flex;justify-content:center;align-items:center;z-index:9999;opacity:0;visibility:hidden;transition:opacity .3s,visibility .3s}
    .ov.on{opacity:1;visibility:visible}
    .sp{width:48px;height:48px;border:5px solid rgba(255,255,255,.3);border-top:5px solid #FCA50D;border-radius:50%;animation:sp 1s linear infinite;margin:0 auto 12px}
    @keyframes sp{to{transform:rotate(360deg)}}
    .ov p{color:#fff;font-size:16px}
    .topbar{height:52px;background:#444;display:flex;align-items:center;gap:10px;padding:0 12px;position:fixed;top:0;left:0;right:0;z-index:2000;border-bottom:2px solid #1a3f7a;box-shadow:0 2px 8px rgba(0,0,0,.5)}
    .topbar-logo{height:34px;border:1px solid rgba(255,255,255,.3)}
    .topbar-title{font-size:17px;font-weight:700;color:#fff;text-shadow:1px 1px 2px rgba(0,0,0,.5);flex:1}
    .topbar-right{display:flex;align-items:center;gap:10px}
    .topbar-name{font-size:13px;color:rgba(255,255,255,.8)}
    .topbar-badge{font-size:11px;padding:2px 8px;font-weight:700;color:#000;background:#FCA50D}
    .hamburger{background:none;border:none;cursor:pointer;padding:5px;display:flex;flex-direction:column;gap:4px;flex-shrink:0}
    .hamburger span{display:block;width:20px;height:2px;background:#fff}
    .hamburger:hover span{background:#ffe8a0}
    .sb-ov{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1500;opacity:0;pointer-events:none;transition:opacity .2s}
    .sb-ov.open{opacity:1;pointer-events:all}
    .sidebar{position:fixed;top:0;left:-250px;width:230px;height:100vh;z-index:1600;transition:left .25s ease;display:flex;flex-direction:column;background:linear-gradient(180deg,#dce9fa,#c5d9f5);border-right:2px solid #7a9fc8;box-shadow:3px 0 12px rgba(0,0,0,.3)}
    .sidebar.open{left:0}
    .sb-head{display:flex;align-items:center;gap:8px;padding:10px 12px;background:linear-gradient(180deg,#4a7db5,#2358a4);border-bottom:2px solid #1a3f7a;min-height:52px}
    .sb-head img{height:30px}
    .sb-head span{font-size:16px;font-weight:700;color:#fff;flex:1}
    .sb-close{background:none;border:none;color:rgba(255,255,255,.8);cursor:pointer;font-size:18px;font-weight:bold;padding:0 4px}
    .sb-nav{flex:1;overflow-y:auto;padding:6px 0}
    .sb-sec{padding:8px 12px 3px;font-size:11px;font-weight:700;color:#1e4d96;letter-spacing:.8px;text-transform:uppercase}
    .sb-item{display:flex;align-items:center;gap:10px;padding:9px 14px;color:#1a1a6e;cursor:pointer;font-size:14px;font-weight:500;border-left:3px solid transparent;user-select:none;text-decoration:none}
    .sb-item:hover{background:rgba(42,95,168,.15);border-left-color:#2a5fa8}
    .sb-item.cur{background:rgba(42,95,168,.2);border-left-color:#FCA50D;font-weight:700}
    #content{width:100%;margin:0;padding:62px 16px 16px}
    .content-header{padding:12px 0;margin-bottom:10px}
    .content-header h2{color:#333;font-size:20px;font-weight:700;margin-bottom:12px}
    .add-btn{padding:7px 16px;cursor:pointer;font-weight:600;font-size:14px;font-family:'Sarabun',Arial,sans-serif;background:linear-gradient(180deg,#44dd77,#00D162);color:#fff;border:1px solid #009940}
    .add-btn:hover{background:linear-gradient(180deg,#55ee88,#11e273)}
    .table-container{background:#fff;overflow-x:auto}
    table{width:100%;border-collapse:collapse;font-size:14px}
    thead{background:linear-gradient(180deg,#555,#333)}
    th{color:#fff;padding:9px 8px;font-weight:600;font-size:13px;white-space:nowrap;position:sticky;top:0;background:#444;z-index:5;border-right:1px solid #555;text-align:center}
    th:last-child{border-right:none}
    td{padding:7px 8px;border-bottom:1px solid #f0f0f0;color:#333;font-size:14px;vertical-align:middle;text-align:center}
    tbody tr:hover{background:#f0f5ff}
    .auth-badge{padding:3px 10px;font-weight:600;font-size:12px;display:inline-block}
    .auth-admin{background:#FF1A1A;color:#fff}
    .auth-user{background:#2749F5;color:#fff}
    .auth-viewer{background:#888;color:#fff}
    .page-chip{display:inline-block;background:#e8f0fe;color:#1a3f7a;font-size:12px;font-weight:700;padding:2px 8px;border:1px solid #a8c3e0}
    .edit-input{width:100%;padding:4px 6px;font-size:13px;border:2px inset #aaa;font-family:'Sarabun',Arial,sans-serif}
    .edit-input:focus{outline:none;border-color:#2358a4}
    .actions{display:flex;gap:5px;flex-wrap:wrap;justify-content:center}
    .actions button{padding:5px 11px;cursor:pointer;font-weight:600;font-size:12px;font-family:'Sarabun',Arial,sans-serif;white-space:nowrap;border:none;color:#fff}
    .edit-btn{background:linear-gradient(180deg,#6090f0,#2749F5);border:1px solid #1a35c7!important}
    .delete-btn{background:linear-gradient(180deg,#ff4444,#FF1A1A);border:1px solid #cc0000!important}
    .save-btn{background:linear-gradient(180deg,#44dd77,#00D162);border:1px solid #009940!important}
    .cancel-btn{background:linear-gradient(180deg,#f0f0f0,#d8d8d8);color:#555!important;border:1px solid #999!important}
    .toast{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:#333;color:#fff;padding:10px 24px;font-size:14px;font-weight:600;z-index:9999;display:none}
    .toast.on{display:block}
    .btn-home{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:7px 18px;
  font-family:'Sarabun',sans-serif;
  font-size:14px;
  font-weight:600;
  color:#fff;
  text-decoration:none;
  cursor:pointer;
  white-space:nowrap;
  border:1px solid #8b0000;
  background:linear-gradient(180deg,#e03030 0%,#b00000 50%,#8b0000 100%);
  box-shadow:inset 0 1px 0 rgba(255,255,255,.3),1px 1px 3px rgba(0,0,0,.3);
}
.btn-home:hover{
  background:linear-gradient(180deg,#ff4444 0%,#cc0000 50%,#a00000 100%);
}
.btn-home:active{
  box-shadow:inset 1px 1px 3px rgba(0,0,0,.4);
  transform:translateY(1px);
}
  </style>
</head>
<body>
<div class="ov" id="ov"><div><div class="sp"></div><p>กำลังโหลดข้อมูล...</p></div></div>
<div class="toast" id="toast"></div>

@php $q = ['create_by' => $authUser['name'] ?? '']; @endphp
<div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
<div class="sidebar" id="sidebar">
  <div class="sb-head"><img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo"><span>3E TRADING</span><button class="sb-close" onclick="closeSB()">&#10005;</button></div>
  <div class="sb-nav">
    <div class="sb-sec">เมนูหลัก</div>
    <a class="sb-item" target="_blank" href="{{ route('inventory.transaction', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รายการสินค้า เข้า-ออก</a>
    <a class="sb-item" target="_blank" href="{{ route('inventory.item', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>ค้นหาสินค้า</a>
    <div style="height:1px;background:#a8c3e0;margin:5px 12px"></div>
    <div class="sb-sec">จัดการระบบ</div>
    <a class="sb-item cur" target="_blank" href="{{ route('inventory.users', $q) }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>จัดการ User</a>
  </div>
</div>

<div class="topbar">
  <button class="hamburger" onclick="openSB()"><span></span><span></span><span></span></button>
  <img src="https://img2.pic.in.th/pic/article_aac164a0b0.png" alt="Logo" class="topbar-logo">
  <span class="topbar-title">3E TRADING</span>
  <div class="topbar-right"><span class="topbar-name">{{ $authUser['name'] ?? '' }}</span><span class="topbar-badge">{{ strtoupper($authRole) }}</span></div>
  <a href="http://server_update:8000/solist" button  type="submit" class="btn-home">🚪 หน้าหลัก</a>
</div>

<div id="content">
  <div class="content-header">
    <h2>จัดการผู้ใช้งาน</h2>
    <button class="add-btn" onclick="addNewUserRow()">+ เพิ่มผู้ใช้ใหม่</button>
  </div>

  <div class="table-container">
    <table id="userTable">
      <thead>
        <tr>
          <th>Username</th>
          <th>Password</th>
          <th>Name</th>
          <th>Auth</th>
          <th>Page</th>
          <th>การจัดการ</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<script>
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
let users=[];

function showOv(){document.getElementById('ov').classList.add('on')}
function hideOv(){document.getElementById('ov').classList.remove('on')}
function openSB(){document.getElementById('sidebar').classList.add('open');document.getElementById('sbOv').classList.add('open')}
function closeSB(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sbOv').classList.remove('open')}
function toast(m,e){const t=document.getElementById('toast');t.textContent=m;t.style.background=e?'#cc0000':'#007722';t.classList.add('on');setTimeout(()=>t.classList.remove('on'),3000)}
function esc(s){return(s||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}

async function loadUsers(){
  showOv();
  try{
    users=await(await fetch('/api/users')).json();
    renderTable();
  }catch(e){console.error(e);alert('เกิดข้อผิดพลาดในการโหลดข้อมูล')}
  hideOv();
}

function authSelect(id,val){
  return`<select id="${id}" style="padding:4px 8px;border:2px inset #aaa;font-family:'Sarabun',Arial,sans-serif;font-size:13px">
    <option value="admin" ${val==='admin'?'selected':''}>Admin</option>
    <option value="user" ${val==='user'||!val?'selected':''}>User</option>
    <option value="viewer" ${val==='viewer'?'selected':''}>Viewer</option>
  </select>`;
}

function renderTable(){
  const tbody=document.querySelector('#userTable tbody');
  tbody.innerHTML='';
  if(!users.length){
    tbody.innerHTML=`<tr><td colspan="7" style="text-align:center;padding:40px;color:#999">ไม่มีข้อมูลผู้ใช้งาน</td></tr>`;
    return;
  }
  users.forEach((user,index)=>{
    const authClass=user.auth==='admin'?'auth-admin':user.auth==='viewer'?'auth-viewer':'auth-user';
    const tr=document.createElement('tr');
    tr.innerHTML=`
      <td><strong>${esc(user.username)}</strong></td>
      <td>${esc(user.password)}</td>
      <td>${esc(user.name)}</td>
      <td><span class="auth-badge ${authClass}">${esc((user.auth||'').toUpperCase())}</span></td>
      <td>${user.page?`<span class="page-chip">${esc(user.page)}</span>`:'-'}</td>
      <td class="actions">
        <button class="edit-btn" onclick="editRow(${index})">แก้ไข</button>
        <button class="delete-btn" onclick="deleteUser(${index})">ลบ</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function editRow(index){
  const user=users[index];
  const tr=document.querySelector('#userTable tbody').children[index];
  if(!tr)return;
  tr.style.background='#e0f2fe';
  tr.innerHTML=`
    <td style="color:#0a7d2c;font-weight:700">${esc(user.id_emp)}</td>
    <td><input class="edit-input" id="e-username-${index}" value="${esc(user.username)}"></td>
    <td><input class="edit-input" id="e-password-${index}" value="${esc(user.password)}"></td>
    <td><input class="edit-input" id="e-name-${index}" value="${esc(user.name)}"></td>
    <td>${authSelect('e-auth-'+index,user.auth)}</td>
    <td><input class="edit-input" id="e-page-${index}" value="${esc(user.page)}" placeholder="เช่น pr"></td>
    <td class="actions">
      <button class="save-btn" onclick="saveEdit(${index})">บันทึก</button>
      <button class="cancel-btn" onclick="renderTable()">ยกเลิก</button>
    </td>
  `;
}

async function saveEdit(index){
  const user=users[index];
  const d={
    username:document.getElementById(`e-username-${index}`).value.trim(),
    password:document.getElementById(`e-password-${index}`).value.trim(),
    name:document.getElementById(`e-name-${index}`).value.trim(),
    auth:document.getElementById(`e-auth-${index}`).value,
    page:document.getElementById(`e-page-${index}`).value.trim(),
  };
  if(!d.username||!d.password||!d.name){alert('กรุณากรอกข้อมูลให้ครบ (Username / Password / Name)');return}
  showOv();
  try{
    const res=await(await fetch('/api/users/'+encodeURIComponent(user.id_emp),{
      method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
      body:JSON.stringify(d)
    })).json();
    if(res.success){toast('อัปเดตข้อมูลเรียบร้อยแล้ว');await loadUsers()}
    else{alert('เกิดข้อผิดพลาด: '+(res.error||'unknown'));hideOv()}
  }catch(e){alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล');hideOv()}
}

async function deleteUser(index){
  const user=users[index];
  if(!confirm(`ลบผู้ใช้ ${user.username}?`))return;
  showOv();
  try{
    const res=await(await fetch('/api/users/'+encodeURIComponent(user.id_emp),{
      method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF}
    })).json();
    if(res.success){toast('ลบข้อมูลสำเร็จ');await loadUsers()}
    else{alert('เกิดข้อผิดพลาด: '+(res.error||'unknown'));hideOv()}
  }catch(e){alert('เกิดข้อผิดพลาดในการลบข้อมูล');hideOv()}
}

function addNewUserRow(){
  if(document.getElementById('new-username')){alert('มีแถวเพิ่มผู้ใช้อยู่แล้ว');return}
  const tbody=document.querySelector('#userTable tbody');
  const tr=document.createElement('tr');
  tr.style.background='#fffacd';
  tr.innerHTML=`
    <td><em style="color:#888;font-size:12px">auto</em></td>
    <td><input class="edit-input" id="new-username" placeholder="Username"></td>
    <td><input class="edit-input" id="new-password" placeholder="Password"></td>
    <td><input class="edit-input" id="new-name" placeholder="ชื่อ"></td>
    <td>${authSelect('new-auth','user')}</td>
    <td><input class="edit-input" id="new-page" placeholder="เช่น pr (เว้นว่างได้)"></td>
    <td class="actions">
      <button class="save-btn" onclick="saveNewUser()">บันทึก</button>
      <button class="cancel-btn" onclick="renderTable()">ยกเลิก</button>
    </td>
  `;
  tbody.prepend(tr);
}

async function saveNewUser(){
  const d={
    username:document.getElementById('new-username').value.trim(),
    password:document.getElementById('new-password').value.trim(),
    name:document.getElementById('new-name').value.trim(),
    auth:document.getElementById('new-auth').value,
    page:document.getElementById('new-page').value.trim(),   // ← เข้า DB local เท่านั้น
  };
  if(!d.username||!d.password||!d.name){alert('กรุณากรอกข้อมูลให้ครบ (Username / Password / Name)');return}
  showOv();
  try{
    const res=await(await fetch('/api/users',{
      method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
      body:JSON.stringify(d)
    })).json();
    if(res.success){toast('เพิ่มผู้ใช้ใหม่เรียบร้อย');await loadUsers()}
    else{alert('เกิดข้อผิดพลาด: '+(res.error||'unknown'));hideOv()}
  }catch(e){alert('เกิดข้อผิดพลาดในการเพิ่มผู้ใช้');hideOv()}
}

loadUsers();
</script>
</body>
</html>