{{-- resources/views/sale/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลจัดส่ง</title>
    
    {{-- bust cache --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.blade.css') }}?v={{ time() }}">

    {{-- =========================================================
         FLUID TYPOGRAPHY LAYER — ตัวอักษรไหลตามจอ (ครอบทั้งหน้า)
         สูตร: clamp( FLOOR , (vw * X) + Y px , CEILING )
         ✅ ชนะ CSS ไฟล์ + ชนะ inline style เดิม (เพราะ !important + มาทีหลัง)
         ปรับ "ความไหล" ได้ที่ค่า vw (มาก=ไหลแรง / น้อย=ไหลนุ่ม)
         ========================================================= --}}
    <style>
        /* --- พื้นฐานทั้งหน้า ไหลตามจอ --- */
        html, body {
            font-size: clamp(12px, 0.45vw + 7px, 16px) !important;
        }

        /* --- หัวหน้า --- */
        .header h2 {
            font-size: clamp(15px, 1vw + 6px, 22px) !important;
        }
        .buttons span {
            font-size: clamp(11px, 0.55vw + 5px, 14px) !important;
        }

        /* --- ปุ่มทั้งหมด ไหลตามจอ + โค้ง 6px --- */
        .btn, .btn-back, .btn-outline, .btn-alert, .btn-clear {
            border-radius: 6px !important;
            font-size: clamp(11px, 0.55vw + 5px, 14px) !important;
        }
        .btn-alert {
            background:#c0392b; color:#fff; padding:6px 18px; text-decoration:none;
            font-weight:500; border:1px solid #c0392b;
            border-radius:6px !important; display:inline-block;
        }
        .btn-alert:hover { background:#a93226; border-color:#a93226; color:#fff; text-decoration:none; }

        .btn-clear {
            flex: 0 0 auto; align-self: center;
            padding: 6px 16px; border-radius: 6px !important;
            background: #ffffff; color: #6b7280; border: 1px solid #dcdcdc;
            font-weight: 500; text-decoration: none; white-space: nowrap; cursor: pointer;
            display: inline-flex; align-items: center; gap: 5px;
            transition: background-color .2s ease, border-color .2s ease, color .2s ease;
        }
        .btn-clear:hover { background: #f3f4f6; border-color: #9ca3af; color: #374151; text-decoration: none; }

        /* --- แถบกรอง: label / input / select ไหลตามจอ --- */
        .filter-form label {
            font-size: clamp(11px, 0.55vw + 5px, 14px) !important;
        }
        .filter-form input[type="date"],
        .filter-form input[type="text"],
        .filter-form select,
        .search-box input {
            font-size: clamp(11px, 0.55vw + 5px, 14px) !important;
        }

        /* --- ตาราง: ตัวปกติไหลตามจอ (ช่วงกว้าง → เห็นผลชัด) --- */
        table {
            font-size: clamp(10px, 0.62vw + 4px, 14px) !important;
        }
        th, td {
            font-size: clamp(10px, 0.62vw + 4px, 14px) !important;
        }
        th {
            font-size: clamp(10px, 0.62vw + 4px, 14px) !important;
        }
        /* ลิงก์ในตาราง inherit ขนาดจาก td → ไหลตามไปด้วย */
        table a, td a, .table-container a {
            font-size: inherit !important;
        }
        .wrap-text {
            font-size: inherit !important;
        }

        /* --- ประเภทงาน: ไหลตามจอ (เดิมแข็ง 10px) + แคบ + สีเต็มช่อง --- */
        th.col-type, td.col-type {
            width: 96px !important;
            max-width: 96px !important;
            min-width: 96px !important;
            white-space: normal !important;
            word-break: break-word;
            padding: 8px 6px !important;
            font-size: clamp(9px, 0.5vw + 3px, 12px) !important;   /* ✅ ไหล */
            line-height: 1.3 !important;
        }
        th.col-status, td.col-status {
            font-size: clamp(9px, 0.5vw + 3px, 12px) !important;   /* ✅ ไหล */
            line-height: 1.35 !important;
            white-space: nowrap !important;
            padding: 8px 8px !important;
        }
        td.col-status .status-done   { color: #16a34a !important; font-weight: 600 !important; }
        td.col-status .status-doing  { color: #2563eb !important; font-weight: 600 !important; }
        td.col-status .status-cancel { color: #dc2626 !important; font-weight: 600 !important; }
        td.col-status .status-noprint { color: #6b7280 !important; font-weight: 600 !important; }

        /* --- อ้างอิงใบส่งของ: กดได้ = เขียว (ขนาด inherit จาก td) --- */
        td.col-billid a { color: #16a34a !important; }
        td.col-billid a:hover { color: #15803d !important; text-decoration: underline; }

        /* --- ป้ายบอกว่ากำลังค้นหาข้ามวัน --- */
        .search-note {
            display: inline-flex; align-items: center; gap: 6px;
            margin-left: 10px; padding: 4px 12px; border-radius: 6px;
            background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;
            font-size: clamp(11px, 0.55vw + 5px, 14px);
            white-space: nowrap;
        }
    </style>

</head>
<body>

    <div class="header">
        <h2>ข้อมูลจัดส่ง</h2>

        <div class="buttons">
       <span> ผู้ใช้: {{ request()->get('create_by', 'Guest') }}</span>
            @csrf

            <a href="http://server_update:8000/solist" button  type="submit" class="btn btn-danger">🚪 หน้าหลัก</a>
            <a href="alertbill" class="btn-alert">งานค้าง</a>
        </div>
    </div>

    <div class="filter-container">
<form method="GET" action="{{ route('sale.dashboard') }}" class="filter-form" id="autoSearchForm">

    <label for="date">📅 วันที่: เดือน / วัน / ปี</label>

    <input type="date" id="date" name="date"
        value="{{ request('date') }}">

    <label for="emp_name" style="margin-left: 15px;">ผู้บันทึก:</label>
    <select name="emp_name" id="emp_name"
            onchange="document.getElementById('autoSearchForm').submit();">
        <option value="">-- ทั้งหมด --</option>
        @foreach($empList as $emp)
            <option value="{{ $emp }}" {{ request('emp_name') == $emp ? 'selected' : '' }}>
                {{ $emp }}
            </option>
        @endforeach
    </select>

    <input type="hidden" name="create_by" value="{{ request('create_by') }}">

    {{-- ✅ ไม่ส่ง keyword / so_keyword ต่อ: เปลี่ยนวันที่ = เริ่มกรองวันใหม่ ไม่ติดคำค้นเดิม --}}

    <button type="submit" style="display: none;">ค้นหา</button>
</form>

    <div class="filter-container">

    <script>
const form = document.getElementById('autoSearchForm');
const dateInput = document.getElementById('date');

dateInput.addEventListener('change', () => {
    form.submit();
});

// ✅ auto-submit เฉพาะตอนเข้าหน้าเปล่า ๆ เท่านั้น
//    ถ้ามี keyword / so_keyword / date / emp_name อยู่แล้ว = ห้ามยิงทับ (เดิมมันยัดวันที่วันนี้ทับผลค้นหา)
window.addEventListener('load', () => {
    const p = new URLSearchParams(location.search);
    if (p.get('keyword') || p.get('so_keyword') || p.get('date') || p.get('emp_name')) return;

    if (!sessionStorage.getItem('hasAutoSubmitted')) {
        sessionStorage.setItem('hasAutoSubmitted', 'true');
        if (!dateInput.value) {
            dateInput.value = new Date().toISOString().slice(0, 10);
        }
        form.submit();
    }
});
    </script>
    </div>

{{-- ✅ ค้นหา อ้างอิงใบสั่งขาย: ไม่ส่ง date / emp_name → ค้นได้ทุกวัน ไม่ต้องกดล้างก่อน --}}
<form method="GET" action="{{ route('sale.dashboard') }}" class="search-box">
    <input type="text"
        name="so_keyword"
        placeholder="ค้นหา อ้างอิงใบสั่งขาย"
        value="{{ request('so_keyword') }}">

    <input type="hidden" name="create_by" value="{{ request('create_by') }}">
</form>

{{-- ✅ ค้นหา เลขที่บิล: ไม่ส่ง date / emp_name → ค้นได้ทุกวัน ไม่ต้องกดล้างก่อน --}}
<form method="GET" action="{{ route('sale.dashboard') }}" class="search-box">

    <input type="text"
        name="keyword"
        placeholder="ค้นหา เลขที่บิล"
        value="{{ request('keyword') }}">

    <input type="hidden" name="create_by" value="{{ request('create_by') }}">
</form>

{{-- ปุ่มล้างทั้งหมด: ล้างทุกช่อง + วันที่กลับเป็นวันนี้ --}}
<a href="{{ route('sale.dashboard', ['date' => now()->format('Y-m-d'), 'create_by' => request('create_by')]) }}"
   class="btn-clear"
   title="ล้างตัวกรองทั้งหมด (วันที่กลับเป็นวันนี้)">🗑 ล้างทั้งหมด</a>


    </div>

    {{-- map สีประเภทงาน --}}
    @php
        $formTypeMap = [
            'บิล/PO3'                       => 'bg-red',
            'บิล/PO3/วางบิล'                => 'bg-green',
            'บิล/PO3/วางบิล/สำเนาหน้าบิล2' => 'bg-blue',
            'บิล/PO3/สำเนาหน้าบิล2'         => 'bg-purple',
            'บิล/PO3/บัญชี'                 => 'bg-yellow',
        ];
    @endphp

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>อ้างอิงใบส่งของ</th>
                    <th>อ้างอิงใบสั่งขาย</th>
                    <th>อ้างอิงใบสั่งซื้อ</th>
                    {{-- ✅ ลบ REF ออกจากตารางหลัก --}}
                    <th>ชื่อลูกค้า</th>
                    <th>วันที่จัดส่ง</th>
                    <th>ผู้บันทึก</th>
                    <th>ประเภทบิล</th>
                    <th>บันทึกลงระบบ</th>
                    <th class="col-type">ประเภทงาน</th>
                    <th class="col-status">สถานะ</th>
                    <th>ข้อมูลสินค้า</th>
                </tr>
            </thead>
            <tbody id="table-body">

                @foreach($bill as $item)
                <tr>
                    <td>{{ ($bill->currentPage() - 1) * $bill->perPage() + $loop->iteration }}</td>
@php
    $pdfPath = "doc_document/{$item->billid}.pdf";
    $billPath = "bill_document/{$item->billid}.pdf";
    $hasPdf  = \Illuminate\Support\Facades\Storage::disk('public')->exists($pdfPath);
    $isNoMerge = ($item->customer_id === 'CUS-26039');
@endphp

<td class="col-billid" @if($item->statusdeli == 1) style="background-color: #a5d6a7;" @endif>
    @if($hasPdf)
        <span style="white-space: nowrap;">
            @if($isNoMerge)
                <a href="{{ asset('storage/doc_document/' . $item->billid . '.pdf') }}"
                   target="_blank">
                    {{ $item->billid }}
                </a>
            @else
                <a href="javascript:void(0);"
                   onclick="mergeAndOpenPdfs('{{ $item->billid }}')">
                    {{ $item->billid }}
                </a>
            @endif
        </span>
    @elseif($item->statusdeli == 1)
        <a href="https://drive.google.com/drive/u/0/search?q={{ $item->billid }}+parent:1WyDB1b01cDQ53Ap7B03UIGFbL6a2Y6WB"
           target="_blank">
            {{ $item->billid }}
        </a>
    @else
        {{ $item->billid }}
    @endif
</td>
                    <td>
                    <a href="http://server_update:8000/sodetail?SONum={{ urlencode($item->so_id) }}"
                        target="_blank" rel="noopener"
                        class="text-blue-600 hover:underline">
                        {{ $item->so_id }}
                    </a>
                    </td>

                    <td>{{ $item->ponum }}</td>
                    
                    {{-- ✅ ลบ REF ออกจากตารางหลัก (แต่ยังส่งเข้า Popup อยู่) --}}
                    
                    <td class="wrap-text" style="text-align: left; white-space: normal; word-wrap: break-word;">
                        {{ $item->customer_name }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') }}</td>
                    <td>{{ $item->emp_name }}</td>
                    <td>{{ $item->billtype }}
                        @if($hasPdf)
                            @if($isNoMerge)
                                <a href="{{ asset('storage/bill_document/' . $item->billid . '.pdf') }}"
                                target="_blank"
                                style="color:#0ea5e9; font-weight:bold; cursor:pointer; margin-left:3px;"
                                title="เปิดไฟล์ใบเสร็จ">+</a>
                            @else
                                <a href="javascript:void(0);"
                                style="color:#0ea5e9; font-weight:bold; cursor:pointer; margin-left:3px;"
                                onclick="openBillOnly('{{ $item->billid }}')"
                                title="เปิดไฟล์ใบเสร็จ">+</a>
                            @endif
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->time)->format('H:i d/m/Y ') }}</td>

                    {{-- ประเภทงาน: col-type + สีเต็มช่อง --}}
                    <td class="col-type {{ $formTypeMap[$item->formtype] ?? '' }}">
                        {{ $item->formtype }}
                    </td>

                    {{-- สถานะ: wrap คำด้วย span สี --}}
                    <td class="col-status">
                    @if($item->statuspdf == 0)
                        <span class="status-doing">กำลังดำเนินการ</span>
                    @elseif($item->statuspdf == 6)
                        <span class="status-cancel">ยกเลิก</span>
                    @elseif($item->statuspdf == 3)
                        <span class="status-noprint">ไม่ปริ้นบิล</span>
                    @else
                        <span class="status-done" style="cursor:pointer;text-decoration:underline;@if(!empty($item->has_delivery)) background:#FFE97A;color:#5a4b00;padding:2px 8px;border-radius:6px;@endif"
                              title="{{ !empty($item->has_delivery) ? 'มีการจัดส่งแล้ว — ดูข้อมูล' : 'ดูข้อมูลจัดส่ง / รับเข้า' }}"
                              onclick="openDeliveryPopup({{ json_encode((string) $item->billid) }})">ปริ้นสำเร็จ</span>
                    @endif
                    <br>
                    {{ $item->print_time ? \Carbon\Carbon::parse($item->print_time)->format('H:i d/m/Y') : '' }}
                    </td>
                   <td>
                    <a href="javascript:void(0);"
                    onclick="openPopup(
                        {{ json_encode($item->so_detail_id) }},
                        {{ json_encode($item->so_id) }},
                        {{ json_encode($item->ponum) }},
                        {{ json_encode($item->customer_name) }},
                        {{ json_encode($item->customer_address) }},
                        {{ json_encode(\Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y')) }},
                        {{ json_encode($item->sale_name) }},
                        {{ json_encode($item->POdocument) }},
                        {{ json_encode($item->notes) }}
                    )">
                        เพิ่มเติม
                    </a>
                </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(isset($message))
        <br>

        <p style="text-align: center">{{ $message }}</p>
             @endif
    </div>

    <!-- Popup ข้อมูลจัดส่ง / รับเข้า (กดจากสถานะ "ปริ้นสำเร็จ") -->
    <div class="popup-overlay" id="deliveryPopup" style="display: none;">
        <div class="popup-content" style="max-width:640px;">
            <span class="close-btn" onclick="closeDeliveryPopup()">&times;</span>
            <h3 style="margin:0 0 12px;">ข้อมูลจัดส่ง / รับเข้า — บิล <span id="dpBill"></span></h3>
            <div id="dpBody"></div>
        </div>
    </div>
    <script>
    const BILL_DELIVERY_URL = "{{ route('sale.billDelivery') }}";
    function dpEsc(s){ return (s==null?'':String(s)).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
    /* ===== ไทม์ไลน์รายละเอียดการจัดส่ง (โค้ดชุดเดียวกันในหน้า sale/dashboard, document/dashboarddoc, so/show) =====
       rows = transaction_transport ทุกรอบของบิล (รวมรอบประวัติ cancelled_at) เรียงเก่า -> ใหม่
       แต่ละรอบบอก: ใครจ่ายงาน -> ใครไปส่ง/รถอะไร/วันไหน -> ผลเป็นอย่างไร ใครยืนยัน -> จบรอบเพราะอะไร (ส่งใหม่/เปลี่ยนคนขับ/ยกเลิก) */
    function dlvEsc(x){ return (x==null?'':String(x)).replace(/[&<>"']/g,function(c){return({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]);}); }
    // สี/ข้อความตามผล (ชุดเดียวกับหน้า billreceive): สำเร็จ=เขียว, สินค้าผิด=แดง, ส่งใหม่=ฟ้า, ค้างบิล=ส้ม
    function dlvStyle(st){
        st = (st||'').toString().trim();
        if(st.indexOf('สำเร็จ')!==-1 && st.indexOf('ไม่')===-1) return {bg:'#e8f5e9',fg:'#1b5e20',border:'#2e7d32',txt:'สำเร็จ'};
        if(st.indexOf('สินค้าผิด')!==-1) return {bg:'#ffebee',fg:'#a91f1f',border:'#c62828',txt:'สินค้าผิด'};
        if(st.indexOf('ไม่สำเร็จ')!==-1) return {bg:'#ffebee',fg:'#a91f1f',border:'#c62828',txt:'ไม่สำเร็จ'};
        if(st.indexOf('ส่งใหม่')!==-1) return {bg:'#eaf0fc',fg:'#2853d5',border:'#2853d5',txt:'ส่งใหม่'};
        if(st.indexOf('ค้างบิล')!==-1) return {bg:'#fff4e5',fg:'#b45309',border:'#ed6c02',txt:'ค้างบิล'};
        if(st===''||st==='0') return {bg:'#f3f4f6',fg:'#6b7280',border:'#d1d5db',txt:'กำลังไปส่ง'};
        return {bg:'#f3f4f6',fg:'#374151',border:'#d1d5db',txt:st};
    }
    function dlvBadge(txt, s){ return '<span style="background:'+s.bg+';color:'+s.fg+';font-size:12px;font-weight:700;padding:2px 10px;border-radius:10px;white-space:nowrap;">'+dlvEsc(txt)+'</span>'; }
    // note อัตโนมัติตอนกดส่งใหม่ ("ส่งใหม่ (ไม่สำเร็จ) เหตุผล: .. · เคยไปวันที่ .. · จ่ายใหม่ให้ ..") -> ดึงเหตุผล/ผู้รับงานใหม่ออกมา
    //   ส่วนอื่นซ้ำกับข้อมูลที่แสดงอยู่แล้วจึงไม่แสดงซ้ำ ; note ที่คนพิมพ์เอง (ค้างบิล/สินค้าผิด/ของผิด) แสดงตามจริง
    function dlvParseNote(note){
        note = (note||'').toString().trim();
        if(!note) return {text:''};
        if(note.indexOf('ส่งใหม่ (ไม่สำเร็จ)') === 0){
            var m1 = note.match(/เหตุผล:\s*([^·]+)/), m2 = note.match(/จ่ายใหม่ให้\s*([^·]+?)(?:\s*·|$)/);
            return {auto:true, reason: m1 ? m1[1].trim() : '', reassign: m2 ? m2[1].trim() : '', text:''};
        }
        return {text: note};
    }
    function dlvLine(label, val){
        return '<div style="display:flex;gap:8px;align-items:baseline;">'
             + '<span style="flex:0 0 110px;color:#6b7280;white-space:nowrap;">'+label+'</span>'
             + '<span style="flex:1;min-width:0;">'+val+'</span></div>';
    }
    function dlvTimeline(rows){
        rows = rows || [];
        var total = rows.length, redoN = 0, active = [];
        rows.forEach(function(r){
            if((r.status||'').indexOf('ส่งใหม่') !== -1) redoN++;
            if(!r.cancelled_at) active.push(r);
        });
        var latest = active.length ? active[active.length-1] : null;

        // ── สรุปด้านบน ──
        var h = '<div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;padding:10px 14px;margin-bottom:12px;font-size:13.5px;color:#374151;line-height:1.8;">'
              + 'จ่ายงานไปส่งทั้งหมด <b>'+total+'</b> รอบ' + (redoN ? ' · สั่งส่งใหม่ <b>'+redoN+'</b> ครั้ง' : '') + '<br>'
              + 'สถานะตอนนี้: ';
        if(latest){
            var ls = dlvStyle(latest.status);
            h += dlvBadge(ls.txt, ls) + ' · คนขับ <b>'+dlvEsc(latest.driver_name||'-')+'</b>'
               + (latest.transport_name ? ' ('+dlvEsc(latest.transport_name)+')' : '')
               + (latest.delivery_date ? ' · ไปส่งวันที่ <b>'+dlvEsc(latest.delivery_date)+'</b>' : '');
        } else {
            h += '<b style="color:#9a3412;">รอจ่ายงานใหม่</b> (ทุกรอบถูกส่งใหม่/ยกเลิกแล้ว — รอจ่ายที่หน้าจ่ายงานขนส่ง)';
        }
        h += '</div>';

        // ── ทีละรอบ ──
        rows.forEach(function(r, i){
            var isHist = !!r.cancelled_at;
            var isRedo = (r.status||'').indexOf('ส่งใหม่') !== -1;
            var next   = rows[i+1];
            // รอบที่จบไปแล้ว: ส่งใหม่ / เปลี่ยนคนขับ (รอบถัดไปเป็น note "เปลี่ยน...") / ยกเลิกการจ่ายงาน (คืนคิว)
            var kind = !isHist ? '' : (isRedo ? 'redo' : ((next && /^เปลี่ยน/.test((next.note||'').trim())) ? 'change' : 'cancel'));
            var s    = dlvStyle(r.status);
            var pn   = dlvParseNote(r.note);
            var st   = (r.status||'').toString().trim();
            var hasResult = st !== '' && st !== '0' && !isRedo;   // มีผลจริง (สำเร็จ/ค้างบิล/สินค้าผิด)

            var head, border;
            if(kind === 'redo')        { head = dlvBadge('ไม่สำเร็จ / ส่งใหม่', dlvStyle('ส่งใหม่')); border = '#2853d5'; }
            else if(kind === 'change') { head = dlvBadge('เปลี่ยนคนขับ/ขนส่ง', dlvStyle('ส่งใหม่')); border = '#2853d5'; }
            else if(kind === 'cancel') { head = dlvBadge('ยกเลิกการจ่ายงาน', {bg:'#f3f4f6',fg:'#6b7280'}); border = '#9ca3af'; }
            else                       { head = dlvBadge(s.txt, s); border = s.border; }

            h += '<div style="border:2px '+(isHist?'dashed':'solid')+' '+border+';border-radius:10px;padding:10px 14px;background:#fff;">'
               + '<div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px;">'
               +   '<span style="font-weight:700;color:#111827;">รอบที่ '+(i+1)
               +   (isHist ? ' <span style="color:#9a3412;font-size:12px;font-weight:600;">(จบรอบแล้ว)</span>'
                           : (r === latest ? ' <span style="color:#1b5e20;font-size:12px;font-weight:600;">(รอบปัจจุบัน)</span>' : ''))
               +   '</span>' + head
               + '</div>'
               + '<div style="font-size:13.5px;color:#374151;line-height:1.9;">';

            // 1) จ่ายงาน
            h += dlvLine('จ่ายงานโดย', '<b>'+dlvEsc(r.name_pick||'-')+'</b>' + (r.time_pick ? ' · '+dlvEsc(r.time_pick) : ''));
            // 2) ใครไปส่ง รถอะไร วันไหน
            h += dlvLine('ผู้ไปส่ง', '<b>'+dlvEsc(r.driver_name||'-')+'</b>'
                 + (r.transport_name ? ' · '+dlvEsc(r.transport_name) : '')
                 + (r.delivery_date ? ' · ให้ไปส่งวันที่ <b>'+dlvEsc(r.delivery_date)+'</b>' : '')
                 + (r.id_transport ? ' · เลขขนส่ง <b>'+dlvEsc(r.id_transport)+'</b>' : ''));
            // 3) ผลการไปส่ง
            if(hasResult){
                h += dlvLine('ผลการส่ง', dlvBadge(s.txt, s)
                     + ' · ยืนยันโดย <b>'+dlvEsc(r.check_name||'-')+'</b>' + (r.check_time ? ' · '+dlvEsc(r.check_time) : ''));
            } else if(kind === 'redo'){
                h += dlvLine('ผลการส่ง', '<b style="color:#a91f1f;">ไม่สำเร็จ</b>');
            } else if(!isHist){
                h += dlvLine('ผลการส่ง', '<span style="color:#9ca3af;">ยังไม่ยืนยันผล (อยู่ระหว่างไปส่ง)</span>');
            }
            // 4) จบรอบเพราะอะไร ใครทำ เมื่อไหร่
            var by = '<b>'+dlvEsc(r.cancelled_by || r.check_name || '-')+'</b>' + (r.cancelled_at ? ' · '+dlvEsc(r.cancelled_at) : '');
            if(kind === 'redo'){
                h += dlvLine('สั่งส่งใหม่', by
                     + (pn.reason ? ' · เหตุผล <b style="color:#a91f1f;">'+dlvEsc(pn.reason)+'</b>' : '')
                     + (pn.reassign ? ' · จ่ายใหม่ให้ '+dlvEsc(pn.reassign) : ''));
            } else if(kind === 'change'){
                h += dlvLine('เปลี่ยนคนขับ', by + ' · ไปต่อที่รอบที่ '+(i+2));
            } else if(kind === 'cancel'){
                h += dlvLine('ยกเลิกการจ่าย', by + ' · คืนงานไปหน้าจ่ายงาน');
            }
            // 5) หมายเหตุที่คนพิมพ์ (ค้างบิล/สินค้าผิด/ของผิด/เปลี่ยนคนขับ ฯลฯ)
            if(pn.text){ h += dlvLine('หมายเหตุ', '<span style="color:#6b7280;">'+dlvEsc(pn.text)+'</span>'); }

            h += '</div></div>';
            if(i < total-1){ h += '<div style="text-align:center;color:#9ca3af;font-size:11px;line-height:1;padding:3px 0;color:#c7ccd3;">|</div>'; }
        });
        return h;
    }
    async function openDeliveryPopup(billid){
        const pop=document.getElementById('deliveryPopup');
        document.getElementById('dpBill').textContent=billid;
        document.getElementById('dpBody').innerHTML='<div style="padding:16px;text-align:center;color:#6b7280;">กำลังโหลด...</div>';
        pop.style.display='flex';
        try{
            const res=await fetch(BILL_DELIVERY_URL+'?billid='+encodeURIComponent(billid),{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
            const data=await res.json();
            const rows=(data&&data.rows)||[];
            if(!rows.length){ document.getElementById('dpBody').innerHTML='<div style="padding:16px;color:#6b7280;text-align:center;">ยังไม่มีข้อมูลการจ่ายงาน/จัดส่งของบิลนี้</div>'; return; }
            document.getElementById('dpBody').innerHTML=dlvTimeline(rows);
        }catch(e){ document.getElementById('dpBody').innerHTML='<div style="padding:16px;color:#dc2626;text-align:center;">โหลดข้อมูลไม่สำเร็จ</div>'; }
    }
    function closeDeliveryPopup(){ document.getElementById('deliveryPopup').style.display='none'; }
    document.getElementById('deliveryPopup').addEventListener('click',function(e){ if(e.target===this) closeDeliveryPopup(); });
    </script>

    <!-- Popup -->
    <div class="popup-overlay" id="popup" style="display: none;">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>REF</th>
                            <th>ชื่อลูกค้า</th>
                            <th>ที่อยู่จัดส่ง</th>
                            <th>วันที่จัดส่ง</th>
                            <th style="white-space: nowrap;">ผู้เปิด</th>
                            <th>เอกสารPO</th>
                        </tr>
                    </thead>
                    <tbody id="popup-body-1">
                    </tbody>
                </table>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>รหัสสินค้า</th>
                            <th>รายการ</th>
                            <th>จำนวน</th>
                            <th>ราคาต่อหน่วย</th>
                        </tr>
                    </thead>
                    <tbody id="popup-body">
                    </tbody>
                </table>
                <br>
               <textarea id="popup-body-3" readonly></textarea>
            </div>
        </div>
    </div>

{{-- ✅ ย้าย merge script ออกมานอก loop (เดิมถูกประกาศซ้ำทุกแถว) --}}
<script>
    function mergeAndOpenPdfs(billid) {
        const pdfDocUrl = "{{ asset('storage/doc_document') }}/" + billid + ".pdf";
        const win1 = window.open(pdfDocUrl, "_blank");
        fetch("{{ route('merge.pdf') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ billid: billid })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && win1) {
                setTimeout(() => {
                    win1.location.reload();
                }, 800);
            } else {
                alert("❌ " + (data.message || "เกิดข้อผิดพลาดในการ merge PDF"));
            }
        })
        return false;
    }

    function openBillOnly(billid) {
        const pdfBillUrl = "{{ asset('storage/bill_document') }}/" + billid + ".pdf";
        const win1 = window.open(pdfBillUrl, "_blank");

        fetch("{{ route('merge.pdf') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ billid: billid })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && win1) {
                setTimeout(() => {
                    win1.location.reload();
                }, 800);
            } else {
                alert("❌ " + (data.message || "เกิดข้อผิดพลาดในการ merge PDF"));
            }
        })
        return false;
    }
</script>

 <script>
        function openPopup(soDetailId,so_id,ponum,customer_name,customer_address,date_of_dali,sale_name,POdocument,notes) {
        document.getElementById("popup").style.display = "flex";

        let popupBody = document.getElementById("popup-body-1");
        popupBody.innerHTML = `
            <tr>
                <td>${soDetailId}</td>
                <td>${customer_name}</td>
                <td>${customer_address}</td>
                <td>${date_of_dali}</td>
                <td>${sale_name}</td>
               <td><a href="/storage/po_documents/${POdocument}" target="_blank">ดูไฟล์</a></td>
            </tr>
        `;
        document.getElementById("popup-body-3").value = notes;
        let secondPopupBody = document.getElementById("popup-body");
        secondPopupBody.innerHTML = "<tr><td colspan='4'>Loading...</td></tr>";

        fetch(`/get-bill-detail/${soDetailId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    secondPopupBody.innerHTML = "";
                    data.forEach(item => {
                        secondPopupBody.insertAdjacentHTML("beforeend", `
                            <tr>
                                <td>${item.item_id}</td>
                                <td>${item.item_name}</td>
                                <td>${item.quantity}</td>
                                <td>${item.unit_price}</td>
                            </tr>
                        `);
                    });
                } else {
                    secondPopupBody.innerHTML = "<tr><td colspan='4'>ไม่มีข้อมูล</td></tr>";
                }
            })
            .catch(error => {
                console.error("Error fetching data:", error);
                secondPopupBody.innerHTML = "<tr><td colspan='4'>เกิดข้อผิดพลาด</td></tr>";
            });
    }

function closePopup() {
    document.getElementById('popup').style.display = 'none';
}

window.onclick = function(event) {
    var popup = document.getElementById('popup');
    if (event.target === popup) {
        closePopup();
    }
}

    </script>

<script>
  function openPopup3E(soId) {
    const url = `http://server-3e/3e/store_report.php?so=${encodeURIComponent(soId)}&po=&search=Search&rowPerPage=25&currentPage=0`;

    const w = 1100, h = 500;
    const margin = 16;

    const dualScreenLeft = window.screenLeft ?? window.screenX ?? 0;
    const dualScreenTop  = window.screenTop  ?? window.screenY ?? 0;

    const width  = window.innerWidth  ?? document.documentElement.clientWidth  ?? screen.width;
    const height = window.innerHeight ?? document.documentElement.clientHeight ?? screen.height;

    const left = Math.max(dualScreenLeft + width  - w - margin, dualScreenLeft);
    const top  = Math.max(dualScreenTop  + height - h - margin, dualScreenTop);

    const features = [
      'toolbar=no',
      'location=no',
      'status=no',
      'menubar=no',
      'scrollbars=yes',
      'resizable=yes',
      `width=${w}`,
      `height=${h}`,
      `top=${top}`,
      `left=${left}`,
      'noopener=yes'
    ].join(',');

    const win = window.open(url, '_blank', features);

    if (!win) window.open(url, '_blank', 'noopener');

    return false;
  }
</script>
<script>
function mergePdf(billid) {
    fetch("{{ route('merge.pdf') }}", {
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ billid: billid })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert("❌ " + data.message);
        }
    })
    .catch(err => {
        alert("❌ เกิดข้อผิดพลาด: " + err.message);
        console.error(err);
    });
}
</script>
<style>
nav[role="navigation"] > div > p{
    display:none !important;
}
nav[role="navigation"] .sm\:hidden{
    display:none !important;
}
.pagination-wrap{
    display:flex;
    justify-content:center;
    margin:20px 0;
}
nav[role="navigation"] > div{
    display:flex !important;
    align-items:center !important;
    justify-content:center !important;
    gap:16px;
    flex-wrap:nowrap !important;
}
nav[role="navigation"] a[rel="prev"] span,
nav[role="navigation"] a[rel="next"] span{
    display:none !important;
}
nav[role="navigation"] a[rel="prev"],
nav[role="navigation"] a[rel="next"]{
    width:40px;
    height:40px;
    border-radius:50%;
    background:#e5e7eb;
    display:flex;
    align-items:center;
    justify-content:center;
}
nav[role="navigation"] svg{
    width:20px !important;
    height:20px !important;
    stroke-width:3;
}
nav[role="navigation"] a.relative,
nav[role="navigation"] span.relative{
    min-width:32px;
    height:32px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:16px;
    font-weight:600;
    color:#2563eb;
    background:transparent !important;
    border:none !important;
}
nav[role="navigation"] span[aria-current="page"]{
    font-weight:800;
    color:#1d4ed8;
}
</style>

<div class="pagination-wrap">
    {{ $bill->appends(request()->query())->links() }}
</div>

    </body>
    </html>