<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📑 ระบบเปิดบิล</title>
    <style>
  /* --- Global Style --- */
/* --- Global Style --- */
body {
    font-family: 'Poppins', sans-serif;
    background-color: rgb(233, 233, 233); /* Light gray background */
    margin: 0;
    padding: 0;
}

/* --- Header Style --- */
.header {
    background: linear-gradient(to right, #2c3e50, #4b6584);
    margin: 40px 5%;
    padding: 20px 5%;
    color: #fff;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
}

.header h4 {
    margin: 0;
    font-size: 2rem;
    font-weight: bold;
}

/* --- Button Container --- */
.buttons {
    display: flex;
    align-items: center;
    gap: 15px;
}

.buttons span {
    color: white;
    font-weight: bold;
}

.buttons a, .buttons button {
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: bold;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.buttons a {
    background-color: #f39c12;
    color: white;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
}

.buttons a:hover {
    background-color: #e67e22;
    transform: scale(1.05);
}

.buttons button {
    background-color: #e74c3c;
    color: white;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
}

.buttons button:hover {
    background-color: #c0392b;
    transform: scale(1.05);
}


/* --- Table Styling --- */
.table-container {
    background: #f9f9f9; /* Light gray background for table */
    margin: 0 5%;
    padding: 10px;
    border-radius: 12px;
    box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    width: 99%;
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
    border: 1px solid #ccc; /* Light gray for borders */
    font-size: 1rem;
    white-space: normal; /* Allow wrapping of text in cells */
}


th {
    background: #00389f; /* Blue background for headers */
    color: white;
}

tr:nth-child(odd) {
    background-color: #f8f9fa; /* Light gray for odd rows */
}

tr:hover {
    background-color: #e1e5ea; /* Light gray on hover */
    transition: 0.2s;
}

/* --- Link Style --- */
td a {
    color: #27ae60; /* Green for links */
    font-weight: bold;
    text-decoration: none;
}

td a:hover {
    text-decoration: underline;
}

/* Filter & Search Section */
.filter-container {
    background: #ffffff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    margin: 20px 5%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
}

.filter-form {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0px 5%;
}

.filter-form label {
    font-weight: bold;
    color: #2c3e50;
}

.filter-form input {
    padding: 8px;
    border-radius: 5px;
    font-size: 1rem;
}

.filter-form button {
    padding: 8px 12px;
    border: none;
    background: #27ae60; /* Green for button */
    color: white;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

.filter-form button:hover {
    background: #2980b9; /* Dark blue on hover */
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

.search-box button {
    padding: 10px 15px;
    background: #2ecc71; /* Green for search button */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
    font-weight: bold;
}

.search-box button:hover {
    background: #27ae60; /* Darker green on hover */
    transform: scale(1.05);
}

/* Popup Styles */
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

/* Responsive Design */
@media (max-width: 768px) {
    .filter-container {
        flex-direction: column;
        align-items: flex-start;
    }

    .filter-form, .search-box {
        width: 100%;
    }

    .search-box input {
        width: 100%;
    }
}

.editButton {
    background: #f39c12; /* Orange button color */
    border: none;
    color: white;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s ease-in-out;
}

.editButton:hover {
    background-color: #e67e22;
    transform: scale(1.05);
}

.editButton:active {
    transform: scale(0.95);
}

.aa {
    padding: 20px 200px;
    border: 1px solid #ccc;
    background-color: #f9f9f9;
    border-radius: 5px;
    font-size: 14px;
    color: #333;
    max-width: 800px;
    margin: 10px 0;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
#delete-btn {
    background-color: #dc3545; /* สีแดงเข้ม */
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
}

#delete-btn:hover {
    background-color: #c82333; /* สีแดงเข้มขึ้นเมื่อ hover */
    transform: scale(1.05);
}

#delete-btn:active {
    background-color: #a71d2a; /* สีแดงเข้มขึ้นเมื่อกด */
    transform: scale(0.95);
}




        </style>
</head>
<body>

    <div class="header">
        <h4>📑 ระบบเปิดบิล</h4>
        <div class="buttons">
            <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>

            
            @csrf
            <a href="adminSO" button  type="submit" class="btn btn-danger">🚪 หน้าหลัก</a>
        </div>
    </div>
    
    <!-- Filter & Search Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('sale.dashboard') }}" class="filter-form">
            <label for="date">📅 วันที่:</label>
            <input type="date" id="date" name="date" value="{{ request('date') }}">
            <button type="submit">ค้นหา</button>
        </form>
    
        <div class="search-box">
            <input type="text" id="search-input" placeholder=" ค้นหา เลขที่บิล" onkeyup="searchTable()">
        </div>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>เลขที่บิล</th>
                    <th>อ้างอิงใบสั่งขาย</th>
                    <th>อ้างอิงใบสั่งซื้อ</th>
                    <th>ชื่อลูกค้า</th>
                    <th>ที่อยู่จัดส่ง</th>
                    <th>วันที่จัดส่ง</th>
                    <th>ผู้เปิดบิล</th>
                    <th>ประเภทบิล</th>
                    <th>เวลาออกบิล</th>
                    <th>สถานะ</th>
                    <th>ข้อมูลสินค้า</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @foreach($bill as $item)
                <tr>
                    <td>{{ $item->so_detail_id }}</td> 
                    <td>{{ $item->so_id }}</td>
                    <td>{{ $item->ponum }}</td>
                    <td>{{ $item->customer_name }}</td>  
                    <td>{!! nl2br(e(wordwrap($item->customer_address, 110, "\n", true))) !!}</td>
                    <td>{{ \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') }}</td> 
                    <td>{{ $item->emp_name }}</td> 
                    <td>{{ $item->billtype }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->time)->format('H:i d/m/Y ') }}</td>
                    <td>
                        @if($item->status == 0)
                            กำลังดำเนินการ
                        @else
                            สำเร็จ
                        @endif
                    </td>
                    <td><a href="javascript:void(0);" 
                        onclick="openPopup(
                            '{{ $item->so_detail_id }}',
                            '{{ $item->so_id }}',
                            '{{ $item->ponum }}',
                            '{{ $item->customer_name }}',
                            '{{ $item->customer_address }}',
                            '{{ \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') }}',
                            '{{ $item->sale_name}}',
                            '{{ $item->notes}}',
                            '{{ $item->POdocument}}',
                        )">
                    เพิ่มเติม
                 </a></td>
                </tr>
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
                            <th>ที่อยู่จัดส่ง</th>
                            <th>วันที่จัดส่ง</th>
                            <th>ผู้ขาย</th>
                            <th>เอกสารPO</th>
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
                        </tr>
                    </thead>
                    <tbody id="popup-body">
                    </tbody>
                </table>
                <br>
                <textarea id="popup-body-3" readonl style="width: 600px; height: 70px;">
                </textarea>
                <div>
                    <br>
                <button id="delete-btn" style="background-color: red; color: white;">ลบบิล</button>
               </div>
                


            </div>
        </div>
    </div>
    
    <script>
