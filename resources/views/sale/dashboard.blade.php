<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📑 ระบบเปิดบิล</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', 'Arial', sans-serif;
            background-color: #f4f7f9;
            color: #2c3e50;
            line-height: 1.6;
            padding: 20px;
        }

        .header {
            background: linear-gradient(99deg, #3f865d 0%, #3f865d 65%, rgb(45, 79, 68) 100%);
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
            font-size: 26px;
            font-weight: bold;
        }

        .header .buttons span {
            margin-right: 15px;
            font-weight: 500;
        }

        .header .buttons a {
            background-color: #e74c3c;
            color: #fff;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s, transform 0.2s;
        }

        .header .buttons a:hover {
            background-color: #c0392b;
            transform: scale(1.03);
        }

        .filter-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 20px;
        }

        .filter-form label,
        .search-box input {
            font-size: 15px;
        }

        .filter-form input[type="date"],
        .search-box input {
            padding: 10px 14px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            outline: none;
            background-color: #ffffff;
        }

        .search-box input {
            width: 280px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 9px;
            text-align: center;
            border: 1px solid #e1e4e8;
            font-size: 14px;
        }

        th {
            background: linear-gradient(99deg, #3f865d 0%, #3f865d 65%);
            color: white;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #eef2f3;
        }

        table a {
            color: #2980b9;
            text-decoration: none;
            font-weight: 500;
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
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            width: 95%;
            max-width: 1050px;
            max-height: 85%;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.25);
        }

        .popup-content table th {
            white-space: nowrap;
            font-size: 14px;
            padding: 10px 12px;
        }

        .popup-content .table-container {
            overflow-x: auto;
        }

        .popup-content table {
            min-width: 950px;
        }

        .close-btn {
            position: absolute;
            top: 12px;
            right: 18px;
            font-size: 24px;
            cursor: pointer;
            color: #2c3e50;
        }

        .close-btn:hover {
            color: #000;
        }

        textarea {
            width: 100%;
            height: 90px;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            resize: none;
            font-size: 14px;
            background-color: #fefefe;
        }

        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column;
            }

            .search-box input {
                width: 100%;
            }
        }
        td.wrap-text {
  max-width: 250px;
  overflow-wrap: break-word;
}

    </style>
</head>
<body>

    <div class="header">
        <h2>📑 ระบบเปิดบิล</h2>
        <div class="buttons">
            <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>
            @csrf
            
            <a href="SOlist" button  type="submit" class="btn btn-danger">🚪 หน้าหลัก</a>
        </div>
        <a href="alertsale">แจ้งเตือนนะจ๊ะ</a>
    </div>
    
    <!-- Filter & Search Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('sale.dashboard') }}" class="filter-form" id="autoSearchForm">
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
                    <td>{{ $item->so_detail_id }}</td> 
                    <td>{{ $item->so_id }}</td>
                    <td>{{ $item->ponum }}</td>
                    <td>{{ $item->billid }}</td>
                    <td class="wrap-text">{{ $item->customer_name }}</td>
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
                            <th>เลขที่บิล</th>
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
                <textarea id="popup-body-3" readonl style="width: 990px; height: 70px;" readonly>
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
