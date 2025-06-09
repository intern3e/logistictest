<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> ระเอกสารชั่วคราว</title>
     <link rel="stylesheet" href="{{ asset('css/dashboarddoc.blade.css') }}">
 
</head>
<body>
    <div class="header">
        <h2> เอกสารชั่วคราว</h2>
        <div class="buttons">
            <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>
    
            <a href="{{ route('document.insertdoc') }}" class="btn btn-warning">➕ เปิดบิลdoc</a>
            
            @csrf
            <a href="{{ route('home') }}" button  type="submit" class="btn btn-danger"style="background-color:red;">🚪 หน้าหลัก</a>
        </div>
    </div>
    
    <!-- Filter & Search Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('document.dashboarddoc') }}" class="filter-form" id="autoSearchForm">
            <label for="date">📅 วันที่: เดือน / วัน / ปี</label>
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
    
    
        <div class="search-box">
            <input type="text" id="search-input" placeholder=" ค้นหา เลขที่บิล" onkeyup="searchTable()">
        </div>
    </div>
    <div class="table-container">
<table>
    <thead>
        <tr>
            <th>เลขที่บิล</th>
            <th>บริษัท</th>
            <th>ผู้ติดต่อ</th>
            <th>เบอร์โทร</th>
            <th>ประเภทงาน</th>
            <th>ผู้เปิดบิล</th>
            <th>วันที่</th>
            <th>ข้อมูลรายละเอียด</th>
        </tr>
    </thead>
    <tbody id="table-body">
        @foreach($docbill as $item)
        <tr>
            <td>{{ $item->doc_id }}</td>
            <td>{{ $item->com_name }}</td>
            <td>{{ $item->contact_name }}</td>
            <td>{{ $item->contact_tel}}</td>
            <td>{{ $item->doctype }}</td>
            <td>{{ $item->emp_name }}</td>
            <td>{{ \Carbon\Carbon::parse($item->time)->format('d/m/Y') }}</td>
            <td>
                <a href="javascript:void(0);" onclick="openPopup(
                    '{{ $item->doc_id }}',
                    '{{ $item->com_name }}',
                    '{{ $item->com_address }}',
                    '{{ $item->contact_name}}',
                    '{{ $item->contact_tel}}',
                    '{{ $item->notes }}',
                )">
                    เพิ่มเติม
                </a>
            </td>

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
                    <th>รายการ</th>
                    <th>จำนวน</th>      
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
        function openPopup(doc_id,com_name,com_address,contact_name,contact_tel,notes) {
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
                        secondPopupBody.innerHTML = ""; 
                        data.forEach(item => {
                            secondPopupBody.insertAdjacentHTML("beforeend", `
                                <tr>
                                    <td>${item.item_name}</td>
                                    <td>${item.quantity}</td>
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

{{--searchTable --}}
     <script>
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
            window.onload = function() {
    // Sort the rows by 'so_detail_id' in descending order on page load
    sortTableDescByColumn(0); // Assuming 'so_detail_id' is in the first column (index 0)
};
    </script>
    </body>
    </html>