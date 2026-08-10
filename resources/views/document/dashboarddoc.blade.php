<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เอกสารชั่วคราว</title>

    <style>
        /* =========================================================
           TOKENS — โทนขาวดำล้วน (grayscale)
           ========================================================= */
        :root {
            --ink-900: #111111;
            --ink-700: #333333;
            --ink-500: #666666;
            --ink-300: #999999;
            --ink-150: #d4d4d4;
            --ink-100: #e6e6e6;
            --ink-050: #f4f4f4;
            --paper:   #ffffff;
            --line:    #dcdcdc;
            --radius:  6px;
        }

        * { box-sizing: border-box; }

        html, body {
            font-size: clamp(12px, 0.45vw + 7px, 16px);
        }

        body {
            font-family: 'Segoe UI', 'Noto Sans Thai', 'Roboto', sans-serif;
            background-color: var(--ink-050);
            margin: 0;
            padding: 0;
            color: var(--ink-900);
            line-height: 1.6;
        }

        a {
            color: var(--ink-900);
            text-decoration: none;
            font-weight: 600;
            border-bottom: 1px solid var(--ink-300);
        }
        a:hover { border-bottom-color: var(--ink-900); }

        /* HEADER */
        .header {
            background-color: var(--ink-900);
            color: #fff;
            border-radius: var(--radius);
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
            margin: 20px;
        }

        .header h2 {
            font-size: clamp(16px, 1vw + 6px, 22px);
            margin: 0;
            letter-spacing: 0.02em;
        }

        .buttons { display: flex; gap: 12px; align-items: center; }

        .buttons span { font-size: clamp(11px, 0.55vw + 5px, 14px); color: var(--ink-150); }

        .btn {
            padding: 7px 16px;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: clamp(11px, 0.55vw + 5px, 14px);
            transition: 0.2s;
            border: 1px solid #fff;
        }

        .btn-outline-light {
            background-color: transparent;
            color: #fff;
            border: 1px solid #ffffff55;
        }
        .btn-outline-light:hover { background-color: #ffffff22; border-color: #fff; }

        .btn-solid {
            background-color: #fff;
            color: var(--ink-900);
            border: 1px solid #fff;
        }
        .btn-solid:hover { background-color: var(--ink-150); }

        /* FILTER */
        .filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            background-color: var(--paper);
            padding: 12px 24px;
            margin: 0 20px;
            border: 1px solid var(--line);
            border-radius: var(--radius);
        }

        .filter-form { display: flex; align-items: center; gap: 10px; font-size: clamp(11px, 0.55vw + 5px, 14px); }
        .filter-form label { font-weight: 600; color: var(--ink-700); }

        .filter-form input[type="date"],
        .headcom select {
            padding: 6px 12px;
            border-radius: var(--radius);
            border: 1px solid var(--ink-150);
            background-color: var(--paper);
            font-size: clamp(11px, 0.55vw + 5px, 14px);
            color: var(--ink-900);
        }

        .headcom { display: flex; align-items: center; gap: 8px; }
        .headcom label { font-weight: 600; color: var(--ink-700); font-size: clamp(11px, 0.55vw + 5px, 14px); }

        .search-box { margin-left: auto; }

        #search-input {
            padding: 6px 12px;
            border: 1px solid var(--ink-150);
            border-radius: var(--radius);
            font-size: clamp(11px, 0.55vw + 5px, 14px);
        }

        /* TABLE */
        .table-container { padding: 20px; overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--paper);
            font-size: clamp(10px, 0.62vw + 4px, 14px);
            border-radius: 10px;
            overflow: hidden;
            min-width: 1000px;
            border: 1px solid var(--line);
        }

        th, td {
            padding: 8px 10px;
            border: 1px solid var(--line);
            text-align: center;
            vertical-align: middle;
        }

        th.customer-name, td.customer-name { text-align: left !important; }

        table thead {
            background-color: var(--ink-900);
            color: #fff;
            font-size: clamp(10px, 0.62vw + 4px, 14px);
        }

        table tbody tr:nth-child(even) { background-color: var(--ink-050); }
        table tbody tr:hover { background-color: var(--ink-100); }

        .wrap-text { text-align: left; white-space: normal; word-wrap: break-word; padding: 10px; }

        /* แถวที่ statusdeli == 1 — เดิมใช้เขียว ตอนนี้ใช้เทาเข้มขึ้นแทน */
        td.row-flagged { background-color: var(--ink-150) !important; font-weight: 600; }

        /* ปุ่มไอคอนเอกสาร PDF */
        .pdf-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1.5px solid var(--ink-900);
            background-color: var(--paper);
            cursor: pointer;
            transition: 0.2s;
        }
        .pdf-btn:hover { background-color: var(--ink-900); }
        .pdf-btn:hover svg { stroke: #fff; }
        .pdf-btn svg { width: 17px; height: 17px; stroke: var(--ink-900); transition: 0.2s; }

        .pdf-btn-disabled {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1.5px dashed var(--ink-300);
            background-color: var(--ink-050);
            cursor: not-allowed;
        }
        .pdf-btn-disabled svg { width: 17px; height: 17px; stroke: var(--ink-300); }

        /* POPUP */
        .popup-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(17, 17, 17, 0.6);
            display: flex; align-items: center; justify-content: center;
            z-index: 1000; padding: 20px;
        }

        .popup-content {
            background-color: var(--paper);
            padding: 25px;
            border-radius: 12px;
            width: 100%;
            max-width: 1200px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow-y: auto;
            max-height: 90vh;
            border: 1px solid var(--line);
        }

        .close-btn { float: right; font-size: 24px; cursor: pointer; color: var(--ink-700); }
        .close-btn:hover { color: var(--ink-900); }

        textarea {
            font-family: 'Segoe UI', sans-serif;
            font-size: clamp(11px, 0.55vw + 5px, 14px);
            padding: 10px;
            border: 1px solid var(--ink-150);
            border-radius: var(--radius);
            resize: vertical;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>เอกสารชั่วคราว</h2>
        <div class="buttons">
            <span>👤 ผู้ใช้: {{ session('emp_name', 'Guest') }}</span>

            <a href="{{ route('document.insertdoc') }}" class="btn btn-solid">สร้างเอกสารชั่วคราว</a>

            @csrf
            <a href="http://server_update:8000/solist" class="btn btn-outline-light">🚪 หน้าหลัก</a>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('document.dashboarddoc') }}" class="filter-form" id="autoSearchForm">
            <label for="date">📅 วันที่: เดือน / วัน / ปี</label>
            <input type="date" id="date" name="date" value="{{ request('date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
            <button type="submit" style="display: none;">ค้นหา</button>
        </form>

        <div class="headcom">
            <label for="headcom">บริษัทผู้ส่ง :</label>
            <select id="headcom" onchange="filterTable()">
                <option value="">ทั้งหมด</option>
                <option value="บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด">บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด</option>
                <option value="บริษัท ทริปเปิ้ล อี อินโนเวชั่น จำกัด">บริษัท ทริปเปิ้ล อี อินโนเวชั่น จำกัด</option>
                <option value="บริษัท ทริปเปิ้ลพี แฟคทอรี่ จำกัด">บริษัท ทริปเปิ้ลพี แฟคทอรี่ จำกัด</option>
                <option value="บริษัท เอตะ แอนด์ พอล อินโนเวชั่น จำกัด">บริษัท เอตะ แอนด์ พอล อินโนเวชั่น จำกัด</option>
                <option value="บริษัท ฮิคาริ เดงกิ จำกัด">บริษัท ฮิคาริ เดงกิ จำกัด</option>
                <option value="บริษัท เอ อี แอนด์ ที อินเตอร์เนชั่นแนล จำกัด">บริษัท เอ อี แอนด์ ที อินเตอร์เนชั่นแนล จำกัด</option>
            </select>
        </div>

        <div class="search-box">
            <input type="text" id="search-input" placeholder="🔍 ค้นหา เลขที่บิล" onkeyup="searchTable()">
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>เลขที่บิล</th>
                    <th>บริษัทผู้ส่ง</th>
                    <th>บริษัท</th>
                    <th>ผู้ติดต่อ</th>
                    <th>เบอร์โทร</th>
                    <th>ประเภทงาน</th>
                    <th>ผู้เปิดบิล</th>
                    <th>วันที่</th>
                    <th>เอกสาร PDF</th>
                    <th>ข้อมูลรายละเอียด</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @foreach($docbill as $item)
                @php
                    $pdfPath = "temporary_bill/{$item->doc_id}.pdf";
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="{{ $item->statusdeli == 1 ? 'row-flagged' : '' }}">
                        @if($item->statusdeli == 1)
                            <a href="https://drive.google.com/drive/u/0/search?q={{ $item->doc_id }}+parent:1WyDB1b01cDQ53Ap7B03UIGFbL6a2Y6WB" target="_blank">
                                {{ $item->doc_id }}
                            </a>
                        @else
                            {{ $item->doc_id }}
                        @endif
                    </td>
                    <td>{{ $item->headcom }}</td>
                    <td>{{ $item->com_name }}</td>
                    <td>{{ $item->contact_name }}</td>
                    <td>{{ $item->contact_tel }}</td>
                    <td>{{ $item->doctype }}</td>
                    <td>{{ $item->emp_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->time)->format('d/m/Y') }}</td>

                    {{-- เอกสาร PDF: กดเปิดไฟล์จาก storage/temporary_bill ตรงๆ --}}
                    <td>
                        <a href="{{ asset('storage/' . $pdfPath) }}" target="_blank" class="pdf-btn" style="border-bottom:none;" title="เปิดเอกสาร PDF">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <path d="M14 2v6h6"/>
                                <path d="M9 15h6"/>
                                <path d="M9 11h6"/>
                            </svg>
                        </a>
                    </td>

                    <td>
                        <a href="javascript:void(0);" onclick="openPopup(
                            '{{ $item->doc_id }}',
                            '{{ $item->com_name }}',
                            '{{ $item->com_address }}',
                            '{{ $item->contact_name }}',
                            '{{ $item->contact_tel }}',
                            '{{ $item->notes }}',
                        )">
                            เพิ่มเติม
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if(isset($message))
            <br>
            <p style="text-align: center">{{ $message }}</p>
        @endif
    </div>

    <!-- Popup -->
    <div class="popup-overlay" id="popup" style="display: none;">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>เลขที่บิล</th>
                            <th>บริษัท</th>
                            <th>ที่อยู่</th>
                            <th>ผู้ติดต่อ</th>
                            <th>เบอร์โทร</th>
                        </tr>
                    </thead>
                    <tbody id="popup-body-1"></tbody>
                </table>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>รายการ</th>
                            <th>จำนวน</th>
                        </tr>
                    </thead>
                    <tbody id="popup-body"></tbody>
                </table>
                <br>
                <textarea id="popup-body-3" readonly></textarea>
            </div>
        </div>
    </div>

    <script>
        function filterTable() {
            let selectedType = document.getElementById("headcom").value;
            let table = document.getElementById("table-body");
            let rows = table.getElementsByTagName("tr");

            for (let i = 0; i < rows.length; i++) {
                let typeCell = rows[i].getElementsByTagName("td")[2];
                if (typeCell) {
                    let typeText = typeCell.textContent.trim();
                    rows[i].style.display = (selectedType === "" || typeText === selectedType) ? "" : "none";
                }
            }
        }

        const form = document.getElementById('autoSearchForm');
        const dateInput = document.getElementById('date');

        dateInput.addEventListener('change', () => { form.submit(); });

        window.addEventListener('load', () => {
            if (!sessionStorage.getItem('hasAutoSubmitted')) {
                sessionStorage.setItem('hasAutoSubmitted', 'true');
                form.submit();
            }
        });

        function openPopup(doc_id, com_name, com_address, contact_name, contact_tel, notes) {
            document.getElementById("popup").style.display = "flex";

            let popupBody = document.getElementById("popup-body-1");
            popupBody.innerHTML = `
                <tr>
                    <td>${doc_id}</td>
                    <td>${com_name}</td>
                    <td>${com_address}</td>
                    <td>${contact_name}</td>
                    <td>${contact_tel}</td>
                </tr>
            `;
            document.getElementById("popup-body-3").value = notes;

            let secondPopupBody = document.getElementById("popup-body");
            secondPopupBody.innerHTML = "<tr><td colspan='4'>กำลังโหลดข้อมูล...</td></tr>";

            fetch(`/get-docbill-detail/${doc_id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        secondPopupBody.innerHTML = "";
                        data.forEach(item => {
                            secondPopupBody.insertAdjacentHTML("beforeend", `
                                <tr>
                                    <td>${item.item_name}</td>
                                    <td>${item.quantity}</td>
                                </tr>
                            `);
                        });
                    } else {
                        secondPopupBody.innerHTML = "<tr><td colspan='4'>ไม่มีข้อมูล</td></tr>";
                    }
                })
                .catch(error => {
                    console.error("Error fetching data:", error);
                    secondPopupBody.innerHTML = "<tr><td colspan='4'>เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>";
                });
        }

        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }

        window.onclick = function(event) {
            let popup = document.getElementById("popup");
            if (event.target === popup) { closePopup(); }
        }

        function searchTable() {
            let searchInput = document.getElementById("search-input").value.toLowerCase();
            let table = document.querySelector("table tbody");
            let rows = table.getElementsByTagName("tr");

            for (let i = 0; i < rows.length; i++) {
                let row = rows[i];
                let cells = row.getElementsByTagName("td");
                let docId = cells[1] ? cells[1].textContent.toLowerCase() : "";
                row.style.display = (docId.indexOf(searchInput) > -1) ? "" : "none";
            }
        }
    </script>
</body>
</html>