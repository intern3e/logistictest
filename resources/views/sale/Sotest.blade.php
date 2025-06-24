<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ข้อมูลจัดส่ง</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f2f4f7;
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

    .filter-container, .search-box {
      padding: 15px 20px;
      background-color: #ffffff;
      border-bottom: 1px solid #dcdcdc;
    }

    .search-box {
      background-color: #f0f2f5;
    }

    #search-input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }

    .table-container {
      padding: 20px;
      overflow-x: auto;
    }

    h3 {
      background-color: #2d3e50;
      color: white;
      padding: 10px 15px;
      margin: 20px 0 10px;
      border-radius: 6px;
      font-size: 18px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
      background-color: white;
      border: 1px solid #ccc;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    th {
      color: rgb(0, 0, 0);
      padding: 12px;
      text-align: left;
    }

    td {
      padding: 12px;
      border-bottom: 1px solid #eee;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .latlong {
      color: #555;
      font-size: 13px;
    }
  </style>
</head>
<form id="csrfForm">@csrf</form>
<body>
<div class="header">
  <h2>ข้อมูลจัดส่ง</h2>
  <div class="buttons">
    <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>
    @csrf
    <a href="WorkSchedule" class="btn btn-danger">ตารางงาน</a>
    <a href="SOlist" class="btn btn-danger">🚪 หน้าหลัก</a>
  </div>
</div>

<div class="filter-container" style="display: flex; align-items: center; gap: 15px;">
  <form method="GET" action="{{ url()->current() }}" id="dateFilterForm" style="display: flex; align-items: center; gap: 10px;">
    <label for="filter_date">📅 เลือกวันที่:</label>
    <input type="date" id="filter_date" name="filter_date"
      value="{{ request('filter_date') ?: date('Y-m-d') }}"
      onchange="document.getElementById('dateFilterForm').submit();">
  </form>

<!-- ปุ่มดาวน์โหลด + สถานะ -->
<div style="display: flex; align-items: center; gap: 15px;">
  <button onclick="downloadJSON()" id="downloadBtn"
    style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 6px; font-weight: bold;">
    📥 ดาวน์โหลดข้อมูล JSON
  </button>
  <span id="statusMessage" style="font-weight: bold; color: #333;"></span>
</div>

</div>

