<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เช็คบิล</title>
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
  background: linear-gradient(to right, #2c3e50, #4b6584);
  padding: 0 30px;
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
  background-color: #01be2a;
  color: white;
  padding: 8px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background 0.3s, transform 0.2s;
}
.header button:hover {
  background-color: #208601;
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
  box-sizing: border-box;
}

/* ===== Table Container ===== */
.table-container {
  background: #fff;
  margin: 20px auto;
  border-radius: 12px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
  overflow-x: auto;
  width: 100%;
  padding: 20px;
  box-sizing: border-box;
  -webkit-overflow-scrolling: touch;
}

/* ===== Table ===== */
table {
  width: 95%;
  border-collapse: collapse;
  background-color: #fff;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  border-radius: 5px;
  overflow: hidden;
  table-layout: fixed; /* กำหนดตารางให้ fix width */
  word-wrap: break-word;
  word-break: break-word;
  margin: 0 auto; /* จัดให้อยู่ตรงกลางจอแนวนอน */
}


th, td {
  padding: 8px;
  text-align: center;
  vertical-align: middle;
  border: 1px solid #2c3e50;
  font-size: 14px;
  max-width: 100px;

  /* ลบคำสั่งตัดข้อความออก */
  overflow: visible;     /* ให้แสดงข้อความทั้งหมด */
  white-space: normal;   /* อนุญาตให้ขึ้นบรรทัดใหม่ได้ */
  word-wrap: break-word; /* ตัดคำและขึ้นบรรทัดใหม่ถ้าคำยาวเกิน */
  word-break: break-word; /* รองรับการตัดคำในเบราว์เซอร์ต่าง ๆ */
}



