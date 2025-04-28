<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
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
        border-bottom: 2px solid #3f865d;
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
        background-color: #3f865d ;
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
</style>



    </style>
</head>
<body>
    <div class="container">
    <div class="header">
        <h2 class="text-dark"> เปิดบิลสินค้า </h2>
    <div class="mb-3">
        <label class="form-label">เลขที่ SO :</label>
        <form id="soSearchForm">
            <div style="display: flex; justify-content: space-between;">
                <input type="text" class="form-control" id="so_number" name="so_number" style="width: 100% ;" required>
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
            <label for="po_document">เลขที่ PO</label>
            <input type="text" id="ponum" name="ponum" readonly>
        </div>

        <div>
            <label>ผู้ขาย :</label>
            <input type="text" id="sale_name" name="sale_name"readonly>
        </div>

        <div>
            <label>อัปโหลดเอกสาร PO :</label>
            <input type="file" id="POdocument"> 
        </div>
        <div>
            <label>รหัสลูกค้า :</label>
            <input type="text" id="customer_id" name="customer_id" readonly>
        </div>
        <div>
            <label>ชื่อบริษัท :</label>
            <input type="text" id="customer_name" name="customer_name" readonly>
        </div>

        <div>
            <label>เบอร์ติดต่อ :</label>
            <input type="text" id="customer_tel" name="customer_tel">
        </div>

        
        <div class="form-group">
            <label for="formtype">ประเภทเอกสาร</label>
            <select id="formtype" name="formtype" required>
                <option value="1">1</option> 
                <option value="2">2</option> 
                <option value="3">3</option> 
                <option value="4">4</option> 
           
            </select>
        </div>
                   <!-- แสดงรูปหรือ PDF -->
        <div id="filePreviewContainer">
            <iframe id="pdfPreview" width="100%" height="300px" style="display: none; border: 1px solid #ccc;"></iframe>
            
        </div>
    </div>
    <div class="form-label">

    <div style="margin-bottom: 20px;">
    <label for="customer_address">ที่อยู่จัดส่ง :</label>
    <textarea id="customer_address" name="customer_address" rows="4" style="width: 100%; padding: 10px; font-size: 16px; border-radius: 10px; border: 1px solid #ccc;"></textarea>
    </div>

        
        <label>ละติจูด ลองจิจูด :</label>
        <div style="display: flex; justify-content: space-between; width: 100%;" >
            <input type="text" id="customer_la_long" name="customer_la_long">
            <button type="button" class="btn-custom" onclick="openGoogleMaps()">Google Maps</button>
        </div>

        <div class="mb-3">
            <label class="form-label">แผนที่ :</label>
            <iframe id="mapFrame" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            
        </div>

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

        <div class="form-section">
            <div class="form-group">
                <label for="date_of_dali">วันกำหนดส่ง</label>
                <input type="text" id="date_of_dali" name="date_of_dali" readonly>
            </div>

            <div class="form-group">
                <label for="billtype">ประเภทบิล</label>
                <select id="billtype" name="billtype" required>
                    <option value="ขายเชื่อ">ขายเชื่อ</option> 
                    <option value="ขายสด">ขายสด</option> 
                </select>
            </div>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>เลือกจัดส่ง</th>
                    <th>รหัสสินค้า</th>
                    <th>รายการ</th>
                    <th>จำนวน</th>
                    <th>ราคาต่อหน่วย</th>
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
        
        <label for="additional_notes">แจ้งเพิ่มเติม</label>
        <textarea id="notes" name="notes" rows="4"></textarea>

        <div style="display: flex; justify-content: center; margin-top: 20px;">
            <button type="button" id="submitBill" class="btn btn-success" 
            style="font-size: 18px; padding: 15px 30px; width: 200px; height: 50px;">
                เปิดบิล
            </button>
        </div>
    </form>

    <script>
