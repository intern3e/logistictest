<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดเตรียมสินค้า</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .header {
            background: linear-gradient(to right, #2c3e50, #4b6584);
            padding: 0px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.2rem;
            border-radius: 8px;
            margin: 20px auto;
            width: 90%;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
            .header-buttons button {
        padding: 8px 20px;
        font-size: 16px;
        cursor: pointer;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-right: 10px; /* Adds space between buttons */
        }

        .btn-po {
            background-color: #4CAF50; /* Green for PO button */
            color: white;
        }

        .btn-so {
            background-color: #2196F3; /* Blue for SO button */
            color: white;
        }

        .header-buttons button:hover {
            transform: scale(1.05); /* Adds a slight grow effect when hovering */
        }

        .btn-po:hover {
            background-color: #27ae60; /* Darker green for PO button on hover */
        }

        .btn-so:hover {
            background-color: #00389f; /* Darker blue for SO button on hover */
        }


        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 90%;
            margin: auto;
        }
        /* --- Table Styling --- */
        .table-container {
            background: #f9f9f9; /* Light gray background for table */
            margin: 2% 5%;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 99%;
            max-width: 100%; /* Ensure table doesn't overflow the container */
            transform: scale(0.9); /* Scale down the table to fit the screen */
            transform-origin: top left; /* Keep the table scaling from the top-left corner */
            overflow-x: auto; 
        }

        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .top-section button {
            padding: 8px 12px;
            border: none;
            background: #27ae60;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        .top-section {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0px 5%;
        }

        .top-section label {
            font-weight: bold;
            color: #2c3e50;
        }

        .top-section input {
            padding: 8px;
            border-radius: 5px;
            font-size: 1rem;
        }
        .top-section button:hover {
            background: #2980b9;
        }
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
            transition: 0.3s;
        }

        .filter-container button:hover {
            background-color: #27ae60;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .button-group button {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .button-group button {
            background-color: #f39c12;
            font-size: 16px;
            color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }

        .button-group button:hover {
            background-color: #e67e22;
            transform: scale(1.05);
        }
        .table th, .table td {
            padding: 12px;
            border: 1px solid #dcdde1;
            text-align: center;
        }

        .table th {
            background: linear-gradient(to right, #2c3e50, #4b6584);
            color: white;
            font-weight: bold;
        }

        .table-striped tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table-striped tr:hover {
            background-color: #ecf0f1;
        }

        .link {
            color: #16a085;
            font-weight: bold;
            text-decoration: none;
        }
        .link:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }

        th, td {
             padding: 9px;
            text-align: center;
            border: 1px solid #e1e4e8;
            font-size: 10px;
        }

        th {
            background: #00389f;
            color: white;
            text-transform: uppercase;
        }

        tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        .tr {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #e1e5ea;
            width: 70%;
            transition: 0.2s;
        }

        td a {
            color: #27ae60;
            font-weight: bold;
            text-decoration: none;
        }

        .search-box {
            flex-grow: 1;
            max-width: 200px;
        }

        .search-box input {
            width: 90%;
            height: 30px;
            margin: 0px -30%;
            background: #f8f9fa;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background: linear-gradient(to right, #f0f2f5, #dfe9f3);
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 1150px;
            height: auto;
            text-align: center;
            position: relative;
            overflow: hidden;
            max-height: 500px;
            overflow-y: auto;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }
        .search-box {
            flex-grow: 1;
            max-width: 200px;
        }

        .search-box input {
            width: 90%;
            height: 30px;
            margin: 0px 10px;
            background: #f8f9fa;
        }

        .search-box {
            display: flex;
            align-items: center;
            transition: 0.3s;
            max-width: 250px;
        }

        .search-box input {
            flex-grow: 1;
            padding: 5px;
            border: none;
            outline: none;
            font-size: 1rem;
            border-radius: 5px;
            
            background-color: #e1e5ea;
        }
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    top: 10px;
    width: 100%;
    height: 100%;
    background-color: rgba(157, 206, 255, 0.5);
    display: flex;
    align-items: center;
}

