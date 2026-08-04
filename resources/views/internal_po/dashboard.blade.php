<!DOCTYPE html>
{{-- resources/views/internal_po/dashboard.blade.php  (ด่าน 1: จัดเสร็จ) --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>รอตรวจสอบการจัด</title>
    <style>
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

        /* ===== Header bar ===== */
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
        .top-banner .user-tag {
            display:flex; align-items:center; gap:4px;
            background:transparent; color:var(--ink);
            border:0; margin-left:auto;
            font-size:13px; font-weight:400;
            padding:0;
        }
        .top-banner .user-tag input {
            border:0; background:transparent; padding:0; margin:0;
            font-family:inherit; font-size:13px; font-weight:700;
            color:var(--ink); width:80px;
        }

        main { padding:20px; background:var(--canvas); }

        /* ===== Filter bar ===== */
        .filter-card {
            border:1px solid var(--border); background:#fafbfd;
            padding:14px 16px; margin-bottom:14px;
        }
        .filter-row { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
        .filter-field { display:flex; flex-direction:column; gap:4px; }
        .filter-field label { font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.3px; }
        input[type="text"],input[type="search"],input[type="date"],select {
            padding:8px 12px; border:1px solid var(--border);
            font-family:inherit; font-size:14px; background:var(--canvas); color:var(--ink);
        }
        input:focus,select:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px var(--primary-light); }
        button {
            padding:8px 20px; border:1px solid transparent; border-radius:6px;
            font-family:inherit; font-weight:600; font-size:15px;
            cursor:pointer; transition:.15s ease;
        }
        .btn-primary { background:var(--primary); color:var(--on-primary); }
        .btn-primary:hover { background:var(--primary-dark); }
        .btn-success { background:var(--success); color:var(--on-primary); }
        .btn-success:hover { background:var(--success-dark); }
        .btn-danger  { background:var(--danger); color:var(--on-primary); }
        .btn-danger:hover { background:var(--danger-dark); }
        .btn-ghost   { background:var(--canvas); color:var(--muted); border-color:var(--border); }
        .btn-ghost:hover { background:#f3f4f6; color:var(--ink); }
        button:disabled { opacity:.4; cursor:not-allowed; }

        /* ===== Table toolbar: summary left, action toolbar right ===== */
        .table-toolbar-row {
            display:flex; justify-content:space-between; align-items:center;
            flex-wrap:wrap; gap:12px; margin-bottom:10px;
        }
        .table-summary { font-size:12px; color:var(--muted); }

        /* ===== Action toolbar (มุมขวาบนของตาราง) ===== */
        .action-toolbar {
            display:flex; align-items:flex-end; gap:10px; flex-wrap:wrap;
            border:1px solid var(--border); background:var(--primary-light);
            padding:10px 14px; margin-left:auto;
        }
        .action-toolbar .selcount {
            font-size:13px; font-weight:700; color:var(--primary-dark);
            padding:0 6px 8px 0;
        }
        #inpSheets { width:70px; text-align:center; }
        .action-toolbar .divider { width:1px; align-self:stretch; background:var(--border); margin:0 4px; }

        /* ===== Table ===== */
        table { width:100%; min-width:820px; border-collapse:collapse; background:var(--canvas); border:1px solid var(--border); overflow:hidden; }
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
        tr.cancelled td { color:var(--danger-dark); background:#fef2f2; }
        .items-cell { max-width:320px; text-align:left; }
        .items-list { display:flex; flex-direction:column; gap:3px; }
        .item-row {
            font-size:13px; color:var(--ink); padding:1px 0; line-height:1.4;
        }
        .item-row .item-qty { font-variant-numeric:tabular-nums; color:var(--muted); font-weight:600; white-space:nowrap; margin-left:6px; }
        .items-count { font-size:11px; color:var(--muted); font-weight:600; margin-bottom:3px; }
        details.items-expand summary { cursor:pointer; color:var(--muted); font-size:11px; list-style:none; font-weight:600; }
        details.items-expand summary::-webkit-details-marker { display:none; }
        details.items-expand[open] summary { margin-bottom:4px; }

        /* เน้น 4 คอลัมน์หลัก: PO ภายใน / SO / รายการสินค้า / จำนวนรวม */
        th.col-key, td.col-key { font-weight:700; font-size:14.5px; color:var(--ink); }
        tr.done td.col-key, tr.cancelled td.col-key { font-weight:700; }
        th.col-minor { font-size:12px; font-weight:700; }
        td.col-minor { font-size:12px; color:var(--muted); font-weight:400; }

        .status-doing { color:var(--warning); font-weight:600; }
        .status-done  { color:var(--success); font-weight:600; }
        a.ref-link { color:var(--primary); font-weight:700; text-decoration:none; }
        a.ref-link:hover { text-decoration:underline; }

        .pagination { margin-top:16px; display:flex; gap:6px; flex-wrap:wrap; }
    </style>
</head>
<body>
<div class="page-frame">
<div class="top-banner">
    <div class="title-group">
        <span class="h1">รอตรวจสอบการจัด</span>
        <span class="sticker">Internal PO</span>
    </div>
</div>
<input type="hidden" id="inpUser" value="{{ $creator }}">
<main>
    {{-- ===== Filter bar ===== --}}
    <form class="filter-card" method="GET" action="{{ url()->current() }}">
        @if ($creator)<input type="hidden" name="create_by" value="{{ $creator }}">@endif
        <div class="filter-row">
            <div class="filter-field">
                <label for="fSONum">ค้นหา SO</label>
                <input type="search" id="fSONum" name="SONum" value="{{ request('SONum') }}" placeholder="เลข SO..." autocomplete="off">
            </div>
            <div class="filter-field">
                <label for="fInternal">ค้นหา PO ภายใน</label>
                <input type="search" id="fInternal" name="internal_id" value="{{ request('internal_id') }}" placeholder="PO ภายใน..." autocomplete="off">
            </div>
            <div class="filter-field">
                <label for="fCust">ค้นหาลูกค้า</label>
                <input type="search" id="fCust" name="customer_name" value="{{ request('customer_name') }}" placeholder="ชื่อลูกค้า..." autocomplete="off">
            </div>
            <div class="filter-field">
                <label for="fStatus">สถานะ</label>
                <select id="fStatus" name="status">
                    <option value="">-- ทั้งหมด --</option>
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-field">
                <label>&nbsp;</label>
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn-primary">ค้นหา</button>
                    @if (request('SONum') || request('internal_id') || request('customer_name') || request('status'))
                        <a href="{{ url()->current() }}{{ $creator ? '?create_by='.urlencode($creator) : '' }}">
                            <button type="button" class="btn-ghost">ล้าง</button>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- ===== Table toolbar: สรุปด้านซ้าย + action toolbar ชิดขวา ===== --}}
    <div class="table-toolbar-row">
        <div class="table-summary">
            รอจัด {{ $statusCounts[\App\Models\internal_po::ST_PENDING] ?? 0 }} /
            แสดง {{ $heads->count() }} จาก {{ $heads->total() }} ใบ (หน้า {{ $heads->currentPage() }}/{{ $heads->lastPage() }})
        </div>
        <div class="action-toolbar" id="actionToolbar" hidden>
            <span class="selcount">เลือกแล้ว <span id="selCount">0</span> ใบ</span>
            <div class="divider"></div>
            <div class="filter-field">
                <label>เครื่องปริ้น</label>
                <select id="selPrinter">
                    <option value="">-- เลือกเครื่องปริ้น --</option>
                    @foreach ($printers as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-field">
                <label>จำนวนสติกเกอร์/ใบ</label>
                <input type="text" id="inpSheets" value="1">
            </div>
            <button type="button" class="btn-success" id="btnMain" onclick="submitFinish()">
                จัดเสร็จ + พิมพ์ (<span id="selCountA">0</span>)
            </button>
            <button type="button" class="btn-danger" id="btnCancel" onclick="submitCancel()">
                ยกเลิก (<span id="selCountB">0</span>)
            </button>
        </div>
    </div>

    <div class="table-scroll">
    <table>
        <thead>
            <tr>
                <th class="center" style="width:44px;"><input type="checkbox" id="chkAll"></th>
                <th class="col-key">PO ภายใน</th>
                <th class="col-key">SO</th>
                <th class="col-key">รายการสินค้า</th>
                <th class="col-key num">จำนวนรวม</th>
                <th>ลูกค้า</th>
                <th class="col-minor">สถานะ</th>
                <th class="col-minor">ผู้ดำเนินการ</th>
                <th class="col-minor">เวลาดำเนินการ</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($heads as $h)
            @php
                $todo   = $h->status === \App\Models\internal_po::ST_PENDING;
                $cancel = $h->status === \App\Models\internal_po::ST_CANCEL;
                // สถานะอื่นๆ ที่ไม่ใช่ pending/cancel ทั้งหมดแสดงเป็น "จัดเสร็จแล้ว"
                $statusLabel = $todo ? $h->status : ($cancel ? $h->status : 'จัดเสร็จแล้ว');
                $statusColor = $todo ? 'inherit' : ($cancel ? $h->status_color : 'var(--success)');
                $cls    = $cancel ? 'cancelled' : (!$todo ? 'done' : '');
                $items  = $h->lines;
                $totalQty = $items->sum('item_quantity');
            @endphp
            <tr class="{{ $cls }}" data-done="{{ $todo ? 0 : 1 }}">
                <td class="center">
                    @if ($todo)<input type="checkbox" class="chkLine" value="{{ $h->internal_id }}">@endif
                </td>
                <td class="col-key"><span class="ref-link">{{ $h->internal_id }}</span></td>
                <td class="col-key">{{ $h->SO_id }}</td>
                <td class="col-key items-cell">
                    @if ($items->count() === 1)
                        {{ $items->first()->item_name }}
                    @elseif ($items->count() <= 5)
                        <div class="items-list">
                            @foreach ($items as $it)
                                <div class="item-row">{{ $it->item_name }}<span class="item-qty">× {{ number_format($it->item_quantity, 2) }}</span></div>
                            @endforeach
                        </div>
                    @else
                        <details class="items-expand">
                            <summary>{{ $items->count() }} รายการ — แตะเพื่อดูทั้งหมด</summary>
                            <div class="items-list">
                                @foreach ($items as $it)
                                    <div class="item-row">{{ $it->item_name }}<span class="item-qty">× {{ number_format($it->item_quantity, 2) }}</span></div>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </td>
                <td class="col-key num">{{ number_format($totalQty, 2) }}</td>
                <td class="cust-cell">{{ $h->customer_name }}</td>
                <td class="col-minor" style="color:{{ $statusColor }};">{{ $statusLabel }}</td>
                <td class="col-minor">{{ $h->pick_by ?: '—' }}</td>
                <td class="col-minor muted">{{ $h->pick_at ? \Carbon\Carbon::parse($h->pick_at)->format('d/m/Y H:i') : '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="9" class="empty">ไม่มีรายการ</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>

    <div class="pagination">
        {{ $heads->onEachSide(1)->links() }}
    </div>
</main>
</div>

<script>
const FINISH_URL = "{{ route('internal_po.pick.submit') }}";
const CANCEL_URL = "{{ route('internal_po.cancel') }}";
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;

const selectedIds = () => Array.from(document.querySelectorAll('.chkLine:checked')).map(c => c.value);
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
</script>
</body>
</html>