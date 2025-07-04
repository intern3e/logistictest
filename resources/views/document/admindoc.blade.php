<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>ระบบจัดบิลชั่วคราว</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/admindoc.blade.css') }}">

</head>
<body>
    <div class="header">
        <h2>ระบบจัดบิลชั่วคราว</h2>
        <a href="http://server_update:8000/solist"><button class="btn-so">หน้าหลัก</button></a>
    </div>
    <div class="container">
        <div class="top-section">

    
            <div class="button-group">
                <button id="submit" onclick="updateStatuspdf()">สถานะ</button>
                <button onclick="window.location.href='historydoc'">📜 ประวัติเอกสาร</button>
            </div>
            
            <div class="search-box">

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
                <th>ผู้ติดต่อ</th>
                <th>เบอร์โทร</th>
                <th>ประเภทงาน</th>
                <th>ผู้เปิดบิล</th>
                <th>วันที่</th>
                <th>ข้อมูลรายละเอียด</th>
                <th>ชื่อบริษัท</th>
                <th>หมายเหตุ</th>
                <th>pdf</th>
            </tr>
        </thead>
        <tbody id="table-body">
            @foreach($docbill as $item)
            @if($item->statuspdf == 0)
            <tr>
                <td>
                    <input type="checkbox" class="form-control1" name="status[]" data-doc-detail-id="{{ $item->doc_id }}">
                </td>
                <td>{{ $item->doc_id }}</td>
                <td>{{ $item->com_name }}</td>
                <td>{{ $item->com_address }}</td>   
                <td>{{ $item->contact_name }}</td>
                <td>{{ $item->contact_tel}}</td>
                <td>{{ $item->doctype }}</td>
                <td>{{ $item->emp_name }}</td>
                <td>{{ \Carbon\Carbon::parse($item->datestamp)->format('d/m/Y') }}</td>
                <td>
                <a href="javascript:void(0);" onclick="openPopup('{{ $item->doc_id }}', '{{ $item->com_name }}', '{{ $item->com_address }}', '{{ $item->contact_name }}', '{{ $item->contact_tel }}', '{{ $item->amount }}', '{{ $item->notes }}')">
                    เพิ่มเติม
                </a>
                <td>{{ $item->headcom }}</td>
                <td>{{ $item->notes }}</td>
                </td>
                <td>
                    <button id = "download"onclick="downloadRowPDF(this)" class="btn btn-sm btn-outline-danger">📄</button>
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
                <textarea id="popup-body-3" readonl style="width: 700px; height: 70px;" readonly>
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
function updateStatuspdf() {
    const docDetailIds = [];
    
    // Collect the selected checkbox document detail IDs
    document.querySelectorAll('input[name="status[]"]:checked').forEach((checkbox) => {
        docDetailIds.push(checkbox.getAttribute('data-doc-detail-id'));
    });
    
    if (docDetailIds.length === 0) {
        alert("กรุณาเลือกเอกสารก่อน");
        return;
    }

    fetch('/update-statuspdfdoc', {
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
        if (!jsPDF || !window.html2canvas) {
            alert("ไม่พบ library jsPDF หรือ html2canvas");
            return;
        }

        const row = button.closest("tr");
        const cells = row.querySelectorAll("td");

        const doc_id = cells[1].innerText.trim();
        const name = cells[2].innerText.trim();
        const address = cells[3].innerText.trim();
        const type = cells[6].innerText.trim();
        const emp = cells[7].innerText.trim();
        const revdate = cells[8].innerText.trim();
        const contact_tel = cells[5].innerText.trim();
        const contact_name = cells[4].innerText.trim();
        const headcom = cells[10].innerText.trim();
        const notes= cells[11].innerText.trim();

        // let popupAmount = '', popupNotes = '';
        // const link = row.querySelector('a[onclick^="openPopup"]');
        // if (link) {
        //     const onclickAttr = link.getAttribute('onclick');
        //     const args = [...onclickAttr.matchAll(/'([^']*)'/g)].map(match => match[1]);
        //     popupAmount = args[5] || '';
        //     popupNotes = args[1] || '';
        // }

        let tableRowsHtml = '';
        try {
            const response = await fetch(`/get-docbill-detail/${doc_id}`);
            const data = await response.json();

            if (data.length > 0) {
                data.forEach((item, index) => {
                    tableRowsHtml += `
                      <tr style="background-color: #fff; border: 1px solid #red;">
                  <td style="border: 1px solid #000; padding: 8px; font-size: 18px;">${index + 1}</td>
                  <td style="border: 1px solid #000; padding: 8px; text-align: left; font-size: 18px;">${item.item_name}</td>
                  <td style="border: 1px solid #000; padding: 8px; text-align: center; font-size: 20px;">${item.quantity}</td>
              </tr>
                    `;
                });
            } else {
                tableRowsHtml = `
                    <tr>
                        <td colspan="3" style="border: 1px solid 	#555555; padding: 8px; text-align: center;">
                            ไม่มีข้อมูลสินค้า
                        </td>
                    </tr>
                `;
            }
        } catch (error) {
            console.error("Error fetching data:", error);
            tableRowsHtml = `
                <tr>
                    <td colspan="3" style="border: 1px solid 	#555555; padding: 8px; text-align: center;">
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

        pdfContainer.innerHTML = `
        <div style="display: flex; flex-direction: column; min-height: 1650px;">
          <div style="flex: 1;">
            <div style="display: flex; flex-direction: column; margin-bottom: 5px; width: calc(100% - 200px); gap: 10px;">
              <div style="display: flex; align-items: center; gap: 80px;">
                <h2 style="margin: 0; font-size: 50px; color: #343a40;">ใบส่งของชั่วคราว</h2>
                <p style="font-size: 26px; margin: 0;"><strong>( ประเภทบิล:</strong> ${type} )</p>
              </div>
              <div style="border: 1px solid #343a40; padding: 8px 12px; display: flex; justify-content: center; align-items: center;">
                <h2 style="margin: 0; font-size: 26px; color: #343a40;">${headcom}</h2>
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

            <p class="print-line" style="font-size: 20px; border-bottom: 3px dotted #555; padding-bottom: 3px;">
              <strong>บริษัท : </strong> ${name}
            </p>

            <p class="print-line" style="font-size: 20px; border-bottom: 3px dotted #555; padding-bottom: 3px;">
              <strong>ที่อยู่ :  </strong> ${address}
            </p>

            <p class="print-line" style="font-size: 20px; border-bottom: 3px dotted #555; padding-bottom: 3px;">
              <strong>ชื่อผู้ติดต่อ :  </strong> ${contact_name}
            </p>

            <p class="print-line" style="font-size: 20px; border-bottom: 3px dotted #555; padding-bottom: 3px;">
              <strong>โทร :  </strong> ${contact_tel}
            </p>

            <p class="print-line" style="font-size: 20px; border-bottom: 3px dotted #555; padding-bottom: 3px;">
              <strong>หมายเหตุ :  </strong> ${notes}
            </p>

            <p class="print-line" style="font-size: 20px; border-bottom: 3px dotted #555; padding-bottom: 3px;">
              <strong>ผู้เปิดบิล:</strong> ${emp}
            </p>


            <table style="width: 100%; border-collapse: collapse; font-size: 20px;">
              <thead>
              <tr>
              <th style="border: 1px solid 	#000; padding: 8px; width: 10%; background-color: #f2f2f2; color: #333; font-size: 20px;">ลำดับ</th>
              <th style="border: 1px solid 	#000; padding: 8px; width: 60%; background-color: #f2f2f2; color: #333; font-size: 20px;">รายการ</th>
              <th style="border: 1px solid 	#000; padding: 8px; width: 30%; background-color: #f2f2f2; color: #333; font-size: 20px;">จำนวน</th>
            </tr>
              </thead>
              <tbody>
                ${tableRowsHtml}
              </tbody>
            </table>
          </div>

          <div>
            <p style="font-size: 20px; display: inline-block; margin-right: 5px;"><strong>ชื่อผู้รับ:</strong></p>
            <p style="font-size: 20px; display: inline-block; border-bottom: 1px solid #000; padding-bottom: 3px; width:400px; margin-right: 130px;">&nbsp;</p>
            <p style="font-size: 20px; display: inline-block; margin-right: 5px;"><strong>ชื่อผู้ส่ง:</strong></p>
            <p style="font-size: 20px; display: inline-block; border-bottom: 1px solid #000; padding-bottom: 3px; width:400px;">&nbsp;</p>
          </div>
        </div>
        `;
document.body.appendChild(pdfContainer);

const MAX_FILE_SIZE = 200 * 1024; // 200 KB
const pageWidth = 595.28;
const pageHeight = 841.89;

function resizeCanvas(originalCanvas, scaleFactor) {
  const resizedCanvas = document.createElement('canvas');
  resizedCanvas.width = originalCanvas.width * scaleFactor;
  resizedCanvas.height = originalCanvas.height * scaleFactor;
  const ctx = resizedCanvas.getContext('2d');
  ctx.drawImage(originalCanvas, 0, 0, resizedCanvas.width, resizedCanvas.height);
  return resizedCanvas;
}

const canvas = await html2canvas(pdfContainer, {
  scale: 1.5, // ไม่สูงเกินไป
  backgroundColor: "#FFFFFF",
  useCORS: true
});

let quality = 0.7;
let pdfBlob;
let imgData;

do {
  const resized = resizeCanvas(canvas, 1);
  imgData = resized.toDataURL("image/jpeg", quality);

  const pdf = new jspdf.jsPDF("p", "pt", "a4");

  let imgWidth = pageWidth;
  let imgHeight = (resized.height * imgWidth) / resized.width;

  if (imgHeight > pageHeight) {
    imgHeight = pageHeight;
    imgWidth = (resized.width * imgHeight) / resized.height;
  }

  const marginX = (pageWidth - imgWidth) / 2;
  const marginY = (pageHeight - imgHeight) / 2;

  pdf.addImage(imgData, "JPEG", marginX, marginY, imgWidth, imgHeight);
  pdfBlob = pdf.output("blob");

  if (pdfBlob.size > MAX_FILE_SIZE) {
    quality -= 0.05;
  } else {
    break;
  }
} while (quality > 1);

console.log("Final PDF size (KB):", (pdfBlob.size / 1024).toFixed(2));
window.open(URL.createObjectURL(pdfBlob), "_blank");

pdfContainer.remove();
button.textContent = "สร้าง PDF";
button.disabled = false;

// ✅ เช็ค checkbox อัตโนมัติในแถวเดียวกับปุ่ม
const checkbox = row.querySelector('input[type="checkbox"].form-control1[name="status[]"]');
if (checkbox) {
  checkbox.checked = true;
}


    }
</script>



</body>
</html>



