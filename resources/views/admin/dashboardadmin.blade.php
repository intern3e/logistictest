<!DOCTYPE html>
<html lang="th">
<head>
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
        .header button {
            background-color: #e74c3c;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        .header button:hover {
            background-color: #c0392b;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 90%;
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
            background-color: #f39c12;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .button-group button:hover {
            background-color: #e67e22;
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
            background: linear-gradient(to right, #2c3e50, #4b6584);
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
    </style>
</head>
<body>
    <div class="header">
        <h2>ระบบจัดเตรียมสินค้า</h2>
    </div>

    <div class="container">
        <div class="top-section">
            <div class="filter-container">
                <label for="date">📅 วันที่:</label>
                <input type="date" id="date" name="date">
            </div>

            <div class="button-group">
                <button onclick="exportToExcel()">🖨 ปริ้นเอกสาร</button>
                <button onclick="window.location.href='history'">📜 ประวัติเอกสาร</button>
            </div>
        </div>


        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>สถานะ</th>
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
                        @if($item->status == 0)
                            <tr>
                                <td><input type="checkbox" class="form-control1" name="status[]"></td>
                                <td>{{ $item->so_detail_id }}</td>
                                <td>{{ $item->customer_id }}</td>
                                <td>{{ $item->customer_address }}</td>  
                                <td>{{ $item->customer_la_long }}</td>
                                <td>{{ $item->date_of_dali }}</td>
                                <td>{{ $item->emp_name }}</td>
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
                        @endif
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

        // Update the status of checked rows
        checkedRows.forEach(row => {
            let statusCell = row.querySelector("td:first-child");
            if (statusCell) {
                statusCell.innerHTML = "✅ พิมพ์แล้ว";
            }
        });

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
        const rowData = row.map(cell => 
            `<Cell><Data ss:Type="String">${cell}</Data></Cell>`
        ).join('');
        acc += `<Row>${rowData}</Row>`;
        return acc;
    }, '');

    return xmlHeader + headerRow + rows + xmlFooter;
}



    </script>
    


</body>
</html>