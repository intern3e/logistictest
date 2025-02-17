<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
  /* --- Global Style --- */
  body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #f0f2f5, #dfe9f3);
            margin: 0;
            padding: 0;
        }

        /* --- Header Style --- */
        .header {
            background: linear-gradient(to right, #2c3e50, #4b6584);
            margin: 40px 5%;
            padding: 20px 5%;
            color: #fff;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .header h4 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
        }

        /* --- Button Container --- */
        .buttons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .buttons span {
            color: white;
            font-weight: bold;
        }

        .buttons a, .buttons button {
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .buttons a {
            background-color: #f39c12;
            color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }

        .buttons a:hover {
            background-color: #e67e22;
            transform: scale(1.05);
        }

        .buttons button {
            background-color: #e74c3c;
            color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }

        .buttons button:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }

        /* --- Table Styling --- */
        .table-container {
            background: white;
            margin: 0 5%;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        th, td {
            padding: 12px;
            border: 1px solid #2c3e50;
            font-size: 1rem;
        }

        th {
            background: linear-gradient(to right, #2c3e50, #4b6584);
            color: white;
            text-transform: uppercase;
        }

        tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #e1e5ea;
            transition: 0.2s;
        }

        /* --- Link Style --- */
        td a {
            color: #27ae60;
            font-weight: bold;
            text-decoration: none;
        }

        td a:hover {
            text-decoration: underline;
        }

        /* Filter & Search Section */
        .filter-container {
            background: #ffffff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin: 20px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .filter-form {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0px 5%;
        }

        .filter-form label {
            font-weight: bold;
            color: #2c3e50;
        }

        .filter-form input {
            padding: 8px;
            border-radius: 5px;
            font-size: 1rem;
        }

        .filter-form button {
            padding: 8px 12px;
            border: none;
            background: #27ae60;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .filter-form button:hover {
            background: #2980b9;
        }

        .search-box {
            flex-grow: 1;
            max-width: 200px;
        }

        .search-box input {
            width: 90%;
            height: 30px;
            margin: 0px -30%;
            background: #f8f9fa;
        }

        .search-box {
            display: flex;
            align-items: center;
            transition: 0.3s;
            max-width: 250px;
        }

        .search-box input {
            flex-grow: 1;
            padding: 5px;
            border: none;
            outline: none;
            font-size: 1rem;
            border-radius: 5px;
            background-color: #e1e5ea;
        }

        .search-box button {
            padding: 10px 15px;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: bold;
        }

        .search-box button:hover {
            background: #27ae60;
            transform: scale(1.05);
        }

        /* สไตล์พื้นหลังมืด */
        .popup-overlay {
            display: none; /* ซ่อน Popup ไว้ก่อน */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* สไตล์กล่อง Popup */
        .popup-content {
            background: linear-gradient(to right, #f0f2f5, #dfe9f3);
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 1000px;
            height: auto;
            text-align: center;
            position: relative;
            overflow: hidden;
            max-height: 500px; /* เพิ่มความสูงสูงสุด */
            overflow-y: auto; /* แสดงแท็บเลื่อน */
        }

        /* ปุ่มปิด */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column;
                align-items: flex-start;
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
    <h4>📑 ระบบเปิดบิล</h4>
    <div class="buttons">
        <span>👤 ผู้ใช้: {{ session('so_number', 'Guest') }}</span>

        <a href="{{ route('sale.insertdata') }}" class="btn btn-warning">➕ เพิ่มข้อมูล</a>
        
            @csrf
            <a href="{{ route('home') }}" button  type="submit" class="btn btn-danger">🚪 หน้าหลัก</a>
    </div>
</div>

<!-- Filter & Search Section -->
<div class="filter-container">
    <form method="GET" action="{{ route('sale.dashboard') }}" class="filter-form">
        <label for="date">📅 วันที่:</label>
        <input type="date" id="date" name="date" value="{{ request('date') }}">
    </form>

    <div class="search-box">
        <input type="text" id="search-input" placeholder=" ค้นหา เลขที่บิล" onkeyup="searchTable()">
        <button type="button" onclick="searchTable()">ค้นหา</button>
    </div>

</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>บิลลำดับที่</th>
                <th>อ้างอิงใบสั่งขาย</th>
                <th>ที่อยู่จัดส่ง</th>
                <th>วันที่จัดส่ง</th>
                <th>ผู้จัดบิล</th>
                <th>สถานะ</th>
                <th>ข้อมูลสินค้า</th>
            </tr>
        </thead>
        <tbody id="table-body">
            @foreach($bill as $item)
            <tr>
            <td>{{ $item->so_detail_id }}</td> 
                <td>{{ $item->customer_id }}</td>
                <td>{{ $item->customer_address }}</td>  
                <td>{{ $item->date_of_dali }}</td> 
                <td>{{ $item->emp_name }}</td> 
                <td> @if($item->status == 0)
                        กำลังดำเนินการ
                    @else
                        {{ $item->status }}
                        สำเร็จ
                    @endif
                </td>
                <td><a href="javascript:void(0);" 
                onclick="openPopup(
                    '{{ $item->so_detail_id }}',
                    '{{ $item->customer_id }}',
                    '{{ $item->customer_address }}',
                    '{{ $item->date_of_dali }}'
                )">
                เพิ่มเติม
             </a></td>
            {{-- '{{ $item->customer ? $item->customer->customer_address : 'ไม่มีข้อมูล' }}',  --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</div>



<!-- Popup -->
<div class="popup-overlay" id="popup" style="display: none;">
    <div class="popup-content">
        <span class="close-btn" onclick="closePopup()">&times;</span>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID SO Detail</th>
                        <th>รหัสลูกค้า</th>
                        <th>ที่อยู่จัดส่ง</th>
                        <th>วันที่จัดส่ง</th>
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
                        <th>จำนวนเงิน</th>
                    </tr>
                </thead>
                <tbody id="popup-body">
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
function openPopup(soDetailId, customer_id, customer_address, date_of_dali) {
    document.getElementById("popup").style.display = "flex"; // แสดง Popup

    let popupBody = document.getElementById("popup-body-1");
    popupBody.innerHTML = `
        <tr>
            <td>${soDetailId}</td>
            <td>${customer_id}</td>
            <td>${customer_address}</td>
            <td>${date_of_dali}</td>
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
                            <td>${calculateTotal(item.quantity, item.unit_price)}</td>
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

    // ปิด Popup เมื่อคลิกนอกกล่อง
    window.onclick = function(event) {
        let popup = document.getElementById("popup");
        if (event.target === popup) { // ถ้าคลิกที่พื้นหลังนอกกล่อง
            closePopup(); // ปิด Popup
        }
    }

    function calculateTotal(quantity, unit_price) {
    let itemQuantity = parseFloat(quantity) || 0;
    let itemPrice = parseFloat(unit_price) || 0;
    return (itemQuantity * itemPrice).toFixed(2);
}


function searchTable() {
    // ดึงข้อมูลจากช่องค้นหา
    let searchInput = document.getElementById("search-input").value.toLowerCase();
    
    // ดึงข้อมูลจาก tbody
    let table = document.querySelector("table tbody");
    let rows = table.getElementsByTagName("tr");

    // ลูปผ่านแถวทั้งหมดในตาราง
    for (let i = 0; i < rows.length; i++) {
        let row = rows[i];
        let cells = row.getElementsByTagName("td");

        // ตรวจสอบว่าค่าของ SO Detail ID อยู่ในแถวไหน (เช่น ใช้คอลัมน์ที่ 0 หรือ 1 ขึ้นอยู่กับความต้องการ)
        let soDetailId = cells[0].textContent.toLowerCase();  // เปลี่ยนจาก cells[1] เป็น cells[0] ถ้าค้นหาจากคอลัมน์แรก

        // ถ้า SO Detail ID ตรงกับข้อความที่ค้นหาให้แสดงแถว
        if (soDetailId.indexOf(searchInput) > -1) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    }
}
</script>


</body>
</html>