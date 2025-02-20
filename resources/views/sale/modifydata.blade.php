<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เปิดบิลสินค้า</title>
    <style>
/* General Styles */
/* Body */
body {
    font-family: 'Sarabun', sans-serif;
    background: linear-gradient(to right, #2c3e50, #597496);
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


    </style>
</head>
<body>
    <div class="container">
    <div class="header">
        <h3 class="text-dark">🔹 เปิดบิลสินค้า 🔹</h3>
    <div class="mb-3">
        <label class="form-label">เลขที่ SO :</label>
        <form id="soSearchForm">
            <div style="display: flex; justify-content: space-between;">
                <input type="text" class="form-control" id="so_number" name="so_number" style="width: 83%;" required>
                <button type="submit" class="btn-search" style="width: 14%; height: 45px;">🔍 ค้นหา</button>
            </div>
        </form>
    </div>

    <form id="billForm">

        <input type="hidden" name="so_id" id="so_id" value="">

        <label>ผู้เปิดบิล :</label>
        <input type="text" id="emp_name" name="emp_name" value="{{ session('emp_name', 'Guest') }}">        

            <label>รหัสลูกค้า :</label>
            <input type="text" id="customer_id" name="customer_id" readonly>

            <label>ชื่อบริษัท :</label>
            <input type="text" id="customer_name" name="customer_name" readonly>

            <label>เบอร์ติดต่อ :</label>
            <input type="text" id="customer_tel" name="customer_tel" >

            <label>ที่อยู่จัดส่ง :</label>
            <input type="text" id="customer_address" name="customer_address" >
            <label >ละติจูด ลองจิจูด :</label>
            <div class="lat-long-container">
                <input type="text" id="customer_la_long" name="customer_la_long">
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
                let coords = document.getElementById('customer_la_long').value;
                if (coords) {
                    document.getElementById('mapFrame').src = `https://www.google.com/maps?q=${coords}&output=embed`;
                }
            }
            document.getElementById('customer_la_long').addEventListener('input', updateMap);
            updateMap();
        </script>
            <label>วันกำหนดส่ง</label>
            <input type="text" id="date_of_dali" name="date_of_dali" readonly>
            
        
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>เลือกจัดส่ง</th>
                                <th>รหัสสินค้า</th>
                                <th>รายการ</th>
                                <th>จำนวน</th>
                                <th>ราคา/หน่วย</th>
                                <th>ลบ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr> 
                                <td><input type="checkbox" class="form-control1" name="status[]"></td>
                                <td><input type="text" class="form-control1" name="item_id[]"></td>
                                <td><input type="text" class="form-control1" name="item_name[]" ></td>
                                <td>
                                    <input type="number" class="form-control1 item_quantity" name="item_quantity[]" >
                                </td>
                                <td>
                                    <input type="number" class="form-control1 item_unit_price" name="item_unit_price[]" >
                                </td>
                                <td><button type="button" class="btn btn-danger delete-btn">ลบ</button></td>
                            </tr>
                        </tbody>
                        </table>
                        <div class="checkbox-container">
                            <label>
                                <input type="checkbox" name="checkall"> เลือกทั้งหมด
                            </label>
                            <button type="button" class="btn btn-danger insert-btn">เพิ่มสินค้า</button>
                        </div>
                        

                        
                        <label for="additional_notes">แจ้งเพิ่มเติม</label>
                        <textarea id="additional_notes" name="additional_notes" rows="4"></textarea>
                        

            <button type="button" id="submitBill" class="btn btn-success"> เปิดบิล</button>

    </form>
</div>


    {{-- function --}}
    <script>
                document.getElementById('submitBill').addEventListener('click', async function (event) {
                event.preventDefault();

                let formData = new FormData(document.getElementById('billForm'));

                // ตรวจสอบว่ามีสินค้าอย่างน้อย 1 รายการถูกเลือก
                let hasSelectedItems = false;
                document.querySelectorAll('input[name="status[]"]:checked').forEach((checkbox) => {
                    hasSelectedItems = true;
                });

                if (!hasSelectedItems) {
                    alert("กรุณาเลือกสินค้าอย่างน้อย 1 รายการ");
                    return;
                }

                // รับข้อมูลสินค้าทุกตัวในตาราง
                let itemRows = document.querySelectorAll('table tbody tr');
                itemRows.forEach((row, index) => {
                    let itemId = row.querySelector('input[name="item_id[]"]').value;
                    let itemName = row.querySelector('input[name="item_name[]"]').value;
                    let itemQuantity = row.querySelector('input[name="item_quantity[]"]').value;
                    let itemUnitPrice = row.querySelector('input[name="item_unit_price[]"]').value;
                    let itemStatus = row.querySelector('input[name="status[]"]').checked ? 1 : 0;

                    // เก็บค่าลงใน FormData
                    formData.append(`item_id[${index}]`, itemId);
                    formData.append(`item_name[${index}]`, itemName);
                    formData.append(`item_quantity[${index}]`, itemQuantity);
                    formData.append(`item_unit_price[${index}]`, itemUnitPrice);
                    formData.append(`status[${index}]`, itemStatus);
                });

                // ส่งข้อมูลไปยัง Controller Laravel
                try {
                    let response = await fetch('{{ route("insert.post") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    });

                    let data = await response.json();
                    if (data.success) {
                        alert(data.success);
                        window.location.href = '/dashboard';
                    } else if (data.error) {
                        alert(data.error);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('มีข้อผิดพลาดในการส่งข้อมูล');
                }
            });
    </script>

    {{-- function --}}
    <script>
                const selectAllCheckbox = document.querySelector('input[name="checkall"]');
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        const checkboxes = document.querySelectorAll('input[type="checkbox"]:not([name="checkall"])');
                        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                    });
                }
        
                const tableBody = document.querySelector('table tbody');
                if (tableBody) {
                    tableBody.addEventListener('click', function(e) {
                        if (e.target.classList.contains('delete-btn')) {
                            var row = e.target.closest('tr');
                            row.remove();
                        }
                    });
                }
        
                const insertBtn = document.querySelector('.insert-btn');
                if (insertBtn) {
                    insertBtn.addEventListener('click', function() {
                        var newRow = document.createElement('tr');
                        newRow.innerHTML = `
                            <td><input type="checkbox" class="form-control1" name="status[]"></td>
                            <td><input type="text" class="form-control1" name="item_id[]"></td>
                            <td><input type="text" class="form-control1" name="item_name[]"></td>
                            <td>
                                <input type="number" class="form-control1 item_quantity" name="item_quantity[]" oninput="calculateTotal(this)">
                            </td>
                            <td>
                                <input type="number" class="form-control1 item_unit_price" name="item_unit_price[]" oninput="calculateTotal(this)">
                            </td>
                        
                            <td><button type="button" class="btn btn-danger delete-btn">ลบ</button></td>
                        `;
                        tableBody.appendChild(newRow);
                    });
                    
                }
        
                    

        
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
                let confirmation = confirm("คุณต้องการเปิดบิลใช่หรือไม่?");

                if (confirmation) {
                // หากผู้ใช้กดตกลง
                let formData = new FormData(document.getElementById('billForm')); // เก็บข้อมูลฟอร์ม

                fetch('{{ route("insert.post") }}', { // ส่งข้อมูลฟอร์มไปยังเส้นทาง insert.post
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
                        window.location.href = '/dashboard'; // เปลี่ยนเส้นทางไปยังหน้า dashboard
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






</body>
</html>
