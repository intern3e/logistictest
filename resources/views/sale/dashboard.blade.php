<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลจัดส่ง</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.blade.css') }}">

</head>
<body>

    <div class="header">
        <h2>ข้อมูลจัดส่ง</h2>

        <div class="buttons">
       <span>👤 ผู้ใช้: {{ request()->get('create_by', 'Guest') }}</span>
            @csrf
            
            <a href="http://server_update:8000/solist" button  type="submit" class="btn btn-danger">🚪 หน้าหลัก</a>
            {{-- <a href="Sotest" style="background-color: #0077ff; color: white; padding: 6px 8px; border-radius: 5px; text-decoration: none;">ตารางเดินรถ</a> --}}
            <a href="alertbill" style="background:#c0392b; color:#fff; padding:7px 14px; text-decoration:none; font-weight:600; font-size:13px; border:1px solid #c0392b; border-radius:6px;">งานค้าง</a>
        </div>
    </div>
    
    <div class="filter-container">
<form method="GET" action="{{ route('sale.dashboard') }}" class="filter-form" id="autoSearchForm">
    
    <label for="date">📅 วันที่: เดือน / วัน / ปี</label>
    
    <input type="date" id="date" name="date"
        value="{{ request('date') }}">

    {{-- 👇 เพิ่ม dropdown ผู้บันทึก --}}
    <label for="emp_name" style="margin-left: 15px;">ผู้บันทึก:</label>
    <select name="emp_name" id="emp_name" 
            onchange="document.getElementById('autoSearchForm').submit();"
            style="padding: 5px; border-radius: 5px;">
        <option value="">-- ทั้งหมด --</option>
        @foreach($empList as $emp)
            <option value="{{ $emp }}" {{ request('emp_name') == $emp ? 'selected' : '' }}>
                {{ $emp }}
            </option>
        @endforeach
    </select>

    <input type="hidden" name="create_by" value="{{ request('create_by') }}">
    <input type="hidden" name="keyword" value="{{ request('keyword') }}">
    <input type="hidden" name="so_keyword" value="{{ request('so_keyword') }}">

    <button type="submit" style="display: none;">ค้นหา</button>
</form>
        


    <!-- Filter & Search Section -->
    <div class="filter-container">
  
    
    <script>
const form = document.getElementById('autoSearchForm');
const dateInput = document.getElementById('date');

dateInput.addEventListener('change', () => {
    form.submit();
});

window.addEventListener('load', () => {
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
<form method="GET" action="{{ route('sale.dashboard') }}" class="search-box">
    <input type="text" 
        name="so_keyword" 
        placeholder="ค้นหา อ้างอิงใบสั่งขาย"
        value="{{ request('so_keyword') }}">

    <input type="hidden" name="keyword" value="{{ request('keyword') }}">
    <input type="hidden" name="date" value="{{ request('date') }}">
    <input type="hidden" name="create_by" value="{{ request('create_by') }}">
    <input type="hidden" name="emp_name" value="{{ request('emp_name') }}"> 
</form>
<form method="GET" action="{{ route('sale.dashboard') }}" class="search-box">
    
    <input type="text" 
        name="keyword" 
        placeholder="ค้นหา เลขที่บิล"
        value="{{ request('keyword') }}">

    <!-- 👇 เพิ่มเหมือนกัน -->
    <input type="hidden" name="so_keyword" value="{{ request('so_keyword') }}">
    <input type="hidden" name="date" value="{{ request('date') }}">
    <input type="hidden" name="create_by" value="{{ request('create_by') }}">
</form>

<script>
document.getElementById('searchInput').addEventListener('input', function () {
    const keyword = this.value.toLowerCase();
    const items = document.querySelectorAll('#dataList li');

    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(keyword) ? '' : 'none';
    });
});
</script>



    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>อ้างอิงใบส่งของ</th>
                    <th>อ้างอิงใบสั่งขาย</th>
                    <th>อ้างอิงใบสั่งซื้อ</th>
                    <th>REF</th>
                    <th>ชื่อลูกค้า</th>
                    <th>วันที่จัดส่ง</th>
                    <th>ผู้บันทึก</th>
                    <th>ประเภทบิล</th>
                    <th>บันทึกลงระบบ</th>
                    <th>ประเภทงาน</th>
                    <th>สถานะ</th>
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

