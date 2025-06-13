<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลจัดส่ง</title>
    {{-- <link rel="stylesheet" href="{{ asset('css/dashboard.blade.css') }}"> --}}
<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', 'Roboto', sans-serif;
        background-color: #f0f2f5;
        color: #2c3e50;
        margin: 0;
        padding: 0;
        line-height: 1.6;
    }

     a {
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
    }

    a:hover {
        text-decoration: underline;
    }

    /* HEADER */
    .header {
        background: linear-gradient(99deg, #3f865d 0%, #3f865d 65%, rgb(45, 79, 68) 100%);
        color: #fff;
        border-radius: 6px;
        padding: 8px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        border-bottom: 3px solid #2e594f;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); อ
    }

    .header h2 {
        font-size: 26px;
        margin: 0;
        font-weight: 600;
    }

    .buttons {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .buttons span {
        font-weight: 500;
        font-size: 15px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        background-color: #c0392b;
        color: white;
        transition: background-color 0.3s ease;
    }

    .btn:hover {
        background-color: #a93226;
    }

    .notification-icon img {
        width: 22px;
        height: 22px;
    }

    .notification-badge {
        background-color: red;
        color: white;
        font-size: 12px;
        padding: 2px 6px;
        border-radius: 50%;
        margin-left: 4px;
        vertical-align: top;
    }

    /* FILTER SECTION */
    .filter-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        background-color: #ffffff;
        padding: 10px 24px;
        margin-top: 10px;
        border-bottom: 1px solid #ddd;
    }

    .filter-form label {
        font-weight: 600;
    }

    .filter-form input[type="date"] {
        padding: 6px 12px;
        border-radius: 6px;
        border: 1px solid #ccc;
        background-color: #f9f9f9;
    }

    .search-box input {
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        background-color: #fdfdfd;
    }

    /* TABLE */
    .table-container {
        margin: 20px;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        font-size: 14px;
        border-radius: 10px;
        overflow: hidden;
        min-width: 1000px;
    }

/* ตั้งค่าหลักให้ข้อมูลกลางและขนาดกะทัดรัด */
th, td {
    padding: 6px 8px;
    border: 1px solid #e1e1e1;
    text-align: center;
    vertical-align: middle;
    font-size: 13px;
    line-height: 1.4;
}

/* เฉพาะคอลัมน์ “ชื่อลูกค้า” ชิดซ้าย */
th.customer-name,
td.customer-name {
    text-align: left !important;
}


    th {
        background-color: #3f865d;
        color: #fff;
        font-weight: 600;
        white-space: nowrap;
    }

    tr:nth-child(even) {
        background-color: #f7fdf9;
    }

    .wrap-text {
        word-break: break-word;
    }

    /* Make "ชื่อลูกค้า" LEFT aligned */
    td.customer-name,
    th.customer-name {
        text-align: left !important;
    }

    /* POPUP */
    .popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
    }

    .popup-content {
        background-color: #ffffff;
        padding: 25px;
        border-radius: 12px;
        width: 100%;
        max-width: 1200px;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.25);
        overflow-y: auto;
        max-height: 90vh;
    }

    .close-btn {
        font-size: 24px;
        font-weight: bold;
        float: right;
        cursor: pointer;
        color: #999;
    }

    .close-btn:hover {
        color: #e74c3c;
    }

    textarea {
        font-family: inherit;
        font-size: 14px;
        padding: 10px;
        width: 100%;
        border-radius: 6px;
        border: 1px solid #ccc;
        resize: vertical;
        background-color: #f9f9f9;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .header {
            flex-direction: column;
            align-items: flex-start;
        }

        .filter-container {
            flex-direction: column;
            align-items: flex-start;
        }

        .buttons {
            flex-direction: column;
            align-items: flex-start;
        }

        table {
            font-size: 12px;
        }

        .popup-content {
            padding: 15px;
        }
    }
    
</style>
<style>
.wrap-text {
    text-align: left;
    white-space: normal;
    word-wrap: break-word;
    padding: 10px; /* ซ้าย-ขวา-บน-ล่าง เว้น 10px */
}

</style>


</head>
<body>

    <div class="header">
        <h2>ข้อมูลจัดส่ง</h2>
        <div class="buttons">
            <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>
            @csrf
            
            <a href="SOlist" button  type="submit" class="btn btn-danger">🚪 หน้าหลัก</a>
       <a href="alertsale" title="แจ้งเตือนนะจ๊ะ" class="notification-icon" style="background-color: rgb(245, 245, 69); padding: 5px; border-radius: 5px; display: inline-block;">
             <img src="https://cdn-icons-png.flaticon.com/512/2645/2645897.png" alt="แจ้งเตือน">
            <span class="notification-badge" id="alertBadge">0</span>
        </a>

        </div>
    </div>
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
    <div class="search-box">
        <input type="text" id="search-input" placeholder=" ค้นหา เลขที่บิล" onkeyup="searchTable()">
    </div>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>REF</th>
                    <th>อ้างอิงใบสั่งขาย</th>
                    <th>อ้างอิงใบสั่งซื้อ</th>
                    <th>อ้างอิงใบส่งของ</th>
                    <th>ชื่อลูกค้า</th>
                    <th>วันที่จัดส่ง</th>
                    <th>ผู้เปิดบิล</th>
                    <th>ประเภทบิล</th>
                    <th>เวลาออกบิล</th>
                    <th>สถานะ</th>
                    <th>ข้อมูลสินค้า</th>
                </tr>
            </thead>
            <tbody id="table-body">

                @foreach($bill as $item)
                <tr>
                    <td style="font-size: 10px;">{{ $item->so_detail_id }}</td>
                    <td>{{ $item->so_id }}</td>
                    <td>{{ $item->ponum }}</td>
                    <td>{{ $item->billid }}</td>
                    <td class="wrap-text" style="text-align: left; white-space: normal; word-wrap: break-word;">
                        {{ $item->customer_name }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') }}</td> 
                    <td>{{ $item->emp_name }}</td> 
                    <td>{{ $item->billtype }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->time)->format('H:i d/m/Y ') }}</td>
                    <td style="font-size: 12px;">
                        @if($item->status == 0)
                            กำลังดำเนินการ
                        @else
                            สำเร็จ
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
                <textarea id="popup-body-3" readonly style="width: 1050px; height: 70px;" readonly>
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
