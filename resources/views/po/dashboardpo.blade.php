<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📑 ระบบPO</title>
    <style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Segoe UI', Arial, sans-serif;
  background-color: #f7f9fc;
  color: #343a40;
  line-height: 1.6;
  padding: 20px;
}

.header {
  background: linear-gradient(90deg, #1e3e7a 0%, #1e3e7a 65%, #355ca8 100%);
  color: #fff;
  padding: 15px;
  border-radius: 6px;
  margin-bottom: 30px;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.header h2 {
  font-size: 24px;
}

.buttons {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  align-items: center;
}

.buttons span {
  margin-right: 10px;
  font-weight: 500;
}

.buttons a {
  text-decoration: none;
  padding: 6px 12px;
  border-radius: 6px;
  font-weight: bold;
  transition: all 0.2s ease;
  font-size: 14px;
}

.buttons a:first-of-type {
  background-color: rgb(36, 180, 0);
  color: white;
}

.buttons a:first-of-type:hover {
  background-color: #1f9f00;
}

.buttons a:last-of-type {
  background-color: #dc3545;
  color: white;
}

.buttons a:last-of-type:hover {
  background-color: #c82333;
}

.filter-container {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-bottom: 20px;
}

@media (min-width: 768px) {
  .filter-container {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
}

.filter-form input[type='date'],
.search-box input {
  padding: 6px 10px;
  border: 1px solid #ced4da;
  border-radius: 6px;
  font-size: 14px;
  max-width: 100%;
}

.search-box input {
  width: 200px;
}

.table-container {
  overflow-x: auto;
  margin-top: 10px;
}

table {
  width: 100%;
  border-collapse: collapse;
  background-color: white;
  border-radius: 6px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

th,
td {
  padding: 8px 8px;
  text-align: center;
  border: 1px solid #e0e0e0;
  font-size: 13px;
  word-break: break-word;
  white-space: normal;
  max-width: 180px;
}

th {
  background-color: #1e3e7a;
  color: white;
  white-space: nowrap;
}

tbody tr:nth-child(even) {
  background-color: #f2f2f2;
}

tbody tr:hover {
  background-color: #e9ecef;
}

table a {
  color: #007bff;
  text-decoration: none;
}

table a:hover {
  text-decoration: underline;
}

.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 10px;
}

.popup-content {
  background: white;
  padding: 30px 20px 20px 20px;
  border-radius: 8px;
  max-width: 1000px;
  width: 100%;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  position: relative;
  word-break: break-word;
}

.popup-content table th,
.popup-content table td {
  padding: 6px 8px;
  font-size: 14px;
  word-break: break-word;
  white-space: normal;
}

.close-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 20px;
  cursor: pointer;
  color: #333;
}

.close-btn:hover {
  color: #000;
}

textarea {
  width: 100%;
  border-radius: 5px;
  border: 1px solid #ccc;
  padding: 10px;
  resize: none;
  font-size: 14px;
  margin-top: 10px;
}

  </style>
</head>
<body>
    <div class="header">
        <h2>📑 ระบบPO</h2>
        <div class="buttons">
            <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>
    
            <a href="{{ route('po.insertpo') }}" class="btn btn-warning">➕ เปิดบิลPO</a> 
            @csrf
            <a href="{{ route('home') }}" button  type="submit" class="btn btn-danger"style="background-color:red;">🚪 หน้าหลัก</a>
        </div>
    </div>

    <div class="filter-container">
        <form method="GET" action="{{ route('po.dashboardpo') }}" class="filter-form" id="autoSearchForm">
            <label for="date">📅 วันที่:</label>
            <input type="date" id="date" name="date" value="{{ request('date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
            <button type="submit" style="display: none;">ค้นหา</button>
        </form>
    
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
                    <th>บิลลำดับที่</th>
                    <th>เลขอ้างอิงใบรับสินค้า</th>
                    <th>ชื่อร้านค้า</th>
                    <th>วันที่รับสินค้า</th>
                    <th>ผู้เปิดบิล</th>
                    <th>ประเภทขนส่ง</th>
                    <th>สถานะการจัดส่ง</th>
                    <th>ข้อมูลสินค้า</th>
                </tr>
            </thead>
                </div>     

                <tbody id="table-body">
                    @foreach($pobill as $item)
                    <tr>
                        <td>{{ $item->po_detail_id}}</td> 
                        <td>{{ $item->po_id}}</td>
                        <td>{{ $item->store_name}}</td>
                        <td>{{ \Carbon\Carbon::parse($item->recvDate)->format('d/m/Y') }}</td> 
                        <td>{{ $item->emp_name }}</td> 
                        <td>
                            @if($item->cartype == 1)
                                มอเตอร์ไซค์
                            @elseif($item->cartype == 2)
                                รถใหญ่
                            @else
                                ไม่ทราบประเภท
                            @endif
                        </td>
                        
                        <td>
                            @if($item->status == 0)
                                กำลังดำเนินการ
                            @else
                                สำเร็จ
                            @endif
                        </td>
                        <td><a href="javascript:void(0);" 
                            onclick="openPopup(
                                '{{ $item->po_detail_id }}',
                                '{{ $item->store_name}}',
                                '{{ $item->store_address}}',
                                '{{ \Carbon\Carbon::parse($item->recvDate)->format('d/m/Y') }}',
                                '{{ $item->emp_name}}',
                                '{{ $item->cartype}}'
                            )">
                        เพิ่มเติม
                     </a></td>
                    </tr>
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
                                <th>เลขบิลPO</th>
                                <th>ชื่อร้านค้า</th>
                                <th>ที่อยู่ร้านค้า</th>
                                <th>วันที่รับสินค้า</th>
                                <th>ผู้เปิดบิล</th>
                                <th>ประเภทขนส่ง</th>
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
                                <th>ราคา/หน่วย</th>
                            </tr>
                        </thead>
                        <tbody id="popup-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <script>
function openPopup(po_detail_id, store_name, store_address, recvDate, emp_name, cartype) {
    document.getElementById("popup").style.display = "flex"; // แสดง Popup

    // แปลงค่า cartype
    let cartypeText = "";
    switch (cartype) {
        case "1":
            cartypeText = "มอเตอร์ไซค์";
            break;
        case "2":
            cartypeText = "รถใหญ่";
            break;
        default:
            cartypeText = "ไม่ระบุประเภท";
    }

    let popupBody = document.getElementById("popup-body-1");
    popupBody.innerHTML = `
        <tr>
            <td>${po_detail_id}</td>
            <td>${store_name}</td>
            <td>${store_address}</td>
            <td>${recvDate}</td>
            <td>${emp_name}</td>
            <td>${cartypeText}</td>
        </tr>
    `;



    let secondPopupBody = document.getElementById("popup-body");
    secondPopupBody.innerHTML = "<tr><td colspan='4'>Loading...</td></tr>";

    fetch(`/get-pobill-detail/${po_detail_id}`)
        .then(response => response.json())
        .then(data => {
            console.log("API Response:", data); 

            if (Array.isArray(data) && data.length > 0) {
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
    document.getElementById("popup").style.display = "none"; // ซ่อน Popup
}

window.onclick = function(event) {
    let popup = document.getElementById("popup");
    if (event.target === popup) {
        closePopup();
    }
}

        </script>
        


{{--searchTable --}}
    <script>
            function searchTable() {
                let searchInput = document.getElementById("search-input").value.toLowerCase();
                let table = document.querySelector("table tbody");
                let rows = table.getElementsByTagName("tr");
            
                for (let i = 0; i < rows.length; i++) {
                    let row = rows[i];
                    let cells = row.getElementsByTagName("td");
                    let soDetailId = cells[0].textContent.toLowerCase(); 
            
                    if (soDetailId.indexOf(searchInput) > -1) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                }
            }
             window.onload = function() {
    // Sort  the rows by 'so_detail_id' in descending order on page load
              sortTableDescByColumn(0); // Assuming 'so_detail_id' is in the first column (index 0)
        };

        function sortTableDescByColumn(columnIndex) {
            let table = document.querySelector("table tbody");
            let rows = Array.from(table.querySelectorAll("tr"));

            rows.sort(function(rowA, rowB) {
                let cellA = rowA.cells[columnIndex].textContent.trim();
                let cellB = rowB.cells[columnIndex].textContent.trim();

                // Compare numerically or lexicographically, depending on the column type
                if (columnIndex === 0) { // For 'so_detail_id', assuming it's numeric
                    return parseInt(cellB) - parseInt(cellA); // Sort in descending order
                } else {
                    return cellB.localeCompare(cellA); // Sort lexicographically for text columns
                }
            });

            // Reorder the rows in the table
            rows.forEach(row => table.appendChild(row));
        }

    </script>


    </body>
    </html>
