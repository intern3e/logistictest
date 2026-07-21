<!DOCTYPE html>
{{-- resources/views/internal_po/checkout.blade.php  (ด่าน 3: ของออก) --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ของออก (ด่าน 3)</title>
    <style>
        * { box-sizing: border-box; }
        body { margin:0; padding:20px; font-family:"Segoe UI",Tahoma,sans-serif; font-size:14px; color:#212529; background:#f8f9fa; }
        h1 { font-size:20px; margin:0 0 12px; }
        .toolbar { display:flex; gap:8px; margin-bottom:12px; flex-wrap:wrap; align-items:center; }
        input[type="text"],input[type="search"] { padding:6px 10px; border:1px solid #ced4da; border-radius:4px; font:inherit; }
        input:focus { outline:2px solid #4dabf7; outline-offset:-1px; }
        button { padding:6px 14px; border:0; border-radius:4px; font:inherit; cursor:pointer; }
        .btn-primary { background:#1971c2; color:#fff; }
        .btn-success { background:#2f9e44; color:#fff; }
        .btn-ghost   { background:#e9ecef; color:#495057; }
        button:disabled { opacity:.5; cursor:not-allowed; }
        table { width:100%; border-collapse:collapse; background:#fff; }
        caption { text-align:left; padding:8px 0; color:#868e96; font-size:12px; }
        th,td { border:1px solid #dee2e6; padding:8px 10px; text-align:left; }
        thead th { background:#2f9e44; color:#fff; font-weight:600; }
        tbody tr:hover { background:#f1f3f5; }
        .num { text-align:right; font-variant-numeric:tabular-nums; }
        .center { text-align:center; }
        .empty { text-align:center; color:#adb5bd; padding:24px; }
        .muted { font-size:12px; color:#868e96; }
        tr.done td { color:#adb5bd; font-weight:300; background:#fcfcfd; }
        tr.done:hover td { background:#f8f9fa; }
        .actionbar { margin-top:14px; display:flex; gap:8px; }
        .chips { display:flex; gap:6px; flex-wrap:wrap; }
        .chip { padding:3px 9px; border:1px solid #ced4da; border-radius:12px; background:#f8f9fa; font-size:12px; cursor:pointer; }
        .chip:hover { background:#e7f5ff; border-color:#4dabf7; }
        a.chip { text-decoration:none; color:inherit; }
        .chip.active { background:#1971c2; color:#fff; border-color:#1971c2; }
    </style>
</head>
<body>
@php $nav = $creator ? ['create_by' => $creator] : []; @endphp
<main>
    <h1>เช็คเอาต์ / ของออก (ด่าน 3)</h1>

    <nav class="chips" style="margin-bottom:12px;">
        <a class="chip"        href="{{ route('internal_po.pick', $nav) }}">1) จัดเสร็จ</a>
        <a class="chip"        href="{{ route('internal_po.location', $nav) }}">2) ระบุตำแหน่ง</a>
        <a class="chip active" href="{{ route('internal_po.checkout', $nav) }}">3) ของออก</a>
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

        <label class="chip" style="cursor:default;">
            ผู้ดำเนินการ:&nbsp;<input type="text" id="inpUser" value="{{ $creator }}" placeholder="ชื่อผู้กด"
                style="border:0;background:transparent;width:120px;padding:0;">
        </label>

        <label class="muted" style="margin-left:auto;">
            <input type="checkbox" id="chkHideDone" checked> ซ่อนที่ทำแล้ว
        </label>
    </form>

    <table>
        <caption>
            รอเอาออก {{ $lines->where('status', \App\Models\internal_poline::ST_STORED)->count() }} /
            แสดง {{ $lines->count() }} รายการ
        </caption>
        <thead>
            <tr>
                <th class="center" style="width:44px;"><input type="checkbox" id="chkAll"></th>
                <th>PO ภายใน</th><th>SO</th><th>ชื่อสินค้า</th><th class="num">จำนวน</th>
                <th>ลูกค้า</th><th>กล่อง</th><th>ระบุที่โดย</th><th>เวลาระบุ</th><th>สถานะ</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($lines as $l)
            @php
                $todo = $l->status === \App\Models\internal_poline::ST_STORED;
                $cls  = $todo ? '' : 'done';
            @endphp
            <tr class="{{ $cls }}" data-done="{{ $todo ? 0 : 1 }}">
                <td class="center">
                    @if ($todo)<input type="checkbox" class="chkLine" value="{{ $l->id }}">@endif
                </td>
                <td>{{ $l->internal_id }}</td>
                <td>{{ $l->SO_id }}</td>
                <td>{{ $l->item_name }}</td>
                <td class="num">{{ number_format($l->item_quantity, 2) }}</td>
                <td>{{ optional($heads->get($l->internal_id))->customer_name }}</td>
                <td>{{ $l->item_location ?: '—' }}</td>
                <td>{{ $l->location_by ?: '—' }}</td>
                <td class="muted">{{ $l->location_at ? \Carbon\Carbon::parse($l->location_at)->format('d/m/Y H:i') : '—' }}</td>
                <td style="color:{{ $todo ? 'inherit' : $l->status_color }};">{{ $l->status }}</td>
            </tr>
        @empty
            <tr><td colspan="10" class="empty">ไม่มีรายการ</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="actionbar">
        <button type="button" class="btn-success" id="btnMain" hidden onclick="submitCheckout()">
            ของออก (<span id="selCount">0</span>)
        </button>
    </div>
</main>

<script>
const SUBMIT_URL = "{{ route('internal_po.checkout.submit') }}";
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;

const selectedIds = () => Array.from(document.querySelectorAll('.chkLine:checked')).map(c => parseInt(c.value));
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
    if (!confirm('ยืนยันของออก ' + ids.length + ' รายการ?')) return;

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