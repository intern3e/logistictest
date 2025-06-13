<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/insertdata.blade.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <title>เปิดบิลสินค้า</title>

</head>
<body>
<div class="container">
  <div class="header">
    <h2 class="text-dark">เปิดบิลสินค้า</h2>

    <!-- SO และ บิล ส่งของ ให้อยู่บรรทัดเดียวกัน -->
    <form id="billForm">
      <div class="mb-3" style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
        <!-- SO -->
        <div style="flex: 1; min-width: 200px;">
          <label class="form-label" for="so_number">เลขที่ SO :</label>
          <input type="text" class="form-control" id="so_number" name="so_number" style="width: 100%;" required readonly>
        </div>

        <!-- บิล -->
        <div style="flex: 1; min-width: 200px;">
          <label for="billid">เลขที่บิลส่งของ:</label>
          <input type="text" id="billid" name="billid" style="width: 100%;" readonly required>
        </div>
      </div>

      <!-- ที่เหลือของฟอร์ม ใส่ต่อได้เลย -->
      <input type="hidden" name="so_id" id="so_id" value="">

      <!-- ส่วนอื่นๆ คงเดิม -->

       
   <div class="form-row">
    <div class="inline-group">
        <label>ผู้เปิดบิล :</label>
        <input type="text" id="emp_name" name="emp_name" value="{{ session('emp_name', 'Guest') }}" readonly>
    </div>
    
    <div class="inline-group">
        <label>ผู้ขาย :</label>
        <input type="text" id="sale_name" name="sale_name" readonly>
    </div>

    <div class="inline-group">
        <label for="ponum">เลขที่ PO :</label>
        <input type="text" id="ponum" name="ponum" readonly>
    </div>

    <div class="inline-group">
        <label for="billtype">ประเภทบิล :</label>
        <input type="text" id="billtype" name="billtype" placeholder="ประเภทบิล" readonly>
    </div>

                 
</div>


<script>
// สมมติว่าค่า billid ถูก set มาแล้ว
const billidInput = document.getElementById('billid');
const billtypeInput = document.getElementById('billtype');

// ฟังก์ชันตรวจสอบ
function checkBillid() {
  const billidValue = billidInput.value.toLowerCase().trim(); // แปลงเป็น lowercase และตัดช่องว่างข้างหน้า-หลัง
  if (billidValue.startsWith('cs')) {  // ถ้า **ขึ้นต้น** ด้วย 'cs'
    billtypeInput.value = 'ขายสด';    // ตั้งค่าเป็น "ขายสด"
  } else if (billidValue.length > 0) {
    billtypeInput.value = 'ขายเชื่อ'; // ถ้าไม่ขึ้นต้นด้วย 'cs' แต่มีค่า ให้เป็น "ขายเชื่อ"
  } else {
    billtypeInput.value = '';          // ถ้าว่าง ให้เป็นค่าว่าง
  }
}

// เรียกตอนโหลดหน้า
window.addEventListener('load', () => {
  checkBillid();
});

// เช็คทุกครั้งที่เปลี่ยนค่า billid
billidInput.addEventListener('input', () => {
  checkBillid();
});
</script>


 <div class="mb-3" style="display: flex; flex-wrap: wrap; gap: 20px; align-items: center;">
    <div style="flex: 2; min-width: 150px;">
        <label for="customer_name">ชื่อบริษัท :</label>
        <input type="text" id="customer_name" name="customer_name" readonly style="width: 100%;">
    </div>

    <div style="flex: 1; min-width: 100px;">
        <label for="customer_tel">เบอร์ติดต่อ :</label>
        <input type="text" id="customer_tel" name="customer_tel" style="width: 100%;">
    </div>

    <div style="flex: 1; min-width: 50px;">
        <label for="date_of_dali">วันกำหนดส่ง :</label>
        <input type="text" id="date_of_dali" name="date_of_dali" style="width: 40%;" readonly required >
    </div>
