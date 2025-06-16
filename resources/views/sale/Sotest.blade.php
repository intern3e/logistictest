<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ข้อมูลจัดส่ง</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; }
    .header { background-color: #2d3e50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
    .header h2 { margin: 0; }
    .buttons { display: flex; gap: 15px; align-items: center; }
    .buttons a { color: black; text-decoration: none; font-weight: bold; padding: 8px 14px; border-radius: 6px; background-color: #ffc107; transition: background-color 0.2s ease; }
    .buttons a:hover { background-color: #e0a800; }
    .notification-icon img { width: 24px; height: 24px; }
    .notification-badge { position: relative; top: -10px; right: 10px; background-color: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; }
    .filter-container { padding: 15px 20px; background-color: #fff; border-bottom: 1px solid #ccc; }
    .search-box { padding: 10px 20px; background-color: #f0f0f0; border-bottom: 1px solid #ddd; }
    #search-input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
    .table-container { padding: 20px; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 14px; background-color: white; }
    th, td { text-align: left; padding: 10px; border: 1px solid #ddd; vertical-align: top; }
    th { background-color: #007bff; color: white; font-weight: bold; }
    tr:nth-child(even) { background-color: #f2f2f2; }
  </style>
</head>
<body>
<div class="header">
  <h2>ข้อมูลจัดส่ง</h2>
  <div class="buttons">
    <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>
    @csrf
    <a href="SOlist" class="btn btn-danger">🚪 หน้าหลัก</a>
    <a href="alertsale" class="notification-icon" title="แจ้งเตือนนะจ๊ะ" style="background-color: rgb(245, 245, 69); padding: 5px; border-radius: 5px;">
      <img src="https://cdn-icons-png.flaticon.com/512/2645/2645897.png" alt="แจ้งเตือน">
      <span class="notification-badge" id="alertBadge">0</span>
    </a>
  </div>
</div>
<div class="filter-container">
  <form method="GET" action="{{ route('sale.dashboard') }}" class="filter-form" id="autoSearchForm">
    <label for="date">📅 วันที่: เดือน / วัน / ปี</label>
    <input type="date" id="date" name="date" value="{{ request('date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
  </form>
</div>
<div class="search-box">
  <input type="text" id="search-input" placeholder=" ค้นหา ชื่อลูกค้า / ที่อยู่ / พิกัด" onkeyup="searchTable()">
</div>
<div class="table-container">
  @php
  // Zone A (กรอบสีแดง) - ใช้ค่าที่ถูกต้องตามลำดับ
  $zoneALatMin = 13.083795;
  $zoneALatMax = 13.123811;
  $zoneALongMin = 100.9167453;
  $zoneALongMax = 100.954473;

  // Zone B (จาก 4 จุดที่คุณให้)
  $zoneBLatMin = 12.9396116;
  $zoneBLatMax = 13.0748591;
  $zoneBLongMin = 101.0855834;
  $zoneBLongMax = 101.21371836789208;

  $grouped = [
    'Zone A (ภายในกรอบสีแดง)' => [],
    'Zone B (กรอบที่สอง)' => [],
    'อื่น ๆ' => []
  ];

  foreach ($bill as $item) {
    if (!empty($item->customer_la_long) && str_contains($item->customer_la_long, ',')) {
      [$lat, $long] = explode(',', $item->customer_la_long);
      $lat = floatval(trim($lat));
      $long = floatval(trim($long));

      if ($lat >= $zoneALatMin && $lat <= $zoneALatMax && $long >= $zoneALongMin && $long <= $zoneALongMax) {
        $grouped['Zone A (ภายในกรอบสีแดง)'][] = $item;
      } elseif ($lat >= $zoneBLatMin && $lat <= $zoneBLatMax && $long >= $zoneBLongMin && $long <= $zoneBLongMax) {
        $grouped['Zone B (กรอบที่สอง)'][] = $item;
      } else {
        $grouped['อื่น ๆ'][] = $item;
      }
    } else {
      $grouped['อื่น ๆ'][] = $item;
    }
  }
  @endphp
</div>


  @foreach($grouped as $zoneName => $items)
  <h3>{{ $zoneName }}</h3>
  <table>
    <thead>
      <tr>
        <th>ข้อมูลลูกค้า</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $index => $item)
        <tr>
          <td>
            {{ $item->customer_name }}<br>
            {{ $item->customer_address }}<br>
            <span class="latlong" id="resolved-address-{{ $loop->index }}" data-latlong="{{ $item->customer_la_long }}">
              📍 กำลังดึงที่อยู่...
            </span>
          </td>
        </tr>
      @empty
        <tr><td>ไม่มีข้อมูล</td></tr>
      @endforelse
    </tbody>
  </table>
  @endforeach
</div>

<script>
async function reverseGeocodeOSM(latlong, elementId) {
  const [lat, lon] = latlong.split(',').map(val => val.trim());
  const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=20&addressdetails=1`;

  try {
    const response = await fetch(url, {
      headers: {
        'User-Agent': 'MyDeliveryApp/1.0',
        'Accept-Language': 'th'
      }
    });
    const data = await response.json();
    const addr = data.address;

    // ⚙️ ใช้ fallback หลายชั้น
    const house = addr.house_number || addr.building || '';
    const road = addr.road || addr.footway || addr.path || addr.residential || addr.street || 'ไม่ระบุถนน';
    const moo = addr.quarter || addr.neighbourhood || addr.village || 'ไม่ระบุหมู่';
    const subdistrict = addr.subdistrict || addr.suburb || addr.town || 'ไม่ระบุตำบล';
    const district = addr.city_district || addr.district || addr.county || 'ไม่ระบุอำเภอ';
    const province = addr.state || 'ไม่ระบุจังหวัด';
    const postcode = addr.postcode || '';

    // 🛠️ รวมบ้านเลขที่ + ถนน
    const houseRoad = (house ? `บ้านเลขที่ ${house}, ` : '') + (road !== 'ไม่ระบุถนน' ? `ถนน${road}, ` : '');
    const mooText = (moo !== 'ไม่ระบุหมู่' && !moo.includes('หมู่')) ? `หมู่ ${moo}, ` : (moo !== 'ไม่ระบุหมู่' ? `${moo}, ` : '');
    const fullAddress = `📌 ${houseRoad}${mooText}ต.${subdistrict}, อ.${district}, จ.${province} ${postcode}`;

    document.getElementById(elementId).textContent = fullAddress;
  } catch (e) {
    console.error('❌ Reverse geocode failed:', e);
    document.getElementById(elementId).textContent = '📌 ไม่พบที่อยู่';
  }
}

// 🔁 เรียกใช้งานทุก .latlong เมื่อโหลดหน้าเสร็จ
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('.latlong').forEach((el) => {
    const latlong = el.getAttribute('data-latlong');
    const elementId = el.id;
    if (latlong && elementId) {
      reverseGeocodeOSM(latlong, elementId);
    }
  });
});
</script>


</body>
</html>
