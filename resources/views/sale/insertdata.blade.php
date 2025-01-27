<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดเตรียมสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .header {
            background-color: #e6e6e6;
            padding: 15px;
            margin-bottom: 20px;
        }
        .header img {
            width: 30px;
            vertical-align: middle;
        }
        .header a {
            font-size: 20px;
            margin-left: 10px;
            color: #333;
            text-decoration: none;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .btn {
            background-color: #e6a756;
            color: white;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #d89544;
        }
    </style>
</head>
<body>
    <div class="header d-flex align-items-center">
        <img src="gear-icon.png" alt="gear icon">
        <a href="{{ url('/dashboard') }}">ระบบเปิดบิล</a>
    </div>

    <div class="container">
        <form action="process.php" method="POST">
            <div class="text-center mb-4">
              <label>เปิดบิล</label>
            </div>
        <div class="mb-3">
                <label class="form-label">เลขที่PO:</label>
                <input type="text" class="form-control" name="po_number">
                <button type="submit" class="btn btn-warning">ค้นหา</button>
            </div>
            <div class="mb-3">
                <label class="form-label">รหัสลูกค้า:</label>
                <input type="text" class="form-control" name="customer_id">
            </div>

            <div class="mb-3">
                <label class="form-label">ชื่อบริษัท:</label>
                <input type="text" class="form-control" name="company_name">
            </div>

            <div class="mb-3">
                <label class="form-label">ที่อยู่จัดส่ง:</label>
                <input type="text" class="form-control" name="delivery_address">
            </div>

            <div class="mb-3">
                <label class="form-label">วันที่กำหนดส่ง:</label>
                <input type="text" class="form-control" name="delivery_date">
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>รหัสสินค้า</th>
                        <th>รายการ</th>
                        <th>จำนวน</th>
                        <th>ราคา/หน่วย</th>
                        <th>จำนวนเงิน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i = 0; $i < 7; $i++): ?>
                    <tr>
                        <td><input type="text" class="form-control" name="product_code[]" style="width: 90%;"></td>
                        <td><input type="text" class="form-control" name="product_name[]" style="width: 90%;"></td>
                        <td><input type="text" class="form-control" name="quantity[]" style="width: 90%;"></td>
                        <td><input type="text" class="form-control" name="price[]" style="width: 90%;"></td>
                        <td><input type="text" class="form-control" name="total[]" style="width: 90%;"></td>
                    </tr>
                    <?php endfor; ?>
                    
                </tbody>
                
            </table>
            <div class="mb-3">
                        <label class="form-label">เลขที่SO:</label>
                        <input type="text" class="form-control" name="so_number">
                    </div>

            

            <button type="submit" class="btn btn-warning w-100">เพิ่มข้อมูล</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
