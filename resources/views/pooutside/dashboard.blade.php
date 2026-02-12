<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบติดตามคำสั่งซื้อภายนอก</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ─── RESET & VARIABLES ─── */
        :root {
            --navy:       #1b2d4f;
            --navy-mid:   #243a5e;
            --navy-light: #3a5a8a;
            --accent:     #4a90d9;
            --accent-soft:#d6e8f7;
            --white:      #ffffff;
            --off-white:  #f4f6f9;
            --border:     #e2e6ec;
            --text-main:  #1e293b;
            --text-mid:   #5a6a7e;
            --text-soft:  #94a3b5;
            --row-even:   #f8fafc;
            --row-hover:  #eef4fb;
            --shadow-sm:  0 1px 3px rgba(27,45,79,.08);
            --shadow-md:  0 3px 12px rgba(27,45,79,.10);
            --shadow-lg:  0 6px 24px rgba(27,45,79,.13);
            --radius:     10px;
            --radius-sm:  6px;
            --transition:.22s ease;
            
            --status-received: #10b981;
            --status-shipping: #f59e0b;
            --status-complete: #06b6d4;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background: #f8f9fa;
            color: var(--text-main);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
        }

        a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: color var(--transition);
        }
        a:hover { color: var(--navy); }

        /* ─── LAYOUT WRAPPER ─── */
        .page-wrap {
            flex: 1;
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0;
            min-height: 100vh;
        }

        /* ─── HEADER ─── */
        .header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 50%, var(--navy-light) 100%);
            color: var(--white);
            border-radius: 0;
            padding: 28px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: 0 4px 12px rgba(27,45,79,.15);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.4), transparent);
        }

        .header h2 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: .3px;
            position: relative;
            z-index: 1;
        }

        /* ─── BUTTONS ─── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all .2s ease;
            white-space: nowrap;
            text-decoration: none;
            color: var(--white);
            box-shadow: var(--shadow-sm);
        }
        
        .btn:active { 
            transform: translateY(1px);
            box-shadow: none;
        }

        .btn-primary { 
            background: linear-gradient(135deg, var(--accent) 0%, #3a7bc8 100%);
        }
        .btn-primary:hover { 
            background: linear-gradient(135deg, #3a7bc8 0%, var(--accent) 100%);
            box-shadow: var(--shadow-md);
        }

        .btn-warning { 
            background: linear-gradient(135deg, #f5a623 0%, #e89e1f 100%);
        }
        .btn-warning:hover { 
            background: linear-gradient(135deg, #e89e1f 0%, #f5a623 100%);
            box-shadow: var(--shadow-md);
        }

        .btn-danger { 
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }
        .btn-danger:hover { 
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
            box-shadow: var(--shadow-md);
        }

        /* ─── CARD SHELL ─── */
        .card {
            flex: 1;
            background: var(--white);
            border-radius: 0;
            box-shadow: none;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        /* ─── FILTER ─── */
        .filter-container {
            padding: 28px 40px;
            border-bottom: 2px solid var(--border);
            background: linear-gradient(to bottom, var(--white) 0%, var(--off-white) 100%);
        }

        .filter-row {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            max-width: 900px;
        }

        .filter-group {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-size: 13px;
            font-weight: 700;
            color: var(--navy);
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .search-input-wrapper {
            position: relative;
        }

        .filter-group input {
            font-family: inherit;
            font-size: 14px;
            padding: 12px 16px 12px 42px;
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--white);
            color: var(--text-main);
            outline: none;
            transition: all var(--transition);
            box-shadow: var(--shadow-sm);
            width: 100%;
        }
        
        .filter-group input:focus {
            border-color: var(--accent);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(74,144,217,.12), var(--shadow-md);
        }
        
        .filter-group input::placeholder { 
            color: var(--text-soft);
            font-weight: 400;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
        }

        /* ─── INFO BAR ─── */
        .info-bar {
            padding: 16px 40px;
            border-bottom: 2px solid var(--border);
            background: linear-gradient(to bottom, var(--off-white) 0%, var(--white) 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-bar-left {
            color: var(--text-mid);
            font-size: 14px;
            font-weight: 600;
        }

        .count-badge {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
            color: var(--white);
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 14px;
            margin-left: 8px;
        }

        /* ─── PO ROWS ─── */
        .po-list {
            flex: 1;
            overflow: auto;
        }

        .po-row {
            border-bottom: 2px solid var(--border);
            transition: all 0.2s ease;
            background: var(--white);
        }

        .po-row:nth-child(even) {
            background-color: #fafbfc;
        }

        .po-row:hover {
            background-color: #f0f7ff;
            border-left: 4px solid var(--accent);
        }

        .po-row.expanded {
            background-color: #fff;
            box-shadow: inset 0 0 0 3px var(--accent-soft);
            border-left: 4px solid var(--accent);
        }

        .po-header {
            display: grid;
            grid-template-columns: 50px 160px 240px 500px 60px;
            gap: 24px;
            padding: 24px 40px;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }

        .expand-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: var(--off-white);
            transition: all 0.3s ease;
            color: var(--text-mid);
            font-weight: 700;
            font-size: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,.06);
        }

        .po-row:hover .expand-icon {
            background: var(--accent);
            color: white;
            transform: translateX(2px);
            box-shadow: 0 4px 12px rgba(74,144,217,.25);
        }

        .po-row.expanded .expand-icon {
            background: var(--accent);
            color: white;
            transform: rotate(90deg);
            box-shadow: 0 4px 12px rgba(74,144,217,.3);
        }

        .po-number {
            font-weight: 700;
            font-size: 16px;
            color: var(--navy);
            letter-spacing: 0.3px;
        }

        .vendor-name {
            font-size: 15px;
            color: var(--text-main);
            font-weight: 500;
            line-height: 1.4;
        }

        .item-count {
            display: none;
        }

        .item-count-badge {
            background: var(--navy);
            color: white;
            padding: 3px 10px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 13px;
            min-width: 32px;
            text-align: center;
        }

        /* ─── STATUS TIMELINE ─── */
        .status-timeline {
            display: flex;
            align-items: stretch;
            gap: 0;
            position: relative;
            padding: 16px 0;
        }

        .status-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            flex: 1;
            position: relative;
            padding: 0 16px;
        }

        .status-step::before {
            content: '';
            position: absolute;
            top: 31px;
            left: 50%;
            width: 100%;
            height: 5px;
            background: var(--border);
            z-index: 0;
        }

        .status-step:first-child::before {
            left: 50%;
            width: 50%;
        }

        .status-step:last-child::before {
            width: 50%;
        }

        .status-step.active::before {
            background: linear-gradient(90deg, var(--status-received) 0%, var(--status-received) 100%);
        }

        .status-icon {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            background: var(--white);
            border: 4px solid var(--border);
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,.08);
        }

        .status-step.active .status-icon {
            background: linear-gradient(135deg, var(--status-received) 0%, #059669 100%);
            border-color: var(--status-received);
            color: white;
            box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.15), 0 8px 24px rgba(16, 185, 129, 0.3);
            transform: scale(1.05);
        }

        .status-step.current .status-icon {
            background: linear-gradient(135deg, var(--status-shipping) 0%, #d97706 100%);
            border-color: var(--status-shipping);
            color: white;
            animation: pulseGlow 2s infinite;
            box-shadow: 0 0 0 6px rgba(245, 158, 11, 0.15), 0 8px 24px rgba(245, 158, 11, 0.3);
            transform: scale(1.05);
        }

        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4), 0 8px 24px rgba(245, 158, 11, 0.3);
            }
            50% {
                box-shadow: 0 0 0 12px rgba(245, 158, 11, 0), 0 8px 24px rgba(245, 158, 11, 0.3);
            }
        }

        .status-label {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-soft);
            text-align: center;
            white-space: nowrap;
            margin-top: 4px;
        }

        .status-step.active .status-label,
        .status-step.current .status-label {
            color: var(--text-main);
            font-size: 16px;
        }

        .status-date {
            font-size: 13px;
            color: var(--text-soft);
            text-align: center;
            font-weight: 500;
        }

        .status-step.active .status-date {
            color: var(--status-received);
            font-weight: 700;
        }

        .status-step.current .status-date {
            color: var(--status-shipping);
            font-weight: 700;
        }

        /* ─── EXPANDABLE DETAILS ─── */
        .po-details {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
        }

        .po-row.expanded .po-details {
            max-height: 2000px;
        }

        .details-content {
            padding: 0 40px 32px 130px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .details-header {
            font-size: 13px;
            font-weight: 700;
            color: var(--navy);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--border);
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-top: 12px;
        }

        .items-table thead {
            background: var(--off-white);
        }

        .items-table th {
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            color: var(--navy);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border);
        }

        .items-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
        }

        .items-table tbody tr:hover {
            background: var(--row-hover);
        }

        .invoice-badge {
            display: inline-block;
            padding: 4px 10px;
            background: var(--accent-soft);
            color: var(--accent);
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
        }
        
        .status-complete {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        
        .status-shipping {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        /* ─── NO DATA ─── */
        .no-data {
            text-align: center;
            padding: 80px 20px;
            color: var(--text-soft);
            font-size: 16px;
            font-weight: 500;
        }

        #noResultInner {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            padding: 60px 20px;
            text-align: center;
            color: var(--text-soft);
            font-size: 16px;
            margin: 24px;
            display: none;
        }

        /* ─── PAGINATION ─── */
        .pagination-container {
            padding: 28px 40px;
            border-top: 2px solid var(--border);
            background: linear-gradient(to top, var(--off-white) 0%, var(--white) 100%);
        }

        .pagination-info {
            text-align: center;
            color: var(--text-mid);
            font-size: 14px;
            margin-bottom: 16px;
            font-weight: 500;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .pagination button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 46px;
            height: 46px;
            padding: 0 14px;
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--white);
            color: var(--navy);
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .pagination button:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--accent) 0%, #3a7bc8 100%);
            color: var(--white);
            border-color: var(--accent);
            box-shadow: 0 4px 12px rgba(74,144,217,.3);
            transform: translateY(-2px);
        }

        .pagination button.active {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
            color: var(--white);
            border-color: var(--navy);
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(27,45,79,.25);
            cursor: default;
        }

        .pagination button:disabled {
            background: var(--off-white);
            color: var(--text-soft);
            border-color: var(--border);
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination .dots {
            color: var(--text-soft);
            padding: 0 8px;
            font-weight: 700;
        }

        /* ─── RESPONSIVE ─── */
        
        /* 1280x720 - Perfect View */
        @media (min-width: 1200px) and (max-width: 1366px) {
            .po-header {
                grid-template-columns: 50px 150px 220px 480px 60px;
                padding: 22px 32px;
            }
            
            .status-icon {
                width: 60px;
                height: 60px;
                font-size: 26px;
            }
            
            .status-step::before {
                top: 30px;
            }
            
            .status-label {
                font-size: 14px;
            }
            
            .status-date {
                font-size: 12px;
            }
        }
        
        @media (max-width: 1199px) {
            .po-header {
                grid-template-columns: 45px 140px 200px 420px 60px;
                padding: 20px 28px;
            }
            
            .status-icon {
                width: 56px;
                height: 56px;
                font-size: 24px;
            }
            
            .status-step::before {
                top: 28px;
            }
            
            .status-label {
                font-size: 13px;
            }
            
            .status-date {
                font-size: 11px;
            }
        }

        @media (max-width: 1024px) {
            .po-header {
                grid-template-columns: 40px 130px 180px 380px 60px;
                padding: 18px 24px;
            }
            
            .status-icon {
                width: 52px;
                height: 52px;
                font-size: 22px;
            }
            
            .status-step::before {
                top: 26px;
                height: 4px;
            }
            
            .status-label {
                font-size: 12px;
            }
            
            .status-date {
                font-size: 10px;
            }
            
            .status-step {
                padding: 0 12px;
            }
        }

        @media (max-width: 768px) {
            .header { 
                padding: 20px 24px; 
            }
            
            .header h2 { 
                font-size: 18px; 
            }
            
            .filter-container { 
                padding: 20px 24px; 
            }
            
            .filter-row { 
                flex-direction: column; 
                align-items: stretch; 
            }
            
            .filter-buttons { 
                width: 100%; 
            }
            
            .filter-buttons .btn {
                flex: 1;
            }

            .info-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                padding: 12px 24px;
            }

            .po-header {
                grid-template-columns: 40px 1fr 60px;
                gap: 12px;
                padding: 16px 20px;
            }

            .vendor-name {
                display: none;
            }

            .status-timeline {
                grid-column: 1 / -1;
                margin-top: 12px;
                padding: 12px 0;
            }

            .status-label {
                font-size: 10px;
            }

            .status-date {
                font-size: 9px;
            }
            
            .status-icon {
                width: 48px;
                height: 48px;
                font-size: 20px;
                border-width: 3px;
            }
            
            .status-step::before {
                top: 24px;
                height: 4px;
            }
            
            .status-step {
                gap: 6px;
                padding: 0 8px;
            }

            .details-content {
                padding: 0 20px 16px 60px;
            }

            .pagination-container {
                padding: 20px 24px;
            }

            .pagination button {
                min-width: 40px;
                height: 40px;
                font-size: 13px;
                padding: 0 10px;
            }
        }
    </style>
