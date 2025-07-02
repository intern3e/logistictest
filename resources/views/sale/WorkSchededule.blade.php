<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ตารางงานสีประจำวัน</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f9fafb;
      margin: 0;
      padding: 0;
    }

    .header {
      background-color: #2d3e50;
      color: white;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      width: 100%;
      box-sizing: border-box;
    }

    .header h2 {
      margin: 0;
      font-size: 24px;
    }

    .buttons {
      display: flex;
      gap: 15px;
      align-items: center;
    }

    .buttons a {
      color: black;
      text-decoration: none;
      font-weight: bold;
      padding: 8px 14px;
      border-radius: 6px;
      background-color: #ffc107;
      transition: background-color 0.2s ease;
    }

    .buttons a:hover {
      background-color: #e0a800;
    }

   .work-table {
  width: 100%;
  max-width: 1260px;
  margin: 50px auto;
  border-collapse: collapse;  /* สำคัญมากสำหรับเส้นติดกัน */
  background: white;
  border-radius: 6px;
  overflow: hidden;
  box-shadow: 0 3px 10px rgb(0 0 0 / 0.1);
  box-sizing: border-box;
  border: 1px solid #ccc; /* ขอบนอกตาราง */
}

.work-table thead tr th,
.work-table tbody tr td {
  padding: 12px 15px;
  text-align: center;
  font-size: 16px;
  font-weight: 600;
  border: 1px solid #ccc;  /* เส้นขอบช่อง */
  vertical-align: middle;
  color: #333;
}

.work-table thead tr th {
  color: white;
  user-select: none;
}

/* สีหัวตารางตามสีประจำวัน */
thead tr th:nth-child(1) {
  background-color: #2d3e50; /* ชื่อ - สีเทาเข้ม */
}
thead tr th:nth-child(2) { background-color: #ffeb3b; color: black; } /* วันอาทิตย์ - เหลือง */
thead tr th:nth-child(3) { background-color: #f48fb1; color: black; } /* วันจันทร์ - ชมพู */
thead tr th:nth-child(4) { background-color: #66bb6a;  color: black;} /* วันอังคาร - เขียว */
thead tr th:nth-child(5) { background-color: #ff9800; color: black; } /* วันพุธ - ส้ม */
thead tr th:nth-child(6) { background-color: #4fc3f7; color: black; } /* วันพฤหัส - ฟ้า */
thead tr th:nth-child(7) { background-color: #ab47bc; color: black; } /* วันศุกร์ - ม่วง */

/* สีวันเสาร์ถ้าต้องการเพิ่ม */
thead tr th:nth-child(8) {
  background-color: #6a1b9a;
  color: white;
}

.work-table tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}

.work-table tbody tr:hover {
  background-color: #d0e8ff;
  cursor: default;
  transition: background-color 0.3s ease;
}

  </style>
</head>
<body>

  <div class="header">
    <h2>ตารางงาน</h2>
    <div class="buttons">
      <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>
      <a href="Sotest" class="btn btn-danger">🚪 ข้อมูลจัดส่ง</a>
    </div>
  </div>

  <table class="work-table">
    <thead>
      <tr>
        <th>ชื่อ</th>
        <th>จันทร์</th>
        <th>อังคาร</th>
        <th>พุธ</th>
        <th>พฤหัสบดี</th>
        <th>ศุกร์</th>
        <th>เสาร์</th>
      </tr>
    </thead>
    <tbody>
      <!-- ตัวอย่างแถวเปล่าไว้ใส่ข้อมูล -->
      <tr>
        <td>กอล์ฟ</td>
        <td>มาบเอียง-บ่อวิน</td>
        <td>มาบเอียง-บ่อวิน</td>
       <td>มาบเอียง-บ่อวิน</td>
       <td>มาบเอียง-บ่อวิน</td>
       <td>มาบเอียง-บ่อวิน</td>
        <td>Thai Food,N/A</td>
      </tr>
      <tr>
        <td>บังเดช</td>
        <td>เเหลมฉบัง-ศรีราชา</td>
        <td>อมตะ-บ้านบึง</td>
        <td>เเหลมฉบัง-ศรีราชา</td>
        <td>อมตะ-บ้านบึง</td>
        <td>เเหลมฉบัง-ศรีราชา</td>
       <td>Thai Food,N/A</td>
      </tr>
      <tr>
        <td>เอ</td>
        <td>บางนาตราด-กม.11</td>
        <td>บางนาตราด-กม.11</td>
        <td>บางนาตราด-กม.11</td>
        <td>บางนาตราด-กม.11</td>
        <td>บางนาตราด-กม.11</td>
        <td>บางนาตราด-กม.11</td>
      </tr>
       <tr>
        <td>ยุทธ</td>
        <td>พระราม2</td>
        <td>พระราม2-เพชรเกรษม</td>
        <td>เพรชบุรี-พระราม2</td>
        <td>พระราม2</td>
        <td>นครปฐม</td>
        <td>เพชรเกรษม</td>
      </tr>
       <tr>
        <td>หรั่ง</td>
        <td>อยุธยา-รังสิต</td>
        <td>อยุธยา-รังสิต</td>
        <td>อยุธยา-รังสิต</td>
        <td>สระบุรี-ลพบุรี</td>
        <td>อยุธยา-รังสิต</td>
        <td>อยุธยา-รังสิต</td>
      </tr>
      <tr>
        <td>แฟรงค์</td>
        <td>ฉะเชิงเทรา</td>
        <td>บางนาตราด กม.13-กม.28</td>
        <td>ฉะเชิงเทรา</td>
        <td>บางนาตราด กม.13-กม.28</td>
        <td>ฉะเชิงเทรา</td>
        <td>ขนส่ง+ใกล้เยาวราช </td>
      </tr>
      <tr>
        <td>เก่ง</td>
        <td>มาบตาพุด-ระยอง</td>
        <td>Thai Food</td>
        <td>มาบตาพุด-ระยอง</td>
        <td>กรมสุล</td>
        <td>มาบตาพุด-ระยอง</td>
        <td>Thai Food,N/A</td>
      </tr>
      <tr>
        <td>เเซม</td>
        <td>ลาดกะบัง</td>
        <td>ในเมือง</td>
        <td>ลาดกะบัง</td>
        <td>ในเมือง</td>
        <td>ลาดกะบัง</td>
        <td>ในเมือง,ลาดกะบัง</td>
      </tr>
      <!-- เพิ่มแถวได้ตามต้องการ -->
    </tbody>
  </table>

</body>
</html>
