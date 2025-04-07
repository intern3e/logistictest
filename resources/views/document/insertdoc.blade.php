<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เปิดบิลสินค้า</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 20px;
        background-color: rgb(233, 233, 233); /* Light gray background */
    }

    .container {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 30px;
        max-width: 1000px;
        margin: auto;
    }

    .header {
        margin-bottom: 30px;
    }

    h2.text-dark {
        color: #333333;
        border-bottom: 2px solid rgb(30, 62, 122);
        padding-bottom: 10px;
    }

    .form-label, label {
        font-weight: bold;
        margin-top: 15px;
        display: block;
        color: #333;
    }

    input[type="text"], input[type="number"], input[type="file"], select, textarea {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-sizing: border-box;
    }

    input[readonly] {
        background-color: #f1f1f1;
    }

    .input-container, .input-container1 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .checkbox-container {
        margin-top: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .btn, .btn-success, .btn-danger {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-custom {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        margin-left: 10px;
    }

    .btn-custom:hover {
        background-color: #0056b3;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        background-color: #fff;
    }

    table th, table td {
        border: 1px solid #dee2e6;
        padding: 10px;
        text-align: center;
    }

    table thead {
        background-color:rgb(30, 62, 122);
        color: #fff;
    }

    textarea {
        resize: vertical;
    }

    iframe {
        border-radius: 8px;
        margin-top: 15px;
    }

    @media (max-width: 768px) {
        .input-container, .input-container1 {
            grid-template-columns: 1fr;
        }

        .btn-custom {
            margin-top: 10px;
        }
    }
    .form-section {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        flex: 1;
        min-width: 250px;
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-group input,
    .form-group select {
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 10px;
    }

/* Style the select element */
select#cartype {
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #fff;
    color: #333;
    width: 100%;
    max-width: 300px;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
}

/* Add focus effect */
select#cartype:focus {
    border-color: #333;
    outline: none;
}

/* Style for disabled and selected option */
select#cartype option:disabled {
    color: #ccc;
}

/* Style for selected option */
select#cartype option:checked {
    background-color: #f39c12; /* สีส้ม */
    color: #fff;
}
    </style>
</head>
<body>
    <div class="container">
    <div class="header">
        <h3 class="text-dark">สร้างเอกสาร</h3>
    <div class="mb-3">
        <label class="form-label">เลขที่ SO :</label>
        <form id="soSearchForm">
            <div style="display: flex; justify-content: space-between;">
                <input type="text" class="form-control" id="so_number" name="so_number" style="width: 83%;" required>
                <button type="submit" class="btn-search" style="width: 14%; height: 45px;">🔍 ค้นหา</button>
            </div>
        </form>
    </div>

    <form id="billForm">
        @csrf
        <input type="hidden" name="so_id" id="so_id" value="">
        
        <script>
            document.getElementById('so_id').addEventListener('change', function() {
                var soIdValue = this.value; // รับค่าจาก input#so_id
                document.getElementById('doc_id').value = soIdValue; // ตั้งค่าของ doc_id ให้เหมือนกับ so_id
            });
        </script>

        <label for="doctype" >ประเภทบิล</label>
                        <select id="doctype" name="doctype" required>
                            <option value="บิลชั่วคราวเพื่อขาย">บิลชั่วคราวเพื่อขาย</option> 
                            <option value="บิลชั่วคราว Project">บิลชั่วคราว Project</option> 
                            <option value="บิลชั่วคราวส่งแล้วจบเลย">บิลชั่วคราวส่งแล้วจบเลย</option> 
                            <option value="เก็บเช็ค">เก็บเช็ค</option> 
                            <option value="วางบิล">วางบิล</option> 
                            <option value="รับของ">รับของ</option> 
                            <option value="เปลี่ยนของ">เปลี่ยนของ</option> 
                            <option value="คำสั่งพิเศษอื่นๆ">คำสั่งพิเศษอื่นๆ</option> 
                        </select>
                        
            <label>ผู้เปิดบิล :</label>
            <input type="text" id="emp_name" name="emp_name" value="{{ session('emp_name', 'Guest') }}"> 


            <label>ชื่อบริษัท :</label>
            <input type="text" id="customer_name" name="customer_name" >

            <label>ชื่อผู้ติดต่อ :</label>
            <input type="text" id="contact_name" name="contact_name" >

            <label>เบอร์ติดต่อ :</label>
            <input type="text" id="customer_tel" name="customer_tel" >

            <label>ที่อยู่จัดส่ง :</label>
            <input type="text" id="customer_address" name="customer_address" >

            <label >ละติจูด ลองจิจูด :</label>
            <div class="lat-long-container">
                <input type="text" id="customer_la_long" name="customer_la_long">
                <button type="button" class="btn-custom" onclick="openGoogleMaps()">Google Maps</button>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">แผนที่ :</label>
            <iframe id="mapFrame" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
        {{-- map --}}
        <script>
            function updateMap() {
                let coords = document.getElementById('customer_la_long').value;
                if (coords) {
                    document.getElementById('mapFrame').src = `https://www.google.com/maps?q=${coords}&output=embed`;
                }
            }
            document.getElementById('customer_la_long').addEventListener('input', updateMap);
            updateMap();
        </script>
            <label>วันกำหนดส่ง</label>
            <input type="date" id="revdate" name="revdate" >

            <label for="notes">หมายเหตุ</label>
            <textarea id="notes" name="notes" rows="4"></textarea>
                        

            <button type="button" id="submitBilldoc" class="btn btn-success" onclick="submitForm(event)">สร้างเอกสาร</button>


    </form>
