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
    .zone-column {
    flex: 1;
    min-width: 500px;
    max-width: 100%;
    height: 700px;
    display: flex;
    flex-direction: column;
    background-color: #fff;
    border: 2px solid #999;
    border-radius: 8px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    padding: 15px;
  }

  .zone-header {
    flex-shrink: 0;
    margin-top: 0;
  }

  .zone-table-wrapper {
    flex-grow: 1;
    overflow-y: auto;
    margin-top: 10px;
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
    <a href="adminroute" button  type="submit" class="btn btn-danger">จัดของ</a>
    <a href="http://server_update:8000/solist" button  type="submit" class="btn btn-danger">🚪 หน้าหลัก</a>
  </div>
</div>

<div class="filter-container" style="display: flex; align-items: center; gap: 15px;">
  <form method="GET" action="{{ url()->current() }}" id="dateFilterForm" style="display: flex; align-items: center; gap: 10px;">
    <label for="filter_date">📅 เลือกวันที่:</label>
    <input type="date" id="filter_date" name="filter_date"
      value="{{ request('filter_date') ?: \Carbon\Carbon::tomorrow()->toDateString() }}"
      onchange="document.getElementById('dateFilterForm').submit();">
  </form>


  <div style="display: flex; align-items: center; gap: 15px;">
    <button onclick="downloadJSON()" id="downloadBtn"
      style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 6px; font-weight: bold;">
      ส่งข้อมูลไปคนขับ
    </button>
    <span id="statusMessage" style="font-weight: bold; color: #333;"></span>
  </div>
</div>

</div>

<script>
// ✅ ตั้งค่าวันที่เป็นวันปัจจุบันเมื่อโหลดหน้าเว็บ (ถ้าไม่มี parameter filter_date)
document.addEventListener('DOMContentLoaded', function() {
  const filterDateInput = document.getElementById('filter_date');
  const urlParams = new URLSearchParams(window.location.search);

  if (!urlParams.has('filter_date')) {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const formatted = tomorrow.toISOString().split('T')[0];
    filterDateInput.value = formatted;
    document.getElementById('dateFilterForm').submit();
  }
});
function downloadJSON() {
  const btn = document.getElementById('downloadBtn');
  const statusEl = document.getElementById('statusMessage');

  const confirmed = confirm("คุณต้องการส่งข้อมูลใช่หรือไม่?");
  if (!confirmed) return;

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

      const contentLines = lines.filter(line => !line.startsWith("งานที่:"));
      const so_id        = contentLines[0] || '';
      const datetime     = contentLines[1] || '';
      const name         = contentLines[2] || '';
      const customer_tel = contentLines[3] || '';
      let address        = contentLines[4] || '';
      const latlongLine  = contentLines.find(line => line.includes('📍 พิกัด:')) || '';
      const distanceLine = contentLines.find(line => line.includes('📏 ระยะทาง:')) || '';
      const latlong      = latlongLine.replace(/📍\s*พิกัด:\s*/g, '').trim();

      if (address.includes('📍 พิกัด:')) {
        address = address.replace(/📍\s*พิกัด:/, '').trim();
      }

      // ✅ ดึง notes จากบรรทัดสุดท้ายที่ไม่ใช่พิกัดหรือระยะทาง
      const notes = contentLines.findLast(line =>
        !line.includes('📍 พิกัด:') &&
        !line.includes('📏 ระยะทาง:') &&
        ![so_id, datetime, name, customer_tel, address].includes(line)
      ) || '';

      let latitude = '', longitude = '';
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
        distance: distanceLine.replace(/📏\s*ระยะทาง:\s*/g, '').trim(),
        notes: notes   // ✅ แก้ตรงนี้ให้มีค่า
      });
    });

    zoneData[zoneName] = zoneItems;
  });

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

if (!function_exists('calculateDistance')) {
    function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        // คำนวณระยะทาง (เช่น Haversine formula)
        $earthRadius = 6371; // หน่วย: กิโลเมตร

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // ระยะทางหน่วยกิโลเมตร
    }
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
    [13.095168680576418, 101.05608371890287], 
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
$zonejPolygon = [

];

