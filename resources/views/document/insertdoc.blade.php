<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>เปิดบิลสินค้า</title>
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
        border-bottom: 2px solid#2c3e50;
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
        background-color:#2c3e50 ;
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

    .autocomplete-list {
        position: absolute;
        z-index: 1000;
        background: white;
        width: 100%;
        max-height: 250px;
        overflow-y: auto;
        font-size: 16px;
        border-radius: 6px;
    }
    .autocomplete-list li {
        padding: 10px;
        cursor: pointer;
    }
    .autocomplete-list li:hover {
        background-color: #f0f0f0;
    }
    </style>


</head>
<body>
    <div class="container">
    <div class="header">
        <h2 class="text-dark"> สร้างเอกสารเพิ่มเติม </h2>

<form id="billForm">
    <div class="input-container">
        <div>
            <label>ผู้เปิดบิล :</label>
            <input type="text" id="emp_name" name="emp_name" value="{{ session('emp_name', 'Guest') }}">
        </div>

        <div class="form-group">
            <label for="datestamp">วันที่</label>
            <input type="date" id="datestamp" name="datestamp">
        </div>
        
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const today = new Date().toISOString().split('T')[0]; // ดึงแค่วันที่
                document.getElementById('datestamp').value = today;
            });
        </script>
    
        <div class="form-group">
            <label for="doctype">ประเภทบิล</label>
            <select id="doctype" name="doctype" required onchange="toggleOtherInput()">
                <option value="" disabled selected>-- กรุณาเลือกประเภทบิล --</option>
                <option value="รับของ">รับของ</option>
                <option value="ส่งของ">ส่งของ</option>
                <option value="รับของ+ส่งของ">รับของ+ส่งของ</option>
                <option value="อื่นๆ" id="other_option">อื่นๆ</option>
            </select>
        
            <input type="text" id="other_input" name="other_input" style="display:none;" placeholder="กรุณากรอกข้อมูล" oninput="updateOtherOption()">
        </div>
        
        <script>
            function toggleOtherInput() {
                var doctype = document.getElementById("doctype").value;
                var otherInput = document.getElementById("other_input");
        
                if (doctype === "อื่นๆ") {
                    otherInput.style.display = "block";
                } else {
                    otherInput.style.display = "none";
                }
            }
        
            function updateOtherOption() {
                var otherInput = document.getElementById("other_input").value;
                var otherOption = document.getElementById("other_option");
        
                // อัปเดตค่าของ option "อื่นๆ" ด้วยค่าที่กรอก
                otherOption.value = otherInput;
                otherOption.text = otherInput || "อื่นๆ"; // ถ้าว่างให้ใช้ "อื่นๆ"
            }
        </script>

    <input type="hidden" id="id_com" name="id_com">


        
<div style="position: relative; margin-bottom: 20px;">
    <label for="com_name">บริษัท:</label>
    <input type="text" id="com_name" name="com_name" style="width: 100%; padding: 10px; font-size: 16px;" autocomplete="off">
    <ul id="autocomplete_list" class="autocomplete-list" style="display: none;"></ul>
    <button type="button" id="search" style="position: absolute; right: 10px; top: 10px; display: none;">🔍 ค้นหา</button>
</div>

<script>
    document.getElementById('com_name').addEventListener('input', function() {
        const inputText = document.getElementById('com_name').value;
        const noDataMessage = document.getElementById('no_data_message');
        if (inputText.length >= 3) { // ตรวจสอบว่ามีอักษรครบ 3 ตัว
            document.getElementById('search').click(); // สั่งกดปุ่ม search
            // สมมติว่าได้ข้อมูลจาก API หรือระบบค้นหา
            const searchResults = []; // ตัวอย่างข้อมูลที่ค้นเจอ
            if (searchResults.length === 0) {
                noDataMessage.style.display = 'block'; // แสดงข้อความ "ไม่มีข้อมูล"
            } else {
                noDataMessage.style.display = 'none'; // ซ่อนข้อความ "ไม่มีข้อมูล" เมื่อเจอข้อมูล
                // แสดงผลข้อมูลใน autocomplete list (สามารถทำได้ตามต้องการ)
            }
        } else {
            noDataMessage.style.display = 'none'; // ซ่อนข้อความ "ไม่มีข้อมูล" ถ้าไม่มีคำค้นหาครบ 3 ตัว
        }
    });

    document.getElementById('com_name').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // ป้องกันการ submit form ถ้ามี
            document.getElementById('search').click(); // สั่งกดปุ่ม search
        }
    });
