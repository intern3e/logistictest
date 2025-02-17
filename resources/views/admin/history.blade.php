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
                    @if($item->status == 1)
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