th {
  background: linear-gradient(to right, #2c3e50, #4b6584);
  color: white;
  text-transform: uppercase;
}

.table-striped tr:nth-child(odd) {
  background-color: #f5f5f7;
}
.table-striped tr:hover {
  background-color: #e5e5e7;
}

table a {
  color: #0071e3;
  font-weight: bold;
  text-decoration: none;
}
table a:hover {
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
  box-shadow: 0 4px 6px rgba(0,0,0,0.2);
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
  color: #0071e3;
  font-weight: bold;
  text-decoration: none;
}
.link:hover {
  text-decoration: underline;
}

/* ===== Popup ===== */
.popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: none; /* เริ่มซ่อน */
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.popup-overlay.active {
  display: flex; /* แสดง popup */
}

.popup-content {
  background: linear-gradient(to right, #f0f2f5, #dfe9f3);
  padding: 8px;
  border-radius: 10px;
  width: 80%;
  max-width: 1000px;
  max-height: 500px;
  overflow-y: auto;
  text-align: center;
  position: relative;
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.close-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
  font-size: 18px;
  font-weight: bold;
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
  .header, .top-section, .filter-container, .button-group {
    flex-direction: column;
    align-items: stretch;
    width: 100%;
    gap: 10px;
  }

  .header button, .button-group button {
    width: 100%;
    padding: 12px 0;
    font-size: 14px;
  }

  table {
    width: 100%;
  }

  th, td {
    font-size: 12px;
    padding: 8px;
  }

  .search-box {
    max-width: 100%;
  }
}

@media (max-width: 480px) {
  th, td {
    font-size: 10px;
    padding: 4px;
  }

  /* ซ่อนคอลัมน์บางส่วนถ้าต้องการ */
  td:nth-child(10), td:nth-child(11), td:nth-child(12) {
    display: none;
  }

  .search-box input {
    font-size: 0.9rem;
  }

  .popup-content {
    width: 90%;
    padding: 10px;
  }
}

    </style>
</head>
<body>
    <div class="header" >
        <h2>บิลที่ไม่สำเร็จ</h2>
        <div class="header-buttons">
            <a href="alertaccount"><button class="btn-so">แจ้งเตือนบัญชี</button></a>
            <a href="dashboard"><button class="btn-so">หน้าหลัก</button></a>
        </div>
    </div>
    <div class="top-section">    
        <div class="search-box">
            <input type="text" id="search-input" placeholder=" ค้นหา เลขที่บิล" onkeyup="searchTable()"> 

        </div>
    
        <div class="button-group">
        </div>
    </div>
            <table>
                <thead>
                <label>
            </label>
                    <tr>
                        <th>ลบ</th>
                        <th>REF</th>
                        <th>อ้างอิงใบส่งของ</th>
                        <th>ชื่อลูกค้า</th>
                        <th>เบอร์ติดต่อ</th>
                        <th>วันที่จัดส่ง</th>
                        <th>ผู้เปิดบิล</th>
                        <th>หมายเหตุ</th>
                        <th>ข้อมูลสินค้า</th>
                        <th>หมายเหตุ</th>
                    </tr>
                </thead>
               <tbody id="table-body">
   @foreach($items as $item)
    @php
        // ใช้ so_detail_id, po_id, doc_id สำหรับแสดงผล
        $alldetailId = $item->so_detail_id ?? $item->po_id ?? $item->doc_id ?? '';

        // ใช้ so_detail_id, po_detail_id, doc_id สำหรับหาข้อมูล detail
        $detailId = $item->so_detail_id ?? $item->po_detail_id ?? $item->doc_id ?? '';

        // ค่าฟอลแบ็คอื่น ๆ
        $billid = $item->billid ?? '';
        $customerName = $item->customer_name ?? $item->store_name ?? $item->com_name ?? '';
        $customerTel = $item->customer_tel ?? $item->store_tel ?? $item->contact_tel ?? '';
        $rawDate = $item->date_of_dali ?? $item->recvDate ?? $item->datestamp ?? null;
        $dateOfDali = $rawDate ? \Carbon\Carbon::parse($rawDate)->format('d/m/Y') : '';
        $empName = $item->emp_name ?? '';
        $billtype = $item->billtype ?? '';
        $notes = $item->notes ?? '';
        $saleName = $item->sale_name ?? '';
        $NG = $item->NG ?? '';
        $solve = $item->solve ?? '';

        // กำหนดตารางสำหรับปุ่มล้างตามตัวแปร detailId
        if (isset($item->so_detail_id)) {
            $table = 'tblbill';
        } elseif (isset($item->po_detail_id)) {
            $table = 'pobills';
        } elseif (isset($item->doc_id)) {
            $table = 'docbills';
        } else {
            $table = '';
        }
    @endphp

    @if($item->solve != null && $item->statusdeli == '0')
        <tr>
            <td>
                <button class="updatestatusdeli"
                        data-id="{{ $detailId }}"
                        data-table="{{ $table }}">
                    ส่งเรื่อง
                </button>
            </td>
            <td>{{ $alldetailId }}</td>
            <td>{{ $billid }}</td>
            <td>{{ $customerName }}</td>
            <td>{{ $customerTel }}</td>
            <td>{{ $dateOfDali }}</td>
            <td>{{ $empName }}</td>
            <td>{{ $NG }}</td>
            <td>{{ $solve}}</td>
            <td>
                <a href="javascript:void(0);" 
                onclick="openPopup(
                    '{{ $alldetailId }}',
                    '{{ $billid }}',
                    '{{ $item->so_id ?? '' }}',
                    '{{ $customerName }}',
                    '{{ $customerTel }}',
                    '{{ $dateOfDali }}',
                    '{{ $saleName }}',
                    '{{ $table }}',
                    '{{ $notes }}',
                    '{{ $detailId }}'
                )">
                    เพิ่มเติม
                </a>
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
                        <th>REF</th>
                        <th>อ้างอิงใบส่งของ</th>
                        <th>อ้างอิงใบสั่งขาย</th>
                        <th>ชื่อลูกค้า</th>
                        <th>ติดต่อ</th>
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
                <textarea id="popup-body-3" readonly style="width: 960px; height: 70px;" readonly>
                </textarea>
        </div>
    </div>
</div>

<script>
function openPopup(alldetailId, billid, so_id, customerName, customerTel, dateOfDali, saleName, table,notes, detailId) {
    console.log('table:', table, 'detailId:', detailId);
    document.getElementById("popup").style.display = "flex";

    const popupBody = document.getElementById("popup-body-1");
    popupBody.innerHTML = `
        <tr>
            <td>${alldetailId || '-'}</td>
            <td>${billid || '-'}</td>
            <td>${so_id || '-'}</td>
            <td>${customerName || '-'}</td>
            <td>${customerTel || '-'}</td>
            <td>${dateOfDali || '-'}</td>
            <td>${saleName || '-'}</td>
        </tr>
    `;
    document.getElementById("popup-body-3").value = notes;
    let secondPopupBody = document.getElementById("popup-body");
    secondPopupBody.innerHTML = "<tr><td colspan='4'>Loading...</td></tr>";

    fetch(`/getall-bill-detail/${detailId}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                secondPopupBody.innerHTML = "";
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
document.querySelectorAll('.updatestatusdeli').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const table = this.getAttribute('data-table');

        fetch('{{ route("updatestatusdeli") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id: id, table: table })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('เกิดข้อผิดพลาด:', error);
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

        // รวมข้อความจาก index 1 และ 2
        let text1 = cells[1] ? cells[1].textContent.toLowerCase() : '';
        let text2 = cells[2] ? cells[2].textContent.toLowerCase() : '';
        let combinedText = text1 + " " + text2;

        // ถ้าตรงกับ searchInput แสดง row
        if (combinedText.indexOf(searchInput) > -1) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    }
}


function searchTablename() {
    let searchInput = document.getElementById("search-name").value.toLowerCase();
    let table = document.querySelector("table tbody");
    let rows = table.getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        let row = rows[i];
        let cells = row.getElementsByTagName("td");

        // ค้นหาในคอลัมน์ที่ 6 (index = 5)
        let columnText = cells[6] ? cells[6].textContent.toLowerCase() : '';

        if (columnText.indexOf(searchInput) > -1) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    }
}

</script>





    

</body>
</html>