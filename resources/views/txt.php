        <td>
                                        <a id="openBillLink" href="#">
                                            <button class="btn btn-info">เปิดบิล</button>
                                        </a>
                                    </td>
                                    <!-- เมื่อโหลดหน้าเสร็จให้ตั้งค่า href ให้ลิงก์ -->
                                    <script>
    window.addEventListener('DOMContentLoaded', function () {
        const soCode = document.getElementById('SOCode').value;
        const deliveryCode = document.getElementById('deliveryCode').value;
        const openBillLink = document.getElementById('openBillLink');

        // สร้าง URL พร้อมพารามิเตอร์
        openBillLink.href = 'insertdata?so_num=' + encodeURIComponent(soCode) + '&billid=' + encodeURIComponent(deliveryCode);
    });
</script>


