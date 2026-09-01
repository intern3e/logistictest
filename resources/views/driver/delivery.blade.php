@if (!empty($printMode))
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<style>
    @page { size: A4; margin: 8mm; }
    body { font-family: 'Sarabun', 'Segoe UI', Tahoma, Arial, sans-serif; font-size: 20px; color: #1e293b; margin: 0; padding: 0; width: 100%; }

.print-header-wrap { border-bottom: 2px solid #1e293b; padding-bottom: 6px; margin-bottom: 8px; }
.print-header-table td { border: none; padding: 0; vertical-align: top; }
.print-header-table h1 { font-size: 20px; margin: 0; }
.print-header-table .sub { font-size: 20px; color: #4b5563; margin-top: 2px; }
.print-header-table .meta-cell { text-align: right; font-size: 20px; color: #4b5563; white-space: nowrap; }

.cust-block { margin-bottom: 6px; border: 1px solid #dcdcdc; page-break-inside: avoid; width: 100%; }
.cust-block-header { background: #f3f4f6; padding: 5px 10px; border-bottom: 1px solid #dcdcdc; }
.cust-header-table { width: 100%; border-collapse: collapse; }
.cust-header-table td { border: none; padding: 0; vertical-align: middle; }
.stop-no-cell { width: 44px; font-size: 24px; font-weight: 800; color: #1e293b; text-align: center; }
.cust-info-cell { vertical-align: top; }
.cust-info-cell .cust-title { font-weight: 700; font-size: 20px; }
.cust-info-cell .cust-address { display: block; margin-top: 2px; font-weight: 400; color: #374151; font-size: 20px; }
.cust-qr-cell { width: 60px; text-align: center; vertical-align: top; }
.cust-qr-cell img { display: block; }
.cust-qr-cell .qr-label { font-size: 20px; color: #6b7280; margin-top: 1px; }

table.job-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
th, td { border-bottom: 1px solid #eeeeea; padding: 3px 10px; text-align: left; font-size: 20px; word-wrap: break-word; page-break-inside: avoid; }
th { background: #fafbfd; font-weight: 700; }
tr { page-break-inside: avoid; }
</style>
</head>
<body>
    <div class="print-header-wrap">
        <table class="print-header-table">
            <tr>
                <td><h1>ใบงานขนส่ง</h1></td>
                <td class="meta-cell">วันที่จัดส่ง: {{ $date ?: 'ไม่ระบุ' }}</td>
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
     โหมดหน้าเว็บปกติ — จ่ายงานขนส่ง (แสดงเฉพาะงานที่ยังไม่ได้จ่ายให้คนขับ)
     แบ่งเป็น 2 แท็บ: รับของเอง (เขียว) / ส่งของ (น้ำเงิน)
     รหัส/ชื่อ/ที่อยู่ vendor รวมอยู่บรรทัดเดียวกันใน group header
     สินค้า/จำนวน ย้ายไปโชว์ใน popup (ตาราง) แทนการแสดงในแถวตาราง
     ตารางทั้งหมด fix ความกว้าง ข้อความยาวจะขึ้นบรรทัดใหม่ ไม่ไหลแนวนอน
     ============================================================ --}}
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จ่ายงานขนส่งสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    :root{
        --ink:#1e293b; --canvas:#ffffff; --muted:#6b7280; --faint:#9ca3af; --border:#dcdcdc;
        --primary:#2563eb; --primary-dark:#1d4ed8; --primary-light:#eff6ff;
        --on-primary:#ffffff; --success:#16a34a; --success-dark:#15803d; --success-light:#dcfce7;
        --danger:#dc2626; --danger-dark:#b91c1c; --danger-light:#fee2e2;

        /* งานรับของเอง (PO) = เขียว ตามที่กำหนด */
        --pickup:#2b8a3e; --pickup-dark:#1f6e30; --pickup-light:#d3f9d8;
    }
    body { background:#f6f7f9; padding-bottom:96px; }

    /* ---- แท็บสลับ รับของเอง / ส่งของ ---- */
    .tab-switch { display:flex; gap:10px; margin-bottom:22px; }
    .tab-btn {
        flex:1; display:flex; align-items:center; justify-content:center; gap:8px;
        padding:14px 16px; border:2px solid var(--border); background:#fff;
        font-size:16px; font-weight:800; color:var(--muted); cursor:pointer;
        border-radius:8px; transition:.12s ease;
    }
    .tab-btn .tab-count {
        font-size:12.5px; font-weight:700; padding:2px 9px; border-radius:20px; background:#eee; color:#555;
    }
    .tab-btn.pickup.active { border-color:var(--pickup); background:var(--pickup-light); color:var(--pickup-dark); }
    .tab-btn.pickup.active .tab-count { background:var(--pickup); color:#fff; }
    .tab-btn.delivery.active { border-color:var(--primary); background:var(--primary-light); color:var(--primary-dark); }
    .tab-btn.delivery.active .tab-count { background:var(--primary); color:#fff; }

    .tab-panel { display:none; }
    .tab-panel.active { display:block; }

    .section-heading { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; margin-top:20px; }
    .section-heading:first-child { margin-top:0; }
    .section-heading h5 { margin:0; }
    .section-heading .section-count { font-size:13px; color:var(--muted); margin-left:6px; }

    /* ---- ตารางรายการงาน — fix ความกว้าง ไม่ไหลแนวนอน ---- */
    .job-list-table-wrap { background:#fff; border:1px solid var(--border); margin-bottom:28px; }
    table.job-list-table { width:100%; border-collapse:collapse; table-layout:fixed; }
    table.job-list-table th, table.job-list-table td {
        border-bottom:1px solid #eeeeea; padding:8px 12px; font-size:14px; text-align:left;
        vertical-align:top; word-wrap:break-word; overflow-wrap:break-word; white-space:normal;
    }
    table.job-list-table thead th {
        background:#fafbfd; font-weight:700; color:var(--ink); border-bottom:2px solid var(--border);
    }
    table.job-list-table tbody tr:hover { background:#f8f9fb; }
    table.job-list-table tr.group-row td {
        background:#f3f4f6; font-weight:800; font-size:14.5px; color:var(--ink);
        border-top:1px solid var(--border); border-bottom:1px solid var(--border); padding:8px 12px;
    }
    table.job-list-table tr.group-row.pickup-row td { background:var(--pickup-light); color:var(--pickup-dark); }
    table.job-list-table tr.group-row.delivery-row td { background:var(--primary-light); color:var(--primary-dark); }
    table.job-list-table .col-check { width:36px; text-align:center; }
    table.job-list-table .col-check input[type="checkbox"] { width:18px; height:18px; accent-color:var(--primary); }
    table.job-list-table .col-check input.pickup-checkbox { accent-color:var(--pickup); }
    table.job-list-table .job-no { font-weight:700; color:var(--ink); }
    table.job-list-table .job-address { font-size:13px; color:var(--ink); }
    table.job-list-table .job-address a { display:inline-block; margin-top:4px; }
    table.job-list-table .job-meta { font-size:12.5px; color:var(--muted); }
    table.job-list-table .job-notes { font-size:12.5px; color:var(--faint); }
    table.job-list-table .group-vendor-address { font-weight:400; font-size:13px; color:var(--muted); display:block; margin-top:2px; }

    .status-pill {
        display:inline-block; font-size:11.5px; font-weight:700; padding:2px 9px;
        border-radius:20px; margin-left:4px; white-space:nowrap;
    }
    .status-pill.status-green  { background:#d3f9d8; color:#2b8a3e; }
    .status-pill.status-yellow { background:#fff3bf; color:#e67700; }
    .status-pill.status-orange { background:#fff3e0; color:#e8590c; }
    .status-pill.status-inherit,
    .status-pill.status-red    { background:#eee; color:#555; }

    .empty-note { font-size:14px; color:var(--muted); padding:14px; border:1px dashed var(--border); background:#fff; margin-bottom:28px; }

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

    .autocomplete-list {
        position:absolute; top:100%; left:0; right:0; z-index:3000;
        max-height:220px; overflow-y:auto; background:var(--canvas);
        border:1px solid var(--border); border-top:none;
        box-shadow:0 6px 14px rgba(0,0,0,.1); display:none;
    }
    .autocomplete-item { padding:8px 12px; font-size:14px; cursor:pointer; }
    .autocomplete-item:hover { background:var(--primary-light); }
    .autocomplete-empty { padding:8px 12px; font-size:13px; color:var(--faint); }

    /* ---- ตาราง items ใน popup — fix ความกว้าง ไม่ไหลแนวนอน ---- */
    #itemsModalBody { word-wrap:break-word; overflow-wrap:break-word; }
    #itemsModal table.table { table-layout:fixed; width:100%; }
    #itemsModal table.table td, #itemsModal table.table th { word-wrap:break-word; overflow-wrap:break-word; white-space:normal; }
</style>
</head>
<body class="bg-light">
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <h3 class="mb-0">จ่ายงานขนส่งสินค้า</h3>
        <div class="text-end">
            <div class="text-muted mb-1">ผู้ใช้งาน: {{ $loggedInName }}</div>
            <a href="{{ route('deliverytrack.summary') }}" class="btn btn-sm btn-outline-secondary">
                📋 สรุปงานคนขับ / งานที่จัดส่งแล้ว
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @php
        $poCount   = collect($poGroups)->sum(fn($g) => count($g['rows']));
        $billCount = collect($billGroups)->sum(fn($g) => count($g['rows']));
        $docCount  = collect($docGroups)->sum(fn($g) => count($g['rows']));
    @endphp

    {{-- ================= แท็บสลับ ================= --}}
    <div class="tab-switch">
        <button type="button" class="tab-btn pickup active" id="tabBtnPickup" onclick="switchTab('pickup')">
            🛵 รับของเอง <span class="tab-count">{{ $poCount }}</span>
        </button>
        <button type="button" class="tab-btn delivery" id="tabBtnDelivery" onclick="switchTab('delivery')">
            🚚 ส่งของ <span class="tab-count">{{ $billCount + $docCount }}</span>
        </button>
    </div>

    <form id="dispatchForm" method="POST" action="{{ route('deliverytrack.store') }}">
        @csrf
        <div id="jobInputs"></div>

        {{-- ================= แท็บ 1: รับของเอง (PO) ================= --}}
        <div class="tab-panel active" id="panelPickup">
            <div class="section-heading">
                <h5>งาน PO รับเอง <span class="section-count">— {{ $poCount }} รายการ</span></h5>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="checkAllPo">
                    <label class="form-check-label" for="checkAllPo">เลือกทั้งหมด (รับของเอง)</label>
                </div>
            </div>

            @if (count($poGroups) > 0)
                <div class="job-list-table-wrap">
                    <table class="job-list-table">
                        <colgroup>
                            <col style="width:5%">
                            <col style="width:20%">
                            <col style="width:14%">
                            <col style="width:16%">
                            <col style="width:12%">
                            <col style="width:33%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="col-check"></th>
                                <th>PO / SO</th>
                                <th>สถานะ</th>
                                <th>วิธีรับของ</th>
                                <th>วันที่นัด</th>
                                <th>สินค้า</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($poGroups as $group)
                                <tr class="group-row pickup-row">
                                    <td colspan="6">
                                        {{ $group['customer_id'] }}
                                        @if (!empty($group['customer_name']))
                                            - {{ $group['customer_name'] }}
                                        @endif
                                        <span class="text-muted fw-normal">({{ count($group['rows']) }} PO)</span>
                                        @if (!empty($group['vendor_address']))
                                            <span class="group-vendor-address">{{ $group['vendor_address'] }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @foreach ($group['rows'] as $row)
                                    @php $po = $row['po']; @endphp
                                    <tr>
                                        <td class="col-check">
                                            <input type="checkbox" class="job-checkbox po-checkbox pickup-checkbox"
                                                   value="po:{{ $po->PONum }}"
                                                   onchange="updateSelectedCount()">
                                        </td>
                                        <td class="job-no">PO {{ $po->PONum }} · SO {{ $po->SONum }}</td>
                                        <td><span class="status-pill status-{{ $po->status_color }}">{{ $po->status_label }}</span></td>
                                        <td class="job-meta">{{ $po->DeliveryMethod }}</td>
                                        <td class="job-meta">{{ $po->DeliveryDate ?: '-' }}</td>
                                        <td class="job-notes">
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    onclick='showItemsModal("PO {{ $po->PONum }}", @json($po->items))'>
                                                🔎 ดูรายการ ({{ count($po->items) }})
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-note">ไม่มีงาน PO รับเองค้างจ่าย</div>
            @endif
        </div>

        {{-- ================= แท็บ 2: ส่งของ (บิลขาย + บิลชั่วคราว) ================= --}}
        <div class="tab-panel" id="panelDelivery">
            <div class="section-heading">
                <h5>งานบิลขาย <span class="section-count">— {{ $billCount }} รายการ</span></h5>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="checkAllBills">
                    <label class="form-check-label" for="checkAllBills">เลือกทั้งหมด (บิลขาย)</label>
                </div>
            </div>

            @if (count($billGroups) > 0)
                <div class="job-list-table-wrap">
                    <table class="job-list-table">
                        <colgroup>
                            <col style="width:5%">
                            <col style="width:20%">
                            <col style="width:35%">
                            <col style="width:18%">
                            <col style="width:22%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="col-check"></th>
                                <th>SO / บิล</th>
                                <th>ที่อยู่จัดส่ง</th>
                                <th>ผู้หยิบสินค้า</th>
                                <th>หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($billGroups as $group)
                                <tr class="group-row delivery-row">
                                    <td colspan="5">
                                        {{ $group['customer_id'] }}
                                        @if (!empty($group['customer_name']))
                                            - {{ $group['customer_name'] }}
                                        @endif
                                        <span class="text-muted fw-normal">({{ count($group['rows']) }} บิล)</span>
                                    </td>
                                </tr>
                                @foreach ($group['rows'] as $row)
                                    @php $bill = $row['bill']; @endphp
                                    <tr>
                                        <td class="col-check">
                                            <input type="checkbox" class="job-checkbox bill-checkbox"
                                                   value="bill:{{ $bill->so_detail_id }}"
                                                   onchange="updateSelectedCount()">
                                        </td>
                                        <td class="job-no">SO {{ $bill->so_id }} · บิล {{ $bill->billid }}</td>
                                        <td class="job-address">
                                            {{ $bill->customer_address }}
                                            @if (!empty($bill->customer_la_long))
                                                <br>
                                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($bill->customer_la_long) }}"
                                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                                    เปิดแผนที่
                                                </a>
                                            @endif
                                        </td>
                                        <td class="job-meta">{{ $bill->emp_picker ?: '-' }} · {{ $bill->time }}</td>
                                        <td class="job-notes">{{ $bill->notes ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-note">ไม่มีงานบิลขายค้างจ่าย</div>
            @endif

            <div class="section-heading">
                <h5>งานบิลชั่วคราว <span class="section-count">— {{ $docCount }} รายการ</span></h5>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="checkAllDocs">
                    <label class="form-check-label" for="checkAllDocs">เลือกทั้งหมด (บิลชั่วคราว)</label>
                </div>
            </div>

            @if (count($docGroups) > 0)
                <div class="job-list-table-wrap">
                    <table class="job-list-table">
                        <colgroup>
                            <col style="width:5%">
                            <col style="width:20%">
                            <col style="width:35%">
                            <col style="width:18%">
                            <col style="width:22%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="col-check"></th>
                                <th>เอกสาร</th>
                                <th>ที่อยู่จัดส่ง</th>
                                <th>ผู้บันทึก</th>
                                <th>หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($docGroups as $group)
                                <tr class="group-row delivery-row">
                                    <td colspan="5">
                                        {{ $group['customer_id'] }}
                                        @if (!empty($group['customer_name']))
                                            - {{ $group['customer_name'] }}
                                        @endif
                                        <span class="text-muted fw-normal">({{ count($group['rows']) }} เอกสาร)</span>
                                    </td>
                                </tr>
                                @foreach ($group['rows'] as $row)
                                    @php $doc = $row['doc']; @endphp
                                    <tr>
                                        <td class="col-check">
                                            <input type="checkbox" class="job-checkbox doc-checkbox"
                                                   value="doc:{{ $doc->doc_id }}"
                                                   onchange="updateSelectedCount()">
                                        </td>
                                        <td class="job-no">เอกสาร {{ $doc->doc_id }} · {{ $doc->contact_name }}</td>
                                        <td class="job-address">
                                            {{ $doc->com_address }}
                                            @if (!empty($doc->com_la_long))
                                                <br>
                                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($doc->com_la_long) }}"
                                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                                    เปิดแผนที่
                                                </a>
                                            @endif
                                        </td>
                                        <td class="job-meta">{{ $doc->emp_name ?: '-' }} · {{ $doc->time }}</td>
                                        <td class="job-notes">{{ $doc->notes ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-note">ไม่มีงานบิลชั่วคราวค้างจ่าย</div>
            @endif
        </div>
    </form>
</div>

{{-- ===== ปุ่มบันทึก ลอยติดมุมขวาล่างของจอเสมอ ===== --}}
<div class="save-floatbar">
    <span class="floatbar-count">เลือกไว้ <strong id="selectedCount">0</strong> รายการ</span>
    <button type="button" id="openModalBtn" class="btn btn-success" disabled>
        บันทึกข้อมูลขนส่ง
    </button>
</div>

{{-- Modal เลือกวันที่จัดส่ง + วิธีการจัดส่ง + ผู้รับผิดชอบ --}}
<div class="modal fade" id="driverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ระบุวันที่จัดส่ง วิธีการจัดส่ง และผู้รับผิดชอบ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">วันที่จัดส่ง <span class="required-mark">*</span></label>
                    <input type="date" id="deliveryDateInput" class="form-control">
                </div>
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

{{-- Modal รายการสินค้า/จำนวน (PO) --}}
<div class="modal fade" id="itemsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemsModalTitle">รายการสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm mb-0">
                    <colgroup>
                        <col style="width:70%">
                        <col style="width:30%">
                    </colgroup>
                    <thead><tr><th>สินค้า</th><th class="text-end">จำนวน</th></tr></thead>
                    <tbody id="itemsModalBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function switchTab(tab) {
    const panelPickup = document.getElementById('panelPickup');
    const panelDelivery = document.getElementById('panelDelivery');
    const btnPickup = document.getElementById('tabBtnPickup');
    const btnDelivery = document.getElementById('tabBtnDelivery');

    const showPickup = tab === 'pickup';
    panelPickup.classList.toggle('active', showPickup);
    panelDelivery.classList.toggle('active', !showPickup);
    btnPickup.classList.toggle('active', showPickup);
    btnDelivery.classList.toggle('active', !showPickup);
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.job-checkbox:checked').length;
    const countEl = document.getElementById('selectedCount');
    const btn = document.getElementById('openModalBtn');
    if (countEl) countEl.textContent = checked;
    if (btn) btn.disabled = checked === 0;
}

let itemsModalInstance = null;

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function showItemsModal(title, items) {
    document.getElementById('itemsModalTitle').textContent = title;

    const tbody = document.getElementById('itemsModalBody');
    tbody.innerHTML = '';

    if (!items || items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="2" class="text-muted">ไม่พบรายการสินค้า</td></tr>';
    } else {
        items.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${escapeHtml(item.name ?? '-')}</td><td class="text-end">${escapeHtml(String(item.qty ?? '-'))}</td>`;
            tbody.appendChild(tr);
        });
    }

    if (!itemsModalInstance) {
        itemsModalInstance = new bootstrap.Modal(document.getElementById('itemsModal'));
    }
    itemsModalInstance.show();
}

document.addEventListener('DOMContentLoaded', function () {
    const checkAllBills = document.getElementById('checkAllBills');
    const checkAllDocs = document.getElementById('checkAllDocs');
    const checkAllPo = document.getElementById('checkAllPo');
    const openModalBtn = document.getElementById('openModalBtn');
    const deliveryDateInput = document.getElementById('deliveryDateInput');

    // ค่าเริ่มต้น = วันนี้ แก้ไขได้
    if (deliveryDateInput && !deliveryDateInput.value) {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        deliveryDateInput.value = `${yyyy}-${mm}-${dd}`;
    }

    checkAllBills?.addEventListener('change', function () {
        document.querySelectorAll('.bill-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });

    checkAllDocs?.addEventListener('change', function () {
        document.querySelectorAll('.doc-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });

    checkAllPo?.addEventListener('change', function () {
        document.querySelectorAll('.po-checkbox').forEach(cb => cb.checked = this.checked);
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
        const deliveryDate = deliveryDateInput.value.trim();
        const driver = driverInput.value.trim();
        const vehicle = vehicleInput.value.trim();

        if (!deliveryDate) {
            alert('กรุณาระบุวันที่จัดส่ง');
            return;
        }

        if (!vehicle) {
            alert('กรุณาเลือกวิธีการจัดส่ง');
            return;
        }

        if (isSelfDeliverySales() && !driver) {
            alert('เลือก "เซลล์ไปส่งเอง" กรุณาพิมพ์ชื่อเซลล์ที่ไปส่งเองในช่องผู้รับผิดชอบด้วย');
            return;
        }

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

        addHidden('delivery_date', deliveryDate);
        addHidden('driver_name', driver);
        addHidden('transport_name', vehicle);

        form.submit();
    });
});
</script>
</body>
</html>
@endif