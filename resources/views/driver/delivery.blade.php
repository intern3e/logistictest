@php
// ฟังก์ชันช่วยตัดบรรทัดที่อยู่ไม่ให้เกิน 150 ตัวอักษรต่อบรรทัด (รองรับภาษาไทยและ UTF-8)
$wrapAddress = function($text, $limit = 150) {
    if (empty($text)) return '';
    $text = (string)$text;

    // ตัดคำตามช่องว่างก่อน เพื่อไม่ให้ตัดกลางคำ/กลางสระ-วรรณยุกต์
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

        // เผื่อกรณีคำเดียวยาวเกิน limit (ไม่มีช่องว่างให้ตัด) ค่อยตัดตรงๆ
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
    /*
      สำคัญ: ตัวอักษรไทยขึ้นเป็น "?????" ในไฟล์ PDF เพราะ dompdf ไม่มีชุดตัวอักษรไทยในตัวเอง
      และฟอนต์ Sarabun เวอร์ชันใหม่ของ Google Fonts (ที่ใช้ตอนแรก) มีโครงสร้างตารางฟอนต์แบบใหม่
      ที่ dompdf อ่าน glyph ภาษาไทยไม่ถูกต้อง (ตัวเลข/อังกฤษขึ้นปกติ แต่ภาษาไทยเป็น "?????" ทั้งหมด)
      เปลี่ยนมาใช้ THSarabunNew ซึ่งเป็นฟอนต์ไทยรุ่นคลาสสิก โครงสร้างไฟล์เรียบง่ายกว่า และเป็นฟอนต์
      ที่ใช้กันแพร่หลายที่สุดสำหรับ dompdf ภาษาไทยโดยเฉพาะ ทดสอบแล้วทำงานได้เสถียรกว่ามาก

      วิธีติดตั้ง (สำหรับ dompdf / laravel-dompdf):
      1) เอาไฟล์ THSarabunNew.ttf และ THSarabunNew-Bold.ttf ไปวางที่ public/fonts/ ในโปรเจกต์
      2) ให้แน่ใจว่าใช้ path จริงในเครื่อง (ไม่ใช่ URL ภายนอก) เพราะ dompdf อ่านไฟล์ในเครื่องได้เสถียรกว่า
      3) ถ้าใช้ตัวสร้าง PDF อื่น (wkhtmltopdf/snappy, mpdf ฯลฯ) วิธีตั้งค่าฟอนต์จะต่างออกไปเล็กน้อย บอกได้เลยจะปรับให้ตรงเอนจิน
    */
    @font-face {
        font-family: 'THSarabunNew';
        font-style: normal;
        font-weight: 400;
        src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
    }
    @font-face {
        font-family: 'THSarabunNew';
        font-style: normal;
        font-weight: 700;
        src: url("{{ public_path('fonts/THSarabunNew-Bold.ttf') }}") format('truetype');
    }
    @font-face {
        font-family: 'THSarabunNew';
        font-style: normal;
        font-weight: 800;
        src: url("{{ public_path('fonts/THSarabunNew-Bold.ttf') }}") format('truetype');
    }
    @font-face {
        font-family: 'Sarabun';
        font-style: normal;
        font-weight: 400;
        src: url("{{ public_path('fonts/Sarabun-Regular.ttf') }}") format('truetype');
    }
    @font-face {
        font-family: 'Sarabun';
        font-style: normal;
        font-weight: 700;
        src: url("{{ public_path('fonts/Sarabun-Bold.ttf') }}") format('truetype');
    }
    @font-face {
        font-family: 'Sarabun';
        font-style: normal;
        font-weight: 800;
        src: url("{{ public_path('fonts/Sarabun-ExtraBold.ttf') }}") format('truetype');
    }

    @page { size: A4; margin: 10mm 8mm; }
    body { font-family: 'THSarabunNew', 'Sarabun', 'Segoe UI', Tahoma, Arial, sans-serif; font-size: 15px; color: #1e293b; margin: 0; padding: 0; width: 100%; }

.print-header-wrap { border-bottom: 3px solid #001137; padding-bottom: 8px; margin-bottom: 12px; }
.print-header-table { width: 100%; border-collapse: collapse; }
.print-header-table td { border: none; padding: 0; vertical-align: bottom; }
.print-header-table h1 { font-size: 26px; font-weight: 800; color: #001137; margin: 0; }
.print-header-table .sub { font-size: 14px; color: #4b5563; margin-top: 4px; }
.print-header-table .sub strong { color: #1e293b; font-weight: 700; }
.print-header-table .meta-cell { text-align: right; font-size: 15px; font-weight: 700; color: #001137; white-space: nowrap; }
.print-header-table .meta-cell-small { text-align: right; font-size: 12px; color: #6b7280; white-space: nowrap; line-height: 1.5; }

.cust-block { margin-bottom: 9px; border: 1px solid #dcdcdc; border-left: 4px solid #001137; page-break-inside: avoid; width: 100%; }
.cust-block-header { background: #fafbfd; padding: 8px 10px; border-bottom: 1px solid #dcdcdc; }
.cust-header-table { width: 100%; border-collapse: collapse; }
.cust-header-table td { border: none; padding: 0; vertical-align: middle; }
.stop-no-cell { width: 34px; padding-right: 10px; }
.stop-no-badge {
    display: inline-block; width: 30px; height: 30px; line-height: 30px;
    background: #001137; color: #ffffff; font-size: 15px; font-weight: 800;
    text-align: center; border-radius: 6px;
}
.cust-info-cell { vertical-align: top; }
.cust-info-cell .cust-title { font-weight: 800; font-size: 16.5px; color: #1e293b; }
.cust-info-cell .cust-address { display: block; margin-top: 3px; font-weight: 400; color: #5c6b7a; font-size: 13px; line-height: 1.45; }
.cust-qr-cell { width: 62px; text-align: center; vertical-align: top; }
.cust-qr-cell img { display: block; border: 1px solid #dcdcdc; }
.cust-qr-cell .qr-label { font-size: 10.5px; color: #6b7280; margin-top: 3px; }

table.job-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
table.job-table th { background: #eef1f6; color: #001137; font-weight: 700; font-size: 12.5px; padding: 6px 10px; border-bottom: 1px solid #dcdcdc; text-align: left; }
table.job-table td { border-bottom: 1px solid #eeeeea; padding: 6px 10px; text-align: left; font-size: 14.5px; word-wrap: break-word; }
table.job-table tr { page-break-inside: avoid; }
table.job-table tbody tr:last-child td { border-bottom: none; }
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
                    วิธีการจัดส่ง: <strong>{{ $box['transport_name'] }}</strong>
                    @if (!empty($box['driver_name']))
                        &nbsp;x&nbsp;ผู้รับผิดชอบ: <strong>{{ $box['driver_name'] }}</strong>
                    @else
                        &nbsp;(ไม่ระบุผู้รับผิดชอบ)
                    @endif
                </td>
                <td class="meta-cell-small">
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
                        <td class="stop-no-cell"><span class="stop-no-badge">{{ $cust['stop_no'] }}</span></td>
                        <td class="cust-info-cell">
                            <span class="cust-title">
                                {{ $cust['customer_code'] }}
                                @if (!empty($cust['customer_name'])) - {{ $cust['customer_name'] }} @endif
                            </span>
                            <span class="cust-address">{!! $wrapAddress($cust['address'] ?? '', 70) !!}</span>
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
                        <th style="width:12%">ลำดับงาน</th>
                        <th style="width:26%">เลขที่บิล</th>
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{
            --ink:#1a2634;
            --ink-soft:#5c6b7a;
            --ink-faint:#8592a0;
            --paper:#f5f6f8;
            --surface:#ffffff;
            --line:#e3e7ec;
            --line-strong:#d2d8e0;

            --delivery:#001137;
            --delivery-rgb:0,17,55;
            --delivery-soft:#eaeffa;

            --pickup:#001137;
            --pickup-rgb:0,17,55;
            --pickup-soft:#eaeffa;

            --bill:#001137;
            --bill-rgb:0,17,55;

            --doc:#f18300;
            --doc-rgb:241,131,0;

            --success:#2f7d4f;
            --danger:#c0392b;

            --row-alt:#f6f8fa;
            --row-hover:#eef2f6;
        }

        *{ box-sizing:border-box; }

        html, body{ background:var(--paper); }

        body{
            font-family:'Sarabun','Segoe UI',Tahoma,Arial,sans-serif;
            color:var(--ink);
            font-size:16px;
            line-height:1.5;
            -webkit-font-smoothing:antialiased;
        }

        .container-fluid{
            width:100%;
            max-width:1600px;
            margin-left:auto;
            margin-right:auto;
        }

        /* ---------- Header ---------- */
        .page-header{
            display:flex;
            justify-content:space-between;
            align-items:flex-end;
            flex-wrap:wrap;
            gap:16px;
            padding-bottom:22px;
            margin-bottom:30px;
            border-bottom:2px solid var(--ink);
        }
        .page-title{
            margin:0;
            font-size:1.7rem;
            font-weight:800;
            letter-spacing:-0.01em;
            color:var(--ink);
        }
        .page-subtitle{
            margin-top:5px;
            font-size:1rem;
            color:var(--ink-soft);
        }
        .page-header-user{
            display:flex;
            flex-direction:column;
            align-items:flex-end;
            gap:10px;
        }
        .user-line{
            font-size:.95rem;
            color:var(--ink-soft);
        }
        .user-line strong{
            color:var(--ink);
            font-weight:700;
        }

        /* ---------- Buttons ---------- */
        .btn-manifest{
            display:inline-flex;
            align-items:center;
            gap:7px;
            padding:10px 18px;
            border-radius:8px;
            border:1px solid transparent;
            font-size:.95rem;
            font-weight:600;
            cursor:pointer;
            text-decoration:none;
            transition:background-color .15s ease, border-color .15s ease, color .15s ease, transform .1s ease;
            white-space:nowrap;
        }
        .btn-manifest:hover{ text-decoration:none; }
        .btn-manifest:active{ transform:translateY(1px); }

        .btn-manifest-ghost-ink{
            background:var(--surface);
            border-color:var(--line-strong);
            color:var(--ink);
        }
        .btn-manifest-ghost-ink:hover{
            background:var(--paper);
            border-color:var(--ink-faint);
            color:var(--ink);
        }

        .btn-manifest-ghost-pickup{
            background:var(--pickup-soft);
            border-color:rgba(var(--pickup-rgb),.35);
            color:var(--pickup);
            padding:7px 14px;
            font-size:.88rem;
        }
        .btn-manifest-ghost-pickup:hover{
            background:rgba(var(--pickup-rgb),.18);
            color:var(--pickup);
        }

        .btn-manifest-cta{
            background:var(--ink);
            border-color:var(--ink);
            color:#fff;
            font-size:1rem;
            padding:12px 22px;
        }
        .btn-manifest-cta:hover:not(:disabled){
            background:#0f1c28;
            color:#fff;
        }
        .btn-manifest-cta:disabled{
            opacity:.4;
            cursor:not-allowed;
        }

        .btn-manifest-cancel{
            background:transparent;
            border-color:var(--line-strong);
            color:var(--ink-soft);
        }
        .btn-manifest-cancel:hover{
            background:var(--paper);
            color:var(--ink);
        }

        /* ---------- Alerts ---------- */
        .alert{
            border-radius:10px;
            border:1px solid transparent;
            border-left:4px solid transparent;
            padding:14px 18px;
            font-size:1rem;
            margin-bottom:22px;
        }
        .alert-success{
            background:#eafaf0;
            border-left-color:var(--success);
            color:#1f5f39;
        }
        .alert-danger{
            background:#fdecea;
            border-left-color:var(--danger);
            color:#8a2a20;
        }

        /* ---------- Tab switch ---------- */
        .tab-switch{
            display:inline-flex;
            gap:4px;
            padding:5px;
            background:var(--surface);
            border:1px solid var(--line-strong);
            border-radius:999px;
            margin-bottom:26px;
        }
        .tab-btn{
            display:inline-flex;
            align-items:center;
            gap:8px;
            border:none;
            background:transparent;
            color:var(--ink-soft);
            font-family:inherit;
            font-size:1rem;
            font-weight:600;
            padding:10px 22px;
            border-radius:999px;
            cursor:pointer;
            transition:background-color .15s ease, color .15s ease;
        }
        .tab-btn:hover{ color:var(--ink); }
        .tab-btn.delivery.active{
            background:var(--delivery);
            color:#fff;
        }
        .tab-btn.pickup.active{
            background:var(--pickup);
            color:#fff;
        }
        .tab-count{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:22px;
            padding:2px 8px;
            border-radius:999px;
            font-size:.8rem;
            font-weight:700;
            background:rgba(26,38,52,.08);
            color:var(--ink-soft);
        }
        .tab-btn.active .tab-count{
            background:rgba(255,255,255,.22);
            color:#fff;
        }

        @media (max-width:768px){
            .tab-switch{ width:100%; }
            .tab-btn{ flex:1; justify-content:center; }
        }

        /* ---------- Tab panels ---------- */
        .tab-panel{ display:none; }
        .tab-panel.active{ display:block; }

        /* ---------- Section heading ---------- */
        .section-heading{
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:10px;
            border-left:5px solid transparent;
            padding:8px 0 8px 14px;
            margin:38px 0 14px;
        }
        .tab-panel > .section-heading:first-child{ margin-top:4px; }
        .section-heading.accent-delivery{ border-left-color:var(--delivery); }
        .section-heading.accent-pickup{ border-left-color:var(--pickup); }
        .section-heading h5{
            margin:0;
            font-size:1.15rem;
            font-weight:800;
            color:var(--ink);
        }
        .section-count{
            font-weight:500;
            color:var(--ink-soft);
            font-size:1rem;
        }
        .section-heading .form-check{
            display:flex;
            align-items:center;
            gap:7px;
            margin:0;
        }
        .section-heading .form-check-label{
            font-size:.95rem;
            color:var(--ink-soft);
        }

        .form-check-input{
            width:18px;
            height:18px;
            accent-color:var(--delivery);
            cursor:pointer;
        }
        .form-check-input.accent-pickup{
            accent-color:var(--pickup);
        }

        /* ---------- Tables ---------- */
        .job-list-table-wrap{
            background:var(--surface);
            border:1px solid var(--line-strong);
            border-radius:10px;
            overflow:hidden;
            margin-bottom:30px;
        }
        .job-list-table{
            width:100%;
            table-layout:fixed;
            border-collapse:collapse;
            font-size:1rem;
        }
        .job-list-table thead th{
            background:var(--paper);
            color:var(--ink-soft);
            font-weight:700;
            font-size:.85rem;
            text-align:left;
            padding:14px 16px;
            border-bottom:2px solid var(--line-strong);
            border-right:1px solid var(--line);
        }
        .job-list-table thead th:last-child{ border-right:none; }
        .job-list-table tbody td{
            padding:16px;
            border-bottom:1px solid var(--line);
            border-right:1px solid var(--line);
            vertical-align:top;
            color:var(--ink);
            line-height:1.55;
        }
        .job-list-table tbody td:last-child{ border-right:none; }
        .job-list-table tbody tr:last-child td{
            border-bottom:none;
        }
        .job-list-table tbody tr.row-alt td{
            background:var(--row-alt);
        }
        .job-list-table tbody tr:not(.group-row):hover td{
            background:var(--row-hover);
        }
        .job-list-table tbody tr.row-clickable{
            cursor:pointer;
        }
        .col-check{ text-align:center; }
        .job-list-table tbody td.col-check,
        .job-list-table tbody td.job-no,
        .job-list-table tbody td.job-meta,
        .job-list-table tbody td.job-address{ vertical-align:middle; }

        .group-row td{
            padding:14px 16px;
            font-size:1rem;
            line-height:1.6;
            background:var(--paper);
            border-top:2px solid var(--line-strong);
            border-right:none;
            overflow:hidden; /* contains the floated count */
            cursor:pointer;
        }
        .job-list-table tbody tr.group-row:first-child td{ border-top:none; }
        .group-row.delivery-row td{ border-left:4px solid var(--delivery); background:var(--delivery); color:#fff; }
        .group-row.pickup-row td{ border-left:4px solid var(--pickup); background:var(--pickup); color:#fff; }
        .group-row.bill-row td{ border-left:4px solid var(--bill); background:var(--bill); color:#fff; }
        .group-row.doc-row td{ border-left:4px solid var(--doc); background:var(--doc); color:#fff; }

        .group-select-checkbox{
            width:18px;
            height:18px;
            margin-right:12px;
            vertical-align:middle;
            cursor:pointer;
            accent-color:#fff;
        }

        .group-customer-id{
            font-family:'JetBrains Mono',monospace;
            font-weight:800;
            font-size:1rem;
            color:var(--ink);
        }
        .group-customer-name{
            color:var(--ink-soft);
            font-weight:500;
            margin-left:6px;
        }
        .group-count-chip{
            float:right;
            color:var(--ink-soft);
            font-size:.9rem;
            font-weight:600;
        }
        .group-address{
            margin-left:8px;
            font-size:.9rem;
            color:var(--ink-soft);
        }
        .group-address::before{
            content:"·";
            margin-right:8px;
            color:var(--line-strong);
        }
        .group-row.delivery-row .group-customer-id,
        .group-row.delivery-row .group-customer-name,
        .group-row.delivery-row .group-count-chip,
        .group-row.delivery-row .group-address,
        .group-row.delivery-row .location-tag,
        .group-row.pickup-row .group-customer-id,
        .group-row.pickup-row .group-customer-name,
        .group-row.pickup-row .group-count-chip,
        .group-row.pickup-row .group-address,
        .group-row.pickup-row .location-tag,
        .group-row.bill-row .group-customer-id,
        .group-row.bill-row .group-customer-name,
        .group-row.bill-row .group-count-chip,
        .group-row.bill-row .group-address,
        .group-row.bill-row .location-tag,
        .group-row.doc-row .group-customer-id,
        .group-row.doc-row .group-customer-name,
        .group-row.doc-row .group-count-chip,
        .group-row.doc-row .group-address,
        .group-row.doc-row .location-tag{
            color:#fff;
        }
        .group-row.delivery-row .group-address::before,
        .group-row.pickup-row .group-address::before,
        .group-row.bill-row .group-address::before,
        .group-row.doc-row .group-address::before{
            color:rgba(255,255,255,.55);
        }

        .job-id-primary{
            display:block;
            font-family:'JetBrains Mono',monospace;
            font-weight:800;
            font-size:1.05rem;
            color:var(--ink);
            white-space:nowrap;
        }
        .job-id-secondary{
            display:block;
            margin-top:3px;
            font-family:'JetBrains Mono',monospace;
            font-size:.85rem;
            color:#5b8a80;
            white-space:nowrap;
        }

        .job-address{
            line-height:1.55;
            color:var(--ink);
        }
        .location-row{ margin-top:6px; }
        .location-tag{
            font-size:.88rem;
            color:var(--ink-soft);
        }

        .job-meta{ color:var(--ink); }
        .meta-name-chip{
            display:block;
            font-weight:700;
            font-size:1rem;
            color:var(--ink);
            margin-bottom:4px;
            white-space:nowrap;
        }
        .meta-time{
            display:block;
            font-size:.85rem;
            color:var(--ink-soft);
            white-space:nowrap;
        }

        .job-notes{
            color:var(--ink-soft);
            line-height:1.55;
        }

        .job-items-cell{
            color:var(--pickup);
            font-weight:600;
            cursor:pointer;
            text-align:center !important;
            vertical-align:middle !important;
        }
        .job-items-cell:hover{
            text-decoration:underline;
        }

        .status-pill{
            display:inline-block;
            margin-top:8px;
            padding:4px 12px;
            border-radius:999px;
            font-size:.82rem;
            font-weight:600;
            border:1px solid transparent;
        }
        .job-id-primary .status-pill{
            margin-top:0;
            margin-left:8px;
            vertical-align:middle;
            font-family:'Sarabun','Segoe UI',Tahoma,Arial,sans-serif;
        }
        .status-green,.status-success{ background:#e6f6ea; color:#1d7a3c; border-color:#bfe8cb; }
        .status-yellow,.status-warning,.status-orange{ background:#fff3e0; color:#a85d00; border-color:#ffdca8; }
        .status-red,.status-danger{
            background:#fdeaea;
            color:#b3261e;
            border-color:#f7c8c5;
            animation:statusBlink 1s ease-in-out infinite;
        }
        @keyframes statusBlink{
            0%,100%{ background:#fdeaea; color:#b3261e; border-color:#f7c8c5; }
            50%{ background:#e02424; color:#fff; border-color:#e02424; }
        }
        .status-blue,.status-info,.status-primary{ background:#e8eefc; color:#2a4494; border-color:#c6d4f5; }
        .status-gray,.status-grey,.status-secondary,.status-default,.status-light{ background:#eef0f3; color:#57626f; border-color:#dde1e6; }
        .status-purple{ background:#f2eafb; color:#6b3fa0; border-color:#ddc7f0; }
        .status-pill.status-overdue{
            background:#fdeaea;
            color:#b3261e;
            border-color:#f7c8c5;
            animation:statusBlink 1s ease-in-out infinite;
        }
        .status-pill.status-waiting{
            background:#e6f6ea;
            color:#1d7a3c;
            border-color:#bfe8cb;
        }

        .empty-note{
            border:1px dashed var(--line-strong);
            border-radius:10px;
            padding:32px;
            text-align:center;
            color:var(--ink-soft);
            font-size:1rem;
            background:var(--surface);
            margin-bottom:26px;
        }

        @media (max-width:900px){
            .job-list-table-wrap{ overflow-x:auto; }
            .job-list-table{ min-width:760px; }
        }

        /* ---------- Floating save bar ---------- */
        .save-floatbar{
            position:fixed;
            right:24px;
            bottom:24px;
            z-index:1055;
            display:flex;
            align-items:center;
            gap:8px;
            background:var(--ink);
            color:#fff;
            padding:12px 12px 12px 18px;
            border-radius:999px;
            box-shadow:0 10px 28px rgba(15,25,35,.28);
        }
        .floatbar-count{
            font-size:.78rem;
            color:rgba(255,255,255,.8);
            white-space:nowrap;
        }
        .floatbar-count strong{
            color:#fff;
            font-weight:700;
        }
        .save-floatbar .btn-manifest-cta{
            background:#fff;
            color:var(--ink);
            border-color:#fff;
            padding:6px 12px;
            font-size:.78rem;
        }
        .save-floatbar .btn-manifest-cta:hover:not(:disabled){
            background:#e7ebf1;
            color:var(--ink);
        }

        @media (max-width:640px){
            .save-floatbar{
                left:16px;
                right:16px;
                bottom:16px;
                justify-content:space-between;
            }
        }

        /* ---------- Modals ---------- */
        .modal-content{
            border:none;
            border-radius:14px;
            box-shadow:0 24px 60px rgba(15,25,35,.25);
        }
        .modal-header{
            border-bottom:1px solid var(--line);
            padding:18px 22px;
        }
        .modal-title{
            font-weight:700;
            font-size:1.1rem;
            color:var(--ink);
        }
        .modal-body{ padding:22px; }
        .modal-footer{
            border-top:1px solid var(--line);
            padding:16px 22px;
        }
        .form-label{
            font-weight:600;
            font-size:.95rem;
            color:var(--ink);
            margin-bottom:7px;
        }
        .required-mark{ color:var(--danger); }
        .optional-hint{
            font-weight:400;
            font-size:.85rem;
            color:var(--ink-faint);
        }
        .form-control{
            border:1px solid var(--line-strong);
            border-radius:8px;
            padding:10px 14px;
            font-size:1rem;
            color:var(--ink);
        }
        .form-control:focus{
            border-color:var(--delivery);
            box-shadow:0 0 0 3px rgba(var(--delivery-rgb),.15);
        }

        .autocomplete-list{
            position:absolute;
            left:0;
            right:0;
            z-index:20;
            margin-top:4px;
            max-height:240px;
            overflow-y:auto;
            background:var(--surface);
            border:1px solid var(--line-strong);
            border-radius:8px;
            box-shadow:0 14px 30px rgba(15,25,35,.14);
            display:none;
        }
        .autocomplete-item{
            padding:10px 14px;
            font-size:1rem;
            color:var(--ink);
            cursor:pointer;
        }
        .autocomplete-item:hover{ background:var(--paper); }
        .autocomplete-empty{
            padding:10px 14px;
            font-size:.95rem;
            color:var(--ink-faint);
        }

        #itemsModal .table{ font-size:1rem; margin-bottom:0; }
        #itemsModal .table th{
            color:var(--ink-soft);
            font-weight:700;
            font-size:.85rem;
            border-color:var(--line);
        }
        #itemsModal .table td{
            border-color:var(--line);
            color:var(--ink);
            vertical-align:middle;
        }

        /* ---------- Accessibility & motion ---------- */
        *:focus-visible{
            outline:2px solid var(--delivery);
            outline-offset:2px;
        }
        @media (prefers-reduced-motion: reduce){
            *{ transition:none !important; animation:none !important; }
        }

        @media (max-width:768px){
            .page-header{
                flex-direction:column;
                align-items:flex-start;
            }
            .page-header-user{
                align-items:flex-start;
                width:100%;
            }
        }
    </style>
</head>
<body class="bg-light">
<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h3 class="page-title">จ่ายงานขนส่งสินค้า</h3>
            <div class="page-subtitle">แผงควบคุมการจ่ายงานขนส่ง</div>
        </div>
        <div class="page-header-user">
            <div class="user-line">ผู้ใช้งาน: <strong>{{ $loggedInName }}</strong></div>
            <a href="{{ route('deliverytrack.summary') }}" class="btn btn-manifest btn-manifest-ghost-ink">
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
        <button type="button" class="tab-btn delivery active" id="tabBtnDelivery" onclick="switchTab('delivery')">
            🚚 ส่งของ <span class="tab-count">{{ $billCount + $docCount }}</span>
        </button>
        <button type="button" class="tab-btn pickup" id="tabBtnPickup" onclick="switchTab('pickup')">
            📦 รับของเอง <span class="tab-count">{{ $poCount }}</span>
        </button>
    </div>

    <form id="dispatchForm" method="POST" action="{{ route('deliverytrack.store') }}">
        @csrf
        <div id="jobInputs"></div>

        {{-- ================= แท็บ 1: ส่งของ (บิลขาย + บิลชั่วคราว) ================= --}}
        <div class="tab-panel active" id="panelDelivery">
            <div class="section-heading accent-delivery">
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
                            <col style="width:56px">
                            <col style="width:220px">
                            <col style="width:190px">
                            <col style="width:auto">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="col-check"></th>
                                <th>SO / บิล</th>
                                <th>ผู้หยิบสินค้า</th>
                                <th>หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($billGroups as $group)
                                @php
                                    $firstBill = $group['rows'][0]['bill'] ?? null;
                                    $groupAddressRaw = $firstBill->customer_address ?? '';
                                    $groupLocationTag = null;
                                    if (str_contains($groupAddressRaw, 'สถานที่ส่ง:')) {
                                        [$groupAddressRaw, $groupLocationTag] = array_map('trim', explode('สถานที่ส่ง:', $groupAddressRaw, 2));
                                        $groupAddressRaw = rtrim($groupAddressRaw, ', ');
                                    }
                                    $groupAddress = $wrapAddress($groupAddressRaw, 90);
                                    $groupKey = 'bill-' . $loop->index;
                                @endphp
                                <tr class="group-row bill-row" data-group="{{ $groupKey }}" onclick="toggleGroup(this)">
                                    <td colspan="4">
                                        <input type="checkbox" class="group-select-checkbox" data-group="{{ $groupKey }}"
                                               onclick="event.stopPropagation()" onchange="onGroupCheckboxChange(this)">
                                        <span class="group-customer-id">{{ $group['customer_id'] }}</span>
                                        @if (!empty($group['customer_name']))
                                            <span class="group-customer-name">- {{ $group['customer_name'] }}</span>
                                        @endif
                                        <span class="group-count-chip">{{ count($group['rows']) }} บิล</span>
                                        @if ($groupAddress)
                                            <span class="group-address">
                                                {!! $groupAddress !!}
                                                @if ($groupLocationTag)
                                                    <span class="location-tag"> · สถานที่ส่ง: {{ $groupLocationTag }}</span>
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @foreach ($group['rows'] as $row)
                                    @php
                                        $bill = $row['bill'];
                                    @endphp
                                    <tr class="{{ $loop->iteration % 2 === 0 ? 'row-alt' : '' }}">
                                        <td class="col-check">
                                            <input type="checkbox" class="job-checkbox bill-checkbox"
                                                   value="bill:{{ $bill->so_detail_id }}"
                                                   data-group="{{ $groupKey }}"
                                                   onchange="updateSelectedCount(); syncGroupCheckboxState(this.dataset.group)">
                                        </td>
                                        <td class="job-no">
                                            <span class="job-id-primary">SO {{ $bill->so_id }}</span>
                                            <span class="job-id-secondary">บิล {{ $bill->billid }}</span>
                                        </td>
                                        <td class="job-meta">
                                            <span class="meta-name-chip">{{ $bill->emp_picker ?: '-' }}</span>
                                            <span class="meta-time">{{ $bill->time }}</span>
                                        </td>
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

            <div class="section-heading accent-delivery">
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
                            <col style="width:56px">
                            <col style="width:220px">
                            <col style="width:190px">
                            <col style="width:auto">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="col-check"></th>
                                <th>เอกสาร</th>
                                <th>ผู้บันทึก</th>
                                <th>หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($docGroups as $group)
                                @php
                                    $firstDoc = $group['rows'][0]['doc'] ?? null;
                                    $groupAddressRaw = $firstDoc->com_address ?? '';
                                    $groupLocationTag = null;
                                    if (str_contains($groupAddressRaw, 'สถานที่ส่ง:')) {
                                        [$groupAddressRaw, $groupLocationTag] = array_map('trim', explode('สถานที่ส่ง:', $groupAddressRaw, 2));
                                        $groupAddressRaw = rtrim($groupAddressRaw, ', ');
                                    }
                                    $groupAddress = $wrapAddress($groupAddressRaw, 90);
                                    $groupKey = 'doc-' . $loop->index;
                                @endphp
                                <tr class="group-row doc-row" data-group="{{ $groupKey }}" onclick="toggleGroup(this)">
                                    <td colspan="4">
                                        <input type="checkbox" class="group-select-checkbox" data-group="{{ $groupKey }}"
                                               onclick="event.stopPropagation()" onchange="onGroupCheckboxChange(this)">
                                        <span class="group-customer-id">{{ $group['customer_id'] }}</span>
                                        @if (!empty($group['customer_name']))
                                            <span class="group-customer-name">- {{ $group['customer_name'] }}</span>
                                        @endif
                                        <span class="group-count-chip">{{ count($group['rows']) }} เอกสาร</span>
                                        @if ($groupAddress)
                                            <span class="group-address">
                                                {!! $groupAddress !!}
                                                @if ($groupLocationTag)
                                                    <span class="location-tag"> · สถานที่ส่ง: {{ $groupLocationTag }}</span>
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @foreach ($group['rows'] as $row)
                                    @php
                                        $doc = $row['doc'];
                                    @endphp
                                    <tr class="{{ $loop->iteration % 2 === 0 ? 'row-alt' : '' }}">
                                        <td class="col-check">
                                            <input type="checkbox" class="job-checkbox doc-checkbox"
                                                   value="doc:{{ $doc->doc_id }}"
                                                   data-group="{{ $groupKey }}"
                                                   onchange="updateSelectedCount(); syncGroupCheckboxState(this.dataset.group)">
                                        </td>
                                        <td class="job-no">
                                            <span class="job-id-primary">เอกสาร {{ $doc->doc_id }}</span>
                                            <span class="job-id-secondary">{{ $doc->contact_name }}</span>
                                        </td>
                                        <td class="job-meta">
                                            <span class="meta-name-chip">{{ $doc->emp_name ?: '-' }}</span>
                                            <span class="meta-time">{{ $doc->time }}</span>
                                        </td>
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
        {{-- ================= แท็บ 2: รับของเอง (PO) ================= --}}
        <div class="tab-panel" id="panelPickup">
            <div class="section-heading accent-pickup">
                <h5>งาน PO รับเอง <span class="section-count">— {{ $poCount }} รายการ</span></h5>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input accent-pickup" id="checkAllPo">
                    <label class="form-check-label" for="checkAllPo">เลือกทั้งหมด (รับของเอง)</label>
                </div>
            </div>

            @if (count($poGroups) > 0)
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
                                <th>PO / SO</th>
                                <th>วิธีรับของ</th>
                                <th>วันที่นัด</th>
                                <th>สินค้า</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($poGroups as $group)
                                @php $groupKey = 'po-' . $loop->index; @endphp
                                <tr class="group-row pickup-row" data-group="{{ $groupKey }}" onclick="toggleGroup(this)">
                                    <td colspan="5">
                                        <input type="checkbox" class="group-select-checkbox" data-group="{{ $groupKey }}"
                                               onclick="event.stopPropagation()" onchange="onGroupCheckboxChange(this)">
                                        <span class="group-customer-id">{{ $group['customer_id'] }}</span>
                                        @if (!empty($group['customer_name']))
                                            <span class="group-customer-name">- {{ $group['customer_name'] }}</span>
                                        @endif
                                        <span class="group-count-chip">{{ count($group['rows']) }} PO</span>
                                        @if (!empty($group['vendor_address']))
                                            <span class="group-address">{!! $wrapAddress($group['vendor_address'], 70) !!}</span>
                                        @endif
                                    </td>
                                </tr>
                                @foreach ($group['rows'] as $row)
                                    @php $po = $row['po']; @endphp
                                    <tr class="{{ $loop->iteration % 2 === 0 ? 'row-alt' : '' }}">
                                        <td class="col-check">
                                            <input type="checkbox" class="job-checkbox po-checkbox pickup-checkbox"
                                                   value="po:{{ $po->PONum }}"
                                                   data-po="{{ $po->PONum }}"
                                                   data-group="{{ $groupKey }}"
                                                   onchange="syncSamePO(this); updateSelectedCount(); syncGroupCheckboxState(this.dataset.group)">
                                        </td>
                                        <td class="job-no">
                                            <span class="job-id-primary">PO {{ $po->PONum }} @php $statusLabel = trim($po->status_label ?? ''); $statusExtraClass = $statusLabel === 'เลยกำหนด' ? ' status-overdue' : ($statusLabel === 'รอเข้า' ? ' status-waiting' : ''); @endphp<span class="status-pill status-{{ $po->status_color }}{{ $statusExtraClass }}">{{ $po->status_label }}</span></span>
                                            <span class="job-id-secondary">SO {{ $po->SONum }}</span>
                                        </td>
                                        <td class="job-address">
                                            {{ $po->DeliveryMethod }}
                                        </td>
                                        <td class="job-meta">{{ $po->DeliveryDate ?: '-' }}</td>
                                        <td class="job-notes job-items-cell"
                                            onclick='event.stopPropagation(); showItemsModal("PO {{ $po->PONum }}", @json($po->items))'>
                                            🔎 ดูรายการ ({{ count($po->items) }})
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

    </form>
</div>

{{-- ===== ปุ่มบันทึก ลอยติดมุมขวาล่างของจอเสมอ ===== --}}
<div class="save-floatbar">
    <span class="floatbar-count">เลือกไว้ <strong id="selectedCount">0</strong> รายการ</span>
    <button type="button" id="openModalBtn" class="btn btn-manifest-cta" disabled>
        บันทึกข้อมูลขนส่ง
    </button>
</div>

{{-- Modal เลือกวันที่จัดส่ง + วิธีการจัดส่ง + ผู้รับผิดชอบ --}}
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
                <button type="button" class="btn btn-manifest btn-manifest-cancel" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" id="confirmSaveBtn" class="btn btn-manifest-cta">ยืนยันบันทึก</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal รายการสินค้า/จำนวน (PO) --}}
<div class="modal fade" id="itemsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
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

// PO เดียวกัน (ที่แตกเป็นหลาย SO/หลายแถว) ให้ถือเป็นงานเดียวกัน — ติ๊กแถวใดแถวหนึ่งแล้ว ติ๊กให้ครบทุกแถวที่เป็น PO เดียวกันอัตโนมัติ
function syncSamePO(checkbox) {
    const poNum = checkbox.dataset.po;
    if (!poNum) return;
    const affectedGroups = new Set();
    document.querySelectorAll('.po-checkbox[data-po="' + CSS.escape(poNum) + '"]').forEach(cb => {
        cb.checked = checkbox.checked;
        if (cb.dataset.group) affectedGroups.add(cb.dataset.group);
    });
    affectedGroups.forEach(groupId => syncGroupCheckboxState(groupId));
}

// ตั้งค่าติ๊ก/ไม่ติ๊กให้ครบทุกแถวของหัวข้อลูกค้ากลุ่มนั้น แล้วอัปเดตสถานะ checkbox หัวข้อให้ตรงกัน
function setGroupChecked(groupId, checked) {
    const checkboxes = document.querySelectorAll('.job-checkbox[data-group="' + CSS.escape(groupId) + '"]');
    checkboxes.forEach(cb => {
        cb.checked = checked;
        if (cb.classList.contains('po-checkbox')) {
            syncSamePO(cb);
        }
    });
    updateSelectedCount();
    syncGroupCheckboxState(groupId);
}

// คลิกที่หัวข้อลูกค้า (group-row) แล้วติ๊ก/เอาติ๊กออกให้ครบทุกแถวของหัวข้อนั้น
function toggleGroup(groupRow) {
    const groupId = groupRow.dataset.group;
    if (!groupId) return;
    const checkboxes = document.querySelectorAll('.job-checkbox[data-group="' + CSS.escape(groupId) + '"]');
    if (checkboxes.length === 0) return;

    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    setGroupChecked(groupId, !allChecked);
}

// คลิกที่ checkbox เล็กๆ ในหัวข้อโดยตรง (ไม่ต้องคลิกทั้งแถว)
function onGroupCheckboxChange(checkbox) {
    const groupId = checkbox.dataset.group;
    if (!groupId) return;
    setGroupChecked(groupId, checkbox.checked);
}

// ปรับสถานะ checkbox ที่หัวข้อให้ตรงกับแถวลูกทั้งหมด: ติ๊กครบ = ติ๊ก, ติ๊กบางส่วน = indeterminate, ไม่ติ๊กเลย = ไม่ติ๊ก
function syncGroupCheckboxState(groupId) {
    if (!groupId) return;
    const groupCb = document.querySelector('.group-select-checkbox[data-group="' + CSS.escape(groupId) + '"]');
    if (!groupCb) return;
    const checkboxes = document.querySelectorAll('.job-checkbox[data-group="' + CSS.escape(groupId) + '"]');
    const total = checkboxes.length;
    const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
    groupCb.checked = total > 0 && checkedCount === total;
    groupCb.indeterminate = checkedCount > 0 && checkedCount < total;
}

function syncAllGroupCheckboxStates() {
    document.querySelectorAll('.group-select-checkbox').forEach(cb => {
        syncGroupCheckboxState(cb.dataset.group);
    });
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
        syncAllGroupCheckboxStates();
    });

    checkAllDocs?.addEventListener('change', function () {
        document.querySelectorAll('.doc-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
        syncAllGroupCheckboxStates();
    });

    checkAllPo?.addEventListener('change', function () {
        document.querySelectorAll('.po-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
        syncAllGroupCheckboxStates();
    });

    syncAllGroupCheckboxStates();

    // คลิกที่ไหนก็ได้ในแถวข้อมูล (ยกเว้นตัวเช็กบ็อกซ์เอง หรือปุ่ม/ลิงก์ในแถว) ให้ติ๊กเช็กบ็อกซ์ของแถวนั้นแทน
    document.querySelectorAll('.job-list-table tbody tr').forEach(function (row) {
        const checkbox = row.querySelector('.job-checkbox');
        if (!checkbox) return; // แถวหัวลูกค้า (group-row) ไม่มีเช็กบ็อกซ์ ข้ามไป

        row.classList.add('row-clickable');

        row.addEventListener('click', function (e) {
            if (e.target.closest('input, button, a, label')) return;
            checkbox.checked = !checkbox.checked;
            checkbox.dispatchEvent(new Event('change'));
        });
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