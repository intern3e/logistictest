<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดเตรียมสินค้า</title>
    <style>
        /* RESET */
        * {
          box-sizing: border-box;
          margin: 0;
          padding: 0;
        }

        body {
          font-family: 'Segoe UI', sans-serif;
          background-color: #f4f6f8;
          color: #333;
          padding: 20px;
        }

        .header {
          background: linear-gradient(90deg, #2c3e50 0%, #4b6584 100%);
          padding: 20px 30px;
          color: #fff;
          border-radius: 10px;
          margin-bottom: 30px;
          display: flex;
          justify-content: space-between;
          align-items: center;
          box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .header h2 {
          font-size: 24px;
        }

        .header-buttons a button {
          padding: 10px 16px;
          font-size: 14px;
          border: none;
          border-radius: 6px;
          font-weight: 600;
          cursor: pointer;
          margin-left: 10px;
          transition: 0.3s;
          background-color: #3498db;
          color: #fff;
        }

        .header-buttons a button:hover {
          background-color: #2c80b4;
        }

        .container {
          background: #fff;
          padding: 25px;
          border-radius: 10px;
          box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .top-section {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 20px;
          flex-wrap: wrap;
          gap: 10px;
        }

        .button-group {
          display: flex;
          gap: 10px;
          flex-wrap: wrap;
        }

        #summitso {
          background-color: #27ae60;
          color: white;
          border: none;
          padding: 10px 18px;
          border-radius: 6px;
          font-weight: bold;
          cursor: pointer;
          transition: 0.3s;
        }

        #summitso:hover {
          background-color: #1f8a4d;
        }

        .button-group a button {
          background-color: #f39c12;
          color: white;
          border: none;
          padding: 10px 18px;
          border-radius: 6px;
          font-weight: bold;
          cursor: pointer;
          transition: 0.3s;
        }

        .button-group a button:hover {
          background-color: #d68910;
        }

        .search-box input {
          padding: 9px 14px;
          border-radius: 6px;
          border: 1px solid #ccc;
          background-color: #eef1f4;
          width: 250px;
          font-size: 14px;
        }

        .table-container {
          margin-top: 20px;
          overflow-x: auto;
        }

        .table-container table {
          width: 100%;
          border-collapse: collapse;
          font-size: 14px;
          background-color: #fff;
          border-radius: 10px;
          overflow: hidden;
          box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        th, td {
          padding: 10px 12px;
          border: 1px solid #e0e0e0;
          text-align: center;
          vertical-align: middle;
          word-break: break-word;
          white-space: normal;
          max-width: 180px;
        }

        th {
          background-color: #2c3e50;
          color: white;
          text-transform: uppercase;
          font-size: 13px;
        }

        tr:nth-child(even) {
          background-color: #f9f9f9;
        }

        tr:hover {
          background-color: #eef2f5;
        }

        td a {
          color: #2980b9;
          font-weight: 500;
          text-decoration: none;
        }

        td a:hover {
          text-decoration: underline;
        }

        .popup-overlay {
          display: none;
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0, 0, 0, 0.5);
          justify-content: center;
          align-items: center;
          z-index: 1000;
          padding: 20px;
        }

        .popup-content {
          background-color: #fff;
          padding: 25px;
          border-radius: 10px;
          width: 95%;
          max-width: 1150px;
          max-height: 80vh;
          overflow-y: auto;
          position: relative;
          box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .close-btn {
          position: absolute;
          top: 15px;
          right: 20px;
          font-size: 20px;
          cursor: pointer;
          color: #444;
        }

        textarea {
          width: 100%;
          height: 80px;
          padding: 10px;
          border-radius: 6px;
          border: 1px solid #ccc;
          resize: none;
          margin-top: 10px;
          font-size: 14px;
          background-color: #fefefe;
        }

        @media (max-width: 768px) {
          .top-section {
            flex-direction: column;
            align-items: stretch;
          }

          .search-box input {
            width: 100%;
          }

          .button-group {
            width: 100%;
            justify-content: center;
          }

          .button-group button,
          .button-group a button {
            width: 100%;
            text-align: center;
          }

          th, td {
            font-size: 12px;
            max-width: 100px;
            padding: 8px;
          }
        }
        th {
          background-color: #2c3e50;
          color: white;
          text-transform: uppercase;
          font-size: 13px;

          white-space: nowrap;     
          overflow: hidden;
          text-overflow: ellipsis; 
          max-width: 150px;         
        }
        .nowrap {
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
          max-width: 180px;
        }
.bg-red { background-color: #ef4444; }      /* สีแดงเข้มขึ้น */
.bg-green { background-color: #22c55e; }    /* สีเขียวเข้มขึ้น */
.bg-blue { background-color: #3b82f6; }     /* สีฟ้าเข้มขึ้น */
.bg-purple { background-color: #a855f7; }   /* สีม่วงเข้มขึ้น */
.bg-yellow {background-color: #fde047;}

@keyframes blink-yellow {
  0%, 100% { background-color: #fde047; } /* เหลือง */
  50% { background-color: #fad103; }         /* ขาว */
}

.bg-yellow1 {
  background-color: #fde047;
  animation: blink-yellow 1s infinite;
}
.btn-danger {
  background-color: #dc3545;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.btn-danger:hover {
  background-color: #ad0314;
}

    </style>

</head>
<body>
    <div class="header">
        <h2>ระบบตรวจเช็คเอกสาร</h2>
        <div class="header-buttons">
          <a href="Sotest" style="background-color: #0077ff; color: white; padding: 6px 8px; border-radius: 5px; text-decoration: none;">ข้อมูลจัดส่ง</a>
            <a href="http://server_update:8000/solist" button  type="submit" class="btn btn-danger">🚪 หน้าหลัก</a>
        </div>
    </div>


    <div class="container">
        <div class="top-section">
            <div class="button-group">
                <button id="summitso" onclick="updateStatuspdf2()">ยืนยัน</button>
                <a href="history"><button>📜 ประวัติเอกสาร</button></a>
            </div>
            <div class="search-box">
            <button id="del" class="btn-danger" onclick="confirmDelete()">❌ ยกเลิก</button>
            <script>
            function confirmDelete() {
              if (confirm('คุณแน่ใจหรือไม่ว่าต้องการยกเลิกเอกสารนี้?')) {
                updateStatuspdfcan(); // เรียกฟังก์ชันยกเลิกจริง
              }
            }
          </script>
            <input type="text" id="search-input" placeholder=" ค้นหา เลขที่บิล" onkeyup="searchTable()">
        </div>
        
        </div>
        
        <div class="table-container">
            <input type="checkbox" id="checkAll" onclick="toggleCheckboxes()"> ทั้งหมด
    <table>
        <thead>
            <tr>
                <th>เลือก</th>
                <th>REF</th>
                <th>อ้างอิงใบสั่งขาย</th>
                <th>อ้างอิงใบสั่งซื้อ</th>
                <th>อ้างอิงใบส่งของ</th>
                <th>ชื่อลูกค้า</th>
                <th>เบอร์ติดต่อ</th>
                <th>วันที่จัดส่ง</th>
                <th>ผู้เปิดบิล</th>
                <th>ประเภทบิล</th>
                <th>แจ้งเพิ่มเติม</th>
                <th>ข้อมูลสินค้า</th>
            </tr>
        </thead>
        <tbody id="table-body">
        @foreach($bill->sortBy('so_detail_id') as $item) 
                @if($item->statuspdf == 1 || $item->statuspdf == 4)
                    <tr>
                        <td>
                        <input type="checkbox" class="form-control1" name="statupdf[]" value="{{ $item->so_id }}" id="checkbox_{{ $item->so_detail_id }}">
                        </td>
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

                    <td class="nowrap {{ $bgColor }}" title="{{ $item->formtype }}">
                        <span title="{{ $item->formtype }}">{{ $item->so_detail_id }}</span>
                    </td>
                    <td>{{ $item->so_id }}</td>

                        <td class="nowrap">{{ $item->ponum }}</td>
                        <td class="{{ ($item->formtype == 'บิล/PO3/บัญชี' && $item->statuspdf == 1) ? 'bg-yellow1' : '' }}">
                        {{ $item->billid }}
                    </td>

                        <td>{{ $item->customer_name}}</td>
                        <td class="tel-column">{!! nl2br(e(str_replace(',', "\n", $item->customer_tel))) !!}</td>
                        <td class="nowrap">{{ \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') }}</td>
                        <td>{{ $item->sale_name }}</td>
                        <td id="billtype">{{ $item->billtype }}</td>
                         <td>{{ $item->notes }}</td>
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
                                        '{{ $item->sale_name }}',
                                        '{{ $item->notes }}'
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
                            <th>อ้างอิงใบสั่งขาย</th>
                            <th>อ้างอิงใบสั่งซื้อ</th>
                            <th>ชื่อลูกค้า</th>
                            <th>เบอร์โทร</th>
                            <th>ที่อยู่จัดส่ง</th>
                            <th>วันที่จัดส่ง</th>
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
                <br>
                <textarea id="popup-body-3" readonly style="width: 1080px; height: 70px;" readonly>
                </textarea>
            </div>
        </div>
    </div>
    
    <script>
        function openPopup(soDetailId,so_id,ponum,customer_name,customer_tel,customer_address,date_of_dali,sale_name,notes) {
        document.getElementById("popup").style.display = "flex"; // แสดง Popup
    
        let popupBody = document.getElementById("popup-body-1");
        popupBody.innerHTML = `
            <tr>
                <td>${soDetailId}</td>
                <td>${so_id}</td>
                <td>${ponum}</td>
                <td>${customer_name}</td>
                <td>${customer_tel}</td>
                <td>${customer_address}</td>
                <td>${date_of_dali}</td>
                <td>${sale_name}</td>
            </tr>
        `;
        document.getElementById("popup-body-3").value = notes;
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
        let soDetailId = cells[2] ? cells[2].textContent.toLowerCase() : '';

        // Search for the text inside the selected column (บิลลำดับ)
        if (soDetailId.indexOf(searchInput) > -1) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    }
}

function updateStatuspdf2() {
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

    fetch('/update-statuspdfso2', {
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
function toggleCheckboxes() {
    var checkAllBox = document.getElementById('checkAll');
    var checkboxes = document.querySelectorAll('input[type="checkbox"]:not(#checkAll)');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = checkAllBox.checked;
    });
}
    </script>
  <script>
    function updateStatuspdfcan() {
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

        fetch('/update-statuspdfcan', {
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
</body>
</html>
