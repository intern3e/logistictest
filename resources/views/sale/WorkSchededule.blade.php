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
      border-collapse: collapse;
      background: white;
      border-radius: 6px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgb(0 0 0 / 0.1);
      border: 1px solid #ccc;
    }

    .work-table th, .work-table td {
      padding: 12px 15px;
      text-align: center;
      font-size: 16px;
      font-weight: 600;
      border: 1px solid #ccc;
      vertical-align: middle;
      color: #333;
    }

    .work-table thead tr th {
      color: white;
      user-select: none;
    }

    thead tr th:nth-child(1) { background-color: #2d3e50; }
    thead tr th:nth-child(2) { background-color: #ffeb3b; color: black; }
    thead tr th:nth-child(3) { background-color: #f48fb1; color: black; }
    thead tr th:nth-child(4) { background-color: #66bb6a; color: black; }
    thead tr th:nth-child(5) { background-color: #ff9800; color: black; }
    thead tr th:nth-child(6) { background-color: #4fc3f7; color: black; }
    thead tr th:nth-child(7) { background-color: #ab47bc; color: black; }

    .work-table tbody tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .work-table tbody tr:hover {
      background-color: #d0e8ff;
      cursor: default;
      transition: background-color 0.3s ease;
    }

    .map-link {
      color: #007bff;
      cursor: pointer;
      text-decoration: underline;
      user-select: none;
    }

    /* Popup modal */
    .map-modal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(0,0,0,0.5);
      z-index: 9999;
      justify-content: center;
      align-items: center;
    }

    .map-modal-content {
      position: relative;
      background: white;
      border-radius: 8px;
      overflow: hidden;
      width: 90%;
      max-width: 700px;
      height: 500px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .map-modal iframe {
      width: 100%;
      height: 100%;
      border: none;
    }

    .map-popup {
  position: fixed; /* หรือ absolute */
  top: 100px;
  left: 100px;
  width: 640px;
  height: 480px;
  border: 1px solid #ccc;
  background: white;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  z-index: 999;
}

/* ปุ่มปิด */
.close-btn {
  position: absolute;
  top: 8px;
  right: -100px; /* ✅ ชิดมุมขวาของกรอบ */
  font-size: 22px;
  font-weight: bold;
  color: #fff;
  background: #f44336;
  border: none;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  cursor: pointer;
  z-index: 1000;
  line-height: 26px;
  text-align: center;
  padding: 0;
  user-select: none;
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
      <tr>
        <td class="map-link" data-map="https://www.google.com/maps/d/u/0/embed?mid=14TnvKVU5fGiSl5aRH72Hurq9uCD0-zA&ehbc=2E312F">กอล์ฟ</td>
        <td>มาบเอียง-บ่อวิน</td>
        <td>มาบเอียง-บ่อวิน</td>
        <td>มาบเอียง-บ่อวิน</td>
        <td>มาบเอียง-บ่อวิน</td>
        <td>มาบเอียง-บ่อวิน</td>
        <td>Thai Food,N/A</td>
      </tr>
      <tr>
        <td class="map-link" data-map="https://www.google.com/maps/d/u/0/embed?mid=1SkIOpaTlIXgSvtD5mX4dc3jl-A8GwR4&ehbc=2E312F">บังเดช</td>
        <td>แหลมฉบัง-ศรีราชา</td>
        <td>อมตะ-บ้านบึง</td>
        <td>แหลมฉบัง-ศรีราชา</td>
        <td>อมตะ-บ้านบึง</td>
        <td>แหลมฉบัง-ศรีราชา</td>
        <td>Thai Food,N/A</td>
      </tr>
      <tr>
        <td class="map-link" data-map="https://www.google.com/maps/d/u/0/embed?mid=1dMI3VbHJjx3UzPI000tPtrJncpnQ5GM&ehbc=2E312F">เอ</td>
        <td>บางนาตราด-กม.11</td>
        <td>บางนาตราด-กม.11</td>
        <td>บางนาตราด-กม.11</td>
        <td>บางนาตราด-กม.11</td>
        <td>บางนาตราด-กม.11</td>
        <td>บางนาตราด-กม.11</td>
      </tr>
      <tr>
        <td class="map-link" data-map="https://www.google.com/maps/d/u/0/embed?mid=1-2BPB4rKs0UmDnT5lGukhSMa0h-lsRo&ehbc=2E312F">ยุทธ</td>
        <td>พระราม2</td>
        <td>พระราม2-เพชรเกษม</td>
        <td>เพชรบุรี-พระราม2</td>
        <td>พระราม2</td>
        <td>นครปฐม</td>
        <td>เพชรเกษม</td>
      </tr>
      <tr>
        <td class="map-link" data-map="https://www.google.com/maps/d/u/0/embed?mid=1-_NZZGuCQh2zF3s1Qrlbab2kVl6rmls&ehbc=2E312F">หรั่ง</td>
        <td>อยุธยา-รังสิต</td>
        <td>อยุธยา-รังสิต</td>
        <td>อยุธยา-รังสิต</td>
        <td>สระบุรี-ลพบุรี</td>
        <td>อยุธยา-รังสิต</td>
        <td>อยุธยา-รังสิต</td>
      </tr>
      <tr>
        <td class="map-link" data-map="https://www.google.com/maps/d/u/0/embed?mid=1ZU0J90ctibU921rNPl0fj8EsnTO6GAQ&ehbc=2E312F">แฟรงค์</td>
        <td>ฉะเชิงเทรา</td>
        <td>บางนาตราด กม.13-กม.28</td>
        <td>ฉะเชิงเทรา</td>
        <td>บางนาตราด กม.13-กม.28</td>
        <td>ฉะเชิงเทรา</td>
        <td>ขนส่ง+ใกล้เยาวราช</td>
      </tr>
      <tr>
        <td class="map-link" data-map="https://www.google.com/maps/d/u/0/embed?mid=1_XhwfSzo-oHJmaEletXrnhLu6ny-9Kw&ehbc=2E312F">เก่ง</td>
        <td>ปราจีนบุรี</td>
        <td>Thai Food</td>
        <td>มาบตาพุด-ระยอง</td>
        <td>กรมศุล</td>
        <td>มาบตาพุด-ระยอง</td>
        <td>Thai Food,N/A</td>
      </tr>
      <tr>
        <td class="map-link" data-map="https://www.google.com/maps/d/u/0/embed?mid=1k_wPYWXm6zSEAgVsHUkR41jPiUf2dKA&ehbc=2E312F">แซม</td>
        <td>ลาดกระบัง</td>
        <td>ในเมือง</td>
        <td>ลาดกระบัง</td>
        <td>ในเมือง</td>
        <td>ลาดกระบัง</td>
        <td>ในเมือง,ลาดกระบัง</td>
      </tr>
    </tbody>
  </table>

  <!-- Modal Popup -->
  <div id="mapModal" class="map-modal" role="dialog" aria-modal="true" aria-labelledby="mapModalTitle">
    <div class="map-modal-content">
      <button class="close-btn" aria-label="ปิดแผนที่" onclick="closeModal()">×</button>
      <iframe id="mapFrame" src="" title="แผนที่พนักงาน"></iframe>
    </div>
  </div>

  <script>
    const mapModal = document.getElementById('mapModal');
    const mapFrame = document.getElementById('mapFrame');

    document.querySelectorAll('.map-link').forEach(el => {
      el.addEventListener('click', () => {
        const url = el.getAttribute('data-map');
        mapFrame.src = url;
        mapModal.style.display = 'flex';
      });
    });

    function closeModal() {
      mapModal.style.display = 'none';
      mapFrame.src = '';
    }

    // ปิด modal ถ้าคลิกด้านนอก iframe
    mapModal.addEventListener('click', (e) => {
      if (e.target === mapModal) {
        closeModal();
      }
    });

    // ปิด modal ด้วยปุ่ม ESC
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && mapModal.style.display === 'flex') {
        closeModal();
      }
    });
  </script>

</body>
</html>