</div>

       <div class="mb-3" style="display: flex; gap: 20px; align-items: center;">
    <!-- ฝั่งอัปโหลดเอกสาร -->
    <div style="flex: 1;">
        <label for="POdocument">อัปโหลดเอกสาร PO :</label>
        <input type="file" id="POdocument" name="POdocument" style="width: 100%;">
    </div>

    <!-- ฝั่งเลือกแบบฟอร์ม -->
    <div style="flex: 1;">
        <label for="formtype">แบบฟอร์มเอกสาร :</label>
        <select id="formtype" name="formtype" style="width: 100%;" required>
            <option value="ไม่มีข้อมูล" disabled selected>ไม่มีข้อมูล</option>
            <option value="บิล/PO3">บิล/PO3</option>
            <option value="บิล/PO3/วางบิล">บิล/PO3/วางบิล</option>
            <option value="บิล/PO3/วางบิล/สำเนาหน้าบิล2">บิล/PO3/วางบิล/สำเนาหน้าบิล2</option>
            <option value="บิล/PO3/สำเนาบิล2">บิล/PO3/สำเนาบิล2</option>
            <option value="บิล/PO3/บัญชี">บิล/PO3/บัญชี</option>
        </select>
    </div>
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


            
    <div class="form-label">

    <div >
    <label for="customer_address">ที่อยู่จัดส่ง :</label>
    {{-- <textarea id="customer_address" name="customer_address" rows="4" readonly required style="width: 100%; padding: 10px; font-size: 14px; border-radius: 10px; border: 1px solid #ccc; height: 50px" ></textarea> --}}
    <input type="text" id="customer_address" name="customer_address" style="width: 100%; padding: 10px; font-size: 14px; border-radius: 6px; border: 1px solid #ccc; height: 30px"  readonly required >
    </div>
        <label>ละติจูด ลองจิจูด :</label>
        <div style="display: flex; justify-content: space-between; width: 100%;" >
            <input type="text" id="customer_la_long" name="customer_la_long">
        </div>
         <button type="button" class="btn-custom" onclick="openGoogleMaps()">Google Maps</button>

      <div class="mb-3" style="display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">

    <!-- แสดงแผนที่ -->
    <div style="flex: 1;">
        <label class="form-label">แผนที่ :</label>
        <iframe id="mapFrame"
            style="width: 140%; height: 300px; border: 0; border-radius: 8px;"
            allowfullscreen=""
            loading="lazy"
        ></iframe>
    </div>

    <!-- แสดง PDF -->
    <div style="flex: 1; display: flex; justify-content: flex-end;">
        <div>
            <label class="form-label">เอกสาร PDF :</label>
            <iframe id="pdfPreview"
                style="width: 100%; height: 300px; border: 1px solid #ccc; border-radius: 8px;"
                allowfullscreen
            ></iframe>
        </div>
    </div>
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

  </div>
          

        <table class="table table-bordered table-striped">
            <thead>
                 <tr>
            <th style="width: 15%;">รหัสสินค้า</th>
            <th style="width: 50%;">รายการ</th>
            <th style="width: 15%;">จำนวน</th>
            <th style="width: 20%;">ราคาต่อหน่วย</th>
               </tr>
            </thead>
            <tbody id="detail"></tbody>
        </table>
        </div>
          <label for="additional_notes">รายละเอียดเพิ่มเติม :</label>
        <textarea id="notes" name="notes" rows="2" style="font-size: 14px; padding: 6px; height: 60px;"></textarea>
        
        <div style="display: flex; justify-content: center; margin-top: 20px;">
            <button type="button" id="submitBill" class="btn btn-success" 
            style="font-size: 18px; padding: 15px 30px; width: 200px; height: 50px;">
                บันทึก
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

    let itemRows = document.querySelectorAll('table tbody tr');
    itemRows.forEach((row, index) => {
        let itemId = row.querySelector('input[name="item_id[]"]').value;
        let itemName = row.querySelector('input[name="item_name[]"]').value;
        let itemQuantity = row.querySelector('input[name="item_quantity[]"]').value;
        let unit_price = row.querySelector('input[name="unit_price[]"]').value;

        formData.append(`item_id[${index}]`, itemId);
        formData.append(`item_name[${index}]`, itemName);
        formData.append(`item_quantity[${index}]`, itemQuantity);
        formData.append(`unit_price[${index}]`, unit_price);
        formData.append(`status[${index}]`, 1); // ถือว่าเลือกทุกตัว
    });

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

// ======= ฟังก์ชันเปิด Google Maps ในหน้าต่างใหม่ด้านขวา =======
let mapWindow;
let closeTimer;