.modal-content {
    background: rgb(255, 255, 255);
    padding: 10px;
    border-radius: 10px;
    width: 70%;
    max-width: 1000px; /* กำหนดขนาดสูงสุด */
    position: relative;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
    margin-left: 13vw;
}

.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 20px;
    cursor: pointer;
    color: rgb(255, 255, 255);
}

@media (max-width: 768px) {
    .modal-content {
        width: 90%;
        height: 80%;
    }
}
.billid {
    width: 70px; /* ปรับความกว้าง */
    height: 25px; /* ปรับความสูง */
    font-size: 12px; /* ปรับขนาดตัวอักษร */
    padding: 2px 5px; /* ปรับระยะห่างภายใน */
}
.bg-red { background-color: #ef4444; color: white; }     /* สีแดง */
.bg-green { background-color: #22c55e; color: white; }   /* สีเขียว */
.bg-blue { background-color: #3b82f6; color: white; }    /* สีฟ้า */
.bg-purple { background-color: #a855f7; color: white; }  /* สีม่วง */
.bg-yellow { background-color: #fde047; color: black; }  /* สีเหลือง - ใช้สีดำจะอ่านง่ายกว่า */


    </style>
</head>
<body>
    <div class="header">
        <h2>ระบบปริ้นเอกสารของบิลSO</h2>
         <div class="header-buttons">
            <button id="pullPoOutside" class="btn-so">ดึงPO ภายนอก</button>
            <a href="http://server_update:8000/solist"><button class="btn-so">หน้าหลัก</button></a>
        </div>
    </div>
    <script>
document.getElementById('pullPoOutside').addEventListener('click', function () {
    fetch("{{ route('pull.pooutside') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
    });
});
</script>


        <div class="top-section">
            <div class="button-group">
                <button id="summitso" onclick="updateStatuspdf()">ปริ้นเอกสารSO</button>
                {{-- <a href="dashboardadmin"><button id="printroute" style="background-color: red">ปริ้นเอกสารเส้นทางการเดินรถ</button></a> --}}
                <a href="history" ><button style="background-color: red">เปลี่ยนวันที่</button></a>

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
                <th id="ref1">เลขที่บิล</th>
                <th>ประเภทบิล</th>
                <th>อ้างอิงใบสั่งขาย</th>
                <th>อ้างอิงใบส่งของ</th>
                <th>อ้างอิงใบสั่งซื้อ</th>
                <th>วันที่จัดส่ง</th>
                <th>ผู้ขาย</th>
                <th>ผู้เปิดบิล</th>
                <th>ประเภทบิล</th>
                <th>เลขที่เอกสาร</th>
                <th>ข้อมูลสินค้า</th>
                <th>เลขใบวางบิล</th>
                <th>เอกสารใบว่างบิล</th>
            </tr>
        </thead>
        <tbody id="table-body">
        @foreach($bill->sortBy('so_detail_id') as $item) 
                @if($item->statuspdf == 0)
                    <tr>
                        <td>
                       <input type="checkbox" class="form-control1"name="statupdf[]"value="{{ $item->so_id }}"id="checkbox_{{ $item->so_detail_id }}">
                        </td>
                        <td id="ref">{{ $item->so_detail_id }}</td>  
                       @php
                        $bgColor = match($item->formtype) {
                            'บิล/PO3' => 'bg-red',
                            'บิล/PO3/วางบิล' => 'bg-green',
                            'บิล/PO3/วางบิล/สำเนาหน้าบิล2' => 'bg-blue',
                            'บิล/PO3/สำเนาหน้าบิล2' => 'bg-purple',
                            'บิล/PO3/บัญชี' => 'bg-yellow',
                            default => ''
                        };
                    @endphp

                    <td id = "formtype"class="{{ $bgColor }}">{{ $item->formtype }}</td>

                        
                        <td>{{ $item->so_id }}</td>
                        <td>    
                            {{ $item->ponum }} 
                            @if($item->POdocument)
                            
                            <button style="background-color: red; color: white;"
                            id="Pumppo"
                                onclick="addSoDetailIdToPoDocument('{{ $item->so_detail_id }}', '{{ $item->POdocument }}')">
                                เพิ่มเลขบิลลงPO
                            </button>
                            <button id="download"
                            style="background-color: #27ae60; color: white;"
                            onclick="openFileInNewTab('{{ asset('storage/po_documents/' . $item->POdocument) }}', 
                                     '{{ $item->ponum }}', 
                                       '{{ $item->so_detail_id }}', 
                                       '{{ $item->so_id }}',
                                       '{{ $item->billid ?? '' }}')">
                                เลือกดูไฟล์
                            </button>
                            @else
                             <button id="downloadnoPO" style="background-color: red; color: white;"
    onclick="copyBillIdToClipboard(this, '{{ $item->billid ?? '' }}')">
    ไม่มีไฟล์
</button>
    <script>
    function copyBillIdToClipboard(button, billid) {
        const text = billid.trim();

        if (!text) {
            alert("ไม่พบเลข Bill ID ที่จะคัดลอก");
            return;
        }

        // ใช้ Clipboard API
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => {
                const originalText = button.innerText;
                button.innerText = "คัดลอกแล้ว ✅";
                setTimeout(() => {
                    button.innerText = originalText;
                }, 1500);
            }).catch(err => {
                console.error('❌ ไม่สามารถคัดลอกได้', err);
                alert("ไม่สามารถคัดลอกได้");
            });
        } else {
            // Fallback (สำหรับเบราว์เซอร์เก่า)
            const tempInput = document.createElement("input");
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            try {
                document.execCommand("copy");
                const originalText = button.innerText;
                button.innerText = "คัดลอกแล้ว ✅";
                setTimeout(() => {
                    button.innerText = originalText;
                }, 1500);
            } catch (err) {
                alert("ไม่สามารถคัดลอกได้");
            }
            document.body.removeChild(tempInput);
        }
    }
</script>        
                                        
                        @endif
                            @if($item->notes != null)
                                <a href="{{ route('print.notes', $item->so_detail_id) }}" target="_blank">
                                    <button id="printnotes" style="background-color: #2980b9; color: white;">
                                        พิมพ์ Notes
                                    </button>
                                </a>
                            @endif
                        </td>
                        <td>{{ $item->billid }}</td>
                        </td>
                            
@php
    $date = \Carbon\Carbon::parse($item->date_of_dali);
    $formatted = $date->format('d/m/') . ($date->year + 543);
@endphp

<td>
    <span id = "datenaja" name="datenaja">{{ $formatted }}</span>
    <button id="copydate" onclick="copydate('{{ $formatted }}', this)">📋 คัดลอก</button>
</td>

<script>
function copydate(text, button) {
    const textarea = document.createElement("textarea");
    textarea.value = text;
    textarea.setAttribute("readonly", "");
    textarea.style.position = "absolute";
    textarea.style.left = "-9999px";

    document.body.appendChild(textarea);
    textarea.select();

    try {
        const success = document.execCommand("copy");
        if (success) {
            const original = button.innerText;
            button.innerText = "คัดลอกแล้ว ✅";
            setTimeout(() => button.innerText = original, 1500);
        } else {
            alert("ไม่สามารถคัดลอกได้");
        }
    } catch (err) {
        alert("ไม่สามารถคัดลอกได้");
        console.error("Error:", err);
    }

    document.body.removeChild(textarea);
}
</script>


                                <td>{{ $item->sale_name }}</td>
                                <td>{{ $item->emp_name }}</td>
                                <td id="billtype">{{ $item->billtype }}</td>
                                <td>
                                    <input type="text" class="billid" id="billid" value="{{ $item->billid ?? '' }}" readonly >
                                    
                                    
                  		<form action="{{ route('upload.pdf') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="pdffile" id="pdffile" accept="application/pdf" required>
                                        <button type="submit" id="subpdffile">อัปโหลด PDF</button>
                                    </form>
                                    <button style="background-color: red; color: white;"
                                        id="Pumpbill"
                                        onclick="checkBillTypeAndAddBill('{{ $item->so_detail_id }}','{{ $item->billid }}','{{ $item->billtype }}','{{ $item->so_id }}')">
                                        เพิ่มเลขบิล
                                    </button>
                                    <button id="downloadbill"
                                        style="background-color: #27ae60; color: white;"
                                        onclick="openFileInNewTabbill(
                                            '{{ asset('storage/doc_document/' . $item->billid . '.pdf') }}', 
                                            '{{ $item->ponum }}', 
                                            '{{ $item->so_detail_id }}', 
                                            '{{ $item->so_id }}',
                                            '{{ $item->billid ?? '' }}'
                                        )">
                                        เลือกดูไฟล์
                                    </button>
                           <button id="mergePdfBtn"
                                style="background-color: orange; color: white;"
                                onclick="mergePdf('{{ $item->billid }}')">
                                รวมและบันทึกไฟล์ PDF
                            </button>
