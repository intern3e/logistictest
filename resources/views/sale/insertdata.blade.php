    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>ระบบจัดเตรียมสินค้า</title>
        <style>
            /* ฟอนต์ Sarabun */
            @font-face {
                font-family: 'Sarabun';
                src: url('fonts/Sarabun-Regular.woff2') format('woff2'),
                    url('fonts/Sarabun-Regular.woff') format('woff'),
                    url('fonts/Sarabun-Regular.ttf') format('truetype');
                font-weight: normal;
                font-style: normal;
            }

            /* ตั้งค่าพื้นหลังและฟอนต์ */
            body {
                font-family: 'Sarabun', sans-serif;
                background: #f0f4f8;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }

            /* กรอบของฟอร์ม */
            .container {
                width: 100%;
                max-width: 900px;
                background: white;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            }

            /* หัวข้อฟอร์ม */
            .header {
                background: linear-gradient(to right, #2c3e50, #4b6584);
                padding: 20px;
                border-radius: 8px;
                color: white;
                text-align: center;
                font-size: 24px;
                margin-bottom: 30px;
            }

            /* ฟอร์มอินพุต */
            .form-control {
                background-color: #f9f9f9;
                border-radius: 8px;
                border: 1px solid #ddd;
                padding: 12px;
                transition: 0.3s;
                width: 97%;
                margin-bottom: 15px;
                font-size: 16px;
            }
            /* ฟอร์มอินพุต */
            .form-control1 {
                background-color: #f9f9f9;
                border-radius: 8px;
                border: 1px solid #ddd;
                padding: 12px;
                transition: 0.3s;
                width: 80%;
                margin-bottom: 15px;
                font-size: 16px;
            }

            .form-control:focus {
                border-color: #f39c12;
                box-shadow: 0 0 8px rgba(243, 156, 18, 0.5);
            }

            /* ปุ่มที่ใช้ */
            .btn-custom {
                background: #f39c12;
                color: white;
                font-size: 18px;
                font-weight: bold;
                padding: 12px;
                border-radius: 8px;
                width: 100%;
                transition: 0.3s;
                border: none;
            }

            .btn-custom:hover {
                background: #e67e22;
                transform: translateY(-2px);
            }

            /* ตารางแสดงข้อมูล */
            table {
                width: 100%;
                margin-top: 20px;
                border-radius: 8px;
                overflow: hidden;
                border-collapse: collapse;
            }

            .table th {
                background: linear-gradient(to right, #2c3e50, #4b6584);
                color: white;
                font-weight: bold;
                padding: 12px;
            }

            .table td {
                border: 1px solid #ddd;
                padding: 12px;
                text-align: center;
                font-size: 14px;
            }

            .table-striped tbody tr:nth-child(odd) {
                background: #f9f9f9;
            }
            .delete-btn {
                padding: 5px 10px;
                background-color: #e74c3c;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
            }

            .delete-btn:hover {
                background-color: #c0392b;
            }
            .insert-btn {
                padding: 5px 10px;
                background-color: green;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
            }

            .insert-btn:hover {
                background-color: green;
            }
            .action-container {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        }

        .action-container label {
            margin-top: 15px;
            margin-right: 10px; /* เพื่อเว้นระยะระหว่าง checkbox และปุ่ม */
        }

        .insert-btn {
            margin-top: 15px;
            width: 150px;
            height: 30px;
            margin-left: 10px; /* เพิ่มระยะห่างจาก checkbox */
        }
        .insert-btn {
            background-color: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .insert-btn:hover {
            background-color: #45a049; /* สีเมื่อ hover */
        }

        .insert-btn:active {
            background-color: #388e3c; /* สีเมื่อถูกคลิก */
        }

        .btn-search {
            background-color: #f39c12; /* สีพื้นหลัง */
            color: white; /* สีข้อความ */
            border: none; /* ไม่มีขอบ */
            border-radius: 8px; /* ขอบมน */
            padding: 12px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-search:hover {
            background-color: #e67e22; /* สีเมื่อ hover */
        }

        .btn-search:active {
            background-color: #d35400; /* สีเมื่อคลิก */
    }
        .btn-primary {
        background: #f39c12;
        color: white;
        font-size: 18px;
        font-weight: bold;
        padding: 12px 20px;
        border-radius: 8px;
        width: 100%;
        transition: 0.3s;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #e67e22; 
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .btn-primary:active {
        background-color: #d35400; /* สีเมื่อคลิก */
        transform: scale(0.98); 
    }

            /* Media Queries สำหรับขนาดหน้าจอที่ต่างกัน */
            @media (max-width: 768px) {
                .container {
                    padding: 20px;
                }

                .header {
                    font-size: 22px;
                }

                .form-control {
                    padding: 10px;
                    font-size: 14px;
                }

                .btn-custom {
                    font-size: 16px;
                    padding: 10px;
                }
            }

            @media (max-width: 480px) {
                .header {
                    font-size: 20px;
                }

                .btn-custom {
                    font-size: 14px;
                    padding: 8px;
                }

                .form-control {
                    padding: 8px;
                    font-size: 12px;
                }

                table th, table td {
                    font-size: 12px;
                    padding: 8px;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                ระบบเปิดบิลสินค้า
            </div>
                <div class="text-center mb-4">
                    <h3 class="text-dark">🔹 เปิดบิลสินค้า 🔹</h3>
                </div>

                <div class="mb-3">
                    <label class="form-label">เลขที่ SO:</label>

                    <form action="{{ route('sodetail.post') }}" method="POST">
                    @csrf
                    <div style="display: flex; justify-content: space-between;">
                        <input type="text" class="form-control" id="so_number" name="so_number" style="width: 80%;" required value="{{ old('so_number', $so->so_id ?? '') }}">
                        <button type="submit" class="btn-search" style="width: 14%; height: 45px;">🔍 ค้นหา</button>
                    </div>
                </form>
                </div>

                
                
                
                @if(session('error'))
                    <script>
                        alert("{{ session('error') }}");
                    </script>
                @endif
                
    <form id="billForm">
                @if(isset($so))
                    <div class="mb-3">
                        <label class="form-label">รหัสลูกค้า:</label>
                        <input type="text" class="form-control" name="customer_id" value="{{$so->customer_id }}"readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ชื่อบริษัท:</label>
                        <input type="text" class="form-control" name="customer_name" value="{{ $customer_name }}" readonly>
                    </div>
                <div class="mb-3">
                        <label class="form-label">เบอร์ติดต่อ:</label>
                        <input type="text" class="form-control" name="customer_tel" value="{{$customer_tel}}" >
                    </div> 
                    <div class="mb-3"> 
                        <label class="form-label">ที่อยู่จัดส่ง:</label>
                        <input type="text" class="form-control" name="customer_address" value="{{ $customer_address }}" >
                    </div>
                    <div class="mb-3">  
                        <label class="form-label">ละติจูด ลองจิจูด:</label>
                    <div style="display: flex; justify-content: space-between;">
                        <input type="text" class="form-control" name="customer_la_long" id="customer_la_long" value="{{ $customer_la_long }}">
                        <button type="button" class="btn-custom" onclick="openGoogleMaps()">Google Maps</button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">แผนที่:</label>
                        <iframe id="mapFrame" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                    
                    <script>
                        function updateMap() {
                            let coords = document.getElementById('customer_la_long').value;
                            if (coords) {
                                document.getElementById('mapFrame').src = `https://www.google.com/maps?q=${coords}&output=embed`;
                            }
                        }
                    
                        // อัปเดตแผนที่เมื่อมีการเปลี่ยนแปลงค่าพิกัด
                        document.getElementById('customer_la_long').addEventListener('input', updateMap);
                    
                        // โหลดแผนที่เริ่มต้น
                        updateMap();
                    </script>
                    
                    </div>
                     <br>
                    <div class="mb-3">
                        <label class="form-label">วันที่กำหนดส่ง:</label>
                        <br>
                        <br>
                        <input type="date" class="form-control" name="date_of_dali">
                    </div>
                    <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>เลือกจัดส่ง</th>
                        <th>รหัสสินค้า</th>
                        <th>รายการ</th>
                        <th>จำนวน</th>
                        <th>ราคา/หน่วย</th>
                        <th>ลบ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)  
                    <tr> 
                        <td><input type="checkbox" class="form-control1" name="status[]"readonly></td>
                        <td><input type="text" class="form-control1" name="item_id[]" value="{{ $item['item_id'] }}"readonly></td>
                        <td><input type="text" class="form-control1" name="item_name[]" value="{{ $item['item_name'] }}"readonly></td>
                        <td>
                            <input type="number" class="form-control1 item_quantity" name="item_quantity[]" 
                            value="{{ $item['item_quantity'] }}"readonly>
                        </td>
                        <td>
                            <input type="number" class="form-control1 item_unit_price" name="item_unit_price[]" 
                            value="{{ $item['item_unit_price'] }}">
                        </td>
                        <td><button type="button" class="btn btn-danger delete-btn">ลบ</button></td>
                    </tr>
            
            @endforeach  
        </tbody>
        
        </table>

    <div class="action-container">
        <label><input type="checkbox" name="checkall"> เลือกทั้งหมด</label>
        <button type="button" class="btn btn-danger insert-btn">เพิ่มสินค้า</button>
    </div>
                <div class="mb-3">
                    <label class="form-label">เเจ้งเพิ่มเติม:</label>
                    <br>
                    <br>
                    <textarea class="form-control" name="additional_notes" rows="4"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">ผู้เปิดบิล</label>
                    <br>
                    <br>
                    <input type="text" class="form-control" name="emp_name">
                </div>
                <br>
                <input type="hidden" name="so_id" value="{{ $so->so_id }}">
                <button type="submit" class="btn btn-primary" onclick="confirmSubmit(event)">เปิดบิล</button>
                
        </div> 
    </form>
                @endif  

    <script>
                    document.getElementById('billForm').addEventListener('submit', function (event) {
                        event.preventDefault();
                
                        let formData = new FormData();
                        formData.append('so_id', document.querySelector('input[name="so_id"]').value);
                        formData.append('customer_id', document.querySelector('input[name="customer_id"]').value);
                        formData.append('customer_tel', document.querySelector('input[name="customer_tel"]').value);
                        formData.append('customer_address', document.querySelector('input[name="customer_address"]').value);
                        formData.append('customer_la_long', document.querySelector('input[name="customer_la_long"]').value);
                        formData.append('date_of_dali', document.querySelector('input[name="date_of_dali"]').value);
                        formData.append('notes', document.querySelector('textarea[name="additional_notes"]').value);
                
                        let statuses = [];
                        let hasSelectedItems = false;
                
                        document.querySelectorAll('tbody tr').forEach((row, index) => {
                            let checkbox = row.querySelector('input[name="status[]"]');
                            if (checkbox && checkbox.checked) {
                                hasSelectedItems = true;
                                formData.append('item_id[]', row.querySelector('input[name="item_id[]"]').value);
                                formData.append('item_name[]', row.querySelector('input[name="item_name[]"]').value);
                                formData.append('item_quantity[]', row.querySelector('input[name="item_quantity[]"]').value);
                                formData.append('item_unit_price[]', row.querySelector('input[name="item_unit_price[]"]').value);
                                formData.append('status[]', 'checked');
                            }
                        });
                
                        if (!hasSelectedItems) {
                            alert("กรุณาเลือกสินค้าอย่างน้อย 1 รายการ");
                            return;
                        }
                
                        fetch('{{ route("insert.post") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.success);
                            } else if (data.error) {
                                alert(data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('มีข้อผิดพลาดในการส่งข้อมูล');
                        });
                    });
                
                    document.addEventListener("DOMContentLoaded", function() {
                        // Set the default value for the "date_of_dali" input to tomorrow's date
                        let tomorrow = new Date();
                        tomorrow.setDate(tomorrow.getDate() + 1);
                        let formattedDate = tomorrow.toISOString().split('T')[0];
                        document.querySelector('input[name="date_of_dali"]').value = formattedDate;
                
                        // Handling "select all" checkbox functionality
                        const selectAllCheckbox = document.querySelector('input[name="checkall"]');
                        if (selectAllCheckbox) {
                            selectAllCheckbox.addEventListener('change', function() {
                                const checkboxes = document.querySelectorAll('input[type="checkbox"]:not([name="checkall"])');
                                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                            });
                        }
                
                        const tableBody = document.querySelector('table tbody');
                        if (tableBody) {
                            tableBody.addEventListener('click', function(e) {
                                if (e.target.classList.contains('delete-btn')) {
                                    var row = e.target.closest('tr');
                                    row.remove();
                                }
                            });
                        }
                
                        const insertBtn = document.querySelector('.insert-btn');
                        if (insertBtn) {
                            insertBtn.addEventListener('click', function() {
                                var newRow = document.createElement('tr');
                                newRow.innerHTML = `
                                    <td><input type="checkbox" class="form-control1" name="status[]"></td>
                                    <td><input type="text" class="form-control1" name="item_id[]"></td>
                                    <td><input type="text" class="form-control1" name="item_name[]"></td>
                                    <td>
                                        <input type="number" class="form-control1 item_quantity" name="item_quantity[]" oninput="calculateTotal(this)">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control1 item_unit_price" name="item_unit_price[]" oninput="calculateTotal(this)">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control1 item_total" name="item_total[]" value="" readonly>
                                    </td>
                                    <td><button type="button" class="btn btn-danger delete-btn">ลบ</button></td>
                                `;
                                tableBody.appendChild(newRow);
                            });
                        }
                
                        window.calculateTotal = function(input) {
                            var row = input.closest('tr');
                            var quantity = row.querySelector('.item_quantity').value;
                            var unitPrice = row.querySelector('.item_unit_price').value;
                            var total = quantity * unitPrice;
                            row.querySelector('.item_total').value = total.toFixed(2);
                        }
                    });
                
                    function openGoogleMaps() {
                        const mapWindow = window.open(
                            "https://www.google.com/maps/@13.7563,100.5018,14z",
                            "Google Maps",
                            "width=800,height=600"
                        );
                    }
        function confirmSubmit(event) {
        event.preventDefault(); // ป้องกันการ submit แบบปกติ

        // แสดงการแจ้งเตือน
        let confirmation = confirm("คุณต้องการเปิดบิลใช่หรือไม่?");

        if (confirmation) {
            // หากผู้ใช้กดตกลง
            let formData = new FormData(document.getElementById('billForm')); // เก็บข้อมูลฟอร์ม

            fetch('{{ route("insert.post") }}', { // ส่งข้อมูลฟอร์มไปยังเส้นทาง insert.post
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // ส่ง CSRF Token
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.success); // แจ้งเตือนสำเร็จ
                    window.location.href = '/dashboard'; // เปลี่ยนเส้นทางไปยังหน้า dashboard
                } else if (data.error) {
                    alert(data.error); // แจ้งเตือนข้อผิดพลาด
                }
            })
            .catch((error) => {
                console.error('Error:', error); // แสดงข้อผิดพลาดในคอนโซล
                alert('มีข้อผิดพลาดในการส่งข้อมูล');
            });

        } else {
            // หากผู้ใช้กดยกเลิก
            alert("คุณยกเลิกการเปิดบิล.");
        }
    }


    </script>
                