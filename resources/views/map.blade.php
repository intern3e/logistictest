<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลพื้นที่</title>
</head>
<body>
    <h1>ข้อมูลบริษัท</h1>

    <table border="1">
        <thead>
            <tr>
                <th>ชื่อบริษัท</th>
                <th>ที่อยู่จัดส่ง</th>
                <th>ละติจูด</th>
                <th>ลองจิจูด</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item['company_name'] }}</td>
                    <td>{{ $item['address'] }}</td>
                    <td>{{ $item['latitude'] }}</td>
                    <td>{{ $item['longitude'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>แผนที่</h2>
    <div id="map" style="height: 500px;"></div>

    <script>
        // ใช้ Google Maps API เพื่อแสดงแผนที่
        function initMap() {
            var map = new google.maps.Map(document.getElementById("map"), {
                zoom: 10,
                center: { lat: 13.7563, lng: 100.5018 }, // ตั้งค่าเริ่มต้น
            });

            @foreach ($data as $item)
                new google.maps.Marker({
                    position: { lat: {{ $item['latitude'] }}, lng: {{ $item['longitude'] }} },
                    map: map,
                    title: "{{ $item['company_name'] }}",
                });
            @endforeach
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap" async defer></script>

</body>
</html>
