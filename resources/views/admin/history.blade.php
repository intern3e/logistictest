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
    </style>
</head>
<body>
    <div class="container">
        <h2>📜 ประวัติการออกเอกสาร</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>วันที่ออกเอกสาร</th>
                    <th>จำนวนรายการ</th>
                    <th>ดาวน์โหลด</th>
                </tr>
            </thead>
            <tbody id="historyTable">
                <!-- รายการเอกสารจะถูกเติมอัตโนมัติที่นี่ -->
            </tbody>
        </table>
        
        <a href="{{ route('admin.dashboardadmin') }}" ><button class="button">กลับไปหน้าหลัก</button></a>
    </div>

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

    function downloadDocument(index) {
        let history = JSON.parse(localStorage.getItem("documentHistory")) || [];
        if (!history[index]) return;

        let doc = history[index];
        let xml = createExcelXML(doc.data);
        let blob = new Blob([xml], { type: "application/vnd.ms-excel" });
        let link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = `เอกสาร-${doc.timestamp}.xls`;
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


    window.onload = loadHistory;
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

window.onload = loadHistory;
</script>

</body>
</html>
