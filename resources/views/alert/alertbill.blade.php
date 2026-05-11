<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งเตือนบิลที่ยังไม่มีในระบบ</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.blade.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; border-radius: 0 !important; }

        :root {
            --bg: #f6f7f9;
            --surface: #fff;
            --surface-soft: #fbfcfd;
            --ink: #0f172a;
            --ink-2: #334155;
            --ink-3: #64748b;
            --ink-4: #94a3b8;
            --line: #e6e9ef;
            --line-soft: #eef1f5;
            --line-strong: #d4d9e2;
            --green: #3a7355;
            --green-2: #2d5a42;
            --green-soft: #ecf5ef;
            --green-tint: #d4e9dc;
            --rose: #c0392b;
            --rose-soft: #fdecea;
            --shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        body {
            font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--ink);
            font-size: 14px;
            line-height: 1.55;
        }

        .mono { font-family: 'JetBrains Mono', ui-monospace, monospace; }

        /* ============ TOP BAR ============ */
        .topbar {
            background: var(--green);
            border-bottom: 3px solid var(--green-2);
            padding: 0 24px;
            min-height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .brand-text { line-height: 1.2; }
        .brand-text .name { display: block; font-weight: 600; font-size: 16px; color: #fff; }
        .brand-text .sub { font-size: 11px; color: rgba(255, 255, 255, 0.75); letter-spacing: 0.04em; text-transform: uppercase; }

        .topbar-nav { display: flex; align-items: center; gap: 8px; }

        .user-info {
            display: flex; align-items: center; gap: 6px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            padding-right: 4px;
        }

        .user-info::before {
            content: "";
            width: 16px; height: 16px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2'/%3E%3Ccircle cx='12' cy='7' r='4'/%3E%3C/svg%3E") no-repeat center;
            opacity: 0.85;
        }

        .user-info b { color: #fff; font-weight: 500; }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            padding: 7px 14px;
            color: #fff;
            background: var(--rose);
            border: 1px solid var(--rose);
            transition: background 0.15s;
        }

        .nav-btn:hover { background: #a93226; border-color: #a93226; }

        /* ============ PAGE ============ */
        .page { max-width: 1280px; margin: 0 auto; padding: 20px 24px 64px; }

        /* ============ TOOLBAR ROW ============ */
        .toolbar-row {
            display: grid;
            grid-template-columns: minmax(320px, auto) 1fr 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .toolbar-row.no-stats { grid-template-columns: 1fr; }

        .card {
            background: var(--surface);
            border: 1px solid var(--line-strong);
            padding: 14px 16px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 96px;
        }

        .filter-card { justify-content: center; }

        .filter-form {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .field { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 140px; }
        .field label { font-size: 12px; font-weight: 500; color: var(--ink-3); }

        .field input[type="date"] {
            padding: 8px 12px;
            border: 1px solid var(--line-strong);
            font-family: inherit;
            font-size: 13px;
            background: var(--surface);
            color: var(--ink);
            outline: none;
            height: 36px;
            width: 100%;
        }

        .field input[type="date"]:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(58, 115, 85, 0.15);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 36px;
            padding: 0 14px;
            font-family: inherit;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            border: 1px solid transparent;
            transition: background 0.15s, border-color 0.15s;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-primary { background: var(--green); color: #fff; border-color: var(--green-2); }
        .btn-primary:hover { background: var(--green-2); }

        .btn-ghost { background: var(--surface); color: var(--ink-2); border-color: var(--line-strong); }
        .btn-ghost:hover { background: #f3f5f8; color: var(--ink); }

        .btn-remove {
            background: var(--rose);
            color: #fff;
            border-color: var(--rose);
            height: 30px;
            padding: 0 12px;
            font-size: 12.5px;
        }

        .btn-remove:hover:not(:disabled) { background: #a93226; border-color: #a93226; }

        .btn-remove:disabled {
            background: #d5d8dc;
            border-color: #d5d8dc;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .btn-count {
            background: rgba(255, 255, 255, 0.25);
            padding: 1px 7px;
            font-size: 11px;
            font-weight: 700;
            min-width: 20px;
            text-align: center;
        }

        /* Stat cards */
        .stat-card .label { font-size: 12px; font-weight: 500; color: var(--ink-3); margin-bottom: 6px; }
        .stat-card .value { font-size: 22px; font-weight: 700; line-height: 1.15; font-variant-numeric: tabular-nums; }
        .stat-card .meta { font-size: 11.5px; color: var(--ink-3); margin-top: 2px; }

        /* ============ ALERT ============ */
        .alert {
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 13px;
            border: 1px solid #f5b7b1;
            background: var(--rose-soft);
            color: #922b21;
        }

        /* ============ TABLE ============ */
        .table-card {
            background: var(--surface);
            border: 1px solid var(--line-strong);
            box-shadow: var(--shadow);
        }

        .table-toolbar {
            padding: 12px 16px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            background: var(--surface-soft);
        }

        .toolbar-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .toolbar-title { font-size: 14px; font-weight: 600; }

        .pill {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            font-size: 12px;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
            border: 1px solid;
        }

        .pill-rose { background: var(--rose-soft); color: var(--rose); border-color: #f5b7b1; }
        .pill-emerald { background: var(--green-soft); color: var(--green-2); border-color: var(--green-tint); }
        .pill-neutral { background: #f3f5f8; color: var(--ink-2); border-color: var(--line-strong); }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }

        thead th {
            background: var(--green);
            color: #fff;
            font-weight: 600;
            padding: 10px 16px;
            text-align: left;
            font-size: 12px;
            letter-spacing: 0.04em;
            white-space: nowrap;
            border-right: 1px solid rgba(255, 255, 255, 0.15);
        }

        thead th:last-child { border-right: none; }
        thead th.th-check { text-align: center; width: 44px; padding: 10px 12px; }
        thead th.th-check + th { text-align: center; width: 70px; }

        .td-check { text-align: center; width: 44px; padding: 10px 12px !important; }

        .row-check {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: var(--green);
            vertical-align: middle;
        }

        .check-all-input { accent-color: #fff; }

        tbody tr {
            border-bottom: 1px solid var(--line-soft);
            transition: background 0.12s;
        }

        tbody tr:nth-child(even) { background: #fafbfc; }
        tbody tr:hover { background: #fff9c4; }
        tbody tr.row-selected { background: #fffde7 !important; }
        tbody tr.row-selected:hover { background: #fff9c4 !important; }

        tbody td {
            padding: 10px 16px;
            font-size: 13.5px;
            border-right: 1px solid var(--line-soft);
        }

        tbody td:last-child { border-right: none; }

        tbody td:nth-child(2) {
            text-align: center;
            color: var(--ink-4);
            font-weight: 500;
            font-variant-numeric: tabular-nums;
            font-size: 12.5px;
        }

        .doc-no {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-weight: 600;
            color: var(--green-2);
        }

        .doc-no::before {
            content: "";
            display: inline-block;
            width: 3px;
            height: 16px;
            background: var(--green);
        }

        .so-no {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-size: 12.5px;
            color: var(--ink-2);
            background: var(--surface-soft);
            padding: 3px 8px;
            border: 1px solid var(--line);
        }

        .date-cell { font-variant-numeric: tabular-nums; color: var(--ink-2); font-weight: 500; }

        /* Empty state */
        .empty-row td { padding: 48px 20px !important; text-align: center; color: var(--ink-3); }
        .empty-row .icon { font-size: 32px; color: var(--green-2); margin-bottom: 8px; }
        .empty-row .t { font-size: 15px; font-weight: 600; color: var(--ink); margin-bottom: 4px; }

        /* Footer */
        .table-footer {
            padding: 12px 16px;
            border-top: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            background: var(--surface-soft);
            font-size: 12.5px;
            color: var(--ink-3);
        }

        .table-footer strong { color: var(--ink); font-variant-numeric: tabular-nums; }

        /* Pagination overrides */
        nav[role="navigation"] > div > p,
        nav[role="navigation"] .sm\:hidden { display: none !important; }

        nav[role="navigation"] > div {
            display: flex !important;
            align-items: center !important;
            gap: 2px;
            flex-wrap: nowrap !important;
        }

        nav[role="navigation"] a[rel="prev"] span,
        nav[role="navigation"] a[rel="next"] span { display: none !important; }

        nav[role="navigation"] a[rel="prev"],
        nav[role="navigation"] a[rel="next"],
        nav[role="navigation"] a.relative,
        nav[role="navigation"] span.relative {
            min-width: 30px;
            height: 30px;
            display: flex !important;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 500;
            color: var(--ink-2);
            background: var(--surface) !important;
            border: 1px solid var(--line-strong) !important;
            padding: 0 10px !important;
        }

        nav[role="navigation"] a:hover {
            background: var(--green-soft) !important;
            border-color: var(--green) !important;
            color: var(--green-2) !important;
        }

        nav[role="navigation"] svg { width: 13px !important; height: 13px !important; }

        nav[role="navigation"] span[aria-current="page"] {
            color: #fff !important;
            background: var(--green) !important;
            border-color: var(--green-2) !important;
        }

        /* Responsive */
        @media (max-width: 1100px) {
            .toolbar-row { grid-template-columns: 1fr 1fr; }
            .toolbar-row .filter-card { grid-column: span 2; }
        }

        @media (max-width: 768px) {
            .topbar { padding: 10px 14px; flex-direction: column; align-items: stretch; gap: 10px; }
            .topbar-nav { width: 100%; justify-content: space-between; flex-wrap: wrap; }
            .page { padding: 16px 14px 48px; }
            .toolbar-row,
            .toolbar-row.no-stats { grid-template-columns: 1fr; }
            .toolbar-row .filter-card { grid-column: span 1; }
            .filter-form { flex-direction: column; align-items: stretch; }
            .field { min-width: 0; }
            .btn { width: 100%; }
            thead th, tbody td { padding: 8px 12px; font-size: 12.5px; }
            .table-footer { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>

    @php $userName = request()->get('create_by', 'Guest'); @endphp

    <header class="topbar">
        <div class="brand-text">
            <span class="name">ระบบตรวจสอบบิล</span>
        </div>

        <nav class="topbar-nav">
            <div class="user-info">ผู้ใช้: <b>{{ $userName }}</b></div>
            <a href="http://server_update:8000/solist" class="nav-btn">หน้าหลัก</a>
            <a href="{{ route('sale.dashboard') }}" class="nav-btn">ข้อมูลจัดส่ง</a>
        </nav>
    </header>

    <main class="page">

        <section class="toolbar-row {{ ($date && !$error) ? '' : 'no-stats' }}">
            <div class="card filter-card">
                <form method="GET" action="{{ route('alert.alertbill') }}" class="filter-form">
                    <div class="field">
                        <label for="date">วันที่ตรวจสอบ</label>
                        <input type="date" id="date" name="date"
                            value="{{ $date ?? \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">ตรวจสอบ</button>
                    <a href="{{ route('alert.alertbill') }}" class="btn btn-ghost">รีเซ็ต</a>
                </form>
            </div>

            @if($date && !$error)
                <div class="card stat-card">
                    <div class="label">วันที่ตรวจสอบ</div>
                    <div>
                        <div class="value">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</div>
                        <div class="meta">{{ \Carbon\Carbon::parse($date)->locale('th')->translatedFormat('l') }}</div>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="label">บิลที่ขาดในระบบ</div>
                    <div>
                        <div class="value">{{ number_format($missingBills->total()) }}</div>
                        <div class="meta">{{ $missingBills->total() == 0 ? 'ข้อมูลครบทุกใบ' : 'รายการที่ต้องดำเนินการ' }}</div>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="label">ตรวจสอบล่าสุด</div>
                    <div>
                        <div class="value mono">{{ \Carbon\Carbon::now()->format('H:i') }}</div>
                        <div class="meta">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
                    </div>
                </div>
            @endif
        </section>

        @if($error)
            <div class="alert">{{ $error }}</div>
        @endif

        @if($date && !$error)
            <section class="table-card">
                <div class="table-toolbar">
                    <div class="toolbar-left">
                        <span class="toolbar-title">รายการบิลที่ขาด</span>
                        <button type="button" id="btn-remove" class="btn btn-remove" disabled>
                            ✕ ลบที่เลือก
                            <span class="btn-count" id="count">0</span>
                        </button>
                        @if($missingBills->total() == 0)
                            <span class="pill pill-emerald">ข้อมูลครบ</span>
                        @else
                            <span class="pill pill-rose">{{ number_format($missingBills->total()) }} รายการ</span>
                        @endif
                    </div>
                    <span class="pill pill-neutral mono">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th class="th-check"><input type="checkbox" id="check-all" class="row-check check-all-input"></th>
                                <th>ลำดับ</th>
                                <th>เลขที่บิล</th>
                                <th>อ้างอิงใบสั่งขาย</th>
                                <th>วันที่เอกสาร</th>
                            </tr>
                        </thead>
                        <tbody>
                                @forelse($missingBills as $i => $item)
                                    <tr data-billid="{{ $item['DocuNo'] ?? '' }}"
                                        data-sono="{{ $item['SONo'] ?? '' }}"
                                        data-docudate="{{ $item['DocuDate'] ?? '' }}">
                                        <td class="td-check">
                                            <input type="checkbox" class="row-check" value="{{ $item['DocuNo'] ?? '' }}">
                                        </td>
                                        <td>{{ ($missingBills->firstItem() ?? 0) + $i }}</td>
                                        <td><span class="doc-no">{{ $item['DocuNo'] ?? '-' }}</span></td>
                                        <td><span class="so-no">{{ $item['SONo'] ?? '-' }}</span></td>
                                        <td><span class="date-cell">{{ isset($item['DocuDate']) ? \Carbon\Carbon::parse($item['DocuDate'])->format('d/m/Y') : '-' }}</span></td>
                                    </tr>
                                @empty
                                <tr class="empty-row">
                                    <td colspan="5">
                                        <div class="icon">✓</div>
                                        <div class="t">ข้อมูลครบทุกใบ</div>
                                        <div>ไม่พบบิลที่ขาดในระบบสำหรับวันที่ที่ระบุ</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($missingBills->total() > 0)
                    <div class="table-footer">
                        <span>
                            แสดง <strong>{{ $missingBills->firstItem() ?? 0 }}</strong>
                            – <strong>{{ $missingBills->lastItem() ?? 0 }}</strong>
                            จากทั้งหมด <strong>{{ number_format($missingBills->total()) }}</strong> รายการ
                        </span>
                        {{ $missingBills->appends(request()->query())->links() }}
                    </div>
                @endif
            </section>
        @elseif(!$error)
            <div class="card" style="text-align:center; padding:48px 20px; border-style:dashed; color:var(--ink-3);">
                กรุณาเลือกวันที่ที่ต้องการตรวจสอบ
            </div>
        @endif
    </main>

    <script>
    (function() {
        const checkAll = document.getElementById('check-all');
        const btnRemove = document.getElementById('btn-remove');
        const countLabel = document.getElementById('count');
        const tbody = document.querySelector('tbody');
        if (!tbody) return;

        const firstItemNo = {{ $missingBills->firstItem() ?? 1 }};

        function getRowChecks() {
            return tbody.querySelectorAll('.row-check');
        }

        function getCheckedRows() {
            return tbody.querySelectorAll('.row-check:checked');
        }

        function highlightRow(cb) {
            const row = cb.closest('tr');
            if (row) row.classList.toggle('row-selected', cb.checked);
        }

        function syncCheckAll() {
            if (!checkAll) return;
            const all = getRowChecks();
            const checked = getCheckedRows().length;
            checkAll.checked = all.length > 0 && checked === all.length;
            checkAll.indeterminate = checked > 0 && checked < all.length;
        }

        function updateUI() {
            const n = getCheckedRows().length;
            if (countLabel) countLabel.textContent = n;
            if (btnRemove) btnRemove.disabled = n === 0;
        }

        function renumber() {
            tbody.querySelectorAll('tr:not(.empty-row)').forEach(function(row, i) {
                const cell = row.querySelector('td:nth-child(2)');
                if (cell) cell.textContent = firstItemNo + i;
            });
        }

        // Check all
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                getRowChecks().forEach(function(cb) {
                    cb.checked = checkAll.checked;
                    highlightRow(cb);
                });
                updateUI();
            });
        }

        // Per-row check (event delegation)
        tbody.addEventListener('change', function(e) {
            if (e.target.classList.contains('row-check')) {
                highlightRow(e.target);
                syncCheckAll();
                updateUI();
            }
        });

        // Remove selected
       if (btnRemove) {
    btnRemove.addEventListener('click', async function() {
        const selected = getCheckedRows();
        if (!selected.length) return;
        if (!confirm('ยืนยันลบรายการที่เลือก ' + selected.length + ' รายการ?')) return;

        // เก็บข้อมูลจากแต่ละแถวที่ถูกเลือก
        const bills = Array.from(selected).map(function(cb) {
            const row = cb.closest('tr');
            return {
                billid:    row.dataset.billid    || '',
                sono:      row.dataset.sono      || '',
                docu_date: row.dataset.docudate  || '',
            };
        }).filter(b => b.billid);

        const userName = @json($userName ?? 'Guest');
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        btnRemove.disabled = true;
        const originalLabel = btnRemove.innerHTML;
        btnRemove.innerHTML = 'กำลังบันทึก...';

        try {
            const res = await fetch('{{ route('alert.removeBills') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ bills: bills }),
            });

            const data = await res.json();

            if (!res.ok || data.status !== 'success') {
                alert(data.message || 'เกิดข้อผิดพลาด');
                btnRemove.innerHTML = originalLabel;
                btnRemove.disabled = false;
                return;
            }

            // ลบแถวออกจาก DOM
            selected.forEach(function(cb) { cb.closest('tr').remove(); });
            renumber();

            if (!tbody.querySelectorAll('tr:not(.empty-row)').length) {
                tbody.innerHTML = '<tr class="empty-row"><td colspan="5">' +
                    '<div class="icon">✓</div>' +
                    '<div class="t">ไม่มีรายการคงเหลือ</div>' +
                    '<div>รายการทั้งหมดถูกลบและบันทึกเรียบร้อย</div>' +
                    '</td></tr>';
            }

            if (checkAll) { checkAll.checked = false; checkAll.indeterminate = false; }
            btnRemove.innerHTML = originalLabel;
            updateUI();
        } catch (err) {
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + err.message);
            btnRemove.innerHTML = originalLabel;
            btnRemove.disabled = false;
        }
    });
}
    </script>

</body>
</html>