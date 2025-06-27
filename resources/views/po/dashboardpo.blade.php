<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <link rel="stylesheet" href="{{ asset('css/dashboardpo.blade.css') }}"> --}}
    <title>ข้อมูลรับของ PO</title>
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
        background: linear-gradient(90deg, #1e3e7a 0%, #1e3e7a 65%, #355ca8 100%);
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
        font-weight: 600;
    }

    .buttons {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
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
        background-color: #f0ad4e;
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

    th {
        background-color: #1e3e7a;
        color: #fff;
        font-weight: 600;
        white-space: nowrap;
    }

    tr:nth-child(even) {
        background-color: #f7fdf9;
    }

    tr:hover {
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
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 16px;
    }

    .popup-content {
        background-color: #ffffff;
        padding: 25px;
        border-radius: 12px;
        width: 50%;
        max-width: 90vw;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.25);
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

    textarea, #popup-body-3 {
        font-family: inherit;
        font-size: 14px;
        padding: 10px;
        width: 100%;
        border-radius: 6px;
        border: 1px solid #ccc;
        background-color: #f9f9f9;
        resize: vertical;
        box-sizing: border-box;
    }

    @media (max-width: 768px) {
        .popup-content {
            padding: 15px;
            max-width: 95vw;
            max-height: 95vh;
        }
    }
</style>

</head>
<body>
    <div class="header">
        <h2> ข้อมูลรับของ PO</h2>
        <div class="buttons">
            <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>
    
            <a href="{{ route('po.insertpo') }}" class="btn btn-warning">➕ เปิดบิลPO</a> 
            @csrf
            <a href="http://server_update:8000/solist" button  type="submit" class="btn btn-danger">🚪 หน้าหลัก</a>
        </div>
    </div>

    <div class="filter-container">
        <form method="GET" action="{{ route('po.dashboardpo') }}" class="filter-form" id="autoSearchForm">
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
                    <th>บิลลำดับที่</th>
                    <th>เลขอ้างอิงใบรับสินค้า</th>
                    <th>ชื่อร้านค้า</th>
                    <th>วันที่รับสินค้า</th>
                    <th>ผู้เปิดบิล</th>
                    <th>ประเภทขนส่ง</th>
                    <th>ข้อมูลสินค้า</th>
                </tr>
            </thead>
                </div>     

                <tbody id="table-body">
                    @foreach($pobill as $item)
                    <tr>
                        <td>{{ count($pobill) - $loop->index }}</td>
                        <td>{{ $item->po_id}}</td>
                        <td class="wrap-text">{{ $item->store_name }}</td>
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
                        
                        <td><a href="javascript:void(0);" 
                            onclick="openPopup(
                                '{{ $item->po_detail_id }}',
                                '{{ $item->po_id }}',
                                '{{ $item->store_name}}',
                                '{{ $item->store_address}}',
                                '{{ \Carbon\Carbon::parse($item->recvDate)->format('d/m/Y') }}',
                                '{{ $item->emp_name}}',
                                '{{ $item->cartype}}',
                                '{{ $item->notes}}',
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
                     <br>
                 <textarea id="popup-body-3" readonly></textarea>
                </textarea>
                </div>
            </div>
        </div>
        
        <script>
function openPopup(po_detail_id, po_id, store_name, store_address, recvDate, emp_name, cartype,notes) {
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
            <td>${po_id}</td>
            <td>${store_name}</td>
            <td>${store_address}</td>
            <td>${recvDate}</td>
            <td>${emp_name}</td>
            <td>${cartypeText}</td>
        </tr>
    `;
    document.getElementById("popup-body-3").value = notes;

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
                    let soDetailId = cells[1].textContent.toLowerCase(); 
            
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
