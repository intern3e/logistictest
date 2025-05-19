<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดเตรียมสินค้า</title>
    <link rel="stylesheet" href="{{ asset('css/adminpo.blade.css') }}">
  
</head>
<body>
    <div class="header">
        <h2>ระบบจัดเตรียมรถรับของPO</h2>
        <div class="header-buttons">
            <a href="admindocroute"><button class="btn-po">ระบบเส้นทางเอกสารเพิ่มเติม</button></a>
            <a href="adminSO"><button class="btn-so">หน้าหลัก</button></a>
        </div>
    </div>
    <div class="top-section">
            <div class="search-box">
            </div>
            <div class="button-group">
                <button id="printroutepojson" onclick="createJSON()">ดาวน์โหลด เส้นทาง</button>
                <button onclick="window.location.href='historypo'">📜 ประวัติเอกสาร</button>
            </div>
            
        
        </div>
  
       <div class="table-container">
    <table>
        <input type="checkbox" id="checkAll" onclick="toggleCheckboxes()"> ทั้งหมด
        <thead>
            <tr>
                <th>ปริ้นเอกสาร</th>
                <th>เลขอ้างอิงใบรับสินค้า</th>
                <th>เลขที่บิล</th>
                <th>ชื่อร้านค้า</th>
                <th>ที่อยู่ร้านค้า</th>
                <th>เบอร์โทร</th>
                <th>ละติจูดลองจิจูด</th>
                <th>วันที่รับสินค้า</th>
                <th>ผู้เปิดบิล</th>
                <th>เบอร์โทร</th>
                <th>ข้อมูลสินค้า</th>
            </tr>
        </thead>
        <tbody id="table-body">
            @foreach($pobill as $item)
                @if($item->status == 0)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-control1" name="status[]" data-po-detail-id="{{ $item->po_detail_id }}">
                        </td>
                        <td>{{ $item->po_id }}</td>
                        <td>{{ $item->po_detail_id }}</td>
                        <td>{{ $item->store_name }}</td>
                        <td>{{ $item->store_address }}</td>  
                        <td>{{ $item->store_tel }}</td>  
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
                        <td><a href="javascript:void(0);" 
                            onclick="openPopup(
                                '{{ $item->po_id }}',
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
                        <th>เลขอ้างอิงใบรับสินค้า</th>
                        <th>เลขที่บิล</th>
                        <th>รหัสลูกค้า</th>
                        <th>ที่อยู่จัดส่ง</th>
                        <th>วันที่จัดส่ง</th>
                        <th>ผุ้ขาย</th>
                        <th>ประเภทรถ</th>
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
                    </tr>
                </thead>
                <tbody id="popup-body">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function openPopup(po_id,po_detail_id, store_name, store_address, recvDate, emp_name, cartype) {
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
        let typeCell = rows[i].getElementsByTagName("td")[8]; // เปลี่ยน index ให้ตรงกับ "ประเภทขนส่ง"
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
function createJSON() {
    let jsonData = [];
    let selectedPoDetailIds = [];

    let checkboxes = document.querySelectorAll("input[type='checkbox']:checked:not(#checkAll)");

    checkboxes.forEach(checkbox => {
        let row = checkbox.closest("tr");
        if (!row) return;

        let cells = row.querySelectorAll("td");
        let billNo       = cells[1].textContent.trim(); // po_id
        let orderDate    = cells[7].textContent.trim(); // recvDate
        let phone        = cells[5].textContent.trim(); // store_tel
        let address      = cells[4].textContent.trim(); // store_address
        let customerName = cells[3].textContent.trim(); // store_name
        let latlong      = cells[6].textContent.trim(); // store_la_long
        let empName      = cells[8].textContent.trim(); 


        // แยกละติจูดกับลองจิจูด
        let [lat, lng] = latlong.split(",").map(val => parseFloat(val.trim()));

        let order = {
            orderNo: `${billNo},(${empName})`,
            date: formatDate(orderDate),
            phone: phone,
            type:"P",
            location: {
                address: address,
                locationName: `${customerName} (${phone})`,
                latitude: lat,
                longitude: lng
            }
        };

        // อ่าน po_detail_id จาก attribute
        let poDetailId = checkbox.getAttribute("data-po-detail-id");
        if (poDetailId) {
            selectedPoDetailIds.push(poDetailId);
        }

        jsonData.push(order);
    });

    if (jsonData.length === 0) {
        alert("กรุณาเลือกข้อมูลที่ต้องการพิมพ์ JSON");
        return;
    }

    const output = { orders: jsonData };
    const jsonContent = JSON.stringify(output, null, 2);
    const blob = new Blob([jsonContent], { type: "application/json;charset=utf-8;" });

    let now = new Date();
    let day = String(now.getDate()).padStart(2, "0");
    let month = String(now.getMonth() + 1).padStart(2, "0");
    let year = now.getFullYear();
    let formattedDate = `${day}-${month}-${year}`;

    const filename = `เอกสารเส้นทางเดินรถของPO_${formattedDate}.json`;
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);

    if (selectedPoDetailIds.length > 0) {
        updateStatus(selectedPoDetailIds);
    }
}

function formatDate(input) {
    let [d, m, y] = input.split("/");
    return `${y}-${m}-${d}`;
}

function updateStatus(poDetailIds) {
    console.log("Updating status for:", poDetailIds);
    fetch('/update-statuspo', {
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
            location.reload();
        } else {
            console.error("Failed to update status");
        }
    })
    .catch(error => {
        console.error("Error updating status:", error);
    });
}

function toggleCheckboxes() {
    const checkAllBox = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('input[type="checkbox"]:not(#checkAll)');
    checkboxes.forEach(cb => cb.checked = checkAllBox.checked);
}

function searchTable() {
    let searchInput = document.getElementById("search-input").value.toLowerCase();
    let rows = document.querySelectorAll("table tbody tr");

    rows.forEach(row => {
        let poDetailId = row.cells[1]?.textContent.toLowerCase() || '';
        row.style.display = poDetailId.includes(searchInput) ? "" : "none";
    });
}

function sortTableDescending() {
    let tbody = document.querySelector("table tbody");
    let rows = Array.from(tbody.querySelectorAll("tr"));

    rows.sort((a, b) => {
        let idA = parseInt(a.cells[2].textContent.trim());
        let idB = parseInt(b.cells[2].textContent.trim());
        return idB - idA;
    });

    rows.forEach(r => tbody.appendChild(r));
}

window.onload = function() {
    sortTableDescending();
};
</script>
</body>
</html>