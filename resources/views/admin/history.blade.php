<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    padding: 1px 30px;
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
    padding: 8px 8px;
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
    padding: 10px 20px;
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
            background: #f9f9f9; /* Light gray background for table */
            margin: 2% 5%;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 100%; /* Ensure table doesn't overflow the container */
            transform: scale(0.9); /* Scale down the table to fit the screen */
            transform-origin: top left; /* Keep the table scaling from the top-left corner */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            word-wrap: break-word; /* Ensure text wraps within table cells */
            font-size: 1rem; /* Adjust the font size to make it smaller */
        }

th, td {
    padding: 8px;
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
        <h2>ประวัติเส้นทางรถของบิลSO</h2>
        <div class="header-buttons">
            <a href="adminSO"><button class="btn-so">หน้าหลัก</button></a>
        </div>
    </div>
    


    <div class="top-section">
        <form method="GET" action="{{ route('admin.history') }}" class="filter-form" id="autoSearchForm">
            <label for="date">📅 วันที่: เดือน / วัน / ปี</label>
            <input type="date" id="date" name="date" value="{{ request('date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
            <button type="submit" style="display: none;">ค้นหา</button>
            
        </form> 
            <div class="button-group">
                <button id="summitbackso" onclick="updateStatuspdfback()" style="background-color: orange;">คืนสถานะงาน</button>
                <a href="adminroute">
                    <button>ตรวจสอบเอกสาร</button>
                </a>
            </div>
        
    <script>
        const form = document.getElementById('autoSearchForm');
        const dateInput = document.getElementById('date');
    
        // ส่งฟอร์มเมื่อเปลี่ยนวันที่
        dateInput.addEventListener('change', () => {
            form.submit();
        });
    
        // ส่งฟอร์มอัตโนมัติเมื่อเข้าหน้าเว็บครั้งแรกเท่านั้น
        window.addEventListener('load', () => {
            if (!sessionStorage.getItem('hasAutoSubmitted')) {
                sessionStorage.setItem('hasAutoSubmitted', 'true');
                form.submit();
            }
        });
    </script>
    
    
        <div class="search-box">
            <input type="text" id="search-input" placeholder=" ค้นหา เลขที่บิล" onkeyup="searchTable()">
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
                        <th>REF</th>
                        <th>อ้างอิงใบสั่งขาย</th>
                        <th>อ้างอิงใบสั่งซื้อ</th>
                        <th>ชื่อลูกค้า</th>
                        <th>เบอร์ติดต่อ</th>
                        <th>วันที่จัดส่ง</th>
                        <th>ผู้เปิดบิล</th>
                        <th>ข้อมูลสินค้า</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach($bill as $item)
                        @if($item->status == 1 && $item->statuspdf == 2)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-control1" name="status[]" data-so-detail-id="{{ $item->so_detail_id }}">
                                </td>
                                <td>{{ $item->so_detail_id }}</td>
                                <td>{{ $item->so_id }}</td>
                                <td>{{ $item->ponum }}</td>
                                <td>{{ $item->customer_name }}</td>
                                <td>{{ $item->customer_tel }}</td>  
                                <td>
                                    <div class="date-container">
                                        <span class="date-display" id="date-display-{{ $item->so_detail_id }}">
                                            {{ \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') }}
                                        </span>
                                        <div class="date-edit-form" id="date-edit-form-{{ $item->so_detail_id }}" style="display:none;">
                                            <input type="date" class="form-control form-control-sm" id="new-date-{{ $item->so_detail_id }}" 
                                                value="{{ \Carbon\Carbon::parse($item->date_of_dali)->format('Y-m-d') }}"
                                                onchange="validateDateFormat(this)">
                                            <div class="mt-1">
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="saveNewDate('{{ $item->so_detail_id }}')">บันทึก</button>
                                                <button type="button" class="btn btn-sm btn-secondary" 
                                                        onclick="cancelEdit('{{ $item->so_detail_id }}')">ยกเลิก</button>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-primary edit-date-btn" 
                                                id="edit-btn-{{ $item->so_detail_id }}"
                                                onclick="showEditForm('{{ $item->so_detail_id }}')">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </button>
                                    </div>
                                </td>
                                <td>{{ $item->emp_name }}</td>
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
        <script>
            function showEditForm(soDetailId) {
                // Hide the display elements and show the edit form
                document.getElementById('date-display-' + soDetailId).style.display = 'none';
                document.getElementById('edit-btn-' + soDetailId).style.display = 'none';
                document.getElementById('date-edit-form-' + soDetailId).style.display = 'block';
            }
            
            function cancelEdit(soDetailId) {
                // Show the display elements and hide the edit form
                document.getElementById('date-display-' + soDetailId).style.display = 'inline';
                document.getElementById('edit-btn-' + soDetailId).style.display = 'inline-block';
                document.getElementById('date-edit-form-' + soDetailId).style.display = 'none';
            }
        
            // ตรวจสอบรูปแบบวันที่
            function validateDateFormat(inputElement) {
                let dateValue = inputElement.value;
                
                // ถ้าค่าไม่ถูกต้องหรือไม่มีค่า
                if (!dateValue || dateValue.includes('undefined')) {
                    // กำหนดค่าเป็นวันที่ปัจจุบัน
                    let today = new Date();
                    let yyyy = today.getFullYear();
                    let mm = String(today.getMonth() + 1).padStart(2, '0');
                    let dd = String(today.getDate()).padStart(2, '0');
                    inputElement.value = yyyy + '-' + mm + '-' + dd;
                }
            }
        
            function saveNewDate(soDetailId) {
                let newDate = document.getElementById('new-date-' + soDetailId).value;
        
                // ตรวจสอบรูปแบบวันที่ก่อนส่งข้อมูล
                if (!newDate || newDate === 'undefined') {
                    alert('กรุณาระบุวันที่ให้ถูกต้อง');
                    return;
                }
        
                // Make an AJAX request to update the date
                $.ajax({
                    url: "{{ route('update.delivery.date') }}", 
                    method: "POST",
                    data: {
                        so_detail_id: soDetailId,
                        new_date: newDate,
                        _token: "{{ csrf_token() }}"
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Format the date for display (d/m/Y)
                            let formattedDate = formatDate(newDate);
                            
                            // Update the displayed date
                            document.getElementById('date-display-' + soDetailId).innerText = formattedDate;
                            
                            // Show success message
                            alert("วันที่ถูกอัพเดทเรียบร้อยแล้ว");

                            
                            // Switch back to display mode
                            cancelEdit(soDetailId);
                            window.location.reload()
                        } else {
                            alert("เกิดข้อผิดพลาด: " + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert("เกิดข้อผิดพลาด: " + xhr.responseText);
                    }
                });
            }
            
            // Helper function to format date as d/m/Y
            function formatDate(dateString) {
                if (!dateString || dateString === 'undefined') {
                    return '';
                }
                
                try {
                    // ถ้าวันที่เป็นรูปแบบ YYYY-MM-DD
                    if (dateString.includes('-') && dateString.split('-').length === 3) {
                        let parts = dateString.split('-');
                        let day = parts[2].padStart(2, '0');
                        let month = parts[1].padStart(2, '0');
                        let year = parts[0];
                        return day + '/' + month + '/' + year;
                    }
                    
                    let date = new Date(dateString);
                    if (isNaN(date.getTime())) { // ตรวจสอบว่าวันที่ถูกต้อง
                        return dateString;
                    }
                    
                    let day = String(date.getDate()).padStart(2, '0');
                    let month = String(date.getMonth() + 1).padStart(2, '0');
                    let year = date.getFullYear();
                    return day + '/' + month + '/' + year;
                } catch (e) {
                    console.error("Error formatting date:", e);
                    return dateString;
                }
            }
        </script>
<!-- Popup -->
<div class="popup-overlay" id="popup" style="display: none;">
    <div class="popup-content">
        <span class="close-btn" onclick="closePopup()">&times;</span>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>REF</th>
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

    function createJSON() {
        let jsonData = [];
        let selectedSoDetailIds = [];
    
        let checkboxes = document.querySelectorAll("input[type='checkbox']:checked");
    
        checkboxes.forEach(checkbox => {
            let row = checkbox.closest("tr");
            if (!row) return;
    
            let cells = row.querySelectorAll("td");
    
            // ดึงค่าจากเซลล์ตาม index (อย่าลืมเช็กว่า index ตรงกับตารางจริง)
            let billNo        = cells[4].textContent.trim(); // เลขที่บิล
            let orderDate     = cells[9].textContent.trim(); // วันที่จัดส่ง
            let phone         = cells[6].textContent.trim(); // เบอร์ติดต่อ
            let address       = cells[7].textContent.trim(); // ที่อยู่จัดส่ง
            let customerName  = cells[5].textContent.trim(); // ชื่อลูกค้า
            let latlong       = cells[8].textContent.trim(); // ละติจูด ลองจิจูด
    
            // แยกละติจูดกับลองจิจูด
            let [lat, lng] = latlong.split(",").map(val => parseFloat(val.trim()));
    
            let order = {
                orderNo: billNo,
                date: formatDate(orderDate),
                phone: phone,
                location: {
                    address: address,
                    locationName: `${customerName} (${phone})`,
                    latitude: lat,
                    longitude: lng
                }
            };
    
            let soDetailId = checkbox.getAttribute("data-so-detail-id");
            if (soDetailId) {
                selectedSoDetailIds.push(soDetailId);
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
    
        const filename = `เอกสารเส้นทางเดินรถของSO_${formattedDate}.json`;
    
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    
        if (selectedSoDetailIds.length > 0) {
            updateStatus(selectedSoDetailIds);
        }
    }
    
    // ฟังก์ชันแปลงวันที่จาก DD/MM/YYYY → YYYY-MM-DD
    function formatDate(input) {
        let [d, m, y] = input.split("/");
        return `${y}-${m}-${d}`;
    }
    

function searchTable() {
    let searchInput = document.getElementById("search-input").value.toLowerCase();
    let table = document.querySelector("table tbody");
    let rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        let row = rows[i];
        let cells = row.getElementsByTagName("td");

        // Get the content of the second column (บิลลำดับ)
        let soDetailId = cells[2] ? cells[1].textContent.toLowerCase() : '';

        // Search for the text inside the selected column (บิลลำดับ)
        if (soDetailId.indexOf(searchInput) > -1) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    }
}


function updateStatus(soDetailIds) {
    console.log("Updating status for:", soDetailIds); 
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
            location.reload();
        } else {
            console.error("Failed to update status");
            location.reload();
        }
    })
    .catch(error => {
        console.error("Error updating status:", error);
        location.reload();
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
<script>
function updateStatuspdfback() {
    let selectedIds = [];
    let checkboxes = document.querySelectorAll("input[name='status[]']:checked");

    checkboxes.forEach(checkbox => {
        let row = checkbox.closest('tr');
        let soDetailId = row.querySelector('td:nth-child(2)').textContent; // Get so_detail_id from second column
        selectedIds.push(soDetailId);
    });

    if (selectedIds.length === 0) {
        alert("กรุณาเลือกบิลที่ต้องการอัปเดต");
        return;
    }

    // Proceed to update
    console.log("Updating status for:", selectedIds); // Log the selected IDs

    fetch('/update-statuspdfsoback', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ soDetailIds: selectedIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Optionally reload the page to reflect changes
        } else {
            alert("ไม่สามารถอัปเดตสถานะได้");
        }
    })
    .catch(error => {
        console.error("Error updating status:", error);
        alert("เกิดข้อผิดพลาดในการอัปเดตสถานะ");
    });
}   
 
    </script>
</body>
</html>
