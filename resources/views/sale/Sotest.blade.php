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
<body>
<div class="header">
  <h2>ข้อมูลจัดส่ง</h2>
  <div class="buttons">
    <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>
    @csrf
    <a href="SOlist" class="btn btn-danger">🚪 หน้าหลัก</a>
  </div>
</div>

<!-- ✅ ฟอร์มกรองวันที่ -->
<div class="filter-container">
  <form method="GET" action="{{ url()->current() }}" id="dateFilterForm">
    <label for="filter_date">📅 เลือกวันที่:</label>
    <input type="date" id="filter_date" name="filter_date" value="{{ request('filter_date') }}" onchange="document.getElementById('dateFilterForm').submit();">
  </form>
</div>

<!-- 🔍 กล่องค้นหาข้อความ -->
<div class="search-box">
  <input type="text" id="search-input" placeholder="ค้นหา ชื่อลูกค้า / ที่อยู่ / พิกัด" onkeyup="searchTable()">
</div>

<div class="table-container">
@php
use Carbon\Carbon;

// ✅ ฟังก์ชันคำนวณระยะทาง Haversine
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

// ✅ พิกัดแต่ละโซน
$zoneAPolygon = [[13.09824143481619, 100.84791113847695], [13.10811217787513, 101.18651778255656], [13.344760830796636, 101.28070372457205], [13.447748580498407, 101.03210018027217], [13.12399906219143, 100.95450518211493]];
$zoneBPolygon = [[13.089028536413522, 101.2088399207378], [12.986216036841897, 101.25203928553853], [12.681584095570864, 101.28166901094463], [13.07247100159725, 101.0855082937613], [13.069937703530751, 101.18134858026723]];
$zoneCPolygon = [
  [13.71461804030927, 100.41190005301226],
  [13.717912288250515, 100.47327512445216],
  [13.670158209942592, 100.47426891095738],
  [13.599941847073415, 100.3354732226036],
  [13.707224573558767, 100.33833247359794],
  [13.730404753778528, 100.45672016678178]  // ← จุดที่เพิ่ม , ให้เรียบร้อย
];

$zoneDPolygon = [[14.004486568517862, 100.68032456863266], [14.255936197588044, 100.71860827776081], [14.370490233809495, 100.67763207282407], [14.353677057518775, 100.46821229807749], [14.068837378273237, 100.43214392858478], [13.949965347557134, 100.61169949941588]];

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

// ✅ กรองตามวันที่
$selectedDate = request('filter_date');
$filteredBills = collect($bill)->filter(function($item) use ($selectedDate) {
    if (!$selectedDate) return true;
    return Carbon::parse($item->time)->toDateString() === $selectedDate;
});

// ✅ แบ่งกลุ่มตามโซน
$grouped = [
    'Zone A (มาบเอียง,ปลวกแดง,บ่อวิน)' => [],
    'Zone B (ชลบุรี)' => [],
    'Zone C (กรุงเทพ)' => [],
    'Zone D (รังสิต,อยุธยา,อ่างทอง)' => [],
    'อื่น ๆ' => []
];

foreach ($filteredBills as $item) {
    if (!empty($item->customer_la_long) && str_contains($item->customer_la_long, ',')) {
        [$lat, $long] = explode(',', $item->customer_la_long);
        $lat = floatval(trim($lat)); $long = floatval(trim($long));
        $point = [$lat, $long];
        if (pointInPolygon($point, $zoneAPolygon)) {
            $grouped['Zone A (มาบเอียง,ปลวกแดง,บ่อวิน)'][] = $item;
        } elseif (pointInPolygon($point, $zoneBPolygon)) {
            $grouped['Zone B (ชลบุรี)'][] = $item;
        } elseif (pointInPolygon($point, $zoneCPolygon)) {
            $grouped['Zone C (กรุงเทพ)'][] = $item;
        } elseif (pointInPolygon($point, $zoneDPolygon)) {
            $grouped['Zone D (รังสิต,อยุธยา,อ่างทอง)'][] = $item;
        } else {
            $grouped['อื่น ๆ'][] = $item;
        }
    } else {
        $grouped['อื่น ๆ'][] = $item;
    }
}
@endphp
</div>

<!-- ✅ ตารางแสดงผลข้อมูลลูกค้า -->
<div style="display: flex; flex-wrap: wrap; gap: 20px; padding: 20px;">
  @foreach($grouped as $zoneName => $items)
  <div style="flex: 1; min-width: 500px; background-color: #fff; border: 2px solid #999; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1); padding: 15px;">
    <h3 style="margin-top: 0;">{{ $zoneName }}</h3>
    <table>
      <thead>
        <tr>
          <th>ข้อมูลลูกค้า</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $index => $item)
        @php
          $distanceText = '';
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
              {{ $item->time }}<br>
              {{ $item->customer_name }}<br>
              {{ $item->customer_address }}<br>
            <a class="latlong" style="color:#007bff; text-decoration:underline;" href="https://www.google.com/maps?q={{ trim($item->customer_la_long) }}" target="_blank">
              📍 พิกัด: {{ $item->customer_la_long }}
            </a>
              </span><br>
              <span class="latlong">📏 ระยะทาง: {{ $distanceText }}</span>
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
