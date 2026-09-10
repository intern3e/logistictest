@php
// ฟังก์ชันช่วยตัดบรรทัดที่อยู่ไม่ให้เกิน 200 ตัวอักษรต่อบรรทัด (รองรับภาษาไทยและ UTF-8)
$wrapAddress = function($text, $limit = 200) {
    if (empty($text)) return '';
    $text = (string)$text;

    $words = preg_split('/\s+/u', trim($text));
    $lines = [];
    $current = '';

    foreach ($words as $word) {
        $candidate = $current === '' ? $word : $current . ' ' . $word;

        if (mb_strlen($candidate, 'UTF-8') > $limit && $current !== '') {
            $lines[] = $current;
            $current = $word;
        } else {
            $current = $candidate;
        }

        while (mb_strlen($current, 'UTF-8') > $limit) {
            $lines[] = mb_substr($current, 0, $limit, 'UTF-8');
            $current = mb_substr($current, $limit, null, 'UTF-8');
        }
    }

    if ($current !== '') {
        $lines[] = $current;
    }

    return implode("<br>", $lines);
};
@endphp

@if (!empty($printMode))
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">

<style>
    /* ใช้ไฟล์ TTF ตรงๆ จาก GitHub แทน woff2 ของ Google Fonts เพราะ dompdf (php-font-lib)
       ไม่รองรับการถอดรหัส woff2 (บีบอัดด้วย Brotli) ทำให้ฟอนต์ไทยหายหรือเพี้ยน */
    @font-face {
        font-family: 'Sarabun';
        font-style: normal;
        font-weight: 400;
        src: url('https://raw.githubusercontent.com/google/fonts/main/ofl/sarabun/Sarabun-Regular.ttf') format('truetype');
    }
    @font-face {
        font-family: 'Sarabun';
        font-style: normal;
        font-weight: 700;
        src: url('https://raw.githubusercontent.com/google/fonts/main/ofl/sarabun/Sarabun-Bold.ttf') format('truetype');
    }

    @page {
        size: A4;
        margin: 8mm 3mm 3mm 3mm;
    }

    * { box-sizing: border-box; }

    body {
        font-family: 'Sarabun', 'THSarabunNew', 'Segoe UI', Tahoma, Arial, sans-serif;
        font-size: 11px;
        color: #111827;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    table.info-table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 4px;
    }

    table.info-table td {
        border: none;
        padding: 2px 6px;
        white-space: nowrap;
        vertical-align: middle;
    }

    table.bill-table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        table-layout: fixed;
    }

    table.bill-table col.col-customer { width: 10%; }
    table.bill-table col.col-company  { width: 46%; }
    table.bill-table col.col-billno   { width: 10%; }
    table.bill-table col.col-notes    { width: 34%; }

    table.bill-table td.col-customer { width: 10%; }
    table.bill-table td.col-company  { width: 46%; }
    table.bill-table td.col-billno   { width: 10%; }
    table.bill-table td.col-notes    { width: 34%; }

.field-label {
    display: inline-block;
    width: 70px;
    text-align: right;
    font-weight: 700;
    font-size: 14px;
    vertical-align: middle;
}

.field-colon {
    display: inline-block;
    width: 10px;
    text-align: center;
    font-weight: 700;
    font-size: 14px;
    vertical-align: middle;
}

