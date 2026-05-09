<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งเตือนบิล</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.blade.css') }}">
    <style>
        nav[role="navigation"] > div > p { display:none !important; }
        nav[role="navigation"] .sm\:hidden { display:none !important; }

        .pagination-wrap {
            display:flex;
            justify-content:center;
            margin:20px 0;
        }

        nav[role="navigation"] > div {
            display:flex !important;
            align-items:center !important;
            justify-content:center !important;
            gap:16px;
            flex-wrap:nowrap !important;
        }

        nav[role="navigation"] a[rel="prev"] span,
        nav[role="navigation"] a[rel="next"] span { display:none !important; }

        nav[role="navigation"] a[rel="prev"],
        nav[role="navigation"] a[rel="next"] {
            width:40px; height:40px; border-radius:50%;
            background:#e5e7eb;
            display:flex; align-items:center; justify-content:center;
        }

        nav[role="navigation"] svg {
            width:20px !important; height:20px !important;
            stroke-width:3;
        }

        nav[role="navigation"] a.relative,
        nav[role="navigation"] span.relative {
            min-width:32px; height:32px;
            display:flex; align-items:center; justify-content:center;
            font-size:16px; font-weight:600;
            color:#2563eb;
            background:transparent !important;
            border:none !important;
        }

        nav[role="navigation"] span[aria-current="page"] {
            font-weight:800;
            color:#1d4ed8;
        }
      </style>
</head>
<body>

    <div class="header">
        <h2>แจ้งเตือนบิลที่ยังไม่มีในระบบ</h2>

        <div class="buttons">
            <span>ผู้ใช้: {{ request()->get('create_by', 'Guest') }}</span>
            <a href="http://server_update:8000/solist" class="btn btn-danger">หน้าหลัก</a>
            <a href="{{ route('sale.dashboard') }}" style="background-color: #0077ff; color: white; padding: 6px 8px; border-radius: 5px; text-decoration: none;">ข้อมูลจัดส่ง</a>
        </div>
    </div>

    <div class="filter-container">
        <form method="GET" action="{{ route('alert.alertbill') }}" class="filter-form">
            <label for="date">วันที่: เดือน / วัน / ปี</label>
            <input type="date" id="date" name="date"
                value="{{ $date ?? \Carbon\Carbon::today()->format('Y-m-d') }}" required>

            <button type="submit" style="background-color: #0077ff; color: white; padding: 6px 14px; border-radius: 5px; border: none; cursor: pointer; margin-left: 10px;">
                ตรวจสอบ
            </button>
        </form>
    </div>

    @if($error)
        <div style="background:#f8d7da; border-left:4px solid #dc3545; padding:12px 15px; margin:15px 20px; border-radius:5px; color:#721c24;">
            {{ $error }}
        </div>
    @endif

    @if($date && !$error)
        <div style="background:#fff3cd; border-left:4px solid #ffc107; padding:12px 15px; margin:15px 20px; border-radius:5px; color:#856404;">
            วันที่: <strong>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</strong>
            &nbsp;|&nbsp; พบบิลที่ยังไม่มีในระบบ:
            <strong style="color:#dc3545;">{{ $missingBills->total() }} รายการ</strong>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ลำดับ</th>
                        <th>เลขที่บิล</th>
                        <th>อ้างอิงใบสั่งขาย</th>
                        <th>วันที่เอกสาร</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($missingBills as $i => $item)
                        <tr>
                            <td>{{ ($missingBills->firstItem() ?? 0) + $i }}</td>
                            <td><strong>{{ $item['DocuNo'] ?? '-' }}</strong></td>
                            <td>{{ $item['SONo'] ?? '-' }}</td>
                            <td>{{ isset($item['DocuDate']) ? \Carbon\Carbon::parse($item['DocuDate'])->format('d/m/Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:20px;">
                                ไม่พบบิลที่ขาดในระบบ ข้อมูลครบทุกใบ
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            {{ $missingBills->appends(request()->query())->links() }}
        </div>
    @else
        @if(!$error)
            <div class="table-container">
                <p style="text-align:center; padding:30px; color:#888;">
                    กรุณาเลือกวันที่เพื่อตรวจสอบ
                </p>
            </div>
        @endif
    @endif

</body>
</html>