function openGoogleMaps() {
    const screenWidth = window.screen.width;
    const screenHeight = window.screen.height;
    const windowWidth = 800;
    const windowHeight = 600;

    const leftPosition = screenWidth - windowWidth;
    const topPosition = (screenHeight - windowHeight) / 2;

    mapWindow = window.open(
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

            // แสดงข้อมูลทั่วไป
            const soDetails = data.SoDetail;
            const SoStatus = data.SoStatus;
            document.getElementById('so_id').value = SoStatus.SONum;  
            document.getElementById('ponum').value = soDetails.CustPONo;  
            document.getElementById('customer_id').value = SoStatus.CustID; 
            fetchFormType();
            document.getElementById('customer_name').value = soDetails.CustName;  
            document.getElementById('customer_address').value = 
                [soDetails.ShipToAddr1, soDetails.CustAddr1, soDetails.ContDistrict, soDetails.ContAmphur, soDetails.ContProvince, soDetails.ContPostCode]
                .filter(Boolean)
                .join(', ');
            document.getElementById('customer_la_long').value = 
                [soDetails.Latitude, soDetails.Longitude]
                .filter(Boolean)
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

            // ดึงค่า billid จาก input
            const billidInput = document.getElementById('billid').value.trim();
            if (!billidInput) {
                alert("กรุณากรอก Bill ID");
                return;
            }

            const tableBody = document.querySelector('#detail');
            tableBody.innerHTML = ''; // ล้างตารางเดิมก่อน

            let itemCounter = 1; // นับรายการสินค้า

            // ตรวจสอบ Bills มีข้อมูลไหม
            if (!data.Bills || data.Bills.length === 0) {
                alert("ไม่พบข้อมูล Bills");
                return;
            }

            // Bills เป็น array ของ object, เลือกตัวแรก
            const billsObj = data.Bills[0];

            // หา key ที่ตรงกับ billidInput
            const items = billsObj[billidInput];

            if (!items || items.length === 0) {
                alert(`ไม่พบข้อมูลสินค้าใน Bills ของ Bill ID: ${billidInput}`);
                return;
            }

            // วน loop แสดงแต่ละรายการใน items
            items.forEach(item => {
                let newRow = document.createElement('tr');

                // ป้องกันเครื่องหมาย " ในชื่อสินค้า
                const safeGoodName = item.GoodName.replace(/"/g, '&quot;');
                const itemId = `53-${String(itemCounter).padStart(4, '0')}`; // เช่น 53-0001

                newRow.innerHTML = `
                    <td style="text-align: center; vertical-align: middle;">
                        <input type="text" class="form-control1" name="item_id[]" value="${itemId}" readonly style="text-align: center;">
                    </td>
                    <td><input type="text" class="form-control1" name="item_name[]" value="${safeGoodName}" readonly></td>
                    <td style="text-align: center; vertical-align: middle;">
                        <input type="text" class="form-control1 item_quantity" name="item_quantity[]" value="${parseFloat(item.GoodQty2).toFixed(2)}" readonly style="text-align: center;">
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        <input type="text" class="form-control1" name="unit_price[]" value="${parseFloat(item.GoodPrice2).toFixed(2)}" readonly style="text-align: center;">
                    </td>

                `;
                tableBody.appendChild(newRow);
                itemCounter++;
            });

        } catch (error) {
            console.error(error);
        }
    }
</script>


<!-- <script>
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
</script> -->
<script>
    let convertedPDFBlob = null;
    let originalFilename = '';

    document.getElementById('POdocument').addEventListener('change', async function (event) {
        const file = event.target.files[0];
        const pdfPreview = document.getElementById('pdfPreview');

        if (!file) return;

        const fileType = file.type;
        originalFilename = file.name;
        pdfPreview.style.display = 'none';
        convertedPDFBlob = null;

        if (fileType === 'application/pdf') {
            // 🔁 Convert PDF -> Image -> New PDF (A4)
            const reader = new FileReader();
            reader.onload = async function (e) {
                const pdfData = new Uint8Array(e.target.result);
                const pdf = await pdfjsLib.getDocument({ data: pdfData }).promise;
                const page = await pdf.getPage(1);

                const viewport = page.getViewport({ scale: 2 });
                const canvas = document.createElement("canvas");
                const context = canvas.getContext("2d");
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                await page.render({ canvasContext: context, viewport: viewport }).promise;

                const imgData = canvas.toDataURL("image/jpeg");

                const { jsPDF } = window.jspdf;
                const newPdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                const pageWidth = 210;
                const pageHeight = 297;

                let imgWidth = pageWidth - 20;
                let imgHeight = (canvas.height / canvas.width) * imgWidth;
                if (imgHeight > pageHeight - 20) {
                    imgHeight = pageHeight - 20;
                    imgWidth = (canvas.width / canvas.height) * imgHeight;
                }

                const x = (pageWidth - imgWidth) / 2;
                const y = (pageHeight - imgHeight) / 2;

                newPdf.addImage(imgData, 'JPEG', x, y, imgWidth, imgHeight);
                convertedPDFBlob = newPdf.output('blob');

                const pdfURL = URL.createObjectURL(convertedPDFBlob);
                pdfPreview.src = pdfURL;
                pdfPreview.style.display = 'block';
            };
            reader.readAsArrayBuffer(file);

        } else if (fileType.startsWith('image/')) {
            // 🖼 Image -> PDF A4
            const reader = new FileReader();
            reader.onload = function (e) {
                const imgData = e.target.result;
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                const img = new Image();
                img.src = imgData;
                img.onload = function () {
                    const pageWidth = 210;
                    const pageHeight = 297;
                    let imgWidth = pageWidth - 20;
                    let imgHeight = (img.height / img.width) * imgWidth;

                    if (imgHeight > pageHeight - 20) {
                        imgHeight = pageHeight - 20;
                        imgWidth = (img.width / img.height) * imgHeight;
                    }

                    const x = (pageWidth - imgWidth) / 2;
                    const y = (pageHeight - imgHeight) / 2;

                    pdf.addImage(img, 'JPEG', x, y, imgWidth, imgHeight);
                    convertedPDFBlob = pdf.output('blob');

                    const pdfURL = URL.createObjectURL(convertedPDFBlob);
                    pdfPreview.src = pdfURL;
                    pdfPreview.style.display = 'block';
                };
            };
            reader.readAsDataURL(file);
        } else {
            alert("กรุณาอัปโหลด PDF หรือ รูปภาพเท่านั้น");
            event.target.value = '';
        }
    });
</script>

<script>
    document.getElementById('uploadPDF').addEventListener('change', async function (event) {
        const file = event.target.files[0];
        if (!file || file.type !== 'application/pdf') {
            alert("กรุณาอัปโหลดไฟล์ PDF เท่านั้น");
            return;
        }

        const reader = new FileReader();
        reader.onload = async function (e) {
            const pdfData = new Uint8Array(e.target.result);
            const pdf = await pdfjsLib.getDocument({ data: pdfData }).promise;
            const { jsPDF } = window.jspdf;
            const newPdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4',
                compress: true,
                pdfVersion: '1.4' // 👈 บังคับให้เป็น PDF เวอร์ชัน 1.4
            });

            const pageCount = pdf.numPages;

            for (let pageNum = 1; pageNum <= pageCount; pageNum++) {
                const page = await pdf.getPage(pageNum);
                const viewport = page.getViewport({ scale: 2 });

                const canvas = document.createElement("canvas");
                const context = canvas.getContext("2d");
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                await page.render({ canvasContext: context, viewport }).promise;

                const imgData = canvas.toDataURL("image/jpeg");

                const pageWidth = 210;
                const pageHeight = 297;
                let imgWidth = pageWidth - 20;
                let imgHeight = (canvas.height / canvas.width) * imgWidth;
                if (imgHeight > pageHeight - 20) {
                    imgHeight = pageHeight - 20;
                    imgWidth = (canvas.width / canvas.height) * imgHeight;
                }

                const x = (pageWidth - imgWidth) / 2;
                const y = (pageHeight - imgHeight) / 2;

                if (pageNum > 1) newPdf.addPage();
                newPdf.addImage(imgData, 'JPEG', x, y, imgWidth, imgHeight);
            }

            const blob = newPdf.output('blob');
            const url = URL.createObjectURL(blob);
            document.getElementById('pdfPreview').src = url;

            // หากต้องการดาวน์โหลด
            // newPdf.save("converted.pdf");
        };

        reader.readAsArrayBuffer(file);
    });
</script>
<script>
    window.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const billid = urlParams.get('billid');
        if (billid) {
            document.getElementById('billid').value = billid;
        }
    });
</script>
</body>
</html>
 