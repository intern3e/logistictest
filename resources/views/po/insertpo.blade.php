<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>เปิดบิลPO</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 20px;
        background-color: rgb(233, 233, 233); /* Light gray background */
    }

    .container {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 30px;
        max-width: 1000px;
        margin: auto;
    }

    .header {
        margin-bottom: 30px;
    }

    h2.text-dark {
        color: #333333;
        border-bottom: 2px solid rgb(30, 62, 122);
        padding-bottom: 10px;
    }

    .form-label, label {
        font-weight: bold;
        margin-top: 15px;
        display: block;
        color: #333;
    }

    input[type="text"], input[type="number"], input[type="file"], select, textarea {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-sizing: border-box;
    }

    input[readonly] {
        background-color: #f1f1f1;
    }

    .input-container, .input-container1 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .checkbox-container {
        margin-top: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .btn, .btn-success, .btn-danger {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-custom {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        margin-left: 10px;
    }

    .btn-custom:hover {
        background-color: #0056b3;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        background-color: #fff;
    }

    table th, table td {
        border: 1px solid #dee2e6;
        padding: 10px;
        text-align: center;
    }

    table thead {
        background-color:rgb(30, 62, 122);
        color: #fff;
    }

    textarea {
        resize: vertical;
    }

    iframe {
        border-radius: 8px;
        margin-top: 15px;
    }

    @media (max-width: 768px) {
        .input-container, .input-container1 {
            grid-template-columns: 1fr;
        }

        .btn-custom {
            margin-top: 10px;
        }
    }
    .form-section {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        flex: 1;
        min-width: 250px;
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-group input,
    .form-group select {
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 10px;
    }

/* Style the select element */
select#cartype {
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #fff;
    color: #333;
    width: 100%;
    max-width: 300px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
}

/* Add focus effect */
select#cartype:focus {
    border-color: #333;
    outline: none;
}

/* Style for disabled and selected option */
select#cartype option:disabled {
    color: #ccc;
}

/* Style for selected option */
select#cartype option:checked {
    background-color: #f39c12; /* สีส้ม */
    color: #fff;
}

    </style>
</head>
<body>
    <div class="container">
    <div class="header">
        <h2 class="text-dark"> เปิดบิลPO </h2>
    <div class="mb-3">
        
        <label class="form-label">เลขที่ PO :</label>
        <form id="poSearchForm">
            <div style="display: flex; justify-content: space-between;">
                <input type="text" class="form-control" id="po_number" name="po_number" style="width: 83%;" required>
                <button type="submit" class="btn-search" style="width: 14%; height: 45px;  background-color:rgb(30, 62, 122); color:#fff;">🔍 ค้นหา</button>
            </div>
        </form>
    </div>

    <form id="billForm">
        <input type="hidden" name="po_id" id="po_id" value="">
        <input type="hidden" name="status" id="status" value="0">

            <label>ผู้เปิดบิล :</label>
            <input type="text" id="emp_name" name="emp_name" value="{{ session('emp_name', 'Guest') }}"> 
            

            <input type="hidden" id="store_id" name="store_id" readonly>
            
            <label>ชื่อร้านค้า :</label>
            <input type="text" id="store_name" name="store_name" readonly>

            <label>เบอร์ติดต่อ :</label>
            <input type="text" id="store_tel" name="store_tel" >

            <label>ที่อยู่ :</label>
            <input type="text" id="store_address" name="store_address" >

            <label >ละติจูด ลองจิจูด :</label>
            <div class="lat-long-container"style="display: flex; justify-content: space-between; width: 100%;">
                <input type="text" id="store_la_long" name="store_la_long">
                <button type="button" class="btn-custom" onclick="openGoogleMaps()">Google Maps</button>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">แผนที่ :</label>
            <iframe id="mapFrame" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
        {{-- map --}}
        <script>
            function updateMap() {
                let coords = document.getElementById('store_la_long').value;
                if (coords) {
                    document.getElementById('mapFrame').src = `https://www.google.com/maps?q=${coords}&output=embed`;
                }
            }
            document.getElementById('store_la_long').addEventListener('input', updateMap);
            updateMap();
        </script>
            <label>วันกำหนดรับ</label>
            <input type="text" id="recvDate" name="recvDate" readonly>
            
        
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>รหัสสินค้า</th>
                                <th>รายการ</th>
                                <th>จำนวน</th>
                                <th>ราคา/หน่วย</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr> 
                                <td><input type="text" class="form-control1" name="item_id[]"></td>
                                <td><input type="text" class="form-control1" name="item_name[]" ></td>
                                <td>
                                    <input type="number" class="form-control1 item_quantity" name="item_quantity[]" >
                                </td>
                                <td>
                                    <input type="number" class="form-control1 item_unit_price" name="item_unit_price[]" >
                                </td>
                            </tr>
                        </tbody>
                        </table>
                        
                        <label for="cartype" >ประเภทรถ</label>
                        <select id="cartype" name="cartype" required>
                            <option value="0" disabled selected>-- เลือกประเภทรถ --</option>
                            <option value="1">รถมอเตอร์ไซค์</option>
                            <option value="2">รถใหญ่</option>
                        </select>
                        
                        
                        <label for="additional_notes">แจ้งเพิ่มเติม</label>
                        <textarea id="additional_notes" name="additional_notes" rows="4"></textarea>
                    

            <button type="button" id="submitBillpo" class="btn btn-success"> เปิดบิล</button>

    </form>
