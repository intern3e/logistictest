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
                    background-color: #F5F5F7;
                    color: #1D1D1F;
                    margin: 0;
                    padding: 0;
                }

                .header {
                    background: linear-gradient(to right, #2c3e50, #4b6584);
                    padding: 15px 30px;
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

                .header-buttons {
                    display: flex;
                    gap: 10px;
                    margin-left: auto;
                }

                .header-buttons button {
                    padding: 15px 20px;
                    font-size: 16px;
                    cursor: pointer;
                    border: none;
                    border-radius: 8px;
                    font-weight: bold;
                    text-decoration: none;
                    transition: all 0.3s ease;
                    margin-right: 10px;
                }

                .btn-po {
                    background-color: #0071E3;
                    color: white;
                }

                .btn-so {
                    background-color: red;
                    color: white;
                }

                .header-buttons button:hover {
                    transform: scale(1.05);
                }

                .btn-po:hover {
                    background-color: #005BB5;
                }

                .btn-so:hover {
                    background-color: rgb(179, 1, 1);
                }

                .top-section {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin: 0 auto;
                    margin-bottom: 15px;
                    gap: 20px;
                    width: 90%;
                    padding: 15px 20px;
                    border-radius: 8px;
                    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
                    text-align: center;
                }

                .filter-form {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }

                .filter-form label {
                    font-weight: bold;
                    color: #1D1D1F;
                    font-size: 1rem;
                }

                .filter-form input[type="date"] {
                    padding: 8px;
                    border-radius: 5px;
                    border: 1px solid #00000a;
                    background: #ffffffa4;
                    color: #000;
                    font-size: 1rem;
                }

                .filter-form button {
                    padding: 8px 12px;
                    border: none;
                    background: #0071E3;
                    color: white;
                    border-radius: 5px;
                    cursor: pointer;
                    transition: 0.3s;
                    font-size: 1rem;
                }

                .filter-form button:hover {
                    background: #005BB5;
                }

                .search-box {
                    display: flex;
                    max-width: 300px;
                    width: 100%;
                    margin-left: auto;
                }

                .search-box input {
                    flex-grow: 1;
                    padding: 8px;
                    border-radius: 5px;
                    border: 1px solid #000000;
                    background-color: #ffffff;
                    font-size: 1rem;
                    transition: border-color 0.3s;
                }

                .search-box input:focus {
                    border-color: #0071E3;
                    outline: none;
                }

                .search-box input::placeholder {
                    color: #888;
                    font-size: 0.9rem;
                }

                .button-group {
                    display: flex;
                    gap: 15px;
                    align-items: center;
                }

                .button-group label {
                    font-weight: bold;
                    font-size: 1rem;
                }

                .button-group button {
                    padding: 15px 20px;
                    border-radius: 8px;
                    font-weight: bold;
                    text-decoration: none;
                    border: none;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    background-color: #ff9d2d;
                    color: rgb(255, 255, 255);
                }

                .button-group button:hover {
                    background-color: #b37005;
                    transform: scale(1.05);
                }

                .button-group a button {
                    background-color: #0071E3;
                    color: white;
                }

                .button-group a button:hover {
                    background-color: #005BB5;
                }

                .button-group a:last-child button {
                    background-color: red;
                }

                .button-group a:last-child button:hover {
                    background-color: #ad0404;
                }

                .search-box {
                    display: flex;
                    align-items: center;
                    max-width: 250px;
                }

                .search-box input {
                    flex-grow: 1;
                    padding: 8px;
                    border-radius: 5px;
                    border: 1px solid #6E6E73;
                    background-color: #FFFFFF;
                }
                .table-container {
                            background: #f9f9f9; /* Light gray background for table */
                            margin: 2% 5%;
                            padding: 40px;
                            border-radius: 12px;
                            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
                            overflow: hidden;
                            width: 100%;
                            max-width: 100%; /* Ensure table doesn't overflow the container */
                            transform: scale(0.9); /* Scale down the table to fit the screen */
                            transform-origin: top left; /* Keep the table scaling from the top-left corner */
                        }

                        table {
                            width: 100%;
                            border-collapse: collapse;
                            text-align: center;
                            word-wrap: break-word; /* Ensure text wraps within table cells */
                            font-size: 1rem; /* Adjust the font size to make it smaller */
                        }

                th, td {
                    padding: 12px;
                    border: 1px solid #2c3e50;
                    font-size: 1rem;
                    max-width: 100px; /* กำหนดความกว้างสูงสุด */
                    word-wrap: break-word; /* ถ้าข้อความยาวเกินจะขึ้นบรรทัดใหม่ */
                    word-break: break-word; /* หักคำเมื่อข้อความยาวเกิน */
                }

                th {
                    background-color: red;
                    color: white;
                    text-transform: uppercase;
                }

                .table-striped tr:nth-child(odd) {
                    background-color: #F5F5F7;
                }

                .table-striped tr:hover {
                    background-color: #E5E5E7;
                }
                .link {
                    color: #0071E3;
                    font-weight: bold;
                    text-decoration: none;
                }

                .link:hover {
                    text-decoration: underline;
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
                    max-width: 1000px;
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

                .table-container {
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                    margin: 30px;
                }

                @media (max-width: 768px) {
                    .header, .top-section, .filter-form, .button-group {
                        flex-direction: column;
                        align-items: stretch;
                        width: 100%;
                        gap: 10px;
                    }

                    .header-buttons {
                        width: 100%;
                        margin-left: 0;
                    }

                    .header-buttons button {
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

                    .button-group button {
                        width: 100%;
                        padding: 12px 0;
                    }
                }

                @media (max-width: 480px) {
                    th, td {
                        font-size: 10px;
                        padding: 4px;
                    }

                    /* Hide some columns if necessary */
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
        <h2>แจ้งเตือนเซลล์</h2>
        <div class="header-buttons">
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
        <div class="table-container">
            <table>
                <thead>
                <label>
            </label>
                    <tr>
                        <th>ลบ</th>
                        <th>เลขที่บิล</th>
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
    @foreach($items as $item)
        @php
            // หา id ที่จะใช้แสดงและส่งใน data attribute
            $detailId = $item->so_detail_id ?? $item->po_detail_id ?? $item->doc_id ?? '';
            // กรณีต้องการแสดงข้อมูลอื่น ๆ แบบทั่วไป ให้กำหนด fallback
            $soId = $item->so_id ?? '';
            $ponum = $item->ponum ?? '';
            $billid = $item->billid ?? '';
            $customerName = $item->customer_name ?? '';
            $customerTel = $item->customer_tel ?? '';
            $customerAddress = $item->customer_address ?? '';
            $dateOfDali = isset($item->date_of_dali) ? \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') : '';
            $empName = $item->emp_name ?? '';
            $billtype = $item->billtype ?? '';
            $notes = $item->notes ?? '';
            $saleName = $item->sale_name ?? '';
            @endphp
                    @if($item->NG != null)
                        <tr>
                            <td>
                                @php
                if (isset($item->so_detail_id)) {
                    $detailId = $item->so_detail_id;
                    $table = 'tblbill';
                } elseif (isset($item->po_detail_id)) {
                    $detailId = $item->po_detail_id;
                    $table = 'pobills';
                } elseif (isset($item->doc_id)) {
                    $detailId = $item->doc_id;
                    $table = 'docbills';
                } else {
                    $detailId = '';
                    $table = '';
                }
            @endphp
            <button class="updateNGButton"
                    data-id="{{ $detailId }}"
                    data-table="{{ $table }}">
                ล้าง
            </button>
                </td>
                <td>{{ $detailId }}</td>
                <td>{{ $soId }}</td>
                <td>{{ $ponum }}</td>
                <td>{{ $billid }}</td>
                <td>{{ $customerName }}</td>
                <td>{{ $customerTel }}</td>
                <td>{{ $dateOfDali }}</td>
                <td>{{ $empName }}</td>
                <td>{{ $billtype }}</td>
                <td>{{ $notes }}</td>
                <td>
                    <a href="javascript:void(0);" 
                       onclick="openPopup(
                           '{{ $detailId }}',
                           '{{ $soId }}',
                           '{{ $ponum }}',
                           '{{ $customerName }}',
                           '{{ $customerTel }}',
                           '{{ $customerAddress }}',
                           '{{ $dateOfDali }}',
                           '{{ $saleName }}'
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
            <td>${so_id}</td>
            <td>${ponum}</td>
            <td>${customer_name}</td>
            <td>${customer_tel}</td>
            <td>${customer_address}</td>
            <td>${date_of_dali}</td>
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
document.querySelectorAll('.updateNGButton').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const table = this.getAttribute('data-table');

        console.log("กำลังล้าง NG ของ ID:", id, "จากตาราง:", table);

        fetch('{{ route("update.ng") }}', {
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







    

</body>
</html>