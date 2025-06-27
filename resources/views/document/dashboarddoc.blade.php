<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> ระเอกสารชั่วคราว</title>
     {{-- <link rel="stylesheet" href="{{ asset('css/dashboarddoc.blade.css') }}"> --}}
<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', 'Roboto', sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
        color: #2c3e50;
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
    .container {
        padding: 0 20px;
    }

    .header {
        background-color: #343a40;
        color: #fff;
        border-radius: 6px;
        padding: 8px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        border-bottom: 4px solid #17305a;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        margin: 20px;
    }

    .header h2 {
        font-size: 24px;
        margin: 0;
    }

    .buttons {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .btn {
        padding: 6px 14px;
        border-radius: 4px;
        color: #fff;
        text-decoration: none;
        font-weight: bold;
        font-size: 14px;
        transition: 0.3s;
    }

    .btn-warning {
        background-color: #039418;
    }

    .btn-danger {
        background-color: #d9534f;
    }

    .btn:hover {
        opacity: 0.9;
    }

    /* FILTER */
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

    .filter-form {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
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

    .search-box {
        margin-left: auto;
    }

    #search-input {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    /* TABLE */
    .table-container {
        padding: 20px;
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

    th, td {
        padding: 6px 8px;
        border: 1px solid #e1e1e1;
        text-align: center;
        vertical-align: middle;
        font-size: 13px;
        line-height: 1.4;
    }

    th.customer-name,
    td.customer-name {
        text-align: left !important;
    }

    table thead {
        background-color: #343a40;
        color: white;
    }

    table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    table tbody tr:hover {
        background-color: #eef7f0;
    }

    .wrap-text {
        text-align: left;
        white-space: normal;
        word-wrap: break-word;
        padding: 10px;
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
        float: right;
        font-size: 24px;
        cursor: pointer;
        color: #333;
    }

    .close-btn:hover {
        color: #e74c3c;
    }

    textarea {
        font-family: 'Segoe UI', sans-serif;
        font-size: 14px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        resize: vertical;
        width: 100%;
    }
</style>

</head>
<body>
    <div class="header">
        <h2> เอกสารชั่วคราว</h2>
        <div class="buttons">
            <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>
    
            <a href="{{ route('document.insertdoc') }}" class="btn btn-warning">➕ เปิดบิลdoc</a>
            
            @csrf
                <a href="http://server_update:8000/solist"><button class="btn-so">หน้าหลัก</button></a>
        </div>
    </div>
    
    <!-- Filter & Search Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('document.dashboarddoc') }}" class="filter-form" id="autoSearchForm">
            <label for="date">📅 วันที่: เดือน / วัน / ปี</label>
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
            <textarea id="popup-body-3" readonly></textarea>
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