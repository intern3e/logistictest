<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="{{ asset('css/dashboardadminpdf.blade.css') }}">
    <title>ระบบจัดเตรียมสินค้า</title>
  
</head>
<body>
    <div class="header">
        <h2>ระบบปริ้นเอกสารของบิลSO</h2>
        <div class="header-buttons">
            <a href="adminSO"><button class="btn-so">หน้าหลัก</button></a>
        </div>
    </div>
    


        <div class="top-section">
            <div class="button-group">
                <button id="summitso" onclick="updateStatuspdf()">ปริ้นเอกสารSO</button>
                <a href="dashboardadmin"><button id="printroute" style="background-color: red">ปริ้นเอกสารเส้นทางการเดินรถ</button></a>
                <a href="history"><button>📜 ประวัติเอกสาร</button></a>
            </div>

            <div class="search-box">
            <input type="text" id="search-input" placeholder=" ค้นหา เลขที่บิล" onkeyup="searchTable()">
        </div>
        
        </div>
        
        <div class="table-container">
            <input type="checkbox" id="checkAll" onclick="toggleCheckboxes()"> ทั้งหมด
    <table>
        <thead>
            <tr>
                <th>ปริ้นเอกสาร</th>
                <th>REF.</th>
                <th>ฟอมเอกสาร</th>
                <th>เลชใบสั่งซื้อ</th>
                <th>เลชPoลูกค้า</th>
                <th>เลขใบส่งของ</th>
            
                <th>วันที่จัดส่ง</th>
                <th>ประเภทบิล</th>
                <th>เลขบิลส่งของ</th>
                <th>เพิ่มเติ่ม</th>
                <th>รหัสลูกค้า</th>
                <th>เลขใบวางบิล</th>
            </tr>
        </thead>
        <tbody id="table-body">
        @foreach($bill->sortBy('so_detail_id') as $item) 
                @if($item->statuspdf == 0)
                    <tr>
                        <td>
                        <input type="checkbox" class="form-control1" name="statupdf[]" value="{{ $item->so_id }}" id="checkbox_{{ $item->so_detail_id }}">
                        </td>
                        <td>{{ $item->so_detail_id }}</td>  
                       @php
                        $bgColor = match($item->formtype) {
                            'บิล/PO3' => 'bg-red',
                            'บิล/PO3/วางบิล' => 'bg-green',
                            'บิล/PO3/วางบิล/สำเนาหน้าบิล2' => 'bg-blue',
                            'บิล/PO3/สำเนาบิล2' => 'bg-purple',
                            'บิล/PO3/บัญชี' => 'bg-yellow',
                            default => ''
                        };
                    @endphp

                  <td class="{{ $bgColor }}">{{ $item->formtype }}</td>

<td>{{ $item->so_id }}</td>

<td>
    <div class="ponum-cell">
        <span class="ponum-text">{{ $item->ponum }}</span>

        @if($item->POdocument)
            <button class="btn-danger-small"
                onclick="addSoDetailIdToPoDocument('{{ $item->so_detail_id }}', '{{ $item->POdocument }}')">
                เพิ่มเลขบิล
            </button>
            <button class="btn-success-small"
                onclick="openFileInNewTab('{{ asset('storage/po_documents/' . $item->POdocument) }}', 
                                           '{{ $item->ponum }}', 
                                           '{{ $item->so_detail_id }}', 
                                           '{{ $item->so_id }}',
                                           '{{ $item->billid ?? '' }}')">
                เลือกดูไฟล์
            </button>
        @else
            <button class="btn-danger-small"
                onclick="copyPonumAndCheckBox('{{ $item->so_id }}', '{{ $item->so_detail_id }}', '{{ $item->billid ?? '' }}')">
                ไม่มีไฟล์
            </button>
    </div>
</td>

<script>
    function copyPonumAndCheckBox(so_id, so_detail_id, billid) {
        if (!billid) {
            return; // ไม่ทำอะไรถ้าไม่มี billid
        }

        navigator.clipboard.writeText(billid).catch(() => {});

        const checkbox = document.querySelector(`input[type="checkbox"][data-detail-id="${so_detail_id}"]`);
        if (checkbox) {
            checkbox.checked = true;
        }
    }
