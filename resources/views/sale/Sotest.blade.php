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
    $zoneALatMin = 12.95;
    $zoneALatMax = 13.60;
    $zoneALongMin = 100.85;
    $zoneALongMax = 101.30;

    $grouped = [
      'Zone A (ภายในกรอบสีแดง)' => [],
      'อื่น ๆ' => []
    ];

    foreach ($bill as $item) {
        if (!empty($item->customer_la_long) && str_contains($item->customer_la_long, ',')) {
            [$lat, $long] = explode(',', $item->customer_la_long);
            $lat = floatval(trim($lat));
            $long = floatval(trim($long));

            if (
                $lat >= $zoneALatMin && $lat <= $zoneALatMax &&
                $long >= $zoneALongMin && $long <= $zoneALongMax
            ) {
                $grouped['Zone A (ภายในกรอบสีแดง)'][] = $item;
            } else {
                $grouped['อื่น ๆ'][] = $item;
            }
        } else {
            $grouped['อื่น ๆ'][] = $item;
        }
    }
  @endphp

  @foreach($grouped as $zoneName => $items)
    <h3>{{ $zoneName }}</h3>
    <table>
      <thead>
        <tr>
          <th>ข้อมูลลูกค้า</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $item)
          <tr>
            <td>
              {{ $item->customer_name }}<br>
              <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($item->customer_address) }}" target="_blank">
                {{ $item->customer_address }}
              </a><br>
              @if(str_contains($item->customer_la_long, ','))
                <a href="https://www.google.com/maps?q={{ $item->customer_la_long }}" target="_blank">
                  {{ $item->customer_la_long }}
                </a>
              @else
                {{ $item->customer_la_long }}
              @endif
            </td>
          </tr>
        @empty
          <tr><td>ไม่มีข้อมูล</td></tr>
        @endforelse
      </tbody>
    </table>
  @endforeach

  @if(isset($message))
    <p style="text-align: center">{{ $message }}</p>
  @endif
</div>

<script>
  const form = document.getElementById('autoSearchForm');
  const dateInput = document.getElementById('date');
  dateInput.addEventListener('change', () => form.submit());
  window.addEventListener('load', () => {
    if (!sessionStorage.getItem('hasAutoSubmitted')) {
      sessionStorage.setItem('hasAutoSubmitted', 'true');
      form.submit();
    }
  });

  async function checkForAlerts() {
    try {
      const response = await fetch('/alertsale/count', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const data = await response.json();
      const badge = document.getElementById('alertBadge');
      badge.textContent = data.count > 0 ? data.count : '';
      badge.style.display = data.count > 0 ? 'block' : 'none';
    } catch (e) {
      console.error('แจ้งเตือนล้มเหลว', e);
    }
  }

  checkForAlerts();
  setInterval(checkForAlerts, 1000);

  function searchTable() {
    let input = document.getElementById("search-input").value.toLowerCase();
    let tables = document.querySelectorAll(".table-container table");

    tables.forEach(table => {
      let rows = table.getElementsByTagName("tr");
      for (let i = 1; i < rows.length; i++) {
        let row = rows[i];
        let cellText = row.textContent.toLowerCase();
        row.style.display = cellText.includes(input) ? "" : "none";
      }
    });
  }
</script>

</body>
</html>