<td @if($item->statusdeli == 1) style="background-color: #a5d6a7;" @endif>
    @if($hasPdf)
        <span style="white-space: nowrap;">
            @if($isNoMerge)
                {{-- CUS-26039: เปิดไฟล์อย่างเดียว ไม่ merge --}}
                <a href="{{ asset('storage/doc_document/' . $item->billid . '.pdf') }}"
                   target="_blank"
                   style="color:#0000FF; font-weight:bold; cursor:pointer;">
                    {{ $item->billid }}
                </a>
            @else
                <a href="javascript:void(0);"
                   style="color:#0000FF; font-weight:bold; cursor:pointer;"
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
<script>
    function mergeAndOpenPdfs(billid) {
        const pdfDocUrl = "{{ asset('storage/doc_document') }}/" + billid + ".pdf";
        const pdfBillUrl = "{{ asset('storage/bill_document') }}/" + billid + ".pdf";
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
                    <td>
                    <a href="#"
                        class="text-blue-600 hover:underline"
                        onclick="return openPopup3E('{{ $item->so_id }}');">
                        {{ $item->so_id }}
                    </a>
                    </td>

                    <td>{{ $item->ponum }}</td>
                    <td style="font-size: 10px;">{{ $item->so_detail_id }}</td>
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
                                style="color:#28a745; font-weight:bold; cursor:pointer; margin-left:3px;"
                                title="เปิดไฟล์ใบเสร็จ">+</a>
                            @else
                                <a href="javascript:void(0);"
                                style="color:#28a745; font-weight:bold; cursor:pointer; margin-left:3px;"
                                onclick="openBillOnly('{{ $item->billid }}')"
                                title="เปิดไฟล์ใบเสร็จ">+</a>
                            @endif
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->time)->format('H:i d/m/Y ') }}</td>
                    <td>{{ $item->formtype }}</td>
                    <td style="font-size: 12px;">
                    @if($item->statuspdf == 0)
                        กำลังดำเนินการ
                    @elseif($item->statuspdf == 6)
                        ยกเลิก
                    @else
                        ปริ้นสำเร็จ
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
                </textarea>
            </div>
        </div>
    </div>
    
   
 <script>
        function openPopup(soDetailId,so_id,ponum,customer_name,customer_address,date_of_dali,sale_name,POdocument,notes) {
        document.getElementById("popup").style.display = "flex"; // แสดง Popup
    
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
    
        // ใช้ fetch ดึงข้อมูลจาก Laravel
        fetch(`/get-bill-detail/${soDetailId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    secondPopupBody.innerHTML = ""; // เคลียร์ข้อมูลเก่า
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
       
           // ฟังก์ชั่นปิด Popup
function closePopup() {
    document.getElementById('popup').style.display = 'none';
}

// ฟังก์ชั่นปิด Popup เมื่อคลิกนอกพื้นที่ของ Popup
window.onclick = function(event) {
    var popup = document.getElementById('popup');
    if (event.target === popup) {
        closePopup();
    }
}
    
    </script>
 {{-- <script>
    function searchTable() {
        let searchInput = document.getElementById("search-input").value.toLowerCase();
        let table = document.querySelector("table tbody");
        let rows = table.getElementsByTagName("tr");
    
        for (let i = 0; i < rows.length; i++) {
            let row = rows[i];
            let cells = row.getElementsByTagName("td");
            let soDetailId = cells[1].textContent.toLowerCase(); 
    
            if (soDetailId.indexOf(searchInput) > -1) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }
//     window.onload = function() {
// sortTableDescByColumn(0); 
// };
</script> --}}
<script>
  function openPopup3E(soId) {
    const url = `http://server-3e/3e/store_report.php?so=${encodeURIComponent(soId)}&po=&search=Search&rowPerPage=25&currentPage=0`;

    // ขนาดป๊อปอัป
    const w = 1100, h = 500;
    const margin = 16; // เว้นจากขอบจอ

    // รองรับหลายจอ
    const dualScreenLeft = window.screenLeft ?? window.screenX ?? 0;
    const dualScreenTop  = window.screenTop  ?? window.screenY ?? 0;

    // ขนาดหน้าต่างหลักตอนนี้
    const width  = window.innerWidth  ?? document.documentElement.clientWidth  ?? screen.width;
    const height = window.innerHeight ?? document.documentElement.clientHeight ?? screen.height;

    // คำนวณให้ไปขวาล่าง (ลบ margin)
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
      'noopener=yes' // เพิ่มความปลอดภัย
    ].join(',');

    const win = window.open(url, '_blank', features);

    // fallback ถ้าโดนบล็อกป๊อปอัป
    if (!win) window.open(url, '_blank', 'noopener');

    return false; // กันการนำทางของ <a href="#">
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
/* ❌ ลบ Showing 1 to 100 of xxxx results */
nav[role="navigation"] > div > p{
    display:none !important;
}

/* ❌ ลบ mobile pagination: « Previous  Next » */
nav[role="navigation"] .sm\:hidden{
    display:none !important;
}

/* ครอบให้อยู่กลาง */
.pagination-wrap{
    display:flex;
    justify-content:center;
    margin:20px 0;
}

/* โครงสร้างหลัก */
nav[role="navigation"] > div{
    display:flex !important;
    align-items:center !important;
    justify-content:center !important;
    gap:16px;
    flex-wrap:nowrap !important;
}

/* ลบคำ Previous / Next */
nav[role="navigation"] a[rel="prev"] span,
nav[role="navigation"] a[rel="next"] span{
    display:none !important;
}

/* ปุ่มลูกศร */
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

/* ไอคอนลูกศร */
nav[role="navigation"] svg{
    width:20px !important;
    height:20px !important;
    stroke-width:3;
}

/* เลขหน้า */
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

/* หน้า active */
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
