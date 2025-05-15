<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📑 ระบบDoc</title>
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
background-color: #343a40;
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
background-color: rgb(36, 180, 0);
color: #ffffff;
padding: 8px 15px;
text-decoration: none;
border-radius: 5px;
margin-right: 10px; /* เพิ่มระยะห่าง */
}

.header .buttons a:hover {
background-color: #15b800;
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
background-color: #fff;
border-radius: 5px;
overflow: hidden;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}


table th, table td {
padding: 8px;
text-align: center; /* จัดข้อความให้อยู่ตรงกลางแนวนอน */
vertical-align: middle; /* จัดข้อความให้อยู่ตรงกลางแนวตั้ง */
border: 1px solid #dee2e6; /* เพิ่มเส้นขอบให้กับเซลล์ */
font-size: 14px; /* ปรับขนาดตัวอักษรที่นี่ */
}


table th {
background-color: #343a40;
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
    max-height: 70%;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
}


.close-btn {
    position: absolute;
    top: 0;
    right: 5px;
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
        <h2>📑 ระบบDoc</h2>
        <div class="buttons">
            <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>
    
            <a href="{{ route('document.insertdoc') }}" class="btn btn-warning">➕ เปิดบิลdoc</a>
            
            @csrf
            <a href="{{ route('home') }}" button  type="submit" class="btn btn-danger"style="background-color:red;">🚪 หน้าหลัก</a>
        </div>
    </div>
    
    <!-- Filter & Search Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('document.dashboarddoc') }}" class="filter-form" id="autoSearchForm">
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
            <th>เลขที่บิล</th>
            <th>บริษัท</th>
            <th>ผู้ติดต่อ</th>
            <th>เบอร์โทร</th>
            <th>ประเภทงาน</th>
            <th>ผู้เปิดบิล</th>
            <th>วันที่</th>
            <th>ข้อมูลรายละเอียด</th>
        </tr>
    </thead>
    <tbody id="table-body">
        @foreach($docbill as $item)
        <tr>
            <td>{{ $item->doc_id }}</td>
            <td>{{ $item->com_name }}</td>
            <td>{{ $item->contact_name }}</td>
            <td>{{ $item->contact_tel}}</td>
            <td>{{ $item->doctype }}</td>
            <td>{{ $item->emp_name }}</td>
            <td>{{ \Carbon\Carbon::parse($item->time)->format('d/m/Y') }}</td>
            <td>
                <a href="javascript:void(0);" onclick="openPopup(
                    '{{ $item->doc_id }}',
                    '{{ $item->com_name }}',
                    '{{ $item->com_address }}',
                    '{{ $item->contact_name}}',
                    '{{ $item->contact_tel}}',
                    '{{ $item->notes }}',
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
                            <th>เลขที่บิล</th>
                            <th>บริษัท</th>
                            <th>ที่อยู่</th>
                            <th>ผู้ติดต่อ</th>
                            <th>เบอร์โทร</th>
                        </tr>
                    </thead>
                    <tbody id="popup-body-1">
                    </tbody>
                </table>
                <br>
                <table>
            <thead>     
                <tr>
                    <th>รายการ</th>
                    <th>จำนวน</th>      
                </tr>
            </thead>
            <tbody id="popup-body">
            </tbody>
        </table>
         <br>
            <textarea id="popup-body-3" readonl style="width: 970px; height: 70px;" readonly>
            </textarea>
    </div> 
</div>
</div>
    
    <script>
        function openPopup(doc_id,com_name,com_address,contact_name,contact_tel,notes) {
            document.getElementById("popup").style.display = "flex"; // แสดง Popup
        
            let popupBody = document.getElementById("popup-body-1");
            popupBody.innerHTML = `
                <tr>
                    <td>${doc_id}</td>
                    <td>${com_name}</td>
                    <td>${com_address}</td>
                    <td>${contact_name}</td>
                    <td>${contact_tel}</td>
                </tr>
            `;
            document.getElementById("popup-body-3").value = notes;
            let secondPopupBody = document.getElementById("popup-body");
            secondPopupBody.innerHTML = "<tr><td colspan='4'>กำลังโหลดข้อมูล...</td></tr>";
            
            // ดึงข้อมูลรายการสินค้าจาก Laravel Controller
            fetch(`/get-docbill-detail/${doc_id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        secondPopupBody.innerHTML = ""; 
                        data.forEach(item => {
                            secondPopupBody.insertAdjacentHTML("beforeend", `
                                <tr>
                                    <td>${item.item_name}</td>
                                    <td>${item.quantity}</td>
                                </tr>
                            `);
                        });
                    } else {
                        secondPopupBody.innerHTML = "<tr><td colspan='4'>ไม่มีข้อมูล</td></tr>";
                    }
                })
                .catch(error => {
                    console.error("Error fetching data:", error);
                    secondPopupBody.innerHTML = "<tr><td colspan='4'>เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>";
                });
        }

// ฟังก์ชันปิด Popup
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
    // Sort the rows by 'so_detail_id' in descending order on page load
    sortTableDescByColumn(0); // Assuming 'so_detail_id' is in the first column (index 0)
};
    </script>
    </body>
    </html>