</script>



    

<script>
    let allCompanies = [];
    
    document.getElementById("search").addEventListener("click", async () => {
        let keyword = document.getElementById("com_name").value.trim();
    
        if (!keyword) {
            alert("กรุณากรอกชื่อบริษัท");
            return;
        }
    
        try {
            const response = await fetch(`http://server_update:8000/api/getCustAndVendor?keySearch=${encodeURIComponent(keyword)}`);
            if (!response.ok) throw new Error("เกิดข้อผิดพลาดในการโหลดข้อมูล");
    
            const data = await response.json();
            allCompanies = [...(data.Customer || []), ...(data.Supplier || [])];
    
            showAutocompleteResults(allCompanies);
    
        } catch (err) {
            console.error(err);
            alert("เกิดข้อผิดพลาดในการดึงข้อมูล");
        }
    });
    
    function showAutocompleteResults(companies) {
        const listEl = document.getElementById("autocomplete_list");
        listEl.innerHTML = "";
        listEl.style.display = "block";
    
        companies.forEach(company => {
            const idcust = (company.CustCode || company.VendorCode || company.SupCode || "").trim();
            const name = (company.CustName || company.VendorName || company.SupName || "").trim();
            const addr = (company.ContAddr1 || "").trim();
            const item = document.createElement("li");
            item.textContent = `${name} [${addr}]`;
            item.addEventListener("click", () => {
                document.getElementById("id_com").value = idcust;
                document.getElementById("com_name").value = name;
                document.getElementById("com_address").value = addr;
                fetchFormType()
                listEl.style.display = "none";
            });
            listEl.appendChild(item);
        });
    }
    
    // ปิด dropdown ถ้าคลิกนอก
    document.addEventListener("click", function(e) {
        const list = document.getElementById("autocomplete_list");
        if (!document.getElementById("com_name").contains(e.target) && !list.contains(e.target)) {
            list.style.display = "none";
        }
    });
    </script>
    


    
        <div>
            <label>ชื่อผู้ติดต่อ:</label>
            <input type="text" id="contact_name" name="contact_name" >
        </div>

        <div>
            <label>เบอร์ติดต่อ :</label>
            <input type="text" id="contact_tel" name="contact_tel">
        </div>
    
    </div>

    <div class="form-label">
    <div style="margin-bottom: 20px;">
    <label for="com_address">ที่อยู่จัดส่ง :</label>
    <textarea id="com_address" name="com_address" rows="4" style="width: 100%; padding: 10px; font-size: 16px; border-radius: 10px; border: 1px solid #ccc;"></textarea>
    </div>

        
        <label>ละติจูด ลองจิจูด :</label>
        <div style="display: flex; justify-content: space-between; width: 100%;" >
            <input type="text" id="com_la_long" name="com_la_long">
            <button type="button" class="btn-custom" onclick="openGoogleMaps()">Google Maps</button>
        </div>

        <div class="mb-3">
            <label class="form-label">แผนที่ :</label>
            <iframe id="mapFrame" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            
        </div>

        <script>
            function updateMap() {
                let coords = document.getElementById('com_la_long').value;
                if (coords) {
                    document.getElementById('mapFrame').src = `https://www.google.com/maps?q=${coords}&output=embed`;
                }
            }

            document.getElementById('com_la_long').addEventListener('input', updateMap);
            updateMap();
        </script>

            
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>เลือกจัดส่ง</th>
                    <th>รายการ</th>
                    <th>จำนวน</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody id="detail"></tbody>
        </table>

        <div class="checkbox-container">
            <label>
                <input type="checkbox" name="checkall"> เลือกทั้งหมด
            </label>
            <button type="button" class="btn btn-success insert-btn">เพิ่มสินค้า</button> 
        </div>
        
        <label for="notes">แจ้งเพิ่มเติม</label>
        <textarea id="notes" name="notes" rows="4"></textarea>

        <div style="display: flex; justify-content: center; margin-top: 20px;">
            <button type="button" id="submitBill" class="btn btn-success" 
            style="font-size: 18px; padding: 15px 30px; width: 200px; height: 50px;">
                สร้างเอกสาร
            </button>
        </div>
    </form>

    <script>
        document.getElementById('submitBill').addEventListener('click', async function (event) {
            event.preventDefault();
            let formData = new FormData(document.getElementById('billForm'));
            // รับข้อมูลสินค้าทั้งหมดโดยไม่เช็ค checkbox
            let itemRows = document.querySelectorAll('table tbody tr');
            itemRows.forEach((row, index) => {
                let itemName = row.querySelector('input[name="item_name[]"]').value;
                let itemQuantity = row.querySelector('input[name="item_quantity[]"]').value;

    
                formData.append(`item_name[${index}]`, itemName);
                formData.append(`item_quantity[${index}]`, itemQuantity);
            });
    
            // ส่งข้อมูลไปยัง Controller Laravel
            try {
                let response = await fetch('{{ route("insertdocu") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                });
    
                let data = await response.json();
                if (data.success) {
                    alert(data.success);
                    window.location.href = 'dashboarddoc';
                } else if (data.error) {
                    alert(data.error);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('มีข้อผิดพลาดในการส่งข้อมูล');
            }
        });

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
                    <td><input type="text" class="form-control1" name="item_name[]"></td>
                    <td>
                        <input type="number" class="form-control1 item_quantity" name="item_quantity[]" >
                    </td>
                    <td><button type="button" class="btn btn-danger delete-btn">ลบ</button></td>
                `;
                tableBody.appendChild(newRow);
                updateTotalAmount();
                const quantityInput = newRow.querySelector('input[name="item_quantity[]"]');
                quantityInput.addEventListener('input', () => calculatePrice(quantityInput));

            });
        }

        let mapWindow;
        let closeTimer;

            function openGoogleMaps() {
                const screenWidth = window.screen.width;
                const screenHeight = window.screen.height;
                const windowWidth = 800;
                const windowHeight = 600;

                // ชิดขวา: left = ความกว้างหน้าจอ - ความกว้างของหน้าต่าง
                const leftPosition = screenWidth - windowWidth;
                // อยู่กลางแนวตั้ง: top = (ความสูงหน้าจอ - ความสูงของหน้าต่าง) / 2
                const topPosition = (screenHeight - windowHeight) / 2;

                // เปิดหน้าต่างใหม่
                const mapWindow = window.open(
                    "https://www.google.com/maps/@13.7563,100.5018,14z",
                    "Google Maps",
                    `width=${windowWidth},height=${windowHeight},left=${leftPosition},top=${topPosition}`
                );
            }
    </script>

<script>
    function fetchFormType() {
        console.log("ตรวจสอบ id_com:", document.getElementById("id_com").value);
        console.log('fetchdoclalong called');
    
        var id_com = document.getElementById("id_com").value;
    
        if (id_com) {
            fetch('/fetch-doclalong', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id_com: id_com }) // ใช้ id_com ที่รับมา
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response from server:', data);
    
                if (data.com_la_long) {
                    document.getElementById("com_la_long").value = data.com_la_long;
                    updateMap(); // เรียกฟังก์ชันอัปเดตแผนที่ (คุณต้องแน่ใจว่ามีฟังก์ชันนี้ด้วย)
                } else {
                    document.getElementById("com_la_long").value = 'ไม่มีข้อมูล';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById("com_la_long").value = 'ไม่มีข้อมูล';
            });
        } else {
            document.getElementById("com_la_long").value = 'ไม่มีข้อมูล';
        }
    }
</script>
    
</body>
</html>
 