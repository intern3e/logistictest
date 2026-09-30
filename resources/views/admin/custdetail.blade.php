<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>จัดการข้อมูลลูกค้า (custdetail)</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{
            --primary:#3E6AE1; --primary-hover:#2f56c4; --primary-light:#eef2fd; --primary-border:#c7d5f5;
            --green:#16a34a; --green-dark:#15803d;
            --red:#dc2626;
            --bg:#f5f7fa; --surface:#fff; --border:#e5e7eb; --border-strong:#d1d5db;
            --ink:#1b2d4f; --ink2:#374151; --muted:#6b7280; --hint:#9ca3af;
            --r:8px; --rl:12px;
            --font:'Sarabun','Segoe UI',system-ui,sans-serif;
            --mono:'JetBrains Mono',ui-monospace,monospace;
        }
        body{font-family:var(--font);background:var(--bg);color:var(--ink);font-size:14px;line-height:1.5;-webkit-font-smoothing:antialiased;padding:24px 16px 60px}
        .container{max-width:760px;margin:0 auto}
        .header-bar{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px}
        .title{font-size:22px;font-weight:800;letter-spacing:-.3px;display:flex;align-items:center;gap:10px}
        .title::before{content:'👤';font-size:22px}
        .subtitle{font-size:12.5px;color:var(--muted);margin-top:2px;font-weight:500}
        .btn-back{background:var(--surface);color:var(--ink2);border:1px solid var(--border);padding:8px 16px;border-radius:var(--r);cursor:pointer;font-size:13px;font-weight:600;font-family:inherit;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
        .btn-back:hover{background:var(--bg);border-color:var(--border-strong)}

        .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--rl);box-shadow:0 1px 3px rgba(0,0,0,.05);overflow:hidden}
        .card-head{padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;flex-wrap:wrap}
        .card-head h3{font-size:15px;font-weight:700}
        .user-chip{margin-left:auto;font-size:12px;color:var(--ink2);background:var(--primary-light);border:1px solid var(--primary-border);border-radius:16px;padding:5px 12px;font-weight:600}
        .card-body{padding:22px}

        /* 4 กล่อง */
        .box-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
        .box{display:flex;flex-direction:column;gap:8px;border:1.5px solid var(--border);border-radius:var(--r);padding:14px 16px;background:var(--surface);transition:border .15s,box-shadow .15s}
        .box:focus-within{border-color:var(--primary);box-shadow:0 0 0 3px rgba(62,106,225,.10)}
        .box.span-full{grid-column:1/-1}
        .box-label{font-size:12px;font-weight:700;color:var(--ink2);letter-spacing:.03em;display:flex;align-items:center;gap:6px}
        .box-label .num{display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:50%;background:var(--primary);color:#fff;font-size:11px;font-weight:700;font-family:var(--mono)}
        .box-label .req{color:var(--red)}
        .box input[type="text"],.box textarea,.box select{
            width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:6px;background:var(--surface);color:var(--ink);
            font-size:14px;font-family:inherit;outline:none;transition:border .15s,box-shadow .15s;
        }
        .box input:focus,.box textarea:focus,.box select:focus{border-color:var(--primary);box-shadow:0 0 0 2px rgba(62,106,225,.12)}
        .box input#idcust{font-family:var(--mono);font-weight:600;letter-spacing:.02em}
        .box textarea{resize:vertical;min-height:72px;line-height:1.5}
        .box select{cursor:pointer;appearance:none;-webkit-appearance:none;
            background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M3 4.5L6 7.5L9 4.5' stroke='%236b7280' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round' fill='none'/%3E%3C/svg%3E");
            background-repeat:no-repeat;background-position:right 12px center;padding-right:32px}
        .box-hint{font-size:11px;color:var(--hint)}
        .box-hint.found{color:var(--green-dark);font-weight:600}

        .actions{display:flex;align-items:center;gap:12px;margin-top:22px;flex-wrap:wrap}
        .btn-save{display:inline-flex;align-items:center;justify-content:center;gap:8px;background:linear-gradient(135deg,var(--primary),#5a85e8);color:#fff;border:none;padding:13px 30px;border-radius:var(--r);font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;box-shadow:0 4px 12px rgba(62,106,225,.30);transition:all .15s}
        .btn-save:hover{background:linear-gradient(135deg,var(--primary-hover),var(--primary));transform:translateY(-1px)}
        .btn-save:disabled{background:linear-gradient(135deg,#94a3b8,#cbd5e1);cursor:not-allowed;transform:none;box-shadow:none}
        .btn-clear{background:var(--surface);color:var(--muted);border:1px solid var(--border);padding:12px 20px;border-radius:var(--r);font-size:14px;font-weight:600;cursor:pointer;font-family:inherit}
        .btn-clear:hover{background:var(--bg);color:var(--ink)}
        .save-msg{font-size:13px;font-weight:600;min-height:20px}
        .save-msg.ok{color:var(--green-dark)}
        .save-msg.err{color:var(--red)}

        @media (max-width:640px){
            body{padding:16px 12px 48px}
            .box-grid{grid-template-columns:1fr}
            .btn-save{width:100%}
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header-bar">
        <div>
            <div class="title">จัดการข้อมูลลูกค้า</div>
            <div class="subtitle">รหัสลูกค้า · ชื่อ · แบบฟอร์มเอกสาร · หมายเหตุ (custdetail)</div>
        </div>
        <button type="button" class="btn-back" onclick="history.back()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            ย้อนกลับ
        </button>
    </div>

    <div class="card">
        <div class="card-head">
            <h3>เพิ่ม / แก้ไขข้อมูลลูกค้า</h3>
            <span class="user-chip">ผู้ใช้งาน: {{ $creator ?: '—' }}</span>
        </div>
        <div class="card-body">
            <div class="box-grid">
                <!-- กล่อง 1: รหัสลูกค้า -->
                <div class="box">
                    <label class="box-label" for="idcust"><span class="num">1</span> รหัสลูกค้า <span class="req">*</span></label>
                    <input type="text" id="idcust" name="idcust" placeholder="เช่น CUS-16809" autocomplete="off">
                    <span class="box-hint" id="idcustHint">พิมพ์รหัสแล้วระบบจะดึงข้อมูลเดิมมาให้อัตโนมัติ (ถ้ามี)</span>
                </div>

                <!-- กล่อง 2: ชื่อลูกค้า -->
                <div class="box">
                    <label class="box-label" for="namecust"><span class="num">2</span> ชื่อลูกค้า</label>
                    <input type="text" id="namecust" name="namecust" placeholder="ชื่อบริษัท / ลูกค้า" autocomplete="off">
                    <span class="box-hint">ชื่อที่ใช้แสดงคู่กับรหัสลูกค้า</span>
                </div>

                <!-- กล่อง 3: แบบฟอร์มเอกสาร -->
                <div class="box">
                    <label class="box-label" for="formtype"><span class="num">3</span> แบบฟอร์มเอกสาร</label>
                    <select id="formtype" name="formtype">
                        <option value="">— เลือกแบบฟอร์ม —</option>
                        <option value="บิล/PO3">บิล/PO3</option>
                        <option value="บิล/PO3/วางบิล">บิล/PO3/วางบิล</option>
                        <option value="บิล/PO3/วางบิล/สำเนาหน้าบิล2">บิล/PO3/วางบิล/สำเนาหน้าบิล2</option>
                        <option value="บิล/PO3/สำเนาหน้าบิล2">บิล/PO3/สำเนาหน้าบิล2</option>
                        <option value="บิล/PO3/บัญชี">บิล/PO3/บัญชี</option>
                        <option value="บิล/PO5/สำเนาหน้าบิล3">บิล/PO5/สำเนาหน้าบิล3</option>
                    </select>
                    <span class="box-hint">ใช้ตอนเปิดบิลของลูกค้ารายนี้</span>
                </div>

                <!-- กล่อง 4: หมายเหตุ -->
                <div class="box">
                    <label class="box-label" for="note"><span class="num">4</span> หมายเหตุ (note)</label>
                    <textarea id="note" name="note" rows="2" placeholder="หมายเหตุเพิ่มเติมของลูกค้ารายนี้"></textarea>
                    <span class="box-hint">ข้อความนี้จะดึงไปแสดงตอนเปิดบิล</span>
                </div>
            </div>

            <div class="actions">
                <button type="button" class="btn-save" id="btnSave">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none"><path d="M3.5 9l4 4 6-7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    บันทึกข้อมูล
                </button>
                <button type="button" class="btn-clear" id="btnClear">ล้างฟอร์ม</button>
                <span class="save-msg" id="saveMsg"></span>
            </div>
        </div>
    </div>
</div>

<script>
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
const LOOKUP_URL = "{{ route('admin.custdetail.lookup') }}";
const SAVE_URL   = "{{ route('admin.custdetail.save') }}";

const idcust   = document.getElementById('idcust');
const namecust = document.getElementById('namecust');
const formtype = document.getElementById('formtype');
const note     = document.getElementById('note');
const idHint   = document.getElementById('idcustHint');
const saveMsg  = document.getElementById('saveMsg');
const btnSave  = document.getElementById('btnSave');

function setMsg(text, cls){ saveMsg.textContent = text || ''; saveMsg.className = 'save-msg' + (cls ? ' ' + cls : ''); }

// ดึงข้อมูลเดิมมาเติมฟอร์มเมื่อออกจากช่องรหัสลูกค้า (ถ้ามีอยู่แล้ว = โหมดแก้ไข)
let lookupTimer = null;
async function lookupCust(){
    const v = idcust.value.trim();
    if(!v){ idHint.textContent = 'พิมพ์รหัสแล้วระบบจะดึงข้อมูลเดิมมาให้อัตโนมัติ (ถ้ามี)'; idHint.className = 'box-hint'; return; }
    try{
        const res = await fetch(LOOKUP_URL, {
            method:'POST',
            headers:{'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify({ idcust: v }),
        });
        const data = await res.json();
        if(data.found){
            namecust.value = data.namecust || '';
            formtype.value = [...formtype.options].some(o => o.value === (data.formtype || '')) ? (data.formtype || '') : '';
            note.value     = data.note || '';
            idHint.textContent = 'พบข้อมูลเดิม — การบันทึกจะเป็นการอัปเดต';
            idHint.className = 'box-hint found';
        } else {
            idHint.textContent = 'ยังไม่มีในระบบ — การบันทึกจะเป็นการเพิ่มใหม่';
            idHint.className = 'box-hint';
        }
    }catch(e){ /* เงียบไว้ ไม่รบกวนการกรอก */ }
}
idcust.addEventListener('blur', lookupCust);
idcust.addEventListener('input', () => {
    setMsg('');
    if(lookupTimer) clearTimeout(lookupTimer);
    lookupTimer = setTimeout(lookupCust, 500);
});

async function save(){
    const v = idcust.value.trim();
    idcust.value = v;   // ตัดช่องว่างหัวท้ายให้เห็นในช่องด้วย
    if(!v){ setMsg('กรุณากรอกรหัสลูกค้า', 'err'); idcust.focus(); return; }

    const original = btnSave.innerHTML;
    btnSave.disabled = true; btnSave.textContent = 'กำลังบันทึก...';
    setMsg('');
    try{
        const res = await fetch(SAVE_URL, {
            method:'POST',
            headers:{'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify({
                idcust: v,
                namecust: namecust.value.trim(),
                formtype: formtype.value,
                note: note.value,
            }),
        });
        const data = await res.json();
        if(res.ok && data.ok){
            setMsg((data.mode === 'update' ? '✓ ' : '✓ ') + (data.message || 'บันทึกแล้ว'), 'ok');
            idHint.textContent = 'พบข้อมูลเดิม — การบันทึกจะเป็นการอัปเดต';
            idHint.className = 'box-hint found';
        } else {
            setMsg(data.message || 'บันทึกไม่สำเร็จ', 'err');
        }
    }catch(e){ setMsg('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'err'); }
    finally{ btnSave.disabled = false; btnSave.innerHTML = original; }
}
btnSave.addEventListener('click', save);

document.getElementById('btnClear').addEventListener('click', () => {
    idcust.value=''; namecust.value=''; formtype.value=''; note.value='';
    setMsg(''); idHint.textContent = 'พิมพ์รหัสแล้วระบบจะดึงข้อมูลเดิมมาให้อัตโนมัติ (ถ้ามี)'; idHint.className='box-hint';
    idcust.focus();
});
</script>
</body>
</html>