<script>
// ✅ ตั้งค่าวันที่เป็นวันปัจจุบันเมื่อโหลดหน้าเว็บ (ถ้าไม่มี parameter filter_date)
document.addEventListener('DOMContentLoaded', function() {
  const filterDateInput = document.getElementById('filter_date');
  const urlParams = new URLSearchParams(window.location.search);
  
  // ถ้าไม่มี parameter filter_date ใน URL ให้ submit form อัตโนมัติ
  if (!urlParams.has('filter_date')) {
    const today = new Date().toISOString().split('T')[0];
    filterDateInput.value = today;
    document.getElementById('dateFilterForm').submit();
  }
});
function downloadJSON() {
  const btn = document.getElementById('downloadBtn');
  const statusEl = document.getElementById('statusMessage');

  statusEl.textContent = '⏳ กำลังส่งข้อมูล...';
  statusEl.style.color = '#ffc107';

  const zoneData = {};
  const zoneBlocks = document.querySelectorAll('h3');

  zoneBlocks.forEach((zoneHeader) => {
    const zoneName = zoneHeader.textContent.trim();
    const table = zoneHeader.nextElementSibling;
    const rows = table.querySelectorAll('tbody tr');

    const zoneItems = [];

    rows.forEach(row => {
      const td = row.querySelector('td');
      if (!td || td.innerText.includes("ไม่มีข้อมูล")) return;

      const lines = td.innerText.trim().split('\n').map(l => l.trim()).filter(Boolean);

      // ข้ามบรรทัด "งานที่: ..."
      const contentLines = lines.filter(line => !line.startsWith("งานที่:"));

      const so_id        = contentLines[0] || '';
      const datetime     = contentLines[1] || '';
      const name         = contentLines[2] || '';
      const customer_tel = contentLines[3] || '';
      let address        = contentLines[4] || '';
      const latlongLine  = contentLines.find(line => line.includes('📍 พิกัด:')) || '';
      const distanceLine = contentLines.find(line => line.includes('📏 ระยะทาง:')) || '';
      const latlong      = latlongLine.replace(/📍\s*พิกัด:\s*/g, '').trim();

        // ลบแค่ "📍 พิกัด:" ออกจาก address (ถ้ามี) โดยเก็บพิกัดไว้
      if (address.includes('📍 พิกัด:')) {
        address = address.replace(/📍\s*พิกัด:/, '').trim();
      }


      // แยก latitude กับ longitude จาก string พิกัด
      let latitude = '';
      let longitude = '';
      if (latlong.includes(',')) {
        const parts = latlong.split(',').map(p => p.trim());
        latitude = parts[0] || '';
        longitude = parts[1] || '';
      }

      zoneItems.push({
        so_id: so_id,
        time: datetime,
        customer_name: name,
        customer_tel: customer_tel,
        customer_address: address,
        coordinates: latlong,
        latitude: latitude,
        longitude: longitude,
        distance: distanceLine.replace(/📏\s*ระยะทาง:\s*/g, '').trim()
      });
    });

    zoneData[zoneName] = zoneItems;
  });

  // ✅ ส่งไปที่ Laravel route /send-to-sheet
  fetch('/send-to-sheet', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
    },
    body: JSON.stringify(zoneData)
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      statusEl.textContent = '✅ ส่งข้อมูลสำเร็จ!';
      statusEl.style.color = '#28a745';
    } else {
      statusEl.textContent = '❌ ล้มเหลว: ' + data.message;
      statusEl.style.color = '#dc3545';
    }
  })
  .catch(error => {
    statusEl.textContent = '❌ ส่งข้อมูลล้มเหลว!';
    statusEl.style.color = '#dc3545';
    console.error(error);
  });
}

</script>

<div class="table-container">
@php
use Carbon\Carbon;

function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // กิโลเมตร
    $latFrom = deg2rad($lat1);
    $lonFrom = deg2rad($lon1);
    $latTo = deg2rad($lat2);
    $lonTo = deg2rad($lon2);
    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $a = sin($latDelta / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}

// ✅ จุดเริ่มต้น
$startLat = 13.717683;
$startLong = 100.4732644;

$zoneAPolygon = [
    [13.096866694533679, 101.26775485422567],
    [13.021710224615083, 101.04269643843428],
    [13.023783741783353, 100.82088836514302],
    [13.27302261388081, 100.86011508026462],
    [13.447002292322688, 100.86798408835267],
    [13.47319712375085, 101.1011754551715],
    [13.496151731088403, 101.27818936995317]
];

$zoneBPolygon = [
    [12.798893670022817, 101.03070268760943],
    [12.946433141125997, 101.04376636488216],
    [13.094329099162358, 101.05652450650597],
    [13.094282365823148, 101.2847204488829],
    [12.972382572554917, 101.3257158592489],
    [12.850104894894153, 101.36664189428281]
];

$zoneCPolygon = [
    [13.355394524173379, 99.919167477935],
    [13.707113289735208, 99.89454148571286],
    [14.041969739147719, 99.93984548663637],
    [14.087183654643262, 100.0217867326325],
    [14.121855638836463, 100.08520385076991],
    [14.205207795100936, 100.23694375973452],
    [14.055981420004677, 100.25284759451608],
    [13.941281542384683, 100.26907977291638],
    [13.929310924570444, 100.27078801601093],
    [13.904273259744796, 100.31944866715641],
    [13.870910626211218, 100.38247952720239],
    [13.81198239540519, 100.49559153275877],
    [13.768631613967866, 100.4987573482835],
    [13.696844305872597, 100.50411226098345],
    [13.670145027198616, 100.50597952457441],
    [13.581906654669524, 100.51223381907708],
    [13.508657185528403, 100.32218265014293],
    [13.479710676041986, 100.09052308237426],
    [13.636535153581917, 100.03208334775479],
    [13.682840154450504, 100.06491085684762],
    [13.717820115488616, 100.1972798451252],
    [13.749194763512383, 100.30211608384103]
];

