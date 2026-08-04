<!DOCTYPE html>
{{-- resources/views/store/store_checkout.blade.php  (ด่าน 3: ของออก - internal + external + legacy) --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ของออก</title>
    <style>
        .actionbar { margin-top:16px; display:flex; gap:8px; align-items:center; justify-content:center; flex-wrap:wrap; }
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
        input[type="text"],input[type="search"],input[type="date"] {
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
        table { width:100%; min-width:1100px; border-collapse:collapse; background:var(--canvas); border:1px solid var(--border); overflow:hidden; }
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
        tr.so-group-start td { border-top:2px solid var(--primary-light); }
        tr.so-selected td { background:#fef9e7; }
        tr.so-selected:hover td { background:#fef3c7; }
        tr.so-hover td { background:#eff6ff; }
        tr.so-hover.so-selected td { background:#fef3c7; }
        .actionbar { margin-top:16px; display:flex; gap:8px; align-items:center; justify-content:space-between; flex-wrap:wrap; }
        .chips { display:flex; gap:6px; flex-wrap:wrap; }
        .chip {
            padding:6px 14px; border:1px solid var(--border); background:var(--canvas); color:var(--muted);
            font-weight:600; font-size:12px;
            cursor:pointer; text-decoration:none; display:inline-block; transition:.15s ease;
        }
        a.chip { text-decoration:none; }
        .chip:hover { border-color:var(--primary); color:var(--primary); }
        .chip.active { background:var(--primary); color:var(--on-primary); border-color:var(--primary); }
        .items-cell { max-width:320px; text-align:left; }
        .items-cell .more { color:var(--muted); }
        details.items-expand summary { cursor:pointer; color:var(--primary); font-size:13px; list-style:none; font-weight:600; text-align:left; }
        details.items-expand summary::-webkit-details-marker { display:none; }
        details.items-expand[open] summary { margin-bottom:6px; }
        .subline { font-size:12px; color:#475569; padding:3px 0 3px 4px; text-align:left; }
        .badge-source {
            font-size:10px; font-weight:700; padding:3px 10px;
            text-transform:uppercase;
        }
        .badge-in     { background:var(--primary-light); color:var(--primary-dark); border:1px solid var(--primary); }
        .badge-out    { background:#fff7ed; color:#c2410c; border:1px solid var(--warning); }
        .badge-legacy { background:#f3e8ff; color:#7e22ce; border:1px solid #a855f7; }

        /* pagination */
        .pager { display:flex; gap:4px; align-items:center; flex-wrap:wrap; }
        .pager a, .pager span {
            display:inline-block; min-width:30px; text-align:center; padding:6px 8px;
            border:1px solid var(--border); font-size:12px; font-weight:600;
            text-decoration:none; color:var(--ink);
        }
        .pager a:hover { border-color:var(--primary); color:var(--primary); }
        .pager span.current { background:var(--primary); color:var(--on-primary); border-color:var(--primary); }
        .pager span.disabled { color:#c1c7d0; }

        /* prompt state (ยังไม่ได้ค้นหา) */
        .search-prompt {
            text-align:center; padding:56px 20px; color:var(--muted);
            border:1px dashed var(--border); background:#fafbfc;
        }
        .search-prompt .icon { font-size:34px; margin-bottom:10px; }
        .search-prompt .title { font-size:15px; font-weight:700; color:var(--ink); margin-bottom:6px; }
        .search-prompt .desc { font-size:13px; }
        a.ref-link { color:var(--primary); font-weight:700; text-decoration:none; }
        a.ref-link:hover { text-decoration:underline; }
        .po-alpha   { color:var(--primary); font-weight:700; }
        .po-numeric { color:var(--success); font-weight:700; }

        /* ===== ปุ่มดูรายการสินค้า + popup ===== */
        .btn-view-items {
            display:inline-flex; align-items:center; gap:6px;
            background:var(--primary-light); color:var(--primary-dark);
            border:1px solid #bfdbfe; padding:6px 12px;
            font-size:12px; font-weight:700; border-radius:6px;
            cursor:pointer; transition:.15s ease;
        }
        .btn-view-items:hover { background:var(--primary); color:var(--on-primary); border-color:var(--primary); }
        .btn-view-items .count-badge {
            background:var(--primary); color:var(--on-primary);
            border-radius:999px; font-size:11px; font-weight:700;
            min-width:18px; height:18px; display:inline-flex; align-items:center; justify-content:center;
            padding:0 5px;
        }
        .btn-view-items:hover .count-badge { background:var(--on-primary); color:var(--primary-dark); }

        .modal-overlay {
            display:none; position:fixed; inset:0; background:rgba(15,23,42,.45);
            align-items:center; justify-content:center; z-index:1000; padding:20px;
        }
        .modal-overlay.open { display:flex; }
        .modal-box {
            background:var(--canvas); width:100%; max-width:480px; max-height:80vh;
            border-radius:10px; overflow:hidden; display:flex; flex-direction:column;
            box-shadow:0 20px 50px rgba(0,0,0,.25);
        }
        .modal-head {
            background:var(--primary); color:var(--on-primary);
            padding:14px 18px; display:flex; align-items:center; justify-content:space-between;
        }
        .modal-head .modal-title { font-weight:700; font-size:15px; }
        .modal-head .modal-sub { font-size:11px; opacity:.85; margin-top:2px; }
        .modal-close {
            background:transparent; border:none; color:var(--on-primary);
            font-size:20px; line-height:1; padding:2px 8px; cursor:pointer; border-radius:4px;
        }
        .modal-close:hover { background:rgba(255,255,255,.2); }
        .modal-body { padding:14px 18px; overflow-y:auto; }
        .modal-foot { padding:10px 18px; border-top:1px solid var(--border); text-align:right; }

        /* ===== การ์ดรายการสินค้าในสไตล์ poRecv (แบบเดียวกับหน้ารับของภายนอก) ===== */
        .poRecv-item {
            background:#f8f9fa; border:1px solid #eee; border-radius:8px;
            padding:10px 14px; margin-bottom:8px;
        }
        .poRecv-item:last-child { margin-bottom:0; }
        .poRecv-item-top {
            display:flex; justify-content:space-between; align-items:baseline; gap:10px;
        }
        .poRecv-item-name { font-size:14px; color:#222; flex:1; text-align:left; }
        .poRecv-item-qty { font-size:14px; white-space:nowrap; color:#2b8a3e; }
        .poRecv-item-qty b { font-size:17px; }
        .poRecv-item.short { background:#fff9f9; border-color:#ffd9d9; }

        /* ===== สถานะรับเข้า (เฉพาะ PO ภายนอก) ===== */
        .modal-status-badge {
            display:inline-block; padding:4px 14px; border-radius:20px;
            font-weight:700; font-size:12px; margin-top:4px;
        }
        .modal-status-badge.st-ครบ      { background:#d3f9d8; color:#2b8a3e; }
        .modal-status-badge.st-บางส่วน  { background:#fff3bf; color:#e67700; }
        .modal-status-badge.st-ยกเลิก   { background:#ffd9d9; color:#c92a2a; }
        .modal-status-badge.st-none     { background:#eee; color:#666; }
        .modal-status-loading { font-size:12px; color:#999; margin-top:4px; }

        .poRecv-item-receive {
            display:flex; align-items:center; justify-content:space-between; gap:10px;
            margin-top:6px; padding-top:6px; border-top:1px dashed #e5e7eb;
            font-size:12px; color:#666;
        }
        .poRecv-item-receive .recv-qty b { font-size:14px; }
        .poRecv-item-receive .recv-qty b.short { color:#dc3545; }
        .poRecv-item-receive .recv-photo-btn {
            border:1px solid #d0d5dd; background:#fff; color:#3E6AE1;
            font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px;
            cursor:pointer; white-space:nowrap;
        }
        .recv-lightbox {
            display:none; position:fixed; inset:0; background:rgba(0,0,0,.85);
            z-index:100001; cursor:zoom-out;
        }
        .recv-lightbox img { max-width:90%; max-height:90%; margin:5vh auto; display:block; box-shadow:0 4px 24px rgba(0,0,0,.5); }
    </style>
</head>
<body>
<div class="page-frame">
<div class="top-banner">
    <div class="title-group">
        <span class="h1">เช็คเอาต์ / ของออก</span>
        <span class="sticker">Store</span>
    </div>
</div>
<input type="hidden" id="inpUser" value="{{ $creator }}">
@php $nav = $creator ? ['create_by' => $creator] : []; @endphp
<main>
    <form class="toolbar" method="GET" action="{{ url()->current() }}">
        @if ($creator)<input type="hidden" name="create_by" value="{{ $creator }}">@endif
        <input type="search" name="SONum" value="{{ request('SONum') }}" placeholder="ค้นหาเลข SO..." autocomplete="off">
        <input type="search" name="PONum" value="{{ request('PONum') }}" placeholder="ค้นหาเลข PO..." autocomplete="off">

        <label class="muted">วันที่เปิด SO
            <input type="date" name="bill_date" value="{{ $billDate }}">
        </label>

        <button type="submit" class="btn-primary">ค้นหา</button>
        @if (request('SONum') || request('PONum') || $billDate)
            <a href="{{ url()->current() }}{{ $creator ? '?create_by='.urlencode($creator) : '' }}">
                <button type="button" class="btn-ghost">ล้าง</button>
            </a>
        @endif

        @if ($searched)
        <button type="button" id="btnHideDone" class="hide-done-toggle active">ซ่อนที่ทำแล้ว</button>
        @endif
    </form>

    @if (!$searched)
        {{-- ยังไม่ได้ค้นหา: ไม่ยิง query และไม่แสดงตาราง ต้องกรอกเงื่อนไขก่อน --}}
        <div class="search-prompt">
            <div class="icon">🔎</div>
            <div class="title">ระบุเงื่อนไขค้นหาก่อนแสดงรายการ</div>
            <div class="desc">เลือก "วันที่เปิด SO" หรือกรอก "เลข SO" หรือ "เลข PO" แล้วกดค้นหา</div>
        </div>
    @else
    <div class="table-scroll">
    <div class="table-topbar">
        <div class="table-info">
            รอเอาออก {{ $totalTodo }} / ทั้งหมด {{ $total }} ใบ
            (หน้า {{ $heads->currentPage() }} จาก {{ $heads->lastPage() }}, แสดงหน้าละ {{ $heads->perPage() }})
        </div>
        <button type="button" class="btn-success" id="btnMain" hidden onclick="submitCheckout()">
            ของออก (<span id="selCount">0</span>)
        </button>
    </div>

    <div class="table-scroll">
    <table>
        <thead>
            <tr>
                <th class="center" style="width:44px;"><input type="checkbox" id="chkAll"></th>
                <th class="center" style="width:50px;">ลำดับ</th>
                <th>PO ภายใน</th><th>SO</th><th>รายการสินค้า</th>
                <th>ลูกค้า</th><th>ที่เก็บ</th><th>ระบุที่โดย</th><th>เวลาระบุ</th>
            </tr>
        </thead>
        <tbody>
        @php $prevSo = null; $rowNum = ($heads->currentPage() - 1) * $heads->perPage(); @endphp
        @forelse ($heads as $h)
            @php
                $rowNum++;
                $todo       = $h->todo;
                $items      = $h->items;
                $newSoGroup = $prevSo !== null && $prevSo !== $h->so_id;
                $prevSo     = $h->so_id;
                $cls        = trim(($todo ? '' : 'done') . ($newSoGroup ? ' so-group-start' : ''));
                $modalId    = 'itemsModal_' . $h->type . '_' . $h->id;
            @endphp
            <tr class="{{ $cls }}" data-done="{{ $todo ? 0 : 1 }}" data-so="{{ $h->so_id }}">
                <td class="center">
                    @if ($todo)<input type="checkbox" class="chkLine" data-so="{{ $h->so_id }}" value="{{ $h->type }}:{{ $h->id }}">@endif
                </td>
                <td class="center muted">{{ $rowNum }}</td>
                @php
                    $poClean = preg_replace('/^\s*PO\s*/i', '', $h->po_display);
                    $poColorClass = preg_match('/[A-Za-z]/', $poClean) ? 'po-numeric' : 'po-alpha';
                @endphp
                <td><span class="{{ $poColorClass }}">{{ $poClean }}</span></td>
                <td>{{ $h->so_id }}</td>
                <td class="items-cell">
                <button type="button" class="btn-view-items" data-modal-id="{{ $modalId }}"
                    onclick="openItemsModal('{{ $modalId }}')">
                    ดูรายการสินค้า <span class="count-badge" id="{{ $modalId }}_count">{{ $h->type === 'internal' ? $items->count() : '…' }}</span>
                    </button>

                    <div class="modal-overlay" id="{{ $modalId }}">
                        <div class="modal-box">
                            <div class="modal-head">
                                <div>
                                    <div class="modal-title">รายการสินค้า</div>
                                    <div class="modal-sub">SO {{ $h->so_id }} • PO {{ $poClean }}</div>
                                    @if ($h->type !== 'internal')
                                        <div id="{{ $modalId }}_statusBadge"></div>
                                    @endif
                                </div>
                                <button type="button" class="modal-close" onclick="closeItemsModal('{{ $modalId }}')">&times;</button>
                            </div>
                            <div class="modal-body" id="{{ $modalId }}_body">
                                @if ($h->type === 'internal')
                                    @foreach ($items as $it)
                                        <div class="poRecv-item">
                                            <div class="poRecv-item-top">
                                                <div class="poRecv-item-name">{{ $it->item_name }}</div>
                                                <div class="poRecv-item-qty"><b>{{ number_format($it->item_quantity, 2) }}</b></div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="modal-status-loading">กำลังโหลดรายการสินค้า...</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>
                <td class="cust-cell">{{ $h->customer_name ?: '—' }}</td>
                <td>{{ $h->location ?: '—' }}</td>
                <td>{{ $h->done_by ?: '—' }}</td>
                <td class="muted">{{ $h->done_at ? \Carbon\Carbon::parse($h->done_at)->format('d/m/Y H:i') : '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="10" class="empty">ไม่มีรายการตรงกับเงื่อนไขที่ค้นหา</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="actionbar">
    <div class="pager">
            @php
                $qs = collect(request()->except('page'))->toArray();
                $mkUrl = fn($p) => url()->current() . '?' . http_build_query(array_merge($qs, ['page' => $p]));
            @endphp

            @if ($heads->currentPage() > 1)
                <a href="{{ $mkUrl(1) }}">« แรก</a>
                <a href="{{ $mkUrl($heads->currentPage() - 1) }}">‹ ก่อนหน้า</a>
            @else
                <span class="disabled">« แรก</span>
                <span class="disabled">‹ ก่อนหน้า</span>
            @endif

            @php
                $start = max(1, $heads->currentPage() - 3);
                $end   = min($heads->lastPage(), $heads->currentPage() + 3);
            @endphp
            @for ($p = $start; $p <= $end; $p++)
                @if ($p == $heads->currentPage())
                    <span class="current">{{ $p }}</span>
                @else
                    <a href="{{ $mkUrl($p) }}">{{ $p }}</a>
                @endif
            @endfor

            @if ($heads->currentPage() < $heads->lastPage())
                <a href="{{ $mkUrl($heads->currentPage() + 1) }}">ถัดไป ›</a>
                <a href="{{ $mkUrl($heads->lastPage()) }}">สุดท้าย »</a>
            @else
                <span class="disabled">ถัดไป ›</span>
                <span class="disabled">สุดท้าย »</span>
            @endif
        </div>
    </div>
    @endif
</main>
</div>

<div class="recv-lightbox" id="recvLightbox" onclick="this.style.display='none'">
    <img id="recvLightboxImg" src="">
</div>
<script>
const SUBMIT_URL = "{{ route('store.checkout.submit') }}";
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const ITEMS_DETAIL_URL = "{{ route('apis.store.legacyPoItemsBatch') }}";

// map: modalId -> {type, id, so_id} เฉพาะ external + legacy (internal เรนเดอร์ inline แล้ว)
const detailModalMap = {!! json_encode(
    $heads->getCollection()
        ->filter(fn ($h) => $h->type !== 'internal')
        ->mapWithKeys(fn ($h) => ['itemsModal_' . $h->type . '_' . $h->id => [
            'type'  => $h->type,
            'id'    => $h->type === 'legacy' ? $h->po_display : $h->id,
            'so_id' => $h->so_id,
        ]])
) !!};

const STATUS_BADGE_CLASS = { 'ครบ': 'st-ครบ', 'บางส่วน': 'st-บางส่วน', 'ยกเลิก': 'st-ยกเลิก' };

// ===== สถานะการโหลดของแต่ละ modal: 'idle' | 'loading' | 'success' | 'error' =====
const loadStatus = {};
Object.keys(detailModalMap).forEach(id => { loadStatus[id] = 'idle'; });

const CHUNK_SIZE = 30;          // โหลดทีละ 30 รายการต่อ 1 คำขอ
const FETCH_TIMEOUT_MS = 20000; // timeout ต่อคำขอ 20 วิ
const MAX_AUTO_RETRY = 1;       // auto retry กี่ครั้งก่อนต้องให้ผู้ใช้กดเอง

function fetchWithTimeout(url, options, timeoutMs) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), timeoutMs);
    return fetch(url, { ...options, signal: controller.signal }).finally(() => clearTimeout(timer));
}

function chunkArray(arr, size) {
    const out = [];
    for (let i = 0; i < arr.length; i += size) out.push(arr.slice(i, i + size));
    return out;
}

async function fetchDetailChunk(modalIds) {
    const items = modalIds.map(id => {
        const m = detailModalMap[id];
        return { type: m.type, id: m.id, so_id: m.so_id };
    });

    const res = await fetchWithTimeout(ITEMS_DETAIL_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CSRF,
        },
        body: JSON.stringify({ items }),
    }, FETCH_TIMEOUT_MS);

    const data = await res.json();
    if (!res.ok || !data.ok) throw new Error('batch fetch failed');
    return data.items;
}

async function loadChunkWithRetry(modalIds, attempt = 0) {
    modalIds.forEach(id => { loadStatus[id] = 'loading'; });

    try {
        const result = await fetchDetailChunk(modalIds);
        const okIds = [];
        const missingIds = [];
        modalIds.forEach(modalId => {
            const m   = detailModalMap[modalId];
            const key = m.type + ':' + m.id + ':' + (m.so_id || '');
            if (result[key]) okIds.push(modalId); else missingIds.push(modalId);
        });

        okIds.forEach(modalId => {
            const m      = detailModalMap[modalId];
            const key    = m.type + ':' + m.id + ':' + (m.so_id || '');
            loadStatus[modalId] = 'success';
            renderItemsDetail(modalId, m.type, result[key]);
        });

        if (missingIds.length) {
            await handleFailedChunk(missingIds, attempt, false);
        }
    } catch (e) {
        const isTimeout = e.name === 'AbortError';
        console.error('loadChunkWithRetry:', e);
        await handleFailedChunk(modalIds, attempt, isTimeout);
    }
}

async function handleFailedChunk(modalIds, attempt, isTimeout) {
    if (attempt < MAX_AUTO_RETRY) {
        modalIds.forEach(modalId => renderItemsDetailLoading(modalId,
            isTimeout ? '⏱ หมดเวลาโหลด (timeout) กำลังโหลดใหม่...' : 'โหลดไม่สำเร็จ กำลังลองใหม่...'
        ));
        await new Promise(r => setTimeout(r, 1500));
        return loadChunkWithRetry(modalIds, attempt + 1);
    }

    modalIds.forEach(modalId => {
        loadStatus[modalId] = 'error';
        renderItemsDetailError(modalId,
            isTimeout ? '⏱ หมดเวลาโหลด (timeout) — เซิร์ฟเวอร์ตอบช้าเกินไป' : 'โหลดรายการสินค้าไม่สำเร็จ'
        );
    });
}

async function loadWave(modalIds) {
    for (const chunk of chunkArray(modalIds, CHUNK_SIZE)) {
        await loadChunkWithRetry(chunk);
    }
}

async function loadItemsDetailBatch() {
    const allIds = Object.keys(detailModalMap);
    if (!allIds.length) return;


    const priorityIds = [];
    const restIds = [];
    allIds.forEach(modalId => {
        const btn = document.querySelector(`[data-modal-id="${modalId}"]`);
        const isDone = btn?.closest('tr')?.dataset.done === '1';
        (isDone ? restIds : priorityIds).push(modalId);
    });

    await loadWave(priorityIds); 
    await loadWave(restIds);     
}

function renderItemsDetailLoading(modalId, message) {
    const body = document.getElementById(modalId + '_body');
    if (body) body.innerHTML = `<div class="modal-status-loading">${escRecv(message)}</div>`;

    const countEl = document.getElementById(modalId + '_count');
    if (countEl) countEl.textContent = '…';
}

function renderItemsDetailError(modalId, message) {
    const body = document.getElementById(modalId + '_body');
    if (body) {
        body.innerHTML = `
            <div class="empty" style="padding:16px;">
                ❌ ${escRecv(message)}<br>
                <button type="button" class="btn-ghost" style="margin-top:10px;" onclick="retryModal('${modalId}')">ลองโหลดใหม่</button>
            </div>`;
    }
    const countEl = document.getElementById(modalId + '_count');
    if (countEl) countEl.textContent = '!';
}

function retryModal(modalId) {
    renderItemsDetailLoading(modalId, 'กำลังโหลดรายการสินค้า...');
    loadChunkWithRetry([modalId], 0);
}

function renderItemsDetail(modalId, type, detail) {
    const body    = document.getElementById(modalId + '_body');
    const badgeEl = document.getElementById(modalId + '_statusBadge');

    if (badgeEl) {
        const cls = STATUS_BADGE_CLASS[detail.status] || 'st-none';
        badgeEl.innerHTML = detail.status
            ? `<span class="modal-status-badge ${cls}">${escRecv(detail.status)}</span>`
            : `<span class="modal-status-badge st-none">ยังไม่มีการรับเข้า</span>`;
    }

    if (body) {
        body.innerHTML = (detail.items || []).map(it => renderItemCardCheckout(it, type)).join('')
            || '<div class="empty" style="padding:16px;">ไม่มีรายการ</div>';
    }

    const countEl = document.getElementById(modalId + '_count');
    if (countEl) countEl.textContent = (detail.items || []).length;
}

function renderItemCardCheckout(it, type) {
    const hasOrdered = it.ordered_qty !== null && it.ordered_qty !== undefined;
    const recv = fmtRecvQty(it.received_qty);
    const qtyHtml = hasOrdered
        ? `<b style="color:${it.short ? '#dc3545' : '#2b8a3e'};">${recv}</b><span style="color:#ccc;">/</span><span style="color:${it.short ? '#dc3545' : '#888'};font-weight:${it.short ? '700' : '400'};">${fmtRecvQty(it.ordered_qty)}</span>`
        : `<b style="color:#2b8a3e;">${recv}</b>`;

    const extra = (type === 'external' && (it.shelf || it.photo_url)) ? `
        <div class="poRecv-item-receive">
            <div class="recv-qty">${it.shelf ? 'ที่เก็บ: ' + escRecv(it.shelf) : ''}</div>
            ${it.photo_url ? `<button type="button" class="recv-photo-btn" onclick="openRecvLightbox('${it.photo_url}')">ดูรูป</button>` : ''}
        </div>` : '';

    return `
        <div class="poRecv-item ${it.short ? 'short' : ''}">
            <div class="poRecv-item-top">
                <div class="poRecv-item-name">${escRecv(it.item_name)}</div>
                <div class="poRecv-item-qty">${qtyHtml}</div>
            </div>
            ${extra}
        </div>`;
}
function openRecvLightbox(url) {
    document.getElementById('recvLightboxImg').src = url;
    document.getElementById('recvLightbox').style.display = 'block';
}

document.addEventListener('DOMContentLoaded', loadItemsDetailBatch);

const selectedIds = () => Array.from(document.querySelectorAll('.chkLine:checked')).map(c => c.value);
const currentUser = () => document.getElementById('inpUser').value.trim();

function refreshBtn() {
    const n = selectedIds().length;
    document.getElementById('selCount').textContent = n;
    document.getElementById('btnMain').hidden = (n === 0);
}

// วางเมาส์ชี้แถวไหน ไฮไลต์ทุกแถวที่เป็น SO เดียวกันให้เห็นทันที (ไม่ต้องกดติ๊ก)
document.querySelectorAll('tr[data-so]').forEach(tr => {
    tr.addEventListener('mouseenter', () => {
        const so = tr.dataset.so;
        document.querySelectorAll('tr[data-so]').forEach(t => {
            if (t.dataset.so === so) t.classList.add('so-hover');
        });
    });
    tr.addEventListener('mouseleave', () => {
        const so = tr.dataset.so;
        document.querySelectorAll('tr[data-so]').forEach(t => {
            if (t.dataset.so === so) t.classList.remove('so-hover');
        });
    });
});

const chkAll = document.getElementById('chkAll');
if (chkAll) {
    chkAll.addEventListener('change', function () {
        document.querySelectorAll('.chkLine').forEach(c => { c.checked = this.checked; });
        updateAllSoHighlight();
        refreshBtn();
    });
}

function syncSoGroup(changed) {
    const so = changed.dataset.so;
    if (changed.checked) {
        document.querySelectorAll('.chkLine').forEach(c => {
            if (c.dataset.so === so) c.checked = true;
        });
    }
    updateSoHighlight(so);
    refreshBtn();
}

function updateSoHighlight(so) {
    const anyChecked = Array.from(document.querySelectorAll(`.chkLine[data-so="${so}"]`)).some(c => c.checked);
    document.querySelectorAll('tr[data-so]').forEach(tr => {
        if (tr.dataset.so === so) tr.classList.toggle('so-selected', anyChecked);
    });
}
function updateAllSoHighlight() {
    const sos = new Set(Array.from(document.querySelectorAll('.chkLine')).map(c => c.dataset.so));
    sos.forEach(updateSoHighlight);
}

document.querySelectorAll('.chkLine').forEach(c => c.addEventListener('change', function () { syncSoGroup(this); }));

const btnHide = document.getElementById('btnHideDone');
if (btnHide) {
    const applyHide = () => document.querySelectorAll('tr[data-done="1"]').forEach(tr => tr.hidden = btnHide.classList.contains('active'));
    btnHide.addEventListener('click', () => { btnHide.classList.toggle('active'); applyHide(); });
    applyHide();
}

// ===== popup ดูรายการสินค้า =====
function openItemsModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('open');

    const status = loadStatus[id]; 
    if (status === 'idle' || status === 'error') {
        renderItemsDetailLoading(id, 'กำลังโหลดรายการสินค้า...');
        loadChunkWithRetry([id], 0);
    }
}
function closeItemsModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
}
function escRecv(s) {
    return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}
function fmtRecvQty(v) {
    const n = parseFloat(v);
    if (isNaN(n)) return '0';
    return (n % 1 === 0) ? String(n) : n.toFixed(2);
}

document.querySelectorAll('.modal-overlay').forEach(ov => {
    ov.addEventListener('click', (e) => { if (e.target === ov) ov.classList.remove('open'); });
});
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(ov => ov.classList.remove('open'));
});

async function submitCheckout() {
    if (!currentUser())        { alert('กรุณาระบุชื่อผู้ดำเนินการ'); return; }
    const ids = selectedIds();
    if (!ids.length)           { alert('ยังไม่ได้เลือกรายการ'); return; }
    if (!confirm('ยืนยันของออก' )) return;

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