<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📑 ระบบเปิดบิล</title>
    <style>
/* รีเซ็ตสไตล์พื้นฐาน */
{

    box-sizing: border-box;
    margin: 0;
    padding: 0; 
}

/* สไตล์สำหรับ body */
body {
    font-family: 'Arial', sans-serif;
    background-color:rgb(255, 255, 255);
    color: #343a40;
    line-height: 1.6;
    padding: 20px;
}

/* ส่วนหัวของหน้า */
.header {
    background-color: #3f865d ;
    color: #ffffff;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header h2 {
    margin: 0;
}

.header .buttons {
    display: flex;
    align-items: center;
}

.header .buttons span {
    margin-right: 15px;
}

.header .buttons a {
    background-color: #dc3545;
    color: #ffffff;
    padding: 8px 15px;
    text-decoration: none;
    border-radius: 5px;
}

.header .buttons a:hover {
    background-color: #c82333;
}

/* คอนเทนเนอร์สำหรับฟิลเตอร์และการค้นหา */
.filter-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin-bottom: 20px;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
}

.filter-form {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.filter-form label {
    margin-right: 10px;
}

.filter-form input[type="date"] {
    padding: 8px;
    margin-right: 10px;
    border: 1px solid #ced4da;
    border-radius: 5px;
}

.filter-form button {
    padding: 8px 15px;
    background-color: #007bff;
    color: #ffffff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.filter-form button:hover {
    background-color: #0056b3;
}

.search-box {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.search-box input {
    padding: 8px;
    width: 200px;
    border: 1px solid #ced4da;
    border-radius: 5px;
}

/* คอนเทนเนอร์สำหรับตาราง */
.table-container {
    overflow-x: auto;
}

table {
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    border-collapse: collapse;
    background-color: #ffffff;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}


table th, table td {
    padding: 12px;
    text-align: center; /* จัดข้อความให้อยู่ตรงกลางแนวนอน */
    vertical-align: middle; /* จัดข้อความให้อยู่ตรงกลางแนวตั้ง */
    border: 1px solid #dee2e6; /* เพิ่มเส้นขอบให้กับเซลล์ */
    font-size: 14px; /* ปรับขนาดตัวอักษรที่นี่ */
}


table th {
    background-color: #3f865d ;
    color: #ffffff;
}

table tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}

table tbody tr:hover {
    background-color: #e9ecef;
}

table a {
    color: #007bff;
    text-decoration: none;
}

table a:hover {
    text-decoration: underline;
}

/* สไตล์สำหรับป็อปอัป */
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.popup-content {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 5px;
    width: 100%;
    max-width: 1000px;
    position: relative;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
}

.close-btn {
    position: absolute;
    top: 0;
    right: 15px;
    font-size: 20px;
    cursor: pointer;
    color: #343a40;
}

.close-btn:hover {
    color: #000000;
}

.popup-content table {
    width: 100%;
    margin-bottom: 20px;
}

textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    resize: none;
}

/* การปรับสไตล์สำหรับหน้าจอขนาดเล็ก */
@media (max-width: 768px) {
    .filter-container {
        flex-direction: column;
    }

    .filter-form, .search-box {
        width: 100%;
    }

    .search-box input {
        width: 100%;
    }
}



        </style>
</head>
<body>

    <div class="header">
        <h2>📑 ระบบเปิดบิล</h2>
        <div class="buttons">
            <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>
            @csrf
            <a href="adminSO" button  type="submit" class="btn btn-danger">🚪 หน้าหลัก</a>
        </div>
    </div>
    
    <!-- Filter & Search Section -->
    <div class="filter-container">
    <form method="GET" action="{{ route('sale.dashboard') }}" class="filter-form">
    <label for="date">📅 วันที่:</label>
    <input type="date" id="date" name="date" value="{{ request('date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
    <button type="submit">ค้นหา</button>
</form>

    
        <div class="search-box">
            <input type="text" id="search-input" placeholder=" ค้นหา เลขที่บิล" onkeyup="searchTable()">
        </div>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>เลขที่บิล</th>
                    <th>อ้างอิงใบสั่งขาย</th>
                    <th>อ้างอิงใบสั่งซื้อ</th>
                    <th>ชื่อลูกค้า</th>
                    <th>ที่อยู่จัดส่ง</th>
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
                    <td>{{ $item->so_detail_id }}</td> 
                    <td>{{ $item->so_id }}</td>
                    <td>{{ $item->ponum }}</td>
                    <td>{{ $item->customer_name }}</td>  
                    <td>{!! nl2br(e(wordwrap($item->customer_address, 100, "\n", true))) !!}</td>
                    <td>{{ \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') }}</td> 
                    <td>{{ $item->emp_name }}</td> 
                    <td>{{ $item->billtype }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->time)->format('H:i d/m/Y ') }}</td>
                    <td>
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
                            '{{ $item->notes}}',
                            '{{ $item->POdocument}}',
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
                            <th>เลขที่บิล</th>
                            <th>อ้างอิงใบสั่งขาย</th>
                            <th>อ้างอิงใบสั่งซื้อ</th>
                            <th>ชื่อลูกค้า</th>
                            <th>ที่อยู่จัดส่ง</th>
                            <th>วันที่จัดส่ง</th>
                            <th>ผู้ขาย</th>
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
                        </tr>
                    </thead>
                    <tbody id="popup-body">
                    </tbody>
                </table>
                <br>
                <textarea id="popup-body-3" readonl style="width: 950px; height: 70px;">
                </textarea>
                


            </div>
        </div>
    </div>
    
   
 <script>
        function openPopup(soDetailId,so_id,ponum,customer_name,customer_tel,customer_address,date_of_dali,sale_name) {
        document.getElementById("popup").style.display = "flex"; // แสดง Popup
    
        let popupBody = document.getElementById("popup-body-1");
        popupBody.innerHTML = `
            <tr>
                <td>${soDetailId}</td>
                <td>${so_id}</td>
                <td>${ponum}</td>
                <td>${customer_name}</td>
                <td>${customer_tel}</td>
                <td>${customer_address}</td>
                <td>${date_of_dali}</td>
                <td>${sale_name}</td>
            </tr>
        `;
    
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
    
        // ฟังก์ชันปิด Popup
        function closePopup() {
            document.getElementById("popup").style.display = "none"; // ซ่อน Popup
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
            let soDetailId = cells[0].textContent.toLowerCase(); 
    
            if (soDetailId.indexOf(searchInput) > -1) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }
    function deleteBill(soDetailId) {
    // Ask for confirmation before deleting
    const confirmation = confirm("คุณต้องการลบบิลนี้หรือไม่?");
    if (confirmation) {
        fetch(/delete-bill/${soDetailId}, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.success); // Show success message
                closePopup(); // Close the popup after successful delete
                location.reload(); // Reload the page to reflect the changes
            } else {
                alert(data.error); // Show error message if deletion fails
            }
        })
        .catch(error => {
            console.error("Error deleting bill:", error);
            alert("เกิดข้อผิดพลาดในการลบบิล");
        });
    }
}
    </script>




    </body>
    </html>