</head>
<body>

    <div class="page-wrap">
        <!-- HEADER -->
        <div class="header">
            <h2>ระบบติดตามคำสั่งซื้อภายนอก</h2>
            <div class="buttons">
                <a href="http://server_update:8000/solist" class="btn btn-danger">หน้าหลัก</a>
            </div>
        </div>

        <!-- CARD: filter + list -->
        <div class="card">

            <!-- FILTER -->
            <div class="filter-container">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>ค้นหา PO หรือ ชื่อร้านค้า</label>
                        <div class="search-input-wrapper">
                            <input 
                                type="text" 
                                id="searchInput" 
                                placeholder="ค้นหาเลข PO หรือ ชื่อร้านค้า" 
                                oninput="liveFilter()"
                            />
                        </div>
                    </div>

                    <div class="filter-buttons">
                        <button class="btn btn-warning" onclick="resetFilters()">
                            ↺ Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- INFO BAR -->
            <div class="info-bar">
                <div class="info-bar-left">
                    ทั้งหมด <span class="count-badge" id="totalCount">0</span> รายการ PO
                </div>
            </div>

            <!-- PO LIST -->
            <div class="po-list" id="poList">
                @if($poData->count() > 0)
                    @php
                        // Group by PO number
                        $groupedPOs = $poData->groupBy('ponum')->map(function($items) {
                            $firstItem = $items->first();
                            $allComplete = $items->every(fn($item) => $item->status === 'Y');
                            $hasInvoice = $items->whereNotNull('invice')->whereNotNull('date_invice')->count() > 0;
                            
                            // Get latest invoice date
                            $latestInvoiceDate = $items->whereNotNull('date_invice')
                                ->sortByDesc('date_invice')
                                ->first()
                                ?->date_invice;
                            
                            $expectedDate = null;
                            if ($latestInvoiceDate) {
                                $expectedDate = \Carbon\Carbon::parse($latestInvoiceDate)->addDays(15);
                            }
                            
                            return [
                                'ponum' => $firstItem->ponum,
                                'vendor_name' => $firstItem->name_vendor ?? '-',
                                'items' => $items,
                                'item_count' => $items->count(),
                                'all_complete' => $allComplete,
                                'has_invoice' => $hasInvoice,
                                'invoice_date' => $latestInvoiceDate,
                                'expected_date' => $expectedDate,
                            ];
                        })->sortByDesc(function($po) {
                            return $po['invoice_date'] ?? '1900-01-01';
                        });
                    @endphp

                    @foreach($groupedPOs as $po)
                    <div class="po-row" 
                         data-po="{{ $po['ponum'] }}" 
                         data-vendor="{{ $po['vendor_name'] }}">
                        
                        <div class="po-header" onclick="toggleRow(this)">
                            <div class="expand-icon">›</div>
                            
                            <div class="po-number">{{ $po['ponum'] }}</div>
                            
                            <div class="vendor-name">{{ $po['vendor_name'] }}</div>
                            
                            <div class="status-timeline">
                                <!-- Step 1: Invoice Received -->
                                <div class="status-step {{ $po['has_invoice'] ? 'active' : '' }}">
                                    <div class="status-icon">📄</div>
                                    <div class="status-label">ได้รับ Invoice</div>
                                    @if($po['invoice_date'])
                                        <div class="status-date">
                                            {{ \Carbon\Carbon::parse($po['invoice_date'])->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Step 2: Shipping -->
                                <div class="status-step {{ $po['has_invoice'] && !$po['all_complete'] ? 'current' : ($po['all_complete'] ? 'active' : '') }}">
                                    <div class="status-icon">📦</div>
                                    <div class="status-label">กำลังจัดส่ง</div>
                                    @if($po['expected_date'] && !$po['all_complete'])
                                        <div class="status-date">
                                            คาดว่า {{ $po['expected_date']->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Step 3: Completed -->
                                <div class="status-step {{ $po['all_complete'] ? 'active' : '' }}">
                                    <div class="status-icon">✓</div>
                                    <div class="status-label">ได้รับสินค้า</div>
                                </div>
                            </div>
                            
                            <div class="item-count">
                                <span>รายการ</span>
                                <span class="item-count-badge">{{ $po['item_count'] }}</span>
                            </div>
                            
                            <div></div>
                        </div>

                        <div class="po-details">
                            <div class="details-content">
                                <div class="details-header">รายละเอียดสินค้า ({{ $po['item_count'] }} รายการ)</div>
                                
                                <table class="items-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th style="width: 130px;">เลข Invoice</th>
                                            <th style="width: 110px;">วันที่ Invoice</th>
                                            <th>ชื่อสินค้า</th>
                                            <th style="width: 80px; text-align: center;">จำนวน</th>
                                            <th style="width: 130px; text-align: center;">สถานะ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($po['items'] as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($item->invice)
                                                    <span class="invoice-badge">{{ $item->invice }}</span>
                                                @else
                                                    <span style="color: var(--text-soft);">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->date_invice)
                                                    {{ \Carbon\Carbon::parse($item->date_invice)->format('d/m/Y') }}
                                                @else
                                                    <span style="color: var(--text-soft);">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->name ?? '-' }}</td>
                                            <td style="text-align: center; font-weight: 600;">{{ $item->quantity ?? '-' }}</td>
                                            <td style="text-align: center;">
                                                @if($item->status === 'Y')
                                                    <span class="status-badge status-complete">✓ รับของสำเร็จ</span>
                                                @else
                                                    <span class="status-badge status-shipping">📦 กำลังจัดส่ง</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="no-data">
                    <p>ไม่พบข้อมูล</p>
                </div>
                @endif
            </div>

            <!-- PAGINATION -->
            <div class="pagination-container">
                <div class="pagination-info" id="paginationInfo"></div>
                <div class="pagination" id="pagination"></div>
            </div>

        </div><!-- .card -->

    </div><!-- .page-wrap -->

    <!-- NO RESULT -->
    <div id="noResultInner">
        <p>ไม่พบข้อมูลที่ตรงกับการค้นหา</p>
    </div>

    <script>
        const ROWS_PER_PAGE = 20;
        let currentPage = 1;
        let filteredRows = [];
        let allRows = [];

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            allRows = Array.from(document.querySelectorAll('.po-row'));
            filteredRows = [...allRows];
            
            // Update total count
            document.getElementById('totalCount').textContent = allRows.length;
            
            renderPagination();
            showPage(1);
        });

        // Toggle row expansion
        function toggleRow(header) {
            const row = header.closest('.po-row');
            row.classList.toggle('expanded');
        }

        // Show specific page
        function showPage(page) {
            currentPage = page;
            const start = (page - 1) * ROWS_PER_PAGE;
            const end = start + ROWS_PER_PAGE;

            // Hide all rows first
            allRows.forEach(row => row.style.display = 'none');

            // Show only rows for current page
            filteredRows.slice(start, end).forEach(row => {
                row.style.display = '';
            });

            // Update pagination info
            updatePaginationInfo();
            
            // Update pagination buttons
            renderPagination();

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Render pagination buttons
        function renderPagination() {
            const totalPages = Math.ceil(filteredRows.length / ROWS_PER_PAGE);
            const paginationEl = document.getElementById('pagination');
            paginationEl.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            // Previous button
            const prevBtn = document.createElement('button');
            prevBtn.textContent = '‹';
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => showPage(currentPage - 1);
            paginationEl.appendChild(prevBtn);

            // Page buttons
            const maxButtons = 7;
            let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);

            if (endPage - startPage < maxButtons - 1) {
                startPage = Math.max(1, endPage - maxButtons + 1);
            }

            // First page
            if (startPage > 1) {
                const btn = document.createElement('button');
                btn.textContent = '1';
                btn.onclick = () => showPage(1);
                paginationEl.appendChild(btn);

                if (startPage > 2) {
                    const dots = document.createElement('span');
                    dots.className = 'dots';
                    dots.textContent = '...';
                    paginationEl.appendChild(dots);
                }
            }

            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                const btn = document.createElement('button');
                btn.textContent = i;
                if (i === currentPage) {
                    btn.className = 'active';
                }
                btn.onclick = () => showPage(i);
                paginationEl.appendChild(btn);
            }

            // Last page
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const dots = document.createElement('span');
                    dots.className = 'dots';
                    dots.textContent = '...';
                    paginationEl.appendChild(dots);
                }

                const btn = document.createElement('button');
                btn.textContent = totalPages;
                btn.onclick = () => showPage(totalPages);
                paginationEl.appendChild(btn);
            }

            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.textContent = '›';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => showPage(currentPage + 1);
            paginationEl.appendChild(nextBtn);
        }

        // Update pagination info
        function updatePaginationInfo() {
            const start = (currentPage - 1) * ROWS_PER_PAGE + 1;
            const end = Math.min(currentPage * ROWS_PER_PAGE, filteredRows.length);
            const total = filteredRows.length;

            const infoEl = document.getElementById('paginationInfo');
            infoEl.textContent = `แสดง ${start} ถึง ${end} จากทั้งหมด ${total} รายการ PO`;
        }

        // Live filter
        function liveFilter() {
            const searchValue = document.getElementById('searchInput').value.trim().toLowerCase();
            
            if (!searchValue) {
                filteredRows = [...allRows];
            } else {
                filteredRows = allRows.filter(row => {
                    const po = row.getAttribute('data-po').toLowerCase();
                    const vendor = row.getAttribute('data-vendor').toLowerCase();
                    return po.includes(searchValue) || vendor.includes(searchValue);
                });
            }

            // Show no result message if needed
            const noResultDiv = document.getElementById('noResultInner');
            if (searchValue && filteredRows.length === 0) {
                noResultDiv.style.display = 'block';
                document.querySelector('.pagination-container').style.display = 'none';
            } else {
                noResultDiv.style.display = 'none';
                document.querySelector('.pagination-container').style.display = 'block';
            }

            // Reset to page 1 and show results
            currentPage = 1;
            showPage(1);
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            filteredRows = [...allRows];
            document.getElementById('noResultInner').style.display = 'none';
            document.querySelector('.pagination-container').style.display = 'block';
            currentPage = 1;
            showPage(1);
        }
    </script>

</body>
</html>