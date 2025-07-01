<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/insertpo.blade.css') }}">
    <title>สร้างเส้นทางรับของPO</title>

</head>
<body>
    <div class="container">
    <div class="header">
        <h2 class="text-dark"> สร้างเส้นทางรับของ PO </h2>
    <div class="mb-3">
        
        <label class="form-label">เลขที่ PO :</label>
        <form id="poSearchForm">
            <div style="display: flex; justify-content: space-between;">
                <input type="text" class="form-control" id="po_number" name="po_number" style="width: 83%;" required>
                <button type="submit" class="btn-search" style="width: 14%; height: 30px;  background-color:rgb(30, 62, 122); color:#fff;">🔍 ค้นหา</button>
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
            <input type="text" id="store_address" name="store_address" readonly >
             
                <div class="form-row-inline">
                <div class="form-group-inline">
            <label for="recvDate">วันกำหนดรับ :</label>
            <input type="date" id="recvDate" name="recvDate">
            </div>

            <div class="form-group-inline">
                <label for="cartype">ประเภทรถ :</label>
                <select id="cartype" name="cartype" required>
                    <option value="0" disabled selected>-- เลือกประเภทรถ --</option>
                    <option value="1">รถมอเตอร์ไซค์</option>
                    <option value="2">รถใหญ่</option>
                </select>
            </div>
        </div>

            <label >ละติจูด ลองจิจูด :</label>
            <div class="lat-long-container"style="display: flex; justify-content: space-between; width: 100%;">
                <input type="text" id="store_la_long" name="store_la_long">
            </div>
        </div>
        <button type="button" class="btn-custom" onclick="openGoogleMaps()">Google Maps</button>
        <br>
       <label for="additional_notes" style="display: block; margin-bottom: 4px; margin-top: 10px;">รายละเอียดเพิ่มเติม :</label>
       <textarea id="notes" name="notes" rows="2" style="font-size: 14px; padding: 6px; height: 40px;"></textarea>

       

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
                        
                        
                    
             <div style="display: flex; justify-content: center; margin-top: 20px;">
            <button type="button" id="submitBillpo" class="btn btn-success" 
            style="font-size: 18px; padding: 15px 30px; width: 200px; height: 50px;">
                บันทึก
            </button>

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
        document.getElementById("store_id").value = data.VendorCode|| '';
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
        let itemCounter = 1; // เริ่มลำดับที่ 1 หรือค่าที่คุณต้องการ

        data.ms_podt.forEach(item => {
            const itemId = `53-${String(itemCounter).padStart(4, '0')}`; // สร้างรหัส item_id เช่น 53-0001

            let row = `
                <tr>
                    <td><input type="text" class="form-control1" name="item_id[]" value="${itemId}" readonly></td>
                    <td><input type="text" class="form-control1" name="item_name[]" value="${item.GoodName}" readonly></td>
                    <td><input type="number" class="form-control1 item_quantity" name="item_quantity[]" value="${parseFloat(item.GoodQty2).toFixed(2)}" readonly></td>
                    <td><input type="number" class="form-control1 item_unit_price" name="item_unit_price[]" value="${parseFloat(item.GoodPrice2).toFixed(2)}" readonly></td>
                </tr>
            `;
            tbody.innerHTML += row;
            itemCounter++; // เพิ่มลำดับ
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