$zoneDPolygon = [
    [13.810916732940521, 100.88197438902678],
    [13.816045401249864, 100.49256596662198],
    [13.933314307659607, 100.27107089815529],
    [14.3399036515983, 100.23714808378625],
    [14.716908380150352, 100.20586580145978],
    [14.771425196855976, 100.67115827732961],
    [14.813240246213219, 101.04568489026524],
    [14.326229548695629, 101.00854134715885]
];

$zoneEPolygon = [
    [13.493616521835158, 100.8242801525344],
    [13.51219344799453, 100.71821058863118],
    [13.537859316422448, 100.61124648692564],
    [13.590588394220532, 100.6492960402475],
    [13.649983483261925, 100.69121185480816],
    [13.606408181994201, 100.87376014417984]
];

$zoneFPolygon = [
  [13.49295754606871, 100.82856008153507],
  [13.608213654070918, 100.8758630796703],
  [13.632320524640807, 100.76904444767723],
  [13.650117416433583, 100.69099415749356],
  [13.593789738148056, 100.65117115919189],
  [13.536818106921862, 100.60881313003387],
  [13.537636609788946, 100.526433245617],
  [13.623126290869333, 100.51442728851738],
  [13.672355912212867, 100.50763436004434],
  [13.678295342270166, 100.63007056846502],
  [13.685679600765358, 100.7876519610175],
  [13.699079529731494, 101.07539148460486],
  [13.574837542457214, 101.07989563932854],
  [13.481596445584746, 101.08303306550918],
  [13.470365206308633, 100.94721989919763]
];

$zoneGPolygon = [
    [13.68853096266238, 100.8036919217823],
    [13.81086091968321, 100.83138033062738],
    [13.812159635512833, 100.66496037833747],
    [13.813250781871972, 100.49798826698886],
    [13.674901026070808, 100.50715414236767],
    [13.681827244874428, 100.65569774314002]
];

$zoneHPolygon = [
    [12.653902469989623, 101.02400744070962],
    [12.780881083683132, 101.01572967934067],
    [12.813728583446014, 101.13974885931125],
    [12.784662177783725, 101.35265882742893],
    [12.633207756418546, 101.34215980719533],
    [12.608060090136046, 101.15413188628654]
];

// ✅ ฟังก์ชันตรวจสอบจุดใน Polygon
function pointInPolygon($point, $polygon) {
    $x = $point[0]; $y = $point[1]; $inside = false;
    for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
        $xi = $polygon[$i][0]; $yi = $polygon[$i][1];
        $xj = $polygon[$j][0]; $yj = $polygon[$j][1];
        $intersect = (($yi > $y) != ($yj > $y)) &&
            ($x < ($xj - $xi) * ($y - $yi) / (($yj - $yi) ?: 0.000001) + $xi);
        if ($intersect) $inside = !$inside;
    }
    return $inside;
}

// ✅ กรองตามวันที่ - ใช้วันที่ปัจจุบันถ้าไม่มีการเลือกวันที่
$selectedDate = request('filter_date') ?: date('Y-m-d');
$filteredBills = collect($bill)->filter(function($item) use ($selectedDate) {
    return Carbon::parse($item->date_of_dali)->toDateString() === $selectedDate;
});

// ✅ แบ่งกลุ่มตามโซน
$grouped = [
    'Zone A กอล์ฟ(มาบเอียง)' => [],
    'Zone B บังเดช(ชลบุรี)' => [],
    'Zone C ยุทร(พระราม 2)' => [],
    'Zone D หรั่ง(รังสิต,อยุธยา)' => [],
    'Zone E เอ(บางนาตราด กม 11)' => [],
    'Zone F แฟรงค์(บางนาตราด กม 13)' => [],
    'Zone G เเชม(กรุงเทพปริมณฑล)' => [],
    'Zone H (เก่ง)' => [],
    'เก่ง อื่น ๆ' => []
];

