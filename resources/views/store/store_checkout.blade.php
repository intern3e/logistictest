<!DOCTYPE html>
{{-- resources/views/store/store_checkout.blade.php  (ด่าน 3: ของออก - เฉพาะภายใน) --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ของออก (ด่าน 3)</title>
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
        .chips { display:flex; gap:6px; flex-wrap:wrap; }
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
        .badge-source {
            font-size:10px; font-weight:700; padding:3px 10px; border-radius:20px;
            text-transform:uppercase;
        }
        .badge-in { background:var(--primary-light); color:var(--primary-dark); border:1px solid var(--primary); }
        .badge-out { background:#fff7ed; color:#c2410c; border:1px solid var(--warning); }
    </style>
</head>
<body>
<div class="page-frame">
<div class="top-banner">
    <span>เช็คเอาต์ / ของออก (ด่าน 3)</span>
    <span class="sticker">Store</span>
</div>
@php $nav = $creator ? ['create_by' => $creator] : []; @endphp
<main>
    <nav class="chips" style="margin-bottom:14px;">
        <a class="chip {{ request()->routeIs('store.location') ? 'active' : '' }}" href="{{ route('store.location', $nav) }}">2) ระบุตำแหน่ง</a>
        <a class="chip {{ request()->routeIs('store.checkout') ? 'active' : '' }}" href="{{ route('store.checkout', $nav) }}">3) ของออก</a>
    </nav>

    <p class="muted" style="margin:-6px 0 14px;">
        หมายเหตุ: หน้านี้รวมทั้ง<strong>ภายใน</strong> (internal_po) และ<strong>ภายนอก</strong> (PoReceive จาก mobile) —
        รายการภายนอกไม่มีชื่อลูกค้าบันทึกไว้ในระบบ จึงแสดง — แทน
    </p>

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
            รอเอาออก {{ $heads->where('todo', true)->count() }} /
            แสดง {{ $heads->count() }} ใบ
        </caption>
        <thead>
            <tr>
                <th class="center" style="width:44px;"><input type="checkbox" id="chkAll"></th>
                <th>ประเภท</th><th>PO ภายใน</th><th>SO</th><th>รายการสินค้า</th><th class="num">จำนวนรวม</th>
                <th>ลูกค้า</th><th>ที่เก็บ</th><th>ระบุที่โดย</th><th>เวลาระบุ</th><th>สถานะ</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($heads as $h)
            @php
                $todo     = $h->todo;
                $cls      = $todo ? '' : 'done';
                $items    = $h->items;
                $totalQty = $items->sum('item_quantity');
                $isIn     = $h->type === 'internal';
            @endphp
            <tr class="{{ $cls }}" data-done="{{ $todo ? 0 : 1 }}">
                <td class="center">
                    @if ($todo)<input type="checkbox" class="chkLine" value="{{ $h->type }}:{{ $h->id }}">@endif
                </td>
                <td>
                    @if ($isIn)
                        <span class="badge-source badge-in">ภายใน</span>
                    @else
                        <span class="badge-source badge-out">ภายนอก</span>
                    @endif
                </td>
                <td>{{ $h->id }}</td>
                <td>{{ $h->so_id }}</td>
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
                <td>{{ $h->customer_name ?: '—' }}</td>
                <td>{{ $h->location ?: '—' }}</td>
                <td>{{ $h->done_by ?: '—' }}</td>
                <td class="muted">{{ $h->done_at ? \Carbon\Carbon::parse($h->done_at)->format('d/m/Y H:i') : '—' }}</td>
                <td style="color:{{ $todo ? 'inherit' : $h->status_color }};">{{ $h->status }}</td>
            </tr>
        @empty
            <tr><td colspan="11" class="empty">ไม่มีรายการ</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="actionbar">
        <button type="button" class="btn-success" id="btnMain" hidden onclick="submitCheckout()">
            ของออก (<span id="selCount">0</span>)
        </button>
    </div>
</main>
</div>

<script>
const SUBMIT_URL = "{{ route('store.checkout.submit') }}";
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;

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

async function submitCheckout() {
    if (!currentUser())        { alert('กรุณาระบุชื่อผู้ดำเนินการ'); document.getElementById('inpUser').focus(); return; }
    const ids = selectedIds();
    if (!ids.length)           { alert('ยังไม่ได้เลือกรายการ'); return; }
    if (!confirm('ยืนยันของออก ' + ids.length + ' ใบ?')) return;

    const btn = document.getElementById('btnMain'); btn.disabled = true;
    try {
        const res = await fetch(SUBMIT_URL, {
            method: 'POST',
            headers: { 'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF },
            body: JSON.stringify({ ids: ids, user: currentUser() })
        });
        const data = await res.json();
        if (res.ok && data.ok) { alert(data.message); window.location.reload(); }
        else { alert(data.message || 'บันทึกไม่สำเร็จ'); btn.disabled = false; }
    } catch (e) { console.error(e); alert('เกิดข้อผิดพลาด'); btn.disabled = false; }
}
</script>
</body>
</html>