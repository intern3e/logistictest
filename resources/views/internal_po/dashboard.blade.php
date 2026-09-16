<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>รอตรวจสอบการจัด</title>
    <style>
        :root{
            --ink:#1e293b; --canvas:#ffffff; --muted:#6b7280; --border:#dcdcdc;
            --primary:#2853d5; --primary-dark:#1e3fa8; --primary-light:#eef2fd;
            --on-primary:#ffffff; --success:#16a34a; --success-dark:#15803d;
            --danger:#dc2626; --danger-dark:#b91c1c; --warning:#ea580c;
            --row-hover:#f0f4ff; --row-done:#f8fafc; --page-bg:#eef2f7;
        }
        * { box-sizing: border-box; margin:0; padding:0; }
        html,body { background:var(--canvas); overflow-x:hidden; max-width:100%; }
        body {
            font-family:'Segoe UI', Tahoma, Arial, sans-serif; font-size:14px;
            color:var(--ink); padding:16px;
        }
        .page-frame { background:var(--canvas); max-width:100%; }
        .table-scroll { overflow-x:auto; -webkit-overflow-scrolling:touch; border:1px solid var(--border); border-radius: 8px; }

        .top-banner {
            background:var(--canvas); color:var(--ink);
            margin:0 -16px; padding:16px 16px 0;
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;
        }
        .top-banner .title-group { display:flex; align-items:center; gap:10px; margin-left:24px; }
        .top-banner .title-group .h1 { font-weight:700; font-size:22px; color:var(--ink); }
        .top-banner .sticker {
            background:var(--primary-light); color:var(--primary); border:1px solid #c7d5fd;
            font-weight:600; font-size:11px;
            padding:4px 12px; text-transform:uppercase; letter-spacing:.3px;
        }
        
        .top-banner .user-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: var(--on-primary);
            padding: 6px 16px;
            border-radius: 50px;
            margin-left: auto;
            margin-right: 28px;
            font-size: 13.5px;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(40, 83, 213, 0.2);
        }
        .top-banner .user-tag .status-dot {
            width: 10px;
            height: 10px;
            background-color: #22c55e;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
        }

        main { padding:20px; background:var(--canvas); }

        .header-toolbar-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 14px;
        }

        .filter-card {
            border: 1px solid var(--border); 
            background: #fafbfd;
            padding: 14px 18px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .filter-row { display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end; }
        .filter-field { display:flex; flex-direction:column; gap:4px; }
        .filter-field label { font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.3px; }
        
        input[type="text"],input[type="search"],input[type="date"],select {
            padding:8px 12px; border:1px solid var(--border);
            font-family:inherit; font-size:14px; background:var(--canvas); color:var(--ink);
            border-radius: 6px;
        }
        input:focus,select:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px var(--primary-light); }
        
        button {
            padding:8px 20px; border:1px solid transparent; border-radius:6px;
            font-family:inherit; font-weight:600; font-size:15px;
            cursor:pointer; transition:.15s ease;
        }
        .btn-success { background:var(--success); color:var(--on-primary); }
        .btn-success:hover { background:var(--success-dark); }
        .btn-danger  { background:var(--danger); color:var(--on-primary); }
        .btn-danger:hover { background:var(--danger-dark); }
        .btn-ghost   { background:var(--canvas); color:var(--muted); border-color:var(--border); padding: 8px 16px; font-size: 14px; border-radius: 6px; }
        .btn-ghost:hover { background:#f3f4f6; color:var(--ink); }
        button:disabled { opacity:.4; cursor:not-allowed; }

        .table-toolbar-row {
            display:flex; justify-content:space-between; align-items:center;
            flex-wrap:wrap; gap:12px; margin-bottom:10px;
        }
        .table-summary { font-size:12px; color:var(--muted); }

        .action-toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .action-toolbar .selcount {
            font-size: 13px;
            font-weight: 700;
            color: var(--ink);
            padding-right: 6px;
        }
        .action-toolbar .divider {
            width: 1px;
            align-self: stretch;
            background: var(--border);
            margin: 0 4px;
        }
        #inpSheets { width: 70px; text-align: center; }

        table {
            width:100%; min-width:720px; border-collapse:collapse;
            background:var(--canvas); border:none;
        }
        th,td {
            border-bottom:1px solid var(--border);
            border-right:1px solid #eef0f3;
            padding:10px 14px; text-align:center; font-size:13px;
            vertical-align:middle;
        }
        th:last-child,td:last-child { border-right:none; }

        thead th {
            background:var(--primary); color:var(--on-primary);
            font-weight:700; font-size:12.5px;
            border-bottom:1px solid var(--primary-dark);
            border-right-color:rgba(255,255,255,.25);
        }
        
        tbody tr td { background: var(--canvas); }
        tbody tr:hover td { background:#f4f7fb; }

        .num { font-variant-numeric:tabular-nums; }
        .cust-cell { text-align:left; }
        .center { text-align:center; }
        .empty { text-align:center; color:var(--muted); padding:32px; font-style:italic; }

        tr.done td { color:#9ca3af; }
        tr.cancelled td { color:#b91c1c; background:#fff5f5 !important; }

        th.col-key, td.col-key { font-weight:600; font-size:13.5px; }
        a.ref-link { color:var(--ink); font-weight:600; text-decoration:none; border-bottom:1px dashed var(--border); }
        a.ref-link:hover { border-bottom-color:var(--ink); }

        .btn-view-items {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px; 
            font-size: 13px; 
            font-weight: 600;
            background: var(--canvas); 
            color: var(--primary);
            border: 1px solid var(--primary); 
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            transition: background 0.15s ease;
        }
        .btn-view-items:hover { background: var(--primary-light); }
        .btn-view-items svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .pagination {
            margin-top:16px; display:flex; align-items:center; justify-content:center;
            gap:6px; flex-wrap:wrap;
        }
        .pagination .page-btn {
            min-width:36px; height:36px; padding:0 10px;
            display:inline-flex; align-items:center; justify-content:center;
            border:1px solid var(--border); background:var(--canvas);
            color:var(--primary); font-weight:600; font-size:13px;
            text-decoration:none; border-radius:6px; transition:.15s ease;
        }
        .pagination .page-btn:hover { border-color:var(--primary); background:var(--primary-light); }
        .pagination .page-btn.active {
            background:var(--primary); color:var(--on-primary);
            border-color:var(--primary); cursor:default;
        }
        .pagination .page-btn.disabled { color:#c3c9d1; cursor:not-allowed; pointer-events:none; }
        .pagination .page-dots { color:var(--muted); padding:0 4px; font-size:13px; user-select:none; }
        .pagination .page-info { width:100%; text-align:center; font-size:12px; color:var(--muted); margin-top:6px; }

        .modal-overlay {
            position:fixed; inset:0; background:rgba(15,23,42,.5);
            display:flex; align-items:center; justify-content:center;
            z-index:1000; padding:16px;
        }
        .modal-overlay[hidden] { display:none; }
        .modal-box {
            background:var(--canvas); width:100%; max-width:680px;
            max-height:85vh; overflow-y:auto; overflow-x:hidden;
            border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,.25);
            display:flex; flex-direction:column;
        }
        .modal-header {
            display:flex; align-items:center; justify-content:space-between; gap:12px;
            padding:16px 22px; background:var(--primary); color:var(--on-primary);
            position:sticky; top:0; flex:0 0 auto;
        }
        .modal-title { font-weight:700; font-size:15px; }
        .modal-close {
            background:transparent; border:none; color:var(--on-primary);
            font-size:22px; line-height:1; padding:0 4px; cursor:pointer;
        }
        .modal-row {
            display:grid; grid-template-columns:1fr 110px; align-items:center;
            gap:12px; padding:12px 22px; border-bottom:1px solid var(--border);
        }
        .modal-row > span { font-size:14px; word-break:break-word; overflow-wrap:anywhere; }
        .modal-row .num { text-align:right; font-variant-numeric:tabular-nums; white-space:nowrap; font-weight:600; }
        .modal-row-head {
            background:#f8fafc; color:var(--muted); font-weight:700;
            font-size:12px; text-transform:uppercase; letter-spacing:.3px;
        }
        .modal-empty { text-align:center; color:var(--muted); padding:24px; font-style:italic; }
    </style>
</head>
<body>
<div class="page-frame">
<div class="top-banner">
    <div class="title-group">
        <span class="h1">รอตรวจสอบการจัด</span>
        <span class="sticker">Internal PO</span>
    </div>
    <div class="user-tag">
        <span class="status-dot"></span>
        <span>ผู้ใช้งาน: {{ $operatorName }}</span>
    </div>
</div>
<input type="hidden" id="inpUser" value="{{ $operatorName }}">
<main>
    <div class="header-toolbar-container">
        <form class="filter-card" id="filterForm" method="GET" action="{{ url()->current() }}">
            <div class="filter-row">
                <div class="filter-field">
                    <input type="search" id="fSONum" name="SONum" value="{{ request('SONum') }}" placeholder="เลข SO..." autocomplete="off">
                </div>
                <div class="filter-field">
                    <input type="search" id="fInternal" name="internal_id" value="{{ request('internal_id') }}" placeholder="PO ภายใน..." autocomplete="off">
                </div>
                <div class="filter-field">
                    <select id="fStatus" name="status">
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected($selectedStatus === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @if (request('SONum') || request('internal_id') || request('customer_name') || ($selectedStatus !== \App\Models\internal_po::ST_PENDING))
                    <div class="filter-field">
                        <a href="{{ url()->current() }}">
                            <button type="button" class="btn-ghost">ล้างค่า</button>
                        </a>
                    </div>
                @endif
            </div>

            <div class="action-toolbar" id="actionToolbar" hidden>
                <span class="selcount">เลือกแล้ว <span id="selCount">0</span> ใบ</span>
                <div class="divider"></div>
                <div class="filter-field">
                    <select id="selPrinter">
                        <option value="">-- เลือกเครื่องปริ้น --</option>
                        @foreach ($printers as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field">
                    <input type="text" id="inpSheets" value="1">
                </div>
                <button type="button" class="btn-success" id="btnMain" onclick="submitFinish()">
                    จัดเสร็จ + พิมพ์ (<span id="selCountA">0</span>)
                </button>
                <button type="button" class="btn-danger" id="btnCancel" onclick="submitCancel()">
                    ยกเลิก (<span id="selCountB">0</span>)
                </button>
            </div>
        </form>
    </div>

    <div class="table-toolbar-row">
        <div class="table-summary">
            รอจัด {{ $statusCounts[\App\Models\internal_po::ST_PENDING] ?? 0 }} /
            แสดง {{ $heads->count() }} จาก {{ $heads->total() }} ใบ (หน้า {{ $heads->currentPage() }}/{{ $heads->lastPage() }})
        </div>
    </div>

    <div class="table-scroll">
    <table>
        <thead>
            <tr>
                <th class="center" style="width:44px;"><input type="checkbox" id="chkAll"></th>
                <th class="col-key">PO ภายใน</th>
                <th class="col-key">SO</th>
                <th class="col-key">รหัสสินค้า</th>
                <th>ชื่อสินค้า</th>
                <th class="center" style="width:80px;">จำนวน</th>
                <th>ลูกค้า</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($heads as $h)
            @php
                $todo     = $h->status === \App\Models\internal_po::ST_PENDING;
                $cancel   = $h->status === \App\Models\internal_po::ST_CANCEL;
                $cls      = $cancel ? 'cancelled' : (!$todo ? 'done' : '');
                $lines    = $h->lines ?? collect();
            @endphp
            @forelse ($lines as $line)
                @php
                    $lineId   = $line->id ?? null;                 // ระบบใหม่มี id ราย line / ของเก่า = null
                    $picked   = !empty($line->picked_at);
                    $chkVal   = $lineId ? ('line:' . $lineId) : ('po:' . $h->internal_id);  // ของเก่าจัดทั้ง PO
                    $lineCls  = $cancel ? 'cancelled' : (($picked || !$todo) ? 'done' : '');
                @endphp
                <tr class="{{ $lineCls }}" data-internal-id="{{ $h->internal_id }}">
                    <td class="center">
                        @if ($todo && !$picked)
                            <input type="checkbox" class="chkLine" value="{{ $chkVal }}">
                        @elseif ($picked)
                            <input type="checkbox" checked disabled title="จัดแล้ว">
                        @endif
                    </td>
                    <td class="col-key">@if ($loop->first)<span class="ref-link">{{ $h->internal_id }}</span>@endif</td>
                    <td class="col-key">@if ($loop->first){{ $h->SO_id }}@endif</td>
                    <td class="col-key">{{ $line->item_id ?: '—' }}</td>
                    <td>{{ $line->item_name }}</td>
                    <td class="center">{{ rtrim(rtrim(number_format((float) $line->item_quantity, 2), '0'), '.') }}</td>
                    <td class="cust-cell">@if ($loop->first){{ $h->customer_name }}@endif</td>
                </tr>
            @empty
                <tr class="{{ $cls }}" data-internal-id="{{ $h->internal_id }}">
                    <td class="center"></td>
                    <td class="col-key"><span class="ref-link">{{ $h->internal_id }}</span></td>
                    <td class="col-key">{{ $h->SO_id }}</td>
                    <td class="col-key" colspan="3" style="color:#999;">— ไม่มีไส้ใน —</td>
                    <td class="cust-cell">{{ $h->customer_name }}</td>
                </tr>
            @endforelse
        @empty
            <tr><td colspan="7" class="empty">ไม่มีรายการ</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>

    <div class="pagination">
        @if ($heads->hasPages())
            @if ($heads->onFirstPage())
                <span class="page-btn disabled">« ก่อนหน้า</span>
            @else
                <a class="page-btn" href="{{ $heads->previousPageUrl() }}">« ก่อนหน้า</a>
            @endif

            @php
                $current = $heads->currentPage();
                $last    = $heads->lastPage();
                $window  = 1;
            @endphp

            @if ($current - $window > 1)
                <a class="page-btn" href="{{ $heads->url(1) }}">1</a>
                @if ($current - $window > 2)
                    <span class="page-dots">…</span>
                @endif
            @endif

            @for ($p = max(1, $current - $window); $p <= min($last, $current + $window); $p++)
                @if ($p === $current)
                    <span class="page-btn active">{{ $p }}</span>
                @else
                    <a class="page-btn" href="{{ $heads->url($p) }}">{{ $p }}</a>
                @endif
            @endfor

            @if ($current + $window < $last)
                @if ($current + $window < $last - 1)
                    <span class="page-dots">…</span>
                @endif
                <a class="page-btn" href="{{ $heads->url($last) }}">{{ $last }}</a>
            @endif

            @if ($heads->hasMorePages())
                <a class="page-btn" href="{{ $heads->nextPageUrl() }}">ถัดไป »</a>
            @else
                <span class="page-btn disabled">ถัดไป »</span>
            @endif

            <div class="page-info">หน้า {{ $heads->currentPage() }} / {{ $heads->lastPage() }} (ทั้งหมด {{ $heads->total() }} ใบ)</div>
        @endif
    </div>
</main>
</div>

<div class="modal-overlay" id="itemsModal" hidden>
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title" id="modalTitle">รายการสินค้า</span>
            <button type="button" class="modal-close" onclick="closeItemsModal()" aria-label="ปิด">&times;</button>
        </div>
        <div class="modal-row modal-row-head">
            <span>ชื่อสินค้า</span>
            <span class="num">จำนวน</span>
        </div>
        <div id="modalBody"></div>
    </div>
</div>

<script>
const FINISH_URL = "{{ route('internal_po.pick.submit') }}";
const CANCEL_URL = "{{ route('internal_po.cancel') }}";
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;

const PO_ITEMS = {
    @foreach ($heads as $h)
        "{{ $h->internal_id }}": @json($h->lines->map(fn ($it) => ['name' => $it->item_name, 'qty' => (float) $it->item_quantity])),
    @endforeach
};

// dedupe: ของเก่าหลายไส้ในใช้ค่าเดียวกัน (po:<id>) — นับ/ส่งครั้งเดียว
const selectedIds = () => Array.from(new Set(Array.from(document.querySelectorAll('.chkLine:checked')).map(c => c.value)));
const currentUser = () => document.getElementById('inpUser').value.trim();

function refreshBtn() {
    const n = selectedIds().length;
    document.getElementById('selCount').textContent  = n;
    document.getElementById('selCountA').textContent = n;
    document.getElementById('selCountB').textContent = n;
    document.getElementById('actionToolbar').hidden = (n === 0);
}
document.getElementById('chkAll').addEventListener('change', function () {
    document.querySelectorAll('.chkLine').forEach(c => c.checked = this.checked);
    refreshBtn();
});
document.querySelectorAll('.chkLine').forEach(c => c.addEventListener('change', refreshBtn));

async function post(url, body) {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF },
        body: JSON.stringify(body)
    });
    if (res.status === 419) {
        alert('เซสชันหมดอายุ กรุณารีเฟรชหน้าแล้วลองใหม่');
        window.location.reload();
        throw new Error('CSRF token expired (419)');
    }
    return { res, data: await res.json() };
}

async function submitFinish() {
    if (!currentUser())        { alert('กรุณาระบุชื่อผู้ดำเนินการ'); document.getElementById('inpUser').focus(); return; }
    if (!selectedIds().length) { alert('ยังไม่ได้เลือกรายการ'); return; }

    const printer = document.getElementById('selPrinter').value;
    if (!printer) { alert('กรุณาเลือกเครื่องปริ้น'); document.getElementById('selPrinter').focus(); return; }

    const sheets = parseInt(document.getElementById('inpSheets').value, 10);
    if (!sheets || sheets < 1) { alert('จำนวนแผ่นต้องมากกว่า 0'); document.getElementById('inpSheets').focus(); return; }

    if (!confirm('ยืนยันจัดเสร็จ')) return;

    const btn = document.getElementById('btnMain'); btn.disabled = true;
    try {
        const { res, data } = await post(FINISH_URL, {
            ids: selectedIds(), user: currentUser(),
            printer: printer, print_sheets: sheets
        });
        if (res.ok && data.ok) { alert(data.message); window.location.reload(); }
        else { alert(data.message || 'บันทึกไม่สำเร็จ'); btn.disabled = false; }
    } catch (e) { console.error(e); alert('เกิดข้อผิดพลาด'); btn.disabled = false; }
}

async function submitCancel() {
    if (!currentUser())        { alert('กรุณาระบุชื่อผู้ดำเนินการ'); return; }
    const ids = selectedIds();
    if (!ids.length)           { alert('ยังไม่ได้เลือกรายการ'); return; }
    if (!confirm('ยกเลิก ' + ids.length + ' ใบ?')) return;

    const btn = document.getElementById('btnCancel'); btn.disabled = true;
    try {
        const { res, data } = await post(CANCEL_URL, { ids: ids, user: currentUser() });
        if (res.ok && data.ok) { alert(data.message); window.location.reload(); }
        else { alert(data.message || 'ยกเลิกไม่สำเร็จ'); btn.disabled = false; }
    } catch (e) { console.error(e); alert('เกิดข้อผิดพลาด'); btn.disabled = false; }
}

function openItemsModal(internalId) {
    const items = PO_ITEMS[internalId] || [];
    document.getElementById('modalTitle').textContent = 'รายการสินค้า - PO ' + internalId;

    const body = document.getElementById('modalBody');
    body.innerHTML = '';

    if (items.length === 0) {
        const empty = document.createElement('div');
        empty.className = 'modal-empty';
        empty.textContent = 'ไม่มีรายการ';
        body.appendChild(empty);
    } else {
        items.forEach(function (it) {
            const row = document.createElement('div');
            row.className = 'modal-row';

            const name = document.createElement('span');
            name.textContent = it.name;

            const qty = document.createElement('span');
            qty.className = 'num';
            qty.textContent = Number(it.qty).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            row.appendChild(name);
            row.appendChild(qty);
            body.appendChild(row);
        });
    }

    document.getElementById('itemsModal').hidden = false;
}
function closeItemsModal() {
    document.getElementById('itemsModal').hidden = true;
}
document.getElementById('itemsModal').addEventListener('click', function (e) {
    if (e.target === this) closeItemsModal();
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeItemsModal();
});
</script>
<script>
(function () {
    const form = document.getElementById('filterForm');
    if (!form) return;

    const DEBOUNCE_MS = 400;
    let debounceTimer = null;

    function submitNow() {
        clearTimeout(debounceTimer);
        form.submit();
    }

    function submitDebounced() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(submitNow, DEBOUNCE_MS);
    }

    // เจาะจงเฉพาะ .filter-row เท่านั้น ไม่ให้ไปจับ #selPrinter / #inpSheets
    // ที่อยู่ใน .action-toolbar (คนละหน้าที่กัน ไม่ควร auto-submit ฟอร์ม)
    form.querySelectorAll('.filter-row input[type="search"], .filter-row input[type="text"]').forEach(function (el) {
        el.addEventListener('input', submitDebounced);
    });

    form.querySelectorAll('.filter-row select').forEach(function (el) {
        el.addEventListener('change', submitNow);
    });

    form.addEventListener('submit', function () {
        clearTimeout(debounceTimer);
    });
})();
</script>
</body>
</html>