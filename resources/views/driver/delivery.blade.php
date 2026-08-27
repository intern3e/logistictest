@if (!empty($printMode))
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<style>
    @page { size: A4; margin: 8mm; }
    body { font-family: 'Sarabun', 'Segoe UI', Tahoma, Arial, sans-serif; font-size: 20px; color: #1e293b; margin: 0; padding: 0; width: 100%; }

    .print-header-wrap { border-bottom: 2px solid #1e293b; padding-bottom: 10px; margin-bottom: 14px; }
    .print-header-table { width: 100%; border-collapse: collapse; }
    .print-header-table td { border: none; padding: 0; vertical-align: top; }
    .print-header-table h1 { font-size: 20px; margin: 0; }
    .print-header-table .sub { font-size: 20px; color: #4b5563; margin-top: 4px; }
    .print-header-table .meta-cell { text-align: right; font-size: 20px; color: #4b5563; white-space: nowrap; }

    .cust-block { margin-bottom: 14px; border: 1px solid #dcdcdc; page-break-inside: avoid; width: 100%; }
    .cust-block-header { background: #f3f4f6; padding: 10px; border-bottom: 1px solid #dcdcdc; }
    .cust-header-table { width: 100%; border-collapse: collapse; }
    .cust-header-table td { border: none; padding: 0; vertical-align: middle; }
    .stop-no-cell { width: 44px; font-size: 30px; font-weight: 800; color: #1e293b; text-align: center; }
    .cust-info-cell { vertical-align: top; }
    .cust-info-cell .cust-title { font-weight: 700; font-size: 20px; }
    .cust-info-cell .cust-address { display: block; margin-top: 4px; font-weight: 400; color: #374151; font-size: 20px; }
    .cust-qr-cell { width: 60px; text-align: center; vertical-align: top; }
    .cust-qr-cell img { display: block; }
    .cust-qr-cell .qr-label { font-size: 20px; color: #6b7280; margin-top: 2px; }

    table.job-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    th, td { border-bottom: 1px solid #eeeeea; padding: 6px 10px; text-align: left; font-size: 20px; word-wrap: break-word; page-break-inside: avoid; }
    th { background: #fafbfd; font-weight: 700; }
    tr { page-break-inside: avoid; }
</style>
</head>
<body>
    <div class="print-header-wrap">
        <table class="print-header-table">
            <tr>
                <td><h1>ใบงานขนส่ง</h1></td>
                <td class="meta-cell">วันที่: {{ $date }}</td>
            </tr>
            <tr>
                <td class="sub">
                    วิธีการจัดส่ง: {{ $box['transport_name'] }}
                    @if (!empty($box['driver_name']))
                        &nbsp;x&nbsp;ผู้รับผิดชอบ: {{ $box['driver_name'] }}
                    @else
                        &nbsp;(ไม่ระบุผู้รับผิดชอบ)
                    @endif
                </td>
                <td class="meta-cell">
                    พิมพ์โดย: {{ $printedBy }}<br>
                    เวลาพิมพ์: {{ $printedAt->format('d/m/Y H:i') }} น.
                </td>
            </tr>
        </table>
    </div>

    @forelse ($box['customers'] as $cust)
        <div class="cust-block">
            <div class="cust-block-header">
                <table class="cust-header-table">
                    <tr>
                        <td class="stop-no-cell">{{ $cust['stop_no'] }}</td>
                        <td class="cust-info-cell">
                            <span class="cust-title">
                                {{ $cust['customer_code'] }}
                                @if (!empty($cust['customer_name'])) - {{ $cust['customer_name'] }} @endif
                            </span>
                            <span class="cust-address">{{ $cust['address'] }}</span>
                        </td>
                        @if (!empty($cust['lalong']))
                            <td class="cust-qr-cell">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data={{ urlencode('https://www.google.com/maps/search/?api=1&query=' . $cust['lalong']) }}"
                                     width="60" height="60">
                                <div class="qr-label">สถานที่ส่ง</div>
                            </td>
                        @endif
                    </tr>
                </table>
            </div>
            <table class="job-table">
                <thead>
                    <tr>
                        <th style="width:15%">ลำดับงาน</th>
                        <th style="width:30%">เลขที่บิล</th>
                        <th>หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cust['items'] as $item)
                        <tr>
                            <td>{{ $item['seq'] }}</td>
                            <td>{{ $item['bill_no'] }}</td>
                            <td>{{ $item['notes'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <p>ไม่พบรายการ</p>
    @endforelse
</body>
</html>
@else
{{-- ============================================================
     โหมดหน้าเว็บปกติ — จัดรถขนส่งสินค้า / จ่ายงาน
     ============================================================ --}}
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดรถขนส่งสินค้า</title>
    {{-- ถ้าโปรเจกต์ของคุณมี layout หลักอยู่แล้ว แนะนำให้ย้าย
         ส่วนเนื้อหาไปแทรกในนั้น แล้วลบ <html><head><body> ส่วนนี้ทิ้ง --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    :root{
        --ink:#1e293b; --canvas:#ffffff; --muted:#6b7280; --faint:#9ca3af; --border:#dcdcdc;
        --primary:#2563eb; --primary-dark:#1d4ed8; --primary-light:#eff6ff;
        --on-primary:#ffffff; --success:#16a34a; --success-dark:#15803d; --success-light:#dcfce7;
        --danger:#dc2626; --danger-dark:#b91c1c; --danger-light:#fee2e2;
    }
    body { background:#f6f7f9; padding-bottom:96px; }

    .section-heading { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
    .section-heading h5 { margin:0; }

    .date-hint { font-size:13px; color:var(--muted); margin-left:8px; }

    .cust-grid {
        display:grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap:14px;
        margin-bottom:28px;
        align-items:start;
    }
    @media (max-width:991px){
        .cust-grid { grid-template-columns:1fr; }
    }

    .cust-card {
        background:var(--canvas); border:1px solid var(--border); border-left:4px solid var(--border);
        overflow:hidden; min-width:0;
    }
    .cust-card[data-has-unassigned="0"] { border-left-color:var(--success); }

    .cust-card-header {
        display:flex; align-items:flex-start; gap:10px; cursor:pointer; user-select:none;
        padding:12px 14px; border-bottom:1px solid var(--border); background:#fafbfd;
    }
    .cust-toggle {
        color:var(--faint); font-size:15px; margin-top:2px; flex-shrink:0; transition:transform .12s ease;
    }
    .cust-card:not(.collapsed) .cust-toggle { transform:rotate(90deg); }
    .cust-card.collapsed .cust-card-body { display:none; }

    .cust-head-main { flex:1; min-width:0; }
    .cust-id-name { font-weight:800; font-size:17px; color:var(--ink); word-break:break-word; }
    .cust-id-name .cust-name-part { font-weight:700; color:var(--primary-dark); }
    .cust-count-badge {
        flex-shrink:0; font-size:12.5px; font-weight:700; padding:4px 10px;
        background:var(--primary-light); color:var(--primary-dark); white-space:nowrap;
    }

    .cust-card-body { padding:6px 14px 12px; }

    .job-block { padding:10px 0; border-top:1px solid #eeeeea; }
    .job-block:first-child { border-top:none; }

    .job-block.is-assigned {
        background:var(--success-light);
        padding:10px 10px;
        margin:6px 0;
        border-top:none;
    }
    .job-block.is-assigned .job-no { color:var(--success-dark); }
    .job-block.is-assigned .job-block-head { padding-left:0; }

    .job-block-head { display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:4px; }
    .job-block-head input[type="checkbox"] { width:18px; height:18px; accent-color:var(--primary); flex-shrink:0; }
    .job-no { font-weight:700; font-size:14.5px; color:var(--ink); word-break:break-word; }

    .job-address {
        font-size:13.5px; color:var(--ink); background:#f8f9fb; border:1px solid var(--border);
        padding:8px 10px; margin:4px 0; word-break:break-word;
    }
    .job-block.is-assigned .job-address { background:#fff; }
    .job-address a { display:inline-block; margin-top:6px; }

    .job-meta { font-size:13px; color:var(--muted); margin-top:2px; }

    .job-driver { font-size:13px; font-weight:600; margin-top:2px; color:var(--muted); }
    .job-driver.assigned { color:var(--success-dark); }

    .job-notes { font-size:12.5px; color:var(--faint); margin-top:2px; }

    .empty-note { font-size:14px; color:var(--muted); padding:14px; border:1px dashed var(--border); background:#fff; margin-bottom:24px; }
    .no-date-note { font-size:15px; color:var(--muted); padding:32px 16px; border:1px dashed var(--border); background:#fff; text-align:center; margin-bottom:24px; }

    .dispatch-box .cust-id-name { font-size:16px; }
    .dispatch-box .cust-name-part { color:var(--success-dark); }

    .save-floatbar {
        position:fixed; right:20px; bottom:20px; z-index:1050;
        display:flex; align-items:center; gap:10px;
    }
    .save-floatbar .floatbar-count {
        font-size:14.5px; font-weight:700; color:var(--ink);
        background:var(--canvas); border:1px solid var(--border);
        padding:9px 14px; box-shadow:0 2px 10px rgba(0,0,0,.12); white-space:nowrap;
    }
    .save-floatbar .floatbar-count strong { color:var(--success-dark); }
    .save-floatbar button { box-shadow:0 2px 10px rgba(0,0,0,.15); }

    .required-mark { color:var(--danger); }
    .optional-hint { color:var(--muted); font-weight:400; font-size:13px; }

    /* ===== Custom autocomplete dropdown (แทน native datalist ที่หลุดตำแหน่งใน modal) ===== */
    .autocomplete-list {
        position:absolute; top:100%; left:0; right:0; z-index:3000;
        max-height:220px; overflow-y:auto; background:var(--canvas);
        border:1px solid var(--border); border-top:none;
        box-shadow:0 6px 14px rgba(0,0,0,.1); display:none;
    }
    .autocomplete-item { padding:8px 12px; font-size:14px; cursor:pointer; }
    .autocomplete-item:hover { background:var(--primary-light); }
    .autocomplete-empty { padding:8px 12px; font-size:13px; color:var(--faint); }
</style>
</head>
<body class="bg-light">
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <h3 class="mb-0">จัดรถขนส่งสินค้า</h3>
        <div class="text-end">
            <div class="text-muted">ผู้ใช้งาน: {{ $loggedInName }}</div>
            <a href="{{ route('logout') }}" class="small">ออกจากระบบ</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- ฟอร์มค้นหา/กรองตามวันที่ — ต้องเลือกก่อนถึงจะแสดงข้อมูล --}}
    <form method="GET" action="{{ route('deliverytrack') }}" class="row g-2 mb-4 align-items-end">
        <div class="col-auto">
            <label class="form-label mb-1">เลือกวันที่</label>
            <input type="date" name="date" value="{{ $date }}" class="form-control" required>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">ค้นหา</button>
            @if (!$hasDate)
                <span class="date-hint">* ต้องเลือกวันที่ก่อน ข้อมูลถึงจะแสดง</span>
            @endif
        </div>
    </form>

    @if (!$hasDate)
        <div class="no-date-note">
            กรุณาเลือกวันที่ด้านบนแล้วกด "ค้นหา" เพื่อแสดงรายการงานขนส่ง
        </div>
    @else
        <form id="dispatchForm" method="POST" action="{{ route('deliverytrack.store') }}">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <div id="jobInputs"></div>

            {{-- ===== รายการบิล (มีคนหยิบสินค้าแล้ว) ===== --}}
            <div class="section-heading">
                <h5>รายการบิล (มีคนหยิบสินค้าแล้ว) — {{ $bills->count() }} รายการ</h5>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="checkAllBills">
                    <label class="form-check-label" for="checkAllBills">เลือกทั้งหมด (บิล)</label>
                </div>
            </div>

            @if (count($billGroups) > 0)
                <div class="cust-grid">
                    @foreach ($billGroups as $group)
                        @php
                            $hasUnassigned = collect($group['rows'])->contains(fn ($row) => !$row['assigned']);
                            $custKey = 'bill_cust_' . preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $group['customer_id']);
                        @endphp
                        <div class="cust-card collapsed" id="{{ $custKey }}" data-has-unassigned="{{ $hasUnassigned ? 1 : 0 }}">
                            <div class="cust-card-header" onclick="toggleCustCard('{{ $custKey }}')">
                                <span class="cust-toggle">▸</span>
                                <div class="cust-head-main">
                                    <div class="cust-id-name">
                                        {{ $group['customer_id'] }}
                                        @if (!empty($group['customer_name']))
                                            <span class="cust-name-part"> - {{ $group['customer_name'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="cust-count-badge">{{ count($group['rows']) }} บิล</span>
                            </div>
                            <div class="cust-card-body">
                                @foreach ($group['rows'] as $row)
                                    @php $bill = $row['bill']; $assigned = $row['assigned']; @endphp
                                    <div class="job-block {{ $assigned ? 'is-assigned' : '' }}">
                                        <div class="job-block-head">
                                            @unless ($assigned)
                                                <input type="checkbox" class="job-checkbox bill-checkbox"
                                                       value="bill:{{ $bill->so_detail_id }}"
                                                       onchange="updateSelectedCount()">
                                            @endunless
                                            <span class="job-no">SO {{ $bill->so_id }} · บิล {{ $bill->billid }}</span>
                                            @if ($assigned)
                                                <span class="badge" style="background:var(--success);">สำเร็จ</span>
                                            @endif
                                        </div>

                                        <div class="job-address">
                                            {{ $bill->customer_address }}
                                            @if (!empty($bill->customer_la_long))
                                                <br>
                                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($bill->customer_la_long) }}"
                                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                                    เปิดแผนที่
                                                </a>
                                            @endif
                                        </div>

                                        <div class="job-meta">
                                            ผู้หยิบสินค้า: {{ $bill->emp_picker ?: '-' }} · เวลา {{ $bill->time }}
                                        </div>

                                        <div class="job-driver {{ $assigned ? 'assigned' : '' }}">
                                            ผู้รับผิดชอบ: {{ $assigned->driver_name ?? '-' }}
                                        </div>

                                        @if (!empty($bill->notes))
                                            <div class="job-notes">หมายเหตุ: {{ $bill->notes }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-note">ไม่มีรายการในวันที่เลือก</div>
            @endif

            {{-- ===== รายการเอกสาร (สถานะรอดำเนินการ) ===== --}}
            <div class="section-heading">
                <h5>รายการเอกสาร (สถานะรอดำเนินการ) — {{ $docbills->count() }} รายการ</h5>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="checkAllDocs">
                    <label class="form-check-label" for="checkAllDocs">เลือกทั้งหมด (เอกสาร)</label>
                </div>
            </div>

            @if (count($docGroups) > 0)
                <div class="cust-grid">
                    @foreach ($docGroups as $group)
                        @php
                            $hasUnassigned = collect($group['rows'])->contains(fn ($row) => !$row['assigned']);
                            $custKey = 'doc_cust_' . preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $group['customer_id']);
                        @endphp
                        <div class="cust-card collapsed" id="{{ $custKey }}" data-has-unassigned="{{ $hasUnassigned ? 1 : 0 }}">
                            <div class="cust-card-header" onclick="toggleCustCard('{{ $custKey }}')">
                                <span class="cust-toggle">▸</span>
                                <div class="cust-head-main">
                                    <div class="cust-id-name">
                                        {{ $group['customer_id'] }}
                                        @if (!empty($group['customer_name']))
                                            <span class="cust-name-part"> - {{ $group['customer_name'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="cust-count-badge">{{ count($group['rows']) }} เอกสาร</span>
                            </div>
                            <div class="cust-card-body">
                                @foreach ($group['rows'] as $row)
                                    @php $doc = $row['doc']; $assigned = $row['assigned']; @endphp
                                    <div class="job-block {{ $assigned ? 'is-assigned' : '' }}">
                                        <div class="job-block-head">
                                            @unless ($assigned)
                                                <input type="checkbox" class="job-checkbox doc-checkbox"
                                                       value="doc:{{ $doc->doc_id }}"
                                                       onchange="updateSelectedCount()">
                                            @endunless
                                            <span class="job-no">เอกสาร {{ $doc->doc_id }} · {{ $doc->contact_name }}</span>
                                            @if ($assigned)
                                                <span class="badge" style="background:var(--success);">สำเร็จ</span>
                                            @endif
                                        </div>

                                        <div class="job-address">
                                            {{ $doc->com_address }}
                                            @if (!empty($doc->com_la_long))
                                                <br>
                                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($doc->com_la_long) }}"
                                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                                    เปิดแผนที่
                                                </a>
                                            @endif
                                        </div>

                                        <div class="job-meta">
                                            ผู้บันทึก: {{ $doc->emp_name ?: '-' }} · เวลา {{ $doc->time }}
                                        </div>

                                        <div class="job-driver {{ $assigned ? 'assigned' : '' }}">
                                            ผู้รับผิดชอบ: {{ $assigned->driver_name ?? '-' }}
                                        </div>

                                        @if (!empty($doc->notes))
                                            <div class="job-notes">หมายเหตุ: {{ $doc->notes }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-note">ไม่มีรายการในวันที่เลือก</div>
            @endif
        </form>

        <hr class="my-5">

        <h4 class="mb-3">งานที่จัดส่งแล้ว (วันที่ {{ $date }})</h4>

        @if (count($dispatchBoxes) > 0)
            <div class="cust-grid">
                @foreach ($dispatchBoxes as $box)
                    @php
                        $boxKey = 'box_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $box['transport_name'] . '_' . ($box['driver_name'] ?? 'none'));
                        $printUrl = route('deliverytrack.printGroup', [
                            'date'      => $date,
                            'transport' => $box['transport_name'],
                            'driver'    => $box['driver_name'],
                        ]);
                    @endphp
                    <div class="cust-card collapsed dispatch-box" id="{{ $boxKey }}">
                        <div class="cust-card-header" onclick="toggleCustCard('{{ $boxKey }}')">
                            <span class="cust-toggle">▸</span>
                            <div class="cust-head-main">
                                <div class="cust-id-name">
                                    {{ $box['transport_name'] }}
                                    @if (!empty($box['driver_name']))
                                        <span class="cust-name-part"> x {{ $box['driver_name'] }}</span>
                                    @else
                                        <span class="optional-hint"> (ไม่ระบุผู้รับผิดชอบ)</span>
                                    @endif
                                </div>
                            </div>
                            <span class="cust-count-badge">{{ $box['total_items'] }} รายการ</span>
                        </div>
                        <div class="cust-card-body">
                            <div class="job-meta mb-2">ผู้จ่ายงาน: {{ $box['assigned_by'] ?: '-' }}</div>
                            @foreach ($box['customers'] as $cust)
                                <div class="job-block">
                                    <div class="job-no">
                                        {{ $cust['customer_code'] }}
                                        @if (!empty($cust['customer_name'])) - {{ $cust['customer_name'] }} @endif
                                    </div>
                                    @foreach ($cust['items'] as $item)
                                        <div class="job-meta">
                                            งานที่ {{ $item['seq'] }} · บิล {{ $item['bill_no'] }}
                                            <a href="{{ route('print.notes', $item['id']) }}" target="_blank" class="ms-1">ปริ้นบิล</a>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach

                            <div class="text-end mt-2">
                                <a class="btn btn-sm btn-success" target="_blank" href="{{ $printUrl }}">
                                    🖨️ ปริ้นใบงาน (A4)
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">ยังไม่มีงานที่จัดส่งในวันที่เลือก</div>
        @endif
    @endif
</div>

@if ($hasDate)
    {{-- ===== ปุ่มบันทึก ลอยติดมุมขวาล่างของจอเสมอ ===== --}}
    <div class="save-floatbar">
        <span class="floatbar-count">เลือกไว้ <strong id="selectedCount">0</strong> รายการ</span>
        <button type="button" id="openModalBtn" class="btn btn-success" disabled>
            บันทึกข้อมูลขนส่ง
        </button>
    </div>

    {{-- Modal เลือกวิธีการจัดส่ง + ผู้รับผิดชอบ --}}
    <div class="modal fade" id="driverModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ระบุวิธีการจัดส่งและผู้รับผิดชอบ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 position-relative">
                        <label class="form-label">วิธีการจัดส่ง <span class="required-mark">*</span></label>
                        <input type="text" id="vehicleSelect" class="form-control"
                               placeholder="พิมพ์เพื่อค้นหา หรือเลือกจากรายการ" autocomplete="off">
                        <div id="vehicleSuggest" class="autocomplete-list"></div>
                    </div>
                    <div class="mb-3 position-relative">
                        <label class="form-label" id="driverLabel">
                            ชื่อผู้รับผิดชอบ <span class="optional-hint" id="driverOptionalHint">(ไม่บังคับ)</span>
                        </label>
                        <input type="text" id="driverSelect" class="form-control"
                               placeholder="พิมพ์เพื่อค้นหา หรือเลือกจากรายการ" autocomplete="off">
                        <div id="driverSuggest" class="autocomplete-list"></div>
                        <div id="salesHint" class="text-danger small mt-1" style="display:none;">
                            เลือก "เซลล์ไปส่งเอง" — กรุณาพิมพ์ชื่อเซลล์ที่ไปส่งเองในช่องด้านบน
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" id="confirmSaveBtn" class="btn btn-primary">ยืนยันบันทึก</button>
                </div>
            </div>
        </div>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleCustCard(id) {
    const card = document.getElementById(id);
    if (card) card.classList.toggle('collapsed');
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.job-checkbox:checked').length;
    const countEl = document.getElementById('selectedCount');
    const btn = document.getElementById('openModalBtn');
    if (countEl) countEl.textContent = checked;
    if (btn) btn.disabled = checked === 0;
}

document.addEventListener('DOMContentLoaded', function () {
    const checkAllBills = document.getElementById('checkAllBills');
    const checkAllDocs = document.getElementById('checkAllDocs');
    const openModalBtn = document.getElementById('openModalBtn');

    checkAllBills?.addEventListener('change', function () {
        document.querySelectorAll('.bill-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });

    checkAllDocs?.addEventListener('change', function () {
        document.querySelectorAll('.doc-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });

    openModalBtn?.addEventListener('click', function () {
        const modal = new bootstrap.Modal(document.getElementById('driverModal'));
        modal.show();
    });

    const vehicleInput = document.getElementById('vehicleSelect');
    const driverInput = document.getElementById('driverSelect');
    const salesHint = document.getElementById('salesHint');
    const driverOptionalHint = document.getElementById('driverOptionalHint');

    // ===== custom dropdown ค้นหาได้ แทน native <datalist> (ที่หลุดตำแหน่งในตัว modal) =====
    const deliveryMethodsData = @json($deliveryMethods);
    const responsiblePersonsData = @json($responsiblePersons);

    function setupAutocomplete(input, listEl, data) {
        function render() {
            const q = input.value.trim().toLowerCase();
            const matches = q === '' ? data : data.filter(item => item.toLowerCase().includes(q));
            listEl.innerHTML = '';

            if (matches.length === 0) {
                listEl.innerHTML = '<div class="autocomplete-empty">ไม่พบรายการ</div>';
            } else {
                matches.slice(0, 50).forEach(item => {
                    const row = document.createElement('div');
                    row.className = 'autocomplete-item';
                    row.textContent = item;
                    // ใช้ mousedown แทน click เพื่อให้ทำงานก่อน input เสีย focus (blur)
                    row.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        input.value = item;
                        listEl.style.display = 'none';
                        input.dispatchEvent(new Event('input'));
                    });
                    listEl.appendChild(row);
                });
            }
            listEl.style.display = 'block';
        }

        input.addEventListener('focus', render);
        input.addEventListener('input', render);
        input.addEventListener('blur', function () {
            setTimeout(() => { listEl.style.display = 'none'; }, 150);
        });
    }

    setupAutocomplete(vehicleInput, document.getElementById('vehicleSuggest'), deliveryMethodsData);
    setupAutocomplete(driverInput, document.getElementById('driverSuggest'), responsiblePersonsData);

    function isSelfDeliverySales() {
        return vehicleInput.value.trim() === 'เซลล์ไปส่งเอง';
    }

    vehicleInput?.addEventListener('input', function () {
        const isSales = isSelfDeliverySales();
        salesHint.style.display = isSales ? 'block' : 'none';
        driverOptionalHint.style.display = isSales ? 'none' : 'inline';
        driverInput.placeholder = isSales
            ? 'พิมพ์ชื่อเซลล์ที่ไปส่งเอง (บังคับ)'
            : 'พิมพ์เพื่อค้นหา หรือเลือกจากรายการ';
    });

    document.getElementById('confirmSaveBtn')?.addEventListener('click', function () {
        const driver = driverInput.value.trim();
        const vehicle = vehicleInput.value.trim();

        if (!vehicle) {
            alert('กรุณาเลือกวิธีการจัดส่ง');
            return;
        }

        // ถ้าเลือก "เซลล์ไปส่งเอง" บังคับต้องพิมพ์ชื่อเซลล์ลงในช่องผู้รับผิดชอบ
        if (isSelfDeliverySales() && !driver) {
            alert('เลือก "เซลล์ไปส่งเอง" กรุณาพิมพ์ชื่อเซลล์ที่ไปส่งเองในช่องผู้รับผิดชอบด้วย');
            return;
        }

        // ถ้าไม่ได้เลือก "เซลล์ไปส่งเอง" ชื่อผู้รับผิดชอบต้องเลือกจากรายการที่มีให้เท่านั้น พิมพ์ชื่ออิสระไม่ได้
        if (!isSelfDeliverySales() && driver && !responsiblePersonsData.includes(driver)) {
            alert('กรุณาเลือกชื่อผู้รับผิดชอบจากรายการที่มีให้เท่านั้น (พิมพ์ชื่ออิสระไม่ได้ ยกเว้นเลือกวิธีจัดส่งเป็น "เซลล์ไปส่งเอง")');
            return;
        }

        const form = document.getElementById('dispatchForm');
        const jobInputsDiv = document.getElementById('jobInputs');
        jobInputsDiv.innerHTML = '';

        document.querySelectorAll('.job-checkbox:checked').forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'jobs[]';
            input.value = cb.value;
            jobInputsDiv.appendChild(input);
        });

        function addHidden(name, value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            jobInputsDiv.appendChild(input);
        }

        addHidden('driver_name', driver);
        addHidden('transport_name', vehicle);

        form.submit();
    });
});
</script>
</body>
</html>
@endif