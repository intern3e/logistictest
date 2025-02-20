<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เปิดบิลสินค้า</title>
    <style>
        .btn-search {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
        .btn-search:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="text-center mb-4">
        <h3 class="text-dark">🔹 เปิดบิลสินค้า 🔹</h3>
    </div>

    <div class="mb-3">
        <label class="form-label">เลขที่ SO:</label>
        <form id="soSearchForm">
            <div style="display: flex; justify-content: space-between;">
                <input type="text" class="form-control" id="so_number" name="so_number" style="width: 80%;" required>
                <button type="submit" class="btn-search" style="width: 14%; height: 45px;">🔍 ค้นหา</button>
            </div>
        </form>
    </div>

    <form id="billForm">

    
        <input type="hidden" name="so_id" id="so_id" value="">

        <label>ผู้เปิดบิล</label>
        <input type="text" id="emp_name" name="emp_name" value="{{ session('emp_name', 'Guest') }}">        

            <label>รหัสลูกค้า</label>
            <input type="text" id="customer_id" name="customer_id" readonly>

            <label>ชื่อบริษัท</label>
            <input type="text" id="customer_name" name="customer_name" readonly>

            <label>เบอร์ติดต่อ</label>
            <input type="text" id="customer_tel" name="customer_tel" >

            <label>ที่อยู่จัดส่ง</label>
            <input type="text" id="customer_address" name="customer_address" >

            <label>ละติจูด ลองจิจูด</label>
            <input type="text" id="customer_la_long" name="customer_la_long" >

            <button type="button" class="btn-custom" onclick="openGoogleMaps()">Google Maps</button>
        </div>

        <div class="mb-3">
            <label class="form-label">แผนที่:</label>
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
                        <label><input type="checkbox" name="checkall"> เลือกทั้งหมด</label>
                        <button type="button" class="btn btn-danger insert-btn">เพิ่มสินค้า</button>

            <label>วันกำหนดส่ง</label>
            <input type="text" id="date_of_dali" name="date_of_dali" readonly>
                        
            <label>แจ้งเพิ่มเติม</label>
            <input type="text" id="additional_notes" name="additional_notes" >

            <button type="button" id="submitBill" class="btn btn-success"> เปิดบิล</button>

    </form>



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

    {{-- api --}}
    <script>
        document.getElementById("soSearchForm").addEventListener("submit", async function(event) {
            event.preventDefault();
            let soNumber = document.getElementById("so_number").value.trim();
            if (!soNumber) {
                alert("กรุณากรอกเลขที่ SO");
                return;
            }

            try {
                let response = await fetch(`http://server_update:8000/api/getSOHD?SONum=${soNumber}`);

                if (!response.ok) {
                    throw new Error("เกิดข้อผิดพลาดในการโหลดข้อมูล");
                }

                let data = await response.json();
                console.log("API Response:", data); // ตรวจสอบข้อมูล API

                if (!data || data.length === 0 || !data[0].CustID) {
                    alert("ไม่พบข้อมูลที่ตรงกับเลขที่ SO นี้");
                    return;
                }

                // กำหนดค่าลงในฟอร์ม
                document.getElementById("customer_id").value = data[0].CustID || 'ไม่พบข้อมูล';
                document.getElementById("customer_name").value = data[0].CustName || 'ไม่พบข้อมูล';
                document.getElementById("date_of_dali").value = data[0].ShipDate || 'ไม่พบข้อมูล';

                // กำหนดค่าให้ฟิลด์ so_id
                document.getElementById("so_id").value = data[0].SONum || '';

            } catch (error) {
                console.error('Error fetching data:', error);
                alert('เกิดข้อผิดพลาดในการดึงข้อมูล');
            }
        });
    </script>




</body>
</html>