</script>
                        
                                        
                        @endif
                        <td>{{ $item->billid }}</td>
                        </td>
        <td>{{ \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') }}</td> 
                                 <!-- คอลัมน์อื่น ๆ -->

      <td id="billtype">{{ $item->billtype }}</td>
<td>
    <div class="bill-actions-condensed">
        <input type="text" class="billid-input-condensed" id="billid" value="{{ $item->billid ?? '' }}" readonly>

        <form action="{{ route('upload.pdf') }}" method="POST" enctype="multipart/form-data" class="upload-form-condensed">
            @csrf
            <input type="file" name="pdffile" id="pdffile" accept="application/pdf" required>
            <button type="submit" class="btn-upload-condensed">อัปโหลด PDF</button>
        </form>

        <div class="action-buttons-condensed">
            <button class="btn-danger-condensed"
                onclick="addIdToDocument('{{ $item->so_detail_id }}', '{{ $item->billid }}')">เพิ่มเลขบิล</button>

            <button class="btn-success-condensed"
                onclick="openFileInNewTabbill(
                    '{{ asset('storage/doc_document/' . $item->billid . '.pdf') }}', 
                    '{{ $item->ponum }}', 
                    '{{ $item->so_detail_id }}', 
                    '{{ $item->so_id }}',
                    '{{ $item->billid ?? '' }}'
                )">เลือกดูไฟล์</button>
        </div>
    </div>
</td>

                                <td><a href="javascript:void(0);" 
                                    onclick="openPopup(
                                        '{{ $item->so_detail_id }}',
                                        '{{ $item->so_id }}',
                                        '{{ $item->ponum }}',
                                        '{{ $item->customer_name }}',
                                        '{{ $item->customer_tel }}',
                                        '{{ $item->customer_address }}',
                                        '{{ $item->date_of_dali }}',
                                        '{{ $item->sale_name }}'
                                    )">
                                    เพิ่มเติม
                                 </a></td>
                                 <td>
                                    <span id="customer-id-{{ $item->id }}">{{ $item->customer_id }}</span>
                                    <button onclick="copyToClipboard('customer-id-{{ $item->id }}')" class="copy-btn">
                                        คัดลอก
                                    </button>
                                </td>
                                <script>
                                function copyToClipboard(elementId) {
                                    const text = document.getElementById(elementId).innerText;
                                    navigator.clipboard.writeText(text).then(() => {
                                        alert("คัดลอกข้อมูลแล้ว: " + text);
                                    }).catch(err => {
                                        console.error('ไม่สามารถคัดลอกได้', err);
                                    });
                                }
                                </script>


                                    <td>
                            <div class="bill-actions-condensed">
                                    <input type="text" class="billid-input-condensed" id="billid" value="{{ $item->customer_id ?? '' }}" readonly>

                                    <form action="{{ route('upload.pdf') }}" method="POST" enctype="multipart/form-data" class="upload-form-condensed">
                                        @csrf
                                        <input type="file" name="pdffile" id="pdffile" accept="application/pdf" required>
                                        <button type="submit" class="btn-upload-condensed">อัปโหลด PDF</button>
                                    </form>

                                    <div class="action-buttons-condensed">
                                        <button class="btn-danger-condensed"
                                            onclick="addIdToDocument('{{ $item->so_detail_id }}', '{{ $item->billid }}')">เพิ่มเลขบิล</button>

                                        <button class="btn-success-condensed"
                                            onclick="openFileInNewTabbill(
                                                '{{ asset('storage/doc_document/' . $item->billid . '.pdf') }}', 
                                                '{{ $item->ponum }}', 
                                                '{{ $item->so_detail_id }}', 
                                                '{{ $item->so_id }}',
                                                '{{ $item->billid ?? '' }}'
                                            )">เลือกดูไฟล์</button>
                                    </div>
                                </div>
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
                            <th>ชื่อลูกค้า</th>
                            <th>เบอร์โทร</th>
                            <th>ที่อยู่จัดส่ง</th>
                            <th>ผุ้ขาย</th>
                        </tr>
                    </thead>
                    <tbody id="popup-body-1">   
                    </tbody>
                </table>
                <br>
                <table>
                    <thead>     
                        <tr>
                            <th>รหัสสินค้า</th>
                            <th>รายการ</th>
                            <th>จำนวน</th>
                            <th>ราคา/หน่วย</th>
                        </tr>
                    </thead>
                    <tbody id="popup-body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        function openPopup(soDetailId,so_id,ponum,customer_name,customer_tel,customer_address,date_of_dali,sale_name) {
        document.getElementById("popup").style.display = "flex"; // แสดง Popup
    
        let popupBody = document.getElementById("popup-body-1");
        popupBody.innerHTML = `
            <tr>
                <td>${soDetailId}</td>
                <td>${customer_name}</td>
                <td>${customer_tel}</td>
                <td>${customer_address}</td>
                <td>${sale_name}</td>
            </tr>
        `;
    
        let secondPopupBody = document.getElementById("popup-body");
        secondPopupBody.innerHTML = "<tr><td colspan='4'>Loading...</td></tr>";
    
        // ใช้ fetch ดึงข้อมูลจาก Laravel
        fetch(`/get-bill-detail/${soDetailId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    secondPopupBody.innerHTML = ""; // เคลียร์ข้อมูลเก่า
                    data.forEach(item => {
                        secondPopupBody.insertAdjacentHTML("beforeend", `
                            <tr>
                                <td>${item.item_id}</td>
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
                secondPopupBody.innerHTML = "<tr><td colspan='4'>เกิดข้อผิดพลาด</td></tr>";
            });
    }
    
        // ฟังก์ชันปิด Popup
        function closePopup() {
            document.getElementById("popup").style.display = "none"; // ซ่อน Popup
        }
    
    </script>
    

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".buttonbill").forEach(button => {
        button.addEventListener("click", function() {
            const row = this.closest("tr");
            const so_detail_id = this.getAttribute("data-sodetailid");
            const billidInput = row.querySelector(".billid");
            const billid = billidInput.value.trim();

            if (!billid) {
                alert("กรุณากรอกเลขที่เอกสาร");
                billidInput.focus();
                return;
            }

            fetch("/update-billid", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    so_detail_id: so_detail_id,  // Using so_id instead of soDetailIds
                    billid: billid
                })
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to update');
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("เกิดข้อผิดพลาด: " + error.message);
            });
        });
    });
});
    </script>
    
