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
            font-size: 14px;
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

    </style>
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
                <th>เลขที่บิล</th>
                <th>อ้างอิงใบสั่งขาย</th>
                <th>อ้างอิงใบส่งของ</th>
                <th>อ้างอิงใบสั่งซื้อ</th>
            
                <th>วันที่จัดส่ง</th>
                <th>ผู้ขาย</th>
                <th>ผู้เปิดบิล</th>
                <th>ประเภทบิล</th>
                <th>เลขที่เอกสาร</th>
                <th>ข้อมูลสินค้า</th>
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
                        <td>{{ $item->so_id }}</td>
                        <td>    
                            {{ $item->ponum }} 
                            @if($item->POdocument)
                            
                            <button style="background-color: red; color: white;"
                            id="Pumppo"
                                onclick="addSoDetailIdToPoDocument('{{ $item->so_detail_id }}', '{{ $item->POdocument }}')">
                                เพิ่มเลขบิล
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
                            onclick="copyPonumAndCheckBox('{{ $item->so_id }}', '{{ $item->so_detail_id }}', '{{ $item->billid ?? '' }}')">
                            ไม่มีไฟล์
                        </button>
                        
                                        
                        @endif
                        <td>{{ $item->billid }}</td>
                        </td>
                                <td>{{ \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') }}</td> 
                                <td>{{ $item->sale_name }}</td>
                                <td>{{ $item->emp_name }}</td>
                                <td id="billtype">{{ $item->billtype }}</td>
                                <td>
                                    <input type="text" class="billid" id="billid" value="{{ $item->billid ?? '' }}">
                                    


                                    <button style="background-color: red; color: white;"
                                    id="Pumpbill"
                                        onclick="addIdToDocument('{{ $item->so_detail_id }}', '{{ $item->billid }}')">
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
        // ทำให้ checkbox ถูกเลือก
        document.getElementById('checkbox_' + so_detail_id).checked = true;
    
        // กำหนดค่าที่ต้องการคัดลอก: ถ้า billid ไม่มีค่าหรือว่าง ให้ใช้ so_id
        var copyValue = billid && billid.trim() !== '' ? billid : so_id.replace(/^SO/, ''); 
    
        // คัดลอกค่าลงคลิปบอร์ด
        navigator.clipboard.writeText(copyValue).then(() => {
            console.log('คัดลอก:', copyValue);
        }).catch(err => {
            console.error('ไม่สามารถคัดลอกได้:', err);
        });
    
        // เปิดไฟล์ในแท็บใหม่
        window.open(url, '_blank');
    

     }
    
    function copyPonumAndCheckBox(so_id, so_detail_id, billid) {
        // ทำให้ checkbox ถูกเลือก
        const checkbox = document.getElementById('checkbox_' + so_detail_id);
        if (checkbox) {
            checkbox.checked = true;
            console.log("Checkbox checked for:", so_detail_id);
        } else {
            console.log("Checkbox not found for:", so_detail_id);
        }
    
        // กำหนดค่าที่จะคัดลอก: ถ้า billid มีค่าให้ใช้ billid, ถ้าไม่มีให้ใช้ so_id
        const copyValue = billid && billid.trim() !== '' ? billid : so_id.replace(/^SO/, '');
    
        // คัดลอกค่าลงคลิปบอร์ด
        navigator.clipboard.writeText(copyValue).then(() => {
            console.log("Copied value:", copyValue);
        }).catch(err => {
            console.error("Failed to copy value:", err);
        });
    

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
        document.getElementById('checkbox_' + so_detail_id).checked = true;
    
        // กำหนดค่าที่ต้องการคัดลอก: ถ้า billid ไม่มีค่าหรือว่าง ให้ใช้ so_id
        var copyValue = billid && billid.trim() !== '' ? billid : so_id.replace(/^SO/, ''); 
    
        // คัดลอกค่าลงคลิปบอร์ด
        navigator.clipboard.writeText(copyValue).then(() => {
            console.log('คัดลอก:', copyValue);
        }).catch(err => {
            console.error('ไม่สามารถคัดลอกได้:', err);
        });
    
        // เปิดไฟล์ในแท็บใหม่
        window.open(url, '_blank');

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