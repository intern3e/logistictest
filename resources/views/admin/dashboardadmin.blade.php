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
        /* ทำให้ส่วนค้นหาและปุ่มอยู่บรรทัดเดียวกัน */
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
        .link {
            color: #16a085;
            font-weight: bold;
            text-decoration: none;
        }
        .link:hover {
            text-decoration: underline;
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

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>สถานะ</th>
                    <th>ID PO</th>
                    <th>รหัสลูกค้า</th>
                    <th>ที่อยู่จัดส่ง</th>
                    <th>วันที่</th>
                    <th>ข้อมูลสินค้า</th>
                </tr>
            </thead>
            <tbody id="table-body">        
            </tbody>
        </table>
    </div>

    <script>
    function exportToExcel() {
        let table = document.querySelector('.table');
        let selectedRows = [];

        table.querySelectorAll('tbody tr').forEach(row => {
            let checkbox = row.querySelector('input[type="checkbox"]');
            if (checkbox && checkbox.checked) {
                let rowData = [];
                row.querySelectorAll('td').forEach((cell, index) => {
                    if (index !== 0) rowData.push(cell.innerText.trim()); // ข้าม Checkbox
                });
                selectedRows.push(rowData);
            }
        });

        if (selectedRows.length === 0) {
            alert("กรุณาเลือกข้อมูลก่อนพิมพ์เอกสาร");
            return;
        }

        let xml = createExcelXML(selectedRows);
        let blob = new Blob([xml], { type: "application/vnd.ms-excel" });
        let link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "เอกสารจัดเตรียมสินค้า.xls";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function createExcelXML(data) {
        let xmlHeader = `<?xml version="1.0"?>
            <?mso-application progid="Excel.Sheet"?>
            <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                      xmlns:o="urn:schemas-microsoft-com:office:office"
                      xmlns:x="urn:schemas-microsoft-com:office:excel"
                      xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
                      xmlns:html="http://www.w3.org/TR/REC-html40">
            <Worksheet ss:Name="Sheet1">
            <Table>`;

        let xmlFooter = `</Table></Worksheet></Workbook>`;

        let headers = ["รหัสลูกค้า", "ที่อยู่จัดส่ง", "วันที่", "ข้อมูลสินค้า"];
        let headerRow = `<Row>` + headers.map(header => `<Cell><Data ss:Type="String">${header}</Data></Cell>`).join("") + `</Row>`;

        let rows = data.map(row => {
            return `<Row>` + row.map(cell => `<Cell><Data ss:Type="String">${cell}</Data></Cell>`).join("") + `</Row>`;
        }).join("");

        return xmlHeader + headerRow + rows + xmlFooter;
    }
    </script>
    <script>
function loadHistory() {
    let history = JSON.parse(localStorage.getItem("documentHistory")) || [];
    let tableBody = document.getElementById("historyTable");

    if (history.length === 0) {
        tableBody.innerHTML = "<tr><td colspan='4'>ไม่มีประวัติเอกสาร</td></tr>";
        return;
    }

    tableBody.innerHTML = "";
    history.forEach((doc, index) => {
        let row = `<tr>
            <td>${index + 1}</td>
            <td>${doc.timestamp}</td>
            <td>${doc.data.length} รายการ</td>
            <td><button onclick="downloadDocument(${index})">⬇️ ดาวน์โหลด</button></td>
        </tr>`;
        tableBody.innerHTML += row;
    });
}

function generateRows() {
        let tbody = document.getElementById("table-body");
        let content = "";
        
        for (let i = 0; i < 60; i++) {
            content += `
            <tr>
                    <td><input type="checkbox"></td>
                    <td>12345</td>
                    <td>ณฏ67890</td>
                    <td>58/9 ต.บางกระเจ้า อ.พระประแดง จ.สมุทรปราการ</td>
                    <td>30/1/2567</td>
                    <td><a href="#" class="link">เพิ่มเติม</a></td>
                </tr>`;
        }
        
        tbody.innerHTML = content;
    }
    
    // เรียกใช้ฟังก์ชันเพื่อเพิ่มแถวในตาราง
    generateRows();


window.onload = loadHistory;
</script>

</body>
</html>
