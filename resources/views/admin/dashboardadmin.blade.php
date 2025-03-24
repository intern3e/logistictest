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
    background-color: #F5F5F7;
    color: #1D1D1F;
    margin: 0;
    padding: 0;
}

.header {
    background: linear-gradient(to right, #2c3e50, #4b6584);
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

.header-buttons {
    display: flex;
    gap: 10px;
    margin-left: auto;
}

.header-buttons button {
    padding: 15px 20px;
    font-size: 16px;
    cursor: pointer;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-right: 10px;
}

.btn-po {
    background-color: #0071E3;
    color: white;
}

.btn-so {
    background-color: red;
    color: white;
}

.header-buttons button:hover {
    transform: scale(1.05);
}

.btn-po:hover {
    background-color: #005BB5;
}

.btn-so:hover {
    background-color: rgb(179, 1, 1);
}

.top-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0 auto;
    margin-bottom: 15px;
    gap: 20px;
    width: 90%;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.filter-form {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-form label {
    font-weight: bold;
    color: #1D1D1F;
    font-size: 1rem;
}

.filter-form input[type="date"] {
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #00000a;
    background: #ffffffa4;
    color: #000;
    font-size: 1rem;
}

.filter-form button {
    padding: 8px 12px;
    border: none;
    background: #0071E3;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
    font-size: 1rem;
}

.filter-form button:hover {
    background: #005BB5;
}

.search-box {
    display: flex;
    max-width: 300px;
    width: 100%;
    margin-left: auto;
}

.search-box input {
    flex-grow: 1;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #000000;
    background-color: #ffffff;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.search-box input:focus {
    border-color: #0071E3;
    outline: none;
}

.search-box input::placeholder {
    color: #888;
    font-size: 0.9rem;
}

.button-group {
    display: flex;
    gap: 15px;
    align-items: center;
}

.button-group label {
    font-weight: bold;
    font-size: 1rem;
}

.button-group button {
    padding: 15px 20px;
    border-radius: 8px;
    font-weight: bold;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #ff9d2d;
    color: rgb(255, 255, 255);
}

.button-group button:hover {
    background-color: #b37005;
    transform: scale(1.05);
}

.button-group a button {
    background-color: #0071E3;
    color: white;
}

.button-group a button:hover {
    background-color: #005BB5;
}

.button-group a:last-child button {
    background-color: red;
}

.button-group a:last-child button:hover {
    background-color: #ad0404;
}

.search-box {
    display: flex;
    align-items: center;
    max-width: 250px;
}

.search-box input {
    flex-grow: 1;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #6E6E73;
    background-color: #FFFFFF;
}

.table-container {
    background: white;
    margin: 0 5%;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    overflow-x: auto; /* ให้ตารางเลื่อนในแนวนอนได้ */
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
    max-width: 100px; /* กำหนดความกว้างสูงสุด */
    word-wrap: break-word; /* ถ้าข้อความยาวเกินจะขึ้นบรรทัดใหม่ */
    word-break: break-word; /* หักคำเมื่อข้อความยาวเกิน */
}

th {
    background-color: #0071E3;
    color: white;
    text-transform: uppercase;
}

.table-striped tr:nth-child(odd) {
    background-color: #F5F5F7;
}

.table-striped tr:hover {
    background-color: #E5E5E7;
}



.link {
    color: #0071E3;
    font-weight: bold;
    text-decoration: none;
}

.link:hover {
    text-decoration: underline;
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

.table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin: 30px;
}

@media (max-width: 768px) {
    .header, .top-section, .filter-form, .button-group {
        flex-direction: column;
        align-items: stretch;
        width: 100%;
        gap: 10px;
    }

    .header-buttons {
        width: 100%;
        margin-left: 0;
    }

    .header-buttons button {
        width: 100%;
        padding: 12px 0;
        font-size: 14px;
    }

    table {
        width: 100%;
    }

    th, td {
        font-size: 12px;
        padding: 8px;
    }

    .search-box {
        max-width: 100%;
    }

    .button-group button {
        width: 100%;
        padding: 12px 0;
    }
}

@media (max-width: 480px) {
    th, td {
        font-size: 10px;
        padding: 4px;
    }

    /* Hide some columns if necessary */
    td:nth-child(10), td:nth-child(11), td:nth-child(12) {
        display: none;
    }

    .search-box input {
        font-size: 0.9rem;
    }

    .popup-content {
        width: 90%;
        padding: 10px;
    }
}
    
        
    </style>
</head>
<body>
    <div class="header">
        <h2>ระบบจัดเตรียมเส้นทางรถของบิลSO</h2>
        <div class="header-buttons">
            <a href="adminpo"><button class="btn-po">ระบบจัดเตรียมรถรับของPO</button></a>
            <a href="adminSO"><button class="btn-so">หน้าหลัก</button></a>
        </div>
    </div>
    


    <div class="top-section">
        <form method="GET" action="{{ route('admin.dashboardadmin') }}" class="filter-form">
            <label style="color: rgb(0, 0, 0)" for="date">📅 วันที่:</label>
            <input type="date" id="date" name="date" value="{{ request('date') }}">
            <button type="submit">ค้นหา</button>
        </form>
    
        <div class="search-box">
            <input type="text" id="search-input" placeholder=" ค้นหา เลขที่บิล" onkeyup="searchTable()">
        </div>
    
        <div class="button-group">
            <button onclick="exportToExcel()">🖨 ปริ้นเอกสารเส้นทางการเดินรถ</button>
            <a href="history"><button>📜 ประวัติเอกสาร</button></a>
            <a href="dashboardadminpdf"><button>ปริ้นเอกสารSO</button></a>
        </div>
    </div>
    
        
        <div class="table-container">
            <table>
                <thead>
                <label>
                <input type="checkbox" id="checkAll" onclick="toggleCheckboxes()"> ทั้งหมด
            </label>
                    <tr>
                        <th>ปริ้นเอกสาร</th>
                        <th>เลขที่บิล</th>
                        <th>อ้างอิงใบสั่งขาย</th>
                        <th>อ้างอิงใบสั่งซื้อ</th>
                        <th>ชื่อลูกค้า</th>
                        <th>เบอร์ติดต่อ</th>
                        <th>ที่อยู่จัดส่ง</th>
                        <th>ละติจูด ลองจิจูด</th>
                        <th>วันที่จัดส่ง</th>
                        <th>ผู้เปิดบิล</th>
                        <th>แจ้งเพิ่มเติม</th>
                        <th>ข้อมูลสินค้า</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($bill as $item)
                        @if($item->status == 0 && $item->statuspdf == 1)
                            <tr>
                                <td><input type="checkbox" class="form-control1" name="status[]"></td>
                                <td>{{ $item->so_detail_id }}</td>
                                <td>{{ $item->so_id }}</td>
                                <td>{{ $item->ponum }}</td>
                                <td>{{ $item->customer_name }}</td>
                                <td>{{ $item->customer_tel }}</td>  
                                <td>{{ $item->customer_address }}</td>
                                <td>{{ $item->customer_la_long }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') }}</td> 
                                <td>{{ $item->emp_name }}</td>
                                <td>{{ $item->notes }}</td>
                                <td><a href="javascript:void(0);" 
                                onclick="openPopup(
                                    '{{ $item->so_detail_id }}',
                                    '{{ $item->so_id }}',
                                    '{{ $item->ponum }}',
                                    '{{ $item->customer_name }}',
                                    '{{ $item->customer_tel }}',
                                    '{{ $item->customer_address }}',
                                    '{{ $item->date_of_dali }}',
                                    '{{ $item->sale_name }}'
                                )">
                                เพิ่มเติม
                             </a></td>
                            </tr>
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
                        <th>เลขที่บิล</th>
                        <th>อ้างอิงใบสั่งขาย</th>
                        <th>อ้างอิงใบสั่งซื้อ</th>
                        <th>ชื่อลูกค้า</th>
                        <th>เบอร์โทร</th>
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
        <Cell><Data ss:Type="String">เลขที่บิล</Data></Cell>
        <Cell><Data ss:Type="String">อ้างอิงใบสั่งขาย</Data></Cell>
        <Cell><Data ss:Type="String">อ้างอิงใบสั่งซื้อ</Data></Cell>
        <Cell><Data ss:Type="String">ชื่อลูกค้า</Data></Cell>
        <Cell><Data ss:Type="String">เบอร์ติดต่อ</Data></Cell>
        <Cell><Data ss:Type="String">ที่อยู่จัดส่ง</Data></Cell>
        <Cell><Data ss:Type="String">ละติจูด ลองจิจูด</Data></Cell>
        <Cell><Data ss:Type="String">วันที่จัดส่ง</Data></Cell>
        <Cell><Data ss:Type="String">ผู้เปิดบิล</Data></Cell>
        <Cell><Data ss:Type="String">แจ้งเพิ่มเติม</Data></Cell>
    </Row>`;

    // Adding data rows (without "เพิ่มเติม" column)
    const rows = data.reduce((acc, row) => {
    // เลือกเฉพาะคอลัมน์ที่ต้องการ (ในที่นี้คอลัมน์ที่ 2 และ 4)
    const selectedData = [row[1], row[2], row[3], row[4], row[5], row[6], row[7], row[8], row[9], row[10]];  

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


function updateStatus(soDetailIds) {
    console.log("Updating status for:", soDetailIds); // เพิ่ม log เช็คค่า
    fetch('/update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ soDetailIds: soDetailIds })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Response:", data);
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

function toggleCheckboxes() {
    var checkAllBox = document.getElementById('checkAll');
    var checkboxes = document.querySelectorAll('input[type="checkbox"]:not(#checkAll)');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = checkAllBox.checked;
    });
}

    </script>



</body>
</html>