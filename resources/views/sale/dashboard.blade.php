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
            <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>
            @csrf
            
            <a href="http://server_update:8000/solist" button  type="submit" class="btn btn-danger">🚪 หน้าหลัก</a>
            <a href="Sotest" style="background-color: #0077ff; color: white; padding: 6px 8px; border-radius: 5px; text-decoration: none;">ข้อมูลจัดส่ง</a>

       <a href="alertsale" title="แจ้งเตือนนะจ๊ะ" class="notification-icon" style="background-color: rgb(245, 245, 69); padding: 5px; border-radius: 5px; display: inline-block;">
             <img src="https://cdn-icons-png.flaticon.com/512/2645/2645897.png" alt="แจ้งเตือน">
            <span class="notification-badge" id="alertBadge">0</span>
        </a>
        </div>
    </div>
    
    <div class="filter-container">
        <form method="GET" action="{{ route('sale.dashboard') }}" class="filter-form" id="autoSearchForm">
             <label for="date">📅 วันที่: เดือน / วัน / ปี</label>
            <input type="date" id="date" name="date" value="{{ request('date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
            <button type="submit" style="display: none;">ค้นหา</button>
        </form>
        
<script>
  let isChecking = false;

  async function checkForAlerts() {
    if (isChecking) return; // ป้องกันเรียกซ้ำซ้อน
    isChecking = true;

    try {
      const response = await fetch('/alertsale/count', {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        }
      });

      if (!response.ok) throw new Error("Response ไม่โอเค");

      const data = await response.json();
      const badge = document.getElementById('alertBadge');

      if (data.count > 0) {
        badge.textContent = data.count;
        badge.style.display = 'block';
      } else {
        badge.style.display = 'none';
      }
    } catch (error) {
      console.error('ไม่สามารถเช็คการแจ้งเตือนได้:', error);
    } finally {
      isChecking = false;
    }
  }

  // เรียกตอนโหลดหน้า
  checkForAlerts();

  // เรียกซ้ำทุก 1 วิ
  setInterval(checkForAlerts, 1000);
</script>


    <!-- Filter & Search Section -->
    <div class="filter-container">
  
    
    <script>
        const form = document.getElementById('autoSearchForm');
        const dateInput = document.getElementById('date');
    
        // ส่งฟอร์มเมื่อเปลี่ยนวันที่
        dateInput.addEventListener('change', () => {
            form.submit();
        });
    
        // ส่งฟอร์มอัตโนมัติเมื่อเข้าหน้าเว็บครั้งแรกเท่านั้น
        window.addEventListener('load', () => {
            if (!sessionStorage.getItem('hasAutoSubmitted')) {
                sessionStorage.setItem('hasAutoSubmitted', 'true');
                form.submit();
            }
        });
    </script>
    </div>
    <div class="search-box">
        <input type="text" id="search-input" placeholder=" ค้นหา เลขที่บิล" onkeyup="searchTable()">
    </div>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>อ้างอิงใบส่งของ</th>
                    <th>อ้างอิงใบสั่งขาย</th>
                    <th>อ้างอิงใบสั่งซื้อ</th>
                     <th>REF</th>
                    <th>ชื่อลูกค้า</th>
                    <th>วันที่จัดส่ง</th>
                    <th>ผู้เปิดบิล</th>
                    <th>ประเภทบิล</th>
                    <th>เวลาออกบิล</th>
                    <th>ประเภทงาน</th>
                    <th>สถานะ</th>
                    <th>ข้อมูลสินค้า</th>
                </tr>
            </thead>
            <tbody id="table-body">

                @foreach($bill as $item)
                <tr>
                    <td>{{ $item->billid }}</td>
                    <td>{{ $item->so_id }}</td>
                    <td>{{ $item->ponum }}</td>
                    <td style="font-size: 10px;">{{ $item->so_detail_id }}</td>
                    <td class="wrap-text" style="text-align: left; white-space: normal; word-wrap: break-word;">
                        {{ $item->customer_name }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') }}</td> 
                    <td>{{ $item->emp_name }}</td> 
                    <td>{{ $item->billtype }}</td>
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
                    </td>
                    <td><a href="javascript:void(0);" 
                        onclick="openPopup(
                            '{{ $item->so_detail_id }}',
                            '{{ $item->so_id }}',
                            '{{ $item->ponum }}',
                            '{{ $item->customer_name }}',
                            '{{ $item->customer_address }}',
                            '{{ \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') }}',
                            '{{ $item->sale_name}}',
                            '{{ $item->POdocument}}',
                            '{{ $item->notes}}',
                        )">
                    เพิ่มเติม
                 </a></td>
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
 <script>
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
    window.onload = function() {
// Sort the rows by 'so_detail_id' in descending order on page load
sortTableDescByColumn(0); // Assuming 'so_detail_id' is in the first column (index 0)
};
</script>

    </body>
    </html>
