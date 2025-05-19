<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>ระบบจัดเส้นทางบิลชั่วคราว</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
 <link rel="stylesheet" href="{{ asset('css/admindocroute.blade.css') }}">


</head>
<body>
    <div class="header">
        <h2>ระบบจัดเส้นทางบิลชั่วคราว</h2>
        <a href="adminSO"><button class="btn-so">หน้าหลัก</button></a>
    </div>
    <div class="container">
        <div class="top-section">
            <div class="button-group">
                <button onclick="createJSON()">ดาวน์โหลด JSON</button>
                <button onclick="window.location.href='historydoc'">📜 ประวัติเอกสาร</button>
            </div>
            <div class="search-box">
        
        </div>
        
        </div>
        <div class="table-container">
    <table>
        <input type="checkbox" id="checkAll" onclick="toggleCheckboxes()"> ทั้งหมด
        <thead>
            <tr>
                <th>ปริ้นเอกสาร</th>
                <th>เลขที่บิล</th>
                <th>บริษัท</th>
                <th>ที่อยู่</th>
                <th>ละติจูด ลองจิจูด</th>
                <th>ผู้ติดต่อ</th>
                <th>เบอร์โทร</th>
                <th>ประเภทงาน</th>
                <th>ผู้เปิดบิล</th>
                <th>วันที่</th     >
                <th>ข้อมูลรายละเอียด</th>
         
            </tr>
        </thead>
        <tbody id="table-body">
            @foreach($docbill as $item)
            @if($item->status == 0 && $item->statuspdf == 1)
            <tr>
                <td>
                <input type="checkbox" class="form-control1" name="status[]" data-doc-id="{{ $item->doc_id }}">

                </td>
                <td>{{ $item->doc_id }}</td>
                <td>{{ $item->com_name }}</td>
                <td>{{ $item->com_address }}</td>
                <td>
                    @php
                        $text = $item->com_la_long;
                        $split_text = str_split($text, 40); // แบ่งข้อความเป็นชิ้น ๆ ที่ไม่เกิน 40 ตัว
                    @endphp
                    @foreach ($split_text as $line)
                        {{ $line }}<br> <!-- แสดงแต่ละบรรทัด -->
                    @endforeach
                </td>
                <td>{{ $item->contact_name }}</td>
                <td>{{ $item->contact_tel}}</td>
                <td>{{ $item->doctype }}</td>
                <td>{{ $item->emp_name }}</td>
                <td>{{ \Carbon\Carbon::parse($item->time)->format('d/m/Y') }}</td>
                <td>
                <a href="javascript:void(0);" onclick="openPopup('{{ $item->doc_id }}', '{{ $item->com_name }}', '{{ $item->com_address }}', '{{ $item->contact_name }}', '{{ $item->contact_tel }}', '{{ $item->amount }}', '{{ $item->notes }}')">
                    เพิ่มเติม
                </a>

                </td>
            
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
                                <th>บริษัท</th>
                                <th>ที่อยู่</th>
                                <th>ผู้ติดต่อ</th>
                                <th>เบอร์โทร</th>
                                <th>รวมทั้งหมด</th>
                            </tr>
                        </thead>
                        <tbody id="popup-body-1">
                        </tbody>
                    </table>
                    <br>
                    <table>
                <thead>     
                    <tr>
                        <th>ลำดับ</th>
                        <th>รายการ</th>
                        <th>จำนวน</th>
                        <th>ราคา/หน่วย</th>
                    </tr>
                </thead>
                <tbody id="popup-body">
                </tbody>
            </table>
             <br>
                <textarea id="popup-body-3" readonl style="width: 950px; height: 70px;" readonly>
                </textarea>
        </div> 
    </div>
</div>
        
        <script>
            function openPopup(doc_id,com_name,com_address,contact_name,contact_tel,amount,notes) {
                document.getElementById("popup").style.display = "flex"; // แสดง Popup
            
                let popupBody = document.getElementById("popup-body-1");
                popupBody.innerHTML = `
                    <tr>
                        <td>${doc_id}</td>
                        <td>${com_name}</td>
                        <td>${com_address}</td>
                        <td>${contact_name}</td>
                        <td>${contact_tel}</td>
                        <td>${amount}</td>
                    </tr>
                `;
                document.getElementById("popup-body-3").value = notes;
                let secondPopupBody = document.getElementById("popup-body");
                secondPopupBody.innerHTML = "<tr><td colspan='4'>กำลังโหลดข้อมูล...</td></tr>";
                
                // ดึงข้อมูลรายการสินค้าจาก Laravel Controller
                fetch(`/get-docbill-detail/${doc_id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            secondPopupBody.innerHTML = ""; // เคลียร์ข้อมูลเก่า
                            data.forEach((item, index) => {
                            secondPopupBody.insertAdjacentHTML("beforeend", `
                                <tr>
                                    <td>${index + 1}</td>
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
                        secondPopupBody.innerHTML = "<tr><td colspan='4'>เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>";
                    });
            }

    // ฟังก์ชันปิด Popup
    function closePopup() {
        document.getElementById("popup").style.display = "none"; // ซ่อน Popup
    }

            
            window.onclick = function(event) {
                let popup = document.getElementById("popup");
                if (event.target === popup) {
                    closePopup();
                }
            }
            </script>
    
    
    <script>
    
    function updateStatus(docDetailIds) {
        fetch('/update-statusdoc', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ docDetailIds: docDetailIds }) 
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
                console.log("Status updated successfully");
            } else {
                console.error("Failed to update status");   
            }
        })
        .catch(error => {
            console.error("Error updating status:", error);
        });
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
    
    <script>
    
    function createJSON() {
        let jsonData = [];
        let selectedDocIds = []; // เปลี่ยนชื่อ array

        let checkboxes = document.querySelectorAll("input[type='checkbox']:checked");

        checkboxes.forEach(checkbox => {
            let row = checkbox.closest("tr");
            if (!row) return;

            let cells = row.querySelectorAll("td");

            let billNo        = cells[1].textContent.trim(); // doc_id
            let customerName  = cells[2].textContent.trim(); // com_name
            let address       = cells[3].textContent.trim(); // com_address
            let latlong       = cells[4].textContent.trim(); // com_la_long
            let contact_name  = cells[5].textContent.trim(); // contact_name
            let phone         = cells[6].textContent.trim(); // contact_tel
            let empName       = cells[8].textContent.trim(); // emp_name
            let orderDate     = cells[9].textContent.trim(); // time

            let [lat, lng] = latlong.split(",").map(val => parseFloat(val.trim()));

            let order = {
                orderNo: `${billNo},(${empName}),(${contact_name})`,
                date: formatDate(orderDate),
                phone: phone,
                type:"D",
                location: {
                    address: address,
                    locationName: `${customerName} (${phone})`,
                    latitude: lat,
                    longitude: lng
                }
            };

            // เปลี่ยนจาก data-so-detail-id เป็น data-doc-id
            let docId = checkbox.getAttribute("data-doc-id");
            if (docId) {
                selectedDocIds.push(docId);
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

        const filename = `เอกสารเส้นทางเดินรถของบิลชั่วคราว_${formattedDate}.json`;

        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        // เรียกฟังก์ชันอัปเดต status โดยใช้ selectedDocIds
        if (selectedDocIds.length > 0) {
            updateStatus(selectedDocIds);
        }
    }
    function formatDate(input) {
    let [d, m, y] = input.split("/");
    return `${y}-${m}-${d}`;
}
</script>

<script>
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