<script>
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

function updateStatuspdf() {
    let selectedIds = [];
    let checkboxes = document.querySelectorAll("input[name='statupdf[]']:checked");

    checkboxes.forEach(checkbox => {
        let row = checkbox.closest('tr');
        let soDetailId = row.querySelector('td:nth-child(2)').textContent; // Get so_detail_id from second column
        selectedIds.push(soDetailId);
    });

    if (selectedIds.length === 0) {
        return;
    }

    // Proceed to update
    console.log("Updating status for:", selectedIds); // Log the selected IDs

    fetch('/update-statuspdfso', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ soDetailIds: selectedIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Optionally reload the page to reflect changes
        } else {
            alert("ไม่สามารถอัปเดตสถานะได้");
        }
    })
    .catch(error => {
        console.error("Error updating status:", error);
        alert("เกิดข้อผิดพลาดในการอัปเดตสถานะ");
    });
}   
 
    </script>




<script>
function openFileInNewTab(url, ponum, so_detail_id, so_id, billid) {
    // ตรวจสอบ checkbox
    var checkbox = document.getElementById('checkbox_' + so_detail_id);
    if (checkbox) {
        checkbox.checked = true;
    } else {
        console.warn('ไม่พบ checkbox สำหรับ so_detail_id:', so_detail_id);
    }

    // เตรียมค่าเพื่อคัดลอก
    var soIdStr = String(so_id || '');
    var copyValue = (billid && billid.trim() !== '') ? billid : soIdStr.replace(/^SO/, '');

    // ฟังก์ชัน fallback หาก Clipboard API ใช้ไม่ได้
    function fallbackCopyTextToClipboard(text) {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";  // ป้องกัน scroll
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            var successful = document.execCommand('copy');
            if (successful) {
          
            } else {
                alert('ไม่สามารถคัดลอกเลขได้ (fallback)');
            }
        } catch (err) {
            console.error('Fallback: ไม่สามารถคัดลอกได้', err);
            alert('เบราว์เซอร์ของคุณไม่รองรับการคัดลอก');
        }

        document.body.removeChild(textArea);
    }

    // ใช้ Clipboard API ถ้าใช้ได้
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(copyValue).then(() => {
            console.log('คัดลอกสำเร็จ:', copyValue);
           
        }).catch(err => {
            console.error('Clipboard API ล้มเหลว:', err);
            fallbackCopyTextToClipboard(copyValue);
        });
    } else {
        // ใช้ fallback
        fallbackCopyTextToClipboard(copyValue);
    }

    // เปิดลิงก์
    if (url && url.trim() !== '') {
        window.open(url, '_blank');
    } else {
        console.warn('URL ว่างหรือไม่ถูกต้อง:', url);
        alert('ไม่สามารถเปิดไฟล์ได้: URL ว่าง');
    }
}

 </script>
