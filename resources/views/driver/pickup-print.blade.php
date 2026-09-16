<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 24px 28px; }
        * { font-family: 'THSarabunNew', 'Sarabun', sans-serif; }
        body { color:#1a1a1a; font-size: 15px; }
        h1 { font-size: 20px; margin:0 0 2px; }
        .sub { color:#555; font-size: 13px; margin-bottom: 12px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #999; padding:5px 8px; vertical-align: top; }
        th { background:#eef2f7; text-align:left; font-size: 14px; }
        .po { font-weight:700; }
        .vendor { font-weight:700; }
        .addr { color:#555; font-size: 13px; }
        .items { font-size: 13px; color:#333; }
        .items div { margin:1px 0; }
        .foot { margin-top: 14px; color:#666; font-size:12px; text-align:right; }
    </style>
</head>
<body>
    <h1>ใบงานรับของ (ไปรับเอง)</h1>
    <div class="sub">
        @if($date) วันที่: {{ $date }} · @endif
        จำนวน {{ count($jobs) }} รายการ · พิมพ์โดย {{ $printedBy }} · {{ \Carbon\Carbon::parse($printedAt)->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:28px;">#</th>
                <th style="width:150px;">ผู้ขาย / ที่อยู่</th>
                <th style="width:110px;">PO / SO</th>
                <th>รายการสินค้า</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobs as $i => $po)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div class="vendor">{{ $po->VendorName ?: '-' }}</div>
                        @if(!empty($po->vendor_address))<div class="addr">{{ $po->vendor_address }}</div>@endif
                    </td>
                    <td>
                        <div class="po">{{ $po->PONum }}</div>
                        <div class="addr">SO {{ $po->SONum }}</div>
                    </td>
                    <td class="items">
                        @forelse(($po->items ?? collect()) as $it)
                            <div>• {{ $it['name'] }} @if(!empty($it['qty']))<b>× {{ $it['qty'] }}</b>@endif</div>
                        @empty
                            <div>-</div>
                        @endforelse
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="foot">ผู้ไปรับของ ______________________  วันที่ ____/____/________</div>
</body>
</html>
