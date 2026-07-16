<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PO ภายใน</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 20px;
            font-family: "Segoe UI", Tahoma, sans-serif;
            font-size: 14px; color: #212529; background: #f8f9fa;
        }
        h1 { font-size: 20px; margin: 0 0 16px; }

        .toolbar { display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap; align-items: center; }
        input[type="text"], input[type="search"] {
            padding: 6px 10px; border: 1px solid #ced4da; border-radius: 4px; font: inherit;
        }
        input:focus { outline: 2px solid #4dabf7; outline-offset: -1px; }

        button { padding: 6px 14px; border: 0; border-radius: 4px; font: inherit; cursor: pointer; }
        .btn-primary { background: #1971c2; color: #fff; }
        .btn-success { background: #2f9e44; color: #fff; }
        .btn-danger  { background: #e03131; color: #fff; }
        .btn-ghost   { background: #e9ecef; color: #495057; }
        button:disabled { opacity: .5; cursor: not-allowed; }

        table { width: 100%; border-collapse: collapse; background: #fff; }
        caption { text-align: left; padding: 8px 0; color: #868e96; font-size: 12px; }
        th, td { border: 1px solid #dee2e6; padding: 8px 10px; text-align: left; }
        thead th { background: #2f9e44; color: #fff; font-weight: 600; }
        tbody tr:hover { background: #f1f3f5; }
        .num { text-align: right; font-variant-numeric: tabular-nums; }
        .center { text-align: center; }
        .empty { text-align: center; color: #adb5bd; padding: 24px; }
        .muted { font-size: 12px; color: #868e96; }

        tr.done td { color: #adb5bd; font-weight: 300; background: #fcfcfd; }
        tr.done:hover td { background: #f8f9fa; }

        tr.cancelled td { color: #adb5bd; font-weight: 300; background: #fff5f5; }
        tr.cancelled td:nth-child(4) { text-decoration: line-through; }
        tr.cancelled:hover td { background: #ffe3e3; }

        .actionbar { margin-top: 14px; display: flex; gap: 8px; }

        dialog { border: 0; border-radius: 8px; padding: 20px; width: 380px; box-shadow: 0 8px 32px rgba(0,0,0,.2); }
        dialog::backdrop { background: rgba(0,0,0,.45); }
        dialog h2 { font-size: 16px; margin: 0 0 12px; }
        dialog label { display: block; margin-bottom: 4px; font-weight: 600; }
        dialog input { width: 100%; }
        .dialog-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
        .hint { font-size: 12px; color: #868e96; margin-top: 4px; min-height: 16px; }
        .chips { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px; }
        .chip {
            padding: 3px 9px; border: 1px solid #ced4da; border-radius: 12px;
            background: #f8f9fa; font-size: 12px; cursor: pointer;
        }
        .chip:hover { background: #e7f5ff; border-color: #4dabf7; }
    </style>
</head>
<body>

<main>
     <span class="chip" style="cursor:default;">ผู้ใช้: <b>{{ $creator }}</b></span>

    <form class="toolbar" method="GET" action="{{ route('internal_po.dashboard') }}">
        <input type="hidden" name="create_by" value="{{ $creator }}">

        <input type="search" name="SONum" value="{{ request('SONum') }}" placeholder="ค้นหา SO..." autocomplete="off">
        <button type="submit" class="btn-primary">ค้นหา</button>

        @if (request('SONum'))
            <a href="{{ route('internal_po.dashboard', ['create_by' => $creator]) }}">
                <button type="button" class="btn-ghost">ล้าง</button>
            </a>
        @endif

        <span class="chip" style="cursor:default;">ผู้สร้าง: <b>{{ $creator }}</b></span>

        <label class="muted" style="margin-left:auto;">
            <input type="checkbox" id="chkHideDone"> ซ่อนที่ดำเนินการแล้ว
        </label>
    </form>

    <table>
        <caption>
            รอจัด {{ $lines->where('status', \App\Models\internal_poline::ST_PENDING)->count() }} /
            ทั้งหมด {{ $lines->count() }} รายการ
        </caption>
        <thead>
            <tr>
                <th class="center" style="width:44px;"><input type="checkbox" id="chkAll" aria-label="เลือกทั้งหมด"></th>
                <th>PO ภายใน</th>
                <th>SO</th>
                <th>ชื่อสินค้า</th>
                <th class="num">จำนวน</th>
                <th>ลูกค้า</th>
                <th>กล่อง</th>
                <th>สถานะ</th>
                <th>ผู้ดำเนินการ</th>
                <th>เวลาดำเนินการ</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($lines as $l)
            @php
                $pending = $l->status === \App\Models\internal_poline::ST_PENDING;
                $cancel  = $l->status === \App\Models\internal_poline::ST_CANCEL;
                $cls     = $cancel ? 'cancelled' : (!$pending ? 'done' : '');
            @endphp
            <tr class="{{ $cls }}" data-done="{{ $pending ? 0 : 1 }}">
                <td class="center">
                    @if ($pending)
                        <input type="checkbox" class="chkLine" value="{{ $l->id }}" aria-label="เลือก {{ $l->item_name }}">
                    @endif
                </td>
                <td>{{ $l->internal_id }}</td>
                <td>{{ $l->SO_id }}</td>
                <td>{{ $l->item_name }}</td>
                <td class="num">{{ number_format($l->item_quantity, 2) }}</td>
                <td>{{ optional($heads->get($l->internal_id))->customer_name }}</td>
                <td>{{ $l->item_location ?: '—' }}</td>
                <td style="color:{{ $pending ? 'inherit' : $l->status_color }};">{{ $l->status }}</td>
                <td>{{ $l->summit_by ?: '—' }}</td>
                <td class="muted">
                    {{ $l->timestamp ? \Carbon\Carbon::parse($l->timestamp)->format('d/m/Y H:i') : '—' }}
                </td>
            </tr>
        @empty
            <tr><td colspan="10" class="empty">ไม่มีรายการ</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="actionbar">
        <button type="button" class="btn-success" id="btnFinish" hidden onclick="openFinishModal()">
            จัดเสร็จ (<span id="selCount">0</span>)
        </button>
        <button type="button" class="btn-danger" id="btnCancel" hidden onclick="submitCancel()">
            ยกเลิก (<span id="selCount2">0</span>)
        </button>
    </div>
</main>

<dialog id="finishModal">
    <h2>จัดเสร็จ</h2>

    <label for="inpLocation">อยู่กล่อง</label>
    <input type="text" id="inpLocation" list="locHistory" placeholder="เช่น A-01" autocomplete="off" maxlength="100">
    <datalist id="locHistory">
        @foreach ($locations as $loc)
            <option value="{{ $loc }}"></option>
        @endforeach
    </datalist>
    <p class="hint" id="dlgHint"></p>

    @if ($locations->count())
        <div class="muted">ใช้ล่าสุด</div>
        <div class="chips">
            @foreach ($locations->take(6) as $loc)
                <span class="chip" onclick="pickLoc(this)">{{ $loc }}</span>
            @endforeach
        </div>
    @endif

    <div class="dialog-actions">
        <button type="button" class="btn-ghost" onclick="document.getElementById('finishModal').close()">ยกเลิก</button>
        <button type="button" class="btn-primary" id="btnOk" onclick="submitFinish()">OK</button>
    </div>
</dialog>

<script>
const CREATOR = "{{ $creator }}";
const FINISH_URL = "{{ route('internal_po.finish') }}";
const CANCEL_URL = "{{ route('internal_po.cancel') }}";
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
const modal      = document.getElementById('finishModal');

function selectedIds() {
    return Array.from(document.querySelectorAll('.chkLine:checked')).map(c => parseInt(c.value));
}

function refreshBtn() {
    const n = selectedIds().length;
    document.getElementById('selCount').textContent  = n;
    document.getElementById('selCount2').textContent = n;
    document.getElementById('btnFinish').hidden = (n === 0);
    document.getElementById('btnCancel').hidden = (n === 0);
}

document.getElementById('chkAll').addEventListener('change', function () {
    document.querySelectorAll('.chkLine').forEach(c => c.checked = this.checked);
    refreshBtn();
});
document.querySelectorAll('.chkLine').forEach(c => c.addEventListener('change', refreshBtn));

document.getElementById('chkHideDone').addEventListener('change', function () {
    document.querySelectorAll('tr[data-done="1"]').forEach(tr => tr.hidden = this.checked);
});

function pickLoc(el) {
    const inp = document.getElementById('inpLocation');
    inp.value = el.textContent.trim();
    inp.focus();
}

function openFinishModal() {
    document.getElementById('inpLocation').value = '';
    const hint = document.getElementById('dlgHint');
    hint.textContent = 'จะบันทึก ' + selectedIds().length + ' รายการ';
    hint.style.color = '#868e96';
    modal.showModal();
    document.getElementById('inpLocation').focus();
}

document.getElementById('inpLocation').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); submitFinish(); }
});

async function submitFinish() {
    const ids  = selectedIds();
    const box  = document.getElementById('inpLocation').value.trim();
    const hint = document.getElementById('dlgHint');

    if (!ids.length) { alert('ยังไม่ได้เลือกรายการ'); return; }
    if (!box) {
        hint.textContent = 'กรุณาระบุกล่อง';
        hint.style.color = '#e03131';
        document.getElementById('inpLocation').focus();
        return;
    }

    const btn = document.getElementById('btnOk');
    btn.disabled = true; btn.textContent = 'กำลังบันทึก...';

    try {
        const res = await fetch(FINISH_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify({ ids: ids, location: box, user: CREATOR })
        });
        const data = await res.json();

        if (res.ok && data.ok) {
            alert(data.message);
            window.location.reload();
        } else {
            alert(data.message || 'บันทึกไม่สำเร็จ');
            btn.disabled = false; btn.textContent = 'OK';
        }
    } catch (e) {
        console.error(e);
        alert('เกิดข้อผิดพลาด');
        btn.disabled = false; btn.textContent = 'OK';
    }
}

async function submitCancel() {
    const ids = selectedIds();
    if (!ids.length) { alert('ยังไม่ได้เลือกรายการ'); return; }
    if (!confirm('ยกเลิก ' + ids.length + ' รายการ?')) return;

    const btn = document.getElementById('btnCancel');
    btn.disabled = true;

    try {
        const res = await fetch(CANCEL_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify({ ids: ids, user: CREATOR })
        });
        const data = await res.json();

        if (res.ok && data.ok) {
            alert(data.message);
            window.location.reload();
        } else {
            alert(data.message || 'ยกเลิกไม่สำเร็จ');
            btn.disabled = false;
        }
    } catch (e) {
        console.error(e);
        alert('เกิดข้อผิดพลาด');
        btn.disabled = false;
    }
}
</script>

</body>
</html>