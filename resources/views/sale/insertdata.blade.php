<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เปิดบิลสินค้า</title>
    <style>
    /* Reset CSS */
    * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color:rgb(233, 233, 233);
            padding: 20px;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color:#f9f9f9 ;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h3 {
            font-size: 28px;
            color: #333;
            font-weight: 600;
        }

        .form-label {
        font-weight: 600;
        margin-bottom: 10px;
        display: block;
        text-align: left; /* ทำให้ชิดซ้าย */
    }


        /* Form Styling */
        input[type="text"], input[type="number"], input[type="date"], input[type="hidden"], textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            font-size: 16px;
            color: #333;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color:#0071e3;
            font-weight: bold;
            color: #fff;
        }

        .btn-search, .btn-custom, .btn-danger, .btn-success {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            
        }

        .btn-search {
            background-color: #0071e3;
            color: #fff;
        }

        .btn-search:hover {
            background-color: #005bb5;
        }

        .btn-custom {
            background-color: #ff6f61;
            color: white;
        }

        .btn-custom:hover {
            background-color: #e85a50;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-success {
            background-color: #2ecc71;
            color: white;
            
        }

        .btn-success:hover {
            background-color: #27ae60;
        }

        .checkbox-container {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .checkbox-container label {
            font-size: 16px;
        }

        .lat-long-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .lat-long-container input {
            width: 80%;
        }

        #mapFrame {
            width: 100%;
            height: 300px;
            border: none;
            border-radius: 8px;
        }
.input-container {
    display: flex;
    flex-wrap: wrap; /* ให้สามารถย้ายไปบรรทัดใหม่เมื่อไม่มีที่ว่าง */
    gap: 65px; /* ช่องว่างระหว่าง input */
    
}

.input-container label {
    width: 30%; /* ขนาดของ label */
    display: inline-block;
    margin-bottom: 10px;
}

.input-container input {
    width: 68%; /* ขนาดของ input */
    padding: 10px;
    font-size: 16px;
    margin-bottom: 10px;
}
.input-container1 {
    display: flex;
    margin-left:-4% ;
    flex-direction: column; /* จัดให้อยู่เป็นแนวตั้ง */
}

.input-container1 div {
    display: flex;
    align-items: center;
    gap: 10px; /* ระยะห่างระหว่าง label กับ input */
}

.input-container1 label {
    width: 120px; /* ปรับให้ label มีขนาดเท่ากัน */
    text-align: right;
}

.input-container1 input {
    width: 77%;
    padding: 10px;
    font-size: 16px;
    box-sizing: border-box; /* ทำให้ padding ไม่ทำให้ขนาด input เปลี่ยน */
}

    .lat-long-container{
        display: flex;
        align-items: center;  /* จัดให้อยู่กึ่งกลางแนวตั้ง */
        gap: 10px;  /* ระยะห่างระหว่างองค์ประกอบ */
        width: 75%;
    }

    .lat-long-container label {
        white-space: nowrap; /* ป้องกันข้อความขึ้นบรรทัดใหม่ */
        font-weight: bold;
        font-size: 16px;
    }

    .lat-long-container input {
        flex: 1; /* ทำให้ input ขยายตัวเพื่อให้มีขนาดพอดีกับพื้นที่ */
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        min-width: 200px; /* ป้องกัน input หดเกินไป */
    }

    .btn-custom {
        flex: 1; /* ทำให้ปุ่มมีขนาดเท่ากับ input */
        background-color: #007bff;
        color: white;
        border: none;
        font-size: 14px;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
        text-align: center;
        width: 250px;
        height: 50px;
    }

    .btn-custom:hover {
        background-color: #0056b3;
    }
    #preview {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .file-preview {
            width: 100px;
            height: 140px;
            border: 1px solid #ccc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            text-align: center;
            padding: 5px;
            background: #f9f9f9;
            position: relative;
        }
        .file-preview img {
            width: 50px;
            height: 50px;
        }
        .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
            font-size: 14px;
            line-height: 16px;
            text-align: center;
        }
        #error-message {
            color: red;
            margin-top: 5px;
        }
        .file-preview {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        padding: 5px;
        border-radius: 5px;
        width: fit-content;
    }

    .remove-btn {
        background: red;
        color: white;
        border: none;
        cursor: pointer;
        padding: 5px;
        border-radius: 50%;
    }
    .file-preview {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        padding: 5px;
        border-radius: 5px;
        width: fit-content;
    }

    .remove-btn {
        background: red;
        color: white;
        border: none;
        cursor: pointer;
        padding: 5px;
        border-radius: 50%;
    }

    a {
        color: blue;
        text-decoration: underline;
        cursor: pointer;
    }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .header h3 {
                font-size: 24px;
            }

            .form-label {
                font-size: 14px;
            }

            input, textarea {
                font-size: 14px;
            }

            table {
                font-size: 14px;
            }

            .checkbox-container {
                flex-direction: column;
            }
        }

    </style>
