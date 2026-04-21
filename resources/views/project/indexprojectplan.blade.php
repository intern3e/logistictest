<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SolarSystem</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Sarabun','Noto Sans Thai',sans-serif;background:#f0f2f7;color:#1a1a1a;font-size:14px;min-height:100vh}

/* ── Top header bar ── */
.topbar{background:
#102273
;display:flex;align-items:center;height:52px;padding:0;gap:0;position:sticky;top:0;z-index:100;box-shadow:0 2px 8px rgba(9,9,181,.25)}
.topbar-brand{display:flex;align-items:center;gap:9px;padding:0 20px;border-right:1px solid rgba(255,255,255,.15);height:100%;flex-shrink:0}
.topbar-brand svg{opacity:.9}
.topbar-brand h1{color:#fff;font-size:15px;font-weight:700;letter-spacing:.4px;white-space:nowrap}
nav{display:flex;align-items:center;flex:1;height:100%;padding:0 10px;overflow-x:auto;scrollbar-width:none}
nav::-webkit-scrollbar{display:none}
.nb{display:flex;align-items:center;gap:7px;padding:0 16px;height:100%;font-size:13px;border:none;background:none;cursor:pointer;color:rgba(255,255,255,.62);white-space:nowrap;transition:all .18s;font-family:inherit;position:relative;border-bottom:3px solid transparent;font-weight:500}
.nb svg{width:15px;height:15px;opacity:.7;transition:opacity .18s;flex-shrink:0}
.nb:hover{color:#fff;background:rgba(255,255,255,.1)}
.nb:hover svg{opacity:1}
.nb.on{color:#fff;border-bottom-color:#7dd3fc;background:rgba(255,255,255,.12)}
.nb.on svg{opacity:1}
.topbar-date{padding:0 18px;font-size:11px;color:rgba(255,255,255,.5);white-space:nowrap;margin-left:auto;flex-shrink:0}

/* ── Content ── */
.main{padding:24px;max-width:1280px;margin:0 auto}

/* old sidebar/app-shell ignored */
.app-shell{display:contents}
.sidebar{display:none}
.content-wrap{display:contents}
.topbar-label{display:none}
.header{display:none}
.panel{display:none}.panel.on{display:block}
.metrics{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:20px}
.mc{background:#fff;border-radius:8px;padding:14px 16px;border:1px solid #e8e8e5}
.ml{font-size:11px;color:#888;margin-bottom:4px}
.mv{font-size:26px;font-weight:500}
.ms{font-size:11px;color:#aaa;margin-top:2px}
.card{background:#fff;border-radius:8px;border:1px solid #e5e5e5;overflow:hidden;margin-bottom:16px}
.ct{padding:12px 16px;font-size:13px;font-weight:500;color:#444;border-bottom:1px solid #e0e0dd;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px}
.ov{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
th{padding:8px 12px;text-align:left;background:
#0f2fbd;color:#ffffff;font-weight:500;border-bottom:1px solid #e8e8e5;white-space:nowrap}
td{padding:8px 12px;border-bottom:1px solid #f0f0ee;color:#1a1a1a;vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:#fafaf8}
.badge{display:inline-block;padding:3px 10px;border-radius:6px;font-size:12px;font-weight:600;letter-spacing:.3px}
.b-ok{background:#1D4ED8;color:#fff}
.b-warn{background:#fdcb27;color:#7a4a00}
.b-due{background:#f52222;color:#fff}
.b-info{background:#1D4ED8;color:#fff}
.b-gray{background:#f52222;color:#fff}
.b-purple{background:#6c10ff;color:#fff}
.b-pink{background:#BE185D;color:#fff}
.b-navy{background:#0f2d6e;color:#e8f0ff}
.b-wash-ok{background:#00e44c;color:#fff}
.btn-sm{padding:3px 10px;font-size:11px;border:1px solid #e0e0dd;border-radius:6px;cursor:pointer;background:#fff;color:#555;transition:all .15s}
.btn-sm:hover{background:#f0f0ee}
.btn-add{padding:6px 14px;font-size:12px;border:none;border-radius:6px;cursor:pointer;background:#0909b5;color:#fff;font-weight:500;transition:background .15s}
.btn-add:hover{background:#0416ba}
.btn-edit{padding:2px 8px;font-size:11px;border:1px solid #0909b5;border-radius:5px;cursor:pointer;background:#fff;color:#0909b5}
.btn-edit:hover{background:#eef}
.btn-del{padding:2px 8px;font-size:11px;border:1px solid #f52222;border-radius:5px;cursor:pointer;background:#fff;color:#f52222}
.btn-del:hover{background:#fff0f0}
.btn-save{padding:6px 16px;font-size:12px;border:none;border-radius:6px;cursor:pointer;background:#0909b5;color:#fff;font-weight:500}
.btn-cancel{padding:6px 14px;font-size:12px;border:1px solid #ccc;border-radius:6px;cursor:pointer;background:#fff;color:#555}
input[type=text],input[type=date],select{padding:6px 10px;border:1px solid #d0d0cc;border-radius:6px;font-size:12px;outline:none;font-family:inherit;transition:border .2s;width:100%}
input[type=text]:focus,input[type=date]:focus,select:focus{border-color:#0909b5}
.search-inp{padding:5px 10px;border:1px solid #e0e0dd;border-radius:8px;font-size:12px;outline:none;width:220px;font-family:inherit}
.search-inp:focus{border-color:#0909b5}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:16px}
@media(max-width:700px){.two-col{grid-template-columns:1fr}}
.gr{display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:12px}
.gl{width:260px;flex-shrink:0;color:#555;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:11px}
.gt{flex:1;height:16px;background:#f0f0ee;border-radius:4px;position:relative;min-width:80px}
.gb{position:absolute;height:100%;border-radius:4px;display:flex;align-items:center;padding-left:4px}
.gd{font-size:9px;color:rgba(255,255,255,.9);white-space:nowrap}
.gn{width:28px;font-size:11px;color:#888;text-align:right;flex-shrink:0}
.legend{display:flex;gap:14px;flex-wrap:wrap;margin-top:12px}
.le{font-size:11px;color:#666;display:flex;align-items:center;gap:4px}
.lc{width:10px;height:10px;border-radius:2px;display:inline-block}
.mono{font-family:monospace;font-size:12px}
.tag{display:inline-block;padding:1px 7px;border-radius:4px;font-size:11px;background:#f0f0ee;color:#555;margin:1px}

/* Modal */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center}
.modal-overlay.open{display:flex}
.modal{background:#fff;border-radius:12px;padding:24px;width:580px;max-width:95vw;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.modal-title{font-size:16px;font-weight:600;margin-bottom:20px;color:#1a1a1a;border-bottom:2px solid #0909b5;padding-bottom:10px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.form-grid.one{grid-template-columns:1fr}
.fg{display:flex;flex-direction:column;gap:4px}
.fg label{font-size:11px;color:#666;font-weight:500}
.modal-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:20px;padding-top:16px;border-top:1px solid #f0f0ee}
.confirm-overlay{display:none;position:fixed;inset:0;background:rgba(0, 0, 0, 0.5);z-index:2000;align-items:center;justify-content:center}
.confirm-overlay.open{display:flex}
.confirm-box{background:#fff;border-radius:10px;padding:24px;width:340px;text-align:center;box-shadow:0 10px 40px rgba(0,0,0,.2)}
.confirm-box h3{font-size:15px;margin-bottom:8px}
.confirm-box p{font-size:13px;color:#666;margin-bottom:20px}
.confirm-actions{display:flex;gap:8px;justify-content:center}
.btn-danger{padding:7px 20px;border:none;border-radius:6px;background:#f52222;color:#fff;cursor:pointer;font-weight:500}
</style>
</head>
<body>

<div class="topbar">
  <div class="topbar-brand">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
    <h1>SolarSystem</h1>
  </div>
  <nav id="nav"></nav>
  <span class="topbar-date" id="today-label"></span>
</div>
<div class="main" id="main"></div>

<!-- Modal: Wash -->
<div class="modal-overlay" id="modal-wash">
  <div class="modal">
    <div class="modal-title" id="wash-modal-title">เพิ่มข้อมูลการล้างแผง</div>
    <div class="form-grid">
      <div class="fg"><label>ชื่อสถานที่ *</label><input type="text" id="w-name" placeholder="ชื่อสถานที่"></div>
      <div class="fg"><label>ขนาด</label><input type="text" id="w-size" placeholder="เช่น 10kW"></div>
      <div class="fg"><label>ล้างครั้งที่ 1</label><input type="date" id="w-wash1"></div>
      <div class="fg"><label>ล้างครั้งที่ 2</label><input type="date" id="w-wash2"></div>
      <div class="fg"><label>ติดต่อ</label><input type="text" id="w-contact" placeholder="ชื่อ เบอร์โทร"></div>
      <div class="fg"><label>วันที่ติดตั้ง</label><input type="date" id="w-date"></div>
      <div class="fg form-grid one" style="grid-column:1/-1"><label>Location</label><input type="text" id="w-loc" placeholder="lat, lng"></div>
    </div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal('modal-wash')">ยกเลิก</button>
      <button class="btn-save" onclick="saveWash()">บันทึก</button>
    </div>
  </div>
</div>

<!-- Modal: Account -->
<div class="modal-overlay" id="modal-acc">
  <div class="modal">
    <div class="modal-title" id="acc-modal-title">เพิ่มบัญชีผู้ใช้</div>
    <div class="form-grid">
      <div class="fg"><label>ลูกค้า *</label><input type="text" id="a-customer" placeholder="ชื่อลูกค้า"></div>
      <div class="fg"><label>Plane</label><input type="text" id="a-plane" placeholder="Plane name"></div>
      <div class="fg"><label>Username</label><input type="text" id="a-username" placeholder="username"></div>
      <div class="fg"><label>Password</label><input type="text" id="a-password" placeholder="password"></div>
      <div class="fg" style="grid-column:1/-1"><label>Email</label><input type="text" id="a-email" placeholder="email"></div>
      <div class="fg" style="grid-column:1/-1"><label>Inverter</label><input type="text" id="a-inverter" placeholder="Inverter type"></div>
    </div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal('modal-acc')">ยกเลิก</button>
      <button class="btn-save" onclick="saveAcc()">บันทึก</button>
    </div>
  </div>
</div>

<!-- Modal: Schedule -->
<div class="modal-overlay" id="modal-sched">
  <div class="modal">
    <div class="modal-title" id="sched-modal-title">เพิ่มตารางงาน</div>
    <div class="form-grid">
      <div class="fg"><label>ชื่อสถานที่ *</label><input type="text" id="s-name" placeholder="ชื่อสถานที่"></div>
      <div class="fg"><label>ผู้ติดต่อ</label><input type="text" id="s-contact" placeholder="ชื่อผู้ติดต่อ"></div>
      <div class="fg"><label>เบอร์โทร</label><input type="text" id="s-tel" placeholder="0xx-xxx-xxxx"></div>
      <div class="fg"><label>ขนาด</label><input type="text" id="s-size" placeholder="เช่น 10kW"></div>
      <div class="fg"><label>วันติดตั้ง</label><input type="text" id="s-date" placeholder="01/01/67"></div>
      <div class="fg"><label>เวลา</label><input type="text" id="s-time" placeholder="09:00"></div>
      <div class="fg"><label>ทีมงาน</label><input type="text" id="s-team" placeholder="ชื่อทีม"></div>
      <div class="fg"><label>ผู้รับผิดชอบ</label><input type="text" id="s-resp" placeholder="ชื่อ"></div>
      <div class="fg"><label>สถานะ</label>
        <select id="s-status">
          <option value="success">success</option>
          <option value="pending">pending</option>
          <option value="cancel">cancel</option>
        </select>
      </div>
      <div class="fg"><label>กำหนดล้าง</label><input type="text" id="s-nextwash" placeholder="เช่น 10/67"></div>
    </div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal('modal-sched')">ยกเลิก</button>
      <button class="btn-save" onclick="saveSched()">บันทึก</button>
    </div>
  </div>
</div>

<!-- Confirm Delete -->
<div class="confirm-overlay" id="confirm-del">
  <div class="confirm-box">
    <h3>ยืนยันการลบ</h3>
    <p>คุณต้องการลบรายการนี้ใช่หรือไม่?<br>การกระทำนี้ไม่สามารถยกเลิกได้</p>
    <div class="confirm-actions">
      <button class="btn-cancel" onclick="closeConfirm()">ยกเลิก</button>
      <button class="btn-danger" onclick="confirmDelete()">ลบ</button>
    </div>
  </div>
</div>

<script>
// =====================================================================
// DATA
// =====================================================================
const TODAY = new Date('2026-04-21');

let INSTALLS = [
  {id:1, date:'2024-07-15', name:'สหกรณ์โคนม ศูนย์ไพจิตร',    size:'100kW', loc:'', contact:'', wash1:'2024-01-19', wash2:'2025-01-19'},
  {id:2, date:'2024-07-15', name:'สยามขแมร์ค้าส่ง',             size:'250kW', loc:'', contact:'', wash1:'2024-01-20', wash2:'2025-01-20'},
  {id:3, date:'2024-07-15', name:'สหกรณ์โคนม คลองหินปูน',     size:'100kW', loc:'', contact:'', wash1:'2024-01-21', wash2:'2025-01-21'},
  {id:4, date:'2024-07-15', name:'สหกรณ์โคนม วังใหม่',         size:'200kW', loc:'', contact:'', wash1:'2024-01-22', wash2:'2025-01-22'},
  {id:5, date:'2023-07-14', name:'บริษัท ชนาธิป',               size:'10kW',  loc:'13.622328, 100.628112', contact:'คุณเบิ้ม 087-5581732',           wash1:'2024-10-14', wash2:''},
  {id:6, date:'2023-08-21', name:'U.P. Resin and Chemical',     size:'20kW',  loc:'13.699806, 100.532472', contact:'คุณวรมิตร 089-2008656',           wash1:'2023-03-01', wash2:'2024-12-11'},
  {id:7, date:'2023-09-11', name:'บจก. บ้านแสนสุขเฮลท์แคร์',  size:'30kW',  loc:'13.827759, 100.667131', contact:'คุณหนิง/คุณหยก 081-9319830',     wash1:'2023-05-01', wash2:'2025-04-24'},
  {id:8, date:'2023-10-05', name:'บ้านคุณโบว์',                 size:'5kW',   loc:'13.775589, 100.591885', contact:'คุณโบ 086-9895243',               wash1:'2024-12-01', wash2:''},
  {id:9, date:'2024-01-20', name:'บ้านคุณนที',                  size:'15kW',  loc:'13.991710, 100.672625', contact:'คุณนที 080-0724507',              wash1:'2025-09-27', wash2:''},
  {id:10,date:'2024-02-01', name:'ร้านอาหาร Twinhomekitchen',  size:'30kW',  loc:'13.594567, 100.617764', contact:'คุณตุ๊ก 083-4495960',            wash1:'2025-02-07', wash2:''},
  {id:11,date:'2024-02-06', name:'บจก. S.K. Precision',         size:'100kW', loc:'13.434796, 101.118759', contact:'คุณพรรณชนก 080-5097365',         wash1:'2025-02-02', wash2:''},
  {id:12,date:'2024-03-09', name:'บ้านคุณอาร์ม',               size:'26kW',  loc:'13.724750, 100.649135', contact:'คุณอาร์ม 081-9362626',           wash1:'2025-02-03', wash2:''},
  {id:13,date:'2024-04-06', name:'บ้านคุณสมพล บางแสน',        size:'10kW',  loc:'13.300587, 100.932582', contact:'คุณอั๋น 086-3551490',            wash1:'2025-04-25', wash2:''},
  {id:14,date:'2024-05-02', name:'บ้านกำพล Green80',            size:'20kW',  loc:'13.663098, 100.518828', contact:'Passachai 089-9213410',          wash1:'2025-04-24', wash2:''},
  {id:15,date:'2024-05-27', name:'บ้านคุณเวย์',                 size:'5kW',   loc:'13.990748, 100.640033', contact:'คุณเวย์ 081-9226442',            wash1:'2025-04-25', wash2:''},
  {id:16,date:'2024-06-09', name:'บ้านคุณคณิน',                size:'5kW',   loc:'13.692618, 100.502598', contact:'คุณอั๋น 085-2442235',            wash1:'2025-09-27', wash2:''},
  {id:17,date:'2024-06-24', name:'บ้านคุณชาญชัย',              size:'5kW',   loc:'14.033528, 100.670666', contact:'ชาญชัย 062-8982456',             wash1:'', wash2:''},
  {id:18,date:'2024-08-01', name:'บจก. บียอนด์ ดีไซน์',        size:'60kW',  loc:'', contact:'ผึ้ง 093-6359236',                                    wash1:'2026-03-24', wash2:''},
  {id:19,date:'2024-04-01', name:'โรงแรม Seamira House',        size:'100kW', loc:'', contact:'',                                                      wash1:'', wash2:''},
];

let ACCOUNTS = [
  {id:1,  plane:'Bannsansuk',           username:'sansuk11',             password:'Sansuk@11',        email:'baansansukhealthcare11@gmail.com', customer:'คลินิคบ้านแสนสุข',   inverter:'Changemead'},
  {id:2,  plane:'vorramit',             username:'Vorramit',             password:'ลูกค้าตั้งเอง',   email:'ลูกค้าตั้งเอง',                  customer:'วรมิตร',             inverter:'Changemead'},
  {id:3,  plane:'BOBO',                 username:'BOBO-Solar',           password:'Bobosolar243',     email:'user003solar@gmail.com',          customer:'คุณโบ',               inverter:'Changemead'},
  {id:4,  plane:'Nattee',               username:'Natee_kevala',         password:'Natee@solar1',     email:'usernatee123@gmail.com',          customer:'คุณนที',              inverter:'Changemead1'},
  {id:5,  plane:'Twin house kitchen',   username:'Twinhousekitchen',     password:'Twinhouse@1',      email:'twinhousekitchen@gmail.com',      customer:'Twinhousekitchen',    inverter:'Changemead1'},
  {id:6,  plane:'SK Precision',         username:'Skprecision',          password:'Sk@solar1',        email:'skprecisionsolar@gmail.com',      customer:'SK Precision',        inverter:'Changemead1'},
  {id:7,  plane:'สยามขแมร์',            username:'siamkamair10',         password:'Siamkamair@10',    email:'userrabbitsolar10@gmail.com',     customer:'สยามขแมร์',          inverter:'Changemead1'},
  {id:8,  plane:'คุณภัสชัย',            username:'Green80',              password:'Green@80',         email:'userrabbitsolar20@gmail.com',     customer:'กำพล Green80',        inverter:'Changemead1'},
  {id:9,  plane:'พิเชฐ สุวิยานนท์',    username:'Way_solar',            password:'W020_rbsolar',     email:'user20solar@gmail.com',           customer:'คุณเวย์',             inverter:'Changemead1'},
  {id:10, plane:'Beyond design',        username:'Beyond.solar',         password:'User30@rbsolar',   email:'user30solarrb@gmail.com',         customer:'Beyond',              inverter:'Changemead1'},
  {id:11, plane:'tumtook',              username:'tumtook',              password:'',                 email:'user30solar@gmail.com',           customer:'tumtook',             inverter:'Changemead1'},
  {id:12, plane:'คุณอาร์ม',             username:'Arm.solar',            password:'Arm_rbsolar42',    email:'user42.rbsolar@gmail.com',        customer:'คุณอาร์ม',            inverter:'Changemead1'},
  {id:13, plane:'-',                    username:'-',                    password:'-',                email:'user43.rbsolar@gmail.com',        customer:'-',                   inverter:'-'},
  {id:14, plane:'สมพล',                 username:'User100.rabbitsolar',  password:'User@100',         email:'User100.rabbitsolar@gmail.com',   customer:'สมพล',               inverter:'solar man'},
  {id:15, plane:'Deyee cloud',          username:'usersolar04',          password:'User04@rbsolar',   email:'usersolar04@gmail.com',           customer:'Deyee cloud',         inverter:'-'},
  {id:16, plane:'Seamira house',        username:'Seamirahouse',         password:'Seamira@solar3e',  email:'Houseseamira@gmail.com',          customer:'Seamira house',       inverter:'Changemead1'},
];

let SCHEDULE = [
  {id:1, name:'หมู่บ้านเกตสินี ซ.20 (คุณโบ)', contact:'คุณโบ',     tel:'086-989-5243', size:'5kW',  date:'03/10/66', time:'10:00', team:'พี่เดช',      resp:'มิน, เมฆ', status:'success', nextWash:'10/67'},
  {id:2, name:'บ้านคุณนที',                   contact:'คุณนที',     tel:'080-072-4507', size:'15kW', date:'17/01/67', time:'10:00', team:'พี่เดช',      resp:'มิน, เมฆ', status:'success', nextWash:'01/68'},
  {id:3, name:'บ้านแสนสุข',                   contact:'คุณหนิง',    tel:'081-931-9830', size:'30kW', date:'11/09/66', time:'-',     team:'พี่มณีรัตน์', resp:'มิน, เมฆ', status:'success', nextWash:'09/67'},
  {id:4, name:'บริษัท วรมิตร',               contact:'คุณวรมิตร',  tel:'089-200-8656', size:'20kW', date:'21/08/66', time:'-',     team:'พี่เดช',      resp:'มิน, เมฆ', status:'success', nextWash:'07/67'},
  {id:5, name:'บริษัท ชนาธิป',               contact:'คุณเบิ้ม',   tel:'087-558-1732', size:'10kW', date:'14/07/66', time:'-',     team:'พี่มณีรัตน์', resp:'มิน, เมฆ', status:'success', nextWash:'-'},
];

const PERMIT_DOCS = [
  {id:1, name:'เอกสารคำร้องขอ ข.1 หรือ ข.2 และอื่นๆ', status:'มี'},
  {id:2, name:'สำเนาหนังสือรับรองการจดทะเบียนนิติบุคคล ไม่เกิน 6 เดือน 1 ชุด', status:'มี'},
  {id:3, name:'สำเนาหรือภาพถ่ายโฉนดที่ดินเท่าต้นฉบับ พร้อมลงนาม', status:'มี'},
  {id:4, name:'หนังสือยินยอมของเจ้าของที่ดิน (กรณีไม่ใช่เจ้าของที่ดิน) 1 ชุด', status:'ไม่ใช้'},
  {id:5, name:'สำเนาบัตรประชาชนและทะเบียนบ้านของผู้ขออนุญาต ผู้รับมอบอำนาจ และผู้มีอำนาจลงนามแทนนิติบุคคล พร้อมลงนาม 1 ชุด', status:'ยังไม่ครบ'},
  {id:6, name:'หนังสือแสดงความยินยอมและรับรองของสถาปนิก วิศวกรผู้ออกแบบ พร้อมสำเนาใบอนุญาตผู้ประกอบวิชาชีพ 1 ชุด', status:'มี'},
  {id:7, name:'แผนผังบริเวณ แบบแปลน และรายการประกอบแบบแปลน 5 ชุด', status:'มี'},
];

const NOTIFY_DOCS = [
  {id:1, name:'หนังสือแจ้งการติดตั้งแผงเซลล์แสงอาทิตย์', qty:'1 ฉบับ'},
  {id:2, name:'หนังสือแบบไฟฟ้า พร้อมลายเซ็นต์จากวิศวกรไฟฟ้า', qty:'1 ชุด'},
  {id:3, name:'หนังสือแสดงความยินยอมและรับรองของสถาปนิก/วิศวกร พร้อมสำเนาใบอนุญาต', qty:'1 ชุด'},
  {id:4, name:'สำเนาหนังสือรับรองการจดทะเบียนนิติบุคคล ไม่เกิน 6 เดือน', qty:'1 ชุด'},
  {id:5, name:'หนังสือมอบอำนาจการขอเชื่อมต่อกับระบบโครงข่ายไฟฟ้า กฟน.', qty:'1 ฉบับ'},
  {id:6, name:'สำเนาบัตรประชาชนและทะเบียนบ้านของผู้ขออนุญาต และผู้มีอำนาจลงนาม', qty:'1 ชุด'},
];

const WORK_STEPS = [
  {id:1,  name:'วางแผนและออกแบบ (แบบไฟฟ้า, หม้อแปลง, โยธา, เอกสาร) ส่งลูกค้าอนุมัติ', start:1,  dur:14, cat:'design'},
  {id:2,  name:'ยื่นแบบโยธา อ.1',                                                         start:10, dur:20, cat:'permit'},
  {id:3,  name:'จัดซื้อวัสดุอุปกรณ์ทั้งหมด',                                              start:10, dur:15, cat:'procure'},
  {id:4,  name:'Install Mounting, Walkway',                                                 start:25, dur:5,  cat:'install'},
  {id:5,  name:'Install Wireway, Cable tray',                                               start:25, dur:5,  cat:'install'},
  {id:6,  name:'สร้างห้อง Inverter',                                                       start:25, dur:7,  cat:'design'},
  {id:7,  name:'Install Inverter, Optimizer',                                               start:30, dur:5,  cat:'install'},
  {id:8,  name:'Install PV Module',                                                         start:30, dur:5,  cat:'install'},
  {id:9,  name:'Wiring DC',                                                                 start:35, dur:5,  cat:'wire'},
  {id:10, name:'Wiring AC',                                                                 start:38, dur:5,  cat:'wire'},
  {id:11, name:'ดับไฟ ขนานไฟฟ้าเข้ากับระบบของโรงงาน (ยังไม่ออนไลน์)',                   start:43, dur:3,  cat:'power'},
  {id:12, name:'ขนานไฟแรงสูง DTVT',                                                       start:43, dur:5,  cat:'power'},
  {id:13, name:'ตรวจสอบระบบโดยวิศวกรก่อนเปิดใช้งาน',                                    start:48, dur:3,  cat:'verify'},
  {id:14, name:'Commissioning test, Install plant',                                          start:50, dur:5,  cat:'verify'},
  {id:15, name:'ยื่นขนานไฟฟ้า กกพ.',                                                       start:55, dur:6,  cat:'verify'},
];

// =====================================================================
// STATE
// =====================================================================
const CAT_COLORS = {design:'#0d86ff',permit:'#05ce20',procure:'#f89306',install:'#01daa3',wire:'#3401ff',power:'#d41e06',verify:'#74d300'};
const CAT_LABELS = {design:'ออกแบบ/เอกสาร',permit:'ยื่นเอกสาร',procure:'จัดซื้อ',install:'ติดตั้ง',wire:'Wiring',power:'ขนานไฟ',verify:'ตรวจสอบ/ยื่น'};

let tab = 'dash';
let searchQ = '';
let editingId = null; // which record is being edited
let editingType = null; // 'wash' | 'acc' | 'sched'
let deletingId = null;
let deletingType = null;
let nextId = {wash: 20, acc: 17, sched: 6};

// =====================================================================
// UTILS
// =====================================================================
function parseD(s){ if(!s) return null; const d=new Date(s); return isNaN(d)?null:d; }
function monthsSince(d){ return d ? (TODAY-d)/(1000*60*60*24*30.44) : null; }
function lastWash(inst){ return parseD(inst.wash2)||parseD(inst.wash1); }

function washBadge(inst){
  const lw=lastWash(inst);
  if(!lw) return '<span class="badge b-gray">ยังไม่เคยล้าง</span>';
  const m=monthsSince(lw);
  if(m>12) return `<span class="badge b-due">เกินกำหนด ${Math.round(m)} เดือน</span>`;
  if(m>6)  return `<span class="badge b-warn">ควรนัดล้าง ${Math.round(m)} เดือน</span>`;
  return `<span class="badge b-wash-ok">ปกติ ${Math.round(m)} เดือน</span>`;
}

function fmtD(s){
  const d=parseD(s); if(!d) return '-';
  return d.toLocaleDateString('th-TH',{day:'numeric',month:'short',year:'2-digit'});
}

function docBadge(s){
  if(s==='มี')         return '<span class="badge b-ok">มี</span>';
  if(s==='ยังไม่ครบ') return '<span class="badge b-warn">ยังไม่ครบ</span>';
  if(s==='ไม่ใช้')    return '<span class="badge b-gray">ไม่ใช้</span>';
  return `<span class="badge b-info">${s}</span>`;
}

function kwNum(s){ const m=(s||'').match(/(\d+)/); return m?parseInt(m[1]):0; }

function setTab(id){ tab=id; searchQ=''; render(); }

// =====================================================================
// MODAL HELPERS
// =====================================================================
function openModal(id){ document.getElementById(id).classList.add('open'); }
function closeModal(id){ document.getElementById(id).classList.remove('open'); editingId=null; editingType=null; }
function closeConfirm(){ document.getElementById('confirm-del').classList.remove('open'); deletingId=null; deletingType=null; }

function askDelete(type, id){
  deletingType=type; deletingId=id;
  document.getElementById('confirm-del').classList.add('open');
}

function confirmDelete(){
  if(deletingType==='wash') INSTALLS = INSTALLS.filter(x=>x.id!==deletingId);
  if(deletingType==='acc')  ACCOUNTS = ACCOUNTS.filter(x=>x.id!==deletingId);
  if(deletingType==='sched') SCHEDULE = SCHEDULE.filter(x=>x.id!==deletingId);
  closeConfirm();
  renderMain();
}

// =====================================================================
// WASH CRUD
// =====================================================================
function openAddWash(){
  editingId=null; editingType='wash';
  document.getElementById('wash-modal-title').textContent='เพิ่มข้อมูลการล้างแผง';
  ['w-name','w-size','w-wash1','w-wash2','w-contact','w-date','w-loc'].forEach(id=>document.getElementById(id).value='');
  openModal('modal-wash');
}

function openEditWash(id){
  const r=INSTALLS.find(x=>x.id===id); if(!r) return;
  editingId=id; editingType='wash';
  document.getElementById('wash-modal-title').textContent='แก้ไขข้อมูลการล้างแผง';
  document.getElementById('w-name').value=r.name||'';
  document.getElementById('w-size').value=r.size||'';
  document.getElementById('w-wash1').value=r.wash1||'';
  document.getElementById('w-wash2').value=r.wash2||'';
  document.getElementById('w-contact').value=r.contact||'';
  document.getElementById('w-date').value=r.date||'';
  document.getElementById('w-loc').value=r.loc||'';
  openModal('modal-wash');
}

function saveWash(){
  const name=document.getElementById('w-name').value.trim();
  if(!name){alert('กรุณากรอกชื่อสถานที่');return;}
  const obj={
    name, size:document.getElementById('w-size').value.trim(),
    wash1:document.getElementById('w-wash1').value, wash2:document.getElementById('w-wash2').value,
    contact:document.getElementById('w-contact').value.trim(),
    date:document.getElementById('w-date').value, loc:document.getElementById('w-loc').value.trim()
  };
  if(editingId){
    const idx=INSTALLS.findIndex(x=>x.id===editingId);
    if(idx>-1) INSTALLS[idx]={...INSTALLS[idx],...obj};
  } else {
    obj.id=nextId.wash++;
    INSTALLS.push(obj);
  }
  closeModal('modal-wash');
  renderMain();
}

// =====================================================================
// ACCOUNT CRUD
// =====================================================================
function openAddAcc(){
  editingId=null; editingType='acc';
  document.getElementById('acc-modal-title').textContent='เพิ่มบัญชีผู้ใช้';
  ['a-customer','a-plane','a-username','a-password','a-email','a-inverter'].forEach(id=>document.getElementById(id).value='');
  openModal('modal-acc');
}

function openEditAcc(id){
  const r=ACCOUNTS.find(x=>x.id===id); if(!r) return;
  editingId=id; editingType='acc';
  document.getElementById('acc-modal-title').textContent='แก้ไขบัญชีผู้ใช้';
  document.getElementById('a-customer').value=r.customer||'';
  document.getElementById('a-plane').value=r.plane||'';
  document.getElementById('a-username').value=r.username||'';
  document.getElementById('a-password').value=r.password||'';
  document.getElementById('a-email').value=r.email||'';
  document.getElementById('a-inverter').value=r.inverter||'';
  openModal('modal-acc');
}

function saveAcc(){
  const customer=document.getElementById('a-customer').value.trim();
  if(!customer){alert('กรุณากรอกชื่อลูกค้า');return;}
  const obj={
    customer, plane:document.getElementById('a-plane').value.trim(),
    username:document.getElementById('a-username').value.trim(),
    password:document.getElementById('a-password').value.trim(),
    email:document.getElementById('a-email').value.trim(),
    inverter:document.getElementById('a-inverter').value.trim()
  };
  if(editingId){
    const idx=ACCOUNTS.findIndex(x=>x.id===editingId);
    if(idx>-1) ACCOUNTS[idx]={...ACCOUNTS[idx],...obj};
  } else {
    obj.id=nextId.acc++;
    ACCOUNTS.push(obj);
  }
  closeModal('modal-acc');
  renderMain();
}

// =====================================================================
// SCHEDULE CRUD
// =====================================================================
function openAddSched(){
  editingId=null; editingType='sched';
  document.getElementById('sched-modal-title').textContent='เพิ่มตารางงาน';
  ['s-name','s-contact','s-tel','s-size','s-date','s-time','s-team','s-resp','s-nextwash'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('s-status').value='success';
  openModal('modal-sched');
}

function openEditSched(id){
  const r=SCHEDULE.find(x=>x.id===id); if(!r) return;
  editingId=id; editingType='sched';
  document.getElementById('sched-modal-title').textContent='แก้ไขตารางงาน';
  document.getElementById('s-name').value=r.name||'';
  document.getElementById('s-contact').value=r.contact||'';
  document.getElementById('s-tel').value=r.tel||'';
  document.getElementById('s-size').value=r.size||'';
  document.getElementById('s-date').value=r.date||'';
  document.getElementById('s-time').value=r.time||'';
  document.getElementById('s-team').value=r.team||'';
  document.getElementById('s-resp').value=r.resp||'';
  document.getElementById('s-status').value=r.status||'success';
  document.getElementById('s-nextwash').value=r.nextWash||'';
  openModal('modal-sched');
}

function saveSched(){
  const name=document.getElementById('s-name').value.trim();
  if(!name){alert('กรุณากรอกชื่อสถานที่');return;}
  const obj={
    name, contact:document.getElementById('s-contact').value.trim(),
    tel:document.getElementById('s-tel').value.trim(),
    size:document.getElementById('s-size').value.trim(),
    date:document.getElementById('s-date').value.trim(),
    time:document.getElementById('s-time').value.trim(),
    team:document.getElementById('s-team').value.trim(),
    resp:document.getElementById('s-resp').value.trim(),
    status:document.getElementById('s-status').value,
    nextWash:document.getElementById('s-nextwash').value.trim()
  };
  if(editingId){
    const idx=SCHEDULE.findIndex(x=>x.id===editingId);
    if(idx>-1) SCHEDULE[idx]={...SCHEDULE[idx],...obj};
  } else {
    obj.id=nextId.sched++;
    SCHEDULE.push(obj);
  }
  closeModal('modal-sched');
  renderMain();
}

// =====================================================================
// PASSWORD TOGGLE
// =====================================================================
function togglePwd(i){
  const el=document.getElementById('pw'+i);
  const bt=document.getElementById('pb'+i);
  if(el.dataset.v==='1'){ el.textContent='••••••••'; el.dataset.v='0'; bt.textContent='แสดง'; }
  else { el.textContent=el.dataset.p; el.dataset.v='1'; bt.textContent='ซ่อน'; }
}

// =====================================================================
// RENDER: DASHBOARD
// =====================================================================
let _dashCharts=[];
function renderDash(){
  const total=INSTALLS.length;
  const kwSum=INSTALLS.reduce((a,b)=>a+kwNum(b.size),0);
  const overdue=INSTALLS.filter(i=>{ const lw=lastWash(i); return lw&&monthsSince(lw)>12; }).length;
  const noWash=INSTALLS.filter(i=>!lastWash(i)).length;
  const shouldWash=INSTALLS.filter(i=>{ const lw=lastWash(i); const m=monthsSince(lw); return lw&&m>6&&m<=12; }).length;
  const ok=INSTALLS.filter(i=>{ const lw=lastWash(i); const m=monthsSince(lw); return lw&&m<=6; }).length;

  const rows=INSTALLS.map(i=>`<tr>
    <td style="color:#aaa;width:30px">${i.id}</td>
    <td><b style="font-weight:500">${i.name}</b></td>
    <td>${i.size||'-'}</td>
    <td style="font-size:12px;color:#666">${fmtD(i.wash1)}</td>
    <td style="font-size:12px;color:#666">${fmtD(i.wash2)}</td>
    <td>${washBadge(i)}</td>
    <td style="font-size:12px;color:#888">${i.contact||'-'}</td>
  </tr>`).join('');

  const barData = INSTALLS.map(i=>{
    const lw=lastWash(i);
    return {name:i.name, months: lw ? Math.round(monthsSince(lw)) : null};
  }).filter(x=>x.months!==null).sort((a,b)=>b.months-a.months);

  const barColors = barData.map(x=>
    x.months>12 ? '#f52222' : x.months>6 ? '#fdcb27' : '#00e44c'
  );

  const html = `
  <div class="metrics">
    <div class="mc"><div class="ml">ระบบที่ติดตั้งทั้งหมด</div><div class="mv">${total}</div><div class="ms">โครงการ</div></div>
    <div class="mc"><div class="ml">กำลังไฟฟ้ารวม</div><div class="mv">${kwSum.toLocaleString()}</div><div class="ms">kW</div></div>
    <div class="mc"><div class="ml">เกินกำหนดล้าง &gt;12 เดือน</div><div class="mv" style="color:#E24B4A">${overdue}</div><div class="ms">ระบบ</div></div>
    <div class="mc"><div class="ml">ควรนัดล้าง 6-12 เดือน</div><div class="mv" style="color:#BA7517">${shouldWash}</div><div class="ms">ระบบ</div></div>
    <div class="mc"><div class="ml">ยังไม่เคยล้างแผง</div><div class="mv" style="color:#888">${noWash}</div><div class="ms">ระบบ</div></div>
  </div>

  <div style="display:grid;grid-template-columns:280px 1fr;gap:16px;margin-bottom:16px">
    <div class="card" style="overflow:visible">
      <div class="ct">สัดส่วนสถานะ</div>
      <div style="padding:16px">
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">
          <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:#444"><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#f52222"></span>เกินกำหนด ${overdue}</span>
          <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:#444"><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#fdcb27"></span>ควรล้าง ${shouldWash}</span>
          <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:#444"><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#00e44c"></span>ปกติ ${ok}</span>
          <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:#444"><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#0169fc"></span>ยังไม่ล้าง ${noWash}</span>
        </div>
        <div style="position:relative;width:100%;height:200px">
          <canvas id="dash-donut" role="img" aria-label="แผนภูมิวงกลมสัดส่วนสถานะการล้างแผง">เกินกำหนด:${overdue}, ควรล้าง:${shouldWash}, ปกติ:${ok}, ยังไม่ล้าง:${noWash}</canvas>
        </div>
      </div>
    </div>
    <div class="card" style="overflow:visible">
      <div class="ct">จำนวนเดือนนับตั้งแต่ล้างแผงล่าสุด (เรียงมากไปน้อย)</div>
      <div style="padding:16px">
        <div style="position:relative;width:100%;height:${Math.max(200, barData.length*34+40)}px">
          <canvas id="dash-bar" role="img" aria-label="แผนภูมิแท่งจำนวนเดือนนับตั้งแต่ล้างแผงล่าสุดของแต่ละระบบ">
            ${barData.map(x=>x.name+':'+x.months+' เดือน').join(', ')}
          </canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="ct">สรุปสถานะการล้างแผงทั้งหมด</div>
    <div class="ov"><table>
      <thead><tr><th>#</th><th>ชื่อสถานที่</th><th>ขนาด</th><th>ล้างครั้งที่ 1</th><th>ล้างครั้งที่ 2</th><th>สถานะ</th><th>ติดต่อ</th></tr></thead>
      <tbody>${rows}</tbody>
    </table></div>
  </div>`;

  setTimeout(()=>{
    _dashCharts.forEach(c=>{ try{c.destroy();}catch(e){} });
    _dashCharts=[];
    const donutCtx=document.getElementById('dash-donut');
    const barCtx=document.getElementById('dash-bar');
    if(donutCtx && typeof Chart!=='undefined'){
      _dashCharts.push(new Chart(donutCtx,{
        type:'doughnut',
        data:{
          labels:['เกินกำหนด >12 เดือน','ควรนัดล้าง 6-12 เดือน','ปกติ ≤6 เดือน','ยังไม่เคยล้าง'],
          datasets:[{
            data:[overdue,shouldWash,ok,noWash],
            backgroundColor:['#f52222','#fdcb27','#00e44c','#0169fc'],
            borderWidth:2,borderColor:'#fff',hoverOffset:6
          }]
        },
        options:{
          responsive:true,maintainAspectRatio:false,cutout:'62%',
          plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>' '+ctx.label+': '+ctx.parsed+' ระบบ'}}}
        }
      }));
    }
    if(barCtx && typeof Chart!=='undefined'){
      _dashCharts.push(new Chart(barCtx,{
        type:'bar',
        data:{
          labels:barData.map(x=>x.name.length>16?x.name.substring(0,16)+'…':x.name),
          datasets:[{
            label:'เดือน',
            data:barData.map(x=>x.months),
            backgroundColor:barColors,
            borderRadius:4,borderSkipped:false
          }]
        },
        options:{
          indexAxis:'y',responsive:true,maintainAspectRatio:false,
          plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>' '+ctx.parsed.x+' เดือน'}}},
          scales:{
            x:{grid:{color:'rgba(0,0,0,0.05)'},ticks:{font:{size:11}},title:{display:true,text:'จำนวนเดือน',font:{size:11}}},
            y:{grid:{display:false},ticks:{font:{size:11}}}
          }
        }
      }));
    }
  },50);

  return html;
}

// =====================================================================
// RENDER: WASHING
// =====================================================================
function renderWash(){
  const overdue=INSTALLS.filter(i=>{ const lw=lastWash(i); return lw&&monthsSince(lw)>12; });
  const should=INSTALLS.filter(i=>{ const lw=lastWash(i); const m=monthsSince(lw); return lw&&m>6&&m<=12; });
  const ok=INSTALLS.filter(i=>{ const lw=lastWash(i); const m=monthsSince(lw); return lw&&m<=6; });
  const none=INSTALLS.filter(i=>!lastWash(i));

  function groupTable(list, showMonths){
    return `<div class="ov"><table>
      <thead><tr><th>#</th><th>ชื่อสถานที่</th><th>ขนาด</th><th>ล้างครั้งที่ 1</th><th>ล้างครั้งที่ 2</th>${showMonths?'<th>ผ่านมา</th>':''}<th>ติดต่อ</th><th style="width:90px">จัดการ</th></tr></thead>
      <tbody>${list.map(i=>{
        const lw=lastWash(i);
        return `<tr>
          <td style="color:#aaa">${i.id}</td>
          <td style="font-weight:500">${i.name}</td>
          <td>${i.size||'-'}</td>
          <td style="font-size:12px">${fmtD(i.wash1)}</td>
          <td style="font-size:12px">${fmtD(i.wash2)}</td>
          ${showMonths?`<td>${washBadge(i)}</td>`:''}
          <td style="font-size:12px;color:#888">${i.contact||'-'}</td>
          <td style="white-space:nowrap">
            <button class="btn-edit" onclick="openEditWash(${i.id})">แก้ไข</button>
            <button class="btn-del" onclick="askDelete('wash',${i.id})" style="margin-left:3px">ลบ</button>
          </td>
        </tr>`;
      }).join('')}</tbody>
    </table></div>`;
  }

  return `
  <div style="display:flex;justify-content:flex-end;margin-bottom:12px">
    <button class="btn-add" onclick="openAddWash()">+ เพิ่มรายการล้างแผง</button>
  </div>
  ${overdue.length?`
  <div class="card" style="border-left:3px solid #f70909">
    <div class="ct" style="color:#A32D2D">เกินกำหนดล้าง (มากกว่า 12 เดือน) <span class="badge b-due">${overdue.length} ระบบ</span></div>
    ${groupTable(overdue,true)}
  </div>`:''}
  ${should.length?`
  <div class="card" style="border-left:3px solid #BA7517">
    <div class="ct" style="color:#633806">ควรนัดล้างแผง (6-12 เดือน) <span class="badge b-warn">${should.length} ระบบ</span></div>
    ${groupTable(should,true)}
  </div>`:''}
  ${ok.length?`
  <div class="card" style="border-left:3px solid #639922">
    <div class="ct" style="color:#27500A">ล้างแผงแล้ว ปกติ (ไม่เกิน 6 เดือน) <span class="badge b-ok">${ok.length} ระบบ</span></div>
    ${groupTable(ok,true)}
  </div>`:''}
  ${none.length?`
  <div class="card" style="border-left:3px solid #888">
    <div class="ct" style="color:#444">ยังไม่เคยล้างแผง <span class="badge b-gray">${none.length} ระบบ</span></div>
    ${groupTable(none,false)}
  </div>`:''}`;
}

// =====================================================================
// RENDER: DOCUMENTS
// =====================================================================
function renderDocs(){
  return `
  <div class="two-col">
    <div class="card">
      <div class="ct">เอกสารขอใบอนุญาต อ.1 (ติดต่อเขต)</div>
      <table>
        <thead><tr><th style="width:28px">#</th><th>รายละเอียด</th><th style="width:90px">สถานะ</th></tr></thead>
        <tbody>${PERMIT_DOCS.map(d=>`<tr>
          <td style="color:#aaa">${d.id}</td>
          <td style="font-size:12px;line-height:1.5">${d.name}</td>
          <td>${docBadge(d.status)}</td>
        </tr>`).join('')}</tbody>
      </table>
    </div>
    <div class="card">
      <div class="ct">เอกสารแจ้งการติดตั้งโซล่าเซลล์กับเขต</div>
      <table>
        <thead><tr><th style="width:28px">#</th><th>รายละเอียด</th><th style="width:60px">จำนวน</th></tr></thead>
        <tbody>${NOTIFY_DOCS.map(d=>`<tr>
          <td style="color:#aaa">${d.id}</td>
          <td style="font-size:12px;line-height:1.5">${d.name}</td>
          <td><span class="badge b-navy">${d.qty}</span></td>
        </tr>`).join('')}</tbody>
      </table>
    </div>
  </div>`;
}

// =====================================================================
// RENDER: ACCOUNTS
// =====================================================================
function renderAcc(){
  const q=searchQ.toLowerCase();
  const list=ACCOUNTS.filter(a=>!q||
    a.customer.toLowerCase().includes(q)||
    a.username.toLowerCase().includes(q)||
    a.plane.toLowerCase().includes(q)
  );
  const rows=list.map((a,i)=>`<tr>
    <td style="color:#aaa">${a.id}</td>
    <td style="font-weight:500">${a.customer||'-'}</td>
    <td style="color:#666">${a.plane||'-'}</td>
    <td class="mono">${a.username||'-'}</td>
    <td>
      ${a.password&&a.password!=='-'?`
        <span id="pw${i}" class="mono" data-p="${a.password}" data-v="0" style="color:#888">••••••••</span>
        <button class="btn-sm" id="pb${i}" onclick="togglePwd(${i})" style="margin-left:6px">แสดง</button>
      `:`<span style="color:#ccc">-</span>`}
    </td>
    <td style="font-size:12px;color:#666">${a.email||'-'}</td>
    <td><span class="badge b-purple" style="font-size:10px">${a.inverter||'-'}</span></td>
    <td style="white-space:nowrap">
      <button class="btn-edit" onclick="openEditAcc(${a.id})">แก้ไข</button>
      <button class="btn-del" onclick="askDelete('acc',${a.id})" style="margin-left:3px">ลบ</button>
    </td>
  </tr>`).join('');

  return `
  <div class="card">
    <div class="ct">
      <span>บัญชีผู้ใช้งานระบบ Monitoring (${list.length}/${ACCOUNTS.length})</span>
      <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <input class="search-inp" type="text" placeholder="ค้นหาลูกค้า / username..." value="${searchQ}"
          onkeyup="searchQ=this.value; renderMain()">
        <button class="btn-add" onclick="openAddAcc()">+ เพิ่มบัญชี</button>
      </div>
    </div>
    <div class="ov"><table>
      <thead><tr><th>#</th><th>ลูกค้า</th><th>Plane</th><th>Username</th><th>Password</th><th>Email</th><th>Inverter</th><th style="width:90px">จัดการ</th></tr></thead>
      <tbody>${rows}</tbody>
    </table></div>
  </div>`;
}

// =====================================================================
// RENDER: WORK PLAN (Gantt)
// =====================================================================
function renderPlan(){
  const TOTAL=60;
  const rows=WORK_STEPS.map(s=>{
    const color=CAT_COLORS[s.cat]||'#888';
    const lPct=((s.start-1)/TOTAL*100).toFixed(1);
    const wPct=(s.dur/TOTAL*100).toFixed(1);
    return `<div class="gr">
      <div class="gl" title="${s.name}">${s.id}. ${s.name}</div>
      <div class="gt">
        <div class="gb" style="left:${lPct}%;width:${wPct}%;background:${color}">
          <span class="gd">${s.start}-${s.start+s.dur-1}</span>
        </div>
      </div>
      <div class="gn">${s.dur}d</div>
    </div>`;
  }).join('');

  const markers=[1,10,20,30,40,50,60].map(d=>
    `<div style="position:absolute;left:${((d-1)/60*100).toFixed(1)}%;transform:translateX(-50%);font-size:10px;color:#bbb">วัน ${d}</div>`
  ).join('');

  const legends=Object.entries(CAT_LABELS).map(([k,v])=>
    `<div class="le"><span class="lc" style="background:${CAT_COLORS[k]}"></span>${v}</div>`
  ).join('');

  return `
  <div class="card" style="padding:16px">
    <div class="ct" style="border:none;padding:0 0 12px">แผนการดำเนินการติดตั้งโซล่าเซลล์ — 60 วัน</div>
    <div style="position:relative;height:18px;margin-left:268px;margin-bottom:6px">${markers}</div>
    ${rows}
    <div class="legend" style="margin-top:14px;padding-top:12px;border-top:1px solid #f0f0ee">${legends}</div>
  </div>`;
}

// =====================================================================
// RENDER: SCHEDULE
// =====================================================================
function renderSched(){
  function statusBadge(s){
    if(s==='success') return '<span class="badge b-ok">success</span>';
    if(s==='pending') return '<span class="badge b-warn">pending</span>';
    if(s==='cancel')  return '<span class="badge b-due">cancel</span>';
    return `<span class="badge b-gray">${s}</span>`;
  }

  const rows=SCHEDULE.map(s=>`<tr>
    <td style="font-weight:500">${s.name}</td>
    <td>${s.contact||'-'}</td>
    <td style="white-space:nowrap;font-size:12px">${s.tel||'-'}</td>
    <td><span class="badge b-info">${s.size||'-'}</span></td>
    <td style="white-space:nowrap">${s.date||'-'}</td>
    <td>${s.time||'-'}</td>
    <td>${s.team||'-'}</td>
    <td><span class="tag">${s.resp||'-'}</span></td>
    <td>${statusBadge(s.status)}</td>
    <td style="white-space:nowrap;font-weight:500;color:#0F6E56">${s.nextWash||'-'}</td>
    <td style="white-space:nowrap">
      <button class="btn-edit" onclick="openEditSched(${s.id})">แก้ไข</button>
      <button class="btn-del" onclick="askDelete('sched',${s.id})" style="margin-left:3px">ลบ</button>
    </td>
  </tr>`).join('');

  return `
  <div style="display:flex;justify-content:flex-end;margin-bottom:12px">
    <button class="btn-add" onclick="openAddSched()">+ เพิ่มตารางงาน</button>
  </div>
  <div class="card">
    <div class="ct">ตารางงานติดตั้งโซล่าเซลล์ <span class="badge b-info" style="font-size:11px">${SCHEDULE.length} รายการ</span></div>
    <div class="ov"><table>
      <thead><tr>
        <th>ชื่อสถานที่</th><th>ผู้ติดต่อ</th><th>เบอร์โทร</th><th>ขนาด</th>
        <th>วันติดตั้ง</th><th>เวลา</th><th>ทีมงาน</th><th>ผู้รับผิดชอบ</th><th>สถานะ</th><th>กำหนดล้าง</th><th style="width:90px">จัดการ</th>
      </tr></thead>
      <tbody>${rows}</tbody>
    </table></div>
  </div>`;
}

// =====================================================================
// MAIN RENDER
// =====================================================================
const ICONS={
  dash:'<svg class="ni" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>',
  wash:'<svg class="ni" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/></svg>',
  docs:'<svg class="ni" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
  acc: '<svg class="ni" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
  plan:'<svg class="ni" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
  sched:'<svg class="ni" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
};
const TABS=[
  {id:'dash', label:'Dashboard'},
  {id:'wash', label:'การล้างแผง'},
  {id:'docs', label:'เอกสาร'},
  {id:'acc',  label:'บัญชีผู้ใช้'},
  {id:'plan', label:'แผนงาน'},
  {id:'sched',label:'ตารางงาน'},
];
const RENDERS={dash:renderDash,wash:renderWash,docs:renderDocs,acc:renderAcc,plan:renderPlan,sched:renderSched};

function renderNav(){
  document.getElementById('nav').innerHTML=TABS.map(t=>
    `<button class="nb${t.id===tab?' on':''}" onclick="setTab('${t.id}')">${ICONS[t.id]||''}<span>${t.label}</span></button>`
  ).join('');
}

function renderMain(){
  document.getElementById('main').innerHTML=RENDERS[tab]();
}
function render(){ renderNav(); renderMain(); }

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(el=>{
  el.addEventListener('click', e=>{ if(e.target===el) el.classList.remove('open'); });
});
document.getElementById('confirm-del').addEventListener('click', e=>{ if(e.target===e.currentTarget) closeConfirm(); });

// Init
document.getElementById('today-label').textContent=
  TODAY.toLocaleDateString('th-TH',{day:'numeric',month:'long',year:'numeric'});
render();
</script>
</body>
</html>