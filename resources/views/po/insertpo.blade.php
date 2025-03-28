<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เปิดบิลPO</title>
    <style>
/* General Styles */
/* Body */
body {
    font-family: 'Sarabun', sans-serif;
    background: linear-gradient(to right, #3a7bd5, #3a6073);
    margin: 0;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
}

/* กรอบครอบฟอร์ม */
.container {
    background: #ffffff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 1000px; /* ปรับขนาดความกว้างสูงสุด */
    text-align: left;
}

/* Header */
.header h3 {
    font-size: 24px;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}

/* Label */
label {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
}

/* Input Fields */
input[type="text"], input[type="number"], input[type="date"], input[type="hidden"] {
    width: 95%;
    padding: 10px;
    margin-bottom: 15px;
    background: #f0f4f8;
    border: 1px solid #333;
    border-radius: 4px;
    font-size: 14px;
}

/* Table Styles */
.table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 12px;
    text-align: center;
    border: 1px solid #ddd;
}

.table th {
    background-color: #f0f4f8;
}

/* Buttons */
button {
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 4px;
    cursor: pointer;
    border: none;
}

.btn-search {
    background-color: #4CAF50;
    color: #fff;
}

.btn-search:hover {
    background-color: #45a049;
}

.btn-danger {
    background-color: #f44336;
    color: white;
}

.btn-danger:hover {
    background-color: #e53935;
}

.btn-success {
    background-color: #4CAF50;
    color: white;
}

.btn-success:hover {
    background-color: #45a049;
}

/* Google Maps iframe */
#mapFrame {
    border: 0;
    border-radius: 8px;
    width: 100%;
    height: 300px;
}

/* Checkbox Styles */
input[type="checkbox"] {
    margin-right: 10px;
}

/* Table Input Fields */
.form-control1 {
    width: 100%;
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ddd;
}

/* Additional Styles */
.mb-3 {
    margin-bottom: 20px;
}

.text-dark {
    color: #333;
}
/* ปรับให้ label, input และปุ่มอยู่ในบรรทัดเดียวกัน */
.lat-long-container {
    display: flex;
    align-items: center;
    gap: 10px; /* ระยะห่างระหว่าง input และปุ่ม */
}

/* ปรับขนาด input ให้พอดีกับพื้นที่ */
.lat-long-container input {
    flex: 1; /* ให้ input ยืดตามพื้นที่ที่เหลือ */
    padding: 10px;
    border: 1px solid #333;
    border-radius: 4px;
}

/* ปรับขนาดปุ่มให้ไม่กินพื้นที่เกินไป */
.lat-long-container .btn-custom {
    white-space: nowrap; /* ป้องกันข้อความขึ้นบรรทัดใหม่ */
    padding: 10px 15px;
}
.btn-custom{
    background-color: #f39c12;
    color: #fff;
}
.btn-custom:hover {
    background-color: #e67e22;
}
.btn-custom:hover{
            background-color: #e74c3c;
            color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }
/* ปรับช่องกรอกข้อมูลในตาราง */
.form-control1 {
    width: 100%;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
    text-align: center;
    background: #f9f9f9; /* เปลี่ยนพื้นหลังให้อ่อนขึ้น */
    transition: all 0.3s ease;
}

/* เปลี่ยนสีเส้นขอบเมื่อโฟกัส */
.form-control1:focus {
    border-color: #4CAF50;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
    background: #fff;
}
/* จัดตำแหน่ง checkbox และปุ่มให้อยู่ชิดขวา */
.checkbox-container {
    display: flex;
    align-items: center;
    justify-content: flex-end; /* ชิดขวา */
    gap: 15px; /* ระยะห่างระหว่าง checkbox กับปุ่ม */
    margin-top: 10px;
}

/* ปรับระยะห่างของ checkbox */
.checkbox-container label {
    display: flex;
    align-items: center;
    font-size: 16px;
}

.insert-btn {
    background-color: #2196F3; /* สีฟ้า */
    color: white; /* สีข้อความ */
    border: none;
}

.insert-btn:hover {
    background-color: #1976D2; /* สีฟ้าเข้มเมื่อโฮเวอร์ */
}
/* ปรับขนาดและสไตล์ของกล่องข้อความ */
textarea {
    width: 95%;
    padding: 10px;
    border: 1px solid #060505;
    border-radius: 4px;
    font-size: 14px;
    resize: vertical; /* ให้ปรับขนาดสูง-ต่ำได้ */
    min-height: 100px;
}
/* เปลี่ยนสีพื้นหลังและข้อความของหัวตาราง */
.table thead th {
    background: linear-gradient(to right, #2c3e50, #4b6584);
    color: white; /* สีตัวอักษร */
    font-size: 16px;
    padding: 10px;
    text-align: center;
}
/* เปลี่ยนขนาดและสีของปุ่ม "เปิดบิล" */
#submitBill {
    background-color: #28a745; /* สีเขียว */
    color: white; /* สีข้อความ */
    padding: 15px 300px; /* ขนาดของปุ่ม */
    font-size: 18px; /* ขนาดฟอนต์ */
    border-radius: 5px; /* มุมปุ่มโค้ง */
    border: none; /* ไม่ให้ขอบ */
    cursor: pointer;
    margin-left: 15%;
    margin-top:10px ;
}


/* เมื่อโฮเวอร์ (เอาเมาส์ไปวาง) เปลี่ยนสี */
#submitBill:hover {
    background-color: #218838; /* สีเขียวเข้ม */
}
/* Style the label */
label[for="cartype"] {
    font-weight: bold;
    color: #2c3e50;
    margin-right: 10px;
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
    max-width: 300px; /* Adjust max width as needed */
    box-sizing: border-box; /* Ensure padding does not affect width */
    transition: border-color 0.3s ease;
}

/* Add focus effect */
select#cartype:focus {
    background-color: #FfF8FF;
    border-color: #333;
    outline: none;
}

/* Style for disabled and selected option */
select#cartype option:disabled {
    color: #ccc;
}

/* Style for selected option */
select#cartype option:checked {
    background-color: #f39c12;
    color: #fff;
}

    </style>
</head>
<body>
    <div class="container">
    <div class="header">
        <h3 class="text-dark"> เปิดบิลPO </h3>
    <div class="mb-3">
        
        <label class="form-label">เลขที่ PO :</label>
        <form id="poSearchForm">
            <div style="display: flex; justify-content: space-between;">
                <input type="text" class="form-control" id="po_number" name="po_number" style="width: 83%;" required>
                <button type="submit" class="btn-search" style="width: 14%; height: 45px;">🔍 ค้นหา</button>
            </div>
        </form>
    </div>

    <form id="billForm">
        <input type="hidden" name="po_id" id="po_id" value="">
        <input type="hidden" name="status" id="status" value="0">

            <label>ผู้เปิดบิล :</label>
            <input type="text" id="emp_name" name="emp_name" value="{{ session('emp_name', 'Guest') }}"> 

            <label>ชื่อร้านค้า :</label>
            <input type="text" id="store_name" name="store_name" >

            <label>เบอร์ติดต่อ :</label>
            <input type="text" id="store_tel" name="store_tel" >

            <label>ที่อยู่ :</label>
            <input type="text" id="store_address" name="store_address" >

            <label >ละติจูด ลองจิจูด :</label>
            <div class="lat-long-container">
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
        document.getElementById("store_tel").value = data.Contact || '';

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
    


</body>
</html>
