<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        <form action="process.php" method="POST">
            <div class="text-center mb-4">
                <h3 class="text-dark">🔹 เปิดบิลสินค้า 🔹</h3>
            </div>

            <!-- เลขที่ SO -->
            <div class="mb-3">
                <label class="form-label">เลขที่ SO:</label>
                <div style="display: flex; justify-content: space-between;">
                    <input type="text" class="form-control" name="po_number" style="width: 80%;">
                    <button type="submit" class="btn-custom" style="width: 14%;height: 45px;">🔍 ค้นหา</button>
                </div>
            </div>

            <!-- รหัสลูกค้า -->
            <div class="mb-3">
                <label class="form-label">รหัสลูกค้า:</label>
                <input type="text" class="form-control" name="customer_id">
            </div>

            <!-- ชื่อบริษัท -->
            <div class="mb-3">
                <label class="form-label">ชื่อบริษัท:</label>
                <input type="text" class="form-control" name="company_name">
            </div>

            <!-- เบอร์ติดต่อ -->
            <div class="mb-3">
                <label class="form-label">เบอร์ติดต่อ:</label>
                <input type="text" class="form-control" name="contact_number">
            </div>

            <!-- ที่อยู่จัดส่ง -->
            <div class="mb-3">
                <label class="form-label">ที่อยู่จัดส่ง:</label>
                <input type="text" class="form-control" name="delivery_address">
            </div>

            <!-- ละติจูด ลองจิจูด -->
            <div class="mb-3">
                <label class="form-label">ละติจูด ลองจิจูด:</label>
                <input type="text" class="form-control" name="location_coordinates">
            </div>

            <!-- วันที่กำหนดส่ง -->
            <div class="mb-3">
                <label class="form-label">วันที่กำหนดส่ง:</label>
                <input type="date" class="form-control" name="delivery_date">
            </div>


            <!-- ตารางสินค้า -->
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>รหัสสินค้า</th>
                        <th>รายการ</th>
                        <th>จำนวน</th>
                        <th>ราคา/หน่วย</th>
                        <th>จำนวนเงิน</th>
                        <th>ลบ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i = 0; $i < 7; $i++): ?>
                    <tr>
                        <td><input type="text" class="form-control1" name="product_code[]"></td>
                        <td><input type="text" class="form-control1" name="product_name[]"></td>
                        <td><input type="number" class="form-control1" name="quantity[]"></td>
                        <td><input type="number" class="form-control1" name="price[]"></td>
                        <td><input type="number" class="form-control1" name="total[]"></td>
                        <td><button type="button" class="btn btn-danger delete-btn">ลบ</button></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
            
            <script>
                // Listen for the delete button click event
                document.querySelectorAll('.delete-btn').forEach(function(button) {
                    button.addEventListener('click', function() {
                        var row = button.closest('tr');
                        row.remove();
                    });
                });
            </script>

            <!-- ช่องข้อความเพิ่มเติม -->
             <br>
            <div class="mb-3">
                <label class="form-label">เเจ้งเพิ่มเติม:</label>
                <textarea class="form-control" name="additional_notes" rows="4"></textarea>
            </div>
            <br>

            <!-- ปุ่มเพิ่มข้อมูล -->
            <button type="submit" class="btn-custom">💎 เปิดบิล</button>
        </form>
    </div>
</body>
</html>
