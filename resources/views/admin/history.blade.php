<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติเอกสาร</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin: auto;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: white;
        }
        .table th, .table td {
            padding: 12px;
            border: 1px solid #dcdde1;
            text-align: center;
        }
        .table th {
            background: linear-gradient(to right, #2c3e50, #4b6584);
            color: white;
            font-weight: bold;
        }
        .table-striped tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
        .table-striped tr:hover {
            background-color: #ecf0f1;
        }
        .button {
            display: block;
            margin: 20px auto;
            background-color: #e74c3c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
        }
        .button:hover {
            background-color: #c0392b;
        }
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 900px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .close-btn {
            font-size: 24px;
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📜 ประวัติการออกเอกสาร</h2>
        <div class="button-group">
            <button onclick="exportToExcel()">🖨 ปริ้นเอกสาร</button>
        </div>
    </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ปริ้นเอกสาร</th>
                    <th>บิลลำดับ</th>
                    <th>รหัสลูกค้า</th>
                    <th>ที่อยู่จัดส่ง</th>
                    <th>ละติจูด ลองจิจูด</th>
                    <th>วันที่จัดส่ง</th>
                    <th>ผู้เปิดบิล</th>
                    <th>ข้อมูลสินค้า</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @foreach($bill as $item)
                    @if($item->status == 1)
                        <tr>
                            <td><input type="checkbox" class="form-control1" name="status[]"></td>
                            <td>{{ $item->so_detail_id }}</td>
                            <td>{{ $item->customer_id }}</td>
                            <td>{{ $item->customer_address }}</td>  
                            <td>{{ $item->customer_la_long }}</td>
                            <td>{{ $item->date_of_dali }}</td>
                            <td>{{ $item->emp_name }}</td>
                            <td>
                                <a href="javascript:void(0);" onclick="openPopup(
                                    '{{ $item->so_detail_id }}',
                                    '{{ $item->so_id }}', 
                                    '{{ $item->customer_id }}',
                                    '{{ $item->customer_address }}',
                                    '{{ $item->date_of_dali }}'
                                )">เพิ่มเติม</a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Popup -->
    <div class="popup-overlay" id="popup">
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
                    <tbody id="popup-body-1"></tbody>
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
                    <tbody id="popup-body"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Open Popup
        function openPopup(soDetailId, soId, customerId, customerAddress, dateOfDali) {
            document.getElementById("popup").style.display = "flex"; // Show Popup

            // Fill in basic information
            let popupBody = document.getElementById("popup-body-1");
            popupBody.innerHTML = `
                <tr>
                    <td>${soDetailId}</td>
                    <td>${customerId}</td>
                    <td>${customerAddress}</td>
                    <td>${dateOfDali}</td>
                </tr>
            `;

            // Fill in product details (with loading indicator)
            let secondPopupBody = document.getElementById("popup-body");
            secondPopupBody.innerHTML = "<tr><td colspan='4'>Loading...</td></tr>";

            // Fetch product details
            fetch(`/get-bill-detail/${soDetailId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        secondPopupBody.innerHTML = ""; // Clear previous data
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

        // Close Popup
        function closePopup() {
            document.getElementById("popup").style.display = "none"; // Hide Popup
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
            link.download = "ประวัติเอกสารจัดเตรียมสินค้า.xls";
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
    
    
        </script>
        
    
    
</body>
</html>
