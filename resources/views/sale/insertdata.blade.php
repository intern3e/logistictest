<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/insertdata.blade.css') }}">
    <title>เปิดบิลสินค้า</title>

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
            <label for="formtype">แบบฟอร์มเอกสาร :</label>
            <select id="formtype" name="formtype" required>
                <option value="ไม่มีข้อมูล" disabled selected>ไม่มีข้อมูล</option>
                <option value="บิล/PO3">บิล/PO3</option>
                <option value="บิล/PO3/วางบิล">บิล/PO3/วางบิล</option>
                <option value="บิล/PO3/วางบิล/สำเนาหน้าบิล2">บิล/PO3/วางบิล/สำเนาหน้าบิล2</option>
                <option value="บิล/PO3/สำเนาบิล2">บิล/PO3/สำเนาบิล2</option>
                <option value="บิล/PO3/บัญชี">บิล/PO3/บัญชี</option>
            </select>
        </div>
        <script>
        function fetchFormType() {
            console.log('fetchFormType called'); // ตรวจสอบการเรียกฟังก์ชัน
        
            var customer_id = document.getElementById("customer_id").value;
        
            if (customer_id) {  // ตรวจสอบว่า customer_id มีค่า
                // ส่งคำขอไปยัง Controller
                fetch('/fetch-formtype', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ customer_id: customer_id })
                })
                .then(response => response.json())  // แปลงคำตอบเป็น JSON
                .then(data => {
                    console.log('Response from server:', data); // ตรวจสอบข้อมูลจากเซิร์ฟเวอร์
        
                    if (data.formtype) {  // ถ้าเจอ formtype จากเซิร์ฟเวอร์
                        document.getElementById("formtype").value = data.formtype;
                        document.getElementById("customer_la_long").value = data.customer_la_long;
                        updateMap();
                    } else {  // ถ้าไม่เจอ formtype
                        document.getElementById("formtype").value = 'ไม่มีข้อมูล';  // กำหนดค่าเป็น "ไม่มีข้อมูล"
                    }
                })
                .catch(error => console.error('Error:', error));  // จับข้อผิดพลาด
            } else {
                // หากไม่มี customer_id, กำหนดค่าให้เป็น "ไม่มีข้อมูล"
                document.getElementById("formtype").value = 'ไม่มีข้อมูล';
            }
        }
        </script>
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

        <div class="form-section">
            <div class="form-group">
                <label for="billid">เลขที่บิลส่งของ:</label>
                <input type="text" id="billid" name="billid" required>
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
    let formType = document.getElementById('formtype').value;
    formData.append('formtype', formType);


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
                // สมมติว่าคุณได้ customer_id จาก API
                document.getElementById('so_id').value = SoStatus.SONum;  
                document.getElementById('ponum').value = soDetails.CustPONo;  
                document.getElementById('customer_id').value = SoStatus.CustID; 
                fetchFormType();
                document.getElementById('customer_name').value = soDetails.CustName;  
                document.getElementById('customer_address').value = 
                [soDetails.ShipToAddr1,soDetails.CustAddr1, soDetails.ContDistrict, soDetails.ContAmphur, soDetails.ContProvince, soDetails.ContPostCode]
                .filter(Boolean) // กรองค่าที่เป็น null หรือ undefined หรือว่าง
                .join(', ');
                document.getElementById('customer_la_long').value = 
                [soDetails.Latitude,soDetails.Longitude,]
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
let itemCounter = 1; // ต้องอยู่ข้างนอก เพื่อให้รันต่อเนื่อง

SOLists.forEach((soItem) => {  
    if (soItem.ms_sodt) {
        soItem.ms_sodt.forEach((item) => { 
            let newRow = document.createElement('tr');

            const safeGoodName = item.GoodName.replace(/"/g, '&quot;');
            const itemId = `53-${String(itemCounter).padStart(4, '0')}`; // เช่น 53-0001

            newRow.innerHTML = `
                <td><input type="checkbox" class="form-control1" name="status[]"></td>
                <td><input type="text" class="form-control1" name="item_id[]" value="${itemId}" readonly></td>
                <td><input type="text" class="form-control1" name="item_name[]" value="${safeGoodName}" readonly></td>
                <td><input type="text" class="form-control1 item_quantity" name="item_quantity[]" value="${item.GoodQty2}"></td>
                <td><input type="text" class="form-control1" name="unit_price[]" value="${item.GoodPrice2}" readonly></td>
                <td><button type="button" class="btn btn-danger delete-btn">ลบ</button></td>
            `;

            tableBody.appendChild(newRow);
            itemCounter++; // เพิ่มลำดับต่อรายการ
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



</body>
</html>
 