<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดเตรียมสินค้า</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .header {
            background: linear-gradient(to right, #0e50ad, #3a6073);
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.2rem;
            border-radius: 8px;
            margin: 20px auto;
            width: 90%;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
        .header button {
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        }
        .header button:hover {
            background-color: #c0392b;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            margin: auto;
        }
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

        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .top-section button {
            padding: 8px 12px;
            border: none;
            background: #27ae60;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        .top-section {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0px 5%;
        }

        .top-section label {
            font-weight: bold;
            color: #2c3e50;
        }

        .top-section input {
            padding: 8px;
            border-radius: 5px;
            font-size: 1rem;
        }
        .top-section button:hover {
            background: #2980b9;
        }
        .filter-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-container input {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .filter-container button {
            background-color: #2ecc71;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .filter-container button:hover {
            background-color: #27ae60;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .button-group button {
            padding: 15px 20px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .button-group button {
            background-color: #f39c12;
            font-size: 16px;
            color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }

        .button-group button:hover {
            background-color: #e67e22;
            transform: scale(1.05);
        }
        .table th, .table td {
            padding: 12px;
            border: 1px solid #dcdde1;
            text-align: center;
        }

        .table th {
            background-color: #e67e22;
            color: white;
            font-weight: bold;
        }

        .table-striped tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table-striped tr:hover {
            background-color: #ecf0f1;
        }

        .link {
            color: #16a085;
            font-weight: bold;
            text-decoration: none;
        }
        .link:hover {
            text-decoration: underline;
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
            background-color: #0e50ad;
            color: white;
            text-transform: uppercase;
        }

        tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        .tr {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #e1e5ea;
            width: 70%;
            transition: 0.2s;
        }

        td a {
            color: #27ae60;
            font-weight: bold;
            text-decoration: none;
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

        .popup-overlay {
            display: none;
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
            max-height: 500px;
            overflow-y: auto;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }
        .search-box {
            flex-grow: 1;
            max-width: 200px;
        }

        .search-box input {
            width: 90%;
            height: 30px;
            margin: 0px 10px;
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
        .container {
        align-items: center;
        gap: 10px; /* ระยะห่างระหว่าง label และ dropdown */
        margin-top: 10px;
        text-align: center;
        width: 100%;
        }
        
    </style>
</head>
<body>
    <div class="header">
        <h2>ระบบจัดเตรียมรถรับของPO</h2>
        <a href="dashboardadmin"><button >ระบบจัดเตรียมสินค้าSO</button></a>
        <a href="adminSO"><button >หน้าหลัก</button></a>
    </div>

    <div class="container">
        <div class="top-section">
            <form method="GET" action="{{ route('po.adminpo') }}" class="filter-form">
                <label for="date">📅 วันที่:</label>
                <input type="date" id="date" name="date" value="{{ request('date') }}">
                <button type="submit">ค้นหา</button>
            </form>

            <div class="button-group">
                <button onclick="exportToExcel()">🖨 ปริ้นเอกสาร</button>
                <button onclick="window.location.href='historypo'">📜 ประวัติเอกสาร</button>
                <div class="container">
                    <label for="cartype">🚗 ประเภทรถ :</label>
                    <select id="cartype" onchange="filterTable()">
                        <option value="">ทั้งหมด</option>
                        <option value="1">รถมอเตอร์ไซค์</option>
                        <option value="2">รถใหญ่</option>
                    </select>
                     </div>
            </div>
            
            <div class="search-box">
            <input type="text" id="search-input" placeholder=" ค้นหา เลขที่บิล" onkeyup="searchTable()">
        </div>
        
        </div>
  
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ปริ้นเอกสาร</th>
                        <th>เลขอ้างอิงใบรับสินค้า</th>
                        <th>ชื่อร้านค้า</th>
                        <th>ที่อยู่ร้านค้า</th>
                        <th>ละติจูดลองจิจูด</th>
                        <th>วันที่รับสินค้า</th>
                        <th>ผู้เปิดบิล</th>
                        <th>ประเภทขนส่ง</th>
                        <th>สถานะการจัดส่ง</th>
                        <th>ข้อมูลสินค้า</th>
                        
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($pobill as $item)
                        @if($item->status == 0)
                            <tr>
                                <td><input type="checkbox" class="form-control1" name="status[]"></td>
                                <td>{{ $item->po_detail_id }}</td>
                                <td>{{ $item->store_name }}</td>
                                <td>{{ $item->store_address }}</td>  
                                <td>{{ $item->store_la_long }}</td>
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
                        @endif
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
                        <th>ID SO Detail</th>
                        <th>รหัสลูกค้า</th>
                        <th>ที่อยู่จัดส่ง</th>
                        <th>วันที่จัดส่ง</th>
                        <th>ผุ้ขาย</th>
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

    // ฟังก์ชันปิด Popup
    function closePopup() {
        document.getElementById("popup").style.display = "none"; // ซ่อน Popup
    }
    function filterTable() {
    let selectedType = document.getElementById("cartype").value; // รับค่าจาก dropdown
    let table = document.getElementById("table-body");
    let rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        let typeCell = rows[i].getElementsByTagName("td")[7]; // เปลี่ยน index ให้ตรงกับ "ประเภทขนส่ง"
        if (typeCell) {
            let typeText = typeCell.textContent.trim(); // ดึงค่าจาก <td>

            // แปลงค่า text เป็นค่าของ dropdown
            let typeValue = "";
            if (typeText === "มอเตอร์ไซค์") typeValue = "1";
            if (typeText === "รถใหญ่") typeValue = "2";

            // เช็คเงื่อนไขการกรอง
            if (selectedType === "" || typeValue === selectedType) {
                rows[i].style.display = ""; // แสดงแถว
            } else {
                rows[i].style.display = "none"; // ซ่อนแถว
            }
        }
    }
}

</script>


<script>
function exportToExcel() {
    let table = document.querySelector("table");
    let rows = table.querySelectorAll("tr");
    let data = [];
    let checkedRows = [];
    let selectedSoDetailIds = []; // Array to store the selected so_detail_ids

    rows.forEach(row => {
        let checkbox = row.querySelector("input[type='checkbox']");
        if (checkbox && checkbox.checked) {
            let rowData = [];
            let cells = row.querySelectorAll("td");
            cells.forEach(cell => {
                rowData.push(cell.textContent.trim());
            });
            data.push(rowData);
            checkedRows.push(row);

            // Collect the so_detail_id from the row
            let soDetailId = row.querySelector("td:nth-child(2)").textContent.trim();
            selectedSoDetailIds.push(soDetailId);
        }
    });

    if (data.length > 0) {
        let xml = createExcelXML(data);
        let blob = new Blob([xml], { type: "application/vnd.ms-excel" });
        let link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "เอกสารจัดเตรียมสินค้า.xls";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        // Update the status of checked rows to 1
        checkedRows.forEach(row => {
            let statusCell = row.querySelector("td:first-child");
            if (statusCell) {
                statusCell.innerHTML = "✅ พิมพ์แล้ว";
            }
        });

        // Send AJAX request to update the status in the database
        updateStatus(selectedSoDetailIds);

        // Reload the page after printing
        location.reload();
    } else {
        alert("กรุณาเลือกข้อมูลที่ต้องการพิมพ์");
    }
}

function updateStatus(poDetailIds) {
    fetch('/update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ poDetailIds: poDetailIds }) 
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log("Status updated successfully");
        } else {
            console.error("Failed to update status");   
        }
    })
    .catch(error => {
        console.error("Error updating status:", error);
    });
}

function createExcelXML(data) {
    const xmlHeader = `<?xml version="1.0" encoding="UTF-8"?>
        <?mso-application progid="Excel.Sheet"?>
        <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                  xmlns:o="urn:schemas-microsoft-com:office:office"
                  xmlns:x="urn:schemas-microsoft-com:office:excel"
                  xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
                  xmlns:html="http://www.w3.org/TR/REC-html40">
        <Worksheet ss:Name="Sheet1">
            <Table>`;

    const xmlFooter = `</Table></Worksheet></Workbook>`;

    // Adding headers for the columns
    const headerRow = `<Row>
        <Cell><Data ss:Type="String">บิลลำดับ</Data></Cell>
        <Cell><Data ss:Type="String">รหัสลูกค้า</Data></Cell>
        <Cell><Data ss:Type="String">ที่อยู่จัดส่ง</Data></Cell>
        <Cell><Data ss:Type="String">ละติจูด ลองจิจูด</Data></Cell>
        <Cell><Data ss:Type="String">วันที่จัดส่ง</Data></Cell>
        <Cell><Data ss:Type="String">ผู้เปิดบิล</Data></Cell>
    </Row>`;

    // Adding data rows (without "เพิ่มเติม" column)
    const rows = data.reduce((acc, row) => {
    // เลือกเฉพาะคอลัมน์ที่ต้องการ (ในที่นี้คอลัมน์ที่ 2 และ 4)
    const selectedData = [row[1], row[2], row[3], row[4], row[5], row[6]];  // เลือกคอลัมน์ที่ 2 (รหัสลูกค้า) และคอลัมน์ที่ 4 (ที่อยู่จัดส่ง)

    // แปลงข้อมูลที่เลือกให้เป็น XML
    const rowData = selectedData.map(cell => 
        `<Cell><Data ss:Type="String">${cell}</Data></Cell>`
    ).join('');

    // เพิ่มแถวลงใน XML
    acc += `<Row>${rowData}</Row>`;
    return acc;
}, '');

    return xmlHeader + headerRow + rows + xmlFooter;
}

function searchTable() {
    let searchInput = document.getElementById("search-input").value.toLowerCase();
    let table = document.querySelector("table tbody");
    let rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        let row = rows[i];
        let cells = row.getElementsByTagName("td");

        // Get the content of the second column (บิลลำดับ)
        let soDetailId = cells[1] ? cells[1].textContent.toLowerCase() : '';

        // Search for the text inside the selected column (บิลลำดับ)
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