document.getElementById('submitBill').addEventListener('click', async function (event) {
    event.preventDefault();

    let formData = new FormData(document.getElementById('billForm'));                         
    if (convertedPDFBlob) {
    formData.append('POdocument', convertedPDFBlob, originalFilename || 'upload.pdf');
}


    // ตรวจสอบว่ามีสินค้าอย่างน้อย 1 รายการถูกเลือก
    let hasSelectedItems = false;
    document.querySelectorAll('input[name="status[]"]:checked').forEach((checkbox) => {
        hasSelectedItems = true;
    });

    if (!hasSelectedItems) {
        alert("กรุณาเลือกสินค้าอย่างน้อย 1 รายการ");
        return;
    }

    // รับข้อมูลสินค้าที่ถูกเลือก
    let itemRows = document.querySelectorAll('table tbody tr');
    itemRows.forEach((row, index) => {
        let itemStatus = row.querySelector('input[name="status[]"]').checked ? 1 : 0;

        if (itemStatus) { // เฉพาะสินค้าที่เลือก (checked)
            let itemId = row.querySelector('input[name="item_id[]"]').value;
            let itemName = row.querySelector('input[name="item_name[]"]').value;
            let itemQuantity = row.querySelector('input[name="item_quantity[]"]').value;
            let unit_price = row.querySelector('input[name="unit_price[]"]').value;

            // เก็บค่าลงใน FormData
            formData.append(`item_id[${index}]`, itemId);
            formData.append(`item_name[${index}]`, itemName);
            formData.append(`item_quantity[${index}]`, itemQuantity);
            formData.append(`unit_price[${index}]`, unit_price);
            formData.append(`status[${index}]`, itemStatus);
        }
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
            window.location.href = '/SOlist';
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
                    <td><input type="text" class="form-control1" name="item_id[]"></td>
                    <td><input type="text" class="form-control1" name="item_name[]"></td>
                    <td>
                        <input type="number" class="form-control1 item_quantity" name="item_quantity[]" >
                    </td>
                    <td>
                        <input type="number" class="form-control1 " name="unit_price[]" >
                    </td>
                    <td><button type="button" class="btn btn-danger delete-btn">ลบ</button></td>
                `;
                tableBody.appendChild(newRow);
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
                let response = await fetch(`http://server_update:8000/api/getSODetail?SONum=${soNum}`);
                if (!response.ok) {
                    throw new Error("เกิดข้อผิดพลาดในการโหลดข้อมูล");
                }

                let data = await response.json();
                console.log("API Response:", data); // ตรวจสอบข้อมูล API 

                if (!data.SoDetail || data.SoDetail.length === 0) {
                    alert("ไม่พบข้อมูลที่ตรงกับเลขที่ SO นี้: " + soNum);
                    return;
                }

                const soDetails = data.SoDetail;
                const SoStatus = data.SoStatus;

                // แสดงข้อมูลทั่วไป
                document.getElementById('so_id').value = SoStatus.SONum;  
                document.getElementById('ponum').value = soDetails.CustPONo;  
                document.getElementById('customer_id').value = SoStatus.CustID; 
                document.getElementById('customer_name').value = soDetails.CustName;  
                document.getElementById('customer_address').value = 
                [soDetails.ShipToAddr1,soDetails.CustAddr1, soDetails.ContDistrict, soDetails.ContAmphur, soDetails.ContProvince, soDetails.ContPostCode]
                .filter(Boolean) // กรองค่าที่เป็น null หรือ undefined หรือว่าง
                .join(', ');
                document.getElementById('customer_tel').value = soDetails.ContTel;  
                document.getElementById('sale_name').value = SoStatus.createdBy; 
         

                // แสดงวันที่จัดส่ง
                let deliveryDate = SoStatus.DeliveryDate;
                if (deliveryDate) {
                    let formattedDate = new Date(deliveryDate);
                    let day = formattedDate.getDate().toString().padStart(2, '0');
                    let month = (formattedDate.getMonth() + 1).toString().padStart(2, '0');
                    let year = formattedDate.getFullYear();
                    document.getElementById("date_of_dali").value = `${day}-${month}-${year}`;
                }

                const SOLists = data.SOLists; 
const tableBody = document.querySelector('#detail');

SOLists.forEach((soItem) => {  
    if (soItem.ms_sodt) { // ตรวจสอบว่ามีข้อมูล ms_sodt หรือไม่
        soItem.ms_sodt.forEach((item) => { 
            let newRow = document.createElement('tr');
            const safeGoodName = item.GoodName.replace(/"/g, '&quot;');
            newRow.innerHTML = `
                <td><input type="checkbox" class="form-control1" name="status[]"></td>
                <td><input type="text" class="form-control1" name="item_id[]" value="${item.GoodID}"readonly></td>
                <td><input type="text" class="form-control1" name="item_name[]" value="${safeGoodName}"readonly></td>
                <td><input type="text" class="form-control1 item_quantity" name="item_quantity[]" value="${item.GoodQty2}" ></td>
                <td><input type="text" class="form-control1" name="unit_price[]" value="${item.GoodPrice2}"readonly></td>
                <td><button type="button" class="btn btn-danger delete-btn">ลบ</button></td>
            `;
            tableBody.appendChild(newRow);
        });
    }
});
            } catch (error) {
                console.error(error);
            }
        }
        
    </script>

<script>
    let convertedPDFBlob = null; // PDF Blob ที่จะถูกส่งไป backend
    let originalFilename = ''; // เก็บชื่อไฟล์ต้นฉบับไว้ตั้งชื่อ

    document.getElementById('POdocument').addEventListener('change', async function(event) {
        const file = event.target.files[0];
        const pdfPreview = document.getElementById('pdfPreview');

        if (file) {
            const fileType = file.type;
            originalFilename = file.name;

            pdfPreview.style.display = 'none';
            convertedPDFBlob = null;

            if (fileType === 'application/pdf') {
                convertedPDFBlob = file;
                const fileURL = URL.createObjectURL(file);
                pdfPreview.src = fileURL;
                pdfPreview.style.display = 'block';
            } else if (fileType.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = async function(e) {
                    const imgData = e.target.result;
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF();
                    const img = new Image();
                    img.src = imgData;

                    img.onload = function() {
                    const imgWidth = 190;
                    const imgHeight = (img.height / img.width) * imgWidth;
                    pdf.addImage(img, 'JPEG', 10, 10, imgWidth, imgHeight);

                    convertedPDFBlob = pdf.output('blob'); // <-- ได้ blob แล้ว
                    const pdfURL = URL.createObjectURL(convertedPDFBlob);
                    pdfPreview.src = pdfURL;
                    pdfPreview.style.display = 'block';
                };
                };
                reader.readAsDataURL(file);
            } else {
                alert('ไฟล์ที่คุณอัปโหลดไม่รองรับ กรุณาอัปโหลด PDF หรือรูปภาพ');
                event.target.value = '';
            }
        }
    });

    // ฟังก์ชันสำหรับส่งไป backend
    async function uploadFile() {
    const formData = new FormData();

    if (convertedPDFBlob) {
        formData.append("POdocument", convertedPDFBlob);
        
    }

    try {
        const response = await fetch('route("insert.post")', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        alert("อัปโหลดสำเร็จ: " + JSON.stringify(result));
    } catch (err) {
        console.error("Upload error", err);
        alert("เกิดข้อผิดพลาดในการอัปโหลด");
    }
}
</script>

<script>
</script>

</body>
</html>
 