<!DOCTYPE html>
{{-- resources/views/store/store_location.blade.php  (ด่าน 2: ระบุตำแหน่ง) --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ระบุตำแหน่งจัดเก็บ</title>
    <style>
        .table-topbar {
            display:flex; align-items:center; justify-content:space-between;
            gap:12px; margin-bottom:8px; flex-wrap:wrap;
        }
        .table-topbar .table-info { font-size:12px; color:var(--muted); }
        .table-topbar #btnMain { margin:0; }
        :root{
            --ink:#1e293b; --canvas:#ffffff; --muted:#6b7280; --border:#dcdcdc;
            --primary:#2563eb; --primary-dark:#1d4ed8; --primary-light:#eff6ff;
            --on-primary:#ffffff; --success:#16a34a; --success-dark:#15803d;
            --danger:#dc2626; --danger-dark:#b91c1c; --warning:#ea580c;
            --row-hover:#f0f7ff; --row-done:#f8fafc; --page-bg:#eef2f7;
        }
        * { box-sizing: border-box; margin:0; padding:0; }
        html,body { background:var(--canvas); overflow-x:hidden; max-width:100%; }
        body {
            font-family:'Segoe UI', Tahoma, Arial, sans-serif; font-size:14px;
            color:var(--ink); padding:16px;
        }
        .page-frame { background:var(--canvas); max-width:100%; }
        .table-scroll { overflow-x:auto; -webkit-overflow-scrolling:touch; }

        /* ===== Header bar: plain white, title left ===== */
        .top-banner {
            background:var(--canvas); color:var(--ink);
            margin:0 -16px; padding:16px 12px 0;
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;
        }
        .top-banner .title-group { display:flex; align-items:center; gap:10px; margin-left:24px; }
        .top-banner .title-group .h1 { font-weight:700; font-size:22px; color:var(--ink); }
        .top-banner .sticker {
            background:var(--primary-light); color:var(--primary-dark); border:1px solid #bfdbfe;
            font-weight:600; font-size:11px;
            padding:4px 12px; text-transform:uppercase; letter-spacing:.3px;
        }
        .hide-done-toggle {
            display:inline-flex; align-items:center; gap:6px;
            font-size:13px; font-weight:700; font-family:inherit;
            padding:7px 16px; border:1px solid var(--border);
            background:var(--canvas); color:var(--muted);
            cursor:pointer; transition:.15s ease;
        }
        .hide-done-toggle:hover { border-color:var(--primary); color:var(--primary); }
        .hide-done-toggle.active { background:var(--primary); color:var(--on-primary); border-color:var(--primary); }

        main {
            padding:20px; background:var(--canvas);
        }
        .toolbar { display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; align-items:center; }
        .toolbar .filter-group { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
        .toolbar .field-label { font-size:12px; color:var(--muted); font-weight:600; margin-right:-4px; }
        input[type="text"],input[type="search"] {
            padding:8px 12px; border:1px solid var(--border);
            font-family:inherit; font-size:14px; background:var(--canvas); color:var(--ink);
        }
        input:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px var(--primary-light); }
        button {
            padding:8px 20px; border:1px solid transparent; border-radius:6px;
            font-family:inherit; font-weight:600; font-size:15px;
            cursor:pointer; transition:.15s ease;
        }
        .btn-primary { background:var(--primary); color:var(--on-primary); }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-success { background:var(--success); color:var(--on-primary); }
        .btn-success:hover { background:var(--success-dark); }
        .btn-ghost   { background:var(--canvas); color:var(--muted); border-color:var(--border); }
        .btn-ghost:hover { background:#f3f4f6; color:var(--ink); }
        button:disabled { opacity:.4; cursor:not-allowed; }

        /* ===== Table: solid blue header, full grid lines ===== */
        table { width:100%; min-width:900px; border-collapse:collapse; background:var(--canvas); border:1px solid var(--border); overflow:hidden; }
        caption { text-align:left; padding:8px 2px; font-size:12px; color:var(--muted); }
        th,td { border-bottom:1px solid var(--border); border-right:1px solid var(--border); padding:12px 14px; text-align:center; font-size:13px; }
        th:last-child,td:last-child { border-right:none; }
        thead th {
            background:var(--primary); color:var(--on-primary);
            font-weight:700; font-size:13px; letter-spacing:.2px;
            border-bottom:2px solid var(--primary-dark);
            border-right-color:rgba(255,255,255,.25);
        }
        tbody tr:nth-child(even) { background:#fafbfd; }
        tbody tr:hover { background:var(--row-hover); }
        .num { font-variant-numeric:tabular-nums; }
        .cust-cell { text-align:left; }
        .center { text-align:center; }
        .empty { text-align:center; color:var(--muted); padding:32px; font-style:italic; }
        .muted { font-size:11px; color:var(--muted); }
        tr.done td { color:#94a3b8; background:var(--row-done); }
        .actionbar { margin-top:16px; display:flex; gap:8px; }
        dialog {
            border:none; padding:32px; width:520px; max-width:90vw;
            box-shadow:0 20px 60px rgba(0,0,0,.25);
            position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); margin:0;
            overflow:visible;
        }
        dialog::backdrop { background:rgba(15,23,42,.5); }
        dialog h2 { font-size:22px; font-weight:700; margin:0 0 18px; color:var(--primary-dark); }
        dialog label { display:block; margin-bottom:8px; font-weight:600; font-size:14px; text-transform:uppercase; color:var(--muted); }
        dialog input { width:100%; font-size:18px; padding:14px 16px; }
        .dialog-actions { display:flex; gap:8px; justify-content:flex-end; margin-top:24px; }
        .dialog-actions button { font-size:16px; padding:10px 26px; }
        .hint { font-size:13px; color:var(--muted); margin-top:10px; min-height:18px; }
        .autocomplete-wrap { position:relative; }
        .suggest-panel {
            display:none; position:absolute; left:0; right:0; top:calc(100% + 4px);
            background:var(--canvas); border:1px solid var(--border);
            box-shadow:0 8px 24px rgba(0,0,0,.12);
            max-height:min(320px, 45vh); overflow-y:auto; z-index:9999;
        }
        .suggest-panel.open { display:block; }
        .suggest-item {
            padding:10px 16px; font-size:15px; color:var(--ink);
            cursor:pointer; border-bottom:1px solid #f1f5f9;
        }
        .suggest-item:last-child { border-bottom:none; }
        .suggest-item:hover, .suggest-item.hl { background:var(--row-hover); color:var(--primary-dark); }
        .suggest-empty { padding:12px 16px; font-size:13px; color:var(--muted); font-style:italic; }
        .chips { display:flex; gap:6px; flex-wrap:wrap; margin-top:8px; }
        .chip {
            padding:6px 14px; border:1px solid var(--border); background:var(--canvas); color:var(--muted);
            font-weight:600; font-size:12px;
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
        a.ref-link { color:var(--primary); font-weight:700; text-decoration:none; }
        a.ref-link:hover { text-decoration:underline; }

        /* ===== Pagination ===== */
        .pagination { display:flex; align-items:center; justify-content:center; gap:12px; margin-top:18px; }
        .page-btn {
            padding:7px 16px; border:1px solid var(--border); background:var(--canvas);
            color:var(--primary); font-weight:600; font-size:13px; text-decoration:none;
            transition:.15s ease;
        }
        .page-btn:hover { border-color:var(--primary); background:var(--primary-light); }
        .page-btn.disabled { color:#c3c9d1; cursor:not-allowed; pointer-events:none; }
        .page-info { font-size:13px; color:var(--muted); font-weight:600; }
    </style>
</head>
<body>
<div class="page-frame">
<div class="top-banner">
    <div class="title-group">
        <span class="h1">ระบุตำแหน่งจัดเก็บ</span>
        <span class="sticker">Store</span>
    </div>
</div>
<input type="hidden" id="inpUser" value="{{ $creator }}">
@php $nav = $creator ? ['create_by' => $creator] : []; @endphp
<main>
    <form class="toolbar" method="GET" action="{{ url()->current() }}">
        @if ($creator)<input type="hidden" name="create_by" value="{{ $creator }}">@endif
        <div class="filter-group">
            <input type="search" name="SONum" value="{{ request('SONum') }}" placeholder="ค้นหา SO..." autocomplete="off">
            <input type="search" name="PONum" value="{{ request('PONum') }}" placeholder="ค้นหา PO ภายใน..." autocomplete="off">
            <input type="search" name="customer" value="{{ request('customer') }}" placeholder="ค้นหาลูกค้า..." autocomplete="off">
            <input type="search" name="location" value="{{ request('location') }}" placeholder="ค้นหาที่เก็บ..." autocomplete="off">
            <input type="search" name="item" value="{{ request('item') }}" placeholder="ค้นหาสินค้า..." autocomplete="off">
        </div>
        <button type="submit" class="btn-primary">ค้นหา</button>
        @if (request('SONum') || request('PONum') || request('customer') || request('location') || request('item'))
            <a href="{{ url()->current() }}{{ $creator ? '?create_by='.urlencode($creator) : '' }}">
                <button type="button" class="btn-ghost">ล้าง</button>
            </a>
        @endif

        <button type="button" id="btnHideDone" class="hide-done-toggle active">ซ่อนที่ทำแล้ว</button>
    </form>

    <div class="table-scroll">
<div class="table-topbar">
    <div class="table-info">
        รอระบุตำแหน่ง {{ $totalTodo }} /
        แสดง {{ $heads->total() }} ใบ
        (หน้า {{ $heads->currentPage() }}/{{ $heads->lastPage() ?: 1 }})
    </div>
    <button type="button" class="btn-success" id="btnMain" hidden onclick="openModal()">
        ระบุตำแหน่ง (<span id="selCount">0</span>)
    </button>
</div>

<div class="table-scroll">
<table>
    <thead>
            <tr>
                <th class="center" style="width:44px;"><input type="checkbox" id="chkAll"></th>
                <th>PO ภายใน</th><th>SO</th><th>รายการสินค้า</th><th class="num">จำนวนรวม</th>
                <th>ลูกค้า</th><th>ที่เก็บ</th><th>จัดโดย</th><th>เวลาจัด</th>
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
                <td><span class="ref-link">{{ $h->internal_id }}</span></td>
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
                <td class="cust-cell">{{ $h->customer_name }}</td>
                <td>{{ $location ?: '—' }}</td>
                <td>{{ $h->pick_by ?: '—' }}</td>
                <td class="muted">{{ $h->pick_at ? \Carbon\Carbon::parse($h->pick_at)->format('d/m/Y H:i') : '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="9" class="empty">ไม่มีรายการ</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>

    @if ($heads->hasPages())
        <div class="pagination">
            @if ($heads->onFirstPage())
                <span class="page-btn disabled">« ก่อนหน้า</span>
            @else
                <a class="page-btn" href="{{ $heads->previousPageUrl() }}">« ก่อนหน้า</a>
            @endif

            <span class="page-info">หน้า {{ $heads->currentPage() }} / {{ $heads->lastPage() }} (ทั้งหมด {{ $heads->total() }} ใบ)</span>

            @if ($heads->hasMorePages())
                <a class="page-btn" href="{{ $heads->nextPageUrl() }}">ถัดไป »</a>
            @else
                <span class="page-btn disabled">ถัดไป »</span>
            @endif
        </div>
    @endif
</main>
</div>

<dialog id="locModal">
    <h2>ระบุตำแหน่งจัดเก็บ (ชั้นวาง)</h2>
    <label for="inpLocation">ชั้นวาง</label>
    <div class="autocomplete-wrap">
        <input type="text" id="inpLocation" placeholder="พิมพ์ค้นหาชั้นวาง เช่น A11" autocomplete="off" maxlength="100">
        <div id="locSuggest" class="suggest-panel"></div>
    </div>
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

const inpLocation = document.getElementById('inpLocation');
const suggestPanel = document.getElementById('locSuggest');
let hlIndex = -1;

function renderSuggestions(filter) {
    const q = filter.trim().toLowerCase();
    const matches = q
        ? SHELF_OPTIONS.filter(s => s.toLowerCase().includes(q)).slice(0, 30)
        : SHELF_OPTIONS.slice(0, 30);

    hlIndex = -1;
    if (!matches.length) {
        suggestPanel.innerHTML = '<div class="suggest-empty">ไม่พบชั้นวางที่ตรงกับคำค้นหา</div>';
        suggestPanel.classList.add('open');
        return;
    }
    suggestPanel.innerHTML = matches
        .map(s => `<div class="suggest-item" data-val="${s.replace(/"/g, '&quot;')}">${s}</div>`)
        .join('');
    suggestPanel.classList.add('open');
}

inpLocation.addEventListener('focus', () => renderSuggestions(inpLocation.value));
inpLocation.addEventListener('input', () => renderSuggestions(inpLocation.value));

suggestPanel.addEventListener('click', (e) => {
    const item = e.target.closest('.suggest-item');
    if (!item) return;
    inpLocation.value = item.dataset.val;
    suggestPanel.classList.remove('open');
    inpLocation.focus();
});

inpLocation.addEventListener('keydown', (e) => {
    const items = Array.from(suggestPanel.querySelectorAll('.suggest-item'));
    if (e.key === 'ArrowDown' && items.length) {
        e.preventDefault();
        hlIndex = Math.min(hlIndex + 1, items.length - 1);
        items.forEach((it, i) => it.classList.toggle('hl', i === hlIndex));
        items[hlIndex].scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'ArrowUp' && items.length) {
        e.preventDefault();
        hlIndex = Math.max(hlIndex - 1, 0);
        items.forEach((it, i) => it.classList.toggle('hl', i === hlIndex));
        items[hlIndex].scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (hlIndex >= 0 && items[hlIndex]) { inpLocation.value = items[hlIndex].dataset.val; suggestPanel.classList.remove('open'); }
        else { confirmLoc(); }
    } else if (e.key === 'Escape') {
        suggestPanel.classList.remove('open');
    }
});

document.addEventListener('click', (e) => {
    if (!e.target.closest('.autocomplete-wrap')) suggestPanel.classList.remove('open');
});

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

const btnHide = document.getElementById('btnHideDone');
const applyHide = () => document.querySelectorAll('tr[data-done="1"]').forEach(tr => tr.hidden = btnHide.classList.contains('active'));
btnHide.addEventListener('click', () => { btnHide.classList.toggle('active'); applyHide(); });
applyHide();

function pickLoc(el) { const i = document.getElementById('inpLocation'); i.value = el.textContent.trim(); i.focus(); }

function openModal() {
    if (!currentUser())        { alert('กรุณาระบุชื่อผู้ดำเนินการ'); return; }
    if (!selectedIds().length) { alert('ยังไม่ได้เลือกรายการ'); return; }
    document.getElementById('inpLocation').value = '';
    const h = document.getElementById('dlgHint');
    h.textContent = 'จะบันทึก ' + selectedIds().length + ' ใบ (ทุกใบที่เลือกจะใช้ตำแหน่งเดียวกัน)'; h.style.color = '#6b7280';
    modal.showModal(); document.getElementById('inpLocation').focus();
}

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

    if (!confirm('ยืนยันระบุตำแหน่ง' )) return;

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