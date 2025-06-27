<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <title>ประวัติระบบจัดเส้นทางเอกสารเพิ่มเติม</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="{{ asset('css/historydoc.blade.css') }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

</head>
<body>
    <div class="header">
        <h2>ประวัติระบบจัดเส้นทางเอกสารเพิ่มเติม</h2>
        <a href="http://server_update:8000/solist"><button class="btn-so">หน้าหลัก</button></a>
    </div>

    <div class="container">
        <div class="top-section">
    <form method="GET" action="{{ route('document.historydoc') }}" class="filter-form" id="autoSearchForm">
        <label for="date">📅 วันที่: เดือน / วัน / ปี</label>
        <input type="date" id="date" name="date" value="{{ request('date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
        <button type="submit" style="display: none;">ค้นหา</button>
    </form>


<script>
    const form = document.getElementById('autoSearchForm');
    const dateInput = document.getElementById('date');
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
                <button type="button" class="btn btn-primary" onclick="updateStatusdocback()">คืนสถานะ</button>
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
            @if($item->status == 1)
            <tr>
                <td>
                    <input type="checkbox" class="form-control1" name="status[]" data-doc-detail-id="{{ $item->doc_id }}">
                </td>
                <td>{{ $item->doc_id }}</td>
                <td>{{ $item->com_name }}</td>
                <td>{{ $item->contact_name }}</td>
                <td>{{ $item->contact_tel}}</td>
                <td>{{ $item->doctype }}</td>
                <td>{{ $item->emp_name }}</td>
                <td>
    <div class="date-container">
        <span class="date-display" id="date-display-{{ $item->doc_id }}">
            {{ \Carbon\Carbon::parse($item->datestamp)->format('d/m/Y') }}
        </span>
        <div class="date-edit-form" id="date-edit-form-{{ $item->doc_id }}" style="display:none;">
            <input type="date" class="form-control form-control-sm" id="new-date-{{ $item->doc_id }}" 
                value="{{ \Carbon\Carbon::parse($item->datestamp)->format('Y-m-d') }}"
                onchange="validateDateFormat(this)">
            <div class="mt-1">
                <button type="button" class="btn btn-sm btn-success" 
                        onclick="saveNewDate('{{ $item->doc_id}}')">บันทึก</button>
                <button type="button" class="btn btn-sm btn-secondary" 
                        onclick="cancelEdit('{{ $item->doc_id }}')">ยกเลิก</button>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-primary edit-date-btn" 
                id="edit-btn-{{ $item->doc_id }}"
                onclick="showEditForm('{{ $item->doc_id }}')">
            <i class="fas fa-edit"></i> แก้ไข
        </button>
    </div>
</td>
                <td>
                <a href="javascript:void(0);" onclick="openPopup('{{ $item->doc_id }}', '{{ $item->com_name }}', '{{ $item->com_address }}', '{{ $item->contact_name }}', '{{ $item->contact_tel }}', '{{ $item->amount }}', '{{ $item->notes }}')">
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

<script>
    function showEditForm(doc_id) {
        document.getElementById('date-display-' + doc_id).style.display = 'none';
        document.getElementById('edit-btn-' + doc_id).style.display = 'none';
        document.getElementById('date-edit-form-' + doc_id).style.display = 'block';
    }
    function cancelEdit(doc_id) {
        document.getElementById('date-display-' + doc_id).style.display = 'inline';
        document.getElementById('edit-btn-' + doc_id).style.display = 'inline-block';
        document.getElementById('date-edit-form-' + doc_id).style.display = 'none';
    }
    function validateDateFormat(inputElement) {
        let dateValue = inputElement.value;
        if (!dateValue || dateValue.includes('undefined')) {
            let today = new Date();
            let yyyy = today.getFullYear();
            let mm = String(today.getMonth() + 1).padStart(2, '0');
            let dd = String(today.getDate()).padStart(2, '0');
            inputElement.value = yyyy + '-' + mm + '-' + dd;
        }
    }
    function saveNewDate(doc_id) {
        let newDate = document.getElementById('new-date-' + doc_id).value;

        if (!newDate || newDate === 'undefined') {
            alert('กรุณาระบุวันที่ให้ถูกต้อง');
            return;
        }

        console.log({ doc_id: doc_id, new_date: newDate }); // ตรวจสอบข้อมูลก่อนส่ง

        $.ajax({
            url: "{{ route('updatedoc.delivery.date') }}", 
            method: "POST",
            data: {
                doc_id: doc_id,
                new_date: newDate,
                _token: "{{ csrf_token() }}"
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log(response); // ดูข้อมูลที่เซิร์ฟเวอร์ส่งกลับมา
                if (response.success) {
                    let formattedDate = formatDate(newDate);
                    document.getElementById('date-display-' + doc_id).innerText = formattedDate;
                    alert("วันที่ถูกอัพเดทเรียบร้อยแล้ว");
                    cancelEdit(doc_id);
                    window.location.reload();
                } else {
                    alert("เกิดข้อผิดพลาด: " + response.message);
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText); // ดูรายละเอียดของข้อผิดพลาดที่เกิดขึ้น
                alert("เกิดข้อผิดพลาด: " + xhr.responseText);
            }
        });
    }

    function formatDate(dateString) {
        if (!dateString || dateString === 'undefined') {
            return '';
        }
        try {
            if (dateString.includes('-') && dateString.split('-').length === 3) {
                let parts = dateString.split('-');
                let day = parts[2].padStart(2, '0');
                let month = parts[1].padStart(2, '0');
                let year = parts[0];
                return day + '/' + month + '/' + year;
            }

            let date = new Date(dateString);
            if (isNaN(date.getTime())) {
                return dateString;
            }

            let day = String(date.getDate()).padStart(2, '0');
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let year = date.getFullYear();
            return day + '/' + month + '/' + year;
        } catch (e) {
            console.error("Error formatting date:", e);
            return dateString;
        }
    }
</script>
    
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
    function toggleCheckboxes() {
        var checkAllBox = document.getElementById('checkAll');
        var checkboxes = document.querySelectorAll('input[type="checkbox"]:not(#checkAll)');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = checkAllBox.checked;
        });
    }
</script>
<script>
    function updateStatusdocback() {
        let selectedIds = [];
        let checkboxes = document.querySelectorAll("input[name='status[]']:checked");

        checkboxes.forEach(checkbox => {
            let row = checkbox.closest('tr');
            let doc_id = row.querySelector('td:nth-child(2)').textContent; 
            selectedIds.push(doc_id);
        });

        if (selectedIds.length === 0) {
            alert("กรุณาเลือกบิลที่ต้องการอัปเดต");
            return;
        }

        // Proceed to update
        console.log("Updating status for:", selectedIds); // Log the selected IDs

        fetch('/update-statusdocback', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ doc_id: selectedIds })
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