</div>

    {{-- function --}}
    <script>
            function openGoogleMaps() {
                const mapWindow = window.open(
                    "https://www.google.com/maps/@13.7563,100.5018,14z",
                    "Google Maps",
                    "width=800,height=600"
                );
            }
    </script>


<script>
document.getElementById('submitBilldoc').addEventListener('click', async function (event) {
    event.preventDefault();

    let formData = new FormData(document.getElementById('billForm'));

    try {
        let response = await fetch('{{ route("insertdocu") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });

        if (!response.ok) {
            let errorText = await response.text();
            console.error('Server error:', errorText);
            alert('เกิดข้อผิดพลาดในการส่งข้อมูล: ' + errorText);
            return;
        }

        let data = await response.json();
        if (data.success) {
            alert(data.success);
            window.location.href = '/SOlist';
        } else {
            alert(data.error);
        }
    } catch (error) {
        console.error('Fetch error:', error);
        alert('มีข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์');
    }
});
</script>




    {{-- api --}}
    <script>
        document.getElementById("soSearchForm").addEventListener("submit", async function(event) {
            event.preventDefault();
            let soNumber = document.getElementById("so_number").value.trim();
            if (!soNumber) {
                alert("กรุณากรอกเลขที่ so");
                return;
            }
    
            try {
                let response = await fetch(`http://server_update:8000/api/getSODetail?SONum=${soNumber}`);
    
                if (!response.ok) {
                    throw new Error("เกิดข้อผิดพลาดในการโหลดข้อมูล");
                }
    
                let data = await response.json();
                console.log("API Response:", data); // ตรวจสอบข้อมูล API
    
                if (!data.SoDetail || data.SoDetail.length === 0) {
                    alert("ไม่พบข้อมูลที่ตรงกับเลขที่ SO นี้: " + soNumber);
                    return;
                }
    
                const soDetails = data.SoDetail;
                const SoStatus = data.SoStatus;

                document.getElementById('so_id').value = SoStatus.SONum;  
                document.getElementById('customer_name').value = soDetails.CustName;  
                document.getElementById('customer_address').value = soDetails.CustAddr1;  
                document.getElementById('customer_tel').value = soDetails.ContTel;  

                
    
            } catch (error) {
                console.error('Error fetching data:', error);
                alert('เกิดข้อผิดพลาดในการดึงข้อมูล');
            }
        });
    
        function formatDate(dateString) {
            let date = new Date(dateString);
            let day = date.getDate().toString().padStart(2, '0');
            let month = (date.getMonth() + 1).toString().padStart(2, '0');
            let year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }
    </script>
    
        
</body>
</html>