</div>


    {{-- function --}}
    <script>
                document.getElementById('submitBillpo').addEventListener('click', async function (event) {
                event.preventDefault();

                let formData = new FormData(document.getElementById('billForm'));

                // รับข้อมูลสินค้าทุกตัวในตาราง
                let itemRows = document.querySelectorAll('table tbody tr');
                itemRows.forEach((row, index) => {
                    let itemId = row.querySelector('input[name="item_id[]"]').value;
                    let itemName = row.querySelector('input[name="item_name[]"]').value;
                    let itemQuantity = row.querySelector('input[name="item_quantity[]"]').value;
                    let itemUnitPrice = row.querySelector('input[name="item_unit_price[]"]').value;

                    // เก็บค่าลงใน FormData
                    formData.append(`item_id[${index}]`, itemId);
                    formData.append(`item_name[${index}]`, itemName);
                    formData.append(`item_quantity[${index}]`, itemQuantity);
                    formData.append(`item_unit_price[${index}]`, itemUnitPrice);
                });

                // ส่งข้อมูลไปยัง Controller Laravel
                let response = await fetch('{{ route("insertpo.post") }}', {
    method: 'POST',
    body: formData,
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
    },
});

if (response.ok) {
    let data = await response.json();
    if (data.success) {
        alert(data.success);
        window.location.href = '/dashboardpo';
    } else if (data.error) {
        alert(data.error);
    }
} else {
    let errorText = await response.text();
    console.error('Server error:', errorText);  // พิมพ์ข้อผิดพลาดที่ได้รับจากเซิร์ฟเวอร์

    // Example of how you can improve the error message
    if (errorText.includes('missing')) {
        alert('กรุณาใส่ข้อมูลให้ครบ เช่น เลขที่ PO, รายการสินค้า หรือ ข้อมูลการติดต่อ');
    } else if (errorText.includes('invalid')) {
        alert('ข้อมูลที่กรอกไม่ถูกต้อง กรุณาตรวจสอบใหม่');
    } else {
        alert('เกิดข้อผิดพลาดในการส่งข้อมูล');
    }
}
            });
    </script>

    {{-- function --}}
    <script>
            function openGoogleMaps() {
                const mapWindow = window.open(
                    "https://www.google.com/maps/@13.7563,100.5018,14z",
                    "Google Maps",
                    "width=800,height=600"
                );
            }
                function confirmSubmit(event) {
                event.preventDefault(); // ป้องกันการ submit แบบปกติ

                // แสดงการแจ้งเตือน
                let confirmation = confirm("คุณต้องการเปิดบิลPOใช่หรือไม่?");

                if (confirmation) {
                // หากผู้ใช้กดตกลง
                let formData = new FormData(document.getElementById('billForm')); // เก็บข้อมูลฟอร์ม

                fetch('{{ route("insertpo.post") }}', { // ส่งข้อมูลฟอร์มไปยังเส้นทาง insert.post
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', // ส่ง CSRF Token
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.success); // แจ้งเตือนสำเร็จ
                        window.location.href = '/dashboardpo'; // เปลี่ยนเส้นทางไปยังหน้า dashboard
                    } else if (data.error) {
                        alert(data.error); // แจ้งเตือนข้อผิดพลาด
                    }
                })
                .catch((error) => {
                    console.error('Error:', error); // แสดงข้อผิดพลาดในคอนโซล
                    alert('มีข้อผิดพลาดในการส่งข้อมูล');
                });

                } else {
                // หากผู้ใช้กดยกเลิก
                alert("คุณยกเลิกการเปิดบิล.");
                }
                }

    </script>

  

