<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popup Example</title>
    <style>
        /* สไตล์พื้นหลังมืด */
        .popup-overlay {
            display: none; /* ซ่อน Popup ไว้ก่อน */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* สไตล์กล่อง Popup */
        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            position: relative;
        }

        /* ปุ่มปิด */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- ปุ่มเปิด Popup -->
    <button onclick="openPopup()">เปิด Popup</button>

    <!-- Popup -->
    <div class="popup-overlay" id="popup">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <h2>Popup</h2>
            <p>นี่คือเนื้อหาใน Popup</p>
        </div>
    </div>

    <script>
        function openPopup() {
            document.getElementById("popup").style.display = "flex";
        }

        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }

        // ปิด Popup เมื่อคลิกนอกกล่อง
        window.onclick = function(event) {
            let popup = document.getElementById("popup");
            if (event.target === popup) {
                closePopup();
            }
        }
    </script>

</body>
</html>