<script>
function mergePdf(billid) {
    fetch("{{ route('merge.pdf') }}", {
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json' // ✅ สำคัญ
        },
        body: JSON.stringify({ billid: billid })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
        } else {
            alert("❌ " + data.message);
        }
    })
    .catch(err => {
        alert("❌ เกิดข้อผิดพลาด: " + err.message);
        console.error(err);
    });
}

</script>




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
                                 </a>
                                </td>


                                  <td>
                                  <span id="customer-id{{ $item->id }}">{{ $item->customer_id }}</span>
                                <button id="copyidcust" onclick="copyToClipboard('customer-id{{ $item->id }}', this)" class="copy-btn">
                                    คัดลอก
                                </button>

<script>
    function copyToClipboard(elementId, button) {
        const textElement = document.getElementById(elementId);
        if (textElement) {
            const text = textElement.innerText.trim();

            // ตรวจสอบว่า Clipboard API ใช้งานได้
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    // เปลี่ยนข้อความปุ่มชั่วคราว
                    const originalText = button.innerText;
                    button.innerText = "คัดลอกแล้ว ✅";
                    setTimeout(() => {
                        button.innerText = originalText;
                    }, 1500);
                }).catch(err => {
                    console.error('❌ ไม่สามารถคัดลอกได้', err);
                    alert("ไม่สามารถคัดลอกได้");
                });
            } else {
                // Fallback (สำหรับเบราว์เซอร์เก่า)
                const tempInput = document.createElement("input");
                document.body.appendChild(tempInput);
                tempInput.value = text;
                tempInput.select();
                try {
                    document.execCommand("copy");
                } catch (err) {
                    alert("ไม่สามารถคัดลอกได้");
                }
                document.body.removeChild(tempInput);
            }
        } else {
            alert("ไม่พบข้อมูลที่ต้องการคัดลอก");
        }
    }
