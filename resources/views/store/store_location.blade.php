<!DOCTYPE html>
{{-- resources/views/store/store_location.blade.php  (ด่าน 2: ระบุตำแหน่ง) --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ระบุตำแหน่ง (ด่าน 2)</title>
    <style>
        :root{
            --ink:#1e293b; --canvas:#ffffff; --muted:#6b7280; --border:#dcdcdc;
            --primary:#2563eb; --primary-dark:#1d4ed8; --primary-light:#eff6ff;
            --on-primary:#ffffff; --success:#16a34a; --success-dark:#15803d;
            --danger:#dc2626; --danger-dark:#b91c1c; --warning:#ea580c;
            --row-hover:#f0f7ff; --row-done:#f8fafc;
        }
        * { box-sizing: border-box; margin:0; padding:0; }
        html,body { background:#eef2f7; }
        body {
            font-family:'Segoe UI', Tahoma, Arial, sans-serif; font-size:14px;
            color:var(--ink); padding:16px;
        }
        .page-frame { background:var(--canvas); border-radius:12px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.08); }
        .top-banner {
            background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:var(--on-primary);
            font-weight:700; font-size:20px;
            padding:16px 20px; display:flex; align-items:center; justify-content:space-between;
        }
        .top-banner .sticker {
            background:rgba(255,255,255,.18); color:var(--on-primary); border:1px solid rgba(255,255,255,.4);
            font-weight:600; font-size:11px; border-radius:20px;
            padding:4px 12px; text-transform:uppercase; letter-spacing:.3px;
        }
        main { padding:20px; background:var(--canvas); }
        .toolbar { display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; align-items:center; }
        input[type="text"],input[type="search"] {
            padding:7px 10px; border:1px solid var(--border); border-radius:6px;
            font-family:inherit; font-size:14px; background:var(--canvas); color:var(--ink);
        }
        input:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px var(--primary-light); }
        button {
            padding:7px 18px; border:1px solid transparent; border-radius:6px;
            font-family:inherit; font-weight:600; font-size:13px;
            cursor:pointer; transition:.15s ease;
        }
        .btn-primary { background:var(--primary); color:var(--on-primary); }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-success { background:var(--success); color:var(--on-primary); }
        .btn-success:hover { background:var(--success-dark); }
        .btn-ghost   { background:var(--canvas); color:var(--muted); border-color:var(--border); }
        .btn-ghost:hover { background:#f3f4f6; color:var(--ink); }
        button:disabled { opacity:.4; cursor:not-allowed; }
        table { width:100%; border-collapse:collapse; background:var(--canvas); border:1px solid var(--border); border-radius:8px; overflow:hidden; }
        caption { text-align:left; padding:8px 2px; font-size:12px; color:var(--muted); }
        th,td { border-bottom:1px solid var(--border); padding:10px 12px; text-align:left; font-size:13px; }
        thead th {
            background:var(--primary-light); color:var(--primary-dark);
            font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:.2px;
            border-bottom:2px solid var(--primary);
        }
        tbody tr:hover { background:var(--row-hover); }
        .num { text-align:right; font-variant-numeric:tabular-nums; }
        .center { text-align:center; }
        .empty { text-align:center; color:var(--muted); padding:32px; font-style:italic; }
        .muted { font-size:11px; color:var(--muted); }
        tr.done td { color:#94a3b8; background:var(--row-done); }
        .actionbar { margin-top:16px; display:flex; gap:8px; }
        dialog { border:none; border-radius:12px; padding:22px; width:380px; box-shadow:0 10px 40px rgba(0,0,0,.2); }
        dialog::backdrop { background:rgba(15,23,42,.5); }
        dialog h2 { font-size:17px; font-weight:700; margin:0 0 14px; color:var(--primary-dark); }
        dialog label { display:block; margin-bottom:5px; font-weight:600; font-size:12px; text-transform:uppercase; color:var(--muted); }
        dialog input { width:100%; }
        .dialog-actions { display:flex; gap:8px; justify-content:flex-end; margin-top:18px; }
        .hint { font-size:12px; color:var(--muted); margin-top:6px; min-height:16px; }
        .chips { display:flex; gap:6px; flex-wrap:wrap; margin-top:8px; }
        .chip {
            padding:6px 14px; border:1px solid var(--border); background:var(--canvas); color:var(--muted);
            font-weight:600; font-size:12px; border-radius:20px;
            cursor:pointer; text-decoration:none; display:inline-block; transition:.15s ease;
        }
        a.chip { text-decoration:none; }
        .chip:hover { border-color:var(--primary); color:var(--primary); }
        .chip.active { background:var(--primary); color:var(--on-primary); border-color:var(--primary); }
        .items-cell { max-width:280px; }
        .items-cell .more { color:var(--muted); }
        details.items-expand summary { cursor:pointer; color:var(--primary); font-size:12px; list-style:none; font-weight:600; }
        details.items-expand summary::-webkit-details-marker { display:none; }
        details.items-expand[open] summary { margin-bottom:4px; }
        .subline { font-size:12px; color:#475569; padding:2px 0; }
    </style>
</head>
<body>
<div class="page-frame">
<div class="top-banner">
    <span>ระบุตำแหน่งจัดเก็บ (ด่าน 2)</span>
    <span class="sticker">Store</span>
</div>
@php $nav = $creator ? ['create_by' => $creator] : []; @endphp
<main>
    <nav class="chips" style="margin-bottom:14px;">
        <a class="chip {{ request()->routeIs('store.location') ? 'active' : '' }}" href="{{ route('store.location', $nav) }}">2) ระบุตำแหน่ง</a>
        <a class="chip {{ request()->routeIs('store.checkout') ? 'active' : '' }}" href="{{ route('store.checkout', $nav) }}">3) ของออก</a>
    </nav>

    <form class="toolbar" method="GET" action="{{ url()->current() }}">
        @if ($creator)<input type="hidden" name="create_by" value="{{ $creator }}">@endif
        <input type="search" name="SONum" value="{{ request('SONum') }}" placeholder="ค้นหา SO..." autocomplete="off">
        <button type="submit" class="btn-primary">ค้นหา</button>
        @if (request('SONum'))
            <a href="{{ url()->current() }}{{ $creator ? '?create_by='.urlencode($creator) : '' }}">
                <button type="button" class="btn-ghost">ล้าง</button>
            </a>
        @endif

        <label class="chip" style="cursor:default; background:var(--primary-light); color:var(--primary-dark);">
            ผู้ดำเนินการ:&nbsp;<input type="text" id="inpUser" value="{{ $creator }}" placeholder="ชื่อผู้กด"
                style="border:0;background:transparent;width:120px;padding:0;color:var(--ink);">
        </label>

        <label class="muted" style="margin-left:auto;">
            <input type="checkbox" id="chkHideDone" checked> ซ่อนที่ทำแล้ว
        </label>
    </form>

    <table>
        <caption>
            รอระบุตำแหน่ง {{ $heads->where('status', \App\Models\internal_po::ST_FINISH)->count() }} /
            แสดง {{ $heads->count() }} ใบ
        </caption>
        <thead>
            <tr>
                <th class="center" style="width:44px;"><input type="checkbox" id="chkAll"></th>
                <th>PO ภายใน</th><th>SO</th><th>รายการสินค้า</th><th class="num">จำนวนรวม</th>
                <th>ลูกค้า</th><th>กล่อง</th><th>จัดโดย</th><th>เวลาจัด</th><th>สถานะ</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($heads as $h)
            @php
                $todo  = $h->status === \App\Models\internal_po::ST_FINISH;
                $cls   = $todo ? '' : 'done';
                $items = $h->lines;
                $totalQty = $items->sum('item_quantity');
                $location = $h->location;
            @endphp
            <tr class="{{ $cls }}" data-done="{{ $todo ? 0 : 1 }}">
                <td class="center">
                    @if ($todo)<input type="checkbox" class="chkLine" value="{{ $h->internal_id }}">@endif
                </td>
                <td>{{ $h->internal_id }}</td>
                <td>{{ $h->SO_id }}</td>
                <td class="items-cell">
                    @if ($items->count() <= 2)
                        {{ $items->pluck('item_name')->implode(', ') }}
                    @else
                        <details class="items-expand">
                            <summary>{{ $items->first()->item_name }} <span class="more">และอีก {{ $items->count() - 1 }} รายการ</span></summary>
                            @foreach ($items as $it)
                                <div class="subline">• {{ $it->item_name }} ({{ number_format($it->item_quantity, 2) }})</div>
                            @endforeach
                        </details>
                    @endif
                </td>
                <td class="num">{{ number_format($totalQty, 2) }}</td>
                <td>{{ $h->customer_name }}</td>
                <td>{{ $location ?: '—' }}</td>
                <td>{{ $h->pick_by ?: '—' }}</td>
                <td class="muted">{{ $h->pick_at ? \Carbon\Carbon::parse($h->pick_at)->format('d/m/Y H:i') : '—' }}</td>
                <td style="color:{{ $todo ? 'inherit' : $h->status_color }};">{{ $h->status }}</td>
            </tr>
        @empty
            <tr><td colspan="10" class="empty">ไม่มีรายการ</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="actionbar">
        <button type="button" class="btn-success" id="btnMain" hidden onclick="openModal()">
            ระบุตำแหน่ง (<span id="selCount">0</span>)
        </button>
    </div>
</main>
</div>

<dialog id="locModal">
    <h2>ระบุตำแหน่งจัดเก็บ (ชั้นวาง)</h2>
    <label for="inpLocation">ชั้นวาง</label>
    <input type="text" id="inpLocation" list="locHistory" placeholder="พิมพ์ค้นหาชั้นวาง เช่น A11" autocomplete="off" maxlength="100">
    <datalist id="locHistory"></datalist>
    <p class="hint" id="dlgHint"></p>

    @if (($locations ?? collect())->count())
        <div class="muted">ใช้ล่าสุด</div>
        <div class="chips">
            @foreach ($locations->take(6) as $loc)<span class="chip" onclick="pickLoc(this)">{{ $loc }}</span>@endforeach
        </div>
    @endif

    <div class="dialog-actions">
        <button type="button" class="btn-ghost" onclick="document.getElementById('locModal').close()">ยกเลิก</button>
        <button type="button" class="btn-primary" onclick="confirmLoc()">OK</button>
    </div>
</dialog>

<script>
const SUBMIT_URL = "{{ route('store.location.submit') }}";
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
const modal      = document.getElementById('locModal');

// รายชื่อชั้นวาง — ชุดเดียวกับ app mobile (po.mobile_app)
const SHELF_OPTIONS = [
  "1A01","1Kโบว์","1กวาง","1กี้","1ตี๋/พลอย","1ต่าย","1ท๊อป","1นภา",
  "1นุ/เต้น","1นุช","1นุ่น","1น้อย/มล","1น้ำ/กิ๊ฟ","1ฟอง","1ฟิล์ม",
  "1ภิรุณ","1มุก","1หนิง","1หมิง","1หมู/ต๋อง","1เจี๊ยบ","1เชร์",
  "1เนย","1เอก","1แยม","1แอม","1โจ",
  "A11","A12","A13","A14","A21","A22","A23","A24",
  "A31","A32","A33","A34","A41","A42","A43","A44",
  "aom stock","B1",
  "C1","C10","C11","C12","C13","C14","C15","C16",
  "C2","C3","C4","C5","C6","C7","C8","C9","Cท่อ",
  "LASADA",
  "Qกรมศุลเล็","Qจัดแล้วรอ","Qติดปัญหา","Qบิลสด","QรอครบSO","Qสหกรณ์","Qแก้ไข","Qแดง",
  "top stock","Z55",
  "ขมจ่ายแล้ว","ของเกิน","คืนstock","ช.เดช","ช.โอ","ชัย-เดช","ชัย1","ชัย2",
  "ด.1","ด.10","ด.11","ด.12","ด.2","ด.3","ด.4","ด.5","ด.6","ด.7","ด.8","ด.9",
  "ทำคืน","ท๊อปบน","ปอ-ฮิคาริ","ปิดรับบิล","ปี69","พู่",
  "ว๊าล","ว๊าลแก้ไข","หน้าออฟฟิศ","หยกรอบิล","หยกรอเคลีย","หลังออฟฟิศ"
];

function populateShelfDatalist() {
    const dl = document.getElementById('locHistory');
    dl.innerHTML = SHELF_OPTIONS
        .map(s => `<option value="${s.replace(/"/g, '&quot;')}"></option>`)
        .join('');
}
populateShelfDatalist();

const selectedIds = () => Array.from(document.querySelectorAll('.chkLine:checked')).map(c => c.value);
const currentUser = () => document.getElementById('inpUser').value.trim();

function refreshBtn() {
    const n = selectedIds().length;
    document.getElementById('selCount').textContent = n;
    document.getElementById('btnMain').hidden = (n === 0);
}
document.getElementById('chkAll').addEventListener('change', function () {
    document.querySelectorAll('.chkLine').forEach(c => c.checked = this.checked);
    refreshBtn();
});
document.querySelectorAll('.chkLine').forEach(c => c.addEventListener('change', refreshBtn));

const chkHide = document.getElementById('chkHideDone');
const applyHide = () => document.querySelectorAll('tr[data-done="1"]').forEach(tr => tr.hidden = chkHide.checked);
chkHide.addEventListener('change', applyHide);
applyHide();

function pickLoc(el) { const i = document.getElementById('inpLocation'); i.value = el.textContent.trim(); i.focus(); }

function openModal() {
    if (!currentUser())        { alert('กรุณาระบุชื่อผู้ดำเนินการ'); document.getElementById('inpUser').focus(); return; }
    if (!selectedIds().length) { alert('ยังไม่ได้เลือกรายการ'); return; }
    document.getElementById('inpLocation').value = '';
    const h = document.getElementById('dlgHint');
    h.textContent = 'จะบันทึก ' + selectedIds().length + ' ใบ (ทุกใบที่เลือกจะใช้ตำแหน่งเดียวกัน)'; h.style.color = '#6b7280';
    modal.showModal(); document.getElementById('inpLocation').focus();
}
document.getElementById('inpLocation').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); confirmLoc(); } });

