<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ระบบติดตามสถานะคำสั่งซื้อและจัดส่ง (SO Tracking)</title>
    <!-- Google Fonts & FontAwesome Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Sarabun', sans-serif;
        }

        body {
            background-color: #f4f7fc;
            color: #333;
            padding: 10px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            background: #fff;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        /* จัดให้ Header และกล่องสถิติอยู่บรรทัดเดียวกัน */
        .top-header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #edf2f7;
            padding-bottom: 15px;
            gap: 20px;
            flex-wrap: wrap;
        }

        header h1 {
            font-size: 22px;
            color: #2c3e50;
            white-space: nowrap;
        }

        header h1 i {
            color: #3498db;
            margin-right: 8px;
        }

        /* กล่องสถิติ เอาพื้นหลังและกรอบออกแล้ว */
        .stats-container {
            display: flex;
            gap: 25px;
            flex: 1;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .stat-card {
            background: transparent;
            padding: 0;
            min-width: 180px;
            box-shadow: none;
        }

        /* ===== สรุปความคืบหน้าแต่ละขั้น ===== */
        .stage-summary {
            margin-bottom: 20px;
        }

        .stage-summary-caption {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .stage-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .stage-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            background: #fff;
        }

        .stage-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }

        .stage-card-head i {
            color: #3498db;
            margin-right: 6px;
        }

        .stage-percent {
            font-size: 13px;
            color: #166534;
            font-weight: 700;
        }

        .stage-bar {
            height: 6px;
            background: #f1f5f9;
            border-radius: 99px;
            margin: 10px 0;
            overflow: hidden;
        }

        .stage-bar span {
            display: block;
            height: 100%;
            background: #22c55e;
            border-radius: 99px;
        }

        .stage-counts {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
        }

        .stage-counts .done {
            color: #166534;
        }

        .stage-counts .pending {
            color: #b45309;
        }

        .stage-counts strong {
            font-size: 16px;
            margin-left: 4px;
        }

        @media (max-width: 768px) {
            .stage-grid {
                grid-template-columns: 1fr;
            }
        }

        .actions-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ช่องค้นหาให้กว้างคงที่ ไม่ยืดกินพื้นที่ทั้งแถว */
        .search-box {
            position: relative;
            flex: 1 1 300px;
            min-width: 240px;
            max-width: 420px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s;
        }

        .search-box input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        /* ส่วนฟิลเตอร์ยืดเต็มพื้นที่ที่เหลือ */
        .filter-date-form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex: 0 0 auto;
            margin-left: auto; /* ชิดขวา */
        }

        .filter-date-form input[type="date"] {
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        .stage-select {
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            background-color: #fff;
            color: #334155;
            cursor: pointer;
            /* dropdown กว้างพอดีข้อความ ไม่ยืด */
            flex: 0 0 190px;
            width: 190px;
        }

        .filter-date-form input[type="date"] {
            flex: 0 0 160px;
        }

        .btn-filter, .btn-reset {
            flex-shrink: 0;
            white-space: nowrap;
        }

        .stage-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        /* ไฮไลต์ dropdown ที่กำลังกรองอยู่ */
        .stage-select.active {
            border-color: #3498db;
            background-color: #eff6ff;
            color: #1e40af;
            font-weight: 600;
        }

        @media (max-width: 1100px) {
            .filter-date-form {
                flex-wrap: wrap;
            }
        }

        .btn-filter, .btn-reset {
            padding: 9px 15px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-filter {
            background-color: #3498db;
            color: white;
        }

        .btn-filter:hover {
            background-color: #2980b9;
        }

        .btn-reset {
            background-color: #94a3b8;
            color: white;
        }

        .btn-reset:hover {
            background-color: #64748b;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid #e2e8f0;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        tbody td {
            text-align: center;
        }

        thead th {
            text-align: center;
        }

        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge.success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge.pending {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge.processing {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .badge.danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* เวลา/ชื่อผู้ทำรายการ ใต้ badge */
        .status-meta {
            font-size: 11px;
            color: #64748b;
            margin-top: 4px;
            line-height: 1.5;
        }

        .alert-message {
            padding: 12px;
            background-color: #fee2e2;
            color: #991b1b;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .action-btns {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #64748b;
            transition: color 0.2s;
        }

        .action-btn.edit:hover {
            color: #3498db;
        }

        .action-btn.delete:hover {
            color: #e74c3c;
        }

        /* ===================== Pagination — standard modern web style ===================== */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 14px;
        }

        .pagination-info {
            font-size: 13.5px;
            color: #64748b;
        }

        .pagination-info strong {
            color: #1e293b;
            font-weight: 600;
        }

        /* ซ่อนข้อความ "Showing X to Y of Z results" ที่ Laravel สร้างมาให้อัตโนมัติ (ซ้ำกับ .pagination-info ที่ทำเองด้านซ้ายแล้ว) */
        .pagination-container nav p,
        .pagination-container nav > div:first-child {
            display: none !important;
        }

        /* จัดแต่งกล่องรายการปุ่ม Pagination ให้เป็นระเบียบ */
        .pagination-container nav {
            display: flex;
            justify-content: flex-end;
        }

        .pagination-container nav ul.pagination,
        .pagination-container nav > div:last-child > div {
            display: flex;
            list-style: none;
            gap: 4px;
            align-items: center;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }

        .pagination-container svg {
            width: 15px;
            height: 15px;
        }

        /* ปุ่มตัวเลขหน้าและปุ่มลูกศร (ค่าเริ่มต้น) */
        .pagination-container nav a,
        .pagination-container nav span:not([aria-current="page"]) span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            color: #475569;
            background-color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            line-height: 1;
            transition: border-color 0.15s ease, color 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
        }

        /* หน้าที่กำลังเลือกอยู่ (Active) */
        .pagination-container nav span[aria-current="page"] span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 10px;
            background-color: #3498db !important;
            color: #fff !important;
            border: 1px solid #3498db !important;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(52, 152, 219, 0.4);
        }

        /* เอฟเฟกต์ตอนชี้เมาส์ (Hover) */
        .pagination-container nav a:hover {
            border-color: #3498db;
            color: #3498db;
            background-color: #fff;
            box-shadow: 0 1px 4px rgba(52, 152, 219, 0.18);
        }

        .pagination-container nav a:active {
            background-color: #eff6ff;
        }

        /* ปุ่มที่ถูกปิดใช้งาน (Disabled) เช่น หน้าแรก/หน้าสุดท้าย */
        .pagination-container nav span[aria-disabled="true"] span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            border-radius: 8px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #cbd5e1;
            cursor: not-allowed;
        }

        /* จุดไข่ปลา "..." ให้ดูเป็นข้อความเฉย ๆ ไม่เหมือนปุ่ม */
        .pagination-container nav span:not([aria-current="page"]):not([aria-disabled="true"]) span {
            background: transparent;
            border-color: transparent;
            box-shadow: none;
            color: #94a3b8;
            font-weight: 600;
            cursor: default;
        }

        @media (max-width: 640px) {
            .pagination-container {
                flex-direction: column;
                align-items: flex-start;
            }
            .pagination-container nav {
                justify-content: flex-start;
                width: 100%;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- จัดให้ Header และตัวเลขสถิติอยู่บรรทัดเดียวกันแบบไม่มีกรอบพื้นหลัง -->
        <div class="top-header-section">
            <header style="border: none; margin: 0; padding: 0;">
                <h1><i class="fa-solid fa-truck-fast"></i> ติดตามสถานะคำสั่งซื้อและจัดส่ง (SO Tracking)</h1>
            </header>

            <div class="stats-container">
                <div class="stat-card total">
                    <div style="font-size: 12px; color: #64748b; font-weight: 600;">คำสั่งซื้อทั้งหมด (ตั้งแต่ {{ \Carbon\Carbon::parse($startDate ?? '2026-09-19')->format('d/m/Y') }})</div>
                    <div style="font-size: 20px; font-weight: 700; color: #0c4a6e; margin-top: 2px;">
                        {{ number_format($totalCount ?? 0) }} <span style="font-size: 13px; font-weight: normal; color: #64748b;">รายการ</span>
                    </div>
                </div>

                <div class="stat-card today">
                    <div style="font-size: 12px; color: #64748b; font-weight: 600;">คำสั่งซื้อในวันนี้ ({{ \Carbon\Carbon::today()->format('d/m/Y') }})</div>
                    <div style="font-size: 20px; font-weight: 700; color: #14532d; margin-top: 2px;">
                        {{ number_format($todayCount ?? 0) }} <span style="font-size: 13px; font-weight: normal; color: #64748b;">รายการ</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- สรุปความคืบหน้า: เสร็จแล้ว / ยังไม่เสร็จ ของแต่ละขั้น -->
        <div class="stage-summary">
            <div class="stage-summary-caption">
                @if(request('date') || request('search'))
                    สรุปตามเงื่อนไขที่กรอง
                    @if(request('date')) (วันที่ส่ง {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}) @endif
                @else
                    สรุปทั้งระบบ
                @endif
                · {{ number_format($activeCount ?? 0) }} รายการ
                @if(($cancelledCount ?? 0) > 0)
                    (ไม่รวมยกเลิก {{ number_format($cancelledCount) }} รายการ)
                @endif
            </div>

            <div class="stage-grid">
                @foreach($stageStats ?? [] as $stage)
                    <div class="stage-card">
                        <div class="stage-card-head">
                            <span><i class="fa-solid {{ $stage['icon'] }}"></i>{{ $stage['label'] }}</span>
                            <span class="stage-percent">{{ $stage['percent'] }}%</span>
                        </div>
                        <div class="stage-bar"><span style="width: {{ $stage['percent'] }}%;"></span></div>
                        <div class="stage-counts">
                            <span class="done"><i class="fa-solid fa-check"></i> เสร็จแล้ว<strong>{{ number_format($stage['done']) }}</strong></span>
                            <span class="pending"><i class="fa-solid fa-clock"></i> ยังไม่เสร็จ<strong>{{ number_format($stage['pending']) }}</strong></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ฟอร์มค้นหาและกรองวันที่ (ค้นหาทั้งฐานข้อมูล ไม่ใช่แค่หน้าที่แสดงอยู่) -->
        <form action="{{ route('admin.dashboardadmin') }}" method="GET" class="actions-bar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="ค้นหา รหัสลูกค้า, รหัส SO, PO...">
            </div>

            <div class="filter-date-form">
                <select name="bill_status" class="stage-select {{ request('bill_status') ? 'active' : '' }}" onchange="this.form.submit()">
                    <option value="">เปิดบิลส่งของ: ทั้งหมด</option>
                    <option value="done" @selected(request('bill_status') === 'done')>เปิดบิลแล้ว</option>
                    <option value="pending" @selected(request('bill_status') === 'pending')>รอดำเนินการ</option>
                    <option value="cancel" @selected(request('bill_status') === 'cancel')>ยกเลิก</option>
                </select>

                <select name="route_status" class="stage-select {{ request('route_status') ? 'active' : '' }}" onchange="this.form.submit()">
                    <option value="">จัดเส้นทาง: ทั้งหมด</option>
                    <option value="done" @selected(request('route_status') === 'done')>จัดเส้นทางแล้ว</option>
                    <option value="pending" @selected(request('route_status') === 'pending')>รอดำเนินการ</option>
                </select>

                <select name="deli_status" class="stage-select {{ request('deli_status') ? 'active' : '' }}" onchange="this.form.submit()">
                    <option value="">ส่งสินค้า: ทั้งหมด</option>
                    <option value="success" @selected(request('deli_status') === 'success')>จัดส่งสำเร็จ</option>
                    <option value="hold" @selected(request('deli_status') === 'hold')>ค้างบิล</option>
                    <option value="wrong" @selected(request('deli_status') === 'wrong')>สินค้าผิด</option>
                    <option value="pending" @selected(request('deli_status') === 'pending')>รอดำเนินการ</option>
                </select>

                <input type="date" name="date" value="{{ request('date') }}" min="{{ $startDate ?? '2026-09-19' }}">
                <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> ค้นหา / กรอง</button>
                <a href="{{ route('admin.dashboardadmin') }}" class="btn-reset"><i class="fa-solid fa-rotate-right"></i> รีเซ็ต</a>
            </div>
        </form>

        @if(isset($message))
            <div class="alert-message">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
            </div>
        @endif

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>รหัสลูกค้า</th>
                        <th>รหัส SO</th>
                        <th>PO</th>
                        <th>เปิดบิลส่งของ</th>
                        <th>จัดเส้นทาง</th>
                        <th>ส่งสินค้า</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($bill as $item)
                    <tr>
                        <td><strong>{{ $item->customer_id ?? '-' }}</strong></td>
                        <td><strong>{{ $item->so_id ?? '-' }}</strong></td>
                        <td>{{ $item->billid ?? '-' }}</td>

                        <td>
                            @if(isset($item->statuspdf) && $item->statuspdf == 6)
                                <span class="badge danger"><i class="fa-solid fa-ban"></i> ยกเลิก</span>
                            @elseif(isset($item->statuspdf) && $item->statuspdf == 1)
                                <span class="badge success"><i class="fa-solid fa-check"></i> เปิดบิลแล้ว</span>
                            @else
                                <span class="badge pending"><i class="fa-solid fa-clock"></i> รอดำเนินการ</span>
                            @endif

                            @if(isset($item->time))
                                <div class="status-meta">
                                    <i class="fa-regular fa-clock"></i> {{ $item->time }}
                                </div>
                            @endif
                        </td>

                        <td>
                            @if(isset($item->statuspdf) && $item->statuspdf == 6)
                                <span class="badge danger"><i class="fa-solid fa-ban"></i> ยกเลิก</span>
                            @elseif(!empty($item->pack_time))
                                <span class="badge success"><i class="fa-solid fa-check"></i> จัดเส้นทางแล้ว</span>
                                <div class="status-meta">
                                    <i class="fa-regular fa-clock"></i> {{ $item->pack_time }}
                                </div>
                            @else
                                <span class="badge pending"><i class="fa-solid fa-clock"></i> รอดำเนินการ</span>
                            @endif
                        </td>

                        <td>
                            @if(isset($item->statuspdf) && $item->statuspdf == 6)
                                <span class="badge danger"><i class="fa-solid fa-ban"></i> ยกเลิก</span>
                            @elseif(!empty($item->statusdeli))
                                @php
                                    $deliBadge = match($item->statusdeli) {
                                        'จัดส่งสำเร็จ' => 'success',
                                        'สินค้าผิด' => 'danger',
                                        'ค้างบิล' => 'pending',
                                        default => 'processing',
                                    };
                                @endphp
                                <span class="badge {{ $deliBadge }}"><i class="fa-solid fa-truck"></i> {{ $item->statusdeli }}</span>

                                {{-- เวลาที่กดยืนยันผลส่ง + ชื่อผู้กด (จาก transaction_transport.check_time / check_name) --}}
                                @if(!empty($item->deli_time))
                                    <div class="status-meta">
                                        <i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($item->deli_time)->format('Y-m-d H:i') }}
                                    </div>
                                @endif
                            @else
                                <span class="badge pending"><i class="fa-solid fa-clock"></i> รอดำเนินการ</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">ไม่พบข้อมูลคำสั่งซื้อในระบบ</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ส่วนแบ่งหน้า Pagination ดีไซน์ใหม่ -->
        <div class="pagination-container">
            <div class="pagination-info">
                แสดงผล <strong>{{ $bill->firstItem() ?? 0 }}</strong> ถึง <strong>{{ $bill->lastItem() ?? 0 }}</strong> จากทั้งหมด <strong>{{ $bill->total() }}</strong> รายการ
            </div>
            <div>
                {{ $bill->links() }}
            </div>
        </div>
    </div>

    <!-- Script ค้นหาแบบ Real-time -->
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#tableBody tr');

            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>


</body>
</html>