.field-box {
    display: inline-block;
    border-radius: 2px;
    font-size: 14px;  
    padding: 0 4px;
    line-height: 1.2;
    background: #fff;
    vertical-align: middle;
    position: relative;
    top: 1px;
}

    table.bill-table thead tr.col-header-row th {
        background: #e9ebef;
        border: 1px solid #000;
        font-weight: 700;
        font-size: 11.5px;
        text-align: center;
        vertical-align: middle;
        padding: 4px 6px;
    }

    table.bill-table td {
        border: 1px solid #000;
        padding: 4px 6px;
        vertical-align: top;
        font-size: 10.5px;
        line-height: 1.35;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    table.bill-table tr { page-break-inside: avoid; }

    table.bill-table tr.group-header-row td {
        background: #f3f4f6;
        font-weight: 700;
        font-size: 10.5px;
        text-align: center;
        padding: 4px 6px;
        border: 1px solid #000;
    }

    .company-name {
        font-weight: 700;
        font-size: 11px;
        margin-bottom: 1px;
    }
    .company-address {
        font-weight: 400;
        font-size: 10px;
        color: #374151;
    }
</style>
</head>
<body>

    @php
        // รองรับทั้งกรณีปริ้นทีละกล่อง (ส่ง $box เดี่ยว) และปริ้นรวมทุกกล่อง (ส่ง $boxes เป็น array)
        // ให้ view นี้ใช้ร่วมกันได้ ไม่ต้องแยกไฟล์
        $printBoxes = isset($boxes) ? $boxes : [$box];
        $lastBoxIndex = array_key_last($printBoxes);
    @endphp

    @foreach ($printBoxes as $boxIndex => $printBox)
        @php
            $rows = [];
            foreach ($printBox['customers'] as $custIndex => $cust) {
                $rawAddress = $cust['address'] ?? '';
                $rawAddress = preg_replace('/[\r\n]+\s*(สถานที่ส่ง)/u', ' $1', $rawAddress);
                $rawAddress = preg_replace('/<br\s*\/?>\s*(สถานที่ส่ง)/iu', ' $1', $rawAddress);

                $addressHtml = $wrapAddress($rawAddress, 200);
                $addressHtml = preg_replace('/(<br\s*\/?>\s*)+(?=สถานที่ส่ง)/iu', ' ', $addressHtml);

                $lastItemIndex = array_key_last($cust['items']);

                foreach ($cust['items'] as $itemIndex => $item) {
                    $rows[] = [
                        'cust_index'    => $custIndex,
                        'is_last_item'  => $itemIndex === $lastItemIndex,
                        'customer_code' => $cust['customer_code'],
                        'customer_name' => $cust['customer_name'] ?? null,
                        'address_html'  => $addressHtml,
                        'bill_no'       => $item['bill_no'],
                        'notes'         => $item['notes'] ?? null,
                    ];
                }
            }

            $rowChunks = array_chunk($rows, 19);
            $lastChunkIndex = array_key_last($rowChunks);
        @endphp

        @foreach ($rowChunks as $chunkIndex => $chunkRows)
<table class="info-table" cellpadding="0" cellspacing="0" style="width:100%;">
    <tr>
        <td>
            <span class="field-label">วันที่เอกสาร</span><span class="field-colon">:</span>
            <span class="field-box">{{ $date ? \Carbon\Carbon::parse($date)->locale('th')->translatedFormat('j M Y') : 'ไม่ระบุ' }}</span>
        </td>
        <td></td>
    </tr>
    <tr>
        <td colspan="2">
            <span class="field-label">ผู้รับผิดชอบ</span><span class="field-colon">:</span>
            <span class="field-box">{{ $printBox['driver_name'] ?: '-' }}</span>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <span class="field-label">วิธีการจัดส่ง</span><span class="field-colon">:</span>
            <span class="field-box">{{ $printBox['transport_name'] }}</span>
        </td>
        <td style="text-align:right;">
            <span>พิมพ์โดย: {{ $printedBy }}</span>
            <span style="margin-left:12px;">เวลาพิมพ์: {{ $printedAt->format('d/m/Y H:i') }} น.</span>
        </td>
    </tr>
</table>

        <table class="bill-table">
            <colgroup>
                <col class="col-customer">
                <col class="col-company">
                <col class="col-billno">
                <col class="col-notes">
            </colgroup>
            <thead>
                <tr class="col-header-row">
                    <th>Customer</th>
                    <th>บริษัท</th>
                    <th>Bill No</th>
                    <th>หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chunkRows as $rowIndex => $row)
                    @php
                        $prevRow = $rowIndex > 0 ? $chunkRows[$rowIndex - 1] : null;
                        $nextRow = isset($chunkRows[$rowIndex + 1]) ? $chunkRows[$rowIndex + 1] : null;

                        $hasSameCustomerPrev = $prevRow && $prevRow['customer_code'] === $row['customer_code'];
                        $hasSameCustomerNext = $nextRow && $nextRow['customer_code'] === $row['customer_code'];

                        $borderTop = $hasSameCustomerPrev ? 'border-top: 0px solid transparent;' : '';
                        $borderBottom = $hasSameCustomerNext ? 'border-bottom: 0px solid transparent;' : '';
                        $borderStyle = $borderTop . ' ' . $borderBottom;

                        $isSelfPickupCustomer = str_starts_with((string) $row['customer_code'], 'VEN-11047');
                        $isFirstOfCustomerGroup = !$hasSameCustomerPrev;

                        // ตัดหมายเหตุให้เหลือไม่เกิน 130 ตัวอักษร
                        $noteText = $row['notes'] ?? '';
                        $displayNote = $noteText === '' 
                            ? '-' 
                            : (mb_strlen($noteText, 'UTF-8') > 100 ? mb_substr($noteText, 0, 100, 'UTF-8') . '...' : $noteText);

                        // ซ่อนชื่อและที่อยู่ถ้าไม่ใช่แถวแรกของกลุ่มลูกค้าเดียวกัน
                        $showCustomerInfo = $isFirstOfCustomerGroup;
                    @endphp

                    @if ($isSelfPickupCustomer && $isFirstOfCustomerGroup)
                        <tr class="group-header-row">
                            <td colspan="4">รับของเอง</td>
                        </tr>
                    @endif

                    <tr>
                        <td class="col-customer" style="{{ $borderStyle }}">
                            @if ($showCustomerInfo)
                                {{ $row['customer_code'] }}
                            @endif
                        </td>
                        <td class="col-company" style="{{ $borderStyle }}">
                            @if ($showCustomerInfo)
                                @if (!empty($row['customer_name']))
                                    <div class="company-name">{{ $row['customer_name'] }}</div>
                                @endif
                                <div class="company-address">
                                    {!! $row['address_html'] !!}
                                </div>
                            @endif
                        </td>
                        <td class="col-billno" style="{{ $borderStyle }}">
                            {{ $row['bill_no'] }}
                        </td>
                        <td class="col-notes" style="{{ $borderStyle }}">
                            {{ $displayNote }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if (!($boxIndex === $lastBoxIndex && $chunkIndex === $lastChunkIndex))
            <div style="page-break-after: always;"></div>
        @endif
        @endforeach
    @endforeach

</body>
</html>
@else
{{-- ============================================================
     โหมดหน้าเว็บปกติ — จ่ายงานขนส่งสินค้า (ปรับให้ดูง่ายขึ้น)
     ============================================================ --}}
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จ่ายงานขนส่งสินค้า</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{
            --ink:#1a2634; --ink-soft:#5c6b7a; --ink-faint:#8592a0;
            --paper:#f8f9fa; --surface:#ffffff; --line:#e9ecef; --line-strong:#dee2e6;
            --delivery:#002a67; --delivery-rgb:0,42,103;
            --pickup:#002a67; --pickup-rgb:0,42,103;
            --doc:#002a67; --doc-rgb:0,42,103;
            --success:#2e7d32; --danger:#c62828;
        }
        *{ box-sizing:border-box; }
        html, body{ background:#f0f2f5; }
        body{ font-family:'Sarabun',sans-serif; color:var(--ink); font-size:15px; line-height:1.6; }
        .container-fluid{ width:100%; max-width:1800px; margin:0 auto; padding:20px; }
        
        .page-header{ 
            padding:20px 0; 
            margin-bottom:25px; 
            display:flex; 
            flex-wrap:wrap;
            gap:16px;
            justify-content:space-between; 
            align-items:center; 
        }
        .page-header-left{
            display:flex;
            align-items:center;
            gap:28px;
            flex-wrap:wrap;
        }
        .page-title{ margin:0; font-size:1.5rem; font-weight:700; color:var(--ink); }
        .page-subtitle{ font-size:0.9rem; color:var(--ink-soft); margin-top:4px; }
        
        .btn-manifest{ 
            padding:8px 16px; 
            border-radius:8px; 
            border:1px solid var(--line-strong); 
            font-size:0.9rem; 
            font-weight:600; 
            cursor:pointer; 
            background:#fff;
            transition:all 0.2s;
        }
        .btn-manifest:hover{ background:var(--paper); }
        .btn-manifest-cta{ 
            background:var(--delivery); 
            color:#fff; 
            border:none; 
            padding:10px 24px;
            font-size:0.95rem;
        }
        .btn-manifest-cta:hover{ background:#001a40; }
        .btn-manifest-cta:disabled{ opacity:0.4; cursor:not-allowed; }

        .alert{ border-radius:8px; padding:12px 18px; font-size:0.95rem; margin-bottom:20px; border:none; }
        .alert-success{ background:#e8f5e9; color:#1b5e20; }
        .alert-danger{ background:#ffebee; color:#c62828; }

        .user-badge{
            display:flex;
            align-items:center;
            gap:10px;
            padding:6px 16px 6px 6px;
            background:#fff;
            border-radius:50px;
            box-shadow:0 2px 8px rgba(0,0,0,0.08);
            border:1px solid var(--line);
        }
        .user-badge-avatar{
            width:32px;
            height:32px;
            border-radius:50%;
            background:var(--delivery);
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:700;
            font-size:0.9rem;
            flex-shrink:0;
        }
        .user-badge-name{
            font-weight:600;
            color:var(--ink);
            font-size:0.95rem;
        }

        .view-controls{ 
            display:flex; 
            gap:10px; 
            flex-wrap:wrap;
        }
        .view-btn{ 
            padding:10px 20px; 
            border:2px solid var(--line-strong); 
            background:#fff; 
            border-radius:10px; 
            font-weight:600; 
            cursor:pointer;
            transition:all 0.2s;
            font-size:0.95rem;
        }
        .view-btn:hover{ border-color:var(--ink-soft); transform:translateY(-1px); }
        .view-btn.active{ color:#fff; border-color:transparent; }
        .view-btn.active.delivery{ background:var(--delivery); }
        .view-btn.active.doc{ background:var(--doc); }
        .view-btn.active.pickup{ background:var(--pickup); }
        .view-btn.active:not(.delivery):not(.doc):not(.pickup){ background:#2853d5; }
        
        .view-count{ 
            display:inline-block; 
            padding:2px 10px; 
            border-radius:20px; 
            font-size:0.85rem; 
            font-weight:700; 
            margin-left:6px;
            background:rgba(0,0,0,0.1);
        }
        .view-btn.active .view-count{ background:rgba(255,255,255,0.25); color:#fff; }

        .dashboard-grid{ 
            display:grid; 
            grid-template-columns:repeat(3, 1fr); 
            gap:20px;
        }
        @media (max-width:1200px){ 
            .dashboard-grid{ grid-template-columns:1fr; } 
        }

        .grid-panel{ 
            background:#fff; 
            border-radius:0; 
            overflow:hidden;
            box-shadow:0 2px 12px rgba(0,0,0,0.08);
            display:flex;
            flex-direction:column;
            min-height:650px;
        }

        .dashboard-grid.is-filtered .grid-panel{ display:none; }
        .dashboard-grid.is-filtered .grid-panel.is-expanded{ 
            display:flex; 
            grid-column:1/-1; 
            animation:fadeIn 0.3s;
        }
        @keyframes fadeIn{ from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

        .section-heading{ 
            padding:16px 20px; 
            display:flex; 
            justify-content:space-between; 
            align-items:center;
            cursor:pointer;
            border-bottom:2px solid var(--line);
            border-radius: 0;
            transition: box-shadow 0.2s ease, transform 0.15s ease;
            position: relative;
            overflow: hidden;
        }
        .section-heading:hover{ 
            box-shadow: inset 0 0 0 2px rgba(255,255,255,0.3);
            transform: translateY(-1px);
        }
        .section-heading:active{
            transform: translateY(0);
            box-shadow: inset 0 0 0 2px rgba(255,255,255,0.1);
        }
        .section-heading.accent-delivery{ background:#2853d5; color:#fff; }
        .section-heading.accent-doc{ background:#2853d5; color:#fff; }
        .section-heading.accent-pickup{ background:#2853d5; color:#fff; }
        
        .section-heading h5{ 
            margin:0; 
            font-size:1.1rem; 
            font-weight:700;
            display:flex;
            align-items:center;
            gap:8px;
        }
        .section-count{ 
            background:rgba(255,255,255,0.2); 
            padding:2px 10px; 
            border-radius:20px; 
            font-size:0.85rem;
            font-weight:600;
        }
        .form-check-input{ width:18px; height:18px; cursor:pointer; }
        .form-check-label{ cursor:pointer; font-weight:500; }

        .expand-hint{ 
            font-size:0.8rem; 
            opacity:0.8;
            display:flex;
            align-items:center;
            gap:4px;
        }

        .job-list-table-wrap{ flex:1; overflow:auto; max-height:calc(100vh - 220px); }
        .job-list-table{ width:100%; border-collapse:collapse; font-size:0.8rem; table-layout:fixed; }
        .job-list-table th:nth-child(1), .job-list-table td:nth-child(1){ width:8%; }
        .job-list-table th:nth-child(2), .job-list-table td:nth-child(2){ width:27%; }
        .job-list-table th:nth-child(3), .job-list-table td:nth-child(3){ width:27%; }
        .job-list-table th:nth-child(4), .job-list-table td:nth-child(4){ width:38%; }
        .job-list-table thead th{ 
            background:var(--paper); 
            padding:12px 15px; 
            font-weight:600; 
            text-align:left;
            border-bottom:2px solid var(--line-strong);
            white-space:nowrap;
        }
        .job-list-table tbody td{ 
            padding:14px 15px; 
            border-bottom:1px solid var(--line);
            vertical-align:top;
            word-wrap:break-word;
            overflow-wrap:break-word;
        }
        .job-list-table tbody tr.row-alt td{ background:#f8f9fa; }
        .col-check{ text-align:center; }

        tr.job-detail-row{ display:none; }
        tr.job-detail-row.is-visible{ display:table-row; }

        .group-row td{ 
            padding:14px 18px; 
            font-weight:600;
            cursor:pointer;
            background:#fff;
            color:var(--ink);
            border-bottom:2px solid var(--line-strong);
        }
        .group-row.delivery-row td{ background:#fff; color:var(--ink); }
        .group-row.doc-row td{ background:#fff; color:var(--ink); }
        .group-row.pickup-row td{ background:#fff; color:var(--ink); }

        .group-select-checkbox{ width:18px; height:18px; cursor:pointer; margin-top:2px; }
        .group-customer-info{ display:flex; flex-direction:column; line-height:1.3; min-width:0; }
        .group-customer-id{ font-family:'JetBrains Mono',monospace; font-weight:700; font-size:0.95rem; color:var(--ink); }
        .group-customer-name{ font-weight:700; font-size:0.7rem; color:var(--ink-soft); margin-top:2px; line-height:1.45; word-break:keep-all; overflow-wrap:normal; }
        .group-customer-address-row{ font-weight:400; font-size:0.7rem; color:var(--ink-faint); margin-top:6px; padding-left:28px; line-height:1.45; white-space:normal; word-break:keep-all; overflow-wrap:normal; }
        .group-count-chip{ background:#2853d5; color:#fff; padding:2px 10px; border-radius:15px; font-size:0.85rem; }
        .group-address{ display:block; margin-top:6px; font-size:0.85rem; opacity:0.9; font-weight:400; }

        .group-row-inner{ display:flex; align-items:flex-start; justify-content:space-between; gap:10px; }
        .group-row-left{ display:flex; align-items:flex-start; gap:10px; flex:1 1 auto; min-width:0; }
        .group-row-right{ display:flex; align-items:center; gap:10px; flex-shrink:0; white-space:nowrap; }

        .group-toggle-btn{
            display:flex;
            align-items:center;
            gap:4px;
            background:#fff;
            border:1px solid var(--line-strong);
            border-radius:8px;
            padding:4px 10px;
            font-size:0.8rem;
            font-weight:600;
            color:var(--ink);
            cursor:pointer;
            transition:all 0.2s;
        }
        .group-toggle-btn:hover{ background:var(--paper); border-color:var(--ink-soft); }
        .group-toggle-icon{ display:inline-block; transition:transform 0.2s; }
        .group-toggle-btn.is-open .group-toggle-icon{ transform:rotate(180deg); }

        .job-id-primary{ font-family:'JetBrains Mono',monospace; font-weight:700; font-size:0.8rem; color:var(--ink); }
        .job-id-secondary{ font-size:0.7rem; color:var(--ink-soft); margin-top:3px; }
        .job-meta{ font-size:0.75rem; }
        .meta-name-chip{ font-weight:600; margin-bottom:2px; }
        .meta-time{ font-size:0.7rem; color:var(--ink-soft); }
        .job-notes{
            font-size:0.7rem;
            color:var(--ink-soft);
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }
        
        .status-pill{ 
            display:inline-block; 
            padding:3px 10px; 
            border-radius:15px; 
            font-size:0.8rem; 
            font-weight:600;
            margin-left:6px;
        }
        .status-overdue{ background:#ffebee; color:#c62828; }
        .status-waiting{ background:#e8f5e9; color:#2e7d32; }
        .status-default{ background:#e3f2fd; color:#1565c0; }

        .job-items-cell{ 
            color:var(--pickup); 
            font-weight:600; 
            cursor:pointer; 
            text-align:center;
            padding:8px 12px;
            border-radius:6px;
            transition:background 0.2s;
        }
        .job-items-cell:hover{ background:#e8f5e9; }

        .empty-note{ 
            padding:40px 20px; 
            text-align:center; 
            color:var(--ink-soft); 
            background:var(--paper);
            margin:10px;
            border-radius:8px;
        }

        .save-floatbar{ 
            position:fixed; 
            right:30px; 
            bottom:30px; 
            background:var(--ink); 
            color:#fff; 
            padding:14px 20px; 
            border-radius:50px; 
            box-shadow:0 8px 24px rgba(0,0,0,0.2);
            display:flex;
            align-items:center;
            gap:15px;
            z-index:1000;
        }
        .floatbar-count{ font-size:0.95rem; }
        .floatbar-count strong{ font-size:1.3rem; font-weight:700; }

        .modal-content{ border:none; border-radius:16px; }
        .modal-header{ border-bottom:1px solid var(--line); padding:18px 24px; }
        .modal-body{ padding:24px; }
        .modal-footer{ border-top:1px solid var(--line); padding:16px 24px; }
        .form-label{ font-weight:600; margin-bottom:8px; }
        .form-control{ padding:10px 14px; border-radius:8px; border:1px solid var(--line-strong); }
        .form-control:focus{ border-color:var(--delivery); box-shadow:0 0 0 3px rgba(13,71,161,0.1); }

        .required-mark{ color:#c62828; }
        .optional-hint{ font-weight:400; font-size:0.85rem; color:var(--ink-faint); }
        .autocomplete-list{
            display:none;
            position:absolute;
            left:0;
            right:0;
            z-index:2000;
            margin-top:4px;
            max-height:240px;
            overflow-y:auto;
            background:#fff;
            border:1px solid var(--line-strong);
            border-radius:8px;
            box-shadow:0 14px 30px rgba(0,0,0,0.14);
        }
        .autocomplete-item{
            padding:10px 14px;
            font-size:0.95rem;
            color:var(--ink);
            cursor:pointer;
        }
        .autocomplete-item:hover{ background:var(--paper); }
        .autocomplete-empty{
            padding:10px 14px;
            font-size:0.9rem;
            color:var(--ink-faint);
        }

        /* Toast Notification Styles */
        .toast-container {
            pointer-events: none;
        }
        
        .toast-container > * {
            pointer-events: auto;
        }
        
        .custom-toast {
            min-width: 300px;
            max-width: 400px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            border: none;
            overflow: hidden;
            animation: slideInRight 0.3s ease-out;
            margin-bottom: 12px;
        }
        
        .custom-toast.toast-success {
            border-left: 4px solid #2e7d32;
        }
        
        .custom-toast.toast-error {
            border-left: 4px solid #c62828;
        }
        
        .custom-toast.toast-warning {
            border-left: 4px solid #ed6c02;
        }
        
        .custom-toast.toast-info {
            border-left: 4px solid #0288d1;
        }
        
        .toast-header {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .toast-success .toast-header {
            background: #e8f5e9;
            color: #1b5e20;
        }
        
        .toast-error .toast-header {
            background: #ffebee;
            color: #c62828;
        }
        
        .toast-warning .toast-header {
            background: #fff3e0;
            color: #e65100;
        }
        
        .toast-info .toast-header {
            background: #e3f2fd;
            color: #01579b;
        }
        
        .toast-body {
            padding: 12px 16px;
            font-size: 0.95rem;
            color: #1a2634;
        }
        
        .toast-icon {
            font-size: 1.2rem;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .toast.hiding {
            animation: slideOutRight 0.3s ease-out;
        }
        
        .toast-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.2s;
            padding: 0;
            line-height: 1;
            margin-left: auto;
        }
        
        .toast-close:hover {
            opacity: 1;
        }

        @media (max-width:768px){
            .view-controls{ flex-direction:column; }
            .view-btn{ width:100%; text-align:center; }
            .save-floatbar{ left:20px; right:20px; justify-content:space-between; }
        }
    </style>
</head>
<body>

<!-- Toast Notification Container -->
<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
</div>

<div class="container-fluid">
    @php
        $poCount = collect($poGroups)->sum(fn($g) => count($g['rows']));
        $billCount = collect($billGroups)->sum(fn($g) => count($g['rows']));
        $docCount = collect($docGroups)->sum(fn($g) => count($g['rows']));
    @endphp

    <div class="page-header">
        <div class="page-header-left">
            <div>
                <h1 class="page-title">จ่ายงานขนส่งสินค้า</h1>
                <div class="page-subtitle">ระบบจัดการงานขนส่งแบบเรียลไทม์</div>
            </div>

            <div class="view-controls">
                <button class="view-btn active" onclick="setView('all')">📊 แสดงทั้งหมด</button>
                <button class="view-btn delivery" onclick="setView('delivery')">🚚 ส่งของ <span class="view-count">{{ $billCount }}</span></button>
                <button class="view-btn doc" onclick="setView('doc')">📄 บิลชั่วคราว <span class="view-count">{{ $docCount }}</span></button>
                <button class="view-btn pickup" onclick="setView('pickup')">📦 รับของเอง <span class="view-count">{{ $poCount }}</span></button>
            </div>
        </div>

        <div style="display:flex; align-items:center; gap:14px;">
            <div class="user-badge">
                <div class="user-badge-avatar">{{ mb_strtoupper(mb_substr($loggedInName, 0, 1)) }}</div>
                <span class="user-badge-name">{{ $loggedInName }}</span>
            </div>
            <a href="{{ route('deliverytrack.summary') }}" class="btn btn-manifest">📋 สรุปงานคนขับ / งานที่จัดส่งแล้ว</a>
        </div>
    </div>

    @if (session('success')) <div class="alert alert-success">✅ {{ session('success') }}</div> @endif
    @if (session('error')) <div class="alert alert-danger">⚠️ {{ session('error') }}</div> @endif

    <form id="dispatchForm" method="POST" action="{{ route('deliverytrack.store') }}">
        @csrf
        <div id="jobInputs"></div>

        <div class="dashboard-grid" id="dashboardGrid">
            
            {{-- Panel 1: ส่งของ --}}
            <div class="grid-panel" id="panelDelivery" data-type="delivery">
                <div class="section-heading accent-delivery" onclick="setView('delivery')">
                    <h5>🚚 ส่งของ <span class="section-count">{{ $billCount }} รายการ</span></h5>
                    <div style="display:flex;align-items:center;gap:15px;">
                        <div class="form-check" onclick="event.stopPropagation()">
                            <input type="checkbox" class="form-check-input" id="checkAllBills">
                            <label class="form-check-label" for="checkAllBills">เลือกทั้งหมด</label>
                        </div>
                        <span class="expand-hint">⛶ ขยาย</span>
                    </div>
                </div>
                <div class="job-list-table-wrap">
                    @if(count($billGroups) > 0)
                    <table class="job-list-table">
                        <colgroup>
                            <col style="width:8%"><col style="width:27%"><col style="width:27%"><col style="width:38%">
                        </colgroup>
                        <tbody>
                            @foreach($billGroups as $group)
                                @php
                                    $groupKey = 'bill-'.$loop->index;
                                    $firstBillRow = $group['rows'][0]['bill'] ?? null;
                                    $groupCustomerName = $group['customer_name'] ?? null;
                                    $groupAddress = $firstBillRow->customer_address ?? null;
                                @endphp
                                <tr class="group-row delivery-row" data-group="{{ $groupKey }}">
                                    <td colspan="4" style="background:#fff;color:#1a2634;">
                                        <div class="group-row-inner">
                                            <div class="group-row-left">
                                                <input type="checkbox" class="group-select-checkbox" data-group="{{ $groupKey }}" onchange="onGroupCheckboxChange(this)">
                                                <div class="group-customer-info">
                                                    <span class="group-customer-id">{{ $group['customer_id'] ?: ($groupCustomerName ?: 'ไม่ระบุรหัส') }}</span>
                                                    @if(!empty($group['customer_id']) && !empty($groupCustomerName))
                                                        <span class="group-customer-name">{{ $groupCustomerName }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="group-row-right">
                                                <span class="group-count-chip">{{ count($group['rows']) }} บิล</span>
                                                <button type="button" class="group-toggle-btn" data-group="{{ $groupKey }}" onclick="event.stopPropagation(); toggleGroupDrawer(this.closest('tr'))">
                                                    <span class="group-toggle-icon">▾</span> รายละเอียด
                                                </button>
                                            </div>
                                        </div>
                                        @if(!empty($groupAddress))
                                            <div class="group-customer-address-row">{{ $groupAddress }}</div>
                                        @endif
                                    </td>
                                </tr>
                                @foreach($group['rows'] as $row)
                                    @php $bill = $row['bill']; @endphp
                                    <tr class="job-detail-row {{ $loop->iteration % 2 == 0 ? 'row-alt' : '' }}" data-group="{{ $groupKey }}">
                                        <td class="col-check">
                                            <input type="checkbox" class="job-checkbox bill-checkbox" value="bill:{{ $bill->so_detail_id }}" data-group="{{ $groupKey }}" onchange="updateSelectedCount()">
                                        </td>
                                        <td>
                                            <div class="job-id-primary">SO {{ $bill->so_id }}</div>
                                            <div class="job-id-secondary">บิล {{ $bill->billid }}</div>
                                        </td>
                                        <td class="job-meta">
                                            <div class="meta-name-chip">{{ $bill->emp_picker ?: '-' }}</div>
                                            <div class="meta-time">{{ $bill->time }}</div>
                                        </td>
                                        <td class="job-notes" title="{{ $bill->notes }}">{{ $bill->notes ? mb_strimwidth($bill->notes, 0, 25, '...') : '-' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="empty-note">ไม่มีงานค้างจ่าย</div>
                    @endif
                </div>
            </div>

            {{-- Panel 2: บิลชั่วคราว --}}
            <div class="grid-panel" id="panelDoc" data-type="doc">
                <div class="section-heading accent-doc" onclick="setView('doc')">
                    <h5>📄 บิลชั่วคราว <span class="section-count">{{ $docCount }} รายการ</span></h5>
                    <div style="display:flex;align-items:center;gap:15px;">
                        <div class="form-check" onclick="event.stopPropagation()">
                            <input type="checkbox" class="form-check-input" id="checkAllDocs">
                            <label class="form-check-label" for="checkAllDocs">เลือกทั้งหมด</label>
                        </div>
                        <span class="expand-hint">⛶ ขยาย</span>
                    </div>
                </div>
                <div class="job-list-table-wrap">
                    @if(count($docGroups) > 0)
                    <table class="job-list-table">
                        <colgroup>
                            <col style="width:8%"><col style="width:27%"><col style="width:27%"><col style="width:38%">
                        </colgroup>
                        <tbody>
                            @foreach($docGroups as $group)
                                @php
                                    $groupKey = 'doc-'.$loop->index;
                                    $firstDocRow = $group['rows'][0]['doc'] ?? null;
                                    $groupCustomerName = $group['customer_name'] ?? null;
                                    $groupAddress = $firstDocRow->com_address ?? null;
                                @endphp
                                <tr class="group-row doc-row" data-group="{{ $groupKey }}">
                                    <td colspan="4" style="background:#fff;color:#1a2634;">
                                        <div class="group-row-inner">
                                            <div class="group-row-left">
                                                <input type="checkbox" class="group-select-checkbox" data-group="{{ $groupKey }}" onchange="onGroupCheckboxChange(this)">
                                                <div class="group-customer-info">
                                                    <span class="group-customer-id">{{ $group['customer_id'] ?: ($groupCustomerName ?: 'ไม่ระบุรหัส') }}</span>
                                                    @if(!empty($group['customer_id']) && !empty($groupCustomerName))
                                                        <span class="group-customer-name">{{ $groupCustomerName }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="group-row-right">
                                                <span class="group-count-chip">{{ count($group['rows']) }} เอกสาร</span>
                                                <button type="button" class="group-toggle-btn" data-group="{{ $groupKey }}" onclick="event.stopPropagation(); toggleGroupDrawer(this.closest('tr'))">
                                                    <span class="group-toggle-icon"></span> รายละเอียด
                                                </button>
                                            </div>
                                        </div>
                                        @if(!empty($groupAddress))
                                            <div class="group-customer-address-row">{{ $groupAddress }}</div>
                                        @endif
                                    </td>
                                </tr>
                                @foreach($group['rows'] as $row)
                                    @php $doc = $row['doc']; @endphp
                                    <tr class="job-detail-row {{ $loop->iteration % 2 == 0 ? 'row-alt' : '' }}" data-group="{{ $groupKey }}">
                                        <td class="col-check">
                                            <input type="checkbox" class="job-checkbox doc-checkbox" value="doc:{{ $doc->doc_id }}" data-group="{{ $groupKey }}" onchange="updateSelectedCount()">
                                        </td>
                                        <td>
                                            <div class="job-id-primary">{{ $doc->doc_id }}</div>
                                            <div class="job-id-secondary">{{ $doc->contact_name }}</div>
                                        </td>
                                        <td class="job-meta">
                                            <div class="meta-name-chip">{{ $doc->emp_name ?: '-' }}</div>
                                            <div class="meta-time">{{ $doc->time }}</div>
                                        </td>
                                        <td class="job-notes" title="{{ $doc->notes }}">{{ $doc->notes ? mb_strimwidth($doc->notes, 0, 25, '...') : '-' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="empty-note">ไม่มีงานค้างจ่าย</div>
                    @endif
                </div>
            </div>

            {{-- Panel 3: รับของเอง --}}
            <div class="grid-panel" id="panelPickup" data-type="pickup">
                <div class="section-heading accent-pickup" onclick="setView('pickup')">
                    <h5>📦 รับของเอง <span class="section-count">{{ $poCount }} รายการ</span></h5>
                    <div style="display:flex;align-items:center;gap:15px;">
                        <div class="form-check" onclick="event.stopPropagation()">
                            <input type="checkbox" class="form-check-input" id="checkAllPo">
                            <label class="form-check-label" for="checkAllPo">เลือกทั้งหมด</label>
                        </div>
                        <span class="expand-hint">⛶ ขยาย</span>
                    </div>
                </div>
                <div class="job-list-table-wrap">
                    @if(count($poGroups) > 0)
                    <table class="job-list-table">
                        <colgroup>
                            <col style="width:8%"><col style="width:27%"><col style="width:27%"><col style="width:38%">
                        </colgroup>
                        <tbody>
                            @foreach($poGroups as $group)
                                @php
                                    $groupKey = 'po-'.$loop->index;
                                    $firstPoRow = $group['rows'][0]['po'] ?? null;
                                    $groupCustomerName = $group['customer_name'] ?? null;
                                    $groupAddress = $group['vendor_address'] ?? ($firstPoRow->vendor_address ?? null);
                                @endphp
                                <tr class="group-row pickup-row" data-group="{{ $groupKey }}">
                                    <td colspan="4" style="background:#fff;color:#1a2634;">
                                        <div class="group-row-inner">
                                            <div class="group-row-left">
                                                <input type="checkbox" class="group-select-checkbox" data-group="{{ $groupKey }}" onchange="onGroupCheckboxChange(this)">
                                                <div class="group-customer-info">
                                                    <span class="group-customer-id">{{ $group['customer_id'] ?: ($groupCustomerName ?: 'ไม่ระบุรหัส') }}</span>
                                                    @if(!empty($group['customer_id']) && !empty($groupCustomerName))
                                                        <span class="group-customer-name">{{ $groupCustomerName }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="group-row-right">
                                                <span class="group-count-chip">{{ count($group['rows']) }} PO</span>
                                                <button type="button" class="group-toggle-btn" data-group="{{ $groupKey }}" onclick="event.stopPropagation(); toggleGroupDrawer(this.closest('tr'))">
                                                    <span class="group-toggle-icon">▾</span> รายละเอียด
                                                </button>
                                            </div>
                                        </div>
                                        @if(!empty($groupAddress))
                                            <div class="group-customer-address-row">{{ $groupAddress }}</div>
                                        @endif
                                    </td>
                                </tr>
                                @foreach($group['rows'] as $row)
                                    @php $po = $row['po']; @endphp
                                    <tr class="job-detail-row {{ $loop->iteration % 2 == 0 ? 'row-alt' : '' }}" data-group="{{ $groupKey }}">
                                        <td class="col-check">
                                            <input type="checkbox" class="job-checkbox po-checkbox" value="po:{{ $po->PONum }}" data-po="{{ $po->PONum }}" data-group="{{ $groupKey }}" onchange="updateSelectedCount()">
                                        </td>
                                        <td>
                                            <div class="job-id-primary">{{ $po->PONum }}</div>
                                            <div class="job-id-secondary">SO {{ $po->SONum }}</div>
                                        </td>
                                        <td>{{ $po->DeliveryDate ?: '-' }}</td>
                                        <td class="job-items-cell" data-po-title="PO {{ $po->PONum }}" data-po-items="{{ json_encode($po->items) }}">
                                            {{ count($po->items) }} รายการ
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="empty-note">ไม่มีงานค้างจ่าย</div>
                    @endif
                </div>
            </div>

        </div>
    </form>
</div>

<div class="save-floatbar">
    <span class="floatbar-count">เลือก <strong id="selectedCount">0</strong> รายการ</span>
    <button id="openModalBtn" class="btn btn-manifest btn-manifest-cta" disabled> บันทึก</button>
</div>

<!-- Modals -->
<div class="modal fade" id="driverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
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
                        ผู้รับผิดชอบ <span class="optional-hint" id="driverOptionalHint">(ไม่บังคับ)</span>
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
                <button type="button" class="btn btn-manifest" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" id="confirmSaveBtn" class="btn btn-manifest btn-manifest-cta">บันทึก</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="itemsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemsModalTitle">รายการสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead><tr><th>สินค้า</th><th class="text-end">จำนวน</th></tr></thead>
                    <tbody id="itemsModalBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@php
    $transportOptions = $deliveryMethods ?? [];
    $driverOptions = $responsiblePersons ?? [];
@endphp
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toast Notification Functions
function showToast(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toastContainer');
    const toastId = 'toast-' + Date.now();
    
    const icons = {
        success: '✓',
        error: '✕',
        warning: '',
        info: ''
    };
    
    const titles = {
        success: 'สำเร็จ',
        error: 'เกิดข้อผิดพลาด',
        warning: 'คำเตือน',
        info: 'ข้อมูล'
    };
    
    const toastHTML = `
        <div id="${toastId}" class="toast custom-toast toast-${type}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <span class="toast-icon">${icons[type]}</span>
                <strong class="me-auto">${titles[type]}</strong>
                <button type="button" class="toast-close" onclick="hideToast('${toastId}')" aria-label="Close">&times;</button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    
    // Auto hide after duration
    if (duration > 0) {
        setTimeout(() => {
            hideToast(toastId);
        }, duration);
    }
    
    // Show toast using Bootstrap
    const bsToast = new bootstrap.Toast(toastElement, {
        delay: duration,
        autohide: true
    });
    bsToast.show();
}

function hideToast(toastId) {
    const toastElement = document.getElementById(toastId);
    if (toastElement) {
        toastElement.classList.add('hiding');
        setTimeout(() => {
            toastElement.remove();
        }, 300);
    }
}

function setView(mode) {
    const grid = document.getElementById('dashboardGrid');
    const panels = document.querySelectorAll('.grid-panel');
    const btns = document.querySelectorAll('.view-btn');
    
    btns.forEach(btn => btn.classList.remove('active'));

    if (mode === 'all') {
        grid.classList.remove('is-filtered');
        panels.forEach(p => p.classList.remove('is-expanded'));
        btns[0].classList.add('active');
    } else {
        grid.classList.add('is-filtered');
        panels.forEach(p => {
            p.classList.toggle('is-expanded', p.dataset.type === mode);
        });
        const activeBtn = document.querySelector(`.view-btn.${mode}`);
        if(activeBtn) activeBtn.classList.add('active');
    }
}

function toggleGroupSelection(row) {
    const groupId = row.dataset.group;
    const checkboxes = document.querySelectorAll(`.job-checkbox[data-group="${groupId}"]`);
    if (checkboxes.length === 0) return;
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    updateSelectedCount();
}

function toggleGroupDrawer(row) {
    const groupId = row.dataset.group;
    const isOpen = row.classList.toggle('is-open');
    document.querySelectorAll(`tr.job-detail-row[data-group="${groupId}"]`).forEach(r => {
        r.classList.toggle('is-visible', isOpen);
    });
    const toggleBtn = row.querySelector('.group-toggle-btn');
    if (toggleBtn) toggleBtn.classList.toggle('is-open', isOpen);
}

document.querySelectorAll('.job-list-table-wrap').forEach(wrap => {
    wrap.addEventListener('click', function (e) {
        const itemsCell = e.target.closest('.job-items-cell');
        if (itemsCell) {
            let items = [];
            try { items = JSON.parse(itemsCell.dataset.poItems || '[]'); } catch (err) { items = []; }
            showItemsModal(itemsCell.dataset.poTitle || '', items);
            return;
        }

        if (e.target.closest('.group-select-checkbox')) return;
        if (e.target.closest('.group-toggle-btn')) return;
        const row = e.target.closest('tr.group-row');
        if (row) toggleGroupSelection(row);
    });
});

function onGroupCheckboxChange(checkbox) {
    const groupId = checkbox.dataset.group;
    const checkboxes = document.querySelectorAll(`.job-checkbox[data-group="${groupId}"]`);
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateSelectedCount();
}

function syncGroupCheckboxes() {
    document.querySelectorAll('.group-select-checkbox').forEach(groupCb => {
        const groupId = groupCb.dataset.group;
        const children = document.querySelectorAll(`.job-checkbox[data-group="${groupId}"]`);
        if (children.length === 0) return;

        const checkedCount = Array.from(children).filter(cb => cb.checked).length;
        groupCb.checked = checkedCount === children.length;
        groupCb.indeterminate = checkedCount > 0 && checkedCount < children.length;
    });
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.job-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('openModalBtn').disabled = count === 0;
    syncGroupCheckboxes();
}

function showItemsModal(title, items) {
    document.getElementById('itemsModalTitle').textContent = title;
    const tbody = document.getElementById('itemsModalBody');
    tbody.innerHTML = items.map(item => 
        `<tr><td>${item.name || '-'}</td><td class="text-end">${item.qty || '-'}</td></tr>`
    ).join('');
    new bootstrap.Modal(document.getElementById('itemsModal')).show();
}

function setupAutocomplete(inputEl, listEl, options) {
    if (!inputEl || !listEl) return;

    function render() {
        const q = inputEl.value.trim().toLowerCase();
        const matches = q === '' ? options : options.filter(opt => opt.toLowerCase().includes(q));

        if (matches.length === 0) {
            listEl.innerHTML = '<div class="autocomplete-empty">ไม่พบรายการ</div>';
        } else {
            listEl.innerHTML = matches.slice(0, 50).map(opt => `<div class="autocomplete-item">${opt}</div>`).join('');
        }
        listEl.style.display = 'block';
    }

    inputEl.addEventListener('focus', render);
    inputEl.addEventListener('input', function () {
        render();
        inputEl.dispatchEvent(new Event('vehicleOrDriverInput'));
    });

    listEl.addEventListener('mousedown', function (e) {
        const item = e.target.closest('.autocomplete-item');
        if (!item) return;
        inputEl.value = item.textContent;
        listEl.style.display = 'none';
        inputEl.dispatchEvent(new Event('input'));
    });

    inputEl.addEventListener('blur', function () {
        setTimeout(() => { listEl.style.display = 'none'; }, 150);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('deliveryDateInput').value = new Date().toISOString().split('T')[0];
    
    document.getElementById('checkAllBills')?.addEventListener('change', function() {
        document.querySelectorAll('.bill-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });
    
    document.getElementById('checkAllDocs')?.addEventListener('change', function() {
        document.querySelectorAll('.doc-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });
    
    document.getElementById('checkAllPo')?.addEventListener('change', function() {
        document.querySelectorAll('.po-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });

    document.getElementById('openModalBtn').addEventListener('click', function() {
        new bootstrap.Modal(document.getElementById('driverModal')).show();
    });

    const vehicleInput = document.getElementById('vehicleSelect');
    const driverInput = document.getElementById('driverSelect');
    const salesHint = document.getElementById('salesHint');
    const driverOptionalHint = document.getElementById('driverOptionalHint');

    const deliveryMethodsData = @json($transportOptions);
    const responsiblePersonsData = @json($driverOptions);

    setupAutocomplete(vehicleInput, document.getElementById('vehicleSuggest'), deliveryMethodsData);
    setupAutocomplete(driverInput, document.getElementById('driverSuggest'), responsiblePersonsData);

    function isSelfDeliverySales() {
        return vehicleInput.value.trim() === 'เซลล์ไปส่งเอง';
    }

    vehicleInput?.addEventListener('vehicleOrDriverInput', function () {
        const isSales = isSelfDeliverySales();
        salesHint.style.display = isSales ? 'block' : 'none';
        driverOptionalHint.style.display = isSales ? 'none' : 'inline';
        driverInput.placeholder = isSales
            ? 'พิมพ์ชื่อเซลล์ที่ไปส่งเอง (บังคับ)'
            : 'พิมพ์เพื่อค้นหา หรือเลือกจากรายการ';
    });

    document.getElementById('confirmSaveBtn').addEventListener('click', function() {
        const date = document.getElementById('deliveryDateInput').value;
        const vehicle = vehicleInput.value.trim();
        const driver = driverInput.value.trim();

        if (!date) {
            showToast('กรุณาระบุวันที่จัดส่ง', 'error');
            return;
        }
        if (!vehicle) {
            showToast('กรุณาเลือกวิธีการจัดส่ง', 'error');
            return;
        }
        if (isSelfDeliverySales() && !driver) {
            showToast('เลือก "เซลล์ไปส่งเอง" กรุณาพิมพ์ชื่อเซลล์ที่ไปส่งเองในช่องผู้รับผิดชอบด้วย', 'warning');
            return;
        }
        if (!isSelfDeliverySales() && driver && !responsiblePersonsData.includes(driver)) {
            showToast('กรุณาเลือกชื่อผู้รับผิดชอบจากรายการที่มีให้เท่านั้น (พิมพ์ชื่ออิสระไม่ได้ ยกเว้นเลือกวิธีจัดส่งเป็น "เซลล์ไปส่งเอง")', 'error');
            return;
        }

        const form = document.getElementById('dispatchForm');
        const div = document.getElementById('jobInputs');
        div.innerHTML = '';
        
        document.querySelectorAll('.job-checkbox:checked').forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'jobs[]';
            input.value = cb.value;
            div.appendChild(input);
        });
        
        ['delivery_date','transport_name','driver_name'].forEach((name, i) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = [date, vehicle, driver][i];
            div.appendChild(input);
        });
        
        showToast('กำลังบันทึกข้อมูล...', 'info', 2000);
        form.submit();
    });
});
</script>
</body>
</html>
@endif