</head>
<body>
    <div class="container">
    <div class="header">
        <h3 class="text-dark"> เปิดบิลสินค้า </h3>
    <div class="mb-3">
        <label class="form-label">เลขที่ SO :</label>
        <form id="soSearchForm">
            <div style="display: flex; justify-content: space-between;">
                <input type="text" class="form-control" id="so_number" name="so_number" style="width: 90% ;" required>
            </div>
        </form>
    </div>

    <form id="billForm">

                <input type="hidden" name="so_id" id="so_id" value="">
                <div class="input-container">
                <div>
                    <label>ผู้เปิดบิล :</label>
                    <input type="text" id="emp_name" name="emp_name" value="{{ session('emp_name', 'Guest') }}">
                </div>

                <div>
                    <label>ผู้ขาย :</label>
                    <input type="text" id="sale_name" name="sale_name">
                </div>

                <div>
                    <label>รหัสลูกค้า :</label>
                    <input type="text" id="customer_id" name="customer_id" readonly>
                </div>
            </div>
            <div class="input-container1">
                <div>
                    <label>ชื่อบริษัท :</label>
                    <input type="text" id="customer_name" name="customer_name" readonly>
                </div>

                <div>
                    <label>เบอร์ติดต่อ :</label>
                    <input type="text" id="customer_tel" name="customer_tel">
                </div>

                <div>
                    <label for="po_document">อัปโหลดไฟล์ PDF :</label>
                    <input type="file" id="po_document" name="po_document" accept=".pdf" multiple>
                    <p id="error-message" style="color: red;"></p>
                </div>
                <div id="preview"></div>
                
            <br>

            </div>
                <div class="form-label">
                <div>
                    <label>ที่อยู่จัดส่ง :</label>
                    <input type="text" id="customer_address" name="customer_address"style="width: 75%">
                </div>
                
                <label>ละติจูด ลองจิจูด :</label>
                    <div style="display: flex; justify-content: space-between; width: 90%;" >
                        <input type="text" id="customer_la_long" name="customer_la_long">
                        <button type="button" class="btn-custom" onclick="openGoogleMaps()">Google Maps</button>
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
                                        <tr class="">
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
                                        <button type="button" class="btn btn-success insert-btn">เพิ่มสินค้า</button>
                                    </div>
                                    

                                    
                                    <label for="additional_notes">แจ้งเพิ่มเติม</label>
                                    <textarea id="additional_notes" name="additional_notes" rows="4"></textarea>
                                    

                                    <div style="display: flex; justify-content: center; margin-top: 20px;">
                                <button type="button" id="submitBill" class="btn btn-success" 
                                style="font-size: 18px; padding: 15px 30px; width: 200px; height: 50px;">
                                    เปิดบิล
                                </button>
            </div>

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

    {{-- api --}}
    <script>
        // ดึงค่า so_num จาก URL
        const urlParams = new URLSearchParams(window.location.search);
        const soNum = urlParams.get('so_num');

        // ถ้ามีค่า so_num ใน URL ให้ดึงข้อมูล SO อัตโนมัติ
        if (soNum) {
            document.getElementById('so_number').value = soNum; // แสดงค่า so_num ใน input
            fetchSODetails(soNum); // เรียกฟังก์ชันดึงข้อมูล SO
        }

        // ฟังก์ชันดึงข้อมูล SO จาก API
        async function fetchSODetails(soNum) {
            try {
                let response = await fetch(`http://server_update:8000/api/getSOHD?SONum=SO${soNum}`);
                if (!response.ok) {
                    throw new Error("เกิดข้อผิดพลาดในการโหลดข้อมูล");
                }

                let data = await response.json();
                console.log("API Response:", data); // ตรวจสอบข้อมูล API 

                if (!Array.isArray(data) || data.length === 0 || !data[0].CustID) {
                    alert("ไม่พบข้อมูลที่ตรงกับเลขที่ SO นี้");
                    return;
                }

                // กำหนดค่าลงในฟอร์ม
                document.getElementById("customer_id").value = data[0].CustID || 'ไม่พบข้อมูล';
                document.getElementById("customer_name").value = data[0].CustName || 'ไม่พบข้อมูล';
                document.getElementById("so_id").value = data[0].SONum || '';

                // Format the ShipDate to "วัน-เดือน-ปี"
                let shipDate = data[0].ShipDate;
                if (shipDate) {
                    let formattedDate = new Date(shipDate);
                    let day = formattedDate.getDate().toString().padStart(2, '0'); // Ensure 2 digits
                    let month = (formattedDate.getMonth() + 1).toString().padStart(2, '0'); // Month is 0-indexed
                    let year = formattedDate.getFullYear();
                    document.getElementById("date_of_dali").value = `${day}-${month}-${year}`;
                }

            } catch (error) {
                console.error('Error fetching data:', error);
                alert('เกิดข้อผิดพลาดในการดึงข้อมูล');        
            }
        }
    </script>

    {{-- ดูpdfหลายหน้า --}}
    <script>
            const input = document.getElementById("po_document");
            const preview = document.getElementById("preview");
            const errorMessage = document.getElementById("error-message");

            let selectedFiles = [];

            input.addEventListener("change", function () {
                const newFiles = Array.from(input.files);

                // รวมไฟล์เก่ากับไฟล์ใหม่
                let totalFiles = [...selectedFiles, ...newFiles];

                if (totalFiles.length > 5) {
                    errorMessage.textContent = "อัปโหลดได้สูงสุด 5 ไฟล์";
                    return;
                }

                selectedFiles = totalFiles;
                errorMessage.textContent = "";
                preview.innerHTML = ""; // เคลียร์ตัวอย่างก่อนแสดงใหม่

                updatePreview();
                updateFileInput();
            });

            function updatePreview() {
                preview.innerHTML = ""; // ล้างตัวอย่างทั้งหมดก่อนสร้างใหม่

                selectedFiles.forEach((file, index) => {
                    const div = document.createElement("div");
                    div.classList.add("file-preview");

                    const img = document.createElement("img");
                    img.src = "https://cdn-icons-png.flaticon.com/512/337/337946.png"; // ไอคอน PDF
                    img.alt = "PDF Preview";
                    img.width = 40;

                    const fileName = document.createElement("p");
                    fileName.textContent = file.name;

                    // สร้างลิงก์เปิดไฟล์ PDF
                    const fileLink = document.createElement("a");
                    fileLink.textContent = "ดูไฟล์";
                    fileLink.href = "#";
                    fileLink.style.marginLeft = "10px";
                    fileLink.onclick = function (event) {
                        event.preventDefault();
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const pdfWindow = window.open("");
                            pdfWindow.document.write(
                                `<iframe src="${e.target.result}" width="100%" height="100%"></iframe>`
                            );
                        };
                        reader.readAsDataURL(file);
                    };

                    // ปุ่มลบไฟล์
                    const removeBtn = document.createElement("button");
                    removeBtn.textContent = "×";
                    removeBtn.classList.add("remove-btn");
                    removeBtn.onclick = function () {
                        selectedFiles.splice(index, 1);
                        updatePreview();
                        updateFileInput();
                    };

                    div.appendChild(img);
                    div.appendChild(fileName);
                    div.appendChild(fileLink);
                    div.appendChild(removeBtn);
                    preview.appendChild(div);
                });
            }

            function updateFileInput() {
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach((file) => dataTransfer.items.add(file));
                input.files = dataTransfer.files;
            }
    </script>

</body>
</html>
 