if (!function_exists('pointInPolygon')) {
    function pointInPolygon($point, $polygon) {
        $x = $point[0];
        $y = $point[1];
        $inside = false;

        $numPoints = count($polygon);
        for ($i = 0, $j = $numPoints - 1; $i < $numPoints; $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];

            $intersect = (($yi > $y) != ($yj > $y)) &&
                         ($x < ($xj - $xi) * ($y - $yi) / (($yj - $yi) ?: 1e-10) + $xi);
            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
}


// ✅ กรองตามวันที่ - ใช้วันที่ปัจจุบันถ้าไม่มีการเลือกวันที่
$selectedDate = request('filter_date') ?: date('Y-m-d');
$filteredBills = collect($bill)->filter(function($item) use ($selectedDate) {
    return Carbon::parse($item->date_of_dali)->toDateString() === $selectedDate
        && $item->status == 1
        && $item->statuspdf == 2
        && $item->statuspdf != 6;
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
    'Zone j เอ้(ใจกลางเมือง)' => [],
    'Zone I (จักรยานยนต์)' => [],
    'อื่น ๆ' => []
];

// ✅ รายการ customer_id ที่บังคับให้จัดเข้า Zone B
$zoneBCustomerIds = [
  'CUS-06438',     'CUS-07464.3',  'CUS-07516.1',  'CUS-07655',    'CUS-08688.5',
  'CUS-08995.1',   'CUS-12069',    'CUS-14942.1',  'CUS-18739',    'CUS-10037',
  'CUS-10924',     'CUS-10924.2',  'CUS-10924.3',  'CUS-11046.2',  'CUS-14370',
  'CUS-14781.2',   'CUS-20561',    'CUS-24401',    'CUS-07573.1',  'CUS-07613.1',
  'CUS-09402',     'CUS-11031',    'CUS-11277',    'CUS-13321.3',  'CUS-07751',
  'CUS-11194',     'CUS-15325',    'CUS-07182',    'CUS-16152.2',  'CUS-18454',
  'CUS-21082',     'CUS-07343',    'CUS-06419.2',  'CUS-06842',    'CUS-08785',
  'CUS-12441.2',   'CUS-12859',    'CUS-17611',    'CUS-20016',    'CUS-08074',
  'CUS-08360.2',   'CUS-10016.3',  'CUS-10144.1',  'CUS-10238.2',  'CUS-24126',
  'CUS-17469',     'CUS-18637.1',  'CUS-21484',    'CUS-16714.1',  'CUS-16714.2',
  'CUS-16714.3',   'CUS-09050',    'CUS-09620.1',  'CUS-11720.2',  'CUS-15942.1',
  'CUS-06058',     'CUS-07003',    'CUS-10404',    'CUS-19424',    'CUS-10897',
  'CUS-15743',     'CUS-18764',    'CUS-07516.2',  'CUS-11378',    'CUS-06124',
  'CUS-20656',     'CUS-06815.1',  'CUS-08074.7',  'CUS-20480',    'CUS-08360.4',
  'CUS-17611.1',   'CUS-20098',    'CUS-22247',    'CUS-07516.3',  'CUS-24714',
  'CUS-12490.1',   'CUS-12490.4',  'CUS-12490.5',  'CUS-14290.7',  'CUS-14290.9',
  'CUS-24126',     'CUS-14942.1',  'CUS-11046.2',  'CUS-21082',    'CUS-08074',
  'CUS-08995.1'
];



$zoneGCustomerIds = [
  "CUS-07992", "CUS-08848.1", "CUS-09037.1", "CUS-09387.1", "CUS-17671.8",
  "CUS-19064.5", "CUS-24145", "CUS-07162.9", "CUS-08007.5", "CUS-10357",
  "CUS-15151", "CUS-15247.3", "CUS-23862", "CUS-07911.2", "CUS-09477",
  "CUS-12971", "CUS-23499", "CUS-24328", "CUS-24577", "CUS-11224",
  "CUS-12574", "CUS-13073.2", "CUS-19615", "CUS-23633", "CUS-07439.2",
  "CUS-10031.1", "CUS-12411.1", "CUS-12601", "CUS-23416.1", "CUS-08521",
  "CUS-24694", "CUS-11875", "CUS-12359.8", "CUS-19648", "CUS-10173",
  "CUS-16704.6", "CUS-17688.4", "CUS-19064.6", "CUS-10239", "CUS-12180.1",
  "CUS-06286", "CUS-08608.1", "CUS-23242.1", "CUS-24818", "CUS-09235",
  "CUS-15325", "CUS-13936.2", "CUS-24821", "CUS-08424", "CUS-11455.3",
  "CUS-17688.1", "CUS-19064.1", "CUS-23844", "CUS-12683", "CUS-24366",
  "CUS-09079", "CUS-16109.1", "CUS-23043", "CUS-06514", "CUS-12558",
  "CUS-07162.7", "CUS-23786", "CUS-24246", "CUS-08071.1", "CUS-08826.1"
];


$zoneACustomerIds = [
  "CUS-06001.1", "CUS-06001.1", "CUS-06022.3", "CUS-06050", "CUS-06224.2",
  "CUS-07005.2", "CUS-13052.1", "CUS-16152.4", "CUS-18637.1", "CUS-07157.3",
  "CUS-07162.7", "CUS-07163", "CUS-10026.2", "CUS-10144.1", "CUS-14374.2",
  "CUS-17416.1", "CUS-23372", "CUS-06030", "CUS-06111.6", "CUS-14038.2",
  "CUS-18345", "CUS-24560", "CUS-06089", "CUS-15325", "CUS-19383.1",
  "CUS-15743", "CUS-08237", "CUS-24811", "CUS-23120", "CUS-06419.2",
  "CUS-09301.8", "CUS-14591.1", "CUS-07157.5", "CUS-11070.2", "CUS-07347.1",
  "CUS-07641.1", "CUS-12837.2", "CUS-06514.4", "CUS-21128", "CUS-12490.4",
  "CUS-12490.5", "CUS-14290.7", "CUS-14290.9", "CUS-07053", "CUS-07516.1",
  "CUS-16610", "CUS-15375", "CUS-06008.7", "CUS-10016.3", "CUS-19332.2",
  "CUS-23970", "CUS-13903", "CUS-13936.29", "CUS-18180", "CUS-06093",
  "CUS-10026", "CUS-13936.30", "CUS-13946.1", "CUS-23310", "CUS-06022.3",
  "CUS-13229", "CUS-13936.6", "CUS-06170.1", "CUS-07485.2", "CUS-14290"
];



$zoneDCustomerIds = [
  'CUS-07162.7', 'CUS-07675.21', 'CUS-09108.7', 'CUS-09108.9', 'CUS-11720',
  'CUS-12198', 'CUS-12490.5', 'CUS-13207.3', 'CUS-13936.10', 'CUS-13936.20',
  'CUS-13936.44', 'CUS-14550', 'CUS-19633.1', 'CUS-20565', 'CUS-08026.1',
  'CUS-09337.2', 'CUS-15620', 'CUS-16152.5', 'CUS-21324', 'CUS-21648',
  'CUS-21730', 'CUS-23304', 'CUS-07257.2', 'CUS-08057.1', 'CUS-08071.1',
  'CUS-12701', 'CUS-12963.1', 'CUS-21052', 'CUS-23056.1', 'CUS-24164',
  'CUS-24182', 'CUS-12490.1', 'CUS-12490.4', 'CUS-14290.7', 'CUS-14290.8',
  'CUS-14290.9', 'CUS-08101.1', 'CUS-10903.1', 'CUS-07162.7', 'CUS-16714.2',
  'CUS-11496', 'CUS-17624', 'CUS-18633.10', 'CUS-18890', 'CUS-21013',
  'CUS-23532.3', 'CUS-13089', 'CUS-14712', 'CUS-15065', 'CUS-20065.1',
  'CUS-21949', 'CUS-24770', 'CUS-24854', 'CUS-07352.1', 'CUS-09812',
  'CUS-11447', 'CUS-23616', 'CUS-08007.5', 'CUS-08688.4', 'CUS-08983.3',
  'CUS-12411.1', 'CUS-15325', 'CUS-15989.1', 'CUS-17688.2', 'CUS-24145',
  'CUS-07521', 'CUS-09219.1', 'CUS-09235', 'CUS-12490.6', 'CUS-16809',
  'CUS-16809.2', 'CUS-16809.9', 'CUS-18458', 'CUS-18527', 'CUS-18763',
  'CUS-19272', 'CUS-20087.1', 'CUS-20735', 'CUS-21948', 'CUS-09108.8',
  'CUS-11033.2', 'CUS-21996', 'CUS-23810.1', 'CUS-07136', 'CUS-10108',
  'CUS-12610', 'CUS-18347', 'CUS-07160', 'CUS-12691', 'CUS-18713',
  'CUS-09456.1', 'CUS-09812', 'CUS-18933.1', 'CUS-07136.3', 'CUS-06905.1',
  'CUS-15892', 'CUS-07062', 'CUS-13147.1', 'CUS-13936.21', 'CUS-21342',
  'CUS-24821', 'CUS-07807', 'CUS-08445', 'CUS-09227', 'CUS-10672.1',
  'CUS-24828', 'CUS-22163', 'CUS-15620', 'CUS-14457', 'CUS-23633',
  'CUS-24512', 'CUS-18897', 'CUS-23208', 'CUS-24405', 'CUS-24455',
  'CUS-07905', 'CUS-10456.1', 'CUS-12359', 'CUS-14223', 'CUS-15833',
  'CUS-24235', 'CUS-16124', 'CUS-16126.1', 'CUS-06084', 'CUS-08291',
  'CUS-11315', 'CUS-15380', 'CUS-15896', 'CUS-18633.1', 'CUS-21193',
  'CUS-22703', 'CUS-24864', 'CUS-13561', 'CUS-20723', 'CUS-22062',
  'CUS-11496', 'CUS-17688.2', 'CUS-23633', 'CUS-14550'
];


$zoneHCustomerIds = [
  'CUS-06226', 'CUS-06226.12', 'CUS-08668', 'CUS-16714.1', 'CUS-16714.3',
  'CUS-06012', 'CUS-07282', 'CUS-12239.3', 'CUS-13927', 'CUS-19357',
  'CUS-16714.2', 'CUS-10124', 'CUS-06810', 'CUS-23163', 'CUS-15325',
  'CUS-06024', 'CUS-09060.1', 'CUS-13321.3', 'CUS-24011', 'CUS-18825',
  'CUS-22873.1', 'CUS-06226.7', 'CUS-10016.2', 'CUS-17889', 'CUS-16301',
  'CUS-06022.1', 'CUS-06226', 'CUS-06280.1', 'CUS-06869.4', 'CUS-20604.1',
  'CUS-07641.1', 'CUS-21996', 'CUS-08007.5', 'CUS-06531', 'CUS-24576.1',
  'CUS-09195', 'CUS-10016.1', 'CUS-14188.1', 'CUS-19634.1', 'CUS-06014.1',
  'CUS-21940', 'CUS-06857.1', 'CUS-09263.1', 'CUS-12411.1', 'CUS-13073',
  'CUS-20098'
];




$zoneECustomerIds = [
  'CUS-06045.10', 'CUS-06076.9', 'CUS-06381.3', 'CUS-06783',   'CUS-11208',
  'CUS-11605',    'CUS-13630.1', 'CUS-21848',   'CUS-06381',   'CUS-06998',
  'CUS-07095.6',  'CUS-07911',   'CUS-09062',   'CUS-09404.4', 'CUS-11104.1',
  'CUS-12530.1',  'CUS-12810.4', 'CUS-19326.1', 'CUS-22967',   'CUS-06442',
  'CUS-06671',    'CUS-06950',   'CUS-07492.1', 'CUS-08057',   'CUS-10322',
  'CUS-10932.1',  'CUS-19206',   'CUS-20520',   'CUS-12878.1', 'CUS-13444',
  'CUS-14355.3',  'CUS-09062.3', 'CUS-09708',   'CUS-09404.3', 'CUS-11557.1',
  'CUS-07911.1',  'CUS-23446',   'CUS-24841',   'CUS-16714.3', 'CUS-06226.5',
  'CUS-06584.1',  'CUS-06998.3', 'CUS-09273.1', 'CUS-21034',   'CUS-17554',
  'CUS-19064.7',  'CUS-08040.1', 'CUS-10322.1', 'CUS-10841.1', 'CUS-14671',
  'CUS-16381'
];



$zoneCCustomerIds = [
  "CUS-06003.1", "CUS-06003.1", "CUS-06003.3", "CUS-07121.6", "CUS-07326.6",
  "CUS-15325", "CUS-18093", "CUS-18519", "CUS-18525.1", "CUS-19332",
  "CUS-06003", "CUS-13207.4", "CUS-22873.1", "CUS-06085", "CUS-06276.1",
  "CUS-14326.2", "CUS-18510", "CUS-20393.1", "CUS-23072", "CUS-06905.1",
  "CUS-06023", "CUS-12770.1", "CUS-14290.11", "CUS-24339", "CUS-06978",
  "CUS-19332.2", "CUS-09387.1", "CUS-12152.5", "CUS-23633", "CUS-06012",
  "CUS-12239.3", "CUS-13927", "CUS-24057.2", "CUS-06116.2", "CUS-08802",
  "CUS-11203", "CUS-18093.1", "CUS-07121.2", "CUS-20290", "CUS-06020",
  "CUS-19298", "CUS-19763", "CUS-22873", "CUS-07282", "CUS-21944",
  "CUS-07326.2", "CUS-18690", "CUS-10817", "CUS-10817.1", "CUS-23627",
  "CUS-13108", "CUS-20443", "CUS-19270", "CUS-19525", "CUS-06697",
  "CUS-16809.12", "CUS-18072", "CUS-18572.1", "CUS-24531", "CUS-24765",
  "CUS-19270", "CUS-19525"
];


$zoneFCustomerIds = [
  "CUS-06623.1", "CUS-06623.1", "CUS-07157.5", "CUS-07157.8", "CUS-09563.3",
  "CUS-13200", "CUS-14747.3", "CUS-19846", "CUS-23421", "CUS-06381.10",
  "CUS-06381.8", "CUS-06494.1", "CUS-06695", "CUS-09328", "CUS-09367.2",
  "CUS-13175", "CUS-18405.1", "CUS-19326", "CUS-06119.1", "CUS-09330.1",
  "CUS-11362", "CUS-06045.11", "CUS-12359.1", "CUS-07157.7", "CUS-07309.5",
  "CUS-22900", "CUS-09456.1", "CUS-22718", "CUS-06961", "CUS-08040.1",
  "CUS-10842.2", "CUS-21549.1", "CUS-12161", "CUS-19859", "CUS-24383.1",
  "CUS-07851.1", "CUS-08455.1", "CUS-18046", "CUS-09062.1", "CUS-09062.2",
  "CUS-14747.4", "CUS-07157.2", "CUS-12810.4", "CUS-15325", "CUS-23707",
  "CUS-07992", "CUS-12941.1", "CUS-10996", "CUS-10552.1", "CUS-22623.1",
  "CUS-09404.4", "CUS-11605", "CUS-12878.1", "CUS-17527", "CUS-12596",
  "CUS-06623", "CUS-12359.8", "CUS-06921", "CUS-06432", "CUS-06391.1"
];

$zonejCustomerIds = [
  'CUS-06905.1', 'CUS-07257.1', 'CUS-07326.2', 'CUS-07952.2', 'CUS-16431',
  'CUS-20735', 'CUS-22737', 'CUS-09932.11', 'CUS-09932.15', 'CUS-09932.5',
  'CUS-13307.12', 'CUS-13307.13', 'CUS-13307.14', 'CUS-13307.2', 'CUS-13307.5',
  'CUS-16809', 'CUS-16809.8', 'CUS-16809.9', 'CUS-17692', 'CUS-18515',
  'CUS-18778', 'CUS-19114', 'CUS-23115', 'CUS-09037', 'CUS-15204',
  'CUS-16416.1', 'CUS-19815.1', 'CUS-18292', 'CUS-13307.9', 'CUS-06067.2',
  'CUS-09219.1', 'CUS-09235', 'CUS-16809.2', 'CUS-18458', 'CUS-18527',
  'CUS-18758.2', 'CUS-18763', 'CUS-19272', 'CUS-19478', 'CUS-24694',
  'CUS-09060.1', 'CUS-11033.2', 'CUS-14290'
];


foreach ($filteredBills as $item) {
    $customerId = $item->customer_id ?? '';

    if (in_array($customerId, $zoneBCustomerIds)) {
        $grouped['Zone B บังเดช(ชลบุรี)'][] = $item;
        continue;
    }
    if (in_array($customerId, $zoneFCustomerIds)) {
        $grouped['Zone F แฟรงค์(บางนาตราด กม 13)'][] = $item;
        continue;
    }
    if (in_array($customerId, $zoneECustomerIds)) {
        $grouped['Zone E เอ(บางนาตราด กม 11)'][] = $item;
        continue;
    }
    if (in_array($customerId, $zoneGCustomerIds)) {
        $grouped['Zone G เเชม(กรุงเทพปริมณฑล)'][] = $item;
        continue;
    }
    if (in_array($customerId, $zoneCCustomerIds)) {
        $grouped['Zone C ยุทร(พระราม 2)'][] = $item;
        continue;
    }
    if (in_array($customerId, $zoneDCustomerIds)) {
        $grouped['Zone D หรั่ง(รังสิต,อยุธยา)'][] = $item;
        continue;
    }
    if (in_array($customerId, $zoneHCustomerIds)) {
        $grouped['Zone H (เก่ง)'][] = $item;
        continue;
    }
    if (in_array($customerId, $zoneACustomerIds)) {
        $grouped['Zone A กอล์ฟ(มาบเอียง)'][] = $item;
        continue;
    }
    if (in_array($customerId, $zonejCustomerIds)) {
    $grouped['Zone j เอ้(ใจกลางเมือง)'][] = $item;
    continue;
}

    


// ตรวจสอบพิกัดหากไม่อยู่ในกลุ่ม customer_id ที่กำหนด
if (!empty($item->customer_la_long) && str_contains($item->customer_la_long, ',')) {
    [$lat, $long] = explode(',', $item->customer_la_long);
    $lat = floatval(trim($lat));
    $long = floatval(trim($long));
    $point = [$lat, $long];

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
    } elseif (pointInPolygon($point, $zonejPolygon)) {
        $grouped['Zone j เอ้(ใจกลางเมือง)'][] = $item;  // ✅ เพิ่มตรงนี้
    } elseif (pointInPolygon($point, $zoneAPolygon)) {
        $grouped['Zone A กอล์ฟ(มาบเอียง)'][] = $item;
    } else {
        $grouped['อื่น ๆ'][] = $item; // ✅ กรณีไม่เข้า polygon ใดเลย
    }
}

}



// ✅ กรองและจัดกลุ่ม PO ตามโซน
$filteredPOs = collect($pobill)->filter(function($item) use ($selectedDate) {
    return Carbon::parse($item->recvDate)->toDateString() === $selectedDate;
});

$groupedPO = [
    'Zone A กอล์ฟ(มาบเอียง)' => [],
    'Zone B บังเดช(ชลบุรี)' => [],
    'Zone C ยุทร(พระราม 2)' => [],
    'Zone D หรั่ง(รังสิต,อยุธยา)' => [],
    'Zone E เอ(บางนาตราด กม 11)' => [],
    'Zone F แฟรงค์(บางนาตราด กม 13)' => [],
    'Zone G เเชม(กรุงเทพปริมณฑล)' => [],
    'Zone H (เก่ง)' => [],
    'Zone j เอ้(ใจกลางเมือง)' => [],
    'Zone I (จักรยานยนต์)' => [],
    'อื่น ๆ' => []
];

foreach ($filteredPOs as $item) {
    // 🚩 ถ้าเป็นจักรยานยนต์ → ส่งเข้า Zone I โดยไม่สนพิกัด
    if ($item->cartype == 1) {
        $groupedPO['Zone I (จักรยานยนต์)'][] = $item;
        continue;
    }

    if (!empty($item->store_la_long) && str_contains($item->store_la_long, ',')) {
        [$lat, $long] = explode(',', $item->store_la_long);
        $lat = floatval(trim($lat));
        $long = floatval(trim($long));
        $point = [$lat, $long];

        if (pointInPolygon($point, $zoneBPolygon)) {
            $groupedPO['Zone B บังเดช(ชลบุรี)'][] = $item;
        } elseif (pointInPolygon($point, $zoneFPolygon)) {
            $groupedPO['Zone F แฟรงค์(บางนาตราด กม 13)'][] = $item;
        } elseif (pointInPolygon($point, $zoneEPolygon)) {
            $groupedPO['Zone E เอ(บางนาตราด กม 11)'][] = $item;
        } elseif (pointInPolygon($point, $zoneGPolygon)) {  
            $groupedPO['Zone G เเชม(กรุงเทพปริมณฑล)'][] = $item;
        } elseif (pointInPolygon($point, $zoneCPolygon)) {
            $groupedPO['Zone C ยุทร(พระราม 2)'][] = $item;
        } elseif (pointInPolygon($point, $zoneDPolygon)) {
            $groupedPO['Zone D หรั่ง(รังสิต,อยุธยา)'][] = $item;
        } elseif (pointInPolygon($point, $zoneHPolygon)) {
            $groupedPO['Zone H (เก่ง)'][] = $item;
        } elseif (pointInPolygon($point, $zonejPolygon)) {
            $groupedPO['Zone j เอ้(ใจกลางเมือง)'][] = $item; // ✅ เพิ่มตรงนี้
        } elseif (pointInPolygon($point, $zoneAPolygon)) {
            $groupedPO['Zone A กอล์ฟ(มาบเอียง)'][] = $item;
        } else {
            $groupedPO['อื่น ๆ'][] = $item;
        }
    } else {
        $groupedPO['อื่น ๆ'][] = $item;
    }
}


// ✅ เพิ่มการกรอง DocBill ตามวันที่ที่เลือก
$filteredDocBills = collect($docbill)->filter(function($item) use ($selectedDate) {
    // สมมติว่าฟิลด์วันที่ใน docbill คือ 'doc_date' หรือฟิลด์ที่เก็บวันที่
    // เปลี่ยนชื่อฟิลด์ให้ตรงกับฐานข้อมูลของคุณ
    return Carbon::parse($item->datestamp)->toDateString() === $selectedDate;
});

$groupedDocBill = [
    'Zone A กอล์ฟ(มาบเอียง)' => [],
    'Zone B บังเดช(ชลบุรี)' => [],
    'Zone C ยุทร(พระราม 2)' => [],
    'Zone D หรั่ง(รังสิต,อยุธยา)' => [],
    'Zone E เอ(บางนาตราด กม 11)' => [],
    'Zone F แฟรงค์(บางนาตราด กม 13)' => [],
    'Zone G เเชม(กรุงเทพปริมณฑล)' => [],
    'Zone H (เก่ง)' => [],
    'Zone j เอ้(ใจกลางเมือง)' => [],
    'Zone I (จักรยานยนต์)' => [],
    'อื่น ๆ' => [],
];

if ($filteredDocBills) {
    foreach ($filteredDocBills as $item) {
        $customerId = $item->customer_id ?? '';

        // ✅ กรองโดย ID เท่านั้น
        if (in_array($customerId, $zoneBCustomerIds)) {
            $groupedDocBill['Zone B บังเดช(ชลบุรี)'][] = $item;
            continue;
        }
        if (in_array($customerId, $zoneFCustomerIds)) {
            $groupedDocBill['Zone F แฟรงค์(บางนาตราด กม 13)'][] = $item;
            continue;
        }
        if (in_array($customerId, $zoneECustomerIds)) {
            $groupedDocBill['Zone E เอ(บางนาตราด กม 11)'][] = $item;
            continue;
        }
        if (in_array($customerId, $zoneGCustomerIds)) {
            $groupedDocBill['Zone G เเชม(กรุงเทพปริมณฑล)'][] = $item;
            continue;
        }
        if (in_array($customerId, $zoneCCustomerIds)) {
            $groupedDocBill['Zone C ยุทร(พระราม 2)'][] = $item;
            continue;
        }
        if (in_array($customerId, $zoneDCustomerIds)) {
            $groupedDocBill['Zone D หรั่ง(รังสิต,อยุธยา)'][] = $item;
            continue;
        }
        if (in_array($customerId, $zoneHCustomerIds)) {
            $groupedDocBill['Zone H (เก่ง)'][] = $item;
            continue;
        }
        if (in_array($customerId, $zoneACustomerIds)) {
            $groupedDocBill['Zone A กอล์ฟ(มาบเอียง)'][] = $item;
            continue;
        }
        if (in_array($customerId, $zonejCustomerIds)) {
    $groupedDocBill['Zone j เอ้(ใจกลางเมือง)'][] = $item;
    continue;
}

// ✅ ถ้าไม่ตรง ID ให้ใช้พิกัด
if (!empty($item->com_la_long) && str_contains($item->com_la_long, ',')) {
    [$lat, $long] = explode(',', $item->com_la_long);
    $lat = floatval(trim($lat));
    $long = floatval(trim($long));
    $point = [$lat, $long];

    if (pointInPolygon($point, $zoneBPolygon)) {
        $groupedDocBill['Zone B บังเดช(ชลบุรี)'][] = $item;
    } elseif (pointInPolygon($point, $zoneFPolygon)) {
        $groupedDocBill['Zone F แฟรงค์(บางนาตราด กม 13)'][] = $item;
    } elseif (pointInPolygon($point, $zoneEPolygon)) {
        $groupedDocBill['Zone E เอ(บางนาตราด กม 11)'][] = $item;
    } elseif (pointInPolygon($point, $zoneGPolygon)) {
        $groupedDocBill['Zone G เเชม(กรุงเทพปริมณฑล)'][] = $item;
    } elseif (pointInPolygon($point, $zoneCPolygon)) {
        $groupedDocBill['Zone C ยุทร(พระราม 2)'][] = $item;
    } elseif (pointInPolygon($point, $zoneDPolygon)) {
        $groupedDocBill['Zone D หรั่ง(รังสิต,อยุธยา)'][] = $item;
    } elseif (pointInPolygon($point, $zoneHPolygon)) {
        $groupedDocBill['Zone H (เก่ง)'][] = $item;
    } elseif (pointInPolygon($point, $zonejPolygon)) {
        $groupedDocBill['Zone j เอ้(ใจกลางเมือง)'][] = $item; // ✅ เพิ่มตรงนี้
    } elseif (pointInPolygon($point, $zoneAPolygon)) {
        $groupedDocBill['Zone A กอล์ฟ(มาบเอียง)'][] = $item;
    } else {
        $groupedDocBill['อื่น ๆ'][] = $item;
    }
} else {
    $groupedDocBill['อื่น ๆ'][] = $item;
}

    }
}




@endphp
</div>

{{-- <!-- ✅ ตารางแสดงผลข้อมูลลูกค้า -->
<div style="display: flex; flex-wrap: wrap; gap: 20px; padding: 20px;">
  @foreach($grouped as $zoneName => $items)
  <div style="flex: 1; min-width: 500px; background-color: #fff; border: 2px solid #999; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1); padding: 15px;"> --}}
    
<!-- ✅ ตารางแสดงผลข้อมูลลูกค้า -->
<div style="display: flex; flex-wrap: wrap; gap: 20px; padding: 20px;">
  @foreach($grouped as $zoneName => $soItems)
  <div class="zone-column">
    @php
      $zoneIndex = $loop->index + 1;
      $docItems = $groupedDocBill[$zoneName] ?? [];
      $poItems = $groupedPO[$zoneName] ?? [];
      $workIndex = 0;
      $mergedItems = [];
      foreach ($soItems as $item) {
        $distance = 'ไม่ทราบระยะทาง';
        if (!empty($item->customer_la_long) && str_contains($item->customer_la_long, ',')) {
          [$lat, $long] = explode(',', $item->customer_la_long);
          $distance = calculateDistance($startLat, $startLong, $lat, $long);
        }
        $mergedItems[] = ['type' => 'SO', 'item' => $item, 'distance' => is_numeric($distance) ? $distance : 9999];
      }
      foreach ($poItems as $item) {
        $distance = 'ไม่ทราบระยะทาง';
        if (!empty($item->store_la_long) && str_contains($item->store_la_long, ',')) {
          [$lat, $long] = explode(',', $item->store_la_long);
          $distance = calculateDistance($startLat, $startLong, $lat, $long);
        }
        $mergedItems[] = ['type' => 'PO', 'item' => $item, 'distance' => is_numeric($distance) ? $distance : 9999];
      }
      foreach ($docItems as $item) {
        $distance = 'ไม่ทราบระยะทาง';
        if (!empty($item->com_la_long) && str_contains($item->com_la_long, ',')) {
          [$lat, $long] = explode(',', $item->com_la_long);
          $distance = calculateDistance($startLat, $startLong, $lat, $long);
        }
        $mergedItems[] = ['type' => 'DOC', 'item' => $item, 'distance' => is_numeric($distance) ? $distance : 9999];
      }
      $sortedItems = collect($mergedItems)->sortBy('distance');
    @endphp

    <h3 class="zone-header">
      โซนที่ {{ $zoneIndex }}: {{ $zoneName }} — จำนวนงาน: {{ count($sortedItems) }}
    </h3>

    <div class="zone-table-wrapper">
      <table>
        <thead><tr><th>ข้อมูลลูกค้า</th></tr></thead>
        <tbody>
          @foreach($sortedItems as $data)
          @php
            $workIndex++;
            $item = $data['item'];
            $distanceText = is_numeric($data['distance']) ? number_format($data['distance'], 2) . ' กม. จากจุดเริ่มต้น' : 'ไม่ทราบระยะทาง';
          @endphp
          <tr><td>
            @if($data['type'] === 'SO')
              <div style="border: 2px solid #007bff; border-radius: 6px; padding: 10px; background-color: #f8f9ff;">
                <strong>SO/งานที่: {{ $workIndex }}</strong><br>
                {{ $item->billid ?? '-' }}/{{ $item->date_of_dali ?? '-' }}<br>
                {{ $item->customer_name ?? '-' }}<br> 
                {{ $item->contactso ?? '-' }} {{ $item->customer_tel ?? '-' }}<br>
                {{ $item->customer_address ?? '-' }}<br>
                <a class="latlong" style="color:#007bff; text-decoration:underline;" href="https://www.google.com/maps?q={{ trim($item->customer_la_long ?? '') }}" target="_blank">📍 พิกัด: {{ $item->customer_la_long ?? '-' }}</a><br>
                <span class="latlong" style="color: #28a745; font-weight: bold;">📏 ระยะทาง: {{ $distanceText }}</span><br>
                {{ $item->notes ?? '-' }}
              </div>
            @elseif($data['type'] === 'PO')
              <div style="border: 2px solid #ff6b35; border-radius: 6px; padding: 10px; background-color: #fff5f0;">
                <strong>PO/งานที่: {{ $workIndex }}</strong><br>
                {{ $item->po_id ?? '-' }}/{{ $item->recvDate ?? '-' }}<br>
                {{ $item->store_name ?? '-' }}<br>
                {{ $item->store_tel ?? '-' }}<br>
                {{ $item->store_address ?? '-' }}<br>
                <a class="latlong" style="color:#007bff; text-decoration:underline;" href="https://www.google.com/maps?q={{ trim($item->store_la_long ?? '') }}" target="_blank">📍 พิกัด: {{ $item->store_la_long ?? '-' }}</a><br>
                {{ $item->cartype == 1 ? 'จักรยานยนต์' : ($item->cartype == 2 ? 'รถใหญ่' : '') }}<br>
                <span class="latlong" style="color: #28a745; font-weight: bold;">📏 ระยะทาง: {{ $distanceText }}</span><br>
                {{ $item->notes ?? '-' }}
              </div>
            @elseif($data['type'] === 'DOC')
              <div style="border: 2px solid #279100; border-radius: 6px; padding: 10px; background-color: #fefffe;">
                <strong>บิลชั่วคราว/ที่: {{ $workIndex }}</strong><br>
                {{ $item->doc_id ?? '-' }}/{{ $item->datestamp ?? '-' }}<br>
                {{ $item->com_name ?? '-' }} {{ $item->doctype ?? '-' }}<br>
                {{ $item->contact_tel ?? '-' }}<br>
                {{ $item->com_address ?? '-' }}<br>
                @if(!empty($item->com_la_long))
                  <a class="latlong" style="color:#007bff; text-decoration:underline;" href="https://www.google.com/maps?q={{ trim($item->com_la_long) }}" target="_blank">📍 พิกัด: {{ $item->com_la_long }}</a><br>
                @endif
                <span class="latlong" style="color: #28a745; font-weight: bold;">📏 ระยะทาง: {{ $distanceText }}</span><br>
                {{ $item->notes ?? '-' }}
              </div>
            @endif
          </td></tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endforeach
</div>

</body>
</html>
