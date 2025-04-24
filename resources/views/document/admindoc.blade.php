<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
       /* ===== Base ===== */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f5f7fa;
    margin: 0;
    padding: 0;
    color: #2c3e50;
}

/* ===== Header ===== */
.header {
    background-color: #343a40;
    padding: 15px 30px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.2rem;
    border-radius: 8px;
    margin: 20px auto;
    width: 90%;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.header button {
    background-color: #e74c3c;
    color: white;
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s;
}

.header button:hover {
    background-color: #c0392b;
    transform: translateY(-2px);
}

/* ===== Container ===== */
.container {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    width: 90%;
    margin: 20px auto;
}

/* ===== Table Container ===== */
.table-container {
    background: #ffffff;
    margin: 20px auto;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    overflow-x: auto;
    width: 95%;
    padding: 20px;
}

/* ===== Table ===== */
table {
    width: 100%;
    border-collapse: collapse;
    text-align: center;
    font-size: 0.95rem;
}

th, td {
    padding: 15px;
    border: 1px solid #e0e0e0;
    white-space: normal;
}

th {
    background-color: #343a40;
    color: white;
    text-transform: uppercase;
}

tr:nth-child(odd) {
    background-color: #f8f9fa;
}

tr:hover {
    background-color: #e1e5ea;
    transition: background 0.3s;
}

td a {
    color: #27ae60;
    font-weight: bold;
    text-decoration: none;
}

td a:hover {
    text-decoration: underline;
}

/* ===== Top Section ===== */
.top-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0 5% 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.top-section label {
    font-weight: bold;
}

.top-section input {
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 1rem;
}

.top-section button {
    padding: 8px 15px;
    background: #27ae60;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s;
}

.top-section button:hover {
    background: #219150;
    transform: translateY(-2px);
}

/* ===== Filter Container ===== */
.filter-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-container input {
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.filter-container button {
    background-color: #2ecc71;
    color: white;
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s;
}

.filter-container button:hover {
    background-color: #27ae60;
}

/* ===== Button Group ===== */
.button-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.button-group button {
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: bold;
    border: none;
    background-color: #f39c12;
    color: white;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    transition: background 0.3s, transform 0.2s;
}

.button-group button:hover {
    background-color: #e67e22;
    transform: scale(1.05);
}

/* ===== Search Box ===== */
.search-box {
    display: flex;
    align-items: center;
    max-width: 250px;
    flex-grow: 1;
}

.search-box input {
    width: 100%;
    padding: 8px;
    border: none;
    border-radius: 5px;
    background-color: #e1e5ea;
    font-size: 1rem;
}

/* ===== Links ===== */
.link {
    color: #16a085;
    font-weight: bold;
    text-decoration: none;
}

.link:hover {
    text-decoration: underline;
}

/* ===== Popup ===== */
.popup-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

.popup-content {
    background: linear-gradient(to right, #f0f2f5, #dfe9f3);
    padding: 30px;
    border-radius: 10px;
    width: 90%;
    max-width: 800px;
    max-height: 80vh;
    overflow-y: auto;
    position: relative;
    text-align: center;
}

.close-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    cursor: pointer;
    font-size: 20px;
    font-weight: bold;
    color: #333;
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .header, .container, .table-container {
        width: 95%;
    }

    .top-section {
        flex-direction: column;
        align-items: stretch;
    }

    .button-group {
        justify-content: center;
    }
}

    </style>
</head>
<body>
    <div class="header">
        <h2>ระบบจัดเส้นทางเอกสารเพิ่มเติม</h2>
        <a href="adminSO"><button class="btn-so">หน้าหลัก</button></a>
    </div>

    <div class="container">
        <div class="top-section">
    <form method="GET" action="{{ route('document.admindoc') }}" class="filter-form" id="autoSearchForm">
        <label for="date">📅 วันที่:</label>
        <input type="date" id="date" name="date" value="{{ request('date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
        <button type="submit" style="display: none;">ค้นหา</button>
    </form>


<script>
    const form = document.getElementById('autoSearchForm');
    const dateInput = document.getElementById('date');

    // ส่งฟอร์มเมื่อเปลี่ยนวันที่
    dateInput.addEventListener('change', () => {
        form.submit();
    });

    // ส่งฟอร์มอัตโนมัติเมื่อเข้าหน้าเว็บครั้งแรกเท่านั้น
    window.addEventListener('load', () => {
        if (!sessionStorage.getItem('hasAutoSubmitted')) {
            sessionStorage.setItem('hasAutoSubmitted', 'true');
            form.submit();
        }
    });
</script>


            <div class="button-group">
                <button onclick="createCSV()">ดาวน์โหลด CSV</button>
                <button onclick="window.location.href='historydoc'">📜 ประวัติเอกสาร</button>
            </div>
            
            <div class="search-box">
            <input type="text" id="search-input" placeholder=" ค้นหา เลขที่บิล" onkeyup="searchTable()">
        </div>
        
        </div>
        <div class="table-container">
    <table>
        <input type="checkbox" id="checkAll" onclick="toggleCheckboxes()"> ทั้งหมด
        <thead>
            <tr>
                <th>ปริ้นเอกสาร</th>
                <th>เลขที่บิล</th>
                <th>บริษัท</th>
                <th>ที่อยู่</th>
                <th>ละติจูด ลองจิจูด</th>
                <th>ผู้ติดต่อ</th>
                <th>เบอร์โทร</th>
                <th>ประเภทงาน</th>
                <th>ผู้เปิดบิล</th>
                <th>วันที่</th>
                <th>ข้อมูลรายละเอียด</th>
                <th>pdf</th>
            </tr>
        </thead>
        <tbody id="table-body">
            @foreach($docbill as $item)
            @if($item->status == 0)
            <tr>
                <td>
                    <input type="checkbox" class="form-control1" name="status[]" data-doc-detail-id="{{ $item->doc_id }}">
                </td>
                <td>{{ $item->doc_id }}</td>
                <td>{{ $item->com_name }}</td>
                <td>{{ $item->com_address }}</td>
                <td>{{ $item->com_la_long }}</td>
                <td>{{ $item->contact_name }}</td>
                <td>{{ $item->contact_tel}}</td>
                <td>{{ $item->doctype }}</td>
                <td>{{ $item->emp_name }}</td>
                <td>{{ \Carbon\Carbon::parse($item->time)->format('d/m/Y') }}</td>
                <td>
                <a href="javascript:void(0);" onclick="openPopup('{{ $item->doc_id }}', '{{ $item->com_name }}', '{{ $item->com_address }}', '{{ $item->contact_name }}', '{{ $item->contact_tel }}', '{{ $item->amount }}', '{{ $item->notes }}')">
                    เพิ่มเติม
                </a>

                </td>
                <td>
                    <button onclick="downloadRowPDF(this)" class="btn btn-sm btn-outline-danger">📄</button>
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>

    @if(isset($message))
    <br>
    <p style="text-align: center">{{ $message }}</p>
    @endif
</div>
        
        <!-- Popup -->
        <div class="popup-overlay" id="popup" style="display: none;">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup()">&times;</span>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>เลขที่บิล</th>
                                <th>บริษัท</th>
                                <th>ที่อยู่</th>
                                <th>ผู้ติดต่อ</th>
                                <th>เบอร์โทร</th>
                                <th>รวมทั้งหมด</th>
                            </tr>
                        </thead>
                        <tbody id="popup-body-1">
                        </tbody>
                    </table>
                    <br>
                    <table>
                <thead>     
                    <tr>
                        <th>ลำดับ</th>
                        <th>รายการ</th>
                        <th>จำนวน</th>
                        <th>ราคา/หน่วย</th>
                    </tr>
                </thead>
                <tbody id="popup-body">
                </tbody>
            </table>
             <br>
                <textarea id="popup-body-3" readonl style="width: 950px; height: 70px;" readonly>
                </textarea>
        </div> 
    </div>
</div>
        
        <script>
            function openPopup(doc_id,com_name,com_address,contact_name,contact_tel,amount,notes) {
                document.getElementById("popup").style.display = "flex"; // แสดง Popup
            
                let popupBody = document.getElementById("popup-body-1");
                popupBody.innerHTML = `
                    <tr>
                        <td>${doc_id}</td>
                        <td>${com_name}</td>
                        <td>${com_address}</td>
                        <td>${contact_name}</td>
                        <td>${contact_tel}</td>
                        <td>${amount}</td>
                    </tr>
                `;
                document.getElementById("popup-body-3").value = notes;
                let secondPopupBody = document.getElementById("popup-body");
                secondPopupBody.innerHTML = "<tr><td colspan='4'>กำลังโหลดข้อมูล...</td></tr>";
                
                // ดึงข้อมูลรายการสินค้าจาก Laravel Controller
                fetch(`/get-docbill-detail/${doc_id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            secondPopupBody.innerHTML = ""; // เคลียร์ข้อมูลเก่า
                            data.forEach((item, index) => {
                            secondPopupBody.insertAdjacentHTML("beforeend", `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.item_name}</td>
                                    <td>${item.quantity}</td>
                                    <td>${item.unit_price}</td>
                                </tr>
                            `);
                        });

                        } else {
                            secondPopupBody.innerHTML = "<tr><td colspan='4'>ไม่มีข้อมูล</td></tr>";
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching data:", error);
                        secondPopupBody.innerHTML = "<tr><td colspan='4'>เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>";
                    });
            }

    // ฟังก์ชันปิด Popup
    function closePopup() {
        document.getElementById("popup").style.display = "none"; // ซ่อน Popup
    }

            
            window.onclick = function(event) {
                let popup = document.getElementById("popup");
                if (event.target === popup) {
                    closePopup();
                }
            }
            </script>
    
    
    <script>
    
    function updateStatus(docDetailIds) {
        fetch('/update-statusdoc', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ docDetailIds: docDetailIds }) 
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
                console.log("Status updated successfully");
            } else {
                console.error("Failed to update status");   
            }
        })
        .catch(error => {
            console.error("Error updating status:", error);
        });
    }
    
    
    function searchTable() {
        let searchInput = document.getElementById("search-input").value.toLowerCase();
        let table = document.querySelector("table tbody");
        let rows = table.getElementsByTagName("tr");
    
        for (let i = 0; i < rows.length; i++) {
            let row = rows[i];
            let cells = row.getElementsByTagName("td");
    
            // Get the content of the second column (บิลลำดับ)
            let soDetailId = cells[1] ? cells[1].textContent.toLowerCase() : '';
    
            // Search for the text inside the selected column (บิลลำดับ)
            if (soDetailId.indexOf(searchInput) > -1) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }
    
        </script>
    
    <script>
         
         function createCSV() {
        const headers = [
            "เลขที่บิล", "เลขที่อ้างอิง", "ชื่อ", "ที่อยู่",
            "ละติจูดลองจิจูด", "ประเภทบิล", "ผู้เปิดบิล", "วันที่จัด"
        ];
    
        let data = [];
        let selecteddocDetailIds = []; // เก็บ so_detail_id ของแถวที่เลือก
    
        let checkboxes = document.querySelectorAll("input[type='checkbox']:checked");
    
        checkboxes.forEach(checkbox => {
            let row = checkbox.closest("tr");
            if (!row) return;
    
            let cells = row.querySelectorAll("td");
            let rowData = [];
    
            // ดึงข้อมูลจากแต่ละเซลล์ (ข้าม checkbox column)
            cells.forEach((cell, index) => {
                if (index > 0 && index <= 8) { 
                    rowData.push(`"${cell.textContent.trim()}"`);
                }
            });
    
            // ดึงค่า so_detail_id แล้วเก็บไว้
            let docDetailId = checkbox.getAttribute("data-doc-detail-id");
            if (docDetailId) {
                selecteddocDetailIds.push(docDetailId);
            }
    
            data.push(rowData.join(","));
        });
    
        if (data.length === 0) {
            alert("กรุณาเลือกข้อมูลที่ต้องการพิมพ์ CSV");
            return;
        }
    
        const csvContent = "\uFEFF" + [headers.join(","), ...data].join("\n");
    
        const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = "เอกสารเส้นทางเดินรถของเอกสารเพิ่มเติม.csv";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    
    
        if (selecteddocDetailIds.length > 0) {
            updateStatus(selecteddocDetailIds);
        }
    }
    
    
    function toggleCheckboxes() {
        var checkAllBox = document.getElementById('checkAll');
        var checkboxes = document.querySelectorAll('input[type="checkbox"]:not(#checkAll)');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = checkAllBox.checked;
        });
    }
    </script>
<script>
    
async function downloadRowPDF(button) {
    const { jsPDF } = window.jspdf;

    const row = button.closest("tr");
    const cells = row.querySelectorAll("td");

    const doc_id = cells[1].innerText.trim();
    const name = cells[2].innerText.trim();
    const address = cells[3].innerText.trim();
    const type = cells[7].innerText.trim();
    const emp = cells[8].innerText.trim();
    const revdate = cells[9].innerText.trim();
    const contact_tel = cells[6].innerText.trim();
    const contact_name = cells[5].innerText.trim();

    let popupAmount = '', popupNotes = '';

    const link = row.querySelector('a[onclick^="openPopup"]');
    if (link) {
        const onclickAttr = link.getAttribute('onclick');
        const args = [...onclickAttr.matchAll(/'([^']*)'/g)].map(match => match[1]);
        popupAmount = args[5] || '';
        popupNotes = args[6] || '';
    }

    let tableRowsHtml = '';
    try {
        const response = await fetch(`/get-docbill-detail/${doc_id}`);
        const data = await response.json();

        if (data.length > 0) {
    data.forEach((item, index) => {
        tableRowsHtml += `
            <tr>
                <td style="border: 1px solid #000; padding: 8px;">${index + 1}</td>
                <td style="border: 1px solid #000; padding: 8px;">${item.item_name}</td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.quantity}</td>
            </tr>
        `;
    });

        } else {
            tableRowsHtml = `
                <tr>
                    <td colspan="3" style="border: 1px solid #000; padding: 8px; text-align: center;">
                        ไม่มีข้อมูลสินค้า
                    </td>
                </tr>
            `;
        }
    } catch (error) {
        console.error("Error fetching data:", error);
        tableRowsHtml = `
            <tr>
                <td colspan="3" style="border: 1px solid #000; padding: 8px; text-align: center;">
                    เกิดข้อผิดพลาดในการโหลดข้อมูลสินค้า
                </td>
            </tr>
        `;
    }

    const pdfContainer = document.createElement("div");
    pdfContainer.style.position = "relative";
    pdfContainer.style.padding = "20px";
    pdfContainer.style.width = "1123px";
    pdfContainer.style.background = "#fff";
    pdfContainer.style.fontFamily = "'Arial', sans-serif";
    pdfContainer.style.lineHeight = "1.6";

    pdfContainer.innerHTML  = 
    `
<div style="display: flex; flex-direction: column; min-height: 1650px;">

  <!-- ส่วนหัวเอกสาร -->

<div style="flex: 1;">
  <div style="display: flex; flex-direction: column; margin-bottom: 5px; width: calc(100% - 200px); position: relative; top: -20px; gap: 10px;">
    <!-- Title and Bill Type in the same row -->
  <div style="display: flex; align-items: center; gap: 80px;">
  <h2 style="margin: 0; font-size: 50px; color: #343a40;">ใบส่งของชั่วคราว</h2>
  <p style="font-size: 26px; margin: 0;"><strong>( ประเภทบิล:</strong> ${type} )</p>
</div>
    <!-- Company Name Section -->
    <div style="border: 1px solid #343a40; padding: 8px 12px; display: flex; justify-content: center; align-items: center;">
      <h2 style="margin: 0; font-size: 26px; color: #343a40;">บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด</h2>
    </div>
  </div>
  <hr>

   <div style="font-size: 24px; position: absolute; top: 0px; right: 20px; border: 1px solid #000; padding: 10px; text-align: center; width: 150px;">
  <p style="margin: 0;"><strong>เลขที่บิล</strong></p>
  <p style="margin: 0;">${doc_id}</p>
</div>

<p style="font-size: 24px; margin: 0; text-align: right; position: absolute; top: 120px; right: 20px;">
  <strong>วันที่:</strong> ${revdate}
</p>

    <p style="font-size: 20px; border-bottom: 1px solid #000; padding-bottom: 3px; width: 1123px;">
    <strong>บริษัท :  </strong> ${name}
</p>

<p style="font-size: 20px; border-bottom: 1px solid #000; padding-bottom: 3px; width: 1123px;">
    <strong>ที่อยู่ :  </strong> ${address}
</p>

<p style="font-size: 20px; border-bottom: 1px solid #000; padding-bottom: 3px; width: 1123px;">
    <strong>ชื่อผู้ติดต่อ :  </strong> ${contact_name}
</p>

<p style="font-size: 20px; border-bottom: 1px solid #000; padding-bottom: 3px; width: 1123px;">
    <strong>โทร :  </strong> ${contact_tel}
</p>

<p style="font-size: 20px; border-bottom: 1px solid #000; padding-bottom: 3px; width: 1123px;">
    <strong>หมายเหตุ :  </strong> ${popupNotes}
</p>

<p  style="font-size: 20px; border-bottom: 1px solid #000; padding-bottom: 3px; width: 1123px;">
    <strong>ผู้เปิดบิล:</strong> ${emp}
</p>

    <table class="product-table" style="width: 100%; border-collapse: collapse; font-size: 20px;">
      <thead>
        <tr>
       <th style="border: 1px solid #fff; padding: 8px; width: 10%;">ลำดับ</th>
          <th style="border: 1px solid #fff; padding: 8px; width: 60%;">รายการ</th>
          <th style="border: 1px solid #fff; padding: 8px; width: 30%;">จำนวน</th>
        </tr>
      </thead>
      <tbody>
        ${tableRowsHtml}
      </tbody>
    </table>

  </div>

  <!-- ส่วนลายเซ็น -->
  <div>
    <p style="font-size: 20px; display: inline-block; margin-right: 5px;"><strong>ชื่อผู้รับ:</strong></p>
    <p style="font-size: 20px; display: inline-block; border-bottom: 1px solid #000; padding-bottom: 3px; width:400px; margin-right: 170px;">&nbsp;</p>
    <p style="font-size: 20px; display: inline-block; margin-right: 5px;"><strong>ชื่อผู้ส่ง:</strong></p>
    <p style="font-size: 20px; display: inline-block; border-bottom: 1px solid #000; padding-bottom: 3px; width:400px;">&nbsp;</p>
  </div>

</div>


`;
// รีเซ็ตปุ่มก่อนเริ่ม
button.textContent = 'สร้าง PDF';
button.classList.remove('btn-success');
button.classList.add('btn-outline-danger');
button.disabled = false;

// ดำเนินการต่อ
document.body.appendChild(pdfContainer);

await html2canvas(pdfContainer, { scale: 0.7 }).then(async (canvas) => {
    const imgData = canvas.toDataURL("image/jpeg", 0.7); // เปลี่ยน PNG เป็น JPEG และกำหนด quality
    const pdf = new jsPDF();
    const pdfWidth = pdf.internal.pageSize.getWidth();
    const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

    const margin = 10;
    pdf.addImage(imgData, "JPEG", margin, margin, pdfWidth - 2 * margin, pdfHeight - 2 * margin);

    const pdfBlob = pdf.output("blob");
    const blobUrl = URL.createObjectURL(pdfBlob);

    const newTab = window.open(blobUrl, '_blank');



    if (newTab) {
        button.textContent = '✅ สำเร็จ';
        button.classList.remove('btn-outline-danger');
        button.classList.add('btn-success');
        button.disabled = true;

        // เรียก API บันทึกสถานะไปยัง database
        try {
            await fetch('/api/save-button-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    buttonId: 'your-button-id', // ตรงนี้เปลี่ยนเป็น ID เฉพาะของปุ่ม หรือข้อมูลที่ใช้ระบุตัว
                    status: 'success'
                }),
            });
        } catch (error) {
            console.error('Error saving button status:', error);
        }

    } else {
        alert('กรุณาอนุญาต popup จากเบราว์เซอร์ของคุณ');
    }
});

document.body.removeChild(pdfContainer);

    
}


</script>

</body>
</html>