function openPopup(soDetailId, so_id, ponum, customer_name, customer_address, date_of_dali, sale_name, notes, POdocument) {
    document.getElementById("popup").style.display = "flex";

    let poDocumentButton = POdocument 
        ? `<a href="storage/po_documents/${POdocument}" target="_blank"><button>ดูเอกสาร</button></a>` 
        : "ไม่มีเอกสาร";

    document.getElementById("popup-body-1").innerHTML = `
        <tr>
            <td>${soDetailId}</td>
            <td>${so_id}</td>
            <td>${ponum}</td>
            <td>${customer_name}</td>
            <td>${customer_address}</td>
            <td>${date_of_dali}</td>
            <td>${sale_name}</td>
            <td>${poDocumentButton}</td>
        </tr>
    `;

    let secondPopupBody = document.getElementById("popup-body");
    secondPopupBody.innerHTML = "<tr><td colspan='3'>Loading...</td></tr>";

    let thirdPopupBody = document.getElementById("popup-body-3");
    thirdPopupBody.value = notes || "ไม่มีหมายเหตุ";

    fetch(`/get-bill-detail/${soDetailId}`)
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
                        </tr>
                    `);
                });
                if (!notes) {
                    thirdPopupBody.value = data[0].notes || "ไม่มีหมายเหตุ";
                }
            } else {
                secondPopupBody.innerHTML = "<tr><td colspan='3'>ไม่มีข้อมูล</td></tr>";
                thirdPopupBody.value = "ไม่มีหมายเหตุ";
            }
        })
        .catch(error => {
            console.error("Error fetching data:", error);
            secondPopupBody.innerHTML = "<tr><td colspan='3'>เกิดข้อผิดพลาด</td></tr>";
            thirdPopupBody.value = "เกิดข้อผิดพลาดในการโหลดหมายเหตุ";
        });

    // ตั้งค่า onclick ให้ปุ่มลบใหม่ทุกครั้งที่เปิด Popup
    document.getElementById("delete-btn").setAttribute("onclick", `deleteBill(${soDetailId})`);
}


    function closePopup() {
        document.getElementById("popup").style.display = "none"; // ซ่อน Popup
    }
    
    window.onclick = function(event) {
        let popup = document.getElementById("popup");
        if (event.target === popup) {
            closePopup();
        }
    }
    
    function searchTable() {
        let searchInput = document.getElementById("search-input").value.toLowerCase();
        let table = document.querySelector("table tbody");
        let rows = table.getElementsByTagName("tr");
    
        for (let i = 0; i < rows.length; i++) {
            let row = rows[i];
            let cells = row.getElementsByTagName("td");
            let soDetailId = cells[0].textContent.toLowerCase(); 
    
            if (soDetailId.indexOf(searchInput) > -1) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }
    function deleteBill(soDetailId) {
    // Ask for confirmation before deleting
    const confirmation = confirm("คุณต้องการลบบิลนี้หรือไม่?");
    if (confirmation) {
        fetch(`/delete-bill/${soDetailId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.success); // Show success message
                closePopup(); // Close the popup after successful delete
                location.reload(); // Reload the page to reflect the changes
            } else {
                alert(data.error); // Show error message if deletion fails
            }
        })
        .catch(error => {
            console.error("Error deleting bill:", error);
            alert("เกิดข้อผิดพลาดในการลบบิล");
        });
    }
}
    </script>




    </body>
    </html>