<script>
document.getElementById("poSearchForm").addEventListener("submit", async function(event) {
    event.preventDefault();
    let poNumber = document.getElementById("po_number").value.trim();
    if (!poNumber) {
        alert("กรุณากรอกเลขที่ po");
        return;
    }

    try {
        let response = await fetch(`http://server_update:8000/api/getPODetail?PONum=${poNumber}`);

        if (!response.ok) {
            throw new Error("เกิดข้อผิดพลาดในการโหลดข้อมูล");
        }

        let data = await response.json();
        console.log("API Response:", data); // ตรวจสอบข้อมูล API

        if (!data || !data.DocuNo || !data.ms_podt || data.ms_podt.length === 0) {
            alert("ไม่พบข้อมูลที่ตรงกับเลขที่ PO นี้");
            return;
        }

        // กำหนดค่าลงในฟอร์ม
        document.getElementById("recvDate").value = formatDate(data.ShipDate);
        document.getElementById("po_id").value = data.DocuNo || '';
        document.getElementById("store_id").value = data.VendorID|| '';
        fetchFormType();
        document.getElementById("store_tel").value = data.ContTel || '';
        document.getElementById('store_name').value = data.VendorName;  
        document.getElementById('store_address').value = 
        [data.ContAddr1,data.ContAddr2,data.ContDistrict,data.ContAmphur, data.ContProvince, data.ContPostCode]
        .filter(Boolean) // กรองค่าที่เป็น null หรือ undefined หรือว่าง
        .join(', ');

        // Clear existing rows in the table before inserting new ones
        let tbody = document.querySelector('table tbody');
        tbody.innerHTML = '';

        // Loop through ms_podt to show product details in the table
        data.ms_podt.forEach(item => {
            let row = `
                <tr>
                    <td><input type="text" class="form-control1" name="item_id[]" value="${item.GoodID}" readonly></td>
                    <td><input type="text" class="form-control1" name="item_name[]" value="${item.GoodName}" readonly></td>
                    <td><input type="number" class="form-control1 item_quantity" name="item_quantity[]" value="${item.GoodQty2}" readonly></td>
                    <td><input type="number" class="form-control1 item_unit_price" name="item_unit_price[]" value="${item.GoodPrice2}" readonly></td>
                </tr>
            `;
            tbody.innerHTML += row; // Add the row to the table
        });

    } catch (error) {
        console.error('Error fetching data:', error);
        alert('เกิดข้อผิดพลาดในการดึงข้อมูล');
    }
});

function formatDate(dateString) {
    let date = new Date(dateString);
    let day = date.getDate().toString().padStart(2, '0');
    let month = (date.getMonth() + 1).toString().padStart(2, '0');
    let year = date.getFullYear();
    return `${day}-${month}-${year}`;
}

    </script>
    
    <script>
        function fetchFormType() {
            console.log('fetchpolalong called');
        
            var store_id = document.getElementById("store_id").value;
        
            if (store_id) {
                fetch('/fetch-polalong', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ store_id: store_id })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Response from server:', data);
        
                    if (data.store_la_long) {
                        document.getElementById("store_la_long").value = data.store_la_long;
                        updateMap();
                    } else {
                        document.getElementById("store_la_long").value = 'ไม่มีข้อมูล';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById("store_la_long").value = 'ไม่มีข้อมูล';
                });
            } else {
                document.getElementById("store_la_long").value = 'ไม่มีข้อมูล';
            }
        }
        </script>
        

</body>
</html> 