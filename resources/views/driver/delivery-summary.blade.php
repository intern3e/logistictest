<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สรุปงานคนขับ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    :root{
        --ink:#1e293b; --canvas:#ffffff; --muted:#6b7280; --faint:#9ca3af; --border:#dcdcdc;
        --primary:#2563eb; --primary-dark:#1d4ed8; --primary-light:#eff6ff;
        --success:#16a34a; --success-dark:#15803d;
    }
    body { background:#f6f7f9; }

    .date-group { margin-bottom:32px; }
    .date-group-title {
        font-size:16px; font-weight:800; color:var(--ink);
        padding-bottom:8px; border-bottom:2px solid var(--ink); margin-bottom:14px;
    }

    .cust-grid {
        display:grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap:14px;
        align-items:start;
    }
    @media (max-width:991px){
        .cust-grid { grid-template-columns:1fr; }
    }

    .cust-card {
        background:var(--canvas); border:1px solid var(--border); border-left:4px solid var(--success);
        overflow:hidden; min-width:0;
    }
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
    .cust-id-name { font-weight:800; font-size:16px; color:var(--ink); word-break:break-word; }
    .cust-id-name .cust-name-part { font-weight:700; color:var(--success-dark); }
    .cust-count-badge {
        flex-shrink:0; font-size:12.5px; font-weight:700; padding:4px 10px;
        background:var(--primary-light); color:var(--primary-dark); white-space:nowrap;
    }

    .cust-card-body { padding:6px 14px 12px; }
    .job-block { padding:8px 0; border-top:1px solid #eeeeea; }
    .job-block:first-child { border-top:none; }
    .job-no { font-weight:700; font-size:14px; color:var(--ink); }
    .job-meta { font-size:13px; color:var(--muted); margin-top:2px; }

    .empty-note { font-size:14px; color:var(--muted); padding:14px; border:1px dashed var(--border); background:#fff; }
</style>
</head>
<body class="bg-light">
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <h3 class="mb-0">สรุปงานคนขับ / งานที่จัดส่งแล้ว</h3>
        <div class="text-end">
            <div class="text-muted mb-1">ผู้ใช้งาน: {{ $loggedInName }}</div>
            <a href="{{ route('deliverytrack') }}" class="btn btn-sm btn-outline-secondary">
                ← กลับไปหน้าจ่ายงาน
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('deliverytrack.summary') }}" class="row g-2 mb-4 align-items-end">
        <div class="col-auto">
            <label class="form-label mb-1">กรองตามวันที่จัดส่ง</label>
            <input type="date" name="date" value="{{ $date }}" class="form-control">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">กรอง</button>
            @if ($date)
                <a href="{{ route('deliverytrack.summary') }}" class="btn btn-outline-secondary">ล้างตัวกรอง</a>
            @endif
        </div>
    </form>

    @forelse ($boxesByDate as $dateKey => $boxes)
        <div class="date-group">
            <div class="date-group-title">
                วันที่จัดส่ง: {{ $dateKey }}
                <span class="text-muted" style="font-weight:400; font-size:13px;">
                    ({{ collect($boxes)->sum('total_items') }} รายการ)
                </span>
            </div>

            @if (count($boxes) > 0)
                <div class="cust-grid">
                    @foreach ($boxes as $box)
                        @php
                            $boxKey = 'box_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $dateKey . '_' . $box['transport_name'] . '_' . ($box['driver_name'] ?? 'none'));
                            $printUrl = route('deliverytrack.printGroup', [
                                'date'      => $dateKey === 'ไม่ระบุวันที่' ? null : $dateKey,
                                'transport' => $box['transport_name'],
                                'driver'    => $box['driver_name'],
                            ]);
                        @endphp
                        <div class="cust-card collapsed" id="{{ $boxKey }}">
                            <div class="cust-card-header" onclick="toggleCustCard('{{ $boxKey }}')">
                                <span class="cust-toggle">▸</span>
                                <div class="cust-head-main">
                                    <div class="cust-id-name">
                                        {{ $box['transport_name'] }}
                                        @if (!empty($box['driver_name']))
                                            <span class="cust-name-part"> x {{ $box['driver_name'] }}</span>
                                        @else
                                            <span class="text-muted" style="font-weight:400;"> (ไม่ระบุผู้รับผิดชอบ)</span>
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
                <div class="empty-note">ไม่มีรายการ</div>
            @endif
        </div>
    @empty
        <div class="alert alert-info">ยังไม่มีงานที่จัดส่งแล้ว{{ $date ? ' ในวันที่เลือก' : '' }}</div>
    @endforelse
</div>

<script>
function toggleCustCard(id) {
    const card = document.getElementById(id);
    if (card) card.classList.toggle('collapsed');
}
</script>
</body>
</html>