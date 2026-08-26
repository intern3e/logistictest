<!DOCTYPE html>
{{-- resources/views/store/store_checkout.blade.php  (ด่าน 3: ของออก - SO -> บิลขนส่ง -> PO เช็คของออก) --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>จัดบิลส่งของ</title>
<style>
    /* ===== จำกัดสีแค่ ขาว / เทา / เขียว / แดง / น้ำเงิน ===== */
    :root{
        --ink:#1e293b; --canvas:#ffffff; --muted:#6b7280; --faint:#9ca3af; --border:#dcdcdc;
        --primary:#2563eb; --primary-dark:#1d4ed8; --primary-light:#eff6ff;
        --on-primary:#ffffff; --success:#16a34a; --success-dark:#15803d; --success-light:#dcfce7;
        --danger:#dc2626; --danger-dark:#b91c1c; --danger-light:#fee2e2;
    }
    * { box-sizing:border-box; margin:0; padding:0; }
    html,body { background:var(--canvas); overflow-x:hidden; max-width:100%; }
    body {
        font-family:'Segoe UI', Tahoma, Arial, sans-serif; font-size:18px;
        color:var(--ink); padding-bottom:40px;
    }
    main { max-width:none; margin:0; padding:20px 28px; }

    /* ===== Header ===== */
    .top-banner {
        background:var(--canvas); padding:16px; border-bottom:1px solid var(--border);
        display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;
    }
    .top-banner .title-group { display:flex; align-items:center; gap:10px; }
    .top-banner .h1 { font-weight:700; font-size:24px; }
    .top-banner .sticker {
        background:var(--primary-light); color:var(--primary-dark); border:1px solid #bfdbfe;
        font-weight:600; font-size:15px; padding:4px 12px; text-transform:uppercase; letter-spacing:.3px; border-radius:0;
    }
    .top-banner .user-info { font-size:17px; color:var(--muted); font-weight:600; }
    .top-banner .user-info span { color:var(--ink); font-weight:700; }

    /* ===== toolbar / filters — SO/PO กดค้นหา/Enter, วันที่เปลี่ยนแล้วค้นหาทันที ===== */
    .toolbar-row {
        display:flex; align-items:baseline; justify-content:space-between; flex-wrap:wrap; gap:12px;
        margin-bottom:14px;
    }
    .toolbar { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
    .toolbar .field-label { font-size:16px; color:var(--muted); font-weight:600; display:flex; align-items:center; gap:6px; }
    input[type="text"],input[type="search"],input[type="date"] {
        padding:9px 12px; border:1px solid var(--border); border-radius:0;
        font-family:inherit; font-size:18px; background:var(--canvas); color:var(--ink);
    }
    input:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px var(--primary-light); }
    button {
        padding:9px 18px; border:1px solid transparent; border-radius:0;
        font-family:inherit; font-weight:600; font-size:18px; cursor:pointer; transition:.15s ease;
    }
    .btn-primary { background:var(--primary); color:var(--on-primary); }
    .btn-primary:hover { background:var(--primary-dark); }
    .btn-success { background:var(--success); color:var(--on-primary); }
    .btn-success:hover { background:var(--success-dark); }
    .btn-ghost   { background:var(--canvas); color:var(--muted); border-color:var(--border); }
    .btn-ghost:hover { background:#f3f4f6; color:var(--ink); }
    button:disabled { opacity:.4; cursor:not-allowed; }

    .empty-state {
        text-align:center; padding:48px 20px; color:var(--muted);
        border:1px dashed var(--border); background:var(--canvas);
    }

    .list-meta {
        flex-shrink:0; white-space:nowrap;
        font-size:16px; color:var(--muted);
    }
    .list-meta .meta-date { font-weight:600; color:var(--ink); }
    .list-meta .meta-stats strong { color:var(--ink); font-weight:800; }
    .list-meta .meta-stats .stat-pending { color:var(--danger-dark); }

    /* ===== การ์ด SO แบบกริด แถวละ 3 ทุกขนาดจอ ===== */
    .so-grid {
        display:grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap:12px;
        margin-top:6px;
        align-items:start;
    }

    .so-card {
        background:var(--canvas); border:1px solid var(--border); border-left:4px solid var(--border);
        overflow:hidden; display:flex; flex-direction:column; min-width:0;
    }
    .so-card[data-done="1"] { border-left-color:var(--success); }
    .so-card-header {
        display:flex; align-items:flex-start; gap:10px;
        padding:12px 14px; border-bottom:1px solid var(--border); background:#fafbfd;
        cursor:pointer;
    }
    .so-card-header:hover { background:#f2f4f8; }
    .so-card-header:focus-visible { outline:2px solid var(--primary); outline-offset:-2px; }
    .so-toggle {
        color:var(--faint); font-size:16px; margin-top:2px; flex-shrink:0;
        display:inline-block; user-select:none; transition:transform .12s ease;
        pointer-events:none;
    }
    .so-card:not(.collapsed) .so-toggle { transform:rotate(90deg); }
    .so-card.collapsed .so-body { display:none; }
    .so-head-main { flex:1; min-width:0; }
    .so-id { font-weight:800; font-size:18px; word-break:break-all; }
    .so-id.is-done { color:var(--success-dark); }
    .so-sub {
        font-size:14.5px; color:var(--muted); margin-top:2px;
        white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    }
    .so-status {
        flex-shrink:0; font-size:13px; font-weight:700; padding:5px 10px; border-radius:0; white-space:nowrap;
    }
    .so-status.pending { background:var(--danger-light); color:var(--danger-dark); }
    .so-status.done { background:var(--success-light); color:var(--success-dark); }

    .so-body { padding:0; overflow-x:auto; }

    /* ===== ระดับ 2: บิลขนส่ง (dn / billid) — คั่นด้วยเส้นบาง ไม่ทำกล่องซ้อนกล่อง =====
       ไม่มีสามเหลี่ยม/เปิดปิดในระดับนี้แล้ว แสดงตลอด (static header) */
    .dn-section { border-top:1px solid var(--border); }
    .dn-section:first-child { border-top:none; }
    .dn-section-header {
        display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;
        padding:9px 14px; background:#fcfcfd;
    }
    .dn-no { font-weight:700; font-size:16px; }
    .dn-no.is-done { color:var(--success-dark); }
    .dn-no.is-cancelled { color:var(--danger); text-decoration:line-through; }
    .dn-time { font-size:14px; color:var(--muted); }
    .dn-hint { font-size:14px; color:var(--faint); margin-left:auto; }
    .dnSelectAll,
    .chkPickOnly {
        width:24px; height:24px; accent-color:var(--primary); flex-shrink:0; cursor:pointer;
        border:2px solid #111827; border-radius:2px;
    }
    .dn-cancelled-badge {
        font-size:14px; font-weight:700; color:var(--danger); margin-left:auto; white-space:nowrap;
    }
    .dn-section.dn-cancelled > .dn-section-header { background:var(--danger-light); }
    .dn-cancelled-note { font-size:14px; color:var(--danger); padding:8px 0 8px 20px; }

    .dn-body { padding:4px 14px 12px; }

    /* ===== หัวคอลัมน์รายการสินค้า ===== */
    .item-col-head {
        display:flex; justify-content:space-between; gap:10px;
        padding:6px 0 6px 20px; font-size:13px; color:var(--ink);
        font-weight:800; text-transform:uppercase; letter-spacing:.3px;
        margin-bottom:2px;
    }

    /* ===== ระดับ 3: รายการ PO ในแต่ละบิลขนส่ง ===== */
    .po-row { padding:8px 0; border-bottom:1px solid #eeeeea; border-radius:0; }
    .po-row:last-of-type { border-bottom:none; }

    /* รายการที่จัดแล้ว (checkout แล้ว) → ทำเป็น "กล่อง" พื้นเขียวอ่อนทั้งใบ แทนตัวหนังสือสีเขียว */
    .po-row.po-row-done {
        background:var(--success-light);
        padding:10px 10px;
        margin:4px 0;
        border-bottom:none;
    }

    .po-row-head { display:flex; align-items:center; gap:8px; margin-bottom:2px; flex-wrap:wrap; }
    .po-row-head input[type="checkbox"] {
        width:24px; height:24px; accent-color:var(--primary); flex-shrink:0;
        border:2px solid #111827; border-radius:2px;
    }
    .po-row.po-row-done .po-row-head { padding-left:22px; }
    .source-tag {
        font-size:13px; font-weight:700; color:var(--muted); border:1px solid var(--border);
        padding:2px 7px; border-radius:0; text-transform:uppercase; letter-spacing:.2px; flex-shrink:0;
        background:var(--canvas); /* กันไม่ให้ tag จมไปกับพื้นเขียวอ่อนของกล่อง po-row-done */
    }
    .po-num { font-weight:700; font-size:15.5px; }

    .item-row {
        display:flex; justify-content:space-between; align-items:baseline; gap:10px;
        padding:4px 0 4px 20px;
    }
    .item-row .item-name { font-size:15px; color:#000000; font-weight:700; }
    .item-row .item-qty { font-size:15px; font-weight:700; color:#000000; white-space:nowrap; }
    .item-meta { font-size:13px; color:var(--faint); padding-left:20px; margin-top:-2px; margin-bottom:4px; }

    .po-row-meta,
    .item-row-meta {
        display:block; font-size:14px; color:var(--ink); font-weight:700;
        margin:2px 0 6px 20px;
    }
    .po-row-meta.checkout-meta { color:var(--success-dark); font-weight:700; }

    .dn-foot { display:flex; justify-content:flex-end; padding-top:10px; }
    .btn-checkout-dn {
        background:var(--success); color:#fff; border-radius:0; padding:9px 18px; font-weight:700; font-size:17px;
    }
    .btn-checkout-dn:hover { background:var(--success-dark); }

    .no-dn-note { font-size:14px; color:var(--muted); padding:6px 0 8px 20px; }

    /* ===== แถบเลือกรายการลอยมุมขวาล่าง — ไม่ใช่บล็อกขาวเต็มจอ ไม่บัง pagination ===== */
    .checkout-floatbar {
        position:fixed; right:20px; bottom:20px; left:auto; z-index:50;
        display:flex; align-items:center; gap:10px;
        padding:0; background:transparent; border:none; box-shadow:none;
        justify-content:flex-end;
    }
    .checkout-floatbar .floatbar-count {
        font-size:15px; font-weight:700; color:var(--ink);
        background:var(--canvas); border:1px solid var(--border);
        padding:9px 16px; border-radius:0;
        box-shadow:0 2px 10px rgba(0,0,0,.12);
        white-space:nowrap;
    }
    .checkout-floatbar .floatbar-count strong { color:var(--success-dark); }
    .checkout-floatbar .btn-ghost,
    .checkout-floatbar .btn-success {
        box-shadow:0 2px 10px rgba(0,0,0,.12);
        border-radius:0;
    }
    body.has-floatbar { padding-bottom:24px; }

    /* ===== pagination ===== */
    .pager { display:flex; gap:4px; align-items:center; flex-wrap:wrap; justify-content:center; margin-top:18px; }
    .pager a, .pager span {
        display:inline-block; min-width:36px; text-align:center; padding:7px 9px;
        border:1px solid var(--border); border-radius:0; font-size:16px; font-weight:600;
        text-decoration:none; color:var(--ink); background:var(--canvas);
    }
    .pager a:hover { border-color:var(--primary); color:var(--primary); }
    .pager span.current { background:var(--primary); color:var(--on-primary); border-color:var(--primary); }
    .pager span.disabled { color:#c1c7d0; }

    /* มือถือ/จอแคบ: กลับเป็นแถวละ 1 ใบตามปกติ ไม่บังคับ 3 คอลัมน์ (3 คอลัมน์ใช้เฉพาะจอคอมพิวเตอร์) */
    @media (max-width:900px){
        .so-grid { grid-template-columns:1fr; }
    }
</style>
</head>
<body>
<div class="top-banner">
    <div class="title-group">
        <span class="h1">จัดบิลส่งของ</span>
    </div>
    <div class="user-info">ผู้ใช้: <span>{{ $creator }}</span></div>
</div>
<input type="hidden" id="inpUser" value="{{ $creator }}">

<main>
    {{-- ===== ตัวกรอง (SO/PO ต้องกดค้นหาหรือ Enter, เปลี่ยนวันที่แล้วค้นหาให้ทันที) + สรุปงานประจำวันอยู่ขวา ===== --}}
    <div class="toolbar-row">
        <form class="toolbar" id="filterForm" method="GET" action="{{ url()->current() }}">
            <input type="search" name="SONum" value="{{ request('SONum') }}" placeholder="ค้นหาเลข SO..." autocomplete="off">
            <input type="search" name="PONum" value="{{ request('PONum') }}" placeholder="ค้นหาเลข PO..." autocomplete="off">
            <label class="field-label">
                วันที่เปิดบิล
                <input type="date" name="bill_date" value="{{ $billDate }}">
            </label>
            <button type="submit" class="btn-primary">ค้นหา</button>
        </form>

        <div class="list-meta">
            <div class="meta-stats">
                <strong>{{ $daySummary['total_bills'] }}</strong> บิลขนส่งทั้งหมด
                &nbsp;·&nbsp;
                <strong class="stat-pending">{{ $daySummary['pending_bills'] }}</strong> บิลยังค้างอยู่
            </div>
        </div>
    </div>

    @if ($bills->count() > 0)
    <div class="so-grid">
        @foreach ($bills as $bill)
            @php
                $soIdSafe = 'so_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $bill->so_id);
                $dnList   = $bill->bills->isNotEmpty() ? $bill->bills : collect([(object) ['dn_no' => null, 'time' => null]]);
                $sourceLabel = fn ($type) => $type === 'internal' ? 'ภายใน' : ($type === 'external' ? 'ระบบใหม่' : '');
                $poClean = fn ($raw) => preg_replace('/^\s*PO\s*/i', '', (string) $raw);
            @endphp
            {{-- การ์ดเริ่มต้นแบบ "ปิด" เสมอ (class collapsed) ผู้ใช้ต้องกดที่หัวการ์ดเพื่อเปิดดูรายละเอียด --}}
            <div class="so-card collapsed" id="{{ $soIdSafe }}" data-done="{{ $bill->all_done ? 1 : 0 }}">
                <div class="so-card-header" role="button" tabindex="0" aria-expanded="false"
                     aria-label="เปิด/ปิดรายการ {{ $bill->so_id }}"
                     onclick="toggleSoCard('{{ $soIdSafe }}')"
                     onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleSoCard('{{ $soIdSafe }}');}">
                    <span class="so-toggle" aria-hidden="true">▸</span>
                    <div class="so-head-main">
                        <div class="so-id {{ $bill->all_done ? 'is-done' : '' }}">{{ $bill->so_id }}</div>
                        <div class="so-sub">
                            @if($bill->customer_id) <span style="font-weight:600;">{{ $bill->customer_id }}</span> @endif
                            @if($bill->customer_id && $bill->customer_name) · @endif
                            @if($bill->customer_name) {{ $bill->customer_name }} @endif
                        </div>
                    </div>
                    @if ($bill->all_done)
                        <span class="so-status done">จัดของแล้ว</span>
                    @endif
                </div>

                <div class="so-body">
                    @if ($bill->bills->isEmpty())
                        <div class="no-dn-note">⚠️ ยังไม่พบข้อมูลบิลขนส่ง (tblbill) ของ SO นี้</div>
                    @endif

                    @php
                        // หา "บิลหลัก" ที่จะผูกกับรายการ PO/สินค้าร่วม — เลือกบิลที่ยังไม่ยกเลิกและยังไม่ถูกบันทึกผู้จัดก่อน
                        // ถ้าทุกบิลถูกบันทึกผู้จัดหมดแล้ว ให้ fallback ไปบิลแรกที่ไม่ยกเลิก
                        $itemHostDnIdx = collect($dnList)->search(fn ($d) => !($d->cancelled ?? false) && !($d->picked ?? false));
                        if ($itemHostDnIdx === false) {
                            $itemHostDnIdx = collect($dnList)->search(fn ($d) => !($d->cancelled ?? false));
                        }
                        if ($itemHostDnIdx === false) $itemHostDnIdx = 0;

                        $itemsElId   = $soIdSafe . '_items';
                        $selectAllId = $soIdSafe . '_selectall';
                        $hostDn      = $dnList[$itemHostDnIdx] ?? null;
                    @endphp

                    {{-- ===== รายชื่อบิลขนส่งทั้งหมดของ SO นี้ เรียงต่อกัน (ไม่แทรกรายการสินค้าคั่นกลาง) ===== --}}
                    @foreach ($dnList as $dnIdx => $dn)
                        @php
                            $dnElId        = $soIdSafe . '_dn' . $dnIdx;
                            $isCancelled   = $dn->cancelled ?? false;
                            $isPicked      = $dn->picked ?? false;
                            $isItemHost    = ($dnIdx === $itemHostDnIdx);
                            $showSelectAll = $isItemHost && !$isCancelled && !$isPicked && !$bill->todo_groups->isEmpty();
                            $showPickOnly  = !$isCancelled && !$isPicked && !$showSelectAll && $dn->dn_no;
                        @endphp
                        <div class="dn-section {{ $isCancelled ? 'dn-cancelled' : '' }}" id="{{ $dnElId }}" data-dnno="{{ $dn->dn_no }}">
                            <div class="dn-section-header">
                                @if ($showSelectAll)
                                    <input type="checkbox" class="dnSelectAll" id="{{ $selectAllId }}" aria-label="เลือกทั้งหมดของ SO นี้"
                                        onchange="toggleDnSelectAll(document.getElementById('{{ $itemsElId }}'), this.checked)">
                                @elseif ($showPickOnly)
                                    <input type="checkbox" class="chkPickOnly"
                                        aria-label="บันทึกชื่อผู้จัดบิลนี้"
                                        onchange="syncCardFromPickOnly(this)">
                                @endif
                                <span class="dn-no {{ $isCancelled ? 'is-cancelled' : ($isPicked ? 'is-done' : '') }}">{{ $dn->dn_no ?: '— (ไม่มีเลขที่บิล)' }}</span>
                                @if ($dn->time)
                                    <span class="dn-time">{{ \Carbon\Carbon::parse($dn->time)->addYears(543)->format('d/m/Y H:i') }}</span>
                                @endif
                                @if ($dn->opened_by)
                                    <span class="dn-time">ผู้เปิดบิล {{ $dn->opened_by }}</span>
                                @endif
                                @if ($isCancelled)
                                    <span class="dn-cancelled-badge">ยกเลิกแล้ว</span>
                                @elseif ($isPicked)
                                    <span class="dn-picked-badge">
                                        จัดของแล้ว{{ $dn->picked_by ? ' โดย ' . $dn->picked_by : '' }}{{ $dn->picked_at ? ' · ' . \Carbon\Carbon::parse($dn->picked_at)->format('d/m/Y H:i') . ' น.' : '' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    {{-- ===== รายการ PO/สินค้า ของ SO นี้ — แสดงครั้งเดียว ไม่ซ้ำใต้ทุกบิล ===== --}}
                    <div class="dn-section" id="{{ $itemsElId }}" data-dnno="{{ $hostDn->dn_no ?? '' }}" data-selectall="{{ $selectAllId }}">
                        <div class="dn-body">
                            @forelse ($bill->groups->sortByDesc('todo') as $g)
                                <div class="po-row {{ $g->todo ? '' : 'po-row-done' }}">
                                    <div class="po-row-head">
                                        @if ($g->todo)
                                            <input type="checkbox" class="chkGroup" value="{{ $g->type }}:{{ $g->id }}"
                                                   onchange="updateDnButton(document.getElementById('{{ $itemsElId }}'))">
                                        @endif
                                        @if ($sourceLabel($g->type))
                                            <span class="source-tag">{{ $sourceLabel($g->type) }}</span>
                                        @endif
                                        <span class="po-num">{{ $poClean($g->po_display) }}</span>
                                    </div>

                                    <div class="item-col-head"><span>ชื่อสินค้า</span><span>จำนวน</span></div>

                                    @foreach ($g->items as $it)
                                        <div class="item-row">
                                            <div class="item-name">{{ $it->item_name }}</div>
                                            <div class="item-qty">{{ rtrim(rtrim(number_format($it->item_quantity, 2), '0'), '.') }}</div>
                                        </div>
                                    @endforeach

                                    @if ($g->type === 'external')
                                        @php
                                            $locLines = collect($g->items)
                                                ->filter(fn ($it) => ($it->shelf ?? null) || ($it->done_by ?? null))
                                                ->unique(fn ($it) => ($it->shelf ?? '') . '|' . ($it->done_by ?? '') . '|' . ($it->done_at ?? ''))
                                                ->values();
                                        @endphp
                                        @foreach ($locLines as $it)
                                            <div class="item-row-meta">
                                                ที่เก็บ {{ $it->shelf ?? '—' }} · ระบุสถานที่โดย {{ $it->done_by ?? '—' }}
                                                @if (!empty($it->done_at)) {{ \Carbon\Carbon::parse($it->done_at)->format('d/m/Y H:i') }} น. @endif
                                            </div>
                                        @endforeach
                                    @endif

                                    @if ($g->type !== 'external')
                                        <div class="po-row-meta">
                                            ที่เก็บ {{ $g->location ?: '—' }} · ระบุสถานที่โดย {{ $g->done_by ?: '—' }}
                                            @if ($g->done_at) {{ \Carbon\Carbon::parse($g->done_at)->format('d/m/Y H:i') }} น. @endif
                                        </div>
                                    @endif
                                    @if (!$g->todo)
                                        <div class="po-row-meta checkout-meta">
                                            เช็คของออก{{ ($g->checkout_by ?? null) ? ' โดย ' . $g->checkout_by : '' }}
                                            @if ($g->checkout_at ?? null)
                                                {{ \Carbon\Carbon::parse($g->checkout_at)->format('d/m/Y H:i') }} น.
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
        <div class="empty-state">
            ไม่มีบิลที่ตรงกับเงื่อนไขที่ค้นหา
        </div>
    @endif

    @if ($bills->lastPage() > 1)
        <div class="pager">
            @php
                $qs = collect(request()->except('page'))->toArray();
                $mkUrl = fn($p) => url()->current() . '?' . http_build_query(array_merge($qs, ['page' => $p]));
            @endphp
            @if ($bills->currentPage() > 1)
                <a href="{{ $mkUrl(1) }}">« แรก</a>
                <a href="{{ $mkUrl($bills->currentPage() - 1) }}">‹ ก่อนหน้า</a>
            @else
                <span class="disabled">« แรก</span>
                <span class="disabled">‹ ก่อนหน้า</span>
            @endif

            @php
                $start = max(1, $bills->currentPage() - 3);
                $end   = min($bills->lastPage(), $bills->currentPage() + 3);
            @endphp
            @for ($p = $start; $p <= $end; $p++)
                @if ($p == $bills->currentPage())
                    <span class="current">{{ $p }}</span>
                @else
                    <a href="{{ $mkUrl($p) }}">{{ $p }}</a>
                @endif
            @endfor

            @if ($bills->currentPage() < $bills->lastPage())
                <a href="{{ $mkUrl($bills->currentPage() + 1) }}">ถัดไป ›</a>
                <a href="{{ $mkUrl($bills->lastPage()) }}">สุดท้าย »</a>
            @else
                <span class="disabled">ถัดไป ›</span>
                <span class="disabled">สุดท้าย »</span>
            @endif
        </div>
    @endif
</main>
<div class="checkout-floatbar" id="floatBar" hidden>
    <span class="floatbar-count">เลือกไว้ <strong id="floatCount">0</strong> รายการ</span>
    <button type="button" class="btn-ghost" onclick="clearAllChecks()">ล้างที่เลือก</button>
    <button type="button" class="btn-success" id="floatSubmitBtn" onclick="submitAllCheckout()">บันทึกข้อมูล</button>
</div>
<script>
const SUBMIT_URL = "{{ route('store.checkout.submit') }}";
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// เปิด/ปิดการ์ด SO — เรียกจากการคลิก/กดปุ่มที่ "แถบหัวการ์ด" (.so-card-header) ทั้งแถบได้เลย
// ไม่จำเป็นต้องกดที่ขีด (▸) เท่านั้น — คลิกที่ชื่อ SO / ลูกค้า / ป้ายสถานะ ก็ toggle ได้เหมือนกัน
// เปิดได้พร้อมกันหลายใบ ไม่กระทบกัน
function toggleSoCard(id) {
    const card = document.getElementById(id);
    if (!card) return;
    const collapsed = card.classList.toggle('collapsed');
    const header = card.querySelector('.so-card-header');
    if (header) header.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
}

// sync checkbox "เลือกทั้งหมดของ SO นี้" + sync ช่อง "บันทึกผู้จัดบิลนี้" ของบิลอื่นๆ ในการ์ด SO
// เดียวกันให้ "เท่ากัน" ไปด้วย (ติ๊กสินค้าครบทุกชิ้น = ติ๊กทุกบิลของ SO นี้ให้อัตโนมัติ, ไม่ครบ/ยกเลิก = ยกเลิกทุกบิลกลับ)
// ผู้ใช้ยังติ๊กบิลอื่นเองแยกได้ตามปกติ ถ้าจะแก้เฉพาะบิลใดบิลหนึ่งภายหลัง
function updateDnButton(itemsEl) {
    const allBoxes = itemsEl.querySelectorAll('.chkGroup');
    const checked  = itemsEl.querySelectorAll('.chkGroup:checked').length;
    const selectAll = document.getElementById(itemsEl.dataset.selectall);
    let allChecked = false;
    if (selectAll) {
        allChecked = allBoxes.length > 0 && checked === allBoxes.length;
        selectAll.checked       = allChecked;
        selectAll.indeterminate = checked > 0 && checked < allBoxes.length;

        const card = itemsEl.closest('.so-card');
        if (card) {
            card.querySelectorAll('.chkPickOnly').forEach(cb => { cb.checked = allChecked; });
        }
    }
    updateFloatBar();
}

function toggleDnSelectAll(dnEl, checked) {
    dnEl.querySelectorAll('.chkGroup').forEach(cb => { cb.checked = checked; });
    updateDnButton(dnEl);
}

// ทิศทางกลับ: ติ๊กช่อง "บันทึกผู้จัดบิลนี้" ของบิลที่ไม่มีรายการสินค้า (chkPickOnly)
// - ติ๊ก (checked) = ตั้งใจเลือกทั้ง SO นี้ทั้งใบ → sync ไปเช็ครายการสินค้า (chkGroup) ของบิลหลัก +
//   ปุ่มเลือกทั้งหมด + บิลอื่นๆ ในการ์ด SO เดียวกันให้ทั้งหมด
// - ยกเลิกติ๊ก (unchecked) = ต้องการเอาแค่ "บิลนี้บิลเดียว" ออกจากรายการที่จะบันทึก จึงไม่ไล่ยกเลิก
//   บิลอื่น/รายการสินค้าที่เลือกไว้แล้วตามไปด้วย (ไม่งั้นจะเอาบิลใดบิลหนึ่งออกจากชุดที่เลือกไว้ไม่ได้เลย)
function syncCardFromPickOnly(cb) {
    if (!cb.checked) {
        updateFloatBar();
        return;
    }

    const card = cb.closest('.so-card');
    if (!card) { updateFloatBar(); return; }

    card.querySelectorAll('.chkPickOnly').forEach(other => { other.checked = true; });

    const itemsEl = card.querySelector('[data-selectall]');
    if (itemsEl) {
        itemsEl.querySelectorAll('.chkGroup').forEach(g => { g.checked = true; });
        const selectAll = document.getElementById(itemsEl.dataset.selectall);
        if (selectAll) {
            selectAll.checked       = true;
            selectAll.indeterminate = false;
        }
    }

    updateFloatBar();
}

function updateFloatBar() {
    const checkedGroups   = document.querySelectorAll('.chkGroup:checked').length;
    const checkedPickOnly = document.querySelectorAll('.chkPickOnly:checked').length;
    const total = checkedGroups + checkedPickOnly;
    const bar = document.getElementById('floatBar');
    const cnt = document.getElementById('floatCount');
    if (cnt) cnt.textContent = total;
    if (bar) bar.hidden = total === 0;
    document.body.classList.toggle('has-floatbar', total > 0);
}

function clearAllChecks() {
    document.querySelectorAll('.chkGroup:checked').forEach(cb => { cb.checked = false; });
    document.querySelectorAll('.chkPickOnly:checked').forEach(cb => { cb.checked = false; });
    document.querySelectorAll('.dnSelectAll').forEach(cb => { cb.checked = false; cb.indeterminate = false; });
    updateFloatBar();
}

async function submitAllCheckout() {
    const checkedBoxes  = Array.from(document.querySelectorAll('.chkGroup:checked'));
    const pickOnlyBoxes = Array.from(document.querySelectorAll('.chkPickOnly:checked'));
    if (!checkedBoxes.length && !pickOnlyBoxes.length) return;

    const ids = Array.from(new Set(checkedBoxes.map(c => c.value)));

    const dnNos = Array.from(new Set(
        checkedBoxes.map(c => c.closest('.dn-section')?.dataset.dnno)
            .concat(pickOnlyBoxes.map(c => c.closest('.dn-section')?.dataset.dnno))
            .filter(Boolean)
    ));

    const totalCount = ids.length + pickOnlyBoxes.length;
    if (!confirm('ยืนยันบันทึกข้อมูล ' + totalCount + ' รายการ')) return;

    const btn = document.getElementById('floatSubmitBtn');
    btn.disabled = true;
    try {
        const res = await fetch(SUBMIT_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ ids, dn_nos: dnNos }),
        });
        const data = await res.json();
        if (res.ok && data.ok) {
            window.location.reload();
        } else {
            alert(data.message || 'บันทึกไม่สำเร็จ');
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