async function confirmLoc() {
    const box = document.getElementById('inpLocation').value.trim();
    const h   = document.getElementById('dlgHint');
    if (!box) { h.textContent = 'กรุณาระบุชั้นวาง'; h.style.color = '#dc2626'; document.getElementById('inpLocation').focus(); return; }
    if (!SHELF_OPTIONS.includes(box)) {
        h.textContent = 'ไม่พบชั้นวางนี้ในระบบ กรุณาเลือกจากรายการ';
        h.style.color = '#dc2626';
        document.getElementById('inpLocation').focus();
        return;
    }

    const btn = document.querySelector('#locModal .btn-primary'); btn.disabled = true; btn.textContent = 'กำลังบันทึก...';
    try {
        const res = await fetch(SUBMIT_URL, {
            method: 'POST',
            headers: { 'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF },
            body: JSON.stringify({ ids: selectedIds(), user: currentUser(), location: box })
        });
        const data = await res.json();
        if (res.ok && data.ok) { alert(data.message); window.location.reload(); }
        else { alert(data.message || 'บันทึกไม่สำเร็จ'); btn.disabled = false; btn.textContent = 'OK'; }
    } catch (e) { console.error(e); alert('เกิดข้อผิดพลาด'); btn.disabled = false; btn.textContent = 'OK'; }
}
</script>
</body>
</html>