foreach ($filteredBills as $item) {
    if (!empty($item->customer_la_long) && str_contains($item->customer_la_long, ',')) {
        [$lat, $long] = explode(',', $item->customer_la_long);
        $lat = floatval(trim($lat));
        $long = floatval(trim($long));
        $point = [$lat, $long];

        // ✅ จัดลำดับ zone แบบแม่นยำ: เล็ก → ใหญ่
        if (pointInPolygon($point, $zoneBPolygon)) {
            $grouped['Zone B บังเดช(ชลบุรี)'][] = $item;
        } elseif (pointInPolygon($point, $zoneFPolygon)) {
            $grouped['Zone F แฟรงค์(บางนาตราด กม 13)'][] = $item;
        } elseif (pointInPolygon($point, $zoneEPolygon)) {
            $grouped['Zone E เอ(บางนาตราด กม 11)'][] = $item;
        } elseif (pointInPolygon($point, $zoneGPolygon)) {
            $grouped['Zone G เเชม(กรุงเทพปริมณฑล)'][] = $item;
        } elseif (pointInPolygon($point, $zoneCPolygon)) {
            $grouped['Zone C ยุทร(พระราม 2)'][] = $item;
        } elseif (pointInPolygon($point, $zoneDPolygon)) {
            $grouped['Zone D หรั่ง(รังสิต,อยุธยา)'][] = $item;
        } elseif (pointInPolygon($point, $zoneHPolygon)) {
            $grouped['Zone H (เก่ง)'][] = $item;
        } elseif (pointInPolygon($point, $zoneAPolygon)) {
            $grouped['Zone A กอล์ฟ(มาบเอียง)'][] = $item;
        } else {
            $grouped['เก่ง อื่น ๆ'][] = $item;
        }
    } else {
        $grouped['เก่ง อื่น ๆ'][] = $item;
    }
}
@endphp
</div>

<!-- ✅ ตารางแสดงผลข้อมูลลูกค้า -->
<div style="display: flex; flex-wrap: wrap; gap: 20px; padding: 20px;">
  @foreach($grouped as $zoneName => $items)
  <div style="flex: 1; min-width: 500px; background-color: #fff; border: 2px solid #999; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1); padding: 15px;">
    
  @php $zoneIndex = $loop->index + 1; @endphp
    <h3 style="margin-top: 0;">
      โซนที่ {{ $zoneIndex }}: {{ $zoneName }} — จำนวนงาน: {{ count($items) }}
    </h3>

    <table>
      <thead>
        <tr>
          <th>ข้อมูลลูกค้า</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $index => $item)
        @php
          $distanceText = 'ไม่ทราบระยะทาง';
          if (!empty($item->customer_la_long) && str_contains($item->customer_la_long, ',')) {
              [$custLat, $custLong] = explode(',', $item->customer_la_long);
              $custLat = floatval(trim($custLat));
              $custLong = floatval(trim($custLong));
              $distance = calculateDistance($startLat, $startLong, $custLat, $custLong);
              $distanceText = number_format($distance, 2) . ' กม. จากจุดเริ่มต้น';
          }
        @endphp

        <tr>
          <td>
            <div style="border: 2px solid #999; border-radius: 6px; padding: 10px;">
              <strong>งานที่: {{ $index + 1 }}</strong><br>
              {{ $item->so_id }} {{ $item->date_of_dali }}<br>
              {{ $item->customer_name }}<br>
              {{ $item->customer_tel }}<br>
              {{ $item->customer_address }}<br>
              <a class="latlong" style="color:#007bff; text-decoration:underline;" href="https://www.google.com/maps?q={{ trim($item->customer_la_long) }}" target="_blank">
                📍 พิกัด: {{ $item->customer_la_long }}
              </a><br>
              <span class="latlong" style="color: #28a745; font-weight: bold;">📏 ระยะทาง: {{ $distanceText }}</span>
            </div>
          </td>
        </tr>
        @empty
        <tr><td>ไม่มีข้อมูล</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @endforeach
</div>

</body>
</html>