</script>

                                    <br>
                                    <span>ใบวางบิล</span>
                                    <input type="text" class="billid-input-condensed" id="billissue" placeholder="กรอกเลขใบวางบิล">
                                    <br>
                                    <button type="button"
                                            id="billissuebut"
                                            data-sodetailid="{{ $item->so_detail_id }}">
                                        เพิ่มเลขใบวางบิล
                                    </button>
                                    
                                </td>

                                    <td>
                                        
                            <div class="bill-actions-condensed">
                                    <input type="text" class="billid-input-condensed" value="{{ $item->bill_issue_no ?? '' }}" readonly>

                                   <form action="{{ route('upload.billissue') }}" method="POST" enctype="multipart/form-data" class="upload-form-condensed">
                                        @csrf
                                        <input type="hidden" id="bill_issue_no" name="bill_issue_no" value="{{ $item->bill_issue_no }}"> 
                                        <input type="file" id="pdffilebillissue" name="pdffilebillissue" accept="application/pdf" required>
                                        <button id="subfileissue" type="submit" class="btn-upload-condensed">อัปโหลด PDF</button>
                                    </form>
                                    <div class="action-buttons-condensed">
                                        <button id="addIdToissue" class="btn-danger-condensed"
                                            onclick="addIdToissueDocument('{{ $item->so_detail_id }}', '{{ $item->bill_issue_no }}')">เพิ่มเลขบิล</button>
                                        <button type="button"
                                            id="openbillissue"
                                            onclick="openBillAndCheck('{{ asset('storage/billissue_document/' . $item->bill_issue_no . '.pdf') }}', 'checkbox_{{ $item->so_detail_id }}')">
                                          ดูใบวางบิล
                                    </button>
                                    <script>
                                    function openBillAndCheck(pdfUrl, checkboxId) {
                                        window.open(pdfUrl, '_blank');

                                        // ติ๊ก checkbox หลังจากเปิด PDF
                                        const checkbox = document.getElementById(checkboxId);
                                        if (checkbox) {
                                            checkbox.checked = true;
                                        }
                                    }
                                    </script>

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
            document.addEventListener("DOMContentLoaded", function () {
                const billissueBtn = document.getElementById("billissuebut");
                const billissueInput = document.getElementById("billissue");

                billissueBtn?.addEventListener("click", function () {
                    const so_detail_id = billissueBtn.getAttribute("data-sodetailid");
                    const billissue = billissueInput.value.trim();

                    if (!billissue) {
                        alert("❗ กรุณากรอกเลขใบวางบิล");
                        billissueInput.focus();
                        return;
                    }

                    if (!so_detail_id) {
                        alert("❌ ไม่พบ so_detail_id");
                        return;
                    }

                    billissueBtn.disabled = true;

                    fetch("/update-billissue", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content,
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            so_detail_id: so_detail_id,
                            bill_issue_no: billissue
                        })
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'เกิดข้อผิดพลาดในการอัปเดต');
                        };
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("❌ เกิดข้อผิดพลาด: " + error.message);
                    })
                    .finally(() => {
                        billissueBtn.disabled = false;
                    });
                });

                billissueInput.addEventListener("keydown", function (e) {
                    if (e.key === "Enter") {
                        e.preventDefault();
                        billissueBtn.click();
                    }
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
                // คัดลอกสำเร็จ (ถ้าต้องการแจ้งเตือนเพิ่มใส่ตรงนี้)
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

    // เพิ่ม timestamp query string เพื่อบังคับโหลดไฟล์ใหม่
    if (url && url.trim() !== '') {
        const timestamp = Date.now();
        const urlWithTimestamp = url.includes('?') ? `${url}&t=${timestamp}` : `${url}?t=${timestamp}`;
        window.open(urlWithTimestamp, '_blank');
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
function addIdToissueDocument(so_detail_id, bill_issue_no) {
    console.log(`กำลังเพิ่ม ${so_detail_id} ลงในเอกสารbill_issue_no: ${bill_issue_no}`);

    fetch(`/add-so-detail-id-to-billissue/${so_detail_id}/${bill_issue_no}`)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.success) {
            } else {
                alert("เกิดข้อผิดพลาดในการเพิ่มข้อมูลลงในเอกสาร bill_issue_no: " + (data.error || ''));
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("เกิดข้อผิดพลาดในการเพิ่มเลขที่บิลลงในเอกสาร bill_issue_no");
        });
}
function checkBillTypeAndAddBill(so_detail_id,billid,billtype,so_id) {
    console.log('Checking billtype:',billtype);
    
    if (billtype && billtype.includes('งานบริการ')) {
        addIdToDocument3(so_detail_id,billid,so_id);
    } else {
        addIdToDocument(so_detail_id,billid,so_id);
    }
}

function addIdToDocument(so_detail_id, billid, so_id) {
    // Encode so_id เพื่อป้องกันปัญหากับ / ใน URL
    const encodedSoId = encodeURIComponent(so_id);
    
    fetch(`/add-so-detail-id-to-bill/${so_detail_id}/${billid}/${encodedSoId}`)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.success) {
                console.log('เพิ่มข้อมูลสำเร็จ');
            } else {
                alert("เกิดข้อผิดพลาดในการเพิ่มข้อมูลลงในเอกสาร bill: " + (data.error || ''));
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("เกิดข้อผิดพลาดในการเพิ่มเลขที่บิลลงในเอกสาร bill");
        });
}

function addIdToDocument3(so_detail_id, billid, so_id) {
    // Encode so_id เพื่อป้องกันปัญหากับ / ใน URL
    const encodedSoId = encodeURIComponent(so_id);
    
    fetch(`/add-so-detail-id-to-bill-3/${so_detail_id}/${billid}/${encodedSoId}`)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.success) {
                console.log('เพิ่มข้อมูลสำเร็จ');
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