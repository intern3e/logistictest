<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* --- Global Style --- */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #f0f2f5, #dfe9f3);
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
            background: white;
            margin: 40px 5%;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        th, td {
            padding: 12px;
            border: 1px solid #2c3e50;
            font-size: 1rem;
        }

        th {
            background: linear-gradient(to right, #2c3e50, #4b6584);
            color: white;
            text-transform: uppercase;
        }

        tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #e1e5ea;
            transition: 0.2s;
        }

        /* --- Link Style --- */
        td a {
            color: #27ae60;
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
            background: #27ae60;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .filter-form button:hover {
            background: #2980b9;
        }

        .search-box {
            flex-grow: 1;
            max-width: 200px;

        }

        .search-box input {
            width: 90%;
            height: 30px;
            margin: 0px -30%;
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
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: bold;
        }

        .search-box button:hover {
            background: #27ae60;
            transform: scale(1.05);
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
    </style>
</head>
<body>

<div class="header">
    <h4>📑 ระบบเปิดบิล</h4>
    <div class="buttons">
        <span>👤 ผู้ใช้: {{ session('so_number', 'Guest') }}</span>

        <a href="{{ route('sale.insertdata') }}" class="btn btn-warning">➕ เพิ่มข้อมูล</a>
        
            @csrf
            <a href="{{ route('home') }}" button  type="submit" class="btn btn-danger">🚪 หน้าหลัก</a>
    </div>
</div>

<!-- Filter & Search Section -->
<div class="filter-container">
    <form method="GET" action="{{ route('sale.dashboard') }}" class="filter-form">
        <label for="date">📅 วันที่:</label>
        <input type="date" id="date" name="date" value="{{ request('date') }}">
    </form>

    <div class="search-box">
        <input type="text" placeholder="🔍 ค้นหา ID SO detail">
        <button type="submit">🔍 ค้นหา</button>
    </div>

</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID SO Detail</th>
                <th>รหัสลูกค้า</th>
                <th>ที่อยู่จัดส่ง</th>
                <th>วันที่จัดส่ง</th>
                <th>ข้อมูลสินค้า</th>
            </tr>
        </thead>
        <tbody id="table-body">
            <!-- Content will be inserted dynamically here -->
        </tbody>
    </table>
</div>

<script>
    function generateRows() {
        let tbody = document.getElementById("table-body");
        let content = "";
        
        for (let i = 0; i < 60; i++) {
            content += `
            <tr>
                <td>1123456</td>
                <td>ณฏ12345</td>
                <td>34/4 หมู่2 ต.บางน้ำจืด อ.เมือง จ.สมุทรสาคร</td>
                <td>29/1/2567</td>
                <td><a href="txt" onclick="popup('txt'); return false;">📄 เพิ่มเติม</a></td>
            </tr>`;
        }
        
        
        tbody.innerHTML = content;
    }
    
    function popup(url) {
    let width = 900;
    let height = 600;
    let left = (screen.width - width) / 2;
    let top = (screen.height - height) / 2;
    
    window.open(url, 'popupWindow', `width=${width},height=${height},left=${left},top=${top},scrollbars=yes,resizable=no,menubar=no,toolbar=no,location=no,status=no`);
}



    // เรียกใช้ฟังก์ชันเพื่อเพิ่มแถวในตาราง
    generateRows();
</script>

<!-- Popup -->
<div class="popup-overlay" id="popup">
    <div class="popup-content">
        <span class="close-btn" onclick="closePopup()">&times;</span>
        <h2>Popup</h2>
        <p>นี่คือเนื้อหาใน Popup</p>
    </div>
</div>

</body>
</html>
