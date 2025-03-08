<?php
// สามารถเพิ่มโค้ด PHP ที่ต้องการที่นี่
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOME</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .button-container {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="button-container">
        <label>ระบบSO</label>
        <a href="loginsale" class="btn btn-primary me-2">เปิดบิล</a>  
        <a href="dashboardadmin" class="btn btn-success m-2">จัดของ</a><br>
        <hr style="border: 10px solid #000000; width: 80%; margin: 20px auto;">
        <label>ระบบPO</label>
        <a href="dashboardpo" class="btn btn-success m-3" style="background-color: red">รับของ</a>
        <a href="adminpo" class="btn btn-success m-3" style="background-color: green">จัดรถ</a>
        <hr style="border: 10px solid #000000; width: 80%; margin: 20px auto;">
        <label>ระบบเอกสาร</label>
        <a href="dashboarddoc" class="btn btn-success m-3" style="background-color: red">เปิดเอกสาร</a>
        <a href="admindoc" class="btn btn-success m-3" style="background-color: green">จัดรถ</a>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