<script>
    function addSoDetailIdToPoDocument(so_detail_id, POdocument) {
    console.log(`กำลังเพิ่ม ${so_detail_id} ลงในเอกสาร PO: ${POdocument}`);

    fetch(`/add-so-detail-id-to-pdf/${so_detail_id}/${POdocument}`)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.success) {
            } else {
                alert("เกิดข้อผิดพลาดในการเพิ่มข้อมูลลงในเอกสาร PO: " + (data.error || ''));
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("เกิดข้อผิดพลาดในการเพิ่มเลขที่บิลลงในเอกสาร PO");
        });
}
</script>
<script>
function addIdToDocument(so_detail_id, billid) {
    console.log(`กำลังเพิ่ม ${so_detail_id} ลงในเอกสารbill: ${billid}`);

    fetch(`/add-so-detail-id-to-bill/${so_detail_id}/${billid}`)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.success) {
            } else {
                alert("เกิดข้อผิดพลาดในการเพิ่มข้อมูลลงในเอกสาร bill: " + (data.error || ''));
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("เกิดข้อผิดพลาดในการเพิ่มเลขที่บิลลงในเอกสาร bill");
        });
}
function openFileInNewTabbill(url, ponum, so_detail_id, so_id, billid) {
    // ทำให้ checkbox ถูกเลือก
    var checkbox = document.getElementById('checkbox_' + so_detail_id);
    if (checkbox) {
        checkbox.checked = true;
    }

    // กำหนดค่าที่ต้องการคัดลอก
    var copyValue = billid && billid.trim() !== '' ? billid : so_id.replace(/^SO/, '');

    // คัดลอกค่าลงคลิปบอร์ด พร้อม fallback
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(copyValue).then(() => {
            console.log('คัดลอกสำเร็จ:', copyValue);
        }).catch(err => {
            fallbackCopyTextToClipboard(copyValue);
        });
    } else {
        fallbackCopyTextToClipboard(copyValue);
    }

    // เปิดไฟล์ในแท็บใหม่
    window.open(url, '_blank');
}

// ฟังก์ชัน fallback สำหรับการคัดลอก
function fallbackCopyTextToClipboard(text) {
    var textArea = document.createElement("textarea");
    textArea.value = text;

    // ป้องกันการแสดงผล
    textArea.style.position = "fixed";
    textArea.style.top = 0;
    textArea.style.left = 0;
    textArea.style.width = "1px";
    textArea.style.height = "1px";
    textArea.style.padding = 0;
    textArea.style.border = "none";
    textArea.style.outline = "none";
    textArea.style.boxShadow = "none";
    textArea.style.background = "transparent";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        var successful = document.execCommand('copy');
        console.log('Fallback copy ' + (successful ? 'สำเร็จ' : 'ล้มเหลว'), text);
    } catch (err) {
        console.error('Fallback copy ล้มเหลว:', err);
    }

    document.body.removeChild(textArea);
}

     function toggleCheckboxes() {
    var checkAllBox = document.getElementById('checkAll');
    var checkboxes = document.querySelectorAll('input[type="checkbox"]:not(#checkAll)');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = checkAllBox.checked;
    });
}

</script>
</body>
</html>